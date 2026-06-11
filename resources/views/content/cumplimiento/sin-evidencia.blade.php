@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Actividades sin Evidencia — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-style')
<style>
.kpi-card { border-radius:14px;border:none;overflow:hidden;transition:transform .18s,box-shadow .18s; }
.kpi-card:hover { transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.10); }
.kpi-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0; }
.kpi-value { font-size:2rem;font-weight:700;line-height:1; }
.kpi-label { font-size:.72rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;opacity:.75; }
.filter-card { border-radius:14px;border:1px solid rgba(0,0,0,.06); }
.filter-card .form-label { font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6e6b7b; }
.act-table-card { border-radius:14px;border:1px solid rgba(0,0,0,.06);overflow:hidden; }
.act-row-vencida { background:rgba(234,84,85,.04) !important; }
.loading #tabla-spinner { display:flex !important; }
.loading #tabla-body { opacity:.4;pointer-events:none; }
</style>
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cumplimiento.panel') }}">Panel SCI</a></li>
    <li class="breadcrumb-item active">Sin Evidencia</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
  <div>
    <h4 class="mb-1 fw-bold">Actividades sin Evidencia</h4>
    <p class="mb-0 text-muted">Actividades con avance registrado pero sin documentos de respaldo</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('cumplimiento.panel') }}" class="btn btn-sm btn-label-secondary">
      <i class="ti tabler-layout-dashboard me-1"></i>Panel
    </a>
    <a href="{{ route('cumplimiento.responsables') }}" class="btn btn-sm btn-label-primary">
      <i class="ti tabler-users me-1"></i>Por Responsable
    </a>
  </div>
</div>

{{-- KPIs --}}
<div class="row g-4 mb-4">
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.22)"><i class="ti tabler-file-off" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff" id="kpi-total">{{ $stats['total'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">Total sin evidencia</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#e52d27,#b31217)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.22)"><i class="ti tabler-clock-x" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff" id="kpi-vencidas">{{ $stats['vencidas'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">Vencidas sin evidencia</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#0acffe,#495aff)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.22)"><i class="ti tabler-file-time" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff" id="kpi-en-proceso">{{ $stats['en_proceso'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">En proceso / Observado</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#e52d27,#b31217)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.22)"><i class="ti tabler-urgent" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff" id="kpi-alta-prio">{{ $stats['alta_prio'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">Alta prioridad</div>
      </div>
    </div>
  </div>
</div>

<div id="alerta-banner" class="{{ $stats['total'] > 0 ? '' : 'd-none' }} alert alert-warning d-flex align-items-center mb-4">
  <i class="ti tabler-alert-triangle me-3 icon-20px flex-shrink-0"></i>
  <div>
    <strong>Atención:</strong> Las siguientes actividades tienen estado avanzado pero
    <strong>no tienen ningún documento de evidencia registrado</strong>. Solicita su subsanación.
  </div>
</div>

{{-- Filtros en tiempo real --}}
<div class="card filter-card mb-4">
  <div class="card-body py-3">
    <div class="row g-3 align-items-end">
      <div class="col-md-2">
        <label class="form-label">Unidad Orgánica</label>
        <select id="f-unidad" class="form-select form-select-sm select2-filter">
          <option value="">Todas</option>
          @foreach($unidades as $u)
          <option value="{{ $u->id }}" {{ $unidadId == $u->id ? 'selected' : '' }}>{{ $u->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Módulo</label>
        <select id="f-modulo" class="form-select form-select-sm">
          <option value="">Ambos</option>
          <option value="sci" {{ $modulo === 'sci' ? 'selected' : '' }}>SCI</option>
          <option value="integridad" {{ $modulo === 'integridad' ? 'selected' : '' }}>Integridad</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Eje SCI</label>
        <select id="f-eje" class="form-select form-select-sm select2-filter">
          <option value="">Todos</option>
          @foreach($ejes as $e)
          <option value="{{ $e->id }}" {{ $ejeId == $e->id ? 'selected' : '' }}>{{ $e->nombre }} ({{ $e->anio }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Responsable</label>
        <select id="f-responsable" class="form-select form-select-sm select2-filter">
          <option value="">Todos</option>
          @foreach($responsables as $r)
          <option value="{{ $r->id }}" {{ $responsableId == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Prioridad</label>
        <select id="f-prioridad" class="form-select form-select-sm">
          <option value="">Todas</option>
          <option value="alta"  {{ $prioridad === 'alta'  ? 'selected' : '' }}>Alta</option>
          <option value="media" {{ $prioridad === 'media' ? 'selected' : '' }}>Media</option>
          <option value="baja"  {{ $prioridad === 'baja'  ? 'selected' : '' }}>Baja</option>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button id="btn-limpiar" type="button" class="btn btn-sm btn-label-secondary w-100">
          <i class="ti tabler-x me-1"></i>Limpiar
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Tabla --}}
<div class="card act-table-card" id="tabla-wrapper">
  <div class="card-header py-3 d-flex align-items-center justify-content-between">
    <h6 class="mb-0 fw-semibold">Listado de actividades</h6>
    <div class="d-flex align-items-center gap-3">
      <div id="tabla-spinner" class="spinner-border spinner-border-sm text-primary" style="display:none"></div>
      <small class="text-muted" id="contador">{{ $actividades->total() }} resultado(s)</small>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Actividad</th>
            <th>Módulo</th>
            <th>Unidad</th>
            <th>Responsable(s)</th>
            <th>Componente</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Prioridad</th>
            <th class="text-center">Avance</th>
            <th>Fecha límite</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody id="tabla-body">
          @forelse($actividades as $act)
          @php
            $ec = match($act->estado) {
              'completada' => 'success', 'vencida' => 'danger',
              'observado'  => 'info',    default   => 'warning',
            };
            $pc = match($act->prioridad) {
              'alta' => 'danger', 'media' => 'warning', default => 'secondary',
            };
            $vencida = $act->fecha_limite && $act->fecha_limite->lt(now());
            $actComp = $act->modulo === 'integridad'
              ? $act->integridadPregunta?->componente?->nombre
              : $act->sciPregunta?->componente?->nombre;
          @endphp
          <tr class="{{ $act->estado === 'vencida' ? 'act-row-vencida' : '' }}">
            <td style="max-width:220px">
              <div class="fw-medium text-truncate" title="{{ $act->nombre }}">{{ $act->nombre }}</div>
              @if($act->codigo)<small class="text-muted">{{ $act->codigo }}</small>@endif
            </td>
            <td>
              <span class="badge bg-label-{{ $act->modulo === 'integridad' ? 'warning' : 'primary' }}" style="font-size:.65rem">
                {{ strtoupper($act->modulo) }}
              </span>
            </td>
            <td><small>{{ $act->unidadOrganica?->sigla ?? '—' }}</small></td>
            <td>
              @foreach($act->responsables->take(2) as $r)
                <div class="d-flex align-items-center gap-1">
                  <span class="badge bg-label-{{ $r->pivot->tipo === 'principal' ? 'primary' : 'secondary' }} badge-sm">
                    {{ $r->pivot->tipo[0] }}
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
              <div class="d-flex align-items-center gap-2 justify-content-center">
                <div class="progress" style="height:6px;width:50px;border-radius:3px">
                  <div class="progress-bar bg-{{ $ec }}" style="width:{{ $act->avance }}%;border-radius:3px"></div>
                </div>
                <small>{{ $act->avance }}%</small>
              </div>
            </td>
            <td>
              @if($act->fecha_limite)
                <span class="{{ $vencida ? 'text-danger fw-medium' : 'text-muted' }}">
                  {{ $act->fecha_limite->format('d/m/Y') }}
                </span>
                @if($vencida)
                  <br><small class="text-danger">+{{ (int) round(now()->diffInDays($act->fecha_limite)) }}d retraso</small>
                @endif
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              <a href="{{ route('sci-evidencias', ['actividad_id' => $act->id, 'nueva' => 1]) }}"
                 class="btn btn-sm btn-primary">
                <i class="ti tabler-upload me-1"></i>Subir
              </a>
            </td>
          </tr>
          @empty
          <tr id="empty-row">
            <td colspan="10" class="text-center text-muted py-5">
              <i class="ti tabler-file-check icon-36px d-block mb-2 text-success"></i>
              <span class="text-success fw-medium">¡Todo en orden! No hay actividades sin evidencia</span>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($actividades->hasPages())
  <div class="card-footer" id="paginacion">{{ $actividades->links() }}</div>
  @endif
</div>

@endsection

@section('page-script')
<script>
(function () {
  const RUTA = @json(route('cumplimiento.sin-evidencia'));
  const RUTA_EV = @json(route('sci-evidencias'));

  document.querySelectorAll('.select2-filter').forEach(el => {
    $(el).select2({ width: '100%' });
    $(el).on('select2:select select2:unselect', fetchDatos);
  });

  ['f-modulo', 'f-prioridad'].forEach(id =>
    document.getElementById(id).addEventListener('change', fetchDatos)
  );

  document.getElementById('btn-limpiar').addEventListener('click', function () {
    ['f-unidad', 'f-eje', 'f-responsable'].forEach(id => {
      $('#' + id).val('').trigger('change');
    });
    document.getElementById('f-modulo').value    = '';
    document.getElementById('f-prioridad').value = '';
    fetchDatos();
  });

  let debounceTimer;

  function getParams() {
    const p = new URLSearchParams();
    const u = $('#f-unidad').val();     if (u) p.set('unidad_organica_id', u);
    const m = document.getElementById('f-modulo').value; if (m) p.set('modulo', m);
    const e = $('#f-eje').val();        if (e) p.set('eje_id', e);
    const r = $('#f-responsable').val(); if (r) p.set('responsable_id', r);
    const pr = document.getElementById('f-prioridad').value; if (pr) p.set('prioridad', pr);
    return p;
  }

  function fetchDatos() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(async () => {
      document.getElementById('tabla-wrapper').classList.add('loading');
      document.getElementById('tabla-spinner').style.display = 'inline-block';

      try {
        const params = getParams();
        const res = await fetch(RUTA + '?' + params.toString(), {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        renderKpis(data.stats);
        renderTabla(data.actividades);
        // Ocultar paginación (en modo AJAX traemos todos — sin paginar)
        const pag = document.getElementById('paginacion');
        if (pag) pag.style.display = 'none';
        history.replaceState(null, '', RUTA + (params.toString() ? '?' + params.toString() : ''));
      } catch (err) {
        console.error(err);
      } finally {
        document.getElementById('tabla-wrapper').classList.remove('loading');
        document.getElementById('tabla-spinner').style.display = 'none';
      }
    }, 260);
  }

  function renderKpis(s) {
    document.getElementById('kpi-total').textContent      = s.total;
    document.getElementById('kpi-vencidas').textContent   = s.vencidas;
    document.getElementById('kpi-en-proceso').textContent = s.en_proceso;
    document.getElementById('kpi-alta-prio').textContent  = s.alta_prio;
    document.getElementById('alerta-banner').classList.toggle('d-none', s.total === 0);
    document.getElementById('contador').textContent = s.total + ' resultado(s)';
  }

  function renderTabla(rows) {
    const tbody = document.getElementById('tabla-body');

    if (!rows.length) {
      tbody.innerHTML = `<tr><td colspan="10" class="text-center text-muted py-5">
        <i class="ti tabler-file-check icon-36px d-block mb-2 text-success"></i>
        <span class="text-success fw-medium">¡Todo en orden! No hay actividades sin evidencia</span>
      </td></tr>`;
      return;
    }

    tbody.innerHTML = rows.map(act => {
      const modBadge = act.modulo === 'integridad' ? 'warning' : 'primary';
      const modLabel = act.modulo === 'integridad' ? 'INTEGRIDAD' : 'SCI';
      const rowClass = act.estado === 'vencida' ? 'act-row-vencida' : '';
      const respHtml = act.responsables.map(r =>
        `<div class="d-flex align-items-center gap-1">
          <span class="badge bg-label-${r.color} badge-sm">${escHtml(r.tipo)}</span>
          <small>${escHtml(r.name)}</small>
        </div>`
      ).join('');
      const fechaHtml = act.fecha_limite
        ? `<span class="${act.vencida ? 'text-danger fw-medium' : 'text-muted'}">${act.fecha_limite}</span>
           ${act.dias_retraso ? `<br><small class="text-danger">+${act.dias_retraso}d retraso</small>` : ''}`
        : `<span class="text-muted">—</span>`;

      return `<tr class="${rowClass}">
        <td style="max-width:220px">
          <div class="fw-medium text-truncate" title="${escHtml(act.nombre)}">${escHtml(act.nombre)}</div>
          ${act.codigo ? `<small class="text-muted">${escHtml(act.codigo)}</small>` : ''}
        </td>
        <td><span class="badge bg-label-${modBadge}" style="font-size:.65rem">${modLabel}</span></td>
        <td><small>${escHtml(act.unidad)}</small></td>
        <td>${respHtml}</td>
        <td><small>${escHtml(act.componente ?? '—')}</small></td>
        <td class="text-center"><span class="badge bg-label-${act.estado_color}">${escHtml(act.estado_label)}</span></td>
        <td class="text-center"><span class="badge bg-label-${act.prioridad_color}">${ucFirst(act.prioridad)}</span></td>
        <td class="text-center">
          <div class="d-flex align-items-center gap-2 justify-content-center">
            <div class="progress" style="height:6px;width:50px;border-radius:3px">
              <div class="progress-bar bg-${act.estado_color}" style="width:${act.avance}%;border-radius:3px"></div>
            </div>
            <small>${act.avance}%</small>
          </div>
        </td>
        <td>${fechaHtml}</td>
        <td>
          <a href="${RUTA_EV}?actividad_id=${act.id}&nueva=1" class="btn btn-sm btn-primary">
            <i class="ti tabler-upload me-1"></i>Subir
          </a>
        </td>
      </tr>`;
    }).join('');
  }

  function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
})();
</script>
@endsection
