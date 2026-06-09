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
  <button class="btn btn-label-primary btn-sm align-self-start" data-bs-toggle="modal" data-bs-target="#modalGuiaModelo">
    <i class="ti tabler-book me-1"></i>Guía del Modelo
  </button>
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

</div>{{-- /tab-content --}}

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
});
</script>
@endsection
