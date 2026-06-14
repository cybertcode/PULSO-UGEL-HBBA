@php
$customizerHidden = 'customizer-hide';
$ci = \App\Models\ConfiguracionInstitucional::cached();
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/blankLayout')

@section('title', 'En Mantenimiento — ' . ($ci?->sigla ?? 'PULSO UGEL'))

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap');
  :root{--gov-navy:#001a4d;--gov-blue:#003087;--gold:#c9a227;--gold-light:#e8c547;--cream:#fafaf7;--text-dark:#0d1b2a}
  *{box-sizing:border-box}html,body{height:100%;margin:0;font-family:'DM Sans',sans-serif;background:var(--gov-navy)}
  .error-page{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;position:relative;overflow:hidden;text-align:center}
  .error-page::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,#1a1000 0%,#2d1a00 40%,var(--gov-navy) 100%);z-index:0}
  .error-bg-pattern{position:absolute;inset:0;z-index:1;background-image:radial-gradient(circle,rgba(201,162,39,.1) 1px,transparent 1px);background-size:40px 40px;pointer-events:none}
  .error-lines{position:absolute;inset:0;z-index:1;pointer-events:none;overflow:hidden}
  .error-line{position:absolute;height:1px;width:100%;background:linear-gradient(90deg,transparent,rgba(201,162,39,.15),transparent);animation:line-pulse 4s ease-in-out infinite}
  .error-line:nth-child(1){top:15%;animation-delay:0s}.error-line:nth-child(2){top:40%;animation-delay:1.2s;opacity:.6}.error-line:nth-child(3){top:70%;animation-delay:2.4s}.error-line:nth-child(4){top:88%;animation-delay:.8s;opacity:.5}
  .circle-tl{position:absolute;top:-150px;left:-150px;width:500px;height:500px;border-radius:50%;border:1px solid rgba(201,162,39,.08);z-index:1}
  .circle-br{position:absolute;bottom:-200px;right:-150px;width:600px;height:600px;border-radius:50%;border:1px solid rgba(255,255,255,.03);z-index:1}
  .error-content{position:relative;z-index:2;max-width:600px;animation:fade-up .7s ease both}
  .error-logo{display:flex;align-items:center;justify-content:center;gap:.75rem;margin-bottom:2.5rem;text-decoration:none}
  .error-logo-img{width:48px;height:48px;object-fit:contain;border-radius:10px;border:2px solid rgba(201,162,39,.3);box-shadow:0 4px 20px rgba(0,0,0,.3)}
  .error-logo-placeholder{width:48px;height:48px;background:linear-gradient(135deg,rgba(201,162,39,.2),rgba(201,162,39,.05));border:2px solid rgba(201,162,39,.4);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--gold-light);font-family:'Playfair Display',serif;font-weight:900;font-size:1.1rem}
  .error-logo-name{font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;color:rgba(255,255,255,.9)}
  /* Engranaje animado */
  .maint-icon{width:100px;height:100px;margin:0 auto 1.75rem;background:linear-gradient(135deg,rgba(201,162,39,.15),rgba(201,162,39,.03));border:2px solid rgba(201,162,39,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:3rem;box-shadow:0 0 50px rgba(201,162,39,.12)}
  .maint-icon i{animation:gear-spin 8s linear infinite;display:block;font-size:2.8rem;color:var(--gold-light)}
  /* Barra de progreso animada */
  .maint-progress{background:rgba(255,255,255,.08);border:1px solid rgba(201,162,39,.2);border-radius:50px;height:6px;margin:1.5rem auto;max-width:280px;overflow:hidden}
  .maint-progress-bar{height:100%;background:linear-gradient(90deg,var(--gold),var(--gold-light));border-radius:50px;animation:progress-wave 2.5s ease-in-out infinite}
  .error-divider{display:flex;align-items:center;gap:1rem;margin:1.25rem auto;max-width:200px}
  .error-divider-line{flex:1;height:1px;background:linear-gradient(90deg,transparent,var(--gold),transparent)}
  .error-divider-dot{width:5px;height:5px;background:var(--gold);border-radius:50%;flex-shrink:0}
  .error-title{font-family:'Playfair Display',serif;font-size:clamp(1.5rem,4vw,2.2rem);font-weight:700;color:#fff;margin:.75rem 0 .5rem;line-height:1.2}
  .error-subtitle{font-size:.95rem;color:rgba(255,255,255,.5);line-height:1.7;font-weight:300;margin-bottom:1.5rem;max-width:440px;margin-left:auto;margin-right:auto}
  /* Info contacto */
  .maint-contact{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1px;background:rgba(201,162,39,.12);border:1px solid rgba(201,162,39,.15);border-radius:14px;overflow:hidden;margin-bottom:2rem;text-align:left}
  .maint-contact-item{background:rgba(255,255,255,.03);padding:1rem 1.25rem;display:flex;align-items:flex-start;gap:.7rem}
  .maint-contact-icon{width:30px;height:30px;background:rgba(201,162,39,.1);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--gold-light);font-size:.85rem}
  .maint-contact-label{font-size:.68rem;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.3);margin-bottom:.15rem;font-weight:500}
  .maint-contact-value{font-size:.8rem;color:rgba(255,255,255,.7);font-weight:500;word-break:break-all}
  .error-actions{display:flex;gap:.85rem;justify-content:center;flex-wrap:wrap;margin-bottom:2rem}
  .btn-error-primary{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.75rem;background:linear-gradient(135deg,var(--gold),var(--gold-light));color:var(--gov-navy);border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:700;cursor:pointer;text-decoration:none;transition:all .3s;box-shadow:0 4px 20px rgba(201,162,39,.35)}
  .btn-error-primary:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(201,162,39,.5);color:var(--gov-navy)}
  .error-inst{display:inline-flex;align-items:center;gap:.6rem;background:rgba(255,255,255,.04);border:1px solid rgba(201,162,39,.15);border-radius:50px;padding:.5rem 1.25rem;font-size:.75rem;color:rgba(255,255,255,.4)}
  .error-inst i{font-size:.85rem;color:rgba(201,162,39,.6)}
  @keyframes line-pulse{0%,100%{opacity:.3}50%{opacity:1}}
  @keyframes fade-up{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
  @keyframes gear-spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
  @keyframes progress-wave{0%{width:0%;margin-left:0}50%{width:70%;margin-left:15%}100%{width:0%;margin-left:100%}}
</style>

<div class="error-page">
  <div class="error-bg-pattern"></div>
  <div class="error-lines">
    <div class="error-line"></div><div class="error-line"></div>
    <div class="error-line"></div><div class="error-line"></div>
  </div>
  <div class="circle-tl"></div>
  <div class="circle-br"></div>

  <div class="error-content">

    <a href="{{ url('/') }}" class="error-logo">
      @if(!empty($ci?->logo_ruta))
        <img src="{{ Storage::url($ci->logo_ruta) }}" alt="logo" class="error-logo-img">
      @else
        <div class="error-logo-placeholder">
          {{ strtoupper(substr($ci?->sigla ?? 'PU', 0, 2)) }}
        </div>
      @endif
      <span class="error-logo-name">{{ $ci?->sigla ?? 'PULSO UGEL' }}</span>
    </a>

    <div class="maint-icon">
      <i class="ti tabler-settings"></i>
    </div>

    <div class="maint-progress">
      <div class="maint-progress-bar"></div>
    </div>

    <div class="error-divider">
      <div class="error-divider-line"></div>
      <div class="error-divider-dot"></div>
      <div class="error-divider-line"></div>
    </div>

    <h1 class="error-title">Sistema en Mantenimiento</h1>
    <p class="error-subtitle">
      Estamos realizando mejoras para ofrecerte un mejor servicio.
      @if($ci?->nombre_institucion)
        El sistema de {{ $ci->nombre_institucion }} estará disponible en breve.
      @else
        El sistema estará disponible nuevamente en breve.
      @endif
    </p>

    {{-- Datos de contacto de BD --}}
    @if($ci?->correo_institucional || $ci?->telefono || $ci?->director || $ci?->coordinador_sci)
    <div class="maint-contact">
      @if($ci?->correo_institucional)
      <div class="maint-contact-item">
        <div class="maint-contact-icon"><i class="ti tabler-mail"></i></div>
        <div>
          <div class="maint-contact-label">Correo</div>
          <div class="maint-contact-value">
            <a href="mailto:{{ $ci->correo_institucional }}" style="color:var(--gold-light);text-decoration:none">{{ $ci->correo_institucional }}</a>
          </div>
        </div>
      </div>
      @endif
      @if($ci?->telefono)
      <div class="maint-contact-item">
        <div class="maint-contact-icon"><i class="ti tabler-phone"></i></div>
        <div>
          <div class="maint-contact-label">Teléfono</div>
          <div class="maint-contact-value">{{ $ci->telefono }}</div>
        </div>
      </div>
      @endif
      @if($ci?->director)
      <div class="maint-contact-item">
        <div class="maint-contact-icon"><i class="ti tabler-user-check"></i></div>
        <div>
          <div class="maint-contact-label">Director</div>
          <div class="maint-contact-value">{{ $ci->director }}</div>
        </div>
      </div>
      @endif
      @if($ci?->coordinador_sci)
      <div class="maint-contact-item">
        <div class="maint-contact-icon"><i class="ti tabler-shield"></i></div>
        <div>
          <div class="maint-contact-label">Coord. SCI</div>
          <div class="maint-contact-value">{{ $ci->coordinador_sci }}</div>
        </div>
      </div>
      @endif
    </div>
    @endif

    <div class="error-actions">
      <a href="{{ url('/') }}" class="btn-error-primary">
        <i class="ti tabler-refresh" style="font-size:1rem;"></i>
        Reintentar
      </a>
    </div>

    <div class="error-inst">
      <i class="ti tabler-shield-check"></i>
      {{ $ci?->nombre_institucion ?? 'Sistema de Control Interno' }}
      @if($ci?->ugel_codigo) &bull; Código {{ $ci->ugel_codigo }}@endif
    </div>

  </div>
</div>
@endsection
