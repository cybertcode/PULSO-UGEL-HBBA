@php use Illuminate\Support\Str; @endphp
{{-- Partial: cards del concurso de buenas prácticas (dos niveles: UGEL + Externo) --}}

@forelse($practicas as $p)

{{-- ══════════════════════════════════════════════════════════════
     TAB: presentados — SCI ve proyectos nuevos para recepcionar
══════════════════════════════════════════════════════════════ --}}
@if($tab === 'presentados' && $esGestor)
<div class="col-12 col-md-6 col-xl-4">
  <div class="bp-card card h-100 border-start border-4 border-info">
    <div class="px-4 pt-4 pb-2">
      <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
        <div class="flex-grow-1 min-w-0">
          <h6 class="fw-bold mb-1" title="{{ $p->titulo }}">{{ $p->titulo }}</h6>
          <div class="d-flex flex-wrap gap-1">
            <span class="bp-badge bg-label-{{ $p->modulo_color }}">
              <i class="ti tabler-{{ $p->modulo === 'sci' ? 'shield-check' : 'award' }} me-1"></i>{{ $p->modulo_label }}
            </span>
            <span class="bp-badge bg-label-secondary">{{ $p->categoria_label }}</span>
          </div>
        </div>
        <span class="estado-pill bg-label-info flex-shrink-0">
          <i class="ti tabler-send me-1"></i>Presentado
        </span>
      </div>
      @if($p->descripcion)
      <p class="text-muted bp-desc mb-3">{{ Str::limit($p->descripcion, 130) }}</p>
      @endif
    </div>
    <div class="px-4 pb-3">
      <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-user text-primary"></i></div>
          <div class="min-w-0">
            <div class="bp-meta-label">Participante</div>
            <div class="bp-meta-value fw-bold">{{ optional($p->propuestoPor)->name ?? '—' }}</div>
          </div>
        </div>
        @if($p->unidadOrganica)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-building text-secondary"></i></div>
          <div class="min-w-0">
            <div class="bp-meta-label">Unidad</div>
            <div class="bp-meta-value">{{ $p->unidadOrganica->sigla ?? $p->unidadOrganica->nombre }}</div>
          </div>
        </div>
        @endif
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-calendar text-info"></i></div>
          <div>
            <div class="bp-meta-label">Presentado el</div>
            <div class="bp-meta-value">{{ $p->created_at->format('d/m/Y H:i') }}</div>
          </div>
        </div>
        @if($p->archivo_proyecto)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-file-download text-success"></i></div>
          <div class="min-w-0">
            <div class="bp-meta-label">Archivo adjunto</div>
            <a href="{{ asset('storage/' . $p->archivo_proyecto) }}" target="_blank"
              class="bp-meta-value text-primary d-flex align-items-center gap-1">
              <i class="ti tabler-download" style="font-size:.8rem"></i>Descargar proyecto
            </a>
          </div>
        </div>
        @endif
      </div>
    </div>
    <div class="bp-actions">
      <button class="btn btn-sm btn-primary flex-grow-1 btn-recepcionar"
        data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}"
        data-participante="{{ optional($p->propuestoPor)->name }}">
        <i class="ti tabler-inbox me-1"></i>Recepcionar
      </button>
      <button class="btn btn-sm btn-icon btn-outline-secondary btn-ver-detalle"
        data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}"
        data-descripcion="{{ $p->descripcion }}"
        data-participante="{{ optional($p->propuestoPor)->name }}" title="Ver descripción">
        <i class="ti tabler-eye"></i>
      </button>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     TAB: recepcionados — SCI evalúa elegibilidad
══════════════════════════════════════════════════════════════ --}}
@elseif($tab === 'recepcionados' && $esGestor)
<div class="col-12 col-md-6 col-xl-4">
  <div class="bp-card card h-100 border-start border-4 border-primary">
    <div class="px-4 pt-4 pb-2">
      <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
        <div class="flex-grow-1 min-w-0">
          <h6 class="fw-bold mb-1" title="{{ $p->titulo }}">{{ $p->titulo }}</h6>
          <div class="d-flex flex-wrap gap-1">
            <span class="bp-badge bg-label-{{ $p->modulo_color }}">
              <i class="ti tabler-{{ $p->modulo === 'sci' ? 'shield-check' : 'award' }} me-1"></i>{{ $p->modulo_label }}
            </span>
            <span class="bp-badge bg-label-secondary">{{ $p->categoria_label }}</span>
          </div>
        </div>
        <span class="estado-pill bg-label-primary flex-shrink-0">
          <i class="ti tabler-inbox me-1"></i>Recepcionado
        </span>
      </div>
      @if($p->descripcion)
      <p class="text-muted bp-desc mb-3">{{ Str::limit($p->descripcion, 110) }}</p>
      @endif
    </div>
    <div class="px-4 pb-3">
      <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-user text-primary"></i></div>
          <div class="min-w-0">
            <div class="bp-meta-label">Participante</div>
            <div class="bp-meta-value fw-bold">{{ optional($p->propuestoPor)->name ?? '—' }}</div>
          </div>
        </div>
        @if($p->numero_expediente)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-file-description text-primary"></i></div>
          <div>
            <div class="bp-meta-label">N° Expediente</div>
            <div class="bp-meta-value fw-bold">{{ $p->numero_expediente }}</div>
          </div>
        </div>
        @endif
        @if($p->fecha_recepcion)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-calendar-check text-success"></i></div>
          <div>
            <div class="bp-meta-label">Fecha recepción</div>
            <div class="bp-meta-value">{{ $p->fecha_recepcion->format('d/m/Y') }}</div>
          </div>
        </div>
        @endif
      </div>
    </div>
    <div class="bp-actions">
      <button class="btn btn-sm btn-success flex-grow-1 btn-elegible"
        data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}"
        data-participante="{{ optional($p->propuestoPor)->name }}">
        <i class="ti tabler-tournament me-1"></i>Declarar Elegible
      </button>
      <button class="btn btn-sm btn-outline-danger btn-no-elegible"
        data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}">
        <i class="ti tabler-circle-x me-1"></i>No Elegible
      </button>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     TAB: elegibles — Comisión elige al ganador UGEL
══════════════════════════════════════════════════════════════ --}}
@elseif($tab === 'elegibles' && $esGestor)
<div class="col-12 col-md-6 col-xl-4">
  <div class="bp-card card h-100 border-start border-4 border-warning">
    <div class="px-4 pt-4 pb-2">
      <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
        <div class="flex-grow-1 min-w-0">
          <h6 class="fw-bold mb-1" title="{{ $p->titulo }}">{{ $p->titulo }}</h6>
          <div class="d-flex flex-wrap gap-1">
            <span class="bp-badge bg-label-{{ $p->modulo_color }}">
              <i class="ti tabler-{{ $p->modulo === 'sci' ? 'shield-check' : 'award' }} me-1"></i>{{ $p->modulo_label }}
            </span>
            <span class="bp-badge bg-label-secondary">{{ $p->categoria_label }}</span>
          </div>
        </div>
        <span class="estado-pill bg-label-warning flex-shrink-0">
          <i class="ti tabler-tournament me-1"></i>Elegible
        </span>
      </div>
      @if($p->descripcion)
      <p class="text-muted bp-desc mb-2">{{ Str::limit($p->descripcion, 100) }}</p>
      @endif
    </div>
    <div class="px-4 pb-3">
      <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-user text-primary"></i></div>
          <div class="min-w-0">
            <div class="bp-meta-label">Participante</div>
            <div class="bp-meta-value fw-bold">{{ optional($p->propuestoPor)->name ?? '—' }}</div>
          </div>
        </div>
        @if($p->puntaje_comision !== null)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-chart-bar text-warning"></i></div>
          <div class="flex-grow-1">
            <div class="bp-meta-label">Puntaje comisión</div>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:8px;border-radius:4px">
                <div class="progress-bar bg-{{ $p->puntaje_comision >= 80 ? 'success' : ($p->puntaje_comision >= 60 ? 'warning' : 'danger') }}"
                  style="width:{{ $p->puntaje_comision }}%"></div>
              </div>
              <span class="fw-bold" style="font-size:.85rem">{{ $p->puntaje_comision }}/100</span>
            </div>
          </div>
        </div>
        @endif
        @if($p->numero_expediente)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-file-description text-muted"></i></div>
          <div>
            <div class="bp-meta-label">N° Expediente</div>
            <div class="bp-meta-value">{{ $p->numero_expediente }}</div>
          </div>
        </div>
        @endif
      </div>
    </div>
    <div class="bp-actions">
      <button class="btn btn-sm btn-success flex-grow-1 btn-ganador-ugel"
        data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}"
        data-participante="{{ optional($p->propuestoPor)->name }}">
        <i class="ti tabler-trophy me-1"></i>Ganador UGEL
      </button>
      <button class="btn btn-sm btn-icon btn-outline-secondary btn-ver-detalle"
        data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}"
        data-descripcion="{{ $p->descripcion }}"
        data-participante="{{ optional($p->propuestoPor)->name }}" title="Ver descripción">
        <i class="ti tabler-eye"></i>
      </button>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     TAB: mis — El usuario ve el estado de sus propios proyectos
══════════════════════════════════════════════════════════════ --}}
@elseif($tab === 'mis')
<div class="col-12 col-md-6 col-xl-4">
  @php
    $esGanadorUgel   = in_array($p->estado, ['ganador_ugel','participante_externo','ganador_externo']);
    $esExterno       = in_array($p->estado, ['participante_externo','ganador_externo']);
    $esGanadorExt    = $p->estado === 'ganador_externo';
  @endphp
  <div class="bp-card card h-100 border-start border-4 border-{{ $p->estado_color }}
    {{ $esGanadorUgel ? 'ganador-card' : '' }}">

    {{-- Banner ganador UGEL --}}
    @if($p->estado === 'ganador_ugel')
    <div class="ganador-banner d-flex align-items-center gap-2 px-4 py-2">
      <i class="ti tabler-trophy text-warning" style="font-size:1.1rem"></i>
      <span class="fw-bold" style="font-size:.78rem;letter-spacing:.04em">¡GANADOR UGEL! — TU PROYECTO REPRESENTA A LA UGEL HUACAYBAMBA</span>
    </div>
    @elseif($esExterno)
    <div class="d-flex align-items-center gap-2 px-4 py-2"
      style="background:linear-gradient(90deg,rgba(130,80,255,.12),rgba(79,172,254,.08));border-bottom:1px solid rgba(130,80,255,.15)">
      <i class="ti tabler-world" style="font-size:1.1rem;color:#7c3aed"></i>
      <span class="fw-bold" style="font-size:.78rem;letter-spacing:.04em;color:#7c3aed">
        {{ $esGanadorExt ? '🏆 GANADOR ' . strtoupper($p->nivel_externo_label) : 'EN CONCURSO ' . strtoupper($p->nivel_externo_label) }}
      </span>
    </div>
    @endif

    <div class="px-4 pt-3 pb-2">
      <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
        <div class="flex-grow-1 min-w-0">
          <h6 class="fw-bold mb-1" title="{{ $p->titulo }}">{{ $p->titulo }}</h6>
          <div class="d-flex flex-wrap gap-1">
            <span class="bp-badge bg-label-{{ $p->modulo_color }}">
              <i class="ti tabler-{{ $p->modulo === 'sci' ? 'shield-check' : 'award' }} me-1"></i>{{ $p->modulo_label }}
            </span>
            <span class="bp-badge bg-label-secondary">{{ $p->categoria_label }}</span>
          </div>
        </div>
        <span class="estado-pill bg-label-{{ $p->estado_color }} flex-shrink-0">
          <i class="ti {{ $p->estado_icon }} me-1"></i>{{ $p->estado_label }}
        </span>
      </div>
    </div>

    <div class="px-4 pb-3">
      <div class="d-flex flex-column gap-2">

        {{-- Timeline de 4 pasos: UGEL --}}
        @php
          $pasosUgel = [
            'presentado'   => ['icon'=>'tabler-send',       'label'=>'Presentado',   'color'=>'info'],
            'recepcionado' => ['icon'=>'tabler-inbox',      'label'=>'Recepcionado', 'color'=>'primary'],
            'elegible'     => ['icon'=>'tabler-tournament', 'label'=>'Elegible UGEL','color'=>'warning'],
            'ganador_ugel' => ['icon'=>'tabler-trophy',     'label'=>'Ganador UGEL', 'color'=>'success'],
          ];
          $ordenUgel = array_keys($pasosUgel);
          $idxActual = array_search($p->estado, $ordenUgel);
          // Para estados externos, marcar todos los pasos UGEL como activos
          if (in_array($p->estado, ['participante_externo','ganador_externo'])) $idxActual = 3;
        @endphp

        @if($p->estado !== 'no_elegible')
        <div class="d-flex align-items-center gap-1 mb-1">
          @foreach($ordenUgel as $idx => $paso)
          @php $info = $pasosUgel[$paso]; $activo = ($idxActual !== false && $idx <= $idxActual); @endphp
          <div class="timeline-step text-center" style="flex:1">
            <div class="timeline-dot bg-{{ $activo ? $info['color'] : 'label-secondary' }} mx-auto">
              <i class="ti {{ $info['icon'] }}" style="font-size:.65rem;color:{{ $activo ? '#fff' : '#aaa' }}"></i>
            </div>
            <div style="font-size:.58rem;margin-top:2px;color:{{ $activo ? '#566a7f' : '#b0b4c4' }};font-weight:{{ $activo ? '600' : '400' }};line-height:1.2">
              {{ $info['label'] }}
            </div>
          </div>
          @if(!$loop->last)
          <div class="timeline-line {{ ($idxActual !== false && $idx < $idxActual) ? 'active' : '' }}"></div>
          @endif
          @endforeach
        </div>

        {{-- Nivel 2: Concurso externo --}}
        @if($esExterno)
        <div class="d-flex align-items-center gap-2 mt-1 p-2 rounded-2"
          style="background:rgba(124,58,237,.07);border:1px solid rgba(124,58,237,.15)">
          <i class="ti tabler-world" style="color:#7c3aed;font-size:1rem"></i>
          <div style="font-size:.78rem">
            <div class="fw-bold" style="color:#7c3aed">Concurso {{ $p->nivel_externo_label }}</div>
            @if($p->fecha_concurso_externo)
            <div class="text-muted">{{ $p->fecha_concurso_externo->format('d/m/Y') }}</div>
            @endif
          </div>
          @if($esGanadorExt)
          <span class="ms-auto bp-badge bg-label-success"><i class="ti tabler-star me-1"></i>Ganador</span>
          @else
          <span class="ms-auto bp-badge bg-label-purple" style="background:rgba(124,58,237,.1);color:#7c3aed">En curso</span>
          @endif
        </div>
        @endif

        @else
        <div class="alert alert-danger py-2 px-3 mb-0" style="font-size:.8rem;border-radius:8px">
          <i class="ti tabler-circle-x me-1"></i><strong>No Elegible</strong> — Tu proyecto no fue admitido.
        </div>
        @endif

        @if($p->numero_expediente)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-file-description text-primary"></i></div>
          <div>
            <div class="bp-meta-label">N° Expediente</div>
            <div class="bp-meta-value fw-bold">{{ $p->numero_expediente }}</div>
          </div>
        </div>
        @endif

        @if($p->observacion_comision)
        <div class="alert {{ in_array($p->estado, ['elegible','ganador_ugel','participante_externo','ganador_externo']) ? 'alert-success' : 'alert-secondary' }} py-2 px-3 mb-0"
          style="font-size:.8rem;border-radius:8px">
          <div class="fw-semibold mb-1"><i class="ti tabler-message-check me-1"></i>Observación de la comisión:</div>
          <div>{{ $p->observacion_comision }}</div>
        </div>
        @endif

        @if($p->puntaje_comision !== null && in_array($p->estado, ['elegible','ganador_ugel','participante_externo','ganador_externo']))
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-chart-bar text-warning"></i></div>
          <div class="flex-grow-1">
            <div class="bp-meta-label">Puntaje de la comisión</div>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:8px;border-radius:4px;width:80px">
                <div class="progress-bar bg-{{ $p->puntaje_comision >= 80 ? 'success' : ($p->puntaje_comision >= 60 ? 'warning' : 'danger') }}"
                  style="width:{{ $p->puntaje_comision }}%"></div>
              </div>
              <span class="fw-bold" style="font-size:.85rem">{{ $p->puntaje_comision }}/100</span>
            </div>
          </div>
        </div>
        @endif

        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-calendar text-muted"></i></div>
          <div>
            <div class="bp-meta-label">Presentado el</div>
            <div class="bp-meta-value">{{ $p->created_at->format('d/m/Y') }}</div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     TAB: concurso_ugel — Vista pública concurso interno UGEL
══════════════════════════════════════════════════════════════ --}}
@elseif($tab === 'concurso_ugel' || $tab === 'concurso')
<div class="col-12 col-md-6 col-xl-4">
  @php
    $esGanUgel = $p->estado === 'ganador_ugel';
    $esExt     = in_array($p->estado, ['participante_externo','ganador_externo']);
    $esGanExt  = $p->estado === 'ganador_externo';
  @endphp
  <div class="bp-card card h-100 border-start border-4 border-{{ $esGanUgel || $esExt ? 'warning' : 'primary' }}
    {{ $esGanUgel || $esExt ? 'ganador-card' : '' }}">

    @if($esGanExt)
    <div class="ganador-banner d-flex align-items-center gap-2 px-4 py-2"
      style="background:linear-gradient(135deg,rgba(40,199,111,.15),rgba(255,159,67,.1));border-bottom:1px solid rgba(40,199,111,.2)">
      <i class="ti tabler-star text-success" style="font-size:1.1rem"></i>
      <span class="fw-bold text-success" style="font-size:.75rem;letter-spacing:.04em">GANADOR {{ strtoupper($p->nivel_externo_label) }} — LOGRO INSTITUCIONAL</span>
    </div>
    @elseif($esExt)
    <div class="ganador-banner d-flex align-items-center gap-2 px-4 py-2"
      style="background:linear-gradient(90deg,rgba(124,58,237,.12),rgba(79,172,254,.08));border-bottom:1px solid rgba(124,58,237,.15)">
      <i class="ti tabler-world" style="font-size:1.1rem;color:#7c3aed"></i>
      <span class="fw-bold" style="font-size:.75rem;letter-spacing:.04em;color:#7c3aed">EN CONCURSO {{ strtoupper($p->nivel_externo_label) }}</span>
    </div>
    @elseif($esGanUgel)
    <div class="ganador-banner d-flex align-items-center gap-2 px-4 py-2">
      <i class="ti tabler-trophy text-warning" style="font-size:1.1rem"></i>
      <span class="fw-bold" style="font-size:.75rem;letter-spacing:.04em">GANADOR UGEL — REPRESENTA A LA UGEL HUACAYBAMBA</span>
    </div>
    @endif

    <div class="px-4 pt-3 pb-2">
      <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
        <div class="flex-grow-1 min-w-0">
          <h6 class="fw-bold mb-1" title="{{ $p->titulo }}">{{ $p->titulo }}</h6>
          <div class="d-flex flex-wrap gap-1">
            <span class="bp-badge bg-label-{{ $p->modulo_color }}">
              <i class="ti tabler-{{ $p->modulo === 'sci' ? 'shield-check' : 'award' }} me-1"></i>{{ $p->modulo_label }}
            </span>
            <span class="bp-badge bg-label-secondary">{{ $p->categoria_label }}</span>
          </div>
        </div>
        <span class="estado-pill bg-label-{{ $p->estado_color }} flex-shrink-0">
          <i class="ti {{ $p->estado_icon }} me-1"></i>{{ $p->estado_label }}
        </span>
      </div>
      @if($p->descripcion)
      <p class="text-muted bp-desc mb-3">{{ Str::limit($p->descripcion, 110) }}</p>
      @endif
    </div>

    <div class="px-4 pb-3">
      <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-user text-primary"></i></div>
          <div class="min-w-0">
            <div class="bp-meta-label">Participante</div>
            <div class="bp-meta-value fw-bold">{{ optional($p->propuestoPor)->name ?? '—' }}</div>
          </div>
        </div>
        @if($p->unidadOrganica)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-building text-secondary"></i></div>
          <div class="min-w-0">
            <div class="bp-meta-label">Unidad</div>
            <div class="bp-meta-value">{{ $p->unidadOrganica->sigla ?? $p->unidadOrganica->nombre }}</div>
          </div>
        </div>
        @endif
        @if($p->puntaje_comision !== null)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-chart-bar text-warning"></i></div>
          <div class="flex-grow-1">
            <div class="bp-meta-label">Puntaje de la comisión</div>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:8px;border-radius:4px">
                <div class="progress-bar bg-{{ $p->puntaje_comision >= 80 ? 'success' : ($p->puntaje_comision >= 60 ? 'warning' : 'danger') }}"
                  style="width:{{ $p->puntaje_comision }}%"></div>
              </div>
              <span class="fw-bold" style="font-size:.85rem">{{ $p->puntaje_comision }}/100</span>
            </div>
          </div>
        </div>
        @endif
        @if($p->numero_expediente)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-file-description text-muted"></i></div>
          <div>
            <div class="bp-meta-label">N° Expediente</div>
            <div class="bp-meta-value">{{ $p->numero_expediente }}</div>
          </div>
        </div>
        @endif
        @if($esExt && $p->fecha_concurso_externo)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-calendar-event" style="color:#7c3aed"></i></div>
          <div>
            <div class="bp-meta-label">Fecha concurso externo</div>
            <div class="bp-meta-value">{{ $p->fecha_concurso_externo->format('d/m/Y') }}</div>
          </div>
        </div>
        @endif
      </div>
    </div>

    @canany(['buenas-practicas.editar','buenas-practicas.eliminar'])
    <div class="bp-actions">
      @if($p->estado === 'elegible')
        <button class="btn btn-sm btn-success flex-grow-1 btn-ganador-ugel"
          data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}"
          data-participante="{{ optional($p->propuestoPor)->name }}">
          <i class="ti tabler-trophy me-1"></i>Declarar Ganador UGEL
        </button>
      @elseif($p->estado === 'ganador_ugel')
        <button class="btn btn-sm btn-outline-purple flex-grow-1 btn-enviar-externo"
          data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}"
          data-participante="{{ optional($p->propuestoPor)->name }}"
          style="border-color:#7c3aed;color:#7c3aed">
          <i class="ti tabler-world me-1"></i>Enviar a Concurso Externo
        </button>
      @elseif($p->estado === 'participante_externo')
        <button class="btn btn-sm btn-outline-success flex-grow-1 btn-resultado-externo"
          data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}"
          data-nivel="{{ $p->nivel_externo }}"
          data-participante="{{ optional($p->propuestoPor)->name }}">
          <i class="ti tabler-flag me-1"></i>Registrar Resultado Externo
        </button>
      @else
        <span class="text-muted small px-2 d-flex align-items-center gap-1">
          <i class="ti tabler-check-circle text-success"></i>Proceso concluido
        </span>
      @endif
      @can('buenas-practicas.eliminar')
      <button class="btn btn-sm btn-icon btn-outline-danger btn-eliminar"
        data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}" title="Eliminar">
        <i class="ti tabler-trash"></i>
      </button>
      @endcan
    </div>
    @endcanany
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     TAB: practicas — Prácticas institucionales del SCI
══════════════════════════════════════════════════════════════ --}}
@else
<div class="col-12 col-md-6 col-xl-4">
  <div class="bp-card card h-100 {{ $p->esta_vencida ? 'is-vencida' : '' }} border-start border-4 border-{{ $p->estado_color }}">
    <div class="px-4 pt-4 pb-2">
      <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
        <div class="flex-grow-1 min-w-0">
          <h6 class="fw-bold mb-1" title="{{ $p->titulo }}">{{ $p->titulo }}</h6>
          <div class="d-flex flex-wrap gap-1 align-items-center">
            <span class="bp-badge bg-label-{{ $p->modulo_color }}">
              <i class="ti tabler-{{ $p->modulo === 'sci' ? 'shield-check' : 'award' }} me-1"></i>{{ $p->modulo_label }}
            </span>
            <span class="bp-badge bg-label-secondary">{{ $p->categoria_label }}</span>
            @if($p->impacto)
            <span class="bp-badge bg-label-{{ $p->impacto_color }}">{{ ucfirst($p->impacto) }}</span>
            @endif
          </div>
        </div>
        <span class="estado-pill bg-label-{{ $p->estado_color }} flex-shrink-0">
          <i class="ti {{ $p->estado_icon }} me-1"></i>{{ $p->estado_label }}
        </span>
      </div>
      @if($p->descripcion)
      <p class="text-muted bp-desc mb-3">{{ Str::limit($p->descripcion, 100) }}</p>
      @endif
      <div class="mb-1">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="bp-meta-label">Avance</span>
          <span class="fw-bold" style="font-size:.85rem">{{ $p->avance }}%</span>
        </div>
        <div class="progress" style="height:6px;border-radius:3px">
          <div class="progress-bar bg-{{ $p->avance >= 100 ? 'success' : ($p->avance >= 60 ? 'primary' : ($p->avance >= 30 ? 'warning' : 'danger')) }}"
            style="width:{{ $p->avance }}%;border-radius:3px"></div>
        </div>
      </div>
    </div>
    <div class="px-4 pb-3">
      <div class="d-flex flex-column gap-2">
        @if($p->responsable)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-user-check text-success"></i></div>
          <div class="min-w-0">
            <div class="bp-meta-label">Responsable</div>
            <div class="bp-meta-value">{{ $p->responsable->name }}</div>
          </div>
        </div>
        @endif
        @if($p->unidadOrganica)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-building text-secondary"></i></div>
          <div class="min-w-0">
            <div class="bp-meta-label">Unidad</div>
            <div class="bp-meta-value">{{ $p->unidadOrganica->sigla ?? $p->unidadOrganica->nombre }}</div>
          </div>
        </div>
        @endif
        @if($p->fecha_termino)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-calendar-due {{ $p->esta_vencida ? 'text-danger' : 'text-warning' }}"></i></div>
          <div>
            <div class="bp-meta-label">Vence</div>
            <div class="d-flex align-items-center gap-2">
              <span class="bp-meta-value {{ $p->esta_vencida ? 'text-danger fw-bold' : '' }}">{{ $p->fecha_termino->format('d/m/Y') }}</span>
              @php $dias = $p->dias_restantes; @endphp
              @if($dias !== null && $p->estado !== 'completada')
                @if($dias < 0)<span class="dias-chip bg-label-danger text-danger">{{ abs($dias) }}d vencida</span>
                @elseif($dias <= 7)<span class="dias-chip bg-label-warning text-warning">{{ $dias }}d</span>
                @elseif($dias <= 30)<span class="dias-chip bg-label-info text-info">{{ $dias }}d</span>
                @endif
              @endif
            </div>
          </div>
        </div>
        @endif
        @if($p->numero_sgd)
        <div class="d-flex align-items-center gap-2">
          <div class="bp-meta-icon"><i class="ti tabler-file-description text-muted"></i></div>
          <div>
            <div class="bp-meta-label">N° SGD</div>
            <div class="bp-meta-value">{{ $p->numero_sgd }}</div>
          </div>
        </div>
        @endif
      </div>
    </div>
    @canany(['buenas-practicas.editar','buenas-practicas.eliminar'])
    <div class="bp-actions">
      @can('buenas-practicas.editar')
      <button class="btn btn-sm btn-outline-primary flex-grow-1 btn-editar"
        data-id="{{ $p->id }}"
        data-titulo="{{ $p->titulo }}"
        data-descripcion="{{ $p->descripcion }}"
        data-categoria="{{ $p->categoria }}"
        data-modulo="{{ $p->modulo }}"
        data-unidad="{{ $p->unidad_organica_id }}"
        data-responsable="{{ $p->responsable_id }}"
        data-estado="{{ $p->estado }}"
        data-avance="{{ $p->avance }}"
        data-inicio="{{ optional($p->fecha_inicio)->format('Y-m-d') }}"
        data-termino="{{ optional($p->fecha_termino)->format('Y-m-d') }}"
        data-sgd="{{ $p->numero_sgd }}"
        data-impacto="{{ $p->impacto }}"
        data-evidencias="{{ $p->evidencias }}"
        data-observaciones="{{ $p->observaciones }}">
        <i class="ti tabler-edit me-1"></i>Editar
      </button>
      @endcan
      @can('buenas-practicas.eliminar')
      <button class="btn btn-sm btn-icon btn-outline-danger btn-eliminar"
        data-id="{{ $p->id }}" data-titulo="{{ $p->titulo }}" title="Eliminar">
        <i class="ti tabler-trash"></i>
      </button>
      @endcan
    </div>
    @endcanany
  </div>
</div>
@endif

@empty
<div class="col-12">
  <div class="text-center py-5">
    <div class="empty-icon bg-label-secondary mx-auto mb-3">
      <i class="ti tabler-clipboard-x text-secondary" style="font-size:2rem"></i>
    </div>
    <h6 class="text-muted fw-semibold mb-1">
      @if($tab === 'presentados') No hay proyectos nuevos por recepcionar
      @elseif($tab === 'recepcionados') No hay proyectos recepcionados pendientes de evaluar
      @elseif($tab === 'elegibles') No hay proyectos elegibles pendientes de declararar ganador
      @elseif(in_array($tab, ['concurso_ugel','concurso'])) No hay proyectos en el concurso UGEL aún
      @elseif($tab === 'concurso_externo') No hay proyectos en concurso externo aún
      @elseif($tab === 'mis') Aún no has presentado ningún proyecto
      @else No se encontraron prácticas institucionales
      @endif
    </h6>
    <p class="text-muted small mb-0">
      @if($tab === 'mis') Usa el botón <strong>"Presentar mi proyecto"</strong> para participar en el concurso.
      @elseif($tab === 'practicas' && !$esGestor) Ajusta los filtros o consulta con el Responsable SCI.
      @endif
    </p>
  </div>
</div>
@endforelse
