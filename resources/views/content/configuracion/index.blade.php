@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Configuración - PULSO UGEL')

@section('content')

<div class="mb-4">
  <h4 class="mb-1">Configuración del Sistema</h4>
  <p class="mb-0 text-muted">Ajustes generales de PULSO UGEL</p>
</div>

<div class="row g-4">
  <!-- Información institucional -->
  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Información Institucional</h5>
      </div>
      <div class="card-body">
        <form>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Nombre de la Institución</label>
              <input type="text" class="form-control" value="UGEL Huacaybamba">
            </div>
            <div class="col-md-6">
              <label class="form-label">RUC</label>
              <input type="text" class="form-control" value="20529812345">
            </div>
            <div class="col-md-6">
              <label class="form-label">Región</label>
              <input type="text" class="form-control" value="Huánuco">
            </div>
            <div class="col-md-6">
              <label class="form-label">Provincia</label>
              <input type="text" class="form-control" value="Huacaybamba">
            </div>
            <div class="col-md-6">
              <label class="form-label">Año Fiscal</label>
              <select class="form-select">
                <option selected>2026</option>
                <option>2025</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Director(a) UGEL</label>
              <input type="text" class="form-control" placeholder="Nombre del Director/a">
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Configuraciones rápidas -->
  <div class="col-12 col-xl-4">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">Notificaciones</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <p class="mb-0 fw-medium">Alertas por email</p>
            <small class="text-muted">Actividades vencidas</small>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" checked>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <p class="mb-0 fw-medium">Recordatorios</p>
            <small class="text-muted">5 días antes del vencimiento</small>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" checked>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p class="mb-0 fw-medium">Reporte semanal</p>
            <small class="text-muted">Resumen automático</small>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox">
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Accesos rápidos</h5>
      </div>
      <div class="card-body p-0">
        <div class="list-group list-group-flush">
          <a href="{{ route('usuarios') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-4">
            <i class="ti tabler-users text-primary"></i> Gestionar Usuarios
          </a>
          <a href="{{ route('roles') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-4">
            <i class="ti tabler-user-check text-info"></i> Gestionar Roles
          </a>
          <a href="{{ route('permisos') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-4">
            <i class="ti tabler-lock text-warning"></i> Gestionar Permisos
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
