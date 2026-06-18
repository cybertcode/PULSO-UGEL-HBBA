@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Roles - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection


@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('content')

<h4 class="mb-1">Lista de Roles</h4>
<p class="mb-6">
  Un rol otorga acceso a menús y funcionalidades predefinidas. Según el rol asignado,<br>
  el usuario tendrá acceso solo a lo que necesita.
</p>

{{-- Role Cards --}}
<div class="row g-6">
  @foreach($roles as $rol)
  @php
    $colorMap = ['Super Admin'=>'danger','Administrador'=>'primary','Responsable de Unidad'=>'success','Operador'=>'info','Visualizador'=>'secondary'];
    $color = $colorMap[$rol->name] ?? 'primary';
    $usersPreview = $rol->usuarios->take(4);
    $extra = $rol->users_count - $usersPreview->count();
  @endphp
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h6 class="fw-normal mb-0 text-body">Total {{ $rol->users_count }} usuario(s)</h6>
          <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
            @foreach($usersPreview as $u)
            <li class="avatar pull-up" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $u->name }}">
              <span class="avatar-initial rounded-circle bg-label-{{ $color }}">{{ strtoupper(substr($u->name,0,1)) }}</span>
            </li>
            @endforeach
            @if($extra > 0)
            <li class="avatar">
              <span class="avatar-initial rounded-circle pull-up bg-label-secondary">+{{ $extra }}</span>
            </li>
            @endif
          </ul>
        </div>
        <div class="d-flex justify-content-between align-items-end">
          <div class="role-heading">
            <h5 class="mb-1">{{ $rol->name }}</h5>
            @can('roles.editar')
            <a href="javascript:;" class="btn-editar-rol"
              data-id="{{ $rol->id }}"
              data-name="{{ addslashes($rol->name) }}"
              data-permisos="{{ $rol->permissions->pluck('name')->toJson() }}"
              data-users="{{ $rol->users_count }}"
              data-bs-toggle="modal" data-bs-target="#editRoleModal">
              <span>Editar Rol</span>
            </a>
            @else
            <span class="text-muted small">{{ $rol->permissions->count() }} permisos</span>
            @endcan
          </div>
          @can('roles.editar')
          <a href="javascript:;" class="btn-editar-rol"
            data-id="{{ $rol->id }}"
            data-name="{{ addslashes($rol->name) }}"
            data-permisos="{{ $rol->permissions->pluck('name')->toJson() }}"
            data-users="{{ $rol->users_count }}"
            data-bs-toggle="modal" data-bs-target="#editRoleModal">
            <i class="icon-base ti tabler-edit icon-md text-heading"></i>
          </a>
          @endcan
        </div>
      </div>
    </div>
  </div>
  @endforeach

  {{-- Add New Role Card --}}
  @can('roles.crear')
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card h-100">
      <div class="row h-100">
        <div class="col-sm-5">
          <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4">
            <img src="{{ asset('assets/img/illustrations/add-new-roles.png') }}" class="img-fluid" alt="Agregar Rol" width="83"
              onerror="this.style.display='none'">
          </div>
        </div>
        <div class="col-sm-7">
          <div class="card-body text-sm-end text-center ps-sm-0">
            <button data-bs-toggle="modal" data-bs-target="#addRoleModal" class="btn btn-sm btn-primary mb-4 text-nowrap add-new-role">
              Agregar Nuevo Rol
            </button>
            <p class="mb-0">Agrega un rol nuevo,<br>si no existe aún.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endcan
</div>

{{-- Tabla de usuarios con roles --}}
<div class="col-12 mt-6">
  <h4 class="mb-1">Usuarios y sus Roles</h4>
  <p class="mb-4 text-muted">Listado de todos los usuarios del sistema con su rol asignado.</p>
  <div class="card">
    <div class="card-datatable table-responsive">
      <table class="datatables-roles table border-top">
        <thead>
          <tr>
            <th></th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($usuarios as $u)
          @php
            $rolesUsuario = $u->roles->pluck('name')->toArray();
            $primerRol    = $rolesUsuario[0] ?? '—';
            $rc           = $colorMap[$primerRol] ?? 'secondary';
            $estadoVal    = $u->estado ?? 'pendiente';
            $ec           = match($estadoVal) { 'activo' => 'success', 'inactivo' => 'danger', default => 'warning' };
            $esTuCuenta   = $u->id === auth()->id();
          @endphp
          <tr>
            <td></td>
            <td>
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-sm">
                  <span class="avatar-initial rounded-circle bg-label-{{ $rc }}">{{ strtoupper(substr($u->name,0,1)) }}</span>
                </div>
                <div>
                  <span class="fw-medium d-block">{{ $u->name }}</span>
                  @if($esTuCuenta)
                    <small class="text-muted">(tú)</small>
                  @endif
                </div>
              </div>
            </td>
            <td>{{ $u->email }}</td>
            <td>
              @forelse($rolesUsuario as $rn)
                @php $rc2 = $colorMap[$rn] ?? 'secondary'; @endphp
                <span class="badge bg-label-{{ $rc2 }} me-1">{{ $rn }}</span>
              @empty
                <span class="text-muted small">Sin rol</span>
              @endforelse
            </td>
            <td><span class="badge bg-label-{{ $ec }}">{{ ucfirst($estadoVal) }}</span></td>
            <td>
              @can('usuarios.editar')
              @if($esTuCuenta)
                <span class="text-muted small" data-bs-toggle="tooltip" title="No puedes cambiar tu propio rol">
                  <i class="ti tabler-lock icon-sm"></i>
                </span>
              @else
              <div class="d-flex align-items-center gap-1">
                {{-- Dropdown toggle roles --}}
                <div class="dropdown">
                  <button type="button"
                    class="btn btn-sm btn-icon btn-label-primary"
                    data-bs-toggle="dropdown"
                    data-bs-placement="top"
                    title="Gestionar roles"
                    aria-expanded="false">
                    <i class="ti tabler-shield-cog icon-sm"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header small">Agregar / Quitar rol</h6></li>
                    @foreach($roles as $rol)
                    @php $tieneEsteRol = in_array($rol->name, $rolesUsuario); @endphp
                    <li>
                      <button type="button"
                        class="dropdown-item btn-toggle-rol d-flex align-items-center gap-2 {{ $tieneEsteRol ? 'active' : '' }}"
                        data-usuario-id="{{ $u->id }}"
                        data-usuario-nombre="{{ $u->name }}"
                        data-rol="{{ $rol->name }}"
                        data-tiene="{{ $tieneEsteRol ? '1' : '0' }}"
                        data-total-roles="{{ count($rolesUsuario) }}">
                        @if($tieneEsteRol)
                          <i class="ti tabler-check icon-xs text-success"></i>
                        @else
                          <i class="ti tabler-circle icon-xs text-muted"></i>
                        @endif
                        {{ $rol->name }}
                      </button>
                    </li>
                    @endforeach
                  </ul>
                </div>
                {{-- Ver perfil --}}
                <a href="{{ route('adm-usuarios') }}?buscar={{ urlencode($u->email) }}"
                  class="btn btn-sm btn-icon btn-label-secondary"
                  data-bs-toggle="tooltip"
                  title="Ver en Usuarios">
                  <i class="ti tabler-external-link icon-sm"></i>
                </a>
              </div>
              @endif
              @else
              <span class="text-muted small">—</span>
              @endcan
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal Agregar Rol --}}
@can('roles.crear')
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
    <div class="modal-content">
      <div class="modal-body p-6">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="role-title mb-2">Agregar Nuevo Rol</h4>
          <p class="text-body-secondary">Configura los permisos del rol</p>
        </div>
        <form method="POST" action="{{ route('adm-roles.store') }}" class="row g-3">
          @csrf
          <div class="col-12 form-control-validation">
            <label class="form-label" for="addRoleName">Nombre del Rol <span class="text-danger">*</span></label>
            <input type="text" id="addRoleName" name="name" class="form-control" placeholder="Ej: Supervisor de Calidad" required>
          </div>
          <div class="col-12">
            <h5 class="mb-4">Permisos del Rol</h5>
            <div class="table-responsive">
              <table class="table table-flush-spacing">
                <tbody>
                  <tr>
                    <td class="text-nowrap fw-medium">
                      Acceso Administrador
                      <i class="icon-base ti tabler-info-circle icon-xs ms-1" data-bs-toggle="tooltip" title="Marca todos los permisos disponibles"></i>
                    </td>
                    <td>
                      <div class="d-flex justify-content-end">
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" id="addSelectAll">
                          <label class="form-check-label" for="addSelectAll">Seleccionar Todo</label>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @foreach($permisos as $modulo => $listaPermisos)
                  <tr>
                    <td class="text-nowrap fw-medium text-heading text-capitalize">{{ $modulo }}</td>
                    <td>
                      <div class="d-flex justify-content-end flex-wrap gap-4">
                        @foreach($listaPermisos as $permiso)
                        @php $slug = $modulo . '-add-' . $permiso->id; @endphp
                        <div class="form-check mb-0">
                          <input class="form-check-input add-perm-check" type="checkbox" name="permisos[]" value="{{ $permiso->name }}" id="{{ $slug }}">
                          <label class="form-check-label" for="{{ $slug }}">{{ ucfirst(explode('.', $permiso->name)[1] ?? $permiso->name) }}</label>
                        </div>
                        @endforeach
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Crear Rol</button>
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal Editar Rol --}}
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
    <div class="modal-content">
      <div class="modal-body p-6">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="mb-2">Editar Rol</h4>
          <p class="text-body-secondary">Actualiza permisos del rol</p>
        </div>
        <form method="POST" id="formEditRole" action="" class="row g-3">
          @csrf @method('PUT')
          <div class="col-12 form-control-validation">
            <label class="form-label" for="editRoleName">Nombre del Rol <span class="text-danger">*</span></label>
            <input type="text" id="editRoleName" name="name" class="form-control" required>
          </div>
          <div class="col-12">
            <h5 class="mb-4">Permisos del Rol</h5>
            <div class="table-responsive">
              <table class="table table-flush-spacing">
                <tbody>
                  <tr>
                    <td class="text-nowrap fw-medium">
                      Acceso Administrador
                      <i class="icon-base ti tabler-info-circle icon-xs ms-1" data-bs-toggle="tooltip" title="Marca todos los permisos disponibles"></i>
                    </td>
                    <td>
                      <div class="d-flex justify-content-end">
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" id="editSelectAll">
                          <label class="form-check-label" for="editSelectAll">Seleccionar Todo</label>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @foreach($permisos as $modulo => $listaPermisos)
                  <tr>
                    <td class="text-nowrap fw-medium text-heading text-capitalize">{{ $modulo }}</td>
                    <td>
                      <div class="d-flex justify-content-end flex-wrap gap-4">
                        @foreach($listaPermisos as $permiso)
                        @php $slug = $modulo . '-edit-' . $permiso->id; @endphp
                        <div class="form-check mb-0">
                          <input class="form-check-input edit-perm-check" type="checkbox" name="permisos[]" value="{{ $permiso->name }}" id="{{ $slug }}">
                          <label class="form-check-label" for="{{ $slug }}">{{ ucfirst(explode('.', $permiso->name)[1] ?? $permiso->name) }}</label>
                        </div>
                        @endforeach
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Guardar Cambios</button>
            <button type="button" class="btn btn-label-secondary me-sm-3 me-1" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-label-danger d-none" id="btnEliminarRol">
              <i class="ti tabler-trash me-1"></i>Eliminar Rol
            </button>
          </div>
        </form>
        <form method="POST" id="formDeleteRole" action="" class="d-none">
          @csrf @method('DELETE')
        </form>
      </div>
    </div>
  </div>
</div>
@endcan


@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // DataTable usuarios
  new DataTable('.datatables-roles', {
    responsive: true,
    pageLength: 10,
    columnDefs: [
      { targets: 0, orderable: false, searchable: false, className: 'control' },
      { targets: 5, orderable: false, searchable: false },
    ],
    layout: {
      topStart: {
        rowClass: 'row m-3 my-0 justify-content-between',
        features: [{ pageLength: { menu: [10, 25, 50], text: '_MENU_' } }]
      },
      topEnd: {
        features: [{ search: { placeholder: 'Buscar usuario...', text: '_INPUT_' } }]
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
        last:     '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
      }
    }
  });

  setTimeout(() => {
    [
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

  // Tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

  // Add Modal — Select All
  document.getElementById('addSelectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.add-perm-check').forEach(c => c.checked = this.checked);
  });
  document.querySelectorAll('.add-perm-check').forEach(c => {
    c.addEventListener('change', syncSelectAll.bind(null, '.add-perm-check', 'addSelectAll'));
  });

  // Edit Modal — Select All
  document.getElementById('editSelectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.edit-perm-check').forEach(c => c.checked = this.checked);
  });
  document.querySelectorAll('.edit-perm-check').forEach(c => {
    c.addEventListener('change', syncSelectAll.bind(null, '.edit-perm-check', 'editSelectAll'));
  });

  function syncSelectAll(selector, toggleId) {
    const all = document.querySelectorAll(selector);
    const toggle = document.getElementById(toggleId);
    if (!toggle) return;
    const checked = [...all].filter(c => c.checked).length;
    toggle.checked = checked === all.length;
    toggle.indeterminate = checked > 0 && checked < all.length;
  }

  // Cargar datos en modal editar
  document.querySelectorAll('.btn-editar-rol').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const id       = this.dataset.id;
      const name     = this.dataset.name;
      const permisos = JSON.parse(this.dataset.permisos || '[]');
      const users    = parseInt(this.dataset.users || 0);

      document.getElementById('formEditRole').action = '/roles/' + id;
      document.getElementById('formDeleteRole').action = '/roles/' + id;
      document.getElementById('editRoleName').value = name;

      // Marcar permisos
      document.querySelectorAll('.edit-perm-check').forEach(cb => {
        cb.checked = permisos.includes(cb.value);
      });

      // Sincronizar Select All
      syncSelectAll('.edit-perm-check', 'editSelectAll');

      // Botón eliminar solo si no tiene usuarios
      const btnElim = document.getElementById('btnEliminarRol');
      if (btnElim) btnElim.classList.toggle('d-none', users > 0);
    });
  });

  // Confirmar eliminar rol
  document.getElementById('btnEliminarRol')?.addEventListener('click', function () {
    const nombre = document.getElementById('editRoleName').value;
    Swal.fire({
      title: '¿Eliminar rol?',
      text: 'Se eliminará el rol "' + nombre + '". Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ea5455',
      cancelButtonColor: '#6e7881',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(r => {
      if (r.isConfirmed) document.getElementById('formDeleteRole').submit();
    });
  });

  // ── Toggle rol desde tabla ──────────────────────────────────────────────
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-toggle-rol');
    if (!btn) return;

    const usuarioId     = btn.dataset.usuarioId;
    const usuarioNombre = btn.dataset.usuarioNombre;
    const rolNombre     = btn.dataset.rol;
    const tiene         = btn.dataset.tiene === '1';
    const totalRoles    = parseInt(btn.dataset.totalRoles || '1');
    const accion        = tiene ? 'quitar' : 'agregar';

    if (tiene && totalRoles <= 1) {
      Swal.fire('No permitido', 'El usuario debe tener al menos un rol asignado.', 'warning');
      return;
    }

    const textoAccion = tiene
      ? `Quitar el rol <strong>${rolNombre}</strong> a <strong>${usuarioNombre}</strong>`
      : `Asignar el rol <strong>${rolNombre}</strong> a <strong>${usuarioNombre}</strong>`;

    Swal.fire({
      title: tiene ? '¿Quitar rol?' : '¿Asignar rol?',
      html:  textoAccion,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: tiene ? '#ea5455' : '#7367f0',
      cancelButtonColor:  '#6e7881',
      confirmButtonText:  tiene ? 'Sí, quitar' : 'Sí, asignar',
      cancelButtonText:   'Cancelar'
    }).then(result => {
      if (!result.isConfirmed) return;

      fetch(`/usuarios/${usuarioId}/rol`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
          'Accept':        'application/json',
        },
        body: JSON.stringify({ rol: rolNombre, accion }),
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          sessionStorage.setItem('flash_title', 'Roles actualizados');
          sessionStorage.setItem('flash_success', data.message);
          location.reload();
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error'));
    });
  });

});
</script>
@endsection
