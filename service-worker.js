const CACHE_NAME = 'nailong-pwa-v3'

const STATIC_FILES = [
    './',
    './index.html',
    './manifest.json',

    './css/bootstrap.min.css',
    './css/animate.css',
    './css/font-awesome.min.css',
    './css/owl.theme.css',
    './css/owl.carousel.css',
    './css/style.css',

    './js/jquery.js',
    './js/bootstrap.min.js',
    './js/jquery.parallax.js',
    './js/owl.carousel.min.js',
    './js/smoothscroll.js',
    './js/wow.min.js',
    './js/custom.js',
    './js/register.js',

    './images/icon-192.png',
    './images/icon-512.png',

    './images/intro-bg.jpg',
    './images/overview-img.jpg',

    './images/speakers-img1.jpg',
    './images/speakers-img2.jpg',
    './images/speakers-img3.jpg',
    './images/speakers-img4.jpg',

    './images/program-img1.jpg',
    './images/program-img2.jpg',
    './images/program-img3.jpg',
    './images/program-img4.jpg',
    './images/program-img5.jpg',
    './images/program-img6.jpg',

    './images/register-bg.jpg'
]

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => cache.addAll(STATIC_FILES))
            .then(() => self.skipWaiting())
    )
})

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) => {
                return Promise.all(cacheNames.filter((name) => name !== CACHE_NAME).map((name) => caches.delete(name)))
            })
            .then(() => self.clients.claim())
    )
})

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            return cachedResponse || fetch(event.request)
        })
    )
})
