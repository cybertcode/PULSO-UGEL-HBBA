<?php

namespace App\Support;

use App\Models\ConfiguracionInstitucional;

class SemaforoHelper
{
    /**
     * Calcula porcentaje y decora un objeto con color/semaforo.
     * $totalField y $completadasField son los nombres de propiedades en el objeto.
     */
    public static function decorar(
        object $item,
        string $totalField,
        string $completadasField,
        ?ConfiguracionInstitucional $config = null,
        string $labelVerde    = 'Verde',
        string $labelAmarillo = 'Amarillo',
        string $labelRojo     = 'Rojo',
    ): object {
        [$umbralVerde, $umbralAmarillo] = self::umbrales($config);

        $total       = (int) ($item->{$totalField}       ?? 0);
        $completadas = (int) ($item->{$completadasField} ?? 0);

        $item->porcentaje = $total > 0 ? (int) round(($completadas / $total) * 100) : 0;
        $item->color      = self::color($item->porcentaje, $umbralVerde, $umbralAmarillo);
        $item->semaforo   = self::label($item->porcentaje, $umbralVerde, $umbralAmarillo, $labelVerde, $labelAmarillo, $labelRojo);

        return $item;
    }

    /** Devuelve el color Bootstrap según el porcentaje */
    public static function color(int $porcentaje, int $umbralVerde = 75, int $umbralAmarillo = 50): string
    {
        return $porcentaje >= $umbralVerde ? 'success'
             : ($porcentaje >= $umbralAmarillo ? 'warning' : 'danger');
    }

    /** Devuelve el color hex según el porcentaje */
    public static function colorHex(int $porcentaje, int $umbralVerde = 75, int $umbralAmarillo = 50): string
    {
        return $porcentaje >= $umbralVerde ? '#28c76f'
             : ($porcentaje >= $umbralAmarillo ? '#ff9f43' : '#ea5455');
    }

    /** Devuelve la etiqueta del semáforo */
    public static function label(
        int $porcentaje,
        int $umbralVerde,
        int $umbralAmarillo,
        string $verde    = 'Verde',
        string $amarillo = 'Amarillo',
        string $rojo     = 'Rojo',
    ): string {
        return $porcentaje >= $umbralVerde ? $verde
             : ($porcentaje >= $umbralAmarillo ? $amarillo : $rojo);
    }

    /** Lee los umbrales desde config (con fallback 75/50) */
    public static function umbrales(?ConfiguracionInstitucional $config = null): array
    {
        return [
            (int) ($config->umbral_verde    ?? 75),
            (int) ($config->umbral_amarillo ?? 50),
        ];
    }
}
