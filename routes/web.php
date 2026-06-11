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
use App\Http\Controllers\pages\SciEstructuraController;
use App\Http\Controllers\pages\IntegridadEstructuraController;
use App\Http\Controllers\pages\LandingController;
use App\Http\Controllers\pages\SliderLandingController;
use App\Http\Controllers\pages\InstitucionVinculadaController;
use App\Http\Controllers\pages\EncuestaController;
use App\Http\Controllers\pages\EncuestaRespuestaController;
use App\Http\Controllers\pages\EncuestaResultadoController;

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
    Route::post('/modelo-integridad',                        [ModeloIntegridadController::class, 'store'])->name('integridad.store')->middleware('can:integridad.crear');
    Route::put('/modelo-integridad/{actividad}',             [ModeloIntegridadController::class, 'update'])->name('integridad.update')->middleware('can:integridad.editar');
    Route::delete('/modelo-integridad/{actividad}',          [ModeloIntegridadController::class, 'destroy'])->name('integridad.destroy')->middleware('can:integridad.editar');
    Route::patch('/modelo-integridad/{actividad}/avance',    [ModeloIntegridadController::class, 'updateAvance'])->name('integridad.avance')->middleware('can:integridad.editar');
    Route::get('/modelo-integridad/{actividad}/historial',   [ModeloIntegridadController::class, 'historial'])->name('integridad.historial')->middleware('can:integridad.ver');
    Route::get('/integridad/componentes',                    [ModeloIntegridadController::class, 'componentesPorEtapa'])->name('integridad.componentes');
    Route::get('/integridad/preguntas',                      [ModeloIntegridadController::class, 'preguntasPorComponente'])->name('integridad.preguntas');

    Route::middleware('can:evidencias.ver')->group(function () {
        Route::get('/evidencias', [EvidenciasController::class, 'index'])->name('sci-evidencias');
    });
    Route::post('/evidencias',                        [EvidenciasController::class, 'store'])->name('sci-evidencias.store')->middleware('can:evidencias.subir');
    Route::put('/evidencias/{evidencia}',             [EvidenciasController::class, 'update'])->name('sci-evidencias.update')->middleware('can:evidencias.subir');
    Route::patch('/evidencias/{evidencia}/validar',   [EvidenciasController::class, 'validar'])->name('sci-evidencias.validar')->middleware('can:evidencias.validar');
    Route::delete('/evidencias/{evidencia}',          [EvidenciasController::class, 'destroy'])->name('sci-evidencias.destroy')->middleware('can:evidencias.validar');

    // Notificaciones
    Route::patch('/notifications/{id}/read', function (string $id) {
        $notif = auth()->user()->notifications()->findOrFail($id);
        $notif->markAsRead();
        return back();
    })->name('notifications.read');

    // --- Monitoreo ---
    Route::get('/semaforo',          [SemaforoController::class,        'index'])->name('sci-semaforo');
    Route::middleware('can:alertas.ver')->group(function () {
        Route::get('/alertas', [AlertasController::class, 'index'])->name('mon-alertas');
        Route::patch('/alertas/{alerta}/leer',  [AlertasController::class, 'marcarLeida'])->name('mon-alertas.leer');
        Route::patch('/alertas/leer-todas',     [AlertasController::class, 'marcarTodasLeidas'])->name('mon-alertas.leer-todas');
    });
    Route::post('/alertas',    [AlertasController::class, 'store'])->name('mon-alertas.store')->middleware('can:alertas.crear');
    Route::post('/alertas/{alerta}/email', [AlertasController::class, 'enviarEmail'])->name('mon-alertas.email')->middleware('can:alertas.ver');
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

    // --- Administración: Estructura SCI (Ejes → Componentes → Preguntas) ---
    Route::middleware('can:componentes.ver')->group(function () {
        Route::get('/administracion/sci', [SciEstructuraController::class, 'index'])->name('adm-sci-estructura');
    });
    Route::middleware('can:componentes.editar')->group(function () {
        // Ejes
        Route::post('/administracion/sci/eje',              [SciEstructuraController::class, 'storeEje'])->name('adm-sci.eje.store');
        Route::put('/administracion/sci/eje/{eje}',         [SciEstructuraController::class, 'updateEje'])->name('adm-sci.eje.update');
        Route::delete('/administracion/sci/eje/{eje}',      [SciEstructuraController::class, 'destroyEje'])->name('adm-sci.eje.destroy');
        // Componentes SCI
        Route::post('/administracion/sci/componente',                    [SciEstructuraController::class, 'storeComponente'])->name('adm-sci.componente.store');
        Route::put('/administracion/sci/componente/{componente}',        [SciEstructuraController::class, 'updateComponente'])->name('adm-sci.componente.update');
        Route::delete('/administracion/sci/componente/{componente}',     [SciEstructuraController::class, 'destroyComponente'])->name('adm-sci.componente.destroy');
        // Preguntas SCI
        Route::post('/administracion/sci/pregunta',                  [SciEstructuraController::class, 'storePregunta'])->name('adm-sci.pregunta.store');
        Route::put('/administracion/sci/pregunta/{pregunta}',        [SciEstructuraController::class, 'updatePregunta'])->name('adm-sci.pregunta.update');
        Route::delete('/administracion/sci/pregunta/{pregunta}',     [SciEstructuraController::class, 'destroyPregunta'])->name('adm-sci.pregunta.destroy');
    });
    // API cascada SCI (sin middleware de permiso extra — requiere auth)
    Route::get('/api/sci/ejes',        [SciEstructuraController::class, 'apiEjes'])->name('api.sci.ejes');
    Route::get('/api/sci/componentes', [SciEstructuraController::class, 'apiComponentes'])->name('api.sci.componentes');
    Route::get('/api/sci/preguntas',   [SciEstructuraController::class, 'apiPreguntas'])->name('api.sci.preguntas');

    // --- Administración: Estructura Integridad (Etapas → Componentes → Preguntas) ---
    Route::middleware('can:integridad.ver')->group(function () {
        Route::get('/administracion/integridad', [IntegridadEstructuraController::class, 'index'])->name('adm-integridad-estructura');
    });
    Route::middleware('can:integridad.editar')->group(function () {
        // Etapas
        Route::post('/administracion/integridad/etapa',             [IntegridadEstructuraController::class, 'storeEtapa'])->name('adm-integridad.etapa.store');
        Route::put('/administracion/integridad/etapa/{etapa}',      [IntegridadEstructuraController::class, 'updateEtapa'])->name('adm-integridad.etapa.update');
        Route::delete('/administracion/integridad/etapa/{etapa}',   [IntegridadEstructuraController::class, 'destroyEtapa'])->name('adm-integridad.etapa.destroy');
        // Componentes Integridad
        Route::post('/administracion/integridad/componente',                    [IntegridadEstructuraController::class, 'storeComponente'])->name('adm-integridad.componente.store');
        Route::put('/administracion/integridad/componente/{componente}',        [IntegridadEstructuraController::class, 'updateComponente'])->name('adm-integridad.componente.update');
        Route::delete('/administracion/integridad/componente/{componente}',     [IntegridadEstructuraController::class, 'destroyComponente'])->name('adm-integridad.componente.destroy');
        // Preguntas Integridad
        Route::post('/administracion/integridad/pregunta',                  [IntegridadEstructuraController::class, 'storePregunta'])->name('adm-integridad.pregunta.store');
        Route::put('/administracion/integridad/pregunta/{pregunta}',        [IntegridadEstructuraController::class, 'updatePregunta'])->name('adm-integridad.pregunta.update');
        Route::delete('/administracion/integridad/pregunta/{pregunta}',     [IntegridadEstructuraController::class, 'destroyPregunta'])->name('adm-integridad.pregunta.destroy');
    });
    // API cascada Integridad
    Route::get('/api/integridad/etapas',      [IntegridadEstructuraController::class, 'apiEtapas'])->name('api.integridad.etapas');
    Route::get('/api/integridad/componentes', [IntegridadEstructuraController::class, 'apiComponentes'])->name('api.integridad.componentes');
    Route::get('/api/integridad/preguntas',   [IntegridadEstructuraController::class, 'apiPreguntas'])->name('api.integridad.preguntas');

    // --- Configuración Institucional ---
    Route::get('/configuracion',  [ConfiguracionController::class, 'index'])->name('adm-configuracion')->middleware('can:configuracion.ver');
    Route::put('/configuracion',  [ConfiguracionController::class, 'update'])->name('adm-configuracion.update')->middleware('can:configuracion.editar');

    // --- Buenas Prácticas ---
    // Cualquier autenticado puede ver el listado y proponer
    Route::get('/buenas-practicas',                            [BuenasPracticasController::class, 'index'])->name('buenas-practicas');
    Route::post('/buenas-practicas/proponer',                  [BuenasPracticasController::class, 'proponer'])->name('buenas-practicas.proponer');
    // Solo gestor puede crear/editar/gestionar propuestas
    Route::post('/buenas-practicas',                           [BuenasPracticasController::class, 'store'])->name('buenas-practicas.store')->middleware('can:buenas-practicas.ver');
    Route::put('/buenas-practicas/{buenaPractica}',            [BuenasPracticasController::class, 'update'])->name('buenas-practicas.update')->middleware('can:buenas-practicas.ver');
    Route::delete('/buenas-practicas/{buenaPractica}',         [BuenasPracticasController::class, 'destroy'])->name('buenas-practicas.destroy')->middleware('can:buenas-practicas.ver');
    Route::patch('/buenas-practicas/{buenaPractica}/avance',   [BuenasPracticasController::class, 'updateAvance'])->name('buenas-practicas.avance')->middleware('can:buenas-practicas.ver');
    Route::patch('/buenas-practicas/{buenaPractica}/aprobar',  [BuenasPracticasController::class, 'aprobar'])->name('buenas-practicas.aprobar')->middleware('can:buenas-practicas.ver');
    Route::patch('/buenas-practicas/{buenaPractica}/rechazar', [BuenasPracticasController::class, 'rechazar'])->name('buenas-practicas.rechazar')->middleware('can:buenas-practicas.ver');

    // --- Recomendaciones ---
    Route::get('/recomendaciones',                              [RecomendacionesController::class, 'index'])->name('recomendaciones')->middleware('can:recomendaciones.ver');
    Route::post('/recomendaciones',                             [RecomendacionesController::class, 'store'])->name('recomendaciones.store')->middleware('can:recomendaciones.crear');
    Route::put('/recomendaciones/{recomendacion}',              [RecomendacionesController::class, 'update'])->name('recomendaciones.update')->middleware('can:recomendaciones.editar');
    Route::delete('/recomendaciones/{recomendacion}',           [RecomendacionesController::class, 'destroy'])->name('recomendaciones.destroy')->middleware('can:recomendaciones.editar');
    Route::patch('/recomendaciones/{recomendacion}/atender',    [RecomendacionesController::class, 'marcarAtendida'])->name('recomendaciones.atender')->middleware('can:recomendaciones.editar');

    // --- Ayuda ---
    Route::get('/ayuda', [AyudaController::class, 'index'])->name('ayuda');

    // ── ENCUESTAS ──────────────────────────────────────────────────────────────
    Route::prefix('encuestas')->name('encuestas.')->group(function () {
        Route::get('/',                              [EncuestaController::class, 'index'])    ->name('index')             ->middleware('can:encuesta.ver');
        Route::get('/data',                          [EncuestaController::class, 'data'])     ->name('data');
        Route::get('/crear',                         [EncuestaController::class, 'create'])   ->name('crear')             ->middleware('can:encuesta.crear');
        Route::post('/',                             [EncuestaController::class, 'store'])    ->name('store')             ->middleware('can:encuesta.crear');
        Route::get('/{encuesta}/editar',             [EncuestaController::class, 'edit'])     ->name('editar')            ->middleware('can:encuesta.editar');
        Route::put('/{encuesta}',                    [EncuestaController::class, 'update'])   ->name('update')            ->middleware('can:encuesta.editar');
        Route::delete('/{encuesta}',                 [EncuestaController::class, 'destroy'])  ->name('destroy')           ->middleware('can:encuesta.eliminar');
        Route::post('/{encuesta}/publicar',          [EncuestaController::class, 'publicar']) ->name('publicar')          ->middleware('can:encuesta.publicar');
        Route::post('/{encuesta}/cerrar',            [EncuestaController::class, 'cerrar'])   ->name('cerrar')            ->middleware('can:encuesta.publicar');
        Route::get('/{encuesta}/responder',          [EncuestaRespuestaController::class, 'show'])   ->name('responder')         ->middleware('can:encuesta.responder');
        Route::post('/{encuesta}/responder',         [EncuestaRespuestaController::class, 'store'])  ->name('responder.store')   ->middleware('can:encuesta.responder');
        Route::get('/{encuesta}/resultados',         [EncuestaResultadoController::class, 'index'])  ->name('resultados')        ->middleware('can:encuesta.resultados');
        Route::get('/{encuesta}/resultados/datos',   [EncuestaResultadoController::class, 'datos'])  ->name('resultados.datos')  ->middleware('can:encuesta.resultados');
        Route::get('/{encuesta}/exportar',           [EncuestaResultadoController::class, 'exportar'])->name('exportar')        ->middleware('can:encuesta.exportar');
    });

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
