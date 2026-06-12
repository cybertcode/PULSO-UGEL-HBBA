@php
$configData = Helper::appClasses();
$breadcrumbs = [
    ['link' => route('dashboard'), 'name' => 'Inicio'],
    ['name' => 'Instituciones Vinculadas'],
];
@endphp

@extends('layouts/contentNavbarLayout')
@section('title', 'Instituciones Vinculadas')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">
      <i class="icon-base ti tabler-building-community me-2 text-primary"></i>
      Instituciones Vinculadas
    </h4>
    <p class="text-muted mb-0 small">
      Gestiona las instituciones que aparecen en el landing público con sus logos y colores.
    </p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('landing') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
      <i class="icon-base ti tabler-external-link me-1"></i>Ver Landing
    </a>
    @can('instituciones.crear')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
      <i class="icon-base ti tabler-plus me-1"></i> Nueva Institución
    </button>
    @endcan
  </div>
</div>

{{-- ── TABLA ── --}}
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:50px" class="text-center">Orden</th>
            <th style="width:70px" class="text-center">Logo</th>
            <th>Nombre / Sigla</th>
            <th style="width:90px" class="text-center">Color</th>
            <th>Sitio web</th>
            <th style="width:80px" class="text-center">Estado</th>
            <th style="width:100px" class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($instituciones as $inst)
          <tr>
            <td class="text-center text-muted small">{{ $inst->orden }}</td>
            <td class="text-center">
              @if($inst->logo_src)
                <img src="{{ $inst->logo_src }}" alt="{{ $inst->sigla }}"
                     style="width:44px;height:44px;object-fit:contain;border-radius:8px;background:#f8fafc;padding:2px;">
              @else
                <span class="badge rounded-pill" style="background:{{ $inst->color_acento }};font-size:.8rem;padding:.35rem .6rem;">
                  {{ $inst->sigla }}
                </span>
              @endif
            </td>
            <td>
              <div class="fw-semibold">{{ $inst->nombre }}</div>
              <div class="text-muted small">{{ $inst->sigla }}
                @if($inst->descripcion) · {{ Str::limit($inst->descripcion,50) }} @endif
              </div>
            </td>
            <td class="text-center">
              <span style="display:inline-block;width:28px;height:28px;border-radius:50%;background:{{ $inst->color_acento }};border:2px solid rgba(0,0,0,.1);"
                    title="{{ $inst->color_acento }}"></span>
            </td>
            <td>
              @if($inst->url_sitio)
                <a href="{{ $inst->url_sitio }}" target="_blank" class="text-muted small text-truncate d-inline-block" style="max-width:180px;">
                  {{ $inst->url_sitio }}
                </a>
              @else
                <span class="text-muted small">—</span>
              @endif
            </td>
            <td class="text-center">
              @can('instituciones.editar')
              <div class="form-check form-switch d-flex justify-content-center mb-0">
                <input class="form-check-input toggle-activo" type="checkbox"
                       data-id="{{ $inst->id }}"
                       data-url="{{ route('instituciones-vinculadas.toggle', $inst) }}"
                       {{ $inst->activo ? 'checked' : '' }}>
              </div>
              @else
              <span class="badge bg-label-{{ $inst->activo ? 'success' : 'secondary' }}">
                {{ $inst->activo ? 'Activa' : 'Inactiva' }}
              </span>
              @endcan
            </td>
            <td class="text-center">
              @can('instituciones.editar')
              <button class="btn btn-sm btn-icon btn-text-secondary btn-editar"
                      data-id="{{ $inst->id }}"
                      data-nombre="{{ $inst->nombre }}"
                      data-sigla="{{ $inst->sigla }}"
                      data-color="{{ $inst->color_acento }}"
                      data-url="{{ $inst->url_sitio }}"
                      data-descripcion="{{ $inst->descripcion }}"
                      data-orden="{{ $inst->orden }}"
                      data-logo-src="{{ $inst->logo_src }}"
                      data-logo-url-ext="{{ $inst->logo_url }}"
                      data-route="{{ route('instituciones-vinculadas.update', $inst) }}"
                      title="Editar">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              @endcan
              @can('instituciones.eliminar')
                <form method="POST" action="{{ route('instituciones-vinculadas.destroy', $inst) }}"
                    class="d-inline form-eliminar"
                    data-nombre="{{ e($inst->nombre) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-text-danger" title="Eliminar">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
              @endcan
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-4 text-muted">
              No hay instituciones registradas.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ══════════════════════════════
     MODAL CREAR
══════════════════════════════ --}}
@can('instituciones.crear')
<div class="modal fade" id="modalCrear" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('instituciones-vinculadas.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-building-plus me-2"></i>Nueva Institución</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @include('content.instituciones-vinculadas._form')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i class="ti tabler-device-floppy me-1"></i>Guardar
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

@endcan

{{-- ══════════════════════════════
     MODAL EDITAR
══════════════════════════════ --}}
@can('instituciones.editar')
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="formEditar" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Institución</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          {{-- Preview logo actual --}}
          <div id="logoActualWrap" class="mb-3 d-none">
            <label class="form-label small fw-semibold text-muted">Logo actual</label>
            <div class="d-flex align-items-center gap-3">
              <img id="logoActualImg" src="" alt="logo" style="width:56px;height:56px;object-fit:contain;border-radius:8px;background:#f8fafc;padding:4px;border:1px solid #e2e8f0;">
              <div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="eliminar_logo" id="eliminarLogo" value="1">
                  <label class="form-check-label text-danger small" for="eliminarLogo">Eliminar logo actual</label>
                </div>
              </div>
            </div>
          </div>
          @include('content.instituciones-vinculadas._form')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i class="ti tabler-device-floppy me-1"></i>Guardar cambios
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endcan

@endsection

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
  <style>
    .swal2-container.swal2-top-end { right:1rem !important; top:1rem !important; left:auto !important; padding:0 !important; width:auto !important; background:transparent !important; }
    .swal2-container.swal2-top-end .swal2-popup.pulso-toast { display:flex !important; flex-direction:row !important; align-items:center !important; gap:.6rem !important; padding:.6rem 1rem !important; min-width:260px; max-width:380px; border-radius:.5rem !important; box-shadow:0 4px 20px rgba(0,0,0,.15) !important; font-size:.875rem !important; margin-bottom:.5rem !important; }
    .swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-title { margin:0 !important; padding:0 !important; font-size:.875rem !important; font-weight:500 !important; flex:1 !important; }
    .swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-icon { width:1.5rem !important; height:1.5rem !important; min-width:1.5rem !important; margin:0 !important; border-width:2px !important; font-size:.5rem !important; }
    .swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-html-container,
    .swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-actions,
    .swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-close { display:none !important; }
    .swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-timer-progress-bar-container { position:absolute !important; bottom:0 !important; left:0 !important; right:0 !important; height:3px !important; border-radius:0 0 .5rem .5rem !important; overflow:hidden !important; }
  </style>
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
<script>
  var iconColors = { success:'#28c76f', error:'#ea5455', warning:'#ff9f43', info:'#00cfe8' };
  function toast(icon, title, timer) {
    Swal.fire({
      toast: true, position: 'top-end', icon: icon, title: title,
      showConfirmButton: false, timer: timer || 2800, timerProgressBar: true,
      customClass: { popup: 'pulso-toast' },
      iconColor: iconColors[icon] || iconColors.info,
    });
  }

document.addEventListener('DOMContentLoaded', function () {

  // ── Toggle activo con toast ──
  document.querySelectorAll('.toggle-activo').forEach(function (el) {
    el.addEventListener('change', function () {
      var checkbox = this;
      var activo = this.checked;
      fetch(this.dataset.url, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
      })
      .then(function(r) {
        if (r.ok) {
          r.json().then(function(d) { checkbox.checked = d.activo; });
          toast(activo ? 'success' : 'info', activo ? 'Institución activada' : 'Institución desactivada', 2000);
        } else {
          checkbox.checked = !checkbox.checked;
          toast('error', 'No se pudo cambiar el estado', 3000);
        }
      })
      .catch(function() {
        checkbox.checked = !checkbox.checked;
        toast('error', 'Error de conexión', 3000);
      });
    });
  });

  // ── Eliminar con SweetAlert2 ──
  document.querySelectorAll('.form-eliminar').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var nombre = this.dataset.nombre || 'esta institución';
      Swal.fire({
        title: '¿Eliminar institución?',
        html: '<strong>' + nombre + '</strong><br><small class="text-muted">Esta acción no se puede deshacer.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="ti tabler-trash me-1"></i>Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ea5455',
      }).then(function(r) { if (r.isConfirmed) form.submit(); });
    });
  });

  // ── Botones editar ──
  document.querySelectorAll('.btn-editar').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var d = this.dataset;
      var form = document.getElementById('formEditar');
      form.action = d.route;

      var wrap = document.getElementById('logoActualWrap');
      var img  = document.getElementById('logoActualImg');
      if (d.logoSrc && d.logoSrc !== 'null' && d.logoSrc !== '') {
        img.src = d.logoSrc;
        wrap.classList.remove('d-none');
      } else {
        wrap.classList.add('d-none');
      }
      document.getElementById('eliminarLogo').checked = false;

      form.querySelector('[name="nombre"]').value       = d.nombre || '';
      form.querySelector('[name="sigla"]').value        = d.sigla  || '';
      form.querySelector('[name="color_acento"]').value = d.color  || '#1e3a8a';
      form.querySelector('[name="url_sitio"]').value    = d.url    || '';
      form.querySelector('[name="descripcion"]').value  = d.descripcion || '';
      form.querySelector('[name="orden"]').value        = d.orden  || 0;

      var logoUrlField = form.querySelector('[name="logo_url"]');
      if (logoUrlField) logoUrlField.value = d.logoUrlExt || '';

      var fileInput = form.querySelector('[name="logo_file"]');
      if (fileInput) fileInput.value = '';

      new bootstrap.Modal(document.getElementById('modalEditar')).show();
    });
  });

});
</script>
@endsection
