@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Panel SCI — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Panel de Control SCI</li>
  </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="mb-1">Panel de Control SCI</h4>
    <p class="mb-0 text-muted">Resumen ejecutivo de cumplimiento — {{ $hoy->format('d/m/Y') }}</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('cumplimiento.responsables') }}" class="btn btn-label-primary btn-sm">
      <i class="ti tabler-users me-1"></i>Por Responsable
    </a>
    <a href="{{ route('cumplimiento.sin-evidencia') }}" class="btn btn-label-warning btn-sm">
      <i class="ti tabler-file-off me-1"></i>Sin Evidencia
    </a>
  </div>
</div>

{{-- KPIs principales --}}
<div class="row g-4 mb-4">
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="badge bg-label-primary rounded p-2"><i class="ti tabler-clipboard-list icon-24px"></i></div>
          <span class="badge bg-label-primary rounded-pill">{{ now()->year }}</span>
        </div>
        <h3 class="mb-1 text-primary">{{ $kpis['total'] }}</h3>
        <p class="mb-0 fw-semibold">Total Actividades</p>
        <small class="text-muted">Año {{ now()->year }}</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="badge bg-label-success rounded p-2"><i class="ti tabler-circle-check icon-24px"></i></div>
          <span class="badge bg-label-success rounded-pill">{{ $kpis['porcentaje_global'] }}%</span>
        </div>
        <h3 class="mb-1 text-success">{{ $kpis['completadas'] }}</h3>
        <p class="mb-0 fw-semibold">Completadas</p>
        <div class="progress mt-2" style="height:6px">
          <div class="progress-bar bg-success" style="width:{{ $kpis['porcentaje_global'] }}%"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card h-100 border-danger border-opacity-25">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="badge bg-label-danger rounded p-2"><i class="ti tabler-clock-x icon-24px"></i></div>
          @if($kpis['vencidas'] > 0)
          <span class="badge bg-danger rounded-pill">Crítico</span>
          @endif
        </div>
        <h3 class="mb-1 text-danger">{{ $kpis['vencidas'] }}</h3>
        <p class="mb-0 fw-semibold">Vencidas</p>
        <small class="text-muted">Sin completar tras el plazo</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card h-100 border-warning border-opacity-25">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="badge bg-label-warning rounded p-2"><i class="ti tabler-file-off icon-24px"></i></div>
          @if($kpis['sin_ev'] > 0)
          <span class="badge bg-warning rounded-pill">Pendiente</span>
          @endif
        </div>
        <h3 class="mb-1 text-warning">{{ $kpis['sin_ev'] }}</h3>
        <p class="mb-0 fw-semibold">Sin Evidencia</p>
        <small class="text-muted">Con avance pero sin docs</small>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  {{-- Incumplidores --}}
  <div class="col-xl-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="ti tabler-alert-triangle text-danger me-2"></i>Responsables con más incumplimientos</h6>
        <a href="{{ route('cumplimiento.responsables') }}" class="btn btn-sm btn-label-secondary">Ver todos</a>
      </div>
      <div class="card-body p-0">
        @forelse($incumplidores as $u)
        @php $pct = $u->inc_vencidas + $u->inc_sin_ev > 0 ? min(100, ($u->inc_vencidas/max(1,$u->inc_total))*100) : 0; @endphp
        <div class="d-flex align-items-center px-4 py-3 border-bottom">
          <div class="avatar avatar-sm me-3 flex-shrink-0">
            @if($u->profile_photo_path)
              <img src="{{ Storage::url($u->profile_photo_path) }}" class="rounded-circle" alt="">
            @else
              <div class="avatar-initial rounded-circle bg-label-danger">{{ strtoupper(substr($u->name,0,1)) }}</div>
            @endif
          </div>
          <div class="flex-grow-1 min-width-0">
            <div class="fw-medium text-truncate">{{ $u->name }}</div>
            <small class="text-muted">{{ $u->inc_unidad }} · {{ $u->cargo ?? 'Sin cargo' }}</small>
          </div>
          <div class="d-flex gap-2 ms-3 text-nowrap">
            @if($u->inc_vencidas > 0)
            <span class="badge bg-label-danger">{{ $u->inc_vencidas }} venc.</span>
            @endif
            @if($u->inc_sin_ev > 0)
            <span class="badge bg-label-warning">{{ $u->inc_sin_ev }} s/ev.</span>
            @endif
          </div>
        </div>
        @empty
        <div class="text-center text-success py-5">
          <i class="ti tabler-circle-check icon-32px d-block mb-2"></i>
          <span class="fw-medium">Sin incumplimientos registrados</span>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Avance por unidad --}}
  <div class="col-xl-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="ti tabler-building-community text-primary me-2"></i>Estado por Unidad Orgánica</h6>
        <a href="{{ route('mon-avance-unidades') }}" class="btn btn-sm btn-label-secondary">Detalle</a>
      </div>
      <div class="card-body p-0">
        @foreach($avance_unidades as $u)
        <div class="px-4 py-3 border-bottom">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-medium">{{ $u->nombre }}</span>
            <span class="badge bg-label-{{ $u->semaforo }}">{{ $u->porcentaje }}%</span>
          </div>
          <div class="progress" style="height:8px">
            <div class="progress-bar bg-{{ $u->semaforo }}" style="width:{{ $u->porcentaje }}%"></div>
          </div>
          <div class="d-flex justify-content-between mt-1">
            <small class="text-muted">{{ $u->completadas_act }}/{{ $u->total_act }} completadas</small>
            @if($u->vencidas_act > 0)
            <small class="text-danger"><i class="ti tabler-clock-x icon-12px me-1"></i>{{ $u->vencidas_act }} vencidas</small>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  {{-- Próximas a vencer --}}
  <div class="col-xl-6">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="ti tabler-calendar-exclamation text-warning me-2"></i>Vencen en los próximos 15 días</h6>
        <span class="badge bg-warning">{{ $proximas->count() }}</span>
      </div>
      <div class="card-body p-0">
        @forelse($proximas as $act)
        @php $dias = now()->diffInDays($act->fecha_limite, false); @endphp
        <div class="d-flex align-items-start px-4 py-3 border-bottom">
          <div class="flex-grow-1 min-width-0">
            <div class="fw-medium text-truncate">{{ $act->nombre }}</div>
            <small class="text-muted">{{ $act->unidadOrganica?->sigla }} · {{ $act->responsables->first()?->name ?? '—' }}</small>
          </div>
          <div class="ms-3 text-nowrap text-end">
            <div class="badge {{ $dias <= 3 ? 'bg-danger' : 'bg-warning' }}">
              {{ $dias }} día{{ $dias != 1 ? 's' : '' }}
            </div>
            <div><small class="text-muted">{{ $act->fecha_limite->format('d/m/Y') }}</small></div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">
          <i class="ti tabler-calendar-check icon-32px d-block mb-2 text-success"></i>
          Sin vencimientos próximos
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Vencidas recientes --}}
  <div class="col-xl-6">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="ti tabler-clock-x text-danger me-2"></i>Vencidas (últimos 30 días)</h6>
        <a href="{{ route('cumplimiento.sin-evidencia') }}" class="btn btn-sm btn-label-danger">Ver sin evidencia</a>
      </div>
      <div class="card-body p-0">
        @forelse($vencidas as $act)
        @php $diasRetraso = now()->diffInDays($act->fecha_limite); @endphp
        <div class="d-flex align-items-start px-4 py-3 border-bottom">
          <div class="flex-grow-1 min-width-0">
            <div class="fw-medium text-truncate">{{ $act->nombre }}</div>
            <small class="text-muted">{{ $act->unidadOrganica?->sigla }} · {{ $act->responsables->first()?->name ?? '—' }}</small>
          </div>
          <div class="ms-3 text-nowrap text-end">
            <div class="badge bg-danger">+{{ $diasRetraso }}d</div>
            <div><small class="text-muted">{{ $act->fecha_limite->format('d/m/Y') }}</small></div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">
          <i class="ti tabler-circle-check icon-32px d-block mb-2 text-success"></i>
          Sin vencimientos recientes
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@endsection
