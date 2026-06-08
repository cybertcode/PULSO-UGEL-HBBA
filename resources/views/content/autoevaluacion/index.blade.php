@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Autoevaluación SCI — PULSO UGEL')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
      <li class="breadcrumb-item active">Autoevaluación SCI</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-1">Autoevaluación del Sistema de Control Interno</h4>
      <p class="text-muted mb-0">Cuestionarios periódicos por componente COSO — Directiva CGR</p>
    </div>
    @can('autoevaluacion.crear')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaAutoev">
      <i class="ti tabler-plus me-1"></i> Nueva Autoevaluación
    </button>
    @endcan
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible mb-4">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  {{-- Estadísticas --}}
  <div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-primary rounded-circle"><i class="ti tabler-clipboard-list fs-4"></i></div>
          <h3 class="mb-0">{{ $stats['total'] }}</h3>
          <small class="text-muted">Total</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-info rounded-circle"><i class="ti tabler-lock-open fs-4"></i></div>
          <h3 class="mb-0">{{ $stats['abiertas'] }}</h3>
          <small class="text-muted">Abiertas</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-success rounded-circle"><i class="ti tabler-lock fs-4"></i></div>
          <h3 class="mb-0">{{ $stats['cerradas'] }}</h3>
          <small class="text-muted">Cerradas</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-warning rounded-circle"><i class="ti tabler-star fs-4"></i></div>
          <h3 class="mb-0">{{ $stats['promedio'] }}</h3>
          <small class="text-muted">Puntaje Promedio</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h5 class="mb-0">Autoevaluaciones Registradas</h5>
      <form method="GET" class="d-flex gap-2 flex-wrap">
        <select name="estado" class="form-select form-select-sm" style="width:140px" onchange="this.form.submit()">
          <option value="">Estado</option>
          <option value="abierta"    {{ request('estado')=='abierta'?'selected':'' }}>Abierta</option>
          <option value="en_proceso" {{ request('estado')=='en_proceso'?'selected':'' }}>En Proceso</option>
          <option value="cerrada"    {{ request('estado')=='cerrada'?'selected':'' }}>Cerrada</option>
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
            <th>Título</th>
            <th>Año / Período</th>
            <th>Estado</th>
            <th>Puntaje</th>
            <th>Respuestas</th>
            <th>Elaborado por</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($autoevaluaciones as $ae)
          @php
            $periodoLabel = match($ae->periodo){
              'I_trimestre'=>'I Trimestre','II_trimestre'=>'II Trimestre',
              'III_trimestre'=>'III Trimestre','IV_trimestre'=>'IV Trimestre',
              'semestral'=>'Semestral','anual'=>'Anual',default=>$ae->periodo
            };
          @endphp
          <tr>
            <td><div class="fw-semibold">{{ $ae->titulo }}</div></td>
            <td>
              <span class="badge bg-label-secondary">{{ $ae->anio }}</span>
              <span class="badge bg-label-info ms-1">{{ $periodoLabel }}</span>
            </td>
            <td><span class="badge bg-{{ $ae->color_estado }}">{{ ucfirst($ae->estado) }}</span></td>
            <td>
              @if($ae->puntaje_total !== null)
              <span class="fw-semibold">{{ $ae->puntaje_total }}</span>
              @else
              <span class="text-muted">—</span>
              @endif
            </td>
            <td><span class="badge bg-label-primary">{{ $ae->respuestas_count }}</span></td>
            <td>{{ $ae->elaboradoPor?->name ?? '—' }}</td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('autoevaluacion.show', $ae) }}" class="btn btn-sm btn-icon btn-outline-info" title="Responder / Ver">
                  <i class="ti tabler-eye"></i>
                </a>
                @if($ae->estado !== 'cerrada')
                @can('autoevaluacion.editar')
                <form method="POST" action="{{ route('autoevaluacion.cerrar', $ae) }}" class="d-inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn btn-sm btn-icon btn-outline-success" title="Cerrar autoevaluación">
                    <i class="ti tabler-lock"></i>
                  </button>
                </form>
                @endcan
                @endif
                @can('autoevaluacion.eliminar')
                <form method="POST" action="{{ route('autoevaluacion.destroy', $ae) }}" class="form-eliminar d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Eliminar"><i class="ti tabler-trash"></i></button>
                </form>
                @endcan
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center py-4 text-muted">No hay autoevaluaciones registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $autoevaluaciones->links() }}</div>
  </div>
</div>

{{-- Modal Nueva Autoevaluación --}}
@can('autoevaluacion.crear')
<div class="modal fade" id="modalNuevaAutoev" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('autoevaluacion.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Autoevaluación SCI</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required placeholder="Ej: Autoevaluación SCI 2026 — I Trimestre">
            </div>
            <div class="col-md-6">
              <label class="form-label">Año <span class="text-danger">*</span></label>
              <select name="anio" class="form-select" required>
                @foreach($anios as $a)
                <option value="{{ $a }}" {{ $a == now()->year ? 'selected':'' }}>{{ $a }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Período <span class="text-danger">*</span></label>
              <select name="periodo" class="form-select" required>
                <option value="I_trimestre">I Trimestre</option>
                <option value="II_trimestre">II Trimestre</option>
                <option value="III_trimestre">III Trimestre</option>
                <option value="IV_trimestre">IV Trimestre</option>
                <option value="semestral">Semestral</option>
                <option value="anual" selected>Anual</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Inicio</label>
              <input type="date" name="fecha_inicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Cierre</label>
              <input type="date" name="fecha_cierre" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Estado <span class="text-danger">*</span></label>
              <select name="estado" class="form-select" required>
                <option value="abierta" selected>Abierta</option>
                <option value="en_proceso">En Proceso</option>
                <option value="cerrada">Cerrada</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Crear Autoevaluación</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endcan
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection
@section('page-script')
<script>
  document.querySelectorAll('.form-eliminar').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      if (typeof Swal !== 'undefined') {
        Swal.fire({ title:'¿Eliminar autoevaluación?', icon:'warning', showCancelButton:true,
          confirmButtonText:'Sí', cancelButtonText:'Cancelar', confirmButtonColor:'#d33'
        }).then(r => { if(r.isConfirmed) form.submit(); });
      } else form.submit();
    });
  });
</script>
@endsection
