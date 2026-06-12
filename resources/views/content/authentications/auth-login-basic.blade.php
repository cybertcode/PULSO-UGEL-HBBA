@php
$customizerHidden = 'customizer-hide';
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Iniciar Sesión - ' . ($configInstitucional?->sigla ?? $configInstitucional?->nombre_institucion ?? 'PULSO UGEL'))

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

          <h4 class="mb-1">Bienvenido 👋</h4>
          <p class="mb-6">Ingresa tus credenciales para acceder a {{ $configInstitucional?->nombre_institucion ?? 'PULSO UGEL' }}</p>

          @if (session('status'))
            <div class="alert alert-success mb-4">{{ session('status') }}</div>
          @endif
          @if ($errors->any())
            <div class="alert alert-danger mb-4">
              @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
          @endif

          <form id="formAuthentication" class="mb-4" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-6 form-control-validation">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror"
                id="email" name="email" value="{{ old('email') }}"
                placeholder="{{ $configInstitucional?->correo_institucional ? 'usuario@' . explode('@', $configInstitucional->correo_institucional)[1] : 'tu.correo@ugel.gob.pe' }}"
                autofocus />
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
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember"
                  {{ old('remember') ? 'checked' : '' }} />
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
