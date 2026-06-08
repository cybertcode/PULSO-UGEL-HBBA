@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Alertas — PULSO UGEL')

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Alertas</li>
  </ol>
</nav>

{{-- ══════════════════════════════════════════════
     CABECERA DE PÁGINA
══════════════════════════════════════════════ --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <h4 class="mb-1 fw-bold">Alertas</h4>
    <p class="text-muted mb-0">Notificaciones automáticas por actividades vencidas, avance bajo o evidencias faltantes.</p>
  </div>
  <div class="d-flex gap-2">
    @if($stats['pendientes'] > 0)
    <form method="POST" action="{{ route('mon-alertas.leer-todas') }}">
      @csrf @method('PATCH')
      <button type="submit" class="btn btn-label-success btn-sm">
        <i class="ti tabler-checks me-1"></i>Marcar todas leídas
      </button>
    </form>
    @endif
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaAlerta">
      <i class="ti tabler-plus me-1"></i>Nueva alerta
    </button>
  </div>
</div>

{{-- ══════════════════════════════════════════════
     TARJETAS DE ESTADÍSTICAS
══════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
  {{-- Pendientes --}}
  <div class="col-6 col-md-3">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.5px;font-size:10px">Pendientes</span>
          <span class="avatar avatar-sm bg-label-danger rounded">
            <span class="avatar-initial rounded bg-label-danger text-danger"><i class="ti tabler-bell"></i></span>
          </span>
        </div>
        <div class="d-flex align-items-end gap-2">
          <h3 class="mb-0 fw-bold text-danger">{{ $stats['pendientes'] }}</h3>
          @if($stats['pendientes'] > 0)
          <span class="badge bg-danger mb-1" style="font-size:9px">Requieren atención</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Alta prioridad --}}
  <div class="col-6 col-md-3">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.5px;font-size:10px">Alta prioridad</span>
          <span class="avatar avatar-sm bg-label-warning rounded">
            <span class="avatar-initial rounded bg-label-warning text-warning"><i class="ti tabler-alert-triangle"></i></span>
          </span>
        </div>
        <div class="d-flex align-items-end gap-2">
          <h3 class="mb-0 fw-bold text-warning">{{ $stats['alta'] }}</h3>
          <span class="text-muted small mb-1">de {{ $stats['pendientes'] }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Media prioridad --}}
  <div class="col-6 col-md-3">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.5px;font-size:10px">Media prioridad</span>
          <span class="avatar avatar-sm bg-label-info rounded">
            <span class="avatar-initial rounded bg-label-info text-info"><i class="ti tabler-info-circle"></i></span>
          </span>
        </div>
        <div class="d-flex align-items-end gap-2">
          <h3 class="mb-0 fw-bold text-info">{{ $stats['media'] }}</h3>
          <span class="text-muted small mb-1">alertas</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Resueltas --}}
  <div class="col-6 col-md-3">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.5px;font-size:10px">Resueltas</span>
          <span class="avatar avatar-sm bg-label-success rounded">
            <span class="avatar-initial rounded bg-label-success text-success"><i class="ti tabler-circle-check"></i></span>
          </span>
        </div>
        <div class="d-flex align-items-end gap-2">
          <h3 class="mb-0 fw-bold text-success">{{ $stats['resueltas'] }}</h3>
          <span class="text-muted small mb-1">atendidas</span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════
     TABLA PRINCIPAL
══════════════════════════════════════════════ --}}
<div class="card border-0 shadow-sm">

  {{-- Tabs + Filtros --}}
  <div class="card-header border-bottom-0 bg-transparent pb-0 pt-3 px-4">

    {{-- Tabs exactos al prototipo --}}
    <ul class="nav nav-tabs border-0 mb-3">
      <li class="nav-item">
        <a class="nav-link px-3 d-flex align-items-center gap-2 {{ $tab === 'pendientes' ? 'active fw-semibold' : 'text-muted' }}"
           href="{{ route('mon-alertas', ['tab'=>'pendientes','prioridad'=>$prioridad,'tipo'=>$tipo]) }}">
          <i class="ti tabler-bell icon-16px"></i>
          Alertas
          <span class="badge {{ $tab === 'pendientes' ? 'bg-danger' : 'bg-label-secondary' }} rounded-pill" style="font-size:10px">
            {{ $stats['pendientes'] }}<i class="ti tabler-arrow-up ms-1" style="font-size:8px"></i>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-3 d-flex align-items-center gap-2 {{ $tab === 'resueltas' ? 'active fw-semibold' : 'text-muted' }}"
           href="{{ route('mon-alertas', ['tab'=>'resueltas','prioridad'=>$prioridad,'tipo'=>$tipo]) }}">
          <i class="ti tabler-circle-check icon-16px"></i>
          Resueltas
          <span class="badge bg-label-success rounded-pill" style="font-size:10px">{{ $stats['resueltas'] }}</span>
        </a>
      </li>
    </ul>

    {{-- Filtros en fila separada y visualmente clara --}}
    <div class="d-flex align-items-center gap-3 flex-wrap pb-3">
      {{-- Prioridad --}}
      <div class="d-flex align-items-center gap-1">
        <span class="text-muted small me-1" style="white-space:nowrap;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Prioridad</span>
        @foreach([''=>'Todas','alta'=>'Alta','media'=>'Media','baja'=>'Baja'] as $val => $label)
        @php
          $isActive = ($prioridad ?? '') === $val;
          $color = match($val) { 'alta'=>'danger','media'=>'warning','baja'=>'info', default=>'secondary' };
        @endphp
        <a href="{{ route('mon-alertas', ['tab'=>$tab,'prioridad'=>$val ?: null,'tipo'=>$tipo]) }}"
           class="badge rounded-pill text-decoration-none px-2 py-1 {{ $isActive ? 'bg-'.$color : 'bg-label-secondary text-secondary' }}"
           style="font-size:11px;font-weight:500">{{ $label }}</a>
        @endforeach
      </div>

      <div class="vr opacity-25 d-none d-md-block"></div>

      {{-- Tipo --}}
      <div class="d-flex align-items-center gap-1 flex-wrap">
        <span class="text-muted small me-1" style="white-space:nowrap;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Tipo</span>
        @foreach([
          ''=>['label'=>'Todos','icon'=>'tabler-list'],
          'vencimiento'=>['label'=>'Vencimiento','icon'=>'tabler-calendar-x'],
          'avance_bajo'=>['label'=>'Avance bajo','icon'=>'tabler-trending-down'],
          'evidencia_falta'=>['label'=>'Sin evidencia','icon'=>'tabler-file-off'],
          'sistema'=>['label'=>'Sistema','icon'=>'tabler-bell'],
        ] as $val => $meta)
        @php $isActive = ($tipo ?? '') === $val; @endphp
        <a href="{{ route('mon-alertas', ['tab'=>$tab,'prioridad'=>$prioridad,'tipo'=>$val ?: null]) }}"
           class="badge rounded-pill text-decoration-none px-2 py-1 {{ $isActive ? 'bg-primary' : 'bg-label-secondary text-secondary' }}"
           style="font-size:11px;font-weight:500">
          <i class="ti {{ $meta['icon'] }} me-1" style="font-size:10px"></i>{{ $meta['label'] }}
        </a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="table-responsive">
    <table class="table align-middle mb-0" style="border-collapse:separate">
      <thead>
        <tr style="background:var(--bs-tertiary-bg)">
          <th class="px-4 py-3 fw-semibold" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Alerta</th>
          <th class="py-3 fw-semibold text-center" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;width:130px">Días restantes <i class="ti tabler-x icon-10px"></i></th>
          <th class="py-3 fw-semibold" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;width:130px">Vencimiento</th>
          <th class="py-3 fw-semibold" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;width:190px">Responsable</th>
          <th class="py-3 fw-semibold text-end px-4" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;width:130px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($alertas as $alerta)
        @php
          $prioColor = match($alerta->prioridad) {
            'alta'  => ['hex'=>'#ea5455','label'=>'danger','text'=>'Alta'],
            'media' => ['hex'=>'#ff9f43','label'=>'warning','text'=>'Media'],
            default => ['hex'=>'#00cfe8','label'=>'info','text'=>'Baja'],
          };
          $tipoIcon = match($alerta->tipo) {
            'vencimiento'     => 'tabler-calendar-x',
            'avance_bajo'     => 'tabler-trending-down',
            'evidencia_falta' => 'tabler-file-off',
            default           => 'tabler-bell',
          };
          $tipoLabel = match($alerta->tipo) {
            'vencimiento'     => 'Vencimiento',
            'avance_bajo'     => 'Avance bajo',
            'evidencia_falta' => 'Sin evidencia',
            default           => 'Sistema',
          };
          $diasRestantes = null;
          if ($alerta->actividad?->fecha_limite) {
            $diasRestantes = (int) now()->diffInDays($alerta->actividad->fecha_limite, false);
          }
          $dc = $diasRestantes !== null
            ? ($diasRestantes < 0 ? 'danger' : ($diasRestantes <= 7 ? 'warning' : 'success'))
            : 'secondary';
          $respAlerta = $alerta->actividad?->responsables->first();
          $isLeida = (bool) $alerta->leida;
        @endphp
        <tr class="{{ $isLeida ? 'opacity-50' : '' }}"
            style="border-bottom:1px solid #f0f0f0;transition:background .15s">
          {{-- Alerta --}}
          <td class="px-4 py-3">
            <div class="d-flex align-items-center gap-3">
              {{-- Barra de prioridad --}}
              <span class="flex-shrink-0" style="width:4px;height:40px;border-radius:4px;background:{{ $prioColor['hex'] }};display:block"></span>
              {{-- Icono de tipo --}}
              <div class="avatar flex-shrink-0 bg-label-{{ $prioColor['label'] }} rounded">
                <span class="avatar-initial rounded bg-label-{{ $prioColor['label'] }}" style="color:{{ $prioColor['hex'] }}">
                  <i class="ti {{ $tipoIcon }}"></i>
                </span>
              </div>
              <div class="overflow-hidden">
                <div class="fw-semibold text-heading mb-1 lh-sm" style="font-size:13.5px">
                  {{ $alerta->titulo }}
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <span class="badge bg-label-{{ $prioColor['label'] }}" style="font-size:10px">
                    {{ $prioColor['text'] }}
                  </span>
                  <span class="badge bg-label-secondary text-secondary" style="font-size:10px">
                    <i class="ti {{ $tipoIcon }} me-1" style="font-size:9px"></i>{{ $tipoLabel }}
                  </span>
                  @if($alerta->actividad)
                  <span class="text-muted" style="font-size:11px">
                    <i class="ti tabler-link me-1" style="font-size:10px"></i>{{ Str::limit($alerta->actividad->nombre, 50) }}
                  </span>
                  @endif
                </div>
              </div>
            </div>
          </td>

          {{-- Estado / días restantes --}}
          <td class="text-center py-3">
            @if($diasRestantes !== null)
              @if($diasRestantes < 0)
              <span class="badge bg-danger px-2 py-1 rounded-pill" style="font-size:11px">
                <i class="ti tabler-clock-x me-1" style="font-size:10px"></i>Vencida
              </span>
              @elseif($diasRestantes === 0)
              <span class="badge bg-warning px-2 py-1 rounded-pill" style="font-size:11px">
                <i class="ti tabler-clock me-1" style="font-size:10px"></i>Hoy
              </span>
              @else
              <span class="badge bg-label-{{ $dc }} px-2 py-1 rounded-pill" style="font-size:11px;color:var(--bs-{{ $dc }})">
                {{ $diasRestantes }}d restantes
              </span>
              @endif
            @else
            <span class="text-muted" style="font-size:12px">—</span>
            @endif
          </td>

          {{-- Fecha vencimiento --}}
          <td class="py-3">
            @if($alerta->actividad?->fecha_limite)
            <div style="font-size:13px" class="fw-medium">
              {{ $alerta->actividad->fecha_limite->format('d M Y') }}
            </div>
            <div class="text-muted" style="font-size:11px">
              {{ $alerta->actividad->fecha_limite->diffForHumans() }}
            </div>
            @else
            <span class="text-muted" style="font-size:12px">Sin fecha</span>
            @endif
          </td>

          {{-- Responsable --}}
          <td class="py-3">
            @if($respAlerta)
            <div class="d-flex align-items-center gap-2">
              <div class="flex-shrink-0">
                @if($respAlerta->profile_photo_path)
                <img src="{{ Storage::url($respAlerta->profile_photo_path) }}"
                     class="rounded-circle" style="width:32px;height:32px;object-fit:cover;border:2px solid #eee"
                     alt="{{ $respAlerta->name }}">
                @else
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                     style="width:32px;height:32px;background:var(--bs-primary);color:#fff;font-size:11px;opacity:.85">
                  {{ strtoupper(substr($respAlerta->name, 0, 2)) }}
                </div>
                @endif
              </div>
              <div class="overflow-hidden">
                <div class="fw-medium text-truncate" style="font-size:12px;max-width:130px">{{ $respAlerta->name }}</div>
                @if($respAlerta->unidadOrganica)
                <div class="text-muted text-truncate" style="font-size:10px;max-width:130px">{{ $respAlerta->unidadOrganica->sigla ?? $respAlerta->unidadOrganica->nombre }}</div>
                @endif
              </div>
            </div>
            @else
            <span class="text-muted" style="font-size:12px">—</span>
            @endif
          </td>

          {{-- Acciones --}}
          <td class="text-end px-4 py-3">
            <div class="d-flex align-items-center justify-content-end gap-2">
              @if(!$isLeida)
              <form method="POST" action="{{ route('mon-alertas.leer', $alerta) }}" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3" style="font-size:12px">
                  <i class="ti tabler-eye me-1" style="font-size:11px"></i>Ver alerta
                </button>
              </form>
              @else
              <span class="badge bg-label-success px-2 py-1 rounded-pill" style="font-size:11px">
                <i class="ti tabler-circle-check me-1" style="font-size:10px"></i>Resuelta
              </span>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-6">
            <div class="py-4">
              <div class="avatar avatar-xl bg-label-success rounded-circle mx-auto mb-3">
                <span class="avatar-initial rounded-circle bg-label-success text-success" style="font-size:28px"><i class="ti tabler-bell-off"></i></span>
              </div>
              <h6 class="fw-semibold mb-1 text-success">
                @if($tab === 'pendientes') ¡Sin alertas activas! @else Sin alertas resueltas @endif
              </h6>
              <p class="text-muted mb-0 small">
                @if($tab === 'pendientes') Todas las actividades están al día. @else No se han resuelto alertas aún. @endif
              </p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Footer con paginación --}}
  @if($alertas->hasPages())
  <div class="card-footer bg-transparent border-top d-flex align-items-center justify-content-between px-4 py-3">
    <span class="text-muted small">
      Mostrando <strong>{{ $alertas->firstItem() }}–{{ $alertas->lastItem() }}</strong> de <strong>{{ $alertas->total() }}</strong> alertas
    </span>
    {{ $alertas->links() }}
  </div>
  @else
  <div class="card-footer bg-transparent border-top px-4 py-2">
    <span class="text-muted small">{{ $alertas->total() }} {{ $alertas->total() === 1 ? 'alerta' : 'alertas' }}</span>
  </div>
  @endif

</div>

{{-- ══════════════════════════════════════════════
     MODAL NUEVA ALERTA
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalNuevaAlerta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form method="POST" action="{{ route('mon-alertas.store') }}">
        @csrf
        <div class="modal-header border-bottom-0 pb-0">
          <div class="d-flex align-items-center gap-2">
            <div class="avatar bg-label-warning rounded">
              <span class="avatar-initial rounded bg-label-warning text-warning"><i class="ti tabler-bell-plus"></i></span>
            </div>
            <div>
              <h5 class="modal-title mb-0">Nueva Alerta Manual</h5>
              <small class="text-muted">Crea una notificación personalizada</small>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body pt-3">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required placeholder="Resumen claro de la alerta">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tipo</label>
              <select name="tipo" class="form-select">
                <option value="sistema">Sistema</option>
                <option value="vencimiento">Vencimiento</option>
                <option value="avance_bajo">Avance Bajo</option>
                <option value="evidencia_falta">Evidencia Faltante</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Prioridad</label>
              <select name="prioridad" class="form-select">
                <option value="alta">🔴 Alta</option>
                <option value="media" selected>🟡 Media</option>
                <option value="baja">🔵 Baja</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
              <textarea name="mensaje" class="form-control" rows="3" required
                placeholder="Detalla el motivo de la alerta, qué actividad o área afecta..."></textarea>
            </div>
            <div class="col-12">
              <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-info">
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" name="enviar_email" value="1" id="chkEmail">
                  <label class="form-check-label fw-medium" for="chkEmail">Notificar por correo electrónico</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top-0 pt-0">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4">
            <i class="ti tabler-send me-1"></i>Crear alerta
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
// Hover effect en filas
document.querySelectorAll('tbody tr').forEach(row => {
  row.addEventListener('mouseenter', () => row.style.background = '#fafafa');
  row.addEventListener('mouseleave', () => row.style.background = '');
});
</script>
@endsection
