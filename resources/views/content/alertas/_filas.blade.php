@php use Illuminate\Support\Str; use Illuminate\Support\Facades\Storage; @endphp
@forelse($alertas as $alerta)
@php
  $prioColor = match($alerta->prioridad) {
    'alta'  => ['hex'=>'#ea5455','label'=>'danger', 'text'=>'Alta'],
    'media' => ['hex'=>'#ff9f43','label'=>'warning','text'=>'Media'],
    default => ['hex'=>'#00cfe8','label'=>'info',   'text'=>'Baja'],
  };
  $tipoIcon = match($alerta->tipo) {
    'vencimiento'         => 'tabler-calendar-x',
    'vencimiento_proximo' => 'tabler-alarm',
    'avance_bajo'         => 'tabler-trending-down',
    'evidencia_falta'     => 'tabler-file-off',
    default               => 'tabler-bell-ringing',
  };
  $tipoLabel = match($alerta->tipo) {
    'vencimiento'         => 'Vencimiento',
    'vencimiento_proximo' => 'Por vencer',
    'avance_bajo'         => 'Avance bajo',
    'evidencia_falta'     => 'Sin evidencia',
    default               => 'Sistema',
  };
  $moduloColor = $alerta->modulo === 'integridad' ? 'warning' : 'primary';
  $isLeida     = (bool) $alerta->leida;
  $destinatario = $alerta->usuario;
@endphp
<tr class="{{ $isLeida ? 'opacity-60' : '' }}" style="border-bottom:1px solid #f0f0f0">

  {{-- Prioridad + Título + badges --}}
  <td class="ps-3" style="min-width:260px">
    <div class="d-flex align-items-center gap-3">
      <span style="width:3px;height:38px;border-radius:3px;background:{{ $prioColor['hex'] }};flex-shrink:0"></span>
      <div class="avatar flex-shrink-0 rounded"
           style="background:{{ $prioColor['hex'] }};width:36px;height:36px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="ti {{ $tipoIcon }}" style="font-size:17px;color:#fff;font-family:'tabler-icons'!important"></i>
      </div>
      <div class="overflow-hidden">
        <div class="fw-semibold text-heading lh-sm mb-1" style="font-size:13.5px">{{ $alerta->titulo }}</div>
        <div class="d-flex align-items-center gap-1 flex-wrap">
          <span class="badge bg-label-{{ $prioColor['label'] }}" style="font-size:10px">{{ $prioColor['text'] }}</span>
          <span class="badge bg-label-secondary text-secondary" style="font-size:10px">
            <i class="ti {{ $tipoIcon }} me-1" style="font-size:9px;font-family:'tabler-icons'!important"></i>{{ $tipoLabel }}
          </span>
          <span class="badge bg-label-{{ $moduloColor }}" style="font-size:10px">{{ strtoupper($alerta->modulo) }}</span>
          @if(!$isLeida)
          <span class="badge bg-danger" style="font-size:10px">Pendiente</span>
          @else
          <span class="badge bg-label-success" style="font-size:10px">
            <i class="ti tabler-circle-check me-1" style="font-size:9px;font-family:'tabler-icons'!important"></i>Resuelta
          </span>
          @endif
        </div>
      </div>
    </div>
  </td>

  {{-- Destinatario --}}
  <td class="py-3" style="min-width:160px">
    @if($alerta->tipo_destino === 'todos')
      <div class="d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:32px;height:32px;background:#28c76f;color:#fff;font-size:14px">
          <i class="ti tabler-users" style="font-family:'tabler-icons'!important"></i>
        </div>
        <div>
          <div class="fw-semibold" style="font-size:12px">Toda la institución</div>
          <span class="badge bg-label-success" style="font-size:9px">Masivo</span>
        </div>
      </div>
    @elseif($alerta->tipo_destino === 'unidad' && $alerta->unidadOrganica)
      <div class="d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:32px;height:32px;background:#ff9f43;color:#fff;font-size:14px">
          <i class="ti tabler-building" style="font-family:'tabler-icons'!important"></i>
        </div>
        <div class="overflow-hidden">
          <div class="fw-medium text-truncate" style="font-size:12px;max-width:140px">{{ $alerta->unidadOrganica->nombre }}</div>
          <span class="badge bg-label-warning" style="font-size:9px">Por unidad</span>
        </div>
      </div>
    @elseif($destinatario)
      <div class="d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
             style="width:32px;height:32px;background:var(--bs-primary);color:#fff;font-size:11px;opacity:.85">
          {{ strtoupper(substr($destinatario->name, 0, 2)) }}
        </div>
        <div class="overflow-hidden">
          <div class="fw-medium text-truncate" style="font-size:12px;max-width:140px">{{ $destinatario->name }}</div>
          <div class="text-muted text-truncate" style="font-size:10px;max-width:140px">{{ $destinatario->email }}</div>
        </div>
      </div>
    @else
      <span class="text-muted" style="font-size:12px">— Sin asignar</span>
    @endif
  </td>

  {{-- Email: botón de envío/reenvío centrado en su propia columna --}}
  <td class="text-center py-3" style="width:80px">
    @can('alertas.crear')
      @if($alerta->email_enviado && !$esAdmin)
        {{-- No-admin: email ya enviado, solo muestra ícono informativo --}}
        <span data-bs-toggle="tooltip"
          title="Email enviado el {{ $alerta->email_enviado_at?->format('d/m/Y H:i') }} a {{ $alerta->destinatario_email ?? '—' }}">
          <i class="ti tabler-mail-check" style="color:#28c76f;font-size:20px;font-family:'tabler-icons'!important"></i>
        </span>
      @else
        {{-- Admin o no enviado: botón de envío/reenvío --}}
        <form method="POST" action="{{ route('mon-alertas.email', $alerta) }}" class="d-inline form-email-alerta">
          @csrf
          <button type="submit"
            class="btn btn-sm btn-icon {{ $alerta->email_enviado ? 'btn-label-warning' : 'btn-label-info' }}"
            data-bs-toggle="tooltip"
            title="{{ $alerta->email_enviado
              ? 'Reenviar email (ya enviado el '.$alerta->email_enviado_at?->format('d/m/Y H:i').')'
              : ($destinatario ? 'Enviar email a '.$destinatario->email : 'Sin destinatario asignado') }}">
            <i class="ti {{ $alerta->email_enviado ? 'tabler-mail-forward' : 'tabler-mail-share' }} icon-16px" style="font-family:'tabler-icons'!important"></i>
          </button>
        </form>
      @endif
    @else
      {{-- Sin permiso crear: solo indicador visual --}}
      @if($alerta->email_enviado)
        <span data-bs-toggle="tooltip"
          title="Enviado el {{ $alerta->email_enviado_at?->format('d/m/Y H:i') }}">
          <i class="ti tabler-mail-check" style="color:#28c76f;font-size:20px;font-family:'tabler-icons'!important"></i>
        </span>
      @else
        <span data-bs-toggle="tooltip" title="Email no enviado">
          <i class="ti tabler-mail-off" style="color:#b0b0b0;font-size:20px;font-family:'tabler-icons'!important"></i>
        </span>
      @endif
    @endcan
  </td>

  {{-- Creado --}}
  <td class="py-3" style="width:110px">
    <div style="font-size:12px" class="fw-medium">{{ $alerta->created_at->format('d M Y') }}</div>
    <div class="text-muted" style="font-size:10px">{{ $alerta->created_at->diffForHumans() }}</div>
  </td>

  {{-- Acciones: Ver | Editar | Marcar leída | Eliminar --}}
  <td class="text-end pe-3 py-3" style="width:140px">
    <div class="d-flex align-items-center justify-content-end gap-1">

      {{-- Ver detalle --}}
      <button type="button" class="btn btn-sm btn-icon btn-label-secondary btn-ver-alerta"
        data-bs-toggle="tooltip" title="Ver detalle"
        data-id="{{ $alerta->id }}"
        data-titulo="{{ e($alerta->titulo) }}"
        data-mensaje="{{ e($alerta->mensaje) }}"
        data-prioridad="{{ $alerta->prioridad }}"
        data-prioridad-label="{{ $prioColor['text'] }}"
        data-prioridad-color="{{ $prioColor['label'] }}"
        data-tipo="{{ $tipoLabel }}"
        data-modulo="{{ strtoupper($alerta->modulo) }}"
        data-destinatario="{{ $destinatario?->name ?? '—' }}"
        data-email-dest="{{ $destinatario?->email ?? '—' }}"
        data-email-enviado="{{ $alerta->email_enviado ? 'Sí, el '.$alerta->email_enviado_at?->format('d/m/Y H:i').' a '.($alerta->destinatario_email ?? '—') : 'No' }}"
        data-estado="{{ $isLeida ? 'Resuelta el '.$alerta->leida_at?->format('d/m/Y H:i') : 'Pendiente' }}"
        data-creado="{{ $alerta->created_at->format('d/m/Y H:i') }}"
        data-actividad="{{ $alerta->actividad?->nombre ?? '' }}">
        <i class="ti tabler-eye icon-14px" style="font-family:'tabler-icons'!important"></i>
      </button>

      {{-- Editar --}}
      @can('alertas.crear')
      <button type="button" class="btn btn-sm btn-icon btn-label-primary btn-editar-alerta"
        data-bs-toggle="tooltip" title="Editar"
        data-id="{{ $alerta->id }}"
        data-url="{{ route('mon-alertas.update', $alerta) }}"
        data-titulo="{{ e($alerta->titulo) }}"
        data-mensaje="{{ e($alerta->mensaje) }}"
        data-prioridad="{{ $alerta->prioridad }}"
        data-tipo="{{ $alerta->tipo }}"
        data-modulo="{{ $alerta->modulo }}"
        data-usuario-id="{{ $alerta->usuario_id }}"
        data-actividad-id="{{ $alerta->actividad_id ?? '' }}">
        <i class="ti tabler-edit icon-14px" style="font-family:'tabler-icons'!important"></i>
      </button>
      @endcan

      {{-- Marcar leída --}}
      @can('alertas.ver')
      @if(!$isLeida)
      <form method="POST" action="{{ route('mon-alertas.leer', $alerta) }}" class="d-inline form-marcar-leida">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-sm btn-icon btn-label-success"
          data-bs-toggle="tooltip" title="Marcar como resuelta">
          <i class="ti tabler-check icon-14px" style="font-family:'tabler-icons'!important"></i>
        </button>
      </form>
      @endif
      @endcan

      {{-- Eliminar --}}
      @can('alertas.eliminar')
      <form method="POST" action="{{ route('mon-alertas.destroy', $alerta) }}" class="d-inline form-eliminar-alerta"
        data-titulo="{{ e($alerta->titulo) }}">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-icon btn-label-danger"
          data-bs-toggle="tooltip" title="Eliminar">
          <i class="ti tabler-trash icon-14px" style="font-family:'tabler-icons'!important"></i>
        </button>
      </form>
      @endcan

    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-center py-5">
    <div class="py-3">
      <div class="avatar avatar-xl bg-label-success rounded-circle mx-auto mb-3">
        <span class="avatar-initial rounded-circle bg-label-success text-success" style="font-size:28px">
          <i class="ti tabler-bell-off"></i>
        </span>
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
