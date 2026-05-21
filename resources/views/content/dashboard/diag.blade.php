@extends('layouts/layoutMaster')
@section('title', 'Diagnóstico')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-body">
      <h5>Diagnóstico del Customizer</h5>
      <pre id="diag-output">Cargando...</pre>
    </div>
  </div>
</div>
@endsection
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var out = [];
  out.push('window.TemplateCustomizer: ' + (typeof window.TemplateCustomizer));
  out.push('window.templateCustomizer: ' + (typeof window.templateCustomizer));
  out.push('window.Helpers: ' + (typeof window.Helpers));
  out.push('#template-customizer: ' + (document.querySelector('#template-customizer') ? 'EXISTS' : 'NOT FOUND'));
  out.push('.template-customizer-open-btn: ' + (document.querySelector('.template-customizer-open-btn') ? 'EXISTS' : 'NOT FOUND'));
  var html = document.documentElement;
  out.push('html classes: ' + html.className);
  
  if(document.querySelector('#template-customizer')) {
    var el = document.querySelector('#template-customizer');
    var style = window.getComputedStyle(el);
    out.push('#template-customizer display: ' + style.display);
    out.push('#template-customizer visibility: ' + style.visibility);
    out.push('#template-customizer style attr: ' + el.getAttribute('style'));
  }
  if(document.querySelector('.template-customizer-open-btn')) {
    var btn = document.querySelector('.template-customizer-open-btn');
    var bstyle = window.getComputedStyle(btn);
    out.push('open-btn display: ' + bstyle.display);
    out.push('open-btn visibility: ' + bstyle.visibility);
    out.push('open-btn transform: ' + bstyle.transform);
  }
  document.getElementById('diag-output').textContent = out.join('\n');
});
</script>
@endsection
