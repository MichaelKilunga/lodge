@extends('template.public')

@section('title', $room->type->name . ' - Room ' . $room->number)
@section('meta_description', 'Book our ' . $room->type->name . ' room. Enjoy premium amenities, ' . ($room->view ?? 'great') . ' views, and comfort for up to ' . $room->capacity . ' guests.')
@section('meta_keywords', $room->type->name . ', luxury suite, room booking, accommodation, ' . ($room->view ?? 'hotel view') . ', bella vista room')
@section('og_type', 'product')
@section('og_image', $room->firstImage())

@section('head')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "HotelRoom",
  "name": "{{ addslashes($room->type->name) }} - Room {{ $room->number }}",
  "description": "Book our {{ addslashes($room->type->name) }} room. Enjoy premium amenities, {{ $room->view ?? 'great' }} views, and comfort for up to {{ $room->capacity }} guests.",
  "image": "{{ $room->firstImage() }}",
  "url": "{{ route('public.room', $room->id) }}",
  "bed": {
    "@type": "BedDetails",
    "numberOfBeds": "1",
    "typeOfBed": "King/Twin"
  },
  "occupancy": {
    "@type": "QuantitativeValue",
    "value": {{ $room->capacity }}
  },
  "offers": {
    "@type": "Offer",
    "priceCurrency": "TZS",
    "price": "{{ $room->price }}",
    "availability": "https://schema.org/InStock",
    "url": "{{ route('public.room', $room->id) }}"
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ route('public.home') }}"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Rooms & Suites",
      "item": "{{ route('public.rooms') }}"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "{{ addslashes($room->type->name) }}",
      "item": "{{ route('public.room', $room->id) }}"
    }
  ]
}
</script>
@endsection

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
                            <button type="button" id="detail-book-btn" class="btn btn-hotel-primary btn-lg"
                                    data-room-id="{{ $room->id }}" 
                                    data-room-number="{{ $room->number }}" 
                                    data-room-capacity="{{ $room->capacity }}" 
                                    data-room-price="{{ $room->price }}" 
                                    data-room-name="{{ $room->type->name }}">
                                Book This Room
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkInVal = "{{ request()->query('check_in') }}";
        const checkOutVal = "{{ request()->query('check_out') }}";
        const guestsVal = parseInt("{{ request()->query('guests') }}") || 0;

        const bookBtn = document.getElementById('detail-book-btn');
        if (bookBtn) {
            // Initialize button state if already selected
            let selectedRooms = [];
            try {
                selectedRooms = JSON.parse(sessionStorage.getItem('selected_rooms')) || [];
            } catch(e) { selectedRooms = []; }

            const isAlreadySelected = selectedRooms.some(r => r.id === bookBtn.getAttribute('data-room-id'));
            if (isAlreadySelected) {
                bookBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selected - Go to Cart';
                bookBtn.className = "btn btn-success btn-lg";
            }

            bookBtn.addEventListener('click', function() {
                const roomId = this.getAttribute('data-room-id');
                const roomNumber = this.getAttribute('data-room-number');
                const roomCapacity = this.getAttribute('data-room-capacity');
                const roomPrice = this.getAttribute('data-room-price');
                const roomName = this.getAttribute('data-room-name');

                if (!checkInVal || !checkOutVal || guestsVal <= 0) {
                    // Redirect to rooms page to search first
                    window.location.href = "{{ route('public.rooms') }}";
                    return;
                }

                // Get latest list
                try {
                    selectedRooms = JSON.parse(sessionStorage.getItem('selected_rooms')) || [];
                } catch(e) { selectedRooms = []; }

                const idx = selectedRooms.findIndex(r => r.id === roomId);
                if (idx === -1) {
                    selectedRooms.push({
                        id: roomId,
                        number: roomNumber,
                        capacity: roomCapacity,
                        price: roomPrice,
                        name: roomName
                    });
                    sessionStorage.setItem('selected_rooms', JSON.stringify(selectedRooms));
                }

                // Calculate total capacity
                const totalCapacity = selectedRooms.reduce((sum, r) => sum + parseInt(r.capacity), 0);

                if (totalCapacity >= guestsVal) {
                    // Meet capacity: build checkout url and redirect
                    let checkoutUrl = "{{ route('public.checkout') }}?";
                    selectedRooms.forEach(room => {
                        checkoutUrl += `room_ids[]=${room.id}&`;
                    });
                    checkoutUrl += `check_in=${checkInVal}&check_out=${checkOutVal}&guests=${guestsVal}`;
                    window.location.href = checkoutUrl;
                } else {
                    // Does not meet capacity yet: redirect to rooms page to select more rooms, carrying params
                    alert(`Room ${roomNumber} added. You are booking for ${guestsVal} guests, but selected room(s) only hold ${totalCapacity} guests. Please select additional rooms.`);
                    window.location.href = "{{ route('public.rooms') }}?check_in=" + checkInVal + "&check_out=" + checkOutVal + "&guests=" + guestsVal;
                }
            });
        }
    });
</script>
@endsection
