<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function data()
    {
        $user = Auth::user();

        // [nombre, route_name, icono, permiso_requerido|null]
        $allItems = [
            // Acceso universal — dashboard siempre visible para autenticados
            ['name' => 'Panel Principal',             'route' => 'dashboard',                  'icon' => 'tabler-smart-home',         'permission' => null],

            // Perfil y acciones propias
            ['name' => 'Mi Perfil',                   'route' => 'profile.show',               'icon' => 'tabler-user-circle',        'permission' => 'perfil.ver'],
            ['name' => 'Mis Actividades',             'route' => 'mis-actividades',            'icon' => 'tabler-checklist',          'permission' => 'mis-actividades.ver'],
            ['name' => 'Ayuda',                       'route' => 'ayuda',                      'icon' => 'tabler-help-circle',        'permission' => 'ayuda.ver'],

            // SCI
            ['name' => 'Control Interno',             'route' => 'sci-control-interno',        'icon' => 'tabler-clipboard-check',    'permission' => 'control-interno.ver'],
            ['name' => 'Evidencias',                  'route' => 'sci-evidencias',             'icon' => 'tabler-file-upload',        'permission' => 'evidencias.ver'],
            ['name' => 'Modelo de Integridad',        'route' => 'sci-modelo-integridad',      'icon' => 'tabler-shield-check',       'permission' => 'integridad.ver'],
            ['name' => 'Semáforo Institucional',      'route' => 'sci-semaforo',               'icon' => 'tabler-traffic-lights',     'permission' => 'semaforo.ver'],

            // Cumplimiento
            ['name' => 'Cumplimiento — Panel',        'route' => 'cumplimiento.panel',         'icon' => 'tabler-layout-dashboard',   'permission' => 'cumplimiento.ver'],
            ['name' => 'Cumplimiento — Responsables', 'route' => 'cumplimiento.responsables',  'icon' => 'tabler-users-group',        'permission' => 'cumplimiento.ver'],
            ['name' => 'Cumplimiento — Sin Evidencia','route' => 'cumplimiento.sin-evidencia', 'icon' => 'tabler-file-off',           'permission' => 'cumplimiento.ver'],

            // Monitoreo
            ['name' => 'Alertas del Sistema',         'route' => 'mon-alertas',                'icon' => 'tabler-bell',               'permission' => 'alertas.ver'],
            ['name' => 'Avance de Unidades',          'route' => 'mon-avance-unidades',        'icon' => 'tabler-chart-area',         'permission' => 'reportes.ver'],
            ['name' => 'Ranking de Unidades',         'route' => 'mon-ranking-unidades',       'icon' => 'tabler-award',              'permission' => 'reportes.ver'],

            // Reportes y más
            ['name' => 'Reportes',                    'route' => 'rep-reportes',               'icon' => 'tabler-chart-bar',          'permission' => 'reportes.ver'],
            ['name' => 'Reconocimientos',             'route' => 'rep-reconocimientos',        'icon' => 'tabler-trophy',             'permission' => 'reconocimientos.ver'],
            ['name' => 'Recomendaciones',             'route' => 'recomendaciones',            'icon' => 'tabler-bulb',               'permission' => 'recomendaciones.ver'],
            ['name' => 'Buenas Prácticas',            'route' => 'buenas-practicas',           'icon' => 'tabler-star',               'permission' => 'buenas-practicas.ver'],
            ['name' => 'Normativas',                  'route' => 'normativas',                 'icon' => 'tabler-book',               'permission' => 'normativas.ver'],
            ['name' => 'Encuestas',                   'route' => 'encuestas.index',            'icon' => 'tabler-forms',              'permission' => 'encuesta.ver'],

            // Administración
            ['name' => 'Usuarios',                    'route' => 'adm-usuarios',               'icon' => 'tabler-users',              'permission' => 'usuarios.ver'],
            ['name' => 'Roles y Permisos',            'route' => 'adm-roles',                  'icon' => 'tabler-shield-lock',        'permission' => 'roles.ver'],
            ['name' => 'Unidades Orgánicas',          'route' => 'adm-unidades',               'icon' => 'tabler-building',           'permission' => 'unidades.ver'],
            ['name' => 'Estructura SCI',              'route' => 'adm-sci-estructura',         'icon' => 'tabler-sitemap',            'permission' => 'componentes.ver'],
            ['name' => 'Estructura Integridad',       'route' => 'adm-integridad-estructura',  'icon' => 'tabler-sitemap',            'permission' => 'integridad.ver'],
            ['name' => 'Configuración del Sistema',   'route' => 'adm-configuracion',          'icon' => 'tabler-settings',           'permission' => 'configuracion.ver'],
            ['name' => 'Slider / Landing',            'route' => 'slider-landing.index',       'icon' => 'tabler-photo',              'permission' => 'slider.ver'],
        ];

        $entries = [];
        foreach ($allItems as $item) {
            // Saltar si el usuario no tiene el permiso requerido
            if ($item['permission'] !== null && !$user->can($item['permission'])) {
                continue;
            }
            // Saltar si la ruta no existe en esta instalación
            if (!app('router')->has($item['route'])) {
                continue;
            }
            $entries[] = [
                'name' => $item['name'],
                'url'  => route($item['route']),
                'icon' => $item['icon'],
            ];
        }

        return response()->json([
            'suggestions' => ['Módulos del sistema' => $entries],
            'navigation'  => ['Menú principal'      => $entries],
        ]);
    }
}
