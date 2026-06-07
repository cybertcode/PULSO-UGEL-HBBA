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

{{-- ── KPI CARDS (6 tarjetas según informe N° 054-2026) ── --}}
<div class="row g-4 mb-6">

  {{-- Control Interno --}}
  <div class="col-6 col-md-4 col-xl-2">
    <a href="{{ route('sci-control-interno') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-2 mb-3">
            <div class="badge rounded bg-label-primary p-2">
              <i class="icon-base ti tabler-clipboard-list icon-22px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary fw-medium">Control Interno</p>
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
  <div class="col-6 col-md-4 col-xl-2">
    <a href="{{ route('sci-modelo-integridad') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-2 mb-3">
            <div class="badge rounded bg-label-success p-2">
              <i class="icon-base ti tabler-shield-check icon-22px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary fw-medium">Modelo de Integridad</p>
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
  <div class="col-6 col-md-4 col-xl-2">
    <a href="{{ route('rep-reconocimientos') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-2 mb-3">
            <div class="badge rounded bg-label-warning p-2">
              <i class="icon-base ti tabler-star icon-22px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary fw-medium">Buenas Prácticas</p>
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
  <div class="col-6 col-md-4 col-xl-2">
    <a href="{{ route('sci-control-interno', ['estado'=>'pendiente']) }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-2 mb-3">
            <div class="badge rounded bg-label-danger p-2">
              <i class="icon-base ti tabler-clock-exclamation icon-22px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary fw-medium">Pendientes</p>
              <small class="text-muted">Por atender</small>
            </div>
          </div>
          <h3 class="mb-1 text-danger">{{ $stats['pendientes'] }}</h3>
          <p class="mb-0 small text-danger fw-medium">
            <i class="ti tabler-alert-triangle icon-14px me-1"></i>{{ $stats['vencidas'] }} vencidas
          </p>
        </div>
      </div>
    </a>
  </div>

  {{-- Responsables Asignados --}}
  <div class="col-6 col-md-4 col-xl-2">
    <a href="{{ route('adm-usuarios') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-2 mb-3">
            <div class="badge rounded bg-label-info p-2">
              <i class="icon-base ti tabler-users-group icon-22px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary fw-medium">Responsables</p>
              <small class="text-muted">Asignados activos</small>
            </div>
          </div>
          <h3 class="mb-1 text-info">{{ $stats['responsables_asignados'] }}</h3>
          <p class="mb-0 small text-body-secondary fw-medium">
            <i class="ti tabler-user-check icon-14px me-1"></i>Con actividades pendientes
          </p>
        </div>
      </div>
    </a>
  </div>

  {{-- Áreas Participantes --}}
  <div class="col-6 col-md-4 col-xl-2">
    <a href="{{ route('mon-ranking-unidades') }}" class="text-decoration-none">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-2 mb-3">
            <div class="badge rounded bg-label-secondary p-2">
              <i class="icon-base ti tabler-building-community icon-22px"></i>
            </div>
            <div>
              <p class="mb-0 small text-body-secondary fw-medium">Áreas Participantes</p>
              <small class="text-muted">En el sistema</small>
            </div>
          </div>
          <h3 class="mb-1">{{ $stats['unidades'] }}</h3>
          <p class="mb-0 small text-body-secondary fw-medium">
            <i class="ti tabler-building icon-14px me-1"></i>De {{ $stats['total_unidades'] }} áreas totales
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
          <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1" type="button" data-bs-toggle="dropdown">
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
          $alertasBajas     = $alertas_recientes->where('tipo','avance_bajo')->count();
          $alertasEvidencia = $alertas_recientes->where('tipo','evidencia_falta')->count();
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
            <div class="fw-medium text-warning" style="font-size:13px">{{ $alertasBajas }} con avance bajo</div>
            <small class="text-muted">Sin progreso registrado</small>
          </div>
          <a href="{{ route('mon-alertas') }}" class="btn btn-xs btn-label-warning">Ver</a>
        </div>
        <div class="px-4 py-3 border-bottom d-flex align-items-center gap-3">
          <div class="badge rounded bg-label-info p-2 flex-shrink-0">
            <i class="icon-base ti tabler-file-off icon-18px"></i>
          </div>
          <div class="flex-grow-1">
            <div class="fw-medium" style="font-size:13px">{{ $alertasEvidencia }} sin evidencias</div>
            <small class="text-muted">Documentos pendientes</small>
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

{{-- ── FILA MEDIA: Actividades por Estado + Comparativo por Áreas ── --}}
<div class="row g-6 mb-6">

  {{-- Donut de estados --}}
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header">
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
            ['label'=>'Completadas', 'color'=>'success', 'hex'=>'#28c76f', 'val'=>$stats['completadas']],
            ['label'=>'En proceso',  'color'=>'warning', 'hex'=>'#ff9f43', 'val'=>$stats['en_proceso']],
            ['label'=>'Pendientes',  'color'=>'info',    'hex'=>'#00cfe8', 'val'=>$stats['pendientes']],
            ['label'=>'Observados',  'color'=>'primary', 'hex'=>'#696cff', 'val'=>$stats['observados']],
            ['label'=>'Vencidas',    'color'=>'danger',  'hex'=>'#ea5455', 'val'=>$stats['vencidas']],
          ];
          @endphp
          @foreach($estadosDonut as $e)
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
              <span style="width:8px;height:8px;min-width:8px;border-radius:50%;display:inline-block;background-color:{{ $e['hex'] }}"></span>
              <small class="text-body-secondary">{{ $e['label'] }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <small class="fw-bold">{{ $e['val'] }}</small>
              <small class="text-muted">({{ $stats['total'] ? round($e['val']/$stats['total']*100) : 0 }}%)</small>
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
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Comparativo por Áreas</h5>
          <p class="card-subtitle">Porcentaje de cumplimiento por unidad orgánica</p>
        </div>
        <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-sm btn-label-primary">
          Ranking <i class="ti tabler-arrow-right ms-1 icon-14px"></i>
        </a>
      </div>
      <div class="card-body pt-2">
        <div id="chartBarAreas"></div>
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
              @forelse($componentes->take(9) as $c)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="badge rounded bg-label-{{ $c->color }} p-1 flex-shrink-0">
                      <i class="icon-base ti {{ $c->icono ?? 'tabler-point' }} icon-sm"></i>
                    </div>
                    <small class="fw-medium text-truncate" style="max-width:160px" title="{{ $c->nombre }}">{{ $c->nombre }}</small>
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
  <div class="col-xl-7">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Actividades Recientes</h5>
          <p class="card-subtitle">Próximas a vencer ordenadas por fecha límite</p>
        </div>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-secondary">
          Ver todas <i class="ti tabler-arrow-right ms-1 icon-14px"></i>
        </a>
      </div>
      <div class="card-body p-0">
        @forelse($actividades_proximas as $a)
        @php
          $ec  = $a->estado_color;
          $ico = match($a->estado) { 'completada'=>'tabler-circle-check','en_proceso'=>'tabler-loader','observado'=>'tabler-eye','vencida'=>'tabler-alert-triangle',default=>'tabler-circle' };
          $diasRestantes = now()->diffInDays($a->fecha_limite, false);
        @endphp
        <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
          <div class="badge rounded bg-label-{{ $ec }} p-1_5 flex-shrink-0 mt-1">
            <i class="icon-base ti {{ $ico }} icon-sm"></i>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-medium text-truncate" style="font-size:13px">{{ $a->nombre }}</div>
            <small class="text-muted">{{ $a->componente->nombre ?? '—' }} · {{ $a->responsable->name ?? 'Sin responsable' }}</small>
          </div>
          <div class="text-end flex-shrink-0">
            <small class="text-muted">{{ $a->fecha_limite->format('d/m/Y') }}</small>
            <div>
              @if($diasRestantes >= 0)
                <span class="badge bg-label-{{ $diasRestantes <= 3 ? 'danger' : ($diasRestantes <= 7 ? 'warning' : 'secondary') }}" style="font-size:10px">{{ $diasRestantes }}d restantes</span>
              @else
                <span class="badge bg-label-danger" style="font-size:10px">Vencida</span>
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="text-center text-body-secondary py-6">
          <i class="ti tabler-circle-check icon-32px d-block mb-2 text-success"></i>
          <small>Sin actividades pendientes próximas</small>
        </div>
        @endforelse
        <div class="px-4 py-3">
          <a href="{{ route('sci-control-interno') }}" class="text-primary small fw-medium">Ver todas las actividades <i class="ti tabler-arrow-right icon-14px"></i></a>
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
  const cardBg    = isDark ? '#2b2c40' : '#fff';

  // ── 1. Área doble mensual ──
  new ApexCharts(document.getElementById('chartLineAvance'), {
    chart: { type: 'area', height: 250, toolbar: { show: false }, zoom: { enabled: false } },
    series: [
      { name: 'Sistema de Control Interno', data: @json($por_mes_sci) },
      { name: 'Modelo de Integridad',        data: @json($por_mes_integ) },
    ],
    xaxis: {
      categories: @json($meses_labels),
      labels: { style: { colors: textColor } },
      axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: { max: 100, min: 0, labels: { formatter: v => v + '%', style: { colors: textColor } } },
    colors: ['#696cff', '#28c76f'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0,90,100] } },
    stroke: { curve: 'smooth', width: 2 },
    markers: { size: 3, hover: { size: 5 } },
    grid: { borderColor: gridColor, strokeDashArray: 4 },
    legend: { show: true, position: 'top', labels: { colors: textColor } },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: v => v + '%' } },
  }).render();

  // ── 2. Donut de estados ──
  new ApexCharts(document.getElementById('chartDonutEstados'), {
    chart: { type: 'donut', height: 200 },
    series: [{{ $stats['completadas'] }}, {{ $stats['en_proceso'] }}, {{ $stats['pendientes'] }}, {{ $stats['observados'] }}, {{ $stats['vencidas'] }}],
    labels: ['Completadas', 'En Proceso', 'Pendientes', 'Observados', 'Vencidas'],
    colors: ['#28c76f', '#ff9f43', '#00cfe8', '#696cff', '#ea5455'],
    plotOptions: {
      pie: { donut: { size: '68%', labels: {
        show: true,
        name: { show: true, fontSize: '12px', color: textColor },
        value: { show: true, fontSize: '20px', fontWeight: 700, color: textColor, formatter: v => v },
        total: { show: true, label: 'Total', color: textColor, formatter: () => '{{ $stats["total"] }}' },
      }}},
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    stroke: { width: 2, colors: [cardBg] },
    tooltip: { y: { formatter: v => v + ' actividades' } },
  }).render();

  // ── 3. Barras comparativo por áreas ──
  const areasLabels = @json($areas_ranking->pluck('sigla'));
  const areasData   = @json($areas_ranking->pluck('porcentaje'));
  const areasColors = @json($areas_ranking->pluck('color'));

  new ApexCharts(document.getElementById('chartBarAreas'), {
    chart: { type: 'bar', height: 240, toolbar: { show: false } },
    series: [{ name: '% Cumplimiento', data: areasData }],
    xaxis: {
      categories: areasLabels,
      labels: { style: { colors: textColor } },
      axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: { max: 100, min: 0, labels: { formatter: v => v + '%', style: { colors: textColor } } },
    colors: areasColors,
    plotOptions: {
      bar: {
        distributed: true,
        borderRadius: 6,
        columnWidth: '55%',
        dataLabels: { position: 'top' },
      },
    },
    dataLabels: {
      enabled: true,
      formatter: v => v + '%',
      offsetY: -18,
      style: { fontSize: '11px', colors: [textColor] },
    },
    grid: { borderColor: gridColor, strokeDashArray: 4 },
    legend: { show: false },
    tooltip: { y: { formatter: v => v + '%' } },
  }).render();
});
</script>
@endsection
