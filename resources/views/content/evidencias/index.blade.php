@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
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
/* ── KPI Cards ────────────────────────────────────────── */
.kpi-card { border-radius: 14px; border: none; overflow: hidden; transition: transform .18s, box-shadow .18s; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.13); }
.kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1; }
.kpi-label { font-size: .72rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; opacity: .75; }
.kpi-sub { font-size: .8rem; font-weight: 600; }

/* ── Tabla ────────────────────────────────────────────── */
.ev-table thead th {
  font-size: 11px; font-weight: 700; letter-spacing: .06em;
  text-transform: uppercase; color: var(--bs-secondary-color);
  background: rgba(var(--bs-secondary-rgb),.04);
  border-bottom: 2px solid rgba(var(--bs-secondary-rgb),.1);
  white-space: nowrap; padding: 12px 14px;
}
.ev-table tbody tr { transition: background .12s; }
.ev-table tbody tr:hover { background: rgba(var(--bs-primary-rgb),.03); }
.ev-table tbody td { padding: 10px 14px; vertical-align: middle; }

/* ── Estado pill ──────────────────────────────────────── */
.estado-pill { font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; }

/* ── URL chip ─────────────────────────────────────────── */
.url-chip { font-size: 11px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle; }

/* ── Acciones ─────────────────────────────────────────── */
.ev-actions { display: flex; gap: 4px; flex-wrap: nowrap; }
.ev-actions .btn { width: 30px; height: 30px; padding: 0; border-radius: 8px; }

/* ── Modal accent ─────────────────────────────────────── */
.modal-header-accent {
  background: linear-gradient(135deg, var(--bs-primary), color-mix(in srgb, var(--bs-primary) 70%, var(--bs-info)));
  color: #fff; border-radius: inherit;
}
.modal-header-accent .modal-title { color: #fff; }
.modal-dialog-scrollable { height: calc(100% - 3.5rem) !important; max-height: calc(100% - 3.5rem) !important; }
.modal-dialog-scrollable .modal-content { max-height: 100% !important; overflow: hidden !important; display: flex !important; flex-direction: column !important; }
.modal-dialog-scrollable .modal-body { overflow-y: auto !important; flex: 1 1 auto !important; min-height: 0 !important; }

/* ── Paginación ───────────────────────────────────────── */
.pagination { margin: 0; }
.page-link { border-radius: 8px !important; margin: 0 2px; font-size: 13px; }

/* ── Empty state ──────────────────────────────────────── */
.empty-ev { padding: 60px 20px; text-align: center; color: var(--bs-secondary-color); }
.empty-ev .empty-icon { font-size: 3.5rem; opacity: .3; margin-bottom: 16px; }
</style>
@endsection

@section('content')

{{-- Errores de validación --}}
@if($errors->any())
  <meta name="flash-errors" content="{{ addslashes($errors->first()) }}">
@endif

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
    <p class="mb-0 text-muted small">Documentos de respaldo por actividad SCI</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaEvidencia">
    <i class="ti tabler-plus me-1"></i>Nueva Evidencia
  </button>
</div>

{{-- KPI Cards --}}
@php
$kpis = [
  ['k'=>'total',      'label'=>'Total',      'sub'=>'Evidencias registradas', 'grad'=>'linear-gradient(135deg,#667eea 0%,#764ba2 100%)', 'icon'=>'tabler-files'],
  ['k'=>'validadas',  'label'=>'Validadas',  'sub'=>'Aprobadas',              'grad'=>'linear-gradient(135deg,#11998e 0%,#38ef7d 100%)', 'icon'=>'tabler-file-check'],
  ['k'=>'pendientes', 'label'=>'Pendientes', 'sub'=>'En revisión',            'grad'=>'linear-gradient(135deg,#f7971e 0%,#ffd200 100%)', 'icon'=>'tabler-file-time'],
  ['k'=>'rechazadas', 'label'=>'Rechazadas', 'sub'=>'Requieren corrección',   'grad'=>'linear-gradient(135deg,#cb2d3e 0%,#ef473a 100%)', 'icon'=>'tabler-file-x'],
];
@endphp
<div class="row g-4 mb-4">
  @foreach($kpis as $kp)
  <div class="col-6 col-md-3">
    <div class="card kpi-card h-100" style="background:{{ $kp['grad'] }}">
      <div class="card-body d-flex align-items-center gap-3 p-4">
        <div class="kpi-icon" style="background:rgba(255,255,255,.2)">
          <i class="ti {{ $kp['icon'] }}" style="color:#fff"></i>
        </div>
        <div style="color:#fff">
          <div class="kpi-value">{{ $stats[$kp['k']] }}</div>
          <div class="kpi-label">{{ $kp['label'] }}</div>
          <div class="kpi-sub" style="opacity:.8">{{ $kp['sub'] }}</div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Filtros --}}
<div class="card filter-card mb-4">
  <div class="card-body py-3 px-4">
    <form id="formFiltros" method="GET" action="{{ route('sci-evidencias') }}">
      <div class="row g-3 align-items-end">

        <div class="col-md-3">
          <label class="form-label fw-semibold mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.04em">Componente</label>
          <select name="componente_id" class="form-select select2-filtro">
            <option value="">Todos los componentes</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ request('componente_id') == $c->id ? 'selected' : '' }}>{{ $c->numero }}. {{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.04em">Actividad</label>
          <select name="actividad_id" class="form-select select2-filtro">
            <option value="">Todas las actividades</option>
            @foreach($actividades as $a)
            <option value="{{ $a->id }}" {{ request('actividad_id') == $a->id ? 'selected' : '' }}>
              {{ $a->codigo }} — {{ Str::limit($a->nombre, 45) }}
            </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label fw-semibold mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.04em">Estado</label>
          <select name="estado" id="filtroEstado" class="form-select">
            <option value="">Todos</option>
            <option value="validado"  {{ request('estado') === 'validado'  ? 'selected' : '' }}>Validado</option>
            <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="rechazado" {{ request('estado') === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.04em">Buscar</label>
          <div class="input-group">
            <span class="input-group-text"><i class="ti tabler-search icon-16px"></i></span>
            <input type="text" id="filtroBuscar" name="buscar" class="form-control"
              value="{{ request('buscar') }}" placeholder="N° SGD o título…">
            @if(request('buscar'))
            <a href="{{ route('sci-evidencias', request()->except('buscar')) }}" class="btn btn-label-secondary" title="Limpiar">
              <i class="ti tabler-x icon-16px"></i>
            </a>
            @endif
          </div>
        </div>

        <div class="col-md-1 d-flex align-items-end">
          @if(request()->hasAny(['componente_id','actividad_id','estado','buscar']))
          <a href="{{ route('sci-evidencias') }}" class="btn btn-label-secondary w-100" title="Limpiar filtros">
            <i class="ti tabler-filter-off"></i>
          </a>
          @endif
        </div>

      </div>
    </form>
  </div>
</div>

{{-- Tabla --}}
<div class="card" style="border-radius:12px">
  <div class="card-header d-flex align-items-center justify-content-between py-3 px-4" style="border-bottom:1px solid rgba(var(--bs-secondary-rgb),.1)">
    <span class="fw-semibold" style="font-size:15px">
      <i class="ti tabler-clipboard-list me-2 text-primary"></i>Evidencias Registradas
    </span>
    <span class="badge bg-label-primary rounded-pill">{{ $evidencias->total() }} registros</span>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle ev-table" style="min-width:860px">
        <thead>
          <tr>
            <th style="min-width:200px">Título / N° SGD</th>
            <th style="min-width:180px">Actividad</th>
            <th style="min-width:130px">Registrado por</th>
            <th style="min-width:160px">Enlace</th>
            <th style="width:100px">Estado</th>
            <th style="width:90px">Fecha</th>
            <th style="width:110px">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($evidencias as $ev)
          @php $ec = match($ev->estado) { 'validado' => 'success', 'rechazado' => 'danger', default => 'warning' }; @endphp
          <tr>
            {{-- Título --}}
            <td>
              <div class="fw-medium" style="font-size:13px">{{ $ev->titulo }}</div>
              @if($ev->numero_sgd)
              <small class="text-muted"><i class="ti tabler-file-description icon-10px me-1"></i>{{ $ev->numero_sgd }}</small>
              @endif
              @if($ev->descripcion)
              <div class="text-muted" style="font-size:11px;max-width:220px" title="{{ $ev->descripcion }}">{{ Str::limit($ev->descripcion, 50) }}</div>
              @endif
            </td>

            {{-- Actividad --}}
            <td>
              <div style="font-size:12px;max-width:200px" title="{{ $ev->actividad->nombre ?? '' }}">
                {{ Str::limit($ev->actividad->nombre ?? '—', 45) }}
              </div>
              @if($ev->actividad->componente ?? false)
              <small class="text-muted" style="font-size:10px">
                <i class="ti tabler-point icon-10px me-1"></i>{{ Str::limit($ev->actividad->componente->nombre, 30) }}
              </small>
              @endif
            </td>

            {{-- Registrado por --}}
            <td>
              <div style="font-size:12px">{{ $ev->subidoPor->name ?? '—' }}</div>
              @if($ev->validadoPor && $ev->estado !== 'pendiente')
              <small class="text-muted" style="font-size:10px">
                {{ $ev->estado === 'validado' ? '✓' : '✗' }} {{ $ev->validadoPor->name }}
              </small>
              @endif
            </td>

            {{-- Enlace --}}
            <td>
              @if($ev->url_documento)
              <a href="{{ $ev->url_documento }}" target="_blank" class="btn btn-sm btn-label-info d-inline-flex align-items-center gap-1" style="font-size:11px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                <i class="ti tabler-external-link icon-12px flex-shrink-0"></i>
                <span class="url-chip">{{ parse_url($ev->url_documento, PHP_URL_HOST) ?: $ev->url_documento }}</span>
              </a>
              @else
              <span class="text-muted fst-italic" style="font-size:11px">Sin enlace</span>
              @endif
            </td>

            {{-- Estado --}}
            <td>
              <span class="estado-pill badge bg-label-{{ $ec }}">{{ ucfirst($ev->estado) }}</span>
              @if($ev->estado === 'rechazado' && $ev->motivo_rechazo)
              <div class="text-danger mt-1" style="font-size:10px" title="{{ $ev->motivo_rechazo }}">{{ Str::limit($ev->motivo_rechazo, 30) }}</div>
              @endif
            </td>

            {{-- Fecha --}}
            <td><small class="text-muted">{{ $ev->created_at->format('d/m/Y') }}</small></td>

            {{-- Acciones --}}
            <td>
              <div class="ev-actions">
                @if($ev->url_documento)
                <a href="{{ $ev->url_documento }}" target="_blank" class="btn btn-icon btn-label-secondary" title="Abrir enlace">
                  <i class="ti tabler-external-link icon-14px"></i>
                </a>
                @endif
                @if($ev->estado === 'pendiente' && $ev->subido_por === auth()->id())
                <button class="btn btn-icon btn-label-primary btn-editar-ev"
                  data-id="{{ $ev->id }}"
                  data-titulo="{{ $ev->titulo }}"
                  data-sgd="{{ $ev->numero_sgd ?? '' }}"
                  data-url="{{ $ev->url_documento ?? '' }}"
                  data-descripcion="{{ $ev->descripcion ?? '' }}"
                  data-action="{{ route('sci-evidencias.update', $ev) }}"
                  title="Editar">
                  <i class="ti tabler-edit icon-14px"></i>
                </button>
                @endif
                @if($ev->estado === 'pendiente')
                <button class="btn btn-icon btn-label-success btn-validar"
                  data-url="{{ route('sci-evidencias.validar', $ev) }}" title="Validar">
                  <i class="ti tabler-check icon-14px"></i>
                </button>
                <button class="btn btn-icon btn-label-danger btn-rechazar"
                  data-url="{{ route('sci-evidencias.validar', $ev) }}" title="Rechazar">
                  <i class="ti tabler-x icon-14px"></i>
                </button>
                @endif
                <form method="POST" action="{{ route('sci-evidencias.destroy', $ev) }}" class="form-eliminar-ev d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-icon btn-label-secondary" title="Eliminar">
                    <i class="ti tabler-trash icon-14px"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7">
              <div class="empty-ev">
                <div class="empty-icon"><i class="ti tabler-file-off"></i></div>
                <div class="fw-semibold mb-1">No hay evidencias registradas</div>
                <div class="text-body-secondary" style="font-size:13px">
                  {{ request()->hasAny(['componente_id','estado','buscar']) ? 'Prueba cambiando los filtros.' : 'Aún no se han registrado evidencias.' }}
                </div>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($evidencias->hasPages())
  <div class="card-footer d-flex align-items-center justify-content-between py-3">
    <span class="text-muted" style="font-size:13px">
      Mostrando {{ $evidencias->firstItem() }}–{{ $evidencias->lastItem() }} de {{ $evidencias->total() }} registros
    </span>
    {{ $evidencias->links() }}
  </div>
  @endif
</div>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- Modal Nueva Evidencia                                                   --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalNuevaEvidencia" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="{{ route('sci-evidencias.store') }}">
        @csrf
        <div class="modal-header modal-header-accent">
          <h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nueva Evidencia</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">

            <div class="col-12">
              <label class="form-label fw-semibold">Actividad <span class="text-danger">*</span></label>
              <select name="actividad_id" class="form-select select2-modal" required>
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
              <label class="form-label fw-semibold">
                Enlace del documento
                <span class="badge bg-label-secondary ms-1" style="font-size:10px;font-weight:500">Opcional</span>
              </label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-link icon-16px"></i></span>
                <input type="url" name="url_documento" class="form-control" placeholder="https://drive.google.com/…">
              </div>
              <div class="form-text">Puede ser Google Drive, SharePoint, SGDOC u otro enlace.</div>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2" placeholder="Observaciones adicionales sobre la evidencia…"></textarea>
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
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                <input type="text" name="numero_sgd" id="edit_ev_sgd" class="form-control" placeholder="Ej: SGD-2026-001">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">
                Enlace del documento
                <span class="badge bg-label-secondary ms-1" style="font-size:10px;font-weight:500">Opcional</span>
              </label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti tabler-link icon-16px"></i></span>
                <input type="url" name="url_documento" id="edit_ev_url" class="form-control" placeholder="https://drive.google.com/…">
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

{{-- Forms ocultos para validar/rechazar --}}
<form method="POST" id="formValidar" style="display:none">@csrf @method('PATCH')<input type="hidden" name="accion" value="validado"></form>
<form method="POST" id="formRechazar" style="display:none">@csrf @method('PATCH')<input type="hidden" name="accion" value="rechazado"><input type="hidden" name="motivo_rechazo" id="motivoInput"></form>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Errores de validación ─────────────────────────────────────────────────
  const flashError = document.querySelector('meta[name="flash-errors"]')?.content;
  if (flashError) {
    Swal.fire({
      icon: 'error', title: 'Error de validación', text: flashError,
      customClass: { popup: 'rounded-3', confirmButton: 'btn btn-primary' },
      buttonsStyling: false,
    });
  }

  // ── Filtros en tiempo real ────────────────────────────────────────────────
  const routeBase   = '{{ route('sci-evidencias') }}';
  const formFiltros = document.getElementById('formFiltros');

  function submitFiltros() {
    const params = new URLSearchParams();
    new FormData(formFiltros).forEach((v, k) => { if (v && v !== '') params.set(k, v); });
    window.location.href = routeBase + (params.toString() ? '?' + params.toString() : '');
  }

  // Select2 con auto-submit al cambiar
  document.querySelectorAll('.select2-filtro').forEach(el => {
    $(el).wrap('<div class="position-relative"></div>').select2({
      dropdownParent: $(el).parent(), width: '100%',
    });
    $(el).on('change', () => submitFiltros());
  });

  // Estado: submit inmediato al cambiar
  document.getElementById('filtroEstado')?.addEventListener('change', () => submitFiltros());

  // Buscar: submit al presionar Enter
  document.getElementById('filtroBuscar')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); submitFiltros(); }
  });

  // ── Select2 modal ─────────────────────────────────────────────────────────
  const modalNuevo = document.getElementById('modalNuevaEvidencia');
  document.querySelectorAll('.select2-modal').forEach(el =>
    $(el).select2({ dropdownParent: modalNuevo, width: '100%' })
  );

  // Fix scroll modal
  modalNuevo.addEventListener('shown.bs.modal', () => {
    const body    = modalNuevo.querySelector('.modal-body');
    const content = modalNuevo.querySelector('.modal-content');
    const header  = modalNuevo.querySelector('.modal-header');
    const footer  = modalNuevo.querySelector('.modal-footer');
    const maxH    = Math.floor(window.innerHeight * 0.88);
    const bodyMaxH = maxH - (header?.offsetHeight ?? 0) - (footer?.offsetHeight ?? 0);
    content.style.setProperty('max-height', maxH + 'px', 'important');
    content.style.setProperty('overflow', 'hidden', 'important');
    body.style.setProperty('overflow-y', 'auto', 'important');
    body.style.setProperty('max-height', bodyMaxH + 'px', 'important');
  });

  // Abrir modal automáticamente si hay actividad preseleccionada
  @if(isset($actividadPresel) && $actividadPresel)
  new bootstrap.Modal(modalNuevo).show();
  @endif

  // ── Validar evidencia ─────────────────────────────────────────────────────
  document.querySelectorAll('.btn-validar').forEach(btn => {
    btn.addEventListener('click', function () {
      Swal.fire({
        title: '¿Validar evidencia?',
        text: 'Se marcará como aprobada.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="ti tabler-check me-1"></i>Sí, validar',
        cancelButtonText: 'Cancelar',
        customClass: { popup: 'rounded-3', confirmButton: 'btn btn-success me-2', cancelButton: 'btn btn-label-secondary' },
        buttonsStyling: false,
      }).then(r => {
        if (r.isConfirmed) {
          document.getElementById('formValidar').action = this.dataset.url;
          document.getElementById('formValidar').submit();
        }
      });
    });
  });

  // ── Rechazar evidencia ────────────────────────────────────────────────────
  document.querySelectorAll('.btn-rechazar').forEach(btn => {
    btn.addEventListener('click', function () {
      const url = this.dataset.url;
      Swal.fire({
        title: 'Rechazar evidencia',
        input: 'textarea',
        inputLabel: 'Motivo del rechazo',
        inputPlaceholder: 'Explica el motivo…',
        showCancelButton: true,
        confirmButtonText: 'Rechazar',
        cancelButtonText: 'Cancelar',
        customClass: { popup: 'rounded-3', confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-label-secondary' },
        buttonsStyling: false,
        inputValidator: v => !v && 'El motivo es requerido',
      }).then(r => {
        if (r.isConfirmed) {
          document.getElementById('motivoInput').value = r.value;
          document.getElementById('formRechazar').action = url;
          document.getElementById('formRechazar').submit();
        }
      });
    });
  });

  // ── Editar evidencia ──────────────────────────────────────────────────────
  const modalEditar   = document.getElementById('modalEditarEvidencia');
  const formEditar    = document.getElementById('formEditarEvidencia');

  modalEditar.addEventListener('shown.bs.modal', () => {
    const body    = modalEditar.querySelector('.modal-body');
    const content = modalEditar.querySelector('.modal-content');
    const header  = modalEditar.querySelector('.modal-header');
    const footer  = modalEditar.querySelector('.modal-footer');
    const maxH    = Math.floor(window.innerHeight * 0.88);
    const bodyMaxH = maxH - (header?.offsetHeight ?? 0) - (footer?.offsetHeight ?? 0);
    content.style.setProperty('max-height', maxH + 'px', 'important');
    content.style.setProperty('overflow', 'hidden', 'important');
    body.style.setProperty('overflow-y', 'auto', 'important');
    body.style.setProperty('max-height', bodyMaxH + 'px', 'important');
  });

  document.querySelectorAll('.btn-editar-ev').forEach(btn => {
    btn.addEventListener('click', function () {
      document.getElementById('edit_ev_titulo').value       = this.dataset.titulo      ?? '';
      document.getElementById('edit_ev_sgd').value          = this.dataset.sgd         ?? '';
      document.getElementById('edit_ev_url').value          = this.dataset.url         ?? '';
      document.getElementById('edit_ev_descripcion').value  = this.dataset.descripcion ?? '';
      formEditar.action = this.dataset.action;
      new bootstrap.Modal(modalEditar).show();
    });
  });

  // ── Eliminar evidencia ────────────────────────────────────────────────────
  document.querySelectorAll('.form-eliminar-ev').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      Swal.fire({
        title: '¿Eliminar evidencia?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="ti tabler-trash me-1"></i>Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: { popup: 'rounded-3', confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-label-secondary' },
        buttonsStyling: false,
      }).then(r => { if (r.isConfirmed) form.submit(); });
    });
  });

});
</script>
@endsection
