const CACHE_NAME = 'mytraveler-cache-v1';
const urlsToCache = [
  '/',
  '/index.html',
  '/about.html',
  '/gallery.html',
  '/contact.html',
  '/feedback.html',
  '/styles.css',
  '/script.js',
  '/feedback.php',
  '/icons/11.png',
  '/icons/22.png',
  '/icons/1.png',
  '/icons/2.png',
  '/icons/3.png',
  '/icons/4.png',
  '/icons/5.png',
  '/icons/6.png',
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      })
  );
});

self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
