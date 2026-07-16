@extends('template.master')
@section('title', 'Create Identity')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
@endsection
@section('content')
    @include('transaction.reservation.progressbar')
    <div class="container mt-3">
        <div class="row justify-content-md-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border">
                    <div class="card-header">
                        <h2>Add Customer</h2>
                    </div>
                    <div class="card-body p-3">
                        <form class="row g-3" method="POST" action="{{ route('transaction.reservation.storeCustomer') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            @php
                                $hasOptionalErrors = $errors->has('birthdate') || $errors->has('gender') || $errors->has('job') || $errors->has('address') || $errors->has('avatar');
                            @endphp

                            <!-- Toggle Link for Optional Fields -->
                            <div class="col-md-12 mt-4">
                                <a class="text-primary fw-bold" data-bs-toggle="collapse" href="#optionalFields" role="button" aria-expanded="{{ $hasOptionalErrors ? 'true' : 'false' }}" aria-controls="optionalFields" style="text-decoration: none;">
                                    <i class="fas fa-plus-circle me-1"></i> More fields (optional)
                                </a>
                            </div>

                            <!-- Collapsible Container for Optional Fields -->
                            <div class="collapse {{ $hasOptionalErrors ? 'show' : '' }} col-md-12" id="optionalFields">
                                <div class="row g-3 pt-2">
                                    <div class="col-md-12">
                                        <label for="birthdate" class="form-label">Date of birth</label>
                                        <input type="date" class="form-control @error('birthdate') is-invalid @enderror"
                                            id="birthdate" name="birthdate" value="{{ old('birthdate') }}">
                                        @error('birthdate')
                                            <div class="text-danger mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" aria-label="Default select example">
                                            <option value="" selected>Select Gender (Optional)</option>
                                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender')
                                            <div class="text-danger mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label for="job" class="form-label">Job</label>
                                        <input type="text" class="form-control @error('job') is-invalid @enderror" id="job"
                                            name="job" value="{{ old('job') }}">
                                        @error('job')
                                            <div class="text-danger mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                                            rows="3">{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="text-danger mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="avatar" class="form-label">Profile Picture</label>
                                        <input class="form-control @error('avatar') is-invalid @enderror" type="file" name="avatar" id="avatar">
                                        @error('avatar')
                                            <div class="text-danger mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn myBtn shadow-sm border float-end">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
