@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Verificación en Dos Pasos — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap');
:root{--ugel-navy:#0d1b3e;--ugel-blue:#1a3a6e;--ugel-gold:#c8952a;--ugel-gold-lt:#f0c96a;--ugel-cream:#fdf8f0;--ugel-text:#1c2d4a;--ugel-muted:#6b7fa3;--ugel-border:#dce4f0}
*{box-sizing:border-box}
.pulso-auth-wrap{min-height:100vh;display:flex;font-family:'DM Sans',sans-serif}
.pulso-left{flex:1;background:var(--ugel-navy);position:relative;display:none;overflow:hidden}
@media(min-width:1024px){.pulso-left{display:flex;flex-direction:column}}
.pulso-left-inner{position:relative;z-index:2;display:flex;flex-direction:column;height:100%;padding:48px 56px}
.pulso-geo{position:absolute;inset:0;z-index:1;
  background-image:repeating-linear-gradient(45deg,rgba(200,149,42,.07) 0,rgba(200,149,42,.07) 1px,transparent 1px,transparent 28px),
  repeating-linear-gradient(-45deg,rgba(200,149,42,.07) 0,rgba(200,149,42,.07) 1px,transparent 1px,transparent 28px)}
.pulso-geo-accent{position:absolute;bottom:-120px;right:-120px;width:480px;height:480px;border-radius:50%;background:radial-gradient(circle,rgba(200,149,42,.18) 0%,transparent 70%);z-index:1}
.pulso-gold-bar{display:flex;gap:6px;margin-bottom:40px}
.pulso-gold-bar span{height:3px;border-radius:2px;background:var(--ugel-gold)}
.pulso-gold-bar span:first-child{width:40px}.pulso-gold-bar span:nth-child(2){width:16px;opacity:.6}.pulso-gold-bar span:last-child{width:8px;opacity:.35}
.pulso-brand-left{display:flex;align-items:center;gap:14px}
.pulso-brand-left .brand-logo{width:52px;height:52px;border-radius:12px;overflow:hidden;background:rgba(200,149,42,.15);border:1px solid rgba(200,149,42,.3);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pulso-brand-left .brand-logo img{width:100%;height:100%;object-fit:cover}
.pulso-brand-left .brand-icon{font-size:24px;color:var(--ugel-gold)}
.pulso-brand-left .brand-name{font-family:'Playfair Display',serif;font-size:20px;color:#fff;font-weight:700}
.pulso-brand-left .brand-sub{font-size:11px;color:rgba(200,149,42,.8);letter-spacing:1.2px;text-transform:uppercase;font-weight:500;margin-top:1px}
.pulso-hero{flex:1;display:flex;flex-direction:column;justify-content:center;padding:40px 0}
.pulso-divider-gold{width:48px;height:2px;background:var(--ugel-gold);margin-bottom:24px;border-radius:1px}
.pulso-hero h2{font-family:'Playfair Display',serif;font-size:clamp(28px,3vw,38px);color:#fff;font-weight:700;line-height:1.3;margin-bottom:18px}
.pulso-hero h2 em{font-style:normal;color:var(--ugel-gold-lt)}
.pulso-hero p{font-size:15px;color:rgba(255,255,255,.6);line-height:1.7;max-width:380px;font-weight:300}
.pulso-security-badge{display:flex;align-items:center;gap:12px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:14px 16px;margin-top:28px}
.pulso-security-badge i{font-size:22px;color:var(--ugel-gold);flex-shrink:0}
.pulso-security-badge span{font-size:13px;color:rgba(255,255,255,.65);line-height:1.5}
.pulso-left-footer{font-size:11px;color:rgba(255,255,255,.3);letter-spacing:.5px;border-top:1px solid rgba(255,255,255,.07);padding-top:20px}

.pulso-right{width:100%;background:var(--ugel-cream);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 24px;min-height:100vh;position:relative}
@media(min-width:1024px){.pulso-right{width:420px;flex-shrink:0}}
.pulso-right::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--ugel-navy),var(--ugel-gold),var(--ugel-navy))}
.pulso-form-box{width:100%;max-width:360px}
.pulso-mobile-brand{display:flex;flex-direction:column;align-items:center;margin-bottom:32px;text-align:center}
@media(min-width:1024px){.pulso-mobile-brand{display:none}}
.pulso-mobile-brand .brand-logo{width:56px;height:56px;border-radius:14px;overflow:hidden;background:var(--ugel-navy);display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.pulso-mobile-brand .brand-logo img{width:100%;height:100%;object-fit:cover}
.pulso-mobile-brand .brand-logo i{font-size:24px;color:var(--ugel-gold)}
.pulso-mobile-brand h1{font-family:'Playfair Display',serif;font-size:19px;color:var(--ugel-navy);font-weight:700}

.pulso-icon-box{width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,var(--ugel-navy),var(--ugel-blue));display:flex;align-items:center;justify-content:center;margin-bottom:20px;box-shadow:0 8px 24px rgba(13,27,62,.2)}
.pulso-icon-box i{font-size:28px;color:var(--ugel-gold-lt)}
.pulso-form-header{margin-bottom:24px}
.pulso-form-header h3{font-family:'Playfair Display',serif;font-size:24px;color:var(--ugel-text);font-weight:700;margin-bottom:6px}
.pulso-form-header p{font-size:13.5px;color:var(--ugel-muted);line-height:1.6}

.pulso-otp-wrap{display:flex;gap:8px;justify-content:center;margin-bottom:24px}
.pulso-otp-input{width:46px;height:52px;text-align:center;font-size:20px;font-weight:700;
  font-family:'Playfair Display',serif;color:var(--ugel-text);
  border:1.5px solid var(--ugel-border);border-radius:10px;background:#fff;
  transition:border-color .2s,box-shadow .2s;outline:none}
.pulso-otp-input:focus{border-color:var(--ugel-blue);box-shadow:0 0 0 3px rgba(26,58,110,.1)}
.pulso-otp-input.filled{border-color:var(--ugel-navy)}

.pulso-alert-danger{background:#fff1f1;border:1px solid #fcc;border-radius:8px;padding:11px 14px;font-size:13px;color:#c0392b;margin-bottom:18px}
.invalid-feedback{color:#e74c3c;font-size:12px;margin-top:4px;display:block}

.pulso-btn-primary{width:100%;padding:13px;background:var(--ugel-navy);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;letter-spacing:.3px;transition:background .2s,box-shadow .2s}
.pulso-btn-primary:hover{background:var(--ugel-blue);box-shadow:0 4px 16px rgba(13,27,62,.25)}
.pulso-resend-row{text-align:center;margin-top:16px;font-size:13px;color:var(--ugel-muted)}
.pulso-resend-row a{color:var(--ugel-gold);text-decoration:none;font-weight:500}
.pulso-resend-row a:hover{text-decoration:underline}
.pulso-link-back{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:20px;font-size:13px;color:var(--ugel-muted);text-decoration:none;transition:color .2s}
.pulso-link-back:hover{color:var(--ugel-text)}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const inputs = document.querySelectorAll('.pulso-otp-input');
  const hiddenOtp = document.getElementById('otp-hidden');

  inputs.forEach((input, idx) => {
    input.addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0,1);
      if (this.value) {
        this.classList.add('filled');
        if (idx < inputs.length - 1) inputs[idx + 1].focus();
      } else {
        this.classList.remove('filled');
      }
      hiddenOtp.value = Array.from(inputs).map(i => i.value).join('');
    });

    input.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && !this.value && idx > 0) {
        inputs[idx - 1].focus();
        inputs[idx - 1].value = '';
        inputs[idx - 1].classList.remove('filled');
      }
    });

    input.addEventListener('paste', function (e) {
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
      text.split('').forEach((ch, i) => {
        if (inputs[i]) { inputs[i].value = ch; inputs[i].classList.add('filled'); }
      });
      hiddenOtp.value = text;
      if (inputs[text.length]) inputs[text.length].focus();
    });
  });
});
</script>
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
        <h2>Verificación<br>en <em>dos pasos</em><br>activada</h2>
        <p>La autenticación en dos pasos protege el acceso a la información institucional incluso si tu contraseña es comprometida.</p>
        <div class="pulso-security-badge">
          <i class="ti tabler-shield-check"></i>
          <span>Este sistema cumple con los estándares de seguridad establecidos por la CGR y el MINEDU.</span>
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
      </div>

      <div class="pulso-icon-box">
        <i class="ti tabler-device-mobile-message"></i>
      </div>

      <div class="pulso-form-header">
        <h3>Verificación en Dos Pasos</h3>
        <p>Ingresa el código de 6 dígitos de tu aplicación autenticadora o el código enviado a tu dispositivo.</p>
      </div>

      @if($errors->any())
        <div class="pulso-alert-danger">
          @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
      @endif

      <form id="twoStepsForm" action="{{ route('two-factor.login') }}" method="POST">
        @csrf
        <div class="pulso-otp-wrap">
          @for($i = 0; $i < 6; $i++)
            <input type="tel" class="pulso-otp-input" maxlength="1" inputmode="numeric"
              {{ $i === 0 ? 'autofocus' : '' }} />
          @endfor
        </div>
        <input type="hidden" name="code" id="otp-hidden" />

        <button type="submit" class="pulso-btn-primary">
          <i class="ti tabler-check me-1" style="font-size:15px;vertical-align:-2px"></i>
          Verificar e ingresar
        </button>
      </form>

      <div class="pulso-resend-row">
        ¿No recibiste el código?
        <a href="{{ route('two-factor.login') }}">Usar código de recuperación</a>
      </div>

      <a href="{{ route('login') }}" class="pulso-link-back">
        <i class="ti tabler-arrow-left"></i>
        Volver al inicio de sesión
      </a>
    </div>
  </div>
</div>
@endsection
