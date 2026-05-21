@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Ranking de Unidades - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

<div class="mb-4">
  <h4 class="mb-1">Ranking de Unidades Orgánicas</h4>
  <p class="mb-0 text-muted">Clasificación por avance en actividades de Control Interno</p>
</div>

{{-- Podio Top 3 --}}
@if($unidades->count() >= 3)
<div class="card mb-4">
  <div class="card-body">
    <div class="row justify-content-center g-0 align-items-end">
      @php $items = [[1,'#6f42c1','warning'], [0,'#28c76f','success'], [2,'#cd7f32','warning']]; @endphp
      @foreach($items as [$idx,$bg,$badge])
      @if($unidades->get($idx))
      @php $u = $unidades->get($idx); $pos = $idx + 1; @endphp
      <div class="col-4 text-center">
        @if($pos == 1)<i class="ti tabler-crown text-warning icon-28px mb-1 d-block"></i>@endif
        <div class="avatar avatar-lg mx-auto mb-2">
          <span class="avatar-initial rounded-circle bg-label-{{ $u->color }} fs-5">
            {{ substr($u->sigla ?? $u->nombre, 0, 2) }}
          </span>
        </div>
        <div class="fw-medium small">{{ $u->sigla }}</div>
        <small class="text-muted d-block mb-2" style="font-size:11px">{{ Str::limit($u->nombre, 20) }}</small>
        <div class="rounded-top py-2" style="background:{{ $bg }}22;min-height:{{ $pos==1?'100':'70' }}px">
          <div class="badge mb-1" style="background:{{ $bg }}">{{ $pos }}°</div>
          <div class="fw-bold" style="color:{{ $bg }}">{{ $u->porcentaje }}%</div>
          <small class="text-muted d-block">{{ $u->completadas_count }}/{{ $u->actividades_count }} act.</small>
        </div>
      </div>
      @endif
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- Gráfica de barras --}}
<div class="card mb-4">
  <div class="card-header"><h5 class="mb-0">Avance por Unidad</h5></div>
  <div class="card-body"><div id="chartRanking"></div></div>
</div>

{{-- Tabla completa --}}
<div class="card">
  <div class="card-header"><h5 class="mb-0">Detalle Completo</h5></div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr><th>#</th><th>Unidad</th><th>Total Act.</th><th>Completadas</th><th>Vencidas</th><th>Avance</th><th>Semáforo</th></tr>
        </thead>
        <tbody>
          @foreach($unidades as $i => $u)
          <tr>
            <td><span class="badge {{ $i < 3 ? 'bg-label-warning' : 'bg-label-secondary' }}">{{ $i+1 }}</span></td>
            <td>
              <div class="fw-medium">{{ $u->sigla }}</div>
              <small class="text-muted">{{ $u->nombre }}</small>
            </td>
            <td>{{ $u->actividades_count }}</td>
            <td><span class="text-success fw-medium">{{ $u->completadas_count }}</span></td>
            <td><span class="text-danger fw-medium">{{ $u->vencidas_count }}</span></td>
            <td style="min-width:160px">
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:8px">
                  <div class="progress-bar bg-{{ $u->color }}" style="width:{{ $u->porcentaje }}%"></div>
                </div>
                <small class="fw-bold text-{{ $u->color }}">{{ $u->porcentaje }}%</small>
              </div>
            </td>
            <td>
              <div class="d-flex align-items-center gap-1">
                <i class="ti tabler-circle-filled text-{{ $u->color }} icon-18px"></i>
                <span class="badge bg-label-{{ $u->color }}">{{ $u->semaforo }}</span>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  new ApexCharts(document.getElementById('chartRanking'), {
    chart: { type: 'bar', height: 280, toolbar: { show: false } },
    series: [{ name: 'Avance %', data: {!! $chart_data !!} }],
    xaxis: { categories: {!! $chart_labels !!} },
    colors: {!! $chart_colors !!},
    dataLabels: { enabled: true, formatter: v => v + '%' },
    plotOptions: { bar: { borderRadius: 4, distributed: true, columnWidth: '50%' } },
    legend: { show: false },
    yaxis: { max: 100, labels: { formatter: v => v + '%' } },
  }).render();
});
</script>
@endsection
