@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Semáforo SCI - PULSO UGEL')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Semáforo de Control Interno</h4>
    <p class="mb-0 text-muted">Estado de avance por componente — Avance global: <strong>{{ round($avance_global) }}%</strong></p>
  </div>
</div>

{{-- Leyenda --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <div class="d-flex flex-wrap gap-4 align-items-center">
      <strong class="text-muted">Leyenda:</strong>
      <span class="badge bg-success p-2"><i class="ti tabler-circle-filled me-1"></i>Verde — Avance ≥ {{ $umbral_verde }}%</span>
      <span class="badge bg-warning p-2"><i class="ti tabler-circle-filled me-1"></i>Amarillo — {{ $umbral_amarillo }}% a {{ $umbral_verde - 1 }}%</span>
      <span class="badge bg-danger p-2"><i class="ti tabler-circle-filled me-1"></i>Rojo — Avance &lt; {{ $umbral_amarillo }}%</span>
    </div>
  </div>
</div>

{{-- Semáforo Componentes --}}
<h5 class="mb-3">Por Componente SCI</h5>
<div class="row g-3 mb-5">
  @foreach($componentes as $c)
  <div class="col-12 col-md-6 col-xl-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="avatar avatar-sm">
            <span class="avatar-initial rounded-circle bg-{{ $c->color }}">
              <i class="ti {{ $c->icono }} icon-18px text-white"></i>
            </span>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-medium text-truncate">{{ $c->nombre }}</div>
            <small class="text-muted">Comp. {{ $c->numero }}</small>
          </div>
          <div class="text-end">
            <h4 class="mb-0 text-{{ $c->color }}">{{ $c->porcentaje }}%</h4>
            <span class="badge bg-label-{{ $c->color }}">{{ $c->semaforo }}</span>
          </div>
        </div>
        <div class="progress mb-2" style="height:10px">
          <div class="progress-bar bg-{{ $c->color }} rounded-pill" style="width:{{ $c->porcentaje }}%"></div>
        </div>
        <div class="d-flex justify-content-between">
          <small class="text-muted">{{ $c->completadas_count }} completadas</small>
          <small class="text-muted">{{ $c->actividades_count }} total</small>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Semáforo Unidades --}}
<h5 class="mb-3">Por Unidad Orgánica</h5>
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Unidad Orgánica</th>
            <th>Actividades</th>
            <th>Avance</th>
            <th>Semáforo</th>
          </tr>
        </thead>
        <tbody>
          @forelse($unidades as $i => $u)
          <tr>
            <td><span class="badge bg-label-secondary">{{ $i + 1 }}</span></td>
            <td>
              <div class="fw-medium">{{ $u->nombre }}</div>
              <small class="text-muted">{{ $u->sigla }}</small>
            </td>
            <td>
              <small>{{ $u->completadas_count }} / {{ $u->actividades_count }}</small>
            </td>
            <td style="min-width:160px">
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:8px">
                  <div class="progress-bar bg-{{ $u->color }}" style="width:{{ $u->porcentaje }}%"></div>
                </div>
                <small class="fw-bold text-{{ $u->color }}">{{ $u->porcentaje }}%</small>
              </div>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <i class="ti tabler-circle-filled text-{{ $u->color }} icon-20px"></i>
                <span class="badge bg-label-{{ $u->color }}">{{ $u->semaforo }}</span>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted py-4">Sin datos de unidades</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
