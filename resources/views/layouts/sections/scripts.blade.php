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

function notifBDNavegar(notifId, destUrl, el) {
  if (el.dataset.procesando) return;
  el.dataset.procesando = '1';
  fetch('/notifications/' + notifId + '/read', {
    method: 'PATCH',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  }).finally(function() {
    window.location.href = destUrl;
  });
}

function marcarTodasLeidasBD(btn) {
  btn.disabled = true;
  fetch('/notifications/read-all', {
    method: 'PATCH',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  }).then(function() {
    // Remover todos los items de notificaciones de BD del dropdown y el badge
    document.querySelectorAll('.dropdown-notifications-item').forEach(function(el) {
      if (el.querySelector('[onclick*="notifBDNavegar"]')) el.remove();
    });
    btn.closest('li').remove();
    var badge = document.querySelector('.badge-notifications');
    if (badge) badge.remove();
  }).catch(function() {
    btn.disabled = false;
  });
}
</script>

<!-- Flash PHP → sessionStorage → toast-flash-js (ver contentNavbarLayout.blade.php) -->

<!-- pulsoToast: toast global reutilizable en todos los módulos -->
<script>
/**
 * pulsoToast(msg, type, title, delay)
 * type: 'success' | 'error' | 'warning' | 'info'
 * Crea un toast Bootstrap en el .toast-container del layout.
 */
window.pulsoToast = function(msg, type, title, delay) {
  type  = type  || 'success';
  delay = delay || (type === 'error' ? 7000 : type === 'warning' ? 6000 : 4000);

  var cfg = {
    success: { color: '#28a745', icon: 'tabler-circle-check',  def: 'Operación exitosa' },
    error:   { color: '#dc3545', icon: 'tabler-circle-x',      def: 'Error'              },
    warning: { color: '#fd7e14', icon: 'tabler-alert-triangle', def: 'Advertencia'       },
    info:    { color: '#0d6efd', icon: 'tabler-info-circle',    def: 'Información'       },
  };
  var c = cfg[type] || cfg.success;
  title = title || c.def;

  var el = document.createElement('div');
  el.className = 'toast border-0 shadow';
  el.setAttribute('role', 'alert');
  el.style.cssText = 'background:#fff;border-left:4px solid ' + c.color + ' !important;border-radius:8px;min-width:280px';
  el.innerHTML =
    '<div class="toast-header border-0 pb-0" style="background:transparent">' +
      '<span class="me-2" style="color:' + c.color + '"><i class="ti ' + c.icon + '" style="font-size:18px"></i></span>' +
      '<strong class="me-auto" style="color:' + c.color + '">' + title + '</strong>' +
      '<button type="button" class="btn-close" data-bs-dismiss="toast"></button>' +
    '</div>' +
    '<div class="toast-body pt-1" style="color:#333">' + msg + '</div>';

  var container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.cssText = 'z-index:9999;min-width:320px;max-width:420px';
    document.body.appendChild(container);
  }
  container.appendChild(el);
  el.addEventListener('hidden.bs.toast', function() { el.remove(); });
  new bootstrap.Toast(el, { autohide: true, delay: delay }).show();
};

/**
 * pulsoConfirm(opts) → Promise<boolean>
 * opts: { title, html, confirmText, cancelText, type }
 * Usa SweetAlert2 si disponible, si no un confirm() nativo.
 */
window.pulsoConfirm = function(opts) {
  opts = opts || {};
  if (typeof Swal !== 'undefined') {
    return Swal.fire({
      title:             opts.title       || '¿Confirmar acción?',
      html:              opts.html        || '',
      icon:              opts.type        || 'warning',
      showCancelButton:  true,
      confirmButtonColor: opts.type === 'danger' || !opts.type ? '#d33' : undefined,
      confirmButtonText: opts.confirmText || 'Confirmar',
      cancelButtonText:  opts.cancelText  || 'Cancelar',
    }).then(function(r) { return r.isConfirmed; });
  }
  // Fallback sin Swal
  var msg = (opts.title || '') + (opts.html ? '\n' + opts.html.replace(/<[^>]+>/g,'') : '');
  return Promise.resolve(window.confirm(msg));
};
</script>
