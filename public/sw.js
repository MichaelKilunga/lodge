/**
 * Bella Vista Lodge - Service Worker
 * Dynamic network-first strategy for pages, cache-first for media assets
 */

const CACHE_NAME = 'bella-vista-lodge-v3';
const STATIC_ASSETS = [
    '/manifest.json',
    '/img/branding/bellavista_logo.jpg',
    '/img/logo/bellavista_logo.jpg',
    '/img/logo/sip.png',
    '/offline.html',
];

// ── Install: pre-cache critical static media assets ─────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('[SW] Pre-cache warning:', err);
            });
        })
    );
    self.skipWaiting();
});

// ── Activate: purge all outdated caches immediately ─────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// ── Fetch: request handler ─────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Bypass non-GET, cross-origin, and API / admin POST requests
    if (
        request.method !== 'GET' ||
        !url.origin.includes(self.location.origin) ||
        url.pathname.startsWith('/api/')
    ) {
        return;
    }

    // Static media assets (Images / Fonts / SVGs) - Cache-first with network fallback
    if (isMediaAsset(url.pathname)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // All HTML Pages & Vite Build Bundle files - Network-first (always live)
    event.respondWith(networkOnlyOrOffline(request));
});

function isMediaAsset(pathname) {
    return /\.(png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot|webp)(\?.*)?$/i.test(pathname);
}

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response && response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return fetch(request);
    }
}

async function networkOnlyOrOffline(request) {
    try {
        return await fetch(request);
    } catch (error) {
        const cached = await caches.match(request);
        if (cached) return cached;

        if (request.mode === 'navigate') {
            const offlinePage = await caches.match('/offline.html');
            if (offlinePage) return offlinePage;
        }

        return new Response('You are offline. Please check your internet connection.', {
            status: 503,
            headers: { 'Content-Type': 'text/plain' },
        });
    }
}
