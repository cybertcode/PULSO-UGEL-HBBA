@php
use Illuminate\Support\Str;
use App\Support\SemaforoHelper;
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
  padding: .6rem 1.4rem; border-radius: 10px; border: 1.5px solid rgba(0,0,0,.14);
  font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .18s;
  background: var(--bs-body-bg); color: #6e6b7b; white-space: nowrap;
  box-shadow: 0 1px 3px rgba(0,0,0,.06);
}
.mod-tab-btn.active-sci  { background: rgba(105,108,255,.1); border-color: #696cff; color: #696cff; box-shadow: 0 2px 10px rgba(105,108,255,.2); }
.mod-tab-btn.active-int  { background: rgba(40,199,111,.1);  border-color: #28c76f; color: #28c76f; box-shadow: 0 2px 10px rgba(40,199,111,.2); }
.mod-tab-btn.active-gen  { background: rgba(108,117,125,.1); border-color: #6c757d; color: #6c757d; box-shadow: 0 2px 10px rgba(108,117,125,.15); }
.mod-tab-btn:hover:not(.active-sci):not(.active-int):not(.active-gen) {
  background: rgba(105,108,255,.05); border-color: rgba(105,108,255,.3); color: #696cff;
}

/* ── KPI cards (estilo mis-actividades) ── */
.kpi-card {
  border-radius: 14px; border: none; overflow: hidden;
  box-shadow: rgba(47,43,61,.14) 0px 3px 12px 0px;
  transition: transform .18s, box-shadow .18s;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.14); }

.kpi-icon {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; flex-shrink: 0;
}
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1; }
.kpi-label { font-size: .72rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }
.kpi-sub   { font-size: .8rem; font-weight: 600; opacity: .85; }

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
  <a href="{{ route('mon-avance-unidades.exportar') }}" class="btn btn-sm btn-label-success align-self-start">
    <i class="ti tabler-file-spreadsheet me-1"></i>Exportar Excel
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
{{-- === SCI — 4 KPI cards horizontales === --}}
<div id="kpi-sci" class="mod-panel">
  <div class="row g-4 mb-4">
    {{-- Avance SCI --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Avance SCI</div>
              <div class="kpi-value">{{ $sci_avance }}%</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-shield-check"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">{{ $lbl_sci }} — UGEL</div>
        </div>
      </div>
    </div>
    {{-- Completadas SCI --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Completadas</div>
              <div class="kpi-value">{{ $sci_completadas }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-circle-check"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">de {{ $sci_total }} actividades SCI</div>
        </div>
      </div>
    </div>
    {{-- En proceso SCI --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">En Proceso</div>
              <div class="kpi-value">{{ $sci_en_proceso }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-loader-2"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">Actividades en curso</div>
        </div>
      </div>
    </div>
    {{-- Pendientes SCI --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#cb2d3e 0%,#ef473a 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Pendientes</div>
              <div class="kpi-value">{{ $sci_pendientes }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-clock-exclamation"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">Requieren atención</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- === INTEGRIDAD — 4 KPI cards horizontales === --}}
<div id="kpi-integridad" class="mod-panel" style="display:none">
  <div class="row g-4 mb-4">
    {{-- Avance Integridad --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Avance Integridad</div>
              <div class="kpi-value">{{ $int_avance }}%</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-heart-handshake"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">{{ $lbl_int }} — UGEL</div>
        </div>
      </div>
    </div>
    {{-- Completadas Integridad --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Completadas</div>
              <div class="kpi-value">{{ $int_completadas }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-circle-check"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">de {{ $int_total }} actividades</div>
        </div>
      </div>
    </div>
    {{-- En proceso Integridad --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">En Proceso</div>
              <div class="kpi-value">{{ $int_en_proceso }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-loader-2"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">Actividades en curso</div>
        </div>
      </div>
    </div>
    {{-- Pendientes Integridad --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#cb2d3e 0%,#ef473a 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Pendientes</div>
              <div class="kpi-value">{{ $int_pendientes }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-clock-exclamation"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">Requieren atención</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- === GENERAL — 4 KPI cards horizontales === --}}
<div id="kpi-general" class="mod-panel" style="display:none">
  <div class="row g-4 mb-4">
    {{-- Avance General --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Avance General</div>
              <div class="kpi-value">{{ $avance_global }}%</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-chart-bar"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">{{ $lbl_global }} — SCI + Integridad</div>
        </div>
      </div>
    </div>
    {{-- Total actividades --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Total Actividades</div>
              <div class="kpi-value">{{ $total_actividades }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-clipboard-list"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">SCI + Integridad</div>
        </div>
      </div>
    </div>
    {{-- Completadas General --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Completadas</div>
              <div class="kpi-value">{{ $total_completadas }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-circle-check"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">{{ $total_en_proceso }} en proceso</div>
        </div>
      </div>
    </div>
    {{-- Pendientes General --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#cb2d3e 0%,#ef473a 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Pendientes</div>
              <div class="kpi-value">{{ $total_pendientes }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-clock-exclamation"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">Requieren atención</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════
     CUERPO PRINCIPAL: TABLA + PANEL LATERAL
════════════════════════════════════════════ --}}
<div class="row g-4">

  {{-- ── Tabla principal (full-width) ──────────────────────────────── --}}
  <div class="col-12">
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
              data-sci-pct="{{ $u->sci_porcentaje }}"
              data-int-pct="{{ $u->int_porcentaje }}"
              data-gen-pct="{{ $u->porcentaje }}"
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
                  @if($u->sci_total > 0)
                  <div class="prog-mini flex-grow-1">
                    <div class="prog-mini-bar" style="width:{{ $u->sci_porcentaje }}%;background:{{ $sciColor }}"></div>
                  </div>
                  <span class="fw-bold" style="min-width:30px;font-size:12px;color:{{ $sciColor }}">{{ $u->sci_porcentaje }}%</span>
                  @else
                  <span class="text-muted" style="font-size:11px;font-style:italic">Sin actividades</span>
                  @endif
                </div>
                {{-- Integridad --}}
                <div class="d-flex align-items-center gap-2 mod-col" data-show="integridad" style="display:none!important">
                  @if($u->int_total > 0)
                  <div class="prog-mini flex-grow-1">
                    <div class="prog-mini-bar" style="width:{{ $u->int_porcentaje }}%;background:{{ $intColor }}"></div>
                  </div>
                  <span class="fw-bold" style="min-width:30px;font-size:12px;color:{{ $intColor }}">{{ $u->int_porcentaje }}%</span>
                  @else
                  <span class="text-muted" style="font-size:11px;font-style:italic">Sin actividades</span>
                  @endif
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
                @if($u->sci_total > 0)
                <span class="sema-badge mod-col" data-show="sci"
                      style="background:rgba({{ $sciRgb }},.12);color:{{ $sciColor }}">
                  {{ $u->sci_porcentaje >= 75 ? 'En avance' : ($u->sci_porcentaje >= 50 ? 'En proceso' : 'En riesgo') }}
                </span>
                @else
                <span class="sema-badge mod-col" data-show="sci" style="background:rgba(108,117,125,.1);color:#6c757d">—</span>
                @endif
                {{-- Integridad --}}
                @if($u->int_total > 0)
                <span class="sema-badge mod-col" data-show="integridad" style="display:none!important;background:rgba({{ $intRgb }},.12);color:{{ $intColor }}">
                  {{ $u->int_porcentaje >= 75 ? 'En avance' : ($u->int_porcentaje >= 50 ? 'En proceso' : 'En riesgo') }}
                </span>
                @else
                <span class="sema-badge mod-col" data-show="integridad" style="display:none!important;background:rgba(108,117,125,.1);color:#6c757d">—</span>
                @endif
                {{-- General --}}
                <span class="sema-badge mod-col" data-show="general" style="display:none!important;background:rgba({{ $genRgb }},.12);color:{{ $genColor }}">
                  {{ $u->semaforo }}
                </span>
              </td>

              {{-- Acción --}}
              <td class="text-center pe-3">
                <a href="{{ route('sci-control-interno') }}?unidad_organica_id={{ $u->id }}"
                   class="btn btn-xs btn-label-primary rounded-pill mod-col" data-show="sci">Ver SCI</a>
                <a href="{{ route('sci-modelo-integridad') }}?unidad_organica_id={{ $u->id }}"
                   class="btn btn-xs btn-label-success rounded-pill mod-col" data-show="integridad" style="display:none!important">Ver Integ.</a>
                <div class="d-flex gap-1 justify-content-center mod-col" data-show="general" style="display:none!important">
                  <a href="{{ route('sci-control-interno') }}?unidad_organica_id={{ $u->id }}"
                     class="btn btn-xs btn-label-primary rounded-pill" style="font-size:9px">SCI</a>
                  <a href="{{ route('sci-modelo-integridad') }}?unidad_organica_id={{ $u->id }}"
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

  {{-- ── Panel inferior: Distribución + Remediación + Logros ────────── --}}
  <div class="col-12">
    <div class="row g-4">

      {{-- Donut distribución --}}
      <div class="col-xl-4 col-md-12">
        <div class="side-section card h-100">
          <div class="side-header d-flex align-items-center justify-content-between">
            <div>
              <h6 class="fw-bold mb-0">Distribución por Estado</h6>
              <small class="text-muted" id="sideModLabel">Sistema de Control Interno</small>
            </div>
          </div>
          <div class="side-body">
            {{-- Donut + stats en fila horizontal --}}
            <div class="d-flex align-items-center gap-4 flex-wrap">
              <div id="chartDistribucion" style="min-width:170px;max-width:190px;flex-shrink:0"></div>
              <div class="flex-grow-1 d-flex flex-column gap-3" id="sideStats">
                @foreach([
                  ['label'=>'Completadas','color'=>'success','val'=>$sci_completadas,'total'=>$sci_total],
                  ['label'=>'En proceso', 'color'=>'warning','val'=>$sci_en_proceso, 'total'=>$sci_total],
                  ['label'=>'Pendientes', 'color'=>'danger', 'val'=>$sci_pendientes, 'total'=>$sci_total],
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
        </div>
      </div>

      {{-- Medidas de Remediación SCI --}}
      <div class="col-xl-4 col-md-6" id="panelRemediacion">
        <div class="side-section card h-100">
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
                  <p class="fw-semibold mb-0" style="font-size:12px;line-height:1.35">{{ Str::limit($m->nombre, 50) }}</p>
                  <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge bg-label-secondary rounded-pill" style="font-size:9px">{{ $m->unidadOrganica->sigla ?? '—' }}</span>
                    @if($m->fecha_limite)
                    <small class="text-muted" style="font-size:10px"><i class="ti tabler-calendar me-1"></i>{{ $m->fecha_limite->format('d/m/Y') }}</small>
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
      </div>

      {{-- Logros Integridad --}}
      <div class="col-xl-4 col-md-6" id="panelControl">
        <div class="side-section card h-100">
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
                  <p class="fw-semibold mb-0" style="font-size:12px;line-height:1.35">{{ Str::limit($m->nombre, 50) }}</p>
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
              <a href="{{ route('sci-modelo-integridad') }}" class="text-success fw-medium" style="font-size:11px">Ver todas en Integridad <i class="ti tabler-arrow-right icon-11px"></i></a>
            </div>
            @endif
          </div>
        </div>
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

  // ── Datos por módulo ─────────────────────────────────────────────
  const modData = {
    sci:       { comp: {{ $sci_completadas }},   proc: {{ $sci_en_proceso }},   pend: {{ $sci_pendientes }},   total: {{ $sci_total }},         label: 'Sistema de Control Interno' },
    integridad:{ comp: {{ $int_completadas }},   proc: {{ $int_en_proceso }},   pend: {{ $int_pendientes }},   total: {{ $int_total }},         label: 'Modelo de Integridad' },
    general:   { comp: {{ $total_completadas }}, proc: {{ $total_en_proceso }}, pend: {{ $total_pendientes }}, total: {{ $total_actividades }}, label: 'Vista General Consolidada' },
  };

  const modMeta = {
    sci:        { tblTitle: 'Avance por Unidad — SCI',        tblSub: 'Sistema de Control Interno',        modLabel: 'SCI',        modLabelColor: '#696cff' },
    integridad: { tblTitle: 'Avance por Unidad — Integridad', tblSub: 'Modelo de Integridad',              modLabel: 'INTEGRIDAD', modLabelColor: '#28c76f' },
    general:    { tblTitle: 'Avance por Unidad — General',    tblSub: 'Vista consolidada SCI + Integridad', modLabel: 'GENERAL',    modLabelColor: '#6c757d' },
  };

  // Paneles laterales por módulo
  const sidePanels = {
    sci:        { id: 'panelRemediacion', other: 'panelControl' },
    integridad: { id: 'panelControl',     other: 'panelRemediacion' },
    general:    { id: null,               other: null },
  };


  // ── FIX #1: Donut — destruir y recrear para que el total cambie ──
  let donutChart = null;
  function renderDonut(mod) {
    const d = modData[mod];
    const container = document.getElementById('chartDistribucion');

    // Destruir instancia anterior para que el label "Total" se actualice
    if (donutChart) {
      donutChart.destroy();
      donutChart = null;
      container.innerHTML = '';
    }

    donutChart = new ApexCharts(container, {
      chart:   { type: 'donut', height: 190, animations: { enabled: false } },
      series:  [d.comp, d.proc, d.pend],
      labels:  ['Completadas', 'En proceso', 'Pendientes'],
      colors:  ['#28c76f', '#ff9f43', '#ea5455'],
      plotOptions: {
        pie: { donut: { size: '72%', labels: {
          show: true,
          total: {
            show: true, label: 'Total', color: textColor,
            formatter: () => String(d.total),
          },
          value: { fontSize: '18px', fontWeight: 700, color: textColor },
        }}}
      },
      legend:      { show: false },
      dataLabels:  { enabled: false },
      stroke:      { width: 2, colors: [bgColor] },
      tooltip:     { y: { formatter: v => v + ' actividades' } },
    });
    donutChart.render();
  }
  renderDonut('sci');

  // ── Stats panel lateral ──────────────────────────────────────────
  function updateSideStats(mod) {
    const d = modData[mod];
    document.getElementById('sideModLabel').textContent           = d.label;
    document.getElementById('sideTotal').textContent              = d.total;
    document.querySelector('[data-stat="success"]').textContent   = d.comp;
    document.querySelector('[data-stat="warning"]').textContent   = d.proc;
    document.querySelector('[data-stat="danger"]').textContent    = d.pend;
    const pct = v => d.total ? Math.round(v / d.total * 100) : 0;
    document.querySelector('[data-bar="success"]').style.width    = pct(d.comp) + '%';
    document.querySelector('[data-bar="warning"]').style.width    = pct(d.proc) + '%';
    document.querySelector('[data-bar="danger"]').style.width     = pct(d.pend) + '%';
  }

  // ── FIX #4: Paneles laterales dinámicos según módulo ────────────
  function updateSidePanels(mod) {
    const p = sidePanels[mod];
    if (mod === 'general') {
      // General: mostrar ambos paneles
      document.getElementById('panelRemediacion').style.display = '';
      document.getElementById('panelControl').style.display     = '';
    } else {
      // SCI → mostrar Remediación primero; Integridad → mostrar Logros primero
      const primary   = document.getElementById(p.id);
      const secondary = document.getElementById(p.other);
      // Reordenar en el DOM: primary arriba
      const parent = primary.parentNode;
      parent.insertBefore(primary, secondary);
      primary.style.display   = '';
      secondary.style.display = '';
    }
  }

  // ── FIX #5: Reordenar filas tabla según porcentaje del módulo ────
  function sortTableByMod(mod) {
    const tbody = document.getElementById('tblBody');
    const rows  = Array.from(tbody.querySelectorAll('tr[data-nombre]'));
    const attr  = mod === 'sci' ? 'sciPct' : (mod === 'integridad' ? 'intPct' : 'genPct');
    rows.sort((a, b) => (parseInt(b.dataset[attr]) || 0) - (parseInt(a.dataset[attr]) || 0));
    rows.forEach(r => tbody.appendChild(r));
  }

  // ── Switch de módulo ─────────────────────────────────────────────
  let currentMod = 'sci';

  window.switchMod = function(mod) {
    currentMod = mod;

    // Tabs activos
    document.querySelectorAll('.mod-tab-btn').forEach(btn => {
      btn.classList.remove('active-sci', 'active-int', 'active-gen');
    });
    const cls = mod === 'sci' ? 'active-sci' : (mod === 'integridad' ? 'active-int' : 'active-gen');
    document.querySelector(`[data-mod="${mod}"]`).classList.add(cls);

    // KPI panels + gauge bajo demanda
    document.querySelectorAll('.mod-panel').forEach(p => p.style.display = 'none');
    document.getElementById('kpi-' + mod).style.display = '';

    // Columnas de tabla
    document.querySelectorAll('.mod-col').forEach(el => el.style.setProperty('display', 'none', 'important'));
    document.querySelectorAll(`.mod-col[data-show="${mod}"]`).forEach(el => el.style.removeProperty('display'));

    // Avatares color
    document.querySelectorAll('.mod-avatar').forEach(av => {
      av.style.background = av.dataset[mod + 'Bg']    || 'rgba(108,117,125,.12)';
      av.style.color      = av.dataset[mod + 'Color'] || '#6c757d';
    });

    // Título tabla
    const meta = modMeta[mod];
    document.getElementById('tblTitle').textContent    = meta.tblTitle;
    document.getElementById('tblSubtitle').textContent = meta.tblSub;
    document.getElementById('tblMod').textContent      = meta.modLabel;
    document.getElementById('tblMod').style.color      = meta.modLabelColor;

    // Donut + stats + paneles laterales
    renderDonut(mod);
    updateSideStats(mod);
    updateSidePanels(mod); // FIX #4

    // FIX #5: reordenar tabla
    sortTableByMod(mod);

    // Re-aplicar filtros
    applyFilters();
  };

  // ── FIX #2: Filtros en tiempo real — bug string literal corregido ─
  const inputBuscar = document.getElementById('filterBuscar');
  const selEstado   = document.getElementById('filterEstado');
  const countEl     = document.getElementById('tblCount');

  function applyFilters() {
    const buscar = inputBuscar.value.toLowerCase().trim();
    const estado = selEstado.value; // 'success' | 'warning' | 'danger' | ''
    const rows   = document.querySelectorAll('#tblBody tr[data-nombre]');
    let visible  = 0;

    rows.forEach(row => {
      // FIX #2: comparación correcta según módulo activo
      let colorKey;
      if      (currentMod === 'sci')        colorKey = row.dataset.sciColor;
      else if (currentMod === 'integridad') colorKey = row.dataset.intColor;
      else                                  colorKey = row.dataset.genColor;

      const matchNombre = !buscar || row.dataset.nombre.includes(buscar);
      const matchEstado = !estado || colorKey === estado;

      if (matchNombre && matchEstado) {
        row.style.display = '';
        visible++;
      } else {
        row.style.display = 'none';
      }
    });

    const total = rows.length;
    countEl.textContent = visible === total
      ? `Mostrando ${total} unidades`
      : `Mostrando ${visible} de ${total} unidades`;
  }

  inputBuscar.addEventListener('input',  applyFilters);
  selEstado.addEventListener('change',   applyFilters);

  window.limpiarFiltros = function () {
    inputBuscar.value = '';
    selEstado.value   = '';
    applyFilters();
  };

  // Render inicial del sort
  sortTableByMod('sci');

});
</script>
@endsection
