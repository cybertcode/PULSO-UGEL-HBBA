@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

$authUser       = Auth::user();
$userFoto       = $authUser?->profile_photo_path
                    ? Storage::url($authUser->profile_photo_path)
                    : null;
$userInitials   = $authUser ? strtoupper(substr($authUser->name, 0, 1) . (strpos($authUser->name,' ')!==false ? substr($authUser->name, strpos($authUser->name,' ')+1, 1) : '')) : 'U';
$userCargo      = $authUser?->cargo ?? 'Usuario';
$userUnidad     = $authUser?->unidadOrganica?->nombre ?? null;
$userRol        = $authUser?->roles->first()?->name ?? null;

// Alertas reales para el dropdown de notificaciones
$alertasDropdown = $authUser
    ? \App\Models\Alerta::where('leida', false)
        ->with('actividad:id,nombre')
        ->latest()
        ->take(5)
        ->get()
    : collect();
$totalAlertas = $alertasDropdown->count();
@endphp

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
<div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
  <a href="{{ url('/') }}" class="app-brand-link">
    @if(!empty($configInstitucional?->logo_ruta))
      <span class="app-brand-logo demo">
        <img src="{{ Storage::url($configInstitucional->logo_ruta) }}" height="28" alt="logo" class="rounded">
      </span>
      <span class="app-brand-text demo menu-text fw-bold" style="font-size:.85rem">
        {{ $configInstitucional->sigla ?? $configInstitucional->nombre_institucion }}
      </span>
    @else
      <span class="app-brand-logo demo">@include('_partials.macros')</span>
      <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName') }}</span>
    @endif
  </a>
  @if (isset($menuHorizontal))
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
    <i class="icon-base ti tabler-x icon-sm d-flex align-items-center justify-content-center"></i>
  </a>
  @endif
</div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
  <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
    <i class="icon-base ti tabler-menu-2 icon-md"></i>
  </a>
</div>
@endif

<div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

  @if (!isset($menuHorizontal))
  <!-- Search -->
  <div class="navbar-nav align-items-center">
    <div class="nav-item navbar-search-wrapper px-md-0 px-2 mb-0">
      <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
      </a>
    </div>
  </div>
  @endif

  <ul class="navbar-nav flex-row align-items-center ms-md-auto">
    @if (isset($menuHorizontal))
    <li class="nav-item navbar-search-wrapper btn btn-text-secondary btn-icon rounded-pill">
      <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
      </a>
    </li>
    @endif

    <!-- Tema claro/oscuro -->
    @if ($configData['hasCustomizer'] == true)
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill" id="nav-theme"
        href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <button type="button" class="dropdown-item active" data-bs-theme-value="light">
            <i class="icon-base ti tabler-sun icon-22px me-3"></i>Claro
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item" data-bs-theme-value="dark">
            <i class="icon-base ti tabler-moon-stars icon-22px me-3"></i>Oscuro
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item" data-bs-theme-value="system">
            <i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"></i>Sistema
          </button>
        </li>
      </ul>
    </li>
    @endif

    <!-- Accesos rápidos PULSO -->
    <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside">
        <i class="icon-base ti tabler-layout-grid-add icon-22px text-heading"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end p-0">
        <div class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">Accesos Rápidos</h6>
          </div>
        </div>
        <div class="dropdown-shortcuts-list scrollable-container">
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-smart-home icon-26px text-heading"></i>
              </span>
              <a href="{{ route('dashboard') }}" class="stretched-link">Dashboard</a>
              <small>Panel principal</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-clipboard-list icon-26px text-heading"></i>
              </span>
              <a href="{{ route('sci-control-interno') }}" class="stretched-link">Control Interno</a>
              <small>Actividades SCI</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-award icon-26px text-heading"></i>
              </span>
              <a href="{{ route('mon-ranking-unidades') }}" class="stretched-link">Ranking</a>
              <small>Unidades</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-file-analytics icon-26px text-heading"></i>
              </span>
              <a href="{{ route('rep-reportes') }}" class="stretched-link">Reportes</a>
              <small>PDF / Excel</small>
            </div>
          </div>
          @can('configuracion.ver')
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-settings icon-26px text-heading"></i>
              </span>
              <a href="{{ route('adm-configuracion') }}" class="stretched-link">Configuración</a>
              <small>Sistema</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-users icon-26px text-heading"></i>
              </span>
              <a href="{{ route('adm-usuarios') }}" class="stretched-link">Usuarios</a>
              <small>Gestión</small>
            </div>
          </div>
          @endcan
        </div>
      </div>
    </li>

    <!-- Notificaciones — alertas reales del sistema -->
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside">
        <span class="position-relative">
          <i class="icon-base ti tabler-bell icon-22px text-heading"></i>
          @if($totalAlertas > 0)
          <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
          @endif
        </span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end p-0">
        <li class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">Alertas del Sistema</h6>
            @if($totalAlertas > 0)
            <span class="badge bg-label-danger rounded-pill me-2">{{ $totalAlertas }} nuevas</span>
            @endif
            <a href="{{ route('mon-alertas') }}" class="dropdown-notifications-all p-2 btn btn-icon"
               data-bs-toggle="tooltip" title="Ver todas">
              <i class="icon-base ti tabler-external-link text-heading icon-18px"></i>
            </a>
          </div>
        </li>
        <li class="dropdown-notifications-list scrollable-container">
          <ul class="list-group list-group-flush">
            @forelse($alertasDropdown as $alerta)
            @php
              $aIcon  = match($alerta->tipo) {
                'vencimiento'     => 'tabler-calendar-x',
                'avance_bajo'     => 'tabler-trending-down',
                'evidencia_falta' => 'tabler-file-off',
                default           => 'tabler-bell',
              };
              $aColor = match($alerta->prioridad) {
                'alta'  => 'danger',
                'media' => 'warning',
                default => 'info',
              };
            @endphp
            <li class="list-group-item list-group-item-action dropdown-notifications-item">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar">
                    <span class="avatar-initial rounded-circle bg-label-{{ $aColor }}">
                      <i class="icon-base ti {{ $aIcon }}"></i>
                    </span>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="small mb-1 fw-semibold">{{ $alerta->titulo }}</h6>
                  @if($alerta->actividad)
                  <small class="mb-1 d-block text-body text-truncate" style="max-width:200px">{{ $alerta->actividad->nombre }}</small>
                  @endif
                  <small class="text-body-secondary">{{ $alerta->created_at->diffForHumans() }}</small>
                </div>
                <div class="flex-shrink-0 dropdown-notifications-actions">
                  <form method="POST" action="{{ route('mon-alertas.leer', $alerta) }}" style="display:inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="dropdown-notifications-read btn p-0 border-0 bg-transparent"
                      data-bs-toggle="tooltip" title="Marcar leída">
                      <span class="badge badge-dot"></span>
                    </button>
                  </form>
                </div>
              </div>
            </li>
            @empty
            <li class="list-group-item text-center py-4 text-body-secondary">
              <i class="ti tabler-bell-off icon-28px d-block mb-2 text-success"></i>
              <small>Sin alertas pendientes</small>
            </li>
            @endforelse
          </ul>
        </li>
        <li class="border-top">
          <div class="d-grid p-3">
            <a class="btn btn-primary btn-sm" href="{{ route('mon-alertas') }}">
              <i class="ti tabler-bell me-1 icon-14px"></i>Ver todas las alertas
            </a>
          </div>
        </li>
      </ul>
    </li>
    <!--/ Notificaciones -->

    <!-- Usuario autenticado -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          @if($userFoto)
            <img src="{{ $userFoto }}" alt="{{ $authUser->name }}" class="rounded-circle w-px-40 h-px-40" style="object-fit:cover">
          @else
            <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ $userInitials }}</span>
          @endif
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        {{-- Cabecera con info completa --}}
        <li>
          <a class="dropdown-item mt-0" href="{{ Route::has('profile.show') ? route('profile.show') : 'javascript:void(0);' }}">
            <div class="d-flex align-items-center gap-3">
              <div class="flex-shrink-0">
                <div class="avatar avatar-online">
                  @if($userFoto)
                    <img src="{{ $userFoto }}" alt="{{ $authUser->name }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover">
                  @else
                    <span class="avatar-initial rounded-circle bg-label-primary fw-bold" style="width:40px;height:40px;font-size:15px">{{ $userInitials }}</span>
                  @endif
                </div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0 fw-semibold">{{ $authUser?->name ?? 'Usuario' }}</h6>
                <small class="text-body-secondary">{{ $userCargo }}</small>
                @if($userUnidad)
                <div><small class="text-muted" style="font-size:10px"><i class="ti tabler-building icon-10px me-1"></i>{{ $userUnidad }}</small></div>
                @endif
                @if($userRol)
                <span class="badge bg-label-primary rounded-pill mt-1" style="font-size:9px">{{ $userRol }}</span>
                @endif
              </div>
            </div>
          </a>
        </li>
        <li><div class="dropdown-divider my-1 mx-n2"></div></li>

        {{-- Mis datos --}}
        <li>
          <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : 'javascript:void(0);' }}">
            <i class="icon-base ti tabler-user-circle me-3 icon-md"></i><span>Mi Perfil</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('mon-alertas') }}">
            <i class="icon-base ti tabler-bell me-3 icon-md"></i>
            <span>Mis Alertas</span>
            @if($totalAlertas > 0)
            <span class="badge bg-danger rounded-pill ms-auto">{{ $totalAlertas }}</span>
            @endif
          </a>
        </li>

        @can('configuracion.ver')
        <li>
          <a class="dropdown-item" href="{{ route('adm-configuracion') }}">
            <i class="icon-base ti tabler-settings me-3 icon-md"></i><span>Configuración</span>
          </a>
        </li>
        @endcan

        <li><div class="dropdown-divider my-1 mx-n2"></div></li>

        {{-- Sesión --}}
        @if(Auth::check())
        <li>
          <a class="dropdown-item text-danger" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="icon-base ti tabler-logout me-3 icon-md"></i><span>Cerrar Sesión</span>
          </a>
        </li>
        <form method="POST" id="logout-form" action="{{ route('logout') }}">@csrf</form>
        @endif
      </ul>
    </li>
    <!--/ Usuario -->
  </ul>
</div>
