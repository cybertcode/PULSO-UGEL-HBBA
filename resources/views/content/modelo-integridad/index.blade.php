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

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Modelo de Integridad</li>
  </ol>
</nav>

{{-- Header --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1 d-flex align-items-center gap-2">
      Modelo de Integridad
      <span class="badge bg-label-success rounded-pill" style="font-size:11px">Nuevos Componentes</span>
    </h4>
    <p class="mb-0 text-muted">Monitorea el cumplimiento de los nueve componentes del Modelo de Integridad de la PCM.<br>Registra tus evidencias y mantiene actualizada la información.</p>
  </div>
  <button class="btn btn-label-primary btn-sm align-self-start" data-bs-toggle="modal" data-bs-target="#modalGuiaModelo">
    <i class="ti tabler-book me-1"></i>Guía del Modelo
  </button>
</div>

{{-- Tabs Vista General / Detalle por Componente --}}
<ul class="nav nav-tabs mb-4" role="tablist">
  <li class="nav-item">
    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#tab-vista-general">Vista General</a>
  </li>
  <li class="nav-item">
    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tab-detalle-componente">Detalle por Componente</a>
  </li>
</ul>

<div class="tab-content">

{{-- Tab Vista General --}}
<div class="tab-pane fade show active" id="tab-vista-general">
<div class="row g-4">

  {{-- Columna izquierda (8/12) --}}
  <div class="col-xl-8">

    {{-- ── Índice de Cumplimiento General ── --}}
    <div class="card mb-4">
      <div class="card-body">
        <div class="row align-items-center g-4">

          {{-- Gauge --}}
          <div class="col-md-4 text-center">
            <div id="gaugeModelo"></div>
            @php
              $gc    = round($avance_global);
              $nivel = $gc >= $umbral_verde ? 'Bueno' : ($gc >= $umbral_amarillo ? 'Regular' : 'En riesgo');
              $nc    = $gc >= $umbral_verde ? 'success' : ($gc >= $umbral_amarillo ? 'warning' : 'danger');
            @endphp
            <p class="mb-1 fw-medium mt-n2">Índice de Cumplimiento</p>
            <span class="badge bg-label-{{ $nc }}">Nivel: {{ $nivel }}</span>
          </div>

          {{-- Contadores rápidos (estilo cards-statistics full-version) --}}
          <div class="col-md-8">
            <div class="row g-3">
              <div class="col-4">
                <div class="card mb-0 border-0 shadow-none bg-label-success rounded">
                  <div class="card-body p-3">
                    <div class="badge rounded bg-success p-1_5 mb-2">
                      <i class="icon-base ti tabler-trending-up icon-md"></i>
                    </div>
                    <h4 class="mb-0 text-success">{{ $en_avance }}</h4>
                    <small class="text-body-secondary">En avance</small>
                  </div>
                </div>
              </div>
              <div class="col-4">
                <div class="card mb-0 border-0 shadow-none bg-label-warning rounded">
                  <div class="card-body p-3">
                    <div class="badge rounded bg-warning p-1_5 mb-2">
                      <i class="icon-base ti tabler-alert-triangle icon-md"></i>
                    </div>
                    <h4 class="mb-0 text-warning">{{ $en_riesgo }}</h4>
                    <small class="text-body-secondary">En riesgo</small>
                  </div>
                </div>
              </div>
              <div class="col-4">
                <div class="card mb-0 border-0 shadow-none bg-label-danger rounded">
                  <div class="card-body p-3">
                    <div class="badge rounded bg-danger p-1_5 mb-2">
                      <i class="icon-base ti tabler-urgent icon-md"></i>
                    </div>
                    <h4 class="mb-0 text-danger">{{ $criticos }}</h4>
                    <small class="text-body-secondary">Críticos</small>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="d-flex justify-content-between mb-1">
                  <small class="text-muted">Avance global del modelo</small>
                  <small class="fw-bold text-{{ $nc }}">{{ $gc }}%</small>
                </div>
                <div class="progress" style="height:10px">
                  <div class="progress-bar bg-{{ $nc }} rounded-pill" style="width:{{ $gc }}%"></div>
                </div>
                <small class="text-muted">Última actualización: {{ now()->translatedFormat('d \d\e F, H:i') }}</small>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    {{-- ── Tarjetas de Componentes — grid 3 cols como el prototipo ── --}}
    <p class="text-muted mb-3" style="font-size:13px">Los 9 componentes del Modelo de Integridad con su nivel de cumplimiento actual</p>
    <div class="row g-3 mb-4">
      @forelse($componentes as $c)
      @php
        $nivelLabel = match($c->color) { 'success'=>'Cumplido', 'warning'=>'En proceso', default=>'En riesgo' };
        $nivelIcon  = match($c->color) { 'success'=>'tabler-circle-check', 'warning'=>'tabler-clock', default=>'tabler-alert-triangle' };
      @endphp
      <div class="col-12 col-sm-6 col-xl-4">
        <div class="card h-100" style="border-top:3px solid var(--bs-{{ $c->color }})">
          <div class="card-body pb-2">
            {{-- Número + nombre --}}
            <div class="d-flex align-items-start justify-content-between mb-2">
              <div class="fw-bold text-muted" style="font-size:12px">{{ $c->numero }}. {{ Str::limit($c->nombre, 28) }}</div>
              <span class="badge bg-label-{{ $c->color }} flex-shrink-0 ms-1" style="font-size:10px">{{ $nivelLabel }}</span>
            </div>
            {{-- % grande + barra --}}
            <div class="d-flex align-items-end gap-1 mb-1">
              <span class="fw-bold text-{{ $c->color }}" style="font-size:1.6rem;line-height:1">{{ $c->porcentaje }}</span>
              <span class="text-muted fw-semibold mb-1" style="font-size:13px">%</span>
              <span class="ms-auto">
                <i class="icon-base ti {{ $nivelIcon }} text-{{ $c->color }}" style="font-size:18px"></i>
              </span>
            </div>
            <div class="progress mb-2" style="height:5px">
              <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
            </div>
            {{-- Mini stats --}}
            <div class="d-flex align-items-center gap-2" style="font-size:11px">
              <span class="text-success fw-semibold"><i class="ti tabler-check icon-12px me-1"></i>{{ $c->completadas_count }}</span>
              <span class="text-muted">·</span>
              <span class="text-warning fw-semibold"><i class="ti tabler-clock icon-12px me-1"></i>{{ $c->en_proceso_count }}</span>
              <span class="text-muted">·</span>
              <span class="text-info fw-semibold"><i class="ti tabler-files icon-12px me-1"></i>{{ $c->evidencias_count }} evid.</span>
            </div>
          </div>
          <div class="card-footer py-2 px-3 d-flex gap-1">
            <a href="{{ route('sci-evidencias') }}?componente_id={{ $c->id }}"
               class="btn btn-xs btn-label-{{ $c->color }} flex-fill text-center">
              <i class="ti tabler-upload icon-12px me-1"></i>Subir N° SGD
            </a>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12">
        <div class="card">
          <div class="card-body text-center text-muted py-5">
            <i class="ti tabler-components icon-32px d-block mb-2"></i>
            No hay componentes configurados.
          </div>
        </div>
      </div>
      @endforelse
    </div>

    {{-- ── Tabla Registro de Evidencias Recientes ── --}}
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="ti tabler-files me-2"></i>Evidencias Recientes</h5>
        <a href="{{ route('sci-evidencias') }}" class="btn btn-sm btn-label-primary">
          Ver todas <i class="ti tabler-arrow-right ms-1 icon-14px"></i>
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>N° SGD</th>
                <th>Título</th>
                <th>Componente</th>
                <th class="text-center">Estado</th>
                <th>Subido por</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody>
              @forelse($evidencias_recientes as $ev)
              @php
                $ec = match($ev->estado) { 'validada'=>'success', 'rechazada'=>'danger', default=>'warning' };
                $el = match($ev->estado) { 'validada'=>'Validada', 'rechazada'=>'Rechazada', default=>'Pendiente' };
              @endphp
              <tr>
                <td><span class="badge bg-label-primary font-monospace">{{ $ev->numero_sgd ?? '—' }}</span></td>
                <td>
                  <div class="fw-medium" style="max-width:180px" class="text-truncate">{{ $ev->titulo }}</div>
                  @if($ev->descripcion)<small class="text-muted text-truncate d-block" style="max-width:180px">{{ Str::limit($ev->descripcion, 50) }}</small>@endif
                </td>
                <td>
                  @if($ev->actividad?->componente)
                    <span class="badge bg-label-secondary" style="font-size:10px">Comp. {{ $ev->actividad->componente->numero }}</span>
                    <div class="small text-muted text-truncate" style="max-width:140px">{{ $ev->actividad->componente->nombre }}</div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-center">
                  <span class="badge bg-label-{{ $ec }}">{{ $el }}</span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs">
                      <span class="avatar-initial rounded-circle bg-label-secondary" style="font-size:10px">
                        {{ strtoupper(substr($ev->subidoPor->name ?? 'U', 0, 2)) }}
                      </span>
                    </div>
                    <small class="text-muted text-truncate" style="max-width:100px">{{ $ev->subidoPor->name ?? '—' }}</small>
                  </div>
                </td>
                <td><small class="text-muted">{{ $ev->created_at->format('d/m/Y') }}</small></td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-5">
                  <i class="ti tabler-files icon-32px d-block mb-2 text-muted"></i>
                  Sin evidencias registradas aún
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>{{-- /col-xl-8 --}}

  {{-- ── Barra lateral (4/12) ── --}}
  <div class="col-xl-4">

    {{-- Alertas Activas --}}
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="ti tabler-bell-ringing me-2 text-danger"></i>Alertas Activas</h5>
        <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-danger">
          Ver todas
        </a>
      </div>
      <div class="card-body p-0">
        @forelse($alertas_activas as $al)
        @php $ic = match($al->prioridad) { 'alta'=>'danger', 'media'=>'warning', default=>'info' }; @endphp
        <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
          <div class="avatar avatar-xs flex-shrink-0 mt-1">
            <span class="avatar-initial rounded-circle bg-label-{{ $ic }}">
              <i class="ti tabler-{{ $al->prioridad === 'alta' ? 'alert-octagon' : 'alert-circle' }} icon-14px"></i>
            </span>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <p class="mb-0 small fw-medium text-truncate">{{ $al->titulo }}</p>
            @if($al->actividad?->componente)
            <small class="text-muted">{{ $al->actividad->componente->nombre }}</small>
            @endif
            <div class="d-flex align-items-center gap-2 mt-1">
              <span class="badge bg-label-{{ $ic }}" style="font-size:10px">{{ ucfirst($al->prioridad) }}</span>
              <small class="text-muted">{{ $al->created_at->diffForHumans() }}</small>
            </div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-5 px-4">
          <i class="ti tabler-bell-off icon-32px d-block mb-2 text-success"></i>
          <small>Sin alertas activas</small>
        </div>
        @endforelse
        <div class="px-4 py-3 border-top bg-body-secondary" style="border-radius:0 0 var(--bs-card-border-radius) var(--bs-card-border-radius)">
          <small class="text-muted d-flex align-items-start gap-2">
            <i class="ti tabler-info-circle text-info flex-shrink-0 mt-px"></i>
            Las alertas se envían automáticamente al correo del responsable asignado.
          </small>
        </div>
      </div>
    </div>

    {{-- Próximas Acciones --}}
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="ti tabler-clock-exclamation me-2 text-warning"></i>Próximas Acciones</h5>
      </div>
      <ul class="timeline card-body py-3 mb-0">
        @forelse($proximas_acciones as $act)
        @php
          $dias  = now()->diffInDays($act->fecha_limite, false);
          $tc    = $dias <= 3 ? 'danger' : ($dias <= 7 ? 'warning' : 'primary');
        @endphp
        <li class="timeline-item timeline-item-transparent pb-3">
          <span class="timeline-point timeline-point-{{ $tc }}"></span>
          <div class="timeline-event ps-3">
            <div class="d-flex justify-content-between mb-1">
              <p class="mb-0 fw-medium small text-truncate" style="max-width:150px">{{ $act->nombre }}</p>
              <span class="badge bg-label-{{ $tc }} flex-shrink-0 ms-1" style="font-size:10px">
                @if($dias <= 0) Vencida
                @elseif($dias == 1) Mañana
                @else {{ $act->fecha_limite->format('d/m') }}
                @endif
              </span>
            </div>
            <small class="text-muted">{{ $act->componente->nombre ?? '—' }}</small>
          </div>
        </li>
        @empty
        <li class="text-center text-muted py-3">
          <i class="ti tabler-circle-check icon-24px text-success d-block mb-1"></i>
          <small>Sin acciones pendientes próximas</small>
        </li>
        @endforelse
      </ul>
    </div>

  </div>{{-- /col-xl-4 --}}

</div>{{-- /row --}}
</div>{{-- /tab-pane vista-general --}}

{{-- Tab Detalle por Componente --}}
<div class="tab-pane fade" id="tab-detalle-componente">
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr style="background:var(--bs-tertiary-bg)">
              <th class="ps-4 fw-semibold" style="font-size:11px">#</th>
              <th class="fw-semibold" style="font-size:11px">COMPONENTE</th>
              <th class="fw-semibold" style="font-size:11px;min-width:150px">AVANCE</th>
              <th class="text-center fw-semibold" style="font-size:11px">COMPLETADAS</th>
              <th class="text-center fw-semibold" style="font-size:11px">EN PROCESO</th>
              <th class="text-center fw-semibold" style="font-size:11px">EVIDENCIAS</th>
              <th class="text-center fw-semibold pe-4" style="font-size:11px">NIVEL</th>
            </tr>
          </thead>
          <tbody>
            @forelse($componentes as $c)
            @php $nivelLabel = match($c->color) { 'success'=>'Cumplido', 'warning'=>'En proceso', default=>'En riesgo' }; @endphp
            <tr style="border-left:3px solid var(--bs-{{ $c->color }})">
              <td class="ps-4"><span class="fw-bold text-muted">{{ $c->numero }}</span></td>
              <td>
                <div class="fw-semibold" style="font-size:13px">{{ $c->nombre }}</div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:6px">
                    <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
                  </div>
                  <span class="fw-bold text-{{ $c->color }}" style="min-width:32px;font-size:12px">{{ $c->porcentaje }}%</span>
                </div>
              </td>
              <td class="text-center"><span class="fw-bold text-success">{{ $c->completadas_count }}</span></td>
              <td class="text-center"><span class="fw-bold text-warning">{{ $c->en_proceso_count }}</span></td>
              <td class="text-center"><span class="fw-bold text-info">{{ $c->evidencias_count }}</span></td>
              <td class="text-center pe-4"><span class="badge bg-label-{{ $c->color }} rounded-pill">{{ $nivelLabel }}</span></td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-6">Sin componentes</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</div>{{-- /tab-content --}}

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark   = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const avance   = {{ round($avance_global) }};
  const umbralV  = {{ $umbral_verde }};
  const umbralA  = {{ $umbral_amarillo }};
  const color    = avance >= umbralV ? '#28c76f' : (avance >= umbralA ? '#ff9f43' : '#ea5455');

  new ApexCharts(document.getElementById('gaugeModelo'), {
    chart: { type: 'radialBar', height: 180, sparkline: { enabled: true } },
    series: [avance],
    plotOptions: {
      radialBar: {
        startAngle: -135, endAngle: 135,
        hollow: { size: '58%' },
        track: { background: isDark ? '#3d3d3d' : '#e8e8e8', strokeWidth: '97%' },
        dataLabels: {
          name: { show: false },
          value: {
            fontSize: '26px', fontWeight: 700, color: color,
            offsetY: 8,
            formatter: v => v + '%',
          },
        },
      }
    },
    fill: { colors: [color] },
    stroke: { lineCap: 'round' },
  }).render();
});
</script>
@endsection
