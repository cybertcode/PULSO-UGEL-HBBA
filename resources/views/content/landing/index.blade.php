@php
    $configData = Helper::appClasses();
    $isFront = true;
    $customizerHidden = 'customizer-hide'; // Ocultar customizer en el landing
@endphp
@extends('layouts/layoutLanding')
@section('title', 'PULSO UGEL — Sistema de Control Interno | UGEL Huacaybamba')
@section('meta-robots', 'index, follow')
@section('meta-description', 'PULSO UGEL Huacaybamba — Sistema digital de Control Interno (SCI) de la UGEL Huacaybamba. Gestión, seguimiento y evaluación institucional alineada con la Contraloría General de la República del Perú.')
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/swiper/swiper.scss'])
@endsection
@section('page-style')
    @vite(['resources/assets/css/landing-institucional.css'])
    <style>
        /* ─── ESTRUCTURA GLOBAL (90% VENTANA) ─── */
        body {
            background-color: var(--bg);
        }

        /* Elementos que se centran al 90% pero SIN fondo de color */
        .ugel-topbar,
        .ugel-nav,
        .ugel-hero,
        .ugel-section,
        .ugel-section--alt,
        .ugel-cta {
            width: 90% !important;
            max-width: 1800px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Sección contacto — full-width con fondo propio */
        .ugel-contact-section {
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* Contenedor interno contacto — centrado al 90% */
        .ugel-contact-section .container {
            width: 90% !important;
            max-width: 1700px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Módulos y footer — card centrada al 90% */
        .ugel-mods-section,
        .ugel-footer-new {
            width: 90% !important;
            max-width: 1700px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Ajustes específicos para elementos pegajosos y bordes */
        .ugel-nav {
            border-radius: 0 0 12px 12px;
        }

        .ugel-hero__grid,
        .ugel-hero__stats {
            width: 100% !important;
            /* Ocupan todo el ancho de su padre (que ya es 90%) */
            max-width: none !important;
        }

        /* ─── CONTENEDORES INTERNOS (95% DEL PADRE) ─── */
        .container,
        .ugel-topbar__inner,
        .ugel-nav__inner,
        .ugel-footer__grid,
        .ugel-footer__bottom-inner {
            width: 95% !important;
            max-width: 1700px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Corrección para secciones con fondo completo */
        .ugel-section--alt {
            background-color: var(--bg);
        }

        .ugel-cta {
            border-radius: 16px;
        }

        @media (max-width: 991px) {
            .ugel-topbar,
            .ugel-nav,
            .ugel-hero,
            .ugel-section,
            .ugel-section--alt,
            .ugel-cta {
                width: 96% !important;
            }
            .container {
                width: 94% !important;
            }
        }

        @media (max-width: 767px) {
            .ugel-topbar,
            .ugel-nav,
            .ugel-hero,
            .ugel-section,
            .ugel-section--alt,
            .ugel-cta,
            .ugel-mods-section,
            .ugel-footer-new {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .ugel-footer-new,
            .ugel-mods-section {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .ugel-hero__grid {
                width: 100% !important;
                border-radius: 0 !important;
                margin-top: 0 !important;
            }
            .ugel-hero__stats {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .ugel-contact-section .container {
                width: 100% !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
        }

        @media (max-width: 480px) {
            .ugel-topbar { display: none; }
        }

        /* ─── PUBLICACIONES RECIENTES ─── */
        .ugel-pub-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        @media (max-width: 991px) { .ugel-pub-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 575px) { .ugel-pub-grid { grid-template-columns: 1fr; } }

        .ugel-pub-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,.07);
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            display: flex;
            flex-direction: column;
            transition: transform .22s ease, box-shadow .22s ease;
        }
        .ugel-pub-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(19,64,160,.13);
        }
        .ugel-pub-card__img-wrap {
            position: relative;
            display: block;
            overflow: hidden;
        }
        .ugel-pub-card__img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            display: block;
            transition: transform .4s ease;
        }
        .ugel-pub-card__img--gradient { height: 190px; }
        .ugel-pub-card__img-wrap:hover .ugel-pub-card__img { transform: scale(1.04); }
        .ugel-pub-card__badge {
            position: absolute;
            top: .75rem;
            left: .75rem;
            padding: .2rem .65rem;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
        }
        .ugel-pub-card__body {
            padding: 1.1rem 1.25rem 1.25rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .ugel-pub-card__eye {
            font-size: .7rem;
            font-weight: 700;
            color: #1340A0;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin: 0 0 .35rem;
        }
        .ugel-pub-card__title {
            font-size: .97rem;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1.4;
            margin: 0 0 .5rem;
            flex: 1;
        }
        .ugel-pub-card__title a {
            color: inherit;
            text-decoration: none;
        }
        .ugel-pub-card__title a:hover { color: #1340A0; }
        .ugel-pub-card__desc {
            font-size: .82rem;
            color: #64748b;
            line-height: 1.5;
            margin: 0 0 .9rem;
        }
        .ugel-pub-card__foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            padding-top: .75rem;
            border-top: 1px solid #f1f5f9;
        }
        .ugel-pub-card__author {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .75rem;
            color: #64748b;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .ugel-pub-card__av {
            width: 22px; height: 22px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .62rem; font-weight: 800; color: #fff; flex-shrink: 0;
        }
        .ugel-pub-card__read {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .76rem;
            font-weight: 700;
            color: #1340A0;
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .ugel-pub-card__read:hover { color: #0c2d80; }
    </style>
@endsection
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/swiper/swiper.js'])
@endsection
@section('page-script')
    @vite(['resources/assets/js/landing-institucional.js'])
@endsection

@section('content')

    {{-- ════ BARRA SUPERIOR INSTITUCIONAL ════ --}}
    <div class="ugel-topbar">
        <div class="container">
            <div class="ugel-topbar__inner">
                <div class="ugel-topbar__left">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    {{ $config?->direccion ?? 'Jr. Huacaybamba S/N, Huacaybamba — Huánuco' }}
                    <span class="ugel-topbar__sep">|</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    Lun – Vie: 8:00 am – 6:00 pm
                    <span class="ugel-topbar__sep">|</span>
                    <span class="">
                        <marquee>ÍNDICE DE CAPACIDAD PREVENTIVA FRENTE A LA CORRUPCIÓN</marquee>
                    </span>

                </div>
                <div class="ugel-topbar__right">
                    <span class="ugel-topbar__live">
                        <span class="ugel-topbar__dot"></span> En línea
                    </span>
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

    {{-- ════ NAVBAR INSTITUCIONAL ════ --}}
    <nav class="ugel-nav" id="ugelNav">
        <div class="container">
            <div class="ugel-nav__inner">

                {{-- Logo --}}
                <a href="{{ route('landing') }}" class="ugel-nav__brand">
                    <div class="ugel-nav__escudo">
                        @if ($config?->logo_ruta)
                            <img src="{{ Storage::url($config->logo_ruta) }}" alt="{{ $config->sigla ?? 'UGEL' }}"
                                style="width:42px;height:42px;object-fit:contain;">
                        @else
                            <svg width="32" height="32" viewBox="0 0 48 48" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 C14 44 4 38 4 28 L4 12 Z" fill="#d32f2f"
                                    opacity=".9" />
                                <path d="M24 4 L24 47 C14 44 4 38 4 28 L4 12 Z" fill="#1a237e" opacity=".85" />
                                <path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 L24 4 Z" fill="#c62828" opacity=".7" />
                                <path d="M18 20 L22 26 L30 18" stroke="white" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round" fill="none" />
                                <circle cx="24" cy="24" r="5" stroke="white" stroke-width="1.5" fill="none"
                                    opacity=".5" />
                            </svg>
                        @endif
                    </div>
                    <div class="ugel-nav__brand-text">
                        <span class="ugel-nav__brand-title">{{ $config?->sigla ?? 'Ugel Huacaybamba' }}</span>
                        <span
                            class="ugel-nav__brand-sub">{{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local' }}{{ $config?->region ? ' · ' . $config->region : ' · Huánuco' }}</span>
                    </div>
                </a>

                {{-- Nav links --}}
                <ul class="ugel-nav__links" id="ugelNavLinks">
                    {{-- Botón cerrar (solo mobile) --}}
                    <li class="ugel-nav__close-item">
                        <button class="ugel-nav__close-btn" id="ugelNavClose" aria-label="Cerrar menú">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </li>
                    <li>
                        <a href="#inicio" class="ugel-nav__link active">
                            <span class="ugel-nav__link-ico">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </span>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="#sistema" class="ugel-nav__link">
                            <span class="ugel-nav__link-ico">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </span>
                            Sistema PULSO
                        </a>
                    </li>
                    <li>
                        <a href="#modulos" class="ugel-nav__link">
                            <span class="ugel-nav__link-ico">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                                </svg>
                            </span>
                            Módulos
                        </a>
                    </li>
                    <li>
                        <a href="#publicaciones" class="ugel-nav__link">
                            <span class="ugel-nav__link-ico">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                            </span>
                            Publicaciones
                        </a>
                    </li>
                    <li>
                        <a href="#normativa" class="ugel-nav__link">
                            <span class="ugel-nav__link-ico">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                </svg>
                            </span>
                            Normativa
                        </a>
                    </li>
                    <li>
                        <a href="#contacto" class="ugel-nav__link">
                            <span class="ugel-nav__link-ico">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                                </svg>
                            </span>
                            Contacto
                        </a>
                    </li>
                </ul>

                {{-- Centro mobile: estado + año --}}
                <div class="ugel-nav__mobile-center" style="display:none">
                    <span class="ugel-nav__mobile-badge">
                        <span class="ugel-nav__mobile-dot"></span>
                        En línea · {{ $config?->anio_gestion ?? date('Y') }}
                    </span>
                </div>

                {{-- End --}}
                <div class="ugel-nav__end">
                    <button class="ugel-burger" id="ugelBurger">
                        <span></span><span></span><span></span>
                    </button>
                </div>

            </div>
        </div>
    </nav>
    <div class="ugel-overlay" id="ugelOverlay"></div>


    {{-- ════ HERO — Layout split full-bleed ════ --}}
    <section class="ugel-hero" id="inicio">
        <div class="ugel-hero__grid mb-4">

            {{-- Columna izquierda: texto institucional fijo --}}
            <div class="ugel-hero__left">
                <div class="ugel-pill">
                    <span class="ugel-pill__dot"></span>
                    {{ $config?->sigla ?? 'UGEL Huacaybamba' }} · Sistema de Control Interno
                </div>

                <h1 class="ugel-hero__title">
                    Sistema <em>PULSO</em><br>Control Interno
                </h1>

                <p class="ugel-hero__desc">
                    Plataforma digital para la gestión, seguimiento y evaluación del Sistema de Control Interno
                    de la {{ $config?->nombre_institucion ?? 'UGEL HUACAYBAMBA' }},
                    alineada con los lineamientos de la Contraloría General de la República del Perú.
                </p>

                <div class="ugel-hero__actions">
                    <a href="{{ route('login') }}" class="ugel-btn ugel-btn--primary ugel-btn--lg">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        Entrar al Sistema
                    </a>
                    <a href="#modulos" class="ugel-btn ugel-btn--outline ugel-btn--lg">
                        Explorar
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </a>
                </div>

                {{-- Instituciones avales --}}
                <div class="ugel-hero__avales">
                    <span class="ugel-hero__avales-label">Bajo los lineamientos de:</span>
                    <div class="ugel-hero__avales-items">
                        <span class="ugel-aval">CGR</span>
                        <span class="ugel-aval-sep">·</span>
                        <span class="ugel-aval">PCM</span>
                        <span class="ugel-aval-sep">·</span>
                        <span class="ugel-aval">GORE Huánuco</span>
                        <span class="ugel-aval-sep">·</span>
                        <span class="ugel-aval ugel-aval--active">UGEL Huacaybamba</span>
                    </div>
                </div>

            </div>

            {{-- Columna derecha: carrusel de noticias/slides --}}
            <div class="ugel-hero__right">

                <div class="swiper ugel-carousel" id="heroSwiper">
                    <div class="swiper-wrapper">

                        @forelse($slides as $slide)
                            <div class="swiper-slide">
                                <div class="ugel-slide"
                                    style="@if ($slide->imagen_url) background-image:url('{{ $slide->imagen_url }}'); @else background: {{ $slide->color_gradiente ?? 'linear-gradient(135deg,#1a237e,#283593,#7367f0)' }}; @endif">
                                    <div class="ugel-slide__overlay"></div>
                                    <div class="ugel-slide__content">
                                        <span
                                            class="ugel-slide__tipo ugel-tipo--{{ $slide->tipo }}">{{ ucfirst($slide->tipo) }}</span>
                                        <h3 class="ugel-slide__title">{{ $slide->titulo }}</h3>
                                        @if ($slide->descripcion)
                                            <p class="ugel-slide__desc">{{ Str::limit($slide->descripcion, 100) }}</p>
                                        @endif
                                        <a href="{{ route('landing.noticia', $slide->id) }}" class="ugel-slide__link">
                                            Leer más
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                                <polyline points="12 5 19 12 12 19" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <div class="ugel-slide" style="background:linear-gradient(135deg,#1a237e,#7367f0)">
                                    <div class="ugel-slide__overlay"></div>
                                    <div class="ugel-slide__content">
                                        <span class="ugel-slide__tipo ugel-tipo--noticia">Sistema</span>
                                        <h3 class="ugel-slide__title">PULSO en línea</h3>
                                        <p class="ugel-slide__desc">Sistema activo y disponible para todos los usuarios
                                            institucionales.</p>
                                    </div>
                                </div>
                            </div>
                        @endforelse

                    </div>
                    <div class="swiper-pagination ugel-carousel-dots"></div>
                    <div class="ugel-carousel-nav">
                        <button class="ugel-carousel-btn" id="heroPrev">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </button>
                        <button class="ugel-carousel-btn" id="heroNext">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>{{-- .ugel-hero__grid --}}

        {{-- Estadísticas debajo del hero --}}
        <div class="ugel-hero__stats">
            <div class="container">
                <div class="ugel-stats-row">
                    <div class="ugel-stat">
                        <span class="ugel-stat__n" data-target="{{ $stats['componentes'] }}">0</span>
                        <span class="ugel-stat__l">Componentes SCI</span>
                    </div>
                    <div class="ugel-stat">
                        <span class="ugel-stat__n" data-target="{{ $stats['unidades'] }}" data-suffix="+">0</span>
                        <span class="ugel-stat__l">Unidades Orgánicas</span>
                    </div>
                    <div class="ugel-stat">
                        <span class="ugel-stat__n" data-target="{{ $stats['avance'] }}" data-suffix="%">0</span>
                        <span class="ugel-stat__l">Avance SCI {{ $stats['paci'] }}</span>
                    </div>
                    <div class="ugel-stat">
                        <span class="ugel-stat__n">{{ $stats['paci'] }}</span>
                        <span class="ugel-stat__l">Plan Anual Activo</span>
                    </div>
                    <div class="ugel-stat">
                        <span class="ugel-stat__n">{{ $stats['gestion'] }}</span>
                        <span class="ugel-stat__l">Años de Gestión</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ════ ACERCA DEL SISTEMA ════ --}}
    <section class="ugel-section" id="sistema">
        <div class="container">
            <div class="ugel-about-grid">

                <div class="ugel-about__text">
                    <span class="ugel-label">¿Qué es PULSO?</span>
                    <h2 class="ugel-section__title">Sistema de Control Interno · <span
                            class="ugel-text-accent">{{ $config?->sigla ?? 'UGEL Huacaybamba' }}</span></h2>
                    <p class="ugel-section__sub">PULSO es la plataforma digital oficial de la
                        {{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local de Huacaybamba' }} para la
                        implementación y seguimiento del Sistema de Control Interno (SCI), en cumplimiento de la Ley N°
                        28716 y los lineamientos de la Contraloría General de la República.</p>
                    <p class="ugel-section__sub">Permite a las unidades orgánicas registrar evidencias, dar seguimiento a
                        actividades del PACI y generar reportes de cumplimiento en tiempo real.</p>
                    <div class="ugel-about__checks">
                        @foreach (['Alineado con la Contraloría General de la República', 'Acceso por unidad orgánica y rol', 'Reportes automáticos de cumplimiento', 'Registro de evidencias y actividades SCI', 'Plan Anual de Control Interno (PACI) digital'] as $item)
                            <div class="ugel-check">
                                <div class="ugel-check__ico">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <span>{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>
                    <a href="#normativa" class="ugel-btn ugel-btn--outline ugel-btn--lg mt-4 d-inline-flex">
                        Ver normativa legal
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </a>
                </div>

                <div class="ugel-about__visual">
                    <div class="ugel-about__card ugel-about__card--main">
                        <div class="ugel-about__card-header">
                            <div class="ugel-about__card-dots">
                                <span></span><span></span><span></span>
                            </div>
                            <span class="ugel-about__card-title">Panel PULSO UGEL</span>
                        </div>
                        <div class="ugel-about__card-body">
                            <div class="ugel-mock-row">
                                <div class="ugel-mock-kpi" style="--c:#1a237e">
                                    <span class="ugel-mock-kpi__n">{{ $stats['avance'] }}%</span>
                                    <span class="ugel-mock-kpi__l">Avance SCI</span>
                                </div>
                                <div class="ugel-mock-kpi" style="--c:#28c76f">
                                    <span class="ugel-mock-kpi__n">{{ $stats['componentes'] }}</span>
                                    <span class="ugel-mock-kpi__l">Componentes</span>
                                </div>
                                <div class="ugel-mock-kpi" style="--c:#ff9f43">
                                    <span class="ugel-mock-kpi__n">{{ $stats['unidades'] }}</span>
                                    <span class="ugel-mock-kpi__l">Unidades</span>
                                </div>
                            </div>
                            <div class="ugel-mock-bar-row">
                                @php
                                    $barColors = ['#28c76f', '#7367f0', '#ff9f43', '#00cfe8'];
                                    $modBars = $modulos->take(4);
                                @endphp
                                @forelse ($modBars as $bi => $mb)
                                    <div class="ugel-mock-bar">
                                        <span>{{ Str::limit($mb->nombre, 14) }}</span>
                                        <div class="ugel-mock-bar__track">
                                            <div class="ugel-mock-bar__fill"
                                                style="width:{{ $stats['avance'] + ($bi % 2 == 0 ? 5 : -8) }}%;background:{{ $barColors[$bi] }}">
                                            </div>
                                        </div>
                                        <span
                                            class="ugel-mock-bar__pct">{{ $stats['avance'] + ($bi % 2 == 0 ? 5 : -8) }}%</span>
                                    </div>
                                @empty
                                    @foreach ([['SCI Comp. I', '92%', '#28c76f'], ['SCI Comp. II', '78%', '#7367f0'], ['SCI Comp. III', '65%', '#ff9f43']] as $b)
                                        <div class="ugel-mock-bar">
                                            <span>{{ $b[0] }}</span>
                                            <div class="ugel-mock-bar__track">
                                                <div class="ugel-mock-bar__fill"
                                                    style="width:{{ $b[1] }};background:{{ $b[2] }}">
                                                </div>
                                            </div>
                                            <span class="ugel-mock-bar__pct">{{ $b[1] }}</span>
                                        </div>
                                    @endforeach
                                @endforelse
                            </div>
                        </div>
                    </div>
                    {{-- Badges flotantes --}}
                    <div class="ugel-about__badge ugel-about__badge--1">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#28c76f"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Plan Anual actualizado
                    </div>
                    <div class="ugel-about__badge ugel-about__badge--2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7367f0"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        Control verificado
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ════ MÓDULOS DE GESTIÓN ════ --}}
    <section class="ugel-mods-section" id="modulos">

        {{-- Cabecera split --}}
        <div class="ugel-mods-head">
            <div class="container">
                <div class="ugel-mods-head__inner">
                    <div class="ugel-mods-head__left ugel-reveal">
                        <div class="ugel-mods-eyebrow">
                            <span class="ugel-mods-eyebrow__dot"></span>
                            Ecosistema Digital
                        </div>
                        <h2 class="ugel-mods-head__title">
                            Módulos de<br>Gestión <em>PULSO</em>
                        </h2>
                    </div>
                    <div class="ugel-mods-head__right ugel-reveal" style="transition-delay:120ms">
                        <p class="ugel-mods-head__desc">Herramientas integradas para la implementación y seguimiento
                            del Sistema de Control Interno, alineadas con los lineamientos de la Contraloría General
                            de la República.</p>
                        <div class="ugel-mods-head__stats">
                            <div class="ugel-mods-stat">
                                <span class="ugel-mods-stat__n">{{ $modulos->count() }}</span>
                                <span class="ugel-mods-stat__l">Módulos</span>
                            </div>
                            <div class="ugel-mods-stat">
                                <span class="ugel-mods-stat__n">{{ $stats['avance'] }}%</span>
                                <span class="ugel-mods-stat__l">Avance SCI</span>
                            </div>
                            <div class="ugel-mods-stat">
                                <span class="ugel-mods-stat__n">{{ $stats['paci'] }}</span>
                                <span class="ugel-mods-stat__l">Plan activo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cards showcase --}}
        @if ($modulos && $modulos->count() > 0)
            @php
                $paleta = [
                    ['a' => '#3B82F6', 'b' => '#1D4ED8', 'glow' => '59,130,246'] /* Blue principal */,
                    ['a' => '#0EA5E9', 'b' => '#0284C7', 'glow' => '14,165,233'] /* Sky */,
                    ['a' => '#10B981', 'b' => '#059669', 'glow' => '16,185,129'] /* Emerald acento */,
                    ['a' => '#F59E0B', 'b' => '#D97706', 'glow' => '245,158,11'] /* Dorado institucional */,
                    ['a' => '#60A5FA', 'b' => '#2563EB', 'glow' => '96,165,250'] /* Blue claro */,
                    ['a' => '#38BDF8', 'b' => '#0369A1', 'glow' => '56,189,248'] /* Cyan azul */,
                    ['a' => '#34D399', 'b' => '#059669', 'glow' => '52,211,153'] /* Verde */,
                    ['a' => '#93C5FD', 'b' => '#3B82F6', 'glow' => '147,197,253'] /* Blue pálido */,
                ];
            @endphp
            <div class="ugel-mods-track" id="ugelModsTrack">
                <div class="ugel-mods-cards" id="ugelModsCards">
                    @foreach ($modulos as $i => $mod)
                        @php $p = $paleta[$i % count($paleta)]; @endphp
                        <article class="ugel-xcard" data-glow="{{ $p['glow'] }}"
                            style="--xa:{{ $p['a'] }};--xb:{{ $p['b'] }};--xglow:{{ $p['glow'] }};">
                            {{-- Orbe de luz de fondo --}}
                            <div class="ugel-xcard__orb"></div>
                            {{-- Línea superior de color --}}
                            <div class="ugel-xcard__stripe"></div>
                            {{-- Número decorativo --}}
                            <span
                                class="ugel-xcard__num">{{ str_pad($mod->numero ?? $i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            {{-- Ícono --}}
                            <div class="ugel-xcard__icon">
                                <i class="ti {{ $mod->icono ?? 'tabler-layout-grid' }}"></i>
                            </div>
                            {{-- Contenido --}}
                            <div class="ugel-xcard__body">
                                <h4 class="ugel-xcard__title">{{ $mod->nombre }}</h4>
                                <p class="ugel-xcard__desc">
                                    {{ Str::limit($mod->descripcion ?? 'Seguimiento, registro y evaluación continua del componente del Sistema de Control Interno institucional.', 120) }}
                                </p>
                            </div>
                            {{-- Badge tipo --}}
                            <div class="ugel-xcard__foot">
                                <span class="ugel-xcard__badge">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    </svg>
                                    {{ $mod->tipo ? ucfirst($mod->tipo) : 'SCI' }}
                                </span>
                                <div class="ugel-xcard__arrow">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                        <polyline points="12 5 19 12 12 19" />
                                    </svg>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Controles de navegación --}}
                @if ($modulos->count() > 3)
                    <div class="ugel-mods-nav">
                        <button class="ugel-mods-nav__btn" id="ugelModsPrev" aria-label="Anterior">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </button>
                        <div class="ugel-mods-nav__dots" id="ugelModsDots"></div>
                        <button class="ugel-mods-nav__btn" id="ugelModsNext" aria-label="Siguiente">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        @else
            <div class="container">
                <div class="ugel-mods-empty ugel-reveal">
                    <div class="ugel-mods-empty__icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                        </svg>
                    </div>
                    <p>Los módulos del sistema estarán disponibles próximamente.</p>
                </div>
            </div>
        @endif

    </section>


    {{-- ════ NORMATIVA ════ --}}
    <section class="ugel-section" id="normativa">
        <div class="container">

            {{-- Cabecera --}}
            <span class="ugel-label">Marco Legal</span>
            <h2 class="ugel-section__title">Sustento Normativo</h2>
            <p class="ugel-section__sub">PULSO está alineado con las disposiciones vigentes del Estado peruano para el Sistema de Control Interno.</p>

            {{-- Lista de normativas full width --}}
            <div class="ugel-norm-list" id="norm-list-landing" style="gap:.6rem;margin-top:1.5rem">
                @forelse($normativas as $i => $n)
                @php
                    $num = str_pad($normativas->firstItem() + $i, 2, '0', STR_PAD_LEFT);
                    $tipoColors = ['ley'=>'#dc3545','decreto'=>'#fd7e14','resolucion'=>'#0d6efd',
                                   'directiva'=>'#0a98c0','manual'=>'#198754','reglamento'=>'#6c757d',
                                   'oficio'=>'#495057','otro'=>'#6c757d'];
                    $tc = $tipoColors[$n->tipo] ?? '#1340A0';
                @endphp
                <div class="ugel-norm" style="gap:1rem">
                    <div class="ugel-norm__num" style="width:32px;height:32px;font-size:.72rem;flex-shrink:0">{{ $num }}</div>
                    <div class="ugel-norm__content" style="padding:.7rem 1rem">
                        <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.25rem">
                            <span style="font-size:.6rem;font-weight:800;padding:.1rem .45rem;border-radius:20px;background:{{ $tc }}18;color:{{ $tc }};text-transform:uppercase;letter-spacing:.04em;white-space:nowrap">{{ $n->tipo_label }}</span>
                            @if($n->tiene_link)
                            <a href="{{ $n->link_externo }}" target="_blank" rel="noopener"
                               style="margin-left:auto;font-size:.68rem;color:#1340A0;text-decoration:none;white-space:nowrap;opacity:.75">↗ Ver norma</a>
                            @elseif($n->tiene_archivo)
                            <a href="{{ asset('storage/'.$n->archivo_path) }}" target="_blank"
                               style="margin-left:auto;font-size:.68rem;color:#1340A0;text-decoration:none;white-space:nowrap;opacity:.75">↓ Descargar</a>
                            @endif
                        </div>
                        <strong style="font-size:.85rem;display:block;line-height:1.3;color:#1e293b">{{ \Illuminate\Support\Str::limit($n->nombre, 100) }}</strong>
                        @if($n->entidad_emisora)
                        <span style="font-size:.72rem;color:#94a3b8;margin-top:.15rem;display:block">{{ $n->entidad_emisora }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <p style="font-size:.85rem;color:#94a3b8;padding:.5rem 0">No hay normativas vigentes registradas.</p>
                @endforelse
            </div>

            {{-- Paginación --}}
            @if($normativas->lastPage() > 1)
            <div style="display:flex;align-items:center;gap:.4rem;margin-top:1rem">
                @if($normativas->onFirstPage())
                <span style="width:28px;height:28px;border-radius:6px;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;color:#cbd5e1;font-size:.8rem;cursor:not-allowed">‹</span>
                @else
                <a href="{{ $normativas->previousPageUrl() }}#normativa"
                   style="width:28px;height:28px;border-radius:6px;border:1px solid #1340A0;display:flex;align-items:center;justify-content:center;color:#1340A0;text-decoration:none;font-size:.8rem"
                   onmouseover="this.style.background='#1340A0';this.style.color='#fff'"
                   onmouseout="this.style.background='';this.style.color='#1340A0'">‹</a>
                @endif
                <span style="font-size:.75rem;color:#94a3b8;padding:0 .25rem">{{ $normativas->currentPage() }} / {{ $normativas->lastPage() }}</span>
                @if($normativas->hasMorePages())
                <a href="{{ $normativas->nextPageUrl() }}#normativa"
                   style="width:28px;height:28px;border-radius:6px;border:1px solid #1340A0;display:flex;align-items:center;justify-content:center;color:#1340A0;text-decoration:none;font-size:.8rem"
                   onmouseover="this.style.background='#1340A0';this.style.color='#fff'"
                   onmouseout="this.style.background='';this.style.color='#1340A0'">›</a>
                @else
                <span style="width:28px;height:28px;border-radius:6px;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;color:#cbd5e1;font-size:.8rem;cursor:not-allowed">›</span>
                @endif
                <span style="font-size:.72rem;color:#94a3b8;margin-left:.25rem">{{ $normativas->total() }} normas</span>
            </div>
            @endif

            {{-- Divisor --}}
            <hr style="margin:2.5rem 0;border-color:#e2e8f0">

            {{-- Instituciones vinculadas full width --}}
            <span class="ugel-label">Instituciones vinculadas</span>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:.65rem;margin-top:1.25rem">
                @foreach($instituciones as $inst)
                @php $tag = $inst->url_sitio ? 'a' : 'div'; @endphp
                <{{ $tag }}
                    @if($inst->url_sitio)
                        href="{{ $inst->url_sitio }}" target="_blank" rel="noopener noreferrer"
                    @endif
                    style="display:flex;align-items:center;gap:.9rem;background:#fff;border:1px solid #e2e8f0;border-left:4px solid {{ $inst->color_acento }};border-radius:10px;padding:.65rem 1rem;transition:transform .2s,box-shadow .2s;text-decoration:none;color:inherit;{{ $inst->url_sitio ? 'cursor:pointer;' : '' }}"
                    onmouseover="this.style.transform='translateX(4px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">

                    {{-- Logo o sigla --}}
                    @if($inst->logo_src)
                        <img src="{{ $inst->logo_src }}" alt="{{ $inst->sigla }}" style="height:32px;width:auto;object-fit:contain;flex-shrink:0" loading="lazy">
                    @else
                        <span style="width:38px;height:38px;border-radius:8px;background:{{ $inst->color_acento }}18;color:{{ $inst->color_acento }};display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:900;flex-shrink:0;letter-spacing:-.02em">{{ $inst->sigla }}</span>
                    @endif

                    {{-- Texto --}}
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.82rem;font-weight:800;color:#1e293b;line-height:1.2">{{ $inst->sigla }}</div>
                        <div style="font-size:.72rem;color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $inst->nombre }}</div>
                        @if($inst->descripcion)
                        <div style="font-size:.68rem;color:#b0bec5;margin-top:.15rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $inst->descripcion }}</div>
                        @endif
                    </div>

                    {{-- Indicador enlace externo --}}
                    @if($inst->url_sitio)
                    <span style="flex-shrink:0;width:26px;height:26px;border-radius:6px;background:{{ $inst->color_acento }}15;display:flex;align-items:center;justify-content:center;" title="Ir al sitio oficial">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $inst->color_acento }}" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                    </span>
                    @endif

                </{{ $tag }}>
                @endforeach
            </div>

        </div>
    </section>


    {{-- ════ PUBLICACIONES RECIENTES ════ --}}
    @if($slides->count() > 0)
    <section class="ugel-section" id="publicaciones">
        <div class="container">

            <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
                <div>
                    <span class="ugel-label">Noticias · Eventos · Normativas</span>
                    <h2 class="ugel-section__title mb-0">Últimas Publicaciones</h2>
                </div>
                <a href="{{ route('landing.publicaciones') }}" class="ugel-btn ugel-btn--outline" style="white-space:nowrap;">
                    Ver todas
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>

            <div class="ugel-pub-grid">
                @foreach($slides->take(3) as $pub)
                @php
                    $tipoColor = match($pub->tipo) { 'evento' => '#28c76f', 'normativa' => '#ff9f43', default => '#1340A0' };
                    $tipoBg    = match($pub->tipo) { 'evento' => '#f0fdf4', 'normativa' => '#fff7ed', default => '#eff6ff' };
                @endphp
                <article class="ugel-pub-card">
                    <a href="{{ route('landing.noticia', $pub->id) }}" class="ugel-pub-card__img-wrap">
                        @if($pub->imagen_url)
                            <img src="{{ $pub->imagen_url }}" alt="{{ $pub->titulo }}" class="ugel-pub-card__img" loading="lazy">
                        @else
                            <div class="ugel-pub-card__img ugel-pub-card__img--gradient" style="background:{{ $pub->color_gradiente ?? 'linear-gradient(135deg,#1340A0,#7367f0)' }};"></div>
                        @endif
                        <span class="ugel-pub-card__badge" style="background:{{ $tipoBg }};color:{{ $tipoColor }};border:1px solid {{ $tipoColor }}22;">{{ ucfirst($pub->tipo) }}</span>
                    </a>
                    <div class="ugel-pub-card__body">
                        @if($pub->etiqueta)
                            <p class="ugel-pub-card__eye">{{ $pub->etiqueta }}</p>
                        @endif
                        <h3 class="ugel-pub-card__title">
                            <a href="{{ route('landing.noticia', $pub->id) }}">{{ Str::limit($pub->titulo, 70) }}</a>
                        </h3>
                        @if($pub->descripcion)
                            <p class="ugel-pub-card__desc">{{ Str::limit($pub->descripcion, 110) }}</p>
                        @endif
                        <div class="ugel-pub-card__foot">
                            @if($pub->autor)
                                <span class="ugel-pub-card__author">
                                    <span class="ugel-pub-card__av" style="background:linear-gradient(135deg,#1340A0,#28c76f);">{{ strtoupper(substr($pub->autor,0,1)) }}</span>
                                    {{ $pub->autor }}
                                </span>
                            @endif
                            <a href="{{ route('landing.noticia', $pub->id) }}" class="ugel-pub-card__read">
                                Leer más
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

        </div>
    </section>
    @endif

    {{-- ════ CTA ════ --}}
    <section class="ugel-cta mb-4">
        <div class="container">
            <div class="ugel-cta__inner">
                <div>
                    <span class="ugel-cta__label">
                        <span class="ugel-cta__dot"></span> Sistema disponible
                    </span>
                    <h2 class="ugel-cta__title">¿Listo para gestionar el<br>Control Interno Institucional?</h2>
                    <p class="ugel-cta__sub">Accede con tus credenciales institucionales asignadas por el administrador del
                        sistema.</p>
                </div>
                <div class="ugel-cta__action">
                    <a href="{{ route('login') }}" class="ugel-btn ugel-btn--white ugel-btn--lg">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        Iniciar Sesión
                    </a>
                    <p class="ugel-cta__note">Solo usuarios autorizados por la institución.</p>
                </div>
            </div>
        </div>
    </section>


    {{-- ════ CONTACTO v3 — Layout split premium ════ --}}
    <section class="ugel-contact-section" id="contacto">
        <div class="container">
            <div class="ugel-ct-wrap ugel-reveal">

                {{-- ── COLUMNA IZQUIERDA: ID Card institucional ── --}}
                <div class="ugel-ct-left">

                    {{-- Eyebrow --}}
                    <div class="ugel-ct-eyebrow">
                        <span class="ugel-ct-eyebrow__dot"></span>
                        Información de Contacto
                    </div>

                    {{-- Nombre institución --}}
                    <h2 class="ugel-ct-title">
                        {{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local de Huacaybamba' }}
                    </h2>

                    {{-- Ubicación --}}
                    @if ($config?->direccion || $config?->region)
                        <div class="ugel-ct-loc">
                            <div class="ugel-ct-loc__icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </div>
                            <div>
                                <p class="ugel-ct-loc__addr">{{ $config?->direccion ?? 'Jr. Huacaybamba S/N' }}</p>
                                <p class="ugel-ct-loc__city">
                                    {{ implode(', ', array_filter([$config?->distrito, $config?->provincia, $config?->region])) }}
                                    — Perú</p>
                            </div>
                        </div>
                    @endif

                    {{-- Divisor --}}
                    <div class="ugel-ct-divider"></div>

                    {{-- Autoridades --}}
                    @if ($config?->director || $config?->coordinador_sci)
                        <div class="ugel-ct-people">
                            @if ($config?->director)
                                <div class="ugel-ct-person">
                                    <div class="ugel-ct-person__av ugel-ct-person__av--blue">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                    </div>
                                    <div class="ugel-ct-person__info">
                                        <span class="ugel-ct-person__role">Director</span>
                                        <strong class="ugel-ct-person__name">{{ $config->director }}</strong>
                                    </div>
                                </div>
                            @endif
                            @if ($config?->coordinador_sci)
                                <div class="ugel-ct-person ugel-ct-person--sci">
                                    <div class="ugel-ct-person__av ugel-ct-person__av--navy">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        </svg>
                                    </div>
                                    <div class="ugel-ct-person__info">
                                        <span class="ugel-ct-person__role">{{ $config->cargo_sci ?? 'Coordinador SCI' }}</span>
                                        <strong class="ugel-ct-person__name">{{ $config->coordinador_sci }}</strong>
                                        {{-- Contacto directo del coordinador --}}
                                        <div class="ugel-ct-person__links">
                                            @if ($config?->whatsapp_sci)
                                                @php
                                                    $wa = preg_replace('/\D/', '', $config->whatsapp_sci);
                                                    $waMsg = urlencode('Hola, me comunico desde el portal PULSO UGEL para consultar sobre el Sistema de Control Interno.');
                                                @endphp
                                                <a href="https://wa.me/51{{ $wa }}?text={{ $waMsg }}"
                                                   target="_blank" rel="noopener"
                                                   class="ugel-ct-person__link ugel-ct-person__link--wa"
                                                   title="WhatsApp directo al Coordinador SCI">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                                                    </svg>
                                                    +51 {{ $config->whatsapp_sci }}
                                                </a>
                                            @endif
                                            @if ($config?->correo_sci)
                                                <a href="mailto:{{ $config->correo_sci }}"
                                                   class="ugel-ct-person__link ugel-ct-person__link--mail"
                                                   title="Correo del Coordinador SCI">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                        <polyline points="22,6 12,13 2,6"/>
                                                    </svg>
                                                    {{ $config->correo_sci }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Tags institucionales --}}
                    <div class="ugel-ct-tags">
                        @if ($config?->ugel_codigo)
                            <span class="ugel-ct-tag">Cód. {{ $config->ugel_codigo }}</span>
                        @endif
                        @if ($config?->ubigeo)
                            <span class="ugel-ct-tag">Ubigeo {{ $config->ubigeo }}</span>
                        @endif
                        <span class="ugel-ct-tag">Sector Educación</span>
                        <span class="ugel-ct-tag ugel-ct-tag--green">
                            <span class="ugel-ct-tag__dot"></span> Sistema activo
                        </span>
                    </div>

                </div>

                {{-- ── COLUMNA DERECHA: datos de contacto ── --}}
                <div class="ugel-ct-right">

                    {{-- Item correo --}}
                    @if ($config?->correo_institucional)
                        <a href="mailto:{{ $config->correo_institucional }}" class="ugel-ct-item ugel-ct-item--link">
                            <div class="ugel-ct-item__icon" style="--cti:#DC2626;--ctil:#FFF1F3">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </div>
                            <div class="ugel-ct-item__body">
                                <span class="ugel-ct-item__label">Correo institucional</span>
                                <strong class="ugel-ct-item__val">{{ $config->correo_institucional }}</strong>
                            </div>
                            <div class="ugel-ct-item__arr">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </div>
                        </a>
                    @endif

                    {{-- Item teléfono --}}
                    <div class="ugel-ct-item {{ $config?->telefono ? 'ugel-ct-item--link' : '' }}"
                        @if ($config?->telefono) onclick="window.location='tel:{{ $config->telefono }}'" style="cursor:pointer" @endif>
                        <div class="ugel-ct-item__icon" style="--cti:#EA580C;--ctil:#FFF7ED">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.38 2 2 0 0 1 3.59 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                        </div>
                        <div class="ugel-ct-item__body">
                            <span class="ugel-ct-item__label">Teléfono / Soporte</span>
                            <strong class="ugel-ct-item__val">{{ $config?->telefono ?? 'No configurado' }}</strong>
                        </div>
                        @if ($config?->telefono)
                            <div class="ugel-ct-item__arr">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Item WhatsApp SCI --}}
                    @if ($config?->whatsapp_sci)
                        @php
                            $waSci = preg_replace('/\D/', '', $config->whatsapp_sci);
                            $waMsgSci = urlencode('Hola, me comunico desde el portal PULSO UGEL para consultar sobre el Sistema de Control Interno.');
                        @endphp
                        <a href="https://wa.me/51{{ $waSci }}?text={{ $waMsgSci }}"
                           target="_blank" rel="noopener"
                           class="ugel-ct-item ugel-ct-item--link">
                            <div class="ugel-ct-item__icon" style="--cti:#25D366;--ctil:#ECFDF5">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#25D366">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                                </svg>
                            </div>
                            <div class="ugel-ct-item__body">
                                <span class="ugel-ct-item__label">WhatsApp — {{ $config->cargo_sci ?? 'Coordinador SCI' }}</span>
                                <strong class="ugel-ct-item__val">+51 {{ $config->whatsapp_sci }}</strong>
                            </div>
                            <div class="ugel-ct-item__arr">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </div>
                        </a>
                    @endif

                    {{-- Item horario --}}
                    <div class="ugel-ct-item">
                        <div class="ugel-ct-item__icon" style="--cti:#059669;--ctil:#ECFDF5">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <div class="ugel-ct-item__body">
                            <span class="ugel-ct-item__label">Horario de atención</span>
                            <strong class="ugel-ct-item__val">Lun – Vie · 8:00 am — 6:00 pm</strong>
                        </div>
                        <div class="ugel-ct-item__badge ugel-ct-item__badge--open">Abierto</div>
                    </div>

                    {{-- Item sitio web --}}
                    @if ($config?->sitio_web)
                        <a href="{{ $config->sitio_web }}" target="_blank" rel="noopener noreferrer"
                            class="ugel-ct-item ugel-ct-item--link">
                            <div class="ugel-ct-item__icon" style="--cti:#3B82F6;--ctil:#EFF6FF">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="2" y1="12" x2="22" y2="12" />
                                    <path
                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                </svg>
                            </div>
                            <div class="ugel-ct-item__body">
                                <span class="ugel-ct-item__label">Portal institucional</span>
                                <strong
                                    class="ugel-ct-item__val">{{ parse_url($config->sitio_web, PHP_URL_HOST) ?? $config->sitio_web }}</strong>
                            </div>
                            <div class="ugel-ct-item__arr">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </div>
                        </a>
                    @endif

                    {{-- Item dirección (mini mapa placeholder) --}}
                    <div class="ugel-ct-map">
                        <div class="ugel-ct-map__pin">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                        </div>
                        <div class="ugel-ct-map__body">
                            <span class="ugel-ct-item__label">Dirección</span>
                            <strong class="ugel-ct-item__val" style="font-size:.9rem">
                                {{ $config?->direccion ?? 'Jr. Huacaybamba S/N' }}
                                @if ($config?->distrito)
                                    , {{ $config->distrito }}
                                @endif
                            </strong>
                            @if ($config?->region)
                                <span style="font-size:.78rem;color:var(--muted)">{{ $config->region }} — Perú</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    {{-- ════ FOOTER ════ --}}
    <footer class="ugel-footer-new">
        {{-- Franja superior oscura con datos --}}
        <div class="ugel-footer-new__top">
            <div class="container">
                <div class="ugel-footer-new__grid">

                    {{-- Col 1: Brand --}}
                    <div class="ugel-fn-brand">
                        <div class="ugel-fn-brand__logo">
                            @if ($config?->logo_ruta)
                                <img src="{{ Storage::url($config->logo_ruta) }}" alt="{{ $config?->sigla ?? 'UGEL' }}">
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
                        {{-- Datos clave --}}
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
                        <h6 class="ugel-fn-col__title">
                            <span class="ugel-fn-col__title-ico">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                            </span>
                            Navegación
                        </h6>
                        <ul>
                            <li>
                                <a href="#inicio">
                                    <span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                                    Inicio
                                </a>
                            </li>
                            <li>
                                <a href="#sistema">
                                    <span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
                                    Sistema PULSO
                                </a>
                            </li>
                            <li>
                                <a href="#modulos">
                                    <span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
                                    Módulos
                                </a>
                            </li>
                            <li>
                                <a href="#publicaciones">
                                    <span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
                                    Publicaciones
                                </a>
                            </li>
                            <li>
                                <a href="#normativa">
                                    <span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></span>
                                    Normativa
                                </a>
                            </li>
                            <li>
                                <a href="#contacto">
                                    <span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
                                    Contacto
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('login') }}" class="ugel-fn-link--accent">
                                    <span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                                    Acceso al Sistema
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Col 3: Instituciones --}}
                    <div class="ugel-fn-col">
                        <h6 class="ugel-fn-col__title">
                            <span class="ugel-fn-col__title-ico">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </span>
                            Instituciones
                        </h6>
                        <ul>
                            @forelse ($instituciones->take(6) as $inst)
                                <li>
                                    <a href="{{ $inst->url_sitio ?? '#' }}"
                                        @if ($inst->url_sitio) target="_blank" rel="noopener noreferrer" @endif>
                                        <span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>
                                        <span class="ugel-fn-col__abbr">{{ $inst->sigla }}</span>
                                        {{ $inst->nombre }}
                                    </a>
                                </li>
                            @empty
                                <li><a href="#"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>Contraloría General de la Rep.</a></li>
                                <li><a href="#"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>Ministerio de Educación</a></li>
                                <li><a href="#"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>Gobierno Regional Huánuco</a></li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Col 4: Normativa + info institucional --}}
                    <div class="ugel-fn-col">
                        <h6 class="ugel-fn-col__title">
                            <span class="ugel-fn-col__title-ico">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </span>
                            Normativa SCI
                        </h6>
                        <ul>
                            <li><a href="#normativa"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>Ley N° 28716 — Control Interno</a></li>
                            <li><a href="#normativa"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>R.C. N° 320-2006-CG</a></li>
                            <li><a href="#normativa"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>Directiva N° 006-2019-CG</a></li>
                            <li><a href="#normativa"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>R.C. N° 004-2017-CG</a></li>
                            <li><a href="#normativa"><span class="ugel-fn-link-ico"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>Plan Anual SCI {{ $stats['paci'] }}</a></li>
                        </ul>
                        {{-- Mini datos institucionales --}}
                        <div class="ugel-fn-inst-data">
                            @if ($config?->ugel_codigo)
                                <div class="ugel-fn-inst-row">
                                    <span><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;margin-right:.3rem"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="15" x2="12" y2="15"/></svg>Cód. UGEL</span>
                                    <strong>{{ $config->ugel_codigo }}</strong>
                                </div>
                            @endif
                            @if ($config?->ubigeo)
                                <div class="ugel-fn-inst-row">
                                    <span><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;margin-right:.3rem"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>Ubigeo</span>
                                    <strong>{{ $config->ubigeo }}</strong>
                                </div>
                            @endif
                            <div class="ugel-fn-inst-row">
                                <span><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;margin-right:.3rem"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>Región</span>
                                <strong>{{ $config?->region ?? 'Huánuco' }}</strong>
                            </div>
                            <div class="ugel-fn-inst-row">
                                <span><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;margin-right:.3rem"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Sector</span>
                                <strong>Educación</strong>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Barra inferior --}}
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
                        <span class="ugel-fn-dev" id="__sysref_a1">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.55">
                                <polyline points="16 18 22 12 16 6" />
                                <polyline points="8 6 2 12 8 18" />
                            </svg>
                            <span id="__sysref_b2"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

@endsection
