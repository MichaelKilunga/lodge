@extends('template.master')
@section('title', 'Roles & Permissions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0"><i class="fas fa-shield-alt text-primary me-2"></i>Roles & Permissions</h3>
                <p class="text-muted mb-0">Manage access levels and permissions for staff</p>
            </div>
            <a href="{{ route('role.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-2"></i>Create New Role
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('failed'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('failed') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Role Name</th>
                                <th class="py-3">Description</th>
                                <th class="py-3">System Role</th>
                                <th class="py-3">Permissions</th>
                                <th class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold text-dark">{{ $role->name }}</div>
                                </td>
                                <td><span class="text-muted small">{{ $role->description }}</span></td>
                                <td>
                                    @if($role->is_system)
                                        <span class="badge bg-secondary">System Role</span>
                                    @else
                                        <span class="badge bg-info text-dark">Custom Role</span>
                                    @endif
                                </td>
                                <td>
                                    @if($role->name === 'Super')
                                        <span class="badge bg-success">All Permissions</span>
                                    @else
                                        <span class="badge bg-primary">{{ $role->permissions->count() }} Permissions</span>
                                    @endif
                                </td>
                                <td class="px-4 text-end">
                                    <a href="{{ route('role.edit', $role->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit Role">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$role->is_system)
                                    <form action="{{ route('role.destroy', $role->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this role?')" data-bs-toggle="tooltip" title="Delete Role">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
