@php
    $configData = Helper::appClasses();
    $isFront = true;
    $texto = strip_tags($noticia->contenido ?? ($noticia->descripcion ?? ''));
    $palabras = str_word_count($texto);
    $minutos = max(1, (int) ceil($palabras / 200));
    $colorTipo = match ($noticia->tipo) {
        'evento' => '#059669',
        'normativa' => '#D97706',
        default => '#1340A0',
    };
@endphp

@extends('layouts/layoutLanding')
@section('title', $noticia->titulo . ' — PULSO UGEL')

@section('page-style')
    @vite(['resources/assets/css/landing-institucional.css'])
    <style>
        /* ══ Reset de página ══ */
        .npage {
            background: #F1F5F9;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Ancho global consistente con la landing */
        .npage .ugel-topbar,
        .npage .ugel-nav,
        .npage .ugel-footer-new {
            width: 90%;
            max-width: 1800px;
            margin-left: auto;
            margin-right: auto;
            border-radius: 0 0 12px 12px;
        }

        .npage .ugel-footer-new {
            border-radius: 16px;
            margin: 2rem auto;
        }

        .npage .ugel-topbar {
            border-radius: 0;
        }

        @media(max-width:991px) {

            .npage .ugel-topbar,
            .npage .ugel-nav,
            .npage .ugel-footer-new {
                width: 96%;
            }
        }

        @media(max-width:767px) {

            .npage .ugel-topbar,
            .npage .ugel-nav,
            .npage .ugel-footer-new {
                width: 100%;
                border-radius: 0 !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .npage .ugel-footer-new {
                margin: 0 !important;
                border-radius: 0 !important;
            }
        }

        @media(max-width:480px) {
            .npage .ugel-topbar {
                display: none;
            }
        }

        /* ══ Hero ══ */
        .npage-hero {
            position: relative;
            min-height: 480px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            overflow: hidden;
        }

        .npage-hero__img {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            transition: transform 8s ease;
        }

        .npage-hero:hover .npage-hero__img {
            transform: scale(1.03);
        }

        .npage-hero__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top,
                    rgba(5, 12, 35, .96) 0%,
                    rgba(5, 12, 35, .7) 45%,
                    rgba(5, 12, 35, .25) 80%,
                    transparent 100%);
        }

        .npage-hero__body {
            position: relative;
            z-index: 2;
            padding: 0 0 3rem;
        }

        .npage-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem .8rem;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: .9rem;
        }

        .npage-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            opacity: .8;
        }

        /* ══ Layout principal ══ */
        .npage-layout {
            padding: 2.5rem 0 4rem;
        }

        /* ══ Artículo ══ */
        .npage-article {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 24px rgba(13, 37, 84, .08);
            overflow: hidden;
        }

        .npage-article__hero-img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            display: block;
        }

        .npage-article__inner {
            padding: 2rem 2.5rem 2.5rem;
        }

        /* Meta bar */
        .npage-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            padding: .75rem 1rem;
            background: #F8FAFF;
            border-radius: 8px;
            margin-bottom: 1.75rem;
            border: 1px solid #E2EAFC;
        }

        .npage-meta__item {
            display: flex;
            align-items: center;
            gap: .35rem;
            font-size: .76rem;
            color: #5B6F8A;
            font-weight: 500;
        }

        .npage-meta__item svg {
            flex-shrink: 0;
        }

        .npage-meta__item strong {
            color: #1340A0;
        }

        /* Lead / resumen */
        .npage-lead {
            font-size: 1.05rem;
            line-height: 1.8;
            font-weight: 500;
            color: #1E3A6E;
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, #EFF6FF, #E8F0FE);
            border-radius: 10px;
            border-left: 4px solid #1340A0;
            margin-bottom: 2rem;
        }

        /* Contenido Quill */
        .npage-content {
            font-size: .95rem;
            line-height: 1.9;
            color: #2D3E50;
        }

        .npage-content h2 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0D2554;
            margin: 2rem 0 .75rem;
            padding-bottom: .4rem;
            border-bottom: 2px solid #EFF6FF;
        }

        .npage-content h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1340A0;
            margin: 1.5rem 0 .5rem;
        }

        .npage-content h4 {
            font-size: .95rem;
            font-weight: 700;
            color: #1A52C4;
            margin: 1.25rem 0 .4rem;
        }

        .npage-content p {
            margin-bottom: 1.1rem;
        }

        .npage-content ul,
        .npage-content ol {
            padding-left: 1.5rem;
            margin-bottom: 1.2rem;
        }

        .npage-content li {
            margin-bottom: .4rem;
        }

        .npage-content blockquote {
            margin: 1.5rem 0;
            padding: 1rem 1.5rem;
            background: #F0F7FF;
            border-left: 4px solid #1340A0;
            border-radius: 0 10px 10px 0;
            font-style: italic;
            color: #1340A0;
            font-size: .95rem;
        }

        .npage-content code {
            background: #F1F5F9;
            padding: .15em .4em;
            border-radius: 4px;
            font-size: .85em;
            color: #C7253E;
        }

        .npage-content pre {
            background: #0F172A;
            color: #E2E8F0;
            padding: 1.25rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1.25rem 0;
        }

        .npage-content pre code {
            color: inherit;
            background: none;
            padding: 0;
        }

        .npage-content a {
            color: #1340A0;
            text-decoration: underline dotted;
        }

        .npage-content a:hover {
            text-decoration: underline;
        }

        .npage-content img {
            max-width: 100%;
            border-radius: 10px;
            margin: 1rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .1);
        }

        .npage-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.25rem 0;
            font-size: .88rem;
        }

        .npage-content th {
            background: #1340A0;
            color: #fff;
            padding: .6rem .9rem;
            text-align: left;
            font-weight: 600;
        }

        .npage-content td {
            padding: .55rem .9rem;
            border-bottom: 1px solid #E8EDF5;
        }

        .npage-content tr:nth-child(even) td {
            background: #F8FAFF;
        }

        /* Acciones */
        .npage-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
            padding-top: 1.5rem;
            margin-top: 1.75rem;
            border-top: 1px solid #E8EDF5;
        }

        .npage-share-btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 8px;
            padding: .4rem .85rem;
            font-size: .75rem;
            font-weight: 600;
            color: #1340A0;
            cursor: pointer;
            transition: all .2s;
        }

        .npage-share-btn:hover {
            background: #DBEAFE;
        }

        /* ══ Sidebar ══ */
        .npage-sidebar {
            position: sticky;
            top: 82px;
        }

        .npage-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 16px rgba(13, 37, 84, .07);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .npage-card__head {
            padding: .75rem 1.2rem;
            background: linear-gradient(135deg, #0D2554, #1340A0);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .npage-card__head-title {
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255, 255, 255, .9);
        }

        .npage-card__body {
            padding: 1rem 1.2rem;
        }

        /* CTA card */
        .npage-cta {
            background: linear-gradient(135deg, #0D2554 0%, #1340A0 50%, #1A52C4 100%);
            border-radius: 12px;
            padding: 1.6rem 1.4rem;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .npage-cta::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(255, 255, 255, .04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .04) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .npage-cta__body {
            position: relative;
            z-index: 1;
        }

        .npage-cta h5 {
            font-size: .9rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: .4rem;
            line-height: 1.3;
        }

        .npage-cta p {
            font-size: .76rem;
            color: rgba(255, 255, 255, .65);
            margin-bottom: 1rem;
        }

        /* Relacionados */
        .nrel {
            display: flex;
            gap: .85rem;
            align-items: flex-start;
            padding: .75rem 0;
            border-bottom: 1px solid #F1F5F9;
            text-decoration: none;
            transition: opacity .2s;
        }

        .nrel:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .nrel:hover {
            opacity: .75;
        }

        .nrel__thumb {
            width: 70px;
            height: 52px;
            flex-shrink: 0;
            border-radius: 6px;
            overflow: hidden;
        }

        .nrel__thumb-inner {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .nrel__info {
            flex: 1;
            min-width: 0;
        }

        .nrel__meta {
            font-size: .64rem;
            color: #94A3B8;
            margin-bottom: .2rem;
        }

        .nrel__title {
            font-size: .76rem;
            font-weight: 700;
            color: #2D3E50;
            line-height: 1.35;
        }

        /* Breadcrumb */
        .npage-breadcrumb {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: .3rem;
            align-items: center;
            font-size: .7rem;
        }

        .npage-breadcrumb li {
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .npage-breadcrumb li+li::before {
            content: '/';
            color: rgba(255, 255, 255, .3);
        }

        .npage-breadcrumb a {
            color: rgba(255, 255, 255, .5);
            text-decoration: none;
        }

        .npage-breadcrumb a:hover {
            color: rgba(255, 255, 255, .8);
        }

        .npage-breadcrumb .active {
            color: rgba(255, 255, 255, .8);
        }

        /* ══ RESPONSIVE ══ */
        @media(max-width:991px) {
            .npage-sidebar {
                position: static;
                top: auto;
            }

            .npage-layout {
                padding: 1.75rem 0 3rem;
            }
        }

        @media(max-width:767px) {
            .npage-hero {
                min-height: 260px;
            }

            .npage-hero__body {
                padding-bottom: 2rem;
            }

            .npage-article__inner {
                padding: 1.1rem;
            }

            .npage-lead {
                font-size: .92rem;
                padding: 1rem 1.1rem;
            }

            .npage-meta {
                gap: .6rem;
            }

            /* Hero meta row: ocultar separador vertical y flex-wrap ya activo */
            .npage-hero__body [style*="width:1px"] {
                display: none !important;
            }

            /* Acciones bottom */
            .npage-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            /* CTA card con enlace */
            .npage-article__inner [style*="justify-content:space-between"] {
                flex-direction: column;
            }
        }

        @media(max-width:575px) {
            .npage-hero {
                min-height: 220px;
            }

            /* Nav: ocultar btn "Ingresar al Sistema" en mobile, dejar solo "Volver" */
            .npage-nav-login {
                display: none !important;
            }

            /* Article content: tablas con scroll horizontal */
            .npage-content {
                overflow-x: auto;
            }

            .npage-content table {
                min-width: 480px;
            }

            .npage-content__table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Shared: botones full width */
            .npage-share-btn {
                width: 100%;
                justify-content: center;
            }

            /* Layout padding */
            .npage-layout {
                padding: 1rem 0 2.5rem;
            }
        }

        @media(max-width:480px) {

            /* Topbar oculto */
            .ugel-topbar {
                display: none;
            }

            .npage-article__inner {
                padding: .9rem;
            }

            /* Hero breadcrumb: truncar */
            .npage-breadcrumb {
                font-size: .65rem;
            }

            .npage-breadcrumb .active {
                max-width: 160px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                display: inline-block;
            }
        }
    </style>
@endsection

@section('page-script')
    @vite(['resources/assets/js/landing-institucional.js'])
@endsection

@section('content')
    <div class="npage">

        {{-- ══ TOPBAR ══ --}}
        <div class="ugel-topbar">
            <div class="container">
                <div class="ugel-topbar__inner">
                    <div class="ugel-topbar__left">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        {{ $config?->direccion ?? 'Jr. Huacaybamba S/N, Huacaybamba — Huánuco' }}
                        <span class="ugel-topbar__sep">|</span>
                        Lun – Vie: 8:00 am – 6:00 pm
                    </div>
                    <div class="ugel-topbar__right">
                        <span class="ugel-topbar__live"><span class="ugel-topbar__dot"></span> Sistema en línea</span>
                        <a href="{{ route('login') }}" class="ugel-topbar__login">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                <polyline points="10 17 15 12 10 7" />
                                <line x1="15" y1="12" x2="3" y2="12" />
                            </svg>
                            Acceso al Sistema
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ NAVBAR ══ --}}
        <nav class="ugel-nav scrolled" style="position:sticky;top:0;z-index:900;">
            <div class="container">
                <div class="ugel-nav__inner">
                    <a href="{{ route('landing') }}" class="ugel-nav__brand">
                        <div class="ugel-nav__escudo">
                            @if ($config?->logo_ruta)
                                <img src="{{ Storage::url($config->logo_ruta) }}" alt="{{ $config->sigla ?? 'UGEL' }}"
                                    style="width:42px;height:42px;object-fit:contain;">
                            @else
                                <svg width="32" height="32" viewBox="0 0 48 48" fill="none">
                                    <path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 C14 44 4 38 4 28 L4 12 Z" fill="#c62828"
                                        opacity=".9" />
                                    <path d="M24 4 L24 47 C14 44 4 38 4 28 L4 12 Z" fill="#1a237e" opacity=".85" />
                                    <path d="M18 20 L22 26 L30 18" stroke="white" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round" fill="none" />
                                </svg>
                            @endif
                        </div>
                        <div class="ugel-nav__brand-text">
                            <span class="ugel-nav__brand-title">{{ $config?->sigla ?? 'UGEL Huacaybamba' }}</span>
                            <span
                                class="ugel-nav__brand-sub">{{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local' }}{{ $config?->region ? ' · ' . $config->region : ' · Huánuco' }}</span>
                        </div>
                    </a>
                    <div style="flex:1"></div>
                    <div class="ugel-nav__end">
                        <a href="{{ route('landing') }}" class="ugel-btn ugel-btn--outline"
                            style="font-size:.78rem;padding:.45rem .9rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                            Volver al inicio
                        </a>
                        <a href="{{ route('login') }}" class="ugel-btn ugel-btn--primary npage-nav-login">
                            Ingresar al Sistema
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        {{-- ══ HERO ══ --}}
        <div class="npage-hero">
            <div class="npage-hero__img"
                style="@if ($noticia->imagen_url) background-image:url('{{ $noticia->imagen_url }}');
              @else
                background:{{ $noticia->color_gradiente ?? 'linear-gradient(135deg,#0D2554,#1340A0,#1A52C4)' }}; @endif">
            </div>
            <div class="npage-hero__overlay"></div>
            <div class="npage-hero__body">
                <div class="container">
                    <ol class="npage-breadcrumb">
                        <li><a href="{{ route('landing') }}">Inicio</a></li>
                        <li><span>Publicaciones</span></li>
                        <li><span class="active">{{ Str::limit($noticia->titulo, 40) }}</span></li>
                    </ol>

                    <span class="npage-badge"
                        style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);">
                        {{ $noticia->etiqueta ?? ucfirst($noticia->tipo) }}
                    </span>
                    <h1
                        style="font-size:clamp(1.6rem,3.2vw,2.4rem);font-weight:900;color:#fff;line-height:1.18;max-width:780px;letter-spacing:-.03em;margin:.25rem 0 1.25rem;text-shadow:0 2px 12px rgba(0,0,0,.3);">
                        {{ $noticia->titulo }}
                    </h1>

                    {{-- Fila de meta ──────────────────────────────────────── --}}
                    <div style="display:flex;flex-wrap:wrap;gap:1.5rem;align-items:center;">
                        {{-- Autor avatar --}}
                        <div style="display:flex;align-items:center;gap:.6rem;">
                            <div
                                style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1340A0,#28c76f);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff"
                                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <div>
                                <div style="font-size:.7rem;color:rgba(255,255,255,.45);line-height:1.2;">Publicado por
                                </div>
                                <div style="font-size:.8rem;color:#fff;font-weight:600;">
                                    {{ $noticia->autor ?? 'Oficina SCI — UGEL Huacaybamba' }}</div>
                            </div>
                        </div>

                        <div style="width:1px;height:28px;background:rgba(255,255,255,.2);"></div>

                        <div
                            style="display:flex;align-items:center;gap:.4rem;font-size:.78rem;color:rgba(255,255,255,.65);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            {{ $noticia->created_at->translatedFormat('d \d\e F \d\e Y') }}
                        </div>

                        <div
                            style="display:flex;align-items:center;gap:.4rem;font-size:.78rem;color:rgba(255,255,255,.65);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            {{ $minutos }} min de lectura
                        </div>

                        <span
                            style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .65rem;border-radius:12px;font-size:.7rem;font-weight:700;color:#fff;border:1px solid rgba(255,255,255,.25);background:rgba(255,255,255,.1);">
                            {{ ucfirst($noticia->tipo) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ LAYOUT ══ --}}
        <div class="npage-layout">
            <div class="container">
                <div class="row g-4">

                    {{-- ── Artículo ── --}}
                    <div class="col-lg-8">
                        <article class="npage-article">
                            <div class="npage-article__inner">

                                {{-- Meta bar --}}
                                <div class="npage-meta">
                                    <span class="npage-meta__item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                            stroke="#1340A0" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" />
                                            <line x1="16" y1="2" x2="16" y2="6" />
                                            <line x1="8" y1="2" x2="8" y2="6" />
                                            <line x1="3" y1="10" x2="21" y2="10" />
                                        </svg>
                                        {{ $noticia->created_at->format('d M Y') }}
                                    </span>
                                    <span class="npage-meta__item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                            stroke="#1340A0" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        {{ $minutos }} min lectura
                                    </span>
                                    <span class="npage-meta__item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                            stroke="#1340A0" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        {{ $noticia->autor ?? 'Oficina SCI' }}
                                    </span>
                                    <span class="npage-meta__item ms-auto">
                                        <strong>{{ ucfirst($noticia->tipo) }}</strong>
                                    </span>
                                </div>

                                {{-- Lead --}}
                                @if ($noticia->descripcion)
                                    <div class="npage-lead">{{ $noticia->descripcion }}</div>
                                @endif

                                {{-- Contenido completo --}}
                                @if ($noticia->contenido)
                                    <div class="npage-content">
                                        {!! $noticia->contenido !!}
                                    </div>
                                @endif

                                {{-- CTA si tiene URL --}}
                                @if ($noticia->url_accion && $noticia->texto_accion)
                                    <div
                                        style="margin-top:2rem;padding:1.5rem;background:#F0F7FF;border-radius:10px;border:1px solid #BFDBFE;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                                        <div>
                                            <div style="font-size:.78rem;font-weight:700;color:#1340A0;">Enlace relacionado
                                            </div>
                                            <div style="font-size:.75rem;color:#5B6F8A;margin-top:.2rem;">
                                                {{ $noticia->url_accion }}</div>
                                        </div>
                                        <a href="{{ $noticia->url_accion }}" class="ugel-btn ugel-btn--primary"
                                            target="_blank" rel="noopener">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.4" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                                <polyline points="15 3 21 3 21 9" />
                                                <line x1="10" y1="14" x2="21" y2="3" />
                                            </svg>
                                            {{ $noticia->texto_accion }}
                                        </a>
                                    </div>
                                @endif

                                {{-- Acciones / compartir --}}
                                <div class="npage-actions">
                                    <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                                        <span style="font-size:.74rem;color:#94A3B8;font-weight:600;">Compartir:</span>
                                        <button class="npage-share-btn"
                                            onclick="
                  if(navigator.share){navigator.share({title:document.title,url:location.href})}
                  else{navigator.clipboard.writeText(location.href).then(function(){
                    var b=this||event.currentTarget; b.textContent='¡Copiado!';
                    setTimeout(function(){b.innerHTML='<svg width=12 height=12 viewBox=\'0 0 24 24\' fill=none stroke=currentColor stroke-width=2.2 stroke-linecap=round stroke-linejoin=round><circle cx=18 cy=5 r=3/><circle cx=6 cy=12 r=3/><circle cx=18 cy=19 r=3/><line x1=8.59 y1=13.51 x2=15.42 y2=17.49/><line x1=15.41 y1=6.51 x2=8.59 y2=10.49/></svg> Copiar enlace';},2000)
                  }).bind(event.currentTarget)}"
                                            type="button">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="18" cy="5" r="3" />
                                                <circle cx="6" cy="12" r="3" />
                                                <circle cx="18" cy="19" r="3" />
                                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
                                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
                                            </svg>
                                            Copiar enlace
                                        </button>
                                    </div>

                                </div>

                            </div>
                        </article>

                        {{-- Más publicaciones (versión mobile) --}}
                        @if ($relacionadas->count())
                            <div class="d-lg-none mt-4">
                                <h5
                                    style="font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#607080;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
                                    <span
                                        style="display:inline-block;width:3px;height:14px;background:#1340A0;border-radius:2px;"></span>
                                    Más publicaciones
                                </h5>
                                <div class="row g-3">
                                    @foreach ($relacionadas as $rel)
                                        <div class="col-sm-4">
                                            <a href="{{ route('landing.noticia', $rel->id) }}"
                                                style="text-decoration:none;display:block;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 1px 12px rgba(0,0,0,.07);transition:transform .25s,box-shadow .25s;"
                                                onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(19,64,160,.12)'"
                                                onmouseout="this.style.transform='';this.style.boxShadow='0 1px 12px rgba(0,0,0,.07)'">
                                                <div
                                                    style="height:100px;@if ($rel->imagen_url) background-image:url('{{ $rel->imagen_url }}');background-size:cover;background-position:center;@else background:{{ $rel->color_gradiente ?? 'linear-gradient(135deg,#1340A0,#1A52C4)' }}; @endif">
                                                </div>
                                                <div style="padding:.75rem;">
                                                    <div style="font-size:.63rem;color:#94A3B8;margin-bottom:.25rem;">
                                                        {{ $rel->created_at->format('d M Y') }}</div>
                                                    <div
                                                        style="font-size:.76rem;font-weight:700;color:#2D3E50;line-height:1.3;">
                                                        {{ Str::limit($rel->titulo, 60) }}</div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- ── Sidebar ── --}}
                    <div class="col-lg-4">
                        <aside class="npage-sidebar">

                            {{-- Más publicaciones --}}
                            @if ($relacionadas->count())
                                <div class="npage-card d-none d-lg-block">
                                    <div class="npage-card__head">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                            stroke="rgba(255,255,255,.8)" stroke-width="2.2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                        </svg>
                                        <span class="npage-card__head-title">Más publicaciones</span>
                                    </div>
                                    <div class="npage-card__body">
                                        @foreach ($relacionadas as $rel)
                                            <a href="{{ route('landing.noticia', $rel->id) }}" class="nrel">
                                                <div class="nrel__thumb">
                                                    @if ($rel->imagen_url)
                                                        <img src="{{ $rel->imagen_url }}" alt=""
                                                            class="nrel__thumb-inner">
                                                    @else
                                                        <div class="nrel__thumb-inner"
                                                            style="background:{{ $rel->color_gradiente ?? 'linear-gradient(135deg,#1340A0,#1A52C4)' }};display:flex;align-items:center;justify-content:center;">
                                                            <svg width="18" height="18" viewBox="0 0 24 24"
                                                                fill="none" stroke="rgba(255,255,255,.5)"
                                                                stroke-width="1.5">
                                                                <path
                                                                    d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                                <polyline points="14 2 14 8 20 8" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="nrel__info">
                                                    <div class="nrel__meta">{{ $rel->created_at->format('d M Y') }} ·
                                                        {{ ucfirst($rel->tipo) }}</div>
                                                    <div class="nrel__title">{{ Str::limit($rel->titulo, 70) }}</div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Ver todas las publicaciones --}}
                            <div class="npage-card"
                                style="background:linear-gradient(135deg,#1340A0 0%,#1d52c4 100%);border:none;">
                                <div style="padding:1.25rem 1.3rem;text-align:center;">
                                    <div
                                        style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="rgba(255,255,255,.9)" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                    </div>
                                    <p
                                        style="color:rgba(255,255,255,.85);font-size:.82rem;line-height:1.5;margin:0 0 1rem;">
                                        Explora todas las noticias, eventos y normativas publicadas por la UGEL Huacaybamba.
                                    </p>
                                    <a href="{{ route('landing.publicaciones') }}"
                                        style="display:inline-flex;align-items:center;gap:.45rem;background:#fff;color:#1340A0;font-size:.82rem;font-weight:700;padding:.55rem 1.2rem;border-radius:8px;text-decoration:none;transition:opacity .2s;">
                                        Ver todas las publicaciones
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                            <polyline points="12 5 19 12 12 19" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            {{-- Datos institucionales --}}
                            <div class="npage-card">
                                <div class="npage-card__head">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="rgba(255,255,255,.8)" stroke-width="2.2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                        <polyline points="9 22 9 12 15 12 15 22" />
                                    </svg>
                                    <span class="npage-card__head-title">UGEL Huacaybamba</span>
                                </div>
                                <div class="npage-card__body">
                                    <div style="font-size:.78rem;color:#5B6F8A;line-height:1.8;">
                                        <div style="display:flex;gap:.5rem;margin-bottom:.5rem;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="#1340A0" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" style="flex-shrink:0;margin-top:.15rem;">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                            Jr. Huacaybamba S/N, Huacaybamba — Huánuco
                                        </div>
                                        <div style="display:flex;gap:.5rem;margin-bottom:.5rem;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="#1340A0" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" style="flex-shrink:0;margin-top:.15rem;">
                                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                                <line x1="16" y1="2" x2="16" y2="6" />
                                                <line x1="8" y1="2" x2="8" y2="6" />
                                                <line x1="3" y1="10" x2="21" y2="10" />
                                            </svg>
                                            Lun – Vie: 8:00 am – 6:00 pm
                                        </div>
                                        <div style="display:flex;gap:.5rem;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="#1340A0" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" style="flex-shrink:0;margin-top:.15rem;">
                                                <path
                                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                            </svg>
                                            Sistema PULSO
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </aside>
                    </div>

                </div>
            </div>
        </div>

        {{-- ══ FOOTER ══ --}}
        <footer class="ugel-footer-new">
            <div class="ugel-footer-new__top">
                <div class="container">
                    <div class="ugel-footer-new__grid">

                        {{-- Col 1: Brand --}}
                        <div class="ugel-fn-brand">
                            <div class="ugel-fn-brand__logo">
                                @if ($config?->logo_ruta)
                                    <img src="{{ Storage::url($config->logo_ruta) }}"
                                        alt="{{ $config?->sigla ?? 'UGEL' }}">
                                @else
                                    <svg width="36" height="36" viewBox="0 0 48 48" fill="none">
                                        <path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 C14 44 4 38 4 28 L4 12 Z"
                                            fill="#c62828" opacity=".9" />
                                        <path d="M24 4 L24 47 C14 44 4 38 4 28 L4 12 Z" fill="#1a237e" opacity=".85" />
                                        <path d="M18 20 L22 26 L30 18" stroke="white" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                    </svg>
                                @endif
                                <div>
                                    <strong>{{ $config?->sigla ?? 'UGEL Huacaybamba' }}</strong>
                                    <span>{{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local' }}</span>
                                </div>
                            </div>
                            <p class="ugel-fn-brand__desc">
                                {{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local de Huacaybamba' }},
                                Región {{ $config?->region ?? 'Huánuco' }}, Perú.
                                Comprometidos con la calidad educativa y el control institucional.
                            </p>
                            <div class="ugel-fn-brand__data">
                                @if ($config?->correo_institucional)
                                    <a href="mailto:{{ $config->correo_institucional }}" class="ugel-fn-data-item">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path
                                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                            <polyline points="22,6 12,13 2,6" />
                                        </svg>
                                        {{ $config->correo_institucional }}
                                    </a>
                                @endif
                                @if ($config?->telefono)
                                    <a href="tel:{{ $config->telefono }}" class="ugel-fn-data-item">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path
                                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.38 2 2 0 0 1 3.59 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                        </svg>
                                        {{ $config->telefono }}
                                    </a>
                                @endif
                                @if ($config?->sitio_web)
                                    <a href="{{ $config->sitio_web }}" target="_blank" rel="noopener noreferrer"
                                        class="ugel-fn-data-item">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <line x1="2" y1="12" x2="22" y2="12" />
                                            <path
                                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                        </svg>
                                        {{ parse_url($config->sitio_web, PHP_URL_HOST) ?? 'Sitio web' }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Col 2: Navegación --}}
                        <div class="ugel-fn-col">
                            <h6 class="ugel-fn-col__title"><span class="ugel-fn-col__title-ico"><svg width="13"
                                        height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="3" y1="12" x2="21" y2="12" />
                                        <line x1="3" y1="6" x2="21" y2="6" />
                                        <line x1="3" y1="18" x2="21" y2="18" />
                                    </svg></span>Navegación</h6>
                            <ul>
                                <li><a href="{{ route('landing') }}"><span class="ugel-fn-link-ico"><svg width="12"
                                                height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                                <polyline points="9 22 9 12 15 12 15 22" />
                                            </svg></span>Inicio</a></li>
                                <li><a href="{{ route('landing') }}#sistema"><span class="ugel-fn-link-ico"><svg
                                                width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                            </svg></span>Sistema PULSO</a></li>
                                <li><a href="{{ route('landing') }}#modulos"><span class="ugel-fn-link-ico"><svg
                                                width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <rect x="3" y="3" width="7" height="7" />
                                                <rect x="14" y="3" width="7" height="7" />
                                                <rect x="14" y="14" width="7" height="7" />
                                                <rect x="3" y="14" width="7" height="7" />
                                            </svg></span>Módulos</a></li>
                                <li><a href="{{ route('landing') }}#normativa"><span class="ugel-fn-link-ico"><svg
                                                width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M12 20h9" />
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                            </svg></span>Normativa</a></li>
                                <li><a href="{{ route('landing') }}#contacto"><span class="ugel-fn-link-ico"><svg
                                                width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg></span>Contacto</a></li>
                                <li><a href="{{ route('login') }}" class="ugel-fn-link--accent"><span
                                            class="ugel-fn-link-ico"><svg width="12" height="12"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                            </svg></span>Acceso al Sistema</a></li>
                            </ul>
                        </div>

                        {{-- Col 3: Instituciones --}}
                        <div class="ugel-fn-col">
                            <h6 class="ugel-fn-col__title"><span class="ugel-fn-col__title-ico"><svg width="13"
                                        height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg></span>Instituciones</h6>
                            <ul>
                                @forelse($instituciones->take(6) as $inst)
                                    <li>
                                        <a href="{{ $inst->url_sitio ?? '#' }}"
                                            @if ($inst->url_sitio) target="_blank" rel="noopener noreferrer" @endif>
                                            <span class="ugel-fn-link-ico"><svg width="12" height="12"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="2" y1="12" x2="22" y2="12" />
                                                    <path
                                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                                </svg></span>
                                            <span class="ugel-fn-col__abbr">{{ $inst->sigla }}</span>{{ $inst->nombre }}
                                        </a>
                                    </li>
                                @empty
                                    <li><a href="#"><span class="ugel-fn-link-ico"><svg width="12"
                                                    height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="2" y1="12" x2="22" y2="12" />
                                                    <path
                                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                                </svg></span>Contraloría General de la Rep.</a></li>
                                    <li><a href="#"><span class="ugel-fn-link-ico"><svg width="12"
                                                    height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="2" y1="12" x2="22" y2="12" />
                                                    <path
                                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                                </svg></span>Ministerio de Educación</a></li>
                                    <li><a href="#"><span class="ugel-fn-link-ico"><svg width="12"
                                                    height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="2" y1="12" x2="22" y2="12" />
                                                    <path
                                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                                </svg></span>Gobierno Regional Huánuco</a></li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Col 4: Datos institucionales --}}
                        <div class="ugel-fn-col">
                            <h6 class="ugel-fn-col__title"><span class="ugel-fn-col__title-ico"><svg width="13"
                                        height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                        <polyline points="9 22 9 12 15 12 15 22" />
                                    </svg></span>Institución</h6>
                            <div class="ugel-fn-inst-data">
                                @if ($config?->ugel_codigo)
                                    <div class="ugel-fn-inst-row"><span>Cód.
                                            UGEL</span><strong>{{ $config->ugel_codigo }}</strong></div>
                                @endif
                                @if ($config?->ubigeo)
                                    <div class="ugel-fn-inst-row">
                                        <span>Ubigeo</span><strong>{{ $config->ubigeo }}</strong>
                                    </div>
                                @endif
                                <div class="ugel-fn-inst-row">
                                    <span>Región</span><strong>{{ $config?->region ?? 'Huánuco' }}</strong>
                                </div>
                                <div class="ugel-fn-inst-row"><span>Sector</span><strong>Educación</strong></div>
                                @if ($config?->director)
                                    <div class="ugel-fn-inst-row">
                                        <span>Director</span><strong>{{ $config->director }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="ugel-footer-new__bottom">
                <div class="container">
                    <div class="ugel-footer-new__bottom-inner">
                        <span>© {{ date('Y') }} {{ $config?->sigla ?? 'UGEL Huacaybamba' }}
                            {{ $config?->region ?? 'Huánuco' }}, Perú. Todos los derechos reservados.</span>
                        <div class="ugel-footer-new__bottom-right">
                            <span class="ugel-fn-status">
                                <span class="ugel-fn-status__dot"></span>
                                Sistema PULSO · Control Interno
                            </span>
                            @if ($config?->sitio_web)
                                <a href="{{ $config->sitio_web }}" target="_blank" rel="noopener noreferrer">Portal
                                    Institucional</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <script>
            // Wrap tables inside npage-content for horizontal scroll on mobile
            document.querySelectorAll('.npage-content table').forEach(function(t) {
                if (t.parentElement.classList.contains('npage-content__table-wrap')) return;
                var w = document.createElement('div');
                w.className = 'npage-content__table-wrap';
                w.style.cssText = 'overflow-x:auto;-webkit-overflow-scrolling:touch;margin:1.25rem 0;';
                t.parentNode.insertBefore(w, t);
                w.appendChild(t);
                t.style.margin = '0';
            });
        </script>
    </div>{{-- .npage --}}
@endsection
