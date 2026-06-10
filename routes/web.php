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
use App\Http\Controllers\pages\AvanceUnidadesController;
use App\Http\Controllers\pages\ConfiguracionController;
use App\Http\Controllers\pages\UnidadesOrganicasController;
use App\Http\Controllers\pages\ComponenteController;
use App\Http\Controllers\apps\UserList;
use App\Http\Controllers\apps\CargosController;
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
use App\Http\Controllers\pages\CumplimientoController;
use App\Http\Controllers\pages\MisActividadesController;
use App\Http\Controllers\pages\PaciController;
use App\Http\Controllers\pages\MatrizRiesgosController;
use App\Http\Controllers\pages\ActasComiteController;
use App\Http\Controllers\pages\AutoevaluacionController;
use App\Http\Controllers\pages\LandingController;
use App\Http\Controllers\pages\SliderLandingController;
use App\Http\Controllers\pages\InstitucionVinculadaController;

Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/',                    [LandingController::class, 'index'])->name('landing');
Route::get('/noticias/{id}',       [LandingController::class, 'show'])->name('landing.noticia');
Route::get('/publicaciones',       [LandingController::class, 'publicaciones'])->name('landing.publicaciones');
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/diag', fn() => view('content.dashboard.diag'))->name('diag')->middleware('can:configuracion.ver');

    // Slider del Landing (administrado desde el panel)
    Route::get('/slider-landing',                          [SliderLandingController::class, 'index'])->name('slider-landing.index');
    Route::post('/slider-landing',                         [SliderLandingController::class, 'store'])->name('slider-landing.store');
    Route::put('/slider-landing/{sliderLanding}',          [SliderLandingController::class, 'update'])->name('slider-landing.update');
    Route::delete('/slider-landing/{sliderLanding}',       [SliderLandingController::class, 'destroy'])->name('slider-landing.destroy');
    Route::patch('/slider-landing/{sliderLanding}/toggle', [SliderLandingController::class, 'toggleActivo'])->name('slider-landing.toggle');

    // Instituciones Vinculadas del Landing
    Route::get('/instituciones-vinculadas',                                    [InstitucionVinculadaController::class, 'index'])->name('instituciones-vinculadas.index');
    Route::post('/instituciones-vinculadas',                                   [InstitucionVinculadaController::class, 'store'])->name('instituciones-vinculadas.store');
    Route::put('/instituciones-vinculadas/{institucionVinculada}',             [InstitucionVinculadaController::class, 'update'])->name('instituciones-vinculadas.update');
    Route::delete('/instituciones-vinculadas/{institucionVinculada}',          [InstitucionVinculadaController::class, 'destroy'])->name('instituciones-vinculadas.destroy');
    Route::patch('/instituciones-vinculadas/{institucionVinculada}/toggle',    [InstitucionVinculadaController::class, 'toggleActivo'])->name('instituciones-vinculadas.toggle');

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
    Route::put('/evidencias/{evidencia}',             [EvidenciasController::class, 'update'])->name('sci-evidencias.update')->middleware('can:evidencias.subir');
    Route::patch('/evidencias/{evidencia}/validar',   [EvidenciasController::class, 'validar'])->name('sci-evidencias.validar')->middleware('can:evidencias.validar');
    Route::delete('/evidencias/{evidencia}',          [EvidenciasController::class, 'destroy'])->name('sci-evidencias.destroy')->middleware('can:evidencias.validar');

    // --- Monitoreo ---
    Route::get('/semaforo',          [SemaforoController::class,        'index'])->name('mon-semaforo')->middleware('can:integridad.ver');
    Route::middleware('can:alertas.ver')->group(function () {
        Route::get('/alertas', [AlertasController::class, 'index'])->name('mon-alertas');
        Route::patch('/alertas/{alerta}/leer',  [AlertasController::class, 'marcarLeida'])->name('mon-alertas.leer');
        Route::patch('/alertas/leer-todas',     [AlertasController::class, 'marcarTodasLeidas'])->name('mon-alertas.leer-todas');
    });
    Route::post('/alertas',    [AlertasController::class, 'store'])->name('mon-alertas.store')->middleware('can:alertas.crear');
    Route::delete('/alertas/{alerta}', [AlertasController::class, 'destroy'])->name('mon-alertas.destroy')->middleware('can:alertas.eliminar');
    Route::get('/ranking-unidades', [RankingUnidadesController::class, 'index'])->name('mon-ranking-unidades')->middleware('can:reportes.ver');
    Route::get('/avance-unidades',  [AvanceUnidadesController::class,  'index'])->name('mon-avance-unidades')->middleware('can:reportes.ver');

    // --- Cumplimiento SCI ---
    Route::middleware('can:control-interno.ver')->group(function () {
        Route::get('/cumplimiento/panel',         [CumplimientoController::class, 'panelSci'])->name('cumplimiento.panel');
        Route::get('/cumplimiento/responsables',  [CumplimientoController::class, 'responsables'])->name('cumplimiento.responsables');
        Route::get('/cumplimiento/sin-evidencia', [CumplimientoController::class, 'sinEvidencia'])->name('cumplimiento.sin-evidencia');
    });
    Route::get('/cumplimiento/exportar', [CumplimientoController::class, 'exportar'])->name('cumplimiento.exportar')->middleware('can:reportes.exportar');

    // --- Mis Actividades ---
    Route::middleware('can:control-interno.ver')->group(function () {
        Route::get('/mis-actividades',                       [MisActividadesController::class, 'index'])->name('mis-actividades');
        Route::get('/mis-actividades/{actividad}/historial', [MisActividadesController::class, 'historial'])->name('mis-actividades.historial');
    });
    Route::patch('/mis-actividades/{actividad}/avance', [MisActividadesController::class, 'updateAvance'])->name('mis-actividades.avance')->middleware('can:control-interno.editar');

    // --- Reportes ---
    Route::get('/reportes',        [ReportesController::class,       'index'])->name('rep-reportes')->middleware('can:reportes.ver');
    Route::get('/reportes/exportar', [ReportesController::class, 'exportar'])->name('rep-reportes.exportar')->middleware('can:reportes.ver');

    Route::get('/reconocimientos', [ReconocimientosController::class, 'index'])->name('rep-reconocimientos')->middleware('can:reconocimientos.ver');
    Route::get('/reconocimientos/{trabajador}', [ReconocimientosController::class, 'show'])->name('rep-reconocimientos.show')->middleware('can:reconocimientos.ver');
    Route::post('/reconocimientos', [ReconocimientosController::class, 'store'])->name('rep-reconocimientos.store')->middleware('can:reconocimientos.crear');
    Route::put('/reconocimientos/{trabajador}', [ReconocimientosController::class, 'update'])->name('rep-reconocimientos.update')->middleware('can:reconocimientos.editar');
    Route::delete('/reconocimientos/{trabajador}', [ReconocimientosController::class, 'destroy'])->name('rep-reconocimientos.destroy')->middleware('can:reconocimientos.eliminar');

    // --- Administración: Usuarios ---
    Route::get('/usuarios',                   [UserList::class, 'index'])->name('adm-usuarios')->middleware('can:usuarios.ver');
    Route::get('/usuarios/data',              [UserList::class, 'data'])->name('adm-usuarios.data')->middleware('can:usuarios.ver');
    Route::post('/usuarios',                  [UserList::class, 'store'])->name('adm-usuarios.store')->middleware('can:usuarios.crear');
    Route::put('/usuarios/{usuario}',         [UserList::class, 'update'])->name('adm-usuarios.update')->middleware('can:usuarios.editar');
    Route::delete('/usuarios/{usuario}',      [UserList::class, 'destroy'])->name('adm-usuarios.destroy')->middleware('can:usuarios.eliminar');
    Route::patch('/usuarios/{usuario}/estado',[UserList::class, 'toggleEstado'])->name('adm-usuarios.estado')->middleware('can:usuarios.editar');

    // Cargos (catálogo)
    Route::get('/cargos',             [CargosController::class, 'index'])->name('cargos.index')->middleware('can:usuarios.ver');
    Route::post('/cargos',            [CargosController::class, 'store'])->name('cargos.store')->middleware('can:usuarios.crear');
    Route::put('/cargos/{cargo}',     [CargosController::class, 'update'])->name('cargos.update')->middleware('can:usuarios.editar');
    Route::delete('/cargos/{cargo}',  [CargosController::class, 'destroy'])->name('cargos.destroy')->middleware('can:usuarios.eliminar');

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
    Route::get('/buenas-practicas',                          [BuenasPracticasController::class, 'index'])->name('buenas-practicas')->middleware('can:buenas-practicas.ver');
    Route::post('/buenas-practicas',                         [BuenasPracticasController::class, 'store'])->name('buenas-practicas.store')->middleware('can:buenas-practicas.crear');
    Route::put('/buenas-practicas/{buenaPractica}',          [BuenasPracticasController::class, 'update'])->name('buenas-practicas.update')->middleware('can:buenas-practicas.editar');
    Route::delete('/buenas-practicas/{buenaPractica}',       [BuenasPracticasController::class, 'destroy'])->name('buenas-practicas.destroy')->middleware('can:buenas-practicas.editar');
    Route::patch('/buenas-practicas/{buenaPractica}/avance', [BuenasPracticasController::class, 'updateAvance'])->name('buenas-practicas.avance')->middleware('can:buenas-practicas.editar');

    // --- Recomendaciones ---
    Route::get('/recomendaciones',                              [RecomendacionesController::class, 'index'])->name('recomendaciones')->middleware('can:recomendaciones.ver');
    Route::post('/recomendaciones',                             [RecomendacionesController::class, 'store'])->name('recomendaciones.store')->middleware('can:recomendaciones.crear');
    Route::put('/recomendaciones/{recomendacion}',              [RecomendacionesController::class, 'update'])->name('recomendaciones.update')->middleware('can:recomendaciones.editar');
    Route::delete('/recomendaciones/{recomendacion}',           [RecomendacionesController::class, 'destroy'])->name('recomendaciones.destroy')->middleware('can:recomendaciones.editar');
    Route::patch('/recomendaciones/{recomendacion}/atender',    [RecomendacionesController::class, 'marcarAtendida'])->name('recomendaciones.atender')->middleware('can:recomendaciones.editar');

    // --- PACI ---
    Route::get('/paci',         [PaciController::class, 'index'])->name('paci.index')->middleware('can:paci.ver');
    Route::post('/paci',        [PaciController::class, 'store'])->name('paci.store')->middleware('can:paci.crear');
    Route::put('/paci/{paci}',  [PaciController::class, 'update'])->name('paci.update')->middleware('can:paci.editar');
    Route::delete('/paci/{paci}', [PaciController::class, 'destroy'])->name('paci.destroy')->middleware('can:paci.eliminar');

    // --- Matriz de Riesgos ---
    Route::get('/matriz-riesgos',                    [MatrizRiesgosController::class, 'index'])->name('matriz-riesgos.index')->middleware('can:riesgos.ver');
    Route::post('/matriz-riesgos',                   [MatrizRiesgosController::class, 'store'])->name('matriz-riesgos.store')->middleware('can:riesgos.crear');
    Route::put('/matriz-riesgos/{matrizRiesgo}',     [MatrizRiesgosController::class, 'update'])->name('matriz-riesgos.update')->middleware('can:riesgos.editar');
    Route::delete('/matriz-riesgos/{matrizRiesgo}',  [MatrizRiesgosController::class, 'destroy'])->name('matriz-riesgos.destroy')->middleware('can:riesgos.eliminar');

    // --- Actas del Comité ---
    Route::get('/actas-comite',                      [ActasComiteController::class, 'index'])->name('actas-comite.index')->middleware('can:actas.ver');
    Route::post('/actas-comite',                     [ActasComiteController::class, 'store'])->name('actas-comite.store')->middleware('can:actas.crear');
    Route::put('/actas-comite/{actasComite}',        [ActasComiteController::class, 'update'])->name('actas-comite.update')->middleware('can:actas.editar');
    Route::delete('/actas-comite/{actasComite}',     [ActasComiteController::class, 'destroy'])->name('actas-comite.destroy')->middleware('can:actas.eliminar');

    // --- Autoevaluación SCI ---
    Route::get('/autoevaluacion',                             [AutoevaluacionController::class, 'index'])->name('autoevaluacion.index')->middleware('can:autoevaluacion.ver');
    Route::post('/autoevaluacion',                            [AutoevaluacionController::class, 'store'])->name('autoevaluacion.store')->middleware('can:autoevaluacion.crear');
    Route::get('/autoevaluacion/{autoevaluacion}',            [AutoevaluacionController::class, 'show'])->name('autoevaluacion.show')->middleware('can:autoevaluacion.ver');
    Route::patch('/autoevaluacion/{autoevaluacion}/respuestas',[AutoevaluacionController::class, 'guardarRespuestas'])->name('autoevaluacion.respuestas')->middleware('can:autoevaluacion.editar');
    Route::patch('/autoevaluacion/{autoevaluacion}/cerrar',   [AutoevaluacionController::class, 'cerrar'])->name('autoevaluacion.cerrar')->middleware('can:autoevaluacion.editar');
    Route::delete('/autoevaluacion/{autoevaluacion}',         [AutoevaluacionController::class, 'destroy'])->name('autoevaluacion.destroy')->middleware('can:autoevaluacion.eliminar');

    // --- Modelo de Integridad — compromisos por pilar ---
    Route::post('/modelo-integridad/compromiso',                         [ModeloIntegridadController::class, 'storeCompromiso'])->name('integridad.compromiso.store')->middleware('can:integridad.editar');
    Route::put('/modelo-integridad/compromiso/{compromiso}',             [ModeloIntegridadController::class, 'updateCompromiso'])->name('integridad.compromiso.update')->middleware('can:integridad.editar');
    Route::delete('/modelo-integridad/compromiso/{compromiso}',          [ModeloIntegridadController::class, 'destroyCompromiso'])->name('integridad.compromiso.destroy')->middleware('can:integridad.editar');

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
