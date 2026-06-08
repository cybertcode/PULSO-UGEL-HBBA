<!-- Modal Agregar Permiso -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h3>Agregar Permiso</h3>
                    <p class="text-body-secondary">Define un permiso para asignarlo a los roles del sistema.</p>
                </div>
                <form id="addPermissionForm" class="row" onsubmit="return false">
                    <div class="col-12 form-control-validation mb-4">
                        <label class="form-label" for="modalPermissionName">Nombre del Permiso</label>
                        <input type="text" id="modalPermissionName" name="modalPermissionName" class="form-control"
                            placeholder="Ej: ver-reportes" autofocus />
                    </div>
                    <div class="col-12 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="corePermission" />
                            <label class="form-check-label" for="corePermission">Permiso esencial del sistema</label>
                        </div>
                    </div>
                    <div class="col-12 text-center demo-vertical-spacing">
                        <button type="submit" class="btn btn-primary me-sm-4 me-1">Crear Permiso</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Modal Agregar Permiso -->
