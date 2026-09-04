var staticCacheName = "bwibo-pwa-v3";
var filesToCache = [
    '/offline.html',
    '/images/icons/icon-72x72.png',
    '/images/icons/icon-96x96.png',
    '/images/icons/icon-128x128.png',
    '/images/icons/icon-144x144.png',
    '/images/icons/icon-152x152.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-384x384.png',
    '/images/icons/icon-512x512.png',
];

self.addEventListener("install", event => {
    self.skipWaiting();
    event.waitUntil(caches.open(staticCacheName).then(cache => cache.addAll(filesToCache)));
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => Promise.all(
            cacheNames
                .filter(cacheName => cacheName.startsWith("pwa-") || cacheName.startsWith("bwibo-pwa-"))
                .filter(cacheName => cacheName !== staticCacheName)
                .map(cacheName => caches.delete(cacheName))
        )).then(() => self.clients.claim())
    );
});

self.addEventListener("fetch", event => {
    if (event.request.method !== 'GET') {
        return;
    }

    const requestUrl = new URL(event.request.url);

    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request).catch(() => caches.match('/offline.html')));
        return;
    }

    if (requestUrl.origin === self.location.origin && requestUrl.pathname.startsWith('/build/assets/')) {
        event.respondWith(fetch(event.request));
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
            .catch(() => caches.match('/offline.html'))
    );
});

self.addEventListener("message", event => {
    if (event.data && event.data.type === 'SET_APP_BADGE') {
        const count = Number(event.data.count) || 0;
        if ('setAppBadge' in navigator) {
            if (count > 0) navigator.setAppBadge(count).catch(() => {});
            else if ('clearAppBadge' in navigator) navigator.clearAppBadge().catch(() => {});
        }
    }
});

self.addEventListener("push", event => {
    if ('setAppBadge' in navigator) {
        navigator.setAppBadge().catch(() => {});
    }
});
