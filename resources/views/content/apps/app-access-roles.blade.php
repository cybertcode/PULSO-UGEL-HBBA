@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Roles - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
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
@vite(['resources/assets/js/app-access-roles.js', 'resources/assets/js/modal-add-role.js'])
@endsection

@section('content')
<h4 class="mb-1">Lista de Roles</h4>
<p class="mb-6">
  Un rol otorga acceso a módulos y funciones predefinidas del sistema PULSO UGEL.<br />
  Cada usuario tiene un rol asignado según sus responsabilidades institucionales.
</p>

<!-- Tarjetas de roles -->
<div class="row g-6">

  <!-- Administrador -->
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h6 class="fw-normal mb-0 text-body">2 usuarios</h6>
          <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="María García" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" />
            </li>
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Carlos López" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" />
            </li>
          </ul>
        </div>
        <div class="d-flex justify-content-between align-items-end">
          <div class="role-heading">
            <h5 class="mb-1">Administrador</h5>
            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal" class="role-edit-modal">
              <span>Editar Rol</span>
            </a>
          </div>
          <a href="javascript:void(0);" title="Copiar rol">
            <i class="icon-base ti tabler-copy icon-md text-heading"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Responsable de Unidad -->
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h6 class="fw-normal mb-0 text-body">5 usuarios</h6>
          <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Luis Ramírez" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/3.png') }}" alt="Avatar" />
            </li>
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Ana Torres" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/4.png') }}" alt="Avatar" />
            </li>
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Rosa Méndez" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/5.png') }}" alt="Avatar" />
            </li>
            <li class="avatar">
              <span class="avatar-initial rounded-circle pull-up" data-bs-toggle="tooltip" data-bs-placement="bottom" title="2 más">+2</span>
            </li>
          </ul>
        </div>
        <div class="d-flex justify-content-between align-items-end">
          <div class="role-heading">
            <h5 class="mb-1">Responsable de Unidad</h5>
            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal" class="role-edit-modal">
              <span>Editar Rol</span>
            </a>
          </div>
          <a href="javascript:void(0);" title="Copiar rol">
            <i class="icon-base ti tabler-copy icon-md text-heading"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Operador -->
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h6 class="fw-normal mb-0 text-body">8 usuarios</h6>
          <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="José Pérez" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar" />
            </li>
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Elena Cruz" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/7.png') }}" alt="Avatar" />
            </li>
            <li class="avatar">
              <span class="avatar-initial rounded-circle pull-up" data-bs-toggle="tooltip" data-bs-placement="bottom" title="6 más">+6</span>
            </li>
          </ul>
        </div>
        <div class="d-flex justify-content-between align-items-end">
          <div class="role-heading">
            <h5 class="mb-1">Operador</h5>
            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal" class="role-edit-modal">
              <span>Editar Rol</span>
            </a>
          </div>
          <a href="javascript:void(0);" title="Copiar rol">
            <i class="icon-base ti tabler-copy icon-md text-heading"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Visualizador -->
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h6 class="fw-normal mb-0 text-body">3 usuarios</h6>
          <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Pedro Huanca" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/9.png') }}" alt="Avatar" />
            </li>
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Carmen Flores" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/10.png') }}" alt="Avatar" />
            </li>
            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Ronal Soto" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png') }}" alt="Avatar" />
            </li>
          </ul>
        </div>
        <div class="d-flex justify-content-between align-items-end">
          <div class="role-heading">
            <h5 class="mb-1">Visualizador</h5>
            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal" class="role-edit-modal">
              <span>Editar Rol</span>
            </a>
          </div>
          <a href="javascript:void(0);" title="Copiar rol">
            <i class="icon-base ti tabler-copy icon-md text-heading"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Agregar nuevo rol -->
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card h-100">
      <div class="row h-100">
        <div class="col-sm-5">
          <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4">
            <img src="{{ asset('assets/img/illustrations/add-new-roles.png') }}" class="img-fluid" alt="Nuevo Rol" width="83" />
          </div>
        </div>
        <div class="col-sm-7">
          <div class="card-body text-sm-end text-center ps-sm-0">
            <button data-bs-target="#addRoleModal" data-bs-toggle="modal"
              class="btn btn-sm btn-primary mb-4 text-nowrap add-new-role">
              Agregar Rol
            </button>
            <p class="mb-0">
              Crea un nuevo rol<br />si no existe aún.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Sección tabla -->
  <div class="col-12">
    <h4 class="mt-6 mb-1">Usuarios y sus roles asignados</h4>
    <p class="mb-0">Lista de todos los usuarios del sistema PULSO UGEL con sus roles y estados.</p>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-datatable">
        <table class="datatables-users table border-top">
          <thead>
            <tr>
              <th></th>
              <th></th>
              <th>Usuario</th>
              <th>Rol</th>
              <th>Unidad</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Agregar Rol -->
@include('_partials/_modals/modal-add-role')
@endsection
