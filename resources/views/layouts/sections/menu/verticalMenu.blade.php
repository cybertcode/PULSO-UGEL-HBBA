@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
$configData = Helper::appClasses();

$authUser     = Auth::user();
$userFoto     = $authUser?->profile_photo_path ? Storage::url($authUser->profile_photo_path) : null;
$userInitials = $authUser ? strtoupper(
    substr($authUser->name, 0, 1) .
    (strpos($authUser->name,' ') !== false ? substr($authUser->name, strpos($authUser->name,' ')+1, 1) : '')
) : 'U';
$userCargo    = $authUser?->cargo ?? 'Usuario';
$userUnidad   = $authUser?->unidadOrganica?->nombre ?? null;
$userRol      = $authUser?->roles->first()?->name ?? null;
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu" @foreach ($configData['menuAttributes'] as $attribute=>
  $value)
  {{ $attribute }}="{{ $value }}" @endforeach>

  <!-- Brand -->
  @if (!isset($navbarFull))
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
      @if(!empty($configInstitucional?->logo_ruta))
        <span class="app-brand-logo demo">
          <img src="{{ Storage::url($configInstitucional->logo_ruta) }}" height="28" alt="logo" class="rounded">
        </span>
        <span class="app-brand-text demo menu-text fw-bold ms-2" style="font-size:.85rem;line-height:1.2">
          {{ $configInstitucional->sigla ?? $configInstitucional->nombre_institucion }}
        </span>
      @else
        <span class="app-brand-logo demo">@include('_partials.macros')</span>
        <span class="app-brand-text demo menu-text fw-bold ms-3">PULSO UGEL</span>
      @endif
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
      <i class="icon-base ti tabler-x d-block d-xl-none"></i>
    </a>
  </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuData[0]->menu as $menu)

    @if (isset($menu->menuHeader))
    <li class="menu-header small">
      <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
    </li>
    @else
    @php
    $activeClass = null;
    $currentRouteName = Route::currentRouteName();

    if ($currentRouteName === $menu->slug) {
      $activeClass = 'active';
    } elseif (isset($menu->submenu)) {
      if (gettype($menu->slug) === 'array') {
        foreach ($menu->slug as $slug) {
          if ($currentRouteName === $slug || str_starts_with($currentRouteName, $slug . '.')) {
            $activeClass = 'active open';
          }
        }
      } else {
        if (str_contains($currentRouteName, $menu->slug) and strpos($currentRouteName, $menu->slug) === 0) {
          $activeClass = 'active open';
        }
      }
    }
    @endphp

    <li class="menu-item {{ $activeClass }}">
      <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
        class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
        @if(isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
        @isset($menu->icon)
        <i class="{{ $menu->icon }}"></i>
        @endisset
        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
        @isset($menu->badge)
        <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
        @endisset
      </a>
      @isset($menu->submenu)
      @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
      @endisset
    </li>
    @endif
    @endforeach
  </ul>

  {{-- ── Perfil del usuario en la parte inferior del menú ── --}}
  @if($authUser)
  <div class="menu-user-profile" style="padding:1rem 1.25rem;border-top:1px solid var(--bs-border-color);margin-top:auto">
    <a href="{{ Route::has('profile.show') ? route('profile.show') : 'javascript:void(0);' }}"
       class="d-flex align-items-center gap-3 text-decoration-none" style="min-width:0">
      {{-- Avatar --}}
      <div class="flex-shrink-0">
        @if($userFoto)
          <img src="{{ $userFoto }}" alt="{{ $authUser->name }}"
               class="rounded-circle" style="width:38px;height:38px;object-fit:cover;border:2px solid var(--bs-border-color)">
        @else
          <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
               style="width:38px;height:38px;background:linear-gradient(135deg,var(--bs-primary),rgba(var(--bs-primary-rgb),.7));color:#fff;font-size:14px;flex-shrink:0">
            {{ $userInitials }}
          </div>
        @endif
      </div>
      {{-- Datos --}}
      <div class="flex-grow-1 overflow-hidden menu-user-text">
        <div class="fw-semibold text-heading text-truncate" style="font-size:13px">{{ $authUser->name }}</div>
        <div class="text-truncate" style="font-size:11px;color:var(--bs-secondary-color)">{{ $userCargo }}</div>
        @if($userUnidad)
        <div class="text-truncate" style="font-size:10px;color:var(--bs-secondary-color);opacity:.75">
          <i class="ti tabler-building" style="font-size:10px"></i> {{ $userUnidad }}
        </div>
        @endif
      </div>
      {{-- Icono de perfil --}}
      <div class="flex-shrink-0 menu-user-text">
        <i class="ti tabler-chevron-right" style="font-size:14px;color:var(--bs-secondary-color)"></i>
      </div>
    </a>
  </div>
  @endif

</aside>
