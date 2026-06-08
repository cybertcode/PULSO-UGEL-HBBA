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
          <th></th>
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
        <div id="errorsAddUser" class="alert alert-danger d-none py-2 small mb-3"></div>
        <button type="button" id="btnGuardarUser" class="btn btn-primary me-3">Guardar</button>
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
        <div id="errorsEditUser" class="alert alert-danger d-none py-2 small mb-3"></div>
        <button type="button" id="btnActualizarUser" class="btn btn-primary me-3">Actualizar</button>
        <button type="button" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancelar</button>
      </form>
    </div>
  </div>
  @endcan
</div>

{{-- ===================== SECCIÓN CARGOS ===================== --}}
<div class="card mt-2">
  <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h5 class="card-title mb-0"><i class="icon-base ti tabler-briefcase me-2 text-primary"></i>Catálogo de Cargos</h5>
      <small class="text-muted">Cargos disponibles para asignar a los usuarios</small>
    </div>
  </div>
  <div class="card-datatable">
    <table class="table" id="dtCargos">
      <thead class="border-top">
        <tr>
          <th>Nombre del Cargo</th>
          <th>Usuarios</th>
          <th>Estado</th>
          <th>Registrado</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>
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
      { data: 'created_ts', visible: false, searchable: false },
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
        // Acciones (columna 8)
        targets: 8, title: 'Acciones', orderable: false, searchable: false,
        render: (data, type, row) => {
          let btns = `<div class="d-flex align-items-center gap-1">`;
          if (canEdit) {
            btns += `<button class="btn btn-icon btn-text-secondary rounded-pill waves-effect btn-edit-user"
              data-id="${row.id}" data-name="${row.name}" data-email="${row.email}"
              data-dni="${row.dni}" data-cargo="${row.cargo}" data-unidad="${row.unidad}"
              data-unidad-id="${row.unidad_id}" data-rol="${row.rol}" data-estado="${row.estado}"
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
    order: [[7, 'desc']],
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
                  {
                    extend: 'print',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-printer me-2"></i>Imprimir</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2,3,4,5,6], stripHtml: true },
                    customize: function(win) {
                      const doc = win.document;
                      const now = new Date().toLocaleDateString('es-PE', { year:'numeric', month:'long', day:'numeric' });
                      const style = doc.createElement('style');
                      style.innerHTML = `
                        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
                        .dt-print-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #696cff; padding-bottom: 12px; }
                        .dt-print-header h2 { margin: 0 0 4px; font-size: 16px; color: #696cff; }
                        .dt-print-header p { margin: 0; font-size: 11px; color: #666; }
                        table { border-collapse: collapse; width: 100%; }
                        thead tr { background: #696cff !important; color: #fff !important; }
                        thead th { padding: 8px 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }
                        tbody tr:nth-child(even) { background: #f5f5ff; }
                        tbody td { padding: 7px 10px; border-bottom: 1px solid #e0e0e0; }
                        .dt-print-footer { margin-top: 16px; font-size: 10px; color: #999; text-align: right; }
                        @media print { thead { display: table-header-group; } }
                      `;
                      doc.head.appendChild(style);
                      const header = doc.createElement('div');
                      header.className = 'dt-print-header';
                      header.innerHTML = `<h2>Unidad de Gestión Educativa Local Huánuco</h2>
                        <p>Reporte de Usuarios del Sistema — PULSO UGEL &nbsp;|&nbsp; ${now}</p>`;
                      doc.body.insertBefore(header, doc.body.firstChild);
                      const footer = doc.createElement('div');
                      footer.className = 'dt-print-footer';
                      footer.innerHTML = `Generado el ${now} &nbsp;·&nbsp; UGEL Huánuco — Sistema de Control Interno`;
                      doc.body.appendChild(footer);
                    }
                  },
                  {
                    extend: 'csv',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file me-2"></i>CSV</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2,3,4,5,6], stripHtml: true },
                    filename: 'usuarios-pulso-ugel',
                    bom: true,
                  },
                  {
                    extend: 'excel',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-export me-2"></i>Excel</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2,3,4,5,6], stripHtml: true },
                    filename: 'usuarios-pulso-ugel',
                    title: 'Usuarios del Sistema — UGEL Huánuco',
                    messageTop: `PULSO UGEL — Sistema de Control Interno | Generado: ${new Date().toLocaleDateString('es-PE')}`,
                    customize: function(xlsx) {
                      const sheet = xlsx.xl.worksheets['sheet1.xml'];
                      // Color azul-violeta en cabecera (fila 4 = primera fila de datos de cabecera tras title+msg)
                      $('row:nth-child(4) c', sheet).attr('s', '2'); // estilo bold+fondo
                      // Ancho de columnas
                      const cols = $('cols', sheet);
                      cols.html('<col min="1" max="1" width="35" customWidth="1"/>' +
                                '<col min="2" max="2" width="18" customWidth="1"/>' +
                                '<col min="3" max="3" width="16" customWidth="1"/>' +
                                '<col min="4" max="4" width="28" customWidth="1"/>' +
                                '<col min="5" max="5" width="14" customWidth="1"/>');
                    }
                  },
                  {
                    extend: 'pdfHtml5',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-2"></i>PDF</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2,3,4,5,6], stripHtml: true },
                    filename: 'usuarios-pulso-ugel',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function(doc) {
                      const now = new Date().toLocaleDateString('es-PE', { year:'numeric', month:'long', day:'numeric' });
                      // Cabecera del documento
                      doc.content.unshift({
                        columns: [
                          {
                            stack: [
                              { text: 'UNIDAD DE GESTIÓN EDUCATIVA LOCAL HUÁNUCO', style: 'instHeader' },
                              { text: 'PULSO UGEL — Sistema de Control Interno', style: 'instSubHeader' },
                              { text: `Reporte de Usuarios del Sistema | ${now}`, style: 'instDate' },
                            ]
                          }
                        ],
                        margin: [0, 0, 0, 12]
                      });
                      // Línea separadora
                      doc.content.splice(1, 0, { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 770, y2: 0, lineWidth: 1.5, lineColor: '#696cff' }], margin: [0, 0, 0, 10] });
                      // Footer
                      doc.footer = function(currentPage, pageCount) {
                        return {
                          columns: [
                            { text: 'UGEL Huánuco — Sistema de Control Interno', fontSize: 8, color: '#888' },
                            { text: `Página ${currentPage} de ${pageCount}`, alignment: 'right', fontSize: 8, color: '#888' }
                          ],
                          margin: [40, 0]
                        };
                      };
                      // Estilo de la tabla
                      doc.styles.tableHeader = { fillColor: '#696cff', color: '#ffffff', bold: true, fontSize: 9, alignment: 'center' };
                      doc.styles.instHeader  = { fontSize: 13, bold: true, color: '#696cff', alignment: 'center' };
                      doc.styles.instSubHeader = { fontSize: 10, color: '#555', alignment: 'center', margin: [0,2,0,0] };
                      doc.styles.instDate    = { fontSize: 9, color: '#888', alignment: 'center', margin: [0,2,0,0] };
                      // Alternar colores de filas
                      doc.content.forEach(function(el) {
                        if (el.table) {
                          el.table.widths = ['*', 'auto', 'auto', '*', 'auto'];
                          el.layout = {
                            fillColor: function(i) { return i % 2 === 0 && i > 0 ? '#f0f0ff' : null; },
                            hLineColor: function() { return '#ddd'; },
                            vLineColor: function() { return '#eee'; },
                          };
                        }
                      });
                    }
                  },
                  {
                    extend: 'copy',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-copy me-2"></i>Copiar</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2,3,4,5,6], stripHtml: true },
                  },
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

  // ── Select2: inicializar en shown.bs.offcanvas (jQuery disponible después de Vite) ──
  function initCargosSelect2(selector, offcanvasEl) {
    if (!window.$ || !$.fn.select2) return;
    if ($(selector).hasClass('select2-hidden-accessible')) return;
    $(selector).select2({
      dropdownParent: $(offcanvasEl),
      placeholder: 'Buscar o escribir cargo...',
      allowClear: true,
      tags: true,
      ajax: {
        url: '{{ route("cargos.index") }}?select=1',
        dataType: 'json',
        delay: 200,
        data: p => ({ q: p.term }),
        processResults: (data, p) => ({
          results: data
            .filter(c => !p.term || c.nombre.toLowerCase().includes(p.term.toLowerCase()))
            .map(c => ({ id: c.nombre, text: c.nombre }))
        }),
        cache: true,
      },
      createTag: p => {
        const term = $.trim(p.term);
        if (!term) return null;
        return { id: term, text: term, newTag: true };
      },
      templateResult: data => {
        if (data.newTag) return $(`<span><i class="ti tabler-plus me-1 text-primary"></i>${data.text} <em class="text-muted">(nuevo)</em></span>`);
        return data.text;
      },
      minimumInputLength: 0,
    });
  }

  // Inicializar Select2 cuando se abre el offcanvas Agregar
  document.getElementById('offcanvasAddUser')?.addEventListener('shown.bs.offcanvas', function () {
    if (!window.$ || !$.fn.select2) return;
    // Unidad
    $('#add-user-unidad').not('.select2-hidden-accessible').select2({ dropdownParent: $(this), width: '100%', placeholder: 'Sin asignar' });
    // Cargo
    initCargosSelect2('#add-user-cargo', this);
    $.get('{{ route("cargos.index") }}?select=1', function(data) {
      const sel = $('#add-user-cargo');
      sel.empty().append('<option value="">Sin cargo</option>');
      data.forEach(c => sel.append(new Option(c.nombre, c.nombre)));
      sel.trigger('change');
    });
  });

  // Inicializar Select2 cuando se abre el offcanvas Editar
  document.getElementById('offcanvasEditUser')?.addEventListener('shown.bs.offcanvas', function () {
    if (!window.$ || !$.fn.select2) return;
    $('#edit-user-unidad').not('.select2-hidden-accessible').select2({ dropdownParent: $(this), width: '100%', placeholder: 'Sin asignar' });
    initCargosSelect2('#edit-user-cargo', this);
    // Aplicar valores pendientes guardados en el dataset del offcanvas
    const unidadId  = this.dataset.pendingUnidad;
    const cargoVal  = this.dataset.pendingCargo;
    if (unidadId)  { $('#edit-user-unidad').val(unidadId).trigger('change'); }
    if (cargoVal && cargoVal !== '—') {
      $.get('{{ route("cargos.index") }}?select=1', function(data) {
        const sel = $('#edit-user-cargo');
        sel.empty().append('<option value="">Sin cargo</option>');
        data.forEach(c => sel.append(new Option(c.nombre, c.nombre, false, false)));
        if (!data.find(c => c.nombre === cargoVal)) {
          sel.append(new Option(cargoVal, cargoVal, true, true));
        } else {
          sel.val(cargoVal);
        }
        sel.trigger('change');
      });
    }
  });

  // ── Poblar offcanvas de edición ──
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-edit-user');
    if (!btn) return;

    const offcanvas = document.getElementById('offcanvasEditUser');

    // Campos de texto — no necesitan jQuery
    document.getElementById('formEditUser').action = '/usuarios/' + btn.dataset.id;
    document.getElementById('edit-user-name').value     = btn.dataset.name;
    document.getElementById('edit-user-email').value    = btn.dataset.email;
    document.getElementById('edit-user-dni').value      = btn.dataset.dni !== '—' ? btn.dataset.dni : '';
    document.getElementById('edit-user-password').value = '';
    document.getElementById('edit-user-rol').value      = btn.dataset.rol;
    document.getElementById('edit-user-estado').value   = btn.dataset.estado;

    // Guardar valores que requieren Select2 en el dataset para aplicarlos en shown.bs.offcanvas
    offcanvas.dataset.pendingCargo  = btn.dataset.cargo    || '';
    offcanvas.dataset.pendingUnidad = btn.dataset.unidadId || '';

    // Establecer valor nativo también (para caso sin Select2)
    const unidadSel = document.getElementById('edit-user-unidad');
    if (unidadSel) unidadSel.value = offcanvas.dataset.pendingUnidad;
  });

  const csrfToken = '{{ csrf_token() }}';

  // ── Helper: mostrar errores de validación ──
  function showErrors(containerEl, errors) {
    let html = '<ul class="mb-0 ps-3">';
    Object.values(errors).forEach(msgs => msgs.forEach(m => { html += `<li>${m}</li>`; }));
    html += '</ul>';
    containerEl.innerHTML = html;
    containerEl.classList.remove('d-none');
  }

  // ── Guardar nuevo usuario (AJAX) ──
  document.getElementById('btnGuardarUser')?.addEventListener('click', function () {
    const form   = document.getElementById('addNewUserForm');
    const errEl  = document.getElementById('errorsAddUser');
    errEl.classList.add('d-none');

    const fd = new FormData(form);
    fetch('{{ route("adm-usuarios.store") }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
      body: fd,
    }).then(async r => {
      const json = await r.json();
      if (!r.ok) { showErrors(errEl, json.errors ?? { _: [json.message ?? 'Error al guardar.'] }); return; }
      bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasAddUser'))?.hide();
      dt.ajax.reload(null, false);
      Swal.fire({ icon: 'success', title: 'Creado', text: json.message, timer: 2000, showConfirmButton: false });
      form.reset();
    }).catch(() => {
      errEl.innerHTML = 'Error de conexión.'; errEl.classList.remove('d-none');
    });
  });

  // ── Actualizar usuario (AJAX) ──
  document.getElementById('btnActualizarUser')?.addEventListener('click', function () {
    const form   = document.getElementById('formEditUser');
    const errEl  = document.getElementById('errorsEditUser');
    errEl.classList.add('d-none');

    const fd = new FormData(form);
    fetch(form.action, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
      body: fd,
    }).then(async r => {
      const json = await r.json();
      if (!r.ok) { showErrors(errEl, json.errors ?? { _: [json.message ?? 'Error al actualizar.'] }); return; }
      bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasEditUser'))?.hide();
      dt.ajax.reload(null, false);
      Swal.fire({ icon: 'success', title: 'Actualizado', text: json.message, timer: 2000, showConfirmButton: false });
    }).catch(() => {
      errEl.innerHTML = 'Error de conexión.'; errEl.classList.remove('d-none');
    });
  });

  // ── Eliminar usuario (AJAX) ──
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
      if (!r.isConfirmed) return;
      fetch(btn.dataset.url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_method=DELETE',
      }).then(async res => {
        const json = await res.json();
        if (!res.ok) { Swal.fire({ icon: 'error', title: 'Error', text: json.message }); return; }
        dt.ajax.reload(null, false);
        Swal.fire({ icon: 'success', title: 'Eliminado', text: json.message, timer: 2000, showConfirmButton: false });
      });
    });
  });

  // ═══════════════════════════════════════════════
  //  DATATABLE CARGOS
  // ═══════════════════════════════════════════════
  const canEditCargo  = {{ auth()->user()->can('usuarios.editar')   ? 'true' : 'false' }};
  const canDeleteCargo= {{ auth()->user()->can('usuarios.eliminar') ? 'true' : 'false' }};
  const canCreateCargo= {{ auth()->user()->can('usuarios.crear')    ? 'true' : 'false' }};

  const dtCargos = new DataTable('#dtCargos', {
    ajax: { url: '{{ route("cargos.index") }}', dataSrc: '' },
    columns: [
      { data: 'nombre' },
      { data: 'numero_usuarios' },
      { data: 'activo' },
      { data: 'created_at' },
      { data: null, orderable: false, searchable: false },
    ],
    columnDefs: [
      {
        targets: 0,
        render: (data) => `<strong>${data}</strong>`,
      },
      {
        targets: 1,
        render: (data) => `<span class="badge bg-label-info">${data}</span>`,
      },
      {
        targets: 2,
        render: (data) =>
          `<span class="badge bg-label-${data ? 'success' : 'secondary'}">${data ? 'Activo' : 'Inactivo'}</span>`,
      },
      {
        targets: -1,
        render: (data, type, row) => {
          let btns = `<div class="d-flex align-items-center gap-1">`;
          if (canEditCargo) {
            btns += `<button class="btn btn-icon btn-text-secondary rounded-pill btn-editar-cargo"
              data-id="${row.id}" data-nombre="${row.nombre}" data-activo="${row.activo ? 1 : 0}"
              data-bs-toggle="modal" data-bs-target="#modalEditCargo" title="Editar">
              <i class="icon-base ti tabler-edit icon-md"></i></button>`;
          }
          if (canDeleteCargo) {
            btns += `<button class="btn btn-icon btn-text-danger rounded-pill btn-eliminar-cargo"
              data-id="${row.id}" data-nombre="${row.nombre}" title="Eliminar">
              <i class="icon-base ti tabler-trash icon-md"></i></button>`;
          }
          btns += `</div>`;
          return btns;
        },
      },
    ],
    order: [[3, 'desc']],
    layout: {
      topStart: {
        rowClass: 'row my-md-0 me-3 ms-0 justify-content-between',
        features: [{ pageLength: { menu: [10, 25, 50], text: '_MENU_' } }],
      },
      topEnd: {
        features: [
          { search: { placeholder: 'Buscar cargo', text: '_INPUT_' } },
          {
            buttons: [
              {
                extend: 'collection',
                className: 'btn btn-label-secondary dropdown-toggle me-2',
                text: '<span class="d-flex align-items-center gap-1"><i class="icon-base ti tabler-upload icon-xs"></i><span class="d-none d-sm-inline-block">Exportar</span></span>',
                buttons: [
                  {
                    extend: 'print',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-printer me-2"></i>Imprimir</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [0, 1, 2, 3], stripHtml: true },
                    customize: function(win) {
                      const doc = win.document;
                      const now = new Date().toLocaleDateString('es-PE', { year:'numeric', month:'long', day:'numeric' });
                      const style = doc.createElement('style');
                      style.innerHTML = `
                        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
                        .dt-print-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #696cff; padding-bottom: 12px; }
                        .dt-print-header h2 { margin: 0 0 4px; font-size: 16px; color: #696cff; }
                        .dt-print-header p { margin: 0; font-size: 11px; color: #666; }
                        table { border-collapse: collapse; width: 100%; }
                        thead tr { background: #696cff !important; color: #fff !important; }
                        thead th { padding: 8px 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }
                        tbody tr:nth-child(even) { background: #f5f5ff; }
                        tbody td { padding: 7px 10px; border-bottom: 1px solid #e0e0e0; }
                        .dt-print-footer { margin-top: 16px; font-size: 10px; color: #999; text-align: right; }
                        @media print { thead { display: table-header-group; } }
                      `;
                      doc.head.appendChild(style);
                      const header = doc.createElement('div');
                      header.className = 'dt-print-header';
                      header.innerHTML = `<h2>Unidad de Gestión Educativa Local Huánuco</h2>
                        <p>Catálogo de Cargos Institucionales — PULSO UGEL &nbsp;|&nbsp; ${now}</p>`;
                      doc.body.insertBefore(header, doc.body.firstChild);
                      const footer = doc.createElement('div');
                      footer.className = 'dt-print-footer';
                      footer.innerHTML = `Generado el ${now} &nbsp;·&nbsp; UGEL Huánuco — Sistema de Control Interno`;
                      doc.body.appendChild(footer);
                    },
                  },
                  {
                    extend: 'csv',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file me-2"></i>CSV</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [0, 1, 2, 3], stripHtml: true },
                    filename: 'catalogo-cargos-ugel',
                    bom: true,
                  },
                  {
                    extend: 'excel',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-export me-2"></i>Excel</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [0, 1, 2, 3], stripHtml: true },
                    filename: 'catalogo-cargos-ugel',
                    title: 'Catálogo de Cargos — UGEL Huánuco',
                    messageTop: `PULSO UGEL — Sistema de Control Interno | Generado: ${new Date().toLocaleDateString('es-PE')}`,
                    customize: function(xlsx) {
                      const sheet = xlsx.xl.worksheets['sheet1.xml'];
                      $('row:nth-child(4) c', sheet).attr('s', '2');
                      const cols = $('cols', sheet);
                      cols.html('<col min="1" max="1" width="40" customWidth="1"/>' +
                                '<col min="2" max="2" width="14" customWidth="1"/>' +
                                '<col min="3" max="3" width="14" customWidth="1"/>' +
                                '<col min="4" max="4" width="18" customWidth="1"/>');
                    },
                  },
                  {
                    extend: 'pdfHtml5',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-2"></i>PDF</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [0, 1, 2, 3], stripHtml: true },
                    filename: 'catalogo-cargos-ugel',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function(doc) {
                      const now = new Date().toLocaleDateString('es-PE', { year:'numeric', month:'long', day:'numeric' });
                      doc.content.unshift({
                        columns: [
                          {
                            stack: [
                              { text: 'UNIDAD DE GESTIÓN EDUCATIVA LOCAL HUÁNUCO', style: 'instHeader' },
                              { text: 'PULSO UGEL — Sistema de Control Interno', style: 'instSubHeader' },
                              { text: `Catálogo de Cargos Institucionales | ${now}`, style: 'instDate' },
                            ]
                          }
                        ],
                        margin: [0, 0, 0, 12]
                      });
                      doc.content.splice(1, 0, { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 770, y2: 0, lineWidth: 1.5, lineColor: '#696cff' }], margin: [0, 0, 0, 10] });
                      doc.footer = function(currentPage, pageCount) {
                        return {
                          columns: [
                            { text: 'UGEL Huánuco — Sistema de Control Interno', fontSize: 8, color: '#888' },
                            { text: `Página ${currentPage} de ${pageCount}`, alignment: 'right', fontSize: 8, color: '#888' }
                          ],
                          margin: [40, 0]
                        };
                      };
                      doc.styles.tableHeader   = { fillColor: '#696cff', color: '#ffffff', bold: true, fontSize: 9, alignment: 'center' };
                      doc.styles.instHeader    = { fontSize: 13, bold: true, color: '#696cff', alignment: 'center' };
                      doc.styles.instSubHeader = { fontSize: 10, color: '#555', alignment: 'center', margin: [0,2,0,0] };
                      doc.styles.instDate      = { fontSize: 9, color: '#888', alignment: 'center', margin: [0,2,0,0] };
                      doc.content.forEach(function(el) {
                        if (el.table) {
                          el.table.widths = ['*', 'auto', 'auto', 'auto'];
                          el.layout = {
                            fillColor: function(i) { return i % 2 === 0 && i > 0 ? '#f0f0ff' : null; },
                            hLineColor: function() { return '#ddd'; },
                            vLineColor: function() { return '#eee'; },
                          };
                        }
                      });
                    },
                  },
                  {
                    extend: 'copy',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-copy me-2"></i>Copiar</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [0, 1, 2, 3], stripHtml: true },
                  },
                ],
              },
              ...(canCreateCargo ? [{
                text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span class="d-none d-sm-inline-block">Nuevo Cargo</span>',
                className: 'btn btn-primary rounded-2 waves-effect waves-light',
                attr: { 'data-bs-toggle': 'modal', 'data-bs-target': '#modalAddCargo' },
              }] : []),
            ],
          },
        ],
      },
      bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
      bottomEnd: 'paging',
    },
    language: {
      info:         'Mostrando _START_ al _END_ de _TOTAL_ registros',
      infoEmpty:    'Mostrando 0 al 0 de 0 registros',
      infoFiltered: '(filtrado de _MAX_ registros en total)',
      lengthMenu:   'Mostrar _MENU_ registros',
      zeroRecords:  'No se encontraron resultados',
      emptyTable:   'No hay cargos registrados',
      search:       'Buscar:',
      paginate: {
        next:     '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
      },
    },
  });

  // Ajuste de clases del layout cargos (igual que usuarios)
  setTimeout(() => {
    const dtCargosEl = document.getElementById('dtCargos');
    if (!dtCargosEl) return;
    const wrapper = dtCargosEl.closest('.card-datatable') || dtCargosEl.parentElement;
    [
      { sel: '.dt-buttons .btn',         rm: 'btn-secondary' },
      { sel: '.dt-search .form-control', rm: 'form-control-sm' },
      { sel: '.dt-length .form-select',  rm: 'form-select-sm' },
      { sel: '.dt-length',               add: 'mb-md-6 mb-0' },
      { sel: '.dt-layout-table',         rm: 'row mt-2' },
      { sel: '.dt-layout-full',          rm: 'col-md col-12', add: 'table-responsive' },
    ].forEach(({ sel, rm, add }) => {
      wrapper?.querySelectorAll(sel).forEach(el => {
        rm  && rm.split(' ').forEach(c => el.classList.remove(c));
        add && add.split(' ').forEach(c => el.classList.add(c));
      });
    });
  }, 200);

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
      dtCargos.ajax.reload(null, false);
      Swal.fire({ icon: 'success', title: 'Creado', text: 'Cargo registrado correctamente.', timer: 2000, showConfirmButton: false });
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
      dtCargos.ajax.reload(null, false);
      Swal.fire({ icon: 'success', title: 'Actualizado', text: 'Cargo actualizado correctamente.', timer: 2000, showConfirmButton: false });
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
      }).then(async res => {
        const json = await res.json();
        if (!res.ok) { Swal.fire({ icon: 'error', title: 'Error', text: json.message ?? 'No se pudo eliminar.' }); return; }
        dtCargos.ajax.reload(null, false);
        Swal.fire({ icon: 'success', title: 'Eliminado', text: 'Cargo eliminado del catálogo.', timer: 2000, showConfirmButton: false });
      });
    });
  });

});
</script>
@endsection
