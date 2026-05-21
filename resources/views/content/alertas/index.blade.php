@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Alertas - PULSO UGEL')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Alertas del Sistema</h4>
    <p class="mb-0 text-muted">Notificaciones automáticas por actividades vencidas o en riesgo</p>
  </div>
  @if($stats['total'] > 0)
  <form method="POST" action="{{ route('mon-alertas.leer-todas') }}">
    @csrf @method('PATCH')
    <button type="submit" class="btn btn-label-secondary">
      <i class="ti tabler-checks me-1"></i>Marcar todas como leídas
    </button>
  </form>
  @endif
</div>

{{-- Resumen --}}
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card border-danger border-opacity-50">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar"><span class="avatar-initial rounded bg-label-danger"><i class="ti tabler-alert-octagon icon-26px"></i></span></div>
        <div><h4 class="mb-0 text-danger">{{ $stats['alta'] }}</h4><small class="text-muted">Alta prioridad</small></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-warning border-opacity-50">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar"><span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-alert-triangle icon-26px"></i></span></div>
        <div><h4 class="mb-0 text-warning">{{ $stats['media'] }}</h4><small class="text-muted">Media prioridad</small></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-info border-opacity-50">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar"><span class="avatar-initial rounded bg-label-info"><i class="ti tabler-info-circle icon-26px"></i></span></div>
        <div><h4 class="mb-0 text-info">{{ $stats['baja'] }}</h4><small class="text-muted">Baja prioridad</small></div>
      </div>
    </div>
  </div>
</div>

{{-- Lista de alertas --}}
<div class="card">
  <div class="card-header"><h5 class="mb-0">Alertas No Leídas ({{ $stats['total'] }})</h5></div>
  <div class="list-group list-group-flush">
    @forelse($alertas as $alerta)
    @php
      $ic  = match($alerta->prioridad) { 'alta' => 'danger', 'media' => 'warning', default => 'info' };
      $ico = match($alerta->tipo) {
        'vencimiento'     => 'tabler-calendar-x',
        'avance_bajo'     => 'tabler-trending-down',
        'evidencia_falta' => 'tabler-file-off',
        default           => 'tabler-bell',
      };
    @endphp
    <div class="list-group-item px-4 py-3 {{ $alerta->leida ? 'opacity-50' : '' }}">
      <div class="d-flex gap-3 align-items-start">
        <div class="avatar flex-shrink-0">
          <span class="avatar-initial rounded bg-label-{{ $ic }}">
            <i class="ti {{ $ico }} icon-20px"></i>
          </span>
        </div>
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-medium">{{ $alerta->titulo }}</div>
              <p class="mb-1 text-muted small">{{ $alerta->mensaje }}</p>
              <div class="d-flex gap-2">
                @if($alerta->actividad)
                <small class="text-muted"><i class="ti tabler-clipboard-list icon-12px me-1"></i>{{ Str::limit($alerta->actividad->nombre, 40) }}</small>
                @endif
                @if($alerta->unidadOrganica)
                <small class="text-muted"><i class="ti tabler-building icon-12px me-1"></i>{{ $alerta->unidadOrganica->sigla }}</small>
                @endif
              </div>
            </div>
            <div class="text-end flex-shrink-0 ms-3">
              <span class="badge bg-label-{{ $ic }} mb-1">{{ ucfirst($alerta->prioridad) }}</span>
              <br><small class="text-muted">{{ $alerta->created_at->diffForHumans() }}</small>
              @if(!$alerta->leida)
              <br>
              <form method="POST" action="{{ route('mon-alertas.leer', $alerta) }}" class="mt-1">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-xs btn-label-secondary py-0 px-2">
                  <i class="ti tabler-check icon-12px me-1"></i>Marcar leída
                </button>
              </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="list-group-item text-center text-muted py-5">
      <i class="ti tabler-bell-off icon-48px d-block mb-3 text-success"></i>
      <h6 class="text-success">¡Sin alertas pendientes!</h6>
      <p class="mb-0">Todas las actividades están al día.</p>
    </div>
    @endforelse
  </div>
  @if($alertas->hasPages())
  <div class="card-footer">{{ $alertas->links() }}</div>
  @endif
</div>

@endsection
