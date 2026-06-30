@extends('template.master')
@section('title', 'Payment Accounts')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Manage Payment Accounts</h5>
        <a href="{{ route('payment-account.create') }}" class="btn btn-primary btn-sm">Add New</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Bank Name</th>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr>
                            <td>{{ $account->bank_name }}</td>
                            <td>{{ $account->account_name }}</td>
                            <td>{{ $account->account_number }}</td>
                            <td>
                                <a href="{{ route('payment-account.edit', $account->id) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('payment-account.destroy', $account->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this account?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No payment accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
