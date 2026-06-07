@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Componentes SCI - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
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
    <p class="mb-0 text-muted">Gestión de los componentes del Modelo de Integridad Institucional</p>
  </div>
  @can('componentes.editar')
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoComponente">
    <i class="ti tabler-plus me-1"></i>Nuevo Componente
  </button>
  @endcan
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4">
  <i class="ti tabler-circle-check me-2"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4">
  <i class="ti tabler-alert-circle me-2"></i>{{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4">
  <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Tarjetas de Componentes --}}
<div class="row g-4">
  @forelse($componentes as $comp)
  @php
    $pct      = $comp->actividades_count > 0
                  ? round(($comp->completadas_count / $comp->actividades_count) * 100)
                  : 0;
    $semaforo = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
  @endphp
  <div class="col-md-6 col-xl-4">
    <div class="card h-100 {{ !$comp->activo ? 'opacity-50' : '' }}">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            <div class="badge bg-label-primary rounded p-2">
              <i class="ti {{ $comp->icono ?? 'tabler-point' }} icon-22px"></i>
            </div>
            <span class="badge bg-label-secondary rounded-pill fw-bold">N° {{ $comp->numero }}</span>
          </div>
          <div class="d-flex gap-1">
            @if(!$comp->activo)
            <span class="badge bg-label-danger">Inactivo</span>
            @endif
            @can('componentes.editar')
            <button class="btn btn-icon btn-sm btn-label-primary btn-editar-comp"
              data-id="{{ $comp->id }}"
              data-numero="{{ $comp->numero }}"
              data-nombre="{{ $comp->nombre }}"
              data-icono="{{ $comp->icono ?? '' }}"
              data-tipo="{{ $comp->tipo ?? '' }}"
              data-descripcion="{{ htmlspecialchars($comp->descripcion ?? '') }}"
              data-activo="{{ $comp->activo ? '1' : '0' }}"
              title="Editar">
              <i class="ti tabler-edit"></i>
            </button>
            <form method="POST" action="{{ route('adm-componentes.toggle', $comp) }}" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-icon btn-sm btn-label-{{ $comp->activo ? 'warning' : 'success' }}"
                title="{{ $comp->activo ? 'Desactivar' : 'Activar' }}">
                <i class="ti {{ $comp->activo ? 'tabler-eye-off' : 'tabler-eye' }}"></i>
              </button>
            </form>
            <form method="POST" action="{{ route('adm-componentes.destroy', $comp) }}" class="form-eliminar-comp d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-icon btn-sm btn-label-danger" title="Eliminar"
                {{ $comp->actividades_count > 0 ? 'disabled' : '' }}>
                <i class="ti tabler-trash"></i>
              </button>
            </form>
            @endcan
          </div>
        </div>

        <h6 class="mb-1 fw-semibold">{{ $comp->nombre }}</h6>
        @if($comp->tipo)
        <small class="text-muted d-block mb-2">{{ $comp->tipo }}</small>
        @endif
        @if($comp->descripcion)
        <p class="text-muted small mb-3">{{ Str::limit($comp->descripcion, 100) }}</p>
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

{{-- Modal Nuevo Componente --}}
@can('componentes.editar')
<div class="modal fade" id="modalNuevoComponente" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('adm-componentes.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nuevo Componente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">N° Orden <span class="text-danger">*</span></label>
              <input type="number" name="numero" class="form-control" min="1" required>
            </div>
            <div class="col-md-9">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" placeholder="Ej: Compromiso de Alta Dirección" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Ícono <small class="text-muted">(Tabler Icons)</small></label>
              <input type="text" name="icono" class="form-control" placeholder="tabler-star">
            </div>
            <div class="col-md-6">
              <label class="form-label">Tipo / Categoría</label>
              <input type="text" name="tipo" class="form-control" placeholder="Ej: Integridad">
            </div>
            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción del componente..."></textarea>
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

{{-- Modal Editar Componente --}}
<div class="modal fade" id="modalEditarComponente" tabindex="-1">
  <div class="modal-dialog">
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
              <label class="form-label">N° Orden <span class="text-danger">*</span></label>
              <input type="number" name="numero" id="edit_comp_numero" class="form-control" min="1" required>
            </div>
            <div class="col-md-9">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="nombre" id="edit_comp_nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Ícono <small class="text-muted">(Tabler Icons)</small></label>
              <input type="text" name="icono" id="edit_comp_icono" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Tipo / Categoría</label>
              <input type="text" name="tipo" id="edit_comp_tipo" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" id="edit_comp_descripcion" class="form-control" rows="3"></textarea>
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

  // Poblar modal editar
  document.querySelectorAll('.btn-editar-comp').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = document.getElementById('formEditarComponente');
      form.action = '/administracion/componentes/' + this.dataset.id;

      document.getElementById('edit_comp_numero').value      = this.dataset.numero;
      document.getElementById('edit_comp_nombre').value      = this.dataset.nombre;
      document.getElementById('edit_comp_icono').value       = this.dataset.icono || '';
      document.getElementById('edit_comp_tipo').value        = this.dataset.tipo || '';
      document.getElementById('edit_comp_descripcion').value = this.dataset.descripcion || '';
      document.getElementById('edit_comp_activo').checked    = this.dataset.activo === '1';

      new bootstrap.Modal(document.getElementById('modalEditarComponente')).show();
    });
  });

  // Confirmar eliminar
  document.querySelectorAll('.form-eliminar-comp').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({
        title: '¿Eliminar componente?',
        text: 'Solo se puede eliminar si no tiene actividades asociadas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ea5455',
      }).then(r => { if (r.isConfirmed) form.submit(); });
    });
  });
});
</script>
@endsection
