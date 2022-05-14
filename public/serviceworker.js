var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/css/home.css',
    '/js/app.js',
    '/pwa/icon-72x72.png',
    '/pwa/icon-96x96.png',
    '/pwa/icon-128x128.png',
    '/pwa/icon-144x144.png',
    '/pwa/icon-152x152.png',
    '/pwa/icon-192x192.png',
    '/pwa/icon-384x384.png',
    '/pwa/icon-512x512.png',
];

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
