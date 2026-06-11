@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();

$iconosSCI = [
    'tabler-crown'            => 'Alta Dirección',
    'tabler-shield-check'     => 'Integridad',
    'tabler-chart-pie'        => 'Gestión / Riesgos',
    'tabler-chart-bar'        => 'Seguimiento',
    'tabler-clipboard-list'   => 'Actividades',
    'tabler-alert-triangle'   => 'Alertas',
    'tabler-messages'         => 'Comunicación',
    'tabler-message-circle'   => 'Mensajería',
    'tabler-eye'              => 'Transparencia',
    'tabler-speakerphone'     => 'Denuncias',
    'tabler-activity'         => 'Monitoreo',
    'tabler-user-check'       => 'Encargado',
    'tabler-users'            => 'Equipo',
    'tabler-building'         => 'Institución',
    'tabler-file-certificate' => 'Norma / Política',
    'tabler-scale'            => 'Equidad',
    'tabler-lock'             => 'Seguridad',
    'tabler-target'           => 'Objetivos',
    'tabler-trending-up'      => 'Desempeño',
    'tabler-checkup-list'     => 'Verificación',
    'tabler-puzzle'           => 'Componente',
    'tabler-compass'          => 'Estrategia',
    'tabler-flag'             => 'Meta / Hito',
    'tabler-microscope'       => 'Análisis',
];

$total      = $componentes->count();
$activos    = $componentes->where('activo', true)->count();
$totalActs  = $componentes->sum('actividades_count');
$totalCompl = $componentes->sum('completadas_count');
$pctGlobal  = $totalActs > 0 ? round(($totalCompl / $totalActs) * 100) : 0;
$semGlobal  = $pctGlobal >= 75 ? 'success' : ($pctGlobal >= 50 ? 'warning' : 'danger');
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Componentes SCI - PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('page-style')
<style>
.swal2-container.swal2-top-end {
  top: 1rem !important; right: 1rem !important;
  left: auto !important; padding: 0 !important;
  width: auto !important; background: transparent !important;
}
.swal2-container.swal2-top-end .swal2-popup.pulso-toast {
  display: flex !important; flex-direction: row !important;
  align-items: center !important; gap: .6rem !important;
  padding: .6rem 1rem !important; width: auto !important;
  min-width: 240px !important; max-width: 340px !important;
  border-radius: .5rem !important;
  box-shadow: 0 4px 20px rgba(0,0,0,.15) !important;
  font-size: .875rem !important; margin-bottom: .5rem !important;
}
.swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-title {
  margin: 0 !important; padding: 0 !important;
  font-size: .875rem !important; font-weight: 500 !important;
  line-height: 1.3 !important; flex: 1 !important;
}
.swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-icon {
  width: 1.5rem !important; height: 1.5rem !important;
  min-width: 1.5rem !important; margin: 0 !important;
  border-width: 2px !important; font-size: .5rem !important;
}
.swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-html-container,
.swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-actions,
.swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-close { display: none !important; }
.swal2-container.swal2-top-end .swal2-popup.pulso-toast .swal2-timer-progress-bar-container {
  position: absolute !important; bottom: 0 !important; left: 0 !important;
  right: 0 !important; height: 3px !important;
  border-radius: 0 0 .5rem .5rem !important; overflow: hidden !important;
}
.swal2-container.swal2-top-end.swal2-backdrop-show { background: transparent !important; }

.icon-picker-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: .35rem; }
.icon-picker-btn {
    display: flex; align-items: center; justify-content: center;
    padding: .4rem; border: 2px solid transparent; border-radius: .5rem;
    cursor: pointer; background: var(--bs-body-bg); transition: all .15s;
    color: var(--bs-secondary-color);
}
.icon-picker-btn:hover { border-color: var(--bs-primary); background: var(--bs-primary-bg-subtle); color: var(--bs-primary); }
.icon-picker-btn.selected { border-color: var(--bs-primary); background: var(--bs-primary); color: #fff; }
.icon-picker-btn i { font-size: 1.3rem; }
.comp-card { transition: transform .15s, box-shadow .15s; }
.comp-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }
.comp-icon-wrap { width: 48px; height: 48px; border-radius: .75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
</style>
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Componentes SCI</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1"><i class="ti tabler-layout-grid me-2"></i>Componentes del Sistema de Control Interno</h4>
    <p class="mb-0 text-muted">Catálogo de componentes SCI</p>
  </div>
  @can('componentes.editar')
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoComponente">
    <i class="ti tabler-plus me-1"></i>Nuevo Componente
  </button>
  @endcan
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <div class="comp-icon-wrap bg-label-primary mx-auto mb-2">
          <i class="ti tabler-layout-grid text-primary" style="font-size:1.4rem"></i>
        </div>
        <h3 class="mb-0 fw-bold">{{ $total }}</h3>
        <small class="text-muted">Componentes</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <div class="comp-icon-wrap bg-label-success mx-auto mb-2">
          <i class="ti tabler-circle-check text-success" style="font-size:1.4rem"></i>
        </div>
        <h3 class="mb-0 fw-bold">{{ $activos }}</h3>
        <small class="text-muted">Activos</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <div class="comp-icon-wrap bg-label-info mx-auto mb-2">
          <i class="ti tabler-clipboard-list text-info" style="font-size:1.4rem"></i>
        </div>
        <h3 class="mb-0 fw-bold">{{ $totalActs }}</h3>
        <small class="text-muted">Actividades</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <div class="comp-icon-wrap bg-label-{{ $semGlobal }} mx-auto mb-2">
          <i class="ti tabler-trending-up text-{{ $semGlobal }}" style="font-size:1.4rem"></i>
        </div>
        <h3 class="mb-0 fw-bold text-{{ $semGlobal }}">{{ $pctGlobal }}%</h3>
        <small class="text-muted">Avance global</small>
      </div>
    </div>
  </div>
</div>

<div class="row g-4" id="componentes-grid">
  @forelse($componentes as $comp)
  @php
    $pct      = $comp->actividades_count > 0
                  ? round(($comp->completadas_count / $comp->actividades_count) * 100)
                  : 0;
    $semaforo = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
    $icono    = $comp->icono ?? 'tabler-puzzle';
  @endphp
  <div class="col-md-6 col-xl-4" id="comp-card-{{ $comp->id }}">
    <div class="card h-100 comp-card {{ !$comp->activo ? 'opacity-60' : '' }}">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-3">
          <div class="d-flex align-items-center gap-3">
            <div class="comp-icon-wrap bg-label-primary">
              <i class="ti {{ $icono }} text-primary" style="font-size:1.4rem"></i>
            </div>
            <span class="badge bg-label-secondary rounded-pill fw-bold">N° {{ $comp->numero }}</span>
          </div>
          <div class="d-flex gap-1 align-items-center">
            @if(!$comp->activo)
            <span class="badge bg-label-danger comp-badge-inactivo">Inactivo</span>
            @endif
            @can('componentes.editar')
            <button type="button"
              class="btn btn-icon btn-sm btn-label-primary btn-editar-comp"
              data-id="{{ $comp->id }}"
              data-numero="{{ $comp->numero }}"
              data-nombre="{{ $comp->nombre }}"
              data-icono="{{ $comp->icono ?? 'tabler-puzzle' }}"
              data-descripcion="{{ e($comp->descripcion ?? '') }}"
              data-activo="{{ $comp->activo ? '1' : '0' }}"
              title="Editar">
              <i class="ti tabler-edit"></i>
            </button>
            <button type="button"
              class="btn btn-icon btn-sm btn-toggle-comp btn-label-{{ $comp->activo ? 'warning' : 'success' }}"
              data-id="{{ $comp->id }}"
              data-activo="{{ $comp->activo ? '1' : '0' }}"
              title="{{ $comp->activo ? 'Desactivar' : 'Activar' }}">
              <i class="ti {{ $comp->activo ? 'tabler-eye-off' : 'tabler-eye' }}"></i>
            </button>
            <button type="button"
              class="btn btn-icon btn-sm btn-label-danger btn-eliminar-comp"
              data-id="{{ $comp->id }}"
              data-nombre="{{ $comp->nombre }}"
              {{ $comp->actividades_count > 0 ? 'disabled' : '' }}
              title="Eliminar">
              <i class="ti tabler-trash"></i>
            </button>
            @endcan
          </div>
        </div>

        <h6 class="mb-1 fw-semibold">{{ $comp->nombre }}</h6>
        @if($comp->descripcion)
        <p class="text-muted small mb-3">{{ Str::limit($comp->descripcion, 110) }}</p>
        @endif

        <div class="border-top pt-3 mt-auto">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <small class="text-muted">Avance general</small>
            <small class="fw-bold text-{{ $semaforo }}">{{ $pct }}%</small>
          </div>
          <div class="progress mb-2" style="height:6px">
            <div class="progress-bar bg-{{ $semaforo }} rounded-pill" style="width:{{ $pct }}%"></div>
          </div>
          <div class="d-flex justify-content-between">
            <small class="text-muted"><i class="ti tabler-clipboard-list icon-12px me-1"></i>{{ $comp->actividades_count }} actividades</small>
            <small class="text-success"><i class="ti tabler-circle-check icon-12px me-1"></i>{{ $comp->completadas_count }} completadas</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="card">
      <div class="card-body text-center py-5 text-muted">
        <i class="ti tabler-layout-grid icon-48px d-block mb-3"></i>
        <p class="mb-0">No hay componentes registrados. Crea el primero con el botón superior.</p>
      </div>
    </div>
  </div>
  @endforelse
</div>

@can('componentes.editar')
{{-- Modal Nuevo --}}
<div class="modal fade" id="modalNuevoComponente" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('adm-componentes.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nuevo Componente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @php $nextNum = (\App\Models\Componente::max('numero') ?? 0) + 1; @endphp
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label text-muted small mb-1">N° asignado</label>
              <input type="text" class="form-control form-control-sm bg-light text-center fw-bold text-muted"
                value="{{ $nextNum }}" disabled tabindex="-1">
            </div>
            <div class="col-md-9">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" placeholder="Ej: Compromiso de Alta Dirección" required autofocus>
            </div>
            <div class="col-12">
              <label class="form-label">Ícono</label>
              <input type="hidden" name="icono" id="nuevo_icono_val" value="tabler-puzzle">
              <div class="icon-picker-grid" id="nuevo_icon_picker">
                @foreach($iconosSCI as $cls => $label)
                <button type="button" class="icon-picker-btn {{ $cls === 'tabler-puzzle' ? 'selected' : '' }}"
                  data-icon="{{ $cls }}" data-target="nuevo_icono_val" data-picker="nuevo_icon_picker"
                  title="{{ $label }}">
                  <i class="ti {{ $cls }}"></i>
                </button>
                @endforeach
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2" placeholder="Descripción del componente..."></textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="nuevo_activo" checked>
                <label class="form-check-label" for="nuevo_activo">Activo</label>
              </div>
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

{{-- Modal Editar --}}
<div class="modal fade" id="modalEditarComponente" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="formEditarComponente">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-edit me-2"></i>Editar Componente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label text-muted small mb-1">N° asignado</label>
              <input type="text" id="edit_comp_numero_display"
                class="form-control form-control-sm bg-light text-center fw-bold text-muted"
                disabled tabindex="-1">
            </div>
            <div class="col-md-9">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="edit_comp_nombre" class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label">Ícono</label>
              <input type="hidden" name="icono" id="edit_icono_val" value="tabler-puzzle">
              <div class="icon-picker-grid" id="edit_icon_picker">
                @foreach($iconosSCI as $cls => $label)
                <button type="button" class="icon-picker-btn"
                  data-icon="{{ $cls }}" data-target="edit_icono_val" data-picker="edit_icon_picker"
                  title="{{ $label }}">
                  <i class="ti {{ $cls }}"></i>
                </button>
                @endforeach
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" id="edit_comp_descripcion" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_comp_activo">
                <label class="form-check-label" for="edit_comp_activo">Activo</label>
              </div>
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
@endcan

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  function toast(icon, title, timer) {
    const iconColors = { success:'#28c76f', error:'#ea5455', warning:'#ff9f43', info:'#00cfe8' };
    Swal.fire({
      toast: true, position: 'top-end', icon, title,
      showConfirmButton: false, timer: timer || 2800, timerProgressBar: true,
      customClass: { popup: 'pulso-toast' }, iconColor: iconColors[icon] || iconColors.info,
    });
  }

  @if(session('success')) toast('success', @json(session('success')), 3000); @endif
  @if(session('error'))   toast('error',   @json(session('error')),   4500); @endif

  // Icon Picker
  document.querySelectorAll('.icon-picker-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const picker = document.getElementById(this.dataset.picker);
      picker.querySelectorAll('.icon-picker-btn').forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
      document.getElementById(this.dataset.target).value = this.dataset.icon;
    });
  });

  // Modal Editar
  document.querySelectorAll('.btn-editar-comp').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = document.getElementById('formEditarComponente');
      form.action = '/administracion/componentes/' + this.dataset.id;
      document.getElementById('edit_comp_numero_display').value = 'N° ' + this.dataset.numero;
      document.getElementById('edit_comp_nombre').value         = this.dataset.nombre;
      document.getElementById('edit_comp_descripcion').value    = this.dataset.descripcion || '';
      document.getElementById('edit_comp_activo').checked       = this.dataset.activo === '1';

      const icono = this.dataset.icono || 'tabler-puzzle';
      document.getElementById('edit_icono_val').value = icono;
      document.querySelectorAll('#edit_icon_picker .icon-picker-btn').forEach(b => {
        b.classList.toggle('selected', b.dataset.icon === icono);
      });

      new bootstrap.Modal(document.getElementById('modalEditarComponente')).show();
    });
  });

  // Toggle AJAX
  document.querySelectorAll('.btn-toggle-comp').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      const id = this.dataset.id;
      const btnEl = this;
      fetch('/administracion/componentes/' + id + '/toggle', {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
      })
      .then(r => r.json())
      .then(data => {
        const activo = data.activo;
        btnEl.dataset.activo = activo ? '1' : '0';
        btnEl.classList.remove('btn-label-warning', 'btn-label-success');
        btnEl.classList.add(activo ? 'btn-label-warning' : 'btn-label-success');
        btnEl.title = activo ? 'Desactivar' : 'Activar';
        btnEl.querySelector('i').className = 'ti ' + (activo ? 'tabler-eye-off' : 'tabler-eye');
        document.querySelector('#comp-card-' + id + ' .card').classList.toggle('opacity-60', !activo);
        const badge = document.querySelector('#comp-card-' + id + ' .comp-badge-inactivo');
        if (!activo && !badge) {
          const nb = document.createElement('span');
          nb.className = 'badge bg-label-danger comp-badge-inactivo';
          nb.textContent = 'Inactivo';
          btnEl.insertAdjacentElement('beforebegin', nb);
        } else if (activo && badge) {
          badge.remove();
        }
        toast(activo ? 'success' : 'warning', activo ? 'Componente activado' : 'Componente desactivado');
      })
      .catch(() => toast('error', 'Error al cambiar estado', 4000));
    });
  });

  // Eliminar
  document.querySelectorAll('.btn-eliminar-comp').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      const id = this.dataset.id;
      const nombre = this.dataset.nombre;
      Swal.fire({
        title: '¿Eliminar componente?',
        html: `<strong>${nombre}</strong><br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="ti tabler-trash me-1"></i>Sí, eliminar',
        cancelButtonText: 'Cancelar',
      }).then(r => {
        if (!r.isConfirmed) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/administracion/componentes/' + id;
        form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}"><input type="hidden" name="_method" value="DELETE">`;
        document.body.appendChild(form);
        form.submit();
      });
    });
  });

});
</script>
@endsection
