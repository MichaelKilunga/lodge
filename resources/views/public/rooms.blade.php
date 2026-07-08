@extends('template.public')

@section('title', 'Our Rooms & Luxury Suites')
@section('meta_description', 'Browse our wide selection of luxury rooms and suites. Find the perfect accommodation for your stay.')
@section('meta_keywords', 'luxury rooms, suites booking, hotel accommodations, safari resort rooms, best lodge rates, king suites')

@section('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "ItemList",
  "name": "All Rooms & Suites at {{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}",
  "description": "Browse our complete inventory of luxury rooms and suites.",
  "url": "{{ route('public.rooms') }}",
  "numberOfItems": {{ count($rooms) }},
  "itemListElement": [
    @foreach($rooms as $index => $room)
    {
      "@@type": "ListItem",
      "position": {{ $index + 1 }},
      "item": {
        "@@type": "HotelRoom",
        "name": "{{ addslashes($room->type->name) }} (Room {{ $room->number }})",
        "description": "Luxury accommodation for up to {{ $room->capacity }} guests. View: {{ $room->view ?? 'Standard View' }}.",
        "image": "{{ $room->firstImage() }}",
        "url": "{{ route('public.room', $room->id) }}",
        "occupancy": {
          "@@type": "QuantitativeValue",
          "value": {{ $room->capacity }}
        },
        "offers": {
          "@@type": "Offer",
          "priceCurrency": "TZS",
          "price": "{{ $room->price }}",
          "availability": "https://schema.org/InStock"
        }
      }
    }{{ $index < count($rooms) - 1 ? ',' : '' }}
    @endforeach
  ]
}
</script>
<style>
    .booking-drawer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(15, 23, 42, 0.96);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        z-index: 1050;
        border-top: 3px solid var(--accent-color) !important;
        transform: translateY(100%);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        padding-bottom: env(safe-area-inset-bottom);
        box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.25);
    }
    .booking-drawer.active {
        transform: translateY(0);
    }
    .room-card {
        transition: all 0.3s ease;
        border: 2px solid transparent !important;
    }
    .room-card.selected {
        border: 2px solid var(--accent-color) !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-5px);
    }
    .select-room-btn {
        transition: all 0.2s ease;
    }
    .select-room-btn.selected {
        background-color: #22c55e !important;
        border-color: #22c55e !important;
        color: #fff !important;
    }
    .letter-spacing-1 {
        letter-spacing: 1px;
    }
    .z-index-2 {
        z-index: 2;
    }
    .mt-n5 {
        margin-top: -3rem !important;
    }
</style>
@endsection

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

    <!-- Search Form -->
    <div class="container mt-n5 mb-5 position-relative z-index-2">
        <div class="card border-0 shadow-lg search-bar-card" id="search-bar-card" style="border-radius: 16px;">
            <div class="card-body p-4">
                <form action="{{ route('public.rooms') }}" method="GET" id="searchForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="check_in" class="form-label fw-bold text-muted small"><i class="fas fa-calendar-alt me-1 text-hotel-primary"></i> Check-In Date</label>
                            <input type="date" class="form-control bg-light border-0 py-2 rounded-3 text-dark" id="check_in" name="check_in" min="{{ date('Y-m-d') }}" value="{{ $check_in }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="check_out" class="form-label fw-bold text-muted small"><i class="fas fa-calendar-check me-1 text-hotel-primary"></i> Check-Out Date</label>
                            <input type="date" class="form-control bg-light border-0 py-2 rounded-3 text-dark" id="check_out" name="check_out" min="{{ $check_in ? date('Y-m-d', strtotime($check_in . ' +1 day')) : date('Y-m-d', strtotime('+1 day')) }}" value="{{ $check_out }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="guests" class="form-label fw-bold text-muted small"><i class="fas fa-users me-1 text-hotel-primary"></i> Guests</label>
                            <input type="number" class="form-control bg-light border-0 py-2 rounded-3 text-dark" id="guests" name="guests" min="1" value="{{ $guests ?? 1 }}" required>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-hotel-primary py-2 rounded-3 text-uppercase fw-bold letter-spacing-1"><i class="fas fa-search me-1"></i> Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Room List -->
    <section class="py-5 bg-light" style="padding-bottom: 120px !important;">
        <div class="container">
            @if(session('error'))
                <div class="alert alert-danger mb-4 rounded-3">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
            @endif

            @if(!$check_in || !$check_out)
                <div class="alert alert-warning mb-5 rounded-3 d-flex align-items-center shadow-sm p-3">
                    <i class="fas fa-info-circle fa-2x me-3 text-warning"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Planning a Stay?</h6>
                        <span class="text-muted small">Please select your stay dates and guests count above to search live room availability and select rooms.</span>
                    </div>
                </div>
            @endif

            <div class="row g-4">
                @forelse($rooms as $room)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm room-card" id="room-card-{{ $room->id }}">
                            <img src="{{ $room->firstImage() }}" class="card-img-top" alt="Room {{ $room->number }}" style="height: 250px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="card-title fw-bold text-dark">{{ $room->type->name }}</h5>
                                <p class="card-text text-muted mb-4 small">
                                    <span class="d-block mb-1"><i class="fas fa-user-friends me-2 text-hotel-primary"></i> Capacity: Up to {{ $room->capacity }} Guests</span>
                                    <span class="d-block mb-1"><i class="fas fa-eye me-2 text-hotel-primary"></i> View: {{ $room->view ?? 'City View' }}</span>
                                    <span class="d-block"><i class="fas fa-door-open me-2 text-hotel-primary"></i> Room Number: {{ $room->number }}</span>
                                </p>
                                
                                <div class="mt-auto pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fs-5 fw-bold text-hotel-primary">TZS {{ number_format($room->price, 2) }} <small class="text-muted fs-6">/ night</small></span>
                                        <a href="{{ route('public.room', $room->id) }}?check_in={{ $check_in }}&check_out={{ $check_out }}&guests={{ $guests }}" class="btn btn-sm btn-link text-decoration-none px-0 text-muted"><i class="fas fa-info-circle me-1"></i>Details</a>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary select-room-btn w-100 rounded-pill py-2 fw-semibold" 
                                            data-room-id="{{ $room->id }}" 
                                            data-room-number="{{ $room->number }}" 
                                            data-room-capacity="{{ $room->capacity }}" 
                                            data-room-price="{{ $room->price }}" 
                                            data-room-name="{{ $room->type->name }}">
                                        <i class="fas fa-plus me-2"></i>Select Room
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-bed fa-4x text-muted mb-3"></i>
                        <h3 class="text-muted">No rooms available for the selected parameters.</h3>
                        <p class="text-muted-50">Please adjust your dates or guest counts to search again.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Floating Booking Drawer -->
    <div id="booking-drawer" class="booking-drawer shadow-lg border-top">
        <div class="container-fluid container-lg py-3 px-4">
            <div class="row align-items-center g-3">
                <div class="col-md-5 d-flex flex-column justify-content-center">
                    <h6 class="fw-bold mb-1 text-white"><i class="fas fa-suitcase me-2 text-warning"></i> Selected Rooms</h6>
                    <div id="selected-rooms-list" class="text-white-50 small mb-0 text-truncate">No rooms selected</div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-1 text-white small fw-bold">
                        <span>Capacity Progress</span>
                        <span id="capacity-stats">0 / 0 Guests</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px; background: rgba(255,255,255,0.1);">
                        <div id="capacity-progress-bar" class="progress-bar bg-warning" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div id="capacity-feedback" class="small text-warning mt-1" style="font-size: 0.75rem;">Please select rooms to accommodate all guests.</div>
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-end gap-3 flex-wrap">
                    <div class="text-end text-white me-2">
                        <div class="small text-white-50">Total Est. Price</div>
                        <div class="fs-5 fw-bold text-warning" id="drawer-total-price">TZS 0.00</div>
                    </div>
                    <a href="#" id="checkout-btn" class="btn btn-warning rounded-pill px-4 py-2 text-dark fw-bold text-uppercase d-flex align-items-center gap-2 disabled" style="pointer-events: none; opacity: 0.5;">
                        <span>Checkout</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkInVal = "{{ $check_in }}";
        const checkOutVal = "{{ $check_out }}";
        const guestsVal = parseInt("{{ $guests }}") || 0;

        const checkIn = document.getElementById('check_in');
        const checkOut = document.getElementById('check_out');

        // Dynamic check_out min boundary
        if (checkIn && checkOut) {
            checkIn.addEventListener('change', function() {
                if (checkIn.value) {
                    const minDate = new Date(checkIn.value);
                    minDate.setDate(minDate.getDate() + 1);
                    const yyyy = minDate.getFullYear();
                    const mm = String(minDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(minDate.getDate()).padStart(2, '0');
                    checkOut.min = `${yyyy}-${mm}-${dd}`;
                    if (checkOut.value && checkOut.value <= checkIn.value) {
                        checkOut.value = `${yyyy}-${mm}-${dd}`;
                    }
                }
            });
        }

        // Keep selections in sessionStorage
        let selectedRooms = [];
        try {
            selectedRooms = JSON.parse(sessionStorage.getItem('selected_rooms')) || [];
        } catch(e) {
            selectedRooms = [];
        }

        // Calculate nights dynamically
        function getNights() {
            if (!checkInVal || !checkOutVal) return 1;
            const start = new Date(checkInVal);
            const end = new Date(checkOutVal);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays || 1;
        }

        const nights = getNights();

        // Update display state of rooms and drawer
        function updateUI() {
            // Remove selection styling on all cards
            document.querySelectorAll('.room-card').forEach(card => card.classList.remove('selected'));
            document.querySelectorAll('.select-room-btn').forEach(btn => {
                btn.classList.remove('selected');
                btn.innerHTML = '<i class="fas fa-plus me-2"></i>Select Room';
            });

            let totalCapacity = 0;
            let totalPrice = 0;
            let roomDetailsText = [];

            selectedRooms.forEach(room => {
                const card = document.getElementById(`room-card-${room.id}`);
                const btn = document.querySelector(`.select-room-btn[data-room-id="${room.id}"]`);

                if (card) card.classList.add('selected');
                if (btn) {
                    btn.classList.add('selected');
                    btn.innerHTML = '<i class="fas fa-check me-2"></i>Selected';
                }

                totalCapacity += parseInt(room.capacity);
                totalPrice += parseFloat(room.price) * nights;
                roomDetailsText.push(`Room ${room.number} (${room.name})`);
            });

            // Update Drawer
            const drawer = document.getElementById('booking-drawer');
            if (selectedRooms.length > 0 && checkInVal && checkOutVal && guestsVal > 0) {
                drawer.classList.add('active');
            } else {
                drawer.classList.remove('active');
            }

            const roomListContainer = document.getElementById('selected-rooms-list');
            if (roomListContainer) {
                roomListContainer.textContent = roomDetailsText.length > 0 ? roomDetailsText.join(', ') : 'No rooms selected';
            }

            const statsContainer = document.getElementById('capacity-stats');
            if (statsContainer) {
                statsContainer.textContent = `${totalCapacity} / ${guestsVal} Guests`;
            }

            const progressBar = document.getElementById('capacity-progress-bar');
            const feedback = document.getElementById('capacity-feedback');
            const checkoutBtn = document.getElementById('checkout-btn');

            if (progressBar && feedback && checkoutBtn) {
                const pct = guestsVal > 0 ? Math.min(100, (totalCapacity / guestsVal) * 100) : 0;
                progressBar.style.width = `${pct}%`;

                if (totalCapacity < guestsVal) {
                    progressBar.className = "progress-bar bg-warning";
                    feedback.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Need room capacity for <strong>${guestsVal - totalCapacity}</strong> more guest(s).`;
                    checkoutBtn.classList.add('disabled');
                    checkoutBtn.style.pointerEvents = 'none';
                    checkoutBtn.style.opacity = '0.5';
                    checkoutBtn.href = '#';
                } else {
                    progressBar.className = "progress-bar bg-success";
                    feedback.innerHTML = `<i class="fas fa-check-circle me-1"></i> Guest capacity fully met! Ready to check out.`;
                    checkoutBtn.classList.remove('disabled');
                    checkoutBtn.style.pointerEvents = 'auto';
                    checkoutBtn.style.opacity = '1';
                    
                    // Build checkout url with query parameters
                    let checkoutUrl = "{{ route('public.checkout') }}?";
                    selectedRooms.forEach(room => {
                        checkoutUrl += `room_ids[]=${room.id}&`;
                    });
                    checkoutUrl += `check_in=${checkInVal}&check_out=${checkOutVal}&guests=${guestsVal}`;
                    checkoutBtn.href = checkoutUrl;
                }
            }

            const priceContainer = document.getElementById('drawer-total-price');
            if (priceContainer) {
                priceContainer.textContent = 'TZS ' + totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        // Handle Room Selection Toggles
        document.querySelectorAll('.select-room-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Force user to pick dates & guests first
                if (!checkInVal || !checkOutVal || guestsVal <= 0) {
                    alert('Please select check-in, check-out dates and guest count first.');
                    const searchCard = document.getElementById('search-bar-card');
                    if (searchCard) {
                        searchCard.scrollIntoView({ behavior: 'smooth' });
                        setTimeout(() => {
                            if (checkIn) checkIn.focus();
                        }, 800);
                    }
                    return;
                }

                const roomId = this.getAttribute('data-room-id');
                const roomNumber = this.getAttribute('data-room-number');
                const roomCapacity = this.getAttribute('data-room-capacity');
                const roomPrice = this.getAttribute('data-room-price');
                const roomName = this.getAttribute('data-room-name');

                const idx = selectedRooms.findIndex(r => r.id === roomId);
                if (idx > -1) {
                    // Remove room
                    selectedRooms.splice(idx, 1);
                } else {
                    // Add room
                    selectedRooms.push({
                        id: roomId,
                        number: roomNumber,
                        capacity: roomCapacity,
                        price: roomPrice,
                        name: roomName
                    });
                }

                sessionStorage.setItem('selected_rooms', JSON.stringify(selectedRooms));
                updateUI();
            });
        });

        // Initialize UI
        updateUI();
    });
</script>
@endsection
