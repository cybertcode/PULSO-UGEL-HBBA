@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Encuestas — PULSO UGEL')

@section('page-style')
<style>
.kpi-enc { border-radius: 12px; border: none; transition: transform .15s, box-shadow .15s; }
.kpi-enc:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.09); }

.tbl-enc td, .tbl-enc th { padding: .42rem .85rem !important; vertical-align: middle; }
.tbl-enc thead th {
  font-size: .68rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
  color: #8a8899; background: #f8f7fa; white-space: nowrap;
  border-bottom: 1px solid rgba(0,0,0,.07) !important;
}
.tbl-enc tbody tr { border-bottom: 1px solid rgba(0,0,0,.04) !important; transition: background .1s; }
.tbl-enc tbody tr:hover { background: rgba(105,108,255,.035) !important; }

.badge-sci        { background: #dbeafe; color: #1d4ed8; font-size: .67rem; font-weight: 700; padding: .25em .6em; border-radius: 6px; }
.badge-integridad { background: #dcfce7; color: #166534; font-size: .67rem; font-weight: 700; padding: .25em .6em; border-radius: 6px; }
.badge-ambos      { background: #f3e8ff; color: #6d28d9; font-size: .67rem; font-weight: 700; padding: .25em .6em; border-radius: 6px; }

.enc-titulo { max-width: 210px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fecha-rango { font-size: .74rem; color: #6e6b7b; line-height: 1.5; white-space: nowrap; }
.prog-wrap { min-width: 90px; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <h4 class="fw-bold mb-0"><span class="text-muted fw-light">PULSO UGEL /</span> Encuestas</h4>
      <p class="text-muted mb-0 small">Gestión de encuestas institucionales</p>
    </div>
    @can('encuesta.crear')
    <a href="{{ route('encuestas.crear') }}" class="btn btn-primary btn-sm px-3">
      <i class="ti tabler-plus me-1"></i> Nueva Encuesta
    </a>
    @endcan
  </div>

  {{-- KPI Cards --}}
  <div class="row g-3 mb-4">
    @php
      $kpis = [
        ['val' => $stats['total'],         'label' => 'Total',           'icon' => 'tabler-clipboard-list',      'color' => 'primary'],
        ['val' => $stats['borrador'],       'label' => 'Borrador',        'icon' => 'tabler-pencil',              'color' => 'secondary'],
        ['val' => $stats['publicadas'],     'label' => 'Publicadas',      'icon' => 'tabler-send',                'color' => 'success'],
        ['val' => $stats['cerradas'],       'label' => 'Cerradas',        'icon' => 'tabler-lock',                'color' => 'warning'],
        ['val' => $stats['mis_pendientes'], 'label' => 'Mis pendientes',  'icon' => 'tabler-clock-exclamation',   'color' => 'danger'],
      ];
    @endphp
    @foreach($kpis as $kpi)
    <div class="col-6 col-sm-4 col-xl">
      <div class="card kpi-enc shadow-sm h-100">
        <div class="card-body py-3 px-3 d-flex align-items-center gap-3">
          <div class="avatar avatar-sm flex-shrink-0">
            <span class="avatar-initial rounded bg-label-{{ $kpi['color'] }}">
              <i class="ti {{ $kpi['icon'] }}"></i>
            </span>
          </div>
          <div>
            <div class="fw-bold fs-5 text-{{ $kpi['color'] }} lh-1 mb-1">{{ $kpi['val'] }}</div>
            <div class="text-muted" style="font-size:.72rem">{{ $kpi['label'] }}</div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Filtros --}}
  <div class="card shadow-sm mb-3 border-0">
    <div class="card-body py-2 px-3 d-flex flex-wrap gap-2 align-items-center">
      <div class="input-group input-group-sm" style="max-width:210px">
        <span class="input-group-text bg-transparent border-end-0"><i class="ti tabler-search text-muted" style="font-size:.85rem"></i></span>
        <input type="text" id="buscar" class="form-control border-start-0 ps-0" placeholder="Buscar título...">
      </div>
      <select id="filtroEstado" class="form-select form-select-sm" style="max-width:145px">
        <option value="">Todos los estados</option>
        <option value="borrador">Borrador</option>
        <option value="publicada">Publicada</option>
        <option value="cerrada">Cerrada</option>
        <option value="archivada">Archivada</option>
      </select>
      <select id="filtroModulo" class="form-select form-select-sm" style="max-width:155px">
        <option value="">Todos los módulos</option>
        <option value="sci">SCI</option>
        <option value="integridad">Integridad</option>
        <option value="ambos">SCI + Integridad</option>
      </select>
      <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="currentPage=1;cargarTabla()">
        <i class="ti tabler-refresh me-1"></i>Recargar
      </button>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table tbl-enc mb-0">
          <thead>
            <tr>
              <th style="width:38px">#</th>
              <th>Título</th>
              <th>Módulo</th>
              <th>Estado</th>
              <th>Período</th>
              <th>Participación</th>
              <th>Creador</th>
              <th class="text-center" style="width:80px">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyEncuestas">
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm me-2 text-primary"></div>Cargando...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer py-2 d-flex justify-content-between align-items-center">
      <small class="text-muted" id="infoRegistros"></small>
      <div id="paginacion"></div>
    </div>
  </div>

</div>

{{-- Modal Publicar --}}
<div class="modal fade" id="modalPublicar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header pb-2">
        <h6 class="modal-title fw-bold"><i class="ti tabler-send me-1 text-success"></i>Publicar encuesta</h6>
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-2 small">¿Publicar esta encuesta? Se enviarán alertas a los destinatarios y no podrás editar las preguntas.</div>
      <div class="modal-footer pt-2">
        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="formPublicar" method="POST">@csrf
          <button type="submit" class="btn btn-sm btn-success"><i class="ti tabler-send me-1"></i>Publicar</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal Cerrar --}}
<div class="modal fade" id="modalCerrar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header pb-2">
        <h6 class="modal-title fw-bold"><i class="ti tabler-lock me-1 text-warning"></i>Cerrar encuesta</h6>
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-2 small">¿Cerrar esta encuesta? Ya no se podrán registrar nuevas respuestas.</div>
      <div class="modal-footer pt-2">
        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="formCerrar" method="POST">@csrf
          <button type="submit" class="btn btn-sm btn-warning"><i class="ti tabler-lock me-1"></i>Cerrar</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal Eliminar --}}
<div class="modal fade" id="modalEliminar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold text-danger"><i class="ti tabler-trash me-1"></i>Eliminar encuesta</h6>
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-2 small">¿Eliminar esta encuesta? Esta acción no se puede deshacer.</div>
      <div class="modal-footer border-0 pt-2">
        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="formEliminar" method="POST">@csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-danger"><i class="ti tabler-trash me-1"></i>Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
let currentPage = 1;
const BASE = '{{ url("encuestas") }}';

function fmtFecha(str) {
  if (!str) return null;
  const m = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
  const [y, mo, d] = str.split('-');
  return `${parseInt(d)} ${m[parseInt(mo)-1]}. ${y}`;
}

function escHtml(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function cargarTabla() {
  const buscar = document.getElementById('buscar').value;
  const estado = document.getElementById('filtroEstado').value;
  const modulo = document.getElementById('filtroModulo').value;
  const url    = BASE + '/data?buscar=' + encodeURIComponent(buscar)
               + '&estado=' + estado + '&modulo=' + modulo + '&page=' + currentPage;

  document.getElementById('tbodyEncuestas').innerHTML =
    '<tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2 text-primary"></div>Cargando...</td></tr>';

  fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(resp => { renderTabla(resp.data); renderPaginacion(resp); });
}

function renderTabla(rows) {
  const tbody = document.getElementById('tbodyEncuestas');
  if (!rows || rows.length === 0) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted"><i class="ti tabler-mood-empty me-1"></i>No se encontraron encuestas.</td></tr>';
    return;
  }

  const modBadge = {
    sci:        '<span class="badge-sci">SCI</span>',
    integridad: '<span class="badge-integridad">Integridad</span>',
    ambos:      '<span class="badge-ambos">SCI + Int.</span>',
  };
  const estColor = { borrador:'secondary', publicada:'success', cerrada:'warning', archivada:'danger' };
  const estLabel = { borrador:'Borrador', publicada:'Publicada', cerrada:'Cerrada', archivada:'Archivada' };

  tbody.innerHTML = rows.map(e => {
    // Período
    const fi = fmtFecha(e.fecha_inicio), ff = fmtFecha(e.fecha_fin);
    let periodo;
    if (fi && ff)       periodo = `<div class="fecha-rango">${fi}<br><span style="font-size:.67rem;color:#aaa">hasta ${ff}</span></div>`;
    else if (fi)        periodo = `<div class="fecha-rango">Desde ${fi}</div>`;
    else if (ff)        periodo = `<div class="fecha-rango">Hasta ${ff}</div>`;
    else                periodo = `<span class="text-muted" style="font-size:.73rem">Sin fecha</span>`;

    // Participación
    const pct = e.respuestas_count > 0 ? Math.round(e.completadas_count / e.respuestas_count * 100) : 0;
    const barColor = pct >= 80 ? 'success' : pct >= 40 ? 'warning' : 'danger';
    const prog = e.respuestas_count > 0
      ? `<div class="prog-wrap">
           <div class="d-flex justify-content-between mb-1" style="font-size:.67rem;color:#aaa">
             <span>${e.completadas_count}/${e.respuestas_count}</span><span>${pct}%</span>
           </div>
           <div class="progress" style="height:5px"><div class="progress-bar bg-${barColor}" style="width:${pct}%"></div></div>
         </div>`
      : '<span class="text-muted" style="font-size:.72rem">Sin asignar</span>';

    const creador = (e.creador?.name || '—').split(' ').slice(0,2).join(' ');

    // ── Botones de acción: patrón btn-label-* con iconos, igual que el resto del sistema
    let btns = '<div class="d-flex gap-1 justify-content-center">';
    let hayBotones = false;

    @can('encuesta.ver')
    if (e.estado === 'publicada' || e.estado === 'cerrada') {
      btns += `<a href="${BASE}/${e.id}/resultados" class="btn btn-sm btn-icon btn-label-info" title="Ver resultados"><i class="ti tabler-chart-bar"></i></a>`;
      hayBotones = true;
    }
    @endcan

    @can('encuesta.responder')
    if (e.estado === 'publicada') {
      btns += `<a href="${BASE}/${e.id}/responder" class="btn btn-sm btn-icon btn-label-primary" title="Responder encuesta"><i class="ti tabler-pencil-check"></i></a>`;
      hayBotones = true;
    }
    @endcan

    @can('encuesta.editar')
    if (e.estado === 'borrador') {
      btns += `<a href="${BASE}/${e.id}/editar" class="btn btn-sm btn-icon btn-label-warning" title="Editar"><i class="ti tabler-pencil"></i></a>`;
      hayBotones = true;
    }
    @endcan

    @can('encuesta.publicar')
    if (e.estado === 'borrador') {
      btns += `<button onclick="confirmarPublicar(${e.id})" class="btn btn-sm btn-icon btn-label-success" title="Publicar"><i class="ti tabler-send"></i></button>`;
      hayBotones = true;
    }
    if (e.estado === 'publicada') {
      btns += `<button onclick="confirmarCerrar(${e.id})" class="btn btn-sm btn-icon btn-label-warning" title="Cerrar encuesta"><i class="ti tabler-lock"></i></button>`;
      hayBotones = true;
    }
    @endcan

    @can('encuesta.exportar')
    if (e.estado === 'cerrada' || e.estado === 'publicada') {
      btns += `<a href="${BASE}/${e.id}/exportar" class="btn btn-sm btn-icon btn-label-success" title="Exportar Excel"><i class="ti tabler-file-excel"></i></a>`;
      hayBotones = true;
    }
    @endcan

    @can('encuesta.eliminar')
    if (e.estado === 'borrador' || e.estado === 'archivada') {
      btns += `<button onclick="confirmarEliminar(${e.id})" class="btn btn-sm btn-icon btn-label-danger" title="Eliminar"><i class="ti tabler-trash"></i></button>`;
      hayBotones = true;
    }
    @endcan

    btns += '</div>';
    if (!hayBotones) btns = '<span class="text-muted">—</span>';

    return `<tr>
      <td><span class="text-muted" style="font-size:.72rem">${e.id}</span></td>
      <td><div class="enc-titulo fw-semibold" style="font-size:.85rem" title="${escHtml(e.titulo)}">${escHtml(e.titulo)}</div></td>
      <td>${modBadge[e.modulo] || e.modulo}</td>
      <td><span class="badge bg-label-${estColor[e.estado]||'secondary'}" style="font-size:.68rem">${estLabel[e.estado]||e.estado}</span></td>
      <td>${periodo}</td>
      <td>${prog}</td>
      <td><span style="font-size:.78rem;color:#6e6b7b">${escHtml(creador)}</span></td>
      <td class="text-center">${btns}</td>
    </tr>`;
  }).join('');
}

function renderPaginacion(resp) {
  document.getElementById('infoRegistros').textContent =
    `Mostrando ${resp.from??0}–${resp.to??0} de ${resp.total} encuestas`;

  const el = document.getElementById('paginacion');
  if (resp.last_page <= 1) { el.innerHTML = ''; return; }

  let html = '<nav><ul class="pagination pagination-sm mb-0">';
  html += `<li class="page-item ${resp.current_page===1?'disabled':''}">
    <button class="page-link" onclick="irPagina(${resp.current_page-1})"><i class="ti tabler-chevron-left" style="font-size:.7rem"></i></button></li>`;
  for (let p = 1; p <= resp.last_page; p++) {
    if (p===1||p===resp.last_page||Math.abs(p-resp.current_page)<=1)
      html += `<li class="page-item ${p===resp.current_page?'active':''}"><button class="page-link" onclick="irPagina(${p})">${p}</button></li>`;
    else if (Math.abs(p-resp.current_page)===2)
      html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
  }
  html += `<li class="page-item ${resp.current_page===resp.last_page?'disabled':''}">
    <button class="page-link" onclick="irPagina(${resp.current_page+1})"><i class="ti tabler-chevron-right" style="font-size:.7rem"></i></button></li>`;
  html += '</ul></nav>';
  el.innerHTML = html;
}

function irPagina(p) { currentPage = p; cargarTabla(); }

function confirmarPublicar(id) {
  document.getElementById('formPublicar').action = BASE + '/' + id + '/publicar';
  new bootstrap.Modal(document.getElementById('modalPublicar')).show();
}
function confirmarCerrar(id) {
  document.getElementById('formCerrar').action = BASE + '/' + id + '/cerrar';
  new bootstrap.Modal(document.getElementById('modalCerrar')).show();
}
function confirmarEliminar(id) {
  document.getElementById('formEliminar').action = BASE + '/' + id;
  new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

let debounce;
document.getElementById('buscar').addEventListener('input', () => {
  clearTimeout(debounce); debounce = setTimeout(() => { currentPage=1; cargarTabla(); }, 380);
});
['filtroEstado','filtroModulo'].forEach(id =>
  document.getElementById(id).addEventListener('change', () => { currentPage=1; cargarTabla(); })
);
document.addEventListener('DOMContentLoaded', cargarTabla);
</script>
@endsection
