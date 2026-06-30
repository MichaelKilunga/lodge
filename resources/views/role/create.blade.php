@extends('template.master')
@section('title', 'Create Role')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0"><i class="fas fa-plus-circle text-primary me-2"></i>Create Role</h3>
                <p class="text-muted mb-0">Define a new staff role and assign permissions</p>
            </div>
            <a href="{{ route('role.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to Roles
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('role.store') }}" method="POST">
            @csrf
            
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold">Role Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="e.g. Manager">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <input type="text" class="form-control" name="description" value="{{ old('description') }}" placeholder="Brief description of what this role does">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Assign Permissions</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label fw-semibold" for="selectAll">Select All</label>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        @foreach($permissions as $group => $perms)
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light h-100">
                                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">{{ $group }}</h6>
                                    @foreach($perms as $perm)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}"
                                                {{ is_array(old('permissions')) && in_array($perm->id, old('permissions')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $perm->id }}">
                                                {{ $perm->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-grid mb-5">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-save me-2"></i>Create Role
                </button>
            </div>
        </form>
    </div>
</div>

@section('footer')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
@endsection
