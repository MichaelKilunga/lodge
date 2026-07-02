@extends('template.master')
@section('title', 'User Profile')
@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold"><i class="fas fa-user-shield me-2 text-primary"></i>Staff / Admin Profile</h2>
            <p class="text-muted">Manage account details and authentication credentials.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 py-3 px-4 shadow-sm" role="alert" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-left: 4px solid #10b981 !important;">
            <i class="fas fa-check-circle text-success me-2 fs-5"></i>
            <span class="fw-bold text-success">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 py-3 px-4 shadow-sm" role="alert">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Card: Profile Overview --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 text-center p-4">
                <div class="position-relative mx-auto mb-3" style="width: 140px; height: 140px;">
                    <img src="{{ $user->getAvatar() }}" 
                         class="rounded-circle w-100 h-100 object-fit-cover shadow-sm border border-3 border-primary" 
                         alt="Profile Avatar">
                </div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <div>
                    <span class="badge bg-dark px-3 py-2 rounded-pill mb-3">{{ $user->userRole ? $user->userRole->name : $user->role }}</span>
                </div>
            </div>
        </div>

        {{-- Right Card: Edit Tabs --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom p-3">
                    <ul class="nav nav-tabs card-header-tabs" id="userProfileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold px-4" id="staff-edit-tab" data-bs-toggle="tab" data-bs-target="#staff-edit" type="button" role="tab" aria-controls="staff-edit" aria-selected="true">
                                <i class="fas fa-edit me-2"></i>Personal Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold px-4" id="staff-pwd-tab" data-bs-toggle="tab" data-bs-target="#staff-pwd" type="button" role="tab" aria-controls="staff-pwd" aria-selected="false">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="userProfileTabContent">
                        {{-- Tab 1: Personal Details --}}
                        <div class="tab-pane fade show active" id="staff-edit" role="tabpanel" aria-labelledby="staff-edit-tab">
                            <form action="{{ route('user.updateProfile', $user->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Profile Photo / Avatar</label>
                                        <input type="file" name="avatar" class="form-control" accept="image/*">
                                        <small class="text-muted">Recommended format: JPG, PNG under 2MB.</small>
                                    </div>
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                                            <i class="fas fa-save me-2"></i>Save Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Tab 2: Change Password --}}
                        <div class="tab-pane fade" id="staff-pwd" role="tabpanel" aria-labelledby="staff-pwd-tab">
                            <form action="{{ route('user.updateProfile', $user->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">New Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="At least 6 characters" minlength="6">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password">
                                    </div>
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-warning px-4 py-2 fw-bold">
                                            <i class="fas fa-lock me-2"></i>Update Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
