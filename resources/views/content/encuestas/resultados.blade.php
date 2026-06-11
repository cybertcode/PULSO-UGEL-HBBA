@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Resultados — ' . $encuesta->titulo)

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
@endsection

@section('page-style')
<style>
.kpi-res { border-radius: 12px; border: none; }
.pregunta-resultado { border: 1px solid #e7e7e7; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; }
.pregunta-resultado .pq-titulo { font-weight: 600; font-size: 1rem; margin-bottom: 1rem; }
.pq-tipo-chip { font-size: .7rem; padding: .2rem .7rem; border-radius: 20px; }
.badge-modulo-sci        { background: #e3f2fd; color: #1565c0; }
.badge-modulo-integridad { background: #e8f5e9; color: #2e7d32; }
.tbl-part td, .tbl-part th { padding: .45rem .8rem !important; vertical-align: middle; font-size: .85rem; }
.tbl-part thead th { background: #f8f7fa; font-size: .7rem; font-weight: 700; text-transform: uppercase; color: #6e6b7b; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h4 class="fw-bold mb-1">
        <span class="text-muted fw-light">Encuestas /</span> Resultados
      </h4>
      <p class="text-muted mb-0">{{ $encuesta->titulo }}</p>
    </div>
    <div class="d-flex gap-2">
      @can('encuesta.exportar')
      <a href="{{ route('encuestas.exportar', $encuesta) }}" class="btn btn-outline-success btn-sm">
        <i class="ti tabler-file-excel me-1"></i> Exportar Excel
      </a>
      @endcan
      <a href="{{ route('encuestas.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti tabler-arrow-left me-1"></i> Volver
      </a>
    </div>
  </div>

  {{-- KPI Cards --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-4">
      <div class="card kpi-res shadow-sm text-center py-3">
        <div class="card-body p-2">
          <div class="mb-1"><span class="badge bg-label-primary rounded-circle p-2"><i class="ti tabler-users fs-4"></i></span></div>
          <div class="fw-bold fs-2 text-primary">{{ $totalDestinatarios }}</div>
          <div class="text-muted small">Destinatarios</div>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="card kpi-res shadow-sm text-center py-3">
        <div class="card-body p-2">
          <div class="mb-1"><span class="badge bg-label-success rounded-circle p-2"><i class="ti tabler-check fs-4"></i></span></div>
          <div class="fw-bold fs-2 text-success">{{ $totalCompletadas }}</div>
          <div class="text-muted small">Respondieron</div>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="card kpi-res shadow-sm text-center py-3">
        <div class="card-body p-2">
          <div class="mb-1"><span class="badge bg-label-info rounded-circle p-2"><i class="ti tabler-percentage fs-4"></i></span></div>
          <div class="fw-bold fs-2 text-info">{{ $porcentaje }}%</div>
          <div class="text-muted small">Participación</div>
          <div class="progress mt-2 mx-3" style="height:6px">
            <div class="progress-bar bg-info" style="width:{{ $porcentaje }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Cargando --}}
  <div id="cargandoResultados" class="text-center py-5 text-muted">
    <div class="spinner-border text-primary mb-3" role="status"></div>
    <p>Cargando resultados...</p>
  </div>

  {{-- Contenedor de resultados por pregunta --}}
  <div id="resultadosContainer" style="display:none"></div>

  {{-- Tabla de participantes --}}
  <div id="participantesSection" style="display:none" class="mt-4">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="ti tabler-users me-1 text-primary"></i> Participantes</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table tbl-part mb-0">
            <thead>
              <tr>
                <th>Usuario</th>
                <th>DNI</th>
                <th>Estado</th>
                <th>Fecha respuesta</th>
              </tr>
            </thead>
            <tbody id="tbodyParticipantes"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/chart.js/chart.umd.js') }}"></script>
@endsection

@section('page-script')
<script>
const COLORES = [
  '#696cff','#03c3ec','#71dd37','#ff3e1d','#ffab00',
  '#20c997','#fd7e14','#6f42c1','#e83e8c','#17a2b8'
];

fetch('{{ route("encuestas.resultados.datos", $encuesta) }}')
  .then(r => r.json())
  .then(resp => {
    document.getElementById('cargandoResultados').style.display = 'none';

    const container = document.getElementById('resultadosContainer');
    container.style.display = '';

    resp.preguntas.forEach((pq, idx) => {
      const div = document.createElement('div');
      div.className = 'pregunta-resultado';

      let contenido = '';
      const tipoLabels = { opcion_multiple:'Opción múltiple', seleccion_multiple:'Selección múltiple', escala:'Escala 1-5', texto_libre:'Texto libre' };
      const tipoColors = { opcion_multiple:'bg-label-primary', seleccion_multiple:'bg-label-info', escala:'bg-label-warning', texto_libre:'bg-label-secondary' };

      contenido += `<div class="d-flex align-items-start gap-3 mb-3">
        <span class="badge bg-label-primary rounded-circle px-2 py-2 fw-bold">${idx+1}</span>
        <div class="flex-grow-1">
          <div class="pq-titulo">${pq.texto}</div>
          <span class="badge ${tipoColors[pq.tipo] || 'bg-label-secondary'} pq-tipo-chip">${tipoLabels[pq.tipo] || pq.tipo}</span>
        </div>
      </div>`;

      if (pq.tipo === 'texto_libre') {
        if (!pq.respuestas || pq.respuestas.length === 0) {
          contenido += '<p class="text-muted small">Sin respuestas aún.</p>';
        } else {
          contenido += '<div class="row g-2">' + pq.respuestas.map(r =>
            `<div class="col-md-6"><div class="border rounded p-2 bg-light small">
              <strong class="text-primary">${r.usuario}</strong> <span class="text-muted">(${r.fecha})</span>
              <p class="mb-0 mt-1">${r.respuesta}</p>
            </div></div>`
          ).join('') + '</div>';
        }
      } else {
        const total = pq.data ? pq.data.reduce((a,b) => a+b, 0) : 0;
        contenido += `<div class="row g-4 align-items-start">
          <div class="col-md-6">
            <canvas id="chart-bar-${pq.id}" height="220"></canvas>
          </div>
          <div class="col-md-6">
            <canvas id="chart-dona-${pq.id}" height="220"></canvas>
          </div>
        </div>`;
        if (pq.tipo === 'escala' && pq.promedio !== undefined) {
          contenido += `<div class="mt-2 text-center">
            <span class="badge bg-label-warning fs-6 px-3">Promedio: <strong>${pq.promedio}</strong> / 5</span>
          </div>`;
        }
      }

      div.innerHTML = contenido;
      container.appendChild(div);

      // Crear charts después de insertar el DOM
      if (pq.tipo !== 'texto_libre' && pq.labels) {
        const ctxBar = document.getElementById('chart-bar-' + pq.id)?.getContext('2d');
        if (ctxBar) {
          new Chart(ctxBar, {
            type: pq.tipo === 'escala' ? 'bar' : 'bar',
            data: {
              labels: pq.labels,
              datasets: [{ label: 'Respuestas', data: pq.data,
                backgroundColor: COLORES.slice(0, pq.labels.length),
                borderRadius: 4 }]
            },
            options: {
              responsive: true,
              plugins: { legend: { display: false },
                tooltip: { callbacks: { label: ctx => ctx.raw + ' resp. (' + (pq.data.reduce((a,b)=>a+b,0) > 0 ? Math.round(ctx.raw / pq.data.reduce((a,b)=>a+b,0) * 100) : 0) + '%)' }}
              },
              scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
          });
        }

        const ctxDona = document.getElementById('chart-dona-' + pq.id)?.getContext('2d');
        if (ctxDona) {
          new Chart(ctxDona, {
            type: 'doughnut',
            data: {
              labels: pq.labels,
              datasets: [{ data: pq.data,
                backgroundColor: COLORES.slice(0, pq.labels.length),
                hoverOffset: 6 }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.raw + ' (' + (ctx.dataset.data.reduce((a,b)=>a+b,0) > 0 ? Math.round(ctx.raw / ctx.dataset.data.reduce((a,b)=>a+b,0) * 100) : 0) + '%)' }}
              }
            }
          });
        }
      }
    });

    // Tabla de participantes
    if (resp.participantes && resp.participantes.length > 0) {
      const section = document.getElementById('participantesSection');
      section.style.display = '';
      const tbody = document.getElementById('tbodyParticipantes');
      tbody.innerHTML = resp.participantes.map(p =>
        `<tr>
          <td>${p.usuario}</td>
          <td><span class="text-muted">${p.dni}</span></td>
          <td>${p.completada
            ? '<span class="badge bg-label-success">Completada</span>'
            : '<span class="badge bg-label-secondary">Pendiente</span>'}</td>
          <td class="text-muted">${p.completada_at || '—'}</td>
        </tr>`
      ).join('');
    }
  })
  .catch(() => {
    document.getElementById('cargandoResultados').innerHTML =
      '<div class="alert alert-danger">Error al cargar los resultados. Recarga la página.</div>';
  });
</script>
@endsection
