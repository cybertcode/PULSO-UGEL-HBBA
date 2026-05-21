<x-form-section submit="updateProfileInformation">
  <x-slot name="title">{{ __('Información del Perfil') }}</x-slot>
  <x-slot name="description">{{ __('Actualiza la información de tu cuenta.') }}</x-slot>

  <x-slot name="form">
    <x-action-message on="saved">
      <div class="alert alert-success py-2 px-3 mb-4">
        <i class="icon-base ti tabler-check me-1"></i> {{ __('Guardado correctamente.') }}
      </div>
    </x-action-message>

    <!-- Foto de perfil -->
    <div class="d-flex align-items-start align-items-sm-center gap-6 mb-6" x-data="{ photoPreview: null }">
      <div x-show="!photoPreview">
        <img src="{{ $this->user->profile_photo_url }}" alt="foto-perfil"
          class="d-block rounded" width="100" height="100" id="uploadedAvatar" style="object-fit:cover;" />
      </div>
      <div x-show="photoPreview" style="display:none!important" x-cloak>
        <img x-bind:src="photoPreview" class="d-block rounded" width="100" height="100" style="object-fit:cover;" />
      </div>
      <div class="button-wrapper">
        <label for="profile-photo-upload" class="btn btn-primary me-3 mb-2" tabindex="0">
          <span class="d-none d-sm-block"><i class="icon-base ti tabler-upload me-1"></i>Subir foto</span>
          <i class="icon-base ti tabler-upload d-block d-sm-none"></i>
          <input type="file" id="profile-photo-upload" class="account-file-input" hidden
            accept="image/png, image/jpeg"
            wire:model.live="photo"
            x-on:change="
              const reader = new FileReader();
              reader.onload = (e) => { photoPreview = e.target.result; };
              reader.readAsDataURL($event.target.files[0]);
            " />
        </label>
        @if ($this->user->profile_photo_path)
        <button type="button" class="btn btn-label-danger mb-2" wire:click="deleteProfilePhoto"
          x-on:click="photoPreview = null">
          <i class="icon-base ti tabler-trash me-1"></i>
          <span class="d-none d-sm-block">Eliminar foto</span>
        </button>
        @endif
        <div class="text-muted small mt-1">Formatos: JPG, PNG. Máx. 1MB</div>
        <x-input-error for="photo" class="mt-1" />
      </div>
    </div>

    <div class="row gy-4 gx-6">
      <div class="col-md-6">
        <label class="form-label" for="name">Nombre completo</label>
        <x-input id="name" type="text"
          class="form-control {{ $errors->updateProfileInformation->has('name') ? 'is-invalid' : '' }}"
          wire:model="state.name" autocomplete="name" placeholder="Tu nombre completo" />
        <x-input-error for="name" bag="updateProfileInformation" />
      </div>

      <div class="col-md-6">
        <label class="form-label" for="email">Correo electrónico</label>
        <x-input id="email" type="email"
          class="form-control {{ $errors->updateProfileInformation->has('email') ? 'is-invalid' : '' }}"
          wire:model="state.email" placeholder="tu.correo@ugel.gob.pe" />
        <x-input-error for="email" bag="updateProfileInformation" />
      </div>

      <div class="col-md-6">
        <label class="form-label" for="dni">DNI</label>
        <x-input id="dni" type="text" maxlength="8"
          class="form-control {{ $errors->updateProfileInformation->has('dni') ? 'is-invalid' : '' }}"
          wire:model="state.dni" placeholder="12345678" />
        <x-input-error for="dni" bag="updateProfileInformation" />
      </div>

      <div class="col-md-6">
        <label class="form-label" for="cargo">Cargo</label>
        <x-input id="cargo" type="text"
          class="form-control {{ $errors->updateProfileInformation->has('cargo') ? 'is-invalid' : '' }}"
          wire:model="state.cargo" placeholder="Tu cargo" />
        <x-input-error for="cargo" bag="updateProfileInformation" />
      </div>
    </div>
  </x-slot>

  <x-slot name="actions">
    <x-action-message on="saved">{{ __('Guardado.') }}</x-action-message>
    <x-button class="btn btn-primary">
      <i class="icon-base ti tabler-device-floppy me-1"></i>{{ __('Guardar cambios') }}
    </x-button>
  </x-slot>
</x-form-section>
