<x-action-section>
  <x-slot name="title">{{ __('Sesiones del Navegador') }}</x-slot>
  <x-slot name="description">{{ __('Administra y cierra las sesiones activas en otros navegadores y dispositivos.') }}</x-slot>

  <x-slot name="content">
    <x-action-message on="loggedOut">
      <div class="alert alert-success py-2 px-3 mb-4">
        <i class="icon-base ti tabler-check me-1"></i>{{ __('Sesiones cerradas correctamente.') }}
      </div>
    </x-action-message>

    <p class="text-muted mb-4">
      Si lo deseas, puedes cerrar sesión en todos tus otros navegadores y dispositivos.
      Algunas sesiones recientes se muestran a continuación. Si crees que tu cuenta fue comprometida,
      también debes actualizar tu contraseña.
    </p>

    @if (count($this->sessions) > 0)
    <div class="mb-4">
      @foreach ($this->sessions as $session)
      <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
        <div class="me-3 text-muted">
          @if ($session->agent->isDesktop())
          <i class="icon-base ti tabler-device-desktop" style="font-size:2rem;"></i>
          @else
          <i class="icon-base ti tabler-device-mobile" style="font-size:2rem;"></i>
          @endif
        </div>
        <div>
          <div class="fw-medium">
            {{ $session->agent->platform() ?: 'Desconocido' }} —
            {{ $session->agent->browser() ?: 'Desconocido' }}
          </div>
          <div class="small text-muted">
            {{ $session->ip_address }}
            @if ($session->is_current_device)
              · <span class="text-success fw-medium"><i class="ti tabler-circle-check me-1"></i>Este dispositivo</span>
            @else
              · Última actividad: {{ $session->last_active }}
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div class="alert alert-info mb-4">
      <i class="icon-base ti tabler-info-circle me-1"></i>No hay otras sesiones activas.
    </div>
    @endif

    <x-button wire:click="confirmLogout" wire:loading.attr="disabled" class="btn btn-danger">
      <i class="icon-base ti tabler-logout me-1"></i>{{ __('Cerrar otras sesiones') }}
    </x-button>

    <!-- Modal de confirmación -->
    <x-dialog-modal wire:model.live="confirmingLogout">
      <x-slot name="title">{{ __('Cerrar Otras Sesiones') }}</x-slot>

      <x-slot name="content">
        <p>Ingresa tu contraseña para confirmar que deseas cerrar todas las demás sesiones activas en otros dispositivos.</p>
        <div class="mt-3 form-password-toggle" x-data="{}"
          x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
          <div class="input-group input-group-merge {{ $errors->has('password') ? 'is-invalid' : '' }}">
            <x-input type="password" placeholder="Tu contraseña" x-ref="password"
              class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
              wire:model="password" wire:keydown.enter="logoutOtherBrowserSessions" />
            <span class="input-group-text cursor-pointer">
              <i class="icon-base ti tabler-eye-off"></i>
            </span>
          </div>
          <x-input-error for="password" class="mt-2" />
        </div>
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled"
          class="btn btn-label-secondary me-2">
          {{ __('Cancelar') }}
        </x-secondary-button>
        <button class="btn btn-danger" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">
          <i class="icon-base ti tabler-logout me-1"></i>{{ __('Cerrar sesiones') }}
        </button>
      </x-slot>
    </x-dialog-modal>
  </x-slot>
</x-action-section>
