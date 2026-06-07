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

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Control Interno</li>
  </ol>
</nav>

{{-- Header --}}
<div class="pulso-page-header mb-6">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <h4 class="mb-1"><i class="ti tabler-clipboard-list me-2"></i>Sistema de Control Interno</h4>
      <p>Seguimiento y registro de actividades de control institucional · {{ now()->year }}</p>
    </div>
    @can('control-interno.crear')
    <button class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4)"
      data-bs-toggle="modal" data-bs-target="#modalNuevaActividad">
      <i class="ti tabler-plus me-1"></i>Nueva Actividad
    </button>
    @endcan
  </div>
</div>

{{-- KPIs --}}
<div class="row g-4 mb-6">
  @php
  $kpis = [
    ['k'=>'total',      'label'=>'Total SCI',    'sub'=>'Actividades registradas', 'color'=>'primary', 'icon'=>'tabler-clipboard-list'],
    ['k'=>'completadas','label'=>'Completadas',  'sub'=>'Actividades finalizadas', 'color'=>'success', 'icon'=>'tabler-circle-check'],
    ['k'=>'en_proceso', 'label'=>'En Proceso',   'sub'=>'En desarrollo',           'color'=>'warning', 'icon'=>'tabler-loader'],
    ['k'=>'observados', 'label'=>'Observados',   'sub'=>'Pendientes de revisión',  'color'=>'info',    'icon'=>'tabler-eye'],
    ['k'=>'vencidas',   'label'=>'Vencidas',     'sub'=>'Sin completar',           'color'=>'danger',  'icon'=>'tabler-alert-triangle'],
  ];
  @endphp
  @foreach($kpis as $kp)
  <div class="col-6 col-md">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-4">
          <div class="badge rounded bg-label-{{ $kp['color'] }} p-2">
            <i class="icon-base ti {{ $kp['icon'] }} icon-26px"></i>
          </div>
          <span class="badge bg-label-{{ $kp['color'] }} rounded-pill">{{ now()->year }}</span>
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
    <form method="GET" action="{{ route('sci-control-interno') }}">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label form-label-sm">Componente</label>
          <select name="componente_id" class="form-select form-select-sm select2-filtro">
            <option value="">Todos</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ request('componente_id') == $c->id ? 'selected' : '' }}>
              {{ $c->numero }}. {{ $c->nombre }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label form-label-sm">Unidad</label>
          <select name="unidad_id" class="form-select form-select-sm select2-filtro">
            <option value="">Todas</option>
            @foreach($unidades as $u)
            <option value="{{ $u->id }}" {{ request('unidad_id') == $u->id ? 'selected' : '' }}>
              {{ $u->sigla ?? $u->nombre }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label form-label-sm">Responsable</label>
          <select name="responsable_id" class="form-select form-select-sm select2-filtro">
            <option value="">Todos</option>
            @foreach($responsables as $u)
            <option value="{{ $u->id }}" {{ request('responsable_id') == $u->id ? 'selected' : '' }}>
              {{ $u->name }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label form-label-sm">Estado</label>
          <select name="estado" class="form-select form-select-sm">
            <option value="">Todos</option>
            @foreach(['pendiente'=>'Pendiente','en_proceso'=>'En Proceso','completada'=>'Completada','observado'=>'Observado','vencida'=>'Vencida'] as $v => $l)
            <option value="{{ $v }}" {{ request('estado') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-1">
          <label class="form-label form-label-sm">Prioridad</label>
          <select name="prioridad" class="form-select form-select-sm">
            <option value="">Todas</option>
            <option value="alta"  {{ request('prioridad') === 'alta'  ? 'selected' : '' }}>Alta</option>
            <option value="media" {{ request('prioridad') === 'media' ? 'selected' : '' }}>Media</option>
            <option value="baja"  {{ request('prioridad') === 'baja'  ? 'selected' : '' }}>Baja</option>
          </select>
        </div>
        <div class="col-md-1">
          <label class="form-label form-label-sm">Buscar</label>
          <input type="text" name="buscar" class="form-control form-control-sm" value="{{ request('buscar') }}" placeholder="Código...">
        </div>
        <div class="col-md-1 d-flex gap-1">
          <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="ti tabler-filter"></i></button>
          <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-secondary"><i class="ti tabler-x"></i></a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Tabla --}}
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Actividades de Control Interno</h5>
    <span class="text-muted small">{{ $actividades->total() }} registros</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle pulso-table">
        <thead class="table-light">
          <tr>
            <th>Código</th>
            <th>Actividad</th>
            <th>Componente</th>
            <th>Unidad</th>
            <th>Responsables</th>
            <th>Prioridad</th>
            <th>Fecha Límite</th>
            <th style="min-width:130px">Avance</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($actividades as $a)
          @php
            $ec        = $a->estado_color;
            $dias      = now()->diffInDays($a->fecha_limite, false);
            $prioColor = match($a->prioridad) { 'alta'=>'danger','media'=>'warning',default=>'info' };
          @endphp
          <tr>
            <td><small class="text-muted fw-medium">{{ $a->codigo }}</small></td>
            <td>
              <div class="fw-medium" style="max-width:200px">{{ Str::limit($a->nombre, 45) }}</div>
              @if($a->numero_sgd)
              <small class="text-muted"><i class="ti tabler-file-description icon-12px me-1"></i>{{ $a->numero_sgd }}</small>
              @endif
            </td>
            <td>
              <small class="d-flex align-items-center gap-1">
                <i class="ti {{ $a->componente->icono ?? 'tabler-point' }} icon-14px text-primary"></i>
                {{ Str::limit($a->componente->nombre ?? '—', 22) }}
              </small>
            </td>
            <td>
              <span class="badge bg-label-secondary" title="{{ $a->unidadOrganica->nombre ?? '—' }}">
                {{ $a->unidadOrganica->sigla ?? '—' }}
              </span>
            </td>
            <td style="max-width:160px">
              @if($a->responsables->isNotEmpty())
                @foreach($a->responsables->take(3) as $resp)
                @php
                  $tipoBadge = match($resp->pivot->tipo) {
                    'principal'   => 'primary',
                    'colaborador' => 'secondary',
                    'supervisor'  => 'info',
                    default       => 'secondary',
                  };
                @endphp
                <div class="d-flex align-items-center gap-1 mb-1">
                  <span class="badge bg-label-{{ $tipoBadge }} rounded-pill" style="font-size:10px">
                    {{ ucfirst($resp->pivot->tipo[0]) }}
                  </span>
                  <small class="text-truncate" style="max-width:110px" title="{{ $resp->name }}">{{ $resp->name }}</small>
                </div>
                @endforeach
                @if($a->responsables->count() > 3)
                <small class="text-muted">+{{ $a->responsables->count() - 3 }} más</small>
                @endif
              @else
                <small class="text-muted fst-italic">Sin asignar</small>
              @endif
            </td>
            <td><span class="badge bg-label-{{ $prioColor }}">{{ ucfirst($a->prioridad) }}</span></td>
            <td>
              <span class="badge bg-label-{{ $dias < 0 ? 'danger' : ($dias <= 7 ? 'warning' : 'secondary') }}">
                {{ $a->fecha_limite->format('d/m/Y') }}
              </span>
              @if($dias >= 0 && $dias <= 7)
              <br><small class="text-warning">{{ $dias }}d restantes</small>
              @endif
            </td>
            <td>
              <div class="d-flex align-items-center gap-1">
                <div class="progress flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-{{ $ec }} rounded-pill" style="width:{{ $a->avance }}%"></div>
                </div>
                <small class="fw-bold text-{{ $ec }}" style="min-width:30px">{{ $a->avance }}%</small>
              </div>
            </td>
            <td><span class="badge bg-label-{{ $ec }}">{{ $a->estado_label }}</span></td>
            <td>
              <div class="d-flex gap-1 flex-nowrap">
                <button class="btn btn-icon btn-sm btn-label-secondary btn-historial"
                  data-id="{{ $a->id }}" data-nombre="{{ $a->nombre }}"
                  data-url="{{ route('sci-control-interno.historial', $a) }}"
                  title="Historial de cambios">
                  <i class="ti tabler-history"></i>
                </button>
                @can('control-interno.editar')
                <button class="btn btn-icon btn-sm btn-label-primary btn-editar"
                  data-id="{{ $a->id }}"
                  data-nombre="{{ $a->nombre }}"
                  data-componente="{{ $a->componente_id }}"
                  data-unidad="{{ $a->unidad_organica_id ?? '' }}"
                  data-responsables="{{ $a->responsables->pluck('id')->join(',') }}"
                  data-tipo-asignacion="{{ $a->responsables->first()?->pivot->tipo ?? 'principal' }}"
                  data-fecha="{{ $a->fecha_limite->format('Y-m-d') }}"
                  data-fechainicio="{{ $a->fecha_inicio?->format('Y-m-d') ?? '' }}"
                  data-avance="{{ $a->avance }}"
                  data-estado="{{ $a->estado }}"
                  data-prioridad="{{ $a->prioridad }}"
                  data-sgd="{{ $a->numero_sgd ?? '' }}"
                  data-observaciones="{{ htmlspecialchars($a->observaciones ?? '') }}"
                  title="Editar">
                  <i class="ti tabler-edit"></i>
                </button>
                <form method="POST" action="{{ route('sci-control-interno.destroy', $a) }}" class="form-eliminar d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-icon btn-sm btn-label-danger" title="Eliminar">
                    <i class="ti tabler-trash"></i>
                  </button>
                </form>
                @endcan
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="10" class="text-center text-muted py-5">
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

{{-- ════════════════════════════════════════════════════ --}}
{{-- Modal Nueva Actividad                               --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalNuevaActividad" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('sci-control-interno.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-plus me-2"></i>Nueva Actividad SCI</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">

            {{-- Nombre --}}
            <div class="col-12">
              <label class="form-label">Nombre de la Actividad <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" placeholder="Descripción de la actividad" required>
            </div>

            {{-- Componente --}}
            <div class="col-md-6">
              <label class="form-label">Componente <span class="text-danger">*</span></label>
              <select name="componente_id" class="form-select select2-modal" required>
                <option value="">Seleccionar componente</option>
                @foreach($componentes as $c)
                <option value="{{ $c->id }}">{{ $c->numero }}. {{ $c->nombre }}</option>
                @endforeach
              </select>
            </div>

            {{-- Unidad Orgánica --}}
            <div class="col-md-6">
              <label class="form-label">Unidad Orgánica</label>
              <select name="unidad_organica_id" class="form-select select2-modal">
                <option value="">Sin unidad</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>

            {{-- Responsables múltiples --}}
            <div class="col-12">
              <label class="form-label d-flex align-items-center gap-2">
                Responsables
                <small class="text-muted fw-normal">(puedes seleccionar uno, varios o todos)</small>
              </label>
              <div class="row g-2">
                <div class="col-md-8">
                  <select name="responsables[]" class="form-select select2-modal-multi" multiple>
                    @foreach($responsables as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4">
                  <select name="tipo_asignacion" class="form-select">
                    <option value="principal">Principal</option>
                    <option value="colaborador">Colaborador</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="todos">⚡ Asignar TODOS</option>
                  </select>
                  <small class="text-muted">"Asignar TODOS" ignora la selección de arriba</small>
                </div>
              </div>
            </div>

            {{-- Fechas --}}
            <div class="col-md-4">
              <label class="form-label">Fecha Inicio</label>
              <input type="date" name="fecha_inicio" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Fecha Límite <span class="text-danger">*</span></label>
              <input type="date" name="fecha_limite" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Prioridad</label>
              <select name="prioridad" class="form-select">
                <option value="alta">Alta</option>
                <option value="media" selected>Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>

            {{-- N° SGD --}}
            <div class="col-12">
              <label class="form-label">N° SGD / Expediente</label>
              <input type="text" name="numero_sgd" class="form-control" placeholder="Ej: SGD-2024-001">
            </div>

            {{-- Observaciones --}}
            <div class="col-12">
              <label class="form-label">Observaciones / Recomendaciones</label>
              <textarea name="observaciones" class="form-control" rows="3"
                placeholder="Detalle adicional, observaciones técnicas..."></textarea>
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

{{-- ════════════════════════════════════════════════════ --}}
{{-- Modal Editar Actividad                              --}}
{{-- ════════════════════════════════════════════════════ --}}
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
              <select name="componente_id" id="edit_componente" class="form-select select2-modal-edit" required>
                @foreach($componentes as $c)
                <option value="{{ $c->id }}">{{ $c->numero }}. {{ $c->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Unidad Orgánica</label>
              <select name="unidad_organica_id" id="edit_unidad" class="form-select select2-modal-edit">
                <option value="">Sin unidad</option>
                @foreach($unidades as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>

            {{-- Responsables múltiples editar --}}
            <div class="col-12">
              <label class="form-label d-flex align-items-center gap-2">
                Responsables
                <small class="text-muted fw-normal">(puedes seleccionar uno, varios o todos)</small>
              </label>
              <div class="row g-2">
                <div class="col-md-8">
                  <select name="responsables[]" id="edit_responsables" class="form-select select2-modal-edit-multi" multiple>
                    @foreach($responsables as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4">
                  <select name="tipo_asignacion" id="edit_tipo_asignacion" class="form-select">
                    <option value="principal">Principal</option>
                    <option value="colaborador">Colaborador</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="todos">⚡ Asignar TODOS</option>
                  </select>
                  <small class="text-muted">"Asignar TODOS" ignora la selección</small>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Fecha Inicio</label>
              <input type="date" name="fecha_inicio" id="edit_fechainicio" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Fecha Límite <span class="text-danger">*</span></label>
              <input type="date" name="fecha_limite" id="edit_fecha" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Estado</label>
              <select name="estado" id="edit_estado" class="form-select">
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="completada">Completada</option>
                <option value="observado">Observado</option>
                <option value="vencida">Vencida</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Prioridad</label>
              <select name="prioridad" id="edit_prioridad" class="form-select">
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Avance %</label>
              <input type="number" name="avance" id="edit_avance" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-8">
              <label class="form-label">N° SGD / Expediente</label>
              <input type="text" name="numero_sgd" id="edit_sgd" class="form-control">
            </div>

            <div class="col-12">
              <label class="form-label">Observaciones / Recomendaciones</label>
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

{{-- ════════════════════════════════════════════════════ --}}
{{-- Modal Historial de Cambios                          --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalHistorial" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ti tabler-history me-2 text-primary"></i>Historial de Cambios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3" id="historial_actividad_nombre"></p>
        <div id="historial_loading" class="text-center py-4">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted">Cargando historial...</p>
        </div>
        <div id="historial_content" style="display:none">
          <div class="timeline-container" id="historial_lista"></div>
        </div>
        <div id="historial_empty" style="display:none" class="text-center text-muted py-4">
          <i class="ti tabler-history icon-32px d-block mb-2"></i>
          <p>Sin historial de cambios registrado.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Select2 filtros ───────────────────────────────────────────────────────
  document.querySelectorAll('.select2-filtro').forEach(el =>
    $(el).select2({ dropdownParent: document.body, width: '100%' })
  );

  // ── Select2 modal nueva — simples ─────────────────────────────────────────
  const modalNuevo = document.getElementById('modalNuevaActividad');
  document.querySelectorAll('.select2-modal').forEach(el =>
    $(el).select2({ dropdownParent: modalNuevo, width: '100%' })
  );
  // Multi-select modal nueva
  $(modalNuevo).find('.select2-modal-multi').select2({
    dropdownParent: modalNuevo,
    width: '100%',
    placeholder: 'Seleccionar responsables...',
    allowClear: true,
  });

  // ── Select2 modal editar — simples ────────────────────────────────────────
  const modalEditar = document.getElementById('modalEditarActividad');
  document.querySelectorAll('.select2-modal-edit').forEach(el =>
    $(el).select2({ dropdownParent: modalEditar, width: '100%' })
  );
  // Multi-select modal editar
  $('#edit_responsables').select2({
    dropdownParent: modalEditar,
    width: '100%',
    placeholder: 'Seleccionar responsables...',
    allowClear: true,
  });

  // ── Modal editar — poblar campos ──────────────────────────────────────────
  document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = document.getElementById('formEditarActividad');
      form.action = '/control-interno/' + this.dataset.id;

      document.getElementById('edit_nombre').value        = this.dataset.nombre;
      document.getElementById('edit_fecha').value         = this.dataset.fecha;
      document.getElementById('edit_fechainicio').value   = this.dataset.fechainicio || '';
      document.getElementById('edit_avance').value        = this.dataset.avance;
      document.getElementById('edit_sgd').value           = this.dataset.sgd || '';
      document.getElementById('edit_observaciones').value = this.dataset.observaciones || '';

      const setSelect = (id, val) => {
        const el = document.getElementById(id);
        if (el && val) { el.value = val; $(el).trigger('change'); }
      };
      setSelect('edit_componente',      this.dataset.componente);
      setSelect('edit_unidad',          this.dataset.unidad);
      setSelect('edit_estado',          this.dataset.estado);
      setSelect('edit_prioridad',       this.dataset.prioridad);
      setSelect('edit_tipo_asignacion', this.dataset.tipoAsignacion || 'principal');

      // Responsables múltiples
      const responsablesIds = this.dataset.responsables
        ? this.dataset.responsables.split(',').filter(Boolean)
        : [];
      $('#edit_responsables').val(responsablesIds).trigger('change');

      new bootstrap.Modal(modalEditar).show();
    });
  });

  // ── Historial de cambios ──────────────────────────────────────────────────
  document.querySelectorAll('.btn-historial').forEach(btn => {
    btn.addEventListener('click', function () {
      const url    = this.dataset.url;
      const nombre = this.dataset.nombre;

      document.getElementById('historial_actividad_nombre').textContent = nombre;
      document.getElementById('historial_loading').style.display  = 'block';
      document.getElementById('historial_content').style.display  = 'none';
      document.getElementById('historial_empty').style.display    = 'none';

      new bootstrap.Modal(document.getElementById('modalHistorial')).show();

      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
          document.getElementById('historial_loading').style.display = 'none';
          if (!data.length) {
            document.getElementById('historial_empty').style.display = 'block';
            return;
          }
          const colores = { estado:'primary', avance:'success', prioridad:'warning', responsables:'info', default:'secondary' };
          const lista = data.map(h => {
            const color = colores[h.campo] || colores.default;
            const fecha = new Date(h.created_at).toLocaleString('es-PE');
            return `<div class="d-flex gap-3 mb-3 pb-3 border-bottom">
              <div class="badge rounded bg-label-${color} p-2 flex-shrink-0 align-self-start">
                <i class="ti tabler-edit icon-18px"></i>
              </div>
              <div class="flex-grow-1">
                <div class="fw-medium">${h.campo_label ?? h.campo}</div>
                <div class="d-flex gap-2 mt-1 flex-wrap">
                  <span class="badge bg-label-secondary text-truncate" style="max-width:180px" title="${h.valor_anterior ?? '—'}">Antes: ${h.valor_anterior ?? '—'}</span>
                  <i class="ti tabler-arrow-right align-self-center text-muted"></i>
                  <span class="badge bg-label-${color} text-truncate" style="max-width:180px" title="${h.valor_nuevo ?? '—'}">Ahora: ${h.valor_nuevo ?? '—'}</span>
                </div>
                <small class="text-muted d-block mt-1">
                  <i class="ti tabler-user icon-12px me-1"></i>${h.usuario?.name ?? 'Sistema'} — ${fecha}
                </small>
              </div>
            </div>`;
          }).join('');

          document.getElementById('historial_lista').innerHTML = lista;
          document.getElementById('historial_content').style.display = 'block';
        })
        .catch(() => {
          document.getElementById('historial_loading').style.display = 'none';
          document.getElementById('historial_empty').style.display   = 'block';
        });
    });
  });

  // ── Confirmar eliminar ────────────────────────────────────────────────────
  document.querySelectorAll('.form-eliminar').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({
        title: '¿Eliminar actividad?',
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
