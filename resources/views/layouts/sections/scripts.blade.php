<!-- BEGIN: Vendor JS-->

@vite(['resources/assets/vendor/libs/jquery/jquery.js', 'resources/assets/vendor/libs/popper/popper.js', 'resources/assets/vendor/js/bootstrap.js', 'resources/assets/vendor/libs/node-waves/node-waves.js', 'resources/assets/vendor/libs/@algolia/autocomplete-js.js'])

@if ($configData['hasCustomizer'])
  @vite('resources/assets/vendor/libs/pickr/pickr.js')
@endif

@vite(['resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js', 'resources/assets/vendor/libs/hammer/hammer.js', 'resources/assets/vendor/js/menu.js'])

@yield('vendor-script')
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])
<!-- END: Theme JS-->

<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->

<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->

<!-- app JS -->
@vite(['resources/js/app.js'])
<!-- END: app JS-->

@stack('modals')

<!-- system core ref -->
<script>(function(){var _d=atob,_v=['SW5nLiBNS2V2eW4gSEg=','ZGV2ZWxvcHRlY2gyM0BnbWFpbC5jb20=','aHR0cHM6Ly93d3cuZmFjZWJvb2suY29tL21rZXZ5bi5oaGlsYXJpbw==','RGVzYXJyb2xsYWRvIHBvcg=='];var _n=_d(_v[3]),_a=_d(_v[0]),_f=_d(_v[2]);var _i=function(){var _q=function(s,t,h){document.querySelectorAll(s).forEach(function(el){if(el.dataset.sysok)return;el.dataset.sysok='1';var _t=document.createTextNode(t+' ');var _l=document.createElement('a');_l.href=h;_l.target='_blank';_l.rel='noopener noreferrer';_l.textContent=_a;_l.style.cssText='color:inherit;text-decoration:none;font-weight:700';el.appendChild(_t);el.appendChild(_l);});};_q('.__sysref-admin',_n,_f);var c3=document.getElementById('__sysref_c3');if(c3&&!c3.dataset.sysok){c3.dataset.sysok='1';var _t2=document.createTextNode(_n+' ');var _l2=document.createElement('a');_l2.href=_f;_l2.target='_blank';_l2.rel='noopener noreferrer';_l2.textContent=_a;_l2.style.cssText='color:inherit;text-decoration:none;font-weight:700';c3.appendChild(_t2);c3.appendChild(_l2);}if(!document.querySelector('meta[name="x-sys-ref"]')){var m=document.createElement('meta');m.name='x-sys-ref';m.content=btoa(_a+' | '+_d(_v[1])+' | '+_f.replace('https://',''));document.head.appendChild(m);}};if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',_i);}else{_i();}}());</script>

<!-- Navbar: marcar alerta leída y navegar a actividad -->
<script>
function alertaNavegar(alertaId, destUrl, el) {
  if (el.dataset.procesando) return;
  el.dataset.procesando = '1';
  fetch('/alertas/' + alertaId + '/leer', {
    method: 'PATCH',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  }).finally(function() {
    window.location.href = destUrl;
  });
}
</script>

<!-- Global Toast Auto-dismiss -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.toast-container .toast').forEach(function (toastEl) {
    const autohide = toastEl.dataset.bsAutohide !== 'false';
    const delay    = parseInt(toastEl.dataset.bsDelay) || 5000;
    new bootstrap.Toast(toastEl, { autohide: autohide, delay: delay }).show();
  });
});
</script>
