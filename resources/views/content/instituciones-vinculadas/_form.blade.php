{{-- Formulario compartido: crear y editar institución vinculada --}}
<div class="row g-3">

  <div class="col-md-8">
    <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
    <input type="text" name="nombre" class="form-control" placeholder="Ej: Contraloría General de la República" required maxlength="255">
  </div>

  <div class="col-md-4">
    <label class="form-label fw-semibold">Sigla <span class="text-danger">*</span></label>
    <input type="text" name="sigla" class="form-control" placeholder="Ej: CGR" required maxlength="30"
           style="font-weight:700;text-transform:uppercase;letter-spacing:.06em;">
  </div>

  <div class="col-12">
    <label class="form-label fw-semibold">Logo (subir archivo)</label>
    <input type="file" name="logo_file" class="form-control" accept="image/png,image/jpeg,image/webp,image/svg+xml">
    <div class="form-text">PNG, JPG, WebP o SVG · máx. 2 MB. Si sube un archivo, se ignorará la URL.</div>
  </div>

  <div class="col-12">
    <label class="form-label fw-semibold">Logo por URL externa</label>
    <input type="url" name="logo_url" class="form-control" placeholder="https://ejemplo.com/logo.png" maxlength="500">
    <div class="form-text">URL directa al logo si no desea subir archivo.</div>
  </div>

  <div class="col-md-4">
    <label class="form-label fw-semibold">Color de acento</label>
    <div class="d-flex gap-2 align-items-center">
      <input type="color" name="color_acento" class="form-control form-control-color" value="#1e3a8a" style="width:48px;height:38px;padding:2px;">
      <input type="text" id="colorHexText" class="form-control form-control-sm" placeholder="#1e3a8a" style="width:100px;font-family:monospace;" readonly>
    </div>
    <div class="form-text">Color del borde inferior y sigla en el landing.</div>
  </div>

  <div class="col-md-8">
    <label class="form-label fw-semibold">URL del sitio web</label>
    <input type="url" name="url_sitio" class="form-control" placeholder="https://www.contraloria.gob.pe" maxlength="255">
  </div>

  <div class="col-md-9">
    <label class="form-label fw-semibold">Descripción corta</label>
    <input type="text" name="descripcion" class="form-control" placeholder="Texto descriptivo opcional" maxlength="500">
  </div>

  <div class="col-md-3">
    <label class="form-label fw-semibold">Orden</label>
    <input type="number" name="orden" class="form-control" value="0" min="0">
  </div>

  <div class="col-12">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="activo" value="1" id="activoCheck" checked>
      <label class="form-check-label fw-semibold" for="activoCheck">Activo (visible en el landing)</label>
    </div>
  </div>

</div>

<script>
(function(){
  // Sincronizar color picker → texto hex
  var colorInputs = document.querySelectorAll('[name="color_acento"]');
  var hexTexts    = document.querySelectorAll('#colorHexText');
  colorInputs.forEach(function(ci, i) {
    if (hexTexts[i]) hexTexts[i].value = ci.value;
    ci.addEventListener('input', function() {
      if (hexTexts[i]) hexTexts[i].value = this.value;
    });
  });
})();
</script>
