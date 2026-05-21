@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Usuarios - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible mb-4" role="alert">
  {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- KPI Cards --}}
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="text-heading d-block">Total Usuarios</span>
            <h4 class="my-1 mb-0">{{ $stats['total'] }}</h4>
            <small class="text-muted">Registrados en el sistema</small>
          </div>
          <div class="avatar"><span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-users icon-26px"></i></span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="text-heading d-block">Administradores</span>
            <h4 class="my-1 mb-0">{{ $stats['admins'] }}</h4>
            <small class="text-muted">Con acceso total</small>
          </div>
          <div class="avatar"><span class="avatar-initial rounded bg-label-danger"><i class="ti tabler-user-shield icon-26px"></i></span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="text-heading d-block">Responsables</span>
            <h4 class="my-1 mb-0">{{ $stats['responsables'] }}</h4>
            <small class="text-muted">Por unidad orgánica</small>
          </div>
          <div class="avatar"><span class="avatar-initial rounded bg-label-success"><i class="ti tabler-user-check icon-26px"></i></span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="text-heading d-block">Pendientes</span>
            <h4 class="my-1 mb-0">{{ $stats['pendientes'] }}</h4>
            <small class="text-muted">Sin verificar / inactivos</small>
          </div>
          <div class="avatar"><span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-user-question icon-26px"></i></span></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Filtros + Tabla --}}
<div class="card">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
    <h5 class="mb-0">Lista de Usuarios</h5>
    @can('usuarios.crear')
    <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
      <i class="ti tabler-plus me-1"></i> Nuevo Usuario
    </button>
    @endcan
  </div>

  {{-- Filtros --}}
  <div class="card-body border-bottom pb-3">
    <form method="GET" action="{{ route('adm-usuarios') }}" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label small">Rol</label>
        <select name="rol" class="form-select form-select-sm">
          <option value="">Todos los roles</option>
          @foreach($roles as $rol)
          <option value="{{ $rol->name }}" {{ request('rol') == $rol->name ? 'selected' : '' }}>{{ $rol->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Unidad Orgánica</label>
        <select name="unidad" class="form-select form-select-sm">
          <option value="">Todas las unidades</option>
          @foreach($unidades as $u)
          <option value="{{ $u->id }}" {{ request('unidad') == $u->id ? 'selected' : '' }}>{{ $u->sigla }} - {{ $u->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small">Estado</label>
        <select name="estado" class="form-select form-select-sm">
          <option value="">Todos</option>
          <option value="activo"    {{ request('estado') == 'activo'    ? 'selected' : '' }}>Activo</option>
          <option value="inactivo"  {{ request('estado') == 'inactivo'  ? 'selected' : '' }}>Inactivo</option>
          <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-sm btn-outline-primary w-100">
          <i class="ti tabler-filter me-1"></i> Filtrar
        </button>
      </div>
      <div class="col-md-2">
        <a href="{{ route('adm-usuarios') }}" class="btn btn-sm btn-outline-secondary w-100">
          <i class="ti tabler-x me-1"></i> Limpiar
        </a>
      </div>
    </form>
  </div>

  {{-- Tabla --}}
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
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
        @forelse($usuarios as $u)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar avatar-sm">
                @if($u->profile_photo_url)
                <img src="{{ $u->profile_photo_url }}" class="rounded-circle" alt="{{ $u->name }}">
                @else
                <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($u->name, 0, 1) }}</span>
                @endif
              </div>
              <div>
                <div class="fw-medium small">{{ $u->name }}</div>
                <small class="text-muted">{{ $u->email }}</small>
              </div>
            </div>
          </td>
          <td><small>{{ $u->dni ?? '—' }}</small></td>
          <td><small>{{ $u->cargo ?? '—' }}</small></td>
          <td>
            @if($u->unidadOrganica)
            <span class="badge bg-label-info">{{ $u->unidadOrganica->sigla }}</span>
            @else
            <small class="text-muted">—</small>
            @endif
          </td>
          <td>
            @foreach($u->roles as $rol)
            <span class="badge bg-label-primary">{{ $rol->name }}</span>
            @endforeach
          </td>
          <td>
            @php
              $badgeMap = ['activo' => 'success', 'inactivo' => 'danger', 'pendiente' => 'warning'];
              $badge = $badgeMap[$u->estado] ?? 'secondary';
            @endphp
            <span class="badge bg-label-{{ $badge }}">{{ ucfirst($u->estado) }}</span>
          </td>
          <td>
            <div class="d-flex gap-1">
              @can('usuarios.editar')
              <button class="btn btn-sm btn-icon btn-outline-primary"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasEditUser"
                onclick="cargarEdicion({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->dni }}', '{{ addslashes($u->cargo) }}', '{{ $u->unidad_organica_id }}', '{{ $u->roles->first()?->name }}', '{{ $u->estado }}')"
                title="Editar">
                <i class="ti tabler-edit"></i>
              </button>
              @endcan
              @can('usuarios.editar')
              <form method="POST" action="{{ route('adm-usuarios.estado', $u) }}" class="d-inline" id="toggle-form-{{ $u->id }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm btn-icon btn-outline-{{ $u->estado === 'activo' ? 'warning' : 'success' }}"
                  title="{{ $u->estado === 'activo' ? 'Desactivar' : 'Activar' }}"
                  onclick="return confirm('¿Cambiar estado del usuario?')">
                  <i class="ti {{ $u->estado === 'activo' ? 'tabler-player-pause' : 'tabler-player-play' }}"></i>
                </button>
              </form>
              @endcan
              @can('usuarios.eliminar')
              @if($u->id !== auth()->id())
              <form method="POST" action="{{ route('adm-usuarios.destroy', $u) }}" class="d-inline" onsubmit="return confirmarEliminar(this, '{{ addslashes($u->name) }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Eliminar">
                  <i class="ti tabler-trash"></i>
                </button>
              </form>
              @endif
              @endcan
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">No se encontraron usuarios.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($usuarios->hasPages())
  <div class="card-footer">
    {{ $usuarios->links() }}
  </div>
  @endif
</div>

{{-- Offcanvas: Crear Usuario --}}
@can('usuarios.crear')
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Nuevo Usuario</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form method="POST" action="{{ route('adm-usuarios.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required placeholder="Ej: María García López">
      </div>
      <div class="mb-3">
        <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" required placeholder="usuario@ugel.gob.pe">
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control" required placeholder="Mín. 8 caracteres con mayúsculas y números">
      </div>
      <div class="mb-3">
        <label class="form-label">DNI</label>
        <input type="text" name="dni" class="form-control" maxlength="8" placeholder="12345678">
      </div>
      <div class="mb-3">
        <label class="form-label">Cargo</label>
        <input type="text" name="cargo" class="form-control" placeholder="Ej: Especialista Administrativo">
      </div>
      <div class="mb-3">
        <label class="form-label">Unidad Orgánica</label>
        <select name="unidad_organica_id" class="form-select">
          <option value="">Sin asignar</option>
          @foreach($unidades as $u)
          <option value="{{ $u->id }}">{{ $u->sigla }} - {{ $u->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Rol <span class="text-danger">*</span></label>
        <select name="rol" class="form-select" required>
          <option value="">Seleccionar rol...</option>
          @foreach($roles as $rol)
          <option value="{{ $rol->name }}">{{ $rol->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-4">
        <label class="form-label">Estado <span class="text-danger">*</span></label>
        <select name="estado" class="form-select" required>
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
          <option value="pendiente">Pendiente</option>
        </select>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">Guardar Usuario</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
      </div>
    </form>
  </div>
</div>
@endcan

{{-- Offcanvas: Editar Usuario --}}
@can('usuarios.editar')
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Editar Usuario</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form method="POST" id="formEditUser" action="">
      @csrf @method('PUT')
      <div class="mb-3">
        <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
        <input type="text" name="name" id="edit-name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
        <input type="email" name="email" id="edit-email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Nueva contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
        <input type="password" name="password" class="form-control" placeholder="Mín. 8 caracteres con mayúsculas y números">
      </div>
      <div class="mb-3">
        <label class="form-label">DNI</label>
        <input type="text" name="dni" id="edit-dni" class="form-control" maxlength="8">
      </div>
      <div class="mb-3">
        <label class="form-label">Cargo</label>
        <input type="text" name="cargo" id="edit-cargo" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Unidad Orgánica</label>
        <select name="unidad_organica_id" id="edit-unidad" class="form-select">
          <option value="">Sin asignar</option>
          @foreach($unidades as $u)
          <option value="{{ $u->id }}">{{ $u->sigla }} - {{ $u->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Rol <span class="text-danger">*</span></label>
        <select name="rol" id="edit-rol" class="form-select" required>
          @foreach($roles as $rol)
          <option value="{{ $rol->name }}">{{ $rol->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-4">
        <label class="form-label">Estado <span class="text-danger">*</span></label>
        <select name="estado" id="edit-estado" class="form-select" required>
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
          <option value="pendiente">Pendiente</option>
        </select>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">Actualizar Usuario</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
      </div>
    </form>
  </div>
</div>
@endcan

@endsection

@section('page-script')
<script>
function cargarEdicion(id, name, email, dni, cargo, unidadId, rol, estado) {
  document.getElementById('formEditUser').action = '/usuarios/' + id;
  document.getElementById('edit-name').value   = name;
  document.getElementById('edit-email').value  = email;
  document.getElementById('edit-dni').value    = dni !== 'null' ? dni : '';
  document.getElementById('edit-cargo').value  = cargo !== 'null' ? cargo : '';

  const selUnidad = document.getElementById('edit-unidad');
  if (selUnidad) selUnidad.value = unidadId !== 'null' ? unidadId : '';

  const selRol = document.getElementById('edit-rol');
  if (selRol) selRol.value = rol;

  const selEstado = document.getElementById('edit-estado');
  if (selEstado) selEstado.value = estado;
}

function confirmarEliminar(form, nombre) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: '¿Eliminar usuario?',
      text: 'Se eliminará a "' + nombre + '" permanentemente.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => { if (result.isConfirmed) form.submit(); });
    return false;
  }
  return confirm('¿Eliminar a ' + nombre + '?');
}
</script>
@endsection
