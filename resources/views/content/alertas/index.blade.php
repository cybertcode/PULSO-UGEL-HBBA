@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
$configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Alertas — PULSO UGEL')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/select2/select2.js',
])
@endsection

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
    @if($esAdmin)
    <p class="text-muted mb-0">Vista global — todas las alertas del sistema.</p>
    @elseif($esResponsableUnidad)
    <p class="text-muted mb-0">Mostrando alertas de tu unidad orgánica y asignadas a ti.</p>
    @else
    <p class="text-muted mb-0">Mostrando tus alertas personales.</p>
    @endif
  </div>
  <div class="d-flex gap-2">
    @can('alertas.crear')
    <form id="form-marcar-todas" method="POST" action="{{ route('mon-alertas.leer-todas') }}"
      style="{{ $stats['pendientes'] > 0 ? '' : 'display:none' }}">
      @csrf @method('PATCH')
      <button id="btn-marcar-todas" type="submit" class="btn btn-label-success btn-sm">
        <i class="ti tabler-checks me-1"></i>Marcar todas leídas
      </button>
    </form>
    @endcan
    @can('alertas.crear')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaAlerta">
      <i class="ti tabler-plus me-1"></i>Nueva alerta
    </button>
    @endcan
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
        <div id="kpi-pendientes" style="font-size:2rem;font-weight:700;color:#fff;line-height:1">{{ $stats['pendientes'] }}</div>
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
        <div id="kpi-alta" style="font-size:2rem;font-weight:700;color:#fff;line-height:1">{{ $stats['alta'] }}</div>
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
        <div id="kpi-media" style="font-size:2rem;font-weight:700;color:#fff;line-height:1">{{ $stats['media'] }}</div>
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
        <div id="kpi-resueltas" style="font-size:2rem;font-weight:700;color:#fff;line-height:1">{{ $stats['resueltas'] }}</div>
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
          <span id="tab-badge-pendientes" class="badge {{ $tab === 'pendientes' ? 'bg-danger' : 'bg-label-secondary' }} rounded-pill" style="font-size:10px">
            <span id="tab-count-pendientes">{{ $stats['pendientes'] }}</span><i class="ti tabler-arrow-up ms-1" style="font-size:8px"></i>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-3 d-flex align-items-center gap-2 {{ $tab === 'resueltas' ? 'active fw-semibold' : 'text-muted' }}"
           href="{{ route('mon-alertas', ['tab'=>'resueltas','prioridad'=>$prioridad,'tipo'=>$tipo]) }}">
          <i class="ti tabler-circle-check icon-16px"></i>
          Resueltas
          <span id="tab-count-resueltas" class="badge bg-label-success rounded-pill" style="font-size:10px">{{ $stats['resueltas'] }}</span>
        </a>
      </li>
    </ul>

    {{-- Filtros en fila separada y visualmente clara --}}
    <div class="d-flex align-items-center gap-3 flex-wrap pb-3 filter-pills">
      {{-- Prioridad --}}
      <div class="d-flex align-items-center gap-1" data-grupo="prioridad">
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
      <div class="d-flex align-items-center gap-1 flex-wrap" data-grupo="modulo">
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
      <div class="d-flex align-items-center gap-1 flex-wrap" data-grupo="tipo">
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
          <th style="width:180px">Destinatario</th>
          <th class="text-center" style="width:70px">Email</th>
          <th style="width:110px">Creado</th>
          <th class="text-end pe-3" style="width:175px">Acciones</th>
        </tr>
      </thead>
      <tbody id="tbody-alertas">
        @include('content.alertas._filas', ['alertas' => $alertas, 'tab' => $tab, 'esAdmin' => $esAdmin])
      </tbody>
    </table>
  </div>

  {{-- Footer con paginación --}}
  <div id="card-footer-alertas" class="card-footer bg-transparent border-top px-4 py-2 {{ $alertas->hasPages() ? 'd-flex align-items-center justify-content-between py-3' : '' }}">
    <span id="footer-total" class="text-muted small">{!! $alertas->hasPages()
      ? 'Mostrando <strong>'.$alertas->firstItem().'–'.$alertas->lastItem().'</strong> de <strong>'.$alertas->total().'</strong> '.($alertas->total() === 1 ? 'alerta' : 'alertas')
      : $alertas->total().' '.($alertas->total() === 1 ? 'alerta' : 'alertas')
    !!}</span>
    <div id="paginacion-alertas">
      @if($alertas->hasPages()){{ $alertas->links() }}@endif
    </div>
  </div>

</div>

{{-- ══════════════════════════════════════════════
     MODAL VER DETALLE
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalVerAlerta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar bg-label-secondary rounded">
            <span class="avatar-initial rounded bg-label-secondary text-secondary"><i class="ti tabler-eye"></i></span>
          </div>
          <div>
            <h5 class="modal-title mb-0">Detalle de Alerta</h5>
            <small class="text-muted" id="ver-modulo-tipo"></small>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-3">
        <h6 id="ver-titulo" class="fw-bold mb-2"></h6>
        <p id="ver-mensaje" class="text-muted mb-3" style="font-size:13.5px;white-space:pre-wrap"></p>
        <div class="row g-2" style="font-size:13px">
          <div class="col-6">
            <div class="text-muted small">Prioridad</div>
            <span id="ver-prioridad" class="badge mt-1"></span>
          </div>
          <div class="col-6">
            <div class="text-muted small">Estado</div>
            <div id="ver-estado" class="fw-medium mt-1"></div>
          </div>
          <div class="col-6">
            <div class="text-muted small">Destinatario</div>
            <div id="ver-destinatario" class="fw-medium mt-1"></div>
            <div id="ver-email-dest" class="text-muted" style="font-size:11px"></div>
          </div>
          <div class="col-6">
            <div class="text-muted small">Email enviado</div>
            <div id="ver-email-enviado" class="fw-medium mt-1"></div>
          </div>
          <div class="col-6">
            <div class="text-muted small">Creado</div>
            <div id="ver-creado" class="fw-medium mt-1"></div>
          </div>
          <div class="col-12" id="ver-actividad-bloque" style="display:none">
            <div class="text-muted small">Actividad vinculada</div>
            <div id="ver-actividad" class="fw-medium mt-1"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL NUEVA ALERTA
══════════════════════════════════════════════ --}}
@can('alertas.crear')
<div class="modal fade" id="modalNuevaAlerta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content border-0 shadow">
      <form method="POST" action="{{ route('mon-alertas.store') }}" id="form-nueva-alerta">
        @csrf
        <div class="modal-header border-bottom-0 pb-0">
          <div class="d-flex align-items-center gap-2">
            <div class="avatar bg-label-warning rounded">
              <span class="avatar-initial rounded bg-label-warning text-warning"><i class="ti tabler-bell-plus"></i></span>
            </div>
            <div>
              <h5 class="modal-title mb-0">Nueva Alerta</h5>
              <small class="text-muted">La alerta quedará registrada para el destinatario seleccionado</small>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body pt-3">
          <div class="row g-3">
            {{-- Tipo de destinatario — solo gestores --}}
            @if($esAdmin)
            <div class="col-12">
              <label class="form-label fw-semibold">¿A quién va dirigida? <span class="text-danger">*</span></label>
              <div class="d-flex gap-2 flex-wrap" id="grupo-tipo-destino">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="tipo_destino" id="td-individual" value="individual" checked>
                  <label class="form-check-label" for="td-individual">
                    <i class="ti tabler-user me-1 text-primary"></i>Individual
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="tipo_destino" id="td-unidad" value="unidad">
                  <label class="form-check-label" for="td-unidad">
                    <i class="ti tabler-building me-1 text-warning"></i>Por unidad
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="tipo_destino" id="td-todos" value="todos">
                  <label class="form-check-label" for="td-todos">
                    <i class="ti tabler-users me-1 text-success"></i>Toda la institución
                  </label>
                </div>
              </div>
            </div>
            {{-- Panel Individual --}}
            <div class="col-12" id="panel-individual">
              <label class="form-label fw-semibold">Usuario destinatario <span class="text-danger">*</span></label>
              <select name="usuario_id" id="nueva-usuario-id" class="form-select select2-nueva-dest">
                <option value="">— Busca por nombre o correo —</option>
                @foreach($usuarios as $u)
                  <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                @endforeach
              </select>
              <div class="form-text">El usuario que recibirá esta alerta en su bandeja.</div>
            </div>
            {{-- Panel Unidad --}}
            <div class="col-12 d-none" id="panel-unidad">
              <label class="form-label fw-semibold">Unidad orgánica <span class="text-danger">*</span></label>
              <select name="unidad_organica_id" id="nueva-unidad-id" class="form-select select2-nueva-unidad">
                <option value="">— Selecciona la unidad —</option>
                @foreach(\App\Models\UnidadOrganica::orderBy('nombre')->get() as $uo)
                  <option value="{{ $uo->id }}">{{ $uo->nombre }}</option>
                @endforeach
              </select>
              <div class="form-text">La alerta se enviará a todos los usuarios de esa unidad con acceso al sistema.</div>
            </div>
            {{-- Panel Todos --}}
            <div class="col-12 d-none" id="panel-todos">
              <div class="alert alert-success mb-0 py-2">
                <i class="ti tabler-users me-2"></i>
                Esta alerta se enviará a <strong>todos los usuarios</strong> de la institución que tengan acceso al módulo de alertas.
              </div>
            </div>
            @else
            {{-- No-gestores: siempre individual para sí mismo --}}
            <input type="hidden" name="tipo_destino" value="individual">
            @endif
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" class="form-control" required placeholder="Resumen claro de la alerta">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Módulo <span class="text-danger">*</span></label>
              <select name="modulo" id="nueva-modulo" class="form-select" required>
                <option value="sci">Control Interno (SCI)</option>
                <option value="integridad">Modelo de Integridad</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
              <select name="tipo" class="form-select" required>
                <option value="sistema">Sistema</option>
                <option value="vencimiento">Vencimiento</option>
                <option value="vencimiento_proximo">Por vencer</option>
                <option value="avance_bajo">Avance Bajo</option>
                <option value="evidencia_falta">Evidencia Faltante</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Actividad vinculada <span class="text-muted fw-normal small">(opcional)</span></label>
              <select name="actividad_id" id="nueva-actividad-id" class="form-select select2-nueva-actividad">
                <option value="">— Selecciona el módulo para cargar actividades —</option>
              </select>
              <div class="form-text">Vincula esta alerta a una actividad específica del módulo seleccionado.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Prioridad <span class="text-danger">*</span></label>
              <select name="prioridad" class="form-select" required>
                <option value="alta">🔴 Alta</option>
                <option value="media" selected>🟡 Media</option>
                <option value="baja">🔵 Baja</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
              <textarea name="mensaje" class="form-control" rows="3" required
                placeholder="Detalla el motivo de la alerta, qué área o actividad afecta..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between align-items-center flex-wrap gap-2">
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" name="enviar_email" value="1" id="chkEmail">
            <label class="form-check-label fw-medium ms-1 text-info" for="chkEmail">
              <i class="ti tabler-mail me-1"></i>Notificar por correo
            </label>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary px-4">
              <i class="ti tabler-bell-plus me-1"></i>Crear alerta
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan

{{-- ══════════════════════════════════════════════
     MODAL EDITAR ALERTA
══════════════════════════════════════════════ --}}
@can('alertas.crear')
<div class="modal fade" id="modalEditarAlerta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content border-0 shadow">
      <form method="POST" id="form-editar-alerta" action="">
        @csrf @method('PUT')
        <div class="modal-header border-bottom-0 pb-0">
          <div class="d-flex align-items-center gap-2">
            <div class="avatar bg-label-primary rounded">
              <span class="avatar-initial rounded bg-label-primary text-primary"><i class="ti tabler-edit"></i></span>
            </div>
            <div>
              <h5 class="modal-title mb-0">Editar Alerta</h5>
              <small class="text-muted">Modifica los datos de la alerta</small>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body pt-3">
          <div class="row g-3">
            @if($esAdmin)
            <div class="col-12">
              <label class="form-label fw-semibold">Destinatario <span class="text-danger">*</span></label>
              <select name="usuario_id" id="edit-usuario-id" class="form-select select2-edit-dest" required>
                <option value="">— Selecciona el usuario destinatario —</option>
                @foreach($usuarios as $u)
                  <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                @endforeach
              </select>
            </div>
            @endif
            <div class="col-12">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" name="titulo" id="edit-titulo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Módulo <span class="text-danger">*</span></label>
              <select name="modulo" id="edit-modulo" class="form-select" required>
                <option value="sci">Control Interno (SCI)</option>
                <option value="integridad">Modelo de Integridad</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
              <select name="tipo" id="edit-tipo" class="form-select" required>
                <option value="sistema">Sistema</option>
                <option value="vencimiento">Vencimiento</option>
                <option value="vencimiento_proximo">Por vencer</option>
                <option value="avance_bajo">Avance Bajo</option>
                <option value="evidencia_falta">Evidencia Faltante</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Actividad vinculada <span class="text-muted fw-normal small">(opcional)</span></label>
              <select name="actividad_id" id="edit-actividad-id" class="form-select select2-edit-actividad">
                <option value="">— Sin actividad vinculada —</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Prioridad <span class="text-danger">*</span></label>
              <select name="prioridad" id="edit-prioridad" class="form-select" required>
                <option value="alta">🔴 Alta</option>
                <option value="media">🟡 Media</option>
                <option value="baja">🔵 Baja</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
              <textarea name="mensaje" id="edit-mensaje" class="form-control" rows="3" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top-0 pt-0">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4">
            <i class="ti tabler-device-floppy me-1"></i>Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  const ACT_URL = '{{ route('mon-alertas.actividades') }}';

  // ── Carga actividades por módulo y puebla un select2 ──────────
  function cargarActividades(modulo, selectEl, valorActual) {
    if (!modulo) return;
    const $sel = window.$ ? $(selectEl) : null;
    if ($sel && $sel.data('select2')) $sel.select2('destroy');

    selectEl.innerHTML = '<option value="">⏳ Cargando actividades...</option>';
    selectEl.disabled = true;

    fetch(ACT_URL + '?modulo=' + modulo, {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(actividades => {
      selectEl.innerHTML = '<option value="">— Sin actividad vinculada —</option>';
      actividades.forEach(a => {
        const estadoIcon = { pendiente:'🕐', en_proceso:'🔄', completada:'✅', observado:'⚠️', vencida:'❌' }[a.estado] || '📋';
        const opt = document.createElement('option');
        opt.value = a.id;
        opt.textContent = estadoIcon + ' ' + a.label + ' (' + a.avance + '%)';
        if (String(a.id) === String(valorActual)) opt.selected = true;
        selectEl.appendChild(opt);
      });
      selectEl.disabled = false;

      if ($sel && window.$.fn.select2) {
        const parent = selectEl.closest('.modal');
        $sel.select2({
          placeholder: '— Busca actividad por código o nombre —',
          allowClear: true,
          width: '100%',
          dropdownParent: parent ? $(parent) : null,
        });
        if (valorActual) $sel.val(String(valorActual)).trigger('change');
      }
    })
    .catch(() => {
      selectEl.innerHTML = '<option value="">— Error al cargar actividades —</option>';
      selectEl.disabled = false;
    });
  }

  // ── Select2 destinatario ──────────────────────────────────────
  if (window.$ && $.fn.select2) {
    const s2opts = {
      placeholder: '— Busca por nombre o correo —',
      allowClear: true,
      width: '100%',
      dropdownParent: null,
    };

    const modalNueva  = document.getElementById('modalNuevaAlerta');
    const modalEditar2 = document.getElementById('modalEditarAlerta');

    if (modalNueva) {
      const s2nueva = $(modalNueva).find('.select2-nueva-dest');
      s2nueva.select2({ ...s2opts, dropdownParent: $(modalNueva) });
    }
    if (modalEditar2) {
      const s2edit = $(modalEditar2).find('.select2-edit-dest');
      s2edit.select2({ ...s2opts, dropdownParent: $(modalEditar2) });
    }
  }

  // ── Tipo de destino — switching de paneles ───────────────────
  const panelIndividual = document.getElementById('panel-individual');
  const panelUnidad     = document.getElementById('panel-unidad');
  const panelTodos      = document.getElementById('panel-todos');
  const grupoTipo       = document.getElementById('grupo-tipo-destino');

  function actualizarPanelDestino(valor) {
    if (!panelIndividual) return;
    panelIndividual.classList.toggle('d-none', valor !== 'individual');
    panelUnidad?.classList.toggle('d-none', valor !== 'unidad');
    panelTodos?.classList.toggle('d-none', valor !== 'todos');

    // required dinámico
    const selUser = document.getElementById('nueva-usuario-id');
    const selUnid = document.getElementById('nueva-unidad-id');
    if (selUser) selUser.required = valor === 'individual';
    if (selUnid) selUnid.required = valor === 'unidad';
  }

  if (grupoTipo) {
    grupoTipo.querySelectorAll('input[name="tipo_destino"]').forEach(radio => {
      radio.addEventListener('change', () => actualizarPanelDestino(radio.value));
    });
    // Estado inicial
    const checked = grupoTipo.querySelector('input[name="tipo_destino"]:checked');
    if (checked) actualizarPanelDestino(checked.value);
  }

  // Select2 para unidad orgánica
  if (window.$ && $.fn.select2) {
    const modalNva = document.getElementById('modalNuevaAlerta');
    if (modalNva) {
      $(modalNva).find('.select2-nueva-unidad').select2({
        placeholder: '— Busca la unidad —',
        allowClear: true,
        width: '100%',
        dropdownParent: $(modalNva),
      });
    }
  }

  // ── Cambio de módulo en modal NUEVA → recarga actividades ─────
  const nuevaModuloSel = document.getElementById('nueva-modulo');
  const nuevaActSel    = document.getElementById('nueva-actividad-id');
  if (nuevaModuloSel && nuevaActSel) {
    nuevaModuloSel.addEventListener('change', function () {
      cargarActividades(this.value, nuevaActSel, null);
    });
    // Carga inicial con el valor por defecto (sci)
    cargarActividades(nuevaModuloSel.value, nuevaActSel, null);
  }

  // ── Cambio de módulo en modal EDITAR → recarga actividades ────
  const editModuloSel = document.getElementById('edit-modulo');
  const editActSel    = document.getElementById('edit-actividad-id');
  if (editModuloSel && editActSel) {
    editModuloSel.addEventListener('change', function () {
      cargarActividades(this.value, editActSel, null);
    });
  }

  const POLL_URL = '{{ route('mon-alertas.poll') }}';
  const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const POLL_MS  = 30000;
  const tbody    = document.getElementById('tbody-alertas');
  const footerEl = document.getElementById('footer-total');

  // ── Modal VER ─────────────────────────────────────────────────
  const modalVer = document.getElementById('modalVerAlerta');
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-ver-alerta');
    if (!btn || !modalVer) return;
    const d = btn.dataset;
    const colores = { alta:'danger', media:'warning', baja:'info' };
    modalVer.querySelector('#ver-titulo').textContent    = d.titulo;
    modalVer.querySelector('#ver-mensaje').textContent   = d.mensaje;
    modalVer.querySelector('#ver-modulo-tipo').textContent = d.modulo + ' · ' + d.tipo;
    modalVer.querySelector('#ver-destinatario').textContent = d.destinatario;
    modalVer.querySelector('#ver-email-dest').textContent   = d.emailDest !== '—' ? d.emailDest : '';
    modalVer.querySelector('#ver-email-enviado').textContent = d.emailEnviado;
    modalVer.querySelector('#ver-estado').textContent    = d.estado;
    modalVer.querySelector('#ver-creado').textContent    = d.creado;
    const pEl = modalVer.querySelector('#ver-prioridad');
    pEl.textContent  = d.prioridadLabel;
    pEl.className    = 'badge mt-1 bg-' + (colores[d.prioridad] || 'secondary');
    const actBloque  = modalVer.querySelector('#ver-actividad-bloque');
    const actEl      = modalVer.querySelector('#ver-actividad');
    if (d.actividad) { actBloque.style.display = ''; actEl.textContent = d.actividad; }
    else              { actBloque.style.display = 'none'; }
    new bootstrap.Modal(modalVer).show();
  });

  // ── Modal EDITAR ──────────────────────────────────────────────
  const modalEditar   = document.getElementById('modalEditarAlerta');
  const formEditar    = document.getElementById('form-editar-alerta');
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-editar-alerta');
    if (!btn || !modalEditar || !formEditar) return;
    const d = btn.dataset;
    formEditar.action = d.url;
    formEditar.querySelector('#edit-titulo').value   = d.titulo;
    formEditar.querySelector('#edit-mensaje').value  = d.mensaje;
    formEditar.querySelector('#edit-modulo').value   = d.modulo;
    formEditar.querySelector('#edit-tipo').value     = d.tipo;
    formEditar.querySelector('#edit-prioridad').value = d.prioridad;
    const selUser = formEditar.querySelector('#edit-usuario-id');
    if (selUser) {
      selUser.value = d.usuarioId || '';
      if (window.$ && $(selUser).data('select2')) $(selUser).trigger('change');
    }
    // Carga actividades del módulo y preselecciona la vinculada
    if (editActSel) {
      cargarActividades(d.modulo, editActSel, d.actividadId || null);
    }
    new bootstrap.Modal(modalEditar).show();
  });

  // ── Submit EDITAR vía AJAX ────────────────────────────────────
  if (formEditar) {
    formEditar.addEventListener('submit', function (e) {
      e.preventDefault();
      const fd  = new FormData(formEditar);
      const btn = formEditar.querySelector('[type=submit]');
      if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...'; }
      lockModal(modalEditar);
      fetch(formEditar.action, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
      })
      .then(res => res.json().then(r => ({ ok: res.ok, data: r })))
      .then(({ ok, data }) => {
        if (ok) {
          bootstrap.Modal.getInstance(modalEditar)?.hide();
          toast(data.message || 'Alerta actualizada.', 'success');
          doPoll(true);
        } else {
          unlockModal(modalEditar);
          toast(data.message || 'Error al guardar.', 'error');
        }
      })
      .catch(() => { unlockModal(modalEditar); toast('Error de conexión.', 'error'); })
      .finally(() => {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ti tabler-device-floppy me-1"></i>Guardar cambios'; }
      });
    });
  }

  // ── Bloquear/desbloquear modal durante operación ─────────────────
  function lockModal(modalEl) {
    if (!modalEl) return;
    const instance = bootstrap.Modal.getInstance(modalEl);
    if (instance) {
      instance._config.backdrop = 'static';
      instance._config.keyboard = false;
    }
    // Deshabilitar botón X y backdrop visual
    modalEl.querySelector('.btn-close')?.setAttribute('disabled', 'true');
  }
  function unlockModal(modalEl) {
    if (!modalEl) return;
    const instance = bootstrap.Modal.getInstance(modalEl);
    if (instance) {
      instance._config.backdrop = true;
      instance._config.keyboard = true;
    }
    modalEl.querySelector('.btn-close')?.removeAttribute('disabled');
  }

  // ── Submit NUEVA ALERTA — AJAX + loading + anti-doble-clic ──────
  const formNueva   = document.getElementById('form-nueva-alerta');
  const modalNuevaEl = document.getElementById('modalNuevaAlerta');
  if (formNueva) {
    formNueva.addEventListener('submit', function (e) {
      e.preventDefault();
      const btn = formNueva.querySelector('[type=submit]');
      const btnOrigHtml = '<i class="ti tabler-bell-plus me-1" style="font-family:\'tabler-icons\'!important"></i>Crear alerta';
      if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creando...'; }
      lockModal(modalNuevaEl);
      fetch(formNueva.action, {
        method: 'POST',
        body: new FormData(formNueva),
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
      })
      .then(res => res.json().then(r => ({ ok: res.ok, data: r })))
      .then(({ ok, data }) => {
        if (ok) {
          bootstrap.Modal.getInstance(modalNuevaEl)?.hide();
          toast(data.message || 'Alerta creada.', 'success');
          doPoll(true);
        } else {
          unlockModal(modalNuevaEl);
          toast(data.message || 'Error al crear la alerta.', 'error');
        }
      })
      .catch(() => { unlockModal(modalNuevaEl); toast('Error de conexión.', 'error'); })
      .finally(() => {
        if (btn) { btn.disabled = false; btn.innerHTML = btnOrigHtml; }
      });
    });
  }

  // Reset modals al cerrar
  [modalEditar, document.getElementById('modalNuevaAlerta')].forEach(m => {
    m?.addEventListener('hidden.bs.modal', () => {
      m.querySelector('form')?.reset();
      if (window.$) $(m).find('.select2-nueva-dest, .select2-edit-dest, .select2-nueva-unidad').trigger('change');
      // Restaurar panel de destino a "individual"
      if (m.id === 'modalNuevaAlerta') actualizarPanelDestino('individual');
    });
  });

  // ── Estado activo de filtros (sin depender de la URL) ──────────
  const estado = {
    tab:       '{{ $tab }}',
    prioridad: '{{ $prioridad ?? '' }}',
    modulo:    '{{ $modulo ?? '' }}',
    tipo:      '{{ $tipo ?? '' }}',
    page:      '{{ request('page', 1) }}',
  };

  // ── Flag para pausar poll mientras hay modal abierto ───────────
  let modalAbierto = false;
  document.addEventListener('show.bs.modal',   () => { modalAbierto = true; });
  document.addEventListener('hidden.bs.modal', () => { modalAbierto = false; });

  // ── Toast usando el sistema Bootstrap del layout ───────────────
  function toast(msg, tipo) {
    const colores = {
      success: { borde:'#28a745', icono:'tabler-circle-check',  titulo:'Operación exitosa' },
      error:   { borde:'#dc3545', icono:'tabler-circle-x',      titulo:'Error' },
      warning: { borde:'#fd7e14', icono:'tabler-alert-triangle', titulo:'Advertencia' },
      info:    { borde:'#0d6efd', icono:'tabler-info-circle',    titulo:'Información' },
    };
    const c   = colores[tipo] || colores.success;
    const cnt = document.querySelector('.toast-container');
    if (!cnt || !window.bootstrap) return;
    const el = document.createElement('div');
    el.className = 'toast border-0 shadow';
    el.setAttribute('role', 'alert');
    el.style.cssText = `background:#fff;border-left:4px solid ${c.borde} !important;border-radius:8px`;
    el.innerHTML = `
      <div class="toast-header border-0 pb-0" style="background:transparent">
        <span class="me-2" style="color:${c.borde}"><i class="ti ${c.icono}" style="font-size:18px"></i></span>
        <strong class="me-auto" style="color:${c.borde}">${c.titulo}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
      </div>
      <div class="toast-body pt-1" style="color:#333">${msg}</div>`;
    cnt.appendChild(el);
    const t = new bootstrap.Toast(el, { autohide:true, delay:4000 });
    t.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  }

  // ── Animación de número en KPI ─────────────────────────────────
  function animNum(el, newVal) {
    if (!el) return;
    if (parseInt(el.textContent) === newVal) return;
    el.style.transition = 'transform .2s, opacity .2s';
    el.style.transform  = 'scale(1.3)';
    el.style.opacity    = '0.4';
    setTimeout(() => {
      el.textContent     = newVal;
      el.style.transform = 'scale(1)';
      el.style.opacity   = '1';
    }, 200);
  }

  // ── Actualiza UI con la respuesta del poll ─────────────────────
  function aplicarData(data) {
    animNum(document.getElementById('kpi-pendientes'), data.stats.pendientes);
    animNum(document.getElementById('kpi-alta'),       data.stats.alta);
    animNum(document.getElementById('kpi-media'),      data.stats.media);
    animNum(document.getElementById('kpi-resueltas'),  data.stats.resueltas);

    const cntP = document.getElementById('tab-count-pendientes');
    const cntR = document.getElementById('tab-count-resueltas');
    if (cntP) cntP.textContent = data.stats.pendientes;
    if (cntR) cntR.textContent = data.stats.resueltas;

    const badgeP = document.getElementById('tab-badge-pendientes');
    if (badgeP) {
      const activo = data.stats.pendientes > 0 && estado.tab === 'pendientes';
      badgeP.classList.toggle('bg-danger',          activo);
      badgeP.classList.toggle('bg-label-secondary', !activo);
    }

    if (tbody) {
      tbody.innerHTML = data.html;
      bindAcciones(tbody);
    }

    // Actualizar footer (texto + paginación)
    if (footerEl) footerEl.innerHTML = data.footer_html || data.total_texto;
    const footerCard = document.getElementById('card-footer-alertas');
    const pagEl      = document.getElementById('paginacion-alertas');
    if (pagEl) {
      pagEl.innerHTML = data.paginacion_html || '';
      bindPaginacion(pagEl);
    }
    if (footerCard) {
      footerCard.classList.toggle('d-flex',                  data.has_pages);
      footerCard.classList.toggle('align-items-center',     data.has_pages);
      footerCard.classList.toggle('justify-content-between',data.has_pages);
      footerCard.classList.toggle('py-3',                   data.has_pages);
      footerCard.classList.toggle('py-2',                  !data.has_pages);
    }

    // Muestra/oculta botón "Marcar todas leídas"
    const btnTodas = document.getElementById('btn-marcar-todas');
    if (btnTodas) btnTodas.style.display = data.stats.pendientes > 0 ? '' : 'none';
  }

  // ── Intercepta clicks en la paginación dinámica ───────────────
  function bindPaginacion(root) {
    root.querySelectorAll('a[href]').forEach(a => {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        const url = new URL(this.href, location.origin);
        estado.page = url.searchParams.get('page') || '1';
        pushEstado();
        doPoll();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });
  }

  // Enlaza la paginación inicial del servidor
  bindPaginacion(document.getElementById('paginacion-alertas') || document);

  // ── Petición al endpoint poll ──────────────────────────────────
  function doPoll(resetPage) {
    if (modalAbierto) return;
    if (resetPage) { estado.page = '1'; pushEstado(); }
    const params = new URLSearchParams(estado);
    fetch(POLL_URL + '?' + params, {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.ok ? r.json() : null)
    .then(data => { if (data) aplicarData(data); })
    .catch(() => {});
  }

  // ── Petición AJAX genérica ─────────────────────────────────────
  function ajaxForm(method, url, formData) {
    if (method === 'DELETE' || method === 'PATCH') {
      formData = formData || new FormData();
      formData.append('_method', method);
      method = 'POST';
    }
    return fetch(url, {
      method,
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    }).then(res => {
      if (res.status === 419) { location.reload(); return Promise.reject('csrf'); }
      return res.json().then(r => { if (!res.ok) return Promise.reject(r); return r; });
    });
  }

  // ── Enlaza acciones AJAX en el tbody (re-ejecutar tras cada poll) ──
  function bindAcciones(root) {

    // Marcar leída
    root.querySelectorAll('.form-marcar-leida').forEach(form => {
      if (form.dataset.bound) return;
      form.dataset.bound = '1';
      form.addEventListener('submit', e => {
        e.preventDefault();
        const btn = form.querySelector('button');
        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; }
        const fd = new FormData(form);
        ajaxForm('PATCH', form.action, fd)
          .then(r => { toast(r.message || 'Marcada como leída'); doPoll(true); })
          .catch(err => { toast(err?.message || 'Error al marcar', 'error'); if (btn) btn.disabled = false; });
      });
    });

    // Enviar email
    root.querySelectorAll('.form-email-alerta').forEach(form => {
      if (form.dataset.bound) return;
      form.dataset.bound = '1';
      form.addEventListener('submit', e => {
        e.preventDefault();
        const btn = form.querySelector('button');
        if (btn) btn.disabled = true;
        fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(res => res.json().then(r => ({ ok: res.ok, data: r })))
        .then(({ ok, data }) => {
          if (ok) {
            toast(data.message || 'Email enviado correctamente.', 'success');
            doPoll();
          } else {
            toast(data.message || 'No se pudo enviar el email.', 'error');
            if (btn) btn.disabled = false;
          }
        })
        .catch(() => { toast('Error de conexión al enviar email.', 'error'); if (btn) btn.disabled = false; });
      });
    });

    // Eliminar
    root.querySelectorAll('.form-eliminar-alerta').forEach(form => {
      if (form.dataset.bound) return;
      form.dataset.bound = '1';
      form.addEventListener('submit', e => {
        e.preventDefault();
        const titulo = form.dataset.titulo || 'esta alerta';
        const btnDel = form.querySelector('button');
        const confirmar = () => {
          if (btnDel) { btnDel.disabled = true; btnDel.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; }
          ajaxForm('DELETE', form.action, new FormData(form))
            .then(r => { toast(r.message || 'Alerta eliminada'); doPoll(true); })
            .catch(err => { toast(err?.message || 'Error al eliminar', 'error'); if (btnDel) btnDel.disabled = false; });
        };
        if (window.Swal) {
          Swal.fire({
            title: '¿Eliminar alerta?',
            html: 'Se eliminará permanentemente: <strong>' + titulo + '</strong>',
            icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ea5455',
            customClass: { confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false,
          }).then(r => { if (r.isConfirmed) confirmar(); });
        } else {
          if (confirm('¿Eliminar "' + titulo + '"?')) confirmar();
        }
      });
    });

    // Inicializar tooltips Bootstrap en este bloque
    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      if (window.bootstrap?.Tooltip) new bootstrap.Tooltip(el, { trigger: 'hover' });
    });
  }

  bindAcciones(document);

  // ── Detecta qué grupo pertenece un link de filtro ─────────────
  function grupoFiltro(params) {
    if (params.get('prioridad')) return 'prioridad';
    if (params.get('modulo'))    return 'modulo';
    if (params.get('tipo'))      return 'tipo';
    return 'todos'; // link "Todos" de cualquier grupo
  }

  // ── Interceptar TABS ──────────────────────────────────────────
  document.querySelectorAll('.nav-tabs .nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const params = new URL(this.href, location.origin).searchParams;
      estado.tab  = params.get('tab') || 'pendientes';
      estado.page = '1';
      actualizarTabsUI();
      pushEstado();
      doPoll();
    });
  });

  // ── Interceptar FILTROS (preserva otros grupos) ───────────────
  document.querySelectorAll('.filter-pills a').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const params = new URL(this.href, location.origin).searchParams;
      const grupo  = grupoFiltro(params);

      // Cada grupo "Todos" limpia solo su propio grupo
      // Los links individuales de prioridad/modulo/tipo son toggle:
      // si ya está activo, lo desmarca; si no, lo activa
      if (grupo === 'prioridad') {
        const val = params.get('prioridad');
        estado.prioridad = estado.prioridad === val ? '' : val;
      } else if (grupo === 'modulo') {
        const val = params.get('modulo');
        estado.modulo = estado.modulo === val ? '' : val;
      } else if (grupo === 'tipo') {
        const val = params.get('tipo');
        estado.tipo = estado.tipo === val ? '' : val;
      } else {
        // "Todos" de cualquier grupo — limpia según el contexto del link
        // Identificamos el grupo por la sección donde está el link
        const seccion = link.closest('[data-grupo]')?.dataset.grupo;
        if (seccion === 'prioridad') estado.prioridad = '';
        else if (seccion === 'modulo') estado.modulo = '';
        else if (seccion === 'tipo')   estado.tipo = '';
        else { estado.prioridad = ''; estado.modulo = ''; estado.tipo = ''; }
      }

      estado.page = '1';
      actualizarFiltrosUI();
      pushEstado();
      doPoll();
    });
  });

  function pushEstado() {
    const p = new URLSearchParams({ tab: estado.tab });
    if (estado.prioridad)              p.set('prioridad', estado.prioridad);
    if (estado.modulo)                 p.set('modulo',    estado.modulo);
    if (estado.tipo)                   p.set('tipo',      estado.tipo);
    if (estado.page && estado.page !== '1') p.set('page', estado.page);
    history.pushState({...estado}, '', '/alertas?' + p.toString());
  }

  function actualizarTabsUI() {
    document.querySelectorAll('.nav-tabs .nav-link').forEach(l => {
      const lTab = new URL(l.href, location.origin).searchParams.get('tab') || 'pendientes';
      l.classList.toggle('active',      lTab === estado.tab);
      l.classList.toggle('fw-semibold', lTab === estado.tab);
      l.classList.toggle('text-muted',  lTab !== estado.tab);
    });
  }

  function actualizarFiltrosUI() {
    const colorPrio = { alta:'danger', media:'warning', baja:'info' };
    const colorMod  = { sci:'primary', integridad:'warning' };

    document.querySelectorAll('.filter-pills a').forEach(a => {
      const p     = new URL(a.href, location.origin).searchParams;
      const aPrio = p.get('prioridad') || '';
      const aMod  = p.get('modulo')    || '';
      const aTipo = p.get('tipo')      || '';
      const grupo = grupoFiltro(p);
      const seccion = a.closest('[data-grupo]')?.dataset.grupo;

      let activo = false;
      if (grupo === 'prioridad')                     activo = estado.prioridad === aPrio;
      else if (grupo === 'modulo')                   activo = estado.modulo    === aMod;
      else if (grupo === 'tipo')                     activo = estado.tipo      === aTipo;
      else if (seccion === 'prioridad')              activo = estado.prioridad === '';
      else if (seccion === 'modulo')                 activo = estado.modulo    === '';
      else if (seccion === 'tipo')                   activo = estado.tipo      === '';

      // Reconstruye clases sin tocar las de layout (rounded-pill, text-decoration-none, etc.)
      a.classList.remove(
        'bg-danger','bg-warning','bg-info','bg-primary','bg-secondary',
        'bg-label-secondary','bg-label-danger','bg-label-warning','bg-label-info','bg-label-primary',
        'text-secondary'
      );
      if (activo) {
        if (aPrio)     a.classList.add('bg-' + (colorPrio[aPrio] || 'secondary'));
        else if (aMod) a.classList.add('bg-' + (colorMod[aMod]   || 'secondary'));
        else           a.classList.add('bg-primary');
      } else {
        a.classList.add('bg-label-secondary', 'text-secondary');
      }
    });
  }

  // ── Botón "Marcar todas leídas" vía AJAX ─────────────────────
  const formTodas = document.getElementById('form-marcar-todas');
  if (formTodas) {
    formTodas.addEventListener('submit', e => {
      e.preventDefault();
      ajaxForm('PATCH', formTodas.action, new FormData(formTodas))
        .then(r => { toast(r.message || 'Todas marcadas como leídas'); doPoll(true); })
        .catch(() => toast('Error', 'error'));
    });
  }


  // ── Polling automático ────────────────────────────────────────
  let pollTimer = setInterval(doPoll, POLL_MS);
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      clearInterval(pollTimer);
    } else {
      doPoll();
      pollTimer = setInterval(doPoll, POLL_MS);
    }
  });

});
</script>
@endsection
