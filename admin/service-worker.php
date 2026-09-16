<?php
header('Content-Type: application/javascript');
header('Service-Worker-Allowed: /');
?>
const CACHE_NAME = 'prisha-erp-shell-v2';
const APP_SHELL = [
  './',
  './assets/style.css',
  './assets/logo.png',
  './assets/light-logo.png'
];

self.addEventListener('install', event => {
  event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(APP_SHELL)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const requestUrl = new URL(event.request.url);
  if (requestUrl.pathname.includes('/database/')) return;
  event.respondWith(fetch(event.request).catch(() => caches.match(event.request)));
});
