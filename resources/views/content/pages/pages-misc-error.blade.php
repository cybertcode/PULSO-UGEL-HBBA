@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Página no encontrada')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection

@section('content')
<!-- Error 404 -->
<div class="container-xxl container-p-y">
  <div class="misc-wrapper">
    @if(!empty($configuracion->logo_ruta))
      <div class="mb-4">
        <img src="{{ asset('storage/' . $configuracion->logo_ruta) }}" alt="{{ $configuracion->sigla ?? 'Logo' }}" height="48" />
      </div>
    @endif
    <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem;">404</h1>
    <h4 class="mb-2 mx-2">Página no encontrada ⚠️</h4>
    <p class="mb-1 mx-2">No pudimos encontrar la página que estás buscando.</p>
    @if(!empty($configuracion->nombre_institucion))
      <p class="text-muted mb-6 mx-2 small">{{ $configuracion->nombre_institucion }}</p>
    @endif
    <a href="{{ url('/') }}" class="btn btn-primary mb-10">Volver al inicio</a>
    <div class="mt-4">
      <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}" alt="error-404" width="225" class="img-fluid" />
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}" height="355" alt="bg-error" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png" />
</div>
<!-- /Error 404 -->
@endsection
