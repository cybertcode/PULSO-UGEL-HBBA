@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Mis Actividades — PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/nouislider/nouislider.scss',
])
@endsection
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/nouislider/nouislider.js',
])
@endsection

@section('page-style')
<style>
/* ── KPI Cards ─────────────────────────────────────────────── */
.kpi-card { border-radius: 14px; border: none; overflow: hidden; transition: transform .18s, box-shadow .18s; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.10); }
.kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1; }
.kpi-label { font-size: .72rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; opacity: .75; }
.kpi-sub { font-size: .8rem; font-weight: 600; }

/* ── Activity Cards ─────────────────────────────────────────── */
.act-card { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); transition: transform .18s, box-shadow .18s; }
.act-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.09); }
.act-card.is-vencida { border-left: 4px solid #ea5455; }
.act-card.is-completada { border-left: 4px solid #28c76f; }
.act-card.is-en_proceso { border-left: 4px solid #ff9f43; }
.act-card.is-observado { border-left: 4px solid #00cfe8; }
.act-card.is-pendiente { border-left: 4px solid #a8aaae; }

.act-header { padding: 1rem 1.25rem .6rem; }
.act-body { padding: .4rem 1.25rem 1rem; }

.progress-thin { height: 6px; border-radius: 3px; }

.rol-badge { font-size: .68rem; padding: .25em .55em; border-radius: 6px; font-weight: 600; letter-spacing: .03em; }
.estado-pill { font-size: .72rem; padding: .28em .7em; border-radius: 20px; font-weight: 700; letter-spacing: .02em; }

/* ── Días restantes ─────────────────────────────────────────── */
.dias-chip { display: inline-flex; align-items: center; gap: .2rem; font-size: .72rem; font-weight: 700; padding: .18em .55em; border-radius: 20px; }

/* ── Action buttons ─────────────────────────────────────────── */
.act-actions { display: flex; gap: .4rem; padding: .75rem 1.25rem; border-top: 1px solid rgba(0,0,0,.05); background: rgba(0,0,0,.015); border-radius: 0 0 14px 14px; }
.btn-act { border-radius: 8px; font-size: .78rem; padding: .38rem .75rem; font-weight: 600; }

/* ── Filter card ────────────────────────────────────────────── */
.filter-card { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); }
.filter-card .form-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #6e6b7b; margin-bottom: .3rem; }

/* ── Próximas banner ────────────────────────────────────────── */
.proximas-item { transition: background .15s; }
.proximas-item:hover { background: rgba(255,159,67,.07); }

/* ── Modal avance ───────────────────────────────────────────── */
.avance-display { font-size: 3rem; font-weight: 800; line-height: 1; }
input[type="range"].avance-range { accent-color: var(--bs-primary); height: 6px; }

/* ── Empty state ────────────────────────────────────────────── */
.empty-icon { width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; }
</style>
@endsection

@section('content')

{{-- ── Breadcrumb ───────────────────────────────────────────── --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home me-1" style="font-size:.85rem"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Mis Actividades</li>
  </ol>
</nav>

{{-- ── Header ──────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div class="d-flex align-items-center gap-3">
    <div class="avatar avatar-lg">
      @if($user->profile_photo_path)
        <img src="{{ Storage::url($user->profile_photo_path) }}" class="rounded-circle" alt="">
      @else
        <div class="avatar-initial rounded-circle bg-label-primary fw-bold" style="font-size:1.3rem">
          {{ strtoupper(substr($user->name,0,1)) }}
        </div>
      @endif
    </div>
    <div>
      <h4 class="mb-0 fw-bold">Mis Actividades</h4>
      <p class="mb-0 text-muted small">
        <i class="ti tabler-user me-1"></i>{{ $user->name }}
        @if($user->cargos->isNotEmpty()) · <i class="ti tabler-briefcase me-1"></i>{{ $user->cargos->pluck('nombre')->implode(', ') }}@endif
        @if($user->unidadOrganica?->sigla) · <i class="ti tabler-building me-1"></i>{{ $user->unidadOrganica->sigla }}@endif
      </p>
    </div>
  </div>
  @if($proximas->count() > 0)
  <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background:rgba(255,159,67,.12);border:1px solid rgba(255,159,67,.3)">
    <div style="width:32px;height:32px;border-radius:8px;background:rgba(255,159,67,.2);display:flex;align-items:center;justify-content:center">
      <i class="ti tabler-calendar-exclamation text-warning"></i>
    </div>
    <div>
      <div class="fw-bold text-warning" style="font-size:.82rem">{{ $proximas->count() }} actividad(es) próximas a vencer</div>
      <div class="text-muted" style="font-size:.72rem">En los próximos 15 días</div>
    </div>
  </div>
  @endif
</div>

{{-- ── KPIs ─────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)">
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
        <div class="kpi-sub text-white-75">Asignadas a mí</div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%)">
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
          <div class="progress flex-grow-1" style="height:4px;background:rgba(255,255,255,.25)">
            <div class="progress-bar bg-white" id="kpi-bar" style="width:{{ $stats['porcentaje'] }}%"></div>
          </div>
          <span class="kpi-sub text-white-75" id="kpi-pct">{{ $stats['porcentaje'] }}%</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%)">
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
        <div class="kpi-sub text-white-75">Pendientes + en proceso</div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#cb2d3e 0%,#ef473a 100%)">
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
        <div class="kpi-sub text-white-75">
          @if($stats['vencidas'] > 0) Requieren atención inmediata @else Sin actividades vencidas @endif
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-md">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%)">
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
        <div class="kpi-sub text-white-75">Actividades sin respaldo</div>
      </div>
    </div>
  </div>

  <div class="col-6 col-sm-4 col-md">
    <a href="{{ route('mis-actividades', ['estado' => 'observado']) }}" class="text-decoration-none">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%)">
        <div class="card-body p-3 text-white">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <div class="kpi-label text-white-50">Observadas</div>
              <div class="kpi-value" id="kpi-observadas">{{ $stats['observadas'] }}</div>
            </div>
            <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
              <i class="ti tabler-file-x"></i>
            </div>
          </div>
          <div class="kpi-sub text-white-75">
            @if($stats['ev_rechazadas'] > 0)
              <i class="ti tabler-alert-circle me-1"></i>{{ $stats['ev_rechazadas'] }} con evidencia rechazada
            @else
              Evidencias rechazadas pendientes
            @endif
          </div>
        </div>
      </div>
    </a>
  </div>

</div>

{{-- ── Banner evidencias rechazadas ───────────────────────────── --}}
<div id="banner-rechazadas" class="mb-4{{ $stats['ev_rechazadas'] > 0 ? '' : ' d-none' }}">
  <div class="d-flex align-items-center gap-3 px-4 py-3" style="background:linear-gradient(135deg,rgba(234,84,85,.12),rgba(234,84,85,.06));border:1px solid rgba(234,84,85,.35);border-radius:14px">
    <div style="width:44px;height:44px;border-radius:12px;background:rgba(234,84,85,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <i class="ti tabler-file-x text-danger" style="font-size:1.4rem"></i>
    </div>
    <div class="flex-grow-1">
      <div class="fw-bold text-danger msg-rechazadas" style="font-size:.92rem">
        <i class="ti tabler-alert-circle me-1"></i>
        Tienes {{ $stats['ev_rechazadas'] }} actividad(es) con evidencia rechazada — requieren corrección
      </div>
      <div class="text-muted" style="font-size:.78rem">
        El coordinador rechazó tus evidencias. Revisa el motivo, corrígelas y reenvíalas para aprobación.
      </div>
    </div>
    <a href="{{ route('sci-evidencias', ['estado' => 'rechazado']) }}"
       class="btn btn-danger btn-sm flex-shrink-0">
      <i class="ti tabler-refresh-alert me-1"></i>Corregir evidencias
    </a>
  </div>
</div>

{{-- ── Próximas a vencer ────────────────────────────────────── --}}
@if($proximas->count() > 0)
<div class="card mb-4" style="border-radius:14px;border:1px solid rgba(255,159,67,.3)">
  <div class="card-header py-2 px-4 d-flex align-items-center gap-2" style="background:rgba(255,159,67,.08);border-bottom:1px solid rgba(255,159,67,.2);border-radius:14px 14px 0 0">
    <div style="width:28px;height:28px;border-radius:8px;background:rgba(255,159,67,.2);display:flex;align-items:center;justify-content:center">
      <i class="ti tabler-calendar-exclamation text-warning" style="font-size:.9rem"></i>
    </div>
    <h6 class="mb-0 fw-bold">Próximas a vencer <span class="text-muted fw-normal">(15 días)</span></h6>
    <span class="badge bg-warning ms-auto">{{ $proximas->count() }}</span>
  </div>
  <div class="card-body p-0">
    @foreach($proximas as $prox)
    @php $dias = (int) round(now()->diffInDays($prox->fecha_limite, false)); @endphp
    <div class="proximas-item d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}"
      role="button" title="Ver actividad" style="cursor:pointer"
      onclick="irAActividad({{ $prox->id }})">
      <div class="d-flex align-items-center justify-content-center flex-shrink-0"
        style="width:44px;height:44px;border-radius:10px;background:{{ $dias <= 3 ? 'rgba(234,84,85,.12)' : 'rgba(255,159,67,.12)' }}">
        <span class="fw-bold" style="font-size:.78rem;color:{{ $dias <= 3 ? '#ea5455' : '#ff9f43' }}">{{ $dias }}d</span>
      </div>
      <div class="flex-grow-1 min-w-0">
        <div class="fw-semibold text-truncate" style="font-size:.88rem">{{ Str::limit($prox->nombre, 55) }}</div>
        <div class="text-muted" style="font-size:.75rem">
          @php $proxComp = $prox->modulo === 'integridad' ? $prox->integridadPregunta?->componente?->nombre : $prox->sciPregunta?->componente?->nombre; @endphp
          <i class="ti tabler-layout-grid me-1"></i>{{ $proxComp ?? '—' }}
          · <i class="ti tabler-calendar me-1"></i>Vence {{ $prox->fecha_limite->format('d/m/Y') }}
        </div>
      </div>
      <div class="flex-shrink-0 d-flex align-items-center gap-2">
        <span class="dias-chip {{ $dias <= 3 ? 'bg-label-danger text-danger' : 'bg-label-warning text-warning' }}">
          <i class="ti tabler-clock-hour-4" style="font-size:.75rem"></i>
          {{ $dias <= 3 ? 'Urgente' : 'Próxima' }}
        </span>
        <i class="ti tabler-chevron-right text-muted" style="font-size:.85rem"></i>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- ── Filtros ──────────────────────────────────────────────── --}}
@php
  $hayFiltros       = request()->hasAny(['modulo','estado','prioridad','buscar','fecha_desde','fecha_hasta','avance_min','avance_max','evidencia','mi_rol']);
  $filtrosAvanzados = request()->hasAny(['fecha_desde','fecha_hasta','avance_min','avance_max','evidencia','mi_rol']);
  $nFiltros = collect(['modulo','estado','prioridad','buscar','fecha_desde','fecha_hasta','avance_min','avance_max','evidencia','mi_rol'])->filter(fn($k) => request()->filled($k))->count();
@endphp
<div class="card filter-card mb-4">
  <div class="card-body p-3">
    <form id="formFiltros" method="GET" action="{{ route('mis-actividades') }}">

      {{-- ── Fila principal ── --}}
      <div class="row g-3 align-items-end">

        {{-- Estado --}}
        <div class="col-md-3 col-sm-6">
          <label class="form-label"><i class="ti tabler-circle-dot me-1"></i>Estado</label>
          <select id="filtroEstado" name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
            <option value="en_proceso" {{ request('estado') === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
            <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completada</option>
            <option value="vencida"    {{ request('estado') === 'vencida'    ? 'selected' : '' }}>Vencida</option>
            <option value="observado"  {{ request('estado') === 'observado'  ? 'selected' : '' }}>Observado</option>
          </select>
        </div>

        {{-- Módulo --}}
        <div class="col-md-3 col-sm-6">
          <label class="form-label"><i class="ti tabler-layers-difference me-1"></i>Módulo</label>
          <select id="filtroModulo" name="modulo" class="form-select">
            <option value="">SCI e Integridad</option>
            <option value="sci"        {{ request('modulo') === 'sci'        ? 'selected' : '' }}>Sistema de Control Interno</option>
            <option value="integridad" {{ request('modulo') === 'integridad' ? 'selected' : '' }}>Modelo de Integridad</option>
          </select>
        </div>

        {{-- Prioridad --}}
        <div class="col-md-2 col-sm-4">
          <label class="form-label"><i class="ti tabler-flag me-1"></i>Prioridad</label>
          <select id="filtroPrioridad" name="prioridad" class="form-select">
            <option value="">Todas</option>
            <option value="alta"  {{ request('prioridad') === 'alta'  ? 'selected' : '' }}>Alta</option>
            <option value="media" {{ request('prioridad') === 'media' ? 'selected' : '' }}>Media</option>
            <option value="baja"  {{ request('prioridad') === 'baja'  ? 'selected' : '' }}>Baja</option>
          </select>
        </div>

        {{-- Buscar + botones de acción en un solo input-group (col 4 = 3+3+2+4=12) --}}
        <div class="col-md-4 col-sm-12">
          <label class="form-label"><i class="ti tabler-search me-1"></i>Buscar</label>
          <div class="input-group">
            <span class="input-group-text"><i class="ti tabler-search"></i></span>
            <input id="filtroBuscar" type="text" name="buscar" class="form-control"
              value="{{ request('buscar') }}" placeholder="Nombre o código..." autocomplete="off">
            <span class="input-group-text px-2" id="filtroBuscarSpinner" style="display:none">
              <span class="spinner-border spinner-border-sm text-primary" style="width:.8rem;height:.8rem"></span>
            </span>
            {{-- Botón filtros avanzados --}}
            <button type="button" id="btnFiltrosAvanzados"
              class="btn {{ $filtrosAvanzados ? 'btn-primary' : 'btn-outline-secondary' }} position-relative px-3"
              title="Filtros avanzados"
              data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados"
              aria-expanded="{{ $filtrosAvanzados ? 'true' : 'false' }}">
              <i class="ti tabler-adjustments-horizontal"></i>
              @if($filtrosAvanzados)
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.58rem;line-height:1.2;min-width:1rem">{{ $nFiltros }}</span>
              @endif
            </button>
            {{-- Botón limpiar: invisible si no hay filtros para no romper el layout --}}
            <a href="{{ route('mis-actividades') }}"
              class="btn btn-outline-danger px-3 {{ $hayFiltros ? '' : 'invisible' }}"
              title="Limpiar todos los filtros">
              <i class="ti tabler-x"></i>
            </a>
          </div>
        </div>
      </div>

      {{-- ── Filtros avanzados (flatpickr + noUiSlider) ── --}}
      <div class="collapse {{ $filtrosAvanzados ? 'show' : '' }}" id="filtrosAvanzados">
        <div class="mt-4 pt-3" style="border-top:1px dashed rgba(0,0,0,.1)">
          <div class="row g-3 align-items-end">

            {{-- Flatpickr: Vence desde --}}
            <div class="col-md-2 col-sm-6">
              <label class="form-label"><i class="ti tabler-calendar-event me-1"></i>Vence desde</label>
              <input id="filtroFechaDesde" name="fecha_desde" type="text"
                class="form-control flatpickr-input"
                placeholder="dd/mm/aaaa"
                value="{{ request('fecha_desde') ? \Carbon\Carbon::parse(request('fecha_desde'))->format('d/m/Y') : '' }}"
                readonly>
              <input type="hidden" id="filtroFechaDesdeVal" name="fecha_desde" value="{{ request('fecha_desde') }}">
            </div>

            {{-- Flatpickr: Vence hasta --}}
            <div class="col-md-2 col-sm-6">
              <label class="form-label"><i class="ti tabler-calendar-event me-1"></i>Vence hasta</label>
              <input id="filtroFechaHasta" name="fecha_hasta" type="text"
                class="form-control flatpickr-input"
                placeholder="dd/mm/aaaa"
                value="{{ request('fecha_hasta') ? \Carbon\Carbon::parse(request('fecha_hasta'))->format('d/m/Y') : '' }}"
                readonly>
              <input type="hidden" id="filtroFechaHastaVal" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
            </div>

            {{-- noUiSlider: Rango de avance --}}
            <div class="col-md-4 col-sm-12">
              <label class="form-label d-flex justify-content-between align-items-center">
                <span><i class="ti tabler-percentage me-1"></i>Rango de avance</span>
                <span class="fw-bold text-primary" id="avanceRangeLabel">
                  {{ request('avance_min', 0) }}% — {{ request('avance_max', 100) }}%
                </span>
              </label>
              {{-- Campos ocultos para submit --}}
              <input type="hidden" id="filtroAvanceMin" name="avance_min" value="{{ request('avance_min', 0) }}">
              <input type="hidden" id="filtroAvanceMax" name="avance_max" value="{{ request('avance_max', 100) }}">
              <div id="sliderAvance" class="mt-2 mb-1 noUi-primary"></div>
              <div class="d-flex justify-content-between text-muted mt-1" style="font-size:.72rem">
                <span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
              </div>
            </div>

            {{-- Evidencia --}}
            <div class="col-md-2 col-sm-6">
              <label class="form-label"><i class="ti tabler-file me-1"></i>Evidencia</label>
              <select id="filtroEvidencia" name="evidencia" class="form-select">
                <option value="">Todas</option>
                <option value="con" {{ request('evidencia') === 'con' ? 'selected' : '' }}>Con evidencia</option>
                <option value="sin" {{ request('evidencia') === 'sin' ? 'selected' : '' }}>Sin evidencia</option>
              </select>
            </div>

            {{-- Mi rol --}}
            <div class="col-md-2 col-sm-6">
              <label class="form-label"><i class="ti tabler-user-check me-1"></i>Mi rol</label>
              <select id="filtroMiRol" name="mi_rol" class="form-select">
                <option value="">Todos</option>
                <option value="principal"   {{ request('mi_rol') === 'principal'   ? 'selected' : '' }}>Principal</option>
                <option value="colaborador" {{ request('mi_rol') === 'colaborador' ? 'selected' : '' }}>Colaborador</option>
                <option value="supervisor"  {{ request('mi_rol') === 'supervisor'  ? 'selected' : '' }}>Supervisor</option>
              </select>
            </div>

          </div>
        </div>
      </div>

    </form>
  </div>
</div>

{{-- ── Contador de resultados ───────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 px-1">
  <div class="text-muted small">
    <i class="ti tabler-list me-1"></i>
    <span id="actContador">
      Mostrando <strong class="text-body">{{ $actividades->firstItem() ?? 0 }}–{{ $actividades->lastItem() ?? 0 }}</strong>
      de <strong class="text-body">{{ $actividades->total() }}</strong> actividad(es)
    </span>
    <span id="badgeFiltrosActivos" class="ms-2 badge bg-label-primary" style="{{ $hayFiltros ? '' : 'display:none' }}">con filtros activos</span>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="javascript:void(0)" id="btnLimpiarFiltros"
      class="btn btn-sm btn-outline-danger px-2 py-1 {{ $hayFiltros ? '' : 'invisible' }}"
      title="Limpiar todos los filtros" style="font-size:.75rem">
      <i class="ti tabler-x me-1"></i>Limpiar
    </a>
    <span class="text-muted small"><i class="ti tabler-sort-descending me-1"></i>Ordenado por urgencia</span>
  </div>
</div>

{{-- ── Lista de actividades ─────────────────────────────────── --}}
<div class="row g-3" id="actGrid">
  @include('content.mis-actividades._cards')
</div>

{{-- ── Paginación ──────────────────────────────────────────── --}}
<div class="mt-4 d-flex justify-content-center" id="actPaginacion">
  @if($actividades->hasPages()){{ $actividades->links() }}@endif
</div>

{{-- ── Modal: Actualizar Avance ────────────────────────────── --}}
<div class="modal fade" id="modalAvance" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none">
      <form id="formAvance">
        <div class="modal-header" style="background:linear-gradient(135deg,var(--bs-primary),color-mix(in srgb,var(--bs-primary) 70%,var(--bs-info)));border-radius:16px 16px 0 0">
          <div class="flex-grow-1">
            <h6 class="modal-title fw-bold mb-0" style="color:#fff"><i class="ti tabler-chart-line me-2"></i>Actualizar Avance</h6>
            <p class="mb-0 mt-1" id="avanceNombre" style="font-size:.78rem;color:rgba(255,255,255,.75);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></p>
          </div>
          <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body pt-4 pb-2 px-4">
          {{-- Display grande del % --}}
          <div class="text-center mb-4">
            <div class="avance-display text-primary" id="avanceValorLabel">0%</div>
            <div class="text-muted" style="font-size:.75rem">de completado</div>
          </div>
          {{-- Slider --}}
          <div class="mb-1">
            <input type="range" class="form-range avance-range" name="avance" id="avanceRange" min="0" max="100" step="5" value="0">
          </div>
          <div class="d-flex justify-content-between text-muted mb-4" style="font-size:.7rem">
            <span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
          </div>
          {{-- Observación --}}
          <div>
            <label class="form-label form-label-sm fw-semibold">Observación <span class="text-muted fw-normal">(opcional)</span></label>
            <textarea name="observaciones" class="form-control form-control-sm" rows="2"
              placeholder="Describe brevemente el avance realizado..." style="border-radius:8px"></textarea>
          </div>
        </div>
        <div class="modal-footer border-0 pt-2 px-4 pb-4">
          <button type="button" class="btn btn-sm btn-label-secondary" data-bs-dismiss="modal" style="border-radius:8px">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary flex-fill" style="border-radius:8px">
            <i class="ti tabler-check me-1"></i>Guardar avance
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ── Modal: Historial ────────────────────────────────────── --}}
<div class="modal fade" id="modalHistorial" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:16px;border:none">
      <div class="modal-header" style="background:linear-gradient(135deg,var(--bs-primary),color-mix(in srgb,var(--bs-primary) 70%,var(--bs-info)));border-radius:16px 16px 0 0">
        <div class="flex-grow-1">
          <h6 class="modal-title fw-bold mb-0" style="color:#fff"><i class="ti tabler-history me-2"></i>Historial de cambios</h6>
          <p class="mb-0 mt-1" id="historialNombre" style="font-size:.78rem;color:rgba(255,255,255,.75);max-width:340px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></p>
        </div>
        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-0" id="historialContenido">
        <div class="text-center py-5">
          <div class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></div>
          <div class="text-muted mt-2 small">Cargando historial...</div>
        </div>
      </div>
      <div class="modal-footer border-0 py-3">
        <button type="button" class="btn btn-sm btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

{{-- ── Modal: Subir evidencia nueva ───────────────────────────────────── --}}
<div class="modal fade" id="modalEvidenciaNueva" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:16px;border:none">
      <form method="POST" id="formEvidenciaNueva">
        @csrf
        <input type="hidden" name="actividad_id" id="evNuevaActividadId">
        <div class="modal-header" style="background:linear-gradient(135deg,var(--bs-primary),color-mix(in srgb,var(--bs-primary) 70%,var(--bs-info)));border-radius:16px 16px 0 0">
          <div>
            <h6 class="modal-title fw-bold mb-0" style="color:#fff"><i class="ti tabler-upload me-2"></i>Subir Evidencia</h6>
            <p class="mb-0 mt-1" id="evNuevaNombre" style="font-size:.78rem;color:rgba(255,255,255,.75);max-width:380px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></p>
          </div>
          <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" placeholder="Nombre o título del documento" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">N° SGD / Expediente</label>
              <input type="text" name="numero_sgd" class="form-control" placeholder="Ej: SGD-2026-001">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Enlace <span class="text-muted small">(opcional)</span></label>
              <input type="url" name="url_documento" class="form-control" placeholder="https://drive.google.com/…">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2" placeholder="Observaciones adicionales…"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 py-3">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btnEvNuevaSubmit">
            <i class="ti tabler-device-floppy me-1"></i>Registrar evidencia
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ── Modal: Corregir evidencia rechazada ────────────────────────────── --}}
<div class="modal fade" id="modalEvidenciaCorregir" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:16px;border:none">
      <form method="POST" id="formEvidenciaCorregir">
        @csrf @method('PUT')
        <div class="modal-header" style="background:linear-gradient(135deg,#ff9f43,#ffbe76);border-radius:16px 16px 0 0">
          <div>
            <h6 class="modal-title fw-bold mb-0" style="color:#fff"><i class="ti tabler-refresh-alert me-2"></i>Corregir y reenviar evidencia</h6>
            <p class="mb-0 mt-1" id="evCorregirNombre" style="font-size:.78rem;color:rgba(255,255,255,.75);max-width:380px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></p>
          </div>
          <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="evCorregirMotivoBanner" class="alert alert-danger border-danger py-2 mb-3" style="font-size:13px;display:none">
            <i class="ti tabler-alert-circle me-1"></i><strong>Motivo del rechazo:</strong>
            <span id="evCorregirMotivoTexto"></span>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" id="evCorregirTitulo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">N° SGD / Expediente</label>
              <input type="text" name="numero_sgd" id="evCorregirSgd" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Enlace</label>
              <input type="url" name="url_documento" id="evCorregirUrl" class="form-control" placeholder="https://…">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" id="evCorregirDesc" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 py-3">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning text-white" id="btnEvCorregirSubmit">
            <i class="ti tabler-send me-1"></i>Reenviar para revisión
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  const BASE_URL   = '{{ route('mis-actividades') }}';
  const CSRF       = '{{ csrf_token() }}';
  const gridEl     = document.getElementById('actGrid');
  const paginaEl   = document.getElementById('actPaginacion');
  const contadorEl = document.getElementById('actContador');
  const buscarInput = document.getElementById('filtroBuscar');
  const spinner     = document.getElementById('filtroBuscarSpinner');
  let fetchAborter  = null;

  // ── Recolectar parámetros actuales ───────────────────────
  function getParams() {
    const form = document.getElementById('formFiltros');
    const params = new URLSearchParams();
    new FormData(form).forEach((v, k) => {
      if (!v) return;
      if (k === 'avance_min' && v === '0') return;
      if (k === 'avance_max' && v === '100') return;
      params.set(k, v);
    });
    return params;
  }

  // ── Actualizar KPI cards en DOM ──────────────────────────
  function updateStats(stats) {
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('kpi-total',       stats.total);
    set('kpi-completadas', stats.completadas);
    set('kpi-en_proceso',  stats.en_proceso);
    set('kpi-vencidas',    stats.vencidas);
    set('kpi-sin_ev',      stats.sin_ev);
    set('kpi-observadas',  stats.observadas ?? 0);
    set('kpi-pct',         stats.porcentaje + '%');
    const bar = document.getElementById('kpi-bar');
    if (bar) bar.style.width = stats.porcentaje + '%';

    // Actualizar el banner de rechazadas
    const banner = document.getElementById('banner-rechazadas');
    if (banner) {
      if ((stats.ev_rechazadas ?? 0) > 0) {
        banner.classList.remove('d-none');
        const msg = banner.querySelector('.msg-rechazadas');
        if (msg) msg.textContent = `Tienes ${stats.ev_rechazadas} actividad(es) con evidencia rechazada — requieren corrección`;
      } else {
        banner.classList.add('d-none');
      }
    }
  }

  // ── Actualizar URL sin recargar ──────────────────────────
  function pushUrl(params) {
    const url = BASE_URL + (params.toString() ? '?' + params.toString() : '');
    history.pushState(null, '', url);
  }

  // ── Fetch AJAX principal ─────────────────────────────────
  function cargarActividades(params) {
    if (fetchAborter) fetchAborter.abort();
    fetchAborter = new AbortController();

    gridEl.style.opacity = '0.45';
    gridEl.style.pointerEvents = 'none';

    return fetch(BASE_URL + '?' + params.toString(), {
      signal: fetchAborter.signal,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
      gridEl.innerHTML = data.html;
      paginaEl.innerHTML = data.pages;
      contadorEl.innerHTML =
        `Mostrando <strong class="text-body">${data.from}–${data.to}</strong> de <strong class="text-body">${data.total}</strong> actividad(es)`;

      // badge filtros activos
      const hayFiltros = params.toString().length > 0;
      const badge = document.getElementById('badgeFiltrosActivos');
      if (badge) badge.style.display = hayFiltros ? '' : 'none';
      const btnLimpiar = document.getElementById('btnLimpiarFiltros');
      if (btnLimpiar) btnLimpiar.classList.toggle('invisible', !hayFiltros);

      updateStats(data.stats);
      bindCardEvents();
      pushUrl(params);
      gridEl.style.opacity = '1';
      gridEl.style.pointerEvents = '';
      spinner.style.display = 'none';
    })
    .catch(err => {
      if (err.name !== 'AbortError') {
        gridEl.style.opacity = '1';
        gridEl.style.pointerEvents = '';
        spinner.style.display = 'none';
      }
    });
  }

  // ── Disparar filtros ─────────────────────────────────────
  function submitFiltros() {
    cargarActividades(getParams());
  }

  // ── Selects: cambio inmediato ────────────────────────────
  ['filtroEstado','filtroModulo','filtroPrioridad','filtroEvidencia','filtroMiRol'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', submitFiltros);
  });

  // ── Buscar: debounce 500ms ───────────────────────────────
  let debBuscar;
  buscarInput.addEventListener('input', function () {
    clearTimeout(debBuscar);
    spinner.style.display = '';
    debBuscar = setTimeout(submitFiltros, 500);
  });

  // ── Limpiar filtros ──────────────────────────────────────
  document.getElementById('btnLimpiarFiltros')?.addEventListener('click', () => {
    document.getElementById('formFiltros').reset();
    document.getElementById('filtroFechaDesdeVal').value = '';
    document.getElementById('filtroFechaHastaVal').value = '';
    fpDesde?.clear(); fpHasta?.clear();
    if (sliderEl?.noUiSlider) sliderEl.noUiSlider.set([0, 100]);
    cargarActividades(new URLSearchParams());
  });

  // ── Paginación delegada ──────────────────────────────────
  paginaEl.addEventListener('click', function (e) {
    const link = e.target.closest('a[href]');
    if (!link) return;
    e.preventDefault();
    const url = new URL(link.href);
    const params = getParams();
    params.set('page', url.searchParams.get('page') || 1);
    cargarActividades(params);
  });

  // ── Flatpickr ────────────────────────────────────────────
  const fpOpts = {
    dateFormat: 'd/m/Y',
    locale: {
      firstDayOfWeek: 1,
      months: { shorthand: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'], longhand: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] },
      weekdays: { shorthand: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'], longhand: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] },
    },
    monthSelectorType: 'static',
    static: true,
  };

  const fpDesde = flatpickr('#filtroFechaDesde', {
    ...fpOpts,
    onClose(dates) {
      document.getElementById('filtroFechaDesdeVal').value = dates[0] ? dates[0].toISOString().slice(0,10) : '';
      submitFiltros();
    },
  });
  const fpHasta = flatpickr('#filtroFechaHasta', {
    ...fpOpts,
    onClose(dates) {
      document.getElementById('filtroFechaHastaVal').value = dates[0] ? dates[0].toISOString().slice(0,10) : '';
      submitFiltros();
    },
  });

  // ── noUiSlider ───────────────────────────────────────────
  const sliderEl   = document.getElementById('sliderAvance');
  const minInput   = document.getElementById('filtroAvanceMin');
  const maxInput   = document.getElementById('filtroAvanceMax');
  const rangeLabel = document.getElementById('avanceRangeLabel');
  let debSlider;

  noUiSlider.create(sliderEl, {
    start: [parseInt(minInput.value)||0, parseInt(maxInput.value)||100],
    connect: true, step: 5,
    range: { min: 0, max: 100 },
    tooltips: [{ to: v => Math.round(v)+'%' }, { to: v => Math.round(v)+'%' }],
  });
  sliderEl.noUiSlider.on('update', (values) => {
    minInput.value = Math.round(values[0]);
    maxInput.value = Math.round(values[1]);
    rangeLabel.textContent = Math.round(values[0]) + '% — ' + Math.round(values[1]) + '%';
  });
  sliderEl.noUiSlider.on('change', () => { clearTimeout(debSlider); debSlider = setTimeout(submitFiltros, 400); });

  // ── Bind eventos a tarjetas (re-bind tras cada fetch) ────
  const modalAvance    = new bootstrap.Modal(document.getElementById('modalAvance'));
  const modalHistorial = new bootstrap.Modal(document.getElementById('modalHistorial'));
  const formAvance     = document.getElementById('formAvance');
  const avanceRange    = document.getElementById('avanceRange');
  const avanceLabel    = document.getElementById('avanceValorLabel');
  let avanceUrl = '';

  avanceRange.addEventListener('input', () => { avanceLabel.textContent = avanceRange.value + '%'; });

  function bindCardEvents() {
    // Botón limpiar en empty state
    document.getElementById('btnLimpiarEmpty')?.addEventListener('click', () => {
      document.getElementById('formFiltros').reset();
      document.getElementById('filtroFechaDesdeVal').value = '';
      document.getElementById('filtroFechaHastaVal').value = '';
      fpDesde?.clear(); fpHasta?.clear();
      if (sliderEl?.noUiSlider) sliderEl.noUiSlider.set([0, 100]);
      cargarActividades(new URLSearchParams());
    });

    // Actualizar avance
    document.querySelectorAll('.btn-actualizar-avance').forEach(btn => {
      btn.addEventListener('click', function () {
        avanceUrl = this.dataset.url;
        document.getElementById('avanceNombre').textContent = this.dataset.nombre;
        avanceRange.value = this.dataset.avance;
        avanceLabel.textContent = this.dataset.avance + '%';
        formAvance.querySelector('[name="observaciones"]').value = '';
        modalAvance.show();
      });
    });

    // Subir evidencia nueva
    document.querySelectorAll('.btn-ev-nueva').forEach(btn => {
      btn.addEventListener('click', function () {
        document.getElementById('evNuevaActividadId').value = this.dataset.actividadId;
        document.getElementById('evNuevaNombre').textContent = this.dataset.nombre;
        formEvNueva.action = this.dataset.action;
        formEvNueva.reset();
        document.getElementById('evNuevaActividadId').value = this.dataset.actividadId;
        modalEvNueva.show();
      });
    });

    // Corregir evidencia rechazada
    document.querySelectorAll('.btn-ev-corregir').forEach(btn => {
      btn.addEventListener('click', function () {
        document.getElementById('evCorregirTitulo').value  = this.dataset.titulo      || '';
        document.getElementById('evCorregirSgd').value     = this.dataset.sgd         || '';
        document.getElementById('evCorregirUrl').value     = this.dataset.urlDoc      || '';
        document.getElementById('evCorregirDesc').value    = this.dataset.descripcion || '';
        formEvCorregir.action = this.dataset.action;
        const motivo = this.dataset.motivo || '';
        const banner = document.getElementById('evCorregirMotivoBanner');
        document.getElementById('evCorregirMotivoTexto').textContent = motivo;
        banner.style.display = motivo ? '' : 'none';
        document.getElementById('evCorregirNombre').textContent =
          this.closest('.act-card')?.querySelector('h6')?.textContent || '';
        modalEvCorregir.show();
      });
    });

    // Historial
    document.querySelectorAll('.btn-ver-historial').forEach(btn => {
      btn.addEventListener('click', function () {
        document.getElementById('historialNombre').textContent = this.dataset.nombre;
        document.getElementById('historialContenido').innerHTML =
          '<div class="text-center py-5"><div class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></div><div class="text-muted mt-2 small">Cargando historial...</div></div>';
        modalHistorial.show();
        fetch(this.dataset.url, { headers: { 'Accept': 'application/json' } })
          .then(r => r.json())
          .then(data => {
            if (!data.length) {
              document.getElementById('historialContenido').innerHTML =
                '<div class="text-center py-5"><i class="ti tabler-history-off text-muted d-block mb-2" style="font-size:2rem"></i><p class="text-muted mb-0">Sin historial registrado aún.</p></div>';
              return;
            }
            const iconMap = { estado:'tabler-circle-dot', avance:'tabler-percentage', observaciones:'tabler-note', nombre:'tabler-pencil', prioridad:'tabler-flag', fecha_limite:'tabler-calendar', responsables:'tabler-users', evidencia:'tabler-file-description' };
            const evColorMap = { validado:'success', rechazado:'danger', pendiente:'warning', null:'secondary' };
            let html = '<div class="p-3">';
            data.forEach((h, i) => {
              const esEvidencia = h.campo === 'evidencia';
              const icon = iconMap[h.campo] || 'tabler-edit';
              const evColor = esEvidencia ? (evColorMap[h.valor_nuevo] ?? 'secondary') : null;
              const bgColor = esEvidencia ? `rgba(var(--bs-${evColor}-rgb),.1)` : 'rgba(115,103,240,.08)';
              const iconColor = esEvidencia ? `text-${evColor}` : 'text-primary';
              const badgeHtml = esEvidencia
                ? `<span class="badge bg-label-${evColor} me-1" style="font-size:.7rem"><i class="ti tabler-file me-1"></i>Evidencia</span>`
                : `<span class="badge bg-label-secondary me-1" style="font-size:.7rem">${h.campo}</span>`;
              const valorNuevoHtml = esEvidencia
                ? `<span class="badge bg-label-${evColor}" style="font-size:.72rem">${h.valor_nuevo ?? '—'}</span>`
                : `<span class="fw-semibold">${h.valor_nuevo ?? '—'}</span>`;
              html += `<div class="d-flex gap-3 mb-3">
                <div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:10px;background:${bgColor}">
                  <i class="ti ${icon} ${iconColor}" style="font-size:.9rem"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                    <div>
                      ${badgeHtml}
                      ${h.descripcion ? `<span class="text-muted" style="font-size:.75rem">${h.descripcion}</span>` : ''}
                    </div>
                    <small class="text-muted" style="font-size:.7rem"><i class="ti tabler-clock me-1"></i>${h.fecha}</small>
                  </div>
                  <div class="mt-1 d-flex align-items-center gap-2 flex-wrap" style="font-size:.8rem">
                    ${h.valor_anterior ? `<span class="text-muted text-decoration-line-through">${h.valor_anterior}</span><i class="ti tabler-arrow-right text-muted" style="font-size:.7rem"></i>` : ''}
                    ${valorNuevoHtml}
                  </div>
                  <div class="mt-1 text-muted" style="font-size:.72rem"><i class="ti tabler-user me-1"></i>${h.usuario}</div>
                </div>
              </div>${i < data.length-1 ? '<hr class="my-2 opacity-25">' : ''}`;
            });
            document.getElementById('historialContenido').innerHTML = html + '</div>';
          })
          .catch(() => {
            document.getElementById('historialContenido').innerHTML =
              '<div class="text-center py-4 text-danger"><i class="ti tabler-wifi-off d-block mb-2" style="font-size:2rem"></i>Error al cargar el historial.</div>';
          });
      });
    });
  }

  // ── Submit avance ────────────────────────────────────────
  formAvance.addEventListener('submit', function (e) {
    e.preventDefault();
    const obs = this.querySelector('[name="observaciones"]').value;
    const btn = this.querySelector('[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    fetch(avanceUrl, {
      method: 'PATCH',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ avance: parseInt(avanceRange.value, 10), observaciones: obs }),
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti tabler-check me-1"></i>Guardar avance';
      if (data.success) {
        modalAvance.hide();
        Swal.fire({
          icon: 'success',
          title: 'Avance actualizado',
          html: `<span class="fw-bold fs-4 text-primary">${data.avance}%</span><br><small class="text-muted">${data.estado_label}</small>`,
          timer: 1800,
          showConfirmButton: false,
          timerProgressBar: true,
        }).then(() => cargarActividades(getParams()));
      } else if (data.advertencia) {
        modalAvance.hide();
        Swal.fire({
          icon: 'warning',
          title: 'Avance guardado al 100%',
          text: data.advertencia,
          confirmButtonText: 'Subir evidencia',
          showCancelButton: true,
          cancelButtonText: 'Entendido',
        }).then(res => {
          const actId = data.actividad_id;
          cargarActividades(getParams()).then(() => {
            if (res.isConfirmed && actId) {
              const btnEv = document.querySelector(`.btn-ev-nueva[data-actividad-id="${actId}"]`);
              if (btnEv) btnEv.click();
            }
          });
        });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo guardar.' });
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti tabler-check me-1"></i>Guardar avance';
      Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
    });
  });

  // ── Modales evidencia ────────────────────────────────────
  const modalEvNueva    = new bootstrap.Modal(document.getElementById('modalEvidenciaNueva'));
  const modalEvCorregir = new bootstrap.Modal(document.getElementById('modalEvidenciaCorregir'));
  const formEvNueva     = document.getElementById('formEvidenciaNueva');
  const formEvCorregir  = document.getElementById('formEvidenciaCorregir');

  // Submit: nueva evidencia vía fetch para mantener en la misma página
  formEvNueva.addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('btnEvNuevaSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando...';
    fetch(this.action, {
      method: 'POST',
      body: new FormData(this),
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(r => {
      if (r.ok || r.redirected) {
        modalEvNueva.hide();
        Swal.fire({ icon:'success', title:'Evidencia registrada', text:'Pendiente de validación por el coordinador.', timer:2500, showConfirmButton:false });
        setTimeout(() => cargarActividades(getParams()), 2600);
      } else {
        return r.text().then(t => { throw new Error(t); });
      }
    })
    .catch(() => {
      Swal.fire({ icon:'error', title:'Error', text:'No se pudo registrar la evidencia. Verifica los datos.' });
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti tabler-device-floppy me-1"></i>Registrar evidencia';
    });
  });

  // Submit: corregir evidencia rechazada
  formEvCorregir.addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('btnEvCorregirSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando...';
    const fd = new FormData(this);
    fd.append('_method', 'PUT');
    fetch(this.action, {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(r => {
      if (r.ok || r.redirected) {
        modalEvCorregir.hide();
        Swal.fire({ icon:'success', title:'Evidencia reenviada', text:'Tu corrección fue enviada y está pendiente de revisión.', timer:2500, showConfirmButton:false });
        setTimeout(() => cargarActividades(getParams()), 2600);
      } else {
        return r.text().then(t => { throw new Error(t); });
      }
    })
    .catch(() => {
      Swal.fire({ icon:'error', title:'Error', text:'No se pudo reenviar la evidencia. Intenta de nuevo.' });
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti tabler-send me-1"></i>Reenviar para revisión';
    });
  });

  // ── Bind inicial ─────────────────────────────────────────
  bindCardEvents();

});

// ── Próximas a vencer: ir a la card correspondiente ──────
function irAActividad(id) {
  const card = document.querySelector(`.act-card[data-act-id="${id}"]`);
  if (card) {
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    card.style.transition = 'box-shadow .2s, outline .2s';
    card.style.outline = '2px solid #ff9f43';
    card.style.boxShadow = '0 0 0 4px rgba(255,159,67,.25)';
    setTimeout(() => {
      card.style.outline = '';
      card.style.boxShadow = '';
    }, 2000);
  } else {
    // La actividad no está en la página actual → filtrar por ella
    const params = new URLSearchParams(window.location.search);
    params.set('buscar', id);
    window.location.href = '{{ route('mis-actividades') }}?' + params.toString();
  }
}
</script>
@endsection
