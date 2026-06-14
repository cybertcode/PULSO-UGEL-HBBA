@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Modelo de Integridad - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('page-style')
<style>
/* ── Cards de componente ─────────────────────────────── */
.comp-card { border-radius: 10px; border: 1px solid rgba(var(--bs-secondary-rgb),.12); transition: transform .15s, box-shadow .15s; }
.comp-card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,.09); }

/* ── KPI unificado ───────────────────────────────────── */
.kpi-unified { border-radius: 12px; overflow: hidden; }
.kpi-divider { width: 1px; background: rgba(var(--bs-secondary-rgb),.15); align-self: stretch; margin: 12px 0; }

/* ── Evidencia item ──────────────────────────────────── */
.ev-item { border-bottom: 1px solid rgba(var(--bs-secondary-rgb),.08); }
.ev-item:last-child { border-bottom: none; }

/* ── Acción item ─────────────────────────────────────── */
.accion-item { border-bottom: 1px solid rgba(var(--bs-secondary-rgb),.08); }
.accion-item:last-child { border-bottom: none; }

/* ── Tab nav ──────────────────────────────────────────── */
.nav-tabs .nav-link { font-size: 13px; padding: .5rem 1rem; }

/* El fix de scroll para modales está en main.js (shown.bs.modal global) */
</style>
@endsection

@section('content')

@php
  $gc    = round($avance_global);
  $nc    = $gc >= $umbral_verde ? 'success' : ($gc >= $umbral_amarillo ? 'warning' : 'danger');
  $nivel = $gc >= $umbral_verde ? 'Bueno'   : ($gc >= $umbral_amarillo ? 'Regular'  : 'En riesgo');
@endphp

{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="d-flex align-items-start justify-content-between mb-3 flex-wrap gap-2">
  <div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Modelo de Integridad</li>
      </ol>
    </nav>
    <h4 class="mb-0 d-flex align-items-center gap-2">
      <i class="ti tabler-shield-check text-warning"></i> Modelo de Integridad
      <span class="badge bg-label-warning rounded-pill" style="font-size:11px">PCM — {{ $componentes->count() }} componentes</span>
    </h4>
    <p class="mb-0 text-muted small mt-1">Monitoreo del cumplimiento de los {{ $componentes->count() ?: 'nueve' }} componentes del Modelo de Integridad de la PCM — {{ $anio }}.</p>
  </div>
  <div class="d-flex gap-2 align-self-start flex-wrap">
    @can('integridad.crear')
    <button class="btn btn-warning btn-sm" id="btnNuevaActividad">
      <i class="ti tabler-plus me-1"></i>Nueva Actividad
    </button>
    @endcan
    <button class="btn btn-label-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalGuiaModelo">
      <i class="ti tabler-book me-1"></i>Guía del Modelo
    </button>
  </div>
</div>

{{-- ── KPI unificado ────────────────────────────────────────── --}}
<div class="card kpi-unified mb-4">
  <div class="card-body py-3 px-0">
    <div class="row g-0 text-center">

      {{-- Índice Global --}}
      <div class="col-12 col-sm-3 px-4 py-2">
        <div class="d-flex align-items-center gap-3 justify-content-center justify-content-sm-start">
          <div id="gaugeModelo" style="min-width:70px;min-height:50px"></div>
          <div class="text-start">
            <div class="fw-bold lh-1 mb-1" style="font-size:2rem;color:var(--bs-{{ $nc }})">{{ $gc }}%</div>
            <div class="fw-semibold" style="font-size:12px">Índice Global</div>
            <span class="badge bg-label-{{ $nc }}" style="font-size:10px">{{ $nivel }}</span>
          </div>
        </div>
      </div>

      <div class="kpi-divider d-none d-sm-block"></div>

      {{-- En avance --}}
      <div class="col-4 col-sm px-3 py-2 border-start border-sm-0">
        <div class="fw-bold text-success lh-1 mb-1" style="font-size:2rem">{{ $en_avance }}</div>
        <div class="fw-semibold" style="font-size:11px">En avance</div>
        <div class="text-muted" style="font-size:10px">≥ {{ $umbral_verde }}%</div>
      </div>

      <div class="kpi-divider d-none d-sm-block"></div>

      {{-- En riesgo --}}
      <div class="col-4 col-sm px-3 py-2 border-start border-sm-0">
        <div class="fw-bold text-warning lh-1 mb-1" style="font-size:2rem">{{ $en_riesgo }}</div>
        <div class="fw-semibold" style="font-size:11px">En riesgo</div>
        <div class="text-muted" style="font-size:10px">{{ $umbral_amarillo }}–{{ $umbral_verde - 1 }}%</div>
      </div>

      <div class="kpi-divider d-none d-sm-block"></div>

      {{-- Críticos --}}
      <div class="col-4 col-sm px-3 py-2 border-start border-sm-0">
        <div class="fw-bold text-danger lh-1 mb-1" style="font-size:2rem">{{ $criticos }}</div>
        <div class="fw-semibold" style="font-size:11px">Críticos</div>
        <div class="text-muted" style="font-size:10px">&lt; {{ $umbral_amarillo }}%</div>
      </div>

      <div class="kpi-divider d-none d-sm-block"></div>

      {{-- Actividades --}}
      <div class="col-4 col-sm px-3 py-2 border-start border-sm-0">
        <div class="fw-bold text-primary lh-1 mb-1" style="font-size:2rem">{{ $actividades->total() }}</div>
        <div class="fw-semibold" style="font-size:11px">Actividades</div>
        <div class="text-muted" style="font-size:10px">registradas</div>
      </div>

      <div class="kpi-divider d-none d-sm-block"></div>

      {{-- Evidencias --}}
      <div class="col-4 col-sm px-3 py-2 border-start border-sm-0">
        <div class="fw-bold text-info lh-1 mb-1" style="font-size:2rem">{{ $componentes->sum('evidencias_count') }}</div>
        <div class="fw-semibold" style="font-size:11px">Evidencias</div>
        <div class="text-muted" style="font-size:10px">cargadas</div>
      </div>

    </div>
  </div>
</div>

{{-- ── Tabs ──────────────────────────────────────────────── --}}
<ul class="nav nav-tabs mb-3" role="tablist">
  <li class="nav-item">
    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#tab-vista-general">
      <i class="ti tabler-layout-dashboard me-1"></i>Vista General
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tab-detalle-componente">
      <i class="ti tabler-table me-1"></i>Por Componente
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tab-actividades" id="tabActividadesLink">
      <i class="ti tabler-list-check me-1"></i>Actividades
      <span class="badge bg-label-warning ms-1 rounded-pill" style="font-size:10px">{{ $actividades->total() }}</span>
    </a>
  </li>
</ul>

<div class="tab-content">

{{-- ══ TAB VISTA GENERAL ═══════════════════════════════════ --}}
<div class="tab-pane fade show active" id="tab-vista-general">

  {{-- ── Fila 1: Componentes del Modelo ──────────────────── --}}
  <div class="d-flex align-items-center justify-content-between mb-2">
    <span class="fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em">
      <i class="ti tabler-components me-1"></i>9 Componentes del Modelo
    </span>
    <small class="text-muted" style="font-size:11px">Última act.: {{ now()->translatedFormat('d \d\e F, H:i') }}</small>
  </div>

  <div class="row g-2 mb-4">
    @forelse($componentes as $c)
    @php $nivelLabel = match($c->color) { 'success'=>'Cumplido','warning'=>'En proceso',default=>'En riesgo' }; @endphp
    <div class="col-6 col-md-4 col-xl-3 col-xxl-2">
      <div class="card comp-card h-100 mb-0">
        <div style="height:3px;background:var(--bs-{{ $c->color }});border-radius:10px 10px 0 0"></div>
        <div class="card-body p-3">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <span class="text-muted fw-bold" style="font-size:10px;text-transform:uppercase;letter-spacing:.05em">Comp. {{ $c->numero }}</span>
            <span class="badge bg-label-{{ $c->color }}" style="font-size:10px">{{ $nivelLabel }}</span>
          </div>
          <div class="fw-semibold mb-2 text-body" style="font-size:12.5px;line-height:1.35;min-height:2.6em">{{ $c->nombre }}</div>
          <div class="d-flex align-items-end gap-1 mb-1">
            <span class="fw-bold text-{{ $c->color }}" style="font-size:1.6rem;line-height:1">{{ $c->porcentaje }}</span>
            <span class="text-muted fw-semibold" style="font-size:12px;margin-bottom:2px">%</span>
          </div>
          <div class="progress mb-2" style="height:4px;border-radius:2px">
            <div class="progress-bar bg-{{ $c->color }}" style="width:{{ $c->porcentaje }}%"></div>
          </div>
          <div class="d-flex gap-3 mt-2" style="font-size:11px">
            <span class="text-success"><i class="ti tabler-check"></i> {{ $c->completadas_count }}</span>
            <span class="text-warning"><i class="ti tabler-clock"></i> {{ $c->en_proceso_count }}</span>
            <span class="text-info ms-auto"><i class="ti tabler-paperclip"></i> {{ $c->evidencias_count }}</span>
          </div>
        </div>
        <div class="card-footer py-2 px-3 bg-transparent border-top" style="border-color:rgba(var(--bs-secondary-rgb),.1)!important">
          <a href="{{ route('sci-evidencias', ['componente_id' => $c->id]) }}"
             class="btn btn-xs btn-label-{{ $c->color }} w-100">
            <i class="ti tabler-upload me-1" style="font-size:10px"></i>Evidencia
          </a>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12">
      <div class="card"><div class="card-body text-center text-muted py-5">Sin componentes configurados.</div></div>
    </div>
    @endforelse
  </div>

  {{-- ── Fila 2: Alertas + Próximas Acciones ─────────────── --}}
  <div class="row g-3 mb-4">

    {{-- Alertas Activas --}}
    <div class="col-12 col-lg-6">
      <div class="card h-100 mb-0">
        <div class="card-header d-flex align-items-center justify-content-between py-3 px-4" style="border-bottom:2px solid rgba(var(--bs-danger-rgb),.12)">
          <span class="fw-semibold" style="font-size:14px">
            <i class="ti tabler-bell-ringing me-2 text-danger"></i>Alertas Activas
          </span>
          <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-danger">Ver todas</a>
        </div>
        <div class="card-body p-0">
          @forelse($alertas_activas as $al)
          @php $ic = match($al->prioridad) { 'alta'=>'danger','media'=>'warning',default=>'info' }; @endphp
          <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom" style="border-color:rgba(var(--bs-secondary-rgb),.08)!important">
            <span class="flex-shrink-0 badge rounded-circle bg-label-{{ $ic }} d-flex align-items-center justify-content-center mt-1" style="width:32px;height:32px">
              <i class="ti tabler-{{ $ic==='danger'?'alert-octagon':'alert-circle' }}" style="font-size:14px"></i>
            </span>
            <div class="flex-grow-1 overflow-hidden">
              <div class="fw-semibold text-truncate" style="font-size:13px">{{ $al->titulo }}</div>
              @if($al->actividad?->componente)
              <div class="text-muted text-truncate" style="font-size:11px">{{ $al->actividad->componente->nombre }}</div>
              @endif
              <div class="d-flex align-items-center gap-2 mt-1">
                <span class="badge bg-label-{{ $ic }}" style="font-size:10px">{{ ucfirst($al->prioridad) }}</span>
                <small class="text-muted" style="font-size:10px">{{ $al->created_at->diffForHumans() }}</small>
              </div>
            </div>
          </div>
          @empty
          <div class="text-center text-muted py-5 px-3">
            <i class="ti tabler-bell-off d-block mb-2 text-success" style="font-size:2rem;opacity:.6"></i>
            <div class="fw-semibold" style="font-size:13px">Sin alertas activas</div>
            <small>El módulo está operando sin incidencias.</small>
          </div>
          @endforelse
        </div>
        @if($alertas_activas->isNotEmpty())
        <div class="card-footer py-2 px-4" style="background:rgba(var(--bs-secondary-rgb),.04)">
          <small class="text-muted d-flex align-items-center gap-1" style="font-size:10px">
            <i class="ti tabler-info-circle text-info"></i>
            Las alertas se envían al correo del responsable asignado.
          </small>
        </div>
        @endif
      </div>
    </div>

    {{-- Próximas Acciones --}}
    <div class="col-12 col-lg-6">
      <div class="card h-100 mb-0">
        <div class="card-header py-3 px-4" style="border-bottom:2px solid rgba(var(--bs-warning-rgb),.18)">
          <div class="d-flex align-items-center justify-content-between">
            <span class="fw-semibold" style="font-size:14px">
              <i class="ti tabler-clock-exclamation me-2 text-warning"></i>Próximas Acciones
            </span>
            <span class="badge bg-label-warning rounded-pill" style="font-size:10px">{{ $proximas_acciones->count() }} pendientes</span>
          </div>
        </div>
        <div class="card-body p-0">
          @forelse($proximas_acciones as $act)
          @php
            $dias = (int) round(now()->diffInDays($act->fecha_limite, false));
            $tc   = $dias <= 0 ? 'danger' : ($dias <= 3 ? 'danger' : ($dias <= 7 ? 'warning' : 'primary'));
          @endphp
          <div class="accion-item d-flex align-items-center gap-3 px-4 py-3">
            {{-- Fecha mini-calendar --}}
            <div class="flex-shrink-0 text-center rounded-2 p-1" style="min-width:46px;background:rgba(var(--bs-{{ $tc }}-rgb),.08);border:1px solid rgba(var(--bs-{{ $tc }}-rgb),.2)">
              @if($dias <= 0)
                <div class="fw-bold text-danger" style="font-size:9px;text-transform:uppercase;line-height:1.2">Venc.</div>
              @else
                <div class="fw-bold text-{{ $tc }}" style="font-size:1.15rem;line-height:1.1">{{ $act->fecha_limite->format('d') }}</div>
                <div class="text-{{ $tc }}" style="font-size:9px;text-transform:uppercase;font-weight:600;opacity:.8">{{ $act->fecha_limite->format('M') }}</div>
              @endif
            </div>
            {{-- Info --}}
            <div class="flex-grow-1 overflow-hidden">
              <div class="fw-semibold text-truncate" style="font-size:13px">{{ Str::limit($act->nombre, 48) }}</div>
              <div class="text-muted text-truncate" style="font-size:11px">{{ $act->componente?->nombre ?? '—' }}</div>
            </div>
            {{-- Badge días --}}
            <span class="badge bg-label-{{ $tc }} flex-shrink-0" style="font-size:10px;white-space:nowrap">
              {{ $dias <= 0 ? 'Vencida' : ($dias === 1 ? 'Mañana' : $dias.' días') }}
            </span>
          </div>
          @empty
          <div class="text-center text-muted py-5 px-3">
            <i class="ti tabler-calendar-check d-block mb-2 text-success" style="font-size:2rem;opacity:.6"></i>
            <div class="fw-semibold" style="font-size:13px">Sin acciones próximas</div>
            <small>Todas las actividades están al día.</small>
          </div>
          @endforelse
        </div>
      </div>
    </div>

  </div>{{-- /fila 2 --}}

  {{-- ── Fila 3: Evidencias Recientes (ancho completo) ───── --}}
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-3 px-4" style="border-bottom:2px solid rgba(var(--bs-primary-rgb),.1)">
      <span class="fw-semibold" style="font-size:14px">
        <i class="ti tabler-paperclip me-2 text-primary"></i>Evidencias Recientes
        <span class="badge bg-label-primary rounded-pill ms-1" id="ev-total-badge" style="font-size:10px">{{ $evidencias_recientes->count() }}</span>
      </span>
      <div class="d-flex align-items-center gap-2">
        <span class="text-muted" id="ev-pag-info" style="font-size:12px"></span>
        <a href="{{ route('sci-evidencias') }}" class="btn btn-xs btn-label-primary">
          Ver todas <i class="ti tabler-arrow-right ms-1"></i>
        </a>
      </div>
    </div>

    {{-- Tabla de evidencias --}}
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="min-width:700px">
        <thead style="background:var(--bs-tertiary-bg)">
          <tr>
            <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;width:44px">Estado</th>
            <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px">Título / SGD</th>
            <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;min-width:160px">Componente</th>
            <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px">Subido por</th>
            <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;white-space:nowrap">Fecha</th>
          </tr>
        </thead>
        <tbody id="ev-tbody">
          @forelse($evidencias_recientes as $ev)
          @php
            $ec = match($ev->estado) { 'validado'=>'success','rechazado'=>'danger',default=>'warning' };
            $el = match($ev->estado) { 'validado'=>'Validado','rechazado'=>'Rechazado',default=>'Pendiente' };
            $ei = match($ev->estado) { 'validado'=>'tabler-circle-check','rechazado'=>'tabler-circle-x',default=>'tabler-clock' };
          @endphp
          <tr class="ev-row">
            <td style="padding:10px 14px">
              <span class="badge rounded-circle bg-label-{{ $ec }} d-flex align-items-center justify-content-center" style="width:32px;height:32px">
                <i class="ti {{ $ei }}" style="font-size:14px"></i>
              </span>
            </td>
            <td style="padding:10px 14px;max-width:280px">
              <div class="fw-semibold text-truncate" style="font-size:13px">{{ $ev->titulo }}</div>
              @if($ev->numero_sgd)
              <span class="badge bg-label-secondary font-monospace mt-1" style="font-size:10px">{{ $ev->numero_sgd }}</span>
              @endif
            </td>
            <td style="padding:10px 14px">
              @if($ev->actividad?->componente)
              <div class="fw-medium text-truncate" style="font-size:12px;max-width:150px">{{ $ev->actividad->componente->nombre }}</div>
              @else
              <span class="text-muted">—</span>
              @endif
            </td>
            <td style="padding:10px 14px">
              <div class="d-flex align-items-center gap-2">
                <span class="avatar-initial rounded-circle bg-label-secondary d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px;font-size:10px;font-weight:700">
                  {{ strtoupper(substr($ev->subidoPor->name ?? 'U', 0, 2)) }}
                </span>
                <span class="text-truncate" style="font-size:12px;max-width:100px">{{ $ev->subidoPor->name ?? '—' }}</span>
              </div>
            </td>
            <td style="padding:10px 14px;white-space:nowrap">
              <div style="font-size:12px">{{ $ev->created_at->format('d/m/Y') }}</div>
              <div class="text-muted" style="font-size:10px">{{ $ev->created_at->format('H:i') }}</div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted py-5">
            <i class="ti tabler-paperclip d-block mb-2" style="font-size:2.5rem;opacity:.25"></i>
            <small>Sin evidencias registradas aún</small>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Paginación JS --}}
    @if($evidencias_recientes->count() > 8)
    <div class="card-footer d-flex align-items-center justify-content-between py-2 px-4">
      <small class="text-muted" id="ev-pag-footer"></small>
      <div class="d-flex gap-1" id="ev-paginador"></div>
    </div>
    @endif
  </div>{{-- /evidencias --}}

</div>{{-- /tab-vista-general --}}

{{-- ══ TAB DETALLE POR COMPONENTE ══════════════════════════ --}}
<div class="tab-pane fade" id="tab-detalle-componente">
  <div class="card">
    <div class="card-header py-3 px-4">
      <span class="fw-semibold" style="font-size:14px">
        <i class="ti tabler-table me-2 text-primary"></i>Detalle por Componente
      </span>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead style="background:var(--bs-tertiary-bg)">
          <tr>
            <th class="ps-4" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;width:50px">#</th>
            <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Componente</th>
            <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;min-width:200px">Avance</th>
            <th class="text-center" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">✓ Completadas</th>
            <th class="text-center" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">⏱ En proceso</th>
            <th class="text-center" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">📎 Evidencias</th>
            <th class="text-center pe-4" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Nivel</th>
          </tr>
        </thead>
        <tbody>
          @forelse($componentes as $c)
          @php $nivelLabel = match($c->color) { 'success'=>'Cumplido','warning'=>'En proceso',default=>'En riesgo' }; @endphp
          <tr style="border-left:3px solid var(--bs-{{ $c->color }})">
            <td class="ps-4">
              <span class="badge bg-label-secondary rounded-pill fw-bold" style="font-size:11px">{{ $c->numero }}</span>
            </td>
            <td>
              <div class="fw-semibold" style="font-size:13.5px">{{ $c->nombre }}</div>
              <small class="text-muted" style="font-size:11px">{{ $c->etapa }}</small>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
                </div>
                <span class="fw-bold text-{{ $c->color }}" style="min-width:36px;font-size:13px">{{ $c->porcentaje }}%</span>
              </div>
            </td>
            <td class="text-center"><span class="fw-bold text-success" style="font-size:1.1rem">{{ $c->completadas_count }}</span></td>
            <td class="text-center"><span class="fw-bold text-warning" style="font-size:1.1rem">{{ $c->en_proceso_count }}</span></td>
            <td class="text-center"><span class="fw-bold text-info" style="font-size:1.1rem">{{ $c->evidencias_count }}</span></td>
            <td class="text-center pe-4">
              <span class="badge bg-label-{{ $c->color }} rounded-pill px-3" style="font-size:11px">{{ $nivelLabel }}</span>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-5">Sin componentes configurados</td></tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr style="background:var(--bs-tertiary-bg);border-top:2px solid rgba(var(--bs-secondary-rgb),.12)">
            <td class="ps-4" colspan="2">
              <span class="fw-bold text-muted" style="font-size:12px">TOTAL GENERAL</span>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-{{ $nc }} rounded-pill" style="width:{{ $gc }}%"></div>
                </div>
                <span class="fw-bold text-{{ $nc }}" style="min-width:36px;font-size:13px">{{ $gc }}%</span>
              </div>
            </td>
            <td class="text-center"><span class="fw-bold text-success" style="font-size:1.1rem">{{ $componentes->sum('completadas_count') }}</span></td>
            <td class="text-center"><span class="fw-bold text-warning" style="font-size:1.1rem">{{ $componentes->sum('en_proceso_count') }}</span></td>
            <td class="text-center"><span class="fw-bold text-info" style="font-size:1.1rem">{{ $componentes->sum('evidencias_count') }}</span></td>
            <td class="text-center pe-4">
              <span class="badge bg-label-{{ $nc }} rounded-pill px-3" style="font-size:11px">{{ $nivel }}</span>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

{{-- ══ TAB ACTIVIDADES ══════════════════════════════════════ --}}
<div class="tab-pane fade" id="tab-actividades">

  {{-- Filtros --}}
  <div class="card mb-3">
    <div class="card-body py-3 px-4">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label fw-semibold mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.04em">Etapa</label>
          <select id="act-f-etapa" class="form-select form-select-sm">
            <option value="">Todas las etapas</option>
            @foreach($etapas as $et)
            <option value="{{ $et->id }}">{{ $et->anio }} · {{ $et->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.04em">Componente</label>
          <select id="act-f-componente" class="form-select form-select-sm" disabled>
            <option value="">— Selecciona etapa —</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.04em">Estado</label>
          <select id="act-f-estado" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="pendiente">Pendiente</option>
            <option value="en_proceso">En proceso</option>
            <option value="completada">Completada</option>
            <option value="observado">Observado</option>
            <option value="vencida">Vencida</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.04em">Buscar</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="ti tabler-search icon-14px"></i></span>
            <input type="text" id="act-f-buscar" class="form-control" placeholder="Código o nombre…">
          </div>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button id="act-f-limpiar" class="btn btn-sm btn-label-secondary w-100" title="Limpiar filtros">
            <i class="ti tabler-filter-off"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabla actividades --}}
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-3 px-4">
      <span class="fw-semibold" style="font-size:14px">
        <i class="ti tabler-certificate me-2 text-warning"></i>Actividades de Integridad
      </span>
      <span class="badge bg-label-warning rounded-pill" id="act-contador">{{ $actividades->total() }} registros</span>
    </div>
    <div class="card-body p-0">
      <div class="position-relative" id="act-wrapper" style="min-height:80px">
        <div id="act-spinner" style="display:none;position:absolute;inset:0;z-index:10;background:rgba(255,255,255,.8);display:none;align-items:center;justify-content:center">
          <div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div><span>Cargando…</span>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle" id="act-tabla" style="min-width:860px">
            <thead style="background:var(--bs-tertiary-bg)">
              <tr>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 14px;white-space:nowrap">Código</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 14px;min-width:210px">Actividad</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 14px;min-width:130px">Componente</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 14px">Responsable</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 14px;min-width:140px">Avance</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 14px">Estado</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 14px;white-space:nowrap">Vence</th>
                @can('integridad.editar')<th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 14px;width:90px">Acciones</th>@endcan
              </tr>
            </thead>
            <tbody id="act-tbody">
              @forelse($actividades as $act)
              @php
                $ac   = match($act->estado) { 'completada'=>'success','vencida'=>'danger','observado'=>'warning','en_proceso'=>'info',default=>'secondary' };
                $comp = $act->integridadPregunta?->componente;
                $pct  = $act->avance ?? 0;
              @endphp
              <tr>
                <td style="padding:10px 14px"><code style="font-size:10.5px">{{ $act->codigo }}</code></td>
                <td style="padding:10px 14px">
                  <div class="fw-semibold text-truncate" style="font-size:13px;max-width:200px">{{ $act->nombre }}</div>
                  @if($act->numero_sgd)<small class="text-muted font-monospace" style="font-size:10px">{{ $act->numero_sgd }}</small>@endif
                </td>
                <td>
                  <div style="font-size:12px;font-weight:600">{{ Str::limit($comp?->nombre ?? '—', 30) }}</div>
                  @if($act->integridadPregunta)
                  <div class="text-muted" style="font-size:10px" title="{{ $act->integridadPregunta->nombre }}">
                    {{ Str::limit($act->integridadPregunta->nombre, 35) }}
                  </div>
                  @if($act->integridadPregunta->link_ficha)
                  <a href="{{ $act->integridadPregunta->link_ficha }}" target="_blank" class="badge bg-label-info mt-1" style="font-size:9px">
                    <i class="ti tabler-external-link me-1"></i>Ficha
                  </a>
                  @endif
                  @endif
                </td>
                <td style="font-size:12px">
                  {{ $act->responsables->where('pivot.tipo','principal')->first()?->name ?? $act->responsables->first()?->name ?? '—' }}
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:6px;min-width:70px">
                      <div class="progress-bar bg-{{ $ac }} rounded-pill" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="fw-bold text-{{ $ac }}" style="font-size:12px;min-width:32px">{{ $pct }}%</span>
                  </div>
                </td>
                <td><span class="badge bg-label-{{ $ac }}" style="font-size:11px">{{ ucfirst(str_replace('_',' ',$act->estado)) }}</span></td>
                <td>
                  <small class="{{ $act->fecha_limite && $act->fecha_limite->lt(now()) && $act->estado !== 'completada' ? 'text-danger fw-bold' : 'text-muted' }}" style="font-size:11px">
                    {{ $act->fecha_limite?->format('d/m/Y') ?? '—' }}
                  </small>
                </td>
                @can('integridad.editar')
                <td>
                  <div class="d-flex gap-1">
                    <button class="btn btn-icon btn-label-primary btn-editar-act" style="width:30px;height:30px;padding:0;border-radius:8px"
                      data-id="{{ $act->id }}"
                      data-nombre="{{ $act->nombre }}"
                      data-pregunta="{{ $act->integridad_pregunta_id }}"
                      data-componente="{{ $act->integridadPregunta?->componente_id }}"
                      data-etapa="{{ $act->integridadPregunta?->componente?->etapa_id }}"
                      data-unidad="{{ $act->unidad_organica_id ?? '' }}"
                      data-inicio="{{ $act->fecha_inicio?->format('Y-m-d') ?? '' }}"
                      data-limite="{{ $act->fecha_limite?->format('Y-m-d') ?? '' }}"
                      data-avance="{{ $act->avance }}"
                      data-estado="{{ $act->estado }}"
                      data-prioridad="{{ $act->prioridad }}"
                      data-sgd="{{ $act->numero_sgd ?? '' }}"
                      data-descripcion="{{ $act->descripcion ?? '' }}"
                      data-observaciones="{{ $act->observaciones ?? '' }}"
                      data-action="{{ route('integridad.update', $act) }}"
                      title="Editar"><i class="ti tabler-edit icon-14px"></i></button>
                    <form method="POST" action="{{ route('integridad.destroy', $act) }}" class="form-eliminar-act d-inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-icon btn-label-secondary" style="width:30px;height:30px;padding:0;border-radius:8px" title="Eliminar">
                        <i class="ti tabler-trash icon-14px"></i>
                      </button>
                    </form>
                  </div>
                </td>
                @endcan
              </tr>
              @empty
              <tr><td colspan="8">
                <div class="text-center py-5 text-muted">
                  <i class="ti tabler-certificate" style="font-size:3rem;opacity:.3"></i>
                  <div class="fw-semibold mt-2">No hay actividades de integridad registradas</div>
                  <div style="font-size:13px">Usa el botón "Nueva Actividad" para comenzar.</div>
                </div>
              </td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @if($actividades->hasPages())
    <div class="card-footer d-flex justify-content-center py-2">
      {{ $actividades->links() }}
    </div>
    @endif
  </div>
</div>{{-- /tab-actividades --}}

</div>{{-- /tab-content --}}

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- Modal Nueva Actividad Integridad                                    --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
@can('integridad.crear')
<div class="modal fade" id="modalNuevaActInt" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="{{ route('integridad.store') }}" id="formNuevaActInt">
        @csrf
        <input type="hidden" name="modulo" value="integridad">
        <div class="modal-header" style="background:linear-gradient(135deg,#ff9f43,#ffbe76);color:#fff">
          <h5 class="modal-title" style="color:#fff"><i class="ti tabler-certificate me-2"></i>Nueva Actividad — Modelo de Integridad</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">

            {{-- ── Cascada Etapa → Componente → Pregunta ── --}}
            <div class="col-12"><h6 class="fw-bold text-warning mb-0"><i class="ti tabler-hierarchy me-1"></i>Estructura del Modelo de Integridad</h6><hr class="mt-1 mb-0"></div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Etapa <span class="text-danger">*</span></label>
              <select name="_etapa_id" id="nueva_etapa_id" class="form-select" required>
                <option value="">— Seleccionar etapa —</option>
                @foreach($etapas as $et)
                <option value="{{ $et->id }}">{{ $et->anio }} · {{ $et->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Componente <span class="text-danger">*</span></label>
              <select name="_componente_id" id="nueva_componente_id" class="form-select" required disabled>
                <option value="">— Selecciona etapa primero —</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Pregunta / Medida <span class="text-danger">*</span></label>
              <select name="integridad_pregunta_id" id="nueva_pregunta_id" class="form-select" required disabled>
                <option value="">— Selecciona componente —</option>
              </select>
              <div id="nueva-ficha-link" class="mt-1" style="display:none">
                <a id="nueva-ficha-url" href="#" target="_blank" class="badge bg-label-info">
                  <i class="ti tabler-external-link me-1"></i>Ver ficha técnica
                </a>
              </div>
            </div>

            {{-- ── Datos actividad ── --}}
            <div class="col-12 mt-2"><h6 class="fw-bold text-muted mb-0"><i class="ti tabler-clipboard-list me-1"></i>Datos de la Actividad</h6><hr class="mt-1 mb-0"></div>

            <div class="col-12">
              <label class="form-label fw-semibold">Título / Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" placeholder="Describe la actividad a desarrollar" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Año <span class="text-danger">*</span></label>
              <input type="number" name="anio" class="form-control" value="{{ now()->year }}" min="2020" max="2099" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2-nueva-int">
                <option value="">— Sin asignar —</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->sigla }} — {{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Prioridad <span class="text-danger">*</span></label>
              <select name="prioridad" class="form-select" required>
                <option value="alta">Alta</option>
                <option value="media" selected>Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha inicio</label>
              <input type="date" name="fecha_inicio" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha límite <span class="text-danger">*</span></label>
              <input type="date" name="fecha_limite" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">N° SGD / Expediente</label>
              <input type="text" name="numero_sgd" class="form-control" placeholder="SGD-2026-001">
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2" placeholder="Descripción detallada de la actividad…"></textarea>
            </div>

            {{-- Responsables --}}
            <div class="col-12 mt-2"><h6 class="fw-bold text-muted mb-0"><i class="ti tabler-users me-1"></i>Responsables</h6><hr class="mt-1 mb-0"></div>

            <div class="col-12" id="nueva-responsables-container">
              <div class="row g-2 mb-2 responsable-row">
                <div class="col-md-8">
                  <select name="responsables[]" class="form-select select2-resp-nueva">
                    <option value="">— Seleccionar responsable —</option>
                    @foreach($usuarios as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <select name="tipos[_idx_]" class="form-select tipos-nueva">
                    <option value="principal">Principal</option>
                    <option value="colaborador">Colaborador</option>
                    <option value="supervisor">Supervisor</option>
                  </select>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                  <button type="button" class="btn btn-icon btn-label-secondary btn-rm-resp" style="width:34px;height:34px;padding:0">
                    <i class="ti tabler-trash icon-14px"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-12">
              <button type="button" id="btn-add-resp-nueva" class="btn btn-sm btn-label-secondary">
                <i class="ti tabler-plus me-1"></i>Agregar responsable
              </button>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning"><i class="ti tabler-device-floppy me-1"></i>Crear Actividad</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan

{{-- Modal Editar Actividad Integridad --}}
@can('integridad.editar')
<div class="modal fade" id="modalEditarActInt" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" id="formEditarActInt">
        @csrf @method('PUT')
        <div class="modal-header" style="background:linear-gradient(135deg,#ff9f43,#ffbe76);color:#fff">
          <h5 class="modal-title" style="color:#fff"><i class="ti tabler-edit me-2"></i>Editar Actividad — Integridad</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">

            <div class="col-12"><h6 class="fw-bold text-warning mb-0"><i class="ti tabler-hierarchy me-1"></i>Estructura</h6><hr class="mt-1 mb-0"></div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Etapa</label>
              <select id="edit_etapa_id" class="form-select" required>
                <option value="">— Seleccionar etapa —</option>
                @foreach($etapas as $et)
                <option value="{{ $et->id }}">{{ $et->anio }} · {{ $et->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Componente</label>
              <select id="edit_componente_id" class="form-select" required disabled>
                <option value="">— Selecciona etapa —</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Pregunta / Medida</label>
              <select name="integridad_pregunta_id" id="edit_pregunta_id" class="form-select" required disabled>
                <option value="">— Selecciona componente —</option>
              </select>
              <div id="edit-ficha-link" class="mt-1" style="display:none">
                <a id="edit-ficha-url" href="#" target="_blank" class="badge bg-label-info">
                  <i class="ti tabler-external-link me-1"></i>Ver ficha técnica
                </a>
              </div>
            </div>

            <div class="col-12 mt-2"><h6 class="fw-bold text-muted mb-0"><i class="ti tabler-clipboard-list me-1"></i>Datos</h6><hr class="mt-1 mb-0"></div>

            <div class="col-12">
              <label class="form-label fw-semibold">Título / Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" id="edit_unidad" class="form-select select2-editar-int">
                <option value="">— Sin asignar —</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->sigla }} — {{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Prioridad</label>
              <select name="prioridad" id="edit_prioridad" class="form-select">
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado</label>
              <select name="estado" id="edit_estado" class="form-select">
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En proceso</option>
                <option value="completada">Completada</option>
                <option value="observado">Observado</option>
                <option value="vencida">Vencida</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label fw-semibold">Avance %</label>
              <input type="number" name="avance" id="edit_avance" class="form-control" min="0" max="100">
            </div>

            <div class="col-md-3">
              <label class="form-label fw-semibold">Fecha inicio</label>
              <input type="date" name="fecha_inicio" id="edit_inicio" class="form-control">
            </div>

            <div class="col-md-3">
              <label class="form-label fw-semibold">Fecha límite</label>
              <input type="date" name="fecha_limite" id="edit_limite" class="form-control" required>
            </div>

            <div class="col-md-3">
              <label class="form-label fw-semibold">N° SGD</label>
              <input type="text" name="numero_sgd" id="edit_sgd" class="form-control">
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="2"></textarea>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="2"></textarea>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning"><i class="ti tabler-device-floppy me-1"></i>Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark  = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const avance  = {{ $gc }};
  const umbralV = {{ $umbral_verde }};
  const umbralA = {{ $umbral_amarillo }};
  const color   = avance >= umbralV ? '#28c76f' : (avance >= umbralA ? '#ff9f43' : '#ea5455');

  new ApexCharts(document.getElementById('gaugeModelo'), {
    chart: { type: 'radialBar', height: 100, width: 100, sparkline: { enabled: true } },
    series: [avance],
    plotOptions: {
      radialBar: {
        startAngle: -135, endAngle: 135,
        hollow: { size: '55%' },
        track: { background: isDark ? '#3d3d3d' : '#e8e8e8', strokeWidth: '97%' },
        dataLabels: { name: { show: false }, value: { show: false } },
      }
    },
    fill:   { colors: [color] },
    stroke: { lineCap: 'round' },
  }).render();

  // Flash messages los maneja el toast global del layout (contentNavbarLayout)

  // ── Select2: inicializar dentro de cada modal ───────────────────────────
  function initSelect2InModal(modalId) {
    const $modal = $('#' + modalId);
    $modal.find('.select2-nueva-int, .select2-editar-int, .select2-resp-nueva').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible')) {
        $(this).select2({ dropdownParent: $modal, width: '100%' });
      }
    });
  }

  // Función para envolver un select nativo en Select2 dentro del modal
  function initCascadeSelect2(selectEl, modalId) {
    const $modal = $('#' + modalId);
    if ($(selectEl).hasClass('select2-hidden-accessible')) {
      $(selectEl).select2('destroy');
    }
    $(selectEl).select2({ dropdownParent: $modal, width: '100%' });
  }

  document.getElementById('modalNuevaActInt')?.addEventListener('shown.bs.modal', function () {
    const $modal = $('#modalNuevaActInt');
    // Cascada + unidad
    ['nueva_etapa_id','nueva_componente_id','nueva_pregunta_id'].forEach(id => {
      initCascadeSelect2(document.getElementById(id), 'modalNuevaActInt');
    });
    $modal.find('.select2-nueva-int').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible'))
        $(this).select2({ dropdownParent: $modal, width: '100%' });
    });
    // Responsables existentes
    $modal.find('.select2-resp-nueva').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible'))
        $(this).select2({ dropdownParent: $modal, width: '100%', placeholder: '— Seleccionar responsable —' });
    });
  });

  document.getElementById('modalEditarActInt')?.addEventListener('shown.bs.modal', function () {
    const $modal = $('#modalEditarActInt');
    ['edit_etapa_id','edit_componente_id','edit_pregunta_id'].forEach(id => {
      initCascadeSelect2(document.getElementById(id), 'modalEditarActInt');
    });
    $modal.find('.select2-editar-int').each(function () {
      if (!$(this).hasClass('select2-hidden-accessible'))
        $(this).select2({ dropdownParent: $modal, width: '100%' });
    });
  });

  // Re-inicializar Select2 en selects de cascada después de cargar opciones vía AJAX
  function reinitSelect2Cascade(selectEl, modalId) {
    const $sel = $(selectEl);
    const val  = $sel.val();
    if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
    $sel.select2({ dropdownParent: $('#' + modalId), width: '100%' });
    if (val) $sel.val(val).trigger('change.select2');
  }

  // ── URLs AJAX cascada ───────────────────────────────────────────────────
  const URL_COMPONENTES = '{{ route('integridad.componentes') }}';
  const URL_PREGUNTAS   = '{{ route('integridad.preguntas') }}';

  // ── Helper: cargar opciones via fetch ──────────────────────────────────
  async function fetchOpciones(url, params) {
    const res = await fetch(url + '?' + new URLSearchParams(params).toString(), {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    return res.json();
  }

  // ── Cascada en modal NUEVA ──────────────────────────────────────────────
  const nuevoEtapa      = document.getElementById('nueva_etapa_id');
  const nuevoComponente = document.getElementById('nueva_componente_id');
  const nuevoPregunta   = document.getElementById('nueva_pregunta_id');
  const nuevaFichaDiv   = document.getElementById('nueva-ficha-link');
  const nuevaFichaUrl   = document.getElementById('nueva-ficha-url');

  function resetSelect(sel, placeholder, disabled = true) {
    sel.innerHTML = `<option value="">${placeholder}</option>`;
    sel.disabled  = disabled;
  }

  if (nuevoEtapa) {
    $(nuevoEtapa).on('change', async function () {
      resetSelect(nuevoComponente, '— Cargando… —', true);
      resetSelect(nuevoPregunta, '— Selecciona componente —', true);
      nuevaFichaDiv.style.display = 'none';
      if (!this.value) { resetSelect(nuevoComponente, '— Selecciona etapa primero —'); return; }
      const data = await fetchOpciones(URL_COMPONENTES, { etapa_id: this.value });
      nuevoComponente.innerHTML = '<option value="">— Seleccionar componente —</option>';
      data.forEach(c => nuevoComponente.innerHTML += `<option value="${c.id}">${c.orden}. ${c.nombre}</option>`);
      nuevoComponente.disabled = false;
      reinitSelect2Cascade(nuevoComponente, 'modalNuevaActInt');
    });

    $(nuevoComponente).on('change', async function () {
      resetSelect(nuevoPregunta, '— Cargando… —', true);
      nuevaFichaDiv.style.display = 'none';
      if (!this.value) { resetSelect(nuevoPregunta, '— Selecciona componente —'); return; }
      const data = await fetchOpciones(URL_PREGUNTAS, { componente_id: this.value });
      nuevoPregunta.innerHTML = '<option value="">— Seleccionar pregunta —</option>';
      data.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = `${p.orden}. ${p.nombre}`;
        if (p.link_ficha) opt.dataset.ficha = p.link_ficha;
        nuevoPregunta.appendChild(opt);
      });
      nuevoPregunta.disabled = false;
      reinitSelect2Cascade(nuevoPregunta, 'modalNuevaActInt');
    });

    $(nuevoPregunta).on('change', function () {
      const opt = this.selectedOptions[0];
      if (opt?.dataset.ficha) {
        nuevaFichaUrl.href = opt.dataset.ficha;
        nuevaFichaDiv.style.display = '';
      } else {
        nuevaFichaDiv.style.display = 'none';
      }
    });
  }

  // ── Cascada en modal EDITAR ─────────────────────────────────────────────
  const editEtapa      = document.getElementById('edit_etapa_id');
  const editComponente = document.getElementById('edit_componente_id');
  const editPregunta   = document.getElementById('edit_pregunta_id');
  const editFichaDiv   = document.getElementById('edit-ficha-link');
  const editFichaUrl   = document.getElementById('edit-ficha-url');

  if (editEtapa) {
    $(editEtapa).on('change', async function () {
      resetSelect(editComponente, '— Cargando… —', true);
      resetSelect(editPregunta, '— Selecciona componente —', true);
      if (editFichaDiv) editFichaDiv.style.display = 'none';
      if (!this.value) return;
      const data = await fetchOpciones(URL_COMPONENTES, { etapa_id: this.value });
      editComponente.innerHTML = '<option value="">— Seleccionar componente —</option>';
      data.forEach(c => editComponente.innerHTML += `<option value="${c.id}">${c.orden}. ${c.nombre}</option>`);
      editComponente.disabled = false;
      reinitSelect2Cascade(editComponente, 'modalEditarActInt');
    });

    $(editComponente).on('change', async function () {
      resetSelect(editPregunta, '— Cargando… —', true);
      if (editFichaDiv) editFichaDiv.style.display = 'none';
      if (!this.value) return;
      const data = await fetchOpciones(URL_PREGUNTAS, { componente_id: this.value });
      editPregunta.innerHTML = '<option value="">— Seleccionar pregunta —</option>';
      data.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = `${p.orden}. ${p.nombre}`;
        if (p.link_ficha) opt.dataset.ficha = p.link_ficha;
        editPregunta.appendChild(opt);
      });
      editPregunta.disabled = false;
      reinitSelect2Cascade(editPregunta, 'modalEditarActInt');
    });

    $(editPregunta).on('change', function () {
      const opt = this.selectedOptions[0];
      if (opt?.dataset.ficha && editFichaDiv) {
        editFichaUrl.href = opt.dataset.ficha;
        editFichaDiv.style.display = '';
      } else if (editFichaDiv) {
        editFichaDiv.style.display = 'none';
      }
    });
  }

  // ── Abrir modal editar con datos pre-cargados ───────────────────────────
  document.querySelectorAll('.btn-editar-act').forEach(btn => {
    btn.addEventListener('click', async function () {
      const d = this.dataset;
      document.getElementById('edit_nombre').value       = d.nombre       ?? '';
      document.getElementById('edit_avance').value       = d.avance       ?? 0;
      document.getElementById('edit_estado').value       = d.estado       ?? 'pendiente';
      document.getElementById('edit_prioridad').value    = d.prioridad    ?? 'media';
      document.getElementById('edit_inicio').value       = d.inicio       ?? '';
      document.getElementById('edit_limite').value       = d.limite       ?? '';
      document.getElementById('edit_sgd').value          = d.sgd          ?? '';
      document.getElementById('edit_descripcion').value  = d.descripcion  ?? '';
      document.getElementById('edit_observaciones').value= d.observaciones ?? '';

      // Set unidad
      const selUnidad = document.getElementById('edit_unidad');
      if (selUnidad) selUnidad.value = d.unidad ?? '';

      document.getElementById('formEditarActInt').action = d.action;

      // Cargar cascada: etapa → componente → pregunta
      if (d.etapa) {
        editEtapa.value = d.etapa;
        editEtapa.dispatchEvent(new Event('change'));
        // Esperar carga componentes
        await new Promise(r => setTimeout(r, 300));
        if (d.componente) {
          editComponente.value = d.componente;
          editComponente.dispatchEvent(new Event('change'));
          await new Promise(r => setTimeout(r, 300));
          if (d.pregunta) {
            editPregunta.value = d.pregunta;
            editPregunta.dispatchEvent(new Event('change'));
          }
        }
      }

      new bootstrap.Modal(document.getElementById('modalEditarActInt')).show();
    });
  });

  // ── Abrir modal nueva desde botón header ───────────────────────────────
  document.getElementById('btnNuevaActividad')?.addEventListener('click', () => {
    // Ir al tab de actividades primero
    bootstrap.Tab.getOrCreateInstance(document.getElementById('tabActividadesLink')).show();
    setTimeout(() => new bootstrap.Modal(document.getElementById('modalNuevaActInt')).show(), 150);
  });

  // ── Eliminar actividad ──────────────────────────────────────────────────
  document.querySelectorAll('.form-eliminar-act').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      Swal.fire({
        title:'¿Eliminar actividad?', text:'Esta acción no se puede deshacer.', icon:'warning',
        showCancelButton:true, confirmButtonText:'<i class="ti tabler-trash me-1"></i>Sí, eliminar',
        cancelButtonText:'Cancelar',
        customClass:{ popup:'rounded-3', confirmButton:'btn btn-danger me-2', cancelButton:'btn btn-label-secondary' },
        buttonsStyling:false,
      }).then(r => { if (r.isConfirmed) form.submit(); });
    });
  });

  // ── Agregar responsables en modal nueva ────────────────────────────────
  const respContainer = document.getElementById('nueva-responsables-container');
  // Plantilla limpia (sin Select2 inicializado) para clonar
  let respIdx = 0;

  function addResponsableRow(container, modalId) {
    respIdx++;
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 responsable-row';
    row.innerHTML = `
      <div class="col-md-8">
        <select name="responsables[]" class="form-select select2-resp-nueva" style="width:100%">
          <option value="">— Seleccionar responsable —</option>
          @foreach($usuarios as $u)
          <option value="{{ $u->id }}">{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select name="tipos[${respIdx}]" class="form-select">
          <option value="principal">Principal</option>
          <option value="colaborador">Colaborador</option>
          <option value="supervisor">Supervisor</option>
        </select>
      </div>
      <div class="col-md-1 d-flex align-items-center">
        <button type="button" class="btn btn-icon btn-label-secondary btn-rm-resp" style="width:34px;height:34px;padding:0">
          <i class="ti tabler-trash icon-14px"></i>
        </button>
      </div>`;
    container.appendChild(row);
    // Inicializar Select2 en el nuevo select
    const $sel = $(row).find('.select2-resp-nueva');
    $sel.select2({ dropdownParent: $('#' + modalId), width: '100%', placeholder: '— Seleccionar responsable —' });
    bindRmResp();
  }

  document.getElementById('btn-add-resp-nueva')?.addEventListener('click', () => {
    addResponsableRow(respContainer, 'modalNuevaActInt');
  });

  function bindRmResp() {
    document.querySelectorAll('.btn-rm-resp').forEach(btn => {
      btn.onclick = function () {
        const rows = document.querySelectorAll('.responsable-row');
        if (rows.length > 1) {
          // Destruir Select2 antes de remover
          $(this.closest('.responsable-row')).find('.select2-resp-nueva').select2('destroy');
          this.closest('.responsable-row').remove();
        }
      };
    });
  }
  bindRmResp();

  // ── Paginación tabla Evidencias Recientes ──────────────────────────────
  (function () {
    const tbody    = document.getElementById('ev-tbody');
    if (!tbody) return;
    const rows     = Array.from(tbody.querySelectorAll('tr.ev-row'));
    if (rows.length === 0) return;
    const PER_PAGE = 8;
    const totalPag = Math.ceil(rows.length / PER_PAGE);
    const footer   = document.getElementById('ev-pag-footer');
    const paginador= document.getElementById('ev-paginador');
    if (!paginador) return;

    let currentPage = 1;

    function render(page) {
      currentPage = page;
      const start = (page - 1) * PER_PAGE;
      const end   = start + PER_PAGE;
      rows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
      if (footer) footer.textContent = `Mostrando ${start + 1}–${Math.min(end, rows.length)} de ${rows.length}`;

      paginador.innerHTML = '';
      // Prev
      const prev = document.createElement('button');
      prev.className = 'btn btn-xs btn-label-secondary' + (page === 1 ? ' disabled' : '');
      prev.innerHTML = '<i class="ti tabler-chevron-left"></i>';
      prev.onclick = () => page > 1 && render(page - 1);
      paginador.appendChild(prev);

      // Páginas
      for (let p = 1; p <= totalPag; p++) {
        const btn = document.createElement('button');
        btn.className = 'btn btn-xs ' + (p === page ? 'btn-primary' : 'btn-label-secondary');
        btn.textContent = p;
        btn.onclick = () => render(p);
        paginador.appendChild(btn);
      }

      // Next
      const next = document.createElement('button');
      next.className = 'btn btn-xs btn-label-secondary' + (page === totalPag ? ' disabled' : '');
      next.innerHTML = '<i class="ti tabler-chevron-right"></i>';
      next.onclick = () => page < totalPag && render(page + 1);
      paginador.appendChild(next);
    }

    render(1);
  })();

  // ── Filtros tab actividades ─────────────────────────────────────────────
  const RUTA_INT = '{{ route('sci-modelo-integridad') }}';
  const actWrapper  = document.getElementById('act-wrapper');
  const actTbody    = document.getElementById('act-tbody');
  const actContador = document.getElementById('act-contador');
  let actDebounce;

  async function fetchActividades() {
    clearTimeout(actDebounce);
    actDebounce = setTimeout(async () => {
      document.getElementById('act-spinner').style.display = 'flex';
      const params = new URLSearchParams();
      const etapa  = document.getElementById('act-f-etapa')?.value;
      const comp   = document.getElementById('act-f-componente')?.value;
      const estado = document.getElementById('act-f-estado')?.value;
      const buscar = document.getElementById('act-f-buscar')?.value;
      if (etapa)  params.set('etapa_id', etapa);
      if (comp)   params.set('componente_id', comp);
      if (estado) params.set('estado', estado);
      if (buscar) params.set('buscar', buscar);
      try {
        const res  = await fetch(RUTA_INT + '?' + params.toString(), {
          headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' }
        });
        const data = await res.json();
        actContador.textContent = data.total + ' registros';
        if (data.actividades.length === 0) {
          actTbody.innerHTML = `<tr><td colspan="8"><div class="text-center py-5 text-muted">
            <i class="ti tabler-search" style="font-size:2rem;opacity:.3"></i>
            <div class="mt-2">No se encontraron actividades con los filtros aplicados.</div></div></td></tr>`;
        } else {
          actTbody.innerHTML = data.actividades.map(a => {
            const ac = a.estado === 'completada' ? 'success' : a.estado === 'vencida' ? 'danger'
              : a.estado === 'observado' ? 'warning' : a.estado === 'en_proceso' ? 'info' : 'secondary';
            const fichaBtn = a.link_ficha
              ? `<a href="${a.link_ficha}" target="_blank" class="badge bg-label-info mt-1" style="font-size:9px"><i class="ti tabler-external-link me-1"></i>Ficha</a>` : '';
            return `<tr>
              <td><code style="font-size:11px">${a.codigo ?? ''}</code></td>
              <td><div class="fw-medium" style="font-size:13px">${a.nombre}</div></td>
              <td><div style="font-size:12px;font-weight:600">${a.componente}</div>
                <div class="text-muted" style="font-size:10px">${a.pregunta}</div>${fichaBtn}</td>
              <td style="font-size:12px">${a.responsable}</td>
              <td><div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px;min-width:70px">
                  <div class="progress-bar bg-${ac} rounded-pill" style="width:${a.avance}%"></div></div>
                <span class="fw-bold text-${ac}" style="font-size:12px">${a.avance}%</span></div></td>
              <td><span class="badge bg-label-${ac}" style="font-size:11px">${a.estado.replace('_',' ')}</span></td>
              <td><small class="${a.vencida ? 'text-danger fw-bold' : 'text-muted'}" style="font-size:11px">${a.fecha_limite ?? '—'}</small></td>
              <td></td>
            </tr>`;
          }).join('');
        }
      } finally {
        document.getElementById('act-spinner').style.display = 'none';
      }
    }, 260);
  }

  // Cascada filtro etapa → componente en filtros de tab
  document.getElementById('act-f-etapa')?.addEventListener('change', async function () {
    const sel = document.getElementById('act-f-componente');
    sel.innerHTML = '<option value="">— Cargando… —</option>';
    sel.disabled = true;
    if (this.value) {
      const data = await fetchOpciones(URL_COMPONENTES, { etapa_id: this.value });
      sel.innerHTML = '<option value="">Todos los componentes</option>';
      data.forEach(c => sel.innerHTML += `<option value="${c.id}">${c.orden}. ${c.nombre}</option>`);
      sel.disabled = false;
    } else {
      sel.innerHTML = '<option value="">— Selecciona etapa —</option>';
    }
    fetchActividades();
  });

  document.getElementById('act-f-componente')?.addEventListener('change', fetchActividades);
  document.getElementById('act-f-estado')?.addEventListener('change', fetchActividades);
  let busActDebounce;
  document.getElementById('act-f-buscar')?.addEventListener('input', () => {
    clearTimeout(busActDebounce);
    busActDebounce = setTimeout(fetchActividades, 400);
  });
  document.getElementById('act-f-limpiar')?.addEventListener('click', () => {
    document.getElementById('act-f-etapa').value     = '';
    document.getElementById('act-f-componente').value = '';
    document.getElementById('act-f-componente').disabled = true;
    document.getElementById('act-f-componente').innerHTML = '<option value="">— Selecciona etapa —</option>';
    document.getElementById('act-f-estado').value   = '';
    document.getElementById('act-f-buscar').value   = '';
    fetchActividades();
  });

});
</script>
@endsection
