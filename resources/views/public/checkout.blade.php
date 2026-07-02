@extends('template.public')

@section('title', 'Checkout - ' . $room->type->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h2 class="fw-bold mb-4">Complete Your Booking</h2>

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('public.checkout.process', $room->id) }}" method="POST">
                @csrf
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">1. Room Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $room->firstImage() }}" class="rounded shadow-sm me-4" alt="Room" style="width: 120px; height: 80px; object-fit: cover;">
                            <div>
                                <h4 class="fw-bold mb-1">{{ $room->type->name }}</h4>
                                <p class="text-muted mb-0">Room {{ $room->number }} &bull; Up to {{ $room->capacity }} Guests</p>
                                <p class="text-hotel-primary fw-bold mb-0">TZS {{ number_format($room->price, 2) }} / night</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="check_in" class="form-label fw-bold">Check-in Date</label>
                                <input type="date" class="form-control" id="check_in" name="check_in" min="{{ date('Y-m-d') }}" value="{{ old('check_in') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="check_out" class="form-label fw-bold">Check-out Date</label>
                                <input type="date" class="form-control" id="check_out" name="check_out" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('check_out') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="guests" class="form-label fw-bold">Number of Guests</label>
                                <input type="number" class="form-control" id="guests" name="guests" min="1" max="{{ $room->capacity }}" value="{{ old('guests', 1) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">2. Guest Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-bold">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-bold">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body text-center py-4">
                        <p class="text-muted mb-3">By clicking below, you agree to our terms and policies. A confirmation will be sent to your email.</p>
                        <button type="submit" class="btn btn-hotel-primary btn-lg px-5">Confirm Booking & Pay at Hotel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
