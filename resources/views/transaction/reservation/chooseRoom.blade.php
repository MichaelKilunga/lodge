@extends('template.master')
@section('title', 'Choose Room Reservation')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
    <style>
        .wrapper {
            max-width: 400px;
        }

        .demo-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

    </style>
@endsection
@section('content')
    @include('transaction.reservation.progressbar')
    <div class="container mt-3">
        <div class="row justify-content-md-center">
            <div class="col-md-8 mt-2">
                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <h2>{{ $roomsCount }} Room Available for:</h2>
                        <p>{{ request()->input('count_person') }}
                            {{ Helper::plural('People', request()->input('count_person')) }} on
                            {{ Helper::dateFormat(request()->input('check_in')) }} to
                            {{ Helper::dateFormat(request()->input('check_out')) }}</p>
                        <hr>
                        @if(!empty($selectedRooms) && $selectedRooms->count() > 0)
                            <div class="card mb-4 border-primary bg-light">
                                <div class="card-body">
                                    <h5 class="fw-bold text-primary mb-3"><i class="fas fa-check-circle me-2"></i>Selected Rooms</h5>
                                    <div class="row g-2">
                                        @foreach($selectedRooms as $sr)
                                            <div class="col-md-6">
                                                <div class="border rounded p-2 bg-white d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong class="d-block">Room {{ $sr->number }}</strong>
                                                        <small class="text-muted">{{ $sr->type->name }} (Cap: {{ $sr->capacity }})</small>
                                                    </div>
                                                    @php
                                                        $remainingRoomIds = array_diff(explode(',', $selectedRoomsString), [$sr->id]);
                                                        $removeUrl = route('transaction.reservation.chooseRoom', array_merge(
                                                            request()->except(['selected_rooms', 'page']),
                                                            [
                                                                'customer' => $customer->id,
                                                                'selected_rooms' => implode(',', $remainingRoomIds)
                                                            ]
                                                        ));
                                                    @endphp
                                                    <a href="{{ $removeUrl }}" class="btn btn-sm btn-outline-danger py-1 px-2"><i class="fas fa-times"></i></a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 d-flex justify-content-between align-items-center">
                                        <span>
                                            Total Capacity: <strong>{{ $currentCapacity }}</strong> / {{ request()->input('count_person') }} Guests
                                        </span>
                                        @php
                                            $confirmUrl = route('transaction.reservation.confirmation', [
                                                'customer' => $customer->id,
                                                'room' => $selectedRoomsString,
                                                'from' => request()->input('check_in'),
                                                'to' => request()->input('check_out')
                                            ]);
                                        @endphp
                                        <a href="{{ $confirmUrl }}" class="btn btn-success px-4 fw-bold">Proceed to Confirmation <i class="fas fa-arrow-right ms-2"></i></a>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif
                        <form method="GET"
                            action="{{ route('transaction.reservation.chooseRoom', ['customer' => $customer->id]) }}"
                            class="mb-4 bg-light p-3 rounded border shadow-sm">
                            <input type="hidden" name="count_person" value="{{ request()->input('count_person') }}">
                            <input type="hidden" name="check_in" value="{{ request()->input('check_in') }}">
                            <input type="hidden" name="check_out" value="{{ request()->input('check_out') }}">
                            <input type="hidden" name="selected_rooms" value="{{ $selectedRoomsString }}">

                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="search" class="form-label small fw-bold text-secondary mb-1">Search Room</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Room # or Type..." value="{{ request()->input('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="type_id" class="form-label small fw-bold text-secondary mb-1">Room Type</label>
                                    <select class="form-select" id="type_id" name="type_id">
                                        <option value="">All Types</option>
                                        @foreach($types as $t)
                                            <option value="{{ $t->id }}" @if(request()->input('type_id') == $t->id) selected @endif>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="sort_name" class="form-label small fw-bold text-secondary mb-1">Sort By</label>
                                    <select class="form-select" id="sort_name" name="sort_name">
                                        <option value="Number" @if (request()->input('sort_name', 'Number') == 'Number') selected @endif>Number</option>
                                        <option value="Price" @if (request()->input('sort_name') == 'Price') selected @endif>Price</option>
                                        <option value="Capacity" @if (request()->input('sort_name') == 'Capacity') selected @endif>Capacity</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="sort_type" class="form-label small fw-bold text-secondary mb-1">Order</label>
                                    <select class="form-select" id="sort_type" name="sort_type">
                                        <option value="ASC" @if (request()->input('sort_type', 'ASC') == 'ASC') selected @endif>Ascending</option>
                                        <option value="DESC" @if (request()->input('sort_type') == 'DESC') selected @endif>Descending</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2 mt-2 align-items-center">
                                <div class="col-md-4">
                                    <label for="per_page" class="form-label small fw-bold text-secondary mb-1">Display Quantity</label>
                                    <select class="form-select" id="per_page" name="per_page">
                                        <option value="12" @if (request()->input('per_page', '12') == '12') selected @endif>12 Rooms per page</option>
                                        <option value="25" @if (request()->input('per_page') == '25') selected @endif>25 Rooms per page</option>
                                        <option value="50" @if (request()->input('per_page') == '50') selected @endif>50 Rooms per page</option>
                                        <option value="100" @if (request()->input('per_page') == '100') selected @endif>100 Rooms per page</option>
                                        <option value="all" @if (request()->input('per_page') == 'all') selected @endif>All Vacant Rooms</option>
                                    </select>
                                </div>
                                <div class="col-md-8 d-flex justify-content-end align-items-end gap-2 mt-3">
                                    <a href="{{ route('transaction.reservation.chooseRoom', ['customer' => $customer->id, 'count_person' => request()->input('count_person'), 'check_in' => request()->input('check_in'), 'check_out' => request()->input('check_out'), 'selected_rooms' => $selectedRoomsString]) }}" class="btn btn-outline-secondary px-3" title="Reset Filters"><i class="fas fa-undo me-1"></i> Reset</a>
                                    <button type="submit" class="btn myBtn shadow-sm px-4"><i class="fas fa-filter me-1"></i> Apply Filters</button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            @forelse ($rooms as $room)
                                <div class="col-lg-12">
                                    <div
                                        class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                                        <div class="col p-4 d-flex flex-column position-static">
                                            <strong class="d-inline-block mb-2 text-secondary">{{ $room->capacity }}
                                                {{ Str::plural('Person', $room->capacity) }}</strong>
                                            <h3 class="mb-0">{{ $room->number }} ~ {{ $room->type->name }}</h3>
                                            <div class="mb-1 text-muted">{{ Helper::convertToRupiah($room->price) }} /
                                                Day
                                            </div>
                                            <div class="wrapper">
                                                <p class="card-text mb-auto demo-1">{{ $room->view }}</p>
                                            </div>
                                            @php
                                                $nextSelectedRooms = $selectedRoomsString ? $selectedRoomsString . ',' . $room->id : $room->id;
                                                $chooseUrl = route('transaction.reservation.chooseRoom', array_merge(
                                                    request()->except(['selected_rooms', 'page']),
                                                    [
                                                        'customer' => $customer->id,
                                                        'selected_rooms' => $nextSelectedRooms
                                                    ]
                                                ));
                                            @endphp
                                            <a href="{{ $chooseUrl }}"
                                                class="btn myBtn shadow-sm border w-100 m-2">Choose</a>
                                        </div>
                                        <div class="col-auto d-none d-lg-block">
                                            <img src="{{ $room->firstImage() }}" width="200" height="250" alt="">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <h3>There are no available rooms for the selected dates.</h3>
                            @endforelse
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                {{ $rooms->onEachSide(1)->appends(request()->all())->links('template.paginationlinks') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="card shadow-sm">
                    <img src="{{ $customer->user->getAvatar() }}"
                        style="border-top-right-radius: 0.5rem; border-top-left-radius: 0.5rem">
                    <div class="card-body">
                        <table>
                            <tr>
                                <td style="text-align: center; width:50px">
                                    <span>
                                        <i class="fas {{ $customer->gender == 'Male' ? 'fa-male' : 'fa-female' }}">
                                        </i>
                                    </span>
                                </td>
                                <td>
                                    {{ $customer->name }}
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; ">
                                    <span>
                                        <i class="fas fa-user-md"></i>
                                    </span>
                                </td>
                                <td>{{ $customer->job }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: center; ">
                                    <span>
                                        <i class="fas fa-birthday-cake"></i>
                                    </span>
                                </td>
                                <td>
                                    {{ $customer->birthdate }}
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; ">
                                    <span>
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                </td>
                                <td>
                                    {{ $customer->address }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
