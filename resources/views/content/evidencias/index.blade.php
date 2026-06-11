@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
$authId = auth()->id();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Evidencias - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('page-style')
<style>
/* ── KPI Cards ───────────────────────────────────────── */
.kpi-card { border-radius:14px; border:none; overflow:hidden; transition:transform .18s,box-shadow .18s; cursor:default; }
.kpi-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(0,0,0,.13); }
.kpi-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
.kpi-value { font-size:2rem; font-weight:700; line-height:1; }
.kpi-label { font-size:.72rem; font-weight:600; letter-spacing:.04em; text-transform:uppercase; opacity:.75; }
.kpi-sub   { font-size:.8rem; font-weight:600; opacity:.8; }

/* ── Tabs módulo ─────────────────────────────────────── */
.mod-tabs { gap:8px; }
.mod-tab  { border-radius:10px !important; font-weight:600; font-size:13px; padding:8px 20px;
  border:2px solid transparent !important; transition:all .2s; }
.mod-tab.active { border-color:var(--bs-primary) !important; }
.mod-tab[data-mod="integridad"].active { border-color:#ff9f43 !important; background:#fff4e0 !important; color:#ff9f43 !important; }

/* ── Tabla ───────────────────────────────────────────── */
.ev-table thead th {
  font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase;
  color:var(--bs-secondary-color); background:rgba(var(--bs-secondary-rgb),.04);
  border-bottom:2px solid rgba(var(--bs-secondary-rgb),.1); white-space:nowrap; padding:12px 14px;
}
.ev-table tbody td { padding:10px 14px; vertical-align:middle; }
.ev-table tbody tr { transition:background .12s; }
.ev-table tbody tr:hover { background:rgba(var(--bs-primary-rgb),.03); }
.estado-pill { font-size:11px; font-weight:600; padding:4px 10px; border-radius:20px; }
.mod-badge   { font-size:10px; font-weight:600; padding:3px 8px; border-radius:10px; }
.url-chip    { font-size:11px; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:inline-block; vertical-align:middle; }
.ev-actions  { display:flex; gap:4px; flex-wrap:nowrap; }
.ev-actions .btn { width:30px; height:30px; padding:0; border-radius:8px; }

/* ── Loading ─────────────────────────────────────────── */
.ev-wrapper { position:relative; min-height:120px; transition:opacity .2s; }
.ev-wrapper.loading { opacity:.5; pointer-events:none; }
#tabla-spinner { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); z-index:10;
  background:#fff; border-radius:12px; padding:16px 24px; box-shadow:0 4px 20px rgba(0,0,0,.12);
  display:none; align-items:center; gap:10px; font-size:13px; }
.ev-wrapper.loading #tabla-spinner { display:flex; }

/* ── Modal ───────────────────────────────────────────── */
.modal-header-accent { background:linear-gradient(135deg,var(--bs-primary),color-mix(in srgb,var(--bs-primary) 70%,var(--bs-info))); color:#fff; border-radius:inherit; }
.modal-header-accent .modal-title { color:#fff; }
.modal-header-integridad { background:linear-gradient(135deg,#ff9f43,#ffbe76); color:#fff; border-radius:inherit; }
.modal-header-integridad .modal-title { color:#fff; }
.modal-dialog-scrollable { height:calc(100% - 3.5rem) !important; max-height:calc(100% - 3.5rem) !important; }
.modal-dialog-scrollable .modal-content { max-height:100% !important; overflow:hidden !important; display:flex !important; flex-direction:column !important; }
.modal-dialog-scrollable .modal-body { overflow-y:auto !important; flex:1 1 auto !important; min-height:0 !important; }

/* ── Alerta notificación ─────────────────────────────── */
.notif-ev-banner { border-left:4px solid; border-radius:10px; }
.notif-ev-banner.validada  { border-color:#28c76f; background:#e8f8ee; }
.notif-ev-banner.rechazada { border-color:#ea5455; background:#fde8e8; }

/* ── Empty ───────────────────────────────────────────── */
.empty-ev { padding:60px 20px; text-align:center; color:var(--bs-secondary-color); }
.empty-ev .empty-icon { font-size:3.5rem; opacity:.3; margin-bottom:16px; }
.pagination { margin:0; }
.page-link  { border-radius:8px !important; margin:0 2px; font-size:13px; }
</style>
@endsection

@section('content')

{{-- Flash errores --}}
@if($errors->any())
  <meta name="flash-errors" content="{{ addslashes($errors->first()) }}">
@endif
@if(session('success'))
  <meta name="flash-success" content="{{ addslashes(session('success')) }}">
@endif

{{-- Notificaciones de evidencias pendientes de revisión propia --}}
@php
  $notifsPendientes = auth()->user()->notifications()
    ->whereIn('data->tipo', ['evidencia_rechazada','evidencia_validada'])
    ->whereNull('read_at')
    ->latest()
    ->take(5)
    ->get();
@endphp
@foreach($notifsPendientes as $notif)
@php $nd = $notif->data; @endphp
<div class="alert notif-ev-banner {{ $nd['tipo'] === 'evidencia_validada' ? 'validada' : 'rechazada' }} d-flex align-items-start gap-3 mb-3 p-3" role="alert">
  <i class="ti {{ $nd['icono'] }} text-{{ $nd['color'] }} fs-5 mt-1 flex-shrink-0"></i>
  <div class="flex-grow-1">
    <div class="fw-semibold mb-1" style="font-size:13px">{{ $nd['titulo'] }}</div>
    <div style="font-size:12px">{{ $nd['mensaje'] }}</div>
    @if(!empty($nd['motivo']))
    <div class="text-danger mt-1" style="font-size:11px"><i class="ti tabler-info-circle me-1"></i>{{ $nd['motivo'] }}</div>
    @endif
  </div>
  <form method="POST" action="{{ route('notifications.read', $notif->id) }}" class="flex-shrink-0">
    @csrf @method('PATCH')
    <button type="submit" class="btn btn-sm btn-label-secondary py-1 px-2" style="font-size:11px">
      <i class="ti tabler-check me-1"></i>Marcar leída
    </button>
  </form>
</div>
@endforeach

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
        <li class="breadcrumb-item active">Evidencias / SGD</li>
      </ol>
    </nav>
    <h4 class="mb-0">Gestión de Evidencias</h4>
    <p class="mb-0 text-muted small">Documentos de respaldo por actividad · SCI e Integridad</p>
  </div>
  <button class="btn btn-primary" id="btnNuevaEvidencia">
    <i class="ti tabler-plus me-1"></i>Nueva Evidencia
  </button>
</div>

{{-- ── Tabs SCI / Integridad ─────────────────────────────────────────── --}}
<ul class="nav mod-tabs mb-4" id="tabsModulo" role="tablist">
  <li class="nav-item">
    <button class="nav-link mod-tab btn btn-label-primary {{ $modulo === 'sci' ? 'active' : '' }}" data-mod="sci">
      <i class="ti tabler-shield-check me-1"></i>SCI
      <span class="badge bg-label-primary ms-2 rounded-pill" id="badge-total-sci">{{ $stats['sci']['total'] }}</span>
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link mod-tab btn btn-label-warning {{ $modulo === 'integridad' ? 'active' : '' }}" data-mod="integridad">
      <i class="ti tabler-certificate me-1"></i>Integridad
      <span class="badge bg-label-warning ms-2 rounded-pill" id="badge-total-int">{{ $stats['integridad']['total'] }}</span>
    </button>
  </li>
</ul>

{{-- ── KPI Cards (4 por módulo activo) ────────────────────────────────── --}}
<div class="row g-4 mb-4" id="kpi-row">
@php
$kpisConfig = [
  ['key'=>'total',      'label'=>'Total',      'sub'=>'evidencias',       'grad'=>'linear-gradient(135deg,#667eea,#764ba2)', 'icon'=>'tabler-files'],
  ['key'=>'validadas',  'label'=>'Validadas',  'sub'=>'aprobadas',        'grad'=>'linear-gradient(135deg,#11998e,#38ef7d)', 'icon'=>'tabler-file-check'],
  ['key'=>'pendientes', 'label'=>'Pendientes', 'sub'=>'en revisión',      'grad'=>'linear-gradient(135deg,#f7971e,#ffd200)', 'icon'=>'tabler-file-time'],
  ['key'=>'rechazadas', 'label'=>'Rechazadas', 'sub'=>'req. corrección',  'grad'=>'linear-gradient(135deg,#cb2d3e,#ef473a)', 'icon'=>'tabler-file-x'],
];
@endphp
@foreach($kpisConfig as $kp)
<div class="col-6 col-md-3">
  <div class="card kpi-card h-100" style="background:{{ $kp['grad'] }}">
    <div class="card-body d-flex align-items-center gap-3 p-4">
      <div class="kpi-icon" style="background:rgba(255,255,255,.2)">
        <i class="ti {{ $kp['icon'] }}" style="color:#fff"></i>
      </div>
      <div style="color:#fff">
        <div class="kpi-value" id="kpi-{{ $kp['key'] }}">{{ $stats[$modulo][$kp['key']] }}</div>
        <div class="kpi-label">{{ $kp['label'] }}</div>
        <div class="kpi-sub">{{ $kp['sub'] }}</div>
      </div>
    </div>
  </div>
</div>
@endforeach
</div>

{{-- ── Filtros ─────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
  <div class="card-body py-3 px-4">
    <div class="row g-3 align-items-end" id="filtros-row">

      {{-- Eje / Etapa dinámico --}}
      <div class="col-md-3" id="col-eje-sci">
        <label class="form-label fw-semibold mb-1 text-uppercase" style="font-size:11px;letter-spacing:.04em">Eje SCI</label>
        <select id="filtroEje" class="form-select">
          <option value="">Todos los ejes</option>
          @foreach($sciEjes as $eje)
          <option value="{{ $eje->id }}">{{ $eje->anio }} · {{ $eje->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 d-none" id="col-etapa-int">
        <label class="form-label fw-semibold mb-1 text-uppercase" style="font-size:11px;letter-spacing:.04em">Etapa Integridad</label>
        <select id="filtroEtapa" class="form-select">
          <option value="">Todas las etapas</option>
          @foreach($integridadEtapas as $etapa)
          <option value="{{ $etapa->id }}">{{ $etapa->anio }} · {{ $etapa->nombre }}</option>
          @endforeach
        </select>
      </div>

      {{-- Actividad --}}
      <div class="col-md-3">
        <label class="form-label fw-semibold mb-1 text-uppercase" style="font-size:11px;letter-spacing:.04em">Actividad</label>
        <select id="filtroActividad" class="form-select select2-filtro">
          <option value="">Todas las actividades</option>
          @foreach($actividades as $a)
          <option value="{{ $a->id }}">{{ $a->codigo }} — {{ Str::limit($a->nombre, 45) }}</option>
          @endforeach
        </select>
      </div>

      {{-- Estado --}}
      <div class="col-md-2">
        <label class="form-label fw-semibold mb-1 text-uppercase" style="font-size:11px;letter-spacing:.04em">Estado</label>
        <select id="filtroEstado" class="form-select">
          <option value="">Todos</option>
          <option value="pendiente">Pendiente</option>
          <option value="validado">Validado</option>
          <option value="rechazado">Rechazado</option>
        </select>
      </div>

      {{-- Buscar --}}
      <div class="col-md-3">
        <label class="form-label fw-semibold mb-1 text-uppercase" style="font-size:11px;letter-spacing:.04em">Buscar</label>
        <div class="input-group">
          <span class="input-group-text"><i class="ti tabler-search icon-16px"></i></span>
          <input type="text" id="filtroBuscar" class="form-control" placeholder="N° SGD o título…">
        </div>
      </div>

      {{-- Limpiar --}}
      <div class="col-md-1 d-flex align-items-end">
        <button id="btnLimpiar" class="btn btn-label-secondary w-100" title="Limpiar filtros">
          <i class="ti tabler-filter-off"></i>
        </button>
      </div>

    </div>
  </div>
</div>

{{-- ── Tabla ────────────────────────────────────────────────────────────── --}}
<div class="card" style="border-radius:12px">
  <div class="card-header d-flex align-items-center justify-content-between py-3 px-4" style="border-bottom:1px solid rgba(var(--bs-secondary-rgb),.1)">
    <span class="fw-semibold" style="font-size:15px">
      <i class="ti tabler-clipboard-list me-2 text-primary"></i>
      Evidencias Registradas
    </span>
    <span class="badge bg-label-secondary rounded-pill" id="contador-ev">{{ $evidencias->total() }} registros</span>
  </div>

  <div class="card-body p-0">
    <div class="ev-wrapper">
      <div id="tabla-spinner">
        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
        <span>Cargando...</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle ev-table" style="min-width:900px">
          <thead>
            <tr>
              <th style="min-width:200px">Título / N° SGD</th>
              <th style="min-width:160px">Actividad</th>
              <th style="min-width:120px">Componente</th>
              <th style="min-width:130px">Registrado por</th>
              <th style="min-width:160px">Enlace</th>
              <th style="width:100px">Estado</th>
              <th style="width:90px">Fecha</th>
              <th style="width:120px">Acciones</th>
            </tr>
          </thead>
          <tbody id="tabla-body">
            @forelse($evidencias as $ev)
            @php
              $ec      = match($ev->estado) { 'validado'=>'success','rechazado'=>'danger',default=>'warning' };
              $evComp  = $ev->actividad?->modulo === 'integridad'
                ? $ev->actividad?->integridadPregunta?->componente
                : $ev->actividad?->sciPregunta?->componente;
              $esMio   = $ev->subido_por === $authId;
            @endphp
            <tr>
              <td>
                <div class="fw-medium" style="font-size:13px">{{ $ev->titulo }}</div>
                @if($ev->numero_sgd)<small class="text-muted"><i class="ti tabler-file-description icon-10px me-1"></i>{{ $ev->numero_sgd }}</small>@endif
                @if($ev->descripcion)<div class="text-muted" style="font-size:11px;max-width:220px" title="{{ $ev->descripcion }}">{{ Str::limit($ev->descripcion, 50) }}</div>@endif
              </td>
              <td>
                <div style="font-size:12px;max-width:180px" title="{{ $ev->actividad->nombre ?? '' }}">{{ Str::limit($ev->actividad->nombre ?? '—', 40) }}</div>
                @if($ev->actividad?->codigo)<small class="text-muted" style="font-size:10px">{{ $ev->actividad->codigo }}</small>@endif
              </td>
              <td>
                <div style="font-size:12px;max-width:160px">{{ $evComp?->nombre ? Str::limit($evComp->nombre, 35) : '—' }}</div>
              </td>
              <td>
                <div style="font-size:12px">{{ $ev->subidoPor?->name ?? '—' }}</div>
                @if($ev->validadoPor && $ev->estado !== 'pendiente')
                <small class="text-muted" style="font-size:10px">{{ $ev->estado === 'validado' ? '✓' : '✗' }} {{ $ev->validadoPor->name }}</small>
                @endif
              </td>
              <td>
                @if($ev->url_documento)
                <a href="{{ $ev->url_documento }}" target="_blank" class="btn btn-sm btn-label-info d-inline-flex align-items-center gap-1" style="font-size:11px;max-width:160px">
                  <i class="ti tabler-external-link icon-12px flex-shrink-0"></i>
                  <span class="url-chip">{{ parse_url($ev->url_documento, PHP_URL_HOST) ?: $ev->url_documento }}</span>
                </a>
                @else
                <span class="text-muted fst-italic" style="font-size:11px">Sin enlace</span>
                @endif
              </td>
              <td>
                <span class="estado-pill badge bg-label-{{ $ec }}">{{ ucfirst($ev->estado) }}</span>
                @if($ev->estado === 'rechazado' && $ev->motivo_rechazo)
                <div class="text-danger mt-1" style="font-size:10px" title="{{ $ev->motivo_rechazo }}">{{ Str::limit($ev->motivo_rechazo, 30) }}</div>
                @endif
              </td>
              <td><small class="text-muted">{{ $ev->created_at->format('d/m/Y') }}</small></td>
              <td>
                <div class="ev-actions">
                  @if($ev->url_documento)
                  <a href="{{ $ev->url_documento }}" target="_blank" class="btn btn-icon btn-label-secondary" title="Abrir"><i class="ti tabler-external-link icon-14px"></i></a>
                  @endif
                  @if($ev->estado === 'pendiente' && $esMio)
                  <button class="btn btn-icon btn-label-primary btn-editar-ev"
                    data-id="{{ $ev->id }}" data-titulo="{{ $ev->titulo }}"
                    data-sgd="{{ $ev->numero_sgd ?? '' }}" data-url="{{ $ev->url_documento ?? '' }}"
                    data-descripcion="{{ $ev->descripcion ?? '' }}"
                    data-action="{{ route('sci-evidencias.update', $ev) }}" title="Editar">
                    <i class="ti tabler-edit icon-14px"></i>
                  </button>
                  @endif
                  @if($ev->estado === 'pendiente')
                  <button class="btn btn-icon btn-label-success btn-validar" data-url="{{ route('sci-evidencias.validar', $ev) }}" title="Validar"><i class="ti tabler-check icon-14px"></i></button>
                  <button class="btn btn-icon btn-label-danger btn-rechazar"  data-url="{{ route('sci-evidencias.validar', $ev) }}" title="Rechazar"><i class="ti tabler-x icon-14px"></i></button>
                  @endif
                  <form method="POST" action="{{ route('sci-evidencias.destroy', $ev) }}" class="form-eliminar-ev d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-icon btn-label-secondary" title="Eliminar"><i class="ti tabler-trash icon-14px"></i></button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="8">
              <div class="empty-ev"><div class="empty-icon"><i class="ti tabler-file-off"></i></div>
              <div class="fw-semibold mb-1">No hay evidencias registradas</div>
              <div class="text-body-secondary" style="font-size:13px">Aún no se han registrado evidencias para este módulo.</div></div>
            </td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div id="paginacion-wrapper">
    @if($evidencias->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-between py-3">
      <span class="text-muted" style="font-size:13px">
        Mostrando {{ $evidencias->firstItem() }}–{{ $evidencias->lastItem() }} de {{ $evidencias->total() }} registros
      </span>
      {{ $evidencias->links() }}
    </div>
    @endif
  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- Modal Nueva Evidencia                                               --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalNuevaEvidencia" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="{{ route('sci-evidencias.store') }}" id="formNueva">
        @csrf
        <input type="hidden" name="modulo_activo" id="nueva_modulo_activo" value="{{ $modulo }}">
        <div class="modal-header" id="modalNuevaHeader">
          <h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nueva Evidencia — <span id="nueva-modulo-label">SCI</span></h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Actividad <span class="text-danger">*</span></label>
              <select name="actividad_id" id="nueva_actividad_id" class="form-select select2-nueva" required>
                <option value="">— Seleccionar actividad —</option>
                @foreach($actividades as $a)
                <option value="{{ $a->id }}" {{ isset($actividadPresel) && $actividadPresel == $a->id ? 'selected' : '' }}>
                  {{ $a->codigo }} — {{ Str::limit($a->nombre, 60) }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-file-text icon-16px"></i></span>
                <input type="text" name="titulo" class="form-control" placeholder="Nombre o título del documento" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">N° SGD / Expediente</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-hash icon-16px"></i></span>
                <input type="text" name="numero_sgd" class="form-control" placeholder="Ej: SGD-2026-001">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Enlace <span class="badge bg-label-secondary ms-1" style="font-size:10px">Opcional</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-link icon-16px"></i></span>
                <input type="url" name="url_documento" class="form-control" placeholder="https://drive.google.com/…">
              </div>
              <div class="form-text">Google Drive, SharePoint, SGDOC u otro enlace.</div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2" placeholder="Observaciones adicionales…"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Registrar Evidencia</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Editar Evidencia --}}
<div class="modal fade" id="modalEditarEvidencia" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" id="formEditarEvidencia">
        @csrf @method('PUT')
        <div class="modal-header modal-header-accent">
          <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Evidencia</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-file-text icon-16px"></i></span>
                <input type="text" name="titulo" id="edit_ev_titulo" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">N° SGD / Expediente</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-hash icon-16px"></i></span>
                <input type="text" name="numero_sgd" id="edit_ev_sgd" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Enlace</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-link icon-16px"></i></span>
                <input type="url" name="url_documento" id="edit_ev_url" class="form-control" placeholder="https://…">
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" id="edit_ev_descripcion" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Forms ocultos validar/rechazar --}}
<form method="POST" id="formValidar" style="display:none">@csrf @method('PATCH')<input type="hidden" name="accion" value="validado"></form>
<form method="POST" id="formRechazar" style="display:none">@csrf @method('PATCH')<input type="hidden" name="accion" value="rechazado"><input type="hidden" name="motivo_rechazo" id="motivoInput"></form>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Config ────────────────────────────────────────────────────────────────
  const RUTA       = '{{ route('sci-evidencias') }}';
  const STATS_INIT = @json($stats);
  const AUTH_ID    = {{ auth()->id() }};

  let moduloActivo = '{{ $modulo }}';
  let debounce;

  const wrapper    = document.querySelector('.ev-wrapper');
  const tablaBody  = document.getElementById('tabla-body');
  const contador   = document.getElementById('contador-ev');

  // ── Flash messages ────────────────────────────────────────────────────────
  const flashErr = document.querySelector('meta[name="flash-errors"]')?.content;
  if (flashErr) Swal.fire({ icon:'error', title:'Error', text: flashErr,
    customClass:{ popup:'rounded-3', confirmButton:'btn btn-primary' }, buttonsStyling:false });

  const flashOk = document.querySelector('meta[name="flash-success"]')?.content;
  if (flashOk) Swal.fire({ icon:'success', title:'Listo', text: flashOk, timer:2800, showConfirmButton:false,
    customClass:{ popup:'rounded-3' } });

  // ── Escape XSS ────────────────────────────────────────────────────────────
  function esc(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ── KPI render ────────────────────────────────────────────────────────────
  function renderKpis(s) {
    ['total','validadas','pendientes','rechazadas'].forEach(k => {
      const el = document.getElementById('kpi-' + k);
      if (el) el.textContent = s[k] ?? 0;
    });
    const badgeSci = document.getElementById('badge-total-sci');
    const badgeInt = document.getElementById('badge-total-int');
    if (badgeSci) badgeSci.textContent = STATS_INIT.sci.total;
    if (badgeInt) badgeInt.textContent = STATS_INIT.integridad.total;
  }

  // ── Estado badge class ────────────────────────────────────────────────────
  function estadoClass(e) {
    return e === 'validado' ? 'success' : e === 'rechazado' ? 'danger' : 'warning';
  }

  // ── Render tabla desde JSON ───────────────────────────────────────────────
  function renderTabla(items) {
    if (!items || items.length === 0) {
      tablaBody.innerHTML = `<tr><td colspan="8"><div class="empty-ev">
        <div class="empty-icon"><i class="ti tabler-file-off"></i></div>
        <div class="fw-semibold mb-1">No hay evidencias registradas</div>
        <div class="text-body-secondary" style="font-size:13px">Prueba cambiando los filtros.</div>
      </div></td></tr>`;
      contador.textContent = '0 registros';
      return;
    }

    contador.textContent = items.length + ' registros';

    tablaBody.innerHTML = items.map(ev => {
      const ec     = estadoClass(ev.estado);
      const esMio  = ev.es_propio;
      const pend   = ev.estado === 'pendiente';

      const enlaceBtn = ev.url_documento
        ? `<a href="${esc(ev.url_documento)}" target="_blank" class="btn btn-icon btn-label-secondary" title="Abrir">
            <i class="ti tabler-external-link icon-14px"></i></a>` : '';

      const editBtn = (pend && esMio)
        ? `<button class="btn btn-icon btn-label-primary btn-editar-ev"
              data-id="${ev.id}" data-titulo="${esc(ev.titulo)}"
              data-sgd="${esc(ev.numero_sgd??'')}" data-url="${esc(ev.url_documento??'')}"
              data-descripcion="${esc(ev.descripcion??'')}"
              data-action="${esc(ev.url_editar)}" title="Editar">
              <i class="ti tabler-edit icon-14px"></i></button>` : '';

      const validarBtns = pend
        ? `<button class="btn btn-icon btn-label-success btn-validar" data-url="${esc(ev.url_validar)}" title="Validar">
             <i class="ti tabler-check icon-14px"></i></button>
           <button class="btn btn-icon btn-label-danger btn-rechazar" data-url="${esc(ev.url_validar)}" title="Rechazar">
             <i class="ti tabler-x icon-14px"></i></button>` : '';

      const eliminarForm = `<form method="POST" action="${esc(ev.url_eliminar)}" class="form-eliminar-ev d-inline">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="btn btn-icon btn-label-secondary" title="Eliminar">
          <i class="ti tabler-trash icon-14px"></i></button></form>`;

      const motiRech = (ev.estado === 'rechazado' && ev.motivo_rechazo)
        ? `<div class="text-danger mt-1" style="font-size:10px" title="${esc(ev.motivo_rechazo)}">${esc(ev.motivo_rechazo)}</div>` : '';

      const validadoPorHtml = ev.validado_por && ev.estado !== 'pendiente'
        ? `<small class="text-muted" style="font-size:10px">${ev.estado === 'validado' ? '✓' : '✗'} ${esc(ev.validado_por)}</small>` : '';

      return `<tr>
        <td>
          <div class="fw-medium" style="font-size:13px">${esc(ev.titulo)}</div>
          ${ev.numero_sgd ? `<small class="text-muted"><i class="ti tabler-file-description icon-10px me-1"></i>${esc(ev.numero_sgd)}</small>` : ''}
          ${ev.descripcion ? `<div class="text-muted" style="font-size:11px;max-width:220px">${esc(ev.descripcion)}</div>` : ''}
        </td>
        <td>
          <div style="font-size:12px;max-width:180px">${esc(ev.actividad)}</div>
          ${ev.codigo ? `<small class="text-muted" style="font-size:10px">${esc(ev.codigo)}</small>` : ''}
        </td>
        <td><div style="font-size:12px;max-width:160px">${esc(ev.componente ?? '—')}</div></td>
        <td>
          <div style="font-size:12px">${esc(ev.subido_por)}</div>
          ${validadoPorHtml}
        </td>
        <td>
          ${ev.url_documento
            ? `<a href="${esc(ev.url_documento)}" target="_blank" class="btn btn-sm btn-label-info d-inline-flex align-items-center gap-1" style="font-size:11px;max-width:160px">
                <i class="ti tabler-external-link icon-12px flex-shrink-0"></i>
                <span class="url-chip">${esc(ev.url_host ?? ev.url_documento)}</span></a>`
            : `<span class="text-muted fst-italic" style="font-size:11px">Sin enlace</span>`}
        </td>
        <td>
          <span class="estado-pill badge bg-label-${ec}">${esc(ev.estado.charAt(0).toUpperCase() + ev.estado.slice(1))}</span>
          ${motiRech}
        </td>
        <td><small class="text-muted">${esc(ev.fecha)}</small></td>
        <td><div class="ev-actions">${enlaceBtn}${editBtn}${validarBtns}${eliminarForm}</div></td>
      </tr>`;
    }).join('');

    // Re-bind acciones en filas renderizadas por JS
    bindRowActions();
  }

  // ── Fetch datos ───────────────────────────────────────────────────────────
  function fetchDatos() {
    clearTimeout(debounce);
    debounce = setTimeout(async () => {
      wrapper.classList.add('loading');
      document.getElementById('paginacion-wrapper').innerHTML = '';

      const params = new URLSearchParams();
      params.set('modulo', moduloActivo);
      const ejeId   = document.getElementById('filtroEje')?.value;
      const etapaId = document.getElementById('filtroEtapa')?.value;
      const actId   = document.getElementById('filtroActividad')?.value;
      const estado  = document.getElementById('filtroEstado')?.value;
      const buscar  = document.getElementById('filtroBuscar')?.value;
      if (ejeId)   params.set('eje_id', ejeId);
      if (etapaId) params.set('etapa_id', etapaId);
      if (actId)   params.set('actividad_id', actId);
      if (estado)  params.set('estado', estado);
      if (buscar)  params.set('buscar', buscar);

      history.replaceState(null, '', RUTA + '?' + params.toString());

      try {
        const res  = await fetch(RUTA + '?' + params.toString(), {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        renderTabla(data.items);
        // Actualizar kpis del módulo activo
        const statsActivo = STATS_INIT[moduloActivo];
        renderKpis({ ...statsActivo, total: data.total });
      } catch (e) {
        console.error(e);
      } finally {
        wrapper.classList.remove('loading');
      }
    }, 260);
  }

  // ── Tabs módulo ───────────────────────────────────────────────────────────
  document.querySelectorAll('.mod-tab').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.mod-tab').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      moduloActivo = this.dataset.mod;

      // Toggle filtros eje/etapa
      document.getElementById('col-eje-sci').classList.toggle('d-none', moduloActivo === 'integridad');
      document.getElementById('col-etapa-int').classList.toggle('d-none', moduloActivo === 'sci');

      // Reset selectores de estructura
      document.getElementById('filtroEje').value   = '';
      document.getElementById('filtroEtapa').value = '';

      // Actualizar modal nueva: actividades + header color
      actualizarModalNueva();

      fetchDatos();
    });
  });

  // ── Filtros cambio ────────────────────────────────────────────────────────
  ['filtroEje','filtroEtapa','filtroEstado'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', fetchDatos);
  });

  document.getElementById('filtroActividad')?.addEventListener('change', fetchDatos);

  let buscarDebounce;
  document.getElementById('filtroBuscar')?.addEventListener('input', () => {
    clearTimeout(buscarDebounce);
    buscarDebounce = setTimeout(fetchDatos, 400);
  });

  document.getElementById('btnLimpiar')?.addEventListener('click', () => {
    document.getElementById('filtroEje').value       = '';
    document.getElementById('filtroEtapa').value     = '';
    document.getElementById('filtroEstado').value    = '';
    document.getElementById('filtroBuscar').value    = '';
    if (document.getElementById('filtroActividad')) {
      $(document.getElementById('filtroActividad')).val(null).trigger('change');
    }
    fetchDatos();
  });

  // ── Select2 Filtros ───────────────────────────────────────────────────────
  document.querySelectorAll('.select2-filtro').forEach(el => {
    const $w = $('<div class="position-relative"></div>');
    $(el).wrap($w);
    $(el).select2({ dropdownParent: $(el).parent(), width: '100%' });
    $(el).on('select2:select select2:unselect', fetchDatos);
  });

  // ── Modal Nueva: actualizar actividades por módulo ────────────────────────
  const actividadesPorModulo = {
    sci:        @json($actividades->where('modulo','sci')->values()),
    integridad: @json($actividades->where('modulo','integridad')->values()),
  };

  function actualizarModalNueva() {
    const sel = document.getElementById('nueva_actividad_id');
    document.getElementById('nueva_modulo_activo').value = moduloActivo;
    sel.innerHTML = '<option value="">— Seleccionar actividad —</option>';
    (actividadesPorModulo[moduloActivo] || []).forEach(a => {
      const opt = document.createElement('option');
      opt.value = a.id;
      opt.textContent = (a.codigo ?? '') + ' — ' + (a.nombre ?? '').substring(0, 60);
      sel.appendChild(opt);
    });
    $(sel).trigger('change');

    const header = document.getElementById('modalNuevaHeader');
    const label  = document.getElementById('nueva-modulo-label');
    if (moduloActivo === 'integridad') {
      header.className = 'modal-header modal-header-integridad';
      header.querySelector('.btn-close').classList.replace('btn-close-white', 'btn-close-white');
      label.textContent = 'Integridad';
    } else {
      header.className = 'modal-header modal-header-accent';
      label.textContent = 'SCI';
    }
  }

  // ── Select2 modal nueva ───────────────────────────────────────────────────
  const modalNuevo = document.getElementById('modalNuevaEvidencia');
  $(document.getElementById('nueva_actividad_id')).select2({ dropdownParent: $(modalNuevo), width:'100%' });

  document.getElementById('btnNuevaEvidencia').addEventListener('click', () => {
    actualizarModalNueva();
    new bootstrap.Modal(modalNuevo).show();
  });

  modalNuevo.addEventListener('shown.bs.modal', () => fixModalHeight(modalNuevo));

  @if(isset($actividadPresel) && $actividadPresel)
  actualizarModalNueva();
  const _preModal = new bootstrap.Modal(modalNuevo);
  _preModal.show();
  @endif

  // ── Modal Editar ──────────────────────────────────────────────────────────
  const modalEditar  = document.getElementById('modalEditarEvidencia');
  const formEditar   = document.getElementById('formEditarEvidencia');

  modalEditar.addEventListener('shown.bs.modal', () => fixModalHeight(modalEditar));

  function bindRowActions() {
    document.querySelectorAll('.btn-editar-ev').forEach(btn => {
      btn.addEventListener('click', function () {
        document.getElementById('edit_ev_titulo').value      = this.dataset.titulo      ?? '';
        document.getElementById('edit_ev_sgd').value         = this.dataset.sgd         ?? '';
        document.getElementById('edit_ev_url').value         = this.dataset.url         ?? '';
        document.getElementById('edit_ev_descripcion').value = this.dataset.descripcion ?? '';
        formEditar.action = this.dataset.action;
        new bootstrap.Modal(modalEditar).show();
      });
    });

    // Validar
    document.querySelectorAll('.btn-validar').forEach(btn => {
      btn.addEventListener('click', function () {
        Swal.fire({
          title:'¿Validar evidencia?', text:'Se marcará como aprobada.', icon:'question',
          showCancelButton:true,
          confirmButtonText:'<i class="ti tabler-check me-1"></i>Sí, validar',
          cancelButtonText:'Cancelar',
          customClass:{ popup:'rounded-3', confirmButton:'btn btn-success me-2', cancelButton:'btn btn-label-secondary' },
          buttonsStyling:false,
        }).then(r => {
          if (r.isConfirmed) {
            document.getElementById('formValidar').action = this.dataset.url;
            document.getElementById('formValidar').submit();
          }
        });
      });
    });

    // Rechazar
    document.querySelectorAll('.btn-rechazar').forEach(btn => {
      btn.addEventListener('click', function () {
        const url = this.dataset.url;
        Swal.fire({
          title:'Rechazar evidencia',
          input:'textarea', inputLabel:'Motivo del rechazo',
          inputPlaceholder:'Explica el motivo para que el usuario pueda corregirlo…',
          showCancelButton:true,
          confirmButtonText:'Rechazar',
          cancelButtonText:'Cancelar',
          customClass:{ popup:'rounded-3', confirmButton:'btn btn-danger me-2', cancelButton:'btn btn-label-secondary' },
          buttonsStyling:false,
          inputValidator: v => !v && 'El motivo es requerido para notificar al usuario',
        }).then(r => {
          if (r.isConfirmed) {
            document.getElementById('motivoInput').value      = r.value;
            document.getElementById('formRechazar').action    = url;
            document.getElementById('formRechazar').submit();
          }
        });
      });
    });

    // Eliminar
    document.querySelectorAll('.form-eliminar-ev').forEach(form => {
      form.addEventListener('submit', e => {
        e.preventDefault();
        Swal.fire({
          title:'¿Eliminar evidencia?', text:'Esta acción no se puede deshacer.', icon:'warning',
          showCancelButton:true,
          confirmButtonText:'<i class="ti tabler-trash me-1"></i>Sí, eliminar',
          cancelButtonText:'Cancelar',
          customClass:{ popup:'rounded-3', confirmButton:'btn btn-danger me-2', cancelButton:'btn btn-label-secondary' },
          buttonsStyling:false,
        }).then(r => { if (r.isConfirmed) form.submit(); });
      });
    });
  }

  // ── Fix altura modal ──────────────────────────────────────────────────────
  function fixModalHeight(modal) {
    const body   = modal.querySelector('.modal-body');
    const content = modal.querySelector('.modal-content');
    const header  = modal.querySelector('.modal-header');
    const footer  = modal.querySelector('.modal-footer');
    const maxH    = Math.floor(window.innerHeight * 0.88);
    const bodyMaxH = maxH - (header?.offsetHeight ?? 0) - (footer?.offsetHeight ?? 0);
    content.style.setProperty('max-height', maxH + 'px', 'important');
    body.style.setProperty('overflow-y', 'auto', 'important');
    body.style.setProperty('max-height', bodyMaxH + 'px', 'important');
  }

  // ── Inicializar acciones en filas del servidor ────────────────────────────
  bindRowActions();
  renderKpis(STATS_INIT[moduloActivo]);

  // ── Tab inicial: mostrar col correcta ─────────────────────────────────────
  if (moduloActivo === 'integridad') {
    document.getElementById('col-eje-sci').classList.add('d-none');
    document.getElementById('col-etapa-int').classList.remove('d-none');
  }

});
</script>
@endsection
