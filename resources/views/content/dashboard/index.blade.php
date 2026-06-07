@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Panel Principal - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

{{-- ── Header banner con gradiente Vuexy ── --}}
<div class="pulso-page-header mb-6 pulso-animate">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <h4 class="mb-1">¡Bienvenido/a, {{ auth()->user()->name }}!</h4>
      <p>Sistema de seguimiento del Plan de Control Interno y Modelo de Integridad Institucional · Directiva N° 006-2019-CG-INTEG</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <div class="px-3 py-2 rounded-pill d-flex align-items-center gap-1" style="background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.35);font-size:13px;color:#fff">
        <i class="ti tabler-calendar-event me-1"></i>
        {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}
      </div>
    </div>
  </div>
  {{-- Mini stats strip dentro del header --}}
  <div class="row g-3 mt-3">
    @php
      $pct = $stats['total'] ? round($stats['completadas']/$stats['total']*100) : 0;
    @endphp
    <div class="col-6 col-md-3">
      <div class="text-white">
        <div style="font-size:1.75rem;font-weight:800;line-height:1">{{ $stats['total'] }}</div>
        <div style="font-size:11px;opacity:.8;margin-top:.2rem">Actividades totales</div>
        <div class="mt-2" style="height:4px;border-radius:2px;background:rgba(255,255,255,.25)">
          <div style="height:100%;width:{{ $pct }}%;border-radius:2px;background:#fff"></div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="text-white">
        <div style="font-size:1.75rem;font-weight:800;line-height:1;color:#a8f5c8">{{ $stats['completadas'] }}</div>
        <div style="font-size:11px;opacity:.8;margin-top:.2rem">Completadas · {{ $pct }}%</div>
        <div class="mt-2" style="height:4px;border-radius:2px;background:rgba(255,255,255,.25)">
          <div style="height:100%;width:{{ $pct }}%;border-radius:2px;background:#28c76f"></div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="text-white">
        <div style="font-size:1.75rem;font-weight:800;line-height:1;color:#ffd6a0">{{ $stats['vencidas'] + $stats['pendientes'] }}</div>
        <div style="font-size:11px;opacity:.8;margin-top:.2rem">Requieren atención</div>
        <div class="mt-2" style="height:4px;border-radius:2px;background:rgba(255,255,255,.25)">
          <div style="height:100%;width:{{ $stats['total'] ? round(($stats['vencidas']+$stats['pendientes'])/$stats['total']*100) : 0 }}%;border-radius:2px;background:#ff9f43"></div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="text-white">
        <div style="font-size:1.75rem;font-weight:800;line-height:1;color:#a8d4ff">{{ $stats['unidades'] }}</div>
        <div style="font-size:11px;opacity:.8;margin-top:.2rem">Áreas participantes</div>
        <div class="mt-2" style="height:4px;border-radius:2px;background:rgba(255,255,255,.25)">
          <div style="height:100%;width:{{ $stats['total_unidades'] ? round($stats['unidades']/$stats['total_unidades']*100) : 0 }}%;border-radius:2px;background:#a8d4ff"></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── KPI CARDS (6 tarjetas) ── --}}
<div class="row g-4 mb-6">

  @php
  $kpis = [
    ['route'=>'sci-control-interno',    'color'=>'primary', 'icon'=>'tabler-clipboard-list',     'label'=>'Control Interno',      'sub'=>'Actividades en seguimiento', 'val'=>$stats['total'],                'trend'=>$stats['avance_global'].'% completadas', 'trend_color'=>'success'],
    ['route'=>'sci-modelo-integridad',  'color'=>'success', 'icon'=>'tabler-shield-check',        'label'=>'Modelo de Integridad', 'sub'=>'Acciones en seguimiento',   'val'=>$componentes->sum('actividades_count'), 'trend'=>round($componentes->avg('porcentaje')).'% completadas', 'trend_color'=>'success'],
    ['route'=>'rep-reconocimientos',    'color'=>'warning', 'icon'=>'tabler-star',                'label'=>'Buenas Prácticas',     'sub'=>'Registradas',               'val'=>($stats['reconocimientos']??0),  'trend'=>($stats['reconocimientos_implementadas']??0).' en implementación', 'trend_color'=>'warning'],
    ['route'=>'sci-control-interno',    'color'=>'danger',  'icon'=>'tabler-clock-exclamation',   'label'=>'Pendientes',           'sub'=>'Por atender urgente',       'val'=>$stats['pendientes'],            'trend'=>$stats['vencidas'].' vencidas', 'trend_color'=>'danger', 'route_params'=>['estado'=>'pendiente']],
    ['route'=>'adm-usuarios',           'color'=>'info',    'icon'=>'tabler-users-group',         'label'=>'Responsables',         'sub'=>'Con actividades asignadas', 'val'=>$stats['responsables_asignados'],'trend'=>'Asignados activos', 'trend_color'=>'secondary'],
    ['route'=>'mon-ranking-unidades',   'color'=>'secondary','icon'=>'tabler-building-community', 'label'=>'Áreas Participantes',  'sub'=>'En el sistema PULSO',       'val'=>$stats['unidades'],              'trend'=>'De '.$stats['total_unidades'].' áreas totales', 'trend_color'=>'secondary'],
  ];
  @endphp

  @foreach($kpis as $kpi)
  <div class="col-6 col-md-4 col-xl-2 pulso-animate" style="animation-delay:{{ $loop->index * .05 }}s">
    <a href="{{ route($kpi['route'], $kpi['route_params'] ?? []) }}" class="pulso-kpi-card text-decoration-none">
      <div class="card h-100 mb-0">
        <div class="card-body p-3">
          {{-- Icono + flecha --}}
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div class="badge rounded bg-label-{{ $kpi['color'] }} p-2 flex-shrink-0" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center">
              <i class="icon-base ti {{ $kpi['icon'] }}" style="font-size:18px"></i>
            </div>
            <i class="ti tabler-chevron-right text-body-secondary" style="font-size:14px;margin-top:2px"></i>
          </div>
          {{-- Número --}}
          <div class="text-{{ $kpi['color'] }} fw-bold mb-0" style="font-size:1.75rem;line-height:1.1">{{ $kpi['val'] }}</div>
          <div class="fw-semibold text-body mb-1" style="font-size:12px;line-height:1.3;margin-top:2px">{{ $kpi['label'] }}</div>
          {{-- Trend — truncado para evitar desborde --}}
          <div class="text-{{ $kpi['trend_color'] }} fw-medium d-flex align-items-center gap-1 text-truncate" style="font-size:11px" title="{{ $kpi['trend'] }}">
            <i class="ti tabler-trending-up flex-shrink-0" style="font-size:11px"></i>
            <span class="text-truncate">{{ $kpi['trend'] }}</span>
          </div>
        </div>
      </div>
    </a>
  </div>
  @endforeach

</div>

{{-- ── GRÁFICOS FILA 1 ── --}}
<div class="row g-6 mb-6">

  {{-- Avance mensual dual --}}
  <div class="col-xl-8">
    <div class="card h-100">
      <div class="card-header d-flex align-items-start justify-content-between pb-0">
        <div class="card-title mb-3">
          <h5 class="mb-1">Avance General del Seguimiento</h5>
          <p class="card-subtitle">Control Interno vs Modelo de Integridad · {{ now()->year }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:50%;background:#696cff;display:inline-block"></span><small class="text-muted" style="font-size:11px">SCI</small></span>
          <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:50%;background:#28c76f;display:inline-block"></span><small class="text-muted" style="font-size:11px">Integridad</small></span>
          <div class="dropdown ms-2">
            <button class="btn btn-text-secondary rounded-pill border-0 p-1" data-bs-toggle="dropdown">
              <i class="icon-base ti tabler-dots-vertical icon-md"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
              <a class="dropdown-item" href="{{ route('rep-reportes') }}"><i class="ti tabler-file-analytics me-2"></i>Reporte completo</a>
              <a class="dropdown-item" href="{{ route('mon-semaforo') }}"><i class="ti tabler-traffic-lights me-2"></i>Ver semáforo</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body pt-2">
        <div id="chartLineAvance"></div>
      </div>
    </div>
  </div>

  {{-- Panel de alertas --}}
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-0"><i class="ti tabler-bell-ringing me-2 text-warning icon-18px"></i>Alertas Activas</h5>
        </div>
        <a href="{{ route('mon-alertas') }}" class="btn btn-sm btn-label-warning rounded-pill">
          Ver todas <i class="ti tabler-arrow-right ms-1 icon-12px"></i>
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
        <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div class="badge rounded bg-label-{{ $ai['color'] }} p-2 flex-shrink-0">
            <i class="icon-base ti {{ $ai['icon'] }} icon-18px"></i>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-1">
              <span class="fw-bold text-{{ $ai['color'] }}" style="font-size:20px;line-height:1">{{ $ai['count'] }}</span>
              <span class="fw-medium" style="font-size:12px">{{ $ai['label'] }}</span>
            </div>
            <small class="text-muted">{{ $ai['sub'] }}</small>
          </div>
          <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-{{ $ai['color'] }} rounded-pill">Ver</a>
        </div>
        @endforeach
        <div class="px-4 py-3 bg-body-secondary" style="border-radius:0 0 var(--bs-card-border-radius) var(--bs-card-border-radius)">
          <div class="d-flex align-items-center justify-content-between">
            <small class="text-muted">Total alertas pendientes</small>
            <span class="badge bg-danger rounded-pill">{{ $alertasVencidas + $alertasBajas + $alertasEvidencia }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ── FILA 2: Donut + Comparativo áreas ── --}}
<div class="row g-6 mb-6">

  {{-- Donut de estados --}}
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header">
        <div class="card-title mb-0">
          <h5 class="mb-1">Actividades por Estado</h5>
          <p class="card-subtitle">{{ $stats['total'] }} actividades registradas</p>
        </div>
      </div>
      <div class="card-body pt-0">
        <div id="chartDonutEstados"></div>
        <div class="mt-2">
          @php
          $estadosDonut = [
            ['label'=>'Completadas', 'color'=>'success', 'hex'=>'#28c76f', 'val'=>$stats['completadas']],
            ['label'=>'En proceso',  'color'=>'warning', 'hex'=>'#ff9f43', 'val'=>$stats['en_proceso']],
            ['label'=>'Pendientes',  'color'=>'info',    'hex'=>'#00cfe8', 'val'=>$stats['pendientes']],
            ['label'=>'Observadas',  'color'=>'primary', 'hex'=>'#696cff', 'val'=>$stats['observados']],
            ['label'=>'Vencidas',    'color'=>'danger',  'hex'=>'#ea5455', 'val'=>$stats['vencidas']],
          ];
          @endphp
          @foreach($estadosDonut as $e)
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
              <span style="width:8px;height:8px;min-width:8px;border-radius:50%;background:{{ $e['hex'] }};display:inline-block"></span>
              <small class="text-body-secondary">{{ $e['label'] }}</small>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div class="progress" style="width:70px;height:4px">
                <div class="progress-bar rounded-pill" style="width:{{ $stats['total'] ? round($e['val']/$stats['total']*100) : 0 }}%;background:{{ $e['hex'] }}"></div>
              </div>
              <small class="fw-bold" style="min-width:24px;text-align:right">{{ $e['val'] }}</small>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- Comparativo por áreas --}}
  <div class="col-xl-8">
    <div class="card h-100">
      <div class="card-header d-flex align-items-start justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Comparativo por Áreas</h5>
          <p class="card-subtitle">% de cumplimiento por unidad orgánica</p>
        </div>
        <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-sm btn-label-primary rounded-pill">
          Ranking completo <i class="ti tabler-award ms-1 icon-12px"></i>
        </a>
      </div>
      <div class="card-body pt-2">
        <div id="chartBarAreas"></div>
      </div>
    </div>
  </div>

</div>

{{-- ── FILA 3: Componentes + Actividades próximas ── --}}
<div class="row g-6">

  {{-- Seguimiento por Componente PCM --}}
  <div class="col-xl-5">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Componentes PCM</h5>
          <p class="card-subtitle">Directiva N° 006-2019-CG-INTEG</p>
        </div>
        <a href="{{ route('mon-semaforo') }}" class="btn btn-sm btn-label-primary rounded-pill">
          Semáforo <i class="ti tabler-traffic-lights ms-1 icon-12px"></i>
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle pulso-table">
            <thead>
              <tr>
                <th>Componente</th>
                <th style="min-width:130px">Avance</th>
                <th class="text-center">Acts.</th>
              </tr>
            </thead>
            <tbody>
              @forelse($componentes->take(9) as $c)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="badge rounded bg-label-{{ $c->color }} p-1 flex-shrink-0" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center">
                      <i class="icon-base ti {{ $c->icono ?? 'tabler-point' }} icon-sm"></i>
                    </div>
                    <small class="fw-semibold text-truncate" style="max-width:150px" title="{{ $c->nombre }}">{{ $c->nombre }}</small>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:6px">
                      <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
                    </div>
                    <small class="fw-bold text-{{ $c->color }}" style="min-width:30px;font-size:11px">{{ $c->porcentaje }}%</small>
                  </div>
                </td>
                <td class="text-center">
                  <small class="fw-semibold">{{ $c->completadas_count }}</small><small class="text-muted">/{{ $c->actividades_count }}</small>
                </td>
              </tr>
              @empty
              <tr><td colspan="3" class="text-center text-muted py-4">Sin datos de componentes</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 py-3 border-top bg-body-secondary" style="border-radius:0 0 var(--bs-card-border-radius) var(--bs-card-border-radius)">
          <a href="{{ route('sci-modelo-integridad') }}" class="text-primary fw-medium" style="font-size:12px">
            Ver todas las actividades <i class="ti tabler-arrow-right icon-12px"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Actividades próximas a vencer — timeline visual --}}
  <div class="col-xl-7">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Actividades Próximas a Vencer</h5>
          <p class="card-subtitle">Ordenadas por fecha límite más cercana</p>
        </div>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-secondary rounded-pill">
          Ver todas <i class="ti tabler-arrow-right ms-1 icon-12px"></i>
        </a>
      </div>
      <div class="card-body p-0">
        @forelse($actividades_proximas as $a)
        @php
          $ec  = $a->estado_color;
          $ico = match($a->estado) {
            'completada' => 'tabler-circle-check',
            'en_proceso' => 'tabler-loader-2',
            'observado'  => 'tabler-eye',
            'vencida'    => 'tabler-alert-triangle',
            default      => 'tabler-circle',
          };
          $dias = (int) now()->diffInDays($a->fecha_limite, false);
          $dc   = $dias < 0 ? 'danger' : ($dias <= 3 ? 'danger' : ($dias <= 7 ? 'warning' : 'secondary'));
        @endphp
        <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom hover-bg-body">
          {{-- Estado icono --}}
          <div class="badge rounded bg-label-{{ $ec }} p-2 flex-shrink-0 mt-1" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center">
            <i class="icon-base ti {{ $ico }} icon-sm"></i>
          </div>
          {{-- Nombre + detalles --}}
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-semibold text-truncate" style="font-size:13px">{{ $a->nombre }}</div>
            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
              <span class="badge bg-label-secondary rounded-pill" style="font-size:10px">{{ $a->componente->nombre ?? '—' }}</span>
              @php $respPrincipal = $a->responsables->first(); @endphp
              @if($respPrincipal)
              <div class="d-flex align-items-center gap-1">
                <div class="avatar avatar-xs">
                  <span class="avatar-initial rounded-circle bg-label-primary" style="font-size:9px;width:18px;height:18px">
                    {{ strtoupper(substr($respPrincipal->name,0,2)) }}
                  </span>
                </div>
                <small class="text-muted" style="font-size:11px">{{ explode(' ',$respPrincipal->name)[0] }}</small>
              </div>
              @endif
            </div>
          </div>
          {{-- Fecha + badge días --}}
          <div class="text-end flex-shrink-0">
            <div class="text-muted" style="font-size:11px">{{ $a->fecha_limite->format('d/m/Y') }}</div>
            <div class="mt-1">
              @if($dias < 0)
                <span class="badge bg-label-danger rounded-pill" style="font-size:10px"><i class="ti tabler-flag-3 icon-10px me-1"></i>Vencida</span>
              @elseif($dias === 0)
                <span class="badge bg-danger rounded-pill text-white" style="font-size:10px">¡Hoy!</span>
              @else
                <span class="badge bg-label-{{ $dc }} rounded-pill" style="font-size:10px">{{ $dias }}d restantes</span>
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="text-center text-body-secondary py-6">
          <i class="ti tabler-circle-check icon-48px d-block mb-2 text-success"></i>
          <p class="fw-medium mb-0">¡Sin actividades pendientes próximas!</p>
          <small>Todas las actividades están al día.</small>
        </div>
        @endforelse
        @if($actividades_proximas->isNotEmpty())
        <div class="px-4 py-3 bg-body-secondary" style="border-radius:0 0 var(--bs-card-border-radius) var(--bs-card-border-radius)">
          <a href="{{ route('sci-control-interno') }}" class="text-primary fw-medium" style="font-size:12px">
            Ver todas las actividades <i class="ti tabler-arrow-right icon-12px"></i>
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>

</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark    = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const gridColor = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.05)';
  const textColor = isDark ? '#b4bdc6' : '#697a8d';
  const cardBg    = isDark ? '#2b2c40' : '#ffffff';

  // ── 1. Área doble mensual ──
  new ApexCharts(document.getElementById('chartLineAvance'), {
    chart: { type: 'area', height: 260, toolbar: { show: false }, zoom: { enabled: false },
      animations: { enabled: true, easing: 'easeinout', speed: 600 } },
    series: [
      { name: 'Control Interno',    data: @json($por_mes_sci)   },
      { name: 'Modelo Integridad',  data: @json($por_mes_integ) },
    ],
    xaxis: {
      categories: @json($meses_labels),
      labels: { style: { colors: textColor, fontSize: '11px' } },
      axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: { max: 100, min: 0, labels: { formatter: v => v + '%', style: { colors: textColor, fontSize: '11px' } } },
    colors: ['#696cff', '#28c76f'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.03, stops: [0, 95, 100] } },
    stroke: { curve: 'smooth', width: 2.5 },
    markers: { size: 4, strokeWidth: 0, hover: { size: 6 } },
    grid: { borderColor: gridColor, strokeDashArray: 5, padding: { left: 0, right: 0 } },
    legend: { show: true, position: 'top', horizontalAlign: 'right', labels: { colors: textColor },
      markers: { width: 8, height: 8, radius: 4 } },
    dataLabels: { enabled: false },
    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: v => v + '%' } },
  }).render();

  // ── 2. Donut de estados ──
  new ApexCharts(document.getElementById('chartDonutEstados'), {
    chart: { type: 'donut', height: 210, animations: { speed: 500 } },
    series: [{{ $stats['completadas'] }}, {{ $stats['en_proceso'] }}, {{ $stats['pendientes'] }}, {{ $stats['observados'] }}, {{ $stats['vencidas'] }}],
    labels: ['Completadas','En Proceso','Pendientes','Observadas','Vencidas'],
    colors: ['#28c76f','#ff9f43','#00cfe8','#696cff','#ea5455'],
    plotOptions: { pie: { donut: { size: '70%', labels: {
      show: true,
      name: { show: true, fontSize: '13px', color: textColor, offsetY: -6 },
      value: { show: true, fontSize: '22px', fontWeight: 700, color: textColor, offsetY: 4, formatter: v => v },
      total: { show: true, label: 'Total', color: textColor, fontWeight: 400, fontSize: '12px',
        formatter: () => '{{ $stats["total"] }}' },
    }}}},
    legend: { show: false },
    dataLabels: { enabled: false },
    stroke: { width: 3, colors: [cardBg] },
    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: v => v + ' actividades' } },
  }).render();

  // ── 3. Barras por áreas ──
  new ApexCharts(document.getElementById('chartBarAreas'), {
    chart: { type: 'bar', height: 255, toolbar: { show: false },
      animations: { enabled: true, easing: 'easeinout', speed: 600 } },
    series: [{ name: '% Cumplimiento', data: @json($areas_ranking->pluck('porcentaje')) }],
    xaxis: {
      categories: @json($areas_ranking->pluck('sigla')),
      labels: { style: { colors: textColor, fontSize: '11px' }, rotate: -20 },
      axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: { max: 100, min: 0, labels: { formatter: v => v + '%', style: { colors: textColor, fontSize: '11px' } } },
    colors: @json($areas_ranking->pluck('color')),
    plotOptions: { bar: {
      distributed: true, borderRadius: 6, columnWidth: '52%',
      dataLabels: { position: 'top' },
    }},
    dataLabels: { enabled: true, formatter: v => v + '%', offsetY: -18,
      style: { fontSize: '10px', fontWeight: 600, colors: [textColor] } },
    grid: { borderColor: gridColor, strokeDashArray: 5, padding: { left: 0, right: 0 } },
    legend: { show: false },
    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: v => v + '% cumplimiento' } },
  }).render();
});
</script>
@endsection
