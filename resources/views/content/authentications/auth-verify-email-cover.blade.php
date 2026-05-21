@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Verificar Correo - PULSO UGEL')

@section('page-style')
@vite('resources/assets/vendor/scss/pages/page-auth.scss')
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
    <span class="app-brand-logo demo">@include('_partials.macros')</span>
    <span class="app-brand-text demo text-heading fw-bold">PULSO UGEL</span>
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
        <p class="mb-4">
          Se envió un enlace de activación a:<br/>
          <span class="fw-medium">{{ auth()->user()->email ?? 'tu.correo@ugel.gob.pe' }}</span><br/>
          Sigue el enlace del mensaje para continuar.
        </p>

        @if (session('status') == 'verification-link-sent')
          <div class="alert alert-success mb-4">
            Se envió un nuevo enlace de verificación a tu correo electrónico.
          </div>
        @endif

        <div class="d-flex gap-3 mb-6">
          <form method="POST" action="{{ route('verification.send') }}" class="flex-grow-1">
            @csrf
            <button type="submit" class="btn btn-primary w-100">Reenviar correo</button>
          </form>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-label-secondary">Cerrar sesión</button>
          </form>
        </div>

        <p class="text-center text-muted small mb-0">
          <i class="ti tabler-shield-check me-1 text-success"></i>
          UGEL Huacaybamba · Sistema de Control Interno
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
