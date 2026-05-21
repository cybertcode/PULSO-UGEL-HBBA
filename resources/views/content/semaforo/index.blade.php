@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Semáforo Institucional - PULSO UGEL')

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
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sci-modelo-integridad') }}">Modelo de Integridad</a></li>
    <li class="breadcrumb-item active">Semáforo Institucional</li>
  </ol>
</nav>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1">Semáforo Institucional</h4>
    <p class="mb-0 text-muted">Visualiza rápidamente el estado de cumplimiento de los componentes del Modelo de Integridad.</p>
  </div>
  <button class="btn btn-label-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCriterios">
    <i class="ti tabler-info-circle me-1"></i>Ver criterios de evaluación
  </button>
</div>

{{-- ── Resumen superior: gauge + 3 contadores grandes ── --}}
<div class="row g-4 mb-5">

  {{-- Gauge velocímetro --}}
  <div class="col-xl-3 col-md-6">
    <div class="card h-100">
      <div class="card-body text-center d-flex flex-column align-items-center justify-content-center py-4">
        <div id="gaugeAvance"></div>
        @php
          $nivel = round($avance_global) >= $umbral_verde ? 'Bueno' : (round($avance_global) >= $umbral_amarillo ? 'Regular' : 'En riesgo');
          $nivelColor = round($avance_global) >= $umbral_verde ? 'success' : (round($avance_global) >= $umbral_amarillo ? 'warning' : 'danger');
        @endphp
        <h5 class="mb-1 mt-n2">Cumplimiento General</h5>
        <span class="badge bg-label-{{ $nivelColor }} mt-1">Nivel: {{ $nivel }}</span>
        <small class="text-muted mt-1">Cumplimiento General <i class="ti tabler-trending-up icon-12px text-success"></i></small>
      </div>
    </div>
  </div>

  {{-- Contador: Cumplido --}}
  @php
    $cumplidos  = $componentes->where('porcentaje', '>=', $umbral_verde)->count();
    $en_proceso = $componentes->where('porcentaje', '<', $umbral_verde)->where('porcentaje', '>=', $umbral_amarillo)->count();
    $en_riesgo  = $componentes->where('porcentaje', '<', $umbral_amarillo)->count();
  @endphp
  <div class="col-xl-3 col-md-6">
    <div class="card h-100 border-success border-opacity-25">
      <div class="card-body d-flex align-items-center gap-4 py-4">
        {{-- Semáforo visual --}}
        <div style="background:#1a1a2e;border-radius:12px;padding:10px 8px;display:flex;flex-direction:column;gap:6px;align-items:center">
          <span style="width:18px;height:18px;border-radius:50%;background:#ea5455;opacity:0.3"></span>
          <span style="width:18px;height:18px;border-radius:50%;background:#ff9f43;opacity:0.3"></span>
          <span style="width:18px;height:18px;border-radius:50%;background:#28c76f"></span>
        </div>
        <div>
          <h2 class="mb-0 text-success fw-bold">{{ $cumplidos }}</h2>
          <p class="mb-0 fw-semibold text-success" style="font-size:16px">Cumplido</p>
          <small class="text-muted">≥ {{ $umbral_verde }}%</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Contador: En proceso --}}
  <div class="col-xl-3 col-md-6">
    <div class="card h-100 border-warning border-opacity-25">
      <div class="card-body d-flex align-items-center gap-4 py-4">
        <div style="background:#1a1a2e;border-radius:12px;padding:10px 8px;display:flex;flex-direction:column;gap:6px;align-items:center">
          <span style="width:18px;height:18px;border-radius:50%;background:#ea5455;opacity:0.3"></span>
          <span style="width:18px;height:18px;border-radius:50%;background:#ff9f43"></span>
          <span style="width:18px;height:18px;border-radius:50%;background:#28c76f;opacity:0.3"></span>
        </div>
        <div>
          <h2 class="mb-0 text-warning fw-bold">{{ $en_proceso }}</h2>
          <p class="mb-0 fw-semibold text-warning" style="font-size:16px">En proceso</p>
          <small class="text-muted">{{ $umbral_amarillo }}–{{ $umbral_verde-1 }}%</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Contador: En riesgo --}}
  <div class="col-xl-3 col-md-6">
    <div class="card h-100 border-danger border-opacity-25">
      <div class="card-body d-flex align-items-center gap-4 py-4">
        <div style="background:#1a1a2e;border-radius:12px;padding:10px 8px;display:flex;flex-direction:column;gap:6px;align-items:center">
          <span style="width:18px;height:18px;border-radius:50%;background:#ea5455"></span>
          <span style="width:18px;height:18px;border-radius:50%;background:#ff9f43;opacity:0.3"></span>
          <span style="width:18px;height:18px;border-radius:50%;background:#28c76f;opacity:0.3"></span>
        </div>
        <div>
          <h2 class="mb-0 text-danger fw-bold">{{ $en_riesgo }}</h2>
          <p class="mb-0 fw-semibold text-danger" style="font-size:16px">En riesgo</p>
          <small class="text-muted">&lt; {{ $umbral_amarillo }}%</small>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ── Semáforo por Componente ── --}}
<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="mb-0">Por Componente del Modelo de Integridad</h5>
  <a href="{{ route('adm-configuracion') }}" class="btn btn-sm btn-label-secondary">
    <i class="ti tabler-settings icon-14px me-1"></i>Configurar umbrales
  </a>
</div>
<div class="row g-3 mb-5">
  @foreach($componentes as $c)
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body pb-2">
        {{-- Semáforo + número componente --}}
        <div class="d-flex align-items-start justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            {{-- Mini semáforo --}}
            <div style="background:#1a1a2e;border-radius:8px;padding:6px 5px;display:flex;flex-direction:column;gap:4px;align-items:center">
              <span style="width:10px;height:10px;border-radius:50%;background:#ea5455;opacity:{{ $c->color === 'danger' ? '1' : '0.2' }}"></span>
              <span style="width:10px;height:10px;border-radius:50%;background:#ff9f43;opacity:{{ $c->color === 'warning' ? '1' : '0.2' }}"></span>
              <span style="width:10px;height:10px;border-radius:50%;background:#28c76f;opacity:{{ $c->color === 'success' ? '1' : '0.2' }}"></span>
            </div>
            <div>
              <small class="text-muted d-block">Comp. {{ $c->numero }}</small>
              <small class="fw-bold" style="font-size:11px">{{ $c->semaforo }}</small>
            </div>
          </div>
          <span class="fw-bold text-{{ $c->color }}" style="font-size:20px">{{ $c->porcentaje }}%</span>
        </div>

        <h6 class="mb-3 fw-medium" style="font-size:13px">{{ $c->nombre }}</h6>

        {{-- Estado badge + progreso --}}
        <div class="mb-1">
          <span class="badge bg-label-{{ $c->color }} mb-2">
            <i class="ti tabler-{{ $c->color === 'success' ? 'circle-check' : ($c->color === 'warning' ? 'clock' : 'alert-triangle') }} icon-12px me-1"></i>{{ $c->semaforo }} {{ $c->porcentaje }}%
          </span>
        </div>
        <div class="progress mb-3" style="height:6px">
          <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
        </div>
      </div>
      <div class="card-footer py-2 px-3">
        <a href="{{ route('sci-control-interno') }}?componente_id={{ $c->id }}"
           class="btn btn-xs btn-label-{{ $c->color }} w-100 text-center">
          <i class="ti tabler-download icon-12px me-1"></i>{{ $c->semaforo }}
        </a>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ── Semáforo por Unidad Orgánica ── --}}
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="card-title mb-0">
      <h5 class="mb-1"><i class="ti tabler-building-community me-2 text-primary"></i>Por Unidad Orgánica</h5>
      <p class="card-subtitle">Avance de cada área en actividades asignadas</p>
    </div>
    <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-sm btn-label-secondary">
      <i class="ti tabler-trophy icon-14px me-1"></i>Ranking
    </a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:40px">#</th>
            <th>Unidad Orgánica</th>
            <th class="text-center">Actividades</th>
            <th style="min-width:200px">Avance</th>
            <th class="text-center">Semáforo</th>
          </tr>
        </thead>
        <tbody>
          @forelse($unidades as $i => $u)
          <tr>
            <td><span class="badge bg-label-{{ $u->color }}">{{ $i + 1 }}</span></td>
            <td>
              <div class="fw-medium">{{ $u->nombre }}</div>
              <small class="text-muted">{{ $u->sigla }}</small>
            </td>
            <td class="text-center">
              <span class="fw-medium">{{ $u->completadas_count }}</span>
              <span class="text-muted">/{{ $u->actividades_count }}</span>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:8px">
                  <div class="progress-bar bg-{{ $u->color }}" style="width:{{ $u->porcentaje }}%"></div>
                </div>
                <span class="fw-bold text-{{ $u->color }}" style="min-width:40px">{{ $u->porcentaje }}%</span>
              </div>
            </td>
            <td class="text-center">
              <span class="badge bg-label-{{ $u->color }} px-3">
                <i class="ti tabler-circle-filled me-1 icon-12px"></i>{{ $u->semaforo }}
              </span>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted py-5">Sin unidades orgánicas registradas</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal criterios --}}
<div class="modal fade" id="modalCriterios" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti tabler-info-circle me-2"></i>Criterios de Evaluación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">Los umbrales están configurados en <a href="{{ route('adm-configuracion') }}">Configuración del Sistema</a>.</p>
        <div class="alert alert-success d-flex align-items-center gap-3">
          <div style="background:#1a1a2e;border-radius:8px;padding:6px 5px;display:flex;flex-direction:column;gap:3px">
            <span style="width:10px;height:10px;border-radius:50%;background:#ea5455;opacity:0.3"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#ff9f43;opacity:0.3"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#28c76f"></span>
          </div>
          <div><strong>Verde — Cumplido:</strong> Avance ≥ {{ $umbral_verde }}%</div>
        </div>
        <div class="alert alert-warning d-flex align-items-center gap-3">
          <div style="background:#1a1a2e;border-radius:8px;padding:6px 5px;display:flex;flex-direction:column;gap:3px">
            <span style="width:10px;height:10px;border-radius:50%;background:#ea5455;opacity:0.3"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#ff9f43"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#28c76f;opacity:0.3"></span>
          </div>
          <div><strong>Amarillo — En proceso:</strong> Entre {{ $umbral_amarillo }}% y {{ $umbral_verde - 1 }}%</div>
        </div>
        <div class="alert alert-danger d-flex align-items-center gap-3">
          <div style="background:#1a1a2e;border-radius:8px;padding:6px 5px;display:flex;flex-direction:column;gap:3px">
            <span style="width:10px;height:10px;border-radius:50%;background:#ea5455"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#ff9f43;opacity:0.3"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#28c76f;opacity:0.3"></span>
          </div>
          <div><strong>Rojo — En riesgo:</strong> Avance &lt; {{ $umbral_amarillo }}%</div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const textColor = isDark ? '#b4bdc6' : '#697a8d';
  const avance = {{ round($avance_global) }};
  const color = avance >= {{ $umbral_verde }} ? '#28c76f' : (avance >= {{ $umbral_amarillo }} ? '#ff9f43' : '#ea5455');

  new ApexCharts(document.getElementById('gaugeAvance'), {
    chart: { type: 'radialBar', height: 200, sparkline: { enabled: true } },
    series: [avance],
    plotOptions: {
      radialBar: {
        startAngle: -135, endAngle: 135,
        hollow: { size: '60%' },
        track: { background: isDark ? '#3d3d3d' : '#e8e8e8', strokeWidth: '97%' },
        dataLabels: {
          name: { show: false },
          value: {
            fontSize: '28px', fontWeight: 700, color: color,
            offsetY: 8,
            formatter: v => v + '%',
          },
        },
      }
    },
    fill: { colors: [color] },
    stroke: { lineCap: 'round' },
  }).render();
});
</script>
@endsection
