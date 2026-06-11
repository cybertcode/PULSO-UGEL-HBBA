@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Buenas Prácticas — PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('page-style')
<style>
.bp-card { border-radius:14px; border:1px solid rgba(0,0,0,.06); transition:transform .18s,box-shadow .18s; }
.bp-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.09); }
.bp-card.is-vencida { border-left:4px solid #ea5455; }
.bp-propuesta-row { background:rgba(255,159,67,.05); }
.kpi-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
      <li class="breadcrumb-item active">Buenas Prácticas</li>
    </ol>
  </nav>

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-1"><i class="ti tabler-rosette-discount-check me-2 text-primary"></i>Buenas Prácticas</h4>
      <p class="text-muted mb-0">Iniciativas institucionales destacadas · propón, gestiona y da seguimiento</p>
    </div>
    <div class="d-flex gap-2">
      {{-- Cualquier usuario puede proponer --}}
      <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalProponer">
        <i class="ti tabler-send me-1"></i>Proponer práctica
      </button>
      @can('buenas-practicas.ver')
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNueva">
        <i class="ti tabler-plus me-1"></i>Registrar directamente
      </button>
      @endcan
    </div>
  </div>

  {{-- KPIs --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon bg-label-primary"><i class="ti tabler-rosette-discount-check"></i></div>
          <div>
            <div class="text-muted small">Total activas</div>
            <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon bg-label-success"><i class="ti tabler-circle-check"></i></div>
          <div>
            <div class="text-muted small">Completadas</div>
            <div class="fw-bold fs-4">{{ $stats['completadas'] }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="kpi-icon bg-label-warning"><i class="ti tabler-loader"></i></div>
          <div>
            <div class="text-muted small">En implementación</div>
            <div class="fw-bold fs-4">{{ $stats['en_implementacion'] }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          @if($stats['propuestas'] > 0)
          <div class="kpi-icon bg-label-danger"><i class="ti tabler-inbox"></i></div>
          @else
          <div class="kpi-icon bg-label-info"><i class="ti tabler-chart-bar"></i></div>
          @endif
          <div>
            @can('buenas-practicas.ver')
            <div class="text-muted small">Propuestas pendientes</div>
            <div class="fw-bold fs-4 {{ $stats['propuestas'] > 0 ? 'text-danger' : '' }}">{{ $stats['propuestas'] }}</div>
            @else
            <div class="text-muted small">Avance promedio</div>
            <div class="fw-bold fs-4">{{ $stats['promedio_avance'] }}%</div>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabs (gestor ve tab extra de propuestas) --}}
  @can('buenas-practicas.ver')
  <ul class="nav nav-pills mb-3" id="bpTabs">
    <li class="nav-item">
      <a class="nav-link {{ request('tab','todas') !== 'propuestas' ? 'active' : '' }}"
         href="{{ route('buenas-practicas') }}">
        <i class="ti tabler-list me-1"></i>Todas las prácticas
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request('tab') === 'propuestas' ? 'active' : '' }}"
         href="{{ route('buenas-practicas', ['tab'=>'propuestas']) }}">
        <i class="ti tabler-inbox me-1"></i>Propuestas por revisar
        @if($pendientesPropuestas > 0)
          <span class="badge bg-danger ms-1">{{ $pendientesPropuestas }}</span>
        @endif
      </a>
    </li>
  </ul>
  @endcan

  {{-- Filtros --}}
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('buenas-practicas') }}" class="row g-3 align-items-end">
        @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}"> @endif
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Buscar</label>
          <input type="text" name="buscar" class="form-control form-control-sm"
            placeholder="Título..." value="{{ request('buscar') }}">
        </div>
        @if(request('tab') !== 'propuestas')
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Estado</label>
          <select name="estado" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="pendiente" {{ request('estado')=='pendiente'?'selected':'' }}>Pendiente</option>
            <option value="en_implementacion" {{ request('estado')=='en_implementacion'?'selected':'' }}>En Implementación</option>
            <option value="completada" {{ request('estado')=='completada'?'selected':'' }}>Completada</option>
            <option value="suspendida" {{ request('estado')=='suspendida'?'selected':'' }}>Suspendida</option>
          </select>
        </div>
        @endif
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Categoría</label>
          <select name="categoria" class="form-select form-select-sm">
            <option value="">Todas</option>
            @foreach($categorias as $k => $v)
              <option value="{{ $k }}" {{ request('categoria')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Unidad Orgánica</label>
          <select name="unidad" class="form-select form-select-sm">
            <option value="">Todas</option>
            @foreach($unidades as $u)
              <option value="{{ $u->id }}" {{ request('unidad')==$u->id?'selected':'' }}>{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary btn-sm w-100">
            <i class="ti tabler-filter me-1"></i>Filtrar
          </button>
          <a href="{{ route('buenas-practicas', request('tab') ? ['tab'=>request('tab')] : []) }}"
             class="btn btn-outline-secondary btn-sm">
            <i class="ti tabler-x"></i>
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">
        @if(request('tab') === 'propuestas')
          Propuestas por revisar
        @else
          Registro de Buenas Prácticas
        @endif
      </h5>
      <small class="text-muted">{{ $practicas->total() }} registros</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Título</th>
              <th>Categoría</th>
              <th>Unidad</th>
              @if(request('tab') === 'propuestas')
                <th>Propuesto por</th>
                <th>Fecha</th>
                @can('buenas-practicas.ver') <th class="text-center">Acción</th> @endcan
              @else
                <th>Responsable</th>
                <th>Avance</th>
                <th>Estado</th>
                <th>Impacto</th>
                <th>Vence</th>
                @can('buenas-practicas.ver') <th class="text-center">Acciones</th> @endcan
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($practicas as $practica)
            @if(request('tab') === 'propuestas')
            <tr class="bp-propuesta-row">
              <td>
                <div class="fw-semibold">{{ $practica->titulo }}</div>
                @if($practica->descripcion)
                  <small class="text-muted">{{ Str::limit($practica->descripcion, 80) }}</small>
                @endif
              </td>
              <td><span class="badge bg-label-info rounded">{{ $practica->categoria_label }}</span></td>
              <td><small>{{ optional($practica->unidadOrganica)->sigla ?? '—' }}</small></td>
              <td>
                <div class="fw-semibold small">{{ optional($practica->propuestoPor)->name ?? '—' }}</div>
                <small class="text-muted">{{ optional($practica->propuestoPor)->cargo_nombre ?? '' }}</small>
              </td>
              <td><small class="text-muted">{{ $practica->created_at->format('d/m/Y') }}</small></td>
              @can('buenas-practicas.ver')
              <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                  <button class="btn btn-sm btn-success btn-aprobar"
                    title="Aprobar propuesta"
                    data-id="{{ $practica->id }}"
                    data-titulo="{{ $practica->titulo }}"
                    data-descripcion="{{ $practica->descripcion }}"
                    data-unidad="{{ $practica->unidad_organica_id }}">
                    <i class="ti tabler-check me-1"></i>Aprobar
                  </button>
                  <button class="btn btn-sm btn-icon btn-label-danger btn-rechazar"
                    title="Rechazar"
                    data-id="{{ $practica->id }}"
                    data-titulo="{{ $practica->titulo }}">
                    <i class="ti tabler-x"></i>
                  </button>
                </div>
              </td>
              @endcan
            </tr>
            @else
            <tr class="{{ $practica->esta_vencida ? 'table-danger' : '' }}">
              <td>
                <div class="fw-semibold">{{ $practica->titulo }}</div>
                @if($practica->numero_sgd)
                  <small class="text-muted">SGD: {{ $practica->numero_sgd }}</small>
                @endif
              </td>
              <td><span class="badge bg-label-info rounded">{{ $practica->categoria_label }}</span></td>
              <td><small>{{ optional($practica->unidadOrganica)->sigla ?? '—' }}</small></td>
              <td><small>{{ optional($practica->responsable)->name ?? '—' }}</small></td>
              <td style="min-width:120px">
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:6px">
                    <div class="progress-bar bg-{{ $practica->avance >= 100 ? 'success' : ($practica->avance >= 60 ? 'primary' : ($practica->avance >= 30 ? 'warning' : 'danger')) }}"
                      style="width:{{ $practica->avance }}%"></div>
                  </div>
                  <small class="fw-bold text-nowrap">{{ $practica->avance }}%</small>
                </div>
              </td>
              <td>
                <span class="badge bg-label-{{ $practica->estado_color }} rounded">
                  {{ $practica->estado_label }}
                </span>
              </td>
              <td>
                @if($practica->impacto)
                  <span class="badge bg-label-{{ $practica->impacto_color }} rounded">{{ ucfirst($practica->impacto) }}</span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td>
                @if($practica->fecha_termino)
                  <small class="{{ $practica->esta_vencida ? 'text-danger fw-bold' : 'text-muted' }}">
                    {{ $practica->fecha_termino->format('d/m/Y') }}
                  </small>
                @else
                  <small class="text-muted">—</small>
                @endif
              </td>
              @can('buenas-practicas.ver')
              <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                  <button class="btn btn-sm btn-icon btn-label-primary btn-editar"
                    title="Editar"
                    data-id="{{ $practica->id }}"
                    data-titulo="{{ $practica->titulo }}"
                    data-descripcion="{{ $practica->descripcion }}"
                    data-categoria="{{ $practica->categoria }}"
                    data-unidad="{{ $practica->unidad_organica_id }}"
                    data-responsable="{{ $practica->responsable_id }}"
                    data-estado="{{ $practica->estado }}"
                    data-avance="{{ $practica->avance }}"
                    data-inicio="{{ optional($practica->fecha_inicio)->format('Y-m-d') }}"
                    data-termino="{{ optional($practica->fecha_termino)->format('Y-m-d') }}"
                    data-sgd="{{ $practica->numero_sgd }}"
                    data-impacto="{{ $practica->impacto }}"
                    data-evidencias="{{ $practica->evidencias }}"
                    data-observaciones="{{ $practica->observaciones }}">
                    <i class="ti tabler-edit"></i>
                  </button>
                  <button class="btn btn-sm btn-icon btn-label-danger btn-eliminar"
                    title="Eliminar"
                    data-id="{{ $practica->id }}"
                    data-titulo="{{ $practica->titulo }}">
                    <i class="ti tabler-trash"></i>
                  </button>
                </div>
              </td>
              @endcan
            </tr>
            @endif
            @empty
            <tr>
              <td colspan="9" class="text-center py-5">
                <i class="ti tabler-clipboard-x icon-48px d-block mx-auto mb-3 opacity-25"></i>
                <p class="text-muted mb-2">
                  {{ request('tab') === 'propuestas' ? 'No hay propuestas pendientes de revisión.' : 'No se encontraron registros.' }}
                </p>
                @if(request('tab') !== 'propuestas')
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalProponer">
                  <i class="ti tabler-send me-1"></i>Proponer la primera
                </button>
                @endif
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($practicas->hasPages())
    <div class="card-footer">{{ $practicas->links() }}</div>
    @endif
  </div>

</div>{{-- /container --}}

{{-- ══════════════════════════════════════════════════════
     MODAL: Proponer buena práctica (cualquier usuario)
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalProponer" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-label-primary">
        <h5 class="modal-title"><i class="ti tabler-send me-2 text-primary"></i>Proponer Buena Práctica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('buenas-practicas.proponer') }}">
        @csrf
        <div class="modal-body">
          <div class="alert alert-info d-flex align-items-start gap-2 py-2 mb-3" style="font-size:.82rem">
            <i class="ti tabler-info-circle mt-1 flex-shrink-0"></i>
            <span>Tu propuesta será revisada por el responsable. Una vez aprobada aparecerá en el registro.</span>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required
                placeholder="Nombre de la buena práctica que propones">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
              <select name="categoria" class="form-select" required>
                @foreach($categorias as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2-proponer">
                <option value="">Sin asignar</option>
                @foreach($unidades as $u)
                  <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha de inicio estimada</label>
              <input type="date" name="fecha_inicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha de término estimada</label>
              <input type="date" name="fecha_termino" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3"
                placeholder="Explica en qué consiste la iniciativa, su objetivo y alcance..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Evidencias / Referencias</label>
              <textarea name="evidencias" class="form-control" rows="2"
                placeholder="Documentos, experiencias previas, referencias..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i class="ti tabler-send me-1"></i>Enviar propuesta
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@can('buenas-practicas.ver')
{{-- ══════════════════════════════════════════════════════
     MODAL: Registrar directamente (gestor)
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalNueva" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti tabler-plus me-2 text-primary"></i>Registrar Buena Práctica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('buenas-practicas.store') }}">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
              <select name="categoria" class="form-select" required>
                @foreach($categorias as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Impacto</label>
              <select name="impacto" class="form-select">
                <option value="">Seleccionar...</option>
                <option value="alto">Alto</option>
                <option value="medio">Medio</option>
                <option value="bajo">Bajo</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2-nueva">
                <option value="">Sin asignar</option>
                @foreach($unidades as $u)
                  <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Responsable</label>
              <select name="responsable_id" class="form-select select2-nueva">
                <option value="">Sin asignar</option>
                @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
              <select name="estado" class="form-select" required>
                <option value="pendiente">Pendiente</option>
                <option value="en_implementacion" selected>En Implementación</option>
                <option value="completada">Completada</option>
                <option value="suspendida">Suspendida</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Avance (%) <span class="text-danger">*</span></label>
              <input type="number" name="avance" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">N° SGD</label>
              <input type="text" name="numero_sgd" class="form-control" placeholder="Ej: 001-2026">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha Inicio</label>
              <input type="date" name="fecha_inicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha Término</label>
              <input type="date" name="fecha_termino" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Evidencias</label>
              <textarea name="evidencias" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: Editar (gestor)
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti tabler-edit me-2 text-primary"></i>Editar Buena Práctica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formEditar">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" id="edit_titulo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
              <select name="categoria" id="edit_categoria" class="form-select" required>
                @foreach($categorias as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Impacto</label>
              <select name="impacto" id="edit_impacto" class="form-select">
                <option value="">Seleccionar...</option>
                <option value="alto">Alto</option>
                <option value="medio">Medio</option>
                <option value="bajo">Bajo</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" id="edit_unidad" class="form-select select2-edit">
                <option value="">Sin asignar</option>
                @foreach($unidades as $u)
                  <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Responsable</label>
              <select name="responsable_id" id="edit_responsable" class="form-select select2-edit">
                <option value="">Sin asignar</option>
                @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
              <select name="estado" id="edit_estado" class="form-select" required>
                <option value="propuesta">Propuesta</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_implementacion">En Implementación</option>
                <option value="completada">Completada</option>
                <option value="suspendida">Suspendida</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Avance (%)</label>
              <input type="number" name="avance" id="edit_avance" class="form-control" min="0" max="100" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">N° SGD</label>
              <input type="text" name="numero_sgd" id="edit_sgd" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha Inicio</label>
              <input type="date" name="fecha_inicio" id="edit_inicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha Término</label>
              <input type="date" name="fecha_termino" id="edit_termino" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Evidencias</label>
              <textarea name="evidencias" id="edit_evidencias" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: Aprobar propuesta (gestor)
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalAprobar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-label-success">
        <h5 class="modal-title"><i class="ti tabler-check me-2 text-success"></i>Aprobar Propuesta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formAprobar">
        @csrf @method('PATCH')
        <div class="modal-body">
          <p class="text-muted mb-3" id="aprobar_titulo_txt" style="font-size:.85rem"></p>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Estado inicial <span class="text-danger">*</span></label>
              <select name="estado" class="form-select" required>
                <option value="pendiente">Pendiente</option>
                <option value="en_implementacion" selected>En Implementación</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Asignar responsable</label>
              <select name="responsable_id" class="form-select select2-aprobar">
                <option value="">Sin asignar</option>
                @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Impacto</label>
              <select name="impacto" class="form-select">
                <option value="">Seleccionar...</option>
                <option value="alto">Alto</option>
                <option value="medio">Medio</option>
                <option value="bajo">Bajo</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="2"
                placeholder="Comentarios al aprobar..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="ti tabler-check me-1"></i>Confirmar aprobación</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Forms ocultos para eliminar / rechazar --}}
<form id="formEliminar" method="POST" style="display:none">@csrf @method('DELETE')</form>
<form id="formRechazar" method="POST" style="display:none">@csrf @method('PATCH')</form>
@endcan

@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('page-script')
<script>
$(function () {
  // Select2
  $('.select2-proponer').select2({ dropdownParent: $('#modalProponer'), width: '100%', placeholder: 'Seleccionar...' });
  @can('buenas-practicas.ver')
  $('.select2-nueva').select2({ dropdownParent: $('#modalNueva'), width: '100%', placeholder: 'Seleccionar...' });
  $('.select2-edit').select2({ dropdownParent: $('#modalEditar'), width: '100%', placeholder: 'Seleccionar...' });
  $('.select2-aprobar').select2({ dropdownParent: $('#modalAprobar'), width: '100%', placeholder: 'Seleccionar...' });
  @endcan

  // Aprobar propuesta
  $(document).on('click', '.btn-aprobar', function () {
    const d = $(this).data();
    $('#formAprobar').attr('action', '/buenas-practicas/' + d.id + '/aprobar');
    $('#aprobar_titulo_txt').text('Propuesta: ' + d.titulo);
    new bootstrap.Modal(document.getElementById('modalAprobar')).show();
  });

  // Rechazar propuesta
  $(document).on('click', '.btn-rechazar', function () {
    const id = $(this).data('id');
    const titulo = $(this).data('titulo');
    Swal.fire({
      title: '¿Rechazar propuesta?',
      html: `<strong>${titulo}</strong><br><small class="text-muted">La práctica pasará a estado "Suspendida".</small>`,
      input: 'textarea',
      inputPlaceholder: 'Motivo del rechazo (opcional)...',
      inputAttributes: { rows: 3 },
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Sí, rechazar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (result.isConfirmed) {
        const f = document.getElementById('formRechazar');
        f.action = '/buenas-practicas/' + id + '/rechazar';
        // Agrega el campo observaciones
        let obs = f.querySelector('[name="observaciones"]');
        if (!obs) {
          obs = document.createElement('input');
          obs.type = 'hidden';
          obs.name = 'observaciones';
          f.appendChild(obs);
        }
        obs.value = result.value || '';
        f.submit();
      }
    });
  });

  // Editar
  $(document).on('click', '.btn-editar', function () {
    const d = $(this).data();
    $('#formEditar').attr('action', '/buenas-practicas/' + d.id);
    $('#edit_titulo').val(d.titulo);
    $('#edit_descripcion').val(d.descripcion);
    $('#edit_categoria').val(d.categoria);
    $('#edit_unidad').val(d.unidad).trigger('change');
    $('#edit_responsable').val(d.responsable).trigger('change');
    $('#edit_estado').val(d.estado);
    $('#edit_avance').val(d.avance);
    $('#edit_inicio').val(d.inicio);
    $('#edit_termino').val(d.termino);
    $('#edit_sgd').val(d.sgd);
    $('#edit_impacto').val(d.impacto);
    $('#edit_evidencias').val(d.evidencias);
    $('#edit_observaciones').val(d.observaciones);
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
  });

  // Eliminar
  $(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    const titulo = $(this).data('titulo');
    Swal.fire({
      title: '¿Eliminar práctica?',
      html: `<strong>${titulo}</strong><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (result.isConfirmed) {
        const f = document.getElementById('formEliminar');
        f.action = '/buenas-practicas/' + id;
        f.submit();
      }
    });
  });

  // Alertas flash
  @if(session('success'))
    Swal.fire({ icon: 'success', title: 'Éxito', text: @json(session('success')), timer: 3000, showConfirmButton: false });
  @endif
  @if(session('error'))
    Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')) });
  @endif
});
</script>
@endsection
