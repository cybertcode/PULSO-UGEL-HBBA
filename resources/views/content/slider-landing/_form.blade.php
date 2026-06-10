<div class="row g-3">

  {{-- Tipo --}}
  <div class="col-md-6">
    <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
    <select name="tipo" class="form-select" required>
      <option value="noticia"   {{ old('tipo', $slide?->tipo) === 'noticia'   ? 'selected' : '' }}>Noticia</option>
      <option value="evento"    {{ old('tipo', $slide?->tipo) === 'evento'    ? 'selected' : '' }}>Evento</option>
      <option value="normativa" {{ old('tipo', $slide?->tipo) === 'normativa' ? 'selected' : '' }}>Normativa</option>
    </select>
  </div>

  {{-- Etiqueta --}}
  <div class="col-md-6">
    <label class="form-label fw-semibold">Etiqueta</label>
    <input type="text" name="etiqueta" class="form-control"
      placeholder="Ej: Próximo Evento, Normativa Vigente…"
      value="{{ old('etiqueta', $slide?->etiqueta) }}" maxlength="80">
    <div class="form-text">Texto pequeño que aparece sobre el título en el slide.</div>
  </div>

  {{-- Título --}}
  <div class="col-12">
    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
    <input type="text" name="titulo" class="form-control" required
      placeholder="Ej: Taller Regional de Control Interno 2025"
      value="{{ old('titulo', $slide?->titulo) }}" maxlength="255">
  </div>

  {{-- Descripción --}}
  <div class="col-12">
    <label class="form-label fw-semibold">Descripción</label>
    <textarea name="descripcion" class="form-control" rows="3"
      placeholder="Texto descriptivo que aparece bajo el título en el slide…" maxlength="1000">{{ old('descripcion', $slide?->descripcion) }}</textarea>
  </div>

  {{-- ── IMAGEN DE FONDO ── --}}
  <div class="col-12">
    <label class="form-label fw-semibold">Imagen de fondo del Slide</label>

    @if(!empty($slide?->imagen_url))
    {{-- Preview de imagen actual --}}
    <div class="mb-2 position-relative d-inline-block">
      <img src="{{ $slide->imagen_url }}" alt="Imagen actual"
           class="rounded border"
           style="height:110px;width:260px;object-fit:cover;display:block;">
      <span class="badge bg-success position-absolute top-0 start-0 m-1" style="font-size:.65rem;">Imagen actual</span>
    </div>
    <div class="mb-2">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="eliminar_imagen" id="chkEliminarImagen{{ $slide->id ?? 'new' }}" value="1">
        <label class="form-check-label text-danger small" for="chkEliminarImagen{{ $slide->id ?? 'new' }}">
          Eliminar imagen actual (quedará solo el degradado CSS)
        </label>
      </div>
    </div>
    @endif

    <div class="input-group">
      <input type="file" name="imagen_file" id="inputImagenFile"
             class="form-control"
             accept="image/jpeg,image/png,image/webp">
      <label class="input-group-text" for="inputImagenFile">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
          <polyline points="21 15 16 10 5 21"/>
        </svg>
      </label>
    </div>
    <div class="form-text">
      Formatos: JPG, PNG, WebP · Tamaño máximo: 4 MB · Recomendado: 1920×1080 px.
      {{ !empty($slide?->imagen_url) ? 'Selecciona un archivo para reemplazar la imagen actual.' : '' }}
    </div>

    {{-- Preview en tiempo real al seleccionar archivo --}}
    <div id="previewNuevaImagen" class="mt-2" style="display:none;">
      <img id="previewNuevaImagenImg" src="" alt="Preview" class="rounded border"
           style="height:110px;width:260px;object-fit:cover;display:block;">
      <small class="text-muted">Vista previa de la nueva imagen</small>
    </div>
  </div>

  {{-- Degradado CSS (fondo alternativo sin imagen) --}}
  <div class="col-12">
    <label class="form-label fw-semibold">Degradado de fondo CSS <small class="text-muted fw-normal">(se usa si no hay imagen)</small></label>
    <input type="text" name="color_gradiente" class="form-control font-monospace"
      placeholder="linear-gradient(135deg,#0a0a2e 0%,#1a1a6e 40%,#7367f0 100%)"
      value="{{ old('color_gradiente', $slide?->color_gradiente) }}" maxlength="300">
    <div class="form-text">
      Ejemplos:
      <code>linear-gradient(135deg,#0a0a2e,#1a1a6e 40%,#7367f0)</code> (azul) ·
      <code>linear-gradient(135deg,#0a2e1a,#28c76f)</code> (verde) ·
      <code>linear-gradient(135deg,#2e1a0a,#ff9f43)</code> (naranja)
    </div>
  </div>

  {{-- URL botón secundario --}}
  <div class="col-md-8">
    <label class="form-label fw-semibold">URL del botón secundario</label>
    <input type="url" name="url_accion" class="form-control"
      placeholder="https://…"
      value="{{ old('url_accion', $slide?->url_accion) }}" maxlength="255">
  </div>

  {{-- Texto botón secundario --}}
  <div class="col-md-4">
    <label class="form-label fw-semibold">Texto del botón</label>
    <input type="text" name="texto_accion" class="form-control"
      placeholder="Ej: Más información"
      value="{{ old('texto_accion', $slide?->texto_accion) }}" maxlength="80">
  </div>

  {{-- Orden --}}
  <div class="col-md-4">
    <label class="form-label fw-semibold">Orden</label>
    <input type="number" name="orden" class="form-control" min="0"
      value="{{ old('orden', $slide?->orden ?? 0) }}">
    <div class="form-text">Menor número = aparece primero.</div>
  </div>

  {{-- Activo --}}
  <div class="col-md-8 d-flex align-items-end">
    <div class="form-check form-switch mb-2">
      <input class="form-check-input" type="checkbox" name="activo" id="checkActivo" value="1"
        {{ old('activo', $slide?->activo ?? true) ? 'checked' : '' }}>
      <label class="form-check-label fw-semibold" for="checkActivo">Slide activo (visible en el landing)</label>
    </div>
  </div>

</div>

<script>
document.getElementById('inputImagenFile')?.addEventListener('change', function () {
  const file = this.files[0];
  const wrap = document.getElementById('previewNuevaImagen');
  const img  = document.getElementById('previewNuevaImagenImg');
  if (file) {
    img.src = URL.createObjectURL(file);
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
  }
});
</script>
