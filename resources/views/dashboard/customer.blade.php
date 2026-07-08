@extends('template.master')
@section('title', 'My Dashboard')

@section('head')
    <style>
        /* ─── CSS Custom Properties ──────────────────────────────── */
        :root {
            --cd-primary: #4f46e5;
            --cd-primary-l: #6366f1;
            --cd-success: #059669;
            --cd-warning: #d97706;
            --cd-danger: #dc2626;
            --cd-muted: #6b7280;
            --cd-border: #e5e7eb;
            --cd-surface: #ffffff;
            --cd-bg: #f3f4f6;
            --cd-radius: 16px;
            --cd-shadow: 0 4px 24px rgba(79, 70, 229, .08);
            --cd-transition: .22s cubic-bezier(.4, 0, .2, 1);
        }

        /* ─── Page wrapper ──────────────────────────────────────── */
        .cd-wrap {
            background: var(--cd-bg);
            min-height: 100vh;
            padding: 1.25rem 0 4rem;
            margin: -1rem;
        }

        /* ─── Hero / greeting ───────────────────────────────────── */
        .cd-hero {
            background: linear-gradient(135deg, var(--cd-primary) 0%, #7c3aed 100%);
            border-radius: var(--cd-radius);
            padding: 2rem 2rem 3.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: -2rem;
        }

        .cd-hero::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, .08);
            border-radius: 50%;
        }

        .cd-hero::after {
            content: '';
            position: absolute;
            bottom: -60px;
            right: 80px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, .05);
            border-radius: 50%;
        }

        .cd-hero-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, .4);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .25);
            flex-shrink: 0;
        }

        .cd-hero h1 {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -.5px;
            margin: 0;
        }

        .cd-hero p {
            font-size: .9rem;
            opacity: .85;
            margin: .25rem 0 0;
        }

        .cd-hero-date {
            font-size: .8rem;
            opacity: .7;
            font-weight: 500;
        }

        /* ─── Stats cards ───────────────────────────────────────── */
        .cd-stats-row {
            position: relative;
            z-index: 10;
        }

        .cd-stat {
            background: var(--cd-surface);
            border-radius: var(--cd-radius);
            box-shadow: var(--cd-shadow);
            padding: 1rem 1.1rem;
            display: flex;
            align-items: center;
            gap: .85rem;
            transition: transform var(--cd-transition), box-shadow var(--cd-transition);
            height: 100%;
            border: 1px solid var(--cd-border);
            min-width: 0;
        }

        .cd-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 32px rgba(79, 70, 229, .13);
        }

        .cd-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .cd-stat-icon.indigo {
            background: #ede9fe;
            color: var(--cd-primary);
        }

        .cd-stat-icon.green {
            background: #d1fae5;
            color: var(--cd-success);
        }

        .cd-stat-icon.amber {
            background: #fef3c7;
            color: var(--cd-warning);
        }

        .cd-stat-icon.red {
            background: #fee2e2;
            color: var(--cd-danger);
        }

        .cd-stat-num {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1.1;
            color: #111827;
            word-break: break-all;
        }

        .cd-stat-lbl {
            font-size: .75rem;
            color: var(--cd-muted);
            font-weight: 500;
            margin-top: 2px;
            line-height: 1.3;
        }

        /* ─── Generic card shell ────────────────────────────────── */
        .cd-card {
            background: var(--cd-surface);
            border-radius: var(--cd-radius);
            box-shadow: var(--cd-shadow);
            border: 1px solid var(--cd-border);
            overflow: hidden;
        }

        .cd-card-header {
            padding: .9rem 1.25rem;
            border-bottom: 1px solid var(--cd-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .cd-card-header h5 {
            font-size: .95rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .cd-card-body {
            padding: 1rem 1.25rem;
        }

        /* ─── Upcoming booking banner ───────────────────────────── */
        .cd-upcoming {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            border-radius: var(--cd-radius);
            padding: 1.4rem 1.75rem;
            position: relative;
            overflow: hidden;
        }

        .cd-upcoming::before {
            content: '\f236';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 1.5rem;
            bottom: .5rem;
            font-size: 5rem;
            color: rgba(59, 130, 246, .1);
            pointer-events: none;
        }

        .cd-upcoming-label {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #2563eb;
            font-weight: 700;
        }

        .cd-upcoming-room {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e3a8a;
        }

        .cd-upcoming-meta {
            font-size: .85rem;
            color: #3b82f6;
        }

        .cd-upcoming-badge {
            display: inline-block;
            background: #2563eb;
            color: #fff;
            font-size: .72rem;
            font-weight: 700;
            padding: .3rem .75rem;
            border-radius: 999px;
        }

        /* ─── Steps guide ───────────────────────────────────────── */
        .cd-step {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .75rem .75rem;
            border-radius: 10px;
            transition: background var(--cd-transition);
        }

        .cd-step:hover {
            background: #f9fafb;
        }

        .cd-step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--cd-primary);
            color: #fff;
            font-size: .75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .cd-step-title {
            font-weight: 700;
            font-size: .82rem;
            color: #111827;
        }

        .cd-step-desc {
            font-size: .75rem;
            color: var(--cd-muted);
            margin-top: 1px;
        }

        /* ─── Booking cards ─────────────────────────────────────── */
        .cd-booking-card {
            border: 1px solid var(--cd-border);
            border-radius: 12px;
            padding: 1rem 1rem;
            transition: box-shadow var(--cd-transition), transform var(--cd-transition);
            background: #fff;
            position: relative;
        }

        .cd-booking-card:hover {
            box-shadow: 0 6px 24px rgba(79, 70, 229, .1);
            transform: translateY(-2px);
        }

        .cd-booking-card .room-img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 10px;
            flex-shrink: 0;
            background: #e5e7eb;
        }

        .cd-booking-card .booking-id {
            font-size: .72rem;
            color: var(--cd-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .cd-booking-card .booking-room {
            font-size: .95rem;
            font-weight: 700;
            color: #111827;
        }

        .cd-booking-card .booking-type {
            font-size: .78rem;
            color: var(--cd-muted);
        }

        .cd-progress {
            height: 6px;
            border-radius: 999px;
            background: #f3f4f6;
            overflow: hidden;
        }

        .cd-progress-bar {
            height: 100%;
            border-radius: 999px;
            transition: width .5s ease;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .6rem;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
        }

        .status-reservation {
            background: #fef3c7;
            color: #92400e;
        }

        .status-done {
            background: #d1fae5;
            color: #065f46;
        }

        .status-canceled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-active {
            background: #dbeafe;
            color: #1e40af;
        }

        /* ─── Filter tabs ───────────────────────────────────────── */
        .cd-filter-tab-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            max-width: 100%;
            flex-shrink: 0;
        }

        .cd-filter-tab-wrap::-webkit-scrollbar {
            display: none;
        }

        .cd-filter-tab {
            display: inline-flex;
            gap: .3rem;
            padding: .25rem .3rem;
            background: #f3f4f6;
            border-radius: 999px;
            white-space: nowrap;
        }

        .cd-filter-tab button {
            border: none;
            background: transparent;
            padding: .3rem .75rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            color: var(--cd-muted);
            cursor: pointer;
            transition: all var(--cd-transition);
            white-space: nowrap;
        }

        .cd-filter-tab button.active,
        .cd-filter-tab button:hover {
            background: #fff;
            color: var(--cd-primary);
            box-shadow: 0 2px 8px rgba(79, 70, 229, .12);
        }

        /* ─── Timeline / payment history ────────────────────────── */
        .cd-timeline {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .cd-timeline li {
            display: flex;
            gap: .75rem;
            align-items: flex-start;
            padding: .75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .cd-timeline li:last-child {
            border-bottom: none;
        }

        .cd-tl-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 5px;
        }

        .cd-tl-dot.paid {
            background: var(--cd-success);
        }

        .cd-tl-dot.pending {
            background: var(--cd-warning);
        }

        .cd-tl-dot.reject {
            background: var(--cd-danger);
        }

        /* ─── Quick-action buttons ──────────────────────────────── */
        .cd-qa-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            padding: 1rem .5rem;
            border-radius: 14px;
            border: 1.5px solid var(--cd-border);
            text-align: center;
            color: #374151;
            font-weight: 600;
            font-size: .78rem;
            transition: all var(--cd-transition);
            cursor: pointer;
            text-decoration: none;
            background: #fff;
            width: 100%;
        }

        .cd-qa-btn i {
            font-size: 1.3rem;
        }

        .cd-qa-btn:hover {
            border-color: var(--cd-primary);
            color: var(--cd-primary);
            background: #ede9fe;
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, .12);
            text-decoration: none;
        }

        /* ─── Payment account chips ─────────────────────────────── */
        .cd-bank-chip {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 1px solid var(--cd-border);
            border-radius: 12px;
            padding: .9rem 1.1rem;
            cursor: pointer;
            transition: all var(--cd-transition);
            user-select: all;
        }

        .cd-bank-chip:hover {
            border-color: var(--cd-primary);
            background: #ede9fe;
        }

        .cd-bank-chip .bank-name {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--cd-muted);
            font-weight: 700;
        }

        .cd-bank-chip .acc-num {
            font-size: .95rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #111827;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .cd-bank-chip .acc-holder {
            font-size: .75rem;
            color: var(--cd-muted);
        }

        /* ─── Modal improvements ────────────────────────────────── */
        .cd-modal .modal-content {
            border-radius: var(--cd-radius);
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .15);
        }

        .cd-modal .modal-header {
            background: linear-gradient(135deg, var(--cd-primary), #7c3aed);
            color: #fff;
            border-radius: var(--cd-radius) var(--cd-radius) 0 0;
            border: none;
        }

        .cd-modal .modal-header .btn-close {
            filter: invert(1);
        }

        .cd-modal .modal-footer {
            border-top: 1px solid var(--cd-border);
        }

        .cd-modal .form-control,
        .cd-modal .form-select {
            border-radius: 10px;
            border-color: var(--cd-border);
            transition: border-color var(--cd-transition), box-shadow var(--cd-transition);
        }

        .cd-modal .form-control:focus,
        .cd-modal .form-select:focus {
            border-color: var(--cd-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .15);
        }

        .cd-drop-zone {
            border: 2px dashed var(--cd-border);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all var(--cd-transition);
            cursor: pointer;
            position: relative;
        }

        .cd-drop-zone:hover,
        .cd-drop-zone.drag-over {
            border-color: var(--cd-primary);
            background: #ede9fe;
        }

        .cd-drop-zone input[type=file] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        /* ─── Alerts ────────────────────────────────────────────── */
        .cd-alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border-left: 4px solid #10b981;
            border-radius: 10px;
            padding: .9rem 1.25rem;
        }

        .cd-alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border-left: 4px solid #ef4444;
            border-radius: 10px;
            padding: .9rem 1.25rem;
        }

        /* ─── Empty state ───────────────────────────────────────── */
        .cd-empty {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--cd-muted);
        }

        .cd-empty-icon {
            font-size: 3rem;
            opacity: .25;
            margin-bottom: 1rem;
        }

        .cd-empty-title {
            font-size: 1rem;
            font-weight: 700;
            color: #374151;
        }

        .cd-empty-sub {
            font-size: .85rem;
            margin-top: .3rem;
        }

        /* ────────────────────────────────────────────────────────── */
        /* ─── Mobile-first responsive overrides                  ─── */
        /* ────────────────────────────────────────────────────────── */

        /* Extra-small – phones < 480 px */
        @media (max-width: 479px) {
            .cd-wrap {
                padding: .75rem 0 3rem;
            }

            /* Hero */
            .cd-hero {
                padding: 1.25rem 1rem 2.75rem;
                border-radius: 12px;
                margin-bottom: -1.5rem;
            }

            .cd-hero h1 {
                font-size: 1.15rem;
            }

            .cd-hero p {
                font-size: .8rem;
            }

            .cd-hero-avatar {
                width: 44px;
                height: 44px;
            }

            .cd-hero-date {
                font-size: .72rem;
            }

            /* Upcoming teaser inside hero – stack vertically */
            .cd-hero .ms-auto {
                margin-left: 0 !important;
                margin-top: .5rem;
            }

            /* Stats – 2 cols already, tighten further */
            .cd-stat {
                padding: .75rem .75rem;
                gap: .6rem;
            }

            .cd-stat-icon {
                width: 36px;
                height: 36px;
                font-size: .9rem;
                border-radius: 9px;
            }

            .cd-stat-num {
                font-size: 1.1rem;
            }

            .cd-stat-lbl {
                font-size: .68rem;
            }

            /* Cards */
            .cd-card-header {
                padding: .75rem 1rem;
            }

            .cd-card-body {
                padding: .75rem 1rem;
            }

            /* Booking card */
            .cd-booking-card {
                padding: .85rem .85rem;
            }

            .cd-booking-card .room-img {
                width: 56px;
                height: 56px;
            }

            /* Action buttons – full width stack */
            .cd-booking-card .d-flex.gap-2.flex-wrap>* {
                width: 100%;
            }

            .cd-booking-card .d-flex.gap-2.flex-wrap .btn {
                width: 100%;
                text-align: center;
            }

            /* Modal summary strip – stack */
            .cd-modal .modal-body .d-flex.justify-content-between {
                flex-direction: column;
                gap: .5rem;
            }

            /* Payment accounts */
            .cd-bank-chip .acc-num {
                font-size: .85rem;
                letter-spacing: 1px;
            }

            /* Quick actions */
            .cd-qa-btn {
                padding: .75rem .4rem;
                font-size: .72rem;
            }

            .cd-qa-btn i {
                font-size: 1.1rem;
            }
        }

        /* Small phones 480–767 px */
        @media (min-width: 480px) and (max-width: 767px) {
            .cd-wrap {
                padding: 1rem 0 3.5rem;
            }

            .cd-hero {
                padding: 1.5rem 1.25rem 3rem;
                border-radius: 14px;
            }

            .cd-hero h1 {
                font-size: 1.3rem;
            }

            .cd-hero-avatar {
                width: 50px;
                height: 50px;
            }

            .cd-stat {
                padding: .9rem .9rem;
                gap: .7rem;
            }

            .cd-stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .cd-stat-num {
                font-size: 1.25rem;
            }

            .cd-card-header {
                padding: .85rem 1.1rem;
            }

            .cd-card-body {
                padding: .9rem 1.1rem;
            }

            /* Action buttons stack nicely */
            .cd-booking-card .d-flex.gap-2.flex-wrap .btn {
                flex: 1 1 auto;
            }
        }

        /* All mobiles shared – < 768 px */
        @media (max-width: 767px) {

            /* Stats number for currency – shrink */
            .cd-stat-num[style*="font-size:1.1rem"] {
                font-size: .95rem !important;
            }

            /* Hero upcoming teaser – allow badge to break */
            .cd-hero .flex-wrap .ms-auto {
                margin-left: 0 !important;
                align-self: flex-start;
            }

            /* Dates row in booking card – allow wrapping with tighter gaps */
            .cd-booking-card .d-flex.flex-wrap.gap-3 {
                gap: .5rem !important;
            }

            /* Payment history timeline – tighten */
            .cd-timeline li {
                gap: .5rem;
            }

            /* Filter tab scrollable wrapper */
            .cd-card-header .cd-filter-tab-wrap {
                max-width: calc(100vw - 140px);
            }

            /* Steps guide – always single column on mobile */
            .cd-card .row.g-2 .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            /* Quick actions: keep 2-col but tighter */
            .cd-qa-btn {
                gap: .35rem;
            }

            /* Modal: ensure dialog doesn't overflow viewport */
            .cd-modal .modal-dialog {
                margin: .75rem;
            }

            .cd-modal .modal-body {
                padding: 1rem;
            }
        }

        /* Tablets 768–991px – minor polish */
        @media (min-width: 768px) and (max-width: 991px) {
            .cd-stat {
                padding: 1rem 1rem;
                gap: .75rem;
            }

            .cd-stat-icon {
                width: 42px;
                height: 42px;
            }

            .cd-stat-num {
                font-size: 1.35rem;
            }
        }

        /* ─── Animations ────────────────────────────────────────── */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* .cd-animate {
            animation: slideUp .4s ease both;
        } */

        .cd-animate-d1 {
            animation-delay: .05s;
        }

        .cd-animate-d2 {
            animation-delay: .1s;
        }

        .cd-animate-d3 {
            animation-delay: .15s;
        }

        .cd-animate-d4 {
            animation-delay: .2s;
        }

        .cd-animate-d5 {
            animation-delay: .25s;
        }
    </style>
@endsection

@section('content')
    @php
        $customer = $customer ?? auth()->user()->customer;
        $userName = $customer ? $customer->name : auth()->user()->name;
        $avatarUrl =
            auth()->user() && method_exists(auth()->user(), 'getAvatar')
                ? auth()->user()->getAvatar()
                : asset('img/default-avatar.png');
    @endphp

    <div class="cd-wrap">
        <div class="container">

            {{-- ── Alerts ─────────────────────────────────────── --}}
            @if (session('success'))
                <div class="cd-alert-success mb-3 d-flex align-items-center gap-2 cd-animate">
                    <i class="fas fa-check-circle text-success fs-5"></i>
                    <div>
                        <div class="fw-bold text-success">Done!</div>
                        <small>{{ session('success') }}</small>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="cd-alert-danger mb-3 d-flex align-items-center gap-2 cd-animate">
                    <i class="fas fa-exclamation-circle text-danger fs-5"></i>
                    <small>{{ session('error') }}</small>
                </div>
            @endif

            {{-- ── Hero / greeting ─────────────────────────────── --}}
            <div class="cd-hero cd-animate mb-4">
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 mb-3">
                    <img src="{{ $avatarUrl }}" alt="{{ $userName }}" class="cd-hero-avatar">
                    <div>
                        <div class="cd-hero-date">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ now()->format('l, F j, Y') }}
                        </div>
                        <h1>Welcome back, {{ $userName }}! 👋</h1>
                        <p>Here's an overview of your bookings and account activity.</p>
                    </div>
                </div>

                {{-- ── Upcoming booking inline teaser ── --}}
                @if ($upcomingBooking ?? null)
                    @php
                        $ub = $upcomingBooking;
                        $daysToCheckIn = \App\Helpers\Helper::getDateDifference(now(), $ub->check_in);
                    @endphp
                    <div class="d-flex align-items-start align-items-sm-center gap-3 flex-wrap mt-1"
                        style="background:rgba(255,255,255,.12); border-radius:10px; padding:.75rem 1rem;">
                        <i class="fas fa-bed" style="font-size:1.4rem; opacity:.7;"></i>
                        <div>
                            <div
                                style="font-size:.72rem; opacity:.75; font-weight:600; text-transform:uppercase; letter-spacing:1px;">
                                Next Check-In</div>
                            <div style="font-size:1rem; font-weight:700;">Room {{ $ub->room->number }} &bull;
                                {{ \Carbon\Carbon::parse($ub->check_in)->format('M d, Y') }}</div>
                        </div>
                        <span class="ms-auto"
                            style="background:rgba(255,255,255,.25); border-radius:999px; padding:.3rem 1rem; font-size:.78rem; font-weight:700;">
                            {{ $daysToCheckIn <= 0 ? 'Today!' : "in {$daysToCheckIn} day" . ($daysToCheckIn > 1 ? 's' : '') }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- ── Stats row ─────────────────────────────────────── --}}
            <div class="row g-3 mb-4 cd-stats-row">
                <div class="col-12 col-sm-6 col-xl-3 cd-animate cd-animate-d1">
                    <div class="cd-stat">
                        <div class="cd-stat-icon indigo"><i class="fas fa-calendar-check"></i></div>
                        <div>
                            <div class="cd-stat-num">{{ $transactions->count() }}</div>
                            <div class="cd-stat-lbl">Total Bookings</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3 cd-animate cd-animate-d2">
                    <div class="cd-stat">
                        <div class="cd-stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
                        <div>
                            <div class="cd-stat-num">{{ $activeBookings }}</div>
                            <div class="cd-stat-lbl">Active / Pending</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3 cd-animate cd-animate-d3">
                    <div class="cd-stat">
                        <div class="cd-stat-icon green"><i class="fas fa-check-double"></i></div>
                        <div>
                            <div class="cd-stat-num">{{ $completedBookings }}</div>
                            <div class="cd-stat-lbl">Completed Stays</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3 cd-animate cd-animate-d4">
                    <div class="cd-stat">
                        <div class="cd-stat-icon indigo"><i class="fas fa-wallet"></i></div>
                        <div>
                            <div class="cd-stat-num" style="font-size:1.1rem;">
                                {{ \App\Helpers\Helper::convertToRupiah($totalSpent) }}
                            </div>
                            <div class="cd-stat-lbl">Total Spent</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Main row ──────────────────────────────────────── --}}
            <div class="row g-4">

                {{-- ──────── LEFT: Bookings ───────────────────── --}}
                <div class="col-lg-8">

                    {{-- How to Activate (only if pending) --}}
                    @if ($transactions->where('status', 'Reservation')->count() > 0)
                        <div class="cd-card mb-4 cd-animate cd-animate-d1">
                            <div class="cd-card-header">
                                <h5><i class="fas fa-info-circle text-primary me-2"></i>How to Confirm Your Booking</h5>
                                @if ($pendingPayment > 0)
                                    <span class="status-pill status-reservation">
                                        <i class="fas fa-clock"></i>
                                        {{ \App\Helpers\Helper::convertToRupiah($pendingPayment) }} due
                                    </span>
                                @endif
                            </div>
                            <div class="cd-card-body p-3">
                                <div class="row g-2">
                                    <div class="col-12 col-md-4">
                                        <div class="cd-step">
                                            <div class="cd-step-num">1</div>
                                            <div>
                                                <div class="cd-step-title"><i
                                                        class="fas fa-university me-1 text-primary"></i> Pay</div>
                                                <div class="cd-step-desc">Transfer the total amount to one of the payment
                                                    accounts listed below.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="cd-step">
                                            <div class="cd-step-num">2</div>
                                            <div>
                                                <div class="cd-step-title"><i class="fas fa-upload me-1 text-primary"></i>
                                                    Upload Receipt</div>
                                                <div class="cd-step-desc">Click <strong>"Pay / Upload Receipt"</strong> on
                                                    your booking card and attach proof.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="cd-step">
                                            <div class="cd-step-num">3</div>
                                            <div>
                                                <div class="cd-step-title"><i
                                                        class="fas fa-shield-alt me-1 text-primary"></i> Get Confirmed</div>
                                                <div class="cd-step-desc">Our team will verify your payment and activate
                                                    your booking shortly.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Bookings Card --}}
                    <div class="cd-card cd-animate cd-animate-d2">
                        <div class="cd-card-header flex-wrap gap-2">
                            <h5><i class="fas fa-suitcase-rolling text-primary me-2"></i>My Bookings</h5>
                            <div class="cd-filter-tab-wrap">
                                <div class="cd-filter-tab">
                                    <button class="active" data-filter="all">All</button>
                                    <button data-filter="Reservation">Pending</button>
                                    <button data-filter="Done">Completed</button>
                                    <button data-filter="Canceled">Canceled</button>
                                </div>
                            </div>
                        </div>
                        <div class="cd-card-body">
                            @forelse($transactions as $transaction)
                                @php
                                    $days = \App\Helpers\Helper::getDateDifference(
                                        $transaction->check_in,
                                        $transaction->check_out,
                                    );
                                    $totalPrice = $transaction->getTotalPrice();
                                    $paid = $transaction->getTotalPayment();
                                    $balance = max(0, $totalPrice - $paid);
                                    $paidPct = $totalPrice > 0 ? min(100, round(($paid / $totalPrice) * 100)) : 0;
                                    $statusClass = match ($transaction->status) {
                                        'Reservation' => 'status-reservation',
                                        'Done' => 'status-done',
                                        'Canceled' => 'status-canceled',
                                        default => 'status-active',
                                    };
                                    $roomImg = $transaction->room->firstImage();
                                    $daysLeft = \App\Helpers\Helper::getDateDifference(now(), $transaction->check_out);
                                @endphp
                                <div class="cd-booking-card mb-3" data-status="{{ $transaction->status }}">
                                    <div class="d-flex gap-3 align-items-start">
                                        {{-- Room image --}}
                                        <img src="{{ $roomImg }}" alt="Room {{ $transaction->room->number }}"
                                            class="room-img d-none d-sm-block">

                                        {{-- Main info --}}
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                <span
                                                    class="booking-id">#BK-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
                                                <span class="status-pill {{ $statusClass }}">
                                                    <i class="fas fa-circle" style="font-size:.45rem;"></i>
                                                    {{ $transaction->status }}
                                                </span>
                                                @if ($transaction->status === 'Reservation' && $daysLeft < 2 && $daysLeft >= 0)
                                                    <span class="status-pill" style="background:#fee2e2;color:#991b1b;">
                                                        <i class="fas fa-exclamation-triangle"
                                                            style="font-size:.7rem;"></i> Urgent
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="booking-room">Room {{ $transaction->room->number }}
                                                @if ($transaction->room->type)
                                                    <span class="booking-type ms-1">·
                                                        {{ $transaction->room->type->name }}</span>
                                                @endif
                                            </div>

                                            {{-- Dates + duration --}}
                                            <div class="d-flex flex-wrap gap-3 mt-2 text-muted" style="font-size:.8rem;">
                                                <span><i
                                                        class="fas fa-sign-in-alt me-1 text-success"></i>{{ \Carbon\Carbon::parse($transaction->check_in)->format('M d, Y') }}</span>
                                                <span><i
                                                        class="fas fa-sign-out-alt me-1 text-danger"></i>{{ \Carbon\Carbon::parse($transaction->check_out)->format('M d, Y') }}</span>
                                                <span><i class="fas fa-moon me-1 text-primary"></i>{{ $days }}
                                                    {{ \Illuminate\Support\Str::plural('Night', $days) }}</span>
                                            </div>

                                            {{-- Payment progress --}}
                                            <div class="mt-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted fw-600">Payment Progress</small>
                                                    <small
                                                        class="fw-bold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                                        {{ $paidPct }}% paid
                                                    </small>
                                                </div>
                                                <div class="cd-progress">
                                                    <div class="cd-progress-bar {{ $paidPct >= 100 ? 'bg-success' : ($paidPct > 0 ? 'bg-warning' : 'bg-danger') }}"
                                                        style="width:{{ $paidPct }}%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <small class="text-muted">Paid:
                                                        <strong>{{ \App\Helpers\Helper::convertToRupiah($paid) }}</strong></small>
                                                    @if ($balance > 0)
                                                        <small class="text-danger">Balance:
                                                            <strong>{{ \App\Helpers\Helper::convertToRupiah($balance) }}</strong></small>
                                                    @else
                                                        <small class="text-success"><i
                                                                class="fas fa-check-circle me-1"></i>Fully Paid</small>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Total price --}}
                                            <div
                                                class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
                                                <div>
                                                    <small class="text-muted">Total: </small>
                                                    <strong>{{ \App\Helpers\Helper::convertToRupiah($totalPrice) }}</strong>
                                                </div>
                                                {{-- Actions --}}
                                                @if ($transaction->status == 'Reservation')
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <button class="btn btn-sm btn-primary px-3" data-bs-toggle="modal"
                                                            data-bs-target="#payModal-{{ $transaction->id }}">
                                                            <i class="fas fa-upload me-1"></i>Pay / Upload Receipt
                                                        </button>
                                                        <form action="{{ route('transaction.cancel', $transaction->id) }}"
                                                            method="POST" class="d-inline"
                                                            id="cancelForm-{{ $transaction->id }}">
                                                            @csrf
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger px-3"
                                                                onclick="confirmCancel({{ $transaction->id }})">
                                                                <i class="fas fa-times me-1"></i>Cancel
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="cd-empty">
                                    <div class="cd-empty-icon"><i class="fas fa-suitcase"></i></div>
                                    <div class="cd-empty-title">No bookings yet</div>
                                    <div class="cd-empty-sub">Ready for your first stay? Browse our rooms below!</div>
                                    <a href="{{ route('public.rooms') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-bed me-2"></i>Browse Rooms
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- ── Payment History ──────────────────── --}}
                    @php
                        $allPayments = $transactions->flatMap(fn($t) => $t->payment)->sortByDesc('created_at');
                    @endphp
                    @if ($allPayments->count() > 0)
                        <div class="cd-card mt-4 cd-animate cd-animate-d3">
                            <div class="cd-card-header">
                                <h5><i class="fas fa-receipt text-primary me-2"></i>Payment History</h5>
                                <span class="badge bg-light text-muted">{{ $allPayments->count() }}
                                    {{ \Illuminate\Support\Str::plural('record', $allPayments->count()) }}</span>
                            </div>
                            <div class="cd-card-body p-3">
                                <ul class="cd-timeline">
                                    @foreach ($allPayments as $pmt)
                                        @php
                                            $dotClass = match ($pmt->status) {
                                                'Approved' => 'paid',
                                                'Pending' => 'pending',
                                                default => 'reject',
                                            };
                                        @endphp
                                        <li>
                                            <div class="cd-tl-dot {{ $dotClass }}" title="{{ $pmt->status }}">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between flex-wrap gap-1">
                                                    <div>
                                                        <div class="fw-bold" style="font-size:.85rem;">
                                                            {{ \App\Helpers\Helper::convertToRupiah($pmt->price) }}
                                                            <span
                                                                class="badge ms-1
                                                {{ $pmt->status === 'Approved' ? 'bg-success' : ($pmt->status === 'Pending' ? 'bg-warning text-dark' : 'bg-danger') }}"
                                                                style="font-size:.65rem;">
                                                                {{ $pmt->status }}
                                                            </span>
                                                        </div>
                                                        <div style="font-size:.75rem; color:var(--cd-muted);">
                                                            Booking
                                                            #BK-{{ str_pad($pmt->transaction_id, 5, '0', STR_PAD_LEFT) }}
                                                            @if ($pmt->reference_number)
                                                                · Ref: {{ $pmt->reference_number }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div
                                                        style="font-size:.75rem; color:var(--cd-muted); white-space:nowrap;">
                                                        {{ \App\Helpers\Helper::dateFormatTimeNoYear($pmt->created_at) }}
                                                    </div>
                                                </div>
                                                @if ($pmt->receipt_image)
                                                    <div class="mt-1">
                                                        <a href="{{ asset($pmt->receipt_image) }}" target="_blank"
                                                            class="text-decoration-none"
                                                            style="font-size:.75rem; color:var(--cd-primary);">
                                                            <i class="fas fa-image me-1"></i>View Receipt
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ──────── RIGHT: sidebar ───────────────────── --}}
                <div class="col-lg-4">

                    {{-- Quick Actions --}}
                    <div class="cd-card mb-4 cd-animate cd-animate-d1">
                        <div class="cd-card-header">
                            <h5><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="cd-card-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('public.rooms') }}" class="cd-qa-btn">
                                        <i class="fas fa-bed text-primary"></i>
                                        Browse Rooms
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('customer.show', auth()->user()->customer->id ?? 0) }}"
                                        class="cd-qa-btn">
                                        <i class="fas fa-user-edit text-indigo" style="color:var(--cd-primary);"></i>
                                        My Profile
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('public.location') }}" class="cd-qa-btn">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                        Location
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('public.blog.index') }}" class="cd-qa-btn">
                                        <i class="fas fa-newspaper text-success"></i>
                                        Blog
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Accounts --}}
                    <div class="cd-card mb-4 cd-animate cd-animate-d2">
                        <div class="cd-card-header">
                            <h5><i class="fas fa-university text-primary me-2"></i>Payment Accounts</h5>
                        </div>
                        <div class="cd-card-body d-flex flex-column gap-3">
                            @forelse($paymentAccounts as $account)
                                <div class="cd-bank-chip" onclick="copyAccNum('{{ $account->account_number }}')"
                                    title="Click to copy account number">
                                    <div class="bank-name">
                                        <i class="fas fa-landmark me-1"></i>{{ $account->bank_name }}
                                    </div>
                                    <div class="acc-num mt-1">{{ $account->account_number }}</div>
                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <div class="acc-holder">{{ $account->account_name }}</div>
                                        <small class="text-muted"><i class="fas fa-copy"></i> Copy</small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">No payment methods configured yet. Please contact support.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Contact / Support --}}
                    <div class="cd-card cd-animate cd-animate-d3">
                        <div class="cd-card-header">
                            <h5><i class="fas fa-headset text-primary me-2"></i>Need Help?</h5>
                        </div>
                        <div class="cd-card-body">
                            <p class="text-muted small mb-3">Our front desk is happy to assist with bookings, payments, or
                                any inquiries.</p>

                            <div class="d-flex flex-column gap-2">
                                <a href="mailto:{{ $global_settings['contact_email'] ?? config('mail.from.address') }}"
                                    class="d-flex align-items-center gap-2 text-decoration-none p-2 rounded-2 hover-bg"
                                    style="color:#374151; transition:background .2s;"
                                    onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background=''">
                                    <div class="cd-stat-icon indigo"
                                        style="width:32px;height:32px;border-radius:8px;font-size:.9rem;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <span
                                        style="font-size:.85rem;">{{ $global_settings['contact_email'] ?? config('mail.from.address') }}</span>
                                </a>

                                @if (!empty($global_settings['contact_phone']))
                                    <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="color:#374151;">
                                        <div class="cd-stat-icon green"
                                            style="width:32px;height:32px;border-radius:8px;font-size:.9rem;">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <span style="font-size:.85rem;">{{ $global_settings['contact_phone'] }}</span>
                                    </div>
                                @endif

                                <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="color:#374151;">
                                    <div class="cd-stat-icon amber"
                                        style="width:32px;height:32px;border-radius:8px;font-size:.9rem;">
                                        <i class="fas fa-hotel"></i>
                                    </div>
                                    <span
                                        style="font-size:.85rem;">{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}
                                        – Front Desk</span>
                                </div>
                            </div>

                            <div class="mt-3 p-2 rounded-2 text-center" style="background:#eff6ff;">
                                <small class="text-primary fw-bold">
                                    <i class="fas fa-clock me-1"></i>
                                    Receipt verification: {{ $global_settings['receipt_verify_time'] ?? '1-2 hours' }}
                                    during business hours
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
     PAY MODALS  (outside table; one per pending booking)
═══════════════════════════════════════════════════════ --}}
    @foreach ($transactions as $transaction)
        @if ($transaction->status == 'Reservation')
            @php
                $tp = $transaction->getTotalPrice();
                $tpaid = $transaction->getTotalPayment();
                $tbal = max(0, $tp - $tpaid);
            @endphp
            <div class="modal fade cd-modal" id="payModal-{{ $transaction->id }}" tabindex="-1"
                aria-labelledby="payModalLabel-{{ $transaction->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="payModalLabel-{{ $transaction->id }}">
                                <i class="fas fa-upload me-2"></i>Upload Payment Receipt
                                <small class="d-block fw-normal opacity-75" style="font-size:.78rem;">
                                    Booking #BK-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }} · Room
                                    {{ $transaction->room->number }}
                                </small>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form action="{{ route('transaction.uploadReceipt', $transaction->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">

                                {{-- Summary strip --}}
                                <div class="d-flex justify-content-between p-3 mb-3 rounded-3"
                                    style="background:#f8fafc; font-size:.85rem;">
                                    <div>
                                        <div class="text-muted">Check-in</div>
                                        <strong>{{ \Carbon\Carbon::parse($transaction->check_in)->format('M d, Y') }}</strong>
                                    </div>
                                    <div>
                                        <div class="text-muted">Check-out</div>
                                        <strong>{{ \Carbon\Carbon::parse($transaction->check_out)->format('M d, Y') }}</strong>
                                    </div>
                                    <div>
                                        <div class="text-muted">Balance Due</div>
                                        <strong class="{{ $tbal > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Helpers\Helper::convertToRupiah($tbal) }}
                                        </strong>
                                    </div>
                                </div>

                                {{-- Drop zone --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Receipt / Proof of Payment <span
                                            class="text-danger">*</span></label>
                                    <div class="cd-drop-zone" id="dropZone-{{ $transaction->id }}">
                                        <input type="file" name="receipt_image" accept="image/*" required
                                            onchange="previewFile(this, 'preview-{{ $transaction->id }}')">
                                        <div id="dropHint-{{ $transaction->id }}">
                                            <i class="fas fa-cloud-upload-alt text-primary mb-2"
                                                style="font-size:2rem;"></i>
                                            <div class="fw-bold" style="font-size:.9rem;">Drag & drop or click to browse
                                            </div>
                                            <small class="text-muted">JPG, PNG, GIF – max 5MB</small>
                                        </div>
                                        <img id="preview-{{ $transaction->id }}" src="" alt="Preview"
                                            style="display:none; max-width:100%; max-height:150px; object-fit:contain; border-radius:8px; margin-top:.5rem;">
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Amount Paid <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"
                                                style="border-radius:10px 0 0 10px; font-size:.8rem;">TZS</span>
                                            <input type="number" class="form-control" name="price"
                                                value="{{ $tbal > 0 ? $tbal : $tp }}" min="1" required
                                                style="border-radius:0 10px 10px 0;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Reference / Transaction ID</label>
                                        <input type="text" class="form-control" name="reference_number"
                                            placeholder="e.g. TRX-20240701-001">
                                    </div>
                                </div>

                                <div class="mt-3 p-2 rounded-2 text-center" style="background:#fef3c7;">
                                    <small class="text-warning fw-bold">
                                        <i class="fas fa-info-circle me-1"></i>
                                        After submission, our team will verify your payment within
                                        {{ $global_settings['receipt_verify_time'] ?? '1-2 hours' }}.
                                    </small>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Receipt
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- ── Cancel confirm toast modal ──────────────────────── --}}
    <div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0" style="border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.15);">
                <div class="modal-body text-center p-4">
                    <div style="font-size:3rem; margin-bottom:.75rem;">⚠️</div>
                    <h6 class="fw-bold mb-1">Cancel Booking?</h6>
                    <p class="text-muted small mb-4">This action cannot be undone. Are you sure you want to cancel this
                        booking?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">Keep It</button>
                        <button class="btn btn-danger btn-sm px-4" id="confirmCancelBtn">Yes, Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /* ─── Booking filter ─────────────────────────── */
        document.querySelectorAll('.cd-filter-tab button').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.cd-filter-tab button').forEach(b => b.classList.remove(
                    'active'));
                this.classList.add('active');
                const filter = this.dataset.filter;
                document.querySelectorAll('.cd-booking-card').forEach(card => {
                    const status = card.dataset.status;
                    card.style.display = (filter === 'all' || status === filter) ? '' : 'none';
                });
            });
        });

        /* ─── File preview ───────────────────────────── */
        function previewFile(input, previewId) {
            const preview = document.getElementById(previewId);
            const hint = document.getElementById('dropHint-' + previewId.replace('preview-', ''));
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (hint) hint.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        /* ─── Copy account number ────────────────────── */
        function copyAccNum(num) {
            navigator.clipboard.writeText(num).then(() => {
                showToast('Account number copied: ' + num, 'success');
            }).catch(() => {
                showToast('Copy failed – please copy manually.', 'danger');
            });
        }

        /* ─── Cancel confirm ─────────────────────────── */
        let cancelTargetId = null;

        function confirmCancel(id) {
            cancelTargetId = id;
            new bootstrap.Modal(document.getElementById('cancelConfirmModal')).show();
        }
        document.getElementById('confirmCancelBtn').addEventListener('click', () => {
            if (cancelTargetId) {
                document.getElementById('cancelForm-' + cancelTargetId).submit();
            }
        });

        /* ─── Toast helper ───────────────────────────── */
        function showToast(msg, type = 'success') {
            const wrap = document.createElement('div');
            wrap.style.cssText = `
                position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999;
                background:${type === 'success' ? '#059669' : '#dc2626'};
                color:#fff; padding:.6rem 1.25rem; border-radius:10px;
                font-size:.85rem; font-weight:600; box-shadow:0 4px 20px rgba(0,0,0,.2);
                animation:slideUp .3s ease;
            `;
            wrap.textContent = msg;
            document.body.appendChild(wrap);
            setTimeout(() => wrap.remove(), 3000);
        }

        /* ─── Animate on scroll ──────────────────────── */
        const cdObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });
        document.querySelectorAll('.cd-booking-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(12px)';
            el.style.transition = 'opacity .35s ease, transform .35s ease';
            cdObserver.observe(el);
        });
    </script>
@endsection
