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

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-3">
  <div>
    <h4 class="mb-1">Ranking de Unidades</h4>
    <p class="mb-0 text-muted">Competencia sana entre áreas para fortalecer el cumplimiento y la integridad institucional.</p>
  </div>
  <div class="d-flex gap-2 align-items-center">
    {{-- Resumen lateral --}}
    <div class="d-none d-xl-flex flex-column gap-1 me-2 text-end">
      @php
        $unCumplieron = $unidades->where('color','success')->count();
        $unRiesgo     = $unidades->where('color','danger')->count();
        $unCriticas   = $unidades->where('porcentaje','<',30)->count();
      @endphp
      <div class="d-flex align-items-center gap-2 justify-content-end">
        <small class="text-muted">{{ $unCumplieron }} Unidades más cumplieron</small>
        <span class="badge bg-label-success rounded-pill">{{ $unCumplieron }}</span>
      </div>
      <div class="d-flex align-items-center gap-2 justify-content-end">
        <small class="text-muted">{{ $unRiesgo }} Unidades en riesgo</small>
        <span class="badge bg-label-warning rounded-pill">{{ $unRiesgo }}</span>
      </div>
      <div class="d-flex align-items-center gap-2 justify-content-end">
        <small class="text-muted">{{ $unCriticas }} Unidades críticas</small>
        <span class="badge bg-label-danger rounded-pill">{{ $unCriticas }}</span>
      </div>
    </div>
    <a href="{{ route('rep-reconocimientos') }}" class="btn btn-primary">
      <i class="ti tabler-award me-1"></i>Nuevo reconocimiento
    </a>
  </div>
</div>
<p class="text-muted small mb-5">Competencia sana entre áreas para fortalecer el cumplimiento y la integridad institucional.</p>

{{-- ── Podio Top 3 ── --}}
@if($unidades->count() >= 1)
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0"><i class="ti tabler-podium me-2 text-warning"></i>Podio — Top 3</h5>
  </div>
  <div class="card-body">
    <div class="row justify-content-center align-items-end g-0">

      {{-- 2° lugar --}}
      @if($unidades->get(1))
      @php $u2 = $unidades->get(1); @endphp
      <div class="col-4 text-center px-2">
        <div class="avatar avatar-lg mx-auto mb-2">
          <span class="avatar-initial rounded-circle bg-label-secondary" style="font-size:18px;font-weight:700">
            {{ strtoupper(substr($u2->sigla ?? $u2->nombre, 0, 2)) }}
          </span>
        </div>
        <div class="fw-semibold small">{{ $u2->sigla }}</div>
        <small class="text-muted d-block mb-1" style="font-size:11px">{{ Str::limit($u2->nombre, 22) }}</small>
        {{-- Variación --}}
        @if($u2->variacion > 0)
        <span class="badge bg-label-success mb-2" style="font-size:10px"><i class="ti tabler-arrow-up icon-10px me-1"></i>+{{ $u2->variacion }}</span>
        @elseif($u2->variacion < 0)
        <span class="badge bg-label-danger mb-2" style="font-size:10px"><i class="ti tabler-arrow-down icon-10px me-1"></i>{{ $u2->variacion }}</span>
        @else
        <span class="badge bg-label-secondary mb-2" style="font-size:10px"><i class="ti tabler-minus icon-10px me-1"></i>—</span>
        @endif
        @if($u2->posicion_anterior != $u2->posicion_actual)
        <div style="font-size:10px" class="text-muted mb-2">Antes: {{ $u2->posicion_anterior }}°</div>
        @endif
        <div class="rounded-top py-3 px-2" style="background:rgba(111,66,193,.12);min-height:80px">
          <div class="badge bg-label-secondary fs-5 mb-1">2°</div>
          <div class="fw-bold fs-5 text-secondary">{{ $u2->porcentaje }}%</div>
          <small class="text-muted">{{ $u2->completadas_count }}/{{ $u2->actividades_count }} act.</small>
        </div>
      </div>
      @endif

      {{-- 1° lugar --}}
      @if($unidades->get(0))
      @php $u1 = $unidades->get(0); @endphp
      <div class="col-4 text-center px-2">
        <i class="ti tabler-crown text-warning icon-32px mb-1 d-block"></i>
        <div class="avatar mx-auto mb-2" style="width:64px;height:64px">
          <span class="avatar-initial rounded-circle bg-label-warning" style="font-size:22px;font-weight:700;width:64px;height:64px;line-height:64px">
            {{ strtoupper(substr($u1->sigla ?? $u1->nombre, 0, 2)) }}
          </span>
        </div>
        <div class="fw-bold fs-6">1. {{ $u1->sigla }}</div>
        <small class="text-muted d-block mb-1" style="font-size:11px">{{ Str::limit($u1->nombre, 26) }}</small>
        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
          <span class="badge bg-label-success" style="font-size:10px">
            <i class="ti tabler-heart icon-10px me-1"></i>Nivel: {{ $u1->porcentaje >= 85 ? 'Excelente' : ($u1->porcentaje >= 75 ? 'Bueno' : 'Regular') }}
          </span>
        </div>
        <small class="text-muted" style="font-size:10px">Antes: {{ $u1->posicion_anterior ?? 2 }}°</small>
        <div class="rounded-top py-3 px-2 mt-2" style="background:rgba(255,193,7,.15);min-height:110px">
          <div class="badge bg-warning text-dark px-3 py-1 mb-1" style="font-size:14px;font-weight:700">1°</div>
          <div class="fw-bold text-warning" style="font-size:28px">{{ $u1->porcentaje }}%</div>
          <small class="text-muted d-block">{{ $u1->completadas_count }}/{{ $u1->actividades_count }} act.</small>
        </div>
      </div>
      @endif

      {{-- 3° lugar --}}
      @if($unidades->get(2))
      @php $u3 = $unidades->get(2); @endphp
      <div class="col-4 text-center px-2">
        <div class="avatar avatar-lg mx-auto mb-2">
          <span class="avatar-initial rounded-circle bg-label-warning" style="font-size:18px;font-weight:700">
            {{ strtoupper(substr($u3->sigla ?? $u3->nombre, 0, 2)) }}
          </span>
        </div>
        <div class="fw-semibold small">{{ $u3->sigla }}</div>
        <small class="text-muted d-block mb-1" style="font-size:11px">{{ Str::limit($u3->nombre, 22) }}</small>
        @if($u3->variacion > 0)
        <span class="badge bg-label-success mb-2" style="font-size:10px"><i class="ti tabler-arrow-up icon-10px me-1"></i>+{{ $u3->variacion }}</span>
        @elseif($u3->variacion < 0)
        <span class="badge bg-label-danger mb-2" style="font-size:10px"><i class="ti tabler-arrow-down icon-10px me-1"></i>{{ $u3->variacion }}</span>
        @else
        <span class="badge bg-label-secondary mb-2" style="font-size:10px"><i class="ti tabler-minus icon-10px me-1"></i>—</span>
        @endif
        @if($u3->posicion_anterior != $u3->posicion_actual)
        <div style="font-size:10px" class="text-muted mb-2">Antes: {{ $u3->posicion_anterior }}°</div>
        @endif
        <div class="rounded-top py-3 px-2" style="background:rgba(205,127,50,.12);min-height:60px">
          <div class="badge bg-label-warning fs-5 mb-1">3°</div>
          <div class="fw-bold fs-5" style="color:#cd7f32">{{ $u3->porcentaje }}%</div>
          <small class="text-muted">{{ $u3->completadas_count }}/{{ $u3->actividades_count }} act.</small>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>
@endif

{{-- ── Gráfica de barras ── --}}
<div class="card mb-4">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Avance por Unidad Orgánica</h5>
    <span class="badge bg-label-primary">{{ now()->year }}</span>
  </div>
  <div class="card-body pt-2">
    <div id="chartRanking"></div>
  </div>
</div>

{{-- ── Tabla completa ── --}}
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Clasificación Completa</h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:60px" class="text-center">Pos.</th>
            <th>Unidad Orgánica</th>
            <th class="text-center">Total</th>
            <th class="text-center">Completadas</th>
            <th class="text-center">Vencidas</th>
            <th style="min-width:180px">Avance</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Variación</th>
          </tr>
        </thead>
        <tbody>
          @forelse($unidades as $u)
          <tr>
            <td class="text-center">
              @if($u->posicion_actual <= 3)
              <span class="badge {{ $u->posicion_actual == 1 ? 'bg-warning text-dark' : ($u->posicion_actual == 2 ? 'bg-label-secondary' : 'bg-label-warning') }} px-2 py-1" style="font-size:13px">
                {{ $u->posicion_actual }}°
              </span>
              @else
              <span class="badge bg-label-secondary">{{ $u->posicion_actual }}°</span>
              @endif
            </td>
            <td>
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-sm flex-shrink-0">
                  <span class="avatar-initial rounded-circle bg-label-{{ $u->color }}" style="font-size:11px;font-weight:700">
                    {{ strtoupper(substr($u->sigla ?? $u->nombre, 0, 2)) }}
                  </span>
                </div>
                <div>
                  <div class="fw-semibold">{{ $u->sigla }}</div>
                  <small class="text-muted">{{ Str::limit($u->nombre, 35) }}</small>
                </div>
              </div>
            </td>
            <td class="text-center fw-medium">{{ $u->actividades_count }}</td>
            <td class="text-center">
              <span class="fw-bold text-success">{{ $u->completadas_count }}</span>
            </td>
            <td class="text-center">
              <span class="fw-bold text-danger">{{ $u->vencidas_count }}</span>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:8px">
                  <div class="progress-bar bg-{{ $u->color }} rounded-pill" style="width:{{ $u->porcentaje }}%"></div>
                </div>
                <span class="fw-bold text-{{ $u->color }}" style="min-width:38px">{{ $u->porcentaje }}%</span>
              </div>
            </td>
            <td class="text-center">
              <span class="badge bg-label-{{ $u->color }}">{{ $u->semaforo }}</span>
            </td>
            <td class="text-center">
              @if($u->variacion > 0)
              <span class="badge bg-label-success"><i class="ti tabler-arrow-up icon-12px me-1"></i>+{{ $u->variacion }}</span>
              @elseif($u->variacion < 0)
              <span class="badge bg-label-danger"><i class="ti tabler-arrow-down icon-12px me-1"></i>{{ $u->variacion }}</span>
              @else
              <span class="badge bg-label-secondary"><i class="ti tabler-minus icon-12px"></i></span>
              @endif
              @if($u->posicion_anterior != $u->posicion_actual)
              <div style="font-size:10px" class="text-muted mt-1">Antes: {{ $u->posicion_anterior }}°</div>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-5">
              <i class="ti tabler-building-community icon-32px d-block mb-2"></i>
              Sin unidades orgánicas registradas
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark    = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const textColor = isDark ? '#b4bdc6' : '#697a8d';

  new ApexCharts(document.getElementById('chartRanking'), {
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    series: [{ name: 'Avance %', data: {!! $chart_data !!} }],
    xaxis: {
      categories: {!! $chart_labels !!},
      labels: { style: { colors: textColor } },
      axisBorder: { show: false },
      axisTicks: { show: false },
    },
    yaxis: { max: 100, labels: { formatter: v => v + '%', style: { colors: textColor } } },
    colors: {!! $chart_colors !!},
    dataLabels: {
      enabled: true,
      formatter: v => v + '%',
      style: { fontSize: '11px', fontWeight: 600 },
    },
    plotOptions: {
      bar: {
        borderRadius: 6,
        distributed: true,
        columnWidth: '55%',
        dataLabels: { position: 'top' },
      }
    },
    legend: { show: false },
    grid: { borderColor: isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.05)', strokeDashArray: 4 },
    tooltip: { y: { formatter: v => v + '%' } },
  }).render();
});
</script>
@endsection
