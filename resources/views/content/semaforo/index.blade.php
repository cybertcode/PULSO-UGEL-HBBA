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

@php
  $avance     = round($avance_global);
  $nivel      = $avance >= $umbral_verde  ? 'Cumplido'   : ($avance >= $umbral_amarillo ? 'En proceso' : 'En riesgo');
  $nivelColor = $avance >= $umbral_verde  ? 'success'    : ($avance >= $umbral_amarillo ? 'warning'    : 'danger');
  $cumplidos  = $componentes->where('color', 'success')->count();
  $en_proceso = $componentes->where('color', 'warning')->count();
  $en_riesgo  = $componentes->where('color', 'danger')->count();
  $total      = $componentes->count();

  $colorHex = ['success' => '#28c76f', 'warning' => '#ff9f43', 'danger' => '#ea5455'];
  $colorRgb = ['success' => '40,199,111', 'warning' => '255,159,67', 'danger' => '234,84,85'];
@endphp

{{-- ════════════════════════════════════════════
     CABECERA
════════════════════════════════════════════ --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-6">
  <div>
    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <span class="badge rounded bg-label-{{ $nivelColor }} p-2">
        <i class="icon-base ti tabler-traffic-lights icon-lg text-{{ $nivelColor }}"></i>
      </span>
      Semáforo Institucional
    </h4>
    <p class="text-muted mb-0 ms-1">
      Modelo de Integridad · {{ $total }} componentes ·
      <span class="badge bg-label-secondary rounded-pill ms-1" style="font-size:10px">
        Directiva N° 006-2019-CG-INTEG
      </span>
    </p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('adm-configuracion') }}" class="btn btn-sm btn-label-secondary">
      <i class="ti tabler-settings me-1"></i>Umbrales
    </a>
    <button class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#modalCriterios">
      <i class="ti tabler-info-circle me-1"></i>Criterios
    </button>
  </div>
</div>

{{-- ════════════════════════════════════════════
     FILA HERO — semáforo + gauge + 3 stats
════════════════════════════════════════════ --}}
<div class="row g-6 mb-6">

  {{-- ── Card hero: semáforo + gauge ── --}}
  <div class="col-xl-5 col-lg-6">
    <div class="card h-100 overflow-hidden">

      {{-- Franja de color superior --}}
      <div style="height:4px;background:{{ $colorHex[$nivelColor] }};
                  box-shadow:0 0 20px 2px rgba({{ $colorRgb[$nivelColor] }},.5)"></div>

      <div class="card-body d-flex align-items-center gap-5 py-5 px-5">

        {{-- Semáforo físico --}}
        <div class="flex-shrink-0 position-relative d-flex flex-column align-items-center justify-content-center rounded-4 py-4 px-3"
             style="background:linear-gradient(160deg,#1a1a2e 0%,#12122a 100%);
                    width:80px;gap:14px;
                    box-shadow:0 8px 30px rgba(0,0,0,.4),inset 0 1px 0 rgba(255,255,255,.05)">

          {{-- Tornillo superior --}}
          <div class="position-absolute top-0 start-50 translate-middle-x mt-n1 rounded-circle"
               style="width:10px;height:10px;background:#2d2d4a;border:2px solid #3a3a5c"></div>

          {{-- Luz ROJA --}}
          <div class="rounded-circle position-relative"
               style="width:44px;height:44px;
                      background:{{ $nivelColor==='danger' ? 'radial-gradient(circle at 35% 35%,#ff6b6b,#ea5455)' : 'radial-gradient(circle at 35% 35%,#3a1a1a,#2a1010)' }};
                      box-shadow:{{ $nivelColor==='danger' ? '0 0 20px 6px rgba(234,84,85,.8),inset 0 1px 0 rgba(255,255,255,.2)' : 'inset 0 2px 4px rgba(0,0,0,.5)' }};
                      transition:all .4s ease">
            @if($nivelColor==='danger')
            <div class="position-absolute rounded-circle"
                 style="top:8px;left:10px;width:12px;height:6px;
                        background:rgba(255,255,255,.35);transform:rotate(-30deg);border-radius:50%"></div>
            @endif
          </div>

          {{-- Luz AMARILLA --}}
          <div class="rounded-circle position-relative"
               style="width:44px;height:44px;
                      background:{{ $nivelColor==='warning' ? 'radial-gradient(circle at 35% 35%,#ffd166,#ff9f43)' : 'radial-gradient(circle at 35% 35%,#2e2a10,#1e1a08)' }};
                      box-shadow:{{ $nivelColor==='warning' ? '0 0 20px 6px rgba(255,159,67,.8),inset 0 1px 0 rgba(255,255,255,.2)' : 'inset 0 2px 4px rgba(0,0,0,.5)' }};
                      transition:all .4s ease">
            @if($nivelColor==='warning')
            <div class="position-absolute rounded-circle"
                 style="top:8px;left:10px;width:12px;height:6px;
                        background:rgba(255,255,255,.35);transform:rotate(-30deg);border-radius:50%"></div>
            @endif
          </div>

          {{-- Luz VERDE --}}
          <div class="rounded-circle position-relative"
               style="width:44px;height:44px;
                      background:{{ $nivelColor==='success' ? 'radial-gradient(circle at 35% 35%,#55f5a3,#28c76f)' : 'radial-gradient(circle at 35% 35%,#0d2a1a,#081a10)' }};
                      box-shadow:{{ $nivelColor==='success' ? '0 0 20px 6px rgba(40,199,111,.8),inset 0 1px 0 rgba(255,255,255,.2)' : 'inset 0 2px 4px rgba(0,0,0,.5)' }};
                      transition:all .4s ease">
            @if($nivelColor==='success')
            <div class="position-absolute rounded-circle"
                 style="top:8px;left:10px;width:12px;height:6px;
                        background:rgba(255,255,255,.35);transform:rotate(-30deg);border-radius:50%"></div>
            @endif
          </div>

          {{-- Tornillo inferior --}}
          <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n1 rounded-circle"
               style="width:10px;height:10px;background:#2d2d4a;border:2px solid #3a3a5c"></div>
        </div>

        {{-- Gauge + info --}}
        <div class="flex-grow-1 text-center">
          <div id="gaugeAvance" class="mx-auto"></div>
          <h4 class="fw-bold mb-1 mt-n2" style="color:{{ $colorHex[$nivelColor] }}">{{ $avance }}%</h4>
          <h6 class="card-title mb-2">Cumplimiento General</h6>
          <span class="badge bg-{{ $nivelColor }} rounded-pill px-3 py-1_5 mb-3">
            <i class="ti tabler-{{ $nivelColor==='success' ? 'circle-check' : ($nivelColor==='warning' ? 'clock' : 'alert-triangle') }} me-1"></i>
            {{ $nivel }}
          </span>
          <div class="d-flex justify-content-center gap-4 mt-2">
            <div class="text-center">
              <div class="fw-bold text-muted" style="font-size:11px">UMBRAL VERDE</div>
              <div class="fw-bold text-success">{{ $umbral_verde }}%</div>
            </div>
            <div style="width:1px;background:var(--bs-border-color)"></div>
            <div class="text-center">
              <div class="fw-bold text-muted" style="font-size:11px">UMBRAL AMARILLO</div>
              <div class="fw-bold text-warning">{{ $umbral_amarillo }}%</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- ── 3 stat cards ── --}}
  <div class="col-xl-7 col-lg-6">
    <div class="row g-6 h-100">

      {{-- Cumplido --}}
      <div class="col-12 col-sm-4">
        <div class="card h-100 border-0"
             style="background:linear-gradient(135deg,rgba(40,199,111,.12) 0%,rgba(40,199,111,.04) 100%);
                    border-left:3px solid #28c76f !important;border-left-style:solid !important">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div class="badge rounded bg-label-success p-2">
                <i class="icon-base ti tabler-circle-check icon-lg text-success"></i>
              </div>
              <span class="badge bg-success rounded-pill" style="font-size:10px">
                {{ $total ? round($cumplidos/$total*100) : 0 }}%
              </span>
            </div>
            <h1 class="display-6 fw-bold text-success mb-1">{{ $cumplidos }}</h1>
            <p class="fw-semibold mb-3" style="font-size:12px;color:var(--bs-body-color)">
              COMPONENTES<br>CUMPLIDOS
            </p>
            <div class="progress rounded-pill" style="height:5px">
              <div class="progress-bar bg-success rounded-pill"
                   style="width:{{ $total ? round($cumplidos/$total*100) : 0 }}%"></div>
            </div>
            <small class="text-muted d-block mt-2">Avance ≥ {{ $umbral_verde }}%</small>
          </div>
        </div>
      </div>

      {{-- En proceso --}}
      <div class="col-12 col-sm-4">
        <div class="card h-100 border-0"
             style="background:linear-gradient(135deg,rgba(255,159,67,.12) 0%,rgba(255,159,67,.04) 100%);
                    border-left:3px solid #ff9f43 !important;border-left-style:solid !important">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div class="badge rounded bg-label-warning p-2">
                <i class="icon-base ti tabler-clock icon-lg text-warning"></i>
              </div>
              <span class="badge bg-warning rounded-pill" style="font-size:10px">
                {{ $total ? round($en_proceso/$total*100) : 0 }}%
              </span>
            </div>
            <h1 class="display-6 fw-bold text-warning mb-1">{{ $en_proceso }}</h1>
            <p class="fw-semibold mb-3" style="font-size:12px;color:var(--bs-body-color)">
              COMPONENTES<br>EN PROCESO
            </p>
            <div class="progress rounded-pill" style="height:5px">
              <div class="progress-bar bg-warning rounded-pill"
                   style="width:{{ $total ? round($en_proceso/$total*100) : 0 }}%"></div>
            </div>
            <small class="text-muted d-block mt-2">{{ $umbral_amarillo }}–{{ $umbral_verde - 1 }}%</small>
          </div>
        </div>
      </div>

      {{-- En riesgo --}}
      <div class="col-12 col-sm-4">
        <div class="card h-100 border-0"
             style="background:linear-gradient(135deg,rgba(234,84,85,.12) 0%,rgba(234,84,85,.04) 100%);
                    border-left:3px solid #ea5455 !important;border-left-style:solid !important">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div class="badge rounded bg-label-danger p-2">
                <i class="icon-base ti tabler-alert-triangle icon-lg text-danger"></i>
              </div>
              <span class="badge bg-danger rounded-pill" style="font-size:10px">
                {{ $total ? round($en_riesgo/$total*100) : 0 }}%
              </span>
            </div>
            <h1 class="display-6 fw-bold text-danger mb-1">{{ $en_riesgo }}</h1>
            <p class="fw-semibold mb-3" style="font-size:12px;color:var(--bs-body-color)">
              COMPONENTES<br>EN RIESGO
            </p>
            <div class="progress rounded-pill" style="height:5px">
              <div class="progress-bar bg-danger rounded-pill"
                   style="width:{{ $total ? round($en_riesgo/$total*100) : 0 }}%"></div>
            </div>
            <small class="text-muted d-block mt-2">Avance &lt; {{ $umbral_amarillo }}%</small>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

{{-- ════════════════════════════════════════════
     COMPONENTES SCI
════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h5 class="fw-bold mb-1">Por Componente del Modelo de Integridad</h5>
    <p class="card-subtitle mb-0">Avance de actividades registradas por cada componente SCI</p>
  </div>
  <a href="{{ route('sci-control-interno') }}" class="btn btn-sm btn-label-secondary">
    <i class="ti tabler-clipboard-list me-1"></i>Todas las actividades
  </a>
</div>

<div class="row g-4 mb-6">
  @foreach($componentes as $c)
  @php
    $icon = match($c->color) {
      'success' => 'tabler-circle-check',
      'warning' => 'tabler-clock',
      default   => 'tabler-alert-triangle',
    };
    $hex = $colorHex[$c->color];
    $rgb = $colorRgb[$c->color];
  @endphp
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card h-100 overflow-hidden">
      {{-- Franja superior de color --}}
      <div style="height:3px;background:{{ $hex }};opacity:.8"></div>
      <div class="card-body pb-2">

        {{-- Header: semáforo mini + badge número --}}
        <div class="d-flex align-items-start justify-content-between mb-3">

          {{-- Semáforo mini --}}
          <div class="d-flex flex-column align-items-center justify-content-center rounded-3 py-2 px-2"
               style="background:linear-gradient(160deg,#1a1a2e 0%,#12122a 100%);gap:6px;width:32px;
                      box-shadow:0 4px 14px rgba(0,0,0,.35),inset 0 1px 0 rgba(255,255,255,.04)">
            {{-- Rojo --}}
            <div class="rounded-circle"
                 style="width:16px;height:16px;flex-shrink:0;
                        background:{{ $c->color==='danger'  ? 'radial-gradient(circle at 35% 35%,#ff6b6b,#ea5455)' : '#1e0e0e' }};
                        box-shadow:{{ $c->color==='danger'  ? '0 0 8px 3px rgba(234,84,85,.75)' : 'inset 0 1px 3px rgba(0,0,0,.6)' }}">
            </div>
            {{-- Amarillo --}}
            <div class="rounded-circle"
                 style="width:16px;height:16px;flex-shrink:0;
                        background:{{ $c->color==='warning' ? 'radial-gradient(circle at 35% 35%,#ffd166,#ff9f43)' : '#1e1a08' }};
                        box-shadow:{{ $c->color==='warning' ? '0 0 8px 3px rgba(255,159,67,.75)' : 'inset 0 1px 3px rgba(0,0,0,.6)' }}">
            </div>
            {{-- Verde --}}
            <div class="rounded-circle"
                 style="width:16px;height:16px;flex-shrink:0;
                        background:{{ $c->color==='success' ? 'radial-gradient(circle at 35% 35%,#55f5a3,#28c76f)' : '#081a10' }};
                        box-shadow:{{ $c->color==='success' ? '0 0 8px 3px rgba(40,199,111,.75)' : 'inset 0 1px 3px rgba(0,0,0,.6)' }}">
            </div>
          </div>

          <div class="badge bg-label-secondary rounded-pill" style="font-size:10px">
            Comp. {{ $c->numero }}
          </div>
        </div>

        {{-- Porcentaje --}}
        <div class="d-flex align-items-end gap-2 mb-1">
          <h2 class="fw-bold mb-0" style="color:{{ $hex }};line-height:1">{{ $c->porcentaje }}</h2>
          <span class="fw-semibold text-muted mb-1">%</span>
        </div>

        {{-- Nombre --}}
        <p class="fw-semibold mb-3" style="font-size:12.5px;line-height:1.45;
           display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
          {{ $c->nombre }}
        </p>

        {{-- Barra de progreso --}}
        <div class="progress rounded-pill mb-2" style="height:8px;background:rgba({{ $rgb }},.15)">
          <div class="progress-bar rounded-pill" style="width:{{ $c->porcentaje }}%;background:{{ $hex }}"></div>
        </div>

        {{-- Actividades --}}
        <div class="d-flex align-items-center justify-content-between mt-2">
          <small class="text-muted">
            <i class="ti tabler-checks me-1" style="font-size:11px"></i>
            {{ $c->completadas_count }}/{{ $c->actividades_count }} completadas
          </small>
          <span class="badge bg-label-{{ $c->color }} rounded-pill" style="font-size:10px">{{ $c->semaforo }}</span>
        </div>
      </div>
      <div class="card-footer border-top py-2 px-4"
           style="background:rgba({{ $rgb }},.05)">
        <a href="{{ route('sci-control-interno') }}?componente_id={{ $c->id }}"
           class="d-flex align-items-center justify-content-center gap-1 text-{{ $c->color }} text-decoration-none fw-semibold"
           style="font-size:12px">
          <i class="ti tabler-arrow-right" style="font-size:13px"></i>Ver actividades
        </a>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ════════════════════════════════════════════
     TABLA UNIDADES ORGÁNICAS
════════════════════════════════════════════ --}}
<div class="card">
  <div class="card-header border-bottom d-flex align-items-center justify-content-between py-4">
    <div>
      <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
        <span class="badge rounded bg-label-primary p-1_5">
          <i class="icon-base ti tabler-building-community icon-md text-primary"></i>
        </span>
        Por Unidad Orgánica
      </h5>
      <p class="card-subtitle mb-0">Avance de actividades asignadas por área</p>
    </div>
    <a href="{{ route('mon-ranking-unidades') }}" class="btn btn-sm btn-label-warning">
      <i class="ti tabler-trophy me-1"></i>Ver ranking
    </a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr style="background:var(--bs-tertiary-bg)">
          <th class="ps-4 text-center fw-semibold" style="width:56px;font-size:11px;letter-spacing:.05em">#</th>
          <th class="fw-semibold" style="font-size:11px;letter-spacing:.05em">UNIDAD ORGÁNICA</th>
          <th class="text-center fw-semibold" style="width:120px;font-size:11px;letter-spacing:.05em">ACTIVIDADES</th>
          <th class="fw-semibold" style="min-width:200px;font-size:11px;letter-spacing:.05em">AVANCE</th>
          <th class="text-center pe-4 fw-semibold" style="width:120px;font-size:11px;letter-spacing:.05em">ESTADO</th>
        </tr>
      </thead>
      <tbody>
        @forelse($unidades as $i => $u)
        @php
          $uIcon = match($u->color) {
            'success' => 'tabler-circle-check',
            'warning' => 'tabler-clock',
            default   => 'tabler-alert-triangle',
          };
          $uHex = $colorHex[$u->color];
          $uRgb = $colorRgb[$u->color];
        @endphp
        <tr style="border-left:3px solid {{ $uHex }}">
          <td class="ps-4 text-center">
            <span class="fw-bold" style="font-size:13px;color:{{ $uHex }}">{{ $i + 1 }}</span>
          </td>
          <td>
            <div class="d-flex align-items-center gap-3">
              <div class="badge rounded bg-label-{{ $u->color }} p-1_5">
                <i class="icon-base ti tabler-building icon-sm text-{{ $u->color }}"></i>
              </div>
              <div>
                <p class="fw-semibold mb-0" style="font-size:13.5px">{{ $u->nombre }}</p>
                <small class="text-muted">{{ $u->sigla }}</small>
              </div>
            </div>
          </td>
          <td class="text-center">
            <span class="fw-bold" style="color:{{ $uHex }}">{{ $u->completadas_count }}</span>
            <span class="text-muted fw-normal">/{{ $u->actividades_count }}</span>
          </td>
          <td>
            <div class="d-flex align-items-center gap-3">
              <div class="progress flex-grow-1 rounded-pill" style="height:8px;background:rgba({{ $uRgb }},.15)">
                <div class="progress-bar rounded-pill" style="width:{{ $u->porcentaje }}%;background:{{ $uHex }}"></div>
              </div>
              <span class="fw-bold" style="min-width:38px;font-size:13px;color:{{ $uHex }}">{{ $u->porcentaje }}%</span>
            </div>
          </td>
          <td class="text-center pe-4">
            <span class="badge bg-label-{{ $u->color }} rounded-pill px-2" style="font-size:11px">
              <i class="icon-base ti {{ $uIcon }} icon-12px me-1"></i>{{ $u->semaforo }}
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted py-6">
            <i class="ti tabler-building-off d-block mb-2" style="font-size:2rem;opacity:.3"></i>
            Sin unidades orgánicas registradas
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ════════════════════════════════════════════
     MODAL CRITERIOS
════════════════════════════════════════════ --}}
<div class="modal fade" id="modalCriterios" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">
          <i class="ti tabler-traffic-lights me-2 text-primary"></i>Criterios del Semáforo
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-3 d-flex flex-column gap-3">
        <p class="text-muted small mb-1">
          Los umbrales se configuran en
          <a href="{{ route('adm-configuracion') }}" class="text-primary fw-semibold">Configuración Institucional</a>.
        </p>

        {{-- Verde --}}
        <div class="d-flex align-items-center gap-3 rounded-3 p-3"
             style="background:rgba(40,199,111,.1);border:1px solid rgba(40,199,111,.25)">
          <div style="width:46px;height:46px;border-radius:50%;
                      background:radial-gradient(circle at 35% 35%,#55f5a3,#28c76f);
                      box-shadow:0 0 16px 4px rgba(40,199,111,.5);flex-shrink:0"></div>
          <div>
            <p class="fw-bold text-success mb-0">Verde — Cumplido</p>
            <small class="text-muted">Avance ≥ <strong>{{ $umbral_verde }}%</strong></small>
          </div>
        </div>

        {{-- Amarillo --}}
        <div class="d-flex align-items-center gap-3 rounded-3 p-3"
             style="background:rgba(255,159,67,.1);border:1px solid rgba(255,159,67,.25)">
          <div style="width:46px;height:46px;border-radius:50%;
                      background:radial-gradient(circle at 35% 35%,#ffd166,#ff9f43);
                      box-shadow:0 0 16px 4px rgba(255,159,67,.5);flex-shrink:0"></div>
          <div>
            <p class="fw-bold text-warning mb-0">Amarillo — En proceso</p>
            <small class="text-muted">Entre <strong>{{ $umbral_amarillo }}%</strong> y <strong>{{ $umbral_verde - 1 }}%</strong></small>
          </div>
        </div>

        {{-- Rojo --}}
        <div class="d-flex align-items-center gap-3 rounded-3 p-3"
             style="background:rgba(234,84,85,.1);border:1px solid rgba(234,84,85,.25)">
          <div style="width:46px;height:46px;border-radius:50%;
                      background:radial-gradient(circle at 35% 35%,#ff6b6b,#ea5455);
                      box-shadow:0 0 16px 4px rgba(234,84,85,.5);flex-shrink:0"></div>
          <div>
            <p class="fw-bold text-danger mb-0">Rojo — En riesgo</p>
            <small class="text-muted">Avance &lt; <strong>{{ $umbral_amarillo }}%</strong></small>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-sm btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isDark  = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const avance  = {{ $avance }};
  const colores = { success: '#28c76f', warning: '#ff9f43', danger: '#ea5455' };
  const nivel   = avance >= {{ $umbral_verde }} ? 'success' : (avance >= {{ $umbral_amarillo }} ? 'warning' : 'danger');
  const color   = colores[nivel];

  new ApexCharts(document.getElementById('gaugeAvance'), {
    chart:   { type: 'radialBar', height: 160, sparkline: { enabled: true } },
    series:  [avance],
    plotOptions: {
      radialBar: {
        startAngle: -135, endAngle: 135,
        hollow: { size: '65%' },
        track:  { background: isDark ? '#2d2d4a' : '#e8e8e8', strokeWidth: '97%' },
        dataLabels: {
          name:  { show: false },
          value: { show: false },
        },
      }
    },
    fill:   { colors: [color] },
    stroke: { lineCap: 'round' },
  }).render();
});
</script>
@endsection
