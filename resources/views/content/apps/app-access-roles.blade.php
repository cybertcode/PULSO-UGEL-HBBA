@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Roles - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
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

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-1">Roles del Sistema</h4>
    <p class="mb-0 text-muted">Gestiona los roles y sus permisos. Los usuarios heredan los permisos del rol asignado.</p>
  </div>
  @can('configuracion.editar')
  <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddRole">
    <i class="ti tabler-plus me-1"></i> Nuevo Rol
  </button>
  @endcan
</div>

{{-- Cards de Roles --}}
<div class="row g-4 mb-4">
  @foreach($roles as $rol)
  @php
    $colorMap = ['Administrador' => 'danger', 'Responsable de Unidad' => 'success', 'Operador' => 'info', 'Visualizador' => 'secondary'];
    $color = $colorMap[$rol->name] ?? 'primary';
    $iconMap = ['Administrador' => 'tabler-shield-lock', 'Responsable de Unidad' => 'tabler-user-check', 'Operador' => 'tabler-user-edit', 'Visualizador' => 'tabler-eye'];
    $icon = $iconMap[$rol->name] ?? 'tabler-users';
  @endphp
  <div class="col-xl-4 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div class="d-flex align-items-center gap-2">
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-{{ $color }}">
                <i class="ti {{ $icon }} icon-22px"></i>
              </span>
            </div>
            <div>
              <h6 class="mb-0">{{ $rol->name }}</h6>
              <small class="text-muted">{{ $rol->users_count }} usuario(s)</small>
            </div>
          </div>
          @can('configuracion.editar')
          <div class="d-flex gap-1">
            <button class="btn btn-sm btn-icon btn-outline-primary"
              data-bs-toggle="offcanvas" data-bs-target="#offcanvasEditRole"
              onclick="cargarRol({{ $rol->id }}, '{{ addslashes($rol->name) }}', {{ $rol->permissions->pluck('name')->toJson() }})"
              title="Editar rol">
              <i class="ti tabler-edit"></i>
            </button>
            @if($rol->users_count == 0)
            <form method="POST" action="{{ route('adm-roles.destroy', $rol) }}" class="d-inline"
              onsubmit="return confirmarEliminar(this, '{{ addslashes($rol->name) }}')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Eliminar rol">
                <i class="ti tabler-trash"></i>
              </button>
            </form>
            @endif
          </div>
          @endcan
        </div>

        <div class="mb-3">
          <small class="text-muted fw-medium d-block mb-2">PERMISOS ASIGNADOS ({{ $rol->permissions->count() }})</small>
          <div class="d-flex flex-wrap gap-1">
            @forelse($rol->permissions->take(8) as $permiso)
            <span class="badge bg-label-{{ $color }} small">{{ $permiso->name }}</span>
            @empty
            <small class="text-muted">Sin permisos asignados</small>
            @endforelse
            @if($rol->permissions->count() > 8)
            <span class="badge bg-label-secondary small">+{{ $rol->permissions->count() - 8 }} más</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Tabla de permisos por módulo --}}
<div class="card">
  <div class="card-header"><h5 class="mb-0">Matriz de Permisos por Módulo</h5></div>
  <div class="table-responsive">
    <table class="table table-bordered mb-0">
      <thead class="table-light">
        <tr>
          <th>Módulo / Permiso</th>
          @foreach($roles as $rol)
          <th class="text-center">{{ $rol->name }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($permisos as $modulo => $listaPermisos)
        <tr class="table-light">
          <td colspan="{{ $roles->count() + 1 }}" class="fw-bold text-uppercase small py-2">
            <i class="ti tabler-folder me-1"></i>{{ $modulo }}
          </td>
        </tr>
        @foreach($listaPermisos as $permiso)
        <tr>
          <td class="ps-4"><small>{{ $permiso->name }}</small></td>
          @foreach($roles as $rol)
          <td class="text-center">
            @if($rol->permissions->contains('id', $permiso->id))
            <i class="ti tabler-check text-success icon-18px"></i>
            @else
            <i class="ti tabler-x text-danger icon-18px"></i>
            @endif
          </td>
          @endforeach
        </tr>
        @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Offcanvas: Crear Rol --}}
@can('configuracion.editar')
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddRole" style="width:480px">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Nuevo Rol</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form method="POST" action="{{ route('adm-roles.store') }}">
      @csrf
      <div class="mb-4">
        <label class="form-label">Nombre del Rol <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required placeholder="Ej: Supervisor de Calidad">
      </div>
      <div class="mb-4">
        <label class="form-label fw-medium">Permisos del Rol</label>
        <p class="text-muted small mb-3">Selecciona los permisos que tendrá este rol. Los usuarios con este rol heredarán estos permisos.</p>
        @foreach($permisos as $modulo => $listaPermisos)
        <div class="mb-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="form-check">
              <input type="checkbox" class="form-check-input toggle-all" id="add-all-{{ $modulo }}"
                data-group="add-{{ $modulo }}">
              <label class="form-check-label fw-bold text-uppercase small" for="add-all-{{ $modulo }}">{{ $modulo }}</label>
            </div>
          </div>
          <div class="ps-3 d-flex flex-wrap gap-2">
            @foreach($listaPermisos as $permiso)
            <div class="form-check">
              <input type="checkbox" class="form-check-input perm-check add-{{ $modulo }}"
                name="permisos[]" value="{{ $permiso->name }}"
                id="add-perm-{{ $permiso->id }}">
              <label class="form-check-label small" for="add-perm-{{ $permiso->id }}">
                {{ $permiso->name }}
              </label>
            </div>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">Crear Rol</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
      </div>
    </form>
  </div>
</div>

{{-- Offcanvas: Editar Rol --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditRole" style="width:480px">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Editar Rol</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form method="POST" id="formEditRole" action="">
      @csrf @method('PUT')
      <div class="mb-4">
        <label class="form-label">Nombre del Rol <span class="text-danger">*</span></label>
        <input type="text" name="name" id="edit-role-name" class="form-control" required>
      </div>
      <div class="mb-4">
        <label class="form-label fw-medium">Permisos del Rol</label>
        <p class="text-muted small mb-3">Marca los permisos que debe tener este rol.</p>
        @foreach($permisos as $modulo => $listaPermisos)
        <div class="mb-3">
          <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input toggle-all" id="edit-all-{{ $modulo }}"
              data-group="edit-{{ $modulo }}">
            <label class="form-check-label fw-bold text-uppercase small" for="edit-all-{{ $modulo }}">{{ $modulo }}</label>
          </div>
          <div class="ps-3 d-flex flex-wrap gap-2">
            @foreach($listaPermisos as $permiso)
            <div class="form-check">
              <input type="checkbox" class="form-check-input perm-check edit-{{ $modulo }}"
                name="permisos[]" value="{{ $permiso->name }}"
                id="edit-perm-{{ $permiso->id }}">
              <label class="form-check-label small" for="edit-perm-{{ $permiso->id }}">
                {{ $permiso->name }}
              </label>
            </div>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">Guardar Cambios</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
      </div>
    </form>
  </div>
</div>
@endcan

@endsection

@section('page-script')
<script>
function cargarRol(id, name, permisos) {
  document.getElementById('formEditRole').action = '/roles/' + id;
  document.getElementById('edit-role-name').value = name;

  // Desmarcar todos
  document.querySelectorAll('#offcanvasEditRole input[name="permisos[]"]').forEach(cb => {
    cb.checked = permisos.includes(cb.value);
  });

  // Sincronizar checkboxes "todos"
  document.querySelectorAll('#offcanvasEditRole .toggle-all').forEach(toggleAll => {
    const group = toggleAll.dataset.group;
    const checks = document.querySelectorAll('.' + group);
    toggleAll.checked = [...checks].every(c => c.checked);
  });
}

function confirmarEliminar(form, nombre) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: '¿Eliminar rol?',
      text: 'Se eliminará el rol "' + nombre + '".',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => { if (r.isConfirmed) form.submit(); });
    return false;
  }
  return confirm('¿Eliminar rol ' + nombre + '?');
}

// Toggle "marcar todos" por módulo
document.querySelectorAll('.toggle-all').forEach(toggleAll => {
  toggleAll.addEventListener('change', function () {
    const group = this.dataset.group;
    document.querySelectorAll('.' + group).forEach(cb => { cb.checked = this.checked; });
  });
});

// Sincronizar toggle-all cuando se cambia un permiso individual
document.querySelectorAll('.perm-check').forEach(cb => {
  cb.addEventListener('change', function () {
    this.classList.forEach(cls => {
      if (cls.startsWith('add-') || cls.startsWith('edit-')) {
        const group = cls;
        const all  = document.querySelectorAll('.' + group);
        const toggleAll = document.querySelector('[data-group="' + group + '"]');
        if (toggleAll) toggleAll.checked = [...all].every(c => c.checked);
      }
    });
  });
});
</script>
@endsection
