@php $uid = $slide?->id ?? 'new'; @endphp

<div class="row g-3">

  {{-- ── FILA 1: Tipo + Etiqueta + Autor ── --}}
  <div class="col-md-4">
    <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
    <select name="tipo" class="form-select" required>
      <option value="noticia"   {{ old('tipo', $slide?->tipo) === 'noticia'   ? 'selected' : '' }}>📰 Noticia</option>
      <option value="evento"    {{ old('tipo', $slide?->tipo) === 'evento'    ? 'selected' : '' }}>📅 Evento</option>
      <option value="normativa" {{ old('tipo', $slide?->tipo) === 'normativa' ? 'selected' : '' }}>📋 Normativa</option>
    </select>
  </div>

  <div class="col-md-4">
    <label class="form-label fw-semibold">Etiqueta <small class="text-muted fw-normal">(badge en el slide)</small></label>
    <input type="text" name="etiqueta" class="form-control"
      placeholder="Ej: Próximo Evento, Normativa Vigente…"
      value="{{ old('etiqueta', $slide?->etiqueta) }}" maxlength="80">
  </div>

  <div class="col-md-4">
    <label class="form-label fw-semibold">Autor <small class="text-muted fw-normal">(opcional)</small></label>
    <input type="text" name="autor" class="form-control"
      placeholder="Ej: Oficina SCI — UGEL Huacaybamba"
      value="{{ old('autor', $slide?->autor) }}" maxlength="100">
  </div>

  {{-- ── Título ── --}}
  <div class="col-12">
    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
    <input type="text" name="titulo" class="form-control form-control-lg" required
      placeholder="Ej: Taller Regional de Control Interno 2025"
      value="{{ old('titulo', $slide?->titulo) }}" maxlength="255">
  </div>

  {{-- ── Resumen / Descripción ── --}}
  <div class="col-12">
    <label class="form-label fw-semibold">Resumen <small class="text-muted fw-normal">(aparece en el slide del carousel)</small></label>
    <textarea name="descripcion" class="form-control" rows="2"
      placeholder="Texto breve que se ve en la tarjeta del slider…" maxlength="1000">{{ old('descripcion', $slide?->descripcion) }}</textarea>
    <div class="form-text">Máx. 1000 caracteres — este texto se muestra en el slide principal.</div>
  </div>

  {{-- ── EDITOR QUILL ── --}}
  <div class="col-12">
    <label class="form-label fw-semibold">Contenido completo del artículo <small class="text-muted fw-normal">(página de detalle)</small></label>

    <div id="quill-editor-{{ $uid }}" style="min-height:260px;border:1px solid #d9dee3;border-radius:0 0 6px 6px;background:#fff;font-size:.92rem;"></div>
    <textarea name="contenido" id="quill-content-{{ $uid }}" style="display:none;">{{ old('contenido', $slide?->contenido) }}</textarea>

    <div class="form-text mt-1">
      <i class="ti tabler-info-circle me-1"></i>
      Este contenido se muestra en la página de detalle al hacer clic en «Leer más» desde el landing.
    </div>
  </div>

  {{-- ── DIVIDER ── --}}
  <div class="col-12"><hr class="my-1"></div>

  {{-- ── IMÁGENES ── --}}
  <div class="col-md-6">
    <label class="form-label fw-semibold">
      <i class="ti tabler-slideshow me-1"></i>
      Imagen del Slide <small class="text-muted fw-normal">(fondo del carousel)</small>
    </label>

    @if(!empty($slide?->imagen_url))
    <div class="mb-2 position-relative d-inline-block">
      <img src="{{ $slide->imagen_url }}" alt="Imagen actual" class="rounded border"
           style="height:90px;width:220px;object-fit:cover;display:block;">
      <span class="badge bg-success position-absolute top-0 start-0 m-1" style="font-size:.6rem;">Actual</span>
    </div>
    <div class="mb-2">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="eliminar_imagen"
               id="chkEliminarImagen{{ $uid }}" value="1">
        <label class="form-check-label text-danger small" for="chkEliminarImagen{{ $uid }}">
          Eliminar imagen (usará degradado CSS)
        </label>
      </div>
    </div>
    @endif

    <input type="file" name="imagen_file" id="inputSliderFile{{ $uid }}"
           class="form-control" accept="image/jpeg,image/png,image/webp">
    <div id="previewSlider{{ $uid }}" class="mt-2" style="display:none;">
      <img id="previewSliderImg{{ $uid }}" src="" alt="" class="rounded border"
           style="height:90px;width:220px;object-fit:cover;">
    </div>
    <div class="form-text">JPG/PNG/WebP · máx 4MB · Recomendado: 1920×1080</div>
  </div>

  <div class="col-md-6">
    <label class="form-label fw-semibold">
      <i class="ti tabler-photo me-1"></i>
      Imagen de portada <small class="text-muted fw-normal">(hero del artículo)</small>
    </label>

    @if(!empty($slide?->imagen_portada_url))
    <div class="mb-2 position-relative d-inline-block">
      <img src="{{ $slide->imagen_portada_url }}" alt="Portada actual" class="rounded border"
           style="height:90px;width:220px;object-fit:cover;display:block;">
      <span class="badge bg-info position-absolute top-0 start-0 m-1" style="font-size:.6rem;">Portada actual</span>
    </div>
    @endif

    <input type="file" name="imagen_portada_file" id="inputPortadaFile{{ $uid }}"
           class="form-control" accept="image/jpeg,image/png,image/webp">
    <div id="previewPortada{{ $uid }}" class="mt-2" style="display:none;">
      <img id="previewPortadaImg{{ $uid }}" src="" alt="" class="rounded border"
           style="height:90px;width:220px;object-fit:cover;">
    </div>
    <div class="form-text">Se muestra como imagen grande en la página del artículo.</div>
  </div>

  {{-- ── Degradado CSS ── --}}
  <div class="col-12">
    <label class="form-label fw-semibold">
      Degradado de fondo <small class="text-muted fw-normal">(si no hay imagen de slide)</small>
    </label>
    <div class="input-group">
      <input type="text" name="color_gradiente" id="inputGradiente{{ $uid }}" class="form-control font-monospace"
        placeholder="linear-gradient(135deg,#0a0a2e 0%,#1a1a6e 40%,#7367f0 100%)"
        value="{{ old('color_gradiente', $slide?->color_gradiente) }}" maxlength="300">
      <span class="input-group-text p-1">
        <div id="gradPreview{{ $uid }}" style="width:40px;height:30px;border-radius:4px;
          background:{{ $slide?->color_gradiente ?? 'linear-gradient(135deg,#1340A0,#7367f0)' }};"></div>
      </span>
    </div>
    <div class="form-text">
      <code>linear-gradient(135deg,#0a0a2e,#1a1a6e 40%,#7367f0)</code> azul ·
      <code>linear-gradient(135deg,#0a2e1a,#28c76f)</code> verde
    </div>
  </div>

  {{-- ── Botón CTA ── --}}
  <div class="col-md-8">
    <label class="form-label fw-semibold">URL del botón de acción</label>
    <input type="url" name="url_accion" class="form-control"
      placeholder="https://…"
      value="{{ old('url_accion', $slide?->url_accion) }}" maxlength="255">
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Texto del botón</label>
    <input type="text" name="texto_accion" class="form-control"
      placeholder="Ej: Más información"
      value="{{ old('texto_accion', $slide?->texto_accion) }}" maxlength="80">
  </div>

  {{-- ── Orden + Activo ── --}}
  <div class="col-md-3">
    <label class="form-label fw-semibold">Orden</label>
    <input type="number" name="orden" class="form-control" min="0"
      value="{{ old('orden', $slide?->orden ?? 0) }}">
    <div class="form-text">Menor = aparece primero.</div>
  </div>
  <div class="col-md-9 d-flex align-items-end pb-2">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="activo" id="checkActivo{{ $uid }}" value="1"
        {{ old('activo', $slide?->activo ?? true) ? 'checked' : '' }}>
      <label class="form-check-label fw-semibold" for="checkActivo{{ $uid }}">
        Slide activo (visible en el landing)
      </label>
    </div>
  </div>

</div>

<script>
(function(){
  var uid = '{{ $uid }}';

  // Preview imagen slide
  var sliderInput = document.getElementById('inputSliderFile' + uid);
  if (sliderInput) {
    sliderInput.addEventListener('change', function () {
      var file = this.files[0];
      var wrap = document.getElementById('previewSlider' + uid);
      var img  = document.getElementById('previewSliderImg' + uid);
      if (file && wrap && img) { img.src = URL.createObjectURL(file); wrap.style.display = 'block'; }
    });
  }

  // Preview imagen portada
  var portadaInput = document.getElementById('inputPortadaFile' + uid);
  if (portadaInput) {
    portadaInput.addEventListener('change', function () {
      var file = this.files[0];
      var wrap = document.getElementById('previewPortada' + uid);
      var img  = document.getElementById('previewPortadaImg' + uid);
      if (file && wrap && img) { img.src = URL.createObjectURL(file); wrap.style.display = 'block'; }
    });
  }

  // Preview degradado en tiempo real
  var gradInput = document.getElementById('inputGradiente' + uid);
  var gradPrev  = document.getElementById('gradPreview' + uid);
  if (gradInput && gradPrev) {
    gradInput.addEventListener('input', function () {
      gradPrev.style.background = this.value || 'linear-gradient(135deg,#1340A0,#7367f0)';
    });
  }

  // Inicializar Quill cuando el modal esté visible
  function initQuill() {
    var editorEl = document.getElementById('quill-editor-' + uid);
    var textarea = document.getElementById('quill-content-' + uid);
    if (!editorEl || !textarea || editorEl._quillInited) return;
    editorEl._quillInited = true;

    var quill = new Quill('#quill-editor-' + uid, {
      theme: 'snow',
      placeholder: 'Escribe el contenido completo del artículo aquí…',
      modules: {
        toolbar: [
          [{ 'header': [2, 3, 4, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'color': [] }, { 'background': [] }],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          [{ 'indent': '-1'}, { 'indent': '+1' }],
          ['blockquote', 'code-block'],
          ['link', 'image'],
          ['clean']
        ]
      }
    });

    // Cargar contenido existente
    if (textarea.value) {
      quill.root.innerHTML = textarea.value;
    }

    // Sincronizar al enviar el formulario
    var form = editorEl.closest('form');
    if (form) {
      form.addEventListener('submit', function () {
        textarea.value = quill.root.innerHTML;
      });
    }
  }

  // Intentar inicializar inmediatamente (si el modal ya está abierto)
  if (typeof Quill !== 'undefined') {
    // Esperar un tick para que el modal termine de renderizar
    setTimeout(initQuill, 80);
  } else {
    // Quill todavía no cargó — reintentar cuando el modal se abra
    document.addEventListener('shown.bs.modal', function handler(e) {
      if (e.target.contains(document.getElementById('quill-editor-' + uid))) {
        if (typeof Quill !== 'undefined') initQuill();
        document.removeEventListener('shown.bs.modal', handler);
      }
    });
  }
})();
</script>
