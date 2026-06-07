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

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Ranking de Unidades</li>
  </ol>
</nav>

{{-- Header con gradiente --}}
<div class="pulso-page-header mb-6">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <h4 class="mb-1"><i class="ti tabler-award me-2"></i>Ranking de Unidades Orgánicas</h4>
      <p>Competencia sana entre áreas — {{ now()->format('F Y') }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      {{-- Mini resumen --}}
      @php
        $unCumplieron = $unidades->where('color','success')->count();
        $unRiesgo     = $unidades->where('color','warning')->count();
        $unCriticas   = $unidades->where('color','danger')->count();
      @endphp
      <div class="badge bg-white bg-opacity-25 text-white px-3 py-2"><i class="ti tabler-check me-1"></i>{{ $unCumplieron }} Cumplen</div>
      <div class="badge bg-white bg-opacity-25 text-white px-3 py-2"><i class="ti tabler-alert-triangle me-1"></i>{{ $unRiesgo }} En riesgo</div>
      <div class="badge bg-white bg-opacity-25 text-white px-3 py-2"><i class="ti tabler-flame me-1"></i>{{ $unCriticas }} Críticas</div>
      <a href="{{ route('rep-reconocimientos') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4)">
        <i class="ti tabler-trophy me-1"></i>Nuevo reconocimiento
      </a>
    </div>
  </div>
</div>

{{-- ── PODIO TOP 3 — diseño columnas desescalonadas ── --}}
@if($unidades->count() >= 1)
<div class="card mb-6">
  <div class="card-header border-0 pb-0">
    <div class="card-title mb-0">
      <h5 class="mb-1"><i class="ti tabler-podium me-2 text-warning"></i>Podio — Top 3</h5>
      <p class="card-subtitle">Las tres unidades con mayor avance en el período</p>
    </div>
  </div>
  <div class="card-body">
    <div class="row justify-content-center align-items-end g-4">

      {{-- 2° lugar --}}
      @if($unidades->get(1))
      @php $u2 = $unidades->get(1); @endphp
      <div class="col-sm-4 col-md-3 text-center">
        <div class="podio-card card border-0 bg-body-secondary mb-0" style="padding: 1.5rem 1rem 0">
          <div class="podio-numero bg-label-secondary">2°</div>
          <div class="avatar avatar-xl mx-auto mb-3" style="width:68px;height:68px">
            <span class="avatar-initial rounded-circle bg-label-secondary fw-bold" style="font-size:22px;width:68px;height:68px">
              {{ strtoupper(substr($u2->sigla ?? $u2->nombre, 0, 2)) }}
            </span>
          </div>
          <h6 class="mb-0 fw-bold">{{ $u2->sigla }}</h6>
          <small class="text-muted d-block mb-2" style="font-size:11px">{{ Str::limit($u2->nombre,24) }}</small>
          @if($u2->variacion > 0)
          <span class="badge bg-label-success rounded-pill mb-2" style="font-size:10px"><i class="ti tabler-arrow-up icon-10px me-1"></i>+{{ $u2->variacion }} pos.</span>
          @elseif($u2->variacion < 0)
          <span class="badge bg-label-danger rounded-pill mb-2" style="font-size:10px"><i class="ti tabler-arrow-down icon-10px me-1"></i>{{ $u2->variacion }} pos.</span>
          @else
          <span class="badge bg-label-secondary rounded-pill mb-2" style="font-size:10px"><i class="ti tabler-minus icon-10px me-1"></i>Sin cambio</span>
          @endif
          <div class="py-4 mt-2 mx-n3" style="background:rgba(134,142,150,.12);border-radius:0 0 var(--bs-card-border-radius) var(--bs-card-border-radius)">
            <div class="fw-bold text-secondary" style="font-size:2rem;line-height:1">{{ $u2->porcentaje }}%</div>
            <small class="text-muted">{{ $u2->completadas_count }}/{{ $u2->actividades_count }} completadas</small>
          </div>
        </div>
      </div>
      @endif

      {{-- 1° lugar — más alto --}}
      @if($unidades->get(0))
      @php $u1 = $unidades->get(0); @endphp
      <div class="col-sm-4 col-md-3 text-center" style="margin-bottom:-1rem">
        <div class="text-warning mb-2"><i class="ti tabler-crown icon-32px"></i></div>
        <div class="podio-card primer-lugar card border-0 mb-0" style="padding:1.5rem 1rem 0">
          <div class="podio-numero" style="background:linear-gradient(135deg,#ffd700,#ffb300);color:#7a5c00">1°</div>
          <div class="mx-auto mb-3" style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#ffc107,#ff9800);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:#fff;box-shadow:0 .5rem 1.5rem rgba(255,193,7,.4)">
            {{ strtoupper(substr($u1->sigla ?? $u1->nombre, 0, 2)) }}
          </div>
          <h5 class="mb-0 fw-bold">{{ $u1->sigla }}</h5>
          <small class="text-muted d-block mb-2" style="font-size:11px">{{ Str::limit($u1->nombre,26) }}</small>
          <span class="badge bg-label-success rounded-pill mb-2">
            <i class="ti tabler-heart me-1 icon-10px"></i>{{ $u1->porcentaje >= 85 ? 'Excelente' : ($u1->porcentaje >= 75 ? 'Bueno' : 'Regular') }}
          </span>
          @if($u1->variacion > 0)<span class="badge bg-label-success rounded-pill mb-2 ms-1" style="font-size:10px"><i class="ti tabler-arrow-up icon-10px me-1"></i>+{{ $u1->variacion }}</span>@endif
          <div class="py-4 mt-2 mx-n3" style="background:rgba(255,193,7,.15);border-radius:0 0 calc(var(--bs-card-border-radius) - 2px) calc(var(--bs-card-border-radius) - 2px)">
            <div class="fw-bold text-warning" style="font-size:2.5rem;line-height:1">{{ $u1->porcentaje }}%</div>
            <small class="text-muted">{{ $u1->completadas_count }}/{{ $u1->actividades_count }} completadas</small>
            @if($u1->posicion_anterior && $u1->posicion_anterior != 1)
            <div class="text-muted" style="font-size:10px;margin-top:4px">Antes: {{ $u1->posicion_anterior }}° lugar</div>
            @endif
          </div>
        </div>
      </div>
      @endif

      {{-- 3° lugar --}}
      @if($unidades->get(2))
      @php $u3 = $unidades->get(2); @endphp
      <div class="col-sm-4 col-md-3 text-center">
        <div class="podio-card card border-0 bg-body-secondary mb-0" style="padding:1.5rem 1rem 0">
          <div class="podio-numero bg-label-warning">3°</div>
          <div class="avatar avatar-xl mx-auto mb-3" style="width:68px;height:68px">
            <span class="avatar-initial rounded-circle bg-label-warning fw-bold" style="font-size:22px;width:68px;height:68px">
              {{ strtoupper(substr($u3->sigla ?? $u3->nombre, 0, 2)) }}
            </span>
          </div>
          <h6 class="mb-0 fw-bold">{{ $u3->sigla }}</h6>
          <small class="text-muted d-block mb-2" style="font-size:11px">{{ Str::limit($u3->nombre,24) }}</small>
          @if($u3->variacion > 0)
          <span class="badge bg-label-success rounded-pill mb-2" style="font-size:10px"><i class="ti tabler-arrow-up icon-10px me-1"></i>+{{ $u3->variacion }} pos.</span>
          @elseif($u3->variacion < 0)
          <span class="badge bg-label-danger rounded-pill mb-2" style="font-size:10px"><i class="ti tabler-arrow-down icon-10px me-1"></i>{{ $u3->variacion }} pos.</span>
          @else
          <span class="badge bg-label-secondary rounded-pill mb-2" style="font-size:10px"><i class="ti tabler-minus icon-10px me-1"></i>Sin cambio</span>
          @endif
          <div class="py-4 mt-2 mx-n3" style="background:rgba(205,127,50,.12);border-radius:0 0 var(--bs-card-border-radius) var(--bs-card-border-radius)">
            <div class="fw-bold" style="font-size:2rem;line-height:1;color:#cd7f32">{{ $u3->porcentaje }}%</div>
            <small class="text-muted">{{ $u3->completadas_count }}/{{ $u3->actividades_count }} completadas</small>
          </div>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>
@endif

{{-- ── Gráfico de barras ── --}}
<div class="card mb-6">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div class="card-title mb-0">
      <h5 class="mb-1">Avance por Unidad Orgánica</h5>
      <p class="card-subtitle">Porcentaje de cumplimiento acumulado {{ now()->year }}</p>
    </div>
    <span class="badge bg-label-primary rounded-pill px-3">{{ now()->year }}</span>
  </div>
  <div class="card-body pt-2">
    <div id="chartRanking"></div>
  </div>
</div>

{{-- ── Clasificación completa con ranking-item pattern ── --}}
<div class="row g-6">
  <div class="col-xl-7">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Clasificación Completa</h5>
          <p class="card-subtitle">Todas las unidades orgánicas ordenadas por avance</p>
        </div>
      </div>
      <div class="card-body p-0">
        @forelse($unidades as $u)
        @php
          $posClass = match($u->posicion_actual) { 1=>'pos-1',2=>'pos-2',3=>'pos-3',default=>'pos-n' };
        @endphp
        <div class="ranking-item">
          {{-- Posición --}}
          <div class="ranking-pos {{ $posClass }}" style="font-size:13px">{{ $u->posicion_actual }}</div>
          {{-- Avatar --}}
          <div class="avatar avatar-sm flex-shrink-0">
            <span class="avatar-initial rounded-circle bg-label-{{ $u->color }}" style="font-size:11px;font-weight:700">
              {{ strtoupper(substr($u->sigla ?? $u->nombre, 0, 2)) }}
            </span>
          </div>
          {{-- Nombre + barra --}}
          <div class="flex-grow-1 overflow-hidden">
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="fw-semibold" style="font-size:13px">{{ $u->sigla }}</span>
              <span class="badge bg-label-{{ $u->color }} rounded-pill" style="font-size:10px">{{ $u->semaforo }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:5px">
                <div class="progress-bar bg-{{ $u->color }} rounded-pill" style="width:{{ $u->porcentaje }}%"></div>
              </div>
              <small class="fw-bold text-{{ $u->color }}" style="min-width:34px;font-size:12px">{{ $u->porcentaje }}%</small>
            </div>
          </div>
          {{-- Variación --}}
          <div class="text-end flex-shrink-0" style="min-width:50px">
            @if($u->variacion > 0)
            <div class="variacion-up"><i class="ti tabler-arrow-up icon-12px"></i>+{{ $u->variacion }}</div>
            @elseif($u->variacion < 0)
            <div class="variacion-down"><i class="ti tabler-arrow-down icon-12px"></i>{{ $u->variacion }}</div>
            @else
            <div class="variacion-same"><i class="ti tabler-minus icon-12px"></i></div>
            @endif
            @if($u->posicion_anterior && $u->posicion_anterior != $u->posicion_actual)
            <div style="font-size:9px" class="text-muted">era {{ $u->posicion_anterior }}°</div>
            @endif
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-6">
          <i class="ti tabler-building-community icon-48px d-block mb-2"></i>
          <p class="mb-0">Sin unidades registradas</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Panel de detalles + resumen --}}
  <div class="col-xl-5">
    {{-- Resumen estadístico --}}
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Resumen del Período</h5>
      </div>
      <div class="card-body">
        @php
          $promedio = $unidades->avg('porcentaje');
          $max      = $unidades->max('porcentaje');
          $min      = $unidades->min('porcentaje');
        @endphp
        <div class="row g-4 text-center">
          <div class="col-4">
            <div class="text-primary" style="font-size:1.75rem;font-weight:800;line-height:1">{{ round($promedio) }}%</div>
            <small class="text-muted">Promedio</small>
          </div>
          <div class="col-4">
            <div class="text-success" style="font-size:1.75rem;font-weight:800;line-height:1">{{ $max }}%</div>
            <small class="text-muted">Máximo</small>
          </div>
          <div class="col-4">
            <div class="text-danger" style="font-size:1.75rem;font-weight:800;line-height:1">{{ $min }}%</div>
            <small class="text-muted">Mínimo</small>
          </div>
        </div>
        <hr class="my-4">
        <div class="row g-3">
          <div class="col-4 text-center">
            <div class="badge bg-label-success rounded-circle p-3 mb-2 d-block mx-auto" style="width:48px;height:48px;display:flex!important;align-items:center;justify-content:center">
              <i class="ti tabler-check icon-22px"></i>
            </div>
            <div class="fw-bold" style="font-size:1.25rem">{{ $unidades->where('color','success')->count() }}</div>
            <small class="text-muted" style="font-size:11px">Cumplen (≥75%)</small>
          </div>
          <div class="col-4 text-center">
            <div class="badge bg-label-warning rounded-circle p-3 mb-2 d-block mx-auto" style="width:48px;height:48px;display:flex!important;align-items:center;justify-content:center">
              <i class="ti tabler-alert-triangle icon-22px"></i>
            </div>
            <div class="fw-bold" style="font-size:1.25rem">{{ $unidades->where('color','warning')->count() }}</div>
            <small class="text-muted" style="font-size:11px">En riesgo (50–74%)</small>
          </div>
          <div class="col-4 text-center">
            <div class="badge bg-label-danger rounded-circle p-3 mb-2 d-block mx-auto" style="width:48px;height:48px;display:flex!important;align-items:center;justify-content:center">
              <i class="ti tabler-flame icon-22px"></i>
            </div>
            <div class="fw-bold" style="font-size:1.25rem">{{ $unidades->where('color','danger')->count() }}</div>
            <small class="text-muted" style="font-size:11px">Críticas (&lt;50%)</small>
          </div>
        </div>
      </div>
    </div>

    {{-- Acciones rápidas --}}
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Acciones Rápidas</h5>
      </div>
      <div class="card-body p-0">
        @foreach([
          ['route'=>'sci-control-interno','icon'=>'tabler-clipboard-list','label'=>'Ver actividades pendientes','color'=>'primary'],
          ['route'=>'mon-alertas','icon'=>'tabler-bell','label'=>'Revisar alertas activas','color'=>'warning'],
          ['route'=>'rep-reportes','icon'=>'tabler-file-analytics','label'=>'Exportar reporte PDF/Excel','color'=>'success'],
          ['route'=>'rep-reconocimientos','icon'=>'tabler-trophy','label'=>'Registrar reconocimiento','color'=>'info'],
        ] as $acc)
        <a href="{{ route($acc['route']) }}" class="d-flex align-items-center gap-3 px-4 py-3 border-bottom text-decoration-none hover-bg-body">
          <div class="badge rounded bg-label-{{ $acc['color'] }} p-2">
            <i class="icon-base ti {{ $acc['icon'] }} icon-18px"></i>
          </div>
          <span class="fw-medium" style="font-size:13px">{{ $acc['label'] }}</span>
          <i class="ti tabler-chevron-right text-muted ms-auto icon-14px"></i>
        </a>
        @endforeach
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
  const gridColor = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.05)';

  new ApexCharts(document.getElementById('chartRanking'), {
    chart: { type: 'bar', height: 300, toolbar: { show: false },
      animations: { enabled: true, easing: 'easeinout', speed: 700 } },
    series: [{ name: '% Cumplimiento', data: {!! $chart_data !!} }],
    xaxis: {
      categories: {!! $chart_labels !!},
      labels: { style: { colors: textColor, fontSize: '11px' }, rotate: -15 },
      axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: { max: 100, min: 0, labels: { formatter: v => v + '%', style: { colors: textColor, fontSize: '11px' } } },
    colors: {!! $chart_colors !!},
    dataLabels: { enabled: true, formatter: v => v + '%', offsetY: -18,
      style: { fontSize: '10px', fontWeight: 700, colors: [textColor] } },
    plotOptions: { bar: { borderRadius: 7, distributed: true, columnWidth: '52%', dataLabels: { position: 'top' } } },
    legend: { show: false },
    grid: { borderColor: gridColor, strokeDashArray: 5, padding: { left: 0, right: 0 } },
    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: v => v + '% cumplimiento' } },
  }).render();
});
</script>
@endsection
