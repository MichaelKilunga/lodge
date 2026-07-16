@extends('template.master')
@section('title', 'Lodge Payments')
@section('content')
    <div class="container-fluid fade-in">
        <div class="row align-items-center mb-4">
            <div class="col-12">
                <h1 class="h3 text-gradient mb-1">Payment Transactions</h1>
                <p class="text-muted mb-0 small">Audit invoice collections, verified guest receipts, and pending payments</p>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 8%;">#</th>
                                <th style="width: 12%;">Room</th>
                                <th style="width: 20%;">Paid Amount</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 15%;">Paid At</th>
                                <th style="width: 15%;">Served By</th>
                                <th style="width: 15%;">Receipt</th>
                                <th style="width: 10%;" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-secondary border font-monospace">
                                            {{ ($payments->currentpage() - 1) * $payments->perpage() + $loop->index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">
                                            <i class="fas fa-door-open text-muted me-1"></i>
                                            {{ $payment->transaction->room->number }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success font-monospace">
                                            {{ Helper::convertToRupiah($payment->price) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $status = $payment->status;
                                            $badgeClass = 'bg-light text-warning border border-warning';
                                            if (strcasecmp($status, 'Approved') === 0 || strcasecmp($status, 'Success') === 0 || strcasecmp($status, 'Paid') === 0) {
                                                $badgeClass = 'bg-light text-success border border-success';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-2.5 py-1">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted small font-monospace">
                                            {{ Helper::dateFormatTime($payment->created_at) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold">
                                            <i class="far fa-user text-muted me-1"></i>
                                            {{ $payment->user->name }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($payment->receipt_image)
                                            <button class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#receiptModal-{{ $payment->id }}" style="font-size: 0.78rem;">
                                                <i class="fas fa-receipt me-1"></i> View Receipt
                                            </button>
                                        @elseif($payment->reference_number)
                                            <span class="text-muted small font-monospace" data-bs-toggle="tooltip" title="{{ $payment->reference_number }}">
                                                Ref: {{ Str::limit($payment->reference_number, 12) }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end"> 
                                        <a href="{{ route('payment.invoice', $payment->id) }}" class="btn btn-sm btn-light border shadow-xs">
                                            <i class="fas fa-file-invoice-dollar text-primary me-1"></i> Invoice
                                        </a>
                                    </td>
                                </tr>

                                @if($payment->receipt_image)
                                    <div class="modal fade" id="receiptModal-{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                                <div class="modal-header bg-light border-0">
                                                    <h5 class="modal-title fw-bold"><i class="fas fa-receipt text-primary me-2"></i>Payment Receipt #{{ $payment->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center p-4">
                                                    <img src="{{ asset($payment->receipt_image) }}" class="img-fluid rounded shadow-sm border mb-3" alt="Receipt" style="max-height: 480px; object-fit: contain;">
                                                    @if($payment->reference_number)
                                                        <div class="mt-3 text-start bg-light p-3 rounded-3 border">
                                                            <strong class="text-secondary small d-block mb-1">Reference / Note:</strong>
                                                            <p class="mb-0 text-dark font-monospace">{{ $payment->reference_number }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer bg-light border-0">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-credit-card mb-3" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                        <p class="mb-0">No payment records found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
