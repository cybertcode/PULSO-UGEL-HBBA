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

// Locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);

// Auth pages (public)
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');

// PULSO UGEL — módulos protegidos
Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {

  // Dashboard
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

  // Control y Seguimiento  (slugs prefijados con "sci-")
  Route::get('/control-interno', [ControlInternoController::class, 'index'])->name('sci-control-interno');
  Route::get('/modelo-integridad', [ModeloIntegridadController::class, 'index'])->name('sci-modelo-integridad');
  Route::get('/evidencias', [EvidenciasController::class, 'index'])->name('sci-evidencias');

  // Monitoreo  (slugs prefijados con "mon-")
  Route::get('/semaforo', [SemaforoController::class, 'index'])->name('mon-semaforo');
  Route::get('/alertas', [AlertasController::class, 'index'])->name('mon-alertas');
  Route::get('/ranking-unidades', [RankingUnidadesController::class, 'index'])->name('mon-ranking-unidades');

  // Reportes  (slugs prefijados con "rep-")
  Route::get('/reportes', [ReportesController::class, 'index'])->name('rep-reportes');
  Route::get('/reconocimientos', [ReconocimientosController::class, 'index'])->name('rep-reconocimientos');

  // Administración  (slugs prefijados con "adm-")
  Route::get('/usuarios', [UserList::class, 'index'])->name('adm-usuarios');
  Route::get('/usuarios/ver', [UserViewAccount::class, 'index'])->name('adm-usuarios-ver');
  Route::get('/usuarios/seguridad', [UserViewSecurity::class, 'index'])->name('adm-usuarios-seguridad');
  Route::get('/roles', [AccessRoles::class, 'index'])->name('adm-roles');
  Route::get('/permisos', [AccessPermission::class, 'index'])->name('adm-permisos');
  Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('adm-configuracion');
});
