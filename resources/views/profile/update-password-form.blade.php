<x-form-section submit="updatePassword">
  <x-slot name="title">{{ __('Cambiar Contraseña') }}</x-slot>
  <x-slot name="description">{{ __('Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.') }}</x-slot>

  <x-slot name="form">
    <x-action-message class="me-3" on="saved">
      <div class="alert alert-success py-2 px-3 mb-4">
        <i class="icon-base ti tabler-check me-1"></i> {{ __('Contraseña actualizada.') }}
      </div>
    </x-action-message>

    <div class="row gy-4 gx-6">
      <div class="col-md-6 form-password-toggle">
        <label class="form-label" for="current_password">Contraseña actual</label>
        <div class="input-group input-group-merge {{ $errors->has('current_password') ? 'is-invalid' : '' }}">
          <x-input id="current_password" type="password"
            class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
            wire:model="state.current_password" autocomplete="current-password"
            placeholder="············" />
          <span class="input-group-text cursor-pointer">
            <i class="icon-base ti tabler-eye-off"></i>
          </span>
        </div>
        <x-input-error for="current_password" />
      </div>

      <div class="col-md-6 form-password-toggle">
        <label class="form-label" for="password">Nueva contraseña</label>
        <div class="input-group input-group-merge {{ $errors->has('password') ? 'is-invalid' : '' }}">
          <x-input id="password" type="password"
            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
            wire:model="state.password" autocomplete="new-password"
            placeholder="············" />
          <span class="input-group-text cursor-pointer">
            <i class="icon-base ti tabler-eye-off"></i>
          </span>
        </div>
        <x-input-error for="password" />
      </div>

      <div class="col-md-6 form-password-toggle">
        <label class="form-label" for="password_confirmation">Confirmar nueva contraseña</label>
        <div class="input-group input-group-merge {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}">
          <x-input id="password_confirmation" type="password"
            class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
            wire:model="state.password_confirmation" autocomplete="new-password"
            placeholder="············" />
          <span class="input-group-text cursor-pointer">
            <i class="icon-base ti tabler-eye-off"></i>
          </span>
        </div>
        <x-input-error for="password_confirmation" />
      </div>
    </div>
  </x-slot>

  <x-slot name="actions">
    <x-action-message on="saved">{{ __('Guardado.') }}</x-action-message>
    <x-button class="btn btn-primary">
      <i class="icon-base ti tabler-device-floppy me-1"></i>{{ __('Actualizar contraseña') }}
    </x-button>
  </x-slot>
</x-form-section>
