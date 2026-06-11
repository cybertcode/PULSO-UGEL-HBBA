@php $configData = Helper::appClasses(); @endphp
@extends('layouts/layoutMaster')
@section('title', 'Responder: ' . $encuesta->titulo)

@section('page-style')
<style>
/* ── Barra de progreso ── */
.progress-responder { height: 6px; border-radius: 10px; background: #e0dffe; }
.progress-responder .bar { height: 100%; border-radius: 10px; background: linear-gradient(90deg,#696cff,#9b59b6); transition: width .4s ease; }

/* ── Tarjeta de pregunta ── */
.pq-resp-card {
  background: #fff; border: 1.5px solid #e7e7e7; border-radius: 14px;
  padding: 1.5rem 1.6rem; margin-bottom: 1.1rem;
  transition: border-color .2s, box-shadow .2s;
}
.pq-resp-card.respondida { border-color: #71dd37; }
.pq-resp-card.tiene-error { border-color: #ea5455 !important; }
.pq-resp-card:focus-within { box-shadow: 0 4px 18px rgba(105,108,255,.1); }

.pq-num-resp {
  min-width: 30px; height: 30px; border-radius: 50%;
  background: linear-gradient(135deg,#696cff,#9b59b6);
  color: #fff; font-weight: 700; font-size: .82rem;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  transition: background .2s;
}
.pq-resp-card.respondida .pq-num-resp { background: linear-gradient(135deg,#28a745,#20c997); }

/* ── Opciones radio/check ── */
.opcion-label {
  display: flex; align-items: center; gap: .75rem;
  padding: .7rem 1rem; border: 1.5px solid #e7e7e7; border-radius: 10px;
  cursor: pointer; margin-bottom: .45rem; transition: all .15s; user-select: none;
}
.opcion-label:hover { border-color: #696cff; background: #f8f7ff; }
.opcion-label .opcion-icon {
  width: 20px; height: 20px; border: 1.5px solid #c5c4f5; border-radius: 50%;
  flex-shrink: 0; display: flex; align-items: center; justify-content: center;
  transition: all .15s; font-size: .7rem; color: transparent;
}
.opcion-label.check-style .opcion-icon { border-radius: 5px; }
input[type=radio]:checked  ~ .opcion-label,
input[type=checkbox]:checked ~ .opcion-label {
  border-color: #696cff; background: #f0efff;
}
input[type=radio]:checked  ~ .opcion-label .opcion-icon,
input[type=checkbox]:checked ~ .opcion-label .opcion-icon {
  background: #696cff; border-color: #696cff; color: #fff;
}
.opcion-wrap { position: relative; }
.opcion-wrap input { position: absolute; opacity: 0; pointer-events: none; }

/* ── Escala ── */
.escala-group { display: flex; gap: .6rem; flex-wrap: wrap; }
.escala-btn {
  width: 54px; height: 54px; border-radius: 12px;
  border: 2px solid #e0dffe; background: #fff;
  font-size: 1.2rem; font-weight: 700; color: #696cff;
  cursor: pointer; transition: all .15s;
  display: flex; align-items: center; justify-content: center;
}
.escala-btn:hover { border-color: #696cff; background: #f0efff; transform: scale(1.06); }
.escala-btn.active { background: #696cff; border-color: #696cff; color: #fff; transform: scale(1.06); }
.escala-labels { display: flex; justify-content: space-between; font-size: .7rem; color: #aaa; margin-top: .4rem; }

/* ── Sí/No y Verdadero/Falso ── */
.binario-group { display: flex; gap: 1rem; flex-wrap: wrap; }
.binario-btn {
  flex: 1; min-width: 120px; padding: .85rem 1rem; border-radius: 12px;
  border: 2px solid #e0dffe; background: #fff; font-weight: 700; font-size: 1rem;
  cursor: pointer; transition: all .15s; text-align: center;
  display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.binario-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.binario-btn.si  { border-color: #28a745; color: #28a745; background: #f0fff4; }
.binario-btn.no  { border-color: #dc3545; color: #dc3545; background: #fff5f5; }
.binario-btn.vt  { border-color: #17a2b8; color: #17a2b8; background: #f0fbff; }
.binario-btn.vf  { border-color: #fd7e14; color: #fd7e14; background: #fff8f0; }
.binario-btn.active-si  { background: #28a745 !important; color: #fff !important; border-color: #28a745; }
.binario-btn.active-no  { background: #dc3545 !important; color: #fff !important; border-color: #dc3545; }
.binario-btn.active-vt  { background: #17a2b8 !important; color: #fff !important; border-color: #17a2b8; }
.binario-btn.active-vf  { background: #fd7e14 !important; color: #fff !important; border-color: #fd7e14; }

/* ── Desplegable ── */
.select-resp { max-width: 380px; }

/* ── Footer flotante ── */
.footer-responder {
  position: sticky; bottom: 0; background: rgba(255,255,255,.96);
  backdrop-filter: blur(8px); border-top: 1px solid #e7e7e7;
  padding: 1rem 1.5rem; margin: 0 -1.5rem;
  display: flex; justify-content: space-between; align-items: center;
  border-radius: 0 0 14px 14px; z-index: 10;
}
.badge-modulo-sci        { background: #e3f2fd; color: #1565c0; }
.badge-modulo-integridad { background: #e8f5e9; color: #2e7d32; }

/* ── Animación entrada tarjetas ── */
.pq-resp-card { animation: slideIn .25s ease both; }
@keyframes slideIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.pq-resp-card:nth-child(1) { animation-delay: .04s }
.pq-resp-card:nth-child(2) { animation-delay: .08s }
.pq-resp-card:nth-child(3) { animation-delay: .12s }
.pq-resp-card:nth-child(4) { animation-delay: .16s }
.pq-resp-card:nth-child(5) { animation-delay: .20s }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

      {{-- ── Header de encuesta ── --}}
      <div class="card shadow-sm mb-4 border-0">
        <div class="card-body pb-2">
          <div class="d-flex align-items-start gap-3">
            <div class="rounded-3 bg-label-primary p-3 flex-shrink-0">
              <i class="ti tabler-clipboard-list fs-2 text-primary"></i>
            </div>
            <div class="flex-grow-1">
              <h4 class="fw-bold mb-1">{{ $encuesta->titulo }}</h4>
              @if($encuesta->descripcion)
                <p class="text-muted small mb-2">{{ $encuesta->descripcion }}</p>
              @endif
              <div class="d-flex flex-wrap gap-2 small">
                @if($encuesta->fecha_fin)
                  <span class="badge bg-label-warning">
                    <i class="ti tabler-calendar-due me-1"></i>Límite: {{ $encuesta->fecha_fin->format('d/m/Y') }}
                  </span>
                @endif
                <span class="badge bg-label-info">
                  <i class="ti tabler-help-circle me-1"></i>{{ $encuesta->preguntas->count() }} pregunta(s)
                </span>
                <span class="badge @if($encuesta->modulo==='sci') badge-modulo-sci @elseif($encuesta->modulo==='integridad') badge-modulo-integridad @else bg-label-secondary @endif">
                  {{ $encuesta->modulo_label }}
                </span>
              </div>
            </div>
          </div>

          {{-- Barra de progreso --}}
          <div class="mt-3 mb-1">
            <div class="d-flex justify-content-between small text-muted mb-1">
              <span>Progreso</span>
              <span id="txtProgreso">0 / {{ $encuesta->preguntas->count() }}</span>
            </div>
            <div class="progress-responder">
              <div class="bar" id="barraProgreso" style="width:0%"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- ── Errores ── --}}
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
          <i class="ti tabler-alert-circle me-1"></i>
          <strong>Completa las preguntas obligatorias marcadas con *</strong>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- ── Formulario ── --}}
      <form method="POST" action="{{ route('encuestas.responder.store', $encuesta) }}" id="formResponder">
        @csrf

        @php $total = $encuesta->preguntas->count(); @endphp

        @foreach($encuesta->preguntas as $i => $pregunta)
        @php $campo = 'respuesta_'.$pregunta->id; $tieneError = $errors->has($campo); @endphp

        <div class="pq-resp-card {{ $tieneError ? 'tiene-error' : '' }}" id="card-pq-{{ $pregunta->id }}"
          data-pq="{{ $pregunta->id }}" data-tipo="{{ $pregunta->tipo }}">

          <div class="d-flex align-items-start gap-3 mb-3">
            <span class="pq-num-resp" id="num-{{ $pregunta->id }}">{{ $i + 1 }}</span>
            <div class="flex-grow-1">
              <p class="fw-semibold mb-0 fs-6{{ $pregunta->requerida ? ' required-star' : '' }}">
                {{ $pregunta->texto }}
              </p>
              @if($tieneError)
                <span class="text-danger small"><i class="ti tabler-alert-circle me-1"></i>Esta respuesta es requerida</span>
              @endif
            </div>
          </div>

          {{-- ── OPCIÓN MÚLTIPLE (radio) ── --}}
          @if($pregunta->tipo === 'opcion_multiple')
            @foreach($pregunta->opciones as $opcion)
              <div class="opcion-wrap">
                <input type="radio" name="{{ $campo }}" id="op-{{ $pregunta->id }}-{{ $opcion->id }}"
                  value="{{ $opcion->id }}"
                  {{ old($campo) == $opcion->id ? 'checked' : '' }}
                  onchange="marcarRespondida({{ $pregunta->id }})">
                <label class="opcion-label" for="op-{{ $pregunta->id }}-{{ $opcion->id }}">
                  <span class="opcion-icon"><i class="ti tabler-check"></i></span>
                  {{ $opcion->texto }}
                </label>
              </div>
            @endforeach

          {{-- ── SELECCIÓN MÚLTIPLE (checkbox) ── --}}
          @elseif($pregunta->tipo === 'seleccion_multiple')
            @foreach($pregunta->opciones as $opcion)
              <div class="opcion-wrap">
                <input type="checkbox" name="{{ $campo }}[]" id="op-{{ $pregunta->id }}-{{ $opcion->id }}"
                  value="{{ $opcion->id }}"
                  {{ in_array($opcion->id, (array)old($campo, [])) ? 'checked' : '' }}
                  onchange="marcarRespondida({{ $pregunta->id }})">
                <label class="opcion-label check-style" for="op-{{ $pregunta->id }}-{{ $opcion->id }}">
                  <span class="opcion-icon"><i class="ti tabler-check"></i></span>
                  {{ $opcion->texto }}
                </label>
              </div>
            @endforeach

          {{-- ── LISTA DESPLEGABLE ── --}}
          @elseif($pregunta->tipo === 'desplegable')
            <select name="{{ $campo }}" class="form-select select-resp"
              onchange="marcarRespondida({{ $pregunta->id }})">
              <option value="">— Selecciona una opción —</option>
              @foreach($pregunta->opciones as $opcion)
                <option value="{{ $opcion->id }}"
                  {{ old($campo) == $opcion->id ? 'selected' : '' }}>
                  {{ $opcion->texto }}
                </option>
              @endforeach
            </select>

          {{-- ── ESCALA 1-5 ── --}}
          @elseif($pregunta->tipo === 'escala')
            <input type="hidden" name="{{ $campo }}" id="escala-val-{{ $pregunta->id }}"
              value="{{ old($campo) }}">
            <div class="escala-group mb-1">
              @for($v = 1; $v <= 5; $v++)
                <button type="button"
                  class="escala-btn {{ old($campo) == $v ? 'active' : '' }}"
                  onclick="seleccionarEscala({{ $pregunta->id }}, {{ $v }}, this)">{{ $v }}</button>
              @endfor
            </div>
            <div class="escala-labels">
              <span>😞 Muy malo</span><span>😕 Malo</span><span>😐 Regular</span><span>🙂 Bueno</span><span>😄 Muy bueno</span>
            </div>

          {{-- ── SÍ / NO ── --}}
          @elseif($pregunta->tipo === 'si_no')
            <input type="hidden" name="{{ $campo }}" id="sn-val-{{ $pregunta->id }}"
              value="{{ old($campo) }}">
            <div class="binario-group">
              <button type="button" class="binario-btn si {{ old($campo) === 'si' ? 'active-si' : '' }}"
                onclick="seleccionarBinario({{ $pregunta->id }}, 'si', this, 'si_no')">
                <i class="ti tabler-check fs-5"></i> Sí
              </button>
              <button type="button" class="binario-btn no {{ old($campo) === 'no' ? 'active-no' : '' }}"
                onclick="seleccionarBinario({{ $pregunta->id }}, 'no', this, 'si_no')">
                <i class="ti tabler-x fs-5"></i> No
              </button>
            </div>

          {{-- ── VERDADERO / FALSO ── --}}
          @elseif($pregunta->tipo === 'verdadero_falso')
            <input type="hidden" name="{{ $campo }}" id="sn-val-{{ $pregunta->id }}"
              value="{{ old($campo) }}">
            <div class="binario-group">
              <button type="button" class="binario-btn vt {{ old($campo) === 'verdadero' ? 'active-vt' : '' }}"
                onclick="seleccionarBinario({{ $pregunta->id }}, 'verdadero', this, 'verdadero_falso')">
                <i class="ti tabler-circle-check fs-5"></i> Verdadero
              </button>
              <button type="button" class="binario-btn vf {{ old($campo) === 'falso' ? 'active-vf' : '' }}"
                onclick="seleccionarBinario({{ $pregunta->id }}, 'falso', this, 'verdadero_falso')">
                <i class="ti tabler-circle-x fs-5"></i> Falso
              </button>
            </div>

          {{-- ── TEXTO LIBRE ── --}}
          @elseif($pregunta->tipo === 'texto_libre')
            <textarea name="{{ $campo }}" class="form-control" rows="4"
              placeholder="Escribe tu respuesta aquí..."
              oninput="marcarRespondida({{ $pregunta->id }})">{{ old($campo) }}</textarea>
          @endif

        </div>
        @endforeach

        {{-- ── Footer sticky ── --}}
        <div class="footer-responder">
          <a href="{{ route('encuestas.index') }}" class="btn btn-outline-secondary">
            <i class="ti tabler-arrow-left me-1"></i> Cancelar
          </a>
          <div class="d-flex align-items-center gap-3">
            <span class="text-muted small d-none d-sm-block">
              <span id="cntRespondidas">0</span> de {{ $total }} respondidas
            </span>
            <button type="submit" class="btn btn-primary btn-lg px-5" id="btnEnviar">
              <i class="ti tabler-send me-1"></i> Enviar respuesta
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<style>
.required-star::after { content: ' *'; color: #ea5455; }
</style>
<script>
const TOTAL_PQ = {{ $encuesta->preguntas->count() }};
const respondidas = new Set();

/* Marcar pregunta como respondida y actualizar progreso */
function marcarRespondida(pqId) {
  respondidas.add(pqId);
  const card = document.getElementById('card-pq-' + pqId);
  if (card) card.classList.add('respondida');

  const pct = Math.round((respondidas.size / TOTAL_PQ) * 100);
  document.getElementById('barraProgreso').style.width = pct + '%';
  document.getElementById('txtProgreso').textContent = respondidas.size + ' / ' + TOTAL_PQ;
  document.getElementById('cntRespondidas').textContent = respondidas.size;
}

/* Escala */
function seleccionarEscala(pqId, valor, btn) {
  document.getElementById('escala-val-' + pqId).value = valor;
  btn.closest('.escala-group').querySelectorAll('.escala-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  marcarRespondida(pqId);
}

/* Sí/No y Verdadero/Falso */
const ACTIVE_CLASS = { si:'active-si', no:'active-no', verdadero:'active-vt', falso:'active-vf' };
function seleccionarBinario(pqId, valor, btn, tipo) {
  document.getElementById('sn-val-' + pqId).value = valor;
  const group = btn.closest('.binario-group');
  // quitar clases activas
  Object.values(ACTIVE_CLASS).forEach(cls => group.querySelectorAll('.' + cls).forEach(b => b.classList.remove(cls)));
  btn.classList.add(ACTIVE_CLASS[valor] || '');
  marcarRespondida(pqId);
}

/* Marcar preguntas ya respondidas (old values tras error) */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.pq-resp-card').forEach(card => {
    const pqId = card.dataset.pq;
    const tipo = card.dataset.tipo;

    let respondida = false;
    if (['opcion_multiple'].includes(tipo)) {
      respondida = !!card.querySelector('input[type=radio]:checked');
    } else if (['seleccion_multiple'].includes(tipo)) {
      respondida = !!card.querySelector('input[type=checkbox]:checked');
    } else if (['escala','si_no','verdadero_falso'].includes(tipo)) {
      const hid = document.getElementById('escala-val-' + pqId) || document.getElementById('sn-val-' + pqId);
      respondida = hid && hid.value !== '';
    } else if (tipo === 'desplegable') {
      const sel = card.querySelector('select');
      respondida = sel && sel.value !== '';
    } else if (tipo === 'texto_libre') {
      const ta = card.querySelector('textarea');
      respondida = ta && ta.value.trim() !== '';
    }

    if (respondida) marcarRespondida(parseInt(pqId));
  });
});
</script>
@endsection
