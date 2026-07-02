@extends('template.public')

@section('title', $room->type->name . ' - Room ' . $room->number)
@section('meta_description', 'Book our ' . $room->type->name . ' room. Enjoy premium amenities, ' . ($room->view ?? 'great') . ' views, and comfort for up to ' . $room->capacity . ' guests.')

@section('content')
    <div class="container py-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('public.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('public.rooms') }}">Rooms</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $room->type->name }}</li>
            </ol>
        </nav>

        <div class="row g-5 mt-2">
            <!-- Room Images -->
            <div class="col-lg-7">
                <div id="roomImageCarousel" class="carousel slide shadow-sm rounded overflow-hidden" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @if(count($room->image) > 0)
                            @foreach($room->image as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ $image->getRoomImage() }}" class="d-block w-100" alt="Room image" style="height: 450px; object-fit: cover;">
                                </div>
                            @endforeach
                        @else
                            <div class="carousel-item active">
                                <img src="{{ asset('img/default/default-room.png') }}" class="d-block w-100" alt="Default Room image" style="height: 450px; object-fit: cover;">
                            </div>
                        @endif
                    </div>
                    @if(count($room->image) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#roomImageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#roomImageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Room Info & Booking Box -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-3">{{ $room->type->name }}</h2>
                        <div class="d-flex align-items-center mb-4">
                            <h3 class="fw-bold text-hotel-primary mb-0">TZS {{ number_format($room->price, 2) }}</h3>
                            <span class="text-muted ms-2">/ night</span>
                        </div>

                        <hr>

                        <ul class="list-unstyled mb-4">
                            <li class="mb-3"><i class="fas fa-hashtag text-hotel-primary me-2"></i> Room Number: <strong>{{ $room->number }}</strong></li>
                            <li class="mb-3"><i class="fas fa-user-friends text-hotel-primary me-2"></i> Capacity: <strong>Up to {{ $room->capacity }} Guests</strong></li>
                            <li class="mb-3"><i class="fas fa-eye text-hotel-primary me-2"></i> View: <strong>{{ $room->view ?? 'Standard View' }}</strong></li>
                            <li class="mb-3"><i class="fas fa-bed text-hotel-primary me-2"></i> Bed Type: <strong>Premium King/Twin</strong></li>
                            <li class="mb-3"><i class="fas fa-wifi text-hotel-primary me-2"></i> <strong>Free High-Speed Wi-Fi</strong></li>
                        </ul>

                        <div class="d-grid gap-2">
                            <a href="{{ route('public.checkout', $room->id) }}" class="btn btn-hotel-primary btn-lg">Book This Room</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
