@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Alertas - PULSO UGEL')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Alertas del Sistema</h4>
    <p class="mb-0 text-muted">Notificaciones automáticas por actividades vencidas o en riesgo</p>
  </div>
</div>

<!-- Resumen de alertas -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card border-danger border-opacity-50">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar">
          <span class="avatar-initial rounded bg-label-danger">
            <i class="ti tabler-alert-triangle icon-26px"></i>
          </span>
        </div>
        <div>
          <h4 class="mb-0 text-danger">5</h4>
          <small class="text-muted">Alta prioridad</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-warning border-opacity-50">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar">
          <span class="avatar-initial rounded bg-label-warning">
            <i class="ti tabler-alert-circle icon-26px"></i>
          </span>
        </div>
        <div>
          <h4 class="mb-0 text-warning">4</h4>
          <small class="text-muted">Media prioridad</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-info border-opacity-50">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar">
          <span class="avatar-initial rounded bg-label-info">
            <i class="ti tabler-info-circle icon-26px"></i>
          </span>
        </div>
        <div>
          <h4 class="mb-0 text-info">3</h4>
          <small class="text-muted">Baja prioridad</small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Lista de alertas -->
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Alertas Activas</h5>
  </div>
  <div class="card-body p-0">
    <ul class="list-group list-group-flush">

      <li class="list-group-item list-group-item-action px-4 py-3">
        <div class="d-flex align-items-start gap-3">
          <div class="avatar avatar-sm flex-shrink-0 mt-1">
            <span class="avatar-initial rounded-circle bg-label-danger">
              <i class="ti tabler-alert-triangle icon-16px"></i>
            </span>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <h6 class="mb-1">Actividad vencida: Implementar mecanismos de supervisión</h6>
              <span class="badge bg-danger">Alta</span>
            </div>
            <small class="text-muted">Componente: Supervisión · Venció el 10/04/2026 · Responsable: Dirección</small>
          </div>
        </div>
      </li>

      <li class="list-group-item list-group-item-action px-4 py-3">
        <div class="d-flex align-items-start gap-3">
          <div class="avatar avatar-sm flex-shrink-0 mt-1">
            <span class="avatar-initial rounded-circle bg-label-warning">
              <i class="ti tabler-clock icon-16px"></i>
            </span>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <h6 class="mb-1">Actividad próxima a vencer: Matriz de riesgos institucional</h6>
              <span class="badge bg-warning">Media</span>
            </div>
            <small class="text-muted">Componente: Gestión de Riesgos · Vence en 5 días · Avance: 75%</small>
          </div>
        </div>
      </li>

      <li class="list-group-item list-group-item-action px-4 py-3">
        <div class="d-flex align-items-start gap-3">
          <div class="avatar avatar-sm flex-shrink-0 mt-1">
            <span class="avatar-initial rounded-circle bg-label-info">
              <i class="ti tabler-info-circle icon-16px"></i>
            </span>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <h6 class="mb-1">Sin evidencia adjunta: Evaluación de riesgos operativos</h6>
              <span class="badge bg-info">Baja</span>
            </div>
            <small class="text-muted">Componente: Evaluación de Riesgos · Actividad completada sin documento de respaldo</small>
          </div>
        </div>
      </li>

    </ul>
  </div>
</div>

@endsection
