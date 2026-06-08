@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Usuarios y Cargos - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('content')

{{-- KPI Cards — mismo diseño que full-version --}}
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
            <span class="text-heading">Usuarios Activos</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['activos'] }}</h4>
            </div>
            <small class="mb-0">Con acceso al sistema</small>
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
            <span class="text-heading">Inactivos</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['inactivos'] }}</h4>
            </div>
            <small class="mb-0">Sin acceso al sistema</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="icon-base ti tabler-user-off icon-26px"></i>
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
            <small class="mb-0">Por verificar o activar</small>
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
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
      <div class="col-md-4 user_rol"></div>
      <div class="col-md-4 user_unidad"></div>
      <div class="col-md-4 user_estado"></div>
    </div>
  </div>
  <div class="card-datatable">
    <table class="datatables-usuarios table">
      <thead class="border-top">
        <tr>
          <th></th>
          <th></th>
          <th>Usuario</th>
          <th>Rol</th>
          <th>Unidad</th>
          <th>Cargo</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>
  </div>

  {{-- Offcanvas Agregar Usuario --}}
  @can('usuarios.crear')
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Agregar Usuario</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form method="POST" action="{{ route('adm-usuarios.store') }}" id="addNewUserForm">
        @csrf
        <div class="mb-6">
          <label class="form-label" for="add-user-name">Nombre Completo <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="add-user-name" name="name" placeholder="Ej: Juan Pérez" required>
        </div>
        <div class="mb-6">
          <label class="form-label" for="add-user-email">Correo Electrónico <span class="text-danger">*</span></label>
          <input type="email" class="form-control" id="add-user-email" name="email" placeholder="juan@ugel.gob.pe" required>
        </div>
        <div class="mb-6">
          <label class="form-label" for="add-user-password">Contraseña <span class="text-danger">*</span></label>
          <input type="password" class="form-control" id="add-user-password" name="password" placeholder="Mínimo 8 caracteres" required>
        </div>
        <div class="mb-6">
          <label class="form-label" for="add-user-dni">DNI</label>
          <input type="text" class="form-control" id="add-user-dni" name="dni" placeholder="12345678" maxlength="8">
        </div>
        <div class="mb-6">
          <label class="form-label" for="add-user-cargo">Cargo</label>
          <select id="add-user-cargo" name="cargo" class="select2-cargo form-select">
            <option value="">Sin cargo</option>
          </select>
          <div class="form-text">Elige de la lista o escribe uno nuevo.</div>
        </div>
        <div class="mb-6">
          <label class="form-label" for="add-user-unidad">Unidad Orgánica</label>
          <select id="add-user-unidad" name="unidad_organica_id" class="select2 form-select">
            <option value="">Sin asignar</option>
            @foreach($unidades as $u)
            <option value="{{ $u->id }}">{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="add-user-rol">Rol <span class="text-danger">*</span></label>
          <select id="add-user-rol" name="rol" class="form-select" required>
            <option value="">Seleccionar rol</option>
            @foreach($roles as $r)
            <option value="{{ $r->name }}">{{ $r->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="add-user-estado">Estado</label>
          <select id="add-user-estado" name="estado" class="form-select">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="pendiente">Pendiente</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary me-3">Guardar</button>
        <button type="button" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancelar</button>
      </form>
    </div>
  </div>
  @endcan

  {{-- Offcanvas Editar Usuario --}}
  @can('usuarios.editar')
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasEditUserLabel" class="offcanvas-title">Editar Usuario</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form method="POST" id="formEditUser" action="">
        @csrf @method('PUT')
        <div class="mb-6">
          <label class="form-label" for="edit-user-name">Nombre Completo <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="edit-user-name" name="name" required>
        </div>
        <div class="mb-6">
          <label class="form-label" for="edit-user-email">Correo Electrónico <span class="text-danger">*</span></label>
          <input type="email" class="form-control" id="edit-user-email" name="email" required>
        </div>
        <div class="mb-6">
          <label class="form-label" for="edit-user-password">Nueva Contraseña</label>
          <input type="password" class="form-control" id="edit-user-password" name="password" placeholder="Dejar en blanco para no cambiar">
        </div>
        <div class="mb-6">
          <label class="form-label" for="edit-user-dni">DNI</label>
          <input type="text" class="form-control" id="edit-user-dni" name="dni" maxlength="8">
        </div>
        <div class="mb-6">
          <label class="form-label" for="edit-user-cargo">Cargo</label>
          <select id="edit-user-cargo" name="cargo" class="select2-cargo form-select">
            <option value="">Sin cargo</option>
          </select>
          <div class="form-text">Elige de la lista o escribe uno nuevo.</div>
        </div>
        <div class="mb-6">
          <label class="form-label" for="edit-user-unidad">Unidad Orgánica</label>
          <select id="edit-user-unidad" name="unidad_organica_id" class="select2 form-select">
            <option value="">Sin asignar</option>
            @foreach($unidades as $u)
            <option value="{{ $u->id }}">{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="edit-user-rol">Rol <span class="text-danger">*</span></label>
          <select id="edit-user-rol" name="rol" class="form-select" required>
            @foreach($roles as $r)
            <option value="{{ $r->name }}">{{ $r->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-6">
          <label class="form-label" for="edit-user-estado">Estado</label>
          <select id="edit-user-estado" name="estado" class="form-select">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="pendiente">Pendiente</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary me-3">Actualizar</button>
        <button type="button" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancelar</button>
      </form>
    </div>
  </div>
  @endcan
</div>

{{-- ===================== SECCIÓN CARGOS ===================== --}}
<div class="card mt-6">
  <div class="card-header border-bottom d-flex align-items-center justify-content-between">
    <div>
      <h5 class="card-title mb-0"><i class="icon-base ti tabler-briefcase me-2 text-primary"></i>Catálogo de Cargos</h5>
      <small class="text-muted">Cargos disponibles para asignar a los usuarios</small>
    </div>
    @can('usuarios.crear')
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddCargo">
      <i class="icon-base ti tabler-plus me-1"></i>Nuevo Cargo
    </button>
    @endcan
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0" id="tablaCargos">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nombre del Cargo</th>
            <th>Estado</th>
            @canany(['usuarios.editar','usuarios.eliminar'])<th>Acciones</th>@endcanany
          </tr>
        </thead>
        <tbody id="cargosBody">
          <tr><td colspan="4" class="text-center py-4 text-muted">Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal Nuevo Cargo --}}
@can('usuarios.crear')
<div class="modal fade" id="modalAddCargo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Cargo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label" for="nuevoCargo">Nombre <span class="text-danger">*</span></label>
        <input type="text" id="nuevoCargo" class="form-control" placeholder="Ej: Especialista Legal" maxlength="150">
        <div id="errorNuevoCargo" class="text-danger small mt-1 d-none"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarCargo">Guardar</button>
      </div>
    </div>
  </div>
</div>
@endcan

{{-- Modal Editar Cargo --}}
@can('usuarios.editar')
<div class="modal fade" id="modalEditCargo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Cargo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editCargoId">
        <label class="form-label" for="editCargoNombre">Nombre <span class="text-danger">*</span></label>
        <input type="text" id="editCargoNombre" class="form-control" maxlength="150">
        <div id="errorEditCargo" class="text-danger small mt-1 d-none"></div>
        <div class="mt-3">
          <label class="form-label">Estado</label>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="editCargoActivo" checked>
            <label class="form-check-label" for="editCargoActivo">Activo</label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnActualizarCargo">Actualizar</button>
      </div>
    </div>
  </div>
</div>
@endcan

@endsection

@section('page-script')
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function () {

  const dataUrl   = '{{ route("adm-usuarios.data") }}';
  const canEdit   = {{ auth()->user()->can('usuarios.editar') ? 'true' : 'false' }};
  const canDelete = {{ auth()->user()->can('usuarios.eliminar') ? 'true' : 'false' }};
  const canCreate = {{ auth()->user()->can('usuarios.crear') ? 'true' : 'false' }};

  const colorMap = {
    'Super Admin': 'danger', 'Administrador': 'primary',
    'Responsable de Unidad': 'success', 'Operador': 'info', 'Visualizador': 'secondary'
  };

  const dtTable = document.querySelector('.datatables-usuarios');

  const dt = new DataTable(dtTable, {
    ajax: { url: dataUrl, dataSrc: 'data' },
    columns: [
      { data: null },
      { data: null, orderable: false, render: DataTable.render.select() },
      { data: 'name' },
      { data: 'rol' },
      { data: 'unidad' },
      { data: 'cargo' },
      { data: 'estado' },
      { data: null, orderable: false, searchable: false },
    ],
    columnDefs: [
      {
        // Responsive control
        className: 'control', orderable: false, searchable: false,
        responsivePriority: 2, targets: 0,
        render: () => ''
      },
      {
        // Checkbox
        targets: 1, orderable: false, searchable: false, responsivePriority: 4,
        checkboxes: true,
        render: () => '<input type="checkbox" class="dt-checkboxes form-check-input">',
        checkboxes: { selectAllRender: '<input type="checkbox" class="form-check-input">' }
      },
      {
        // Usuario (avatar + nombre + email)
        targets: 2, responsivePriority: 1,
        render: (data, type, row) => {
          const color = colorMap[row.rol] || 'secondary';
          return `
            <div class="d-flex justify-content-start align-items-center user-name">
              <div class="avatar-wrapper">
                <div class="avatar avatar-sm me-4">
                  <span class="avatar-initial rounded-circle bg-label-${color}">${row.initials}</span>
                </div>
              </div>
              <div class="d-flex flex-column">
                <span class="text-heading text-truncate fw-medium">${row.name}</span>
                <small class="text-muted">${row.email}</small>
              </div>
            </div>`;
        }
      },
      {
        // Rol
        targets: 3,
        render: (data, type, row) => {
          const color = colorMap[row.rol] || 'secondary';
          const icons = {
            'Super Admin': 'tabler-shield-lock', 'Administrador': 'tabler-crown',
            'Responsable de Unidad': 'tabler-user-check', 'Operador': 'tabler-user-edit',
            'Visualizador': 'tabler-eye'
          };
          const icon = icons[row.rol] || 'tabler-user';
          return `<span class="d-flex align-items-center gap-1 text-truncate">
            <i class="icon-base ti ${icon} icon-20px text-${color}"></i> ${row.rol}
          </span>`;
        }
      },
      {
        // Estado
        targets: 6,
        render: (data, type, row) => {
          const map = { activo: 'bg-label-success', inactivo: 'bg-label-secondary', pendiente: 'bg-label-warning' };
          const cls = map[row.estado] || 'bg-label-warning';
          const label = { activo: 'Activo', inactivo: 'Inactivo', pendiente: 'Pendiente' }[row.estado] || row.estado;
          return `<span class="badge ${cls}">${label}</span>`;
        }
      },
      {
        // Acciones
        targets: -1, title: 'Acciones',
        render: (data, type, row) => {
          let btns = `<div class="d-flex align-items-center gap-1">`;
          if (canEdit) {
            btns += `<button class="btn btn-icon btn-text-secondary rounded-pill waves-effect btn-edit-user"
              data-id="${row.id}" data-name="${row.name}" data-email="${row.email}"
              data-dni="${row.dni}" data-cargo="${row.cargo}" data-unidad="${row.unidad}"
              data-rol="${row.rol}" data-estado="${row.estado}"
              data-bs-toggle="offcanvas" data-bs-target="#offcanvasEditUser"
              title="Editar"><i class="icon-base ti tabler-edit icon-md"></i></button>`;
          }
          if (canDelete) {
            btns += `<button class="btn btn-icon btn-text-secondary rounded-pill waves-effect btn-delete-user"
              data-id="${row.id}" data-name="${row.name}"
              data-url="/usuarios/${row.id}"
              title="Eliminar"><i class="icon-base ti tabler-trash icon-md"></i></button>`;
          }
          btns += `</div>`;
          return btns;
        }
      }
    ],
    select: { style: 'multi', selector: 'td:nth-child(2)' },
    order: [[2, 'asc']],
    layout: {
      topStart: {
        rowClass: 'row my-md-0 me-3 ms-0 justify-content-between',
        features: [{ pageLength: { menu: [10, 25, 50, 100], text: '_MENU_' } }]
      },
      topEnd: {
        features: [
          { search: { placeholder: 'Buscar usuario', text: '_INPUT_' } },
          {
            buttons: [
              {
                extend: 'collection',
                className: 'btn btn-label-secondary dropdown-toggle me-4',
                text: '<span class="d-flex align-items-center gap-1"><i class="icon-base ti tabler-upload icon-xs"></i><span class="d-none d-sm-inline-block">Exportar</span></span>',
                buttons: [
                  { extend: 'print',  text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-printer me-2"></i>Imprimir</span>', className: 'dropdown-item', exportOptions: { columns: [2,3,4,5,6] } },
                  { extend: 'csv',    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file me-2"></i>CSV</span>', className: 'dropdown-item', exportOptions: { columns: [2,3,4,5,6] } },
                  { extend: 'excel',  text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-export me-2"></i>Excel</span>', className: 'dropdown-item', exportOptions: { columns: [2,3,4,5,6] } },
                  { extend: 'pdf',    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-2"></i>PDF</span>', className: 'dropdown-item', exportOptions: { columns: [2,3,4,5,6] } },
                  { extend: 'copy',   text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-copy me-2"></i>Copiar</span>', className: 'dropdown-item', exportOptions: { columns: [2,3,4,5,6] } },
                ]
              },
              @can('usuarios.crear')
              {
                text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span class="d-none d-sm-inline-block">Agregar Usuario</span>',
                className: 'btn btn-primary rounded-2 waves-effect waves-light',
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
      info:           'Mostrando _START_ al _END_ de _TOTAL_ registros',
      infoEmpty:      'Mostrando 0 al 0 de 0 registros',
      infoFiltered:   '(filtrado de _MAX_ registros en total)',
      lengthMenu:     'Mostrar _MENU_ registros',
      zeroRecords:    'No se encontraron resultados',
      emptyTable:     'No hay datos disponibles',
      search:         'Buscar:',
      paginate: {
        next:     '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
        first:    '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
        last:     '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>',
      }
    },
    responsive: {
      details: {
        display: DataTable.Responsive.display.modal({
          header: row => 'Detalles de ' + row.data().name
        }),
        type: 'column',
        renderer: function (api, rowIdx, columns) {
          const data = columns.map(col => col.title
            ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}"><td>${col.title}:</td><td>${col.data}</td></tr>`
            : ''
          ).join('');
          if (!data) return false;
          const div = document.createElement('div');
          div.classList.add('table-responsive');
          const tbl = document.createElement('table');
          tbl.classList.add('table');
          const tbody = document.createElement('tbody');
          tbody.innerHTML = data;
          tbl.appendChild(tbody);
          div.appendChild(tbl);
          return div;
        }
      }
    }
  });

  // Filtros en el header
  setTimeout(() => {
    // Rol
    const rolDiv = document.querySelector('.user_rol');
    if (rolDiv) {
      rolDiv.innerHTML = `<select class="form-select text-capitalize">
        <option value="">Filtrar por Rol</option>
        @foreach($roles as $r)<option value="{{ $r->name }}">{{ $r->name }}</option>
        @endforeach
      </select>`;
      rolDiv.querySelector('select').addEventListener('change', function () {
        dt.column(3).search(this.value).draw();
      });
    }

    // Unidad
    const unidadDiv = document.querySelector('.user_unidad');
    if (unidadDiv) {
      unidadDiv.innerHTML = `<select class="form-select">
        <option value="">Filtrar por Unidad</option>
        @foreach($unidades as $u)<option value="{{ $u->sigla }}">{{ $u->nombre }}</option>
        @endforeach
      </select>`;
      unidadDiv.querySelector('select').addEventListener('change', function () {
        dt.column(4).search(this.value).draw();
      });
    }

    // Estado
    const estadoDiv = document.querySelector('.user_estado');
    if (estadoDiv) {
      estadoDiv.innerHTML = `<select class="form-select">
        <option value="">Filtrar por Estado</option>
        <option value="Activo">Activo</option>
        <option value="Inactivo">Inactivo</option>
        <option value="Pendiente">Pendiente</option>
      </select>`;
      estadoDiv.querySelector('select').addEventListener('change', function () {
        dt.column(6).search(this.value).draw();
      });
    }

    // Ajustes de clases del layout
    [
      { sel: '.dt-buttons .btn',    rm: 'btn-secondary' },
      { sel: '.dt-search .form-control', rm: 'form-control-sm' },
      { sel: '.dt-length .form-select',  rm: 'form-select-sm' },
      { sel: '.dt-length',               add: 'mb-md-6 mb-0' },
      { sel: '.dt-layout-table',         rm: 'row mt-2' },
      { sel: '.dt-layout-full',          rm: 'col-md col-12', add: 'table-responsive' },
    ].forEach(({ sel, rm, add }) => {
      document.querySelectorAll(sel).forEach(el => {
        rm  && rm.split(' ').forEach(c => el.classList.remove(c));
        add && add.split(' ').forEach(c => el.classList.add(c));
      });
    });
  }, 100);

  // ── Select2 en offcanvas: unidad y rol ──
  document.querySelectorAll('.offcanvas .select2').forEach(el => {
    $(el).select2({ dropdownParent: $(el).closest('.offcanvas') });
  });

  // ── Select2 con tags para cargo (carga dinámica desde /cargos) ──
  function initCargosSelect2(selector, offcanvasEl) {
    $(selector).select2({
      dropdownParent: $(offcanvasEl),
      placeholder: 'Buscar o escribir cargo...',
      allowClear: true,
      tags: true,
      ajax: {
        url: '{{ route("cargos.index") }}',
        dataType: 'json',
        delay: 200,
        data: params => ({ q: params.term }),
        processResults: data => ({
          results: data
            .filter(c => !params || !params.term || c.nombre.toLowerCase().includes((params && params.term || '').toLowerCase()))
            .map(c => ({ id: c.nombre, text: c.nombre }))
        }),
        cache: true,
      },
      createTag: params => {
        const term = $.trim(params.term);
        if (!term) return null;
        return { id: term, text: term, newTag: true };
      },
      templateResult: data => {
        if (data.newTag) return $(`<span><i class="ti tabler-plus me-1 text-primary"></i>${data.text} <em class="text-muted">(nuevo)</em></span>`);
        return data.text;
      },
      minimumInputLength: 0,
    });
    // forzar carga inicial
    $(selector).trigger('change');
  }

  initCargosSelect2('#add-user-cargo',  document.getElementById('offcanvasAddUser'));
  initCargosSelect2('#edit-user-cargo', document.getElementById('offcanvasEditUser'));

  // precargar opciones al abrir el offcanvas de agregar
  document.getElementById('offcanvasAddUser')?.addEventListener('show.bs.offcanvas', function () {
    $.get('{{ route("cargos.index") }}', function(data) {
      const sel = $('#add-user-cargo');
      sel.empty().append('<option value="">Sin cargo</option>');
      data.forEach(c => sel.append(new Option(c.nombre, c.nombre)));
    });
  });

  // ── Poblar offcanvas de edición ──
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-edit-user');
    if (!btn) return;

    const form = document.getElementById('formEditUser');
    form.action = '/usuarios/' + btn.dataset.id;
    document.getElementById('edit-user-name').value  = btn.dataset.name;
    document.getElementById('edit-user-email').value = btn.dataset.email;
    document.getElementById('edit-user-dni').value   = btn.dataset.dni !== '—' ? btn.dataset.dni : '';
    document.getElementById('edit-user-password').value = '';

    // Cargo — cargar opciones y seleccionar/crear el valor actual
    const cargoVal = btn.dataset.cargo !== '—' ? btn.dataset.cargo : '';
    $.get('{{ route("cargos.index") }}', function(data) {
      const sel = $('#edit-user-cargo');
      sel.empty().append('<option value="">Sin cargo</option>');
      data.forEach(c => sel.append(new Option(c.nombre, c.nombre, false, false)));
      if (cargoVal) {
        // si no existe en lista, crearlo como tag
        if (!data.find(c => c.nombre === cargoVal)) {
          sel.append(new Option(cargoVal, cargoVal, true, true));
        } else {
          sel.val(cargoVal);
        }
      }
      sel.trigger('change');
    });

    // Rol
    const rolSel = document.getElementById('edit-user-rol');
    if (rolSel) rolSel.value = btn.dataset.rol;

    // Estado
    const estadoSel = document.getElementById('edit-user-estado');
    if (estadoSel) estadoSel.value = btn.dataset.estado;

    // Unidad — buscar por sigla
    const unidadSel = document.getElementById('edit-user-unidad');
    if (unidadSel) {
      Array.from(unidadSel.options).forEach(opt => {
        if (opt.text.includes(btn.dataset.unidad) || btn.dataset.unidad === '—') {
          unidadSel.value = btn.dataset.unidad !== '—' ? opt.value : '';
        }
      });
      $(unidadSel).trigger('change');
    }
  });

  // Eliminar usuario
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-delete-user');
    if (!btn) return;

    Swal.fire({
      title: '¿Eliminar usuario?',
      text: `Se eliminará a "${btn.dataset.name}" del sistema.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ea5455',
      cancelButtonColor: '#6e7881',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(r => {
      if (r.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = btn.dataset.url;
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
      }
    });
  });

  // ═══════════════════════════════════════════════
  //  GESTIÓN DE CARGOS
  // ═══════════════════════════════════════════════
  const csrfToken = '{{ csrf_token() }}';

  function renderCargos(data) {
    const tbody = document.getElementById('cargosBody');
    if (!data.length) {
      tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">No hay cargos registrados.</td></tr>';
      return;
    }
    tbody.innerHTML = data.map((c, i) => `
      <tr>
        <td>${i + 1}</td>
        <td><strong>${c.nombre}</strong></td>
        <td>
          <span class="badge bg-label-${c.activo ? 'success' : 'secondary'}">
            ${c.activo ? 'Activo' : 'Inactivo'}
          </span>
        </td>
        <td>
          @can('usuarios.editar')
          <button class="btn btn-icon btn-text-secondary rounded-pill btn-editar-cargo"
            data-id="${c.id}" data-nombre="${c.nombre}" data-activo="${c.activo ? 1 : 0}"
            data-bs-toggle="modal" data-bs-target="#modalEditCargo" title="Editar">
            <i class="icon-base ti tabler-edit icon-md"></i>
          </button>
          @endcan
          @can('usuarios.eliminar')
          <button class="btn btn-icon btn-text-danger rounded-pill btn-eliminar-cargo"
            data-id="${c.id}" data-nombre="${c.nombre}" title="Eliminar">
            <i class="icon-base ti tabler-trash icon-md"></i>
          </button>
          @endcan
        </td>
      </tr>`).join('');
  }

  function cargarCargos() {
    fetch('{{ route("cargos.index") }}')
      .then(r => r.json())
      .then(data => renderCargos(data));
  }

  cargarCargos();

  // Crear cargo
  document.getElementById('btnGuardarCargo')?.addEventListener('click', function () {
    const nombre = document.getElementById('nuevoCargo').value.trim();
    const errEl  = document.getElementById('errorNuevoCargo');
    errEl.classList.add('d-none');
    if (!nombre) { errEl.textContent = 'El nombre es requerido.'; errEl.classList.remove('d-none'); return; }

    fetch('{{ route("cargos.store") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({ nombre }),
    }).then(async r => {
      if (!r.ok) {
        const err = await r.json();
        errEl.textContent = err.errors?.nombre?.[0] ?? 'Error al guardar.';
        errEl.classList.remove('d-none');
        return;
      }
      document.getElementById('nuevoCargo').value = '';
      bootstrap.Modal.getInstance(document.getElementById('modalAddCargo')).hide();
      cargarCargos();
    });
  });

  // Abrir modal editar
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-editar-cargo');
    if (!btn) return;
    document.getElementById('editCargoId').value       = btn.dataset.id;
    document.getElementById('editCargoNombre').value   = btn.dataset.nombre;
    document.getElementById('editCargoActivo').checked = btn.dataset.activo === '1';
    document.getElementById('errorEditCargo').classList.add('d-none');
  });

  // Actualizar cargo
  document.getElementById('btnActualizarCargo')?.addEventListener('click', function () {
    const id     = document.getElementById('editCargoId').value;
    const nombre = document.getElementById('editCargoNombre').value.trim();
    const activo = document.getElementById('editCargoActivo').checked;
    const errEl  = document.getElementById('errorEditCargo');
    errEl.classList.add('d-none');
    if (!nombre) { errEl.textContent = 'El nombre es requerido.'; errEl.classList.remove('d-none'); return; }

    fetch(`/cargos/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({ nombre, activo }),
    }).then(async r => {
      if (!r.ok) {
        const err = await r.json();
        errEl.textContent = err.errors?.nombre?.[0] ?? 'Error al actualizar.';
        errEl.classList.remove('d-none');
        return;
      }
      bootstrap.Modal.getInstance(document.getElementById('modalEditCargo')).hide();
      cargarCargos();
    });
  });

  // Eliminar cargo
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-eliminar-cargo');
    if (!btn) return;
    Swal.fire({
      title: '¿Eliminar cargo?',
      text: `Se eliminará "${btn.dataset.nombre}" del catálogo.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ea5455',
      cancelButtonColor: '#6e7881',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      fetch(`/cargos/${btn.dataset.id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken },
      }).then(() => cargarCargos());
    });
  });

});
</script>
@endsection
