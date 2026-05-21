@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Avance por Unidades - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss',
       'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js',
       'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Avance por Unidades</li>
  </ol>
</nav>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-3">
  <div>
    <h4 class="mb-1">Avance por Unidades Orgánicas</h4>
    <p class="mb-0 text-muted">Consulta el progreso de las actividades del Sistema de Control Interno y del Modelo de Integridad por cada unidad.</p>
  </div>
  <div class="d-flex align-items-center gap-3">
    @if($ultima_actualizacion)
    <div class="d-flex align-items-center gap-2 text-muted">
      <i class="ti tabler-clock icon-16px text-primary"></i>
      <small>Última actualización: {{ \Carbon\Carbon::parse($ultima_actualizacion)->translatedFormat('d \d\e F \d\e Y, g:i a') }}</small>
    </div>
    @endif
    <a href="{{ route('rep-reportes') }}" class="btn btn-sm btn-label-primary">
      <i class="ti tabler-download me-1"></i>Exportar
    </a>
  </div>
</div>

{{-- Filtros --}}
<div class="row g-3 mb-5 align-items-end">
  <div class="col-auto">
    <label class="form-label form-label-sm">Período</label>
    <select class="form-select form-select-sm" style="width:200px">
      <option>I Trimestre {{ now()->year }} (Ene - Mar)</option>
      <option>II Trimestre {{ now()->year }} (Abr - Jun)</option>
      <option>III Trimestre {{ now()->year }} (Jul - Sep)</option>
      <option>IV Trimestre {{ now()->year }} (Oct - Dic)</option>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label form-label-sm">Dimensión</label>
    <select class="form-select form-select-sm" style="width:180px">
      <option>Todas las dimensiones</option>
      <option>Control Interno</option>
      <option>Modelo de Integridad</option>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label form-label-sm">Estado</label>
    <select class="form-select form-select-sm" style="width:160px">
      <option>Todos los estados</option>
      <option>En avance</option>
      <option>En proceso</option>
      <option>En riesgo</option>
    </select>
  </div>
</div>

{{-- ── Tarjetas grandes de unidades (top 3 + promedio general) ── --}}
<div class="row g-4 mb-4">

  @php $top3u = $unidades->take(3); @endphp

  @foreach($top3u as $idx => $u)
  @php
    $bordColor = ['primary','success','info'][$idx] ?? 'secondary';
    $headerBg  = ['rgba(105,108,255,.08)','rgba(40,199,111,.08)','rgba(0,207,232,.08)'][$idx] ?? 'rgba(0,0,0,.03)';
  @endphp
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 border-{{ $bordColor }} border-opacity-25">
      <div class="card-body" style="background:{{ $headerBg }};border-radius:6px 6px 0 0">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-{{ $bordColor }}">
              <i class="ti tabler-building icon-22px"></i>
            </span>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-bold text-truncate">{{ $u->sigla }}</div>
            <small class="text-muted text-truncate d-block" style="max-width:160px">{{ $u->nombre }}</small>
          </div>
          <span class="badge bg-label-{{ $u->color }}">{{ $u->semaforo }}</span>
        </div>
        <div class="d-flex align-items-end gap-2 mb-2">
          <h2 class="mb-0 text-{{ $bordColor }} fw-bold">{{ $u->porcentaje }}%</h2>
          <span class="badge bg-label-success mb-1"><i class="ti tabler-trending-up icon-12px me-1"></i>{{ $u->semaforo }}</span>
        </div>
        <div class="progress mb-2" style="height:8px">
          <div class="progress-bar bg-{{ $u->color }} rounded-pill" style="width:{{ $u->porcentaje }}%"></div>
        </div>
        <div class="d-flex justify-content-between">
          <small class="text-muted">{{ $u->completadas_count }} de {{ $u->actividades_count }} actividades</small>
        </div>
      </div>
      <div class="card-footer py-2 px-3 d-flex gap-2">
        <div class="d-flex align-items-center gap-2 flex-fill">
          <span class="badge bg-label-warning">
            <i class="ti tabler-loader icon-12px me-1"></i>En Proceso: {{ $u->en_proceso_count }}
          </span>
          <span class="badge bg-label-danger">
            <i class="ti tabler-clock icon-12px me-1"></i>Pendientes: {{ $u->pendientes_count }}
          </span>
        </div>
      </div>
    </div>
  </div>
  @endforeach

  {{-- Promedio General --}}
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 border-warning border-opacity-25">
      <div class="card-body" style="background:rgba(255,159,67,.06);border-radius:6px 6px 0 0">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="ti tabler-chart-pie icon-22px"></i>
            </span>
          </div>
          <div>
            <div class="fw-bold">Promedio General</div>
            <small class="text-muted">UGEL</small>
          </div>
          <span class="badge bg-label-success ms-auto">En avance</span>
        </div>
        <div class="d-flex align-items-end gap-2 mb-2">
          <h2 class="mb-0 text-warning fw-bold">{{ $avance_global }}%</h2>
          <span class="badge bg-label-success mb-1"><i class="ti tabler-trending-up icon-12px me-1"></i>En avance</span>
        </div>
        <div class="progress mb-2" style="height:8px">
          <div class="progress-bar bg-warning rounded-pill" style="width:{{ $avance_global }}%"></div>
        </div>
        <small class="text-muted">{{ $total_completadas }} de {{ $total_actividades }} actividades</small>
      </div>
      <div class="card-footer py-2 px-3">
        <a href="{{ route('rep-reportes') }}" class="text-primary small fw-medium">Ver resumen general <i class="ti tabler-arrow-right icon-12px"></i></a>
      </div>
    </div>
  </div>

</div>

{{-- ── Tabla con tabs ── --}}
<div class="card mb-5">
  <div class="card-header pb-0">
    <ul class="nav nav-tabs card-header-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="tab-avance" data-bs-toggle="tab" href="#pane-avance" role="tab">
          <i class="ti tabler-chart-bar icon-14px me-1"></i>Avance por Unidad
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="tab-remediacion" data-bs-toggle="tab" href="#pane-remediacion" role="tab">
          <i class="ti tabler-tool icon-14px me-1"></i>Medidas de Remediación
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="tab-control" data-bs-toggle="tab" href="#pane-control" role="tab">
          <i class="ti tabler-shield-check icon-14px me-1"></i>Medidas de Control
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="tab-historial" data-bs-toggle="tab" href="#pane-historial" role="tab">
          <i class="ti tabler-history icon-14px me-1"></i>Historial de Acciones
        </a>
      </li>
    </ul>
  </div>

  <div class="tab-content">

    {{-- Tab Avance por Unidad --}}
    <div class="tab-pane fade show active" id="pane-avance" role="tabpanel">
      <div class="row g-0">
        <div class="col-xl-8">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Unidad Orgánica</th>
                  <th style="min-width:130px">Avance</th>
                  <th class="text-center">Completadas</th>
                  <th class="text-center">En Proceso</th>
                  <th class="text-center">Pendientes</th>
                  <th class="text-center">Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($unidades as $u)
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <div class="avatar avatar-sm flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-{{ $u->color }}" style="font-size:11px;font-weight:700">
                          {{ strtoupper(substr($u->sigla, 0, 2)) }}
                        </span>
                      </div>
                      <div>
                        <div class="fw-semibold">{{ $u->sigla }}</div>
                        <small class="text-muted text-truncate d-block" style="max-width:200px">{{ $u->nombre }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-grow-1" style="height:8px">
                        <div class="progress-bar bg-{{ $u->color }} rounded-pill" style="width:{{ $u->porcentaje }}%"></div>
                      </div>
                      <span class="fw-bold text-{{ $u->color }}" style="min-width:38px">{{ $u->porcentaje }}%</span>
                    </div>
                  </td>
                  <td class="text-center fw-bold text-success">{{ $u->completadas_count }}</td>
                  <td class="text-center fw-bold text-warning">{{ $u->en_proceso_count }}</td>
                  <td class="text-center fw-bold text-danger">{{ $u->pendientes_count }}</td>
                  <td class="text-center">
                    <span class="badge bg-label-{{ $u->color }}">{{ $u->semaforo }}</span>
                  </td>
                  <td>
                    <a href="{{ route('sci-control-interno') }}?unidad_organica_id={{ $u->id }}" class="btn btn-xs btn-label-primary">
                      Ver detalle
                    </a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-5">Sin unidades registradas</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-4 py-2 border-top">
            <small class="text-muted">Mostrando 1 a {{ $unidades->count() }} de {{ $unidades->count() }} unidades</small>
          </div>
        </div>

        {{-- Donut lateral --}}
        <div class="col-xl-4 border-start">
          <div class="p-4">
            <h6 class="mb-3">Distribución por Estado</h6>
            <div id="chartDistribucion"></div>
            <div class="mt-3">
              @php
                $distribItems = [
                  ['label'=>'Completadas','color'=>'#28c76f','val'=>$total_completadas],
                  ['label'=>'En proceso', 'color'=>'#ff9f43','val'=>$total_en_proceso],
                  ['label'=>'Pendientes', 'color'=>'#ea5455','val'=>$total_pendientes],
                ];
              @endphp
              @foreach($distribItems as $d)
              <div class="d-flex justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                  <span style="width:10px;height:10px;border-radius:50%;background:{{ $d['color'] }};display:inline-block"></span>
                  <small>{{ $d['label'] }}</small>
                </div>
                <small class="fw-bold">{{ $d['val'] }}</small>
              </div>
              @endforeach
            </div>
          </div>
          <div class="px-4 pt-2 border-top">
            <h6 class="mb-3 mt-3">Acciones Rápidas</h6>
            <div class="d-grid gap-2">
              <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-outline-success text-start">
                <i class="ti tabler-plus icon-14px me-2"></i>Nueva Medida de Remediación
              </a>
              <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-outline-primary text-start">
                <i class="ti tabler-plus icon-14px me-2"></i>Nueva Medida de Control
              </a>
              <a href="{{ route('rep-reportes') }}" class="btn btn-sm btn-outline-secondary text-start">
                <i class="ti tabler-file-text icon-14px me-2"></i>Ver Reporte Detallado
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Tab Medidas de Remediación --}}
    <div class="tab-pane fade" id="pane-remediacion" role="tabpanel">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Medida</th>
              <th>Unidad</th>
              <th>Actividad Relacionada</th>
              <th class="text-center">Estado</th>
              <th>Fecha Límite</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($medidas_remediacion as $m)
            @php $mc = match($m->estado) {'completada'=>'success','en_proceso'=>'warning','vencida'=>'danger',default=>'secondary'}; @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-label-danger rounded-pill" style="width:10px;height:10px;padding:0"></span>
                  <div class="fw-medium text-truncate" style="max-width:220px">{{ $m->nombre }}</div>
                </div>
              </td>
              <td><small>{{ $m->unidadOrganica->sigla ?? '—' }}</small></td>
              <td><small class="text-muted text-truncate d-block" style="max-width:160px">{{ $m->componente->nombre ?? '—' }}</small></td>
              <td class="text-center"><span class="badge bg-label-{{ $mc }}">{{ ucfirst(str_replace('_',' ',$m->estado)) }}</span></td>
              <td><small>{{ $m->fecha_limite->format('d/m/Y') }}</small></td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('sci-control-interno') }}" class="btn btn-xs btn-label-primary">
                    <i class="ti tabler-eye icon-12px"></i>
                  </a>
                  <button class="btn btn-xs btn-label-secondary">
                    <i class="ti tabler-dots-vertical icon-12px"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-5">Sin medidas de remediación activas</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="px-4 py-2 border-top">
        <a href="{{ route('sci-control-interno') }}" class="text-primary small fw-medium">Ver todas las medidas de remediación <i class="ti tabler-arrow-right icon-12px"></i></a>
      </div>
    </div>

    {{-- Tab Medidas de Control --}}
    <div class="tab-pane fade" id="pane-control" role="tabpanel">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Medida</th>
              <th>Unidad</th>
              <th>Actividad Relacionada</th>
              <th class="text-center">Estado</th>
              <th>Fecha Límite</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($medidas_control as $m)
            <tr>
              <td>
                <div class="fw-medium text-truncate" style="max-width:220px">{{ $m->nombre }}</div>
              </td>
              <td><small>{{ $m->unidadOrganica->sigla ?? '—' }}</small></td>
              <td><small class="text-muted">{{ $m->componente->nombre ?? '—' }}</small></td>
              <td class="text-center"><span class="badge bg-label-success">Activa</span></td>
              <td><small>{{ $m->fecha_limite->format('d/m/Y') }}</small></td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('sci-control-interno') }}" class="btn btn-xs btn-label-primary">
                    <i class="ti tabler-eye icon-12px"></i>
                  </a>
                  <button class="btn btn-xs btn-label-secondary">
                    <i class="ti tabler-dots-vertical icon-12px"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-5">Sin medidas de control registradas</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="px-4 py-2 border-top">
        <a href="{{ route('sci-control-interno') }}" class="text-primary small fw-medium">Ver todas las medidas de control <i class="ti tabler-arrow-right icon-12px"></i></a>
      </div>
    </div>

    {{-- Tab Historial --}}
    <div class="tab-pane fade" id="pane-historial" role="tabpanel">
      <div class="p-4 text-center text-muted py-5">
        <i class="ti tabler-history icon-48px d-block mb-3"></i>
        <h6>Historial de Acciones</h6>
        <p class="mb-0">El historial de acciones por unidad orgánica está disponible en el módulo de reportes.</p>
        <a href="{{ route('rep-reportes') }}" class="btn btn-sm btn-label-primary mt-3">
          <i class="ti tabler-chart-bar me-1"></i>Ver Reportes
        </a>
      </div>
    </div>

  </div>
</div>

{{-- ── Medidas recientes (parte inferior en 2 columnas) ── --}}
<div class="row g-4">

  {{-- Medidas de Remediación Recientes --}}
  <div class="col-xl-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Medidas de Remediación Recientes</h5>
        </div>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-xs btn-label-secondary">
          Ver todas las medidas de remediación <i class="ti tabler-arrow-right icon-12px ms-1"></i>
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Medida</th>
                <th>Unidad</th>
                <th>Actividad Relacionada</th>
                <th class="text-center">Estado</th>
                <th>Fecha Límite</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($medidas_remediacion as $m)
              @php $mc2 = match($m->estado) {'completada'=>'success','en_proceso'=>'warning','vencida'=>'danger',default=>'secondary'}; @endphp
              <tr>
                <td>
                  <span class="badge bg-label-danger rounded-circle me-2" style="width:8px;height:8px;padding:0;display:inline-block"></span>
                  <span class="text-truncate" style="max-width:160px">{{ Str::limit($m->nombre, 30) }}</span>
                </td>
                <td><small>{{ $m->unidadOrganica->sigla ?? '—' }}</small></td>
                <td><small>{{ Str::limit($m->componente->nombre ?? '—', 20) }}</small></td>
                <td class="text-center"><span class="badge bg-label-{{ $mc2 }}">{{ ucfirst(str_replace('_',' ',$m->estado)) }}</span></td>
                <td><small>{{ $m->fecha_limite->format('d/m/Y') }}</small></td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="{{ route('sci-control-interno') }}" class="btn btn-xs btn-icon btn-label-primary"><i class="ti tabler-eye icon-12px"></i></a>
                    <button class="btn btn-xs btn-icon btn-label-secondary"><i class="ti tabler-pencil icon-12px"></i></button>
                    <button class="btn btn-xs btn-icon btn-label-secondary"><i class="ti tabler-dots icon-12px"></i></button>
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center text-muted py-4">Sin medidas de remediación</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Medidas de Control Recientes --}}
  <div class="col-xl-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Medidas de Control Recientes</h5>
        </div>
        <a href="{{ route('sci-control-interno') }}" class="btn btn-xs btn-label-secondary">
          Ver todas las medidas de control <i class="ti tabler-arrow-right icon-12px ms-1"></i>
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Medida</th>
                <th>Unidad</th>
                <th>Actividad Relacionada</th>
                <th class="text-center">Estado</th>
                <th>Fecha Límite</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($medidas_control as $m)
              <tr>
                <td>
                  <span class="text-truncate" style="max-width:160px">{{ Str::limit($m->nombre, 30) }}</span>
                </td>
                <td><small>{{ $m->unidadOrganica->sigla ?? '—' }}</small></td>
                <td><small>{{ Str::limit($m->componente->nombre ?? '—', 20) }}</small></td>
                <td class="text-center"><span class="badge bg-label-success">Activa</span></td>
                <td><small>{{ $m->fecha_limite->format('d/m/Y') }}</small></td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="{{ route('sci-control-interno') }}" class="btn btn-xs btn-icon btn-label-primary"><i class="ti tabler-eye icon-12px"></i></a>
                    <button class="btn btn-xs btn-icon btn-label-secondary"><i class="ti tabler-pencil icon-12px"></i></button>
                    <button class="btn btn-xs btn-icon btn-label-secondary"><i class="ti tabler-dots icon-12px"></i></button>
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center text-muted py-4">Sin medidas de control</td></tr>
              @endforelse
            </tbody>
          </table>
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

  new ApexCharts(document.getElementById('chartDistribucion'), {
    chart: { type: 'donut', height: 200 },
    series: [{{ $total_completadas }}, {{ $total_en_proceso }}, {{ $total_pendientes }}],
    labels: ['Completadas', 'En proceso', 'Pendientes'],
    colors: ['#28c76f', '#ff9f43', '#ea5455'],
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            total: {
              show: true, label: 'Total', color: textColor,
              formatter: () => '{{ $total_actividades }}',
            },
            value: { fontSize: '18px', fontWeight: 700, color: textColor, formatter: v => v },
          },
        }
      }
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    stroke: { width: 2, colors: [isDark ? '#2b2c40' : '#fff'] },
    tooltip: { y: { formatter: v => v + ' actividades' } },
  }).render();
});
</script>
@endsection
