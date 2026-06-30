@extends('template.public')

@section('title', 'Welcome')
@section('meta_description', 'Discover the luxury and comfort of Bella Vista Lodge. Book your stay today for an
    unforgettable experience.')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">Welcome to Bella Vista Lodge</h1>
            <p class="lead mb-5">Experience luxury, comfort, and exceptional service.</p>
            <a href="{{ route('public.rooms') }}" class="btn btn-hotel-primary btn-lg px-5">Book Your Stay</a>
        </div>
    </section>

    <!-- Featured Rooms Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Our Featured Rooms</h2>
                <p class="text-muted">Handpicked accommodations for your perfect stay.</p>
            </div>

            <div class="row g-4">
                @foreach ($rooms as $room)
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm room-card">
                            <img src="{{ $room->firstImage() }}" class="card-img-top" alt="Room {{ $room->number }}"
                                style="height: 250px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold">{{ $room->type->name }}</h5>
                                <p class="card-text text-muted mb-4">
                                    <i class="fas fa-user-friends me-2"></i> Up to {{ $room->capacity }} Guests<br>
                                    <i class="fas fa-bed me-2"></i> Room {{ $room->number }}
                                </p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-hotel-primary">${{ number_format($room->price, 2) }}
                                        <small class="text-muted fs-6">/ night</small></span>
                                    <a href="{{ route('public.room', $room->id) }}" class="btn btn-outline-primary">View
                                        Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('public.rooms') }}" class="btn btn-outline-dark px-4">View All Rooms</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-5">Why Choose Us?</h2>
            <div class="row g-4">
                @foreach ($facilities as $facility)
                    <div class="col-md-4">
                        <div class="p-4">
                            <i class="{{ $facility->icon ?? 'fas fa-star' }} fa-3x text-hotel-primary mb-3"></i>
                            <h4>{{ $facility->name }}</h4>
                            <p class="text-muted">{{ $facility->detail }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
