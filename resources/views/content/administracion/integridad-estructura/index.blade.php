@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Estructura Integridad - PULSO UGEL')

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
.int-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 1.25rem;
  align-items: start;
}
@media (max-width: 991px) {
  .int-layout { grid-template-columns: 1fr; }
}

/* ══ Panel izquierdo: lista de etapas ══ */
.int-etapas-panel {
  background: var(--bs-body-bg);
  border: 1px solid var(--bs-border-color);
  border-radius: .625rem;
  overflow: hidden;
  position: sticky;
  top: 80px;
}
.int-etapas-header {
  padding: .65rem 1rem;
  background: rgba(var(--bs-warning-rgb), .1);
  border-bottom: 1px solid var(--bs-border-color);
  display: flex; align-items: center; justify-content: space-between;
  font-weight: 700; font-size: .72rem; letter-spacing: .07em; text-transform: uppercase;
  color: var(--bs-warning);
}
.int-etapa-item {
  display: flex; align-items: center; gap: .6rem;
  padding: .6rem 1rem;
  border-bottom: 1px solid var(--bs-border-color);
  cursor: pointer;
  transition: background .15s;
  position: relative;
}
.int-etapa-item:last-child { border-bottom: none; }
.int-etapa-item:hover { background: var(--bs-tertiary-bg); }
.int-etapa-item.activa-tab {
  background: rgba(var(--bs-warning-rgb), .1);
  box-shadow: inset 3px 0 0 var(--bs-warning);
  font-weight: 600;
}
.int-etapa-num {
  flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%;
  background: var(--bs-warning); color: #000;
  display: flex; align-items: center; justify-content: center;
  font-size: .65rem; font-weight: 700;
}
.int-etapa-nombre { flex: 1; font-size: .84rem; min-width: 0; line-height: 1.25; }
.int-etapa-off { opacity: .5; }
.int-etapa-toggle { flex-shrink: 0; }
.int-etapa-actions { display: flex; gap: .2rem; flex-shrink: 0; }

.int-etapas-footer {
  padding: .5rem .75rem;
  border-top: 1px solid var(--bs-border-color);
}

/* ══ Panel derecho: contenido ══ */
.int-content-panel { min-width: 0; }

.int-content-empty {
  border: 1px dashed var(--bs-border-color);
  border-radius: .625rem;
  padding: 3rem 1rem;
  text-align: center;
  color: var(--bs-secondary-color);
}

/* ── Cabecera de etapa activa ── */
.int-etapa-titulo {
  display: flex; align-items: center; gap: .75rem;
  margin-bottom: 1rem;
  padding-bottom: .75rem;
  border-bottom: 2px solid rgba(var(--bs-warning-rgb), .25);
}
.int-etapa-titulo-badge {
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--bs-warning); color: #000;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: .8rem; flex-shrink: 0;
}
.int-etapa-titulo h5 { margin: 0; font-size: 1.05rem; }

/* ── Componente card ── */
.int-comp-card {
  border: 1px solid var(--bs-border-color);
  border-radius: .5rem;
  margin-bottom: .875rem;
  overflow: hidden;
  transition: box-shadow .15s;
}
.int-comp-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,.06); }

.int-comp-header {
  display: flex; align-items: center; gap: .6rem;
  padding: .6rem .875rem;
  background: rgba(var(--bs-info-rgb), .07);
  border-bottom: 1px solid rgba(var(--bs-info-rgb), .15);
  cursor: pointer; user-select: none;
}
.int-comp-header:hover { background: rgba(var(--bs-info-rgb), .12); }
.int-comp-icono { color: var(--bs-info); font-size: 1rem; flex-shrink: 0; }
.int-comp-nombre { flex: 1; font-weight: 600; font-size: .875rem; min-width: 0; }
.int-comp-count {
  font-size: .68rem; color: var(--bs-info);
  background: rgba(var(--bs-info-rgb), .12);
  border-radius: 50rem; padding: .1em .55em; flex-shrink: 0;
}
.int-comp-actions { display: flex; gap: .2rem; flex-shrink: 0; }
.int-comp-chevron { flex-shrink: 0; color: var(--bs-secondary-color); transition: transform .2s; }
.int-comp-chevron.open { transform: rotate(180deg); }

/* ── Preguntas tabla ── */
.int-comp-body { display: none; }
.int-comp-body.open { display: block; }

.int-preg-row {
  display: flex; align-items: flex-start; gap: .6rem;
  padding: .5rem .875rem;
  border-bottom: 1px solid var(--bs-border-color);
  font-size: .8125rem;
  transition: background .1s;
}
.int-preg-row:last-of-type { border-bottom: none; }
.int-preg-row:hover { background: var(--bs-tertiary-bg); }

.int-preg-num {
  flex-shrink: 0; width: 18px; height: 18px; border-radius: 50%;
  background: var(--bs-success); color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: .6rem; font-weight: 700; margin-top: 2px;
}
.int-preg-body { flex: 1; min-width: 0; }
.int-preg-nombre { white-space: normal; word-break: break-word; line-height: 1.35; }
.int-preg-link { font-size: .73rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; margin-top: .1rem; }
.int-preg-actions { display: flex; gap: .2rem; flex-shrink: 0; align-items: flex-start; margin-top: 1px; }

.int-preg-add {
  display: flex; align-items: center; gap: .4rem;
  padding: .4rem .875rem;
  background: transparent; border: none;
  color: var(--bs-success); font-size: .77rem;
  cursor: pointer; width: 100%; text-align: left;
  border-top: 1px dashed rgba(var(--bs-success-rgb), .3);
  transition: background .15s;
}
.int-preg-add:hover { background: rgba(var(--bs-success-rgb), .06); }

.int-comp-add {
  display: flex; align-items: center; gap: .4rem;
  padding: .45rem .875rem;
  background: rgba(var(--bs-info-rgb), .05);
  border: 1px dashed rgba(var(--bs-info-rgb), .3);
  border-radius: .5rem; color: var(--bs-info);
  font-size: .77rem; cursor: pointer; width: 100%;
  transition: background .15s; margin-top: .25rem;
}
.int-comp-add:hover { background: rgba(var(--bs-info-rgb), .1); }

.badge-off { font-size: .58rem; padding: .12em .4em; vertical-align: middle; }

/* ── Icon picker ── */
.icon-picker { display: flex; flex-wrap: wrap; gap: .375rem; max-height: 180px; overflow-y: auto; padding: .5rem; background: var(--bs-tertiary-bg); border-radius: .375rem; border: 1px solid var(--bs-border-color); }
.icon-picker-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: .375rem; border: 1px solid transparent; cursor: pointer; background: var(--bs-body-bg); transition: all .15s; font-size: 1.1rem; }
.icon-picker-btn:hover { border-color: var(--bs-info); background: rgba(var(--bs-info-rgb),.1); }
.icon-picker-btn.selected { border-color: var(--bs-info); background: rgba(var(--bs-info-rgb),.2); box-shadow: 0 0 0 2px rgba(var(--bs-info-rgb),.3); }

@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin .8s linear infinite; display: inline-block; }

/* ── Drag & drop ── */
.drag-handle {
  cursor: grab; flex-shrink: 0;
  color: var(--bs-secondary-color);
  opacity: .4; font-size: .95rem;
  padding: 0 2px;
  transition: opacity .15s;
}
.drag-handle:hover { opacity: .85; }
.drag-handle:active { cursor: grabbing; }
.sortable-ghost {
  opacity: .35;
  background: rgba(var(--bs-primary-rgb), .06) !important;
  border: 1px dashed var(--bs-primary) !important;
}
.sortable-drag { opacity: .95; box-shadow: 0 6px 20px rgba(0,0,0,.15); }
.reorder-saving { pointer-events: none; opacity: .7; }
</style>
@endsection

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Estructura Integridad</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
  <div>
    <h4 class="mb-1"><i class="ti tabler-shield-check me-2 text-warning"></i>Modelo de Integridad</h4>
    <p class="mb-0 text-muted small">Gestiona Etapas → Componentes → Preguntas del modelo</p>
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

<div class="int-layout">

  {{-- ══ PANEL IZQUIERDO: ETAPAS ══ --}}
  <div class="int-etapas-panel">
    <div class="int-etapas-header">
      <span><i class="ti tabler-layers-subtract me-1"></i>Etapas</span>
      <span class="badge bg-warning text-dark" style="font-size:.65rem">{{ $etapas->count() }}</span>
    </div>

    <div id="lista-etapas">
      @forelse($etapas as $idx => $etapa)
      <div class="int-etapa-item {{ $idx === 0 ? 'activa-tab' : '' }} {{ !$etapa->activo ? 'int-etapa-off' : '' }}"
           id="etapa-tab-{{ $etapa->id }}"
           data-etapa-id="{{ $etapa->id }}"
           data-etapa-nombre="{{ e($etapa->nombre) }}"
           data-etapa-descripcion="{{ e($etapa->descripcion ?? '') }}"
           data-etapa-anio="{{ $etapa->anio }}"
           data-etapa-activo="{{ $etapa->activo ? 1 : 0 }}"
           onclick="seleccionarEtapa({{ $etapa->id }})">

        <span class="int-etapa-num">{{ $idx + 1 }}</span>
        <span class="int-etapa-nombre">
          {{ $etapa->nombre }}
          @if(!$etapa->activo)
            <span class="badge bg-label-danger badge-off d-block mt-1" style="width:fit-content">Inactiva</span>
          @endif
        </span>

        @can('integridad.editar')
        <div class="int-etapa-toggle" onclick="event.stopPropagation()" title="{{ $etapa->activo ? 'Desactivar' : 'Activar' }} etapa">
          <div class="form-check form-switch mb-0">
            <input class="form-check-input toggle-etapa" type="checkbox"
                   {{ $etapa->activo ? 'checked' : '' }}
                   data-etapa-id="{{ $etapa->id }}"
                   data-url="{{ route('adm-integridad.etapa.toggle', $etapa) }}">
          </div>
        </div>
        @endcan

        @canany(['integridad.editar','integridad.eliminar'])
        <div class="int-etapa-actions" onclick="event.stopPropagation()">
          @can('integridad.editar')
          <button class="btn btn-icon btn-xs btn-warning btn-editar-etapa" title="Editar" style="width:24px;height:24px">
            <i class="ti tabler-edit" style="font-size:.75rem"></i>
          </button>
          @endcan
          @can('integridad.eliminar')
          <button class="btn btn-icon btn-xs btn-danger btn-eliminar-etapa" title="Eliminar"
            data-url="{{ route('adm-integridad.etapa.destroy', $etapa) }}"
            data-nombre="{{ e($etapa->nombre) }}"
            style="width:24px;height:24px">
            <i class="ti tabler-trash" style="font-size:.75rem"></i>
          </button>
          @endcan
        </div>
        @endcanany
      </div>
      @empty
      <div class="p-3 text-center text-muted" style="font-size:.82rem">
        <i class="ti tabler-layers-subtract d-block mb-1" style="font-size:1.5rem;opacity:.3"></i>
        Sin etapas para {{ $anio }}
      </div>
      @endforelse
    </div>

    <div class="int-etapas-footer">
      @can('integridad.crear')
      <button class="btn btn-sm btn-warning w-100" id="btn-nueva-etapa-open">
        <i class="ti tabler-plus me-1"></i>Nueva Etapa
      </button>
      @endcan
    </div>
  </div>

  {{-- ══ PANEL DERECHO: CONTENIDO DE ETAPA ══ --}}
  <div class="int-content-panel" id="content-panel">

    @if($etapas->isEmpty())
    <div class="int-content-empty">
      <i class="ti tabler-shield-check d-block mb-2" style="font-size:2.5rem;opacity:.2"></i>
      <p class="mb-0">Crea una etapa para comenzar a estructurar el modelo.</p>
    </div>
    @else
    {{-- Contenido de cada etapa (solo visible la primera) --}}
    @foreach($etapas as $idx => $etapa)
    <div class="int-etapa-content {{ $idx !== 0 ? 'd-none' : '' }}"
         id="content-etapa-{{ $etapa->id }}">

      {{-- Título etapa --}}
      <div class="int-etapa-titulo">
        <span class="int-etapa-titulo-badge">{{ $idx + 1 }}</span>
        <div>
          <h5>{{ $etapa->nombre }}</h5>
          @if($etapa->descripcion)
            <p class="mb-0 text-muted" style="font-size:.78rem">{{ $etapa->descripcion }}</p>
          @endif
        </div>
        <span class="ms-auto badge {{ $etapa->activo ? 'bg-label-success' : 'bg-label-danger' }}" style="font-size:.72rem">
          {{ $etapa->activo ? 'Activa' : 'Inactiva' }}
        </span>
      </div>

      {{-- Componentes --}}
      @if($etapa->componentes->isEmpty())
        <div class="int-content-empty mb-3">
          <i class="ti tabler-puzzle d-block mb-1" style="font-size:1.8rem;opacity:.25"></i>
          <p class="mb-0 small">Sin componentes. Agrega el primero.</p>
        </div>
      @else
        @foreach($etapa->componentes as $cIdx => $comp)
        <div class="int-comp-card"
             id="comp-card-{{ $comp->id }}"
             data-comp-id="{{ $comp->id }}"
             data-comp-nombre="{{ e($comp->nombre) }}"
             data-comp-icono="{{ e($comp->icono ?? '') }}"
             data-comp-descripcion="{{ e($comp->descripcion ?? '') }}"
             data-comp-activo="{{ $comp->activo ? 1 : 0 }}"
             data-etapa-id="{{ $etapa->id }}">

          <div class="int-comp-header" onclick="toggleComp({{ $comp->id }})">
            @can('integridad.editar')
            <i class="ti tabler-grip-vertical drag-handle comp-drag-handle" title="Arrastrar para reordenar" onclick="event.stopPropagation()"></i>
            @endcan
            @if($comp->icono)
              <i class="ti {{ $comp->icono }} int-comp-icono"></i>
            @else
              <i class="ti tabler-puzzle int-comp-icono" style="opacity:.35"></i>
            @endif
            <span class="int-comp-nombre">
              {{ $comp->nombre }}
              @if(!$comp->activo)<span class="badge bg-label-danger badge-off ms-1">Off</span>@endif
            </span>
            <span class="int-comp-count" id="comp-count-{{ $comp->id }}">{{ $comp->preguntas->count() }}p</span>
            @canany(['integridad.editar','integridad.eliminar'])
            <div class="int-comp-actions" onclick="event.stopPropagation()">
              @can('integridad.editar')
              <button class="btn btn-icon btn-xs btn-info btn-editar-comp" title="Editar componente" style="width:26px;height:26px">
                <i class="ti tabler-edit" style="font-size:.78rem"></i>
              </button>
              @endcan
              @can('integridad.eliminar')
              <button class="btn btn-icon btn-xs btn-danger btn-eliminar-comp" title="Eliminar componente"
                data-url="{{ route('adm-integridad.componente.destroy', $comp) }}"
                data-nombre="{{ e($comp->nombre) }}"
                style="width:26px;height:26px">
                <i class="ti tabler-trash" style="font-size:.78rem"></i>
              </button>
              @endcan
            </div>
            @endcanany
            <i class="ti tabler-chevron-down int-comp-chevron open" id="comp-chevron-{{ $comp->id }}"></i>
          </div>

          <div class="int-comp-body open" id="comp-body-{{ $comp->id }}">
            {{-- Filas de preguntas --}}
            @forelse($comp->preguntas as $pIdx => $preg)
            <div class="int-preg-row"
                 id="preg-row-{{ $preg->id }}"
                 data-preg-id="{{ $preg->id }}"
                 data-preg-nombre="{{ e($preg->nombre) }}"
                 data-preg-link="{{ e($preg->link_ficha ?? '') }}"
                 data-preg-activo="{{ $preg->activo ? 1 : 0 }}"
                 data-url-destroy="{{ route('adm-integridad.pregunta.destroy', $preg) }}">
              @can('integridad.editar')
              <i class="ti tabler-grip-vertical drag-handle preg-drag-handle" title="Arrastrar para reordenar"></i>
              @endcan
              <span class="int-preg-num">{{ $pIdx + 1 }}</span>
              <div class="int-preg-body">
                <span class="int-preg-nombre">
                  {{ $preg->nombre }}
                  @if(!$preg->activo)<span class="badge bg-label-danger badge-off ms-1">Off</span>@endif
                </span>
                @if($preg->link_ficha)
                  <a href="{{ $preg->link_ficha }}" target="_blank" class="int-preg-link text-info" title="{{ $preg->link_ficha }}">
                    <i class="ti tabler-link" style="font-size:.7rem"></i> {{ $preg->link_ficha }}
                  </a>
                @else
                  <span class="int-preg-link text-muted"><i class="ti tabler-link-off" style="font-size:.7rem"></i> Sin ficha</span>
                @endif
              </div>
              @canany(['integridad.editar','integridad.eliminar'])
              <div class="int-preg-actions">
                @can('integridad.editar')
                <button class="btn btn-icon btn-xs btn-success btn-editar-preg" title="Editar pregunta" style="width:26px;height:26px">
                  <i class="ti tabler-edit" style="font-size:.75rem"></i>
                </button>
                @endcan
                @can('integridad.eliminar')
                <button class="btn btn-icon btn-xs btn-danger btn-eliminar-preg" title="Eliminar pregunta"
                  data-nombre="{{ e($preg->nombre) }}"
                  style="width:26px;height:26px">
                  <i class="ti tabler-trash" style="font-size:.75rem"></i>
                </button>
                @endcan
              </div>
              @endcanany
            </div>
            @empty
            <div class="p-3 text-muted text-center" style="font-size:.78rem" id="preg-empty-{{ $comp->id }}">
              <i class="ti tabler-help-circle me-1 opacity-50"></i>Sin preguntas. Agrega la primera.
            </div>
            @endforelse

            @can('integridad.crear')
            <button class="int-preg-add btn-nueva-preg-inline" data-comp-id="{{ $comp->id }}">
              <i class="ti tabler-plus"></i> Nueva pregunta
            </button>
            @endcan
          </div>
        </div>
        @endforeach
      @endif

      @can('integridad.crear')
      <button class="int-comp-add btn-nuevo-comp-inline" data-etapa-id="{{ $etapa->id }}">
        <i class="ti tabler-plus"></i> Nuevo componente
      </button>
      @endcan
    </div>
    @endforeach
    @endif
  </div>{{-- /content-panel --}}

</div>{{-- /int-layout --}}

{{-- Leyenda --}}
<div class="d-flex align-items-center gap-2 mt-3 flex-wrap" style="font-size:.74rem;color:var(--bs-secondary-color)">
  <span class="d-flex align-items-center gap-1"><span style="width:9px;height:9px;border-radius:50%;background:var(--bs-warning);display:inline-block"></span>Etapa</span>
  <i class="ti tabler-chevron-right opacity-40"></i>
  <span class="d-flex align-items-center gap-1"><span style="width:9px;height:9px;border-radius:50%;background:var(--bs-info);display:inline-block"></span>Componente</span>
  <i class="ti tabler-chevron-right opacity-40"></i>
  <span class="d-flex align-items-center gap-1"><span style="width:9px;height:9px;border-radius:50%;background:var(--bs-success);display:inline-block"></span>Pregunta → genera actividades de cumplimiento</span>
  <span class="ms-auto">El toggle activa/desactiva la etapa en el modelo público.</span>
</div>

@canany(['integridad.crear','integridad.editar'])
{{-- MODAL: NUEVA ETAPA --}}
<div class="modal fade" id="modalNuevaEtapa" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--bs-warning);color:#000">
        <h5 class="modal-title"><i class="ti tabler-layers-subtract me-2"></i>Nueva Etapa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-8">
            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
            <input type="text" id="nueva_etapa_nombre" class="form-control" placeholder="Ej: Planificación">
          </div>
          <div class="col-4">
            <label class="form-label fw-semibold">Año</label>
            <input type="number" id="nueva_etapa_anio" class="form-control" value="{{ $anio }}" min="2020" max="2099">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="nueva_etapa_descripcion" class="form-control" rows="2" placeholder="Opcional..."></textarea>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="nueva_etapa_activo" checked>
              <label class="form-check-label" for="nueva_etapa_activo">Activa</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning" id="btn-guardar-etapa"><i class="ti tabler-device-floppy me-1"></i>Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: EDITAR ETAPA --}}
<div class="modal fade" id="modalEditarEtapa" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Etapa</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-8">
            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
            <input type="text" id="edit_etapa_nombre" class="form-control">
          </div>
          <div class="col-4">
            <label class="form-label fw-semibold">Año</label>
            <input type="number" id="edit_etapa_anio" class="form-control" min="2020" max="2099">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="edit_etapa_descripcion" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="edit_etapa_activo">
              <label class="form-check-label" for="edit_etapa_activo">Activa</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-info" id="btn-actualizar-etapa"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button>
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
            <input type="text" id="nuevo_comp_nombre" class="form-control" placeholder="Ej: Ética pública">
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
            @php $iconosList = ['tabler-crown','tabler-shield-check','tabler-chart-pie','tabler-chart-bar','tabler-clipboard-list','tabler-alert-triangle','tabler-messages','tabler-message-circle','tabler-eye','tabler-speakerphone','tabler-activity','tabler-user-check','tabler-users','tabler-building','tabler-file-certificate','tabler-scale','tabler-lock','tabler-target','tabler-trending-up','tabler-checkup-list','tabler-puzzle','tabler-compass','tabler-flag','tabler-microscope','tabler-layers-intersect','tabler-sitemap','tabler-hierarchy','tabler-map-pin','tabler-book','tabler-certificate'] @endphp
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
  const H    = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF };

  let pendingEtapaId = null;
  let pendingCompId  = null;
  let editEtapaId    = null;
  let editCompId     = null;
  let editPregId     = null;

  /* ── Toast ── */
  function toast(type, msg) {
    if (typeof pulsoToast === 'function') { pulsoToast(msg, type); return; }
    if (typeof Swal !== 'undefined') {
      const c = { success:'#28c76f', error:'#ea5455', warning:'#ff9f43', info:'#00cfe8' };
      Swal.fire({ toast:true, position:'top-end', icon:type, title:msg,
        showConfirmButton:false, timer:2800, timerProgressBar:true,
        customClass:{popup:'pulso-toast'}, iconColor:c[type]||c.info });
    }
  }

  /* ── API ── */
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

  /* ══ Seleccionar etapa (tab izquierdo) ══ */
  window.seleccionarEtapa = function(etapaId) {
    document.querySelectorAll('.int-etapa-item').forEach(t => t.classList.remove('activa-tab'));
    document.querySelectorAll('.int-etapa-content').forEach(c => c.classList.add('d-none'));
    const tab = document.getElementById('etapa-tab-' + etapaId);
    if (tab) tab.classList.add('activa-tab');
    const content = document.getElementById('content-etapa-' + etapaId);
    if (content) content.classList.remove('d-none');
  };

  /* ══ Toggle acordeón de componente ══ */
  window.toggleComp = function(compId) {
    const body    = document.getElementById('comp-body-' + compId);
    const chevron = document.getElementById('comp-chevron-' + compId);
    if (!body) return;
    body.classList.toggle('open');
    chevron.classList.toggle('open');
  };

  /* ══ Toggle activo etapa ══ */
  document.querySelectorAll('.toggle-etapa').forEach(chk => {
    chk.addEventListener('change', async function () {
      const etapaId = this.dataset.etapaId;
      const prev = this.checked;
      this.disabled = true;
      try {
        const res = await api(this.dataset.url, 'PATCH');
        // Actualizar tab
        const tab = document.getElementById('etapa-tab-' + etapaId);
        if (tab) {
          tab.classList.toggle('int-etapa-off', !res.activo);
          tab.dataset.etapaActivo = res.activo ? '1' : '0';
          const nombre = tab.querySelector('.int-etapa-nombre');
          let badge = nombre.querySelector('.badge');
          if (!res.activo) {
            if (!badge) {
              badge = document.createElement('span');
              badge.className = 'badge bg-label-danger badge-off d-block mt-1';
              badge.style.width = 'fit-content';
              nombre.appendChild(badge);
            }
            badge.textContent = 'Inactiva';
          } else { badge?.remove(); }
        }
        // Actualizar badge en panel derecho
        const content = document.getElementById('content-etapa-' + etapaId);
        if (content) {
          const b = content.querySelector('.int-etapa-titulo .badge');
          if (b) {
            b.textContent = res.activo ? 'Activa' : 'Inactiva';
            b.className = `ms-auto badge ${res.activo ? 'bg-label-success' : 'bg-label-danger'}`;
            b.style.fontSize = '.72rem';
          }
        }
        this.checked = res.activo;
        this.disabled = false;
        toast('success', res.message);
      } catch (err) {
        this.checked = prev; this.disabled = false;
        toast('error', err.message);
      }
    });
  });

  /* ══ Editar / Eliminar etapa ══ */
  function bindEtapaTab(tab) {
    tab.querySelector('.btn-editar-etapa')?.addEventListener('click', e => {
      e.stopPropagation();
      editEtapaId = tab.dataset.etapaId;
      document.getElementById('edit_etapa_nombre').value      = tab.dataset.etapaNombre;
      document.getElementById('edit_etapa_descripcion').value = tab.dataset.etapaDescripcion;
      document.getElementById('edit_etapa_anio').value        = tab.dataset.etapaAnio;
      document.getElementById('edit_etapa_activo').checked    = tab.dataset.etapaActivo === '1';
      getModal('modalEditarEtapa').show();
    });
    tab.querySelector('.btn-eliminar-etapa')?.addEventListener('click', e => {
      e.stopPropagation();
      confirmarEliminar(tab.querySelector('.btn-eliminar-etapa').dataset.url,
        tab.dataset.etapaNombre, () => {
          tab.remove();
          document.getElementById('content-etapa-' + tab.dataset.etapaId)?.remove();
          // Seleccionar la primera disponible
          const primera = document.querySelector('.int-etapa-item');
          if (primera) seleccionarEtapa(primera.dataset.etapaId);
          toast('success', 'Etapa eliminada.');
        });
    });
  }
  document.querySelectorAll('.int-etapa-item').forEach(bindEtapaTab);

  /* ── Nueva etapa ── */
  document.getElementById('btn-nueva-etapa-open')?.addEventListener('click', () => {
    document.getElementById('nueva_etapa_nombre').value      = '';
    document.getElementById('nueva_etapa_descripcion').value = '';
    document.getElementById('nueva_etapa_activo').checked    = true;
    getModal('modalNuevaEtapa').show();
  });

  document.getElementById('btn-guardar-etapa')?.addEventListener('click', async () => {
    const nombre = document.getElementById('nueva_etapa_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    const btn = document.getElementById('btn-guardar-etapa');
    btn.disabled = true;
    try {
      const res = await api('{{ route("adm-integridad.etapa.store") }}', 'POST', {
        nombre,
        descripcion: document.getElementById('nueva_etapa_descripcion').value,
        anio:   document.getElementById('nueva_etapa_anio').value,
        activo: document.getElementById('nueva_etapa_activo').checked ? 1 : 0,
      });
      hideModal('modalNuevaEtapa');
      // Insertar en lista de tabs y panel de contenido
      const lista = document.getElementById('lista-etapas');
      lista.querySelector('.p-3.text-center')?.remove(); // quitar estado vacío
      const num = document.querySelectorAll('.int-etapa-item').length + 1;
      const tab = crearEtapaTab(res.etapa, num);
      lista.appendChild(tab);
      bindEtapaTab(tab);
      const panel = document.getElementById('content-panel');
      panel.querySelector('.int-content-empty')?.remove();
      panel.appendChild(crearEtapaContent(res.etapa, num));
      seleccionarEtapa(res.etapa.id);
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  /* ── Actualizar etapa ── */
  document.getElementById('btn-actualizar-etapa')?.addEventListener('click', async () => {
    const nombre = document.getElementById('edit_etapa_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    const btn = document.getElementById('btn-actualizar-etapa');
    btn.disabled = true;
    try {
      const res = await api(`/administracion/integridad/etapa/${editEtapaId}`, 'PUT', {
        nombre,
        descripcion: document.getElementById('edit_etapa_descripcion').value,
        anio:   document.getElementById('edit_etapa_anio').value,
        activo: document.getElementById('edit_etapa_activo').checked ? 1 : 0,
      });
      hideModal('modalEditarEtapa');
      const tab = document.getElementById('etapa-tab-' + editEtapaId);
      if (tab) {
        tab.dataset.etapaNombre      = res.etapa.nombre;
        tab.dataset.etapaDescripcion = res.etapa.descripcion || '';
        tab.dataset.etapaAnio        = res.etapa.anio;
        tab.dataset.etapaActivo      = res.etapa.activo ? '1' : '0';
        tab.classList.toggle('int-etapa-off', !res.etapa.activo);
        const nombreEl = tab.querySelector('.int-etapa-nombre');
        nombreEl.childNodes[0].textContent = res.etapa.nombre;
        let badge = nombreEl.querySelector('.badge');
        if (!res.etapa.activo) {
          if (!badge) { badge = document.createElement('span'); badge.className='badge bg-label-danger badge-off d-block mt-1'; badge.style.width='fit-content'; nombreEl.appendChild(badge); }
          badge.textContent = 'Inactiva';
        } else { badge?.remove(); }
        const chk = tab.querySelector('.toggle-etapa');
        if (chk) chk.checked = res.etapa.activo;
      }
      // Actualizar título en panel derecho
      const cont = document.getElementById('content-etapa-' + editEtapaId);
      if (cont) {
        cont.querySelector('.int-etapa-titulo h5').textContent = res.etapa.nombre;
        const desc = cont.querySelector('.int-etapa-titulo p');
        if (res.etapa.descripcion) {
          if (desc) desc.textContent = res.etapa.descripcion;
        } else { desc?.remove(); }
        const b = cont.querySelector('.int-etapa-titulo .badge');
        if (b) { b.textContent = res.etapa.activo ? 'Activa' : 'Inactiva'; b.className = `ms-auto badge ${res.etapa.activo ? 'bg-label-success' : 'bg-label-danger'}`; b.style.fontSize='.72rem'; }
      }
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  function crearEtapaTab(etapa, num) {
    const div = document.createElement('div');
    div.className = 'int-etapa-item' + (!etapa.activo ? ' int-etapa-off' : '');
    div.id = 'etapa-tab-' + etapa.id;
    div.dataset.etapaId          = etapa.id;
    div.dataset.etapaNombre      = etapa.nombre;
    div.dataset.etapaDescripcion = etapa.descripcion || '';
    div.dataset.etapaAnio        = etapa.anio;
    div.dataset.etapaActivo      = etapa.activo ? '1' : '0';
    div.setAttribute('onclick', `seleccionarEtapa(${etapa.id})`);
    div.innerHTML = `
      <span class="int-etapa-num">${num}</span>
      <span class="int-etapa-nombre">${esc(etapa.nombre)}${!etapa.activo?'<span class="badge bg-label-danger badge-off d-block mt-1" style="width:fit-content">Inactiva</span>':''}</span>
      <div class="int-etapa-toggle" onclick="event.stopPropagation()">
        <div class="form-check form-switch mb-0">
          <input class="form-check-input toggle-etapa" type="checkbox" ${etapa.activo?'checked':''}
            data-etapa-id="${etapa.id}" data-url="/administracion/integridad/etapa/${etapa.id}/toggle">
        </div>
      </div>
      <div class="int-etapa-actions" onclick="event.stopPropagation()">
        <button class="btn btn-icon btn-xs btn-warning btn-editar-etapa" style="width:24px;height:24px"><i class="ti tabler-edit" style="font-size:.75rem"></i></button>
        <button class="btn btn-icon btn-xs btn-danger btn-eliminar-etapa" style="width:24px;height:24px"
          data-url="/administracion/integridad/etapa/${etapa.id}" data-nombre="${esc(etapa.nombre)}">
          <i class="ti tabler-trash" style="font-size:.75rem"></i></button>
      </div>`;
    div.querySelector('.toggle-etapa').addEventListener('change', async function () {
      const prev = this.checked; this.disabled = true;
      try {
        const res = await api(this.dataset.url, 'PATCH');
        div.classList.toggle('int-etapa-off', !res.activo);
        this.checked = res.activo; this.disabled = false;
        toast('success', res.message);
      } catch(err) { this.checked = prev; this.disabled = false; toast('error', err.message); }
    });
    return div;
  }

  function crearEtapaContent(etapa, num) {
    const div = document.createElement('div');
    div.className = 'int-etapa-content d-none';
    div.id = 'content-etapa-' + etapa.id;
    div.innerHTML = `
      <div class="int-etapa-titulo">
        <span class="int-etapa-titulo-badge">${num}</span>
        <div><h5>${esc(etapa.nombre)}</h5>${etapa.descripcion?`<p class="mb-0 text-muted" style="font-size:.78rem">${esc(etapa.descripcion)}</p>`:''}</div>
        <span class="ms-auto badge ${etapa.activo?'bg-label-success':'bg-label-danger'}" style="font-size:.72rem">${etapa.activo?'Activa':'Inactiva'}</span>
      </div>
      <div class="int-content-empty mb-3">
        <i class="ti tabler-puzzle d-block mb-1" style="font-size:1.8rem;opacity:.25"></i>
        <p class="mb-0 small">Sin componentes. Agrega el primero.</p>
      </div>
      <button class="int-comp-add btn-nuevo-comp-inline" data-etapa-id="${etapa.id}">
        <i class="ti tabler-plus"></i> Nuevo componente
      </button>`;
    div.querySelector('.btn-nuevo-comp-inline').addEventListener('click', function() {
      abrirModalNuevoComp(this.dataset.etapaId);
    });
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
        const etapaId = card.dataset.etapaId;
        card.remove();
        actualizarTituloEtapa(etapaId);
        toast('success', 'Componente eliminado.');
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
    card.querySelectorAll('.int-preg-row').forEach(bindPregRow);
  }
  document.querySelectorAll('.int-comp-card').forEach(bindCompCard);
  document.querySelectorAll('.btn-nuevo-comp-inline').forEach(b => {
    b.addEventListener('click', () => abrirModalNuevoComp(b.dataset.etapaId));
  });

  function abrirModalNuevoComp(etapaId) {
    pendingEtapaId = etapaId;
    document.getElementById('nuevo_comp_nombre').value      = '';
    document.getElementById('nuevo_comp_descripcion').value = '';
    document.getElementById('nuevo_comp_activo').checked    = true;
    setIconPicker('nuevo_icon_picker','nuevo_comp_icono','nuevo_comp_icono_preview','nuevo_comp_icono_label','');
    getModal('modalNuevoComponente').show();
  }

  document.getElementById('btn-guardar-comp')?.addEventListener('click', async () => {
    const nombre = document.getElementById('nuevo_comp_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    if (!pendingEtapaId) { toast('warning', 'Selecciona una etapa.'); return; }
    const btn = document.getElementById('btn-guardar-comp');
    btn.disabled = true;
    try {
      const res = await api('{{ route("adm-integridad.componente.store") }}', 'POST', {
        etapa_id:    pendingEtapaId,
        nombre,
        icono:       document.getElementById('nuevo_comp_icono').value,
        descripcion: document.getElementById('nuevo_comp_descripcion').value,
        activo:      document.getElementById('nuevo_comp_activo').checked ? 1 : 0,
      });
      hideModal('modalNuevoComponente');
      const cont = document.getElementById('content-etapa-' + pendingEtapaId);
      if (cont) {
        cont.querySelector('.int-content-empty')?.remove();
        const btnComp = cont.querySelector('.btn-nuevo-comp-inline');
        const card = crearCompCard(res.componente, pendingEtapaId);
        cont.insertBefore(card, btnComp);
        bindCompCard(card);
        initSortablePregs(card.querySelector('.int-comp-body'));
        actualizarTituloEtapa(pendingEtapaId);
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
      const res = await api(`/administracion/integridad/componente/${editCompId}`, 'PUT', {
        nombre,
        icono:       document.getElementById('edit_comp_icono').value,
        descripcion: document.getElementById('edit_comp_descripcion').value,
        activo:      document.getElementById('edit_comp_activo').checked ? 1 : 0,
      });
      hideModal('modalEditarComponente');
      const card = document.getElementById('comp-card-' + editCompId);
      if (card) {
        card.dataset.compNombre      = res.componente.nombre;
        card.dataset.compIcono       = res.componente.icono || '';
        card.dataset.compDescripcion = res.componente.descripcion || '';
        card.dataset.compActivo      = res.componente.activo ? '1' : '0';
        const ico = res.componente.icono;
        const count = card.querySelectorAll('.int-preg-row').length;
        const h = card.querySelector('.int-comp-header');
        const chevronEl = h.querySelector('.int-comp-chevron');
        const open = chevronEl?.classList.contains('open');
        h.innerHTML = `
          ${ico?`<i class="ti ${esc(ico)} int-comp-icono"></i>`:'<i class="ti tabler-puzzle int-comp-icono" style="opacity:.35"></i>'}
          <span class="int-comp-nombre">${esc(res.componente.nombre)}${!res.componente.activo?'<span class="badge bg-label-danger badge-off ms-1">Off</span>':''}</span>
          <span class="int-comp-count" id="comp-count-${editCompId}">${count}p</span>
          <div class="int-comp-actions" onclick="event.stopPropagation()">
            <button class="btn btn-icon btn-xs btn-info btn-editar-comp" style="width:26px;height:26px"><i class="ti tabler-edit" style="font-size:.78rem"></i></button>
            <button class="btn btn-icon btn-xs btn-danger btn-eliminar-comp" style="width:26px;height:26px"
              data-url="/administracion/integridad/componente/${editCompId}" data-nombre="${esc(res.componente.nombre)}"><i class="ti tabler-trash" style="font-size:.78rem"></i></button>
          </div>
          <i class="ti tabler-chevron-down int-comp-chevron ${open?'open':''}" id="comp-chevron-${editCompId}"></i>`;
        h.setAttribute('onclick', `toggleComp(${editCompId})`);
        bindCompCard(card);
      }
      toast('success', res.message);
    } catch(err) { toast('error', err.message); }
    btn.disabled = false;
  });

  function crearCompCard(c, etapaId) {
    const div = document.createElement('div');
    div.className = 'int-comp-card';
    div.id = 'comp-card-' + c.id;
    div.dataset.compId          = c.id;
    div.dataset.compNombre      = c.nombre;
    div.dataset.compIcono       = c.icono || '';
    div.dataset.compDescripcion = c.descripcion || '';
    div.dataset.compActivo      = c.activo ? '1' : '0';
    div.dataset.etapaId         = etapaId;
    div.innerHTML = `
      <div class="int-comp-header" onclick="toggleComp(${c.id})">
        <i class="ti tabler-grip-vertical drag-handle comp-drag-handle" title="Arrastrar para reordenar" onclick="event.stopPropagation()"></i>
        ${c.icono?`<i class="ti ${esc(c.icono)} int-comp-icono"></i>`:'<i class="ti tabler-puzzle int-comp-icono" style="opacity:.35"></i>'}
        <span class="int-comp-nombre">${esc(c.nombre)}${!c.activo?'<span class="badge bg-label-danger badge-off ms-1">Off</span>':''}</span>
        <span class="int-comp-count" id="comp-count-${c.id}">0p</span>
        <div class="int-comp-actions" onclick="event.stopPropagation()">
          <button class="btn btn-icon btn-xs btn-info btn-editar-comp" style="width:26px;height:26px"><i class="ti tabler-edit" style="font-size:.78rem"></i></button>
          <button class="btn btn-icon btn-xs btn-danger btn-eliminar-comp" style="width:26px;height:26px"
            data-url="/administracion/integridad/componente/${c.id}" data-nombre="${esc(c.nombre)}"><i class="ti tabler-trash" style="font-size:.78rem"></i></button>
        </div>
        <i class="ti tabler-chevron-down int-comp-chevron open" id="comp-chevron-${c.id}"></i>
      </div>
      <div class="int-comp-body open" id="comp-body-${c.id}">
        <div class="p-3 text-muted text-center" id="preg-empty-${c.id}" style="font-size:.78rem">
          <i class="ti tabler-help-circle me-1 opacity-50"></i>Sin preguntas.
        </div>
        <button class="int-preg-add btn-nueva-preg-inline" data-comp-id="${c.id}">
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
      pendingCompId = row.closest('.int-comp-card')?.dataset.compId;
      document.getElementById('edit_preg_nombre').value   = row.dataset.pregNombre;
      document.getElementById('edit_preg_link').value     = row.dataset.pregLink;
      document.getElementById('edit_preg_activo').checked = row.dataset.pregActivo === '1';
      getModal('modalEditarPregunta').show();
    });
    row.querySelector('.btn-eliminar-preg')?.addEventListener('click', e => {
      e.stopPropagation();
      confirmarEliminar(row.dataset.urlDestroy, row.dataset.pregNombre, () => {
        const card    = row.closest('.int-comp-card');
        const etapaId = card?.dataset.etapaId;
        row.remove();
        if (card) { renumerarPregs(card); actualizarCountComp(card); }
        if (etapaId) actualizarTituloEtapa(etapaId);
        toast('success', 'Pregunta eliminada.');
      });
    });
  }
  document.querySelectorAll('.int-preg-row').forEach(bindPregRow);

  document.getElementById('btn-guardar-preg')?.addEventListener('click', async () => {
    const nombre = document.getElementById('nueva_preg_nombre').value.trim();
    if (!nombre)     { toast('warning', 'El enunciado es obligatorio.'); return; }
    if (!pendingCompId) { toast('warning', 'Componente no detectado.'); return; }
    const btn = document.getElementById('btn-guardar-preg');
    btn.disabled = true;
    try {
      const res = await api('{{ route("adm-integridad.pregunta.store") }}', 'POST', {
        componente_id: pendingCompId,
        nombre,
        link_ficha: document.getElementById('nueva_preg_link').value || null,
        activo:     document.getElementById('nueva_preg_activo').checked ? 1 : 0,
      });
      hideModal('modalNuevaPregunta');
      const card = document.getElementById('comp-card-' + pendingCompId);
      if (card) {
        const body    = document.getElementById('comp-body-' + pendingCompId);
        body?.querySelector(`#preg-empty-${pendingCompId}`)?.remove();
        const addBtn  = body?.querySelector('.btn-nueva-preg-inline');
        const num     = card.querySelectorAll('.int-preg-row').length + 1;
        const row     = crearPregRow(res.pregunta, num);
        body?.insertBefore(row, addBtn);
        bindPregRow(row);
        actualizarCountComp(card);
        actualizarTituloEtapa(card.dataset.etapaId);
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
      const res = await api(`/administracion/integridad/pregunta/${editPregId}`, 'PUT', {
        nombre,
        link_ficha: document.getElementById('edit_preg_link').value || null,
        activo:     document.getElementById('edit_preg_activo').checked ? 1 : 0,
      });
      hideModal('modalEditarPregunta');
      const row = document.getElementById('preg-row-' + editPregId);
      if (row) {
        row.dataset.pregNombre = res.pregunta.nombre;
        row.dataset.pregLink   = res.pregunta.link_ficha || '';
        row.dataset.pregActivo = res.pregunta.activo ? '1' : '0';
        const num  = row.querySelector('.int-preg-num').outerHTML;
        const acts = row.querySelector('.int-preg-actions')?.outerHTML || '';
        const lf   = res.pregunta.link_ficha;
        row.innerHTML = num +
          `<div class="int-preg-body">
            <span class="int-preg-nombre">${esc(res.pregunta.nombre)}${!res.pregunta.activo?'<span class="badge bg-label-danger badge-off ms-1">Off</span>':''}</span>
            ${lf?`<a href="${esc(lf)}" target="_blank" class="int-preg-link text-info"><i class="ti tabler-link" style="font-size:.7rem"></i> ${esc(lf)}</a>`
                :`<span class="int-preg-link text-muted"><i class="ti tabler-link-off" style="font-size:.7rem"></i> Sin ficha</span>`}
          </div>` + acts;
        bindPregRow(row);
      }
      toast('success', res.message);
    } catch(err) { toast('error', err.message); }
    btn.disabled = false;
  });

  function crearPregRow(p, num) {
    const div = document.createElement('div');
    div.className = 'int-preg-row';
    div.id = 'preg-row-' + p.id;
    div.dataset.pregId      = p.id;
    div.dataset.pregNombre  = p.nombre;
    div.dataset.pregLink    = p.link_ficha || '';
    div.dataset.pregActivo  = p.activo ? '1' : '0';
    div.dataset.urlDestroy  = p.url_destroy;
    div.innerHTML = `
      <i class="ti tabler-grip-vertical drag-handle preg-drag-handle" title="Arrastrar para reordenar"></i>
      <span class="int-preg-num">${num}</span>
      <div class="int-preg-body">
        <span class="int-preg-nombre">${esc(p.nombre)}${!p.activo?'<span class="badge bg-label-danger badge-off ms-1">Off</span>':''}</span>
        ${p.link_ficha
          ?`<a href="${esc(p.link_ficha)}" target="_blank" class="int-preg-link text-info"><i class="ti tabler-link" style="font-size:.7rem"></i> ${esc(p.link_ficha)}</a>`
          :`<span class="int-preg-link text-muted"><i class="ti tabler-link-off" style="font-size:.7rem"></i> Sin ficha</span>`}
      </div>
      <div class="int-preg-actions">
        <button class="btn btn-icon btn-xs btn-success btn-editar-preg" style="width:26px;height:26px"><i class="ti tabler-edit" style="font-size:.75rem"></i></button>
        <button class="btn btn-icon btn-xs btn-danger btn-eliminar-preg" style="width:26px;height:26px"
          data-nombre="${esc(p.nombre)}"><i class="ti tabler-trash" style="font-size:.75rem"></i></button>
      </div>`;
    return div;
  }

  /* ══ Helpers contadores ══ */
  function renumerarPregs(card) {
    card.querySelectorAll('.int-preg-num').forEach((el, i) => el.textContent = i + 1);
  }
  function actualizarCountComp(card) {
    const n = card.querySelectorAll('.int-preg-row').length;
    const el = document.getElementById('comp-count-' + card.dataset.compId);
    if (el) el.textContent = n + 'p';
  }
  function actualizarTituloEtapa(etapaId) {
    // nada visual extra que actualizar en el título por ahora
  }

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

  /* ══════════════════════════════════════
     SORTABLE — reorder componentes y preguntas
  ══════════════════════════════════════ */
  const REORDER_COMP_URL = '{{ route("adm-integridad.componente.reorder") }}';
  const REORDER_PREG_URL = '{{ route("adm-integridad.pregunta.reorder") }}';

  // Inicializa Sortable en lista de preguntas de un comp-body
  function initSortablePregs(compBodyEl) {
    if (!compBodyEl || compBodyEl._sortablePreg) return;
    compBodyEl._sortablePreg = Sortable.create(compBodyEl, {
      animation: 150,
      handle: '.preg-drag-handle',
      draggable: '.int-preg-row',
      ghostClass: 'sortable-ghost',
      dragClass: 'sortable-drag',
      onEnd: async function () {
        const items = Array.from(compBodyEl.querySelectorAll('.int-preg-row')).map((el, i) => ({
          id: parseInt(el.dataset.pregId), orden: i,
        }));
        compBodyEl.querySelectorAll('.int-preg-num').forEach((el, i) => el.textContent = i + 1);
        try { await api(REORDER_PREG_URL, 'POST', { items }); }
        catch (err) { toast('error', 'Error al guardar orden: ' + err.message); }
      },
    });
  }

  // Inicializa Sortable en contenedor de componentes de una etapa
  function initSortableComps(etapaContentEl) {
    if (!etapaContentEl || etapaContentEl._sortableComp) return;
    // Inicializar preguntas de cada componente ya presente
    etapaContentEl.querySelectorAll('.int-comp-body').forEach(initSortablePregs);
    // Sortable de componentes — necesitamos un wrapper directo; usamos el contenido del etapa-content
    // Los .int-comp-card son hermanos directos dentro de etapaContentEl
    etapaContentEl._sortableComp = Sortable.create(etapaContentEl, {
      animation: 150,
      handle: '.comp-drag-handle',
      draggable: '.int-comp-card',
      ghostClass: 'sortable-ghost',
      dragClass: 'sortable-drag',
      onEnd: async function () {
        const items = Array.from(etapaContentEl.querySelectorAll(':scope > .int-comp-card')).map((el, i) => ({
          id: parseInt(el.dataset.compId), orden: i,
        }));
        try { await api(REORDER_COMP_URL, 'POST', { items }); }
        catch (err) { toast('error', 'Error al guardar orden: ' + err.message); }
      },
    });
  }

  // Inicializar en todas las etapas renderizadas en el servidor
  document.querySelectorAll('.int-etapa-content').forEach(initSortableComps);

  // Exponer para que crearCompCard y crearPregRow puedan activar Sortable en nuevos elementos
  window._intSortablePregs = initSortablePregs;
  window._intSortableComps = initSortableComps;

})();
</script>
@endsection
