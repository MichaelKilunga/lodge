<?php

namespace App\Http\Controllers;

use App\Models\MarketingTrafficLog;
use App\Models\MarketingReport;
use App\Models\MarketingStrategyItem;
use App\Models\MarketingCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\User;
use App\Mail\MarketingCampaignMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MarketingController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'trends');
        $period = $request->get('period', '30days');
        $reportId = $request->get('report_id');

        // Date range filtering
        $now = Carbon::now();
        switch ($period) {
            case 'today':
                $from = $now->copy()->startOfDay();
                $to = $now->copy()->endOfDay();
                $prevFrom = $now->copy()->subDay()->startOfDay();
                $prevTo = $now->copy()->subDay()->endOfDay();
                break;
            case '7days':
                $from = $now->copy()->subDays(6)->startOfDay();
                $to = $now->copy()->endOfDay();
                $prevFrom = $now->copy()->subDays(13)->startOfDay();
                $prevTo = $now->copy()->subDays(7)->endOfDay();
                break;
            case 'this_month':
                $from = $now->copy()->startOfMonth();
                $to = $now->copy()->endOfMonth();
                $prevFrom = $now->copy()->subMonth()->startOfMonth();
                $prevTo = $now->copy()->subMonth()->endOfMonth();
                break;
            default: // 30days
                $from = $now->copy()->subDays(29)->startOfDay();
                $to = $now->copy()->endOfDay();
                $prevFrom = $now->copy()->subDays(59)->startOfDay();
                $prevTo = $now->copy()->subDays(30)->endOfDay();
                break;
        }

        // 1. KPI Metrics from Live Traffic & Bookings
        $logs = MarketingTrafficLog::whereBetween('created_at', [$from, $to]);
        $prevLogs = MarketingTrafficLog::whereBetween('created_at', [$prevFrom, $prevTo]);

        $totalVisits = (clone $logs)->where('event_type', 'page_view')->count();
        $prevVisits = (clone $prevLogs)->where('event_type', 'page_view')->count();
        $visitsGrowth = $prevVisits > 0 ? round((($totalVisits - $prevVisits) / $prevVisits) * 100, 1) : ($totalVisits > 0 ? 100 : 0);

        $uniqueVisitors = (clone $logs)->where('event_type', 'page_view')->distinct('session_id')->count('session_id');
        
        $whatsappClicks = (clone $logs)->where('event_type', 'whatsapp_click')->count();
        $phoneClicks = (clone $logs)->where('event_type', 'phone_click')->count();
        $contactForms = (clone $logs)->where('event_type', 'contact_form_submit')->count();

        // Actual bookings during period
        $transactions = Transaction::with('payment')
            ->whereBetween('created_at', [$from, $to])
            ->get();
        $totalBookings = $transactions->count();
        $totalRevenue = $transactions->sum(fn ($t) => optional($t->payment)->total ?? 0);

        // Conversion rate
        $conversionRate = $totalVisits > 0 ? round(($totalBookings / $totalVisits) * 100, 2) : 0;

        // 2. Acquisition Channels Breakdown (Sources)
        $sourcesBreakdown = (clone $logs)->where('event_type', 'page_view')
            ->select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->orderByDesc('count')
            ->get();

        // 3. Device Breakdown
        $devicesBreakdown = (clone $logs)->where('event_type', 'page_view')
            ->select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->get();

        // 4. Daily Chart Trend Data
        $chartDays = $period === 'today' ? 24 : ($period === '7days' ? 7 : 30);
        $dailyDates = [];
        $dailyVisitsSeries = [];
        $dailyInteractionsSeries = [];

        if ($period === 'today') {
            for ($i = 0; $i < 24; $i++) {
                $hourStr = sprintf('%02d:00', $i);
                $dailyDates[] = $hourStr;
                $hourStart = $from->copy()->addHours($i);
                $hourEnd = $hourStart->copy()->endOfHour();
                
                $dailyVisitsSeries[] = MarketingTrafficLog::whereBetween('created_at', [$hourStart, $hourEnd])
                    ->where('event_type', 'page_view')->count();
                $dailyInteractionsSeries[] = MarketingTrafficLog::whereBetween('created_at', [$hourStart, $hourEnd])
                    ->whereIn('event_type', ['whatsapp_click', 'phone_click', 'contact_form_submit', 'booking_click'])->count();
            }
        } else {
            for ($i = $chartDays - 1; $i >= 0; $i--) {
                $dayDate = Carbon::now()->subDays($i);
                $dailyDates[] = $dayDate->format('M d');
                
                $dayStart = $dayDate->copy()->startOfDay();
                $dayEnd = $dayDate->copy()->endOfDay();
                
                $dailyVisitsSeries[] = MarketingTrafficLog::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->where('event_type', 'page_view')->count();
                $dailyInteractionsSeries[] = MarketingTrafficLog::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->whereIn('event_type', ['whatsapp_click', 'phone_click', 'contact_form_submit', 'booking_click'])->count();
            }
        }

        // Top Viewed Pages
        $topPages = (clone $logs)->where('event_type', 'page_view')
            ->select('page_type', 'url', DB::raw('count(*) as views'))
            ->groupBy('page_type', 'url')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        // TAB 2 & TAB 3: Weekly Digital Marketing & Growth Report Data
        $allReports = MarketingReport::orderByDesc('id')->get();
        
        $selectedReport = null;
        if ($reportId) {
            $selectedReport = MarketingReport::find($reportId);
        }
        if (!$selectedReport && $allReports->count() > 0) {
            $selectedReport = $allReports->first();
        }

        // Clean template for new report or fallback (NO DUMMY DATA - only real live auto-collected metrics)
        $cleanTemplate = [
            'week_number' => 'Week ' . $now->weekOfYear,
            'reporting_period' => $now->copy()->startOfWeek()->format('M d, Y') . ' - ' . $now->copy()->endOfWeek()->format('M d, Y'),
            'prepared_by' => auth()->user()->name ?? 'Digital Marketing Lead',
            'department' => 'MEDIA & ICT',
            'date_submitted' => $now->format('Y-m-d'),
            'reviewed_by' => 'General Manager',
            'tasks_data' => [],
            'kpi_data' => [
                ['kpi' => 'Planned Tasks Completed', 'target' => '100%', 'actual' => '', 'status' => 'Pending'],
                ['kpi' => 'Social Media Posts', 'target' => '3', 'actual' => '', 'status' => 'Pending'],
                ['kpi' => 'Reels Published', 'target' => '2', 'actual' => '', 'status' => 'Pending'],
                ['kpi' => 'Videos Produced', 'target' => '1', 'actual' => '', 'status' => 'Pending'],
                ['kpi' => 'Photos Captured', 'target' => '10', 'actual' => '', 'status' => 'Pending'],
                ['kpi' => 'Website Visitors', 'target' => '250', 'actual' => strval($totalVisits), 'status' => '⚡ Auto-Collected Live'],
                ['kpi' => 'WhatsApp Inquiries', 'target' => '20', 'actual' => strval($whatsappClicks), 'status' => '⚡ Auto-Collected Live'],
                ['kpi' => 'Phone Calls', 'target' => '15', 'actual' => strval($phoneClicks), 'status' => '⚡ Auto-Collected Live'],
                ['kpi' => 'Booking Requests', 'target' => '10', 'actual' => strval($totalBookings), 'status' => '⚡ Auto-Collected Live'],
                ['kpi' => 'New Guests', 'target' => '8', 'actual' => strval(Customer::count()), 'status' => '⚡ Auto-Collected Live'],
                ['kpi' => 'Google Reviews', 'target' => '5', 'actual' => '', 'status' => 'Pending'],
                ['kpi' => 'Meta Ads CTR', 'target' => '2.5%', 'actual' => '', 'status' => 'Pending'],
                ['kpi' => 'Cost per Lead', 'target' => '$5.00', 'actual' => '', 'status' => 'Pending'],
            ],
            'social_media_data' => [
                ['platform' => 'Facebook', 'planned' => '', 'posted' => '', 'reels' => '', 'stories' => '', 'status' => 'Not Started'],
                ['platform' => 'Instagram', 'planned' => '', 'posted' => '', 'reels' => '', 'stories' => '', 'status' => 'Not Started'],
                ['platform' => 'TikTok', 'planned' => '', 'posted' => '', 'reels' => '', 'stories' => '', 'status' => 'Not Started'],
            ],
            'website_performance_data' => [
                ['metric' => 'Website Visitors', 'value' => number_format($totalVisits)],
                ['metric' => 'Unique Visitors', 'value' => number_format($uniqueVisitors)],
                ['metric' => 'Online Bookings Generated', 'value' => number_format($totalBookings)],
                ['metric' => 'WhatsApp Button Clicks', 'value' => number_format($whatsappClicks)],
                ['metric' => 'Phone Call Clicks', 'value' => number_format($phoneClicks)],
                ['metric' => 'Average Conversion Rate', 'value' => $conversionRate . '%'],
            ],
            'google_business_data' => [
                ['metric' => 'Searches', 'value' => ''],
                ['metric' => 'Profile Views', 'value' => ''],
                ['metric' => 'Website Clicks', 'value' => ''],
                ['metric' => 'Phone Calls', 'value' => ''],
                ['metric' => 'Direction Requests', 'value' => ''],
                ['metric' => 'New Reviews', 'value' => ''],
                ['metric' => 'Average Rating', 'value' => ''],
            ],
            'bookings_leads_data' => [
                ['source' => 'Website Direct', 'leads' => '', 'bookings' => strval($totalBookings), 'revenue' => '$' . number_format($totalRevenue, 2)],
                ['source' => 'WhatsApp Direct', 'leads' => '', 'bookings' => '0', 'revenue' => '$0.00'],
                ['source' => 'Phone Calls Direct', 'leads' => '', 'bookings' => '0', 'revenue' => '$0.00'],
                ['source' => 'Walk-ins', 'leads' => '', 'bookings' => '0', 'revenue' => '$0.00'],
                ['source' => 'Facebook Referral', 'leads' => '', 'bookings' => '0', 'revenue' => '$0.00'],
                ['source' => 'Instagram Referral', 'leads' => '', 'bookings' => '0', 'revenue' => '$0.00'],
                ['source' => 'Google Search', 'leads' => '', 'bookings' => '0', 'revenue' => '$0.00'],
            ],
            'paid_ads_data' => [],
            'content_created_data' => [],
            'challenges_data' => [],
            'achievements_data' => [],
            'next_week_plan_data' => []
        ];

        // If viewing an existing report, use its data; otherwise use clean template
        $activeReportData = $selectedReport ? $selectedReport->toArray() : $cleanTemplate;

        // If in 'feed' tab and we selected a report_id, load it for editing
        $editReport = null;
        if ($tab === 'feed' && $reportId) {
            $editReport = MarketingReport::find($reportId);
        }

        // TAB 4: Digital Growth Strategy Checklist (Document 1)
        $strategyItems = MarketingStrategyItem::orderBy('area_number')->orderBy('id')->get()->groupBy('area_name');

        // TAB 5: Email Advertisement Campaigns
        $campaigns = MarketingCampaign::orderByDesc('id')->get();
        $subscribersCount = NewsletterSubscriber::count();
        $customersCount = User::whereIn('role', ['Customer', 'Guest'])->orWhereHas('customer')->count();
        $staffCount = User::whereNotIn('role', ['Customer', 'Guest'])->whereDoesntHave('customer')->count();

        return view('marketing.index', compact(
            'tab', 'period', 'from', 'to',
            'totalVisits', 'visitsGrowth', 'uniqueVisitors',
            'whatsappClicks', 'phoneClicks', 'contactForms',
            'totalBookings', 'totalRevenue', 'conversionRate',
            'sourcesBreakdown', 'devicesBreakdown',
            'dailyDates', 'dailyVisitsSeries', 'dailyInteractionsSeries',
            'topPages',
            'selectedReport', 'allReports', 'activeReportData', 'cleanTemplate', 'editReport',
            'strategyItems',
            'campaigns', 'subscribersCount', 'customersCount', 'staffCount'
        ));
    }

    public function trackInteraction(Request $request)
    {
        $eventType = $request->input('event_type', 'page_view');
        $url = $request->input('url', $request->header('referer') ?: url('/'));
        $pageType = $request->input('page_type', 'Home');
        
        $referrer = $request->input('referrer', $request->header('referer'));
        $source = 'Direct';
        if ($request->has('utm_source') && !empty($request->input('utm_source'))) {
            $source = ucfirst($request->input('utm_source'));
        } elseif ($referrer) {
            $refLower = strtolower($referrer);
            if (str_contains($refLower, 'facebook.com') || str_contains($refLower, 'fb.me')) $source = 'Facebook';
            elseif (str_contains($refLower, 'instagram.com')) $source = 'Instagram';
            elseif (str_contains($refLower, 'tiktok.com')) $source = 'TikTok';
            elseif (str_contains($refLower, 'google.com')) $source = 'Google Search';
            elseif (str_contains($refLower, 'whatsapp.com')) $source = 'WhatsApp';
            else $source = 'Referral';
        }

        $userAgent = $request->header('User-Agent');
        $deviceType = 'Desktop';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($userAgent))) {
            $deviceType = 'Tablet';
        } elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
            $deviceType = 'Mobile';
        }

        MarketingTrafficLog::create([
            'session_id'  => $request->session()->getId() ?: md5($request->ip() . '_' . date('Y-m-d')),
            'ip_address'  => $request->ip(),
            'url'         => substr($url, 0, 255),
            'page_type'   => substr($pageType, 0, 100),
            'referrer'    => $referrer ? substr($referrer, 0, 500) : null,
            'source'      => substr($source, 0, 100),
            'device_type' => $deviceType,
            'event_type'  => $eventType,
            'utm_source'  => $request->input('utm_source'),
            'utm_medium'  => $request->input('utm_medium'),
            'utm_campaign'=> $request->input('utm_campaign'),
        ]);

        return response()->json(['status' => 'success']);
    }

    public function saveReport(Request $request)
    {
        $validated = $request->validate([
            'report_id'        => 'nullable|integer',
            'week_number'      => 'required|string',
            'reporting_period' => 'required|string',
            'prepared_by'      => 'nullable|string',
            'department'       => 'nullable|string',
            'date_submitted'   => 'nullable|date',
            'reviewed_by'      => 'nullable|string',
        ]);

        // Clean and filter arrays to remove empty rows
        $cleanArray = function ($input, $requiredKey) {
            if (!is_array($input)) return [];
            return array_values(array_filter($input, fn($row) => is_array($row) && !empty($row[$requiredKey])));
        };

        $data = [
            'week_number'      => $validated['week_number'],
            'reporting_period' => $validated['reporting_period'],
            'prepared_by'      => $validated['prepared_by'] ?? 'Digital Marketing Lead',
            'department'       => $validated['department'] ?? 'MEDIA & ICT',
            'date_submitted'   => $validated['date_submitted'] ?? date('Y-m-d'),
            'reviewed_by'      => $validated['reviewed_by'] ?? 'General Manager',
            'tasks_data'       => $cleanArray($request->input('tasks_data', []), 'task'),
            'kpi_data'         => $cleanArray($request->input('kpi_data', []), 'kpi'),
            'social_media_data'=> $cleanArray($request->input('social_media_data', []), 'platform'),
            'website_performance_data' => $cleanArray($request->input('website_performance_data', []), 'metric'),
            'google_business_data'     => $cleanArray($request->input('google_business_data', []), 'metric'),
            'bookings_leads_data'      => $cleanArray($request->input('bookings_leads_data', []), 'source'),
            'paid_ads_data'            => $cleanArray($request->input('paid_ads_data', []), 'campaign'),
            'content_created_data'     => $cleanArray($request->input('content_created_data', []), 'type'),
            'challenges_data'          => $cleanArray($request->input('challenges_data', []), 'challenge'),
            'achievements_data'        => $cleanArray($request->input('achievements_data', []), 'achievement'),
            'next_week_plan_data'      => $cleanArray($request->input('next_week_plan_data', []), 'activity'),
        ];

        if (!empty($validated['report_id'])) {
            $report = MarketingReport::findOrFail($validated['report_id']);
            $report->update($data);
            $msg = '✅ Weekly Digital Marketing & Growth Report updated successfully!';
        } else {
            $report = MarketingReport::create($data);
            $msg = '✅ New Weekly Digital Marketing & Growth Report created and saved successfully!';
        }

        return redirect()->route('marketing.index', ['tab' => 'report', 'report_id' => $report->id])
            ->with('success', $msg);
    }

    public function destroyReport(MarketingReport $report)
    {
        $report->delete();
        return redirect()->route('marketing.index', ['tab' => 'report'])
            ->with('success', '🗑️ Marketing Report deleted successfully!');
    }

    public function storeStrategyItem(Request $request)
    {
        $request->validate([
            'area_number' => 'required|integer',
            'area_name'   => 'required|string',
            'task'        => 'required|string',
            'cost'        => 'nullable|string',
            'status'      => 'required|string',
        ]);

        MarketingStrategyItem::create([
            'area_number' => $request->area_number,
            'area_name'   => $request->area_name,
            'task'        => $request->task,
            'cost'        => $request->cost ?: '0/=',
            'status'      => $request->status,
        ]);

        return redirect()->back()->with('success', '✅ New strategy task added successfully!');
    }

    public function updateStrategyItem(Request $request, MarketingStrategyItem $item)
    {
        $request->validate([
            'status' => 'required|string',
            'cost'   => 'nullable|string',
            'notes'  => 'nullable|string',
        ]);

        $item->update([
            'status' => $request->status,
            'cost'   => $request->cost,
            'notes'  => $request->notes,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'item' => $item]);
        }

        return redirect()->back()->with('success', '✅ Strategy task updated successfully!');
    }

    public function destroyStrategyItem(MarketingStrategyItem $item)
    {
        $item->delete();
        return redirect()->back()->with('success', '🗑️ Strategy task removed successfully!');
    }

    public function sendCampaign(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'subject'         => 'required|string|max:255',
            'headline'        => 'required|string|max:255',
            'banner_url'      => 'nullable|url|max:500',
            'content'         => 'required|string',
            'cta_text'        => 'nullable|string|max:100',
            'cta_url'         => 'nullable|url|max:500',
            'discount_code'   => 'nullable|string|max:50',
            'target_audiences'=> 'nullable|array',
            'custom_emails'   => 'nullable|string',
        ]);

        $audiences = $request->input('target_audiences', []);
        $recipients = [];

        // 1. Newsletter Subscribers
        if (in_array('subscribers', $audiences)) {
            $subs = NewsletterSubscriber::all();
            foreach ($subs as $s) {
                if ($s->email && filter_var($s->email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[$s->email] = 'Valued Subscriber';
                }
            }
        }

        // 2. Customers / Past Guests
        if (in_array('customers', $audiences)) {
            $custs = User::whereIn('role', ['Customer', 'Guest'])->orWhereHas('customer')->get();
            foreach ($custs as $c) {
                if ($c->email && filter_var($c->email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[$c->email] = $c->name ?: 'Valued Guest';
                }
            }
        }

        // 3. Staff & Internal System Users
        if (in_array('staff', $audiences)) {
            $staff = User::whereNotIn('role', ['Customer', 'Guest'])->whereDoesntHave('customer')->get();
            foreach ($staff as $s) {
                if ($s->email && filter_var($s->email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[$s->email] = $s->name ?: 'Team Member';
                }
            }
        }

        // 4. Custom Emails
        if (!empty($request->custom_emails)) {
            $rawEmails = preg_split('/[,;\r\n]+/', $request->custom_emails);
            foreach ($rawEmails as $e) {
                $trimmed = trim($e);
                if (filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
                    $recipients[$trimmed] = 'Valued Client';
                }
            }
        }

        if (empty($recipients)) {
            return redirect()->back()->withErrors(['target_audiences' => 'Please select at least one target audience or enter valid custom email addresses.'])->withInput();
        }

        // Format target audience string
        $audienceNames = [];
        if (in_array('subscribers', $audiences)) $audienceNames[] = 'Subscribers';
        if (in_array('customers', $audiences)) $audienceNames[] = 'Customers & Guests';
        if (in_array('staff', $audiences)) $audienceNames[] = 'Staff & System Users';
        if (!empty($request->custom_emails)) $audienceNames[] = 'Custom Recipients';
        $targetAudienceStr = implode(', ', $audienceNames);

        // Record campaign in database
        $campaign = MarketingCampaign::create([
            'title'            => $request->title,
            'subject'          => $request->subject,
            'headline'         => $request->headline,
            'banner_url'       => $request->banner_url,
            'content'          => $request->content,
            'cta_text'         => $request->cta_text,
            'cta_url'          => $request->cta_url,
            'discount_code'    => $request->discount_code,
            'target_audience'  => $targetAudienceStr,
            'recipients_count' => count($recipients),
            'sent_by'          => auth()->user()->name ?? 'Marketing Admin',
            'status'           => 'Sent',
        ]);

        // Send Emails
        $sentCount = 0;
        foreach ($recipients as $email => $name) {
            try {
                Mail::to($email)->send(new MarketingCampaignMail($campaign, $name));
                $sentCount++;
            } catch (\Exception $ex) {
                Log::error("Failed sending marketing ad to {$email}: " . $ex->getMessage());
            }
        }

        return redirect()->route('marketing.index', ['tab' => 'campaigns'])
            ->with('success', "🚀 Advertisement Campaign '{$campaign->title}' pushed successfully to {$sentCount} recipients!");
    }

    public function destroyCampaign(MarketingCampaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('marketing.index', ['tab' => 'campaigns'])
            ->with('success', '🗑️ Campaign record removed from history!');
    }
}
