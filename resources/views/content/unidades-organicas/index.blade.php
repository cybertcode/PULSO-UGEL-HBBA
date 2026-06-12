@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Unidades Orgánicas')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('content')

{{-- Header --}}
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h4 class="mb-1"><i class="ti tabler-sitemap me-2 text-primary"></i>Unidades Orgánicas</h4>
    <p class="mb-0 text-muted">Gestión de unidades orgánicas de la institución</p>
  </div>
  @can('unidades.crear')
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaUnidad">
    <i class="ti tabler-plus me-1"></i>Nueva Unidad
  </button>
  @endcan
</div>

{{-- Tarjetas de resumen --}}
<div class="row g-4 mb-4">
  <div class="col-6 col-sm-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md flex-shrink-0">
          <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-sitemap"></i></span>
        </div>
        <div>
          <div class="fw-bold fs-3 lh-1">{{ $unidades->count() }}</div>
          <div class="small text-muted mt-1">Total unidades</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md flex-shrink-0">
          <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-circle-check"></i></span>
        </div>
        <div>
          <div class="fw-bold fs-3 lh-1 text-success">{{ $unidades->where('activo', true)->count() }}</div>
          <div class="small text-muted mt-1">Activas</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md flex-shrink-0">
          <span class="avatar-initial rounded bg-label-secondary"><i class="ti tabler-circle-x"></i></span>
        </div>
        <div>
          <div class="fw-bold fs-3 lh-1 text-secondary">{{ $unidades->where('activo', false)->count() }}</div>
          <div class="small text-muted mt-1">Inactivas</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md flex-shrink-0">
          <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-user-check"></i></span>
        </div>
        <div>
          <div class="fw-bold fs-3 lh-1 text-info">{{ $unidades->whereNotNull('responsable_id')->count() }}</div>
          <div class="small text-muted mt-1">Con responsable</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Tabla principal --}}
<div class="card">
  <div class="card-header d-flex align-items-center gap-2 py-3">
    <span class="badge bg-label-primary p-2 rounded"><i class="ti tabler-list icon-20px"></i></span>
    <h5 class="mb-0">Listado de Unidades</h5>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="ps-4">Código</th>
          <th>Unidad Orgánica</th>
          <th>Responsable</th>
          <th>Contacto</th>
          <th class="text-center">Estado</th>
          <th class="text-center pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($unidades as $u)
        <tr>
          <td class="ps-4">
            <span class="badge bg-label-secondary fw-semibold">{{ $u->codigo }}</span>
          </td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar avatar-sm flex-shrink-0">
                <span class="avatar-initial rounded-circle bg-label-primary text-uppercase">
                  {{ substr($u->sigla ?? $u->nombre, 0, 2) }}
                </span>
              </div>
              <div>
                <div class="fw-semibold">{{ $u->nombre }}</div>
                @if($u->sigla)<small class="text-muted">{{ $u->sigla }}</small>@endif
                @if($u->descripcion)
                  <div class="small text-muted text-truncate" style="max-width:280px">{{ $u->descripcion }}</div>
                @endif
              </div>
            </div>
          </td>
          <td>
            @if($u->responsable)
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm flex-shrink-0">
                  <span class="avatar-initial rounded-circle bg-label-warning text-uppercase">
                    {{ substr($u->responsable->name, 0, 2) }}
                  </span>
                </div>
                <div>
                  <div class="fw-semibold lh-sm">{{ $u->responsable->name }}</div>
                  @if($u->responsable->cargo)
                    <small class="text-muted">{{ $u->responsable->cargo->nombre }}</small>
                  @endif
                </div>
              </div>
            @else
              <span class="text-muted fst-italic small">Sin asignar</span>
            @endif
          </td>
          <td>
            @if($u->correo || $u->telefono)
              @if($u->correo)
                <div class="small"><i class="ti tabler-mail text-muted me-1"></i>{{ $u->correo }}</div>
              @endif
              @if($u->telefono)
                <div class="small"><i class="ti tabler-phone text-muted me-1"></i>{{ $u->telefono }}</div>
              @endif
            @else
              <span class="text-muted fst-italic small">—</span>
            @endif
          </td>
          <td class="text-center">
            @can('unidades.editar')
            <span class="badge cursor-pointer bg-label-{{ $u->activo ? 'success' : 'secondary' }} btn-toggle"
              data-id="{{ $u->id }}"
              data-nombre="{{ $u->nombre }}"
              data-activo="{{ $u->activo ? '1' : '0' }}">
              <i class="ti tabler-{{ $u->activo ? 'check' : 'x' }} me-1"></i>
              {{ $u->activo ? 'Activa' : 'Inactiva' }}
            </span>
            @else
            <span class="badge bg-label-{{ $u->activo ? 'success' : 'secondary' }}">
              <i class="ti tabler-{{ $u->activo ? 'check' : 'x' }} me-1"></i>
              {{ $u->activo ? 'Activa' : 'Inactiva' }}
            </span>
            @endcan
          </td>
          <td class="text-center pe-4">
            <div class="d-flex gap-1 justify-content-center">
              @can('unidades.editar')
              <button type="button"
                class="btn btn-sm btn-icon btn-text-secondary rounded-pill btn-editar"
                title="Editar"
                data-id="{{ $u->id }}"
                data-nombre="{{ $u->nombre }}"
                data-sigla="{{ $u->sigla }}"
                data-responsable="{{ $u->responsable_id }}"
                data-activo="{{ $u->activo ? '1' : '0' }}"
                data-correo="{{ $u->correo }}"
                data-telefono="{{ $u->telefono }}"
                data-descripcion="{{ $u->descripcion }}">
                <i class="ti tabler-edit"></i>
              </button>
              @endcan
              @can('unidades.eliminar')
              <button type="button"
                class="btn btn-sm btn-icon btn-text-danger rounded-pill btn-eliminar"
                title="Eliminar"
                data-id="{{ $u->id }}"
                data-nombre="{{ $u->nombre }}">
                <i class="ti tabler-trash"></i>
              </button>
              @endcan
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-5 text-muted">
            <i class="ti tabler-sitemap d-block mx-auto mb-2 opacity-25" style="font-size:3rem"></i>
            <div class="fw-semibold mb-1">No hay unidades orgánicas registradas</div>
            <small>Usa el botón <strong>Nueva Unidad</strong> para agregar la primera.</small>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Modal Nueva Unidad --}}
@can('unidades.crear')
<div class="modal fade" id="modalNuevaUnidad" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('adm-unidades.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-plus me-2 text-primary"></i>Nueva Unidad Orgánica</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" placeholder="Nombre completo de la unidad" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Sigla</label>
              <input type="text" name="sigla" class="form-control" placeholder="Ej: ADM" maxlength="20">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Teléfono</label>
              <input type="text" name="telefono" class="form-control" placeholder="076-123456" maxlength="20">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Responsable</label>
              <select name="responsable_id" class="form-select select2-nueva">
                <option value="">Sin asignar</option>
                @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }}{{ $usr->cargo ? ' — '.$usr->cargo->nombre : '' }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Correo electrónico</label>
              <input type="email" name="correo" class="form-control" placeholder="correo@ugel.gob.pe" maxlength="100">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2" maxlength="500"
                placeholder="Breve descripción de las funciones de esta unidad..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-plus me-1"></i>Crear Unidad</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endcan

{{-- Modal Editar Unidad --}}
@can('unidades.editar')
<div class="modal fade" id="modalEditarUnidad" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="formEditarUnidad" action="">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-edit me-2 text-primary"></i>Editar Unidad Orgánica</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="editNombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Sigla</label>
              <input type="text" name="sigla" id="editSigla" class="form-control" maxlength="20">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Estado</label>
              <select name="activo" id="editActivo" class="form-select">
                <option value="1">Activa</option>
                <option value="0">Inactiva</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Responsable</label>
              <select name="responsable_id" id="editResponsable" class="form-select select2-editar">
                <option value="">Sin asignar</option>
                @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }}{{ $usr->cargo ? ' — '.$usr->cargo->nombre : '' }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Correo electrónico</label>
              <input type="email" name="correo" id="editCorreo" class="form-control" maxlength="100">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Teléfono</label>
              <input type="text" name="telefono" id="editTelefono" class="form-control" maxlength="20">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2" maxlength="500"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endcan

{{-- Formularios ocultos --}}
@can('unidades.editar')
<form id="formToggle" method="POST" style="display:none">
  @csrf @method('PATCH')
</form>
@endcan
@can('unidades.eliminar')
<form id="formEliminar" method="POST" style="display:none">
  @csrf @method('DELETE')
</form>
@endcan

@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // Select2 — inicializar cuando se abre cada modal para asegurar que Select2 ya cargó
  document.getElementById('modalNuevaUnidad').addEventListener('shown.bs.modal', function () {
    if (window.$ && $.fn.select2) {
      $('.select2-nueva').select2({ dropdownParent: $('#modalNuevaUnidad'), width: '100%', placeholder: 'Buscar responsable...' });
    }
  });
  document.getElementById('modalEditarUnidad').addEventListener('shown.bs.modal', function () {
    if (window.$ && $.fn.select2) {
      $('.select2-editar').select2({ dropdownParent: $('#modalEditarUnidad'), width: '100%', placeholder: 'Buscar responsable...' });
      // Aplicar valor pendiente de responsable
      const pending = document.getElementById('modalEditarUnidad').dataset.pendingResponsable;
      $('#editResponsable').val(pending || null).trigger('change');
    }
  });

  // Editar
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-editar');
    if (!btn) return;
    const d = btn.dataset;
    document.getElementById('formEditarUnidad').action = '/unidades-organicas/' + d.id;
    document.getElementById('editNombre').value      = d.nombre || '';
    document.getElementById('editSigla').value       = d.sigla || '';
    document.getElementById('editActivo').value      = d.activo || '1';
    document.getElementById('editCorreo').value      = d.correo || '';
    document.getElementById('editTelefono').value    = d.telefono || '';
    document.getElementById('editDescripcion').value = d.descripcion || '';
    // Guardar responsable pendiente para aplicar cuando el modal abra y Select2 esté listo
    document.getElementById('modalEditarUnidad').dataset.pendingResponsable = d.responsable || '';
    new bootstrap.Modal(document.getElementById('modalEditarUnidad')).show();
  });

  // Toggle estado
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-toggle');
    if (!btn) return;
    const id     = btn.dataset.id;
    const nombre = btn.dataset.nombre;
    const activo = btn.dataset.activo === '1';
    Swal.fire({
      title: activo ? 'Desactivar unidad' : 'Activar unidad',
      html: `¿Confirmar cambio de estado de <strong>${nombre}</strong>?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: activo ? '#6c757d' : '#28a745',
      confirmButtonText: activo ? 'Sí, desactivar' : 'Sí, activar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (result.isConfirmed) {
        const f = document.getElementById('formToggle');
        f.action = '/unidades-organicas/' + id + '/toggle';
        f.submit();
      }
    });
  });

  // Eliminar
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-eliminar');
    if (!btn) return;
    const id     = btn.dataset.id;
    const nombre = btn.dataset.nombre;
    Swal.fire({
      title: '¿Eliminar unidad?',
      html: `<strong>${nombre}</strong><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (result.isConfirmed) {
        const f = document.getElementById('formEliminar');
        f.action = '/unidades-organicas/' + id;
        f.submit();
      }
    });
  });

});
</script>
@endsection
