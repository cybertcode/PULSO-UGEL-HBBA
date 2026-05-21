@extends('layouts.layoutMaster')

@php
$breadcrumbs = [
  ['link' => '/', 'name' => 'Inicio'],
  ['name' => 'Mi Perfil'],
];
@endphp

@section('title', 'Mi Perfil - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages-profile-account.js'])
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">

    <!-- Tabs de navegación -->
    <div class="nav-align-top">
      <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2" id="profileTabs">
        <li class="nav-item">
          <a class="nav-link active" href="#tab-cuenta" data-bs-toggle="pill">
            <i class="icon-base ti tabler-user-circle icon-sm me-1_5"></i> Mi Cuenta
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#tab-seguridad" data-bs-toggle="pill">
            <i class="icon-base ti tabler-lock icon-sm me-1_5"></i> Seguridad
          </a>
        </li>
        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <li class="nav-item">
          <a class="nav-link" href="#tab-2fa" data-bs-toggle="pill">
            <i class="icon-base ti tabler-shield-check icon-sm me-1_5"></i> Verificación 2FA
          </a>
        </li>
        @endif
        <li class="nav-item">
          <a class="nav-link" href="#tab-sesiones" data-bs-toggle="pill">
            <i class="icon-base ti tabler-devices icon-sm me-1_5"></i> Sesiones
          </a>
        </li>
      </ul>
    </div>

    <div class="tab-content p-0">

      <!-- TAB: MI CUENTA -->
      <div class="tab-pane fade show active" id="tab-cuenta">

        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
        <!-- Foto de perfil -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Información del Perfil</h5>
            <small class="text-muted">Actualiza tu foto, nombre y correo electrónico</small>
          </div>
          <div class="card-body">
            <!-- Livewire: información de perfil -->
            @livewire('profile.update-profile-information-form')
          </div>
        </div>
        @endif

        <!-- Eliminar cuenta -->
        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <div class="card border-danger">
          <h5 class="card-header text-danger">
            <i class="icon-base ti tabler-alert-triangle me-1"></i>Eliminar Cuenta
          </h5>
          <div class="card-body">
            <div class="alert alert-warning mb-4">
              <h6 class="alert-heading mb-1">¿Estás seguro de que deseas eliminar tu cuenta?</h6>
              <p class="mb-0">Una vez eliminada, no hay vuelta atrás. Por favor, asegúrate de estar seguro.</p>
            </div>
            @livewire('profile.delete-user-form')
          </div>
        </div>
        @endif
      </div>

      <!-- TAB: SEGURIDAD -->
      <div class="tab-pane fade" id="tab-seguridad">

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div class="card mb-6">
          <h5 class="card-header">Cambiar Contraseña</h5>
          <div class="card-body pt-1">
            @livewire('profile.update-password-form')
          </div>
        </div>
        @endif

        <!-- Sesiones de navegador en seguridad también -->
        <div class="card">
          <h5 class="card-header">Sesiones Activas</h5>
          <div class="card-body">
            @livewire('profile.logout-other-browser-sessions-form')
          </div>
        </div>
      </div>

      <!-- TAB: 2FA -->
      @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
      <div class="tab-pane fade" id="tab-2fa">
        <div class="card">
          <h5 class="card-header">
            <i class="icon-base ti tabler-shield-lock me-1"></i>Autenticación en Dos Pasos
          </h5>
          <div class="card-body">
            <p class="text-muted mb-4">
              Agrega una capa adicional de seguridad a tu cuenta. Al activarla, se te pedirá un código de
              tu aplicación de autenticación (Google Authenticator o similar) al iniciar sesión.
            </p>
            @livewire('profile.two-factor-authentication-form')
          </div>
        </div>
      </div>
      @endif

      <!-- TAB: SESIONES -->
      <div class="tab-pane fade" id="tab-sesiones">
        <div class="card">
          <h5 class="card-header">
            <i class="icon-base ti tabler-devices me-1"></i>Sesiones en Otros Dispositivos
          </h5>
          <div class="card-body">
            @livewire('profile.logout-other-browser-sessions-form')
          </div>
        </div>
      </div>

    </div>{{-- /tab-content --}}
  </div>
</div>
@endsection
