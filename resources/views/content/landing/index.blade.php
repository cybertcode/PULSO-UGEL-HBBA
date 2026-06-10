@php
    $configData = Helper::appClasses();
    $isFront = true;
    $customizerHidden = 'customizer-hide'; // Ocultar customizer en el landing
@endphp
@extends('layouts/layoutLanding')
@section('title', 'PULSO UGEL — Sistema de Control Interno')
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

        /* Envoltorio principal para centrar todo el contenido al 90% */
        .ugel-topbar,
        .ugel-nav,
        section,
        .ugel-footer {
            width: 90% !important;
            max-width: 1800px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Ajustes específicos para elementos pegajosos y bordes */
        .ugel-nav {
            border-radius: 0 0 12px 12px;
        }
        
        .ugel-hero__grid, 
        .ugel-hero__stats {
            width: 100% !important; /* Ocupan todo el ancho de su padre (que ya es 90%) */
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
            .ugel-topbar, .ugel-nav, section, .ugel-footer {
                width: 96% !important;
            }
            .container {
                width: 92% !important;
            }
        }
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
                    Lun – Vie: 8:00 am – 4:30 pm
                </div>
                <div class="ugel-topbar__right">
                    <span class="ugel-topbar__live">
                        <span class="ugel-topbar__dot"></span> Sistema en línea
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
                    <li><a href="#inicio" class="ugel-nav__link active">Inicio</a></li>
                    <li><a href="#sistema" class="ugel-nav__link">Sistema PULSO</a></li>
                    <li><a href="#modulos" class="ugel-nav__link">Módulos</a></li>
                    <li><a href="#normativa" class="ugel-nav__link">Normativa</a></li>
                    <li><a href="#contacto" class="ugel-nav__link">Contacto</a></li>
                </ul>

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
        <div class="ugel-hero__grid">

            {{-- Columna izquierda: texto institucional fijo --}}
            <div class="ugel-hero__left">

                <div class="ugel-pill">
                    <span class="ugel-pill__dot"></span>
                    {{ $config?->sigla ?? 'UGEL Huacaybamba' }} · Sistema de Control Interno
                </div>

                <h1 class="ugel-hero__title">
                    Sistema <em>PULSO UGEL</em><br>Control Interno
                </h1>

                <p class="ugel-hero__desc">
                    Plataforma digital para la gestión, seguimiento y evaluación del Sistema de Control Interno
                    de la {{ $config?->nombre_institucion ?? 'UGEL Huacaybamba' }},
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
                        Ver módulos
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
                                        <h3 class="ugel-slide__title">PULSO UGEL en línea</h3>
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
                    <span class="ugel-label">¿Qué es PULSO UGEL?</span>
                    <h2 class="ugel-section__title">Sistema de Control Interno para la <span
                            class="ugel-text-accent">{{ $config?->sigla ?? 'UGEL Huacaybamba' }}</span></h2>
                    <p class="ugel-section__sub">PULSO UGEL es la plataforma digital oficial de la
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
                                    <span class="ugel-mock-kpi__n">85%</span>
                                    <span class="ugel-mock-kpi__l">Avance SCI</span>
                                </div>
                                <div class="ugel-mock-kpi" style="--c:#28c76f">
                                    <span class="ugel-mock-kpi__n">142</span>
                                    <span class="ugel-mock-kpi__l">Actividades</span>
                                </div>
                                <div class="ugel-mock-kpi" style="--c:#ff9f43">
                                    <span class="ugel-mock-kpi__n">12</span>
                                    <span class="ugel-mock-kpi__l">Alertas</span>
                                </div>
                            </div>
                            <div class="ugel-mock-bar-row">
                                @foreach ([['Componente A', '92%', '#28c76f'], ['Componente B', '78%', '#7367f0'], ['Componente C', '65%', '#ff9f43'], ['Componente D', '88%', '#00cfe8']] as $b)
                                    <div class="ugel-mock-bar">
                                        <span>{{ $b[0] }}</span>
                                        <div class="ugel-mock-bar__track">
                                            <div class="ugel-mock-bar__fill"
                                                style="width:{{ $b[1] }};background:{{ $b[2] }}"></div>
                                        </div>
                                        <span class="ugel-mock-bar__pct">{{ $b[1] }}</span>
                                    </div>
                                @endforeach
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
    <section class="ugel-section ugel-section--alt" id="modulos">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end mb-4 ugel-reveal">
                <div style="max-width: 600px;">
                    <span class="ugel-label">Ecosistema Digital</span>
                    <h2 class="ugel-section__title mb-0">Módulos de Gestión <span class="ugel-text-accent">PULSO</span></h2>
                </div>
                <div class="mt-3 mt-lg-0 text-lg-end" style="max-width: 450px;">
                    <p class="ugel-section__sub mb-0" style="font-size: 0.95rem;">
                        Herramientas integradas gestionadas desde el panel administrativo para el fortalecimiento del Control Interno institucional.
                    </p>
                </div>
            </div>

            <div class="ugel-components-grid">
                @if($modulos && $modulos->count() > 0)
                    @foreach($modulos as $index => $mod)
                        <div class="ugel-comp-card ugel-reveal" style="transition-delay: {{ $index * 50 }}ms;">
                            <div class="ugel-comp-card__icon">
                                <i class="ti {{ str_replace('tabler-', 'ti-', $mod->icono ?? 'ti-check') }}"></i>
                            </div>
                            <div class="ugel-comp-card__body">
                                <h5 class="ugel-comp-card__title">{{ $mod->nombre }}</h5>
                                <p class="ugel-comp-card__desc">{{ Str::limit($mod->descripcion ?? 'Monitoreo continuo del componente.', 95) }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center text-muted">No hay componentes configurados en el sistema.</div>
                @endif
            </div>
        </div>
    </section>


    {{-- ════ NORMATIVA ════ --}}
    <section class="ugel-section" id="normativa">
        <div class="container">
            <div class="ugel-norm-grid">

                <div>
                    <span class="ugel-label">Marco Legal</span>
                    <h2 class="ugel-section__title">Sustento Normativo</h2>
                    <p class="ugel-section__sub">PULSO UGEL está alineado con las disposiciones vigentes del Estado peruano
                        para el Sistema de Control Interno.</p>

                    <div class="ugel-norm-list">
                        @foreach ([['01', 'Ley N° 28716', 'Ley de Control Interno de las Entidades del Estado — Congreso de la República'], ['02', 'R.C. N° 320-2006-CG', 'Normas de Control Interno — Contraloría General de la República'], ['03', 'Directiva N° 006-2019-CG', 'Modelo de Integridad para el Sector Público Peruano'], ['04', 'R.C. N° 004-2017-CG', 'Guía para la Implementación y Fortalecimiento del SCI']] as $n)
                            <div class="ugel-norm">
                                <div class="ugel-norm__num">{{ $n[0] }}</div>
                                <div class="ugel-norm__content">
                                    <strong>{{ $n[1] }}</strong>
                                    <p>{{ $n[2] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="ugel-inst-col">
                    <span class="ugel-label">Instituciones vinculadas</span>
                    <div class="ugel-inst-grid">
                        @foreach ($instituciones as $inst)
                            <div class="ugel-inst" style="--ac:{{ $inst->color_acento }}">
                                @if ($inst->logo_src)
                                    <img src="{{ $inst->logo_src }}" alt="{{ $inst->sigla }}" class="ugel-inst__logo"
                                        loading="lazy">
                                @else
                                    <span class="ugel-inst__abbr">{{ $inst->sigla }}</span>
                                @endif
                                <span class="ugel-inst__name">{{ $inst->nombre }}</span>
                                @if ($inst->url_sitio)
                                    <a href="{{ $inst->url_sitio }}" target="_blank" rel="noopener"
                                        class="ugel-inst__link">Ver sitio</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ════ CTA ════ --}}
    <section class="ugel-cta">
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


    {{-- ════ CONTACTO ════ --}}
    <section class="ugel-section ugel-section--alt" id="contacto">
        <div class="container">
            <div class="text-center mb-5">
                <span class="ugel-label">Información de contacto</span>
                <h2 class="ugel-section__title">{{ $config?->nombre_institucion ?? 'UGEL Huacaybamba' }}</h2>
                @if ($config?->director)
                    <p class="ugel-section__sub">Director: <strong>{{ $config->director }}</strong>
                        @if ($config->coordinador_sci)
                            · Coordinador SCI: <strong>{{ $config->coordinador_sci }}</strong>
                        @endif
                    </p>
                @endif
            </div>
            <div class="ugel-contact-grid">
                <div class="ugel-contact-card">
                    <div class="ugel-contact-card__ico" style="--c:#1a237e">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </div>
                    <div>
                        <h6>Dirección</h6>
                        <p>{{ $config?->direccion ?? 'Jr. Huacaybamba S/N' }}<br>{{ $config?->distrito ?? 'Huacaybamba' }},
                            {{ $config?->region ?? 'Huánuco' }} — Perú</p>
                    </div>
                </div>
                <div class="ugel-contact-card">
                    <div class="ugel-contact-card__ico" style="--c:#c62828">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                    </div>
                    <div>
                        <h6>Correo Electrónico</h6>
                        <p>{{ $config?->correo_institucional ?? 'noreply@ugel-huacaybamba.gob.pe' }}</p>
                    </div>
                </div>
                <div class="ugel-contact-card">
                    <div class="ugel-contact-card__ico" style="--c:#1b5e20">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </div>
                    <div>
                        <h6>Horario de Atención</h6>
                        <p>Lunes a Viernes<br>8:00 am — 4:30 pm</p>
                    </div>
                </div>
                <div class="ugel-contact-card">
                    <div class="ugel-contact-card__ico" style="--c:#e65100">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.38 2 2 0 0 1 3.59 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                    </div>
                    <div>
                        <h6>Teléfono / Soporte</h6>
                        <p>{{ $config?->telefono ?? '—' }}<br>Coordinación de Tecnología</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ════ FOOTER ════ --}}
    <footer class="ugel-footer">
        <div class="ugel-footer__top">
            <div class="container">
                <div class="ugel-footer__grid">

                    <div class="ugel-footer__col ugel-footer__col--brand">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            @if ($config?->logo_ruta)
                                <img src="{{ Storage::url($config->logo_ruta) }}" alt="{{ $config->sigla ?? 'UGEL' }}" style="width:28px;height:28px;object-fit:contain;">
                            @else
                                <svg width="28" height="28" viewBox="0 0 48 48" fill="none">
                                    <path d="M24 4 L44 12 L44 28 C44 38 34 44 24 47 C14 44 4 38 4 28 L4 12 Z" fill="#c62828" opacity=".9" />
                                    <path d="M24 4 L24 47 C14 44 4 38 4 28 L4 12 Z" fill="#1a237e" opacity=".85" />
                                    <path d="M18 20 L22 26 L30 18" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                </svg>
                            @endif
                            <div>
                                <div class="fw-bold text-white" style="font-size:.9rem;">{{ $config?->sigla ?? 'Ugel Huacaybamba' }}</div>
                                <div style="font-size:.6rem;color:rgba(255,255,255,.4);">{{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local' }}</div>
                            </div>
                        </div>
                        <p class="ugel-footer__about">{{ $config?->nombre_institucion ?? 'Unidad de Gestión Educativa Local de Huacaybamba' }} — Región {{ $config?->region ?? 'Huánuco' }}, Perú. Comprometidos con la calidad educativa y el control institucional.</p>
                        <div class="ugel-footer__tags">
                            <span>{{ $config?->departamento ?? 'Huánuco' }}, Perú</span>
                            <span>{{ $stats['paci'] }}</span>
                            <span>Sector Educación</span>
                        </div>
                    </div>

                    <div class="ugel-footer__col">
                        <h6 class="ugel-footer__col-title">Navegación</h6>
                        <ul>
                            <li><a href="#inicio">Inicio</a></li>
                            <li><a href="#sistema">Sistema PULSO</a></li>
                            <li><a href="#modulos">Módulos</a></li>
                            <li><a href="#normativa">Normativa</a></li>
                            <li><a href="#contacto">Contacto</a></li>
                        </ul>
                    </div>

                    <div class="ugel-footer__col">
                        <h6 class="ugel-footer__col-title">Instituciones</h6>
                        <ul>
                            @if(isset($instituciones) && $instituciones->count() > 0)
                                @foreach($instituciones->take(5) as $inst)
                                    <li><a href="{{ $inst->url_sitio ?? '#' }}" target="{{ $inst->url_sitio ? '_blank' : '_self' }}">{{ $inst->nombre }}</a></li>
                                @endforeach
                            @else
                                <li><a href="#">Contraloría General de la Rep.</a></li>
                                <li><a href="#">Ministerio de Educación</a></li>
                                <li><a href="#">Gobierno Regional Huánuco</a></li>
                            @endif
                        </ul>
                    </div>

                    <div class="ugel-footer__col">
                        <h6 class="ugel-footer__col-title">Normativa SCI</h6>
                        <ul>
                            <li><a href="#">Ley N° 28716 — Control Interno</a></li>
                            <li><a href="#">R.C. N° 320-2006-CG</a></li>
                            <li><a href="#">Directiva N° 006-2019-CG</a></li>
                            <li><a href="#">R.C. N° 004-2017-CG</a></li>
                            <li><a href="#">Plan Anual SCI {{ $stats['paci'] }}</a></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        <div class="ugel-footer__bottom">
            <div class="container">
                <div class="ugel-footer__bottom-inner">
                    <span>© {{ date('Y') }} {{ $config?->sigla ?? 'UGEL Huacaybamba' }} — Gobierno Regional {{ $config?->region ?? 'Huánuco' }}. Todos los derechos reservados.</span>
                    <span>Sistema PULSO UGEL · Control Interno</span>
                </div>
            </div>
        </div>
    </footer>

@endsection
