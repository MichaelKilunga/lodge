<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif; color: #333; }
        .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #2563eb 100%); padding: 36px 28px; text-align: center; color: #ffffff; }
        .header .logo { font-size: 1.8rem; font-weight: 800; letter-spacing: 0.5px; margin-bottom: 6px; color: #ffffff; text-decoration: none; }
        .header .tagline { font-size: 0.85rem; color: rgba(255, 255, 255, 0.75); text-transform: uppercase; letter-spacing: 1.5px; margin: 0; }
        .banner-container { width: 100%; max-height: 320px; overflow: hidden; background: #e2e8f0; text-align: center; }
        .banner-img { width: 100%; height: auto; display: block; object-fit: cover; }
        .body { padding: 36px 32px; }
        .greeting { font-size: 1.15rem; font-weight: 600; color: #1e293b; margin-bottom: 16px; }
        .headline { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 20px; line-height: 1.3; }
        .content { font-size: 1rem; color: #475569; line-height: 1.7; margin-bottom: 28px; }
        .promo-box { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 2px dashed #f59e0b; border-radius: 12px; padding: 20px; text-align: center; margin: 28px 0; }
        .promo-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: #b45309; font-weight: 700; margin-bottom: 6px; }
        .promo-code { font-size: 1.5rem; font-weight: 900; color: #92400e; font-family: monospace; letter-spacing: 2px; background: #ffffff; padding: 6px 18px; border-radius: 6px; display: inline-block; border: 1px solid #fde68a; }
        .cta-container { text-align: center; margin: 36px 0 20px; }
        .cta-btn { display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff !important; text-decoration: none; padding: 16px 38px; border-radius: 50px; font-weight: 700; font-size: 1.05rem; box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35); transition: all 0.3s; }
        .cta-btn:hover { background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%); }
        .divider { height: 1px; background: #e2e8f0; margin: 32px 0; }
        .footer { background: #f8fafc; padding: 28px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #64748b; font-size: 0.85rem; margin: 6px 0; }
        .footer .hotel-name { font-weight: 700; color: #334155; }
        .footer .small { font-size: 0.75rem; color: #94a3b8; margin-top: 14px; }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Header -->
    <div class="header">
        <div class="logo">🏨 {{ $hotelName }}</div>
        <p class="tagline">Exclusive Offer & News</p>
    </div>

    <!-- Optional Banner Image -->
    @if(!empty($campaign->banner_url))
    <div class="banner-container">
        <img src="{{ $campaign->banner_url }}" alt="{{ $campaign->headline }}" class="banner-img">
    </div>
    @endif

    <!-- Body -->
    <div class="body">
        <div class="greeting">Hello {{ $recipientName }},</div>
        
        <h1 class="headline">{{ $campaign->headline }}</h1>

        <div class="content">
            {!! nl2br(e($campaign->content)) !!}
        </div>

        <!-- Optional Discount Code Box -->
        @if(!empty($campaign->discount_code))
        <div class="promo-box">
            <div class="promo-label">🎁 Exclusive Promotional Code</div>
            <div class="promo-code">{{ $campaign->discount_code }}</div>
        </div>
        @endif

        <!-- Call to Action Button -->
        @if(!empty($campaign->cta_text) && !empty($campaign->cta_url))
        <div class="cta-container">
            <a href="{{ $campaign->cta_url }}" target="_blank" class="cta-btn">
                {{ $campaign->cta_text }} &rarr;
            </a>
        </div>
        @endif

        <div class="divider"></div>

        <p style="font-size: 0.9rem; color: #64748b; margin: 0;">
            We look forward to welcoming you soon.<br>
            <strong>Warm Regards,</strong><br>
            The {{ $hotelName }} Team
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p class="hotel-name">{{ $hotelName }}</p>
        <p>If you have any questions, reply directly to this email or reach us at <a href="mailto:{{ $contactEmail }}" style="color: #2563eb;">{{ $contactEmail }}</a>.</p>
        <p class="small">
            You received this email because you are a valued client or subscriber of {{ $hotelName }}.<br>
            &copy; {{ date('Y') }} {{ $hotelName }}. All rights reserved.
        </p>
    </div>
</div>
</body>
</html>
