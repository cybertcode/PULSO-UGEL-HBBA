@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
try { $ci = \App\Models\ConfiguracionInstitucional::cached(); } catch (\Exception $e) { $ci = null; }
@endphp

@extends('layouts/blankLayout')

@section('title', 'Registrarse - ' . ($ci?->sigla ?? $ci?->nombre_institucion ?? 'PULSO UGEL'))

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
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

    <!-- Formulario -->
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">Crear Cuenta 🚀</h4>
        <p class="mb-6 text-muted">Completa tus datos para acceder a {{ $ci?->nombre_institucion ?? 'PULSO UGEL' }}</p>

        @if ($errors->any())
          <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3">
              @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        @endif

        <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
          @csrf

          <div class="mb-6">
            <label for="name" class="form-label">Nombre completo</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
              id="name" name="name" placeholder="Ej: María García López"
              autofocus value="{{ old('name') }}" />
            @error('name')
              <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>

          <div class="mb-6">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
              id="email" name="email"
              placeholder="{{ $ci?->correo_institucional ? 'usuario@' . explode('@', $ci->correo_institucional)[1] : 'tu.correo@ugel.gob.pe' }}"
              value="{{ old('email') }}" />
            @error('email')
              <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>

          <div class="mb-6 form-password-toggle">
            <label class="form-label" for="password">Contraseña</label>
            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
              <input type="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password" placeholder="············" aria-describedby="password" />
              <span class="input-group-text cursor-pointer">
                <i class="icon-base ti tabler-eye-off"></i>
              </span>
            </div>
            @error('password')
              <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>

          <div class="mb-6 form-password-toggle">
            <label class="form-label" for="password-confirm">Confirmar contraseña</label>
            <div class="input-group input-group-merge">
              <input type="password" id="password-confirm" class="form-control"
                name="password_confirmation" placeholder="············" />
              <span class="input-group-text cursor-pointer">
                <i class="icon-base ti tabler-eye-off"></i>
              </span>
            </div>
          </div>

          @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mb-6 mt-8">
              <div class="form-check mb-8 ms-2 @error('terms') is-invalid @enderror">
                <input class="form-check-input @error('terms') is-invalid @enderror"
                  type="checkbox" id="terms" name="terms" />
                <label class="form-check-label" for="terms">
                  Acepto la <a href="{{ route('policy.show') }}" target="_blank">política de privacidad</a>
                  y <a href="{{ route('terms.show') }}" target="_blank">términos de uso</a>
                </label>
              </div>
              @error('terms')
                <div class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></div>
              @enderror
            </div>
          @endif

          <button type="submit" class="btn btn-primary d-grid w-100">Crear Cuenta</button>
        </form>

        <p class="text-center">
          <span>¿Ya tienes cuenta?</span>
          @if (Route::has('login'))
            <a href="{{ route('login') }}"> Iniciar sesión</a>
          @endif
        </p>

        <div class="divider my-6">
          <div class="divider-text">
            {{ $ci?->nombre_institucion ?? 'PULSO UGEL' }}
            @if($ci?->provincia || $ci?->departamento)
              &bull; {{ implode(', ', array_filter([$ci->provincia, $ci->departamento])) }}
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
