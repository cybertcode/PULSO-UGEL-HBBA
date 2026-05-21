@php
$configData = Helper::appClasses();
$componentes = [
  ['num' => 1, 'nombre' => 'Compromiso e Integridad', 'icon' => 'tabler-shield-check', 'total' => 15, 'completadas' => 12],
  ['num' => 2, 'nombre' => 'Gestión de Riesgos', 'icon' => 'tabler-chart-pie', 'total' => 18, 'completadas' => 10],
  ['num' => 3, 'nombre' => 'Actividades de Control', 'icon' => 'tabler-checklist', 'total' => 20, 'completadas' => 14],
  ['num' => 4, 'nombre' => 'Información y Comunicación', 'icon' => 'tabler-messages', 'total' => 12, 'completadas' => 9],
  ['num' => 5, 'nombre' => 'Supervisión', 'icon' => 'tabler-eye', 'total' => 10, 'completadas' => 8],
  ['num' => 6, 'nombre' => 'Ambiente de Control', 'icon' => 'tabler-building', 'total' => 14, 'completadas' => 6],
  ['num' => 7, 'nombre' => 'Evaluación de Riesgos', 'icon' => 'tabler-alert-circle', 'total' => 16, 'completadas' => 11],
  ['num' => 8, 'nombre' => 'Respuesta al Riesgo', 'icon' => 'tabler-shield-bolt', 'total' => 13, 'completadas' => 7],
  ['num' => 9, 'nombre' => 'Seguimiento', 'icon' => 'tabler-timeline', 'total' => 11, 'completadas' => 9],
];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Modelo de Integridad - PULSO UGEL')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Modelo de Integridad</h4>
    <p class="mb-0 text-muted">9 Componentes del Sistema de Integridad Institucional</p>
  </div>
  <a href="{{ route('semaforo') }}" class="btn btn-outline-secondary">
    <i class="ti tabler-traffic-lights me-1"></i> Ver Semáforo
  </a>
</div>

<!-- Tarjetas de los 9 componentes -->
<div class="row g-4">
  @foreach($componentes as $c)
  @php
  $pct = round(($c['completadas'] / $c['total']) * 100);
  $color = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
  $semaforo = $pct >= 75 ? 'Verde' : ($pct >= 50 ? 'Amarillo' : 'Rojo');
  @endphp
  <div class="col-12 col-md-6 col-xl-4">
    <div class="card h-100 border-{{ $color }} border-opacity-50">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            <div class="avatar avatar-sm">
              <span class="avatar-initial rounded bg-label-{{ $color }}">
                <i class="ti {{ $c['icon'] }} icon-18px"></i>
              </span>
            </div>
            <span class="badge bg-label-secondary">Comp. {{ $c['num'] }}</span>
          </div>
          <span class="badge bg-{{ $color }}">{{ $semaforo }}</span>
        </div>
        <h6 class="mb-3">{{ $c['nombre'] }}</h6>
        <div class="d-flex justify-content-between mb-1">
          <small class="text-muted">{{ $c['completadas'] }} de {{ $c['total'] }} actividades</small>
          <small class="fw-medium text-{{ $color }}">{{ $pct }}%</small>
        </div>
        <div class="progress mb-3" style="height:8px">
          <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%"></div>
        </div>
        <a href="javascript:void(0)" class="btn btn-sm btn-label-{{ $color }} w-100">
          Ver actividades
        </a>
      </div>
    </div>
  </div>
  @endforeach
</div>

@endsection
