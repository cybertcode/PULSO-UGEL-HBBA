<x-action-section>
  <x-slot name="title">{{ __('Autenticación en Dos Pasos') }}</x-slot>
  <x-slot name="description">{{ __('Agrega seguridad adicional a tu cuenta con la autenticación en dos pasos.') }}</x-slot>

  <x-slot name="content">
    <h6 class="mb-3">
      @if ($this->enabled)
        @if ($showingConfirmation)
          <span class="badge bg-warning">Activando autenticación en dos pasos...</span>
        @else
          <span class="badge bg-success"><i class="ti tabler-shield-check me-1"></i>Autenticación en dos pasos activada</span>
        @endif
      @else
        <span class="badge bg-secondary">Autenticación en dos pasos no activada</span>
      @endif
    </h6>

    <p class="text-muted">
      Cuando la autenticación en dos pasos está habilitada, se te pedirá un código seguro al iniciar sesión.
      Puedes obtener este código desde la aplicación <strong>Google Authenticator</strong> en tu teléfono.
    </p>

    @if ($this->enabled)
      @if ($showingQrCode)
      <p class="mt-3">
        @if ($showingConfirmation)
          Escanea el siguiente código QR con tu aplicación de autenticación y confirma con el código OTP generado.
        @else
          Autenticación en dos pasos activada. Escanea el código QR con tu aplicación de autenticación.
        @endif
      </p>
      <div class="mt-3 p-3 bg-light rounded d-inline-block">
        {!! $this->user->twoFactorQrCodeSvg() !!}
      </div>
      <div class="mt-3">
        <p class="mb-1"><strong>Clave de configuración:</strong></p>
        <code class="p-2 bg-light rounded d-inline-block">{{ decrypt($this->user->two_factor_secret) }}</code>
      </div>
      @if ($showingConfirmation)
      <div class="mt-3">
        <label class="form-label" for="code">Código de confirmación</label>
        <x-input id="code" class="form-control" type="text" inputmode="numeric" name="code"
          autofocus autocomplete="one-time-code"
          wire:model="code" wire:keydown.enter="confirmTwoFactorAuthentication"
          placeholder="Ingresa el código de 6 dígitos" />
        <x-input-error for="code" class="mt-2" />
      </div>
      @endif
      @endif

      @if ($showingRecoveryCodes)
      <div class="mt-3">
        <p class="text-muted">
          Guarda estos códigos de recuperación en un lugar seguro. Puedes usarlos para acceder a tu cuenta
          si pierdes acceso a tu dispositivo de autenticación.
        </p>
        <div class="bg-light rounded p-3 font-monospace small">
          @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
          <div>{{ $code }}</div>
          @endforeach
        </div>
      </div>
      @endif
    @endif

    <div class="mt-4 d-flex gap-2 flex-wrap">
      @if (!$this->enabled)
        <x-confirms-password wire:then="enableTwoFactorAuthentication">
          <x-button type="button" wire:loading.attr="disabled" class="btn btn-primary">
            <i class="icon-base ti tabler-shield-lock me-1"></i>{{ __('Activar 2FA') }}
          </x-button>
        </x-confirms-password>
      @else
        @if ($showingRecoveryCodes)
          <x-confirms-password wire:then="regenerateRecoveryCodes">
            <x-secondary-button class="btn btn-label-warning me-1">
              <i class="icon-base ti tabler-refresh me-1"></i>{{ __('Regenerar códigos') }}
            </x-secondary-button>
          </x-confirms-password>
        @elseif ($showingConfirmation)
          <x-confirms-password wire:then="confirmTwoFactorAuthentication">
            <x-button type="button" wire:loading.attr="disabled" class="btn btn-success">
              <i class="icon-base ti tabler-check me-1"></i>{{ __('Confirmar') }}
            </x-button>
          </x-confirms-password>
        @else
          <x-confirms-password wire:then="showRecoveryCodes">
            <x-secondary-button class="btn btn-label-secondary me-1">
              <i class="icon-base ti tabler-key me-1"></i>{{ __('Ver códigos de recuperación') }}
            </x-secondary-button>
          </x-confirms-password>
        @endif

        <x-confirms-password wire:then="disableTwoFactorAuthentication">
          <x-danger-button wire:loading.attr="disabled" class="btn btn-danger">
            <i class="icon-base ti tabler-shield-off me-1"></i>{{ __('Desactivar 2FA') }}
          </x-danger-button>
        </x-confirms-password>
      @endif
    </div>
  </x-slot>
</x-action-section>
