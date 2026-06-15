@php use Illuminate\Support\Str; @endphp
@forelse($actividades as $act)
@php
  $ec = match($act->estado) {
    'completada' => 'success',
    'vencida'    => 'danger',
    'observado'  => 'info',
    'en_proceso' => 'warning',
    default      => 'secondary',
  };
  $estadoIcon = match($act->estado) {
    'completada' => 'tabler-circle-check',
    'vencida'    => 'tabler-clock-x',
    'observado'  => 'tabler-eye',
    'en_proceso' => 'tabler-loader-2',
    default      => 'tabler-clock-pause',
  };
  $pc = match($act->prioridad) { 'alta' => 'danger', 'media' => 'warning', default => 'secondary' };
  $prioIcon = match($act->prioridad) { 'alta' => 'tabler-flag-3', 'media' => 'tabler-flag-2', default => 'tabler-flag' };
  $miRol = $act->responsables->where('id', $user->id)->first()?->pivot->tipo ?? 'principal';
  $rolIcon = match($miRol) { 'principal' => 'tabler-crown', 'supervisor' => 'tabler-eye', default => 'tabler-users' };
  $diasRestantes = $act->fecha_limite ? (int) round(now()->diffInDays($act->fecha_limite, false)) : null;
  $actComp = $act->modulo === 'integridad'
    ? $act->integridadPregunta?->componente?->nombre
    : $act->sciPregunta?->componente?->nombre;
  $actModuloBadge = $act->modulo === 'integridad' ? 'warning' : 'primary';

  // Estado de evidencias (debe calcularse antes de $canEdit)
  $evTotal     = $act->evidencias->count();
  $evValidadas = $act->evidencias->where('estado', 'validado')->count();
  $evRechazadas= $act->evidencias->where('estado', 'rechazado')->count();
  $evPendientes= $act->evidencias->where('estado', 'pendiente')->count();

  // Si está "completada" pero tiene evidencia rechazada → mostrar como observado visualmente
  $estadoEfectivo = ($act->estado === 'completada' && $evRechazadas > 0) ? 'observado' : $act->estado;
  if ($estadoEfectivo !== $act->estado) {
    $ec = 'warning'; // naranja para indicar problema
    $estadoIcon = 'tabler-alert-circle';
  }
  $canEdit = !in_array($act->estado, ['completada', 'vencida']) || $evRechazadas > 0;
@endphp
<div class="col-md-6 col-xl-4">
  <div class="card act-card is-{{ $act->estado }} h-100" data-act-id="{{ $act->id }}">
    <div class="act-header">
      <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
        <span class="estado-pill bg-label-{{ $ec }} text-{{ $ec }}">
          <i class="ti {{ $estadoIcon }} me-1" style="font-size:.75rem"></i>{{ $act->estado_label }}
        </span>
        <div class="d-flex gap-1">
          <span class="rol-badge bg-label-{{ $pc }} text-{{ $pc }}">
            <i class="ti {{ $prioIcon }} me-1" style="font-size:.7rem"></i>{{ ucfirst($act->prioridad) }}
          </span>
          <span class="rol-badge bg-label-secondary text-secondary text-capitalize">
            <i class="ti {{ $rolIcon }} me-1" style="font-size:.7rem"></i>{{ $miRol }}
          </span>
        </div>
      </div>
      <h6 class="mb-0 fw-bold lh-sm" title="{{ $act->nombre }}" style="font-size:.9rem">{{ Str::limit($act->nombre, 65) }}</h6>
    </div>
    <div class="act-body flex-grow-1">
      <p class="text-muted mb-3 d-flex align-items-center gap-2 flex-wrap" style="font-size:.78rem">
        <span class="badge bg-label-{{ $actModuloBadge }}" style="font-size:.65rem">{{ strtoupper($act->modulo) }}</span>
        <i class="ti tabler-layout-grid" style="font-size:.8rem"></i>
        {{ $actComp ?? '—' }}
        <span class="mx-1">·</span>
        <code class="text-muted" style="font-size:.72rem">{{ $act->codigo }}</code>
      </p>
      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="text-muted" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.03em">Avance</span>
          <span class="fw-bold text-{{ $ec }}" style="font-size:.88rem">{{ $act->avance }}%</span>
        </div>
        <div class="progress progress-thin">
          <div class="progress-bar bg-{{ $ec }}" style="width:{{ $act->avance }}%;border-radius:3px" role="progressbar"></div>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center" style="font-size:.78rem">
        <div class="d-flex align-items-center gap-1 text-muted">
          <i class="ti tabler-calendar" style="font-size:.85rem"></i>
          @if($act->fecha_limite)
            <span>{{ $act->fecha_limite->format('d/m/Y') }}</span>
            @if($diasRestantes !== null && $act->estado !== 'completada')
              <span class="dias-chip ms-1 {{ $diasRestantes < 0 ? 'bg-label-danger text-danger' : ($diasRestantes <= 7 ? 'bg-label-warning text-warning' : 'bg-label-secondary text-secondary') }}">
                {{ $diasRestantes < 0 ? abs($diasRestantes).'d tarde' : $diasRestantes.'d' }}
              </span>
            @elseif($act->estado === 'completada')
              <span class="dias-chip ms-1 bg-label-success text-success"><i class="ti tabler-check" style="font-size:.7rem"></i>OK</span>
            @endif
          @else
            <span class="text-muted">Sin fecha límite</span>
          @endif
        </div>
        <div class="d-flex align-items-center gap-1 flex-wrap">
          @if($evRechazadas > 0)
            <span class="dias-chip bg-label-danger text-danger" title="{{ $evRechazadas }} evidencia(s) rechazada(s) — requiere corrección">
              <i class="ti tabler-file-x" style="font-size:.75rem"></i>
              {{ $evRechazadas }} rechazada{{ $evRechazadas > 1 ? 's' : '' }}
            </span>
          @endif
          @if($evPendientes > 0)
            <span class="dias-chip bg-label-warning text-warning" title="{{ $evPendientes }} evidencia(s) pendiente(s) de revisión">
              <i class="ti tabler-file-time" style="font-size:.75rem"></i>
              {{ $evPendientes }} en revisión
            </span>
          @endif
          @if($evValidadas > 0)
            <span class="dias-chip bg-label-success text-success" title="{{ $evValidadas }} evidencia(s) validada(s)">
              <i class="ti tabler-file-check" style="font-size:.75rem"></i>
              {{ $evValidadas }} validada{{ $evValidadas > 1 ? 's' : '' }}
            </span>
          @endif
          @if($evTotal === 0 && !in_array($act->estado, ['pendiente', 'completada']))
            <span class="dias-chip bg-label-warning text-warning" title="Sin evidencias registradas">
              <i class="ti tabler-file-off" style="font-size:.75rem"></i>
              Sin evidencia
            </span>
          @endif
        </div>
      </div>
    </div>
    <div class="act-actions">
      @if($canEdit)
      <button class="btn btn-sm btn-primary btn-act flex-fill btn-actualizar-avance"
        data-id="{{ $act->id }}"
        data-avance="{{ $act->avance }}"
        data-nombre="{{ Str::limit($act->nombre, 50) }}"
        data-url="{{ route('mis-actividades.avance', $act) }}">
        <i class="ti tabler-pencil me-1"></i>Actualizar
      </button>
      @endif
      @php
        // Determinar estado del botón de evidencia
        if ($act->estado === 'completada' && $evRechazadas === 0) {
          // Completada y validada: solo mostrar ícono, sin acción de subir
          $evBtnClass   = 'btn-outline-success';
          $evBtnIcon    = 'tabler-file-check';
          $evBtnTitle   = 'Actividad completada y validada';
          $evBtnDesact  = true;
        } elseif ($evPendientes > 0) {
          // En revisión: no puede subir otra mientras espera validación
          $evBtnClass   = 'btn-outline-warning';
          $evBtnIcon    = 'tabler-file-time';
          $evBtnTitle   = 'Evidencia en revisión — espera la validación del coordinador';
          $evBtnDesact  = true;
        } elseif ($evRechazadas > 0) {
          // Rechazada: enlace a evidencias para corregir
          $evBtnClass   = 'btn-outline-danger';
          $evBtnIcon    = 'tabler-file-x';
          $evBtnTitle   = 'Evidencia rechazada — clic para corregir';
          $evBtnDesact  = false;
        } else {
          // Libre: puede subir evidencia
          $evBtnClass   = 'btn-outline-secondary';
          $evBtnIcon    = 'tabler-upload';
          $evBtnTitle   = 'Subir evidencia';
          $evBtnDesact  = false;
        }
        $evNueva = (!$evBtnDesact) ? '1' : '0';
      @endphp
      @if($evBtnDesact)
      <button type="button" class="btn btn-sm btn-act {{ $evBtnClass }}" title="{{ $evBtnTitle }}" disabled>
        <i class="ti {{ $evBtnIcon }}"></i>
      </button>
      @elseif($evRechazadas > 0)
      {{-- Corregir evidencia rechazada: abre modal local con datos pre-cargados --}}
      @php $evRechazada = $act->evidencias->where('estado','rechazado')->first(); @endphp
      <button type="button"
        class="btn btn-sm btn-act {{ $evBtnClass }} btn-ev-corregir"
        title="{{ $evBtnTitle }}"
        data-actividad-id="{{ $act->id }}"
        data-evidencia-id="{{ $evRechazada?->id }}"
        data-titulo="{{ $evRechazada?->titulo }}"
        data-sgd="{{ $evRechazada?->numero_sgd }}"
        data-url-doc="{{ $evRechazada?->url_documento }}"
        data-descripcion="{{ $evRechazada?->descripcion }}"
        data-motivo="{{ $evRechazada?->motivo_rechazo }}"
        data-action="{{ route('sci-evidencias.update', $evRechazada ?? 0) }}">
        <i class="ti {{ $evBtnIcon }}"></i>
      </button>
      @else
      {{-- Subir nueva evidencia: abre modal local --}}
      <button type="button"
        class="btn btn-sm btn-act {{ $evBtnClass }} btn-ev-nueva"
        title="{{ $evBtnTitle }}"
        data-actividad-id="{{ $act->id }}"
        data-modulo="{{ $act->modulo }}"
        data-nombre="{{ Str::limit($act->nombre, 60) }}"
        data-action="{{ route('sci-evidencias.store') }}">
        <i class="ti {{ $evBtnIcon }}"></i>
      </button>
      @endif
      <button class="btn btn-sm btn-act btn-outline-secondary btn-ver-historial"
        data-id="{{ $act->id }}"
        data-nombre="{{ Str::limit($act->nombre, 50) }}"
        data-url="{{ route('mis-actividades.historial', $act) }}"
        title="Ver historial de cambios">
        <i class="ti tabler-history"></i>
      </button>
    </div>
  </div>
</div>
@empty
<div class="col-12">
  <div class="card" style="border-radius:14px;border:none">
    <div class="card-body text-center py-5">
      <div class="empty-icon bg-label-secondary mx-auto mb-3">
        <i class="ti tabler-clipboard-off text-muted"></i>
      </div>
      <h5 class="fw-bold">No hay actividades que mostrar</h5>
      <p class="text-muted mb-3">Ninguna actividad coincide con los filtros aplicados.</p>
      <button type="button" class="btn btn-label-primary btn-sm" id="btnLimpiarEmpty">
        <i class="ti tabler-x me-1"></i>Limpiar filtros
      </button>
    </div>
  </div>
</div>
@endforelse
