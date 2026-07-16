@extends('template.master')
@section('title', 'Add New User')
@section('content')
    <div class="row justify-content-md-center fade-in">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h3 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Add Staff Member
                    </h3>
                    <p class="text-muted mb-0 small">Create a new administrative or operational staff profile</p>
                </div>
                <div class="card-body p-4">
                    <form class="row g-3" method="POST" action="{{ route('user.store') }}">
                        @csrf
                        <div class="col-md-12">
                            <label for="name" class="form-label fw-medium text-secondary">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required>
                            </div>
                            @error('name')
                                <div class="text-danger mt-1 small">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-medium text-secondary">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="e.g. john@example.com" required>
                            </div>
                            @error('email')
                                <div class="text-danger mt-1 small">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-medium text-secondary">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" value="{{ old('phone') }}" placeholder="e.g. 0712345678">
                            </div>
                            @error('phone')
                                <div class="text-danger mt-1 small">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="password" class="form-label fw-medium text-secondary">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                    name="password" placeholder="Create user password" required>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1 small">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="role_id" class="form-label fw-medium text-secondary">System Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-shield-alt text-muted"></i></span>
                                <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="" selected disabled hidden>Choose role...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @if (old('role_id') == $role->id) selected @endif>{{ $role->name }} - {{ $role->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role_id')
                                <div class="text-danger mt-1 small">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-12 mt-4 pt-2 d-flex justify-content-between">
                            <a href="{{ route('user.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-xs">
                                <i class="fas fa-save me-1"></i> Save User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
