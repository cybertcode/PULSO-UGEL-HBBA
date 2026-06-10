@php
  $configData = Helper::appClasses();
  $isFront = true;
@endphp

@extends('layouts/layoutLanding')
@section('title', $noticia->titulo . ' — PULSO UGEL')

@section('page-style')
  @vite(['resources/assets/css/landing-institucional.css'])
@endsection
@section('page-script')
  @vite(['resources/assets/js/landing-institucional.js'])
@endsection

@section('content')

{{-- BARRA SUPERIOR --}}
<div class="ugel-topbar">
  <div class="container">
    <div class="ugel-topbar__inner">
      <div class="ugel-topbar__left">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Jr. Huacaybamba S/N, Huacaybamba — Huánuco
        <span class="ugel-topbar__sep">|</span>
        Lun – Vie: 8:00 am – 4:30 pm
      </div>
      <div class="ugel-topbar__right">
        <span class="ugel-topbar__live"><span class="ugel-topbar__dot"></span> Sistema en línea</span>
        <a href="{{ route('login') }}" class="ugel-topbar__login">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Acceso al Sistema
        </a>
      </div>
    </div>
  </div>
</div>

{{-- NAVBAR --}}
<nav class="ugel-nav scrolled" style="position:sticky;top:0;">
  <div class="container">
    <div class="ugel-nav__inner">
      <a href="{{ route('landing') }}" class="ugel-nav__brand">
        <div class="ugel-nav__escudo">
          <svg width="32" height="32" viewBox="0 0 48 48" fill="none">
            <path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 C14 44 4 38 4 28 L4 12 Z" fill="#c62828" opacity=".9"/>
            <path d="M24 4 L24 47 C14 44 4 38 4 28 L4 12 Z" fill="#1a237e" opacity=".85"/>
            <path d="M18 20 L22 26 L30 18" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          </svg>
        </div>
        <div class="ugel-nav__brand-text">
          <span class="ugel-nav__brand-title">UGEL HUACAYBAMBA</span>
          <span class="ugel-nav__brand-sub">Unidad de Gestión Educativa Local · Huánuco</span>
        </div>
      </a>
      <div style="flex:1"></div>
      <div class="ugel-nav__end">
        <a href="{{ route('landing') }}" class="ugel-btn ugel-btn--outline" style="font-size:.78rem;padding:.45rem .9rem;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          Volver al inicio
        </a>
        <a href="{{ route('login') }}" class="ugel-btn ugel-btn--primary">
          Ingresar al Sistema
        </a>
      </div>
    </div>
  </div>
</nav>

{{-- HERO DE LA NOTICIA --}}
<div class="ugel-noticia-hero"
     style="@if($noticia->imagen_url) background-image:url('{{ $noticia->imagen_url }}'); @else background: {{ $noticia->color_gradiente ?? 'linear-gradient(135deg,#0d1b2a,#1a237e,#1565c0)' }}; @endif">
  <div class="ugel-noticia-hero__overlay"></div>
  <div class="container" style="position:relative;z-index:2;padding-bottom:2.5rem;">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb mb-0" style="font-size:.72rem;">
        <li class="breadcrumb-item"><a href="{{ route('landing') }}" style="color:rgba(255,255,255,.5);text-decoration:none;">Inicio</a></li>
        <li class="breadcrumb-item" style="color:rgba(255,255,255,.4);">Noticias</li>
        <li class="breadcrumb-item active" style="color:rgba(255,255,255,.75);">{{ Str::limit($noticia->titulo, 45) }}</li>
      </ol>
    </nav>
    <span class="ugel-slide__tipo ugel-tipo--{{ $noticia->tipo }} mb-3 d-inline-block">
      {{ $noticia->etiqueta ?? ucfirst($noticia->tipo) }}
    </span>
    <h1 style="font-size:1.7rem;font-weight:800;color:#fff;line-height:1.25;max-width:680px;letter-spacing:-.02em;margin-top:.5rem;">
      {{ $noticia->titulo }}
    </h1>
    <p style="font-size:.78rem;color:rgba(255,255,255,.45);margin-top:.6rem;">
      {{ $noticia->created_at->format('d \d\e F \d\e Y') }}
    </p>
  </div>
</div>

{{-- CONTENIDO --}}
<section style="padding:3.5rem 0;background:#f4f6f9;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">

        <div style="background:#fff;border:1px solid #dde3ed;border-radius:8px;padding:2rem 2.5rem;margin-bottom:1.2rem;border-top:3px solid #1a237e;">
          @if($noticia->descripcion)
          <p style="font-size:.95rem;line-height:1.8;color:#2c3e50;margin-bottom:1.5rem;">{{ $noticia->descripcion }}</p>
          @endif

          @if($noticia->url_accion && $noticia->texto_accion)
          <a href="{{ $noticia->url_accion }}" class="ugel-btn ugel-btn--primary" target="_blank" rel="noopener">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            {{ $noticia->texto_accion }}
          </a>
          @endif

          <hr style="margin:1.5rem 0;border-color:#dde3ed;">
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
            <span style="font-size:.75rem;color:#607080;">
              Publicado el {{ $noticia->created_at->format('d M Y') }}
              · <strong style="color:#1a237e;">{{ ucfirst($noticia->tipo) }}</strong>
            </span>
            <a href="{{ route('login') }}" class="ugel-btn ugel-btn--primary" style="font-size:.78rem;padding:.45rem .9rem;">
              Ingresar al Sistema
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

{{-- RELACIONADAS --}}
@if($relacionadas->count())
<section style="padding:0 0 3.5rem;background:#f4f6f9;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <h5 style="font-size:.82rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#607080;margin-bottom:1.2rem;padding-left:.75rem;position:relative;">
          <span style="position:absolute;left:0;top:50%;transform:translateY(-50%);width:3px;height:14px;background:#c62828;border-radius:2px;"></span>
          Más publicaciones
        </h5>
        <div class="row g-3">
          @foreach($relacionadas as $rel)
          <div class="col-sm-4">
            <a href="{{ route('landing.noticia', $rel->id) }}" style="text-decoration:none;">
              <div style="background:#fff;border:1px solid #dde3ed;border-radius:8px;overflow:hidden;transition:transform .3s,box-shadow .3s;"
                   onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.1)'"
                   onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div style="height:120px;@if($rel->imagen_url)background-image:url('{{ $rel->imagen_url }}');background-size:cover;background-position:center;@else background:{{ $rel->color_gradiente ?? 'linear-gradient(135deg,#1a237e,#1565c0)' }};@endif position:relative;">
                  <span class="ugel-slide__tipo ugel-tipo--{{ $rel->tipo }}" style="position:absolute;top:.6rem;left:.6rem;">{{ ucfirst($rel->tipo) }}</span>
                </div>
                <div style="padding:.85rem;">
                  <p style="font-size:.65rem;color:#607080;margin-bottom:.3rem;">{{ $rel->created_at->format('d M Y') }}</p>
                  <p style="font-size:.78rem;font-weight:700;color:#2c3e50;line-height:1.3;margin:0;">{{ Str::limit($rel->titulo, 60) }}</p>
                </div>
              </div>
            </a>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
@endif

{{-- FOOTER SIMPLE --}}
<footer style="background:#0d1b2a;padding:1.5rem 0;border-top:3px solid #c62828;">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
      <div style="display:flex;align-items:center;gap:.6rem;">
        <svg width="22" height="22" viewBox="0 0 48 48" fill="none">
          <path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 C14 44 4 38 4 28 L4 12 Z" fill="#c62828" opacity=".9"/>
          <path d="M24 4 L24 47 C14 44 4 38 4 28 L4 12 Z" fill="#1a237e" opacity=".85"/>
          <path d="M18 20 L22 26 L30 18" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>
        <div>
          <div style="font-size:.78rem;font-weight:700;color:#fff;">UGEL HUACAYBAMBA</div>
          <div style="font-size:.6rem;color:rgba(255,255,255,.35);">Sistema PULSO UGEL</div>
        </div>
      </div>
      <span style="font-size:.68rem;color:rgba(255,255,255,.25);">© {{ date('Y') }} UGEL Huacaybamba — Gobierno Regional Huánuco</span>
    </div>
  </div>
</footer>

@endsection
