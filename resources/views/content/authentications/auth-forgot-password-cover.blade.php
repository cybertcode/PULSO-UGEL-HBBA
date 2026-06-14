@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Recuperar Contraseña — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap');
:root {
  --ugel-navy:  #0d1b3e;
  --ugel-blue:  #1a3a6e;
  --ugel-gold:  #c8952a;
  --ugel-gold-lt:#f0c96a;
  --ugel-cream: #fdf8f0;
  --ugel-text:  #1c2d4a;
  --ugel-muted: #6b7fa3;
  --ugel-border:#dce4f0;
}
* { box-sizing: border-box; }
.pulso-auth-wrap { min-height:100vh; display:flex; font-family:'DM Sans',sans-serif; }

.pulso-left {
  flex:1; background:var(--ugel-navy); position:relative;
  display:none; overflow:hidden;
}
@media(min-width:1024px){ .pulso-left { display:flex; flex-direction:column; } }
.pulso-left-inner { position:relative; z-index:2; display:flex; flex-direction:column; height:100%; padding:48px 56px; }
.pulso-geo { position:absolute; inset:0; z-index:1;
  background-image:
    repeating-linear-gradient(45deg, rgba(200,149,42,.07) 0, rgba(200,149,42,.07) 1px, transparent 1px, transparent 28px),
    repeating-linear-gradient(-45deg, rgba(200,149,42,.07) 0, rgba(200,149,42,.07) 1px, transparent 1px, transparent 28px); }
.pulso-geo-accent { position:absolute; bottom:-120px; right:-120px; width:480px; height:480px; border-radius:50%;
  background:radial-gradient(circle,rgba(200,149,42,.18) 0%,transparent 70%); z-index:1; }
.pulso-gold-bar { display:flex; gap:6px; margin-bottom:40px; }
.pulso-gold-bar span { height:3px; border-radius:2px; background:var(--ugel-gold); }
.pulso-gold-bar span:first-child{width:40px} .pulso-gold-bar span:nth-child(2){width:16px;opacity:.6} .pulso-gold-bar span:last-child{width:8px;opacity:.35}

.pulso-brand-left { display:flex; align-items:center; gap:14px; }
.pulso-brand-left .brand-logo { width:52px; height:52px; border-radius:12px; overflow:hidden;
  background:rgba(200,149,42,.15); border:1px solid rgba(200,149,42,.3);
  display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.pulso-brand-left .brand-logo img { width:100%; height:100%; object-fit:cover; }
.pulso-brand-left .brand-icon { font-size:24px; color:var(--ugel-gold); }
.pulso-brand-left .brand-name { font-family:'Playfair Display',serif; font-size:20px; color:#fff; font-weight:700; letter-spacing:.3px; }
.pulso-brand-left .brand-sub { font-size:11px; color:rgba(200,149,42,.8); letter-spacing:1.2px; text-transform:uppercase; font-weight:500; margin-top:1px; }

.pulso-hero { flex:1; display:flex; flex-direction:column; justify-content:center; padding:40px 0; }
.pulso-divider-gold { width:48px; height:2px; background:var(--ugel-gold); margin-bottom:24px; border-radius:1px; }
.pulso-hero h2 { font-family:'Playfair Display',serif; font-size:clamp(28px,3vw,38px); color:#fff; font-weight:700; line-height:1.3; margin-bottom:18px; }
.pulso-hero h2 em { font-style:normal; color:var(--ugel-gold-lt); }
.pulso-hero p { font-size:15px; color:rgba(255,255,255,.6); line-height:1.7; max-width:380px; margin-bottom:32px; font-weight:300; }

.pulso-steps { display:flex; flex-direction:column; gap:16px; margin-bottom:36px; }
.pulso-step { display:flex; align-items:flex-start; gap:14px; }
.pulso-step-num { width:28px; height:28px; border-radius:50%; background:rgba(200,149,42,.2);
  border:1px solid rgba(200,149,42,.4); display:flex; align-items:center; justify-content:center;
  font-size:12px; font-weight:700; color:var(--ugel-gold-lt); flex-shrink:0; margin-top:1px; }
.pulso-step-text strong { display:block; font-size:13px; color:#fff; font-weight:600; margin-bottom:2px; }
.pulso-step-text span { font-size:12px; color:rgba(255,255,255,.5); }

.pulso-left-footer { font-size:11px; color:rgba(255,255,255,.3); letter-spacing:.5px;
  border-top:1px solid rgba(255,255,255,.07); padding-top:20px; }

/* Derecho */
.pulso-right { width:100%; background:var(--ugel-cream); display:flex; flex-direction:column;
  align-items:center; justify-content:center; padding:40px 24px; min-height:100vh; position:relative; }
@media(min-width:1024px){ .pulso-right { width:420px; flex-shrink:0; } }
.pulso-right::before { content:''; position:absolute; top:0; left:0; right:0; height:4px;
  background:linear-gradient(90deg,var(--ugel-navy),var(--ugel-gold),var(--ugel-navy)); }

.pulso-form-box { width:100%; max-width:360px; }
.pulso-mobile-brand { display:flex; flex-direction:column; align-items:center; margin-bottom:36px; text-align:center; }
@media(min-width:1024px){ .pulso-mobile-brand { display:none; } }
.pulso-mobile-brand .brand-logo { width:60px; height:60px; border-radius:14px; overflow:hidden;
  background:var(--ugel-navy); display:flex; align-items:center; justify-content:center; margin-bottom:12px; }
.pulso-mobile-brand .brand-logo img { width:100%; height:100%; object-fit:cover; }
.pulso-mobile-brand .brand-logo i { font-size:26px; color:var(--ugel-gold); }
.pulso-mobile-brand h1 { font-family:'Playfair Display',serif; font-size:20px; color:var(--ugel-navy); font-weight:700; }
.pulso-mobile-brand span { font-size:11px; color:var(--ugel-muted); letter-spacing:.8px; text-transform:uppercase; margin-top:2px; }

.pulso-icon-box {
  width:64px; height:64px; border-radius:16px;
  background:linear-gradient(135deg,var(--ugel-navy),var(--ugel-blue));
  display:flex; align-items:center; justify-content:center;
  margin-bottom:20px; box-shadow:0 8px 24px rgba(13,27,62,.2);
}
.pulso-icon-box i { font-size:28px; color:var(--ugel-gold-lt); }

.pulso-form-header { margin-bottom:24px; }
.pulso-form-header h3 { font-family:'Playfair Display',serif; font-size:24px; color:var(--ugel-text); font-weight:700; margin-bottom:6px; }
.pulso-form-header p { font-size:13.5px; color:var(--ugel-muted); line-height:1.6; }

.pulso-alert-success { background:#edfcf2; border:1px solid #a6f4c5; border-radius:10px;
  padding:14px 16px; font-size:13.5px; color:#1a7f4b; margin-bottom:20px; }
.pulso-alert-success strong { display:block; margin-bottom:4px; }
.pulso-alert-danger { background:#fff1f1; border:1px solid #fcc; border-radius:8px;
  padding:11px 14px; font-size:13px; color:#c0392b; margin-bottom:18px; }

.pulso-field { margin-bottom:18px; }
.pulso-field label { display:block; font-size:12.5px; font-weight:600; color:var(--ugel-text);
  margin-bottom:6px; letter-spacing:.2px; text-transform:uppercase; }
.pulso-input { width:100%; padding:11px 14px; border:1.5px solid var(--ugel-border); border-radius:8px;
  font-size:14px; font-family:'DM Sans',sans-serif; color:var(--ugel-text); background:#fff;
  transition:border-color .2s,box-shadow .2s; outline:none; }
.pulso-input:focus { border-color:var(--ugel-blue); box-shadow:0 0 0 3px rgba(26,58,110,.1); }
.pulso-input.is-invalid { border-color:#e74c3c; }
.pulso-input::placeholder { color:#b0baca; }
.invalid-feedback { color:#e74c3c; font-size:12px; margin-top:4px; display:block; }

.pulso-btn-primary { width:100%; padding:13px; background:var(--ugel-navy); color:#fff; border:none;
  border-radius:8px; font-size:14px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer;
  letter-spacing:.3px; transition:background .2s,box-shadow .2s; }
.pulso-btn-primary:hover { background:var(--ugel-blue); box-shadow:0 4px 16px rgba(13,27,62,.25); }

.pulso-link-back { display:flex; align-items:center; justify-content:center; gap:6px;
  margin-top:20px; font-size:13px; color:var(--ugel-muted); text-decoration:none;
  transition:color .2s; }
.pulso-link-back:hover { color:var(--ugel-text); }
.pulso-link-back i { font-size:14px; }
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
@endsection

@section('content')
@php $ci = \App\Models\ConfiguracionInstitucional::cached(); @endphp

<div class="pulso-auth-wrap">
  <div class="pulso-left">
    <div class="pulso-geo"></div>
    <div class="pulso-geo-accent"></div>
    <div class="pulso-left-inner">
      <div class="pulso-gold-bar"><span></span><span></span><span></span></div>
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
        <h2>Recupera tu<br><em>acceso</em> en<br>3 pasos</h2>
        <p>El proceso es rápido y seguro. Recibirás un enlace de restablecimiento directamente en tu correo institucional.</p>
        <div class="pulso-steps">
          <div class="pulso-step">
            <div class="pulso-step-num">1</div>
            <div class="pulso-step-text">
              <strong>Ingresa tu correo</strong>
              <span>El correo con el que accedes al sistema</span>
            </div>
          </div>
          <div class="pulso-step">
            <div class="pulso-step-num">2</div>
            <div class="pulso-step-text">
              <strong>Revisa tu bandeja</strong>
              <span>Recibirás un enlace en los próximos minutos</span>
            </div>
          </div>
          <div class="pulso-step">
            <div class="pulso-step-num">3</div>
            <div class="pulso-step-text">
              <strong>Establece tu nueva contraseña</strong>
              <span>Mínimo 8 caracteres con mayúsculas y números</span>
            </div>
          </div>
        </div>
      </div>
      <div class="pulso-left-footer">
        {{ $ci?->nombre_institucion ?? 'UGEL Huacaybamba' }} &bull; Perú
      </div>
    </div>
  </div>

  <div class="pulso-right">
    <div class="pulso-form-box">
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

      <div class="pulso-icon-box">
        <i class="ti tabler-lock-open"></i>
      </div>

      <div class="pulso-form-header">
        <h3>Recuperar Contraseña</h3>
        <p>Ingresa tu correo institucional y te enviaremos instrucciones para restablecer tu contraseña.</p>
      </div>

      @if(session('status'))
        <div class="pulso-alert-success">
          <strong><i class="ti tabler-circle-check"></i> Correo enviado correctamente</strong>
          {{ session('status') }}
        </div>
      @endif

      @if($errors->any())
        <div class="pulso-alert-danger">
          @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
      @endif

      <form action="{{ route('password.email') }}" method="POST" id="formAuthentication">
        @csrf
        <div class="pulso-field">
          <label for="email">Correo electrónico institucional</label>
          <input type="email" id="email" name="email"
            class="pulso-input @error('email') is-invalid @enderror"
            placeholder="usuario@ugelhuacaybamba.edu.pe"
            value="{{ old('email') }}" autofocus />
          @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="pulso-btn-primary">
          <i class="ti tabler-send me-1" style="font-size:15px;vertical-align:-2px"></i>
          Enviar instrucciones de recuperación
        </button>
      </form>

      <a href="{{ route('login') }}" class="pulso-link-back">
        <i class="ti tabler-arrow-left"></i>
        Volver al inicio de sesión
      </a>
    </div>
  </div>
</div>
@endsection
