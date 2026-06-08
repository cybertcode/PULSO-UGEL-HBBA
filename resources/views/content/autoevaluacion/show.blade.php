@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Autoevaluación: '.$autoevaluacion->titulo)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
      <li class="breadcrumb-item"><a href="{{ route('autoevaluacion.index') }}">Autoevaluación SCI</a></li>
      <li class="breadcrumb-item active">{{ $autoevaluacion->titulo }}</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-1">{{ $autoevaluacion->titulo }}</h4>
      <div class="d-flex gap-2 flex-wrap">
        <span class="badge bg-label-secondary">{{ $autoevaluacion->anio }}</span>
        <span class="badge bg-{{ $autoevaluacion->color_estado }}">{{ ucfirst($autoevaluacion->estado) }}</span>
        @if($autoevaluacion->puntaje_total !== null)
        <span class="badge bg-label-primary">Puntaje: {{ $autoevaluacion->puntaje_total }}</span>
        @endif
      </div>
    </div>
    <a href="{{ route('autoevaluacion.index') }}" class="btn btn-outline-secondary">
      <i class="ti tabler-arrow-left me-1"></i> Volver
    </a>
  </div>

  @if($autoevaluacion->estado !== 'cerrada')
  <form method="POST" action="{{ route('autoevaluacion.respuestas', $autoevaluacion) }}">
    @csrf @method('PATCH')
  @endif

  @foreach($componentes as $comp)
  @php $respuestasComp = $respuestas_por_componente[$comp->id] ?? collect(); @endphp
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="ti tabler-cube me-2 text-primary"></i>
        {{ $comp->nombre }}
      </h5>
    </div>
    <div class="card-body">
      @if($respuestasComp->isEmpty())
      <div class="text-center py-3 text-muted">
        <i class="ti tabler-clipboard-x fs-2 mb-2 d-block"></i>
        No hay preguntas registradas para este componente.
      </div>
      @else
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:40%">Pregunta</th>
              <th style="width:15%">Respuesta</th>
              <th style="width:10%">Puntaje</th>
              <th style="width:20%">Evidencia</th>
              <th style="width:15%">Observación</th>
            </tr>
          </thead>
          <tbody>
            @foreach($respuestasComp as $i => $resp)
            <tr>
              <td>{{ $resp->pregunta }}</td>
              <td>
                @if($autoevaluacion->estado !== 'cerrada')
                <select name="respuestas[{{ $resp->id }}][respuesta]" class="form-select form-select-sm">
                  <option value="">—</option>
                  <option value="si"       {{ $resp->respuesta=='si'?'selected':'' }}>Sí</option>
                  <option value="no"       {{ $resp->respuesta=='no'?'selected':'' }}>No</option>
                  <option value="parcial"  {{ $resp->respuesta=='parcial'?'selected':'' }}>Parcial</option>
                  <option value="no_aplica"{{ $resp->respuesta=='no_aplica'?'selected':'' }}>No aplica</option>
                </select>
                <input type="hidden" name="respuestas[{{ $resp->id }}][componente_id]" value="{{ $comp->id }}">
                <input type="hidden" name="respuestas[{{ $resp->id }}][pregunta]" value="{{ $resp->pregunta }}">
                @else
                @php $lbl = match($resp->respuesta){'si'=>'Sí','no'=>'No','parcial'=>'Parcial','no_aplica'=>'No aplica',default=>'—'}; @endphp
                @php $bc = match($resp->respuesta){'si'=>'success','no'=>'danger','parcial'=>'warning','no_aplica'=>'secondary',default=>'secondary'}; @endphp
                <span class="badge bg-{{ $bc }}">{{ $lbl }}</span>
                @endif
              </td>
              <td>
                @if($autoevaluacion->estado !== 'cerrada')
                <input type="number" name="respuestas[{{ $resp->id }}][puntaje]" class="form-control form-control-sm"
                  min="0" max="3" value="{{ $resp->puntaje }}">
                @else
                <span class="badge bg-label-primary">{{ $resp->puntaje }}</span>
                @endif
              </td>
              <td>
                @if($autoevaluacion->estado !== 'cerrada')
                <input type="text" name="respuestas[{{ $resp->id }}][evidencia]" class="form-control form-control-sm"
                  value="{{ $resp->evidencia }}" placeholder="N° doc / código">
                @else
                {{ $resp->evidencia ?? '—' }}
                @endif
              </td>
              <td>
                @if($autoevaluacion->estado !== 'cerrada')
                <input type="text" name="respuestas[{{ $resp->id }}][observacion]" class="form-control form-control-sm"
                  value="{{ $resp->observacion }}">
                @else
                {{ $resp->observacion ?? '—' }}
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>
  @endforeach

  @if($autoevaluacion->estado !== 'cerrada')
  <div class="d-flex justify-content-end gap-2 mb-4">
    <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i> Guardar Respuestas</button>
  </div>
  </form>
  @endif

</div>
@endsection
