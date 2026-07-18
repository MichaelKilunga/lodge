@extends('template.public')

@section('title', 'Checkout - ' . $rooms->first()->type->name . ($rooms->count() > 1 ? ' & More' : ''))

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

            <form action="{{ route('public.checkout.process') }}" method="POST">
                @csrf
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">1. Room Details</h5>
                    </div>
                    <div class="card-body">
                        @foreach($rooms as $room)
                        <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <img src="{{ $room->firstImage() }}" class="rounded shadow-sm me-4" alt="Room" style="width: 120px; height: 80px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-1">{{ $room->type->name }}</h4>
                                <p class="text-muted mb-0 small">Room {{ $room->number }} &bull; Up to {{ $room->capacity }} Guests</p>
                                <p class="text-hotel-primary fw-bold mb-0 small">TZS {{ number_format($room->price, 2) }} / night</p>
                            </div>
                        </div>
                        <input type="hidden" name="room_ids[]" value="{{ $room->id }}">
                        @endforeach

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label for="check_in" class="form-label fw-bold">Check-in Date</label>
                                <input type="date" class="form-control" id="check_in" name="check_in" value="{{ old('check_in', $check_in) }}" required readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="check_out" class="form-label fw-bold">Check-out Date</label>
                                <input type="date" class="form-control" id="check_out" name="check_out" value="{{ old('check_out', $check_out) }}" required readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="guests" class="form-label fw-bold">Number of Guests</label>
                                <input type="number" class="form-control" id="guests" name="guests" value="{{ old('guests', $guests) }}" required readonly>
                            </div>
                        </div>
                        <div class="mt-3 text-muted small bg-light p-2 rounded">
                            <i class="fas fa-info-circle text-hotel-primary me-1"></i> Dates and guest count are locked based on your room selection. To change them, please return to the <a href="{{ route('public.rooms') }}">rooms list</a>.
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 text-dark">Booking Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Rooms</span>
                            <span class="fw-bold text-dark">{{ count($rooms) }} Room(s)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Stay Duration</span>
                            <span class="fw-bold text-dark">{{ $nights }} Night(s)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Accommodated Capacity</span>
                            <span class="fw-bold text-dark">{{ $totalCapacity }} Guests</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold text-dark">Estimated Total Price</span>
                            <span class="fs-4 fw-bold text-hotel-primary">TZS {{ number_format($totalPrice, 2) }}</span>
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
                                <label for="name" class="form-label fw-bold">Full Name(Required)</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email Address(Optional)</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-bold">Phone Number(Required)</label>
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
