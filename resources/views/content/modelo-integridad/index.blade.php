@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Modelo de Integridad - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-style')
<style>
/* ── Componente cards ─────────────────────────────────── */
.comp-card { border-radius: 12px; border: none; transition: transform .15s, box-shadow .15s; }
.comp-card:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,0,0,.10); }
.comp-name { font-size: 12.5px; font-weight: 600; line-height: 1.4; min-height: 2.8em; }

/* ── Alerta item ──────────────────────────────────────── */
.alerta-item:last-of-type { border-bottom: none !important; }

/* ── Tab nav ──────────────────────────────────────────── */
.nav-tabs .nav-link { font-size: 13.5px; padding: .55rem 1.1rem; }

/* ── KPI strip ────────────────────────────────────────── */
.kpi-strip .kpi-box { border-radius: 10px; padding: 14px 16px; }
</style>
@endsection

@section('content')

@php
  $gc    = round($avance_global);
  $nc    = $gc >= $umbral_verde ? 'success' : ($gc >= $umbral_amarillo ? 'warning' : 'danger');
  $nivel = $gc >= $umbral_verde ? 'Bueno'   : ($gc >= $umbral_amarillo ? 'Regular'  : 'En riesgo');
@endphp

{{-- ── Breadcrumb + Header ──────────────────────────────── --}}
<nav aria-label="breadcrumb" class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Modelo de Integridad</li>
  </ol>
</nav>

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1 d-flex align-items-center gap-2">
      Modelo de Integridad
      <span class="badge bg-label-success rounded-pill" style="font-size:11px">PCM — 9 componentes</span>
    </h4>
    <p class="mb-0 text-muted small">Monitorea el cumplimiento de los nueve componentes del Modelo de Integridad de la PCM.</p>
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

{{-- ── KPI Strip ─────────────────────────────────────────── --}}
<div class="row g-3 mb-4 kpi-strip">

  {{-- Gauge --}}
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card h-100 mb-0" style="border-top:3px solid var(--bs-{{ $nc }})">
      <div class="card-body d-flex align-items-center gap-3 py-3">
        <div id="gaugeModelo" style="min-width:80px"></div>
        <div>
          <div class="fw-bold" style="font-size:1.5rem;color:var(--bs-{{ $nc }});line-height:1">{{ $gc }}%</div>
          <div class="fw-semibold" style="font-size:12px">Índice global</div>
          <span class="badge bg-label-{{ $nc }} mt-1" style="font-size:10px">{{ $nivel }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- En avance --}}
  <div class="col-6 col-sm-3 col-xl-3">
    <div class="card h-100 mb-0 kpi-box" style="border-top:3px solid #28c76f">
      <div class="card-body py-3 px-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="badge rounded bg-label-success p-2"><i class="ti tabler-trending-up icon-md text-success"></i></span>
          <span class="fw-bold text-success" style="font-size:1.9rem;line-height:1">{{ $en_avance }}</span>
        </div>
        <div class="fw-semibold" style="font-size:12px">En avance</div>
        <div class="text-muted" style="font-size:11px">≥ {{ $umbral_verde }}%</div>
      </div>
    </div>
  </div>

  {{-- En riesgo --}}
  <div class="col-6 col-sm-3 col-xl-3">
    <div class="card h-100 mb-0" style="border-top:3px solid #ff9f43">
      <div class="card-body py-3 px-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="badge rounded bg-label-warning p-2"><i class="ti tabler-alert-triangle icon-md text-warning"></i></span>
          <span class="fw-bold text-warning" style="font-size:1.9rem;line-height:1">{{ $en_riesgo }}</span>
        </div>
        <div class="fw-semibold" style="font-size:12px">En riesgo</div>
        <div class="text-muted" style="font-size:11px">{{ $umbral_amarillo }}–{{ $umbral_verde - 1 }}%</div>
      </div>
    </div>
  </div>

  {{-- Críticos --}}
  <div class="col-6 col-sm-3 col-xl-3">
    <div class="card h-100 mb-0" style="border-top:3px solid #ea5455">
      <div class="card-body py-3 px-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="badge rounded bg-label-danger p-2"><i class="ti tabler-urgent icon-md text-danger"></i></span>
          <span class="fw-bold text-danger" style="font-size:1.9rem;line-height:1">{{ $criticos }}</span>
        </div>
        <div class="fw-semibold" style="font-size:12px">Críticos</div>
        <div class="text-muted" style="font-size:11px">&lt; {{ $umbral_amarillo }}%</div>
      </div>
    </div>
  </div>

</div>

{{-- ── Tabs ──────────────────────────────────────────────── --}}
<ul class="nav nav-tabs mb-4" role="tablist">
  <li class="nav-item">
    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#tab-vista-general">
      <i class="ti tabler-layout-dashboard me-1"></i>Vista General
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tab-detalle-componente">
      <i class="ti tabler-table me-1"></i>Detalle por Componente
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tab-actividades" id="tabActividadesLink">
      <i class="ti tabler-list-check me-1"></i>Actividades
      <span class="badge bg-label-warning ms-1 rounded-pill" style="font-size:10px">{{ $actividades->count() }}</span>
    </a>
  </li>
</ul>

<div class="tab-content">

{{-- ══════════════════════════════════════════════════════════
     TAB VISTA GENERAL
══════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade show active" id="tab-vista-general">
<div class="row g-4">

  {{-- ── Columna principal (8/12) ── --}}
  <div class="col-xl-8">

    {{-- Tarjetas de Componentes --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
      <span class="text-muted small fw-semibold" style="text-transform:uppercase;letter-spacing:.04em">
        <i class="ti tabler-components me-1"></i>9 Componentes del Modelo
      </span>
      <small class="text-muted">Última act.: {{ now()->translatedFormat('d \d\e F, H:i') }}</small>
    </div>

    <div class="row g-3 mb-4">
      @forelse($componentes as $c)
      @php
        $nivelLabel = match($c->color) { 'success'=>'Cumplido', 'warning'=>'En proceso', default=>'En riesgo' };
        $nivelIcon  = match($c->color) { 'success'=>'tabler-circle-check', 'warning'=>'tabler-clock', default=>'tabler-alert-triangle' };
      @endphp
      <div class="col-12 col-sm-6 col-xxl-4">
        <div class="card comp-card h-100 mb-0" style="border-top:3px solid var(--bs-{{ $c->color }})">
          <div class="card-body pb-2 pt-3 px-3">

            {{-- Número + badge --}}
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="badge bg-label-secondary rounded-pill" style="font-size:10px">Comp. {{ $c->numero }}</span>
              <span class="badge bg-label-{{ $c->color }}" style="font-size:10px">
                <i class="ti {{ $nivelIcon }} me-1" style="font-size:9px"></i>{{ $nivelLabel }}
              </span>
            </div>

            {{-- Nombre completo --}}
            <div class="comp-name mb-2 text-body">{{ $c->nombre }}</div>

            {{-- % + barra --}}
            <div class="d-flex align-items-end gap-1 mb-1">
              <span class="fw-bold text-{{ $c->color }}" style="font-size:1.8rem;line-height:1">{{ $c->porcentaje }}</span>
              <span class="text-muted fw-semibold mb-1">%</span>
            </div>
            <div class="progress mb-3" style="height:5px">
              <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
            </div>

            {{-- Mini stats --}}
            <div class="d-flex align-items-center gap-3" style="font-size:11px">
              <span class="text-success fw-semibold">
                <i class="ti tabler-check me-1"></i>{{ $c->completadas_count }} hecho
              </span>
              <span class="text-warning fw-semibold">
                <i class="ti tabler-clock me-1"></i>{{ $c->en_proceso_count }} proceso
              </span>
              <span class="text-info fw-semibold ms-auto">
                <i class="ti tabler-files me-1"></i>{{ $c->evidencias_count }}
              </span>
            </div>

          </div>
          <div class="card-footer py-2 px-3">
            <a href="{{ route('sci-evidencias', ['componente_id' => $c->id]) }}"
               class="btn btn-xs btn-label-{{ $c->color }} w-100 text-center">
              <i class="ti tabler-upload me-1" style="font-size:11px"></i>Registrar evidencia
            </a>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12">
        <div class="card"><div class="card-body text-center text-muted py-5">
          <i class="ti tabler-components icon-32px d-block mb-2"></i>No hay componentes configurados.
        </div></div>
      </div>
      @endforelse
    </div>

    {{-- Evidencias Recientes --}}
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0 fw-semibold"><i class="ti tabler-files me-2 text-primary"></i>Evidencias Recientes</h5>
        <a href="{{ route('sci-evidencias') }}" class="btn btn-sm btn-label-primary">
          Ver todas <i class="ti tabler-arrow-right ms-1"></i>
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle" style="min-width:640px">
          <thead style="background:rgba(var(--bs-secondary-rgb),.04)">
            <tr>
              <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;white-space:nowrap">N° SGD</th>
              <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px">Título</th>
              <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px">Componente</th>
              <th class="text-center" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px">Estado</th>
              <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px">Subido por</th>
              <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;white-space:nowrap">Fecha</th>
            </tr>
          </thead>
          <tbody>
            @forelse($evidencias_recientes as $ev)
            @php
              $ec = match($ev->estado) { 'validado'=>'success', 'rechazado'=>'danger', default=>'warning' };
              $el = match($ev->estado) { 'validado'=>'Validado', 'rechazado'=>'Rechazado', default=>'Pendiente' };
            @endphp
            <tr>
              <td style="padding:10px 14px">
                <span class="badge bg-label-primary font-monospace" style="font-size:11px">{{ $ev->numero_sgd ?? '—' }}</span>
              </td>
              <td style="padding:10px 14px;max-width:220px">
                <div class="fw-medium text-truncate" style="font-size:13px">{{ $ev->titulo }}</div>
                @if($ev->descripcion)
                  <small class="text-muted text-truncate d-block">{{ Str::limit($ev->descripcion, 55) }}</small>
                @endif
              </td>
              <td style="padding:10px 14px">
                @if($ev->actividad?->componente)
                  <span class="badge bg-label-secondary" style="font-size:10px">Comp. {{ $ev->actividad->componente->numero }}</span>
                  <div class="text-muted text-truncate" style="font-size:11px;max-width:130px">{{ $ev->actividad->componente->nombre }}</div>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td class="text-center" style="padding:10px 14px">
                <span class="badge bg-label-{{ $ec }}" style="font-size:11px">{{ $el }}</span>
              </td>
              <td style="padding:10px 14px">
                <div class="d-flex align-items-center gap-2">
                  <span class="avatar-initial rounded-circle bg-label-secondary d-inline-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:28px;height:28px;font-size:10px;font-weight:700">
                    {{ strtoupper(substr($ev->subidoPor->name ?? 'U', 0, 2)) }}
                  </span>
                  <span class="text-muted text-truncate" style="font-size:12px;max-width:110px">{{ $ev->subidoPor->name ?? '—' }}</span>
                </div>
              </td>
              <td style="padding:10px 14px">
                <small class="text-muted">{{ $ev->created_at->format('d/m/Y') }}</small>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-5">
                <i class="ti tabler-files d-block mb-2" style="font-size:2rem;opacity:.3"></i>
                Sin evidencias registradas aún
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>{{-- /col-xl-8 --}}

  {{-- ── Sidebar (4/12) ── --}}
  <div class="col-xl-4">

    {{-- Alertas Activas --}}
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0 fw-semibold" style="font-size:14px">
          <i class="ti tabler-bell-ringing me-2 text-danger"></i>Alertas Activas
        </h5>
        <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-danger">Ver todas</a>
      </div>
      <div class="card-body p-0">
        @forelse($alertas_activas as $al)
        @php $ic = match($al->prioridad) { 'alta'=>'danger', 'media'=>'warning', default=>'info' }; @endphp
        <div class="alerta-item d-flex align-items-start gap-3 px-3 py-3 border-bottom">
          <span class="avatar-initial rounded-circle bg-label-{{ $ic }} d-inline-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                style="width:32px;height:32px">
            <i class="ti tabler-{{ $al->prioridad === 'alta' ? 'alert-octagon' : 'alert-circle' }}" style="font-size:14px"></i>
          </span>
          <div class="flex-grow-1 overflow-hidden">
            <p class="mb-1 fw-semibold text-truncate" style="font-size:12.5px">{{ $al->titulo }}</p>
            @if($al->actividad?->componente)
            <div class="text-muted text-truncate" style="font-size:11px">{{ $al->actividad->componente->nombre }}</div>
            @endif
            <div class="d-flex align-items-center gap-2 mt-1">
              <span class="badge bg-label-{{ $ic }}" style="font-size:10px">{{ ucfirst($al->prioridad) }}</span>
              <small class="text-muted">{{ $al->created_at->diffForHumans() }}</small>
            </div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-5 px-4">
          <i class="ti tabler-bell-off d-block mb-2 text-success" style="font-size:2rem"></i>
          <small>Sin alertas activas</small>
        </div>
        @endforelse
        <div class="px-3 py-2 bg-body-secondary" style="border-radius:0 0 var(--bs-card-border-radius) var(--bs-card-border-radius)">
          <small class="text-muted d-flex align-items-start gap-2" style="font-size:11px">
            <i class="ti tabler-info-circle text-info flex-shrink-0 mt-px"></i>
            Las alertas se envían al correo del responsable asignado.
          </small>
        </div>
      </div>
    </div>

    {{-- Próximas Acciones --}}
    <div class="card">
      <div class="card-header py-3">
        <h5 class="mb-0 fw-semibold" style="font-size:14px">
          <i class="ti tabler-clock-exclamation me-2 text-warning"></i>Próximas Acciones
        </h5>
      </div>
      <div class="card-body py-3 px-3">
        @forelse($proximas_acciones as $act)
        @php
          $dias = (int) round(now()->diffInDays($act->fecha_limite, false));
          $tc   = $dias <= 3 ? 'danger' : ($dias <= 7 ? 'warning' : 'primary');
        @endphp
        <div class="d-flex align-items-start gap-3 mb-3">
          <span class="badge rounded bg-label-{{ $tc }} p-1_5 flex-shrink-0 mt-1">
            <i class="ti tabler-{{ $tc === 'danger' ? 'urgent' : 'clock' }}" style="font-size:13px"></i>
          </span>
          <div class="flex-grow-1 overflow-hidden">
            <div class="d-flex align-items-start justify-content-between gap-1">
              <p class="mb-0 fw-semibold text-truncate" style="font-size:12.5px">{{ $act->nombre }}</p>
              <span class="badge bg-label-{{ $tc }} flex-shrink-0 ms-1" style="font-size:10px;white-space:nowrap">
                @if($dias <= 0) Vencida
                @elseif($dias == 1) Mañana
                @else {{ $act->fecha_limite->format('d/m') }}
                @endif
              </span>
            </div>
            <small class="text-muted" style="font-size:11px">{{ $act->componente->nombre ?? '—' }}</small>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">
          <i class="ti tabler-circle-check d-block mb-2 text-success" style="font-size:2rem"></i>
          <small>Sin acciones pendientes próximas</small>
        </div>
        @endforelse
      </div>
    </div>

  </div>{{-- /col-xl-4 --}}

</div>{{-- /row --}}
</div>{{-- /tab-vista-general --}}

{{-- ══════════════════════════════════════════════════════════
     TAB DETALLE POR COMPONENTE
══════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade" id="tab-detalle-componente">
  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr style="background:var(--bs-tertiary-bg)">
            <th class="ps-4" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;width:50px">#</th>
            <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Componente</th>
            <th style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;min-width:180px">Avance</th>
            <th class="text-center" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Completadas</th>
            <th class="text-center" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">En proceso</th>
            <th class="text-center" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Evidencias</th>
            <th class="text-center pe-4" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Nivel</th>
          </tr>
        </thead>
        <tbody>
          @forelse($componentes as $c)
          @php $nivelLabel = match($c->color) { 'success'=>'Cumplido', 'warning'=>'En proceso', default=>'En riesgo' }; @endphp
          <tr style="border-left:3px solid var(--bs-{{ $c->color }})">
            <td class="ps-4">
              <span class="badge bg-label-secondary rounded-pill fw-bold" style="font-size:11px">{{ $c->numero }}</span>
            </td>
            <td>
              <div class="fw-semibold" style="font-size:13.5px">{{ $c->nombre }}</div>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:7px">
                  <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
                </div>
                <span class="fw-bold text-{{ $c->color }}" style="min-width:36px;font-size:13px">{{ $c->porcentaje }}%</span>
              </div>
            </td>
            <td class="text-center"><span class="fw-bold text-success fs-5">{{ $c->completadas_count }}</span></td>
            <td class="text-center"><span class="fw-bold text-warning fs-5">{{ $c->en_proceso_count }}</span></td>
            <td class="text-center"><span class="fw-bold text-info fs-5">{{ $c->evidencias_count }}</span></td>
            <td class="text-center pe-4">
              <span class="badge bg-label-{{ $c->color }} rounded-pill px-3 py-1" style="font-size:11px">{{ $nivelLabel }}</span>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-6">Sin componentes configurados</td></tr>
          @endforelse
        </tbody>
        {{-- Totales --}}
        <tfoot>
          <tr style="background:var(--bs-tertiary-bg);border-top:2px solid rgba(var(--bs-secondary-rgb),.15)">
            <td class="ps-4" colspan="2">
              <span class="fw-bold text-muted" style="font-size:12px">TOTALES</span>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:7px">
                  <div class="progress-bar bg-{{ $nc }} rounded-pill" style="width:{{ $gc }}%"></div>
                </div>
                <span class="fw-bold text-{{ $nc }}" style="min-width:36px;font-size:13px">{{ $gc }}%</span>
              </div>
            </td>
            <td class="text-center">
              <span class="fw-bold text-success fs-5">{{ $componentes->sum('completadas_count') }}</span>
            </td>
            <td class="text-center">
              <span class="fw-bold text-warning fs-5">{{ $componentes->sum('en_proceso_count') }}</span>
            </td>
            <td class="text-center">
              <span class="fw-bold text-info fs-5">{{ $componentes->sum('evidencias_count') }}</span>
            </td>
            <td class="text-center pe-4">
              <span class="badge bg-label-{{ $nc }} rounded-pill px-3 py-1" style="font-size:11px">{{ $nivel }}</span>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     TAB ACTIVIDADES
══════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade" id="tab-actividades">

  {{-- Filtros --}}
  <div class="card mb-3">
    <div class="card-body py-3 px-4">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label fw-semibold mb-1 text-uppercase" style="font-size:11px;letter-spacing:.04em">Etapa</label>
          <select id="act-f-etapa" class="form-select form-select-sm">
            <option value="">Todas las etapas</option>
            @foreach($etapas as $et)
            <option value="{{ $et->id }}">{{ $et->anio }} · {{ $et->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold mb-1 text-uppercase" style="font-size:11px;letter-spacing:.04em">Componente</label>
          <select id="act-f-componente" class="form-select form-select-sm" disabled>
            <option value="">— Selecciona etapa —</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold mb-1 text-uppercase" style="font-size:11px;letter-spacing:.04em">Estado</label>
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
          <label class="form-label fw-semibold mb-1 text-uppercase" style="font-size:11px;letter-spacing:.04em">Buscar</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="ti tabler-search icon-14px"></i></span>
            <input type="text" id="act-f-buscar" class="form-control" placeholder="Código o nombre…">
          </div>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button id="act-f-limpiar" class="btn btn-sm btn-label-secondary w-100" title="Limpiar">
            <i class="ti tabler-filter-off"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabla actividades --}}
  <div class="card" style="border-radius:12px">
    <div class="card-header d-flex align-items-center justify-content-between py-3 px-4">
      <span class="fw-semibold" style="font-size:15px">
        <i class="ti tabler-certificate me-2 text-warning"></i>Actividades de Integridad
      </span>
      <span class="badge bg-label-warning rounded-pill" id="act-contador">{{ $actividades->count() }} registros</span>
    </div>
    <div class="card-body p-0">
      <div class="position-relative" id="act-wrapper" style="min-height:100px">
        <div id="act-spinner" style="display:none;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:10;background:#fff;border-radius:12px;padding:12px 20px;box-shadow:0 4px 20px rgba(0,0,0,.12)">
          <div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div>Cargando…
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle" id="act-tabla" style="min-width:900px">
            <thead>
              <tr>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;padding:12px 14px;background:rgba(var(--bs-secondary-rgb),.04)">Código</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;padding:12px 14px;background:rgba(var(--bs-secondary-rgb),.04);min-width:200px">Actividad</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;padding:12px 14px;background:rgba(var(--bs-secondary-rgb),.04);min-width:140px">Componente / Pregunta</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;padding:12px 14px;background:rgba(var(--bs-secondary-rgb),.04)">Responsable</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;padding:12px 14px;background:rgba(var(--bs-secondary-rgb),.04);min-width:160px">Avance</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;padding:12px 14px;background:rgba(var(--bs-secondary-rgb),.04)">Estado</th>
                <th style="font-size:11px;font-weight:700;text-transform:uppercase;padding:12px 14px;background:rgba(var(--bs-secondary-rgb),.04)">Vence</th>
                @can('integridad.editar')<th style="font-size:11px;font-weight:700;text-transform:uppercase;padding:12px 14px;background:rgba(var(--bs-secondary-rgb),.04);width:110px">Acciones</th>@endcan
              </tr>
            </thead>
            <tbody id="act-tbody">
              @forelse($actividades as $act)
              @php
                $ac = match($act->estado) { 'completada'=>'success','vencida'=>'danger','observado'=>'warning','en_proceso'=>'info',default=>'secondary' };
                $comp = $act->integridadPregunta?->componente;
                $pct  = $act->avance ?? 0;
              @endphp
              <tr>
                <td><code style="font-size:11px">{{ $act->codigo }}</code></td>
                <td>
                  <div class="fw-medium" style="font-size:13px">{{ Str::limit($act->nombre, 50) }}</div>
                  @if($act->numero_sgd)<small class="text-muted" style="font-size:10px">SGD: {{ $act->numero_sgd }}</small>@endif
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
  </div>
</div>{{-- /tab-actividades --}}

</div>{{-- /tab-content --}}

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- Modal Nueva Actividad Integridad                                    --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
@can('integridad.crear')
<div class="modal fade" id="modalNuevaActInt" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
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
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
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

  // ── Flash messages ──────────────────────────────────────────────────────
  @if(session('success'))
  Swal.fire({ icon:'success', title:'Listo', text:@json(session('success')), timer:2800, showConfirmButton:false,
    customClass:{ popup:'rounded-3' } });
  @endif
  @if($errors->any())
  Swal.fire({ icon:'error', title:'Error de validación', text:@json($errors->first()),
    customClass:{ popup:'rounded-3', confirmButton:'btn btn-primary' }, buttonsStyling:false });
  @endif

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
    nuevoEtapa.addEventListener('change', async function () {
      resetSelect(nuevoComponente, '— Cargando… —', true);
      resetSelect(nuevoPregunta, '— Selecciona componente —', true);
      nuevaFichaDiv.style.display = 'none';
      if (!this.value) { resetSelect(nuevoComponente, '— Selecciona etapa primero —'); return; }
      const data = await fetchOpciones(URL_COMPONENTES, { etapa_id: this.value });
      nuevoComponente.innerHTML = '<option value="">— Seleccionar componente —</option>';
      data.forEach(c => nuevoComponente.innerHTML += `<option value="${c.id}">${c.orden}. ${c.nombre}</option>`);
      nuevoComponente.disabled = false;
    });

    nuevoComponente.addEventListener('change', async function () {
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
    });

    nuevoPregunta.addEventListener('change', function () {
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
    editEtapa.addEventListener('change', async function () {
      resetSelect(editComponente, '— Cargando… —', true);
      resetSelect(editPregunta, '— Selecciona componente —', true);
      if (editFichaDiv) editFichaDiv.style.display = 'none';
      if (!this.value) return;
      const data = await fetchOpciones(URL_COMPONENTES, { etapa_id: this.value });
      editComponente.innerHTML = '<option value="">— Seleccionar componente —</option>';
      data.forEach(c => editComponente.innerHTML += `<option value="${c.id}">${c.orden}. ${c.nombre}</option>`);
      editComponente.disabled = false;
    });

    editComponente.addEventListener('change', async function () {
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
    });

    editPregunta.addEventListener('change', function () {
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
  const respTpl = respContainer?.querySelector('.responsable-row')?.outerHTML ?? '';
  let respIdx = 0;

  document.getElementById('btn-add-resp-nueva')?.addEventListener('click', () => {
    respIdx++;
    const div = document.createElement('div');
    div.innerHTML = respTpl.replace(/\[\]/g, `[${respIdx}]`).replace(/_idx_/g, respIdx);
    respContainer.appendChild(div.firstChild);
    bindRmResp();
  });

  function bindRmResp() {
    document.querySelectorAll('.btn-rm-resp').forEach(btn => {
      btn.onclick = function () {
        const rows = document.querySelectorAll('.responsable-row');
        if (rows.length > 1) this.closest('.responsable-row').remove();
      };
    });
  }
  bindRmResp();

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
