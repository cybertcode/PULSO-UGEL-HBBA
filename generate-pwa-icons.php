<?php
/**
 * Generador de iconos PWA para PULSO UGEL
 * Ejecutar desde la raíz del proyecto: php generate-pwa-icons.php
 */

$outputDir = __DIR__ . '/public/icons/pwa/';
$sizes = [72, 96, 128, 144, 152, 192, 384, 512];

// Color primario PULSO UGEL
$bgR = 115; $bgG = 103; $bgB = 240; // #7367f0

foreach ($sizes as $size) {
    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);
    imagealphablending($img, false);

    $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagefilledrectangle($img, 0, 0, $size - 1, $size - 1, $transparent);
    imagealphablending($img, true);

    // Fondo con esquinas redondeadas (simulado con círculo + rectángulos)
    $radius = (int)($size * 0.1875); // ~96/512 de radio
    $bg = imagecolorallocate($img, $bgR, $bgG, $bgB);

    // Rectángulo central
    imagefilledrectangle($img, $radius, 0, $size - $radius - 1, $size - 1, $bg);
    imagefilledrectangle($img, 0, $radius, $size - 1, $size - $radius - 1, $bg);
    // Esquinas redondeadas con elipse
    imagefilledellipse($img, $radius, $radius, $radius * 2, $radius * 2, $bg);
    imagefilledellipse($img, $size - $radius - 1, $radius, $radius * 2, $radius * 2, $bg);
    imagefilledellipse($img, $radius, $size - $radius - 1, $radius * 2, $radius * 2, $bg);
    imagefilledellipse($img, $size - $radius - 1, $size - $radius - 1, $radius * 2, $radius * 2, $bg);

    // Letra "P" centrada
    $white = imagecolorallocate($img, 255, 255, 255);
    $fontSize = (int)($size * 0.5);
    $fontX = (int)($size * 0.25);
    $fontY = (int)($size * 0.72);

    // Usar fuente built-in de GD (si no hay fuente TrueType)
    $gd_font = 5; // fuente más grande disponible en GD
    $charW = imagefontwidth($gd_font);
    $charH = imagefontheight($gd_font);
    $scale = max(1, (int)($size / 40));

    // Para iconos pequeños usar texto directo, para grandes escalar
    if ($size >= 96) {
        // Dibujar "P" con escala
        $tmpSize = 40;
        $tmp = imagecreatetruecolor($tmpSize, $tmpSize);
        $tmpBg = imagecolorallocate($tmp, $bgR, $bgG, $bgB);
        imagefilledrectangle($tmp, 0, 0, $tmpSize - 1, $tmpSize - 1, $tmpBg);
        $tmpWhite = imagecolorallocate($tmp, 255, 255, 255);
        imagestring($tmp, 5, 10, 8, 'P', $tmpWhite);
        imagecopyresampled($img, $tmp,
            (int)($size * 0.22), (int)($size * 0.22),
            0, 0,
            (int)($size * 0.55), (int)($size * 0.55),
            $tmpSize, $tmpSize
        );
        imagedestroy($tmp);
    } else {
        imagestring($img, $gd_font,
            (int)(($size - $charW) / 2),
            (int)(($size - $charH) / 2),
            'P', $white
        );
    }

    // Barra decorativa inferior
    $barH = max(2, (int)($size * 0.031));
    $barY = (int)($size * 0.82);
    $barX1 = (int)($size * 0.2);
    $barX2 = (int)($size * 0.8);
    $barColor = imagecolorallocatealpha($img, 255, 255, 255, 60);
    imagefilledrectangle($img, $barX1, $barY, $barX2, $barY + $barH, $barColor);

    $filename = $outputDir . "icon-{$size}x{$size}.png";
    imagepng($img, $filename, 9);
    imagedestroy($img);
    echo "Generado: icon-{$size}x{$size}.png\n";
}

// Generar screenshots placeholder
foreach (['screenshot-desktop' => [1280, 720], 'screenshot-mobile' => [390, 844]] as $name => [$w, $h]) {
    $img = imagecreatetruecolor($w, $h);
    $bg = imagecolorallocate($img, 248, 247, 250);
    $accent = imagecolorallocate($img, 115, 103, 240);
    imagefilledrectangle($img, 0, 0, $w - 1, $h - 1, $bg);
    imagefilledrectangle($img, 0, 0, $w - 1, 60, $accent);
    $white = imagecolorallocate($img, 255, 255, 255);
    imagestring($img, 5, 20, 20, 'PULSO UGEL - Sistema de Control Interno', $white);
    imagepng($img, $outputDir . $name . '.png', 9);
    imagedestroy($img);
    echo "Generado: {$name}.png\n";
}

echo "\n✓ Todos los iconos PWA generados en public/icons/pwa/\n";
