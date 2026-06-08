<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\DashboardController;
use App\Http\Controllers\pages\ControlInternoController;
use App\Http\Controllers\pages\ModeloIntegridadController;
use App\Http\Controllers\pages\EvidenciasController;
use App\Http\Controllers\pages\AlertasController;
use App\Http\Controllers\pages\ReportesController;
use App\Http\Controllers\pages\ReconocimientosController;
use App\Models\TrabajadorDestacado;
use App\Http\Controllers\pages\SemaforoController;
use App\Http\Controllers\pages\RankingUnidadesController;
use App\Http\Controllers\pages\AvanceUnidadesController;
use App\Http\Controllers\pages\ConfiguracionController;
use App\Http\Controllers\pages\UnidadesOrganicasController;
use App\Http\Controllers\pages\ComponenteController;
use App\Http\Controllers\apps\UserList;
use App\Http\Controllers\apps\UserViewAccount;
use App\Http\Controllers\apps\UserViewSecurity;
use App\Http\Controllers\apps\AccessRoles;
use App\Http\Controllers\apps\AccessPermission;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\pages\PerfilController;
use App\Http\Controllers\pages\BuenasPracticasController;
use App\Http\Controllers\pages\RecomendacionesController;
use App\Http\Controllers\pages\AyudaController;

Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/auth/login-basic',    [LoginBasic::class,    'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/pages/misc-error',    [MiscError::class,     'index'])->name('pages-misc-error');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    // Perfil de usuario (reemplaza el Livewire de Jetstream)
    Route::get('/user/profile',           [PerfilController::class, 'show'])->name('profile.show');
    Route::post('/user/profile/info',     [PerfilController::class, 'updateInfo'])->name('profile.update-info');
    Route::post('/user/profile/password', [PerfilController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/user/profile/photo',  [PerfilController::class, 'deletePhoto'])->name('profile.delete-photo');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/diag', fn() => view('content.dashboard.diag'))->name('diag');

    // --- Control y Seguimiento (permisos: control-interno.*, integridad.*, evidencias.*) ---
    Route::middleware('can:control-interno.ver')->group(function () {
        Route::get('/control-interno', [ControlInternoController::class, 'index'])->name('sci-control-interno');
    });
    Route::post('/control-interno',                       [ControlInternoController::class, 'store'])->name('sci-control-interno.store')->middleware('can:control-interno.crear');
    Route::put('/control-interno/{actividad}',            [ControlInternoController::class, 'update'])->name('sci-control-interno.update')->middleware('can:control-interno.editar');
    Route::delete('/control-interno/{actividad}',         [ControlInternoController::class, 'destroy'])->name('sci-control-interno.destroy')->middleware('can:control-interno.editar');
    Route::patch('/control-interno/{actividad}/avance',   [ControlInternoController::class, 'updateAvance'])->name('sci-control-interno.avance')->middleware('can:control-interno.editar');
    Route::get('/control-interno/{actividad}/historial',  [ControlInternoController::class, 'historial'])->name('sci-control-interno.historial')->middleware('can:control-interno.ver');

    Route::get('/modelo-integridad', [ModeloIntegridadController::class, 'index'])->name('sci-modelo-integridad')->middleware('can:integridad.ver');

    Route::middleware('can:evidencias.ver')->group(function () {
        Route::get('/evidencias', [EvidenciasController::class, 'index'])->name('sci-evidencias');
    });
    Route::post('/evidencias',                        [EvidenciasController::class, 'store'])->name('sci-evidencias.store')->middleware('can:evidencias.subir');
    Route::patch('/evidencias/{evidencia}/validar',   [EvidenciasController::class, 'validar'])->name('sci-evidencias.validar')->middleware('can:evidencias.validar');
    Route::delete('/evidencias/{evidencia}',          [EvidenciasController::class, 'destroy'])->name('sci-evidencias.destroy')->middleware('can:evidencias.validar');

    // --- Monitoreo ---
    Route::get('/semaforo',          [SemaforoController::class,        'index'])->name('mon-semaforo');
    Route::middleware('can:alertas.ver')->group(function () {
        Route::get('/alertas', [AlertasController::class, 'index'])->name('mon-alertas');
    });
    Route::patch('/alertas/{alerta}/leer',   [AlertasController::class, 'marcarLeida'])->name('mon-alertas.leer');
    Route::patch('/alertas/leer-todas',      [AlertasController::class, 'marcarTodasLeidas'])->name('mon-alertas.leer-todas');
    Route::post('/alertas',                  [AlertasController::class, 'store'])->name('mon-alertas.store')->middleware('can:alertas.ver');
    Route::delete('/alertas/{alerta}',       [AlertasController::class, 'destroy'])->name('mon-alertas.destroy')->middleware('can:alertas.ver');
    Route::get('/ranking-unidades', [RankingUnidadesController::class, 'index'])->name('mon-ranking-unidades');
    Route::get('/avance-unidades',  [AvanceUnidadesController::class,  'index'])->name('mon-avance-unidades');

    // --- Reportes ---
    Route::get('/reportes',        [ReportesController::class,       'index'])->name('rep-reportes')->middleware('can:reportes.ver');
    Route::get('/reportes/exportar', [ReportesController::class, 'exportar'])->name('rep-reportes.exportar')->middleware('can:reportes.ver');

    Route::get('/reconocimientos', [ReconocimientosController::class, 'index'])->name('rep-reconocimientos')->middleware('can:reconocimientos.ver');
    Route::post('/reconocimientos', [ReconocimientosController::class, 'store'])->name('rep-reconocimientos.store')->middleware('can:reconocimientos.ver');
    Route::get('/reconocimientos/{trabajador}', [ReconocimientosController::class, 'show'])->name('rep-reconocimientos.show')->middleware('can:reconocimientos.ver');
    Route::put('/reconocimientos/{trabajador}', [ReconocimientosController::class, 'update'])->name('rep-reconocimientos.update')->middleware('can:reconocimientos.ver');
    Route::delete('/reconocimientos/{trabajador}', [ReconocimientosController::class, 'destroy'])->name('rep-reconocimientos.destroy')->middleware('can:reconocimientos.ver');

    // --- Administración: Usuarios ---
    Route::get('/usuarios',                   [UserList::class, 'index'])->name('adm-usuarios')->middleware('can:usuarios.ver');
    Route::get('/usuarios/data',              [UserList::class, 'data'])->name('adm-usuarios.data')->middleware('can:usuarios.ver');
    Route::post('/usuarios',                  [UserList::class, 'store'])->name('adm-usuarios.store')->middleware('can:usuarios.crear');
    Route::put('/usuarios/{usuario}',         [UserList::class, 'update'])->name('adm-usuarios.update')->middleware('can:usuarios.editar');
    Route::delete('/usuarios/{usuario}',      [UserList::class, 'destroy'])->name('adm-usuarios.destroy')->middleware('can:usuarios.eliminar');
    Route::patch('/usuarios/{usuario}/estado',[UserList::class, 'toggleEstado'])->name('adm-usuarios.estado')->middleware('can:usuarios.editar');

    Route::get('/usuarios/ver',       [UserViewAccount::class,  'index'])->name('adm-usuarios-ver');
    Route::get('/usuarios/seguridad', [UserViewSecurity::class, 'index'])->name('adm-usuarios-seguridad');

    // --- Administración: Roles y Permisos ---
    Route::middleware('can:configuracion.ver')->group(function () {
        Route::get('/roles',    [AccessRoles::class,      'index'])->name('adm-roles');
        Route::get('/permisos', [AccessPermission::class, 'index'])->name('adm-permisos');
    });
    Route::middleware('can:configuracion.editar')->group(function () {
        Route::post('/roles',          [AccessRoles::class, 'store'])->name('adm-roles.store');
        Route::put('/roles/{role}',    [AccessRoles::class, 'update'])->name('adm-roles.update');
        Route::delete('/roles/{role}', [AccessRoles::class, 'destroy'])->name('adm-roles.destroy');
    });

    // --- Administración: Componentes SCI ---
    Route::middleware('can:componentes.ver')->group(function () {
        Route::get('/administracion/componentes', [ComponenteController::class, 'index'])->name('adm-componentes');
    });
    Route::middleware('can:componentes.editar')->group(function () {
        Route::post('/administracion/componentes',                    [ComponenteController::class, 'store'])->name('adm-componentes.store');
        Route::put('/administracion/componentes/{componente}',        [ComponenteController::class, 'update'])->name('adm-componentes.update');
        Route::patch('/administracion/componentes/{componente}/toggle',[ComponenteController::class, 'toggle'])->name('adm-componentes.toggle');
        Route::delete('/administracion/componentes/{componente}',     [ComponenteController::class, 'destroy'])->name('adm-componentes.destroy');
    });

    // --- Configuración Institucional ---
    Route::get('/configuracion',  [ConfiguracionController::class, 'index'])->name('adm-configuracion')->middleware('can:configuracion.ver');
    Route::put('/configuracion',  [ConfiguracionController::class, 'update'])->name('adm-configuracion.update')->middleware('can:configuracion.editar');

    // --- Buenas Prácticas ---
    Route::get('/buenas-practicas',                          [BuenasPracticasController::class, 'index'])->name('buenas-practicas');
    Route::post('/buenas-practicas',                         [BuenasPracticasController::class, 'store'])->name('buenas-practicas.store');
    Route::put('/buenas-practicas/{buenaPractica}',          [BuenasPracticasController::class, 'update'])->name('buenas-practicas.update');
    Route::delete('/buenas-practicas/{buenaPractica}',       [BuenasPracticasController::class, 'destroy'])->name('buenas-practicas.destroy');
    Route::patch('/buenas-practicas/{buenaPractica}/avance', [BuenasPracticasController::class, 'updateAvance'])->name('buenas-practicas.avance');

    // --- Recomendaciones ---
    Route::get('/recomendaciones',                              [RecomendacionesController::class, 'index'])->name('recomendaciones');
    Route::post('/recomendaciones',                             [RecomendacionesController::class, 'store'])->name('recomendaciones.store');
    Route::put('/recomendaciones/{recomendacion}',              [RecomendacionesController::class, 'update'])->name('recomendaciones.update');
    Route::delete('/recomendaciones/{recomendacion}',           [RecomendacionesController::class, 'destroy'])->name('recomendaciones.destroy');
    Route::patch('/recomendaciones/{recomendacion}/atender',    [RecomendacionesController::class, 'marcarAtendida'])->name('recomendaciones.atender');

    // --- Ayuda ---
    Route::get('/ayuda', [AyudaController::class, 'index'])->name('ayuda');

    // --- Unidades Orgánicas ---
    Route::middleware('can:configuracion.ver')->group(function () {
        Route::get('/unidades-organicas', [UnidadesOrganicasController::class, 'index'])->name('adm-unidades');
    });
    Route::middleware('can:configuracion.editar')->group(function () {
        Route::post('/unidades-organicas',                  [UnidadesOrganicasController::class, 'store'])->name('adm-unidades.store');
        Route::put('/unidades-organicas/{unidad}',          [UnidadesOrganicasController::class, 'update'])->name('adm-unidades.update');
        Route::patch('/unidades-organicas/{unidad}/toggle', [UnidadesOrganicasController::class, 'toggle'])->name('adm-unidades.toggle');
        Route::delete('/unidades-organicas/{unidad}',       [UnidadesOrganicasController::class, 'destroy'])->name('adm-unidades.destroy');
    });
});
