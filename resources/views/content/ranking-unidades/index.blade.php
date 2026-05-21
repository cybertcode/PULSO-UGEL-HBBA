@php
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
  <p class="mb-0 text-muted">Comparativo de cumplimiento entre unidades de la UGEL Huacaybamba</p>
</div>

<!-- Podio top 3 unidades -->
<div class="row g-4 mb-4">
  @php
  $podio = [
    ['pos' => 2, 'nombre' => 'Área de Gestión Pedagógica', 'pct' => 85, 'color' => 'secondary'],
    ['pos' => 1, 'nombre' => 'Dirección', 'pct' => 92, 'color' => 'warning'],
    ['pos' => 3, 'nombre' => 'Área de Gestión Institucional', 'pct' => 78, 'color' => 'danger'],
  ];
  @endphp
  @foreach($podio as $p)
  <div class="col-md-4">
    <div class="card text-center border-{{ $p['color'] }} {{ $p['pos'] == 1 ? 'border-2' : 'border-opacity-50' }}">
      <div class="card-body py-4">
        @if($p['pos'] == 1)
        <i class="ti tabler-crown text-warning icon-28px mb-2"></i><br>
        @endif
        <span class="badge bg-{{ $p['color'] }} rounded-circle p-3 fs-4 mb-3 d-inline-flex align-items-center justify-content-center" style="width:50px;height:50px">{{ $p['pos'] }}</span>
        <h6 class="mb-2">{{ $p['nombre'] }}</h6>
        <h3 class="text-{{ $p['color'] }} mb-1">{{ $p['pct'] }}%</h3>
        <div class="progress" style="height:6px">
          <div class="progress-bar bg-{{ $p['color'] }}" style="width:{{ $p['pct'] }}%"></div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

<!-- Gráfico comparativo -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">Comparativo de Avance por Unidad</h5>
  </div>
  <div class="card-body">
    <div id="rankingBarChart"></div>
  </div>
</div>

<!-- Tabla completa -->
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Detalle por Unidad Orgánica</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Unidad Orgánica</th>
            <th class="text-center">Actividades</th>
            <th class="text-center">Completadas</th>
            <th>Avance</th>
            <th class="text-center">Estado</th>
          </tr>
        </thead>
        <tbody>
          @php
          $unidades = [
            [1, 'Dirección', 20, 18, 92, 'success', 'Verde'],
            [2, 'Área de Gestión Pedagógica', 25, 21, 85, 'success', 'Verde'],
            [3, 'Área de Gestión Institucional', 18, 14, 78, 'success', 'Verde'],
            [4, 'Área de Administración', 22, 14, 64, 'warning', 'Amarillo'],
            [5, 'Área de Asesoría Jurídica', 10, 5, 50, 'warning', 'Amarillo'],
          ];
          @endphp
          @foreach($unidades as [$pos, $nombre, $total, $comp, $pct, $color, $label])
          <tr>
            <td class="fw-bold">{{ $pos }}</td>
            <td>{{ $nombre }}</td>
            <td class="text-center">{{ $total }}</td>
            <td class="text-center">{{ $comp }}</td>
            <td style="min-width:150px">
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%"></div>
                </div>
                <small class="fw-medium">{{ $pct }}%</small>
              </div>
            </td>
            <td class="text-center">
              <span class="badge bg-label-{{ $color }}">{{ $label }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
