@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Responder Encuesta — PULSO UGEL')

@section('page-style')
<style>
.pregunta-card { border: 1px solid #e7e7e7; border-radius: 12px; padding: 1.4rem; background: #fff; margin-bottom: 1.25rem; transition: box-shadow .2s; }
.pregunta-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); }
.pregunta-num { width: 28px; height: 28px; border-radius: 50%; background: #696cff; color: #fff; font-weight: 700; font-size: .8rem; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
.escala-btn { width: 52px; height: 52px; border-radius: 10px; border: 2px solid #d5d4dd; background: #fff; font-size: 1.2rem; font-weight: 700; cursor: pointer; transition: all .15s; display: flex; align-items: center; justify-content: center; }
.escala-btn:hover, .escala-btn.active { border-color: #696cff; background: #696cff; color: #fff; }
.escala-labels { display: flex; justify-content: space-between; font-size: .72rem; color: #6e6b7b; margin-top: .3rem; }
.opcion-check-label { cursor: pointer; padding: .6rem 1rem; border: 1.5px solid #e7e7e7; border-radius: 8px; display: block; margin-bottom: .5rem; transition: all .15s; }
.opcion-check-label:hover { border-color: #696cff; background: #f4f3ff; }
input[type="checkbox"]:checked + .opcion-check-label,
input[type="radio"]:checked + .opcion-check-label { border-color: #696cff; background: #f4f3ff; }
.required-star::after { content: ' *'; color: #ea5455; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      {{-- Header --}}
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="d-flex align-items-start gap-3">
            <span class="badge bg-label-primary rounded p-3">
              <i class="ti tabler-clipboard-list fs-3"></i>
            </span>
            <div>
              <h4 class="fw-bold mb-1">{{ $encuesta->titulo }}</h4>
              @if($encuesta->descripcion)
                <p class="text-muted mb-1">{{ $encuesta->descripcion }}</p>
              @endif
              <div class="d-flex flex-wrap gap-2 small text-muted">
                @if($encuesta->fecha_fin)
                  <span><i class="ti tabler-calendar-due me-1 text-warning"></i>Fecha límite: {{ $encuesta->fecha_fin->format('d/m/Y') }}</span>
                @endif
                <span><i class="ti tabler-help-circle me-1 text-info"></i>{{ $encuesta->preguntas->count() }} pregunta(s)</span>
                <span class="badge @if($encuesta->modulo==='sci') badge-modulo-sci @elseif($encuesta->modulo==='integridad') badge-modulo-integridad @else bg-label-secondary @endif">{{ $encuesta->modulo_label }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Errores --}}
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
          <i class="ti tabler-alert-circle me-1"></i>
          <strong>Por favor completa los campos requeridos:</strong>
          <ul class="mb-0 mt-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <form method="POST" action="{{ route('encuestas.responder.store', $encuesta) }}" id="formResponder">
        @csrf

        {{-- Preguntas --}}
        @foreach($encuesta->preguntas as $i => $pregunta)
        <div class="pregunta-card @if($errors->has('respuesta_'.$pregunta->id)) border-danger @endif">
          <div class="d-flex align-items-start gap-3 mb-3">
            <span class="pregunta-num">{{ $i + 1 }}</span>
            <div>
              <p class="fw-semibold mb-0 {{ $pregunta->requerida ? 'required-star' : '' }}">{{ $pregunta->texto }}</p>
              <small class="text-muted">{{ $pregunta->tipo_label }}</small>
            </div>
          </div>

          @error('respuesta_'.$pregunta->id)
            <div class="text-danger small mb-2"><i class="ti tabler-alert-circle me-1"></i>{{ $message }}</div>
          @enderror

          {{-- Opción múltiple --}}
          @if($pregunta->tipo === 'opcion_multiple')
            @foreach($pregunta->opciones as $opcion)
              <div>
                <input type="radio" class="btn-check" name="respuesta_{{ $pregunta->id }}"
                  id="op-{{ $pregunta->id }}-{{ $opcion->id }}" value="{{ $opcion->id }}"
                  {{ old('respuesta_'.$pregunta->id) == $opcion->id ? 'checked' : '' }}>
                <label class="opcion-check-label" for="op-{{ $pregunta->id }}-{{ $opcion->id }}">
                  <i class="ti tabler-circle me-2 text-muted"></i>{{ $opcion->texto }}
                </label>
              </div>
            @endforeach

          {{-- Selección múltiple --}}
          @elseif($pregunta->tipo === 'seleccion_multiple')
            @foreach($pregunta->opciones as $opcion)
              <div>
                <input type="checkbox" class="btn-check" name="respuesta_{{ $pregunta->id }}[]"
                  id="op-{{ $pregunta->id }}-{{ $opcion->id }}" value="{{ $opcion->id }}"
                  {{ in_array($opcion->id, (array)old('respuesta_'.$pregunta->id, [])) ? 'checked' : '' }}>
                <label class="opcion-check-label" for="op-{{ $pregunta->id }}-{{ $opcion->id }}">
                  <i class="ti tabler-square me-2 text-muted"></i>{{ $opcion->texto }}
                </label>
              </div>
            @endforeach

          {{-- Escala --}}
          @elseif($pregunta->tipo === 'escala')
            <div>
              <input type="hidden" name="respuesta_{{ $pregunta->id }}" id="escala-val-{{ $pregunta->id }}"
                value="{{ old('respuesta_'.$pregunta->id) }}">
              <div class="d-flex gap-2 mb-1">
                @for($v = 1; $v <= 5; $v++)
                  <button type="button"
                    class="escala-btn {{ old('respuesta_'.$pregunta->id) == $v ? 'active' : '' }}"
                    onclick="seleccionarEscala({{ $pregunta->id }}, {{ $v }}, this)">{{ $v }}</button>
                @endfor
              </div>
              <div class="escala-labels">
                <span>Muy malo</span><span>Malo</span><span>Regular</span><span>Bueno</span><span>Muy bueno</span>
              </div>
            </div>

          {{-- Texto libre --}}
          @elseif($pregunta->tipo === 'texto_libre')
            <textarea name="respuesta_{{ $pregunta->id }}" class="form-control" rows="4"
              placeholder="Escribe tu respuesta aquí...">{{ old('respuesta_'.$pregunta->id) }}</textarea>
          @endif

        </div>
        @endforeach

        {{-- Botones --}}
        <div class="d-flex justify-content-between align-items-center mt-4">
          <a href="{{ route('encuestas.index') }}" class="btn btn-outline-secondary">
            <i class="ti tabler-arrow-left me-1"></i> Cancelar
          </a>
          <button type="submit" class="btn btn-primary btn-lg px-5">
            <i class="ti tabler-send me-1"></i> Enviar respuesta
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<style>
.badge-modulo-sci        { background: #e3f2fd; color: #1565c0; }
.badge-modulo-integridad { background: #e8f5e9; color: #2e7d32; }
</style>
<script>
function seleccionarEscala(preguntaId, valor, btn) {
  document.getElementById('escala-val-' + preguntaId).value = valor;
  btn.closest('.d-flex').querySelectorAll('.escala-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}
</script>
@endsection
