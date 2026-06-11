@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Nueva Encuesta — PULSO UGEL')

@section('page-style')
<style>
.wizard-step { display: none; }
.wizard-step.active { display: block; }
.step-indicator { display: flex; gap: 0; counter-reset: step; }
.step-indicator .step { flex: 1; text-align: center; position: relative; }
.step-indicator .step .step-num {
  width: 32px; height: 32px; border-radius: 50%; border: 2px solid #d5d4dd;
  background: #fff; color: #6e6b7b; font-weight: 700; font-size: .85rem;
  display: inline-flex; align-items: center; justify-content: center; margin-bottom: 4px; position: relative; z-index: 1;
}
.step-indicator .step.done .step-num  { border-color: #696cff; background: #696cff; color: #fff; }
.step-indicator .step.active .step-num { border-color: #696cff; color: #696cff; }
.step-indicator .step::before {
  content: ''; position: absolute; top: 15px; left: 50%; right: -50%;
  height: 2px; background: #d5d4dd; z-index: 0;
}
.step-indicator .step:last-child::before { display: none; }
.step-indicator .step.done::before { background: #696cff; }
.pregunta-item { border: 1px solid #e7e7e7; border-radius: 10px; background: #fafafa; padding: 1rem; }
.pregunta-item .drag-handle { cursor: grab; color: #aaa; }
.opciones-container .opcion-row { display: flex; gap: .5rem; align-items: center; margin-bottom: .4rem; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Encuestas /</span> Nueva Encuesta</h4>
    </div>
    <a href="{{ route('encuestas.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="ti tabler-arrow-left me-1"></i> Volver
    </a>
  </div>

  {{-- Indicador de pasos --}}
  <div class="card shadow-sm mb-4">
    <div class="card-body py-3">
      <div class="step-indicator" id="stepIndicator">
        <div class="step active" id="ind-1">
          <div class="step-num">1</div>
          <div class="small text-muted">Datos generales</div>
        </div>
        <div class="step" id="ind-2">
          <div class="step-num">2</div>
          <div class="small text-muted">Preguntas</div>
        </div>
        <div class="step" id="ind-3">
          <div class="step-num">3</div>
          <div class="small text-muted">Destinatarios</div>
        </div>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('encuestas.store') }}" id="formEncuesta">
    @csrf

    {{-- PASO 1: Datos generales --}}
    <div class="wizard-step active" id="step-1">
      <div class="card shadow-sm">
        <div class="card-header"><h5 class="mb-0"><i class="ti tabler-info-circle me-1 text-primary"></i> Datos Generales</h5></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                value="{{ old('titulo') }}" placeholder="Ej: Encuesta de Clima Institucional 2026" required>
              @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3"
                placeholder="Objetivo e instrucciones de la encuesta...">{{ old('descripcion') }}</textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Módulo <span class="text-danger">*</span></label>
              <select name="modulo" class="form-select @error('modulo') is-invalid @enderror" required>
                <option value="ambos"      {{ old('modulo','ambos') === 'ambos'      ? 'selected':'' }}>SCI + Integridad</option>
                <option value="sci"        {{ old('modulo') === 'sci'        ? 'selected':'' }}>SCI</option>
                <option value="integridad" {{ old('modulo') === 'integridad' ? 'selected':'' }}>Integridad</option>
              </select>
              @error('modulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha inicio</label>
              <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha límite</label>
              <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}">
            </div>
          </div>
        </div>
        <div class="card-footer text-end">
          <button type="button" class="btn btn-primary" onclick="irPaso(2)">
            Siguiente <i class="ti tabler-arrow-right ms-1"></i>
          </button>
        </div>
      </div>
    </div>

    {{-- PASO 2: Preguntas --}}
    <div class="wizard-step" id="step-2">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="ti tabler-help-circle me-1 text-primary"></i> Preguntas</h5>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarPregunta()">
            <i class="ti tabler-plus me-1"></i> Agregar pregunta
          </button>
        </div>
        <div class="card-body">
          <div id="listaPreguntas"></div>
          <div id="sinPreguntas" class="text-center py-4 text-muted">
            <i class="ti tabler-help fs-1 d-block mb-2"></i>
            Aún no hay preguntas. Haz clic en <strong>"Agregar pregunta"</strong>.
          </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
          <button type="button" class="btn btn-outline-secondary" onclick="irPaso(1)">
            <i class="ti tabler-arrow-left me-1"></i> Anterior
          </button>
          <button type="button" class="btn btn-primary" onclick="irPaso(3)">
            Siguiente <i class="ti tabler-arrow-right ms-1"></i>
          </button>
        </div>
      </div>
    </div>

    {{-- PASO 3: Destinatarios --}}
    <div class="wizard-step" id="step-3">
      <div class="card shadow-sm">
        <div class="card-header"><h5 class="mb-0"><i class="ti tabler-users me-1 text-primary"></i> Destinatarios</h5></div>
        <div class="card-body">
          <p class="text-muted small mb-3">Define quiénes recibirán esta encuesta. Puedes combinar criterios.</p>

          {{-- Todos --}}
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="destTodos" onchange="toggleTodos(this)">
            <label class="form-check-label fw-semibold" for="destTodos">
              <i class="ti tabler-users me-1 text-primary"></i> Todos los usuarios activos
            </label>
          </div>
          <input type="hidden" name="destinatarios[0][tipo]" id="inputDestTodos" value="">

          <hr class="my-3">

          <div id="seccionDetallada">
            {{-- Por unidad --}}
            <div class="mb-4">
              <label class="form-label fw-semibold">Por Unidad Orgánica</label>
              <select name="destinatarios[1][ids][]" id="selectUnidades" class="form-select" multiple>
                @foreach($unidades as $u)
                  <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                @endforeach
              </select>
              <input type="hidden" name="destinatarios[1][tipo]" value="unidad_organica">
            </div>

            {{-- Por rol --}}
            <div class="mb-4">
              <label class="form-label fw-semibold">Por Rol</label>
              <select name="destinatarios[2][ids][]" id="selectRoles" class="form-select" multiple>
                @foreach($roles as $r)
                  <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
              </select>
              <input type="hidden" name="destinatarios[2][tipo]" value="rol">
            </div>

            {{-- Individual --}}
            <div class="mb-4">
              <label class="form-label fw-semibold">Usuarios individuales</label>
              <select name="destinatarios[3][ids][]" id="selectUsuarios" class="form-select" multiple>
                @foreach($usuarios as $u)
                  <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->dni }})</option>
                @endforeach
              </select>
              <input type="hidden" name="destinatarios[3][tipo]" value="usuario">
            </div>
          </div>

        </div>
        <div class="card-footer d-flex justify-content-between">
          <button type="button" class="btn btn-outline-secondary" onclick="irPaso(2)">
            <i class="ti tabler-arrow-left me-1"></i> Anterior
          </button>
          <button type="submit" class="btn btn-success">
            <i class="ti tabler-device-floppy me-1"></i> Guardar como borrador
          </button>
        </div>
      </div>
    </div>

  </form>
</div>
@endsection

@section('page-script')
<script>
let preguntaIdx = 0;
let pasoActual = 1;

function irPaso(p) {
  if (p === 3 && document.querySelectorAll('.pregunta-item').length === 0) {
    alert('Agrega al menos una pregunta antes de continuar.');
    return;
  }
  document.querySelector('.wizard-step.active').classList.remove('active');
  document.getElementById('step-' + p).classList.add('active');
  // Indicadores
  for (let i = 1; i <= 3; i++) {
    const ind = document.getElementById('ind-' + i);
    ind.classList.remove('active', 'done');
    if (i < p) ind.classList.add('done');
    if (i === p) ind.classList.add('active');
  }
  pasoActual = p;
}

function agregarPregunta() {
  document.getElementById('sinPreguntas').style.display = 'none';
  const idx = preguntaIdx++;
  const div = document.createElement('div');
  div.className = 'pregunta-item mb-3';
  div.id = 'pq-' + idx;
  div.innerHTML = `
    <div class="d-flex align-items-start gap-2 mb-2">
      <span class="drag-handle mt-2"><i class="ti tabler-grip-vertical"></i></span>
      <div class="flex-grow-1">
        <input type="text" name="preguntas[${idx}][texto]" class="form-control mb-2"
          placeholder="Escribe la pregunta aquí..." required>
        <div class="d-flex gap-2 align-items-center flex-wrap">
          <select name="preguntas[${idx}][tipo]" class="form-select form-select-sm" style="max-width:220px"
            onchange="cambiarTipo(this, ${idx})">
            <option value="opcion_multiple">Opción múltiple</option>
            <option value="seleccion_multiple">Selección múltiple</option>
            <option value="escala">Escala de valoración (1-5)</option>
            <option value="texto_libre">Texto libre</option>
          </select>
          <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="preguntas[${idx}][requerida]" value="1" checked id="req-${idx}">
            <label class="form-check-label small" for="req-${idx}">Obligatoria</label>
          </div>
        </div>
      </div>
      <button type="button" class="btn btn-icon btn-sm btn-outline-danger mt-1" onclick="eliminarPregunta(${idx})" title="Eliminar pregunta">
        <i class="ti tabler-trash"></i>
      </button>
    </div>
    <div id="opciones-${idx}" class="opciones-container ms-4 mt-2">
      <div class="opcion-row">
        <input type="text" name="preguntas[${idx}][opciones][]" class="form-control form-control-sm" placeholder="Opción 1">
        <button type="button" class="btn btn-icon btn-sm btn-outline-secondary" onclick="agregarOpcion(${idx})"><i class="ti tabler-plus"></i></button>
      </div>
    </div>
    <div id="escala-${idx}" class="ms-4 mt-2" style="display:none">
      <div class="d-flex gap-2">
        ${[1,2,3,4,5].map(v => `<span class="badge bg-label-secondary px-3 py-2">${v}</span>`).join('')}
      </div>
      <small class="text-muted">El usuario seleccionará un valor del 1 al 5.</small>
    </div>
    <div id="libre-${idx}" class="ms-4 mt-2" style="display:none">
      <div class="form-control bg-light text-muted" style="min-height:60px">Campo de texto abierto</div>
    </div>`;
  document.getElementById('listaPreguntas').appendChild(div);
}

function cambiarTipo(sel, idx) {
  const tipo = sel.value;
  document.getElementById('opciones-' + idx).style.display  = ['opcion_multiple','seleccion_multiple'].includes(tipo) ? '' : 'none';
  document.getElementById('escala-' + idx).style.display    = tipo === 'escala' ? '' : 'none';
  document.getElementById('libre-' + idx).style.display     = tipo === 'texto_libre' ? '' : 'none';
}

function agregarOpcion(idx) {
  const cont = document.querySelector(`#opciones-${idx}`);
  const n = cont.querySelectorAll('.opcion-row').length + 1;
  const row = document.createElement('div');
  row.className = 'opcion-row';
  row.innerHTML = `
    <input type="text" name="preguntas[${idx}][opciones][]" class="form-control form-control-sm" placeholder="Opción ${n}">
    <button type="button" class="btn btn-icon btn-sm btn-outline-danger" onclick="this.parentElement.remove()"><i class="ti tabler-x"></i></button>`;
  cont.appendChild(row);
}

function eliminarPregunta(idx) {
  document.getElementById('pq-' + idx).remove();
  if (document.querySelectorAll('.pregunta-item').length === 0) {
    document.getElementById('sinPreguntas').style.display = '';
  }
}

function toggleTodos(cb) {
  document.getElementById('inputDestTodos').value  = cb.checked ? 'todos' : '';
  document.getElementById('seccionDetallada').style.opacity = cb.checked ? '.4' : '1';
  document.getElementById('seccionDetallada').style.pointerEvents = cb.checked ? 'none' : '';
}
</script>
@endsection
