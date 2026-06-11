@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Cumplimiento por Responsable — PULSO UGEL')

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
.resp-table-card { border-radius:14px;border:1px solid rgba(0,0,0,.06);overflow:hidden; }
#tabla-spinner { display:none; }
.loading #tabla-spinner { display:flex; }
.loading #tabla-body { opacity:.4;pointer-events:none; }
</style>
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cumplimiento.panel') }}">Panel SCI</a></li>
    <li class="breadcrumb-item active">Por Responsable</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
  <div>
    <h4 class="mb-1 fw-bold">Cumplimiento por Responsable</h4>
    <p class="mb-0 text-muted">¿Quién está cumpliendo con plazos y evidencias?</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('cumplimiento.panel') }}" class="btn btn-sm btn-label-secondary">
      <i class="ti tabler-layout-dashboard me-1"></i>Panel
    </a>
    <a href="{{ route('cumplimiento.sin-evidencia') }}" class="btn btn-sm btn-label-warning">
      <i class="ti tabler-file-off me-1"></i>Sin Evidencia
    </a>
    <div class="dropdown">
      <button class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown">
        <i class="ti tabler-download me-1"></i>Exportar
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item export-link" data-formato="excel" href="#">
          <i class="ti tabler-file-spreadsheet me-2 text-success"></i>Excel (.xlsx)
        </a></li>
        <li><a class="dropdown-item export-link" data-formato="pdf" href="#">
          <i class="ti tabler-file-type-pdf me-2 text-danger"></i>PDF
        </a></li>
      </ul>
    </div>
  </div>
</div>

{{-- KPIs (actualizados por JS) --}}
<div class="row g-4 mb-4">
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#667eea,#764ba2)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.2)"><i class="ti tabler-users" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff" id="kpi-responsables">{{ $totales['responsables'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">Responsables</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#e52d27,#b31217)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.2)"><i class="ti tabler-alert-triangle" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff" id="kpi-en-riesgo">{{ $totales['en_riesgo'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">En riesgo (&lt;50%)</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.2)"><i class="ti tabler-file-off" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff" id="kpi-sin-ev">{{ $totales['sin_evidencia'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">Sin Evidencia</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#e52d27,#b31217)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.2)"><i class="ti tabler-clock-x" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff" id="kpi-vencidas">{{ $totales['vencidas_total'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">Vencidas</div>
      </div>
    </div>
  </div>
</div>

{{-- Filtros en tiempo real --}}
<div class="card filter-card mb-4">
  <div class="card-body py-3">
    <div class="row g-3 align-items-end" id="filtros">
      <div class="col-md-3">
        <label class="form-label">Unidad Orgánica</label>
        <select id="f-unidad" class="form-select form-select-sm select2-filter">
          <option value="">Todas las unidades</option>
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
      <div class="col-md-3">
        <label class="form-label">Eje SCI</label>
        <select id="f-eje" class="form-select form-select-sm select2-filter">
          <option value="">Todos los ejes</option>
          @foreach($ejes as $e)
          <option value="{{ $e->id }}" {{ $ejeId == $e->id ? 'selected' : '' }}>{{ $e->nombre }} ({{ $e->anio }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Año</label>
        <select id="f-anio" class="form-select form-select-sm">
          @foreach($anios as $a)
          <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
          @endforeach
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

{{-- Tabla de responsables --}}
<div class="card resp-table-card" id="tabla-wrapper">
  <div class="card-header py-3 d-flex align-items-center justify-content-between">
    <h6 class="mb-0 fw-semibold">Detalle por persona <small class="text-muted fw-normal">(menor cumplimiento primero)</small></h6>
    <div class="d-flex align-items-center gap-3">
      <div id="tabla-spinner" class="spinner-border spinner-border-sm text-primary"></div>
      <small class="text-muted" id="contador-resp">{{ $responsables->count() }} responsables</small>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Responsable</th>
            <th>Unidad</th>
            <th class="text-center" style="min-width:160px">Cumplimiento</th>
            <th class="text-center">Completadas</th>
            <th class="text-center">Vencidas</th>
            <th class="text-center">Sin evidencia</th>
            <th class="text-center">Ev. pendiente</th>
            <th class="text-center">Retraso prom.</th>
            <th class="text-center">Estado</th>
          </tr>
        </thead>
        <tbody id="tabla-body">
          @forelse($responsables as $u)
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm flex-shrink-0">
                  @if($u->profile_photo_path)
                    <img src="{{ Storage::url($u->profile_photo_path) }}" class="rounded-circle" alt="">
                  @else
                    <div class="avatar-initial rounded-circle bg-label-{{ $u->stat_semaforo }}">
                      {{ strtoupper(substr($u->name,0,1)) }}
                    </div>
                  @endif
                </div>
                <div>
                  <div class="fw-medium">{{ $u->name }}</div>
                  <small class="text-muted">{{ $u->cargo?->nombre ?? 'Sin cargo' }}</small>
                </div>
              </div>
            </td>
            <td><small>{{ $u->unidadOrganica?->sigla ?? '—' }}</small></td>
            <td class="text-center">
              <div class="d-flex align-items-center gap-2 justify-content-center">
                <div class="progress flex-grow-1" style="height:8px;min-width:60px;max-width:80px;border-radius:4px">
                  <div class="progress-bar bg-{{ $u->stat_semaforo }}" style="width:{{ $u->stat_porcentaje }}%;border-radius:4px"></div>
                </div>
                <span class="fw-bold text-{{ $u->stat_semaforo }}">{{ $u->stat_porcentaje }}%</span>
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
            <td class="text-center">
              @php
                $label = match($u->stat_semaforo) {
                  'success' => 'Al día', 'warning' => 'En proceso', default => 'En riesgo',
                };
              @endphp
              <span class="badge bg-label-{{ $u->stat_semaforo }}">{{ $label }}</span>
            </td>
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
</div>

@endsection

@section('page-script')
<script>
(function () {
  const RUTA = @json(route('cumplimiento.responsables'));
  const RUTA_SE = @json(route('cumplimiento.sin-evidencia'));
  const RUTA_EXP = @json(route('cumplimiento.exportar'));

  // ── Select2 ───────────────────────────────────────────────────────────────
  document.querySelectorAll('.select2-filter').forEach(el => {
    $(el).select2({ width: '100%' });
    $(el).on('select2:select select2:unselect', fetchDatos);
  });

  document.getElementById('f-modulo').addEventListener('change', fetchDatos);
  document.getElementById('f-anio').addEventListener('change', fetchDatos);

  document.getElementById('btn-limpiar').addEventListener('click', function () {
    $('#f-unidad').val('').trigger('change');
    $('#f-eje').val('').trigger('change');
    document.getElementById('f-modulo').value = '';
    document.getElementById('f-anio').value = '{{ now()->year }}';
    fetchDatos();
  });

  // ── Exportar ──────────────────────────────────────────────────────────────
  document.querySelectorAll('.export-link').forEach(a => {
    a.addEventListener('click', function (e) {
      e.preventDefault();
      const p = getParams();
      p.set('formato', this.dataset.formato);
      window.location.href = RUTA_EXP + '?' + p.toString();
    });
  });

  // ── Fetch principal ───────────────────────────────────────────────────────
  let debounceTimer;

  function getParams() {
    const p = new URLSearchParams();
    const u = $('#f-unidad').val(); if (u) p.set('unidad_organica_id', u);
    const m = document.getElementById('f-modulo').value; if (m) p.set('modulo', m);
    const e = $('#f-eje').val(); if (e) p.set('eje_id', e);
    const a = document.getElementById('f-anio').value; if (a) p.set('anio', a);
    return p;
  }

  function fetchDatos() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(async () => {
      const wrapper = document.getElementById('tabla-wrapper');
      wrapper.classList.add('loading');

      try {
        const params = getParams();
        const res = await fetch(RUTA + '?' + params.toString(), {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        renderTabla(data.responsables);
        renderKpis(data.totales);

        // Sincroniza URL sin recargar
        history.replaceState(null, '', RUTA + (params.toString() ? '?' + params.toString() : ''));
      } catch (err) {
        console.error(err);
      } finally {
        wrapper.classList.remove('loading');
      }
    }, 260);
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
              <div class="avatar-initial rounded-circle bg-label-${u.semaforo}">${u.inicial}</div>
            </div>
            <div>
              <div class="fw-medium">${escHtml(u.name)}</div>
              <small class="text-muted">${escHtml(u.cargo)}</small>
            </div>
          </div>
        </td>
        <td><small>${escHtml(u.unidad)}</small></td>
        <td class="text-center">
          <div class="d-flex align-items-center gap-2 justify-content-center">
            <div class="progress flex-grow-1" style="height:8px;min-width:60px;max-width:80px;border-radius:4px">
              <div class="progress-bar bg-${u.semaforo}" style="width:${u.porcentaje}%;border-radius:4px"></div>
            </div>
            <span class="fw-bold text-${u.semaforo}">${u.porcentaje}%</span>
          </div>
          <small class="text-muted">${u.total} actividades</small>
        </td>
        <td class="text-center"><span class="badge bg-label-success">${u.completadas}</span></td>
        <td class="text-center">
          ${u.vencidas > 0
            ? `<span class="badge bg-danger">${u.vencidas}</span>`
            : `<span class="badge bg-label-secondary">0</span>`}
        </td>
        <td class="text-center">
          ${u.sin_evidencia > 0
            ? `<a href="${RUTA_SE}?responsable_id=${u.id}" class="badge bg-warning text-dark text-decoration-none">${u.sin_evidencia}</a>`
            : `<span class="badge bg-label-secondary">0</span>`}
        </td>
        <td class="text-center">
          ${u.ev_pendiente > 0
            ? `<span class="badge bg-label-info">${u.ev_pendiente}</span>`
            : `<span class="text-muted">—</span>`}
        </td>
        <td class="text-center">
          ${u.dias_retraso > 0
            ? `<span class="text-danger fw-medium">${u.dias_retraso}d</span>`
            : `<span class="text-muted">—</span>`}
        </td>
        <td class="text-center">
          <span class="badge bg-label-${u.semaforo}">${semaforoLabel(u.semaforo)}</span>
        </td>
      </tr>`
    ).join('');
  }

  function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
})();
</script>
@endsection
