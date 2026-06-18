<!DOCTYPE html>
@php
  use Illuminate\Support\Str;
  use App\Helpers\Helpers;

  $menuFixed =
      $configData['layout'] === 'vertical'
          ? $menuFixed ?? ''
          : ($configData['layout'] === 'front'
              ? ''
              : $configData['headerType']);
  $navbarType =
      $configData['layout'] === 'vertical'
          ? $configData['navbarType']
          : ($configData['layout'] === 'front'
              ? 'layout-navbar-fixed'
              : '');
  $isFront = ($isFront ?? '') == true ? 'Front' : '';
  $contentLayout = isset($container) ? ($container === 'container-xxl' ? 'layout-compact' : 'layout-wide') : '';

  // Get skin name from configData - only applies to admin layouts
  $isAdminLayout = !Str::contains($configData['layout'] ?? '', 'front');
  $skinName = $isAdminLayout ? $configData['skinName'] ?? 'default' : 'default';

  // Get semiDark value from configData - only applies to admin layouts
  $semiDarkEnabled = $isAdminLayout && filter_var($configData['semiDark'] ?? false, FILTER_VALIDATE_BOOLEAN);

  // Generate primary color CSS if color is set
  $primaryColorCSS = '';
  if (isset($configData['color']) && $configData['color']) {
      $primaryColorCSS = Helpers::generatePrimaryColorCSS($configData['color']);
  }

@endphp

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}"
  class="{{ $navbarType ?? '' }} {{ $contentLayout ?? '' }} {{ $menuFixed ?? '' }} {{ $menuCollapsed ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}"
  dir="{{ $configData['textDirection'] }}" data-skin="{{ $skinName }}" data-assets-path="{{ asset('/assets') . '/' }}"
  data-base-url="{{ url('/') }}" data-framework="laravel" data-template="{{ $configData['layout'] }}-menu-template"
  data-bs-theme="{{ $configData['theme'] }}" @if ($isAdminLayout && $semiDarkEnabled) data-semidark-menu="true" @endif>

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  @php
    // Garantizar que $configInstitucional esté disponible aunque el composer falle
    if (empty($configInstitucional)) {
        try { $configInstitucional = \App\Models\ConfiguracionInstitucional::cached(); } catch (\Exception $e) { $configInstitucional = null; }
    }
    $instNombre = $configInstitucional?->nombre_institucion ?? config('variables.templateName', 'PULSO UGEL');
    $instSigla  = $configInstitucional?->sigla ?? config('variables.templateSuffix', 'Sistema SCI');
    $instDesc   = $configInstitucional?->descripcion
                  ?? 'Sistema digital de Control Interno para '.$instNombre.'. Gestión, seguimiento y evaluación alineada con la Contraloría General de la República del Perú.';
    $ogImage    = $configInstitucional?->logo_ruta
                  ? \Illuminate\Support\Facades\Storage::url($configInstitucional->logo_ruta)
                  : (config('variables.ogImage') ?: '');
    $siteUrl    = url('/');
  @endphp

  <title>@yield('title', $instNombre) | {{ $instSigla }}</title>

  <meta name="description" content="@yield('meta-description', $instDesc)" />
  <meta name="keywords" content="PULSO UGEL, PULSO UGEL Huacaybamba, {{ $instNombre }}, Control Interno, SCI, PACI, {{ $instSigla }}, Contraloría, UGEL" />

  <meta property="og:title" content="@yield('title', $instNombre) | {{ $instSigla }}" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ $siteUrl }}" />
  <meta property="og:image" content="{{ $ogImage }}" />
  <meta property="og:description" content="@yield('meta-description', $instDesc)" />
  <meta property="og:site_name" content="{{ $instNombre }}" />
  <meta property="og:locale" content="es_PE" />

  <meta name="author" content="Ing. Marvyn Kevyn Huanca Hilario" />
  <meta name="robots" content="@yield('meta-robots', 'noindex, nofollow')" />
  <meta name="x-sys-ref" content="SW5nLiBNS2V2eW4gSEggfCBkZXZlbG9wdGVjaDIzQGdtYWlsLmNvbSB8IGZhY2Vib29rLmNvbS9ta2V2eW4uaGhpbGFyaW8=" />
  <meta name="x-build-id" content="UFVMUk8tVUdFTC12MSAyMDI1LTA2IHwgSW5nLiBNS2V2eW4gSEg=" />
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <!-- Canonical SEO -->
  <link rel="canonical" href="@yield('canonical', url()->current())" />

  <!-- Favicon dinámico desde configuración institucional -->
  @if (!empty($configInstitucional?->favicon_ruta))
    <link rel="icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($configInstitucional->favicon_ruta) }}" />
    <link rel="shortcut icon" href="{{ \Illuminate\Support\Facades\Storage::url($configInstitucional->favicon_ruta) }}" />
  @elseif (!empty($configInstitucional?->logo_ruta))
    <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($configInstitucional->logo_ruta) }}" />
  @else
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
  @endif

  <!-- PWA Manifest & Meta Tags -->
  <link rel="manifest" href="/manifest.json" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="apple-mobile-web-app-title" content="{{ $instSigla }}" />
  <meta name="application-name" content="{{ $instSigla }}" />
  <meta name="theme-color" content="#7367f0" />
  <meta name="msapplication-TileColor" content="#7367f0" />
  <meta name="msapplication-TileImage" content="/icons/pwa/icon-144x144.png" />
  <!-- Apple Touch Icons -->
  <link rel="apple-touch-icon" href="/icons/pwa/icon-192x192.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="/icons/pwa/icon-152x152.png" />
  <link rel="apple-touch-icon" sizes="192x192" href="/icons/pwa/icon-192x192.png" />
  <link rel="apple-touch-icon" sizes="512x512" href="/icons/pwa/icon-512x512.png" />
  <!-- Apple Splash Screens (iPhone 14 Pro, iPhone SE) -->
  <link rel="apple-touch-startup-image" media="(device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3)" href="/icons/pwa/screenshot-mobile.png" />
  <!-- End PWA -->

  <!-- Include Styles -->
  <!-- $isFront is used to append the front layout styles only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/styles' . $isFront)

  @if (
      $primaryColorCSS &&
          (config('custom.custom.primaryColor') ||
              isset($_COOKIE['admin-primaryColor']) ||
              isset($_COOKIE['front-primaryColor'])))
    <!-- Primary Color Style -->
    <style id="primary-color-style">
      {!! $primaryColorCSS !!}
    </style>
  @endif

  <!-- Include Scripts for customizer, helper, analytics, config -->
  <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scriptsIncludes' . $isFront)
</head>

<body>
  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->

  {{-- remove while creating package --}}
  {{-- remove while creating package end --}}

  <!-- Include Scripts -->
  <!-- $isFront is used to append the front layout scripts only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scripts' . $isFront)

  <!-- PWA Service Worker & Install Banner -->
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js', { scope: '/' })
          .then(reg => {
            reg.addEventListener('updatefound', () => {
              const newWorker = reg.installing;
              newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                  showUpdateBanner();
                }
              });
            });
          })
          .catch(err => console.warn('[PWA] Service worker no registrado:', err));
      });
    }

    // Banner de actualización disponible
    function showUpdateBanner() {
      const banner = document.getElementById('pwa-update-banner');
      if (banner) banner.style.display = 'flex';
    }

    // Banner de instalación
    let deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
      const installBtn = document.getElementById('pwa-install-btn');
      if (installBtn) {
        installBtn.style.display = 'flex';
        installBtn.addEventListener('click', async () => {
          installBtn.style.display = 'none';
          deferredPrompt.prompt();
          const { outcome } = await deferredPrompt.userChoice;
          deferredPrompt = null;
        }, { once: true });
      }
    });

    window.addEventListener('appinstalled', () => {
      deferredPrompt = null;
      const installBtn = document.getElementById('pwa-install-btn');
      if (installBtn) installBtn.style.display = 'none';
    });
  </script>

  <!-- Botón discreto de instalación PWA -->
  <button id="pwa-install-btn" title="Instalar aplicación" style="display:none;position:fixed;bottom:1.25rem;right:1.25rem;z-index:9999;
    width:36px;height:36px;border-radius:50%;border:none;cursor:pointer;
    background:rgba(115,103,240,.12);color:#7367f0;
    align-items:center;justify-content:center;
    transition:background .2s,transform .15s;"
    onmouseenter="this.style.background='rgba(115,103,240,.22)';this.style.transform='scale(1.1)'"
    onmouseleave="this.style.background='rgba(115,103,240,.12)';this.style.transform='scale(1)'">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
      <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
      <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
    </svg>
  </button>

  <!-- Banner de actualización disponible -->
  <div id="pwa-update-banner" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:9998;
    background:#28c76f;color:#fff;padding:.75rem 1.25rem;justify-content:space-between;
    align-items:center;font-size:.875rem;font-family:inherit;gap:1rem;flex-wrap:wrap;">
    <span>Nueva versión disponible — recarga para actualizar.</span>
    <button onclick="window.location.reload()" style="background:rgba(255,255,255,.25);border:1px solid rgba(255,255,255,.5);
      color:#fff;border-radius:.375rem;padding:.375rem .875rem;font-size:.8125rem;cursor:pointer;">
      Actualizar ahora
    </button>
  </div>
</body>

</html>
