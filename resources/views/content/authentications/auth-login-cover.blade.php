@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Iniciar Sesión — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap');

:root {
  --ugel-navy:    #0d1b3e;
  --ugel-blue:    #1a3a6e;
  --ugel-gold:    #c8952a;
  --ugel-gold-lt: #f0c96a;
  --ugel-cream:   #fdf8f0;
  --ugel-text:    #1c2d4a;
  --ugel-muted:   #6b7fa3;
  --ugel-border:  #dce4f0;
}

* { box-sizing: border-box; }

.pulso-auth-wrap {
  min-height: 100vh;
  display: flex;
  font-family: 'DM Sans', sans-serif;
}

/* ── Panel izquierdo ─────────────────────────────── */
.pulso-left {
  flex: 1;
  background: var(--ugel-navy);
  position: relative;
  display: none;
  overflow: hidden;
}
@media (min-width: 1024px) { .pulso-left { display: flex; flex-direction: column; } }

.pulso-left-inner {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 48px 56px;
}

/* Patrón geométrico andino */
.pulso-geo {
  position: absolute;
  inset: 0;
  z-index: 1;
  background-image:
    repeating-linear-gradient(45deg,  rgba(200,149,42,.07) 0px, rgba(200,149,42,.07) 1px, transparent 1px, transparent 28px),
    repeating-linear-gradient(-45deg, rgba(200,149,42,.07) 0px, rgba(200,149,42,.07) 1px, transparent 1px, transparent 28px);
}
.pulso-geo-accent {
  position: absolute;
  bottom: -120px;
  right: -120px;
  width: 480px;
  height: 480px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(200,149,42,.18) 0%, transparent 70%);
  z-index: 1;
}
.pulso-geo-top {
  position: absolute;
  top: -80px;
  left: -80px;
  width: 300px;
  height: 300px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(26,58,110,.6) 0%, transparent 70%);
  z-index: 1;
}

/* Barra dorada superior */
.pulso-gold-bar {
  display: flex;
  gap: 6px;
  margin-bottom: 40px;
}
.pulso-gold-bar span {
  height: 3px;
  border-radius: 2px;
  background: var(--ugel-gold);
}
.pulso-gold-bar span:first-child { width: 40px; }
.pulso-gold-bar span:nth-child(2) { width: 16px; opacity: .6; }
.pulso-gold-bar span:last-child   { width: 8px;  opacity: .35; }

.pulso-brand-left {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 0;
}
.pulso-brand-left .brand-logo {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  overflow: hidden;
  background: rgba(200,149,42,.15);
  border: 1px solid rgba(200,149,42,.3);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.pulso-brand-left .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
.pulso-brand-left .brand-icon {
  font-size: 24px;
  color: var(--ugel-gold);
}
.pulso-brand-left .brand-name {
  font-family: 'Playfair Display', serif;
  font-size: 20px;
  color: #fff;
  font-weight: 700;
  line-height: 1.2;
  letter-spacing: .3px;
}
.pulso-brand-left .brand-sub {
  font-size: 11px;
  color: rgba(200,149,42,.8);
  letter-spacing: 1.2px;
  text-transform: uppercase;
  font-weight: 500;
  margin-top: 1px;
}

.pulso-hero {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 40px 0;
}

.pulso-divider-gold {
  width: 48px;
  height: 2px;
  background: var(--ugel-gold);
  margin-bottom: 24px;
  border-radius: 1px;
}

.pulso-hero h2 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(30px, 3vw, 42px);
  color: #fff;
  font-weight: 700;
  line-height: 1.25;
  margin-bottom: 20px;
  letter-spacing: -.3px;
}
.pulso-hero h2 em {
  font-style: normal;
  color: var(--ugel-gold-lt);
}

.pulso-hero p {
  font-size: 15px;
  color: rgba(255,255,255,.65);
  line-height: 1.7;
  max-width: 380px;
  margin-bottom: 40px;
  font-weight: 300;
}

.pulso-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 40px;
}
.pulso-stat {
  background: rgba(255,255,255,.05);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 10px;
  padding: 16px 14px;
  text-align: center;
}
.pulso-stat .val {
  font-family: 'Playfair Display', serif;
  font-size: 24px;
  font-weight: 700;
  color: var(--ugel-gold-lt);
  display: block;
  line-height: 1;
  margin-bottom: 4px;
}
.pulso-stat .lbl {
  font-size: 10px;
  color: rgba(255,255,255,.45);
  text-transform: uppercase;
  letter-spacing: .8px;
  font-weight: 500;
}

.pulso-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.pulso-badge {
  display: flex;
  align-items: center;
  gap: 7px;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 50px;
  padding: 7px 14px;
  font-size: 12px;
  color: rgba(255,255,255,.7);
  font-weight: 400;
}
.pulso-badge i { color: var(--ugel-gold); font-size: 13px; }

.pulso-left-footer {
  font-size: 11px;
  color: rgba(255,255,255,.3);
  letter-spacing: .5px;
  border-top: 1px solid rgba(255,255,255,.07);
  padding-top: 20px;
}

/* ── Panel derecho ─────────────────────────────────── */
.pulso-right {
  width: 100%;
  background: var(--ugel-cream);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
  min-height: 100vh;
  position: relative;
}
@media (min-width: 1024px) {
  .pulso-right { width: 420px; flex-shrink: 0; }
}

.pulso-right::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--ugel-navy), var(--ugel-gold), var(--ugel-navy));
}

.pulso-form-box {
  width: 100%;
  max-width: 360px;
}

.pulso-mobile-brand {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 36px;
  text-align: center;
}
@media (min-width: 1024px) { .pulso-mobile-brand { display: none; } }

.pulso-mobile-brand .brand-logo {
  width: 60px;
  height: 60px;
  border-radius: 14px;
  overflow: hidden;
  background: var(--ugel-navy);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
}
.pulso-mobile-brand .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
.pulso-mobile-brand .brand-logo i { font-size: 26px; color: var(--ugel-gold); }
.pulso-mobile-brand h1 {
  font-family: 'Playfair Display', serif;
  font-size: 20px;
  color: var(--ugel-navy);
  font-weight: 700;
}
.pulso-mobile-brand span {
  font-size: 11px;
  color: var(--ugel-muted);
  letter-spacing: .8px;
  text-transform: uppercase;
  margin-top: 2px;
}

.pulso-form-header { margin-bottom: 28px; }
.pulso-form-header h3 {
  font-family: 'Playfair Display', serif;
  font-size: 26px;
  color: var(--ugel-text);
  font-weight: 700;
  margin-bottom: 6px;
  letter-spacing: -.3px;
}
.pulso-form-header p {
  font-size: 13.5px;
  color: var(--ugel-muted);
  line-height: 1.5;
  font-weight: 400;
}

.pulso-alert-success {
  background: #edfcf2;
  border: 1px solid #a6f4c5;
  border-radius: 8px;
  padding: 11px 14px;
  font-size: 13px;
  color: #1a7f4b;
  margin-bottom: 18px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pulso-alert-danger {
  background: #fff1f1;
  border: 1px solid #fcc;
  border-radius: 8px;
  padding: 11px 14px;
  font-size: 13px;
  color: #c0392b;
  margin-bottom: 18px;
}

.pulso-field { margin-bottom: 18px; }
.pulso-field label {
  display: block;
  font-size: 12.5px;
  font-weight: 600;
  color: var(--ugel-text);
  margin-bottom: 6px;
  letter-spacing: .2px;
  text-transform: uppercase;
}
.pulso-input {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid var(--ugel-border);
  border-radius: 8px;
  font-size: 14px;
  font-family: 'DM Sans', sans-serif;
  color: var(--ugel-text);
  background: #fff;
  transition: border-color .2s, box-shadow .2s;
  outline: none;
}
.pulso-input:focus {
  border-color: var(--ugel-blue);
  box-shadow: 0 0 0 3px rgba(26,58,110,.1);
}
.pulso-input.is-invalid {
  border-color: #e74c3c;
  box-shadow: 0 0 0 3px rgba(231,76,60,.08);
}
.pulso-input::placeholder { color: #b0baca; }

.pulso-field-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 6px;
}
.pulso-field-row label {
  font-size: 12.5px;
  font-weight: 600;
  color: var(--ugel-text);
  letter-spacing: .2px;
  text-transform: uppercase;
}
.pulso-field-row a {
  font-size: 12px;
  color: var(--ugel-gold);
  text-decoration: none;
  font-weight: 500;
}
.pulso-field-row a:hover { text-decoration: underline; }

.pulso-input-group {
  position: relative;
}
.pulso-input-group .pulso-input { padding-right: 42px; }
.pulso-eye-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: var(--ugel-muted);
  padding: 4px;
  line-height: 1;
}
.pulso-eye-btn:hover { color: var(--ugel-text); }

.pulso-check-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 22px;
}
.pulso-check-row input[type=checkbox] {
  width: 16px;
  height: 16px;
  accent-color: var(--ugel-navy);
  cursor: pointer;
}
.pulso-check-row label {
  font-size: 13px;
  color: var(--ugel-muted);
  cursor: pointer;
}

.pulso-btn-primary {
  width: 100%;
  padding: 13px;
  background: var(--ugel-navy);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  letter-spacing: .3px;
  transition: background .2s, transform .1s, box-shadow .2s;
  position: relative;
  overflow: hidden;
}
.pulso-btn-primary::after {
  content: '';
  position: absolute;
  top: 0; left: -100%;
  width: 60%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.08), transparent);
  transition: left .4s ease;
}
.pulso-btn-primary:hover { background: var(--ugel-blue); box-shadow: 0 4px 16px rgba(13,27,62,.25); }
.pulso-btn-primary:hover::after { left: 160%; }
.pulso-btn-primary:active { transform: translateY(1px); }

.pulso-sep {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 22px 0 18px;
}
.pulso-sep::before, .pulso-sep::after {
  content: ''; flex: 1;
  height: 1px;
  background: var(--ugel-border);
}
.pulso-sep span {
  font-size: 11px;
  color: var(--ugel-muted);
  letter-spacing: .5px;
  text-transform: uppercase;
  white-space: nowrap;
}

.pulso-footer-note {
  text-align: center;
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid var(--ugel-border);
}
.pulso-footer-note p {
  font-size: 12px;
  color: var(--ugel-muted);
  line-height: 1.6;
}
.pulso-footer-note i { color: #27ae60; vertical-align: -1px; }

.invalid-feedback {
  color: #e74c3c;
  font-size: 12px;
  margin-top: 4px;
  display: block;
}
</style>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Toggle mostrar/ocultar contraseña
  document.querySelectorAll('.pulso-eye-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const input = this.closest('.pulso-input-group').querySelector('input');
      const icon  = this.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('tabler-eye-off', 'tabler-eye');
      } else {
        input.type = 'password';
        icon.classList.replace('tabler-eye', 'tabler-eye-off');
      }
    });
  });
});
</script>
@endsection

@section('content')
@php $ci = \App\Models\ConfiguracionInstitucional::cached(); @endphp

<div class="pulso-auth-wrap">

  {{-- ── Panel izquierdo ── --}}
  <div class="pulso-left">
    <div class="pulso-geo"></div>
    <div class="pulso-geo-accent"></div>
    <div class="pulso-geo-top"></div>

    <div class="pulso-left-inner">
      <div class="pulso-gold-bar">
        <span></span><span></span><span></span>
      </div>

      <div class="pulso-brand-left">
        <div class="brand-logo">
          @if(!empty($ci?->logo_ruta))
            <img src="{{ Storage::url($ci->logo_ruta) }}" alt="logo">
          @else
            <i class="ti tabler-building-community brand-icon"></i>
          @endif
        </div>
        <div>
          <div class="brand-name">{{ $ci?->sigla ?? 'PULSO UGEL' }}</div>
          <div class="brand-sub">Sistema Institucional</div>
        </div>
      </div>

      <div class="pulso-hero">
        <div class="pulso-divider-gold"></div>
        <h2>
          Monitoreo de<br>
          <em>Control Interno</em><br>
          e Integridad
        </h2>
        <p>
          Plataforma oficial de seguimiento, registro de actividades y
          cumplimiento institucional de la
          {{ $ci?->nombre_institucion ?? 'UGEL Huacaybamba' }}.
        </p>

        <div class="pulso-stats">
          <div class="pulso-stat">
            <span class="val">SCI</span>
            <span class="lbl">Control Interno</span>
          </div>
          <div class="pulso-stat">
            <span class="val">PEI</span>
            <span class="lbl">Integridad</span>
          </div>
          <div class="pulso-stat">
            <span class="val">DRE</span>
            <span class="lbl">Huánuco</span>
          </div>
        </div>

        <div class="pulso-badges">
          <span class="pulso-badge">
            <i class="ti tabler-shield-check"></i> Acceso seguro
          </span>
          <span class="pulso-badge">
            <i class="ti tabler-lock"></i> Datos protegidos
          </span>
          <span class="pulso-badge">
            <i class="ti tabler-certificate"></i> Cumplimiento MINEDU
          </span>
        </div>
      </div>

      <div class="pulso-left-footer">
        {{ $ci?->nombre_institucion ?? 'UGEL Huacaybamba' }}
        @if($ci?->departamento) &bull; {{ $ci->departamento }} @endif
        &bull; Perú
      </div>
    </div>
  </div>

  {{-- ── Panel derecho ── --}}
  <div class="pulso-right">
    <div class="pulso-form-box">

      {{-- Logo en móvil --}}
      <div class="pulso-mobile-brand">
        <div class="brand-logo">
          @if(!empty($ci?->logo_ruta))
            <img src="{{ Storage::url($ci->logo_ruta) }}" alt="logo">
          @else
            <i class="ti tabler-building-community"></i>
          @endif
        </div>
        <h1>{{ $ci?->sigla ?? 'PULSO UGEL' }}</h1>
        <span>Sistema Institucional</span>
      </div>

      <div class="pulso-form-header">
        <h3>Iniciar Sesión</h3>
        <p>Ingresa tus credenciales para acceder al sistema</p>
      </div>

      @if(session('status'))
        <div class="pulso-alert-success">
          <i class="ti tabler-circle-check"></i> {{ session('status') }}
        </div>
      @endif

      @if($errors->any())
        <div class="pulso-alert-danger">
          @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
      @endif

      <form action="{{ route('login') }}" method="POST" id="formAuthentication">
        @csrf

        <div class="pulso-field">
          <label for="email">Correo electrónico</label>
          <input type="email" id="email" name="email" class="pulso-input @error('email') is-invalid @enderror"
            placeholder="usuario@ugelhuacaybamba.edu.pe"
            value="{{ old('email') }}" autofocus autocomplete="email" />
          @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>

        <div class="pulso-field">
          <div class="pulso-field-row">
            <label for="password">Contraseña</label>
            @if(Route::has('password.request'))
              <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            @endif
          </div>
          <div class="pulso-input-group">
            <input type="password" id="password" name="password"
              class="pulso-input @error('password') is-invalid @enderror"
              placeholder="············" autocomplete="current-password" />
            <button type="button" class="pulso-eye-btn" tabindex="-1">
              <i class="ti tabler-eye-off" style="font-size:16px"></i>
            </button>
          </div>
          @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>

        <div class="pulso-check-row">
          <input type="checkbox" id="remember-me" name="remember" />
          <label for="remember-me">Mantener sesión iniciada</label>
        </div>

        <button type="submit" class="pulso-btn-primary">
          <i class="ti tabler-login me-1" style="font-size:15px;vertical-align:-2px"></i>
          Ingresar al Sistema
        </button>
      </form>

      <div class="pulso-sep"><span>UGEL Huacaybamba</span></div>

      <div class="pulso-footer-note">
        <p>
          <i class="ti tabler-shield-check"></i>
          Sistema de Monitoreo de Control Interno e Integridad Institucional<br>
          <span style="color:#b0baca;font-size:11px;margin-top:4px;display:inline-block">
            {{ $ci?->nombre_institucion ?? 'UGEL Huacaybamba' }}
            @if($ci?->distrito || $ci?->provincia)
              &bull; {{ implode(', ', array_filter([$ci->distrito ?? null, $ci->provincia ?? null])) }}
            @endif
          </span>
        </p>
      </div>

    </div>
  </div>

</div>
@endsection
