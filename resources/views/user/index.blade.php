@extends('template.master')
@section('title', 'Directory & Management')
@section('content')
    <!-- Custom Directory Theme Styles -->
    <style>
        .directory-container {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgba(248, 250, 252, 0.7) 0%, rgba(241, 245, 249, 0.5) 90%);
            border-radius: 24px;
            padding: 1.5rem;
        }

        .text-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        /* Stats Cards */
        .stat-card-glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .stat-card-glass:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px -5px rgba(59, 130, 246, 0.15);
            border-color: rgba(59, 130, 246, 0.3);
        }

        /* Nav Pills Custom Theme */
        .nav-directory {
            background: rgba(226, 232, 240, 0.6);
            padding: 0.4rem;
            border-radius: 9999px;
            display: inline-flex;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .nav-directory .nav-link {
            border-radius: 9999px;
            padding: 0.6rem 1.8rem;
            color: #475569;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            border: none !important;
        }

        .nav-directory .nav-link.active {
            background: white !important;
            color: #2563eb !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        /* View Toggle Switches */
        .btn-view-toggle {
            background: rgba(226, 232, 240, 0.6);
            border-radius: 10px;
            padding: 2px;
            display: inline-flex;
        }

        .btn-view-toggle .btn {
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            color: #64748b;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-view-toggle .btn.active {
            background: white;
            color: #2563eb;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        /* Profile Cards Grid */
        .profile-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.04);
            animation: cardFadeIn 0.5s ease-out;
        }

        .profile-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px -10px rgba(37, 99, 235, 0.15);
            border-color: rgba(37, 99, 235, 0.25);
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .profile-card.card-customer::before {
            background: linear-gradient(90deg, #10b981 0%, #047857 100%);
        }

        .profile-avatar-wrapper {
            position: relative;
            width: 72px;
            height: 72px;
            margin: 0 auto;
        }

        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .status-badge-dot {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #10b981;
            border: 2px solid white;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Role Badges Gradients */
        .badge-role-super {
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%) !important;
            color: white !important;
        }
        .badge-role-admin {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: white !important;
        }
        .badge-role-owner {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: white !important;
        }
        .badge-role-staff {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: white !important;
        }

        /* Contact Items Wrapper */
        .contact-info-list {
            background: rgba(241, 245, 249, 0.6);
            border-radius: 12px;
            padding: 0.75rem;
            margin-top: 1rem;
        }

        /* Quick Floating Actions */
        .card-actions-overlay {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 1.25rem;
            border-top: 1px solid rgba(226, 232, 240, 0.6);
            padding-top: 1rem;
        }

        .action-circle-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .action-circle-btn:hover {
            background: #2563eb;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
        }

        .action-circle-btn.btn-delete-hover:hover {
            background: #ef4444;
            border-color: #ef4444;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
        }

        /* Row Layout (List View) Styling */
        .strip-row-item {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            transition: all 0.25s;
            margin-bottom: 0.75rem;
        }

        .strip-row-item:hover {
            transform: translateX(4px);
            border-color: rgba(37, 99, 235, 0.25);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="directory-container fade-in">
        <!-- Dashboard Directory Title -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 mb-2 mb-md-0">
                <h1 class="h3 text-gradient mb-1">User & Guest Hub</h1>
                <p class="text-muted mb-0 small">Access operational staff accounts and check registered customer records</p>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end justify-content-start gap-2">
                <!-- Search Bar -->
                <form id="search-directory-form" method="GET" action="{{ route('user.index') }}" class="d-flex me-2">
                    <input type="hidden" name="tab" id="active-tab-input" value="{{ request()->input('tab', 'staff') }}">
                    <div class="input-group">
                        <input class="form-control bg-white rounded-start-pill border-end-0 px-3" type="search" 
                               placeholder="Search directory..." name="q" id="search-q"
                               value="{{ request()->input('q') }}">
                        <button class="btn btn-white border border-start-0 rounded-end-pill text-muted px-3" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <a href="{{ route('user.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold d-flex align-items-center">
                    <i class="fas fa-plus-circle me-2"></i> Add Staff
                </a>
            </div>
        </div>

        <!-- Quick Summary Cards Row -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card stat-card-glass border-0">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-4 p-3 me-3">
                            <i class="fas fa-users-cog fa-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium">Active Staff</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $users->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card stat-card-glass border-0">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-4 p-3 me-3">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium">Lodge Guests</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $customers->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card stat-card-glass border-0">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-4 p-3 me-3">
                            <i class="fas fa-concierge-bell fa-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium">Receptionists</span>
                            <h4 class="fw-bold mb-0 text-dark">
                                {{ \App\Models\User::where('role', 'Front Desk')->orWhereHas('userRole', fn($q) => $q->where('name', 'Front Desk'))->count() }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card stat-card-glass border-0">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="icon-shape bg-indigo bg-opacity-10 text-indigo rounded-4 p-3 me-3">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium">Administrators</span>
                            <h4 class="fw-bold mb-0 text-dark">
                                {{ \App\Models\User::whereIn('role', ['Super', 'Admin', 'Owner'])->orWhereHas('userRole', fn($q) => $q->whereIn('name', ['Super', 'Admin', 'Owner']))->count() }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layout Controls: Tab Switcher & View Switcher -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-2 border-bottom border-secondary border-opacity-10">
            <!-- Tabs -->
            <ul class="nav nav-directory" id="directoryTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ request()->input('tab', 'staff') == 'staff' ? 'active' : '' }}" 
                            id="staff-tab" data-bs-toggle="pill" data-bs-target="#staff-pane" type="button" role="tab" 
                            aria-controls="staff-pane" aria-selected="true" onclick="setActiveTab('staff')">
                        <i class="fas fa-user-shield me-2"></i> Staff Operators ({{ $users->total() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ request()->input('tab') == 'guest' ? 'active' : '' }}" 
                            id="guest-tab" data-bs-toggle="pill" data-bs-target="#guest-pane" type="button" role="tab" 
                            aria-controls="guest-pane" aria-selected="false" onclick="setActiveTab('guest')">
                        <i class="fas fa-users me-2"></i> Registered Guests ({{ $customers->total() }})
                    </button>
                </li>
            </ul>

            <!-- View Switcher (Grid / List) -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold d-none d-sm-inline">Layout:</span>
                <div class="btn-view-toggle">
                    <button type="button" class="btn active" id="btn-grid" onclick="setViewMode('grid')">
                        <i class="fas fa-th-large me-1"></i> Grid
                    </button>
                    <button type="button" class="btn" id="btn-list" onclick="setViewMode('list')">
                        <i class="fas fa-list me-1"></i> List
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs Content Pane -->
        <div class="tab-content" id="directoryTabContent">
            <!-- Staff Directory Pane -->
            <div class="tab-pane fade {{ request()->input('tab', 'staff') == 'staff' ? 'show active' : '' }}" id="staff-pane" role="tabpanel" aria-labelledby="staff-tab">
                
                <!-- Grid View -->
                <div id="staff-grid-view" class="row">
                    @forelse ($users as $user)
                        @php
                            $roleName = $user->userRole->name ?? $user->role;
                            $badgeClass = 'bg-secondary';
                            if (strcasecmp($roleName, 'Super') === 0) {
                                $badgeClass = 'badge-role-super';
                            } elseif (strcasecmp($roleName, 'Admin') === 0) {
                                $badgeClass = 'badge-role-admin';
                            } elseif (strcasecmp($roleName, 'Owner') === 0) {
                                $badgeClass = 'badge-role-owner';
                            } elseif (strcasecmp($roleName, 'Front Desk') === 0) {
                                $badgeClass = 'badge-role-staff';
                            }
                        @endphp
                        <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-12 mb-4">
                            <div class="card profile-card border-0 text-center p-4">
                                <div class="profile-avatar-wrapper mb-3">
                                    <img src="{{ $user->getAvatar() }}" class="profile-avatar" alt="Avatar">
                                    <span class="status-badge-dot" data-bs-toggle="tooltip" title="Account Active"></span>
                                </div>

                                <h5 class="fw-bold text-dark mb-1 text-truncate" title="{{ $user->name }}">{{ $user->name }}</h5>
                                <span class="badge {{ $badgeClass }} px-3 py-1.5 rounded-pill mb-3" style="font-size: 0.78rem;">
                                    {{ $roleName }}
                                </span>

                                <div class="contact-info-list text-start">
                                    <div class="text-break text-muted mb-2 small" title="{{ $user->email }}">
                                        <i class="fas fa-envelope me-2 text-primary"></i>{{ $user->email }}
                                    </div>
                                    <div class="text-nowrap text-muted small">
                                        <i class="fas fa-phone-alt me-2 text-success"></i>{{ $user->phone ?? 'No Phone Number' }}
                                    </div>
                                </div>

                                <div class="card-actions-overlay">
                                    <a class="action-circle-btn" href="{{ route('user.show', ['user' => $user->id]) }}" data-bs-toggle="tooltip" title="View Profile">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="action-circle-btn" href="{{ route('user.edit', ['user' => $user->id]) }}" data-bs-toggle="tooltip" title="Edit Account">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form class="d-inline p-0 m-0" method="POST" id="delete-grid-staff-{{ $user->id }}" action="{{ route('user.destroy', ['user' => $user->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="action-circle-btn btn-delete-hover delete-btn" user-id="{{ $user->id }}" user-name="{{ $user->name }}" user-role="Staff" data-bs-toggle="tooltip" title="Remove Member">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-users-cog mb-3" style="font-size: 3.5rem; opacity: 0.3;"></i>
                                <h5 class="fw-bold text-dark">No Staff Members Found</h5>
                                <p class="mb-0">Try checking your search parameters or register a new user.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- List View (Compact Strip Layout) -->
                <div id="staff-list-view" class="d-none">
                    @forelse ($users as $user)
                        @php
                            $roleName = $user->userRole->name ?? $user->role;
                            $badgeClass = 'bg-secondary';
                            if (strcasecmp($roleName, 'Super') === 0) {
                                $badgeClass = 'badge-role-super';
                            } elseif (strcasecmp($roleName, 'Admin') === 0) {
                                $badgeClass = 'badge-role-admin';
                            } elseif (strcasecmp($roleName, 'Owner') === 0) {
                                $badgeClass = 'badge-role-owner';
                            } elseif (strcasecmp($roleName, 'Front Desk') === 0) {
                                $badgeClass = 'badge-role-staff';
                            }
                        @endphp
                        <div class="strip-row-item p-3">
                            <div class="row align-items-center">
                                <div class="col-md-3 col-sm-6 mb-2 mb-md-0 d-flex align-items-center">
                                    <img src="{{ $user->getAvatar() }}" class="rounded-circle me-3 border shadow-xs" width="40" height="40" alt="Avatar">
                                    <div class="text-truncate">
                                        <div class="fw-bold text-dark text-truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                                        <small class="text-muted">ID: #{{ $user->id }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                                    <div class="text-break text-muted small"><i class="fas fa-envelope me-2 text-primary"></i>{{ $user->email }}</div>
                                    <div class="text-nowrap text-muted small mt-1"><i class="fas fa-phone-alt me-2 text-success"></i>{{ $user->phone ?? 'No Phone' }}</div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                    <span class="badge {{ $badgeClass }} px-3 py-1.5 rounded-pill">{{ $roleName }}</span>
                                </div>
                                <div class="col-md-2 col-sm-6 text-md-end text-start">
                                    <div class="d-inline-flex gap-2">
                                        <a class="action-circle-btn" href="{{ route('user.show', ['user' => $user->id]) }}" data-bs-toggle="tooltip" title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a class="action-circle-btn" href="{{ route('user.edit', ['user' => $user->id]) }}" data-bs-toggle="tooltip" title="Edit Staff">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form class="d-inline p-0 m-0" method="POST" id="delete-list-staff-{{ $user->id }}" action="{{ route('user.destroy', ['user' => $user->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="action-circle-btn btn-delete-hover delete-btn" user-id="{{ $user->id }}" user-name="{{ $user->name }}" user-role="Staff" data-bs-toggle="tooltip" title="Remove Member">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">No staff members found matching search parameters.</div>
                    @endforelse
                </div>

                <!-- Footer Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->onEachSide(1)->appends(['customers' => $customers->currentPage(), 'tab' => 'staff', 'q' => request()->input('q')])->links('template.paginationlinks') }}
                </div>
            </div>

            <!-- Guests Directory Pane -->
            <div class="tab-pane fade {{ request()->input('tab') == 'guest' ? 'show active' : '' }}" id="guest-pane" role="tabpanel" aria-labelledby="guest-tab">
                
                <!-- Grid View -->
                <div id="guest-grid-view" class="row">
                    @forelse ($customers as $user)
                        <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-12 mb-4">
                            <div class="card profile-card card-customer border-0 text-center p-4">
                                <div class="profile-avatar-wrapper mb-3">
                                    <img src="{{ $user->getAvatar() }}" class="profile-avatar" alt="Avatar">
                                </div>

                                <h5 class="fw-bold text-dark mb-1 text-truncate" title="{{ $user->name }}">{{ $user->name }}</h5>
                                <span class="badge bg-light text-success border border-success px-3 py-1.5 rounded-pill mb-3" style="font-size: 0.78rem;">
                                    Customer Account
                                </span>

                                <div class="contact-info-list text-start">
                                    <div class="text-break text-muted mb-2 small" title="{{ $user->email }}">
                                        <i class="fas fa-envelope me-2 text-primary"></i>{{ $user->email }}
                                    </div>
                                    <div class="text-nowrap text-muted small">
                                        <i class="fas fa-phone-alt me-2 text-success"></i>{{ $user->phone ?? 'No Phone Number' }}
                                    </div>
                                </div>

                                <div class="card-actions-overlay">
                                    @if($user->customer)
                                    <a class="action-circle-btn" href="{{ route('customer.show', ['customer' => $user->customer->id]) }}" data-bs-toggle="tooltip" title="View Guest Reservation Profiles">
                                        <i class="fas fa-id-card text-success"></i>
                                    </a>
                                    @endif
                                    <a class="action-circle-btn" href="{{ route('user.edit', ['user' => $user->id]) }}" data-bs-toggle="tooltip" title="Edit Guest Details">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form class="d-inline p-0 m-0" method="POST" id="delete-grid-guest-{{ $user->id }}" action="{{ route('user.destroy', ['user' => $user->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="action-circle-btn btn-delete-hover delete-btn" user-id="{{ $user->id }}" user-name="{{ $user->name }}" user-role="Customer" data-bs-toggle="tooltip" title="Remove Customer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-users mb-3" style="font-size: 3.5rem; opacity: 0.3;"></i>
                                <h5 class="fw-bold text-dark">No Guests Found</h5>
                                <p class="mb-0">Try checking your search parameters or check guest books.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- List View (Compact Strip Layout) -->
                <div id="guest-list-view" class="d-none">
                    @forelse ($customers as $user)
                        <div class="strip-row-item p-3">
                            <div class="row align-items-center">
                                <div class="col-md-3 col-sm-6 mb-2 mb-md-0 d-flex align-items-center">
                                    <img src="{{ $user->getAvatar() }}" class="rounded-circle me-3 border shadow-xs" width="40" height="40" alt="Avatar">
                                    <div class="text-truncate">
                                        <div class="fw-bold text-dark text-truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                                        <small class="text-muted">ID: #{{ $user->id }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                                    <div class="text-break text-muted small"><i class="fas fa-envelope me-2 text-primary"></i>{{ $user->email }}</div>
                                    <div class="text-nowrap text-muted small mt-1"><i class="fas fa-phone-alt me-2 text-success"></i>{{ $user->phone ?? 'No Phone' }}</div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                    <span class="badge bg-light text-success border border-success px-3 py-1.5 rounded-pill">Customer</span>
                                </div>
                                <div class="col-md-2 col-sm-6 text-md-end text-start">
                                    <div class="d-inline-flex gap-2">
                                        @if($user->customer)
                                        <a class="action-circle-btn" href="{{ route('customer.show', ['customer' => $user->customer->id]) }}" data-bs-toggle="tooltip" title="View Guest Details">
                                            <i class="fas fa-id-card text-success"></i>
                                        </a>
                                        @endif
                                        <a class="action-circle-btn" href="{{ route('user.edit', ['user' => $user->id]) }}" data-bs-toggle="tooltip" title="Edit Guest">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form class="d-inline p-0 m-0" method="POST" id="delete-list-guest-{{ $user->id }}" action="{{ route('user.destroy', ['user' => $user->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="action-circle-btn btn-delete-hover delete-btn" user-id="{{ $user->id }}" user-name="{{ $user->name }}" user-role="Customer" data-bs-toggle="tooltip" title="Remove Customer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">No guests found matching search parameters.</div>
                    @endforelse
                </div>

                <!-- Footer Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $customers->onEachSide(1)->appends(['users' => $users->currentPage(), 'tab' => 'guest', 'q' => request()->input('q')])->links('template.paginationlinks') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        // Setup initial view mode from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('directory_view_mode') || 'grid';
            setViewMode(savedView);
            
            // Enable tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // SweetAlert confirmations for deletes
            $('.delete-btn').click(function() {
                var user_id = $(this).attr('user-id');
                var user_name = $(this).attr('user-name');
                var user_role = $(this).attr('user-role');
                const activeTab = document.getElementById('active-tab-input').value;
                const viewMode = localStorage.getItem('directory_view_mode') || 'grid';
                
                Swal.fire({
                    title: 'Delete this account?',
                    text: 'User "' + user_name + '" will be permanently deleted. This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete permanently',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (user_role === "Customer") {
                            if (viewMode === 'grid') {
                                $('#delete-grid-guest-' + user_id).submit();
                            } else {
                                $('#delete-list-guest-' + user_id).submit();
                            }
                        } else {
                            if (viewMode === 'grid') {
                                $('#delete-grid-staff-' + user_id).submit();
                            } else {
                                $('#delete-list-staff-' + user_id).submit();
                            }
                        }
                    }
                });
            });
        });

        // Set active tab in search form helper
        function setActiveTab(tabName) {
            document.getElementById('active-tab-input').value = tabName;
            // Append tab parameter to pagination links dynamically if needed, or update URL state
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        }

        // Toggle layout mode between grid and list view
        function setViewMode(mode) {
            localStorage.setItem('directory_view_mode', mode);
            
            const btnGrid = document.getElementById('btn-grid');
            const btnList = document.getElementById('btn-list');
            
            const staffGrid = document.getElementById('staff-grid-view');
            const staffList = document.getElementById('staff-list-view');
            const guestGrid = document.getElementById('guest-grid-view');
            const guestList = document.getElementById('guest-list-view');

            if (mode === 'list') {
                btnGrid.classList.remove('active');
                btnList.classList.add('active');
                
                staffGrid.classList.add('d-none');
                staffList.classList.remove('d-none');
                guestGrid.classList.add('d-none');
                guestList.classList.remove('d-none');
            } else {
                btnGrid.classList.add('active');
                btnList.classList.remove('active');
                
                staffGrid.classList.remove('d-none');
                staffList.classList.add('d-none');
                guestGrid.classList.remove('d-none');
                guestList.classList.add('d-none');
            }
        }
    </script>
@endsection
