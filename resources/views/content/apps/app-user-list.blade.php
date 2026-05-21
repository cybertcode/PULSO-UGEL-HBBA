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
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('content')

{{-- KPI Cards --}}
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Usuarios</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['total'] }}</h4>
            </div>
            <small class="mb-0">Registrados en el sistema</small>
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
              <h4 class="mb-0 me-2">{{ $stats['admins'] }}</h4>
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
              <h4 class="mb-0 me-2">{{ $stats['responsables'] }}</h4>
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
              <h4 class="mb-0 me-2">{{ $stats['pendientes'] }}</h4>
            </div>
            <small class="mb-0">Sin verificar o inactivos</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="icon-base ti tabler-user-search icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Tabla de Usuarios --}}
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Filtros</h5>
  </div>
  <div class="card-body border-bottom py-4">
    <div class="row g-3">
      <div class="col-md-3">
        <select id="filtroRol" class="form-select text-capitalize">
          <option value="">Todos los roles</option>
          @foreach($roles as $rol)
          <option value="{{ $rol->name }}">{{ $rol->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select id="filtroUnidad" class="form-select text-capitalize">
          <option value="">Todas las unidades</option>
          @foreach($unidades as $u)
          <option value="{{ $u->sigla }}">{{ $u->sigla }} - {{ $u->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select id="filtroEstado" class="form-select text-capitalize">
          <option value="">Todos los estados</option>
          <option value="Activo">Activo</option>
          <option value="Inactivo">Inactivo</option>
          <option value="Pendiente">Pendiente</option>
        </select>
      </div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-usuarios table border-top">
      <thead>
        <tr>
          <th></th>
          <th>Usuario</th>
          <th>DNI</th>
          <th>Cargo</th>
          <th>Unidad</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($usuarios as $u)
        <tr>
          <td></td>
          <td>
            <div class="d-flex justify-content-start align-items-center user-name">
              <div class="avatar-wrapper">
                <div class="avatar avatar-sm me-4">
                  @php
                    $colors = ['success','danger','warning','info','primary','secondary'];
                    $color  = $colors[crc32($u->name) % count($colors)];
                    $initials = collect(explode(' ', $u->name))->map(fn($w)=>strtoupper($w[0]))->take(2)->join('');
                  @endphp
                  <span class="avatar-initial rounded-circle bg-label-{{ $color }}">{{ $initials }}</span>
                </div>
              </div>
              <div class="d-flex flex-column">
                <span class="text-heading fw-medium text-truncate">{{ $u->name }}</span>
                <small class="text-muted">{{ $u->email }}</small>
              </div>
            </div>
          </td>
          <td>{{ $u->dni ?? '—' }}</td>
          <td>{{ $u->cargo ?? '—' }}</td>
          <td>{{ $u->unidadOrganica?->sigla ?? '—' }}</td>
          <td>{{ $u->roles->first()?->name ?? '—' }}</td>
          <td>
            @php
              $estadoMap = ['activo'=>'success','inactivo'=>'danger','pendiente'=>'warning'];
              $ec = $estadoMap[$u->estado] ?? 'secondary';
            @endphp
            <span class="badge bg-label-{{ $ec }}">{{ ucfirst($u->estado) }}</span>
          </td>
          <td>
            <div class="d-flex align-items-center">
              @can('usuarios.editar')
              <a href="javascript:;" class="btn btn-icon btn-text-secondary rounded-pill waves-effect btn-editar"
                data-id="{{ $u->id }}"
                data-name="{{ addslashes($u->name) }}"
                data-email="{{ $u->email }}"
                data-dni="{{ $u->dni }}"
                data-cargo="{{ addslashes($u->cargo) }}"
                data-unidad="{{ $u->unidad_organica_id }}"
                data-rol="{{ $u->roles->first()?->name }}"
                data-estado="{{ $u->estado }}"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasEditUser"
                title="Editar">
                <i class="icon-base ti tabler-edit icon-22px"></i>
              </a>
              @endcan
              @can('usuarios.editar')
              <form method="POST" action="{{ route('adm-usuarios.estado', $u) }}" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                  title="{{ $u->estado === 'activo' ? 'Desactivar' : 'Activar' }}"
                  onclick="return confirm('¿Cambiar estado?')">
                  <i class="icon-base ti {{ $u->estado === 'activo' ? 'tabler-player-pause' : 'tabler-player-play' }} icon-22px"></i>
                </button>
              </form>
              @endcan
              @can('usuarios.eliminar')
              @if($u->id !== auth()->id())
              <form method="POST" action="{{ route('adm-usuarios.destroy', $u) }}" class="d-inline form-eliminar">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-icon btn-text-secondary rounded-pill waves-effect btn-eliminar"
                  data-nombre="{{ addslashes($u->name) }}" title="Eliminar">
                  <i class="icon-base ti tabler-trash icon-22px"></i>
                </button>
              </form>
              @endif
              @endcan
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Offcanvas Crear Usuario --}}
  @can('usuarios.crear')
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Nuevo Usuario</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form method="POST" action="{{ route('adm-usuarios.store') }}">
        @csrf
        <div class="mb-6">
          <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" required placeholder="Ej: María García López">
        </div>
        <div class="mb-6">
          <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" required placeholder="usuario@ugel.gob.pe">
        </div>
        <div class="mb-6">
          <label class="form-label">Contraseña <span class="text-danger">*</span></label>
          <input type="password" name="password" class="form-control" required placeholder="Mín. 8 car. con mayúsculas y números">
        </div>
        <div class="mb-6">
          <label class="form-label">DNI</label>
          <input type="text" name="dni" class="form-control" maxlength="8" placeholder="12345678">
        </div>
        <div class="mb-6">
          <label class="form-label">Cargo</label>
          <input type="text" name="cargo" class="form-control" placeholder="Ej: Especialista Administrativo">
        </div>
        <div class="mb-6">
          <label class="form-label">Unidad Orgánica</label>
          <select name="unidad_organica_id" class="form-select">
            <option value="">Sin asignar</option>
            @foreach($unidades as $un)
            <option value="{{ $un->id }}">{{ $un->sigla }} - {{ $un->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label">Rol <span class="text-danger">*</span></label>
          <select name="rol" class="form-select" required>
            <option value="">Seleccionar rol...</option>
            @foreach($roles as $rol)
            <option value="{{ $rol->name }}">{{ $rol->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label">Estado <span class="text-danger">*</span></label>
          <select name="estado" class="form-select" required>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="pendiente">Pendiente</option>
          </select>
        </div>
        <div class="d-flex gap-4">
          <button type="submit" class="btn btn-primary waves-effect waves-light">Guardar</button>
          <button type="button" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  @endcan

  {{-- Offcanvas Editar Usuario --}}
  @can('usuarios.editar')
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasEditUserLabel" class="offcanvas-title">Editar Usuario</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form method="POST" id="formEditUser" action="">
        @csrf @method('PUT')
        <div class="mb-6">
          <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
          <input type="text" name="name" id="edit-name" class="form-control" required>
        </div>
        <div class="mb-6">
          <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
          <input type="email" name="email" id="edit-email" class="form-control" required>
        </div>
        <div class="mb-6">
          <label class="form-label">Nueva contraseña <small class="text-muted">(vacío = sin cambio)</small></label>
          <input type="password" name="password" class="form-control" placeholder="Mín. 8 car. con mayúsculas y números">
        </div>
        <div class="mb-6">
          <label class="form-label">DNI</label>
          <input type="text" name="dni" id="edit-dni" class="form-control" maxlength="8">
        </div>
        <div class="mb-6">
          <label class="form-label">Cargo</label>
          <input type="text" name="cargo" id="edit-cargo" class="form-control">
        </div>
        <div class="mb-6">
          <label class="form-label">Unidad Orgánica</label>
          <select name="unidad_organica_id" id="edit-unidad" class="form-select">
            <option value="">Sin asignar</option>
            @foreach($unidades as $un)
            <option value="{{ $un->id }}">{{ $un->sigla }} - {{ $un->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label">Rol <span class="text-danger">*</span></label>
          <select name="rol" id="edit-rol" class="form-select" required>
            @foreach($roles as $rol)
            <option value="{{ $rol->name }}">{{ $rol->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label">Estado <span class="text-danger">*</span></label>
          <select name="estado" id="edit-estado" class="form-select" required>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="pendiente">Pendiente</option>
          </select>
        </div>
        <div class="d-flex gap-4">
          <button type="submit" class="btn btn-primary waves-effect waves-light">Actualizar</button>
          <button type="button" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  @endcan
</div>

@endsection

@section('page-script')
<script>
'use strict';
document.addEventListener('DOMContentLoaded', function () {
  const table = document.querySelector('.datatables-usuarios');
  if (!table) return;

  const dt = new DataTable(table, {
    responsive: { details: { type: 'column', target: 0 } },
    columnDefs: [
      { className: 'control', orderable: false, searchable: false, targets: 0, render: () => '' },
      { targets: -1, orderable: false, searchable: false },
    ],
    order: [[1, 'asc']],
    layout: {
      topStart: {
        rowClass: 'row m-3 my-0 justify-content-between',
        features: [{ pageLength: { menu: [10, 25, 50], text: '_MENU_' } }]
      },
      topEnd: {
        features: [
          { search: { placeholder: 'Buscar usuario...', text: '_INPUT_' } },
          {
            buttons: [
              @can('usuarios.crear')
              {
                text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span class="d-none d-sm-inline-block">Nuevo Usuario</span>',
                className: 'btn btn-primary waves-effect waves-light',
                attr: { 'data-bs-toggle': 'offcanvas', 'data-bs-target': '#offcanvasAddUser' }
              }
              @endcan
            ]
          }
        ]
      },
      bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
      bottomEnd: 'paging'
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json',
      paginate: {
        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>'
      }
    }
  });

  // Filtros encima de la tabla
  document.getElementById('filtroRol')?.addEventListener('change', function () {
    dt.column(5).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
  });
  document.getElementById('filtroUnidad')?.addEventListener('change', function () {
    dt.column(4).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
  });
  document.getElementById('filtroEstado')?.addEventListener('change', function () {
    dt.column(6).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
  });

  // Cargar datos en offcanvas de edición
  document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', function () {
      const d = this.dataset;
      document.getElementById('formEditUser').action = '/usuarios/' + d.id;
      document.getElementById('edit-name').value   = d.name;
      document.getElementById('edit-email').value  = d.email;
      document.getElementById('edit-dni').value    = d.dni !== 'null' && d.dni ? d.dni : '';
      document.getElementById('edit-cargo').value  = d.cargo !== 'null' && d.cargo ? d.cargo : '';
      const selU = document.getElementById('edit-unidad');
      if (selU) selU.value = d.unidad && d.unidad !== 'null' ? d.unidad : '';
      const selR = document.getElementById('edit-rol');
      if (selR) selR.value = d.rol || '';
      const selE = document.getElementById('edit-estado');
      if (selE) selE.value = d.estado || 'activo';
    });
  });

  // Confirmación de eliminación
  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const nombre = this.dataset.nombre;
      const form   = this.closest('form');
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: '¿Eliminar usuario?',
          text: 'Se eliminará a "' + nombre + '" permanentemente.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6e7881',
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then(r => { if (r.isConfirmed) form.submit(); });
      } else {
        if (confirm('¿Eliminar a ' + nombre + '?')) form.submit();
      }
    });
  });

  // Ajustes visuales DataTables al estilo Vuexy
  setTimeout(() => {
    [
      { sel: '.dt-buttons .btn',       rm: 'btn-secondary' },
      { sel: '.dt-search .form-control', rm: 'form-control-sm' },
      { sel: '.dt-length .form-select',  rm: 'form-select-sm' },
      { sel: '.dt-length',               add: 'mb-md-6 mb-0' },
      { sel: '.dt-layout-end',           rm: 'justify-content-between', add: 'd-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap' },
      { sel: '.dt-layout-table',         rm: 'row mt-2' },
      { sel: '.dt-layout-full',          rm: 'col-md col-12', add: 'table-responsive' },
    ].forEach(({ sel, rm, add }) => {
      document.querySelectorAll(sel).forEach(el => {
        rm  && rm.split(' ').forEach(c => el.classList.remove(c));
        add && add.split(' ').forEach(c => el.classList.add(c));
      });
    });
  }, 100);
});
</script>
@endsection

