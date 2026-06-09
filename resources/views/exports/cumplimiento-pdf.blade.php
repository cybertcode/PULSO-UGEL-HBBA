<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size:10px; color:#333; }
  .header { background:#696cff; color:#fff; padding:14px 20px; margin-bottom:12px; }
  .header h1 { font-size:15px; font-weight:700; }
  .header p  { font-size:9px; opacity:.85; margin-top:2px; }
  .meta { display:flex; justify-content:space-between; padding:0 20px 10px; font-size:9px; color:#666; }
  .kpis { display:flex; gap:10px; padding:0 20px 12px; }
  .kpi  { flex:1; border:1px solid #e0e0ff; border-radius:6px; padding:8px 10px; text-align:center; }
  .kpi strong { display:block; font-size:18px; margin-bottom:2px; }
  .kpi span   { font-size:9px; color:#888; }
  .kpi.rojo   strong { color:#ea5455; }
  .kpi.verde  strong { color:#28c76f; }
  .kpi.naranja strong { color:#ff9f43; }
  .kpi.azul   strong { color:#696cff; }
  h2 { font-size:11px; font-weight:700; padding:6px 20px; background:#f5f5ff; border-left:4px solid #696cff; margin-bottom:0; }
  table { width:100%; border-collapse:collapse; font-size:9px; }
  th { background:#696cff; color:#fff; padding:6px 8px; text-align:left; font-weight:700; }
  td { padding:5px 8px; border-bottom:1px solid #eee; vertical-align:middle; }
  tr:nth-child(even) td { background:#fafafa; }
  .badge { display:inline-block; padding:2px 6px; border-radius:10px; font-size:8px; font-weight:700; }
  .badge-success { background:#e8f8ee; color:#28c76f; }
  .badge-danger  { background:#fde8e8; color:#ea5455; }
  .badge-warning { background:#fff4e0; color:#ff9f43; }
  .semaforo-bar { background:#e9ecef; border-radius:3px; height:6px; width:80px; display:inline-block; vertical-align:middle; }
  .semaforo-fill { height:6px; border-radius:3px; display:inline-block; }
  .page-break { page-break-after: always; }
  h2.second { border-left-color:#ff9f43; }
  th.second { background:#ff9f43; }
  .footer { position:fixed; bottom:0; left:0; right:0; padding:6px 20px; font-size:8px; color:#aaa; border-top:1px solid #eee; display:flex; justify-content:space-between; }
</style>
</head>
<body>

<div class="header">
  <h1>PULSO UGEL — Reporte de Cumplimiento · {{ $anio }}</h1>
  <p>Generado el {{ now()->format('d/m/Y H:i') }} · UGEL Huacaybamba · Área de Control Interno</p>
</div>

<div class="meta">
  <span>Período: {{ $anio }}</span>
  @if($filtro_unidad)<span>Unidad: {{ $filtro_unidad }}</span>@endif
  @if($filtro_componente)<span>Componente: {{ $filtro_componente }}</span>@endif
</div>

{{-- KPIs --}}
<div class="kpis">
  <div class="kpi azul">  <strong>{{ $totales['responsables'] }}</strong><span>Responsables</span></div>
  <div class="kpi rojo">  <strong>{{ $totales['en_riesgo'] }}</strong><span>En riesgo</span></div>
  <div class="kpi naranja"><strong>{{ $totales['sin_evidencia'] }}</strong><span>Sin evidencia</span></div>
  <div class="kpi rojo">  <strong>{{ $totales['vencidas_total'] }}</strong><span>Vencidas</span></div>
</div>

{{-- HOJA 1: Por Responsable --}}
<h2>Cumplimiento por Responsable</h2>
<table>
  <thead>
    <tr>
      <th>#</th><th>Responsable</th><th>Unidad</th>
      <th>Total</th><th>Completadas</th><th>Vencidas</th>
      <th>Sin Evidencia</th><th>Cumplimiento</th><th>Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($responsables as $i => $u)
    @php
      $color = $u->stat_porcentaje >= 75 ? '#28c76f' : ($u->stat_porcentaje >= 50 ? '#ff9f43' : '#ea5455');
      $badgeClass = $u->stat_porcentaje >= 75 ? 'badge-success' : ($u->stat_porcentaje >= 50 ? 'badge-warning' : 'badge-danger');
      $label = $u->stat_porcentaje >= 75 ? 'Al día' : ($u->stat_porcentaje >= 50 ? 'En proceso' : 'En riesgo');
    @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td><strong>{{ $u->name }}</strong><br><span style="color:#888;font-size:8px">{{ $u->cargo?->nombre ?? '—' }}</span></td>
      <td>{{ $u->unidadOrganica?->sigla ?? '—' }}</td>
      <td style="text-align:center">{{ $u->stat_total }}</td>
      <td style="text-align:center;color:#28c76f;font-weight:700">{{ $u->stat_completadas }}</td>
      <td style="text-align:center;color:{{ $u->stat_vencidas > 0 ? '#ea5455' : '#888' }};font-weight:{{ $u->stat_vencidas > 0 ? '700' : '400' }}">{{ $u->stat_vencidas }}</td>
      <td style="text-align:center;color:{{ $u->stat_sin_evidencia > 0 ? '#ff9f43' : '#888' }}">{{ $u->stat_sin_evidencia }}</td>
      <td>
        <div style="display:flex;align-items:center;gap:4px">
          <span class="semaforo-bar"><span class="semaforo-fill" style="width:{{ $u->stat_porcentaje }}%;background:{{ $color }}"></span></span>
          <strong style="color:{{ $color }}">{{ $u->stat_porcentaje }}%</strong>
        </div>
      </td>
      <td><span class="badge {{ $badgeClass }}">{{ $label }}</span></td>
    </tr>
    @endforeach
  </tbody>
</table>

@if($sinEvidencia->count() > 0)
<div class="page-break"></div>

{{-- HOJA 2: Sin Evidencia --}}
<div class="header" style="background:#ff9f43">
  <h1>PULSO UGEL — Actividades sin Evidencia · {{ $anio }}</h1>
  <p>Total: {{ $sinEvidencia->count() }} actividad(es) sin documentar</p>
</div>

<h2 class="second">Actividades sin Evidencia</h2>
<table>
  <thead>
    <tr style="background:#ff9f43">
      <th class="second">#</th><th>Actividad</th><th>Unidad</th>
      <th>Responsable</th><th>Estado</th><th>Avance</th>
      <th>Vence</th><th>Retraso</th>
    </tr>
  </thead>
  <tbody>
    @foreach($sinEvidencia as $i => $act)
    @php
      $ec = match($act->estado) { 'vencida'=>'badge-danger', 'observado'=>'badge-warning', default=>'badge-warning' };
      $retraso = $act->fecha_limite && $act->fecha_limite->lt(now()) ? (int) round(now()->diffInDays($act->fecha_limite)) : null;
    @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td style="max-width:160px">{{ \Illuminate\Support\Str::limit($act->nombre, 55) }}</td>
      <td>{{ $act->unidadOrganica?->sigla ?? '—' }}</td>
      <td>{{ \Illuminate\Support\Str::limit($act->responsables->first()?->name ?? '—', 20) }}</td>
      <td><span class="badge {{ $ec }}">{{ $act->estado_label }}</span></td>
      <td style="text-align:center">{{ $act->avance }}%</td>
      <td>{{ $act->fecha_limite?->format('d/m/Y') ?? '—' }}</td>
      <td style="color:{{ $retraso ? '#ea5455' : '#888' }};font-weight:{{ $retraso ? '700' : '400' }}">
        {{ $retraso ? '+' . $retraso . 'd' : '—' }}
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endif

<div class="footer">
  <span>PULSO UGEL — Sistema de Control Interno · UGEL Huacaybamba</span>
  <span>{{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
