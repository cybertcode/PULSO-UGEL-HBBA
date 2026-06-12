@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Reportes — PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/nouislider/nouislider.scss',
])
@endsection
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/nouislider/nouislider.js',
])
@endsection

@section('page-style')
<style>
/* ─── KPI Cards (mismo patrón mis-actividades) ───────────── */
.kpi-card            { border-radius:14px;border:none;overflow:hidden;transition:transform .18s,box-shadow .18s }
.kpi-card:hover      { transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.12) }
.kpi-icon            { width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.35rem;flex-shrink:0 }
.kpi-value           { font-size:1.9rem;font-weight:700;line-height:1 }
.kpi-label           { font-size:.69rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;opacity:.72 }
.kpi-sub             { font-size:.76rem;font-weight:600 }

/* ─── Filtros ────────────────────────────────────────────── */
.filter-card         { border-radius:14px;border:1px solid rgba(0,0,0,.06) }
/* form-label usa el default de Vuexy: .75rem, fw-medium */

/* ─── Mod tabs — misma altura que form-select-sm ──────────── */
.mod-tabs            { display:inline-flex;gap:.2rem;padding:.2rem;background:rgba(0,0,0,.04);border-radius:8px;
                       height:31px;align-items:center }
.mod-tab             { padding:0 .6rem;border-radius:6px;font-size:.76rem;font-weight:600;
                       cursor:pointer;border:none;background:transparent;transition:all .15s;color:#6e6b7b;
                       white-space:nowrap;display:flex;align-items:center;height:100% }
.mod-tab.active      { background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.10) }
.mod-tab[data-mod="sci"].active        { color:#696cff }
.mod-tab[data-mod="integridad"].active { color:#28c76f }
.mod-tab[data-mod=""].active           { color:var(--bs-primary) }
[data-bs-theme=dark] .mod-tabs         { background:rgba(255,255,255,.06) }
[data-bs-theme=dark] .mod-tab.active   { background:rgba(255,255,255,.10) }

/* ─── Chart + resumen cards ──────────────────────────────── */
.rep-card            { border-radius:14px;border:1px solid rgba(0,0,0,.06);overflow:hidden }
.mod-strip-sci       { border-left:4px solid #696cff }
.mod-strip-int       { border-left:4px solid #28c76f }

/* ─── Ranking ────────────────────────────────────────────── */
.rank-row            { display:flex;align-items:center;gap:12px;padding:.5rem 0 }
.rank-row + .rank-row{ border-top:1px solid rgba(0,0,0,.05) }
.rank-label          { font-size:.78rem;font-weight:600;min-width:70px;text-align:right;flex-shrink:0 }
.rank-bar-bg         { flex:1;height:8px;border-radius:4px;background:rgba(0,0,0,.06);overflow:hidden }
.rank-bar-fg         { height:100%;border-radius:4px;transition:width .5s ease }
.rank-pct            { font-size:.78rem;font-weight:700;min-width:36px;text-align:right;flex-shrink:0 }
.rank-sub            { font-size:.66rem;color:#a1a5b7;text-align:right }

/* ─── Tabla ──────────────────────────────────────────────── */
.rep-table thead th  { font-size:.69rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;border-bottom-width:1px;padding:.65rem 1rem }
.rep-table tbody td  { vertical-align:middle;padding:.6rem 1rem }
.rep-table tbody tr  { transition:background .12s }
.mod-badge-sci       { background:rgba(105,108,255,.12);color:#696cff;font-size:.68rem;border-radius:6px;padding:.2em .5em;font-weight:700 }
.mod-badge-int       { background:rgba(40,199,111,.12);color:#28c76f;font-size:.68rem;border-radius:6px;padding:.2em .5em;font-weight:700 }
.avance-bar-wrap     { width:64px;height:5px;border-radius:3px;background:rgba(0,0,0,.07);overflow:hidden;display:inline-block }
.avance-bar-fill     { height:100%;border-radius:3px }
.sem-dot             { width:10px;height:10px;border-radius:50%;display:inline-block }

/* ─── Estado pills ───────────────────────────────────────── */
.estado-pill         { font-size:.69rem;padding:.22em .6em;border-radius:20px;font-weight:700;letter-spacing:.02em;white-space:nowrap }

/* ─── Dias chip ──────────────────────────────────────────── */
.dias-chip           { display:inline-flex;align-items:center;gap:.18rem;font-size:.68rem;font-weight:700;padding:.15em .48em;border-radius:20px }

/* ─── Loading overlay ────────────────────────────────────── */
.rep-grid-wrap       { position:relative }
.rep-grid-wrap.is-loading { pointer-events:none }
.rep-grid-wrap.is-loading::after {
  content:'';position:absolute;inset:0;
  background:rgba(255,255,255,.55);border-radius:14px;z-index:9
}
[data-bs-theme=dark] .rep-grid-wrap.is-loading::after { background:rgba(20,20,30,.45) }
.rep-spinner {
  position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
  z-index:10;display:none
}
.rep-grid-wrap.is-loading .rep-spinner { display:flex;align-items:center;gap:.5rem }

/* ─── Empty state ────────────────────────────────────────── */
.empty-icon          { width:72px;height:72px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.8rem }

/* ─── Donut overlay text ─────────────────────────────────── */
#donutCenter         { position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none }

/* ─── Paginación ─────────────────────────────────────────── */
#repPaginacion:not(:empty) { border-top:1px solid rgba(0,0,0,.06) }
#repPaginacion .pagination  { margin-bottom:0 }
#repPaginacion .page-link   { font-size:.78rem;padding:.3rem .6rem;border-radius:7px!important;border:none;margin:0 2px;color:#6e6b7b;background:rgba(0,0,0,.04) }
#repPaginacion .page-item.active .page-link { background:var(--bs-primary);color:#fff;box-shadow:0 3px 10px rgba(105,108,255,.35) }
#repPaginacion .page-item.disabled .page-link { opacity:.45 }
#repPaginacion .page-link:hover:not(.disabled) { background:rgba(105,108,255,.1);color:var(--bs-primary) }
</style>
@endsection

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Reportes</li>
  </ol>
</nav>

{{-- ═══════════════════════════════════════════════════════════
     HEADER: título + acciones exportar
════════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
      <span class="d-flex align-items-center justify-content-center rounded-3"
        style="width:38px;height:38px;background:linear-gradient(135deg,#667eea,#764ba2)">
        <i class="ti tabler-chart-bar text-white" style="font-size:1.1rem"></i>
      </span>
      Reportes de Avance
    </h4>
    <p class="mb-0 text-muted ms-1 mt-1" style="font-size:.82rem">
      <i class="ti tabler-refresh me-1 text-success"></i>Tiempo real ·
      <span id="hdrAnio" class="fw-semibold text-body">{{ $anio }}</span> ·
      <span id="hdrModulo" class="fw-semibold text-body">{{ $modulo ? ($modulo==='sci' ? 'Control Interno (SCI)' : 'Modelo de Integridad') : 'Todos los módulos' }}</span>
    </p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <button id="btnExportExcel" class="btn btn-sm btn-label-success">
      <i class="ti tabler-file-spreadsheet me-1"></i>Excel
    </button>
    <button id="btnExportPdf" class="btn btn-sm btn-label-danger">
      <i class="ti tabler-file-type-pdf me-1"></i>PDF
    </button>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     FILTROS
════════════════════════════════════════════════════════════ --}}
@php
  $hayFiltros       = request()->hasAny(['estado','unidad_organica_id','prioridad','buscar','fecha_desde','fecha_hasta','avance_min','avance_max']);
  $filtrosAvanzados = request()->hasAny(['fecha_desde','fecha_hasta','avance_min','avance_max','prioridad']);
  $nFiltros = collect(['modulo','estado','unidad_organica_id','prioridad','buscar','fecha_desde','fecha_hasta','avance_min','avance_max'])
    ->filter(fn($k) => request()->filled($k))->count();
@endphp

<div class="card filter-card mb-4">
  <div class="card-body py-3 px-3">

    {{-- Fila principal de filtros --}}
    <div class="row g-2 align-items-center">

      {{-- Tabs módulo --}}
      <div class="col-auto">
        <div class="mod-tabs">
          <button class="mod-tab {{ !$modulo ? 'active':'' }}" data-mod="" type="button">
            <i class="ti tabler-layout-grid"></i><span class="ms-1">Todos</span>
          </button>
          <button class="mod-tab {{ $modulo==='sci' ? 'active':'' }}" data-mod="sci" type="button">
            <i class="ti tabler-shield-check"></i><span class="ms-1">SCI</span>
          </button>
          <button class="mod-tab {{ $modulo==='integridad' ? 'active':'' }}" data-mod="integridad" type="button">
            <i class="ti tabler-heart-handshake"></i><span class="ms-1">Integridad</span>
          </button>
        </div>
      </div>

      {{-- Año --}}
      <div class="col-auto">
        <select id="filtroAnio" class="form-select form-select-sm" style="width:86px">
          @foreach($anios as $a)
          <option value="{{ $a }}" {{ $anio==$a ? 'selected':'' }}>{{ $a }}</option>
          @endforeach
        </select>
      </div>

      {{-- Estado --}}
      <div class="col-auto">
        <select id="filtroEstado" class="form-select form-select-sm" style="width:128px">
          <option value="">Estado</option>
          <option value="pendiente"  {{ $estado==='pendiente'  ?'selected':'' }}>Pendiente</option>
          <option value="en_proceso" {{ $estado==='en_proceso' ?'selected':'' }}>En Proceso</option>
          <option value="completada" {{ $estado==='completada' ?'selected':'' }}>Completada</option>
          <option value="vencida"    {{ $estado==='vencida'    ?'selected':'' }}>Vencida</option>
          <option value="observado"  {{ $estado==='observado'  ?'selected':'' }}>Observado</option>
        </select>
      </div>

      {{-- Unidad orgánica — flex --}}
      <div class="col">
        <select id="filtroUnidad" class="select2 form-select form-select-sm" data-allow-clear="true">
          <option value="">Unidad Orgánica</option>
          @foreach($unidades as $u)
          <option value="{{ $u->id }}" {{ $unidad==$u->id ? 'selected':'' }}>
            {{ $u->sigla }} — {{ $u->nombre }}
          </option>
          @endforeach
        </select>
      </div>

      {{-- Buscar --}}
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <span class="input-group-text"><i class="ti tabler-search"></i></span>
          <input id="filtroBuscar" type="text" class="form-control"
            value="{{ $buscar ?? '' }}"
            placeholder="Nombre o código..." autocomplete="off">
          <span class="input-group-text" id="repSpinner" style="display:none">
            <span class="spinner-border spinner-border-sm text-primary" style="width:.75rem;height:.75rem"></span>
          </span>
        </div>
      </div>

      {{-- Botones --}}
      <div class="col-auto d-flex gap-1">
        <button type="button" id="btnFiltrosAvanzados"
          class="btn btn-sm {{ $filtrosAvanzados ? 'btn-primary' : 'btn-outline-secondary' }} position-relative"
          data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados"
          aria-expanded="{{ $filtrosAvanzados ? 'true':'false' }}"
          title="Más filtros">
          <i class="ti tabler-adjustments-horizontal"></i>
          @if($filtrosAvanzados)
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="font-size:.55rem;min-width:.9rem;line-height:1.3">{{ $nFiltros }}</span>
          @endif
        </button>
        <button id="btnLimpiar" type="button"
          class="btn btn-sm btn-outline-danger {{ $hayFiltros||$modulo ? '':'invisible' }}"
          title="Limpiar filtros">
          <i class="ti tabler-x"></i>
        </button>
      </div>
    </div>

    {{-- Filtros avanzados --}}
    <div class="collapse {{ $filtrosAvanzados ? 'show':'' }}" id="filtrosAvanzados">
      <hr class="my-2" style="border-style:dashed;opacity:.2">
      <div class="row g-2 align-items-center">

        {{-- Prioridad --}}
        <div class="col-auto">
          <select id="filtroPrioridad" class="form-select form-select-sm" style="width:120px">
            <option value="">Prioridad</option>
            <option value="alta"  {{ ($prioridad??'')=='alta'  ?'selected':'' }}>Alta</option>
            <option value="media" {{ ($prioridad??'')=='media' ?'selected':'' }}>Media</option>
            <option value="baja"  {{ ($prioridad??'')=='baja'  ?'selected':'' }}>Baja</option>
          </select>
        </div>

        {{-- Vence desde --}}
        <div class="col-auto">
          <div class="input-group input-group-sm" style="width:158px">
            <span class="input-group-text"><i class="ti tabler-calendar-event"></i></span>
            <input id="filtroFechaDesde" type="text" class="form-control"
              placeholder="Desde..." readonly
              value="{{ isset($fecha_desde) && $fecha_desde ? \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') : '' }}">
          </div>
          <input type="hidden" id="filtroFechaDesdeVal" value="{{ $fecha_desde ?? '' }}">
        </div>

        {{-- Vence hasta --}}
        <div class="col-auto">
          <div class="input-group input-group-sm" style="width:158px">
            <span class="input-group-text"><i class="ti tabler-calendar-event"></i></span>
            <input id="filtroFechaHasta" type="text" class="form-control"
              placeholder="Hasta..." readonly
              value="{{ isset($fecha_hasta) && $fecha_hasta ? \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') : '' }}">
          </div>
          <input type="hidden" id="filtroFechaHastaVal" value="{{ $fecha_hasta ?? '' }}">
        </div>

        {{-- Rango avance --}}
        <div class="col d-flex align-items-center gap-2">
          <span class="text-muted text-nowrap" style="font-size:.74rem">Avance</span>
          <div id="sliderAvance" class="noUi-primary flex-grow-1"></div>
          <span class="badge bg-label-primary rounded-pill text-nowrap" id="avanceRangeLabel" style="font-size:.7rem">
            {{ $avance_min ?? 0 }}%–{{ $avance_max ?? 100 }}%
          </span>
          <input type="hidden" id="filtroAvanceMin" value="{{ $avance_min ?? 0 }}">
          <input type="hidden" id="filtroAvanceMax" value="{{ $avance_max ?? 100 }}">
        </div>

      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     KPIs: 6 tarjetas en gradiente
════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#667eea,#764ba2)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">Total</div>
            <div class="kpi-value" id="kpi-total">{{ $stats['total'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
            <i class="ti tabler-clipboard-list"></i>
          </div>
        </div>
        <div class="kpi-sub" style="opacity:.8">Actividades</div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">Completadas</div>
            <div class="kpi-value" id="kpi-completadas">{{ $stats['completadas'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
            <i class="ti tabler-circle-check"></i>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <div class="progress flex-grow-1" style="height:3px;background:rgba(255,255,255,.25)">
            <div class="progress-bar bg-white" id="kpi-bar" style="width:{{ $stats['porcentaje'] }}%"></div>
          </div>
          <span class="kpi-sub" style="opacity:.8" id="kpi-pct">{{ $stats['porcentaje'] }}%</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">En Proceso</div>
            <div class="kpi-value" id="kpi-en_proceso">{{ $stats['en_proceso'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
            <i class="ti tabler-loader-2"></i>
          </div>
        </div>
        <div class="kpi-sub" style="opacity:.8">En desarrollo</div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#a8c0ff,#3f2b96)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">Pendientes</div>
            <div class="kpi-value" id="kpi-pendientes">{{ $stats['pendientes'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
            <i class="ti tabler-clock-pause"></i>
          </div>
        </div>
        <div class="kpi-sub" style="opacity:.8">Sin iniciar</div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#cb2d3e,#ef473a)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">Vencidas</div>
            <div class="kpi-value" id="kpi-vencidas">{{ $stats['vencidas'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
            <i class="ti tabler-alarm-off"></i>
          </div>
        </div>
        <div class="kpi-sub" style="opacity:.8">
          {{ $stats['vencidas'] > 0 ? 'Atención urgente' : 'Sin vencidas' }}
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">Sin Evidencia</div>
            <div class="kpi-value" id="kpi-sin_ev">{{ $stats['sin_ev'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
            <i class="ti tabler-file-off"></i>
          </div>
        </div>
        <div class="kpi-sub" style="opacity:.8">Sin respaldo</div>
      </div>
    </div>
  </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     GRÁFICAS + RESUMEN + RANKING (layout 3 columnas)
════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

  {{-- Gráfica barras mensual: col 6 --}}
  <div class="col-lg-6">
    <div class="card rep-card h-100">
      <div class="card-header d-flex align-items-center justify-content-between py-3 px-4" style="border-bottom:1px solid rgba(0,0,0,.06)">
        <div>
          <h6 class="mb-0 fw-bold">Actividades por Mes</h6>
          <small class="text-muted">Completadas vs Pendientes — <span class="js-anio-label">{{ $anio }}</span></small>
        </div>
        <div class="d-flex gap-1">
          <span class="badge rounded-pill" style="background:rgba(40,199,111,.12);color:#28c76f;font-size:.68rem">
            <i class="ti tabler-square-filled me-1" style="font-size:.55rem"></i>Completadas
          </span>
          <span class="badge rounded-pill" style="background:rgba(105,108,255,.12);color:#696cff;font-size:.68rem">
            <i class="ti tabler-square-filled me-1" style="font-size:.55rem"></i>Pendientes
          </span>
        </div>
      </div>
      <div class="card-body px-2 pb-2 pt-1">
        <div id="chartMensual"></div>
      </div>
    </div>
  </div>

  {{-- Donut cumplimiento global + resumen módulos: col 3 --}}
  <div class="col-lg-3">
    <div class="card rep-card h-100">
      <div class="card-header py-3 px-4" style="border-bottom:1px solid rgba(0,0,0,.06)">
        <h6 class="mb-0 fw-bold">Cumplimiento Global</h6>
        <small class="text-muted">% completado / total</small>
      </div>
      <div class="card-body d-flex flex-column align-items-center justify-content-center py-3 px-3 gap-3">
        {{-- Donut --}}
        <div style="position:relative;width:150px;height:150px">
          <div id="chartDonut"></div>
          <div id="donutCenter">
            <div class="fw-bold" style="font-size:1.5rem;line-height:1" id="donutPct">{{ $stats['porcentaje'] }}%</div>
            <div class="text-muted" style="font-size:.65rem">cumplido</div>
          </div>
        </div>
        {{-- SCI --}}
        <div class="w-100">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="d-flex align-items-center gap-1" style="font-size:.78rem;font-weight:600">
              <i class="ti tabler-shield-check" style="color:#696cff;font-size:.9rem"></i>SCI
            </span>
            <span class="fw-bold" style="font-size:.82rem;color:#696cff" id="sci-pct">{{ $resumen[0]->porcentaje ?? 0 }}%</span>
          </div>
          <div class="progress" style="height:6px;border-radius:3px">
            <div id="sci-bar" class="progress-bar rounded-pill"
              style="width:{{ $resumen[0]->porcentaje ?? 0 }}%;background:linear-gradient(90deg,#696cff,#a8aaff)"></div>
          </div>
          <div class="d-flex justify-content-between mt-1">
            <small class="text-muted" style="font-size:.65rem" id="sci-detail">{{ $resumen[0]->completadas ?? 0 }} de {{ $resumen[0]->total ?? 0 }}</small>
          </div>
        </div>
        {{-- Integridad --}}
        <div class="w-100">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="d-flex align-items-center gap-1" style="font-size:.78rem;font-weight:600">
              <i class="ti tabler-heart-handshake" style="color:#28c76f;font-size:.9rem"></i>Integridad
            </span>
            <span class="fw-bold" style="font-size:.82rem;color:#28c76f" id="int-pct">{{ $resumen[1]->porcentaje ?? 0 }}%</span>
          </div>
          <div class="progress" style="height:6px;border-radius:3px">
            <div id="int-bar" class="progress-bar rounded-pill"
              style="width:{{ $resumen[1]->porcentaje ?? 0 }}%;background:linear-gradient(90deg,#28c76f,#48da89)"></div>
          </div>
          <div class="d-flex justify-content-between mt-1">
            <small class="text-muted" style="font-size:.65rem" id="int-detail">{{ $resumen[1]->completadas ?? 0 }} de {{ $resumen[1]->total ?? 0 }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Ranking unidades: col 3 --}}
  <div class="col-lg-3">
    <div class="card rep-card h-100">
      <div class="card-header py-3 px-4" style="border-bottom:1px solid rgba(0,0,0,.06)">
        <h6 class="mb-0 fw-bold"><i class="ti tabler-trophy me-1 text-warning"></i>Ranking Unidades</h6>
        <small class="text-muted">% cumplimiento por unidad</small>
      </div>
      <div class="card-body px-4 py-2" id="rankingContainer">
        @include('content.reportes._ranking', ['por_unidad' => $por_unidad])
      </div>
    </div>
  </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     AVANCE POR COMPONENTE (SCI / Integridad)
════════════════════════════════════════════════════════════ --}}
<div class="card rep-card mb-4" id="cardComponentes">
  <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between"
    style="border-bottom:1px solid rgba(0,0,0,.06)">
    <div>
      <h6 class="mb-0 fw-bold"><i class="ti tabler-list-check me-1 text-primary"></i>Avance por Componente</h6>
      <small class="text-muted">Cumplimiento agrupado por componente del marco normativo</small>
    </div>
    {{-- tabs SCI / Integridad --}}
    <div class="d-flex gap-1">
      <button class="btn btn-sm btn-primary comp-tab-btn" data-tab="sci" id="compTabSci">
        <i class="ti tabler-shield-check me-1"></i>SCI
      </button>
      <button class="btn btn-sm btn-outline-secondary comp-tab-btn" data-tab="integridad" id="compTabInt">
        <i class="ti tabler-heart-handshake me-1"></i>Integridad
      </button>
    </div>
  </div>
  <div class="card-body p-0">
    {{-- Panel SCI --}}
    <div id="compPanelSci" class="comp-panel">
      @if($por_componente_sci->isEmpty())
        <p class="text-muted text-center py-4 small">Sin datos de componentes SCI para este período.</p>
      @else
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.82rem">
          <thead class="table-light">
            <tr>
              <th class="ps-4" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">Componente SCI</th>
              <th style="width:80px;text-align:center;font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">Total</th>
              <th style="width:90px;text-align:center;font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">Completadas</th>
              <th style="width:220px;font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">Avance</th>
              <th style="width:70px;text-align:center;font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">%</th>
            </tr>
          </thead>
          <tbody>
            @foreach($por_componente_sci as $c)
            @php $cc = $c['porcentaje'] >= 75 ? '#28c76f' : ($c['porcentaje'] >= 50 ? '#ff9f43' : '#ea5455'); @endphp
            <tr>
              <td class="ps-4 fw-semibold">{{ $c['nombre'] }}</td>
              <td class="text-center text-muted">{{ $c['total'] }}</td>
              <td class="text-center fw-semibold" style="color:#28c76f">{{ $c['completadas'] }}</td>
              <td>
                <div style="height:6px;border-radius:3px;background:rgba(0,0,0,.06);overflow:hidden">
                  <div style="height:100%;border-radius:3px;width:{{ $c['porcentaje'] }}%;background:{{ $cc }};transition:width .4s"></div>
                </div>
              </td>
              <td class="text-center fw-bold" style="color:{{ $cc }}">{{ $c['porcentaje'] }}%</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
    {{-- Panel Integridad --}}
    <div id="compPanelInt" class="comp-panel d-none">
      @if($por_componente_int->isEmpty())
        <p class="text-muted text-center py-4 small">Sin datos de componentes de Integridad para este período.</p>
      @else
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.82rem">
          <thead class="table-light">
            <tr>
              <th class="ps-4" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">Componente Integridad</th>
              <th style="width:80px;text-align:center;font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">Total</th>
              <th style="width:90px;text-align:center;font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">Completadas</th>
              <th style="width:220px;font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">Avance</th>
              <th style="width:70px;text-align:center;font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">%</th>
            </tr>
          </thead>
          <tbody>
            @foreach($por_componente_int as $c)
            @php $cc = $c['porcentaje'] >= 75 ? '#28c76f' : ($c['porcentaje'] >= 50 ? '#ff9f43' : '#ea5455'); @endphp
            <tr>
              <td class="ps-4 fw-semibold">{{ $c['nombre'] }}</td>
              <td class="text-center text-muted">{{ $c['total'] }}</td>
              <td class="text-center fw-semibold" style="color:#28c76f">{{ $c['completadas'] }}</td>
              <td>
                <div style="height:6px;border-radius:3px;background:rgba(0,0,0,.06);overflow:hidden">
                  <div style="height:100%;border-radius:3px;width:{{ $c['porcentaje'] }}%;background:{{ $cc }};transition:width .4s"></div>
                </div>
              </td>
              <td class="text-center fw-bold" style="color:{{ $cc }}">{{ $c['porcentaje'] }}%</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     TABLA: contador + tabla detalle
════════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-2 px-1">
  <div class="text-muted small d-flex align-items-center gap-2">
    <i class="ti tabler-table"></i>
    <span id="repContador">
      Mostrando <strong class="text-body">{{ $actividades->firstItem() ?? 0 }}–{{ $actividades->lastItem() ?? 0 }}</strong>
      de <strong class="text-body">{{ $actividades->total() }}</strong> actividad(es)
    </span>
    <span id="badgeFiltros" class="badge bg-label-primary" style="{{ $hayFiltros||$modulo ?'':'display:none' }}">filtros activos</span>
  </div>
  <div class="d-flex align-items-center gap-2">
    <button id="btnLimpiarTabla" type="button"
      class="btn btn-sm btn-outline-danger px-2 {{ $hayFiltros||$modulo ?'':'invisible' }}"
      style="font-size:.73rem">
      <i class="ti tabler-x me-1"></i>Limpiar
    </button>
    <span class="text-muted" style="font-size:.73rem">
      <i class="ti tabler-sort-ascending me-1"></i>por fecha límite
    </span>
  </div>
</div>

<div class="card rep-card rep-grid-wrap" id="repGridWrap">
  <div class="rep-spinner">
    <div class="spinner-border spinner-border-sm text-primary"></div>
    <span class="text-muted small">Actualizando...</span>
  </div>
  <div id="repTabla" class="p-0">
    @include('content.reportes._tabla', ['actividades' => $actividades])
  </div>
  <div id="repPaginacion" class="d-flex justify-content-center align-items-center py-3 px-4"
    style="{{ $actividades->hasPages() ? 'border-top:1px solid rgba(0,0,0,.06)' : '' }}">
    @if($actividades->hasPages()){{ $actividades->links() }}@endif
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  const BASE  = "{{ route('rep-reportes') }}";
  const tablaEl  = document.getElementById('repTabla');
  const pagEl    = document.getElementById('repPaginacion');
  const contEl   = document.getElementById('repContador');
  const wrapEl   = document.getElementById('repGridWrap');
  const spinEl   = document.getElementById('repSpinner');
  const rankEl   = document.getElementById('rankingContainer');
  let aborter    = null;
  let chartBar   = null;
  let chartDonut = null;
  let modActual  = '{{ $modulo ?? '' }}';

  const isDark    = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const gridColor = isDark ? 'rgba(255,255,255,.07)' : 'rgba(0,0,0,.05)';
  const txtColor  = isDark ? '#b4bdc6' : '#697a8d';
  const meses     = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Set','Oct','Nov','Dic'];

  // ── Select2 para unidad (patrón Vuexy: clase select2 + form-select) ──
  let pauseUnidad = false;
  $('#filtroUnidad').select2({
    width: '100%',
    placeholder: 'Todas las unidades',
    allowClear: true,
  }).on('change', function() {
    if (!pauseUnidad) submitFiltros();
  });

  // ── Recolectar parámetros (SIN FormData, directo de DOM) ──
  function getParams() {
    const p = new URLSearchParams();

    // Módulo (tab)
    if (modActual) p.set('modulo', modActual);

    // Año
    const anio = document.getElementById('filtroAnio').value;
    if (anio) p.set('anio', anio);

    // Estado
    const estado = document.getElementById('filtroEstado').value;
    if (estado) p.set('estado', estado);

    // Unidad (leer desde Select2)
    const unidad = $('#filtroUnidad').val();
    if (unidad) p.set('unidad_organica_id', unidad);

    // Buscar
    const buscar = document.getElementById('filtroBuscar').value.trim();
    if (buscar) p.set('buscar', buscar);

    // Prioridad
    const prio = document.getElementById('filtroPrioridad')?.value;
    if (prio) p.set('prioridad', prio);

    // Fechas (de inputs hidden)
    const fd = document.getElementById('filtroFechaDesdeVal').value;
    const fh = document.getElementById('filtroFechaHastaVal').value;
    if (fd) p.set('fecha_desde', fd);
    if (fh) p.set('fecha_hasta', fh);

    // Avance min/max
    const amin = document.getElementById('filtroAvanceMin').value;
    const amax = document.getElementById('filtroAvanceMax').value;
    if (amin && amin !== '0')   p.set('avance_min', amin);
    if (amax && amax !== '100') p.set('avance_max', amax);

    return p;
  }

  // ── Fetch ──────────────────────────────────────────────────
  function cargar(params) {
    if (aborter) aborter.abort();
    aborter = new AbortController();
    wrapEl.classList.add('is-loading');

    fetch(BASE + '?' + params.toString(), {
      signal: aborter.signal,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    })
    .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
    .then(d => {
      tablaEl.innerHTML  = d.html;
      pagEl.innerHTML    = d.pages || '';
      pagEl.className    = 'd-flex justify-content-center align-items-center py-3 px-4';
      if (d.pages) pagEl.style.borderTop = '1px solid rgba(0,0,0,.06)';
      else pagEl.style.borderTop = '';
      contEl.innerHTML   =
        `Mostrando <strong class="text-body">${d.from}–${d.to}</strong> de <strong class="text-body">${d.total}</strong> actividad(es)`;

      const hayF = params.toString().length > 0;
      document.getElementById('badgeFiltros')?.style.setProperty('display', hayF ? '' : 'none');
      document.getElementById('btnLimpiarTabla')?.classList.toggle('invisible', !hayF);
      const btnL = document.getElementById('btnLimpiar');
      if (btnL) { btnL.classList.toggle('invisible', !hayF); btnL.classList.remove('d-none'); }

      actualizarKpis(d.stats);
      actualizarResumen(d.resumen);
      if (d.por_mes)            buildBarChart(d.por_mes);
      if (d.por_unidad)         rankEl.innerHTML = renderRanking(d.por_unidad);
      if (d.por_componente_sci) renderComponentes('sci', d.por_componente_sci);
      if (d.por_componente_int) renderComponentes('integridad', d.por_componente_int);

      // Año en header/gráfica
      const a = params.get('anio') || new Date().getFullYear();
      document.querySelectorAll('.js-anio-label').forEach(el => el.textContent = a);
      document.getElementById('hdrAnio').textContent = a;

      // Módulo en header
      const mods = {'':'Todos los módulos','sci':'Control Interno (SCI)','integridad':'Modelo de Integridad'};
      document.getElementById('hdrModulo').textContent = mods[modActual] || 'Todos los módulos';

      history.pushState(null, '', BASE + (params.toString() ? '?' + params.toString() : ''));
      bindPaginacion();
    })
    .catch(e => { if (e.name !== 'AbortError') console.error('Reportes fetch:', e); })
    .finally(() => wrapEl.classList.remove('is-loading'));
  }

  function submitFiltros() { cargar(getParams()); }

  // ── KPIs ───────────────────────────────────────────────────
  function actualizarKpis(s) {
    if (!s) return;
    const set = (id, v) => { const el=document.getElementById(id); if(el) el.textContent=v; };
    set('kpi-total',      s.total);
    set('kpi-completadas',s.completadas);
    set('kpi-en_proceso', s.en_proceso);
    set('kpi-pendientes', s.pendientes);
    set('kpi-vencidas',   s.vencidas);
    set('kpi-sin_ev',     s.sin_ev);
    set('kpi-pct',        s.porcentaje + '%');
    set('donutPct',       s.porcentaje + '%');
    const bar = document.getElementById('kpi-bar');
    if (bar) bar.style.width = s.porcentaje + '%';
    // Actualizar donut
    if (chartDonut) chartDonut.updateSeries([s.completadas, Math.max(0, s.total - s.completadas)]);
  }

  // ── Resumen módulos ────────────────────────────────────────
  function actualizarResumen(res) {
    if (!res || res.length < 2) return;
    const sci = res[0], int_ = res[1];
    const s = (id,v) => { const el=document.getElementById(id); if(el) el.textContent=v; };
    s('sci-pct',    (sci.porcentaje??0)+'%');
    s('sci-detail', (sci.completadas??0)+' de '+(sci.total??0));
    const sb = document.getElementById('sci-bar');
    if (sb) sb.style.width = (sci.porcentaje??0)+'%';

    s('int-pct',    (int_.porcentaje??0)+'%');
    s('int-detail', (int_.completadas??0)+' de '+(int_.total??0));
    const ib = document.getElementById('int-bar');
    if (ib) ib.style.width = (int_.porcentaje??0)+'%';
  }

  // ── Ranking HTML (renderizado client-side tras fetch) ─────
  function renderRanking(data) {
    if (!data || !data.length) return '<p class="text-muted text-center py-3 small">Sin datos de unidades</p>';
    return data.slice(0, 8).map(ru => {
      const color = ru.porcentaje >= 75 ? '#28c76f' : ru.porcentaje >= 50 ? '#ff9f43' : '#ea5455';
      return `<div class="rank-row">
        <span class="rank-label">${ru.nombre}</span>
        <div class="rank-bar-bg"><div class="rank-bar-fg" style="width:${ru.porcentaje}%;background:${color}"></div></div>
        <div class="rank-pct" style="color:${color}">${ru.porcentaje}%<div class="rank-sub">${ru.completadas}/${ru.total}</div></div>
      </div>`;
    }).join('');
  }

  // ── Gráfica barras ─────────────────────────────────────────
  function buildBarChart(raw) {
    const cats = raw.length ? raw.map(d => meses[d.mes - 1]) : meses;
    const comp = raw.map(d => +d.completadas);
    const pend = raw.map(d => Math.max(0, +d.total - +d.completadas));
    const opts = {
      chart: { type:'bar', height:220, stacked:true, toolbar:{show:false}, animations:{enabled:true,speed:350} },
      series: [{ name:'Completadas', data:comp }, { name:'Pendientes', data:pend }],
      colors: ['#28c76f','#696cff'],
      fill: { opacity:1 },
      dataLabels: { enabled:false },
      legend: { show:false },
      plotOptions: { bar: { borderRadius:4, borderRadiusWhenStacked:'last', columnWidth:'55%' } },
      xaxis: { categories:cats, labels:{style:{colors:txtColor,fontSize:'10px'}}, axisBorder:{show:false}, axisTicks:{show:false} },
      yaxis: { labels:{formatter:v=>parseInt(v),style:{colors:txtColor,fontSize:'10px'}} },
      grid: { borderColor:gridColor, strokeDashArray:4, padding:{top:-12,left:0,right:0} },
      tooltip: { y:{formatter:v=>v+' act.'} },
    };
    if (chartBar) { chartBar.updateOptions(opts, true); }
    else { chartBar = new ApexCharts(document.getElementById('chartMensual'), opts); chartBar.render(); }
  }

  // ── Gráfica donut ──────────────────────────────────────────
  function buildDonutChart(comp, total) {
    const rest = Math.max(0, total - comp);
    const opts = {
      chart: { type:'donut', height:150, sparkline:{enabled:true}, animations:{speed:350} },
      series: [comp, rest],
      colors: ['#28c76f','rgba(0,0,0,.07)'],
      labels: ['Completadas','Resto'],
      dataLabels: { enabled:false },
      legend: { show:false },
      stroke: { width:0 },
      plotOptions: { pie:{ donut:{ size:'72%' } } },
      tooltip: { enabled:false },
      states: { hover:{filter:{type:'none'}}, active:{filter:{type:'none'}} },
    };
    chartDonut = new ApexCharts(document.getElementById('chartDonut'), opts);
    chartDonut.render();
  }

  // ── Avance por componente ──────────────────────────────────
  function renderComponentes(modulo, data) {
    const panelEl = document.getElementById(modulo === 'sci' ? 'compPanelSci' : 'compPanelInt');
    if (!panelEl) return;
    if (!data || !data.length) {
      panelEl.innerHTML = `<p class="text-muted text-center py-4 small">Sin datos de componentes para este período.</p>`;
      return;
    }
    const label = modulo === 'sci' ? 'Componente SCI' : 'Componente Integridad';
    const rows = data.map(c => {
      const cc = c.porcentaje >= 75 ? '#28c76f' : c.porcentaje >= 50 ? '#ff9f43' : '#ea5455';
      return `<tr>
        <td class="ps-4 fw-semibold">${c.nombre}</td>
        <td class="text-center text-muted">${c.total}</td>
        <td class="text-center fw-semibold" style="color:#28c76f">${c.completadas}</td>
        <td><div style="height:6px;border-radius:3px;background:rgba(0,0,0,.06);overflow:hidden">
          <div style="height:100%;border-radius:3px;width:${c.porcentaje}%;background:${cc};transition:width .4s"></div>
        </div></td>
        <td class="text-center fw-bold" style="color:${cc}">${c.porcentaje}%</td>
      </tr>`;
    }).join('');
    panelEl.innerHTML = `<div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:.82rem">
        <thead class="table-light"><tr>
          <th class="ps-4" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.04em">${label}</th>
          <th style="width:80px;text-align:center;font-size:.68rem;text-transform:uppercase">Total</th>
          <th style="width:90px;text-align:center;font-size:.68rem;text-transform:uppercase">Completadas</th>
          <th style="width:220px;font-size:.68rem;text-transform:uppercase">Avance</th>
          <th style="width:70px;text-align:center;font-size:.68rem;text-transform:uppercase">%</th>
        </tr></thead>
        <tbody>${rows}</tbody>
      </table></div>`;
  }

  // ── Tabs componentes (SCI / Integridad) ────────────────────
  document.querySelectorAll('.comp-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const tab = this.dataset.tab;
      document.querySelectorAll('.comp-tab-btn').forEach(b => {
        b.className = b === this
          ? 'btn btn-sm btn-primary comp-tab-btn'
          : 'btn btn-sm btn-outline-secondary comp-tab-btn';
      });
      document.getElementById('compPanelSci').classList.toggle('d-none', tab !== 'sci');
      document.getElementById('compPanelInt').classList.toggle('d-none', tab !== 'integridad');
    });
  });

  buildBarChart(@json($por_mes));
  buildDonutChart({{ $stats['completadas'] }}, {{ $stats['total'] }});

  // ── Tabs módulo ────────────────────────────────────────────
  document.querySelectorAll('.mod-tab').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.mod-tab').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      modActual = this.dataset.mod;
      submitFiltros();
    });
  });

  // ── Selects nativos ────────────────────────────────────────
  document.getElementById('filtroAnio')?.addEventListener('change', submitFiltros);
  document.getElementById('filtroEstado')?.addEventListener('change', submitFiltros);
  document.getElementById('filtroPrioridad')?.addEventListener('change', submitFiltros);

  // ── Buscar debounce ────────────────────────────────────────
  let debBuscar;
  document.getElementById('filtroBuscar').addEventListener('input', function () {
    clearTimeout(debBuscar);
    spinEl.style.display = '';
    debBuscar = setTimeout(() => { spinEl.style.display='none'; submitFiltros(); }, 500);
  });

  // ── Limpiar (ambos botones) ────────────────────────────────
  function limpiarTodo() {
    // Resets nativos
    document.getElementById('filtroAnio').value    = new Date().getFullYear();
    document.getElementById('filtroEstado').value  = '';
    document.getElementById('filtroBuscar').value  = '';
    document.getElementById('filtroPrioridad').value = '';
    // Select2 — resetear sin disparar doble fetch
    pauseUnidad = true;
    $('#filtroUnidad').val('').trigger('change');
    pauseUnidad = false;
    // Fechas
    document.getElementById('filtroFechaDesdeVal').value = '';
    document.getElementById('filtroFechaHastaVal').value = '';
    fpDesde?.clear(); fpHasta?.clear();
    // Slider
    if (sliderEl?.noUiSlider) sliderEl.noUiSlider.set([0, 100]);
    // Tab
    modActual = '';
    document.querySelectorAll('.mod-tab').forEach(b => b.classList.toggle('active', b.dataset.mod === ''));
    cargar(new URLSearchParams());
  }

  document.getElementById('btnLimpiar')?.addEventListener('click', limpiarTodo);
  document.getElementById('btnLimpiarTabla')?.addEventListener('click', limpiarTodo);

  // ── Exportar ───────────────────────────────────────────────
  document.getElementById('btnExportPdf')?.addEventListener('click', () => {
    const p = getParams(); p.set('formato','pdf');
    window.location.href = "{{ route('rep-reportes.exportar') }}?" + p;
  });
  document.getElementById('btnExportExcel')?.addEventListener('click', () => {
    const p = getParams(); p.set('formato','excel');
    window.location.href = "{{ route('rep-reportes.exportar') }}?" + p;
  });

  // ── Paginación delegada (event delegation — no re-bind necesario) ──
  function bindPaginacion() { /* no-op, usamos delegación global abajo */ }
  bindPaginacion();

  document.addEventListener('click', function(e) {
    const link = e.target.closest('#repPaginacion a[href]');
    if (!link) return;
    e.preventDefault();
    const pg = new URL(link.href, location.origin).searchParams.get('page') || 1;
    const p  = getParams(); p.set('page', pg);
    cargar(p);
    window.scrollTo({ top: document.getElementById('repGridWrap').offsetTop - 20, behavior: 'smooth' });
  });

  // ── Flatpickr ──────────────────────────────────────────────
  const fpCfg = {
    dateFormat: 'd/m/Y',
    locale: {
      firstDayOfWeek: 1,
      months: { shorthand:['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'], longhand:['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] },
      weekdays: { shorthand:['Do','Lu','Ma','Mi','Ju','Vi','Sa'], longhand:['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] },
    },
    monthSelectorType: 'static', static: true,
  };
  const fpDesde = flatpickr('#filtroFechaDesde', { ...fpCfg, onClose(d) {
    document.getElementById('filtroFechaDesdeVal').value = d[0] ? d[0].toISOString().slice(0,10) : '';
    submitFiltros();
  }});
  const fpHasta = flatpickr('#filtroFechaHasta', { ...fpCfg, onClose(d) {
    document.getElementById('filtroFechaHastaVal').value = d[0] ? d[0].toISOString().slice(0,10) : '';
    submitFiltros();
  }});

  // ── noUiSlider ─────────────────────────────────────────────
  const sliderEl = document.getElementById('sliderAvance');
  const minEl    = document.getElementById('filtroAvanceMin');
  const maxEl    = document.getElementById('filtroAvanceMax');
  const lblEl    = document.getElementById('avanceRangeLabel');
  let debSlider;
  noUiSlider.create(sliderEl, {
    start: [+minEl.value || 0, +maxEl.value || 100],
    connect: true, step: 5,
    range: { min: 0, max: 100 },
    tooltips: [{ to: v => Math.round(v)+'%' }, { to: v => Math.round(v)+'%' }],
  });
  sliderEl.noUiSlider.on('update', v => {
    minEl.value = Math.round(v[0]); maxEl.value = Math.round(v[1]);
    lblEl.textContent = Math.round(v[0]) + '% — ' + Math.round(v[1]) + '%';
  });
  sliderEl.noUiSlider.on('change', () => { clearTimeout(debSlider); debSlider = setTimeout(submitFiltros, 400); });

});
</script>
@endsection
