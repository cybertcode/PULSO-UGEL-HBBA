@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Nueva Contraseña — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap');
:root {
  --ugel-navy:#0d1b3e; --ugel-blue:#1a3a6e; --ugel-gold:#c8952a;
  --ugel-gold-lt:#f0c96a; --ugel-cream:#fdf8f0;
  --ugel-text:#1c2d4a; --ugel-muted:#6b7fa3; --ugel-border:#dce4f0;
}
*{box-sizing:border-box}
.pulso-auth-wrap{min-height:100vh;display:flex;font-family:'DM Sans',sans-serif}
.pulso-left{flex:1;background:var(--ugel-navy);position:relative;display:none;overflow:hidden}
@media(min-width:1024px){.pulso-left{display:flex;flex-direction:column}}
.pulso-left-inner{position:relative;z-index:2;display:flex;flex-direction:column;height:100%;padding:48px 56px}
.pulso-geo{position:absolute;inset:0;z-index:1;
  background-image:repeating-linear-gradient(45deg,rgba(200,149,42,.07) 0,rgba(200,149,42,.07) 1px,transparent 1px,transparent 28px),
  repeating-linear-gradient(-45deg,rgba(200,149,42,.07) 0,rgba(200,149,42,.07) 1px,transparent 1px,transparent 28px)}
.pulso-geo-accent{position:absolute;bottom:-120px;right:-120px;width:480px;height:480px;border-radius:50%;
  background:radial-gradient(circle,rgba(200,149,42,.18) 0%,transparent 70%);z-index:1}
.pulso-gold-bar{display:flex;gap:6px;margin-bottom:40px}
.pulso-gold-bar span{height:3px;border-radius:2px;background:var(--ugel-gold)}
.pulso-gold-bar span:first-child{width:40px}.pulso-gold-bar span:nth-child(2){width:16px;opacity:.6}.pulso-gold-bar span:last-child{width:8px;opacity:.35}
.pulso-brand-left{display:flex;align-items:center;gap:14px}
.pulso-brand-left .brand-logo{width:52px;height:52px;border-radius:12px;overflow:hidden;
  background:rgba(200,149,42,.15);border:1px solid rgba(200,149,42,.3);
  display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pulso-brand-left .brand-logo img{width:100%;height:100%;object-fit:cover}
.pulso-brand-left .brand-icon{font-size:24px;color:var(--ugel-gold)}
.pulso-brand-left .brand-name{font-family:'Playfair Display',serif;font-size:20px;color:#fff;font-weight:700}
.pulso-brand-left .brand-sub{font-size:11px;color:rgba(200,149,42,.8);letter-spacing:1.2px;text-transform:uppercase;font-weight:500;margin-top:1px}
.pulso-hero{flex:1;display:flex;flex-direction:column;justify-content:center;padding:40px 0}
.pulso-divider-gold{width:48px;height:2px;background:var(--ugel-gold);margin-bottom:24px;border-radius:1px}
.pulso-hero h2{font-family:'Playfair Display',serif;font-size:clamp(28px,3vw,38px);color:#fff;font-weight:700;line-height:1.3;margin-bottom:18px}
.pulso-hero h2 em{font-style:normal;color:var(--ugel-gold-lt)}
.pulso-hero p{font-size:15px;color:rgba(255,255,255,.6);line-height:1.7;max-width:380px;margin-bottom:32px;font-weight:300}

.pulso-rules{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:20px 22px}
.pulso-rules h4{font-size:12px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;font-weight:600}
.pulso-rule{display:flex;align-items:center;gap:10px;margin-bottom:10px;font-size:13px;color:rgba(255,255,255,.65)}
.pulso-rule:last-child{margin-bottom:0}
.pulso-rule i{color:var(--ugel-gold);font-size:14px;flex-shrink:0}

.pulso-left-footer{font-size:11px;color:rgba(255,255,255,.3);letter-spacing:.5px;border-top:1px solid rgba(255,255,255,.07);padding-top:20px}

.pulso-right{width:100%;background:var(--ugel-cream);display:flex;flex-direction:column;
  align-items:center;justify-content:center;padding:40px 24px;min-height:100vh;position:relative}
@media(min-width:1024px){.pulso-right{width:420px;flex-shrink:0}}
.pulso-right::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;
  background:linear-gradient(90deg,var(--ugel-navy),var(--ugel-gold),var(--ugel-navy))}

.pulso-form-box{width:100%;max-width:360px}
.pulso-mobile-brand{display:flex;flex-direction:column;align-items:center;margin-bottom:32px;text-align:center}
@media(min-width:1024px){.pulso-mobile-brand{display:none}}
.pulso-mobile-brand .brand-logo{width:56px;height:56px;border-radius:14px;overflow:hidden;
  background:var(--ugel-navy);display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.pulso-mobile-brand .brand-logo img{width:100%;height:100%;object-fit:cover}
.pulso-mobile-brand .brand-logo i{font-size:24px;color:var(--ugel-gold)}
.pulso-mobile-brand h1{font-family:'Playfair Display',serif;font-size:19px;color:var(--ugel-navy);font-weight:700}

.pulso-icon-box{width:64px;height:64px;border-radius:16px;
  background:linear-gradient(135deg,var(--ugel-navy),var(--ugel-blue));
  display:flex;align-items:center;justify-content:center;
  margin-bottom:20px;box-shadow:0 8px 24px rgba(13,27,62,.2)}
.pulso-icon-box i{font-size:28px;color:var(--ugel-gold-lt)}

.pulso-form-header{margin-bottom:24px}
.pulso-form-header h3{font-family:'Playfair Display',serif;font-size:24px;color:var(--ugel-text);font-weight:700;margin-bottom:6px}
.pulso-form-header p{font-size:13.5px;color:var(--ugel-muted);line-height:1.6}

.pulso-alert-danger{background:#fff1f1;border:1px solid #fcc;border-radius:8px;padding:11px 14px;font-size:13px;color:#c0392b;margin-bottom:18px}
.invalid-feedback{color:#e74c3c;font-size:12px;margin-top:4px;display:block}

.pulso-field{margin-bottom:18px}
.pulso-field label{display:block;font-size:12.5px;font-weight:600;color:var(--ugel-text);margin-bottom:6px;letter-spacing:.2px;text-transform:uppercase}
.pulso-input-group{position:relative}
.pulso-input{width:100%;padding:11px 14px;border:1.5px solid var(--ugel-border);border-radius:8px;
  font-size:14px;font-family:'DM Sans',sans-serif;color:var(--ugel-text);background:#fff;
  transition:border-color .2s,box-shadow .2s;outline:none}
.pulso-input-group .pulso-input{padding-right:42px}
.pulso-input:focus{border-color:var(--ugel-blue);box-shadow:0 0 0 3px rgba(26,58,110,.1)}
.pulso-input.is-invalid{border-color:#e74c3c}
.pulso-input::placeholder{color:#b0baca}
.pulso-eye-btn{position:absolute;right:12px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:var(--ugel-muted);padding:4px;line-height:1}
.pulso-eye-btn:hover{color:var(--ugel-text)}

.pulso-strength{margin-top:8px}
.pulso-strength-bar{display:flex;gap:4px;margin-bottom:4px}
.pulso-strength-bar span{flex:1;height:3px;border-radius:2px;background:var(--ugel-border);transition:background .3s}
.pulso-strength-label{font-size:11px;color:var(--ugel-muted)}

.pulso-btn-primary{width:100%;padding:13px;background:var(--ugel-navy);color:#fff;border:none;
  border-radius:8px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;
  letter-spacing:.3px;transition:background .2s,box-shadow .2s}
.pulso-btn-primary:hover{background:var(--ugel-blue);box-shadow:0 4px 16px rgba(13,27,62,.25)}
.pulso-link-back{display:flex;align-items:center;justify-content:center;gap:6px;
  margin-top:20px;font-size:13px;color:var(--ugel-muted);text-decoration:none;transition:color .2s}
.pulso-link-back:hover{color:var(--ugel-text)}
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
  document.querySelectorAll('.pulso-eye-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const input = this.closest('.pulso-input-group').querySelector('input');
      const icon  = this.querySelector('i');
      input.type = input.type === 'password' ? 'text' : 'password';
      icon.classList.toggle('tabler-eye-off');
      icon.classList.toggle('tabler-eye');
    });
  });

  const pwInput = document.getElementById('password');
  const bars    = document.querySelectorAll('.pulso-strength-bar span');
  const label   = document.querySelector('.pulso-strength-label');
  if (pwInput) {
    pwInput.addEventListener('input', function () {
      const v = this.value;
      let score = 0;
      if (v.length >= 8) score++;
      if (/[A-Z]/.test(v)) score++;
      if (/[0-9]/.test(v)) score++;
      if (/[^A-Za-z0-9]/.test(v)) score++;
      const colors = ['#e74c3c','#e67e22','#f39c12','#27ae60'];
      const labels = ['Muy débil','Débil','Aceptable','Fuerte'];
      bars.forEach((b, i) => b.style.background = i < score ? colors[score-1] : 'var(--ugel-border)');
      if (label) label.textContent = v.length ? labels[score-1] ?? '' : '';
    });
  }
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
        <h2>Establece una<br><em>contraseña</em><br>segura</h2>
        <p>Tu nueva contraseña protege el acceso a la información institucional. Asegúrate de que sea robusta.</p>
        <div class="pulso-rules">
          <h4>Requisitos de contraseña</h4>
          <div class="pulso-rule"><i class="ti tabler-check"></i> Mínimo 8 caracteres</div>
          <div class="pulso-rule"><i class="ti tabler-check"></i> Al menos una letra mayúscula</div>
          <div class="pulso-rule"><i class="ti tabler-check"></i> Al menos una letra minúscula</div>
          <div class="pulso-rule"><i class="ti tabler-check"></i> Al menos un número</div>
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
        <i class="ti tabler-shield-lock"></i>
      </div>

      <div class="pulso-form-header">
        <h3>Nueva Contraseña</h3>
        <p>Tu nueva contraseña debe ser diferente a las contraseñas utilizadas anteriormente.</p>
      </div>

      @if($errors->any())
        <div class="pulso-alert-danger">
          @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
      @endif

      <form action="{{ route('password.update') }}" method="POST" id="formAuthentication">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}" />
        <input type="hidden" name="email" value="{{ $request->email }}" />

        <div class="pulso-field">
          <label for="password">Nueva contraseña</label>
          <div class="pulso-input-group">
            <input type="password" id="password" name="password"
              class="pulso-input @error('password') is-invalid @enderror"
              placeholder="············" autofocus autocomplete="new-password" />
            <button type="button" class="pulso-eye-btn" tabindex="-1">
              <i class="ti tabler-eye-off" style="font-size:16px"></i>
            </button>
          </div>
          @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
          <div class="pulso-strength">
            <div class="pulso-strength-bar">
              <span></span><span></span><span></span><span></span>
            </div>
            <span class="pulso-strength-label"></span>
          </div>
        </div>

        <div class="pulso-field">
          <label for="password_confirmation">Confirmar contraseña</label>
          <div class="pulso-input-group">
            <input type="password" id="password_confirmation" name="password_confirmation"
              class="pulso-input" placeholder="············" autocomplete="new-password" />
            <button type="button" class="pulso-eye-btn" tabindex="-1">
              <i class="ti tabler-eye-off" style="font-size:16px"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="pulso-btn-primary">
          <i class="ti tabler-lock-check me-1" style="font-size:15px;vertical-align:-2px"></i>
          Establecer nueva contraseña
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
