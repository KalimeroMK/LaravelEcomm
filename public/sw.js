var cacheName = 'hello-pwa';
var filesToCache = [
  '/',
  '/en'
];

/* Start the service worker and cache all of the app's content */
self.addEventListener('install', function(e) {
  e.waitUntil(
    caches.open(cacheName).then(function(cache) {
      // Cache only existing routes, skip failed requests
      return Promise.all(
        filesToCache.map(function(url) {
          return fetch(url, { mode: 'no-cors' })
            .then(function(response) {
              if (response.status === 200 || response.type === 'opaque') {
                return cache.put(url, response);
              }
            })
            .catch(function() {
              // Silently skip failed requests
              console.log('Skipping cache for:', url);
            });
        })
      );
    })
  );
});

/* Serve cached content when offline */
self.addEventListener('fetch', function(e) {
  e.respondWith(
    caches.match(e.request).then(function(response) {
      return response || fetch(e.request).catch(function() {
        // Return a simple offline message for failed requests
        if (e.request.mode === 'navigate') {
          return new Response('You are offline. Please check your connection.');
        }
      });
    })
  );
});
