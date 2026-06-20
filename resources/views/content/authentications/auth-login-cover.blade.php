@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
$ci = \App\Models\ConfiguracionInstitucional::cached();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Iniciar Sesión - ' . ($ci?->sigla ?? $ci?->nombre_institucion ?? 'PULSO UGEL'))

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap');

  :root {
    --gov-navy: #001a4d;
    --gov-blue: #003087;
    --gov-mid: #0047b3;
    --gov-light: #1565c0;
    --gold: #c9a227;
    --gold-light: #e8c547;
    --gold-pale: #f5e6b0;
    --cream: #fafaf7;
    --text-dark: #0d1b2a;
    --text-muted: #5a6a7a;
    --border: #e2e8f0;
    --white: #ffffff;
  }

  * { box-sizing: border-box; }

  html, body {
    height: 100%;
    margin: 0;
    font-family: 'DM Sans', sans-serif;
  }

  .pulso-login-wrapper {
    display: flex;
    height: 100vh;
    width: 100%;
    overflow: hidden;
    position: relative;
  }

  /* ═══════════════════════════════════════
     PANEL IZQUIERDO — INSTITUCIONAL
  ═══════════════════════════════════════ */
  .pulso-left {
    flex: 0 0 58.333%;
    max-width: 58.333%;
    position: relative;
    background: var(--gov-navy);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    overflow: hidden;
    padding: 3rem 3rem 0 3rem;
  }

  /* Fondo con patrón geométrico andino */
  .pulso-left::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
      linear-gradient(135deg, var(--gov-navy) 0%, var(--gov-blue) 50%, #002470 100%);
    z-index: 0;
  }

  /* Diagonal dorada — sello de autoridad */
  .pulso-left::after {
    content: '';
    position: absolute;
    top: -10%;
    right: -5%;
    width: 3px;
    height: 130%;
    background: linear-gradient(180deg, transparent 0%, var(--gold) 30%, var(--gold-light) 60%, transparent 100%);
    transform: rotate(-12deg);
    transform-origin: top center;
    opacity: 0.6;
    z-index: 1;
  }

  /* Grid de puntos andino */
  .left-pattern {
    position: absolute;
    inset: 0;
    z-index: 1;
    background-image:
      radial-gradient(circle, rgba(201,162,39,0.12) 1px, transparent 1px),
      radial-gradient(circle, rgba(201,162,39,0.06) 1px, transparent 1px);
    background-size: 40px 40px, 80px 80px;
    background-position: 0 0, 20px 20px;
  }

  /* Líneas geométricas decorativas */
  .left-geo {
    position: absolute;
    inset: 0;
    z-index: 1;
    overflow: hidden;
  }

  .left-geo .geo-line {
    position: absolute;
    background: linear-gradient(90deg, transparent, rgba(201,162,39,0.15), transparent);
    height: 1px;
    width: 100%;
    animation: geo-pulse 4s ease-in-out infinite;
  }

  .left-geo .geo-line:nth-child(1) { top: 20%; animation-delay: 0s; }
  .left-geo .geo-line:nth-child(2) { top: 40%; animation-delay: 1s; opacity: 0.6; }
  .left-geo .geo-line:nth-child(3) { top: 65%; animation-delay: 2s; }
  .left-geo .geo-line:nth-child(4) { top: 85%; animation-delay: 1.5s; opacity: 0.4; }

  /* Círculo decorativo superior */
  .left-circle-top {
    position: absolute;
    top: -120px;
    left: -120px;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    border: 1px solid rgba(201,162,39,0.1);
    z-index: 1;
  }
  .left-circle-top::after {
    content: '';
    position: absolute;
    inset: 30px;
    border-radius: 50%;
    border: 1px solid rgba(201,162,39,0.08);
  }

  /* Círculo decorativo inferior */
  .left-circle-bottom {
    position: absolute;
    bottom: -150px;
    right: -100px;
    width: 450px;
    height: 450px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.05);
    z-index: 1;
  }

  .left-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: var(--white);
    max-width: 520px;
    width: 100%;
  }

  /* Escudo institucional SVG animado */
  .escudo-wrapper {
    margin: 0 auto 1rem;
    width: 130px;
    height: 130px;
    position: relative;
    animation: escudo-float 6s ease-in-out infinite;
  }

  .escudo-ring {
    position: absolute;
    inset: -12px;
    border-radius: 50%;
    border: 1px solid rgba(201,162,39,0.3);
    animation: ring-spin 20s linear infinite;
  }
  .escudo-ring::before {
    content: '';
    position: absolute;
    top: -3px;
    left: 50%;
    width: 6px;
    height: 6px;
    background: var(--gold);
    border-radius: 50%;
    transform: translateX(-50%);
  }

  .escudo-icon {
    width: 130px;
    height: 130px;
    background: linear-gradient(135deg, rgba(201,162,39,0.2), rgba(201,162,39,0.05));
    border-radius: 50%;
    border: 2px solid rgba(201,162,39,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.4rem;
    box-shadow: 0 0 40px rgba(201,162,39,0.15), inset 0 0 30px rgba(201,162,39,0.05);
  }

  .left-pretitle {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.7rem;
    font-weight: 500;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: var(--gold-light);
    margin-bottom: 0.5rem;
    opacity: 0.9;
  }

  .left-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(1.6rem, 3vw, 2.4rem);
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 0.4rem;
    letter-spacing: -0.02em;
    color: var(--white);
  }

  .left-title span {
    color: var(--gold-light);
    display: block;
  }

  .left-subtitle {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.55);
    line-height: 1.5;
    margin-bottom: 1.2rem;
    font-weight: 300;
    max-width: 380px;
    margin-left: auto;
    margin-right: auto;
  }

  /* Divider dorado */
  .left-divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.2rem;
  }
  .left-divider-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(201,162,39,0.4), transparent);
  }
  .left-divider-diamond {
    width: 6px;
    height: 6px;
    background: var(--gold);
    transform: rotate(45deg);
    flex-shrink: 0;
  }

  /* Stats institucionales */
  .left-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: rgba(201,162,39,0.15);
    border: 1px solid rgba(201,162,39,0.2);
    border-radius: 16px;
    overflow: hidden;
    backdrop-filter: blur(10px);
  }

  .stat-item {
    background: rgba(255,255,255,0.03);
    padding: 1.25rem 1rem;
    text-align: center;
    transition: background 0.3s;
  }
  .stat-item:hover {
    background: rgba(201,162,39,0.08);
  }

  .stat-icon {
    font-size: 1.4rem;
    margin-bottom: 0.5rem;
    display: block;
    opacity: 0.9;
  }

  .stat-value {
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--gold-light);
    line-height: 1;
    margin-bottom: 0.25rem;
  }

  .stat-label {
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.4);
    font-weight: 500;
  }

  /* Footer izquierdo */
  .left-footer {
    position: relative;
    width: 100%;
    left: auto;
    right: auto;
    bottom: auto;
    text-align: center;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 0;
    margin-top: auto;
  }

  .left-footer-text {
    font-size: 0.65rem;
    color: rgba(255,255,255,0.2);
    letter-spacing: 0.15em;
    text-transform: uppercase;
    margin: 0;
    padding: 0.5rem 1rem;
  }

  /* Tira de logos de instituciones */
  .unidades-strip {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    padding: 0.5rem 1.5rem 0.75rem;
    overflow-x: auto;
    scrollbar-width: none;
  }
  .unidades-strip::-webkit-scrollbar { display: none; }

  .unidad-logo-item {
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    opacity: 0.5;
    transition: opacity 0.25s, transform 0.25s;
    text-decoration: none;
    cursor: pointer;
  }
  .unidad-logo-item:hover {
    opacity: 1;
    transform: translateY(-4px);
  }
  .unidad-logo-item img {
    width: 32px;
    height: 32px;
    object-fit: contain;
    border-radius: 50%;
    border: 1px solid rgba(201,162,39,0.3);
    background: rgba(255,255,255,0.08);
  }
  .unidad-logo-initials {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(201,162,39,0.3), rgba(0,48,135,0.4));
    border: 1px solid rgba(201,162,39,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    font-weight: 700;
    color: rgba(255,255,255,0.8);
    letter-spacing: 0.03em;
  }
  .unidad-logo-label {
    font-size: 0.48rem;
    color: rgba(255,255,255,0.45);
    letter-spacing: 0.05em;
    text-transform: uppercase;
    max-width: 40px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  @media (max-width: 1200px) {
    .unidad-logo-item img { width: 26px; height: 26px; }
  }

  /* ═══════════════════════════════════════
     PANEL DERECHO — FORMULARIO
  ═══════════════════════════════════════ */
  .pulso-right {
    flex: 0 0 41.667%;
    max-width: 41.667%;
    background: var(--cream);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
  }

  /* Textura sutil fondo derecho */
  .pulso-right::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle at 80% 20%, rgba(0,48,135,0.04) 0%, transparent 60%),
                      radial-gradient(circle at 20% 80%, rgba(201,162,39,0.04) 0%, transparent 60%);
    pointer-events: none;
  }

  /* Acento línea izquierda del panel */
  .pulso-right::after {
    content: '';
    position: absolute;
    left: 0;
    top: 15%;
    height: 70%;
    width: 2px;
    background: linear-gradient(180deg, transparent, var(--gold), transparent);
    opacity: 0.5;
  }

  .form-container {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 400px;
  }

  /* Header del formulario */
  .form-header {
    margin-bottom: 2.5rem;
    text-align: center;
  }

  .form-logo-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    text-decoration: none;
  }

  .form-logo-img {
    width: 44px;
    height: 44px;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,48,135,0.15);
  }

  .form-logo-placeholder {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--gov-blue), var(--gov-mid));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(0,48,135,0.3);
    font-family: 'Playfair Display', serif;
  }

  .form-brand-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--gov-navy);
    letter-spacing: -0.02em;
  }

  /* Línea decorativa triple */
  .form-title-block {
    position: relative;
    margin-bottom: 0.5rem;
  }

  .form-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-dark);
    line-height: 1.2;
    margin: 0 0 0.4rem;
  }

  .form-title-accent {
    display: inline-block;
    width: 36px;
    height: 2px;
    background: var(--gold);
    border-radius: 2px;
    margin-right: 6px;
    vertical-align: middle;
    position: relative;
    top: -2px;
  }
  .form-title-accent::before {
    content: '';
    position: absolute;
    left: -10px;
    top: 0;
    width: 6px;
    height: 2px;
    background: var(--gold-light);
    border-radius: 2px;
  }

  .form-subtitle {
    font-size: 0.82rem;
    color: var(--text-muted);
    font-weight: 400;
    line-height: 1.5;
  }

  /* Badge institucional */
  .form-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(0,48,135,0.06);
    border: 1px solid rgba(0,48,135,0.12);
    border-radius: 50px;
    padding: 0.3rem 0.85rem;
    font-size: 0.7rem;
    color: var(--gov-blue);
    font-weight: 600;
    letter-spacing: 0.04em;
    margin-bottom: 1.5rem;
  }
  .form-badge-dot {
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
    animation: pulse-dot 2s ease-in-out infinite;
  }

  /* Alertas */
  .pulso-alert {
    border-radius: 10px;
    padding: 0.85rem 1rem;
    font-size: 0.82rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    border: 1px solid;
  }
  .pulso-alert.success {
    background: rgba(34,197,94,0.07);
    border-color: rgba(34,197,94,0.25);
    color: #166534;
  }
  .pulso-alert.danger {
    background: rgba(239,68,68,0.06);
    border-color: rgba(239,68,68,0.2);
    color: #991b1b;
  }

  /* Campos de formulario */
  .pulso-field {
    margin-bottom: 1.25rem;
  }

  .pulso-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-dark);
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
  }

  .pulso-input-wrap {
    position: relative;
  }

  .pulso-input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1rem;
    pointer-events: none;
    transition: color 0.2s;
    z-index: 1;
  }

  .pulso-input {
    width: 100%;
    height: 48px;
    padding: 0 1rem 0 2.75rem;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 0.9rem;
    font-family: 'DM Sans', sans-serif;
    color: var(--text-dark);
    background: var(--white);
    transition: all 0.2s ease;
    outline: none;
    appearance: none;
  }

  .pulso-input::placeholder { color: #b8c3cc; }

  .pulso-input:focus {
    border-color: var(--gov-blue);
    box-shadow: 0 0 0 3px rgba(0,48,135,0.1);
  }

  .pulso-input:focus + .pulso-input-icon-after,
  .pulso-input-wrap:focus-within .pulso-input-icon {
    color: var(--gov-blue);
  }

  .pulso-input.is-invalid {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.08);
  }

  .pulso-toggle-pw {
    position: absolute;
    right: 0.9rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    padding: 0.25rem;
    line-height: 1;
    transition: color 0.2s;
    z-index: 2;
  }
  .pulso-toggle-pw:hover { color: var(--gov-blue); }

  .pulso-invalid {
    font-size: 0.75rem;
    color: #ef4444;
    margin-top: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
  }

  /* Fila recordarme / olvidé */
  .form-row-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    margin-top: 0.25rem;
  }

  .pulso-check-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
  }

  .pulso-check {
    width: 16px;
    height: 16px;
    border: 1.5px solid var(--border);
    border-radius: 4px;
    appearance: none;
    cursor: pointer;
    position: relative;
    transition: all 0.15s;
    background: white;
    flex-shrink: 0;
  }

  .pulso-check:checked {
    background: var(--gov-blue);
    border-color: var(--gov-blue);
  }

  .pulso-check:checked::after {
    content: '';
    position: absolute;
    left: 3px;
    top: 0px;
    width: 5px;
    height: 9px;
    border: 2px solid white;
    border-top: none;
    border-left: none;
    transform: rotate(45deg);
  }

  .pulso-check-label {
    font-size: 0.82rem;
    color: var(--text-muted);
    cursor: pointer;
    user-select: none;
  }

  .pulso-forgot {
    font-size: 0.8rem;
    color: var(--gov-blue);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
  }
  .pulso-forgot:hover { color: var(--gold); }

  /* Botón principal */
  .btn-pulso-primary {
    width: 100%;
    height: 50px;
    background: linear-gradient(135deg, var(--gov-navy) 0%, var(--gov-blue) 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    box-shadow: 0 4px 20px rgba(0,48,135,0.35);
  }

  .btn-pulso-primary::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 0%, rgba(201,162,39,0.2) 100%);
    opacity: 0;
    transition: opacity 0.3s;
  }

  .btn-pulso-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 28px rgba(0,48,135,0.45);
  }
  .btn-pulso-primary:hover::before { opacity: 1; }
  .btn-pulso-primary:active { transform: translateY(0); }

  .btn-arrow {
    transition: transform 0.2s;
  }
  .btn-pulso-primary:hover .btn-arrow { transform: translateX(3px); }

  /* Separador */
  .form-sep {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 1.75rem 0;
  }
  .form-sep-line {
    flex: 1;
    height: 1px;
    background: var(--border);
  }
  .form-sep-text {
    font-size: 0.7rem;
    color: #94a3b8;
    font-weight: 500;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    white-space: nowrap;
  }

  /* Bloque institucional final */
  .form-inst-block {
    background: linear-gradient(135deg, rgba(0,48,135,0.04), rgba(201,162,39,0.04));
    border: 1px solid rgba(0,48,135,0.1);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .form-inst-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--gov-navy), var(--gov-blue));
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,48,135,0.25);
  }

  .form-inst-name {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-dark);
    line-height: 1.3;
    margin-bottom: 0.15rem;
  }

  .form-inst-location {
    font-size: 0.7rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 0.3rem;
  }

  /* ═══════════════════════════════════════
     ANIMACIONES
  ═══════════════════════════════════════ */
  @keyframes geo-pulse {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 1; }
  }

  @keyframes escudo-float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
  }

  @keyframes ring-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  @keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(0.8); }
  }

  @keyframes fade-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .form-container {
    animation: fade-up 0.6s ease both;
    animation-delay: 0.1s;
    opacity: 0;
  }

  .left-content {
    animation: fade-up 0.7s ease both;
  }

  /* ═══════════════════════════════════════
     RESPONSIVE
  ═══════════════════════════════════════ */
  @media (max-width: 1200px) {
    .pulso-left { flex: 0 0 50%; max-width: 50%; }
    .pulso-right { flex: 0 0 50%; max-width: 50%; }
    .escudo-wrapper { width: 110px; height: 110px; }
    .escudo-icon { width: 110px; height: 110px; }
    .escudo-icon img { width: 90px !important; height: 90px !important; }
  }

  @media (max-width: 900px) {
    .pulso-left { display: none; }
    .pulso-right {
      flex: 0 0 100%;
      max-width: 100%;
      padding: 2rem 1.5rem;
    }
    .pulso-right::after { display: none; }
  }

  @media (max-width: 480px) {
    .pulso-right { padding: 1.5rem 1.25rem; }
    .form-container { max-width: 100%; }
  }

  /* Fix: Bootstrap has-validation ::before overlay blocks input clicks */
  .input-group.has-validation::before {
    pointer-events: none !important;
  }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
<div class="pulso-login-wrapper">

  {{-- ═══════ PANEL IZQUIERDO ═══════ --}}
  <div class="pulso-left">
    <div class="left-pattern"></div>
    <div class="left-geo">
      <div class="geo-line"></div>
      <div class="geo-line"></div>
      <div class="geo-line"></div>
      <div class="geo-line"></div>
    </div>
    <div class="left-circle-top"></div>
    <div class="left-circle-bottom"></div>

    <div class="left-content">

      <!-- Escudo animado -->
      <div class="escudo-wrapper">
        <div class="escudo-ring"></div>
        <div class="escudo-icon">
          @if(!empty($ci?->logo_ruta))
            <img src="{{ Storage::url($ci->logo_ruta) }}" alt="logo"
                 style="width:108px;height:108px;object-fit:contain;border-radius:50%;">
          @else
            <span style="color:var(--gold-light);font-family:'Playfair Display',serif;font-weight:900;">
              {{ strtoupper(substr($ci?->sigla ?? $ci?->nombre_institucion ?? 'P', 0, 2)) }}
            </span>
          @endif
        </div>
      </div>

      <p class="left-pretitle">
        {{ $ci?->region ? "Región {$ci->region}" : 'República del Perú' }}
        &bull; Sistema de Gestión
      </p>

      @php
        $sigla = $ci?->sigla ?? '';
        $partes = $sigla ? explode(' ', $sigla, 2) : ['PULSO', 'UGEL'];
      @endphp
      <h1 class="left-title">
        {{ $partes[0] }}<br>
        <span>{{ $partes[1] ?? $ci?->nombre_institucion ?? 'UGEL' }}</span>
      </h1>

      <p class="left-subtitle">
        {{ $ci?->nombre_institucion ?? 'Plataforma unificada de monitoreo, control interno e integridad institucional' }}
        @if($ci?->distrito || $ci?->provincia)
          &mdash; {{ implode(', ', array_filter([$ci->distrito, $ci->provincia])) }}
        @endif
      </p>

      <div class="left-divider">
        <div class="left-divider-line"></div>
        <div class="left-divider-diamond"></div>
        <div class="left-divider-line"></div>
      </div>

      <!-- Stats institucionales -->
      <div class="left-stats">
        <div class="stat-item">
          <span class="stat-icon">🛡️</span>
          <div class="stat-value">SCI</div>
          <div class="stat-label">Control Interno</div>
        </div>
        <div class="stat-item">
          <span class="stat-icon">📅</span>
          <div class="stat-value">{{ $ci?->anio_gestion ?? date('Y') }}</div>
          <div class="stat-label">Gestión</div>
        </div>
        <div class="stat-item">
          <span class="stat-icon">⚖️</span>
          <div class="stat-value">INT</div>
          <div class="stat-label">Integridad</div>
        </div>
      </div>

    </div>

    <div class="left-footer">
      <p class="left-footer-text">
        @if($ci?->correo_institucional)
          {{ $ci->correo_institucional }} &bull;
        @endif
        @if($ci?->ugel_codigo)
          Código {{ $ci->ugel_codigo }} &bull;
        @endif
        {{ date('Y') }}
      </p>

      @if(!empty($instituciones) && $instituciones->count() > 0)
      <div class="unidades-strip">
        @foreach($instituciones as $inst)
          <a class="unidad-logo-item"
             title="{{ $inst->nombre }}"
             @if($inst->url_sitio) href="{{ $inst->url_sitio }}" target="_blank" rel="noopener noreferrer" @endif>
            @if($inst->logo_src)
              <img src="{{ $inst->logo_src }}" alt="{{ $inst->sigla ?? $inst->nombre }}">
            @else
              <div class="unidad-logo-initials">{{ strtoupper(substr($inst->sigla ?? $inst->nombre, 0, 2)) }}</div>
            @endif
            <span class="unidad-logo-label">{{ $inst->sigla ?? \Illuminate\Support\Str::limit($inst->nombre, 5, '') }}</span>
          </a>
        @endforeach
      </div>
      @endif
    </div>
  </div>

  {{-- ═══════ PANEL DERECHO ═══════ --}}
  <div class="pulso-right">
    <div class="form-container">

      <!-- Header -->
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

        <div style="text-align:center; margin-bottom:0.75rem;">
          <span class="form-badge">
            <span class="form-badge-dot"></span>
            Sistema en línea
          </span>
        </div>

        <div class="form-title-block">
          <h2 class="form-title">
            <span class="form-title-accent"></span>Iniciar Sesión
          </h2>
        </div>
        <p class="form-subtitle">
          Accede con tus credenciales
          @if($ci?->nombre_institucion) de {{ $ci->nombre_institucion }}@endif
        </p>
      </div>

      {{-- Alertas --}}
      @if (session('status'))
        <div class="pulso-alert success">
          <i class="ti tabler-circle-check" style="font-size:1rem;flex-shrink:0;margin-top:1px;"></i>
          <span>{{ session('status') }}</span>
        </div>
      @endif

      @if ($errors->any())
        <div class="pulso-alert danger">
          <i class="ti tabler-alert-circle" style="font-size:1rem;flex-shrink:0;margin-top:1px;"></i>
          <div>@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        </div>
      @endif

      {{-- Formulario --}}
      <form id="formAuthentication" action="{{ route('login') }}" method="POST" novalidate>
        @csrf

        {{-- Email --}}
        <div class="pulso-field form-control-validation">
          <label class="pulso-label" for="email">Correo electrónico</label>
          <div class="pulso-input-wrap">
            <i class="ti tabler-mail pulso-input-icon"></i>
            <input
              type="email"
              id="email"
              name="email"
              value="{{ old('email') }}"
              placeholder="{{ $ci?->correo_institucional ? 'ej: usuario@' . explode('@', $ci->correo_institucional)[1] : 'tu.correo@ugel.gob.pe' }}"
              autofocus
              autocomplete="email"
              class="pulso-input @error('email') is-invalid @enderror"
            />
          </div>
        </div>

        {{-- Contraseña --}}
        <div class="pulso-field form-password-toggle form-control-validation">
          <label class="pulso-label" for="password">Contraseña</label>
          <div class="input-group input-group-merge">
            <input
              type="password"
              id="password"
              name="password"
              placeholder="············"
              autocomplete="current-password"
              class="pulso-input @error('password') is-invalid @enderror"
              style="border-radius:10px 0 0 10px;border-left-width:1.5px;border-right:none;padding-left:1rem;"
            />
            <span class="input-group-text cursor-pointer" style="background:var(--white);border:1.5px solid var(--border);border-left:none;border-radius:0 10px 10px 0;padding:0 0.85rem;">
              <i class="icon-base ti tabler-eye-off"></i>
            </span>
          </div>
        </div>

        {{-- Recordarme / Olvidé --}}
        <div class="form-row-meta">
          <label class="pulso-check-wrap">
            <input type="checkbox" id="remember-me" name="remember" class="pulso-check">
            <span class="pulso-check-label">Recordarme</span>
          </label>
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="pulso-forgot">¿Olvidaste tu contraseña?</a>
          @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-pulso-primary">
          <i class="ti tabler-login" style="font-size:1rem;"></i>
          Ingresar al Sistema
          <i class="ti tabler-arrow-right btn-arrow" style="font-size:0.9rem;"></i>
        </button>

      </form>

      {{-- Registro --}}
      @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
        <div class="form-sep">
          <div class="form-sep-line"></div>
          <span class="form-sep-text">¿Nuevo usuario?</span>
          <div class="form-sep-line"></div>
        </div>
        <a href="{{ route('register') }}"
           style="display:flex;align-items:center;justify-content:center;gap:0.5rem;width:100%;height:46px;border:1.5px solid var(--border);border-radius:10px;text-decoration:none;color:var(--text-dark);font-size:0.875rem;font-weight:500;font-family:'DM Sans',sans-serif;transition:all 0.2s;background:white;"
           onmouseover="this.style.borderColor='var(--gov-blue)';this.style.color='var(--gov-blue)'"
           onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-dark)'">
          <i class="ti tabler-user-plus" style="font-size:1rem;"></i>
          Crear una cuenta
        </a>
      @endif

      {{-- Bloque institución --}}
      <div class="form-sep" style="margin-top:1.5rem;">
        <div class="form-sep-line"></div>
        <div class="form-sep-line"></div>
      </div>

      <div class="form-inst-block">
        <div class="form-inst-icon">
          <i class="ti tabler-shield-check" style="font-size:1rem;"></i>
        </div>
        <div>
          <div class="form-inst-name">
            {{ $ci?->nombre_institucion ?? 'Sistema de Monitoreo Institucional' }}
          </div>
          @if($ci?->director)
            <div class="form-inst-location">
              <i class="ti tabler-user-check" style="font-size:0.75rem;"></i>
              Dir.: {{ $ci->director }}
            </div>
          @elseif($ci?->distrito || $ci?->provincia)
            <div class="form-inst-location">
              <i class="ti tabler-map-pin" style="font-size:0.75rem;"></i>
              {{ implode(', ', array_filter([$ci?->distrito, $ci?->provincia, $ci?->departamento])) }}
            </div>
          @else
            <div class="form-inst-location">
              <i class="ti tabler-shield" style="font-size:0.75rem;"></i>
              Control Interno &bull; Integridad Institucional
            </div>
          @endif
        </div>
      </div>

    </div>
  </div>

</div>

@endsection
