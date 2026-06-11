@php use Illuminate\Support\Str; @endphp
@forelse($actividades as $a)
@php
  $ec        = $a->estado_color;
  $dias      = (int) round(now()->diffInDays($a->fecha_limite, false));
  $prioColor = match($a->prioridad) { 'alta'=>'danger', 'media'=>'warning', default=>'info' };
  $fechaColor = $dias < 0 ? 'danger' : ($dias <= 7 ? 'warning' : 'secondary');
@endphp
<tr class="row-{{ $a->estado }}">

  {{-- Código --}}
  <td>
    <div class="codigo-chip">{{ $a->codigo }}</div>
    @if($a->numero_sgd)
    <div class="sgd-chip"><i class="ti tabler-file-text icon-10px me-1"></i>{{ $a->numero_sgd }}</div>
    @endif
  </td>

  {{-- Actividad --}}
  <td>
    <div class="fw-medium" style="font-size:13px;line-height:1.4;max-width:230px"
      title="{{ $a->nombre }}">{{ Str::limit($a->nombre, 55) }}</div>
  </td>

  {{-- Componente / Pregunta --}}
  <td>
    @php
      $comp = $a->sciPregunta?->componente;
      $preg = $a->sciPregunta;
    @endphp
    <div class="d-flex align-items-center gap-1" style="font-size:12px">
      <i class="ti {{ $comp?->icono ?? 'tabler-point' }} icon-14px text-primary flex-shrink-0"></i>
      <span title="{{ $comp?->nombre ?? '' }}">{{ Str::limit($comp?->nombre ?? '—', 20) }}</span>
    </div>
    @if($preg)
    <div class="text-muted mt-1" style="font-size:11px" title="{{ $preg->nombre }}">
      <i class="ti tabler-chevron-right icon-10px"></i>{{ Str::limit($preg->nombre, 22) }}
    </div>
    @endif
  </td>

  {{-- Unidad --}}
  <td class="text-center">
    <span class="badge bg-label-secondary" title="{{ $a->unidadOrganica->nombre ?? '—' }}">
      {{ $a->unidadOrganica->sigla ?? '—' }}
    </span>
  </td>

  {{-- Responsables --}}
  <td>
    @if($a->responsables->isNotEmpty())
      @foreach($a->responsables->take(2) as $resp)
      @php
        $tipoKey = $resp->pivot->tipo;
        $tipoCss = match($tipoKey) { 'principal'=>'p', 'colaborador'=>'c', default=>'s' };
        $tipoLbl = match($tipoKey) { 'principal'=>'P', 'colaborador'=>'C', default=>'S' };
      @endphp
      <div class="resp-row">
        <span class="resp-tipo {{ $tipoCss }}" title="{{ ucfirst($tipoKey) }}">{{ $tipoLbl }}</span>
        <span class="resp-name text-body" title="{{ $resp->name }}">{{ $resp->name }}</span>
      </div>
      @endforeach
      @if($a->responsables->count() > 2)
      <small class="text-muted" style="font-size:10px">+{{ $a->responsables->count() - 2 }} más</small>
      @endif
    @else
      <small class="text-muted fst-italic" style="font-size:11px">Sin asignar</small>
    @endif
  </td>

  {{-- Prioridad --}}
  <td>
    <span class="badge bg-label-{{ $prioColor }}" style="font-size:11px">
      @if($a->prioridad === 'alta')<i class="ti tabler-chevrons-up me-1"></i>@elseif($a->prioridad === 'baja')<i class="ti tabler-chevrons-down me-1"></i>@endif
      {{ ucfirst($a->prioridad) }}
    </span>
  </td>

  {{-- Fecha límite --}}
  <td>
    <span class="fecha-chip badge bg-label-{{ $fechaColor }}">
      {{ $a->fecha_limite->format('d/m/Y') }}
    </span>
    @if($dias < 0 && !in_array($a->estado, ['completada','observado']))
    <div class="dias-tag text-danger"><i class="ti tabler-clock-x icon-10px"></i>{{ abs($dias) }}d tarde</div>
    @elseif($dias >= 0 && $dias <= 7 && !in_array($a->estado, ['completada','observado']))
    <div class="dias-tag text-warning"><i class="ti tabler-clock icon-10px"></i>{{ $dias }}d restantes</div>
    @elseif(in_array($a->estado, ['completada']))
    <div class="dias-tag text-success"><i class="ti tabler-check icon-10px"></i>Completada</div>
    @endif
  </td>

  {{-- Avance --}}
  <td>
    <div class="prog-wrap">
      <div class="prog-track">
        <div class="prog-fill bg-{{ $ec }}" style="width:{{ $a->avance }}%"></div>
      </div>
      <span class="prog-pct text-{{ $ec }}">{{ $a->avance }}%</span>
    </div>
  </td>

  {{-- Estado --}}
  <td>
    <span class="estado-pill badge bg-label-{{ $ec }}">{{ $a->estado_label }}</span>
  </td>

  {{-- Acciones --}}
  <td>
    <div class="act-actions">
      <button class="btn btn-icon btn-label-secondary btn-historial"
        data-id="{{ $a->id }}"
        data-nombre="{{ $a->nombre }}"
        data-url="{{ route('sci-control-interno.historial', $a) }}"
        title="Historial de cambios">
        <i class="ti tabler-history icon-14px"></i>
      </button>
      @can('control-interno.editar')
      <button class="btn btn-icon btn-label-primary btn-editar"
        data-id="{{ $a->id }}"
        data-nombre="{{ $a->nombre }}"
        data-anio="{{ $a->anio ?? date('Y') }}"
        data-eje-id="{{ $a->sciPregunta?->componente?->eje_id ?? '' }}"
        data-componente-id="{{ $a->sciPregunta?->componente_id ?? '' }}"
        data-pregunta-id="{{ $a->sci_pregunta_id ?? '' }}"
        data-unidad="{{ $a->unidad_organica_id ?? '' }}"
        data-responsables-json='@json($a->responsables->map(fn($r) => ["id"=>$r->id,"name"=>$r->name,"tipo"=>$r->pivot->tipo]))'
        data-fecha="{{ $a->fecha_limite->format('Y-m-d') }}"
        data-fechainicio="{{ $a->fecha_inicio?->format('Y-m-d') ?? '' }}"
        data-avance="{{ $a->avance }}"
        data-estado="{{ $a->estado }}"
        data-prioridad="{{ $a->prioridad }}"
        data-sgd="{{ $a->numero_sgd ?? '' }}"
        data-descripcion="{{ htmlspecialchars($a->descripcion ?? '', ENT_QUOTES) }}"
        data-observaciones="{{ htmlspecialchars($a->observaciones ?? '', ENT_QUOTES) }}"
        title="Editar">
        <i class="ti tabler-edit icon-14px"></i>
      </button>
      <button type="button" class="btn btn-icon btn-label-danger btn-eliminar"
        data-id="{{ $a->id }}"
        data-url="{{ route('sci-control-interno.destroy', $a) }}"
        title="Eliminar">
        <i class="ti tabler-trash icon-14px"></i>
      </button>
      @endcan
    </div>
  </td>

</tr>
@empty
<tr>
  <td colspan="10">
    <div class="empty-sci">
      <div class="empty-icon"><i class="ti tabler-clipboard-off"></i></div>
      <div class="fw-semibold mb-1">No se encontraron actividades</div>
      <div class="text-body-secondary" style="font-size:13px" id="emptyMsg">
        Prueba cambiando los filtros de búsqueda.
      </div>
      <button type="button" class="btn btn-sm btn-label-secondary mt-3" id="btnLimpiarEmpty">
        <i class="ti tabler-x me-1"></i>Limpiar filtros
      </button>
    </div>
  </td>
</tr>
@endforelse
