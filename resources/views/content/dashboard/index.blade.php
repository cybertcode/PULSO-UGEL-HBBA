@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Panel Principal - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/dashboards-analytics.js'])
@endsection

@section('content')

<!-- KPI Cards -->
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="fw-medium d-block mb-1">Actividades Totales</span>
            <h3 class="card-title mb-2">128</h3>
            <small class="text-success fw-medium"><i class="ti tabler-trending-up me-1"></i>+12% este mes</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="ti tabler-clipboard-list icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="fw-medium d-block mb-1">Completadas</span>
            <h3 class="card-title mb-2">84</h3>
            <small class="text-success fw-medium"><i class="ti tabler-trending-up me-1"></i>65.6% avance</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="ti tabler-circle-check icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="fw-medium d-block mb-1">En Proceso</span>
            <h3 class="card-title mb-2">32</h3>
            <small class="text-warning fw-medium"><i class="ti tabler-clock me-1"></i>25% pendientes</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="ti tabler-loader icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="fw-medium d-block mb-1">Alertas Activas</span>
            <h3 class="card-title mb-2">12</h3>
            <small class="text-danger fw-medium"><i class="ti tabler-alert-triangle me-1"></i>Requieren atención</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="ti tabler-bell icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Gráficos -->
<div class="row g-4 mb-4">
  <!-- Avance mensual -->
  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Avance Mensual de Actividades</h5>
        <div class="dropdown">
          <button class="btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" type="button"
            data-bs-toggle="dropdown">
            <i class="ti tabler-dots-vertical icon-20px text-muted"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="javascript:void(0);">Ver detalles</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">Exportar</a></li>
          </ul>
        </div>
      </div>
      <div class="card-body">
        <div id="avanceMensualChart"></div>
      </div>
    </div>
  </div>

  <!-- Distribución por componente -->
  <div class="col-12 col-xl-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title m-0">Estado por Componente</h5>
      </div>
      <div class="card-body">
        <div id="componenteDonutChart"></div>
      </div>
    </div>
  </div>
</div>

<!-- Tabla de componentes del Modelo de Integridad -->
<div class="row g-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0">Modelo de Integridad — Estado de Componentes</h5>
        <a href="{{ route('sci-modelo-integridad') }}" class="btn btn-sm btn-primary">Ver módulo completo</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Componente</th>
                <th>Actividades</th>
                <th>Avance</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              @php
              $componentes = [
                ['num' => 1, 'nombre' => 'Compromiso e Integridad', 'total' => 15, 'completadas' => 12],
                ['num' => 2, 'nombre' => 'Gestión de Riesgos', 'total' => 18, 'completadas' => 10],
                ['num' => 3, 'nombre' => 'Actividades de Control', 'total' => 20, 'completadas' => 14],
                ['num' => 4, 'nombre' => 'Información y Comunicación', 'total' => 12, 'completadas' => 9],
                ['num' => 5, 'nombre' => 'Supervisión', 'total' => 10, 'completadas' => 8],
              ];
              @endphp
              @foreach($componentes as $c)
              @php
              $pct = round(($c['completadas'] / $c['total']) * 100);
              $color = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
              @endphp
              <tr>
                <td>{{ $c['num'] }}</td>
                <td>{{ $c['nombre'] }}</td>
                <td>{{ $c['completadas'] }}/{{ $c['total'] }}</td>
                <td style="min-width:140px">
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:6px">
                      <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <small>{{ $pct }}%</small>
                  </div>
                </td>
                <td>
                  <span class="badge bg-label-{{ $color }}">
                    {{ $pct >= 75 ? 'Verde' : ($pct >= 50 ? 'Amarillo' : 'Rojo') }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
