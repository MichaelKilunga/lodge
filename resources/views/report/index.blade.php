@extends('template.master')
@section('title', 'Reports')

@section('content')
<div class="row g-4">

    {{-- Period Filter --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>Performance Report</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('report.index', ['period' => 'today']) }}"
                            class="btn btn-sm {{ $period == 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">Today</a>
                        <a href="{{ route('report.index', ['period' => 'week']) }}"
                            class="btn btn-sm {{ $period == 'week' ? 'btn-primary' : 'btn-outline-secondary' }}">This Week</a>
                        <a href="{{ route('report.index', ['period' => 'month']) }}"
                            class="btn btn-sm {{ $period == 'month' ? 'btn-primary' : 'btn-outline-secondary' }}">This Month</a>
                    </div>
                </div>
                <small class="text-muted d-block mt-1">
                    {{ $from->format('M d, Y H:i') }} &rarr; {{ $to->format('M d, Y H:i') }}
                </small>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #3b82f6!important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Total Bookings</div>
                        <div class="fs-2 fw-bold">{{ $totalBookings }}</div>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-calendar-check fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #22c55e!important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Total Revenue</div>
                        <div class="fs-2 fw-bold">TZS {{ number_format($totalRevenue, 2) }}</div>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fas fa-dollar-sign fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b!important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Occupancy Rate</div>
                        <div class="fs-2 fw-bold">{{ $occupancyRate }}%</div>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-bed fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Booking Transactions</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">Guest</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $t)
                            <tr>
                                <td class="px-4">{{ optional($t->customer)->name ?? 'N/A' }}</td>
                                <td>Room {{ $t->room->number ?? '—' }} ({{ $t->room->type->name ?? '—' }})</td>
                                <td>{{ \Carbon\Carbon::parse($t->check_in)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($t->check_out)->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge rounded-pill
                                        {{ $t->status == 'Completed' ? 'bg-success' :
                                           ($t->status == 'Reservation' ? 'bg-primary' : 'bg-secondary') }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td>TZS {{ number_format(optional($t->payment)->total ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No transactions found for this period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
