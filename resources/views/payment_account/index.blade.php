@extends('template.master')
@section('title', 'Payment Accounts')
@section('content')
    <style>
        .bank-card-container {
            font-family: 'Inter', sans-serif;
            perspective: 1000px;
        }

        .text-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        /* Virtual Bank Card Styling */
        .virtual-bank-card {
            border-radius: 20px;
            padding: 1.75rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 16px -6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 200px;
            display: flex;
            flex-column: justify;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .virtual-bank-card:hover {
            transform: translateY(-8px) rotateY(2deg);
            box-shadow: 0 20px 35px -10px rgba(59, 130, 246, 0.25);
            border-color: rgba(59, 130, 246, 0.3);
        }

        /* Card background decorative shapes */
        .virtual-bank-card::after {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0) 70%);
            z-index: 1;
        }

        /* Card details */
        .card-chip {
            width: 42px;
            height: 32px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 6px;
            position: relative;
            box-shadow: inset 0 1px 1px rgba(255,255,255,0.4);
            margin-bottom: 1.5rem;
        }

        .card-chip::after {
            content: '';
            position: absolute;
            top: 4px;
            left: 4px;
            right: 4px;
            bottom: 4px;
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 4px;
        }

        .card-number-display {
            font-family: 'Courier New', Courier, monospace;
            font-size: 1.25rem;
            letter-spacing: 0.15em;
            word-spacing: 0.2em;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            font-weight: bold;
        }

        .card-holder-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.7;
            margin-bottom: 2px;
        }

        .card-holder-value {
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        .card-bank-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-transform: uppercase;
            margin-bottom: 0;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Floating action buttons on cards */
        .card-action-overlay {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 5;
            display: flex;
            gap: 6px;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .virtual-bank-card:hover .card-action-overlay {
            opacity: 1;
        }

        .card-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            font-size: 0.8rem;
            transition: all 0.2s;
            backdrop-filter: blur(5px);
        }

        .card-action-btn:hover {
            background: white;
            color: #1e293b;
            transform: scale(1.1);
        }

        .card-action-btn.btn-delete:hover {
            background: #ef4444;
            border-color: #ef4444;
            color: white;
        }
    </style>

    <div class="bank-card-container fade-in">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h1 class="h3 text-gradient mb-1">Billing Accounts</h1>
                <p class="text-muted mb-0 small">Manage lodge banking details, digital wallets, and payment collection outlets</p>
            </div>
            <div class="col-md-6 text-md-end text-start mt-3 mt-md-0">
                <a href="{{ route('payment-account.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                    <i class="fas fa-plus-circle me-2"></i> Add Account
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            @forelse($accounts as $index => $account)
                @php
                    // Dynamic premium gradients for each card in sequence
                    $gradients = [
                        'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',   // Blue
                        'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)',   // Slate
                        'linear-gradient(135deg, #064e3b 0%, #10b981 100%)',   // Emerald
                        'linear-gradient(135deg, #581c87 0%, #7c3aed 100%)',   // Purple
                        'linear-gradient(135deg, #781a1a 0%, #ef4444 100%)'    // Red
                    ];
                    $cardGrad = $gradients[$index % count($gradients)];
                    
                    // Space formatting for account numbers like CC formatting
                    $spacedAccountNum = implode(' ', str_split(trim($account->account_number), 4));
                @endphp
                <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
                    <div class="virtual-bank-card d-flex flex-column justify-content-between" style="background: {{ $cardGrad }};">
                        
                        <!-- Floating edit/delete controls -->
                        <div class="card-action-overlay">
                            <a href="{{ route('payment-account.edit', $account->id) }}" class="card-action-btn" data-bs-toggle="tooltip" title="Edit Details">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('payment-account.destroy', $account->id) }}" method="POST" id="delete-card-form-{{ $account->id }}" class="d-inline p-0 m-0">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="card-action-btn btn-delete delete-card" card-id="{{ $account->id }}" card-bank="{{ $account->bank_name }}" data-bs-toggle="tooltip" title="Remove Account">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Top row -->
                        <div class="d-flex justify-content-between align-items-start w-100">
                            <div class="card-chip"></div>
                            <h5 class="card-bank-title">{{ $account->bank_name }}</h5>
                        </div>

                        <!-- Middle Row: Number -->
                        <div class="card-number-display mt-3">
                            {{ $spacedAccountNum }}
                        </div>

                        <!-- Bottom row: Holder -->
                        <div class="d-flex justify-content-between align-items-end w-100">
                            <div>
                                <div class="card-holder-label">Account Name</div>
                                <div class="card-holder-value">{{ $account->account_name }}</div>
                            </div>
                            <div style="opacity: 0.65;">
                                <i class="fas fa-wifi fa-lg rotate-90"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="card border-0 shadow-sm p-5 text-muted">
                        <i class="fas fa-credit-card mb-3" style="font-size: 3.5rem; opacity: 0.3;"></i>
                        <h5 class="fw-bold text-dark">No Accounts Registered</h5>
                        <p class="mb-0">Please register a lodge bank account or digital payment wallet to continue.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(function () {
            // Enable tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Confirm delete
            $('.delete-card').click(function() {
                var card_id = $(this).attr('card-id');
                var card_bank = $(this).attr('card-bank');
                
                Swal.fire({
                    title: 'Remove payment account?',
                    text: 'Account at "' + card_bank + '" will be permanently deleted. This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-card-form-' + card_id).submit();
                    }
                });
            });
        });
    </script>
@endsection
