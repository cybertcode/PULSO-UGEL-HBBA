@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
$pct = $stats['total'] ? $stats['avance_global'] : 0;
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Panel Principal — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-style')
<style>
/* ══ HEADER BANNER ══════════════════════════════════════════ */
.dash-hero {
  background: linear-gradient(135deg, var(--bs-primary) 0%, color-mix(in srgb, var(--bs-primary) 60%, #0d1117) 100%);
  border-radius: 20px;
  padding: 1.75rem 2rem 1.5rem;
  color: #fff;
  position: relative;
  overflow: hidden;
}
.dash-hero::before {
  content: '';
  position: absolute;
  top: -60px; right: -60px;
  width: 260px; height: 260px;
  border-radius: 50%;
  background: rgba(255,255,255,.05);
}
.dash-hero::after {
  content: '';
  position: absolute;
  bottom: -80px; right: 80px;
  width: 180px; height: 180px;
  border-radius: 50%;
  background: rgba(255,255,255,.04);
}
.dash-hero-title { font-size: 1.4rem; font-weight: 800; color: #fff; margin: 0; }
.dash-hero-sub   { font-size: .82rem; opacity: .78; margin: .3rem 0 0; }
.dash-date-pill  {
  background: rgba(255,255,255,.15);
  border: 1px solid rgba(255,255,255,.3);
  border-radius: 50px;
  padding: .3rem .9rem;
  font-size: .75rem;
  color: #fff;
  white-space: nowrap;
}
.dash-hs-val { font-size: 1.9rem; font-weight: 800; line-height: 1; color: #fff; }
.dash-hs-lbl { font-size: .7rem; opacity: .78; margin-top: .2rem; }
.dash-hs-bar { height: 3px; border-radius: 2px; background: rgba(255,255,255,.18); margin-top: .55rem; }
.dash-hs-fill { height: 100%; border-radius: 2px; transition: width .6s ease; }

/* ══ MÓDULOS (SCI / INTEGRIDAD) ════════════════════════════ */
.mod-card {
  border-radius: 16px;
  border: 1px solid rgba(0,0,0,.07);
  padding: 1.25rem 1.5rem;
  transition: box-shadow .18s, transform .18s;
}
.mod-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.1); transform: translateY(-2px); }
.mod-card.sci-card      { border-left: 4px solid var(--bs-primary); }
.mod-card.integ-card    { border-left: 4px solid var(--bs-warning); }
.mod-kpi-val  { font-size: 2.6rem; font-weight: 800; line-height: 1; }
.mod-kpi-sub  { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; opacity: .65; }
.mod-mini-kpi { text-align: center; }
.mod-mini-kpi .val { font-size: 1.35rem; font-weight: 800; line-height: 1; }
.mod-mini-kpi .lbl { font-size: .68rem; opacity: .7; margin-top: .15rem; }
.mod-sep      { width: 1px; background: rgba(0,0,0,.08); align-self: stretch; margin: .5rem 0; }
.mod-prog-track { height: 8px; border-radius: 4px; background: rgba(0,0,0,.06); }
.mod-prog-fill  { height: 100%; border-radius: 4px; transition: width .6s ease; }

/* ══ KPI STRIP ═════════════════════════════════════════════ */
.kpi-strip { border-radius: 14px; border: none; overflow: hidden; transition: transform .18s, box-shadow .18s; }
.kpi-strip:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.13); }
.kpi-strip .card-body { padding: 1rem 1.1rem; }
.kpi-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0; background: rgba(255,255,255,.22); color: #fff; }
.kpi-val  { font-size: 1.85rem; font-weight: 800; line-height: 1; color: #fff; }
.kpi-lbl  { font-size: .68rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: rgba(255,255,255,.72); }
.kpi-g-blue   { background: linear-gradient(135deg,#667eea,#764ba2); }
.kpi-g-red    { background: linear-gradient(135deg,#e52d27,#b31217); }
.kpi-g-orange { background: linear-gradient(135deg,#f7971e,#ffd200); }
.kpi-g-cyan   { background: linear-gradient(135deg,#0acffe,#495aff); }
.kpi-g-purple { background: linear-gradient(135deg,#a855f7,#7c3aed); }

/* ══ SECTION CARDS ══════════════════════════════════════════ */
.sec-card { border-radius: 14px; border: 1px solid rgba(0,0,0,.07); overflow: hidden; }
.sec-card .card-header { background: transparent; border-bottom: 1px solid rgba(0,0,0,.06); padding: 1rem 1.25rem .75rem; }
.sec-card .card-header h6 { font-size: .9rem; font-weight: 700; margin: 0; }
.sec-card .card-header p  { font-size: .73rem; color: var(--bs-secondary-color); margin: .1rem 0 0; }
.sec-footer { padding: .65rem 1.25rem; background: var(--bs-tertiary-bg); border-top: 1px solid rgba(0,0,0,.06); font-size: .78rem; }

/* ══ LISTA ROWS ═════════════════════════════════════════════ */
.list-row { padding: .8rem 1.25rem; transition: background .12s; border-bottom: 1px solid rgba(0,0,0,.05); }
.list-row:last-child { border-bottom: none; }
.list-row:hover { background: var(--bs-tertiary-bg); }
.act-ico { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .9rem; flex-shrink: 0; }

/* ══ TABLA ══════════════════════════════════════════════════ */
.dash-tbl thead th { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--bs-secondary-color); background: var(--bs-tertiary-bg); padding: .55rem .85rem; white-space: nowrap; border-bottom: 1px solid rgba(0,0,0,.07); }
.dash-tbl tbody td { padding: .55rem .85rem; font-size: .82rem; vertical-align: middle; }
.dash-tbl tbody tr:hover { background: var(--bs-tertiary-bg); }

/* ══ DOT LEGEND ════════════════════════════════════════════ */
.leg-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; flex-shrink: 0; }

/* ══ ALERTA ROW ════════════════════════════════════════════ */
.al-row { display: flex; align-items: flex-start; gap: .85rem; padding: .85rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,.05); transition: background .12s; }
.al-row:last-child { border-bottom: none; }
.al-row:hover { background: var(--bs-tertiary-bg); }

/* ══ PROGRESS BARS ══════════════════════════════════════════ */
.prog-sm { height: 5px; border-radius: 3px; }

/* ══ BADGE MODULO ═══════════════════════════════════════════ */
.badge-sci   { background: rgba(var(--bs-primary-rgb),.12); color: var(--bs-primary); font-size: .65rem; padding: .2em .55em; border-radius: 5px; font-weight: 700; }
.badge-integ { background: rgba(var(--bs-warning-rgb),.15); color: var(--bs-warning); font-size: .65rem; padding: .2em .55em; border-radius: 5px; font-weight: 700; }
</style>
@endsection

@section('content')

{{-- ══ 1. HERO HEADER ══════════════════════════════════════════════════════════ --}}
<div class="dash-hero mb-4">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
    <div>
      <p class="dash-date-pill d-inline-flex align-items-center gap-1 mb-2">
        <i class="ti tabler-calendar-event"></i>
        {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}
      </p>
      <h1 class="dash-hero-title mb-1">
        Bienvenido/a, {{ Str::of($user->name)->explode(' ')->first() }}
        @if($mis_actividades_count > 0)
        <span class="badge" style="font-size:.6rem;font-weight:700;background:rgba(255,255,255,.22);color:#fff;padding:.35em .7em;border-radius:8px;vertical-align:middle">
          {{ $mis_actividades_count }} pendientes
        </span>
        @endif
      </h1>
      <p class="dash-hero-sub">Sistema de Seguimiento · Control Interno y Modelo de Integridad · UGEL Huacaybamba</p>
    </div>
    <div class="d-flex gap-2 flex-wrap align-self-start">
      <a href="{{ route('sci-semaforo') }}" class="btn btn-sm" style="background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:10px">
        <i class="ti tabler-traffic-lights me-1"></i>Semáforo
      </a>
      <a href="{{ route('mis-actividades') }}" class="btn btn-sm" style="background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:10px">
        <i class="ti tabler-checklist me-1"></i>Mis Actividades
      </a>
    </div>
  </div>

  {{-- Mini stats globales --}}
  <div class="row g-3 g-md-4 mt-1">
    @php
    $hstats = [
      ['val' => $stats['total'],       'lbl' => 'Total actividades', 'fill_w' => 100,  'fill_c' => 'rgba(255,255,255,.4)'],
      ['val' => $stats['completadas'],  'lbl' => 'Completadas · '.$pct.'%', 'fill_w' => $pct, 'fill_c' => '#28c76f'],
      ['val' => $stats['vencidas'],     'lbl' => 'Vencidas',         'fill_w' => $stats['total'] ? round($stats['vencidas']/$stats['total']*100) : 0, 'fill_c' => '#ea5455'],
      ['val' => $stats['alertas'],      'lbl' => 'Alertas activas',  'fill_w' => min(100,$stats['alertas']*10), 'fill_c' => '#ff9f43'],
    ];
    @endphp
    @foreach($hstats as $hs)
    <div class="col-6 col-md-3">
      <div class="dash-hs-val">{{ $hs['val'] }}</div>
      <div class="dash-hs-lbl">{{ $hs['lbl'] }}</div>
      <div class="dash-hs-bar"><div class="dash-hs-fill" style="width:{{ $hs['fill_w'] }}%;background:{{ $hs['fill_c'] }}"></div></div>
    </div>
    @endforeach
  </div>
</div>

{{-- ══ 2. MÓDULOS SCI + INTEGRIDAD ════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

  {{-- SCI --}}
  <div class="col-12 col-lg-6">
    <a href="{{ route('sci-control-interno') }}" class="text-decoration-none">
      <div class="card mod-card sci-card mb-0">
        <div class="card-body p-0">
          <div class="d-flex align-items-center justify-content-between p-3 pb-2 border-bottom" style="border-color:rgba(0,0,0,.06)!important">
            <div class="d-flex align-items-center gap-2">
              <div class="rounded-2 bg-label-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px">
                <i class="ti tabler-clipboard-check text-primary" style="font-size:1.1rem"></i>
              </div>
              <div>
                <div class="fw-bold" style="font-size:.88rem">Sistema de Control Interno</div>
                <div class="text-muted" style="font-size:.72rem">SCI · {{ $anio }}</div>
              </div>
            </div>
            <span class="badge bg-label-primary rounded-pill" style="font-size:.72rem">{{ $stats['avance_sci'] }}% avance</span>
          </div>
          <div class="d-flex align-items-center gap-0 p-3 pt-2">
            <div class="mod-mini-kpi flex-grow-1">
              <div class="val text-primary">{{ $stats['total_sci'] }}</div>
              <div class="lbl">Total</div>
            </div>
            <div class="mod-sep"></div>
            <div class="mod-mini-kpi flex-grow-1">
              <div class="val text-success">{{ $stats['completadas_sci'] }}</div>
              <div class="lbl">Completadas</div>
            </div>
            <div class="mod-sep"></div>
            <div class="mod-mini-kpi flex-grow-1">
              <div class="val text-danger">{{ $stats['vencidas_sci'] }}</div>
              <div class="lbl">Vencidas</div>
            </div>
            <div class="mod-sep"></div>
            <div class="mod-mini-kpi flex-grow-1 pe-1">
              <div class="val text-warning">{{ $stats['total_sci'] - $stats['completadas_sci'] - $stats['vencidas_sci'] }}</div>
              <div class="lbl">En curso</div>
            </div>
          </div>
          <div class="px-3 pb-3">
            <div class="mod-prog-track">
              <div class="mod-prog-fill bg-primary" style="width:{{ $stats['avance_sci'] }}%"></div>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  {{-- INTEGRIDAD --}}
  <div class="col-12 col-lg-6">
    <a href="{{ route('sci-modelo-integridad') }}" class="text-decoration-none">
      <div class="card mod-card integ-card mb-0">
        <div class="card-body p-0">
          <div class="d-flex align-items-center justify-content-between p-3 pb-2 border-bottom" style="border-color:rgba(0,0,0,.06)!important">
            <div class="d-flex align-items-center gap-2">
              <div class="rounded-2 bg-label-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px">
                <i class="ti tabler-shield-check text-warning" style="font-size:1.1rem"></i>
              </div>
              <div>
                <div class="fw-bold" style="font-size:.88rem">Modelo de Integridad</div>
                <div class="text-muted" style="font-size:.72rem">PCM · {{ $anio }}</div>
              </div>
            </div>
            <span class="badge bg-label-warning rounded-pill" style="font-size:.72rem">{{ $stats['avance_int'] }}% avance</span>
          </div>
          <div class="d-flex align-items-center gap-0 p-3 pt-2">
            <div class="mod-mini-kpi flex-grow-1">
              <div class="val text-warning">{{ $stats['total_int'] }}</div>
              <div class="lbl">Total</div>
            </div>
            <div class="mod-sep"></div>
            <div class="mod-mini-kpi flex-grow-1">
              <div class="val text-success">{{ $stats['completadas_int'] }}</div>
              <div class="lbl">Completadas</div>
            </div>
            <div class="mod-sep"></div>
            <div class="mod-mini-kpi flex-grow-1">
              <div class="val text-danger">{{ $stats['vencidas_int'] }}</div>
              <div class="lbl">Vencidas</div>
            </div>
            <div class="mod-sep"></div>
            <div class="mod-mini-kpi flex-grow-1 pe-1">
              <div class="val text-primary">{{ $stats['total_int'] - $stats['completadas_int'] - $stats['vencidas_int'] }}</div>
              <div class="lbl">En curso</div>
            </div>
          </div>
          <div class="px-3 pb-3">
            <div class="mod-prog-track">
              <div class="mod-prog-fill bg-warning" style="width:{{ $stats['avance_int'] }}%"></div>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

</div>

{{-- ══ 3. KPI STRIP ════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
  @php
  $kpis = [
    ['grad'=>'kpi-g-blue',   'icon'=>'tabler-activity',          'val'=>$stats['total'],       'lbl'=>'Total actividades', 'route'=>'sci-control-interno'],
    ['grad'=>'kpi-g-red',    'icon'=>'tabler-alarm-off',         'val'=>$stats['vencidas'],    'lbl'=>'Vencidas',          'route'=>'sci-control-interno'],
    ['grad'=>'kpi-g-orange', 'icon'=>'tabler-bell-ringing',      'val'=>$stats['alertas'],     'lbl'=>'Alertas activas',   'route'=>'mon-alertas'],
    ['grad'=>'kpi-g-cyan',   'icon'=>'tabler-building-community','val'=>$stats['unidades'],    'lbl'=>'Áreas participantes','route'=>'mon-ranking-unidades'],
    ['grad'=>'kpi-g-purple', 'icon'=>'tabler-trophy',            'val'=>$bp_stats['ganadores'],'lbl'=>'Ganadores concurso','route'=>'buenas-practicas'],
  ];
  @endphp
  @foreach($kpis as $kpi)
  <div class="col-6 col-xl">
    <a href="{{ route($kpi['route']) }}" class="text-decoration-none">
      <div class="card kpi-strip {{ $kpi['grad'] }} mb-0">
        <div class="card-body">
          <div class="d-flex align-items-center gap-3">
            <div class="kpi-icon"><i class="ti {{ $kpi['icon'] }}"></i></div>
            <div>
              <div class="kpi-val">{{ $kpi['val'] }}</div>
              <div class="kpi-lbl">{{ $kpi['lbl'] }}</div>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
  @endforeach
</div>

{{-- ══ 4. GRÁFICO + ALERTAS ════════════════════════════════════════════════════ --}}
<div class="row g-4 mb-4">

  {{-- Gráfico avance mensual --}}
  <div class="col-12 col-xl-8">
    <div class="card sec-card h-100 mb-0">
      <div class="card-header d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
          <h6>Avance Mensual {{ $anio }}</h6>
          <p>Control Interno vs Modelo de Integridad — % completadas por mes</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <span class="d-flex align-items-center gap-1">
            <span class="leg-dot" style="background:#696cff"></span>
            <small class="text-muted" style="font-size:.72rem">SCI</small>
          </span>
          <span class="d-flex align-items-center gap-1">
            <span class="leg-dot" style="background:#ff9f43"></span>
            <small class="text-muted" style="font-size:.72rem">Integridad</small>
          </span>
          <div class="dropdown">
            <button class="btn btn-sm btn-icon border-0 p-1" data-bs-toggle="dropdown">
              <i class="ti tabler-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="{{ route('rep-reportes') }}"><i class="ti tabler-file-analytics me-2"></i>Reporte completo</a></li>
              <li><a class="dropdown-item" href="{{ route('sci-semaforo') }}"><i class="ti tabler-traffic-lights me-2"></i>Ver semáforo</a></li>
              <li><a class="dropdown-item" href="{{ route('mon-avance-unidades') }}"><i class="ti tabler-chart-bar me-2"></i>Avance por unidades</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="card-body pt-1 pb-3">
        <div id="chartAvanceMensual"></div>
      </div>
    </div>
  </div>

  {{-- Alertas activas --}}
  <div class="col-12 col-xl-4">
    <div class="card sec-card h-100 mb-0">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h6><i class="ti tabler-bell-ringing me-2 text-danger"></i>Alertas Activas</h6>
          <p>{{ $alertas_stats['total'] }} sin leer</p>
        </div>
        <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-danger rounded-pill px-3">Ver todas</a>
      </div>
      <div class="card-body p-0">
        @php
        $alertas_tipos = [
          ['key'=>'vencimiento',     'label'=>'Vencimientos',    'icon'=>'tabler-alarm-off',     'color'=>'danger'],
          ['key'=>'avance_bajo',     'label'=>'Avance bajo',     'icon'=>'tabler-trending-down',  'color'=>'warning'],
          ['key'=>'evidencia_falta', 'label'=>'Sin evidencias',  'icon'=>'tabler-file-off',        'color'=>'info'],
        ];
        @endphp
        @foreach($alertas_tipos as $at)
        <div class="al-row">
          <div class="act-ico bg-label-{{ $at['color'] }}">
            <i class="ti {{ $at['icon'] }} text-{{ $at['color'] }}" style="font-size:.9rem"></i>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-baseline gap-1">
              <span class="fw-bold text-{{ $at['color'] }}" style="font-size:1.5rem;line-height:1">{{ $alertas_stats[$at['key']] }}</span>
              <span class="fw-semibold" style="font-size:.8rem">{{ $at['label'] }}</span>
            </div>
          </div>
          @if($alertas_stats[$at['key']] > 0)
          <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-{{ $at['color'] }} rounded-pill flex-shrink-0">Ver</a>
          @endif
        </div>
        @endforeach

        {{-- Lista alertas recientes --}}
        @if($alertas_recientes->isNotEmpty())
        <div class="px-3 pt-2 pb-1">
          <div class="fw-semibold text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">Más recientes</div>
        </div>
        @foreach($alertas_recientes->take(3) as $al)
        <div class="d-flex align-items-center gap-2 px-3 py-2 border-top" style="border-color:rgba(0,0,0,.05)!important">
          <span class="badge bg-label-{{ $al->prioridad_color }} rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:22px;height:22px;padding:0">
            <i class="ti {{ $al->prioridad_icon }}" style="font-size:.65rem"></i>
          </span>
          <span class="text-truncate" style="font-size:.78rem;flex:1">{{ Str::limit($al->titulo, 40) }}</span>
          <small class="text-muted flex-shrink-0" style="font-size:.68rem">{{ $al->created_at->diffForHumans() }}</small>
        </div>
        @endforeach
        @endif
      </div>
      <div class="sec-footer d-flex align-items-center justify-content-between">
        <span class="text-muted">Total pendientes</span>
        <span class="badge {{ $alertas_stats['total'] > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill">{{ $alertas_stats['total'] }}</span>
      </div>
    </div>
  </div>

</div>

{{-- ══ 5. EJES SCI + ETAPAS INTEGRIDAD ════════════════════════════════════════ --}}
<div class="row g-4 mb-4">

  {{-- Ejes SCI --}}
  <div class="col-12 col-xl-6">
    <div class="card sec-card mb-0">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h6><i class="ti tabler-clipboard-check me-2 text-primary"></i>Ejes del Control Interno</h6>
          <p>SCI · {{ $anio }}</p>
        </div>
        <a href="{{ route('sci-semaforo') }}" class="btn btn-xs btn-label-primary rounded-pill">
          <i class="ti tabler-traffic-lights me-1"></i>Semáforo
        </a>
      </div>
      <div class="table-responsive">
        <table class="table mb-0 dash-tbl">
          <thead>
            <tr>
              <th>Eje</th>
              <th style="min-width:110px">Avance</th>
              <th class="text-center">Comp./Total</th>
            </tr>
          </thead>
          <tbody>
            @forelse($sciEjes as $eje)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="act-ico bg-label-{{ $eje->color ?? 'secondary' }}" style="width:26px;height:26px;border-radius:6px">
                    <i class="ti tabler-layout-grid text-{{ $eje->color ?? 'secondary' }}" style="font-size:.75rem"></i>
                  </div>
                  <span class="fw-medium text-truncate" style="max-width:140px;font-size:.8rem" title="{{ $eje->nombre }}">
                    {{ Str::limit($eje->nombre, 32) }}
                  </span>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1 prog-sm">
                    <div class="progress-bar bg-{{ $eje->color ?? 'secondary' }} rounded-pill" style="width:{{ $eje->porcentaje ?? 0 }}%"></div>
                  </div>
                  <small class="fw-bold text-{{ $eje->color ?? 'secondary' }}" style="min-width:28px;font-size:.78rem">{{ $eje->porcentaje ?? 0 }}%</small>
                </div>
              </td>
              <td class="text-center">
                <span class="fw-bold text-success" style="font-size:.82rem">{{ $eje->completadas_count }}</span>
                <span class="text-muted" style="font-size:.78rem">/{{ $eje->actividades_count }}</span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted py-4">
                <i class="ti tabler-list-tree d-block mb-1" style="font-size:1.5rem;opacity:.4"></i>
                <small>Sin ejes SCI configurados para {{ $anio }}</small>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="sec-footer">
        <a href="{{ route('sci-control-interno') }}" class="text-primary fw-semibold" style="font-size:.78rem">
          Ver todas las actividades SCI <i class="ti tabler-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- Etapas Integridad --}}
  <div class="col-12 col-xl-6">
    <div class="card sec-card mb-0">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h6><i class="ti tabler-shield-check me-2 text-warning"></i>Etapas de Integridad</h6>
          <p>Modelo PCM · {{ $anio }}</p>
        </div>
        <a href="{{ route('sci-modelo-integridad') }}" class="btn btn-xs btn-label-warning rounded-pill">
          <i class="ti tabler-eye me-1"></i>Ver módulo
        </a>
      </div>
      <div class="table-responsive">
        <table class="table mb-0 dash-tbl">
          <thead>
            <tr>
              <th>Etapa</th>
              <th style="min-width:110px">Avance</th>
              <th class="text-center">Comp./Total</th>
            </tr>
          </thead>
          <tbody>
            @forelse($integridadEtapas as $etapa)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="act-ico bg-label-{{ $etapa->color ?? 'secondary' }}" style="width:26px;height:26px;border-radius:6px">
                    <i class="ti tabler-shield text-{{ $etapa->color ?? 'secondary' }}" style="font-size:.75rem"></i>
                  </div>
                  <span class="fw-medium text-truncate" style="max-width:140px;font-size:.8rem" title="{{ $etapa->nombre }}">
                    {{ Str::limit($etapa->nombre, 32) }}
                  </span>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1 prog-sm">
                    <div class="progress-bar bg-{{ $etapa->color ?? 'secondary' }} rounded-pill" style="width:{{ $etapa->porcentaje ?? 0 }}%"></div>
                  </div>
                  <small class="fw-bold text-{{ $etapa->color ?? 'secondary' }}" style="min-width:28px;font-size:.78rem">{{ $etapa->porcentaje ?? 0 }}%</small>
                </div>
              </td>
              <td class="text-center">
                <span class="fw-bold text-success" style="font-size:.82rem">{{ $etapa->completadas_count }}</span>
                <span class="text-muted" style="font-size:.78rem">/{{ $etapa->actividades_count }}</span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted py-4">
                <i class="ti tabler-shield d-block mb-1" style="font-size:1.5rem;opacity:.4"></i>
                <small>Sin etapas de Integridad para {{ $anio }}</small>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="sec-footer">
        <a href="{{ route('sci-modelo-integridad') }}" class="text-warning fw-semibold" style="font-size:.78rem">
          Ver módulo de Integridad <i class="ti tabler-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>

</div>

{{-- ══ 6. PRÓXIMAS A VENCER + BUENAS PRÁCTICAS ════════════════════════════════ --}}
<div class="row g-4 mb-4">

  {{-- Actividades próximas a vencer --}}
  <div class="col-12 col-xl-7">
    <div class="card sec-card mb-0">
      <div class="card-header d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
          <h6><i class="ti tabler-clock-exclamation me-2 text-warning"></i>Próximas a Vencer</h6>
          <p>Actividades ordenadas por fecha límite más cercana</p>
        </div>
        <div class="d-flex gap-2">
          @if($actividades_vencidas->isNotEmpty())
          <span class="badge bg-label-danger rounded-pill" style="font-size:.72rem">
            <i class="ti tabler-alarm-off me-1"></i>{{ $actividades_vencidas->count() }} vencidas
          </span>
          @endif
          <a href="{{ route('sci-control-interno') }}" class="btn btn-xs btn-label-secondary rounded-pill">Ver todo</a>
        </div>
      </div>
      <div class="card-body p-0">
        @forelse($actividades_proximas as $a)
        @php
          $ec   = $a->estado_color;
          $ico  = match($a->estado) { 'en_proceso'=>'tabler-loader-2','observado'=>'tabler-eye','vencida'=>'tabler-alert-triangle',default=>'tabler-circle' };
          $dias = (int) round(now()->diffInDays($a->fecha_limite, false));
          $dc   = $dias <= 0 ? 'danger' : ($dias <= 3 ? 'danger' : ($dias <= 7 ? 'warning' : 'secondary'));
          $compNombre = $a->modulo === 'integridad'
            ? ($a->integridadPregunta?->componente?->nombre ?? '—')
            : ($a->sciPregunta?->componente?->nombre ?? '—');
          $resp = $a->responsables->where('pivot.tipo','principal')->first() ?? $a->responsables->first();
        @endphp
        <div class="list-row d-flex align-items-start gap-3">
          <div class="act-ico bg-label-{{ $ec }} mt-1">
            <i class="ti {{ $ico }} text-{{ $ec }}" style="font-size:.85rem"></i>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-semibold text-truncate" style="font-size:.82rem">{{ $a->nombre }}</div>
            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
              <span class="{{ $a->modulo === 'integridad' ? 'badge-integ' : 'badge-sci' }}">{{ strtoupper($a->modulo) }}</span>
              <span class="badge bg-label-secondary rounded-pill" style="font-size:.65rem;padding:.18em .55em">{{ Str::limit($compNombre, 22) }}</span>
              @if($resp)
              <span class="d-flex align-items-center gap-1">
                <span class="avatar-initial rounded-circle bg-label-primary d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:16px;height:16px;font-size:.55rem;font-weight:700">{{ strtoupper(substr($resp->name,0,2)) }}</span>
                <small class="text-muted" style="font-size:.7rem">{{ Str::of($resp->name)->explode(' ')->first() }}</small>
              </span>
              @endif
            </div>
          </div>
          <div class="text-end flex-shrink-0">
            <div class="text-muted" style="font-size:.72rem">{{ $a->fecha_limite->format('d/m/Y') }}</div>
            <div class="mt-1">
              @if($dias < 0)
                <span class="badge bg-label-danger rounded-pill" style="font-size:.65rem">Vencida</span>
              @elseif($dias === 0)
                <span class="badge bg-danger rounded-pill text-white" style="font-size:.65rem">¡Hoy!</span>
              @else
                <span class="badge bg-label-{{ $dc }} rounded-pill" style="font-size:.65rem">{{ $dias }}d</span>
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
          <i class="ti tabler-circle-check d-block mb-2 text-success" style="font-size:2.5rem;opacity:.5"></i>
          <div class="fw-semibold text-success">¡Sin actividades próximas a vencer!</div>
          <small>Todas las actividades están al día.</small>
        </div>
        @endforelse
      </div>
      @if($actividades_proximas->isNotEmpty())
      <div class="sec-footer">
        <div class="d-flex align-items-center justify-content-between">
          <a href="{{ route('sci-control-interno') }}" class="text-primary fw-semibold" style="font-size:.78rem">Ver SCI <i class="ti tabler-arrow-right ms-1"></i></a>
          <a href="{{ route('sci-modelo-integridad') }}" class="text-warning fw-semibold" style="font-size:.78rem">Ver Integridad <i class="ti tabler-arrow-right ms-1"></i></a>
        </div>
      </div>
      @endif
    </div>
  </div>

  {{-- Buenas Prácticas --}}
  <div class="col-12 col-xl-5">
    <div class="card sec-card mb-0">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h6><i class="ti tabler-rosette-discount-check me-2 text-warning"></i>Buenas Prácticas</h6>
          <p>{{ $bp_stats['total'] }} registradas · {{ $bp_stats['en_concurso'] }} en concurso</p>
        </div>
        <a href="{{ route('buenas-practicas') }}" class="btn btn-xs btn-label-warning rounded-pill">Ver todo</a>
      </div>
      {{-- Stats concurso --}}
      <div class="d-flex align-items-center gap-0 px-3 py-2 border-bottom" style="border-color:rgba(0,0,0,.06)!important">
        @php
        $bpKpis = [
          ['val'=>$bp_stats['total'],        'lbl'=>'Total',          'color'=>'secondary'],
          ['val'=>$bp_stats['en_concurso'],  'lbl'=>'En concurso',    'color'=>'warning'],
          ['val'=>$bp_stats['ganadores'],    'lbl'=>'Ganadores',      'color'=>'success'],
          ['val'=>$bp_stats['implementadas'],'lbl'=>'Implementadas',  'color'=>'primary'],
        ];
        @endphp
        @foreach($bpKpis as $bk)
        <div class="text-center flex-grow-1 {{ !$loop->last ? 'border-end' : '' }}" style="border-color:rgba(0,0,0,.06)!important;padding:.4rem .6rem">
          <div class="fw-bold text-{{ $bk['color'] }}" style="font-size:1.3rem;line-height:1">{{ $bk['val'] }}</div>
          <div class="text-muted" style="font-size:.65rem">{{ $bk['lbl'] }}</div>
        </div>
        @endforeach
      </div>
      {{-- Lista --}}
      <div class="card-body p-0">
        @forelse($buenas_practicas as $bp)
        <div class="list-row d-flex align-items-start gap-3">
          <div class="act-ico bg-label-{{ $bp->estado_color }}" style="margin-top:2px">
            <i class="ti {{ $bp->estado_icon }} text-{{ $bp->estado_color }}" style="font-size:.85rem"></i>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-semibold text-truncate" style="font-size:.82rem">{{ Str::limit($bp->titulo, 45) }}</div>
            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
              <span class="{{ $bp->modulo === 'integridad' ? 'badge-integ' : 'badge-sci' }}">{{ $bp->modulo_label }}</span>
              <span class="badge bg-label-{{ $bp->estado_color }} rounded-pill" style="font-size:.65rem;padding:.18em .55em">{{ $bp->estado_label }}</span>
              @if($bp->unidadOrganica)
              <small class="text-muted" style="font-size:.7rem">{{ $bp->unidadOrganica->sigla ?? Str::limit($bp->unidadOrganica->nombre, 14) }}</small>
              @endif
            </div>
          </div>
          @if($bp->avance !== null)
          <div class="text-end flex-shrink-0">
            <span class="fw-bold text-{{ $bp->estado_color }}" style="font-size:.88rem">{{ $bp->avance }}%</span>
          </div>
          @endif
        </div>
        @empty
        <div class="text-center text-muted py-5">
          <i class="ti tabler-rosette-discount-check d-block mb-2" style="font-size:2rem;opacity:.3"></i>
          <small>Sin buenas prácticas registradas</small>
        </div>
        @endforelse
      </div>
      <div class="sec-footer">
        <a href="{{ route('buenas-practicas') }}" class="text-warning fw-semibold" style="font-size:.78rem">
          Ver todas las prácticas <i class="ti tabler-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>

</div>

{{-- ══ 7. DONUT ESTADOS + RANKING UNIDADES ════════════════════════════════════ --}}
<div class="row g-4 mb-2">

  {{-- Donut --}}
  <div class="col-12 col-xl-4">
    <div class="card sec-card mb-0">
      <div class="card-header">
        <h6>Estado de Actividades</h6>
        <p>{{ $stats['total'] }} actividades en total</p>
      </div>
      <div class="card-body pt-2 pb-2">
        <div id="chartDonut"></div>
        <div class="mt-1">
          @php
          $donutSeries = [
            ['label'=>'Completadas', 'hex'=>'#28c76f', 'val'=>$stats['completadas']],
            ['label'=>'En proceso',  'hex'=>'#ff9f43', 'val'=>$stats['en_proceso']],
            ['label'=>'Pendientes',  'hex'=>'#00cfe8', 'val'=>$stats['pendientes']],
            ['label'=>'Observadas',  'hex'=>'#696cff', 'val'=>$stats['observados']],
            ['label'=>'Vencidas',    'hex'=>'#ea5455', 'val'=>$stats['vencidas']],
          ];
          @endphp
          @foreach($donutSeries as $ds)
          <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="d-flex align-items-center gap-2">
              <span class="leg-dot" style="background:{{ $ds['hex'] }}"></span>
              <small class="text-muted" style="font-size:.76rem">{{ $ds['label'] }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div class="progress prog-sm" style="width:60px">
                <div class="progress-bar rounded-pill" style="width:{{ $stats['total'] ? round($ds['val']/$stats['total']*100) : 0 }}%;background:{{ $ds['hex'] }}"></div>
              </div>
              <small class="fw-bold text-end" style="min-width:20px;font-size:.78rem">{{ $ds['val'] }}</small>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- Ranking Unidades --}}
  <div class="col-12 col-xl-8">
    <div class="card sec-card mb-0">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h6><i class="ti tabler-trophy me-2 text-warning"></i>Ranking de Unidades Orgánicas</h6>
          <p>Ambos módulos · porcentaje de completadas</p>
        </div>
        <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-xs btn-label-warning rounded-pill">Ver ranking</a>
      </div>
      <div class="card-body pt-2 pb-3">
        @forelse($areas_ranking as $i => $u)
        @php
          $pc  = $u->actividades_count > 0 ? round($u->completadas_count/$u->actividades_count*100) : 0;
          $col = $pc >= 75 ? 'success' : ($pc >= 50 ? 'warning' : 'danger');
          $medalColors = ['#FFD700','#C0C0C0','#CD7F32'];
          $medal = $i < 3 ? $medalColors[$i] : null;
        @endphp
        <div class="d-flex align-items-center gap-3 mb-{{ $loop->last ? '0' : '3' }}">
          <div class="text-center flex-shrink-0" style="width:22px">
            @if($medal)
            <i class="ti tabler-medal" style="color:{{ $medal }};font-size:1rem"></i>
            @else
            <span class="text-muted fw-bold" style="font-size:.78rem">{{ $i+1 }}</span>
            @endif
          </div>
          <div class="flex-shrink-0">
            <span class="badge bg-label-secondary rounded-pill fw-bold" style="font-size:.72rem;min-width:38px">{{ $u->sigla ?? '—' }}</span>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <span class="fw-medium text-truncate" style="font-size:.8rem;max-width:160px" title="{{ $u->nombre }}">{{ Str::limit($u->nombre, 28) }}</span>
              <span class="fw-bold text-{{ $col }}" style="font-size:.82rem;flex-shrink:0">{{ $pc }}%</span>
            </div>
            <div class="progress prog-sm">
              <div class="progress-bar bg-{{ $col }} rounded-pill" style="width:{{ $pc }}%"></div>
            </div>
          </div>
          <div class="text-muted text-center flex-shrink-0" style="font-size:.72rem;min-width:38px">
            {{ $u->completadas_count }}/{{ $u->actividades_count }}
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-3">
          <small>Sin unidades con actividades registradas</small>
        </div>
        @endforelse
      </div>
    </div>
  </div>

</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark    = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const gridColor = isDark ? 'rgba(255,255,255,.07)' : 'rgba(0,0,0,.05)';
  const textColor = isDark ? '#b4bdc6' : '#697a8d';
  const cardBg    = isDark ? '#2b2c40' : '#ffffff';

  // ── Gráfico área doble mensual ─────────────────────────────
  new ApexCharts(document.getElementById('chartAvanceMensual'), {
    chart: {
      type: 'area', height: 240,
      toolbar: { show: false }, zoom: { enabled: false },
      animations: { enabled: true, easing: 'easeinout', speed: 700 },
    },
    series: [
      { name: 'Control Interno (SCI)',   data: @json($por_mes_sci)   },
      { name: 'Modelo de Integridad',    data: @json($por_mes_integ) },
    ],
    xaxis: {
      categories: @json($meses_labels),
      labels: { style: { colors: textColor, fontSize: '11px' } },
      axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: {
      max: 100, min: 0, tickAmount: 4,
      labels: { formatter: v => v + '%', style: { colors: textColor, fontSize: '11px' } },
    },
    colors: ['#696cff', '#ff9f43'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .25, opacityTo: .02, stops: [0, 95, 100] } },
    stroke: { curve: 'smooth', width: 2.5 },
    markers: { size: 3.5, strokeWidth: 0, hover: { size: 5.5 } },
    grid: { borderColor: gridColor, strokeDashArray: 5, padding: { left: 4, right: 4 } },
    legend: { show: false },
    dataLabels: { enabled: false },
    tooltip: {
      theme: isDark ? 'dark' : 'light',
      y: { formatter: v => v + '%' },
      x: { show: true },
    },
  }).render();

  // ── Donut de estados ───────────────────────────────────────
  new ApexCharts(document.getElementById('chartDonut'), {
    chart: { type: 'donut', height: 185, animations: { speed: 500 } },
    series: [
      {{ $stats['completadas'] }},
      {{ $stats['en_proceso'] }},
      {{ $stats['pendientes'] }},
      {{ $stats['observados'] }},
      {{ $stats['vencidas'] }},
    ],
    labels: ['Completadas','En proceso','Pendientes','Observadas','Vencidas'],
    colors: ['#28c76f','#ff9f43','#00cfe8','#696cff','#ea5455'],
    plotOptions: { pie: { donut: { size: '68%', labels: {
      show: true,
      name:  { show: true, fontSize: '11px', color: textColor, offsetY: -5 },
      value: { show: true, fontSize: '19px', fontWeight: 700, color: textColor, offsetY: 4,
               formatter: v => parseInt(v) },
      total: { show: true, label: 'Total', color: textColor, fontWeight: 400, fontSize: '11px',
               formatter: () => '{{ $stats["total"] }}' },
    }}}},
    legend:     { show: false },
    dataLabels: { enabled: false },
    stroke:     { width: 2.5, colors: [cardBg] },
    tooltip:    { theme: isDark ? 'dark' : 'light', y: { formatter: v => v + ' actividades' } },
  }).render();
});
</script>
@endsection
