@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
@endphp

@php $ci = \App\Models\ConfiguracionInstitucional::cached(); @endphp
@extends('layouts/blankLayout')

@section('title', 'Restablecer Contraseña - ' . ($ci?->sigla ?? $ci?->nombre_institucion ?? 'PULSO UGEL'))

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
        <img src="{{ asset('assets/img/illustrations/auth-reset-password-illustration-' . $configData['theme'] . '.png') }}"
          alt="reset-password" class="my-5 auth-illustration"
          data-app-light-img="illustrations/auth-reset-password-illustration-light.png"
          data-app-dark-img="illustrations/auth-reset-password-illustration-dark.png" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
          alt="bg" class="platform-bg"
          data-app-light-img="illustrations/bg-shape-image-light.png"
          data-app-dark-img="illustrations/bg-shape-image-dark.png" />
      </div>
    </div>

    <!-- Formulario -->
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">Restablecer Contraseña 🔒</h4>
        <p class="mb-6"><span class="fw-medium">Tu nueva contraseña debe ser diferente a las anteriores</span></p>

        @if ($errors->any())
          <div class="alert alert-danger mb-4">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
          </div>
        @endif

        <form id="formAuthentication" class="mb-6" action="{{ route('password.update') }}" method="POST">
          @csrf
          <input type="hidden" name="token" value="{{ $request->route('token') }}" />
          <input type="hidden" name="email" value="{{ $request->email }}" />

          <div class="mb-6 form-password-toggle form-control-validation">
            <label class="form-label" for="password">Nueva Contraseña</label>
            <div class="input-group input-group-merge">
              <input type="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password" placeholder="············" autofocus />
              <span class="input-group-text cursor-pointer">
                <i class="icon-base ti tabler-eye-off"></i>
              </span>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-6 form-password-toggle form-control-validation">
            <label class="form-label" for="confirm-password">Confirmar Contraseña</label>
            <div class="input-group input-group-merge">
              <input type="password" id="confirm-password"
                class="form-control" name="password_confirmation" placeholder="············" />
              <span class="input-group-text cursor-pointer">
                <i class="icon-base ti tabler-eye-off"></i>
              </span>
            </div>
          </div>

          <button class="btn btn-primary d-grid w-100 mb-6" type="submit">Establecer nueva contraseña</button>

          <div class="text-center">
            <a href="{{ route('login') }}" class="d-flex justify-content-center align-items-center">
              <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl me-1_5"></i>
              Volver al inicio de sesión
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
