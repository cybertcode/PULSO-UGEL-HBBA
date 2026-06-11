@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Semáforo Institucional — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-style')
<style>
/* ══ Semáforo poste ══ */
.tl-housing {
  background: #1a1a2e;
  border-radius: 10px;
  padding: 8px 6px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  box-shadow: 0 3px 12px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.07);
  border: 1.5px solid #0d0d1a;
}
.tl-pole { width: 7px; height: 22px; background: linear-gradient(to right,#555,#888,#555); margin: 0 auto; border-radius: 0 0 3px 3px; }
.tl-base { width: 30px; height: 7px; background: linear-gradient(to bottom,#888,#555); border-radius: 0 0 5px 5px; }
.tl-bulb { width: 22px; height: 22px; border-radius: 50%; transition: all .2s; }

.tl-bulb.off-red    { background:#3a1010; box-shadow: inset 0 2px 4px rgba(0,0,0,.5); }
.tl-bulb.off-yellow { background:#2a2200; box-shadow: inset 0 2px 4px rgba(0,0,0,.5); }
.tl-bulb.off-green  { background:#0a1f0a; box-shadow: inset 0 2px 4px rgba(0,0,0,.5); }

.tl-bulb.on-red {
  background: radial-gradient(circle at 35% 35%, #ff8a80, #ea5455 45%, #b71c1c);
  box-shadow: 0 0 10px 3px rgba(234,84,85,.65), 0 0 20px 6px rgba(234,84,85,.25), inset 0 1px 2px rgba(255,255,255,.25);
}
.tl-bulb.on-yellow {
  background: radial-gradient(circle at 35% 35%, #ffe082, #ff9f43 45%, #e65100);
  box-shadow: 0 0 10px 3px rgba(255,159,67,.65), 0 0 20px 6px rgba(255,159,67,.25), inset 0 1px 2px rgba(255,255,255,.25);
}
.tl-bulb.on-green {
  background: radial-gradient(circle at 35% 35%, #a5d6a7, #28c76f 45%, #1b5e20);
  box-shadow: 0 0 10px 3px rgba(40,199,111,.65), 0 0 20px 6px rgba(40,199,111,.25), inset 0 1px 2px rgba(255,255,255,.25);
}

@keyframes pulse-red    { 0%,100%{box-shadow:0 0 10px 3px rgba(234,84,85,.65),0 0 20px 6px rgba(234,84,85,.25)} 50%{box-shadow:0 0 16px 6px rgba(234,84,85,.9),0 0 30px 12px rgba(234,84,85,.45)} }
@keyframes pulse-yellow { 0%,100%{box-shadow:0 0 10px 3px rgba(255,159,67,.65),0 0 20px 6px rgba(255,159,67,.25)} 50%{box-shadow:0 0 16px 6px rgba(255,159,67,.9),0 0 30px 12px rgba(255,159,67,.45)} }
@keyframes pulse-green  { 0%,100%{box-shadow:0 0 10px 3px rgba(40,199,111,.65),0 0 20px 6px rgba(40,199,111,.25)} 50%{box-shadow:0 0 16px 6px rgba(40,199,111,.9),0 0 30px 12px rgba(40,199,111,.45)} }

.tl-bulb.on-red    { animation: pulse-red    2.2s ease-in-out infinite; }
.tl-bulb.on-yellow { animation: pulse-yellow 1.3s ease-in-out infinite; }
.tl-bulb.on-green  { animation: pulse-green  2.2s ease-in-out infinite; }

/* mini semáforo (sección header + pills) */
.tl-sm .tl-housing { padding: 4px 4px; gap: 3px; border-radius: 7px; }
.tl-sm .tl-bulb    { width: 13px; height: 13px; }

/* ══ Tarjeta componente ══ */
.sem-card {
  background: #fff;
  border-radius: 12px;
  border: 1.5px solid #e9ecef;
  border-top: 3px solid transparent;
  padding: 0;
  overflow: hidden;
  transition: box-shadow .18s, transform .18s;
  height: 100%;
  display: flex;
  flex-direction: column;
}
.sem-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.1); transform: translateY(-2px); }
.sem-card.c-verde    { border-top-color: #28c76f; }
.sem-card.c-amarillo { border-top-color: #ff9f43; }
.sem-card.c-rojo     { border-top-color: #ea5455; }

.sem-card-head {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px 14px 8px;
}
.sem-card-meta {
  flex-grow: 1;
  min-width: 0;
}
.sem-card-num  { font-size: .65rem; font-weight: 700; color: #c0c0c0; line-height: 1; margin-bottom: 3px; }
.sem-card-name { font-size: .83rem; font-weight: 700; color: #2d3748; line-height: 1.35; }
.sem-card-pct  { font-size: 1.3rem; font-weight: 800; line-height: 1; flex-shrink: 0; }

/* barra progreso */
.sem-bar-wrap { height: 6px; background: #f0f0f0; margin: 0 14px 10px; border-radius: 3px; overflow: hidden; }
.sem-bar-fill { height: 100%; border-radius: 3px; transition: width .6s ease; }

/* stats fila */
.sem-stats-row {
  display: flex;
  gap: 0;
  border-top: 1px solid #f0f0f0;
  margin-top: auto;
}
.sem-stat-item {
  flex: 1;
  text-align: center;
  padding: 7px 4px;
  border-right: 1px solid #f0f0f0;
  min-width: 0;
}
.sem-stat-item:last-child { border-right: none; }
.sem-stat-val  { font-size: .8rem; font-weight: 800; line-height: 1; }
.sem-stat-lbl  { font-size: .6rem; color: #aaa; font-weight: 600; text-transform: uppercase; letter-spacing: .02em; }

/* alertas fila */
.sem-alerts-row {
  display: flex;
  gap: 6px;
  padding: 6px 14px 10px;
  flex-wrap: wrap;
}
.sem-badge-mini {
  font-size: .63rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 3px;
  white-space: nowrap;
}
.sem-badge-vencida  { background: rgba(234,84,85,.12);  color: #ea5455; }
.sem-badge-alta     { background: rgba(255,159,67,.12);  color: #ff9f43; }
.sem-badge-fecha    { background: rgba(105,108,255,.1);  color: #696cff; }
.sem-badge-obs      { background: rgba(3,195,236,.12);   color: #03c3ec; }

/* estado badge */
.sem-estado-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: .72rem;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 20px;
  margin: 0 14px 10px;
}
.sem-estado-badge.b-verde    { background: rgba(40,199,111,.12);  color: #28c76f; }
.sem-estado-badge.b-amarillo { background: rgba(255,159,67,.12);  color: #ff9f43; }
.sem-estado-badge.b-rojo     { background: rgba(234,84,85,.12);   color: #ea5455; }

/* ══ Resumen global ══ */
.sem-global {
  background: #fff;
  border-radius: 14px;
  border: 1.5px solid #e9ecef;
  padding: 20px 24px;
  display: flex;
  align-items: center;
  gap: 28px;
  flex-wrap: wrap;
  margin-bottom: 24px;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.sem-gauge-wrap { position: relative; width: 150px; height: 150px; flex-shrink: 0; }
.sem-gauge-label {
  position: absolute; bottom: 18px; left: 50%;
  transform: translateX(-50%);
  text-align: center; line-height: 1.1; white-space: nowrap;
}
.sem-gauge-pct   { font-size: 1.75rem; font-weight: 800; }
.sem-gauge-nivel { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #aaa; }

.sem-pills { display: flex; gap: 10px; flex-wrap: wrap; }
.sem-pill {
  display: flex; align-items: center; gap: 9px;
  padding: 9px 16px; border-radius: 10px; min-width: 105px;
}
.sem-pill-count { font-size: 1.5rem; font-weight: 800; line-height: 1; }
.sem-pill-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
.pill-verde    { background: rgba(40,199,111,.1); }
.pill-amarillo { background: rgba(255,159,67,.1); }
.pill-rojo     { background: rgba(234,84,85,.1); }

/* ══ Sección eje/etapa ══ */
.sem-section {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 14px; border-radius: 10px; margin-bottom: 14px;
  cursor: pointer; user-select: none;
}
.sem-section.sh-verde    { background: rgba(40,199,111,.08); }
.sem-section.sh-amarillo { background: rgba(255,159,67,.08); }
.sem-section.sh-rojo     { background: rgba(234,84,85,.08); }
.sem-section-title { font-size: .9rem; font-weight: 700; flex-grow: 1; color: #2d3748; }
.sem-section-pct   { font-size: .95rem; font-weight: 800; }

/* colores */
.col-verde    { color: #28c76f !important; }
.col-amarillo { color: #ff9f43 !important; }
.col-rojo     { color: #ea5455 !important; }

/* nav tabs — fix icono invisible en active */
#tabsSemaforo .nav-link .ti {
  display: inline-block;
  vertical-align: middle;
  background-color: currentColor !important;
}
</style>
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Semáforo Institucional</li>
  </ol>
</nav>

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Semáforo Institucional</h4>
    <p class="text-muted mb-0" style="font-size:13px">Estado de cumplimiento por módulo, eje/etapa y componente.</p>
  </div>
  <div class="d-flex align-items-center gap-2">
    <label class="form-label mb-0 fw-semibold" style="font-size:13px">Año:</label>
    <select class="form-select form-select-sm" style="width:90px"
      onchange="window.location.href='{{ route('sci-semaforo') }}?anio='+this.value">
      @foreach($anios as $a)
        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
      @endforeach
    </select>
    <a href="{{ route('adm-configuracion') }}" class="btn btn-sm btn-label-secondary">
      <span class="ti tabler-settings me-1"></span>Umbrales
    </a>
  </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-pills mb-4 gap-2" id="tabsSemaforo" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active fw-semibold px-4" id="tab-sci" data-bs-toggle="pill"
      data-bs-target="#pane-sci" type="button" role="tab">
      Control Interno
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link fw-semibold px-4" id="tab-int" data-bs-toggle="pill"
      data-bs-target="#pane-int" type="button" role="tab">
      Modelo de Integridad
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link fw-semibold px-4" id="tab-uni" data-bs-toggle="pill"
      data-bs-target="#pane-uni" type="button" role="tab">
      Por Unidad Orgánica
    </button>
  </li>
</ul>

<div class="tab-content">

  {{-- ══════════ TAB SCI ══════════ --}}
  <div class="tab-pane fade show active" id="pane-sci" role="tabpanel">
    @php
      $sciCls   = $sciAvance >= $umbral_verde ? 'verde' : ($sciAvance >= $umbral_amarillo ? 'amarillo' : 'rojo');
      $sciNivel = $sciAvance >= $umbral_verde ? 'Bueno' : ($sciAvance >= $umbral_amarillo ? 'Regular' : 'En riesgo');
      $sciV = $sciA = $sciR = 0;
      foreach ($sciEjes as $e) foreach ($e->componentes as $c) {
        if ($c->color==='success') $sciV++;
        elseif ($c->color==='warning') $sciA++;
        else $sciR++;
      }
      $sciVencidas   = $sciEjes->flatMap(fn($e) => $e->componentes)->sum('vencidas_count');
      $sciEnProceso  = $sciEjes->flatMap(fn($e) => $e->componentes)->sum('en_proceso_count');
      $sciAltaPrio   = $sciEjes->flatMap(fn($e) => $e->componentes)->sum('alta_prioridad');
    @endphp

    {{-- Resumen global SCI --}}
    <div class="sem-global">
      <div class="sem-gauge-wrap">
        <div id="chartSCI"></div>
        <div class="sem-gauge-label">
          <div class="sem-gauge-pct col-{{ $sciCls }}">{{ $sciAvance }}%</div>
          <div class="sem-gauge-nivel">{{ $sciNivel }}</div>
        </div>
      </div>
      <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-3 mb-1 flex-wrap">
          <span class="fw-bold" style="font-size:1rem">Cumplimiento — Control Interno {{ $anio }}</span>
          @if($sciVencidas > 0)
            <span class="sem-badge-mini sem-badge-vencida">{{ $sciVencidas }} vencida{{ $sciVencidas>1?'s':'' }}</span>
          @endif
          @if($sciAltaPrio > 0)
            <span class="sem-badge-mini sem-badge-alta">{{ $sciAltaPrio }} alta prioridad</span>
          @endif
        </div>
        <div class="text-muted mb-3" style="font-size:12px">
          <strong>{{ $sciTotales['completadas'] }}</strong> completadas de <strong>{{ $sciTotales['total'] }}</strong> actividades
          &nbsp;·&nbsp; <strong>{{ $sciEnProceso }}</strong> en proceso
          &nbsp;·&nbsp; Umbral verde ≥{{ $umbral_verde }}% · amarillo ≥{{ $umbral_amarillo }}%
        </div>
        <div class="sem-pills">
          <div class="sem-pill pill-verde">
            <div style="display:flex;flex-direction:column;align-items:center">
              <div class="tl-sm" style="display:flex;flex-direction:column;align-items:center">
                <div class="tl-housing">
                  <div class="tl-bulb off-red"></div>
                  <div class="tl-bulb off-yellow"></div>
                  <div class="tl-bulb on-green"></div>
                </div>
              </div>
            </div>
            <div>
              <div class="sem-pill-count col-verde">{{ $sciV }}</div>
              <div class="sem-pill-label col-verde">Cumplido</div>
            </div>
          </div>
          <div class="sem-pill pill-amarillo">
            <div style="display:flex;flex-direction:column;align-items:center">
              <div class="tl-sm" style="display:flex;flex-direction:column;align-items:center">
                <div class="tl-housing">
                  <div class="tl-bulb off-red"></div>
                  <div class="tl-bulb on-yellow"></div>
                  <div class="tl-bulb off-green"></div>
                </div>
              </div>
            </div>
            <div>
              <div class="sem-pill-count col-amarillo">{{ $sciA }}</div>
              <div class="sem-pill-label col-amarillo">En proceso</div>
            </div>
          </div>
          <div class="sem-pill pill-rojo">
            <div style="display:flex;flex-direction:column;align-items:center">
              <div class="tl-sm" style="display:flex;flex-direction:column;align-items:center">
                <div class="tl-housing">
                  <div class="tl-bulb on-red"></div>
                  <div class="tl-bulb off-yellow"></div>
                  <div class="tl-bulb off-green"></div>
                </div>
              </div>
            </div>
            <div>
              <div class="sem-pill-count col-rojo">{{ $sciR }}</div>
              <div class="sem-pill-label col-rojo">En riesgo</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Ejes SCI --}}
    @php $nSCI = 0; @endphp
    @forelse($sciEjes as $eje)
    @php $ejeCls = $eje->color==='success' ? 'verde' : ($eje->color==='warning' ? 'amarillo' : 'rojo'); @endphp
    <div class="mb-4">
      <div class="sem-section sh-{{ $ejeCls }}" data-bs-toggle="collapse" data-bs-target="#sci-eje-{{ $eje->id }}">
        <div style="display:flex;flex-direction:column;align-items:center">
          <div class="tl-sm" style="display:flex;flex-direction:column;align-items:center">
            <div class="tl-housing">
              <div class="tl-bulb {{ $ejeCls==='rojo'    ? 'on-red'    : 'off-red' }}"></div>
              <div class="tl-bulb {{ $ejeCls==='amarillo'? 'on-yellow' : 'off-yellow' }}"></div>
              <div class="tl-bulb {{ $ejeCls==='verde'   ? 'on-green'  : 'off-green' }}"></div>
            </div>
          </div>
        </div>
        <span class="sem-section-title">{{ $eje->nombre }}</span>
        <span class="sem-section-pct col-{{ $ejeCls }}">{{ (int)$eje->porcentaje }}%</span>
        <span class="badge {{ $ejeCls==='verde'?'bg-success':($ejeCls==='amarillo'?'bg-warning':'bg-danger') }}" style="font-size:.68rem">{{ $eje->semaforo }}</span>
        <span class="text-muted" style="font-size:12px">{{ $eje->componentes->count() }} componentes</span>
        <span class="ti tabler-chevron-down text-muted" style="font-size:1rem"></span>
      </div>

      <div class="collapse show" id="sci-eje-{{ $eje->id }}">
        <div class="row g-3">
          @foreach($eje->componentes as $comp)
          @php
            $nSCI++;
            $cc       = $comp->color==='success' ? 'verde' : ($comp->color==='warning' ? 'amarillo' : 'rojo');
            $barColor = $cc==='verde' ? '#28c76f' : ($cc==='amarillo' ? '#ff9f43' : '#ea5455');
            $pendTotal= ($comp->pendiente_count ?? 0) + ($comp->en_proceso_count ?? 0);
          @endphp
          <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="sem-card c-{{ $cc }}">
              {{-- Cabecera --}}
              <div class="sem-card-head">
                <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0">
                  <div class="tl-housing">
                    <div class="tl-bulb {{ $cc==='rojo'    ? 'on-red'    : 'off-red' }}"></div>
                    <div class="tl-bulb {{ $cc==='amarillo'? 'on-yellow' : 'off-yellow' }}"></div>
                    <div class="tl-bulb {{ $cc==='verde'   ? 'on-green'  : 'off-green' }}"></div>
                  </div>
                  <div class="tl-pole"></div>
                  <div class="tl-base"></div>
                </div>
                <div class="sem-card-meta">
                  <div class="sem-card-num">{{ $nSCI }}.</div>
                  <div class="sem-card-name" title="{{ $comp->nombre }}">{{ $comp->nombre }}</div>
                </div>
                <div class="sem-card-pct col-{{ $cc }}">{{ $comp->porcentaje }}%</div>
              </div>

              {{-- Barra --}}
              <div class="sem-bar-wrap">
                <div class="sem-bar-fill" style="width:{{ $comp->porcentaje }}%; background:{{ $barColor }}"></div>
              </div>

              {{-- Estado badge --}}
              <div class="sem-estado-badge b-{{ $cc }}">
                {{ $comp->semaforo }}
              </div>

              {{-- Alertas --}}
              @if(($comp->vencidas_count??0) > 0 || ($comp->alta_prioridad??0) > 0 || ($comp->proxima_fecha??null) || ($comp->observado_count??0) > 0)
              <div class="sem-alerts-row">
                @if(($comp->vencidas_count??0) > 0)
                  <span class="sem-badge-mini sem-badge-vencida">{{ $comp->vencidas_count }} vencida{{ $comp->vencidas_count>1?'s':'' }}</span>
                @endif
                @if(($comp->observado_count??0) > 0)
                  <span class="sem-badge-mini sem-badge-obs">{{ $comp->observado_count }} obs.</span>
                @endif
                @if(($comp->alta_prioridad??0) > 0)
                  <span class="sem-badge-mini sem-badge-alta">{{ $comp->alta_prioridad }} urgente{{ $comp->alta_prioridad>1?'s':'' }}</span>
                @endif
                @if($comp->proxima_fecha??null)
                  <span class="sem-badge-mini sem-badge-fecha">
                    Vence {{ \Carbon\Carbon::parse($comp->proxima_fecha)->format('d/m') }}
                  </span>
                @endif
              </div>
              @else
              <div style="height:10px"></div>
              @endif

              {{-- Stats --}}
              <div class="sem-stats-row">
                <div class="sem-stat-item">
                  <div class="sem-stat-val text-success">{{ $comp->completadas_count }}</div>
                  <div class="sem-stat-lbl">Hecho</div>
                </div>
                <div class="sem-stat-item">
                  <div class="sem-stat-val text-warning">{{ $comp->en_proceso_count ?? 0 }}</div>
                  <div class="sem-stat-lbl">Proceso</div>
                </div>
                <div class="sem-stat-item">
                  <div class="sem-stat-val text-secondary">{{ $comp->pendiente_count ?? 0 }}</div>
                  <div class="sem-stat-lbl">Pend.</div>
                </div>
                <div class="sem-stat-item">
                  <div class="sem-stat-val col-rojo">{{ $comp->vencidas_count ?? 0 }}</div>
                  <div class="sem-stat-lbl">Venc.</div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    @empty
    <div class="alert alert-info">No hay ejes SCI registrados para el año {{ $anio }}.</div>
    @endforelse
  </div>

  {{-- ══════════ TAB INTEGRIDAD ══════════ --}}
  <div class="tab-pane fade" id="pane-int" role="tabpanel">
    @php
      $intCls   = $intAvance >= $umbral_verde ? 'verde' : ($intAvance >= $umbral_amarillo ? 'amarillo' : 'rojo');
      $intNivel = $intAvance >= $umbral_verde ? 'Bueno' : ($intAvance >= $umbral_amarillo ? 'Regular' : 'En riesgo');
      $intV = $intA = $intR = 0;
      foreach ($integridadEtapas as $et) foreach ($et->componentes as $c) {
        if ($c->color==='success') $intV++;
        elseif ($c->color==='warning') $intA++;
        else $intR++;
      }
      $intVencidas  = $integridadEtapas->flatMap(fn($e) => $e->componentes)->sum('vencidas_count');
      $intEnProceso = $integridadEtapas->flatMap(fn($e) => $e->componentes)->sum('en_proceso_count');
      $intAltaPrio  = $integridadEtapas->flatMap(fn($e) => $e->componentes)->sum('alta_prioridad');
    @endphp

    <div class="sem-global">
      <div class="sem-gauge-wrap">
        <div id="chartINT"></div>
        <div class="sem-gauge-label">
          <div class="sem-gauge-pct col-{{ $intCls }}">{{ $intAvance }}%</div>
          <div class="sem-gauge-nivel">{{ $intNivel }}</div>
        </div>
      </div>
      <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-3 mb-1 flex-wrap">
          <span class="fw-bold" style="font-size:1rem">Cumplimiento — Modelo de Integridad {{ $anio }}</span>
          @if($intVencidas > 0)
            <span class="sem-badge-mini sem-badge-vencida">{{ $intVencidas }} vencida{{ $intVencidas>1?'s':'' }}</span>
          @endif
          @if($intAltaPrio > 0)
            <span class="sem-badge-mini sem-badge-alta">{{ $intAltaPrio }} alta prioridad</span>
          @endif
        </div>
        <div class="text-muted mb-3" style="font-size:12px">
          <strong>{{ $intTotales['completadas'] }}</strong> completadas de <strong>{{ $intTotales['total'] }}</strong> actividades
          &nbsp;·&nbsp; <strong>{{ $intEnProceso }}</strong> en proceso
          &nbsp;·&nbsp; Umbral verde ≥{{ $umbral_verde }}% · amarillo ≥{{ $umbral_amarillo }}%
        </div>
        <div class="sem-pills">
          <div class="sem-pill pill-verde">
            <div style="display:flex;flex-direction:column;align-items:center">
              <div class="tl-sm" style="display:flex;flex-direction:column;align-items:center">
                <div class="tl-housing">
                  <div class="tl-bulb off-red"></div>
                  <div class="tl-bulb off-yellow"></div>
                  <div class="tl-bulb on-green"></div>
                </div>
              </div>
            </div>
            <div>
              <div class="sem-pill-count col-verde">{{ $intV }}</div>
              <div class="sem-pill-label col-verde">Cumplido</div>
            </div>
          </div>
          <div class="sem-pill pill-amarillo">
            <div style="display:flex;flex-direction:column;align-items:center">
              <div class="tl-sm" style="display:flex;flex-direction:column;align-items:center">
                <div class="tl-housing">
                  <div class="tl-bulb off-red"></div>
                  <div class="tl-bulb on-yellow"></div>
                  <div class="tl-bulb off-green"></div>
                </div>
              </div>
            </div>
            <div>
              <div class="sem-pill-count col-amarillo">{{ $intA }}</div>
              <div class="sem-pill-label col-amarillo">En proceso</div>
            </div>
          </div>
          <div class="sem-pill pill-rojo">
            <div style="display:flex;flex-direction:column;align-items:center">
              <div class="tl-sm" style="display:flex;flex-direction:column;align-items:center">
                <div class="tl-housing">
                  <div class="tl-bulb on-red"></div>
                  <div class="tl-bulb off-yellow"></div>
                  <div class="tl-bulb off-green"></div>
                </div>
              </div>
            </div>
            <div>
              <div class="sem-pill-count col-rojo">{{ $intR }}</div>
              <div class="sem-pill-label col-rojo">En riesgo</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @php $nINT = 0; @endphp
    @forelse($integridadEtapas as $etapa)
    @php $etapaCls = $etapa->color==='success' ? 'verde' : ($etapa->color==='warning' ? 'amarillo' : 'rojo'); @endphp
    <div class="mb-4">
      <div class="sem-section sh-{{ $etapaCls }}" data-bs-toggle="collapse" data-bs-target="#int-etapa-{{ $etapa->id }}">
        <div style="display:flex;flex-direction:column;align-items:center">
          <div class="tl-sm" style="display:flex;flex-direction:column;align-items:center">
            <div class="tl-housing">
              <div class="tl-bulb {{ $etapaCls==='rojo'    ? 'on-red'    : 'off-red' }}"></div>
              <div class="tl-bulb {{ $etapaCls==='amarillo'? 'on-yellow' : 'off-yellow' }}"></div>
              <div class="tl-bulb {{ $etapaCls==='verde'   ? 'on-green'  : 'off-green' }}"></div>
            </div>
          </div>
        </div>
        <span class="sem-section-title">{{ $etapa->nombre }}</span>
        <span class="sem-section-pct col-{{ $etapaCls }}">{{ (int)$etapa->porcentaje }}%</span>
        <span class="badge {{ $etapaCls==='verde'?'bg-success':($etapaCls==='amarillo'?'bg-warning':'bg-danger') }}" style="font-size:.68rem">{{ $etapa->semaforo }}</span>
        <span class="text-muted" style="font-size:12px">{{ $etapa->componentes->count() }} componentes</span>
        <span class="ti tabler-chevron-down text-muted" style="font-size:1rem"></span>
      </div>

      <div class="collapse show" id="int-etapa-{{ $etapa->id }}">
        <div class="row g-3">
          @foreach($etapa->componentes as $comp)
          @php
            $nINT++;
            $cc       = $comp->color==='success' ? 'verde' : ($comp->color==='warning' ? 'amarillo' : 'rojo');
            $barColor = $cc==='verde' ? '#28c76f' : ($cc==='amarillo' ? '#ff9f43' : '#ea5455');
          @endphp
          <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="sem-card c-{{ $cc }}">
              <div class="sem-card-head">
                <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0">
                  <div class="tl-housing">
                    <div class="tl-bulb {{ $cc==='rojo'    ? 'on-red'    : 'off-red' }}"></div>
                    <div class="tl-bulb {{ $cc==='amarillo'? 'on-yellow' : 'off-yellow' }}"></div>
                    <div class="tl-bulb {{ $cc==='verde'   ? 'on-green'  : 'off-green' }}"></div>
                  </div>
                  <div class="tl-pole"></div>
                  <div class="tl-base"></div>
                </div>
                <div class="sem-card-meta">
                  <div class="sem-card-num">{{ $nINT }}.</div>
                  <div class="sem-card-name" title="{{ $comp->nombre }}">{{ $comp->nombre }}</div>
                </div>
                <div class="sem-card-pct col-{{ $cc }}">{{ $comp->porcentaje }}%</div>
              </div>

              <div class="sem-bar-wrap">
                <div class="sem-bar-fill" style="width:{{ $comp->porcentaje }}%; background:{{ $barColor }}"></div>
              </div>

              <div class="sem-estado-badge b-{{ $cc }}">
                {{ $comp->semaforo }}
              </div>

              @if(($comp->vencidas_count??0) > 0 || ($comp->alta_prioridad??0) > 0 || ($comp->proxima_fecha??null) || ($comp->observado_count??0) > 0)
              <div class="sem-alerts-row">
                @if(($comp->vencidas_count??0) > 0)
                  <span class="sem-badge-mini sem-badge-vencida">{{ $comp->vencidas_count }} vencida{{ $comp->vencidas_count>1?'s':'' }}</span>
                @endif
                @if(($comp->observado_count??0) > 0)
                  <span class="sem-badge-mini sem-badge-obs">{{ $comp->observado_count }} obs.</span>
                @endif
                @if(($comp->alta_prioridad??0) > 0)
                  <span class="sem-badge-mini sem-badge-alta">{{ $comp->alta_prioridad }} urgente{{ $comp->alta_prioridad>1?'s':'' }}</span>
                @endif
                @if($comp->proxima_fecha??null)
                  <span class="sem-badge-mini sem-badge-fecha">
                    Vence {{ \Carbon\Carbon::parse($comp->proxima_fecha)->format('d/m') }}
                  </span>
                @endif
              </div>
              @else
              <div style="height:10px"></div>
              @endif

              <div class="sem-stats-row">
                <div class="sem-stat-item">
                  <div class="sem-stat-val text-success">{{ $comp->completadas_count }}</div>
                  <div class="sem-stat-lbl">Hecho</div>
                </div>
                <div class="sem-stat-item">
                  <div class="sem-stat-val text-warning">{{ $comp->en_proceso_count ?? 0 }}</div>
                  <div class="sem-stat-lbl">Proceso</div>
                </div>
                <div class="sem-stat-item">
                  <div class="sem-stat-val text-secondary">{{ $comp->pendiente_count ?? 0 }}</div>
                  <div class="sem-stat-lbl">Pend.</div>
                </div>
                <div class="sem-stat-item">
                  <div class="sem-stat-val col-rojo">{{ $comp->vencidas_count ?? 0 }}</div>
                  <div class="sem-stat-lbl">Venc.</div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    @empty
    <div class="alert alert-info">No hay etapas de Integridad registradas para el año {{ $anio }}.</div>
    @endforelse
  </div>

  {{-- ══════════ TAB UNIDADES ══════════ --}}
  <div class="tab-pane fade" id="pane-uni" role="tabpanel">
    <div class="row g-3">
      @forelse($unidades as $u)
      @php
        $uc       = $u->color==='success' ? 'verde' : ($u->color==='warning' ? 'amarillo' : 'rojo');
        $uBar     = $uc==='verde' ? '#28c76f' : ($uc==='amarillo' ? '#ff9f43' : '#ea5455');
        $uVenc    = $u->vencidas_count ?? 0;
        $uProc    = $u->en_proceso_count ?? 0;
        $uPend    = $u->pendiente_count ?? 0;
      @endphp
      <div class="col-sm-6 col-xl-4">
        <div class="sem-card c-{{ $uc }}">
          <div class="sem-card-head">
            <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0">
              <div class="tl-housing">
                <div class="tl-bulb {{ $uc==='rojo'    ? 'on-red'    : 'off-red' }}"></div>
                <div class="tl-bulb {{ $uc==='amarillo'? 'on-yellow' : 'off-yellow' }}"></div>
                <div class="tl-bulb {{ $uc==='verde'   ? 'on-green'  : 'off-green' }}"></div>
              </div>
              <div class="tl-pole"></div>
              <div class="tl-base"></div>
            </div>
            <div class="sem-card-meta">
              <div class="sem-card-name" title="{{ $u->nombre }}">{{ $u->nombre }}</div>
              @if($u->sigla ?? false)<div style="font-size:.68rem;color:#bbb;font-weight:700">{{ $u->sigla }}</div>@endif
            </div>
            <div class="sem-card-pct col-{{ $uc }}">{{ $u->porcentaje }}%</div>
          </div>
          <div class="sem-bar-wrap">
            <div class="sem-bar-fill" style="width:{{ $u->porcentaje }}%; background:{{ $uBar }}"></div>
          </div>
          <div class="sem-estado-badge b-{{ $uc }}">
            {{ $uc==='verde'?'Cumplido':($uc==='amarillo'?'En proceso':'En riesgo') }}
          </div>
          <div style="height:6px"></div>
          <div class="sem-stats-row">
            <div class="sem-stat-item">
              <div class="sem-stat-val text-success">{{ $u->completadas_count }}</div>
              <div class="sem-stat-lbl">Hecho</div>
            </div>
            <div class="sem-stat-item">
              <div class="sem-stat-val text-warning">{{ $uProc }}</div>
              <div class="sem-stat-lbl">Proceso</div>
            </div>
            <div class="sem-stat-item">
              <div class="sem-stat-val text-secondary">{{ $uPend }}</div>
              <div class="sem-stat-lbl">Pend.</div>
            </div>
            <div class="sem-stat-item">
              <div class="sem-stat-val col-rojo">{{ $uVenc }}</div>
              <div class="sem-stat-lbl">Venc.</div>
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12"><div class="alert alert-info">No hay unidades orgánicas registradas.</div></div>
      @endforelse
    </div>
  </div>

</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  function makeGauge(id, pct, hex) {
    var el = document.getElementById(id);
    if (!el || typeof ApexCharts === 'undefined') return;
    new ApexCharts(el, {
      chart: { type: 'radialBar', height: 150, sparkline: { enabled: true } },
      plotOptions: {
        radialBar: {
          startAngle: -130, endAngle: 130,
          hollow: { size: '58%' },
          track: { background: '#f0f0f0', strokeWidth: '100%' },
          dataLabels: { show: false }
        }
      },
      series: [pct],
      colors: [hex],
      stroke: { lineCap: 'round' },
    }).render();
  }

  var sciHex = '{{ $sciAvance >= $umbral_verde ? "#28c76f" : ($sciAvance >= $umbral_amarillo ? "#ff9f43" : "#ea5455") }}';
  var intHex = '{{ $intAvance >= $umbral_verde ? "#28c76f" : ($intAvance >= $umbral_amarillo ? "#ff9f43" : "#ea5455") }}';

  makeGauge('chartSCI', {{ $sciAvance }}, sciHex);
  document.getElementById('tab-int')?.addEventListener('shown.bs.tab', function () {
    makeGauge('chartINT', {{ $intAvance }}, intHex);
  });

  // Parpadeo realista: amarillo intermitente, rojo parpadeo lento
  document.querySelectorAll('.tl-bulb.on-yellow').forEach(function (b) {
    setInterval(function () {
      b.classList.remove('on-yellow');
      setTimeout(function () { b.classList.add('on-yellow'); }, 160);
    }, 1400);
  });

  document.querySelectorAll('.tl-bulb.on-red').forEach(function (b) {
    setInterval(function () {
      b.classList.remove('on-red');
      setTimeout(function () { b.classList.add('on-red'); }, 160);
    }, 3000);
  });

});
</script>
@endsection
