@isset($pageConfigs)
  {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@php
  $configData = Helper::appClasses();
@endphp
@extends('layouts/commonMaster')

@php
  /* Display elements */
  $contentNavbar = $contentNavbar ?? true;
  $containerNav = $containerNav ?? 'container-xxl';
  $isNavbar = $isNavbar ?? true;
  $isMenu = $isMenu ?? true;
  $isFlex = $isFlex ?? false;
  $isFooter = $isFooter ?? true;
  $customizerHidden = $customizerHidden ?? '';

  /* HTML Classes */
  $navbarDetached = 'navbar-detached';
  $menuFixed = isset($configData['menuFixed']) ? $configData['menuFixed'] : '';
  if (isset($navbarType)) {
      $configData['navbarType'] = $navbarType;
  }
  $navbarType = isset($configData['navbarType']) ? $configData['navbarType'] : '';
  $footerFixed = isset($configData['footerFixed']) ? $configData['footerFixed'] : '';
  $menuCollapsed = isset($configData['menuCollapsed']) ? $configData['menuCollapsed'] : '';

  /* Content classes */
  $container =
      isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
          ? 'container-xxl'
          : 'container-fluid';

@endphp

@section('layoutContent')
  <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
    <div class="layout-container">

      @if ($isMenu)
        @include('layouts/sections/menu/verticalMenu')
      @endif

      <!-- Layout page -->
      <div class="layout-page">

        {{-- Below commented code read by artisan command while installing jetstream. !! Do not remove if you want to use jetstream. --}}
        {{-- <x-banner /> --}}

        <!-- BEGIN: Navbar-->
        @if ($isNavbar)
          @include('layouts/sections/navbar/navbar')
        @endif
        <!-- END: Navbar-->

        <!-- Content wrapper -->
        <div class="content-wrapper">

          <!-- Content -->
          @if ($isFlex)
            <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
            @else
              <div class="{{ $container }} flex-grow-1 container-p-y">
          @endif

          @yield('content')

        </div>
        <!-- / Content -->

        <!-- Footer -->
        @if ($isFooter)
          @include('layouts/sections/footer/footer')
        @endif
        <!-- / Footer -->

        <!-- Toast Container Global -->
        <div class="bs-toast toast-placement-ex m-4" style="position:fixed;top:0;right:0;z-index:9999;min-width:320px">
          @if(session('success'))
          <div class="toast align-items-center text-bg-success border-0 show" role="alert" id="toast-global">
            <div class="d-flex">
              <div class="toast-body d-flex align-items-center gap-2">
                <i class="ti tabler-circle-check icon-20px flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
              </div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
          </div>
          @elseif(session('error'))
          <div class="toast align-items-center text-bg-danger border-0 show" role="alert" id="toast-global">
            <div class="d-flex">
              <div class="toast-body d-flex align-items-center gap-2">
                <i class="ti tabler-circle-x icon-20px flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
              </div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
          </div>
          @elseif(session('warning'))
          <div class="toast align-items-center text-bg-warning border-0 show" role="alert" id="toast-global">
            <div class="d-flex">
              <div class="toast-body d-flex align-items-center gap-2">
                <i class="ti tabler-alert-triangle icon-20px flex-shrink-0"></i>
                <span>{{ session('warning') }}</span>
              </div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
          </div>
          @elseif(session('info'))
          <div class="toast align-items-center text-bg-primary border-0 show" role="alert" id="toast-global">
            <div class="d-flex">
              <div class="toast-body d-flex align-items-center gap-2">
                <i class="ti tabler-info-circle icon-20px flex-shrink-0"></i>
                <span>{{ session('info') }}</span>
              </div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
          </div>
          @endif
        </div>
        <!-- / Toast Container Global -->

        <div class="content-backdrop fade"></div>
      </div>
      <!--/ Content wrapper -->
    </div>
    <!-- / Layout page -->
  </div>

  @if ($isMenu)
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  @endif
  <!-- Drag Target Area To SlideIn Menu On Small Screens -->
  <div class="drag-target"></div>
  </div>
  <!-- / Layout wrapper -->
@endsection
