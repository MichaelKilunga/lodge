@extends('template.master')
@section('title', 'My Profile')
@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold"><i class="fas fa-user-circle me-2 text-primary"></i>My Profile</h2>
            <p class="text-muted">Manage your personal information and account security.</p>
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
                    <img src="{{ $customer->user ? $customer->user->getAvatar() : asset('img/default-avatar.png') }}" 
                         class="rounded-circle w-100 h-100 object-fit-cover shadow-sm border border-3 border-primary" 
                         alt="Profile Avatar">
                </div>
                <h4 class="fw-bold mb-1">{{ $customer->name }}</h4>
                <p class="text-muted mb-2">{{ $customer->user ? $customer->user->email : '' }}</p>
                <div>
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">Customer Profile</span>
                </div>
                <hr class="text-muted my-3">
                <div class="text-start">
                    <div class="mb-2">
                        <small class="text-muted d-block">Occupation / Job</small>
                        <span class="fw-medium">{{ $customer->job ?: 'Not specified' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Address</small>
                        <span class="fw-medium">{{ $customer->address ?: 'Not specified' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Phone Number</small>
                        <span class="fw-medium">{{ $customer->user && $customer->user->phone ? $customer->user->phone : 'Not specified' }}</span>
                    </div>
                    @if($customer->birthdate)
                    <div class="mb-2">
                        <small class="text-muted d-block">Birth Date</small>
                        <span class="fw-medium">{{ \Carbon\Carbon::parse($customer->birthdate)->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Card: Edit Tabs --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom p-3">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold px-4" id="edit-info-tab" data-bs-toggle="tab" data-bs-target="#edit-info" type="button" role="tab" aria-controls="edit-info" aria-selected="true">
                                <i class="fas fa-edit me-2"></i>Personal Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold px-4" id="change-pwd-tab" data-bs-toggle="tab" data-bs-target="#change-pwd" type="button" role="tab" aria-controls="change-pwd" aria-selected="false">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabContent">
                        {{-- Tab 1: Personal Details --}}
                        <div class="tab-pane fade show active" id="edit-info" role="tabpanel" aria-labelledby="edit-info-tab">
                            <form action="{{ route('user.updateProfile', $customer->user ? $customer->user->id : auth()->id()) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $customer->user ? $customer->user->email : '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Occupation / Job</label>
                                        <input type="text" name="job" class="form-control" placeholder="e.g. Engineer / Entrepreneur" value="{{ old('job', $customer->job) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Address / Phone Number</label>
                                        <input type="text" name="address" class="form-control" placeholder="Street or Phone contact" value="{{ old('address', $customer->address) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Birth Date</label>
                                        <input type="date" name="birthdate" class="form-control" value="{{ old('birthdate', $customer->birthdate) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Gender</label>
                                        <select name="gender" class="form-select">
                                            <option value="Male" {{ old('gender', $customer->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $customer->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
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
                        <div class="tab-pane fade" id="change-pwd" role="tabpanel" aria-labelledby="change-pwd-tab">
                            <form action="{{ route('user.updateProfile', $customer->user ? $customer->user->id : auth()->id()) }}" method="POST">
                                @csrf
                                <input type="hidden" name="name" value="{{ $customer->name }}">
                                <input type="hidden" name="email" value="{{ $customer->user ? $customer->user->email : '' }}">
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
