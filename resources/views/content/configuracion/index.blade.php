@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Configuración - PULSO UGEL')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4"><i class="ti tabler-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="mb-4">
  <h4 class="mb-1">Configuración del Sistema</h4>
  <p class="mb-0 text-muted">Parámetros institucionales y umbrales del semáforo</p>
</div>

<div class="row g-4">
  {{-- Formulario principal --}}
  <div class="col-xl-8">
    <form method="POST" action="{{ route('adm-configuracion.update') }}" enctype="multipart/form-data">
      @csrf @method('PUT')

      {{-- Datos institucionales --}}
      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="ti tabler-building me-2"></i>Datos Institucionales</h5></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Nombre de la Institución <span class="text-danger">*</span></label>
              <input type="text" name="nombre_institucion" class="form-control" value="{{ $config->nombre_institucion }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Sigla <span class="text-danger">*</span></label>
              <input type="text" name="sigla" class="form-control" value="{{ $config->sigla }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Código UGEL</label>
              <input type="text" name="ugel_codigo" class="form-control" value="{{ $config->ugel_codigo }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Región</label>
              <input type="text" name="region" class="form-control" value="{{ $config->region }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Provincia</label>
              <input type="text" name="provincia" class="form-control" value="{{ $config->provincia }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Director(a)</label>
              <input type="text" name="director" class="form-control" value="{{ $config->director }}" placeholder="Nombres y apellidos">
            </div>
            <div class="col-md-6">
              <label class="form-label">Coordinador(a) SCI</label>
              <input type="text" name="coordinador_sci" class="form-control" value="{{ $config->coordinador_sci }}" placeholder="Nombres y apellidos">
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo Institucional</label>
              <input type="email" name="correo_institucional" class="form-control" value="{{ $config->correo_institucional }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Teléfono</label>
              <input type="text" name="telefono" class="form-control" value="{{ $config->telefono }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Año de Gestión</label>
              <input type="number" name="anio_gestion" class="form-control" value="{{ $config->anio_gestion }}" min="2020" max="2099">
            </div>
            <div class="col-md-6">
              <label class="form-label">Logo Institucional</label>
              @if($config->logo_ruta)
              <div class="mb-2"><img src="{{ Storage::url($config->logo_ruta) }}" height="50" class="rounded border"></div>
              @endif
              <input type="file" name="logo" class="form-control" accept="image/*">
            </div>
          </div>
        </div>
      </div>

      {{-- Umbrales semáforo --}}
      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="ti tabler-traffic-lights me-2"></i>Umbrales del Semáforo</h5></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-success fw-medium">Umbral Verde (≥ %)</label>
              <div class="input-group">
                <input type="number" name="umbral_verde" class="form-control" value="{{ $config->umbral_verde }}" min="1" max="100" required>
                <span class="input-group-text text-success"><i class="ti tabler-circle-filled"></i></span>
              </div>
              <div class="form-text">Actividades con avance igual o mayor a este % se marcan en verde.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label text-warning fw-medium">Umbral Amarillo (≥ %)</label>
              <div class="input-group">
                <input type="number" name="umbral_amarillo" class="form-control" value="{{ $config->umbral_amarillo }}" min="1" max="100" required>
                <span class="input-group-text text-warning"><i class="ti tabler-circle-filled"></i></span>
              </div>
              <div class="form-text">Entre amarillo y verde se muestran en amarillo. Por debajo, en rojo.</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Notificaciones --}}
      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="ti tabler-bell me-2"></i>Configuración de Notificaciones</h5></div>
        <div class="card-body">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                  <div class="fw-medium">Alertas de Vencimiento</div>
                  <small class="text-muted">Notificar actividades próximas a vencer</small>
                </div>
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" name="notif_vencimiento" value="1" {{ $config->notif_vencimiento ? 'checked' : '' }}>
                </div>
              </div>
              <label class="form-label">Días de anticipación</label>
              <input type="number" name="notif_dias_anticipacion" class="form-control" value="{{ $config->notif_dias_anticipacion }}" min="1" max="30">
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                  <div class="fw-medium">Alertas de Avance Bajo</div>
                  <small class="text-muted">Notificar actividades con bajo avance</small>
                </div>
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" name="notif_avance_bajo" value="1" {{ $config->notif_avance_bajo ? 'checked' : '' }}>
                </div>
              </div>
              <label class="form-label">Umbral de avance (%)</label>
              <input type="number" name="notif_umbral_avance" class="form-control" value="{{ $config->notif_umbral_avance }}" min="1" max="100">
            </div>
            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-medium">Notificaciones por Correo</div>
                  <small class="text-muted">Enviar alertas al correo institucional</small>
                </div>
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" name="notif_email" value="1" {{ $config->notif_email ? 'checked' : '' }}>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar Configuración</button>
        <a href="{{ route('adm-configuracion') }}" class="btn btn-label-secondary">Cancelar</a>
      </div>
    </form>
  </div>

  {{-- Panel lateral --}}
  <div class="col-xl-4">
    {{-- Accesos rápidos --}}
    <div class="card mb-4">
      <div class="card-header"><h5 class="mb-0">Accesos Rápidos</h5></div>
      <div class="list-group list-group-flush">
        <a href="{{ route('adm-usuarios') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
          <i class="ti tabler-users text-primary icon-20px"></i><span>Gestión de Usuarios</span><i class="ti tabler-chevron-right ms-auto text-muted"></i>
        </a>
        <a href="{{ route('adm-roles') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
          <i class="ti tabler-user-shield text-success icon-20px"></i><span>Roles del Sistema</span><i class="ti tabler-chevron-right ms-auto text-muted"></i>
        </a>
        <a href="{{ route('adm-permisos') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
          <i class="ti tabler-lock text-warning icon-20px"></i><span>Permisos</span><i class="ti tabler-chevron-right ms-auto text-muted"></i>
        </a>
        <a href="{{ route('sci-control-interno') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
          <i class="ti tabler-clipboard-list text-info icon-20px"></i><span>Control Interno</span><i class="ti tabler-chevron-right ms-auto text-muted"></i>
        </a>
      </div>
    </div>

    {{-- Unidades orgánicas --}}
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Unidades Orgánicas</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaUnidad">
          <i class="ti tabler-plus"></i>
        </button>
      </div>
      <div class="list-group list-group-flush">
        @foreach($unidades as $u)
        <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-4">
          <div>
            <div class="fw-medium small">{{ $u->nombre }}</div>
            <small class="text-muted">{{ $u->sigla }} {{ $u->responsable ? '· '.$u->responsable : '' }}</small>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-label-{{ $u->activo ? 'success' : 'secondary' }}">{{ $u->activo ? 'Activa' : 'Inactiva' }}</span>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- Modal Nueva Unidad --}}
<div class="modal fade" id="modalNuevaUnidad" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('adm-configuracion.unidades.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Nueva Unidad Orgánica</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Código <span class="text-danger">*</span></label>
            <input type="text" name="codigo" class="form-control text-uppercase" placeholder="Ej: ADM" required maxlength="20">
          </div>
          <div class="mb-3">
            <label class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" placeholder="Nombre completo" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Sigla</label>
            <input type="text" name="sigla" class="form-control" placeholder="Ej: ADM" maxlength="20">
          </div>
          <div class="mb-3">
            <label class="form-label">Responsable</label>
            <input type="text" name="responsable" class="form-control" placeholder="Nombre del responsable">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Crear Unidad</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
