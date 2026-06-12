@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Verificar Correo - ' . ($configInstitucional?->sigla ?? $configInstitucional?->nombre_institucion ?? 'PULSO UGEL'))

@section('page-style')
@vite('resources/assets/vendor/scss/pages/page-auth.scss')
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">

  <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
    @if(!empty($configInstitucional?->logo_ruta))
      <span class="app-brand-logo demo">
        <img src="{{ Storage::url($configInstitucional->logo_ruta) }}" height="28" alt="logo" class="rounded">
      </span>
    @endif
    <span class="app-brand-text demo text-heading fw-bold">
      {{ $configInstitucional?->sigla ?? $configInstitucional?->nombre_institucion ?? 'PULSO UGEL' }}
    </span>
  </a>

  <div class="authentication-inner row m-0">
    <!-- Ilustración lateral -->
    <div class="d-none d-xl-flex col-xl-8 p-0">
      <div class="auth-cover-bg d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-verify-email-illustration-' . $configData['theme'] . '.png') }}"
          alt="verify-email" class="my-5 auth-illustration"
          data-app-light-img="illustrations/auth-verify-email-illustration-light.png"
          data-app-dark-img="illustrations/auth-verify-email-illustration-dark.png" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
          alt="bg" class="platform-bg"
          data-app-light-img="illustrations/bg-shape-image-light.png"
          data-app-dark-img="illustrations/bg-shape-image-dark.png" />
      </div>
    </div>

    <!-- Contenido -->
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12">
      <div class="w-px-400 mx-auto mt-12 mt-5">
        <h4 class="mb-1">Verifica tu correo ✉️</h4>

        @if (session('status') == 'verification-link-sent')
          <div class="alert alert-success mb-4" role="alert">
            Se envió un nuevo enlace de verificación a tu correo electrónico.
          </div>
        @endif

        <p class="text-start mb-0">
          Se envió un enlace de activación a:<br/>
          <span class="fw-medium text-heading">{{ Auth::user()->email }}</span><br/>
          Sigue el enlace del mensaje para continuar.
        </p>

        <div class="mt-6 d-flex flex-column gap-2">
          <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-100 btn btn-primary">Reenviar correo de verificación</button>
          </form>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-100 btn btn-label-secondary">Cerrar sesión</button>
          </form>
        </div>

        <div class="divider my-6">
          <div class="divider-text">
            {{ $configInstitucional?->nombre_institucion ?? 'PULSO UGEL' }}
            @if($configInstitucional?->provincia || $configInstitucional?->departamento)
              &bull; {{ implode(', ', array_filter([$configInstitucional->provincia, $configInstitucional->departamento])) }}
            @endif
          </div>
        </div>

        <p class="text-center text-muted small mb-0">
          <i class="ti tabler-shield-check me-1 text-success"></i>
          Sistema de Monitoreo de Control Interno e Integridad Institucional
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
