<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    private ImageManager $manager;

    // Tipos MIME de imagen aceptados
    public const ALLOWED_MIMES = 'jpg,jpeg,png,webp,gif,bmp,svg';

    // Tamaño máximo en KB (5 MB)
    public const MAX_SIZE_KB = 5120;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimiza y guarda una imagen en el disco público.
     * Devuelve la ruta relativa almacenada.
     */
    public function store(UploadedFile $file, string $directory, int $maxWidth = 1200, int $quality = 80): string
    {
        $extension = $this->resolveExtension($file);
        $filename  = Str::uuid() . '.' . $extension;
        $fullPath  = storage_path('app/public/' . $directory . '/' . $filename);

        // Crear directorio si no existe
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        // SVG no se procesa con Intervention (es vectorial)
        if ($extension === 'svg') {
            $file->move(dirname($fullPath), $filename);
            return $directory . '/' . $filename;
        }

        $image = $this->manager->read($file->getRealPath());

        // Redimensionar solo si supera el ancho máximo (mantiene proporción)
        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        // GIF animado: guardar sin recodificar para preservar animación
        if ($extension === 'gif') {
            $file->move(dirname($fullPath), $filename);
            return $directory . '/' . $filename;
        }

        $image->save($fullPath, quality: $quality);

        return $directory . '/' . $filename;
    }

    /**
     * Elimina una imagen del disco público si existe.
     */
    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function resolveExtension(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        // Fallback por MIME cuando la extensión no coincide
        if (!$ext || !in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'])) {
            $map = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif',
                'image/bmp'  => 'bmp',
                'image/svg+xml' => 'svg',
            ];
            $ext = $map[$file->getMimeType()] ?? 'jpg';
        }
        // Normalizar jpeg → jpg
        return $ext === 'jpeg' ? 'jpg' : $ext;
    }
}
