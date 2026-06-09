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

@section('page-style')
<style>
/* ── Tabla compacta ── */
.tbl-avance td, .tbl-avance th { padding: .42rem .75rem !important; font-size: .845rem; vertical-align: middle; }
.tbl-avance thead th { font-size: .7rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #6e6b7b; background: #f8f7fa; white-space: nowrap; border-bottom: 1px solid rgba(0,0,0,.06) !important; }
.tbl-avance tbody tr { transition: background .1s; }
.tbl-avance tbody tr:hover { background: rgba(105,108,255,.04) !important; }

/* ── Filtro compacto ── */
.filter-strip { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); }
.filter-strip .form-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6e6b7b; margin-bottom: .2rem; }
.filter-strip .form-select { font-size: .8rem; height: 34px; padding: .28rem .75rem; border-radius: 8px; }

/* ── Card tabs ── */
.tab-card .nav-tabs { border-bottom: 1px solid rgba(0,0,0,.08); }
.tab-card .nav-link { font-size: .82rem; padding: .6rem 1rem; }
</style>
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
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Avance por Unidades Orgánicas</h4>
    <p class="text-muted mb-0">
      Consulta el progreso de las actividades del Sistema de Control Interno y del Modelo de Integridad por cada unidad.
      @if($ultima_actualizacion)
      <span class="ms-2" style="font-size:11px">
        <i class="ti tabler-clock me-1 text-muted"></i>Última actualización: {{ \Carbon\Carbon::parse($ultima_actualizacion)->translatedFormat('d M Y, g:i a') }}
      </span>
      @endif
    </p>
  </div>
  <a href="{{ route('rep-reportes') }}" class="btn btn-label-secondary btn-sm align-self-start">
    <i class="ti tabler-download me-1"></i>Exportar
  </a>
</div>

{{-- Filtros de período / dimensión / estado --}}
<div class="card filter-strip mb-5">
  <div class="card-body py-3">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-auto">
        <label class="form-label">Período</label>
        <select name="periodo" class="form-select" onchange="this.form.submit()">
          <option value="1T">I Trimestre {{ now()->year }} (Ene – Mar)</option>
          <option value="2T">II Trimestre {{ now()->year }} (Abr – Jun)</option>
          <option value="3T">III Trimestre {{ now()->year }} (Jul – Sep)</option>
          <option value="4T">IV Trimestre {{ now()->year }} (Oct – Dic)</option>
          <option value="anual" selected>Año completo {{ now()->year }}</option>
        </select>
      </div>
      <div class="col-auto">
        <label class="form-label">Dimensión</label>
        <select name="dimension" class="form-select" onchange="this.form.submit()">
          <option value="">Todas</option>
          <option value="sci">Control Interno</option>
          <option value="integridad">Modelo de Integridad</option>
        </select>
      </div>
      <div class="col-auto">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select" onchange="this.form.submit()">
          <option value="">Todos</option>
          <option value="cumplido">Cumplido</option>
          <option value="en_proceso">En proceso</option>
          <option value="en_riesgo">En riesgo</option>
        </select>
      </div>
    </form>
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
     TABS + TABLA PRINCIPAL + PANEL LATERAL
════════════════════════════════════════════ --}}
<div class="row g-6 mb-6">

  {{-- Tabla principal con tabs --}}
  <div class="col-xl-8">
    <div class="card h-100 tab-card">

      {{-- Tabs al estilo prototipo --}}
      <div class="card-header border-bottom p-0">
        <ul class="nav nav-tabs card-header-tabs px-4 pt-3" role="tablist">
          <li class="nav-item">
            <a class="nav-link active fw-semibold d-flex align-items-center gap-1" data-bs-toggle="tab" href="#tab-avance">
              <i class="ti tabler-building-community icon-14px"></i> Avance por Unidad
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold d-flex align-items-center gap-1" data-bs-toggle="tab" href="#tab-remediacion">
              <i class="ti tabler-tool icon-14px"></i> Medidas de Remediación
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold d-flex align-items-center gap-1" data-bs-toggle="tab" href="#tab-control">
              <i class="ti tabler-shield-check icon-14px"></i> Medidas de Control
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold d-flex align-items-center gap-1" data-bs-toggle="tab" href="#tab-historial">
              <i class="ti tabler-history icon-14px"></i> Historial de Acciones
            </a>
          </li>
        </ul>
      </div>

      <div class="tab-content">

        {{-- Tab: Avance por Unidad --}}
        <div class="tab-pane fade show active" id="tab-avance">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tbl-avance">
              <thead>
                <tr>
                  <th class="ps-4">Unidad Orgánica</th>
                  <th style="min-width:130px">Avance</th>
                  <th class="text-center">Compl.</th>
                  <th class="text-center">En proc.</th>
                  <th class="text-center">Pendient.</th>
                  <th class="text-center">Estado</th>
                  <th class="text-center pe-3">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($unidades as $i => $u)
                @php
                  $hex  = $colorHex[$u->color];
                  $rgb  = $colorRgb[$u->color];
                  $icon = match($u->color) {'success'=>'tabler-circle-check','warning'=>'tabler-clock',default=>'tabler-alert-triangle'};
                  $pen  = $u->actividades_count - $u->completadas_count - ($u->en_proceso_count ?? 0);
                @endphp
                <tr>
                  <td class="ps-4">
                    <div class="d-flex align-items-center gap-3">
                      <div class="avatar avatar-sm flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-{{ $u->color }}" style="font-size:11px;font-weight:800">
                          {{ strtoupper(substr($u->sigla,0,2)) }}
                        </span>
                      </div>
                      <div>
                        <p class="fw-bold mb-0" style="font-size:13px">{{ $u->sigla }}</p>
                        <small class="text-muted">{{ Str::limit($u->nombre, 30) }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-grow-1 rounded-pill" style="height:6px;background:rgba({{ $rgb }},.15)">
                        <div class="progress-bar rounded-pill" style="width:{{ $u->porcentaje }}%;background:{{ $hex }}"></div>
                      </div>
                      <span class="fw-bold" style="min-width:32px;font-size:12px;color:{{ $hex }}">{{ $u->porcentaje }}%</span>
                    </div>
                  </td>
                  <td class="text-center"><span class="fw-bold text-success">{{ $u->completadas_count }}</span></td>
                  <td class="text-center"><span class="fw-bold text-warning">{{ $u->en_proceso_count ?? 0 }}</span></td>
                  <td class="text-center"><span class="fw-bold text-danger">{{ max(0,$pen) }}</span></td>
                  <td class="text-center pe-4">
                    <span class="badge bg-label-{{ $u->color }} rounded-pill" style="font-size:10px">
                      <i class="icon-base ti {{ $icon }} icon-10px me-1"></i>{{ $u->semaforo }}
                    </span>
                  </td>
                  <td class="text-center pe-4">
                    <a href="{{ route('sci-control-interno') }}?unidad_organica_id={{ $u->id }}"
                       class="btn btn-xs btn-label-primary rounded-pill">Ver detalle</a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-6">Sin unidades registradas</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
            <small class="text-muted">Mostrando {{ $unidades->count() }} de {{ $unidades->count() }} unidades</small>
            <div class="d-flex align-items-center gap-1">
              <button class="btn btn-xs btn-icon btn-label-secondary rounded" disabled><i class="ti tabler-chevron-left"></i></button>
              <span class="badge bg-primary rounded px-2">1</span>
              <button class="btn btn-xs btn-icon btn-label-secondary rounded"><i class="ti tabler-chevron-right"></i></button>
            </div>
          </div>
        </div>

        {{-- Tab: Medidas de Remediación --}}
        <div class="tab-pane fade" id="tab-remediacion">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tbl-avance">
              <thead>
                <tr>
                  <th class="ps-4">Medida</th>
                  <th>Unidad</th>
                  <th>Actividad relacionada</th>
                  <th class="text-center">Estado</th>
                  <th class="text-center">Fecha límite</th>
                  <th class="text-center pe-3">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($medidas_remediacion as $m)
                @php $mc = match($m->estado) {'completada'=>'success','en_proceso'=>'warning','vencida'=>'danger',default=>'secondary'}; @endphp
                <tr>
                  <td class="ps-4">
                    <p class="fw-semibold mb-0" style="font-size:13px">{{ Str::limit($m->nombre, 35) }}</p>
                    <small class="text-muted">{{ $m->componente->nombre ?? '—' }}</small>
                  </td>
                  <td><span class="badge bg-label-secondary rounded-pill">{{ $m->unidadOrganica->sigla ?? '—' }}</span></td>
                  <td><small class="text-muted">{{ Str::limit($m->componente->nombre ?? '—', 25) }}</small></td>
                  <td class="text-center">
                    <span class="badge bg-label-{{ $mc }} rounded-pill" style="font-size:11px">{{ ucfirst(str_replace('_',' ',$m->estado)) }}</span>
                  </td>
                  <td class="text-center"><small class="text-muted">{{ $m->fecha_limite?->format('d/m/Y') ?? '—' }}</small></td>
                  <td class="text-center pe-4">
                    <div class="d-flex justify-content-center gap-1">
                      <button class="btn btn-xs btn-icon btn-label-primary rounded"><i class="ti tabler-eye"></i></button>
                      <button class="btn btn-xs btn-icon btn-label-secondary rounded"><i class="ti tabler-dots-vertical"></i></button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-6"><i class="ti tabler-tool d-block mb-2 fs-2 opacity-25"></i>Sin medidas de remediación</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-4 py-3 border-top">
            <a href="{{ route('sci-control-interno') }}" class="text-primary fw-medium" style="font-size:12px">Ver todas las medidas de remediación <i class="ti tabler-arrow-right icon-12px"></i></a>
          </div>
        </div>

        {{-- Tab: Medidas de Control --}}
        <div class="tab-pane fade" id="tab-control">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tbl-avance">
              <thead>
                <tr>
                  <th class="ps-4">Medida de control</th>
                  <th>Unidad</th>
                  <th>Actividad relacionada</th>
                  <th class="text-center">Estado</th>
                  <th class="text-center pe-3">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($medidas_control as $m)
                <tr>
                  <td class="ps-4">
                    <p class="fw-semibold mb-0" style="font-size:13px">{{ Str::limit($m->nombre, 38) }}</p>
                    <small class="text-muted">{{ $m->componente->nombre ?? '—' }}</small>
                  </td>
                  <td><span class="badge bg-label-secondary rounded-pill">{{ $m->unidadOrganica->sigla ?? '—' }}</span></td>
                  <td><small class="text-muted">{{ $m->componente->nombre ?? '—' }}</small></td>
                  <td class="text-center"><span class="badge bg-label-success rounded-pill">Activa</span></td>
                  <td class="text-center pe-4">
                    <div class="d-flex justify-content-center gap-1">
                      <button class="btn btn-xs btn-icon btn-label-primary rounded"><i class="ti tabler-eye"></i></button>
                      <button class="btn btn-xs btn-icon btn-label-secondary rounded"><i class="ti tabler-dots-vertical"></i></button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-6"><i class="ti tabler-shield d-block mb-2 fs-2 opacity-25"></i>Sin medidas de control</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-4 py-3 border-top">
            <a href="{{ route('sci-control-interno') }}" class="text-primary fw-medium" style="font-size:12px">Ver todas las medidas de control <i class="ti tabler-arrow-right icon-12px"></i></a>
          </div>
        </div>

        {{-- Tab: Historial --}}
        <div class="tab-pane fade" id="tab-historial">
          <div class="p-4 text-center text-muted py-6">
            <i class="ti tabler-history fs-1 d-block mb-3 opacity-25"></i>
            <p class="fw-medium">Historial de acciones disponible próximamente</p>
            <small>Se registrarán todas las modificaciones y aprobaciones del período.</small>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- Panel lateral: distribución + acciones rápidas --}}
  <div class="col-xl-4">

    {{-- Distribución por estado --}}
    <div class="card mb-4">
      <div class="card-header">
        <h6 class="fw-bold mb-1">Distribución por Estado</h6>
        <p class="card-subtitle mb-0">Actividades del período</p>
      </div>
      <div class="card-body pt-2">
        <div id="chartDistribucion" class="mx-auto mb-3" style="max-width:180px"></div>
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
              <div class="d-flex justify-content-between mb-1">
                <small class="fw-semibold">{{ $d['label'] }}</small>
                <small class="fw-bold text-{{ $d['color'] }}">{{ $d['val'] }}</small>
              </div>
              <div class="progress rounded-pill" style="height:4px">
                <div class="progress-bar bg-{{ $d['color'] }} rounded-pill"
                     style="width:{{ $total_actividades ? round($d['val']/$total_actividades*100) : 0 }}%"></div>
              </div>
            </div>
          </div>
          @endforeach
          <div class="border-top pt-2 d-flex justify-content-between">
            <small class="fw-semibold text-muted">Total</small>
            <small class="fw-bold">{{ $total_actividades }}</small>
          </div>
        </div>
      </div>
    </div>

    {{-- Acciones rápidas --}}
    <div class="card">
      <div class="card-header">
        <h6 class="fw-bold mb-0">Acciones Rápidas</h6>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
          <a href="{{ route('sci-control-interno') }}" class="btn btn-label-success btn-sm">
            <i class="ti tabler-plus me-1"></i>Nueva Medida de Remediación
          </a>
          <a href="{{ route('sci-control-interno') }}" class="btn btn-label-primary btn-sm">
            <i class="ti tabler-plus me-1"></i>Nueva Medida de Control
          </a>
          <a href="{{ route('rep-reportes') }}" class="btn btn-label-secondary btn-sm">
            <i class="ti tabler-file-text me-1"></i>Ver Reporte Detallado
          </a>
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
