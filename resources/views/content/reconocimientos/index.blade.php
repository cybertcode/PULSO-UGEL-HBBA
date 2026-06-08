@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Reconocimientos - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
       'resources/assets/vendor/libs/select2/select2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
       'resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Reconocimientos</li>
  </ol>
</nav>

{{-- Header --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div class="d-flex align-items-start gap-3">
    <div class="badge rounded bg-label-warning p-3">
      <i class="ti tabler-trophy icon-28px"></i>
    </div>
    <div>
      <h4 class="mb-1">Reconocimientos</h4>
      <p class="text-muted mb-0">Celebramos el compromiso y los resultados de quienes impulsan la mejora continua<br>en el Sistema de Control Interno y el Modelo de Integridad.</p>
    </div>
  </div>
  <div class="d-flex gap-2 align-items-center flex-wrap">
    <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
      <select name="anio" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
        @foreach($anios as $a)
        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
      </select>
      <select name="mes" class="form-select form-select-sm" style="width:130px" onchange="this.form.submit()">
        <option value="">Año completo</option>
        @foreach($meses as $m => $nm)
        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ $nm }}</option>
        @endforeach
      </select>
    </form>
    <a href="{{ route('rep-reportes', ['tipo'=>'reconocimientos','anio'=>$anio]) }}" class="btn btn-label-secondary btn-sm">
      <i class="ti tabler-download me-1"></i>Exportar
    </a>
  </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="reconocimientosTabs">
  <li class="nav-item">
    <a class="nav-link active" href="#tab-resumen" data-bs-toggle="tab">Resumen</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#tab-reconocimientos" data-bs-toggle="tab">
      Reconocimientos
      <span class="badge bg-primary rounded-pill ms-1">{{ $trabajadores->count() }}</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#tab-historico" data-bs-toggle="tab">Histórico</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#tab-criterios" data-bs-toggle="tab">Criterios</a>
  </li>
</ul>

<div class="tab-content">

{{-- TAB RESUMEN --}}
<div class="tab-pane fade show active" id="tab-resumen">

  {{-- KPIs --}}
  <div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="badge rounded bg-label-primary p-2"><i class="ti tabler-trophy icon-20px"></i></div>
            <span class="text-muted small">Reconocimientos Entregados</span>
          </div>
          <h3 class="mb-0 text-primary">{{ $stats['total_reconocidos'] ?? 12 }}</h3>
          <small class="text-success"><i class="ti tabler-trending-up icon-14px"></i> +20% vs. trimestre anterior</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="badge rounded bg-label-info p-2"><i class="ti tabler-users icon-20px"></i></div>
            <span class="text-muted small">Funcionarios Reconocidos</span>
          </div>
          <h3 class="mb-0 text-info">18</h3>
          <small class="text-success"><i class="ti tabler-trending-up icon-14px"></i> +12% vs. trimestre anterior</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="badge rounded bg-label-warning p-2"><i class="ti tabler-building-community icon-20px"></i></div>
            <span class="text-muted small">Unidades Destacadas</span>
          </div>
          <h3 class="mb-0 text-warning">{{ $stats['unidades_destacadas'] ?? 4 }}</h3>
          <small class="text-success"><i class="ti tabler-trending-up icon-14px"></i> +1 vs. trimestre anterior</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="badge rounded bg-label-success p-2"><i class="ti tabler-chart-line icon-20px"></i></div>
            <span class="text-muted small">Promedio de Cumplimiento</span>
          </div>
          <h3 class="mb-0 text-success">{{ $stats['promedio_puntaje'] ?? 85.6 }}%</h3>
          <small class="text-danger"><i class="ti tabler-trending-down icon-14px"></i> -8.5% vs. trimestre anterior</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Main + Sidebar --}}
  <div class="row g-4">
    <div class="col-xl-8">

      {{-- Banner Reconocimiento a la Excelencia --}}
      <div class="card mb-4 bg-primary text-white">
        <div class="card-body d-flex align-items-center justify-content-between gap-3 flex-wrap">
          <div class="d-flex align-items-center gap-3">
            <div class="badge bg-white text-primary rounded p-3 flex-shrink-0">
              <i class="ti tabler-trophy icon-24px"></i>
            </div>
            <div>
              <h5 class="text-white mb-1">Reconocimiento a la Excelencia en Gestión</h5>
              <p class="mb-0 small" style="opacity:.85">Este reconocimiento destaca a los funcionarios y unidades que, con liderazgo, compromiso y buenas prácticas, generan un impacto positivo en la integridad institucional.</p>
            </div>
          </div>
          <a href="#tab-criterios" data-bs-toggle="tab" class="btn btn-white btn-sm flex-shrink-0">Conoce los criterios</a>
        </div>
      </div>

      {{-- Podio Reconocimientos Destacados --}}
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="mb-0"><i class="ti tabler-award me-2 text-warning"></i>Reconocimientos Destacados del Período</h5>
          <a href="#tab-reconocimientos" data-bs-toggle="tab" class="btn btn-sm btn-label-secondary">Ver todos →</a>
        </div>
        <div class="card-body">
          <div class="row g-3">
            @php
              $podioItems = [
                ['label'=>'Primer Lugar',    'color'=>'warning',  'icon'=>'ti tabler-medal'],
                ['label'=>'Segundo Lugar',   'color'=>'secondary','icon'=>'ti tabler-medal-2'],
                ['label'=>'Tercer Lugar',    'color'=>'danger',   'icon'=>'ti tabler-medal-2'],
                ['label'=>'Mención Especial','color'=>'info',     'icon'=>'ti tabler-star'],
              ];
            @endphp
            @foreach($top3 as $idx => $t)
            @php $p = $podioItems[$idx] ?? $podioItems[3]; @endphp
            <div class="col-md-6 col-xl-3">
              <div class="card border h-100 text-center">
                <div class="card-body p-3">
                  <span class="badge bg-label-{{ $p['color'] }} rounded-pill mb-2 d-inline-block px-2 py-1" style="font-size:11px">
                    <i class="{{ $p['icon'] }} me-1"></i>{{ $p['label'] }}
                  </span>
                  <div class="avatar avatar-md mx-auto mb-2">
                    <img src="{{ $t->foto_url }}" alt="{{ $t->nombre }}" class="rounded-circle" style="width:52px;height:52px;object-fit:cover">
                  </div>
                  <div class="fw-semibold" style="font-size:13px;line-height:1.3">{{ $t->nombre }}</div>
                  <div class="text-muted mb-1" style="font-size:11px">{{ $t->unidadOrganica->sigla ?? $t->unidadOrganica->nombre ?? '—' }}</div>
                  @if($t->motivo)
                  <div class="text-muted mb-2" style="font-size:11px;font-style:italic;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $t->motivo }}</div>
                  @endif
                  <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="text-muted" style="font-size:11px">Puntaje: <strong class="text-body">{{ number_format($t->puntaje_total,1) }}/100</strong></span>
                    <a href="{{ route('rep-reconocimientos.show', $t) }}" class="btn btn-sm btn-label-primary py-0 px-2" style="font-size:11px">Ver detalle →</a>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
            @for($i = $top3->count(); $i < 4; $i++)
            @php $p = $podioItems[$i]; @endphp
            <div class="col-md-6 col-xl-3">
              <div class="card border h-100 text-center bg-body-secondary">
                <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                  <span class="badge bg-label-{{ $p['color'] }} rounded-pill mb-2 d-inline-block px-2 py-1" style="font-size:11px">{{ $p['label'] }}</span>
                  <div class="text-muted" style="font-size:12px">Sin registro</div>
                </div>
              </div>
            </div>
            @endfor
          </div>
        </div>
      </div>

      {{-- Tabla Reconocimientos Recientes --}}
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Reconocimientos Recientes</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
              <thead class="table-light">
                <tr>
                  <th>Fecha</th>
                  <th>Reconocido</th>
                  <th>Unidad</th>
                  <th>Categoría</th>
                  <th>Motivo</th>
                  <th>Puntaje</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($trabajadores->take(10) as $t)
                <tr>
                  <td><small class="text-muted">{{ $t->created_at ? $t->created_at->format('d/m/Y') : '—' }}</small></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="avatar avatar-sm">
                        <img src="{{ $t->foto_url }}" class="rounded-circle" alt="{{ $t->nombre }}" style="width:32px;height:32px;object-fit:cover">
                      </div>
                      <div class="fw-medium" style="font-size:13px">{{ $t->nombre }}</div>
                    </div>
                  </td>
                  <td><span class="badge bg-label-secondary">{{ $t->unidadOrganica->sigla ?? '—' }}</span></td>
                  <td>
                    @if($t->categoria)
                    <span class="badge bg-label-primary" style="font-size:11px">{{ $t->categoria }}</span>
                    @else
                    <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td><small class="text-muted" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;max-width:200px">{{ $t->motivo ?? '—' }}</small></td>
                  <td><span class="fw-bold text-primary">{{ number_format($t->puntaje_total,1) }}</span></td>
                  <td>
                    <a href="{{ route('rep-reconocimientos.show', $t) }}" class="btn btn-icon btn-sm btn-label-primary" title="Ver detalle">
                      <i class="ti tabler-eye"></i>
                    </a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-5">
                    <i class="ti tabler-trophy-off icon-32px d-block mb-2"></i>
                    No hay reconocimientos para el período seleccionado.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>{{-- col-xl-8 --}}

    {{-- Sidebar --}}
    <div class="col-xl-4">

      {{-- Ranking de Unidades --}}
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h6 class="mb-0"><i class="ti tabler-list-numbers me-2 text-primary"></i>Ranking de Unidades</h6>
          <a href="#" class="btn btn-sm btn-label-primary py-0" style="font-size:11px">Ver ranking completo →</a>
        </div>
        <div class="card-body p-0">
          @php
            $rankingUnidades = [
              ['sigla'=>'UGA',  'nombre'=>'Unidad de Gestión Administrativa',          'puntaje'=>92.5, 'pos'=>1, 'color'=>'warning'],
              ['sigla'=>'UPDI', 'nombre'=>'Unidad de Planeamiento y Desarrollo Inst.', 'puntaje'=>87.3, 'pos'=>2, 'color'=>'secondary'],
              ['sigla'=>'UGP',  'nombre'=>'Unidad de Gestión Pedagógica',              'puntaje'=>84.1, 'pos'=>3, 'color'=>'danger'],
              ['sigla'=>'OCI',  'nombre'=>'Órgano de Control Institucional',           'puntaje'=>78.9, 'pos'=>4, 'color'=>'info'],
            ];
          @endphp
          <ul class="list-group list-group-flush">
            @foreach($rankingUnidades as $r)
            <li class="list-group-item d-flex align-items-center gap-3 py-3">
              <div class="badge bg-label-{{ $r['color'] }} rounded fw-bold" style="min-width:28px;text-align:center">{{ $r['pos'] }}</div>
              <div class="flex-grow-1 overflow-hidden">
                <div class="fw-semibold text-truncate" style="font-size:13px">{{ $r['sigla'] }}</div>
                <div class="text-muted text-truncate" style="font-size:11px">{{ $r['nombre'] }}</div>
              </div>
              <div class="text-end flex-shrink-0">
                <div class="fw-bold text-primary" style="font-size:15px">{{ $r['puntaje'] }}</div>
                <div class="text-muted" style="font-size:10px">puntos</div>
              </div>
            </li>
            @endforeach
          </ul>
        </div>
      </div>

      {{-- Próxima Ceremonia --}}
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0"><i class="ti tabler-calendar-event me-2 text-success"></i>Próxima Ceremonia</h6>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="badge rounded bg-label-success p-2 flex-shrink-0">
              <i class="ti tabler-calendar icon-20px"></i>
            </div>
            <div>
              <div class="fw-semibold" style="font-size:13px">{{ $stats['proxima_ceremonia'] ?? '15 de julio de 2026' }}</div>
              <div class="text-muted" style="font-size:12px">Evento</div>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="badge rounded bg-label-primary p-2 flex-shrink-0">
              <i class="ti tabler-trophy icon-20px"></i>
            </div>
            <div>
              <div class="fw-semibold" style="font-size:13px">Reconocimiento a la Gestión Íntegra</div>
              <div class="text-muted" style="font-size:12px">I Semestre 2026</div>
            </div>
          </div>
          <a href="#" class="btn btn-label-success btn-sm w-100">
            <i class="ti tabler-info-circle me-1"></i>Ver detalles del evento →
          </a>
        </div>
      </div>

    </div>{{-- sidebar --}}
  </div>{{-- row --}}

</div>{{-- tab-pane resumen --}}

{{-- TAB RECONOCIMIENTOS --}}
<div class="tab-pane fade" id="tab-reconocimientos">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h5 class="mb-0">Todos los Reconocimientos</h5>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoReconocimiento">
        <i class="ti tabler-plus me-1"></i>Nuevo Reconocimiento
      </button>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Servidor/a</th>
              <th>Unidad</th>
              <th>Categoría</th>
              <th>Cumplimiento</th>
              <th>Puntaje Total</th>
              <th>Nivel</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($trabajadores as $idx => $t)
            <tr>
              <td><strong class="text-muted">{{ $idx + 1 }}</strong></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar avatar-sm">
                    <img src="{{ $t->foto_url }}" class="rounded-circle" alt="{{ $t->nombre }}" style="width:36px;height:36px;object-fit:cover">
                  </div>
                  <div>
                    <div class="fw-medium" style="font-size:13px">{{ $t->nombre }}</div>
                    <small class="text-muted">{{ $t->cargo }}</small>
                  </div>
                </div>
              </td>
              <td><span class="badge bg-label-secondary">{{ $t->unidadOrganica->sigla ?? '—' }}</span></td>
              <td><small>{{ $t->categoria ?? '—' }}</small></td>
              <td>
                <div class="d-flex align-items-center gap-1">
                  <div class="progress flex-grow-1" style="height:5px;min-width:50px">
                    <div class="progress-bar bg-success" style="width:{{ $t->puntaje_cumplimiento }}%"></div>
                  </div>
                  <small class="fw-bold">{{ $t->puntaje_cumplimiento }}%</small>
                </div>
              </td>
              <td><span class="badge bg-label-{{ $t->nivel_color }} fs-6">{{ number_format($t->puntaje_total, 1) }}%</span></td>
              <td><span class="badge bg-{{ $t->nivel_color }}">{{ $t->nivel }}</span></td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('rep-reconocimientos.show', $t) }}" class="btn btn-icon btn-sm btn-label-info" title="Ver detalle">
                    <i class="ti tabler-eye"></i>
                  </a>
                  <button class="btn btn-icon btn-sm btn-label-primary btn-editar-reconocimiento"
                    data-id="{{ $t->id }}"
                    data-nombre="{{ $t->nombre }}"
                    data-cargo="{{ $t->cargo }}"
                    data-unidad="{{ $t->unidad_organica_id }}"
                    data-dni="{{ $t->dni }}"
                    data-correo="{{ $t->correo }}"
                    data-cumplimiento="{{ $t->puntaje_cumplimiento }}"
                    data-puntualidad="{{ $t->puntaje_puntualidad }}"
                    data-participacion="{{ $t->puntaje_participacion }}"
                    data-responsabilidad="{{ $t->puntaje_responsabilidad }}"
                    data-categoria="{{ $t->categoria }}"
                    data-motivo="{{ $t->motivo }}"
                    data-resolucion="{{ $t->numero_resolucion }}"
                    title="Editar">
                    <i class="ti tabler-edit"></i>
                  </button>
                  <form method="POST" action="{{ route('rep-reconocimientos.destroy', $t) }}" class="form-eliminar-rec d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-icon btn-sm btn-label-danger" title="Eliminar">
                      <i class="ti tabler-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-5">
              <i class="ti tabler-trophy-off icon-32px d-block mb-2"></i>
              No hay reconocimientos para el período seleccionado.
            </td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- TAB HISTÓRICO --}}
<div class="tab-pane fade" id="tab-historico">
  <div class="card">
    <div class="card-body text-center py-5">
      <div class="badge rounded bg-label-secondary p-4 mb-3 d-inline-block">
        <i class="ti tabler-history icon-32px"></i>
      </div>
      <h5 class="mb-2">Histórico de Reconocimientos</h5>
      <p class="text-muted">Consulta el historial completo de reconocimientos por período.</p>
      <a href="#" class="btn btn-primary btn-sm">Cargar histórico</a>
    </div>
  </div>
</div>

{{-- TAB CRITERIOS --}}
<div class="tab-pane fade" id="tab-criterios">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Criterios de Evaluación</h5>
    </div>
    <div class="card-body">
      <p class="text-muted mb-4">Los reconocimientos se otorgan en base a los siguientes indicadores de evaluación institucional:</p>
      <div class="row g-4">
        <div class="col-md-6 col-xl-3">
          <div class="card border text-center h-100">
            <div class="card-body p-4">
              <div class="badge rounded bg-label-success p-3 mb-3 d-inline-block"><i class="ti tabler-check icon-24px"></i></div>
              <h6>Cumplimiento</h6>
              <p class="text-muted small mb-2">Nivel de cumplimiento de actividades y compromisos asignados.</p>
              <span class="badge bg-label-success">Peso: 40%</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card border text-center h-100">
            <div class="card-body p-4">
              <div class="badge rounded bg-label-primary p-3 mb-3 d-inline-block"><i class="ti tabler-clock icon-24px"></i></div>
              <h6>Puntualidad</h6>
              <p class="text-muted small mb-2">Entrega oportuna de informes, evidencias y reportes solicitados.</p>
              <span class="badge bg-label-primary">Peso: 25%</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card border text-center h-100">
            <div class="card-body p-4">
              <div class="badge rounded bg-label-warning p-3 mb-3 d-inline-block"><i class="ti tabler-users icon-24px"></i></div>
              <h6>Participación</h6>
              <p class="text-muted small mb-2">Participación activa en capacitaciones, talleres y reuniones institucionales.</p>
              <span class="badge bg-label-warning">Peso: 20%</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="card border text-center h-100">
            <div class="card-body p-4">
              <div class="badge rounded bg-label-info p-3 mb-3 d-inline-block"><i class="ti tabler-shield-check icon-24px"></i></div>
              <h6>Responsabilidad</h6>
              <p class="text-muted small mb-2">Compromiso con la integridad institucional y las buenas prácticas de gestión.</p>
              <span class="badge bg-label-info">Peso: 15%</span>
            </div>
          </div>
        </div>
      </div>
      <div class="alert alert-primary mt-4 mb-0">
        <div class="d-flex align-items-start gap-2">
          <i class="ti tabler-info-circle flex-shrink-0 mt-1"></i>
          <div>
            <strong>Niveles de Reconocimiento:</strong>
            <div class="d-flex flex-wrap gap-2 mt-2">
              <span class="badge bg-success">Excelente (90–100%)</span>
              <span class="badge bg-primary">Muy Bueno (80–89%)</span>
              <span class="badge bg-warning">Bueno (70–79%)</span>
              <span class="badge bg-secondary">Regular (60–69%)</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</div>{{-- tab-content --}}

{{-- Modal Nuevo Reconocimiento --}}
<div class="modal fade" id="modalNuevoReconocimiento" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('rep-reconocimientos.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-trophy me-2 text-warning"></i>Propuesta de Reconocimiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" required placeholder="Nombres y apellidos">
            </div>
            <div class="col-md-4">
              <label class="form-label">DNI</label>
              <input type="text" name="dni" class="form-control" maxlength="8" placeholder="12345678">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cargo</label>
              <input type="text" name="cargo" class="form-control" placeholder="Especialista en...">
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo institucional</label>
              <input type="email" name="correo" class="form-control" placeholder="servidor@ugel.gob.pe">
            </div>
            <div class="col-md-6">
              <label class="form-label">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2-rec">
                <option value="">Seleccionar unidad</option>
                @foreach(\App\Models\UnidadOrganica::where('activo',true)->orderBy('nombre')->get() as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Año <span class="text-danger">*</span></label>
              <select name="anio" class="form-select">
                @foreach($anios as $a)
                <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Mes</label>
              <select name="mes" class="form-select">
                <option value="">Anual</option>
                @foreach($meses as $m => $nm)
                <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ $nm }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Categoría</label>
              <select name="categoria" class="form-select">
                <option value="">Sin categoría</option>
                @foreach($categorias as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">N° Resolución Directoral</label>
              <input type="text" name="numero_resolucion" class="form-control" placeholder="RD N° 1457-2025">
            </div>
            <hr class="my-1">
            <div class="col-12"><h6 class="text-muted mb-0"><i class="ti tabler-chart-bar me-1"></i>Indicadores de Evaluación (0-100)</h6></div>
            <div class="col-md-3">
              <label class="form-label">Cumplimiento <span class="text-danger">*</span></label>
              <input type="number" name="puntaje_cumplimiento" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Puntualidad <span class="text-danger">*</span></label>
              <input type="number" name="puntaje_puntualidad" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Participación <span class="text-danger">*</span></label>
              <input type="number" name="puntaje_participacion" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Responsabilidad <span class="text-danger">*</span></label>
              <input type="number" name="puntaje_responsabilidad" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Foto del servidor/a</label>
              <input type="file" name="foto" class="form-control" accept="image/*">
              <div class="form-text">Imagen JPG/PNG. Máx. 2MB.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Resolución Directoral (PDF)</label>
              <input type="file" name="resolucion_archivo" class="form-control" accept=".pdf">
              <div class="form-text">PDF. Máx. 5MB.</div>
            </div>
            <div class="col-12">
              <label class="form-label">Motivo / Justificación</label>
              <textarea name="motivo" class="form-control" rows="3" placeholder="Descripción de los logros y contribuciones..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning"><i class="ti tabler-trophy me-1"></i>Registrar Reconocimiento</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Editar Reconocimiento --}}
<div class="modal fade" id="modalEditarReconocimiento" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="formEditarRec" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Reconocimiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="rec_nombre" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">DNI</label>
              <input type="text" name="dni" id="rec_dni" class="form-control" maxlength="8">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cargo</label>
              <input type="text" name="cargo" id="rec_cargo" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo</label>
              <input type="email" name="correo" id="rec_correo" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Categoría</label>
              <select name="categoria" id="rec_categoria" class="form-select">
                <option value="">Sin categoría</option>
                @foreach($categorias as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">N° Resolución</label>
              <input type="text" name="numero_resolucion" id="rec_resolucion" class="form-control">
            </div>
            <hr class="my-1">
            <div class="col-md-3">
              <label class="form-label">Cumplimiento</label>
              <input type="number" name="puntaje_cumplimiento" id="rec_cumplimiento" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-3">
              <label class="form-label">Puntualidad</label>
              <input type="number" name="puntaje_puntualidad" id="rec_puntualidad" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-3">
              <label class="form-label">Participación</label>
              <input type="number" name="puntaje_participacion" id="rec_participacion" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-3">
              <label class="form-label">Responsabilidad</label>
              <input type="number" name="puntaje_responsabilidad" id="rec_responsabilidad" class="form-control" min="0" max="100">
            </div>
            <div class="col-12">
              <label class="form-label">Nueva foto (opcional)</label>
              <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label">Motivo</label>
              <textarea name="motivo" id="rec_motivo" class="form-control" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.select2-rec').forEach(el =>
    $(el).select2({ dropdownParent: el.closest('.modal'), width: '100%' })
  );

  document.querySelectorAll('.btn-editar-reconocimiento').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = document.getElementById('formEditarRec');
      form.action = '/reconocimientos/' + this.dataset.id;
      document.getElementById('rec_nombre').value          = this.dataset.nombre;
      document.getElementById('rec_cargo').value           = this.dataset.cargo || '';
      document.getElementById('rec_dni').value             = this.dataset.dni || '';
      document.getElementById('rec_correo').value          = this.dataset.correo || '';
      document.getElementById('rec_cumplimiento').value    = this.dataset.cumplimiento;
      document.getElementById('rec_puntualidad').value     = this.dataset.puntualidad;
      document.getElementById('rec_participacion').value   = this.dataset.participacion;
      document.getElementById('rec_responsabilidad').value = this.dataset.responsabilidad;
      document.getElementById('rec_motivo').value          = this.dataset.motivo || '';
      document.getElementById('rec_resolucion').value      = this.dataset.resolucion || '';
      const catEl = document.getElementById('rec_categoria');
      if (catEl) { catEl.value = this.dataset.categoria || ''; }
      new bootstrap.Modal(document.getElementById('modalEditarReconocimiento')).show();
    });
  });

  document.querySelectorAll('.form-eliminar-rec').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({
        title: '¿Eliminar reconocimiento?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ea5455'
      }).then(r => { if (r.isConfirmed) form.submit(); });
    });
  });
});
</script>
@endsection
