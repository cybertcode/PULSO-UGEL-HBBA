@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Propuesta de Reconocimiento - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('rep-reconocimientos') }}">Reconocimientos</a></li>
    <li class="breadcrumb-item active">Propuesta de Reconocimiento</li>
  </ol>
</nav>

<div class="row g-6">
  {{-- Columna izquierda: foto y datos --}}
  <div class="col-lg-5">
    <div class="card">
      <div class="card-body text-center p-5">
        <img src="{{ $trabajador->foto_url }}" alt="{{ $trabajador->nombre }}"
          class="rounded-3 img-fluid mb-4 shadow"
          style="max-height:320px;width:100%;object-fit:cover">
        <h4 class="mb-1">{{ $trabajador->nombre }}</h4>
        <p class="text-muted mb-1">{{ $trabajador->cargo }}</p>
        <p class="mb-3"><span class="badge bg-label-primary">{{ $trabajador->unidadOrganica->nombre ?? '—' }}</span></p>

        @if($trabajador->numero_resolucion || $trabajador->resolucion_ruta)
        <div class="card bg-body-secondary mb-3">
          <div class="card-body py-3">
            <div class="d-flex align-items-center gap-3">
              <div class="badge rounded bg-label-danger p-2">
                <i class="ti tabler-file-certificate icon-22px"></i>
              </div>
              <div class="text-start">
                <div class="fw-medium">Documento de Reconocimiento</div>
                @if($trabajador->numero_resolucion)
                <small class="text-muted">{{ $trabajador->numero_resolucion }}</small>
                @endif
              </div>
              @if($trabajador->resolucion_ruta)
              <a href="{{ Storage::url($trabajador->resolucion_ruta) }}" target="_blank"
                class="btn btn-sm btn-label-danger ms-auto">
                <i class="ti tabler-download me-1"></i>Ver documento
              </a>
              @endif
            </div>
          </div>
        </div>
        @endif

        <div class="d-flex justify-content-center gap-4">
          <div class="text-center">
            <div class="display-6 fw-bold text-{{ $trabajador->nivel_color }}">{{ number_format($trabajador->puntaje_total, 1) }}%</div>
            <small class="text-muted">Evaluación Semestral</small>
            <br><span class="badge bg-label-{{ $trabajador->nivel_color }}">{{ $trabajador->nivel }}</span>
          </div>
          <div class="vr"></div>
          <div class="text-center">
            <div class="display-6 fw-bold text-{{ $trabajador->nivel_color }}">{{ number_format($trabajador->puntaje_total, 1) }}%</div>
            <small class="text-muted">Evaluación Anual</small>
            <br><span class="badge bg-label-{{ $trabajador->nivel_color }}">{{ $trabajador->nivel }}</span>
          </div>
        </div>

        @if($trabajador->motivo)
        <hr>
        <div class="alert alert-success text-start mt-3">
          <h6 class="mb-1"><i class="ti tabler-sparkles me-1"></i>¡Felicitaciones!</h6>
          <p class="mb-0 small">{{ $trabajador->motivo }}</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Columna derecha: gráfico y detalles --}}
  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-1">Indicadores de Evaluación Individual</h5>
        <p class="card-subtitle">Desempeño en los cuatro criterios institucionales</p>
      </div>
      <div class="card-body">
        <div id="chartRadar"></div>
        <div class="row g-3 mt-2">
          @php
          $indicadores = [
            ['label'=>'Cumplimiento',     'val'=>$trabajador->puntaje_cumplimiento,    'color'=>'success', 'icon'=>'tabler-circle-check'],
            ['label'=>'Puntualidad',      'val'=>$trabajador->puntaje_puntualidad,     'color'=>'primary', 'icon'=>'tabler-clock'],
            ['label'=>'Participación',    'val'=>$trabajador->puntaje_participacion,   'color'=>'warning', 'icon'=>'tabler-users'],
            ['label'=>'Responsabilidad',  'val'=>$trabajador->puntaje_responsabilidad, 'color'=>'info',    'icon'=>'tabler-star'],
          ];
          @endphp
          @foreach($indicadores as $ind)
          <div class="col-6">
            <div class="p-3 bg-body-secondary rounded">
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="ti {{ $ind['icon'] }} text-{{ $ind['color'] }} icon-18px"></i>
                <small class="fw-medium">{{ $ind['label'] }}</small>
              </div>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:8px">
                  <div class="progress-bar bg-{{ $ind['color'] }} rounded-pill" style="width:{{ $ind['val'] }}%"></div>
                </div>
                <span class="fw-bold text-{{ $ind['color'] }}">{{ $ind['val'] }}%</span>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Datos del Reconocimiento</h5>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-4 text-muted">Módulo</dt>
          <dd class="col-sm-8">
            @if($trabajador->categoria === 'Control Interno')
              <span class="badge" style="background:rgba(115,103,240,.15);color:#7367f0"><i class="ti tabler-shield-check me-1"></i>Control Interno (SCI)</span>
            @elseif($trabajador->categoria === 'Modelo de Integridad')
              <span class="badge" style="background:rgba(40,199,111,.15);color:#28c76f"><i class="ti tabler-star me-1"></i>Modelo de Integridad</span>
            @else
              {{ $trabajador->categoria ?? '—' }}
            @endif
          </dd>

          <dt class="col-sm-4 text-muted">Período</dt>
          <dd class="col-sm-8">{{ $trabajador->mes_nombre ? $trabajador->mes_nombre . ' ' . $trabajador->anio : 'Año ' . $trabajador->anio }}</dd>

          @if($trabajador->dni)
          <dt class="col-sm-4 text-muted">DNI</dt>
          <dd class="col-sm-8">{{ $trabajador->dni }}</dd>
          @endif

          @if($trabajador->correo)
          <dt class="col-sm-4 text-muted">Correo</dt>
          <dd class="col-sm-8"><a href="mailto:{{ $trabajador->correo }}">{{ $trabajador->correo }}</a></dd>
          @endif

          <dt class="col-sm-4 text-muted">Unidad Orgánica</dt>
          <dd class="col-sm-8">{{ $trabajador->unidadOrganica?->nombre ?? '—' }}</dd>

          <dt class="col-sm-4 text-muted">Puntaje total</dt>
          <dd class="col-sm-8">
            <span class="fw-bold text-{{ $trabajador->nivel_color }}">{{ number_format($trabajador->puntaje_total, 1) }} / 100</span>
            <span class="badge bg-{{ $trabajador->nivel_color }} ms-2">{{ $trabajador->nivel }}</span>
          </dd>

          <dt class="col-sm-4 text-muted">Registrado por</dt>
          <dd class="col-sm-8">{{ $trabajador->registradoPor?->name ?? 'Sistema' }}</dd>

          <dt class="col-sm-4 text-muted">Fecha registro</dt>
          <dd class="col-sm-8">{{ $trabajador->created_at->locale('es')->translatedFormat('d \d\e F \d\e Y, H:i') }}</dd>
        </dl>
      </div>
      <div class="card-footer d-flex gap-2">
        <a href="{{ route('rep-reconocimientos') }}" class="btn btn-label-secondary">
          <i class="ti tabler-arrow-left me-1"></i>Volver
        </a>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark    = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const textColor = isDark ? '#b4bdc6' : '#697a8d';

  new ApexCharts(document.getElementById('chartRadar'), {
    chart: { type: 'radar', height: 280, toolbar: { show: false } },
    series: [{ name: 'Puntaje', data: [
      {{ $trabajador->puntaje_cumplimiento }},
      {{ $trabajador->puntaje_puntualidad }},
      {{ $trabajador->puntaje_participacion }},
      {{ $trabajador->puntaje_responsabilidad }},
    ]}],
    xaxis: { categories: ['Cumplimiento','Puntualidad','Participación','Responsabilidad'] },
    yaxis: { show: false, max: 100 },
    colors: ['#696cff'],
    fill: { opacity: 0.2 },
    stroke: { width: 2 },
    markers: { size: 4 },
    plotOptions: { radar: { polygons: { strokeColors: isDark ? 'rgba(255,255,255,.1)' : 'rgba(0,0,0,.1)' } } },
    dataLabels: { enabled: true, formatter: v => v + '%', style: { colors: [textColor] } },
    tooltip: { y: { formatter: v => v + '%' } },
  }).render();
});
</script>
@endsection
