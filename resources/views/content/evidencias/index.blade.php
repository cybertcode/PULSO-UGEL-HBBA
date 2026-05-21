@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Evidencias - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
       'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
       'resources/assets/vendor/libs/select2/select2.scss',
       'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
       'resources/assets/vendor/libs/select2/select2.js',
       'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Evidencias / SGD</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Gestión de Evidencias</h4>
    <p class="mb-0 text-muted">Documentos y archivos de respaldo por actividad</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSubirEvidencia">
    <i class="ti tabler-upload me-1"></i>Subir Evidencia
  </button>
</div>

{{-- Stats (estilo cards-statistics full-version) --}}
<div class="row g-6 mb-6">
  @php
  $kpis = [
    ['k'=>'total',     'label'=>'Total SGD',   'sub'=>'Documentos cargados',  'color'=>'primary', 'icon'=>'tabler-files'],
    ['k'=>'validadas', 'label'=>'Validadas',   'sub'=>'Aprobadas por revisión','color'=>'success', 'icon'=>'tabler-file-check'],
    ['k'=>'pendientes','label'=>'Pendientes',  'sub'=>'En revisión',           'color'=>'warning', 'icon'=>'tabler-file-time'],
    ['k'=>'rechazadas','label'=>'Rechazadas',  'sub'=>'Requieren corrección',  'color'=>'danger',  'icon'=>'tabler-file-x'],
  ];
  @endphp
  @foreach($kpis as $kp)
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-4">
          <div class="badge rounded bg-label-{{ $kp['color'] }} p-2">
            <i class="icon-base ti {{ $kp['icon'] }} icon-26px"></i>
          </div>
          <span class="badge bg-label-{{ $kp['color'] }} rounded-pill">SGD</span>
        </div>
        <h4 class="mb-1 text-{{ $kp['color'] }}">{{ $stats[$kp['k']] }}</h4>
        <p class="mb-0 fw-medium">{{ $kp['label'] }}</p>
        <small class="text-body-secondary">{{ $kp['sub'] }}</small>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Filtros --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('sci-evidencias') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label form-label-sm">Componente</label>
          <select name="componente_id" class="form-select form-select-sm select2">
            <option value="">Todos los componentes</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ request('componente_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label form-label-sm">Estado</label>
          <select name="estado" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="validado"  {{ request('estado') === 'validado'  ? 'selected' : '' }}>Validado</option>
            <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="rechazado" {{ request('estado') === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label form-label-sm">Buscar SGD / Título</label>
          <input type="text" name="buscar" class="form-control form-control-sm" value="{{ request('buscar') }}" placeholder="N° SGD o título">
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="ti tabler-filter me-1"></i>Filtrar</button>
          <a href="{{ route('sci-evidencias') }}" class="btn btn-sm btn-label-secondary"><i class="ti tabler-x"></i></a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Tabla --}}
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Título / N° SGD</th>
            <th>Actividad</th>
            <th>Subido por</th>
            <th>Archivo</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($evidencias as $ev)
          @php
            $ec = match($ev->estado) { 'validado' => 'success', 'rechazado' => 'danger', default => 'warning' };
          @endphp
          <tr>
            <td>
              <div class="fw-medium">{{ $ev->titulo }}</div>
              @if($ev->numero_sgd)<small class="text-muted"><i class="ti tabler-file-description icon-12px me-1"></i>{{ $ev->numero_sgd }}</small>@endif
            </td>
            <td><small>{{ Str::limit($ev->actividad->nombre ?? '—', 40) }}</small></td>
            <td><small>{{ $ev->subidoPor->name ?? '—' }}</small></td>
            <td>
              <div class="d-flex align-items-center gap-1">
                <i class="ti tabler-file icon-16px text-muted"></i>
                <small>{{ $ev->archivo_nombre }}</small>
              </div>
              <small class="text-muted">{{ $ev->tamanio_formateado }}</small>
            </td>
            <td>
              <span class="badge bg-label-{{ $ec }}">{{ ucfirst($ev->estado) }}</span>
              @if($ev->estado === 'rechazado' && $ev->motivo_rechazo)
              <br><small class="text-danger">{{ Str::limit($ev->motivo_rechazo, 30) }}</small>
              @endif
            </td>
            <td><small>{{ $ev->created_at->format('d/m/Y') }}</small></td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ Storage::url($ev->archivo_ruta) }}" target="_blank" class="btn btn-icon btn-sm btn-label-info" title="Descargar">
                  <i class="ti tabler-download"></i>
                </a>
                @if($ev->estado === 'pendiente')
                <button class="btn btn-icon btn-sm btn-label-success btn-validar"
                  data-id="{{ $ev->id }}" data-url="{{ route('sci-evidencias.validar', $ev) }}" title="Validar">
                  <i class="ti tabler-check"></i>
                </button>
                <button class="btn btn-icon btn-sm btn-label-danger btn-rechazar"
                  data-id="{{ $ev->id }}" data-url="{{ route('sci-evidencias.validar', $ev) }}" title="Rechazar">
                  <i class="ti tabler-x"></i>
                </button>
                @endif
                <form method="POST" action="{{ route('sci-evidencias.destroy', $ev) }}" class="form-eliminar-ev">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-icon btn-sm btn-label-secondary" title="Eliminar">
                    <i class="ti tabler-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-5">
            <i class="ti tabler-file-off icon-32px d-block mb-2"></i>No hay evidencias registradas
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($evidencias->hasPages())
  <div class="card-footer">{{ $evidencias->links() }}</div>
  @endif
</div>

{{-- Modal Subir Evidencia --}}
<div class="modal fade" id="modalSubirEvidencia" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('sci-evidencias.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-upload me-2"></i>Subir Evidencia</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Actividad <span class="text-danger">*</span></label>
            <select name="actividad_id" class="form-select select2" required>
              <option value="">Seleccionar actividad</option>
              @foreach($actividades as $a)
              <option value="{{ $a->id }}">{{ $a->codigo }} — {{ Str::limit($a->nombre, 50) }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Título <span class="text-danger">*</span></label>
            <input type="text" name="titulo" class="form-control" placeholder="Título del documento" required>
          </div>
          <div class="mb-3">
            <label class="form-label">N° SGD / Expediente</label>
            <input type="text" name="numero_sgd" class="form-control" placeholder="Ej: SGD-2024-001">
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Archivo <span class="text-danger">*</span></label>
            <input type="file" name="archivo" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" required>
            <div class="form-text">PDF, Word, Excel, Imagen. Máximo 10MB.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-upload me-1"></i>Subir</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Forms ocultos para validar/rechazar --}}
<form method="POST" id="formValidar" style="display:none">@csrf @method('PATCH')<input type="hidden" name="accion" value="validado"></form>
<form method="POST" id="formRechazar" style="display:none">@csrf @method('PATCH')<input type="hidden" name="accion" value="rechazado"><input type="hidden" name="motivo_rechazo" id="motivoInput"></form>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.select2').forEach(el => $(el).select2({ dropdownParent: el.closest('.modal') || document.body }));

  document.querySelectorAll('.btn-validar').forEach(btn => {
    btn.addEventListener('click', function () {
      Swal.fire({ title: '¿Validar evidencia?', icon: 'question', showCancelButton: true,
        confirmButtonText: 'Sí, validar', cancelButtonText: 'Cancelar', confirmButtonColor: '#28c76f' })
        .then(r => { if (r.isConfirmed) { document.getElementById('formValidar').action = this.dataset.url; document.getElementById('formValidar').submit(); } });
    });
  });

  document.querySelectorAll('.btn-rechazar').forEach(btn => {
    btn.addEventListener('click', function () {
      const url = this.dataset.url;
      Swal.fire({ title: 'Rechazar evidencia', input: 'textarea', inputLabel: 'Motivo del rechazo',
        inputPlaceholder: 'Explica el motivo...', showCancelButton: true,
        confirmButtonText: 'Rechazar', cancelButtonText: 'Cancelar', confirmButtonColor: '#ea5455',
        inputValidator: v => !v && 'El motivo es requerido' })
        .then(r => { if (r.isConfirmed) { document.getElementById('motivoInput').value = r.value; document.getElementById('formRechazar').action = url; document.getElementById('formRechazar').submit(); } });
    });
  });

  document.querySelectorAll('.form-eliminar-ev').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      Swal.fire({ title: '¿Eliminar evidencia?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', confirmButtonColor: '#ea5455' })
        .then(r => { if (r.isConfirmed) form.submit(); });
    });
  });
});
</script>
@endsection

