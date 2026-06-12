@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Reutiliza el mismo mapa de permisos definido en verticalMenu
$menuPermisosLocal = [
  'sci-control-interno'         => 'control-interno.ver',
  'sci-evidencias'              => 'evidencias.ver',
  'cumplimiento.panel'          => 'cumplimiento.ver',
  'cumplimiento.responsables'   => 'cumplimiento.ver',
  'cumplimiento.sin-evidencia'  => 'cumplimiento.ver',
  'mon-avance-unidades'         => 'reportes.ver',
  'mon-ranking-unidades'        => 'reportes.ver',
  'sci-modelo-integridad'       => 'integridad.ver',
  'sci-semaforo'                => 'semaforo.ver',
  'buenas-practicas'            => 'buenas-practicas.ver',
  'recomendaciones'             => 'recomendaciones.ver',
  'rep-reconocimientos'         => 'reconocimientos.ver',
  'mon-alertas'                 => 'alertas.ver',
  'rep-reportes'                => 'reportes.ver',
  'adm-usuarios'                => 'usuarios.ver',
  'adm-roles'                   => 'roles.ver',
  'adm-permisos'                => 'roles.ver',
  'adm-unidades'                => 'unidades.ver',
  'adm-componentes'             => 'componentes.ver',
  'adm-sci-estructura'          => 'componentes.ver',
  'adm-integridad-estructura'   => 'integridad.ver',
  'adm-configuracion'           => 'configuracion.ver',
  'slider-landing'              => 'slider.ver',
  'encuestas.index'             => 'encuesta.ver',
  'normativas'                  => 'normativas.ver',
];
$subAuthUser = Auth::user();
@endphp

<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)

    @php
      // Verificar permiso del subítem
      $subSlug = is_array($submenu->slug ?? null) ? ($submenu->slug[0] ?? '') : ($submenu->slug ?? '');
      $subVisible = !$subAuthUser || !isset($menuPermisosLocal[$subSlug]) || $subAuthUser->can($menuPermisosLocal[$subSlug]);
    @endphp
    @if($subVisible)

    {{-- active menu method --}}
    @php
      $activeClass = null;
      $active = $configData["layout"] === 'vertical' ? 'active open':'active';
      $currentRouteName =  Route::currentRouteName();

      if ($currentRouteName === $submenu->slug) {
          $activeClass = 'active';
      }
      elseif (isset($submenu->submenu)) {
        if (gettype($submenu->slug) === 'array') {
          foreach($submenu->slug as $slug){
            if (str_contains($currentRouteName,$slug) and strpos($currentRouteName,$slug) === 0) {
                $activeClass = $active;
            }
          }
        }
        else{
          if (str_contains($currentRouteName,$submenu->slug) and strpos($currentRouteName,$submenu->slug) === 0) {
            $activeClass = $active;
          }
        }
      }
    @endphp

      <li class="menu-item {{$activeClass}}">
        <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}" class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
          @if (isset($submenu->icon))
          <i class="{{ $submenu->icon }}"></i>
          @endif
          <div class="d-flex flex-column lh-1">
            <span>{{ isset($submenu->name) ? __($submenu->name) : '' }}</span>
            @isset($submenu->i18n)
            <small class="text-muted fw-normal" style="font-size:10px;opacity:.75">{{ __($submenu->i18n) }}</small>
            @endisset
          </div>
          @isset($submenu->badge)
            <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ $submenu->badge[1] }}</div>
          @endisset
        </a>

        {{-- submenu --}}
        @if (isset($submenu->submenu))
          @include('layouts.sections.menu.submenu',['menu' => $submenu->submenu])
        @endif
      </li>
    @endif
    @endforeach
  @endif
</ul>
