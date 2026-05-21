@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Control Interno - PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
       'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Control Interno</h4>
    <p class="mb-0 text-muted">Seguimiento de actividades del Sistema de Control Interno</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaActividad">
    <i class="ti tabler-plus me-1"></i> Nueva Actividad
  </button>
</div>

<!-- Stats rápidas -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <h3 class="text-primary mb-1">128</h3>
        <small class="text-muted">Total</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <h3 class="text-success mb-1">84</h3>
        <small class="text-muted">Completadas</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <h3 class="text-warning mb-1">32</h3>
        <small class="text-muted">En Proceso</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <h3 class="text-danger mb-1">12</h3>
        <small class="text-muted">Vencidas</small>
      </div>
    </div>
  </div>
</div>

<!-- Tabla de actividades -->
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Actividades de Control Interno</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover datatables-actividades">
        <thead>
          <tr>
            <th>Actividad</th>
            <th>Componente</th>
            <th>Responsable</th>
            <th>Fecha Límite</th>
            <th>Avance</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Elaborar matriz de riesgos institucional</td>
            <td>Gestión de Riesgos</td>
            <td>Área de Planificación</td>
            <td>30/06/2026</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px;min-width:80px">
                  <div class="progress-bar bg-success" style="width:75%"></div>
                </div>
                <small>75%</small>
              </div>
            </td>
            <td><span class="badge bg-label-warning">En Proceso</span></td>
            <td>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-secondary btn-sm">
                <i class="ti tabler-edit icon-18px"></i>
              </a>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-secondary btn-sm">
                <i class="ti tabler-eye icon-18px"></i>
              </a>
            </td>
          </tr>
          <tr>
            <td>Plan de capacitación en control interno</td>
            <td>Compromiso e Integridad</td>
            <td>Recursos Humanos</td>
            <td>15/05/2026</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px;min-width:80px">
                  <div class="progress-bar bg-success" style="width:100%"></div>
                </div>
                <small>100%</small>
              </div>
            </td>
            <td><span class="badge bg-label-success">Completada</span></td>
            <td>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-secondary btn-sm">
                <i class="ti tabler-edit icon-18px"></i>
              </a>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-secondary btn-sm">
                <i class="ti tabler-eye icon-18px"></i>
              </a>
            </td>
          </tr>
          <tr>
            <td>Implementar mecanismos de supervisión</td>
            <td>Supervisión</td>
            <td>Dirección</td>
            <td>10/04/2026</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px;min-width:80px">
                  <div class="progress-bar bg-danger" style="width:20%"></div>
                </div>
                <small>20%</small>
              </div>
            </td>
            <td><span class="badge bg-label-danger">Vencida</span></td>
            <td>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-secondary btn-sm">
                <i class="ti tabler-edit icon-18px"></i>
              </a>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-secondary btn-sm">
                <i class="ti tabler-eye icon-18px"></i>
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Nueva Actividad -->
<div class="modal fade" id="modalNuevaActividad" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nueva Actividad de Control Interno</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Nombre de la Actividad</label>
              <input type="text" class="form-control" placeholder="Descripción de la actividad">
            </div>
            <div class="col-md-6">
              <label class="form-label">Componente</label>
              <select class="form-select">
                <option>Compromiso e Integridad</option>
                <option>Gestión de Riesgos</option>
                <option>Actividades de Control</option>
                <option>Información y Comunicación</option>
                <option>Supervisión</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Responsable</label>
              <input type="text" class="form-control" placeholder="Área o persona responsable">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha Límite</label>
              <input type="date" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">N° SGD / Documento</label>
              <input type="text" class="form-control" placeholder="Número de expediente">
            </div>
            <div class="col-12">
              <label class="form-label">Observaciones</label>
              <textarea class="form-control" rows="3" placeholder="Detalles adicionales..."></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Guardar Actividad</button>
      </div>
    </div>
  </div>
</div>

@endsection
