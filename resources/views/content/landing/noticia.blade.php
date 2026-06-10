@php
  $configData = Helper::appClasses();
  $isFront = true;
  // Tiempo de lectura estimado
  $texto = strip_tags($noticia->contenido ?? $noticia->descripcion ?? '');
  $palabras = str_word_count($texto);
  $minutos  = max(1, (int) ceil($palabras / 200));
@endphp

@extends('layouts/layoutLanding')
@section('title', $noticia->titulo . ' — PULSO UGEL')

@section('page-style')
  @vite(['resources/assets/css/landing-institucional.css'])
  <style>
    /* ── Barra topbar en noticia ── */
    .ugel-noticia-page { background: #f0f4fa; min-height: 100vh; }

    /* ── Hero ── */
    .noti-hero {
      position: relative;
      min-height: 420px;
      display: flex; flex-direction: column; justify-content: flex-end;
      overflow: hidden;
    }
    .noti-hero__bg {
      position: absolute; inset: 0;
      background-size: cover; background-position: center;
    }
    .noti-hero__overlay {
      position: absolute; inset: 0;
      background: linear-gradient(to top, rgba(7,15,35,.92) 0%, rgba(7,15,35,.55) 50%, rgba(7,15,35,.25) 100%);
    }
    .noti-hero__content {
      position: relative; z-index: 2;
      padding: 3rem 0 2.5rem;
    }

    /* ── Layout artículo ── */
    .noti-body { padding: 3rem 0 4rem; }
    .noti-article {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 20px rgba(19,64,160,.08);
      overflow: hidden;
    }
    .noti-article__portada {
      width: 100%; max-height: 400px;
      object-fit: cover; display: block;
    }
    .noti-article__body {
      padding: 2rem 2.5rem;
    }
    .noti-article__meta {
      display: flex; flex-wrap: wrap; align-items: center; gap: .75rem;
      padding-bottom: 1.25rem;
      border-bottom: 1px solid #e8edf5;
      margin-bottom: 1.75rem;
      font-size: .78rem; color: #607080;
    }
    .noti-article__meta-item {
      display: flex; align-items: center; gap: .3rem;
    }
    .noti-article__content {
      font-size: .96rem; line-height: 1.9; color: #2c3e50;
    }
    .noti-article__content h2 { font-size: 1.25rem; font-weight: 700; margin: 1.75rem 0 .75rem; color: #0D2554; }
    .noti-article__content h3 { font-size: 1.05rem; font-weight: 700; margin: 1.5rem 0 .5rem; color: #1340A0; }
    .noti-article__content p  { margin-bottom: 1.1rem; }
    .noti-article__content ul,
    .noti-article__content ol { padding-left: 1.4rem; margin-bottom: 1.1rem; }
    .noti-article__content li { margin-bottom: .35rem; }
    .noti-article__content blockquote {
      border-left: 4px solid #1340A0;
      padding: .75rem 1.25rem;
      background: #EFF6FF;
      border-radius: 0 8px 8px 0;
      margin: 1.25rem 0;
      color: #1340A0;
      font-style: italic;
    }
    .noti-article__content a { color: #1340A0; text-decoration: underline; }
    .noti-article__content img { max-width: 100%; border-radius: 8px; margin: 1rem 0; }
    .noti-article__content pre,
    .noti-article__content code {
      background: #f4f6f9; padding: .2em .5em; border-radius: 4px;
      font-size: .85em;
    }
    .noti-article__content pre { padding: 1rem; overflow-x: auto; }

    /* ── Sidebar ── */
    .noti-sidebar { position: sticky; top: 88px; }
    .noti-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 16px rgba(19,64,160,.07);
      padding: 1.4rem 1.5rem;
      margin-bottom: 1.25rem;
    }
    .noti-card__title {
      font-size: .7rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: .1em; color: #607080;
      margin-bottom: 1rem;
      padding-left: .6rem;
      border-left: 3px solid #1340A0;
    }
    .noti-related-item {
      display: flex; gap: .75rem;
      text-decoration: none;
      padding: .6rem 0;
      border-bottom: 1px solid #f0f4fa;
      transition: opacity .2s;
    }
    .noti-related-item:last-child { border-bottom: none; }
    .noti-related-item:hover { opacity: .8; }
    .noti-related-thumb {
      width: 64px; height: 48px; flex-shrink: 0;
      border-radius: 6px; overflow: hidden;
    }
    .noti-related-thumb-inner {
      width: 100%; height: 100%;
      object-fit: cover;
    }
    .noti-related-info { flex: 1; min-width: 0; }
    .noti-related-date { font-size: .65rem; color: #99aab8; }
    .noti-related-tit  { font-size: .75rem; font-weight: 700; color: #2c3e50; line-height: 1.35; }

    /* ── CTA inline ── */
    .noti-cta {
      background: linear-gradient(135deg, #0D2554, #1340A0 40%, #1A52C4, #1340A0);
      border-radius: 12px;
      padding: 1.5rem;
      text-align: center;
      color: #fff;
      margin-top: 2rem;
    }
    .noti-cta h5 { font-size: .88rem; font-weight: 800; margin-bottom: .4rem; }
    .noti-cta p  { font-size: .75rem; opacity: .75; margin-bottom: .9rem; }

    /* ── Footer página noticia ── */
    .noti-footer {
      background: linear-gradient(135deg, #0D2554, #1340A0);
      padding: 1.5rem 0;
    }

    @media (max-width: 767px) {
      .noti-article__body { padding: 1.5rem 1.25rem; }
      .noti-hero { min-height: 280px; }
    }
  </style>
@endsection

@section('page-script')
  @vite(['resources/assets/js/landing-institucional.js'])
@endsection

@section('content')
<div class="ugel-noticia-page">

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
<nav class="ugel-nav scrolled" style="position:sticky;top:0;z-index:900;">
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

{{-- ══ HERO ══ --}}
<div class="noti-hero">
  <div class="noti-hero__bg"
       style="@if($noticia->imagen_portada_url ?? $noticia->imagen_url)
                background-image: url('{{ $noticia->imagen_portada_url ?? $noticia->imagen_url }}');
              @else
                background: {{ $noticia->color_gradiente ?? 'linear-gradient(135deg,#0D2554,#1340A0,#1A52C4)' }};
              @endif">
  </div>
  <div class="noti-hero__overlay"></div>
  <div class="noti-hero__content">
    <div class="container">
      {{-- Breadcrumb --}}
      <nav aria-label="breadcrumb" class="mb-3">
        <ol style="list-style:none;display:flex;flex-wrap:wrap;gap:.35rem;align-items:center;padding:0;margin:0;font-size:.72rem;">
          <li><a href="{{ route('landing') }}" style="color:rgba(255,255,255,.5);text-decoration:none;">Inicio</a></li>
          <li style="color:rgba(255,255,255,.3);">/</li>
          <li style="color:rgba(255,255,255,.4);">Publicaciones</li>
          <li style="color:rgba(255,255,255,.3);">/</li>
          <li style="color:rgba(255,255,255,.7);">{{ Str::limit($noticia->titulo, 50) }}</li>
        </ol>
      </nav>

      {{-- Badge tipo --}}
      <span class="ugel-slide__tipo ugel-tipo--{{ $noticia->tipo }} mb-3 d-inline-block">
        {{ $noticia->etiqueta ?? ucfirst($noticia->tipo) }}
      </span>

      {{-- Título --}}
      <h1 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:800;color:#fff;line-height:1.2;max-width:760px;letter-spacing:-.025em;margin:.5rem 0 1rem;">
        {{ $noticia->titulo }}
      </h1>

      {{-- Meta --}}
      <div style="display:flex;flex-wrap:wrap;align-items:center;gap:1.25rem;font-size:.78rem;color:rgba(255,255,255,.55);">
        <span style="display:flex;align-items:center;gap:.35rem;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          {{ $noticia->created_at->translatedFormat('d \d\e F \d\e Y') }}
        </span>
        <span style="display:flex;align-items:center;gap:.35rem;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          {{ $minutos }} min de lectura
        </span>
        @if($noticia->autor)
        <span style="display:flex;align-items:center;gap:.35rem;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          {{ $noticia->autor }}
        </span>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- ══ CUERPO ══ --}}
<div class="noti-body">
  <div class="container">
    <div class="row g-4">

      {{-- Columna principal --}}
      <div class="col-lg-8">
        <article class="noti-article">

          {{-- Imagen portada dentro del artículo (si existe y difiere del hero) --}}
          @if(($noticia->imagen_portada_url ?? $noticia->imagen_url) && !($noticia->imagen_portada_url ?? $noticia->imagen_url))
          {{-- solo si no se usó arriba ya --}}
          @endif

          <div class="noti-article__body">

            {{-- Meta inferior --}}
            <div class="noti-article__meta">
              <span class="noti-article__meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1340A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Publicado {{ $noticia->created_at->diffForHumans() }}
              </span>
              <span class="noti-article__meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1340A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                <strong style="color:#1340A0;">{{ ucfirst($noticia->tipo) }}</strong>
              </span>
              <span class="noti-article__meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1340A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ $minutos }} min lectura
              </span>
              @if($noticia->autor)
              <span class="noti-article__meta-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1340A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ $noticia->autor }}
              </span>
              @endif
            </div>

            {{-- Resumen/Descripción destacado --}}
            @if($noticia->descripcion)
            <div style="background:#EFF6FF;border-left:4px solid #1340A0;padding:1rem 1.25rem;border-radius:0 8px 8px 0;margin-bottom:1.75rem;">
              <p style="font-size:1rem;line-height:1.7;color:#1340A0;font-weight:500;margin:0;">{{ $noticia->descripcion }}</p>
            </div>
            @endif

            {{-- Contenido enriquecido --}}
            @if($noticia->contenido)
            <div class="noti-article__content">
              {!! $noticia->contenido !!}
            </div>
            @endif

            {{-- Botón CTA si tiene URL --}}
            @if($noticia->url_accion && $noticia->texto_accion)
            <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #e8edf5;">
              <a href="{{ $noticia->url_accion }}" class="ugel-btn ugel-btn--primary" target="_blank" rel="noopener">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                {{ $noticia->texto_accion }}
              </a>
            </div>
            @endif

            {{-- Share / Acciones --}}
            <div style="margin-top:2rem;padding-top:1.25rem;border-top:1px solid #e8edf5;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
              <div style="display:flex;gap:.5rem;align-items:center;">
                <span style="font-size:.75rem;color:#607080;font-weight:600;">Compartir:</span>
                <button onclick="navigator.share ? navigator.share({title:document.title,url:location.href}) : navigator.clipboard.writeText(location.href).then(()=>alert('Enlace copiado'))"
                        style="background:#EFF6FF;border:none;border-radius:6px;padding:.4rem .8rem;font-size:.72rem;color:#1340A0;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.3rem;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                  Copiar enlace
                </button>
              </div>
              <a href="{{ route('login') }}" class="ugel-btn ugel-btn--primary" style="font-size:.78rem;padding:.45rem .9rem;">
                Ingresar al Sistema
              </a>
            </div>

          </div>
        </article>
      </div>

      {{-- Sidebar --}}
      <div class="col-lg-4">
        <aside class="noti-sidebar">

          {{-- CTA Sistema --}}
          <div class="noti-cta">
            <h5>Sistema PULSO UGEL</h5>
            <p>Gestiona el Control Interno Institucional desde una sola plataforma.</p>
            <a href="{{ route('login') }}" class="ugel-btn ugel-btn--primary" style="width:100%;justify-content:center;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
              Acceder al Sistema
            </a>
          </div>

          {{-- Más publicaciones --}}
          @if($relacionadas->count())
          <div class="noti-card">
            <div class="noti-card__title">Más publicaciones</div>
            @foreach($relacionadas as $rel)
            <a href="{{ route('landing.noticia', $rel->id) }}" class="noti-related-item">
              <div class="noti-related-thumb">
                @if($rel->imagen_portada_url ?? $rel->imagen_url)
                  <img src="{{ $rel->imagen_portada_url ?? $rel->imagen_url }}" alt=""
                       class="noti-related-thumb-inner" style="object-fit:cover;width:100%;height:100%;">
                @else
                  <div class="noti-related-thumb-inner" style="background:{{ $rel->color_gradiente ?? 'linear-gradient(135deg,#1340A0,#1A52C4)' }};display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.6)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  </div>
                @endif
              </div>
              <div class="noti-related-info">
                <div class="noti-related-date">{{ $rel->created_at->format('d M Y') }} · {{ ucfirst($rel->tipo) }}</div>
                <div class="noti-related-tit">{{ Str::limit($rel->titulo, 65) }}</div>
              </div>
            </a>
            @endforeach
          </div>
          @endif

          {{-- Info UGEL --}}
          <div class="noti-card">
            <div class="noti-card__title">Información institucional</div>
            <div style="font-size:.78rem;color:#607080;line-height:1.7;">
              <div style="display:flex;gap:.5rem;margin-bottom:.5rem;align-items:flex-start;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1340A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:.15rem;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Jr. Huacaybamba S/N, Huacaybamba — Huánuco
              </div>
              <div style="display:flex;gap:.5rem;align-items:center;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1340A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Lun – Vie: 8:00 am – 4:30 pm
              </div>
            </div>
          </div>

        </aside>
      </div>

    </div>
  </div>
</div>

{{-- ══ FOOTER ══ --}}
<footer class="noti-footer">
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

</div>{{-- .ugel-noticia-page --}}
@endsection
