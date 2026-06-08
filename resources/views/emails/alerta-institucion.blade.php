<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alerta PULSO UGEL</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background:#f0f2f5; color:#333; }
  .wrapper { max-width:600px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
  .header { background:linear-gradient(135deg,#696cff,#9c9eff); padding:32px 40px; text-align:center; }
  .header-icon { font-size:48px; margin-bottom:8px; display:block; }
  .header h1 { color:#fff; font-size:22px; font-weight:700; margin-bottom:4px; }
  .header p { color:rgba(255,255,255,.85); font-size:13px; }
  .borde-prioridad { height:5px; background:{{ $colorBorde }}; }
  .body { padding:32px 40px; }
  .badge-prioridad { display:inline-block; padding:4px 14px; border-radius:50px; font-size:11px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#fff; background:{{ $colorBorde }}; margin-bottom:16px; }
  .titulo-alerta { font-size:18px; font-weight:700; color:#2c2c3e; margin-bottom:8px; }
  .mensaje-alerta { font-size:15px; color:#555; line-height:1.6; margin-bottom:24px; }
  .card-actividad { background:#f8f9ff; border:1px solid #e8eaff; border-radius:8px; padding:18px 20px; margin-bottom:24px; }
  .card-actividad h3 { font-size:13px; color:#696cff; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px; }
  .dato-fila { display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid #eef0ff; font-size:13px; }
  .dato-fila:last-child { border-bottom:none; }
  .dato-label { color:#888; }
  .dato-valor { font-weight:600; color:#2c2c3e; }
  .estado-badge { padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600; }
  .estado-vencida    { background:#fde8e8; color:#ea5455; }
  .estado-en_proceso { background:#fff4e0; color:#ff9f43; }
  .estado-pendiente  { background:#eef0ff; color:#696cff; }
  .estado-completada { background:#e8f8ee; color:#28c76f; }
  .avance-bar { background:#e9ecef; border-radius:4px; height:8px; overflow:hidden; margin-top:4px; }
  .avance-fill { height:8px; border-radius:4px; background:{{ $colorBorde }}; }
  .btn-cta { display:block; text-align:center; background:#696cff; color:#fff !important; text-decoration:none; padding:14px 32px; border-radius:8px; font-weight:700; font-size:15px; margin-bottom:12px; }
  .btn-secondary { display:block; text-align:center; background:#f0f2f5; color:#555 !important; text-decoration:none; padding:11px 32px; border-radius:8px; font-weight:600; font-size:14px; }
  .footer { background:#f8f9ff; padding:20px 40px; text-align:center; border-top:1px solid #eef0ff; }
  .footer p { font-size:12px; color:#aaa; line-height:1.6; }
  .footer strong { color:#696cff; }
  @media (max-width:600px) {
    .body, .header, .footer { padding:24px 20px !important; }
    .dato-fila { flex-direction:column; align-items:flex-start; gap:2px; }
  }
</style>
</head>
<body>
<div class="wrapper">

  {{-- Encabezado --}}
  <div class="header">
    <span class="header-icon">{{ $iconoTexto }}</span>
    <h1>Sistema PULSO UGEL</h1>
    <p>UGEL Huacaybamba · Control Interno y Modelo de Integridad</p>
  </div>

  <div class="borde-prioridad"></div>

  {{-- Cuerpo --}}
  <div class="body">

    <span class="badge-prioridad">{{ $prioridadLabel }}</span>
    <div class="titulo-alerta">{{ $alerta->titulo }}</div>
    <p class="mensaje-alerta">{{ $alerta->mensaje }}</p>

    {{-- Datos de la actividad --}}
    @if($actividad)
    <div class="card-actividad">
      <h3>📋 Detalle de la Actividad</h3>
      <div class="dato-fila">
        <span class="dato-label">Actividad</span>
        <span class="dato-valor">{{ $actividad->nombre }}</span>
      </div>
      @if($actividad->codigo)
      <div class="dato-fila">
        <span class="dato-label">Código</span>
        <span class="dato-valor">{{ $actividad->codigo }}</span>
      </div>
      @endif
      <div class="dato-fila">
        <span class="dato-label">Unidad</span>
        <span class="dato-valor">{{ $unidad }}</span>
      </div>
      <div class="dato-fila">
        <span class="dato-label">Fecha límite</span>
        <span class="dato-valor">{{ $actividad->fecha_limite?->format('d/m/Y') ?? '—' }}</span>
      </div>
      <div class="dato-fila">
        <span class="dato-label">Estado</span>
        <span class="estado-badge estado-{{ $actividad->estado }}">{{ $actividad->estado_label }}</span>
      </div>
      <div class="dato-fila" style="flex-direction:column;align-items:flex-start;gap:4px">
        <span class="dato-label">Avance actual</span>
        <div style="width:100%">
          <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
            <span class="dato-valor">{{ $actividad->avance }}%</span>
          </div>
          <div class="avance-bar"><div class="avance-fill" style="width:{{ $actividad->avance }}%"></div></div>
        </div>
      </div>
    </div>
    @endif

    {{-- Acciones --}}
    <a href="{{ $urlSistema }}" class="btn-cta">Ver mis actividades en PULSO UGEL</a>
    <a href="{{ $urlAlertas }}" class="btn-secondary">Ver todas las alertas</a>
  </div>

  {{-- Footer --}}
  <div class="footer">
    <p>
      Este mensaje fue generado automáticamente por <strong>PULSO UGEL</strong>.<br>
      Por favor, no responda a este correo.<br>
      UGEL Huacaybamba · Dirección Regional de Educación Huánuco
    </p>
    <p style="margin-top:8px;font-size:11px;color:#ccc">{{ now()->format('d/m/Y H:i') }} — Hora de Lima (UTC-5)</p>
  </div>

</div>
</body>
</html>
