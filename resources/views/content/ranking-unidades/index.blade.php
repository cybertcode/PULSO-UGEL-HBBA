@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Ranking de Unidades — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-style')
<style>
/* Clasificación compacta */
.ranking-row { transition: background .1s; }
.ranking-row:hover { background: rgba(105,108,255,.04) !important; }
.ranking-row .sigla-text { font-size: .845rem; font-weight: 700; }
.ranking-row .nombre-text { font-size: .75rem; }
.ranking-row .pct-text { font-size: .78rem; font-weight: 700; min-width: 36px; }

/* Podio responsive */
@media (max-width: 767px) {
  .podio-wrap { flex-direction: column !important; align-items: center !important; }
  .podio-wrap > div { width: 100% !important; max-width: 280px; }
}

/* Cards laterales */
.stat-block { border-radius: 12px; padding: .85rem 1rem; }

/* Chart card header compacto */
.chart-card .card-header { padding: .85rem 1.25rem; }
.chart-card h5 { font-size: 1rem; }
</style>
@endsection

@section('content')

@php
  $unCumplieron = $unidades->where('color','success')->count();
  $unRiesgo     = $unidades->where('color','warning')->count();
  $unCriticas   = $unidades->where('color','danger')->count();
  $promedio     = round($unidades->avg('porcentaje'));
  $maxVal       = $unidades->max('porcentaje');
  $minVal       = $unidades->min('porcentaje');
  $u1 = $unidades->get(0);
  $u2 = $unidades->get(1);
  $u3 = $unidades->get(2);
  $colorHex = ['success'=>'#28c76f','warning'=>'#ff9f43','danger'=>'#ea5455'];
  $colorRgb = ['success'=>'40,199,111','warning'=>'255,159,67','danger'=>'234,84,85'];
@endphp

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sci-modelo-integridad') }}">Modelo de Integridad</a></li>
    <li class="breadcrumb-item active">Ranking de Unidades</li>
  </ol>
</nav>

{{-- ════ CABECERA ════ --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-6">
  <div>
    <h4 class="fw-bold mb-1">Ranking de Unidades</h4>
    <p class="text-muted mb-0">Competencia sana entre áreas para fortalecer el cumplimiento y la integridad institucional.</p>
  </div>
</div>

{{-- ════ PODIO TOP 3 ════ --}}
@if($u1)
<div class="card mb-6" style="overflow:visible">
  <div class="card-body pt-5 pb-4 px-4">

    <div class="text-center mb-5">
      <h5 class="fw-bold mb-1"><i class="ti tabler-podium me-2 text-warning"></i>Podio — Mejores del Período</h5>
      <p class="card-subtitle mb-0">Las tres unidades con mayor porcentaje de cumplimiento</p>
    </div>

    {{-- Podio: flex puro, align-items:flex-end garantiza escalonado real --}}
    <div class="podio-wrap" style="display:flex;justify-content:center;align-items:flex-end;gap:16px;padding:0 16px">

      {{-- ── 2° PLATA (altura media) ── --}}
      @if($u2)
      <div style="width:200px;flex-shrink:0">
        <div class="rounded-3 p-3 text-center"
             style="height:270px;display:flex;flex-direction:column;align-items:center;
                    background:linear-gradient(170deg,rgba(192,192,192,.18),rgba(192,192,192,.05));
                    border:1px solid rgba(192,192,192,.3)">
          <div style="width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                      font-size:16px;font-weight:900;flex-shrink:0;margin-bottom:8px;
                      background:linear-gradient(135deg,#b8b8b8,#e0e0e0);color:#fff;
                      box-shadow:0 3px 10px rgba(0,0,0,.2)">2</div>
          <div style="width:58px;height:58px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                      font-size:19px;font-weight:900;flex-shrink:0;margin-bottom:8px;
                      background:linear-gradient(145deg,#c0c0c0,#989898);color:#fff;
                      box-shadow:0 6px 18px rgba(0,0,0,.18)">{{ strtoupper(substr($u2->sigla,0,2)) }}</div>
          <p class="fw-bold mb-0" style="font-size:14px">{{ $u2->sigla }}</p>
          <p class="text-muted mb-0" style="font-size:10px;line-height:1.3">{{ Str::limit($u2->nombre,20) }}</p>
          <div style="margin-top:auto;padding-top:8px">
            <div class="fw-bold mb-1" style="font-size:1.6rem;color:#909090;line-height:1">{{ $u2->porcentaje }}%</div>
            <small class="text-muted d-block mb-2">{{ $u2->completadas_count }}/{{ $u2->actividades_count }}</small>
            @if($u2->variacion > 0)<span class="badge bg-label-success rounded-pill" style="font-size:9px"><i class="ti tabler-arrow-up"></i>+{{ $u2->variacion }}</span>
            @elseif($u2->variacion < 0)<span class="badge bg-label-danger rounded-pill" style="font-size:9px"><i class="ti tabler-arrow-down"></i>{{ $u2->variacion }}</span>
            @else<span class="badge bg-label-secondary rounded-pill" style="font-size:9px">—</span>@endif
          </div>
        </div>
      </div>
      @endif

      {{-- ── 1° ORO (el más alto) ── --}}
      <div style="width:220px;flex-shrink:0">
        <div class="rounded-3 p-3 text-center"
             style="height:360px;display:flex;flex-direction:column;align-items:center;
                    background:linear-gradient(170deg,rgba(255,193,7,.22),rgba(255,193,7,.06));
                    border:2px solid rgba(255,193,7,.45)">
          <div style="font-size:26px;line-height:1;margin-bottom:6px;flex-shrink:0;
                      filter:drop-shadow(0 3px 8px rgba(255,193,7,.7))">👑</div>
          <div style="width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                      font-size:19px;font-weight:900;flex-shrink:0;margin-bottom:8px;
                      background:linear-gradient(135deg,#ffd700,#ff9800);color:#fff;
                      box-shadow:0 4px 16px rgba(255,193,7,.5)">1</div>
          <div style="width:76px;height:76px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                      font-size:26px;font-weight:900;flex-shrink:0;margin-bottom:8px;
                      background:linear-gradient(145deg,#ffd700,#ff9800);color:#fff;
                      box-shadow:0 8px 24px rgba(255,193,7,.5)">{{ strtoupper(substr($u1->sigla,0,2)) }}</div>
          <p class="fw-bold mb-0" style="font-size:16px">{{ $u1->sigla }}</p>
          <p class="text-muted mb-1" style="font-size:10px;line-height:1.3">{{ Str::limit($u1->nombre,24) }}</p>
          <span class="badge bg-warning rounded-pill px-2 mb-0" style="font-size:10px;color:#fff">
            {{ $u1->porcentaje >= 85 ? '⭐ Excelente' : ($u1->porcentaje >= 75 ? '✓ Bueno' : '↑ Regular') }}
          </span>
          <div style="margin-top:auto;padding-top:8px">
            <div class="fw-bold mb-1" style="font-size:2.2rem;color:#ff9800;line-height:1">{{ $u1->porcentaje }}%</div>
            <small class="text-muted d-block mb-2">{{ $u1->completadas_count }}/{{ $u1->actividades_count }}</small>
            @if($u1->variacion > 0)<span class="badge bg-label-success rounded-pill" style="font-size:9px">+{{ $u1->variacion }}</span>@endif
          </div>
        </div>
      </div>

      {{-- ── 3° BRONCE (el más bajo) ── --}}
      @if($u3)
      <div style="width:200px;flex-shrink:0">
        <div class="rounded-3 p-3 text-center"
             style="height:210px;display:flex;flex-direction:column;align-items:center;
                    background:linear-gradient(170deg,rgba(205,127,50,.18),rgba(205,127,50,.05));
                    border:1px solid rgba(205,127,50,.3)">
          <div style="width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                      font-size:14px;font-weight:900;flex-shrink:0;margin-bottom:8px;
                      background:linear-gradient(135deg,#cd7f32,#e09050);color:#fff;
                      box-shadow:0 3px 10px rgba(205,127,50,.3)">3</div>
          <div style="width:54px;height:54px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                      font-size:18px;font-weight:900;flex-shrink:0;margin-bottom:8px;
                      background:linear-gradient(145deg,#d4833a,#b8692a);color:#fff;
                      box-shadow:0 5px 16px rgba(205,127,50,.3)">{{ strtoupper(substr($u3->sigla,0,2)) }}</div>
          <p class="fw-bold mb-0" style="font-size:14px">{{ $u3->sigla }}</p>
          <p class="text-muted mb-0" style="font-size:10px;line-height:1.3">{{ Str::limit($u3->nombre,20) }}</p>
          <div style="margin-top:auto;padding-top:8px">
            <div class="fw-bold mb-1" style="font-size:1.6rem;color:#cd7f32;line-height:1">{{ $u3->porcentaje }}%</div>
            <small class="text-muted d-block mb-2">{{ $u3->completadas_count }}/{{ $u3->actividades_count }}</small>
            @if($u3->variacion > 0)<span class="badge bg-label-success rounded-pill" style="font-size:9px"><i class="ti tabler-arrow-up"></i>+{{ $u3->variacion }}</span>
            @elseif($u3->variacion < 0)<span class="badge bg-label-danger rounded-pill" style="font-size:9px"><i class="ti tabler-arrow-down"></i>{{ $u3->variacion }}</span>
            @else<span class="badge bg-label-secondary rounded-pill" style="font-size:9px">—</span>@endif
          </div>
        </div>
      </div>
      @endif

    </div>

    {{-- Base inferior --}}
    <div class="mt-4 rounded-pill mx-auto" style="height:5px;max-width:700px;background:linear-gradient(90deg,rgba(192,192,192,.3) 0%,rgba(255,193,7,.5) 50%,rgba(205,127,50,.3) 100%)"></div>

  </div>
</div>
@endif

{{-- ════ GRÁFICO DE BARRAS ════ --}}
<div class="card mb-6 chart-card">
  <div class="card-header border-bottom d-flex align-items-center justify-content-between py-4">
    <div>
      <h5 class="fw-bold mb-1">Avance por Unidad Orgánica</h5>
      <p class="card-subtitle mb-0">Porcentaje de cumplimiento acumulado · {{ now()->year }}</p>
    </div>
    <div class="d-flex align-items-center gap-3">
      <div class="d-flex align-items-center gap-1" style="font-size:11px;color:var(--bs-secondary-color)">
        <span style="width:20px;height:2px;background:#28c76f;display:inline-block;border-radius:2px;border-top:2px dashed #28c76f"></span>
        Umbral 75%
      </div>
      <span class="badge bg-label-primary rounded-pill px-3">{{ now()->year }}</span>
    </div>
  </div>
  <div class="card-body">
    <div id="chartRanking"></div>
  </div>
</div>

{{-- ════ TABLA CLASIFICACIÓN + PANEL LATERAL ════ --}}
<div class="row g-6">

  {{-- Clasificación completa --}}
  <div class="col-xl-7">
    <div class="card h-100">
      <div class="card-header border-bottom py-4">
        <h5 class="fw-bold mb-1">Clasificación Completa</h5>
        <p class="card-subtitle mb-0">Todas las unidades ordenadas por cumplimiento</p>
      </div>
      <div class="card-body p-0">

        {{-- TOP 3 con fondo especial --}}
        @foreach($unidades->take(3) as $u)
        @php
          $bgMap  = [1=>'rgba(255,193,7,.07)',2=>'rgba(192,192,192,.07)',3=>'rgba(205,127,50,.07)'];
          $bdMap  = [1=>'rgba(255,193,7,.3)', 2=>'rgba(192,192,192,.25)',3=>'rgba(205,127,50,.25)'];
          $numBg  = [1=>'linear-gradient(135deg,#ffd700,#ff9800)',2=>'linear-gradient(135deg,#c0c0c0,#e0e0e0)',3=>'linear-gradient(135deg,#cd7f32,#e09050)'];
          $numClr = [1=>'#fff',2=>'#666',3=>'#fff'];
          $hex    = $colorHex[$u->color];
          $rgb    = $colorRgb[$u->color];
          $icon   = match($u->color){'success'=>'tabler-circle-check','warning'=>'tabler-clock',default=>'tabler-alert-triangle'};
        @endphp
        <div class="d-flex align-items-center gap-3 px-4 py-2 border-bottom ranking-row"
             style="background:{{ $bgMap[$u->posicion_actual] }};border-left:3px solid {{ $bdMap[$u->posicion_actual] }}!important">
          <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
               style="width:30px;height:30px;font-size:12px;
                      background:{{ $numBg[$u->posicion_actual] }};color:{{ $numClr[$u->posicion_actual] }};
                      box-shadow:0 2px 8px rgba(0,0,0,.15)">
            {{ $u->posicion_actual }}
          </div>
          <div class="avatar avatar-sm flex-shrink-0">
            <span class="avatar-initial rounded-circle bg-label-{{ $u->color }}" style="font-size:11px;font-weight:800">
              {{ strtoupper(substr($u->sigla,0,2)) }}
            </span>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="fw-bold" style="font-size:13.5px">{{ $u->sigla }}</span>
              <small class="text-muted text-truncate">{{ $u->nombre }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1 rounded-pill" style="height:6px;background:rgba({{ $rgb }},.15)">
                <div class="progress-bar rounded-pill" style="width:{{ $u->porcentaje }}%;background:{{ $hex }}"></div>
              </div>
              <span class="fw-bold" style="min-width:38px;font-size:12px;color:{{ $hex }}">{{ $u->porcentaje }}%</span>
            </div>
          </div>
          <div class="flex-shrink-0 text-end" style="min-width:90px">
            <span class="badge bg-label-{{ $u->color }} rounded-pill d-block mb-1" style="font-size:10px">{{ $u->semaforo }}</span>
            @if($u->variacion > 0)<small class="text-success fw-bold"><i class="ti tabler-arrow-up" style="font-size:10px"></i>+{{ $u->variacion }}</small>
            @elseif($u->variacion < 0)<small class="text-danger fw-bold"><i class="ti tabler-arrow-down" style="font-size:10px"></i>{{ $u->variacion }}</small>
            @else<small class="text-muted"><i class="ti tabler-minus" style="font-size:10px"></i></small>@endif
          </div>
        </div>
        @endforeach

        {{-- Resto --}}
        @if($unidades->count() > 3)
        <div class="px-4 py-2" style="background:var(--bs-tertiary-bg)">
          <small class="text-muted fw-semibold" style="font-size:10px;letter-spacing:.07em">RESTO DE UNIDADES</small>
        </div>
        @foreach($unidades->slice(3) as $u)
        @php
          $hex = $colorHex[$u->color];
          $rgb = $colorRgb[$u->color];
        @endphp
        <div class="d-flex align-items-center gap-3 px-4 py-2 border-bottom ranking-row"
             style="border-left:3px solid {{ $hex }}!important">
          <div class="text-center flex-shrink-0" style="width:30px">
            <span class="fw-bold text-muted" style="font-size:12px">{{ $u->posicion_actual }}</span>
          </div>
          <div class="avatar avatar-sm flex-shrink-0">
            <span class="avatar-initial rounded-circle bg-label-{{ $u->color }}" style="font-size:11px;font-weight:800">
              {{ strtoupper(substr($u->sigla,0,2)) }}
            </span>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="fw-semibold" style="font-size:13px">{{ $u->sigla }}</span>
              <small class="text-muted text-truncate">{{ $u->nombre }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1 rounded-pill" style="height:5px;background:rgba({{ $rgb }},.15)">
                <div class="progress-bar rounded-pill" style="width:{{ $u->porcentaje }}%;background:{{ $hex }}"></div>
              </div>
              <span class="fw-bold" style="min-width:38px;font-size:12px;color:{{ $hex }}">{{ $u->porcentaje }}%</span>
            </div>
          </div>
          <div class="flex-shrink-0 text-end" style="min-width:90px">
            <span class="badge bg-label-{{ $u->color }} rounded-pill d-block mb-1" style="font-size:10px">{{ $u->semaforo }}</span>
            @if($u->variacion > 0)<small class="text-success fw-bold"><i class="ti tabler-arrow-up" style="font-size:10px"></i>+{{ $u->variacion }}</small>
            @elseif($u->variacion < 0)<small class="text-danger fw-bold"><i class="ti tabler-arrow-down" style="font-size:10px"></i>{{ $u->variacion }}</small>
            @else<small class="text-muted"><i class="ti tabler-minus" style="font-size:10px"></i></small>@endif
          </div>
        </div>
        @endforeach
        @endif

      </div>
    </div>
  </div>

  {{-- Panel lateral --}}
  <div class="col-xl-5 d-flex flex-column gap-4">

    {{-- Resumen --}}
    <div class="card">
      <div class="card-header border-bottom py-3">
        <h6 class="fw-bold mb-0">Resumen</h6>
      </div>
      <div class="card-body pb-2">
        <div class="d-flex align-items-center gap-3 mb-3 stat-block" style="background:rgba(40,199,111,.08);border:1px solid rgba(40,199,111,.2)">
          <div class="badge rounded bg-label-success p-2 flex-shrink-0">
            <i class="ti tabler-circle-check icon-20px text-success"></i>
          </div>
          <div>
            <div class="fw-semibold" style="font-size:13px">{{ $unCumplieron }} Unidades más cumplieron</div>
            <div class="text-muted" style="font-size:11px">Con avance ≥ 75% en el período</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-3 mb-3 stat-block" style="background:rgba(255,159,67,.08);border:1px solid rgba(255,159,67,.2)">
          <div class="badge rounded bg-label-warning p-2 flex-shrink-0">
            <i class="ti tabler-alert-circle icon-20px text-warning"></i>
          </div>
          <div>
            <div class="fw-semibold" style="font-size:13px">{{ $unRiesgo }} Unidades en riesgo</div>
            <div class="text-muted" style="font-size:11px">Requieren atención urgente</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-3 mb-4 stat-block" style="background:rgba(234,84,85,.08);border:1px solid rgba(234,84,85,.2)">
          <div class="badge rounded bg-label-danger p-2 flex-shrink-0">
            <i class="ti tabler-flame icon-20px text-danger"></i>
          </div>
          <div>
            <div class="fw-semibold" style="font-size:13px">{{ $unCriticas }} Unidades críticas</div>
            <div class="text-muted" style="font-size:11px">Avance menor al 50%</div>
          </div>
        </div>
        <a href="{{ route('rep-reconocimientos') }}" class="btn btn-primary w-100">
          <i class="ti tabler-trophy me-2"></i>Nuevo reconocimiento
        </a>
      </div>
    </div>

    {{-- Estadísticas del período --}}
    <div class="card">
      <div class="card-header border-bottom py-3">
        <h6 class="fw-bold mb-0">Estadísticas del Período</h6>
      </div>
      <div class="card-body">
        <div class="row g-4 text-center">
          <div class="col-4">
            <h3 class="fw-bold text-primary mb-0">{{ $promedio }}%</h3>
            <small class="text-muted">Promedio</small>
          </div>
          <div class="col-4">
            <h3 class="fw-bold text-success mb-0">{{ $maxVal }}%</h3>
            <small class="text-muted">Máximo</small>
          </div>
          <div class="col-4">
            <h3 class="fw-bold text-danger mb-0">{{ $minVal }}%</h3>
            <small class="text-muted">Mínimo</small>
          </div>
        </div>
        <div class="d-flex rounded-pill overflow-hidden mt-4" style="height:8px;gap:2px">
          @if($unCumplieron)<div style="flex:{{ $unCumplieron }};background:#28c76f;border-radius:99px 0 0 99px"></div>@endif
          @if($unRiesgo)<div style="flex:{{ $unRiesgo }};background:#ff9f43"></div>@endif
          @if($unCriticas)<div style="flex:{{ $unCriticas }};background:#ea5455;border-radius:0 99px 99px 0"></div>@endif
        </div>
        <div class="d-flex justify-content-between mt-2">
          <small class="text-success fw-semibold">{{ $unCumplieron }} cumplen</small>
          <small class="text-warning fw-semibold">{{ $unRiesgo }} en proceso</small>
          <small class="text-danger fw-semibold">{{ $unCriticas }} críticas</small>
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
  const textColor = isDark ? '#b4bdc6' : '#697a8d';
  const gridColor = isDark ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.04)';

  new ApexCharts(document.getElementById('chartRanking'), {
    chart: {
      type: 'bar', height: 300, toolbar: { show: false },
      animations: { enabled: true, easing: 'easeinout', speed: 700 },
    },
    series: [{ name: '% Cumplimiento', data: {!! $chart_data !!} }],
    xaxis: {
      categories: {!! $chart_labels !!},
      labels: { style: { colors: textColor, fontSize: '11px' } },
      axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: {
      max: 100, min: 0,
      labels: { formatter: v => v + '%', style: { colors: textColor, fontSize: '11px' } },
    },
    colors: {!! $chart_colors !!},
    dataLabels: {
      enabled: true, formatter: v => v + '%', offsetY: -20,
      style: { fontSize: '10px', fontWeight: 700, colors: [textColor] },
    },
    plotOptions: {
      bar: { borderRadius: 8, distributed: true, columnWidth: '48%', dataLabels: { position: 'top' } },
    },
    annotations: {
      yaxis: [
        { y: 75, borderColor: '#28c76f', borderWidth: 2, strokeDashArray: 5,
          label: { text: 'Verde ≥75%', position: 'right',
            style: { color: '#28c76f', background: isDark ? '#2b2c40' : '#fff', fontSize: '10px', fontWeight: 700 } } },
        { y: 50, borderColor: '#ff9f43', borderWidth: 1, strokeDashArray: 5,
          label: { text: '50%', position: 'right',
            style: { color: '#ff9f43', background: isDark ? '#2b2c40' : '#fff', fontSize: '10px', fontWeight: 700 } } },
      ],
    },
    legend: { show: false },
    grid: { borderColor: gridColor, strokeDashArray: 5 },
    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: v => v + '% cumplimiento' } },
  }).render();
});
</script>
@endsection
