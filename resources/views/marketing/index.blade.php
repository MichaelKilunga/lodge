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
    .status-pill {
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .status-Completed { background: #dcfce7; color: #166534; }
    .status-InProgress { background: #fef9c3; color: #854d0e; }
    .status-NotStarted { background: #f1f5f9; color: #475569; }
    
    @media print {
        .lh-sidebar, .lh-header, .mkt-nav-pills, .btn, .no-print { display: none !important; }
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
            <p class="text-white-50 mb-0">Track live web visitor analytics, measure acquisition trends, and manage weekly growth reports.</p>
        </div>
        <div class="d-flex gap-2">
            @if($tab === 'report')
            <button onclick="window.print()" class="btn btn-light fw-bold px-4 rounded-pill shadow-sm">
                <i class="fas fa-print me-2"></i> Print Report
            </button>
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

    <!-- Navigation Pills -->
    <ul class="nav nav-pills mkt-nav-pills gap-3 mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'trends' ? 'active' : '' }}" href="{{ route('marketing.index', ['tab' => 'trends', 'period' => $period]) }}">
                <i class="fas fa-chart-area me-2"></i> 📈 Traffic & Growth Trends
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'report' ? 'active' : '' }}" href="{{ route('marketing.index', ['tab' => 'report']) }}">
                <i class="fas fa-file-contract me-2"></i> 📑 Weekly Marketing & Growth Report
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'strategy' ? 'active' : '' }}" href="{{ route('marketing.index', ['tab' => 'strategy']) }}">
                <i class="fas fa-tasks me-2"></i> 🎯 Digital Growth Strategy (Areas 1-8)
            </a>
        </li>
    </ul>

    <!-- TAB 1: TRAFFIC & GROWTH TRENDS -->
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
                        <small class="text-success fw-bold">Active</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth Charts Row -->
        <div class="row g-4 mb-4">
            <!-- Visitor Growth Trend Line Chart -->
            <div class="col-lg-8">
                <div class="chart-container-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Market Growth & Traffic Trend</h5>
                            <p class="text-muted small mb-0">Comparison of daily website visitors versus guest interactions</p>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">Live Website Telemetry</span>
                    </div>
                    <div style="height: 320px;">
                        <canvas id="trafficTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Acquisition Channels Doughnut Chart -->
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
                        <div class="d-flex justify-content-around text-center">
                            @foreach($sourcesBreakdown->take(3) as $src)
                            <div>
                                <div class="fw-bold fs-6">{{ $src->count }}</div>
                                <small class="text-muted">{{ $src->source }}</small>
                            </div>
                            @endforeach
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
                                <tr><td colspan="4" class="text-center py-3 text-muted">No page views logged yet for this period.</td></tr>
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

    <!-- TAB 2: WEEKLY DIGITAL MARKETING & GROWTH REPORT (Document 2) -->
    @elseif($tab === 'report')
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-dark text-white p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1 fw-bold"><i class="fas fa-file-invoice me-2 text-info"></i>WEEKLY DIGITAL MARKETING & GROWTH REPORT</h4>
                            <p class="mb-0 text-white-50">Department: MEDIA & ICT | Official Weekly Performance & KPI Scorecard</p>
                        </div>
                        <button class="btn btn-outline-info rounded-pill px-4" type="button" data-bs-toggle="modal" data-bs-target="#saveReportModal">
                            <i class="fas fa-save me-2"></i> Save / Record This Report
                        </button>
                    </div>

                    <div class="card-body p-4">
                        <!-- 1. REPORT INFORMATION -->
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">1. REPORT INFORMATION</h5>
                        <div class="row g-3 mb-4 bg-light p-3 rounded-3">
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Week Number</label>
                                <div class="fw-bold fs-6">{{ $defaultReport['week_number'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Reporting Period</label>
                                <div class="fw-bold fs-6">{{ $defaultReport['reporting_period'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Prepared By</label>
                                <div class="fw-bold fs-6">{{ $defaultReport['prepared_by'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Department</label>
                                <div class="fw-bold fs-6">{{ $defaultReport['department'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Date Submitted</label>
                                <div class="fw-bold fs-6">{{ $defaultReport['date_submitted'] }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-bold">Reviewed By</label>
                                <div class="fw-bold fs-6">{{ $defaultReport['reviewed_by'] }}</div>
                            </div>
                        </div>

                        <!-- 2. TASKS & COMPLETION RATE -->
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-5">
                            <h5 class="fw-bold text-dark mb-0">2. DIGITAL GROWTH TASKS</h5>
                            <span class="badge bg-success px-3 py-2 fs-6 rounded-pill">Weekly Completion Rate: 80%</span>
                        </div>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Task Description</th>
                                        <th style="width: 130px;">Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($defaultReport['tasks_data'] as $tsk)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $tsk['no'] }}</td>
                                        <td>{{ $tsk['task'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $tsk['status'] === 'Completed' ? 'success' : 'warning text-dark' }} px-3 py-1 rounded-pill">
                                                {{ $tsk['status'] }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ $tsk['remarks'] }}</td>
                                    </tr>
                                    @endforeach
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
                                        <th>Actual (Measured)</th>
                                        <th>Status Indicator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($defaultReport['kpi_data'] as $kpi)
                                    <tr>
                                        <td class="fw-bold">{{ $kpi['kpi'] }}</td>
                                        <td>{{ $kpi['target'] }}</td>
                                        <td class="fw-bold text-primary">{{ $kpi['actual'] }}</td>
                                        <td>
                                            <span class="badge bg-info bg-opacity-10 text-dark border px-3 py-1 rounded-pill">
                                                {{ $kpi['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
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
                                            @foreach($defaultReport['social_media_data'] as $sm)
                                            <tr>
                                                <td class="fw-bold"><i class="fab fa-{{ strtolower($sm['platform']) }} me-2"></i>{{ $sm['platform'] }}</td>
                                                <td class="small">{{ $sm['planned'] }}</td>
                                                <td class="fw-bold text-center">{{ $sm['posted'] }}</td>
                                                <td><span class="badge bg-success bg-opacity-75">{{ $sm['status'] }}</span></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">5. WEBSITE PERFORMANCE</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Website Metric</th>
                                                <th class="text-end">Live Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($defaultReport['website_performance_data'] as $wp)
                                            <tr>
                                                <td>{{ $wp['metric'] }}</td>
                                                <td class="text-end fw-bold text-primary">{{ $wp['value'] }}</td>
                                            </tr>
                                            @endforeach
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
                                            @foreach($defaultReport['google_business_data'] as $gb)
                                            <tr>
                                                <td>{{ $gb['metric'] }}</td>
                                                <td class="text-end fw-bold">{{ $gb['value'] }}</td>
                                            </tr>
                                            @endforeach
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
                                                <th class="text-center">Leads</th>
                                                <th class="text-center">Bookings</th>
                                                <th class="text-end">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($defaultReport['bookings_leads_data'] as $src)
                                            <tr>
                                                <td class="fw-bold">{{ $src['source'] }}</td>
                                                <td class="text-center">{{ $src['leads'] }}</td>
                                                <td class="text-center fw-bold text-success">{{ $src['bookings'] }}</td>
                                                <td class="text-end fw-bold">{{ $src['revenue'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 8. CONTENT CREATED & CHALLENGES -->
                        <div class="row g-4 mb-4 mt-3">
                            <div class="col-lg-5">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">8. CONTENT CREATED THIS WEEK</h5>
                                <ul class="list-group">
                                    @foreach($defaultReport['content_created_data'] as $cc)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $cc['type'] }}</span>
                                        <span class="badge bg-primary rounded-pill">{{ $cc['quantity'] }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-lg-7">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">9. CHALLENGES & RESOLUTIONS</h5>
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
                                            @foreach($defaultReport['challenges_data'] as $ch)
                                            <tr>
                                                <td class="text-danger fw-bold small">{{ $ch['challenge'] }}</td>
                                                <td class="small">{{ $ch['impact'] }}</td>
                                                <td class="text-success fw-bold small">{{ $ch['action'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 10. ACHIEVEMENTS & NEXT WEEK PLAN -->
                        <div class="row g-4 mb-4 mt-3">
                            <div class="col-lg-5">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">10. ACHIEVEMENTS</h5>
                                <div class="bg-light p-3 rounded-3 border">
                                    <ul class="mb-0 ps-3">
                                        @foreach($defaultReport['achievements_data'] as $ach)
                                        <li class="mb-2 fw-bold text-success">{{ $ach['achievement'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">11. NEXT WEEK'S PLAN</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Activity</th>
                                                <th>Priority</th>
                                                <th>Responsible</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($defaultReport['next_week_plan_data'] as $np)
                                            <tr>
                                                <td class="text-center">{{ $np['no'] }}</td>
                                                <td class="fw-bold">{{ $np['activity'] }}</td>
                                                <td><span class="badge bg-{{ $np['priority'] === 'High' ? 'danger' : 'warning text-dark' }}">{{ $np['priority'] }}</span></td>
                                                <td class="small">{{ $np['responsible'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal to Save Weekly Report -->
        <div class="modal fade" id="saveReportModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('marketing.report.save') }}" method="POST">
                    @csrf
                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-header bg-dark text-white p-4">
                            <h5 class="modal-title fw-bold"><i class="fas fa-save me-2"></i>Save Weekly Marketing Report</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="text-muted">Archive this report into the official database records for lodge management review.</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Week Number</label>
                                    <input type="text" name="week_number" class="form-control" value="{{ $defaultReport['week_number'] }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Reporting Period</label>
                                    <input type="text" name="reporting_period" class="form-control" value="{{ $defaultReport['reporting_period'] }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Prepared By</label>
                                    <input type="text" name="prepared_by" class="form-control" value="{{ auth()->user()->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Reviewed By</label>
                                    <input type="text" name="reviewed_by" class="form-control" value="General Manager">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer p-3 bg-light">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Confirm & Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <!-- TAB 3: DIGITAL GROWTH STRATEGY (Document 1) -->
    @elseif($tab === 'strategy')
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card border-0 shadow-sm rounded-4 bg-light p-3 d-flex flex-row justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="fas fa-sitemap text-primary me-2"></i>Digital Growth Strategy Implementation Matrix</h5>
                        <p class="text-muted small mb-0">Track exact progress and budgets across all 8 strategic growth areas defined in Bella Vista Lodge Digital Strategy.</p>
                    </div>
                    <div>
                        <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">8 Strategic Areas</span>
                    </div>
                </div>
            </div>

            @foreach($strategyItems as $areaName => $items)
            <div class="col-lg-6 mb-4">
                <div class="strategy-area-card bg-white h-100">
                    <div class="strategy-area-header">
                        <span>{{ $areaName }}</span>
                        <span class="badge bg-dark rounded-pill">{{ count($items) }} Tasks</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-muted">
                                <tr>
                                    <th style="width: 45%">Task</th>
                                    <th style="width: 25%">Estimated Cost</th>
                                    <th style="width: 30%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->task }}</td>
                                    <td class="text-muted small">{{ $item->cost ?: '0/=' }}</td>
                                    <td>
                                        <form action="{{ route('marketing.strategy.update', $item->id) }}" method="POST" class="d-flex align-items-center gap-1">
                                            @csrf
                                            <select name="status" class="form-select form-select-sm fw-bold border-0 bg-light rounded-pill" onchange="this.form.submit()">
                                                <option value="Not Started" {{ $item->status === 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                                <option value="In Progress" {{ $item->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="Completed" {{ $item->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

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
