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
/* ── Layout general ── */
.sci-layout { display: grid; grid-template-columns: 280px 260px 1fr; gap: 1rem; align-items: start; min-height: 520px; }
@media (max-width: 1199px) { .sci-layout { grid-template-columns: 1fr; } }

/* ── Columna ── */
.sci-col { display: flex; flex-direction: column; gap: 0; }
.sci-col-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: .75rem 1rem; border-radius: .5rem .5rem 0 0;
  font-weight: 600; font-size: .8125rem; letter-spacing: .04em; text-transform: uppercase;
}
.sci-col-header.ejes    { background: var(--bs-primary); color: #fff; }
.sci-col-header.comps   { background: var(--bs-info);    color: #fff; }
.sci-col-header.pregs   { background: var(--bs-success);  color: #fff; }
.sci-col-body { border: 1px solid var(--bs-border-color); border-top: none; border-radius: 0 0 .5rem .5rem; background: var(--bs-body-bg); overflow: hidden; }

/* ── Items ── */
.sci-item {
  display: flex; align-items: center; gap: .5rem;
  padding: .625rem .875rem; border-bottom: 1px solid var(--bs-border-color);
  cursor: pointer; transition: background .15s, box-shadow .15s; position: relative;
  font-size: .875rem;
}
.sci-item:last-child { border-bottom: none; }
.sci-item:hover { background: var(--bs-tertiary-bg); }

/* ── Estado ACTIVO / seleccionado — más visible ── */
.sci-item.active {
  background: rgba(var(--bs-primary-rgb), .12);
  font-weight: 600;
  box-shadow: inset 4px 0 0 var(--bs-primary);
}
.sci-item.active.comp-item {
  background: rgba(var(--bs-info-rgb), .12);
  box-shadow: inset 4px 0 0 var(--bs-info);
}
/* badge cambia a color sólido cuando está activo */
.sci-item.active .sci-item-badge { filter: brightness(1.1); box-shadow: 0 0 0 2px rgba(255,255,255,.6), 0 0 0 4px var(--bs-primary); }
.sci-item.active.comp-item .sci-item-badge { box-shadow: 0 0 0 2px rgba(255,255,255,.6), 0 0 0 4px var(--bs-info); }

.sci-item-badge {
  flex-shrink: 0; width: 24px; height: 24px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .68rem; font-weight: 700; transition: box-shadow .15s;
}
.sci-item-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sci-item-actions { display: flex; gap: .25rem; flex-shrink: 0; }
.sci-item-meta { font-size: .7rem; color: var(--bs-secondary-color); white-space: nowrap; }

/* ── Inline badge inactive ── */
.badge-inactivo { font-size: .6rem; padding: .15em .4em; }

/* ── Empty state ── */
.sci-empty { padding: 2rem 1rem; text-align: center; color: var(--bs-secondary-color); font-size: .8125rem; }
.sci-empty i { font-size: 2rem; display: block; margin-bottom: .5rem; opacity: .4; }

/* ── Selector de íconos ── */
.icon-picker { display: flex; flex-wrap: wrap; gap: .375rem; max-height: 180px; overflow-y: auto; padding: .5rem; background: var(--bs-tertiary-bg); border-radius: .375rem; border: 1px solid var(--bs-border-color); }
.icon-picker-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: .375rem; border: 1px solid transparent; cursor: pointer; background: var(--bs-body-bg); transition: all .15s; font-size: 1.1rem; }
.icon-picker-btn:hover { border-color: var(--bs-info); background: rgba(var(--bs-info-rgb),.1); }
.icon-picker-btn.selected { border-color: var(--bs-info); background: rgba(var(--bs-info-rgb),.2); box-shadow: 0 0 0 2px rgba(var(--bs-info-rgb),.3); }

/* ── Contadores en header ── */
.sci-count-badge { font-size: .7rem; background: rgba(255,255,255,.25); border-radius: 50rem; padding: .1em .5em; }

/* ── Link ficha ── */
.link-ficha-text { font-size: .78rem; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }

/* ── Leyenda de flujo ── */
.sci-leyenda { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; font-size: .78rem; color: var(--bs-secondary-color); }
.sci-leyenda-step { display: flex; align-items: center; gap: .375rem; }
.sci-leyenda-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.sci-leyenda-arrow { opacity: .4; font-size: .9rem; }
</style>
@endsection

@section('content')
{{-- Breadcrumb + Header --}}
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Estructura SCI</li>
  </ol>
</nav>

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1"><i class="ti tabler-sitemap me-2 text-primary"></i>Sistema de Control Interno</h4>
    <p class="mb-0 text-muted small">Gestiona la estructura: Ejes → Componentes → Preguntas</p>
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


{{-- ══════════════════════════════════════════════ --}}
{{--   LAYOUT 3 COLUMNAS                           --}}
{{-- ══════════════════════════════════════════════ --}}
<div class="sci-layout">

  {{-- ── COL 1: EJES ── --}}
  <div class="sci-col">
    <div class="sci-col-header ejes">
      <span><i class="ti tabler-layers-intersect me-2"></i>Ejes</span>
      <span class="sci-count-badge" id="cnt-ejes">{{ $ejes->count() }}</span>
    </div>
    <div class="sci-col-body">
      @if($ejes->isEmpty())
        <div class="sci-empty"><i class="ti tabler-layers-intersect"></i>Sin ejes para {{ $anio }}</div>
      @else
        @foreach($ejes as $eje)
        <div class="sci-item eje-item {{ $loop->first ? 'active' : '' }}"
             data-eje-id="{{ $eje->id }}"
             data-eje-nombre="{{ e($eje->nombre) }}"
             data-eje-descripcion="{{ e($eje->descripcion) }}"
             data-eje-anio="{{ $eje->anio }}"
             data-eje-activo="{{ $eje->activo ? 1 : 0 }}"
             onclick="seleccionarEje(this)">
          <span class="sci-item-badge bg-primary text-white">{{ $loop->iteration }}</span>
          <span class="sci-item-name">{{ $eje->nombre }}
            @if(!$eje->activo)<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>@endif
          </span>
          <span class="sci-item-meta">{{ $eje->componentes->count() }}c</span>
          @canany(['componentes.editar','componentes.eliminar'])
          <div class="sci-item-actions">
            @can('componentes.editar')
            <button class="btn btn-icon btn-sm btn-info btn-editar-eje" title="Editar" onclick="event.stopPropagation()">
              <i class="ti tabler-edit"></i>
            </button>
            @endcan
            @can('componentes.eliminar')
            <button class="btn btn-icon btn-sm btn-danger btn-eliminar-eje" title="Eliminar"
              data-url="{{ route('adm-sci.eje.destroy', $eje) }}" data-nombre="{{ $eje->nombre }}" onclick="event.stopPropagation()">
              <i class="ti tabler-trash"></i>
            </button>
            @endcan
          </div>
          @endcanany
        </div>
        @endforeach
      @endif
      @can('componentes.crear')
      <div class="p-2 border-top">
        <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalNuevoEje">
          <i class="ti tabler-plus me-1"></i>Nuevo Eje
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
        <div class="sci-empty"><i class="ti tabler-hand-click"></i>Selecciona un eje</div>
      </div>
      @can('componentes.crear')
      <div class="p-2 border-top" id="btn-nuevo-componente-wrap" style="display:none">
        <button class="btn btn-sm btn-info w-100" id="btn-nuevo-componente" data-bs-toggle="modal" data-bs-target="#modalNuevoComponente">
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
      @can('componentes.crear')
      <div class="p-2 border-top" id="btn-nueva-pregunta-wrap" style="display:none">
        <button class="btn btn-sm btn-success w-100" id="btn-nueva-pregunta" data-bs-toggle="modal" data-bs-target="#modalNuevaPregunta">
          <i class="ti tabler-plus me-1"></i>Nueva Pregunta
        </button>
      </div>
      @endcan
    </div>
  </div>

</div>{{-- /sci-layout --}}

{{-- ── Leyenda ── --}}
<div class="mt-3 px-1 sci-leyenda">
  <span class="sci-leyenda-step">
    <span class="sci-leyenda-dot" style="background:var(--bs-primary)"></span>
    <strong class="text-primary" style="font-size:.78rem">1. Selecciona un Eje</strong>
    <span>— agrupa los componentes del SCI</span>
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

{{-- Datos JSON para JS --}}
@php
$sciJson = $ejes->map(function($eje) {
  return [
    'id'          => $eje->id,
    'nombre'      => $eje->nombre,
    'descripcion' => $eje->descripcion,
    'anio'        => $eje->anio,
    'activo'      => $eje->activo,
    'componentes' => $eje->componentes->map(function($c) {
      return [
        'id'          => $c->id,
        'nombre'      => $c->nombre,
        'icono'       => $c->icono,
        'descripcion' => $c->descripcion,
        'activo'      => $c->activo,
        'preguntas'   => $c->preguntas->map(function($p) {
          return [
            'id'         => $p->id,
            'nombre'     => $p->nombre,
            'link_ficha' => $p->link_ficha,
            'activo'     => $p->activo,
          ];
        })->values(),
      ];
    })->values(),
  ];
})->values();
@endphp
<script id="sci-data" type="application/json">{!! json_encode($sciJson) !!}</script>

@canany(['componentes.crear','componentes.editar'])

{{-- ══ MODAL: NUEVO EJE ══ --}}
<div class="modal fade" id="modalNuevoEje" tabindex="-1" aria-labelledby="lblNuevoEje">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('adm-sci.eje.store') }}">@csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="lblNuevoEje"><i class="ti tabler-layers-intersect me-2"></i>Nuevo Eje SCI</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-8">
              <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" required autofocus placeholder="Ej: Ambiente de Control">
            </div>
            <div class="col-4">
              <label class="form-label fw-semibold">Año <span class="text-danger">*</span></label>
              <input type="number" name="anio" class="form-control" value="{{ $anio }}" min="2020" max="2099" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2" placeholder="Descripción opcional..."></textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="nuevo_eje_activo" checked>
                <label class="form-check-label" for="nuevo_eje_activo">Activo</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar Eje</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ MODAL: EDITAR EJE ══ --}}
<div class="modal fade" id="modalEditarEje" tabindex="-1" aria-labelledby="lblEditarEje">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formEditarEje">@csrf @method('PUT')
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="lblEditarEje"><i class="ti tabler-edit me-2"></i>Editar Eje</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-8">
              <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="edit_eje_nombre" class="form-control" required>
            </div>
            <div class="col-4">
              <label class="form-label fw-semibold">Año</label>
              <input type="number" name="anio" id="edit_eje_anio" class="form-control" min="2020" max="2099" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" id="edit_eje_descripcion" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_eje_activo">
                <label class="form-check-label" for="edit_eje_activo">Activo</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-info"><i class="ti tabler-device-floppy me-1"></i>Actualizar Eje</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ MODAL: NUEVO COMPONENTE ══ --}}
<div class="modal fade" id="modalNuevoComponente" tabindex="-1" aria-labelledby="lblNuevoComp">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('adm-sci.componente.store') }}">@csrf
        <input type="hidden" name="eje_id" id="nuevo_comp_eje_id">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="lblNuevoComp"><i class="ti tabler-puzzle me-2"></i>Nuevo Componente</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" required autofocus placeholder="Ej: Filosofía de la Dirección">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                Ícono
                <span class="text-muted fw-normal small">— selecciona o escribe la clase</span>
                <span class="ms-auto d-flex align-items-center gap-1">
                  <i class="ti text-info fs-5" id="nuevo_comp_icono_preview"></i>
                  <span class="text-muted small" id="nuevo_comp_icono_label">Sin ícono</span>
                </span>
              </label>
              <input type="hidden" name="icono" id="nuevo_comp_icono">
              <div class="icon-picker" id="nuevo_icon_picker">
                @php $iconosList = ['tabler-crown','tabler-shield-check','tabler-chart-pie','tabler-chart-bar','tabler-clipboard-list','tabler-alert-triangle','tabler-messages','tabler-message-circle','tabler-eye','tabler-speakerphone','tabler-activity','tabler-user-check','tabler-users','tabler-building','tabler-file-certificate','tabler-scale','tabler-lock','tabler-target','tabler-trending-up','tabler-checkup-list','tabler-puzzle','tabler-compass','tabler-flag','tabler-microscope','tabler-layers-intersect','tabler-sitemap','tabler-hierarchy','tabler-map-pin','tabler-book','tabler-certificate'] @endphp
                @foreach($iconosList as $ico)
                <button type="button" class="icon-picker-btn" data-icon="{{ $ico }}" data-target="nuevo_comp_icono" data-preview="nuevo_comp_icono_preview" data-label="nuevo_comp_icono_label" data-picker="nuevo_icon_picker" title="{{ $ico }}">
                  <i class="ti {{ $ico }}"></i>
                </button>
                @endforeach
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2" placeholder="Descripción opcional..."></textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="nuevo_comp_activo" checked>
                <label class="form-check-label" for="nuevo_comp_activo">Activo</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-info"><i class="ti tabler-device-floppy me-1"></i>Guardar Componente</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ MODAL: EDITAR COMPONENTE ══ --}}
<div class="modal fade" id="modalEditarComponente" tabindex="-1" aria-labelledby="lblEditarComp">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="POST" id="formEditarComponente">@csrf @method('PUT')
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="lblEditarComp"><i class="ti tabler-edit me-2"></i>Editar Componente</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="edit_comp_nombre" class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                Ícono
                <span class="ms-auto d-flex align-items-center gap-1">
                  <i class="ti text-info fs-5" id="edit_comp_icono_preview"></i>
                  <span class="text-muted small" id="edit_comp_icono_label">Sin ícono</span>
                </span>
              </label>
              <input type="hidden" name="icono" id="edit_comp_icono">
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
              <textarea name="descripcion" id="edit_comp_descripcion" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_comp_activo">
                <label class="form-check-label" for="edit_comp_activo">Activo</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-info"><i class="ti tabler-device-floppy me-1"></i>Actualizar Componente</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ MODAL: NUEVA PREGUNTA ══ --}}
<div class="modal fade" id="modalNuevaPregunta" tabindex="-1" aria-labelledby="lblNuevoPreg">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('adm-sci.pregunta.store') }}">@csrf
        <input type="hidden" name="componente_id" id="nueva_preg_comp_id">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="lblNuevoPreg"><i class="ti tabler-help-circle me-2"></i>Nueva Pregunta</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Enunciado de la pregunta <span class="text-danger">*</span></label>
              <textarea name="nombre" class="form-control" rows="4" required autofocus placeholder="Escribe el enunciado de la pregunta SCI..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Link de ficha <span class="text-muted fw-normal small">(URL opcional)</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="ti tabler-link"></i></span>
                <input type="url" name="link_ficha" class="form-control" placeholder="https://drive.google.com/...">
              </div>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="nueva_preg_activo" checked>
                <label class="form-check-label" for="nueva_preg_activo">Activo</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="ti tabler-device-floppy me-1"></i>Guardar Pregunta</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ MODAL: EDITAR PREGUNTA ══ --}}
<div class="modal fade" id="modalEditarPregunta" tabindex="-1" aria-labelledby="lblEditarPreg">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="POST" id="formEditarPregunta">@csrf @method('PUT')
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="lblEditarPreg"><i class="ti tabler-edit me-2"></i>Editar Pregunta</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Enunciado <span class="text-danger">*</span></label>
              <textarea name="nombre" id="edit_preg_nombre" class="form-control" rows="4" required></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Link de ficha</label>
              <div class="input-group">
                <span class="input-group-text"><i class="ti tabler-link"></i></span>
                <input type="url" name="link_ficha" id="edit_preg_link" class="form-control" placeholder="https://...">
              </div>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_preg_activo">
                <label class="form-check-label" for="edit_preg_activo">Activo</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="ti tabler-device-floppy me-1"></i>Actualizar Pregunta</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endcanany
@endsection

@section('page-script')
<script>
(function () {
  'use strict';

  /* ── Datos cargados desde blade ── */
  const SCI_DATA = JSON.parse(document.getElementById('sci-data').textContent);
  let activeEjeId   = null;
  let activeCompId  = null;

  /* ────────────────────────────────────────────────
     ICON PICKER
  ──────────────────────────────────────────────── */
  document.querySelectorAll('.icon-picker-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const ico     = this.dataset.icon;
      const target  = document.getElementById(this.dataset.target);
      const preview = document.getElementById(this.dataset.preview);
      const label   = document.getElementById(this.dataset.label);
      const picker  = document.getElementById(this.dataset.picker);

      target.value = ico;
      preview.className = `ti ${ico} text-info fs-5`;
      label.textContent = ico;
      picker.querySelectorAll('.icon-picker-btn').forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
    });
  });

  function setIconPicker(pickerId, targetId, previewId, labelId, iconoValue) {
    const picker  = document.getElementById(pickerId);
    const target  = document.getElementById(targetId);
    const preview = document.getElementById(previewId);
    const label   = document.getElementById(labelId);
    target.value  = iconoValue || '';
    picker.querySelectorAll('.icon-picker-btn').forEach(b => b.classList.remove('selected'));
    if (iconoValue) {
      preview.className = `ti ${iconoValue} text-info fs-5`;
      label.textContent = iconoValue;
      const sel = picker.querySelector(`[data-icon="${iconoValue}"]`);
      if (sel) sel.classList.add('selected');
    } else {
      preview.className = 'ti text-info fs-5';
      label.textContent = 'Sin ícono';
    }
  }

  /* ────────────────────────────────────────────────
     RENDERIZAR COMPONENTES
  ──────────────────────────────────────────────── */
  function renderComponentes(ejeId) {
    const eje  = SCI_DATA.find(e => e.id === ejeId);
    const wrap = document.getElementById('lista-componentes');
    const cnt  = document.getElementById('cnt-comps');
    const btnWrap = document.getElementById('btn-nuevo-componente-wrap');

    if (!eje) { wrap.innerHTML = '<div class="sci-empty"><i class="ti tabler-hand-click"></i>Selecciona un eje</div>'; cnt.textContent = '0'; if(btnWrap) btnWrap.style.display='none'; return; }

    cnt.textContent = eje.componentes.length;
    if (btnWrap) { btnWrap.style.display = 'block'; document.getElementById('nuevo_comp_eje_id').value = ejeId; }

    if (!eje.componentes.length) {
      wrap.innerHTML = '<div class="sci-empty"><i class="ti tabler-puzzle"></i>Sin componentes. Agrega el primero.</div>';
      return;
    }

    wrap.innerHTML = eje.componentes.map((c, idx) => `
      <div class="sci-item comp-item" data-comp-id="${c.id}"
           data-comp-nombre="${escHtml(c.nombre)}" data-comp-descripcion="${escHtml(c.descripcion||'')}"
           data-comp-icono="${escHtml(c.icono||'')}" data-comp-activo="${c.activo?1:0}"
           onclick="seleccionarComponente(this)">
        <span class="sci-item-badge bg-info text-white">${idx+1}</span>
        ${c.icono ? `<i class="ti ${c.icono} text-info" style="font-size:.9rem;flex-shrink:0"></i>` : ''}
        <span class="sci-item-name">${escHtml(c.nombre)}${!c.activo?'<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>':''}</span>
        <span class="sci-item-meta">${c.preguntas.length}p</span>
        @canany(['componentes.editar','componentes.eliminar'])
        <div class="sci-item-actions">
          @can('componentes.editar')
          <button class="btn btn-icon btn-sm btn-info btn-editar-componente" title="Editar" onclick="event.stopPropagation()">
            <i class="ti tabler-edit"></i>
          </button>
          @endcan
          @can('componentes.eliminar')
          <button class="btn btn-icon btn-sm btn-danger btn-eliminar-comp" title="Eliminar"
            data-url="/administracion/sci/componente/${c.id}" data-nombre="${escHtml(c.nombre)}" onclick="event.stopPropagation()">
            <i class="ti tabler-trash"></i>
          </button>
          @endcan
        </div>
        @endcanany
      </div>
    `).join('');

    attachCompEvents();
    renderPreguntas(null);
  }

  /* ────────────────────────────────────────────────
     RENDERIZAR PREGUNTAS
  ──────────────────────────────────────────────── */
  function renderPreguntas(compId) {
    const eje   = SCI_DATA.find(e => e.id === activeEjeId);
    const comp  = eje ? eje.componentes.find(c => c.id === compId) : null;
    const wrap  = document.getElementById('lista-preguntas');
    const cnt   = document.getElementById('cnt-pregs');
    const btnWrap = document.getElementById('btn-nueva-pregunta-wrap');

    if (!comp) { wrap.innerHTML = '<div class="sci-empty"><i class="ti tabler-hand-click"></i>Selecciona un componente</div>'; cnt.textContent = '0'; if(btnWrap) btnWrap.style.display='none'; return; }

    cnt.textContent = comp.preguntas.length;
    if (btnWrap) { btnWrap.style.display = 'block'; document.getElementById('nueva_preg_comp_id').value = compId; }

    if (!comp.preguntas.length) {
      wrap.innerHTML = '<div class="sci-empty"><i class="ti tabler-help-circle"></i>Sin preguntas. Agrega la primera.</div>';
      return;
    }

    wrap.innerHTML = comp.preguntas.map((p, idx) => `
      <div class="sci-item preg-item" data-preg-id="${p.id}"
           data-preg-nombre="${escHtml(p.nombre)}" data-preg-link="${escHtml(p.link_ficha||'')}"
           data-preg-activo="${p.activo?1:0}">
        <span class="sci-item-badge bg-success text-white">${idx+1}</span>
        <div class="sci-item-name d-flex flex-column gap-0" style="min-width:0">
          <span style="font-size:.8125rem;white-space:normal;line-height:1.3">${escHtml(p.nombre)}${!p.activo?'<span class="badge bg-label-danger badge-inactivo ms-1">Off</span>':''}</span>
          ${p.link_ficha ? `<a href="${escHtml(p.link_ficha)}" target="_blank" class="link-ficha-text text-info mt-1" title="${escHtml(p.link_ficha)}"><i class="ti tabler-link me-1" style="font-size:.75rem"></i>${escHtml(p.link_ficha)}</a>` : '<span class="text-muted" style="font-size:.7rem"><i class="ti tabler-link-off me-1"></i>Sin ficha</span>'}
        </div>
        @canany(['componentes.editar','componentes.eliminar'])
        <div class="sci-item-actions flex-shrink-0 ms-1" style="flex-direction:column;gap:.2rem">
          @can('componentes.editar')
          <button class="btn btn-icon btn-sm btn-success btn-editar-pregunta" title="Editar" onclick="event.stopPropagation()">
            <i class="ti tabler-edit"></i>
          </button>
          @endcan
          @can('componentes.eliminar')
          <button class="btn btn-icon btn-sm btn-danger btn-eliminar-preg" title="Eliminar"
            data-url="/administracion/sci/pregunta/${p.id}" data-nombre="${escHtml(p.nombre)}" onclick="event.stopPropagation()">
            <i class="ti tabler-trash"></i>
          </button>
          @endcan
        </div>
        @endcanany
      </div>
    `).join('');

    attachPregEvents();
  }

  /* ────────────────────────────────────────────────
     SELECCIÓN
  ──────────────────────────────────────────────── */
  window.seleccionarEje = function(el) {
    document.querySelectorAll('.eje-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    activeEjeId  = parseInt(el.dataset.ejeId);
    activeCompId = null;
    renderComponentes(activeEjeId);
  };

  window.seleccionarComponente = function(el) {
    document.querySelectorAll('.comp-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    activeCompId = parseInt(el.dataset.compId);
    renderPreguntas(activeCompId);
  };

  /* ────────────────────────────────────────────────
     EVENTOS EJES (estáticos)
  ──────────────────────────────────────────────── */
  document.querySelectorAll('.btn-editar-eje').forEach(btn => {
    btn.addEventListener('click', function () {
      const item = this.closest('.eje-item');
      document.getElementById('formEditarEje').action = `/administracion/sci/eje/${item.dataset.ejeId}`;
      document.getElementById('edit_eje_nombre').value      = item.dataset.ejeNombre;
      document.getElementById('edit_eje_descripcion').value = item.dataset.ejeDescripcion || '';
      document.getElementById('edit_eje_anio').value        = item.dataset.ejeAnio;
      document.getElementById('edit_eje_activo').checked    = item.dataset.ejeActivo === '1';
      new bootstrap.Modal(document.getElementById('modalEditarEje')).show();
    });
  });

  document.querySelectorAll('.btn-eliminar-eje').forEach(btn => {
    btn.addEventListener('click', function () {
      confirmarEliminar(this.dataset.url, this.dataset.nombre);
    });
  });

  /* ────────────────────────────────────────────────
     EVENTOS COMPONENTES (dinámicos)
  ──────────────────────────────────────────────── */
  function attachCompEvents() {
    document.querySelectorAll('.btn-editar-componente').forEach(btn => {
      btn.addEventListener('click', function () {
        const item = this.closest('.comp-item');
        document.getElementById('formEditarComponente').action = `/administracion/sci/componente/${item.dataset.compId}`;
        document.getElementById('edit_comp_nombre').value      = item.dataset.compNombre;
        document.getElementById('edit_comp_descripcion').value = item.dataset.compDescripcion || '';
        document.getElementById('edit_comp_activo').checked    = item.dataset.compActivo === '1';
        setIconPicker('edit_icon_picker', 'edit_comp_icono', 'edit_comp_icono_preview', 'edit_comp_icono_label', item.dataset.compIcono);
        new bootstrap.Modal(document.getElementById('modalEditarComponente')).show();
      });
    });

    document.querySelectorAll('.btn-eliminar-comp').forEach(btn => {
      btn.addEventListener('click', function () {
        confirmarEliminar(this.dataset.url, this.dataset.nombre);
      });
    });
  }

  /* ────────────────────────────────────────────────
     EVENTOS PREGUNTAS (dinámicos)
  ──────────────────────────────────────────────── */
  function attachPregEvents() {
    document.querySelectorAll('.btn-editar-pregunta').forEach(btn => {
      btn.addEventListener('click', function () {
        const item = this.closest('.preg-item');
        document.getElementById('formEditarPregunta').action = `/administracion/sci/pregunta/${item.dataset.pregId}`;
        document.getElementById('edit_preg_nombre').value  = item.dataset.pregNombre;
        document.getElementById('edit_preg_link').value    = item.dataset.pregLink || '';
        document.getElementById('edit_preg_activo').checked = item.dataset.pregActivo === '1';
        new bootstrap.Modal(document.getElementById('modalEditarPregunta')).show();
      });
    });

    document.querySelectorAll('.btn-eliminar-preg').forEach(btn => {
      btn.addEventListener('click', function () {
        confirmarEliminar(this.dataset.url, this.dataset.nombre);
      });
    });
  }

  /* ────────────────────────────────────────────────
     TOAST (mismo patrón que otros módulos)
  ──────────────────────────────────────────────── */
  function toast(icon, title, timer) {
    const iconColors = { success:'#28c76f', error:'#ea5455', warning:'#ff9f43', info:'#00cfe8' };
    Swal.fire({
      toast: true, position: 'top-end', icon, title,
      showConfirmButton: false, timer: timer || 2800, timerProgressBar: true,
      customClass: { popup: 'pulso-toast' }, iconColor: iconColors[icon] || iconColors.info,
    });
  }
  @if(session('success')) toast('success', @json(session('success')), 3000); @endif
  @if(session('error'))   toast('error',   @json(session('error')),   4500); @endif

  /* ────────────────────────────────────────────────
     ELIMINAR (SweetAlert2)
  ──────────────────────────────────────────────── */
  function confirmarEliminar(url, nombre) {
    Swal.fire({
      title: '¿Eliminar?',
      html: `<span class="fw-semibold">${nombre}</span><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '<i class="ti tabler-trash me-1"></i>Sí, eliminar',
      cancelButtonText: 'Cancelar',
      customClass: { confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-label-secondary' },
      buttonsStyling: false,
    }).then(r => {
      if (!r.isConfirmed) return;
      const f = document.createElement('form');
      f.method = 'POST';
      f.action = url;
      f.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}"><input type="hidden" name="_method" value="DELETE">`;
      document.body.appendChild(f);
      f.submit();
    });
  }

  /* ────────────────────────────────────────────────
     HELPER: escape HTML
  ──────────────────────────────────────────────── */
  function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }

  /* ────────────────────────────────────────────────
     INIT: seleccionar primer eje automáticamente
  ──────────────────────────────────────────────── */
  const primerEje = document.querySelector('.eje-item');
  if (primerEje) {
    activeEjeId = parseInt(primerEje.dataset.ejeId);
    renderComponentes(activeEjeId);
  }

})();
</script>
@endsection
