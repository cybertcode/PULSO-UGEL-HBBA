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

{{-- ── Bienvenida ── --}}
<div class="d-flex align-items-center justify-content-between mb-6 flex-wrap gap-3">
  <div>
    <h4 class="mb-1">¡Bienvenido/a, <span class="text-primary">{{ auth()->user()->name }}</span>!</h4>
    <p class="mb-0 text-body-secondary">Desde aquí puedes dar seguimiento a las actividades del Sistema de Control Interno y del Modelo de Integridad de tu entidad.</p>
  </div>
  <div class="d-flex align-items-center gap-2 text-body-secondary">
    <i class="ti tabler-calendar-event icon-18px text-primary"></i>
    <span class="fw-medium">{{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}</span>
  </div>
</div>

{{-- ── KPI CARDS (5 tarjetas tipo prototipo p01) ── --}}
<div class="row g-4 mb-6">

  {{-- Control Interno --}}
  <div class="col-6 col-md">
    <a href="{{ route('sci-control-interno') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="badge rounded bg-label-primary p-2">
              <i class="icon-base ti tabler-clipboard-list icon-26px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary">Control Interno</p>
              <small class="text-muted">Actividades en seguimiento</small>
            </div>
          </div>
          <h3 class="mb-1 text-primary">{{ $stats['total'] }}</h3>
          <p class="mb-0 small text-success fw-medium">
            <i class="ti tabler-trending-up icon-14px me-1"></i>{{ $stats['avance_global'] }}% completadas
          </p>
        </div>
      </div>
    </a>
  </div>

  {{-- Modelo de Integridad --}}
  <div class="col-6 col-md">
    <a href="{{ route('sci-modelo-integridad') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="badge rounded bg-label-success p-2">
              <i class="icon-base ti tabler-shield-check icon-26px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary">Modelo de Integridad</p>
              <small class="text-muted">Acciones en seguimiento</small>
            </div>
          </div>
          @php $avanceComp = $componentes->isNotEmpty() ? round($componentes->avg('porcentaje')) : 0; @endphp
          <h3 class="mb-1 text-success">{{ $componentes->sum('actividades_count') }}</h3>
          <p class="mb-0 small text-success fw-medium">
            <i class="ti tabler-trending-up icon-14px me-1"></i>{{ $avanceComp }}% completadas
          </p>
        </div>
      </div>
    </a>
  </div>

  {{-- Buenas Prácticas --}}
  <div class="col-6 col-md">
    <a href="{{ route('rep-reconocimientos') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="badge rounded bg-label-warning p-2">
              <i class="icon-base ti tabler-star icon-26px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary">Buenas Prácticas</p>
              <small class="text-muted">Registradas</small>
            </div>
          </div>
          <h3 class="mb-1 text-warning">{{ $stats['reconocimientos'] ?? 0 }}</h3>
          <p class="mb-0 small text-warning fw-medium">
            <i class="ti tabler-award icon-14px me-1"></i>{{ $stats['reconocimientos_implementadas'] ?? 0 }} en implementación
          </p>
        </div>
      </div>
    </a>
  </div>

  {{-- Pendientes --}}
  <div class="col-6 col-md">
    <a href="{{ route('sci-control-interno') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="badge rounded bg-label-danger p-2">
              <i class="icon-base ti tabler-clock-exclamation icon-26px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary">Pendientes</p>
              <small class="text-muted">Por atender</small>
            </div>
          </div>
          <h3 class="mb-1 text-danger">{{ $stats['pendientes'] }}</h3>
          <p class="mb-0 small text-danger fw-medium">
            <i class="ti tabler-alert-triangle icon-14px me-1"></i>Requieren atención
          </p>
        </div>
      </div>
    </a>
  </div>

  {{-- Áreas Participantes --}}
  <div class="col-6 col-md">
    <a href="{{ route('mon-ranking-unidades') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="badge rounded bg-label-info p-2">
              <i class="icon-base ti tabler-building-community icon-26px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary">Áreas Participantes</p>
              <small class="text-muted">En el sistema</small>
            </div>
          </div>
          <h3 class="mb-1 text-info">{{ $stats['unidades'] }}</h3>
          <p class="mb-0 small text-body-secondary fw-medium">
            <i class="ti tabler-users icon-14px me-1"></i>De {{ $stats['total_unidades'] ?? $stats['unidades'] }} áreas totales
          </p>
        </div>
      </div>
    </a>
  </div>

</div>

{{-- ── GRÁFICOS PRINCIPALES ── --}}
<div class="row g-6 mb-6">

  {{-- Avance mensual — área doble --}}
  <div class="col-xl-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Avance General del Seguimiento</h5>
          <p class="card-subtitle">Sistema de Control Interno vs Modelo de Integridad — {{ now()->year }}</p>
        </div>
        <div class="dropdown">
          <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1" type="button"
            data-bs-toggle="dropdown">
            <i class="icon-base ti tabler-dots-vertical icon-md"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="{{ route('rep-reportes') }}">Ver Reporte Completo</a>
            <a class="dropdown-item" href="{{ route('mon-semaforo') }}">Ver Semáforo</a>
          </div>
        </div>
      </div>
      <div class="card-body pt-2">
        <div id="chartLineAvance"></div>
      </div>
    </div>
  </div>

  {{-- Alertas y Notificaciones --}}
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1"><i class="ti tabler-bell-ringing me-1 text-warning"></i>Alertas y Notificaciones</h5>
        </div>
        <a href="{{ route('mon-alertas') }}" class="btn btn-sm btn-label-danger">
          Ver todas <i class="ti tabler-arrow-right ms-1 icon-14px"></i>
        </a>
      </div>
      <div class="card-body p-0">
        @php
          $alertasVencidas  = $alertas_recientes->where('tipo','vencimiento')->count();
          $alertasPorVencer = $alertas_recientes->where('tipo','avance_bajo')->count();
        @endphp
        <div class="px-4 py-3 border-bottom d-flex align-items-center gap-3">
          <div class="badge rounded bg-label-danger p-2 flex-shrink-0">
            <i class="icon-base ti tabler-alert-octagon icon-18px"></i>
          </div>
          <div class="flex-grow-1">
            <div class="fw-medium text-danger" style="font-size:13px">{{ $alertasVencidas }} actividades vencidas</div>
            <small class="text-muted">Requieren atención inmediata</small>
          </div>
          <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-danger">Ver</a>
        </div>
        <div class="px-4 py-3 border-bottom d-flex align-items-center gap-3">
          <div class="badge rounded bg-label-warning p-2 flex-shrink-0">
            <i class="icon-base ti tabler-clock icon-18px"></i>
          </div>
          <div class="flex-grow-1">
            <div class="fw-medium text-warning" style="font-size:13px">{{ $alertasPorVencer }} próximas a vencer</div>
            <small class="text-muted">En los próximos 7 días</small>
          </div>
          <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-warning">Ver</a>
        </div>
        <div class="px-4 py-3 border-bottom d-flex align-items-center gap-3">
          <div class="badge rounded bg-label-info p-2 flex-shrink-0">
            <i class="icon-base ti tabler-bell icon-18px"></i>
          </div>
          <div class="flex-grow-1">
            <div class="fw-medium" style="font-size:13px">{{ $alertas_recientes->count() }} nuevas alertas</div>
            <small class="text-muted">Sin leer</small>
          </div>
          <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-secondary">Ver</a>
        </div>
        <div class="px-4 py-3">
          <a href="{{ route('mon-alertas') }}" class="text-primary small fw-medium">Ver todas las alertas <i class="ti tabler-arrow-right icon-14px"></i></a>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ── FILA INFERIOR ── --}}
<div class="row g-6">

  {{-- Seguimiento por Componente --}}
  <div class="col-xl-5">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Seguimiento por Componente</h5>
          <p class="card-subtitle">Modelo de Integridad — Avance y Actividades</p>
        </div>
        <a href="{{ route('mon-semaforo') }}" class="btn btn-sm btn-label-primary">
          Semáforo <i class="ti tabler-arrow-right ms-1 icon-14px"></i>
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Componente</th>
                <th style="min-width:120px">Avance</th>
                <th class="text-center">Actividades</th>
              </tr>
            </thead>
            <tbody>
              @forelse($componentes->take(7) as $c)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="badge rounded bg-label-{{ $c->color }} p-1 flex-shrink-0">
                      <i class="icon-base ti {{ $c->icono ?? 'tabler-point' }} icon-sm"></i>
                    </div>
                    <small class="fw-medium text-truncate" style="max-width:160px">{{ $c->nombre }}</small>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:6px">
                      <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
                    </div>
                    <small class="fw-bold text-{{ $c->color }}" style="min-width:32px">{{ $c->porcentaje }}%</small>
                  </div>
                </td>
                <td class="text-center">
                  <small class="fw-medium">{{ $c->completadas_count }}</small><small class="text-muted">/{{ $c->actividades_count }}</small>
                </td>
              </tr>
              @empty
              <tr><td colspan="3" class="text-center text-muted py-4">Sin datos de componentes</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 py-3 border-top">
          <a href="{{ route('sci-modelo-integridad') }}" class="text-primary small fw-medium">Ver todas las actividades <i class="ti tabler-arrow-right icon-14px"></i></a>
        </div>
      </div>
    </div>
  </div>

  {{-- Actividades Recientes --}}
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Actividades Recientes</h5>
          <p class="card-subtitle">Últimas actualizaciones</p>
        </div>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-secondary">
          Ver todas <i class="ti tabler-arrow-right ms-1 icon-14px"></i>
        </a>
      </div>
      <div class="card-body p-0">
        @forelse($actividades_proximas as $a)
        @php
          $ec = match($a->estado) { 'completada'=>'success','en_proceso'=>'warning','vencida'=>'danger',default=>'secondary' };
          $ico = match($a->estado) { 'completada'=>'tabler-circle-check','en_proceso'=>'tabler-loader','vencida'=>'tabler-alert-triangle',default=>'tabler-circle' };
        @endphp
        <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
          <div class="badge rounded bg-label-{{ $ec }} p-1_5 flex-shrink-0 mt-1">
            <i class="icon-base ti {{ $ico }} icon-sm"></i>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-medium text-truncate" style="font-size:13px">{{ $a->nombre }}</div>
            <small class="text-muted">{{ $a->componente->nombre ?? '—' }}</small>
          </div>
          <div class="text-end flex-shrink-0">
            <small class="text-muted">{{ $a->updated_at->format('d/m/Y') }}</small>
            <div><span class="badge bg-label-{{ $ec }}" style="font-size:10px">{{ ucfirst(str_replace('_',' ',$a->estado)) }}</span></div>
          </div>
        </div>
        @empty
        <div class="text-center text-body-secondary py-6">
          <i class="ti tabler-circle-check icon-32px d-block mb-2 text-success"></i>
          <small>Sin actividades recientes</small>
        </div>
        @endforelse
        <div class="px-4 py-3">
          <a href="{{ route('sci-control-interno') }}" class="text-primary small fw-medium">Ver todas las actividades <i class="ti tabler-arrow-right icon-14px"></i></a>
        </div>
      </div>
    </div>
  </div>

  {{-- Donut de estados + Actividades por Estado --}}
  <div class="col-xl-3">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Actividades por Estado</h5>
          <p class="card-subtitle">{{ $stats['total'] }} total registradas</p>
        </div>
      </div>
      <div class="card-body pt-2">
        <div id="chartDonutEstados"></div>
        <div class="mt-3">
          @php
          $estadosDonut = [
            ['label'=>'Completadas', 'color'=>'success', 'hex'=>'#28c76f', 'val'=>$stats['completadas'], 'pct'=> $stats['total'] ? round($stats['completadas']/$stats['total']*100) : 0],
            ['label'=>'En proceso',  'color'=>'warning', 'hex'=>'#ff9f43', 'val'=>$stats['en_proceso'],  'pct'=> $stats['total'] ? round($stats['en_proceso']/$stats['total']*100) : 0],
            ['label'=>'Pendientes',  'color'=>'info',    'hex'=>'#00cfe8', 'val'=>$stats['pendientes'],  'pct'=> $stats['total'] ? round($stats['pendientes']/$stats['total']*100) : 0],
            ['label'=>'Vencidas',    'color'=>'danger',  'hex'=>'#ea5455', 'val'=>$stats['vencidas'],    'pct'=> $stats['total'] ? round($stats['vencidas']/$stats['total']*100) : 0],
          ];
          @endphp
          @foreach($estadosDonut as $e)
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
              <span class="badge rounded-pill bg-label-{{ $e['color'] }}" style="width:8px;height:8px;min-width:8px;padding:0;border-radius:50%!important;background-color:{{ $e['hex'] }}!important"></span>
              <small class="text-body-secondary">{{ $e['label'] }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <small class="fw-bold">{{ $e['val'] }}</small>
              <small class="text-muted">({{ $e['pct'] }}%)</small>
            </div>
          </div>
          @endforeach
        </div>
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

  const sciData  = @json($por_mes_sci);
  const compData = @json($por_mes_comp);

  // ── Área doble mensual ──
  new ApexCharts(document.getElementById('chartLineAvance'), {
    chart: { type: 'area', height: 260, toolbar: { show: false }, zoom: { enabled: false } },
    series: [
      { name: 'Sistema de Control Interno',  data: sciData },
      { name: 'Modelo de Integridad',         data: compData },
    ],
    xaxis: {
      categories: @json($meses_labels),
      labels: { style: { colors: textColor } },
      axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: {
      max: 100, min: 0,
      labels: { formatter: v => v + '%', style: { colors: textColor } },
    },
    colors: ['#696cff', '#28c76f'],
    fill: {
      type: 'gradient',
      gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] },
    },
    stroke: { curve: 'smooth', width: 2 },
    markers: { size: 3, hover: { size: 5 } },
    grid: { borderColor: gridColor, strokeDashArray: 4 },
    legend: { show: true, position: 'top', labels: { colors: textColor } },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: v => v + '%' } },
  }).render();

  // ── Donut de estados ──
  new ApexCharts(document.getElementById('chartDonutEstados'), {
    chart: { type: 'donut', height: 180 },
    series: [{{ $stats['completadas'] }}, {{ $stats['en_proceso'] }}, {{ $stats['pendientes'] }}, {{ $stats['vencidas'] }}],
    labels: ['Completadas', 'En Proceso', 'Pendientes', 'Vencidas'],
    colors: ['#28c76f', '#ff9f43', '#00cfe8', '#ea5455'],
    plotOptions: {
      pie: {
        donut: {
          size: '68%',
          labels: {
            show: true,
            name: { show: true, fontSize: '12px', color: textColor },
            value: { show: true, fontSize: '20px', fontWeight: 700, color: textColor, formatter: v => v },
            total: { show: true, label: 'Total', color: textColor, formatter: () => '{{ $stats["total"] }}' },
          },
        },
      },
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    stroke: { width: 2, colors: [isDark ? '#2b2c40' : '#fff'] },
    tooltip: { y: { formatter: v => v + ' actividades' } },
  }).render();
});
</script>
@endsection
