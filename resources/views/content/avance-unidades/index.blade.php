@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Avance por Unidades — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

@php
  $nivelColor = $avance_global >= 75 ? 'success' : ($avance_global >= 50 ? 'warning' : 'danger');
  $nivelLabel = $avance_global >= 75 ? 'Cumplido' : ($avance_global >= 50 ? 'En proceso' : 'En riesgo');
  $colorHex   = ['success'=>'#28c76f','warning'=>'#ff9f43','danger'=>'#ea5455'];
  $colorRgb   = ['success'=>'40,199,111','warning'=>'255,159,67','danger'=>'234,84,85'];
@endphp

{{-- ════════════════════════════════════════════
     CABECERA
════════════════════════════════════════════ --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-6">
  <div>
    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <span class="badge rounded bg-label-primary p-2">
        <i class="icon-base ti tabler-building-community icon-lg text-primary"></i>
      </span>
      Avance por Unidades Orgánicas
    </h4>
    <p class="text-muted mb-0 ms-1">
      Progreso de actividades SCI y Modelo de Integridad · {{ $unidades->count() }} unidades
      @if($ultima_actualizacion)
      <span class="text-muted ms-2" style="font-size:11px">
        <i class="ti tabler-clock me-1"></i>{{ \Carbon\Carbon::parse($ultima_actualizacion)->translatedFormat('d M Y, g:i a') }}
      </span>
      @endif
    </p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('rep-reportes') }}" class="btn btn-sm btn-label-secondary">
      <i class="ti tabler-download me-1"></i>Exportar
    </a>
    <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-sm btn-label-warning">
      <i class="ti tabler-trophy me-1"></i>Ranking
    </a>
  </div>
</div>

{{-- ════════════════════════════════════════════
     FILA HERO — promedio general + 3 top unidades
════════════════════════════════════════════ --}}
<div class="row g-6 mb-6">

  {{-- Promedio general --}}
  <div class="col-xl-3 col-sm-6">
    <div class="card h-100 overflow-hidden">
      <div style="height:4px;background:{{ $colorHex[$nivelColor] }};box-shadow:0 0 16px 2px rgba({{ $colorRgb[$nivelColor] }},.4)"></div>
      <div class="card-body d-flex flex-column align-items-center text-center py-4">
        <div id="chartPromedioGeneral"></div>
        <h2 class="fw-bold mb-1 mt-n2" style="color:{{ $colorHex[$nivelColor] }}">{{ $avance_global }}%</h2>
        <p class="card-subtitle mb-2">Promedio General UGEL</p>
        <span class="badge bg-{{ $nivelColor }} rounded-pill px-3 mb-4">
          <i class="ti tabler-{{ $nivelColor==='success' ? 'circle-check' : ($nivelColor==='warning' ? 'clock' : 'alert-triangle') }} me-1"></i>
          {{ $nivelLabel }}
        </span>
        <div class="d-flex flex-column gap-2 w-100 text-start">
          <div class="d-flex align-items-center gap-2">
            <span style="width:8px;height:8px;border-radius:50%;background:#28c76f;flex-shrink:0"></span>
            <small class="text-muted">Completadas</small>
            <span class="fw-bold ms-auto text-success">{{ $total_completadas }}</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span style="width:8px;height:8px;border-radius:50%;background:#ff9f43;flex-shrink:0"></span>
            <small class="text-muted">En proceso</small>
            <span class="fw-bold ms-auto text-warning">{{ $total_en_proceso }}</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span style="width:8px;height:8px;border-radius:50%;background:#ea5455;flex-shrink:0"></span>
            <small class="text-muted">Pendientes</small>
            <span class="fw-bold ms-auto text-danger">{{ $total_pendientes }}</span>
          </div>
          <div class="border-top pt-2 mt-1 d-flex align-items-center gap-2">
            <small class="text-muted fw-semibold">Total</small>
            <span class="fw-bold ms-auto">{{ $total_actividades }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Top 3 unidades --}}
  <div class="col-xl-9 col-sm-6">
    <div class="row g-4 h-100">
      @php $top3 = $unidades->take(3); @endphp
      @foreach($top3 as $idx => $u)
      @php
        $hex = $colorHex[$u->color];
        $rgb = $colorRgb[$u->color];
      @endphp
      <div class="col-12 col-md-4">
        <div class="card h-100 overflow-hidden">
          <div style="height:3px;background:{{ $hex }};opacity:.9"></div>
          <div class="card-body">
            {{-- Header: avatar sigla + posición --}}
            <div class="d-flex align-items-start justify-content-between mb-3">
              <div class="avatar">
                <span class="avatar-initial rounded-circle bg-label-{{ $u->color }}"
                      style="font-size:12px;font-weight:800">
                  {{ strtoupper(substr($u->sigla,0,2)) }}
                </span>
              </div>
              <span class="badge bg-label-secondary rounded-pill" style="font-size:10px">
                #{{ $idx + 1 }}
              </span>
            </div>

            {{-- Nombre --}}
            <p class="fw-bold mb-0" style="font-size:13px">{{ $u->sigla }}</p>
            <p class="text-muted mb-3" style="font-size:11px;line-height:1.3;
               display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
              {{ $u->nombre }}
            </p>

            {{-- Porcentaje --}}
            <div class="d-flex align-items-end gap-1 mb-2">
              <h2 class="fw-bold mb-0" style="color:{{ $hex }};line-height:1">{{ $u->porcentaje }}</h2>
              <span class="text-muted fw-semibold mb-1">%</span>
            </div>

            {{-- Barra --}}
            <div class="progress rounded-pill mb-2" style="height:6px;background:rgba({{ $rgb }},.15)">
              <div class="progress-bar rounded-pill" style="width:{{ $u->porcentaje }}%;background:{{ $hex }}"></div>
            </div>

            <small class="text-muted">{{ $u->completadas_count }}/{{ $u->actividades_count }} completadas</small>
          </div>
          <div class="card-footer border-top-0 pt-0 pb-3 px-4">
            <span class="badge bg-label-{{ $u->color }} rounded-pill w-100 py-1_5" style="font-size:11px">
              {{ $u->semaforo }}
            </span>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

</div>

{{-- ════════════════════════════════════════════
     TABLA PRINCIPAL + PANEL LATERAL
════════════════════════════════════════════ --}}
<div class="card mb-6">
  <div class="card-header border-bottom d-flex align-items-center justify-content-between py-4">
    <div>
      <h5 class="fw-bold mb-1">Todas las Unidades Orgánicas</h5>
      <p class="card-subtitle mb-0">Avance detallado de actividades por área</p>
    </div>
    <div class="d-flex gap-2">
      <select class="form-select form-select-sm" style="width:160px">
        <option>Todos los estados</option>
        <option>Cumplido</option>
        <option>En proceso</option>
        <option>En riesgo</option>
      </select>
    </div>
  </div>

  <div class="row g-0">
    {{-- Tabla --}}
    <div class="col-xl-8 col-lg-7">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr style="background:var(--bs-tertiary-bg)">
              <th class="ps-4 fw-semibold" style="font-size:11px;letter-spacing:.05em;width:52px">#</th>
              <th class="fw-semibold" style="font-size:11px;letter-spacing:.05em">UNIDAD ORGÁNICA</th>
              <th class="fw-semibold" style="font-size:11px;letter-spacing:.05em;min-width:160px">AVANCE</th>
              <th class="text-center fw-semibold" style="font-size:11px;letter-spacing:.05em">ACTIV.</th>
              <th class="text-center pe-4 fw-semibold" style="font-size:11px;letter-spacing:.05em;width:120px">ESTADO</th>
            </tr>
          </thead>
          <tbody>
            @forelse($unidades as $i => $u)
            @php
              $hex = $colorHex[$u->color];
              $rgb = $colorRgb[$u->color];
              $icon = match($u->color) {'success'=>'tabler-circle-check','warning'=>'tabler-clock',default=>'tabler-alert-triangle'};
            @endphp
            <tr style="border-left:3px solid {{ $hex }};cursor:pointer"
                onclick="window.location='{{ route('sci-control-interno') }}?unidad_organica_id={{ $u->id }}'">
              <td class="ps-4 text-center">
                <span class="fw-bold text-muted" style="font-size:12px">{{ $i+1 }}</span>
              </td>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="avatar avatar-sm flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-{{ $u->color }}"
                          style="font-size:11px;font-weight:800">
                      {{ strtoupper(substr($u->sigla,0,2)) }}
                    </span>
                  </div>
                  <div>
                    <p class="fw-bold mb-0" style="font-size:13.5px">{{ $u->sigla }}</p>
                    <small class="text-muted">{{ Str::limit($u->nombre, 35) }}</small>
                  </div>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="progress flex-grow-1 rounded-pill" style="height:8px;background:rgba({{ $rgb }},.15)">
                    <div class="progress-bar rounded-pill" style="width:{{ $u->porcentaje }}%;background:{{ $hex }}"></div>
                  </div>
                  <span class="fw-bold" style="min-width:36px;font-size:13px;color:{{ $hex }}">{{ $u->porcentaje }}%</span>
                </div>
              </td>
              <td class="text-center">
                <span class="fw-bold text-success">{{ $u->completadas_count }}</span>
                <span class="text-muted">/{{ $u->actividades_count }}</span>
              </td>
              <td class="text-center pe-4">
                <span class="badge bg-label-{{ $u->color }} rounded-pill px-2" style="font-size:11px">
                  <i class="icon-base ti {{ $icon }} icon-12px me-1"></i>{{ $u->semaforo }}
                </span>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-6">Sin unidades registradas</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="px-4 py-3 border-top">
        <small class="text-muted">{{ $unidades->count() }} unidades orgánicas</small>
      </div>
    </div>

    {{-- Panel lateral --}}
    <div class="col-xl-4 col-lg-5 border-start">
      <div class="p-4">
        <h6 class="fw-bold mb-1">Distribución de Actividades</h6>
        <p class="card-subtitle mb-3">Por estado actual</p>
        <div id="chartDistribucion" class="mx-auto mb-3" style="max-width:200px"></div>

        {{-- Leyenda detallada --}}
        <div class="d-flex flex-column gap-3">
          @foreach([
            ['label'=>'Completadas','color'=>'success','hex'=>'#28c76f','val'=>$total_completadas],
            ['label'=>'En proceso', 'color'=>'warning','hex'=>'#ff9f43','val'=>$total_en_proceso],
            ['label'=>'Pendientes', 'color'=>'danger', 'hex'=>'#ea5455','val'=>$total_pendientes],
          ] as $d)
          <div class="d-flex align-items-center gap-3">
            <div class="badge rounded bg-label-{{ $d['color'] }} p-1_5">
              <i class="icon-base ti {{ $d['color']==='success' ? 'tabler-circle-check' : ($d['color']==='warning' ? 'tabler-clock' : 'tabler-alert-triangle') }} icon-sm text-{{ $d['color'] }}"></i>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between">
                <small class="fw-semibold">{{ $d['label'] }}</small>
                <small class="fw-bold text-{{ $d['color'] }}">{{ $d['val'] }}</small>
              </div>
              <div class="progress rounded-pill mt-1" style="height:4px">
                <div class="progress-bar bg-{{ $d['color'] }} rounded-pill"
                     style="width:{{ $total_actividades ? round($d['val']/$total_actividades*100) : 0 }}%"></div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Acciones rápidas --}}
      <div class="border-top p-4">
        <h6 class="fw-bold mb-3">Acciones Rápidas</h6>
        <div class="d-grid gap-2">
          <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-success">
            <i class="ti tabler-plus me-1"></i>Nueva Medida de Remediación
          </a>
          <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-primary">
            <i class="ti tabler-plus me-1"></i>Nueva Medida de Control
          </a>
          <a href="{{ route('rep-reportes') }}" class="btn btn-sm btn-label-secondary">
            <i class="ti tabler-file-text me-1"></i>Ver Reporte Detallado
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════
     MEDIDAS RECIENTES — 2 columnas
════════════════════════════════════════════ --}}
<div class="row g-6">

  {{-- Medidas de Remediación --}}
  <div class="col-xl-6">
    <div class="card h-100">
      <div class="card-header border-bottom d-flex align-items-center justify-content-between py-4">
        <div>
          <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span class="badge rounded bg-label-danger p-1_5">
              <i class="icon-base ti tabler-tool icon-sm text-danger"></i>
            </span>
            Medidas de Remediación
          </h5>
          <p class="card-subtitle mb-0">Recientes con seguimiento activo</p>
        </div>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-secondary">
          Ver todas <i class="ti tabler-arrow-right ms-1"></i>
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr style="background:var(--bs-tertiary-bg)">
              <th class="ps-4 fw-semibold" style="font-size:11px;letter-spacing:.05em">MEDIDA</th>
              <th class="fw-semibold text-center" style="font-size:11px;letter-spacing:.05em;width:60px">ÁREA</th>
              <th class="fw-semibold text-center pe-4" style="font-size:11px;letter-spacing:.05em;width:110px">ESTADO</th>
            </tr>
          </thead>
          <tbody>
            @forelse($medidas_remediacion as $m)
            @php $mc = match($m->estado) {'completada'=>'success','en_proceso'=>'warning','vencida'=>'danger',default=>'secondary'}; @endphp
            <tr>
              <td class="ps-4">
                <p class="fw-semibold mb-0" style="font-size:13px">{{ Str::limit($m->nombre, 38) }}</p>
                <small class="text-muted">{{ Str::limit($m->componente->nombre ?? '—', 28) }}</small>
              </td>
              <td class="text-center">
                <span class="badge bg-label-secondary rounded-pill" style="font-size:10px">
                  {{ $m->unidadOrganica->sigla ?? '—' }}
                </span>
              </td>
              <td class="text-center pe-4">
                <span class="badge bg-label-{{ $mc }} rounded-pill" style="font-size:11px">
                  {{ ucfirst(str_replace('_',' ',$m->estado)) }}
                </span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted py-5">
                <i class="ti tabler-tool d-block mb-2" style="font-size:2rem;opacity:.3"></i>
                Sin medidas de remediación activas
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Medidas de Control --}}
  <div class="col-xl-6">
    <div class="card h-100">
      <div class="card-header border-bottom d-flex align-items-center justify-content-between py-4">
        <div>
          <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span class="badge rounded bg-label-primary p-1_5">
              <i class="icon-base ti tabler-shield-check icon-sm text-primary"></i>
            </span>
            Medidas de Control
          </h5>
          <p class="card-subtitle mb-0">Controles activos registrados</p>
        </div>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-secondary">
          Ver todas <i class="ti tabler-arrow-right ms-1"></i>
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr style="background:var(--bs-tertiary-bg)">
              <th class="ps-4 fw-semibold" style="font-size:11px;letter-spacing:.05em">MEDIDA</th>
              <th class="fw-semibold text-center" style="font-size:11px;letter-spacing:.05em;width:60px">ÁREA</th>
              <th class="fw-semibold text-center pe-4" style="font-size:11px;letter-spacing:.05em;width:110px">ESTADO</th>
            </tr>
          </thead>
          <tbody>
            @forelse($medidas_control as $m)
            <tr>
              <td class="ps-4">
                <p class="fw-semibold mb-0" style="font-size:13px">{{ Str::limit($m->nombre, 38) }}</p>
                <small class="text-muted">{{ Str::limit($m->componente->nombre ?? '—', 28) }}</small>
              </td>
              <td class="text-center">
                <span class="badge bg-label-secondary rounded-pill" style="font-size:10px">
                  {{ $m->unidadOrganica->sigla ?? '—' }}
                </span>
              </td>
              <td class="text-center pe-4">
                <span class="badge bg-label-success rounded-pill" style="font-size:11px">Activa</span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted py-5">
                <i class="ti tabler-shield d-block mb-2" style="font-size:2rem;opacity:.3"></i>
                Sin medidas de control registradas
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
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
  const bgColor   = isDark ? '#2b2c40' : '#fff';

  // Donut distribución
  new ApexCharts(document.getElementById('chartDistribucion'), {
    chart:   { type: 'donut', height: 180 },
    series:  [{{ $total_completadas }}, {{ $total_en_proceso }}, {{ $total_pendientes }}],
    labels:  ['Completadas', 'En proceso', 'Pendientes'],
    colors:  ['#28c76f', '#ff9f43', '#ea5455'],
    plotOptions: {
      pie: {
        donut: {
          size: '72%',
          labels: {
            show: true,
            total: {
              show: true, label: 'Total', color: textColor,
              formatter: () => '{{ $total_actividades }}',
            },
            value: { fontSize: '20px', fontWeight: 700, color: textColor },
          }
        }
      }
    },
    legend:      { show: false },
    dataLabels:  { enabled: false },
    stroke:      { width: 2, colors: [bgColor] },
    tooltip:     { y: { formatter: v => v + ' actividades' } },
  }).render();

  // Gauge promedio general
  @php
    $gc = $avance_global >= 75 ? '#28c76f' : ($avance_global >= 50 ? '#ff9f43' : '#ea5455');
  @endphp
  new ApexCharts(document.getElementById('chartPromedioGeneral'), {
    chart:   { type: 'radialBar', height: 180, sparkline: { enabled: true } },
    series:  [{{ $avance_global }}],
    plotOptions: {
      radialBar: {
        startAngle: -135, endAngle: 135,
        hollow: { size: '62%' },
        track:  { background: isDark ? '#2d2d4a' : '#e8e8e8', strokeWidth: '97%' },
        dataLabels: {
          name:  { show: false },
          value: { show: false },
        },
      }
    },
    fill:   { colors: ['{{ $gc }}'] },
    stroke: { lineCap: 'round' },
  }).render();
});
</script>
@endsection
