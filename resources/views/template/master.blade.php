<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Icon --}}
    <link rel="icon" href="{{ asset('img/logo/sip.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/logo/sip.png') }}">

    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="BV Lodge">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="BV Lodge">
    <meta name="msapplication-TileColor" content="#0f172a">
    <meta name="msapplication-TileImage" content="{{ asset('img/logo/sip.png') }}">

    {{-- style --}}
    @vite('resources/sass/app.scss')
    <title>@yield('title') - Hotel Admin</title>
    @yield('head')
</head>

<body class="sidebar-layout">
    <main>
        <!-- Enhanced Modal -->
        <div class="modal fade" id="main-modal" tabindex="-1" aria-labelledby="main-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                    <div class="modal-header bg-light border-0">
                        <h1 class="modal-title fs-5 fw-bold" id="main-modalLabel"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button id="btn-modal-close" type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Close</button>
                        <button id="btn-modal-save" type="button" class="btn btn-hotel-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Header -->
        @include('template.include._mobile-header')

        <div class="d-flex vh-100" id="wrapper">
            <!-- Desktop Sidebar -->
            @include('template.include._sidebar')

            <!-- Page Content -->
            <div id="page-content-wrapper" class="flex-fill">
                <div class="p-3 h-100">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- PWA Install Prompt Banner -->
        <div id="pwa-install-banner" style="
            display: none;
            position: fixed;
            bottom: 1.25rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: calc(100% - 2rem);
            max-width: 480px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border: 1px solid rgba(59,130,246,0.4);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.45), 0 0 0 1px rgba(59,130,246,0.15);
            backdrop-filter: blur(12px);
            animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        ">
            <style>
                @keyframes slideUp {
                    from { opacity: 0; transform: translateX(-50%) translateY(40px); }
                    to   { opacity: 1; transform: translateX(-50%) translateY(0); }
                }
                #pwa-install-banner .pwa-inner {
                    display: flex;
                    align-items: center;
                    gap: 0.875rem;
                }
                #pwa-install-banner .pwa-logo {
                    width: 52px;
                    height: 52px;
                    border-radius: 12px;
                    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                    font-size: 1.5rem;
                }
                #pwa-install-banner .pwa-text { flex: 1; min-width: 0; }
                #pwa-install-banner .pwa-title {
                    color: #f8fafc;
                    font-weight: 700;
                    font-size: 0.95rem;
                    margin-bottom: 0.2rem;
                }
                #pwa-install-banner .pwa-subtitle {
                    color: rgba(255,255,255,0.55);
                    font-size: 0.78rem;
                    line-height: 1.4;
                }
                #pwa-install-banner .pwa-actions {
                    display: flex;
                    gap: 0.5rem;
                    margin-top: 0.875rem;
                    justify-content: flex-end;
                }
                #pwa-install-banner .btn-install {
                    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    padding: 0.5rem 1.25rem;
                    font-size: 0.85rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: transform 0.2s, box-shadow 0.2s;
                }
                #pwa-install-banner .btn-install:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(59,130,246,0.45);
                }
                #pwa-install-banner .btn-dismiss {
                    background: rgba(255,255,255,0.08);
                    color: rgba(255,255,255,0.6);
                    border: 1px solid rgba(255,255,255,0.12);
                    border-radius: 8px;
                    padding: 0.5rem 1rem;
                    font-size: 0.85rem;
                    cursor: pointer;
                    transition: background 0.2s;
                }
                #pwa-install-banner .btn-dismiss:hover {
                    background: rgba(255,255,255,0.15);
                    color: #fff;
                }
            </style>
            <div class="pwa-inner">
                <div class="pwa-logo">🏨</div>
                <div class="pwa-text">
                    <div class="pwa-title">Install Bella Vista Lodge</div>
                    <div class="pwa-subtitle">Add to your home screen for faster access and offline support.</div>
                </div>
            </div>
            <div class="pwa-actions">
                <button class="btn-dismiss" id="pwa-dismiss-btn">Not now</button>
                <button class="btn-install" id="pwa-install-btn">⬇ Install App</button>
            </div>
        </div>
    </main>

    @vite('resources/js/app.js')

    <!-- Initialize Tooltips -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Sidebar toggle functionality for mobile
            const toggleBtn = document.getElementById('sidebar-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    document.getElementById('sidebar-wrapper').classList.toggle('collapsed');
                });
            }

            // Mobile dropdown functionality for offcanvas sidebar
            const mobileDropdownToggles = document.querySelectorAll('#mobileOffcanvas .nav-toggle[data-bs-toggle="collapse"]');

            mobileDropdownToggles.forEach(toggle => {
                const targetId = toggle.getAttribute('data-bs-target');
                const targetElement = document.querySelector(targetId);
                const arrow = toggle.querySelector('.nav-arrow');

                // Set initial state based on whether submenu is shown (server-side rendered)
                if (targetElement && targetElement.classList.contains('show')) {
                    toggle.setAttribute('aria-expanded', 'true');
                    if (arrow) {
                        arrow.style.transform = 'rotate(180deg)';
                    }
                } else {
                    toggle.setAttribute('aria-expanded', 'false');
                    if (arrow) {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                }

                // Handle click events for manual toggle
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Get or create Bootstrap Collapse instance
                    const collapse = bootstrap.Collapse.getOrCreateInstance(targetElement, {
                        toggle: false
                    });

                    // Toggle the collapse
                    collapse.toggle();
                });

                // Listen to Bootstrap collapse events to update aria and arrow
                targetElement.addEventListener('shown.bs.collapse', function() {
                    toggle.setAttribute('aria-expanded', 'true');
                    if (arrow) {
                        arrow.style.transform = 'rotate(180deg)';
                    }
                });

                targetElement.addEventListener('hidden.bs.collapse', function() {
                    toggle.setAttribute('aria-expanded', 'false');
                    if (arrow) {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                });
            });

            // Auto-close mobile menu when clicking nav links
            const mobileNavLinks = document.querySelectorAll('#mobileOffcanvas .nav-item:not(.dropdown-nav), #mobileOffcanvas .nav-subitem');
            const offcanvasElement = document.getElementById('mobileOffcanvas');

            mobileNavLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (offcanvasElement && window.innerWidth <= 768) {
                        const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                        if (offcanvas) {
                            offcanvas.hide();
                        }
                    }
                });
            });
        });
    </script>

    @yield('footer')

    <script>
        // ── Service Worker Registration ─────────────────────────────────────────
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(reg => console.log('[PWA] Service Worker registered:', reg.scope))
                    .catch(err => console.warn('[PWA] SW registration failed:', err));
            });
        }

        // ── PWA Install Prompt ─────────────────────────────────────────────────
        (function () {
            const DISMISSED_KEY = 'pwa_install_dismissed';
            const INSTALLED_KEY = 'pwa_installed';
            const banner        = document.getElementById('pwa-install-banner');
            const installBtn    = document.getElementById('pwa-install-btn');
            const dismissBtn    = document.getElementById('pwa-dismiss-btn');

            // Don't show if already dismissed/installed
            if (localStorage.getItem(DISMISSED_KEY) || localStorage.getItem(INSTALLED_KEY)) return;

            let deferredPrompt = null;
            let interacted     = false;

            // Capture the browser's install prompt event
            window.addEventListener('beforeinstallprompt', function (e) {
                e.preventDefault();
                deferredPrompt = e;

                // Show banner only after the user has interacted with the page
                if (interacted) showBanner();
            });

            // Track first interaction (click / touch / keydown)
            function onFirstInteraction() {
                interacted = true;
                document.removeEventListener('click',   onFirstInteraction);
                document.removeEventListener('touchend', onFirstInteraction);
                document.removeEventListener('keydown',  onFirstInteraction);

                if (deferredPrompt) showBanner();
            }

            document.addEventListener('click',   onFirstInteraction);
            document.addEventListener('touchend', onFirstInteraction);
            document.addEventListener('keydown',  onFirstInteraction);

            function showBanner() {
                if (banner) {
                    banner.style.display = 'block';
                }
            }

            function hideBanner() {
                if (banner) {
                    banner.style.animation = 'none';
                    banner.style.opacity   = '0';
                    banner.style.transform = 'translateX(-50%) translateY(20px)';
                    banner.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    setTimeout(() => { banner.style.display = 'none'; }, 350);
                }
            }

            // Install button
            if (installBtn) {
                installBtn.addEventListener('click', async function () {
                    if (!deferredPrompt) return;
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        localStorage.setItem(INSTALLED_KEY, '1');
                        console.log('[PWA] App installed!');
                    }
                    deferredPrompt = null;
                    hideBanner();
                });
            }

            // Dismiss button
            if (dismissBtn) {
                dismissBtn.addEventListener('click', function () {
                    localStorage.setItem(DISMISSED_KEY, '1');
                    hideBanner();
                });
            }

            // Hide when already installed
            window.addEventListener('appinstalled', function () {
                localStorage.setItem(INSTALLED_KEY, '1');
                hideBanner();
            });
        })();
    </script>
</body>

</html>
