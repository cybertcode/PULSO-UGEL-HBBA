@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Iniciar Sesión')

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
        <img src="{{ Storage::url($ci->logo_ruta) }}" height="28" alt="logo" class="rounded">
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
        <img src="{{ asset('assets/img/illustrations/auth-login-illustration-' . $configData['theme'] . '.png') }}"
          alt="login" class="my-5 auth-illustration"
          data-app-light-img="illustrations/auth-login-illustration-light.png"
          data-app-dark-img="illustrations/auth-login-illustration-dark.png" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
          alt="bg" class="platform-bg"
          data-app-light-img="illustrations/bg-shape-image-light.png"
          data-app-dark-img="illustrations/bg-shape-image-dark.png" />
      </div>
    </div>

    <!-- Formulario -->
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-12 pt-5">

        <h4 class="mb-1">Bienvenido a {{ $ci?->sigla ?? $ci?->nombre_institucion ?? 'PULSO UGEL' }}</h4>
        <p class="mb-6 text-muted">Ingresa tus credenciales para acceder al Sistema de Control Interno</p>

        @if (session('status'))
          <div class="alert alert-success mb-4">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger mb-4">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
          </div>
        @endif

        <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
          @csrf
          <div class="mb-6 form-control-validation">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
              id="email" name="email" value="{{ old('email') }}"
              placeholder="tu.correo@ugel.gob.pe" autofocus />
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-6 form-password-toggle form-control-validation">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="password">Contraseña</label>
              @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm">
                  <small>¿Olvidaste tu contraseña?</small>
                </a>
              @endif
            </div>
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

          <div class="my-8">
            <div class="form-check mb-0 ms-2">
              <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
              <label class="form-check-label" for="remember-me">Recordarme</label>
            </div>
          </div>

          <button class="btn btn-primary d-grid w-100" type="submit">Iniciar Sesión</button>
        </form>

        @if (Route::has('register'))
        <p class="text-center">
          <span>¿No tienes cuenta?</span>
          <a href="{{ route('register') }}"> Regístrate</a>
        </p>
        @endif

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
