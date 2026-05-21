@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
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

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Reportes de Avance</h4>
    <p class="mb-0 text-muted">Análisis del cumplimiento del Plan de Control Interno</p>
  </div>
  <a href="{{ route('rep-reportes') }}?{{ http_build_query(request()->except('page')) }}&export=pdf" class="btn btn-outline-danger">
    <i class="ti tabler-file-type-pdf me-1"></i>Exportar PDF
  </a>
</div>

{{-- Filtros --}}
<div class="card mb-4">
  <div class="card-body">
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

{{-- Gráfica mensual + Resumen --}}
<div class="row g-4 mb-4">
  <div class="col-xl-8">
    <div class="card h-100">
      <div class="card-header"><h5 class="mb-0">Actividades por Mes — {{ $anio }}</h5></div>
      <div class="card-body"><div id="chartMensual"></div></div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header"><h5 class="mb-0">Avance por Componente</h5></div>
      <div class="card-body p-0">
        <div class="list-group list-group-flush">
          @foreach($resumen as $r)
          @php $rc = $r->porcentaje >= 75 ? 'success' : ($r->porcentaje >= 50 ? 'warning' : 'danger'); @endphp
          <div class="list-group-item px-4 py-2">
            <div class="d-flex justify-content-between mb-1">
              <small class="fw-medium text-truncate me-2" style="max-width:160px">{{ $r->nombre }}</small>
              <small class="fw-bold text-{{ $rc }}">{{ $r->porcentaje }}%</small>
            </div>
            <div class="progress" style="height:5px">
              <div class="progress-bar bg-{{ $rc }}" style="width:{{ $r->porcentaje }}%"></div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Tabla detalle --}}
<div class="card">
  <div class="card-header"><h5 class="mb-0">Detalle de Actividades ({{ $actividades->total() }})</h5></div>
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

  const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Set','Oct','Nov','Dic'];
  const data  = @json($por_mes);
  const cats  = data.map(d => meses[d.mes - 1]);
  const total = data.map(d => parseInt(d.total));
  const comp  = data.map(d => parseInt(d.completadas));

  new ApexCharts(document.getElementById('chartMensual'), {
    chart: { type: 'bar', height: 280, toolbar: { show: false } },
    series: [
      { name: 'Total', data: total },
      { name: 'Completadas', data: comp },
    ],
    xaxis: { categories: cats.length ? cats : meses },
    colors: ['#696cff', '#28c76f'],
    dataLabels: { enabled: false },
    legend: { position: 'top' },
    plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
  }).render();
});
</script>
@endsection
