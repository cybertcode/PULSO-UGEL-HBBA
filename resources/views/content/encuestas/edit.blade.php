@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Editar Encuesta — PULSO UGEL')

@section('page-style')
<style>
.pregunta-item { border: 1px solid #e7e7e7; border-radius: 10px; background: #fafafa; padding: 1rem; }
.opciones-container .opcion-row { display: flex; gap: .5rem; align-items: center; margin-bottom: .4rem; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Encuestas /</span> Editar: {{ $encuesta->titulo }}</h4>
    <a href="{{ route('encuestas.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="ti tabler-arrow-left me-1"></i> Volver
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-4"><i class="ti tabler-check me-1"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  <form method="POST" action="{{ route('encuestas.update', $encuesta) }}" id="formEncuesta">
    @csrf @method('PUT')

    <div class="row g-4">
      {{-- Columna izquierda: datos + destinatarios --}}
      <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
          <div class="card-header"><h5 class="mb-0"><i class="ti tabler-info-circle me-1"></i> Datos Generales</h5></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                value="{{ old('titulo', $encuesta->titulo) }}" required>
              @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $encuesta->descripcion) }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Módulo</label>
              <select name="modulo" class="form-select">
                <option value="ambos"      {{ old('modulo',$encuesta->modulo) === 'ambos'      ? 'selected':'' }}>SCI + Integridad</option>
                <option value="sci"        {{ old('modulo',$encuesta->modulo) === 'sci'        ? 'selected':'' }}>SCI</option>
                <option value="integridad" {{ old('modulo',$encuesta->modulo) === 'integridad' ? 'selected':'' }}>Integridad</option>
              </select>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label fw-semibold small">Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control form-control-sm"
                  value="{{ old('fecha_inicio', $encuesta->fecha_inicio?->format('Y-m-d')) }}">
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold small">Fecha límite</label>
                <input type="date" name="fecha_fin" class="form-control form-control-sm"
                  value="{{ old('fecha_fin', $encuesta->fecha_fin?->format('Y-m-d')) }}">
              </div>
            </div>
          </div>
        </div>

        <div class="card shadow-sm">
          <div class="card-header"><h5 class="mb-0"><i class="ti tabler-users me-1"></i> Destinatarios</h5></div>
          <div class="card-body">
            @php
              $destTodos = $encuesta->destinatarios->firstWhere('tipo','todos');
              $destUnidades = $encuesta->destinatarios->where('tipo','unidad_organica')->pluck('referencia_id')->toArray();
              $destRoles = $encuesta->destinatarios->where('tipo','rol')->pluck('referencia_id')->toArray();
              $destUsuarios = $encuesta->destinatarios->where('tipo','usuario')->pluck('referencia_id')->toArray();
            @endphp

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="destTodos" onchange="toggleTodos(this)"
                {{ $destTodos ? 'checked' : '' }}>
              <label class="form-check-label fw-semibold" for="destTodos">Todos los usuarios activos</label>
            </div>
            <input type="hidden" name="destinatarios[0][tipo]" id="inputDestTodos" value="{{ $destTodos ? 'todos' : '' }}">

            <div id="seccionDetallada" style="{{ $destTodos ? 'opacity:.4;pointer-events:none' : '' }}">
              <div class="mb-3">
                <label class="form-label small fw-semibold">Por Unidad Orgánica</label>
                <select name="destinatarios[1][ids][]" class="form-select form-select-sm" multiple>
                  @foreach($unidades as $u)
                    <option value="{{ $u->id }}" {{ in_array($u->id, $destUnidades) ? 'selected':'' }}>{{ $u->nombre }}</option>
                  @endforeach
                </select>
                <input type="hidden" name="destinatarios[1][tipo]" value="unidad_organica">
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold">Por Rol</label>
                <select name="destinatarios[2][ids][]" class="form-select form-select-sm" multiple>
                  @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ in_array($r->id, $destRoles) ? 'selected':'' }}>{{ $r->name }}</option>
                  @endforeach
                </select>
                <input type="hidden" name="destinatarios[2][tipo]" value="rol">
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold">Usuarios individuales</label>
                <select name="destinatarios[3][ids][]" class="form-select form-select-sm" multiple>
                  @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ in_array($u->id, $destUsuarios) ? 'selected':'' }}>{{ $u->name }} ({{ $u->dni }})</option>
                  @endforeach
                </select>
                <input type="hidden" name="destinatarios[3][tipo]" value="usuario">
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Columna derecha: preguntas --}}
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti tabler-help-circle me-1"></i> Preguntas</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarPregunta()">
              <i class="ti tabler-plus me-1"></i> Agregar pregunta
            </button>
          </div>
          <div class="card-body">
            <div id="listaPreguntas"></div>
            <div id="sinPreguntas" class="text-center py-4 text-muted" style="display:none">
              <i class="ti tabler-help fs-1 d-block mb-2"></i>
              Agrega al menos una pregunta.
            </div>
          </div>
          <div class="card-footer text-end">
            <a href="{{ route('encuestas.index') }}" class="btn btn-outline-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">
              <i class="ti tabler-device-floppy me-1"></i> Guardar cambios
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@section('page-script')
<script>
let preguntaIdx = 0;

const preguntasExistentes = @json($encuesta->preguntas->map(fn($p) => [
    'id' => $p->id,
    'texto' => $p->texto,
    'tipo' => $p->tipo,
    'requerida' => $p->requerida,
    'opciones' => $p->opciones->pluck('texto'),
]));

function agregarPregunta(data = null) {
  if (document.querySelectorAll('.pregunta-item').length === 0) {
    document.getElementById('sinPreguntas').style.display = 'none';
  }
  const idx = preguntaIdx++;
  const tipo = data?.tipo || 'opcion_multiple';
  const div = document.createElement('div');
  div.className = 'pregunta-item mb-3';
  div.id = 'pq-' + idx;
  div.innerHTML = `
    <div class="d-flex align-items-start gap-2 mb-2">
      <div class="flex-grow-1">
        <input type="text" name="preguntas[${idx}][texto]" class="form-control mb-2"
          placeholder="Escribe la pregunta aquí..." value="${data?.texto || ''}" required>
        <div class="d-flex gap-2 align-items-center flex-wrap">
          <select name="preguntas[${idx}][tipo]" class="form-select form-select-sm" style="max-width:220px"
            onchange="cambiarTipo(this, ${idx})">
            <option value="opcion_multiple"    ${tipo==='opcion_multiple'    ?'selected':''}>Opción múltiple</option>
            <option value="seleccion_multiple" ${tipo==='seleccion_multiple' ?'selected':''}>Selección múltiple</option>
            <option value="escala"             ${tipo==='escala'             ?'selected':''}>Escala (1-5)</option>
            <option value="texto_libre"        ${tipo==='texto_libre'        ?'selected':''}>Texto libre</option>
          </select>
          <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="preguntas[${idx}][requerida]" value="1"
              ${(data?.requerida ?? true) ? 'checked' : ''} id="req-${idx}">
            <label class="form-check-label small" for="req-${idx}">Obligatoria</label>
          </div>
        </div>
      </div>
      <button type="button" class="btn btn-icon btn-sm btn-outline-danger mt-1" onclick="eliminarPregunta(${idx})">
        <i class="ti tabler-trash"></i>
      </button>
    </div>
    <div id="opciones-${idx}" class="opciones-container ms-4 mt-2"
      style="${['opcion_multiple','seleccion_multiple'].includes(tipo) ? '' : 'display:none'}">
    </div>
    <div id="escala-${idx}" class="ms-4 mt-2" style="${tipo==='escala' ? '' : 'display:none'}">
      <div class="d-flex gap-2">${[1,2,3,4,5].map(v=>`<span class="badge bg-label-secondary px-3 py-2">${v}</span>`).join('')}</div>
    </div>
    <div id="libre-${idx}" class="ms-4 mt-2" style="${tipo==='texto_libre' ? '' : 'display:none'}">
      <div class="form-control bg-light text-muted" style="min-height:50px">Campo de texto abierto</div>
    </div>`;
  document.getElementById('listaPreguntas').appendChild(div);

  const opciones = data?.opciones || [''];
  opciones.forEach((txt, j) => {
    const cont = document.querySelector(`#opciones-${idx}`);
    const row = document.createElement('div'); row.className = 'opcion-row';
    row.innerHTML = `<input type="text" name="preguntas[${idx}][opciones][]" class="form-control form-control-sm"
      placeholder="Opción ${j+1}" value="${txt}">
      ${j===0 ? `<button type="button" class="btn btn-icon btn-sm btn-outline-secondary" onclick="agregarOpcion(${idx})"><i class="ti tabler-plus"></i></button>` : `<button type="button" class="btn btn-icon btn-sm btn-outline-danger" onclick="this.parentElement.remove()"><i class="ti tabler-x"></i></button>`}`;
    cont.appendChild(row);
  });
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
  const row = document.createElement('div'); row.className = 'opcion-row';
  row.innerHTML = `<input type="text" name="preguntas[${idx}][opciones][]" class="form-control form-control-sm" placeholder="Opción ${n}">
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
  document.getElementById('inputDestTodos').value = cb.checked ? 'todos' : '';
  document.getElementById('seccionDetallada').style.opacity = cb.checked ? '.4' : '1';
  document.getElementById('seccionDetallada').style.pointerEvents = cb.checked ? 'none' : '';
}

// Cargar preguntas existentes
preguntasExistentes.forEach(p => agregarPregunta(p));
</script>
@endsection
