@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Acceso Restringido — PULSO UGEL')

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
.pulso-left-footer{font-size:11px;color:rgba(255,255,255,.3);letter-spacing:.5px;border-top:1px solid rgba(255,255,255,.07);padding-top:20px}
.pulso-right{width:100%;background:var(--ugel-cream);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 24px;min-height:100vh;position:relative}
@media(min-width:1024px){.pulso-right{width:420px;flex-shrink:0}}
.pulso-right::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--ugel-navy),var(--ugel-gold),var(--ugel-navy))}
.pulso-form-box{width:100%;max-width:360px;text-align:center}
.pulso-lock-icon{width:80px;height:80px;border-radius:20px;
  background:linear-gradient(135deg,var(--ugel-navy),var(--ugel-blue));
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 24px;box-shadow:0 8px 32px rgba(13,27,62,.2)}
.pulso-lock-icon i{font-size:36px;color:var(--ugel-gold-lt)}
.pulso-form-header h3{font-family:'Playfair Display',serif;font-size:24px;color:var(--ugel-text);font-weight:700;margin-bottom:10px}
.pulso-form-header p{font-size:13.5px;color:var(--ugel-muted);line-height:1.6;margin-bottom:24px}
.pulso-info-box{background:rgba(13,27,62,.05);border:1px solid var(--ugel-border);border-radius:10px;padding:16px 18px;margin-bottom:24px;text-align:left}
.pulso-info-box p{font-size:13px;color:var(--ugel-muted);line-height:1.6;margin:0}
.pulso-info-box strong{color:var(--ugel-text)}
.pulso-btn-primary{width:100%;padding:13px;background:var(--ugel-navy);color:#fff;border:none;
  border-radius:8px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;
  letter-spacing:.3px;transition:background .2s,box-shadow .2s;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px}
.pulso-btn-primary:hover{background:var(--ugel-blue);box-shadow:0 4px 16px rgba(13,27,62,.25);color:#fff}
.pulso-footer-note{margin-top:24px;padding-top:20px;border-top:1px solid var(--ugel-border)}
.pulso-footer-note p{font-size:12px;color:var(--ugel-muted);line-height:1.6}
</style>
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
        <h2>Sistema de<br><em>acceso</em><br>controlado</h2>
        <p>El acceso al sistema es exclusivo para el personal autorizado de la {{ $ci?->nombre_institucion ?? 'UGEL Huacaybamba' }}. Los usuarios son creados por el administrador.</p>
      </div>
      <div class="pulso-left-footer">
        {{ $ci?->nombre_institucion ?? 'UGEL Huacaybamba' }} &bull; Perú
      </div>
    </div>
  </div>

  <div class="pulso-right">
    <div class="pulso-form-box">
      <div class="pulso-lock-icon">
        <i class="ti tabler-shield-lock"></i>
      </div>

      <div class="pulso-form-header">
        <h3>Registro no disponible</h3>
        <p>El registro público está deshabilitado en este sistema institucional.</p>
      </div>

      <div class="pulso-info-box">
        <p>
          <strong>¿Necesitas acceso?</strong><br>
          Comunícate con el administrador del sistema para que cree tu cuenta de usuario.
        </p>
      </div>

      <a href="{{ route('login') }}" class="pulso-btn-primary">
        <i class="ti tabler-arrow-left" style="font-size:15px"></i>
        Ir al inicio de sesión
      </a>

      <div class="pulso-footer-note">
        <p>
          <i class="ti tabler-shield-check" style="color:#27ae60;vertical-align:-2px"></i>
          {{ $ci?->nombre_institucion ?? 'UGEL Huacaybamba' }}<br>
          Sistema de Control Interno e Integridad Institucional
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
