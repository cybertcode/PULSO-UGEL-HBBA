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
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Modelo de Integridad</li>
  </ol>
</nav>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1">
      <i class="ti tabler-shield-half me-2 text-primary"></i>Modelo de Integridad
      <span class="badge bg-label-primary ms-2 align-middle" style="font-size:12px">9 Componentes</span>
    </h4>
    <p class="mb-0 text-muted">Sistema de Integridad Institucional — Seguimiento y cumplimiento por componente.</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('sci-evidencias') }}" class="btn btn-label-primary btn-sm">
      <i class="ti tabler-upload me-1"></i>Subir Evidencia
    </a>
    <a href="{{ route('mon-semaforo') }}" class="btn btn-label-secondary btn-sm">
      <i class="ti tabler-traffic-lights me-1"></i>Semáforo
    </a>
  </div>
</div>

{{-- ── Fila principal: resumen + componentes | barra lateral ── --}}
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

    {{-- ── Tarjetas de Componentes ── --}}
    <h5 class="mb-3"><i class="ti tabler-components me-2"></i>Componentes del Modelo</h5>
    <div class="row g-3 mb-4">
      @forelse($componentes as $c)
      <div class="col-12 col-md-6">
        <div class="card h-100 border-{{ $c->color }} border-opacity-25">
          <div class="card-body pb-2">

            {{-- Header tarjeta --}}
            <div class="d-flex align-items-start justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <span class="avatar-initial rounded bg-label-{{ $c->color }}">
                    <i class="ti {{ $c->icono ?? 'tabler-point' }} icon-18px"></i>
                  </span>
                </div>
                <div>
                  <span class="badge bg-label-secondary" style="font-size:10px">Comp. {{ $c->numero }}</span>
                </div>
              </div>
              <span class="badge bg-label-{{ $c->color }}">{{ $c->nivel }}</span>
            </div>

            <h6 class="mb-2 fw-semibold" style="font-size:13px;line-height:1.3">{{ $c->nombre }}</h6>

            {{-- Progreso --}}
            <div class="d-flex justify-content-between mb-1">
              <small class="text-muted">{{ $c->completadas_count }}/{{ $c->actividades_count }} actividades</small>
              <small class="fw-bold text-{{ $c->color }}">{{ $c->porcentaje }}%</small>
            </div>
            <div class="progress mb-3" style="height:7px">
              <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
            </div>

            {{-- Mini stats --}}
            <div class="row g-2 mb-2">
              <div class="col-4 text-center">
                <div class="rounded bg-label-success py-1 px-2">
                  <div class="fw-bold text-success small">{{ $c->completadas_count }}</div>
                  <div style="font-size:10px" class="text-muted">Completadas</div>
                </div>
              </div>
              <div class="col-4 text-center">
                <div class="rounded bg-label-warning py-1 px-2">
                  <div class="fw-bold text-warning small">{{ $c->en_proceso_count }}</div>
                  <div style="font-size:10px" class="text-muted">En proceso</div>
                </div>
              </div>
              <div class="col-4 text-center">
                <div class="rounded bg-label-info py-1 px-2">
                  <div class="fw-bold text-info small">{{ $c->evidencias_count }}</div>
                  <div style="font-size:10px" class="text-muted">Evidencias</div>
                </div>
              </div>
            </div>

          </div>
          {{-- Footer con acciones --}}
          <div class="card-footer py-2 px-3 d-flex gap-1">
            <a href="{{ route('sci-evidencias') }}?componente_id={{ $c->id }}"
               class="btn btn-xs btn-label-{{ $c->color }} flex-fill text-center py-1">
              <i class="ti tabler-upload icon-14px me-1"></i>Subir N° SGD
            </a>
            <a href="{{ route('sci-control-interno') }}?componente_id={{ $c->id }}"
               class="btn btn-xs btn-label-secondary flex-fill text-center py-1">
              <i class="ti tabler-list-details icon-14px me-1"></i>Actividades
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
