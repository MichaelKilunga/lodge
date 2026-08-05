@extends('template.master')
@section('title', 'Notification Center')
@section('head')
<link rel="stylesheet" href="{{ asset('style/css/notification.css') }}">
@endsection

@section('content')
<div class="container-fluid py-4 notification-center-container">
    {{-- Header Banner Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 56px; height: 56px; background-color: rgba(69, 87, 187, 0.12);">
                    <i class="fas fa-bell fa-lg text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-1 fw-bold text-dark">Notification Center</h3>
                    <p class="text-muted mb-0 small">Track all system notifications, reservation updates, and alerts in one place.</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if (auth()->user()->unreadNotifications->count() > 0)
                    <a href="{{ route('notification.markAllAsRead') }}" class="btn btn-outline-primary fw-medium px-3 rounded-3 shadow-xs">
                        <i class="fas fa-check-double me-2"></i>Mark All as Read
                    </a>
                @else
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill" style="background-color: rgba(40, 167, 69, 0.12); color: #28a745;">
                        <i class="fas fa-check-circle me-1"></i>All caught up
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('failed'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('failed') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $allNotifications = auth()->user()->notifications;
        $unreadNotifications = auth()->user()->unreadNotifications;
        $readNotifications = auth()->user()->readNotifications;
    @endphp

    {{-- Filter Navigation Tabs --}}
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
        <ul class="nav nav-pills notif-nav-tabs gap-2" id="notifTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-notifications" type="button" role="tab" aria-controls="all-notifications" aria-selected="true">
                    All Notifications
                    <span class="badge bg-secondary ms-2 rounded-pill">{{ $allNotifications->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="unread-tab" data-bs-toggle="tab" data-bs-target="#unread-notifications" type="button" role="tab" aria-controls="unread-notifications" aria-selected="false">
                    Unread
                    @if($unreadNotifications->count() > 0)
                        <span class="badge bg-primary ms-2 rounded-pill">{{ $unreadNotifications->count() }}</span>
                    @else
                        <span class="badge bg-light text-dark ms-2 rounded-pill">0</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="read-tab" data-bs-toggle="tab" data-bs-target="#read-notifications" type="button" role="tab" aria-controls="read-notifications" aria-selected="false">
                    Read History
                    <span class="badge bg-light text-dark ms-2 rounded-pill">{{ $readNotifications->count() }}</span>
                </button>
            </li>
        </ul>
    </div>

    {{-- Tab Contents --}}
    <div class="tab-content" id="notifTabContent">
        {{-- ALL TAB --}}
        <div class="tab-pane fade show active" id="all-notifications" role="tabpanel" aria-labelledby="all-tab">
            @forelse ($allNotifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $isReservation = str_contains(strtolower($notification->data['message'] ?? ''), 'reservated') || str_contains(strtolower($notification->data['message'] ?? ''), 'reservation');
                @endphp
                <div class="card notif-card {{ $isUnread ? 'unread' : 'read' }} mb-3">
                    <div class="card-body p-3 p-md-4 d-flex align-items-start gap-3">
                        <div class="notif-icon-box {{ $isReservation ? 'notif-icon-reservation' : 'notif-icon-system' }}">
                            <i class="fas {{ $isReservation ? 'fa-key' : 'fa-bell' }} fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-1 mb-1">
                                <div class="notif-title me-2">
                                    {{ $notification->data['message'] ?? 'Notification details' }}
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($isUnread)
                                        <span class="badge bg-primary text-white rounded-pill px-2 py-1" style="font-size: 0.7rem;">Unread</span>
                                    @else
                                        <span class="badge bg-light text-muted rounded-pill px-2 py-1" style="font-size: 0.7rem;">Read</span>
                                    @endif
                                </div>
                            </div>
                            <div class="notif-meta d-flex align-items-center gap-3">
                                <span><i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</span>
                                <span><i class="far fa-calendar-alt me-1"></i>{{ Helper::dateFormatTimeNoYear($notification->created_at) }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            @if ($isUnread)
                                <a href="{{ route('notification.markAsRead', ['id' => $notification->id]) }}" class="btn btn-sm btn-light text-muted rounded-3 px-2 py-1" title="Mark as read">
                                    <i class="fas fa-check me-1"></i>Mark Read
                                </a>
                            @endif
                            <a href="{{ route('notification.routeTo', ['id' => $notification->id]) }}" class="btn btn-sm btn-primary rounded-3 px-3 py-1 fw-medium shadow-xs">
                                See Detail <i class="fas fa-external-link-alt ms-1" style="font-size: 0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state-box">
                    <div class="empty-state-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No notifications found</h5>
                    <p class="text-muted mb-0">When system events or reservation alerts occur, they will appear here.</p>
                </div>
            @endforelse
        </div>

        {{-- UNREAD TAB --}}
        <div class="tab-pane fade" id="unread-notifications" role="tabpanel" aria-labelledby="unread-tab">
            @forelse ($unreadNotifications as $notification)
                @php
                    $isReservation = str_contains(strtolower($notification->data['message'] ?? ''), 'reservated') || str_contains(strtolower($notification->data['message'] ?? ''), 'reservation');
                @endphp
                <div class="card notif-card unread mb-3">
                    <div class="card-body p-3 p-md-4 d-flex align-items-start gap-3">
                        <div class="notif-icon-box {{ $isReservation ? 'notif-icon-reservation' : 'notif-icon-system' }}">
                            <i class="fas {{ $isReservation ? 'fa-key' : 'fa-bell' }} fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-1 mb-1">
                                <div class="notif-title me-2">
                                    {{ $notification->data['message'] ?? 'Notification details' }}
                                </div>
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1" style="font-size: 0.7rem;">New</span>
                            </div>
                            <div class="notif-meta d-flex align-items-center gap-3">
                                <span><i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</span>
                                <span><i class="far fa-calendar-alt me-1"></i>{{ Helper::dateFormatTimeNoYear($notification->created_at) }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <a href="{{ route('notification.markAsRead', ['id' => $notification->id]) }}" class="btn btn-sm btn-light text-muted rounded-3 px-2 py-1" title="Mark as read">
                                <i class="fas fa-check me-1"></i>Mark Read
                            </a>
                            <a href="{{ route('notification.routeTo', ['id' => $notification->id]) }}" class="btn btn-sm btn-primary rounded-3 px-3 py-1 fw-medium shadow-xs">
                                See Detail <i class="fas fa-external-link-alt ms-1" style="font-size: 0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state-box">
                    <div class="empty-state-icon" style="background-color: rgba(40, 167, 69, 0.12); color: #28a745;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No unread notifications</h5>
                    <p class="text-muted mb-0">You have read all your notifications!</p>
                </div>
            @endforelse
        </div>

        {{-- READ TAB --}}
        <div class="tab-pane fade" id="read-notifications" role="tabpanel" aria-labelledby="read-tab">
            @forelse ($readNotifications as $notification)
                @php
                    $isReservation = str_contains(strtolower($notification->data['message'] ?? ''), 'reservated') || str_contains(strtolower($notification->data['message'] ?? ''), 'reservation');
                @endphp
                <div class="card notif-card read mb-3">
                    <div class="card-body p-3 p-md-4 d-flex align-items-start gap-3">
                        <div class="notif-icon-box {{ $isReservation ? 'notif-icon-reservation' : 'notif-icon-system' }}">
                            <i class="fas {{ $isReservation ? 'fa-key' : 'fa-bell' }} fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-1 mb-1">
                                <div class="notif-title me-2 text-muted">
                                    {{ $notification->data['message'] ?? 'Notification details' }}
                                </div>
                                <span class="badge bg-light text-muted rounded-pill px-2 py-1" style="font-size: 0.7rem;">Read</span>
                            </div>
                            <div class="notif-meta d-flex align-items-center gap-3">
                                <span><i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</span>
                                <span><i class="far fa-calendar-alt me-1"></i>{{ Helper::dateFormatTimeNoYear($notification->created_at) }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <a href="{{ route('notification.routeTo', ['id' => $notification->id]) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-1 fw-medium">
                                See Detail <i class="fas fa-external-link-alt ms-1" style="font-size: 0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state-box">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No read history</h5>
                    <p class="text-muted mb-0">Read notifications will be archived here for your reference.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
