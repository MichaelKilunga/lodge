@extends('template.master')
@section('title', 'Payment')
@section('content')

    <div class="card shadow-sm border">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Room</th>
                        <th scope="col">Paid Off</th>
                        <th scope="col">Status</th>
                        <th scope="col">At</th>
                        <th scope="col">Served By</th>
                        <th scope="col">Receipt</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <th scope="row">{{ ($payments->currentpage() - 1) * $payments->perpage() + $loop->index + 1 }}
                            </th>
                            <td>{{ $payment->transaction->room->number }}</td>
                            <td>{{ Helper::convertToRupiah($payment->price) }}</td>
                            <td>
                                <span class="badge {{ $payment->status == 'Pending' ? 'bg-warning' : 'bg-success' }}">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td>{{ Helper::dateFormatTime($payment->created_at) }}</td>
                            <td>{{ $payment->user->name }}</td>
                            <td>
                                @if($payment->receipt_image)
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#receiptModal-{{ $payment->id }}">View Receipt</button>
                                @elseif($payment->reference_number)
                                    <span class="text-muted" data-bs-toggle="tooltip" title="{{ $payment->reference_number }}">Ref: {{ Str::limit($payment->reference_number, 10) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td> 
                                <a href="{{ route('payment.invoice', $payment->id) }}">Invoice</a> 
                                @if($payment->status == 'Pending')
                                    <!-- Here we can add a form to approve payment if needed -->
                                @endif
                            </td>
                        </tr>

                        @if($payment->receipt_image)
                            <div class="modal fade" id="receiptModal-{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Payment Receipt</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="{{ asset($payment->receipt_image) }}" class="img-fluid rounded" alt="Receipt">
                                            @if($payment->reference_number)
                                                <div class="mt-3 text-start">
                                                    <strong>Reference/Message:</strong>
                                                    <p>{{ $payment->reference_number }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr class="text-center">
                            <td colspan="8">Theres no payment found on database</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
