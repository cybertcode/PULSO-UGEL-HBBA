@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Panel Principal - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-style')
<style>
/* ── Header ── */
.dash-header {
  background: linear-gradient(135deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), .72) 100%);
  border-radius: 18px;
  padding: 2rem 2rem 1.5rem;
  color: #fff;
  margin-bottom: 2rem;
}
.dash-header h4 { font-size: 1.35rem; font-weight: 800; margin: 0; color: #fff; }
.dash-header p  { font-size: .82rem; opacity: .82; margin: .25rem 0 0; }
.dash-date-pill {
  background: rgba(255,255,255,.18);
  border: 1px solid rgba(255,255,255,.35);
  border-radius: 50px;
  padding: .35rem .9rem;
  font-size: .78rem;
  color: #fff;
  white-space: nowrap;
}
/* mini stats dentro del header */
.hstat-val  { font-size: 1.8rem; font-weight: 800; line-height: 1; }
.hstat-lbl  { font-size: .72rem; opacity: .82; margin-top: .18rem; }
.hstat-bar  { height: 4px; border-radius: 2px; background: rgba(255,255,255,.22); margin-top: .55rem; }
.hstat-fill { height: 100%; border-radius: 2px; }

/* ── KPI gradient cards ── */
.kpi-card { border-radius: 14px; border: none; overflow: hidden; transition: transform .18s, box-shadow .18s; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.12); }
.kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; background: rgba(255,255,255,.22); }
.kpi-val  { font-size: 2.1rem; font-weight: 800; line-height: 1; color: #fff; }
.kpi-lbl  { font-size: .7rem; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: rgba(255,255,255,.78); }
.kpi-trend{ font-size: .72rem; color: rgba(255,255,255,.7); margin-top: .3rem; }
.kpi-grad-blue   { background: linear-gradient(135deg,#667eea,#764ba2); }
.kpi-grad-green  { background: linear-gradient(135deg,#11998e,#38ef7d); }
.kpi-grad-orange { background: linear-gradient(135deg,#f7971e,#ffd200); }
.kpi-grad-red    { background: linear-gradient(135deg,#e52d27,#b31217); }
.kpi-grad-cyan   { background: linear-gradient(135deg,#0acffe,#495aff); }

/* ── Section cards ── */
.sec-card {
  border-radius: 14px;
  border: 1px solid rgba(0,0,0,.06);
  overflow: hidden;
}
.sec-card .card-header {
  background: transparent;
  border-bottom: 1px solid rgba(0,0,0,.06);
  padding: 1rem 1.25rem .8rem;
}
.sec-card .card-header h6 { font-size: .92rem; font-weight: 700; margin: 0; }
.sec-card .card-header p  { font-size: .74rem; color: #a8a5b5; margin: .15rem 0 0; }

/* ── Chart label legend dot ── */
.legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; }

/* ── Alerta items ── */
.alerta-row { padding: .85rem 1.25rem; transition: background .15s; }
.alerta-row:hover { background: rgba(0,0,0,.025); }

/* ── Timeline activity rows ── */
.act-row { padding: .75rem 1.25rem; transition: background .15s; }
.act-row:hover { background: rgba(0,0,0,.025); }
.act-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .95rem; flex-shrink: 0; }

/* ── Componentes table ── */
.tbl-comp td, .tbl-comp th { padding: .5rem .85rem; font-size: .82rem; vertical-align: middle; }
.tbl-comp thead th { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6e6b7b; background: #f8f7fa; white-space: nowrap; border-bottom: 1px solid rgba(0,0,0,.07); }

/* ── Footer values strip ── */
.val-chip { border-radius: 12px; background: var(--bs-body-bg); border: 1px solid rgba(0,0,0,.06); padding: .85rem 1rem; }
.val-chip-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.val-chip-title { font-size: .82rem; font-weight: 700; }
.val-chip-sub   { font-size: .72rem; color: #a8a5b5; }

/* ── Card footer strip ── */
.card-foot { padding: .7rem 1.25rem; background: #f8f7fa; border-top: 1px solid rgba(0,0,0,.06); font-size: .78rem; }

/* ── Buenas prácticas row ── */
.bp-row { padding: .8rem 1.25rem; }
.bp-row:hover { background: rgba(0,0,0,.025); }
</style>
@endsection

@section('content')

@php $pct = $stats['total'] ? round($stats['completadas']/$stats['total']*100) : 0; @endphp

{{-- ── HEADER BANNER ── --}}
<div class="dash-header mb-4 pulso-animate">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h4>¡Bienvenido/a, {{ auth()->user()->name }}!</h4>
      <p>Sistema de seguimiento del Plan de Control Interno y Modelo de Integridad Institucional · Directiva N° 006-2019-CG-INTEG</p>
    </div>
    <div class="dash-date-pill">
      <i class="ti tabler-calendar-event me-1"></i>
      {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}
    </div>
  </div>
  <div class="row g-3">
    <div class="col-6 col-md-3">
      <div class="hstat-val">{{ $stats['total'] }}</div>
      <div class="hstat-lbl">Actividades totales</div>
      <div class="hstat-bar"><div class="hstat-fill" style="width:{{ $pct }}%;background:#fff"></div></div>
    </div>
    <div class="col-6 col-md-3">
      <div class="hstat-val" style="color:#a8f5c8">{{ $stats['completadas'] }}</div>
      <div class="hstat-lbl">Completadas · {{ $pct }}%</div>
      <div class="hstat-bar"><div class="hstat-fill" style="width:{{ $pct }}%;background:#28c76f"></div></div>
    </div>
    <div class="col-6 col-md-3">
      <div class="hstat-val" style="color:#ffd6a0">{{ $stats['vencidas'] + $stats['pendientes'] }}</div>
      <div class="hstat-lbl">Requieren atención</div>
      <div class="hstat-bar"><div class="hstat-fill" style="width:{{ $stats['total'] ? round(($stats['vencidas']+$stats['pendientes'])/$stats['total']*100) : 0 }}%;background:#ff9f43"></div></div>
    </div>
    <div class="col-6 col-md-3">
      <div class="hstat-val" style="color:#a8d4ff">{{ $stats['unidades'] }}</div>
      <div class="hstat-lbl">Áreas participantes</div>
      <div class="hstat-bar"><div class="hstat-fill" style="width:{{ $stats['total_unidades'] ? round($stats['unidades']/$stats['total_unidades']*100) : 0 }}%;background:#a8d4ff"></div></div>
    </div>
  </div>
</div>

{{-- ── KPI CARDS ── --}}
<div class="row g-3 mb-4">
  @php
  $kpis = [
    ['route'=>'sci-control-interno',   'grad'=>'kpi-grad-blue',   'icon'=>'tabler-clipboard-list',       'label'=>'Control Interno',      'sub'=>'Actividades en seguimiento', 'val'=>$stats['total'],                        'trend'=>$stats['avance_global'].'% completadas'],
    ['route'=>'sci-modelo-integridad', 'grad'=>'kpi-grad-green',  'icon'=>'tabler-shield-check',          'label'=>'Modelo de Integridad', 'sub'=>'Acciones en seguimiento',    'val'=>$componentes->sum('actividades_count'),  'trend'=>round($componentes->avg('porcentaje')).'% completadas'],
    ['route'=>'buenas-practicas',      'grad'=>'kpi-grad-orange', 'icon'=>'tabler-rosette-discount-check','label'=>'Buenas Prácticas',     'sub'=>'Registradas',                'val'=>($stats['reconocimientos']??0),           'trend'=>($stats['reconocimientos_implementadas']??0).' en implementación'],
    ['route'=>'sci-control-interno',   'grad'=>'kpi-grad-red',    'icon'=>'tabler-clock-exclamation',     'label'=>'Pendientes',           'sub'=>'Por atender',                'val'=>$stats['pendientes'],                    'trend'=>$stats['vencidas'].' vencidas'],
    ['route'=>'mon-ranking-unidades',  'grad'=>'kpi-grad-cyan',   'icon'=>'tabler-building-community',    'label'=>'Áreas Participantes',  'sub'=>'En el sistema',              'val'=>$stats['unidades'],                      'trend'=>'De '.$stats['total_unidades'].' totales'],
  ];
  @endphp
  @foreach($kpis as $kpi)
  <div class="col-6 col-xl pulso-animate" style="animation-delay:{{ $loop->index * .06 }}s">
    <a href="{{ route($kpi['route']) }}" class="text-decoration-none">
      <div class="card kpi-card {{ $kpi['grad'] }} mb-0">
        <div class="card-body p-3 p-md-4">
          <div class="kpi-icon mb-3">
            <i class="ti {{ $kpi['icon'] }}" style="color:#fff;font-size:1.3rem"></i>
          </div>
          <div class="kpi-val">{{ $kpi['val'] }}</div>
          <div class="kpi-lbl mt-1">{{ $kpi['label'] }}</div>
          <div class="kpi-trend"><i class="ti tabler-trending-up me-1"></i>{{ $kpi['trend'] }}</div>
        </div>
      </div>
    </a>
  </div>
  @endforeach
</div>

{{-- ── FILA 1: Gráfico de avance + Alertas ── --}}
<div class="row g-4 mb-4">

  {{-- Avance mensual dual --}}
  <div class="col-xl-8">
    <div class="card sec-card h-100 mb-0">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h6>Avance General del Seguimiento</h6>
          <p>Control Interno vs Modelo de Integridad · {{ now()->year }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <span class="d-flex align-items-center gap-1">
            <span class="legend-dot" style="background:#696cff"></span>
            <small class="text-muted" style="font-size:.72rem">SCI</small>
          </span>
          <span class="d-flex align-items-center gap-1">
            <span class="legend-dot" style="background:#28c76f"></span>
            <small class="text-muted" style="font-size:.72rem">Integridad</small>
          </span>
          <div class="dropdown">
            <button class="btn btn-sm btn-icon btn-text-secondary border-0 p-1" data-bs-toggle="dropdown">
              <i class="ti tabler-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="{{ route('rep-reportes') }}"><i class="ti tabler-file-analytics me-2"></i>Reporte completo</a></li>
              <li><a class="dropdown-item" href="{{ route('mon-semaforo') }}"><i class="ti tabler-traffic-lights me-2"></i>Ver semáforo</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="card-body pt-2 pb-3">
        <div id="chartLineAvance"></div>
      </div>
    </div>
  </div>

  {{-- Alertas activas --}}
  <div class="col-xl-4">
    <div class="card sec-card h-100 mb-0">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h6><i class="ti tabler-bell-ringing me-2 text-warning" style="font-size:1rem"></i>Alertas Activas</h6>
          <p>Requieren atención inmediata</p>
        </div>
        <a href="{{ route('mon-alertas') }}" class="btn btn-sm btn-label-warning rounded-pill px-3" style="font-size:.74rem">
          Ver todas <i class="ti tabler-arrow-right ms-1" style="font-size:.72rem"></i>
        </a>
      </div>
      <div class="card-body p-0">
        @php
          $alertasVencidas  = $alertas_recientes->where('tipo','vencimiento')->count();
          $alertasBajas     = $alertas_recientes->where('tipo','avance_bajo')->count();
          $alertasEvidencia = $alertas_recientes->where('tipo','evidencia_falta')->count();
          $alertasItems = [
            ['count'=>$alertasVencidas,  'label'=>'actividades vencidas',  'sub'=>'Requieren atención inmediata','color'=>'danger', 'icon'=>'tabler-alert-octagon'],
            ['count'=>$alertasBajas,     'label'=>'con avance bajo',       'sub'=>'Sin progreso en 7+ días',     'color'=>'warning','icon'=>'tabler-trending-down'],
            ['count'=>$alertasEvidencia, 'label'=>'sin evidencias',        'sub'=>'Documentos pendientes',       'color'=>'info',   'icon'=>'tabler-file-off'],
          ];
        @endphp
        @foreach($alertasItems as $ai)
        <div class="alerta-row d-flex align-items-center gap-3 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div class="badge rounded bg-label-{{ $ai['color'] }} flex-shrink-0" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;padding:0">
            <i class="ti {{ $ai['icon'] }}" style="font-size:1.1rem"></i>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-baseline gap-1">
              <span class="fw-bold text-{{ $ai['color'] }}" style="font-size:1.4rem;line-height:1">{{ $ai['count'] }}</span>
              <span style="font-size:.8rem;font-weight:600">{{ $ai['label'] }}</span>
            </div>
            <small class="text-muted" style="font-size:.72rem">{{ $ai['sub'] }}</small>
          </div>
          <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-{{ $ai['color'] }} rounded-pill" style="font-size:.7rem;padding:.2rem .6rem">Ver</a>
        </div>
        @endforeach
        <div class="card-foot d-flex align-items-center justify-content-between">
          <span class="text-muted">Total alertas pendientes</span>
          <span class="badge bg-danger rounded-pill">{{ $alertasVencidas + $alertasBajas + $alertasEvidencia }}</span>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ── FILA 2: Donut + Actividades Recientes + Buenas Prácticas ── --}}
<div class="row g-4 mb-4">

  {{-- Donut estados --}}
  <div class="col-xl-4">
    <div class="card sec-card h-100 mb-0">
      <div class="card-header">
        <h6>Actividades por Estado</h6>
        <p>{{ $stats['total'] }} actividades registradas</p>
      </div>
      <div class="card-body pt-2 pb-2">
        <div id="chartDonutEstados"></div>
        <div class="mt-2">
          @php
          $estadosDonut = [
            ['label'=>'Completadas', 'hex'=>'#28c76f', 'val'=>$stats['completadas']],
            ['label'=>'En proceso',  'hex'=>'#ff9f43', 'val'=>$stats['en_proceso']],
            ['label'=>'Pendientes',  'hex'=>'#00cfe8', 'val'=>$stats['pendientes']],
            ['label'=>'Observadas',  'hex'=>'#696cff', 'val'=>$stats['observados']],
            ['label'=>'Vencidas',    'hex'=>'#ea5455', 'val'=>$stats['vencidas']],
          ];
          @endphp
          @foreach($estadosDonut as $e)
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
              <span class="legend-dot" style="background:{{ $e['hex'] }}"></span>
              <small class="text-muted" style="font-size:.78rem">{{ $e['label'] }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div class="progress" style="width:64px;height:4px;border-radius:2px">
                <div class="progress-bar rounded-pill" style="width:{{ $stats['total'] ? round($e['val']/$stats['total']*100) : 0 }}%;background:{{ $e['hex'] }}"></div>
              </div>
              <small class="fw-bold" style="min-width:20px;text-align:right;font-size:.8rem">{{ $e['val'] }}</small>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- Actividades recientes --}}
  <div class="col-xl-4">
    <div class="card sec-card h-100 mb-0">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h6>Actividades Recientes</h6>
          <p>Últimas actualizaciones</p>
        </div>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-primary rounded-pill px-3" style="font-size:.74rem">
          Ver todas <i class="ti tabler-arrow-right ms-1" style="font-size:.72rem"></i>
        </a>
      </div>
      <div class="card-body p-0">
        @forelse($actividades_proximas->take(5) as $a)
        @php
          $ec  = $a->estado_color;
          $ico = match($a->estado) { 'completada'=>'tabler-circle-check','en_proceso'=>'tabler-loader-2','observado'=>'tabler-eye','vencida'=>'tabler-alert-triangle',default=>'tabler-circle' };
          $lbl = match($a->estado) { 'completada'=>'Completada','en_proceso'=>'En proceso','observado'=>'Observada','vencida'=>'Vencida',default=>'Pendiente' };
        @endphp
        <div class="act-row d-flex align-items-start gap-3 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div class="act-icon bg-label-{{ $ec }} mt-1">
            <i class="ti {{ $ico }} text-{{ $ec }}" style="font-size:.9rem"></i>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-semibold text-truncate" style="font-size:.82rem">{{ $a->nombre }}</div>
            <div class="d-flex align-items-center gap-2 mt-1">
              <span class="badge bg-label-{{ $ec }} rounded-pill" style="font-size:.68rem;padding:.18em .55em">{{ $lbl }}</span>
              <small class="text-muted" style="font-size:.72rem">{{ $a->fecha_limite->format('d/m/Y') }}</small>
            </div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
          <i class="ti tabler-inbox d-block mb-2" style="font-size:2rem"></i>
          <small>Sin actividades recientes</small>
        </div>
        @endforelse
        @if($actividades_proximas->isNotEmpty())
        <div class="card-foot">
          <a href="{{ route('sci-control-interno') }}" class="text-primary fw-medium">
            Ver todas las actividades <i class="ti tabler-arrow-right ms-1" style="font-size:.72rem"></i>
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Buenas prácticas --}}
  <div class="col-xl-4">
    <div class="card sec-card h-100 mb-0">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h6><i class="ti tabler-rosette-discount-check me-2 text-warning" style="font-size:.95rem"></i>Buenas Prácticas</h6>
          <p>En implementación</p>
        </div>
        <a href="{{ route('buenas-practicas') }}" class="btn btn-sm btn-label-warning rounded-pill px-3" style="font-size:.74rem">
          Ver todas <i class="ti tabler-arrow-right ms-1" style="font-size:.72rem"></i>
        </a>
      </div>
      <div class="card-body p-0">
        @php
        $practicas = [
          ['nombre'=>'Digitalización de procesos de contratación', 'area'=>'Logística',        'pct'=>75, 'color'=>'success'],
          ['nombre'=>'Tablero de control de riesgos institucionales','area'=>'Control Interno', 'pct'=>60, 'color'=>'warning'],
          ['nombre'=>'Canal de denuncias integrado y confidencial', 'area'=>'Integridad',       'pct'=>40, 'color'=>'danger'],
        ];
        @endphp
        @foreach($practicas as $pr)
        <div class="bp-row {{ !$loop->last ? 'border-bottom' : '' }}">
          <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
            <div class="flex-grow-1 overflow-hidden">
              <div class="fw-semibold text-truncate" style="font-size:.82rem">{{ $pr['nombre'] }}</div>
              <small class="text-muted" style="font-size:.72rem">{{ $pr['area'] }}</small>
            </div>
            <span class="badge bg-label-{{ $pr['color'] }} rounded-pill fw-bold flex-shrink-0" style="font-size:.72rem">{{ $pr['pct'] }}%</span>
          </div>
          <div class="progress" style="height:5px;border-radius:3px">
            <div class="progress-bar bg-{{ $pr['color'] }} rounded-pill" style="width:{{ $pr['pct'] }}%"></div>
          </div>
        </div>
        @endforeach
        <div class="card-foot">
          <a href="{{ route('buenas-practicas') }}" class="text-warning fw-medium">
            Ver todas las prácticas <i class="ti tabler-arrow-right ms-1" style="font-size:.72rem"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ── FILA 3: Componentes PCM + Próximas a vencer ── --}}
<div class="row g-4 mb-4">

  {{-- Componentes PCM --}}
  <div class="col-xl-5">
    <div class="card sec-card h-100 mb-0">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h6>Componentes PCM</h6>
          <p>Directiva N° 006-2019-CG-INTEG</p>
        </div>
        <a href="{{ route('mon-semaforo') }}" class="btn btn-sm btn-label-primary rounded-pill px-3" style="font-size:.74rem">
          <i class="ti tabler-traffic-lights me-1" style="font-size:.8rem"></i>Semáforo
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 tbl-comp">
            <thead>
              <tr>
                <th>Componente</th>
                <th style="min-width:120px">Avance</th>
                <th class="text-center">Acts.</th>
              </tr>
            </thead>
            <tbody>
              @forelse($componentes->take(9) as $c)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="badge rounded bg-label-{{ $c->color }} flex-shrink-0" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;padding:0">
                      <i class="ti {{ $c->icono ?? 'tabler-point' }}" style="font-size:.78rem"></i>
                    </div>
                    <span class="fw-semibold text-truncate" style="max-width:145px;font-size:.8rem" title="{{ $c->nombre }}">{{ $c->nombre }}</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:5px;border-radius:3px">
                      <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
                    </div>
                    <small class="fw-bold text-{{ $c->color }}" style="min-width:28px;font-size:.78rem">{{ $c->porcentaje }}%</small>
                  </div>
                </td>
                <td class="text-center">
                  <span class="fw-semibold" style="font-size:.8rem">{{ $c->completadas_count }}</span><span class="text-muted" style="font-size:.78rem">/{{ $c->actividades_count }}</span>
                </td>
              </tr>
              @empty
              <tr><td colspan="3" class="text-center text-muted py-4">Sin datos de componentes</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-foot">
          <a href="{{ route('sci-modelo-integridad') }}" class="text-primary fw-medium">
            Ver todas las actividades <i class="ti tabler-arrow-right ms-1" style="font-size:.72rem"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Actividades próximas a vencer --}}
  <div class="col-xl-7">
    <div class="card sec-card h-100 mb-0">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div>
          <h6>Actividades Próximas a Vencer</h6>
          <p>Ordenadas por fecha límite más cercana</p>
        </div>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-secondary rounded-pill px-3" style="font-size:.74rem">
          Ver todas <i class="ti tabler-arrow-right ms-1" style="font-size:.72rem"></i>
        </a>
      </div>
      <div class="card-body p-0">
        @forelse($actividades_proximas as $a)
        @php
          $ec  = $a->estado_color;
          $ico = match($a->estado) { 'completada'=>'tabler-circle-check','en_proceso'=>'tabler-loader-2','observado'=>'tabler-eye','vencida'=>'tabler-alert-triangle',default=>'tabler-circle' };
          $dias = (int) round(now()->diffInDays($a->fecha_limite, false));
          $dc   = $dias < 0 ? 'danger' : ($dias <= 3 ? 'danger' : ($dias <= 7 ? 'warning' : 'secondary'));
        @endphp
        <div class="act-row d-flex align-items-start gap-3 border-bottom">
          <div class="act-icon bg-label-{{ $ec }} mt-1">
            <i class="ti {{ $ico }} text-{{ $ec }}" style="font-size:.9rem"></i>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-semibold text-truncate" style="font-size:.82rem">{{ $a->nombre }}</div>
            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
              <span class="badge bg-label-secondary rounded-pill" style="font-size:.68rem;padding:.18em .55em">{{ $a->componente->nombre ?? '—' }}</span>
              @php $resp = $a->responsables->first(); @endphp
              @if($resp)
              <div class="d-flex align-items-center gap-1">
                <span class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center" style="width:18px;height:18px;font-size:.62rem;font-weight:700">{{ strtoupper(substr($resp->name,0,2)) }}</span>
                <small class="text-muted" style="font-size:.72rem">{{ explode(' ',$resp->name)[0] }}</small>
              </div>
              @endif
            </div>
          </div>
          <div class="text-end flex-shrink-0">
            <div class="text-muted" style="font-size:.72rem">{{ $a->fecha_limite->format('d/m/Y') }}</div>
            <div class="mt-1">
              @if($dias < 0)
                <span class="badge bg-label-danger rounded-pill" style="font-size:.68rem">Vencida</span>
              @elseif($dias === 0)
                <span class="badge bg-danger rounded-pill text-white" style="font-size:.68rem">¡Hoy!</span>
              @else
                <span class="badge bg-label-{{ $dc }} rounded-pill" style="font-size:.68rem">{{ $dias }}d</span>
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
          <i class="ti tabler-circle-check d-block mb-2 text-success" style="font-size:2.5rem"></i>
          <p class="fw-medium mb-0 text-success">¡Sin actividades pendientes próximas!</p>
          <small>Todas las actividades están al día.</small>
        </div>
        @endforelse
        @if($actividades_proximas->isNotEmpty())
        <div class="card-foot">
          <a href="{{ route('sci-control-interno') }}" class="text-primary fw-medium">
            Ver todas las actividades <i class="ti tabler-arrow-right ms-1" style="font-size:.72rem"></i>
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>

</div>

{{-- ── FOOTER: Valores institucionales ── --}}
<div class="row g-3 mt-1 mb-2">
  @php
  $valores = [
    ['icon'=>'tabler-device-desktop-analytics','color'=>'primary', 'title'=>'Sistema de Seguimiento','sub'=>'Moderno, información confiable, decisiones oportunas.'],
    ['icon'=>'tabler-eye',                     'color'=>'success', 'title'=>'Transparencia',          'sub'=>'Información clara y accesible'],
    ['icon'=>'tabler-bolt',                    'color'=>'warning', 'title'=>'Eficiencia',             'sub'=>'Procesos más ágiles'],
    ['icon'=>'tabler-shield-check',            'color'=>'danger',  'title'=>'Integridad',             'sub'=>'Gestión ética y responsable'],
  ];
  @endphp
  @foreach($valores as $v)
  <div class="col-6 col-md-3">
    <div class="val-chip d-flex align-items-center gap-3">
      <div class="val-chip-icon bg-label-{{ $v['color'] }}">
        <i class="ti {{ $v['icon'] }} text-{{ $v['color'] }}" style="font-size:1.1rem"></i>
      </div>
      <div class="overflow-hidden">
        <div class="val-chip-title text-truncate">{{ $v['title'] }}</div>
        <div class="val-chip-sub text-truncate">{{ $v['sub'] }}</div>
      </div>
    </div>
  </div>
  @endforeach
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark    = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const gridColor = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.05)';
  const textColor = isDark ? '#b4bdc6' : '#697a8d';
  const cardBg    = isDark ? '#2b2c40' : '#ffffff';

  // ── Área doble mensual ──
  new ApexCharts(document.getElementById('chartLineAvance'), {
    chart: {
      type: 'area', height: 255, toolbar: { show: false }, zoom: { enabled: false },
      animations: { enabled: true, easing: 'easeinout', speed: 600 },
    },
    series: [
      { name: 'Control Interno',   data: @json($por_mes_sci)   },
      { name: 'Modelo Integridad', data: @json($por_mes_integ) },
    ],
    xaxis: {
      categories: @json($meses_labels),
      labels: { style: { colors: textColor, fontSize: '11px' } },
      axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: {
      max: 100, min: 0,
      labels: { formatter: v => v + '%', style: { colors: textColor, fontSize: '11px' } },
    },
    colors: ['#696cff', '#28c76f'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .28, opacityTo: .02, stops: [0, 95, 100] } },
    stroke: { curve: 'smooth', width: 2.5 },
    markers: { size: 4, strokeWidth: 0, hover: { size: 6 } },
    grid: { borderColor: gridColor, strokeDashArray: 5, padding: { left: 4, right: 4 } },
    legend: { show: false },
    dataLabels: { enabled: false },
    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: v => v + '%' } },
  }).render();

  // ── Donut de estados ──
  new ApexCharts(document.getElementById('chartDonutEstados'), {
    chart: { type: 'donut', height: 200, animations: { speed: 500 } },
    series: [{{ $stats['completadas'] }}, {{ $stats['en_proceso'] }}, {{ $stats['pendientes'] }}, {{ $stats['observados'] }}, {{ $stats['vencidas'] }}],
    labels: ['Completadas','En Proceso','Pendientes','Observadas','Vencidas'],
    colors: ['#28c76f','#ff9f43','#00cfe8','#696cff','#ea5455'],
    plotOptions: { pie: { donut: { size: '70%', labels: {
      show: true,
      name:  { show: true, fontSize: '12px', color: textColor, offsetY: -6 },
      value: { show: true, fontSize: '20px', fontWeight: 700, color: textColor, offsetY: 4, formatter: v => v },
      total: { show: true, label: 'Total', color: textColor, fontWeight: 400, fontSize: '11px',
               formatter: () => '{{ $stats["total"] }}' },
    }}}},
    legend:      { show: false },
    dataLabels:  { enabled: false },
    stroke:      { width: 3, colors: [cardBg] },
    tooltip:     { theme: isDark ? 'dark' : 'light', y: { formatter: v => v + ' actividades' } },
  }).render();
});
</script>
@endsection
