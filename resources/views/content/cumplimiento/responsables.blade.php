@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Seguimiento por Responsable — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-style')
<style>
/* ── Shared layout tokens ── */
.seg-header { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.75rem; }
.seg-title  { font-size:1.3rem;font-weight:700;margin:0; }
.seg-sub    { font-size:.82rem;color:var(--bs-secondary-color);margin:0; }

/* ── Módulo tabs ── */
.mod-tabs { display:flex;gap:.35rem;background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:10px;padding:3px; }
.mod-tab  { padding:.4rem 1rem;border-radius:8px;border:none;background:transparent;font-size:.78rem;font-weight:600;
            letter-spacing:.03em;cursor:pointer;transition:all .18s;color:var(--bs-secondary-color); }
.mod-tab.active { background:var(--bs-primary);color:#fff;box-shadow:0 2px 8px rgba(var(--bs-primary-rgb),.35); }
.mod-tab:hover:not(.active) { background:var(--bs-tertiary-bg); }

/* ── KPI cards ── */
.kpi-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem; }
@media(max-width:860px){ .kpi-grid{grid-template-columns:repeat(2,1fr);} }
.kpi-card { border-radius:14px;border:none;overflow:hidden;transition:transform .18s,box-shadow .18s; }
.kpi-card:hover { transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.12); }
.kpi-card .card-body { padding:1.3rem; }
.kpi-icon  { width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;background:rgba(255,255,255,.22); }
.kpi-value { font-size:1.9rem;font-weight:800;line-height:1;color:#fff; }
.kpi-label { font-size:.65rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:rgba(255,255,255,.75); }

/* ── Filter panel ── */
.filter-panel { border-radius:14px;border:1px solid var(--bs-border-color);margin-bottom:1.5rem; }
.filter-panel .card-body { padding:1rem 1.25rem; }
.filter-label { font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--bs-secondary-color);margin-bottom:.35rem;display:block; }
.filter-panel .form-select, .filter-panel .form-control {
  border-radius:9px;border-color:var(--bs-border-color);font-size:.82rem;
  background:var(--bs-body-bg);color:var(--bs-body-color);
}
.filter-panel .form-select:focus, .filter-panel .form-control:focus {
  border-color:var(--bs-primary);box-shadow:0 0 0 3px rgba(var(--bs-primary-rgb),.12);
}
.btn-filter-clear { border-radius:9px;font-size:.78rem;font-weight:600; }

/* ── Ordenar pills ── */
.order-pills { display:flex;gap:.35rem; }
.order-pill  { padding:.3rem .75rem;border-radius:20px;font-size:.72rem;font-weight:600;cursor:pointer;
               border:1px solid var(--bs-border-color);background:var(--bs-body-bg);color:var(--bs-secondary-color);transition:all .15s; }
.order-pill.active { background:var(--bs-primary);color:#fff;border-color:var(--bs-primary); }

/* ── Tabla responsables ── */
.resp-card { border-radius:14px;border:1px solid var(--bs-border-color);overflow:hidden; }
.resp-card .card-header { background:transparent;border-bottom:1px solid var(--bs-border-color);padding:.85rem 1.25rem; }
.resp-card table thead th { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap; }
.resp-card table tbody tr { transition:background .1s; }
.resp-card table tbody tr:hover { background:var(--bs-tertiary-bg); }

/* ── Progress inline ── */
.pbar-wrap { display:flex;align-items:center;gap:.5rem; }
.pbar-track { flex:1;height:8px;border-radius:4px;background:var(--bs-tertiary-bg);min-width:55px;max-width:80px; }
.pbar-fill  { height:8px;border-radius:4px;transition:width .4s ease; }

/* ── Paginación custom ── */
.seg-pagination { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding:.85rem 1.25rem;border-top:1px solid var(--bs-border-color); }
.pag-info  { font-size:.78rem;color:var(--bs-secondary-color); }
.pag-btns  { display:flex;gap:.35rem; }
.pag-btn   { width:32px;height:32px;border-radius:8px;border:1px solid var(--bs-border-color);background:var(--bs-body-bg);
             display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.8rem;transition:all .15s;
             color:var(--bs-body-color); }
.pag-btn:hover:not(:disabled) { border-color:var(--bs-primary);color:var(--bs-primary); }
.pag-btn.active { background:var(--bs-primary);color:#fff;border-color:var(--bs-primary); }
.pag-btn:disabled { opacity:.4;cursor:not-allowed; }

/* ── Loading ── */
.is-loading { opacity:.45;pointer-events:none;transition:opacity .2s; }
#spinner-resp { display:none; }
.is-loading #spinner-resp { display:inline-block !important; }

/* ── Módulo chips ── */
.chip-sci        { background:rgba(var(--bs-primary-rgb),.12);color:var(--bs-primary);border-radius:6px;padding:.15rem .5rem;font-size:.63rem;font-weight:700;letter-spacing:.04em; }
.chip-integridad { background:rgba(255,159,67,.15);color:#ff9f43;border-radius:6px;padding:.15rem .5rem;font-size:.63rem;font-weight:700;letter-spacing:.04em; }
</style>
@endsection

@section('content')

{{-- ── Header ── --}}
<div class="seg-header">
  <div>
    <nav aria-label="breadcrumb" class="mb-1">
      <ol class="breadcrumb mb-0" style="font-size:.78rem">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home" style="font-size:.9rem"></i></a></li>
        <li class="breadcrumb-item"><a href="{{ route('cumplimiento.panel') }}">Panel</a></li>
        <li class="breadcrumb-item active">Por Responsable</li>
      </ol>
    </nav>
    <h1 class="seg-title">Seguimiento por Responsable</h1>
    <p class="seg-sub">¿Quién está cumpliendo con plazos y evidencias?</p>
  </div>
  <div class="d-flex align-items-center gap-2 flex-wrap">
    {{-- Tabs módulo --}}
    <div class="mod-tabs" id="mod-tabs">
      <button class="mod-tab {{ (!$modulo || $modulo === '') ? 'active' : '' }}" data-mod="">Ambos</button>
      <button class="mod-tab {{ $modulo === 'sci' ? 'active' : '' }}" data-mod="sci">SCI</button>
      <button class="mod-tab {{ $modulo === 'integridad' ? 'active' : '' }}" data-mod="integridad">Integridad</button>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('cumplimiento.panel') }}" class="btn btn-sm btn-label-secondary">
        <i class="ti tabler-layout-dashboard me-1"></i>Panel
      </a>
      <div class="dropdown">
        <button class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown">
          <i class="ti tabler-download me-1"></i>Exportar
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item export-btn" data-formato="excel" href="#">
            <i class="ti tabler-file-spreadsheet me-2 text-success"></i>Excel (.xlsx)
          </a></li>
          <li><a class="dropdown-item export-btn" data-formato="pdf" href="#">
            <i class="ti tabler-file-type-pdf me-2 text-danger"></i>PDF
          </a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

{{-- ── KPIs ── --}}
<div class="kpi-grid">
  <div class="kpi-card" style="background:linear-gradient(135deg,#667eea,#764ba2)">
    <div class="card-body">
      <div class="kpi-icon mb-3"><i class="ti tabler-users"></i></div>
      <div class="kpi-value" id="kpi-responsables">{{ $totales['responsables'] }}</div>
      <div class="kpi-label mt-1">Responsables</div>
    </div>
  </div>
  <div class="kpi-card" style="background:linear-gradient(135deg,#e52d27,#b31217)">
    <div class="card-body">
      <div class="kpi-icon mb-3"><i class="ti tabler-alert-triangle"></i></div>
      <div class="kpi-value" id="kpi-en-riesgo">{{ $totales['en_riesgo'] }}</div>
      <div class="kpi-label mt-1">En riesgo (&lt;50%)</div>
    </div>
  </div>
  <div class="kpi-card" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
    <div class="card-body">
      <div class="kpi-icon mb-3"><i class="ti tabler-file-off"></i></div>
      <div class="kpi-value" id="kpi-sin-ev">{{ $totales['sin_evidencia'] }}</div>
      <div class="kpi-label mt-1">Sin Evidencia</div>
    </div>
  </div>
  <div class="kpi-card" style="background:linear-gradient(135deg,#e52d27,#b31217)">
    <div class="card-body">
      <div class="kpi-icon mb-3"><i class="ti tabler-clock-x"></i></div>
      <div class="kpi-value" id="kpi-vencidas">{{ $totales['vencidas_total'] }}</div>
      <div class="kpi-label mt-1">Vencidas</div>
    </div>
  </div>
</div>

{{-- ── Filtros ── --}}
<div class="card filter-panel">
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-md-3 col-sm-6">
        <label class="filter-label">Unidad Orgánica</label>
        <select id="f-unidad" class="form-select form-select-sm select2-filter">
          <option value="">Todas las unidades</option>
          @foreach($unidades as $u)
            <option value="{{ $u->id }}" {{ $unidadId == $u->id ? 'selected' : '' }}>{{ $u->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 col-sm-6">
        <label class="filter-label">Eje SCI</label>
        <select id="f-eje" class="form-select form-select-sm select2-filter">
          <option value="">Todos los ejes</option>
          @foreach($ejes as $e)
            <option value="{{ $e->id }}" {{ $ejeId == $e->id ? 'selected' : '' }}>{{ $e->nombre }} ({{ $e->anio }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-4">
        <label class="filter-label">Año</label>
        <select id="f-anio" class="form-select form-select-sm">
          @foreach($anios as $a)
            <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-4">
        <label class="filter-label">Ordenar por</label>
        <div class="order-pills">
          <span class="order-pill {{ $orden === 'peor' ? 'active' : '' }}" data-orden="peor" title="Menor cumplimiento primero">↑ Peor</span>
          <span class="order-pill {{ $orden === 'mejor' ? 'active' : '' }}" data-orden="mejor" title="Mayor cumplimiento primero">↓ Mejor</span>
          <span class="order-pill {{ $orden === 'nombre' ? 'active' : '' }}" data-orden="nombre" title="Orden alfabético">A–Z</span>
        </div>
      </div>
      <div class="col-md-2 col-sm-4">
        <label class="filter-label">&nbsp;</label>
        <button id="btn-limpiar" type="button" class="btn btn-sm btn-label-secondary btn-filter-clear w-100">
          <i class="ti tabler-x me-1"></i>Limpiar filtros
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ── Tabla ── --}}
<div class="card resp-card" id="tabla-wrapper">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <h6 class="mb-0 fw-semibold">Detalle por persona</h6>
      <span id="spinner-resp" class="spinner-border spinner-border-sm text-primary"></span>
    </div>
    <small class="text-muted" id="contador-resp">{{ $totales['responsables'] }} responsables</small>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Responsable</th>
            <th>Unidad</th>
            <th style="min-width:170px">Cumplimiento</th>
            <th class="text-center">Completadas</th>
            <th class="text-center">Vencidas</th>
            <th class="text-center">Sin evidencia</th>
            <th class="text-center">Ev. pendiente</th>
            <th class="text-center">Retraso prom.</th>
            <th class="text-center">Estado</th>
          </tr>
        </thead>
        <tbody id="tabla-body">
          @forelse($paginados as $u)
          @php $label = match($u->stat_semaforo) { 'success' => 'Al día', 'warning' => 'En proceso', default => 'En riesgo' }; @endphp
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm flex-shrink-0">
                  @if($u->profile_photo_path)
                    <img src="{{ Storage::url($u->profile_photo_path) }}" class="rounded-circle" alt="">
                  @else
                    <div class="avatar-initial rounded-circle bg-label-{{ $u->stat_semaforo }}">{{ strtoupper(substr($u->name,0,1)) }}</div>
                  @endif
                </div>
                <div>
                  <div class="fw-medium" style="font-size:.88rem">{{ $u->name }}</div>
                  <small class="text-muted">{{ $u->cargo?->nombre ?? 'Sin cargo' }}</small>
                </div>
              </div>
            </td>
            <td><small>{{ $u->unidadOrganica?->sigla ?? '—' }}</small></td>
            <td>
              <div class="pbar-wrap">
                <div class="pbar-track"><div class="pbar-fill bg-{{ $u->stat_semaforo }}" style="width:{{ $u->stat_porcentaje }}%"></div></div>
                <span class="fw-bold text-{{ $u->stat_semaforo }}" style="font-size:.85rem">{{ $u->stat_porcentaje }}%</span>
              </div>
              <small class="text-muted">{{ $u->stat_total }} actividades</small>
            </td>
            <td class="text-center"><span class="badge bg-label-success">{{ $u->stat_completadas }}</span></td>
            <td class="text-center">
              @if($u->stat_vencidas > 0)
                <span class="badge bg-danger">{{ $u->stat_vencidas }}</span>
              @else
                <span class="badge bg-label-secondary">0</span>
              @endif
            </td>
            <td class="text-center">
              @if($u->stat_sin_evidencia > 0)
                <a href="{{ route('cumplimiento.sin-evidencia', ['responsable_id' => $u->id]) }}"
                   class="badge bg-warning text-dark text-decoration-none">{{ $u->stat_sin_evidencia }}</a>
              @else
                <span class="badge bg-label-secondary">0</span>
              @endif
            </td>
            <td class="text-center">
              @if($u->stat_ev_pendiente > 0)
                <span class="badge bg-label-info">{{ $u->stat_ev_pendiente }}</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td class="text-center">
              @if($u->stat_dias_retraso > 0)
                <span class="text-danger fw-medium">{{ $u->stat_dias_retraso }}d</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td class="text-center"><span class="badge bg-label-{{ $u->stat_semaforo }}">{{ $label }}</span></td>
          </tr>
          @empty
          <tr id="empty-row">
            <td colspan="9" class="text-center text-muted py-5">
              <i class="ti tabler-users-off icon-36px d-block mb-2"></i>
              No hay responsables con actividades en el período seleccionado
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Paginación ── --}}
  <div class="seg-pagination" id="paginacion">
    <div class="pag-info" id="pag-info">
      Mostrando <strong>{{ ($pagina - 1) * $porPagina + 1 }}</strong>–<strong>{{ min($pagina * $porPagina, $totales['responsables']) }}</strong>
      de <strong>{{ $totales['responsables'] }}</strong> responsables
    </div>
    <div class="pag-btns" id="pag-btns"></div>
  </div>
</div>

@endsection

@section('page-script')
<script>
window.addEventListener('load', function () {
(function () {
  const RUTA     = @json(route('cumplimiento.responsables'));
  const RUTA_SE  = @json(route('cumplimiento.sin-evidencia'));
  const RUTA_EXP = @json(route('cumplimiento.exportar'));

  let estado = {
    modulo  : @json($modulo ?? ''),
    unidad  : @json((string)($unidadId ?? '')),
    eje     : @json((string)($ejeId ?? '')),
    anio    : @json($anio),
    orden   : @json($orden),
    pagina  : @json($pagina),
    totalPags: @json($totalPags),
    total   : @json($totales['responsables']),
  };

  // ── Select2 ──────────────────────────────────────────────────────────────
  document.querySelectorAll('.select2-filter').forEach(el => {
    $(el).select2({ width: '100%' });
    $(el).on('select2:select select2:unselect', () => { estado.pagina = 1; fetch(); });
  });
  document.getElementById('f-anio').addEventListener('change', () => { estado.pagina = 1; fetch(); });

  // ── Tabs módulo ───────────────────────────────────────────────────────────
  document.querySelectorAll('.mod-tab').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.mod-tab').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      estado.modulo = this.dataset.mod;
      estado.pagina = 1;
      fetch();
    });
  });

  // ── Orden pills ───────────────────────────────────────────────────────────
  document.querySelectorAll('.order-pill').forEach(pill => {
    pill.addEventListener('click', function () {
      document.querySelectorAll('.order-pill').forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      estado.orden = this.dataset.orden;
      estado.pagina = 1;
      fetch();
    });
  });

  // ── Limpiar ───────────────────────────────────────────────────────────────
  document.getElementById('btn-limpiar').addEventListener('click', () => {
    $('#f-unidad').val('').trigger('change');
    $('#f-eje').val('').trigger('change');
    document.getElementById('f-anio').value = String(new Date().getFullYear());
    document.querySelectorAll('.mod-tab')[0].click();
    document.querySelectorAll('.order-pill')[0].click();
  });

  // ── Exportar ──────────────────────────────────────────────────────────────
  document.querySelectorAll('.export-btn').forEach(a => {
    a.addEventListener('click', function (e) {
      e.preventDefault();
      const p = buildParams();
      p.set('formato', this.dataset.formato);
      window.location.href = RUTA_EXP + '?' + p.toString();
    });
  });

  // ── Paginación ────────────────────────────────────────────────────────────
  function renderPaginacion() {
    const { pagina, totalPags, total, pagina: pg } = estado;
    const desde = (pg - 1) * 15 + 1;
    const hasta = Math.min(pg * 15, total);
    document.getElementById('pag-info').innerHTML =
      `Mostrando <strong>${desde}</strong>–<strong>${hasta}</strong> de <strong>${total}</strong> responsables`;

    const btns = document.getElementById('pag-btns');
    btns.innerHTML = '';

    const prev = Object.assign(document.createElement('button'), { className: 'pag-btn', innerHTML: '<i class="ti tabler-chevron-left"></i>' });
    prev.disabled = pagina <= 1;
    prev.addEventListener('click', () => { estado.pagina--; fetch(); });
    btns.appendChild(prev);

    let start = Math.max(1, pagina - 2), end = Math.min(totalPags, start + 4);
    if (end - start < 4) start = Math.max(1, end - 4);

    for (let i = start; i <= end; i++) {
      const b = document.createElement('button');
      b.className = 'pag-btn' + (i === pagina ? ' active' : '');
      b.textContent = i;
      b.addEventListener('click', (pg => () => { estado.pagina = pg; fetch(); })(i));
      btns.appendChild(b);
    }

    const next = Object.assign(document.createElement('button'), { className: 'pag-btn', innerHTML: '<i class="ti tabler-chevron-right"></i>' });
    next.disabled = pagina >= totalPags;
    next.addEventListener('click', () => { estado.pagina++; fetch(); });
    btns.appendChild(next);
  }

  renderPaginacion();

  // ── Fetch ─────────────────────────────────────────────────────────────────
  let timer;
  function buildParams() {
    const p = new URLSearchParams();
    const u = $('#f-unidad').val(); if (u) p.set('unidad_organica_id', u);
    const e = $('#f-eje').val();    if (e) p.set('eje_id', e);
    const a = document.getElementById('f-anio').value; if (a) p.set('anio', a);
    if (estado.modulo) p.set('modulo', estado.modulo);
    if (estado.orden)  p.set('orden', estado.orden);
    p.set('pagina', estado.pagina);
    return p;
  }

  function fetch() {
    clearTimeout(timer);
    timer = setTimeout(async () => {
      const wrapper = document.getElementById('tabla-wrapper');
      wrapper.classList.add('is-loading');
      try {
        const params = buildParams();
        const res  = await window.fetch(RUTA + '?' + params.toString(), {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        estado.total     = data.meta.total;
        estado.totalPags = data.meta.total_pags;
        estado.pagina    = data.meta.pagina;
        renderKpis(data.totales);
        renderTabla(data.responsables);
        renderPaginacion();
        history.replaceState(null, '', RUTA + '?' + params.toString());
      } catch (err) { console.error(err); }
      finally { wrapper.classList.remove('is-loading'); }
    }, 280);
  }

  function renderKpis(t) {
    document.getElementById('kpi-responsables').textContent = t.responsables;
    document.getElementById('kpi-en-riesgo').textContent    = t.en_riesgo;
    document.getElementById('kpi-sin-ev').textContent       = t.sin_evidencia;
    document.getElementById('kpi-vencidas').textContent     = t.vencidas_total;
    document.getElementById('contador-resp').textContent    = t.responsables + ' responsables';
  }

  function semaforoLabel(s) {
    return s === 'success' ? 'Al día' : s === 'warning' ? 'En proceso' : 'En riesgo';
  }

  function renderTabla(rows) {
    const tbody = document.getElementById('tabla-body');
    if (!rows.length) {
      tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted py-5">
        <i class="ti tabler-users-off icon-36px d-block mb-2"></i>
        No hay responsables con actividades en el período seleccionado
      </td></tr>`;
      return;
    }
    tbody.innerHTML = rows.map(u => `
      <tr>
        <td>
          <div class="d-flex align-items-center gap-2">
            <div class="avatar avatar-sm flex-shrink-0">
              <div class="avatar-initial rounded-circle bg-label-${u.semaforo}">${esc(u.inicial)}</div>
            </div>
            <div>
              <div class="fw-medium" style="font-size:.88rem">${esc(u.name)}</div>
              <small class="text-muted">${esc(u.cargo)}</small>
            </div>
          </div>
        </td>
        <td><small>${esc(u.unidad)}</small></td>
        <td>
          <div class="pbar-wrap">
            <div class="pbar-track"><div class="pbar-fill bg-${u.semaforo}" style="width:${u.porcentaje}%"></div></div>
            <span class="fw-bold text-${u.semaforo}" style="font-size:.85rem">${u.porcentaje}%</span>
          </div>
          <small class="text-muted">${u.total} actividades</small>
        </td>
        <td class="text-center"><span class="badge bg-label-success">${u.completadas}</span></td>
        <td class="text-center">
          ${u.vencidas > 0 ? `<span class="badge bg-danger">${u.vencidas}</span>` : `<span class="badge bg-label-secondary">0</span>`}
        </td>
        <td class="text-center">
          ${u.sin_evidencia > 0
            ? `<a href="${RUTA_SE}?responsable_id=${u.id}" class="badge bg-warning text-dark text-decoration-none">${u.sin_evidencia}</a>`
            : `<span class="badge bg-label-secondary">0</span>`}
        </td>
        <td class="text-center">
          ${u.ev_pendiente > 0 ? `<span class="badge bg-label-info">${u.ev_pendiente}</span>` : `<span class="text-muted">—</span>`}
        </td>
        <td class="text-center">
          ${u.dias_retraso > 0 ? `<span class="text-danger fw-medium">${u.dias_retraso}d</span>` : `<span class="text-muted">—</span>`}
        </td>
        <td class="text-center">
          <span class="badge bg-label-${u.semaforo}">${semaforoLabel(u.semaforo)}</span>
        </td>
      </tr>`).join('');
  }

  function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
})();
}); // end window.load
</script>
@endsection
