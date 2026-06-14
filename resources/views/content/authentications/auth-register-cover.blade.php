@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Registrarse')

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
@php $ci = \App\Models\ConfiguracionInstitucional::cached(); @endphp
<div class="authentication-wrapper authentication-cover">
  <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
    @if(!empty($ci?->logo_ruta))
      <span class="app-brand-logo demo">
        <img src="{{ \Illuminate\Support\Facades\Storage::url($ci->logo_ruta) }}" height="28" alt="logo" class="rounded">
      </span>
    @endif
    <span class="app-brand-text demo text-heading fw-bold">
      {{ $ci?->sigla ?? $ci?->nombre_institucion ?? 'PULSO UGEL' }}
    </span>
  </a>

  <div class="authentication-inner row m-0">
    <!-- Ilustración lateral -->
    <div class="d-none d-xl-flex col-xl-8 p-0">
      <div class="auth-cover-bg d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-register-illustration-' . $configData['theme'] . '.png') }}"
          alt="register" class="my-5 auth-illustration"
          data-app-light-img="illustrations/auth-register-illustration-light.png"
          data-app-dark-img="illustrations/auth-register-illustration-dark.png" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
          alt="bg" class="platform-bg"
          data-app-light-img="illustrations/bg-shape-image-light.png"
          data-app-dark-img="illustrations/bg-shape-image-dark.png" />
      </div>
    </div>

    <!-- Formulario de registro -->
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">Crear Cuenta 🚀</h4>
        <p class="mb-6 text-muted">Completa tus datos para acceder a {{ $ci?->sigla ?? $ci?->nombre_institucion ?? 'PULSO UGEL' }}</p>

        @if ($errors->any())
          <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3">
              @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        @endif

        <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
          @csrf

          <div class="mb-5 form-control-validation">
            <label for="name" class="form-label">Nombre completo</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
              id="name" name="name" value="{{ old('name') }}"
              placeholder="Ej: María García López" autofocus />
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-5 form-control-validation">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
              id="email" name="email" value="{{ old('email') }}"
              placeholder="tu.correo@ugel.gob.pe" />
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-5 form-password-toggle form-control-validation">
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

          <div class="mb-5 form-password-toggle">
            <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
            <div class="input-group input-group-merge">
              <input type="password" id="password_confirmation"
                class="form-control" name="password_confirmation" placeholder="············" />
              <span class="input-group-text cursor-pointer">
                <i class="icon-base ti tabler-eye-off"></i>
              </span>
            </div>
          </div>

          <div class="mb-6 mt-2">
            <div class="form-check ms-2 form-control-validation">
              <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
              <label class="form-check-label" for="terms-conditions">
                Acepto la <a href="javascript:void(0);">política de privacidad y términos de uso</a>
              </label>
            </div>
          </div>

          <button class="btn btn-primary d-grid w-100" type="submit">Crear Cuenta</button>
        </form>

        <p class="text-center">
          <span>¿Ya tienes cuenta?</span>
          <a href="{{ route('login') }}"> Iniciar sesión</a>
        </p>

        <div class="divider my-6">
          <div class="divider-text">
            {{ $ci?->nombre_institucion ?? 'PULSO UGEL' }}
            @if($ci?->distrito || $ci?->provincia)
              &bull; {{ implode(', ', array_filter([$ci->distrito, $ci->provincia, $ci->departamento])) }}
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
