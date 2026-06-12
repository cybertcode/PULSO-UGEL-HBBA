@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Ranking — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-style')
<style>
/* ── KPI Cards (mis-actividades style) ─────────────────────────────────── */
.kpi-card { border-radius: 14px; border: none; overflow: hidden; transition: transform .18s, box-shadow .18s; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.10); }
.kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1; }
.kpi-label { font-size: .72rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; opacity: .75; }
.kpi-sub   { font-size: .8rem; font-weight: 600; }

/* ── Ranking cards (act-card style) ────────────────────────────────────── */
.rank-card { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); transition: transform .18s, box-shadow .18s; overflow: hidden; }
.rank-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.09); }
.rank-card.is-success { border-left: 4px solid #28c76f; }
.rank-card.is-warning { border-left: 4px solid #ff9f43; }
.rank-card.is-danger  { border-left: 4px solid #ea5455; }
.rank-card.is-top1 { border: 2px solid rgba(255,193,7,.5); border-left: 4px solid #ffd700; }
.rank-card.is-top2 { border: 1px solid rgba(192,192,192,.4); border-left: 4px solid #c0c0c0; }
.rank-card.is-top3 { border: 1px solid rgba(205,127,50,.3); border-left: 4px solid #cd7f32; }

.rank-actions { display:flex; gap:.4rem; padding:.65rem 1rem; border-top:1px solid rgba(0,0,0,.05); background:rgba(0,0,0,.015); }
.rank-pct-big { font-size: 1.9rem; font-weight: 800; line-height: 1; }

/* ── Podio inline ──────────────────────────────────────────────────────── */
.podio-col {
  border-radius: 14px;
  padding: 1.25rem 1rem;
  text-align: center;
  transition: transform .18s;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
}
.podio-col:hover { transform: translateY(-3px); }
.podio-avatar {
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  color: #fff;
  margin: 0 auto .75rem;
  flex-shrink: 0;
}
/* Alturas fijas por posición para efecto escalonado */
.podio-slot-1 { min-height: 300px; }
.podio-slot-2 { min-height: 240px; }
.podio-slot-3 { min-height: 200px; }
/* Fila del podio: alineada al fondo para el efecto escalonado */
#podioRow { align-items: flex-end; justify-content: center; }

/* ── Vista tabs ────────────────────────────────────────────────────────── */
.vista-tabs .nav-link { border-radius: 10px; font-weight: 600; font-size: .82rem; padding: .42rem 1.1rem; color: var(--bs-secondary-color); transition: all .15s; }
.vista-tabs .nav-link.active[data-vista="unidades"] { background: #7367f0; color: #fff; }
.vista-tabs .nav-link.active[data-vista="usuarios"] { background: #00cfe8; color: #fff; }

/* ── Módulo tabs ────────────────────────────────────────────────────────── */
.modulo-tabs .nav-link { border-radius: 8px; font-size: .78rem; font-weight: 600; padding: .32rem .85rem; color: var(--bs-secondary-color); transition: all .15s; }
.modulo-tabs .nav-link.active[data-modulo="ambos"]      { background: #ff9f43; color: #fff; }
.modulo-tabs .nav-link.active[data-modulo="sci"]        { background: #7367f0; color: #fff; }
.modulo-tabs .nav-link.active[data-modulo="integridad"] { background: #28c76f; color: #fff; }

/* ── Tiempo real ────────────────────────────────────────────────────────── */
.rt-indicator { display:inline-flex; align-items:center; gap:5px; font-size:.72rem; color:var(--bs-secondary-color); }
.rt-dot { width:7px;height:7px;border-radius:50%;background:#28c76f;animation:rtPulse 2s infinite; }
.rt-dot.paused { background:#adb5bd;animation:none; }
@keyframes rtPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.7)} }

/* ── Skeleton ────────────────────────────────────────────────────────────── */
.skel { border-radius:6px;background:linear-gradient(90deg,var(--bs-tertiary-bg) 25%,var(--bs-secondary-bg) 50%,var(--bs-tertiary-bg) 75%);background-size:200% 100%;animation:skelAnim 1.4s infinite; }
@keyframes skelAnim { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* ── Misc ────────────────────────────────────────────────────────────────── */
.stat-block { border-radius:12px;padding:.85rem 1rem; }
.dias-chip { display:inline-flex;align-items:center;gap:.2rem;font-size:.72rem;font-weight:700;padding:.18em .55em;border-radius:20px; }
.chart-card .card-header { padding:.85rem 1.25rem; }
.rank-num-badge { width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;flex-shrink:0; }
.progress-thin { height:6px;border-radius:3px; }
</style>
@endsection

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home me-1" style="font-size:.85rem"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Ranking</li>
  </ol>
</nav>

{{-- ════ CABECERA ════ --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <h4 class="fw-bold mb-1"><i class="ti tabler-trophy me-2 text-warning"></i>Ranking de Cumplimiento</h4>
    <p class="text-muted mb-0">Competencia sana entre áreas y personas para fortalecer la integridad institucional.</p>
  </div>
  <div class="d-flex align-items-center gap-3 flex-wrap">
    <div class="rt-indicator">
      <div class="rt-dot" id="rtDot"></div>
      <span id="rtLabel">En vivo</span>
      <span class="text-muted" id="rtTimestamp" style="font-size:.68rem"></span>
    </div>
    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnPausa" style="font-size:.75rem">
      <i class="ti tabler-player-pause me-1"></i>Pausar
    </button>
  </div>
</div>

{{-- ════ BARRA DE CONTROL: VISTA + MÓDULO ════ --}}
<div class="card mb-4">
  <div class="card-body py-3 px-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

      {{-- Vista: Unidades / Usuarios --}}
      <div class="d-flex align-items-center gap-3">
        <span class="text-muted fw-semibold" style="font-size:.78rem">Ver ranking por:</span>
        <ul class="nav gap-2 vista-tabs mb-0" id="vistaTabs">
          <li class="nav-item">
            <a class="nav-link active" data-vista="unidades" href="#">
              <i class="ti tabler-building me-1"></i>Unidades
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-vista="usuarios" href="#">
              <i class="ti tabler-users me-1"></i>Usuarios
            </a>
          </li>
        </ul>
      </div>

      {{-- Módulo --}}
      <div class="d-flex align-items-center gap-3">
        <span class="text-muted fw-semibold" style="font-size:.78rem">Módulo:</span>
        <ul class="nav gap-1 modulo-tabs mb-0" id="moduloTabs">
          <li class="nav-item">
            <a class="nav-link active" data-modulo="ambos" href="#"><i class="ti tabler-layout-grid me-1"></i>Todos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-modulo="sci" href="#"><i class="ti tabler-shield-check me-1"></i>SCI</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-modulo="integridad" href="#"><i class="ti tabler-award me-1"></i>Integridad</a>
          </li>
        </ul>
        <span class="badge rounded-pill px-3" id="moduloBadge" style="background:#ff9f43;color:#fff;font-size:.78rem">Ambos</span>
      </div>

    </div>
  </div>
</div>

{{-- ════ KPI CARDS ════ --}}
<div class="row g-3 mb-4" id="kpiZone">
  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div><div class="kpi-label text-white-50" id="kpiLabelTotal">Unidades</div>
          <div class="kpi-value" id="kpiTotal">—</div></div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)"><i class="ti tabler-layout-grid"></i></div>
        </div>
        <div class="kpi-sub text-white-75" id="kpiSubTotal">Total en ranking</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div><div class="kpi-label text-white-50">Cumplieron</div>
          <div class="kpi-value" id="kpiCumplieron">—</div></div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)"><i class="ti tabler-circle-check"></i></div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <div class="progress flex-grow-1" style="height:4px;background:rgba(255,255,255,.25)">
            <div class="progress-bar bg-white" id="kpiBarPct" style="width:0%"></div>
          </div>
          <span class="kpi-sub text-white-75" id="kpiPct">0%</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div><div class="kpi-label text-white-50">Promedio</div>
          <div class="kpi-value" id="kpiPromedio">—</div></div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)"><i class="ti tabler-chart-bar"></i></div>
        </div>
        <div class="kpi-sub text-white-75">% cumplimiento promedio</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#cb2d3e 0%,#ef473a 100%)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div><div class="kpi-label text-white-50">En Riesgo</div>
          <div class="kpi-value" id="kpiRiesgo">—</div></div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)"><i class="ti tabler-alert-triangle"></i></div>
        </div>
        <div class="kpi-sub text-white-75" id="kpiSubRiesgo">Requieren atención</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div><div class="kpi-label text-white-50">Críticas</div>
          <div class="kpi-value" id="kpiCriticas">—</div></div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)"><i class="ti tabler-flame"></i></div>
        </div>
        <div class="kpi-sub text-white-75">Avance muy bajo</div>
      </div>
    </div>
  </div>
</div>

{{-- ════ ZONA DINÁMICA ════ --}}
<div id="rankingContent">
  {{-- Skeleton --}}
  <div id="skeletonZone">
    <div class="card mb-4"><div class="card-body" style="height:180px">
      <div class="skel rounded mb-3" style="height:14px;width:30%"></div>
      <div class="row g-3">
        <div class="col-4"><div class="skel rounded-3" style="height:120px"></div></div>
        <div class="col-4"><div class="skel rounded-3" style="height:120px"></div></div>
        <div class="col-4"><div class="skel rounded-3" style="height:120px"></div></div>
      </div>
    </div></div>
    <div class="row g-3">
      @for($i=0;$i<6;$i++)
      <div class="col-md-6 col-xl-4">
        <div class="skel rounded-3" style="height:130px"></div>
      </div>
      @endfor
    </div>
  </div>

  {{-- Contenido real --}}
  <div id="rankingData" style="display:none">

    {{-- PODIO TOP 3 --}}
    <div class="card mb-4">
      <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
        <div>
          <h6 class="fw-bold mb-0" id="podioTitulo"><i class="ti tabler-podium me-2 text-warning"></i>Podio — Top 3</h6>
          <p class="card-subtitle mb-0 mt-1" id="podioSub" style="font-size:.78rem">Las tres unidades líderes del período</p>
        </div>
        <span class="badge bg-label-warning rounded-pill px-3" style="font-size:.78rem">{{ now()->format('F Y') }}</span>
      </div>
      <div class="card-body pb-3">
        <div class="row g-3 align-items-end justify-content-center" id="podioRow">
          {{-- Rellenado por JS --}}
        </div>
      </div>
    </div>

    {{-- GRÁFICO --}}
    <div class="card mb-4 chart-card">
      <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
        <div>
          <h6 class="fw-bold mb-0" id="chartTitulo">Avance por Unidad Orgánica</h6>
          <p class="card-subtitle mb-0 mt-1" style="font-size:.78rem">Porcentaje de cumplimiento · {{ now()->year }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <div class="d-flex align-items-center gap-1" style="font-size:11px;color:var(--bs-secondary-color)">
            <span style="width:18px;height:2px;background:#28c76f;display:inline-block;border-radius:2px;border-top:2px dashed #28c76f"></span> Umbral verde
          </div>
          <span class="badge bg-label-primary rounded-pill px-3" style="font-size:.78rem">{{ now()->year }}</span>
        </div>
      </div>
      <div class="card-body pt-3">
        <div id="chartRanking"></div>
      </div>
    </div>

    {{-- ── UNIDADES: Grid de cards ── --}}
    <div id="seccionUnidades">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0">Clasificación Completa</h6>
        <small class="text-muted" id="contadorUnidades"></small>
      </div>
      <div class="row g-3" id="gridUnidades">
        {{-- Rellenado por JS --}}
      </div>
    </div>

    {{-- ── USUARIOS: Grid de cards ── --}}
    <div id="seccionUsuarios" style="display:none">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0">Ranking por Personas</h6>
        <small class="text-muted" id="contadorUsuarios"></small>
      </div>
      <div class="row g-3" id="gridUsuarios">
        {{-- Rellenado por JS --}}
      </div>
    </div>

    {{-- Panel lateral: estadísticas y distribución --}}
    <div class="row g-4 mt-1">
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header border-bottom py-3">
            <h6 class="fw-bold mb-0">Distribución de Semáforo</h6>
          </div>
          <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3 stat-block" style="background:rgba(40,199,111,.08);border:1px solid rgba(40,199,111,.2)">
              <div class="badge rounded bg-label-success p-2 flex-shrink-0"><i class="ti tabler-circle-check icon-20px text-success"></i></div>
              <div>
                <div class="fw-semibold" style="font-size:13px"><span id="statCumplieron">—</span> cumplieron</div>
                <div class="text-muted" style="font-size:11px">Avance ≥ umbral verde</div>
              </div>
            </div>
            <div class="d-flex align-items-center gap-3 mb-3 stat-block" style="background:rgba(255,159,67,.08);border:1px solid rgba(255,159,67,.2)">
              <div class="badge rounded bg-label-warning p-2 flex-shrink-0"><i class="ti tabler-alert-circle icon-20px text-warning"></i></div>
              <div>
                <div class="fw-semibold" style="font-size:13px"><span id="statRiesgo">—</span> en proceso</div>
                <div class="text-muted" style="font-size:11px">Requieren atención</div>
              </div>
            </div>
            <div class="d-flex align-items-center gap-3 mb-3 stat-block" style="background:rgba(234,84,85,.08);border:1px solid rgba(234,84,85,.2)">
              <div class="badge rounded bg-label-danger p-2 flex-shrink-0"><i class="ti tabler-flame icon-20px text-danger"></i></div>
              <div>
                <div class="fw-semibold" style="font-size:13px"><span id="statCriticas">—</span> críticas</div>
                <div class="text-muted" style="font-size:11px">Avance muy bajo</div>
              </div>
            </div>
            <div class="d-flex rounded-pill overflow-hidden mt-3" style="height:8px;gap:2px" id="statBar"></div>
            <div class="d-flex justify-content-between mt-2">
              <small class="text-success fw-semibold"><span id="barCumplieron">0</span> cumplen</small>
              <small class="text-warning fw-semibold"><span id="barRiesgo">0</span> en proceso</small>
              <small class="text-danger fw-semibold"><span id="barCriticas">0</span> críticas</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header border-bottom py-3">
            <h6 class="fw-bold mb-0">Estadísticas del Período</h6>
          </div>
          <div class="card-body">
            <div class="row g-4 text-center mb-4">
              <div class="col-4">
                <h3 class="fw-bold text-primary mb-0" id="statPromedio">—</h3>
                <small class="text-muted">Promedio</small>
              </div>
              <div class="col-4">
                <h3 class="fw-bold text-success mb-0" id="statMax">—</h3>
                <small class="text-muted">Máximo</small>
              </div>
              <div class="col-4">
                <h3 class="fw-bold text-danger mb-0" id="statMin">—</h3>
                <small class="text-muted">Mínimo</small>
              </div>
            </div>
            <a href="{{ route('rep-reconocimientos') }}" class="btn btn-primary w-100">
              <i class="ti tabler-trophy me-2"></i>Nuevo reconocimiento
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- #rankingData --}}
</div>{{-- #rankingContent --}}

@endsection

@section('page-script')
<script>
(function () {
  'use strict';

  // ── Estado ────────────────────────────────────────────────────────────────
  let vistaActual  = 'unidades';
  let moduloActual = 'ambos';
  let chart        = null;
  let paused       = false;
  let primeraCarga = true;

  const INTERVALO_MS   = 30000;
  const URL_UNIDADES   = '{{ route("mon-ranking-unidades.data") }}';
  const URL_USUARIOS   = '{{ route("mon-ranking-unidades.usuarios") }}';

  const moduloMeta = {
    ambos:      { text: 'Ambos',      color: '#ff9f43' },
    sci:        { text: 'SCI',        color: '#7367f0' },
    integridad: { text: 'Integridad', color: '#28c76f' },
  };

  // ── DOM refs ──────────────────────────────────────────────────────────────
  const $ = id => document.getElementById(id);

  // ── Vista tabs ────────────────────────────────────────────────────────────
  document.querySelectorAll('#vistaTabs .nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      document.querySelectorAll('#vistaTabs .nav-link').forEach(l => l.classList.remove('active'));
      this.classList.add('active');
      vistaActual = this.dataset.vista;
      cargar();
    });
  });

  // ── Módulo tabs ────────────────────────────────────────────────────────────
  document.querySelectorAll('#moduloTabs .nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      document.querySelectorAll('#moduloTabs .nav-link').forEach(l => l.classList.remove('active'));
      this.classList.add('active');
      moduloActual = this.dataset.modulo;
      const meta = moduloMeta[moduloActual];
      $('moduloBadge').textContent = meta.text;
      $('moduloBadge').style.background = meta.color;
      cargar();
    });
  });

  // ── Pausa ──────────────────────────────────────────────────────────────────
  $('btnPausa').addEventListener('click', () => {
    paused = !paused;
    $('rtDot').classList.toggle('paused', paused);
    $('rtLabel').textContent = paused ? 'Pausado' : 'En vivo';
    $('btnPausa').innerHTML = paused
      ? '<i class="ti tabler-player-play me-1"></i>Reanudar'
      : '<i class="ti tabler-player-pause me-1"></i>Pausar';
    if (!paused) cargar();
  });

  // ── Carga ─────────────────────────────────────────────────────────────────
  async function cargar() {
    if (paused) return;
    const url = vistaActual === 'unidades' ? URL_UNIDADES : URL_USUARIOS;
    try {
      const res  = await fetch(`${url}?modulo=${moduloActual}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = await res.json();

      if (primeraCarga) {
        $('skeletonZone').style.display = 'none';
        $('rankingData').style.display  = 'block';
        primeraCarga = false;
      }

      renderKpis(json, vistaActual);
      renderResumen(json.resumen);
      renderChart(json.chart, json.umbral_verde, json.umbral_amarillo);

      if (vistaActual === 'unidades') {
        $('seccionUnidades').style.display = '';
        $('seccionUsuarios').style.display = 'none';
        renderPodioUnidades(json.unidades);
        renderGridUnidades(json.unidades);
        $('podioTitulo').innerHTML = '<i class="ti tabler-podium me-2 text-warning"></i>Podio — Top 3 Unidades';
        $('podioSub').textContent  = 'Las tres unidades con mayor cumplimiento';
        $('chartTitulo').textContent = 'Avance por Unidad Orgánica';
      } else {
        $('seccionUnidades').style.display = 'none';
        $('seccionUsuarios').style.display = '';
        renderPodioUsuarios(json.usuarios);
        renderGridUsuarios(json.usuarios);
        $('podioTitulo').innerHTML = '<i class="ti tabler-podium me-2 text-info"></i>Podio — Top 3 Usuarios';
        $('podioSub').textContent  = 'Las tres personas con mayor cumplimiento';
        $('chartTitulo').textContent = 'Avance por Persona';
      }

      const ts = new Date(json.timestamp);
      $('rtTimestamp').textContent = ts.toLocaleTimeString('es-PE', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    } catch (err) {
      console.warn('[Ranking] Error:', err);
    }
  }

  // ── KPIs ──────────────────────────────────────────────────────────────────
  function renderKpis(json, vista) {
    const r = json.resumen;
    const items = vista === 'unidades' ? json.unidades : json.usuarios;
    const total = items?.length ?? 0;
    $('kpiTotal').textContent      = total;
    $('kpiLabelTotal').textContent = vista === 'unidades' ? 'Unidades' : 'Usuarios';
    $('kpiSubTotal').textContent   = vista === 'unidades' ? 'En el ranking actual' : 'Personas rankeadas';
    $('kpiCumplieron').textContent = r.cumplieron;
    $('kpiRiesgo').textContent     = r.en_riesgo;
    $('kpiCriticas').textContent   = r.criticas;
    $('kpiPromedio').textContent   = r.promedio + '%';
    $('kpiPct').textContent        = (total > 0 ? Math.round((r.cumplieron / total) * 100) : 0) + '%';
    $('kpiBarPct').style.width     = (total > 0 ? Math.round((r.cumplieron / total) * 100) : 0) + '%';
  }

  // ── Resumen lateral ───────────────────────────────────────────────────────
  function renderResumen(r) {
    $('statCumplieron').textContent = r.cumplieron;
    $('statRiesgo').textContent     = r.en_riesgo;
    $('statCriticas').textContent   = r.criticas;
    $('statPromedio').textContent   = r.promedio + '%';
    $('statMax').textContent        = r.maximo + '%';
    $('statMin').textContent        = r.minimo + '%';
    $('barCumplieron').textContent  = r.cumplieron;
    $('barRiesgo').textContent      = r.en_riesgo;
    $('barCriticas').textContent    = r.criticas;

    const total = r.cumplieron + r.en_riesgo + r.criticas;
    const bar   = $('statBar');
    bar.innerHTML = '';
    if (total > 0) {
      [ [r.cumplieron,'#28c76f','99px 0 0 99px'],
        [r.en_riesgo, '#ff9f43','0'],
        [r.criticas,  '#ea5455','0 99px 99px 0'] ]
        .filter(s => s[0] > 0)
        .forEach(([flex, bg, r]) => {
          const d = document.createElement('div');
          d.style.cssText = `flex:${flex};background:${bg};border-radius:${r}`;
          bar.appendChild(d);
        });
    }
  }

  // ── Gráfico ───────────────────────────────────────────────────────────────
  function renderChart(ch, uVerde, uAmarillo) {
    const isDark    = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const textColor = isDark ? '#b4bdc6' : '#697a8d';
    const gridColor = isDark ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.04)';
    const bgTheme   = isDark ? '#2b2c40' : '#fff';

    const opts = {
      chart: { type:'bar', height:260, toolbar:{ show:false },
               animations:{ enabled:!chart, easing:'easeinout', speed:600 } },
      series: [{ name:'% Cumplimiento', data:ch.data }],
      xaxis: { categories:ch.labels, labels:{ style:{ colors:textColor, fontSize:'11px' } }, axisBorder:{ show:false }, axisTicks:{ show:false } },
      yaxis: { max:100, min:0, labels:{ formatter:v=>v+'%', style:{ colors:textColor, fontSize:'11px' } } },
      colors: ch.colors,
      dataLabels: { enabled:true, formatter:v=>v+'%', offsetY:-20, style:{ fontSize:'10px', fontWeight:700, colors:[textColor] } },
      plotOptions: { bar:{ borderRadius:8, distributed:true, columnWidth:'48%', dataLabels:{ position:'top' } } },
      annotations: { yaxis: [
        { y:uVerde, borderColor:'#28c76f', borderWidth:2, strokeDashArray:5,
          label:{ text:`Verde ≥${uVerde}%`, position:'right', style:{ color:'#28c76f', background:bgTheme, fontSize:'10px', fontWeight:700 } } },
        { y:uAmarillo, borderColor:'#ff9f43', borderWidth:1, strokeDashArray:5,
          label:{ text:`${uAmarillo}%`, position:'right', style:{ color:'#ff9f43', background:bgTheme, fontSize:'10px', fontWeight:700 } } },
      ]},
      legend:{ show:false },
      grid:{ borderColor:gridColor, strokeDashArray:5 },
      tooltip:{ theme:isDark?'dark':'light', y:{ formatter:v=>v+'% cumplimiento' } },
    };

    if (chart) { chart.updateOptions(opts, true, true); }
    else { chart = new ApexCharts(document.getElementById('chartRanking'), opts); chart.render(); }
  }

  // ── Podio Unidades ────────────────────────────────────────────────────────
  function renderPodioUnidades(unidades) {
    // Orden visual: 2° izq, 1° centro, 3° der
    const top = [unidades[1], unidades[0], unidades[2]];
    const cfg = [
      { pos:2, slotClass:'podio-slot-2', colClass:'col-5 col-sm-4 col-md-3',
        bg:'linear-gradient(160deg,rgba(192,192,192,.18),rgba(192,192,192,.05))', bd:'1px solid rgba(192,192,192,.35)',
        medBg:'linear-gradient(135deg,#b8b8b8,#e0e0e0)', medColor:'#555',
        avBg:'linear-gradient(145deg,#c0c0c0,#989898)', avSize:'52px', avFs:'18px',
        pctColor:'#909090', pctSize:'1.5rem', crown:'', nameFs:'13px' },
      { pos:1, slotClass:'podio-slot-1', colClass:'col-10 col-sm-5 col-md-4',
        bg:'linear-gradient(160deg,rgba(255,193,7,.22),rgba(255,193,7,.06))', bd:'2px solid rgba(255,193,7,.45)',
        medBg:'linear-gradient(135deg,#ffd700,#ff9800)', medColor:'#fff',
        avBg:'linear-gradient(145deg,#ffd700,#ff9800)', avSize:'68px', avFs:'24px',
        pctColor:'#ff9800', pctSize:'2.1rem', crown:'👑', nameFs:'15px' },
      { pos:3, slotClass:'podio-slot-3', colClass:'col-5 col-sm-4 col-md-3',
        bg:'linear-gradient(160deg,rgba(205,127,50,.18),rgba(205,127,50,.05))', bd:'1px solid rgba(205,127,50,.3)',
        medBg:'linear-gradient(135deg,#cd7f32,#e09050)', medColor:'#fff',
        avBg:'linear-gradient(145deg,#d4833a,#b8692a)', avSize:'48px', avFs:'16px',
        pctColor:'#cd7f32', pctSize:'1.4rem', crown:'', nameFs:'12px' },
    ];
    const row = $('podioRow');
    row.innerHTML = '';

    top.forEach((u, i) => {
      if (!u) return;
      const c = cfg[i];
      const div = document.createElement('div');
      div.className = c.colClass;
      div.innerHTML = `
        <div class="podio-col ${c.slotClass}" style="background:${c.bg};border:${c.bd}">
          ${c.crown ? `<div style="font-size:24px;line-height:1;margin-bottom:6px;filter:drop-shadow(0 3px 8px rgba(255,193,7,.7))">${c.crown}</div>` : '<div style="height:30px"></div>'}
          <div class="rank-num-badge mb-3" style="width:34px;height:34px;font-size:${c.pos===1?'16':'13'}px;background:${c.medBg};color:${c.medColor}">${c.pos}</div>
          <div class="podio-avatar mb-2" style="width:${c.avSize};height:${c.avSize};font-size:${c.avFs};background:${c.avBg}">
            ${esc(u.sigla.substring(0,2).toUpperCase())}
          </div>
          <div class="fw-bold mb-0" style="font-size:${c.nameFs};line-height:1.2">${esc(u.sigla)}</div>
          <div class="text-muted mb-3" style="font-size:9px;line-height:1.3;max-width:120px">${esc(truncate(u.nombre, 24))}</div>
          <div class="fw-bold mb-1" style="font-size:${c.pctSize};color:${c.pctColor};line-height:1">${u.porcentaje}%</div>
          <small class="text-muted d-block mb-2" style="font-size:9px">${u.completadas_count}/${u.actividades_count} actividades</small>
          <span class="badge bg-label-${u.color} rounded-pill mb-2" style="font-size:9px">${esc(u.semaforo)}</span>
          <div>${varBadge(u.variacion)}</div>
        </div>`;
      row.appendChild(div);
    });
  }

  // ── Podio Usuarios ────────────────────────────────────────────────────────
  function renderPodioUsuarios(usuarios) {
    const top = [usuarios[1], usuarios[0], usuarios[2]];
    const cfg = [
      { pos:2, slotClass:'podio-slot-2', colClass:'col-5 col-sm-4 col-md-3',
        bg:'linear-gradient(160deg,rgba(192,192,192,.18),rgba(192,192,192,.05))', bd:'1px solid rgba(192,192,192,.35)',
        medBg:'linear-gradient(135deg,#b8b8b8,#e0e0e0)', medColor:'#555',
        avBg:'linear-gradient(145deg,#c0c0c0,#989898)', avSize:'52px', avFs:'18px',
        pctColor:'#909090', pctSize:'1.5rem', crown:'', nameFs:'12px' },
      { pos:1, slotClass:'podio-slot-1', colClass:'col-10 col-sm-5 col-md-4',
        bg:'linear-gradient(160deg,rgba(0,207,232,.18),rgba(0,207,232,.05))', bd:'2px solid rgba(0,207,232,.4)',
        medBg:'linear-gradient(135deg,#00cfe8,#0099b8)', medColor:'#fff',
        avBg:'linear-gradient(145deg,#00cfe8,#0099b8)', avSize:'68px', avFs:'24px',
        pctColor:'#00b8d4', pctSize:'2.1rem', crown:'🥇', nameFs:'14px' },
      { pos:3, slotClass:'podio-slot-3', colClass:'col-5 col-sm-4 col-md-3',
        bg:'linear-gradient(160deg,rgba(205,127,50,.18),rgba(205,127,50,.05))', bd:'1px solid rgba(205,127,50,.3)',
        medBg:'linear-gradient(135deg,#cd7f32,#e09050)', medColor:'#fff',
        avBg:'linear-gradient(145deg,#d4833a,#b8692a)', avSize:'48px', avFs:'16px',
        pctColor:'#cd7f32', pctSize:'1.4rem', crown:'', nameFs:'11px' },
    ];
    const row = $('podioRow');
    row.innerHTML = '';

    top.forEach((u, i) => {
      if (!u) return;
      const c = cfg[i];
      const div = document.createElement('div');
      div.className = c.colClass;
      div.innerHTML = `
        <div class="podio-col ${c.slotClass}" style="background:${c.bg};border:${c.bd}">
          ${c.crown ? `<div style="font-size:24px;line-height:1;margin-bottom:6px">${c.crown}</div>` : '<div style="height:30px"></div>'}
          <div class="rank-num-badge mb-3" style="width:34px;height:34px;font-size:${c.pos===1?'16':'13'}px;background:${c.medBg};color:${c.medColor}">${c.pos}</div>
          <div class="podio-avatar mb-2" style="width:${c.avSize};height:${c.avSize};font-size:${c.avFs};background:${c.avBg}">
            ${esc(u.inicial)}
          </div>
          <div class="fw-bold mb-0" style="font-size:${c.nameFs};line-height:1.2">${esc(truncate(u.name, 20))}</div>
          <div class="text-muted mb-1" style="font-size:9px;line-height:1.3;max-width:120px">${u.cargo ? esc(truncate(u.cargo, 22)) : '&nbsp;'}</div>
          ${u.unidad ? `<span class="badge bg-label-secondary rounded-pill mb-2" style="font-size:9px">${esc(u.unidad)}</span>` : '<div class="mb-2"></div>'}
          <div class="fw-bold mb-1" style="font-size:${c.pctSize};color:${c.pctColor};line-height:1">${u.porcentaje}%</div>
          <small class="text-muted d-block mb-2" style="font-size:9px">${u.completadas_count}/${u.actividades_count} actividades</small>
          <span class="badge bg-label-${u.color} rounded-pill" style="font-size:9px">${esc(u.semaforo)}</span>
        </div>`;
      row.appendChild(div);
    });
  }

  // ── Grid Unidades ──────────────────────────────────────────────────────────
  function renderGridUnidades(unidades) {
    $('contadorUnidades').textContent = unidades.length + ' unidades';
    const grid = $('gridUnidades');
    grid.innerHTML = '';
    unidades.forEach((u, i) => {
      const pos = i + 1;
      const topClass = pos === 1 ? 'is-top1' : pos === 2 ? 'is-top2' : pos === 3 ? 'is-top3' : `is-${u.color}`;
      const numBg = pos === 1 ? 'linear-gradient(135deg,#ffd700,#ff9800)' :
                    pos === 2 ? 'linear-gradient(135deg,#c0c0c0,#e0e0e0)' :
                    pos === 3 ? 'linear-gradient(135deg,#cd7f32,#e09050)' : 'var(--bs-tertiary-bg)';
      const numColor = pos === 2 ? '#555' : pos <= 3 ? '#fff' : 'var(--bs-secondary-color)';

      const div = document.createElement('div');
      div.className = 'col-md-6 col-xl-4';
      div.innerHTML = `
        <div class="rank-card ${topClass}">
          <div class="p-3 pb-2">

            <div class="d-flex align-items-center gap-3 mb-2">
              <div class="rank-num-badge flex-shrink-0" style="background:${numBg};color:${numColor}">${pos}</div>
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center gap-2">
                  <span class="fw-bold text-truncate" style="font-size:14px">${esc(u.sigla)}</span>
                  <span class="badge bg-label-${u.color} rounded-pill ms-auto flex-shrink-0" style="font-size:.68rem">${esc(u.semaforo)}</span>
                </div>
                <div class="text-muted text-truncate" style="font-size:.73rem">${esc(u.nombre)}</div>
              </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-2">
              <div class="progress flex-grow-1 progress-thin" style="background:rgba(0,0,0,.06)">
                <div class="progress-bar rounded-pill" style="width:${u.porcentaje}%;background:${u.color_hex}"></div>
              </div>
              <span class="fw-bold flex-shrink-0" style="font-size:13px;color:${u.color_hex};min-width:36px;text-align:right">${u.porcentaje}%</span>
            </div>

            <div class="d-flex align-items-center gap-2">
              <small class="text-muted">
                <i class="ti tabler-circle-check me-1" style="font-size:.7rem;color:#28c76f"></i>${u.completadas_count}/${u.actividades_count}
              </small>
              ${u.vencidas_count > 0 ? `<small class="text-danger"><i class="ti tabler-alarm-off me-1" style="font-size:.7rem"></i>${u.vencidas_count} venc.</small>` : ''}
              <span class="ms-auto">${varBadge(u.variacion)}</span>
            </div>

          </div>
        </div>`;
      grid.appendChild(div);
    });
  }

  // ── Grid Usuarios ──────────────────────────────────────────────────────────
  function renderGridUsuarios(usuarios) {
    $('contadorUsuarios').textContent = usuarios.length + ' personas';
    const grid = $('gridUsuarios');
    grid.innerHTML = '';
    usuarios.forEach((u, i) => {
      const pos = i + 1;
      const topClass = pos === 1 ? 'is-top1' : pos === 2 ? 'is-top2' : pos === 3 ? 'is-top3' : `is-${u.color}`;
      const numBg = pos === 1 ? 'linear-gradient(135deg,#00cfe8,#0099b8)' :
                    pos === 2 ? 'linear-gradient(135deg,#c0c0c0,#e0e0e0)' :
                    pos === 3 ? 'linear-gradient(135deg,#cd7f32,#e09050)' : 'var(--bs-tertiary-bg)';
      const numColor = pos === 2 ? '#555' : pos <= 3 ? '#fff' : 'var(--bs-secondary-color)';

      const div = document.createElement('div');
      div.className = 'col-md-6 col-xl-4';
      div.innerHTML = `
        <div class="rank-card ${topClass}">
          <div class="p-3 pb-2">

            <div class="d-flex align-items-center gap-2 mb-2">
              <div class="rank-num-badge flex-shrink-0" style="background:${numBg};color:${numColor}">${pos}</div>
              <div class="avatar avatar-sm flex-shrink-0">
                <span class="avatar-initial rounded-circle bg-label-${u.color}" style="font-size:12px;font-weight:800">${esc(u.inicial)}</span>
              </div>
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center gap-1">
                  <span class="fw-bold text-truncate" style="font-size:13px">${esc(u.name)}</span>
                  <span class="badge bg-label-${u.color} rounded-pill ms-auto flex-shrink-0" style="font-size:.68rem">${esc(u.semaforo)}</span>
                </div>
                <div class="d-flex align-items-center gap-1 text-muted" style="font-size:.7rem">
                  <span class="text-truncate">${u.cargo ? esc(truncate(u.cargo, 20)) : '—'}</span>
                  ${u.unidad ? `<span class="badge bg-label-secondary rounded-pill flex-shrink-0" style="font-size:8px">${esc(u.unidad)}</span>` : ''}
                </div>
              </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-2">
              <div class="progress flex-grow-1 progress-thin" style="background:rgba(0,0,0,.06)">
                <div class="progress-bar rounded-pill" style="width:${u.porcentaje}%;background:${u.color_hex}"></div>
              </div>
              <span class="fw-bold flex-shrink-0" style="font-size:13px;color:${u.color_hex};min-width:36px;text-align:right">${u.porcentaje}%</span>
            </div>

            <div class="d-flex align-items-center gap-2">
              <small class="text-muted">
                <i class="ti tabler-circle-check me-1" style="font-size:.7rem;color:#28c76f"></i>${u.completadas_count}/${u.actividades_count}
              </small>
              ${u.vencidas_count > 0 ? `<small class="text-danger"><i class="ti tabler-alarm-off me-1" style="font-size:.7rem"></i>${u.vencidas_count} venc.</small>` : ''}
              ${u.en_proceso_count > 0 ? `<small class="text-warning ms-auto"><i class="ti tabler-loader-2 me-1" style="font-size:.7rem"></i>${u.en_proceso_count} en proc.</small>` : ''}
            </div>

          </div>
        </div>`;
      grid.appendChild(div);
    });
  }

  // ── Helpers ───────────────────────────────────────────────────────────────
  function varBadge(v) {
    if (v > 0)  return `<small class="text-success fw-bold"><i class="ti tabler-arrow-up" style="font-size:10px"></i>+${v}</small>`;
    if (v < 0)  return `<small class="text-danger fw-bold"><i class="ti tabler-arrow-down" style="font-size:10px"></i>${v}</small>`;
    return `<small class="text-muted"><i class="ti tabler-minus" style="font-size:10px"></i></small>`;
  }

  function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c =>
      ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  function truncate(s, n) { return s && s.length > n ? s.substring(0, n) + '…' : s; }

  // ── Arranque ──────────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    cargar();
    setInterval(() => { if (!paused) cargar(); }, INTERVALO_MS);
  });

})();
</script>
@endsection
