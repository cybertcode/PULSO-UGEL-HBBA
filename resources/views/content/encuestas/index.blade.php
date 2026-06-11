@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Encuestas — PULSO UGEL')

@section('page-style')
<style>
.kpi-enc { border-radius: 12px; border: none; transition: transform .15s, box-shadow .15s; }
.kpi-enc:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.09); }
.tbl-enc td, .tbl-enc th { padding: .5rem .9rem !important; vertical-align: middle; }
.tbl-enc thead th { font-size: .7rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
    color: #6e6b7b; background: #f8f7fa; white-space: nowrap; border-bottom: 1px solid rgba(0,0,0,.07) !important; }
.tbl-enc tbody tr { border-bottom: 1px solid rgba(0,0,0,.04) !important; transition: background .1s; }
.tbl-enc tbody tr:hover { background: rgba(105,108,255,.04) !important; }
.badge-modulo-sci        { background: #e3f2fd; color: #1565c0; }
.badge-modulo-integridad { background: #e8f5e9; color: #2e7d32; }
.badge-modulo-ambos      { background: #f3e5f5; color: #6a1b9a; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <h4 class="fw-bold mb-1"><span class="text-muted fw-light">PULSO UGEL /</span> Encuestas</h4>
      <p class="text-muted mb-0 small">Gestión de encuestas institucionales</p>
    </div>
    @can('encuesta.crear')
    <a href="{{ route('encuestas.crear') }}" class="btn btn-primary">
      <i class="ti tabler-plus me-1"></i> Nueva Encuesta
    </a>
    @endcan
  </div>

  {{-- Alertas flash --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert">
      <i class="ti tabler-check me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
      <i class="ti tabler-alert-circle me-1"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('info'))
    <div class="alert alert-info alert-dismissible mb-4" role="alert">
      <i class="ti tabler-info-circle me-1"></i> {{ session('info') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- KPI Cards --}}
  <div class="row g-4 mb-4">
    <div class="col-6 col-sm-4 col-lg-2">
      <div class="card kpi-enc shadow-sm h-100 text-center py-3">
        <div class="card-body p-2">
          <div class="mb-1"><span class="badge bg-label-primary rounded-circle p-2"><i class="ti tabler-clipboard-list fs-4"></i></span></div>
          <div class="fw-bold fs-3 text-primary">{{ $stats['total'] }}</div>
          <div class="text-muted small">Total</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-sm-4 col-lg-2">
      <div class="card kpi-enc shadow-sm h-100 text-center py-3">
        <div class="card-body p-2">
          <div class="mb-1"><span class="badge bg-label-secondary rounded-circle p-2"><i class="ti tabler-pencil fs-4"></i></span></div>
          <div class="fw-bold fs-3 text-secondary">{{ $stats['borrador'] }}</div>
          <div class="text-muted small">Borrador</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-sm-4 col-lg-2">
      <div class="card kpi-enc shadow-sm h-100 text-center py-3">
        <div class="card-body p-2">
          <div class="mb-1"><span class="badge bg-label-success rounded-circle p-2"><i class="ti tabler-send fs-4"></i></span></div>
          <div class="fw-bold fs-3 text-success">{{ $stats['publicadas'] }}</div>
          <div class="text-muted small">Publicadas</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-sm-4 col-lg-2">
      <div class="card kpi-enc shadow-sm h-100 text-center py-3">
        <div class="card-body p-2">
          <div class="mb-1"><span class="badge bg-label-warning rounded-circle p-2"><i class="ti tabler-lock fs-4"></i></span></div>
          <div class="fw-bold fs-3 text-warning">{{ $stats['cerradas'] }}</div>
          <div class="text-muted small">Cerradas</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-8 col-lg-4">
      <div class="card kpi-enc shadow-sm h-100 text-center py-3 border-primary">
        <div class="card-body p-2">
          <div class="mb-1"><span class="badge bg-label-danger rounded-circle p-2"><i class="ti tabler-clock-exclamation fs-4"></i></span></div>
          <div class="fw-bold fs-3 text-danger">{{ $stats['mis_pendientes'] }}</div>
          <div class="text-muted small">Mis encuestas pendientes</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Filtros --}}
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3 d-flex flex-wrap gap-2 align-items-center">
      <input type="text" id="buscar" class="form-control form-control-sm" style="max-width:220px" placeholder="Buscar título...">
      <select id="filtroEstado" class="form-select form-select-sm" style="max-width:150px">
        <option value="">Todos los estados</option>
        <option value="borrador">Borrador</option>
        <option value="publicada">Publicada</option>
        <option value="cerrada">Cerrada</option>
        <option value="archivada">Archivada</option>
      </select>
      <select id="filtroModulo" class="form-select form-select-sm" style="max-width:160px">
        <option value="">Todos los módulos</option>
        <option value="sci">SCI</option>
        <option value="integridad">Integridad</option>
        <option value="ambos">SCI + Integridad</option>
      </select>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table tbl-enc mb-0" id="tablaEncuestas">
          <thead>
            <tr>
              <th>#</th>
              <th>Título</th>
              <th>Módulo</th>
              <th>Estado</th>
              <th>Fechas</th>
              <th>Participación</th>
              <th>Creado por</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyEncuestas">
            <tr><td colspan="8" class="text-center py-4 text-muted"><i class="ti tabler-loader-2 spin me-1"></i> Cargando...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end" id="paginacion"></div>
  </div>

</div>

{{-- Modal confirmar publicar --}}
<div class="modal fade" id="modalPublicar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti tabler-send me-1 text-success"></i> Publicar encuesta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de publicar esta encuesta? Se enviarán alertas a todos los destinatarios y no podrás editar las preguntas.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="formPublicar" method="POST">
          @csrf
          <button type="submit" class="btn btn-success">
            <i class="ti tabler-send me-1"></i> Publicar
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal confirmar eliminar --}}
<div class="modal fade" id="modalEliminar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title text-danger"><i class="ti tabler-trash me-1"></i> Eliminar encuesta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">¿Eliminar esta encuesta? Esta acción no se puede deshacer.</div>
      <div class="modal-footer border-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="formEliminar" method="POST">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal confirmar cerrar --}}
<div class="modal fade" id="modalCerrar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti tabler-lock me-1 text-warning"></i> Cerrar encuesta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">¿Cerrar esta encuesta? Ya no se podrán registrar nuevas respuestas.</div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="formCerrar" method="POST">
          @csrf
          <button type="submit" class="btn btn-warning">Cerrar encuesta</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
let currentPage = 1;
const ROUTE_BASE = '{{ route("encuestas.index") }}';

function cargarTabla() {
  const buscar  = document.getElementById('buscar').value;
  const estado  = document.getElementById('filtroEstado').value;
  const modulo  = document.getElementById('filtroModulo').value;
  const url     = ROUTE_BASE + '/data?buscar=' + encodeURIComponent(buscar)
                + '&estado=' + estado + '&modulo=' + modulo + '&page=' + currentPage;

  fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(resp => {
      renderTabla(resp.data);
      renderPaginacion(resp);
    });
}

function renderTabla(rows) {
  const tbody = document.getElementById('tbodyEncuestas');
  if (!rows || rows.length === 0) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron encuestas.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((e, i) => {
    const modLabels = { sci: '<span class="badge badge-modulo-sci">SCI</span>',
                        integridad: '<span class="badge badge-modulo-integridad">Integridad</span>',
                        ambos: '<span class="badge badge-modulo-ambos">SCI + Integridad</span>' };
    const estColors = { borrador:'secondary', publicada:'success', cerrada:'warning', archivada:'danger' };
    const pct = e.respuestas_count > 0 ? Math.round((e.completadas_count / e.respuestas_count) * 100) : 0;
    const fechas = (e.fecha_inicio || e.fecha_fin)
      ? (e.fecha_inicio || '?') + ' → ' + (e.fecha_fin || '∞')
      : '<span class="text-muted">Sin fecha</span>';

    let acciones = '';
    @can('encuesta.ver')
    if (e.estado === 'publicada' || e.estado === 'cerrada') {
      acciones += `<a href="{{ route('encuestas.index') }}/${e.id}/resultados" class="btn btn-icon btn-sm btn-outline-info" title="Ver resultados"><i class="ti tabler-chart-bar"></i></a> `;
    }
    @endcan
    @can('encuesta.editar')
    if (e.estado === 'borrador') {
      acciones += `<a href="{{ route('encuestas.index') }}/${e.id}/editar" class="btn btn-icon btn-sm btn-outline-warning" title="Editar"><i class="ti tabler-pencil"></i></a> `;
    }
    @endcan
    @can('encuesta.publicar')
    if (e.estado === 'borrador') {
      acciones += `<button onclick="confirmarPublicar(${e.id})" class="btn btn-icon btn-sm btn-outline-success" title="Publicar"><i class="ti tabler-send"></i></button> `;
    }
    if (e.estado === 'publicada') {
      acciones += `<button onclick="confirmarCerrar(${e.id})" class="btn btn-icon btn-sm btn-outline-warning" title="Cerrar"><i class="ti tabler-lock"></i></button> `;
    }
    @endcan
    @can('encuesta.responder')
    if (e.estado === 'publicada') {
      acciones += `<a href="{{ route('encuestas.index') }}/${e.id}/responder" class="btn btn-icon btn-sm btn-outline-primary" title="Responder"><i class="ti tabler-pencil-check"></i></a> `;
    }
    @endcan
    @can('encuesta.eliminar')
    if (e.estado === 'borrador' || e.estado === 'archivada') {
      acciones += `<button onclick="confirmarEliminar(${e.id})" class="btn btn-icon btn-sm btn-outline-danger" title="Eliminar"><i class="ti tabler-trash"></i></button>`;
    }
    @endcan

    return `<tr>
      <td><span class="text-muted small">${e.id}</span></td>
      <td><strong>${e.titulo}</strong></td>
      <td>${modLabels[e.modulo] || e.modulo}</td>
      <td><span class="badge bg-label-${estColors[e.estado] || 'secondary'}">${e.estado.charAt(0).toUpperCase() + e.estado.slice(1)}</span></td>
      <td class="small">${fechas}</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="progress flex-grow-1" style="height:6px;min-width:70px">
            <div class="progress-bar bg-success" style="width:${pct}%"></div>
          </div>
          <small class="text-muted">${e.completadas_count}/${e.respuestas_count}</small>
        </div>
      </td>
      <td class="small">${e.creador?.name || '-'}</td>
      <td class="text-center">${acciones || '<span class="text-muted small">—</span>'}</td>
    </tr>`;
  }).join('');
}

function renderPaginacion(resp) {
  const el = document.getElementById('paginacion');
  if (resp.last_page <= 1) { el.innerHTML = ''; return; }
  let html = '<nav><ul class="pagination pagination-sm mb-0">';
  for (let p = 1; p <= resp.last_page; p++) {
    html += `<li class="page-item ${p === resp.current_page ? 'active' : ''}">
      <button class="page-link" onclick="irPagina(${p})">${p}</button></li>`;
  }
  html += '</ul></nav>';
  el.innerHTML = html;
}

function irPagina(p) { currentPage = p; cargarTabla(); }

function confirmarPublicar(id) {
  document.getElementById('formPublicar').action = ROUTE_BASE + '/' + id + '/publicar';
  new bootstrap.Modal(document.getElementById('modalPublicar')).show();
}
function confirmarEliminar(id) {
  document.getElementById('formEliminar').action = ROUTE_BASE + '/' + id;
  new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
function confirmarCerrar(id) {
  document.getElementById('formCerrar').action = ROUTE_BASE + '/' + id + '/cerrar';
  new bootstrap.Modal(document.getElementById('modalCerrar')).show();
}

let debounce;
document.getElementById('buscar').addEventListener('input', () => {
  clearTimeout(debounce); debounce = setTimeout(() => { currentPage = 1; cargarTabla(); }, 400);
});
['filtroEstado', 'filtroModulo'].forEach(id =>
  document.getElementById(id).addEventListener('change', () => { currentPage = 1; cargarTabla(); })
);

document.addEventListener('DOMContentLoaded', cargarTabla);
</script>
@endsection
