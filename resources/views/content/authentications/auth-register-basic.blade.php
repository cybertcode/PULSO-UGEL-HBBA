@php
$customizerHidden = 'customizer-hide';
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Crear Cuenta - ' . ($configInstitucional?->sigla ?? $configInstitucional?->nombre_institucion ?? 'PULSO UGEL'))

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <div class="card">
        <div class="card-body">

          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              @if(!empty($configInstitucional?->logo_ruta))
                <span class="app-brand-logo demo">
                  <img src="{{ Storage::url($configInstitucional->logo_ruta) }}" height="28" alt="logo" class="rounded">
                </span>
              @endif
              <span class="app-brand-text demo text-heading fw-bold">
                {{ $configInstitucional?->sigla ?? $configInstitucional?->nombre_institucion ?? 'PULSO UGEL' }}
              </span>
            </a>
          </div>

          <h4 class="mb-1">Crear Cuenta 🚀</h4>
          <p class="mb-6">Completa tus datos para registrarte en {{ $configInstitucional?->nombre_institucion ?? 'PULSO UGEL' }}</p>

          @if ($errors->any())
            <div class="alert alert-danger mb-4">
              <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
              </ul>
            </div>
          @endif

          <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-6 form-control-validation">
              <label for="name" class="form-label">Nombre completo</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror"
                id="name" name="name" value="{{ old('name') }}"
                placeholder="Ej: María García López" autofocus />
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-6 form-control-validation">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror"
                id="email" name="email" value="{{ old('email') }}"
                placeholder="{{ $configInstitucional?->correo_institucional ? 'usuario@' . explode('@', $configInstitucional->correo_institucional)[1] : 'tu.correo@ugel.gob.pe' }}" />
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-6 form-password-toggle form-control-validation">
              <label class="form-label" for="password">Contraseña</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password"
                  class="form-control @error('password') is-invalid @enderror"
                  name="password" placeholder="············" />
                <span class="input-group-text cursor-pointer">
                  <i class="icon-base ti tabler-eye-off"></i>
                </span>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="mb-6 form-password-toggle">
              <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password_confirmation"
                  class="form-control" name="password_confirmation" placeholder="············" />
                <span class="input-group-text cursor-pointer">
                  <i class="icon-base ti tabler-eye-off"></i>
                </span>
              </div>
            </div>

            <div class="my-8 form-control-validation">
              <div class="form-check mb-0 ms-2">
                <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                <label class="form-check-label" for="terms-conditions">
                  Acepto la <a href="javascript:void(0);">política de privacidad y términos de uso</a>
                </label>
              </div>
            </div>

            <button class="btn btn-primary d-grid w-100">Crear Cuenta</button>
          </form>

          <p class="text-center">
            <span>¿Ya tienes cuenta?</span>
            <a href="{{ route('login') }}"> Iniciar sesión</a>
          </p>

          @if($configInstitucional?->nombre_institucion)
            <div class="divider my-4">
              <div class="divider-text small">
                {{ $configInstitucional->nombre_institucion }}
                @if($configInstitucional->provincia || $configInstitucional->departamento)
                  &bull; {{ implode(', ', array_filter([$configInstitucional->provincia, $configInstitucional->departamento])) }}
                @endif
              </div>
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
