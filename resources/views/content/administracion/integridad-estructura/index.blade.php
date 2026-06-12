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
/* ── Layout 3 paneles ── */
.sci-layout { display: grid; grid-template-columns: 280px 260px 1fr; gap: 1rem; align-items: start; min-height: 520px; }
@media (max-width: 1199px) { .sci-layout { grid-template-columns: 1fr; } }

.sci-col { display: flex; flex-direction: column; gap: 0; }
.sci-col-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: .75rem 1rem; border-radius: .5rem .5rem 0 0;
  font-weight: 600; font-size: .8125rem; letter-spacing: .04em; text-transform: uppercase;
}
.sci-col-header.etapas { background: var(--bs-warning); color: #000; }
.sci-col-header.comps  { background: var(--bs-info);    color: #fff; }
.sci-col-header.pregs  { background: var(--bs-success);  color: #fff; }
.sci-col-body { border: 1px solid var(--bs-border-color); border-top: none; border-radius: 0 0 .5rem .5rem; background: var(--bs-body-bg); overflow: hidden; }

.sci-item {
  display: flex; align-items: center; gap: .5rem;
  padding: .625rem .875rem; border-bottom: 1px solid var(--bs-border-color);
  cursor: pointer; transition: background .15s, box-shadow .15s; position: relative;
  font-size: .875rem;
}
.sci-item:last-child { border-bottom: none; }
.sci-item:hover { background: var(--bs-tertiary-bg); }

.sci-item.active {
  background: rgba(var(--bs-warning-rgb), .12);
  font-weight: 600;
  box-shadow: inset 4px 0 0 var(--bs-warning);
}
.sci-item.active.comp-item {
  background: rgba(var(--bs-info-rgb), .12);
  box-shadow: inset 4px 0 0 var(--bs-info);
}
.sci-item.active .sci-item-badge { filter: brightness(1.1); box-shadow: 0 0 0 2px rgba(255,255,255,.6), 0 0 0 4px var(--bs-warning); }
.sci-item.active.comp-item .sci-item-badge { box-shadow: 0 0 0 2px rgba(255,255,255,.6), 0 0 0 4px var(--bs-info); }

.sci-item-badge {
  flex-shrink: 0; width: 24px; height: 24px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .68rem; font-weight: 700; transition: box-shadow .15s;
}
.sci-item-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sci-item-actions { display: flex; gap: .25rem; flex-shrink: 0; }
.sci-item-meta { font-size: .7rem; color: var(--bs-secondary-color); white-space: nowrap; }

.badge-inactivo { font-size: .6rem; padding: .15em .4em; }

.sci-empty { padding: 2rem 1rem; text-align: center; color: var(--bs-secondary-color); font-size: .8125rem; }
.sci-empty i { font-size: 2rem; display: block; margin-bottom: .5rem; opacity: .4; }

/* Icon picker */
.icon-picker { display: flex; flex-wrap: wrap; gap: .375rem; max-height: 180px; overflow-y: auto; padding: .5rem; background: var(--bs-tertiary-bg); border-radius: .375rem; border: 1px solid var(--bs-border-color); }
.icon-picker-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: .375rem; border: 1px solid transparent; cursor: pointer; background: var(--bs-body-bg); transition: all .15s; font-size: 1.1rem; }
.icon-picker-btn:hover { border-color: var(--bs-info); background: rgba(var(--bs-info-rgb),.1); }
.icon-picker-btn.selected { border-color: var(--bs-info); background: rgba(var(--bs-info-rgb),.2); box-shadow: 0 0 0 2px rgba(var(--bs-info-rgb),.3); }

.sci-count-badge { font-size: .7rem; background: rgba(0,0,0,.15); border-radius: 50rem; padding: .1em .5em; }

.link-ficha-text { font-size: .78rem; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }

@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin .8s linear infinite; display: inline-block; }

.sci-leyenda { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; font-size: .78rem; color: var(--bs-secondary-color); }
.sci-leyenda-step { display: flex; align-items: center; gap: .375rem; }
.sci-leyenda-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.sci-leyenda-arrow { opacity: .4; font-size: .9rem; }
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

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1"><i class="ti tabler-shield-check me-2 text-warning"></i>Modelo de Integridad</h4>
    <p class="mb-0 text-muted small">Gestiona la estructura: Etapas → Componentes → Preguntas</p>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <form method="GET" class="d-flex align-items-center gap-2">
      <label class="form-label mb-0 text-muted small">Año</label>
      <select name="anio" class="form-select form-select-sm" style="width:90px" onchange="this.form.submit()">
        @foreach($anios->merge([now()->year])->unique()->sortDesc() as $a)
          <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
      </select>
    </form>
  </div>
</div>

{{-- ══ LAYOUT 3 COLUMNAS ══ --}}
<div class="sci-layout">

  {{-- ── COL 1: ETAPAS ── --}}
  <div class="sci-col">
    <div class="sci-col-header etapas">
      <span><i class="ti tabler-layers-subtract me-2"></i>Etapas</span>
      <span class="sci-count-badge" id="cnt-etapas">{{ $etapas->count() }}</span>
    </div>
    <div class="sci-col-body">
      <div id="lista-etapas">
        @if($etapas->isEmpty())
          <div class="sci-empty"><i class="ti tabler-layers-subtract"></i>Sin etapas para {{ $anio }}</div>
        @else
          @foreach($etapas as $etapa)
          <div class="sci-item etapa-item {{ $loop->first ? 'active' : '' }}"
               data-etapa-id="{{ $etapa->id }}"
               data-etapa-nombre="{{ e($etapa->nombre) }}"
               data-etapa-descripcion="{{ e($etapa->descripcion ?? '') }}"
               data-etapa-anio="{{ $etapa->anio }}"
               data-etapa-activo="{{ $etapa->activo ? 1 : 0 }}">
            <span class="sci-item-badge bg-warning text-dark">{{ $loop->iteration }}</span>
            <span class="sci-item-name">{{ $etapa->nombre }}
              @if(!$etapa->activo)<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>@endif
            </span>
            <span class="sci-item-meta">{{ $etapa->componentes->count() }}c</span>
            @can('integridad.editar')
            <div class="sci-item-actions">
              <button class="btn btn-icon btn-sm btn-warning btn-editar-etapa" title="Editar" onclick="event.stopPropagation()">
                <i class="ti tabler-edit"></i>
              </button>
              <button class="btn btn-icon btn-sm btn-danger btn-eliminar-etapa" title="Eliminar"
                data-url="{{ route('adm-integridad.etapa.destroy', $etapa) }}"
                data-nombre="{{ e($etapa->nombre) }}" onclick="event.stopPropagation()">
                <i class="ti tabler-trash"></i>
              </button>
            </div>
            @endcan
          </div>
          @endforeach
        @endif
      </div>
      @can('integridad.editar')
      <div class="p-2 border-top">
        <button class="btn btn-sm btn-warning w-100" id="btn-nueva-etapa-open">
          <i class="ti tabler-plus me-1"></i>Nueva Etapa
        </button>
      </div>
      @endcan
    </div>
  </div>

  {{-- ── COL 2: COMPONENTES ── --}}
  <div class="sci-col">
    <div class="sci-col-header comps">
      <span><i class="ti tabler-puzzle me-2"></i>Componentes</span>
      <span class="sci-count-badge" id="cnt-comps">0</span>
    </div>
    <div class="sci-col-body">
      <div id="lista-componentes">
        <div class="sci-empty"><i class="ti tabler-hand-click"></i>Selecciona una etapa</div>
      </div>
      @can('integridad.editar')
      <div class="p-2 border-top" id="btn-nuevo-comp-wrap" style="display:none">
        <button class="btn btn-sm btn-info w-100" id="btn-nuevo-comp-open">
          <i class="ti tabler-plus me-1"></i>Nuevo Componente
        </button>
      </div>
      @endcan
    </div>
  </div>

  {{-- ── COL 3: PREGUNTAS ── --}}
  <div class="sci-col">
    <div class="sci-col-header pregs">
      <span><i class="ti tabler-help-circle me-2"></i>Preguntas</span>
      <span class="sci-count-badge" id="cnt-pregs">0</span>
    </div>
    <div class="sci-col-body">
      <div id="lista-preguntas">
        <div class="sci-empty"><i class="ti tabler-hand-click"></i>Selecciona un componente</div>
      </div>
      @can('integridad.editar')
      <div class="p-2 border-top" id="btn-nueva-preg-wrap" style="display:none">
        <button class="btn btn-sm btn-success w-100" id="btn-nueva-preg-open">
          <i class="ti tabler-plus me-1"></i>Nueva Pregunta
        </button>
      </div>
      @endcan
    </div>
  </div>

</div>

{{-- ── Leyenda ── --}}
<div class="mt-3 px-1 sci-leyenda">
  <span class="sci-leyenda-step">
    <span class="sci-leyenda-dot" style="background:var(--bs-warning)"></span>
    <strong class="text-warning" style="font-size:.78rem">1. Selecciona una Etapa</strong>
    <span>— agrupa los componentes del Modelo</span>
  </span>
  <span class="sci-leyenda-arrow"><i class="ti tabler-chevron-right"></i></span>
  <span class="sci-leyenda-step">
    <span class="sci-leyenda-dot" style="background:var(--bs-info)"></span>
    <strong class="text-info" style="font-size:.78rem">2. Selecciona un Componente</strong>
    <span>— agrupa las preguntas de verificación</span>
  </span>
  <span class="sci-leyenda-arrow"><i class="ti tabler-chevron-right"></i></span>
  <span class="sci-leyenda-step">
    <span class="sci-leyenda-dot" style="background:var(--bs-success)"></span>
    <strong class="text-success" style="font-size:.78rem">3. Gestiona las Preguntas</strong>
    <span>— cada pregunta genera actividades de cumplimiento</span>
  </span>
  <span class="ms-auto d-flex align-items-center gap-1 text-muted" style="font-size:.72rem">
    <i class="ti tabler-info-circle"></i> Usa <i class="ti tabler-edit text-info mx-1"></i> para editar y <i class="ti tabler-trash text-danger mx-1"></i> para eliminar
  </span>
</div>

@can('integridad.editar')
{{-- ══ MODAL: NUEVA ETAPA ══ --}}
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
            <input type="text" id="nueva_etapa_nombre" class="form-control" required placeholder="Ej: Planificación">
          </div>
          <div class="col-4">
            <label class="form-label fw-semibold">Año <span class="text-danger">*</span></label>
            <input type="number" id="nueva_etapa_anio" class="form-control" value="{{ $anio }}" min="2020" max="2099" required>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="nueva_etapa_descripcion" class="form-control" rows="2" placeholder="Descripción opcional..."></textarea>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="nueva_etapa_activo" checked>
              <label class="form-check-label" for="nueva_etapa_activo">Activo</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning" id="btn-guardar-etapa"><i class="ti tabler-device-floppy me-1"></i>Guardar Etapa</button>
      </div>
    </div>
  </div>
</div>

{{-- ══ MODAL: EDITAR ETAPA ══ --}}
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
            <input type="text" id="edit_etapa_nombre" class="form-control" required>
          </div>
          <div class="col-4">
            <label class="form-label fw-semibold">Año</label>
            <input type="number" id="edit_etapa_anio" class="form-control" min="2020" max="2099" required>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="edit_etapa_descripcion" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="edit_etapa_activo">
              <label class="form-check-label" for="edit_etapa_activo">Activo</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-info" id="btn-actualizar-etapa"><i class="ti tabler-device-floppy me-1"></i>Actualizar Etapa</button>
      </div>
    </div>
  </div>
</div>

{{-- ══ MODAL: NUEVO COMPONENTE ══ --}}
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
            <input type="text" id="nuevo_comp_nombre" class="form-control" required placeholder="Ej: Ética pública">
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
            <textarea id="nuevo_comp_descripcion" class="form-control" rows="2" placeholder="Descripción opcional..."></textarea>
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
        <button type="button" class="btn btn-info" id="btn-guardar-comp"><i class="ti tabler-device-floppy me-1"></i>Guardar Componente</button>
      </div>
    </div>
  </div>
</div>

{{-- ══ MODAL: EDITAR COMPONENTE ══ --}}
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
            <input type="text" id="edit_comp_nombre" class="form-control" required>
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
        <button type="button" class="btn btn-info" id="btn-actualizar-comp"><i class="ti tabler-device-floppy me-1"></i>Actualizar Componente</button>
      </div>
    </div>
  </div>
</div>

{{-- ══ MODAL: NUEVA PREGUNTA ══ --}}
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
            <textarea id="nueva_preg_nombre" class="form-control" rows="4" required placeholder="Ej: ¿La entidad cuenta con un plan de integridad aprobado?"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Link de ficha <span class="text-muted fw-normal small">(URL opcional)</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti tabler-link"></i></span>
              <input type="url" id="nueva_preg_link" class="form-control" placeholder="https://...">
            </div>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="nueva_preg_activo" checked>
              <label class="form-check-label" for="nueva_preg_activo">Activo</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btn-guardar-preg"><i class="ti tabler-device-floppy me-1"></i>Guardar Pregunta</button>
      </div>
    </div>
  </div>
</div>

{{-- ══ MODAL: EDITAR PREGUNTA ══ --}}
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
            <textarea id="edit_preg_nombre" class="form-control" rows="4" required></textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Link de ficha</label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti tabler-link"></i></span>
              <input type="url" id="edit_preg_link" class="form-control" placeholder="https://...">
            </div>
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="edit_preg_activo">
              <label class="form-check-label" for="edit_preg_activo">Activo</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btn-actualizar-preg"><i class="ti tabler-device-floppy me-1"></i>Actualizar Pregunta</button>
      </div>
    </div>
  </div>
</div>
@endcan
@endsection

@section('page-script')
<script>
function initIntegridad() {
  'use strict';

  const CSRF = document.querySelector('meta[name="csrf-token"]').content;
  const HEADERS = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF };

  // Estado activo
  let etapaActivaId   = null;
  let etapaActivaItem = null;
  let compActivoId    = null;
  let compActivoItem  = null;

  // IDs de edición
  let editEtapaId = null;
  let editCompId  = null;
  let editPregId  = null;

  /* ────── TOAST ────── */
  function toast(icon, title, timer) {
    const cols = { success:'#28c76f', error:'#ea5455', warning:'#ff9f43', info:'#00cfe8' };
    if (typeof Swal === 'undefined') { console.warn('toast:', icon, title); return; }
    Swal.fire({
      toast: true, position: 'top-end', icon, title,
      showConfirmButton: false, timer: timer||2800, timerProgressBar: true,
      customClass: { popup: 'pulso-toast' }, iconColor: cols[icon]||cols.info,
    });
  }

  /* ────── ICON PICKER ────── */
  document.querySelectorAll('.icon-picker-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const ico    = this.dataset.icon;
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
      const sel = document.getElementById(pickerId).querySelector(`[data-icon="${ico}"]`);
      if (sel) sel.classList.add('selected');
    } else {
      document.getElementById(previewId).className = 'ti text-info fs-5';
      document.getElementById(labelId).textContent = 'Sin ícono';
    }
  }

  /* ────── HELPERS DOM ────── */
  function esc(str) {
    return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }
  function getModal(id) { return bootstrap.Modal.getOrCreateInstance(document.getElementById(id)); }
  function hideModal(id) { getModal(id).hide(); }

  /* ────── FETCH WRAPPER ────── */
  async function apiFetch(url, method, body) {
    const opts = { method, headers: HEADERS };
    if (body) opts.body = JSON.stringify(body);
    const r = await fetch(url, opts);
    const json = await r.json();
    if (!r.ok) throw new Error(json.message || 'Error del servidor');
    return json;
  }

  /* ══════════════════════════════════════════
     ETAPAS
  ══════════════════════════════════════════ */
  function renderEtapaItem(e, numero) {
    const div = document.createElement('div');
    div.className = 'sci-item etapa-item';
    div.dataset.etapaId          = e.id;
    div.dataset.etapaNombre      = e.nombre;
    div.dataset.etapaDescripcion = e.descripcion || '';
    div.dataset.etapaAnio        = e.anio;
    div.dataset.etapaActivo      = e.activo ? '1' : '0';
    div.innerHTML = `
      <span class="sci-item-badge bg-warning text-dark">${numero}</span>
      <span class="sci-item-name">${esc(e.nombre)}${!e.activo?'<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>':''}</span>
      <span class="sci-item-meta">0c</span>
      <div class="sci-item-actions">
        <button class="btn btn-icon btn-sm btn-warning btn-editar-etapa" title="Editar"><i class="ti tabler-edit"></i></button>
        <button class="btn btn-icon btn-sm btn-danger btn-eliminar-etapa" title="Eliminar"
          data-url="${esc(window.location.pathname.split('/administracion')[0])}/administracion/integridad/etapa/${e.id}"
          data-nombre="${esc(e.nombre)}"><i class="ti tabler-trash"></i></button>
      </div>`;
    bindEtapaItem(div);
    return div;
  }

  function bindEtapaItem(el) {
    el.addEventListener('click', function () {
      document.querySelectorAll('.etapa-item').forEach(i => i.classList.remove('active'));
      this.classList.add('active');
      etapaActivaId   = this.dataset.etapaId;
      etapaActivaItem = this;
      compActivoId   = null;
      compActivoItem = null;
      cargarComponentes(etapaActivaId);
      resetPreguntas();
    });
    el.querySelector('.btn-editar-etapa').addEventListener('click', function (e) {
      e.stopPropagation();
      const item = this.closest('.etapa-item');
      editEtapaId = item.dataset.etapaId;
      document.getElementById('edit_etapa_nombre').value      = item.dataset.etapaNombre;
      document.getElementById('edit_etapa_descripcion').value = item.dataset.etapaDescripcion;
      document.getElementById('edit_etapa_anio').value        = item.dataset.etapaAnio;
      document.getElementById('edit_etapa_activo').checked    = item.dataset.etapaActivo === '1';
      getModal('modalEditarEtapa').show();
    });
    el.querySelector('.btn-eliminar-etapa').addEventListener('click', function (e) {
      e.stopPropagation();
      confirmarEliminar(this.dataset.url, this.dataset.nombre, () => {
        const item = this.closest('.etapa-item');
        item.remove();
        renumerarLista('#lista-etapas .etapa-item', '.sci-item-badge');
        document.getElementById('cnt-etapas').textContent =
          document.querySelectorAll('#lista-etapas .etapa-item').length;
        resetComponentes();
        resetPreguntas();
        toast('success', 'Etapa eliminada.');
      });
    });
  }

  // Inicializar etapas estáticas
  document.querySelectorAll('.etapa-item').forEach(el => bindEtapaItem(el));

  // Nueva etapa
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
      const res = await apiFetch('{{ route("adm-integridad.etapa.store") }}', 'POST', {
        nombre,
        descripcion: document.getElementById('nueva_etapa_descripcion').value,
        anio: document.getElementById('nueva_etapa_anio').value,
        activo: document.getElementById('nueva_etapa_activo').checked ? 1 : 0,
      });
      hideModal('modalNuevaEtapa');
      const lista = document.getElementById('lista-etapas');
      // quitar empty state
      lista.querySelectorAll('.sci-empty').forEach(e => e.remove());
      const num = document.querySelectorAll('#lista-etapas .etapa-item').length + 1;
      const newItem = renderEtapaItem(res.etapa, num);
      lista.appendChild(newItem);
      document.getElementById('cnt-etapas').textContent = num;
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  // Actualizar etapa
  document.getElementById('btn-actualizar-etapa')?.addEventListener('click', async () => {
    const nombre = document.getElementById('edit_etapa_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    const btn = document.getElementById('btn-actualizar-etapa');
    btn.disabled = true;
    try {
      const res = await apiFetch(`/administracion/integridad/etapa/${editEtapaId}`, 'PUT', {
        nombre,
        descripcion: document.getElementById('edit_etapa_descripcion').value,
        anio: document.getElementById('edit_etapa_anio').value,
        activo: document.getElementById('edit_etapa_activo').checked ? 1 : 0,
      });
      hideModal('modalEditarEtapa');
      // Actualizar item en DOM
      const item = document.querySelector(`.etapa-item[data-etapa-id="${editEtapaId}"]`);
      if (item) {
        item.dataset.etapaNombre      = res.etapa.nombre;
        item.dataset.etapaDescripcion = res.etapa.descripcion || '';
        item.dataset.etapaAnio        = res.etapa.anio;
        item.dataset.etapaActivo      = res.etapa.activo ? '1' : '0';
        item.querySelector('.sci-item-name').innerHTML =
          esc(res.etapa.nombre) + (!res.etapa.activo ? '<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>' : '');
      }
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  /* ══════════════════════════════════════════
     COMPONENTES
  ══════════════════════════════════════════ */
  function cargarComponentes(etapaId) {
    const lista = document.getElementById('lista-componentes');
    const cnt   = document.getElementById('cnt-comps');
    const btnW  = document.getElementById('btn-nuevo-comp-wrap');
    lista.innerHTML = '<div class="sci-empty"><i class="ti tabler-loader-2 spin"></i>Cargando...</div>';
    cnt.textContent = '…';

    fetch(`/api/integridad/componentes-admin?etapa_id=${etapaId}`)
      .then(r => r.json())
      .then(data => {
        cnt.textContent = data.length;
        if (btnW) btnW.style.display = 'block';
        if (!data.length) {
          lista.innerHTML = '<div class="sci-empty"><i class="ti tabler-puzzle"></i>Sin componentes. Agrega el primero.</div>';
          return;
        }
        lista.innerHTML = '';
        data.forEach((c, i) => lista.appendChild(renderCompItem(c, i + 1)));
      })
      .catch(() => { lista.innerHTML = '<div class="sci-empty"><i class="ti tabler-alert-circle"></i>Error al cargar.</div>'; });
  }

  function renderCompItem(c, numero) {
    const div = document.createElement('div');
    div.className = 'sci-item comp-item';
    div.dataset.compId          = c.id;
    div.dataset.compNombre      = c.nombre;
    div.dataset.compIcono       = c.icono || '';
    div.dataset.compDescripcion = c.descripcion || '';
    div.dataset.compActivo      = c.activo ? '1' : '0';
    div.dataset.urlDestroy      = c.url_destroy;
    div.innerHTML = `
      <span class="sci-item-badge bg-info text-white">${numero}</span>
      ${c.icono ? `<i class="ti ${esc(c.icono)} text-info" style="font-size:.9rem;flex-shrink:0"></i>` : ''}
      <span class="sci-item-name">${esc(c.nombre)}${!c.activo?'<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>':''}</span>
      <span class="sci-item-meta">${c.preguntas_count}p</span>
      <div class="sci-item-actions">
        <button class="btn btn-icon btn-sm btn-info btn-editar-comp" title="Editar"><i class="ti tabler-edit"></i></button>
        <button class="btn btn-icon btn-sm btn-danger btn-eliminar-comp" title="Eliminar"
          data-nombre="${esc(c.nombre)}"><i class="ti tabler-trash"></i></button>
      </div>`;
    bindCompItem(div);
    return div;
  }

  function bindCompItem(el) {
    el.addEventListener('click', function () {
      document.querySelectorAll('#lista-componentes .comp-item').forEach(i => i.classList.remove('active'));
      this.classList.add('active');
      compActivoId   = this.dataset.compId;
      compActivoItem = this;
      cargarPreguntas(compActivoId);
    });
    el.querySelector('.btn-editar-comp').addEventListener('click', function (e) {
      e.stopPropagation();
      const item = this.closest('.comp-item');
      editCompId = item.dataset.compId;
      document.getElementById('edit_comp_nombre').value      = item.dataset.compNombre;
      document.getElementById('edit_comp_descripcion').value = item.dataset.compDescripcion;
      document.getElementById('edit_comp_activo').checked    = item.dataset.compActivo === '1';
      setIconPicker('edit_icon_picker','edit_comp_icono','edit_comp_icono_preview','edit_comp_icono_label', item.dataset.compIcono);
      getModal('modalEditarComponente').show();
    });
    el.querySelector('.btn-eliminar-comp').addEventListener('click', function (e) {
      e.stopPropagation();
      const item = this.closest('.comp-item');
      confirmarEliminar(item.dataset.urlDestroy, item.dataset.compNombre, () => {
        item.remove();
        renumerarLista('#lista-componentes .comp-item', '.sci-item-badge');
        document.getElementById('cnt-comps').textContent =
          document.querySelectorAll('#lista-componentes .comp-item').length;
        resetPreguntas();
        toast('success', 'Componente eliminado.');
      });
    });
  }

  function resetComponentes() {
    document.getElementById('lista-componentes').innerHTML = '<div class="sci-empty"><i class="ti tabler-hand-click"></i>Selecciona una etapa</div>';
    document.getElementById('cnt-comps').textContent = '0';
    const btnW = document.getElementById('btn-nuevo-comp-wrap');
    if (btnW) btnW.style.display = 'none';
  }

  // Nuevo componente
  document.getElementById('btn-nuevo-comp-open')?.addEventListener('click', () => {
    document.getElementById('nuevo_comp_nombre').value      = '';
    document.getElementById('nuevo_comp_descripcion').value = '';
    document.getElementById('nuevo_comp_activo').checked    = true;
    setIconPicker('nuevo_icon_picker','nuevo_comp_icono','nuevo_comp_icono_preview','nuevo_comp_icono_label', '');
    getModal('modalNuevoComponente').show();
  });

  document.getElementById('btn-guardar-comp')?.addEventListener('click', async () => {
    const nombre = document.getElementById('nuevo_comp_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    if (!etapaActivaId) { toast('warning', 'Selecciona una etapa primero.'); return; }
    const btn = document.getElementById('btn-guardar-comp');
    btn.disabled = true;
    try {
      const res = await apiFetch('{{ route("adm-integridad.componente.store") }}', 'POST', {
        etapa_id:    etapaActivaId,
        nombre,
        icono:       document.getElementById('nuevo_comp_icono').value,
        descripcion: document.getElementById('nuevo_comp_descripcion').value,
        activo:      document.getElementById('nuevo_comp_activo').checked ? 1 : 0,
      });
      hideModal('modalNuevoComponente');
      const lista = document.getElementById('lista-componentes');
      lista.querySelectorAll('.sci-empty').forEach(e => e.remove());
      const num = document.querySelectorAll('#lista-componentes .comp-item').length + 1;
      lista.appendChild(renderCompItem(res.componente, num));
      document.getElementById('cnt-comps').textContent = num;
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  // Actualizar componente
  document.getElementById('btn-actualizar-comp')?.addEventListener('click', async () => {
    const nombre = document.getElementById('edit_comp_nombre').value.trim();
    if (!nombre) { toast('warning', 'El nombre es obligatorio.'); return; }
    const btn = document.getElementById('btn-actualizar-comp');
    btn.disabled = true;
    try {
      const res = await apiFetch(`/administracion/integridad/componente/${editCompId}`, 'PUT', {
        nombre,
        icono:       document.getElementById('edit_comp_icono').value,
        descripcion: document.getElementById('edit_comp_descripcion').value,
        activo:      document.getElementById('edit_comp_activo').checked ? 1 : 0,
      });
      hideModal('modalEditarComponente');
      const item = document.querySelector(`.comp-item[data-comp-id="${editCompId}"]`);
      if (item) {
        item.dataset.compNombre      = res.componente.nombre;
        item.dataset.compIcono       = res.componente.icono || '';
        item.dataset.compDescripcion = res.componente.descripcion || '';
        item.dataset.compActivo      = res.componente.activo ? '1' : '0';
        // Refrescar display
        const ico = res.componente.icono;
        const badge = item.querySelector('.sci-item-badge').outerHTML;
        const meta  = item.querySelector('.sci-item-meta').outerHTML;
        const acts  = item.querySelector('.sci-item-actions').outerHTML;
        item.innerHTML = badge +
          (ico ? `<i class="ti ${esc(ico)} text-info" style="font-size:.9rem;flex-shrink:0"></i>` : '') +
          `<span class="sci-item-name">${esc(res.componente.nombre)}${!res.componente.activo?'<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>':''}</span>` +
          meta + acts;
        bindCompItem(item); // re-bind tras innerHTML
      }
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  /* ══════════════════════════════════════════
     PREGUNTAS
  ══════════════════════════════════════════ */
  function cargarPreguntas(compId) {
    const lista = document.getElementById('lista-preguntas');
    const cnt   = document.getElementById('cnt-pregs');
    const btnW  = document.getElementById('btn-nueva-preg-wrap');
    lista.innerHTML = '<div class="sci-empty"><i class="ti tabler-loader-2 spin"></i>Cargando...</div>';
    cnt.textContent = '…';

    fetch(`/api/integridad/preguntas-admin?componente_id=${compId}`)
      .then(r => r.json())
      .then(data => {
        cnt.textContent = data.length;
        if (btnW) btnW.style.display = 'block';
        if (!data.length) {
          lista.innerHTML = '<div class="sci-empty"><i class="ti tabler-help-circle"></i>Sin preguntas. Agrega la primera.</div>';
          return;
        }
        lista.innerHTML = '';
        data.forEach((p, i) => lista.appendChild(renderPregItem(p, i + 1)));
      })
      .catch(() => { lista.innerHTML = '<div class="sci-empty"><i class="ti tabler-alert-circle"></i>Error al cargar.</div>'; });
  }

  function renderPregItem(p, numero) {
    const div = document.createElement('div');
    div.className = 'sci-item preg-item';
    div.dataset.pregId     = p.id;
    div.dataset.pregNombre = p.nombre;
    div.dataset.pregLink   = p.link_ficha || '';
    div.dataset.pregActivo = p.activo ? '1' : '0';
    div.dataset.urlDestroy = p.url_destroy;
    div.innerHTML = `
      <span class="sci-item-badge bg-success text-white">${numero}</span>
      <div class="sci-item-name d-flex flex-column gap-0" style="min-width:0">
        <span style="font-size:.8125rem;white-space:normal;line-height:1.3">${esc(p.nombre)}${!p.activo?'<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>':''}</span>
        ${p.link_ficha
          ? `<a href="${esc(p.link_ficha)}" target="_blank" class="link-ficha-text text-info mt-1"><i class="ti tabler-link me-1" style="font-size:.75rem"></i>${esc(p.link_ficha)}</a>`
          : `<span class="text-muted" style="font-size:.7rem"><i class="ti tabler-link-off me-1"></i>Sin ficha</span>`}
      </div>
      <div class="sci-item-actions flex-shrink-0 ms-1" style="flex-direction:column;gap:.2rem">
        <button class="btn btn-icon btn-sm btn-success btn-editar-preg" title="Editar"><i class="ti tabler-edit"></i></button>
        <button class="btn btn-icon btn-sm btn-danger btn-eliminar-preg" title="Eliminar"
          data-nombre="${esc(p.nombre)}"><i class="ti tabler-trash"></i></button>
      </div>`;
    bindPregItem(div);
    return div;
  }

  function bindPregItem(el) {
    el.querySelector('.btn-editar-preg').addEventListener('click', function (e) {
      e.stopPropagation();
      const item = this.closest('.preg-item');
      editPregId = item.dataset.pregId;
      document.getElementById('edit_preg_nombre').value  = item.dataset.pregNombre;
      document.getElementById('edit_preg_link').value    = item.dataset.pregLink;
      document.getElementById('edit_preg_activo').checked = item.dataset.pregActivo === '1';
      getModal('modalEditarPregunta').show();
    });
    el.querySelector('.btn-eliminar-preg').addEventListener('click', function (e) {
      e.stopPropagation();
      const item = this.closest('.preg-item');
      confirmarEliminar(item.dataset.urlDestroy, item.dataset.pregNombre, () => {
        // Actualizar contador en comp-item activo
        if (compActivoItem) {
          const meta = compActivoItem.querySelector('.sci-item-meta');
          if (meta) {
            const n = Math.max(0, parseInt(meta.textContent) - 1);
            meta.textContent = n + 'p';
          }
        }
        item.remove();
        renumerarLista('#lista-preguntas .preg-item', '.sci-item-badge');
        document.getElementById('cnt-pregs').textContent =
          document.querySelectorAll('#lista-preguntas .preg-item').length;
        toast('success', 'Pregunta eliminada.');
      });
    });
  }

  function resetPreguntas() {
    document.getElementById('lista-preguntas').innerHTML = '<div class="sci-empty"><i class="ti tabler-hand-click"></i>Selecciona un componente</div>';
    document.getElementById('cnt-pregs').textContent = '0';
    const btnW = document.getElementById('btn-nueva-preg-wrap');
    if (btnW) btnW.style.display = 'none';
  }

  // Nueva pregunta
  document.getElementById('btn-nueva-preg-open')?.addEventListener('click', () => {
    document.getElementById('nueva_preg_nombre').value = '';
    document.getElementById('nueva_preg_link').value   = '';
    document.getElementById('nueva_preg_activo').checked = true;
    getModal('modalNuevaPregunta').show();
  });

  document.getElementById('btn-guardar-preg')?.addEventListener('click', async () => {
    const nombre = document.getElementById('nueva_preg_nombre').value.trim();
    if (!nombre) { toast('warning', 'El enunciado es obligatorio.'); return; }
    if (!compActivoId) { toast('warning', 'Selecciona un componente primero.'); return; }
    const btn = document.getElementById('btn-guardar-preg');
    btn.disabled = true;
    try {
      const res = await apiFetch('{{ route("adm-integridad.pregunta.store") }}', 'POST', {
        componente_id: compActivoId,
        nombre,
        link_ficha: document.getElementById('nueva_preg_link').value || null,
        activo:     document.getElementById('nueva_preg_activo').checked ? 1 : 0,
      });
      hideModal('modalNuevaPregunta');
      const lista = document.getElementById('lista-preguntas');
      lista.querySelectorAll('.sci-empty').forEach(e => e.remove());
      const num = document.querySelectorAll('#lista-preguntas .preg-item').length + 1;
      lista.appendChild(renderPregItem(res.pregunta, num));
      document.getElementById('cnt-pregs').textContent = num;
      // Actualizar contador en comp-item
      if (compActivoItem) {
        const meta = compActivoItem.querySelector('.sci-item-meta');
        if (meta) meta.textContent = num + 'p';
      }
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  // Actualizar pregunta
  document.getElementById('btn-actualizar-preg')?.addEventListener('click', async () => {
    const nombre = document.getElementById('edit_preg_nombre').value.trim();
    if (!nombre) { toast('warning', 'El enunciado es obligatorio.'); return; }
    const btn = document.getElementById('btn-actualizar-preg');
    btn.disabled = true;
    try {
      const res = await apiFetch(`/administracion/integridad/pregunta/${editPregId}`, 'PUT', {
        nombre,
        link_ficha: document.getElementById('edit_preg_link').value || null,
        activo:     document.getElementById('edit_preg_activo').checked ? 1 : 0,
      });
      hideModal('modalEditarPregunta');
      const item = document.querySelector(`.preg-item[data-preg-id="${editPregId}"]`);
      if (item) {
        item.dataset.pregNombre = res.pregunta.nombre;
        item.dataset.pregLink   = res.pregunta.link_ficha || '';
        item.dataset.pregActivo = res.pregunta.activo ? '1' : '0';
        const badge = item.querySelector('.sci-item-badge').outerHTML;
        const acts  = item.querySelector('.sci-item-actions').outerHTML;
        const lf    = res.pregunta.link_ficha;
        item.innerHTML = badge +
          `<div class="sci-item-name d-flex flex-column gap-0" style="min-width:0">
            <span style="font-size:.8125rem;white-space:normal;line-height:1.3">${esc(res.pregunta.nombre)}${!res.pregunta.activo?'<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>':''}</span>
            ${lf ? `<a href="${esc(lf)}" target="_blank" class="link-ficha-text text-info mt-1"><i class="ti tabler-link me-1" style="font-size:.75rem"></i>${esc(lf)}</a>`
                 : `<span class="text-muted" style="font-size:.7rem"><i class="ti tabler-link-off me-1"></i>Sin ficha</span>`}
          </div>` + acts;
        bindPregItem(item);
      }
      toast('success', res.message);
    } catch (err) { toast('error', err.message); }
    btn.disabled = false;
  });

  /* ══════════════════════════════════════════
     ELIMINAR (SweetAlert + fetch DELETE)
  ══════════════════════════════════════════ */
  function confirmarEliminar(url, nombre, onSuccess) {
    Swal.fire({
      title: '¿Eliminar?',
      html: `<span class="fw-semibold">${esc(nombre)}</span><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '<i class="ti tabler-trash me-1"></i>Sí, eliminar',
      cancelButtonText: 'Cancelar',
      customClass: { confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-label-secondary' },
      buttonsStyling: false,
    }).then(async r => {
      if (!r.isConfirmed) return;
      // pequeño delay para que Swal cierre antes de abrir el toast
      await new Promise(res => setTimeout(res, 200));
      try {
        await apiFetch(url, 'DELETE');
        onSuccess();
      } catch (err) {
        toast('error', err.message, 4500);
      }
    });
  }

  /* ══════════════════════════════════════════
     HELPER: Renumerar badges
  ══════════════════════════════════════════ */
  function renumerarLista(selector, badgeSelector) {
    document.querySelectorAll(selector).forEach((el, i) => {
      const badge = el.querySelector(badgeSelector);
      if (badge) badge.textContent = i + 1;
    });
  }

  /* ══════════════════════════════════════════
     INIT: cargar componentes de la primera etapa
  ══════════════════════════════════════════ */
  const primeraEtapa = document.querySelector('.etapa-item');
  if (primeraEtapa) {
    etapaActivaId   = primeraEtapa.dataset.etapaId;
    etapaActivaItem = primeraEtapa;
    cargarComponentes(etapaActivaId);
  }

}

// Esperar a que SweetAlert2 esté disponible antes de inicializar
(function waitForSwal() {
  if (typeof Swal !== 'undefined') { initIntegridad(); }
  else { setTimeout(waitForSwal, 50); }
})();
</script>
@endsection
