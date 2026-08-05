@extends('template.master')
@section('title', 'Notifications Center')
@section('content')
<div class="container-fluid py-3 notification-hub">
    <!-- Header Title & Breadcrumb -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">
                <i class="fas fa-bell text-primary me-2"></i>Notifications Center
            </h3>
            <p class="text-muted mb-0 small">Manage and track system notifications, reservation alerts, and payment updates.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
            @if(($unreadCount ?? 0) > 0)
                <a href="{{ route('notification.markAllAsRead') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm btn-sm align-self-center">
                    <i class="fas fa-check-double me-1"></i> Mark All as Read
                </a>
            @endif
            @if(($totalCount ?? 0) > 0)
                <form action="{{ route('notification.destroyAll') }}" method="POST" class="d-inline mb-0" onsubmit="return confirm('Are you sure you want to clear all notifications?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3 shadow-sm btn-sm align-self-center">
                        <i class="fas fa-trash-alt me-1"></i> Clear All
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('failed'))
        <div class="alert alert-warning alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('failed') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <a href="{{ route('notification.index', ['tab' => 'all']) }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm p-3 bg-white h-100 {{ ($tab ?? 'all') === 'all' ? 'border-primary border-2' : '' }}">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-subtle text-primary me-3">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Total Notifications</div>
                            <div class="fs-4 fw-bold text-dark">{{ $totalCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-4">
            <a href="{{ route('notification.index', ['tab' => 'unread']) }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm p-3 bg-white h-100 {{ ($tab ?? '') === 'unread' ? 'border-warning border-2' : '' }}">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-subtle text-warning me-3">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Unread</div>
                            <div class="fs-4 fw-bold text-dark">{{ $unreadCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-4">
            <a href="{{ route('notification.index', ['tab' => 'read']) }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm p-3 bg-white h-100 {{ ($tab ?? '') === 'read' ? 'border-success border-2' : '' }}">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-subtle text-success me-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Read</div>
                            <div class="fs-4 fw-bold text-dark">{{ $readCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter Tabs Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <ul class="nav nav-pills nav-pills-custom gap-2 mb-0">
                <li class="nav-item">
                    <a class="nav-link {{ ($tab ?? 'all') === 'all' ? 'active' : '' }}" href="{{ route('notification.index', ['tab' => 'all']) }}">
                        All Notifications
                        <span class="badge {{ ($tab ?? 'all') === 'all' ? 'bg-light text-dark' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $totalCount ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($tab ?? '') === 'unread' ? 'active' : '' }}" href="{{ route('notification.index', ['tab' => 'unread']) }}">
                        Unread
                        @if(($unreadCount ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($tab ?? '') === 'read' ? 'active' : '' }}" href="{{ route('notification.index', ['tab' => 'read']) }}">
                        Read
                        <span class="badge {{ ($tab ?? '') === 'read' ? 'bg-light text-dark' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $readCount ?? 0 }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="row g-3">
        @forelse ($notifications as $notification)
            @php
                $isUnread = is_null($notification->read_at);
                $message = $notification->data['message'] ?? 'New notification received.';
                $url = route('notification.routeTo', ['id' => $notification->id]);
            @endphp
            <div class="col-12">
                <div class="card notif-card shadow-sm p-3 {{ $isUnread ? 'unread' : '' }}">
                    <div class="d-flex align-items-start gap-3">
                        <!-- Icon -->
                        <div class="notif-icon-wrapper {{ $isUnread ? 'bg-primary text-white shadow-sm' : 'bg-light text-secondary' }}">
                            <i class="fas {{ Str::contains(strtolower($message), 'room') ? 'fa-door-open' : (Str::contains(strtolower($message), 'payment') ? 'fa-credit-card' : 'fa-info-circle') }}"></i>
                        </div>

                        <!-- Content -->
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="notif-title text-dark">
                                        {{ $notification->data['title'] ?? (Str::contains(strtolower($message), 'room') ? 'Room Reservation' : 'System Notification') }}
                                    </span>
                                    @if ($isUnread)
                                        <span class="unread-dot" title="Unread notification"></span>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill small">New</span>
                                    @else
                                        <span class="badge bg-light text-muted rounded-pill small"><i class="fas fa-check me-1"></i>Read</span>
                                    @endif
                                </div>
                                <span class="notif-time text-muted small">
                                    <i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="notif-body mb-2 text-secondary">{{ $message }}</p>

                            <!-- Actions -->
                            <div class="d-flex align-items-center gap-2 notif-actions pt-2 border-top mt-2">
                                <a href="{{ $url }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-none">
                                    <i class="fas fa-external-link-alt me-1 small"></i> See Details
                                </a>

                                @if ($isUnread)
                                    <form action="{{ route('notification.markAsRead', $notification->id) }}" method="POST" class="d-inline mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                            <i class="fas fa-check me-1"></i> Mark as Read
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('notification.destroy', $notification->id) }}" method="POST" class="d-inline mb-0 ms-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none p-1" title="Delete Notification">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state-card shadow-sm">
                    <div class="empty-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No Notifications Found</h5>
                    <p class="text-muted small mb-3">
                        @if(($tab ?? '') === 'unread')
                            You have caught up on all unread notifications!
                        @elseif(($tab ?? '') === 'read')
                            You do not have any read notifications archived yet.
                        @else
                            You have zero system notifications at the moment.
                        @endif
                    </p>
                    <a href="{{ route('dashboard.index') }}" class="btn btn-primary rounded-pill px-4 btn-sm">
                        <i class="fas fa-home me-1"></i> Return to Dashboard
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($notifications->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

