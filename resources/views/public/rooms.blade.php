@extends('template.public')

@section('title', 'Our Rooms')
@section('meta_description', 'Browse our wide selection of luxury rooms and suites. Find the perfect accommodation for your stay.')

@section('content')
    @php
        $roomsHeroImage = !empty($global_settings['rooms_hero_image_path'])
                            ? asset($global_settings['rooms_hero_image_path'])
                            : (!empty($global_settings['hero_image_path'])
                                ? asset($global_settings['hero_image_path'])
                                : asset('img/default/default-room.png'));
    @endphp
    <!-- Header -->
    <section class="bg-dark text-white py-5 text-center" style="background: linear-gradient(rgba(15,23,42,0.7), rgba(15,23,42,0.7)), url('{{ $roomsHeroImage }}') center/cover fixed; padding: 120px 0 !important;">
        <div class="container">
            <h1 class="display-4 fw-bold">{{ $global_settings['rooms_hero_title'] ?? 'Our Rooms & Suites' }}</h1>
            <p class="lead">{{ $global_settings['rooms_hero_subtitle'] ?? 'Find your perfect sanctuary.' }}</p>
        </div>
    </section>

    <!-- Room List -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                @forelse($rooms as $room)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm room-card">
                            <img src="{{ $room->firstImage() }}" class="card-img-top" alt="Room {{ $room->number }}" style="height: 250px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold">{{ $room->type->name }}</h5>
                                <p class="card-text text-muted mb-4">
                                    <i class="fas fa-user-friends me-2"></i> Capacity: {{ $room->capacity }}<br>
                                    <i class="fas fa-eye me-2"></i> View: {{ $room->view ?? 'City View' }}
                                </p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-hotel-primary">${{ number_format($room->price, 2) }} <small class="text-muted fs-6">/ night</small></span>
                                    <a href="{{ route('public.room', $room->id) }}" class="btn btn-outline-primary">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <h3 class="text-muted">No rooms available at the moment.</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
