@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Reconocimientos - PULSO UGEL')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Reconocimientos y Ranking</h4>
    <p class="mb-0 text-muted">Unidades con mejor desempeño en Control Interno</p>
  </div>
  <form method="GET" action="{{ route('rep-reconocimientos') }}" class="d-flex gap-2">
    <select name="anio" class="form-select form-select-sm" onchange="this.form.submit()">
      @foreach($anios as $a)
      <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
      @endforeach
    </select>
    <select name="mes" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">Anual</option>
      @foreach($meses as $num => $nombre)
      <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ $nombre }}</option>
      @endforeach
    </select>
  </form>
</div>

@if($ranking->isEmpty())
<div class="card">
  <div class="card-body text-center text-muted py-5">
    <i class="ti tabler-trophy-off icon-48px d-block mb-3"></i>
    <h6>Sin datos de reconocimientos para este período</h6>
    <p class="mb-0">Los reconocimientos se generan automáticamente al cierre de cada período.</p>
  </div>
</div>
@else

{{-- Podio Top 3 --}}
@if($top3->count() >= 2)
<div class="card mb-4">
  <div class="card-header"><h5 class="mb-0"><i class="ti tabler-trophy me-2 text-warning"></i>Top 3 Unidades Destacadas</h5></div>
  <div class="card-body">
    <div class="row justify-content-center g-0 align-items-end">
      {{-- 2do lugar --}}
      @if($top3->count() >= 2)
      @php $seg = $top3->get(1); @endphp
      <div class="col-4 text-center">
        <div class="avatar avatar-xl mx-auto mb-2">
          <span class="avatar-initial rounded-circle bg-label-secondary fs-4">
            {{ strtoupper(substr($seg->unidadOrganica->sigla ?? 'U', 0, 2)) }}
          </span>
        </div>
        <div class="fw-medium">{{ $seg->unidadOrganica->sigla ?? '—' }}</div>
        <div class="small text-muted mb-2">{{ $seg->unidadOrganica->nombre ?? '—' }}</div>
        <div class="bg-secondary bg-opacity-10 rounded-top py-3 px-2" style="min-height:80px">
          <div class="badge bg-secondary fs-6 mb-1">2°</div>
          <div class="fw-bold text-secondary">{{ $seg->puntaje }}%</div>
          <div class="badge bg-label-secondary mt-1"><i class="ti tabler-medal me-1"></i>Plata</div>
        </div>
      </div>
      @endif

      {{-- 1er lugar --}}
      @php $pri = $top3->first(); @endphp
      <div class="col-4 text-center">
        <i class="ti tabler-crown text-warning icon-32px mb-1 d-block"></i>
        <div class="avatar avatar-xl mx-auto mb-2">
          <span class="avatar-initial rounded-circle bg-label-warning fs-4">
            {{ strtoupper(substr($pri->unidadOrganica->sigla ?? 'U', 0, 2)) }}
          </span>
        </div>
        <div class="fw-medium">{{ $pri->unidadOrganica->sigla ?? '—' }}</div>
        <div class="small text-muted mb-2">{{ $pri->unidadOrganica->nombre ?? '—' }}</div>
        <div class="bg-warning bg-opacity-10 rounded-top py-3 px-2" style="min-height:120px">
          <div class="badge bg-warning fs-5 mb-1">1°</div>
          <div class="fw-bold text-warning fs-5">{{ $pri->puntaje }}%</div>
          <div class="badge bg-label-warning mt-1"><i class="ti tabler-trophy me-1"></i>Oro</div>
        </div>
      </div>

      {{-- 3er lugar --}}
      @if($top3->count() >= 3)
      @php $ter = $top3->get(2); @endphp
      <div class="col-4 text-center">
        <div class="avatar avatar-xl mx-auto mb-2">
          <span class="avatar-initial rounded-circle bg-label-warning fs-4" style="background-color:rgba(205,127,50,.15)!important;color:#cd7f32!important">
            {{ strtoupper(substr($ter->unidadOrganica->sigla ?? 'U', 0, 2)) }}
          </span>
        </div>
        <div class="fw-medium">{{ $ter->unidadOrganica->sigla ?? '—' }}</div>
        <div class="small text-muted mb-2">{{ $ter->unidadOrganica->nombre ?? '—' }}</div>
        <div class="rounded-top py-3 px-2" style="background:rgba(205,127,50,.08);min-height:60px">
          <div class="badge mb-1" style="background:#cd7f32">3°</div>
          <div class="fw-bold" style="color:#cd7f32">{{ $ter->puntaje }}%</div>
          <div class="badge bg-label-warning mt-1"><i class="ti tabler-medal-2 me-1"></i>Bronce</div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endif

{{-- Tabla completa --}}
<div class="card">
  <div class="card-header"><h5 class="mb-0">Ranking Completo — {{ $mes ? $meses[$mes].' '.$anio : 'Anual '.$anio }}</h5></div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr><th>#</th><th>Unidad Orgánica</th><th>Actividades</th><th>Avance</th><th>Puntaje</th><th>Reconocimiento</th></tr>
        </thead>
        <tbody>
          @foreach($ranking as $r)
          @php
            $mc = match($r->medalla) { 'oro' => 'warning', 'plata' => 'secondary', 'bronce' => 'warning', default => 'info' };
            $pc = $r->avance_global >= 75 ? 'success' : ($r->avance_global >= 50 ? 'warning' : 'danger');
          @endphp
          <tr>
            <td>
              <span class="badge {{ $r->posicion <= 3 ? 'bg-label-warning' : 'bg-label-secondary' }} fs-6">
                {{ $r->posicion }}°
              </span>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <span class="avatar-initial rounded bg-label-primary">{{ substr($r->unidadOrganica->sigla ?? 'U',0,2) }}</span>
                </div>
                <div>
                  <div class="fw-medium">{{ $r->unidadOrganica->sigla ?? '—' }}</div>
                  <small class="text-muted">{{ $r->unidadOrganica->nombre ?? '—' }}</small>
                </div>
              </div>
            </td>
            <td><small>{{ $r->actividades_completadas }}/{{ $r->actividades_total }}</small></td>
            <td style="min-width:120px">
              <div class="d-flex align-items-center gap-1">
                <div class="progress flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-{{ $pc }}" style="width:{{ $r->avance_global }}%"></div>
                </div>
                <small class="fw-bold text-{{ $pc }}">{{ $r->avance_global }}%</small>
              </div>
            </td>
            <td><strong class="text-primary">{{ $r->puntaje }}</strong></td>
            <td>
              @if($r->medalla)
              <span class="badge bg-label-{{ $mc }}">
                <i class="ti tabler-{{ $r->medalla === 'oro' ? 'trophy' : 'medal' }} me-1"></i>{{ ucfirst($r->medalla) }}
              </span>
              @else<span class="text-muted">—</span>@endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

@endsection
