<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
  body { color: #333; padding: 20px; }
  .header { border-bottom: 3px solid #696cff; padding-bottom: 10px; margin-bottom: 14px; }
  .header h1 { font-size: 18px; color: #696cff; margin-bottom: 2px; }
  .header p  { color: #666; font-size: 9px; }
  .meta { display: flex; gap: 24px; margin-bottom: 14px; background:#f5f5ff; padding:8px 10px; border-radius:4px; }
  .meta span { color: #666; }
  .meta strong { color: #333; }
  table { width: 100%; border-collapse: collapse; margin-top: 6px; }
  thead th { background: #696cff; color: #fff; padding: 6px 8px; text-align: left; font-size: 9px; }
  tbody tr:nth-child(even) { background: #f8f8ff; }
  tbody td { padding: 5px 8px; border-bottom: 1px solid #e0e0e0; font-size: 9px; }
  .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: bold; }
  .badge-success { background:#d1fae5; color:#065f46; }
  .badge-warning { background:#fef3c7; color:#92400e; }
  .badge-danger  { background:#fee2e2; color:#991b1b; }
  .badge-info    { background:#dbeafe; color:#1e40af; }
  .badge-secondary{ background:#e5e7eb; color:#374151; }
  .footer { margin-top: 20px; border-top: 1px solid #e0e0e0; padding-top: 8px; color: #999; font-size: 8px; }
  .resumen { display: flex; gap: 12px; margin-bottom: 14px; }
  .resumen-card { flex: 1; border: 1px solid #e0e0e0; border-radius: 6px; padding: 8px 10px; }
  .resumen-card .val { font-size: 20px; font-weight: bold; color: #696cff; }
  .resumen-card .lbl { font-size: 8px; color: #666; }
</style>
</head>
<body>

<div class="header">
  <h1>PULSO UGEL — Reporte de Actividades</h1>
  <p>Directiva N° 006-2019-CG-INTEG / DS 148-2024-PCM &nbsp;|&nbsp; Generado: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Período: {{ $anio }}</p>
</div>

<table style="width:auto;border:none;margin-bottom:10px">
  <tr>
    @if(!empty($filtro_modulo))<td style="padding:2px 10px 2px 0;border:none"><strong>Módulo:</strong> {{ $filtro_modulo }}</td>@endif
    @if($filtro_estado)<td style="padding:2px 10px 2px 0;border:none"><strong>Estado:</strong> {{ ucfirst($filtro_estado) }}</td>@endif
    @if($filtro_unidad)<td style="padding:2px 10px 2px 0;border:none"><strong>Unidad:</strong> {{ $filtro_unidad }}</td>@endif
  </tr>
</table>

{{-- Resumen --}}
<table style="width:100%;border:none;margin-bottom:14px">
  <tr>
    @foreach([['Total','total','696cff'],['Completadas','completadas','22c55e'],['Pendientes','pendientes','f59e0b'],['Observadas','observadas','ef4444']] as $stat)
    <td style="border:1px solid #e0e0e0;border-radius:6px;padding:8px 12px;text-align:center;width:25%">
      <div style="font-size:22px;font-weight:bold;color:#{{ $stat[2] }}">{{ $stats[$stat[1]] }}</div>
      <div style="font-size:8px;color:#666">{{ $stat[0] }}</div>
    </td>
    @endforeach
  </tr>
</table>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Actividad</th>
      <th>Módulo</th>
      <th>Unidad</th>
      <th>Responsable</th>
      <th>Estado</th>
      <th>Prioridad</th>
      <th>F. Límite</th>
      <th>Avance</th>
    </tr>
  </thead>
  <tbody>
    @forelse($actividades as $i => $a)
    @php
      $eb = match($a->estado) { 'completada'=>'success','pendiente'=>'secondary','en_proceso'=>'info','observado'=>'warning',default=>'danger'};
      $pb = match($a->prioridad??'') { 'alta'=>'danger','media'=>'warning','baja'=>'info',default=>'secondary'};
    @endphp
    <tr>
      <td style="color:#999">{{ $i + 1 }}</td>
      <td>{{ $a->nombre }}</td>
      <td>{{ $a->modulo === 'sci' ? 'SCI' : 'Integridad' }}</td>
      <td>{{ $a->unidadOrganica->nombre ?? '—' }}</td>
      <td>{{ $a->responsables->pluck('name')->implode(', ') ?: '—' }}</td>
      <td><span class="badge badge-{{ $eb }}">{{ ucfirst($a->estado) }}</span></td>
      <td>@if($a->prioridad)<span class="badge badge-{{ $pb }}">{{ ucfirst($a->prioridad) }}</span>@else —@endif</td>
      <td>{{ $a->fecha_limite?->format('d/m/Y') ?? '—' }}</td>
      <td>{{ $a->porcentaje_avance }}%</td>
    </tr>
    @empty
    <tr><td colspan="9" style="text-align:center;padding:20px;color:#999">Sin actividades para los filtros seleccionados.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  PULSO UGEL &nbsp;|&nbsp; {{ config('app.name') }} &nbsp;|&nbsp; Total: {{ count($actividades) }} registros &nbsp;|&nbsp; Página 1
</div>

</body>
</html>
