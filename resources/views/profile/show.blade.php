@extends('layouts.layoutMaster')
@section('title', 'Mi Perfil - PULSO UGEL')

@php
  use Illuminate\Support\Facades\Storage;
  $authUser   = $user;
  $fotoUrl    = $authUser->profile_photo_path
                  ? Storage::url($authUser->profile_photo_path)
                  : null;
  $initials   = strtoupper(
    substr($authUser->name, 0, 1) .
    (str_contains($authUser->name, ' ') ? substr($authUser->name, strrpos($authUser->name, ' ') + 1, 1) : '')
  );
@endphp

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Mi Perfil</li>
  </ol>
</nav>

{{-- Tabs de navegación --}}
<ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
  <li class="nav-item">
    <a class="nav-link {{ !request('tab') || request('tab') === 'cuenta' ? 'active' : '' }}"
       href="{{ route('profile.show') }}?tab=cuenta">
      <i class="icon-base ti tabler-user-circle icon-sm me-1_5"></i>Mi Cuenta
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request('tab') === 'password' ? 'active' : '' }}"
       href="{{ route('profile.show') }}?tab=password">
      <i class="icon-base ti tabler-lock icon-sm me-1_5"></i>Contraseña
    </a>
  </li>
</ul>

{{-- TAB: MI CUENTA --}}
@if(!request('tab') || request('tab') === 'cuenta')
<div class="card mb-6">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div>
      <h5 class="mb-0">Información del Perfil</h5>
      <small class="text-muted">Actualiza tu foto, nombre, correo y cargo</small>
    </div>
  </div>
  <div class="card-body">
    @can('perfil.editar')
    <form method="POST" action="{{ route('profile.update-info') }}" enctype="multipart/form-data">
      @csrf
    @endcan

      {{-- Foto de perfil --}}
      <div class="d-flex align-items-start align-items-sm-center gap-6 mb-6">
        <div id="avatarPreviewWrap">
          @if($fotoUrl)
            <img id="avatarPreview" src="{{ $fotoUrl }}" alt="foto" class="d-block rounded-circle"
                 width="100" height="100" style="object-fit:cover;border:3px solid var(--bs-border-color)">
          @else
            <div id="avatarInitials" class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                 style="width:100px;height:100px;font-size:2rem;background:linear-gradient(135deg,var(--bs-primary),rgba(var(--bs-primary-rgb),.7));color:#fff">
              {{ $initials }}
            </div>
            <img id="avatarPreview" src="" alt="preview"
                 class="d-none rounded-circle d-block" width="100" height="100" style="object-fit:cover;border:3px solid var(--bs-primary)">
          @endif
        </div>

        <div>
          @can('perfil.editar')
          <label for="fotoInput" class="btn btn-primary me-3 mb-2 cursor-pointer">
            <i class="icon-base ti tabler-upload me-1"></i>Subir foto
            <input type="file" id="fotoInput" name="foto" class="d-none" accept="image/png,image/jpeg">
          </label>
          @if($authUser->profile_photo_path)
          <button type="submit" name="remove_foto" value="1" class="btn btn-label-danger mb-2">
            <i class="icon-base ti tabler-trash me-1"></i>Eliminar foto
          </button>
          @endif
          <div class="text-muted small mt-1">Formatos: JPG, PNG. Máx. 2MB</div>
          @error('foto') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          @endcan
        </div>
      </div>

      {{-- Datos del perfil --}}
      <div class="row gy-4 gx-6">
        <div class="col-md-6">
          <label class="form-label fw-medium" for="name">Nombre completo @can('perfil.editar')<span class="text-danger">*</span>@endcan</label>
          <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror @cannot('perfil.editar') bg-body-secondary @endcannot"
                 value="{{ old('name', $authUser->name) }}" placeholder="Nombres y apellidos completos"
                 @cannot('perfil.editar') disabled @endcannot>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-medium" for="email">Correo electrónico @can('perfil.editar')<span class="text-danger">*</span>@endcan</label>
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror @cannot('perfil.editar') bg-body-secondary @endcannot"
                 value="{{ old('email', $authUser->email) }}" placeholder="correo@ugel.gob.pe"
                 @cannot('perfil.editar') disabled @endcannot>
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label fw-medium" for="dni">DNI</label>
          <input type="text" id="dni" name="dni" class="form-control @error('dni') is-invalid @enderror @cannot('perfil.editar') bg-body-secondary @endcannot"
                 value="{{ old('dni', $authUser->dni) }}" maxlength="8" placeholder="12345678"
                 @cannot('perfil.editar') disabled @endcannot>
          @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-8">
          <label class="form-label fw-medium" for="perfil-cargos">Cargo(s)</label>
          @can('perfil.editar')
          @php $cargosActuales = $authUser->cargos->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre])->values(); @endphp
          <select id="perfil-cargos" name="cargos[]" class="form-select @error('cargos') is-invalid @enderror" multiple="multiple">
            @foreach($cargosActuales as $c)
              <option value="{{ $c['id'] }}" selected>{{ $c['nombre'] }}</option>
            @endforeach
          </select>
          @error('cargos') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          @else
          <input type="text" class="form-control bg-body-secondary" disabled
                 value="{{ $authUser->cargos->pluck('nombre')->implode(', ') ?: 'Sin cargo' }}">
          <div class="form-text">Asignado por el administrador del sistema.</div>
          @endcan
        </div>
        <div class="col-md-6">
          <label class="form-label fw-medium">Unidad Orgánica</label>
          @can('usuarios.editar')
          <select name="unidad_organica_id" class="form-select">
            <option value="">— Sin asignar —</option>
            @foreach(\App\Models\UnidadOrganica::where('activo', true)->orderBy('nombre')->get() as $u)
              <option value="{{ $u->id }}" {{ $authUser->unidad_organica_id == $u->id ? 'selected' : '' }}>{{ $u->nombre }}</option>
            @endforeach
          </select>
          @else
          <input type="text" class="form-control bg-body-secondary" disabled
                 value="{{ $authUser->unidadOrganica?->nombre ?? 'Sin asignar' }}">
          <div class="form-text">Asignada por el administrador del sistema.</div>
          @endcan
        </div>
        <div class="col-md-3">
          <label class="form-label fw-medium">Rol(es)</label>
          @can('usuarios.editar')
          @php $rolesActuales = $authUser->roles->pluck('name')->toArray(); @endphp
          <select name="roles[]" id="perfil-roles" class="form-select" multiple="multiple">
            @foreach(\Spatie\Permission\Models\Role::orderBy('name')->get() as $r)
              <option value="{{ $r->name }}" {{ in_array($r->name, $rolesActuales) ? 'selected' : '' }}>{{ $r->name }}</option>
            @endforeach
          </select>
          @else
          <input type="text" class="form-control bg-body-secondary" disabled
                 value="{{ $authUser->roles->pluck('name')->implode(', ') ?: 'Sin rol asignado' }}">
          <div class="form-text">Asignado por el administrador.</div>
          @endcan
        </div>
        <div class="col-md-3">
          <label class="form-label fw-medium">Estado de cuenta</label>
          @php
            $estadoLabel = ['activo' => 'Activo', 'inactivo' => 'Inactivo', 'pendiente' => 'Pendiente'][$authUser->estado] ?? ucfirst($authUser->estado ?? '—');
            $estadoColor = ['activo' => 'success', 'inactivo' => 'secondary', 'pendiente' => 'warning'][$authUser->estado] ?? 'secondary';
          @endphp
          @can('usuarios.editar')
          <select name="estado" class="form-select">
            <option value="activo"    {{ $authUser->estado === 'activo'    ? 'selected' : '' }}>Activo</option>
            <option value="inactivo"  {{ $authUser->estado === 'inactivo'  ? 'selected' : '' }}>Inactivo</option>
            <option value="pendiente" {{ $authUser->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
          </select>
          @else
          <div class="form-control bg-body-secondary d-flex align-items-center" style="cursor:default;">
            <span class="badge bg-label-{{ $estadoColor }}">{{ $estadoLabel }}</span>
          </div>
          <div class="form-text">Gestionado por el administrador.</div>
          @endcan
        </div>
      </div>

      @can('perfil.editar')
      <div class="mt-5 pt-2 border-top">
        <button type="submit" class="btn btn-primary">
          <i class="icon-base ti tabler-device-floppy me-1"></i>Guardar cambios
        </button>
      </div>
      @endcan

    @can('perfil.editar')
    </form>
    @endcan
  </div>
</div>
@endif

{{-- TAB: CONTRASEÑA --}}
@if(request('tab') === 'password')
<div class="card mb-6">
  <div class="card-header">
    <h5 class="mb-0">Cambiar Contraseña</h5>
    <small class="text-muted">Asegúrate de usar una contraseña segura de al menos 8 caracteres</small>
  </div>
  <div class="card-body">
    @can('perfil.editar')
    <form method="POST" action="{{ route('profile.update-password') }}">
      @csrf
      <div class="row gy-4 gx-6">
        <div class="col-md-6">
          <label class="form-label fw-medium" for="current_password">Contraseña actual <span class="text-danger">*</span></label>
          <div class="input-group input-group-merge">
            <input type="password" id="current_password" name="current_password"
                   class="form-control @error('current_password') is-invalid @enderror"
                   placeholder="••••••••" required>
            <span class="input-group-text cursor-pointer" onclick="togglePwd('current_password')">
              <i class="icon-base ti tabler-eye"></i>
            </span>
            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-6">
          <label class="form-label fw-medium" for="password">Nueva contraseña <span class="text-danger">*</span></label>
          <div class="input-group input-group-merge">
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="••••••••" required>
            <span class="input-group-text cursor-pointer" onclick="togglePwd('password')">
              <i class="icon-base ti tabler-eye"></i>
            </span>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-medium" for="password_confirmation">Confirmar contraseña <span class="text-danger">*</span></label>
          <div class="input-group input-group-merge">
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control" placeholder="••••••••" required>
            <span class="input-group-text cursor-pointer" onclick="togglePwd('password_confirmation')">
              <i class="icon-base ti tabler-eye"></i>
            </span>
          </div>
        </div>
      </div>
      <div class="mt-5 pt-2 border-top">
        <button type="submit" class="btn btn-primary">
          <i class="icon-base ti tabler-lock-check me-1"></i>Actualizar contraseña
        </button>
      </div>
    </form>
    @else
    <div class="alert alert-warning mb-0">
      <i class="ti tabler-lock me-2"></i>No tienes permisos para cambiar la contraseña. Contacta al administrador.
    </div>
    @endcan
  </div>
</div>
@endif

@endsection

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
<script>
function initPerfilSelect2() {
  var $ = window.$;
  if (!$ || !$.fn.select2) { setTimeout(initPerfilSelect2, 100); return; }

  // Roles múltiples
  $('#perfil-roles').select2({
    placeholder: 'Seleccionar rol(es)...',
    allowClear: false,
    width: '100%',
    closeOnSelect: false,
  });

  // Cargo(s) múltiple con AJAX
  if ($('#perfil-cargos').length) {
    $('#perfil-cargos').select2({
      placeholder: 'Buscar cargo(s)...',
      allowClear: true,
      width: '100%',
      closeOnSelect: false,
      ajax: {
        url: '{{ route("cargos.index") }}?select=1',
        dataType: 'json',
        delay: 200,
        data: function(params) { return { q: params.term }; },
        processResults: function(data) { return { results: data.map(function(c) { return { id: c.id, text: c.nombre }; }) }; },
        cache: true,
      },
      minimumInputLength: 0,
    });
  }
}
document.addEventListener('DOMContentLoaded', initPerfilSelect2);

// Preview foto antes de subir
document.getElementById('fotoInput')?.addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(ev) {
    const preview = document.getElementById('avatarPreview');
    const initials = document.getElementById('avatarInitials');
    preview.src = ev.target.result;
    preview.classList.remove('d-none');
    if (initials) initials.classList.add('d-none');
  };
  reader.readAsDataURL(file);
});

// Toggle visibilidad contraseña
function togglePwd(id) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
