@extends('template.public')

@section('title', 'Location & Directions | Prime Accessibility')
@section('meta_description', $global_settings['location_description'] ?? 'Discover our prime luxury location and directions. Easy access from airports with private transfers and helipad.')
@section('meta_keywords', 'hotel location, directions to bella vista lodge, arusha hotel map, tanzania lodge directions, helipad transfer, airport transfer, valet parking')

@section('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "LocalBusiness",
  "name": "{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}",
  "description": "{{ $global_settings['location_description'] ?? 'Nestled amidst breathtaking natural vistas, Bella Vista Lodge offers tranquil seclusion with effortless accessibility.' }}",
  "url": "{{ route('public.location') }}",
  "telephone": "{{ $global_settings['contact_phone'] ?? '+255 123 456 789' }}",
  "address": {
    "@@type": "PostalAddress",
    "streetAddress": "{{ $global_settings['hotel_address'] ?? '123 Luxury Way, Serengeti Estate' }}",
    "addressLocality": "Arusha",
    "addressCountry": "TZ"
  },
  "geo": {
    "@@type": "GeoCoordinates",
    "latitude": "-3.386925",
    "longitude": "36.682995"
  },
  "image": "{{ !empty($global_settings['hero_image_path']) ? asset($global_settings['hero_image_path']) : asset('img/default/default-room.png') }}",
  "priceRange": "$$$"
}
</script>
<style>
    .location-hero {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(30, 41, 59, 0.92) 100%),
                    url('{{ !empty($global_settings["hero_image_path"]) ? asset($global_settings["hero_image_path"]) : asset("img/default/default-room.png") }}') center/cover no-repeat;
        padding: 160px 0 100px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .location-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 60px;
        background: linear-gradient(to top, var(--bg-light), transparent);
    }

    .media-card {
        border-radius: 24px;
        overflow: hidden;
        background: white;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        height: 100%;
    }

    .media-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.12);
    }

    .media-card-header {
        padding: 1.75rem 2rem;
        background: linear-gradient(to right, #ffffff, #f8fafc);
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .embed-responsive-container {
        position: relative;
        width: 100%;
        padding-top: 56.25%; /* 16:9 Aspect Ratio */
        background: #0f172a;
    }

    .embed-responsive-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    .info-badge-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        height: 100%;
        transition: all 0.3s ease;
    }

    .info-badge-card:hover {
        border-color: var(--accent-color);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    }

    .info-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary-color) 0%, #334155 100%);
        color: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')
    <!-- Hero Header -->
    <header class="location-hero text-center">
        <div class="container position-relative z-1">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm text-uppercase letter-spacing">
                <i class="fas fa-compass me-1"></i> {{ $global_settings['location_badge'] ?? 'Prime Accessibility' }}
            </span>
            <h1 class="display-3 fw-bold mb-4">{{ $global_settings['location_heading'] ?? 'Discover Our Prime Luxury Location' }}</h1>
            <p class="lead mx-auto mb-0 text-white-50" style="max-width: 750px; font-size: 1.25rem; line-height: 1.8;">
                {{ $global_settings['location_description'] ?? 'Nestled amidst breathtaking natural vistas, Bella Vista Lodge offers tranquil seclusion with effortless accessibility from international airports and scenic transport hubs.' }}
            </p>
        </div>
    </header>

    <!-- Main Map & Direction Video Section -->
    <section class="py-5 my-3">
        <div class="container">
            <div class="row g-5 align-items-stretch">
                <!-- Interactive Google Map Card -->
                <div class="col-lg-6">
                    <div class="media-card d-flex flex-column">
                        <div class="media-card-header">
                            <div>
                                <h4 class="fw-bold mb-1"><i class="fas fa-map-marked-alt text-primary me-2"></i>{{ $global_settings['location_map_card_title'] ?? 'Interactive Satellite Map' }}</h4>
                                <p class="text-muted mb-0 small">Pan, zoom, and explore surrounding landmarks</p>
                            </div>
                            <span class="badge bg-light text-primary border px-3 py-2 rounded-pill fw-medium">Live GPS</span>
                        </div>
                        <div class="embed-responsive-container flex-grow-1">
                            @php
                                $mapIframe = $global_settings['location_map_iframe'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d101408.21722940251!2d-122.08554284488975!3d37.42199990176881!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fb7495bec0189%3A0x7c17d44a466baf9b!2sMountain%20View%2C%20CA!5e0!3m2!1sen!2sus!4v1700000000000!5m2!1sen!2sus';
                            @endphp
                            <iframe src="{{ $mapIframe }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="p-4 bg-light border-top d-flex align-items-center justify-content-between">
                            <span class="text-muted small"><i class="fas fa-location-arrow me-2"></i>Pinpoint lodge entrance & visitor parking</span>
                            <a href="{{ $mapIframe }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">Open Fullscreen</a>
                        </div>
                    </div>
                </div>

                <!-- Google Earth Animated Direction Video Card -->
                <div class="col-lg-6">
                    <div class="media-card d-flex flex-column">
                        <div class="media-card-header">
                            <div>
                                <h4 class="fw-bold mb-1"><i class="fas fa-video text-danger me-2"></i>{{ $global_settings['location_video_card_title'] ?? 'Google Earth 3D Animated Route' }}</h4>
                                <p class="text-muted mb-0 small">Visual flight path and approach instructions</p>
                            </div>
                            <span class="badge bg-danger text-white px-3 py-2 rounded-pill fw-medium"><i class="fas fa-play me-1"></i> 3D Video</span>
                        </div>
                        <div class="embed-responsive-container flex-grow-1">
                            @php
                                $videoUrl = $global_settings['location_youtube_video'] ?? 'https://www.youtube.com/embed/ScMzIvxBSi4';
                            @endphp
                            <iframe src="{{ $videoUrl }}" title="Google Earth Animated Direction" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                        <div class="p-4 bg-light border-top d-flex align-items-center justify-content-between">
                            <span class="text-muted small"><i class="fas fa-plane-departure me-2"></i>Scenic flight approach overview</span>
                            <a href="{{ $videoUrl }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">Watch on YouTube</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Driving & Transport Instructions -->
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container py-4">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h2 class="fw-bold mb-3">{{ $global_settings['location_info_title'] ?? 'Effortless Arrival & Connections' }}</h2>
                <p class="text-muted lead">{{ $global_settings['location_info_subtitle'] ?? 'Whether arriving by private transfer, helicopter, or scenic drive, reaching our sanctuary is part of your unforgettable journey.' }}</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="info-badge-card">
                        <div class="info-icon-wrapper">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Scenic Driving Route</h4>
                        <p class="text-muted mb-0" style="line-height: 1.7;">
                            {{ $global_settings['location_directions'] ?? 'From International Airport: Take Highway 101 North for 35 miles, follow scenic Route 9 directly to private lodge gates. Valet and private helicopter transfer available upon reservation.' }}
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-badge-card">
                        <div class="info-icon-wrapper">
                            <i class="fas fa-helicopter"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $global_settings['location_helipad_title'] ?? 'Private Helipad & Transfers' }}</h4>
                        <p class="text-muted mb-0" style="line-height: 1.7;">
                            {{ $global_settings['location_helipad_text'] ?? 'We maintain an on-site private helipad for chartered arrivals. VIP luxury SUV transfers from major airports can also be arranged directly through our concierge team 24 hours prior to arrival.' }}
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-badge-card">
                        <div class="info-icon-wrapper">
                            <i class="fas fa-parking"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $global_settings['location_parking_title'] ?? 'Valet Parking & EV Charging' }}</h4>
                        <p class="text-muted mb-0" style="line-height: 1.7;">
                            {{ $global_settings['location_parking_text'] ?? 'Complimentary 24/7 secure valet parking is extended to all registered lodge guests. Fast Level-2 and DC universal electric vehicle charging stations are available upon arrival at our main lodge portico.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-5 text-center my-4">
        <div class="container">
            <div class="p-5 rounded-4 shadow-sm text-white" style="background: linear-gradient(135deg, var(--primary-color) 0%, #334155 100%);">
                <h2 class="fw-bold mb-3">{{ $global_settings['location_cta_title'] ?? 'Ready to Experience Our Sanctuary?' }}</h2>
                <p class="lead text-white-50 mx-auto mb-4" style="max-width: 600px;">
                    {{ $global_settings['location_cta_subtitle'] ?? 'Secure your preferred dates and let our concierge prepare your customized arrival itinerary.' }}
                </p>
                <a href="{{ route('public.rooms') }}" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow">
                    Book Your Stay Now <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
@endsection
