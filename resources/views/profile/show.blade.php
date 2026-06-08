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
    <form method="POST" action="{{ route('profile.update-info') }}" enctype="multipart/form-data">
      @csrf

      {{-- Foto de perfil --}}
      <div class="d-flex align-items-start align-items-sm-center gap-6 mb-6">
        {{-- Avatar actual --}}
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
          <label for="fotoInput" class="btn btn-primary me-3 mb-2 cursor-pointer">
            <i class="icon-base ti tabler-upload me-1"></i>
            <span>Subir foto</span>
            <input type="file" id="fotoInput" name="foto" class="d-none" accept="image/png,image/jpeg">
          </label>
          @if($authUser->profile_photo_path)
          <button type="submit" name="remove_foto" value="1" class="btn btn-label-danger mb-2">
            <i class="icon-base ti tabler-trash me-1"></i>Eliminar foto
          </button>
          @endif
          <div class="text-muted small mt-1">Formatos: JPG, PNG. Máx. 2MB</div>
          @error('foto') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      {{-- Datos del perfil --}}
      <div class="row gy-4 gx-6">
        <div class="col-md-6">
          <label class="form-label fw-medium" for="name">Nombre completo <span class="text-danger">*</span></label>
          <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name', $authUser->name) }}" required placeholder="Nombres y apellidos completos">
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-medium" for="email">Correo electrónico <span class="text-danger">*</span></label>
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email', $authUser->email) }}" required placeholder="correo@ugel.gob.pe">
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label fw-medium" for="dni">DNI</label>
          <input type="text" id="dni" name="dni" class="form-control @error('dni') is-invalid @enderror"
                 value="{{ old('dni', $authUser->dni) }}" maxlength="8" placeholder="12345678">
          @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-8">
          <label class="form-label fw-medium" for="cargo">Cargo</label>
          <input type="text" id="cargo" name="cargo" class="form-control @error('cargo') is-invalid @enderror"
                 value="{{ old('cargo', $authUser->cargo) }}" placeholder="Especialista en Control Interno">
          @error('cargo') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        @if($authUser->unidadOrganica)
        <div class="col-md-12">
          <label class="form-label fw-medium">Unidad Orgánica</label>
          <input type="text" class="form-control bg-body-secondary" value="{{ $authUser->unidadOrganica->nombre }}" readonly>
          <div class="form-text">La unidad orgánica es asignada por el administrador del sistema.</div>
        </div>
        @endif
      </div>

      <div class="mt-5 pt-2 border-top">
        <button type="submit" class="btn btn-primary">
          <i class="icon-base ti tabler-device-floppy me-1"></i>Guardar cambios
        </button>
      </div>
    </form>
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
  </div>
</div>
@endif

@endsection

@section('page-script')
<script>
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
