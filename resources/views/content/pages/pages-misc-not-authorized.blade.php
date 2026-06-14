@php
$customizerHidden = 'customizer-hide';
$ci = \App\Models\ConfiguracionInstitucional::cached();
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/blankLayout')

@section('title', 'Sin autorización — ' . ($ci?->sigla ?? 'PULSO UGEL'))

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap');
  :root{--gov-navy:#001a4d;--gov-blue:#003087;--gold:#c9a227;--gold-light:#e8c547;--cream:#fafaf7;--text-dark:#0d1b2a;--text-muted:#5a6a7a}
  *{box-sizing:border-box}html,body{height:100%;margin:0;font-family:'DM Sans',sans-serif;background:var(--gov-navy)}
  .error-page{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;position:relative;overflow:hidden;text-align:center}
  .error-page::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--gov-navy) 0%,#1a0030 60%,#0a001a 100%);z-index:0}
  .error-bg-pattern{position:absolute;inset:0;z-index:1;background-image:radial-gradient(circle,rgba(201,162,39,.1) 1px,transparent 1px);background-size:40px 40px;pointer-events:none}
  .error-lines{position:absolute;inset:0;z-index:1;pointer-events:none;overflow:hidden}
  .error-line{position:absolute;height:1px;width:100%;background:linear-gradient(90deg,transparent,rgba(201,162,39,.12),transparent);animation:line-pulse 4s ease-in-out infinite}
  .error-line:nth-child(1){top:15%;animation-delay:0s}.error-line:nth-child(2){top:40%;animation-delay:1.2s;opacity:.6}.error-line:nth-child(3){top:70%;animation-delay:2.4s}.error-line:nth-child(4){top:88%;animation-delay:.8s;opacity:.5}
  .circle-tl{position:absolute;top:-150px;left:-150px;width:500px;height:500px;border-radius:50%;border:1px solid rgba(201,162,39,.07);z-index:1}
  .circle-br{position:absolute;bottom:-200px;right:-150px;width:600px;height:600px;border-radius:50%;border:1px solid rgba(255,255,255,.03);z-index:1}
  .error-content{position:relative;z-index:2;max-width:560px;animation:fade-up .7s ease both}
  .error-logo{display:flex;align-items:center;justify-content:center;gap:.75rem;margin-bottom:2.5rem;text-decoration:none}
  .error-logo-img{width:48px;height:48px;object-fit:contain;border-radius:10px;border:2px solid rgba(201,162,39,.3);box-shadow:0 4px 20px rgba(0,0,0,.3)}
  .error-logo-placeholder{width:48px;height:48px;background:linear-gradient(135deg,rgba(201,162,39,.2),rgba(201,162,39,.05));border:2px solid rgba(201,162,39,.4);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--gold-light);font-family:'Playfair Display',serif;font-weight:900;font-size:1.1rem}
  .error-logo-name{font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;color:rgba(255,255,255,.9)}
  /* Ícono de escudo animado */
  .error-shield{width:100px;height:100px;margin:0 auto 1.5rem;background:linear-gradient(135deg,rgba(239,68,68,.15),rgba(239,68,68,.05));border:2px solid rgba(239,68,68,.3);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:3rem;animation:shield-pulse 3s ease-in-out infinite;box-shadow:0 0 40px rgba(239,68,68,.1)}
  /* Número error */
  .error-number{font-family:'Playfair Display',serif;font-size:clamp(6rem,15vw,10rem);font-weight:900;line-height:.9;color:transparent;background:linear-gradient(135deg,#f87171 0%,#fca5a5 50%,rgba(239,68,68,.4) 100%);-webkit-background-clip:text;background-clip:text;margin-bottom:.5rem;letter-spacing:-.04em}
  .error-divider{display:flex;align-items:center;gap:1rem;margin:1.25rem auto;max-width:200px}
  .error-divider-line{flex:1;height:1px;background:linear-gradient(90deg,transparent,rgba(239,68,68,.5),transparent)}
  .error-divider-dot{width:5px;height:5px;background:#f87171;border-radius:50%;flex-shrink:0}
  .error-title{font-family:'Playfair Display',serif;font-size:clamp(1.5rem,4vw,2.2rem);font-weight:700;color:#fff;margin:.75rem 0 .5rem;line-height:1.2}
  .error-subtitle{font-size:.95rem;color:rgba(255,255,255,.5);line-height:1.7;font-weight:300;margin-bottom:2rem;max-width:400px;margin-left:auto;margin-right:auto}
  /* Badge de acceso */
  .access-badge{display:inline-flex;align-items:center;gap:.5rem;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);border-radius:50px;padding:.4rem 1rem;font-size:.78rem;color:#fca5a5;font-weight:500;margin-bottom:1.5rem}
  .access-badge-dot{width:6px;height:6px;background:#f87171;border-radius:50%;animation:pulse-dot 1.5s ease-in-out infinite}
  .error-actions{display:flex;gap:.85rem;justify-content:center;flex-wrap:wrap;margin-bottom:2.5rem}
  .btn-error-primary{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.75rem;background:linear-gradient(135deg,var(--gold),var(--gold-light));color:var(--gov-navy);border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:700;cursor:pointer;text-decoration:none;transition:all .3s;box-shadow:0 4px 20px rgba(201,162,39,.35)}
  .btn-error-primary:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(201,162,39,.5);color:var(--gov-navy)}
  .btn-error-secondary{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;background:rgba(255,255,255,.06);color:rgba(255,255,255,.8);border:1.5px solid rgba(255,255,255,.15);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:500;cursor:pointer;text-decoration:none;transition:all .3s}
  .btn-error-secondary:hover{background:rgba(255,255,255,.1);border-color:rgba(201,162,39,.4);color:#fff}
  .error-inst{display:inline-flex;align-items:center;gap:.6rem;background:rgba(255,255,255,.04);border:1px solid rgba(201,162,39,.15);border-radius:50px;padding:.5rem 1.25rem;font-size:.75rem;color:rgba(255,255,255,.4)}
  .error-inst i{font-size:.85rem;color:rgba(201,162,39,.6)}
  @keyframes line-pulse{0%,100%{opacity:.3}50%{opacity:1}}
  @keyframes fade-up{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
  @keyframes shield-pulse{0%,100%{box-shadow:0 0 40px rgba(239,68,68,.1)}50%{box-shadow:0 0 60px rgba(239,68,68,.25)}}
  @keyframes pulse-dot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.7)}}
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

    <div class="access-badge">
      <span class="access-badge-dot"></span>
      Acceso restringido
    </div>

    <div class="error-number">401</div>

    <div class="error-divider">
      <div class="error-divider-line"></div>
      <div class="error-divider-dot"></div>
      <div class="error-divider-line"></div>
    </div>

    <h1 class="error-title">Sin autorización</h1>
    <p class="error-subtitle">
      No tienes permiso para acceder a este recurso.
      @if($ci?->coordinador_sci)
        Contacta al coordinador SCI: <strong style="color:rgba(255,255,255,.7)">{{ $ci->coordinador_sci }}</strong>
        @if($ci?->correo_sci) (<a href="mailto:{{ $ci->correo_sci }}" style="color:var(--gold-light);text-decoration:none">{{ $ci->correo_sci }}</a>)@endif
        para solicitar acceso.
      @elseif($ci?->director)
        Comunícate con la dirección: <strong style="color:rgba(255,255,255,.7)">{{ $ci->director }}</strong>.
      @else
        Regresa al inicio o solicita los permisos correspondientes.
      @endif
    </p>

    <div class="error-actions">
      <a href="{{ url('/') }}" class="btn-error-primary">
        <i class="ti tabler-home" style="font-size:1rem;"></i>
        Ir al inicio
      </a>
      <a href="javascript:history.back()" class="btn-error-secondary">
        <i class="ti tabler-arrow-left" style="font-size:1rem;"></i>
        Volver atrás
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
