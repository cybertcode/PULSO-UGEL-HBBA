<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Resultados — {{ $encuesta->titulo }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 9.5pt;
    color: #2d2b45;
    background: #fff;
    line-height: 1.45;
  }

  /* ── PORTADA / HEADER ── */
  .header {
    background: linear-gradient(135deg, #696cff 0%, #7c3aed 100%);
    color: #fff;
    padding: 28px 32px 22px;
    margin-bottom: 0;
  }
  .header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 14px;
  }
  .header-org { font-size: 8pt; opacity: .8; margin-bottom: 3px; }
  .header-title { font-size: 17pt; font-weight: 700; line-height: 1.2; }
  .header-sub { font-size: 8.5pt; opacity: .85; margin-top: 4px; }
  .header-meta {
    text-align: right;
    font-size: 8pt;
    opacity: .85;
    line-height: 1.7;
  }

  /* KPI strip */
  .kpi-strip {
    background: #f4f3ff;
    border-left: 4px solid #696cff;
    padding: 10px 32px;
    display: flex;
    gap: 0;
  }
  .kpi-item {
    flex: 1;
    text-align: center;
    border-right: 1px solid #ddd;
    padding: 4px 0;
  }
  .kpi-item:last-child { border-right: none; }
  .kpi-num { font-size: 18pt; font-weight: 900; color: #696cff; line-height: 1; }
  .kpi-lbl { font-size: 7.5pt; color: #666; margin-top: 2px; text-transform: uppercase; letter-spacing: .04em; }

  .kpi-num.green  { color: #28c76f; }
  .kpi-num.orange { color: #ff9f43; }
  .kpi-num.red    { color: #ea5455; }

  /* ── SECCIÓN TÍTULO ── */
  .section-title {
    font-size: 11pt;
    font-weight: 700;
    color: #696cff;
    border-bottom: 2px solid #696cff;
    padding-bottom: 4px;
    margin: 22px 32px 14px;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .section-badge {
    display: inline-block;
    background: #696cff;
    color: #fff;
    font-size: 7pt;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
    margin-left: 6px;
  }

  /* ── PREGUNTA CARD ── */
  .pq-wrap { margin: 0 32px 16px; }
  .pq-card {
    border: 1px solid #e8e6f9;
    border-radius: 8px;
    overflow: hidden;
    page-break-inside: avoid;
  }
  .pq-head {
    background: linear-gradient(135deg, #f8f7ff, #f0eeff);
    padding: 9px 14px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    border-bottom: 1px solid #ece9ff;
  }
  .pq-num {
    min-width: 26px;
    height: 26px;
    background: linear-gradient(135deg, #696cff, #9b59b6);
    color: #fff;
    font-weight: 800;
    font-size: 9pt;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .pq-texto { font-size: 9.5pt; font-weight: 600; color: #2d2b45; flex: 1; }
  .pq-tipo {
    font-size: 7pt;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
    white-space: nowrap;
    flex-shrink: 0;
  }
  .tipo-opcion_multiple    { background: #dbeafe; color: #1d4ed8; }
  .tipo-seleccion_multiple { background: #e0f2fe; color: #0369a1; }
  .tipo-escala             { background: #fef9c3; color: #a16207; }
  .tipo-si_no              { background: #dcfce7; color: #166534; }
  .tipo-verdadero_falso    { background: #dbeafe; color: #1e40af; }
  .tipo-desplegable        { background: #f3e8ff; color: #6d28d9; }
  .tipo-texto_libre        { background: #f1f5f9; color: #475569; }

  .pq-body { padding: 12px 14px; }

  /* Tabla de opciones */
  .opts-table { width: 100%; border-collapse: collapse; }
  .opts-table th {
    font-size: 7.5pt;
    font-weight: 700;
    text-transform: uppercase;
    color: #888;
    letter-spacing: .04em;
    border-bottom: 1px solid #e0ddff;
    padding: 4px 8px;
    text-align: left;
  }
  .opts-table td {
    padding: 5px 8px;
    font-size: 8.5pt;
    border-bottom: 1px solid #f5f4fe;
  }
  .opts-table tr:last-child td { border-bottom: none; }
  .opts-table tr:nth-child(even) td { background: #fafafe; }

  /* Barra de progreso en tabla */
  .bar-wrap {
    background: #f0eeff;
    border-radius: 20px;
    height: 9px;
    overflow: hidden;
    width: 100%;
    min-width: 60px;
  }
  .bar-fill {
    height: 100%;
    border-radius: 20px;
    background: linear-gradient(90deg, #696cff, #9b59b6);
  }
  .bar-fill.green  { background: linear-gradient(90deg, #28c76f, #48da89); }
  .bar-fill.orange { background: linear-gradient(90deg, #ff9f43, #ffb976); }
  .bar-fill.red    { background: linear-gradient(90deg, #ea5455, #f27474); }
  .bar-fill.blue   { background: linear-gradient(90deg, #4169e1, #6495ed); }

  .cnt-badge {
    display: inline-block;
    background: #696cff;
    color: #fff;
    font-size: 7.5pt;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
    min-width: 28px;
    text-align: center;
  }
  .pct-text { font-size: 8pt; font-weight: 700; color: #696cff; }

  /* Escala cards en línea */
  .escala-row { display: flex; gap: 6px; margin-bottom: 10px; }
  .ec { flex: 1; text-align: center; border-radius: 7px; padding: 6px 4px; }
  .ec-num { font-size: 13pt; font-weight: 900; line-height: 1; }
  .ec-lbl { font-size: 6.5pt; font-weight: 700; text-transform: uppercase; margin-top: 2px; }
  .ec-cnt { font-size: 7pt; margin-top: 1px; opacity: .8; }
  .ec-1 { background: #fee2e2; color: #991b1b; }
  .ec-2 { background: #ffedd5; color: #9a3412; }
  .ec-3 { background: #fef9c3; color: #854d0e; }
  .ec-4 { background: #dcfce7; color: #166534; }
  .ec-5 { background: #dbeafe; color: #1e40af; }

  .promedio-box {
    background: #f4f3ff;
    border: 1.5px solid #d8d5ff;
    border-radius: 8px;
    padding: 8px 14px;
    text-align: center;
    margin-top: 8px;
  }
  .promedio-big { font-size: 22pt; font-weight: 900; color: #696cff; line-height: 1; }
  .promedio-sub { font-size: 7.5pt; color: #888; margin-top: 2px; }
  .stars { font-size: 12pt; color: #ffab00; margin: 3px 0; }

  /* Binario */
  .bin-row { display: flex; gap: 10px; margin-bottom: 8px; }
  .bin-card {
    flex: 1; border-radius: 8px; padding: 10px 12px;
    text-align: center; color: #fff;
  }
  .bin-num { font-size: 18pt; font-weight: 900; line-height: 1; }
  .bin-pct { font-size: 8pt; font-weight: 700; opacity: .9; margin-top: 2px; }
  .bin-lbl { font-size: 7.5pt; font-weight: 700; margin-top: 3px; }
  .bin-si   { background: linear-gradient(135deg,#28c76f,#48da89); }
  .bin-no   { background: linear-gradient(135deg,#ea5455,#f27474); }
  .bin-verd { background: linear-gradient(135deg,#4169e1,#6495ed); }
  .bin-fals { background: linear-gradient(135deg,#ff9f43,#ffb976); }

  /* Texto libre */
  .resp-bubble {
    background: #f8f7fe;
    border-left: 3px solid #696cff;
    border-radius: 0 6px 6px 0;
    padding: 7px 10px;
    margin-bottom: 6px;
    page-break-inside: avoid;
  }
  .resp-meta { display: flex; justify-content: space-between; margin-bottom: 3px; }
  .resp-autor { font-size: 7.5pt; font-weight: 700; color: #696cff; }
  .resp-fecha { font-size: 7pt; color: #bbb; }
  .resp-text  { font-size: 8.5pt; color: #333; }

  /* ── PARTICIPANTES ── */
  .part-table { width: 100%; border-collapse: collapse; }
  .part-table th {
    font-size: 7.5pt;
    font-weight: 700;
    text-transform: uppercase;
    color: #fff;
    background: #696cff;
    padding: 6px 10px;
    text-align: left;
    letter-spacing: .04em;
  }
  .part-table td {
    padding: 5px 10px;
    font-size: 8.5pt;
    border-bottom: 1px solid #f0eeff;
    vertical-align: middle;
  }
  .part-table tr:nth-child(even) td { background: #fafafe; }
  .part-table tr:last-child td { border-bottom: none; }

  .badge-comp   { background: #d4f5e2; color: #1a7a45; font-size: 7pt; font-weight: 700; padding: 2px 8px; border-radius: 10px; }
  .badge-pend   { background: #fff0e0; color: #b05c00; font-size: 7pt; font-weight: 700; padding: 2px 8px; border-radius: 10px; }
  .badge-prog   { background: #fff4e5; color: #d07000; font-size: 7pt; font-weight: 700; padding: 2px 8px; border-radius: 10px; }

  .pend-table { width: 100%; border-collapse: collapse; }
  .pend-table th {
    font-size: 7.5pt;
    font-weight: 700;
    text-transform: uppercase;
    color: #fff;
    background: #ff9f43;
    padding: 6px 10px;
    text-align: left;
    letter-spacing: .04em;
  }
  .pend-table td {
    padding: 5px 10px;
    font-size: 8.5pt;
    border-bottom: 1px solid #fff3e6;
    vertical-align: middle;
  }
  .pend-table tr:nth-child(even) td { background: #fffaf5; }
  .pend-table tr:last-child td { border-bottom: none; }

  /* ── FOOTER ── */
  .footer {
    margin-top: 28px;
    padding: 10px 32px;
    border-top: 1px solid #e8e6f9;
    display: flex;
    justify-content: space-between;
    font-size: 7.5pt;
    color: #aaa;
  }

  /* ── SIN DATOS ── */
  .empty-box {
    text-align: center;
    padding: 14px;
    color: #bbb;
    font-size: 8.5pt;
    font-style: italic;
  }

  /* ── SEPARADOR ── */
  .divider { height: 1px; background: #f0eeff; margin: 4px 32px; }

  page { page-break-after: always; }
</style>
</head>
<body>

{{-- ══ ENCABEZADO ══ --}}
<div class="header">
  <div class="header-top">
    <div>
      <div class="header-org">UGEL — Sistema PULSO · Módulo de Encuestas</div>
      <div class="header-title">{{ $encuesta->titulo }}</div>
      @if($encuesta->descripcion)
        <div class="header-sub">{{ $encuesta->descripcion }}</div>
      @endif
    </div>
    <div class="header-meta">
      @php
        $mLabels = ['sci'=>'SCI','integridad'=>'Integridad','ambos'=>'SCI + Integridad'];
        $eLabels = ['publicada'=>'Publicada','cerrada'=>'Cerrada','borrador'=>'Borrador','archivada'=>'Archivada'];
      @endphp
      <div><strong>Módulo:</strong> {{ $mLabels[$encuesta->modulo] ?? $encuesta->modulo }}</div>
      <div><strong>Estado:</strong> {{ $eLabels[$encuesta->estado] ?? $encuesta->estado }}</div>
      @if($encuesta->fecha_inicio)
        <div><strong>Período:</strong> {{ $encuesta->fecha_inicio->format('d/m/Y') }} — {{ $encuesta->fecha_fin?->format('d/m/Y') ?? 'indefinido' }}</div>
      @endif
      <div><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</div>
    </div>
  </div>
</div>

{{-- ══ KPIs ══ --}}
@php
  $totalDest  = count($respondieron) + count($pendientes);
  $totalComp  = collect($respondieron)->where('completada', true)->count();
  $totalPend  = count($pendientes);
  $pct        = $totalDest > 0 ? round($totalComp / $totalDest * 100, 1) : 0;
  $pctColor   = $pct >= 80 ? 'green' : ($pct >= 40 ? 'orange' : 'red');
@endphp
<div class="kpi-strip">
  <div class="kpi-item">
    <div class="kpi-num">{{ $totalDest }}</div>
    <div class="kpi-lbl">Destinatarios</div>
  </div>
  <div class="kpi-item">
    <div class="kpi-num green">{{ $totalComp }}</div>
    <div class="kpi-lbl">Completaron</div>
  </div>
  <div class="kpi-item">
    <div class="kpi-num orange">{{ $totalPend }}</div>
    <div class="kpi-lbl">Pendientes</div>
  </div>
  <div class="kpi-item">
    <div class="kpi-num {{ $pctColor }}">{{ $pct }}%</div>
    <div class="kpi-lbl">Participación</div>
  </div>
  <div class="kpi-item">
    <div class="kpi-num">{{ count($preguntas) }}</div>
    <div class="kpi-lbl">Preguntas</div>
  </div>
</div>

{{-- ══ RESULTADOS POR PREGUNTA ══ --}}
<div class="section-title">
  Resultados por Pregunta
  <span class="section-badge">{{ count($preguntas) }}</span>
</div>

@foreach($preguntas as $idx => $pq)
@php
  $tipoLabels = [
    'opcion_multiple'    => 'Opción múltiple',
    'seleccion_multiple' => 'Selección múltiple',
    'escala'             => 'Escala 1–5',
    'si_no'              => 'Sí / No',
    'verdadero_falso'    => 'Verdadero / Falso',
    'desplegable'        => 'Lista desplegable',
    'texto_libre'        => 'Texto libre',
  ];
  $totalResp = array_sum($pq['data'] ?? []);
@endphp
<div class="pq-wrap">
  <div class="pq-card">
    <div class="pq-head">
      <div class="pq-num">{{ $idx + 1 }}</div>
      <div class="pq-texto">{{ $pq['texto'] }}</div>
      <span class="pq-tipo tipo-{{ $pq['tipo'] }}">{{ $tipoLabels[$pq['tipo']] ?? $pq['tipo'] }}</span>
    </div>
    <div class="pq-body">

      {{-- Opción múltiple / Selección múltiple / Desplegable --}}
      @if(in_array($pq['tipo'], ['opcion_multiple','seleccion_multiple','desplegable']))
        @if($totalResp > 0)
          <table class="opts-table">
            <thead>
              <tr>
                <th style="width:35%">Opción</th>
                <th style="width:35%">Distribución</th>
                <th style="width:10%;text-align:center">Votos</th>
                <th style="width:10%;text-align:right">%</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pq['labels'] as $i => $lbl)
              @php
                $cnt  = $pq['data'][$i] ?? 0;
                $pct2 = $totalResp > 0 ? round($cnt / $totalResp * 100, 1) : 0;
                $palColors = ['#696cff','#03c3ec','#71dd37','#ffab00','#ff3e1d','#20c997','#fd7e14','#6f42c1'];
                $barColor  = $palColors[$i % count($palColors)];
              @endphp
              <tr>
                <td>{{ $lbl }}</td>
                <td>
                  <div class="bar-wrap">
                    <div class="bar-fill" style="width:{{ $pct2 }}%;background:{{ $barColor }}"></div>
                  </div>
                </td>
                <td style="text-align:center">
                  <span class="cnt-badge" style="background:{{ $barColor }}">{{ $cnt }}</span>
                </td>
                <td style="text-align:right">
                  <span class="pct-text" style="color:{{ $barColor }}">{{ $pct2 }}%</span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div style="margin-top:6px;font-size:7.5pt;color:#888;text-align:right">
            Total: {{ $totalResp }} respuesta{{ $totalResp !== 1 ? 's' : '' }}
          </div>
        @else
          <div class="empty-box">Sin respuestas registradas</div>
        @endif

      {{-- Escala 1-5 --}}
      @elseif($pq['tipo'] === 'escala')
        @php
          $etqs  = ['Muy malo','Malo','Regular','Bueno','Muy bueno'];
          $eclss = ['ec-1','ec-2','ec-3','ec-4','ec-5'];
          $totalEsc = array_sum($pq['data']);
          $prom  = $pq['promedio'] ?? 0;
          $stars = round($prom);
        @endphp
        @if($totalEsc > 0)
          <div class="escala-row">
            @foreach($pq['labels'] as $i => $v)
            <div class="ec {{ $eclss[$i] }}">
              <div class="ec-num">{{ $v }}</div>
              <div class="ec-lbl">{{ $etqs[$i] }}</div>
              <div class="ec-cnt">{{ $pq['data'][$i] }} resp.</div>
            </div>
            @endforeach
          </div>
          <table class="opts-table">
            <thead>
              <tr>
                <th>Valor</th>
                <th>Etiqueta</th>
                <th style="width:40%">Distribución</th>
                <th style="width:8%;text-align:center">N°</th>
                <th style="width:8%;text-align:right">%</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pq['labels'] as $i => $v)
              @php
                $cnt  = $pq['data'][$i];
                $pct2 = $totalEsc > 0 ? round($cnt / $totalEsc * 100, 1) : 0;
                $escalaCols = ['#ea5455','#fd7e14','#ffab00','#71dd37','#696cff'];
              @endphp
              <tr>
                <td style="font-weight:700;font-size:10pt">{{ $v }}</td>
                <td>{{ $etqs[$i] }}</td>
                <td><div class="bar-wrap"><div class="bar-fill" style="width:{{ $pct2 }}%;background:{{ $escalaCols[$i] }}"></div></div></td>
                <td style="text-align:center">{{ $cnt }}</td>
                <td style="text-align:right;font-weight:700;color:{{ $escalaCols[$i] }}">{{ $pct2 }}%</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div class="promedio-box" style="margin-top:8px">
            <div class="promedio-big">{{ $prom }}</div>
            <div class="stars">
              @for($s=1;$s<=5;$s++){{ $s <= $stars ? '★' : '☆' }}@endfor
            </div>
            <div class="promedio-sub">Promedio de {{ $totalEsc }} respuesta{{ $totalEsc !== 1 ? 's' : '' }} · Escala 1–5</div>
          </div>
        @else
          <div class="empty-box">Sin respuestas registradas</div>
        @endif

      {{-- Sí/No · Verdadero/Falso --}}
      @elseif(in_array($pq['tipo'], ['si_no','verdadero_falso']))
        @php
          $isSiNo = $pq['tipo'] === 'si_no';
          $keys   = $isSiNo ? ['si','no'] : ['verdadero','falso'];
          $lbls   = $isSiNo ? ['Sí','No'] : ['Verdadero','Falso'];
          $clss   = $isSiNo ? ['bin-si','bin-no'] : ['bin-verd','bin-fals'];
          $counts = [];
          foreach($keys as $ki => $k) $counts[$k] = $pq['data'][$ki] ?? 0;
          $totalBin = array_sum($counts);
          $pcts = [];
          foreach($keys as $k) $pcts[$k] = $totalBin > 0 ? round($counts[$k] / $totalBin * 100, 1) : 0;
          $binCols = $isSiNo ? ['#28c76f','#ea5455'] : ['#4169e1','#ff9f43'];
        @endphp
        @if($totalBin > 0)
          <div class="bin-row">
            @foreach($keys as $i => $k)
            <div class="bin-card {{ $clss[$i] }}">
              <div class="bin-num">{{ $counts[$k] }}</div>
              <div class="bin-pct">{{ $pcts[$k] }}%</div>
              <div class="bin-lbl">{{ $lbls[$i] }}</div>
            </div>
            @endforeach
          </div>
          <table class="opts-table" style="margin-top:4px">
            <tbody>
              @foreach($keys as $i => $k)
              <tr>
                <td style="font-weight:700;color:{{ $binCols[$i] }}">{{ $lbls[$i] }}</td>
                <td><div class="bar-wrap"><div class="bar-fill" style="width:{{ $pcts[$k] }}%;background:{{ $binCols[$i] }}"></div></div></td>
                <td style="text-align:center;width:50px">{{ $counts[$k] }}</td>
                <td style="text-align:right;width:50px;font-weight:700;color:{{ $binCols[$i] }}">{{ $pcts[$k] }}%</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div style="margin-top:5px;font-size:7.5pt;color:#888;text-align:center">
            Total: {{ $totalBin }} respuesta{{ $totalBin !== 1 ? 's' : '' }}
          </div>
        @else
          <div class="empty-box">Sin respuestas registradas</div>
        @endif

      {{-- Texto libre --}}
      @elseif($pq['tipo'] === 'texto_libre')
        @if(!empty($pq['respuestas']) && count($pq['respuestas']) > 0)
          <div style="margin-bottom:6px;font-size:7.5pt;color:#888">
            {{ count($pq['respuestas']) }} respuesta{{ count($pq['respuestas']) !== 1 ? 's' : '' }} registradas
          </div>
          @foreach($pq['respuestas'] as $r)
          <div class="resp-bubble">
            <div class="resp-meta">
              <span class="resp-autor">{{ $r['usuario'] }}</span>
              <span class="resp-fecha">{{ $r['fecha'] }}</span>
            </div>
            <div class="resp-text">{{ $r['respuesta'] }}</div>
          </div>
          @endforeach
        @else
          <div class="empty-box">Sin respuestas de texto registradas</div>
        @endif

      @endif
    </div>
  </div>
</div>
@endforeach

{{-- ══ PARTICIPANTES QUE RESPONDIERON ══ --}}
<div class="section-title" style="margin-top:26px">
  Participantes que Respondieron
  <span class="section-badge" style="background:#28c76f">{{ count($respondieron) }}</span>
</div>

<div style="margin: 0 32px 16px;">
  @if(count($respondieron) > 0)
  <table class="part-table">
    <thead>
      <tr>
        <th style="width:4%">#</th>
        <th style="width:8%">DNI</th>
        <th style="width:30%">Nombre completo</th>
        <th style="width:28%">Unidad orgánica</th>
        <th style="width:15%">Estado</th>
        <th style="width:15%">Completó</th>
      </tr>
    </thead>
    <tbody>
      @foreach($respondieron as $i => $p)
      <tr>
        <td style="color:#aaa;font-size:8pt">{{ $i + 1 }}</td>
        <td style="font-family:monospace">{{ $p['dni'] }}</td>
        <td style="font-weight:600">{{ $p['usuario'] }}</td>
        <td style="color:#666">{{ $p['unidad'] ?? '—' }}</td>
        <td>
          @if($p['completada'])
            <span class="badge-comp">✓ Completada</span>
          @else
            <span class="badge-prog">⏳ En progreso</span>
          @endif
        </td>
        <td style="color:#666;font-size:8pt">{{ $p['completada_at'] ?? '—' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
    <div class="empty-box">Ningún participante ha respondido aún</div>
  @endif
</div>

{{-- ══ PENDIENTES ══ --}}
<div class="section-title" style="margin-top:20px;color:#ff9f43;border-bottom-color:#ff9f43">
  Participantes Pendientes
  <span class="section-badge" style="background:#ff9f43">{{ count($pendientes) }}</span>
</div>

<div style="margin: 0 32px 16px;">
  @if(count($pendientes) > 0)
  <table class="pend-table">
    <thead>
      <tr>
        <th style="width:4%">#</th>
        <th style="width:10%">DNI</th>
        <th style="width:40%">Nombre completo</th>
        <th style="width:34%">Unidad orgánica</th>
        <th style="width:12%">Estado</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pendientes as $i => $p)
      <tr>
        <td style="color:#aaa;font-size:8pt">{{ $i + 1 }}</td>
        <td style="font-family:monospace">{{ $p['dni'] }}</td>
        <td style="font-weight:600;color:#666">{{ $p['usuario'] }}</td>
        <td style="color:#888">{{ $p['unidad'] ?? '—' }}</td>
        <td><span class="badge-pend">⏰ Pendiente</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
    <div class="empty-box" style="color:#28c76f">🎉 ¡Todos los participantes respondieron!</div>
  @endif
</div>

{{-- ══ FOOTER ══ --}}
<div class="footer">
  <span>PULSO UGEL · Sistema de Gestión Institucional</span>
  <span>Generado el {{ now()->format('d \d\e F \d\e Y, H:i') }}</span>
  <span>{{ $encuesta->titulo }}</span>
</div>

</body>
</html>
