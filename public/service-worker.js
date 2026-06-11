/**
 * PULSO UGEL - Service Worker PWA
 * Estrategia: Network First para páginas, Cache First para assets estáticos
 */

const CACHE_NAME = 'pulso-ugel-v1';
const STATIC_CACHE = 'pulso-ugel-static-v1';
const DYNAMIC_CACHE = 'pulso-ugel-dynamic-v1';

// Assets críticos para el shell de la app (offline fallback)
const STATIC_ASSETS = [
  '/offline.html',
  '/icons/pwa/icon-192x192.png',
  '/icons/pwa/icon-512x512.png',
];

// Rutas que NUNCA cachear (siempre requieren red)
const NEVER_CACHE = [
  '/logout',
  '/login',
  '/api/',
  '/sanctum/',
  '/_debugbar',
  '/telescope',
];

// ─── Install ───────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) => {
      return cache.addAll(STATIC_ASSETS).catch((err) => {
        console.warn('[SW] No se pudieron cachear assets estáticos:', err);
      });
    })
  );
  self.skipWaiting();
});

// ─── Activate ──────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys
          .filter((key) => key !== STATIC_CACHE && key !== DYNAMIC_CACHE)
          .map((key) => caches.delete(key))
      );
    })
  );
  self.clients.claim();
});

// ─── Fetch ─────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Solo manejar peticiones del mismo origen o assets
  if (url.origin !== location.origin) return;

  // Nunca cachear rutas sensibles
  if (NEVER_CACHE.some((path) => url.pathname.startsWith(path))) return;

  // Solo GET
  if (request.method !== 'GET') return;

  // Assets estáticos (JS, CSS, imágenes, fuentes) → Cache First
  if (isStaticAsset(url.pathname)) {
    event.respondWith(cacheFirst(request));
    return;
  }

  // Navegación HTML → Network First con fallback offline
  if (request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(networkFirstWithOfflineFallback(request));
    return;
  }

  // Resto → Network First
  event.respondWith(networkFirst(request));
});

// ─── Estrategias ───────────────────────────────────────────────────────────

function isStaticAsset(pathname) {
  return (
    pathname.startsWith('/build/') ||
    pathname.startsWith('/assets/') ||
    pathname.startsWith('/icons/') ||
    /\.(js|css|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|ico|webp)$/.test(pathname)
  );
}

async function cacheFirst(request) {
  const cached = await caches.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(STATIC_CACHE);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    return new Response('Asset no disponible offline', { status: 503 });
  }
}

async function networkFirst(request) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    return cached || new Response('Sin conexión', { status: 503 });
  }
}

async function networkFirstWithOfflineFallback(request) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    if (cached) return cached;
    return caches.match('/offline.html');
  }
}

// ─── Push Notifications (base) ─────────────────────────────────────────────
self.addEventListener('push', (event) => {
  if (!event.data) return;

  const data = event.data.json();
  event.waitUntil(
    self.registration.showNotification(data.title || 'PULSO UGEL', {
      body: data.body || '',
      icon: '/icons/pwa/icon-192x192.png',
      badge: '/icons/pwa/icon-72x72.png',
      data: { url: data.url || '/' },
      vibrate: [200, 100, 200],
    })
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const targetUrl = event.notification.data?.url || '/';
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then((clientList) => {
      for (const client of clientList) {
        if (client.url === targetUrl && 'focus' in client) return client.focus();
      }
      if (clients.openWindow) return clients.openWindow(targetUrl);
    })
  );
});
