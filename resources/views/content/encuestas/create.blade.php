@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Nueva Encuesta — PULSO UGEL')

@section('page-style')
<style>
/* ── Wizard ── */
.wizard-step { display: none; animation: fadeInUp .25s ease; }
.wizard-step.active { display: block; }
@keyframes fadeInUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

.step-bar { display: flex; align-items: center; gap: 0; }
.step-bar .sb-item { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; }
.step-bar .sb-item::after {
  content: ''; position: absolute; top: 16px; left: 50%; width: 100%; height: 2px;
  background: #e0dffe; z-index: 0;
}
.step-bar .sb-item:last-child::after { display: none; }
.step-bar .sb-item.done::after { background: #696cff; }
.sb-circle {
  width: 34px; height: 34px; border-radius: 50%; border: 2px solid #e0dffe;
  background: #fff; color: #a8a5c1; font-weight: 700; font-size: .82rem;
  display: flex; align-items: center; justify-content: center;
  position: relative; z-index: 1; transition: all .25s;
}
.sb-item.active .sb-circle { border-color: #696cff; color: #696cff; box-shadow: 0 0 0 4px rgba(105,108,255,.12); }
.sb-item.done  .sb-circle  { border-color: #696cff; background: #696cff; color: #fff; }
.sb-label { font-size: .72rem; color: #a8a5c1; margin-top: 4px; font-weight: 500; }
.sb-item.active .sb-label, .sb-item.done .sb-label { color: #696cff; }

/* ── Tarjeta de pregunta ── */
.pq-card {
  background: #fff; border: 1.5px solid #e7e7e7; border-radius: 14px;
  padding: 1.1rem 1.2rem; margin-bottom: 1rem;
  transition: box-shadow .2s, border-color .2s;
  animation: fadeInUp .2s ease;
}
.pq-card:hover { box-shadow: 0 4px 18px rgba(105,108,255,.10); border-color: #c5c4f5; }
.pq-card.has-error { border-color: #ff3e1d !important; }
.pq-card.dragging { opacity: .5; border: 2px dashed #696cff; }
.pq-header { display: flex; gap: .8rem; align-items: flex-start; }
.pq-num {
  min-width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg,#696cff,#9b59b6);
  color: #fff; font-weight: 700; font-size: .78rem;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 6px;
}
.pq-texto { flex: 1; }
.pq-texto input.form-control {
  border: none; border-bottom: 1.5px solid #e7e7e7; border-radius: 0;
  padding-left: 0; font-size: .97rem; font-weight: 500; background: transparent;
  transition: border-color .2s;
}
.pq-texto input.form-control:focus { border-color: #696cff; box-shadow: none; }
.pq-texto input.form-control.is-invalid { border-color: #ff3e1d; }
.pq-tipo-selector { display: flex; flex-wrap: wrap; gap: .45rem; margin-top: .7rem; }
.tipo-pill {
  border: 1.5px solid #e0dffe; border-radius: 20px; padding: .3rem .9rem;
  font-size: .73rem; font-weight: 600; color: #696cff; cursor: pointer;
  background: #fff; transition: all .15s; display: flex; align-items: center; gap: .35rem;
  white-space: nowrap;
}
.tipo-pill:hover { background: #f0efff; border-color: #696cff; }
.tipo-pill.active { background: #696cff; color: #fff; border-color: #696cff; }
.tipo-pill i { font-size: .85rem; }

/* ── Zona de opciones ── */
.pq-body { padding-top: .8rem; border-top: 1px dashed #e7e7e7; margin-top: .8rem; }
.opcion-row { display: flex; gap: .5rem; align-items: center; margin-bottom: .4rem; animation: fadeInUp .15s ease; }
.opcion-row .opcion-prefix {
  width: 22px; height: 22px; border: 1.5px solid #c5c4f5; border-radius: 50%;
  flex-shrink: 0; display: flex; align-items: center; justify-content: center;
  font-size: .65rem; font-weight: 700; color: #696cff;
}
.opcion-row.check-style .opcion-prefix { border-radius: 4px; }
.opcion-row input.form-control {
  border: none; border-bottom: 1px solid #e7e7e7; border-radius: 0; background: transparent;
  padding-left: 0; font-size: .88rem; transition: border-color .15s;
}
.opcion-row input.form-control:focus { border-color: #696cff; box-shadow: none; }

/* Preview bloques */
.preview-bloque { background: #f8f7fe; border: 1px solid #e0dffe; border-radius: 10px; padding: .9rem 1.1rem; }
.si-no-btn, .vf-btn {
  padding: .55rem 2rem; border-radius: 8px; font-weight: 600; font-size: .9rem;
  cursor: default; transition: all .15s; border: 2px solid;
}
.si-no-btn.si  { border-color: #28a745; color: #28a745; background: #f0fff4; }
.si-no-btn.no  { border-color: #dc3545; color: #dc3545; background: #fff5f5; }
.vf-btn.v      { border-color: #17a2b8; color: #17a2b8; background: #f0fbff; }
.vf-btn.f      { border-color: #fd7e14; color: #fd7e14; background: #fff8f0; }

.escala-preview span {
  width: 44px; height: 44px; border-radius: 10px; border: 2px solid #e0dffe;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 1rem; color: #696cff; background: #fff;
}

/* ── Destinatarios ── */
.dest-option {
  border: 1.5px solid #e7e7e7; border-radius: 12px; padding: .9rem 1.1rem;
  cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: .8rem;
}
.dest-option:hover { border-color: #696cff; background: #f8f7ff; }
.dest-option.active { border-color: #696cff; background: #f0efff; }
.dest-option .dest-icon { font-size: 1.5rem; color: #696cff; }

.drag-handle { cursor: grab; color: #c5c4f5; font-size: 1rem; margin-top: 8px; flex-shrink: 0; }
.drag-handle:active { cursor: grabbing; }

/* ── Error global ── */
.alert-server-error { border-left: 4px solid #ff3e1d; background: #fff5f5; border-radius: 8px; padding: 1rem 1.2rem; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Encuestas /</span> Nueva Encuesta</h4>
    </div>
    <a href="{{ route('encuestas.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="ti tabler-arrow-left me-1"></i> Volver
    </a>
  </div>

  {{-- Errores del servidor (si llegaran por fallback) --}}
  @if($errors->any())
  <div class="alert-server-error mb-4" id="serverErrors">
    <div class="fw-bold text-danger mb-1"><i class="ti tabler-alert-circle me-1"></i>Corrige los siguientes errores:</div>
    <ul class="mb-0 ps-3 small">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  {{-- Step bar --}}
  <div class="card shadow-sm mb-4 border-0">
    <div class="card-body py-3 px-4">
      <div class="step-bar" id="stepBar">
        <div class="sb-item active" id="sb-1">
          <div class="sb-circle"><i class="ti tabler-info-circle" style="font-size:.9rem"></i></div>
          <span class="sb-label">Datos</span>
        </div>
        <div class="sb-item" id="sb-2">
          <div class="sb-circle"><i class="ti tabler-help-circle" style="font-size:.9rem"></i></div>
          <span class="sb-label">Preguntas</span>
        </div>
        <div class="sb-item" id="sb-3">
          <div class="sb-circle"><i class="ti tabler-users" style="font-size:.9rem"></i></div>
          <span class="sb-label">Destinatarios</span>
        </div>
      </div>
    </div>
  </div>

  <form id="formEncuesta" novalidate>
    @csrf

    {{-- ╔══════════════════════════════╗ --}}
    {{-- ║  PASO 1: Datos generales     ║ --}}
    {{-- ╚══════════════════════════════╝ --}}
    <div class="wizard-step active" id="step-1">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
          <h5 class="mb-0 fw-bold"><i class="ti tabler-sparkles me-2 text-primary"></i>Datos de la encuesta</h5>
          <p class="text-muted small mb-0">Completa la información básica antes de agregar preguntas.</p>
        </div>
        <div class="card-body px-4">
          <div class="row g-4">
            <div class="col-12">
              <label class="form-label fw-semibold">Título de la encuesta <span class="text-danger">*</span></label>
              <input type="text" name="titulo" id="inp-titulo"
                class="form-control form-control-lg"
                value="{{ old('titulo') }}"
                placeholder="Ej: Encuesta de Clima Institucional 2026">
              <div class="invalid-feedback" id="err-titulo">El título es obligatorio.</div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción / Instrucciones</label>
              <textarea name="descripcion" id="inp-descripcion" class="form-control" rows="3"
                placeholder="Explica el propósito de esta encuesta a los participantes...">{{ old('descripcion') }}</textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Módulo <span class="text-danger">*</span></label>
              <select name="modulo" id="inp-modulo" class="form-select">
                <option value="ambos"      {{ old('modulo','ambos') === 'ambos'      ? 'selected':'' }}>🔵 SCI + Integridad</option>
                <option value="sci"        {{ old('modulo') === 'sci'        ? 'selected':'' }}>📋 Solo SCI</option>
                <option value="integridad" {{ old('modulo') === 'integridad' ? 'selected':'' }}>🛡️ Solo Integridad</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha de inicio</label>
              <input type="date" name="fecha_inicio" id="inp-fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha límite</label>
              <input type="date" name="fecha_fin" id="inp-fecha_fin" class="form-control" value="{{ old('fecha_fin') }}">
              <div class="invalid-feedback" id="err-fecha_fin">La fecha límite debe ser posterior a la de inicio.</div>
            </div>
          </div>
        </div>
        <div class="card-footer bg-white border-top-0 px-4 pb-4 text-end">
          <button type="button" class="btn btn-primary px-4" onclick="irPaso(2)">
            Siguiente: Agregar preguntas <i class="ti tabler-arrow-right ms-1"></i>
          </button>
        </div>
      </div>
    </div>

    {{-- ╔══════════════════════════════╗ --}}
    {{-- ║  PASO 2: Preguntas           ║ --}}
    {{-- ╚══════════════════════════════╝ --}}
    <div class="wizard-step" id="step-2">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
              <h5 class="mb-0 fw-bold"><i class="ti tabler-list-details me-2 text-primary"></i>Constructor de preguntas</h5>
              <p class="text-muted small mb-0">Agrega y ordena las preguntas de tu encuesta.</p>
            </div>
            <button type="button" class="btn btn-primary" onclick="agregarPregunta()">
              <i class="ti tabler-plus me-1"></i> Nueva pregunta
            </button>
          </div>
          <div class="d-flex flex-wrap gap-2 mt-3 pb-3 border-bottom">
            <span class="text-muted small me-1 align-self-center">Agregar rápido:</span>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarPregunta('opcion_multiple')">
              <i class="ti tabler-circle-dot me-1"></i>Opción única
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarPregunta('seleccion_multiple')">
              <i class="ti tabler-checkbox me-1"></i>Selección múltiple
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarPregunta('escala')">
              <i class="ti tabler-stars me-1"></i>Escala 1-5
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarPregunta('si_no')">
              <i class="ti tabler-checks me-1"></i>Sí / No
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarPregunta('verdadero_falso')">
              <i class="ti tabler-shield-check me-1"></i>Verdadero / Falso
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarPregunta('desplegable')">
              <i class="ti tabler-selector me-1"></i>Lista desplegable
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarPregunta('texto_libre')">
              <i class="ti tabler-text-size me-1"></i>Texto libre
            </button>
          </div>
        </div>
        <div class="card-body px-4 pt-3" id="preguntasWrap">
          <div id="listaPreguntas"></div>
          <div id="sinPreguntas" class="text-center py-5">
            <div class="mb-3" style="font-size:3rem">📋</div>
            <h6 class="text-muted">Aún no hay preguntas</h6>
            <p class="text-muted small">Usa el botón <strong>"Nueva pregunta"</strong> o los accesos rápidos de arriba.</p>
            <button type="button" class="btn btn-outline-primary mt-1" onclick="agregarPregunta()">
              <i class="ti tabler-plus me-1"></i> Agregar primera pregunta
            </button>
          </div>
        </div>
        <div class="card-footer bg-white border-top px-4 pb-4 d-flex justify-content-between">
          <button type="button" class="btn btn-outline-secondary" onclick="irPaso(1)">
            <i class="ti tabler-arrow-left me-1"></i> Anterior
          </button>
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted small" id="contPreguntas">0 preguntas</span>
            <button type="button" class="btn btn-primary px-4" onclick="irPaso(3)">
              Siguiente: Destinatarios <i class="ti tabler-arrow-right ms-1"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- ╔══════════════════════════════╗ --}}
    {{-- ║  PASO 3: Destinatarios       ║ --}}
    {{-- ╚══════════════════════════════╝ --}}
    <div class="wizard-step" id="step-3">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
          <h5 class="mb-0 fw-bold"><i class="ti tabler-users me-2 text-primary"></i>¿A quiénes va dirigida?</h5>
          <p class="text-muted small mb-0">Combina criterios — se enviarán alertas a los usuarios resultantes.</p>
        </div>
        <div class="card-body px-4">

          <div class="dest-option mb-3" id="destCardTodos" onclick="toggleDestTodos(this)">
            <span class="dest-icon"><i class="ti tabler-world"></i></span>
            <div class="flex-grow-1">
              <div class="fw-semibold">Todos los usuarios activos</div>
              <div class="text-muted small">Enviará la encuesta a todos los usuarios con estado activo.</div>
            </div>
            <div class="form-check mb-0">
              <input class="form-check-input" type="checkbox" id="cbTodos" onclick="event.stopPropagation()">
            </div>
          </div>

          <hr class="my-4">

          <div id="seccionDetallada">
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label fw-semibold small">
                  <i class="ti tabler-sitemap me-1 text-primary"></i>Por Unidad Orgánica
                </label>
                <select name="destinatarios[1][ids][]" class="form-select" multiple size="5" id="sel-unidades">
                  @foreach($unidades as $u)
                    <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                  @endforeach
                </select>
                <input type="hidden" name="destinatarios[1][tipo]" value="unidad_organica">
                <small class="text-muted">Mantén Ctrl para seleccionar varios.</small>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold small">
                  <i class="ti tabler-lock me-1 text-primary"></i>Por Rol del sistema
                </label>
                <select name="destinatarios[2][ids][]" class="form-select" multiple size="5" id="sel-roles">
                  @foreach($roles as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                  @endforeach
                </select>
                <input type="hidden" name="destinatarios[2][tipo]" value="rol">
                <small class="text-muted">Mantén Ctrl para seleccionar varios.</small>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold small">
                  <i class="ti tabler-user-check me-1 text-primary"></i>Usuarios individuales
                </label>
                <select name="destinatarios[3][ids][]" class="form-select" multiple size="5" id="sel-usuarios">
                  @foreach($usuarios as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->dni }}</option>
                  @endforeach
                </select>
                <input type="hidden" name="destinatarios[3][tipo]" value="usuario">
                <small class="text-muted">Mantén Ctrl para seleccionar varios.</small>
              </div>
            </div>
          </div>

          {{-- Error destinatarios --}}
          <div id="err-destinatarios" class="text-danger small mt-3" style="display:none">
            <i class="ti tabler-alert-circle me-1"></i>Selecciona al menos un destinatario.
          </div>

        </div>
        <div class="card-footer bg-white border-top px-4 pb-4 d-flex justify-content-between align-items-center">
          <button type="button" class="btn btn-outline-secondary" onclick="irPaso(2)">
            <i class="ti tabler-arrow-left me-1"></i> Anterior
          </button>
          <button type="button" class="btn btn-success btn-lg px-5" id="btnGuardar" onclick="submitEncuesta()">
            <span id="btnGuardarTxt"><i class="ti tabler-device-floppy me-1"></i> Guardar como borrador</span>
            <span id="btnGuardarSpin" style="display:none">
              <span class="spinner-border spinner-border-sm me-1"></span> Guardando...
            </span>
          </button>
        </div>
      </div>
    </div>

  </form>
</div>

{{-- Toast container --}}
<div id="toastContainer" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:.5rem;"></div>
@endsection

@section('page-script')
<script>
/* ═══════════════════════════════════════════
   CONFIGURACIÓN DE TIPOS
═══════════════════════════════════════════ */
const TIPOS = {
  opcion_multiple:    { label: 'Opción única',        icon: 'ti tabler-circle-dot',     color: 'text-primary' },
  seleccion_multiple: { label: 'Selección múltiple',  icon: 'ti tabler-checkbox',       color: 'text-info' },
  escala:             { label: 'Escala 1-5',           icon: 'ti tabler-stars',          color: 'text-warning' },
  si_no:              { label: 'Sí / No',              icon: 'ti tabler-checks',         color: 'text-success' },
  verdadero_falso:    { label: 'Verdadero / Falso',    icon: 'ti tabler-shield-check',   color: 'text-info' },
  desplegable:        { label: 'Lista desplegable',    icon: 'ti tabler-selector',       color: 'text-secondary' },
  texto_libre:        { label: 'Texto libre',          icon: 'ti tabler-text-size',      color: 'text-muted' },
};

let preguntaIdx = 0;

/* ═══════════════════════════════════════════
   WIZARD — navegación entre pasos
═══════════════════════════════════════════ */
function irPaso(p) {
  // Validar antes de avanzar
  if (p > 1 && !validarPaso1()) return;
  if (p > 2 && !validarPaso2()) return;

  document.querySelector('.wizard-step.active').classList.remove('active');
  document.getElementById('step-' + p).classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });

  for (let i = 1; i <= 3; i++) {
    const sb = document.getElementById('sb-' + i);
    sb.classList.remove('active', 'done');
    if (i < p) sb.classList.add('done');
    if (i === p) sb.classList.add('active');
  }
}

/* ── Validación Paso 1 ── */
function validarPaso1() {
  let ok = true;

  const titulo = document.getElementById('inp-titulo');
  if (!titulo.value.trim()) {
    titulo.classList.add('is-invalid');
    document.getElementById('err-titulo').style.display = 'block';
    titulo.focus();
    ok = false;
  } else {
    titulo.classList.remove('is-invalid');
    document.getElementById('err-titulo').style.display = 'none';
  }

  const fi = document.getElementById('inp-fecha_inicio').value;
  const ff = document.getElementById('inp-fecha_fin').value;
  const errFecha = document.getElementById('err-fecha_fin');
  if (fi && ff && ff < fi) {
    document.getElementById('inp-fecha_fin').classList.add('is-invalid');
    errFecha.style.display = 'block';
    ok = false;
  } else {
    document.getElementById('inp-fecha_fin').classList.remove('is-invalid');
    errFecha.style.display = 'none';
  }

  return ok;
}

/* ── Validación Paso 2 ── */
function validarPaso2() {
  const cards = document.querySelectorAll('.pq-card');
  if (cards.length === 0) {
    mostrarToast('Agrega al menos una pregunta antes de continuar.', 'warning');
    return false;
  }

  let ok = true;
  cards.forEach(card => {
    const inp = card.querySelector('input[type=text]');
    if (inp && !inp.value.trim()) {
      inp.classList.add('is-invalid');
      card.classList.add('has-error');
      ok = false;
    } else if (inp) {
      inp.classList.remove('is-invalid');
      card.classList.remove('has-error');
    }
  });

  if (!ok) {
    mostrarToast('Completa el texto de todas las preguntas.', 'danger');
  }
  return ok;
}

/* ── Validación Paso 3 ── */
function validarPaso3() {
  const cbTodos = document.getElementById('cbTodos').checked;
  if (cbTodos) return true;

  const unidades = document.getElementById('sel-unidades');
  const roles    = document.getElementById('sel-roles');
  const usuarios = document.getElementById('sel-usuarios');

  const haySeleccion = (unidades && unidades.selectedOptions.length > 0)
    || (roles    && roles.selectedOptions.length > 0)
    || (usuarios && usuarios.selectedOptions.length > 0);

  const err = document.getElementById('err-destinatarios');
  if (!haySeleccion) {
    err.style.display = 'block';
    mostrarToast('Selecciona al menos un destinatario.', 'warning');
    return false;
  }
  err.style.display = 'none';
  return true;
}

/* ═══════════════════════════════════════════
   SUBMIT VÍA AJAX — el form NUNCA recarga
═══════════════════════════════════════════ */
function submitEncuesta() {
  if (!validarPaso1() || !validarPaso2() || !validarPaso3()) return;

  const btn     = document.getElementById('btnGuardar');
  const btnTxt  = document.getElementById('btnGuardarTxt');
  const btnSpin = document.getElementById('btnGuardarSpin');
  btn.disabled  = true;
  btnTxt.style.display  = 'none';
  btnSpin.style.display = 'inline-flex';

  const form = document.getElementById('formEncuesta');
  const data = new FormData(form);
  // Añadir input hidden de destinatarios[0][tipo] si "todos" está marcado
  if (document.getElementById('cbTodos').checked) {
    data.set('destinatarios[0][tipo]', 'todos');
  }

  fetch('{{ route("encuestas.store") }}', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: data,
  })
  .then(async res => {
    const json = await res.json();
    if (res.ok && json.success) {
      mostrarToast('Encuesta guardada correctamente.', 'success');
      setTimeout(() => { window.location.href = json.redirect; }, 1200);
    } else if (res.status === 422 && json.errors) {
      mostrarErroresServidor(json.errors);
      btn.disabled  = false;
      btnTxt.style.display  = 'inline';
      btnSpin.style.display = 'none';
    } else {
      mostrarToast('Error inesperado. Intenta de nuevo.', 'danger');
      btn.disabled  = false;
      btnTxt.style.display  = 'inline';
      btnSpin.style.display = 'none';
    }
  })
  .catch(() => {
    mostrarToast('Error de conexión. Verifica tu red.', 'danger');
    btn.disabled  = false;
    btnTxt.style.display  = 'inline';
    btnSpin.style.display = 'none';
  });
}

/* Muestra errores de validación del servidor sin recargar */
function mostrarErroresServidor(errors) {
  // Errores generales
  const msgs = Object.values(errors).flat();
  mostrarToast(msgs[0] || 'Revisa los campos del formulario.', 'danger');

  // Errores de preguntas individuales (preguntas.0.texto, etc.)
  Object.entries(errors).forEach(([field, messages]) => {
    const match = field.match(/^preguntas\.(\d+)\.texto$/);
    if (match) {
      const idx = parseInt(match[1]);
      const cards = document.querySelectorAll('.pq-card');
      if (cards[idx]) {
        const inp = cards[idx].querySelector('input[type=text]');
        if (inp) inp.classList.add('is-invalid');
        cards[idx].classList.add('has-error');
      }
    }
    // Error de título
    if (field === 'titulo') {
      document.getElementById('inp-titulo').classList.add('is-invalid');
    }
  });

  // Ir al primer paso con error
  const tieneErrorPaso1 = errors.titulo || errors.modulo || errors.fecha_inicio || errors.fecha_fin;
  const tieneErrorPaso2 = Object.keys(errors).some(k => k.startsWith('preguntas'));
  if (tieneErrorPaso1) {
    irPasoSilencioso(1);
  } else if (tieneErrorPaso2) {
    irPasoSilencioso(2);
  }
}

function irPasoSilencioso(p) {
  document.querySelector('.wizard-step.active').classList.remove('active');
  document.getElementById('step-' + p).classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
  for (let i = 1; i <= 3; i++) {
    const sb = document.getElementById('sb-' + i);
    sb.classList.remove('active', 'done');
    if (i < p) sb.classList.add('done');
    if (i === p) sb.classList.add('active');
  }
}

/* ═══════════════════════════════════════════
   AGREGAR PREGUNTA
═══════════════════════════════════════════ */
function agregarPregunta(tipoInicial = 'opcion_multiple') {
  document.getElementById('sinPreguntas').style.display = 'none';
  const idx = preguntaIdx++;

  const card = document.createElement('div');
  card.className = 'pq-card';
  card.id = 'pq-' + idx;
  card.dataset.idx = idx;

  const pillsHtml = Object.entries(TIPOS).map(([val, cfg]) =>
    `<button type="button" class="tipo-pill ${val === tipoInicial ? 'active' : ''}"
      data-tipo="${val}" onclick="seleccionarTipo(${idx}, '${val}', this)">
      <i class="${cfg.icon}"></i>${cfg.label}
    </button>`
  ).join('');

  card.innerHTML = `
    <input type="hidden" name="preguntas[${idx}][tipo]" id="tipo-val-${idx}" value="${tipoInicial}">
    <div class="pq-header">
      <span class="drag-handle" title="Arrastrar"><i class="ti tabler-grip-vertical"></i></span>
      <span class="pq-num" id="pq-num-${idx}">?</span>
      <div class="pq-texto flex-grow-1">
        <input type="text" name="preguntas[${idx}][texto]"
          class="form-control" placeholder="Escribe tu pregunta aquí..."
          oninput="this.classList.remove('is-invalid'); this.closest('.pq-card').classList.remove('has-error')">
        <div class="invalid-feedback">El texto de la pregunta es obligatorio.</div>
        <div class="pq-tipo-selector mt-2">${pillsHtml}</div>
        <div class="d-flex align-items-center gap-3 mt-2">
          <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="preguntas[${idx}][requerida]"
              value="1" checked id="req-${idx}">
            <label class="form-check-label small text-muted" for="req-${idx}">Obligatoria</label>
          </div>
        </div>
      </div>
      <button type="button" class="btn btn-icon btn-sm btn-outline-danger mt-1"
        onclick="eliminarPregunta(${idx})" title="Eliminar pregunta">
        <i class="ti tabler-trash"></i>
      </button>
    </div>
    <div class="pq-body" id="pq-body-${idx}"></div>`;

  document.getElementById('listaPreguntas').appendChild(card);
  renderCuerpo(idx, tipoInicial);
  actualizarNumeros();

  setTimeout(() => card.querySelector('input[type=text]').focus(), 50);
}

/* ═══════════════════════════════════════════
   SELECCIONAR TIPO (pills)
═══════════════════════════════════════════ */
function seleccionarTipo(idx, tipo, pillBtn) {
  document.getElementById('tipo-val-' + idx).value = tipo;
  pillBtn.closest('.pq-tipo-selector').querySelectorAll('.tipo-pill').forEach(p => p.classList.remove('active'));
  pillBtn.classList.add('active');
  renderCuerpo(idx, tipo);
}

/* ═══════════════════════════════════════════
   RENDER CUERPO SEGÚN TIPO
═══════════════════════════════════════════ */
function renderCuerpo(idx, tipo) {
  const body = document.getElementById('pq-body-' + idx);
  body.innerHTML = '';

  if (tipo === 'opcion_multiple' || tipo === 'seleccion_multiple' || tipo === 'desplegable') {
    const isCheck = tipo === 'seleccion_multiple';
    const wrap = document.createElement('div');
    wrap.className = 'opciones-container';
    wrap.id = 'opciones-' + idx;

    [1, 2].forEach(n => wrap.appendChild(crearFilaOpcion(idx, n, isCheck)));

    const btnAdd = document.createElement('div');
    btnAdd.className = 'mt-2 btn-agregar-wrap';
    btnAdd.innerHTML = `<button type="button" class="btn btn-sm btn-outline-secondary"
      onclick="agregarOpcion(${idx})">
      <i class="ti tabler-plus me-1"></i> Agregar opción</button>`;

    if (tipo === 'desplegable') {
      btnAdd.insertAdjacentHTML('beforeend',
        `<span class="text-muted small ms-2"><i class="ti tabler-info-circle me-1"></i>Se mostrará como lista desplegable al responder.</span>`);
    }

    wrap.appendChild(btnAdd);
    body.appendChild(wrap);

  } else if (tipo === 'escala') {
    body.innerHTML = `
      <div class="preview-bloque">
        <p class="small text-muted mb-2 fw-semibold">Vista previa — el usuario verá esto:</p>
        <div class="d-flex gap-2 escala-preview">
          ${[1,2,3,4,5].map(v => `<span class="d-flex align-items-center justify-content-center">${v}</span>`).join('')}
        </div>
        <div class="d-flex justify-content-between mt-1" style="font-size:.7rem;color:#aaa">
          <span>Muy malo</span><span>Malo</span><span>Regular</span><span>Bueno</span><span>Muy bueno</span>
        </div>
      </div>`;

  } else if (tipo === 'si_no') {
    body.innerHTML = `
      <div class="preview-bloque">
        <p class="small text-muted mb-2 fw-semibold">Vista previa — el usuario verá esto:</p>
        <div class="d-flex gap-3">
          <button type="button" class="si-no-btn si"><i class="ti tabler-check me-1"></i>Sí</button>
          <button type="button" class="si-no-btn no"><i class="ti tabler-x me-1"></i>No</button>
        </div>
      </div>`;

  } else if (tipo === 'verdadero_falso') {
    body.innerHTML = `
      <div class="preview-bloque">
        <p class="small text-muted mb-2 fw-semibold">Vista previa — el usuario verá esto:</p>
        <div class="d-flex gap-3">
          <button type="button" class="vf-btn v"><i class="ti tabler-check me-1"></i>Verdadero</button>
          <button type="button" class="vf-btn f"><i class="ti tabler-x me-1"></i>Falso</button>
        </div>
      </div>`;

  } else if (tipo === 'texto_libre') {
    body.innerHTML = `
      <div class="preview-bloque">
        <p class="small text-muted mb-2 fw-semibold">Vista previa — el usuario verá esto:</p>
        <textarea class="form-control" rows="3" disabled placeholder="El usuario escribirá su respuesta aquí..."></textarea>
      </div>`;
  }
}

/* ═══════════════════════════════════════════
   FILA DE OPCIÓN
═══════════════════════════════════════════ */
function crearFilaOpcion(idx, n, isCheck = false) {
  const row = document.createElement('div');
  row.className = 'opcion-row' + (isCheck ? ' check-style' : '');
  row.innerHTML = `
    <div class="opcion-prefix">${n}</div>
    <input type="text" name="preguntas[${idx}][opciones][]"
      class="form-control" placeholder="Opción ${n}">
    <button type="button" class="btn btn-icon btn-sm btn-outline-danger"
      onclick="this.closest('.opcion-row').remove(); renumerarOpciones(${idx})"
      title="Eliminar opción"><i class="ti tabler-x"></i></button>`;
  return row;
}

function agregarOpcion(idx) {
  const cont = document.querySelector('#opciones-' + idx);
  const btnWrap = cont.querySelector('.btn-agregar-wrap');
  const n = cont.querySelectorAll('.opcion-row').length + 1;
  const tipo = document.getElementById('tipo-val-' + idx).value;
  const row = crearFilaOpcion(idx, n, tipo === 'seleccion_multiple');
  cont.insertBefore(row, btnWrap);
  row.querySelector('input').focus();
}

function renumerarOpciones(idx) {
  document.querySelectorAll('#opciones-' + idx + ' .opcion-row').forEach((row, i) => {
    const prefix = row.querySelector('.opcion-prefix');
    if (prefix) prefix.textContent = i + 1;
    const inp = row.querySelector('input');
    if (inp) inp.placeholder = 'Opción ' + (i + 1);
  });
}

/* ═══════════════════════════════════════════
   ELIMINAR / NUMERAR
═══════════════════════════════════════════ */
function eliminarPregunta(idx) {
  const card = document.getElementById('pq-' + idx);
  card.style.opacity = '0';
  card.style.transform = 'translateY(-8px)';
  card.style.transition = 'all .2s ease';
  setTimeout(() => {
    card.remove();
    actualizarNumeros();
    if (document.querySelectorAll('.pq-card').length === 0) {
      document.getElementById('sinPreguntas').style.display = '';
    }
  }, 200);
}

function actualizarNumeros() {
  const cards = document.querySelectorAll('.pq-card');
  cards.forEach((card, i) => {
    const num = card.querySelector('.pq-num');
    if (num) num.textContent = i + 1;
  });
  const cont = document.getElementById('contPreguntas');
  if (cont) cont.textContent = cards.length + ' pregunta' + (cards.length !== 1 ? 's' : '');
}

/* ═══════════════════════════════════════════
   DESTINATARIOS
═══════════════════════════════════════════ */
function toggleDestTodos(card) {
  const cb = document.getElementById('cbTodos');
  cb.checked = !cb.checked;
  card.classList.toggle('active', cb.checked);
  const sd = document.getElementById('seccionDetallada');
  sd.style.opacity = cb.checked ? '.35' : '1';
  sd.style.pointerEvents = cb.checked ? 'none' : '';
  if (cb.checked) document.getElementById('err-destinatarios').style.display = 'none';
}

/* ═══════════════════════════════════════════
   TOAST
═══════════════════════════════════════════ */
function mostrarToast(msg, tipo = 'info') {
  const colors = { info:'#696cff', warning:'#ffab00', danger:'#ff3e1d', success:'#71dd37' };
  const icons  = { info:'tabler-info-circle', warning:'tabler-alert-triangle', danger:'tabler-alert-circle', success:'tabler-circle-check' };
  const el = document.createElement('div');
  el.style.cssText = `background:${colors[tipo]||colors.info};color:#fff;padding:.75rem 1.25rem;
    border-radius:10px;font-size:.88rem;font-weight:600;min-width:260px;
    box-shadow:0 6px 24px rgba(0,0,0,.18);animation:fadeInUp .25s ease;
    display:flex;align-items:center;gap:.5rem;`;
  el.innerHTML = `<i class="ti ${icons[tipo]||icons.info}" style="font-size:1.1rem;flex-shrink:0"></i><span>${msg}</span>`;
  document.getElementById('toastContainer').appendChild(el);
  setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(),350); }, 4000);
}
</script>
@endsection
