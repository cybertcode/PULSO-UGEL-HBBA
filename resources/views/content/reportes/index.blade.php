@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Reportes - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

<div class="mb-4">
  <h4 class="mb-1">Reportes</h4>
  <p class="mb-0 text-muted">Análisis y estadísticas del Sistema de Control Interno</p>
</div>

<!-- Filtros de reporte -->
<div class="card mb-4">
  <div class="card-body py-3">
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label form-label-sm">Período</label>
        <select class="form-select form-select-sm">
          <option>2026 - Anual</option>
          <option>2026 - Semestre I</option>
          <option>2025 - Anual</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label form-label-sm">Componente</label>
        <select class="form-select form-select-sm">
          <option value="">Todos</option>
          <option>Compromiso e Integridad</option>
          <option>Gestión de Riesgos</option>
          <option>Actividades de Control</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label form-label-sm">Estado</label>
        <select class="form-select form-select-sm">
          <option value="">Todos</option>
          <option>Completadas</option>
          <option>En Proceso</option>
          <option>Vencidas</option>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-sm btn-primary flex-grow-1">Generar</button>
        <button class="btn btn-sm btn-label-secondary">
          <i class="ti tabler-download"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Gráficos de reporte -->
<div class="row g-4 mb-4">
  <div class="col-12 col-xl-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Avance por Componente</h5>
      </div>
      <div class="card-body">
        <div id="reporteBarChart"></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Tendencia Mensual</h5>
      </div>
      <div class="card-body">
        <div id="reporteLineChart"></div>
      </div>
    </div>
  </div>
</div>

<!-- Tabla resumen -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Resumen por Componente — 2026</h5>
    <button class="btn btn-sm btn-label-primary">
      <i class="ti tabler-file-export me-1"></i> Exportar PDF
    </button>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Componente</th>
            <th class="text-center">Total</th>
            <th class="text-center">Completadas</th>
            <th class="text-center">En Proceso</th>
            <th class="text-center">Vencidas</th>
            <th class="text-center">% Avance</th>
            <th class="text-center">Semáforo</th>
          </tr>
        </thead>
        <tbody>
          @php
          $rows = [
            ['Compromiso e Integridad', 15, 12, 2, 1, 80, 'success', 'Verde'],
            ['Gestión de Riesgos', 18, 10, 6, 2, 56, 'warning', 'Amarillo'],
            ['Actividades de Control', 20, 14, 4, 2, 70, 'warning', 'Amarillo'],
            ['Información y Comunicación', 12, 9, 2, 1, 75, 'success', 'Verde'],
            ['Supervisión', 10, 8, 1, 1, 80, 'success', 'Verde'],
          ];
          @endphp
          @foreach($rows as [$nombre, $total, $comp, $proc, $venc, $pct, $color, $label])
          <tr>
            <td>{{ $nombre }}</td>
            <td class="text-center">{{ $total }}</td>
            <td class="text-center text-success">{{ $comp }}</td>
            <td class="text-center text-warning">{{ $proc }}</td>
            <td class="text-center text-danger">{{ $venc }}</td>
            <td class="text-center fw-medium">{{ $pct }}%</td>
            <td class="text-center"><span class="badge bg-{{ $color }}">{{ $label }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
