<header class="topbar-header d-none d-lg-block bg-white border-bottom shadow-sm sticky-top" style="z-index: 1020;">
    <div class="container-fluid px-4 py-2.5">
        <div class="d-flex align-items-center justify-content-between">

            <!-- Left: Sidebar Toggle & App Title / Quick Search -->
            <div class="d-flex align-items-center gap-3">
                <button id="sidebar-toggle" class="btn btn-light btn-sm border-0 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 38px; height: 38px;" title="Toggle Sidebar">
                    <i class="fas fa-bars text-secondary"></i>
                </button>

                <div class="d-flex align-items-center">
                    <span class="fw-bold fs-5 text-dark me-2">{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}</span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill small px-2 py-1">Admin Panel</span>
                </div>
            </div>

            <!-- Right: Quick Action, Notification Bell, User Avatar Dropdown -->
            <div class="d-flex align-items-center gap-3">
                <!-- Quick New Reservation Action -->
                <a href="{{ route('transaction.reservation.createIdentity') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-medium d-flex align-items-center shadow-sm">
                    <i class="fas fa-plus me-1.5 small"></i> New Reservation
                </a>

                <!-- Desktop Notification Bell Dropdown -->
                <div class="dropdown me-1" id="refreshThisDropdown">
                    <button class="btn btn-light position-relative border-0 rounded-circle d-flex align-items-center justify-content-center shadow-none bg-light-subtle"
                         id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false"
                         style="width: 40px; height: 40px; cursor: pointer;" title="Notifications">
                        <i class="fas fa-bell fs-5 text-secondary"></i>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm border border-2 border-white" style="font-size: 0.65rem; padding: 0.25em 0.5em;">
                                {{ auth()->user()->unreadNotifications->count() }}
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" aria-labelledby="dropdownMenuButton2" style="width: 350px; overflow: hidden;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center bg-light py-3 px-3 border-bottom">
                            <span class="fw-bold text-dark fs-6"><i class="fas fa-bell text-primary me-2"></i>Notifications</span>
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1">{{ auth()->user()->unreadNotifications->count() }} unread</span>
                            @endif
                        </li>

                        <div style="max-height: 320px; overflow-y: auto;">
                            @forelse (auth()->user()->unreadNotifications->take(5) as $notification)
                                <li>
                                    <a class="dropdown-item py-3 px-3 border-bottom text-wrap"
                                       href="{{ route('notification.routeTo', ['id' => $notification->id]) }}">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 mt-1">
                                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 36px; height: 36px;">
                                                    <i class="fas fa-calendar-check" style="font-size: 0.875rem;"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="mb-1 text-dark small fw-medium lh-sm">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li>
                                    <div class="dropdown-item-text text-center py-4">
                                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-2">
                                            <i class="fas fa-bell-slash text-muted fs-4"></i>
                                        </div>
                                        <p class="text-muted mb-0 small">No unread notifications</p>
                                    </div>
                                </li>
                            @endforelse
                        </div>

                        <li class="bg-light border-top p-2 d-flex justify-content-between align-items-center">
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <a href="{{ route('notification.markAllAsRead') }}" class="btn btn-sm btn-link text-decoration-none text-muted small px-2">
                                    <i class="fas fa-check-double me-1"></i>Mark all read
                                </a>
                            @endif
                            <a href="{{ route('notification.index') }}" class="btn btn-sm btn-primary ms-auto px-3 rounded-pill">
                                View All Center <i class="fas fa-arrow-right ms-1 small"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="vr text-muted opacity-25" style="height: 24px;"></div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <div class="dropdown-toggle d-flex align-items-center gap-2" id="dropdownMenuButton1"
                         data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <img src="{{ auth()->user()->getAvatar() }}"
                             class="rounded-circle border border-2 border-primary-subtle shadow-sm"
                             width="36" height="36" alt="Profile">
                        <div class="d-none d-xl-block text-start">
                            <div class="fw-semibold text-dark lh-1" style="font-size: 0.875rem;">{{ auth()->user()->name }}</div>
                            <small class="text-muted" style="font-size: 0.725rem;">{{ auth()->user()->role_id ? auth()->user()->userRole->name : auth()->user()->role }}</small>
                        </div>
                        <i class="fas fa-chevron-down ms-1 text-muted" style="font-size: 0.7rem;"></i>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" aria-labelledby="dropdownMenuButton1" style="min-width: 220px;">
                        <li class="dropdown-header py-2 px-3">
                            <div class="fw-semibold text-dark">{{ auth()->user()->name }}</div>
                            <div class="text-muted small text-truncate">{{ auth()->user()->email }}</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('user.show', ['user' => auth()->user()->id]) }}">
                                <i class="fas fa-user me-2.5 text-primary small"></i> View Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('activity-log.index') }}">
                                <i class="fas fa-history me-2.5 text-info small"></i> Activity Log
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('setting.index') }}">
                                <i class="fas fa-cog me-2.5 text-secondary small"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2.5 small"></i> Logout
                            </a>
                            <form id="navbar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</header>
