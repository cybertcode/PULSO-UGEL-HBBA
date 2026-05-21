<x-form-section submit="updateProfileInformation">
  <x-slot name="title">{{ __('Información del Perfil') }}</x-slot>
  <x-slot name="description">{{ __('Actualiza el nombre y correo electrónico de tu cuenta.') }}</x-slot>

  <x-slot name="form">
    <x-action-message on="saved">
      <div class="alert alert-success py-2 px-3 mb-4">
        <i class="icon-base ti tabler-check me-1"></i> {{ __('Guardado correctamente.') }}
      </div>
    </x-action-message>

    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
    <div class="mb-6" x-data="{ photoPreview: null }">
      <input type="file" hidden wire:model.live="photo" x-ref="photo"
        x-on:change="
          const reader = new FileReader();
          reader.onload = (e) => { photoPreview = e.target.result; };
          reader.readAsDataURL($refs.photo.files[0]);
        " />

      <div x-show="!photoPreview">
        <img src="{{ $this->user->profile_photo_url }}" alt="foto-perfil"
          class="rounded" width="80" height="80" style="object-fit:cover;" />
      </div>
      <div x-show="photoPreview">
        <img x-bind:src="photoPreview" class="rounded" width="80" height="80" style="object-fit:cover;" />
      </div>

      <div class="mt-3 d-flex gap-2">
        <button type="button" class="btn btn-sm btn-primary" x-on:click.prevent="$refs.photo.click()">
          <i class="icon-base ti tabler-upload me-1"></i>{{ __('Seleccionar foto') }}
        </button>
        @if ($this->user->profile_photo_path)
        <button type="button" class="btn btn-sm btn-danger" wire:click="deleteProfilePhoto">
          <i class="icon-base ti tabler-trash me-1"></i>{{ __('Eliminar foto') }}
        </button>
        @endif
      </div>
      <x-input-error for="photo" class="mt-2" />
    </div>
    @endif

    <div class="row gy-4 gx-6">
      <div class="col-md-6">
        <label class="form-label" for="name">Nombre completo</label>
        <x-input id="name" type="text"
          class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
          wire:model="state.name" autocomplete="name" placeholder="Tu nombre completo" />
        <x-input-error for="name" />
      </div>

      <div class="col-md-6">
        <label class="form-label" for="email">Correo electrónico</label>
        <x-input id="email" type="email"
          class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
          wire:model="state.email" placeholder="tu.correo@ugel.gob.pe" />
        <x-input-error for="email" />
      </div>
    </div>
  </x-slot>

  <x-slot name="actions">
    <x-action-message class="me-3" on="saved">
      {{ __('Guardado.') }}
    </x-action-message>
    <x-button class="btn btn-primary">
      <i class="icon-base ti tabler-device-floppy me-1"></i>{{ __('Guardar cambios') }}
    </x-button>
  </x-slot>
</x-form-section>
