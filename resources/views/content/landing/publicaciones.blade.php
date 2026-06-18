@php
  $configData = Helper::appClasses();
  $isFront = true;
@endphp

@extends('layouts/layoutLanding')
@section('title', 'Publicaciones — PULSO UGEL Huacaybamba')
@section('meta-robots', 'index, follow')
@section('meta-description', 'Publicaciones y noticias oficiales de la UGEL Huacaybamba sobre Control Interno, gestión institucional y Sistema de Control Interno (SCI).')

@section('page-style')
  @vite(['resources/assets/css/landing-institucional.css'])
  <style>
    .bpage { background: #F1F5F9; min-height: 100vh; overflow-x: hidden; }
    .bpage .ugel-topbar,
    .bpage .ugel-nav,
    .bpage .ugel-footer-new {
      width: 90%; max-width: 1800px;
      margin-left: auto; margin-right: auto;
      border-radius: 0 0 12px 12px;
    }
    .bpage .ugel-footer-new { border-radius: 16px; margin: 2rem auto; }
    .bpage .ugel-topbar { border-radius: 0; }
    @media (max-width: 991px) {
      .bpage .ugel-topbar,
      .bpage .ugel-nav,
      .bpage .ugel-footer-new { width: 96%; }
    }
    @media (max-width: 767px) {
      .bpage .ugel-topbar,
      .bpage .ugel-nav { width: 100%; border-radius: 0; }
      .bpage .ugel-footer-new { width: 100%; border-radius: 0; margin: 0; }
    }
    @media (max-width: 480px) {
      .bpage .ugel-topbar { display: none; }
    }

    /* ── Hero banner ── */
    .bpage-hero {
      background: linear-gradient(135deg, #0a0f2e 0%, #1340A0 55%, #1d52c4 100%);
      padding: 4rem 1rem 3rem;
      position: relative;
      overflow: hidden;
    }
    .bpage-hero::before {
      content: '';
      position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .bpage-hero__inner {
      max-width: 1200px; margin: 0 auto;
      position: relative; z-index: 1;
    }
    .bpage-hero__breadcrumb {
      display: flex; align-items: center; gap: .4rem;
      font-size: .78rem; color: rgba(255,255,255,.6);
      margin-bottom: 1.25rem;
    }
    .bpage-hero__breadcrumb a { color: rgba(255,255,255,.7); text-decoration: none; }
    .bpage-hero__breadcrumb a:hover { color: #fff; }
    .bpage-hero__title {
      font-size: clamp(1.8rem, 4vw, 2.8rem);
      font-weight: 800; color: #fff;
      margin: 0 0 .75rem;
      line-height: 1.15;
    }
    .bpage-hero__sub {
      font-size: 1rem; color: rgba(255,255,255,.75);
      max-width: 580px; margin: 0;
    }
    .bpage-hero__stats {
      display: flex; gap: 2rem;
      margin-top: 1.75rem;
      flex-wrap: wrap;
    }
    .bpage-hero__stat span:first-child {
      display: block; font-size: 1.6rem;
      font-weight: 800; color: #fff;
    }
    .bpage-hero__stat span:last-child {
      font-size: .74rem; color: rgba(255,255,255,.6);
      text-transform: uppercase; letter-spacing: .06em;
    }

    /* ── Filtros ── */
    .bpage-filters {
      background: #fff;
      border-bottom: 1px solid #e2e8f0;
      position: sticky; top: 0; z-index: 100;
    }
    .bpage-filters__inner {
      max-width: 1200px; margin: 0 auto;
      padding: .75rem 1rem;
      display: flex; align-items: center; gap: .5rem;
      flex-wrap: wrap;
    }
    .bpage-filter-btn {
      display: inline-flex; align-items: center; gap: .4rem;
      padding: .38rem .9rem;
      border-radius: 20px;
      font-size: .8rem; font-weight: 600;
      text-decoration: none;
      border: 1.5px solid transparent;
      transition: all .18s;
      cursor: pointer;
    }
    .bpage-filter-btn--all {
      background: #1340A0; color: #fff; border-color: #1340A0;
    }
    .bpage-filter-btn--all.inactive {
      background: transparent; color: #64748b; border-color: #e2e8f0;
    }
    .bpage-filter-btn--all.inactive:hover { border-color: #1340A0; color: #1340A0; }
    .bpage-filter-btn--noticia { background: #eff6ff; color: #1340A0; border-color: #bfdbfe; }
    .bpage-filter-btn--noticia.active,
    .bpage-filter-btn--noticia:hover { background: #1340A0; color: #fff; border-color: #1340A0; }
    .bpage-filter-btn--evento { background: #f0fdf4; color: #059669; border-color: #bbf7d0; }
    .bpage-filter-btn--evento.active,
    .bpage-filter-btn--evento:hover { background: #059669; color: #fff; border-color: #059669; }
    .bpage-filter-btn--normativa { background: #fff7ed; color: #D97706; border-color: #fed7aa; }
    .bpage-filter-btn--normativa.active,
    .bpage-filter-btn--normativa:hover { background: #D97706; color: #fff; border-color: #D97706; }
    .bpage-filter-count {
      font-size: .68rem; background: rgba(255,255,255,.3);
      padding: .1rem .4rem; border-radius: 10px; font-weight: 700;
    }
    .bpage-filter-btn--all .bpage-filter-count { background: rgba(255,255,255,.25); }

    /* ── Grid de cards ── */
    .bpage-main {
      max-width: 1200px; margin: 0 auto;
      padding: 2.5rem 1rem 3rem;
    }
    .bpage-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }
    @media (max-width: 991px) { .bpage-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 575px) { .bpage-grid { grid-template-columns: 1fr; } }

    /* ── Card ── */
    .bcard {
      background: #fff;
      border-radius: 14px;
      overflow: hidden;
      border: 1px solid rgba(0,0,0,.07);
      box-shadow: 0 2px 10px rgba(0,0,0,.06);
      display: flex; flex-direction: column;
      transition: transform .22s, box-shadow .22s;
    }
    .bcard:hover { transform: translateY(-5px); box-shadow: 0 10px 32px rgba(19,64,160,.14); }
    .bcard__img-wrap {
      position: relative; display: block; overflow: hidden;
    }
    .bcard__img {
      width: 100%; height: 200px; object-fit: cover;
      display: block; transition: transform .4s ease;
    }
    .bcard__img--gradient { height: 200px; }
    .bcard__img-wrap:hover .bcard__img { transform: scale(1.05); }
    .bcard__badge {
      position: absolute; top: .8rem; left: .8rem;
      padding: .22rem .65rem; border-radius: 20px;
      font-size: .68rem; font-weight: 700;
      letter-spacing: .04em; text-transform: uppercase;
    }
    .bcard__body {
      padding: 1.15rem 1.3rem 1.3rem;
      display: flex; flex-direction: column; flex: 1;
    }
    .bcard__eye {
      font-size: .68rem; font-weight: 700;
      color: #94a3b8; text-transform: uppercase;
      letter-spacing: .07em; margin: 0 0 .4rem;
    }
    .bcard__title {
      font-size: 1rem; font-weight: 700;
      color: #1a1a2e; line-height: 1.4;
      margin: 0 0 .5rem; flex: 1;
    }
    .bcard__title a { color: inherit; text-decoration: none; }
    .bcard__title a:hover { color: #1340A0; }
    .bcard__desc {
      font-size: .82rem; color: #64748b;
      line-height: 1.55; margin: 0 0 1rem;
    }
    .bcard__foot {
      display: flex; align-items: center;
      justify-content: space-between; gap: .5rem;
      padding-top: .85rem;
      border-top: 1px solid #f1f5f9;
      margin-top: auto;
    }
    .bcard__author {
      display: flex; align-items: center; gap: .45rem;
      font-size: .75rem; color: #64748b;
      min-width: 0; overflow: hidden;
    }
    .bcard__av {
      width: 26px; height: 26px; border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      font-size: .65rem; font-weight: 800; color: #fff; flex-shrink: 0;
    }
    .bcard__author-name {
      overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .bcard__read {
      display: inline-flex; align-items: center; gap: .3rem;
      font-size: .78rem; font-weight: 700;
      color: #1340A0; text-decoration: none; white-space: nowrap; flex-shrink: 0;
    }
    .bcard__read:hover { color: #0c2d80; }

    /* ── Highlight card (primera posición) ── */
    .bcard--featured { grid-column: span 2; }
    .bcard--featured .bcard__img,
    .bcard--featured .bcard__img--gradient { height: 260px; }
    .bcard--featured .bcard__title { font-size: 1.2rem; }
    @media (max-width: 991px) { .bcard--featured { grid-column: span 1; } }

    /* ── Empty state ── */
    .bpage-empty {
      text-align: center; padding: 5rem 1rem;
      color: #94a3b8;
    }
    .bpage-empty__icon { font-size: 3rem; opacity: .3; margin-bottom: 1rem; }

    /* ── Responsive ── */
    @media (max-width: 767px) {
      .bpage-hero { padding: 2.5rem 1rem 2rem; }
      .bpage-hero__stats { gap: 1.25rem; margin-top: 1.25rem; }
      .bpage-hero__stat span:first-child { font-size: 1.3rem; }
      .bpage-filters { position: static; }
      .bpage-filters__inner { gap: .4rem; padding: .6rem .75rem; }
      .bpage-filter-btn { font-size: .75rem; padding: .3rem .7rem; }
      .bpage-main { padding: 1.5rem .75rem 2.5rem; }
      .bcard--featured { grid-column: span 1 !important; }
      .bcard--featured .bcard__img,
      .bcard--featured .bcard__img--gradient { height: 200px; }
      .bcard--featured .bcard__title { font-size: 1rem; }
    }
    @media (max-width: 480px) {
      .bpage-hero { padding: 2rem .75rem 1.75rem; }
      .bpage-hero__title { font-size: 1.6rem; }
      .bpage-hero__sub { font-size: .88rem; }
      .bpage-filter-btn { font-size: .72rem; padding: .28rem .55rem; }
      .bpage-filter-count { display: none; }
      .bpage-main { padding: 1.25rem .5rem 2rem; }
      .bcard__body { padding: .9rem 1rem 1rem; }
    }

    /* ── Paginación ── */
    .bpage-pagination {
      display: flex; justify-content: center; gap: .4rem;
      margin-top: 2.5rem; flex-wrap: wrap;
    }
    .bpage-pagination .page-link {
      border-radius: 8px !important;
      font-size: .82rem; font-weight: 600;
      padding: .45rem .85rem;
      border-color: #e2e8f0; color: #475569;
    }
    .bpage-pagination .page-item.active .page-link {
      background: #1340A0; border-color: #1340A0; color: #fff;
    }
    .bpage-pagination .page-link:hover { background: #eff6ff; color: #1340A0; }
  </style>
@endsection

@section('content')
<div class="bpage">

  {{-- ── Topbar ── --}}
  <div class="ugel-topbar">
    <div class="container">
      <div class="ugel-topbar__inner">
        <div class="ugel-topbar__left">
          {{ $config?->direccion ?? 'Jr. Huacaybamba S/N, Huacaybamba — Huánuco' }}
          <span class="ugel-topbar__sep">|</span>
          Lun – Vie: 8:00 am – 6:00 pm
        </div>
        <div class="ugel-topbar__right">
          <span class="ugel-topbar__live"><span class="ugel-topbar__dot"></span> En línea</span>
          <a href="{{ route('login') }}" class="ugel-topbar__login">Acceso al Sistema</a>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Navbar ── --}}
  <nav class="ugel-nav" id="ugelNav">
    <div class="container">
      <div class="ugel-nav__inner">
        <a href="{{ route('landing') }}" class="ugel-nav__brand">
          <div class="ugel-nav__escudo">
            @if($config?->logo_ruta)
              <img src="{{ Storage::url($config->logo_ruta) }}" alt="{{ $config->sigla ?? 'UGEL' }}" style="width:42px;height:42px;object-fit:contain;">
            @else
              <svg width="32" height="32" viewBox="0 0 48 48" fill="none"><path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 C14 44 4 38 4 28 L4 12 Z" fill="#d32f2f" opacity=".9"/><path d="M24 4 L24 47 C14 44 4 38 4 28 L4 12 Z" fill="#1a237e" opacity=".85"/><path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 L24 4 Z" fill="#c62828" opacity=".7"/><path d="M18 20 L22 26 L30 18" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
            @endif
          </div>
          <div class="ugel-nav__brand-text">
            <span class="ugel-nav__brand-title">{{ $config?->sigla ?? 'Ugel Huacaybamba' }}</span>
            <span class="ugel-nav__brand-sub">{{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local' }}</span>
          </div>
        </a>
        <ul class="ugel-nav__links" id="ugelNavLinks">
          <li class="ugel-nav__close-item">
            <button class="ugel-nav__close-btn" id="ugelNavClose" aria-label="Cerrar menú">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </li>
          <li>
            <a href="{{ route('landing') }}" class="ugel-nav__link">
              <span class="ugel-nav__link-ico"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
              Inicio
            </a>
          </li>
          <li>
            <a href="{{ route('landing') }}#sistema" class="ugel-nav__link">
              <span class="ugel-nav__link-ico"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
              Sistema PULSO
            </a>
          </li>
          <li>
            <a href="{{ route('landing') }}#modulos" class="ugel-nav__link">
              <span class="ugel-nav__link-ico"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
              Módulos
            </a>
          </li>
          <li>
            <a href="{{ route('landing.publicaciones') }}" class="ugel-nav__link active">
              <span class="ugel-nav__link-ico"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
              Publicaciones
            </a>
          </li>
          <li>
            <a href="{{ route('landing') }}#normativa" class="ugel-nav__link">
              <span class="ugel-nav__link-ico"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></span>
              Normativa
            </a>
          </li>
          <li>
            <a href="{{ route('landing') }}#contacto" class="ugel-nav__link">
              <span class="ugel-nav__link-ico"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
              Contacto
            </a>
          </li>
        </ul>
        <div class="ugel-nav__end">
          <button class="ugel-burger" id="ugelBurger"><span></span><span></span><span></span></button>
        </div>
      </div>
    </div>
  </nav>
  <div class="ugel-overlay" id="ugelOverlay"></div>

  {{-- ── Hero ── --}}
  <div class="bpage-hero">
    <div class="bpage-hero__inner">
      <nav class="bpage-hero__breadcrumb" aria-label="breadcrumb">
        <a href="{{ route('landing') }}">Inicio</a>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        <span>Publicaciones</span>
      </nav>
      <h1 class="bpage-hero__title">
        {{ $tipo ? ucfirst($tipo) . 's' : 'Publicaciones' }}
      </h1>
      <p class="bpage-hero__sub">
        Noticias, eventos y normativas de la {{ $config?->sigla ?? 'UGEL Huacaybamba' }} — Sistema de Control Interno PULSO.
      </p>
      <div class="bpage-hero__stats">
        <div class="bpage-hero__stat">
          <span>{{ $totales['noticia'] }}</span>
          <span>Noticias</span>
        </div>
        <div class="bpage-hero__stat">
          <span>{{ $totales['evento'] }}</span>
          <span>Eventos</span>
        </div>
        <div class="bpage-hero__stat">
          <span>{{ $totales['normativa'] }}</span>
          <span>Normativas</span>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Filtros ── --}}
  <div class="bpage-filters">
    <div class="bpage-filters__inner">
      <a href="{{ route('landing.publicaciones') }}"
         class="bpage-filter-btn bpage-filter-btn--all {{ !$tipo ? '' : 'inactive' }}">
        Todas
        <span class="bpage-filter-count">{{ array_sum($totales) }}</span>
      </a>
      <a href="{{ route('landing.publicaciones', ['tipo' => 'noticia']) }}"
         class="bpage-filter-btn bpage-filter-btn--noticia {{ $tipo === 'noticia' ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        Noticias
        <span class="bpage-filter-count">{{ $totales['noticia'] }}</span>
      </a>
      <a href="{{ route('landing.publicaciones', ['tipo' => 'evento']) }}"
         class="bpage-filter-btn bpage-filter-btn--evento {{ $tipo === 'evento' ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Eventos
        <span class="bpage-filter-count">{{ $totales['evento'] }}</span>
      </a>
      <a href="{{ route('landing.publicaciones', ['tipo' => 'normativa']) }}"
         class="bpage-filter-btn bpage-filter-btn--normativa {{ $tipo === 'normativa' ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Normativas
        <span class="bpage-filter-count">{{ $totales['normativa'] }}</span>
      </a>
    </div>
  </div>

  {{-- ── Grid principal ── --}}
  <div class="bpage-main">

    @if($publicaciones->count() > 0)
      <div class="bpage-grid">
        @foreach($publicaciones as $i => $pub)
          @php
            $tipoColor = match($pub->tipo) { 'evento' => '#059669', 'normativa' => '#D97706', default => '#1340A0' };
            $tipoBg    = match($pub->tipo) { 'evento' => '#f0fdf4', 'normativa' => '#fff7ed', default => '#eff6ff' };
            $featured  = ($i === 0 && !$tipo && $publicaciones->currentPage() === 1);
          @endphp
          <article class="bcard {{ $featured ? 'bcard--featured' : '' }}">
            <a href="{{ route('landing.noticia', $pub->id) }}" class="bcard__img-wrap">
              @if($pub->imagen_url)
                <img src="{{ $pub->imagen_url }}" alt="{{ $pub->titulo }}" class="bcard__img" loading="lazy">
              @else
                <div class="bcard__img bcard__img--gradient" style="background:{{ $pub->color_gradiente ?? 'linear-gradient(135deg,#1340A0,#7367f0)' }};"></div>
              @endif
              <span class="bcard__badge" style="background:{{ $tipoBg }};color:{{ $tipoColor }};border:1px solid {{ $tipoColor }}33;">{{ ucfirst($pub->tipo) }}</span>
            </a>
            <div class="bcard__body">
              @if($pub->etiqueta)
                <p class="bcard__eye">{{ $pub->etiqueta }}</p>
              @endif
              <h2 class="bcard__title">
                <a href="{{ route('landing.noticia', $pub->id) }}">{{ Str::limit($pub->titulo, $featured ? 100 : 70) }}</a>
              </h2>
              @if($pub->descripcion)
                <p class="bcard__desc">{{ Str::limit($pub->descripcion, $featured ? 180 : 110) }}</p>
              @endif
              <div class="bcard__foot">
                @if($pub->autor)
                  <span class="bcard__author">
                    <span class="bcard__av" style="background:linear-gradient(135deg,#1340A0,#28c76f);">{{ strtoupper(substr($pub->autor,0,1)) }}</span>
                    <span class="bcard__author-name">{{ $pub->autor }}</span>
                  </span>
                @else
                  <span></span>
                @endif
                <a href="{{ route('landing.noticia', $pub->id) }}" class="bcard__read">
                  Leer más
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
              </div>
            </div>
          </article>
        @endforeach
      </div>

      {{-- Paginación --}}
      @if($publicaciones->hasPages())
        <div class="bpage-pagination">
          {{ $publicaciones->links('pagination::bootstrap-5') }}
        </div>
      @endif

    @else
      <div class="bpage-empty">
        <div class="bpage-empty__icon">
          <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <p style="font-size:1.1rem;font-weight:600;color:#475569;">
          No hay publicaciones de tipo <strong>{{ ucfirst($tipo) }}</strong> aún.
        </p>
        <a href="{{ route('landing.publicaciones') }}" style="color:#1340A0;font-weight:600;font-size:.9rem;">
          Ver todas las publicaciones →
        </a>
      </div>
    @endif

  </div>

  {{-- ── Footer ── --}}
  <footer class="ugel-footer-new">
    <div class="ugel-footer-new__top">
      <div class="container">
        <div class="ugel-footer-new__grid">
          <div class="ugel-fn-brand">
            <div class="ugel-fn-brand__logo">
              @if($config?->logo_ruta)
                <img src="{{ Storage::url($config->logo_ruta) }}" alt="{{ $config?->sigla ?? 'UGEL' }}">
              @else
                <svg width="36" height="36" viewBox="0 0 48 48" fill="none"><path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 C14 44 4 38 4 28 L4 12 Z" fill="#c62828" opacity=".9"/><path d="M24 4 L24 47 C14 44 4 38 4 28 L4 12 Z" fill="#1a237e" opacity=".85"/><path d="M18 20 L22 26 L30 18" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
              @endif
              <div>
                <strong>{{ $config?->sigla ?? 'UGEL Huacaybamba' }}</strong>
                <span>{{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local' }}</span>
              </div>
            </div>
            <p class="ugel-fn-brand__desc">
              {{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local de Huacaybamba' }},
              Región {{ $config?->region ?? 'Huánuco' }}, Perú.
            </p>
          </div>
          <div class="ugel-fn-col">
            <h6 class="ugel-fn-col__title"><span class="ugel-fn-col__title-ico"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></span>Navegación</h6>
            <ul>
              <li><a href="{{ route('landing') }}"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>Inicio</a></li>
              <li><a href="{{ route('landing') }}#sistema"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>Sistema PULSO</a></li>
              <li><a href="{{ route('landing') }}#modulos"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>Módulos</a></li>
              <li><a href="{{ route('landing.publicaciones') }}"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg></span>Publicaciones</a></li>
              <li><a href="{{ route('landing') }}#normativa"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></span>Normativa</a></li>
              <li><a href="{{ route('login') }}" class="ugel-fn-link--accent"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>Acceso al Sistema</a></li>
            </ul>
          </div>
          <div class="ugel-fn-col">
            <h6 class="ugel-fn-col__title"><span class="ugel-fn-col__title-ico"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>Instituciones</h6>
            <ul>
              @forelse($instituciones->take(6) as $inst)
                <li><a href="{{ $inst->url_sitio ?? '#' }}" @if($inst->url_sitio) target="_blank" rel="noopener" @endif>
                  <span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>
                  <span class="ugel-fn-col__abbr">{{ $inst->sigla }}</span> {{ $inst->nombre }}
                </a></li>
              @empty
                <li><a href="#"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>Contraloría General de la Rep.</a></li>
                <li><a href="#"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>Ministerio de Educación</a></li>
              @endforelse
            </ul>
          </div>
          <div class="ugel-fn-col">
            <h6 class="ugel-fn-col__title"><span class="ugel-fn-col__title-ico"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>Publicaciones</h6>
            <ul>
              <li><a href="{{ route('landing.publicaciones') }}"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></span>Todas las publicaciones</a></li>
              <li><a href="{{ route('landing.publicaciones', ['tipo' => 'noticia']) }}"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>Noticias</a></li>
              <li><a href="{{ route('landing.publicaciones', ['tipo' => 'evento']) }}"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>Eventos</a></li>
              <li><a href="{{ route('landing.publicaciones', ['tipo' => 'normativa']) }}"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>Normativas</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="ugel-footer-new__bottom">
      <div class="container">
        <div class="ugel-footer-new__bottom-inner">
          <span>© {{ date('Y') }} {{ $config?->sigla ?? 'UGEL Huacaybamba' }} · Todos los derechos reservados.</span>
          <div class="ugel-footer-new__bottom-right">
            <span class="ugel-fn-status">
              <span class="ugel-fn-status__dot"></span>
              Sistema PULSO · Control Interno
            </span>
          </div>
        </div>
      </div>
    </div>
  </footer>

</div>
@endsection

@section('page-script')
  @vite(['resources/assets/js/landing-institucional.js'])
  <script>
    // Burger menu (mismo patrón que landing)
    document.addEventListener('DOMContentLoaded', function () {
      var burger  = document.getElementById('ugelBurger');
      var links   = document.getElementById('ugelNavLinks');
      var overlay = document.getElementById('ugelOverlay');
      if (!burger) return;
      function openMenu() { burger.classList.add('open'); links?.classList.add('open'); overlay?.classList.add('open'); }
      function closeMenu() { burger.classList.remove('open'); links?.classList.remove('open'); overlay?.classList.remove('open'); }
      burger.addEventListener('click', function () { this.classList.contains('open') ? closeMenu() : openMenu(); });
      overlay?.addEventListener('click', closeMenu);
    });
  </script>
@endsection
