@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Verificación en Dos Pasos - ' . ($configInstitucional?->sigla ?? $configInstitucional?->nombre_institucion ?? 'PULSO UGEL'))

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages-auth.js', 'resources/assets/js/pages-auth-two-steps.js'])
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
        <img src="{{ asset('assets/img/illustrations/auth-two-step-illustration-' . $configData['theme'] . '.png') }}"
          alt="two-steps" class="my-5 auth-illustration"
          data-app-light-img="illustrations/auth-two-step-illustration-light.png"
          data-app-dark-img="illustrations/auth-two-step-illustration-dark.png" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
          alt="bg" class="platform-bg"
          data-app-light-img="illustrations/bg-shape-image-light.png"
          data-app-dark-img="illustrations/bg-shape-image-dark.png" />
      </div>
    </div>

    <!-- Formulario -->
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12">
      <div class="w-px-400 mx-auto mt-12 mt-5">
        <h4 class="mb-1">Verificación en Dos Pasos 💬</h4>
        <p class="text-start mb-6">
          Ingresa el código de verificación de tu aplicación autenticadora o el código enviado a tu dispositivo.
        </p>
        <p class="mb-0">Escribe tu código de 6 dígitos</p>

        <form id="twoStepsForm" action="{{ url('/') }}" method="GET">
          <div class="mb-6 form-control-validation">
            <div class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
              @for ($i = 0; $i < 6; $i++)
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                  maxlength="1" {{ $i === 0 ? 'autofocus' : '' }} />
              @endfor
            </div>
            <input type="hidden" name="otp" />
          </div>
          <button class="btn btn-primary d-grid w-100 mb-6">Verificar cuenta</button>
          <div class="text-center">
            ¿No recibiste el código?
            <a href="javascript:void(0);">Reenviar</a>
          </div>
        </form>

        <div class="divider my-6">
          <div class="divider-text">
            {{ $configInstitucional?->nombre_institucion ?? 'PULSO UGEL' }}
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
