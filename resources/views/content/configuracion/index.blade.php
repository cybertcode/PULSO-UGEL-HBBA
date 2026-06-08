@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Configuración del Sistema')

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
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Director(a)</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="ti tabler-user-tie"></i></span>
                  <input type="text" name="director" class="form-control"
                    value="{{ old('director', $config->director) }}" placeholder="Nombres y apellidos completos">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Coordinador(a) SCI</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="ti tabler-user-check"></i></span>
                  <input type="text" name="coordinador_sci" class="form-control"
                    value="{{ old('coordinador_sci', $config->coordinador_sci) }}" placeholder="Nombres y apellidos completos">
                </div>
              </div>
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
            <p class="text-muted mb-4">Define los porcentajes de avance que determinan el color del semáforo para cada actividad.</p>
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
        <div class="card">
          <div class="card-header"><h6 class="mb-0"><i class="ti tabler-info-circle me-1"></i>¿Cómo funciona?</h6></div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li class="d-flex gap-2 mb-3">
                <span class="badge bg-success mt-1 flex-shrink-0" style="width:12px;height:12px;border-radius:50%"></span>
                <div>
                  <div class="fw-medium text-success small">VERDE</div>
                  <div class="text-muted small">La actividad tiene un avance igual o superior al umbral verde. Está en buen camino.</div>
                </div>
              </li>
              <li class="d-flex gap-2 mb-3">
                <span class="badge bg-warning mt-1 flex-shrink-0" style="width:12px;height:12px;border-radius:50%"></span>
                <div>
                  <div class="fw-medium text-warning small">AMARILLO</div>
                  <div class="text-muted small">El avance está entre el umbral amarillo y el verde. Requiere seguimiento.</div>
                </div>
              </li>
              <li class="d-flex gap-2">
                <span class="badge bg-danger mt-1 flex-shrink-0" style="width:12px;height:12px;border-radius:50%"></span>
                <div>
                  <div class="fw-medium text-danger small">ROJO</div>
                  <div class="text-muted small">El avance está por debajo del umbral amarillo. Requiere acción inmediata.</div>
                </div>
              </li>
            </ul>
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
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <span class="badge bg-label-primary p-2"><i class="ti tabler-bell icon-20px"></i></span>
            <h5 class="mb-0">Alertas y Notificaciones</h5>
          </div>
          <div class="card-body pb-2">

            {{-- Alertas de vencimiento --}}
            <div class="d-flex align-items-start gap-3 py-3 border-bottom">
              <div class="avatar flex-shrink-0">
                <span class="avatar-initial rounded bg-label-warning">
                  <i class="ti tabler-calendar-x icon-20px"></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div>
                    <h6 class="mb-0">Alertas de Vencimiento</h6>
                    <small class="text-muted">Notificar cuando una actividad esté próxima a vencer</small>
                  </div>
                  <div class="form-check form-switch ms-3 mb-0">
                    <input class="form-check-input" type="checkbox" name="notif_vencimiento" value="1"
                      id="switchVencimiento" {{ $config->notif_vencimiento ? 'checked' : '' }}
                      onchange="toggleField('vencimientoDias', this.checked)">
                  </div>
                </div>
                <div id="vencimientoDias" class="{{ $config->notif_vencimiento ? '' : 'd-none' }} mt-2">
                  <label class="form-label small">Días de anticipación</label>
                  <div class="input-group" style="max-width:200px">
                    <span class="input-group-text"><i class="ti tabler-clock"></i></span>
                    <input type="number" name="notif_dias_anticipacion" class="form-control"
                      value="{{ old('notif_dias_anticipacion', $config->notif_dias_anticipacion) }}" min="1" max="30">
                    <span class="input-group-text">días</span>
                  </div>
                </div>
              </div>
            </div>

            {{-- Alertas de avance bajo --}}
            <div class="d-flex align-items-start gap-3 py-3 border-bottom">
              <div class="avatar flex-shrink-0">
                <span class="avatar-initial rounded bg-label-danger">
                  <i class="ti tabler-trending-down icon-20px"></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div>
                    <h6 class="mb-0">Alertas de Avance Bajo</h6>
                    <small class="text-muted">Notificar cuando el avance de una actividad sea insuficiente</small>
                  </div>
                  <div class="form-check form-switch ms-3 mb-0">
                    <input class="form-check-input" type="checkbox" name="notif_avance_bajo" value="1"
                      id="switchAvanceBajo" {{ $config->notif_avance_bajo ? 'checked' : '' }}
                      onchange="toggleField('avanceBajoUmbral', this.checked)">
                  </div>
                </div>
                <div id="avanceBajoUmbral" class="{{ $config->notif_avance_bajo ? '' : 'd-none' }} mt-2">
                  <label class="form-label small">Umbral de avance bajo (%)</label>
                  <div class="input-group" style="max-width:200px">
                    <span class="input-group-text"><i class="ti tabler-percentage"></i></span>
                    <input type="number" name="notif_umbral_avance" class="form-control"
                      value="{{ old('notif_umbral_avance', $config->notif_umbral_avance) }}" min="1" max="100">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>
            </div>

            {{-- Notificaciones por correo --}}
            <div class="d-flex align-items-start gap-3 py-3">
              <div class="avatar flex-shrink-0">
                <span class="avatar-initial rounded bg-label-info">
                  <i class="ti tabler-mail icon-20px"></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="mb-0">Notificaciones por Correo</h6>
                    <small class="text-muted">Enviar alertas al correo institucional configurado</small>
                    @if($config->correo_institucional)
                      <div class="small text-muted mt-1"><i class="ti tabler-send icon-14px me-1"></i>{{ $config->correo_institucional }}</div>
                    @else
                      <div class="small text-warning mt-1"><i class="ti tabler-alert-triangle icon-14px me-1"></i>Sin correo institucional configurado</div>
                    @endif
                  </div>
                  <div class="form-check form-switch ms-3 mb-0">
                    <input class="form-check-input" type="checkbox" name="notif_email" value="1"
                      {{ $config->notif_email ? 'checked' : '' }}>
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
        <div class="card">
          <div class="card-header"><h6 class="mb-0"><i class="ti tabler-info-circle me-1"></i>Sobre las Notificaciones</h6></div>
          <div class="card-body">
            <div class="alert alert-info mb-0 p-3">
              <i class="ti tabler-info-circle me-2"></i>
              Las notificaciones se generan automáticamente según los parámetros configurados y son visibles en el panel de alertas del sistema.
            </div>
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
  <div class="row g-4">
    @php
    $accesos = [
      ['route'=>'adm-usuarios','icon'=>'tabler-users','color'=>'primary','title'=>'Gestión de Usuarios','desc'=>'Crear, editar y gestionar usuarios del sistema'],
      ['route'=>'adm-roles','icon'=>'tabler-user-shield','color'=>'success','title'=>'Roles del Sistema','desc'=>'Definir roles y sus permisos asociados'],
      ['route'=>'adm-permisos','icon'=>'tabler-lock','color'=>'warning','title'=>'Permisos','desc'=>'Gestionar permisos granulares del sistema'],
      ['route'=>'sci-control-interno','icon'=>'tabler-clipboard-list','color'=>'info','title'=>'Control Interno','desc'=>'Módulo SCI - Componentes y actividades'],
    ];
    @endphp
    @foreach($accesos as $acc)
    <div class="col-md-6 col-xl-3">
      <a href="{{ route($acc['route']) }}" class="card card-hover-shadow text-decoration-none h-100">
        <div class="card-body text-center py-5">
          <div class="avatar avatar-lg bg-label-{{ $acc['color'] }} rounded mx-auto mb-3">
            <i class="ti {{ $acc['icon'] }} icon-30px"></i>
          </div>
          <h6 class="mb-1 text-heading">{{ $acc['title'] }}</h6>
          <p class="text-muted small mb-0">{{ $acc['desc'] }}</p>
        </div>
      </a>
    </div>
    @endforeach
  </div>
</div>


@endsection

@section('page-script')
<script>
// Mostrar/ocultar campo dependiente del switch
function toggleField(id, show) {
  const el = document.getElementById(id);
  if (el) el.classList.toggle('d-none', !show);
}

// Preview del logo al seleccionar archivo
document.getElementById('logoUpload')?.addEventListener('change', function (e) {
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

// Preview del favicon al seleccionar archivo
document.getElementById('faviconUpload')?.addEventListener('change', function (e) {
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

// Botón eliminar favicon
document.getElementById('btnRemoveFavicon')?.addEventListener('click', function () {
  document.getElementById('removeFavicon').value = '1';
  const preview = document.getElementById('faviconPreview');
  const placeholder = document.createElement('div');
  placeholder.id = 'faviconPreview';
  placeholder.className = 'rounded border d-flex align-items-center justify-content-center bg-label-secondary';
  placeholder.style = 'width:64px;height:64px';
  placeholder.innerHTML = '<i class="ti tabler-browser icon-24px text-muted"></i>';
  preview.replaceWith(placeholder);
  this.disabled = true;
});

// Botón eliminar logo
document.getElementById('btnRemoveLogo')?.addEventListener('click', function () {
  document.getElementById('removeLogo').value = '1';
  const preview = document.getElementById('logoPreview');
  const placeholder = document.createElement('div');
  placeholder.id = 'logoPreview';
  placeholder.className = 'rounded border d-flex align-items-center justify-content-center bg-label-secondary';
  placeholder.style = 'width:120px;height:120px';
  placeholder.innerHTML = '<i class="ti tabler-building icon-40px text-muted"></i>';
  preview.replaceWith(placeholder);
  this.disabled = true;
});

// Live preview del nombre e institución
document.querySelector('[name="nombre_institucion"]')?.addEventListener('input', function () {
  const el = document.getElementById('previewNombre');
  if (el) el.textContent = this.value || '—';
});

// Vista previa de rangos del semáforo
function updateSemaforoBar() {
  const verde    = parseInt(document.querySelector('[name="umbral_verde"]')?.value || 75);
  const amarillo = parseInt(document.querySelector('[name="umbral_amarillo"]')?.value || 50);
  const rojo     = document.getElementById('barRojo');
  const am       = document.getElementById('barAmarillo');
  const vd       = document.getElementById('barVerde');
  if (rojo) rojo.textContent     = `Rojo (0–${amarillo - 1}%)`;
  if (am)   am.textContent       = `Amarillo (${amarillo}–${verde - 1}%)`;
  if (vd)   vd.textContent       = `Verde (≥ ${verde}%)`;
}
document.querySelector('[name="umbral_verde"]')?.addEventListener('input', updateSemaforoBar);
document.querySelector('[name="umbral_amarillo"]')?.addEventListener('input', updateSemaforoBar);

// Tabs con contenido fuera del form principal (accesos)
document.querySelectorAll('#configTabs .nav-link').forEach(link => {
  link.addEventListener('shown.bs.tab', function (e) {
    const target = e.target.getAttribute('href');
    document.getElementById('tabAccesosContent')?.classList.toggle('d-none', target !== '#tab-accesos');
  });
});

// Activar tab correcto si hay sesión de éxito/error
@if(session('_tab'))
  const tabEl = document.querySelector(`#configTabs [href="#{{ session('_tab') }}"]`);
  if (tabEl) new bootstrap.Tab(tabEl).show();
@endif
</script>
@endsection
