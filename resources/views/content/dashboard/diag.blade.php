@extends('layouts/layoutMaster')
@section('title', 'Diagnóstico Customizer')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card border-primary">
    <div class="card-header bg-primary text-white"><h5 class="mb-0 text-white">Diagnóstico del Template Customizer</h5></div>
    <div class="card-body">
      <pre id="diag-output" style="background:#1e1e1e;color:#d4d4d4;padding:1rem;border-radius:8px;font-size:13px;line-height:1.6">Ejecutando diagnóstico...</pre>
    </div>
  </div>
  <div class="card mt-4">
    <div class="card-header"><h5 class="mb-0">Errores de Consola JS capturados</h5></div>
    <div class="card-body">
      <pre id="errors-output" style="background:#1e1e1e;color:#ff6b6b;padding:1rem;border-radius:8px;font-size:13px;line-height:1.6">Ninguno capturado aún...</pre>
    </div>
  </div>
</div>
@endsection
@section('page-script')
<script>
// Capture errors BEFORE DOMContentLoaded
var _errors = [];
var _origError = window.onerror;
window.onerror = function(msg, src, line, col, err) {
  _errors.push('[ERROR] ' + msg + ' (' + src + ':' + line + ')');
  if (_origError) return _origError(msg, src, line, col, err);
};
var _origUnhandled = window.onunhandledrejection;
window.addEventListener('unhandledrejection', function(e) {
  _errors.push('[PROMISE REJECTION] ' + (e.reason || e));
});

document.addEventListener('DOMContentLoaded', function() {
  setTimeout(function() {
    var out = [];
    out.push('=== GLOBALS ===');
    out.push('window.TemplateCustomizer : ' + typeof window.TemplateCustomizer);
    out.push('window.templateCustomizer : ' + typeof window.templateCustomizer);
    out.push('window.Helpers            : ' + typeof window.Helpers);
    out.push('window.Pickr              : ' + typeof window.Pickr);
    out.push('');
    out.push('=== HTML ELEMENT ===');
    out.push('html.className   : "' + document.documentElement.className + '"');
    out.push('html.data-skin   : ' + document.documentElement.getAttribute('data-skin'));
    out.push('html.data-template: ' + document.documentElement.getAttribute('data-template'));
    out.push('');
    out.push('=== DOM ELEMENTS ===');
    var custEl = document.querySelector('#template-customizer');
    out.push('#template-customizer : ' + (custEl ? 'EXISTS' : 'NOT FOUND'));
    if (custEl) {
      var cs = window.getComputedStyle(custEl);
      out.push('  style attr    : ' + (custEl.getAttribute('style') || '(none)'));
      out.push('  computed display   : ' + cs.display);
      out.push('  computed visibility: ' + cs.visibility);
      out.push('  computed transform : ' + cs.transform);
      out.push('  computed zIndex    : ' + cs.zIndex);
      out.push('  offsetWidth  : ' + custEl.offsetWidth);
      out.push('  offsetHeight : ' + custEl.offsetHeight);
    }
    var btn = document.querySelector('.template-customizer-open-btn');
    out.push('');
    out.push('.template-customizer-open-btn : ' + (btn ? 'EXISTS' : 'NOT FOUND'));
    if (btn) {
      var bs = window.getComputedStyle(btn);
      out.push('  computed display   : ' + bs.display);
      out.push('  computed visibility: ' + bs.visibility);
      out.push('  computed transform : ' + bs.transform);
      out.push('  computed zIndex    : ' + bs.zIndex);
      out.push('  getBoundingClientRect: ' + JSON.stringify(btn.getBoundingClientRect()));
    }
    out.push('');
    out.push('=== VIEWPORT ===');
    out.push('window.innerWidth  : ' + window.innerWidth);
    out.push('window.innerHeight : ' + window.innerHeight);
    out.push('');
    out.push('=== TEMPLATE CUSTOMIZER INSTANCE ===');
    if (window.templateCustomizer) {
      out.push('settings.displayCustomizer: ' + window.templateCustomizer.settings.displayCustomizer);
      out.push('settings.defaultTheme     : ' + window.templateCustomizer.settings.defaultTheme);
    } else {
      out.push('NO INSTANCE (window.templateCustomizer is undefined)');
    }

    document.getElementById('diag-output').textContent = out.join('\n');

    // Update errors
    if (_errors.length > 0) {
      document.getElementById('errors-output').textContent = _errors.join('\n');
    } else {
      document.getElementById('errors-output').textContent = 'Ningún error de JS capturado.';
    }
  }, 1500); // Wait 1.5s to let all scripts finish
});
</script>
@endsection
