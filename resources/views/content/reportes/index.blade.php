@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Reportes - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss',
       'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
       'resources/assets/vendor/libs/select2/select2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js',
       'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
       'resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Reportes</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-6 flex-wrap gap-3">
  <div>
    <h4 class="mb-1"><i class="ti tabler-chart-bar me-2 text-primary"></i>Reportes de Avance</h4>
    <p class="mb-0 text-body-secondary">Análisis del cumplimiento del Plan de Control Interno — {{ $anio }}</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    @php $exportParams = array_filter(['anio'=>$anio,'componente_id'=>$componente,'estado'=>$estado,'unidad_organica_id'=>$unidad]) @endphp
    <a href="{{ route('rep-reportes.exportar', [...$exportParams, 'formato'=>'pdf']) }}"
       class="btn btn-label-danger btn-sm">
      <i class="ti tabler-file-type-pdf me-1"></i>Exportar PDF
    </a>
    <a href="{{ route('rep-reportes.exportar', [...$exportParams, 'formato'=>'excel']) }}"
       class="btn btn-label-success btn-sm">
      <i class="ti tabler-file-spreadsheet me-1"></i>Exportar Excel
    </a>
    <a href="{{ route('rep-reportes') }}" class="btn btn-label-secondary btn-sm">
      <i class="ti tabler-refresh me-1"></i>Limpiar
    </a>
  </div>
</div>

{{-- Filtros --}}
<div class="card mb-6">
  <div class="card-header">
    <div class="card-title mb-0">
      <h5 class="mb-1">Filtros de Búsqueda</h5>
      <p class="card-subtitle">Filtra por año, componente, estado o unidad orgánica</p>
    </div>
  </div>
  <div class="card-body pt-4">
    <form method="GET" action="{{ route('rep-reportes') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Año</label>
          <select name="anio" class="form-select">
            @foreach($anios as $a)
            <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Componente</label>
          <select name="componente_id" class="form-select select2">
            <option value="">Todos</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ $componente == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="">Todos</option>
            @foreach(['pendiente','en_proceso','completada','vencida','cancelada'] as $e)
            <option value="{{ $e }}" {{ $estado === $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Unidad</label>
          <select name="unidad_organica_id" class="form-select select2">
            <option value="">Todas</option>
            @foreach($unidades as $u)
            <option value="{{ $u->id }}" {{ $unidad == $u->id ? 'selected' : '' }}>{{ $u->sigla }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="ti tabler-filter me-1"></i>Generar Reporte</button>
          <a href="{{ route('rep-reportes') }}" class="btn btn-label-secondary">Limpiar</a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- ── KPI rápido de resultados filtrados ── --}}
<div class="row g-6 mb-6">
  @php
  $rkpis = [
    ['val'=>$actividades->total(),                                                                'label'=>'Actividades',  'sub'=>'En el período', 'color'=>'primary', 'icon'=>'tabler-clipboard-list'],
    ['val'=>$actividades->getCollection()->where('estado','completada')->count(),                 'label'=>'Completadas',  'sub'=>'Finalizadas',   'color'=>'success', 'icon'=>'tabler-circle-check'],
    ['val'=>$actividades->getCollection()->where('estado','en_proceso')->count(),                 'label'=>'En Proceso',   'sub'=>'En desarrollo', 'color'=>'warning', 'icon'=>'tabler-loader'],
    ['val'=>$actividades->getCollection()->filter(fn($a)=>$a->estado!='completada'&&$a->fecha_limite < now())->count(),'label'=>'Vencidas','sub'=>'Sin completar','color'=>'danger','icon'=>'tabler-alert-triangle'],
  ];
  @endphp
  @foreach($rkpis as $rk)
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-4">
          <div class="badge rounded bg-label-{{ $rk['color'] }} p-2">
            <i class="icon-base ti {{ $rk['icon'] }} icon-26px"></i>
          </div>
          <span class="badge bg-label-secondary rounded-pill">{{ $anio }}</span>
        </div>
        <h4 class="mb-1 text-{{ $rk['color'] }}">{{ $rk['val'] }}</h4>
        <p class="mb-0 fw-medium">{{ $rk['label'] }}</p>
        <small class="text-body-secondary">{{ $rk['sub'] }}</small>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ── Gráfica mensual + Resumen por componente ── --}}
<div class="row g-6 mb-6">
  <div class="col-xl-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Actividades por Mes — {{ $anio }}</h5>
          <p class="card-subtitle">Total vs Completadas (barras agrupadas)</p>
        </div>
        <div class="dropdown">
          <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1" type="button" data-bs-toggle="dropdown">
            <i class="icon-base ti tabler-dots-vertical icon-md"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="{{ route('rep-reportes') }}?{{ http_build_query(request()->except('page')) }}&export=pdf">
              <i class="ti tabler-file-type-pdf me-2"></i>Exportar PDF
            </a>
          </div>
        </div>
      </div>
      <div class="card-body pt-2"><div id="chartMensual"></div></div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header">
        <div class="card-title mb-0">
          <h5 class="mb-1">Avance por Componente</h5>
          <p class="card-subtitle">% de cumplimiento</p>
        </div>
      </div>
      <div class="card-body p-0">
        <ul class="p-0 m-0">
          @foreach($resumen as $r)
          @php $rc = $r->porcentaje >= 75 ? 'success' : ($r->porcentaje >= 50 ? 'warning' : 'danger'); @endphp
          <li class="px-6 py-3 d-flex align-items-center gap-4 border-bottom">
            <div class="badge rounded bg-label-{{ $rc }} p-1_5 flex-shrink-0">
              <i class="icon-base ti tabler-point icon-sm"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between mb-1">
                <small class="fw-medium text-truncate me-2">{{ $r->nombre }}</small>
                <small class="fw-bold text-{{ $rc }} flex-shrink-0">{{ $r->porcentaje }}%</small>
              </div>
              <div class="progress" style="height:5px">
                <div class="progress-bar bg-{{ $rc }} rounded-pill" style="width:{{ $r->porcentaje }}%"></div>
              </div>
            </div>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>

{{-- Tabla detalle --}}
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="card-title mb-0">
      <h5 class="mb-1">Detalle de Actividades</h5>
      <p class="card-subtitle">{{ $actividades->total() }} registros encontrados</p>
    </div>
    <a href="{{ route('rep-reportes.exportar', [...$exportParams, 'formato'=>'pdf']) }}"
       class="btn btn-sm btn-label-danger">
      <i class="ti tabler-file-type-pdf me-1"></i>PDF
    </a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 datatables-reportes">
        <thead>
          <tr>
            <th>Código</th><th>Actividad</th><th>Componente</th><th>Unidad</th><th>Vence</th><th>Avance</th><th>Estado</th><th>Semáforo</th>
          </tr>
        </thead>
        <tbody>
          @forelse($actividades as $a)
          @php
            $ec = match($a->estado) { 'completada' => 'success', 'en_proceso' => 'warning', 'vencida' => 'danger', default => 'secondary' };
            $sc = $a->avance >= 75 ? 'success' : ($a->avance >= 50 ? 'warning' : 'danger');
          @endphp
          <tr>
            <td><small class="text-muted">{{ $a->codigo }}</small></td>
            <td><div class="fw-medium text-truncate" style="max-width:200px">{{ $a->nombre }}</div></td>
            <td><small>{{ $a->componente->nombre ?? '—' }}</small></td>
            <td><small>{{ $a->unidadOrganica->sigla ?? '—' }}</small></td>
            <td><small>{{ $a->fecha_limite->format('d/m/Y') }}</small></td>
            <td>
              <div class="d-flex align-items-center gap-1">
                <div class="progress" style="width:50px;height:5px"><div class="progress-bar bg-{{ $ec }}" style="width:{{ $a->avance }}%"></div></div>
                <small>{{ $a->avance }}%</small>
              </div>
            </td>
            <td><span class="badge bg-label-{{ $ec }}">{{ ucfirst(str_replace('_',' ',$a->estado)) }}</span></td>
            <td><i class="ti tabler-circle-filled text-{{ $sc }} icon-20px"></i></td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">Sin resultados</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($actividades->hasPages())
  <div class="card-footer">{{ $actividades->links() }}</div>
  @endif
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.select2').forEach(el => $(el).select2());

  const isDark    = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const gridColor = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.05)';
  const textColor = isDark ? '#b4bdc6' : '#697a8d';

  const meses  = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Set','Oct','Nov','Dic'];
  const rawData = @json($por_mes);
  const cats    = rawData.length ? rawData.map(d => meses[d.mes - 1]) : meses;
  const total   = rawData.map(d => parseInt(d.total));
  const comp    = rawData.map(d => parseInt(d.completadas));
  const pend    = rawData.map(d => Math.max(0, parseInt(d.total) - parseInt(d.completadas)));

  // Gráfica de barras apiladas (estilo dashboards full-version)
  new ApexCharts(document.getElementById('chartMensual'), {
    chart: {
      type: 'bar',
      height: 295,
      stacked: true,
      toolbar: { show: false },
    },
    series: [
      { name: 'Completadas', data: comp },
      { name: 'Pendientes/Proceso', data: pend },
    ],
    xaxis: {
      categories: cats,
      labels: { style: { colors: textColor } },
      axisBorder: { show: false },
      axisTicks: { show: false },
    },
    yaxis: {
      labels: { formatter: v => v + ' act.', style: { colors: textColor } },
    },
    colors: ['#28c76f', '#696cff'],
    fill: { opacity: 1 },
    dataLabels: { enabled: false },
    legend: {
      show: true,
      position: 'top',
      labels: { colors: textColor },
    },
    plotOptions: {
      bar: {
        borderRadius: 4,
        borderRadiusWhenStacked: 'last',
        columnWidth: '48%',
      },
    },
    grid: { borderColor: gridColor, strokeDashArray: 4 },
    tooltip: { y: { formatter: v => v + ' actividades' } },
  }).render();
});
</script>
@endsection
