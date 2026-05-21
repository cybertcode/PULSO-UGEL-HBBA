<!-- Modal Editar Permiso -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-simple">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h3>Editar Permiso</h3>
          <p class="text-body-secondary">Modifica el permiso según tus necesidades.</p>
        </div>
        <div class="alert alert-warning" role="alert">
          <h6 class="alert-heading mb-2">Advertencia</h6>
          <p class="mb-0">Editar el nombre de un permiso puede afectar el funcionamiento del sistema.
            Asegúrate de proceder con precaución.</p>
        </div>
        <form id="editPermissionForm" class="row" onsubmit="return false">
          <div class="col-sm-9 form-control-validation">
            <label class="form-label" for="editPermissionName">Nombre del Permiso</label>
            <input type="text" id="editPermissionName" name="editPermissionName" class="form-control"
              placeholder="Nombre del permiso" tabindex="-1" />
          </div>
          <div class="col-sm-3 mb-4">
            <label class="form-label invisible d-none d-sm-inline-block">Botón</label>
            <button type="submit" class="btn btn-primary mt-1 mt-sm-0">Actualizar</button>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="editCorePermission" />
              <label class="form-check-label" for="editCorePermission">Permiso esencial del sistema</label>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Modal Editar Permiso -->
