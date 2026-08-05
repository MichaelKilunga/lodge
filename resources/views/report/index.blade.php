@extends('template.master')
@section('title', 'Financial & Operational Reports')

@section('head')
<style>
    .report-kpi-card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .report-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.09);
    }
    .report-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }
    .filter-card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }
    .period-pill {
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    @media print {
        .no-print { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
    }
</style>
@endsection

@section('content')
<div class="fade-in pb-5">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-chart-line text-primary me-2"></i>Performance & Financial Report
            </h4>
            <p class="text-muted mb-0 small">
                Showing data from <strong>{{ $from->format('M d, Y') }}</strong> to <strong>{{ $to->format('M d, Y') }}</strong>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 no-print">
            <a href="{{ route('report.exportCsv', request()->all()) }}" class="btn btn-outline-success btn-sm shadow-sm" style="border-radius:10px;">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm" style="border-radius:10px;">
                <i class="fas fa-print me-1"></i> Print Report
            </button>
        </div>
    </div>

    {{-- Filter Bar Card --}}
    <div class="card filter-card mb-4 no-print">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('report.index') }}" id="reportFilterForm">
                <input type="hidden" name="period" id="periodInput" value="{{ $period }}">

                {{-- Period Quick Pills --}}
                <div class="d-flex align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                    <span class="text-muted small fw-bold me-2"><i class="fas fa-clock me-1"></i>Period:</span>
                    <a href="javascript:void(0)" onclick="selectPeriod(event, 'today')" class="period-pill {{ $period == 'today' ? 'btn-primary text-white' : 'btn-light text-secondary' }}">Today</a>
                    <a href="javascript:void(0)" onclick="selectPeriod(event, 'yesterday')" class="period-pill {{ $period == 'yesterday' ? 'btn-primary text-white' : 'btn-light text-secondary' }}">Yesterday</a>
                    <a href="javascript:void(0)" onclick="selectPeriod(event, 'week')" class="period-pill {{ $period == 'week' ? 'btn-primary text-white' : 'btn-light text-secondary' }}">This Week</a>
                    <a href="javascript:void(0)" onclick="selectPeriod(event, 'month')" class="period-pill {{ $period == 'month' ? 'btn-primary text-white' : 'btn-light text-secondary' }}">This Month</a>
                    <a href="javascript:void(0)" onclick="selectPeriod(event, 'last_month')" class="period-pill {{ $period == 'last_month' ? 'btn-primary text-white' : 'btn-light text-secondary' }}">Last Month</a>
                    <a href="javascript:void(0)" onclick="selectPeriod(event, 'year')" class="period-pill {{ $period == 'year' ? 'btn-primary text-white' : 'btn-light text-secondary' }}">This Year</a>
                    <a href="javascript:void(0)" onclick="selectPeriod(event, 'custom')" class="period-pill {{ $period == 'custom' ? 'btn-primary text-white' : 'btn-light text-secondary' }}">Custom Range</a>
                </div>

                {{-- Filter Controls --}}
                <div class="row g-2 align-items-end">
                    <div class="col-md-2" id="customDateFromCol" style="{{ $period == 'custom' ? '' : 'display:none;' }}">
                        <label class="form-label text-muted small mb-1 fw-bold">Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from', $from->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2" id="customDateToCol" style="{{ $period == 'custom' ? '' : 'display:none;' }}">
                        <label class="form-label text-muted small mb-1 fw-bold">Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to', $to->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label text-muted small mb-1 fw-bold">Room Category</label>
                        <select name="type_id" class="form-select form-select-sm">
                            <option value="all">All Room Types</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ $typeId == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label text-muted small mb-1 fw-bold">Booking Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="all">All Statuses</option>
                            <option value="Reservation" {{ $status == 'Reservation' ? 'selected' : '' }}>Reservation</option>
                            <option value="Active" {{ $status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Done" {{ $status == 'Done' ? 'selected' : '' }}>Completed / Done</option>
                            <option value="Canceled" {{ $status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label text-muted small mb-1 fw-bold">Payment Status</label>
                        <select name="payment_status" class="form-select form-select-sm">
                            <option value="all">All Payments</option>
                            <option value="paid" {{ $paymentStatus == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                            <option value="partial" {{ $paymentStatus == 'partial' ? 'selected' : '' }}>Partial Balance</option>
                            <option value="unpaid" {{ $paymentStatus == 'unpaid' ? 'selected' : '' }}>Unpaid / Zero</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1 fw-bold">Search Guest / ID</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Guest name or ID..." value="{{ $search }}">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Filter</button>
                            @if(request()->anyFilled(['search', 'type_id', 'status', 'payment_status', 'date_from']))
                                <a href="{{ route('report.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="fas fa-times"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- KPI Cards Row (6 Metric Cards) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Revenue --}}
        <div class="col-xl-4 col-md-6">
            <div class="card report-kpi-card h-100 p-3" style="border-left: 4px solid #10b981!important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Total Revenue</div>
                        <div class="fs-3 fw-bold text-success">TZS {{ number_format($totalRevenue, 2) }}</div>
                        <div class="text-muted small mt-1"><i class="fas fa-coins me-1"></i> Gross revenue collected</div>
                    </div>
                    <div class="report-icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Bookings & Nights --}}
        <div class="col-xl-4 col-md-6">
            <div class="card report-kpi-card h-100 p-3" style="border-left: 4px solid #3b82f6!important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Total Bookings</div>
                        <div class="fs-3 fw-bold text-primary">{{ $totalBookings }}</div>
                        <div class="text-muted small mt-1"><i class="fas fa-bed me-1"></i> {{ $totalNights }} total room nights</div>
                    </div>
                    <div class="report-icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: ADR (Average Daily Rate) --}}
        <div class="col-xl-4 col-md-6">
            <div class="card report-kpi-card h-100 p-3" style="border-left: 4px solid #6366f1!important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Average Daily Rate (ADR)</div>
                        <div class="fs-3 fw-bold" style="color:#4f46e5;">TZS {{ number_format($adr, 2) }}</div>
                        <div class="text-muted small mt-1"><i class="fas fa-calculator me-1"></i> Rev per occupied room/night</div>
                    </div>
                    <div class="report-icon-box text-indigo" style="background:#e0e7ff;color:#4f46e5;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Occupancy Rate --}}
        <div class="col-xl-4 col-md-6">
            <div class="card report-kpi-card h-100 p-3" style="border-left: 4px solid #f59e0b!important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Occupancy Rate</div>
                        <div class="fs-3 fw-bold text-warning">{{ $occupancyRate }}%</div>
                        <div class="text-muted small mt-1"><i class="fas fa-hotel me-1"></i> Rooms occupied vs inventory</div>
                    </div>
                    <div class="report-icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-door-open"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 5: Outstanding Balance --}}
        <div class="col-xl-4 col-md-6">
            <div class="card report-kpi-card h-100 p-3" style="border-left: 4px solid #ef4444!important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Outstanding Balance</div>
                        <div class="fs-3 fw-bold text-danger">TZS {{ number_format($totalOutstanding, 2) }}</div>
                        <div class="text-muted small mt-1"><i class="fas fa-exclamation-triangle me-1"></i> Pending unpaid balances</div>
                    </div>
                    <div class="report-icon-box bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 6: Average Stay Duration --}}
        <div class="col-xl-4 col-md-6">
            <div class="card report-kpi-card h-100 p-3" style="border-left: 4px solid #06b6d4!important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Average Length of Stay</div>
                        <div class="fs-3 fw-bold text-info">{{ $avgStayDuration }} Days</div>
                        <div class="text-muted small mt-1"><i class="fas fa-user-clock me-1"></i> Avg nights per reservation</div>
                    </div>
                    <div class="report-icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Analytics Charts Section --}}
    <div class="row g-4 mb-4 no-print">
        {{-- Revenue & Bookings Trend Chart --}}
        <div class="col-lg-8">
            <div class="card filter-card h-100">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0" style="color:#1e293b;">
                        <i class="fas fa-chart-area text-primary me-2"></i>Revenue & Booking Trends
                    </h6>
                    <span class="badge bg-light text-secondary fw-normal">Period Analytics</span>
                </div>
                <div class="card-body">
                    <canvas id="revenueTrendChart" height="230"></canvas>
                </div>
            </div>
        </div>

        {{-- Room Category Revenue Breakdown --}}
        <div class="col-lg-4">
            <div class="card filter-card h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0" style="color:#1e293b;">
                        <i class="fas fa-cubes text-primary me-2"></i>Revenue by Room Category
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if(array_sum($roomTypeDistribution) > 0)
                        <canvas id="roomTypePieChart" height="230"></canvas>
                    @else
                        <div class="text-center py-4 px-3 text-muted">
                            <div class="rounded-circle bg-light d-inline-flex p-3 mb-2 text-secondary">
                                <i class="fas fa-chart-pie fa-2x opacity-50"></i>
                            </div>
                            <h6 class="fw-bold mb-1" style="color:#475569;">No Category Revenue</h6>
                            <small class="text-muted">No room revenue recorded for this period.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Booking Transactions Table --}}
    <div class="card filter-card">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="fw-bold mb-0" style="color:#1e293b;">
                <i class="fas fa-list text-primary me-2"></i>Filtered Booking Transactions
                <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ $transactions->count() }} Records</span>
            </h6>
            <div class="text-muted small">
                Showing transactions created between {{ $from->format('d M Y') }} - {{ $to->format('d M Y') }}
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">ID</th>
                            <th>Guest Name</th>
                            <th>Room Info</th>
                            <th>Check-In & Out</th>
                            <th>Duration</th>
                            <th>Booking Status</th>
                            <th class="text-end">Total Price</th>
                            <th class="text-end">Paid Amount</th>
                            <th class="text-end">Balance</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sumTotal   = 0;
                            $sumPaid    = 0;
                            $sumBalance = 0;
                        @endphp
                        @forelse($transactions as $t)
                            @php
                                $price   = $t->getTotalPrice();
                                $paid    = $t->getTotalPayment();
                                $bal     = max(0, $price - $paid);
                                $sumTotal   += $price;
                                $sumPaid    += $paid;
                                $sumBalance += $bal;
                                $nights  = max(1, \App\Helpers\Helper::getDateDifference($t->check_in, $t->check_out));
                            @endphp
                            <tr>
                                <td class="px-3 fw-bold text-muted">#{{ $t->id }}</td>
                                <td>
                                    <div class="fw-bold" style="color:#334155;">{{ optional($t->customer)->name ?? 'N/A' }}</div>
                                    <small class="text-muted">ID: {{ optional($t->customer)->id ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium">Room {{ $t->room->number ?? '—' }}</div>
                                    <small class="badge bg-light text-dark">{{ $t->room->type->name ?? 'Standard' }}</small>
                                </td>
                                <td>
                                    <div class="small">
                                        <div><strong class="text-muted">In:</strong> {{ \Carbon\Carbon::parse($t->check_in)->format('M d, Y') }}</div>
                                        <div><strong class="text-muted">Out:</strong> {{ \Carbon\Carbon::parse($t->check_out)->format('M d, Y') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $nights }} {{ \Illuminate\Support\Str::plural('Night', $nights) }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill
                                        {{ $t->status == 'Done' || $t->status == 'Completed' ? 'bg-success' :
                                           ($t->status == 'Reservation' ? 'bg-primary' :
                                           ($t->status == 'Active' ? 'bg-info' : 'bg-danger')) }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="text-end fw-medium">TZS {{ number_format($price, 2) }}</td>
                                <td class="text-end fw-bold text-success">TZS {{ number_format($paid, 2) }}</td>
                                <td class="text-end fw-bold {{ $bal > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ $bal > 0 ? 'TZS ' . number_format($bal, 2) : 'Paid' }}
                                </td>
                                <td>
                                    @if($bal <= 0)
                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle me-1"></i>Paid</span>
                                    @elseif($paid > 0)
                                        <span class="badge bg-warning bg-opacity-10 text-warning"><i class="fas fa-clock me-1"></i>Partial</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger"><i class="fas fa-times-circle me-1"></i>Unpaid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fas fa-filter mb-2" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                    <p class="mb-0 fw-medium">No transactions found for the selected filter criteria.</p>
                                    <small>Try selecting a broader date range or clearing your filters.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($transactions->count() > 0)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="6" class="px-3 text-end">Filtered Total:</td>
                                <td class="text-end">TZS {{ number_format($sumTotal, 2) }}</td>
                                <td class="text-end text-success">TZS {{ number_format($sumPaid, 2) }}</td>
                                <td class="text-end text-danger">TZS {{ number_format($sumBalance, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('footer')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function selectPeriod(e, period) {
        if (e && e.preventDefault) e.preventDefault();
        document.getElementById('periodInput').value = period;

        const dateFromInput = document.querySelector('input[name="date_from"]');
        const dateToInput = document.querySelector('input[name="date_to"]');

        if (period === 'custom') {
            document.getElementById('customDateFromCol').style.display = 'block';
            document.getElementById('customDateToCol').style.display = 'block';
            if (dateFromInput) dateFromInput.disabled = false;
            if (dateToInput) dateToInput.disabled = false;
        } else {
            if (dateFromInput) dateFromInput.disabled = true;
            if (dateToInput) dateToInput.disabled = true;
            document.getElementById('reportFilterForm').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js library failed to load.');
            return;
        }

        // Chart 1: Revenue & Booking Volume Trend
        const trendElem = document.getElementById('revenueTrendChart');
        if (trendElem) {
            const trendCtx = trendElem.getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Revenue (TZS)',
                            data: {!! json_encode($chartRevenue) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.12)',
                            fill: true,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Bookings Count',
                            data: {!! json_encode($chartBookings) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: false,
                            borderDash: [5, 5],
                            tension: 0.2,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) { return 'TZS ' + Number(value).toLocaleString(); }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: { drawOnChartArea: false },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // Chart 2: Room Category Revenue Pie Chart
        const pieElem = document.getElementById('roomTypePieChart');
        if (pieElem) {
            const pieCtx = pieElem.getContext('2d');
            const roomDist = {!! json_encode($roomTypeDistribution) !!};
            const pieLabels = Object.keys(roomDist);
            const pieValues = Object.values(roomDist);

            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        data: pieValues,
                        backgroundColor: ['#3b82f6', '#10b981', '#6366f1', '#f59e0b', '#ec4899', '#06b6d4']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    });
</script>
@endsection
