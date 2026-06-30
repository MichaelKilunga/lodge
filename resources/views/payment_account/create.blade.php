@extends('template.master')
@section('title', 'Add Payment Account')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Add Payment Account</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payment-account.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bank Name / Provider</label>
                        <input type="text" name="bank_name" class="form-control" required placeholder="e.g., Bank of America">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Account Name</label>
                        <input type="text" name="account_name" class="form-control" required placeholder="e.g., Bella Vista Lodge">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Account Number</label>
                        <input type="text" name="account_number" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payment-account.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
