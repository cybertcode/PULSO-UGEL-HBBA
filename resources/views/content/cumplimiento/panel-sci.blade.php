@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Panel SCI — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-style')
<style>
.kpi-card { border-radius: 14px; border: none; overflow: hidden; transition: transform .18s, box-shadow .18s; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.10); }
.kpi-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: rgba(255,255,255,.22); }
.kpi-value { font-size: 2.2rem; font-weight: 700; line-height: 1; color: #fff; }
.kpi-label { font-size: .72rem; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: rgba(255,255,255,.80); }
.kpi-sub   { font-size: .78rem; color: rgba(255,255,255,.65); }
.kpi-grad-blue   { background: linear-gradient(135deg,#667eea,#764ba2); }
.kpi-grad-green  { background: linear-gradient(135deg,#11998e,#38ef7d); }
.kpi-grad-red    { background: linear-gradient(135deg,#e52d27,#b31217); }
.kpi-grad-orange { background: linear-gradient(135deg,#f7971e,#ffd200); }
.section-card { border-radius: 14px; border: 1px solid rgba(0,0,0,.06); }
.section-card .card-header { border-radius: 14px 14px 0 0; background: transparent; border-bottom: 1px solid rgba(0,0,0,.06); }
.list-row { transition: background .12s; }
.list-row:hover { background: rgba(0,0,0,.02); }
</style>
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
    <h4 class="mb-1 fw-bold">Panel de Control SCI</h4>
    <p class="mb-0 text-muted">Resumen ejecutivo de cumplimiento — {{ $hoy->format('d \d\e F Y') }}</p>
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
    <div class="card kpi-card kpi-grad-blue">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="kpi-icon"><i class="ti tabler-clipboard-list"></i></div>
          <span class="badge" style="background:rgba(255,255,255,.25);color:#fff;">{{ now()->year }}</span>
        </div>
        <div class="kpi-value mb-1">{{ $kpis['total'] }}</div>
        <div class="kpi-label">Total Actividades</div>
        <div class="kpi-sub mt-1">Registradas este año</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card kpi-card kpi-grad-green">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="kpi-icon"><i class="ti tabler-circle-check"></i></div>
          <span class="badge" style="background:rgba(255,255,255,.25);color:#fff;">{{ $kpis['porcentaje_global'] }}%</span>
        </div>
        <div class="kpi-value mb-1">{{ $kpis['completadas'] }}</div>
        <div class="kpi-label">Completadas</div>
        <div class="progress mt-2" style="height:5px;background:rgba(255,255,255,.25)">
          <div class="progress-bar" style="width:{{ $kpis['porcentaje_global'] }}%;background:#fff"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card kpi-card kpi-grad-red">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="kpi-icon"><i class="ti tabler-clock-x"></i></div>
          @if($kpis['vencidas'] > 0)
          <span class="badge" style="background:rgba(255,255,255,.25);color:#fff;">Crítico</span>
          @endif
        </div>
        <div class="kpi-value mb-1">{{ $kpis['vencidas'] }}</div>
        <div class="kpi-label">Vencidas</div>
        <div class="kpi-sub mt-1">Sin completar tras el plazo</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card kpi-card kpi-grad-orange">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="kpi-icon"><i class="ti tabler-file-off"></i></div>
          @if($kpis['sin_ev'] > 0)
          <span class="badge" style="background:rgba(255,255,255,.25);color:#fff;">Pendiente</span>
          @endif
        </div>
        <div class="kpi-value mb-1" style="color:#fff">{{ $kpis['sin_ev'] }}</div>
        <div class="kpi-label">Sin Evidencia</div>
        <div class="kpi-sub mt-1">Con avance pero sin docs</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  {{-- Incumplidores --}}
  <div class="col-xl-6">
    <div class="card section-card h-100">
      <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-semibold"><i class="ti tabler-alert-triangle text-danger me-2"></i>Responsables con más incumplimientos</h6>
        <a href="{{ route('cumplimiento.responsables') }}" class="btn btn-sm btn-label-secondary">Ver todos</a>
      </div>
      <div class="card-body p-0">
        @forelse($incumplidores as $u)
        <div class="d-flex align-items-center px-4 py-3 border-bottom list-row">
          <div class="avatar avatar-sm me-3 flex-shrink-0">
            @if($u->profile_photo_path)
              <img src="{{ Storage::url($u->profile_photo_path) }}" class="rounded-circle" alt="">
            @else
              <div class="avatar-initial rounded-circle bg-label-danger">{{ strtoupper(substr($u->name,0,1)) }}</div>
            @endif
          </div>
          <div class="flex-grow-1 min-width-0">
            <div class="fw-medium text-truncate">{{ $u->name }}</div>
            <small class="text-muted">{{ $u->inc_unidad }} · {{ $u->cargo?->nombre ?? 'Sin cargo' }}</small>
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
          <i class="ti tabler-circle-check icon-36px d-block mb-2"></i>
          <span class="fw-medium">Sin incumplimientos registrados</span>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Avance por unidad --}}
  <div class="col-xl-6">
    <div class="card section-card h-100">
      <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-semibold"><i class="ti tabler-building-community text-primary me-2"></i>Estado por Unidad Orgánica</h6>
        <a href="{{ route('mon-avance-unidades') }}" class="btn btn-sm btn-label-secondary">Detalle</a>
      </div>
      <div class="card-body p-0">
        @foreach($avance_unidades as $u)
        <div class="px-4 py-3 border-bottom list-row">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-medium small">{{ $u->nombre }}</span>
            <span class="badge bg-label-{{ $u->semaforo }}">{{ $u->porcentaje }}%</span>
          </div>
          <div class="progress" style="height:7px;border-radius:4px">
            <div class="progress-bar bg-{{ $u->semaforo }}" style="width:{{ $u->porcentaje }}%;border-radius:4px"></div>
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
    <div class="card section-card">
      <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-semibold"><i class="ti tabler-calendar-exclamation text-warning me-2"></i>Vencen en los próximos 15 días</h6>
        <span class="badge bg-warning rounded-pill">{{ $proximas->count() }}</span>
      </div>
      <div class="card-body p-0">
        @forelse($proximas as $act)
        @php $dias = (int) round($act->fecha_limite->diffInDays(now(), false) * -1); @endphp
        <div class="d-flex align-items-start px-4 py-3 border-bottom list-row">
          <div class="flex-grow-1 min-width-0">
            <div class="fw-medium text-truncate small">{{ $act->nombre }}</div>
            <small class="text-muted">{{ $act->unidadOrganica?->sigla }} · {{ $act->responsables->first()?->name ?? '—' }}</small>
          </div>
          <div class="ms-3 text-nowrap text-end">
            <div class="badge {{ $dias <= 3 ? 'bg-danger' : 'bg-warning' }}">
              {{ $dias }}d
            </div>
            <div><small class="text-muted">{{ $act->fecha_limite->format('d/m/Y') }}</small></div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">
          <i class="ti tabler-calendar-check icon-36px d-block mb-2 text-success"></i>
          Sin vencimientos próximos
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Vencidas recientes --}}
  <div class="col-xl-6">
    <div class="card section-card">
      <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-semibold"><i class="ti tabler-clock-x text-danger me-2"></i>Vencidas (últimos 30 días)</h6>
        <a href="{{ route('cumplimiento.sin-evidencia') }}" class="btn btn-sm btn-label-danger">Ver sin evidencia</a>
      </div>
      <div class="card-body p-0">
        @forelse($vencidas as $act)
        @php $diasRetraso = (int) round(now()->diffInDays($act->fecha_limite)); @endphp
        <div class="d-flex align-items-start px-4 py-3 border-bottom list-row">
          <div class="flex-grow-1 min-width-0">
            <div class="fw-medium text-truncate small">{{ $act->nombre }}</div>
            <small class="text-muted">{{ $act->unidadOrganica?->sigla }} · {{ $act->responsables->first()?->name ?? '—' }}</small>
          </div>
          <div class="ms-3 text-nowrap text-end">
            <div class="badge bg-danger">+{{ $diasRetraso }}d</div>
            <div><small class="text-muted">{{ $act->fecha_limite->format('d/m/Y') }}</small></div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">
          <i class="ti tabler-circle-check icon-36px d-block mb-2 text-success"></i>
          Sin vencimientos recientes
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@endsection
