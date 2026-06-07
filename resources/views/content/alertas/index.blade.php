@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Alertas - PULSO UGEL')

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Alertas</li>
  </ol>
</nav>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <h4 class="mb-1">Alertas</h4>
    <p class="mb-0 text-muted">Notificaciones automáticas por actividades vencidas, avance bajo o evidencias faltantes.</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    @if($stats['pendientes'] > 0)
    <form method="POST" action="{{ route('mon-alertas.leer-todas') }}">
      @csrf @method('PATCH')
      <button type="submit" class="btn btn-label-secondary btn-sm">
        <i class="ti tabler-checks me-1"></i>Marcar todas como leídas
      </button>
    </form>
    @endif
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaAlerta">
      <i class="ti tabler-plus me-1"></i>Nueva Alerta
    </button>
  </div>
</div>

{{-- Tabla de alertas --}}
<div class="card">

  {{-- Header con tabs y búsqueda --}}
  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 pb-0">
    <ul class="nav nav-tabs card-header-tabs border-0" role="tablist">
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'pendientes' ? 'active fw-semibold' : '' }}"
           href="{{ route('mon-alertas', ['tab' => 'pendientes', 'prioridad' => $prioridad]) }}">
          <i class="ti tabler-bell me-1"></i>Alertas
          @if($stats['pendientes'] > 0)
          <span class="badge bg-danger rounded-pill ms-1">{{ $stats['pendientes'] }}</span>
          @endif
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'resueltas' ? 'active fw-semibold' : '' }}"
           href="{{ route('mon-alertas', ['tab' => 'resueltas', 'prioridad' => $prioridad]) }}">
          Resueltas
          <span class="badge bg-label-secondary rounded-pill ms-1">{{ $stats['resueltas'] }}</span>
        </a>
      </li>
    </ul>
    <div class="d-flex gap-1 pb-2 flex-wrap">
      @foreach([''=>'Todas','alta'=>'Alta','media'=>'Media','baja'=>'Baja'] as $val => $label)
      <a href="{{ route('mon-alertas', ['tab'=>$tab,'prioridad'=>$val ?: null,'tipo'=>$tipo]) }}"
         class="btn btn-xs {{ $prioridad == $val ? 'btn-danger' : 'btn-label-secondary' }}">{{ $label }}</a>
      @endforeach
      <span class="ms-2"></span>
      @foreach([''=>'Tipo','vencimiento'=>'Vencimiento','avance_bajo'=>'Avance','evidencia_falta'=>'Evidencia','sistema'=>'Sistema'] as $val => $label)
      <a href="{{ route('mon-alertas', ['tab'=>$tab,'prioridad'=>$prioridad,'tipo'=>$val ?: null]) }}"
         class="btn btn-xs {{ ($tipo ?? '') == $val ? 'btn-primary' : 'btn-label-secondary' }}">{{ $label }}</a>
      @endforeach
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Alerta</th>
            <th class="text-center" style="min-width:110px">Días restantes</th>
            <th>Vencimiento</th>
            <th>Responsable</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($alertas as $alerta)
          @php
            $ic  = match($alerta->prioridad) { 'alta' => 'danger', 'media' => 'warning', default => 'info' };
            $ico = match($alerta->tipo) {
              'vencimiento'     => 'tabler-calendar-x',
              'avance_bajo'     => 'tabler-trending-down',
              'evidencia_falta' => 'tabler-file-off',
              default           => 'tabler-bell',
            };
            $diasRestantes = null;
            if ($alerta->actividad?->fecha_limite) {
              $diasRestantes = (int) now()->diffInDays($alerta->actividad->fecha_limite, false);
            }
            $dc = $diasRestantes !== null ? ($diasRestantes < 0 ? 'danger' : ($diasRestantes <= 5 ? 'warning' : 'success')) : 'secondary';
          @endphp
          <tr class="{{ $alerta->leida ? 'opacity-60' : '' }}">
            <td>
              <div class="d-flex align-items-center gap-3">
                {{-- Icono de prioridad con color --}}
                <div style="width:10px;height:10px;border-radius:50%;background:{{ $ic === 'danger' ? '#ea5455' : ($ic === 'warning' ? '#ff9f43' : '#00cfe8') }};flex-shrink:0"></div>
                <div>
                  <div class="fw-medium" style="font-size:14px">{{ $alerta->titulo }}</div>
                  @if($alerta->actividad)
                  <small class="text-muted">{{ Str::limit($alerta->actividad->nombre, 60) }}</small>
                  @endif
                </div>
              </div>
            </td>
            <td class="text-center">
              @if($diasRestantes !== null)
              <span class="badge bg-{{ $dc }} px-3 py-2 rounded-pill" style="font-size:13px;font-weight:700">
                @if($diasRestantes < 0)
                  Vencida
                @elseif($diasRestantes == 0)
                  Hoy
                @else
                  {{ $diasRestantes }} <i class="ti tabler-arrow-up icon-12px"></i>
                @endif
              </span>
              @else
              <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              @if($alerta->actividad?->fecha_limite)
              <small class="fw-medium">{{ $alerta->actividad->fecha_limite->format('d. M. Y') }}</small>
              @else
              <small class="text-muted">—</small>
              @endif
            </td>
            <td>
              @if($alerta->actividad?->responsable)
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  @if($alerta->actividad->responsable->profile_photo_path)
                  <img src="{{ Storage::url($alerta->actividad->responsable->profile_photo_path) }}" alt="{{ $alerta->actividad->responsable->name }}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover">
                  @else
                  <span class="avatar-initial rounded-circle bg-label-primary" style="width:36px;height:36px;font-size:13px">
                    {{ strtoupper(substr($alerta->actividad->responsable->name, 0, 2)) }}
                  </span>
                  @endif
                </div>
                <small class="fw-medium d-none d-md-inline" style="max-width:120px">{{ $alerta->actividad->responsable->name }}</small>
              </div>
              @else
              <span class="text-muted small">—</span>
              @endif
            </td>
            <td>
              @if($tab === 'pendientes' && !$alerta->leida)
              <form method="POST" action="{{ route('mon-alertas.leer', $alerta) }}" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm btn-primary">
                  <i class="ti tabler-external-link icon-14px me-1"></i>Ver alerta
                </button>
              </form>
              @else
              <span class="badge bg-label-success"><i class="ti tabler-check icon-12px me-1"></i>Resuelta</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center py-6">
              <i class="ti tabler-bell-off icon-48px d-block mb-3 text-success"></i>
              <h6 class="text-success mb-1">
                @if($tab === 'pendientes') ¡Sin alertas pendientes! @else Sin alertas resueltas @endif
              </h6>
              <p class="text-muted mb-0">
                @if($tab === 'pendientes') Todas las actividades están al día. @else Aún no se han resuelto alertas. @endif
              </p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($alertas->hasPages())
  <div class="card-footer d-flex align-items-center justify-content-between">
    <small class="text-muted">{{ $alertas->total() }} alertas</small>
    {{ $alertas->links() }}
  </div>
  @endif

</div>

{{-- Modal Nueva Alerta --}}
<div class="modal fade" id="modalNuevaAlerta" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('mon-alertas.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="ti tabler-bell-plus me-2 text-warning"></i>Nueva Alerta Manual</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required placeholder="Resumen de la alerta">
            </div>
            <div class="col-md-6">
              <label class="form-label">Tipo</label>
              <select name="tipo" class="form-select">
                <option value="sistema">Sistema</option>
                <option value="vencimiento">Vencimiento</option>
                <option value="avance_bajo">Avance Bajo</option>
                <option value="evidencia_falta">Evidencia Faltante</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Prioridad</label>
              <select name="prioridad" class="form-select">
                <option value="alta">Alta</option>
                <option value="media" selected>Media</option>
                <option value="baja">Baja</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Mensaje <span class="text-danger">*</span></label>
              <textarea name="mensaje" class="form-control" rows="3" required placeholder="Descripción detallada de la alerta..."></textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="enviar_email" value="1" id="chkEmail">
                <label class="form-check-label" for="chkEmail">Enviar notificación por correo electrónico</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="ti tabler-send me-1"></i>Crear Alerta</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
