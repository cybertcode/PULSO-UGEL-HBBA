@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Avance por Unidades — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-style')
<style>
/* ── Módulo tabs principales ── */
.mod-tab-btn {
  display: flex; align-items: center; gap: .5rem;
  padding: .55rem 1.4rem; border-radius: 10px; border: 1.5px solid transparent;
  font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .18s;
  background: transparent; white-space: nowrap;
}
.mod-tab-btn.active-sci  { background: rgba(105,108,255,.1); border-color: #696cff; color: #696cff; }
.mod-tab-btn.active-int  { background: rgba(40,199,111,.1);  border-color: #28c76f; color: #28c76f; }
.mod-tab-btn.active-gen  { background: rgba(108,117,125,.1); border-color: #6c757d; color: #6c757d; }
.mod-tab-btn:not(.active-sci):not(.active-int):not(.active-gen) {
  color: #6e6b7b; border-color: rgba(0,0,0,.1);
}
.mod-tab-btn:hover:not(.active-sci):not(.active-int):not(.active-gen) {
  background: rgba(0,0,0,.04);
}

/* ── KPI cards ── */
.kpi-card { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); overflow:hidden; transition: box-shadow .18s; }
.kpi-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.09); }
.kpi-bar { height: 3px; }

/* ── Filtros inline ── */
.filter-bar { background: var(--bs-body-bg); border: 1px solid rgba(0,0,0,.07); border-radius: 12px; }
.filter-bar input, .filter-bar select {
  font-size: .8rem; border-radius: 8px; height: 34px; padding: .3rem .75rem;
  border: 1px solid rgba(0,0,0,.1); background: var(--bs-body-bg); color: var(--bs-body-color);
  transition: border-color .15s;
}
.filter-bar input:focus, .filter-bar select:focus { outline: none; border-color: #696cff; box-shadow: 0 0 0 3px rgba(105,108,255,.15); }

/* ── Tabla avance ── */
.tbl-avance td, .tbl-avance th { padding: .45rem .8rem !important; font-size: .835rem; vertical-align: middle; }
.tbl-avance thead th {
  font-size: .68rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
  color: #a5a3ae; background: rgba(0,0,0,.02); border-bottom: 1px solid rgba(0,0,0,.06) !important;
  white-space: nowrap;
}
.tbl-avance tbody tr { transition: background .12s; }
.tbl-avance tbody tr:hover { background: rgba(105,108,255,.035) !important; }
.tbl-avance tbody tr[data-hidden] { display: none !important; }

/* ── Avatar sigla ── */
.sigla-av { width: 34px; height: 34px; border-radius: 50%; display:flex; align-items:center; justify-content:center; font-size: 11px; font-weight: 800; flex-shrink: 0; }

/* ── Progress mini ── */
.prog-mini { height: 5px; border-radius: 99px; overflow: hidden; background: rgba(0,0,0,.06); min-width: 80px; }
.prog-mini-bar { height: 100%; border-radius: 99px; transition: width .4s; }

/* ── Semáforo badge ── */
.sema-badge { font-size: .7rem; padding: .22rem .7rem; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; font-weight: 600; }

/* ── Empty state ── */
.empty-state { padding: 3rem 1rem; text-align: center; color: #a5a3ae; }
.empty-state i { font-size: 2.5rem; display: block; margin-bottom: .75rem; opacity: .35; }

/* ── Panel lateral ── */
.side-section { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); overflow: hidden; }
.side-section .side-header { padding: .9rem 1.1rem; border-bottom: 1px solid rgba(0,0,0,.06); }
.side-section .side-body { padding: 1rem 1.1rem; }

/* ── Remediacion / control items ── */
.rem-item { padding: .65rem 0; border-bottom: 1px solid rgba(0,0,0,.05); }
.rem-item:last-child { border-bottom: none; }

/* ── Responsive tweaks ── */
@media (max-width: 767px) {
  .mod-tabs-wrap { flex-wrap: wrap; }
  .filter-bar .row { flex-wrap: wrap; }
}
</style>
@endsection

@section('content')

@php
  $colorHex = ['success'=>'#28c76f','warning'=>'#ff9f43','danger'=>'#ea5455'];
  $colorRgb = ['success'=>'40,199,111','warning'=>'255,159,67','danger'=>'234,84,85'];
  $gc_global = $avance_global >= 75 ? '#28c76f' : ($avance_global >= 50 ? '#ff9f43' : '#ea5455');
  $gc_sci    = $sci_avance    >= 75 ? '#28c76f' : ($sci_avance    >= 50 ? '#ff9f43' : '#ea5455');
  $gc_int    = $int_avance    >= 75 ? '#28c76f' : ($int_avance    >= 50 ? '#ff9f43' : '#ea5455');
  $lbl_global = $avance_global >= 75 ? 'En avance' : ($avance_global >= 50 ? 'En proceso' : 'En riesgo');
  $lbl_sci    = $sci_avance    >= 75 ? 'En avance' : ($sci_avance    >= 50 ? 'En proceso' : 'En riesgo');
  $lbl_int    = $int_avance    >= 75 ? 'En avance' : ($int_avance    >= 50 ? 'En proceso' : 'En riesgo');
  $col_global = $avance_global >= 75 ? 'success'   : ($avance_global >= 50 ? 'warning'    : 'danger');
  $col_sci    = $sci_avance    >= 75 ? 'success'   : ($sci_avance    >= 50 ? 'warning'    : 'danger');
  $col_int    = $int_avance    >= 75 ? 'success'   : ($int_avance    >= 50 ? 'warning'    : 'danger');
@endphp

{{-- ════════════════════════════════════════════
     CABECERA
════════════════════════════════════════════ --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Avance por Unidades Orgánicas</h4>
    <p class="text-muted mb-0" style="font-size:.85rem">
      Seguimiento consolidado SCI e Integridad por unidad.
      @if($ultima_actualizacion)
        <span class="ms-2 text-muted" style="font-size:.78rem">
          <i class="ti tabler-clock me-1"></i>Actualizado {{ \Carbon\Carbon::parse($ultima_actualizacion)->translatedFormat('d M Y, g:i a') }}
        </span>
      @endif
    </p>
  </div>
  <a href="{{ route('rep-reportes') }}" class="btn btn-sm btn-label-secondary align-self-start">
    <i class="ti tabler-download me-1"></i>Exportar
  </a>
</div>

{{-- ════════════════════════════════════════════
     TABS MÓDULO — SCI / INTEGRIDAD / GENERAL
════════════════════════════════════════════ --}}
<div class="d-flex align-items-center gap-2 mb-4 mod-tabs-wrap" id="modTabs">
  <button class="mod-tab-btn active-sci" data-mod="sci" onclick="switchMod('sci')">
    <i class="ti tabler-shield-check"></i>
    Control Interno (SCI)
    <span class="badge rounded-pill ms-1" style="background:rgba(105,108,255,.18);color:#696cff;font-size:9px">{{ $sci_avance }}%</span>
  </button>
  <button class="mod-tab-btn" data-mod="integridad" onclick="switchMod('integridad')">
    <i class="ti tabler-heart-handshake"></i>
    Modelo de Integridad
    <span class="badge rounded-pill ms-1" style="background:rgba(40,199,111,.15);color:#28c76f;font-size:9px">{{ $int_avance }}%</span>
  </button>
  <button class="mod-tab-btn" data-mod="general" onclick="switchMod('general')">
    <i class="ti tabler-chart-bar"></i>
    Vista General
    <span class="badge rounded-pill ms-1" style="background:rgba(108,117,125,.12);color:#6c757d;font-size:9px">{{ $avance_global }}%</span>
  </button>
</div>

{{-- ════════════════════════════════════════════
     KPI HERO — cambia según módulo activo
════════════════════════════════════════════ --}}
{{-- === SCI === --}}
<div id="kpi-sci" class="mod-panel">
  <div class="row g-4 mb-4">
    {{-- Gauge SCI --}}
    <div class="col-xl-3 col-sm-6">
      <div class="kpi-card card h-100">
        <div class="kpi-bar" style="background:#696cff"></div>
        <div class="card-body d-flex flex-column align-items-center text-center py-4">
          <div id="chartSci"></div>
          <h2 class="fw-bold mb-1 mt-n2" style="color:{{ $gc_sci }}">{{ $sci_avance }}%</h2>
          <p class="text-muted mb-2" style="font-size:.8rem">Avance SCI — UGEL</p>
          <span class="badge bg-{{ $col_sci }} rounded-pill px-3 mb-4">{{ $lbl_sci }}</span>
          <div class="w-100 d-flex flex-column gap-2 text-start">
            <div class="d-flex align-items-center gap-2"><span style="width:7px;height:7px;border-radius:50%;background:#28c76f;flex-shrink:0"></span><small class="text-muted">Completadas</small><span class="fw-bold ms-auto text-success">{{ $sci_completadas }}</span></div>
            <div class="d-flex align-items-center gap-2"><span style="width:7px;height:7px;border-radius:50%;background:#ff9f43;flex-shrink:0"></span><small class="text-muted">En proceso</small><span class="fw-bold ms-auto text-warning">{{ $sci_en_proceso }}</span></div>
            <div class="d-flex align-items-center gap-2"><span style="width:7px;height:7px;border-radius:50%;background:#ea5455;flex-shrink:0"></span><small class="text-muted">Pendientes</small><span class="fw-bold ms-auto text-danger">{{ $sci_pendientes }}</span></div>
            <div class="border-top pt-2 mt-1 d-flex align-items-center gap-2"><small class="text-muted fw-semibold">Total actividades</small><span class="fw-bold ms-auto">{{ $sci_total }}</span></div>
          </div>
        </div>
      </div>
    </div>
    {{-- Top 3 SCI --}}
    <div class="col-xl-9 col-sm-6">
      <div class="row g-4 h-100">
        @php $top3sci = $unidades->filter(fn($u) => $u->sci_total > 0)->sortByDesc('sci_porcentaje')->take(3)->values(); @endphp
        @foreach($top3sci as $idx => $u)
        <div class="col-12 col-md-4">
          <div class="kpi-card card h-100">
            <div class="kpi-bar" style="background:{{ $colorHex[$u->sci_color] }}"></div>
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="sigla-av bg-label-primary" style="background:rgba(105,108,255,.12);color:#696cff">{{ strtoupper(substr($u->sigla,0,2)) }}</div>
                <span class="badge bg-label-secondary rounded-pill" style="font-size:9px">#{{ $idx+1 }} SCI</span>
              </div>
              <p class="fw-bold mb-0" style="font-size:13px">{{ $u->sigla }}</p>
              <p class="text-muted mb-3" style="font-size:11px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $u->nombre }}</p>
              <div class="d-flex align-items-end gap-1 mb-2">
                <h2 class="fw-bold mb-0" style="color:{{ $colorHex[$u->sci_color] }};line-height:1">{{ $u->sci_porcentaje }}</h2>
                <span class="text-muted fw-semibold mb-1">%</span>
              </div>
              <div class="prog-mini mb-2">
                <div class="prog-mini-bar" style="width:{{ $u->sci_porcentaje }}%;background:{{ $colorHex[$u->sci_color] }}"></div>
              </div>
              <small class="text-muted">{{ $u->sci_completadas }}/{{ $u->sci_total }} completadas</small>
            </div>
            <div class="card-footer border-top-0 pt-0 pb-3 px-3">
              <span class="sema-badge w-100 justify-content-center" style="background:rgba({{ $colorRgb[$u->sci_color] }},.12);color:{{ $colorHex[$u->sci_color] }}">
                {{ SemaforoHelper::label($u->sci_porcentaje, 75, 50, 'En avance', 'En proceso', 'En riesgo') }}
              </span>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- === INTEGRIDAD === --}}
<div id="kpi-integridad" class="mod-panel" style="display:none">
  <div class="row g-4 mb-4">
    <div class="col-xl-3 col-sm-6">
      <div class="kpi-card card h-100">
        <div class="kpi-bar" style="background:#28c76f"></div>
        <div class="card-body d-flex flex-column align-items-center text-center py-4">
          <div id="chartInt"></div>
          <h2 class="fw-bold mb-1 mt-n2" style="color:{{ $gc_int }}">{{ $int_avance }}%</h2>
          <p class="text-muted mb-2" style="font-size:.8rem">Avance Integridad — UGEL</p>
          <span class="badge bg-{{ $col_int }} rounded-pill px-3 mb-4">{{ $lbl_int }}</span>
          <div class="w-100 d-flex flex-column gap-2 text-start">
            <div class="d-flex align-items-center gap-2"><span style="width:7px;height:7px;border-radius:50%;background:#28c76f;flex-shrink:0"></span><small class="text-muted">Completadas</small><span class="fw-bold ms-auto text-success">{{ $int_completadas }}</span></div>
            <div class="d-flex align-items-center gap-2"><span style="width:7px;height:7px;border-radius:50%;background:#ff9f43;flex-shrink:0"></span><small class="text-muted">En proceso</small><span class="fw-bold ms-auto text-warning">{{ $int_en_proceso }}</span></div>
            <div class="d-flex align-items-center gap-2"><span style="width:7px;height:7px;border-radius:50%;background:#ea5455;flex-shrink:0"></span><small class="text-muted">Pendientes</small><span class="fw-bold ms-auto text-danger">{{ $int_pendientes }}</span></div>
            <div class="border-top pt-2 mt-1 d-flex align-items-center gap-2"><small class="text-muted fw-semibold">Total actividades</small><span class="fw-bold ms-auto">{{ $int_total }}</span></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-9 col-sm-6">
      <div class="row g-4 h-100">
        @php $top3int = $unidades->filter(fn($u) => $u->int_total > 0)->sortByDesc('int_porcentaje')->take(3)->values(); @endphp
        @foreach($top3int as $idx => $u)
        <div class="col-12 col-md-4">
          <div class="kpi-card card h-100">
            <div class="kpi-bar" style="background:{{ $colorHex[$u->int_color] }}"></div>
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="sigla-av" style="background:rgba(40,199,111,.12);color:#28c76f">{{ strtoupper(substr($u->sigla,0,2)) }}</div>
                <span class="badge bg-label-secondary rounded-pill" style="font-size:9px">#{{ $idx+1 }} Integ.</span>
              </div>
              <p class="fw-bold mb-0" style="font-size:13px">{{ $u->sigla }}</p>
              <p class="text-muted mb-3" style="font-size:11px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $u->nombre }}</p>
              <div class="d-flex align-items-end gap-1 mb-2">
                <h2 class="fw-bold mb-0" style="color:{{ $colorHex[$u->int_color] }};line-height:1">{{ $u->int_porcentaje }}</h2>
                <span class="text-muted fw-semibold mb-1">%</span>
              </div>
              <div class="prog-mini mb-2">
                <div class="prog-mini-bar" style="width:{{ $u->int_porcentaje }}%;background:{{ $colorHex[$u->int_color] }}"></div>
              </div>
              <small class="text-muted">{{ $u->int_completadas }}/{{ $u->int_total }} completadas</small>
            </div>
            <div class="card-footer border-top-0 pt-0 pb-3 px-3">
              <span class="sema-badge w-100 justify-content-center" style="background:rgba({{ $colorRgb[$u->int_color] }},.12);color:{{ $colorHex[$u->int_color] }}">
                {{ SemaforoHelper::label($u->int_porcentaje, 75, 50, 'En avance', 'En proceso', 'En riesgo') }}
              </span>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- === GENERAL === --}}
<div id="kpi-general" class="mod-panel" style="display:none">
  <div class="row g-4 mb-4">
    <div class="col-xl-3 col-sm-6">
      <div class="kpi-card card h-100">
        <div class="kpi-bar" style="background:#6c757d"></div>
        <div class="card-body d-flex flex-column align-items-center text-center py-4">
          <div id="chartGen"></div>
          <h2 class="fw-bold mb-1 mt-n2" style="color:{{ $gc_global }}">{{ $avance_global }}%</h2>
          <p class="text-muted mb-2" style="font-size:.8rem">Promedio General UGEL</p>
          <span class="badge bg-{{ $col_global }} rounded-pill px-3 mb-4">{{ $lbl_global }}</span>
          <div class="w-100 d-flex flex-column gap-2 text-start">
            <div class="d-flex align-items-center gap-2"><span style="width:7px;height:7px;border-radius:50%;background:#28c76f;flex-shrink:0"></span><small class="text-muted">Completadas</small><span class="fw-bold ms-auto text-success">{{ $total_completadas }}</span></div>
            <div class="d-flex align-items-center gap-2"><span style="width:7px;height:7px;border-radius:50%;background:#ff9f43;flex-shrink:0"></span><small class="text-muted">En proceso</small><span class="fw-bold ms-auto text-warning">{{ $total_en_proceso }}</span></div>
            <div class="d-flex align-items-center gap-2"><span style="width:7px;height:7px;border-radius:50%;background:#ea5455;flex-shrink:0"></span><small class="text-muted">Pendientes</small><span class="fw-bold ms-auto text-danger">{{ $total_pendientes }}</span></div>
            <div class="border-top pt-2 mt-1 d-flex align-items-center gap-2"><small class="text-muted fw-semibold">Total actividades</small><span class="fw-bold ms-auto">{{ $total_actividades }}</span></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-9 col-sm-6">
      <div class="row g-4 h-100">
        @php $top3gen = $unidades->take(3); @endphp
        @foreach($top3gen as $idx => $u)
        <div class="col-12 col-md-4">
          <div class="kpi-card card h-100">
            <div class="kpi-bar" style="background:{{ $colorHex[$u->color] }}"></div>
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="sigla-av" style="background:rgba(108,117,125,.12);color:#6c757d">{{ strtoupper(substr($u->sigla,0,2)) }}</div>
                <span class="badge bg-label-secondary rounded-pill" style="font-size:9px">#{{ $idx+1 }}</span>
              </div>
              <p class="fw-bold mb-0" style="font-size:13px">{{ $u->sigla }}</p>
              <p class="text-muted mb-3" style="font-size:11px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $u->nombre }}</p>
              <div class="d-flex align-items-end gap-1 mb-2">
                <h2 class="fw-bold mb-0" style="color:{{ $colorHex[$u->color] }};line-height:1">{{ $u->porcentaje }}</h2>
                <span class="text-muted fw-semibold mb-1">%</span>
              </div>
              <div class="prog-mini mb-2">
                <div class="prog-mini-bar" style="width:{{ $u->porcentaje }}%;background:{{ $colorHex[$u->color] }}"></div>
              </div>
              <small class="text-muted">{{ $u->completadas_count }}/{{ $u->actividades_count }} completadas</small>
            </div>
            <div class="card-footer border-top-0 pt-0 pb-3 px-3">
              <span class="sema-badge w-100 justify-content-center" style="background:rgba({{ $colorRgb[$u->color] }},.12);color:{{ $colorHex[$u->color] }}">{{ $u->semaforo }}</span>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════
     CUERPO PRINCIPAL: TABLA + PANEL LATERAL
════════════════════════════════════════════ --}}
<div class="row g-5">

  {{-- ── Tabla principal ─────────────────────────────────────────── --}}
  <div class="col-xl-8">
    <div class="card" style="border-radius:14px;border:1px solid rgba(0,0,0,.06)">

      {{-- Encabezado tabla con filtros en tiempo real --}}
      <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
        <div>
          <h6 class="fw-bold mb-0" id="tblTitle">Avance por Unidad — SCI</h6>
          <p class="text-muted mb-0" style="font-size:.75rem" id="tblSubtitle">Sistema de Control Interno</p>
        </div>
        {{-- Filtros en tiempo real --}}
        <div class="filter-bar d-flex align-items-center gap-2 px-3 py-2">
          <i class="ti tabler-search text-muted" style="font-size:.95rem"></i>
          <input type="text" id="filterBuscar" placeholder="Buscar unidad…" style="width:160px">
          <select id="filterEstado">
            <option value="">Todos los estados</option>
            <option value="success">En avance</option>
            <option value="warning">En proceso</option>
            <option value="danger">En riesgo</option>
          </select>
          <button class="btn btn-xs btn-label-secondary rounded" id="btnLimpiar" onclick="limpiarFiltros()" title="Limpiar filtros">
            <i class="ti tabler-x"></i>
          </button>
        </div>
      </div>

      {{-- Tabla --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 tbl-avance" id="tblUnidades">
          <thead>
            <tr>
              <th class="ps-4">Unidad Orgánica</th>
              <th style="min-width:110px">Avance</th>
              <th class="text-center">Compl.</th>
              <th class="text-center">En proc.</th>
              <th class="text-center">Pendient.</th>
              <th class="text-center">Estado</th>
              <th class="text-center pe-3">Detalle</th>
            </tr>
          </thead>
          <tbody id="tblBody">
            @forelse($unidades as $u)
            @php
              $sciColor  = $colorHex[$u->sci_color]  ?? '#ea5455';
              $intColor  = $colorHex[$u->int_color]  ?? '#ea5455';
              $genColor  = $colorHex[$u->color]      ?? '#ea5455';
              $sciRgb    = $colorRgb[$u->sci_color]  ?? '234,84,85';
              $intRgb    = $colorRgb[$u->int_color]  ?? '234,84,85';
              $genRgb    = $colorRgb[$u->color]      ?? '234,84,85';
            @endphp
            <tr
              data-nombre="{{ strtolower($u->nombre) }} {{ strtolower($u->sigla) }}"
              data-sci-color="{{ $u->sci_color }}"
              data-int-color="{{ $u->int_color }}"
              data-gen-color="{{ $u->color }}"
            >
              <td class="ps-4">
                <div class="d-flex align-items-center gap-3">
                  {{-- Avatar cambia color según módulo --}}
                  <div class="sigla-av mod-avatar"
                       data-sci-bg="rgba(105,108,255,.12)" data-sci-color="#696cff"
                       data-int-bg="rgba(40,199,111,.12)"  data-int-color="#28c76f"
                       data-gen-bg="rgba(108,117,125,.12)" data-gen-color="#6c757d"
                       style="background:rgba(105,108,255,.12);color:#696cff">
                    {{ strtoupper(substr($u->sigla,0,2)) }}
                  </div>
                  <div>
                    <p class="fw-bold mb-0" style="font-size:13px">{{ $u->sigla }}</p>
                    <small class="text-muted">{{ Str::limit($u->nombre, 28) }}</small>
                  </div>
                </div>
              </td>

              {{-- Avance — cambia según módulo activo --}}
              <td>
                {{-- SCI --}}
                <div class="d-flex align-items-center gap-2 mod-col" data-show="sci">
                  <div class="prog-mini flex-grow-1">
                    <div class="prog-mini-bar" style="width:{{ $u->sci_porcentaje }}%;background:{{ $sciColor }}"></div>
                  </div>
                  <span class="fw-bold" style="min-width:30px;font-size:12px;color:{{ $sciColor }}">{{ $u->sci_porcentaje }}%</span>
                </div>
                {{-- Integridad --}}
                <div class="d-flex align-items-center gap-2 mod-col" data-show="integridad" style="display:none!important">
                  <div class="prog-mini flex-grow-1">
                    <div class="prog-mini-bar" style="width:{{ $u->int_porcentaje }}%;background:{{ $intColor }}"></div>
                  </div>
                  <span class="fw-bold" style="min-width:30px;font-size:12px;color:{{ $intColor }}">{{ $u->int_porcentaje }}%</span>
                </div>
                {{-- General --}}
                <div class="d-flex align-items-center gap-2 mod-col" data-show="general" style="display:none!important">
                  <div class="prog-mini flex-grow-1">
                    <div class="prog-mini-bar" style="width:{{ $u->porcentaje }}%;background:{{ $genColor }}"></div>
                  </div>
                  <span class="fw-bold" style="min-width:30px;font-size:12px;color:{{ $genColor }}">{{ $u->porcentaje }}%</span>
                </div>
              </td>

              {{-- Completadas --}}
              <td class="text-center">
                <span class="fw-bold text-success mod-col" data-show="sci">{{ $u->sci_completadas }}</span>
                <span class="fw-bold text-success mod-col" data-show="integridad" style="display:none!important">{{ $u->int_completadas }}</span>
                <span class="fw-bold text-success mod-col" data-show="general" style="display:none!important">{{ $u->completadas_count }}</span>
              </td>

              {{-- En proceso --}}
              <td class="text-center">
                <span class="fw-bold text-warning mod-col" data-show="sci">{{ $u->sci_en_proceso }}</span>
                <span class="fw-bold text-warning mod-col" data-show="integridad" style="display:none!important">{{ $u->int_en_proceso }}</span>
                <span class="fw-bold text-warning mod-col" data-show="general" style="display:none!important">{{ $u->en_proceso_count }}</span>
              </td>

              {{-- Pendientes --}}
              <td class="text-center">
                <span class="fw-bold text-danger mod-col" data-show="sci">{{ $u->sci_pendientes }}</span>
                <span class="fw-bold text-danger mod-col" data-show="integridad" style="display:none!important">{{ $u->int_pendientes }}</span>
                <span class="fw-bold text-danger mod-col" data-show="general" style="display:none!important">{{ $u->pendientes_count }}</span>
              </td>

              {{-- Estado semáforo --}}
              <td class="text-center">
                {{-- SCI --}}
                <span class="sema-badge mod-col" data-show="sci"
                      style="background:rgba({{ $sciRgb }},.12);color:{{ $sciColor }}">
                  {{ $u->sci_porcentaje >= 75 ? 'En avance' : ($u->sci_porcentaje >= 50 ? 'En proceso' : 'En riesgo') }}
                </span>
                {{-- Integridad --}}
                <span class="sema-badge mod-col" data-show="integridad" style="display:none!important;background:rgba({{ $intRgb }},.12);color:{{ $intColor }}">
                  {{ $u->int_porcentaje >= 75 ? 'En avance' : ($u->int_porcentaje >= 50 ? 'En proceso' : 'En riesgo') }}
                </span>
                {{-- General --}}
                <span class="sema-badge mod-col" data-show="general" style="display:none!important;background:rgba({{ $genRgb }},.12);color:{{ $genColor }}">
                  {{ $u->semaforo }}
                </span>
              </td>

              {{-- Acción --}}
              <td class="text-center pe-3">
                <a href="{{ route('sci-control-interno') }}?unidad_organica_id={{ $u->id }}"
                   class="btn btn-xs btn-label-primary rounded-pill mod-col" data-show="sci">Ver SCI</a>
                <a href="{{ route('integridad') }}?unidad_organica_id={{ $u->id }}"
                   class="btn btn-xs btn-label-success rounded-pill mod-col" data-show="integridad" style="display:none!important">Ver Integ.</a>
                <div class="d-flex gap-1 justify-content-center mod-col" data-show="general" style="display:none!important">
                  <a href="{{ route('sci-control-interno') }}?unidad_organica_id={{ $u->id }}"
                     class="btn btn-xs btn-label-primary rounded-pill" style="font-size:9px">SCI</a>
                  <a href="{{ route('integridad') }}?unidad_organica_id={{ $u->id }}"
                     class="btn btn-xs btn-label-success rounded-pill" style="font-size:9px">Int.</a>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i class="ti tabler-building-community"></i>Sin unidades registradas</div></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Footer tabla --}}
      <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between" style="background:rgba(0,0,0,.01)">
        <small class="text-muted" id="tblCount">Mostrando {{ $unidades->count() }} unidades</small>
        <small class="text-muted" id="tblMod" style="font-size:.72rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:#696cff">
          SCI
        </small>
      </div>
    </div>
  </div>

  {{-- ── Panel lateral ───────────────────────────────────────────── --}}
  <div class="col-xl-4">

    {{-- Distribución donut --}}
    <div class="side-section card mb-4">
      <div class="side-header d-flex align-items-center justify-content-between">
        <div>
          <h6 class="fw-bold mb-0">Distribución por Estado</h6>
          <small class="text-muted" id="sideModLabel">Sistema de Control Interno</small>
        </div>
      </div>
      <div class="side-body">
        <div id="chartDistribucion" class="mx-auto mb-3" style="max-width:190px"></div>
        <div class="d-flex flex-column gap-3" id="sideStats">
          {{-- Se actualiza vía JS --}}
          @foreach([
            ['label'=>'Completadas','color'=>'success','hex'=>'#28c76f','val'=>$sci_completadas,'total'=>$sci_total],
            ['label'=>'En proceso', 'color'=>'warning','hex'=>'#ff9f43','val'=>$sci_en_proceso, 'total'=>$sci_total],
            ['label'=>'Pendientes', 'color'=>'danger', 'hex'=>'#ea5455','val'=>$sci_pendientes, 'total'=>$sci_total],
          ] as $d)
          <div class="d-flex align-items-center gap-3">
            <div class="badge rounded p-1_5" style="background:rgba({{ $d['color']==='success'?'40,199,111':($d['color']==='warning'?'255,159,67':'234,84,85') }},.12)">
              <i class="icon-base ti {{ $d['color']==='success'?'tabler-circle-check':($d['color']==='warning'?'tabler-clock':'tabler-alert-triangle') }} icon-sm text-{{ $d['color'] }}"></i>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between mb-1">
                <small class="fw-semibold">{{ $d['label'] }}</small>
                <small class="fw-bold text-{{ $d['color'] }}" data-stat="{{ $d['color'] }}">{{ $d['val'] }}</small>
              </div>
              <div class="progress rounded-pill" style="height:4px">
                <div class="progress-bar bg-{{ $d['color'] }} rounded-pill"
                     style="width:{{ $d['total'] ? round($d['val']/$d['total']*100) : 0 }}%"
                     data-bar="{{ $d['color'] }}"></div>
              </div>
            </div>
          </div>
          @endforeach
          <div class="border-top pt-2 d-flex justify-content-between">
            <small class="fw-semibold text-muted">Total</small>
            <small class="fw-bold" id="sideTotal">{{ $sci_total }}</small>
          </div>
        </div>
      </div>
    </div>

    {{-- Alertas SCI: medidas de remediación --}}
    <div class="side-section card mb-4" id="panelRemediacion">
      <div class="side-header d-flex align-items-center gap-2">
        <span style="width:8px;height:8px;border-radius:50%;background:#696cff;flex-shrink:0"></span>
        <div>
          <h6 class="fw-bold mb-0" style="font-size:.85rem">Medidas de Remediación</h6>
          <small class="text-muted" style="font-size:.72rem">SCI · Alta prioridad pendientes</small>
        </div>
      </div>
      <div class="side-body pt-2 pb-1">
        @forelse($medidas_remediacion as $m)
        <div class="rem-item">
          <div class="d-flex align-items-start gap-2">
            <div style="width:6px;height:6px;border-radius:50%;background:#ea5455;flex-shrink:0;margin-top:6px"></div>
            <div class="flex-grow-1">
              <p class="fw-semibold mb-0" style="font-size:12px;line-height:1.35">{{ Str::limit($m->nombre, 45) }}</p>
              <div class="d-flex align-items-center gap-2 mt-1">
                <span class="badge bg-label-secondary rounded-pill" style="font-size:9px">{{ $m->unidadOrganica->sigla ?? '—' }}</span>
                @if($m->fecha_limite)
                <small class="text-muted" style="font-size:10px">
                  <i class="ti tabler-calendar me-1"></i>{{ $m->fecha_limite->format('d/m/Y') }}
                </small>
                @endif
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="empty-state py-4"><i class="ti tabler-circle-check" style="color:#28c76f;opacity:.6"></i><small>Sin medidas críticas pendientes</small></div>
        @endforelse
        @if($medidas_remediacion->count())
        <div class="pt-2 pb-1">
          <a href="{{ route('sci-control-interno') }}" class="text-primary fw-medium" style="font-size:11px">Ver todas en SCI <i class="ti tabler-arrow-right icon-11px"></i></a>
        </div>
        @endif
      </div>
    </div>

    {{-- Logros Integridad: medidas de control --}}
    <div class="side-section card" id="panelControl">
      <div class="side-header d-flex align-items-center gap-2">
        <span style="width:8px;height:8px;border-radius:50%;background:#28c76f;flex-shrink:0"></span>
        <div>
          <h6 class="fw-bold mb-0" style="font-size:.85rem">Logros de Integridad</h6>
          <small class="text-muted" style="font-size:.72rem">Integridad · Completadas recientemente</small>
        </div>
      </div>
      <div class="side-body pt-2 pb-1">
        @forelse($medidas_control as $m)
        <div class="rem-item">
          <div class="d-flex align-items-start gap-2">
            <div style="width:6px;height:6px;border-radius:50%;background:#28c76f;flex-shrink:0;margin-top:6px"></div>
            <div class="flex-grow-1">
              <p class="fw-semibold mb-0" style="font-size:12px;line-height:1.35">{{ Str::limit($m->nombre, 45) }}</p>
              <div class="d-flex align-items-center gap-2 mt-1">
                <span class="badge bg-label-success rounded-pill" style="font-size:9px">{{ $m->unidadOrganica->sigla ?? '—' }}</span>
                <small class="text-muted" style="font-size:10px"><i class="ti tabler-check me-1 text-success"></i>Completada</small>
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="empty-state py-4"><i class="ti tabler-shield" style="opacity:.4"></i><small>Sin logros recientes</small></div>
        @endforelse
        @if($medidas_control->count())
        <div class="pt-2 pb-1">
          <a href="{{ route('integridad') }}" class="text-success fw-medium" style="font-size:11px">Ver todas en Integridad <i class="ti tabler-arrow-right icon-11px"></i></a>
        </div>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  const isDark    = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const textColor = isDark ? '#b4bdc6' : '#697a8d';
  const bgColor   = isDark ? '#2b2c40' : '#fff';

  // ── Datos por módulo para el donut lateral ────────────────────────
  const modData = {
    sci:       { comp: {{ $sci_completadas }},    proc: {{ $sci_en_proceso }},   pend: {{ $sci_pendientes }},   total: {{ $sci_total }},          label: 'Sistema de Control Interno' },
    integridad:{ comp: {{ $int_completadas }},    proc: {{ $int_en_proceso }},   pend: {{ $int_pendientes }},   total: {{ $int_total }},          label: 'Modelo de Integridad' },
    general:   { comp: {{ $total_completadas }},  proc: {{ $total_en_proceso }}, pend: {{ $total_pendientes }}, total: {{ $total_actividades }},  label: 'Vista General Consolidada' },
  };

  const modMeta = {
    sci:        { tblTitle: 'Avance por Unidad — SCI',        tblSub: 'Sistema de Control Interno',   modLabel: 'SCI',        modLabelColor: '#696cff' },
    integridad: { tblTitle: 'Avance por Unidad — Integridad', tblSub: 'Modelo de Integridad',         modLabel: 'INTEGRIDAD', modLabelColor: '#28c76f' },
    general:    { tblTitle: 'Avance por Unidad — General',    tblSub: 'Vista consolidada SCI + Integridad', modLabel: 'GENERAL',    modLabelColor: '#6c757d' },
  };

  // ── Gauges ──────────────────────────────────────────────────────
  function makeGauge(id, value, color) {
    if (!document.getElementById(id)) return null;
    return new ApexCharts(document.getElementById(id), {
      chart:   { type: 'radialBar', height: 160, sparkline: { enabled: true } },
      series:  [value],
      plotOptions: {
        radialBar: {
          startAngle: -135, endAngle: 135,
          hollow: { size: '60%' },
          track:  { background: isDark ? '#2d2d4a' : '#e8e8e8', strokeWidth: '97%' },
          dataLabels: { name: { show: false }, value: { show: false } },
        }
      },
      fill:   { colors: [color] },
      stroke: { lineCap: 'round' },
    });
  }

  const gc_sci = '{{ $gc_sci }}', gc_int = '{{ $gc_int }}', gc_gen = '{{ $gc_global }}';
  const gauges = {
    sci:        makeGauge('chartSci', {{ $sci_avance }},    gc_sci),
    integridad: makeGauge('chartInt', {{ $int_avance }},    gc_int),
    general:    makeGauge('chartGen', {{ $avance_global }}, gc_gen),
  };
  Object.values(gauges).forEach(g => g && g.render());

  // ── Donut distribución lateral ───────────────────────────────────
  let donutChart = null;
  function renderDonut(mod) {
    const d = modData[mod];
    const opts = {
      chart:   { type: 'donut', height: 190 },
      series:  [d.comp, d.proc, d.pend],
      labels:  ['Completadas', 'En proceso', 'Pendientes'],
      colors:  ['#28c76f', '#ff9f43', '#ea5455'],
      plotOptions: {
        pie: { donut: { size: '72%', labels: {
          show: true,
          total: { show: true, label: 'Total', color: textColor, formatter: () => d.total },
          value: { fontSize: '18px', fontWeight: 700, color: textColor },
        }}}
      },
      legend:      { show: false },
      dataLabels:  { enabled: false },
      stroke:      { width: 2, colors: [bgColor] },
      tooltip:     { y: { formatter: v => v + ' actividades' } },
    };
    if (donutChart) {
      donutChart.updateSeries([d.comp, d.proc, d.pend]);
    } else {
      donutChart = new ApexCharts(document.getElementById('chartDistribucion'), opts);
      donutChart.render();
    }
  }
  renderDonut('sci');

  // ── Actualizar stats del panel lateral ──────────────────────────
  function updateSideStats(mod) {
    const d = modData[mod];
    document.getElementById('sideModLabel').textContent = modData[mod].label;
    document.getElementById('sideTotal').textContent = d.total;
    document.querySelector('[data-stat="success"]').textContent  = d.comp;
    document.querySelector('[data-stat="warning"]').textContent  = d.proc;
    document.querySelector('[data-stat="danger"]').textContent   = d.pend;
    const pct = (v) => d.total ? Math.round(v/d.total*100) : 0;
    document.querySelector('[data-bar="success"]').style.width  = pct(d.comp) + '%';
    document.querySelector('[data-bar="warning"]').style.width  = pct(d.proc) + '%';
    document.querySelector('[data-bar="danger"]').style.width   = pct(d.pend) + '%';
  }

  // ── Switch de módulo ─────────────────────────────────────────────
  let currentMod = 'sci';

  window.switchMod = function(mod) {
    currentMod = mod;

    // Tabs
    document.querySelectorAll('.mod-tab-btn').forEach(btn => {
      btn.classList.remove('active-sci', 'active-int', 'active-gen');
    });
    const activeBtn = document.querySelector(`[data-mod="${mod}"]`);
    if (mod === 'sci')        activeBtn.classList.add('active-sci');
    else if (mod === 'integridad') activeBtn.classList.add('active-int');
    else                      activeBtn.classList.add('active-gen');

    // KPI panels
    document.querySelectorAll('.mod-panel').forEach(p => p.style.display = 'none');
    document.getElementById('kpi-' + mod).style.display = '';

    // Columnas de tabla
    document.querySelectorAll('.mod-col').forEach(el => {
      el.style.setProperty('display', 'none', 'important');
    });
    document.querySelectorAll(`.mod-col[data-show="${mod}"]`).forEach(el => {
      el.style.removeProperty('display');
    });

    // Avatares
    document.querySelectorAll('.mod-avatar').forEach(av => {
      av.style.background = av.dataset[mod + 'Bg'] || 'rgba(108,117,125,.12)';
      av.style.color      = av.dataset[mod + 'Color'] || '#6c757d';
    });

    // Título tabla
    const meta = modMeta[mod];
    document.getElementById('tblTitle').textContent    = meta.tblTitle;
    document.getElementById('tblSubtitle').textContent = meta.tblSub;
    document.getElementById('tblMod').textContent      = meta.modLabel;
    document.getElementById('tblMod').style.color      = meta.modLabelColor;

    // Donut + stats lateral
    renderDonut(mod);
    updateSideStats(mod);

    // Re-aplicar filtros
    applyFilters();
  };

  // ── Filtros en tiempo real ───────────────────────────────────────
  const inputBuscar  = document.getElementById('filterBuscar');
  const selEstado    = document.getElementById('filterEstado');
  const countEl      = document.getElementById('tblCount');

  function applyFilters() {
    const buscar = inputBuscar.value.toLowerCase().trim();
    const estado = selEstado.value;
    const rows   = document.querySelectorAll('#tblBody tr');
    let visible  = 0;

    rows.forEach(row => {
      if (!row.dataset.nombre) { return; } // fila vacía/empty

      const nombre   = row.dataset.nombre || '';
      const colorKey = `data-${currentMod}-color` === 'data-gen-color'
                       ? row.dataset.genColor
                       : (currentMod === 'sci' ? row.dataset.sciColor : row.dataset.intColor);

      const matchNombre = !buscar || nombre.includes(buscar);
      const matchEstado = !estado || colorKey === estado;

      if (matchNombre && matchEstado) {
        row.style.display = '';
        visible++;
      } else {
        row.style.display = 'none';
      }
    });

    const total = document.querySelectorAll('#tblBody tr[data-nombre]').length;
    countEl.textContent = visible === total
      ? `Mostrando ${total} unidades`
      : `Mostrando ${visible} de ${total} unidades`;
  }

  inputBuscar.addEventListener('input',  applyFilters);
  selEstado.addEventListener('change',   applyFilters);

  window.limpiarFiltros = function() {
    inputBuscar.value = '';
    selEstado.value   = '';
    applyFilters();
  };

});
</script>
@endsection
