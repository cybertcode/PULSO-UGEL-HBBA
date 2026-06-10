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

  <meta name="description" content="{{ $instDesc }}" />
  <meta name="keywords" content="{{ $instNombre }}, Control Interno, SCI, PACI, {{ $instSigla }}, Contraloría, UGEL" />

  <meta property="og:title" content="@yield('title', $instNombre) | {{ $instSigla }}" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ $siteUrl }}" />
  <meta property="og:image" content="{{ $ogImage }}" />
  <meta property="og:description" content="{{ $instDesc }}" />
  <meta property="og:site_name" content="{{ $instNombre }}" />
  <meta property="og:locale" content="es_PE" />

  <meta name="robots" content="noindex, nofollow" />
  <meta name="x-sys-ref" content="SW5nLiBNS2V2eW4gSEggfCBkZXZlbG9wdGVjaDIzQGdtYWlsLmNvbSB8IGZhY2Vib29rLmNvbS9ta2V2eW4uaGhpbGFyaW8=" />
  <meta name="x-build-id" content="UFVMUk8tVUdFTC12MSAyMDI1LTA2IHwgSW5nLiBNS2V2eW4gSEg=" />
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <!-- Canonical SEO -->
  <link rel="canonical" href="{{ $siteUrl }}" />

  <!-- Favicon dinámico desde configuración institucional -->
  @if (!empty($configInstitucional?->favicon_ruta))
    <link rel="icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($configInstitucional->favicon_ruta) }}" />
    <link rel="shortcut icon" href="{{ \Illuminate\Support\Facades\Storage::url($configInstitucional->favicon_ruta) }}" />
  @elseif (!empty($configInstitucional?->logo_ruta))
    <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($configInstitucional->logo_ruta) }}" />
  @else
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
  @endif

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
</body>

</html>
