@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Reconocimientos - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
       'resources/assets/vendor/libs/select2/select2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
       'resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-style')
<style>
/* ── KPI Cards ───────────────────────────────────────────────── */
.kpi-card { border-radius: 14px; border: none; overflow: hidden; transition: transform .18s, box-shadow .18s; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.10); }
.kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; }
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1; }
.kpi-label { font-size: .72rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; opacity: .75; }
.kpi-sub { font-size: .78rem; font-weight: 600; }

/* ── Recognition Cards ───────────────────────────────────────── */
.rec-card { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); transition: transform .18s, box-shadow .18s; }
.rec-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,.10); }
.rec-card.is-sci        { border-left: 4px solid #7367f0; }
.rec-card.is-integridad { border-left: 4px solid #28c76f; }
.rec-card.is-practicas  { border-left: 4px solid #ff9f43; }
.rec-card.is-apoyo      { border-left: 4px solid #00cfe8; }

/* ── Podium Cards ────────────────────────────────────────────── */
.podium-card { border-radius: 16px; border: 1px solid rgba(0,0,0,.07); transition: transform .18s, box-shadow .18s; }
.podium-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.12); }
.podium-avatar { width: 72px; height: 72px; object-fit: cover; border-radius: 50%; border: 3px solid rgba(255,255,255,.9); box-shadow: 0 4px 12px rgba(0,0,0,.15); }

/* ── Modulo tabs ─────────────────────────────────────────────── */
.modulo-tab { border-radius: 10px !important; padding: .45rem 1.1rem !important; font-size: .83rem; font-weight: 600; transition: all .18s; }
.modulo-tab.active { box-shadow: 0 4px 14px rgba(0,0,0,.12); }

/* ── Filter bar ──────────────────────────────────────────────── */
.filter-bar { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); background: var(--bs-body-bg); }
.filter-bar .form-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6e6b7b; margin-bottom: .25rem; }

/* ── Progress thin ───────────────────────────────────────────── */
.progress-thin { height: 6px; border-radius: 3px; }

/* ── Empty state ─────────────────────────────────────────────── */
.empty-icon { width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; }

/* ── Ranking list ────────────────────────────────────────────── */
.rank-item { transition: background .15s; border-radius: 10px; }
.rank-item:hover { background: rgba(115,103,240,.05); }

/* ── Loading overlay ─────────────────────────────────────────── */
#reconocimientos-loading { display: none; }

/* ── Fix modal cortado por Vuexy layout-wrapper overflow:hidden ── */
body.modal-open .layout-wrapper { overflow: visible !important; }
#modalNuevoReconocimiento .modal-dialog,
#modalEditarReconocimiento .modal-dialog {
  max-height: 90vh;
}
#modalNuevoReconocimiento .modal-body,
#modalEditarReconocimiento .modal-body {
  overflow-y: auto !important;
  max-height: calc(90vh - 130px);
}

/* ── Rec cards grid — ocupa todo el ancho, siempre 2 cols ──── */
#rec-cards-container {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}
#rec-cards-container > div { width: 100% !important; }
@media (max-width: 767px) {
  #rec-cards-container { grid-template-columns: 1fr; }
}

/* ── Modulo option cards ─────────────────────────────────────── */
.modulo-option-card { user-select: none; }
.modulo-option-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.10); }
.modulo-option-card.selected { box-shadow: 0 4px 14px rgba(0,0,0,.12); transform: translateY(-1px); }
.modulo-option-card.selected[data-value="Control Interno"]    { border-color:#7367f0 !important; background:rgba(115,103,240,.12) !important; }
.modulo-option-card.selected[data-value="Modelo de Integridad"]{ border-color:#28c76f !important; background:rgba(40,199,111,.12) !important; }
.modulo-option-card.selected[data-value="Buenas Prácticas"]   { border-color:#ff9f43 !important; background:rgba(255,159,67,.12) !important; }
.modulo-option-card.selected[data-value="Apoyo Estratégico"]  { border-color:#00cfe8 !important; background:rgba(0,207,232,.12) !important; }
</style>
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home me-1" style="font-size:.85rem"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Reconocimientos</li>
  </ol>
</nav>

{{-- Header --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div class="d-flex align-items-start gap-3">
    <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#ff9f43,#ffcd4a);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 6px 18px rgba(255,159,67,.35)">
      <i class="ti tabler-trophy text-white" style="font-size:1.5rem"></i>
    </div>
    <div>
      <h4 class="mb-1 fw-bold">Reconocimientos</h4>
      <p class="text-muted mb-0 small">Celebramos el compromiso y resultados de quienes impulsan la mejora continua en el <strong>SCI</strong> y el <strong>Modelo de Integridad</strong>.</p>
    </div>
  </div>
  @can('reconocimientos.crear')
  <button class="btn btn-warning btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevoReconocimiento" style="border-radius:10px">
    <i class="ti tabler-plus"></i> Nuevo Reconocimiento
  </button>
  @endcan
</div>

{{-- KPIs --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="card kpi-card h-100">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="kpi-icon" style="background:rgba(115,103,240,.12)"><i class="ti tabler-trophy text-primary"></i></div>
        <div>
          <div class="kpi-value text-primary">{{ $stats['total_reconocidos'] }}</div>
          <div class="kpi-label">Total Reconocidos</div>
          <div class="kpi-sub text-muted mt-1"><span class="text-primary">{{ $stats['total_sci'] }}</span> SCI · <span class="text-success">{{ $stats['total_integridad'] }}</span> Integridad</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card kpi-card h-100">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="kpi-icon" style="background:rgba(40,199,111,.12)"><i class="ti tabler-chart-bar text-success"></i></div>
        <div>
          <div class="kpi-value text-success">{{ $stats['promedio_puntaje'] }}%</div>
          <div class="kpi-label">Promedio General</div>
          <div class="kpi-sub text-muted mt-1">Puntaje promedio del período</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card kpi-card h-100">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="kpi-icon" style="background:rgba(0,207,232,.12)"><i class="ti tabler-building-community text-info"></i></div>
        <div>
          <div class="kpi-value text-info">{{ $stats['unidades_destacadas'] }}</div>
          <div class="kpi-label">Unidades Destacadas</div>
          <div class="kpi-sub text-muted mt-1">Unidades con reconocimiento</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card kpi-card h-100">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="kpi-icon" style="background:rgba(255,159,67,.12)"><i class="ti tabler-calendar-event text-warning"></i></div>
        <div>
          <div class="fw-bold text-warning" style="font-size:1rem;line-height:1.2">Próx. Ceremonia</div>
          <div class="kpi-label">Reconocimiento</div>
          <div class="kpi-sub text-muted mt-1">{{ $stats['proxima_ceremonia'] }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Main Tabs: SCI / Integridad / Todos --}}
<ul class="nav nav-tabs mb-0" id="moduloTabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link modulo-tab {{ !$modulo ? 'active' : '' }}" href="#" data-modulo="" role="tab">
      <i class="ti tabler-layout-grid me-1"></i>Todos
      <span class="badge bg-primary rounded-pill ms-1 modulo-count-todos">{{ $trabajadores->count() }}</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link modulo-tab {{ $modulo === 'sci' ? 'active' : '' }}" href="#" data-modulo="sci" role="tab">
      <i class="ti tabler-shield-check me-1"></i>Sistema de Control Interno
      <span class="badge rounded-pill ms-1 modulo-count-sci" style="background:rgba(115,103,240,.15);color:#7367f0">{{ $stats['total_sci'] }}</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link modulo-tab {{ $modulo === 'integridad' ? 'active' : '' }}" href="#" data-modulo="integridad" role="tab">
      <i class="ti tabler-star me-1"></i>Modelo de Integridad
      <span class="badge rounded-pill ms-1 modulo-count-integridad" style="background:rgba(40,199,111,.15);color:#28c76f">{{ $stats['total_integridad'] }}</span>
    </a>
  </li>
  <li class="nav-item ms-auto">
    <a class="nav-link modulo-tab" href="#tab-criterios" data-bs-toggle="tab">
      <i class="ti tabler-info-circle me-1"></i>Criterios
    </a>
  </li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="tab-principal">

  {{-- Filter Bar --}}
  <div class="card filter-bar mb-4 mt-0" style="border-radius:0 14px 14px 14px">
    <div class="card-body py-3">
      <div class="row g-3 align-items-end">
        <div class="col-6 col-md-2">
          <label class="form-label">Año</label>
          <select id="f-anio" class="form-select form-select-sm">
            @foreach($anios as $a)
            <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label">Mes</label>
          <select id="f-mes" class="form-select form-select-sm">
            <option value="">Año completo</option>
            @foreach($meses as $m => $nm)
            <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ $nm }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Categoría</label>
          <select id="f-categoria" class="form-select form-select-sm">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $cat)
            <option value="{{ $cat }}" {{ $categoria == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Buscar servidor/a</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="ti tabler-search"></i></span>
            <input type="text" id="f-buscar" class="form-control" placeholder="Nombre o cargo...">
          </div>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button id="btn-limpiar" class="btn btn-label-secondary btn-sm w-100" style="border-radius:8px">
            <i class="ti tabler-x me-1"></i>Limpiar
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- Columna principal --}}
    <div class="col-12">

      {{-- Podio Top Reconocimientos --}}
      <div class="card mb-4" style="border-radius:14px">
        <div class="card-header d-flex align-items-center justify-content-between" style="border-radius:14px 14px 0 0">
          <h6 class="mb-0 fw-bold"><i class="ti tabler-award me-2 text-warning"></i>Destacados del Período</h6>
          <span class="badge bg-label-warning text-warning" id="podio-periodo-label">
            {{ $mes ? ($meses[(int)$mes] ?? '') . ' ' . $anio : 'Año ' . $anio }}
          </span>
        </div>
        <div class="card-body">
          <div id="podio-container" class="row g-3">
            @php
              $podioColors = ['warning','secondary','danger','info'];
              $podioIcons  = ['tabler-medal','tabler-medal-2','tabler-medal-2','tabler-star'];
              $podioLabels = ['1er Lugar','2do Lugar','3er Lugar','Mención'];
            @endphp
            @foreach($top3 as $idx => $t)
            @php
              $pc = $podioColors[$idx] ?? 'info';
              $pi = $podioIcons[$idx]  ?? 'tabler-star';
              $pl = $podioLabels[$idx] ?? 'Mención';
              $catClass = match($t->categoria) {
                'Control Interno'       => 'is-sci',
                'Modelo de Integridad'  => 'is-integridad',
                'Buenas Prácticas'      => 'is-practicas',
                default                 => 'is-apoyo',
              };
            @endphp
            <div class="col-6 col-xl-3">
              <div class="card podium-card rec-card {{ $catClass }} h-100 text-center p-0">
                <div class="card-body p-3 d-flex flex-column align-items-center">
                  <span class="badge bg-label-{{ $pc }} rounded-pill mb-2 px-2 py-1" style="font-size:10px">
                    <i class="ti {{ $pi }} me-1"></i>{{ $pl }}
                  </span>
                  <img src="{{ $t->foto_url }}" alt="{{ $t->nombre }}" class="podium-avatar mb-2">
                  <div class="fw-bold text-center" style="font-size:12.5px;line-height:1.35">{{ $t->nombre }}</div>
                  <div class="text-muted mb-1" style="font-size:11px">{{ $t->cargo }}</div>
                  <span class="badge bg-label-secondary mb-2" style="font-size:10px">{{ $t->unidadOrganica?->sigla ?? '—' }}</span>
                  @if($t->categoria)
                  <span class="badge mb-2" style="font-size:10px;background:{{ match($t->categoria) { 'Control Interno'=>'rgba(115,103,240,.15)', 'Modelo de Integridad'=>'rgba(40,199,111,.15)', default=>'rgba(0,207,232,.15)' } }};color:{{ match($t->categoria) { 'Control Interno'=>'#7367f0', 'Modelo de Integridad'=>'#28c76f', default=>'#00cfe8' } }}">
                    {{ $t->categoria }}
                  </span>
                  @endif
                  <div class="mt-auto pt-2 w-100">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="text-muted" style="font-size:10px">Puntaje</span>
                      <span class="fw-bold text-{{ $t->nivel_color }}" style="font-size:14px">{{ number_format($t->puntaje_total,1) }}</span>
                    </div>
                    <div class="progress progress-thin mb-2">
                      <div class="progress-bar bg-{{ $t->nivel_color }}" style="width:{{ $t->puntaje_total }}%"></div>
                    </div>
                    <a href="{{ route('rep-reconocimientos.show', $t) }}" class="btn btn-sm btn-label-{{ $pc }} w-100 py-1" style="border-radius:8px;font-size:11px">
                      Ver detalle <i class="ti tabler-arrow-right ms-1"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
            @for($i = $top3->count(); $i < 4; $i++)
            @php $pc = $podioColors[$i]; $pl = $podioLabels[$i]; @endphp
            <div class="col-6 col-xl-3">
              <div class="card podium-card h-100 text-center" style="border:1px dashed rgba(0,0,0,.12)">
                <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center" style="min-height:200px">
                  <div class="empty-icon mb-2" style="background:rgba(0,0,0,.04);color:#a8aaae;width:56px;height:56px;font-size:1.3rem">
                    <i class="ti tabler-award-off"></i>
                  </div>
                  <span class="badge bg-label-{{ $pc }} rounded-pill mb-2" style="font-size:10px">{{ $pl }}</span>
                  <div class="text-muted" style="font-size:12px">Sin registro</div>
                </div>
              </div>
            </div>
            @endfor
          </div>
        </div>
      </div>

      {{-- Lista de Reconocimientos (dinámica) --}}
      <div class="card" style="border-radius:14px">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h6 class="mb-0 fw-bold"><i class="ti tabler-list me-2 text-primary"></i>Todos los Reconocimientos</h6>
          <div class="d-flex align-items-center gap-2">
            <span id="resultado-count" class="badge bg-label-primary">{{ $trabajadores->count() }} registros</span>
            <div id="reconocimientos-loading">
              <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
          </div>
        </div>
        <div id="reconocimientos-grid" class="card-body p-3">
          <div id="rec-cards-container">
            @forelse($trabajadores as $t)
            @php
              $catClass = match($t->categoria) {
                'Control Interno'       => 'is-sci',
                'Modelo de Integridad'  => 'is-integridad',
                'Buenas Prácticas'      => 'is-practicas',
                default                 => 'is-apoyo',
              };
            @endphp
            <div>
              <div class="card rec-card {{ $catClass }} h-100">
                <div class="card-body p-3">
                  <div class="d-flex align-items-start gap-3 mb-3">
                    <img src="{{ $t->foto_url }}" class="rounded-circle flex-shrink-0" style="width:46px;height:46px;object-fit:cover;border:2px solid rgba(0,0,0,.08)" alt="{{ $t->nombre }}">
                    <div class="flex-grow-1 min-width-0">
                      <div class="fw-bold text-truncate" style="font-size:13.5px">{{ $t->nombre }}</div>
                      <div class="text-muted text-truncate" style="font-size:11px">{{ $t->cargo ?? '—' }}</div>
                      <div class="d-flex flex-wrap gap-1 mt-1">
                        <span class="badge bg-label-secondary" style="font-size:10px">{{ $t->unidadOrganica?->sigla ?? '—' }}</span>
                        @if($t->categoria)
                        <span class="badge" style="font-size:10px;background:{{ match($t->categoria) { 'Control Interno'=>'rgba(115,103,240,.15)', 'Modelo de Integridad'=>'rgba(40,199,111,.15)', 'Buenas Prácticas'=>'rgba(255,159,67,.15)', default=>'rgba(0,207,232,.15)' } }};color:{{ match($t->categoria) { 'Control Interno'=>'#7367f0', 'Modelo de Integridad'=>'#28c76f', 'Buenas Prácticas'=>'#ff9f43', default=>'#00cfe8' } }}">
                          {{ $t->categoria }}
                        </span>
                        @endif
                      </div>
                    </div>
                    <div class="text-end flex-shrink-0">
                      <div class="fw-bold text-{{ $t->nivel_color }}" style="font-size:1.4rem;line-height:1">{{ number_format($t->puntaje_total,1) }}</div>
                      <div class="text-muted" style="font-size:10px">/ 100</div>
                      <span class="badge bg-{{ $t->nivel_color }} mt-1" style="font-size:10px">{{ $t->nivel }}</span>
                    </div>
                  </div>
                  {{-- Indicadores --}}
                  <div class="row g-2 mb-3" style="font-size:11px">
                    <div class="col-6">
                      <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Cumplimiento</span>
                        <span class="fw-semibold">{{ $t->puntaje_cumplimiento }}%</span>
                      </div>
                      <div class="progress progress-thin"><div class="progress-bar bg-success" style="width:{{ $t->puntaje_cumplimiento }}%"></div></div>
                    </div>
                    <div class="col-6">
                      <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Puntualidad</span>
                        <span class="fw-semibold">{{ $t->puntaje_puntualidad }}%</span>
                      </div>
                      <div class="progress progress-thin"><div class="progress-bar bg-primary" style="width:{{ $t->puntaje_puntualidad }}%"></div></div>
                    </div>
                    <div class="col-6">
                      <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Participación</span>
                        <span class="fw-semibold">{{ $t->puntaje_participacion }}%</span>
                      </div>
                      <div class="progress progress-thin"><div class="progress-bar bg-warning" style="width:{{ $t->puntaje_participacion }}%"></div></div>
                    </div>
                    <div class="col-6">
                      <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Responsabilidad</span>
                        <span class="fw-semibold">{{ $t->puntaje_responsabilidad }}%</span>
                      </div>
                      <div class="progress progress-thin"><div class="progress-bar bg-info" style="width:{{ $t->puntaje_responsabilidad }}%"></div></div>
                    </div>
                  </div>
                  {{-- Motivo --}}
                  @if($t->motivo)
                  <p class="text-muted mb-3" style="font-size:11px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden"><i class="ti tabler-quote me-1"></i>{{ $t->motivo }}</p>
                  @endif
                  {{-- Acciones --}}
                  <div class="d-flex gap-2">
                    <a href="{{ route('rep-reconocimientos.show', $t) }}" class="btn btn-sm btn-label-primary flex-grow-1" style="border-radius:8px;font-size:11px">
                      <i class="ti tabler-eye me-1"></i>Ver detalle
                    </a>
                    @can('reconocimientos.editar')
                    <button class="btn btn-sm btn-label-secondary btn-editar-reconocimiento" style="border-radius:8px"
                      data-id="{{ $t->id }}" data-user="{{ $t->user_id }}"
                      data-nombre="{{ $t->nombre }}" data-cargo="{{ $t->cargo }}"
                      data-unidad="{{ $t->unidad_organica_id }}" data-dni="{{ $t->dni }}"
                      data-correo="{{ $t->correo }}" data-cumplimiento="{{ $t->puntaje_cumplimiento }}"
                      data-puntualidad="{{ $t->puntaje_puntualidad }}" data-participacion="{{ $t->puntaje_participacion }}"
                      data-responsabilidad="{{ $t->puntaje_responsabilidad }}" data-categoria="{{ $t->categoria }}"
                      data-motivo="{{ $t->motivo }}" data-resolucion="{{ $t->numero_resolucion }}" title="Editar">
                      <i class="ti tabler-edit"></i>
                    </button>
                    @endcan
                    @can('reconocimientos.eliminar')
                    <form method="POST" action="{{ route('rep-reconocimientos.destroy', $t) }}" class="form-eliminar-rec d-inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-label-danger" style="border-radius:8px" title="Eliminar">
                        <i class="ti tabler-trash"></i>
                      </button>
                    </form>
                    @endcan
                  </div>
                </div>
              </div>
            </div>
            @empty
            <div style="grid-column:1/-1">
              <div class="text-center py-5">
                <div class="empty-icon mx-auto mb-3" style="background:rgba(0,0,0,.04);color:#a8aaae">
                  <i class="ti tabler-trophy-off"></i>
                </div>
                <h6 class="text-muted mb-1">Sin reconocimientos</h6>
                <p class="text-muted small mb-0">No hay reconocimientos para el período seleccionado.</p>
              </div>
            </div>
            @endforelse
          </div>
          {{-- Paginación dinámica --}}
          <div id="rec-pagination" class="px-3 pt-3 pb-3"></div>
        </div>
      </div>

    </div>{{-- col-12 --}}
  </div>{{-- row --}}

</div>{{-- tab-pane principal --}}

{{-- TAB CRITERIOS --}}
<div class="tab-pane fade" id="tab-criterios">
  <div class="card mt-3" style="border-radius:14px">
    <div class="card-header">
      <h5 class="mb-0 fw-bold">Criterios de Evaluación</h5>
    </div>
    <div class="card-body">
      <p class="text-muted mb-4">Los reconocimientos se otorgan en base a los siguientes indicadores institucionales:</p>
      <div class="row g-4">
        <div class="col-md-6 col-xl-3">
          <div class="card border text-center h-100" style="border-radius:14px">
            <div class="card-body p-4 d-flex flex-column align-items-center">
              <div style="width:56px;height:56px;border-radius:14px;background:rgba(40,199,111,.12);display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1rem">
                <i class="ti tabler-check text-success"></i>
              </div>
              <h6 class="fw-bold mb-2">Cumplimiento</h6>
              <p class="text-muted small mb-3 flex-grow-1">Nivel de cumplimiento de actividades y compromisos asignados.</p>
              <span class="badge bg-label-success fs-6">40%</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card border text-center h-100" style="border-radius:14px">
            <div class="card-body p-4 d-flex flex-column align-items-center">
              <div style="width:56px;height:56px;border-radius:14px;background:rgba(115,103,240,.12);display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1rem">
                <i class="ti tabler-clock text-primary"></i>
              </div>
              <h6 class="fw-bold mb-2">Puntualidad</h6>
              <p class="text-muted small mb-3 flex-grow-1">Entrega oportuna de informes, evidencias y reportes solicitados.</p>
              <span class="badge bg-label-primary fs-6">25%</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card border text-center h-100" style="border-radius:14px">
            <div class="card-body p-4 d-flex flex-column align-items-center">
              <div style="width:56px;height:56px;border-radius:14px;background:rgba(255,159,67,.12);display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1rem">
                <i class="ti tabler-users text-warning"></i>
              </div>
              <h6 class="fw-bold mb-2">Participación</h6>
              <p class="text-muted small mb-3 flex-grow-1">Participación activa en capacitaciones, talleres y reuniones institucionales.</p>
              <span class="badge bg-label-warning fs-6">20%</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card border text-center h-100" style="border-radius:14px">
            <div class="card-body p-4 d-flex flex-column align-items-center">
              <div style="width:56px;height:56px;border-radius:14px;background:rgba(0,207,232,.12);display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1rem">
                <i class="ti tabler-shield-check text-info"></i>
              </div>
              <h6 class="fw-bold mb-2">Responsabilidad</h6>
              <p class="text-muted small mb-3 flex-grow-1">Compromiso con la integridad institucional y buenas prácticas de gestión.</p>
              <span class="badge bg-label-info fs-6">15%</span>
            </div>
          </div>
        </div>
      </div>
      <div class="alert alert-primary mt-4 mb-0" style="border-radius:12px">
        <div class="d-flex align-items-start gap-2">
          <i class="ti tabler-info-circle flex-shrink-0 mt-1"></i>
          <div>
            <strong>Niveles de Reconocimiento:</strong>
            <div class="d-flex flex-wrap gap-2 mt-2">
              <span class="badge bg-success px-3 py-2">Excelente (90–100%)</span>
              <span class="badge bg-primary px-3 py-2">Bueno (75–89%)</span>
              <span class="badge bg-warning px-3 py-2">Regular (60–74%)</span>
              <span class="badge bg-danger px-3 py-2">En riesgo (&lt;60%)</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</div>{{-- tab-content --}}

{{-- ═══════════════════ MODAL NUEVO RECONOCIMIENTO ═══════════════════ --}}
<div class="modal fade" id="modalNuevoReconocimiento" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:16px">
      <form method="POST" action="{{ route('rep-reconocimientos.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header" style="border-radius:16px 16px 0 0">
          <h5 class="modal-title fw-bold"><i class="ti tabler-trophy me-2 text-warning"></i>Nueva Propuesta de Reconocimiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          {{-- Selección de usuario --}}
          <div class="p-3 mb-3 rounded-3" style="background:rgba(115,103,240,.06);border:1px solid rgba(115,103,240,.15)">
            <label class="form-label fw-bold text-primary"><i class="ti tabler-user-search me-1"></i>Vincular usuario del sistema</label>
            <select name="user_id" class="form-select select2-usuario-nuevo" id="nuevo_user_id">
              <option value="">— Buscar usuario del sistema —</option>
              @foreach($usuarios as $usr)
              <option value="{{ $usr->id }}"
                data-nombre="{{ $usr->name }}"
                data-correo="{{ $usr->email }}"
                data-dni="{{ $usr->dni ?? '' }}"
                data-cargo="{{ $usr->cargo?->nombre ?? '' }}"
                data-unidad="{{ $usr->unidad_organica_id ?? '' }}"
                data-foto="{{ $usr->profile_photo_url }}">
                {{ $usr->name }} · {{ $usr->email }}
              </option>
              @endforeach
            </select>
            <div class="form-text"><i class="ti tabler-info-circle me-1"></i>Al seleccionar un usuario, todos sus datos se cargan automáticamente.</div>
          </div>

          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="nuevo_nombre" class="form-control" required placeholder="Nombres y apellidos completos">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">DNI</label>
              <input type="text" name="dni" id="nuevo_dni" class="form-control" maxlength="8" placeholder="12345678">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Cargo</label>
              <select name="cargo" class="form-select select2-cargo-nuevo" id="nuevo_cargo">
                <option value="">Sin cargo</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Correo institucional</label>
              <input type="email" name="correo" id="nuevo_correo" class="form-control" placeholder="servidor@ugel.gob.pe">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" id="nuevo_unidad" class="form-select select2-unidad-nuevo">
                <option value="">Seleccionar unidad</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Módulo de Reconocimiento <span class="text-danger">*</span></label>
              <input type="hidden" name="categoria" id="nuevo_categoria" required>
              <div class="d-flex gap-3" id="nuevo-modulo-cards">
                <div class="modulo-option-card flex-fill p-3 rounded-3 text-center" data-value="Control Interno" data-target="nuevo_categoria"
                  style="border:2px solid rgba(115,103,240,.3);background:rgba(115,103,240,.05);cursor:pointer;transition:all .18s">
                  <div style="width:44px;height:44px;border-radius:12px;background:rgba(115,103,240,.15);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem">
                    <i class="ti tabler-shield-check text-primary" style="font-size:1.3rem"></i>
                  </div>
                  <div class="fw-bold mb-1" style="font-size:13px;color:#7367f0">Control Interno</div>
                  <div class="text-muted" style="font-size:11px;line-height:1.4">Sistema de Control Interno (SCI)</div>
                </div>
                <div class="modulo-option-card flex-fill p-3 rounded-3 text-center" data-value="Modelo de Integridad" data-target="nuevo_categoria"
                  style="border:2px solid rgba(40,199,111,.3);background:rgba(40,199,111,.05);cursor:pointer;transition:all .18s">
                  <div style="width:44px;height:44px;border-radius:12px;background:rgba(40,199,111,.15);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem">
                    <i class="ti tabler-star text-success" style="font-size:1.3rem"></i>
                  </div>
                  <div class="fw-bold mb-1" style="font-size:13px;color:#28c76f">Modelo de Integridad</div>
                  <div class="text-muted" style="font-size:11px;line-height:1.4">Ética y transparencia institucional</div>
                </div>
              </div>
              <div class="form-text text-danger d-none" id="nuevo-categoria-error"><i class="ti tabler-alert-circle me-1"></i>Selecciona el módulo de reconocimiento.</div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Año <span class="text-danger">*</span></label>
              <select name="anio" class="form-select">
                @foreach($anios as $a)
                <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Mes</label>
              <select name="mes" class="form-select">
                <option value="">Anual</option>
                @foreach($meses as $m => $nm)
                <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ $nm }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">N° Resolución Directoral</label>
              <input type="text" name="numero_resolucion" class="form-control" placeholder="RD N° 1457-2025">
            </div>

            <div class="col-12"><hr class="my-1"><h6 class="text-primary mb-0"><i class="ti tabler-chart-bar me-1"></i>Indicadores de Evaluación <small class="text-muted fw-normal">(0 – 100)</small></h6></div>

            <div class="col-md-3">
              <label class="form-label">Cumplimiento <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" name="puntaje_cumplimiento" id="nuevo_cumplimiento" class="form-control" min="0" max="100" value="0" required>
                <span class="input-group-text text-muted">%</span>
              </div>
              <div class="progress progress-thin mt-1"><div class="progress-bar bg-success" id="bar-nuevo-cumplimiento" style="width:0%"></div></div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Puntualidad <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" name="puntaje_puntualidad" id="nuevo_puntualidad" class="form-control" min="0" max="100" value="0" required>
                <span class="input-group-text text-muted">%</span>
              </div>
              <div class="progress progress-thin mt-1"><div class="progress-bar bg-primary" id="bar-nuevo-puntualidad" style="width:0%"></div></div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Participación <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" name="puntaje_participacion" id="nuevo_participacion" class="form-control" min="0" max="100" value="0" required>
                <span class="input-group-text text-muted">%</span>
              </div>
              <div class="progress progress-thin mt-1"><div class="progress-bar bg-warning" id="bar-nuevo-participacion" style="width:0%"></div></div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Responsabilidad <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" name="puntaje_responsabilidad" id="nuevo_responsabilidad" class="form-control" min="0" max="100" value="0" required>
                <span class="input-group-text text-muted">%</span>
              </div>
              <div class="progress progress-thin mt-1"><div class="progress-bar bg-info" id="bar-nuevo-responsabilidad" style="width:0%"></div></div>
            </div>

            {{-- Puntaje calculado en vivo --}}
            <div class="col-12">
              <div class="d-flex align-items-center justify-content-end gap-2 p-2 rounded-3" style="background:rgba(0,0,0,.03)">
                <span class="text-muted small">Puntaje total estimado:</span>
                <span id="nuevo-puntaje-total" class="fw-bold text-primary fs-5">0.0</span>
                <span class="text-muted small">/ 100</span>
              </div>
            </div>

            <div class="col-md-6" id="nuevo-foto-wrap">
              <label class="form-label fw-semibold">Foto del servidor/a</label>
              <div id="nuevo-foto-preview" class="mb-2 d-none">
                <img id="nuevo-foto-img" src="" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;border:2px solid rgba(0,0,0,.1)" alt="">
                <span class="ms-2 text-muted small">Foto cargada del perfil</span>
                <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" id="nuevo-foto-clear">Cambiar</button>
              </div>
              <div id="nuevo-foto-input">
                <input type="file" name="foto" id="nuevo_foto_file" class="form-control" accept="image/*">
                <div class="form-text">JPG/PNG. Máx. 2MB.</div>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Documento de reconocimiento (PDF)</label>
              <input type="file" name="resolucion_archivo" class="form-control" accept=".pdf">
              <div class="form-text">RD, certificado, constancia. Máx. 5MB.</div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Motivo / Justificación</label>
              <textarea name="motivo" class="form-control" rows="3" placeholder="Descripción de logros y contribuciones..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" style="border-radius:8px">Cancelar</button>
          <button type="submit" class="btn btn-warning fw-bold" style="border-radius:8px"><i class="ti tabler-trophy me-1"></i>Registrar Reconocimiento</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ═══════════════════ MODAL EDITAR RECONOCIMIENTO ═══════════════════ --}}
<div class="modal fade" id="modalEditarReconocimiento" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:16px">
      <form method="POST" id="formEditarRec" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title fw-bold"><i class="ti tabler-edit me-2 text-primary"></i>Editar Reconocimiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <div class="p-3 rounded-3" style="background:rgba(115,103,240,.06);border:1px solid rgba(115,103,240,.15)">
                <label class="form-label fw-bold text-primary"><i class="ti tabler-user-search me-1"></i>Usuario del sistema vinculado</label>
                <select name="user_id" id="rec_user_id" class="form-select select2-usuario-editar">
                  <option value="">— Sin vincular —</option>
                  @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}"
                    data-nombre="{{ $usr->name }}"
                    data-correo="{{ $usr->email }}"
                    data-dni="{{ $usr->dni ?? '' }}"
                    data-cargo="{{ $usr->cargo?->nombre ?? '' }}"
                    data-unidad="{{ $usr->unidad_organica_id ?? '' }}"
                    data-foto="{{ $usr->profile_photo_url }}">
                    {{ $usr->name }} · {{ $usr->email }}
                  </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-8">
              <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="rec_nombre" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">DNI</label>
              <input type="text" name="dni" id="rec_dni" class="form-control" maxlength="8">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Cargo</label>
              <select name="cargo" id="rec_cargo" class="form-select select2-cargo-editar">
                <option value="">Sin cargo</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Correo</label>
              <input type="email" name="correo" id="rec_correo" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" id="rec_unidad" class="form-select select2-unidad-editar">
                <option value="">Seleccionar unidad...</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Módulo de Reconocimiento <span class="text-danger">*</span></label>
              <input type="hidden" name="categoria" id="rec_categoria">
              <div class="d-flex gap-3" id="rec-modulo-cards">
                <div class="modulo-option-card flex-fill p-3 rounded-3 text-center" data-value="Control Interno" data-target="rec_categoria"
                  style="border:2px solid rgba(115,103,240,.3);background:rgba(115,103,240,.05);cursor:pointer;transition:all .18s">
                  <div style="width:44px;height:44px;border-radius:12px;background:rgba(115,103,240,.15);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem">
                    <i class="ti tabler-shield-check text-primary" style="font-size:1.3rem"></i>
                  </div>
                  <div class="fw-bold mb-1" style="font-size:13px;color:#7367f0">Control Interno</div>
                  <div class="text-muted" style="font-size:11px;line-height:1.4">Sistema de Control Interno (SCI)</div>
                </div>
                <div class="modulo-option-card flex-fill p-3 rounded-3 text-center" data-value="Modelo de Integridad" data-target="rec_categoria"
                  style="border:2px solid rgba(40,199,111,.3);background:rgba(40,199,111,.05);cursor:pointer;transition:all .18s">
                  <div style="width:44px;height:44px;border-radius:12px;background:rgba(40,199,111,.15);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem">
                    <i class="ti tabler-star text-success" style="font-size:1.3rem"></i>
                  </div>
                  <div class="fw-bold mb-1" style="font-size:13px;color:#28c76f">Modelo de Integridad</div>
                  <div class="text-muted" style="font-size:11px;line-height:1.4">Ética y transparencia institucional</div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">N° / Referencia del documento</label>
              <input type="text" name="numero_resolucion" id="rec_resolucion" class="form-control" placeholder="RD, certificado, constancia...">
            </div>
            <div class="col-12"><hr class="my-1"><h6 class="text-primary mb-0"><i class="ti tabler-chart-bar me-1"></i>Indicadores de Evaluación <small class="text-muted fw-normal">(0 – 100)</small></h6></div>
            <div class="col-md-3">
              <label class="form-label">Cumplimiento</label>
              <div class="input-group">
                <input type="number" name="puntaje_cumplimiento" id="rec_cumplimiento" class="form-control" min="0" max="100">
                <span class="input-group-text text-muted">%</span>
              </div>
              <div class="progress progress-thin mt-1"><div class="progress-bar bg-success" id="bar-rec-cumplimiento" style="width:0%"></div></div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Puntualidad</label>
              <div class="input-group">
                <input type="number" name="puntaje_puntualidad" id="rec_puntualidad" class="form-control" min="0" max="100">
                <span class="input-group-text text-muted">%</span>
              </div>
              <div class="progress progress-thin mt-1"><div class="progress-bar bg-primary" id="bar-rec-puntualidad" style="width:0%"></div></div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Participación</label>
              <div class="input-group">
                <input type="number" name="puntaje_participacion" id="rec_participacion" class="form-control" min="0" max="100">
                <span class="input-group-text text-muted">%</span>
              </div>
              <div class="progress progress-thin mt-1"><div class="progress-bar bg-warning" id="bar-rec-participacion" style="width:0%"></div></div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Responsabilidad</label>
              <div class="input-group">
                <input type="number" name="puntaje_responsabilidad" id="rec_responsabilidad" class="form-control" min="0" max="100">
                <span class="input-group-text text-muted">%</span>
              </div>
              <div class="progress progress-thin mt-1"><div class="progress-bar bg-info" id="bar-rec-responsabilidad" style="width:0%"></div></div>
            </div>
            <div class="col-12">
              <div class="d-flex align-items-center justify-content-end gap-2 p-2 rounded-3" style="background:rgba(0,0,0,.03)">
                <span class="text-muted small">Puntaje total:</span>
                <span id="rec-puntaje-total" class="fw-bold text-primary fs-5">0.0</span>
                <span class="text-muted small">/ 100</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nueva foto (opcional)</label>
              <input type="file" name="foto" class="form-control" accept="image/*">
              <div class="form-text">Dejar vacío para conservar la actual.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Documento de reconocimiento (PDF)</label>
              <input type="file" name="resolucion_archivo" class="form-control" accept=".pdf">
              <div class="form-text">RD, certificado, constancia. Máx. 5MB.</div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Motivo</label>
              <textarea name="motivo" id="rec_motivo" class="form-control" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" style="border-radius:8px">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-bold" style="border-radius:8px"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  const AJAX_URL    = '{{ route("rep-reconocimientos.ajax") }}';
  const USUARIO_URL = '{{ url("/reconocimientos-usuario") }}';

  /* ── Utilidades ──────────────────────────────────────────────────── */
  const catColorMap = {
    'Control Interno':      { bg: 'rgba(115,103,240,.15)', text: '#7367f0', cls: 'is-sci' },
    'Modelo de Integridad': { bg: 'rgba(40,199,111,.15)',  text: '#28c76f', cls: 'is-integridad' },
    'Buenas Prácticas':     { bg: 'rgba(255,159,67,.15)',  text: '#ff9f43', cls: 'is-practicas' },
    'Apoyo Estratégico':    { bg: 'rgba(0,207,232,.15)',   text: '#00cfe8', cls: 'is-apoyo' },
  };
  const nivelColorMap = { success:'#28c76f', primary:'#7367f0', warning:'#ff9f43', danger:'#ea5455' };

  /* ── Select2 inicialización ──────────────────────────────────────── */
  function initSelect2(sel, dropdownParent, opts = {}) {
    $(sel).select2({ dropdownParent: $(dropdownParent), width: '100%', ...opts });
  }
  function initCargosSelect2(sel, dropdownParent) {
    $(sel).select2({
      dropdownParent: $(dropdownParent),
      placeholder: 'Buscar o escribir cargo...',
      allowClear: true, tags: true, width: '100%',
      ajax: {
        url: '{{ route("cargos.index") }}', dataType: 'json', delay: 200,
        processResults: data => ({ results: data.map(c => ({ id: c.nombre, text: c.nombre })) }),
        cache: true,
      },
      createTag: p => { const t = $.trim(p.term); return t ? { id: t, text: t, newTag: true } : null; },
      templateResult: d => d.newTag ? $(`<span><i class="ti tabler-plus me-1 text-primary"></i>${d.text} <em class="text-muted">(nuevo)</em></span>`) : d.text,
    });
  }

  initSelect2('.select2-usuario-nuevo',  '#modalNuevoReconocimiento',  { placeholder: 'Buscar usuario...' });
  initSelect2('.select2-unidad-nuevo',   '#modalNuevoReconocimiento',  { placeholder: 'Seleccionar unidad...' });
  initSelect2('.select2-usuario-editar', '#modalEditarReconocimiento', { placeholder: 'Buscar usuario...' });
  initSelect2('.select2-unidad-editar',  '#modalEditarReconocimiento', { placeholder: 'Seleccionar unidad...' });
  initCargosSelect2('#nuevo_cargo',  '#modalNuevoReconocimiento');
  initCargosSelect2('#rec_cargo',    '#modalEditarReconocimiento');

  /* ── Foto preview al seleccionar usuario ────────────────────────── */
  function setFotoPreview(fotoUrl) {
    const preview = document.getElementById('nuevo-foto-preview');
    const input   = document.getElementById('nuevo-foto-input');
    const img     = document.getElementById('nuevo-foto-img');
    if (fotoUrl) {
      img.src = fotoUrl;
      preview.classList.remove('d-none');
      input.classList.add('d-none');
    } else {
      clearFotoPreview();
    }
  }
  function clearFotoPreview() {
    document.getElementById('nuevo-foto-preview').classList.add('d-none');
    document.getElementById('nuevo-foto-input').classList.remove('d-none');
    document.getElementById('nuevo_foto_file').value = '';
  }
  document.getElementById('nuevo-foto-clear')?.addEventListener('click', clearFotoPreview);

  /* ── Auto-fill al seleccionar usuario ───────────────────────────── */
  function fillFormFromOption(opt, prefix) {
    const nombre = opt.dataset.nombre || '';
    const correo = opt.dataset.correo || '';
    const dni    = opt.dataset.dni    || '';
    const cargo  = opt.dataset.cargo  || '';
    const unidad = opt.dataset.unidad || '';
    const foto   = opt.dataset.foto   || '';

    if (prefix === 'nuevo') {
      document.getElementById('nuevo_nombre').value = nombre;
      document.getElementById('nuevo_correo').value = correo;
      document.getElementById('nuevo_dni').value    = dni;
      const cargoSel = $('#nuevo_cargo');
      cargoSel.empty().append('<option value="">Sin cargo</option>');
      if (cargo) cargoSel.append(new Option(cargo, cargo, true, true));
      cargoSel.trigger('change');
      if (unidad) $('#nuevo_unidad').val(unidad).trigger('change');
      setFotoPreview(foto);
    } else {
      document.getElementById('rec_nombre').value = nombre;
      document.getElementById('rec_correo').value = correo;
      document.getElementById('rec_dni').value    = dni;
      const cargoSel = $('#rec_cargo');
      cargoSel.empty().append('<option value="">Sin cargo</option>');
      if (cargo) cargoSel.append(new Option(cargo, cargo, true, true));
      cargoSel.trigger('change');
    }
  }

  $('#nuevo_user_id').on('select2:select', function () {
    const opt = this.options[this.selectedIndex];
    fillFormFromOption(opt, 'nuevo');
  });
  $('#nuevo_user_id').on('select2:clear select2:unselect', function () {
    ['nuevo_nombre','nuevo_correo','nuevo_dni'].forEach(id => { document.getElementById(id).value = ''; });
    $('#nuevo_cargo').empty().append('<option value="">Sin cargo</option>').trigger('change');
    $('#nuevo_unidad').val('').trigger('change');
    clearFotoPreview();
  });

  $('#rec_user_id').on('select2:select', function () {
    const opt = this.options[this.selectedIndex];
    fillFormFromOption(opt, 'rec');
  });
  $('#rec_user_id').on('select2:clear select2:unselect', function () {
    ['rec_nombre','rec_correo','rec_dni'].forEach(id => { document.getElementById(id).value = ''; });
    $('#rec_cargo').empty().append('<option value="">Sin cargo</option>').trigger('change');
  });

  /* ── Progress bars dinámicos en modales ──────────────────────────── */
  function bindProgressBar(inputId, barId, totalId, allInputs) {
    const inp = document.getElementById(inputId);
    const bar = document.getElementById(barId);
    if (!inp || !bar) return;
    inp.addEventListener('input', () => {
      const v = Math.min(Math.max(parseInt(inp.value) || 0, 0), 100);
      bar.style.width = v + '%';
      if (totalId) updateTotal(allInputs, totalId);
    });
  }
  function updateTotal(inputIds, totalId) {
    const sum = inputIds.reduce((acc, id) => {
      const el = document.getElementById(id);
      return acc + (parseInt(el?.value) || 0);
    }, 0);
    const el = document.getElementById(totalId);
    if (el) el.textContent = (sum / inputIds.length).toFixed(1);
  }

  const nuevoInputs = ['nuevo_cumplimiento','nuevo_puntualidad','nuevo_participacion','nuevo_responsabilidad'];
  const recInputs   = ['rec_cumplimiento','rec_puntualidad','rec_participacion','rec_responsabilidad'];
  bindProgressBar('nuevo_cumplimiento',     'bar-nuevo-cumplimiento',     'nuevo-puntaje-total', nuevoInputs);
  bindProgressBar('nuevo_puntualidad',      'bar-nuevo-puntualidad',      'nuevo-puntaje-total', nuevoInputs);
  bindProgressBar('nuevo_participacion',    'bar-nuevo-participacion',    'nuevo-puntaje-total', nuevoInputs);
  bindProgressBar('nuevo_responsabilidad',  'bar-nuevo-responsabilidad',  'nuevo-puntaje-total', nuevoInputs);
  bindProgressBar('rec_cumplimiento',       'bar-rec-cumplimiento',       'rec-puntaje-total',   recInputs);
  bindProgressBar('rec_puntualidad',        'bar-rec-puntualidad',        'rec-puntaje-total',   recInputs);
  bindProgressBar('rec_participacion',      'bar-rec-participacion',      'rec-puntaje-total',   recInputs);
  bindProgressBar('rec_responsabilidad',    'bar-rec-responsabilidad',    'rec-puntaje-total',   recInputs);

  /* ── Editar reconocimiento ───────────────────────────────────────── */
  /* ── Editar / Eliminar con delegación (funciona con cards dinámicos) ─ */
  function openEditModal(btn) {
    const form = document.getElementById('formEditarRec');
    form.action = '/reconocimientos/' + btn.dataset.id;

    $('#rec_user_id').val(btn.dataset.user || '').trigger('change');
    document.getElementById('rec_nombre').value     = btn.dataset.nombre     || '';
    document.getElementById('rec_dni').value        = btn.dataset.dni        || '';
    document.getElementById('rec_correo').value     = btn.dataset.correo     || '';
    document.getElementById('rec_motivo').value     = btn.dataset.motivo     || '';
    document.getElementById('rec_resolucion').value = btn.dataset.resolucion || '';

    const cargoVal = btn.dataset.cargo || '';
    const recCargoSel = $('#rec_cargo');
    recCargoSel.empty().append('<option value="">Sin cargo</option>');
    if (cargoVal) recCargoSel.append(new Option(cargoVal, cargoVal, true, true));
    recCargoSel.trigger('change');

    if (btn.dataset.unidad) $('#rec_unidad').val(btn.dataset.unidad).trigger('change');

    selectModuloCard('rec_categoria', btn.dataset.categoria || '');

    ['cumplimiento','puntualidad','participacion','responsabilidad'].forEach(k => {
      const el  = document.getElementById('rec_' + k);
      const bar = document.getElementById('bar-rec-' + k);
      const val = parseFloat(btn.dataset[k]) || 0;
      if (el) el.value = val;
      if (bar) bar.style.width = val + '%';
    });
    updateTotal(recInputs, 'rec-puntaje-total');

    new bootstrap.Modal(document.getElementById('modalEditarReconocimiento')).show();
  }

  document.getElementById('rec-cards-container').addEventListener('click', function (e) {
    const editBtn = e.target.closest('.btn-editar-reconocimiento');
    if (editBtn) { openEditModal(editBtn); return; }

    const delBtn = e.target.closest('.btn-eliminar-reconocimiento');
    if (delBtn) {
      Swal.fire({
        title: '¿Eliminar reconocimiento?',
        html: `<span class="text-muted">Se eliminará el reconocimiento de <strong>${delBtn.dataset.nombre}</strong>. Esta acción no se puede deshacer.</span>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ea5455', cancelButtonColor: '#a8aaae',
        customClass: { popup: 'rounded-4' }
      }).then(r => {
        if (!r.isConfirmed) return;
        const f = document.createElement('form');
        f.method = 'POST'; f.action = delBtn.dataset.url;
        f.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
        document.body.appendChild(f);
        f.submit();
      });
    }
  });

  /* ── Tarjetas de módulo (selector visual de categoría) ──────────── */
  function initModuloCards() {
    document.querySelectorAll('.modulo-option-card').forEach(card => {
      card.addEventListener('click', function () {
        const targetId = this.dataset.target;
        const container = this.closest('.row');
        container.querySelectorAll('.modulo-option-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        const hidden = document.getElementById(targetId);
        if (hidden) {
          hidden.value = this.dataset.value;
          // Limpiar error si existía
          const errEl = document.getElementById(targetId.replace('_categoria','') + '-categoria-error');
          if (errEl) errEl.classList.add('d-none');
        }
      });
    });
  }
  initModuloCards();

  function selectModuloCard(targetId, value) {
    const hidden = document.getElementById(targetId);
    if (!hidden) return;
    hidden.value = value || '';
    const container = document.querySelector(`[data-target="${targetId}"]`)?.closest('.row');
    if (!container) return;
    container.querySelectorAll('.modulo-option-card').forEach(c => {
      c.classList.toggle('selected', c.dataset.value === value);
    });
  }

  /* ── Validación categoría al submit modal nuevo ──────────────────── */
  document.querySelector('#modalNuevoReconocimiento form').addEventListener('submit', function(e) {
    const cat = document.getElementById('nuevo_categoria').value;
    if (!cat) {
      e.preventDefault();
      const errEl = document.getElementById('nuevo-categoria-error');
      if (errEl) errEl.classList.remove('d-none');
      document.getElementById('nuevo-modulo-cards').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });

  /* ── Módulo Tabs (SCI / Integridad / Todos) ──────────────────────── */
  let currentModulo = '{{ $modulo ?? "" }}';
  document.querySelectorAll('.modulo-tab[data-modulo]').forEach(tab => {
    tab.addEventListener('click', function (e) {
      e.preventDefault();
      document.querySelectorAll('.modulo-tab[data-modulo]').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      currentModulo = this.dataset.modulo;
      fetchReconocimientos();
    });
  });

  /* ── Filtros en tiempo real ──────────────────────────────────────── */
  let debounceTimer;
  function onFilterChange() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchReconocimientos, 350);
  }
  document.getElementById('f-anio').addEventListener('change', fetchReconocimientos);
  document.getElementById('f-mes').addEventListener('change', fetchReconocimientos);
  document.getElementById('f-categoria').addEventListener('change', fetchReconocimientos);
  document.getElementById('f-buscar').addEventListener('input', onFilterChange);

  document.getElementById('btn-limpiar').addEventListener('click', () => {
    document.getElementById('f-mes').value = '';
    document.getElementById('f-categoria').value = '';
    document.getElementById('f-buscar').value = '';
    fetchReconocimientos();
  });

  /* ── Paginación client-side ──────────────────────────────────────── */
  const PER_PAGE = 8;
  let allItems   = [];
  let currentPage = 1;

  function renderPagination(total) {
    const pages = Math.ceil(total / PER_PAGE);
    const wrap  = document.getElementById('rec-pagination');
    if (!wrap) return;
    if (pages <= 1) { wrap.innerHTML = ''; return; }

    let html = `<nav aria-label="Paginación reconocimientos"><ul class="pagination pagination-sm justify-content-center mb-0 flex-wrap gap-1">`;

    // Anterior
    html += `<li class="page-item${currentPage === 1 ? ' disabled' : ''}">
      <button class="page-link rounded-2" data-page="${currentPage - 1}" style="border-radius:8px!important">
        <i class="ti tabler-chevron-left" style="font-size:13px"></i>
      </button></li>`;

    // Páginas
    for (let i = 1; i <= pages; i++) {
      const active = i === currentPage;
      html += `<li class="page-item${active ? ' active' : ''}">
        <button class="page-link rounded-2" data-page="${i}" style="border-radius:8px!important;min-width:36px">${i}</button>
      </li>`;
    }

    // Siguiente
    html += `<li class="page-item${currentPage === pages ? ' disabled' : ''}">
      <button class="page-link rounded-2" data-page="${currentPage + 1}" style="border-radius:8px!important">
        <i class="ti tabler-chevron-right" style="font-size:13px"></i>
      </button></li>`;

    html += `</ul></nav>
    <div class="text-center text-muted mt-2" style="font-size:12px">
      Mostrando ${Math.min((currentPage-1)*PER_PAGE+1, total)}–${Math.min(currentPage*PER_PAGE, total)} de ${total}
    </div>`;
    wrap.innerHTML = html;

    wrap.querySelectorAll('button[data-page]').forEach(btn => {
      btn.addEventListener('click', function () {
        const p = parseInt(this.dataset.page);
        if (p < 1 || p > pages) return;
        currentPage = p;
        renderCards(allItems);
        document.getElementById('reconocimientos-grid').scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });
  }

  /* ── AJAX fetch reconocimientos ──────────────────────────────────── */
  function fetchReconocimientos() {
    const params = new URLSearchParams({
      anio:      document.getElementById('f-anio').value,
      mes:       document.getElementById('f-mes').value,
      categoria: document.getElementById('f-categoria').value,
      buscar:    document.getElementById('f-buscar').value,
      modulo:    currentModulo,
    });
    document.getElementById('reconocimientos-loading').style.display = 'inline-block';
    currentPage = 1;

    fetch(AJAX_URL + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        allItems = data.trabajadores;
        renderCards(allItems);
        document.getElementById('resultado-count').textContent = data.stats.total + ' registros';
      })
      .catch(() => {})
      .finally(() => { document.getElementById('reconocimientos-loading').style.display = 'none'; });
  }

  function renderCards(items) {
    const container = document.getElementById('rec-cards-container');
    const total = items.length;
    const start = (currentPage - 1) * PER_PAGE;
    const page  = items.slice(start, start + PER_PAGE);
    renderPagination(total);

    if (!page.length) {
      container.innerHTML = `
        <div style="grid-column:1/-1">
          <div class="text-center py-5">
            <div class="empty-icon mx-auto mb-3" style="background:rgba(0,0,0,.04);color:#a8aaae;width:80px;height:80px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:2rem">
              <i class="ti tabler-trophy-off"></i>
            </div>
            <h6 class="text-muted mb-1">Sin reconocimientos</h6>
            <p class="text-muted small mb-0">No hay reconocimientos para los filtros seleccionados.</p>
          </div>
        </div>`;
      return;
    }

    container.innerHTML = page.map((t, idx) => {
      const cm   = catColorMap[t.categoria] || { bg:'rgba(168,170,174,.15)', text:'#a8aaae', cls:'' };
      const nc   = nivelColorMap[t.nivel_color] || '#a8aaae';
      const catBadge = t.categoria
        ? `<span class="badge" style="font-size:10px;background:${cm.bg};color:${cm.text}">${t.categoria}</span>`
        : '';
      return `
      <div>
        <div class="card rec-card ${cm.cls} h-100">
          <div class="card-body p-3">
            <div class="d-flex align-items-start gap-3 mb-3">
              <img src="${t.foto_url}" class="rounded-circle flex-shrink-0" style="width:46px;height:46px;object-fit:cover;border:2px solid rgba(0,0,0,.08)" alt="${t.nombre}">
              <div class="flex-grow-1 min-width-0">
                <div class="fw-bold text-truncate" style="font-size:13.5px">${t.nombre}</div>
                <div class="text-muted text-truncate" style="font-size:11px">${t.cargo || '—'}</div>
                <div class="d-flex flex-wrap gap-1 mt-1">
                  <span class="badge bg-label-secondary" style="font-size:10px">${t.unidad_sigla}</span>
                  ${catBadge}
                </div>
              </div>
              <div class="text-end flex-shrink-0">
                <div class="fw-bold" style="font-size:1.4rem;line-height:1;color:${nc}">${t.puntaje_total}</div>
                <div class="text-muted" style="font-size:10px">/ 100</div>
                <span class="badge mt-1" style="font-size:10px;background:${nc};color:#fff">${t.nivel}</span>
              </div>
            </div>
            <div class="row g-2 mb-3" style="font-size:11px">
              <div class="col-6">
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Cumplimiento</span><span class="fw-semibold">${t.cumplimiento}%</span></div>
                <div class="progress" style="height:6px;border-radius:3px"><div class="progress-bar bg-success" style="width:${t.cumplimiento}%"></div></div>
              </div>
              <div class="col-6">
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Nivel</span><span class="fw-semibold" style="color:${nc}">${t.nivel}</span></div>
                <div class="progress" style="height:6px;border-radius:3px"><div class="progress-bar" style="width:${t.puntaje_total}%;background:${nc}"></div></div>
              </div>
            </div>
            ${t.motivo ? `<p class="text-muted mb-3" style="font-size:11px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden"><i class="ti tabler-quote me-1"></i>${t.motivo}</p>` : ''}
            <div class="d-flex gap-2">
              <a href="${t.show_url}" class="btn btn-sm btn-label-primary flex-grow-1" style="border-radius:8px;font-size:11px">
                <i class="ti tabler-eye me-1"></i>Ver detalle
              </a>
              @can('reconocimientos.editar')
              <button class="btn btn-sm btn-label-secondary btn-editar-reconocimiento" style="border-radius:8px" title="Editar"
                data-id="${t.id}"
                data-user="${t.user_id ?? ''}"
                data-nombre="${t.nombre}"
                data-cargo="${t.cargo ?? ''}"
                data-unidad="${t.unidad_organica_id ?? ''}"
                data-dni="${t.dni ?? ''}"
                data-correo="${t.correo ?? ''}"
                data-cumplimiento="${t.cumplimiento}"
                data-puntualidad="${t.puntualidad}"
                data-participacion="${t.participacion}"
                data-responsabilidad="${t.responsabilidad}"
                data-categoria="${t.categoria ?? ''}"
                data-motivo="${(t.motivo ?? '').replace(/"/g,'&quot;')}"
                data-resolucion="${t.numero_resolucion ?? ''}">
                <i class="ti tabler-edit"></i>
              </button>
              @endcan
              @can('reconocimientos.eliminar')
              <button class="btn btn-sm btn-label-danger btn-eliminar-reconocimiento" style="border-radius:8px" title="Eliminar"
                data-id="${t.id}"
                data-nombre="${t.nombre}"
                data-url="${t.delete_url}">
                <i class="ti tabler-trash"></i>
              </button>
              @endcan
            </div>
          </div>
        </div>
      </div>`;
    }).join('');
  }

  /* ── Fix modal cortado: Vuexy pone overflow:hidden en layout-wrapper ── */
  const layoutWrapper = document.querySelector('.layout-wrapper');
  ['modalNuevoReconocimiento', 'modalEditarReconocimiento'].forEach(id => {
    const el = document.getElementById(id);
    if (!el || !layoutWrapper) return;
    el.addEventListener('show.bs.modal',  () => layoutWrapper.style.setProperty('overflow', 'visible', 'important'));
    el.addEventListener('hidden.bs.modal',() => layoutWrapper.style.removeProperty('overflow'));
  });

  /* ── Carga inicial con paginación ────────────────────────────────── */
  fetchReconocimientos();

});
</script>
@endsection
