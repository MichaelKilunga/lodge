@extends('template.public')

@section('title', 'Welcome')
@section('meta_description', 'Discover the luxury and comfort of Bella Vista Lodge. Book your stay today for an
    unforgettable experience.')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">{{ $global_settings['home_hero_title'] ?? 'Welcome to ' . ($global_settings['hotel_name'] ?? 'Bella Vista Lodge') }}</h1>
            <p class="lead mb-5">{{ $global_settings['home_hero_subtitle'] ?? 'Experience luxury, comfort, and exceptional service.' }}</p>
            <a href="{{ route('public.rooms') }}" class="btn btn-hotel-primary btn-lg px-5">Book Your Stay</a>
        </div>
    </section>

    <!-- Featured Rooms Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">{{ $global_settings['home_featured_title'] ?? 'Our Featured Rooms' }}</h2>
                <p class="text-muted">{{ $global_settings['home_featured_subtitle'] ?? 'Handpicked accommodations for your perfect stay.' }}</p>
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
            <h2 class="fw-bold mb-5">{{ $global_settings['home_features_title'] ?? 'Why Choose Us?' }}</h2>
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

    <!-- Prime Location & Interactive Directions Preview -->
    <section class="py-5 bg-light border-top">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-dark text-warning px-3 py-2 rounded-pill fw-bold mb-3 text-uppercase letter-spacing">
                        <i class="fas fa-map-marker-alt me-1"></i> Interactive Directions
                    </span>
                    <h2 class="display-6 fw-bold mb-4">{{ $global_settings['location_heading'] ?? 'Discover Our Prime Luxury Location' }}</h2>
                    <p class="lead text-muted mb-4" style="line-height: 1.8;">
                        {{ $global_settings['location_description'] ?? 'Nestled amidst breathtaking natural vistas, Bella Vista Lodge offers tranquil seclusion with effortless accessibility from international airports and scenic transport hubs.' }}
                    </p>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <a href="{{ route('public.location') }}" class="btn btn-hotel-primary btn-lg rounded-pill px-5 shadow-sm">
                            <i class="fas fa-compass me-2"></i> Explore Interactive Map &amp; 3D Video
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative rounded-4 shadow-lg overflow-hidden bg-white border p-2" style="transform: rotate(1deg); transition: transform 0.3s ease;">
                        <div class="rounded-3 overflow-hidden position-relative" style="height: 350px;">
                            @php
                                $landingMap = $global_settings['location_map_iframe'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d101408.21722940251!2d-122.08554284488975!3d37.42199990176881!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fb7495bec0189%3A0x7c17d44a466baf9b!2sMountain%20View%2C%20CA!5e0!3m2!1sen!2sus!4v1700000000000!5m2!1sen!2sus';
                            @endphp
                            <iframe src="{{ $landingMap }}" width="100%" height="100%" style="border:0; pointer-events: none;" allowfullscreen="" loading="lazy"></iframe>
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(2px);">
                                <a href="{{ route('public.location') }}" class="btn btn-light btn-lg rounded-pill px-4 fw-bold shadow-lg d-flex align-items-center gap-2">
                                    <i class="fas fa-play text-danger"></i> View Google Earth Route Video
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
