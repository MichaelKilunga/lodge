@extends('template.master')
@section('title', 'Roles & Permissions')
@section('content')
    <style>
        .role-hub-container {
            font-family: 'Inter', sans-serif;
        }

        .text-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        /* Role Permission Cards */
        .role-security-card {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.03), 0 2px 8px -2px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .role-security-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px -10px rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.25);
        }

        /* Security card decoration bar */
        .role-security-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%);
        }

        .role-security-card.system-role::before {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }

        .role-card-header {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .role-card-body {
            padding: 0 1.5rem 1.5rem 1.5rem;
            flex-grow: 1;
        }

        .role-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .role-description {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.5;
            min-height: 42px;
        }

        .permissions-count-box {
            background: rgba(99, 102, 241, 0.06);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(99, 102, 241, 0.1);
        }

        .system-role .permissions-count-box {
            background: rgba(245, 158, 11, 0.05);
            border-color: rgba(245, 158, 11, 0.1);
        }

        .role-card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #f1f5f9;
            background-color: #fafbfc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Action buttons layout */
        .circle-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.2s;
        }

        .circle-action-btn:hover {
            background: #4f46e5;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2);
            border-color: #4f46e5;
        }

        .circle-action-btn.btn-delete-hover:hover {
            background: #ef4444;
            border-color: #ef4444;
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.2);
        }
    </style>

    <div class="role-hub-container fade-in">
        <div class="row align-items-center mb-4">
            <div class="col-md-6 col-sm-12">
                <h1 class="h3 text-gradient mb-1">Access Control Roles</h1>
                <p class="text-muted mb-0 small">Define role levels, sync specific operational permissions, and audit staff security profiles</p>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end text-start mt-3 mt-md-0">
                <a href="{{ route('role.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                    <i class="fas fa-plus-circle me-2"></i> Create Role
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('failed'))
            <div class="alert alert-danger alert-dismissible fade show border-0 mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('failed') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            @foreach($roles as $role)
                @php
                    $isSystem = $role->is_system;
                @endphp
                <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
                    <div class="role-security-card {{ $isSystem ? 'system-role' : '' }}">
                        
                        <!-- Top Header -->
                        <div class="role-card-header">
                            <div>
                                @if($isSystem)
                                    <span class="badge bg-light text-warning border border-warning px-2.5 py-1">
                                        <i class="fas fa-cube me-1"></i> System Role
                                    </span>
                                @else
                                    <span class="badge bg-light text-primary border border-primary px-2.5 py-1">
                                        <i class="fas fa-sliders-h me-1"></i> Custom Role
                                    </span>
                                @endif
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-fingerprint"></i> ID: #{{ $role->id }}
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="role-card-body">
                            <h4 class="role-title">{{ $role->name }}</h4>
                            <p class="role-description">{{ $role->description ?? 'No description provided.' }}</p>

                            <!-- Permissions Overview Box -->
                            <div class="permissions-count-box">
                                <div class="small fw-semibold text-secondary">
                                    <i class="fas fa-key me-2 text-indigo"></i> Permissions
                                </div>
                                <span class="badge {{ $role->name === 'Super' ? 'bg-success' : 'bg-primary' }}">
                                    @if($role->name === 'Super')
                                        Full Access
                                    @else
                                        {{ $role->permissions->count() }} Bound
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Card Actions Footer -->
                        <div class="role-card-footer">
                            <span class="text-muted small">
                                <i class="fas fa-shield-alt text-muted me-1"></i> Security Profile
                            </span>

                            <div class="d-flex gap-2">
                                <a href="{{ route('role.edit', $role->id) }}" class="circle-action-btn" data-bs-toggle="tooltip" title="Edit Permissions">
                                    <i class="fas fa-shield-alt"></i>
                                </a>
                                @if(!$isSystem)
                                    <form action="{{ route('role.destroy', $role->id) }}" method="POST" id="delete-role-form-{{ $role->id }}" class="d-inline p-0 m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="circle-action-btn btn-delete-hover delete-role" role-id="{{ $role->id }}" role-name="{{ $role->name }}" data-bs-toggle="tooltip" title="Delete Role">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(function () {
            // Enable tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Confirm delete
            $('.delete-role').click(function() {
                var role_id = $(this).attr('role-id');
                var role_name = $(this).attr('role-name');
                
                Swal.fire({
                    title: 'Delete this role?',
                    text: 'The security role "' + role_name + '" will be permanently deleted. Staff members assigned to it will lose custom configurations.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete permanently',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-role-form-' + role_id).submit();
                    }
                });
            });
        });
    </script>
@endsection
