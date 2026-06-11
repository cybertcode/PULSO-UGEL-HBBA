@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Alertas — PULSO UGEL')

@section('page-style')
<style>
/* ── Tabla alertas compacta ── */
.tbl-alertas td, .tbl-alertas th { padding: .5rem .9rem !important; vertical-align: middle; }
.tbl-alertas thead th { font-size: .7rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #6e6b7b; background: #f8f7fa; white-space: nowrap; border-bottom: 1px solid rgba(0,0,0,.07) !important; }
.tbl-alertas tbody tr { transition: background .1s; border-bottom: 1px solid rgba(0,0,0,.04) !important; }
.tbl-alertas tbody tr:hover { background: rgba(105,108,255,.04) !important; }

/* Barra de prioridad */
.prio-bar { width: 3px; height: 36px; border-radius: 3px; flex-shrink: 0; display: block; }

/* KPI cards */
.kpi-alerta { border-radius: 12px; border: none; transition: transform .15s, box-shadow .15s; }
.kpi-alerta:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.09); }

/* Filtros pill */
.filter-pills a { line-height: 1.6; }
</style>
@endsection

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
  <div class="col-6 col-md-3">
    <div class="card kpi-alerta h-100" style="background:linear-gradient(135deg,#e52d27,#b31217)">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff">
            <i class="ti tabler-bell"></i>
          </div>
          @if($stats['pendientes'] > 0)
          <span class="badge" style="background:rgba(255,255,255,.25);color:#fff;font-size:.7rem">Activas</span>
          @endif
        </div>
        <div style="font-size:2rem;font-weight:700;color:#fff;line-height:1">{{ $stats['pendientes'] }}</div>
        <div style="font-size:.7rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:rgba(255,255,255,.8)">Pendientes</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-alerta h-100" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff">
            <i class="ti tabler-alert-triangle"></i>
          </div>
        </div>
        <div style="font-size:2rem;font-weight:700;color:#fff;line-height:1">{{ $stats['alta'] }}</div>
        <div style="font-size:.7rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:rgba(255,255,255,.8)">Alta prioridad</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-alerta h-100" style="background:linear-gradient(135deg,#0acffe,#495aff)">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff">
            <i class="ti tabler-info-circle"></i>
          </div>
        </div>
        <div style="font-size:2rem;font-weight:700;color:#fff;line-height:1">{{ $stats['media'] }}</div>
        <div style="font-size:.7rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:rgba(255,255,255,.8)">Media prioridad</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card kpi-alerta h-100" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff">
            <i class="ti tabler-circle-check"></i>
          </div>
        </div>
        <div style="font-size:2rem;font-weight:700;color:#fff;line-height:1">{{ $stats['resueltas'] }}</div>
        <div style="font-size:.7rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:rgba(255,255,255,.8)">Resueltas</div>
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
    <div class="d-flex align-items-center gap-3 flex-wrap pb-3 filter-pills">
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

      <div class="vr opacity-25 d-none d-md-block"></div>

      {{-- Módulo --}}
      <div class="d-flex align-items-center gap-1 flex-wrap">
        <span class="text-muted small me-1" style="white-space:nowrap;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Módulo</span>
        @foreach([
          ''=>['label'=>'Todos','color'=>'secondary'],
          'sci'=>['label'=>'SCI','color'=>'primary'],
          'integridad'=>['label'=>'Integridad','color'=>'warning'],
        ] as $val => $meta)
        @php $isActive = ($modulo ?? '') === $val; @endphp
        <a href="{{ route('mon-alertas', ['tab'=>$tab,'prioridad'=>$prioridad,'tipo'=>$tipo,'modulo'=>$val ?: null]) }}"
           class="badge rounded-pill text-decoration-none px-2 py-1 {{ $isActive ? 'bg-'.$meta['color'] : 'bg-label-secondary text-secondary' }}"
           style="font-size:11px;font-weight:500">{{ $meta['label'] }}</a>
        @endforeach
      </div>

      <div class="vr opacity-25 d-none d-md-block"></div>

      {{-- Tipo --}}
      <div class="d-flex align-items-center gap-1 flex-wrap">
        <span class="text-muted small me-1" style="white-space:nowrap;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Tipo</span>
        @foreach([
          ''=>['label'=>'Todos','icon'=>'tabler-list'],
          'vencimiento'=>['label'=>'Vencimiento','icon'=>'tabler-calendar-x'],
          'vencimiento_proximo'=>['label'=>'Por vencer','icon'=>'tabler-clock-alert'],
          'avance_bajo'=>['label'=>'Avance bajo','icon'=>'tabler-trending-down'],
          'evidencia_falta'=>['label'=>'Sin evidencia','icon'=>'tabler-file-off'],
          'sistema'=>['label'=>'Sistema','icon'=>'tabler-bell'],
        ] as $val => $meta)
        @php $isActive = ($tipo ?? '') === $val; @endphp
        <a href="{{ route('mon-alertas', ['tab'=>$tab,'prioridad'=>$prioridad,'tipo'=>$val ?: null,'modulo'=>$modulo]) }}"
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
    <table class="table align-middle mb-0 tbl-alertas">
      <thead>
        <tr>
          <th class="ps-3">Alerta</th>
          <th class="text-center" style="width:120px">Días restantes</th>
          <th style="width:120px">Vencimiento</th>
          <th style="width:180px">Responsable</th>
          <th class="text-end pe-3" style="width:120px">Acciones</th>
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
            'vencimiento'         => 'Vencimiento',
            'vencimiento_proximo' => 'Por vencer (' . ($alerta->dias_anticipacion ?? '?') . 'd)',
            'avance_bajo'         => 'Avance bajo',
            'evidencia_falta'     => 'Sin evidencia',
            default               => 'Sistema',
          };
          $moduloColor = $alerta->modulo === 'integridad' ? 'warning' : 'primary';
          $diasRestantes = null;
          if ($alerta->actividad?->fecha_limite) {
            $diasRestantes = (int) round(now()->diffInDays($alerta->actividad->fecha_limite, false));
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
          <td class="ps-3">
            <div class="d-flex align-items-center gap-3">
              <span class="prio-bar flex-shrink-0" style="background:{{ $prioColor['hex'] }}"></span>
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
                  @if($alerta->modulo)
                  <span class="badge bg-label-{{ $moduloColor }}" style="font-size:10px">
                    {{ strtoupper($alerta->modulo) }}
                  </span>
                  @endif
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
              {{-- Enviar email manual --}}
              <form method="POST" action="{{ route('mon-alertas.email', $alerta) }}" class="d-inline"
                title="{{ $alerta->email_enviado ? 'Email ya enviado el '.$alerta->email_enviado_at?->format('d/m/Y') : 'Enviar email al responsable' }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-icon btn-label-info"
                  {{ $alerta->email_enviado ? 'disabled' : '' }}>
                  <i class="ti tabler-mail icon-14px"></i>
                </button>
              </form>
              @if(!$isLeida)
              <form method="POST" action="{{ route('mon-alertas.leer', $alerta) }}" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3" style="font-size:12px">
                  <i class="ti tabler-eye me-1" style="font-size:11px"></i>Marcar leída
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
              <label class="form-label fw-semibold">Módulo <span class="text-danger">*</span></label>
              <select name="modulo" class="form-select" required>
                <option value="sci">Sistema de Control Interno</option>
                <option value="integridad">Modelo de Integridad</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tipo</label>
              <select name="tipo" class="form-select">
                <option value="sistema">Sistema</option>
                <option value="vencimiento">Vencimiento</option>
                <option value="vencimiento_proximo">Por vencer</option>
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
// no inline script needed — hover handled by CSS
</script>
@endsection
