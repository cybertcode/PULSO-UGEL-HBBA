@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Estructura Integridad - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-style')
<style>
.tree-etapa    { border-left: 4px solid var(--bs-warning); }
.tree-comp     { border-left: 4px solid var(--bs-info); margin-left: 1.5rem; }
.tree-pregunta { border-left: 4px solid var(--bs-success); margin-left: 3rem; }
.tree-header   { cursor: pointer; user-select: none; }
.tree-header:hover { background: var(--bs-tertiary-bg); border-radius: .375rem; }
.badge-orden   { font-size: .65rem; min-width: 22px; }
.link-ficha    { font-size: .75rem; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle; }
</style>
@endsection

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Estructura Integridad</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1"><i class="ti tabler-shield-check me-2 text-warning"></i>Estructura del Modelo de Integridad</h4>
    <p class="mb-0 text-muted">Árbol: Etapa → Componente → Preguntas</p>
  </div>
  <div class="d-flex gap-2 align-items-center flex-wrap">
    <form method="GET" class="d-flex gap-2 align-items-center">
      <select name="anio" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
        @foreach($anios->merge([now()->year])->unique()->sortDesc() as $a)
        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
      </select>
    </form>
    @can('integridad.editar')
    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaEtapa">
      <i class="ti tabler-plus me-1"></i>Nueva Etapa
    </button>
    @endcan
  </div>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible mb-3"><i class="ti tabler-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible mb-3"><i class="ti tabler-alert-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

@forelse($etapas as $etapa)
<div class="card mb-3 tree-etapa">
  <div class="card-body pb-2">
    <div class="d-flex align-items-center justify-content-between tree-header mb-2" data-bs-toggle="collapse" data-bs-target="#etapa-{{ $etapa->id }}">
      <div class="d-flex align-items-center gap-2">
        <i class="ti tabler-chevron-down text-muted"></i>
        <span class="badge bg-warning badge-orden">ET{{ $loop->iteration }}</span>
        <strong>{{ $etapa->nombre }}</strong>
        @if(!$etapa->activo)<span class="badge bg-label-danger ms-2">Inactivo</span>@endif
        <span class="text-muted small ms-2">{{ $etapa->componentes->count() }} comp. · {{ $etapa->componentes->sum(fn($c) => $c->preguntas->count()) }} preg.</span>
      </div>
      @can('integridad.editar')
      <div class="d-flex gap-1" onclick="event.stopPropagation()">
        <button class="btn btn-icon btn-sm btn-label-warning btn-editar-etapa"
          data-id="{{ $etapa->id }}" data-nombre="{{ $etapa->nombre }}"
          data-descripcion="{{ e($etapa->descripcion) }}" data-anio="{{ $etapa->anio }}"
          data-activo="{{ $etapa->activo ? 1 : 0 }}" title="Editar etapa">
          <i class="ti tabler-edit"></i></button>
        <button class="btn btn-icon btn-sm btn-label-info" title="Agregar componente"
          data-bs-toggle="modal" data-bs-target="#modalNuevoComponente"
          onclick="document.getElementById('nuevo_comp_etapa_id').value='{{ $etapa->id }}'">
          <i class="ti tabler-layout-grid-add"></i></button>
        <button class="btn btn-icon btn-sm btn-label-danger btn-eliminar"
          data-url="{{ route('adm-integridad.etapa.destroy', $etapa) }}" data-nombre="{{ $etapa->nombre }}" title="Eliminar etapa">
          <i class="ti tabler-trash"></i></button>
      </div>
      @endcan
    </div>

    <div class="collapse show" id="etapa-{{ $etapa->id }}">
      @foreach($etapa->componentes as $comp)
      <div class="card mb-2 tree-comp">
        <div class="card-body py-2">
          <div class="d-flex align-items-center justify-content-between tree-header" data-bs-toggle="collapse" data-bs-target="#icomp-{{ $comp->id }}">
            <div class="d-flex align-items-center gap-2">
              <i class="ti tabler-chevron-down text-muted" style="font-size:.8rem"></i>
              <span class="badge bg-info badge-orden">C{{ $loop->iteration }}</span>
              @if($comp->icono)<i class="ti {{ $comp->icono }} text-info"></i>@endif
              <span class="fw-semibold">{{ $comp->nombre }}</span>
              @if(!$comp->activo)<span class="badge bg-label-danger ms-1">Inactivo</span>@endif
              <span class="text-muted small ms-1">{{ $comp->preguntas->count() }} preguntas</span>
            </div>
            @can('integridad.editar')
            <div class="d-flex gap-1" onclick="event.stopPropagation()">
              <button class="btn btn-icon btn-sm btn-label-info btn-editar-componente"
                data-id="{{ $comp->id }}" data-nombre="{{ $comp->nombre }}"
                data-icono="{{ $comp->icono }}" data-descripcion="{{ e($comp->descripcion) }}"
                data-activo="{{ $comp->activo ? 1 : 0 }}" title="Editar componente">
                <i class="ti tabler-edit"></i></button>
              <button class="btn btn-icon btn-sm btn-label-success" title="Agregar pregunta"
                data-bs-toggle="modal" data-bs-target="#modalNuevaPregunta"
                onclick="document.getElementById('nueva_preg_comp_id').value='{{ $comp->id }}'">
                <i class="ti tabler-plus"></i></button>
              <button class="btn btn-icon btn-sm btn-label-danger btn-eliminar"
                data-url="{{ route('adm-integridad.componente.destroy', $comp) }}" data-nombre="{{ $comp->nombre }}" title="Eliminar">
                <i class="ti tabler-trash"></i></button>
            </div>
            @endcan
          </div>

          <div class="collapse show" id="icomp-{{ $comp->id }}">
            @foreach($comp->preguntas as $preg)
            <div class="d-flex align-items-start justify-content-between py-2 px-2 mt-1 tree-pregunta rounded">
              <div class="d-flex align-items-start gap-2">
                <span class="badge bg-success badge-orden mt-1">P{{ $loop->iteration }}</span>
                <div>
                  <div class="fw-medium" style="font-size:.875rem">{{ $preg->nombre }}</div>
                  @if($preg->link_ficha)
                  <a href="{{ $preg->link_ficha }}" target="_blank" class="link-ficha text-primary" title="{{ $preg->link_ficha }}">
                    <i class="ti tabler-link me-1"></i>{{ $preg->link_ficha }}
                  </a>
                  @else
                  <span class="text-muted" style="font-size:.75rem"><i class="ti tabler-link-off me-1"></i>Sin ficha</span>
                  @endif
                </div>
              </div>
              @can('integridad.editar')
              <div class="d-flex gap-1 ms-2 flex-shrink-0">
                <button class="btn btn-icon btn-sm btn-label-success btn-editar-pregunta"
                  data-id="{{ $preg->id }}" data-nombre="{{ $preg->nombre }}"
                  data-link="{{ $preg->link_ficha }}" data-activo="{{ $preg->activo ? 1 : 0 }}" title="Editar">
                  <i class="ti tabler-edit"></i></button>
                <button class="btn btn-icon btn-sm btn-label-danger btn-eliminar"
                  data-url="{{ route('adm-integridad.pregunta.destroy', $preg) }}" data-nombre="{{ $preg->nombre }}" title="Eliminar">
                  <i class="ti tabler-trash"></i></button>
              </div>
              @endcan
            </div>
            @endforeach
            @if($comp->preguntas->isEmpty())
            <div class="text-muted small py-2 px-3"><i class="ti tabler-info-circle me-1"></i>Sin preguntas.</div>
            @endif
          </div>
        </div>
      </div>
      @endforeach
      @if($etapa->componentes->isEmpty())
      <div class="text-muted small py-2 px-2"><i class="ti tabler-info-circle me-1"></i>Sin componentes.</div>
      @endif
    </div>
  </div>
</div>
@empty
<div class="card"><div class="card-body text-center py-5 text-muted">
  <i class="ti tabler-shield-check icon-48px d-block mb-3"></i>
  <p class="mb-0">No hay etapas para el año {{ $anio }}. Crea la primera con el botón superior.</p>
</div></div>
@endforelse

@can('integridad.editar')
{{-- Modal Nueva Etapa --}}
<div class="modal fade" id="modalNuevaEtapa" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('adm-integridad.etapa.store') }}">@csrf
      <div class="modal-header"><h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nueva Etapa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" class="form-control" required autofocus></div>
          <div class="col-md-4"><label class="form-label">Año <span class="text-danger">*</span></label><input type="number" name="anio" class="form-control" value="{{ $anio }}" min="2020" max="2099" required></div>
          <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control" rows="2"></textarea></div>
          <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" checked><label class="form-check-label">Activo</label></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning"><i class="ti tabler-device-floppy me-1"></i>Guardar</button></div>
    </form>
  </div></div>
</div>

{{-- Modal Editar Etapa --}}
<div class="modal fade" id="modalEditarEtapa" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="formEditarEtapa">@csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Etapa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" id="edit_etapa_nombre" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Año</label><input type="number" name="anio" id="edit_etapa_anio" class="form-control" min="2020" max="2099" required></div>
          <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" id="edit_etapa_descripcion" class="form-control" rows="2"></textarea></div>
          <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_etapa_activo"><label class="form-check-label">Activo</label></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button></div>
    </form>
  </div></div>
</div>

{{-- Modal Nuevo Componente --}}
<div class="modal fade" id="modalNuevoComponente" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('adm-integridad.componente.store') }}">@csrf
      <input type="hidden" name="etapa_id" id="nuevo_comp_etapa_id">
      <div class="modal-header"><h5 class="modal-title"><i class="ti tabler-layout-grid-add me-2"></i>Nuevo Componente</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" class="form-control" required autofocus></div>
          <div class="col-12"><label class="form-label">Ícono <span class="text-muted small">(clase tabler)</span></label><input type="text" name="icono" class="form-control" placeholder="tabler-shield-check"></div>
          <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control" rows="2"></textarea></div>
          <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" checked><label class="form-check-label">Activo</label></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-info"><i class="ti tabler-device-floppy me-1"></i>Guardar</button></div>
    </form>
  </div></div>
</div>

{{-- Modal Editar Componente --}}
<div class="modal fade" id="modalEditarComponente" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="formEditarComponente">@csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Componente</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" id="edit_comp_nombre" class="form-control" required></div>
          <div class="col-12"><label class="form-label">Ícono</label><input type="text" name="icono" id="edit_comp_icono" class="form-control" placeholder="tabler-shield-check"></div>
          <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" id="edit_comp_descripcion" class="form-control" rows="2"></textarea></div>
          <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_comp_activo"><label class="form-check-label">Activo</label></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-info"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button></div>
    </form>
  </div></div>
</div>

{{-- Modal Nueva Pregunta --}}
<div class="modal fade" id="modalNuevaPregunta" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="POST" action="{{ route('adm-integridad.pregunta.store') }}">@csrf
      <input type="hidden" name="componente_id" id="nueva_preg_comp_id">
      <div class="modal-header"><h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nueva Pregunta</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label">Nombre de la pregunta <span class="text-danger">*</span></label><textarea name="nombre" class="form-control" rows="3" required autofocus></textarea></div>
          <div class="col-12"><label class="form-label">Link de ficha <span class="text-muted small">(URL opcional)</span></label><input type="url" name="link_ficha" class="form-control" placeholder="https://..."></div>
          <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" checked><label class="form-check-label">Activo</label></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success"><i class="ti tabler-device-floppy me-1"></i>Guardar</button></div>
    </form>
  </div></div>
</div>

{{-- Modal Editar Pregunta --}}
<div class="modal fade" id="modalEditarPregunta" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="POST" id="formEditarPregunta">@csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Pregunta</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label">Nombre <span class="text-danger">*</span></label><textarea name="nombre" id="edit_preg_nombre" class="form-control" rows="3" required></textarea></div>
          <div class="col-12"><label class="form-label">Link de ficha</label><input type="url" name="link_ficha" id="edit_preg_link" class="form-control" placeholder="https://..."></div>
          <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_preg_activo"><label class="form-check-label">Activo</label></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button></div>
    </form>
  </div></div>
</div>
@endcan

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  function confirmarEliminar(url, nombre) {
    Swal.fire({
      title: '¿Eliminar?',
      html: `<strong>${nombre}</strong><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
      icon: 'warning', showCancelButton: true,
      confirmButtonText: '<i class="ti tabler-trash me-1"></i>Eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      const f = document.createElement('form');
      f.method = 'POST'; f.action = url;
      f.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}"><input type="hidden" name="_method" value="DELETE">`;
      document.body.appendChild(f); f.submit();
    });
  }

  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', () => confirmarEliminar(btn.dataset.url, btn.dataset.nombre));
  });

  document.querySelectorAll('.btn-editar-etapa').forEach(btn => {
    btn.addEventListener('click', function () {
      document.getElementById('formEditarEtapa').action = '/administracion/integridad/etapa/' + this.dataset.id;
      document.getElementById('edit_etapa_nombre').value      = this.dataset.nombre;
      document.getElementById('edit_etapa_descripcion').value = this.dataset.descripcion || '';
      document.getElementById('edit_etapa_anio').value        = this.dataset.anio;
      document.getElementById('edit_etapa_activo').checked    = this.dataset.activo === '1';
      new bootstrap.Modal(document.getElementById('modalEditarEtapa')).show();
    });
  });

  document.querySelectorAll('.btn-editar-componente').forEach(btn => {
    btn.addEventListener('click', function () {
      document.getElementById('formEditarComponente').action = '/administracion/integridad/componente/' + this.dataset.id;
      document.getElementById('edit_comp_nombre').value      = this.dataset.nombre;
      document.getElementById('edit_comp_icono').value       = this.dataset.icono || '';
      document.getElementById('edit_comp_descripcion').value = this.dataset.descripcion || '';
      document.getElementById('edit_comp_activo').checked    = this.dataset.activo === '1';
      new bootstrap.Modal(document.getElementById('modalEditarComponente')).show();
    });
  });

  document.querySelectorAll('.btn-editar-pregunta').forEach(btn => {
    btn.addEventListener('click', function () {
      document.getElementById('formEditarPregunta').action = '/administracion/integridad/pregunta/' + this.dataset.id;
      document.getElementById('edit_preg_nombre').value   = this.dataset.nombre;
      document.getElementById('edit_preg_link').value     = this.dataset.link || '';
      document.getElementById('edit_preg_activo').checked = this.dataset.activo === '1';
      new bootstrap.Modal(document.getElementById('modalEditarPregunta')).show();
    });
  });

});
</script>
@endsection
