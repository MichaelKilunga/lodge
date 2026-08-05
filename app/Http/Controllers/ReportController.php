<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Room;
use App\Models\Type;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->buildReportData($request);
        return view('report.index', $data);
    }

    public function exportCsv(Request $request)
    {
        $data = $this->buildReportData($request);
        $transactions = $data['transactions'];

        $filename = 'hotel_report_' . now()->format('Y-m-d_Hi') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Booking ID',
                'Guest Name',
                'Room Number',
                'Room Type',
                'Check-In',
                'Check-Out',
                'Status',
                'Total Price (TZS)',
                'Paid Amount (TZS)',
                'Balance Due (TZS)',
                'Payment Status',
                'Booking Date'
            ]);

            foreach ($transactions as $t) {
                $totalPrice = $t->getTotalPrice();
                $totalPaid  = $t->getTotalPayment();
                $balance    = max(0, $totalPrice - $totalPaid);
                $paymentStatus = $balance <= 0 ? 'Paid' : ($totalPaid > 0 ? 'Partial' : 'Unpaid');

                fputcsv($file, [
                    '#' . $t->id,
                    optional($t->customer)->name ?? 'N/A',
                    $t->room->number ?? 'N/A',
                    $t->room->type->name ?? 'N/A',
                    Carbon::parse($t->check_in)->format('Y-m-d'),
                    Carbon::parse($t->check_out)->format('Y-m-d'),
                    $t->status,
                    number_format($totalPrice, 2, '.', ''),
                    number_format($totalPaid, 2, '.', ''),
                    number_format($balance, 2, '.', ''),
                    $paymentStatus,
                    Carbon::parse($t->created_at)->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function buildReportData(Request $request): array
    {
        $period = $request->get('period', 'today');
        $dateFromInput = $request->get('date_from');
        $dateToInput = $request->get('date_to');

        if ($period === 'custom' && $dateFromInput && $dateToInput) {
            try {
                $from = Carbon::parse($dateFromInput)->startOfDay();
                $to   = Carbon::parse($dateToInput)->endOfDay();
                if ($from->gt($to)) {
                    $temp = $from;
                    $from = $to->copy()->startOfDay();
                    $to   = $temp->copy()->endOfDay();
                }
            } catch (\Throwable $e) {
                $period = 'today';
                $from = Carbon::now()->startOfDay();
                $to   = Carbon::now()->endOfDay();
            }
        } else {
            switch ($period) {
                case 'yesterday':
                    $from = Carbon::yesterday()->startOfDay();
                    $to   = Carbon::yesterday()->endOfDay();
                    break;
                case 'week':
                    $from = Carbon::now()->startOfWeek();
                    $to   = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $from = Carbon::now()->startOfMonth();
                    $to   = Carbon::now()->endOfMonth();
                    break;
                case 'last_month':
                    $from = Carbon::now()->subMonth()->startOfMonth();
                    $to   = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case 'year':
                    $from = Carbon::now()->startOfYear();
                    $to   = Carbon::now()->endOfYear();
                    break;
                default: // today
                    $period = 'today';
                    $from = Carbon::now()->startOfDay();
                    $to   = Carbon::now()->endOfDay();
                    break;
            }
        }

        $typeId        = $request->get('type_id');
        $status        = $request->get('status');
        $paymentStatus = $request->get('payment_status');
        $search        = $request->get('search');

        $query = Transaction::with(['room.type', 'customer', 'payment', 'user'])
            ->whereBetween('created_at', [$from, $to]);

        if ($typeId && $typeId !== 'all') {
            $query->whereHas('room', function ($q) use ($typeId) {
                $q->where('type_id', $typeId);
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', '=', $search)
                  ->orWhereHas('customer', function ($c) use ($search) {
                      $c->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $transactions = $query->latest()->get();

        // Apply Payment Status Filter on Collection
        if ($paymentStatus && $paymentStatus !== 'all') {
            $transactions = $transactions->filter(function ($t) use ($paymentStatus) {
                $totalPrice = $t->getTotalPrice();
                $totalPaid  = $t->getTotalPayment();
                $balance    = $totalPrice - $totalPaid;

                if ($paymentStatus === 'paid') {
                    return $balance <= 0;
                } elseif ($paymentStatus === 'unpaid') {
                    return $totalPaid == 0;
                } elseif ($paymentStatus === 'partial') {
                    return $totalPaid > 0 && $balance > 0;
                }
                return true;
            });
        }

        // Calculate Core KPIs
        $totalBookings = $transactions->count();

        $transactionRevenue = $transactions->sum(fn ($t) => $t->getTotalPayment());
        $periodPayments     = \App\Models\Payment::whereBetween('created_at', [$from, $to])->sum('price');
        $totalRevenue       = max($transactionRevenue, $periodPayments);

        $totalOutstanding = $transactions->sum(fn ($t) => max(0, $t->getTotalPrice() - $t->getTotalPayment()));

        // Total room nights
        $totalNights = $transactions->sum(function ($t) {
            return max(1, Helper::getDateDifference($t->check_in, $t->check_out));
        });

        // ADR (Average Daily Rate)
        $adr = $totalNights > 0 ? round($totalRevenue / $totalNights, 2) : 0;

        // Average Stay Duration
        $avgStayDuration = $totalBookings > 0 ? round($totalNights / $totalBookings, 1) : 0;

        // Occupancy Rate
        $totalRooms    = Room::count();
        $occupiedRooms = $transactions->pluck('room_id')->unique()->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // Chart Data Generation (Hourly or Daily trend)
        $chartLabels   = [];
        $chartRevenue  = [];
        $chartBookings = [];

        $diffInHours = $from->diffInHours($to);

        if ($diffInHours <= 30) {
            // 3-hour intervals for 1-day view (e.g. today / yesterday)
            for ($h = 0; $h < 24; $h += 3) {
                $slotStart = $from->copy()->addHours($h);
                $slotEnd   = $from->copy()->addHours($h + 3)->subSecond();

                $dayLabel = $slotStart->format('H:i');

                $slotTxns = $transactions->filter(function ($t) use ($slotStart, $slotEnd) {
                    $created = Carbon::parse($t->created_at);
                    return $created >= $slotStart && $created <= $slotEnd;
                });

                $slotRev = $slotTxns->sum(fn ($t) => $t->getTotalPayment());
                if ($slotRev == 0) {
                    $slotRev = \App\Models\Payment::whereBetween('created_at', [$slotStart, $slotEnd])->sum('price');
                }

                $chartLabels[]   = $dayLabel;
                $chartRevenue[]  = round($slotRev, 2);
                $chartBookings[] = $slotTxns->count();
            }
        } else {
            $diffInDays = $from->diffInDays($to);
            $stepDays   = $diffInDays > 60 ? 7 : 1;

            $currentDate = $from->copy();
            while ($currentDate <= $to) {
                $dayStart = $currentDate->copy()->startOfDay();
                $dayEnd   = $stepDays > 1 ? $currentDate->copy()->addDays($stepDays - 1)->endOfDay() : $currentDate->copy()->endOfDay();

                $dayLabel = $stepDays > 1 
                    ? $dayStart->format('M d') . ' - ' . $dayEnd->format('M d') 
                    : $dayStart->format('M d');

                $dayTxns = $transactions->filter(function ($t) use ($dayStart, $dayEnd) {
                    $created = Carbon::parse($t->created_at);
                    return $created >= $dayStart && $created <= $dayEnd;
                });

                $dayRev = $dayTxns->sum(fn ($t) => $t->getTotalPayment());
                if ($dayRev == 0) {
                    $dayRev = \App\Models\Payment::whereBetween('created_at', [$dayStart, $dayEnd])->sum('price');
                }

                $chartLabels[]   = $dayLabel;
                $chartRevenue[]  = round($dayRev, 2);
                $chartBookings[] = $dayTxns->count();

                $currentDate->addDays($stepDays);
            }
        }

        // Room Type Revenue Breakdown
        $roomTypes = Type::orderBy('name')->get();
        $roomTypeDistribution = [];
        foreach ($roomTypes as $rt) {
            $rtTxns = $transactions->filter(fn ($t) => optional($t->room)->type_id == $rt->id);
            $roomTypeDistribution[$rt->name] = $rtTxns->sum(fn ($t) => $t->getTotalPayment());
        }

        return compact(
            'transactions',
            'totalRevenue',
            'totalBookings',
            'totalNights',
            'adr',
            'avgStayDuration',
            'occupancyRate',
            'totalOutstanding',
            'period',
            'from',
            'to',
            'roomTypes',
            'typeId',
            'status',
            'paymentStatus',
            'search',
            'chartLabels',
            'chartRevenue',
            'chartBookings',
            'roomTypeDistribution'
        );
    }
}
