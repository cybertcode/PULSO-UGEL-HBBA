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

@section('page-style')
<style>
.kpi-card { border-radius: 14px; border: none; overflow: hidden; transition: transform .18s, box-shadow .18s; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.10); }
.kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1; }
.kpi-label { font-size: .72rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; opacity: .75; }
.filter-card { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); }
.filter-card .form-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #6e6b7b; }
.act-table-card { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); overflow: hidden; }
.act-row-vencida { background: rgba(234,84,85,.04) !important; }
</style>
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cumplimiento.panel') }}">Panel SCI</a></li>
    <li class="breadcrumb-item active">Sin Evidencia</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1 fw-bold">Actividades sin Evidencia</h4>
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
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.22)"><i class="ti tabler-file-off" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff">{{ $stats['total'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">Total sin evidencia</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#e52d27,#b31217)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.22)"><i class="ti tabler-clock-x" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff">{{ $stats['vencidas'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">Vencidas sin evidencia</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#0acffe,#495aff)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.22)"><i class="ti tabler-file-time" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff">{{ $stats['en_proceso'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">En proceso / Observado</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#e52d27,#b31217)">
      <div class="card-body p-4">
        <div class="kpi-icon mb-3" style="background:rgba(255,255,255,.22)"><i class="ti tabler-urgent" style="color:#fff"></i></div>
        <div class="kpi-value" style="color:#fff">{{ $stats['alta_prio'] }}</div>
        <div class="kpi-label" style="color:rgba(255,255,255,.8)">Alta prioridad</div>
      </div>
    </div>
  </div>
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
<div class="card filter-card mb-4">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('cumplimiento.sin-evidencia') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Unidad Orgánica</label>
          <select name="unidad_organica_id" class="form-select form-select-sm select2">
            <option value="">Todas</option>
            @foreach($unidades as $u)
            <option value="{{ $u->id }}" {{ $unidadId == $u->id ? 'selected' : '' }}>{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Componente SCI</label>
          <select name="componente_id" class="form-select form-select-sm select2">
            <option value="">Todos</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ $componenteId == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Responsable</label>
          <select name="responsable_id" class="form-select form-select-sm select2">
            <option value="">Todos</option>
            @foreach($responsables as $r)
            <option value="{{ $r->id }}" {{ $responsableId == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Prioridad</label>
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
<div class="card act-table-card">
  <div class="card-header py-3 d-flex align-items-center justify-content-between">
    <h6 class="mb-0 fw-semibold">Listado de actividades</h6>
    <small class="text-muted">{{ $actividades->total() }} resultado(s)</small>
  </div>
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
          <tr class="{{ $act->estado === 'vencida' ? 'act-row-vencida' : '' }}">
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
                <div class="progress" style="height:6px;width:50px;border-radius:3px">
                  <div class="progress-bar bg-{{ $ec }}" style="width:{{ $act->avance }}%;border-radius:3px"></div>
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
                  <br><small class="text-danger">+{{ (int) round(now()->diffInDays($act->fecha_limite)) }}d retraso</small>
                @endif
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              <a href="{{ route('sci-evidencias', ['actividad_id' => $act->id, 'nueva' => 1]) }}"
                 class="btn btn-sm btn-primary" title="Subir evidencia">
                <i class="ti tabler-upload me-1"></i>Subir
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-5">
              <i class="ti tabler-file-check icon-36px d-block mb-2 text-success"></i>
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
  const form = document.querySelector('form[method="GET"]');

  document.querySelectorAll('.select2').forEach(el => {
    const $w = $('<div class="position-relative"></div>');
    $(el).wrap($w);
    $(el).select2({ dropdownParent: $(el).parent(), width: '100%' });
    $(el).on('select2:select select2:unselect', () => form.submit());
  });

  document.querySelectorAll('select:not(.select2)').forEach(el => {
    el.addEventListener('change', () => form.submit());
  });
});
</script>
@endsection
