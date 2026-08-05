<nav class="navbar navbar-expand navbar-lh px-3 fixed-top" style="height: 65px">
    <div class="container-fluid">
        <!-- Menu Toggle -->
        <div id="menu-toggle" class="btn btn-outline-secondary d-flex justify-content-center align-items-center me-3"
            style="width: 2.5rem; height: 2.5rem; border-radius: 8px;">
            <i class="fa fa-bars"></i>
        </div>

        <!-- Brand -->
        <div class="navbar-brand fw-bold text-gradient me-auto">
            <i class="fas fa-hotel me-2"></i>
            Hotel Admin
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="ms-auto d-flex align-items-center">

                <!-- Quick Actions (New) -->
                <div class="btn-group me-3" role="group">
                    <button type="button" class="btn btn-hotel-primary btn-sm" data-bs-toggle="tooltip" title="New Reservation">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- Notifications -->
                @php $headerUnreadCount = auth()->user()->unreadNotifications->count(); @endphp
                <div class="dropdown me-3" id="refreshThisDropdown">
                    <div class="dropdown-toggle btn btn-outline-secondary position-relative d-flex align-items-center justify-content-center"
                         id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false"
                         style="border-radius: 10px; width: 40px; height: 40px; padding: 0;">
                        <i class="fas fa-bell fs-5"></i>
                        @if ($headerUnreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-light p-1" style="font-size: 0.7rem; min-width: 18px; min-height: 18px; line-height: 10px;">
                                {{ $headerUnreadCount > 99 ? '99+' : $headerUnreadCount }}
                            </span>
                        @endif
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 p-0" aria-labelledby="dropdownMenuButton2" style="width: 360px; overflow: hidden;">
                        <li class="dropdown-header bg-light py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-dark fs-6">Notifications</span>
                                @if ($headerUnreadCount > 0)
                                    <span class="badge bg-primary-soft text-primary ms-1" style="background-color: rgba(69, 87, 187, 0.1);">{{ $headerUnreadCount }} unread</span>
                                @endif
                            </div>
                            @if ($headerUnreadCount > 0)
                                <a href="{{ route('notification.markAllAsRead') }}" class="text-decoration-none text-muted small hover-primary" title="Mark all as read">
                                    <i class="fas fa-check-double me-1"></i>Mark read
                                </a>
                            @endif
                        </li>

                        <div style="max-height: 340px; overflow-y: auto;">
                            @forelse (auth()->user()->unreadNotifications->take(5) as $notification)
                                @php
                                    $isReservation = str_contains(strtolower($notification->data['message'] ?? ''), 'reservated') || str_contains(strtolower($notification->data['message'] ?? ''), 'reservation');
                                @endphp
                                <li class="border-bottom">
                                    <div class="dropdown-item py-3 px-3 d-flex align-items-start position-relative hover-bg-light">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="{{ $isReservation ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }} rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 38px; height: 38px; background-color: {{ $isReservation ? 'rgba(40, 167, 69, 0.12)' : 'rgba(69, 87, 187, 0.12)' }};">
                                                <i class="fas {{ $isReservation ? 'fa-key' : 'fa-bell' }}" style="font-size: 0.9rem; color: {{ $isReservation ? '#28a745' : '#4557bb' }};"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 min-w-0 me-2">
                                            <a href="{{ route('notification.routeTo', ['id' => $notification->id]) }}" class="text-decoration-none text-dark d-block">
                                                <p class="mb-1 fw-semibold text-break" style="font-size: 0.875rem; line-height: 1.35;">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                                <small class="text-muted" style="font-size: 0.75rem;"><i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</small>
                                            </a>
                                        </div>
                                        <div class="flex-shrink-0 ms-1">
                                            <a href="{{ route('notification.markAsRead', ['id' => $notification->id]) }}" class="btn btn-sm btn-light text-muted rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;" title="Dismiss">
                                                <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li>
                                    <div class="dropdown-item-text text-center py-5">
                                        <div class="mb-3 text-muted opacity-50">
                                            <i class="fas fa-bell-slash fa-3x"></i>
                                        </div>
                                        <p class="text-muted mb-0 fw-medium">No unread notifications</p>
                                        <small class="text-muted">You are all caught up!</small>
                                    </div>
                                </li>
                            @endforelse
                        </div>

                        <li class="bg-light py-2 px-3 border-top text-center">
                            <a href="{{ route('notification.index') }}" class="btn btn-sm btn-primary w-100 fw-medium shadow-sm">
                                View Notification Center <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- User Profile -->
                <div class="dropdown">
                    <div class="dropdown-toggle d-flex align-items-center" id="dropdownMenuButton1"
                         data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <img src="{{ auth()->user()->getAvatar() }}"
                             class="rounded-circle me-2"
                             width="32" height="32" alt="Profile">
                        <div class="d-none d-md-block text-start">
                            <div class="fw-medium" style="font-size: 0.875rem;">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->role }}</div>
                        </div>
                        <i class="fas fa-chevron-down ms-2 text-muted" style="font-size: 0.75rem;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="dropdownMenuButton1">
                        <li class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ auth()->user()->getAvatar() }}"
                                     class="rounded-circle me-3"
                                     width="40" height="40" alt="Profile">
                                <div>
                                    <div class="fw-medium">{{ auth()->user()->name }}</div>
                                    <div class="text-muted small">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center"
                               href="{{ route('user.show', ['user' => auth()->user()->id]) }}">
                                <i class="fas fa-user me-3 text-primary"></i>
                                View Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center"
                               href="{{ route('activity-log.index') }}">
                                <i class="fas fa-history me-3 text-info"></i>
                                Activity Log
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <i class="fas fa-cog me-3 text-secondary"></i>
                                Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="/logout" method="POST" class="mb-0">
                                @csrf
                                <button class="dropdown-item d-flex align-items-center text-danger" type="submit">
                                    <i class="fas fa-sign-out-alt me-3"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
