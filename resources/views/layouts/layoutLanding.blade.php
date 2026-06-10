@php
  $configData = Helper::appClasses();
  $configData['hasCustomizer'] = false;
  $configData['displayCustomizer'] = false;
  $isFront = true;
@endphp

@section('layoutContent')
  @extends('layouts/commonMaster')

  <!-- Landing: sin navbar ni footer genérico de Vuexy -->
  @yield('content')
@endsection
