@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Recomendaciones — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss',
       'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
      <li class="breadcrumb-item active">Recomendaciones</li>
    </ol>
  </nav>

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-1">Recomendaciones y Observaciones</h4>
      <p class="text-muted mb-0">Seguimiento en tiempo real de hallazgos del SCI y Modelo de Integridad</p>
    </div>
    @can('recomendaciones.crear')
    <button class="btn btn-primary" id="btnNueva">
      <i class="ti tabler-plus me-1"></i> Nueva Recomendación
    </button>
    @endcan
  </div>

  {{-- Tabs --}}
  <ul class="nav nav-tabs nav-fill mb-4" id="tabModulo">
    <li class="nav-item">
      <a class="nav-link active" href="#" data-modulo="sci">
        <i class="ti tabler-shield-check me-1"></i>
        Sistema de Control Interno
        <span class="badge bg-warning ms-1 rounded-pill tab-badge-sci d-none"></span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#" data-modulo="integridad">
        <i class="ti tabler-scale me-1"></i>
        Modelo de Integridad
        <span class="badge bg-warning ms-1 rounded-pill tab-badge-integridad d-none"></span>
      </a>
    </li>
  </ul>

  {{-- KPIs --}}
  <div class="row g-3 mb-4" id="kpiRow">
    @foreach([
      ['id'=>'kpi-total',      'color'=>'primary',   'icon'=>'tabler-list-details',      'label'=>'Total'],
      ['id'=>'kpi-pendientes', 'color'=>'warning',   'icon'=>'tabler-clock',             'label'=>'Pendientes'],
      ['id'=>'kpi-atendidas',  'color'=>'success',   'icon'=>'tabler-circle-check',      'label'=>'Atendidas'],
      ['id'=>'kpi-vencidas',   'color'=>'danger',    'icon'=>'tabler-alert-triangle',    'label'=>'Vencidas'],
      ['id'=>'kpi-por-vencer', 'color'=>'info',      'icon'=>'tabler-clock-exclamation', 'label'=>'Por vencer (7d)'],
      ['id'=>'kpi-rechazadas', 'color'=>'secondary', 'icon'=>'tabler-ban',              'label'=>'Rechazadas'],
    ] as $k)
    <div class="col-6 col-xl-2">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body text-center py-3">
          <div class="avatar avatar-md mx-auto mb-2">
            <span class="avatar-initial rounded bg-label-{{ $k['color'] }}">
              <i class="ti {{ $k['icon'] }}"></i>
            </span>
          </div>
          <h4 class="mb-0 fw-bold kpi-val" id="{{ $k['id'] }}">—</h4>
          <small class="text-muted">{{ $k['label'] }}</small>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Semáforo --}}
  <div id="semaforo" class="row g-2 mb-4"></div>

  {{-- Filtros --}}
  <div class="card mb-4">
    <div class="card-body py-3">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label small fw-semibold mb-1">Buscar</label>
          <input type="text" id="f-buscar" class="form-control form-control-sm" placeholder="Título, descripción, SGD...">
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Tipo</label>
          <select id="f-tipo" class="form-select form-select-sm">
            <option value="">Todos</option>
            @foreach($tipos as $k => $v)
              <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Estado</label>
          <select id="f-estado" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="pendiente">Pendiente</option>
            <option value="en_proceso">En Proceso</option>
            <option value="atendida">Atendida</option>
            <option value="rechazada">Rechazada</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Prioridad</label>
          <select id="f-prioridad" class="form-select form-select-sm">
            <option value="">Todas</option>
            <option value="alta">Alta</option>
            <option value="media">Media</option>
            <option value="baja">Baja</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Origen</label>
          <select id="f-origen" class="form-select form-select-sm">
            <option value="">Todos</option>
            @foreach($origenes as $o)
              <option value="{{ $o }}">{{ $o }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-1 d-flex gap-1">
          <button id="btnFiltrar" class="btn btn-primary btn-sm flex-grow-1" title="Filtrar">
            <i class="ti tabler-filter"></i>
          </button>
          <button id="btnLimpiar" class="btn btn-outline-secondary btn-sm" title="Limpiar">
            <i class="ti tabler-x"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
      <h5 class="card-title mb-0" id="tablaTitle">
        <i class="ti tabler-shield-check me-2 text-primary"></i>Recomendaciones SCI
      </h5>
      <small class="text-muted" id="totalLabel">—</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="min-width:200px">Título / SGD</th>
              <th>Tipo</th>
              <th>Unidad</th>
              <th>Responsable</th>
              <th>Prioridad</th>
              <th>Estado</th>
              <th>Origen</th>
              <th style="min-width:100px">Fecha Límite</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody id="tablaBody">
            <tr><td colspan="9" class="text-center py-4">
              <div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando...
            </td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer" id="paginacionWrap"></div>
  </div>

</div>

{{-- ══ MODAL VER ══ --}}
<div class="modal fade" id="modalVer" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti tabler-eye me-2 text-info"></i>Detalle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalVerBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

{{-- ══ MODAL NUEVA / EDITAR ══ --}}
@canany(['recomendaciones.crear','recomendaciones.editar'])
<div class="modal fade" id="modalForm" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalFormTitle"><i class="ti tabler-plus me-2 text-primary"></i>Nueva Recomendación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="formAlerts"></div>
        <div class="row g-3">
          <div class="col-12">
            <div id="moduloBadge" class="alert py-2 mb-0 d-flex align-items-center"></div>
          </div>
          <div class="col-12" id="moduloFieldWrap" style="display:none">
            <label class="form-label fw-semibold">Módulo <span class="text-danger">*</span></label>
            <select id="f_modulo" class="form-select">
              <option value="sci">Sistema de Control Interno (SCI)</option>
              <option value="integridad">Modelo de Integridad</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
            <input type="text" id="f_titulo" class="form-control" placeholder="Descripción breve...">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
            <select id="f_tipo" class="form-select">
              @foreach($tipos as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Prioridad <span class="text-danger">*</span></label>
            <select id="f_prioridad" class="form-select">
              <option value="alta">Alta</option>
              <option value="media" selected>Media</option>
              <option value="baja">Baja</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
            @can('recomendaciones.editar')
            <select id="f_estado" class="form-select">
              <option value="pendiente" selected>Pendiente</option>
              <option value="en_proceso">En Proceso</option>
              <option value="atendida">Atendida</option>
              <option value="rechazada">Rechazada</option>
            </select>
            @else
            <select id="f_estado" class="form-select" disabled>
              <option value="pendiente" selected>Pendiente</option>
            </select>
            <input type="hidden" id="f_estado_hidden" name="estado" value="pendiente">
            @endcan
          </div>
          <div class="col-md-6" id="wrap-unidad">
            <label class="form-label fw-semibold">Unidad Orgánica</label>
            <select id="f_unidad" class="form-select select2-form">
              <option value="">Sin asignar</option>
              @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6" id="wrap-responsable">
            <label class="form-label fw-semibold">Responsable</label>
            <select id="f_responsable" class="form-select select2-form">
              <option value="">Sin asignar</option>
              @foreach($usuarios as $usr)
                <option value="{{ $usr->id }}">{{ $usr->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Actividad SCI vinculada</label>
            <select id="f_actividad" class="form-select select2-form">
              <option value="">Ninguna</option>
              @foreach($actividades as $act)
                <option value="{{ $act->id }}">{{ \Illuminate\Support\Str::limit($act->nombre, 60) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Origen</label>
            <select id="f_origen" class="form-select">
              <option value="">Seleccionar...</option>
              @foreach($origenes as $o)
                <option value="{{ $o }}">{{ $o }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Fecha Emisión</label>
            <input type="date" id="f_emision" class="form-control" value="{{ date('Y-m-d') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Fecha Límite</label>
            <input type="date" id="f_limite" class="form-control">
          </div>
          <div class="col-md-4" id="atencionWrap" style="display:none">
            <label class="form-label fw-semibold">Fecha Atención</label>
            <input type="date" id="f_atencion" class="form-control">
          </div>
          <div class="col-md-4" id="sgdRow">
            <label class="form-label fw-semibold">N° SGD</label>
            <input type="text" id="f_sgd" class="form-control" placeholder="Ej: 001-2026">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción detallada</label>
            <textarea id="f_descripcion" class="form-control" rows="3" placeholder="Descripción completa..."></textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Observaciones / Acciones tomadas</label>
            <textarea id="f_observaciones" class="form-control" rows="2" placeholder="Acciones implementadas..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="btnGuardar" class="btn btn-primary">
          <i class="ti tabler-device-floppy me-1"></i><span id="btnGuardarTxt">Guardar</span>
        </button>
      </div>
    </div>
  </div>
</div>
@endcanany
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js',
       'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
<script>
(function () {
'use strict';

// ── Config ────────────────────────────────────────────────────────────────────
const CSRF   = '{{ csrf_token() }}';
const URL_DATA  = '{{ route("recomendaciones.data") }}';
const URL_STORE = '{{ route("recomendaciones.store") }}';
const CAN_CREAR   = {{ auth()->user()->can('recomendaciones.crear')   ? 'true' : 'false' }};
const CAN_EDITAR  = {{ auth()->user()->can('recomendaciones.editar')  ? 'true' : 'false' }};
const CAN_ELIMINAR= {{ auth()->user()->can('recomendaciones.eliminar')? 'true' : 'false' }};

let modulo      = 'sci';
let currentPage = 1;
let editingId   = null;
let debounceTimer;

// ── Helpers ───────────────────────────────────────────────────────────────────
const qs  = id  => document.getElementById(id);
const qss = sel => document.querySelector(sel);
const $$  = sel => document.querySelectorAll(sel);
const fv  = id  => qs(id)?.value ?? '';

function badgeHtml(label, color) {
  return `<span class="badge bg-label-${color} rounded">${label}</span>`;
}

function formatDate(y_m_d) {
  if (!y_m_d) return '—';
  const [y,m,d] = y_m_d.split('-');
  return `${d}/${m}/${y}`;
}

function showToast(msg, type) {
  pulsoToast(msg, type || 'success');
}

function showErrors(errors) {
  const al = qs('formAlerts');
  const msgs = Object.values(errors).flat().map(e => `<li>${e}</li>`).join('');
  al.innerHTML = `<div class="alert alert-danger py-2"><ul class="mb-0 ps-3">${msgs}</ul></div>`;
}

function clearErrors() { qs('formAlerts').innerHTML = ''; }

// ── Select2 init ──────────────────────────────────────────────────────────────
function initSelect2() {
  $('.select2-form').each(function() {
    if ($(this).hasClass('select2-hidden-accessible')) return;
    $(this).select2({ dropdownParent: $('#modalForm'), width: '100%', placeholder: 'Seleccionar...' });
  });
}

// ── Cargar datos ──────────────────────────────────────────────────────────────
function loadData(page = 1) {
  currentPage = page;
  const params = new URLSearchParams({
    modulo,
    page,
    buscar:    fv('f-buscar'),
    tipo:      fv('f-tipo'),
    estado:    fv('f-estado'),
    prioridad: fv('f-prioridad'),
    origen:    fv('f-origen'),
  });

  qs('tablaBody').innerHTML = `<tr><td colspan="9" class="text-center py-4">
    <div class="spinner-border spinner-border-sm text-primary me-2"></div>Actualizando...
  </td></tr>`;

  fetch(`${URL_DATA}?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(json => {
      renderStats(json.stats, json.tabStats);
      renderTabla(json.data);
      renderPaginacion(json.pagination);
    })
    .catch(() => {
      qs('tablaBody').innerHTML = `<tr><td colspan="9" class="text-center text-danger py-4">
        Error al cargar datos. <button class="btn btn-sm btn-link" onclick="loadData()">Reintentar</button>
      </td></tr>`;
    });
}

// ── Render KPIs y semáforo ────────────────────────────────────────────────────
function renderStats(stats, tabStats) {
  qs('kpi-total').textContent      = stats.total;
  qs('kpi-pendientes').textContent = stats.pendientes;
  qs('kpi-atendidas').textContent  = stats.atendidas;
  qs('kpi-vencidas').textContent   = stats.vencidas;
  qs('kpi-por-vencer').textContent = stats.por_vencer;
  qs('kpi-rechazadas').textContent = stats.rechazadas;

  // Tab badges
  ['sci', 'integridad'].forEach(m => {
    const badge = document.querySelector(`.tab-badge-${m}`);
    if (!badge) return;
    if (tabStats[m] > 0) {
      badge.textContent = tabStats[m];
      badge.classList.remove('d-none');
    } else {
      badge.classList.add('d-none');
    }
  });

  // Semáforo
  let sem = '';
  if (stats.vencidas > 0)
    sem += `<div class="col-md-4"><div class="alert alert-danger mb-0 d-flex align-items-center py-2">
      <span class="me-2 fs-4">🔴</span><div><strong>${stats.vencidas} vencida${stats.vencidas!=1?'s':''}</strong> sin atender — acción inmediata</div></div></div>`;
  if (stats.por_vencer > 0)
    sem += `<div class="col-md-4"><div class="alert alert-warning mb-0 d-flex align-items-center py-2">
      <span class="me-2 fs-4">🟡</span><div><strong>${stats.por_vencer}</strong> vence${stats.por_vencer==1?'':'n'} en menos de 7 días</div></div></div>`;
  if (stats.alta_prior > 0)
    sem += `<div class="col-md-4"><div class="alert alert-danger mb-0 d-flex align-items-center py-2" style="background:rgba(234,84,85,.1);border-color:#ea5455">
      <span class="me-2 fs-4">🔺</span><div><strong>${stats.alta_prior}</strong> de prioridad alta pendiente${stats.alta_prior!=1?'s':''}</div></div></div>`;
  qs('semaforo').innerHTML = sem;
}

// ── Render tabla ──────────────────────────────────────────────────────────────
function renderTabla(rows) {
  const iconModulo = modulo === 'sci' ? 'tabler-shield-check' : 'tabler-scale';
  const labelModulo = modulo === 'sci' ? 'Recomendaciones SCI' : 'Recomendaciones — Modelo de Integridad';
  qs('tablaTitle').innerHTML = `<i class="ti ${iconModulo} me-2 text-primary"></i>${labelModulo}`;

  if (!rows.length) {
    qs('tablaBody').innerHTML = `<tr><td colspan="9" class="text-center py-5">
      <div class="text-muted">
        <i class="ti tabler-message-off icon-48px d-block mx-auto mb-3 opacity-25"></i>
        <p class="mb-1">No se encontraron recomendaciones.</p>
        <button class="btn btn-sm btn-primary mt-2" id="btnNuevaEmpty">
          <i class="ti tabler-plus me-1"></i> Registrar primera
        </button>
      </div></td></tr>`;
    qs('btnNuevaEmpty')?.addEventListener('click', abrirNueva);
    return;
  }

  const html = rows.map(r => {
    const vencida  = r.esta_vencida;
    const rowClass = vencida ? 'table-danger' : '';

    let fechaLimHtml = '—';
    if (r.fecha_limite_fmt) {
      fechaLimHtml = `<small class="${vencida ? 'text-danger fw-bold' : 'text-muted'}">${r.fecha_limite_fmt}`;
      if (vencida)
        fechaLimHtml += `<br><span class="badge bg-label-danger rounded" style="font-size:9px">VENCIDA</span>`;
      else if (r.dias_restantes !== null && r.dias_restantes >= 0 && r.dias_restantes <= 7)
        fechaLimHtml += `<br><span class="badge bg-label-warning rounded" style="font-size:9px">${r.dias_restantes}d</span>`;
      fechaLimHtml += '</small>';
    }

    const actividad = r.actividad_nombre
      ? `<br><small class="text-info"><i class="ti tabler-link me-1"></i>${r.actividad_nombre.substring(0,35)}</small>`
      : '';

    const btnAtender = (CAN_EDITAR && !['atendida','rechazada'].includes(r.estado))
      ? `<button class="btn btn-sm btn-icon btn-label-success btn-atender" title="Marcar atendida"
           data-id="${r.id}" data-titulo="${escHtml(r.titulo)}"><i class="ti tabler-circle-check"></i></button>`
      : '';

    const btnEditar   = CAN_EDITAR
      ? `<button class="btn btn-sm btn-icon btn-label-primary btn-editar" title="Editar"
           data-rec='${JSON.stringify(r).replace(/'/g,"&#39;")}'>
           <i class="ti tabler-edit"></i></button>` : '';

    const btnEliminar = CAN_ELIMINAR
      ? `<button class="btn btn-sm btn-icon btn-label-danger btn-eliminar" title="Eliminar"
           data-id="${r.id}" data-titulo="${escHtml(r.titulo)}">
           <i class="ti tabler-trash"></i></button>` : '';

    return `<tr class="${rowClass}" data-id="${r.id}">
      <td>
        <div class="fw-semibold lh-sm">${escHtml(r.titulo)}</div>
        ${r.numero_sgd ? `<small class="text-muted">SGD: ${r.numero_sgd}</small>` : ''}
        ${actividad}
      </td>
      <td>${badgeHtml(r.tipo_label, r.tipo_color)}</td>
      <td><small>${r.unidad_sigla || '—'}</small></td>
      <td><small>${r.responsable_nombre || '—'}</small></td>
      <td>${badgeHtml(ucfirst(r.prioridad), r.prioridad_color)}</td>
      <td>${badgeHtml(r.estado_label, r.estado_color)}</td>
      <td><small class="text-muted">${r.origen || '—'}</small></td>
      <td>${fechaLimHtml}</td>
      <td class="text-center">
        <div class="d-flex gap-1 justify-content-center">
          <button class="btn btn-sm btn-icon btn-label-info btn-ver" title="Ver detalle"
            data-rec='${JSON.stringify(r).replace(/'/g,"&#39;")}'>
            <i class="ti tabler-eye"></i></button>
          ${btnAtender}
          ${btnEditar}
          ${btnEliminar}
        </div>
      </td>
    </tr>`;
  }).join('');

  qs('tablaBody').innerHTML = html;
}

// ── Paginación ────────────────────────────────────────────────────────────────
function renderPaginacion(p) {
  qs('totalLabel').textContent = p.total > 0 ? `${p.total} registros` : '';
  qs('paginacionWrap').innerHTML = '';
  if (p.last_page <= 1) return;

  let btns = '';
  for (let i = 1; i <= p.last_page; i++) {
    btns += `<li class="page-item ${i===p.current_page?'active':''}">
      <button class="page-link" data-page="${i}">${i}</button></li>`;
  }
  qs('paginacionWrap').innerHTML = `<nav><ul class="pagination pagination-sm mb-0 justify-content-end">${btns}</ul></nav>`;
}

// ── Modal Ver ─────────────────────────────────────────────────────────────────
function abrirVer(r) {
  qs('modalVerBody').innerHTML = `
    <h6 class="fw-bold mb-3">${escHtml(r.titulo)}</h6>
    <div class="row g-3">
      <div class="col-6"><p class="text-muted small mb-1">Tipo</p><p class="mb-0">${r.tipo_label}</p></div>
      <div class="col-6"><p class="text-muted small mb-1">Estado</p><p class="mb-0">${r.estado_label}</p></div>
      <div class="col-6"><p class="text-muted small mb-1">Prioridad</p><p class="mb-0">${ucfirst(r.prioridad)}</p></div>
      <div class="col-6"><p class="text-muted small mb-1">Origen</p><p class="mb-0">${r.origen||'—'}</p></div>
      <div class="col-12"><p class="text-muted small mb-1">Unidad Orgánica</p><p class="mb-0">${r.unidad_nombre||'—'}</p></div>
      <div class="col-12"><p class="text-muted small mb-1">Responsable</p><p class="mb-0">${r.responsable_nombre||'—'}</p></div>
      <div class="col-4"><p class="text-muted small mb-1">N° SGD</p><p class="mb-0">${r.numero_sgd||'—'}</p></div>
      <div class="col-4"><p class="text-muted small mb-1">Emisión</p><p class="mb-0">${formatDate(r.fecha_emision)}</p></div>
      <div class="col-4"><p class="text-muted small mb-1">Límite</p><p class="mb-0">${formatDate(r.fecha_limite)}</p></div>
      <div class="col-12"><p class="text-muted small mb-1">Atención</p><p class="mb-0">${formatDate(r.fecha_atencion)}</p></div>
      ${r.descripcion ? `<div class="col-12"><p class="text-muted small mb-1">Descripción</p><p class="mb-0 text-wrap">${escHtml(r.descripcion)}</p></div>` : ''}
      ${r.observaciones ? `<div class="col-12"><p class="text-muted small mb-1">Observaciones</p><p class="mb-0 fst-italic text-wrap">${escHtml(r.observaciones)}</p></div>` : ''}
    </div>`;
  new bootstrap.Modal(qs('modalVer')).show();
}

// ── Modal Formulario ──────────────────────────────────────────────────────────
function resetForm() {
  clearErrors();
  ['f_titulo','f_sgd','f_emision','f_limite','f_atencion','f_descripcion','f_observaciones'].forEach(id => {
    const el = qs(id);
    if (el) el.value = id === 'f_emision' ? new Date().toISOString().slice(0,10) : '';
  });
  ['f_tipo','f_prioridad','f_estado','f_origen'].forEach(id => {
    const el = qs(id);
    if (el) el.selectedIndex = 0;
  });
  // Reset select2
  $('#f_unidad').val('').trigger('change');
  $('#f_responsable').val('').trigger('change');
  $('#f_actividad').val('').trigger('change');
  qs('f_prioridad').value = 'media';
  qs('f_estado').value    = 'pendiente';
  qs('atencionWrap').style.display = 'none';
}

function abrirNueva() {
  editingId = null;
  resetForm();
  // Ocultar unidad/responsable si el usuario no puede editarlos (sin ver-todas/ver-unidad)
  if (!CAN_EDITAR) {
    qs('wrap-unidad')   && (qs('wrap-unidad').style.display   = 'none');
    qs('wrap-responsable') && (qs('wrap-responsable').style.display = 'none');
  }
  qs('moduloFieldWrap').style.display = 'none';
  qs('f_modulo').value = modulo;
  const label = modulo === 'sci' ? 'Sistema de Control Interno' : 'Modelo de Integridad';
  const icon  = modulo === 'sci' ? 'tabler-shield-check' : 'tabler-scale';
  const color = modulo === 'sci' ? 'primary' : 'info';
  qs('moduloBadge').className = `alert alert-${color} py-2 mb-0 d-flex align-items-center`;
  qs('moduloBadge').innerHTML = `<i class="ti ${icon} me-2"></i>Registrando en: <strong class="ms-1">${label}</strong>`;
  qs('modalFormTitle').innerHTML = '<i class="ti tabler-plus me-2 text-primary"></i>Nueva Recomendación';
  qs('btnGuardarTxt').textContent = 'Guardar';
  new bootstrap.Modal(qs('modalForm')).show();
}

function abrirEditar(r) {
  editingId = r.id;
  resetForm();
  qs('moduloFieldWrap').style.display = CAN_EDITAR ? 'block' : 'none';
  qs('f_modulo').value      = r.modulo;
  qs('f_titulo').value      = r.titulo;
  qs('f_tipo').value        = r.tipo;
  qs('f_prioridad').value   = r.prioridad;
  // Sincronizar estado en select o hidden según permiso
  if (qs('f_estado') && !qs('f_estado').disabled) {
    qs('f_estado').value = r.estado;
  } else if (qs('f_estado_hidden')) {
    qs('f_estado_hidden').value = r.estado;
  }
  qs('f_origen').value      = r.origen || '';
  qs('f_emision').value     = r.fecha_emision || '';
  qs('f_limite').value      = r.fecha_limite || '';
  qs('f_sgd').value         = r.numero_sgd || '';
  qs('f_descripcion').value = r.descripcion || '';
  qs('f_observaciones').value = r.observaciones || '';
  qs('atencionWrap').style.display = 'block';
  qs('f_atencion').value    = r.fecha_atencion || '';
  $('#f_unidad').val(r.unidad_id||'').trigger('change');
  $('#f_responsable').val(r.responsable_id||'').trigger('change');
  $('#f_actividad').val(r.actividad_id||'').trigger('change');
  qs('moduloBadge').className = 'alert alert-secondary py-2 mb-0 d-flex align-items-center';
  qs('moduloBadge').innerHTML = `<i class="ti tabler-edit me-2"></i>Editando recomendación ID <strong class="ms-1">#${r.id}</strong>`;
  qs('modalFormTitle').innerHTML = '<i class="ti tabler-edit me-2 text-primary"></i>Editar Recomendación';
  qs('btnGuardarTxt').textContent = 'Actualizar';
  new bootstrap.Modal(qs('modalForm')).show();
}

// ── Guardar (store / update) ──────────────────────────────────────────────────
function guardar() {
  clearErrors();
  const btn = qs('btnGuardar');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

  // Si el select de estado está disabled, tomar el hidden
  const estadoVal = qs('f_estado')?.disabled
    ? (qs('f_estado_hidden')?.value || 'pendiente')
    : fv('f_estado');

  const body = new URLSearchParams({
    _token:             CSRF,
    titulo:             fv('f_titulo'),
    tipo:               fv('f_tipo'),
    modulo:             fv('f_modulo'),
    prioridad:          fv('f_prioridad'),
    estado:             estadoVal,
    origen:             fv('f_origen'),
    fecha_emision:      fv('f_emision'),
    fecha_limite:       fv('f_limite'),
    fecha_atencion:     fv('f_atencion'),
    numero_sgd:         fv('f_sgd'),
    descripcion:        fv('f_descripcion'),
    observaciones:      fv('f_observaciones'),
    unidad_organica_id: fv('f_unidad'),
    responsable_id:     fv('f_responsable'),
    actividad_id:       fv('f_actividad'),
  });

  const url    = editingId ? `/recomendaciones/${editingId}` : URL_STORE;
  const method = editingId ? 'PUT' : 'POST';

  if (editingId) body.append('_method', 'PUT');

  fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }, body })
    .then(async r => {
      const json = await r.json();
      if (!r.ok) { showErrors(json.errors || { error: ['Error inesperado'] }); return; }
      bootstrap.Modal.getInstance(qs('modalForm'))?.hide();
      showToast(editingId ? 'Recomendación actualizada.' : 'Recomendación registrada.');
      loadData(currentPage);
    })
    .catch(() => showErrors({ error: ['Error de conexión. Intente nuevamente.'] }))
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti tabler-device-floppy me-1"></i><span id="btnGuardarTxt">' + (editingId ? 'Actualizar' : 'Guardar') + '</span>';
    });
}

// ── Marcar atendida ───────────────────────────────────────────────────────────
function marcarAtendida(id, titulo) {
  pulsoConfirm({
    title: 'Marcar como atendida',
    html: `¿Confirmar que "<strong>${titulo}</strong>" ha sido atendida?`,
    type: 'question', confirmText: 'Sí, atendida', cancelText: 'Cancelar',
  }).then(ok => {
    if (!ok) return;
    fetch(`/recomendaciones/${id}/atender`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: '_method=PATCH&_token=' + CSRF,
    }).then(r => r.json()).then(j => {
      if (j.success) { showToast('Marcada como atendida.'); loadData(currentPage); }
    });
  });
}

// ── Eliminar ──────────────────────────────────────────────────────────────────
function eliminar(id, titulo) {
  pulsoConfirm({
    title: '¿Eliminar recomendación?',
    html: `<strong>${titulo}</strong><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
    type: 'warning', confirmText: 'Sí, eliminar', cancelText: 'Cancelar',
  }).then(ok => {
    if (!ok) return;
    fetch(`/recomendaciones/${id}`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: '_method=DELETE&_token=' + CSRF,
    }).then(r => r.json()).then(j => {
      if (j.success) { showToast('Recomendación eliminada.', 'warning'); loadData(currentPage); }
    });
  });
}

// ── Utils ─────────────────────────────────────────────────────────────────────
function escHtml(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function ucfirst(str) { return str ? str.charAt(0).toUpperCase()+str.slice(1) : ''; }

// ── Event listeners ───────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

  // Inicializar Select2
  $('#modalForm').on('shown.bs.modal', function() { initSelect2(); });

  // Tabs
  $$('#tabModulo .nav-link').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      $$('#tabModulo .nav-link').forEach(l => l.classList.remove('active'));
      link.classList.add('active');
      modulo = link.dataset.modulo;
      currentPage = 1;
      loadData();
    });
  });

  // Botón nueva (solo si el usuario tiene permiso crear)
  qs('btnNueva')?.addEventListener('click', abrirNueva);

  // Filtrar con botón
  qs('btnFiltrar').addEventListener('click', () => loadData(1));

  // Limpiar filtros
  qs('btnLimpiar').addEventListener('click', () => {
    ['f-buscar','f-tipo','f-estado','f-prioridad','f-origen'].forEach(id => { if(qs(id)) qs(id).value=''; });
    loadData(1);
  });

  // Buscar al escribir (debounce 400ms)
  qs('f-buscar').addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadData(1), 400);
  });

  // Filtros select cambian al instante
  ['f-tipo','f-estado','f-prioridad','f-origen'].forEach(id => {
    qs(id)?.addEventListener('change', () => loadData(1));
  });

  // Delegación de eventos en la tabla
  qs('tablaBody').addEventListener('click', e => {
    const btnVer      = e.target.closest('.btn-ver');
    const btnEditar   = e.target.closest('.btn-editar');
    const btnAtender  = e.target.closest('.btn-atender');
    const btnEliminar = e.target.closest('.btn-eliminar');

    if (btnVer) {
      abrirVer(JSON.parse(btnVer.dataset.rec));
    } else if (btnEditar) {
      abrirEditar(JSON.parse(btnEditar.dataset.rec));
    } else if (btnAtender) {
      marcarAtendida(btnAtender.dataset.id, btnAtender.dataset.titulo);
    } else if (btnEliminar) {
      eliminar(btnEliminar.dataset.id, btnEliminar.dataset.titulo);
    }
  });

  // Delegación paginación
  qs('paginacionWrap').addEventListener('click', e => {
    const btn = e.target.closest('[data-page]');
    if (btn) loadData(parseInt(btn.dataset.page));
  });

  // Guardar formulario
  qs('btnGuardar')?.addEventListener('click', guardar);

  // Carga inicial
  loadData();
});

})();
</script>
@endsection
