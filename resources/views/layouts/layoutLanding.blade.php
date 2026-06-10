@php
  $configData = Helper::appClasses();
  $isFront = true;
@endphp

@section('layoutContent')
  @extends('layouts/commonMaster')

  <!-- Landing: sin navbar ni footer genérico de Vuexy -->
  @yield('content')
@endsection
