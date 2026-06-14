@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
$ci = \App\Models\ConfiguracionInstitucional::cached();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Verificar Correo - ' . ($ci?->sigla ?? $ci?->nombre_institucion ?? 'PULSO UGEL'))

@section('page-style')
@vite('resources/assets/vendor/scss/pages/page-auth.scss')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap');
  :root{--gov-navy:#001a4d;--gov-blue:#003087;--gov-mid:#0047b3;--gold:#c9a227;--gold-light:#e8c547;--cream:#fafaf7;--text-dark:#0d1b2a;--text-muted:#5a6a7a;--border:#e2e8f0;--white:#ffffff}
  *{box-sizing:border-box}html,body{height:100%;margin:0;font-family:'DM Sans',sans-serif}
  .pulso-login-wrapper{display:flex;min-height:100vh;width:100%;overflow:hidden}
  .pulso-left{flex:0 0 58.333%;max-width:58.333%;position:relative;background:var(--gov-navy);display:flex;flex-direction:column;justify-content:center;align-items:center;overflow:hidden;padding:3rem}
  .pulso-left::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--gov-navy) 0%,var(--gov-blue) 50%,#002470 100%);z-index:0}
  .pulso-left::after{content:'';position:absolute;top:-10%;right:-5%;width:3px;height:130%;background:linear-gradient(180deg,transparent 0%,var(--gold) 30%,var(--gold-light) 60%,transparent 100%);transform:rotate(-12deg);transform-origin:top center;opacity:.6;z-index:1}
  .left-pattern{position:absolute;inset:0;z-index:1;background-image:radial-gradient(circle,rgba(201,162,39,.12) 1px,transparent 1px),radial-gradient(circle,rgba(201,162,39,.06) 1px,transparent 1px);background-size:40px 40px,80px 80px;background-position:0 0,20px 20px}
  .left-geo{position:absolute;inset:0;z-index:1;overflow:hidden}
  .left-geo .geo-line{position:absolute;background:linear-gradient(90deg,transparent,rgba(201,162,39,.15),transparent);height:1px;width:100%;animation:geo-pulse 4s ease-in-out infinite}
  .left-geo .geo-line:nth-child(1){top:20%;animation-delay:0s}.left-geo .geo-line:nth-child(2){top:40%;animation-delay:1s;opacity:.6}.left-geo .geo-line:nth-child(3){top:65%;animation-delay:2s}.left-geo .geo-line:nth-child(4){top:85%;animation-delay:1.5s;opacity:.4}
  .left-circle-top{position:absolute;top:-120px;left:-120px;width:400px;height:400px;border-radius:50%;border:1px solid rgba(201,162,39,.1);z-index:1}
  .left-circle-bottom{position:absolute;bottom:-150px;right:-100px;width:450px;height:450px;border-radius:50%;border:1px solid rgba(255,255,255,.05);z-index:1}
  .left-content{position:relative;z-index:2;text-align:center;color:var(--white);max-width:480px;width:100%;animation:fade-up .7s ease both}
  /* Envelope animado */
  .envelope-wrap{width:110px;height:110px;margin:0 auto 2rem;position:relative}
  .envelope-icon{width:110px;height:110px;background:linear-gradient(135deg,rgba(201,162,39,.15),rgba(201,162,39,.03));border:2px solid rgba(201,162,39,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:3rem;animation:escudo-float 5s ease-in-out infinite;box-shadow:0 0 60px rgba(201,162,39,.12)}
  .envelope-dot{position:absolute;top:8px;right:8px;width:18px;height:18px;background:#22c55e;border-radius:50%;border:3px solid var(--gov-navy);animation:pulse-dot 2s ease-in-out infinite}
  .left-pretitle{font-size:.7rem;font-weight:500;letter-spacing:.35em;text-transform:uppercase;color:var(--gold-light);margin-bottom:1rem;opacity:.9}
  .left-title{font-family:'Playfair Display',Georgia,serif;font-size:clamp(1.8rem,3vw,2.4rem);font-weight:900;line-height:1.1;margin-bottom:.75rem;color:var(--white)}
  .left-title span{color:var(--gold-light);display:block}
  .left-subtitle{font-size:.88rem;color:rgba(255,255,255,.5);line-height:1.7;margin-bottom:2rem;font-weight:300;max-width:340px;margin-left:auto;margin-right:auto}
  /* Info card izquierdo */
  .info-card{background:rgba(255,255,255,.04);border:1px solid rgba(201,162,39,.15);border-radius:14px;padding:1.25rem 1.5rem;text-align:left}
  .info-card-row{display:flex;align-items:flex-start;gap:.75rem;margin-bottom:.85rem}
  .info-card-row:last-child{margin-bottom:0}
  .info-card-icon{width:32px;height:32px;background:rgba(201,162,39,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;color:var(--gold-light)}
  .info-card-text strong{display:block;font-size:.8rem;font-weight:600;color:rgba(255,255,255,.8);margin-bottom:.1rem}
  .info-card-text span{font-size:.74rem;color:rgba(255,255,255,.4)}
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
  .form-subtitle{font-size:.82rem;color:var(--text-muted);line-height:1.5;margin-bottom:0}
  /* Email display */
  .email-display{background:linear-gradient(135deg,rgba(0,48,135,.05),rgba(201,162,39,.05));border:1px solid rgba(0,48,135,.12);border-radius:12px;padding:1rem 1.25rem;margin:1.5rem 0;display:flex;align-items:center;gap:.75rem}
  .email-display-icon{width:36px;height:36px;background:linear-gradient(135deg,var(--gov-navy),var(--gov-blue));border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;flex-shrink:0}
  .email-display-addr{font-size:.85rem;font-weight:600;color:var(--text-dark);word-break:break-all}
  .email-display-sub{font-size:.72rem;color:var(--text-muted);margin-top:.15rem}
  .pulso-alert{border-radius:10px;padding:.85rem 1rem;font-size:.82rem;margin-bottom:1.25rem;display:flex;align-items:flex-start;gap:.6rem;border:1px solid}
  .pulso-alert.success{background:rgba(34,197,94,.07);border-color:rgba(34,197,94,.25);color:#166534}
  .btn-pulso-primary{width:100%;height:50px;background:linear-gradient(135deg,var(--gov-navy) 0%,var(--gov-blue) 100%);color:white;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;letter-spacing:.04em;cursor:pointer;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:.6rem;box-shadow:0 4px 20px rgba(0,48,135,.35)}
  .btn-pulso-primary:hover{transform:translateY(-1px);box-shadow:0 8px 28px rgba(0,48,135,.45)}
  .btn-pulso-secondary{width:100%;height:46px;background:white;color:var(--text-dark);border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.875rem;font-weight:500;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.5rem;margin-top:.75rem}
  .btn-pulso-secondary:hover{border-color:var(--gov-blue);color:var(--gov-blue)}
  .form-inst-block{background:linear-gradient(135deg,rgba(0,48,135,.04),rgba(201,162,39,.04));border:1px solid rgba(0,48,135,.1);border-radius:12px;padding:.9rem 1.1rem;display:flex;align-items:center;gap:.65rem;margin-top:1.5rem}
  .form-inst-icon{width:32px;height:32px;background:linear-gradient(135deg,var(--gov-navy),var(--gov-blue));border-radius:7px;display:flex;align-items:center;justify-content:center;color:white;font-size:.85rem;flex-shrink:0}
  .form-inst-text{font-size:.74rem;color:var(--text-muted);line-height:1.4}
  .form-inst-text strong{display:block;color:var(--text-dark);font-size:.78rem;margin-bottom:.1rem}
  @keyframes geo-pulse{0%,100%{opacity:.4}50%{opacity:1}}
  @keyframes escudo-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
  @keyframes pulse-dot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.6;transform:scale(.8)}}
  @keyframes fade-up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  @media(max-width:1200px){.pulso-left{flex:0 0 50%;max-width:50%}.pulso-right{flex:0 0 50%;max-width:50%}}
  @media(max-width:900px){.pulso-left{display:none}.pulso-right{flex:0 0 100%;max-width:100%;padding:2rem 1.5rem}.pulso-right::after{display:none}}
</style>
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
      <div class="envelope-wrap">
        <div class="envelope-icon">✉️</div>
        <div class="envelope-dot"></div>
      </div>

      <p class="left-pretitle">Verificación de Cuenta &bull; {{ $ci?->sigla ?? 'PULSO UGEL' }}</p>
      <h1 class="left-title">Confirma<br><span>tu correo</span></h1>
      <p class="left-subtitle">
        Para garantizar la seguridad del sistema institucional, necesitamos verificar tu identidad.
      </p>

      <div class="info-card">
        <div class="info-card-row">
          <div class="info-card-icon">
            <i class="ti tabler-clock" style="font-size:.9rem;"></i>
          </div>
          <div class="info-card-text">
            <strong>El enlace expira en 60 minutos</strong>
            <span>Por seguridad, el enlace tiene vigencia limitada</span>
          </div>
        </div>
        <div class="info-card-row">
          <div class="info-card-icon">
            <i class="ti tabler-inbox" style="font-size:.9rem;"></i>
          </div>
          <div class="info-card-text">
            <strong>Revisa tu bandeja de entrada</strong>
            <span>También verifica la carpeta de spam o correo no deseado</span>
          </div>
        </div>
        <div class="info-card-row">
          <div class="info-card-icon">
            <i class="ti tabler-refresh" style="font-size:.9rem;"></i>
          </div>
          <div class="info-card-text">
            <strong>¿No llegó el correo?</strong>
            <span>Puedes solicitar el reenvío con el botón correspondiente</span>
          </div>
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
          <span class="form-title-accent"></span>Verifica tu correo
        </h2>
        <p class="form-subtitle">Hemos enviado un enlace de activación a tu cuenta</p>
      </div>

      <div class="email-display">
        <div class="email-display-icon">
          <i class="ti tabler-mail" style="font-size:.95rem;"></i>
        </div>
        <div>
          <div class="email-display-addr">{{ auth()->user()->email ?? 'tu.correo@ugel.gob.pe' }}</div>
          <div class="email-display-sub">Sigue el enlace del mensaje para activar tu cuenta</div>
        </div>
      </div>

      @if (session('status') == 'verification-link-sent')
        <div class="pulso-alert success">
          <i class="ti tabler-circle-check" style="font-size:1rem;flex-shrink:0;margin-top:1px;"></i>
          <span>Se envió un nuevo enlace de verificación a tu correo electrónico.</span>
        </div>
      @endif

      @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()))
        <form method="POST" action="{{ route('verification.send') }}">
          @csrf
          <button type="submit" class="btn-pulso-primary">
            <i class="ti tabler-send" style="font-size:1rem;"></i>
            Reenviar correo de verificación
          </button>
        </form>
      @endif

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-pulso-secondary">
          <i class="ti tabler-logout" style="font-size:.95rem;"></i>
          Cerrar sesión
        </button>
      </form>

      <div class="form-inst-block">
        <div class="form-inst-icon">
          <i class="ti tabler-shield-check"></i>
        </div>
        <div class="form-inst-text">
          <strong>{{ $ci?->nombre_institucion ?? 'PULSO UGEL' }}</strong>
          Sistema de Control Interno e Integridad Institucional
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
