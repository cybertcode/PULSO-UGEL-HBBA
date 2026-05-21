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
use App\Http\Controllers\pages\SemaforoController;
use App\Http\Controllers\pages\RankingUnidadesController;
use App\Http\Controllers\pages\ConfiguracionController;
use App\Http\Controllers\apps\UserList;
use App\Http\Controllers\apps\UserViewAccount;
use App\Http\Controllers\apps\UserViewSecurity;
use App\Http\Controllers\apps\AccessRoles;
use App\Http\Controllers\apps\AccessPermission;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Models\UnidadOrganica;

Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/auth/login-basic',    [LoginBasic::class,    'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/pages/misc-error',    [MiscError::class,     'index'])->name('pages-misc-error');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // --- Control y Seguimiento ---
    Route::get('/control-interno',   [ControlInternoController::class, 'index'])->name('sci-control-interno');
    Route::post('/control-interno',  [ControlInternoController::class, 'store'])->name('sci-control-interno.store');
    Route::put('/control-interno/{actividad}',    [ControlInternoController::class, 'update'])->name('sci-control-interno.update');
    Route::delete('/control-interno/{actividad}', [ControlInternoController::class, 'destroy'])->name('sci-control-interno.destroy');
    Route::patch('/control-interno/{actividad}/avance', [ControlInternoController::class, 'updateAvance'])->name('sci-control-interno.avance');

    Route::get('/modelo-integridad', [ModeloIntegridadController::class, 'index'])->name('sci-modelo-integridad');

    Route::get('/evidencias',    [EvidenciasController::class, 'index'])->name('sci-evidencias');
    Route::post('/evidencias',   [EvidenciasController::class, 'store'])->name('sci-evidencias.store');
    Route::patch('/evidencias/{evidencia}/validar',  [EvidenciasController::class, 'validar'])->name('sci-evidencias.validar');
    Route::delete('/evidencias/{evidencia}', [EvidenciasController::class, 'destroy'])->name('sci-evidencias.destroy');

    // --- Monitoreo ---
    Route::get('/semaforo',         [SemaforoController::class,       'index'])->name('mon-semaforo');
    Route::get('/alertas',          [AlertasController::class,        'index'])->name('mon-alertas');
    Route::patch('/alertas/{alerta}/leer',  [AlertasController::class, 'marcarLeida'])->name('mon-alertas.leer');
    Route::patch('/alertas/leer-todas',     [AlertasController::class, 'marcarTodasLeidas'])->name('mon-alertas.leer-todas');
    Route::get('/ranking-unidades', [RankingUnidadesController::class,'index'])->name('mon-ranking-unidades');

    // --- Reportes ---
    Route::get('/reportes',         [ReportesController::class,       'index'])->name('rep-reportes');
    Route::get('/reconocimientos',  [ReconocimientosController::class,'index'])->name('rep-reconocimientos');

    // --- Administración ---
    Route::get('/usuarios',          [UserList::class,        'index'])->name('adm-usuarios');
    Route::get('/usuarios/ver',      [UserViewAccount::class, 'index'])->name('adm-usuarios-ver');
    Route::get('/usuarios/seguridad',[UserViewSecurity::class,'index'])->name('adm-usuarios-seguridad');
    Route::get('/roles',             [AccessRoles::class,     'index'])->name('adm-roles');
    Route::get('/permisos',          [AccessPermission::class,'index'])->name('adm-permisos');

    Route::get('/configuracion',                             [ConfiguracionController::class, 'index'])->name('adm-configuracion');
    Route::put('/configuracion',                             [ConfiguracionController::class, 'update'])->name('adm-configuracion.update');
    Route::post('/configuracion/unidades',                   [ConfiguracionController::class, 'storeUnidad'])->name('adm-configuracion.unidades.store');
    Route::put('/configuracion/unidades/{unidad}',           [ConfiguracionController::class, 'updateUnidad'])->name('adm-configuracion.unidades.update');
});
