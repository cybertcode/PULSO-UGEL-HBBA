@extends('layouts/layoutMaster')
@section('title', 'Normativas — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-style')
<style>
/* ── KPI Cards ─────────────────────────────────────────────── */
.kpi-card { border-radius:14px;border:none;overflow:hidden;transition:transform .18s,box-shadow .18s; }
.kpi-card:hover { transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.10); }
.kpi-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0; }
.kpi-value { font-size:2rem;font-weight:700;line-height:1; }
.kpi-label { font-size:.72rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;opacity:.75; }
.kpi-sub { font-size:.8rem;font-weight:600; }

/* ── Normativa Cards ────────────────────────────────────────── */
.norm-card { border-radius:14px;border:1px solid rgba(0,0,0,.06);transition:transform .18s,box-shadow .18s;display:flex;flex-direction:column; }
.norm-card:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.09); }
.norm-card.is-vigente   { border-left:4px solid #28c76f; }
.norm-card.is-novigente { border-left:4px solid #a8aaae; }
.norm-header { padding:1rem 1.25rem .6rem; }
.norm-body   { padding:.4rem 1.25rem 1rem;flex:1; }

.tipo-pill { font-size:.72rem;padding:.28em .7em;border-radius:20px;font-weight:700;letter-spacing:.02em; }
.mod-badge { font-size:.68rem;padding:.25em .55em;border-radius:6px;font-weight:600;letter-spacing:.03em; }

.recurso-chip { display:inline-flex;align-items:center;gap:.2rem;font-size:.72rem;font-weight:700;
                padding:.2em .6em;border-radius:20px;text-decoration:none;transition:opacity .15s;cursor:pointer; }
.recurso-chip:hover { opacity:.8; }

/* ── Action bar ─────────────────────────────────────────────── */
.norm-actions { display:flex;gap:.4rem;padding:.75rem 1.25rem;
                border-top:1px solid rgba(0,0,0,.05);background:rgba(0,0,0,.015);
                border-radius:0 0 14px 14px;margin-top:auto; }
.btn-act { border-radius:8px;font-size:.78rem;padding:.38rem .75rem;font-weight:600; }

/* ── Filter card ────────────────────────────────────────────── */
.filter-card { border-radius:14px;border:1px solid rgba(0,0,0,.06); }
.filter-card .form-label { font-size:.72rem;font-weight:600;text-transform:uppercase;
                           letter-spacing:.04em;color:#6e6b7b;margin-bottom:.3rem; }

/* ── Empty state ────────────────────────────────────────────── */
.empty-icon { width:80px;height:80px;border-radius:50%;
              display:inline-flex;align-items:center;justify-content:center;font-size:2rem; }

/* ── Modal header gradient ──────────────────────────────────── */
.modal-header-grad {
  background:linear-gradient(135deg,var(--bs-primary),color-mix(in srgb,var(--bs-primary) 70%,var(--bs-info)));
  border-radius:16px 16px 0 0;
}
</style>
@endsection

@section('content')

{{-- ── Breadcrumb ─────────────────────────────────────────── --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home me-1" style="font-size:.85rem"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Normativas</li>
  </ol>
</nav>

{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div class="d-flex align-items-center gap-3">
    <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#667eea,#764ba2);
                display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <i class="ti tabler-book-2 text-white" style="font-size:1.4rem"></i>
    </div>
    <div>
      <h4 class="mb-0 fw-bold">Normativas</h4>
      <p class="mb-0 text-muted small"><i class="ti tabler-file-certificate me-1"></i>Marco legal y documentos normativos institucionales</p>
    </div>
  </div>
  @if($esGestor)
  <button class="btn btn-primary" onclick="abrirCrear()">
    <i class="ti tabler-plus me-1"></i>Nueva Normativa
  </button>
  @endif
</div>

{{-- ── KPIs ──────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-sm-3">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">Total</div>
            <div class="kpi-value" id="kpi-total">{{ $stats['total'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)"><i class="ti tabler-files"></i></div>
        </div>
        <div class="kpi-sub text-white-75">Registradas</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-3">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">Vigentes</div>
            <div class="kpi-value" id="kpi-vigentes">{{ $stats['vigentes'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)"><i class="ti tabler-circle-check"></i></div>
        </div>
        <div class="kpi-sub text-white-75">En vigor actualmente</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-3">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">Con Archivo</div>
            <div class="kpi-value" id="kpi-archivo">{{ $stats['con_archivo'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)"><i class="ti tabler-file-download"></i></div>
        </div>
        <div class="kpi-sub text-white-75">Descargables</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-3">
    <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%)">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <div class="kpi-label text-white-50">Con Tutorial</div>
            <div class="kpi-value" id="kpi-tutorial">{{ $stats['con_tutorial'] }}</div>
          </div>
          <div class="kpi-icon" style="background:rgba(255,255,255,.15)"><i class="ti tabler-player-play"></i></div>
        </div>
        <div class="kpi-sub text-white-75">Con video o guía</div>
      </div>
    </div>
  </div>
</div>

{{-- ── Filtros ─────────────────────────────────────────────── --}}
<div class="card filter-card mb-4">
  <div class="card-body p-3">
    <div class="row g-3 align-items-end">
      <div class="col-md-4 col-sm-12">
        <label class="form-label"><i class="ti tabler-search me-1"></i>Buscar</label>
        <div class="input-group">
          <span class="input-group-text"><i class="ti tabler-search"></i></span>
          <input type="text" id="filtro-buscar" class="form-control" placeholder="Nombre, código, entidad...">
          <span class="input-group-text px-2 d-none" id="spinner-buscar">
            <span class="spinner-border spinner-border-sm text-primary" style="width:.8rem;height:.8rem"></span>
          </span>
        </div>
      </div>
      <div class="col-md-2 col-sm-6">
        <label class="form-label"><i class="ti tabler-tag me-1"></i>Tipo</label>
        <select id="filtro-tipo" class="form-select">
          <option value="">Todos los tipos</option>
          @foreach($tipos as $k => $v)
          <option value="{{ $k }}">{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-6">
        <label class="form-label"><i class="ti tabler-layers-difference me-1"></i>Módulo</label>
        <select id="filtro-modulo" class="form-select">
          <option value="">Todos</option>
          @foreach($modulos as $k => $v)
          <option value="{{ $k }}">{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-6">
        <label class="form-label"><i class="ti tabler-map-pin me-1"></i>Alcance</label>
        <select id="filtro-alcance" class="form-select">
          <option value="">Todos</option>
          @foreach($alcances as $k => $v)
          <option value="{{ $k }}">{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-6">
        <label class="form-label d-block">&nbsp;</label>
        <div class="d-flex gap-2 align-items-center">
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="filtro-vigentes">
            <label class="form-check-label small fw-semibold" for="filtro-vigentes">Solo vigentes</label>
          </div>
          <button class="btn btn-outline-danger btn-sm px-2 invisible" id="btn-limpiar" title="Limpiar">
            <i class="ti tabler-x"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Contador ─────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 px-1">
  <div class="text-muted small">
    <i class="ti tabler-list me-1"></i>
    <span id="contador-resultados">Cargando...</span>
    <span id="badge-filtros" class="ms-2 badge bg-label-primary d-none">con filtros activos</span>
  </div>
  <span class="text-muted small"><i class="ti tabler-sort-descending me-1"></i>Más recientes primero</span>
</div>

{{-- ── Grid ────────────────────────────────────────────────── --}}
<div class="row g-3" id="normativas-grid">
  <div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>
</div>

{{-- ── Paginación ──────────────────────────────────────────── --}}
<div class="mt-4 d-flex justify-content-center" id="paginacion-container"></div>


{{-- ══════════════════════════════════════════════════════════
     MODAL CREAR / EDITAR
═══════════════════════════════════════════════════════════ --}}
@if($esGestor)
<div class="modal fade" id="modalNormativa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:16px;border:none">

      <div class="modal-header modal-header-grad">
        <div class="flex-grow-1">
          <h6 class="modal-title fw-bold mb-0" id="modalNormativaLabel" style="color:#fff">
            <i class="ti tabler-file-plus me-2"></i>Nueva Normativa
          </h6>
          <p class="mb-0 mt-1" style="font-size:.78rem;color:rgba(255,255,255,.75)">Complete los datos del documento normativo</p>
        </div>
        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        {{-- NOTA: no usamos <form> con enctype para poder controlar el FormData manualmente --}}
        <div id="formNormativa">

          {{-- Nombre + Código --}}
          <div class="row g-3 mb-4">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
              <input type="text" id="f_nombre" class="form-control"
                     placeholder="Ej: Ley N° 28716 — Ley de Control Interno">
              <div class="invalid-feedback" id="err_nombre"></div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Código / Número</label>
              <input type="text" id="f_codigo" class="form-control" placeholder="Ej: D.S. 054-2018-PCM">
            </div>
          </div>

          {{-- Tipo + Alcance + Módulo --}}
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
              <select id="f_tipo" class="form-select">
                @foreach($tipos as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Alcance <span class="text-danger">*</span></label>
              <select id="f_alcance" class="form-select">
                @foreach($alcances as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Módulo <span class="text-danger">*</span></label>
              <select id="f_modulo" class="form-select">
                @foreach($modulos as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- Descripción --}}
          <div class="mb-4">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="f_descripcion" class="form-control" rows="3"
                      placeholder="Resumen del contenido y alcance de esta normativa..."></textarea>
          </div>

          {{-- Recursos --}}
          <div class="card border mb-4" style="border-radius:12px">
            <div class="card-header py-2 px-3 d-flex align-items-center gap-2"
                 style="background:rgba(115,103,240,.05);border-bottom:1px solid rgba(0,0,0,.06);border-radius:12px 12px 0 0">
              <i class="ti tabler-paperclip text-primary"></i>
              <span class="fw-semibold" style="font-size:.88rem">Recursos del Documento</span>
              <small class="text-muted ms-1">— puede subir archivo Y/O link externo</small>
            </div>
            <div class="card-body p-3">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    <i class="ti tabler-upload me-1 text-primary"></i>Subir Archivo
                  </label>
                  <input type="file" id="f_archivo" class="form-control"
                         accept=".pdf,.doc,.docx,.zip,.pptx,.xls,.xlsx">
                  <div class="form-text">PDF, Word, Excel, ZIP, PPT — máx. 20 MB</div>
                  <div id="archivo_actual" class="mt-1 small text-success d-none">
                    <i class="ti tabler-file-check me-1"></i><span id="archivo_nombre"></span>
                    <button type="button" class="btn btn-xs btn-outline-danger ms-2" onclick="quitarArchivo()">
                      <i class="ti tabler-x"></i> quitar
                    </button>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    <i class="ti tabler-link me-1 text-info"></i>Link Externo
                  </label>
                  <input type="url" id="f_link" class="form-control"
                         placeholder="https://www.gob.pe/... o Google Drive...">
                  <div class="form-text">Portal oficial, Drive, repositorio, etc.</div>
                </div>
              </div>
            </div>
          </div>

          {{-- Tutorial --}}
          <div class="card border mb-4" style="border-radius:12px">
            <div class="card-header py-2 px-3 d-flex align-items-center gap-2"
                 style="background:rgba(234,84,85,.05);border-bottom:1px solid rgba(0,0,0,.06);border-radius:12px 12px 0 0">
              <i class="ti tabler-brand-youtube text-danger"></i>
              <span class="fw-semibold" style="font-size:.88rem">Tutorial / Video</span>
              <small class="text-muted ms-1">— opcional</small>
            </div>
            <div class="card-body p-3">
              <input type="url" id="f_tutorial" class="form-control"
                     placeholder="https://www.youtube.com/watch?v=... o cualquier enlace de video">
              <div class="form-text">YouTube (embed automático), Vimeo, Drive, etc.</div>
            </div>
          </div>

          {{-- Fechas + Entidad --}}
          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <label class="form-label fw-semibold">Fecha de Emisión</label>
              <input type="date" id="f_fecha_emision" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Vigente hasta</label>
              <input type="date" id="f_fecha_vigencia" class="form-control">
              <div class="form-text">Vacío = indefinida</div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Entidad Emisora</label>
              <input type="text" id="f_entidad" class="form-control" placeholder="PCM, CGR, MINEDU...">
            </div>
            <div class="col-md-2 d-flex align-items-end pb-1">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="f_vigente" checked>
                <label class="form-check-label fw-semibold" for="f_vigente">Vigente</label>
              </div>
            </div>
          </div>

          {{-- Observación + Orden --}}
          <div class="row g-3">
            <div class="col-md-10">
              <label class="form-label fw-semibold">Observación Interna</label>
              <textarea id="f_observacion" class="form-control" rows="2"
                        placeholder="Notas sobre aplicación, restricciones o contexto..."></textarea>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Orden</label>
              <input type="number" id="f_orden" class="form-control" value="0" min="0">
            </div>
          </div>

        </div>{{-- fin #formNormativa --}}
      </div>

      <div class="modal-footer border-0 py-3 px-4">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" style="border-radius:8px">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardar" onclick="guardarNormativa()" style="border-radius:8px">
          <span class="spinner-border spinner-border-sm me-1 d-none" id="spinGuardar"></span>
          <i class="ti tabler-device-floppy me-1" id="iconGuardar"></i>Guardar
        </button>
      </div>
    </div>
  </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     MODAL DETALLE
═══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:16px;border:none">
      <div class="modal-header modal-header-grad">
        <div class="flex-grow-1 min-w-0">
          <h6 class="modal-title fw-bold mb-0" id="detalleNombre" style="color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></h6>
          <p class="mb-0 mt-1" id="detalleCodigo" style="font-size:.78rem;color:rgba(255,255,255,.75)"></p>
        </div>
        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="detalleBody">
        <div class="text-center py-5">
          <div class="spinner-border text-primary"></div>
          <div class="text-muted mt-2 small">Cargando...</div>
        </div>
      </div>
      <div class="modal-footer border-0 py-3 px-4" id="detalleFooter"></div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
/* ══════════════════════════════════════════════════════════════
   CONFIGURACIÓN GLOBAL
══════════════════════════════════════════════════════════════ */
const CSRF    = '{{ csrf_token() }}';
const GESTOR  = {{ $esGestor ? 'true' : 'false' }};
const URL_DATA    = '{{ route("normativas.data") }}';
const URL_STORE   = '{{ route("normativas.store") }}';

// IDs de normativas con rutas (se generan en el servidor, seguros)
function urlShow(id)    { return `/normativas/${id}`; }
function urlUpdate(id)  { return `/normativas/${id}`; }
function urlDestroy(id) { return `/normativas/${id}`; }
function urlToggle(id)  { return `/normativas/${id}/toggle-vigente`; }

/* ── Estado de edición ── */
let normIdEdit = null;
let modalNorm  = null;
let modalDet   = null;

const gridEl = document.getElementById('normativas-grid');
const pagEl  = document.getElementById('paginacion-container');
const cntEl  = document.getElementById('contador-resultados');

/* ══════════════════════════════════════════════════════════════
   INICIALIZAR
══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  modalNorm = new bootstrap.Modal(document.getElementById('modalNormativa') || document.createElement('div'));
  modalDet  = new bootstrap.Modal(document.getElementById('modalDetalle'));

  cargarNormativas();

  /* Filtros con debounce */
  let deb;
  document.getElementById('filtro-buscar').addEventListener('input', () => {
    document.getElementById('spinner-buscar').classList.remove('d-none');
    clearTimeout(deb);
    deb = setTimeout(() => cargarNormativas(), 450);
  });

  ['filtro-tipo','filtro-modulo','filtro-alcance'].forEach(id =>
    document.getElementById(id)?.addEventListener('change', () => cargarNormativas())
  );
  document.getElementById('filtro-vigentes').addEventListener('change', () => cargarNormativas());

  document.getElementById('btn-limpiar').addEventListener('click', limpiarFiltros);

  /* Paginación delegada */
  pagEl.addEventListener('click', e => {
    const link = e.target.closest('a[href]');
    if (!link) return;
    e.preventDefault();
    const pg = new URL(link.href).searchParams.get('page') || 1;
    cargarNormativas(pg);
  });
});

/* ══════════════════════════════════════════════════════════════
   CARGA AJAX
══════════════════════════════════════════════════════════════ */
function getFiltros() {
  return {
    buscar:       document.getElementById('filtro-buscar').value.trim(),
    tipo:         document.getElementById('filtro-tipo').value,
    modulo:       document.getElementById('filtro-modulo').value,
    alcance:      document.getElementById('filtro-alcance').value,
    solo_vigentes:document.getElementById('filtro-vigentes').checked ? '1' : '',
  };
}

function cargarNormativas(page = 1) {
  const f = getFiltros();
  const params = new URLSearchParams({ ...f, page });

  gridEl.style.opacity = '0.45';
  gridEl.style.pointerEvents = 'none';

  fetch(`${URL_DATA}?${params}`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(d => {
    gridEl.innerHTML = d.html;
    pagEl.innerHTML  = d.links ?? '';
    document.getElementById('spinner-buscar').classList.add('d-none');

    cntEl.innerHTML = d.total === 0
      ? '0 resultados'
      : `Mostrando <strong class="text-body">${d.from}–${d.to}</strong> de <strong class="text-body">${d.total}</strong> normativa(s)`;

    const hayFiltros = !!(f.buscar || f.tipo || f.modulo || f.alcance || f.solo_vigentes);
    document.getElementById('badge-filtros').classList.toggle('d-none', !hayFiltros);
    document.getElementById('btn-limpiar').classList.toggle('invisible', !hayFiltros);

    // Actualizar KPIs
    if (d.stats) {
      setKpi('kpi-total',    d.stats.total);
      setKpi('kpi-vigentes', d.stats.vigentes);
      setKpi('kpi-archivo',  d.stats.archivo);
      setKpi('kpi-tutorial', d.stats.tutorial);
    }

    gridEl.style.opacity = '1';
    gridEl.style.pointerEvents = '';
  })
  .catch(() => {
    gridEl.style.opacity = '1';
    gridEl.style.pointerEvents = '';
    gridEl.innerHTML = '<div class="col-12"><div class="alert alert-danger">Error al cargar normativas.</div></div>';
  });
}

function setKpi(id, val) {
  const el = document.getElementById(id);
  if (el && val !== undefined) el.textContent = val;
}

function limpiarFiltros() {
  document.getElementById('filtro-buscar').value = '';
  document.getElementById('filtro-tipo').value = '';
  document.getElementById('filtro-modulo').value = '';
  document.getElementById('filtro-alcance').value = '';
  document.getElementById('filtro-vigentes').checked = false;
  cargarNormativas();
}

/* ══════════════════════════════════════════════════════════════
   MODAL CREAR
══════════════════════════════════════════════════════════════ */
function abrirCrear() {
  normIdEdit = null;
  document.getElementById('modalNormativaLabel').innerHTML =
    '<i class="ti tabler-file-plus me-2"></i>Nueva Normativa';

  // Limpiar campos
  ['f_nombre','f_codigo','f_descripcion','f_link','f_tutorial',
   'f_fecha_emision','f_fecha_vigencia','f_entidad','f_observacion'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });
  document.getElementById('f_tipo').value    = 'ley';
  document.getElementById('f_alcance').value = 'nacional';
  document.getElementById('f_modulo').value  = 'general';
  document.getElementById('f_orden').value   = '0';
  document.getElementById('f_vigente').checked = true;
  document.getElementById('f_archivo').value  = '';
  document.getElementById('archivo_actual').classList.add('d-none');
  limpiarErrores();

  modalNorm.show();
}

/* ══════════════════════════════════════════════════════════════
   MODAL EDITAR
══════════════════════════════════════════════════════════════ */
function editarNormativa(id) {
  normIdEdit = id;
  document.getElementById('modalNormativaLabel').innerHTML =
    '<i class="ti tabler-edit me-2"></i>Editar Normativa';
  limpiarErrores();

  fetch(urlShow(id), { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(d => {
      const n = d.normativa;
      document.getElementById('f_nombre').value         = n.nombre        || '';
      document.getElementById('f_codigo').value         = n.codigo        || '';
      document.getElementById('f_descripcion').value    = n.descripcion   || '';
      document.getElementById('f_tipo').value           = n.tipo          || 'otro';
      document.getElementById('f_alcance').value        = n.alcance       || 'nacional';
      document.getElementById('f_modulo').value         = n.modulo        || 'general';
      document.getElementById('f_link').value           = n.link_externo  || '';
      document.getElementById('f_tutorial').value       = n.tutorial_url  || '';
      document.getElementById('f_fecha_emision').value  = n.fecha_emision || '';
      document.getElementById('f_fecha_vigencia').value = n.fecha_vigencia|| '';
      document.getElementById('f_entidad').value        = n.entidad_emisora|| '';
      document.getElementById('f_observacion').value    = n.observacion   || '';
      document.getElementById('f_orden').value          = n.orden ?? 0;
      document.getElementById('f_vigente').checked      = !!n.vigente;
      document.getElementById('f_archivo').value        = '';

      if (d.tiene_archivo) {
        document.getElementById('archivo_actual').classList.remove('d-none');
        document.getElementById('archivo_nombre').textContent = n.archivo_nombre_original || 'Archivo adjunto';
      } else {
        document.getElementById('archivo_actual').classList.add('d-none');
      }

      modalNorm.show();
    })
    .catch(() => toastErr('No se pudo cargar la normativa.'));
}

function quitarArchivo() {
  document.getElementById('f_archivo').value = '';
  document.getElementById('archivo_actual').classList.add('d-none');
}

/* ══════════════════════════════════════════════════════════════
   GUARDAR (crear o actualizar)
══════════════════════════════════════════════════════════════ */
function guardarNormativa() {
  const nombre = document.getElementById('f_nombre').value.trim();
  if (!nombre) {
    document.getElementById('f_nombre').classList.add('is-invalid');
    document.getElementById('err_nombre').textContent = 'El nombre es obligatorio.';
    return;
  }

  const esEditar = !!normIdEdit;
  const url      = esEditar ? urlUpdate(normIdEdit) : URL_STORE;

  const fd = new FormData();
  fd.append('_token',      CSRF);
  if (esEditar) fd.append('_method', 'PUT');

  fd.append('nombre',          document.getElementById('f_nombre').value.trim());
  fd.append('codigo',          document.getElementById('f_codigo').value.trim());
  fd.append('descripcion',     document.getElementById('f_descripcion').value.trim());
  fd.append('tipo',            document.getElementById('f_tipo').value);
  fd.append('alcance',         document.getElementById('f_alcance').value);
  fd.append('modulo',          document.getElementById('f_modulo').value);
  fd.append('link_externo',    document.getElementById('f_link').value.trim());
  fd.append('tutorial_url',    document.getElementById('f_tutorial').value.trim());
  fd.append('fecha_emision',   document.getElementById('f_fecha_emision').value);
  fd.append('fecha_vigencia',  document.getElementById('f_fecha_vigencia').value);
  fd.append('entidad_emisora', document.getElementById('f_entidad').value.trim());
  fd.append('observacion',     document.getElementById('f_observacion').value.trim());
  fd.append('orden',           document.getElementById('f_orden').value || '0');
  fd.append('vigente',         document.getElementById('f_vigente').checked ? '1' : '0');

  const archivoInput = document.getElementById('f_archivo');
  if (archivoInput.files.length > 0) {
    fd.append('archivo', archivoInput.files[0]);
  }

  setBtnGuardar(true);

  fetch(url, {
    method:  'POST',           // Laravel usa _method=PUT para emular PUT con FormData
    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: fd,
  })
  .then(r => r.json())
  .then(d => {
    setBtnGuardar(false);

    if (d.errors) {
      mostrarErrores(d.errors);
      return;
    }

    modalNorm.hide();
    Swal.fire({
      icon: 'success', title: '¡Guardado!',
      text: d.message, timer: 1800,
      showConfirmButton: false, timerProgressBar: true,
    }).then(() => cargarNormativas());
  })
  .catch(() => {
    setBtnGuardar(false);
    Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
  });
}

function setBtnGuardar(cargando) {
  const btn  = document.getElementById('btnGuardar');
  const spin = document.getElementById('spinGuardar');
  const icon = document.getElementById('iconGuardar');
  if (!btn) return;
  btn.disabled = cargando;
  spin.classList.toggle('d-none', !cargando);
  icon.classList.toggle('d-none', cargando);
}

/* ══════════════════════════════════════════════════════════════
   VER DETALLE
══════════════════════════════════════════════════════════════ */
function verNormativa(id) {
  document.getElementById('detalleBody').innerHTML =
    '<div class="text-center py-5"><div class="spinner-border text-primary"></div><div class="text-muted mt-2 small">Cargando...</div></div>';
  document.getElementById('detalleFooter').innerHTML = '';
  document.getElementById('detalleNombre').textContent = '';
  document.getElementById('detalleCodigo').textContent = '';

  modalDet.show();

  fetch(urlShow(id), { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(d => {
      const n = d.normativa;
      document.getElementById('detalleNombre').textContent = n.nombre;
      document.getElementById('detalleCodigo').textContent = n.codigo || '';

      let html = `<div class="d-flex flex-wrap gap-2 mb-4">
        <span class="tipo-pill bg-label-${d.tipo_color}"><i class="ti ${d.tipo_icon} me-1"></i>${d.tipo_label}</span>
        <span class="mod-badge bg-label-${d.modulo_color}">${d.modulo_label}</span>
        <span class="mod-badge bg-label-secondary">${d.alcance_label}</span>
        ${d.esta_vigente
          ? '<span class="mod-badge bg-label-success"><i class="ti tabler-check me-1"></i>Vigente</span>'
          : '<span class="mod-badge bg-label-secondary text-muted"><i class="ti tabler-x me-1"></i>No vigente</span>'}
      </div>`;

      if (n.entidad_emisora || n.fecha_emision || n.fecha_vigencia) {
        html += `<div class="d-flex flex-wrap gap-3 mb-4 small text-muted">`;
        if (n.entidad_emisora) html += `<span><i class="ti tabler-building me-1"></i>${esc(n.entidad_emisora)}</span>`;
        if (n.fecha_emision)   html += `<span><i class="ti tabler-calendar me-1"></i>Emitida: ${n.fecha_emision}</span>`;
        if (n.fecha_vigencia)  html += `<span><i class="ti tabler-calendar-off me-1"></i>Vigente hasta: ${n.fecha_vigencia}</span>`;
        html += '</div>';
      }

      if (n.descripcion) {
        html += `<p class="text-muted mb-4" style="line-height:1.6">${esc(n.descripcion).replace(/\n/g,'<br>')}</p>`;
      }

      // Recursos
      const tieneRecurso = d.tiene_archivo || d.tiene_link || d.tiene_tutorial;
      if (tieneRecurso) {
        html += `<div class="card border mb-4" style="border-radius:12px">
          <div class="card-header py-2 px-3 d-flex align-items-center gap-2"
               style="background:rgba(115,103,240,.05);border-radius:12px 12px 0 0;font-size:.82rem">
            <i class="ti tabler-paperclip text-primary"></i><strong>Recursos disponibles</strong>
          </div>
          <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-2 mb-2">`;

        if (d.tiene_archivo) {
          html += `<a href="${d.archivo_url}" target="_blank" class="btn btn-sm btn-primary btn-act">
            <i class="ti tabler-download me-1"></i>Descargar Archivo</a>`;
        }
        if (d.tiene_link) {
          html += `<a href="${esc(n.link_externo)}" target="_blank" rel="noopener noreferrer"
            class="btn btn-sm btn-info btn-act text-white">
            <i class="ti tabler-external-link me-1"></i>Abrir Link Externo</a>`;
        }
        if (d.tiene_tutorial) {
          html += `<button onclick="toggleTutorial()" class="btn btn-sm btn-danger btn-act">
            <i class="ti tabler-player-play me-1"></i>Ver Tutorial</button>`;
        }
        html += `</div>`;

        if (d.tiene_tutorial) {
          if (d.youtube_embed) {
            html += `<div id="tutorialBox" class="d-none mt-3">
              <div class="ratio ratio-16x9" style="border-radius:10px;overflow:hidden">
                <iframe src="${d.youtube_embed}" allowfullscreen loading="lazy"></iframe>
              </div></div>`;
          } else {
            html += `<div id="tutorialBox" class="d-none mt-2">
              <a href="${esc(n.tutorial_url)}" target="_blank" rel="noopener noreferrer"
                 class="btn btn-sm btn-outline-danger btn-act">
                <i class="ti tabler-external-link me-1"></i>Abrir Tutorial</a></div>`;
          }
        }
        html += '</div></div>';
      }

      if (n.observacion) {
        html += `<div class="alert alert-warning d-flex gap-2 mb-0" style="border-radius:10px">
          <i class="ti tabler-eye flex-shrink-0 mt-1"></i>
          <div><strong>Observación interna:</strong> ${esc(n.observacion)}</div>
        </div>`;
      }

      document.getElementById('detalleBody').innerHTML = html;

      // Footer
      let footer = `<button type="button" class="btn btn-label-secondary btn-act"
                             data-bs-dismiss="modal">Cerrar</button>`;
      if (GESTOR) {
        const lblToggle = d.esta_vigente ? 'Marcar No Vigente' : 'Marcar Vigente';
        const clsToggle = d.esta_vigente ? 'btn-outline-secondary' : 'btn-outline-success';
        footer += `
          <button class="btn btn-sm ${clsToggle} btn-act" onclick="toggleVigente(${n.id})">
            <i class="ti tabler-toggle-${d.esta_vigente ? 'left' : 'right'} me-1"></i>${lblToggle}
          </button>
          <button class="btn btn-sm btn-outline-primary btn-act"
                  onclick="modalDet.hide(); editarNormativa(${n.id})">
            <i class="ti tabler-edit me-1"></i>Editar
          </button>
          <button class="btn btn-sm btn-outline-danger btn-act"
                  onclick="confirmarEliminar(${n.id}, ${JSON.stringify(n.nombre)})">
            <i class="ti tabler-trash me-1"></i>Eliminar
          </button>`;
      }
      document.getElementById('detalleFooter').innerHTML = footer;
    })
    .catch(() => {
      document.getElementById('detalleBody').innerHTML =
        '<div class="text-center py-4 text-danger"><i class="ti tabler-wifi-off d-block mb-2" style="font-size:2rem"></i>Error al cargar el detalle.</div>';
    });
}

function toggleTutorial() {
  document.getElementById('tutorialBox')?.classList.toggle('d-none');
}

/* ══════════════════════════════════════════════════════════════
   TOGGLE VIGENTE
══════════════════════════════════════════════════════════════ */
function toggleVigente(id) {
  fetch(urlToggle(id), {
    method:  'PATCH',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
  })
  .then(r => {
    if (!r.ok) throw new Error('HTTP ' + r.status);
    return r.json();
  })
  .then(d => {
    modalDet.hide();
    Swal.fire({
      icon: 'success', title: d.message, timer: 1600,
      showConfirmButton: false, timerProgressBar: true,
    }).then(() => cargarNormativas());
  })
  .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cambiar el estado.' }));
}

/* ══════════════════════════════════════════════════════════════
   ELIMINAR
══════════════════════════════════════════════════════════════ */
function confirmarEliminar(id, nombre) {
  Swal.fire({
    title: '¿Eliminar normativa?',
    html:  `<b>${esc(nombre)}</b><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
    icon:  'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText:  'Cancelar',
  }).then(r => {
    if (!r.isConfirmed) return;

    fetch(urlDestroy(id), {
      method:  'DELETE',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
    })
    .then(r => {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    })
    .then(d => {
      modalDet.hide();
      Swal.fire({
        icon: 'success', title: d.message, timer: 1600,
        showConfirmButton: false, timerProgressBar: true,
      }).then(() => cargarNormativas());
    })
    .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar la normativa.' }));
  });
}

/* ══════════════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════════════ */
function esc(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

function toastErr(msg) {
  Swal.fire({ icon: 'error', title: 'Error', text: msg, timer: 2500, showConfirmButton: false });
}

function mostrarErrores(errors) {
  Object.keys(errors).forEach(f => {
    const el  = document.getElementById('f_' + f);
    const err = document.getElementById('err_' + f);
    if (el)  el.classList.add('is-invalid');
    if (err) err.textContent = errors[f][0];
  });
}

function limpiarErrores() {
  document.querySelectorAll('#formNormativa .is-invalid')
    .forEach(el => el.classList.remove('is-invalid'));
  document.querySelectorAll('#formNormativa .invalid-feedback')
    .forEach(el => el.textContent = '');
}
</script>
@endsection
