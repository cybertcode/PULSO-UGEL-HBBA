@php
$configData = Helper::appClasses();
$componentes = [
  ['num' => 1, 'nombre' => 'Compromiso e Integridad', 'icon' => 'tabler-shield-check', 'pct' => 80],
  ['num' => 2, 'nombre' => 'Gestión de Riesgos', 'icon' => 'tabler-chart-pie', 'pct' => 56],
  ['num' => 3, 'nombre' => 'Actividades de Control', 'icon' => 'tabler-checklist', 'pct' => 70],
  ['num' => 4, 'nombre' => 'Información y Comunicación', 'icon' => 'tabler-messages', 'pct' => 75],
  ['num' => 5, 'nombre' => 'Supervisión', 'icon' => 'tabler-eye', 'pct' => 80],
  ['num' => 6, 'nombre' => 'Ambiente de Control', 'icon' => 'tabler-building', 'pct' => 43],
  ['num' => 7, 'nombre' => 'Evaluación de Riesgos', 'icon' => 'tabler-alert-circle', 'pct' => 69],
  ['num' => 8, 'nombre' => 'Respuesta al Riesgo', 'icon' => 'tabler-shield-bolt', 'pct' => 54],
  ['num' => 9, 'nombre' => 'Seguimiento', 'icon' => 'tabler-timeline', 'pct' => 82],
];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Semáforo Institucional - PULSO UGEL')

@section('content')

<div class="mb-4">
  <h4 class="mb-1">Semáforo Institucional</h4>
  <p class="mb-0 text-muted">Vista rápida del estado de los 9 componentes del Modelo de Integridad</p>
</div>

<!-- Leyenda -->
<div class="d-flex gap-3 mb-4 flex-wrap">
  <span class="badge bg-success fs-6 px-3 py-2">● Verde ≥ 75%</span>
  <span class="badge bg-warning fs-6 px-3 py-2">● Amarillo 50–74%</span>
  <span class="badge bg-danger fs-6 px-3 py-2">● Rojo &lt; 50%</span>
</div>

<!-- Grid semáforo -->
<div class="row g-3">
  @foreach($componentes as $c)
  @php
  $color = $c['pct'] >= 75 ? 'success' : ($c['pct'] >= 50 ? 'warning' : 'danger');
  $label = $c['pct'] >= 75 ? 'Verde' : ($c['pct'] >= 50 ? 'Amarillo' : 'Rojo');
  @endphp
  <div class="col-6 col-md-4 col-xl-3">
    <div class="card h-100 text-center border-{{ $color }} border-2">
      <div class="card-body py-4">
        <div class="avatar avatar-lg mx-auto mb-3">
          <span class="avatar-initial rounded-circle bg-{{ $color }}">
            <i class="ti {{ $c['icon'] }} icon-28px text-white"></i>
          </span>
        </div>
        <p class="small fw-medium mb-1">Componente {{ $c['num'] }}</p>
        <h6 class="mb-3">{{ $c['nombre'] }}</h6>
        <div class="progress mb-2" style="height:10px">
          <div class="progress-bar bg-{{ $color }}" style="width:{{ $c['pct'] }}%"></div>
        </div>
        <span class="fw-bold text-{{ $color }}">{{ $c['pct'] }}% — {{ $label }}</span>
      </div>
    </div>
  </div>
  @endforeach
</div>

@endsection
