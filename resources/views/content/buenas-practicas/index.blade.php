@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Buenas Prácticas — PULSO UGEL')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <h4 class="mb-1 fw-bold">
      <i class="ti tabler-rosette-discount-check me-2 text-success"></i>Buenas Prácticas
    </h4>
    <p class="text-muted mb-0">Registro y seguimiento de buenas prácticas institucionales.</p>
  </div>
  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaPractica">
    <i class="ti tabler-plus me-1"></i> Nueva Práctica
  </button>
</div>

{{-- Estadísticas rápidas --}}
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md bg-label-success rounded">
          <i class="ti tabler-rosette-discount-check fs-4"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Registradas</p>
          <h5 class="mb-0 fw-bold">16</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md bg-label-primary rounded">
          <i class="ti tabler-loader fs-4"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">En implementación</p>
          <h5 class="mb-0 fw-bold">5</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md bg-label-warning rounded">
          <i class="ti tabler-clock fs-4"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Pendientes</p>
          <h5 class="mb-0 fw-bold">3</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md bg-label-info rounded">
          <i class="ti tabler-building-community fs-4"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Unidades</p>
          <h5 class="mb-0 fw-bold">8</h5>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Tabla de buenas prácticas --}}
<div class="card border-0 shadow-sm">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="mb-0 fw-semibold">Listado de Buenas Prácticas</h6>
    <div class="d-flex gap-2">
      <input type="text" class="form-control form-control-sm" placeholder="Buscar..." style="width:200px">
      <select class="form-select form-select-sm" style="width:150px">
        <option value="">Todos los estados</option>
        <option>En implementación</option>
        <option>Completada</option>
        <option>Pendiente</option>
      </select>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Práctica</th>
            <th>Unidad</th>
            <th>Categoría</th>
            <th>Estado</th>
            <th>Avance</th>
            <th>Responsable</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="ti tabler-inbox fs-2 d-block mb-2"></i>
              No hay buenas prácticas registradas aún.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
