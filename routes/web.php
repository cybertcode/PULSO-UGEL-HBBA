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

// PULSO UGEL - Módulos protegidos
Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('pages-home');

  // Control y Seguimiento
  Route::get('/control-interno', [ControlInternoController::class, 'index'])->name('control-interno');
  Route::get('/modelo-integridad', [ModeloIntegridadController::class, 'index'])->name('modelo-integridad');
  Route::get('/evidencias', [EvidenciasController::class, 'index'])->name('evidencias');

  // Monitoreo
  Route::get('/semaforo', [SemaforoController::class, 'index'])->name('semaforo');
  Route::get('/alertas', [AlertasController::class, 'index'])->name('alertas');
  Route::get('/ranking-unidades', [RankingUnidadesController::class, 'index'])->name('ranking-unidades');

  // Reportes
  Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');
  Route::get('/reconocimientos', [ReconocimientosController::class, 'index'])->name('reconocimientos');

  // Administración - Usuarios
  Route::get('/usuarios', [UserList::class, 'index'])->name('usuarios');
  Route::get('/usuarios/ver', [UserViewAccount::class, 'index'])->name('usuarios-ver');
  Route::get('/usuarios/seguridad', [UserViewSecurity::class, 'index'])->name('usuarios-seguridad');

  // Administración - Acceso
  Route::get('/roles', [AccessRoles::class, 'index'])->name('roles');
  Route::get('/permisos', [AccessPermission::class, 'index'])->name('permisos');

  Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
});
