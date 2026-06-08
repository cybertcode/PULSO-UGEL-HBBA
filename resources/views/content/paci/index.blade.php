@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Plan Anual de Control Interno (PACI) — PULSO UGEL')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
      <li class="breadcrumb-item active">PACI</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-1">Plan Anual de Control Interno</h4>
      <p class="text-muted mb-0">Registro y seguimiento del PACI — UGEL Huacaybamba</p>
    </div>
    @can('paci.crear')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoPaci">
      <i class="ti tabler-plus me-1"></i> Nuevo PACI
    </button>
    @endcan
  </div>

  {{-- Alertas --}}
  @if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible mb-4" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- Estadísticas --}}
  <div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-primary rounded-circle">
            <i class="ti tabler-file-description fs-4"></i>
          </div>
          <h3 class="mb-0">{{ $stats['total'] }}</h3>
          <small class="text-muted">Total PACI</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-success rounded-circle">
            <i class="ti tabler-player-play fs-4"></i>
          </div>
          <h3 class="mb-0">{{ $stats['en_ejecucion'] }}</h3>
          <small class="text-muted">En Ejecución</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-info rounded-circle">
            <i class="ti tabler-circle-check fs-4"></i>
          </div>
          <h3 class="mb-0">{{ $stats['aprobados'] }}</h3>
          <small class="text-muted">Aprobados</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <div class="avatar avatar-md mx-auto mb-2 bg-label-warning rounded-circle">
            <i class="ti tabler-chart-bar fs-4"></i>
          </div>
          <h3 class="mb-0">{{ $stats['promedio'] }}%</h3>
          <small class="text-muted">Avance Promedio</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h5 class="mb-0">Planes Registrados</h5>
      <form method="GET" class="d-flex gap-2 flex-wrap">
        <select name="estado" class="form-select form-select-sm" style="width:160px" onchange="this.form.submit()">
          <option value="">Todos los estados</option>
          <option value="borrador" {{ request('estado')=='borrador' ? 'selected' : '' }}>Borrador</option>
          <option value="aprobado" {{ request('estado')=='aprobado' ? 'selected' : '' }}>Aprobado</option>
          <option value="en_ejecucion" {{ request('estado')=='en_ejecucion' ? 'selected' : '' }}>En Ejecución</option>
          <option value="cerrado" {{ request('estado')=='cerrado' ? 'selected' : '' }}>Cerrado</option>
        </select>
      </form>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Título / Resolución</th>
            <th>Año</th>
            <th>Estado</th>
            <th>Avance</th>
            <th>Actividades</th>
            <th>Creado por</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pacis as $paci)
          <tr>
            <td>
              <div class="fw-semibold">{{ $paci->titulo }}</div>
              @if($paci->numero_resolucion)
              <small class="text-muted">Res. {{ $paci->numero_resolucion }}</small>
              @endif
            </td>
            <td><span class="badge bg-label-secondary">{{ $paci->anio }}</span></td>
            <td><span class="badge bg-{{ $paci->color_estado }}">{{ $paci->etiqueta_estado }}</span></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-primary" style="width:{{ $paci->avance }}%"></div>
                </div>
                <small>{{ $paci->avance }}%</small>
              </div>
            </td>
            <td><span class="badge bg-label-primary">{{ $paci->actividades_count }}</span></td>
            <td>{{ $paci->creadoPor?->name ?? '—' }}</td>
            <td>
              <div class="d-flex gap-1">
                @if($paci->archivo)
                <a href="{{ Storage::url($paci->archivo) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-info" title="Ver archivo">
                  <i class="ti tabler-file-download"></i>
                </a>
                @endif
                @can('paci.editar')
                <button class="btn btn-sm btn-icon btn-outline-primary btn-editar-paci"
                  data-paci="{{ json_encode($paci->only(['id','titulo','anio','descripcion','numero_resolucion','fecha_aprobacion','fecha_inicio','fecha_fin','estado','avance','observaciones'])) }}"
                  title="Editar">
                  <i class="ti tabler-edit"></i>
                </button>
                @endcan
                @can('paci.eliminar')
                <form method="POST" action="{{ route('paci.destroy', $paci) }}" class="form-eliminar d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Eliminar">
                    <i class="ti tabler-trash"></i>
                  </button>
                </form>
                @endcan
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center py-4 text-muted">No hay planes registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $pacis->links() }}</div>
  </div>
</div>

{{-- Modal Nuevo PACI --}}
@can('paci.crear')
<div class="modal fade" id="modalNuevoPaci" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('paci.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Plan Anual de Control Interno</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required value="{{ old('titulo') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Año <span class="text-danger">*</span></label>
              <select name="anio" class="form-select" required>
                @foreach($anios as $a)
                <option value="{{ $a }}" {{ $a == now()->year ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">N° Resolución</label>
              <input type="text" name="numero_resolucion" class="form-control" placeholder="Ej: RD-001-2026">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Aprobación</label>
              <input type="date" name="fecha_aprobacion" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Inicio</label>
              <input type="date" name="fecha_inicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Fin</label>
              <input type="date" name="fecha_fin" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Estado <span class="text-danger">*</span></label>
              <select name="estado" class="form-select" required>
                <option value="borrador">Borrador</option>
                <option value="aprobado">Aprobado</option>
                <option value="en_ejecucion">En Ejecución</option>
                <option value="cerrado">Cerrado</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Avance (%) <span class="text-danger">*</span></label>
              <input type="number" name="avance" class="form-control" min="0" max="100" value="0" required>
            </div>
            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Actividades vinculadas</label>
              <select name="actividades[]" class="form-select select2" multiple>
                @foreach($actividades as $act)
                <option value="{{ $act->id }}">{{ $act->titulo }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Archivo (PDF/Word)</label>
              <input type="file" name="archivo" class="form-control" accept=".pdf,.doc,.docx">
            </div>
            <div class="col-12">
              <label class="form-label">Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Registrar PACI</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endcan

{{-- Modal Editar PACI --}}
@can('paci.editar')
<div class="modal fade" id="modalEditarPaci" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" id="formEditarPaci" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar PACI</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" id="edit_titulo" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Año <span class="text-danger">*</span></label>
              <select name="anio" id="edit_anio" class="form-select" required>
                @foreach($anios as $a)
                <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">N° Resolución</label>
              <input type="text" name="numero_resolucion" id="edit_numero_resolucion" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Aprobación</label>
              <input type="date" name="fecha_aprobacion" id="edit_fecha_aprobacion" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Inicio</label>
              <input type="date" name="fecha_inicio" id="edit_fecha_inicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Fin</label>
              <input type="date" name="fecha_fin" id="edit_fecha_fin" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Estado <span class="text-danger">*</span></label>
              <select name="estado" id="edit_estado" class="form-select" required>
                <option value="borrador">Borrador</option>
                <option value="aprobado">Aprobado</option>
                <option value="en_ejecucion">En Ejecución</option>
                <option value="cerrado">Cerrado</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Avance (%)</label>
              <input type="number" name="avance" id="edit_avance" class="form-control" min="0" max="100">
            </div>
            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Observaciones</label>
              <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Nuevo archivo (opcional)</label>
              <input type="file" name="archivo" class="form-control" accept=".pdf,.doc,.docx">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
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
  // Select2
  document.querySelectorAll('.select2').forEach(el => {
    if (window.jQuery) $(el).select2({ dropdownParent: $(el).closest('.modal') });
  });

  // Botón editar
  document.querySelectorAll('.btn-editar-paci').forEach(btn => {
    btn.addEventListener('click', function() {
      const d = JSON.parse(this.dataset.paci);
      document.getElementById('formEditarPaci').action = `/paci/${d.id}`;
      ['titulo','anio','numero_resolucion','estado','avance','descripcion','observaciones'].forEach(k => {
        const el = document.getElementById('edit_' + k);
        if (el) el.value = d[k] ?? '';
      });
      ['fecha_aprobacion','fecha_inicio','fecha_fin'].forEach(k => {
        const el = document.getElementById('edit_' + k);
        if (el) el.value = d[k] ? d[k].substring(0,10) : '';
      });
      new bootstrap.Modal(document.getElementById('modalEditarPaci')).show();
    });
  });

  // Confirm eliminar
  document.querySelectorAll('.form-eliminar').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      if (typeof Swal !== 'undefined') {
        Swal.fire({ title:'¿Eliminar PACI?', text:'Esta acción no se puede deshacer.', icon:'warning',
          showCancelButton:true, confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar',
          confirmButtonColor:'#d33'
        }).then(r => { if(r.isConfirmed) form.submit(); });
      } else { form.submit(); }
    });
  });

  @if(session('success') || session('error'))
  const modal = document.getElementById('modalNuevoPaci');
  if (modal) bootstrap.Modal.getInstance(modal)?.hide();
  @endif
</script>
@endsection
