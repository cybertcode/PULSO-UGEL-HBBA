@php
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Recomendaciones — PULSO UGEL')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
      <li class="breadcrumb-item active">Recomendaciones</li>
    </ol>
  </nav>

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-1">Recomendaciones e Observaciones</h4>
      <p class="text-muted mb-0">Registro y seguimiento de observaciones institucionales derivadas del SCI</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNueva">
      <i class="ti tabler-plus me-1"></i> Nueva Recomendación
    </button>
  </div>

  {{-- Alertas flash --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert">
      <i class="ti tabler-circle-check me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
      <i class="ti tabler-alert-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- KPIs --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar avatar-lg flex-shrink-0">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="ti tabler-message-report icon-26px"></i>
            </span>
          </div>
          <div>
            <p class="mb-0 text-muted small">Total Registradas</p>
            <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar avatar-lg flex-shrink-0">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="ti tabler-clock icon-26px"></i>
            </span>
          </div>
          <div>
            <p class="mb-0 text-muted small">Pendientes / En Proceso</p>
            <h4 class="mb-0 fw-bold">{{ $stats['pendientes'] }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar avatar-lg flex-shrink-0">
            <span class="avatar-initial rounded bg-label-success">
              <i class="ti tabler-circle-check icon-26px"></i>
            </span>
          </div>
          <div>
            <p class="mb-0 text-muted small">Atendidas</p>
            <h4 class="mb-0 fw-bold">{{ $stats['atendidas'] }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar avatar-lg flex-shrink-0">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="ti tabler-alert-triangle icon-26px"></i>
            </span>
          </div>
          <div>
            <p class="mb-0 text-muted small">Vencidas sin atender</p>
            <h4 class="mb-0 fw-bold">{{ $stats['vencidas'] }}</h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Alerta prioridad alta --}}
  @if($stats['alta_prior'] > 0)
  <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
    <i class="ti tabler-alert-circle me-2 flex-shrink-0"></i>
    <div>
      Hay <strong>{{ $stats['alta_prior'] }}</strong> recomendaci{{ $stats['alta_prior'] == 1 ? 'ón' : 'ones' }}
      de <strong>prioridad alta</strong> pendiente{{ $stats['alta_prior'] != 1 ? 's' : '' }} de atención.
    </div>
  </div>
  @endif

  {{-- Filtros --}}
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('recomendaciones') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Buscar</label>
          <input type="text" name="buscar" class="form-control form-control-sm"
            placeholder="Título..." value="{{ request('buscar') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Tipo</label>
          <select name="tipo" class="form-select form-select-sm">
            <option value="">Todos</option>
            @foreach($tipos as $k => $v)
              <option value="{{ $k }}" {{ request('tipo')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Estado</label>
          <select name="estado" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="pendiente" {{ request('estado')=='pendiente'?'selected':'' }}>Pendiente</option>
            <option value="en_proceso" {{ request('estado')=='en_proceso'?'selected':'' }}>En Proceso</option>
            <option value="atendida" {{ request('estado')=='atendida'?'selected':'' }}>Atendida</option>
            <option value="rechazada" {{ request('estado')=='rechazada'?'selected':'' }}>Rechazada</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Prioridad</label>
          <select name="prioridad" class="form-select form-select-sm">
            <option value="">Todas</option>
            <option value="alta" {{ request('prioridad')=='alta'?'selected':'' }}>Alta</option>
            <option value="media" {{ request('prioridad')=='media'?'selected':'' }}>Media</option>
            <option value="baja" {{ request('prioridad')=='baja'?'selected':'' }}>Baja</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2 align-items-end">
          <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
            <i class="ti tabler-filter me-1"></i>Filtrar
          </button>
          <a href="{{ route('recomendaciones') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti tabler-x"></i>
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- Tabla principal --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Registro de Recomendaciones e Observaciones</h5>
      <small class="text-muted">{{ $recomendaciones->total() }} registros</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Título / SGD</th>
              <th>Tipo</th>
              <th>Unidad</th>
              <th>Responsable</th>
              <th>Prioridad</th>
              <th>Estado</th>
              <th>Origen</th>
              <th>Fecha Límite</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recomendaciones as $rec)
            <tr class="{{ $rec->esta_vencida ? 'table-danger' : '' }}">
              <td>
                <div class="fw-semibold">{{ $rec->titulo }}</div>
                @if($rec->numero_sgd)
                  <small class="text-muted">SGD: {{ $rec->numero_sgd }}</small>
                @endif
                @if($rec->actividad)
                  <br><small class="text-info"><i class="ti tabler-link me-1"></i>{{ \Illuminate\Support\Str::limit($rec->actividad->nombre, 30) }}</small>
                @endif
              </td>
              <td>
                <span class="badge bg-label-{{ $rec->tipo_color }} rounded">{{ $rec->tipo_label }}</span>
              </td>
              <td>
                <small>{{ optional($rec->unidadOrganica)->sigla ?? '—' }}</small>
              </td>
              <td>
                <small>{{ optional($rec->responsable)->name ?? '—' }}</small>
              </td>
              <td>
                <span class="badge bg-label-{{ $rec->prioridad_color }} rounded">
                  {{ ucfirst($rec->prioridad) }}
                </span>
              </td>
              <td>
                <span class="badge bg-label-{{ $rec->estado_color }} rounded">
                  {{ $rec->estado_label }}
                </span>
              </td>
              <td>
                <small class="text-muted">{{ $rec->origen ?? '—' }}</small>
              </td>
              <td>
                @if($rec->fecha_limite)
                  <small class="{{ $rec->esta_vencida ? 'text-danger fw-bold' : 'text-muted' }}">
                    {{ $rec->fecha_limite->format('d/m/Y') }}
                    @if($rec->esta_vencida)
                      <br><span class="badge bg-label-danger rounded" style="font-size:9px">VENCIDA</span>
                    @elseif($rec->dias_restantes !== null && $rec->dias_restantes <= 7)
                      <br><span class="badge bg-label-warning rounded" style="font-size:9px">{{ $rec->dias_restantes }}d</span>
                    @endif
                  </small>
                @else
                  <small class="text-muted">—</small>
                @endif
              </td>
              <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                  @if(!in_array($rec->estado, ['atendida', 'rechazada']))
                  <button class="btn btn-sm btn-icon btn-label-success btn-atender"
                    title="Marcar como atendida"
                    data-id="{{ $rec->id }}"
                    data-titulo="{{ $rec->titulo }}">
                    <i class="ti tabler-circle-check"></i>
                  </button>
                  @endif
                  <button class="btn btn-sm btn-icon btn-label-primary btn-editar"
                    title="Editar"
                    data-id="{{ $rec->id }}"
                    data-titulo="{{ $rec->titulo }}"
                    data-descripcion="{{ $rec->descripcion }}"
                    data-tipo="{{ $rec->tipo }}"
                    data-actividad="{{ $rec->actividad_id }}"
                    data-unidad="{{ $rec->unidad_organica_id }}"
                    data-responsable="{{ $rec->responsable_id }}"
                    data-estado="{{ $rec->estado }}"
                    data-prioridad="{{ $rec->prioridad }}"
                    data-emision="{{ optional($rec->fecha_emision)->format('Y-m-d') }}"
                    data-limite="{{ optional($rec->fecha_limite)->format('Y-m-d') }}"
                    data-atencion="{{ optional($rec->fecha_atencion)->format('Y-m-d') }}"
                    data-sgd="{{ $rec->numero_sgd }}"
                    data-origen="{{ $rec->origen }}"
                    data-observaciones="{{ $rec->observaciones }}">
                    <i class="ti tabler-edit"></i>
                  </button>
                  <button class="btn btn-sm btn-icon btn-label-danger btn-eliminar"
                    title="Eliminar"
                    data-id="{{ $rec->id }}"
                    data-titulo="{{ $rec->titulo }}">
                    <i class="ti tabler-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="9" class="text-center py-5">
                <div class="text-muted">
                  <i class="ti tabler-message-off icon-48px d-block mx-auto mb-3 opacity-25"></i>
                  <p class="mb-1">No se encontraron recomendaciones registradas.</p>
                  <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalNueva">
                    <i class="ti tabler-plus me-1"></i> Registrar primera
                  </button>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($recomendaciones->hasPages())
    <div class="card-footer">
      {{ $recomendaciones->links() }}
    </div>
    @endif
  </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════
     MODAL: Nueva Recomendación
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalNueva" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti tabler-plus me-2 text-primary"></i>Nueva Recomendación / Observación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('recomendaciones.store') }}">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required placeholder="Descripción breve de la recomendación...">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
              <select name="tipo" class="form-select" required>
                @foreach($tipos as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Prioridad <span class="text-danger">*</span></label>
              <select name="prioridad" class="form-select" required>
                <option value="alta">Alta</option>
                <option value="media" selected>Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
              <select name="estado" class="form-select" required>
                <option value="pendiente" selected>Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="atendida">Atendida</option>
                <option value="rechazada">Rechazada</option>
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
            <div class="col-md-6">
              <label class="form-label fw-semibold">Actividad SCI vinculada</label>
              <select name="actividad_id" class="form-select select2-nueva">
                <option value="">Ninguna</option>
                @foreach($actividades as $act)
                  <option value="{{ $act->id }}">{{ \Illuminate\Support\Str::limit($act->nombre, 60) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Origen</label>
              <select name="origen" class="form-select">
                <option value="">Seleccionar...</option>
                @foreach($origenes as $o)
                  <option value="{{ $o }}">{{ $o }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha Emisión</label>
              <input type="date" name="fecha_emision" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha Límite</label>
              <input type="date" name="fecha_limite" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">N° SGD</label>
              <input type="text" name="numero_sgd" class="form-control" placeholder="Ej: 001-2026">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción detallada</label>
              <textarea name="descripcion" class="form-control" rows="3"
                placeholder="Descripción completa de la observación o recomendación..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Observaciones / Acciones tomadas</label>
              <textarea name="observaciones" class="form-control" rows="2"
                placeholder="Observaciones adicionales, acciones implementadas..."></textarea>
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

{{-- ═══════════════════════════════════════════════════════════════
     MODAL: Editar Recomendación
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti tabler-edit me-2 text-primary"></i>Editar Recomendación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formEditar">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" id="edit_titulo" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
              <select name="tipo" id="edit_tipo" class="form-select" required>
                @foreach($tipos as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Prioridad <span class="text-danger">*</span></label>
              <select name="prioridad" id="edit_prioridad" class="form-select" required>
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
              <select name="estado" id="edit_estado" class="form-select" required>
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="atendida">Atendida</option>
                <option value="rechazada">Rechazada</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Unidad Orgánica</label>
              <select name="unidad_organica_id" id="edit_unidad" class="form-select select2-editar">
                <option value="">Sin asignar</option>
                @foreach($unidades as $u)
                  <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Responsable</label>
              <select name="responsable_id" id="edit_responsable" class="form-select select2-editar">
                <option value="">Sin asignar</option>
                @foreach($usuarios as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Actividad SCI vinculada</label>
              <select name="actividad_id" id="edit_actividad" class="form-select select2-editar">
                <option value="">Ninguna</option>
                @foreach($actividades as $act)
                  <option value="{{ $act->id }}">{{ \Illuminate\Support\Str::limit($act->nombre, 60) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Origen</label>
              <select name="origen" id="edit_origen" class="form-select">
                <option value="">Seleccionar...</option>
                @foreach($origenes as $o)
                  <option value="{{ $o }}">{{ $o }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha Emisión</label>
              <input type="date" name="fecha_emision" id="edit_emision" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha Límite</label>
              <input type="date" name="fecha_limite" id="edit_limite" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha Atención</label>
              <input type="date" name="fecha_atencion" id="edit_atencion" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">N° SGD</label>
              <input type="text" name="numero_sgd" id="edit_sgd" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-12">
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

{{-- Form oculto para eliminar --}}
<form id="formEliminar" method="POST" style="display:none">
  @csrf
  @method('DELETE')
</form>

{{-- Form oculto para marcar atendida --}}
<form id="formAtender" method="POST" style="display:none">
  @csrf
  @method('PATCH')
</form>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
<script>
$(function () {
  // Select2
  const s2Nueva  = { dropdownParent: $('#modalNueva'),  width: '100%', placeholder: 'Seleccionar...' };
  const s2Editar = { dropdownParent: $('#modalEditar'), width: '100%', placeholder: 'Seleccionar...' };
  $('.select2-nueva').select2(s2Nueva);
  $('.select2-editar').select2(s2Editar);

  // Marcar como atendida
  $(document).on('click', '.btn-atender', function () {
    const id = $(this).data('id');
    const titulo = $(this).data('titulo');
    Swal.fire({
      title: 'Marcar como atendida',
      html: `¿Confirmar que la recomendación "<strong>${titulo}</strong>" ha sido atendida?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      confirmButtonText: 'Sí, atendida',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (result.isConfirmed) {
        const f = document.getElementById('formAtender');
        f.action = '/recomendaciones/' + id + '/atender';
        f.submit();
      }
    });
  });

  // Editar
  $(document).on('click', '.btn-editar', function () {
    const d = $(this).data();
    $('#formEditar').attr('action', '/recomendaciones/' + d.id);
    $('#edit_titulo').val(d.titulo);
    $('#edit_descripcion').val(d.descripcion);
    $('#edit_tipo').val(d.tipo);
    $('#edit_actividad').val(d.actividad).trigger('change');
    $('#edit_unidad').val(d.unidad).trigger('change');
    $('#edit_responsable').val(d.responsable).trigger('change');
    $('#edit_estado').val(d.estado);
    $('#edit_prioridad').val(d.prioridad);
    $('#edit_emision').val(d.emision);
    $('#edit_limite').val(d.limite);
    $('#edit_atencion').val(d.atencion);
    $('#edit_sgd').val(d.sgd);
    $('#edit_origen').val(d.origen);
    $('#edit_observaciones').val(d.observaciones);
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
  });

  // Eliminar
  $(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    const titulo = $(this).data('titulo');
    Swal.fire({
      title: '¿Eliminar recomendación?',
      html: `<strong>${titulo}</strong><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (result.isConfirmed) {
        const f = document.getElementById('formEliminar');
        f.action = '/recomendaciones/' + id;
        f.submit();
      }
    });
  });
});
</script>
@endsection
