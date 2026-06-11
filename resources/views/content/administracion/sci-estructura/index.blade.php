@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Estructura SCI - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-style')
<style>
.tree-eje      { border-left: 4px solid var(--bs-primary); }
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
    <li class="breadcrumb-item active">Estructura SCI</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1"><i class="ti tabler-sitemap me-2 text-primary"></i>Estructura del Sistema de Control Interno</h4>
    <p class="mb-0 text-muted">Árbol: Eje → Componente → Preguntas</p>
  </div>
  <div class="d-flex gap-2 align-items-center flex-wrap">
    {{-- Filtro año --}}
    <form method="GET" class="d-flex gap-2 align-items-center">
      <select name="anio" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
        @foreach($anios->merge([now()->year]) ->unique()->sortDesc() as $a)
        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
      </select>
    </form>
    @can('componentes.editar')
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoEje">
      <i class="ti tabler-plus me-1"></i>Nuevo Eje
    </button>
    @endcan
  </div>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible mb-3"><i class="ti tabler-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible mb-3"><i class="ti tabler-alert-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

@forelse($ejes as $eje)
<div class="card mb-3 tree-eje">
  <div class="card-body pb-2">
    {{-- Cabecera eje --}}
    <div class="d-flex align-items-center justify-content-between tree-header mb-2" data-bs-toggle="collapse" data-bs-target="#eje-{{ $eje->id }}">
      <div class="d-flex align-items-center gap-2">
        <i class="ti tabler-chevron-down text-muted"></i>
        <span class="badge bg-primary badge-orden">E{{ $loop->iteration }}</span>
        <strong>{{ $eje->nombre }}</strong>
        @if(!$eje->activo)<span class="badge bg-label-danger ms-2">Inactivo</span>@endif
        <span class="text-muted small ms-2">{{ $eje->componentes->count() }} comp. · {{ $eje->componentes->sum(fn($c) => $c->preguntas->count()) }} preg.</span>
      </div>
      @can('componentes.editar')
      <div class="d-flex gap-1" onclick="event.stopPropagation()">
        <button class="btn btn-icon btn-sm btn-label-info btn-editar-eje"
          data-id="{{ $eje->id }}" data-nombre="{{ $eje->nombre }}"
          data-descripcion="{{ e($eje->descripcion) }}" data-anio="{{ $eje->anio }}"
          data-activo="{{ $eje->activo ? 1 : 0 }}" title="Editar eje">
          <i class="ti tabler-edit"></i></button>
        <button class="btn btn-icon btn-sm btn-label-primary" title="Agregar componente"
          data-bs-toggle="modal" data-bs-target="#modalNuevoComponente"
          onclick="document.getElementById('nuevo_comp_eje_id').value='{{ $eje->id }}'">
          <i class="ti tabler-layout-grid-add"></i></button>
        <button class="btn btn-icon btn-sm btn-label-danger btn-eliminar"
          data-url="{{ route('adm-sci.eje.destroy', $eje) }}" data-nombre="{{ $eje->nombre }}" title="Eliminar eje">
          <i class="ti tabler-trash"></i></button>
      </div>
      @endcan
    </div>

    {{-- Componentes --}}
    <div class="collapse show" id="eje-{{ $eje->id }}">
      @foreach($eje->componentes as $comp)
      <div class="card mb-2 tree-comp">
        <div class="card-body py-2">
          <div class="d-flex align-items-center justify-content-between tree-header" data-bs-toggle="collapse" data-bs-target="#comp-{{ $comp->id }}">
            <div class="d-flex align-items-center gap-2">
              <i class="ti tabler-chevron-down text-muted" style="font-size:.8rem"></i>
              <span class="badge bg-info badge-orden">C{{ $loop->iteration }}</span>
              @if($comp->icono)<i class="ti {{ $comp->icono }} text-info"></i>@endif
              <span class="fw-semibold">{{ $comp->nombre }}</span>
              @if(!$comp->activo)<span class="badge bg-label-danger ms-1">Inactivo</span>@endif
              <span class="text-muted small ms-1">{{ $comp->preguntas->count() }} preguntas</span>
            </div>
            @can('componentes.editar')
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
                data-url="{{ route('adm-sci.componente.destroy', $comp) }}" data-nombre="{{ $comp->nombre }}" title="Eliminar componente">
                <i class="ti tabler-trash"></i></button>
            </div>
            @endcan
          </div>

          {{-- Preguntas --}}
          <div class="collapse show" id="comp-{{ $comp->id }}">
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
              @can('componentes.editar')
              <div class="d-flex gap-1 ms-2 flex-shrink-0">
                <button class="btn btn-icon btn-sm btn-label-success btn-editar-pregunta"
                  data-id="{{ $preg->id }}" data-nombre="{{ $preg->nombre }}"
                  data-link="{{ $preg->link_ficha }}" data-activo="{{ $preg->activo ? 1 : 0 }}" title="Editar">
                  <i class="ti tabler-edit"></i></button>
                <button class="btn btn-icon btn-sm btn-label-danger btn-eliminar"
                  data-url="{{ route('adm-sci.pregunta.destroy', $preg) }}" data-nombre="{{ $preg->nombre }}" title="Eliminar">
                  <i class="ti tabler-trash"></i></button>
              </div>
              @endcan
            </div>
            @endforeach
            @if($comp->preguntas->isEmpty())
            <div class="text-muted small py-2 px-3"><i class="ti tabler-info-circle me-1"></i>Sin preguntas. Agrega la primera.</div>
            @endif
          </div>
        </div>
      </div>
      @endforeach
      @if($eje->componentes->isEmpty())
      <div class="text-muted small py-2 px-2"><i class="ti tabler-info-circle me-1"></i>Sin componentes. Agrega el primero.</div>
      @endif
    </div>
  </div>
</div>
@empty
<div class="card"><div class="card-body text-center py-5 text-muted">
  <i class="ti tabler-sitemap icon-48px d-block mb-3"></i>
  <p class="mb-0">No hay ejes para el año {{ $anio }}. Crea el primero con el botón superior.</p>
</div></div>
@endforelse

@can('componentes.editar')
{{-- Modal Nuevo Eje --}}
<div class="modal fade" id="modalNuevoEje" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('adm-sci.eje.store') }}">@csrf
      <div class="modal-header"><h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nuevo Eje SCI</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" class="form-control" required autofocus></div>
          <div class="col-md-4"><label class="form-label">Año <span class="text-danger">*</span></label><input type="number" name="anio" class="form-control" value="{{ $anio }}" min="2020" max="2099" required></div>
          <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control" rows="2"></textarea></div>
          <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" checked><label class="form-check-label">Activo</label></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar</button></div>
    </form>
  </div></div>
</div>

{{-- Modal Editar Eje --}}
<div class="modal fade" id="modalEditarEje" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="formEditarEje">@csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Eje</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" id="edit_eje_nombre" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Año</label><input type="number" name="anio" id="edit_eje_anio" class="form-control" min="2020" max="2099" required></div>
          <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" id="edit_eje_descripcion" class="form-control" rows="2"></textarea></div>
          <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_eje_activo"><label class="form-check-label">Activo</label></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button></div>
    </form>
  </div></div>
</div>

{{-- Modal Nuevo Componente --}}
<div class="modal fade" id="modalNuevoComponente" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('adm-sci.componente.store') }}">@csrf
      <input type="hidden" name="eje_id" id="nuevo_comp_eje_id">
      <div class="modal-header"><h5 class="modal-title"><i class="ti tabler-layout-grid-add me-2"></i>Nuevo Componente</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" class="form-control" required autofocus></div>
          <div class="col-12"><label class="form-label">Ícono <span class="text-muted small">(clase tabler, ej: tabler-shield)</span></label><input type="text" name="icono" class="form-control" placeholder="tabler-puzzle"></div>
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
          <div class="col-12"><label class="form-label">Ícono</label><input type="text" name="icono" id="edit_comp_icono" class="form-control" placeholder="tabler-puzzle"></div>
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
    <form method="POST" action="{{ route('adm-sci.pregunta.store') }}">@csrf
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

  // Editar eje
  document.querySelectorAll('.btn-editar-eje').forEach(btn => {
    btn.addEventListener('click', function () {
      document.getElementById('formEditarEje').action = '/administracion/sci/eje/' + this.dataset.id;
      document.getElementById('edit_eje_nombre').value      = this.dataset.nombre;
      document.getElementById('edit_eje_descripcion').value = this.dataset.descripcion || '';
      document.getElementById('edit_eje_anio').value        = this.dataset.anio;
      document.getElementById('edit_eje_activo').checked    = this.dataset.activo === '1';
      new bootstrap.Modal(document.getElementById('modalEditarEje')).show();
    });
  });

  // Editar componente
  document.querySelectorAll('.btn-editar-componente').forEach(btn => {
    btn.addEventListener('click', function () {
      document.getElementById('formEditarComponente').action = '/administracion/sci/componente/' + this.dataset.id;
      document.getElementById('edit_comp_nombre').value      = this.dataset.nombre;
      document.getElementById('edit_comp_icono').value       = this.dataset.icono || '';
      document.getElementById('edit_comp_descripcion').value = this.dataset.descripcion || '';
      document.getElementById('edit_comp_activo').checked    = this.dataset.activo === '1';
      new bootstrap.Modal(document.getElementById('modalEditarComponente')).show();
    });
  });

  // Editar pregunta
  document.querySelectorAll('.btn-editar-pregunta').forEach(btn => {
    btn.addEventListener('click', function () {
      document.getElementById('formEditarPregunta').action = '/administracion/sci/pregunta/' + this.dataset.id;
      document.getElementById('edit_preg_nombre').value = this.dataset.nombre;
      document.getElementById('edit_preg_link').value   = this.dataset.link || '';
      document.getElementById('edit_preg_activo').checked = this.dataset.activo === '1';
      new bootstrap.Modal(document.getElementById('modalEditarPregunta')).show();
    });
  });

});
</script>
@endsection
