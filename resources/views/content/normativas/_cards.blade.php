@php use Illuminate\Support\Str; @endphp
@forelse($normativas as $n)
@php
  $vigente = $n->esta_vigente;
  $ec      = $vigente ? 'success' : 'secondary';
  $icEst   = $vigente ? 'tabler-circle-check' : 'tabler-circle-pause';

  $tipoColors = ['ley'=>'danger','decreto'=>'warning','resolucion'=>'primary',
                 'directiva'=>'info','manual'=>'success','reglamento'=>'secondary',
                 'oficio'=>'dark','otro'=>'secondary'];
  $tipoIcons  = ['ley'=>'tabler-gavel','decreto'=>'tabler-file-certificate',
                 'resolucion'=>'tabler-file-check','directiva'=>'tabler-file-description',
                 'manual'=>'tabler-book','reglamento'=>'tabler-list-details',
                 'oficio'=>'tabler-mail','otro'=>'tabler-file'];
  $tc = $tipoColors[$n->tipo] ?? 'secondary';
  $ti = $tipoIcons[$n->tipo]  ?? 'tabler-file';
  $mc = match($n->modulo) { 'sci'=>'primary','integridad'=>'warning', default=>'secondary' };
@endphp
<div class="col-md-6 col-xl-4">
  <div class="card norm-card is-{{ $vigente ? 'vigente' : 'novigente' }} h-100">

    {{-- Header --}}
    <div class="norm-header">
      <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
        <span class="tipo-pill bg-label-{{ $tc }}">
          <i class="ti {{ $ti }} me-1" style="font-size:.75rem"></i>{{ $n->tipo_label }}
        </span>
        <span class="tipo-pill bg-label-{{ $ec }}">
          <i class="ti {{ $icEst }} me-1" style="font-size:.75rem"></i>
          {{ $vigente ? 'Vigente' : 'No vigente' }}
        </span>
      </div>
      <h6 class="mb-0 fw-bold lh-sm" title="{{ $n->nombre }}" style="font-size:.9rem">
        {{ Str::limit($n->nombre, 68) }}
      </h6>
    </div>

    {{-- Body --}}
    <div class="norm-body">

      {{-- Módulo + Alcance + Código --}}
      <p class="text-muted mb-3 d-flex align-items-center gap-2 flex-wrap" style="font-size:.78rem">
        <span class="mod-badge bg-label-{{ $mc }}" style="font-size:.65rem">{{ strtoupper($n->modulo) }}</span>
        <i class="ti tabler-map-pin" style="font-size:.8rem"></i>{{ $n->alcance_label }}
        @if($n->codigo)
          <span class="mx-1 text-muted">·</span>
          <code class="text-muted" style="font-size:.72rem">{{ $n->codigo }}</code>
        @endif
      </p>

      {{-- Entidad + Fecha --}}
      @if($n->entidad_emisora)
      <div class="d-flex align-items-center gap-1 mb-3 text-muted" style="font-size:.78rem">
        <i class="ti tabler-building" style="font-size:.85rem"></i>
        <span class="text-truncate">{{ Str::limit($n->entidad_emisora, 38) }}</span>
        @if($n->fecha_emision)
          <span class="ms-auto text-muted" style="font-size:.72rem;white-space:nowrap">{{ $n->fecha_emision->format('d/m/Y') }}</span>
        @endif
      </div>
      @endif

      {{-- Descripción --}}
      @if($n->descripcion)
      <p class="text-muted mb-3"
         style="font-size:.8rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.45">
        {{ $n->descripcion }}
      </p>
      @endif

      {{-- Chips recurso --}}
      <div class="d-flex flex-wrap align-items-center gap-2 mt-auto">
        @if($n->tiene_archivo)
          <a href="{{ asset('storage/'.$n->archivo_path) }}" target="_blank"
             class="recurso-chip bg-label-primary text-primary">
            <i class="ti tabler-download" style="font-size:.78rem"></i>Archivo
          </a>
        @endif
        @if($n->tiene_link)
          <a href="{{ $n->link_externo }}" target="_blank" rel="noopener noreferrer"
             class="recurso-chip bg-label-info text-info">
            <i class="ti tabler-external-link" style="font-size:.78rem"></i>Link
          </a>
        @endif
        @if($n->tiene_tutorial)
          <span class="recurso-chip bg-label-danger text-danger"
                onclick="verNormativa({{ $n->id }})">
            <i class="ti tabler-player-play" style="font-size:.78rem"></i>Tutorial
          </span>
        @endif
        @if(!$n->tiene_archivo && !$n->tiene_link && !$n->tiene_tutorial)
          <span class="recurso-chip bg-label-secondary text-muted">
            <i class="ti tabler-file-off" style="font-size:.78rem"></i>Sin recursos
          </span>
        @endif
      </div>

    </div>{{-- /.norm-body --}}

    {{-- Acciones --}}
    <div class="norm-actions">
      <button class="btn btn-sm btn-primary btn-act flex-fill" onclick="verNormativa({{ $n->id }})">
        <i class="ti tabler-eye me-1"></i>Ver detalle
      </button>
      @if($esGestor)
      <button class="btn btn-sm btn-act btn-outline-primary" title="Editar"
              onclick="editarNormativa({{ $n->id }})">
        <i class="ti tabler-edit"></i>
      </button>
      <button class="btn btn-sm btn-act btn-outline-{{ $vigente ? 'secondary' : 'success' }}"
              title="{{ $vigente ? 'Marcar como no vigente' : 'Marcar como vigente' }}"
              onclick="toggleVigente({{ $n->id }})">
        <i class="ti tabler-toggle-{{ $vigente ? 'left' : 'right' }}"></i>
      </button>
      <button class="btn btn-sm btn-act btn-outline-danger" title="Eliminar"
              onclick="confirmarEliminar({{ $n->id }}, {{ json_encode(Str::limit($n->nombre, 60)) }})">
        <i class="ti tabler-trash"></i>
      </button>
      @endif
    </div>

  </div>
</div>
@empty
<div class="col-12">
  <div class="card" style="border-radius:14px;border:none">
    <div class="card-body text-center py-5">
      <div class="empty-icon bg-label-secondary mx-auto mb-3">
        <i class="ti tabler-file-off text-muted"></i>
      </div>
      <h5 class="fw-bold">No hay normativas que mostrar</h5>
      <p class="text-muted mb-3">Ninguna normativa coincide con los filtros aplicados.</p>
      <button type="button" class="btn btn-label-primary btn-sm"
              onclick="document.getElementById('btn-limpiar').click()">
        <i class="ti tabler-x me-1"></i>Limpiar filtros
      </button>
    </div>
  </div>
</div>
@endforelse
