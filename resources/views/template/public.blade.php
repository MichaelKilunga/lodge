<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <meta name="description" content="@yield('meta_description', ($global_settings['hotel_tagline'] ?? ($global_settings['hotel_name'] ?? 'Bella Vista Lodge') . ' - The best place to stay and relax.'))">
    <meta name="keywords" content="@yield('meta_keywords', 'hotel, booking, accommodation, relax, luxury, tanzania lodge, suites, holiday getaway, bella vista')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="{{ url()->current() }}" />

    {{-- OpenGraph Cards --}}
    <meta property="og:title" content="@yield('title') - {{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}" />
    <meta property="og:description" content="@yield('meta_description', ($global_settings['hotel_tagline'] ?? 'Experience luxury, comfort, and exceptional service.'))" />
    <meta property="og:image" content="@yield('og_image', !empty($global_settings['hero_image_path']) ? asset($global_settings['hero_image_path']) : asset('img/default/default-room.png'))" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:site_name" content="{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}" />

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('title') - {{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}" />
    <meta name="twitter:description" content="@yield('meta_description', ($global_settings['hotel_tagline'] ?? 'Experience luxury, comfort, and exceptional service.'))" />
    <meta name="twitter:image" content="@yield('og_image', !empty($global_settings['hero_image_path']) ? asset($global_settings['hero_image_path']) : asset('img/default/default-room.png'))" />

    @php
        $faviconUrl = !empty($global_settings['favicon_path']) ? asset($global_settings['favicon_path']) : (!empty($global_settings['logo_path']) ? asset($global_settings['logo_path']) : asset('img/logo/sip.png'));
        $themeColor = $global_settings['primary_color'] ?? '#0f172a';
        $appName = $global_settings['hotel_name'] ?? 'Bella Vista Lodge';
    @endphp
    {{-- Favicon & PWA Icons --}}
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="{{ $themeColor }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $appName }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="{{ $appName }}">
    <meta name="msapplication-TileColor" content="{{ $themeColor }}">
    <meta name="msapplication-TileImage" content="{{ $faviconUrl }}">

    {{-- Styles --}}
    @vite('resources/sass/app.scss')

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <title>@yield('title') - {{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}</title>
    @yield('head')

    @php
        $primaryColor = $global_settings['primary_color'] ?? '#0f172a';
        $accentColor  = $global_settings['accent_color']  ?? '#d4af37';
        $heroImage    = !empty($global_settings['hero_image_path'])
                            ? asset($global_settings['hero_image_path'])
                            : asset('img/default/default-room.png');
    @endphp

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --accent-color:  {{ $accentColor }};
            --text-color:    #334155;
            --bg-light:      #f8fafc;
        }

        html { height: 100%; }

        body {
            font-family: 'Outfit', sans-serif;
            color: var(--text-color);
            background-color: var(--bg-light);
            min-height: 100%;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Playfair Display', serif;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            padding: 1rem 0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--accent-color) !important;
            letter-spacing: 1px;
        }

        .nav-link { font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.9rem; transition: color 0.3s; }
        .nav-link:hover { color: var(--accent-color) !important; }

        .hero-section {
            background: linear-gradient(rgba(15,23,42,0.7), rgba(15,23,42,0.7)),
                        url('{{ $heroImage }}') center/cover fixed;
            color: white;
            padding: 150px 0;
            text-align: center;
        }

        .btn-hotel-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-hotel-primary:hover {
            filter: brightness(0.88);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
            color: #fff;
        }

        .room-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .room-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }

        main { flex: 1 1 auto; display: block; padding-top: 72px; }

        /* Footer */
        .footer {
            flex-shrink: 0;
            width: 100%;
            background-color: var(--primary-color);
            color: #cbd5e1;
            padding: 60px 0 20px 0;
        }

        .footer h4 { color: var(--accent-color); margin-bottom: 20px; font-weight: 700; }
        .footer a { color: #94a3b8; text-decoration: none; transition: color 0.3s; }
        .footer a:hover { color: var(--accent-color); }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 40px;
            font-size: 0.9rem;
        }

        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            text-decoration: none;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
            color: white;
            box-shadow: 2px 2px 15px rgba(37,211,102,0.5);
        }
    </style>

    {{-- Schema.org LodgingBusiness Structured Data --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "LodgingBusiness",
      "name": "{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}",
      "description": "{{ $global_settings['hotel_tagline'] ?? 'Experience unparalleled luxury, comfort, and exceptional service.' }}",
      "image": "{{ !empty($global_settings['hero_image_path']) ? asset($global_settings['hero_image_path']) : asset('img/default/default-room.png') }}",
      "url": "{{ url('/') }}",
      "telephone": "{{ $global_settings['contact_phone'] ?? '+255 123 456 789' }}",
      "address": {
        "@@type": "PostalAddress",
        "streetAddress": "{{ $global_settings['hotel_address'] ?? '123 Luxury Way, Serengeti Estate' }}",
        "addressLocality": "Arusha",
        "addressCountry": "TZ"
      },
      "priceRange": "$$$",
      "starRating": {
        "@@type": "Rating",
        "ratingValue": "5"
      }
    }
    </script>
</head>


<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('public.home') }}">
                @if(!empty($global_settings['logo_path']))
                    <img src="{{ asset($global_settings['logo_path']) }}" alt="{{ $global_settings['hotel_name'] ?? 'Logo' }}" style="height: 44px; object-fit: contain;">
                @else
                    <i class="fas fa-hotel me-2 text-white"></i>{{ $global_settings['hotel_name'] ?? 'Bella Vista' }}
                @endif
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.rooms') }}">Rooms &amp; Suites</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.location') }}">Location &amp; Directions</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.blog.index') }}">Blog</a></li>
                    @auth
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-light rounded-pill px-4" href="{{ route('dashboard.index') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-light rounded-pill px-4" href="{{ route('login.index') }}">Sign In</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @if(session('success'))
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                {{-- Brand Column --}}
                <div class="col-lg-4 col-md-6">
                    <h4>
                        @if(!empty($global_settings['logo_path']))
                            <img src="{{ asset($global_settings['logo_path']) }}" alt="{{ $global_settings['hotel_name'] ?? 'Logo' }}" style="height: 40px; object-fit: contain; filter: brightness(0) invert(1);">
                        @else
                            <i class="fas fa-hotel me-2"></i>{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}
                        @endif
                    </h4>
                    <p class="mb-4">{{ $global_settings['footer_tagline'] ?? ($global_settings['hotel_tagline'] ?? 'Experience unparalleled luxury, comfort, and exceptional service. Your perfect getaway awaits.') }}</p>
                    {{-- Social Media --}}
                    <div class="d-flex gap-3 fs-5">
                        @if(!empty($global_settings['social_facebook']) && $global_settings['social_facebook'] !== '#')
                            <a href="{{ $global_settings['social_facebook'] }}" target="_blank" rel="noopener"><i class="fab fa-facebook"></i></a>
                        @endif
                        @if(!empty($global_settings['social_instagram']) && $global_settings['social_instagram'] !== '#')
                            <a href="{{ $global_settings['social_instagram'] }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(!empty($global_settings['social_twitter']) && $global_settings['social_twitter'] !== '#')
                            <a href="{{ $global_settings['social_twitter'] }}" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>
                        @endif
                        @if(!empty($global_settings['social_tiktok']))
                            <a href="{{ $global_settings['social_tiktok'] }}" target="_blank" rel="noopener"><i class="fab fa-tiktok"></i></a>
                        @endif
                        @if(!empty($global_settings['social_youtube']))
                            <a href="{{ $global_settings['social_youtube'] }}" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>
                        @endif
                        @if(!empty($global_settings['social_linkedin']))
                            <a href="{{ $global_settings['social_linkedin'] }}" target="_blank" rel="noopener"><i class="fab fa-linkedin"></i></a>
                        @endif
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="col-lg-2 col-md-6">
                    <h5 class="text-white mb-3 fw-bold">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('public.home') }}">Home</a></li>
                        <li class="mb-2"><a href="{{ route('public.rooms') }}">Our Rooms</a></li>
                        <li class="mb-2"><a href="{{ route('public.blog.index') }}">Blog</a></li>
                        @auth
                            <li class="mb-2"><a href="{{ route('dashboard.index') }}">My Dashboard</a></li>
                        @else
                            <li class="mb-2"><a href="{{ route('login.index') }}">Sign In</a></li>
                        @endauth
                    </ul>
                </div>

                {{-- Contact --}}
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-3 fw-bold">Contact Us</h5>
                    <ul class="list-unstyled">
                        @if(!empty($global_settings['hotel_address']))
                            <li class="mb-2"><i class="fas fa-map-marker-alt me-2" style="color: var(--accent-color)"></i>{{ $global_settings['hotel_address'] }}</li>
                        @endif
                        @if(!empty($global_settings['contact_phone']))
                            <li class="mb-2"><i class="fas fa-phone me-2" style="color: var(--accent-color)"></i>{{ $global_settings['contact_phone'] }}</li>
                        @endif
                        @if(!empty($global_settings['contact_email']))
                            <li class="mb-2"><i class="fas fa-envelope me-2" style="color: var(--accent-color)"></i>{{ $global_settings['contact_email'] }}</li>
                        @endif
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-3 fw-bold">Newsletter</h5>
                    <p>Subscribe for exclusive offers and updates.</p>
                    <form action="{{ route('public.subscribe') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" class="form-control border-0" placeholder="Your email address" required>
                            <button class="btn btn-hotel-primary" type="submit"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center footer-bottom">
                @php
                    $copyright = $global_settings['footer_copyright'] ?? '';
                    if (empty($copyright)) {
                        $copyright = '© ' . date('Y') . ' ' . ($global_settings['hotel_name'] ?? 'Bella Vista Lodge') . '. All rights reserved.';
                    }
                @endphp
                <p class="mb-0">{{ $copyright }}</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Widget -->
    @if(!empty($global_settings['whatsapp_number']))
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $global_settings['whatsapp_number']) }}" target="_blank" class="whatsapp-float track-whatsapp">
        <i class="fab fa-whatsapp"></i>
    </a>
    @endif

    @vite('resources/js/app.js')
    @yield('footer')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function trackEvent(eventType) {
            fetch('{{ route('marketing.track') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    event_type: eventType,
                    url: window.location.href,
                    page_type: document.title || 'Public Page'
                })
            }).catch(e => {});
        }

        document.querySelectorAll('a[href*="wa.me"], .whatsapp-float, .track-whatsapp').forEach(el => {
            el.addEventListener('click', () => trackEvent('whatsapp_click'));
        });
        document.querySelectorAll('a[href^="tel:"]').forEach(el => {
            el.addEventListener('click', () => trackEvent('phone_click'));
        });
        document.querySelectorAll('form[action*="subscribe"], form[action*="contact"]').forEach(el => {
            el.addEventListener('submit', () => trackEvent('contact_form_submit'));
        });
        document.querySelectorAll('a[href*="checkout"], button[type="submit"]').forEach(el => {
            if (el.innerText.toLowerCase().includes('book')) {
                el.addEventListener('click', () => trackEvent('booking_click'));
            }
        });
    });
    </script>
</body>
</html>
