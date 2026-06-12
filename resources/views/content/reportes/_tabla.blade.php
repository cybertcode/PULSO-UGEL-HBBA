@php use Illuminate\Support\Str; @endphp

@if($actividades->isEmpty())
{{-- ── Empty state ─────────────────────────────────────────── --}}
<div class="text-center py-5 px-4">
  <div class="empty-icon bg-label-secondary mx-auto mb-3">
    <i class="ti tabler-chart-bar-off text-muted" style="font-size:2rem"></i>
  </div>
  <h6 class="fw-bold mb-1">Sin resultados</h6>
  <p class="text-muted mb-3" style="font-size:.83rem">
    No se encontraron actividades con los filtros seleccionados.
  </p>
  <button id="btnLimpiarEmpty" class="btn btn-sm btn-label-primary">
    <i class="ti tabler-refresh me-1"></i>Limpiar filtros
  </button>
</div>

@else
<div class="table-responsive">
  <table class="table table-hover align-middle mb-0 rep-table">
    <thead class="table-light">
      <tr>
        <th class="ps-4" style="width:90px">Código</th>
        <th>Actividad / Componente</th>
        <th style="width:105px;text-align:center">Módulo</th>
        <th style="width:110px">Unidad</th>
        <th style="width:95px">Fecha límite</th>
        <th style="width:140px">Avance</th>
        <th style="width:120px">Estado</th>
        <th style="width:60px;text-align:center">Sem.</th>
      </tr>
    </thead>
    <tbody>
      @foreach($actividades as $a)
      @php
        [$ec, $elabel, $eicon] = match($a->estado) {
          'completada' => ['success',   'Completada',  'tabler-circle-check'],
          'en_proceso' => ['warning',   'En Proceso',  'tabler-loader-2'],
          'vencida'    => ['danger',    'Vencida',     'tabler-alarm-off'],
          'observado'  => ['info',      'Observado',   'tabler-eye'],
          default      => ['secondary', 'Pendiente',   'tabler-clock-pause'],
        };
        $sc    = $a->avance >= 75 ? '#28c76f' : ($a->avance >= 50 ? '#ff9f43' : '#ea5455');
        $stip  = $a->avance >= 75 ? 'Bueno' : ($a->avance >= 50 ? 'Regular' : 'Bajo');
        $comp  = $a->modulo === 'integridad'
          ? $a->integridadPregunta?->componente?->nombre
          : $a->sciPregunta?->componente?->nombre;
        $esVen = $a->estado !== 'completada' && $a->fecha_limite < now();
        $dias  = (int) round(now()->diffInDays($a->fecha_limite, false));
      @endphp
      <tr>

        {{-- Código --}}
        <td class="ps-4">
          <code class="text-muted" style="font-size:.7rem;background:rgba(0,0,0,.04);padding:.1rem .35rem;border-radius:4px">
            {{ $a->codigo ?? '—' }}
          </code>
        </td>

        {{-- Actividad + componente --}}
        <td>
          <div class="fw-semibold lh-sm mb-1" style="font-size:.84rem;max-width:280px" title="{{ $a->nombre }}">
            {{ Str::limit($a->nombre, 58) }}
          </div>
          @if($comp)
          <div class="d-flex align-items-center gap-1 text-muted" style="font-size:.71rem">
            <i class="ti tabler-layout-grid" style="font-size:.75rem"></i>
            {{ Str::limit($comp, 45) }}
          </div>
          @endif
        </td>

        {{-- Módulo --}}
        <td class="text-center">
          @if($a->modulo === 'sci')
            <span class="mod-badge-sci"><i class="ti tabler-shield-check me-1"></i>SCI</span>
          @else
            <span class="mod-badge-int"><i class="ti tabler-heart-handshake me-1"></i>Integr.</span>
          @endif
        </td>

        {{-- Unidad --}}
        <td>
          @if($a->unidadOrganica)
            <span class="fw-semibold" style="font-size:.78rem" title="{{ $a->unidadOrganica->nombre }}">
              {{ $a->unidadOrganica->sigla }}
            </span>
          @else
            <span class="text-muted" style="font-size:.78rem">—</span>
          @endif
        </td>

        {{-- Fecha límite --}}
        <td>
          <div class="d-flex flex-column gap-1">
            <span style="font-size:.78rem" class="{{ $esVen ? 'text-danger fw-semibold':'' }}">
              {{ $a->fecha_limite->format('d/m/Y') }}
            </span>
            @if($a->estado !== 'completada')
              @if($esVen)
                <span class="dias-chip bg-label-danger text-danger">
                  <i class="ti tabler-alert-triangle" style="font-size:.68rem"></i>
                  {{ abs($dias) }}d venc.
                </span>
              @elseif($dias <= 7)
                <span class="dias-chip bg-label-warning text-warning">
                  <i class="ti tabler-clock-hour-4" style="font-size:.68rem"></i>
                  {{ $dias }}d rest.
                </span>
              @else
                <span class="dias-chip bg-label-secondary text-muted">{{ $dias }}d</span>
              @endif
            @else
              <span class="dias-chip bg-label-success text-success">
                <i class="ti tabler-check" style="font-size:.68rem"></i>OK
              </span>
            @endif
          </div>
        </td>

        {{-- Avance --}}
        <td>
          <div class="d-flex align-items-center gap-2">
            <div class="avance-bar-wrap flex-shrink-0">
              <div class="avance-bar-fill" style="width:{{ $a->avance }}%;background:{{ $sc }}"></div>
            </div>
            <span class="fw-bold" style="font-size:.8rem;color:{{ $sc }};min-width:28px;text-align:right">
              {{ $a->avance }}%
            </span>
          </div>
        </td>

        {{-- Estado --}}
        <td>
          <span class="estado-pill bg-label-{{ $ec }} text-{{ $ec }}">
            <i class="ti {{ $eicon }} me-1" style="font-size:.72rem"></i>{{ $elabel }}
          </span>
        </td>

        {{-- Semáforo --}}
        <td class="text-center">
          <span class="sem-dot" style="background:{{ $sc }}" title="{{ $stip }}"></span>
        </td>

      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif
