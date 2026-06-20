@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Configuración del Sistema')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/select2/select2.scss',
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
  ])
@endsection

@section('content')

<div class="mb-4">
  <h4 class="mb-1"><i class="ti tabler-settings me-2 text-primary"></i>Configuración del Sistema</h4>
  <p class="mb-0 text-muted">Gestión de parámetros institucionales, semáforo, notificaciones y unidades orgánicas</p>
</div>


{{-- Tabs de navegación --}}
<div class="nav-align-top mb-4">
  <ul class="nav nav-pills flex-column flex-md-row gap-md-0 gap-2 mb-0" id="configTabs">
    <li class="nav-item">
      <a class="nav-link active" href="#tab-institucional" data-bs-toggle="pill">
        <i class="ti tabler-building icon-sm me-1_5"></i>Datos Institucionales
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#tab-semaforo" data-bs-toggle="pill">
        <i class="ti tabler-traffic-lights icon-sm me-1_5"></i>Semáforo
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#tab-notificaciones" data-bs-toggle="pill">
        <i class="ti tabler-bell icon-sm me-1_5"></i>Notificaciones
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#tab-accesos" data-bs-toggle="pill">
        <i class="ti tabler-layout-grid icon-sm me-1_5"></i>Accesos Rápidos
      </a>
    </li>
  </ul>
</div>

<form method="POST" action="{{ route('adm-configuracion.update') }}" enctype="multipart/form-data" id="formConfig">
@csrf @method('PUT')

<div class="tab-content p-0">

  {{-- ============================
       TAB: DATOS INSTITUCIONALES
       ============================ --}}
  <div class="tab-pane fade show active" id="tab-institucional">
    <div class="row g-4">
      <div class="col-xl-8">

        {{-- Logo y nombre --}}
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-primary p-2"><i class="ti tabler-photo icon-20px"></i></span>
            <h5 class="mb-0">Identidad Visual</h5>
          </div>
          <div class="card-body">
            <div class="d-flex align-items-start gap-5 flex-wrap">
              {{-- Preview logo --}}
              <div class="text-center">
                <div class="position-relative d-inline-block mb-2">
                  @if($config->logo_ruta)
                    <img src="{{ Storage::url($config->logo_ruta) }}"
                         id="logoPreview"
                         alt="Logo institucional"
                         class="rounded border"
                         style="width:120px;height:120px;object-fit:contain;background:#f8f9fa">
                  @else
                    <div id="logoPreview"
                         class="rounded border d-flex align-items-center justify-content-center bg-label-secondary"
                         style="width:120px;height:120px">
                      <i class="ti tabler-building icon-40px text-muted"></i>
                    </div>
                  @endif
                </div>
                <div class="small text-muted">Logo actual</div>
              </div>

              {{-- Controles --}}
              <div class="flex-grow-1">
                <label for="logoUpload" class="btn btn-primary me-2 mb-2" tabindex="0">
                  <i class="ti tabler-upload me-1"></i>
                  <span>Subir nuevo logo</span>
                  <input type="file" id="logoUpload" name="logo" class="d-none" accept="image/png,image/jpeg,image/gif,image/svg+xml">
                </label>
                @if($config->logo_ruta)
                <button type="button" class="btn btn-label-danger mb-2" id="btnRemoveLogo">
                  <i class="ti tabler-trash me-1"></i>Eliminar logo
                </button>
                <input type="hidden" name="remove_logo" id="removeLogo" value="0">
                @endif
                <div class="text-muted small mt-1">PNG, JPG, SVG o GIF. Máximo 2 MB. Recomendado 400×400px.</div>
              </div>
            </div>

            <hr class="my-4">

            {{-- Favicon --}}
            <div class="d-flex align-items-start gap-5 flex-wrap">
              <div class="text-center">
                <div class="position-relative d-inline-block mb-2">
                  @if($config->favicon_ruta)
                    <img src="{{ Storage::url($config->favicon_ruta) }}"
                         id="faviconPreview"
                         alt="Favicon"
                         class="rounded border"
                         style="width:64px;height:64px;object-fit:contain;background:#f8f9fa">
                  @else
                    <div id="faviconPreview"
                         class="rounded border d-flex align-items-center justify-content-center bg-label-secondary"
                         style="width:64px;height:64px">
                      <i class="ti tabler-browser icon-24px text-muted"></i>
                    </div>
                  @endif
                </div>
                <div class="small text-muted">Favicon actual</div>
              </div>

              <div class="flex-grow-1">
                <label for="faviconUpload" class="btn btn-primary me-2 mb-2" tabindex="0">
                  <i class="ti tabler-upload me-1"></i>
                  <span>Subir favicon</span>
                  <input type="file" id="faviconUpload" name="favicon" class="d-none" accept="image/png,image/jpeg,image/gif,image/svg+xml,image/x-icon">
                </label>
                @if($config->favicon_ruta)
                <button type="button" class="btn btn-label-danger mb-2" id="btnRemoveFavicon">
                  <i class="ti tabler-trash me-1"></i>Eliminar favicon
                </button>
                <input type="hidden" name="remove_favicon" id="removeFavicon" value="0">
                @else
                <input type="hidden" name="remove_favicon" id="removeFavicon" value="0">
                @endif
                <div class="text-muted small mt-1">PNG, JPG, SVG, GIF o ICO. Máximo 512 KB. Recomendado 32×32 o 64×64px.</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Datos generales --}}
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-info p-2"><i class="ti tabler-info-circle icon-20px"></i></span>
            <h5 class="mb-0">Datos Generales</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label">Nombre de la Institución <span class="text-danger">*</span></label>
                <input type="text" name="nombre_institucion" class="form-control @error('nombre_institucion') is-invalid @enderror"
                  value="{{ old('nombre_institucion', $config->nombre_institucion) }}" required>
                @error('nombre_institucion')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">Sigla <span class="text-danger">*</span></label>
                <input type="text" name="sigla" class="form-control text-uppercase @error('sigla') is-invalid @enderror"
                  value="{{ old('sigla', $config->sigla) }}" required maxlength="30">
                @error('sigla')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">Departamento</label>
                <input type="text" name="departamento" class="form-control"
                  value="{{ old('departamento', $config->departamento) }}" maxlength="100"
                  placeholder="Ej: Lima">
              </div>
              <div class="col-md-4">
                <label class="form-label">Provincia</label>
                <input type="text" name="provincia" class="form-control"
                  value="{{ old('provincia', $config->provincia) }}" maxlength="100"
                  placeholder="Ej: Lima">
              </div>
              <div class="col-md-4">
                <label class="form-label">Distrito</label>
                <input type="text" name="distrito" class="form-control"
                  value="{{ old('distrito', $config->distrito) }}" maxlength="100"
                  placeholder="Ej: San Isidro">
              </div>
              <div class="col-md-4">
                <label class="form-label">Región / Departamento (UGEL)</label>
                <input type="text" name="region" class="form-control"
                  value="{{ old('region', $config->region) }}" maxlength="100"
                  placeholder="Ej: Lima Metropolitana">
              </div>
              <div class="col-md-4">
                <label class="form-label">Ubigeo</label>
                <input type="text" name="ubigeo" class="form-control"
                  value="{{ old('ubigeo', $config->ubigeo) }}" maxlength="10"
                  placeholder="Ej: 150101">
              </div>
              <div class="col-md-4">
                <label class="form-label">Año de Gestión</label>
                <input type="number" name="anio_gestion" class="form-control"
                  value="{{ old('anio_gestion', $config->anio_gestion) }}" min="2020" max="2099">
              </div>
              <div class="col-md-8">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control"
                  value="{{ old('direccion', $config->direccion) }}" maxlength="255"
                  placeholder="Ej: Av. Arequipa 123, Lima">
              </div>
              <div class="col-md-4">
                <label class="form-label">Sitio Web</label>
                <input type="url" name="sitio_web" class="form-control"
                  value="{{ old('sitio_web', $config->sitio_web) }}" maxlength="255"
                  placeholder="https://www.ugel.gob.pe">
              </div>
            </div>
          </div>
        </div>

        {{-- Zona horaria y configuración regional --}}
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-warning p-2"><i class="ti tabler-clock icon-20px"></i></span>
            <h5 class="mb-0">Configuración Regional</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Zona Horaria</label>
                <select name="timezone" class="form-select">
                  @php
                    $zonas = [
                      'America/Lima'       => 'Lima, Perú (UTC-5)',
                      'America/Bogota'     => 'Bogotá, Colombia (UTC-5)',
                      'America/Santiago'   => 'Santiago, Chile (UTC-4)',
                      'America/La_Paz'     => 'La Paz, Bolivia (UTC-4)',
                      'America/Caracas'    => 'Caracas, Venezuela (UTC-4)',
                      'America/Sao_Paulo'  => 'São Paulo, Brasil (UTC-3)',
                      'America/Argentina/Buenos_Aires' => 'Buenos Aires, Argentina (UTC-3)',
                      'America/Guayaquil'  => 'Guayaquil, Ecuador (UTC-5)',
                      'America/Asuncion'   => 'Asunción, Paraguay (UTC-4)',
                      'America/Montevideo' => 'Montevideo, Uruguay (UTC-3)',
                    ];
                    $tzActual = old('timezone', $config->timezone ?? 'America/Lima');
                  @endphp
                  @foreach($zonas as $tz => $label)
                    <option value="{{ $tz }}" {{ $tzActual === $tz ? 'selected' : '' }}>{{ $label }}</option>
                  @endforeach
                </select>
                <div class="form-text">Hora actual del servidor: <strong>{{ now()->format('d/m/Y H:i:s') }}</strong></div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Código UGEL</label>
                <input type="text" name="ugel_codigo" class="form-control"
                  value="{{ old('ugel_codigo', $config->ugel_codigo) }}" maxlength="20"
                  placeholder="Ej: 150101">
                <div class="form-text">Código oficial asignado por el MINEDU</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Autoridades --}}
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-success p-2"><i class="ti tabler-user-star icon-20px"></i></span>
            <h5 class="mb-0">Autoridades y Contacto</h5>
          </div>
          <div class="card-body">

            {{-- Selector Director --}}
            <div class="mb-4">
              <label class="form-label fw-medium">
                <i class="ti tabler-user-tie me-1 text-primary"></i>Director(a) de la Institución
              </label>
              <select name="director_id" id="selectDirector" class="form-select select2-usuarios @error('director_id') is-invalid @enderror"
                      data-placeholder="Buscar y seleccionar director(a)...">
                <option value=""></option>
                @foreach($usuarios as $u)
                  <option value="{{ $u['id'] }}"
                    data-cargo="{{ $u['cargo'] }}"
                    data-unidad="{{ $u['unidad'] }}"
                    data-email="{{ $u['email'] }}"
                    data-foto="{{ $u['foto_url'] }}"
                    {{ old('director_id', $config->director_id) == $u['id'] ? 'selected' : '' }}>
                    {{ $u['name'] }}
                  </option>
                @endforeach
              </select>
              @error('director_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              {{-- Tarjeta de preview del director seleccionado --}}
              <div id="previewDirector" class="{{ $config->director_id ? '' : 'd-none' }} mt-3">
                <div class="d-flex align-items-center gap-3 p-3 rounded border bg-label-primary">
                  <div id="previewDirectorFoto" class="flex-shrink-0"></div>
                  <div>
                    <div class="fw-semibold" id="previewDirectorNombre">{{ $config->directorUser?->name }}</div>
                    <div class="small text-muted" id="previewDirectorCargo">{{ $config->directorUser?->cargos->first()?->nombre }}</div>
                    <div class="small text-muted" id="previewDirectorUnidad">{{ $config->directorUser?->unidadOrganica?->nombre }}</div>
                    <div class="small" id="previewDirectorEmail">{{ $config->directorUser?->email }}</div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Selector Coordinador SCI --}}
            <div class="mb-4">
              <label class="form-label fw-medium">
                <i class="ti tabler-user-check me-1 text-success"></i>Coordinador(a) del SCI
              </label>
              <select name="coordinador_sci_id" id="selectCoordinadorSci" class="form-select select2-usuarios @error('coordinador_sci_id') is-invalid @enderror"
                      data-placeholder="Buscar y seleccionar coordinador(a) SCI...">
                <option value=""></option>
                @foreach($usuarios as $u)
                  <option value="{{ $u['id'] }}"
                    data-cargo="{{ $u['cargo'] }}"
                    data-unidad="{{ $u['unidad'] }}"
                    data-email="{{ $u['email'] }}"
                    data-foto="{{ $u['foto_url'] }}"
                    {{ old('coordinador_sci_id', $config->coordinador_sci_id) == $u['id'] ? 'selected' : '' }}>
                    {{ $u['name'] }}
                  </option>
                @endforeach
              </select>
              @error('coordinador_sci_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              {{-- Tarjeta de preview del coordinador seleccionado --}}
              <div id="previewCoordinador" class="{{ $config->coordinador_sci_id ? '' : 'd-none' }} mt-3">
                <div class="d-flex align-items-center gap-3 p-3 rounded border bg-label-success">
                  <div id="previewCoordinadorFoto" class="flex-shrink-0"></div>
                  <div>
                    <div class="fw-semibold" id="previewCoordinadorNombre">{{ $config->coordinadorSciUser?->name }}</div>
                    <div class="small text-muted" id="previewCoordinadorCargo">{{ $config->coordinadorSciUser?->cargos->first()?->nombre }}</div>
                    <div class="small text-muted" id="previewCoordinadorUnidad">{{ $config->coordinadorSciUser?->unidadOrganica?->nombre }}</div>
                    <div class="small" id="previewCoordinadorEmail">{{ $config->coordinadorSciUser?->email }}</div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Contacto directo del Coordinador SCI --}}
            <div class="row g-3 mt-1">
              <div class="col-12">
                <p class="text-muted small mb-2"><i class="ti tabler-info-circle me-1"></i>Estos datos se muestran en el portal público para que los ciudadanos contacten directamente al Coordinador SCI.</p>
              </div>
              <div class="col-md-4">
                <label class="form-label">Cargo / Título del Coordinador SCI</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="ti tabler-id-badge"></i></span>
                  <input type="text" name="cargo_sci" class="form-control"
                    value="{{ old('cargo_sci', $config->cargo_sci) }}" placeholder="Coordinador SCI" maxlength="80">
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label">WhatsApp del Coordinador SCI</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="ti tabler-brand-whatsapp text-success"></i></span>
                  <input type="text" name="whatsapp_sci" class="form-control"
                    value="{{ old('whatsapp_sci', $config->whatsapp_sci) }}" placeholder="987654321" maxlength="20">
                </div>
                <div class="text-muted" style="font-size:.72rem;margin-top:.25rem;">Solo dígitos, sin prefijo +51. Se enlazará a WhatsApp.</div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Correo del Coordinador SCI</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="ti tabler-mail"></i></span>
                  <input type="email" name="correo_sci" class="form-control"
                    value="{{ old('correo_sci', $config->correo_sci) }}" placeholder="sci@ugel.gob.pe">
                </div>
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-7">
                <label class="form-label">Correo Institucional</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="ti tabler-mail"></i></span>
                  <input type="email" name="correo_institucional" class="form-control @error('correo_institucional') is-invalid @enderror"
                    value="{{ old('correo_institucional', $config->correo_institucional) }}" placeholder="correo@ugel.gob.pe">
                </div>
                @error('correo_institucional')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-5">
                <label class="form-label">Teléfono</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="ti tabler-phone"></i></span>
                  <input type="text" name="telefono" class="form-control"
                    value="{{ old('telefono', $config->telefono) }}" placeholder="(062) 000-0000" maxlength="20">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="ti tabler-device-floppy me-1"></i>Guardar Configuración
          </button>
          <a href="{{ route('adm-configuracion') }}" class="btn btn-label-secondary">Cancelar</a>
        </div>
      </div>

      {{-- Sidebar: vista previa institucional --}}
      <div class="col-xl-4">
        <div class="card">
          <div class="card-header"><h6 class="mb-0"><i class="ti tabler-eye me-1"></i>Vista Previa</h6></div>
          <div class="card-body text-center py-5">
            <div id="previewLogo" class="mb-3">
              @if($config->logo_ruta)
                <img src="{{ Storage::url($config->logo_ruta) }}" height="80" class="rounded mb-2" alt="logo">
              @else
                <div class="avatar avatar-xl bg-label-primary rounded mx-auto mb-2">
                  <span class="avatar-initial rounded fs-2">
                    {{ strtoupper(substr($config->sigla ?? 'U', 0, 2)) }}
                  </span>
                </div>
              @endif
            </div>
            <h5 class="mb-1" id="previewNombre">{{ $config->nombre_institucion }}</h5>
            <p class="text-muted small mb-0" id="previewRegion">
              {{ implode(' — ', array_filter([$config->departamento ?: $config->region, $config->provincia, $config->distrito])) }}
            </p>
            @if($config->anio_gestion)
            <span class="badge bg-label-primary mt-2">Gestión {{ $config->anio_gestion }}</span>
            @endif
            @if($config->ubigeo)
            <div class="small text-muted mt-1"><i class="ti tabler-map-pin icon-12px me-1"></i>Ubigeo: {{ $config->ubigeo }}</div>
            @endif
          </div>
          @if($config->director || $config->correo_institucional)
          <div class="card-body border-top pt-3">
            @if($config->director)
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="ti tabler-user-tie text-muted icon-18px"></i>
              <div>
                <div class="small text-muted">Director(a)</div>
                <div class="small fw-medium">{{ $config->director }}</div>
              </div>
            </div>
            @endif
            @if($config->correo_institucional)
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="ti tabler-mail text-muted icon-18px"></i>
              <div class="small">{{ $config->correo_institucional }}</div>
            </div>
            @endif
            @if($config->telefono)
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="ti tabler-phone text-muted icon-18px"></i>
              <div class="small">{{ $config->telefono }}</div>
            </div>
            @endif
            @if($config->direccion)
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="ti tabler-map-pin text-muted icon-18px"></i>
              <div class="small">{{ $config->direccion }}</div>
            </div>
            @endif
            @if($config->sitio_web)
            <div class="d-flex align-items-center gap-2">
              <i class="ti tabler-world text-muted icon-18px"></i>
              <a href="{{ $config->sitio_web }}" target="_blank" class="small text-primary">{{ $config->sitio_web }}</a>
            </div>
            @endif
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>{{-- /tab-institucional --}}

  {{-- ============================
       TAB: SEMÁFORO
       ============================ --}}
  <div class="tab-pane fade" id="tab-semaforo">
    <div class="row g-4">
      <div class="col-xl-8">
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-warning p-2"><i class="ti tabler-traffic-lights icon-20px"></i></span>
            <h5 class="mb-0">Umbrales del Semáforo</h5>
          </div>
          <div class="card-body">
            <div class="alert alert-info d-flex gap-2 mb-4 p-3">
              <i class="ti tabler-info-circle mt-1 flex-shrink-0"></i>
              <div class="small">
                Estos umbrales aplican globalmente al <strong>Sistema de Control Interno (SCI)</strong> y al <strong>Módulo de Integridad</strong>.
                Todos los cálculos de avance, semáforo institucional, ranking y cumplimiento usan esta configuración.
              </div>
            </div>
            <div class="row g-4">
              <div class="col-md-4">
                <div class="card border border-success">
                  <div class="card-body text-center py-4">
                    <div class="mb-3">
                      <span class="badge bg-success p-3 rounded-circle">
                        <i class="ti tabler-circle-filled icon-24px"></i>
                      </span>
                    </div>
                    <h6 class="text-success mb-3">Umbral Verde</h6>
                    <div class="input-group input-group-lg justify-content-center">
                      <input type="number" name="umbral_verde"
                        class="form-control text-center fw-bold text-success @error('umbral_verde') is-invalid @enderror"
                        value="{{ old('umbral_verde', $config->umbral_verde) }}"
                        min="1" max="100" required style="max-width:100px">
                      <span class="input-group-text fw-bold">%</span>
                    </div>
                    <div class="form-text mt-2">Avance igual o mayor → Verde</div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border border-warning">
                  <div class="card-body text-center py-4">
                    <div class="mb-3">
                      <span class="badge bg-warning p-3 rounded-circle">
                        <i class="ti tabler-circle-filled icon-24px"></i>
                      </span>
                    </div>
                    <h6 class="text-warning mb-3">Umbral Amarillo</h6>
                    <div class="input-group input-group-lg justify-content-center">
                      <input type="number" name="umbral_amarillo"
                        class="form-control text-center fw-bold text-warning @error('umbral_amarillo') is-invalid @enderror"
                        value="{{ old('umbral_amarillo', $config->umbral_amarillo) }}"
                        min="1" max="100" required style="max-width:100px">
                      <span class="input-group-text fw-bold">%</span>
                    </div>
                    <div class="form-text mt-2">Entre amarillo y verde → Amarillo</div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border border-danger">
                  <div class="card-body text-center py-4">
                    <div class="mb-3">
                      <span class="badge bg-danger p-3 rounded-circle">
                        <i class="ti tabler-circle-filled icon-24px"></i>
                      </span>
                    </div>
                    <h6 class="text-danger mb-3">Umbral Rojo</h6>
                    <div class="input-group input-group-lg justify-content-center">
                      <input type="number" class="form-control text-center fw-bold text-danger"
                        value="0" disabled style="max-width:100px">
                      <span class="input-group-text fw-bold">%</span>
                    </div>
                    <div class="form-text mt-2">Por debajo de amarillo → Rojo</div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Barra visual de umbrales --}}
            <div class="mt-4">
              <h6 class="mb-2 text-muted small text-uppercase">Vista previa de rangos</h6>
              <div class="d-flex rounded overflow-hidden" style="height:36px">
                <div class="bg-danger d-flex align-items-center justify-content-center text-white small fw-bold flex-fill" id="barRojo">
                  Rojo (0–{{ $config->umbral_amarillo - 1 }}%)
                </div>
                <div class="bg-warning d-flex align-items-center justify-content-center text-white small fw-bold flex-fill" id="barAmarillo">
                  Amarillo ({{ $config->umbral_amarillo }}–{{ $config->umbral_verde - 1 }}%)
                </div>
                <div class="bg-success d-flex align-items-center justify-content-center text-white small fw-bold flex-fill" id="barVerde">
                  Verde (≥ {{ $config->umbral_verde }}%)
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="ti tabler-device-floppy me-1"></i>Guardar Umbrales
          </button>
        </div>
      </div>

      <div class="col-xl-4">
        <div class="card mb-4">
          <div class="card-header"><h6 class="mb-0"><i class="ti tabler-info-circle me-1"></i>¿Cómo funciona?</h6></div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li class="d-flex gap-2 mb-3">
                <span class="bg-success mt-1 flex-shrink-0 rounded-circle" style="width:12px;height:12px;min-width:12px"></span>
                <div>
                  <div class="fw-medium text-success small">VERDE</div>
                  <div class="text-muted small">Avance igual o superior al umbral verde. En buen camino.</div>
                </div>
              </li>
              <li class="d-flex gap-2 mb-3">
                <span class="bg-warning mt-1 flex-shrink-0 rounded-circle" style="width:12px;height:12px;min-width:12px"></span>
                <div>
                  <div class="fw-medium text-warning small">AMARILLO</div>
                  <div class="text-muted small">Entre el umbral amarillo y verde. Requiere seguimiento.</div>
                </div>
              </li>
              <li class="d-flex gap-2 mb-4">
                <span class="bg-danger mt-1 flex-shrink-0 rounded-circle" style="width:12px;height:12px;min-width:12px"></span>
                <div>
                  <div class="fw-medium text-danger small">ROJO</div>
                  <div class="text-muted small">Por debajo del umbral amarillo. Requiere acción inmediata.</div>
                </div>
              </li>
            </ul>
            <hr>
            <div class="small text-muted">
              <i class="ti tabler-refresh me-1"></i>
              <strong>Módulos que usan este semáforo:</strong>
              <ul class="mt-1 mb-0 ps-3">
                <li>Sistema de Control Interno (SCI)</li>
                <li>Módulo de Integridad</li>
                <li>Semáforo Institucional</li>
                <li>Ranking de Unidades</li>
                <li>Avance por Unidades</li>
                <li>Panel de Cumplimiento</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>{{-- /tab-semaforo --}}

  {{-- ============================
       TAB: NOTIFICACIONES
       ============================ --}}
  <div class="tab-pane fade" id="tab-notificaciones">
    <div class="row g-4">
      <div class="col-xl-8">

        {{-- Módulos a notificar --}}
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-secondary p-2"><i class="ti tabler-layout-2 icon-20px"></i></span>
            <h5 class="mb-0">Módulos con Notificaciones</h5>
          </div>
          <div class="card-body pb-2">
            <div class="d-flex align-items-start gap-3 py-3 border-bottom">
              <div class="avatar flex-shrink-0">
                <span class="avatar-initial rounded bg-label-primary">
                  <i class="ti tabler-clipboard-check icon-20px"></i>
                </span>
              </div>
              <div class="flex-grow-1 d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-0">Sistema de Control Interno (SCI)</h6>
                  <small class="text-muted">Actividades, evidencias y cumplimiento SCI</small>
                </div>
                <div class="form-check form-switch ms-3 mb-0">
                  <input class="form-check-input" type="checkbox" name="notif_modulo_sci" value="1"
                    {{ $config->notif_modulo_sci ? 'checked' : '' }}>
                </div>
              </div>
            </div>
            <div class="d-flex align-items-start gap-3 py-3">
              <div class="avatar flex-shrink-0">
                <span class="avatar-initial rounded bg-label-success">
                  <i class="ti tabler-shield-check icon-20px"></i>
                </span>
              </div>
              <div class="flex-grow-1 d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-0">Módulo de Integridad</h6>
                  <small class="text-muted">Actividades del modelo PCM — 9 componentes</small>
                </div>
                <div class="form-check form-switch ms-3 mb-0">
                  <input class="form-check-input" type="checkbox" name="notif_modulo_integridad" value="1"
                    {{ $config->notif_modulo_integridad ? 'checked' : '' }}>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Alertas de vencimiento con 3 niveles --}}
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-warning p-2"><i class="ti tabler-calendar-x icon-20px"></i></span>
            <h5 class="mb-0">Alertas de Vencimiento</h5>
          </div>
          <div class="card-body pb-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-0">Activar alertas de proximidad</h6>
                <small class="text-muted">Notificar cuando una actividad esté próxima a vencer</small>
              </div>
              <div class="form-check form-switch ms-3 mb-0">
                <input class="form-check-input" type="checkbox" name="notif_vencimiento" value="1"
                  id="switchVencimiento" {{ $config->notif_vencimiento ? 'checked' : '' }}
                  onchange="toggleField('vencimientoNiveles', this.checked)">
              </div>
            </div>

            <div id="vencimientoNiveles" class="{{ $config->notif_vencimiento ? '' : 'd-none' }}">
              <p class="text-muted small mb-3">Selecciona los niveles de anticipación para las alertas:</p>
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="card border {{ $config->notif_10dias ? 'border-warning' : '' }} h-100">
                    <div class="card-body text-center py-3">
                      <div class="avatar avatar-md bg-label-warning rounded mx-auto mb-2">
                        <i class="ti tabler-clock icon-24px"></i>
                      </div>
                      <h6 class="mb-1">10 días antes</h6>
                      <div class="small text-muted mb-3">Alerta temprana</div>
                      <div class="form-check form-switch d-flex justify-content-center mb-0">
                        <input class="form-check-input" type="checkbox" name="notif_10dias" value="1"
                          id="switch10dias" {{ $config->notif_10dias ? 'checked' : '' }}
                          onchange="this.closest('.card').classList.toggle('border-warning', this.checked)">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card border {{ $config->notif_5dias ? 'border-warning' : '' }} h-100">
                    <div class="card-body text-center py-3">
                      <div class="avatar avatar-md bg-label-warning rounded mx-auto mb-2">
                        <i class="ti tabler-clock-hour-4 icon-24px"></i>
                      </div>
                      <h6 class="mb-1">5 días antes</h6>
                      <div class="small text-muted mb-3">Alerta media</div>
                      <div class="form-check form-switch d-flex justify-content-center mb-0">
                        <input class="form-check-input" type="checkbox" name="notif_5dias" value="1"
                          id="switch5dias" {{ $config->notif_5dias ? 'checked' : '' }}
                          onchange="this.closest('.card').classList.toggle('border-warning', this.checked)">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card border {{ $config->notif_1dia ? 'border-danger' : '' }} h-100">
                    <div class="card-body text-center py-3">
                      <div class="avatar avatar-md bg-label-danger rounded mx-auto mb-2">
                        <i class="ti tabler-clock-hour-12 icon-24px"></i>
                      </div>
                      <h6 class="mb-1">1 día antes</h6>
                      <div class="small text-muted mb-3">Alerta urgente</div>
                      <div class="form-check form-switch d-flex justify-content-center mb-0">
                        <input class="form-check-input" type="checkbox" name="notif_1dia" value="1"
                          id="switch1dia" {{ $config->notif_1dia ? 'checked' : '' }}
                          onchange="this.closest('.card').classList.toggle('border-danger', this.checked)">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Avance bajo --}}
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-danger p-2"><i class="ti tabler-trending-down icon-20px"></i></span>
            <h5 class="mb-0">Alertas de Avance Bajo</h5>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-0">Activar alertas de avance bajo</h6>
                <small class="text-muted">Notificar cuando el avance de una actividad sea insuficiente</small>
              </div>
              <div class="form-check form-switch ms-3 mb-0">
                <input class="form-check-input" type="checkbox" name="notif_avance_bajo" value="1"
                  id="switchAvanceBajo" {{ $config->notif_avance_bajo ? 'checked' : '' }}
                  onchange="toggleField('avanceBajoUmbral', this.checked)">
              </div>
            </div>
            <div id="avanceBajoUmbral" class="{{ $config->notif_avance_bajo ? '' : 'd-none' }}">
              <label class="form-label small">Umbral de avance bajo (%)</label>
              <div class="input-group" style="max-width:220px">
                <span class="input-group-text"><i class="ti tabler-percentage"></i></span>
                <input type="number" name="notif_umbral_avance" class="form-control"
                  value="{{ old('notif_umbral_avance', $config->notif_umbral_avance) }}" min="1" max="100">
                <span class="input-group-text">%</span>
              </div>
              <div class="form-text">Actividades con avance menor a este % generan alerta</div>
            </div>
          </div>
        </div>

        {{-- Correo electrónico --}}
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-info p-2"><i class="ti tabler-mail icon-20px"></i></span>
            <h5 class="mb-0">Envío por Correo Electrónico</h5>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h6 class="mb-0">Activar envío de correos</h6>
                <small class="text-muted">Las alertas se enviarán al responsable de cada actividad y al correo institucional</small>
              </div>
              <div class="form-check form-switch ms-3 mb-0">
                <input class="form-check-input" type="checkbox" name="notif_email" value="1"
                  id="switchEmail" {{ $config->notif_email ? 'checked' : '' }}
                  onchange="toggleField('smtpConfig', this.checked)">
              </div>
            </div>

            <div id="smtpConfig" class="{{ $config->notif_email ? '' : 'd-none' }}">
              <div class="alert alert-warning d-flex gap-2 p-3 mb-4">
                <i class="ti tabler-alert-triangle mt-1 flex-shrink-0"></i>
                <div class="small">
                  Configura el servidor SMTP. Si dejas estos campos vacíos, se usará la configuración del archivo <code>.env</code>.
                  La contraseña solo es necesaria si la cambias.
                </div>
              </div>
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label">Servidor SMTP</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="ti tabler-server"></i></span>
                    <input type="text" name="mail_host" class="form-control"
                      value="{{ old('mail_host', $config->mail_host) }}"
                      placeholder="smtp.gmail.com / smtp.office365.com">
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Puerto</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="ti tabler-plug"></i></span>
                    <input type="number" name="mail_port" class="form-control"
                      value="{{ old('mail_port', $config->mail_port ?? 587) }}"
                      placeholder="587">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Usuario / Correo remitente</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="ti tabler-at"></i></span>
                    <input type="text" name="mail_username" class="form-control"
                      value="{{ old('mail_username', $config->mail_username) }}"
                      placeholder="correo@gmail.com">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Contraseña</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="ti tabler-lock"></i></span>
                    <input type="password" name="mail_password" class="form-control"
                      placeholder="{{ $config->mail_password ? '••••••••••••' : 'Ingresar contraseña' }}"
                      autocomplete="new-password">
                  </div>
                  <div class="form-text">Dejar vacío para mantener la contraseña actual</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Cifrado</label>
                  <select name="mail_encryption" class="form-select">
                    @foreach(['tls'=>'TLS (recomendado)', 'ssl'=>'SSL', 'none'=>'Sin cifrado'] as $val => $lbl)
                      <option value="{{ $val }}" {{ old('mail_encryption', $config->mail_encryption ?? 'tls') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-8">
                  <label class="form-label">Nombre del remitente</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="ti tabler-pencil"></i></span>
                    <input type="text" name="mail_from_name" class="form-control"
                      value="{{ old('mail_from_name', $config->mail_from_name ?? $config->nombre_institucion) }}"
                      placeholder="PULSO UGEL — Sistema de Control Interno">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="ti tabler-device-floppy me-1"></i>Guardar Notificaciones
          </button>
        </div>
      </div>

      <div class="col-xl-4">
        <div class="card mb-4">
          <div class="card-header"><h6 class="mb-0"><i class="ti tabler-info-circle me-1"></i>Sobre las Notificaciones</h6></div>
          <div class="card-body">
            <div class="small text-muted mb-3">
              Las alertas se generan automáticamente por el sistema y aplican a ambos módulos según la configuración.
            </div>
            <ul class="list-unstyled mb-0">
              <li class="d-flex gap-2 mb-2">
                <i class="ti tabler-point-filled text-warning mt-1 flex-shrink-0"></i>
                <div class="small"><strong>Vencimiento:</strong> Cuando una actividad ya venció sin completarse</div>
              </li>
              <li class="d-flex gap-2 mb-2">
                <i class="ti tabler-point-filled text-warning mt-1 flex-shrink-0"></i>
                <div class="small"><strong>10 días:</strong> Alerta temprana, prioridad baja</div>
              </li>
              <li class="d-flex gap-2 mb-2">
                <i class="ti tabler-point-filled text-warning mt-1 flex-shrink-0"></i>
                <div class="small"><strong>5 días:</strong> Alerta media, prioridad media</div>
              </li>
              <li class="d-flex gap-2 mb-2">
                <i class="ti tabler-point-filled text-danger mt-1 flex-shrink-0"></i>
                <div class="small"><strong>1 día:</strong> Alerta urgente, prioridad alta</div>
              </li>
              <li class="d-flex gap-2 mb-2">
                <i class="ti tabler-point-filled text-danger mt-1 flex-shrink-0"></i>
                <div class="small"><strong>Avance bajo:</strong> Actividades sin progreso después de 7 días</div>
              </li>
              <li class="d-flex gap-2">
                <i class="ti tabler-point-filled text-info mt-1 flex-shrink-0"></i>
                <div class="small"><strong>Evidencia faltante:</strong> Actividades en proceso sin documentos</div>
              </li>
            </ul>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h6 class="mb-0"><i class="ti tabler-mail-check me-1"></i>Correo Institucional</h6></div>
          <div class="card-body">
            @if($config->correo_institucional)
              <div class="d-flex align-items-center gap-2">
                <i class="ti tabler-send text-primary"></i>
                <span class="small">{{ $config->correo_institucional }}</span>
              </div>
              <div class="small text-muted mt-1">Destino de alertas sin responsable asignado</div>
            @else
              <div class="alert alert-warning p-2 mb-0">
                <i class="ti tabler-alert-triangle me-1"></i>
                <span class="small">Sin correo institucional. Configúralo en la pestaña <strong>Datos Institucionales</strong>.</span>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>{{-- /tab-notificaciones --}}

</div>{{-- /tab-content --}}
</form>


{{-- ============================
     TAB: ACCESOS RÁPIDOS (fuera del form)
     ============================ --}}
<div id="tabAccesosContent" class="d-none">

  {{-- SCI --}}
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="badge bg-label-primary p-2"><i class="ti tabler-clipboard-check icon-16px"></i></span>
    <h6 class="mb-0 text-primary">Sistema de Control Interno (SCI)</h6>
    <hr class="flex-grow-1 my-0 ms-2">
  </div>
  <div class="row g-4 mb-5">
    @php
    $accesos_sci = [
      ['route'=>'sci-control-interno',  'icon'=>'tabler-clipboard-check', 'color'=>'primary',   'title'=>'Control Interno',    'desc'=>'Actividades y compromisos SCI'],
      ['route'=>'sci-evidencias',       'icon'=>'tabler-files',           'color'=>'info',       'title'=>'Evidencias',         'desc'=>'Documentos y registros de avance'],
      ['route'=>'cumplimiento.panel',   'icon'=>'tabler-chart-dots',      'color'=>'secondary',  'title'=>'Cumplimiento SCI',   'desc'=>'Panel ejecutivo de cumplimiento'],
      ['route'=>'mon-avance-unidades',  'icon'=>'tabler-building-community','color'=>'warning',  'title'=>'Avance por Unidades','desc'=>'Seguimiento por área orgánica'],
      ['route'=>'mon-ranking-unidades', 'icon'=>'tabler-podium',          'color'=>'success',    'title'=>'Ranking de Unidades','desc'=>'Clasificación general por avance'],
      ['route'=>'adm-sci-estructura',   'icon'=>'tabler-layout-grid',     'color'=>'secondary',  'title'=>'Estructura SCI',     'desc'=>'Administrar ejes, componentes y preguntas'],
    ];
    @endphp
    @foreach($accesos_sci as $acc)
    <div class="col-md-6 col-xl-2">
      <a href="{{ route($acc['route']) }}" class="card card-hover-shadow text-decoration-none h-100">
        <div class="card-body text-center py-4">
          <div class="avatar avatar-md bg-label-{{ $acc['color'] }} rounded mx-auto mb-2">
            <i class="ti {{ $acc['icon'] }} icon-24px"></i>
          </div>
          <h6 class="mb-1 text-heading" style="font-size:12px">{{ $acc['title'] }}</h6>
          <p class="text-muted mb-0" style="font-size:11px">{{ $acc['desc'] }}</p>
        </div>
      </a>
    </div>
    @endforeach
  </div>

  {{-- Integridad --}}
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="badge bg-label-success p-2"><i class="ti tabler-shield-check icon-16px"></i></span>
    <h6 class="mb-0 text-success">Módulo de Integridad</h6>
    <hr class="flex-grow-1 my-0 ms-2">
  </div>
  <div class="row g-4 mb-5">
    @php
    $accesos_integridad = [
      ['route'=>'sci-modelo-integridad',     'icon'=>'tabler-shield-check',    'color'=>'success', 'title'=>'Modelo de Integridad',   'desc'=>'PCM — 9 componentes'],
      ['route'=>'sci-semaforo',              'icon'=>'tabler-traffic-lights',  'color'=>'warning', 'title'=>'Semáforo Institucional', 'desc'=>'Estado en tiempo real'],
      ['route'=>'mon-alertas',               'icon'=>'tabler-bell',            'color'=>'danger',  'title'=>'Alertas',                'desc'=>'Notificaciones y pendientes'],
      ['route'=>'buenas-practicas',          'icon'=>'tabler-rosette-discount-check','color'=>'info','title'=>'Buenas Prácticas',     'desc'=>'Registro y seguimiento'],
      ['route'=>'recomendaciones',           'icon'=>'tabler-message-report',  'color'=>'secondary','title'=>'Recomendaciones',        'desc'=>'Observaciones institucionales'],
      ['route'=>'adm-integridad-estructura', 'icon'=>'tabler-layers-intersect','color'=>'success', 'title'=>'Estructura Integridad',  'desc'=>'Etapas, componentes y preguntas PCM'],
    ];
    @endphp
    @foreach($accesos_integridad as $acc)
    <div class="col-md-6 col-xl-2">
      <a href="{{ route($acc['route']) }}" class="card card-hover-shadow text-decoration-none h-100">
        <div class="card-body text-center py-4">
          <div class="avatar avatar-md bg-label-{{ $acc['color'] }} rounded mx-auto mb-2">
            <i class="ti {{ $acc['icon'] }} icon-24px"></i>
          </div>
          <h6 class="mb-1 text-heading" style="font-size:12px">{{ $acc['title'] }}</h6>
          <p class="text-muted mb-0" style="font-size:11px">{{ $acc['desc'] }}</p>
        </div>
      </a>
    </div>
    @endforeach
  </div>

  {{-- Mantenimiento --}}
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="badge bg-label-danger p-2"><i class="ti tabler-tool icon-16px"></i></span>
    <h6 class="mb-0 text-danger">Mantenimiento del Sistema</h6>
    <hr class="flex-grow-1 my-0 ms-2">
  </div>
  <div class="card border border-danger-subtle mb-5">
    <div class="card-body">
      <div class="row align-items-center g-3">
        <div class="col-md-8">
          <div class="d-flex gap-3 align-items-start">
            <div class="avatar avatar-md bg-label-danger rounded flex-shrink-0">
              <i class="ti tabler-refresh icon-24px"></i>
            </div>
            <div>
              <h6 class="mb-1">Limpiar Caché del Sistema</h6>
              <p class="text-muted small mb-0">Limpia la caché de rutas, vistas, configuración y aplicación. Úsalo cuando el sistema muestre errores de rutas o cambios que no se reflejan correctamente.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 text-md-end">
          <form method="POST" action="{{ route('adm-configuracion.cache') }}" id="formClearCache">
            @csrf
            <button type="submit" class="btn btn-danger"
              onclick="return confirm('¿Limpiar toda la caché del sistema? El sistema tardará un momento en regenerarla.')">
              <i class="ti tabler-refresh me-1"></i>Limpiar Caché
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Administración --}}
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="badge bg-label-secondary p-2"><i class="ti tabler-settings icon-16px"></i></span>
    <h6 class="mb-0 text-secondary">Administración</h6>
    <hr class="flex-grow-1 my-0 ms-2">
  </div>
  <div class="row g-4">
    @php
    $accesos_adm = [
      ['route'=>'adm-usuarios',  'icon'=>'tabler-users',      'color'=>'primary',   'title'=>'Usuarios',          'desc'=>'Cuentas y accesos'],
      ['route'=>'adm-roles',     'icon'=>'tabler-user-shield','color'=>'warning',   'title'=>'Roles',             'desc'=>'Perfiles de usuario'],
      ['route'=>'adm-permisos',  'icon'=>'tabler-lock',       'color'=>'danger',    'title'=>'Permisos',          'desc'=>'Control de acceso'],
      ['route'=>'adm-unidades',  'icon'=>'tabler-sitemap',    'color'=>'info',      'title'=>'Unidades Orgánicas','desc'=>'Estructura institucional'],
      ['route'=>'rep-reportes',  'icon'=>'tabler-chart-bar',  'color'=>'secondary', 'title'=>'Reportes',          'desc'=>'Análisis y exportación'],
      ['route'=>'slider-landing.index','icon'=>'tabler-slideshow','color'=>'secondary','title'=>'Slider Landing', 'desc'=>'Slides de la página principal'],
    ];
    @endphp
    @foreach($accesos_adm as $acc)
    <div class="col-md-6 col-xl-2">
      <a href="{{ route($acc['route']) }}" class="card card-hover-shadow text-decoration-none h-100">
        <div class="card-body text-center py-4">
          <div class="avatar avatar-md bg-label-{{ $acc['color'] }} rounded mx-auto mb-2">
            <i class="ti {{ $acc['icon'] }} icon-24px"></i>
          </div>
          <h6 class="mb-1 text-heading" style="font-size:12px">{{ $acc['title'] }}</h6>
          <p class="text-muted mb-0" style="font-size:11px">{{ $acc['desc'] }}</p>
        </div>
      </a>
    </div>
    @endforeach
  </div>

</div>


@endsection

@section('page-script')
<script>
window.addEventListener('load', function () {
const $ = window.jQuery;

  // ── Helpers ───────────────────────────────────────────────────────────────
  function avatarHtml(foto, nombre, size) {
    if (foto) {
      return `<img src="${foto}" class="rounded-circle me-2" style="width:${size}px;height:${size}px;object-fit:cover">`;
    }
    const ini = nombre ? nombre.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase() : '?';
    return `<div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold me-2"
      style="width:${size}px;height:${size}px;background:linear-gradient(135deg,var(--bs-primary),rgba(var(--bs-primary-rgb),.6));color:#fff;font-size:${Math.round(size/3)}px;flex-shrink:0">${ini}</div>`;
  }

  function formatUsuarioOption(option) {
    if (!option.id) return $(`<span class="text-muted">${option.text}</span>`);
    const el = option.element;
    const cargo  = el.dataset.cargo  || '';
    const unidad = el.dataset.unidad || '';
    const email  = el.dataset.email  || '';
    const foto   = el.dataset.foto   || '';
    return $(`<div class="d-flex align-items-center gap-2 py-1">
      ${avatarHtml(foto, option.text, 36)}
      <div>
        <div class="fw-semibold" style="font-size:13px">${option.text}</div>
        <div class="text-muted" style="font-size:11px">${cargo}${unidad ? ' · ' + unidad : ''}</div>
        <div class="text-muted" style="font-size:11px">${email}</div>
      </div>
    </div>`);
  }

  function formatUsuarioSelected(option) {
    if (!option.id) return option.text;
    const el = option.element;
    const cargo = el.dataset.cargo || '';
    const foto  = el.dataset.foto  || '';
    return $(`<div class="d-flex align-items-center gap-2">
      ${avatarHtml(foto, option.text, 24)}
      <span>${option.text}</span>
      ${cargo ? `<span class="badge bg-label-secondary ms-1" style="font-size:10px">${cargo}</span>` : ''}
    </div>`);
  }

  function actualizarPreview(previewId, data) {
    $(`#${previewId} [id$="Nombre"]`).text(data.nombre || '');
    $(`#${previewId} [id$="Cargo"]`).text(data.cargo || '');
    $(`#${previewId} [id$="Unidad"]`).text(data.unidad || '');
    $(`#${previewId} [id$="Email"]`).text(data.email || '');
    $(`#${previewId} [id$="Foto"]`).html(avatarHtml(data.foto, data.nombre, 48));
  }

  // ── Select2 autoridades ───────────────────────────────────────────────────
  function initSelect2Autoridad(selectId, previewId) {
    $(`#${selectId}`).select2({
      placeholder: $(`#${selectId}`).data('placeholder'),
      allowClear: true,
      templateResult: formatUsuarioOption,
      templateSelection: formatUsuarioSelected,
      dropdownParent: $(`#${selectId}`).closest('.card-body'),
    }).on('select2:select', function (e) {
      const el = e.params.data.element;
      actualizarPreview(previewId, {
        nombre: e.params.data.text,
        cargo:  el.dataset.cargo  || '',
        unidad: el.dataset.unidad || '',
        email:  el.dataset.email  || '',
        foto:   el.dataset.foto   || '',
      });
      $(`#${previewId}`).removeClass('d-none');
    }).on('select2:unselect', function () {
      $(`#${previewId}`).addClass('d-none');
    });
  }

  initSelect2Autoridad('selectDirector',       'previewDirector');
  initSelect2Autoridad('selectCoordinadorSci', 'previewCoordinador');

  // Poblar avatar en previews ya visibles al cargar
  ['Director', 'Coordinador'].forEach(function (key) {
    const previewId = 'preview' + key;
    const el = document.getElementById(previewId);
    if (el && !el.classList.contains('d-none')) {
      const fotoEl = document.getElementById('preview' + key + 'Foto');
      const nombre = document.getElementById('preview' + key + 'Nombre')?.textContent || '';
      if (fotoEl) fotoEl.innerHTML = avatarHtml('', nombre, 48);
    }
  });

  // ── Logo / Favicon previews ───────────────────────────────────────────────
  $('#logoUpload').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
      const preview = document.getElementById('logoPreview');
      if (preview.tagName === 'IMG') {
        preview.src = ev.target.result;
      } else {
        const img = document.createElement('img');
        img.src = ev.target.result;
        img.id = 'logoPreview';
        img.className = 'rounded border';
        img.style = 'width:120px;height:120px;object-fit:contain;background:#f8f9fa';
        img.alt = 'Logo institucional';
        preview.replaceWith(img);
      }
    };
    reader.readAsDataURL(file);
  });

  $('#faviconUpload').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
      const preview = document.getElementById('faviconPreview');
      if (preview.tagName === 'IMG') {
        preview.src = ev.target.result;
      } else {
        const img = document.createElement('img');
        img.src = ev.target.result;
        img.id = 'faviconPreview';
        img.className = 'rounded border';
        img.style = 'width:64px;height:64px;object-fit:contain;background:#f8f9fa';
        img.alt = 'Favicon';
        preview.replaceWith(img);
      }
    };
    reader.readAsDataURL(file);
  });

  $('#btnRemoveFavicon').on('click', function () {
    $('#removeFavicon').val('1');
    const placeholder = $('<div id="faviconPreview" class="rounded border d-flex align-items-center justify-content-center bg-label-secondary" style="width:64px;height:64px"><i class="ti tabler-browser icon-24px text-muted"></i></div>');
    $('#faviconPreview').replaceWith(placeholder);
    $(this).prop('disabled', true);
  });

  $('#btnRemoveLogo').on('click', function () {
    $('#removeLogo').val('1');
    const placeholder = $('<div id="logoPreview" class="rounded border d-flex align-items-center justify-content-center bg-label-secondary" style="width:120px;height:120px"><i class="ti tabler-building icon-40px text-muted"></i></div>');
    $('#logoPreview').replaceWith(placeholder);
    $(this).prop('disabled', true);
  });

  // ── Live preview nombre institución ──────────────────────────────────────
  $('[name="nombre_institucion"]').on('input', function () {
    $('#previewNombre').text(this.value || '—');
  });

  // ── Semáforo barra visual ─────────────────────────────────────────────────
  function updateSemaforoBar() {
    const verde    = parseInt($('[name="umbral_verde"]').val() || 75);
    const amarillo = parseInt($('[name="umbral_amarillo"]').val() || 50);
    $('#barRojo').text(`Rojo (0–${amarillo - 1}%)`);
    $('#barAmarillo').text(`Amarillo (${amarillo}–${verde - 1}%)`);
    $('#barVerde').text(`Verde (≥ ${verde}%)`);
  }
  $('[name="umbral_verde"], [name="umbral_amarillo"]').on('input', updateSemaforoBar);

  // ── Tabs accesos rápidos (fuera del form) ─────────────────────────────────
  $('#configTabs .nav-link').on('shown.bs.tab', function (e) {
    const target = $(e.target).attr('href');
    $('#tabAccesosContent').toggleClass('d-none', target !== '#tab-accesos');
  });

  // ── Activar tab por sesión ────────────────────────────────────────────────
  @if(session('_tab'))
    const tabEl = document.querySelector(`#configTabs [href="#{{ session('_tab') }}"]`);
    if (tabEl) new bootstrap.Tab(tabEl).show();
  @endif

}); // fin window.load

// toggleField global — usado inline en onchange de los switches
function toggleField(id, show) {
  const el = document.getElementById(id);
  if (el) el.classList.toggle('d-none', !show);
}
</script>
@endsection
