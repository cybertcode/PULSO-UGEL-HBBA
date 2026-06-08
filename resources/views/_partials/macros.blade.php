@php
  $height = $height ?? '32';
@endphp

@if(!empty($configInstitucional?->logo_ruta))
  <img src="{{ \Illuminate\Support\Facades\Storage::url($configInstitucional->logo_ruta) }}"
       height="{{ $height }}" width="auto" alt="logo" class="rounded" style="max-height:{{ $height }}px;width:auto;">
@endif
