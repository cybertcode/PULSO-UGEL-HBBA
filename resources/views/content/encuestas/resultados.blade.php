@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Resultados — ' . $encuesta->titulo)

@section('page-style')
<style>
/* ── Layout ── */
.results-page { max-width: 1100px; margin: 0 auto; }

/* ── KPI Cards ── */
.kpi-card { border-radius: 16px; border: none; overflow: hidden; position: relative; }
.kpi-card::before {
  content: ''; position: absolute; top: -30px; right: -30px;
  width: 100px; height: 100px; border-radius: 50%;
  background: rgba(255,255,255,.08);
}
.kpi-icon {
  width: 52px; height: 52px; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; flex-shrink: 0;
}
.kpi-num { font-size: 2.4rem; font-weight: 900; line-height: 1; letter-spacing: -.03em; }

/* ── Gauge ring ── */
.gauge-wrap { position: relative; width: 80px; height: 80px; flex-shrink: 0; }
.gauge-wrap > div { position: absolute; inset: 0; }
.gauge-label {
  position: absolute; inset: 0; display: flex; flex-direction: column;
  align-items: center; justify-content: center; line-height: 1.1;
  pointer-events: none; z-index: 2;
}
.gauge-pct { font-size: 1.1rem; font-weight: 900; }
.gauge-sub { font-size: .6rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #aaa; }

/* ── Pregunta card ── */
.pq-card {
  border-radius: 18px; border: 1px solid #ebe9f9;
  background: #fff; margin-bottom: 1.8rem;
  box-shadow: 0 2px 12px rgba(105,108,255,.06);
  overflow: hidden; transition: box-shadow .2s;
}
.pq-card:hover { box-shadow: 0 6px 30px rgba(105,108,255,.13); }
.pq-header {
  padding: 1.1rem 1.5rem .8rem;
  background: linear-gradient(135deg, #f8f7ff 0%, #fff 100%);
  border-bottom: 1px solid #f0eeff;
  display: flex; align-items: flex-start; gap: 1rem;
}
.pq-num {
  min-width: 34px; height: 34px; border-radius: 10px;
  background: linear-gradient(135deg, #696cff, #9b59b6);
  color: #fff; font-weight: 800; font-size: .85rem;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  box-shadow: 0 3px 8px rgba(105,108,255,.35);
}
.pq-body { padding: 1.5rem; }

/* ── Tipo chip ── */
.chip-tipo {
  display: inline-flex; align-items: center; gap: .35rem;
  font-size: .67rem; font-weight: 700; padding: .22em .8em;
  border-radius: 20px; letter-spacing: .03em;
}
.chip-opcion_multiple    { background:#dbeafe; color:#1d4ed8; }
.chip-seleccion_multiple { background:#e0f2fe; color:#0369a1; }
.chip-escala             { background:#fef9c3; color:#a16207; }
.chip-si_no              { background:#dcfce7; color:#166534; }
.chip-verdadero_falso    { background:#dbeafe; color:#1e40af; }
.chip-desplegable        { background:#f3e8ff; color:#6d28d9; }
.chip-texto_libre        { background:#f1f5f9; color:#475569; }

/* ── Stat bars ── */
.stat-row { display: flex; align-items: center; gap: .7rem; margin-bottom: .6rem; }
.stat-lbl { min-width: 140px; font-size: .8rem; color: #444; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.stat-track { flex: 1; height: 12px; background: #f0eeff; border-radius: 20px; overflow: hidden; }
.stat-fill  { height: 100%; border-radius: 20px; transition: width 1.1s cubic-bezier(.4,0,.2,1); width: 0; }
.stat-val   { min-width: 55px; text-align: right; font-size: .78rem; font-weight: 700; color: #696cff; }

/* ── Binario cards ── */
.bin-card {
  border-radius: 14px; padding: 1.2rem 1.5rem; text-align: center; flex: 1;
  position: relative; overflow: hidden;
}
.bin-card::after {
  content: ''; position: absolute; bottom: -15px; right: -15px;
  width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,.15);
}
.bin-num  { font-size: 2.8rem; font-weight: 900; line-height: 1; }
.bin-pct  { font-size: .82rem; font-weight: 700; opacity: .8; margin-top: .15rem; }
.bin-lbl  { font-size: .88rem; font-weight: 700; margin-top: .3rem; opacity: .9; }
.bin-si   { background: linear-gradient(135deg,#28c76f,#48da89); color: #fff; box-shadow: 0 6px 20px rgba(40,199,111,.3); }
.bin-no   { background: linear-gradient(135deg,#ea5455,#f27474); color: #fff; box-shadow: 0 6px 20px rgba(234,84,85,.3); }
.bin-verd { background: linear-gradient(135deg,#4169e1,#6495ed); color: #fff; box-shadow: 0 6px 20px rgba(65,105,225,.3); }
.bin-fals { background: linear-gradient(135deg,#ff9f43,#ffb976); color: #fff; box-shadow: 0 6px 20px rgba(255,159,67,.3); }

/* ── Escala cards ── */
.escala-card {
  border-radius: 14px; padding: .8rem .6rem; text-align: center; flex: 1;
  border: 2px solid transparent; transition: transform .2s, box-shadow .2s; cursor: default;
}
.escala-card:hover { transform: translateY(-3px); }
.escala-card .ec-num { font-size: 1.7rem; font-weight: 900; line-height: 1; }
.escala-card .ec-cnt { font-size: .72rem; margin-top: .2rem; opacity: .75; }
.escala-card .ec-lbl { font-size: .65rem; margin-top: .15rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
.ec-1 { background: linear-gradient(135deg,#fee2e2,#fecaca); color: #991b1b; border-color: #fca5a5; }
.ec-2 { background: linear-gradient(135deg,#ffedd5,#fed7aa); color: #9a3412; border-color: #fdba74; }
.ec-3 { background: linear-gradient(135deg,#fef9c3,#fef08a); color: #854d0e; border-color: #fde047; }
.ec-4 { background: linear-gradient(135deg,#dcfce7,#bbf7d0); color: #166534; border-color: #86efac; }
.ec-5 { background: linear-gradient(135deg,#dbeafe,#bfdbfe); color: #1e40af; border-color: #93c5fd; }

/* ── Promedio display ── */
.promedio-display {
  background: linear-gradient(135deg, #696cff15, #9b59b615);
  border-radius: 16px; padding: 1.5rem; text-align: center; border: 1.5px solid #e0ddff;
}
.promedio-big { font-size: 4rem; font-weight: 900; line-height: 1; color: #696cff; }
.star-row { display: flex; gap: .25rem; justify-content: center; margin: .5rem 0; }
.star-row i { font-size: 1.3rem; }

/* ── Texto libre cards ── */
.resp-bubble {
  background: #f8f7fe; border-left: 3px solid #696cff;
  border-radius: 0 12px 12px 0; padding: .9rem 1.1rem; margin-bottom: .6rem;
  transition: background .15s;
}
.resp-bubble:hover { background: #f0eeff; }
.resp-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: .35rem; }
.resp-autor { font-size: .72rem; font-weight: 700; color: #696cff; }
.resp-fecha { font-size: .68rem; color: #bbb; }
.resp-texto { font-size: .87rem; color: #333; line-height: 1.55; }

/* ── Participantes section ── */
.part-section { border-radius: 18px; overflow: hidden; border: 1px solid #ebe9f9; }
.part-tabs .nav-link {
  font-size: .82rem; font-weight: 700; padding: .65rem 1.2rem;
  border-radius: 0; border-bottom: 2px solid transparent; color: #888;
}
.part-tabs .nav-link.active { color: #696cff; border-bottom-color: #696cff; background: transparent; }
.part-search { position: relative; }
.part-search input { padding-left: 2.2rem; border-radius: 10px; font-size: .83rem; }
.part-search .search-icon { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: #aaa; }

/* ── Avatar initial ── */
.av-initial {
  width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: .78rem; font-weight: 800; color: #fff; letter-spacing: .03em;
}

/* ── Tabla ── */
.tbl-part td, .tbl-part th { padding: .55rem 1rem !important; vertical-align: middle; font-size: .83rem; }
.tbl-part thead { background: #f8f7fa; }
.tbl-part thead th { font-size: .67rem; font-weight: 700; text-transform: uppercase; color: #8a8899; letter-spacing: .05em; border-bottom: 1px solid rgba(0,0,0,.07) !important; }
.tbl-part tbody tr:hover td { background: rgba(105,108,255,.03); }

/* ── Progress encabezado ── */
.participation-bar { height: 8px; border-radius: 4px; background: #f0eeff; overflow: hidden; }
.participation-fill { height: 100%; border-radius: 4px; transition: width 1.4s cubic-bezier(.4,0,.2,1); }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 3rem 1rem; }
.empty-state i { font-size: 3rem; color: #c5c3e0; display: block; margin-bottom: .75rem; }
.empty-state p { color: #aaa; font-size: .87rem; margin: 0; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
<div class="results-page">

  {{-- ── Header ── --}}
  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
      <nav aria-label="breadcrumb" class="mb-1">
        <ol class="breadcrumb mb-0" style="font-size:.78rem">
          <li class="breadcrumb-item"><a href="{{ route('encuestas.index') }}">Encuestas</a></li>
          <li class="breadcrumb-item active">Resultados</li>
        </ol>
      </nav>
      <h4 class="fw-bold mb-1">{{ $encuesta->titulo }}</h4>
      <div class="d-flex gap-2 mt-1 flex-wrap align-items-center">
        @php
          $mBadge = ['sci'=>['bg-label-primary','SCI'], 'integridad'=>['bg-label-success','Integridad'], 'ambos'=>['bg-label-info','SCI + Integridad']];
          [$mCls,$mLbl] = $mBadge[$encuesta->modulo] ?? ['bg-label-secondary','—'];
          $eBadge = ['publicada'=>'success','cerrada'=>'warning','borrador'=>'secondary','archivada'=>'danger'];
        @endphp
        <span class="badge {{ $mCls }}" style="font-size:.72rem">{{ $mLbl }}</span>
        <span class="badge bg-label-{{ $eBadge[$encuesta->estado]??'secondary' }}" style="font-size:.72rem">
          {{ ucfirst($encuesta->estado) }}
        </span>
        @if($encuesta->fecha_inicio || $encuesta->fecha_fin)
          <span class="badge bg-label-secondary" style="font-size:.72rem">
            <i class="ti tabler-calendar me-1"></i>
            {{ $encuesta->fecha_inicio?->format('d M Y') ?? '—' }}
            @if($encuesta->fecha_fin) → {{ $encuesta->fecha_fin->format('d M Y') }} @endif
          </span>
        @endif
      </div>
    </div>
    <div class="d-flex gap-2">
      @can('encuesta.exportar')
      <a href="{{ route('encuestas.exportar', $encuesta) }}" class="btn btn-sm btn-label-success">
        <i class="ti tabler-file-excel me-1"></i>Excel
      </a>
      <a href="{{ route('encuestas.exportar.pdf', $encuesta) }}" class="btn btn-sm btn-label-danger">
        <i class="ti tabler-file-type-pdf me-1"></i>PDF
      </a>
      @endcan
      <a href="{{ route('encuestas.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="ti tabler-arrow-left me-1"></i>Volver
      </a>
    </div>
  </div>

  {{-- ── KPIs ── --}}
  <div class="row g-3 mb-5">

    {{-- Total destinatarios --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card kpi-card h-100 mb-0" style="background:linear-gradient(135deg,#696cff,#9b59b6)">
        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
          <div class="kpi-icon" style="background:rgba(255,255,255,.18)">
            <i class="ti tabler-users" style="color:#fff"></i>
          </div>
          <div>
            <div class="kpi-num" style="color:#fff">{{ $totalDestinatarios }}</div>
            <div style="color:rgba(255,255,255,.85);font-size:.78rem;font-weight:600">Destinatarios</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Respondieron --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card kpi-card h-100 mb-0" style="background:linear-gradient(135deg,#28c76f,#48da89)">
        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
          <div class="kpi-icon" style="background:rgba(255,255,255,.18)">
            <i class="ti tabler-circle-check" style="color:#fff"></i>
          </div>
          <div>
            <div class="kpi-num" style="color:#fff">{{ $totalCompletadas }}</div>
            <div style="color:rgba(255,255,255,.85);font-size:.78rem;font-weight:600">Completaron</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Pendientes --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card kpi-card h-100 mb-0" style="background:linear-gradient(135deg,#ff9f43,#ffb976)">
        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
          <div class="kpi-icon" style="background:rgba(255,255,255,.18)">
            <i class="ti tabler-clock" style="color:#fff"></i>
          </div>
          <div>
            <div class="kpi-num" style="color:#fff">{{ $totalDestinatarios - $totalCompletadas }}</div>
            <div style="color:rgba(255,255,255,.85);font-size:.78rem;font-weight:600">Pendientes</div>
          </div>
        </div>
      </div>
    </div>

    {{-- % Participación con gauge --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100 mb-0 border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
          <div class="gauge-wrap">
            <div id="gaugeMain"></div>
            <div class="gauge-label">
              <span class="gauge-pct" id="gaugePct" style="color:{{ $porcentaje>=80?'#28c76f':($porcentaje>=40?'#ff9f43':'#ea5455') }}">{{ $porcentaje }}%</span>
              <span class="gauge-sub">resp.</span>
            </div>
          </div>
          <div class="flex-grow-1">
            <div class="fw-bold mb-1" style="font-size:1rem;color:{{ $porcentaje>=80?'#28c76f':($porcentaje>=40?'#ff9f43':'#ea5455') }}">
              {{ $porcentaje >= 80 ? 'Alta participación' : ($porcentaje >= 40 ? 'Participación media' : 'Baja participación') }}
            </div>
            <div class="participation-bar">
              <div class="participation-fill" id="partFill"
                style="width:0%;background:{{ $porcentaje>=80?'linear-gradient(90deg,#28c76f,#48da89)':($porcentaje>=40?'linear-gradient(90deg,#ff9f43,#ffb976)':'linear-gradient(90deg,#ea5455,#f27474)') }}">
              </div>
            </div>
            <div class="text-muted mt-1" style="font-size:.72rem">{{ $totalCompletadas }} de {{ $totalDestinatarios }}</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- ── Resultados por pregunta ── --}}
  <div class="d-flex align-items-center gap-2 mb-3">
    <i class="ti tabler-chart-bar text-primary fs-5"></i>
    <h5 class="mb-0 fw-bold">Resultados por pregunta</h5>
    <span class="badge bg-label-primary ms-1" id="cntPreguntas"></span>
  </div>

  <div id="cargandoResultados" class="text-center py-5">
    <div class="spinner-border text-primary mb-3" style="width:2.5rem;height:2.5rem" role="status"></div>
    <p class="text-muted">Cargando resultados...</p>
  </div>

  <div id="resultadosContainer"></div>

  {{-- ── Participantes ── --}}
  <div id="participantesSection" style="display:none" class="mt-4 mb-2">
    <div class="d-flex align-items-center gap-2 mb-3">
      <i class="ti tabler-users text-primary fs-5"></i>
      <h5 class="mb-0 fw-bold">Participantes</h5>
    </div>
    <div class="card border-0 shadow-sm part-section">

      {{-- Tabs respondieron / pendientes --}}
      <div class="card-header bg-white border-0 pb-0">
        <ul class="nav part-tabs" id="partTabs">
          <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-respondieron">
              <i class="ti tabler-circle-check me-1 text-success"></i>
              Respondieron <span class="badge bg-label-success ms-1" id="cntRespondieron"></span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-pendientes">
              <i class="ti tabler-clock me-1 text-warning"></i>
              Pendientes <span class="badge bg-label-warning ms-1" id="cntPendientes"></span>
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body pt-3">
        {{-- Buscador --}}
        <div class="part-search mb-3" style="max-width:300px">
          <i class="ti tabler-search search-icon"></i>
          <input type="text" class="form-control form-control-sm" id="partSearch" placeholder="Buscar usuario...">
        </div>

        <div class="tab-content">

          {{-- Tab respondieron --}}
          <div class="tab-pane fade show active" id="tab-respondieron">
            <div class="table-responsive">
              <table class="table tbl-part mb-0">
                <thead>
                  <tr>
                    <th>Usuario</th>
                    <th>DNI</th>
                    <th>Estado</th>
                    <th>Inició</th>
                    <th>Completó</th>
                  </tr>
                </thead>
                <tbody id="tbodyRespondieron"></tbody>
              </table>
            </div>
            <div id="emptyRespondieron" class="empty-state d-none">
              <i class="ti tabler-mood-empty"></i>
              <p>Ningún usuario ha respondido aún</p>
            </div>
          </div>

          {{-- Tab pendientes --}}
          <div class="tab-pane fade" id="tab-pendientes">
            <div class="table-responsive">
              <table class="table tbl-part mb-0">
                <thead>
                  <tr>
                    <th>Usuario</th>
                    <th>DNI</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody id="tbodyPendientes"></tbody>
              </table>
            </div>
            <div id="emptyPendientes" class="empty-state d-none">
              <i class="ti tabler-confetti"></i>
              <p>¡Todos respondieron!</p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

</div>
</div>
@endsection

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
/* ═══════════════════════════════════════════
   Paleta y metadata
═══════════════════════════════════════════ */
const PAL = ['#696cff','#03c3ec','#71dd37','#ffab00','#ff3e1d','#20c997','#fd7e14','#6f42c1','#e83e8c','#17a2b8'];

const TIPO_META = {
  opcion_multiple:    { label: 'Opción múltiple',    icon: 'tabler-circle-dot' },
  seleccion_multiple: { label: 'Selección múltiple', icon: 'tabler-checkbox' },
  escala:             { label: 'Escala 1–5',          icon: 'tabler-stars' },
  si_no:              { label: 'Sí / No',             icon: 'tabler-checks' },
  verdadero_falso:    { label: 'Verdadero / Falso',   icon: 'tabler-shield-check' },
  desplegable:        { label: 'Lista desplegable',   icon: 'tabler-selector' },
  texto_libre:        { label: 'Texto libre',         icon: 'tabler-text-size' },
};

/* ── Gauge de participación (ApexCharts radialBar) ── */
(function () {
  const pct   = {{ $porcentaje }};
  const color = pct >= 80 ? '#28c76f' : pct >= 40 ? '#ff9f43' : '#ea5455';

  new ApexCharts(document.getElementById('gaugeMain'), {
    chart: { type: 'radialBar', height: 80, sparkline: { enabled: true } },
    series: [pct],
    colors: [color],
    plotOptions: {
      radialBar: {
        startAngle: -90, endAngle: 90,
        track: { background: '#f0eeff', strokeWidth: '97%' },
        dataLabels: { show: false },
        hollow: { size: '55%' }
      }
    },
    stroke: { lineCap: 'round' },
  }).render();

  // Animar barra de progreso
  setTimeout(() => {
    document.getElementById('partFill').style.width = pct + '%';
  }, 200);
})();

/* ═══════════════════════════════════════════
   Carga principal de datos
═══════════════════════════════════════════ */
fetch('{{ route("encuestas.resultados.datos", $encuesta) }}')
  .then(r => {
    if (!r.ok) throw new Error('HTTP ' + r.status);
    return r.json();
  })
  .then(resp => {
    document.getElementById('cargandoResultados').style.display = 'none';

    const container = document.getElementById('resultadosContainer');
    document.getElementById('cntPreguntas').textContent = resp.preguntas.length;
    resp.preguntas.forEach((pq, i) => renderPregunta(pq, i, container));

    // Participantes — split por completada
    const respondieron = (resp.participantes || []).filter(p => p.completada);
    const pendientes   = (resp.pendientes || []);
    if (respondieron.length || pendientes.length) {
      document.getElementById('participantesSection').style.display = '';
      renderParticipantes(respondieron, pendientes);
    }
  })
  .catch(err => {
    console.error('[Encuesta Resultados] Error:', err);
    document.getElementById('cargandoResultados').innerHTML =
      `<div class="alert alert-danger"><i class="ti tabler-alert-circle me-1"></i>Error al cargar resultados: ${err.message}</div>`;
  });

/* ═══════════════════════════════════════════
   Renderizar pregunta
═══════════════════════════════════════════ */
function renderPregunta(pq, idx, container) {
  const meta  = TIPO_META[pq.tipo] || { label: pq.tipo, icon: 'tabler-help' };
  const total = pq.data?.reduce((a, b) => a + b, 0) ?? 0;

  const card  = document.createElement('div');
  card.className = 'pq-card';
  card.innerHTML = `
    <div class="pq-header">
      <div class="pq-num">${idx + 1}</div>
      <div class="flex-grow-1">
        <div class="fw-semibold mb-1" style="font-size:1rem;color:#2d2b45;line-height:1.4">${escH(pq.texto)}</div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <span class="chip-tipo chip-${pq.tipo}">
            <i class="ti ${meta.icon}"></i>${meta.label}
          </span>
          ${total > 0
            ? `<span class="text-muted" style="font-size:.72rem"><i class="ti tabler-message me-1"></i>${total} respuesta${total !== 1 ? 's' : ''}</span>`
            : `<span class="badge bg-label-secondary" style="font-size:.67rem">Sin respuestas</span>`
          }
        </div>
      </div>
    </div>
    <div class="pq-body" id="pqb-${pq.id}"></div>`;
  container.appendChild(card);

  const body = document.getElementById('pqb-' + pq.id);
  if      (pq.tipo === 'texto_libre')     renderTextoLibre(pq, body);
  else if (pq.tipo === 'si_no')           renderBinario(pq, body, ['si','no'],           ['Sí','No'],           ['bin-si','bin-no'],     ['#28c76f','#ea5455']);
  else if (pq.tipo === 'verdadero_falso') renderBinario(pq, body, ['verdadero','falso'], ['Verdadero','Falso'], ['bin-verd','bin-fals'], ['#4169e1','#ff9f43']);
  else if (pq.tipo === 'escala')          renderEscala(pq, body);
  else                                    renderOpciones(pq, body);
}

/* ═══════════════════════════════════════════
   Opciones múltiples / desplegable
═══════════════════════════════════════════ */
function renderOpciones(pq, body) {
  const total = pq.data.reduce((a, b) => a + b, 0);
  if (total === 0) { body.innerHTML = sinRespuestas(); return; }

  const colores = PAL.slice(0, pq.labels.length);

  body.innerHTML = `
    <div class="row g-4 align-items-start">
      <div class="col-lg-5">
        <div id="apex-bar-${pq.id}"></div>
      </div>
      <div class="col-lg-3">
        <div id="apex-donut-${pq.id}"></div>
      </div>
      <div class="col-lg-4" id="sbw-${pq.id}"></div>
    </div>`;

  // Stat bars
  const sbw = document.getElementById('sbw-' + pq.id);
  sbw.innerHTML = pq.labels.map((lbl, i) => {
    const pct = total > 0 ? Math.round(pq.data[i] / total * 100) : 0;
    return `<div class="stat-row">
      <div class="stat-lbl" title="${escH(lbl)}">${escH(lbl)}</div>
      <div class="stat-track"><div class="stat-fill" id="sf-${pq.id}-${i}" style="background:${colores[i]}" data-pct="${pct}"></div></div>
      <div class="stat-val">${pq.data[i]}</div>
    </div>`;
  }).join('');
  setTimeout(() => pq.labels.forEach((_, i) => {
    const el = document.getElementById(`sf-${pq.id}-${i}`);
    if (el) el.style.width = el.dataset.pct + '%';
  }), 150);

  // Bar horizontal (ApexCharts)
  new ApexCharts(document.getElementById('apex-bar-' + pq.id), {
    chart: { type: 'bar', height: Math.max(180, pq.labels.length * 44), toolbar: { show: false } },
    plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '60%',
      dataLabels: { position: 'right' } } },
    series: [{ name: 'Respuestas', data: pq.data }],
    xaxis: { categories: pq.labels, labels: { style: { fontSize: '11px' } } },
    colors: colores,
    dataLabels: { enabled: true, formatter: v => v + ' resp.', style: { fontSize: '11px' } },
    tooltip: { y: { formatter: v => `${v} resp. (${total > 0 ? Math.round(v/total*100) : 0}%)` } },
    grid: { borderColor: '#f5f4fe' },
    legend: { show: false },
  }).render();

  // Donut (ApexCharts)
  new ApexCharts(document.getElementById('apex-donut-' + pq.id), {
    chart: { type: 'donut', height: 230 },
    series: pq.data,
    labels: pq.labels,
    colors: colores,
    plotOptions: { pie: { donut: { size: '64%' } } },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '10px' },
    tooltip: { y: { formatter: v => `${v} (${total > 0 ? Math.round(v/total*100) : 0}%)` } },
  }).render();
}

/* ═══════════════════════════════════════════
   Escala 1-5
═══════════════════════════════════════════ */
function renderEscala(pq, body) {
  const total = pq.data.reduce((a, b) => a + b, 0);
  if (total === 0) { body.innerHTML = sinRespuestas(); return; }

  const clases    = ['ec-1','ec-2','ec-3','ec-4','ec-5'];
  const etiquetas = ['Muy malo','Malo','Regular','Bueno','Muy bueno'];
  const colores   = ['#ea5455','#fd7e14','#ffab00','#71dd37','#696cff'];
  const promedio  = pq.promedio ?? 0;
  const estrellas = Math.round(promedio);

  body.innerHTML = `
    <div class="d-flex gap-2 mb-4 flex-wrap">
      ${pq.labels.map((v, i) => `
        <div class="escala-card ${clases[i]}" style="min-width:90px">
          <div class="ec-num">${v}</div>
          <div class="ec-cnt">${pq.data[i]} resp.</div>
          <div class="ec-lbl">${etiquetas[i]}</div>
        </div>`).join('')}
    </div>
    <div class="row g-4 align-items-center">
      <div class="col-md-7">
        <div id="apex-escala-${pq.id}"></div>
      </div>
      <div class="col-md-5">
        <div class="promedio-display">
          <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#888;margin-bottom:.5rem">Promedio</div>
          <div class="promedio-big">${promedio}</div>
          <div style="font-size:.72rem;color:#aaa;margin-bottom:.6rem">de 5 puntos · ${total} respuesta${total !== 1 ? 's' : ''}</div>
          <div class="star-row">
            ${[1,2,3,4,5].map(s => `<i class="ti tabler-star${s <= estrellas ? '-filled text-warning' : ' text-muted'}"></i>`).join('')}
          </div>
          <div class="mt-3">
            <div style="height:10px;background:#f0eeff;border-radius:5px;overflow:hidden">
              <div id="epb-${pq.id}" style="height:100%;background:linear-gradient(90deg,#ffab00,#696cff);border-radius:5px;transition:width 1.3s;width:0"></div>
            </div>
            <div class="d-flex justify-content-between mt-1" style="font-size:.67rem;color:#bbb"><span>1</span><span>5</span></div>
          </div>
        </div>
      </div>
    </div>`;

  setTimeout(() => {
    const el = document.getElementById('epb-' + pq.id);
    if (el) el.style.width = ((promedio - 1) / 4 * 100) + '%';
  }, 200);

  new ApexCharts(document.getElementById('apex-escala-' + pq.id), {
    chart: { type: 'bar', height: 230, toolbar: { show: false }, animations: { easing: 'easeOutBounce' } },
    plotOptions: { bar: { borderRadius: 8, columnWidth: '55%', distributed: true } },
    series: [{ name: 'Respuestas', data: pq.data }],
    xaxis: { categories: pq.labels.map((v, i) => [v, etiquetas[i]]), labels: { style: { fontSize: '10px' } } },
    yaxis: { labels: { style: { fontSize: '11px' } }, min: 0 },
    colors: colores,
    dataLabels: { enabled: true, style: { fontSize: '11px' } },
    tooltip: { y: { formatter: v => `${v} resp. (${total > 0 ? Math.round(v/total*100) : 0}%)` } },
    legend: { show: false },
    grid: { borderColor: '#f5f4fe' },
  }).render();
}

/* ═══════════════════════════════════════════
   Binario (Sí/No · Verdadero/Falso)
═══════════════════════════════════════════ */
function renderBinario(pq, body, keys, labels, cls, binColors) {
  const total = pq.data?.reduce((a, b) => a + b, 0) ?? 0;
  if (total === 0) { body.innerHTML = sinRespuestas(); return; }

  const counts = {};
  keys.forEach((k, i) => counts[k] = pq.data[i] ?? 0);
  const pcts = keys.map(k => total > 0 ? Math.round(counts[k] / total * 100) : 0);

  body.innerHTML = `
    <div class="row g-4 align-items-center">
      <div class="col-md-5">
        <div class="d-flex gap-3 mb-3">
          ${keys.map((k, i) => `
            <div class="bin-card ${cls[i]}">
              <div class="bin-num">${counts[k]}</div>
              <div class="bin-pct">${pcts[i]}%</div>
              <div class="bin-lbl">${labels[i]}</div>
            </div>`).join('')}
        </div>
        <div class="text-muted" style="font-size:.72rem;text-align:center">${total} respuesta${total !== 1 ? 's' : ''} totales</div>
      </div>
      <div class="col-md-4">
        <div id="apex-bin-${pq.id}"></div>
      </div>
      <div class="col-md-3" id="sbwb-${pq.id}"></div>
    </div>`;

  // Stat bars
  const sbw = document.getElementById('sbwb-' + pq.id);
  sbw.innerHTML = keys.map((k, i) => `
    <div class="stat-row">
      <div class="stat-lbl fw-bold">${labels[i]}</div>
      <div class="stat-track"><div class="stat-fill" id="sfb-${pq.id}-${i}" style="background:${binColors[i]}" data-pct="${pcts[i]}"></div></div>
      <div class="stat-val" style="color:${binColors[i]}">${pcts[i]}%</div>
    </div>`).join('');
  setTimeout(() => keys.forEach((_, i) => {
    const el = document.getElementById(`sfb-${pq.id}-${i}`);
    if (el) el.style.width = el.dataset.pct + '%';
  }), 150);

  new ApexCharts(document.getElementById('apex-bin-' + pq.id), {
    chart: { type: 'donut', height: 220 },
    series: keys.map(k => counts[k]),
    labels: labels,
    colors: binColors,
    plotOptions: { pie: { donut: { size: '58%', labels: {
      show: true, total: { show: true, label: 'Total', formatter: () => total }
    } } } },
    dataLabels: { enabled: true, formatter: (v) => Math.round(v) + '%' },
    legend: { position: 'bottom', fontSize: '11px' },
    tooltip: { y: { formatter: v => `${v} resp.` } },
  }).render();
}

/* ═══════════════════════════════════════════
   Texto libre
═══════════════════════════════════════════ */
function renderTextoLibre(pq, body) {
  if (!pq.respuestas?.length) { body.innerHTML = sinRespuestas(); return; }
  body.innerHTML = `
    <div class="text-muted mb-3" style="font-size:.75rem">
      <i class="ti tabler-message-circle me-1"></i>${pq.respuestas.length} respuesta${pq.respuestas.length !== 1 ? 's' : ''} registradas
    </div>
    <div class="row g-2">
      ${pq.respuestas.map(r => `
        <div class="col-md-6">
          <div class="resp-bubble">
            <div class="resp-meta">
              <span class="resp-autor"><i class="ti tabler-user-circle me-1"></i>${escH(r.usuario)}</span>
              <span class="resp-fecha"><i class="ti tabler-calendar me-1"></i>${r.fecha}</span>
            </div>
            <div class="resp-texto">${escH(r.respuesta)}</div>
          </div>
        </div>`).join('')}
    </div>`;
}

/* ═══════════════════════════════════════════
   Participantes (respondieron / pendientes)
═══════════════════════════════════════════ */
function avatarColor(name) {
  const colors = ['#696cff','#03c3ec','#28c76f','#ff9f43','#ea5455','#7367f0','#00bad1','#ff6480'];
  let h = 0;
  for (let c of String(name)) h = (h * 31 + c.charCodeAt(0)) & 0xffffffff;
  return colors[Math.abs(h) % colors.length];
}
function initials(name) {
  return String(name).split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

let _respondieron = [], _pendientes = [];

function renderParticipantes(respondieron, pendientes) {
  _respondieron = respondieron;
  _pendientes   = pendientes;

  document.getElementById('cntRespondieron').textContent = respondieron.length;
  document.getElementById('cntPendientes').textContent   = pendientes.length;

  renderTablaRespondieron(respondieron);
  renderTablaPendientes(pendientes);
}

function renderTablaRespondieron(data) {
  const tbody = document.getElementById('tbodyRespondieron');
  const empty = document.getElementById('emptyRespondieron');
  if (!data.length) {
    tbody.innerHTML = '';
    empty.classList.remove('d-none');
    return;
  }
  empty.classList.add('d-none');
  tbody.innerHTML = data.map(p => `
    <tr>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="av-initial" style="background:${avatarColor(p.usuario)}">${initials(p.usuario)}</div>
          <span class="fw-semibold">${escH(p.usuario)}</span>
        </div>
      </td>
      <td><span class="text-muted">${escH(p.dni)}</span></td>
      <td>${p.completada
        ? '<span class="badge bg-label-success" style="font-size:.7rem"><i class="ti tabler-check me-1"></i>Completada</span>'
        : '<span class="badge bg-label-warning" style="font-size:.7rem"><i class="ti tabler-loader me-1"></i>En progreso</span>'
      }</td>
      <td class="text-muted" style="font-size:.8rem">${p.iniciada_at || '—'}</td>
      <td class="text-muted" style="font-size:.8rem">${p.completada_at || '—'}</td>
    </tr>`).join('');
}

function renderTablaPendientes(data) {
  const tbody = document.getElementById('tbodyPendientes');
  const empty = document.getElementById('emptyPendientes');
  if (!data.length) {
    tbody.innerHTML = '';
    empty.classList.remove('d-none');
    return;
  }
  empty.classList.add('d-none');
  tbody.innerHTML = data.map(p => `
    <tr>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="av-initial" style="background:${avatarColor(p.usuario)};opacity:.6">${initials(p.usuario)}</div>
          <span class="fw-semibold text-muted">${escH(p.usuario)}</span>
        </div>
      </td>
      <td><span class="text-muted">${escH(p.dni)}</span></td>
      <td><span class="badge bg-label-secondary" style="font-size:.7rem"><i class="ti tabler-clock me-1"></i>Pendiente</span></td>
    </tr>`).join('');
}

/* ── Búsqueda en tabla ── */
document.getElementById('partSearch')?.addEventListener('input', function () {
  const q = this.value.toLowerCase();
  const filtrar = arr => arr.filter(p => p.usuario.toLowerCase().includes(q) || p.dni.toLowerCase().includes(q));
  renderTablaRespondieron(filtrar(_respondieron));
  renderTablaPendientes(filtrar(_pendientes));
});

/* ── Helpers ── */
function sinRespuestas() {
  return `<div class="empty-state">
    <i class="ti tabler-mood-empty"></i>
    <p>Sin respuestas aún</p>
  </div>`;
}
function escH(s) {
  return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
}); // end DOMContentLoaded
</script>
@endsection
