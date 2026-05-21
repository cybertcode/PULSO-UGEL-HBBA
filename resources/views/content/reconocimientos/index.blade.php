@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Reconocimientos - PULSO UGEL')

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Reconocimientos</li>
  </ol>
</nav>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <div class="d-flex align-items-center gap-3 mb-1">
      <div class="avatar">
        <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-trophy icon-26px"></i></span>
      </div>
      <div>
        <h4 class="mb-0">Reconocimientos</h4>
        <p class="mb-0 text-muted small">Celebramos el compromiso y los resultados de quienes impulsan la mejora continua en el Sistema de Control Interno y el Modelo de Integridad.</p>
      </div>
    </div>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <form method="GET" action="{{ route('rep-reconocimientos') }}" class="d-flex gap-2">
      <select name="anio" class="form-select form-select-sm" onchange="this.form.submit()" style="width:200px">
        <option>I Trimestre {{ now()->year }} (Ene - Mar)</option>
        @foreach($anios as $a)
        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
      </select>
    </form>
    <button class="btn btn-sm btn-label-secondary">
      <i class="ti tabler-download me-1"></i>Exportar
    </button>
  </div>
</div>

{{-- ── Tabs principales ── --}}
<div class="card mb-4">
  <div class="card-header pb-0">
    <ul class="nav nav-tabs card-header-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-resumen" role="tab">Resumen</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-reconocidos" role="tab">
          Reconocimientos
          @if($ranking->count() > 0)
          <span class="badge bg-label-warning rounded-pill ms-1">{{ $ranking->count() }}</span>
          @endif
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-historico" role="tab">Histórico</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-criterios" role="tab">Criterios</a>
      </li>
    </ul>
  </div>

  <div class="tab-content">

    {{-- Tab Resumen --}}
    <div class="tab-pane fade show active" id="tab-resumen" role="tabpanel">
      <div class="card-body">

        {{-- KPI Cards --}}
        <div class="row g-4 mb-5">
          @php
          $rkpis = [
            ['val'=>$ranking->count(),                          'label'=>'Reconocimientos Entregados', 'sub'=>'+20% vs. trimestre anterior', 'color'=>'primary', 'icon'=>'tabler-award'],
            ['val'=>$ranking->count(),                          'label'=>'Funcionarios Reconocidos',   'sub'=>'+12% vs. trimestre anterior', 'color'=>'info',    'icon'=>'tabler-users'],
            ['val'=>$ranking->groupBy('unidad_organica_id')->count(),'label'=>'Unidades Destacadas',  'sub'=>'+1 vs. trimestre anterior',   'color'=>'success', 'icon'=>'tabler-building-community'],
            ['val'=>($ranking->isNotEmpty() ? $ranking->avg('puntaje') : 0).'%','label'=>'Promedio de Cumplimiento','sub'=>'+8.5% vs. trimestre anterior','color'=>'warning','icon'=>'tabler-chart-pie'],
          ];
          @endphp
          @foreach($rkpis as $rk)
          <div class="col-6 col-md-3">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-4">
                  <div class="badge rounded bg-label-{{ $rk['color'] }} p-2">
                    <i class="icon-base ti {{ $rk['icon'] }} icon-26px"></i>
                  </div>
                </div>
                <h3 class="mb-1 text-{{ $rk['color'] }}">{{ $rk['val'] }}</h3>
                <p class="mb-0 fw-medium">{{ $rk['label'] }}</p>
                <small class="text-body-secondary">
                  <i class="ti tabler-trending-up icon-12px text-success me-1"></i>{{ $rk['sub'] }}
                </small>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        {{-- Banner reconocimiento a la excelencia --}}
        <div class="card mb-5 border-warning border-opacity-25" style="background:linear-gradient(135deg,rgba(255,193,7,.08) 0%,rgba(255,159,67,.05) 100%)">
          <div class="card-body py-4">
            <div class="row align-items-center g-3">
              <div class="col-auto">
                <div class="avatar" style="width:56px;height:56px">
                  <span class="avatar-initial rounded-circle bg-label-warning" style="width:56px;height:56px;font-size:28px">🏆</span>
                </div>
              </div>
              <div class="col">
                <h5 class="mb-1 text-warning fw-bold">Reconocimiento a la Excelencia en Gestión</h5>
                <p class="mb-0 text-muted">Este reconocimiento destaca a los funcionarios y unidades que, con liderazgo, compromiso y buenas prácticas, generan un impacto positivo en la integridad institucional.</p>
              </div>
              <div class="col-auto">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCriterios">
                  <i class="ti tabler-info-circle me-1"></i>Conoce los criterios
                </button>
              </div>
            </div>
          </div>
        </div>

        @if($ranking->isEmpty())
        <div class="text-center py-5">
          <i class="ti tabler-trophy-off icon-48px d-block mb-3 text-muted"></i>
          <h5 class="text-muted">Sin reconocimientos para este período</h5>
          <p class="text-muted mb-4">Los reconocimientos se generan automáticamente al cierre de cada período evaluado.</p>
          <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-label-warning">
            <i class="ti tabler-trophy me-1"></i>Ver Ranking Actual
          </a>
        </div>
        @else

        {{-- ── Reconocimientos Destacados del Período — Podio ── --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5 class="mb-0">Reconocimientos Destacados del Período</h5>
          <a href="#tab-reconocidos" data-bs-toggle="tab" class="btn btn-xs btn-label-secondary">Ver todos</a>
        </div>

        <div class="row g-4 mb-5">
          @foreach($top3->take(4) as $idx => $r)
          @php
            $lugares = ['Primer Lugar', 'Segundo Lugar', 'Tercer Lugar', 'Mención Especial'];
            $lcolors = ['warning','secondary','orange','info'];
            $lcolor  = ['warning','secondary','warning','info'][$idx] ?? 'secondary';
            $medalColors = ['#ffc107','#6c757d','#cd7f32','#00cfe8'];
          @endphp
          <div class="col-md-6 col-xl-3">
            <div class="card h-100 text-center">
              <div class="card-body py-4">
                <small class="text-muted d-block mb-2">{{ $lugares[$idx] ?? 'Destacado' }}</small>
                {{-- Avatar --}}
                <div class="avatar mx-auto mb-3" style="width:64px;height:64px">
                  <span class="avatar-initial rounded-circle bg-label-{{ $lcolor }}" style="font-size:20px;font-weight:700;width:64px;height:64px;line-height:64px">
                    {{ strtoupper(substr($r->unidadOrganica->sigla ?? 'U', 0, 2)) }}
                  </span>
                </div>
                <div class="fw-bold mb-0">{{ $r->unidadOrganica->sigla ?? '—' }}</div>
                <small class="text-muted d-block mb-2">{{ Str::limit($r->unidadOrganica->nombre ?? '—', 30) }}</small>
                <p class="text-muted small mb-3" style="font-size:11px">
                  {{ Str::limit($r->observaciones ?? 'Desempeño destacado en actividades de control.', 80) }}
                </p>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="badge bg-label-{{ $lcolor }}">
                    Puntaje: {{ $r->puntaje }}/100
                  </span>
                  <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-xs btn-label-secondary">Ver detalle</a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        {{-- ── Ranking + Próxima Ceremonia ── --}}
        <div class="row g-4">
          <div class="col-xl-8">
            <h5 class="mb-3">Reconocimientos Recientes</h5>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Fecha</th>
                    <th>Reconocido</th>
                    <th>Unidad</th>
                    <th class="text-center">Categoría</th>
                    <th>Motivo</th>
                    <th class="text-center">Puntaje</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($ranking->take(6) as $r)
                  @php $pc = $r->avance_global >= 75 ? 'success' : ($r->avance_global >= 50 ? 'warning' : 'danger'); @endphp
                  <tr>
                    <td><small class="text-muted">{{ $r->created_at->format('d/m/Y') }}</small></td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm">
                          <span class="avatar-initial rounded-circle bg-label-{{ $pc }}" style="font-size:11px;font-weight:700">
                            {{ strtoupper(substr($r->unidadOrganica->sigla ?? 'U', 0, 2)) }}
                          </span>
                        </div>
                        <div class="fw-medium">{{ $r->unidadOrganica->sigla ?? '—' }}</div>
                      </div>
                    </td>
                    <td><small class="text-muted">{{ Str::limit($r->unidadOrganica->nombre ?? '—', 30) }}</small></td>
                    <td class="text-center">
                      <span class="badge bg-label-{{ $pc }}">
                        @if($r->medalla === 'oro') Control Interno
                        @elseif($r->medalla === 'plata') Modelo de Integridad
                        @else Buenas Prácticas
                        @endif
                      </span>
                    </td>
                    <td><small class="text-muted">{{ Str::limit($r->observaciones ?? 'Implementación de controles.', 40) }}</small></td>
                    <td class="text-center fw-bold text-primary">{{ $r->puntaje }}</td>
                    <td>
                      <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-xs btn-label-secondary">
                        <i class="ti tabler-eye icon-12px me-1"></i>Ver
                      </a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          {{-- Próxima Ceremonia + Ranking de Unidades --}}
          <div class="col-xl-4">
            <div class="card mb-3 border-warning border-opacity-25">
              <div class="card-header">
                <h6 class="mb-0"><i class="ti tabler-calendar-event me-2 text-warning"></i>Próxima Ceremonia</h6>
              </div>
              <div class="card-body py-3">
                @php
                  $ceremonia = now()->addMonth()->startOfMonth();
                @endphp
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div class="badge bg-label-warning p-2 rounded">
                    <i class="icon-base ti tabler-calendar icon-22px"></i>
                  </div>
                  <div>
                    <div class="fw-medium">{{ $ceremonia->translatedFormat('d \d\e F \d\e Y') }}</div>
                    <small class="text-muted">Ceremonia de Reconocimiento</small>
                  </div>
                </div>
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div class="badge bg-label-primary p-2 rounded">
                    <i class="icon-base ti tabler-award icon-22px"></i>
                  </div>
                  <div>
                    <div class="fw-medium">Reconocimiento a la Gestión Íntegra</div>
                    <small class="text-muted">I Semestre {{ now()->year }}</small>
                  </div>
                </div>
                <a href="#" class="btn btn-xs btn-label-warning w-100 mt-2">
                  <i class="ti tabler-calendar-check me-1"></i>Ver detalles del evento
                </a>
              </div>
            </div>

            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0">Ranking de Unidades</h6>
                <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-xs btn-label-secondary">Ver ranking completo</a>
              </div>
              <div class="card-body p-0">
                @foreach($ranking->take(4) as $idx => $r)
                @php $pc2 = $r->avance_global >= 75 ? 'success' : ($r->avance_global >= 50 ? 'warning' : 'danger'); @endphp
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                  <span class="badge bg-label-{{ $idx === 0 ? 'warning' : 'secondary' }}" style="min-width:24px;text-align:center">{{ $idx+1 }}</span>
                  <div class="avatar avatar-xs">
                    <span class="avatar-initial rounded-circle bg-label-{{ $pc2 }}" style="font-size:10px;font-weight:700">
                      {{ strtoupper(substr($r->unidadOrganica->sigla ?? 'U', 0, 2)) }}
                    </span>
                  </div>
                  <div class="flex-grow-1">
                    <div class="fw-medium small">{{ $r->unidadOrganica->sigla ?? '—' }}</div>
                    <small class="text-muted" style="font-size:10px">{{ Str::limit($r->unidadOrganica->nombre ?? '—', 25) }}</small>
                  </div>
                  <span class="fw-bold text-{{ $pc2 }}">{{ $r->puntaje }}</span>
                  <small class="text-muted">pts.</small>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        @endif {{-- /ranking no vacío --}}

      </div>
    </div>

    {{-- Tab Reconocidos --}}
    <div class="tab-pane fade" id="tab-reconocidos" role="tabpanel">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:60px" class="text-center">Pos.</th>
                <th>Unidad Orgánica</th>
                <th class="text-center">Actividades</th>
                <th style="min-width:160px">Avance</th>
                <th class="text-center">Puntaje</th>
                <th class="text-center">Reconocimiento</th>
              </tr>
            </thead>
            <tbody>
              @forelse($ranking as $r)
              @php
                $pc = $r->avance_global >= 75 ? 'success' : ($r->avance_global >= 50 ? 'warning' : 'danger');
              @endphp
              <tr>
                <td class="text-center">
                  @if($r->posicion <= 3)
                  <span class="badge {{ $r->posicion == 1 ? 'bg-warning text-dark' : 'bg-label-secondary' }} px-2">{{ $r->posicion }}°</span>
                  @else
                  <span class="badge bg-label-secondary">{{ $r->posicion }}°</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm">
                      <span class="avatar-initial rounded-circle bg-label-{{ $pc }}" style="font-size:11px;font-weight:700">
                        {{ strtoupper(substr($r->unidadOrganica->sigla ?? 'U', 0, 2)) }}
                      </span>
                    </div>
                    <div>
                      <div class="fw-semibold">{{ $r->unidadOrganica->sigla ?? '—' }}</div>
                      <small class="text-muted">{{ Str::limit($r->unidadOrganica->nombre ?? '—', 35) }}</small>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  <span class="fw-medium">{{ $r->actividades_completadas }}</span>
                  <span class="text-muted">/{{ $r->actividades_total }}</span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:8px">
                      <div class="progress-bar bg-{{ $pc }} rounded-pill" style="width:{{ $r->avance_global }}%"></div>
                    </div>
                    <span class="fw-bold text-{{ $pc }}" style="min-width:38px">{{ $r->avance_global }}%</span>
                  </div>
                </td>
                <td class="text-center"><strong class="text-primary">{{ $r->puntaje }}</strong></td>
                <td class="text-center">
                  @if($r->medalla === 'oro')
                  <span class="badge bg-label-warning"><i class="ti tabler-trophy me-1"></i>Oro</span>
                  @elseif($r->medalla === 'plata')
                  <span class="badge bg-label-secondary"><i class="ti tabler-medal me-1"></i>Plata</span>
                  @elseif($r->medalla === 'bronce')
                  <span class="badge" style="background:rgba(205,127,50,.15);color:#cd7f32"><i class="ti tabler-medal-2 me-1"></i>Bronce</span>
                  @else
                  <span class="text-muted">—</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center text-muted py-5">Sin reconocimientos para este período</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Tab Histórico --}}
    <div class="tab-pane fade" id="tab-historico" role="tabpanel">
      <div class="card-body text-center py-5 text-muted">
        <i class="ti tabler-history icon-48px d-block mb-3"></i>
        <h6>Historial de Reconocimientos</h6>
        <p class="mb-0">Aquí se mostrará el historial completo de reconocimientos por período.</p>
      </div>
    </div>

    {{-- Tab Criterios --}}
    <div class="tab-pane fade" id="tab-criterios" role="tabpanel">
      <div class="card-body">
        <h5 class="mb-4">Criterios de Evaluación para Reconocimientos</h5>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card border-primary border-opacity-25 h-100">
              <div class="card-body">
                <div class="avatar mb-3"><span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-shield-check icon-24px"></i></span></div>
                <h6 class="fw-bold mb-2">Control Interno</h6>
                <p class="text-muted small mb-0">Porcentaje de actividades completadas del Sistema de Control Interno en el período evaluado.</p>
                <div class="mt-3"><span class="badge bg-label-primary">Peso: 40%</span></div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border-info border-opacity-25 h-100">
              <div class="card-body">
                <div class="avatar mb-3"><span class="avatar-initial rounded bg-label-info"><i class="ti tabler-shield-half icon-24px"></i></span></div>
                <h6 class="fw-bold mb-2">Modelo de Integridad</h6>
                <p class="text-muted small mb-0">Cumplimiento de los componentes del Modelo de Integridad asignados a la unidad.</p>
                <div class="mt-3"><span class="badge bg-label-info">Peso: 40%</span></div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border-success border-opacity-25 h-100">
              <div class="card-body">
                <div class="avatar mb-3"><span class="avatar-initial rounded bg-label-success"><i class="ti tabler-star icon-24px"></i></span></div>
                <h6 class="fw-bold mb-2">Buenas Prácticas</h6>
                <p class="text-muted small mb-0">Iniciativas y buenas prácticas implementadas que contribuyen a la integridad institucional.</p>
                <div class="mt-3"><span class="badge bg-label-success">Peso: 20%</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Modal criterios --}}
<div class="modal fade" id="modalCriterios" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Criterios de Evaluación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Los reconocimientos se otorgan en base a tres criterios ponderados:</p>
        <ul>
          <li><strong>Control Interno (40%):</strong> % actividades SCI completadas</li>
          <li><strong>Modelo de Integridad (40%):</strong> cumplimiento de componentes</li>
          <li><strong>Buenas Prácticas (20%):</strong> iniciativas y prácticas implementadas</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@endsection
