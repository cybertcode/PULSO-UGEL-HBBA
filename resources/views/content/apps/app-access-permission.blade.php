@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Permisos - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite([
  'resources/assets/js/app-access-permission.js',
  'resources/assets/js/modal-add-permission.js',
  'resources/assets/js/modal-edit-permission.js'
])
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Permisos del Sistema</h4>
    <p class="mb-0 text-muted">Lista de permisos asignados a cada rol de PULSO UGEL</p>
  </div>
</div>

<!-- Tabla de Permisos -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-permissions table border-top">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th>Permiso</th>
          <th>Asignado a Roles</th>
          <th>Fecha de Creación</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Modales -->
@include('_partials/_modals/modal-add-permission')
@include('_partials/_modals/modal-edit-permission')

@endsection
