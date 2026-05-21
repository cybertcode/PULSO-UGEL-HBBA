@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Usuarios - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'
])
@endsection

@section('page-script')
@vite('resources/assets/js/app-user-list.js')
@endsection

@section('content')

<!-- KPI Cards -->
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Sesión activa</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ \Illuminate\Support\Facades\DB::table('users')->count() }}</h4>
              <p class="text-success mb-0">(total)</p>
            </div>
            <small class="mb-0">Usuarios registrados</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="icon-base ti tabler-users icon-26px"></i>
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
          <div class="content-left">
            <span class="text-heading">Administradores</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">2</h4>
              <p class="text-success mb-0">(activos)</p>
            </div>
            <small class="mb-0">Con acceso total</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="icon-base ti tabler-user-shield icon-26px"></i>
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
          <div class="content-left">
            <span class="text-heading">Responsables</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">8</h4>
              <p class="text-success mb-0">(activos)</p>
            </div>
            <small class="mb-0">Por unidad orgánica</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="icon-base ti tabler-user-check icon-26px"></i>
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
          <div class="content-left">
            <span class="text-heading">Pendientes</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">1</h4>
              <p class="text-warning mb-0">(sin verificar)</p>
            </div>
            <small class="mb-0">Email no confirmado</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="icon-base ti tabler-user-question icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabla de Usuarios -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Filtros</h5>
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
      <div class="col-md-4 user_role"></div>
      <div class="col-md-4 user_plan"></div>
      <div class="col-md-4 user_status"></div>
    </div>
  </div>
  <div class="card-datatable">
    <table class="datatables-users table">
      <thead class="border-top">
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

  <!-- Offcanvas agregar usuario -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Agregar Usuario</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form class="add-new-user pt-0" id="addNewUserForm" onsubmit="return false">
        <div class="mb-6 form-control-validation">
          <label class="form-label" for="add-user-fullname">Nombre completo</label>
          <input type="text" class="form-control" id="add-user-fullname" placeholder="Ej: María García" name="userFullname" />
        </div>
        <div class="mb-6 form-control-validation">
          <label class="form-label" for="add-user-email">Correo electrónico</label>
          <input type="text" id="add-user-email" class="form-control" placeholder="usuario@ugel.gob.pe" name="userEmail" />
        </div>
        <div class="mb-6">
          <label class="form-label" for="add-user-contact">Teléfono</label>
          <input type="text" id="add-user-contact" class="form-control phone-mask" placeholder="+51 999 999 999" name="userContact" />
        </div>
        <div class="mb-6">
          <label class="form-label" for="add-user-company">Unidad Orgánica</label>
          <input type="text" id="add-user-company" class="form-control" placeholder="Ej: Área de Gestión Pedagógica" name="companyName" />
        </div>
        <div class="mb-6">
          <label class="form-label" for="country">Cargo</label>
          <select id="country" class="select2 form-select">
            <option value="">Seleccionar</option>
            <option value="Director">Director(a)</option>
            <option value="Especialista">Especialista</option>
            <option value="Responsable">Responsable de área</option>
            <option value="Tecnico">Técnico administrativo</option>
            <option value="Auxiliar">Auxiliar</option>
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="user-role">Rol en el sistema</label>
          <select id="user-role" class="form-select">
            <option value="admin">Administrador</option>
            <option value="responsable">Responsable de unidad</option>
            <option value="operador">Operador</option>
            <option value="visualizador">Visualizador</option>
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="user-plan">Estado</label>
          <select id="user-plan" class="form-select">
            <option value="active">Activo</option>
            <option value="inactive">Inactivo</option>
            <option value="pending">Pendiente</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary me-3 data-submit">Guardar</button>
        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancelar</button>
      </form>
    </div>
  </div>
</div>

@endsection
