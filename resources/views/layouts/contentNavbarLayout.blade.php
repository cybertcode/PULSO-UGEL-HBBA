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
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;min-width:320px;max-width:420px">

          {{-- Los flash success/error/warning los maneja pulsoToast en scripts.blade.php --}}

          @if(session('warning'))
          <div class="toast show border-0 shadow" role="alert" data-bs-autohide="true" data-bs-delay="7000"
               style="background:#fff;border-left:4px solid #fd7e14 !important;border-radius:8px">
            <div class="toast-header border-0 pb-0" style="background:transparent">
              <span class="me-2" style="color:#fd7e14"><i class="ti tabler-alert-triangle" style="font-size:18px"></i></span>
              <strong class="me-auto" style="color:#fd7e14">Advertencia</strong>
              <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body pt-1" style="color:#333">{{ session('warning') }}</div>
          </div>
          @endif

          @if(session('info'))
          <div class="toast show border-0 shadow" role="alert" data-bs-autohide="true" data-bs-delay="5000"
               style="background:#fff;border-left:4px solid #0d6efd !important;border-radius:8px">
            <div class="toast-header border-0 pb-0" style="background:transparent">
              <span class="me-2" style="color:#0d6efd"><i class="ti tabler-info-circle" style="font-size:18px"></i></span>
              <strong class="me-auto" style="color:#0d6efd">Información</strong>
              <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body pt-1" style="color:#333">{{ session('info') }}</div>
          </div>
          @endif

          @if($errors->any())
          <div class="toast show border-0 shadow" role="alert" data-bs-autohide="false"
               style="background:#fff;border-left:4px solid #dc3545 !important;border-radius:8px">
            <div class="toast-header border-0 pb-0" style="background:transparent">
              <span class="me-2" style="color:#dc3545"><i class="ti tabler-alert-circle" style="font-size:18px"></i></span>
              <strong class="me-auto" style="color:#dc3545">Corrige los errores</strong>
              <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body pt-1" style="color:#333">
              <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
              </ul>
            </div>
          </div>
          @endif

          {{-- Toast desde sessionStorage (para AJAX + reload) --}}
          <div id="toast-flash-js" class="toast border-0 shadow d-none" role="alert" data-bs-autohide="true" data-bs-delay="5000"
               style="background:#fff;border-left:4px solid #28a745 !important;border-radius:8px">
            <div class="toast-header border-0 pb-0" style="background:transparent">
              <span class="me-2" style="color:#28a745"><i class="ti tabler-circle-check" style="font-size:18px"></i></span>
              <strong class="me-auto toast-flash-title" style="color:#28a745">Operación exitosa</strong>
              <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body pt-1 toast-flash-body" style="color:#333"></div>
          </div>

        </div>
        <!-- / Toast Container Global -->

        <script>
        (function () {
          const msg = sessionStorage.getItem('flash_success');
          if (!msg) return;
          sessionStorage.removeItem('flash_success');
          const toastEl = document.getElementById('toast-flash-js');
          const title   = sessionStorage.getItem('flash_title') || 'Operación exitosa';
          sessionStorage.removeItem('flash_title');
          toastEl.querySelector('.toast-flash-title').textContent = title;
          toastEl.querySelector('.toast-flash-body').textContent  = msg;
          toastEl.classList.remove('d-none');
          new bootstrap.Toast(toastEl, { delay: 5000 }).show();
        })();
        </script>

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
