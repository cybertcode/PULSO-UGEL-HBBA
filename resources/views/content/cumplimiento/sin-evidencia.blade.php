@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Actividades sin Evidencia — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss',
       'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js',
       'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cumplimiento.panel') }}">Panel SCI</a></li>
    <li class="breadcrumb-item active">Actividades sin Evidencia</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Actividades sin Evidencia</h4>
    <p class="mb-0 text-muted">Actividades con avance registrado pero sin documentos de respaldo</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('cumplimiento.panel') }}" class="btn btn-sm btn-label-secondary">
      <i class="ti tabler-layout-dashboard me-1"></i>Panel SCI
    </a>
    <a href="{{ route('cumplimiento.responsables') }}" class="btn btn-sm btn-label-primary">
      <i class="ti tabler-users me-1"></i>Por Responsable
    </a>
  </div>
</div>

{{-- KPIs --}}
<div class="row g-4 mb-4">
  @php
  $kpis_ev = [
    ['val' => $stats['total'],     'label' => 'Total sin evidencia', 'color' => 'warning', 'icon' => 'tabler-file-off'],
    ['val' => $stats['vencidas'],  'label' => 'Vencidas sin evidencia', 'color' => 'danger', 'icon' => 'tabler-clock-x'],
    ['val' => $stats['en_proceso'],'label' => 'En proceso / Observado', 'color' => 'info', 'icon' => 'tabler-file-time'],
    ['val' => $stats['alta_prio'], 'label' => 'Alta prioridad', 'color' => 'danger', 'icon' => 'tabler-urgent'],
  ];
  @endphp
  @foreach($kpis_ev as $k)
  <div class="col-6 col-md-3">
    <div class="card {{ $k['val'] > 0 && $k['color'] === 'danger' ? 'border-danger border-opacity-25' : '' }}">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-3">
          <div class="badge bg-label-{{ $k['color'] }} rounded p-2">
            <i class="ti {{ $k['icon'] }} icon-22px"></i>
          </div>
        </div>
        <h3 class="mb-1 text-{{ $k['color'] }}">{{ $k['val'] }}</h3>
        <p class="mb-0 text-muted small">{{ $k['label'] }}</p>
      </div>
    </div>
  </div>
  @endforeach
</div>

@if($stats['total'] > 0)
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
  <i class="ti tabler-alert-triangle me-3 icon-20px flex-shrink-0"></i>
  <div>
    <strong>Atención:</strong> Las siguientes actividades tienen estado avanzado (en proceso, observado, completada o vencida) pero
    <strong>no tienen ningún documento de evidencia registrado</strong>. El Coordinador SCI debe solicitar su subsanación.
  </div>
</div>
@endif

{{-- Filtros --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('cumplimiento.sin-evidencia') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label form-label-sm">Unidad Orgánica</label>
          <select name="unidad_organica_id" class="form-select form-select-sm select2">
            <option value="">Todas</option>
            @foreach($unidades as $u)
            <option value="{{ $u->id }}" {{ $unidadId == $u->id ? 'selected' : '' }}>{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label form-label-sm">Componente SCI</label>
          <select name="componente_id" class="form-select form-select-sm select2">
            <option value="">Todos</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ $componenteId == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label form-label-sm">Responsable</label>
          <select name="responsable_id" class="form-select form-select-sm select2">
            <option value="">Todos</option>
            @foreach($responsables as $r)
            <option value="{{ $r->id }}" {{ $responsableId == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label form-label-sm">Prioridad</label>
          <select name="prioridad" class="form-select form-select-sm">
            <option value="">Todas</option>
            <option value="alta"  {{ $prioridad === 'alta'  ? 'selected' : '' }}>Alta</option>
            <option value="media" {{ $prioridad === 'media' ? 'selected' : '' }}>Media</option>
            <option value="baja"  {{ $prioridad === 'baja'  ? 'selected' : '' }}>Baja</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="ti tabler-filter me-1"></i>Filtrar</button>
          <a href="{{ route('cumplimiento.sin-evidencia') }}" class="btn btn-sm btn-label-secondary"><i class="ti tabler-x"></i></a>
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
        <thead class="table-light">
          <tr>
            <th>Actividad</th>
            <th>Unidad</th>
            <th>Responsable(s)</th>
            <th>Componente</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Prioridad</th>
            <th class="text-center">Avance</th>
            <th>Fecha límite</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          @forelse($actividades as $act)
          @php
            $ec = match($act->estado) {
              'completada' => 'success',
              'vencida'    => 'danger',
              'observado'  => 'info',
              default      => 'warning',
            };
            $pc = match($act->prioridad) {
              'alta'  => 'danger',
              'media' => 'warning',
              default => 'secondary',
            };
            $hoy = now();
            $vencida = $act->fecha_limite && $act->fecha_limite->lt($hoy);
          @endphp
          <tr class="{{ $act->estado === 'vencida' ? 'table-danger bg-opacity-10' : '' }}">
            <td style="max-width:240px">
              <div class="fw-medium text-truncate" title="{{ $act->nombre }}">{{ $act->nombre }}</div>
              @if($act->codigo)<small class="text-muted">{{ $act->codigo }}</small>@endif
            </td>
            <td><small>{{ $act->unidadOrganica?->sigla ?? '—' }}</small></td>
            <td>
              @foreach($act->responsables->take(2) as $r)
                <div class="d-flex align-items-center gap-1">
                  <span class="badge bg-label-{{ $r->pivot->tipo === 'principal' ? 'primary' : 'secondary' }} badge-sm">{{ $r->pivot->tipo[0] }}</span>
                  <small>{{ $r->name }}</small>
                </div>
              @endforeach
              @if($act->responsables->count() > 2)
                <small class="text-muted">+{{ $act->responsables->count() - 2 }} más</small>
              @endif
            </td>
            <td><small>{{ $act->componente?->nombre ?? '—' }}</small></td>
            <td class="text-center">
              <span class="badge bg-label-{{ $ec }}">{{ $act->estado_label }}</span>
            </td>
            <td class="text-center">
              <span class="badge bg-label-{{ $pc }}">{{ ucfirst($act->prioridad) }}</span>
            </td>
            <td class="text-center">
              <div class="d-flex align-items-center gap-2 justify-content-center">
                <div class="progress" style="height:6px;width:50px">
                  <div class="progress-bar bg-{{ $ec }}" style="width:{{ $act->avance }}%"></div>
                </div>
                <small>{{ $act->avance }}%</small>
              </div>
            </td>
            <td>
              @if($act->fecha_limite)
                <span class="{{ $vencida ? 'text-danger fw-medium' : 'text-muted' }}">
                  {{ $act->fecha_limite->format('d/m/Y') }}
                </span>
                @if($vencida)
                  <br><small class="text-danger">+{{ now()->diffInDays($act->fecha_limite) }}d retraso</small>
                @endif
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              <a href="{{ route('sci-evidencias', ['actividad_id' => $act->id]) }}"
                 class="btn btn-sm btn-primary" title="Subir evidencia">
                <i class="ti tabler-upload me-1"></i>Subir
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-5">
              <i class="ti tabler-file-check icon-32px d-block mb-2 text-success"></i>
              <span class="text-success fw-medium">¡Todo en orden! No hay actividades sin evidencia</span>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($actividades->hasPages())
  <div class="card-footer">{{ $actividades->links() }}</div>
  @endif
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.select2').forEach(el => $(el).select2({ width: '100%' }));
});
</script>
@endsection
