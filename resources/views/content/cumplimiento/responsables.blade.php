@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Cumplimiento por Responsable — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
       'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
       'resources/assets/vendor/libs/select2/select2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
       'resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cumplimiento.panel') }}">Panel SCI</a></li>
    <li class="breadcrumb-item active">Cumplimiento por Responsable</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Cumplimiento por Responsable</h4>
    <p class="mb-0 text-muted">¿Quién está cumpliendo con plazos y evidencias?</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('cumplimiento.panel') }}" class="btn btn-sm btn-label-secondary">
      <i class="ti tabler-layout-dashboard me-1"></i>Panel SCI
    </a>
    <a href="{{ route('cumplimiento.sin-evidencia') }}" class="btn btn-sm btn-label-warning">
      <i class="ti tabler-file-off me-1"></i>Sin Evidencia
    </a>
    <div class="dropdown">
      <button class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown">
        <i class="ti tabler-download me-1"></i>Exportar
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ route('cumplimiento.exportar', array_merge(request()->query(), ['formato'=>'excel'])) }}">
          <i class="ti tabler-file-spreadsheet me-2 text-success"></i>Excel (.xlsx)
        </a></li>
        <li><a class="dropdown-item" href="{{ route('cumplimiento.exportar', array_merge(request()->query(), ['formato'=>'pdf'])) }}">
          <i class="ti tabler-file-type-pdf me-2 text-danger"></i>PDF
        </a></li>
      </ul>
    </div>
  </div>
</div>

{{-- Resumen global --}}
<div class="row g-4 mb-4">
  <div class="col-6 col-md-3">
    <div class="card">
      <div class="card-body text-center py-3">
        <h3 class="mb-1 text-primary">{{ $totales['responsables'] }}</h3>
        <p class="mb-0 text-muted small">Responsables activos</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-danger border-opacity-25">
      <div class="card-body text-center py-3">
        <h3 class="mb-1 text-danger">{{ $totales['en_riesgo'] }}</h3>
        <p class="mb-0 text-muted small">En riesgo (< 50%)</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-warning border-opacity-25">
      <div class="card-body text-center py-3">
        <h3 class="mb-1 text-warning">{{ $totales['sin_evidencia'] }}</h3>
        <p class="mb-0 text-muted small">Actividades sin evidencia</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-danger border-opacity-25">
      <div class="card-body text-center py-3">
        <h3 class="mb-1 text-danger">{{ $totales['vencidas_total'] }}</h3>
        <p class="mb-0 text-muted small">Actividades vencidas</p>
      </div>
    </div>
  </div>
</div>

{{-- Filtros --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('cumplimiento.responsables') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label form-label-sm">Unidad Orgánica</label>
          <select name="unidad_organica_id" class="form-select form-select-sm select2">
            <option value="">Todas las unidades</option>
            @foreach($unidades as $u)
            <option value="{{ $u->id }}" {{ $unidadId == $u->id ? 'selected' : '' }}>{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label form-label-sm">Componente SCI</label>
          <select name="componente_id" class="form-select form-select-sm select2">
            <option value="">Todos los componentes</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ $componenteId == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label form-label-sm">Año</label>
          <select name="anio" class="form-select form-select-sm">
            @foreach($anios as $a)
            <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="ti tabler-filter me-1"></i>Filtrar</button>
          <a href="{{ route('cumplimiento.responsables') }}" class="btn btn-sm btn-label-secondary"><i class="ti tabler-x"></i></a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Tabla de responsables --}}
<div class="card">
  <div class="card-header">
    <h6 class="mb-0">Detalle por persona <small class="text-muted">(ordenado por menor cumplimiento)</small></h6>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0" id="tablaResponsables">
        <thead class="table-light">
          <tr>
            <th>Responsable</th>
            <th>Unidad</th>
            <th class="text-center">Cumplimiento</th>
            <th class="text-center">Completadas</th>
            <th class="text-center">Vencidas</th>
            <th class="text-center">Sin evidencia</th>
            <th class="text-center">Ev. pendiente validar</th>
            <th class="text-center">Retraso prom.</th>
            <th class="text-center">Estado</th>
          </tr>
        </thead>
        <tbody>
          @forelse($responsables as $u)
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm flex-shrink-0">
                  @if($u->profile_photo_path)
                    <img src="{{ Storage::url($u->profile_photo_path) }}" class="rounded-circle" alt="">
                  @else
                    <div class="avatar-initial rounded-circle bg-label-{{ $u->stat_semaforo }}">
                      {{ strtoupper(substr($u->name,0,1)) }}
                    </div>
                  @endif
                </div>
                <div>
                  <div class="fw-medium">{{ $u->name }}</div>
                  <small class="text-muted">{{ $u->cargo ?? 'Sin cargo' }}</small>
                </div>
              </div>
            </td>
            <td><small>{{ $u->unidadOrganica?->sigla ?? '—' }}</small></td>
            <td class="text-center">
              <div class="d-flex align-items-center gap-2 justify-content-center">
                <div class="progress flex-grow-1" style="height:8px;min-width:60px;max-width:80px">
                  <div class="progress-bar bg-{{ $u->stat_semaforo }}" style="width:{{ $u->stat_porcentaje }}%"></div>
                </div>
                <span class="fw-bold text-{{ $u->stat_semaforo }}">{{ $u->stat_porcentaje }}%</span>
              </div>
              <small class="text-muted">{{ $u->stat_total }} actividades</small>
            </td>
            <td class="text-center">
              <span class="badge bg-label-success">{{ $u->stat_completadas }}</span>
            </td>
            <td class="text-center">
              @if($u->stat_vencidas > 0)
                <span class="badge bg-danger">{{ $u->stat_vencidas }}</span>
              @else
                <span class="badge bg-label-secondary">0</span>
              @endif
            </td>
            <td class="text-center">
              @if($u->stat_sin_evidencia > 0)
                <a href="{{ route('cumplimiento.sin-evidencia', ['responsable_id' => $u->id]) }}" class="badge bg-warning text-dark text-decoration-none">
                  {{ $u->stat_sin_evidencia }}
                </a>
              @else
                <span class="badge bg-label-secondary">0</span>
              @endif
            </td>
            <td class="text-center">
              @if($u->stat_ev_pendiente > 0)
                <span class="badge bg-label-info">{{ $u->stat_ev_pendiente }}</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td class="text-center">
              @if($u->stat_dias_retraso > 0)
                <span class="text-danger fw-medium">{{ $u->stat_dias_retraso }}d</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td class="text-center">
              @php
                $label = match($u->stat_semaforo) {
                  'success' => 'Al día',
                  'warning' => 'En proceso',
                  default   => 'En riesgo',
                };
              @endphp
              <span class="badge bg-label-{{ $u->stat_semaforo }}">{{ $label }}</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-5">
              <i class="ti tabler-users-off icon-32px d-block mb-2"></i>
              No hay responsables con actividades en el período seleccionado
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.select2').forEach(el => $(el).select2({ width: '100%' }));

  if (document.getElementById('tablaResponsables')) {
    $('#tablaResponsables').DataTable({
      responsive: true,
      pageLength: 20,
      language: {
        search: 'Buscar:',
        lengthMenu: 'Mostrar _MENU_ por página',
        info: '_START_ - _END_ de _TOTAL_ responsables',
        paginate: { previous: '‹', next: '›' },
        zeroRecords: 'Sin resultados',
        emptyTable: 'Sin datos',
      },
      order: [[2, 'asc']], // ordenar por cumplimiento asc por defecto
    });
  }
});
</script>
@endsection
