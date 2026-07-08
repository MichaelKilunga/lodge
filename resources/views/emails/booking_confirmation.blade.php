<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation – Bella Vista Lodge</title>
    <style>
        body { margin: 0; padding: 0; background: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif; color: #333; }
        .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); padding: 40px 32px; text-align: center; }
        .header .logo { font-size: 2rem; color: #fff; margin-bottom: 8px; }
        .header h1 { color: #ffffff; font-size: 1.5rem; margin: 0; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.8); margin: 6px 0 0; font-size: 0.95rem; }
        .badge { display: inline-block; background: #22c55e; color: #fff; padding: 4px 16px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-top: 12px; }
        .body { padding: 32px; }
        .greeting { font-size: 1.1rem; margin-bottom: 8px; }
        .intro { color: #555; line-height: 1.6; margin-bottom: 24px; }
        .section-title { font-size: 1rem; font-weight: 700; color: #1e3a5f; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px; margin: 28px 0 14px; }
        .booking-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; }
        .booking-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e9ecef; font-size: 0.9rem; }
        .booking-row:last-child { border-bottom: none; }
        .booking-row .label { color: #6b7280; }
        .booking-row .value { font-weight: 600; color: #1f2937; }
        .steps { counter-reset: step; margin: 0; padding: 0; list-style: none; }
        .step { display: flex; align-items: flex-start; margin-bottom: 16px; }
        .step-num { width: 32px; height: 32px; background: #2563eb; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0; margin-right: 14px; }
        .step-content strong { display: block; color: #1f2937; margin-bottom: 2px; }
        .step-content span { color: #6b7280; font-size: 0.875rem; line-height: 1.5; }
        .credentials-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 18px 20px; }
        .cred-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 0.9rem; }
        .cred-row .label { color: #4b5563; }
        .cred-row .value { font-weight: 700; color: #1d4ed8; font-family: monospace; }
        .account-card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px 18px; margin-bottom: 10px; }
        .account-card .bank { font-weight: 700; color: #15803d; font-size: 1rem; }
        .account-card .detail { color: #374151; font-size: 0.875rem; margin-top: 4px; }
        .account-card .number { font-family: monospace; font-size: 1.1rem; font-weight: 700; color: #1f2937; margin-top: 2px; }
        .alert-box { background: #fefce8; border: 1px solid #fde047; border-radius: 8px; padding: 14px 18px; margin-bottom: 10px; font-size: 0.875rem; color: #713f12; }
        .login-btn { display: block; text-align: center; background: #2563eb; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 700; font-size: 1rem; margin: 20px 0; }
        .contact-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 18px; }
        .contact-box p { margin: 4px 0; font-size: 0.875rem; color: #374151; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #9ca3af; font-size: 0.8rem; margin: 4px 0; }
        .warning { color: #dc2626; font-weight: 600; font-size: 0.8rem; margin-top: 6px; display: block; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Header -->
    <div class="header">
        <div class="logo">🏨</div>
        <h1>Booking Confirmed!</h1>
        <p>Bella Vista Lodge – Your stay is reserved</p>
        <span class="badge">✓ Reservation Received</span>
    </div>

    @php
        $firstTransaction = $transactions->first();
        $grandTotal = $transactions->sum(fn($t) => $t->getTotalPrice());
    @endphp
    <!-- Body -->
    <div class="body">
        <p class="greeting">Dear <strong>{{ $firstTransaction->customer->name }}</strong>,</p>
        <p class="intro">
            Thank you for choosing <strong>Bella Vista Lodge</strong>! Your room reservation(s) have been successfully received.
            To <strong>activate and confirm</strong> your booking, please complete payment using the accounts listed below and upload your proof of payment to our guest portal.
        </p>

        <!-- Booking Details -->
        <div class="section-title">📋 Booking Details</div>
        @foreach($transactions as $transaction)
        <div class="booking-card" style="margin-bottom: 12px;">
            <div class="booking-row">
                <span class="label">Booking ID</span>
                <span class="value">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="booking-row">
                <span class="label">Room</span>
                <span class="value">Room {{ $transaction->room->number }} – {{ $transaction->room->type->name ?? 'Standard' }}</span>
            </div>
            <div class="booking-row">
                <span class="label">Check-In</span>
                <span class="value">{{ \Carbon\Carbon::parse($transaction->check_in)->format('D, d M Y') }}</span>
            </div>
            <div class="booking-row">
                <span class="label">Check-Out</span>
                <span class="value">{{ \Carbon\Carbon::parse($transaction->check_out)->format('D, d M Y') }}</span>
            </div>
            <div class="booking-row">
                <span class="label">Duration</span>
                <span class="value">{{ \Carbon\Carbon::parse($transaction->check_in)->diffInDays($transaction->check_out) }} Night(s)</span>
            </div>
            <div class="booking-row">
                <span class="label">Amount</span>
                <span class="value">{{ App\Helpers\Helper::convertToRupiah($transaction->getTotalPrice()) }}</span>
            </div>
            <div class="booking-row">
                <span class="label">Status</span>
                <span class="value" style="color: #f59e0b;">⏳ Pending Payment</span>
            </div>
        </div>
        @endforeach

        @if($transactions->count() > 1)
        <div class="booking-card" style="background: #eff6ff; border: 1px solid #bfdbfe; margin-top: 15px;">
            <div class="booking-row" style="font-size: 1.1rem; font-weight: bold;">
                <span class="label" style="color: #1e3a5f;">Grand Total</span>
                <span class="value" style="color: #2563eb;">{{ App\Helpers\Helper::convertToRupiah($grandTotal) }}</span>
            </div>
        </div>
        @endif

        <!-- How to Confirm -->
        <div class="section-title">✅ How to Confirm Your Booking (3 Easy Steps)</div>
        <ul class="steps">
            <li class="step">
                <div class="step-num">1</div>
                <div class="step-content">
                    <strong>Transfer Payment</strong>
                    <span>Send the total amount to any of the payment accounts listed below. Keep your transaction receipt or take a screenshot.</span>
                </div>
            </li>
            <li class="step">
                <div class="step-num">2</div>
                <div class="step-content">
                    <strong>Log In to Your Account</strong>
                    <span>Use the credentials below to log in to our guest portal at <a href="{{ config('app.url') }}/login">{{ config('app.url') }}/login</a></span>
                </div>
            </li>
            <li class="step">
                <div class="step-num">3</div>
                <div class="step-content">
                    <strong>Upload Your Receipt</strong>
                    <span>Once logged in, go to your <strong>Dashboard → My Bookings</strong> and click <em>"Pay / Upload Receipt"</em> on your booking. Our team will verify and activate your reservation within a few hours.</span>
                </div>
            </li>
        </ul>

        <!-- Login Credentials -->
        <div class="section-title">🔐 Your Login Credentials</div>
        <div class="credentials-box">
            <div class="cred-row">
                <span class="label">Login URL</span>
                <span class="value"><a href="{{ config('app.url') }}/login">{{ config('app.url') }}/login</a></span>
            </div>
            <div class="cred-row">
                <span class="label">Email</span>
                <span class="value">{{ $firstTransaction->customer->user->email }}</span>
            </div>
            <div class="cred-row">
                <span class="label">Temporary Password: </span>
                <span class="value">{{ $tempPassword }}</span>
            </div>
        </div>
        <span class="warning">⚠ Please log in and change your password after your first login for security.</span>

        <!-- Payment Accounts -->
        <div class="section-title">🏦 Payment Accounts</div>
        <div class="alert-box">
            <strong>Important:</strong> After payment, you MUST upload your receipt on the guest portal to activate your booking.
        </div>
        @foreach($paymentAccounts as $account)
        <div class="account-card">
            <div class="bank">{{ $account->bank_name }}</div>
            <div class="detail">Account Name: <strong>{{ $account->account_name }}</strong></div>
            <div class="number">{{ $account->account_number }}</div>
        </div>
        @endforeach
        @if($paymentAccounts->isEmpty())
        <p style="color:#6b7280;">Payment account details will be confirmed by our team. Please contact us for details.</p>
        @endif

        <!-- Guest Portal Button -->
        <a href="{{ config('app.url') }}/dashboard" class="login-btn">Go to My Dashboard →</a>

        <!-- Contact -->
        <div class="section-title">📞 Need Help? Contact Us</div>
        <div class="contact-box">
            <p>📧 Email: <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
            <p>📍 {{ $hotelName }} – We're happy to assist you at any time.</p>
            <p>⏰ Our team reviews uploaded receipts within {{ $receiptVerifyTime }} during business hours.</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is an automated email from <strong>{{ $hotelName }}</strong>. Please do not reply directly to this email.</p>
        <p>© {{ date('Y') }} {{ $hotelName }}. All rights reserved.</p>
    </div>

</div>
</body>
</html>
