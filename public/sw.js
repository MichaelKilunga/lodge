/**
 * Bella Vista Lodge - Service Worker
 * Cache-first for static assets, network-first for pages/API
 */

const CACHE_NAME = 'bella-vista-lodge-v1';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/img/logo/sip.png',
    '/offline.html',
];

// ── Install: pre-cache critical static assets ──────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('[SW] Pre-cache failed for some assets:', err);
            });
        })
    );
    self.skipWaiting();
});

// ── Activate: clean up old caches ──────────────────────────────────────────
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

// ── Fetch: strategy based on request type ─────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET, cross-origin, and API/POST requests
    if (
        request.method !== 'GET' ||
        !url.origin.includes(self.location.origin) ||
        url.pathname.startsWith('/api/')
    ) {
        return;
    }

    // Cache-first for static assets (JS, CSS, images, fonts)
    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Network-first for HTML pages (dashboard, forms, etc.)
    event.respondWith(networkFirst(request));
});

// ── Helpers ────────────────────────────────────────────────────────────────

function isStaticAsset(pathname) {
    return /\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot|webp)(\?.*)?$/.test(pathname)
        || pathname.startsWith('/build/')
        || pathname.startsWith('/asset/');
}

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('Asset unavailable offline', { status: 503 });
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;

        // Fallback to offline page for navigation requests
        if (request.mode === 'navigate') {
            const offlinePage = await caches.match('/offline.html');
            if (offlinePage) return offlinePage;
        }

        return new Response('You are offline. Please check your connection.', {
            status: 503,
            headers: { 'Content-Type': 'text/plain' },
        });
    }
}
