<x-action-section>
  <x-slot name="title">{{ __('Eliminar Cuenta') }}</x-slot>
  <x-slot name="description">{{ __('Eliminar permanentemente tu cuenta del sistema.') }}</x-slot>

  <x-slot name="content">
    <p class="text-muted">
      Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán borrados permanentemente.
      Antes de eliminar tu cuenta, descarga cualquier información que desees conservar.
    </p>

    <div class="mt-3">
      <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled" class="btn btn-danger">
        <i class="icon-base ti tabler-trash me-1"></i>{{ __('Eliminar mi cuenta') }}
      </x-danger-button>
    </div>

    <!-- Modal de confirmación -->
    <x-dialog-modal wire:model.live="confirmingUserDeletion">
      <x-slot name="title">
        <i class="icon-base ti tabler-alert-triangle text-danger me-1"></i>{{ __('Eliminar Cuenta') }}
      </x-slot>

      <x-slot name="content">
        <p>¿Estás seguro de que deseas eliminar tu cuenta? Una vez eliminada, todos tus datos serán borrados permanentemente. Ingresa tu contraseña para confirmar.</p>

        <div class="mt-3 form-password-toggle" x-data="{}"
          x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
          <div class="input-group input-group-merge {{ $errors->has('password') ? 'is-invalid' : '' }}">
            <x-input type="password" placeholder="Tu contraseña" x-ref="password"
              class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
              wire:model="password" wire:keydown.enter="deleteUser" />
            <span class="input-group-text cursor-pointer">
              <i class="icon-base ti tabler-eye-off"></i>
            </span>
          </div>
          <x-input-error for="password" class="mt-2" />
        </div>
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled"
          class="btn btn-label-secondary me-2">
          {{ __('Cancelar') }}
        </x-secondary-button>
        <x-danger-button class="btn btn-danger" wire:click="deleteUser" wire:loading.attr="disabled">
          <i class="icon-base ti tabler-trash me-1"></i>{{ __('Eliminar cuenta') }}
        </x-danger-button>
      </x-slot>
    </x-dialog-modal>
  </x-slot>
</x-action-section>
