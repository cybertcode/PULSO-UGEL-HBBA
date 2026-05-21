@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Panel Principal - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

{{-- Alertas flash --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4" role="alert">
  <i class="ti tabler-check me-2"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- KPIs --}}
<div class="row g-4 mb-4">
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg"><span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-clipboard-list icon-26px"></i></span></div>
        <div>
          <h3 class="mb-0 text-primary">{{ $stats['total'] }}</h3>
          <small class="text-muted">Actividades Totales</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg"><span class="avatar-initial rounded bg-label-success"><i class="ti tabler-circle-check icon-26px"></i></span></div>
        <div>
          <h3 class="mb-0 text-success">{{ $stats['completadas'] }}</h3>
          <small class="text-muted">Completadas</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg"><span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-loader icon-26px"></i></span></div>
        <div>
          <h3 class="mb-0 text-warning">{{ $stats['en_proceso'] }}</h3>
          <small class="text-muted">En Proceso</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg"><span class="avatar-initial rounded bg-label-danger"><i class="ti tabler-alert-triangle icon-26px"></i></span></div>
        <div>
          <h3 class="mb-0 text-danger">{{ $stats['alertas'] }}</h3>
          <small class="text-muted">Alertas Activas</small>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Avance global + Componentes --}}
<div class="row g-4 mb-4">
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header"><h5 class="mb-0">Avance Global SCI</h5></div>
      <div class="card-body d-flex flex-column align-items-center justify-content-center">
        <div id="chartAvanceGlobal"></div>
        <h2 class="mt-2 mb-0">{{ $stats['avance_global'] }}%</h2>
        <p class="text-muted mb-0">Actividades completadas</p>
      </div>
    </div>
  </div>
  <div class="col-xl-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Semáforo por Componente</h5>
        <a href="{{ route('mon-semaforo') }}" class="btn btn-sm btn-outline-primary">Ver semáforo completo</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr><th>Componente</th><th>Avance</th><th>Estado</th><th></th></tr></thead>
            <tbody>
              @forelse($componentes as $c)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs"><span class="avatar-initial rounded bg-label-{{ $c->semaforo }}"><i class="ti {{ $c->icono }} icon-14px"></i></span></div>
                    <span class="fw-medium">{{ $c->nombre }}</span>
                  </div>
                </td>
                <td style="width:200px">
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:6px">
                      <div class="progress-bar bg-{{ $c->semaforo }}" style="width:{{ $c->porcentaje }}%"></div>
                    </div>
                    <small class="fw-medium text-{{ $c->semaforo }}">{{ $c->porcentaje }}%</small>
                  </div>
                </td>
                <td><span class="badge bg-label-{{ $c->semaforo }}">{{ ucfirst($c->semaforo === 'success' ? 'Verde' : ($c->semaforo === 'warning' ? 'Amarillo' : 'Rojo')) }}</span></td>
                <td><small class="text-muted">{{ $c->completadas_count }}/{{ $c->actividades_count }}</small></td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">Sin datos de componentes</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Actividades próximas + Alertas recientes --}}
<div class="row g-4">
  <div class="col-xl-7">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Actividades Próximas a Vencer</h5>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr><th>Actividad</th><th>Componente</th><th>Vence</th><th>Avance</th></tr></thead>
            <tbody>
              @forelse($actividades_proximas as $a)
              @php
                $dias  = now()->diffInDays($a->fecha_limite, false);
                $color = $dias <= 3 ? 'danger' : ($dias <= 7 ? 'warning' : 'secondary');
              @endphp
              <tr>
                <td>
                  <div class="fw-medium text-truncate" style="max-width:200px">{{ $a->nombre }}</div>
                  @if($a->responsable)<small class="text-muted">{{ $a->responsable->name }}</small>@endif
                </td>
                <td><small>{{ $a->componente->nombre ?? '—' }}</small></td>
                <td>
                  <span class="badge bg-label-{{ $color }}">
                    @if($dias == 0) Hoy
                    @elseif($dias == 1) Mañana
                    @else {{ $a->fecha_limite->format('d/m/Y') }}
                    @endif
                  </span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-1">
                    <div class="progress" style="width:60px;height:6px">
                      <div class="progress-bar" style="width:{{ $a->avance }}%"></div>
                    </div>
                    <small>{{ $a->avance }}%</small>
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">No hay actividades pendientes</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-5">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Alertas Recientes</h5>
        <a href="{{ route('mon-alertas') }}" class="btn btn-sm btn-outline-danger">Ver todas</a>
      </div>
      <div class="list-group list-group-flush">
        @forelse($alertas_recientes as $alerta)
        @php $ic = $alerta->prioridad === 'alta' ? 'danger' : ($alerta->prioridad === 'media' ? 'warning' : 'info'); @endphp
        <div class="list-group-item px-4 py-3">
          <div class="d-flex gap-3 align-items-start">
            <div class="avatar avatar-sm flex-shrink-0">
              <span class="avatar-initial rounded bg-label-{{ $ic }}">
                <i class="ti tabler-alert-{{ $alerta->prioridad === 'alta' ? 'octagon' : 'circle' }} icon-16px"></i>
              </span>
            </div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="fw-medium text-truncate">{{ $alerta->titulo }}</div>
              <small class="text-muted">{{ $alerta->created_at->diffForHumans() }}</small>
            </div>
            <span class="badge bg-label-{{ $ic }}">{{ ucfirst($alerta->prioridad) }}</span>
          </div>
        </div>
        @empty
        <div class="list-group-item text-center text-muted py-4">
          <i class="ti tabler-bell-off icon-24px mb-2 d-block"></i>Sin alertas activas
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const textColor = isDark ? '#cdd9e5' : '#697a8d';

  new ApexCharts(document.getElementById('chartAvanceGlobal'), {
    chart: { type: 'radialBar', height: 160, sparkline: { enabled: true } },
    series: [{{ $stats['avance_global'] }}],
    plotOptions: {
      radialBar: {
        hollow: { size: '60%' },
        dataLabels: { name: { show: false }, value: { show: false } },
        track: { background: isDark ? '#3d3d3d' : '#e8e8e8' }
      }
    },
    colors: ['#696cff'],
  }).render();
});
</script>
@endsection
