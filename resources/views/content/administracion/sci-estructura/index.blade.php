@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Estructura SCI - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
@endsection

@section('page-style')
<style>
/* ══ Layout dos paneles ══ */
.sci2-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 1.25rem;
  align-items: start;
}
@media (max-width: 991px) { .sci2-layout { grid-template-columns: 1fr; } }

/* ══ Panel izquierdo: ejes ══ */
.sci2-ejes-panel {
  background: var(--bs-body-bg);
  border: 1px solid var(--bs-border-color);
  border-radius: .625rem;
  overflow: hidden;
  position: sticky;
  top: 80px;
}
.sci2-ejes-header {
  padding: .65rem 1rem;
  background: rgba(var(--bs-primary-rgb), .1);
  border-bottom: 1px solid var(--bs-border-color);
  display: flex; align-items: center; justify-content: space-between;
  font-weight: 700; font-size: .72rem; letter-spacing: .07em; text-transform: uppercase;
  color: var(--bs-primary);
}
.sci2-eje-item {
  display: flex; align-items: center; gap: .6rem;
  padding: .6rem 1rem;
  border-bottom: 1px solid var(--bs-border-color);
  cursor: pointer;
  transition: background .15s;
}
.sci2-eje-item:last-child { border-bottom: none; }
.sci2-eje-item:hover { background: var(--bs-tertiary-bg); }
.sci2-eje-item.activa-tab {
  background: rgba(var(--bs-primary-rgb), .08);
  box-shadow: inset 3px 0 0 var(--bs-primary);
  font-weight: 600;
}
.sci2-eje-num {
  flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%;
  background: var(--bs-primary); color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: .65rem; font-weight: 700;
}
.sci2-eje-nombre { flex: 1; font-size: .84rem; min-width: 0; line-height: 1.25; }
.sci2-eje-off { opacity: .5; }
.sci2-eje-actions { display: flex; gap: .2rem; flex-shrink: 0; }
.sci2-ejes-footer { padding: .5rem .75rem; border-top: 1px solid var(--bs-border-color); }

/* ══ Panel derecho ══ */
.sci2-content-panel { min-width: 0; }
.sci2-content-empty {
  border: 1px dashed var(--bs-border-color);
  border-radius: .625rem;
  padding: 3rem 1rem;
  text-align: center;
  color: var(--bs-secondary-color);
}

/* ── Título eje ── */
.sci2-eje-titulo {
  display: flex; align-items: center; gap: .75rem;
  margin-bottom: 1rem; padding-bottom: .75rem;
  border-bottom: 2px solid rgba(var(--bs-primary-rgb), .2);
}
.sci2-eje-titulo-badge {
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--bs-primary); color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: .8rem; flex-shrink: 0;
}
.sci2-eje-titulo h5 { margin: 0; font-size: 1.05rem; }

/* ── Componente card ── */
.sci2-comp-card {
  border: 1px solid var(--bs-border-color);
  border-radius: .5rem;
  margin-bottom: .875rem;
  overflow: hidden;
  transition: box-shadow .15s;
}
.sci2-comp-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,.06); }

.sci2-comp-header {
  display: flex; align-items: center; gap: .6rem;
  padding: .6rem .875rem;
  background: rgba(var(--bs-info-rgb), .07);
  border-bottom: 1px solid rgba(var(--bs-info-rgb), .15);
  cursor: pointer; user-select: none;
}
.sci2-comp-header:hover { background: rgba(var(--bs-info-rgb), .12); }
.sci2-comp-icono { color: var(--bs-info); font-size: 1rem; flex-shrink: 0; }
.sci2-comp-nombre { flex: 1; font-weight: 600; font-size: .875rem; min-width: 0; }
.sci2-comp-count {
  font-size: .68rem; color: var(--bs-info);
  background: rgba(var(--bs-info-rgb), .12);
  border-radius: 50rem; padding: .1em .55em; flex-shrink: 0;
}
.sci2-comp-actions { display: flex; gap: .2rem; flex-shrink: 0; }
.sci2-comp-chevron { flex-shrink: 0; color: var(--bs-secondary-color); transition: transform .2s; }
.sci2-comp-chevron.open { transform: rotate(180deg); }

/* ── Preguntas ── */
.sci2-comp-body { display: none; }
.sci2-comp-body.open { display: block; }

.sci2-preg-row {
  display: flex; align-items: flex-start; gap: .6rem;
  padding: .5rem .875rem;
  border-bottom: 1px solid var(--bs-border-color);
  font-size: .8125rem;
  transition: background .1s;
}
.sci2-preg-row:last-of-type { border-bottom: none; }
.sci2-preg-row:hover { background: var(--bs-tertiary-bg); }
.sci2-preg-num {
  flex-shrink: 0; width: 18px; height: 18px; border-radius: 50%;
  background: var(--bs-success); color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: .6rem; font-weight: 700; margin-top: 2px;
}
.sci2-preg-body { flex: 1; min-width: 0; }
.sci2-preg-nombre { white-space: normal; word-break: break-word; line-height: 1.35; }
.sci2-preg-link { font-size: .73rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; margin-top: .1rem; }
.sci2-preg-actions { display: flex; gap: .2rem; flex-shrink: 0; align-items: flex-start; margin-top: 1px; }

.sci2-preg-add {
  display: flex; align-items: center; gap: .4rem;
  padding: .4rem .875rem;
  background: transparent; border: none;
  border-top: 1px dashed rgba(var(--bs-success-rgb), .3);
  color: var(--bs-success); font-size: .77rem;
  cursor: pointer; width: 100%; text-align: left;
  transition: background .15s;
}
.sci2-preg-add:hover { background: rgba(var(--bs-success-rgb), .06); }

.sci2-comp-add {
  display: flex; align-items: center; gap: .4rem;
  padding: .45rem .875rem;
  background: rgba(var(--bs-info-rgb), .05);
  border: 1px dashed rgba(var(--bs-info-rgb), .3);
  border-radius: .5rem; color: var(--bs-info);
  font-size: .77rem; cursor: pointer; width: 100%;
  transition: background .15s; margin-top: .25rem;
}
.sci2-comp-add:hover { background: rgba(var(--bs-info-rgb), .1); }

/* ── Drag & drop ── */
.drag-handle {
  cursor: grab; flex-shrink: 0;
  color: var(--bs-secondary-color);
  opacity: .4; font-size: .95rem;
  padding: 0 2px; transition: opacity .15s;
}
.drag-handle:hover { opacity: .85; }
.drag-handle:active { cursor: grabbing; }
.sortable-ghost {
  opacity: .35;
  background: rgba(var(--bs-primary-rgb), .06) !important;
  border: 1px dashed var(--bs-primary) !important;
}
.sortable-drag { opacity: .95; box-shadow: 0 6px 20px rgba(0,0,0,.15); }

.badge-off { font-size: .58rem; padding: .12em .4em; vertical-align: middle; }

/* ── Icon picker ── */
.icon-picker { display: flex; flex-wrap: wrap; gap: .375rem; max-height: 180px; overflow-y: auto; padding: .5rem; background: var(--bs-tertiary-bg); border-radius: .375rem; border: 1px solid var(--bs-border-color); }
.icon-picker-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: .375rem; border: 1px solid transparent; cursor: pointer; background: var(--bs-body-bg); transition: all .15s; font-size: 1.1rem; }
.icon-picker-btn:hover { border-color: var(--bs-info); background: rgba(var(--bs-info-rgb),.1); }
.icon-picker-btn.selected { border-color: var(--bs-info); background: rgba(var(--bs-info-rgb),.2); box-shadow: 0 0 0 2px rgba(var(--bs-info-rgb),.3); }

@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin .8s linear infinite; display: inline-block; }
</style>
@endsection

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Estructura SCI</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
  <div>
    <h4 class="mb-1"><i class="ti tabler-shield-half me-2 text-primary"></i>Control Interno (SCI)</h4>
    <p class="mb-0 text-muted small">Gestiona Ejes → Componentes → Preguntas del modelo SCI</p>
  </div>
  <form method="GET" class="d-flex align-items-center gap-2">
    <label class="form-label mb-0 text-muted small">Año</label>
    <select name="anio" class="form-select form-select-sm" style="width:90px" onchange="this.form.submit()">
      @foreach($anios->merge([now()->year])->unique()->sortDesc() as $a)
        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
      @endforeach
    </select>
  </form>
</div>

<div class="sci2-layout">

  {{-- ══ PANEL IZQUIERDO: EJES ══ --}}
  <div class="sci2-ejes-panel">
    <div class="sci2-ejes-header">
      <span><i class="ti tabler-list me-1"></i>Ejes</span>
      <span class="badge bg-primary" style="font-size:.65rem">{{ $ejes->count() }}</span>
    </div>

    <div id="lista-ejes">
      @forelse($ejes as $idx => $eje)
      <div class="sci2-eje-item {{ $idx === 0 ? 'activa-tab' : '' }} {{ !$eje->activo ? 'sci2-eje-off' : '' }}"
           id="eje-tab-{{ $eje->id }}"
           data-eje-id="{{ $eje->id }}"
           data-eje-nombre="{{ e($eje->nombre) }}"
           data-eje-descripcion="{{ e($eje->descripcion ?? '') }}"
           data-eje-anio="{{ $eje->anio }}"
           data-eje-activo="{{ $eje->activo ? 1 : 0 }}"
           onclick="seleccionarEje({{ $eje->id }})">

        <span class="sci2-eje-num">{{ $idx + 1 }}</span>
        <span class="sci2-eje-nombre">
          {{ $eje->nombre }}
          @if(!$eje->activo)
            <span class="badge bg-label-danger badge-off d-block mt-1" style="width:fit-content">Inactivo</span>
          @endif
        </span>

        @can('componentes.editar')
        <div onclick="event.stopPropagation()" title="{{ $eje->activo ? 'Desactivar' : 'Activar' }} eje">
          <div class="form-check form-switch mb-0">
            <input class="form-check-input toggle-eje" type="checkbox"
                   {{ $eje->activo ? 'checked' : '' }}
                   data-eje-id="{{ $eje->id }}"
                   data-url="{{ route('adm-sci.eje.toggle', $eje) }}">
          </div>
        </div>
        @endcan

        @canany(['componentes.editar','componentes.eliminar'])
        <div class="sci2-eje-actions" onclick="event.stopPropagation()">
          @can('componentes.editar')
          <button class="btn btn-icon btn-xs btn-primary btn-editar-eje" title="Editar" style="width:24px;height:24px">
            <i class="ti tabler-edit" style="font-size:.75rem"></i>
          </button>
          @endcan
          @can('componentes.eliminar')
          <button class="btn btn-icon btn-xs btn-danger btn-eliminar-eje" title="Eliminar"
            data-url="{{ route('adm-sci.eje.destroy', $eje) }}"
            data-nombre="{{ e($eje->nombre) }}"
            style="width:24px;height:24px">
            <i class="ti tabler-trash" style="font-size:.75rem"></i>
          </button>
          @endcan
        </div>
        @endcanany
      </div>
      @empty
      <div class="p-3 text-center text-muted" style="font-size:.82rem">
        <i class="ti tabler-list d-block mb-1" style="font-size:1.5rem;opacity:.3"></i>
        Sin ejes para {{ $anio }}
      </div>
      @endforelse
    </div>

    <div class="sci2-ejes-footer">
      @can('componentes.crear')
      <button class="btn btn-sm btn-primary w-100" id="btn-nuevo-eje-open">
        <i class="ti tabler-plus me-1"></i>Nuevo Eje
      </button>
      @endcan
    </div>
  </div>

  {{-- ══ PANEL DERECHO: CONTENIDO DE EJE ══ --}}
  <div class="sci2-content-panel" id="sci2-content-panel">

    @if($ejes->isEmpty())
    <div class="sci2-content-empty">
      <i class="ti tabler-shield-half d-block mb-2" style="font-size:2.5rem;opacity:.2"></i>
      <p class="mb-0">Crea un eje para comenzar a estructurar el SCI.</p>
    </div>
    @else
    @foreach($ejes as $idx => $eje)
    <div class="sci2-eje-content {{ $idx !== 0 ? 'd-none' : '' }}"
         id="content-eje-{{ $eje->id }}">

      <div class="sci2-eje-titulo">
        <span class="sci2-eje-titulo-badge">{{ $idx + 1 }}</span>
        <div>
          <h5>{{ $eje->nombre }}</h5>
          @if($eje->descripcion)
            <p class="mb-0 text-muted" style="font-size:.78rem">{{ $eje->descripcion }}</p>
          @endif
        </div>
        <span class="ms-auto badge {{ $eje->activo ? 'bg-label-success' : 'bg-label-danger' }}" style="font-size:.72rem">
          {{ $eje->activo ? 'Activo' : 'Inactivo' }}
        </span>
      </div>

      @if($eje->componentes->isEmpty())
        <div class="sci2-content-empty mb-3">
          <i class="ti tabler-puzzle d-block mb-1" style="font-size:1.8rem;opacity:.25"></i>
          <p class="mb-0 small">Sin componentes. Agrega el primero.</p>
        </div>
      @else
        @foreach($eje->componentes as $cIdx => $comp)
        <div class="sci2-comp-card"
             id="sci2-comp-card-{{ $comp->id }}"
             data-comp-id="{{ $comp->id }}"
             data-comp-nombre="{{ e($comp->nombre) }}"
             data-comp-icono="{{ e($comp->icono ?? '') }}"
             data-comp-descripcion="{{ e($comp->descripcion ?? '') }}"
             data-comp-activo="{{ $comp->activo ? 1 : 0 }}"
             data-eje-id="{{ $eje->id }}">

          <div class="sci2-comp-header" onclick="toggleComp2({{ $comp->id }})">
            @can('componentes.editar')
            <i class="ti tabler-grip-vertical drag-handle comp-drag-handle" title="Arrastrar para reordenar" onclick="event.stopPropagation()"></i>
            @endcan
            @if($comp->icono)
              <i class="ti {{ $comp->icono }} sci2-comp-icono"></i>
            @else
              <i class="ti tabler-puzzle sci2-comp-icono" style="opacity:.35"></i>
            @endif
            <span class="sci2-comp-nombre">
              {{ $comp->nombre }}
              @if(!$comp->activo)<span class="badge bg-label-danger badge-off ms-1">Off</span>@endif
            </span>
            <span class="sci2-comp-count" id="sci2-comp-count-{{ $comp->id }}">{{ $comp->preguntas->count() }}p</span>
            @canany(['componentes.editar','componentes.eliminar'])
            <div class="sci2-comp-actions" onclick="event.stopPropagation()">
              @can('componentes.editar')
              <button class="btn btn-icon btn-xs btn-info btn-editar-comp" title="Editar componente" style="width:26px;height:26px">
                <i class="ti tabler-edit" style="font-size:.78rem"></i>
              </button>
              @endcan
              @can('componentes.eliminar')
              <button class="btn btn-icon btn-xs btn-danger btn-eliminar-comp" title="Eliminar componente"
                data-url="{{ route('adm-sci.componente.destroy', $comp) }}"
                data-nombre="{{ e($comp->nombre) }}"
                style="width:26px;height:26px">
                <i class="ti tabler-trash" style="font-size:.78rem"></i>
              </button>
              @endcan
            </div>
            @endcanany
            <i class="ti tabler-chevron-down sci2-comp-chevron open" id="sci2-comp-chevron-{{ $comp->id }}"></i>
          </div>

          <div class="sci2-comp-body open" id="sci2-comp-body-{{ $comp->id }}">
            @forelse($comp->preguntas as $pIdx => $preg)
            <div class="sci2-preg-row"
                 id="sci2-preg-row-{{ $preg->id }}"
                 data-preg-id="{{ $preg->id }}"
                 data-preg-nombre="{{ e($preg->nombre) }}"
                 data-preg-link="{{ e($preg->link_ficha ?? '') }}"
                 data-preg-activo="{{ $preg->activo ? 1 : 0 }}"
                 data-url-destroy="{{ route('adm-sci.pregunta.destroy', $preg) }}">
              @can('componentes.editar')
              <i class="ti tabler-grip-vertical drag-handle preg-drag-handle" title="Arrastrar para reordenar"></i>
              @endcan
              <span class="sci2-preg-num">{{ $pIdx + 1 }}</span>
              <div class="sci2-preg-body">
                <span class="sci2-preg-nombre">
                  {{ $preg->nombre }}
                  @if(!$preg->activo)<span class="badge bg-label-danger badge-off ms-1">Off</span>@endif
                </span>
                @if($preg->link_ficha)
                  <a href="{{ $preg->link_ficha }}" target="_blank" class="sci2-preg-link text-info">
                    <i class="ti tabler-link" style="font-size:.7rem"></i> {{ $preg->link_ficha }}
                  </a>
                @else
                  <span class="sci2-preg-link text-muted"><i class="ti tabler-link-off" style="font-size:.7rem"></i> Sin ficha</span>
                @endif
              </div>
              @canany(['componentes.editar','componentes.eliminar'])
              <div class="sci2-preg-actions">
                @can('componentes.editar')
                <button class="btn btn-icon btn-xs btn-success btn-editar-preg" title="Editar" style="width:26px;height:26px">
                  <i class="ti tabler-edit" style="font-size:.75rem"></i>
                </button>
                @endcan
                @can('componentes.eliminar')
                <button class="btn btn-icon btn-xs btn-danger btn-eliminar-preg" title="Eliminar"
                  data-nombre="{{ e($preg->nombre) }}"
                  style="width:26px;height:26px">
                  <i class="ti tabler-trash" style="font-size:.75rem"></i>
                </button>
                @endcan
              </div>
              @endcanany
            </div>
            @empty
            <div class="p-3 text-muted text-center" style="font-size:.78rem" id="sci2-preg-empty-{{ $comp->id }}">
              <i class="ti tabler-help-circle me-1 opacity-50"></i>Sin preguntas.
            </div>
            @endforelse
            @can('componentes.crear')
            <button class="sci2-preg-add btn-nueva-preg-inline" data-comp-id="{{ $comp->id }}">
              <i class="ti tabler-plus"></i> Nueva pregunta
            </button>
            @endcan
          </div>
        </div>
        @endforeach
      @endif

      @can('componentes.crear')
      <button class="sci2-comp-add btn-nuevo-comp-inline" data-eje-id="{{ $eje->id }}">
        <i class="ti tabler-plus"></i> Nuevo componente
      </button>
      @endcan
    </div>
    @endforeach
    @endif
  </div>

</div>

<div class="d-flex align-items-center gap-2 mt-3 flex-wrap" style="font-size:.74rem;color:var(--bs-secondary-color)">
  <span class="d-flex align-items-center gap-1"><span style="width:9px;height:9px;border-radius:50%;background:var(--bs-primary);display:inline-block"></span>Eje</span>
  <i class="ti tabler-chevron-right opacity-40"></i>
  <span class="d-flex align-items-center gap-1"><span style="width:9px;height:9px;border-radius:50%;background:var(--bs-info);display:inline-block"></span>Componente</span>
  <i class="ti tabler-chevron-right opacity-40"></i>
  <span class="d-flex align-items-center gap-1"><span style="width:9px;height:9px;border-radius:50%;background:var(--bs-success);display:inline-block"></span>Pregunta → genera actividades de cumplimiento</span>
  <span class="ms-auto">Arrastra <i class="ti tabler-grip-vertical"></i> para reordenar. El orden se guarda automáticamente.</span>
</div>

@canany(['componentes.crear','componentes.editar'])
{{-- MODAL: NUEVO EJE --}}
<div class="modal fade" id="modalNuevoEje" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="ti tabler-list me-2"></i>Nuevo Eje</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-8">
            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
            <input type="text" id="nuevo_eje_nombre" class="form-control" placeholder="Ej: Ambiente de Control">
          </div>
          <div class="col-4">
            <label class="form-label fw-semibold">Año</label>
            <input type="number" id="nuevo_eje_anio" class="form-control" value="{{ $anio }}" min="2020" max="2099">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="nuevo_eje_descripcion" class="form-control" rows="2" placeholder="Opcional..."></textarea>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="nuevo_eje_activo" checked>
              <label class="form-check-label" for="nuevo_eje_activo">Activo</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-guardar-eje"><i class="ti tabler-device-floppy me-1"></i>Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: EDITAR EJE --}}
<div class="modal fade" id="modalEditarEje" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Eje</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-8">
            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
            <input type="text" id="edit_eje_nombre" class="form-control">
          </div>
          <div class="col-4">
            <label class="form-label fw-semibold">Año</label>
            <input type="number" id="edit_eje_anio" class="form-control" min="2020" max="2099">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="edit_eje_descripcion" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="edit_eje_activo">
              <label class="form-check-label" for="edit_eje_activo">Activo</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-info" id="btn-actualizar-eje"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: NUEVO COMPONENTE --}}
<div class="modal fade" id="modalNuevoComponente" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="ti tabler-puzzle me-2"></i>Nuevo Componente</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
            <input type="text" id="nuevo_comp_nombre" class="form-control" placeholder="Ej: Integridad y valores éticos">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold d-flex align-items-center gap-2">
              Ícono
              <span class="ms-auto d-flex align-items-center gap-1">
                <i class="ti text-info fs-5" id="nuevo_comp_icono_preview"></i>
                <span class="text-muted small" id="nuevo_comp_icono_label">Sin ícono</span>
              </span>
            </label>
            <input type="hidden" id="nuevo_comp_icono">
            @php $iconosList = ['tabler-crown','tabler-shield-check','tabler-chart-pie','tabler-chart-bar','tabler-clipboard-list','tabler-alert-triangle','tabler-messages','tabler-message-circle','tabler-eye','tabler-speakerphone','tabler-activity','tabler-user-check','tabler-users','tabler-building','tabler-file-certificate','tabler-scale','tabler-lock','tabler-target','tabler-trending-up','tabler-checkup-list','tabler-puzzle','tabler-compass','tabler-flag','tabler-microscope'] @endphp
            <div class="icon-picker" id="nuevo_icon_picker">
              @foreach($iconosList as $ico)
              <button type="button" class="icon-picker-btn" data-icon="{{ $ico }}" data-target="nuevo_comp_icono" data-preview="nuevo_comp_icono_preview" data-label="nuevo_comp_icono_label" data-picker="nuevo_icon_picker" title="{{ $ico }}">
                <i class="ti {{ $ico }}"></i>
              </button>
              @endforeach
            </div>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="nuevo_comp_descripcion" class="form-control" rows="2" placeholder="Opcional..."></textarea>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="nuevo_comp_activo" checked>
              <label class="form-check-label" for="nuevo_comp_activo">Activo</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-info" id="btn-guardar-comp"><i class="ti tabler-device-floppy me-1"></i>Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: EDITAR COMPONENTE --}}
<div class="modal fade" id="modalEditarComponente" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Componente</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
            <input type="text" id="edit_comp_nombre" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold d-flex align-items-center gap-2">
              Ícono
              <span class="ms-auto d-flex align-items-center gap-1">
                <i class="ti text-info fs-5" id="edit_comp_icono_preview"></i>
                <span class="text-muted small" id="edit_comp_icono_label">Sin ícono</span>
              </span>
            </label>
            <input type="hidden" id="edit_comp_icono">
            <div class="icon-picker" id="edit_icon_picker">
              @foreach($iconosList as $ico)
              <button type="button" class="icon-picker-btn" data-icon="{{ $ico }}" data-target="edit_comp_icono" data-preview="edit_comp_icono_preview" data-label="edit_comp_icono_label" data-picker="edit_icon_picker" title="{{ $ico }}">
                <i class="ti {{ $ico }}"></i>
              </button>
              @endforeach
            </div>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="edit_comp_descripcion" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="edit_comp_activo">
              <label class="form-check-label" for="edit_comp_activo">Activo</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-info" id="btn-actualizar-comp"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: NUEVA PREGUNTA --}}
<div class="modal fade" id="modalNuevaPregunta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="ti tabler-help-circle me-2"></i>Nueva Pregunta</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold">Enunciado <span class="text-danger">*</span></label>
            <textarea id="nueva_preg_nombre" class="form-control" rows="4" maxlength="1000" placeholder="¿La entidad cuenta con...?"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Link de ficha <span class="text-muted fw-normal small">(URL opcional)</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti tabler-link"></i></span>
              <input type="url" id="nueva_preg_link" class="form-control" placeholder="https://" maxlength="1000">
            </div>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="nueva_preg_activo" checked>
              <label class="form-check-label" for="nueva_preg_activo">Activa</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btn-guardar-preg"><i class="ti tabler-device-floppy me-1"></i>Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: EDITAR PREGUNTA --}}
<div class="modal fade" id="modalEditarPregunta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Pregunta</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold">Enunciado <span class="text-danger">*</span></label>
            <textarea id="edit_preg_nombre" class="form-control" rows="4" maxlength="1000"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Link de ficha</label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti tabler-link"></i></span>
              <input type="url" id="edit_preg_link" class="form-control" placeholder="https://" maxlength="1000">
            </div>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="edit_preg_activo">
              <label class="form-check-label" for="edit_preg_activo">Activa</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btn-actualizar-preg"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button>
      </div>
    </div>
  </div>
</div>
@endcanany
@endsection

@section('page-script')
<script>
(function () {
  'use strict';
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;
  const H = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF };

  let pendingEjeId  = null;
  let pendingCompId = null;
  let editEjeId     = null;
  let editCompId    = null;
  let editPregId    = null;

  function toast(type, msg) {
    if (typeof pulsoToast === 'function') { pulsoToast(msg, type); return; }
    if (typeof Swal !== 'undefined') {
      const c = { success:'#28c76f', error:'#ea5455', warning:'#ff9f43', info:'#00cfe8' };
      Swal.fire({ toast:true, position:'top-end', icon:type, title:msg,
        showConfirmButton:false, timer:2800, timerProgressBar:true,
        customClass:{popup:'pulso-toast'}, iconColor:c[type]||c.info });
    }
  }
  async function api(url, method, body) {
    const opts = { method, headers: H };
    if (body) opts.body = JSON.stringify(body);
    const r = await fetch(url, opts);
    const j = await r.json();
    if (!r.ok) {
      if (j.errors) { const f = Object.values(j.errors)[0]; throw new Error(Array.isArray(f)?f[0]:f); }
      throw new Error(j.message || 'Error del servidor');
    }
    return j;
  }
  function esc(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }
  function getModal(id) { return bootstrap.Modal.getOrCreateInstance(document.getElementById(id)); }
  function hideModal(id) { getModal(id).hide(); }

  /* ══ Seleccionar eje ══ */
  window.seleccionarEje = function(ejeId) {
    document.querySelectorAll('.sci2-eje-item').forEach(t => t.classList.remove('activa-tab'));
    document.querySelectorAll('.sci2-eje-content').forEach(c => c.classList.add('d-none'));
    document.getElementById('eje-tab-' + ejeId)?.classList.add('activa-tab');
    document.getElementById('content-eje-' + ejeId)?.classList.remove('d-none');
  };

  /* ══ Toggle acordeón componente ══ */
  window.toggleComp2 = function(compId) {
    const body    = document.getElementById('sci2-comp-body-' + compId);
    const chevron = document.getElementById('sci2-comp-chevron-' + compId);
    body?.classList.toggle('open');
    chevron?.classList.toggle('open');
  };

  /* ══ Toggle activo eje ══ */
  document.querySelectorAll('.toggle-eje').forEach(chk => {
    chk.addEventListener('change', async function () {
      const ejeId = this.dataset.ejeId;
      const prev  = this.checked;
      this.disabled = true;
      try {
        const res = await api(this.dataset.url, 'PATCH');
        const tab = document.getElementById('eje-tab-' + ejeId);
        if (tab) {
          tab.classList.toggle('sci2-eje-off', !res.activo);
          const nombreEl = tab.querySelector('.sci2-eje-nombre');
          let badge = nombreEl.querySelector('.badge');
          if (!res.activo) {
            if (!badge) { badge = document.createElement('span'); badge.className='badge bg-label-danger badge-off d-block mt-1'; badge.style.width='fit-content'; nombreEl.appendChild(badge); }
            badge.textContent = 'Inactivo';
          } else { badge?.remove(); }
        }
        const cont = document.getElementById('content-eje-' + ejeId);
        if (cont) {
          const b = cont.querySelector('.sci2-eje-titulo .badge');
          if (b) { b.textContent = res.activo ? 'Activo' : 'Inactivo'; b.className = `ms-auto badge ${res.activo?'bg-label-success':'bg-label-danger'}`; b.style.fontSize='.72rem'; }
        }
        this.checked = res.activo; this.disabled = false;
        toast('success', res.message);
      } catch (err) { this.checked = prev; this.disabled = false; toast('error', err.message); }
    });
  });

  /* ══ Ejes: editar / eliminar ══ */
  function bindEjeTab(tab) {
    tab.querySelector('.btn-editar-eje')?.addEventListener('click', e => {
      e.stopPropagation();
      editEjeId = tab.dataset.ejeId;
      document.getElementById('edit_eje_nombre').value      = tab.dataset.ejeNombre;
      document.getElementById('edit_eje_descripcion').value = tab.dataset.ejeDescripcion;
      document.getElementById('edit_eje_anio').value        = tab.dataset.ejeAnio;
      document.getElementById('edit_eje_activo').checked    = tab.dataset.ejeActivo === '1';
      getModal('modalEditarEje').show();
    });
    tab.querySelector('.btn-eliminar-eje')?.addEventListener('click', e => {
      e.stopPropagation();
      const btn = tab.querySelector('.btn-eliminar-eje');
      confirmarEliminar(btn.dataset.url, tab.dataset.ejeNombre, () => {
        tab.remove();
        document.getElementById('content-eje-' + tab.dataset.ejeId)?.remove();
        const primera = document.querySelector('.sci2-eje-item');
        if (primera) seleccionarEje(primera.dataset.ejeId);
        toast('success', 'Eje eliminado.');
      });
    });
  }
  document.querySelectorAll('.sci2-eje-item').forEach(bindEjeTab);

  document.getElementById('btn-nuevo-eje-open')?.addEventListener('click', () => {
    document.getElementById('nuevo_eje_nombre').value      = '';
    document.getElementById('nuevo_eje_descripcion').value = '';
    document.getElementById('nuevo_eje_activo').checked    = true;
    getModal('modalNuevoEje').show();
  });

  document.getElementById('btn-guardar-eje')?.addEventListener('click', async () => {
    const nombre = document.getElementById('nuevo_eje_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    const btn = document.getElementById('btn-guardar-eje');
    btn.disabled = true;
    try {
      const res = await api('{{ route("adm-sci.eje.store") }}', 'POST', {
        nombre, descripcion: document.getElementById('nuevo_eje_descripcion').value,
        anio: document.getElementById('nuevo_eje_anio').value,
        activo: document.getElementById('nuevo_eje_activo').checked ? 1 : 0,
      });
      hideModal('modalNuevoEje');
      const lista = document.getElementById('lista-ejes');
      lista.querySelector('.p-3.text-center')?.remove();
      const num = document.querySelectorAll('.sci2-eje-item').length + 1;
      const tab = crearEjeTab(res.eje, num);
      lista.appendChild(tab);
      bindEjeTab(tab);
      const panel = document.getElementById('sci2-content-panel');
      panel.querySelector('.sci2-content-empty')?.remove();
      panel.appendChild(crearEjeContent(res.eje, num));
      seleccionarEje(res.eje.id);
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  document.getElementById('btn-actualizar-eje')?.addEventListener('click', async () => {
    const nombre = document.getElementById('edit_eje_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    const btn = document.getElementById('btn-actualizar-eje');
    btn.disabled = true;
    try {
      const res = await api(`/administracion/sci/eje/${editEjeId}`, 'PUT', {
        nombre, descripcion: document.getElementById('edit_eje_descripcion').value,
        anio: document.getElementById('edit_eje_anio').value,
        activo: document.getElementById('edit_eje_activo').checked ? 1 : 0,
      });
      hideModal('modalEditarEje');
      const tab = document.getElementById('eje-tab-' + editEjeId);
      if (tab) {
        tab.dataset.ejeNombre      = res.eje.nombre;
        tab.dataset.ejeDescripcion = res.eje.descripcion || '';
        tab.dataset.ejeAnio        = res.eje.anio;
        tab.dataset.ejeActivo      = res.eje.activo ? '1' : '0';
        tab.classList.toggle('sci2-eje-off', !res.eje.activo);
        const nombreEl = tab.querySelector('.sci2-eje-nombre');
        nombreEl.childNodes[0].textContent = res.eje.nombre;
        let badge = nombreEl.querySelector('.badge');
        if (!res.eje.activo) {
          if (!badge) { badge=document.createElement('span'); badge.className='badge bg-label-danger badge-off d-block mt-1'; badge.style.width='fit-content'; nombreEl.appendChild(badge); }
          badge.textContent='Inactivo';
        } else { badge?.remove(); }
        tab.querySelector('.toggle-eje').checked = res.eje.activo;
      }
      const cont = document.getElementById('content-eje-' + editEjeId);
      if (cont) {
        cont.querySelector('.sci2-eje-titulo h5').textContent = res.eje.nombre;
        const b = cont.querySelector('.sci2-eje-titulo .badge');
        if (b) { b.textContent = res.eje.activo?'Activo':'Inactivo'; b.className=`ms-auto badge ${res.eje.activo?'bg-label-success':'bg-label-danger'}`; b.style.fontSize='.72rem'; }
      }
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  function crearEjeTab(eje, num) {
    const div = document.createElement('div');
    div.className = 'sci2-eje-item' + (!eje.activo?' sci2-eje-off':'');
    div.id = 'eje-tab-' + eje.id;
    div.dataset.ejeId          = eje.id;
    div.dataset.ejeNombre      = eje.nombre;
    div.dataset.ejeDescripcion = eje.descripcion || '';
    div.dataset.ejeAnio        = eje.anio;
    div.dataset.ejeActivo      = eje.activo ? '1' : '0';
    div.setAttribute('onclick', `seleccionarEje(${eje.id})`);
    div.innerHTML = `
      <span class="sci2-eje-num">${num}</span>
      <span class="sci2-eje-nombre">${esc(eje.nombre)}</span>
      <div onclick="event.stopPropagation()">
        <div class="form-check form-switch mb-0">
          <input class="form-check-input toggle-eje" type="checkbox" ${eje.activo?'checked':''}
            data-eje-id="${eje.id}" data-url="/administracion/sci/eje/${eje.id}/toggle">
        </div>
      </div>
      <div class="sci2-eje-actions" onclick="event.stopPropagation()">
        <button class="btn btn-icon btn-xs btn-primary btn-editar-eje" style="width:24px;height:24px"><i class="ti tabler-edit" style="font-size:.75rem"></i></button>
        <button class="btn btn-icon btn-xs btn-danger btn-eliminar-eje" style="width:24px;height:24px"
          data-url="/administracion/sci/eje/${eje.id}" data-nombre="${esc(eje.nombre)}"><i class="ti tabler-trash" style="font-size:.75rem"></i></button>
      </div>`;
    div.querySelector('.toggle-eje').addEventListener('change', async function () {
      const prev = this.checked; this.disabled = true;
      try {
        const res = await api(this.dataset.url, 'PATCH');
        div.classList.toggle('sci2-eje-off', !res.activo);
        this.checked = res.activo; this.disabled = false;
        toast('success', res.message);
      } catch(err) { this.checked = prev; this.disabled = false; toast('error', err.message); }
    });
    return div;
  }

  function crearEjeContent(eje, num) {
    const div = document.createElement('div');
    div.className = 'sci2-eje-content d-none';
    div.id = 'content-eje-' + eje.id;
    div.innerHTML = `
      <div class="sci2-eje-titulo">
        <span class="sci2-eje-titulo-badge">${num}</span>
        <div><h5>${esc(eje.nombre)}</h5></div>
        <span class="ms-auto badge ${eje.activo?'bg-label-success':'bg-label-danger'}" style="font-size:.72rem">${eje.activo?'Activo':'Inactivo'}</span>
      </div>
      <div class="sci2-content-empty mb-3">
        <i class="ti tabler-puzzle d-block mb-1" style="font-size:1.8rem;opacity:.25"></i>
        <p class="mb-0 small">Sin componentes. Agrega el primero.</p>
      </div>
      <button class="sci2-comp-add btn-nuevo-comp-inline" data-eje-id="${eje.id}">
        <i class="ti tabler-plus"></i> Nuevo componente
      </button>`;
    div.querySelector('.btn-nuevo-comp-inline').addEventListener('click', function() {
      abrirModalNuevoComp(this.dataset.ejeId);
    });
    initSortableComps(div);
    return div;
  }

  /* ══ Componentes ══ */
  function bindCompCard(card) {
    card.querySelector('.btn-editar-comp')?.addEventListener('click', e => {
      e.stopPropagation();
      editCompId = card.dataset.compId;
      document.getElementById('edit_comp_nombre').value      = card.dataset.compNombre;
      document.getElementById('edit_comp_descripcion').value = card.dataset.compDescripcion;
      document.getElementById('edit_comp_activo').checked    = card.dataset.compActivo === '1';
      setIconPicker('edit_icon_picker','edit_comp_icono','edit_comp_icono_preview','edit_comp_icono_label', card.dataset.compIcono);
      getModal('modalEditarComponente').show();
    });
    card.querySelector('.btn-eliminar-comp')?.addEventListener('click', e => {
      e.stopPropagation();
      const btn = card.querySelector('.btn-eliminar-comp');
      confirmarEliminar(btn.dataset.url, btn.dataset.nombre, () => {
        card.remove(); toast('success', 'Componente eliminado.');
      });
    });
    card.querySelectorAll('.btn-nueva-preg-inline').forEach(b => {
      b.addEventListener('click', () => {
        pendingCompId = b.dataset.compId;
        document.getElementById('nueva_preg_nombre').value   = '';
        document.getElementById('nueva_preg_link').value     = '';
        document.getElementById('nueva_preg_activo').checked = true;
        getModal('modalNuevaPregunta').show();
      });
    });
    card.querySelectorAll('.sci2-preg-row').forEach(bindPregRow);
  }
  document.querySelectorAll('.sci2-comp-card').forEach(bindCompCard);
  document.querySelectorAll('.btn-nuevo-comp-inline').forEach(b => {
    b.addEventListener('click', () => abrirModalNuevoComp(b.dataset.ejeId));
  });

  function abrirModalNuevoComp(ejeId) {
    pendingEjeId = ejeId;
    document.getElementById('nuevo_comp_nombre').value      = '';
    document.getElementById('nuevo_comp_descripcion').value = '';
    document.getElementById('nuevo_comp_activo').checked    = true;
    setIconPicker('nuevo_icon_picker','nuevo_comp_icono','nuevo_comp_icono_preview','nuevo_comp_icono_label','');
    getModal('modalNuevoComponente').show();
  }

  document.getElementById('btn-guardar-comp')?.addEventListener('click', async () => {
    const nombre = document.getElementById('nuevo_comp_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    if (!pendingEjeId) { toast('warning', 'Eje no detectado.'); return; }
    const btn = document.getElementById('btn-guardar-comp');
    btn.disabled = true;
    try {
      const res = await api('{{ route("adm-sci.componente.store") }}', 'POST', {
        eje_id: pendingEjeId, nombre,
        icono:       document.getElementById('nuevo_comp_icono').value,
        descripcion: document.getElementById('nuevo_comp_descripcion').value,
        activo:      document.getElementById('nuevo_comp_activo').checked ? 1 : 0,
      });
      hideModal('modalNuevoComponente');
      const cont = document.getElementById('content-eje-' + pendingEjeId);
      if (cont) {
        cont.querySelector('.sci2-content-empty')?.remove();
        const btnComp = cont.querySelector('.btn-nuevo-comp-inline');
        const card = crearCompCard(res.componente, pendingEjeId);
        cont.insertBefore(card, btnComp);
        bindCompCard(card);
        initSortablePregs(card.querySelector('.sci2-comp-body'));
      }
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  document.getElementById('btn-actualizar-comp')?.addEventListener('click', async () => {
    const nombre = document.getElementById('edit_comp_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    const btn = document.getElementById('btn-actualizar-comp');
    btn.disabled = true;
    try {
      const res = await api(`/administracion/sci/componente/${editCompId}`, 'PUT', {
        nombre,
        icono:       document.getElementById('edit_comp_icono').value,
        descripcion: document.getElementById('edit_comp_descripcion').value,
        activo:      document.getElementById('edit_comp_activo').checked ? 1 : 0,
      });
      hideModal('modalEditarComponente');
      const card = document.getElementById('sci2-comp-card-' + editCompId);
      if (card) {
        card.dataset.compNombre      = res.componente.nombre;
        card.dataset.compIcono       = res.componente.icono || '';
        card.dataset.compDescripcion = res.componente.descripcion || '';
        card.dataset.compActivo      = res.componente.activo ? '1' : '0';
        const ico   = res.componente.icono;
        const count = card.querySelectorAll('.sci2-preg-row').length;
        const h     = card.querySelector('.sci2-comp-header');
        const open  = h.querySelector('.sci2-comp-chevron')?.classList.contains('open');
        h.innerHTML = `
          <i class="ti tabler-grip-vertical drag-handle comp-drag-handle" title="Arrastrar" onclick="event.stopPropagation()"></i>
          ${ico?`<i class="ti ${esc(ico)} sci2-comp-icono"></i>`:'<i class="ti tabler-puzzle sci2-comp-icono" style="opacity:.35"></i>'}
          <span class="sci2-comp-nombre">${esc(res.componente.nombre)}${!res.componente.activo?'<span class="badge bg-label-danger badge-off ms-1">Off</span>':''}</span>
          <span class="sci2-comp-count" id="sci2-comp-count-${editCompId}">${count}p</span>
          <div class="sci2-comp-actions" onclick="event.stopPropagation()">
            <button class="btn btn-icon btn-xs btn-info btn-editar-comp" style="width:26px;height:26px"><i class="ti tabler-edit" style="font-size:.78rem"></i></button>
            <button class="btn btn-icon btn-xs btn-danger btn-eliminar-comp" style="width:26px;height:26px"
              data-url="/administracion/sci/componente/${editCompId}" data-nombre="${esc(res.componente.nombre)}"><i class="ti tabler-trash" style="font-size:.78rem"></i></button>
          </div>
          <i class="ti tabler-chevron-down sci2-comp-chevron ${open?'open':''}" id="sci2-comp-chevron-${editCompId}"></i>`;
        h.setAttribute('onclick', `toggleComp2(${editCompId})`);
        bindCompCard(card);
      }
      toast('success', res.message);
    } catch(err) { toast('error', err.message); }
    btn.disabled = false;
  });

  function crearCompCard(c, ejeId) {
    const div = document.createElement('div');
    div.className = 'sci2-comp-card';
    div.id = 'sci2-comp-card-' + c.id;
    div.dataset.compId          = c.id;
    div.dataset.compNombre      = c.nombre;
    div.dataset.compIcono       = c.icono || '';
    div.dataset.compDescripcion = c.descripcion || '';
    div.dataset.compActivo      = c.activo ? '1' : '0';
    div.dataset.ejeId           = ejeId;
    div.innerHTML = `
      <div class="sci2-comp-header" onclick="toggleComp2(${c.id})">
        <i class="ti tabler-grip-vertical drag-handle comp-drag-handle" title="Arrastrar" onclick="event.stopPropagation()"></i>
        ${c.icono?`<i class="ti ${esc(c.icono)} sci2-comp-icono"></i>`:'<i class="ti tabler-puzzle sci2-comp-icono" style="opacity:.35"></i>'}
        <span class="sci2-comp-nombre">${esc(c.nombre)}</span>
        <span class="sci2-comp-count" id="sci2-comp-count-${c.id}">0p</span>
        <div class="sci2-comp-actions" onclick="event.stopPropagation()">
          <button class="btn btn-icon btn-xs btn-info btn-editar-comp" style="width:26px;height:26px"><i class="ti tabler-edit" style="font-size:.78rem"></i></button>
          <button class="btn btn-icon btn-xs btn-danger btn-eliminar-comp" style="width:26px;height:26px"
            data-url="/administracion/sci/componente/${c.id}" data-nombre="${esc(c.nombre)}"><i class="ti tabler-trash" style="font-size:.78rem"></i></button>
        </div>
        <i class="ti tabler-chevron-down sci2-comp-chevron open" id="sci2-comp-chevron-${c.id}"></i>
      </div>
      <div class="sci2-comp-body open" id="sci2-comp-body-${c.id}">
        <div class="p-3 text-muted text-center" id="sci2-preg-empty-${c.id}" style="font-size:.78rem">Sin preguntas.</div>
        <button class="sci2-preg-add btn-nueva-preg-inline" data-comp-id="${c.id}">
          <i class="ti tabler-plus"></i> Nueva pregunta
        </button>
      </div>`;
    return div;
  }

  /* ══ Preguntas ══ */
  function bindPregRow(row) {
    row.querySelector('.btn-editar-preg')?.addEventListener('click', e => {
      e.stopPropagation();
      editPregId    = row.dataset.pregId;
      pendingCompId = row.closest('.sci2-comp-card')?.dataset.compId;
      document.getElementById('edit_preg_nombre').value   = row.dataset.pregNombre;
      document.getElementById('edit_preg_link').value     = row.dataset.pregLink;
      document.getElementById('edit_preg_activo').checked = row.dataset.pregActivo === '1';
      getModal('modalEditarPregunta').show();
    });
    row.querySelector('.btn-eliminar-preg')?.addEventListener('click', e => {
      e.stopPropagation();
      confirmarEliminar(row.dataset.urlDestroy, row.dataset.pregNombre, () => {
        const card = row.closest('.sci2-comp-card');
        row.remove();
        if (card) { renumerarPregs2(card); actualizarCountComp2(card); }
        toast('success', 'Pregunta eliminada.');
      });
    });
  }
  document.querySelectorAll('.sci2-preg-row').forEach(bindPregRow);

  document.getElementById('btn-guardar-preg')?.addEventListener('click', async () => {
    const nombre = document.getElementById('nueva_preg_nombre').value.trim();
    if (!nombre)     { toast('warning', 'El enunciado es obligatorio.'); return; }
    if (!pendingCompId) { toast('warning', 'Componente no detectado.'); return; }
    const btn = document.getElementById('btn-guardar-preg');
    btn.disabled = true;
    try {
      const res = await api('{{ route("adm-sci.pregunta.store") }}', 'POST', {
        componente_id: pendingCompId,
        nombre,
        link_ficha: document.getElementById('nueva_preg_link').value || null,
        activo:     document.getElementById('nueva_preg_activo').checked ? 1 : 0,
      });
      hideModal('modalNuevaPregunta');
      const card = document.getElementById('sci2-comp-card-' + pendingCompId);
      if (card) {
        const body = document.getElementById('sci2-comp-body-' + pendingCompId);
        body?.querySelector(`#sci2-preg-empty-${pendingCompId}`)?.remove();
        const num  = card.querySelectorAll('.sci2-preg-row').length + 1;
        const row  = crearPregRow(res.pregunta, num);
        const add  = body?.querySelector('.btn-nueva-preg-inline');
        body?.insertBefore(row, add);
        bindPregRow(row);
        actualizarCountComp2(card);
      }
      toast('success', res.message);
    } catch(err) { toast('error', err.message); }
    btn.disabled = false;
  });

  document.getElementById('btn-actualizar-preg')?.addEventListener('click', async () => {
    const nombre = document.getElementById('edit_preg_nombre').value.trim();
    if (!nombre) { toast('warning', 'El enunciado es obligatorio.'); return; }
    const btn = document.getElementById('btn-actualizar-preg');
    btn.disabled = true;
    try {
      const res = await api(`/administracion/sci/pregunta/${editPregId}`, 'PUT', {
        nombre,
        link_ficha: document.getElementById('edit_preg_link').value || null,
        activo:     document.getElementById('edit_preg_activo').checked ? 1 : 0,
      });
      hideModal('modalEditarPregunta');
      const row = document.getElementById('sci2-preg-row-' + editPregId);
      if (row) {
        row.dataset.pregNombre = res.pregunta.nombre;
        row.dataset.pregLink   = res.pregunta.link_ficha || '';
        row.dataset.pregActivo = res.pregunta.activo ? '1' : '0';
        const num  = row.querySelector('.sci2-preg-num').outerHTML;
        const acts = row.querySelector('.sci2-preg-actions')?.outerHTML || '';
        const gh   = row.querySelector('.drag-handle')?.outerHTML || '';
        const lf   = res.pregunta.link_ficha;
        row.innerHTML = gh + num +
          `<div class="sci2-preg-body">
            <span class="sci2-preg-nombre">${esc(res.pregunta.nombre)}${!res.pregunta.activo?'<span class="badge bg-label-danger badge-off ms-1">Off</span>':''}</span>
            ${lf?`<a href="${esc(lf)}" target="_blank" class="sci2-preg-link text-info"><i class="ti tabler-link" style="font-size:.7rem"></i> ${esc(lf)}</a>`:`<span class="sci2-preg-link text-muted"><i class="ti tabler-link-off" style="font-size:.7rem"></i> Sin ficha</span>`}
          </div>` + acts;
        bindPregRow(row);
      }
      toast('success', res.message);
    } catch(err) { toast('error', err.message); }
    btn.disabled = false;
  });

  function crearPregRow(p, num) {
    const div = document.createElement('div');
    div.className = 'sci2-preg-row';
    div.id = 'sci2-preg-row-' + p.id;
    div.dataset.pregId     = p.id;
    div.dataset.pregNombre = p.nombre;
    div.dataset.pregLink   = p.link_ficha || '';
    div.dataset.pregActivo = p.activo ? '1' : '0';
    div.dataset.urlDestroy = p.url_destroy;
    div.innerHTML = `
      <i class="ti tabler-grip-vertical drag-handle preg-drag-handle" title="Arrastrar"></i>
      <span class="sci2-preg-num">${num}</span>
      <div class="sci2-preg-body">
        <span class="sci2-preg-nombre">${esc(p.nombre)}</span>
        ${p.link_ficha?`<a href="${esc(p.link_ficha)}" target="_blank" class="sci2-preg-link text-info"><i class="ti tabler-link" style="font-size:.7rem"></i> ${esc(p.link_ficha)}</a>`:`<span class="sci2-preg-link text-muted"><i class="ti tabler-link-off" style="font-size:.7rem"></i> Sin ficha</span>`}
      </div>
      <div class="sci2-preg-actions">
        <button class="btn btn-icon btn-xs btn-success btn-editar-preg" style="width:26px;height:26px"><i class="ti tabler-edit" style="font-size:.75rem"></i></button>
        <button class="btn btn-icon btn-xs btn-danger btn-eliminar-preg" style="width:26px;height:26px"
          data-nombre="${esc(p.nombre)}"><i class="ti tabler-trash" style="font-size:.75rem"></i></button>
      </div>`;
    return div;
  }

  function renumerarPregs2(card) {
    card.querySelectorAll('.sci2-preg-num').forEach((el, i) => el.textContent = i + 1);
  }
  function actualizarCountComp2(card) {
    const n  = card.querySelectorAll('.sci2-preg-row').length;
    const el = document.getElementById('sci2-comp-count-' + card.dataset.compId);
    if (el) el.textContent = n + 'p';
  }

  /* ══ Sortable ══ */
  const REORDER_COMP_URL = '{{ route("adm-sci.componente.reorder") }}';
  const REORDER_PREG_URL = '{{ route("adm-sci.pregunta.reorder") }}';

  function initSortablePregs(bodyEl) {
    if (!bodyEl || bodyEl._sp) return;
    bodyEl._sp = Sortable.create(bodyEl, {
      animation: 150,
      handle: '.preg-drag-handle',
      draggable: '.sci2-preg-row',
      ghostClass: 'sortable-ghost',
      dragClass: 'sortable-drag',
      onEnd: async function () {
        const items = Array.from(bodyEl.querySelectorAll('.sci2-preg-row')).map((el, i) => ({ id: parseInt(el.dataset.pregId), orden: i }));
        bodyEl.querySelectorAll('.sci2-preg-num').forEach((el, i) => el.textContent = i + 1);
        try { await api(REORDER_PREG_URL, 'POST', { items }); }
        catch (err) { toast('error', 'Error al guardar orden: ' + err.message); }
      },
    });
  }

  function initSortableComps(ejeContentEl) {
    if (!ejeContentEl || ejeContentEl._sc) return;
    ejeContentEl.querySelectorAll('.sci2-comp-body').forEach(initSortablePregs);
    ejeContentEl._sc = Sortable.create(ejeContentEl, {
      animation: 150,
      handle: '.comp-drag-handle',
      draggable: '.sci2-comp-card',
      ghostClass: 'sortable-ghost',
      dragClass: 'sortable-drag',
      onEnd: async function () {
        const items = Array.from(ejeContentEl.querySelectorAll(':scope > .sci2-comp-card')).map((el, i) => ({ id: parseInt(el.dataset.compId), orden: i }));
        try { await api(REORDER_COMP_URL, 'POST', { items }); }
        catch (err) { toast('error', 'Error al guardar orden: ' + err.message); }
      },
    });
  }

  document.querySelectorAll('.sci2-eje-content').forEach(initSortableComps);

  /* ══ Confirmar eliminar ══ */
  function confirmarEliminar(url, nombre, onSuccess) {
    Swal.fire({
      title: '¿Eliminar?',
      html: `<span class="fw-semibold">${esc(nombre)}</span><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
      icon: 'warning', showCancelButton: true,
      confirmButtonText: '<i class="ti tabler-trash me-1"></i>Sí, eliminar',
      cancelButtonText: 'Cancelar',
      customClass: { confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-label-secondary' },
      buttonsStyling: false,
    }).then(async r => {
      if (!r.isConfirmed) return;
      await new Promise(res => setTimeout(res, 200));
      try { await api(url, 'DELETE'); onSuccess(); }
      catch (err) { toast('error', err.message); }
    });
  }

  /* ══ Icon picker ══ */
  document.querySelectorAll('.icon-picker-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const ico = this.dataset.icon;
      document.getElementById(this.dataset.target).value = ico;
      document.getElementById(this.dataset.preview).className = `ti ${ico} text-info fs-5`;
      document.getElementById(this.dataset.label).textContent = ico;
      document.getElementById(this.dataset.picker).querySelectorAll('.icon-picker-btn').forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
    });
  });

  function setIconPicker(pickerId, targetId, previewId, labelId, ico) {
    document.getElementById(targetId).value = ico || '';
    document.getElementById(pickerId).querySelectorAll('.icon-picker-btn').forEach(b => b.classList.remove('selected'));
    if (ico) {
      document.getElementById(previewId).className = `ti ${ico} text-info fs-5`;
      document.getElementById(labelId).textContent = ico;
      document.getElementById(pickerId).querySelector(`[data-icon="${ico}"]`)?.classList.add('selected');
    } else {
      document.getElementById(previewId).className = 'ti text-info fs-5';
      document.getElementById(labelId).textContent = 'Sin ícono';
    }
  }

})();
</script>
@endsection
