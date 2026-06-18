@php
  $uid        = $slide?->id ?? 'new';
  $autoAutor  = $autorName ?? auth()->user()?->name ?? '';
  $autoCargo  = $autorCargo ?? auth()->user()?->cargos->first()?->nombre ?? null;
  // Extraer color hex del degradado guardado para mostrarlo en el color picker
  $hexActual = '#1340A0';
  if (!empty($slide?->color_gradiente)) {
      preg_match('/#([0-9a-fA-F]{6})/', $slide->color_gradiente, $m);
      if (!empty($m[0])) $hexActual = $m[0];
  }
@endphp

<div class="row g-3">

  {{-- ── Fila 1: Tipo + Etiqueta ── --}}
  <div class="col-md-5">
    <label class="form-label fw-semibold">Tipo de publicación <span class="text-danger">*</span></label>
    <select name="tipo" class="form-select" required>
      <option value="noticia"   {{ old('tipo', $slide?->tipo) === 'noticia'   ? 'selected' : '' }}>📰 Noticia</option>
      <option value="evento"    {{ old('tipo', $slide?->tipo) === 'evento'    ? 'selected' : '' }}>📅 Evento</option>
      <option value="normativa" {{ old('tipo', $slide?->tipo) === 'normativa' ? 'selected' : '' }}>📋 Normativa</option>
    </select>
  </div>

  <div class="col-md-7">
    <label class="form-label fw-semibold">
      Etiqueta
      <small class="text-muted fw-normal">— texto que aparece sobre el título en el slide</small>
    </label>
    <input type="text" name="etiqueta" class="form-control"
      placeholder="Ej: Próximo Evento · Normativa Vigente · Comunicado"
      value="{{ old('etiqueta', $slide?->etiqueta) }}" maxlength="80">
  </div>

  {{-- ── Título ── --}}
  <div class="col-12">
    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
    <input type="text" name="titulo" class="form-control" required
      placeholder="Ej: Taller Regional de Control Interno — GORE Huánuco 2025"
      value="{{ old('titulo', $slide?->titulo) }}" maxlength="255">
  </div>

  {{-- ── Resumen ── --}}
  <div class="col-12">
    <label class="form-label fw-semibold">
      Resumen
      <small class="text-muted fw-normal">— se muestra en el slide del carousel principal</small>
    </label>
    <textarea name="descripcion" class="form-control" rows="2"
      placeholder="Breve descripción visible en la tarjeta del slider (máx. 2 líneas)…"
      maxlength="1000">{{ old('descripcion', $slide?->descripcion) }}</textarea>
  </div>

  {{-- ── EDITOR QUILL (Vuexy nativo) ── --}}
  <div class="col-12">
    <label class="form-label fw-semibold">
      <i class="ti tabler-article me-1 text-primary"></i>
      Contenido del artículo
      <small class="text-muted fw-normal">— página completa al hacer clic en «Leer más»</small>
    </label>

    {{-- Toolbar HTML estilo Vuexy --}}
    <div id="quill-toolbar-{{ $uid }}" class="border rounded-top px-2 py-1 bg-light">
      <span class="ql-formats">
        <select class="ql-header">
          <option value="2">Título 2</option>
          <option value="3">Título 3</option>
          <option value="4">Título 4</option>
          <option selected>Normal</option>
        </select>
      </span>
      <span class="ql-formats">
        <button class="ql-bold"></button>
        <button class="ql-italic"></button>
        <button class="ql-underline"></button>
        <button class="ql-strike"></button>
      </span>
      <span class="ql-formats">
        <button class="ql-list" value="ordered"></button>
        <button class="ql-list" value="bullet"></button>
        <button class="ql-indent" value="-1"></button>
        <button class="ql-indent" value="+1"></button>
      </span>
      <span class="ql-formats">
        <button class="ql-blockquote"></button>
        <button class="ql-code-block"></button>
      </span>
      <span class="ql-formats">
        <select class="ql-color"></select>
        <select class="ql-background"></select>
      </span>
      <span class="ql-formats">
        <button class="ql-link"></button>
        <button class="ql-clean"></button>
      </span>
    </div>

    <div id="quill-editor-{{ $uid }}"
         data-quill-uid="{{ $uid }}"
         style="min-height:240px;border-radius:0 0 6px 6px;font-size:.92rem;"></div>

    <textarea name="contenido" id="quill-content-{{ $uid }}" class="d-none">{{ old('contenido', $slide?->contenido) }}</textarea>
  </div>

  {{-- ── DIVIDER ── --}}
  <div class="col-12"><hr class="my-1 text-muted"></div>

  {{-- ── Imagen + Color de fondo ── --}}
  <div class="col-md-8">
    <label class="form-label fw-semibold">
      <i class="ti tabler-photo me-1"></i>
      Imagen
      <small class="text-muted fw-normal">— se usa tanto en el slide como en la página del artículo</small>
    </label>

    @if(!empty($slide?->imagen_url))
    <div class="mb-2 d-flex align-items-center gap-3">
      <img src="{{ $slide->imagen_url }}" alt=""
           class="rounded border" style="height:72px;width:180px;object-fit:cover;">
      <div>
        <div class="badge bg-label-success mb-1">Imagen actual</div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="eliminar_imagen"
                 id="chkElim{{ $uid }}" value="1">
          <label class="form-check-label text-danger small" for="chkElim{{ $uid }}">
            Quitar imagen
          </label>
        </div>
      </div>
    </div>
    @endif

    <input type="file" name="imagen_file" id="imgFile{{ $uid }}"
           class="form-control" accept="image/jpeg,image/png,image/webp">
    <div id="imgPreviewWrap{{ $uid }}" class="mt-2" style="display:none;">
      <img id="imgPreview{{ $uid }}" src="" alt=""
           class="rounded border" style="height:72px;width:180px;object-fit:cover;">
      <small class="text-muted d-block">Vista previa</small>
    </div>
    <div class="form-text">JPG / PNG / WebP · máx 4 MB · Resolución recomendada: 1280×720</div>
  </div>

  <div class="col-md-4">
    <label class="form-label fw-semibold">
      <i class="ti tabler-palette me-1"></i>
      Color de fondo
      <small class="text-muted fw-normal">— si no hay imagen</small>
    </label>
    <div class="d-flex align-items-center gap-2">
      <input type="color" name="color_fondo" id="colorPicker{{ $uid }}"
             class="form-control form-control-color"
             style="width:48px;height:38px;padding:2px;"
             value="{{ old('color_fondo', $hexActual) }}"
             title="Elige un color de fondo">
      <div id="colorPreviewBox{{ $uid }}"
           style="flex:1;height:38px;border-radius:6px;border:1px solid #d9dee3;
                  background:linear-gradient(135deg, {{ $hexActual }}dd, {{ $hexActual }}, {{ $hexActual }}bb);
                  transition:background .3s;"></div>
    </div>
    <div class="form-text">Se genera un degradado automático con este color.</div>
  </div>

  {{-- ── Autor (usuario logueado, no editable) ── --}}
  <div class="col-md-6">
    <label class="form-label fw-semibold">
      <i class="ti tabler-user-check me-1 text-success"></i>
      Autor
    </label>
    {{-- Hidden para enviar el valor --}}
    <input type="hidden" name="autor" value="{{ $autoAutor }}">
    {{-- Visual solo lectura --}}
    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded"
         style="background:#f0fdf4;border:1px solid #bbf7d0;">
      <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#1340A0,#28c76f);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.9rem;font-weight:800;color:#fff;">
        {{ strtoupper(substr($autoAutor, 0, 1)) }}
      </div>
      <div style="min-width:0;">
        <div style="font-size:.85rem;font-weight:700;color:#166534;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $autoAutor }}</div>
        <div style="font-size:.68rem;color:#16a34a;">
          {{ $autoCargo ?? 'Usuario del Sistema PULSO UGEL' }}
        </div>
      </div>
      <i class="ti tabler-lock ms-auto" style="color:#86efac;font-size:.82rem;flex-shrink:0;" title="Asignado automáticamente al usuario logueado"></i>
    </div>
  </div>

  {{-- ── URL de acción ── --}}
  <div class="col-md-4">
    <label class="form-label fw-semibold">
      <i class="ti tabler-link me-1"></i>
      Enlace externo
      <small class="text-muted fw-normal">(opcional)</small>
    </label>
    <input type="url" name="url_accion" class="form-control"
      placeholder="https://…"
      value="{{ old('url_accion', $slide?->url_accion) }}" maxlength="255">
  </div>

  <div class="col-md-2">
    <label class="form-label fw-semibold">Texto del botón</label>
    <input type="text" name="texto_accion" class="form-control"
      placeholder="Ej: Ver más"
      value="{{ old('texto_accion', $slide?->texto_accion) }}" maxlength="80">
  </div>

  {{-- ── Activo ── --}}
  <input type="hidden" name="orden" value="{{ $slide?->orden ?? (\App\Models\SliderLanding::max('orden') + 1) }}">
  <div class="col-12 d-flex align-items-end pb-1">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="activo"
             id="chkActivo{{ $uid }}" value="1"
             {{ old('activo', $slide?->activo ?? true) ? 'checked' : '' }}>
      <label class="form-check-label fw-semibold" for="chkActivo{{ $uid }}">
        Publicación activa y visible en el landing
      </label>
    </div>
  </div>

</div>

<script>
(function () {
  var uid = '{{ $uid }}';

  // Preview imagen
  var fi = document.getElementById('imgFile' + uid);
  if (fi) fi.addEventListener('change', function () {
    var f = this.files[0];
    var w = document.getElementById('imgPreviewWrap' + uid);
    var i = document.getElementById('imgPreview' + uid);
    if (f && w && i) { i.src = URL.createObjectURL(f); w.style.display = 'block'; }
  });

  // Color picker → preview degradado
  var cp = document.getElementById('colorPicker' + uid);
  var cb = document.getElementById('colorPreviewBox' + uid);
  if (cp && cb) cp.addEventListener('input', function () {
    var h = this.value;
    cb.style.background = 'linear-gradient(135deg, ' + h + 'dd, ' + h + ', ' + h + 'bb)';
  });
})();
</script>
