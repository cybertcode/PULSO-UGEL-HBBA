@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Sin Evidencia / Observadas — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-style')
<style>
/* ── Layout ── */
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

/* ── Módulo mini-stats ── */
.mod-split { display:flex;gap:1rem;margin-bottom:1.5rem; }
.mod-split-card { flex:1;border-radius:12px;border:1px solid var(--bs-border-color);padding:.9rem 1.1rem;display:flex;align-items:center;gap:.85rem; }
.mod-split-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0; }
.mod-split-val  { font-size:1.6rem;font-weight:800;line-height:1; }
.mod-split-lbl  { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--bs-secondary-color); }
@media(max-width:600px){ .mod-split{flex-direction:column;} }

/* ── Filter panel ── */
.filter-panel { border-radius:14px;border:1px solid var(--bs-border-color);margin-bottom:1.5rem; }
.filter-panel .card-body { padding:1rem 1.25rem; }
.filter-label { font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--bs-secondary-color);margin-bottom:.35rem;display:block; }
.filter-panel .form-select { border-radius:9px;font-size:.82rem; }
.filter-panel .form-select:focus { border-color:var(--bs-primary);box-shadow:0 0 0 3px rgba(var(--bs-primary-rgb),.12); }

/* ── Tabla actividades ── */
.act-card { border-radius:14px;border:1px solid var(--bs-border-color);overflow:hidden; }
.act-card .card-header { background:transparent;border-bottom:1px solid var(--bs-border-color);padding:.85rem 1.25rem; }
.act-card table thead th { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap; }
.act-card table tbody tr { transition:background .1s; }
.act-card table tbody tr:hover { background:var(--bs-tertiary-bg); }
.row-vencida { background:rgba(234,84,85,.035) !important; }
.row-vencida:hover { background:rgba(234,84,85,.07) !important; }

/* ── Progress inline ── */
.pbar-mini { display:flex;align-items:center;gap:.4rem; }
.pbar-track-mini { width:44px;height:6px;border-radius:3px;background:var(--bs-tertiary-bg);flex-shrink:0; }
.pbar-fill-mini  { height:6px;border-radius:3px; }

/* ── Chips ── */
.chip-sci        { background:rgba(var(--bs-primary-rgb),.12);color:var(--bs-primary);border-radius:6px;padding:.18rem .5rem;font-size:.63rem;font-weight:700;letter-spacing:.04em;white-space:nowrap; }
.chip-integridad { background:rgba(255,159,67,.15);color:#ff9f43;border-radius:6px;padding:.18rem .5rem;font-size:.63rem;font-weight:700;letter-spacing:.04em;white-space:nowrap; }

/* ── Paginación custom ── */
.seg-pagination { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding:.85rem 1.25rem;border-top:1px solid var(--bs-border-color); }
.pag-info  { font-size:.78rem;color:var(--bs-secondary-color); }
.pag-btns  { display:flex;gap:.35rem;flex-wrap:wrap; }
.pag-btn   { width:32px;height:32px;border-radius:8px;border:1px solid var(--bs-border-color);background:var(--bs-body-bg);
             display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.8rem;transition:all .15s;color:var(--bs-body-color); }
.pag-btn:hover:not(:disabled) { border-color:var(--bs-primary);color:var(--bs-primary); }
.pag-btn.active { background:var(--bs-primary);color:#fff;border-color:var(--bs-primary); }
.pag-btn:disabled { opacity:.4;cursor:not-allowed; }

/* ── Loading ── */
.is-loading { opacity:.45;pointer-events:none;transition:opacity .2s; }
#spinner-act { display:none; }
.is-loading #spinner-act { display:inline-block !important; }
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
        <li class="breadcrumb-item active">Sin Evidencia / Observadas</li>
      </ol>
    </nav>
    <h1 class="seg-title">Actividades sin Evidencia válida</h1>
    <p class="seg-sub">Actividades sin evidencia, con evidencia rechazada o en estado observado · más recientes primero</p>
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
      <a href="{{ route('cumplimiento.responsables') }}" class="btn btn-sm btn-label-primary">
        <i class="ti tabler-users me-1"></i>Responsables
      </a>
    </div>
  </div>
</div>

{{-- ── KPIs ── --}}
<div class="kpi-grid">
  <div class="kpi-card" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
    <div class="card-body">
      <div class="kpi-icon mb-3"><i class="ti tabler-file-off"></i></div>
      <div class="kpi-value" id="kpi-total">{{ $stats['total'] }}</div>
      <div class="kpi-label mt-1">Total sin evidencia</div>
    </div>
  </div>
  <div class="kpi-card" style="background:linear-gradient(135deg,#e52d27,#b31217)">
    <div class="card-body">
      <div class="kpi-icon mb-3"><i class="ti tabler-clock-x"></i></div>
      <div class="kpi-value" id="kpi-vencidas">{{ $stats['vencidas'] }}</div>
      <div class="kpi-label mt-1">Vencidas sin evidencia</div>
    </div>
  </div>
  <div class="kpi-card" style="background:linear-gradient(135deg,#0acffe,#495aff)">
    <div class="card-body">
      <div class="kpi-icon mb-3"><i class="ti tabler-file-time"></i></div>
      <div class="kpi-value" id="kpi-en-proceso">{{ $stats['en_proceso'] }}</div>
      <div class="kpi-label mt-1">En proceso / Observado</div>
    </div>
  </div>
  <div class="kpi-card" style="background:linear-gradient(135deg,#f953c6,#b91d73)">
    <div class="card-body">
      <div class="kpi-icon mb-3"><i class="ti tabler-urgent"></i></div>
      <div class="kpi-value" id="kpi-alta-prio">{{ $stats['alta_prio'] }}</div>
      <div class="kpi-label mt-1">Alta prioridad</div>
    </div>
  </div>
</div>

{{-- ── Split módulo ── --}}
<div class="mod-split" id="mod-split">
  <div class="mod-split-card">
    <div class="mod-split-icon" style="background:rgba(var(--bs-primary-rgb),.12)">
      <i class="ti tabler-clipboard-check text-primary" style="font-size:1.2rem"></i>
    </div>
    <div>
      <div class="mod-split-val text-primary" id="split-sci">{{ $stats['sci'] }}</div>
      <div class="mod-split-lbl">SCI — sin evidencia</div>
    </div>
  </div>
  <div class="mod-split-card">
    <div class="mod-split-icon" style="background:rgba(255,159,67,.12)">
      <i class="ti tabler-shield-check" style="color:#ff9f43;font-size:1.2rem"></i>
    </div>
    <div>
      <div class="mod-split-val" style="color:#ff9f43" id="split-int">{{ $stats['integridad'] }}</div>
      <div class="mod-split-lbl">Integridad — sin evidencia</div>
    </div>
  </div>
</div>

{{-- ── Alerta banner ── --}}
<div id="alerta-banner" class="{{ $stats['total'] > 0 ? '' : 'd-none' }} alert alert-warning d-flex align-items-center mb-4" style="border-radius:12px">
  <i class="ti tabler-alert-triangle me-3 icon-20px flex-shrink-0"></i>
  <div>
    <strong>Atención:</strong> Las siguientes actividades tienen avance registrado pero
    <strong>no tienen ningún documento de evidencia</strong>. Se muestran de más reciente a más antigua.
  </div>
</div>

{{-- ── Filtros ── --}}
<div class="card filter-panel">
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-md-3 col-sm-6">
        <label class="filter-label">Unidad Orgánica</label>
        <select id="f-unidad" class="form-select form-select-sm select2-filter">
          <option value="">Todas</option>
          @foreach($unidades as $u)
            <option value="{{ $u->id }}" {{ $unidadId == $u->id ? 'selected' : '' }}>{{ $u->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-6">
        <label class="filter-label">Eje SCI</label>
        <select id="f-eje" class="form-select form-select-sm select2-filter">
          <option value="">Todos</option>
          @foreach($ejes as $e)
            <option value="{{ $e->id }}" {{ $ejeId == $e->id ? 'selected' : '' }}>{{ $e->nombre }} ({{ $e->anio }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-4">
        <label class="filter-label">Responsable</label>
        <select id="f-responsable" class="form-select form-select-sm select2-filter">
          <option value="">Todos</option>
          @foreach($responsables as $r)
            <option value="{{ $r->id }}" {{ $responsableId == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-4">
        <label class="filter-label">Prioridad</label>
        <select id="f-prioridad" class="form-select form-select-sm">
          <option value="">Todas</option>
          <option value="alta"  {{ $prioridad === 'alta'  ? 'selected' : '' }}>Alta</option>
          <option value="media" {{ $prioridad === 'media' ? 'selected' : '' }}>Media</option>
          <option value="baja"  {{ $prioridad === 'baja'  ? 'selected' : '' }}>Baja</option>
        </select>
      </div>
      <div class="col-md-1 col-sm-4">
        <label class="filter-label">Año</label>
        <select id="f-anio" class="form-select form-select-sm">
          @foreach($anios as $a)
            <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-6">
        <label class="filter-label">&nbsp;</label>
        <button id="btn-limpiar" type="button" class="btn btn-sm btn-label-secondary w-100" style="border-radius:9px">
          <i class="ti tabler-x me-1"></i>Limpiar
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ── Tabla ── --}}
<div class="card act-card" id="tabla-wrapper">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <h6 class="mb-0 fw-semibold">Listado de actividades</h6>
      <span id="spinner-act" class="spinner-border spinner-border-sm text-primary"></span>
    </div>
    <small class="text-muted" id="contador">{{ $actividades->total() }} resultado(s)</small>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Actividad</th>
            <th class="text-center">Módulo</th>
            <th>Unidad</th>
            <th>Responsable(s)</th>
            <th>Componente</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Prioridad</th>
            <th class="text-center">Avance</th>
            <th>Fecha límite</th>
            <th>Registrado</th>
            <th class="text-center">Acción</th>
          </tr>
        </thead>
        <tbody id="tabla-body">
          @forelse($actividades as $act)
          @php
            $ec      = match($act->estado) { 'completada' => 'success','vencida' => 'danger','observado' => 'info', default => 'warning' };
            $pc      = match($act->prioridad) { 'alta' => 'danger','media' => 'warning', default => 'secondary' };
            $vencida = $act->fecha_limite && $act->fecha_limite->lt(now());
            $actComp = $act->modulo === 'integridad'
              ? $act->integridadPregunta?->componente?->nombre
              : $act->sciPregunta?->componente?->nombre;
          @endphp
          <tr class="{{ $act->estado === 'vencida' ? 'row-vencida' : '' }}">
            <td style="max-width:220px">
              <div class="fw-medium text-truncate" style="font-size:.87rem" title="{{ $act->nombre }}">{{ $act->nombre }}</div>
              @if($act->codigo)<small class="text-muted">{{ $act->codigo }}</small>@endif
            </td>
            <td class="text-center">
              @if($act->modulo === 'integridad')
                <span class="chip-integridad">INT</span>
              @else
                <span class="chip-sci">SCI</span>
              @endif
            </td>
            <td><small>{{ $act->unidadOrganica?->sigla ?? '—' }}</small></td>
            <td>
              @foreach($act->responsables->take(2) as $r)
                <div class="d-flex align-items-center gap-1">
                  <span class="badge bg-label-{{ $r->pivot->tipo === 'principal' ? 'primary' : 'secondary' }} badge-sm" style="font-size:.58rem">
                    {{ strtoupper(substr($r->pivot->tipo,0,1)) }}
                  </span>
                  <small>{{ $r->name }}</small>
                </div>
              @endforeach
              @if($act->responsables->count() > 2)
                <small class="text-muted">+{{ $act->responsables->count() - 2 }} más</small>
              @endif
            </td>
            <td><small>{{ $actComp ?? '—' }}</small></td>
            <td class="text-center"><span class="badge bg-label-{{ $ec }}">{{ $act->estado_label }}</span></td>
            <td class="text-center"><span class="badge bg-label-{{ $pc }}">{{ ucfirst($act->prioridad) }}</span></td>
            <td class="text-center">
              <div class="pbar-mini">
                <div class="pbar-track-mini"><div class="pbar-fill-mini bg-{{ $ec }}" style="width:{{ $act->avance }}%"></div></div>
                <small>{{ $act->avance }}%</small>
              </div>
            </td>
            <td>
              @if($act->fecha_limite)
                <span class="{{ $vencida ? 'text-danger fw-medium' : 'text-muted' }}" style="font-size:.83rem">
                  {{ $act->fecha_limite->format('d/m/Y') }}
                </span>
                @if($vencida)
                  <br><small class="text-danger">+{{ (int) round(now()->diffInDays($act->fecha_limite)) }}d</small>
                @endif
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td><small class="text-muted">{{ $act->created_at->format('d/m/Y') }}</small></td>
            <td class="text-center">
              @if($act->estado === 'observado')
                <a href="{{ route('sci-evidencias', ['actividad_id' => $act->id, 'modulo' => $act->modulo, 'estado' => 'rechazado']) }}"
                   class="btn btn-sm btn-danger" style="border-radius:8px;font-size:.78rem;padding:.3rem .7rem">
                  <i class="ti tabler-refresh-alert me-1"></i>Corregir
                </a>
              @else
                <a href="{{ route('sci-evidencias', ['actividad_id' => $act->id, 'nueva' => 1]) }}"
                   class="btn btn-sm btn-primary" style="border-radius:8px;font-size:.78rem;padding:.3rem .7rem">
                  <i class="ti tabler-upload me-1"></i>Subir
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr id="empty-row">
            <td colspan="11" class="text-center text-muted py-5">
              <i class="ti tabler-file-check icon-36px d-block mb-2 text-success"></i>
              <span class="text-success fw-medium">¡Todo en orden! No hay actividades sin evidencia</span>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ── Paginación ── --}}
  <div class="seg-pagination" id="paginacion">
    <div class="pag-info" id="pag-info">
      @if($actividades->total() > 0)
        Mostrando <strong>{{ $actividades->firstItem() }}</strong>–<strong>{{ $actividades->lastItem() }}</strong>
        de <strong>{{ $actividades->total() }}</strong> actividades
      @endif
    </div>
    <div class="pag-btns" id="pag-btns"></div>
  </div>
</div>

@endsection

@section('page-script')
<script>
window.addEventListener('load', function () {
(function () {
  const RUTA    = @json(route('cumplimiento.sin-evidencia'));
  const RUTA_EV = @json(route('sci-evidencias'));
  const POR_PAG = 20;

  let estado = {
    modulo      : @json($modulo ?? ''),
    pagina      : {{ $actividades->currentPage() }},
    totalPags   : {{ $actividades->lastPage() }},
    total       : {{ $actividades->total() }},
  };

  // ── Select2 ──────────────────────────────────────────────────────────────
  document.querySelectorAll('.select2-filter').forEach(el => {
    $(el).select2({ width: '100%' });
    $(el).on('select2:select select2:unselect', () => { estado.pagina = 1; fetch(); });
  });
  ['f-prioridad','f-anio'].forEach(id =>
    document.getElementById(id).addEventListener('change', () => { estado.pagina = 1; fetch(); })
  );

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

  // ── Limpiar ───────────────────────────────────────────────────────────────
  document.getElementById('btn-limpiar').addEventListener('click', () => {
    ['f-unidad','f-eje','f-responsable'].forEach(id => $('#' + id).val('').trigger('change'));
    document.getElementById('f-prioridad').value = '';
    document.getElementById('f-anio').value = String(new Date().getFullYear());
    document.querySelectorAll('.mod-tab')[0].click();
  });

  // ── Paginación ────────────────────────────────────────────────────────────
  function renderPaginacion() {
    const { pagina, totalPags, total } = estado;
    if (!total) { document.getElementById('pag-info').textContent = '0 resultados'; document.getElementById('pag-btns').innerHTML = ''; return; }
    const desde = (pagina - 1) * POR_PAG + 1;
    const hasta = Math.min(pagina * POR_PAG, total);
    document.getElementById('pag-info').innerHTML =
      `Mostrando <strong>${desde}</strong>–<strong>${hasta}</strong> de <strong>${total}</strong> actividades`;

    const btns = document.getElementById('pag-btns');
    btns.innerHTML = '';

    const prev = Object.assign(document.createElement('button'), { className:'pag-btn', innerHTML:'<i class="ti tabler-chevron-left"></i>' });
    prev.disabled = pagina <= 1;
    prev.addEventListener('click', () => { estado.pagina--; fetch(); });
    btns.appendChild(prev);

    let start = Math.max(1, pagina - 2), end = Math.min(totalPags, start + 4);
    if (end - start < 4) start = Math.max(1, end - 4);
    for (let i = start; i <= end; i++) {
      const b = document.createElement('button');
      b.className = 'pag-btn' + (i === pagina ? ' active' : '');
      b.textContent = i;
      b.addEventListener('click', (n => () => { estado.pagina = n; fetch(); })(i));
      btns.appendChild(b);
    }

    const next = Object.assign(document.createElement('button'), { className:'pag-btn', innerHTML:'<i class="ti tabler-chevron-right"></i>' });
    next.disabled = pagina >= totalPags;
    next.addEventListener('click', () => { estado.pagina++; fetch(); });
    btns.appendChild(next);
  }
  renderPaginacion();

  // ── Fetch ─────────────────────────────────────────────────────────────────
  let timer;
  function buildParams() {
    const p = new URLSearchParams();
    const u  = $('#f-unidad').val();      if (u)  p.set('unidad_organica_id', u);
    const e  = $('#f-eje').val();         if (e)  p.set('eje_id', e);
    const r  = $('#f-responsable').val(); if (r)  p.set('responsable_id', r);
    const pr = document.getElementById('f-prioridad').value; if (pr) p.set('prioridad', pr);
    const a  = document.getElementById('f-anio').value;      if (a)  p.set('anio', a);
    if (estado.modulo) p.set('modulo', estado.modulo);
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
        renderKpis(data.stats);
        renderTabla(data.actividades);
        renderPaginacion();
        history.replaceState(null, '', RUTA + '?' + params.toString());
      } catch (err) { console.error(err); }
      finally { wrapper.classList.remove('is-loading'); }
    }, 280);
  }

  function renderKpis(s) {
    document.getElementById('kpi-total').textContent      = s.total;
    document.getElementById('kpi-vencidas').textContent   = s.vencidas;
    document.getElementById('kpi-en-proceso').textContent = s.en_proceso;
    document.getElementById('kpi-alta-prio').textContent  = s.alta_prio;
    document.getElementById('split-sci').textContent      = s.sci;
    document.getElementById('split-int').textContent      = s.integridad;
    document.getElementById('alerta-banner').classList.toggle('d-none', s.total === 0);
    document.getElementById('contador').textContent = s.total + ' resultado(s)';
  }

  function renderTabla(rows) {
    const tbody = document.getElementById('tabla-body');
    if (!rows.length) {
      tbody.innerHTML = `<tr><td colspan="11" class="text-center text-muted py-5">
        <i class="ti tabler-file-check icon-36px d-block mb-2 text-success"></i>
        <span class="text-success fw-medium">¡Todo en orden! No hay actividades sin evidencia</span>
      </td></tr>`;
      return;
    }
    tbody.innerHTML = rows.map(act => {
      const isInt   = act.modulo === 'integridad';
      const rowCls  = act.estado === 'vencida' ? 'row-vencida' : '';
      const respHtml = act.responsables.map(r =>
        `<div class="d-flex align-items-center gap-1">
           <span class="badge bg-label-${r.color}" style="font-size:.58rem">${esc(r.tipo)}</span>
           <small>${esc(r.name)}</small>
         </div>`
      ).join('');
      const fechaHtml = act.fecha_limite
        ? `<span class="${act.vencida ? 'text-danger fw-medium' : 'text-muted'}" style="font-size:.83rem">${act.fecha_limite}</span>
           ${act.dias_retraso ? `<br><small class="text-danger">+${act.dias_retraso}d</small>` : ''}`
        : `<span class="text-muted">—</span>`;
      return `<tr class="${rowCls}">
        <td style="max-width:220px">
          <div class="fw-medium text-truncate" style="font-size:.87rem" title="${esc(act.nombre)}">${esc(act.nombre)}</div>
          ${act.codigo ? `<small class="text-muted">${esc(act.codigo)}</small>` : ''}
        </td>
        <td class="text-center">
          ${isInt ? `<span class="chip-integridad">INT</span>` : `<span class="chip-sci">SCI</span>`}
        </td>
        <td><small>${esc(act.unidad)}</small></td>
        <td>${respHtml}</td>
        <td><small>${esc(act.componente ?? '—')}</small></td>
        <td class="text-center"><span class="badge bg-label-${act.estado_color}">${esc(act.estado_label)}</span></td>
        <td class="text-center"><span class="badge bg-label-${act.prioridad_color}">${ucFirst(act.prioridad)}</span></td>
        <td class="text-center">
          <div class="pbar-mini">
            <div class="pbar-track-mini"><div class="pbar-fill-mini bg-${act.estado_color}" style="width:${act.avance}%"></div></div>
            <small>${act.avance}%</small>
          </div>
        </td>
        <td>${fechaHtml}</td>
        <td><small class="text-muted">${esc(act.created_at ?? '—')}</small></td>
        <td class="text-center">
          ${act.estado === 'observado'
            ? `<a href="${RUTA_EV}?actividad_id=${act.id}&modulo=${act.modulo}&estado=rechazado"
                 class="btn btn-sm btn-danger" style="border-radius:8px;font-size:.78rem;padding:.3rem .7rem">
                <i class="ti tabler-refresh-alert me-1"></i>Corregir
               </a>`
            : `<a href="${RUTA_EV}?actividad_id=${act.id}&nueva=1"
                 class="btn btn-sm btn-primary" style="border-radius:8px;font-size:.78rem;padding:.3rem .7rem">
                <i class="ti tabler-upload me-1"></i>Subir
               </a>`
          }
        </td>
      </tr>`;
    }).join('');
  }

  function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
})();
}); // end window.load
</script>
@endsection
