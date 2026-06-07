@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Unidades Orgánicas')

@section('content')

<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h4 class="mb-1"><i class="ti tabler-sitemap me-2 text-primary"></i>Unidades Orgánicas</h4>
    <p class="mb-0 text-muted">Gestión de unidades orgánicas de la institución</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaUnidad">
    <i class="ti tabler-plus me-1"></i>Nueva Unidad
  </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="ti tabler-circle-check me-2"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="ti tabler-alert-circle me-2"></i>
  <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
  <div class="col-xl-9">
    <div class="card">
      <div class="card-header d-flex align-items-center gap-2">
        <span class="badge bg-label-primary p-2"><i class="ti tabler-sitemap icon-20px"></i></span>
        <h5 class="mb-0">Listado de Unidades</h5>
        <span class="badge bg-label-secondary ms-auto">{{ $unidades->count() }} total</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Código</th>
              <th>Nombre</th>
              <th>Sigla</th>
              <th>Responsable</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($unidades as $u)
            <tr>
              <td><span class="badge bg-label-secondary">{{ $u->codigo }}</span></td>
              <td><div class="fw-medium">{{ $u->nombre }}</div></td>
              <td>{{ $u->sigla ?? '—' }}</td>
              <td>{{ $u->responsable ?? '—' }}</td>
              <td class="text-center">
                <form method="POST" action="{{ route('adm-unidades.toggle', $u) }}" class="d-inline">
                  @csrf @method('PATCH')
                  <button type="submit"
                    class="badge border-0 cursor-pointer bg-label-{{ $u->activo ? 'success' : 'secondary' }}"
                    title="{{ $u->activo ? 'Desactivar' : 'Activar' }}">
                    {{ $u->activo ? 'Activa' : 'Inactiva' }}
                  </button>
                </form>
              </td>
              <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                  <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                    onclick="editarUnidad({{ $u->id }}, '{{ addslashes($u->nombre) }}', '{{ addslashes($u->sigla ?? '') }}', '{{ addslashes($u->responsable ?? '') }}', {{ $u->activo ? 'true' : 'false' }})"
                    title="Editar">
                    <i class="ti tabler-edit"></i>
                  </button>
                  <form method="POST" action="{{ route('adm-unidades.destroy', $u) }}" class="d-inline"
                    onsubmit="return confirm('¿Eliminar la unidad {{ addslashes($u->nombre) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" title="Eliminar">
                      <i class="ti tabler-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="ti tabler-sitemap icon-40px d-block mx-auto mb-2 opacity-25"></i>
                No hay unidades orgánicas registradas.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-xl-3">
    <div class="card mb-4">
      <div class="card-header"><h6 class="mb-0">Resumen</h6></div>
      <div class="card-body">
        <div class="row g-3 text-center">
          <div class="col-6">
            <div class="fw-bold fs-3 text-primary">{{ $unidades->count() }}</div>
            <div class="small text-muted">Total</div>
          </div>
          <div class="col-6">
            <div class="fw-bold fs-3 text-success">{{ $unidades->where('activo', true)->count() }}</div>
            <div class="small text-muted">Activas</div>
          </div>
          <div class="col-6">
            <div class="fw-bold fs-3 text-secondary">{{ $unidades->where('activo', false)->count() }}</div>
            <div class="small text-muted">Inactivas</div>
          </div>
          <div class="col-6">
            <div class="fw-bold fs-3 text-info">{{ $unidades->whereNotNull('responsable')->count() }}</div>
            <div class="small text-muted">Con responsable</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h6 class="mb-0"><i class="ti tabler-info-circle me-1"></i>Información</h6></div>
      <div class="card-body">
        <p class="text-muted small mb-0">
          Las unidades orgánicas representan las áreas de la institución que participan en el Sistema de Control Interno (SCI).
          Cada actividad del SCI está asociada a una unidad orgánica.
        </p>
      </div>
    </div>
  </div>
</div>

{{-- Modal Nueva Unidad --}}
<div class="modal fade" id="modalNuevaUnidad" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('adm-unidades.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nueva Unidad Orgánica</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Código <span class="text-danger">*</span></label>
              <input type="text" name="codigo" class="form-control text-uppercase"
                placeholder="Ej: UGEL-ADM" required maxlength="20">
            </div>
            <div class="col-md-6">
              <label class="form-label">Sigla</label>
              <input type="text" name="sigla" class="form-control" placeholder="Ej: ADM" maxlength="20">
            </div>
            <div class="col-12">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" placeholder="Nombre completo de la unidad" required>
            </div>
            <div class="col-12">
              <label class="form-label">Responsable</label>
              <input type="text" name="responsable" class="form-control" placeholder="Nombre del jefe de la unidad">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-plus me-1"></i>Crear Unidad</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Editar Unidad --}}
<div class="modal fade" id="modalEditarUnidad" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="formEditarUnidad" action="">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Unidad Orgánica</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Sigla</label>
              <input type="text" name="sigla" id="editSigla" class="form-control" maxlength="20">
            </div>
            <div class="col-md-6">
              <label class="form-label">Estado</label>
              <select name="activo" class="form-select" id="editActivo">
                <option value="1">Activa</option>
                <option value="0">Inactiva</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="editNombre" class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label">Responsable</label>
              <input type="text" name="responsable" id="editResponsable" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
function editarUnidad(id, nombre, sigla, responsable, activo) {
  document.getElementById('editNombre').value      = nombre;
  document.getElementById('editSigla').value       = sigla;
  document.getElementById('editResponsable').value = responsable;
  document.getElementById('editActivo').value      = activo ? '1' : '0';
  document.getElementById('formEditarUnidad').action = `/unidades-organicas/${id}`;
  new bootstrap.Modal(document.getElementById('modalEditarUnidad')).show();
}
</script>
@endsection
