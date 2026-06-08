@php
use Illuminate\Support\Facades\Storage;
$configInstitucional = $configInstitucional ?? \App\Models\ConfiguracionInstitucional::first();
$containerFooter =
isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
? 'container-xxl'
: 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
      <div class="d-flex align-items-center gap-2 text-body">
        @if(!empty($configInstitucional?->logo_ruta))
          <img src="{{ Storage::url($configInstitucional->logo_ruta) }}" height="22" class="rounded" alt="logo">
        @endif
        <span>
          &#169; {{ date('Y') }}
          <strong>{{ $configInstitucional?->nombre_institucion ?? config('variables.creatorName') }}</strong>
          @if($configInstitucional?->anio_gestion)
            &mdash; Gestión {{ $configInstitucional->anio_gestion }}
          @endif
        </span>
      </div>
      <div class="d-none d-lg-inline-block text-muted small">
        @if($configInstitucional?->sigla)
          {{ $configInstitucional->sigla }}
          @if($configInstitucional?->region)
            &bull; {{ $configInstitucional->region }}
          @endif
        @endif
      </div>
    </div>
  </div>
</footer>
<!-- / Footer -->
