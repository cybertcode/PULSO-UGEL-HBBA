@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Evidencias - PULSO UGEL')

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
    <h4 class="mb-1">Gestión de Evidencias</h4>
    <p class="mb-0 text-muted">Documentos y archivos de respaldo por actividad</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSubirEvidencia">
    <i class="ti tabler-upload me-1"></i> Subir Evidencia
  </button>
</div>

<!-- Filtros -->
<div class="card mb-4">
  <div class="card-body py-3">
    <div class="row g-3">
      <div class="col-md-4">
        <select class="form-select form-select-sm">
          <option value="">Todos los componentes</option>
          <option>Compromiso e Integridad</option>
          <option>Gestión de Riesgos</option>
          <option>Actividades de Control</option>
          <option>Información y Comunicación</option>
          <option>Supervisión</option>
        </select>
      </div>
      <div class="col-md-3">
        <select class="form-select form-select-sm">
          <option value="">Todos los estados</option>
          <option>Validado</option>
          <option>Pendiente</option>
          <option>Rechazado</option>
        </select>
      </div>
      <div class="col-md-3">
        <input type="text" class="form-control form-control-sm" placeholder="N° SGD / Expediente">
      </div>
      <div class="col-md-2">
        <button class="btn btn-sm btn-primary w-100">Filtrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Tabla de evidencias -->
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover datatables-evidencias">
        <thead>
          <tr>
            <th>N° SGD</th>
            <th>Actividad</th>
            <th>Componente</th>
            <th>Archivo</th>
            <th>Subido por</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>SGD-2026-0124</td>
            <td>Plan de capacitación en control interno</td>
            <td>Compromiso e Integridad</td>
            <td><i class="ti tabler-file-type-pdf text-danger me-1"></i>plan_capacitacion.pdf</td>
            <td>María García</td>
            <td>15/05/2026</td>
            <td><span class="badge bg-label-success">Validado</span></td>
            <td>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-secondary btn-sm" title="Descargar">
                <i class="ti tabler-download icon-18px"></i>
              </a>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-secondary btn-sm" title="Ver">
                <i class="ti tabler-eye icon-18px"></i>
              </a>
            </td>
          </tr>
          <tr>
            <td>SGD-2026-0198</td>
            <td>Matriz de riesgos institucional</td>
            <td>Gestión de Riesgos</td>
            <td><i class="ti tabler-file-type-xls text-success me-1"></i>matriz_riesgos_v2.xlsx</td>
            <td>Carlos López</td>
            <td>28/05/2026</td>
            <td><span class="badge bg-label-warning">Pendiente</span></td>
            <td>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-secondary btn-sm" title="Descargar">
                <i class="ti tabler-download icon-18px"></i>
              </a>
              <a href="javascript:void(0)" class="btn btn-icon btn-text-success btn-sm" title="Validar">
                <i class="ti tabler-circle-check icon-18px"></i>
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Subir Evidencia -->
<div class="modal fade" id="modalSubirEvidencia" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Subir Evidencia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label class="form-label">Actividad relacionada</label>
            <select class="form-select">
              <option>Selecciona una actividad...</option>
              <option>Plan de capacitación en control interno</option>
              <option>Matriz de riesgos institucional</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">N° SGD / Expediente</label>
            <input type="text" class="form-control" placeholder="SGD-2026-XXXX">
          </div>
          <div class="mb-3">
            <label class="form-label">Archivo</label>
            <input type="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png">
            <div class="form-text">Formatos permitidos: PDF, Word, Excel, imágenes. Máx. 10MB.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea class="form-control" rows="2" placeholder="Descripción del documento..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Subir Evidencia</button>
      </div>
    </div>
  </div>
</div>

@endsection
