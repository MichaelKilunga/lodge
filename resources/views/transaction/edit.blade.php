@extends('template.master')
@section('title', 'Edit Booking #' . $transaction->id)
@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Edit Booking Details (#{{ $transaction->id }})</h2>
                <p class="text-muted mb-0">Update customer, room assignment, dates, or reservation status.</p>
            </div>
            <a href="{{ route('transaction.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bookings
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 py-3 px-4 shadow-sm" role="alert">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Booking Modification Form</h5>
                    <span class="badge {{ $transaction->status == 'Reservation' ? 'bg-warning text-dark' : ($transaction->status == 'Canceled' ? 'bg-danger' : 'bg-success') }}">
                        Current Status: {{ $transaction->status }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('transaction.update', $transaction->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-select" required>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $transaction->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} (ID: {{ $customer->id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Assigned Room <span class="text-danger">*</span></label>
                                <select name="room_id" class="form-select" required>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ $transaction->room_id == $room->id ? 'selected' : '' }}>
                                            Room #{{ $room->number }} - {{ $room->type->name }} ({{ App\Helpers\Helper::convertToRupiah($room->price) }}/day)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Check-In Date <span class="text-danger">*</span></label>
                                <input type="date" name="check_in" class="form-control" value="{{ old('check_in', \Carbon\Carbon::parse($transaction->check_in)->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Check-Out Date <span class="text-danger">*</span></label>
                                <input type="date" name="check_out" class="form-control" value="{{ old('check_out', \Carbon\Carbon::parse($transaction->check_out)->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Booking Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Reservation" {{ $transaction->status == 'Reservation' ? 'selected' : '' }}>Reservation</option>
                                    <option value="Paid" {{ $transaction->status == 'Paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="Checked In" {{ $transaction->status == 'Checked In' ? 'selected' : '' }}>Checked In</option>
                                    <option value="Checked Out" {{ $transaction->status == 'Checked Out' ? 'selected' : '' }}>Checked Out</option>
                                    <option value="Canceled" {{ $transaction->status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                                </select>
                            </div>

                            <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                <a href="{{ route('transaction.index') }}" class="btn btn-secondary px-4 py-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
