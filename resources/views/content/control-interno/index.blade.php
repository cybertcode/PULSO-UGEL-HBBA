@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();

$hayFiltros = request()->hasAny(['eje_id','componente_id','pregunta_id','unidad_id','responsable_id','estado','prioridad','buscar','anio','fecha_desde','fecha_hasta']);
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Control Interno - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-style')
<style>
/* ── KPI Cards ────────────────────────────────────────── */
.kpi-card { border-radius: 14px; border: none; overflow: hidden; transition: transform .18s, box-shadow .18s; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.13); }
.kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1; }
.kpi-label { font-size: .72rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; opacity: .75; }
.kpi-sub { font-size: .8rem; font-weight: 600; }

/* ── Filtros card ─────────────────────────────────────── */
.filter-card { border-radius: 12px; border: 1px solid rgba(var(--bs-primary-rgb),.12); }
.filter-badge-count {
  position: absolute; top: -6px; right: -6px;
  width: 18px; height: 18px; border-radius: 50%;
  background: var(--bs-danger); color: #fff;
  font-size: 10px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
}

/* ── Tabla premium ────────────────────────────────────── */
.sci-table thead th {
  font-size: 11px; font-weight: 700; letter-spacing: .06em;
  text-transform: uppercase; color: var(--bs-secondary-color);
  background: rgba(var(--bs-secondary-rgb),.04);
  border-bottom: 2px solid rgba(var(--bs-secondary-rgb),.1);
  white-space: nowrap; padding: 12px 14px;
}
.sci-table tbody tr {
  transition: background .12s;
  border-left: 3px solid transparent;
}
.sci-table tbody tr:hover { background: rgba(var(--bs-primary-rgb),.03); }
.sci-table tbody tr.row-vencida  { border-left-color: var(--bs-danger); }
.sci-table tbody tr.row-completada { border-left-color: var(--bs-success); }
.sci-table tbody tr.row-observado { border-left-color: var(--bs-info); }
.sci-table tbody tr.row-en_proceso { border-left-color: var(--bs-warning); }
.sci-table tbody tr.row-pendiente { border-left-color: var(--bs-secondary); }
.sci-table tbody td { padding: 10px 14px; vertical-align: middle; }

/* ── Código chip ──────────────────────────────────────── */
.codigo-chip {
  font-family: monospace; font-size: 11px; font-weight: 700;
  color: var(--bs-primary);
  background: rgba(var(--bs-primary-rgb),.08);
  padding: 2px 8px; border-radius: 6px; white-space: nowrap;
}
.sgd-chip {
  font-size: 10px; color: var(--bs-secondary-color);
  background: rgba(var(--bs-secondary-rgb),.06);
  padding: 1px 6px; border-radius: 4px; white-space: nowrap;
  margin-top: 3px; display: inline-block;
}

/* ── Responsable badges ───────────────────────────────── */
.resp-row { display: flex; align-items: center; gap: 5px; margin-bottom: 3px; }
.resp-tipo {
  font-size: 9px; font-weight: 700; letter-spacing: .04em;
  padding: 2px 5px; border-radius: 4px; flex-shrink: 0;
  text-transform: uppercase;
}
.resp-tipo.p { background: rgba(var(--bs-primary-rgb),.12); color: var(--bs-primary); }
.resp-tipo.c { background: rgba(var(--bs-secondary-rgb),.12); color: var(--bs-secondary-color); }
.resp-tipo.s { background: rgba(var(--bs-info-rgb),.15); color: var(--bs-info); }
.resp-name { font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 105px; }

/* ── Progress slim ────────────────────────────────────── */
.prog-wrap { display: flex; align-items: center; gap: 6px; }
.prog-track { flex-grow: 1; height: 6px; background: rgba(var(--bs-secondary-rgb),.12); border-radius: 3px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 3px; transition: width .4s ease; }
.prog-pct { font-size: 11px; font-weight: 700; min-width: 32px; text-align: right; }

/* ── Fecha chip ───────────────────────────────────────── */
.fecha-chip { font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; white-space: nowrap; }
.dias-tag { font-size: 10px; margin-top: 3px; display: flex; align-items: center; gap: 3px; }

/* ── Estado pill ──────────────────────────────────────── */
.estado-pill { font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }

/* ── Acciones ─────────────────────────────────────────── */
.act-actions { display: flex; gap: 4px; flex-wrap: nowrap; }
.act-actions .btn { width: 30px; height: 30px; padding: 0; border-radius: 8px; }

/* ── Vencidas alert banner ────────────────────────────── */
.vencidas-banner {
  border-radius: 12px;
  background: linear-gradient(135deg, rgba(var(--bs-danger-rgb),.08), rgba(var(--bs-warning-rgb),.06));
  border: 1px solid rgba(var(--bs-danger-rgb),.18);
  padding: 14px 18px;
}

/* ── Paginación ───────────────────────────────────────── */
.pagination { margin: 0; }
.page-link { border-radius: 8px !important; margin: 0 2px; font-size: 13px; }

/* ── Modal header accent ──────────────────────────────── */
.modal-header-accent {
  background: linear-gradient(135deg, var(--bs-primary), color-mix(in srgb, var(--bs-primary) 70%, var(--bs-info)));
  color: #fff;
  border-radius: inherit;
}
.modal-header-accent .modal-title { color: #fff; }

/* ── Timeline historial ───────────────────────────────── */
.hist-item { display: flex; gap: 14px; padding-bottom: 16px; border-bottom: 1px solid rgba(var(--bs-secondary-rgb),.08); margin-bottom: 16px; }
.hist-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.hist-dot { width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
.hist-arrow { font-size: 11px; color: var(--bs-secondary-color); }

/* ── Empty state ──────────────────────────────────────── */
.empty-sci { padding: 60px 20px; text-align: center; color: var(--bs-secondary-color); }
.empty-sci .empty-icon { font-size: 3.5rem; opacity: .3; margin-bottom: 16px; }


/* ── Responsables builder ─────────────────────────────── */
.resp-builder { border: 1px solid rgba(var(--bs-secondary-rgb),.18); border-radius: 10px; padding: 10px; background: rgba(var(--bs-secondary-rgb),.03); min-height: 60px; }
.resp-row-item { display: flex; align-items: center; gap: 8px; padding: 6px 8px; background: var(--bs-body-bg); border: 1px solid rgba(var(--bs-secondary-rgb),.12); border-radius: 8px; margin-bottom: 6px; }
.resp-tipo-badge { font-size: 10px; font-weight: 700; padding: 3px 7px; border-radius: 6px; white-space: nowrap; flex-shrink: 0; cursor: pointer; }
.resp-tipo-badge.principal   { background: rgba(var(--bs-primary-rgb),.12); color: var(--bs-primary); }
.resp-tipo-badge.colaborador { background: rgba(var(--bs-secondary-rgb),.15); color: var(--bs-secondary-color); }
.resp-tipo-badge.supervisor  { background: rgba(var(--bs-info-rgb),.15); color: var(--bs-info); }
.resp-add-row { display: flex; gap: 8px; align-items: center; padding-top: 8px; border-top: 1px dashed rgba(var(--bs-secondary-rgb),.2); margin-top: 8px; }
.resp-empty-msg { text-align: center; color: var(--bs-secondary-color); font-size: 12px; padding: 10px 0; font-style: italic; }

/* ── Modal scroll fix ─────────────────────────────────── */
.modal-dialog-scrollable {
  height: calc(100% - 3.5rem) !important;
  max-height: calc(100% - 3.5rem) !important;
}
.modal-dialog-scrollable .modal-content {
  max-height: 100% !important;
  overflow: hidden !important;
  display: flex !important;
  flex-direction: column !important;
}
.modal-dialog-scrollable .modal-body {
  overflow-y: auto !important;
  flex: 1 1 auto !important;
  min-height: 0 !important;
}
</style>
@endsection

@section('content')

{{-- Errores de validación via SweetAlert --}}
@if($errors->any())
  <meta name="flash-errors" content="{{ addslashes($errors->first()) }}">
@endif

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Control Interno</li>
  </ol>
</nav>

{{-- Header --}}
<div class="pulso-page-header mb-6">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <h4 class="mb-1"><i class="ti tabler-shield-check me-2"></i>Sistema de Control Interno</h4>
      <p class="mb-0">Seguimiento y registro de actividades institucionales · {{ now()->year }}</p>
    </div>
    @can('control-interno.crear')
    <button class="btn btn-sm"
      style="background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.35);border-radius:10px;"
      data-bs-toggle="modal" data-bs-target="#modalNuevaActividad">
      <i class="ti tabler-plus me-1"></i>Nueva Actividad
    </button>
    @endcan
  </div>
</div>

{{-- ── KPI Cards ──────────────────────────────────────────────────────────── --}}
@php
$totalBase = max($stats['total'], 1);
$kpis = [
  ['k'=>'total',      'label'=>'Total SCI',   'sub'=>'Actividades registradas',  'grad'=>'linear-gradient(135deg,#667eea 0%,#764ba2 100%)', 'icon'=>'tabler-clipboard-list', 'extra'=>null],
  ['k'=>'completadas','label'=>'Completadas', 'sub'=>'Actividades finalizadas',  'grad'=>'linear-gradient(135deg,#11998e 0%,#38ef7d 100%)', 'icon'=>'tabler-circle-check',   'extra'=>'porcentaje'],
  ['k'=>'en_proceso', 'label'=>'En Proceso',  'sub'=>'Pendientes + en proceso',  'grad'=>'linear-gradient(135deg,#f7971e 0%,#ffd200 100%)', 'icon'=>'tabler-loader-2',       'extra'=>null],
  ['k'=>'observados', 'label'=>'Observados',  'sub'=>'Pendientes de revisión',   'grad'=>'linear-gradient(135deg,#4facfe 0%,#00f2fe 100%)', 'icon'=>'tabler-eye',            'extra'=>null],
  ['k'=>'vencidas',   'label'=>'Vencidas',    'sub'=>'Requieren atención',       'grad'=>'linear-gradient(135deg,#cb2d3e 0%,#ef473a 100%)', 'icon'=>'tabler-alarm-off',      'extra'=>null],
];
$porcentaje = round(($stats['completadas'] / $totalBase) * 100);
@endphp
<div class="row g-3 mb-4">
  @foreach($kpis as $kp)
  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:{{ $kp['grad'] }}">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">{{ $kp['label'] }}</div>
            <div class="kpi-value" id="kpi-{{ $kp['k'] }}">{{ $stats[$kp['k']] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
            <i class="ti {{ $kp['icon'] }}"></i>
          </div>
        </div>
        @if($kp['extra'] === 'porcentaje')
        <div class="d-flex align-items-center gap-2">
          <div class="progress flex-grow-1" style="height:4px;background:rgba(255,255,255,.25)">
            <div class="progress-bar bg-white" id="kpi-bar" style="width:{{ $porcentaje }}%"></div>
          </div>
          <span class="kpi-sub text-white-75" id="kpi-pct">{{ $porcentaje }}%</span>
        </div>
        @else
        <div class="kpi-sub text-white-75">{{ $kp['sub'] }}</div>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ── Banner vencidas próximas ──────────────────────────────────────────── --}}
@php
$proxVencer = \App\Models\Actividad::whereNotIn('estado', ['completada','observado'])
  ->where('modulo', 'sci')
  ->whereDate('fecha_limite', '>=', now())
  ->whereDate('fecha_limite', '<=', now()->addDays(7))
  ->orderBy('fecha_limite')
  ->with('sciPregunta.componente')
  ->limit(5)
  ->get();
@endphp
@if($proxVencer->isNotEmpty())
<div class="vencidas-banner mb-4 d-flex align-items-start gap-3 flex-wrap">
  <div class="flex-shrink-0 mt-1">
    <span class="badge bg-danger rounded-pill p-2"><i class="ti tabler-alarm icon-18px"></i></span>
  </div>
  <div class="flex-grow-1">
    <div class="fw-semibold text-danger mb-2" style="font-size:13px">
      <i class="ti tabler-clock-exclamation me-1"></i>{{ $proxVencer->count() }} actividad(es) vencen en los próximos 7 días
    </div>
    <div class="d-flex flex-wrap gap-2">
      @foreach($proxVencer as $pv)
      @php $dPv = (int) round(now()->diffInDays($pv->fecha_limite, false)); @endphp
      <span class="badge rounded-pill {{ $dPv <= 2 ? 'bg-danger' : 'bg-warning' }}" style="font-size:11px;font-weight:600;padding:5px 10px"
        title="{{ $pv->nombre }}">
        <i class="ti tabler-clock me-1"></i>{{ Str::limit($pv->nombre, 28) }}
        — {{ $dPv === 0 ? 'hoy' : $dPv.'d' }}
      </span>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- ── Banner evidencias rechazadas ────────────────────────────────────────── --}}
@if(($stats['ev_rechazadas'] ?? 0) > 0)
<div class="alert alert-danger d-flex align-items-center gap-3 py-2 px-3 mb-3" role="alert"
     style="border-radius:10px;border-left:4px solid #dc3545">
  <i class="ti tabler-file-x" style="font-size:1.4rem;flex-shrink:0"></i>
  <div class="flex-grow-1">
    <strong>{{ $stats['ev_rechazadas'] }} actividad(es)</strong> tienen evidencias rechazadas que requieren corrección.
  </div>
  <a href="{{ route('sci-evidencias', ['modulo' => 'sci', 'estado' => 'rechazado']) }}"
     class="btn btn-danger btn-sm ms-auto text-nowrap">
    <i class="ti tabler-refresh-alert me-1"></i>Corregir evidencias
  </a>
</div>
@endif

{{-- ── Filtros ───────────────────────────────────────────────────────────── --}}
<div class="card filter-card mb-4">
  <div class="card-body py-3">
    <form id="formFiltros" method="GET" action="{{ route('sci-control-interno') }}">
      <div class="row g-2 align-items-end">

        {{-- Año --}}
        <div class="col-md-1">
          <label class="form-label form-label-sm mb-1">Año</label>
          <select name="anio" id="filtroAnio" class="form-select">
            <option value="">Todos</option>
            @foreach($anios as $a)
            <option value="{{ $a }}" {{ request('anio') == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
          </select>
        </div>

        {{-- Eje --}}
        <div class="col-md-2">
          <label class="form-label form-label-sm mb-1">Eje</label>
          <select name="eje_id" id="filtroEje" class="form-select select2-filtro">
            <option value="">Todos los ejes</option>
            @foreach($ejes as $e)
            <option value="{{ $e->id }}" {{ request('eje_id') == $e->id ? 'selected' : '' }}>
              {{ Str::limit($e->nombre, 28) }} ({{ $e->anio }})
            </option>
            @endforeach
          </select>
        </div>

        {{-- Componente (carga dinámica) --}}
        <div class="col-md-2">
          <label class="form-label form-label-sm mb-1">Componente</label>
          <select name="componente_id" id="filtroComponente" class="form-select select2-filtro">
            <option value="">Todos</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ request('componente_id') == $c->id ? 'selected' : '' }}>
              {{ Str::limit($c->nombre, 26) }}
            </option>
            @endforeach
          </select>
        </div>

        {{-- Unidad --}}
        <div class="col-md-2">
          <label class="form-label form-label-sm mb-1">Unidad</label>
          <select name="unidad_id" id="filtroUnidad" class="form-select select2-filtro">
            <option value="">Todas</option>
            @foreach($unidades as $u)
            <option value="{{ $u->id }}" {{ request('unidad_id') == $u->id ? 'selected' : '' }}>
              {{ $u->sigla ?? Str::limit($u->nombre,18) }}
            </option>
            @endforeach
          </select>
        </div>

        {{-- Responsable --}}
        <div class="col-md-2">
          <label class="form-label form-label-sm mb-1">Responsable</label>
          <select name="responsable_id" id="filtroResponsable" class="form-select select2-filtro">
            <option value="">Todos</option>
            @foreach($responsables as $u)
            <option value="{{ $u->id }}" {{ request('responsable_id') == $u->id ? 'selected' : '' }}>
              {{ $u->name }}
            </option>
            @endforeach
          </select>
        </div>

        {{-- Estado --}}
        <div class="col-md-1">
          <label class="form-label form-label-sm mb-1">Estado</label>
          <select name="estado" id="filtroEstado" class="form-select">
            <option value="">Todos</option>
            @foreach(['pendiente'=>'Pendiente','en_proceso'=>'En Proceso','completada'=>'Completada','observado'=>'Observado','vencida'=>'Vencida'] as $v => $l)
            <option value="{{ $v }}" {{ request('estado') === $v ? 'selected' : '' }}>{{ Str::limit($l,8) }}</option>
            @endforeach
          </select>
        </div>

        {{-- Prioridad --}}
        <div class="col-md-1">
          <label class="form-label form-label-sm mb-1">Prioridad</label>
          <select name="prioridad" id="filtroPrioridad" class="form-select">
            <option value="">Todas</option>
            <option value="alta"  {{ request('prioridad') === 'alta'  ? 'selected' : '' }}>Alta</option>
            <option value="media" {{ request('prioridad') === 'media' ? 'selected' : '' }}>Media</option>
            <option value="baja"  {{ request('prioridad') === 'baja'  ? 'selected' : '' }}>Baja</option>
          </select>
        </div>

        {{-- Buscar + botones — col-md-3 para que el input sea legible --}}
        <div class="col-md-3">
          <label class="form-label form-label-sm mb-1">Buscar</label>
          <div class="input-group">
            <span class="input-group-text"><i class="ti tabler-search icon-14px"></i></span>
            <input type="text" name="buscar" id="filtroBuscar" class="form-control"
              value="{{ request('buscar') }}" placeholder="Código o nombre…">
            <button type="button" class="btn btn-primary px-2" id="btnFiltrosAvanzados"
              data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados"
              title="Filtros avanzados" style="position:relative">
              <i class="ti tabler-adjustments-horizontal icon-16px"></i>
              @if(request()->hasAny(['fecha_desde','fecha_hasta']))
              <span class="filter-badge-count">!</span>
              @endif
            </button>
            <button type="button"
               class="btn btn-label-secondary px-2 {{ $hayFiltros ? '' : 'invisible' }}"
               id="btnLimpiar" title="Limpiar filtros">
              <i class="ti tabler-x icon-14px"></i>
            </button>
          </div>
        </div>

      </div>{{-- /row principal --}}

      {{-- Filtros avanzados (collapse) --}}
      <div class="collapse {{ request()->hasAny(['fecha_desde','fecha_hasta']) ? 'show' : '' }}" id="filtrosAvanzados">
        <hr class="my-3">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label form-label-sm mb-1"><i class="ti tabler-calendar-event me-1"></i>Vence desde</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="ti tabler-calendar icon-14px"></i></span>
              <input type="text" id="filtroFechaDesde" class="form-control flatpickr-input"
                placeholder="dd/mm/aaaa" readonly>
              <input type="hidden" name="fecha_desde" id="filtroFechaDesdeVal" value="{{ request('fecha_desde') }}">
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label form-label-sm mb-1"><i class="ti tabler-calendar-event me-1"></i>Vence hasta</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="ti tabler-calendar icon-14px"></i></span>
              <input type="text" id="filtroFechaHasta" class="form-control flatpickr-input"
                placeholder="dd/mm/aaaa" readonly>
              <input type="hidden" name="fecha_hasta" id="filtroFechaHastaVal" value="{{ request('fecha_hasta') }}">
            </div>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
              <i class="ti tabler-filter me-1"></i>Aplicar filtros
            </button>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>

{{-- ── Tabla de actividades ─────────────────────────────────────────────── --}}
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center py-3">
    <div class="d-flex align-items-center gap-2">
      <i class="ti tabler-table text-primary icon-18px"></i>
      <h5 class="mb-0">Actividades de Control Interno</h5>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="badge bg-label-primary rounded-pill" id="badgeTotal">{{ $actividades->total() }} registros</span>
      <span class="badge bg-label-warning rounded-pill {{ $hayFiltros ? '' : 'd-none' }}" id="badgeFiltroActivo"><i class="ti tabler-filter me-1"></i>Filtro activo</span>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle sci-table" style="min-width:960px">
        <thead>
          <tr>
            <th style="width:115px">Código</th>
            <th style="min-width:200px">Actividad</th>
            <th style="min-width:140px">Componente</th>
            <th style="width:72px;text-align:center">Unidad</th>
            <th style="min-width:165px">Responsables</th>
            <th style="width:80px">Prioridad</th>
            <th style="width:105px">Vencimiento</th>
            <th style="width:130px">Avance</th>
            <th style="width:95px">Estado</th>
            <th style="width:105px">Acciones</th>
          </tr>
        </thead>
        <tbody id="tablaBody">
          @include('content.control-interno._tabla')
        </tbody>
      </table>
    </div>
  </div>

  <div id="tablaFooter">
    @if($actividades->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-between py-3">
      <span class="text-muted" style="font-size:13px" id="tablaContador">
        Mostrando {{ $actividades->firstItem() }}–{{ $actividades->lastItem() }} de {{ $actividades->total() }} registros
      </span>
      <div id="tablaPages">{{ $actividades->links() }}</div>
    </div>
    @endif
  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- Modal Nueva Actividad                                                   --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalNuevaActividad" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="{{ route('sci-control-interno.store') }}">
        @csrf
        <div class="modal-header modal-header-accent">
          <h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nueva Actividad SCI</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">

            <div class="col-12">
              <label class="form-label">Nombre de la Actividad <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-clipboard-text icon-16px"></i></span>
                <input type="text" name="nombre" class="form-control" placeholder="Descripción de la actividad" required>
              </div>
            </div>

            {{-- Cascada SCI: Año → Eje → Componente → Pregunta --}}
            <div class="col-md-3">
              <label class="form-label">Año <span class="text-danger">*</span></label>
              <input type="number" name="anio" id="nuevo_anio" class="form-control"
                value="{{ date('Y') }}" min="2020" max="2099" required>
            </div>

            <div class="col-md-9">
              <label class="form-label">Eje SCI <span class="text-danger">*</span></label>
              <select name="_eje_id" id="nuevo_eje" class="form-select select2-modal">
                <option value="">— Seleccionar eje —</option>
                @foreach($ejes as $e)
                <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->anio }})</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Componente <span class="text-danger">*</span></label>
              <select name="_comp_id" id="nuevo_componente" class="form-select select2-modal" disabled>
                <option value="">— Primero seleccione eje —</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Pregunta <span class="text-danger">*</span></label>
              <select name="sci_pregunta_id" id="nuevo_pregunta" class="form-select select2-modal" disabled required>
                <option value="">— Primero seleccione componente —</option>
              </select>
            </div>

            <input type="hidden" name="modulo" value="sci">

            <div class="col-md-6">
              <label class="form-label">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2-modal">
                <option value="">Sin unidad</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">
                <i class="ti tabler-users icon-16px me-1 text-primary"></i>Responsables
              </label>
              <div class="resp-builder" id="respListaNuevo">
                <div class="resp-empty-msg" id="respEmptyNuevo">Sin responsables asignados aún</div>
              </div>
              <div class="resp-add-row mt-2">
                <select class="form-select form-select-sm" id="respSelectNuevo" style="flex:1">
                  <option value="">— Agregar responsable —</option>
                  @foreach($responsables as $u)
                  <option value="{{ $u->id }}" data-name="{{ $u->name }}">{{ $u->name }}</option>
                  @endforeach
                </select>
                <select class="form-select form-select-sm" id="respTipoNuevo" style="width:130px">
                  <option value="principal">Principal</option>
                  <option value="colaborador">Colaborador</option>
                  <option value="supervisor">Supervisor</option>
                </select>
                <button type="button" class="btn btn-sm btn-primary" id="respAddBtnNuevo" title="Agregar">
                  <i class="ti tabler-plus"></i>
                </button>
                <button type="button" class="btn btn-sm btn-label-warning" id="respAddAllBtnNuevo" title="Asignar todos los usuarios">
                  <i class="ti tabler-users-plus me-1"></i>Todos
                </button>
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Fecha Inicio</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-calendar icon-16px"></i></span>
                <input type="date" name="fecha_inicio" class="form-control" value="{{ date('Y-m-d') }}">
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Fecha Límite <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-calendar-due icon-16px"></i></span>
                <input type="date" name="fecha_limite" class="form-control" required>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Prioridad</label>
              <select name="prioridad" class="form-select">
                <option value="alta">Alta</option>
                <option value="media" selected>Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">N° SGD / Expediente</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-file-text icon-16px"></i></span>
                <input type="text" name="numero_sgd" class="form-control" placeholder="Ej: SGD-2026-001">
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2"
                placeholder="Descripción detallada de la actividad…"></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Observaciones / Recomendaciones</label>
              <textarea name="observaciones" class="form-control" rows="2"
                placeholder="Detalle adicional, observaciones técnicas…"></textarea>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar Actividad</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- Modal Editar Actividad                                                  --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditarActividad" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" id="formEditarActividad">
        @csrf @method('PUT')
        <div class="modal-header modal-header-accent">
          <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Actividad</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">

            <div class="col-12">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-clipboard-text icon-16px"></i></span>
                <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
              </div>
            </div>

            {{-- Cascada SCI edición --}}
            <div class="col-md-3">
              <label class="form-label">Año <span class="text-danger">*</span></label>
              <input type="number" name="anio" id="edit_anio" class="form-control"
                min="2020" max="2099" required>
            </div>

            <div class="col-md-9">
              <label class="form-label">Eje SCI <span class="text-danger">*</span></label>
              <select name="_edit_eje_id" id="edit_eje" class="form-select select2-modal-edit">
                <option value="">— Seleccionar eje —</option>
                @foreach($ejes as $e)
                <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->anio }})</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Componente <span class="text-danger">*</span></label>
              <select name="_edit_comp_id" id="edit_componente" class="form-select select2-modal-edit">
                <option value="">— Seleccionar componente —</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Pregunta <span class="text-danger">*</span></label>
              <select name="sci_pregunta_id" id="edit_pregunta" class="form-select select2-modal-edit" required>
                <option value="">— Seleccionar pregunta —</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Unidad Orgánica</label>
              <select name="unidad_organica_id" id="edit_unidad" class="form-select select2-modal-edit">
                <option value="">Sin unidad</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">
                <i class="ti tabler-users icon-16px me-1 text-primary"></i>Responsables
              </label>
              <div class="resp-builder" id="respListaEditar">
                <div class="resp-empty-msg" id="respEmptyEditar">Sin responsables asignados aún</div>
              </div>
              <div class="resp-add-row mt-2">
                <select class="form-select form-select-sm" id="respSelectEditar" style="flex:1">
                  <option value="">— Agregar responsable —</option>
                  @foreach($responsables as $u)
                  <option value="{{ $u->id }}" data-name="{{ $u->name }}">{{ $u->name }}</option>
                  @endforeach
                </select>
                <select class="form-select form-select-sm" id="respTipoEditar" style="width:130px">
                  <option value="principal">Principal</option>
                  <option value="colaborador">Colaborador</option>
                  <option value="supervisor">Supervisor</option>
                </select>
                <button type="button" class="btn btn-sm btn-primary" id="respAddBtnEditar" title="Agregar">
                  <i class="ti tabler-plus"></i>
                </button>
                <button type="button" class="btn btn-sm btn-label-warning" id="respAddAllBtnEditar" title="Asignar todos los usuarios">
                  <i class="ti tabler-users-plus me-1"></i>Todos
                </button>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Fecha Inicio</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-calendar icon-16px"></i></span>
                <input type="date" name="fecha_inicio" id="edit_fechainicio" class="form-control">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Fecha Límite <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-calendar-due icon-16px"></i></span>
                <input type="date" name="fecha_limite" id="edit_fecha" class="form-control" required>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Estado</label>
              <select name="estado" id="edit_estado" class="form-select">
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="completada">Completada</option>
                <option value="observado">Observado</option>
                <option value="vencida">Vencida</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Prioridad</label>
              <select name="prioridad" id="edit_prioridad" class="form-select">
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Avance %</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-percent icon-16px"></i></span>
                <input type="number" name="avance" id="edit_avance" class="form-control" min="0" max="100">
              </div>
            </div>
            <div class="col-md-8">
              <label class="form-label">N° SGD / Expediente</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-file-text icon-16px"></i></span>
                <input type="text" name="numero_sgd" id="edit_sgd" class="form-control">
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="2"></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Observaciones / Recomendaciones</label>
              <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="2"></textarea>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Actualizar Actividad</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- Modal Historial de Cambios                                              --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalHistorial" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header modal-header-accent">
        <h5 class="modal-title"><i class="ti tabler-history me-2"></i>Historial de Cambios</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-3" id="historial_actividad_nombre"></p>
        <div id="historial_loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted small">Cargando historial…</p>
        </div>
        <div id="historial_content" style="display:none">
          <div id="historial_lista"></div>
        </div>
        <div id="historial_empty" style="display:none" class="empty-sci py-4">
          <div class="empty-icon" style="font-size:2rem"><i class="ti tabler-history"></i></div>
          <div class="fw-semibold">Sin historial registrado</div>
          <div class="text-body-secondary small">Aún no hay cambios auditados para esta actividad.</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Errores de validación ─────────────────────────────────────────────────
  const flashError = document.querySelector('meta[name="flash-errors"]')?.content;
  if (flashError) {
    Swal.fire({
      icon: 'error', title: 'Error de validación', text: flashError,
      customClass: { popup: 'rounded-3', confirmButton: 'btn btn-primary' },
      buttonsStyling: false,
    });
  }

  const BASE_URL    = '{{ route('sci-control-interno') }}';
  const form        = document.getElementById('formFiltros');
  const tablaBody   = document.getElementById('tablaBody');
  const tablaFooter = document.getElementById('tablaFooter');
  const badgeTotal  = document.getElementById('badgeTotal');
  const badgeFiltro = document.getElementById('badgeFiltroActivo');
  const btnLimpiar  = document.getElementById('btnLimpiar');

  let currentXhr = null;

  // ── Colectar parámetros ───────────────────────────────────────────────────
  function getParams() {
    const params = new URLSearchParams();
    new FormData(form).forEach((v, k) => {
      if (v && v !== '') params.set(k, v);
    });
    return params;
  }

  // ── Actualizar KPIs ───────────────────────────────────────────────────────
  function updateStats(stats) {
    ['total','completadas','en_proceso','observados','vencidas'].forEach(k => {
      const el = document.getElementById('kpi-' + k);
      if (el) el.textContent = stats[k] ?? 0;
    });
    const total = Math.max(stats.total, 1);
    const pct   = Math.round((stats.completadas / total) * 100);
    const bar   = document.getElementById('kpi-bar');
    const pctEl = document.getElementById('kpi-pct');
    if (bar)   bar.style.width = pct + '%';
    if (pctEl) pctEl.textContent = pct + '%';
  }

  // ── Actualizar URL ────────────────────────────────────────────────────────
  function pushUrl(params) {
    const qs = params.toString();
    history.pushState(null, '', BASE_URL + (qs ? '?' + qs : ''));
  }

  // ── Verificar si hay filtros activos ─────────────────────────────────────
  function hayFiltros(params) {
    return params.toString().length > 0;
  }

  // ── Cargar tabla via AJAX ─────────────────────────────────────────────────
  function cargarTabla(params, page) {
    if (page) params.set('page', page);

    if (currentXhr) currentXhr.abort();
    const ctrl = new AbortController();
    currentXhr = ctrl;

    tablaBody.style.opacity = '0.5';

    fetch(BASE_URL + '?' + params.toString(), {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      signal: ctrl.signal,
    })
    .then(r => r.json())
    .then(data => {
      currentXhr = null;
      tablaBody.style.opacity = '1';

      tablaBody.innerHTML = data.html;
      updateStats(data.stats);
      pushUrl(params);

      // Badge total
      badgeTotal.textContent = data.total + ' registros';

      // Badge filtro activo
      if (hayFiltros(params)) {
        badgeFiltro.classList.remove('d-none');
        btnLimpiar.classList.remove('invisible');
      } else {
        badgeFiltro.classList.add('d-none');
        btnLimpiar.classList.add('invisible');
      }

      // Footer paginación
      if (data.pages) {
        tablaFooter.innerHTML = `
          <div class="card-footer d-flex align-items-center justify-content-between py-3">
            <span class="text-muted" style="font-size:13px" id="tablaContador">
              Mostrando ${data.from}–${data.to} de ${data.total} registros
            </span>
            <div id="tablaPages">${data.pages}</div>
          </div>`;
      } else {
        tablaFooter.innerHTML = '';
      }

      bindTableEvents();
    })
    .catch(err => {
      if (err.name !== 'AbortError') {
        tablaBody.style.opacity = '1';
      }
    });
  }

  // ── Re-enlazar eventos de la tabla tras actualización ────────────────────
  function bindTableEvents() {
    // Limpiar filtros desde empty state
    document.getElementById('btnLimpiarEmpty')?.addEventListener('click', limpiarFiltros);

    // Botones historial
    tablaBody.querySelectorAll('.btn-historial').forEach(btn => {
      btn.addEventListener('click', function () {
        abrirHistorial(this.dataset.url, this.dataset.nombre);
      });
    });

    // Botones editar
    tablaBody.querySelectorAll('.btn-editar').forEach(btn => {
      btn.addEventListener('click', function () {
        abrirEditar(this);
      });
    });

    // Botones eliminar
    tablaBody.querySelectorAll('.btn-eliminar').forEach(btn => {
      btn.addEventListener('click', function () {
        confirmarEliminar(this.dataset.url);
      });
    });

    // Paginación delegada en el footer
    tablaFooter.querySelectorAll('a[href]').forEach(a => {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        const url   = new URL(this.href);
        const page  = url.searchParams.get('page');
        const p     = getParams();
        cargarTabla(p, page);
      });
    });
  }

  // ── Select2 filtros ───────────────────────────────────────────────────────
  document.querySelectorAll('.select2-filtro').forEach(el => {
    $(el).wrap('<div class="position-relative"></div>').select2({
      dropdownParent: $(el).parent(),
      width: '100%',
    });
    $(el).on('change', () => {
      // Para el Eje: primero actualizar componentes, luego filtrar
      if (el.id === 'filtroEje') return; // lo maneja el listener de cascada
      cargarTabla(getParams());
    });
  });

  // Buscar: disparar con debounce al escribir o Enter
  let searchTimer = null;
  document.getElementById('filtroBuscar')?.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => cargarTabla(getParams()), 400);
  });
  document.getElementById('filtroBuscar')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); clearTimeout(searchTimer); cargarTabla(getParams()); }
  });

  // Estado, Prioridad, Año
  ['filtroEstado','filtroPrioridad','filtroAnio'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', () => cargarTabla(getParams()));
  });

  // Cascada filtro: Eje → cargar componentes → luego filtrar
  $('#filtroEje').on('change', function () {
    const ejeId  = this.value;
    const compEl = document.getElementById('filtroComponente');
    $(compEl).empty().append('<option value="">Todos</option>');
    $(compEl).trigger('change.select2');

    if (!ejeId) { cargarTabla(getParams()); return; }

    fetch(`/api/sci/componentes?eje_id=${ejeId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        data.forEach(c => {
          const o = document.createElement('option');
          o.value = c.id; o.textContent = c.nombre;
          compEl.appendChild(o);
        });
        $(compEl).trigger('change.select2');
        cargarTabla(getParams());
      });
  });

  // Flatpickr: disparar al cerrar
  const fpOpts = {
    dateFormat: 'd/m/Y',
    locale: {
      firstDayOfWeek: 1,
      weekdays: { shorthand:['Do','Lu','Ma','Mi','Ju','Vi','Sa'], longhand:['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] },
      months: { shorthand:['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
        longhand:['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] },
    },
    static: false, allowInput: false,
  };
  const desdeVal = document.getElementById('filtroFechaDesdeVal');
  const hastaVal = document.getElementById('filtroFechaHastaVal');
  flatpickr('#filtroFechaDesde', { ...fpOpts, defaultDate: desdeVal.value || null,
    onClose(dates) { desdeVal.value = dates[0] ? dates[0].toISOString().slice(0,10) : ''; cargarTabla(getParams()); }
  });
  flatpickr('#filtroFechaHasta', { ...fpOpts, defaultDate: hastaVal.value || null,
    onClose(dates) { hastaVal.value = dates[0] ? dates[0].toISOString().slice(0,10) : ''; cargarTabla(getParams()); }
  });

  // Botón Aplicar filtros avanzados
  document.querySelector('#filtrosAvanzados .btn-primary')?.addEventListener('click', e => {
    e.preventDefault();
    cargarTabla(getParams());
    bootstrap.Collapse.getInstance(document.getElementById('filtrosAvanzados'))?.hide();
  });

  // ── Limpiar filtros ───────────────────────────────────────────────────────
  function limpiarFiltros() {
    form.reset();
    desdeVal.value = '';
    hastaVal.value = '';
    // Limpiar Select2
    document.querySelectorAll('.select2-filtro').forEach(el => {
      $(el).val('').trigger('change.select2');
    });
    cargarTabla(new URLSearchParams());
  }

  btnLimpiar?.addEventListener('click', limpiarFiltros);

  // ── Helpers modal ─────────────────────────────────────────────────────────
  function loadSelect(url, targetEl, placeholder) {
    const $el = $(targetEl);
    $el.empty().append(`<option value="">${placeholder}</option>`);
    targetEl.disabled = true;
    return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        data.forEach(item => {
          const opt = document.createElement('option');
          opt.value = item.id; opt.textContent = item.nombre;
          targetEl.appendChild(opt);
        });
        targetEl.disabled = false;
        $el.trigger('change.select2');
      });
  }

  // ── Select2 modales ───────────────────────────────────────────────────────
  const modalNuevo  = document.getElementById('modalNuevaActividad');
  const modalEditar = document.getElementById('modalEditarActividad');

  document.querySelectorAll('.select2-modal').forEach(el =>
    $(el).select2({ dropdownParent: modalNuevo, width: '100%' })
  );
  document.querySelectorAll('.select2-modal-edit').forEach(el =>
    $(el).select2({ dropdownParent: modalEditar, width: '100%' })
  );

  // ── Builder responsables ──────────────────────────────────────────────────
  function buildRespManager(listaId, emptyId, addBtnId, selectId, tipoId) {
    const lista  = document.getElementById(listaId);
    const empty  = document.getElementById(emptyId);
    const addBtn = document.getElementById(addBtnId);
    const sel    = document.getElementById(selectId);
    const tipo   = document.getElementById(tipoId);
    const LABELS = { principal: 'Principal', colaborador: 'Colaborador', supervisor: 'Supervisor' };

    function refresh() { empty.style.display = lista.querySelectorAll('.resp-row-item').length ? 'none' : 'block'; }

    function addRow(userId, userName, tipoVal) {
      if (!userId || lista.querySelector(`.resp-row-item[data-uid="${userId}"]`)) return;
      const div = document.createElement('div');
      div.className = 'resp-row-item'; div.dataset.uid = userId;
      div.innerHTML = `
        <input type="hidden" name="responsables[]" value="${userId}">
        <input type="hidden" name="tipos[${userId}]" value="${tipoVal}" class="tipo-hidden">
        <span class="resp-tipo-badge ${tipoVal}">${LABELS[tipoVal]}</span>
        <span class="flex-grow-1" style="font-size:13px">${userName}</span>
        <div class="btn-group btn-group-sm">
          <button type="button" class="btn btn-sm btn-icon btn-label-secondary btn-change-tipo" style="height:26px;width:26px;padding:0">
            <i class="ti tabler-arrows-exchange icon-12px"></i>
          </button>
          <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove" style="height:26px;width:26px;padding:0">
            <i class="ti tabler-x icon-12px"></i>
          </button>
        </div>`;
      div.querySelector('.btn-remove').addEventListener('click', () => { div.remove(); refresh(); });
      div.querySelector('.btn-change-tipo').addEventListener('click', () => {
        const order = ['principal','colaborador','supervisor'];
        const h = div.querySelector('.tipo-hidden');
        const b = div.querySelector('.resp-tipo-badge');
        const next = order[(order.indexOf(h.value) + 1) % order.length];
        h.value = next; b.className = `resp-tipo-badge ${next}`; b.textContent = LABELS[next];
      });
      lista.appendChild(div); refresh();
    }

    addBtn.addEventListener('click', () => {
      const opt = sel.options[sel.selectedIndex];
      if (!opt.value) return;
      addRow(opt.value, opt.dataset.name, tipo.value);
      sel.value = '';
    });

    return {
      addRow,
      addAll: tipoVal => Array.from(sel.options).forEach(o => { if (o.value) addRow(o.value, o.dataset.name, tipoVal); }),
      clear:  () => { lista.querySelectorAll('.resp-row-item').forEach(r => r.remove()); refresh(); },
    };
  }

  const respNuevo  = buildRespManager('respListaNuevo',  'respEmptyNuevo',  'respAddBtnNuevo',  'respSelectNuevo',  'respTipoNuevo');
  const respEditar = buildRespManager('respListaEditar', 'respEmptyEditar', 'respAddBtnEditar', 'respSelectEditar', 'respTipoEditar');

  document.getElementById('respAddAllBtnNuevo')?.addEventListener('click',  () => respNuevo.addAll(document.getElementById('respTipoNuevo').value));
  document.getElementById('respAddAllBtnEditar')?.addEventListener('click', () => respEditar.addAll(document.getElementById('respTipoEditar').value));
  modalNuevo.addEventListener('show.bs.modal', () => respNuevo.clear());

  // Fix de scroll manejado globalmente en main.js (shown.bs.modal)

  // ── Cascada modal NUEVO ───────────────────────────────────────────────────
  $('#nuevo_eje').on('change', function () {
    const ejeId = this.value, compEl = document.getElementById('nuevo_componente'), pregEl = document.getElementById('nuevo_pregunta');
    $(pregEl).empty().append('<option value="">— Primero seleccione componente —</option>'); pregEl.disabled = true;
    if (!ejeId) { compEl.innerHTML = '<option value="">— Primero seleccione eje —</option>'; compEl.disabled = true; return; }
    loadSelect(`/api/sci/componentes?eje_id=${ejeId}`, compEl, '— Seleccionar componente —');
  });
  $('#nuevo_componente').on('change', function () {
    const compId = this.value, pregEl = document.getElementById('nuevo_pregunta');
    if (!compId) { $(pregEl).empty().append('<option value="">— Primero seleccione componente —</option>'); pregEl.disabled = true; return; }
    loadSelect(`/api/sci/preguntas?componente_id=${compId}`, pregEl, '— Seleccionar pregunta —');
  });

  // ── Cascada modal EDITAR ──────────────────────────────────────────────────
  $('#edit_eje').on('change', function () {
    const ejeId = this.value, compEl = document.getElementById('edit_componente'), pregEl = document.getElementById('edit_pregunta');
    $(pregEl).empty().append('<option value="">— Seleccionar pregunta —</option>');
    if (!ejeId) return;
    loadSelect(`/api/sci/componentes?eje_id=${ejeId}`, compEl, '— Seleccionar componente —');
  });
  $('#edit_componente').on('change', function () {
    const compId = this.value, pregEl = document.getElementById('edit_pregunta');
    if (!compId) return;
    loadSelect(`/api/sci/preguntas?componente_id=${compId}`, pregEl, '— Seleccionar pregunta —');
  });

  // ── Abrir modal editar ────────────────────────────────────────────────────
  async function abrirEditar(btn) {
    const f = document.getElementById('formEditarActividad');
    f.action = '{{ url('control-interno') }}/' + btn.dataset.id;

    document.getElementById('edit_nombre').value        = btn.dataset.nombre;
    document.getElementById('edit_anio').value          = btn.dataset.anio || '{{ date("Y") }}';
    document.getElementById('edit_fecha').value         = btn.dataset.fecha;
    document.getElementById('edit_fechainicio').value   = btn.dataset.fechainicio || '';
    document.getElementById('edit_avance').value        = btn.dataset.avance;
    document.getElementById('edit_sgd').value           = btn.dataset.sgd || '';
    document.getElementById('edit_descripcion').value   = btn.dataset.descripcion || '';
    document.getElementById('edit_observaciones').value = btn.dataset.observaciones || '';

    const set = (id, val) => { const el = document.getElementById(id); if (el && val) { el.value = val; $(el).trigger('change'); } };
    set('edit_unidad', btn.dataset.unidad); set('edit_estado', btn.dataset.estado); set('edit_prioridad', btn.dataset.prioridad);

    const ejeId = btn.dataset.ejeId, compId = btn.dataset.componenteId, pregId = btn.dataset.preguntaId;
    if (ejeId) {
      $('#edit_eje').val(ejeId).trigger('change');
      if (compId) {
        await loadSelect(`/api/sci/componentes?eje_id=${ejeId}`, document.getElementById('edit_componente'), '— Seleccionar componente —');
        $('#edit_componente').val(compId).trigger('change.select2');
        if (pregId) {
          await loadSelect(`/api/sci/preguntas?componente_id=${compId}`, document.getElementById('edit_pregunta'), '— Seleccionar pregunta —');
          $('#edit_pregunta').val(pregId).trigger('change.select2');
        }
      }
    }

    respEditar.clear();
    (btn.dataset.responsablesJson ? JSON.parse(btn.dataset.responsablesJson) : [])
      .forEach(r => respEditar.addRow(r.id, r.name, r.tipo));

    new bootstrap.Modal(modalEditar).show();
  }

  // ── Historial ─────────────────────────────────────────────────────────────
  const campoIconos = {
    estado: { icon:'tabler-toggle-right', color:'primary' }, avance: { icon:'tabler-chart-bar', color:'success' },
    prioridad: { icon:'tabler-flag', color:'warning' }, responsables: { icon:'tabler-users', color:'info' },
    nombre: { icon:'tabler-pencil', color:'secondary' }, fecha_limite: { icon:'tabler-calendar', color:'danger' },
    observaciones: { icon:'tabler-notes', color:'secondary' },
  };

  function abrirHistorial(url, nombre) {
    document.getElementById('historial_actividad_nombre').textContent = nombre;
    document.getElementById('historial_loading').style.display = 'block';
    document.getElementById('historial_content').style.display = 'none';
    document.getElementById('historial_empty').style.display   = 'none';
    new bootstrap.Modal(document.getElementById('modalHistorial')).show();

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        document.getElementById('historial_loading').style.display = 'none';
        if (!data.length) { document.getElementById('historial_empty').style.display = 'block'; return; }
        document.getElementById('historial_lista').innerHTML = data.map(h => {
          const cfg = campoIconos[h.campo] || { icon:'tabler-edit', color:'secondary' };
          const fecha = h.created_at ? new Date(h.created_at).toLocaleString('es-PE') : '—';
          const usuario = h.usuario?.name ?? 'Sistema';
          const label = h.campo_label ?? h.campo, ant = h.valor_anterior ?? '—', nvo = h.valor_nuevo ?? '—';
          return `<div class="hist-item">
            <div class="hist-dot bg-label-${cfg.color}"><i class="ti ${cfg.icon} text-${cfg.color}"></i></div>
            <div class="flex-grow-1">
              <div class="fw-semibold" style="font-size:13px">${label}</div>
              <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                <span class="badge bg-label-secondary" style="max-width:200px;overflow:hidden;text-overflow:ellipsis" title="${ant}">${ant}</span>
                <i class="ti tabler-arrow-right hist-arrow"></i>
                <span class="badge bg-label-${cfg.color}" style="max-width:200px;overflow:hidden;text-overflow:ellipsis" title="${nvo}">${nvo}</span>
              </div>
              <div class="text-muted mt-1" style="font-size:11px">
                <i class="ti tabler-user icon-12px me-1"></i>${usuario}
                <span class="mx-1">·</span>
                <i class="ti tabler-clock icon-12px me-1"></i>${fecha}
              </div>
            </div>
          </div>`;
        }).join('');
        document.getElementById('historial_content').style.display = 'block';
      })
      .catch(() => {
        document.getElementById('historial_loading').style.display = 'none';
        document.getElementById('historial_empty').style.display   = 'block';
      });
  }

  // ── Eliminar via AJAX ─────────────────────────────────────────────────────
  function confirmarEliminar(url) {
    Swal.fire({
      title: '¿Eliminar actividad?', text: 'Esta acción no se puede deshacer.', icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '<i class="ti tabler-trash me-1"></i>Sí, eliminar',
      cancelButtonText: 'Cancelar',
      customClass: { popup:'rounded-3', confirmButton:'btn btn-danger me-2', cancelButton:'btn btn-label-secondary' },
      buttonsStyling: false,
    }).then(r => {
      if (!r.isConfirmed) return;
      fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_method=DELETE',
      })
      .then(res => res.ok ? cargarTabla(getParams()) : Promise.reject())
      .catch(() => Swal.fire({ icon:'error', title:'Error', text:'No se pudo eliminar la actividad.' }));
    });
  }

  // ── Enlazar tabla inicial ─────────────────────────────────────────────────
  bindTableEvents();

  // Tras guardar en modal (nueva / editar): el form hace POST y recarga — OK
  // Los modales de nueva actividad y edición usan submit normal (back()->with('success'))

});
</script>
@endsection
