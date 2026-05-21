@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Control Interno - PULSO UGEL')

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

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4"><i class="ti tabler-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Control Interno</h4>
    <p class="mb-0 text-muted">Seguimiento de actividades del Sistema de Control Interno</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaActividad">
    <i class="ti tabler-plus me-1"></i>Nueva Actividad
  </button>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
  @foreach([['total','Total','primary','tabler-clipboard-list'],['completadas','Completadas','success','tabler-circle-check'],['en_proceso','En Proceso','warning','tabler-loader'],['vencidas','Vencidas','danger','tabler-alert-triangle']] as [$k,$label,$color,$icon])
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <div class="avatar mx-auto mb-2"><span class="avatar-initial rounded bg-label-{{ $color }}"><i class="ti {{ $icon }}"></i></span></div>
        <h3 class="text-{{ $color }} mb-0">{{ $stats[$k] }}</h3>
        <small class="text-muted">{{ $label }}</small>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Tabla --}}
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Actividades de Control Interno</h5>
    <span class="text-muted small">{{ $actividades->total() }} registros</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover datatables-actividades mb-0">
        <thead>
          <tr>
            <th>Código</th>
            <th>Actividad</th>
            <th>Componente</th>
            <th>Unidad</th>
            <th>Fecha Límite</th>
            <th>Avance</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($actividades as $a)
          @php
            $estadoColor = match($a->estado) {
              'completada' => 'success', 'en_proceso' => 'warning',
              'vencida' => 'danger', 'cancelada' => 'secondary', default => 'info',
            };
            $dias = now()->diffInDays($a->fecha_limite, false);
          @endphp
          <tr>
            <td><small class="text-muted">{{ $a->codigo }}</small></td>
            <td>
              <div class="fw-medium">{{ Str::limit($a->nombre, 45) }}</div>
              @if($a->responsable)<small class="text-muted"><i class="ti tabler-user icon-12px me-1"></i>{{ $a->responsable->name }}</small>@endif
            </td>
            <td><small>{{ $a->componente->nombre ?? '—' }}</small></td>
            <td><small>{{ $a->unidadOrganica->sigla ?? '—' }}</small></td>
            <td>
              <span class="badge bg-label-{{ $dias >= 0 ? ($dias <= 7 ? 'warning' : 'secondary') : 'danger' }}">
                {{ $a->fecha_limite->format('d/m/Y') }}
              </span>
            </td>
            <td style="min-width:120px">
              <div class="d-flex align-items-center gap-1">
                <div class="progress flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-{{ $estadoColor }}" style="width:{{ $a->avance }}%"></div>
                </div>
                <small class="fw-medium">{{ $a->avance }}%</small>
              </div>
            </td>
            <td><span class="badge bg-label-{{ $estadoColor }}">{{ ucfirst(str_replace('_',' ',$a->estado)) }}</span></td>
            <td>
              <div class="d-flex gap-1">
                <button class="btn btn-icon btn-sm btn-label-primary"
                  data-bs-toggle="modal" data-bs-target="#modalEditarActividad"
                  data-id="{{ $a->id }}" data-nombre="{{ $a->nombre }}"
                  data-componente="{{ $a->componente_id }}" data-unidad="{{ $a->unidad_organica_id }}"
                  data-responsable="{{ $a->responsable_id }}" data-fecha="{{ $a->fecha_limite->format('Y-m-d') }}"
                  data-avance="{{ $a->avance }}" data-estado="{{ $a->estado }}"
                  data-prioridad="{{ $a->prioridad }}" data-sgd="{{ $a->numero_sgd }}"
                  data-observaciones="{{ $a->observaciones }}"
                  title="Editar">
                  <i class="ti tabler-edit"></i>
                </button>
                <form method="POST" action="{{ route('sci-control-interno.destroy', $a) }}" class="form-eliminar">
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
            <i class="ti tabler-clipboard-off icon-32px d-block mb-2"></i>No hay actividades registradas
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($actividades->hasPages())
  <div class="card-footer">{{ $actividades->links() }}</div>
  @endif
</div>

{{-- Modal Nueva Actividad --}}
<div class="modal fade" id="modalNuevaActividad" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('sci-control-interno.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nueva Actividad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Nombre de la Actividad <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" placeholder="Descripción de la actividad" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Componente <span class="text-danger">*</span></label>
              <select name="componente_id" class="form-select select2" required>
                <option value="">Seleccionar componente</option>
                @foreach($componentes as $c)
                <option value="{{ $c->id }}">{{ $c->numero }}. {{ $c->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2">
                <option value="">Todas las unidades</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Responsable</label>
              <select name="responsable_id" class="form-select select2">
                <option value="">Sin asignar</option>
                @foreach($responsables as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Fecha Inicio</label>
              <input type="date" name="fecha_inicio" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Fecha Límite <span class="text-danger">*</span></label>
              <input type="date" name="fecha_limite" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Prioridad</label>
              <select name="prioridad" class="form-select">
                <option value="alta">Alta</option>
                <option value="media" selected>Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">N° SGD / Expediente</label>
              <input type="text" name="numero_sgd" class="form-control" placeholder="Ej: SGD-2024-001">
            </div>
            <div class="col-12">
              <label class="form-label">Descripción / Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="3" placeholder="Detalle adicional..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Editar Actividad --}}
<div class="modal fade" id="modalEditarActividad" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="formEditarActividad">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Actividad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Componente <span class="text-danger">*</span></label>
              <select name="componente_id" id="edit_componente" class="form-select select2" required>
                @foreach($componentes as $c)
                <option value="{{ $c->id }}">{{ $c->numero }}. {{ $c->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Unidad Orgánica</label>
              <select name="unidad_organica_id" id="edit_unidad" class="form-select select2">
                <option value="">Todas las unidades</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Responsable</label>
              <select name="responsable_id" id="edit_responsable" class="form-select select2">
                <option value="">Sin asignar</option>
                @foreach($responsables as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Fecha Límite <span class="text-danger">*</span></label>
              <input type="date" name="fecha_limite" id="edit_fecha" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Avance %</label>
              <input type="number" name="avance" id="edit_avance" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-6">
              <label class="form-label">Estado</label>
              <select name="estado" id="edit_estado" class="form-select">
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="completada">Completada</option>
                <option value="vencida">Vencida</option>
                <option value="cancelada">Cancelada</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Prioridad</label>
              <select name="prioridad" id="edit_prioridad" class="form-select">
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">N° SGD</label>
              <input type="text" name="numero_sgd" id="edit_sgd" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Observaciones</label>
              <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="3"></textarea>
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
  // DataTable
  if (document.querySelector('.datatables-actividades tbody tr td[colspan]') === null) {
    new DataTable('.datatables-actividades', {
      responsive: true, pageLength: 15,
      language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
    });
  }

  // Select2
  document.querySelectorAll('.select2').forEach(el => $(el).select2({ dropdownParent: el.closest('.modal') || document.body }));

  // Modal editar — poblar campos
  document.getElementById('modalEditarActividad').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    const id  = btn.dataset.id;
    document.getElementById('formEditarActividad').action = '/control-interno/' + id;
    document.getElementById('edit_nombre').value       = btn.dataset.nombre;
    document.getElementById('edit_fecha').value        = btn.dataset.fecha;
    document.getElementById('edit_avance').value       = btn.dataset.avance;
    document.getElementById('edit_sgd').value          = btn.dataset.sgd || '';
    document.getElementById('edit_observaciones').value= btn.dataset.observaciones || '';
    ['edit_componente','edit_unidad','edit_responsable','edit_estado','edit_prioridad'].forEach(id => {
      const el  = document.getElementById(id);
      const key = id.replace('edit_','');
      const map = { edit_componente: 'componente', edit_unidad: 'unidad', edit_responsable: 'responsable' };
      const val = btn.dataset[map[id] || key];
      if (el && val) { el.value = val; $(el).trigger('change'); }
    });
  });

  // Confirm delete
  document.querySelectorAll('.form-eliminar').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({ title: '¿Eliminar actividad?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ea5455' })
        .then(r => { if (r.isConfirmed) form.submit(); });
    });
  });
});
</script>
@endsection
