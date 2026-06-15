@php
$configData = Helper::appClasses();
use Illuminate\Support\Str;
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Concurso de Buenas Prácticas — PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('page-style')
<style>
/* ── KPI Cards ─────────────────────────────────────────────── */
.kpi-card { border-radius:14px;border:none;overflow:hidden;transition:transform .18s,box-shadow .18s; }
.kpi-card:hover { transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.10); }
.kpi-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0; }
.kpi-value { font-size:2rem;font-weight:700;line-height:1; }
.kpi-label { font-size:.72rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;opacity:.75; }

/* ── BP Cards ──────────────────────────────────────────────── */
.bp-card { border-radius:14px;border:1px solid rgba(0,0,0,.06);transition:transform .18s,box-shadow .18s;overflow:hidden;word-break:break-word; }
.bp-card:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.09); }
.bp-card.is-vencida { border-left-color:#ea5455 !important; }
/* Título de card: permite máx 2 líneas, no corta con ellipsis en una sola */
.bp-card h6 { display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;white-space:normal !important;text-overflow:unset !important; }
/* Meta valor: wrap normal */
.bp-meta-value { white-space:normal;word-break:break-word; }

/* Ganador highlight */
.ganador-card { box-shadow:0 4px 20px rgba(255,159,67,.3) !important; }
.ganador-banner { background:linear-gradient(135deg,rgba(255,159,67,.15),rgba(255,193,7,.1));border-bottom:1px solid rgba(255,159,67,.2); }

/* ── Badges / Pills ─────────────────────────────────────────── */
.bp-badge { font-size:.68rem;padding:.22em .55em;border-radius:6px;font-weight:600;letter-spacing:.02em;display:inline-flex;align-items:center; }
.estado-pill { font-size:.72rem;padding:.28em .7em;border-radius:20px;font-weight:700;letter-spacing:.02em;display:inline-flex;align-items:center;white-space:nowrap; }

.bp-desc { font-size:.82rem;line-height:1.5;margin-bottom:0; }
.bp-meta-icon { width:24px;height:24px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.95rem; }
.bp-meta-label { font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#a0a4b8;line-height:1; }
.bp-meta-value { font-size:.8rem;font-weight:600;color:#566a7f;line-height:1.3; }
.bp-actions { display:flex;gap:.4rem;padding:.75rem 1rem;border-top:1px solid rgba(0,0,0,.05);background:rgba(0,0,0,.015);border-radius:0 0 14px 14px; }

.dias-chip { display:inline-flex;align-items:center;font-size:.68rem;font-weight:700;padding:.15em .5em;border-radius:20px; }
.empty-icon { width:72px;height:72px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center; }

/* ── Timeline de estado (tab mis) ──────────────────────────── */
.concurso-timeline { padding:.3rem 0; }
.timeline-step { min-width:0; }
.timeline-dot { width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center; }
.timeline-dot.active { box-shadow:0 0 0 3px rgba(var(--bs-primary-rgb),.15); }
.timeline-line { flex:1;height:2px;background:#e0e0e0;min-width:8px; }
.timeline-line.active { background:var(--bs-primary); }

/* ── Tabs ───────────────────────────────────────────────────── */
.modulo-tab { border-radius:10px;padding:.45rem 1rem;font-size:.82rem;font-weight:600;border:2px solid transparent;transition:all .15s; }
.modulo-tab.active { background:var(--bs-primary);color:#fff;border-color:var(--bs-primary); }
.modulo-tab:not(.active) { background:rgba(0,0,0,.03);color:#6e6b7b;border-color:rgba(0,0,0,.07); }
.modulo-tab:not(.active):hover { border-color:var(--bs-primary);color:var(--bs-primary); }

/* ── Filter card ────────────────────────────────────────────── */
.filter-card { border-radius:14px;border:1px solid rgba(0,0,0,.06);background:#fff; }
.filter-card .form-label { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6e6b7b;margin-bottom:.35rem; }
.filter-card .form-control,.filter-card .form-select { border-radius:8px;font-size:.85rem;border-color:rgba(0,0,0,.12); }
.filter-card .form-control:focus,.filter-card .form-select:focus { border-color:var(--bs-primary);box-shadow:0 0 0 3px rgba(var(--bs-primary-rgb),.1); }
.btn-limpiar { border-radius:8px;border:2px solid rgba(0,0,0,.12);color:#6e6b7b;font-size:.82rem;font-weight:600;padding:.42rem .9rem;transition:all .15s;white-space:nowrap; }
.btn-limpiar:hover { border-color:#ea5455;color:#ea5455;background:rgba(234,84,85,.05); }
.btn-limpiar i { font-size:.95rem; }

/* ── Skeleton ───────────────────────────────────────────────── */
.skeleton { background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.2s infinite; }
@keyframes shimmer { 0%{background-position:200% 0}100%{background-position:-200% 0} }
.skeleton-card { border-radius:14px;height:230px; }

/* ── Módulo selector en modales ─────────────────────────────── */
.modulo-selector-card { transition:all .15s;cursor:pointer; }

/* ── Upload area ─────────────────────────────────────────────── */
.upload-area { border-style:dashed !important;border-width:2px !important;border-color:rgba(102,126,234,.35) !important; }
.upload-area:hover,.upload-area.dragover { border-color:var(--bs-primary) !important;background:rgba(102,126,234,.07) !important; }
.upload-area.has-file { border-color:#28c76f !important;background:rgba(40,199,111,.05) !important; }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y px-4">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home me-1" style="font-size:.85rem"></i>Inicio</a></li>
      <li class="breadcrumb-item active">Concurso de Buenas Prácticas</li>
    </ol>
  </nav>

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
      <h4 class="mb-1 fw-bold"><i class="ti tabler-trophy me-2 text-warning"></i>Concurso de Buenas Prácticas</h4>
      <p class="text-muted mb-0 small">Presenta tu proyecto · La comisión SCI lo evaluará y determinará quién representa a la UGEL Huacaybamba</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProponer">
        <i class="ti tabler-send me-1"></i>Presentar mi proyecto
      </button>
      @can('buenas-practicas.editar')
      <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalNueva">
        <i class="ti tabler-plus me-1"></i>Registrar práctica
      </button>
      @endcan
    </div>
  </div>

  {{-- KPIs --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#667eea,#764ba2)">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon" style="background:rgba(255,255,255,.2)"><i class="ti tabler-send text-white"></i></div>
          <div>
            <div class="kpi-label text-white">Proyectos enviados</div>
            <div class="kpi-value text-white">{{ $stats['total_proyectos'] }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon" style="background:rgba(255,255,255,.2)"><i class="ti tabler-tournament text-white"></i></div>
          <div>
            <div class="kpi-label text-white">Elegibles UGEL</div>
            <div class="kpi-value text-white">{{ $stats['elegibles'] }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon" style="background:rgba(255,255,255,.2)"><i class="ti tabler-trophy text-white"></i></div>
          <div>
            <div class="kpi-label text-white">Ganadores UGEL</div>
            <div class="kpi-value text-white">{{ $stats['ganadores_ugel'] }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      @can('buenas-practicas.editar')
      <div class="card kpi-card h-100" style="background:{{ $pendientesRevision > 0 ? 'linear-gradient(135deg,#f5515f,#9f041b)' : 'linear-gradient(135deg,#7c3aed,#4facfe)' }}">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon" style="background:rgba(255,255,255,.2)">
            <i class="ti {{ $pendientesRevision > 0 ? 'tabler-inbox' : 'tabler-world' }} text-white"></i>
          </div>
          <div>
            <div class="kpi-label text-white">{{ $pendientesRevision > 0 ? 'Por recepcionar' : 'En concurso externo' }}</div>
            <div class="kpi-value text-white">{{ $pendientesRevision > 0 ? $pendientesRevision : $stats['en_externo'] }}</div>
          </div>
        </div>
      </div>
      @else
      <div class="card kpi-card h-100" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon" style="background:rgba(255,255,255,.2)"><i class="ti tabler-user text-white"></i></div>
          <div>
            <div class="kpi-label text-white">Mis proyectos</div>
            <div class="kpi-value text-white">{{ $stats['mis_proyectos'] }}</div>
          </div>
        </div>
      </div>
      @endcan
    </div>
  </div>

  {{-- Tabs módulo + vista --}}
  <div class="d-flex flex-wrap gap-3 align-items-start justify-content-between mb-3">

    {{-- Grupo izquierdo: filtro por módulo --}}
    <div>
      <div class="d-flex gap-2 flex-wrap">
        <button class="modulo-tab active" data-modulo="">
          <i class="ti tabler-layout-grid me-1"></i>Todos los módulos
        </button>
        <button class="modulo-tab" data-modulo="sci">
          <i class="ti tabler-shield-check me-1"></i>Control Interno (SCI)
        </button>
        <button class="modulo-tab" data-modulo="integridad">
          <i class="ti tabler-award me-1"></i>Modelo de Integridad
        </button>
      </div>
      <div class="text-muted mt-1" style="font-size:.72rem"><i class="ti tabler-filter me-1"></i>Filtrar por módulo institucional</div>
    </div>

    {{-- Grupo derecho: cambio de vista --}}
    <div>
      <div class="d-flex gap-2 flex-wrap justify-content-end">
      <button class="btn btn-sm btn-outline-warning tab-vista" data-tab="concurso_ugel">
        <i class="ti tabler-tournament me-1"></i>Concurso UGEL
      </button>
      <button class="btn btn-sm btn-outline-secondary tab-vista" data-tab="concurso_externo">
        <i class="ti tabler-world me-1"></i>Externo
      </button>
      <button class="btn btn-sm btn-outline-secondary tab-vista @cannot('buenas-practicas.editar') active @endcannot" data-tab="mis">
        <i class="ti tabler-user me-1"></i>Mis proyectos
      </button>
      @can('buenas-practicas.editar')
      <button class="btn btn-sm btn-outline-info tab-vista active" data-tab="presentados" id="btnTabPresentados">
        <i class="ti tabler-send me-1"></i>Presentados
        @if($pendientesRevision > 0)
          <span class="badge bg-danger ms-1">{{ $pendientesRevision }}</span>
        @endif
      </button>
      <button class="btn btn-sm btn-outline-primary tab-vista" data-tab="recepcionados">
        <i class="ti tabler-inbox me-1"></i>Recepcionados
      </button>
      <button class="btn btn-sm btn-outline-warning tab-vista" data-tab="elegibles">
        <i class="ti tabler-tournament me-1"></i>Elegibles
      </button>
      <button class="btn btn-sm btn-outline-secondary tab-vista" data-tab="practicas">
        <i class="ti tabler-list me-1"></i>Prácticas SCI
      </button>
      @endcan
      </div>
      <div class="text-muted mt-1 text-end" style="font-size:.72rem"><i class="ti tabler-eye me-1"></i>Vista por etapa del concurso</div>
    </div>

  </div>

  {{-- Filtros --}}
  <div class="card filter-card mb-4">
    <div class="card-body py-3">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label">Buscar</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0" style="border-radius:8px 0 0 8px;border-color:rgba(0,0,0,.12)">
              <i class="ti tabler-search text-muted"></i>
            </span>
            <input type="text" id="filtBuscar" class="form-control border-start-0 ps-0" placeholder="Título o descripción..."
              style="border-radius:0 8px 8px 0;border-color:rgba(0,0,0,.12)">
          </div>
        </div>
        <div class="col-6 col-md-2" id="wrapFiltEstado" style="display:none">
          <label class="form-label">Estado</label>
          <select id="filtEstado" class="form-select">
            <option value="">Todos</option>
            <option value="pendiente">Pendiente</option>
            <option value="en_implementacion">En Implementación</option>
            <option value="completada">Completada</option>
            <option value="suspendida">Suspendida</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label">Categoría</label>
          <select id="filtCategoria" class="form-select">
            <option value="">Todas</option>
            @foreach($categorias as $k => $v)
              <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-4">
          <label class="form-label">Unidad Orgánica</label>
          <select id="filtUnidad" class="form-select select2-filter">
            <option value="">Todas las unidades</option>
            @foreach($unidades as $u)
              <option value="{{ $u->id }}">{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-auto d-flex align-items-end">
          <button class="btn btn-limpiar d-flex align-items-center gap-2" id="btnLimpiarFiltros">
            <i class="ti tabler-refresh"></i> Limpiar filtros
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Resultado label + spinner --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 fw-semibold text-muted" id="resultadosLabel">
      <i class="ti tabler-list me-1"></i>Cargando...
    </h6>
    <div id="loadingSpinner" class="d-none">
      <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
    </div>
  </div>

  {{-- Grid --}}
  <div class="row g-3" id="bpGrid">
    @for($i=0;$i<6;$i++)
    <div class="col-12 col-md-6 col-xl-4 skeleton-wrapper">
      <div class="skeleton skeleton-card"></div>
    </div>
    @endfor
  </div>

  {{-- Paginación --}}
  <div class="mt-4" id="bpPaginacion"></div>

</div>{{-- /container --}}

{{-- ══════════════════════════════════════════════════════════════
     MODAL: Usuario presenta su proyecto al concurso
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalProponer" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="max-height:90vh">
      <div class="modal-header flex-shrink-0" style="background:linear-gradient(135deg,#667eea,#764ba2)">
        <div>
          <h5 class="modal-title text-white fw-bold">
            <i class="ti tabler-send me-2"></i>Presentar Proyecto al Concurso
          </h5>
          <p class="text-white mb-0 opacity-75" style="font-size:.8rem">Tu proyecto será recepcionado por el Responsable SCI y evaluado por la comisión</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="overflow-y:auto">
      <form id="formProponer" method="POST" action="{{ route('buenas-practicas.proponer') }}" enctype="multipart/form-data">
        @csrf

          {{-- Flujo del concurso --}}
          <div class="d-flex gap-3 p-3 rounded-3 mb-4" style="background:rgba(102,126,234,.07);border:1px solid rgba(102,126,234,.18)">
            <div style="flex-shrink:0;width:32px;height:32px;border-radius:8px;background:rgba(102,126,234,.15);display:flex;align-items:center;justify-content:center">
              <i class="ti tabler-info-circle text-primary"></i>
            </div>
            <div style="font-size:.82rem">
              <div class="fw-bold mb-2">Flujo del concurso:</div>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-label-info">1. Presentas tu proyecto</span>
                <i class="ti tabler-arrow-right text-muted" style="font-size:.75rem;margin-top:2px"></i>
                <span class="badge bg-label-primary">2. SCI lo recepciona</span>
                <i class="ti tabler-arrow-right text-muted" style="font-size:.75rem;margin-top:2px"></i>
                <span class="badge bg-label-warning">3. Comisión evalúa</span>
                <i class="ti tabler-arrow-right text-muted" style="font-size:.75rem;margin-top:2px"></i>
                <span class="badge bg-label-success">4. Se declara ganador</span>
              </div>
            </div>
          </div>

          <div class="row g-3">
            {{-- Selección de módulo --}}
            <div class="col-12">
              <label class="form-label fw-bold">¿A qué módulo pertenece tu proyecto? <span class="text-danger">*</span></label>
              <div class="row g-2">
                <div class="col-md-6">
                  <input type="radio" name="modulo" id="moduloSCI" value="sci" class="d-none modulo-radio" required>
                  <label for="moduloSCI" class="modulo-selector-card w-100 h-100 d-flex align-items-center gap-3 p-3 rounded-3 border-2 border"
                    style="cursor:pointer;border-color:rgba(0,0,0,.1)">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(102,126,234,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <i class="ti tabler-shield-check text-primary" style="font-size:1.4rem"></i>
                    </div>
                    <div>
                      <div class="fw-bold" style="font-size:.9rem">Sistema de Control Interno</div>
                      <div class="text-muted" style="font-size:.75rem">Prácticas del SCI institucional</div>
                    </div>
                    <div class="ms-auto check-icon d-none"><i class="ti tabler-circle-check-filled text-primary" style="font-size:1.3rem"></i></div>
                  </label>
                </div>
                <div class="col-md-6">
                  <input type="radio" name="modulo" id="moduloIntegridad" value="integridad" class="d-none modulo-radio">
                  <label for="moduloIntegridad" class="modulo-selector-card w-100 h-100 d-flex align-items-center gap-3 p-3 rounded-3 border-2 border"
                    style="cursor:pointer;border-color:rgba(0,0,0,.1)">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,159,67,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <i class="ti tabler-award text-warning" style="font-size:1.4rem"></i>
                    </div>
                    <div>
                      <div class="fw-bold" style="font-size:.9rem">Modelo de Integridad</div>
                      <div class="text-muted" style="font-size:.75rem">Prácticas del modelo de integridad</div>
                    </div>
                    <div class="ms-auto check-icon d-none"><i class="ti tabler-circle-check-filled text-warning" style="font-size:1.3rem"></i></div>
                  </label>
                </div>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Título del proyecto <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required placeholder="Nombre claro y descriptivo de tu iniciativa">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
              <select name="categoria" class="form-select" required>
                <option value="">Seleccionar...</option>
                @foreach($categorias as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2-proponer">
                <option value="">Sin asignar</option>
                @foreach($unidades as $u)
                  <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha inicio estimada</label>
              <input type="date" name="fecha_inicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha término estimada</label>
              <input type="date" name="fecha_termino" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción del proyecto <span class="text-danger">*</span></label>
              <textarea name="descripcion" class="form-control" rows="4" required
                placeholder="Describe el objetivo, alcance, metodología y beneficios esperados..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción de evidencias / Sustento</label>
              <textarea name="evidencias" class="form-control" rows="2"
                placeholder="Documentos de referencia, normativa aplicable, experiencias previas..."></textarea>
            </div>

            {{-- Campo adjuntar archivo --}}
            <div class="col-12">
              <label class="form-label fw-bold">Adjuntar proyecto <span class="text-muted fw-normal">(PDF, DOC, DOCX, ZIP — máx. 10MB)</span></label>
              <div class="upload-area rounded-3 border-2 border border-dashed p-4 text-center"
                id="uploadArea"
                style="border-color:rgba(102,126,234,.3);background:rgba(102,126,234,.03);cursor:pointer;transition:all .15s"
                onclick="document.getElementById('archivoProyecto').click()">
                <i class="ti tabler-cloud-upload text-primary" style="font-size:2rem"></i>
                <div class="fw-semibold mt-2" style="font-size:.9rem">Haz clic para seleccionar el archivo</div>
                <div class="text-muted small mt-1" id="uploadLabel">o arrastra y suelta aquí</div>
              </div>
              <input type="file" name="archivo_proyecto" id="archivoProyecto" class="d-none"
                accept=".pdf,.doc,.docx,.zip,.pptx">
            </div>
          </div>
      </form>
      </div>
      <div class="modal-footer flex-shrink-0 border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" form="formProponer" class="btn btn-primary px-4">
            <i class="ti tabler-send me-1"></i>Presentar al concurso
          </button>
        </div>
    </div>
  </div>
</div>

@canany(['buenas-practicas.editar','buenas-practicas.crear','buenas-practicas.eliminar'])
{{-- ══════════════════════════════════════════════════════════════
     MODAL: SCI Recepciona el proyecto
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalRecepcionar" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header flex-shrink-0" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
        <div>
          <h5 class="modal-title text-white fw-bold"><i class="ti tabler-inbox me-2"></i>Recepcionar Proyecto</h5>
          <p class="text-white mb-0 opacity-75" style="font-size:.8rem">Confirma la recepción del documento presentado por el participante</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="overflow-y:auto">
        <form method="POST" id="formRecepcionar">
          @csrf @method('PATCH')
          <div class="p-3 rounded-3 mb-4" style="background:rgba(79,172,254,.08);border:1px solid rgba(79,172,254,.2)">
            <div class="fw-semibold mb-1 text-primary" style="font-size:.8rem"><i class="ti tabler-file me-1"></i>PROYECTO A RECEPCIONAR</div>
            <div id="recv_titulo" class="fw-bold" style="font-size:.95rem"></div>
            <div id="recv_participante" class="text-muted small mt-1"></div>
          </div>
          <div class="row g-3">
            <div class="col-md-7">
              <label class="form-label fw-semibold">N° de Expediente</label>
              <input type="text" name="numero_expediente" id="recv_expediente" class="form-control" placeholder="Ej: EXP-001-2026">
            </div>
            <div class="col-md-5">
              <label class="form-label fw-semibold">Fecha de recepción</label>
              <input type="date" name="fecha_recepcion" id="recv_fecha" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="2" placeholder="Notas sobre la recepción del documento..."></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer flex-shrink-0 border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formRecepcionar" class="btn btn-primary px-4">
          <i class="ti tabler-inbox me-1"></i>Confirmar recepción
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: Comisión declara ELEGIBLE (concurso interno UGEL)
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalElegible" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header flex-shrink-0" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
        <div>
          <h5 class="modal-title text-white fw-bold"><i class="ti tabler-tournament me-2"></i>Declarar Elegible — Concurso UGEL</h5>
          <p class="text-white mb-0 opacity-75" style="font-size:.8rem">El proyecto pasa a la fase de concurso interno de la UGEL Huacaybamba</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="overflow-y:auto">
        <form method="POST" id="formElegible">
          @csrf @method('PATCH')
          <div class="p-3 rounded-3 mb-4" style="background:rgba(247,151,30,.08);border:1px solid rgba(247,151,30,.2)">
            <div class="fw-semibold mb-1 text-warning" style="font-size:.8rem"><i class="ti tabler-file-check me-1"></i>PROYECTO A EVALUAR</div>
            <div id="eleg_titulo" class="fw-bold" style="font-size:.95rem"></div>
            <div id="eleg_participante" class="text-muted small mt-1"></div>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-bold">Puntaje de la comisión (0–100) <span class="text-danger">*</span></label>
              <div class="d-flex align-items-center gap-3">
                <input type="range" name="puntaje_comision" id="rangePuntaje" class="form-range flex-grow-1" min="0" max="100" value="70">
                <span class="fw-bold text-primary" id="puntajeLabel" style="min-width:45px;font-size:1.1rem">70</span>
              </div>
              <div class="d-flex justify-content-between" style="font-size:.7rem;color:#aaa">
                <span>0 — Deficiente</span><span>50 — Regular</span><span>80 — Bueno</span><span>100 — Excelente</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Responsable asignado</label>
              <select name="responsable_id" class="form-select select2-elegible">
                <option value="">Sin asignar</option>
                @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Observación de la comisión <span class="text-danger">*</span></label>
              <textarea name="observacion_comision" class="form-control" rows="3" required
                placeholder="Fundamento de la decisión de declarar elegible este proyecto..."></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer flex-shrink-0 border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formElegible" class="btn btn-warning px-4">
          <i class="ti tabler-tournament me-1"></i>Declarar Elegible
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: Declarar Ganador UGEL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalGanadorUgel" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header flex-shrink-0" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
        <div>
          <h5 class="modal-title text-white fw-bold"><i class="ti tabler-trophy me-2"></i>Declarar Ganador UGEL</h5>
          <p class="text-white mb-0 opacity-75" style="font-size:.8rem">Este proyecto representará a la UGEL Huacaybamba ante MINEDU o DRE</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="overflow-y:auto">
        <form method="POST" id="formGanadorUgel">
          @csrf @method('PATCH')
          <div class="text-center py-3 mb-3">
            <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#ffd200,#f7971e);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
              <i class="ti tabler-trophy text-white" style="font-size:2rem"></i>
            </div>
            <h6 class="fw-bold mb-1" id="ganu_titulo"></h6>
            <p class="text-muted small mb-0" id="ganu_participante"></p>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Mensaje para el ganador</label>
            <textarea name="observacion_comision" class="form-control" rows="3"
              placeholder="Felicitaciones. Su proyecto fue seleccionado para representar a la UGEL Huacaybamba..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer flex-shrink-0 border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formGanadorUgel" class="btn btn-success px-4">
          <i class="ti tabler-trophy me-1"></i>¡Declarar Ganador UGEL!
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: Enviar a concurso externo (MINEDU / DRE)
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalExterno" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header flex-shrink-0" style="background:linear-gradient(135deg,#7c3aed,#4facfe)">
        <div>
          <h5 class="modal-title text-white fw-bold"><i class="ti tabler-world me-2"></i>Enviar a Concurso Externo</h5>
          <p class="text-white mb-0 opacity-75" style="font-size:.8rem">Registra la participación de la UGEL en el concurso externo</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="overflow-y:auto">
        <form method="POST" id="formExterno">
          @csrf @method('PATCH')
          <div class="p-3 rounded-3 mb-4" style="background:rgba(124,58,237,.07);border:1px solid rgba(124,58,237,.15)">
            <div class="fw-semibold mb-1" style="font-size:.8rem;color:#7c3aed"><i class="ti tabler-world me-1"></i>PROYECTO A ENVIAR</div>
            <div id="ext_titulo" class="fw-bold" style="font-size:.95rem"></div>
            <div id="ext_participante" class="text-muted small mt-1"></div>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-bold">Nivel del concurso externo <span class="text-danger">*</span></label>
              <div class="row g-2">
                <div class="col-6">
                  <input type="radio" name="nivel_externo" id="nivelMinedu" value="minedu" class="d-none" required>
                  <label for="nivelMinedu" class="w-100 text-center p-3 rounded-3 border-2 border"
                    style="cursor:pointer;border-color:rgba(0,0,0,.1)">
                    <i class="ti tabler-building-community d-block mb-1" style="font-size:1.5rem;color:#7c3aed"></i>
                    <div class="fw-bold" style="font-size:.9rem">MINEDU</div>
                    <div class="text-muted" style="font-size:.72rem">Ministerio de Educación</div>
                  </label>
                </div>
                <div class="col-6">
                  <input type="radio" name="nivel_externo" id="nivelDre" value="dre" class="d-none">
                  <label for="nivelDre" class="w-100 text-center p-3 rounded-3 border-2 border"
                    style="cursor:pointer;border-color:rgba(0,0,0,.1)">
                    <i class="ti tabler-map-pin d-block mb-1" style="font-size:1.5rem;color:#4facfe"></i>
                    <div class="fw-bold" style="font-size:.9rem">DRE Huánuco</div>
                    <div class="text-muted" style="font-size:.72rem">Dirección Regional</div>
                  </label>
                </div>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Fecha del concurso</label>
              <input type="date" name="fecha_concurso_externo" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea name="observacion_comision" class="form-control" rows="2"
                placeholder="Detalles sobre la participación en el concurso externo..."></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer flex-shrink-0 border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formExterno" class="btn px-4 text-white" style="background:#7c3aed">
          <i class="ti tabler-world me-1"></i>Registrar participación
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: Resultado del concurso externo
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalResultadoExterno" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header flex-shrink-0" style="background:linear-gradient(135deg,#11998e,#7c3aed)">
        <div>
          <h5 class="modal-title text-white fw-bold"><i class="ti tabler-flag me-2"></i>Resultado Concurso Externo</h5>
          <p class="text-white mb-0 opacity-75" style="font-size:.8rem">Registra el resultado obtenido en el concurso externo</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="overflow-y:auto">
        <form method="POST" id="formResultadoExterno">
          @csrf @method('PATCH')
          <div class="p-3 rounded-3 mb-4" style="background:rgba(17,153,142,.08);border:1px solid rgba(17,153,142,.2)">
            <div class="fw-semibold mb-1 text-success" style="font-size:.8rem"><i class="ti tabler-flag me-1"></i>PROYECTO</div>
            <div id="res_titulo" class="fw-bold" style="font-size:.95rem"></div>
            <div id="res_nivel" class="text-muted small mt-1"></div>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-bold">¿Ganó en el concurso externo? <span class="text-danger">*</span></label>
              <div class="d-flex gap-3">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gano_externo" id="ganoSi" value="1" required>
                  <label class="form-check-label fw-semibold text-success" for="ganoSi">
                    <i class="ti tabler-trophy me-1"></i>Sí, ganó
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gano_externo" id="ganoNo" value="0">
                  <label class="form-check-label fw-semibold text-muted" for="ganoNo">
                    <i class="ti tabler-circle-x me-1"></i>No ganó
                  </label>
                </div>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción del resultado</label>
              <textarea name="resultado_externo" class="form-control" rows="2"
                placeholder="Puesto obtenido, puntaje, observaciones del jurado externo..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Mensaje para el participante</label>
              <textarea name="observacion_comision" class="form-control" rows="2"
                placeholder="Comunicado oficial del resultado..."></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer flex-shrink-0 border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formResultadoExterno" class="btn btn-success px-4">
          <i class="ti tabler-flag me-1"></i>Guardar resultado
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: Registrar práctica institucional (SCI directo)
══════════════════════════════════════════════════════════════ --}}
@can('buenas-practicas.crear')
<div class="modal fade" id="modalNueva" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="ti tabler-plus me-2 text-primary"></i>Registrar Práctica Institucional</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="overflow-y:auto">
        <form id="formNueva" method="POST" action="{{ route('buenas-practicas.store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-bold">Módulo <span class="text-danger">*</span></label>
              <div class="row g-2">
                <div class="col-md-6">
                  <input type="radio" name="modulo" id="nModuloSCI" value="sci" class="d-none modulo-radio-nueva" required>
                  <label for="nModuloSCI" class="modulo-selector-card w-100 d-flex align-items-center gap-2 p-3 rounded-3 border-2 border" style="cursor:pointer;border-color:rgba(0,0,0,.1)">
                    <i class="ti tabler-shield-check text-primary fs-5"></i>
                    <span class="fw-semibold" style="font-size:.88rem">Control Interno (SCI)</span>
                    <div class="ms-auto check-icon d-none"><i class="ti tabler-circle-check-filled text-primary"></i></div>
                  </label>
                </div>
                <div class="col-md-6">
                  <input type="radio" name="modulo" id="nModuloIntegridad" value="integridad" class="d-none modulo-radio-nueva">
                  <label for="nModuloIntegridad" class="modulo-selector-card w-100 d-flex align-items-center gap-2 p-3 rounded-3 border-2 border" style="cursor:pointer;border-color:rgba(0,0,0,.1)">
                    <i class="ti tabler-award text-warning fs-5"></i>
                    <span class="fw-semibold" style="font-size:.88rem">Modelo de Integridad</span>
                    <div class="ms-auto check-icon d-none"><i class="ti tabler-circle-check-filled text-warning"></i></div>
                  </label>
                </div>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
              <select name="categoria" class="form-select" required>
                @foreach($categorias as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Impacto</label>
              <select name="impacto" class="form-select">
                <option value="">Seleccionar...</option>
                <option value="alto">Alto</option>
                <option value="medio">Medio</option>
                <option value="bajo">Bajo</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2-nueva">
                <option value="">Sin asignar</option>
                @foreach($unidades as $u)
                  <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Responsable</label>
              <select name="responsable_id" class="form-select select2-nueva">
                <option value="">Sin asignar</option>
                @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
              <select name="estado" class="form-select" required>
                <option value="pendiente">Pendiente</option>
                <option value="en_implementacion" selected>En Implementación</option>
                <option value="completada">Completada</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Avance (%)</label>
              <input type="number" name="avance" class="form-control" min="0" max="100" value="0">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">N° SGD</label>
              <input type="text" name="numero_sgd" class="form-control" placeholder="001-2026">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha Inicio</label>
              <input type="date" name="fecha_inicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha Término</label>
              <input type="date" name="fecha_termino" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Evidencias</label>
              <textarea name="evidencias" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer flex-shrink-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formNueva" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar</button>
      </div>
    </div>
  </div>
</div>
@endcan

{{-- ══════════════════════════════════════════════════════════════
     MODAL: Presentar práctica institucional al concurso
══════════════════════════════════════════════════════════════ --}}
@can('buenas-practicas.editar')
<div class="modal fade" id="modalPresentarPractica" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
        <div>
          <h5 class="modal-title text-white fw-bold">
            <i class="ti tabler-send me-2"></i>Presentar al Concurso
          </h5>
          <p class="text-white mb-0 opacity-75" style="font-size:.8rem">Esta práctica se registrará como proyecto concursal</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-4">
        {{-- Práctica seleccionada --}}
        <div class="d-flex gap-3 p-3 rounded-3 mb-4" style="background:rgba(17,153,142,.07);border:1px solid rgba(17,153,142,.2)">
          <div style="flex-shrink:0;width:36px;height:36px;border-radius:10px;background:rgba(17,153,142,.15);display:flex;align-items:center;justify-content:center">
            <i class="ti tabler-file-check text-success"></i>
          </div>
          <div>
            <div class="fw-bold" id="pp_titulo" style="font-size:.95rem"></div>
            <div class="text-muted small mt-1" id="pp_modulo"></div>
          </div>
        </div>

        {{-- Campos editables para el concurso --}}
        <form id="formPresentarPractica" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título para el concurso <span class="text-danger">*</span></label>
              <input type="text" name="titulo" id="pp_titulo_input" class="form-control" required
                placeholder="Puedes ajustar el título para el concurso">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción del proyecto <span class="text-danger">*</span></label>
              <textarea name="descripcion" id="pp_descripcion" class="form-control" rows="3" required
                placeholder="Describe brevemente el objetivo e impacto..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">¿A qué módulo pertenece? <span class="text-danger">*</span></label>
              <select name="modulo" id="pp_modulo_select" class="form-select" required>
                <option value="sci">Sistema de Control Interno (SCI)</option>
                <option value="integridad">Modelo de Integridad</option>
              </select>
            </div>
          </div>
        </form>

        <div class="alert alert-info d-flex gap-2 mt-3 mb-0 py-2" style="font-size:.82rem">
          <i class="ti tabler-info-circle flex-shrink-0 mt-1"></i>
          <span>Se creará un nuevo proyecto de concurso con estado <strong>Presentado</strong>. La práctica original no se modifica.</span>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formPresentarPractica" class="btn btn-success px-4">
          <i class="ti tabler-send me-1"></i>Presentar al concurso
        </button>
      </div>
    </div>
  </div>
</div>
@endcan

{{-- ══════════════════════════════════════════════════════════════
     MODAL: Editar práctica institucional
══════════════════════════════════════════════════════════════ --}}
@can('buenas-practicas.editar')
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="ti tabler-edit me-2 text-primary"></i>Editar Práctica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="overflow-y:auto">
        <form method="POST" id="formEditar">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-bold">Módulo <span class="text-danger">*</span></label>
              <div class="row g-2">
                <div class="col-md-6">
                  <input type="radio" name="modulo" id="eModuloSCI" value="sci" class="d-none modulo-radio-edit">
                  <label for="eModuloSCI" class="modulo-selector-card w-100 d-flex align-items-center gap-2 p-3 rounded-3 border-2 border" style="cursor:pointer;border-color:rgba(0,0,0,.1)">
                    <i class="ti tabler-shield-check text-primary fs-5"></i>
                    <span class="fw-semibold" style="font-size:.88rem">Control Interno (SCI)</span>
                    <div class="ms-auto check-icon d-none"><i class="ti tabler-circle-check-filled text-primary"></i></div>
                  </label>
                </div>
                <div class="col-md-6">
                  <input type="radio" name="modulo" id="eModuloIntegridad" value="integridad" class="d-none modulo-radio-edit">
                  <label for="eModuloIntegridad" class="modulo-selector-card w-100 d-flex align-items-center gap-2 p-3 rounded-3 border-2 border" style="cursor:pointer;border-color:rgba(0,0,0,.1)">
                    <i class="ti tabler-award text-warning fs-5"></i>
                    <span class="fw-semibold" style="font-size:.88rem">Modelo de Integridad</span>
                    <div class="ms-auto check-icon d-none"><i class="ti tabler-circle-check-filled text-warning"></i></div>
                  </label>
                </div>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" id="edit_titulo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Categoría</label>
              <select name="categoria" id="edit_categoria" class="form-select">
                @foreach($categorias as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Impacto</label>
              <select name="impacto" id="edit_impacto" class="form-select">
                <option value="">Seleccionar...</option>
                <option value="alto">Alto</option>
                <option value="medio">Medio</option>
                <option value="bajo">Bajo</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" id="edit_unidad" class="form-select select2-edit">
                <option value="">Sin asignar</option>
                @foreach($unidades as $u)
                  <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Responsable</label>
              <select name="responsable_id" id="edit_responsable" class="form-select select2-edit">
                <option value="">Sin asignar</option>
                @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado</label>
              <select name="estado" id="edit_estado" class="form-select">
                <option value="pendiente">Pendiente</option>
                <option value="en_implementacion">En Implementación</option>
                <option value="completada">Completada</option>
                <option value="suspendida">Suspendida</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Avance (%)</label>
              <input type="number" name="avance" id="edit_avance" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">N° SGD</label>
              <input type="text" name="numero_sgd" id="edit_sgd" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha Inicio</label>
              <input type="date" name="fecha_inicio" id="edit_inicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha Término</label>
              <input type="date" name="fecha_termino" id="edit_termino" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Evidencias</label>
              <textarea name="evidencias" id="edit_evidencias" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer flex-shrink-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formEditar" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button>
      </div>
    </div>
  </div>
</div>
@endcan

{{-- Forms ocultos para acciones PATCH/DELETE --}}
<form id="formEliminar"   method="POST" style="display:none">@csrf @method('DELETE')</form>
<form id="formNoElegible" method="POST" style="display:none">@csrf @method('PATCH')</form>
@endcanany

@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('page-script')
<script>
window.addEventListener('load', () => window.setTimeout(() => {

  // ── Estado reactivo ──────────────────────────────────────────
  // Gestores ven primero los "Presentados", usuarios normales sus propios proyectos
  @can('buenas-practicas.editar')
  let activeTab = 'presentados';
  @else
  let activeTab = 'mis';
  @endcan
  let activeModulo = '';
  let searchTimer  = null;
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN':     CSRF,
      'X-Requested-With': 'XMLHttpRequest',
      'Accept':           'application/json',
    }
  });

  // ── Select2 ─────────────────────────────────────────────────
  $('.select2-filter').select2({ width: '100%', placeholder: 'Todas' });
  $('.select2-proponer').select2({ dropdownParent: $('#modalProponer'), width: '100%', placeholder: 'Seleccionar...' });
  @can('buenas-practicas.ver')
  $('.select2-nueva').select2({ dropdownParent: $('#modalNueva'), width: '100%', placeholder: 'Seleccionar...' });
  $('.select2-edit').select2({ dropdownParent: $('#modalEditar'), width: '100%', placeholder: 'Seleccionar...' });
  $('.select2-elegible').select2({ dropdownParent: $('#modalElegible'), width: '100%', placeholder: 'Seleccionar...' });
  @endcan
  $('#filtUnidad').on('change', () => loadCards());

  // ── Módulo selector cards ────────────────────────────────────
  function initModuloSelector(radioClass) {
    $(document).on('change', '.' + radioClass, function () {
      const val = $(this).val();
      $('.' + radioClass).each(function () {
        const $lbl = $('label[for="' + $(this).attr('id') + '"]');
        if ($(this).val() === val) {
          $lbl.css({ borderColor: val === 'sci' ? '#696cff' : '#ff9f43', background: val === 'sci' ? 'rgba(102,126,234,.06)' : 'rgba(255,159,67,.06)' });
          $lbl.find('.check-icon').removeClass('d-none');
        } else {
          $lbl.css({ borderColor: 'rgba(0,0,0,.1)', background: '' });
          $lbl.find('.check-icon').addClass('d-none');
        }
      });
    });
  }
  initModuloSelector('modulo-radio');
  initModuloSelector('modulo-radio-nueva');
  initModuloSelector('modulo-radio-edit');

  // ── Tabs Módulo ──────────────────────────────────────────────
  $('.modulo-tab').on('click', function () {
    $('.modulo-tab').removeClass('active');
    $(this).addClass('active');
    activeModulo = $(this).data('modulo');
    loadCards();
  });

  // ── Tabs Vista ───────────────────────────────────────────────
  $('.tab-vista').on('click', function () {
    $('.tab-vista').removeClass('active');
    $(this).addClass('active');
    activeTab = $(this).data('tab');
    // Filtro estado solo aplica en "practicas"
    $('#wrapFiltEstado').toggle(activeTab === 'practicas');
    loadCards();
  });
  // El filtro estado solo aplica en tab "practicas" (ya oculto por defecto en HTML)

  // ── Filtros ──────────────────────────────────────────────────
  $('#filtBuscar').on('input', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadCards(), 400);
  });
  $('#filtEstado, #filtCategoria').on('change', () => loadCards());
  $('#btnLimpiarFiltros').on('click', function () {
    $('#filtBuscar').val('');
    $('#filtEstado').val('');
    $('#filtCategoria').val('');
    $('#filtUnidad').val('').trigger('change');
    loadCards();
  });

  // ── Cargar cards AJAX ────────────────────────────────────────
  function loadCards(page) {
    const params = {
      tab:       activeTab,
      modulo:    activeModulo,
      buscar:    $('#filtBuscar').val() || '',
      estado:    activeTab === 'practicas' ? ($('#filtEstado').val() || '') : '',
      categoria: $('#filtCategoria').val() || '',
      unidad:    $('#filtUnidad').val() || '',
    };
    if (page) params.page = page;

    $('#loadingSpinner').removeClass('d-none');
    $('#bpGrid').css('opacity', '.5');

    $.ajax({ url: '/buenas-practicas/data', type: 'GET', data: params, dataType: 'json' })
    .done(function (res) {
      if (!res || typeof res.html === 'undefined') {
        $('#bpGrid').html('<div class="col-12 text-center py-5"><div class="alert alert-warning">Sesión expirada. <a href="/dashboard">Volver</a></div></div>').css('opacity','1');
        return;
      }
      $('#bpGrid').html(res.html).css('opacity','1');
      $('#bpPaginacion').html(res.links || '');

      const labels = {
        concurso_ugel:    'proyecto(s) en concurso UGEL',
        concurso_externo: 'proyecto(s) en concurso externo',
        presentados:      'proyecto(s) presentados por recepcionar',
        recepcionados:    'proyecto(s) recepcionados por evaluar',
        elegibles:        'proyecto(s) elegibles (concurso UGEL)',
        mis:              'mis proyecto(s)',
        practicas:        'práctica(s) institucional(es)',
      };
      $('#resultadosLabel').html('<i class="ti tabler-list me-1"></i><strong>' + res.total + '</strong> ' + (labels[activeTab] || 'resultado(s)'));

      $('#bpPaginacion').find('a[href]').on('click', function(e) {
        e.preventDefault();
        try {
          const p = new URL($(this).attr('href'), window.location.origin);
          loadCards(p.searchParams.get('page') || 1);
          window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch(err) {}
      });
    })
    .fail(function (xhr) {
      let msg = 'Error al cargar los datos.';
      if (xhr.status === 401 || xhr.status === 419) msg = 'Sesión expirada. <a href="/dashboard" class="alert-link">Recarga la página</a>.';
      else if (xhr.status === 500) msg = 'Error del servidor (500). Revisa el log de Laravel.';
      $('#bpGrid').html('<div class="col-12"><div class="alert alert-danger m-3">' + msg + '</div></div>').css('opacity','1');
    })
    .always(function () { $('#loadingSpinner').addClass('d-none'); });
  }

  loadCards();

  // ── Slider puntaje comisión ──────────────────────────────────
  $(document).on('input', '#rangePuntaje', function () {
    $('#puntajeLabel').text($(this).val());
  });

  // ── Helper: enviar form modal vía AJAX y recargar cards ──────
  function bindFormAjax(formId, modalId, successMsg, nextTab, validateFn) {
    $(document).on('submit', formId, function(e) {
      e.preventDefault();
      if (validateFn && !validateFn()) return;
      const $form = $(this);
      const $modal = $(modalId);
      const $btn = $('[form="' + $form.attr('id') + '"][type="submit"]');
      const origHtml = $btn.html();

      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...');

      $.ajax({ url: $form.attr('action'), type: 'POST', data: $form.serialize() })
      .done(function() {
        $modal.find('[data-bs-dismiss="modal"]').trigger('click');
        if (nextTab) { activeTab = nextTab; $('.tab-vista').removeClass('active'); $('.tab-vista[data-tab="' + nextTab + '"]').addClass('active'); }
        loadCards();
        pulsoToast(successMsg, 'success');
      })
      .fail(function(xhr) {
        let msg = 'Error al guardar. Intenta nuevamente.';
        if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors).flat().join(' · ');
        else if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
        pulsoToast(msg, 'error');
      })
      .always(function() { $btn.prop('disabled', false).html(origHtml); });
    });
  }

  // ── PRESENTAR PRÁCTICA AL CONCURSO ──────────────────────────
  $(document).on('click', '.btn-presentar-practica', function () {
    const d = $(this).data();
    $('#formPresentarPractica').attr('action', '/buenas-practicas/' + d.id + '/presentar-practica');
    $('#pp_titulo').text(d.titulo);
    $('#pp_modulo').text(d.modulo === 'sci' ? 'Sistema de Control Interno' : 'Modelo de Integridad');
    $('#pp_titulo_input').val(d.titulo);
    $('#pp_descripcion').val(d.descripcion);
    $('#pp_modulo_select').val(d.modulo);
    new bootstrap.Modal(document.getElementById('modalPresentarPractica')).show();
  });

  $(document).on('submit', '#formPresentarPractica', function(e) {
    e.preventDefault();
    const $form = $(this);
    const $btn  = $('[form="formPresentarPractica"][type="submit"]');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Enviando...');

    $.ajax({ url: $form.attr('action'), type: 'POST', data: $form.serialize() })
    .done(function() {
      $('#modalPresentarPractica').find('[data-bs-dismiss="modal"]').trigger('click');
      // Ir al tab "presentados" para que el gestor lo vea
      activeTab = 'presentados';
      $('.tab-vista').removeClass('active');
      $('.tab-vista[data-tab="presentados"]').addClass('active');
      loadCards();
      pulsoToast('Proyecto presentado al concurso. Aparece en el tab "Presentados".', 'success');
    })
    .fail(function(xhr) {
      let msg = 'Error al presentar. Intenta nuevamente.';
      if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors).flat().join(' · ');
      else if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
      pulsoToast(msg, 'error');
    })
    .always(function() { $btn.prop('disabled', false).html(origHtml); });
  });

  // ── PROPONER PROYECTO (FormData para soportar archivo adjunto) ─
  $(document).on('submit', '#formProponer', function(e) {
    e.preventDefault();
    const $form = $(this);
    const $btn  = $('[form="formProponer"][type="submit"]');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Enviando...');

    // Validar módulo seleccionado antes de enviar
    const moduloVal = $form.find('input[name="modulo"]:checked').val();
    if (!moduloVal) {
      $btn.prop('disabled', false).html(origHtml);
      pulsoToast('Selecciona el módulo al que pertenece tu proyecto.', 'warning');
      return;
    }

    const fd = new FormData(this);
    $.ajax({
      url: $form.attr('action'), type: 'POST',
      data: fd,
      processData: false, contentType: false,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    })
    .done(function() {
      $('#modalProponer').find('[data-bs-dismiss="modal"]').trigger('click');
      $form[0].reset();
      $('.select2-proponer').val('').trigger('change');
      // Cambiar al tab "mis proyectos" para que el usuario vea su proyecto recién creado
      activeTab = 'mis';
      $('.tab-vista').removeClass('active');
      $('.tab-vista[data-tab="mis"]').addClass('active');
      $('#wrapFiltEstado').hide();
      loadCards();
      pulsoToast('¡Proyecto presentado exitosamente! El Responsable SCI lo recepcionará.', 'success');
    })
    .fail(function(xhr) {
      let msg = 'Error al enviar. Intenta nuevamente.';
      if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors).flat().join(' · ');
      else if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
      pulsoToast(msg, 'error');
    })
    .always(function() { $btn.prop('disabled', false).html(origHtml); });
  });

  // ── RECEPCIONAR ──────────────────────────────────────────────
  $(document).on('click', '.btn-recepcionar', function () {
    const d = $(this).data();
    $('#formRecepcionar').attr('action', '/buenas-practicas/' + d.id + '/recepcionar');
    $('#recv_titulo').text(d.titulo);
    $('#recv_participante').text('Participante: ' + (d.participante || '—'));
    $('#recv_fecha').val(new Date().toISOString().split('T')[0]);
    $('#recv_expediente').val('EXP-' + String(d.id).padStart(3,'0') + '-' + new Date().getFullYear());
    new bootstrap.Modal(document.getElementById('modalRecepcionar')).show();
  });

  bindFormAjax('#formRecepcionar', '#modalRecepcionar', 'Proyecto recepcionado correctamente.', 'recepcionados');

  // ── VER DETALLE (descripción completa) ───────────────────────
  $(document).on('click', '.btn-ver-detalle', function () {
    const d = $(this).data();
    Swal.fire({
      title: d.titulo,
      html: '<p class="text-start text-muted" style="font-size:.9rem">' + (d.descripcion || 'Sin descripción.') + '</p>'
            + '<p class="text-start mb-0"><strong>Participante:</strong> ' + (d.participante || '—') + '</p>',
      icon: 'info', confirmButtonText: 'Cerrar',
    });
  });

  // ── ELEGIBLE (Nivel 1 UGEL) ──────────────────────────────────
  $(document).on('click', '.btn-elegible', function () {
    const d = $(this).data();
    $('#formElegible').attr('action', '/buenas-practicas/' + d.id + '/elegible');
    $('#eleg_titulo').text(d.titulo);
    $('#eleg_participante').text('Participante: ' + (d.participante || '—'));
    $('#rangePuntaje').val(70); $('#puntajeLabel').text('70');
    new bootstrap.Modal(document.getElementById('modalElegible')).show();
  });

  bindFormAjax('#formElegible', '#modalElegible', 'Proyecto declarado Elegible.', 'elegibles');

  // ── NO ELEGIBLE ──────────────────────────────────────────────
  $(document).on('click', '.btn-no-elegible', function () {
    const id = $(this).data('id'), titulo = $(this).data('titulo');
    Swal.fire({
      title: 'Declarar No Elegible',
      html: `<div class="text-start"><p class="fw-semibold mb-3">"${titulo}"</p>
        <label class="form-label fw-semibold">Observación <span class="text-danger">*</span></label>
        <textarea id="swal-obs" class="swal2-textarea w-100" rows="3" placeholder="Explica el motivo..."></textarea></div>`,
      icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#d33', confirmButtonText: 'Confirmar', cancelButtonText: 'Cancelar',
      preConfirm: () => {
        const obs = document.getElementById('swal-obs').value.trim();
        if (!obs) { Swal.showValidationMessage('La observación es obligatoria.'); return false; }
        return obs;
      }
    }).then(result => {
      if (!result.isConfirmed) return;
      $.ajax({
        url: '/buenas-practicas/' + id + '/no-elegible',
        type: 'POST',
        data: { _token: CSRF, _method: 'PATCH', observacion_comision: result.value },
      }).done(function() {
        loadCards();
        pulsoToast('Proyecto marcado como No Elegible.', 'warning');
      }).fail(function() { pulsoToast('Error al guardar.', 'error'); });
    });
  });

  // ── GANADOR UGEL (Nivel 1) ────────────────────────────────────
  $(document).on('click', '.btn-ganador-ugel', function () {
    const d = $(this).data();
    $('#formGanadorUgel').attr('action', '/buenas-practicas/' + d.id + '/ganador-ugel');
    $('#ganu_titulo').text(d.titulo);
    $('#ganu_participante').text('Participante: ' + (d.participante || '—'));
    new bootstrap.Modal(document.getElementById('modalGanadorUgel')).show();
  });

  bindFormAjax('#formGanadorUgel', '#modalGanadorUgel', '¡Ganador UGEL declarado!', 'concurso_ugel');

  // ── ENVIAR A CONCURSO EXTERNO (Nivel 2) ──────────────────────
  $(document).on('click', '.btn-enviar-externo', function () {
    const d = $(this).data();
    $('#formExterno').attr('action', '/buenas-practicas/' + d.id + '/externo');
    $('#ext_titulo').text(d.titulo);
    $('#ext_participante').text('Participante: ' + (d.participante || '—'));
    $('input[name="nivel_externo"]').prop('checked', false);
    $('label[for="nivelMinedu"], label[for="nivelDre"]').css({ borderColor: 'rgba(0,0,0,.1)', background: '' });
    new bootstrap.Modal(document.getElementById('modalExterno')).show();
  });

  bindFormAjax('#formExterno', '#modalExterno', 'Proyecto registrado en concurso externo.', 'concurso_ugel', function() {
    if (!$('input[name="nivel_externo"]:checked').val()) {
      pulsoToast('Selecciona el nivel del concurso externo.', 'warning'); return false;
    }
    return true;
  });

  $(document).on('change', 'input[name="nivel_externo"]', function () {
    const val = $(this).val();
    $('input[name="nivel_externo"]').each(function () {
      const $lbl = $('label[for="' + $(this).attr('id') + '"]');
      if ($(this).val() === val) $lbl.css({ borderColor: val === 'minedu' ? '#7c3aed' : '#4facfe', background: val === 'minedu' ? 'rgba(124,58,237,.06)' : 'rgba(79,172,254,.06)' });
      else $lbl.css({ borderColor: 'rgba(0,0,0,.1)', background: '' });
    });
  });

  // ── RESULTADO CONCURSO EXTERNO (Nivel 2) ─────────────────────
  $(document).on('click', '.btn-resultado-externo', function () {
    const d = $(this).data();
    $('#formResultadoExterno').attr('action', '/buenas-practicas/' + d.id + '/resultado-externo');
    $('#res_titulo').text(d.titulo);
    $('#res_nivel').text('Concurso: ' + (d.nivel === 'minedu' ? 'MINEDU' : 'DRE Huánuco'));
    $('input[name="gano_externo"]').prop('checked', false);
    new bootstrap.Modal(document.getElementById('modalResultadoExterno')).show();
  });

  bindFormAjax('#formResultadoExterno', '#modalResultadoExterno', 'Resultado del concurso externo registrado.', 'concurso_ugel');

  // ── EDITAR (prácticas institucionales) ──────────────────────
  $(document).on('click', '.btn-editar', function () {
    const d = $(this).data();
    $('#formEditar').attr('action', '/buenas-practicas/' + d.id);
    $('#edit_titulo').val(d.titulo);
    $('#edit_descripcion').val(d.descripcion);
    $('#edit_categoria').val(d.categoria);
    $('#edit_unidad').val(d.unidad).trigger('change');
    $('#edit_responsable').val(d.responsable).trigger('change');
    $('#edit_estado').val(d.estado);
    $('#edit_avance').val(d.avance);
    $('#edit_inicio').val(d.inicio);
    $('#edit_termino').val(d.termino);
    $('#edit_sgd').val(d.sgd);
    $('#edit_impacto').val(d.impacto);
    $('#edit_evidencias').val(d.evidencias);
    $('#edit_observaciones').val(d.observaciones);
    $('input[name="modulo"][value="' + d.modulo + '"]', '#modalEditar').prop('checked', true).trigger('change');
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
  });

  bindFormAjax('#formEditar', '#modalEditar', 'Práctica actualizada correctamente.', null);

  // ── REGISTRAR NUEVA PRÁCTICA ─────────────────────────────────
  bindFormAjax('#formNueva', '#modalNueva', 'Práctica institucional registrada.', 'practicas');

  // ── ELIMINAR ─────────────────────────────────────────────────
  $(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id'), titulo = $(this).data('titulo');
    pulsoConfirm({
      title: '¿Eliminar?',
      html: `<strong>${titulo}</strong><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
      type: 'warning', confirmText: 'Sí, eliminar', cancelText: 'Cancelar',
    }).then(ok => {
      if (!ok) return;
      $.ajax({ url: '/buenas-practicas/' + id, type: 'POST', data: { _token: CSRF, _method: 'DELETE' } })
      .done(function() { loadCards(); pulsoToast('Eliminado correctamente.', 'warning'); })
      .fail(function() { pulsoToast('Error al eliminar.', 'error'); });
    });
  });

  // ── Upload area (adjuntar proyecto) ──────────────────────────
  const uploadArea = document.getElementById('uploadArea');
  const archivoInput = document.getElementById('archivoProyecto');
  if (uploadArea && archivoInput) {
    archivoInput.addEventListener('change', function () {
      if (this.files && this.files[0]) {
        const nombre = this.files[0].name;
        const size   = (this.files[0].size / 1024 / 1024).toFixed(2);
        $('#uploadLabel').html('<span class="text-success fw-semibold"><i class="ti tabler-file-check me-1"></i>' + nombre + ' (' + size + ' MB)</span>');
        uploadArea.classList.add('has-file');
      }
    });
    uploadArea.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
    uploadArea.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
    uploadArea.addEventListener('drop', function(e) {
      e.preventDefault(); this.classList.remove('dragover');
      if (e.dataTransfer.files[0]) {
        archivoInput.files = e.dataTransfer.files;
        $(archivoInput).trigger('change');
      }
    });
    // Reset al cerrar modal
    $('#modalProponer').on('hidden.bs.modal', function() {
      archivoInput.value = '';
      $('#uploadLabel').text('o arrastra y suelta aquí');
      uploadArea.classList.remove('has-file','dragover');
    });
  }

  // Flash messages son manejados globalmente por el Toast del layout (contentNavbarLayout)

}, 200));
</script>
@endsection
