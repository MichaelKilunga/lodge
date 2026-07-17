@extends('template.master')
@section('title', 'Transactions')
@section('head')
<style>
/* ═══════════════════════════════════════════════════════════
   TRANSACTION PANEL — ADVANCED STYLES
═══════════════════════════════════════════════════════════ */

/* KPI Cards */
.txn-kpi-card {
    border: none;
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: default;
}
.txn-kpi-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,.12) !important; }
.txn-kpi-card::after {
    content: '';
    position: absolute; right: -20px; bottom: -20px;
    width: 100px; height: 100px; border-radius: 50%;
    background: rgba(255,255,255,0.12);
}
.txn-kpi-card .kpi-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    background: rgba(255,255,255,0.22);
    flex-shrink: 0;
}
.txn-kpi-card .kpi-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: .06em; opacity: .85; }
.txn-kpi-card .kpi-value { font-size: 1.45rem; font-weight: 800; line-height: 1.2; }
.txn-kpi-card .kpi-sub   { font-size: 0.75rem; opacity: .75; margin-top: 2px; }

.kpi-active   { background: linear-gradient(135deg, #0ea5e9, #2563eb); color: #fff; }
.kpi-expired  { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
.kpi-revenue  { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
.kpi-debt     { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }

/* Tab Nav */
.txn-tab-nav {
    border: none;
    gap: 6px;
    flex-wrap: nowrap;
}
.txn-tab-nav .nav-link {
    border: 1.5px solid #dee2e6 !important;
    border-radius: 10px !important;
    color: #64748b;
    font-weight: 500;
    font-size: .85rem;
    padding: .45rem 1.1rem;
    background: #fff;
    transition: all 0.18s ease;
    display: flex; align-items: center; gap: .4rem;
}
.txn-tab-nav .nav-link:hover { border-color: #93c5fd !important; color: #2563eb; background: #eff6ff; }
.txn-tab-nav .nav-link.active {
    background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
    border-color: #2563eb !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(37,99,235,.35);
}
.txn-tab-nav .nav-link .tab-badge {
    background: rgba(255,255,255,0.3);
    border-radius: 20px;
    padding: 1px 7px;
    font-size: 0.72rem;
    font-weight: 700;
}
.txn-tab-nav .nav-link:not(.active) .tab-badge { background: #e2e8f0; color: #475569; }

/* Filter Bar */
.txn-filter-bar {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
}
.txn-filter-bar .form-control,
.txn-filter-bar .form-select {
    border-radius: 10px;
    border-color: #cbd5e1;
    font-size: .85rem;
    height: 38px;
}
.txn-filter-bar .form-control:focus,
.txn-filter-bar .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}
.txn-filter-bar .btn-filter {
    height: 38px;
    border-radius: 10px;
    font-size: .84rem;
    font-weight: 600;
    padding: 0 1.1rem;
}

/* ── Table Wrapper ─────────────────────────────────────────────
   Use overflow:visible on the card wrapper so the inner
   table-responsive div can scroll horizontally without being
   clipped. Border-radius is faked via a clip trick on the inner. */
.txn-table-wrap {
    border-radius: 14px;
    overflow: visible;
    border: 1px solid #e2e8f0;
    /* clip the rounded corners while still allowing scrollbar */
    isolation: isolate;
}
.txn-table-wrap > .table-responsive {
    border-radius: 14px 14px 0 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    /* thin custom scrollbar */
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}
.txn-table-wrap > .table-responsive::-webkit-scrollbar { height: 5px; }
.txn-table-wrap > .table-responsive::-webkit-scrollbar-track { background: transparent; }
.txn-table-wrap > .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

.txn-table { margin-bottom: 0; min-width: 900px; }
.txn-table thead th {
    background: #f1f5f9;
    border-bottom: 1.5px solid #e2e8f0;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #64748b;
    padding: .75rem 1rem;
    white-space: nowrap;
}
.txn-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.txn-table tbody tr:hover { background: #f8fafc; }
.txn-table tbody tr:last-child { border-bottom: none; }
.txn-table td, .txn-table th { padding: .7rem 1rem; vertical-align: middle; font-size: .855rem; }

/* ── Responsive column priorities ──────────────────────────────
   On screens < 992px (lg) collapse the table into a stacked
   card layout using data-label attributes. Hidden columns on
   small screens are shown inside inline "sub-rows". */

/* On medium screens (≥768, <992): hide lower-priority cols */
@media (max-width: 991.98px) {
    .txn-table { min-width: unset; }
    .txn-table thead { display: none; }
    .txn-table tbody tr {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin: .6rem .5rem;
        padding: .6rem .75rem;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }
    .txn-table tbody tr:hover { background: #f8fafc; }
    .txn-table td {
        display: flex;
        flex-direction: column;
        padding: .3rem .35rem;
        border: none;
        font-size: .82rem;
    }
    .txn-table td::before {
        content: attr(data-label);
        font-size: .67rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        margin-bottom: 2px;
    }
    /* Row number: tiny, span full width at top */
    .txn-table td[data-label="#"] {
        grid-column: 1 / -1;
        flex-direction: row;
        align-items: center;
        gap: .5rem;
        border-bottom: 1px dashed #f1f5f9;
        padding-bottom: .4rem;
        margin-bottom: .2rem;
    }
    /* Guest + Actions span full width for breathing room */
    .txn-table td[data-label="Guest"] {
        grid-column: 1 / -1;
    }
    .txn-table td[data-label="Actions"] {
        grid-column: 1 / -1;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 5px;
        align-items: center;
        padding-top: .5rem;
        border-top: 1px dashed #f1f5f9;
        margin-top: .2rem;
    }
    .txn-table td[data-label="Actions"]::before { display: none; }
    /* Payment col: full width so progress bar looks good */
    .txn-table td[data-label="Payment"] {
        grid-column: 1 / -1;
    }
    .txn-table-wrap { border: none; background: transparent; box-shadow: none !important; }
    .txn-table-wrap > .table-responsive { overflow-x: visible; border-radius: 0; }
    .txn-table tbody tr.empty-row { display: table-row; }
    .txn-table tbody tr.empty-row td { display: table-cell; }
}

/* On very small screens (< 576px): stack to single column */
@media (max-width: 575.98px) {
    .txn-table tbody tr {
        grid-template-columns: 1fr;
    }
    .txn-table td[data-label="Payment"],
    .txn-table td[data-label="Guest"] {
        grid-column: 1;
    }
}

/* Payment Progress Bar */
.pay-progress-wrap { min-width: 0; width: 100%; }
.pay-progress-bar-bg {
    height: 6px; border-radius: 10px; background: #e2e8f0;
    overflow: hidden; margin-top: 4px;
}
.pay-progress-bar-fill {
    height: 100%; border-radius: 10px;
    background: linear-gradient(90deg, #10b981, #059669);
    transition: width 0.6s ease;
}
.pay-progress-bar-fill.partial { background: linear-gradient(90deg, #f59e0b, #d97706); }
.pay-progress-bar-fill.none    { background: #ef4444; }

/* Status Badges */
.txn-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: .3rem .7rem; border-radius: 8px;
    font-size: .75rem; font-weight: 700;
    letter-spacing: .03em;
    white-space: nowrap;
}
.txn-badge-reservation { background: #fef3c7; color: #92400e; }
.txn-badge-paid        { background: #d1fae5; color: #065f46; }
.txn-badge-checkin     { background: #dbeafe; color: #1e40af; }
.txn-badge-checkout    { background: #ede9fe; color: #5b21b6; }
.txn-badge-canceled    { background: #fee2e2; color: #991b1b; }
.txn-badge-expired     { background: #fce7f3; color: #9d174d; }

/* Customer Avatar Cell */
.txn-customer-cell { display: flex; align-items: center; gap: .65rem; }
.txn-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    object-fit: cover; flex-shrink: 0;
    border: 2px solid #e2e8f0;
}
.txn-avatar-fallback {
    width: 34px; height: 34px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 800;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff; flex-shrink: 0;
}
.txn-customer-name { font-weight: 600; font-size: .845rem; line-height: 1.2; }
.txn-customer-email { font-size: .72rem; color: #94a3b8; }

/* Days Countdown */
.days-pill {
    display: inline-flex; align-items: center; gap: 4px;
    padding: .18rem .6rem; border-radius: 20px;
    font-size: .75rem; font-weight: 700;
    white-space: nowrap;
}
.days-urgent  { background: #fee2e2; color: #b91c1c; }
.days-warning { background: #fef3c7; color: #b45309; }
.days-ok      { background: #d1fae5; color: #065f46; }
.days-past    { background: #f1f5f9; color: #94a3b8; }

/* Action buttons */
.txn-actions { display: flex; gap: 5px; align-items: center; flex-wrap: wrap; }
.txn-btn {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .82rem;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #475569;
    transition: all 0.15s ease;
    cursor: pointer;
    text-decoration: none;
    flex-shrink: 0;
}
.txn-btn:hover  { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.12); color: #1e40af; border-color: #93c5fd; }
.txn-btn.pay    { background: #f0fdf4; color: #059669; border-color: #6ee7b7; }
.txn-btn.pay:hover { background: #059669; color: #fff; border-color: #059669; }
.txn-btn.edit   { background: #eff6ff; color: #2563eb; border-color: #93c5fd; }
.txn-btn.edit:hover { background: #2563eb; color: #fff; border-color: #2563eb; }
.txn-btn.view   { background: #f8fafc; color: #475569; border-color: #cbd5e1; }
.txn-btn.view:hover { background: #475569; color: #fff; border-color: #475569; }
.txn-btn.cancel { background: #fffbeb; color: #d97706; border-color: #fcd34d; }
.txn-btn.cancel:hover { background: #d97706; color: #fff; border-color: #d97706; }
.txn-btn.del    { background: #fef2f2; color: #dc2626; border-color: #fca5a5; }
.txn-btn.del:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
.txn-btn.disabled { opacity: .45; pointer-events: none; }

/* Quick View Modal */
.txn-modal-header {
    background: linear-gradient(135deg, #1e293b, #0f172a);
    color: #fff;
    border-radius: 16px 16px 0 0;
    padding: 1.5rem 1.75rem;
}
.txn-modal-body { padding: 1.5rem 1.75rem; }
.txn-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
@media (max-width: 575.98px) {
    .txn-detail-grid { grid-template-columns: 1fr; }
    .txn-modal-header { padding: 1rem 1.25rem; }
    .txn-modal-body   { padding: 1rem 1.25rem; }
}
.txn-detail-item {
    background: #f8fafc;
    border-radius: 10px;
    padding: .7rem .9rem;
    border: 1px solid #f1f5f9;
}
.txn-detail-item .detail-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; font-weight: 700; }
.txn-detail-item .detail-value { font-size: .9rem; font-weight: 600; color: #1e293b; margin-top: 2px; }

/* Payment Timeline */
.pay-timeline { position: relative; padding-left: 1.5rem; }
.pay-timeline::before {
    content: '';
    position: absolute; left: 6px; top: 0; bottom: 0;
    width: 2px; background: #e2e8f0;
}
.pay-timeline-item { position: relative; margin-bottom: 1rem; }
.pay-timeline-item::before {
    content: '';
    position: absolute; left: -1.36rem; top: 4px;
    width: 10px; height: 10px; border-radius: 50%;
    background: #3b82f6; border: 2px solid #fff;
    box-shadow: 0 0 0 2px #3b82f6;
}
.pay-timeline-item:last-child { margin-bottom: 0; }
.pay-timeline-item .pay-tl-amount { font-weight: 700; color: #059669; font-size: .92rem; }
.pay-timeline-item .pay-tl-meta { font-size: .75rem; color: #94a3b8; margin-top: 1px; }
.pay-timeline-item .pay-tl-status { font-size: .72rem; }

/* Summary total row */
.txn-summary-bar {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: .65rem 1rem;
    font-size: .82rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.txn-summary-bar strong { color: #1e293b; }

/* Empty state */
.txn-empty {
    text-align: center; padding: 3rem 1rem; color: #94a3b8;
}
.txn-empty .empty-icon { font-size: 3rem; margin-bottom: .75rem; opacity: .5; }
.txn-empty p { font-size: .9rem; }

/* Urgency row highlight */
tr.row-urgent td { background: rgba(239,68,68,.035); }
tr.row-today td  { background: rgba(245,158,11,.035); }

/* Responsive tab-nav + header */
@media (max-width: 767.98px) {
    .txn-tab-nav { flex-wrap: wrap; }
    .txn-tab-nav .nav-link { font-size: .78rem; padding: .38rem .8rem; }
}
@media (max-width: 575.98px) {
    .txn-summary-bar { flex-direction: column; align-items: flex-start; gap: .4rem; }
}
</style>
@endsection

@section('content')

@php
    /* ── Aggregate Stats ──────────────────────────────── */
    $totalActive   = $transactions->total();
    $totalExpired  = is_countable($transactionsExpired) ? count($transactionsExpired) : $transactionsExpired->count();
    $totalRevenue  = 0;
    $totalOutstanding = 0;
    foreach ($transactions->getCollection() as $t) {
        $totalRevenue    += $t->getTotalPayment();
        $totalOutstanding += max(0, $t->getTotalPrice() - $t->getTotalPayment());
    }
    foreach ($transactionsExpired as $t) {
        $totalRevenue    += $t->getTotalPayment();
        $totalOutstanding += max(0, $t->getTotalPrice() - $t->getTotalPayment());
    }
    $today = \Carbon\Carbon::today();
@endphp

{{-- ── Alerts ───────────────────────────────────────────────── --}}
@if(session('success'))
    <div class="alert border-0 shadow-sm alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
         style="background:#d1fae5;color:#065f46;border-radius:12px;" role="alert">
        <i class="fas fa-check-circle fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('failed'))
    <div class="alert border-0 shadow-sm alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
         style="background:#fee2e2;color:#991b1b;border-radius:12px;" role="alert">
        <i class="fas fa-exclamation-circle fs-5"></i>
        <div>{{ session('failed') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="fw-bold mb-0" style="color:#1e293b;">
            <i class="fas fa-exchange-alt me-2" style="color:#3b82f6;"></i>Transaction Panel
        </h4>
        <p class="text-muted mb-0" style="font-size:.82rem;">Manage all bookings, payments, and guest activity</p>
    </div>
    <div class="d-flex gap-2">
        <span data-bs-toggle="tooltip" data-bs-placement="bottom" title="Payment History">
            <a href="{{ route('payment.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px;">
                <i class="fas fa-history me-1"></i>History
            </a>
        </span>
        <span data-bs-toggle="tooltip" data-bs-placement="bottom" title="Export transactions to CSV">
            <button onclick="exportCSV()" class="btn btn-sm btn-outline-success" style="border-radius:10px;">
                <i class="fas fa-file-csv me-1"></i>Export
            </button>
        </span>
        <span data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Room Reservation">
            <button type="button" class="btn btn-sm btn-primary shadow-sm" style="border-radius:10px;"
                    data-bs-toggle="modal" data-bs-target="#newReservationModal">
                <i class="fas fa-plus me-1"></i>New Booking
            </button>
        </span>
    </div>
</div>

{{-- ── KPI Stats ─────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="txn-kpi-card kpi-active shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="kpi-icon"><i class="fas fa-bed"></i></div>
                <div>
                    <div class="kpi-label">Active Guests</div>
                    <div class="kpi-value">{{ $totalActive }}</div>
                </div>
            </div>
            <div class="kpi-sub"><i class="fas fa-circle me-1" style="font-size:.5rem;"></i>Currently checked-in / reserved</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="txn-kpi-card kpi-expired shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="kpi-label">Expired</div>
                    <div class="kpi-value">{{ $totalExpired }}</div>
                </div>
            </div>
            <div class="kpi-sub"><i class="fas fa-circle me-1" style="font-size:.5rem;"></i>Past check-out date</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="txn-kpi-card kpi-revenue shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="kpi-icon"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="kpi-label">Revenue</div>
                    <div class="kpi-value" style="font-size:1.1rem;">{{ Helper::convertToRupiah($totalRevenue) }}</div>
                </div>
            </div>
            <div class="kpi-sub"><i class="fas fa-circle me-1" style="font-size:.5rem;"></i>Total payments received</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="txn-kpi-card kpi-debt shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <div class="kpi-label">Outstanding</div>
                    <div class="kpi-value" style="font-size:1.1rem;">{{ Helper::convertToRupiah($totalOutstanding) }}</div>
                </div>
            </div>
            <div class="kpi-sub"><i class="fas fa-circle me-1" style="font-size:.5rem;"></i>Unpaid balance</div>
        </div>
    </div>
</div>

{{-- ── Tab Nav + Filter ─────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <ul class="nav txn-tab-nav" id="txnTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#tab-active" type="button">
                    <i class="fas fa-user-check"></i> Active
                    <span class="tab-badge">{{ $totalActive }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#tab-expired" type="button">
                    <i class="fas fa-history"></i> Expired
                    <span class="tab-badge">{{ $totalExpired }}</span>
                </button>
            </li>
        </ul>

        @php
            $hasActiveFilters = request()->hasAny(['search','filter_status','filter_date_from','filter_date_to']);
        @endphp

        <button class="btn btn-outline-secondary btn-sm ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#txnFilterCollapse" aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}" aria-controls="txnFilterCollapse">
            <i class="fas fa-filter me-1"></i> Filter Options
            @if($hasActiveFilters)
                <span class="badge bg-primary ms-1">Active</span>
            @endif
        </button>
    </div>

    @if($hasActiveFilters)
        <a href="{{ route('transaction.index') }}" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-times me-1"></i> Clear Filters
        </a>
    @endif
</div>

{{-- Collapsible Filter Bar --}}
<div class="collapse {{ $hasActiveFilters ? 'show' : '' }} mb-3" id="txnFilterCollapse">
    <div class="card border-0 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0 !important;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('transaction.index') }}" id="searchForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Search ID or Customer</label>
                    <input class="form-control" type="search" placeholder="Search name or ID…"
                           name="search" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Status</label>
                    <select name="filter_status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="Reservation" {{ request('filter_status')=='Reservation' ? 'selected' : '' }}>Reservation</option>
                        <option value="Paid"        {{ request('filter_status')=='Paid'        ? 'selected' : '' }}>Paid</option>
                        <option value="Checked In"  {{ request('filter_status')=='Checked In'  ? 'selected' : '' }}>Checked In</option>
                        <option value="Checked Out" {{ request('filter_status')=='Checked Out' ? 'selected' : '' }}>Checked Out</option>
                        <option value="Canceled"    {{ request('filter_status')=='Canceled'    ? 'selected' : '' }}>Canceled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">From (Check-in)</label>
                    <input type="date" name="filter_date_from" class="form-control"
                           value="{{ request('filter_date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">To (Check-in)</label>
                    <input type="date" name="filter_date_to" class="form-control"
                           value="{{ request('filter_date_to') }}">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-4 btn-sm">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                    @if($hasActiveFilters)
                        <a href="{{ route('transaction.index') }}" class="btn btn-outline-secondary px-4 btn-sm ms-2">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     TAB CONTENT
══════════════════════════════════════════════════════════════ --}}
<div class="tab-content" id="txnTabContent">

    {{-- ─── TAB: Active Guests ────────────────────────────── --}}
    <div class="tab-pane fade show active" id="tab-active" role="tabpanel">
        <div class="txn-table-wrap shadow-sm">
            <div class="table-responsive">
                <table class="table txn-table" id="activeTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Booking ID</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Duration</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Countdown</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            @php
                                $totalPrice   = $transaction->getTotalPrice();
                                $totalPaid    = $transaction->getTotalPayment();
                                $debt         = $totalPrice - $totalPaid;
                                $pct          = $totalPrice > 0 ? min(100, round(($totalPaid / $totalPrice) * 100)) : 0;
                                $fillClass    = $pct == 100 ? '' : ($pct > 0 ? 'partial' : 'none');
                                $checkOut     = \Carbon\Carbon::parse($transaction->check_out);
                                $daysLeft     = (int) $today->diffInDays($checkOut, false);
                                $countClass   = $daysLeft < 0 ? 'days-past' : ($daysLeft == 0 ? 'days-urgent' : ($daysLeft <= 2 ? 'days-warning' : 'days-ok'));
                                $countLabel   = $daysLeft < 0 ? abs($daysLeft).'d ago' : ($daysLeft == 0 ? 'Today' : $daysLeft.'d left');
                                $rowClass     = $daysLeft == 0 ? 'row-today' : ($daysLeft < 0 ? 'row-urgent' : '');
                            @endphp
                            <tr class="{{ $rowClass }}" data-txn-id="{{ $transaction->id }}">
                                <td data-label="#" class="text-muted" style="font-size:.78rem;">
                                    {{ ($transactions->currentpage() - 1) * $transactions->perpage() + $loop->index + 1 }}
                                </td>
                                <td data-label="Booking ID">
                                    <span class="fw-bold" style="color:#3b82f6;font-family:monospace;font-size:.85rem;">#{{ $transaction->id }}</span>
                                </td>
                                <td data-label="Guest">
                                    <div class="txn-customer-cell">
                                        <div class="txn-avatar-fallback">{{ strtoupper(substr($transaction->customer->name, 0, 2)) }}</div>
                                        <div>
                                            <div class="txn-customer-name">{{ $transaction->customer->name }}</div>
                                            <div class="txn-customer-email">{{ $transaction->customer->job ?? 'Guest' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Room">
                                    <div class="fw-bold" style="font-size:.85rem;">Room {{ $transaction->room->number }}</div>
                                    <div style="font-size:.72rem;color:#94a3b8;">{{ $transaction->room->type->name ?? '-' }}</div>
                                </td>
                                <td data-label="Check-In">
                                    <div style="font-size:.84rem;">{{ Helper::dateFormat($transaction->check_in) }}</div>
                                </td>
                                <td data-label="Check-Out">
                                    <div style="font-size:.84rem;">{{ Helper::dateFormat($transaction->check_out) }}</div>
                                </td>
                                <td data-label="Duration">
                                    <span class="fw-600" style="font-size:.84rem;">{{ $transaction->getDateDifferenceWithPlural() }}</span>
                                </td>
                                <td data-label="Payment">
                                    <div class="pay-progress-wrap">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <span style="font-size:.78rem;color:#64748b;">{{ $pct }}%</span>
                                            @if($debt > 0)
                                                <span style="font-size:.7rem;color:#ef4444;">-{{ Helper::convertToRupiah($debt) }}</span>
                                            @else
                                                <span style="font-size:.7rem;color:#10b981;"><i class="fas fa-check"></i></span>
                                            @endif
                                        </div>
                                        <div class="pay-progress-bar-bg">
                                            <div class="pay-progress-bar-fill {{ $fillClass }}" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">
                                            {{ Helper::convertToRupiah($totalPaid) }} / {{ Helper::convertToRupiah($totalPrice) }}
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Status">
                                    @php
                                        $statusClass = match($transaction->status) {
                                            'Reservation' => 'txn-badge-reservation',
                                            'Paid'        => 'txn-badge-paid',
                                            'Checked In'  => 'txn-badge-checkin',
                                            'Checked Out' => 'txn-badge-checkout',
                                            'Canceled'    => 'txn-badge-canceled',
                                            default       => 'txn-badge-reservation',
                                        };
                                        $statusIcon = match($transaction->status) {
                                            'Reservation' => 'fa-calendar-check',
                                            'Paid'        => 'fa-check-circle',
                                            'Checked In'  => 'fa-sign-in-alt',
                                            'Checked Out' => 'fa-sign-out-alt',
                                            'Canceled'    => 'fa-times-circle',
                                            default       => 'fa-question',
                                        };
                                    @endphp
                                    <span class="txn-badge {{ $statusClass }}">
                                        <i class="fas {{ $statusIcon }}" style="font-size:.65rem;"></i>
                                        {{ $transaction->status }}
                                    </span>
                                </td>
                                <td data-label="Countdown">
                                    <span class="days-pill {{ $countClass }}">
                                        <i class="fas fa-{{ $daysLeft < 0 ? 'exclamation-circle' : 'hourglass-half' }}" style="font-size:.65rem;"></i>
                                        {{ $countLabel }}
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <div class="txn-actions">
                                        {{-- Quick View --}}
                                        <button type="button"
                                            class="txn-btn view"
                                            data-bs-toggle="modal" data-bs-target="#quickViewModal"
                                            data-txn="{{ json_encode([
                                                'id'          => $transaction->id,
                                                'customer'    => $transaction->customer->name,
                                                'phone'       => $transaction->customer->job ?? '-',
                                                'gender'      => $transaction->customer->gender ?? '-',
                                                'address'     => $transaction->customer->address ?? '-',
                                                'room'        => $transaction->room->number,
                                                'room_type'   => $transaction->room->type->name ?? '-',
                                                'room_price'  => Helper::convertToRupiah($transaction->room->price),
                                                'check_in'    => Helper::dateFormat($transaction->check_in),
                                                'check_out'   => Helper::dateFormat($transaction->check_out),
                                                'duration'    => $transaction->getDateDifferenceWithPlural(),
                                                'total_price' => Helper::convertToRupiah($totalPrice),
                                                'total_paid'  => Helper::convertToRupiah($totalPaid),
                                                'debt'        => $debt > 0 ? Helper::convertToRupiah($debt) : 'Fully Paid',
                                                'status'      => $transaction->status,
                                                'pct'         => $pct,
                                                'payments'    => $transaction->payment->map(fn($p) => [
                                                    'id'     => $p->id,
                                                    'amount' => Helper::convertToRupiah($p->price),
                                                    'status' => $p->status,
                                                    'date'   => Helper::dateFormatTimeNoYear($p->created_at),
                                                    'ref'    => $p->reference_number ?? '-',
                                                    'by'     => $p->user?->name ?? 'System',
                                                ]),
                                                'pay_url'     => route('transaction.payment.create', ['transaction' => $transaction->id]),
                                                'edit_url'    => auth()->user()->isSuperAdmin() ? route('transaction.edit', $transaction->id) : null,
                                                'cancel_url'  => ($transaction->status != 'Canceled') ? route('transaction.cancel', $transaction->id) : null,
                                                'delete_url'  => auth()->user()->isSuperAdmin() ? route('transaction.destroy', $transaction->id) : null,
                                                'can_pay'     => $debt > 0,
                                                'is_admin'    => auth()->user()->isSuperAdmin(),
                                            ]) }}"
                                            title="Quick View" data-bs-toggle2="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- Pay --}}
                                        <a class="txn-btn pay {{ $debt <= 0 ? 'disabled' : '' }}"
                                           href="{{ route('transaction.payment.create', ['transaction' => $transaction->id]) }}"
                                           title="Add Payment" data-bs-toggle="tooltip" data-bs-placement="top">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </a>

                                        @if(auth()->user()->isSuperAdmin())
                                            <a class="txn-btn edit"
                                               href="{{ route('transaction.edit', $transaction->id) }}"
                                               title="Edit Booking" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($transaction->status != 'Canceled')
                                                <button type="button" class="txn-btn cancel"
                                                    onclick="confirmCancel('{{ $transaction->id }}','{{ route('transaction.cancel',$transaction->id) }}')"
                                                    title="Cancel Booking" data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="txn-btn del"
                                                onclick="confirmDelete('{{ $transaction->id }}','{{ route('transaction.destroy',$transaction->id) }}')"
                                                title="Delete Booking" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="11">
                                    <div class="txn-empty">
                                        <div class="empty-icon"><i class="fas fa-bed"></i></div>
                                        <p class="fw-bold mb-1">No active transactions found</p>
                                        <p>Try adjusting your filters or add a new booking.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->count())
            <div class="txn-summary-bar">
                <span>Showing <strong>{{ $transactions->firstItem() }}–{{ $transactions->lastItem() }}</strong> of <strong>{{ $transactions->total() }}</strong> active bookings</span>
                <span class="ms-auto">{{ $transactions->onEachSide(2)->links('template.paginationlinks') }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ─── TAB: Expired ──────────────────────────────────── --}}
    <div class="tab-pane fade" id="tab-expired" role="tabpanel">
        <div class="txn-table-wrap shadow-sm">
            <div class="table-responsive">
                <table class="table txn-table" id="expiredTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Booking ID</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Duration</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Overdue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactionsExpired as $transaction)
                            @php
                                $totalPrice  = $transaction->getTotalPrice();
                                $totalPaid   = $transaction->getTotalPayment();
                                $debt        = $totalPrice - $totalPaid;
                                $pct         = $totalPrice > 0 ? min(100, round(($totalPaid / $totalPrice) * 100)) : 0;
                                $fillClass   = $pct == 100 ? '' : ($pct > 0 ? 'partial' : 'none');
                                $checkOut    = \Carbon\Carbon::parse($transaction->check_out);
                                $daysAgo     = (int) $today->diffInDays($checkOut);
                            @endphp
                            <tr data-txn-id="{{ $transaction->id }}">
                                <td data-label="#" class="text-muted" style="font-size:.78rem;">{{ $loop->index + 1 }}</td>
                                <td data-label="Booking ID">
                                    <span class="fw-bold" style="color:#94a3b8;font-family:monospace;font-size:.85rem;">#{{ $transaction->id }}</span>
                                </td>
                                <td data-label="Guest">
                                    <div class="txn-customer-cell">
                                        <div class="txn-avatar-fallback" style="background:linear-gradient(135deg,#94a3b8,#64748b);">
                                            {{ strtoupper(substr($transaction->customer->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="txn-customer-name">{{ $transaction->customer->name }}</div>
                                            <div class="txn-customer-email">{{ $transaction->customer->job ?? 'Guest' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Room">
                                    <div class="fw-bold" style="font-size:.85rem;">Room {{ $transaction->room->number }}</div>
                                    <div style="font-size:.72rem;color:#94a3b8;">{{ $transaction->room->type->name ?? '-' }}</div>
                                </td>
                                <td data-label="Check-In"><div style="font-size:.84rem;">{{ Helper::dateFormat($transaction->check_in) }}</div></td>
                                <td data-label="Check-Out"><div style="font-size:.84rem;">{{ Helper::dateFormat($transaction->check_out) }}</div></td>
                                <td data-label="Duration"><span style="font-size:.84rem;">{{ $transaction->getDateDifferenceWithPlural() }}</span></td>
                                <td data-label="Payment">
                                    <div class="pay-progress-wrap">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <span style="font-size:.78rem;color:#64748b;">{{ $pct }}%</span>
                                            @if($debt > 0)
                                                <span style="font-size:.7rem;color:#ef4444;">-{{ Helper::convertToRupiah($debt) }}</span>
                                            @else
                                                <span style="font-size:.7rem;color:#10b981;"><i class="fas fa-check"></i></span>
                                            @endif
                                        </div>
                                        <div class="pay-progress-bar-bg">
                                            <div class="pay-progress-bar-fill {{ $fillClass }}" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">
                                            {{ Helper::convertToRupiah($totalPaid) }} / {{ Helper::convertToRupiah($totalPrice) }}
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Status">
                                    @php
                                        $statusClass = match($transaction->status) {
                                            'Reservation' => 'txn-badge-reservation',
                                            'Paid'        => 'txn-badge-paid',
                                            'Checked In'  => 'txn-badge-checkin',
                                            'Checked Out' => 'txn-badge-checkout',
                                            'Canceled'    => 'txn-badge-canceled',
                                            default       => 'txn-badge-expired',
                                        };
                                        $statusIcon = match($transaction->status) {
                                            'Reservation' => 'fa-calendar-check',
                                            'Paid'        => 'fa-check-circle',
                                            'Checked In'  => 'fa-sign-in-alt',
                                            'Checked Out' => 'fa-sign-out-alt',
                                            'Canceled'    => 'fa-times-circle',
                                            default       => 'fa-question',
                                        };
                                    @endphp
                                    <span class="txn-badge {{ $statusClass }}">
                                        <i class="fas {{ $statusIcon }}" style="font-size:.65rem;"></i>
                                        {{ $transaction->status }}
                                    </span>
                                </td>
                                <td data-label="Overdue">
                                    <span class="days-pill days-past">
                                        <i class="fas fa-exclamation-circle" style="font-size:.65rem;"></i>
                                        {{ $daysAgo }}d ago
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <div class="txn-actions">
                                        <button type="button"
                                            class="txn-btn view"
                                            data-bs-toggle="modal" data-bs-target="#quickViewModal"
                                            data-txn="{{ json_encode([
                                                'id'          => $transaction->id,
                                                'customer'    => $transaction->customer->name,
                                                'phone'       => $transaction->customer->phone ?? '-',
                                                'gender'      => $transaction->customer->gender ?? '-',
                                                'address'     => $transaction->customer->address ?? '-',
                                                'room'        => $transaction->room->number,
                                                'room_type'   => $transaction->room->type->name ?? '-',
                                                'room_price'  => Helper::convertToRupiah($transaction->room->price),
                                                'check_in'    => Helper::dateFormat($transaction->check_in),
                                                'check_out'   => Helper::dateFormat($transaction->check_out),
                                                'duration'    => $transaction->getDateDifferenceWithPlural(),
                                                'total_price' => Helper::convertToRupiah($totalPrice),
                                                'total_paid'  => Helper::convertToRupiah($totalPaid),
                                                'debt'        => $debt > 0 ? Helper::convertToRupiah($debt) : 'Fully Paid',
                                                'status'      => $transaction->status,
                                                'pct'         => $pct,
                                                'payments'    => $transaction->payment->map(fn($p) => [
                                                    'id'     => $p->id,
                                                    'amount' => Helper::convertToRupiah($p->price),
                                                    'status' => $p->status,
                                                    'date'   => Helper::dateFormatTimeNoYear($p->created_at),
                                                    'ref'    => $p->reference_number ?? '-',
                                                    'by'     => $p->user?->name ?? 'System',
                                                ]),
                                                'pay_url'    => route('transaction.payment.create', ['transaction' => $transaction->id]),
                                                'edit_url'   => auth()->user()->isSuperAdmin() ? route('transaction.edit', $transaction->id) : null,
                                                'cancel_url' => ($transaction->status != 'Canceled') ? route('transaction.cancel', $transaction->id) : null,
                                                'delete_url' => auth()->user()->isSuperAdmin() ? route('transaction.destroy', $transaction->id) : null,
                                                'can_pay'    => $debt > 0,
                                                'is_admin'   => auth()->user()->isSuperAdmin(),
                                            ]) }}"
                                            title="Quick View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a class="txn-btn pay {{ $debt <= 0 ? 'disabled' : '' }}"
                                           href="{{ route('transaction.payment.create', ['transaction' => $transaction->id]) }}"
                                           title="Add Payment">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </a>
                                        @if(auth()->user()->isSuperAdmin())
                                            <a class="txn-btn edit"
                                               href="{{ route('transaction.edit', $transaction->id) }}"
                                               title="Edit Booking">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($transaction->status != 'Canceled')
                                                <button type="button" class="txn-btn cancel"
                                                    onclick="confirmCancel('{{ $transaction->id }}','{{ route('transaction.cancel',$transaction->id) }}')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="txn-btn del"
                                                onclick="confirmDelete('{{ $transaction->id }}','{{ route('transaction.destroy',$transaction->id) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="11">
                                    <div class="txn-empty">
                                        <div class="empty-icon"><i class="fas fa-check-double"></i></div>
                                        <p class="fw-bold mb-1">No expired transactions</p>
                                        <p>All bookings are within their stay period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($transactionsExpired))
            <div class="txn-summary-bar">
                <span>Showing <strong>{{ count($transactionsExpired) }}</strong> expired bookings</span>
            </div>
            @endif
        </div>
    </div>

</div>{{-- end tab-content --}}


{{-- ══════════════════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════════════════ --}}

{{-- ─── Quick View Modal ──────────────────────────────────── --}}
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="txn-modal-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0" id="quickViewLabel">
                            <i class="fas fa-receipt me-2" style="color:#93c5fd;"></i>
                            Booking Details — <span id="qv-id"></span>
                        </h5>
                        <p class="mb-0 mt-1" style="font-size:.8rem;opacity:.65;">Full transaction information & payment history</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="txn-modal-body">

                {{-- Status + Actions row --}}
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <span id="qv-status-badge" class="txn-badge"></span>
                    <div class="d-flex gap-2" id="qv-action-btns"></div>
                </div>

                <div class="row g-4">
                    {{-- Left: Details grid --}}
                    <div class="col-md-6">
                        <div class="fw-bold mb-2" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;">Guest Information</div>
                        <div class="txn-detail-grid mb-4">
                            <div class="txn-detail-item">
                                <div class="detail-label">Name</div>
                                <div class="detail-value" id="qv-customer"></div>
                            </div>
                            <div class="txn-detail-item">
                                <div class="detail-label">Occupation</div>
                                <div class="detail-value" id="qv-phone"></div>
                            </div>
                            <div class="txn-detail-item">
                                <div class="detail-label">Gender</div>
                                <div class="detail-value" id="qv-gender"></div>
                            </div>
                            <div class="txn-detail-item" style="grid-column:span 2;">
                                <div class="detail-label">Address</div>
                                <div class="detail-value" id="qv-address" style="font-size:.82rem;"></div>
                            </div>
                        </div>

                        <div class="fw-bold mb-2" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;">Room & Stay</div>
                        <div class="txn-detail-grid">
                            <div class="txn-detail-item">
                                <div class="detail-label">Room No.</div>
                                <div class="detail-value" id="qv-room"></div>
                            </div>
                            <div class="txn-detail-item">
                                <div class="detail-label">Room Type</div>
                                <div class="detail-value" id="qv-room-type"></div>
                            </div>
                            <div class="txn-detail-item">
                                <div class="detail-label">Price / Night</div>
                                <div class="detail-value" id="qv-room-price"></div>
                            </div>
                            <div class="txn-detail-item">
                                <div class="detail-label">Duration</div>
                                <div class="detail-value" id="qv-duration"></div>
                            </div>
                            <div class="txn-detail-item">
                                <div class="detail-label">Check-In</div>
                                <div class="detail-value" id="qv-checkin"></div>
                            </div>
                            <div class="txn-detail-item">
                                <div class="detail-label">Check-Out</div>
                                <div class="detail-value" id="qv-checkout"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Payment summary + history --}}
                    <div class="col-md-6">
                        <div class="fw-bold mb-2" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;">Payment Summary</div>
                        <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #f1f5f9;">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:.83rem;color:#64748b;">Total Charge</span>
                                <span class="fw-bold" style="font-size:.9rem;" id="qv-total-price"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:.83rem;color:#64748b;">Paid</span>
                                <span class="fw-bold" style="font-size:.9rem;color:#059669;" id="qv-total-paid"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:.83rem;color:#64748b;">Outstanding</span>
                                <span class="fw-bold" style="font-size:.9rem;" id="qv-debt"></span>
                            </div>
                            <div class="pay-progress-bar-bg" style="height:8px;">
                                <div class="pay-progress-bar-fill" id="qv-progress-fill" style="width:0%;height:100%;"></div>
                            </div>
                            <div class="text-end mt-1" style="font-size:.72rem;color:#94a3b8;" id="qv-pct-label"></div>
                        </div>

                        <div class="fw-bold mb-2" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;">Payment History</div>
                        <div id="qv-payment-timeline" class="pay-timeline"></div>
                        <div id="qv-no-payments" class="text-center py-3" style="display:none;color:#94a3b8;font-size:.83rem;">
                            <i class="fas fa-receipt me-1"></i>No payments recorded yet.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── New Booking Modal ──────────────────────────────────── --}}
<div class="modal fade" id="newReservationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="newReservationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0" style="background:linear-gradient(135deg,#1e293b,#0f172a);color:#fff;padding:1.5rem 1.75rem;">
                <div>
                    <h5 class="fw-bold mb-0" id="newReservationLabel">
                        <i class="fas fa-plus-circle me-2" style="color:#93c5fd;"></i>New Reservation
                    </h5>
                    <p class="mb-0 mt-1" style="font-size:.8rem;opacity:.65;">Choose how to proceed with the new booking</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-3" style="font-size:.88rem;">Does the guest already have an account in the system?</p>
                <div class="d-grid gap-3">
                    <a href="{{ route('transaction.reservation.pickFromCustomer') }}"
                       class="btn d-flex align-items-center gap-3 p-3 text-start"
                       style="background:#f0fdf4;border:1.5px solid #6ee7b7;border-radius:12px;color:#065f46;">
                        <span style="width:42px;height:42px;background:#d1fae5;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-user-check" style="color:#059669;font-size:1.1rem;"></i>
                        </span>
                        <div>
                            <div class="fw-bold">Yes — Use Existing Account</div>
                            <div style="font-size:.8rem;opacity:.75;">Search and pick a registered guest</div>
                        </div>
                    </a>
                    <a href="{{ route('transaction.reservation.createIdentity') }}"
                       class="btn d-flex align-items-center gap-3 p-3 text-start"
                       style="background:#eff6ff;border:1.5px solid #93c5fd;border-radius:12px;color:#1e40af;">
                        <span style="width:42px;height:42px;background:#dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-user-plus" style="color:#2563eb;font-size:1.1rem;"></i>
                        </span>
                        <div>
                            <div class="fw-bold">No — Create New Account</div>
                            <div style="font-size:.8rem;opacity:.75;">Register a new guest and proceed</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Confirm Cancel Modal ───────────────────────────────── --}}
<div class="modal fade" id="confirmCancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-body p-4 text-center">
                <div class="mb-3" style="width:64px;height:64px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i class="fas fa-ban" style="font-size:1.6rem;color:#d97706;"></i>
                </div>
                <h5 class="fw-bold mb-1">Cancel Booking?</h5>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    Are you sure you want to cancel booking <strong id="cancel-booking-id"></strong>?
                    This action can be reversed by editing the booking.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" style="border-radius:10px;" data-bs-dismiss="modal">Keep Booking</button>
                <form id="cancelForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning px-4 fw-bold" style="border-radius:10px;">
                        <i class="fas fa-ban me-1"></i>Yes, Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ─── Confirm Delete Modal ───────────────────────────────── --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-body p-4 text-center">
                <div class="mb-3" style="width:64px;height:64px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i class="fas fa-trash" style="font-size:1.6rem;color:#dc2626;"></i>
                </div>
                <h5 class="fw-bold mb-1">Permanently Delete?</h5>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    Booking <strong id="delete-booking-id"></strong> and all its payment records will be
                    <span class="text-danger fw-bold">permanently deleted</span>. This cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" style="border-radius:10px;" data-bs-dismiss="modal">Keep It</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 fw-bold" style="border-radius:10px;">
                        <i class="fas fa-trash me-1"></i>Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')
<script>
/* ── Confirmation Helpers ──────────────────────────────── */
function confirmCancel(id, url) {
    document.getElementById('cancel-booking-id').textContent = '#' + id;
    document.getElementById('cancelForm').action = url;
    new bootstrap.Modal(document.getElementById('confirmCancelModal')).show();
}

function confirmDelete(id, url) {
    document.getElementById('delete-booking-id').textContent = '#' + id;
    document.getElementById('deleteForm').action = url;
    new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
}

/* ── Quick View Modal ────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const qvModal = document.getElementById('quickViewModal');

    qvModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn) return;

        let txn;
        try { txn = JSON.parse(btn.getAttribute('data-txn')); }
        catch(e) { console.error('Error parsing txn data', e); return; }

        // Basic fields
        document.getElementById('qv-id').textContent         = '#' + txn.id;
        document.getElementById('qv-customer').textContent   = txn.customer;
        document.getElementById('qv-phone').textContent      = txn.phone;
        document.getElementById('qv-gender').textContent     = txn.gender;
        document.getElementById('qv-address').textContent    = txn.address;
        document.getElementById('qv-room').textContent       = 'Room ' + txn.room;
        document.getElementById('qv-room-type').textContent  = txn.room_type;
        document.getElementById('qv-room-price').textContent = txn.room_price;
        document.getElementById('qv-duration').textContent   = txn.duration;
        document.getElementById('qv-checkin').textContent    = txn.check_in;
        document.getElementById('qv-checkout').textContent   = txn.check_out;
        document.getElementById('qv-total-price').textContent = txn.total_price;
        document.getElementById('qv-total-paid').textContent  = txn.total_paid;
        document.getElementById('qv-debt').textContent        = txn.debt;
        document.getElementById('qv-pct-label').textContent   = txn.pct + '% paid';

        // Status badge
        const statusBadge = document.getElementById('qv-status-badge');
        const statusMap = {
            'Reservation': ['txn-badge-reservation','fa-calendar-check'],
            'Paid':        ['txn-badge-paid',       'fa-check-circle'],
            'Checked In':  ['txn-badge-checkin',    'fa-sign-in-alt'],
            'Checked Out': ['txn-badge-checkout',   'fa-sign-out-alt'],
            'Canceled':    ['txn-badge-canceled',   'fa-times-circle'],
        };
        const [cls, icon] = statusMap[txn.status] || ['txn-badge-expired','fa-question'];
        statusBadge.className = 'txn-badge ' + cls;
        statusBadge.innerHTML = `<i class="fas ${icon}" style="font-size:.65rem;"></i> ${txn.status}`;

        // Progress bar
        const fill = document.getElementById('qv-progress-fill');
        fill.style.width = txn.pct + '%';
        fill.className = 'pay-progress-bar-fill' + (txn.pct === 100 ? '' : (txn.pct > 0 ? ' partial' : ' none'));

        // Debt color
        const debtEl = document.getElementById('qv-debt');
        debtEl.style.color = txn.debt === 'Fully Paid' ? '#059669' : '#ef4444';

        // Action buttons
        const actBtns = document.getElementById('qv-action-btns');
        actBtns.innerHTML = '';

        if (txn.can_pay) {
            actBtns.innerHTML += `<a href="${txn.pay_url}" class="btn btn-success btn-sm fw-bold" style="border-radius:9px;">
                <i class="fas fa-money-bill-wave me-1"></i>Add Payment
            </a>`;
        }
        if (txn.edit_url) {
            actBtns.innerHTML += `<a href="${txn.edit_url}" class="btn btn-primary btn-sm fw-bold" style="border-radius:9px;">
                <i class="fas fa-edit me-1"></i>Edit
            </a>`;
        }
        if (txn.cancel_url) {
            actBtns.innerHTML += `<button type="button" class="btn btn-warning btn-sm fw-bold text-dark" style="border-radius:9px;"
                onclick="bootstrap.Modal.getInstance(document.getElementById('quickViewModal')).hide(); setTimeout(()=>confirmCancel('${txn.id}','${txn.cancel_url}'),300);">
                <i class="fas fa-ban me-1"></i>Cancel
            </button>`;
        }
        if (txn.delete_url) {
            actBtns.innerHTML += `<button type="button" class="btn btn-danger btn-sm fw-bold" style="border-radius:9px;"
                onclick="bootstrap.Modal.getInstance(document.getElementById('quickViewModal')).hide(); setTimeout(()=>confirmDelete('${txn.id}','${txn.delete_url}'),300);">
                <i class="fas fa-trash me-1"></i>Delete
            </button>`;
        }

        // Payment timeline
        const timeline = document.getElementById('qv-payment-timeline');
        const noPayments = document.getElementById('qv-no-payments');
        timeline.innerHTML = '';

        if (txn.payments && txn.payments.length > 0) {
            noPayments.style.display = 'none';
            timeline.style.display = '';
            txn.payments.forEach(p => {
                const statusColors = { 'Pending': '#f59e0b', 'Payment': '#059669', 'Down Payment': '#3b82f6' };
                const statusColor  = statusColors[p.status] || '#64748b';
                timeline.innerHTML += `
                    <div class="pay-timeline-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pay-tl-amount">${p.amount}</div>
                            <span class="pay-tl-status badge" style="background:${statusColor}20;color:${statusColor};font-size:.68rem;padding:.2rem .55rem;border-radius:6px;">${p.status}</span>
                        </div>
                        <div class="pay-tl-meta">
                            <i class="fas fa-calendar-alt me-1"></i>${p.date}
                            &nbsp;·&nbsp;
                            <i class="fas fa-user me-1"></i>${p.by}
                            ${p.ref !== '-' ? `&nbsp;·&nbsp;<i class="fas fa-tag me-1"></i><code style="font-size:.7rem;">${p.ref}</code>` : ''}
                        </div>
                    </div>`;
            });
        } else {
            noPayments.style.display = '';
            timeline.style.display = 'none';
        }
    });
});

/* ── CSV Export ──────────────────────────────────────────── */
function exportCSV() {
    const tables = document.querySelectorAll('.txn-table');
    let csv = 'No,Booking ID,Guest,Room,Check-In,Check-Out,Duration,Paid %,Status\n';

    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row, i) => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 9) return;

            // Extract text, stripping extra whitespace
            const clean = el => el ? el.innerText.trim().replace(/\n+/g,' ').replace(/,/g,'') : '';

            csv += [
                i + 1,
                clean(cells[1]),
                clean(cells[2]?.querySelector('.txn-customer-name') || cells[2]),
                clean(cells[3]?.querySelector('.fw-bold') || cells[3]),
                clean(cells[4]),
                clean(cells[5]),
                clean(cells[6]),
                clean(cells[7]?.querySelector('span') || cells[7]),
                clean(cells[8]?.querySelector('.txn-badge') || cells[8]),
            ].join(',') + '\n';
        });
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'transactions_' + new Date().toISOString().slice(0,10) + '.csv';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endsection
