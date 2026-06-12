@php
use Illuminate\Support\Facades\Storage;
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

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
          alt="two-factor" class="my-5 auth-illustration"
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

        <div x-data="{ recovery: false }">
          <div class="text-start mb-6" x-show="! recovery">
            Confirma el acceso ingresando el código de tu aplicación autenticadora.
          </div>
          <div class="text-start mb-6" x-show="recovery">
            Ingresa uno de tus códigos de recuperación de emergencia.
          </div>

          <x-validation-errors class="mb-1" />

          <form method="POST" action="{{ route('two-factor.login') }}">
            @csrf
            <div class="mb-6" x-show="! recovery">
              <label class="form-label">Código de verificación</label>
              <x-input class="{{ $errors->has('code') ? 'is-invalid' : '' }}"
                type="text" inputmode="numeric" name="code"
                autofocus x-ref="code" autocomplete="one-time-code"
                placeholder="000000" />
              <x-input-error for="code"></x-input-error>
            </div>
            <div class="mb-5" x-show="recovery">
              <label class="form-label">Código de recuperación</label>
              <x-input class="{{ $errors->has('recovery_code') ? 'is-invalid' : '' }}"
                type="text" name="recovery_code"
                x-ref="recovery_code" autocomplete="one-time-code" />
              <x-input-error for="recovery_code"></x-input-error>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <div x-show="! recovery" x-on:click="recovery = true; $nextTick(() => { $refs.recovery_code.focus() })">
                <button type="button" class="btn btn-outline-secondary">Usar código de recuperación</button>
              </div>
              <div x-cloak x-show="recovery" x-on:click="recovery = false; $nextTick(() => { $refs.code.focus() })">
                <button type="button" class="btn btn-outline-secondary">Usar código de autenticador</button>
              </div>
              <x-button class="px-3">Verificar</x-button>
            </div>
          </form>
        </div>

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
