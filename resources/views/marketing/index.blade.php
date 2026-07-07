@extends('template.master')
@section('title', 'Marketing & Digital Growth Panel')

@section('content')
<style>
    /* Premium Marketing Module Styles */
    :root {
        --mkt-primary: #4facfe;
        --mkt-secondary: #00f2fe;
        --mkt-purple: #8a2387;
        --mkt-pink: #e94057;
        --mkt-orange: #f27121;
        --mkt-dark: #1e293b;
    }
    .mkt-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        color: #fff;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        position: relative;
        overflow: hidden;
    }
    .mkt-header-card::before {
        content: "";
        position: absolute;
        top: -50px;
        right: -50px;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(79,172,254,0.2) 0%, rgba(0,242,254,0) 70%);
        border-radius: 50%;
    }
    .mkt-nav-pills .nav-link {
        border-radius: 12px;
        padding: 12px 20px;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .mkt-nav-pills .nav-link:hover {
        background: #f1f5f9;
        color: #1e293b;
        transform: translateY(-1px);
    }
    .mkt-nav-pills .nav-link.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    .kpi-growth-card {
        background: #fff;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .kpi-growth-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }
    .kpi-icon-wrapper {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .chart-container-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .badge-growth {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    .badge-decline {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    .strategy-area-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .strategy-area-header {
        background: #f8fafc;
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .form-feed-section {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }
    .form-feed-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 12px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn-add-row {
        background: #f1f5f9;
        color: #3b82f6;
        border: 1px dashed #3b82f6;
        font-weight: 600;
        border-radius: 30px;
        padding: 6px 16px;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .btn-add-row:hover {
        background: #3b82f6;
        color: #fff;
    }
    @media print {
        .lh-sidebar, .lh-header, .mkt-nav-pills, .btn, .no-print, .alert { display: none !important; }
        .card, .chart-container-card { box-shadow: none !important; border: 1px solid #ccc !important; }
        body { background: #fff !important; }
    }
</style>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="mkt-header-card mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="badge bg-info bg-opacity-25 text-info border border-info mb-2 px-3 py-1 rounded-pill">
                <i class="fas fa-chart-line me-1"></i> Digital Marketing & Growth Intelligence
            </span>
            <h2 class="fw-bold mb-1">Bella Vista Lodge Traffic & Marketing Panel</h2>
            <p class="text-white-50 mb-0">Track live web visitor telemetry, measure acquisition trends, and manually feed weekly growth reports.</p>
        </div>
        <div class="d-flex gap-2">
            @if($tab === 'report')
            <button onclick="window.print()" class="btn btn-light fw-bold px-4 rounded-pill shadow-sm">
                <i class="fas fa-print me-2"></i> Print Report
            </button>
            <a href="{{ route('marketing.index', ['tab' => 'feed', 'report_id' => optional($selectedReport)->id]) }}" class="btn btn-warning fw-bold px-4 rounded-pill shadow-sm text-dark">
                <i class="fas fa-edit me-2"></i> {{ $selectedReport ? 'Edit This Report' : 'Feed Report Data' }}
            </a>
            @endif
            @if($tab === 'feed')
            <a href="{{ route('marketing.index', ['tab' => 'report']) }}" class="btn btn-outline-light px-4 rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Back to Reports
            </a>
            @endif
            <a href="{{ route('public.home') }}" target="_blank" class="btn btn-outline-light px-4 rounded-pill">
                <i class="fas fa-external-link-alt me-2"></i> View Website
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Navigation Pills -->
    <ul class="nav nav-pills mkt-nav-pills gap-3 mb-4 no-print">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'trends' ? 'active' : '' }}" href="{{ route('marketing.index', ['tab' => 'trends', 'period' => $period]) }}">
                <i class="fas fa-chart-area me-2"></i> 📈 1. Live Traffic & Growth Trends
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'report' ? 'active' : '' }}" href="{{ route('marketing.index', ['tab' => 'report']) }}">
                <i class="fas fa-file-invoice me-2"></i> 📑 2. Weekly Marketing Report
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'feed' ? 'active' : '' }}" href="{{ route('marketing.index', ['tab' => 'feed']) }}">
                <i class="fas fa-edit me-2"></i> ✍️ 3. Feed / Edit Report Data
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'strategy' ? 'active' : '' }}" href="{{ route('marketing.index', ['tab' => 'strategy']) }}">
                <i class="fas fa-tasks me-2"></i> 🎯 4. Digital Growth Strategy (Areas 1-8)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'campaigns' ? 'active' : '' }}" href="{{ route('marketing.index', ['tab' => 'campaigns']) }}">
                <i class="fas fa-envelope-open-text me-2"></i> 📧 5. Push Email Ads & Campaigns
            </a>
        </li>
    </ul>

    <!-- =========================================================================
         TAB 1: LIVE TRAFFIC & GROWTH TRENDS (Auto-Collected Telemetry)
         ========================================================================= -->
    @if($tab === 'trends')
        <!-- Period Selector Filter -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-filter text-primary me-2 fs-5"></i>
                    <span class="fw-bold me-2">Analytics Timeframe:</span>
                    <span class="text-muted small">{{ $from->format('M d, Y') }} &rarr; {{ $to->format('M d, Y') }}</span>
                </div>
                <div class="btn-group rounded-pill overflow-hidden shadow-sm" role="group">
                    <a href="{{ route('marketing.index', ['tab' => 'trends', 'period' => 'today']) }}" class="btn btn-sm px-3 {{ $period === 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">Today</a>
                    <a href="{{ route('marketing.index', ['tab' => 'trends', 'period' => '7days']) }}" class="btn btn-sm px-3 {{ $period === '7days' ? 'btn-primary' : 'btn-outline-secondary' }}">Last 7 Days</a>
                    <a href="{{ route('marketing.index', ['tab' => 'trends', 'period' => '30days']) }}" class="btn btn-sm px-3 {{ $period === '30days' ? 'btn-primary' : 'btn-outline-secondary' }}">Last 30 Days</a>
                    <a href="{{ route('marketing.index', ['tab' => 'trends', 'period' => 'this_month']) }}" class="btn btn-sm px-3 {{ $period === 'this_month' ? 'btn-primary' : 'btn-outline-secondary' }}">This Month</a>
                </div>
            </div>
        </div>

        <!-- Top KPI Scorecard Row -->
        <div class="row g-4 mb-4">
            <!-- Total Visitors -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-growth-card h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Website Visitors</div>
                            <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalVisits) }}</h3>
                        </div>
                        <div class="kpi-icon-wrapper" style="background: rgba(79, 172, 254, 0.15); color: #0284c7;">
                            <i class="fas fa-globe"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="{{ $visitsGrowth >= 0 ? 'badge-growth' : 'badge-decline' }}">
                            <i class="fas fa-arrow-{{ $visitsGrowth >= 0 ? 'up' : 'down' }} me-1"></i> {{ abs($visitsGrowth) }}% vs prev
                        </span>
                        <small class="text-muted">{{ number_format($uniqueVisitors) }} Unique</small>
                    </div>
                </div>
            </div>

            <!-- WhatsApp & Phone Leads -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-growth-card h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Direct Action Clicks</div>
                            <h3 class="fw-bold mb-0 mt-1">{{ number_format($whatsappClicks + $phoneClicks) }}</h3>
                        </div>
                        <div class="kpi-icon-wrapper" style="background: rgba(34, 197, 94, 0.15); color: #16a34a;">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <small class="text-success fw-bold"><i class="fab fa-whatsapp me-1"></i> {{ $whatsappClicks }} WhatsApp</small>
                        <small class="text-primary fw-bold"><i class="fas fa-phone me-1"></i> {{ $phoneClicks }} Calls</small>
                    </div>
                </div>
            </div>

            <!-- Online Bookings Converted -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-growth-card h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Converted Bookings</div>
                            <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalBookings) }}</h3>
                        </div>
                        <div class="kpi-icon-wrapper" style="background: rgba(138, 35, 135, 0.15); color: #8a2387;">
                            <i class="fas fa-bed"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="badge bg-purple text-dark bg-opacity-10 fw-bold">Conversion Rate</span>
                        <span class="fw-bold text-dark">{{ $conversionRate }}%</span>
                    </div>
                </div>
            </div>

            <!-- Revenue Generated -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-growth-card h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Tracked Revenue</div>
                            <h3 class="fw-bold mb-0 mt-1">${{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                        <div class="kpi-icon-wrapper" style="background: rgba(242, 113, 33, 0.15); color: #ea580c;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <small class="text-muted">Digital & Direct Funnel</small>
                        <small class="text-success fw-bold">Active Live</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="chart-container-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Market Growth & Traffic Trend</h5>
                            <p class="text-muted small mb-0">Comparison of daily website visitors versus guest interaction events</p>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">Live Website Telemetry</span>
                    </div>
                    <div style="height: 320px;">
                        <canvas id="trafficTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="chart-container-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1">Acquisition Channels</h5>
                        <p class="text-muted small mb-3">Where your visitors are coming from</p>
                        <div style="height: 240px; position: relative;">
                            <canvas id="sourceChart"></canvas>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-around text-center flex-wrap gap-2">
                            @forelse($sourcesBreakdown->take(3) as $src)
                            <div>
                                <div class="fw-bold fs-6">{{ $src->count }}</div>
                                <small class="text-muted">{{ $src->source }}</small>
                            </div>
                            @empty
                            <small class="text-muted">No channel data recorded yet.</small>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Pages & Device Matrix -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="chart-container-card h-100">
                    <h5 class="fw-bold mb-3"><i class="fas fa-layer-group text-primary me-2"></i>Top Viewed Pages & Sections</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Page / Section</th>
                                    <th>URL Path</th>
                                    <th class="text-end">Views Tracked</th>
                                    <th class="text-end">Traffic Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPages as $page)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark fw-bold px-3 py-1 rounded-pill">
                                            {{ $page->page_type }}
                                        </span>
                                    </td>
                                    <td class="text-muted small text-truncate" style="max-width: 250px;">{{ $page->url }}</td>
                                    <td class="text-end fw-bold">{{ number_format($page->views) }}</td>
                                    <td class="text-end">
                                        @php $share = $totalVisits > 0 ? round(($page->views / $totalVisits) * 100) : 0; @endphp
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $share }}%"></div>
                                            </div>
                                            <span class="small fw-bold">{{ $share }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">No page views logged yet for this period. As visitors browse your public pages, telemetry will appear here live!</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="chart-container-card h-100">
                    <h5 class="fw-bold mb-3"><i class="fas fa-mobile-alt text-primary me-2"></i>Device Share</h5>
                    <div style="height: 250px;">
                        <canvas id="deviceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    <!-- =========================================================================
         TAB 2: WEEKLY DIGITAL MARKETING & GROWTH REPORT (Document 2 Display)
         ========================================================================= -->
    @elseif($tab === 'report')
        <div class="row g-4">
            <div class="col-12">
                <!-- Report Selector & Controls -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 no-print bg-light p-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold"><i class="fas fa-history text-primary me-2"></i>Select Saved Report:</span>
                            <form action="{{ route('marketing.index') }}" method="GET" class="d-inline-flex">
                                <input type="hidden" name="tab" value="report">
                                <select name="report_id" class="form-select form-select-sm fw-bold rounded-pill" style="min-width: 250px;" onchange="this.form.submit()">
                                    <option value="" {{ !$selectedReport ? 'selected' : '' }}>-- Current Active Draft / Template --</option>
                                    @foreach($allReports as $rep)
                                        <option value="{{ $rep->id }}" {{ optional($selectedReport)->id == $rep->id ? 'selected' : '' }}>
                                            {{ $rep->week_number }} ({{ $rep->reporting_period }})
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('marketing.index', ['tab' => 'feed', 'report_id' => optional($selectedReport)->id]) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-edit me-1"></i> {{ $selectedReport ? 'Edit / Feed Data for This Report' : '✍️ Feed Data for New Report' }}
                            </a>
                            @if($selectedReport)
                            <form action="{{ route('marketing.report.destroy', $selectedReport->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this saved report?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Report Presentation Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-dark text-white p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="badge bg-info text-dark fw-bold px-3 py-1 rounded-pill mb-2">OFFICIAL SCORECARD</span>
                            <h4 class="mb-1 fw-bold"><i class="fas fa-file-invoice me-2 text-info"></i>WEEKLY DIGITAL MARKETING & GROWTH REPORT</h4>
                            <p class="mb-0 text-white-50">Department: MEDIA & ICT | Official Weekly Performance & KPI Scorecard</p>
                        </div>
                        <div class="text-end">
                            <h5 class="text-info fw-bold mb-0">{{ $activeReportData['week_number'] }}</h5>
                            <small class="text-white-50">{{ $activeReportData['reporting_period'] }}</small>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- 1. REPORT INFORMATION -->
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">1. REPORT INFORMATION</h5>
                        <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Week Number</label>
                                <div class="fw-bold fs-6">{{ $activeReportData['week_number'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Reporting Period</label>
                                <div class="fw-bold fs-6">{{ $activeReportData['reporting_period'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Prepared By</label>
                                <div class="fw-bold fs-6">{{ $activeReportData['prepared_by'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Department</label>
                                <div class="fw-bold fs-6">{{ $activeReportData['department'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Date Submitted</label>
                                <div class="fw-bold fs-6">{{ $activeReportData['date_submitted'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Reviewed By</label>
                                <div class="fw-bold fs-6">{{ $activeReportData['reviewed_by'] }}</div>
                            </div>
                        </div>

                        <!-- 2. TASKS & COMPLETION RATE -->
                        @php
                            $tasksCount = count($activeReportData['tasks_data'] ?? []);
                            $completedTasks = collect($activeReportData['tasks_data'] ?? [])->where('status', 'Completed')->count();
                            $completionRate = $tasksCount > 0 ? round(($completedTasks / $tasksCount) * 100) : 0;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-5">
                            <h5 class="fw-bold text-dark mb-0">2. DIGITAL GROWTH TASKS</h5>
                            <span class="badge bg-{{ $completionRate >= 80 ? 'success' : ($completionRate >= 50 ? 'warning text-dark' : 'secondary') }} px-3 py-2 fs-6 rounded-pill">
                                Weekly Completion Rate: {{ $completionRate }}%
                            </span>
                        </div>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Task Description</th>
                                        <th style="width: 140px;">Status</th>
                                        <th>Remarks / Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activeReportData['tasks_data'] ?? [] as $idx => $tsk)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                                        <td class="fw-bold">{{ $tsk['task'] ?? '' }}</td>
                                        <td>
                                            @php $st = $tsk['status'] ?? 'Pending'; @endphp
                                            <span class="badge bg-{{ $st === 'Completed' ? 'success' : ($st === 'In Progress' ? 'warning text-dark' : 'secondary') }} px-3 py-1 rounded-pill">
                                                {{ $st }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ $tsk['remarks'] ?? '' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No manual growth tasks fed yet for this week. Click <b>"✍️ Feed / Edit Report Data"</b> above to add tasks.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- 3. WEEKLY KPI SCORECARD -->
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-5">3. WEEKLY KPI SCORECARD</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-striped border align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Key Performance Indicator (KPI)</th>
                                        <th>Target</th>
                                        <th>Actual (Measured / Fed)</th>
                                        <th>Status Indicator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activeReportData['kpi_data'] ?? [] as $kpi)
                                    <tr>
                                        <td class="fw-bold">{{ $kpi['kpi'] ?? '' }}</td>
                                        <td>{{ $kpi['target'] ?? '' }}</td>
                                        <td class="fw-bold text-primary">{{ $kpi['actual'] ?? '' }}</td>
                                        <td>
                                            @php $st = $kpi['status'] ?? ''; @endphp
                                            <span class="badge bg-{{ str_contains($st, 'Live') || $st === 'Achieved' || $st === 'Exceeded' ? 'info text-dark' : 'secondary' }} px-3 py-1 rounded-pill">
                                                {{ $st }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No KPI scorecard metrics defined yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- 4 & 5. SOCIAL MEDIA & WEBSITE PERFORMANCE -->
                        <div class="row g-4 mb-4 mt-3">
                            <div class="col-lg-6">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">4. SOCIAL MEDIA PERFORMANCE</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Platform</th>
                                                <th>Planned Posts</th>
                                                <th>Posted</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeReportData['social_media_data'] ?? [] as $sm)
                                            <tr>
                                                <td class="fw-bold"><i class="fab fa-{{ strtolower($sm['platform'] ?? 'share') }} me-2 text-primary"></i>{{ $sm['platform'] ?? '' }}</td>
                                                <td class="small">{{ $sm['planned'] ?? '-' }}</td>
                                                <td class="fw-bold text-center">{{ $sm['posted'] ?? '-' }}</td>
                                                <td><span class="badge bg-secondary">{{ $sm['status'] ?? 'Not Started' }}</span></td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center py-3 text-muted">No social media figures manually fed yet.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">5. WEBSITE PERFORMANCE (⚡ Auto-Collected Live)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Website Metric</th>
                                                <th class="text-end">Live Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeReportData['website_performance_data'] ?? [] as $wp)
                                            <tr>
                                                <td class="fw-bold">{{ $wp['metric'] ?? '' }}</td>
                                                <td class="text-end fw-bold text-primary">{{ $wp['value'] ?? '' }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="2" class="text-center py-3 text-muted">No website metrics.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 6 & 7. GOOGLE BUSINESS & ACQUISITION CHANNELS -->
                        <div class="row g-4 mb-4 mt-3">
                            <div class="col-lg-5">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">6. GOOGLE BUSINESS PROFILE</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Metric</th>
                                                <th class="text-end">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeReportData['google_business_data'] ?? [] as $gb)
                                            <tr>
                                                <td>{{ $gb['metric'] ?? '' }}</td>
                                                <td class="text-end fw-bold">{{ $gb['value'] !== '' ? $gb['value'] : '-' }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="2" class="text-center py-3 text-muted">No Google Business metrics fed yet.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">7. BOOKINGS & LEADS SOURCE MATRIX</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Acquisition Source</th>
                                                <th class="text-center">Leads (Fed)</th>
                                                <th class="text-center">Bookings</th>
                                                <th class="text-end">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeReportData['bookings_leads_data'] ?? [] as $src)
                                            <tr>
                                                <td class="fw-bold">{{ $src['source'] ?? '' }}</td>
                                                <td class="text-center">{{ $src['leads'] !== '' ? $src['leads'] : '-' }}</td>
                                                <td class="text-center fw-bold text-success">{{ $src['bookings'] ?? '0' }}</td>
                                                <td class="text-end fw-bold">{{ $src['revenue'] ?? '$0.00' }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center py-3 text-muted">No source matrix data.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 8. PAID ADVERTISING PERFORMANCE -->
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">8. PAID ADVERTISING PERFORMANCE</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Campaign Name</th>
                                        <th>Budget</th>
                                        <th>Reach</th>
                                        <th>Clicks</th>
                                        <th>Leads</th>
                                        <th>Cost / Lead</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activeReportData['paid_ads_data'] ?? [] as $ad)
                                    <tr>
                                        <td class="fw-bold">{{ $ad['campaign'] ?? '' }}</td>
                                        <td>{{ $ad['budget'] ?? '-' }}</td>
                                        <td>{{ $ad['reach'] ?? '-' }}</td>
                                        <td>{{ $ad['clicks'] ?? '-' }}</td>
                                        <td>{{ $ad['leads'] ?? '-' }}</td>
                                        <td class="fw-bold text-primary">{{ $ad['cost_lead'] ?? '-' }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $ad['status'] ?? 'Active' }}</span></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No paid advertising campaigns fed yet. Click <b>"✍️ Feed / Edit Report Data"</b> to add Meta Ads or Google Ads campaigns.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- 9 & 10. CONTENT CREATED & CHALLENGES -->
                        <div class="row g-4 mb-4 mt-3">
                            <div class="col-lg-5">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">9. CONTENT CREATED THIS WEEK</h5>
                                <ul class="list-group">
                                    @forelse($activeReportData['content_created_data'] ?? [] as $cc)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $cc['type'] ?? '' }}</span>
                                        <span class="badge bg-primary rounded-pill">{{ $cc['quantity'] ?? '' }}</span>
                                    </li>
                                    @empty
                                    <li class="list-group-item text-center text-muted py-3">No content creation entries fed yet.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="col-lg-7">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">10. CHALLENGES & RESOLUTIONS</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Challenge</th>
                                                <th>Impact</th>
                                                <th>Action Taken</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeReportData['challenges_data'] ?? [] as $ch)
                                            <tr>
                                                <td class="text-danger fw-bold small">{{ $ch['challenge'] ?? '' }}</td>
                                                <td class="small">{{ $ch['impact'] ?? '' }}</td>
                                                <td class="text-success fw-bold small">{{ $ch['action'] ?? '' }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="3" class="text-center py-3 text-muted">No challenges recorded.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 11 & 12. ACHIEVEMENTS & NEXT WEEK PLAN -->
                        <div class="row g-4 mb-4 mt-3">
                            <div class="col-lg-5">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">11. ACHIEVEMENTS</h5>
                                <div class="bg-light p-3 rounded-3 border">
                                    <ul class="mb-0 ps-3">
                                        @forelse($activeReportData['achievements_data'] ?? [] as $ach)
                                        <li class="mb-2 fw-bold text-success">{{ $ach['achievement'] ?? '' }}</li>
                                        @empty
                                        <li class="text-muted list-unstyled text-center py-2">No achievements recorded yet.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">12. NEXT WEEK'S PLAN</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th>Activity</th>
                                                <th>Priority</th>
                                                <th>Responsible</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeReportData['next_week_plan_data'] ?? [] as $idx => $np)
                                            <tr>
                                                <td class="text-center">{{ $idx + 1 }}</td>
                                                <td class="fw-bold">{{ $np['activity'] ?? '' }}</td>
                                                <td>
                                                    @php $pr = $np['priority'] ?? 'Medium'; @endphp
                                                    <span class="badge bg-{{ $pr === 'High' ? 'danger' : ($pr === 'Low' ? 'secondary' : 'warning text-dark') }}">{{ $pr }}</span>
                                                </td>
                                                <td class="small">{{ $np['responsible'] ?? '' }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center py-3 text-muted">No activities planned yet for next week.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- =========================================================================
         TAB 3: ✍️ FEED / EDIT MARKETING DATA (Interactive Report Builder Form)
         ========================================================================= -->
    @elseif($tab === 'feed')
        <div class="row">
            <div class="col-12">
                <form action="{{ route('marketing.report.save') }}" method="POST" id="feedReportForm">
                    @csrf
                    @if($editReport)
                        <input type="hidden" name="report_id" value="{{ $editReport->id }}">
                    @endif

                    <!-- Sticky Save Bar -->
                    <div class="card border-0 shadow rounded-4 mb-4 bg-dark text-white p-3 sticky-top" style="z-index: 1020;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="mb-0 fw-bold"><i class="fas fa-edit text-warning me-2"></i>{{ $editReport ? 'Editing Saved Report: ' . $editReport->week_number : 'Feed New Weekly Marketing Report Data' }}</h5>
                                <small class="text-white-50">Manually feed off-website figures. Auto-collected metrics are linked live from system telemetry.</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('marketing.index', ['tab' => 'report', 'report_id' => optional($editReport)->id]) }}" class="btn btn-outline-light rounded-pill px-4">Cancel</a>
                                <button type="submit" class="btn btn-warning fw-bold rounded-pill px-5 text-dark shadow">
                                    <i class="fas fa-save me-2"></i> Save Report Data
                                </button>
                            </div>
                        </div>
                    </div>

                    @php
                        $feed = $editReport ? $editReport->toArray() : $cleanTemplate;
                    @endphp

                    <!-- 1. Report Metadata -->
                    <div class="form-feed-section">
                        <div class="form-feed-title">
                            <span><i class="fas fa-info-circle text-primary me-2"></i>1. Report Metadata & Timeframe</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Week Number <span class="text-danger">*</span></label>
                                <input type="text" name="week_number" class="form-control" value="{{ $feed['week_number'] ?? '' }}" placeholder="e.g. Week 28" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Reporting Period <span class="text-danger">*</span></label>
                                <input type="text" name="reporting_period" class="form-control" value="{{ $feed['reporting_period'] ?? '' }}" placeholder="e.g. July 07 - July 13, 2026" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Prepared By</label>
                                <input type="text" name="prepared_by" class="form-control" value="{{ $feed['prepared_by'] ?? auth()->user()->name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Department</label>
                                <input type="text" name="department" class="form-control" value="{{ $feed['department'] ?? 'MEDIA & ICT' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Reviewed By</label>
                                <input type="text" name="reviewed_by" class="form-control" value="{{ $feed['reviewed_by'] ?? 'General Manager' }}">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Digital Growth Tasks Checklist -->
                    <div class="form-feed-section">
                        <div class="form-feed-title">
                            <span><i class="fas fa-tasks text-primary me-2"></i>2. Digital Growth Tasks Checklist (Manual Feed)</span>
                            <button type="button" class="btn btn-add-row" onclick="addRow('tasksBody', 'taskRow')"><i class="fas fa-plus me-1"></i> Add Task Row</button>
                        </div>
                        <p class="text-muted small">Add weekly marketing tasks executed by the team. Weekly completion rate is calculated automatically.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Task Description</th>
                                        <th style="width: 180px;">Status</th>
                                        <th>Remarks / Notes</th>
                                        <th style="width: 60px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tasksBody">
                                    @foreach($feed['tasks_data'] ?? [] as $i => $tsk)
                                    <tr class="taskRow">
                                        <td><input type="text" name="tasks_data[{{ $i }}][task]" class="form-control form-control-sm fw-bold" value="{{ $tsk['task'] ?? '' }}" placeholder="Enter task description..."></td>
                                        <td>
                                            <select name="tasks_data[{{ $i }}][status]" class="form-select form-select-sm">
                                                <option value="Completed" {{ ($tsk['status'] ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="In Progress" {{ ($tsk['status'] ?? '') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="Not Started" {{ ($tsk['status'] ?? '') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="tasks_data[{{ $i }}][remarks]" class="form-control form-control-sm" value="{{ $tsk['remarks'] ?? '' }}" placeholder="Optional notes..."></td>
                                        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 3. Weekly KPI Scorecard -->
                    <div class="form-feed-section">
                        <div class="form-feed-title">
                            <span><i class="fas fa-chart-line text-primary me-2"></i>3. Weekly KPI Scorecard (Hybrid Live + Manual Feed)</span>
                        </div>
                        <p class="text-muted small">Website visitor telemetry and bookings are auto-collected live. Manually input targets and actuals for social media and advertising KPIs.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Key Performance Indicator (KPI)</th>
                                        <th style="width: 150px;">Target</th>
                                        <th style="width: 200px;">Actual Value</th>
                                        <th style="width: 220px;">Status / Source</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feed['kpi_data'] ?? [] as $i => $kpi)
                                    @php
                                        $kpiName = $kpi['kpi'] ?? '';
                                        $isAuto = str_contains($kpi['status'] ?? '', 'Auto') || in_array($kpiName, ['Website Visitors', 'WhatsApp Inquiries', 'Phone Calls', 'Booking Requests', 'New Guests']);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="text" name="kpi_data[{{ $i }}][kpi]" class="form-control form-control-sm fw-bold {{ $isAuto ? 'bg-light' : '' }}" value="{{ $kpiName }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="kpi_data[{{ $i }}][target]" class="form-control form-control-sm" value="{{ $kpi['target'] ?? '' }}" placeholder="Target...">
                                        </td>
                                        <td>
                                            @if($isAuto)
                                                <input type="text" name="kpi_data[{{ $i }}][actual]" class="form-control form-control-sm fw-bold text-primary bg-light" value="{{ $kpi['actual'] ?? '' }}" readonly>
                                            @else
                                                <input type="text" name="kpi_data[{{ $i }}][actual]" class="form-control form-control-sm fw-bold text-dark" value="{{ $kpi['actual'] ?? '' }}" placeholder="Enter actual figure...">
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" name="kpi_data[{{ $i }}][status]" class="form-control form-control-sm {{ $isAuto ? 'bg-light text-success fw-bold' : '' }}" value="{{ $kpi['status'] ?? '' }}" {{ $isAuto ? 'readonly' : '' }}>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 4. Social Media & Google Business -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            <div class="form-feed-section h-100 mb-0">
                                <div class="form-feed-title">
                                    <span><i class="fas fa-share-alt text-primary me-2"></i>4. Social Media Performance</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Platform</th>
                                                <th>Planned Posts</th>
                                                <th>Posted</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($feed['social_media_data'] ?? [] as $i => $sm)
                                            <tr>
                                                <td><input type="text" name="social_media_data[{{ $i }}][platform]" class="form-control form-control-sm fw-bold bg-light" value="{{ $sm['platform'] ?? '' }}" readonly></td>
                                                <td><input type="text" name="social_media_data[{{ $i }}][planned]" class="form-control form-control-sm" value="{{ $sm['planned'] ?? '' }}" placeholder="e.g. 8 posts"></td>
                                                <td><input type="text" name="social_media_data[{{ $i }}][posted]" class="form-control form-control-sm fw-bold text-center" value="{{ $sm['posted'] ?? '' }}" placeholder="0"></td>
                                                <td>
                                                    <select name="social_media_data[{{ $i }}][status]" class="form-select form-select-sm">
                                                        <option value="On Target" {{ ($sm['status'] ?? '') == 'On Target' ? 'selected' : '' }}>On Target</option>
                                                        <option value="Completed" {{ ($sm['status'] ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="Below Target" {{ ($sm['status'] ?? '') == 'Below Target' ? 'selected' : '' }}>Below Target</option>
                                                        <option value="Not Started" {{ ($sm['status'] ?? '') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-feed-section h-100 mb-0">
                                <div class="form-feed-title">
                                    <span><i class="fas fa-store text-primary me-2"></i>5. Google Business Profile</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Metric Name</th>
                                                <th class="text-end">Value (Feed Manual Figure)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($feed['google_business_data'] ?? [] as $i => $gb)
                                            <tr>
                                                <td><input type="text" name="google_business_data[{{ $i }}][metric]" class="form-control form-control-sm bg-light" value="{{ $gb['metric'] ?? '' }}" readonly></td>
                                                <td><input type="text" name="google_business_data[{{ $i }}][value]" class="form-control form-control-sm text-end fw-bold" value="{{ $gb['value'] ?? '' }}" placeholder="Enter figure..."></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Bookings & Leads Source Matrix -->
                    <div class="form-feed-section">
                        <div class="form-feed-title">
                            <span><i class="fas fa-filter text-primary me-2"></i>6. Bookings & Leads Source Matrix</span>
                            <button type="button" class="btn btn-add-row" onclick="addRow('sourcesBody', 'sourceRow')"><i class="fas fa-plus me-1"></i> Add Source Row</button>
                        </div>
                        <p class="text-muted small">Online bookings and revenue are pre-filled from system transactions. Manually feed the number of Leads and any off-platform walk-in/phone conversions.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Acquisition Source</th>
                                        <th style="width: 160px;" class="text-center">Leads Count (Fed)</th>
                                        <th style="width: 160px;" class="text-center">Bookings</th>
                                        <th style="width: 180px;" class="text-end">Revenue</th>
                                        <th style="width: 60px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="sourcesBody">
                                    @foreach($feed['bookings_leads_data'] ?? [] as $i => $src)
                                    <tr class="sourceRow">
                                        <td><input type="text" name="bookings_leads_data[{{ $i }}][source]" class="form-control form-control-sm fw-bold" value="{{ $src['source'] ?? '' }}" placeholder="Source name..."></td>
                                        <td><input type="text" name="bookings_leads_data[{{ $i }}][leads]" class="form-control form-control-sm text-center" value="{{ $src['leads'] ?? '' }}" placeholder="e.g. 15"></td>
                                        <td><input type="text" name="bookings_leads_data[{{ $i }}][bookings]" class="form-control form-control-sm text-center fw-bold text-success" value="{{ $src['bookings'] ?? '0' }}"></td>
                                        <td><input type="text" name="bookings_leads_data[{{ $i }}][revenue]" class="form-control form-control-sm text-end fw-bold" value="{{ $src['revenue'] ?? '$0.00' }}"></td>
                                        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 7. Paid Advertising Performance -->
                    <div class="form-feed-section">
                        <div class="form-feed-title">
                            <span><i class="fas fa-ad text-primary me-2"></i>7. Paid Advertising Performance (Meta Ads / Google Ads)</span>
                            <button type="button" class="btn btn-add-row" onclick="addRow('adsBody', 'adRow')"><i class="fas fa-plus me-1"></i> Add Ad Campaign</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Campaign Name</th>
                                        <th>Budget</th>
                                        <th>Reach</th>
                                        <th>Clicks</th>
                                        <th>Leads</th>
                                        <th>Cost / Lead</th>
                                        <th>Status</th>
                                        <th style="width: 60px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="adsBody">
                                    @foreach($feed['paid_ads_data'] ?? [] as $i => $ad)
                                    <tr class="adRow">
                                        <td><input type="text" name="paid_ads_data[{{ $i }}][campaign]" class="form-control form-control-sm fw-bold" value="{{ $ad['campaign'] ?? '' }}" placeholder="e.g. Meta Ads (Weekend Getaway)"></td>
                                        <td><input type="text" name="paid_ads_data[{{ $i }}][budget]" class="form-control form-control-sm" value="{{ $ad['budget'] ?? '' }}" placeholder="$30 / mo"></td>
                                        <td><input type="text" name="paid_ads_data[{{ $i }}][reach]" class="form-control form-control-sm" value="{{ $ad['reach'] ?? '' }}" placeholder="10,000"></td>
                                        <td><input type="text" name="paid_ads_data[{{ $i }}][clicks]" class="form-control form-control-sm" value="{{ $ad['clicks'] ?? '' }}" placeholder="350"></td>
                                        <td><input type="text" name="paid_ads_data[{{ $i }}][leads]" class="form-control form-control-sm" value="{{ $ad['leads'] ?? '' }}" placeholder="25"></td>
                                        <td><input type="text" name="paid_ads_data[{{ $i }}][cost_lead]" class="form-control form-control-sm fw-bold text-primary" value="{{ $ad['cost_lead'] ?? '' }}" placeholder="$1.20"></td>
                                        <td><input type="text" name="paid_ads_data[{{ $i }}][status]" class="form-control form-control-sm" value="{{ $ad['status'] ?? 'Active' }}" placeholder="Active"></td>
                                        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 8 & 9. Content Created & Challenges -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-5">
                            <div class="form-feed-section h-100 mb-0">
                                <div class="form-feed-title">
                                    <span><i class="fas fa-palette text-primary me-2"></i>8. Content Created This Week</span>
                                    <button type="button" class="btn btn-add-row" onclick="addRow('contentBody', 'contentRow')"><i class="fas fa-plus"></i></button>
                                </div>
                                <table class="table table-bordered align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Content Type</th>
                                            <th>Quantity / Notes</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="contentBody">
                                        @foreach($feed['content_created_data'] ?? [] as $i => $cc)
                                        <tr class="contentRow">
                                            <td><input type="text" name="content_created_data[{{ $i }}][type]" class="form-control form-control-sm fw-bold" value="{{ $cc['type'] ?? '' }}" placeholder="e.g. Reels Produced"></td>
                                            <td><input type="text" name="content_created_data[{{ $i }}][quantity]" class="form-control form-control-sm" value="{{ $cc['quantity'] ?? '' }}" placeholder="2 HD Videos"></td>
                                            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="form-feed-section h-100 mb-0">
                                <div class="form-feed-title">
                                    <span><i class="fas fa-exclamation-triangle text-danger me-2"></i>9. Challenges & Resolutions</span>
                                    <button type="button" class="btn btn-add-row" onclick="addRow('challengesBody', 'challengeRow')"><i class="fas fa-plus"></i></button>
                                </div>
                                <table class="table table-bordered align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Challenge Encountered</th>
                                            <th>Impact</th>
                                            <th>Action Taken / Resolution</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="challengesBody">
                                        @foreach($feed['challenges_data'] ?? [] as $i => $ch)
                                        <tr class="challengeRow">
                                            <td><input type="text" name="challenges_data[{{ $i }}][challenge]" class="form-control form-control-sm text-danger fw-bold" value="{{ $ch['challenge'] ?? '' }}" placeholder="Describe challenge..."></td>
                                            <td><input type="text" name="challenges_data[{{ $i }}][impact]" class="form-control form-control-sm" value="{{ $ch['impact'] ?? '' }}" placeholder="Impact..."></td>
                                            <td><input type="text" name="challenges_data[{{ $i }}][action]" class="form-control form-control-sm text-success fw-bold" value="{{ $ch['action'] ?? '' }}" placeholder="Resolution..."></td>
                                            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 10 & 11. Achievements & Next Week Plan -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-5">
                            <div class="form-feed-section h-100 mb-0">
                                <div class="form-feed-title">
                                    <span><i class="fas fa-trophy text-warning me-2"></i>10. Achievements</span>
                                    <button type="button" class="btn btn-add-row" onclick="addRow('achievementsBody', 'achievementRow')"><i class="fas fa-plus"></i></button>
                                </div>
                                <table class="table table-bordered align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Achievement Description</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="achievementsBody">
                                        @foreach($feed['achievements_data'] ?? [] as $i => $ach)
                                        <tr class="achievementRow">
                                            <td><input type="text" name="achievements_data[{{ $i }}][achievement]" class="form-control form-control-sm text-success fw-bold" value="{{ $ach['achievement'] ?? '' }}" placeholder="Describe achievement..."></td>
                                            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="form-feed-section h-100 mb-0">
                                <div class="form-feed-title">
                                    <span><i class="fas fa-calendar-plus text-primary me-2"></i>11. Next Week's Plan</span>
                                    <button type="button" class="btn btn-add-row" onclick="addRow('planBody', 'planRow')"><i class="fas fa-plus"></i></button>
                                </div>
                                <table class="table table-bordered align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Planned Activity</th>
                                            <th style="width: 130px;">Priority</th>
                                            <th style="width: 150px;">Responsible</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="planBody">
                                        @foreach($feed['next_week_plan_data'] ?? [] as $i => $np)
                                        <tr class="planRow">
                                            <td><input type="text" name="next_week_plan_data[{{ $i }}][activity]" class="form-control form-control-sm fw-bold" value="{{ $np['activity'] ?? '' }}" placeholder="Activity description..."></td>
                                            <td>
                                                <select name="next_week_plan_data[{{ $i }}][priority]" class="form-select form-select-sm">
                                                    <option value="High" {{ ($np['priority'] ?? '') == 'High' ? 'selected' : '' }}>High</option>
                                                    <option value="Medium" {{ ($np['priority'] ?? '') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="Low" {{ ($np['priority'] ?? '') == 'Low' ? 'selected' : '' }}>Low</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="next_week_plan_data[{{ $i }}][responsible]" class="form-control form-control-sm" value="{{ $np['responsible'] ?? '' }}" placeholder="e.g. Media Lead"></td>
                                            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-5">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                            <i class="fas fa-save me-2"></i> Confirm & Save All Weekly Report Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

    <!-- =========================================================================
         TAB 4: DIGITAL GROWTH STRATEGY IMPLEMENTATION MATRIX (Areas 1-8)
         ========================================================================= -->
    @elseif($tab === 'strategy')
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm rounded-4 bg-light p-4 d-flex flex-row justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="fas fa-sitemap text-primary me-2"></i>Digital Growth Strategy Implementation Matrix</h5>
                        <p class="text-muted small mb-0">Manage tasks and budgets across all 8 strategic growth areas defined in Bella Vista Lodge Digital Strategy. Add new tasks or edit costs dynamically.</p>
                    </div>
                    <div>
                        <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">8 Strategic Areas Active</span>
                    </div>
                </div>
            </div>

            @foreach($strategyItems as $areaName => $items)
            @php
                $areaNum = $items->first()->area_number ?? 1;
            @endphp
            <div class="col-lg-6 mb-4">
                <div class="strategy-area-card bg-white h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="strategy-area-header">
                            <span>{{ $areaName }}</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark rounded-pill">{{ count($items) }} Tasks</span>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addStrategyModal{{ $areaNum }}">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small text-muted">
                                    <tr>
                                        <th style="width: 40%">Task</th>
                                        <th style="width: 25%">Estimated Cost</th>
                                        <th style="width: 25%">Status</th>
                                        <th style="width: 10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <td class="fw-bold">{{ $item->task }}</td>
                                        <td>
                                            <form action="{{ route('marketing.strategy.update', $item->id) }}" method="POST">
                                                @csrf
                                                <input type="text" name="cost" value="{{ $item->cost }}" class="form-control form-control-sm bg-light border-0" onchange="this.form.status.value='{{ $item->status }}'; this.form.submit()" placeholder="0/=" title="Change cost and press Tab/Enter">
                                                <input type="hidden" name="status" value="{{ $item->status }}">
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{ route('marketing.strategy.update', $item->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="cost" value="{{ $item->cost }}">
                                                <select name="status" class="form-select form-select-sm fw-bold border-0 bg-light rounded-pill" onchange="this.form.submit()">
                                                    <option value="Not Started" {{ $item->status === 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                                    <option value="In Progress" {{ $item->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="Completed" {{ $item->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('marketing.strategy.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Remove this strategy task?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0 border-0"><i class="fas fa-times"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal to Add Task to this Area -->
                <div class="modal fade" id="addStrategyModal{{ $areaNum }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('marketing.strategy.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="area_number" value="{{ $areaNum }}">
                            <input type="hidden" name="area_name" value="{{ $areaName }}">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header bg-dark text-white p-3">
                                    <h6 class="modal-title fw-bold">Add Task to {{ $areaName }}</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Task Description <span class="text-danger">*</span></label>
                                        <input type="text" name="task" class="form-control" placeholder="e.g. Implement M-Pesa Online Payment" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Estimated Cost</label>
                                        <input type="text" name="cost" class="form-control" placeholder="e.g. 100,000/=" value="0/=">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Initial Status</label>
                                        <select name="status" class="form-select">
                                            <option value="Not Started">Not Started</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer p-3 bg-light">
                                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Add Strategy Task</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    <!-- =========================================================================
         TAB 5: PUSH EMAIL ADVERTISEMENTS & CAMPAIGNS
         ========================================================================= -->
    @elseif($tab === 'campaigns')
        <div class="row g-4">
            <!-- Left Column: Compose & Push Advertisement -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-dark text-white p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2">DIRECT EMAIL BLAST</span>
                            <h4 class="mb-1 fw-bold"><i class="fas fa-paper-plane text-warning me-2"></i>Compose & Push Advertisement</h4>
                            <p class="mb-0 text-white-50">Send promotional offers directly to clients, guests, and system users.</p>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('marketing.campaign.send') }}" method="POST" id="campaignForm">
                            @csrf
                            
                            <!-- Step 1: Target Audience Selection -->
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-users text-primary me-2"></i>1. Select Target Audiences</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="form-check card p-3 border rounded-3 h-100 bg-light">
                                        <input class="form-check-input ms-1" type="checkbox" name="target_audiences[]" value="customers" id="audCust" checked>
                                        <label class="form-check-label fw-bold ms-2 d-block" for="audCust">
                                            Clients & Guests
                                        </label>
                                        <small class="text-muted d-block ms-2 mt-1">{{ number_format($customersCount) }} Registered Clients</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card p-3 border rounded-3 h-100 bg-light">
                                        <input class="form-check-input ms-1" type="checkbox" name="target_audiences[]" value="subscribers" id="audSub" checked>
                                        <label class="form-check-label fw-bold ms-2 d-block" for="audSub">
                                            Newsletter Subscribers
                                        </label>
                                        <small class="text-muted d-block ms-2 mt-1">{{ number_format($subscribersCount) }} Website Subscribers</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card p-3 border rounded-3 h-100 bg-light">
                                        <input class="form-check-input ms-1" type="checkbox" name="target_audiences[]" value="staff" id="audStaff">
                                        <label class="form-check-label fw-bold ms-2 d-block" for="audStaff">
                                            Internal Staff & Users
                                        </label>
                                        <small class="text-muted d-block ms-2 mt-1">{{ number_format($staffCount) }} System Users</small>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <label class="form-label small fw-bold text-muted">Additional / Custom Email Addresses (Optional)</label>
                                    <input type="text" name="custom_emails" class="form-control" placeholder="e.g. vipclient@gmail.com, partner@agency.org (separate by comma)">
                                </div>
                            </div>

                            <!-- Step 2: Campaign Details & Content -->
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="fas fa-edit text-primary me-2"></i>2. Advertisement Content</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Campaign Title (Internal Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Weekend Special Blast 2026" required value="{{ old('title') }}">
                                    <small class="text-muted">For internal tracking in your reports</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email Subject Line <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" class="form-control" placeholder="e.g. 🌴 20% OFF ALL ROOMS This Weekend!" required value="{{ old('subject') }}">
                                    <small class="text-muted">What recipients see in their inbox</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Advertisement Headline <span class="text-danger">*</span></label>
                                    <input type="text" name="headline" class="form-control fw-bold fs-6" placeholder="e.g. Experience Luxury & Relaxation at Bella Vista Lodge" required value="{{ old('headline') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Promotional Banner Image URL (Optional)</label>
                                    <input type="url" name="banner_url" class="form-control" placeholder="e.g. https://bellavistalodge.com/images/promo.jpg" value="{{ old('banner_url') }}">
                                    <small class="text-muted">Paste a link to a promotional flyer or room image to display at the top of the email.</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Main Promotional Message / Body <span class="text-danger">*</span></label>
                                    <textarea name="content" class="form-control" rows="6" placeholder="Write your advertisement message here... Explain the offer, dates, highlights, and why they should book today!" required>{{ old('content') }}</textarea>
                                </div>
                            </div>

                            <!-- Step 3: Offer & Call to Action -->
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="fas fa-gift text-primary me-2"></i>3. Offer Code & Call to Action (CTA)</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Promo / Discount Code</label>
                                    <input type="text" name="discount_code" class="form-control text-uppercase fw-bold text-center text-primary" placeholder="e.g. WEEKEND20" value="{{ old('discount_code') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Button Text</label>
                                    <input type="text" name="cta_text" class="form-control" placeholder="e.g. Book Your Room Now" value="{{ old('cta_text', 'Book Now & Save') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Button Link URL</label>
                                    <input type="url" name="cta_url" class="form-control" placeholder="e.g. {{ url('/rooms') }}" value="{{ old('cta_url', url('/rooms')) }}">
                                </div>
                            </div>

                            <div class="alert alert-info rounded-3 mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Push Notice:</strong> Clicking Send will dispatch customized, branded HTML emails to all selected recipient groups immediately.
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill px-5 text-dark shadow" onclick="return confirm('Are you sure you want to blast this advertisement to the selected audiences?');">
                                    <i class="fas fa-paper-plane me-2"></i> 🚀 Send Advertisement Blast Now
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Campaign History & Stats -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-light p-4 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="fas fa-history text-primary me-2"></i>Past Push Campaigns</h5>
                        <span class="badge bg-primary rounded-pill">{{ count($campaigns) }} Sent</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($campaigns as $camp)
                            <div class="list-group-item p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold mb-1"><i class="fas fa-check-circle me-1"></i> {{ $camp->status }}</span>
                                        <h6 class="fw-bold mb-1">{{ $camp->title }}</h6>
                                    </div>
                                    <form action="{{ route('marketing.campaign.destroy', $camp->id) }}" method="POST" onsubmit="return confirm('Delete this campaign record from history?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-muted p-0 border-0" title="Delete record"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                                <p class="small text-muted mb-2"><strong>Subject:</strong> {{ $camp->subject }}</p>
                                <div class="bg-light p-2 rounded-3 small text-truncate mb-3" style="max-height: 60px;">
                                    {{ Str::limit($camp->content, 120) }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center small text-muted border-top pt-2">
                                    <span><i class="fas fa-users me-1 text-primary"></i> <strong>{{ number_format($camp->recipients_count) }}</strong> Recipients</span>
                                    <span><i class="fas fa-user-shield me-1"></i> {{ $camp->sent_by }}</span>
                                    <span><i class="fas fa-calendar-alt me-1"></i> {{ $camp->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted p-4">
                                <i class="fas fa-envelope-open-text fs-1 mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0">No advertisement campaigns pushed yet.<br>Use the form on the left to send your first promotional blast!</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- JavaScript Helper for Dynamic Form Rows -->
<script>
function addRow(tbodyId, rowClass) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    
    const rows = tbody.getElementsByClassName(rowClass);
    const index = rows.length;
    let newRowHtml = '';

    if (rowClass === 'taskRow') {
        newRowHtml = `
            <td><input type="text" name="tasks_data[${index}][task]" class="form-control form-control-sm fw-bold" placeholder="Enter task description..."></td>
            <td>
                <select name="tasks_data[${index}][status]" class="form-select form-select-sm">
                    <option value="Completed">Completed</option>
                    <option value="In Progress" selected>In Progress</option>
                    <option value="Not Started">Not Started</option>
                </select>
            </td>
            <td><input type="text" name="tasks_data[${index}][remarks]" class="form-control form-control-sm" placeholder="Optional notes..."></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
    } else if (rowClass === 'sourceRow') {
        newRowHtml = `
            <td><input type="text" name="bookings_leads_data[${index}][source]" class="form-control form-control-sm fw-bold" placeholder="Source name..."></td>
            <td><input type="text" name="bookings_leads_data[${index}][leads]" class="form-control form-control-sm text-center" placeholder="e.g. 10"></td>
            <td><input type="text" name="bookings_leads_data[${index}][bookings]" class="form-control form-control-sm text-center fw-bold text-success" value="0"></td>
            <td><input type="text" name="bookings_leads_data[${index}][revenue]" class="form-control form-control-sm text-end fw-bold" value="$0.00"></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
    } else if (rowClass === 'adRow') {
        newRowHtml = `
            <td><input type="text" name="paid_ads_data[${index}][campaign]" class="form-control form-control-sm fw-bold" placeholder="e.g. Meta Ads"></td>
            <td><input type="text" name="paid_ads_data[${index}][budget]" class="form-control form-control-sm" placeholder="$30 / mo"></td>
            <td><input type="text" name="paid_ads_data[${index}][reach]" class="form-control form-control-sm" placeholder="5,000"></td>
            <td><input type="text" name="paid_ads_data[${index}][clicks]" class="form-control form-control-sm" placeholder="150"></td>
            <td><input type="text" name="paid_ads_data[${index}][leads]" class="form-control form-control-sm" placeholder="10"></td>
            <td><input type="text" name="paid_ads_data[${index}][cost_lead]" class="form-control form-control-sm fw-bold text-primary" placeholder="$1.50"></td>
            <td><input type="text" name="paid_ads_data[${index}][status]" class="form-control form-control-sm" value="Active"></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
    } else if (rowClass === 'contentRow') {
        newRowHtml = `
            <td><input type="text" name="content_created_data[${index}][type]" class="form-control form-control-sm fw-bold" placeholder="e.g. Photos Captured"></td>
            <td><input type="text" name="content_created_data[${index}][quantity]" class="form-control form-control-sm" placeholder="10 HD Shots"></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
    } else if (rowClass === 'challengeRow') {
        newRowHtml = `
            <td><input type="text" name="challenges_data[${index}][challenge]" class="form-control form-control-sm text-danger fw-bold" placeholder="Describe challenge..."></td>
            <td><input type="text" name="challenges_data[${index}][impact]" class="form-control form-control-sm" placeholder="Impact..."></td>
            <td><input type="text" name="challenges_data[${index}][action]" class="form-control form-control-sm text-success fw-bold" placeholder="Resolution..."></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
    } else if (rowClass === 'achievementRow') {
        newRowHtml = `
            <td><input type="text" name="achievements_data[${index}][achievement]" class="form-control form-control-sm text-success fw-bold" placeholder="Describe achievement..."></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
    } else if (rowClass === 'planRow') {
        newRowHtml = `
            <td><input type="text" name="next_week_plan_data[${index}][activity]" class="form-control form-control-sm fw-bold" placeholder="Activity description..."></td>
            <td>
                <select name="next_week_plan_data[${index}][priority]" class="form-select form-select-sm">
                    <option value="High">High</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="Low">Low</option>
                </select>
            </td>
            <td><input type="text" name="next_week_plan_data[${index}][responsible]" class="form-control form-control-sm" placeholder="e.g. Media Lead"></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
    }

    const tr = document.createElement('tr');
    tr.className = rowClass;
    tr.innerHTML = newRowHtml;
    tbody.appendChild(tr);
}

function removeRow(btn) {
    const tr = btn.closest('tr');
    if (tr) {
        tr.remove();
    }
}
</script>

<!-- Chart.js Scripts -->
@if($tab === 'trends')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Trend Line Chart
    const trendCtx = document.getElementById('trafficTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyDates) !!},
                datasets: [
                    {
                        label: 'Website Visitors',
                        data: {!! json_encode($dailyVisitsSeries) !!},
                        borderColor: '#4facfe',
                        backgroundColor: 'rgba(79, 172, 254, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Action Clicks & Inquiries',
                        data: {!! json_encode($dailyInteractionsSeries) !!},
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.05)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Source Doughnut Chart
    const sourceCtx = document.getElementById('sourceChart');
    if (sourceCtx) {
        @php
            $srcLabels = $sourcesBreakdown->pluck('source')->toArray();
            $srcCounts = $sourcesBreakdown->pluck('count')->toArray();
        @endphp
        new Chart(sourceCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($srcLabels) !!},
                datasets: [{
                    data: {!! json_encode($srcCounts) !!},
                    backgroundColor: ['#4facfe', '#e94057', '#8a2387', '#22c55e', '#f27121', '#38bdf8', '#64748b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                },
                cutout: '70%'
            }
        });
    }

    // Device Bar Chart
    const deviceCtx = document.getElementById('deviceChart');
    if (deviceCtx) {
        @php
            $devLabels = $devicesBreakdown->pluck('device_type')->toArray();
            $devCounts = $devicesBreakdown->pluck('count')->toArray();
        @endphp
        new Chart(deviceCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($devLabels) !!},
                datasets: [{
                    label: 'Visitors',
                    data: {!! json_encode($devCounts) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endif
@endsection
