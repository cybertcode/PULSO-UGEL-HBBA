@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Semáforo Institucional — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Semáforo Institucional</li>
  </ol>
</nav>

{{-- Cabecera + filtro año --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-5">
  <div>
    <h4 class="fw-bold mb-1">Semáforo Institucional</h4>
    <p class="text-muted mb-0">Estado de cumplimiento por módulo, eje/etapa y componente.</p>
  </div>
  <div class="d-flex align-items-center gap-2">
    <label class="form-label mb-0 fw-semibold" style="font-size:13px">Año:</label>
    <select id="filtroAnioSemaforo" class="form-select form-select-sm" style="width:100px"
      onchange="window.location.href='{{ route('sci-semaforo') }}?anio='+this.value">
      @foreach($anios as $a)
      <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
      @endforeach
    </select>
    <a href="{{ route('adm-configuracion') }}" class="btn btn-sm btn-label-secondary">
      <i class="ti tabler-settings me-1"></i>Umbrales
    </a>
  </div>
</div>

{{-- Tabs SCI / Integridad --}}
<ul class="nav nav-pills nav-fill mb-4 gap-2" id="tabsSemaforo" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active fw-semibold" id="tab-sci" data-bs-toggle="pill"
      data-bs-target="#pane-sci" type="button" role="tab">
      <i class="ti tabler-shield-check me-2"></i>Sistema de Control Interno
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link fw-semibold" id="tab-int" data-bs-toggle="pill"
      data-bs-target="#pane-int" type="button" role="tab">
      <i class="ti tabler-balance me-2"></i>Modelo de Integridad
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link fw-semibold" id="tab-uni" data-bs-toggle="pill"
      data-bs-target="#pane-uni" type="button" role="tab">
      <i class="ti tabler-building-community me-2"></i>Por Unidad Orgánica
    </button>
  </li>
</ul>

<div class="tab-content" id="tabsSemaforoContent">

  {{-- ══════════════════ TAB SCI ══════════════════ --}}
  <div class="tab-pane fade show active" id="pane-sci" role="tabpanel">

    {{-- Avance global SCI --}}
    @php
      $sciNivel      = $sciAvance >= $umbral_verde ? 'Cumplido' : ($sciAvance >= $umbral_amarillo ? 'En proceso' : 'En riesgo');
      $sciNivelColor = $sciAvance >= $umbral_verde ? 'success'  : ($sciAvance >= $umbral_amarillo ? 'warning'    : 'danger');
    @endphp
    <div class="card mb-5 border-top border-4 border-{{ $sciNivelColor }}">
      <div class="card-body d-flex align-items-center gap-4 py-4">
        <div class="sem-circle-wrap">
          <div class="sem-circle bg-label-{{ $sciNivelColor }}" style="--sem-pct:{{ $sciAvance }}">
            <span class="sem-pct text-{{ $sciNivelColor }}">{{ $sciAvance }}%</span>
          </div>
        </div>
        <div class="flex-grow-1">
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-{{ $sciNivelColor }} fs-6">{{ $sciNivel }}</span>
            <span class="text-muted" style="font-size:13px">Avance SCI {{ $anio }}</span>
          </div>
          <div class="progress" style="height:12px;border-radius:8px">
            <div class="progress-bar bg-{{ $sciNivelColor }}" style="width:{{ $sciAvance }}%"></div>
          </div>
          <div class="row g-3 mt-2">
            <div class="col-auto">
              <div class="text-muted small">Total actividades</div>
              <div class="fw-bold">{{ $sciTotales['total'] }}</div>
            </div>
            <div class="col-auto">
              <div class="text-muted small">Completadas</div>
              <div class="fw-bold text-success">{{ $sciTotales['completadas'] }}</div>
            </div>
            <div class="col-auto">
              <div class="text-muted small">Umbral verde</div>
              <div class="fw-bold text-success">≥ {{ $umbral_verde }}%</div>
            </div>
            <div class="col-auto">
              <div class="text-muted small">Umbral amarillo</div>
              <div class="fw-bold text-warning">≥ {{ $umbral_amarillo }}%</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Ejes y componentes SCI --}}
    @forelse($sciEjes as $eje)
    <div class="card mb-4 border-start border-4 border-{{ $eje->color }}">
      <div class="card-header d-flex align-items-center justify-content-between py-3 cursor-pointer"
        data-bs-toggle="collapse" data-bs-target="#sci-eje-{{ $eje->id }}">
        <div class="d-flex align-items-center gap-3">
          <div class="sem-mini bg-label-{{ $eje->color }}">
            <span class="text-{{ $eje->color }} fw-bold" style="font-size:13px">{{ (int)$eje->porcentaje }}%</span>
          </div>
          <div>
            <div class="fw-semibold">{{ $eje->nombre }}</div>
            <small class="text-muted">{{ $eje->componentes->count() }} componentes · Año {{ $eje->anio }}</small>
          </div>
        </div>
        <span class="badge bg-label-{{ $eje->color }}">{{ $eje->semaforo }}</span>
      </div>
      <div class="collapse show" id="sci-eje-{{ $eje->id }}">
        <div class="card-body p-3">
          <div class="row g-3">
            @foreach($eje->componentes as $comp)
            <div class="col-md-4 col-lg-3">
              <div class="card border-top border-3 border-{{ $comp->color }} h-100">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="ti {{ $comp->icono ?? 'tabler-point' }} text-{{ $comp->color }}"></i>
                    <span class="fw-semibold" style="font-size:13px" title="{{ $comp->nombre }}">
                      {{ Str::limit($comp->nombre, 28) }}
                    </span>
                  </div>
                  <div class="progress mb-2" style="height:8px;border-radius:6px">
                    <div class="progress-bar bg-{{ $comp->color }}" style="width:{{ $comp->porcentaje }}%"></div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">{{ $comp->completadas_count }}/{{ $comp->actividades_count }}</small>
                    <span class="badge bg-label-{{ $comp->color }}" style="font-size:11px">{{ $comp->porcentaje }}%</span>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="alert alert-info">
      <i class="ti tabler-info-circle me-2"></i>No hay ejes SCI registrados para el año {{ $anio }}.
    </div>
    @endforelse

  </div>

  {{-- ══════════════════ TAB INTEGRIDAD ══════════════════ --}}
  <div class="tab-pane fade" id="pane-int" role="tabpanel">

    @php
      $intNivel      = $intAvance >= $umbral_verde ? 'Cumplido' : ($intAvance >= $umbral_amarillo ? 'En proceso' : 'En riesgo');
      $intNivelColor = $intAvance >= $umbral_verde ? 'success'  : ($intAvance >= $umbral_amarillo ? 'warning'    : 'danger');
    @endphp
    <div class="card mb-5 border-top border-4 border-{{ $intNivelColor }}">
      <div class="card-body d-flex align-items-center gap-4 py-4">
        <div class="sem-circle-wrap">
          <div class="sem-circle bg-label-{{ $intNivelColor }}" style="--sem-pct:{{ $intAvance }}">
            <span class="sem-pct text-{{ $intNivelColor }}">{{ $intAvance }}%</span>
          </div>
        </div>
        <div class="flex-grow-1">
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-{{ $intNivelColor }} fs-6">{{ $intNivel }}</span>
            <span class="text-muted" style="font-size:13px">Avance Integridad {{ $anio }}</span>
          </div>
          <div class="progress" style="height:12px;border-radius:8px">
            <div class="progress-bar bg-{{ $intNivelColor }}" style="width:{{ $intAvance }}%"></div>
          </div>
          <div class="row g-3 mt-2">
            <div class="col-auto">
              <div class="text-muted small">Total actividades</div>
              <div class="fw-bold">{{ $intTotales['total'] }}</div>
            </div>
            <div class="col-auto">
              <div class="text-muted small">Completadas</div>
              <div class="fw-bold text-success">{{ $intTotales['completadas'] }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @forelse($integridadEtapas as $etapa)
    <div class="card mb-4 border-start border-4 border-{{ $etapa->color }}">
      <div class="card-header d-flex align-items-center justify-content-between py-3 cursor-pointer"
        data-bs-toggle="collapse" data-bs-target="#int-etapa-{{ $etapa->id }}">
        <div class="d-flex align-items-center gap-3">
          <div class="sem-mini bg-label-{{ $etapa->color }}">
            <span class="text-{{ $etapa->color }} fw-bold" style="font-size:13px">{{ (int)$etapa->porcentaje }}%</span>
          </div>
          <div>
            <div class="fw-semibold">{{ $etapa->nombre }}</div>
            <small class="text-muted">{{ $etapa->componentes->count() }} componentes · Año {{ $etapa->anio }}</small>
          </div>
        </div>
        <span class="badge bg-label-{{ $etapa->color }}">{{ $etapa->semaforo }}</span>
      </div>
      <div class="collapse show" id="int-etapa-{{ $etapa->id }}">
        <div class="card-body p-3">
          <div class="row g-3">
            @foreach($etapa->componentes as $comp)
            <div class="col-md-4 col-lg-3">
              <div class="card border-top border-3 border-{{ $comp->color }} h-100">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="ti {{ $comp->icono ?? 'tabler-point' }} text-{{ $comp->color }}"></i>
                    <span class="fw-semibold" style="font-size:13px" title="{{ $comp->nombre }}">
                      {{ Str::limit($comp->nombre, 28) }}
                    </span>
                  </div>
                  <div class="progress mb-2" style="height:8px;border-radius:6px">
                    <div class="progress-bar bg-{{ $comp->color }}" style="width:{{ $comp->porcentaje }}%"></div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">{{ $comp->completadas_count }}/{{ $comp->actividades_count }}</small>
                    <span class="badge bg-label-{{ $comp->color }}" style="font-size:11px">{{ $comp->porcentaje }}%</span>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="alert alert-info">
      <i class="ti tabler-info-circle me-2"></i>No hay etapas de Integridad registradas para el año {{ $anio }}.
    </div>
    @endforelse

  </div>

  {{-- ══════════════════ TAB UNIDADES ══════════════════ --}}
  <div class="tab-pane fade" id="pane-uni" role="tabpanel">
    <div class="row g-4">
      @forelse($unidades as $u)
      <div class="col-md-6 col-xl-4">
        <div class="card border-start border-4 border-{{ $u->color }} h-100">
          <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="avatar avatar-sm bg-label-{{ $u->color }} rounded">
                <span class="fw-bold text-{{ $u->color }}" style="font-size:11px">{{ $u->sigla ?? '—' }}</span>
              </div>
              <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold text-truncate" title="{{ $u->nombre }}">{{ $u->nombre }}</div>
                <small class="text-muted">{{ $u->completadas_count }}/{{ $u->actividades_count }} completadas</small>
              </div>
              <span class="badge bg-label-{{ $u->color }}">{{ $u->porcentaje }}%</span>
            </div>
            <div class="progress" style="height:10px;border-radius:8px">
              <div class="progress-bar bg-{{ $u->color }}" style="width:{{ $u->porcentaje }}%"></div>
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12">
        <div class="alert alert-info"><i class="ti tabler-info-circle me-2"></i>No hay unidades orgánicas registradas.</div>
      </div>
      @endforelse
    </div>
  </div>

</div>

@endsection

@section('page-style')
<style>
.sem-circle-wrap { flex-shrink: 0; }
.sem-circle {
  width: 100px; height: 100px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}
.sem-pct { font-size: 1.4rem; font-weight: 700; }
.sem-mini {
  width: 64px; height: 48px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
}
.cursor-pointer { cursor: pointer; }
</style>
@endsection
