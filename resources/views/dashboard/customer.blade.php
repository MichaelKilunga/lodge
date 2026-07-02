@extends('template.master')
@section('title', 'My Dashboard')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">My Dashboard</h2>
            <p class="text-muted">Manage your bookings and payments.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert border-0 py-3 px-4" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-left: 4px solid #10b981 !important;">
            <div class="d-flex align-items-start">
                <span class="me-3" style="font-size: 1.5rem;">🎉</span>
                <div>
                    <div class="fw-bold text-success mb-1">Booking Confirmed!</div>
                    <div>{{ session('success') }}</div>
                </div>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Next Steps Banner: shown when there are pending reservations --}}
    @if($transactions->where('status', 'Reservation')->count() > 0)
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border-left: 4px solid #2563eb !important;">
        <div class="card-body py-4">
            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i>How to Activate Your Booking</h6>
            <div class="row g-3">
                <div class="col-md-4 text-center">
                    <div class="p-3">
                        <div style="font-size: 2rem; margin-bottom: 8px;">🏦</div>
                        <div class="fw-bold mb-1">Step 1: Pay</div>
                        <div class="text-muted small">Transfer the total amount to one of the payment accounts shown on the right →</div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-3">
                        <div style="font-size: 2rem; margin-bottom: 8px;">📤</div>
                        <div class="fw-bold mb-1">Step 2: Upload Receipt</div>
                        <div class="text-muted small">Click <strong>"Pay / Upload Receipt"</strong> on your booking below and attach your payment proof.</div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-3">
                        <div style="font-size: 2rem; margin-bottom: 8px;">✅</div>
                        <div class="fw-bold mb-1">Step 3: Get Confirmed</div>
                        <div class="text-muted small">Our team will verify your receipt and activate your booking within a few hours.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">My Bookings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Room</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Total Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>Room {{ $transaction->room->number }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->check_in)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->check_out)->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge {{ $transaction->status == 'Reservation' ? 'bg-warning' : ($transaction->status == 'Canceled' ? 'bg-danger' : 'bg-success') }}">
                                                {{ $transaction->status }}
                                            </span>
                                        </td>
                                        <td>{{ App\Helpers\Helper::convertToRupiah($transaction->getTotalPrice()) }}</td>
                                        <td>
                                            @if($transaction->status == 'Reservation')
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#payModal-{{ $transaction->id }}">Pay / Upload Receipt</button>
                                                <form action="{{ route('transaction.cancel', $transaction->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">You have no bookings yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Where to Pay</h5>
                </div>
                <div class="card-body">
                    @forelse($paymentAccounts as $account)
                        <div class="mb-3 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-1"><i class="fas fa-university me-2 text-primary"></i>{{ $account->bank_name }}</h6>
                            <div class="mb-1"><strong>Account Name:</strong> {{ $account->account_name }}</div>
                            <div class="fs-5 fw-bold font-monospace">{{ $account->account_number }}</div>
                        </div>
                    @empty
                        <p class="text-muted">No payment methods configured by the administrator yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Contact Card --}}
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-headset me-2 text-primary"></i>Need Help?</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Our team is happy to assist you with your booking, payment, or any other inquiries.</p>
                    <div class="mb-2">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <a href="mailto:{{ $global_settings['contact_email'] ?? config('mail.from.address') }}" class="text-decoration-none">{{ $global_settings['contact_email'] ?? config('mail.from.address') }}</a>
                    </div>
                    @if(!empty($global_settings['contact_phone']))
                    <div class="mb-2">
                        <i class="fas fa-phone me-2 text-primary"></i>
                        <span class="text-muted">{{ $global_settings['contact_phone'] }}</span>
                    </div>
                    @endif
                    <div class="mb-2">
                        <i class="fas fa-hotel me-2 text-primary"></i>
                        <span class="text-muted">{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }} – Front Desk</span>
                    </div>
                    <div class="mt-3 p-2 rounded text-center" style="background: #eff6ff;">
                        <small class="text-primary fw-bold">
                            <i class="fas fa-clock me-1"></i>
                            Receipt verification: {{ $global_settings['receipt_verify_time'] ?? '1-2 hours' }} during business hours
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pay Modals – must live OUTSIDE the table to avoid invalid HTML (div inside tbody) --}}
@foreach($transactions as $transaction)
    @if($transaction->status == 'Reservation')
    <div class="modal fade" id="payModal-{{ $transaction->id }}" tabindex="-1" aria-labelledby="payModalLabel-{{ $transaction->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="payModalLabel-{{ $transaction->id }}">Upload Payment Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('transaction.uploadReceipt', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Receipt Image</label>
                            <input type="file" class="form-control" name="receipt_image" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference Number / Message (Optional)</label>
                            <textarea class="form-control" name="reference_number" rows="3" placeholder="Transaction ID or notes..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount Paid</label>
                            <input type="number" class="form-control" name="price" value="{{ $transaction->getTotalPrice() }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Receipt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection
