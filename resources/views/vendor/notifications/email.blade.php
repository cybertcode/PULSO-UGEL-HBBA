@php
  try {
    $ci = \App\Models\ConfiguracionInstitucional::cached();
  } catch (\Exception $e) {
    $ci = null;
  }
  $instNombre = $ci?->nombre_institucion ?? config('app.name', 'PULSO UGEL');
  $instSigla  = $ci?->sigla ?? $instNombre;
  $instLugar  = implode(' &bull; ', array_filter([
    $ci?->provincia,
    $ci?->departamento,
    'Perú'
  ]));
@endphp
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $instSigla }}</title>
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
                             alt="{{ $instSigla }}">
                      </td>
                      @else
                      <td style="width:44px;height:44px;background-color:rgba(200,149,42,0.2);border:1px solid rgba(200,149,42,0.35);border-radius:10px;text-align:center;vertical-align:middle;padding:0 10px;font-size:20px;">
                        🏛️
                      </td>
                      @endif
                      <td style="padding-left:14px;">
                        <div style="font-family:Georgia,'Times New Roman',serif;font-size:17px;font-weight:700;color:#ffffff;line-height:1.2;letter-spacing:0.2px;">{{ $instSigla }}</div>
                        <div style="font-size:10px;color:#d4a843;letter-spacing:1px;text-transform:uppercase;margin-top:2px;">Sistema Institucional &bull; {{ $instNombre }}</div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Barra dorada --}}
        <tr>
          <td style="height:4px;background:linear-gradient(90deg,#c8952a,#f0c96a,#c8952a);font-size:0;line-height:0;">&nbsp;</td>
        </tr>

        {{-- ── BODY ── --}}
        <tr>
          <td style="background-color:#ffffff;padding:36px 40px;">

            {{-- Saludo --}}
            @if (!empty($greeting))
              <p style="font-family:Georgia,'Times New Roman',serif;font-size:22px;font-weight:700;color:#0d1b3e;margin:0 0 18px;line-height:1.3;">{{ $greeting }}</p>
            @else
              @if ($level === 'error')
                <p style="font-family:Georgia,'Times New Roman',serif;font-size:22px;font-weight:700;color:#0d1b3e;margin:0 0 18px;line-height:1.3;">Ha ocurrido un problema</p>
              @else
                <p style="font-family:Georgia,'Times New Roman',serif;font-size:22px;font-weight:700;color:#0d1b3e;margin:0 0 18px;line-height:1.3;">Hola,</p>
              @endif
            @endif

            {{-- Líneas de introducción --}}
            @foreach ($introLines as $line)
              <p style="font-size:14.5px;color:#445573;line-height:1.75;margin:0 0 14px;">{{ $line }}</p>
            @endforeach

            {{-- Botón de acción --}}
            @isset($actionText)
              @php
                $bgColor = match($level) {
                  'success' => '#1a7f4b',
                  'error'   => '#c0392b',
                  default   => '#0d1b3e',
                };
              @endphp
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
                <tr>
                  <td align="center">
                    <a href="{{ $actionUrl }}" target="_blank"
                       style="display:inline-block;background-color:{{ $bgColor }};color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:14px;font-weight:700;font-family:Arial,Helvetica,sans-serif;letter-spacing:0.3px;">
                      {{ $actionText }}
                    </a>
                  </td>
                </tr>
              </table>
            @endisset

            {{-- Líneas de cierre --}}
            @foreach ($outroLines as $line)
              <p style="font-size:14.5px;color:#445573;line-height:1.75;margin:0 0 14px;">{{ $line }}</p>
            @endforeach

            {{-- Divisor --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;">
              <tr><td style="height:1px;background-color:#dce4f0;font-size:0;line-height:0;">&nbsp;</td></tr>
            </table>

            {{-- Salutación --}}
            <p style="font-size:14px;color:#445573;line-height:1.7;margin:0;">
              @if (!empty($salutation))
                {{ $salutation }}
              @else
                Atentamente,<br>
                <strong style="color:#0d1b3e;">{{ $instNombre }}</strong>
              @endif
            </p>

            {{-- Subcopy: enlace de respaldo --}}
            @isset($actionText)
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:28px;">
                <tr>
                  <td style="background-color:#f7f9fc;border:1px solid #dce4f0;border-radius:8px;padding:16px 18px;">
                    <p style="font-size:12px;color:#7a8fae;line-height:1.6;margin:0 0 8px;">Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                    <p style="font-size:11.5px;margin:0;word-break:break-all;">
                      <a href="{{ $actionUrl }}" style="color:#1a3a6e;">{{ $displayableActionUrl }}</a>
                    </p>
                  </td>
                </tr>
              </table>
            @endisset

          </td>
        </tr>

        {{-- ── FOOTER ── --}}
        <tr>
          <td style="background-color:#f0f4fa;border-top:1px solid #dce4f0;border-radius:0 0 14px 14px;padding:20px 36px;text-align:center;">
            <p style="font-size:11.5px;color:#8a9ab8;line-height:1.7;margin:0;">
              Mensaje generado automáticamente por <strong style="color:#c8952a;">{{ $instSigla }}</strong>.<br>
              Por favor, no respondas a este correo.<br>
              {!! $instLugar !!}
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
