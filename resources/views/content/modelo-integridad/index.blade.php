@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Modelo de Integridad - PULSO UGEL')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Modelo de Integridad</h4>
    <p class="mb-0 text-muted">9 Componentes del Sistema de Integridad Institucional — Avance global: <strong>{{ round($avance_global) }}%</strong></p>
  </div>
  <a href="{{ route('mon-semaforo') }}" class="btn btn-outline-secondary">
    <i class="ti tabler-traffic-lights me-1"></i>Ver Semáforo
  </a>
</div>

{{-- Barra de avance global --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <div class="d-flex justify-content-between mb-1">
      <span class="fw-medium">Avance Global del Modelo</span>
      <span class="fw-bold">{{ round($avance_global) }}%</span>
    </div>
    <div class="progress" style="height:12px">
      @php $gc = round($avance_global) >= 75 ? 'success' : (round($avance_global) >= 50 ? 'warning' : 'danger'); @endphp
      <div class="progress-bar bg-{{ $gc }}" style="width:{{ round($avance_global) }}%" role="progressbar"></div>
    </div>
    <div class="d-flex gap-4 mt-2">
      <small class="text-success"><i class="ti tabler-circle-filled me-1"></i>Verde ≥ 75%</small>
      <small class="text-warning"><i class="ti tabler-circle-filled me-1"></i>Amarillo 50-74%</small>
      <small class="text-danger"><i class="ti tabler-circle-filled me-1"></i>Rojo &lt; 50%</small>
    </div>
  </div>
</div>

{{-- Tarjetas de componentes --}}
<div class="row g-4">
  @forelse($componentes as $c)
  <div class="col-12 col-md-6 col-xl-4">
    <div class="card h-100 border-{{ $c->color }} border-opacity-50">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            <div class="avatar avatar-sm">
              <span class="avatar-initial rounded bg-label-{{ $c->color }}">
                <i class="ti {{ $c->icono }} icon-18px"></i>
              </span>
            </div>
            <span class="badge bg-label-secondary">Comp. {{ $c->numero }}</span>
          </div>
          <span class="badge bg-{{ $c->color }}">{{ $c->semaforo }}</span>
        </div>
        <h6 class="mb-3">{{ $c->nombre }}</h6>
        <div class="d-flex justify-content-between mb-1">
          <small class="text-muted">{{ $c->completadas_count }} de {{ $c->actividades_count }} actividades</small>
          <small class="fw-bold text-{{ $c->color }}">{{ $c->porcentaje }}%</small>
        </div>
        <div class="progress mb-3" style="height:8px">
          <div class="progress-bar bg-{{ $c->color }}" style="width:{{ $c->porcentaje }}%"></div>
        </div>
        <div class="d-flex gap-2 text-center">
          <div class="flex-fill bg-label-success rounded py-1">
            <div class="fw-bold text-success">{{ $c->completadas_count }}</div>
            <small class="text-muted">Completadas</small>
          </div>
          <div class="flex-fill bg-label-warning rounded py-1">
            <div class="fw-bold text-warning">{{ $c->en_proceso_count }}</div>
            <small class="text-muted">En Proceso</small>
          </div>
          <div class="flex-fill bg-label-danger rounded py-1">
            <div class="fw-bold text-danger">{{ $c->vencidas_count }}</div>
            <small class="text-muted">Vencidas</small>
          </div>
        </div>
      </div>
      <div class="card-footer py-2">
        <a href="{{ route('sci-control-interno') }}?componente_id={{ $c->id }}" class="btn btn-sm btn-outline-{{ $c->color }} w-100">
          <i class="ti tabler-list-details me-1"></i>Ver actividades
        </a>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="card"><div class="card-body text-center text-muted py-5">
      <i class="ti tabler-components icon-32px d-block mb-2"></i>No hay componentes configurados.
    </div></div>
  </div>
  @endforelse
</div>

@endsection
