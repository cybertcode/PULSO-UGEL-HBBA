@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Seguimiento — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-style')
<style>
/* ── Layout ── */
.seg-header { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.75rem; }
.seg-title  { font-size:1.35rem;font-weight:700;margin:0;line-height:1.2; }
.seg-sub    { font-size:.82rem;color:var(--bs-secondary-color);margin:0; }

/* ── Módulo tabs ── */
.mod-tabs { display:flex;gap:.5rem;background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:12px;padding:4px; }
.mod-tab  { padding:.45rem 1.1rem;border-radius:9px;border:none;background:transparent;font-size:.8rem;font-weight:600;
            letter-spacing:.03em;cursor:pointer;transition:all .18s;color:var(--bs-secondary-color); }
.mod-tab.active { background:var(--bs-primary);color:#fff;box-shadow:0 2px 8px rgba(var(--bs-primary-rgb),.35); }
.mod-tab:hover:not(.active) { background:var(--bs-tertiary-bg); }

/* ── Año selector ── */
.anio-select { border-radius:9px;border:1px solid var(--bs-border-color);padding:.35rem .8rem;font-size:.82rem;font-weight:600;
               background:var(--bs-body-bg);color:var(--bs-body-color);cursor:pointer; }

/* ── KPI cards ── */
.kpi-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem; }
@media(max-width:900px){ .kpi-grid{grid-template-columns:repeat(2,1fr);} }
@media(max-width:500px){ .kpi-grid{grid-template-columns:1fr 1fr;} }

.kpi-card { border-radius:16px;border:none;overflow:hidden;transition:transform .18s,box-shadow .18s;position:relative; }
.kpi-card:hover { transform:translateY(-3px);box-shadow:0 10px 32px rgba(0,0,0,.13); }
.kpi-card .card-body { padding:1.4rem; }
.kpi-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;
            font-size:1.35rem;flex-shrink:0;background:rgba(255,255,255,.22); }
.kpi-value { font-size:2.1rem;font-weight:800;line-height:1;color:#fff; }
.kpi-label { font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:rgba(255,255,255,.78); }
.kpi-sub   { font-size:.75rem;color:rgba(255,255,255,.60);margin-top:.2rem; }
.kpi-progress { height:4px;border-radius:2px;background:rgba(255,255,255,.25);margin-top:.75rem;overflow:hidden; }
.kpi-progress-bar { height:100%;background:#fff;border-radius:2px;transition:width .6s ease; }

.grad-blue   { background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); }
.grad-green  { background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%); }
.grad-red    { background:linear-gradient(135deg,#e52d27 0%,#b31217 100%); }
.grad-orange { background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%); }
.grad-teal   { background:linear-gradient(135deg,#0acffe 0%,#495aff 100%); }
.grad-pink   { background:linear-gradient(135deg,#f953c6 0%,#b91d73 100%); }

/* ── Mini KPI módulo ── */
.mod-kpi-grid { display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem; }
.mod-kpi-card { border-radius:14px;border:1px solid var(--bs-border-color);overflow:hidden; }
.mod-kpi-card .card-body { padding:1.25rem; }
.mod-kpi-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem; }
.mod-kpi-badge  { font-size:.68rem;font-weight:700;padding:.3rem .65rem;border-radius:6px;letter-spacing:.04em; }
.mod-kpi-stats  { display:flex;gap:1.25rem; }
.mod-stat-val   { font-size:1.5rem;font-weight:800;line-height:1; }
.mod-stat-lbl   { font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--bs-secondary-color); }
.mod-ring       { width:64px;height:64px;flex-shrink:0; }

/* ── Section cards ── */
.section-card { border-radius:14px;border:1px solid var(--bs-border-color); }
.section-card .card-header { border-radius:14px 14px 0 0;background:transparent;border-bottom:1px solid var(--bs-border-color);padding:.85rem 1.25rem; }
.list-row { transition:background .12s; }
.list-row:hover { background:var(--bs-tertiary-bg); }
.progress-thin { height:7px;border-radius:4px;background:var(--bs-tertiary-bg); }

/* ── Estado chip ── */
.chip-sci        { background:rgba(var(--bs-primary-rgb),.12);color:var(--bs-primary);border-radius:6px;padding:.2rem .55rem;font-size:.65rem;font-weight:700;letter-spacing:.04em; }
.chip-integridad { background:rgba(255,159,67,.15);color:#ff9f43;border-radius:6px;padding:.2rem .55rem;font-size:.65rem;font-weight:700;letter-spacing:.04em; }

/* ── Loading overlay ── */
.seg-loading { opacity:.5;pointer-events:none;transition:opacity .2s; }
</style>
@endsection

@section('content')

{{-- ── Header ─────────────────────────────────────────────────────────────── --}}
<div class="seg-header">
  <div>
    <nav aria-label="breadcrumb" class="mb-1">
      <ol class="breadcrumb mb-0" style="font-size:.78rem">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home" style="font-size:.9rem"></i></a></li>
        <li class="breadcrumb-item active">Panel de Seguimiento</li>
      </ol>
    </nav>
    <h1 class="seg-title">Panel de Seguimiento</h1>
    <p class="seg-sub"><i class="ti tabler-refresh" style="font-size:.8rem"></i> Datos en tiempo real · {{ $hoy->format('d \d\e F \d\e Y') }}</p>
  </div>
  <div class="d-flex align-items-center gap-2 flex-wrap">
    {{-- Tabs módulo --}}
    <div class="mod-tabs" id="mod-tabs" role="tablist">
      <button class="mod-tab {{ $modulo === 'ambos' ? 'active' : '' }}" data-mod="ambos">Ambos</button>
      <button class="mod-tab {{ $modulo === 'sci' ? 'active' : '' }}" data-mod="sci">SCI</button>
      <button class="mod-tab {{ $modulo === 'integridad' ? 'active' : '' }}" data-mod="integridad">Integridad</button>
    </div>
    {{-- Año --}}
    <select class="anio-select" id="sel-anio">
      @foreach($anios as $a)
        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
      @endforeach
    </select>
    {{-- Acciones --}}
    <div class="d-flex gap-2">
      <a href="{{ route('cumplimiento.responsables') }}" class="btn btn-sm btn-label-primary">
        <i class="ti tabler-users me-1"></i>Responsables
      </a>
      <a href="{{ route('cumplimiento.sin-evidencia') }}" class="btn btn-sm btn-label-warning">
        <i class="ti tabler-file-off me-1"></i>Sin Evidencia
      </a>
    </div>
  </div>
</div>

{{-- ── KPIs globales ────────────────────────────────────────────────────────── --}}
<div class="kpi-grid" id="kpis-globales">
  <div class="kpi-card grad-blue">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="kpi-icon"><i class="ti tabler-clipboard-list"></i></div>
        <span style="font-size:.72rem;font-weight:700;color:rgba(255,255,255,.7)">{{ $anio }}</span>
      </div>
      <div class="kpi-value" id="kpi-total">{{ $kpis['total'] }}</div>
      <div class="kpi-label mt-1">Total Actividades</div>
      <div class="kpi-sub">Registradas en el período</div>
    </div>
  </div>
  <div class="kpi-card grad-green">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="kpi-icon"><i class="ti tabler-circle-check"></i></div>
        <span style="font-size:.72rem;font-weight:700;color:rgba(255,255,255,.7)" id="kpi-pct">{{ $kpis['porcentaje_global'] }}%</span>
      </div>
      <div class="kpi-value" id="kpi-completadas">{{ $kpis['completadas'] }}</div>
      <div class="kpi-label mt-1">Completadas</div>
      <div class="kpi-progress"><div class="kpi-progress-bar" id="kpi-bar" style="width:{{ $kpis['porcentaje_global'] }}%"></div></div>
    </div>
  </div>
  <div class="kpi-card grad-red">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="kpi-icon"><i class="ti tabler-clock-x"></i></div>
        @if($kpis['vencidas'] > 0)
          <span style="font-size:.68rem;font-weight:700;background:rgba(255,255,255,.25);padding:.2rem .55rem;border-radius:6px;color:#fff">Crítico</span>
        @endif
      </div>
      <div class="kpi-value" id="kpi-vencidas">{{ $kpis['vencidas'] }}</div>
      <div class="kpi-label mt-1">Vencidas</div>
      <div class="kpi-sub">Sin completar tras el plazo</div>
    </div>
  </div>
  <div class="kpi-card grad-orange">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="kpi-icon"><i class="ti tabler-file-off"></i></div>
        @if($kpis['sin_ev'] > 0)
          <span style="font-size:.68rem;font-weight:700;background:rgba(255,255,255,.25);padding:.2rem .55rem;border-radius:6px;color:#fff">Pendiente</span>
        @endif
      </div>
      <div class="kpi-value" id="kpi-sinev">{{ $kpis['sin_ev'] }}</div>
      <div class="kpi-label mt-1">Sin Evidencia</div>
      <div class="kpi-sub">Con avance sin documentar</div>
    </div>
  </div>
</div>

{{-- ── Mini KPIs por módulo ─────────────────────────────────────────────────── --}}
<div class="mod-kpi-grid" id="mod-kpis">
  {{-- SCI --}}
  <div class="mod-kpi-card">
    <div class="card-body">
      <div class="mod-kpi-header">
        <div>
          <span class="chip-sci">CONTROL INTERNO · SCI</span>
          <div class="fw-semibold mt-2" style="font-size:.95rem">Sistema de Control Interno</div>
        </div>
        <div style="position:relative;width:64px;height:64px">
          <svg viewBox="0 0 36 36" class="mod-ring">
            <circle cx="18" cy="18" r="15.9" fill="none" stroke="var(--bs-border-color)" stroke-width="3"/>
            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#696cff" stroke-width="3"
              stroke-dasharray="{{ $kpis_sci['porcentaje'] }} {{ 100 - $kpis_sci['porcentaje'] }}"
              stroke-dashoffset="25" stroke-linecap="round" id="ring-sci"/>
          </svg>
          <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800" id="ring-sci-pct">{{ $kpis_sci['porcentaje'] }}%</div>
        </div>
      </div>
      <div class="mod-kpi-stats">
        <div>
          <div class="mod-stat-val text-primary" id="sci-total">{{ $kpis_sci['total'] }}</div>
          <div class="mod-stat-lbl">Total</div>
        </div>
        <div>
          <div class="mod-stat-val text-success" id="sci-comp">{{ $kpis_sci['completadas'] }}</div>
          <div class="mod-stat-lbl">Completadas</div>
        </div>
        <div>
          <div class="mod-stat-val text-danger" id="sci-venc">{{ $kpis_sci['vencidas'] }}</div>
          <div class="mod-stat-lbl">Vencidas</div>
        </div>
        <div>
          <div class="mod-stat-val text-warning" id="sci-sinev">{{ $kpis_sci['sin_ev'] }}</div>
          <div class="mod-stat-lbl">Sin ev.</div>
        </div>
      </div>
    </div>
  </div>
  {{-- Integridad --}}
  <div class="mod-kpi-card">
    <div class="card-body">
      <div class="mod-kpi-header">
        <div>
          <span class="chip-integridad">INTEGRIDAD · PCM</span>
          <div class="fw-semibold mt-2" style="font-size:.95rem">Modelo de Integridad</div>
        </div>
        <div style="position:relative;width:64px;height:64px">
          <svg viewBox="0 0 36 36" class="mod-ring">
            <circle cx="18" cy="18" r="15.9" fill="none" stroke="var(--bs-border-color)" stroke-width="3"/>
            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#ff9f43" stroke-width="3"
              stroke-dasharray="{{ $kpis_integridad['porcentaje'] }} {{ 100 - $kpis_integridad['porcentaje'] }}"
              stroke-dashoffset="25" stroke-linecap="round" id="ring-int"/>
          </svg>
          <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800" id="ring-int-pct">{{ $kpis_integridad['porcentaje'] }}%</div>
        </div>
      </div>
      <div class="mod-kpi-stats">
        <div>
          <div class="mod-stat-val" style="color:#ff9f43" id="int-total">{{ $kpis_integridad['total'] }}</div>
          <div class="mod-stat-lbl">Total</div>
        </div>
        <div>
          <div class="mod-stat-val text-success" id="int-comp">{{ $kpis_integridad['completadas'] }}</div>
          <div class="mod-stat-lbl">Completadas</div>
        </div>
        <div>
          <div class="mod-stat-val text-danger" id="int-venc">{{ $kpis_integridad['vencidas'] }}</div>
          <div class="mod-stat-lbl">Vencidas</div>
        </div>
        <div>
          <div class="mod-stat-val text-warning" id="int-sinev">{{ $kpis_integridad['sin_ev'] }}</div>
          <div class="mod-stat-lbl">Sin ev.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  {{-- Incumplidores --}}
  <div class="col-xl-6">
    <div class="card section-card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold"><i class="ti tabler-alert-triangle text-danger me-2"></i>Mayores incumplimientos</h6>
        <a href="{{ route('cumplimiento.responsables') }}" class="btn btn-xs btn-label-secondary" style="font-size:.75rem;padding:.3rem .7rem">Ver todos</a>
      </div>
      <div class="card-body p-0" id="lista-incumplidores">
        @forelse($incumplidores as $u)
        <div class="d-flex align-items-center px-4 py-3 border-bottom list-row">
          <div class="avatar avatar-sm me-3 flex-shrink-0">
            @if($u->profile_photo_path)
              <img src="{{ Storage::url($u->profile_photo_path) }}" class="rounded-circle" alt="">
            @else
              <div class="avatar-initial rounded-circle bg-label-danger">{{ strtoupper(substr($u->name,0,1)) }}</div>
            @endif
          </div>
          <div class="flex-grow-1 min-width-0">
            <div class="fw-medium text-truncate" style="font-size:.88rem">{{ $u->name }}</div>
            <small class="text-muted">{{ $u->inc_unidad }} · {{ $u->cargo?->nombre ?? 'Sin cargo' }}</small>
          </div>
          <div class="d-flex gap-2 ms-3 text-nowrap">
            @if($u->inc_vencidas > 0)
              <span class="badge bg-label-danger">{{ $u->inc_vencidas }} venc.</span>
            @endif
            @if($u->inc_sin_ev > 0)
              <span class="badge bg-label-warning">{{ $u->inc_sin_ev }} s/ev.</span>
            @endif
          </div>
        </div>
        @empty
        <div class="text-center text-success py-5">
          <i class="ti tabler-circle-check icon-36px d-block mb-2"></i>
          <span class="fw-medium">Sin incumplimientos registrados</span>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Avance por unidad --}}
  <div class="col-xl-6">
    <div class="card section-card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold"><i class="ti tabler-building-community text-primary me-2"></i>Avance por Unidad Orgánica</h6>
        <a href="{{ route('mon-avance-unidades') }}" class="btn btn-xs btn-label-secondary" style="font-size:.75rem;padding:.3rem .7rem">Detalle</a>
      </div>
      <div class="card-body p-0" id="lista-unidades">
        @foreach($avance_unidades as $u)
        <div class="px-4 py-3 border-bottom list-row">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-medium" style="font-size:.85rem">{{ $u->nombre }}</span>
            <span class="badge bg-label-{{ $u->semaforo }}">{{ $u->porcentaje }}%</span>
          </div>
          <div class="progress-thin">
            <div class="progress-bar bg-{{ $u->semaforo }}" style="width:{{ $u->porcentaje }}%;height:7px;border-radius:4px;transition:width .5s ease"></div>
          </div>
          <div class="d-flex justify-content-between mt-1">
            <small class="text-muted">{{ $u->completadas_act }}/{{ $u->total_act }} completadas</small>
            @if($u->vencidas_act > 0)
              <small class="text-danger"><i class="ti tabler-clock-x" style="font-size:.7rem"></i> {{ $u->vencidas_act }} vencidas</small>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  {{-- Próximas a vencer --}}
  <div class="col-xl-6">
    <div class="card section-card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold"><i class="ti tabler-calendar-exclamation text-warning me-2"></i>Vencen en 15 días</h6>
        <span class="badge bg-warning rounded-pill">{{ $proximas->count() }}</span>
      </div>
      <div class="card-body p-0">
        @forelse($proximas as $act)
        @php $dias = (int) round($act->fecha_limite->diffInDays(now(), false) * -1); @endphp
        <div class="d-flex align-items-center px-4 py-3 border-bottom list-row">
          <div class="me-3 flex-shrink-0">
            @if($act->modulo === 'integridad')
              <span class="chip-integridad">INT</span>
            @else
              <span class="chip-sci">SCI</span>
            @endif
          </div>
          <div class="flex-grow-1 min-width-0">
            <div class="fw-medium text-truncate" style="font-size:.85rem">{{ $act->nombre }}</div>
            <small class="text-muted">{{ $act->unidadOrganica?->sigla }} · {{ $act->responsables->first()?->name ?? '—' }}</small>
          </div>
          <div class="ms-3 text-nowrap text-end">
            <div class="badge {{ $dias <= 3 ? 'bg-danger' : 'bg-warning' }}">{{ $dias }}d</div>
            <div><small class="text-muted">{{ $act->fecha_limite->format('d/m/Y') }}</small></div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
          <i class="ti tabler-calendar-check icon-36px d-block mb-2 text-success"></i>
          Sin vencimientos próximos
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Vencidas recientes --}}
  <div class="col-xl-6">
    <div class="card section-card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold"><i class="ti tabler-clock-x text-danger me-2"></i>Vencidas · últimos 30 días</h6>
        <a href="{{ route('cumplimiento.sin-evidencia') }}" class="btn btn-xs btn-label-danger" style="font-size:.75rem;padding:.3rem .7rem">Sin evidencia</a>
      </div>
      <div class="card-body p-0">
        @forelse($vencidas as $act)
        @php $diasRetraso = (int) round(now()->diffInDays($act->fecha_limite)); @endphp
        <div class="d-flex align-items-center px-4 py-3 border-bottom list-row">
          <div class="me-3 flex-shrink-0">
            @if($act->modulo === 'integridad')
              <span class="chip-integridad">INT</span>
            @else
              <span class="chip-sci">SCI</span>
            @endif
          </div>
          <div class="flex-grow-1 min-width-0">
            <div class="fw-medium text-truncate" style="font-size:.85rem">{{ $act->nombre }}</div>
            <small class="text-muted">{{ $act->unidadOrganica?->sigla }} · {{ $act->responsables->first()?->name ?? '—' }}</small>
          </div>
          <div class="ms-3 text-nowrap text-end">
            <div class="badge bg-danger">+{{ $diasRetraso }}d</div>
            <div><small class="text-muted">{{ $act->fecha_limite->format('d/m/Y') }}</small></div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
          <i class="ti tabler-circle-check icon-36px d-block mb-2 text-success"></i>
          Sin vencimientos recientes
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
(function () {
  const RUTA = @json(route('cumplimiento.panel'));
  let moduloActual = @json($modulo);
  let anioActual   = @json($anio);

  // ── Tabs de módulo ──────────────────────────────────────────────────────
  document.querySelectorAll('.mod-tab').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.mod-tab').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      moduloActual = this.dataset.mod;
      recargar();
    });
  });

  document.getElementById('sel-anio').addEventListener('change', function () {
    anioActual = this.value;
    recargar();
  });

  function recargar() {
    const params = new URLSearchParams({ modulo: moduloActual, anio: anioActual });
    window.location.href = RUTA + '?' + params.toString();
  }
})();
</script>
@endsection
