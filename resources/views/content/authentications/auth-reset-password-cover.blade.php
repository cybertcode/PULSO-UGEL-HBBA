@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
$ci = \App\Models\ConfiguracionInstitucional::cached();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Restablecer Contraseña - ' . ($ci?->sigla ?? $ci?->nombre_institucion ?? 'PULSO UGEL'))

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap');
  :root{--gov-navy:#001a4d;--gov-blue:#003087;--gov-mid:#0047b3;--gold:#c9a227;--gold-light:#e8c547;--cream:#fafaf7;--text-dark:#0d1b2a;--text-muted:#5a6a7a;--border:#e2e8f0;--white:#ffffff}
  *{box-sizing:border-box}html,body{height:100%;margin:0;font-family:'DM Sans',sans-serif}
  .pulso-login-wrapper{display:flex;min-height:100vh;width:100%;overflow:hidden;position:relative}
  .pulso-left{flex:0 0 58.333%;max-width:58.333%;position:relative;background:var(--gov-navy);display:flex;flex-direction:column;justify-content:center;align-items:center;overflow:hidden;padding:3rem}
  .pulso-left::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--gov-navy) 0%,var(--gov-blue) 50%,#002470 100%);z-index:0}
  .pulso-left::after{content:'';position:absolute;top:-10%;right:-5%;width:3px;height:130%;background:linear-gradient(180deg,transparent 0%,var(--gold) 30%,var(--gold-light) 60%,transparent 100%);transform:rotate(-12deg);transform-origin:top center;opacity:.6;z-index:1}
  .left-pattern{position:absolute;inset:0;z-index:1;background-image:radial-gradient(circle,rgba(201,162,39,.12) 1px,transparent 1px),radial-gradient(circle,rgba(201,162,39,.06) 1px,transparent 1px);background-size:40px 40px,80px 80px;background-position:0 0,20px 20px}
  .left-geo{position:absolute;inset:0;z-index:1;overflow:hidden}
  .left-geo .geo-line{position:absolute;background:linear-gradient(90deg,transparent,rgba(201,162,39,.15),transparent);height:1px;width:100%;animation:geo-pulse 4s ease-in-out infinite}
  .left-geo .geo-line:nth-child(1){top:20%;animation-delay:0s}.left-geo .geo-line:nth-child(2){top:40%;animation-delay:1s;opacity:.6}.left-geo .geo-line:nth-child(3){top:65%;animation-delay:2s}.left-geo .geo-line:nth-child(4){top:85%;animation-delay:1.5s;opacity:.4}
  .left-circle-top{position:absolute;top:-120px;left:-120px;width:400px;height:400px;border-radius:50%;border:1px solid rgba(201,162,39,.1);z-index:1}
  .left-circle-bottom{position:absolute;bottom:-150px;right:-100px;width:450px;height:450px;border-radius:50%;border:1px solid rgba(255,255,255,.05);z-index:1}
  .left-content{position:relative;z-index:2;text-align:center;color:var(--white);max-width:520px;width:100%;animation:fade-up .7s ease both}
  .left-icon-big{width:100px;height:100px;margin:0 auto 2rem;background:linear-gradient(135deg,rgba(201,162,39,.15),rgba(201,162,39,.03));border:2px solid rgba(201,162,39,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.8rem;box-shadow:0 0 60px rgba(201,162,39,.12);animation:escudo-float 6s ease-in-out infinite}
  .left-pretitle{font-size:.7rem;font-weight:500;letter-spacing:.35em;text-transform:uppercase;color:var(--gold-light);margin-bottom:1rem;opacity:.9}
  .left-title{font-family:'Playfair Display',Georgia,serif;font-size:clamp(1.8rem,3vw,2.5rem);font-weight:900;line-height:1.1;margin-bottom:.75rem;color:var(--white)}
  .left-title span{color:var(--gold-light);display:block}
  .left-subtitle{font-size:.88rem;color:rgba(255,255,255,.5);line-height:1.7;margin-bottom:2rem;font-weight:300;max-width:360px;margin-left:auto;margin-right:auto}
  /* Indicador de fortaleza */
  .strength-guide{background:rgba(255,255,255,.04);border:1px solid rgba(201,162,39,.15);border-radius:12px;padding:1.25rem;text-align:left}
  .strength-guide-title{font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:.85rem;font-weight:600}
  .strength-rule{display:flex;align-items:center;gap:.6rem;font-size:.8rem;color:rgba(255,255,255,.55);margin-bottom:.5rem}
  .strength-rule:last-child{margin-bottom:0}
  .strength-rule i{font-size:.9rem;color:rgba(201,162,39,.6);flex-shrink:0}
  .left-footer{position:absolute;bottom:2rem;left:0;right:0;text-align:center;z-index:2}
  .left-footer-text{font-size:.65rem;color:rgba(255,255,255,.2);letter-spacing:.15em;text-transform:uppercase}
  /* Panel derecho */
  .pulso-right{flex:0 0 41.667%;max-width:41.667%;background:var(--cream);display:flex;align-items:center;justify-content:center;padding:2.5rem;position:relative;overflow:hidden}
  .pulso-right::before{content:'';position:absolute;inset:0;background-image:radial-gradient(circle at 80% 20%,rgba(0,48,135,.04) 0%,transparent 60%),radial-gradient(circle at 20% 80%,rgba(201,162,39,.04) 0%,transparent 60%);pointer-events:none}
  .pulso-right::after{content:'';position:absolute;left:0;top:15%;height:70%;width:2px;background:linear-gradient(180deg,transparent,var(--gold),transparent);opacity:.5}
  .form-container{position:relative;z-index:1;width:100%;max-width:400px;animation:fade-up .6s ease both;animation-delay:.1s;opacity:0}
  .form-header{margin-bottom:2rem;text-align:center}
  .form-logo-wrap{display:flex;align-items:center;justify-content:center;gap:.75rem;margin-bottom:1.5rem;text-decoration:none}
  .form-logo-img{width:44px;height:44px;object-fit:contain;border-radius:10px;box-shadow:0 4px 12px rgba(0,48,135,.15)}
  .form-logo-placeholder{width:44px;height:44px;background:linear-gradient(135deg,var(--gov-blue),var(--gov-mid));border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1.1rem;box-shadow:0 4px 12px rgba(0,48,135,.3);font-family:'Playfair Display',serif}
  .form-brand-name{font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;color:var(--gov-navy)}
  .form-title{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:var(--text-dark);line-height:1.2;margin:0 0 .4rem}
  .form-title-accent{display:inline-block;width:32px;height:2px;background:var(--gold);border-radius:2px;margin-right:6px;vertical-align:middle;position:relative;top:-2px}
  .form-subtitle{font-size:.82rem;color:var(--text-muted);font-weight:400;line-height:1.5}
  .pulso-alert{border-radius:10px;padding:.85rem 1rem;font-size:.82rem;margin-bottom:1.25rem;display:flex;align-items:flex-start;gap:.6rem;border:1px solid}
  .pulso-alert.danger{background:rgba(239,68,68,.06);border-color:rgba(239,68,68,.2);color:#991b1b}
  .pulso-field{margin-bottom:1.1rem}
  .pulso-label{display:block;font-size:.78rem;font-weight:600;color:var(--text-dark);letter-spacing:.04em;text-transform:uppercase;margin-bottom:.5rem}
  .pulso-input-wrap{position:relative}
  .pulso-input-icon{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:1rem;pointer-events:none;z-index:1;transition:color .2s}
  .pulso-input{width:100%;height:48px;padding:0 1rem 0 2.75rem;border:1.5px solid var(--border);border-radius:10px;font-size:.9rem;font-family:'DM Sans',sans-serif;color:var(--text-dark);background:var(--white);transition:all .2s;outline:none;appearance:none}
  .pulso-input::placeholder{color:#b8c3cc}
  .pulso-input:focus{border-color:var(--gov-blue);box-shadow:0 0 0 3px rgba(0,48,135,.1)}
  .pulso-input-wrap:focus-within .pulso-input-icon{color:var(--gov-blue)}
  .pulso-input.is-invalid{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.08)}
  .pulso-toggle-pw{position:absolute;right:.9rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:.25rem;line-height:1;transition:color .2s;z-index:2}
  .pulso-toggle-pw:hover{color:var(--gov-blue)}
  .pulso-invalid{font-size:.75rem;color:#ef4444;margin-top:.35rem;display:flex;align-items:center;gap:.3rem}
  .btn-pulso-primary{width:100%;height:50px;background:linear-gradient(135deg,var(--gov-navy) 0%,var(--gov-blue) 100%);color:white;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;letter-spacing:.04em;cursor:pointer;position:relative;overflow:hidden;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:.6rem;box-shadow:0 4px 20px rgba(0,48,135,.35);margin-bottom:1rem}
  .btn-pulso-primary:hover{transform:translateY(-1px);box-shadow:0 8px 28px rgba(0,48,135,.45)}
  .back-link{display:flex;align-items:center;justify-content:center;gap:.4rem;margin-top:.5rem;color:var(--text-muted);text-decoration:none;font-size:.84rem;font-weight:500;transition:color .2s}
  .back-link:hover{color:var(--gov-blue)}
  .back-link i{font-size:.9rem;transition:transform .2s}
  .back-link:hover i{transform:translateX(-3px)}
  @keyframes geo-pulse{0%,100%{opacity:.4}50%{opacity:1}}
  @keyframes escudo-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
  @keyframes fade-up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  @media(max-width:1200px){.pulso-left{flex:0 0 50%;max-width:50%}.pulso-right{flex:0 0 50%;max-width:50%}}
  @media(max-width:900px){.pulso-left{display:none}.pulso-right{flex:0 0 100%;max-width:100%;padding:2rem 1.5rem}.pulso-right::after{display:none}}
  .input-group.has-validation::before { pointer-events: none !important; }
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
<div class="pulso-login-wrapper">

  {{-- PANEL IZQUIERDO --}}
  <div class="pulso-left">
    <div class="left-pattern"></div>
    <div class="left-geo">
      <div class="geo-line"></div><div class="geo-line"></div>
      <div class="geo-line"></div><div class="geo-line"></div>
    </div>
    <div class="left-circle-top"></div>
    <div class="left-circle-bottom"></div>

    <div class="left-content">
      <div class="left-icon-big">🔑</div>

      <p class="left-pretitle">Nueva Contraseña &bull; {{ $ci?->sigla ?? 'PULSO UGEL' }}</p>
      <h1 class="left-title">Restablecer<br><span>Contraseña</span></h1>
      <p class="left-subtitle">
        Tu nueva contraseña debe cumplir con los requisitos de seguridad institucional.
      </p>

      <div class="strength-guide">
        <p class="strength-guide-title">Requisitos de seguridad</p>
        <div class="strength-rule">
          <i class="ti tabler-check"></i>
          Mínimo 8 caracteres
        </div>
        <div class="strength-rule">
          <i class="ti tabler-check"></i>
          Al menos una letra mayúscula
        </div>
        <div class="strength-rule">
          <i class="ti tabler-check"></i>
          Al menos un número
        </div>
        <div class="strength-rule">
          <i class="ti tabler-check"></i>
          Diferente a contraseñas anteriores
        </div>
        <div class="strength-rule">
          <i class="ti tabler-check"></i>
          Confirmación debe coincidir exactamente
        </div>
      </div>
    </div>

    <div class="left-footer">
      <p class="left-footer-text">
        @if($ci?->correo_institucional){{ $ci->correo_institucional }} &bull; @endif
        @if($ci?->ugel_codigo)Código {{ $ci->ugel_codigo }} &bull; @endif
        {{ date('Y') }}
      </p>
    </div>
  </div>

  {{-- PANEL DERECHO --}}
  <div class="pulso-right">
    <div class="form-container">

      <div class="form-header">
        <a href="{{ url('/') }}" class="form-logo-wrap">
          @if(!empty($ci?->logo_ruta))
            <img src="{{ Storage::url($ci->logo_ruta) }}" alt="logo" class="form-logo-img">
          @else
            <div class="form-logo-placeholder">
              {{ strtoupper(substr($ci?->sigla ?? $ci?->nombre_institucion ?? 'P', 0, 2)) }}
            </div>
          @endif
          <span class="form-brand-name">{{ $ci?->sigla ?? 'PULSO UGEL' }}</span>
        </a>
        <h2 class="form-title">
          <span class="form-title-accent"></span>Nueva contraseña
        </h2>
        <p class="form-subtitle">Debe ser diferente a tus contraseñas anteriores</p>
      </div>

      @if ($errors->any())
        <div class="pulso-alert danger">
          <i class="ti tabler-alert-circle" style="font-size:1rem;flex-shrink:0;margin-top:1px;"></i>
          <div>@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        </div>
      @endif

      <form id="formAuthentication" action="{{ route('password.update') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}" />
        <input type="hidden" name="email" value="{{ $request->email }}" />

        <div class="pulso-field form-password-toggle form-control-validation">
          <label class="pulso-label" for="password">Nueva contraseña</label>
          <div class="input-group input-group-merge">
            <input type="password" id="password" name="password"
              placeholder="············" autofocus
              class="pulso-input @error('password') is-invalid @enderror"
              style="border-radius:10px 0 0 10px;border-left-width:1.5px;border-right:none;padding-left:1rem;" />
            <span class="input-group-text cursor-pointer" style="background:var(--white);border:1.5px solid var(--border);border-left:none;border-radius:0 10px 10px 0;padding:0 0.85rem;">
              <i class="icon-base ti tabler-eye-off"></i>
            </span>
          </div>
        </div>

        <div class="pulso-field form-password-toggle">
          <label class="pulso-label" for="confirm-password">Confirmar contraseña</label>
          <div class="input-group input-group-merge">
            <input type="password" id="confirm-password"
              name="password_confirmation" placeholder="············"
              class="pulso-input"
              style="border-radius:10px 0 0 10px;border-left-width:1.5px;border-right:none;padding-left:1rem;" />
            <span class="input-group-text cursor-pointer" style="background:var(--white);border:1.5px solid var(--border);border-left:none;border-radius:0 10px 10px 0;padding:0 0.85rem;">
              <i class="icon-base ti tabler-eye-off"></i>
            </span>
          </div>
        </div>

        <button type="submit" class="btn-pulso-primary" style="margin-top:.5rem;">
          <i class="ti tabler-shield-check" style="font-size:1rem;"></i>
          Establecer nueva contraseña
        </button>

        <a href="{{ route('login') }}" class="back-link">
          <i class="ti tabler-arrow-left"></i>
          Volver al inicio de sesión
        </a>
      </form>

    </div>
  </div>

</div>

@endsection
