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
        @if($user->cargo) · <i class="ti tabler-briefcase me-1"></i>{{ $user->cargo }}@endif
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
            <div class="kpi-value">{{ $stats['total'] }}</div>
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
            <div class="kpi-value">{{ $stats['completadas'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
            <i class="ti tabler-circle-check"></i>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <div class="progress flex-grow-1" style="height:4px;background:rgba(255,255,255,.25)">
            <div class="progress-bar bg-white" style="width:{{ $stats['porcentaje'] }}%"></div>
          </div>
          <span class="kpi-sub text-white-75">{{ $stats['porcentaje'] }}%</span>
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
            <div class="kpi-value">{{ $stats['en_proceso'] }}</div>
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
            <div class="kpi-value">{{ $stats['vencidas'] }}</div>
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
            <div class="kpi-value">{{ $stats['sin_ev'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)">
            <i class="ti tabler-file-off"></i>
          </div>
        </div>
        <div class="kpi-sub text-white-75">Actividades sin respaldo</div>
      </div>
    </div>
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
    @php $dias = (int) now()->diffInDays($prox->fecha_limite, false); @endphp
    <div class="proximas-item d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
      <div class="d-flex align-items-center justify-content-center flex-shrink-0"
        style="width:44px;height:44px;border-radius:10px;background:{{ $dias <= 3 ? 'rgba(234,84,85,.12)' : 'rgba(255,159,67,.12)' }}">
        <span class="fw-bold" style="font-size:.78rem;color:{{ $dias <= 3 ? '#ea5455' : '#ff9f43' }}">{{ $dias }}d</span>
      </div>
      <div class="flex-grow-1 min-w-0">
        <div class="fw-semibold text-truncate" style="font-size:.88rem">{{ Str::limit($prox->nombre, 55) }}</div>
        <div class="text-muted" style="font-size:.75rem">
          <i class="ti tabler-component me-1"></i>{{ $prox->componente?->nombre ?? '—' }}
          · <i class="ti tabler-calendar me-1"></i>Vence {{ $prox->fecha_limite->format('d/m/Y') }}
        </div>
      </div>
      <div class="flex-shrink-0">
        <span class="dias-chip {{ $dias <= 3 ? 'bg-label-danger text-danger' : 'bg-label-warning text-warning' }}">
          <i class="ti tabler-clock-hour-4" style="font-size:.75rem"></i>
          {{ $dias <= 3 ? 'Urgente' : 'Próxima' }}
        </span>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- ── Filtros ──────────────────────────────────────────────── --}}
@php
  $hayFiltros       = request()->hasAny(['estado','componente_id','prioridad','buscar','fecha_desde','fecha_hasta','avance_min','avance_max','evidencia','mi_rol']);
  $filtrosAvanzados = request()->hasAny(['fecha_desde','fecha_hasta','avance_min','avance_max','evidencia','mi_rol']);
  $nFiltros = collect(['estado','componente_id','prioridad','buscar','fecha_desde','fecha_hasta','avance_min','avance_max','evidencia','mi_rol'])->filter(fn($k) => request()->filled($k))->count();
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

        {{-- Componente (Select2) --}}
        <div class="col-md-3 col-sm-6">
          <label class="form-label"><i class="ti tabler-layout-grid me-1"></i>Componente</label>
          <select id="filtroComponente" name="componente_id" class="form-select select2">
            <option value="">Todos los componentes</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ request('componente_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
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
    Mostrando <strong class="text-body">{{ $actividades->firstItem() ?? 0 }}–{{ $actividades->lastItem() ?? 0 }}</strong>
    de <strong class="text-body">{{ $actividades->total() }}</strong> actividad(es)
    @if($hayFiltros)<span class="ms-2 badge bg-label-primary">con filtros activos</span>@endif
  </div>
  <div class="text-muted small">
    <i class="ti tabler-sort-descending me-1"></i>Ordenado por urgencia
  </div>
</div>

{{-- ── Lista de actividades ─────────────────────────────────── --}}
<div class="row g-3">
  @forelse($actividades as $act)
  @php
    $ec = match($act->estado) {
      'completada' => 'success',
      'vencida'    => 'danger',
      'observado'  => 'info',
      'en_proceso' => 'warning',
      default      => 'secondary',
    };
    $ecHex = match($act->estado) {
      'completada' => '#28c76f',
      'vencida'    => '#ea5455',
      'observado'  => '#00cfe8',
      'en_proceso' => '#ff9f43',
      default      => '#a8aaae',
    };
    $estadoIcon = match($act->estado) {
      'completada' => 'tabler-circle-check',
      'vencida'    => 'tabler-clock-x',
      'observado'  => 'tabler-eye',
      'en_proceso' => 'tabler-loader-2',
      default      => 'tabler-clock-pause',
    };
    $pc = match($act->prioridad) { 'alta' => 'danger', 'media' => 'warning', default => 'secondary' };
    $prioIcon = match($act->prioridad) { 'alta' => 'tabler-flag-3', 'media' => 'tabler-flag-2', default => 'tabler-flag' };
    $miRol = $act->responsables->where('id', $user->id)->first()?->pivot->tipo ?? 'principal';
    $rolIcon = match($miRol) { 'principal' => 'tabler-crown', 'supervisor' => 'tabler-eye', default => 'tabler-users' };
    $tieneEvidencias = $act->evidencias->count() > 0;
    $diasRestantes = $act->fecha_limite ? (int) now()->diffInDays($act->fecha_limite, false) : null;
    $canEdit = !in_array($act->estado, ['completada', 'vencida']);
  @endphp

  <div class="col-md-6 col-xl-4">
    <div class="card act-card is-{{ $act->estado }} h-100">

      {{-- Header --}}
      <div class="act-header">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
          {{-- Estado --}}
          <span class="estado-pill bg-label-{{ $ec }} text-{{ $ec }}">
            <i class="ti {{ $estadoIcon }} me-1" style="font-size:.75rem"></i>{{ $act->estado_label }}
          </span>
          {{-- Prioridad + Rol --}}
          <div class="d-flex gap-1">
            <span class="rol-badge bg-label-{{ $pc }} text-{{ $pc }}">
              <i class="ti {{ $prioIcon }} me-1" style="font-size:.7rem"></i>{{ ucfirst($act->prioridad) }}
            </span>
            <span class="rol-badge bg-label-secondary text-secondary text-capitalize">
              <i class="ti {{ $rolIcon }} me-1" style="font-size:.7rem"></i>{{ $miRol }}
            </span>
          </div>
        </div>

        {{-- Nombre --}}
        <h6 class="mb-0 fw-bold lh-sm" title="{{ $act->nombre }}" style="font-size:.9rem">{{ Str::limit($act->nombre, 65) }}</h6>
      </div>

      {{-- Body --}}
      <div class="act-body flex-grow-1">
        <p class="text-muted mb-3 d-flex align-items-center gap-1" style="font-size:.78rem">
          <i class="ti tabler-layout-grid" style="font-size:.8rem"></i>
          {{ $act->componente?->nombre ?? '—' }}
          <span class="mx-1">·</span>
          <code class="text-muted" style="font-size:.72rem">{{ $act->codigo }}</code>
        </p>

        {{-- Barra de avance --}}
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.03em">Avance</span>
            <span class="fw-bold text-{{ $ec }}" style="font-size:.88rem">{{ $act->avance }}%</span>
          </div>
          <div class="progress progress-thin">
            <div class="progress-bar bg-{{ $ec }}" style="width:{{ $act->avance }}%;border-radius:3px" role="progressbar"></div>
          </div>
        </div>

        {{-- Fecha + Evidencia --}}
        <div class="d-flex justify-content-between align-items-center" style="font-size:.78rem">
          <div class="d-flex align-items-center gap-1 text-muted">
            <i class="ti tabler-calendar" style="font-size:.85rem"></i>
            @if($act->fecha_limite)
              <span>{{ $act->fecha_limite->format('d/m/Y') }}</span>
              @if($diasRestantes !== null && $act->estado !== 'completada')
                <span class="dias-chip ms-1 {{ $diasRestantes < 0 ? 'bg-label-danger text-danger' : ($diasRestantes <= 7 ? 'bg-label-warning text-warning' : 'bg-label-secondary text-secondary') }}">
                  {{ $diasRestantes < 0 ? abs($diasRestantes).'d tarde' : $diasRestantes.'d' }}
                </span>
              @elseif($act->estado === 'completada')
                <span class="dias-chip ms-1 bg-label-success text-success"><i class="ti tabler-check" style="font-size:.7rem"></i>OK</span>
              @endif
            @else
              <span class="text-muted">Sin fecha límite</span>
            @endif
          </div>
          <div class="d-flex align-items-center gap-1">
            @if($tieneEvidencias)
              <span class="dias-chip bg-label-success text-success">
                <i class="ti tabler-file-check" style="font-size:.75rem"></i>
                {{ $act->evidencias->count() }} ev.
              </span>
            @elseif(!in_array($act->estado, ['pendiente']))
              <span class="dias-chip bg-label-warning text-warning">
                <i class="ti tabler-file-off" style="font-size:.75rem"></i>
                Sin evidencia
              </span>
            @endif
          </div>
        </div>
      </div>

      {{-- Acciones --}}
      <div class="act-actions">
        @if($canEdit)
        <button class="btn btn-sm btn-primary btn-act flex-fill btn-actualizar-avance"
          data-id="{{ $act->id }}"
          data-avance="{{ $act->avance }}"
          data-nombre="{{ Str::limit($act->nombre, 50) }}"
          data-url="{{ route('mis-actividades.avance', $act) }}">
          <i class="ti tabler-pencil me-1"></i>Actualizar
        </button>
        @endif
        <a href="{{ route('sci-evidencias', ['actividad_id' => $act->id]) }}"
           class="btn btn-sm btn-act {{ $tieneEvidencias ? 'btn-outline-success' : 'btn-outline-warning' }}"
           title="{{ $tieneEvidencias ? 'Ver/subir evidencias' : 'Subir evidencia' }}">
          <i class="ti {{ $tieneEvidencias ? 'tabler-file-check' : 'tabler-upload' }}"></i>
        </a>
        <button class="btn btn-sm btn-act btn-outline-secondary btn-ver-historial"
          data-id="{{ $act->id }}"
          data-nombre="{{ Str::limit($act->nombre, 50) }}"
          data-url="{{ route('mis-actividades.historial', $act) }}"
          title="Ver historial de cambios">
          <i class="ti tabler-history"></i>
        </button>
      </div>

    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="card" style="border-radius:14px;border:none">
      <div class="card-body text-center py-5">
        <div class="empty-icon bg-label-secondary mx-auto mb-3">
          <i class="ti tabler-clipboard-off text-muted"></i>
        </div>
        <h5 class="fw-bold">No hay actividades que mostrar</h5>
        <p class="text-muted mb-3">
          @if($hayFiltros)
            Ninguna actividad coincide con los filtros aplicados.
          @else
            Cuando el Coordinador SCI te asigne una actividad, aparecerá aquí.
          @endif
        </p>
        @if($hayFiltros)
        <a href="{{ route('mis-actividades') }}" class="btn btn-label-primary btn-sm">
          <i class="ti tabler-x me-1"></i>Limpiar filtros
        </a>
        @endif
      </div>
    </div>
  </div>
  @endforelse
</div>

{{-- ── Paginación ──────────────────────────────────────────── --}}
@if($actividades->hasPages())
<div class="mt-4 d-flex justify-content-center">{{ $actividades->links() }}</div>
@endif

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

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Select2 ───────────────────────────────────────────────
  document.querySelectorAll('.select2').forEach(el => {
    $(el).wrap('<div class="position-relative"></div>').select2({
      placeholder: el.dataset.placeholder || '',
      dropdownParent: $(el).parent(),
      width: '100%',
    });
  });

  // ── Filtros en tiempo real ────────────────────────────────
  const form        = document.getElementById('formFiltros');
  const buscarInput = document.getElementById('filtroBuscar');
  const spinner     = document.getElementById('filtroBuscarSpinner');

  function submitFiltros() {
    const params = new URLSearchParams();
    new FormData(form).forEach((v, k) => {
      if (!v) return;
      // Omitir campos hidden duplicados de flatpickr (el visible no tiene name)
      if (k === 'fecha_desde' && !v) return;
      if (k === 'fecha_hasta' && !v) return;
      if (k === 'avance_min' && v === '0') return;
      if (k === 'avance_max' && v === '100') return;
      params.set(k, v);
    });
    window.location.href = '{{ route('mis-actividades') }}' + (params.toString() ? '?' + params.toString() : '');
  }

  // Selects nativos
  ['filtroEstado', 'filtroPrioridad', 'filtroEvidencia', 'filtroMiRol'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', submitFiltros);
  });

  // Select2 componente
  $('#filtroComponente').on('select2:select select2:unselect', submitFiltros);

  // ── Flatpickr ─────────────────────────────────────────────
  const fpOpts = {
    dateFormat: 'd/m/Y',        // display
    altInput: false,
    locale: {
      firstDayOfWeek: 1,
      months: { shorthand: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'], longhand: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] },
      weekdays: { shorthand: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'], longhand: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] },
    },
    monthSelectorType: 'static',
    static: true,
  };

  // Fecha desde
  flatpickr('#filtroFechaDesde', {
    ...fpOpts,
    onClose(dates) {
      const hidden = document.getElementById('filtroFechaDesdeVal');
      hidden.value = dates[0] ? dates[0].toISOString().slice(0,10) : '';
      if (dates[0]) submitFiltros();
    },
    onReady(dates, str, fp) {
      // Limpiar al click en el icono X del input
      fp._input.addEventListener('keydown', e => { if (e.key === 'Delete' || e.key === 'Backspace') { fp.clear(); document.getElementById('filtroFechaDesdeVal').value = ''; submitFiltros(); } });
    }
  });

  // Fecha hasta
  flatpickr('#filtroFechaHasta', {
    ...fpOpts,
    onClose(dates) {
      const hidden = document.getElementById('filtroFechaHastaVal');
      hidden.value = dates[0] ? dates[0].toISOString().slice(0,10) : '';
      if (dates[0]) submitFiltros();
    },
    onReady(dates, str, fp) {
      fp._input.addEventListener('keydown', e => { if (e.key === 'Delete' || e.key === 'Backspace') { fp.clear(); document.getElementById('filtroFechaHastaVal').value = ''; submitFiltros(); } });
    }
  });

  // ── noUiSlider: rango de avance ───────────────────────────
  const sliderEl  = document.getElementById('sliderAvance');
  const minInput  = document.getElementById('filtroAvanceMin');
  const maxInput  = document.getElementById('filtroAvanceMax');
  const rangeLabel = document.getElementById('avanceRangeLabel');
  let debounceSlider;

  noUiSlider.create(sliderEl, {
    start: [parseInt(minInput.value, 10) || 0, parseInt(maxInput.value, 10) || 100],
    connect: true,
    step: 5,
    range: { min: 0, max: 100 },
    tooltips: [
      { to: v => Math.round(v) + '%' },
      { to: v => Math.round(v) + '%' },
    ],
  });

  sliderEl.noUiSlider.on('update', (values) => {
    const min = Math.round(values[0]);
    const max = Math.round(values[1]);
    minInput.value  = min;
    maxInput.value  = max;
    rangeLabel.textContent = min + '% — ' + max + '%';
  });

  sliderEl.noUiSlider.on('change', () => {
    clearTimeout(debounceSlider);
    debounceSlider = setTimeout(submitFiltros, 400);
  });

  // ── Buscar con debounce ───────────────────────────────────
  let debounce;
  buscarInput.addEventListener('input', function () {
    clearTimeout(debounce);
    spinner.style.display = '';
    debounce = setTimeout(() => { spinner.style.display = 'none'; submitFiltros(); }, 500);
  });

  // ── Modal: Actualizar avance ──────────────────────────────
  const modalAvance = new bootstrap.Modal(document.getElementById('modalAvance'));
  const formAvance  = document.getElementById('formAvance');
  const avanceRange = document.getElementById('avanceRange');
  const avanceLabel = document.getElementById('avanceValorLabel');
  let avanceUrl = '';

  avanceRange.addEventListener('input', () => {
    avanceLabel.textContent = avanceRange.value + '%';
  });

  document.querySelectorAll('.btn-actualizar-avance').forEach(btn => {
    btn.addEventListener('click', function () {
      avanceUrl = this.dataset.url;
      document.getElementById('avanceNombre').textContent = this.dataset.nombre;
      avanceRange.value = this.dataset.avance;
      avanceLabel.textContent = this.dataset.avance + '%';
      modalAvance.show();
    });
  });

  formAvance.addEventListener('submit', function (e) {
    e.preventDefault();
    const obs = this.querySelector('[name="observaciones"]').value;
    const btn = this.querySelector('[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    fetch(avanceUrl, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ avance: parseInt(avanceRange.value, 10), observaciones: obs }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        modalAvance.hide();
        Swal.fire({
          icon: 'success',
          title: 'Avance actualizado',
          html: `<span class="fw-bold fs-4 text-primary">${data.avance}%</span><br><small class="text-muted">${data.estado_label}</small>`,
          timer: 2000,
          showConfirmButton: false,
          timerProgressBar: true,
        }).then(() => location.reload());
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo guardar.' });
        btn.disabled = false;
        btn.innerHTML = '<i class="ti tabler-check me-1"></i>Guardar avance';
      }
    })
    .catch(() => {
      Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
      btn.disabled = false;
      btn.innerHTML = '<i class="ti tabler-check me-1"></i>Guardar avance';
    });
  });

  // ── Modal: Historial ─────────────────────────────────────
  const modalHistorial = new bootstrap.Modal(document.getElementById('modalHistorial'));

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
          const iconMap = { estado: 'tabler-circle-dot', avance: 'tabler-percentage', observaciones: 'tabler-note', nombre: 'tabler-pencil', prioridad: 'tabler-flag', fecha_limite: 'tabler-calendar', responsables: 'tabler-users' };
          let html = `<div class="p-3">`;
          data.forEach(h => {
            const icon = iconMap[h.campo] || 'tabler-edit';
            html += `
              <div class="d-flex gap-3 mb-3">
                <div class="flex-shrink-0 d-flex align-items-center justify-content-center"
                  style="width:36px;height:36px;border-radius:10px;background:rgba(115,103,240,.08)">
                  <i class="ti ${icon} text-primary" style="font-size:.9rem"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                    <div>
                      <span class="badge bg-label-secondary me-1" style="font-size:.7rem">${h.campo}</span>
                      <span class="text-muted" style="font-size:.75rem">${h.descripcion || ''}</span>
                    </div>
                    <small class="text-muted" style="font-size:.7rem"><i class="ti tabler-clock me-1"></i>${h.fecha}</small>
                  </div>
                  <div class="mt-1 d-flex align-items-center gap-2 flex-wrap" style="font-size:.8rem">
                    <span class="text-muted text-decoration-line-through">${h.valor_anterior ?? '—'}</span>
                    <i class="ti tabler-arrow-right text-muted" style="font-size:.7rem"></i>
                    <span class="fw-semibold">${h.valor_nuevo ?? '—'}</span>
                  </div>
                  <div class="mt-1 text-muted" style="font-size:.72rem">
                    <i class="ti tabler-user me-1"></i>${h.usuario}
                  </div>
                </div>
              </div>
              ${data.indexOf(h) < data.length - 1 ? '<hr class="my-2 opacity-25">' : ''}`;
          });
          html += `</div>`;
          document.getElementById('historialContenido').innerHTML = html;
        })
        .catch(() => {
          document.getElementById('historialContenido').innerHTML =
            '<div class="text-center py-4 text-danger"><i class="ti tabler-wifi-off d-block mb-2" style="font-size:2rem"></i>Error al cargar el historial.</div>';
        });
    });
  });

});
</script>
@endsection
