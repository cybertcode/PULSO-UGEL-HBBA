@php
use Illuminate\Support\Str;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Mis Actividades — PULSO UGEL')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss',
       'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js',
       'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ti tabler-home icon-14px me-1"></i>Inicio</a></li>
    <li class="breadcrumb-item active">Mis Actividades</li>
  </ol>
</nav>

{{-- Header personalizado --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div class="d-flex align-items-center gap-3">
    <div class="avatar avatar-lg">
      @if($user->profile_photo_path)
        <img src="{{ Storage::url($user->profile_photo_path) }}" class="rounded-circle" alt="">
      @else
        <div class="avatar-initial rounded-circle bg-label-primary" style="font-size:1.2rem">
          {{ strtoupper(substr($user->name,0,1)) }}
        </div>
      @endif
    </div>
    <div>
      <h4 class="mb-0">Mis Actividades</h4>
      <p class="mb-0 text-muted">{{ $user->name }} · {{ $user->cargo ?? 'Sin cargo' }} · {{ $user->unidadOrganica?->sigla ?? '—' }}</p>
    </div>
  </div>
  @if($proximas->count() > 0)
  <div class="alert alert-warning mb-0 py-2 px-3 d-flex align-items-center gap-2">
    <i class="ti tabler-calendar-exclamation"></i>
    <span><strong>{{ $proximas->count() }}</strong> actividad(es) vencen en los próximos 15 días</span>
  </div>
  @endif
</div>

{{-- KPIs --}}
<div class="row g-4 mb-4">
  <div class="col-6 col-md">
    <div class="card h-100">
      <div class="card-body text-center py-3">
        <h3 class="mb-1 text-primary">{{ $stats['total'] }}</h3>
        <p class="mb-0 fw-semibold small">Total asignadas</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md">
    <div class="card h-100">
      <div class="card-body text-center py-3">
        <h3 class="mb-1 text-success">{{ $stats['completadas'] }}</h3>
        <p class="mb-0 fw-semibold small">Completadas</p>
        <small class="text-muted">{{ $stats['porcentaje'] }}%</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md">
    <div class="card h-100">
      <div class="card-body text-center py-3">
        <h3 class="mb-1 text-warning">{{ $stats['en_proceso'] }}</h3>
        <p class="mb-0 fw-semibold small">En proceso</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md">
    <div class="card h-100 {{ $stats['vencidas'] > 0 ? 'border-danger border-opacity-25' : '' }}">
      <div class="card-body text-center py-3">
        <h3 class="mb-1 text-danger">{{ $stats['vencidas'] }}</h3>
        <p class="mb-0 fw-semibold small">Vencidas</p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md">
    <div class="card h-100 {{ $stats['sin_ev'] > 0 ? 'border-warning border-opacity-25' : '' }}">
      <div class="card-body text-center py-3">
        <h3 class="mb-1 text-warning">{{ $stats['sin_ev'] }}</h3>
        <p class="mb-0 fw-semibold small">Sin evidencia</p>
      </div>
    </div>
  </div>
</div>

{{-- Próximas a vencer --}}
@if($proximas->count() > 0)
<div class="card mb-4 border-warning border-opacity-50">
  <div class="card-header bg-label-warning py-2">
    <h6 class="mb-0"><i class="ti tabler-calendar-exclamation me-2"></i>Próximas a vencer (15 días)</h6>
  </div>
  <div class="card-body p-0">
    <div class="d-flex flex-wrap gap-0">
      @foreach($proximas as $prox)
      @php $dias = now()->diffInDays($prox->fecha_limite, false); @endphp
      <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom border-end flex-fill" style="min-width:260px">
        <div class="badge {{ $dias <= 3 ? 'bg-danger' : 'bg-warning' }} rounded-pill fs-6 px-3">{{ $dias }}d</div>
        <div>
          <div class="fw-medium">{{ Str::limit($prox->nombre, 45) }}</div>
          <small class="text-muted">{{ $prox->componente?->nombre }} · Vence {{ $prox->fecha_limite->format('d/m/Y') }}</small>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- Filtros --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('mis-actividades') }}">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label form-label-sm">Estado</label>
          <select name="estado" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
            <option value="en_proceso" {{ request('estado') === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
            <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completada</option>
            <option value="vencida"    {{ request('estado') === 'vencida'    ? 'selected' : '' }}>Vencida</option>
            <option value="observado"  {{ request('estado') === 'observado'  ? 'selected' : '' }}>Observado</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label form-label-sm">Componente</label>
          <select name="componente_id" class="form-select form-select-sm select2">
            <option value="">Todos</option>
            @foreach($componentes as $c)
            <option value="{{ $c->id }}" {{ request('componente_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label form-label-sm">Prioridad</label>
          <select name="prioridad" class="form-select form-select-sm">
            <option value="">Todas</option>
            <option value="alta"  {{ request('prioridad') === 'alta'  ? 'selected' : '' }}>Alta</option>
            <option value="media" {{ request('prioridad') === 'media' ? 'selected' : '' }}>Media</option>
            <option value="baja"  {{ request('prioridad') === 'baja'  ? 'selected' : '' }}>Baja</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label form-label-sm">Buscar</label>
          <input type="text" name="buscar" class="form-control form-control-sm" value="{{ request('buscar') }}" placeholder="Nombre o código">
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="ti tabler-filter me-1"></i>Filtrar</button>
          <a href="{{ route('mis-actividades') }}" class="btn btn-sm btn-label-secondary"><i class="ti tabler-x"></i></a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Lista de actividades --}}
<div class="row g-4">
  @forelse($actividades as $act)
  @php
    $ec = match($act->estado) {
      'completada' => 'success',
      'vencida'    => 'danger',
      'observado'  => 'info',
      'en_proceso' => 'warning',
      default      => 'secondary',
    };
    $pc = match($act->prioridad) { 'alta' => 'danger', 'media' => 'warning', default => 'secondary' };
    $miRol = $act->responsables->where('id', $user->id)->first()?->pivot->tipo ?? 'principal';
    $tieneEvidencias = $act->evidencias->count() > 0;
    $diasRestantes = $act->fecha_limite ? now()->diffInDays($act->fecha_limite, false) : null;
  @endphp
  <div class="col-md-6 col-xl-4">
    <div class="card h-100 {{ $act->estado === 'vencida' ? 'border-danger border-opacity-50' : '' }}">
      <div class="card-header pb-2 pt-3 px-4">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <span class="badge bg-label-{{ $ec }}">{{ $act->estado_label }}</span>
          <div class="d-flex gap-1">
            <span class="badge bg-label-{{ $pc }}">{{ ucfirst($act->prioridad) }}</span>
            <span class="badge bg-label-secondary text-capitalize">{{ $miRol }}</span>
          </div>
        </div>
      </div>
      <div class="card-body px-4 pb-3">
        <h6 class="mb-1" title="{{ $act->nombre }}">{{ Str::limit($act->nombre, 60) }}</h6>
        <p class="text-muted small mb-3">{{ $act->componente?->nombre ?? '—' }} · {{ $act->codigo }}</p>

        {{-- Barra de avance --}}
        <div class="d-flex align-items-center gap-2 mb-2">
          <div class="progress flex-grow-1" style="height:10px">
            <div class="progress-bar bg-{{ $ec }}" style="width:{{ $act->avance }}%" role="progressbar"></div>
          </div>
          <span class="fw-bold text-{{ $ec }}" style="min-width:38px;text-align:right">{{ $act->avance }}%</span>
        </div>

        {{-- Info fechas --}}
        <div class="d-flex justify-content-between align-items-center mb-3 small text-muted">
          <span><i class="ti tabler-calendar me-1"></i>
            @if($act->fecha_limite)
              Vence {{ $act->fecha_limite->format('d/m/Y') }}
              @if($diasRestantes !== null && $act->estado !== 'completada')
                <span class="ms-1 badge {{ $diasRestantes < 0 ? 'bg-danger' : ($diasRestantes <= 7 ? 'bg-warning text-dark' : 'bg-label-secondary') }}">
                  {{ $diasRestantes < 0 ? abs($diasRestantes).'d tarde' : $diasRestantes.'d' }}
                </span>
              @endif
            @else
              Sin fecha límite
            @endif
          </span>
          <span>
            @if($tieneEvidencias)
              <i class="ti tabler-file-check text-success me-1"></i>{{ $act->evidencias->count() }} ev.
            @elseif(!in_array($act->estado, ['pendiente']))
              <i class="ti tabler-file-off text-warning me-1"></i><span class="text-warning">Sin evidencia</span>
            @endif
          </span>
        </div>

        {{-- Acciones --}}
        <div class="d-flex gap-2 flex-wrap">
          @if(!in_array($act->estado, ['completada', 'vencida']))
          <button class="btn btn-sm btn-primary flex-fill btn-actualizar-avance"
            data-id="{{ $act->id }}"
            data-avance="{{ $act->avance }}"
            data-nombre="{{ Str::limit($act->nombre, 50) }}"
            data-url="{{ route('mis-actividades.avance', $act) }}">
            <i class="ti tabler-pencil me-1"></i>Actualizar avance
          </button>
          @endif
          <a href="{{ route('sci-evidencias', ['actividad_id' => $act->id]) }}"
             class="btn btn-sm btn-label-{{ $tieneEvidencias ? 'success' : 'warning' }}"
             title="{{ $tieneEvidencias ? 'Ver/subir evidencias' : 'Subir evidencia' }}">
            <i class="ti {{ $tieneEvidencias ? 'tabler-file-check' : 'tabler-upload' }}"></i>
          </a>
          <button class="btn btn-sm btn-label-secondary btn-ver-historial"
            data-id="{{ $act->id }}"
            data-nombre="{{ Str::limit($act->nombre, 50) }}"
            data-url="{{ route('mis-actividades.historial', $act) }}"
            title="Ver historial">
            <i class="ti tabler-history"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="card">
      <div class="card-body text-center py-5">
        <i class="ti tabler-clipboard-off icon-48px d-block mb-3 text-muted"></i>
        <h5 class="text-muted">No tienes actividades asignadas</h5>
        <p class="text-muted mb-0">Cuando el Coordinador SCI te asigne una actividad, aparecerá aquí.</p>
      </div>
    </div>
  </div>
  @endforelse
</div>

@if($actividades->hasPages())
<div class="mt-4">{{ $actividades->links() }}</div>
@endif

{{-- Modal actualizar avance --}}
<div class="modal fade" id="modalAvance" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <form id="formAvance">
        <div class="modal-header">
          <h6 class="modal-title"><i class="ti tabler-pencil me-2"></i>Actualizar Avance</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small mb-3" id="avanceNombre"></p>
          <div class="mb-3">
            <label class="form-label">Porcentaje de avance: <strong id="avanceValorLabel">0%</strong></label>
            <input type="range" class="form-range" name="avance" id="avanceRange" min="0" max="100" step="5" value="0">
          </div>
          <div class="mb-3">
            <label class="form-label form-label-sm">Observación (opcional)</label>
            <textarea name="observaciones" class="form-control form-control-sm" rows="2" placeholder="Describe el avance realizado..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary"><i class="ti tabler-check me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal historial --}}
<div class="modal fade" id="modalHistorial" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title"><i class="ti tabler-history me-2"></i>Historial: <span id="historialNombre"></span></h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0" id="historialContenido">
        <div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.select2').forEach(el => $(el).select2({ width: '100%' }));

  // Actualizar avance
  const modalAvance  = new bootstrap.Modal(document.getElementById('modalAvance'));
  const formAvance   = document.getElementById('formAvance');
  const avanceRange  = document.getElementById('avanceRange');
  const avanceLabel  = document.getElementById('avanceValorLabel');
  let   avanceUrl    = '';

  avanceRange.addEventListener('input', () => avanceLabel.textContent = avanceRange.value + '%');

  document.querySelectorAll('.btn-actualizar-avance').forEach(btn => {
    btn.addEventListener('click', function () {
      avanceUrl = this.dataset.url;
      document.getElementById('avanceNombre').textContent = this.dataset.nombre;
      avanceRange.value = this.dataset.avance;
      avanceLabel.textContent = this.dataset.avance + '%';
      modalAvance.show();
    });
  });

  formAvance.addEventListener('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch(avanceUrl, {
      method: 'PATCH',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
      body: fd,
    }).then(r => r.json()).then(data => {
      if (data.success) {
        modalAvance.hide();
        Swal.fire({ icon: 'success', title: 'Avance actualizado', text: `Avance: ${data.avance}% — ${data.estado_label}`, timer: 2000, showConfirmButton: false })
          .then(() => location.reload());
      }
    }).catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar el avance.' }));
  });

  // Historial
  const modalHistorial = new bootstrap.Modal(document.getElementById('modalHistorial'));

  document.querySelectorAll('.btn-ver-historial').forEach(btn => {
    btn.addEventListener('click', function () {
      document.getElementById('historialNombre').textContent = this.dataset.nombre;
      document.getElementById('historialContenido').innerHTML =
        '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
      modalHistorial.show();

      fetch(this.dataset.url, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
          if (!data.length) {
            document.getElementById('historialContenido').innerHTML =
              '<p class="text-center text-muted py-4">Sin historial registrado.</p>';
            return;
          }
          let html = '<table class="table table-sm mb-0"><thead class="table-light"><tr><th>Campo</th><th>Antes</th><th>Después</th><th>Usuario</th><th>Fecha</th></tr></thead><tbody>';
          data.forEach(h => {
            html += `<tr>
              <td><span class="badge bg-label-secondary">${h.campo}</span></td>
              <td class="text-muted">${h.valor_anterior ?? '—'}</td>
              <td class="fw-medium">${h.valor_nuevo ?? '—'}</td>
              <td><small>${h.usuario}</small></td>
              <td><small class="text-muted">${h.fecha}</small></td>
            </tr>`;
          });
          html += '</tbody></table>';
          document.getElementById('historialContenido').innerHTML = html;
        });
    });
  });
});
</script>
@endsection
