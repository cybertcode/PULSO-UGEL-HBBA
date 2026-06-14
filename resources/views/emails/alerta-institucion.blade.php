<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alerta — {{ $instSigla ?? 'PULSO UGEL' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#eef1f7;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eef1f7;padding:32px 16px 48px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;">

        {{-- ── HEADER ── --}}
        <tr>
          <td style="background-color:#0d1b3e;border-radius:14px 14px 0 0;padding:0;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td style="padding:26px 36px 22px;">
                  <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      @if (!empty($ci?->logo_ruta))
                      <td style="width:44px;height:44px;vertical-align:middle;">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($ci->logo_ruta) }}"
                             width="44" height="44"
                             style="border-radius:10px;display:block;object-fit:cover;"
                             alt="{{ $instSigla ?? 'Logo' }}">
                      </td>
                      @else
                      <td style="width:44px;height:44px;background-color:rgba(200,149,42,0.2);border:1px solid rgba(200,149,42,0.4);border-radius:10px;text-align:center;vertical-align:middle;padding:0 10px;font-size:20px;">
                        🏛️
                      </td>
                      @endif
                      <td style="padding-left:14px;">
                        <div style="font-family:Georgia,'Times New Roman',serif;font-size:17px;font-weight:700;color:#ffffff;line-height:1.2;">{{ $instSigla ?? 'PULSO UGEL' }}</div>
                        <div style="font-size:10px;color:#d4a843;letter-spacing:1px;text-transform:uppercase;margin-top:2px;">Control Interno &amp; Integridad &bull; {{ $instNombre ?? '' }}</div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Barra de prioridad --}}
        <tr>
          <td style="height:5px;background-color:{{ $colorBorde }};font-size:0;line-height:0;">&nbsp;</td>
        </tr>

        {{-- Franja tipo de alerta --}}
        <tr>
          <td style="background-color:{{ $colorBorde }};padding:10px 36px;">
            <table cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td style="font-size:18px;vertical-align:middle;padding-right:10px;">{{ $iconoTexto }}</td>
                <td style="font-size:11px;font-weight:700;color:#ffffff;letter-spacing:1.2px;text-transform:uppercase;vertical-align:middle;">{{ $tipo }}</td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- ── BODY ── --}}
        <tr>
          <td style="background-color:#ffffff;padding:32px 36px;">

            {{-- Badge prioridad --}}
            <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
              <tr>
                <td style="background-color:{{ $colorBorde }};color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.6px;text-transform:uppercase;padding:4px 14px;border-radius:50px;">
                  {{ $prioridadLabel }}
                </td>
              </tr>
            </table>

            {{-- Título --}}
            <p style="font-family:Georgia,'Times New Roman',serif;font-size:20px;font-weight:700;color:#0d1b3e;margin:0 0 10px;line-height:1.35;">{{ $alerta->titulo }}</p>

            {{-- Mensaje --}}
            <p style="font-size:14.5px;color:#445573;line-height:1.75;margin:0 0 24px;">{{ $alerta->mensaje }}</p>

            {{-- Card actividad --}}
            @if($actividad)
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
              <tr>
                <td style="background-color:#f7f9fc;border:1px solid #dce4f0;border-left:4px solid {{ $colorBorde }};border-radius:0 10px 10px 0;padding:20px 22px;">

                  <p style="font-size:10.5px;font-weight:700;color:#8a9ab8;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;">📋 Detalle de la actividad</p>

                  {{-- Actividad --}}
                  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom:1px solid #e8eef5;padding-bottom:8px;margin-bottom:8px;">
                    <tr>
                      <td style="font-size:12.5px;color:#8a9ab8;vertical-align:top;width:40%;">Actividad</td>
                      <td style="font-size:13px;font-weight:600;color:#0d1b3e;text-align:right;">{{ $actividad->nombre }}</td>
                    </tr>
                  </table>

                  @if($actividad->codigo)
                  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom:1px solid #e8eef5;padding-bottom:8px;margin-bottom:8px;">
                    <tr>
                      <td style="font-size:12.5px;color:#8a9ab8;width:40%;">Código</td>
                      <td style="font-size:13px;font-weight:600;color:#0d1b3e;text-align:right;">{{ $actividad->codigo }}</td>
                    </tr>
                  </table>
                  @endif

                  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom:1px solid #e8eef5;padding-bottom:8px;margin-bottom:8px;">
                    <tr>
                      <td style="font-size:12.5px;color:#8a9ab8;width:40%;">Unidad orgánica</td>
                      <td style="font-size:13px;font-weight:600;color:#0d1b3e;text-align:right;">{{ $unidad }}</td>
                    </tr>
                  </table>

                  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom:1px solid #e8eef5;padding-bottom:8px;margin-bottom:8px;">
                    <tr>
                      <td style="font-size:12.5px;color:#8a9ab8;width:40%;">Fecha límite</td>
                      <td style="font-size:13px;font-weight:600;color:#0d1b3e;text-align:right;">{{ $actividad->fecha_limite?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                  </table>

                  {{-- Estado --}}
                  @php
                    $estadoStyle = match($actividad->estado) {
                      'vencida'    => 'background-color:#fde8e8;color:#c0392b;',
                      'en_proceso' => 'background-color:#fff4e0;color:#e67e22;',
                      'completada' => 'background-color:#e8f8ee;color:#1a7f4b;',
                      default      => 'background-color:#eef0ff;color:#1a3a6e;',
                    };
                  @endphp
                  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom:1px solid #e8eef5;padding-bottom:8px;margin-bottom:8px;">
                    <tr>
                      <td style="font-size:12.5px;color:#8a9ab8;width:40%;vertical-align:middle;">Estado</td>
                      <td style="text-align:right;">
                        <span style="{{ $estadoStyle }}font-size:11.5px;font-weight:600;padding:3px 10px;border-radius:20px;display:inline-block;">
                          {{ $actividad->estado_label }}
                        </span>
                      </td>
                    </tr>
                  </table>

                  {{-- Avance --}}
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td>
                        <p style="font-size:12.5px;color:#8a9ab8;margin:0 0 8px;">Avance actual</p>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:4px;">
                          <tr>
                            <td style="font-size:13px;font-weight:700;color:#0d1b3e;">{{ $actividad->avance }}%</td>
                            <td style="font-size:11px;color:#8a9ab8;text-align:right;">completado</td>
                          </tr>
                        </table>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#e0e7f0;border-radius:4px;height:8px;">
                          <tr>
                            <td style="width:{{ $actividad->avance }}%;background-color:{{ $colorBorde }};border-radius:4px;height:8px;font-size:0;line-height:0;">&nbsp;</td>
                            <td style="width:{{ 100 - $actividad->avance }}%;font-size:0;line-height:0;">&nbsp;</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>

                </td>
              </tr>
            </table>
            @endif

            {{-- Botón principal --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:10px;">
              <tr>
                <td align="center">
                  <a href="{{ $urlSistema }}" target="_blank"
                     style="display:block;background-color:#0d1b3e;color:#ffffff;text-decoration:none;padding:14px 28px;border-radius:8px;font-size:14px;font-weight:700;font-family:Arial,Helvetica,sans-serif;letter-spacing:0.2px;text-align:center;">
                    Ver mis actividades en {{ $instSigla ?? 'PULSO UGEL' }} →
                  </a>
                </td>
              </tr>
            </table>

            {{-- Botón secundario --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td align="center">
                  <a href="{{ $urlAlertas }}" target="_blank"
                     style="display:block;background-color:#f0f4fa;color:#445573;text-decoration:none;padding:11px 28px;border-radius:8px;font-size:13px;font-weight:600;font-family:Arial,Helvetica,sans-serif;border:1px solid #dce4f0;text-align:center;">
                    Ver todas las alertas del sistema
                  </a>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        {{-- ── FOOTER ── --}}
        <tr>
          <td style="background-color:#f0f4fa;border-top:1px solid #dce4f0;border-radius:0 0 14px 14px;padding:18px 36px;text-align:center;">
            <p style="font-size:11.5px;color:#8a9ab8;line-height:1.7;margin:0;">
              Mensaje generado automáticamente por <strong style="color:#c8952a;">{{ $instSigla ?? 'PULSO UGEL' }}</strong>.<br>
              Por favor, no respondas a este correo.<br>
              {{ $instLugar ?? '' }}
            </p>
            <p style="font-size:10.5px;color:#b0baca;margin:6px 0 0;">
              {{ now()->setTimezone('America/Lima')->format('d/m/Y H:i') }} — Hora de Lima (UTC-5)
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
