@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Actas del Comité SCI — PULSO UGEL')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
      <li class="breadcrumb-item active">Actas del Comité</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-1">Actas del Comité de Control Interno</h4>
      <p class="text-muted mb-0">Registro de sesiones, acuerdos y compromisos del comité SCI</p>
    </div>
    @can('actas.crear')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaActa">
      <i class="ti tabler-plus me-1"></i> Nueva Acta
    </button>
    @endcan
  </div>

  {{-- Estadísticas --}}
  <div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2"><span class="avatar-initial rounded-circle bg-label-primary"><i class="ti tabler-notebook fs-4"></i></span></div>
          <h3 class="mb-0">{{ $stats['total'] }}</h3>
          <small class="text-muted">Total Actas</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2"><span class="avatar-initial rounded-circle bg-label-success"><i class="ti tabler-circle-check fs-4"></i></span></div>
          <h3 class="mb-0">{{ $stats['realizadas'] }}</h3>
          <small class="text-muted">Realizadas</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2"><span class="avatar-initial rounded-circle bg-label-warning"><i class="ti tabler-clock fs-4"></i></span></div>
          <h3 class="mb-0">{{ $stats['convocadas'] }}</h3>
          <small class="text-muted">Convocadas</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2"><span class="avatar-initial rounded-circle bg-label-info"><i class="ti tabler-calendar fs-4"></i></span></div>
          <h3 class="mb-0">{{ $stats['anio_actual'] }}</h3>
          <small class="text-muted">Este año</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h5 class="mb-0">Historial de Actas</h5>
      <form method="GET" class="d-flex gap-2 flex-wrap">
        <select name="estado" class="form-select form-select-sm" style="width:140px" onchange="this.form.submit()">
          <option value="">Estado</option>
          <option value="convocada" {{ request('estado')=='convocada'?'selected':'' }}>Convocada</option>
          <option value="realizada" {{ request('estado')=='realizada'?'selected':'' }}>Realizada</option>
          <option value="cancelada" {{ request('estado')=='cancelada'?'selected':'' }}>Cancelada</option>
        </select>
        <select name="anio" class="form-select form-select-sm" style="width:110px" onchange="this.form.submit()">
          <option value="">Año</option>
          @foreach($anios as $a)
          <option value="{{ $a }}" {{ request('anio')==$a?'selected':'' }}>{{ $a }}</option>
          @endforeach
        </select>
      </form>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>N° Acta</th>
            <th>Título</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Participantes</th>
            <th>Secretario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($actas as $acta)
          <tr>
            <td><span class="badge bg-label-primary">{{ $acta->numero_acta }}</span></td>
            <td>
              <div class="fw-semibold">{{ $acta->titulo }}</div>
              @if($acta->lugar)<small class="text-muted"><i class="ti tabler-map-pin me-1"></i>{{ $acta->lugar }}</small>@endif
            </td>
            <td>
              <div>{{ $acta->fecha_sesion->format('d/m/Y') }}</div>
              @if($acta->hora_inicio)<small class="text-muted">{{ $acta->hora_inicio }}</small>@endif
            </td>
            <td><span class="badge bg-label-{{ $acta->tipo_sesion=='ordinaria' ? 'info' : 'warning' }}">{{ ucfirst($acta->tipo_sesion) }}</span></td>
            <td><span class="badge bg-{{ $acta->color_estado }}">{{ ucfirst($acta->estado) }}</span></td>
            <td><span class="badge bg-label-secondary">{{ $acta->participantes_count }}</span></td>
            <td>{{ $acta->secretario?->name ?? '—' }}</td>
            <td>
              <div class="d-flex gap-1">
                @if($acta->archivo_acta)
                <a href="{{ Storage::url($acta->archivo_acta) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-info" title="Ver acta"><i class="ti tabler-file-download"></i></a>
                @endif
                @can('actas.editar')
                <button class="btn btn-sm btn-icon btn-outline-primary btn-editar-acta"
                  data-acta="{{ json_encode($acta->only(['id','numero_acta','titulo','fecha_sesion','hora_inicio','hora_fin','lugar','tipo_sesion','agenda','desarrollo','acuerdos','compromisos','estado','secretario_id','observaciones'])) }}"
                  title="Editar"><i class="ti tabler-edit"></i></button>
                @endcan
                @can('actas.eliminar')
                <form method="POST" action="{{ route('actas-comite.destroy', $acta) }}" class="form-eliminar d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Eliminar"><i class="ti tabler-trash"></i></button>
                </form>
                @endcan
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center py-4 text-muted">No hay actas registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $actas->links() }}</div>
  </div>
</div>

{{-- Modal Nueva Acta --}}
@can('actas.crear')
<div class="modal fade" id="modalNuevaActa" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <form method="POST" action="{{ route('actas-comite.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Acta del Comité</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">N° de Acta <span class="text-danger">*</span></label>
              <input type="text" name="numero_acta" class="form-control" placeholder="Ej: 001-2026" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Tipo de Sesión <span class="text-danger">*</span></label>
              <select name="tipo_sesion" class="form-select" required>
                <option value="ordinaria">Ordinaria</option>
                <option value="extraordinaria">Extraordinaria</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Fecha <span class="text-danger">*</span></label>
              <input type="date" name="fecha_sesion" class="form-control" required>
            </div>
            <div class="col-md-2">
              <label class="form-label">Hora Inicio</label>
              <input type="time" name="hora_inicio" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Hora Fin</label>
              <input type="time" name="hora_fin" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Estado <span class="text-danger">*</span></label>
              <select name="estado" class="form-select" required>
                <option value="convocada">Convocada</option>
                <option value="realizada">Realizada</option>
                <option value="cancelada">Cancelada</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Lugar</label>
              <input type="text" name="lugar" class="form-control" placeholder="Ej: Sala de reuniones UGEL">
            </div>
            <div class="col-md-6">
              <label class="form-label">Secretario</label>
              <select name="secretario_id" class="form-select select2">
                <option value="">— Sin asignar —</option>
                @foreach($usuarios as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Participantes</label>
              <select name="participantes[]" class="form-select select2" multiple>
                @foreach($usuarios as $u)
                <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->cargo }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Agenda</label>
              <textarea name="agenda" class="form-control" rows="2" placeholder="Puntos de la agenda..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Desarrollo de la Sesión</label>
              <textarea name="desarrollo" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Acuerdos</label>
              <textarea name="acuerdos" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Compromisos</label>
              <textarea name="compromisos" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Archivo del Acta (PDF/Word)</label>
              <input type="file" name="archivo_acta" class="form-control" accept=".pdf,.doc,.docx">
            </div>
            <div class="col-12">
              <label class="form-label">Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Registrar Acta</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endcan
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection
@section('page-script')
<script>
  document.querySelectorAll('.select2').forEach(el => {
    if (window.jQuery) $(el).select2({ dropdownParent: $(el).closest('.modal') });
  });
  document.querySelectorAll('.form-eliminar').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      if (typeof Swal !== 'undefined') {
        Swal.fire({ title:'¿Eliminar acta?', icon:'warning', showCancelButton:true,
          confirmButtonText:'Sí', cancelButtonText:'Cancelar', confirmButtonColor:'#d33'
        }).then(r => { if(r.isConfirmed) form.submit(); });
      } else form.submit();
    });
  });
</script>
@endsection
