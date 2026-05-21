<!-- Modal Agregar/Editar Rol -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="role-title">Agregar Nuevo Rol</h4>
                    <p class="text-body-secondary">Configura los permisos del rol</p>
                </div>
                <!-- Formulario de rol -->
                <form id="addRoleForm" class="row g-3" onsubmit="return false">
                    <div class="col-12 form-control-validation">
                        <label class="form-label" for="modalRoleName">Nombre del Rol</label>
                        <input type="text" id="modalRoleName" name="modalRoleName" class="form-control"
                            placeholder="Ej: Responsable de Control Interno" tabindex="-1" />
                    </div>
                    <div class="col-12">
                        <h5 class="mb-6">Permisos del Rol</h5>
                        <!-- Tabla de permisos -->
                        <div class="table-responsive">
                            <table class="table table-flush-spacing">
                                <tbody>
                                    <tr>
                                        <td class="text-nowrap fw-medium">
                                            Acceso de Administrador
                                            <i class="icon-base ti tabler-info-circle icon-xs" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Otorga acceso total al sistema"></i>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="selectAll" />
                                                    <label class="form-check-label" for="selectAll">Seleccionar
                                                        Todo</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap fw-medium text-heading">Gestión de Usuarios</td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="userRead" />
                                                    <label class="form-check-label" for="userRead">Ver</label>
                                                </div>
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="userWrite" />
                                                    <label class="form-check-label" for="userWrite">Editar</label>
                                                </div>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="userCreate" />
                                                    <label class="form-check-label" for="userCreate">Crear</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap fw-medium text-heading">Control Interno</td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="sciRead" />
                                                    <label class="form-check-label" for="sciRead">Ver</label>
                                                </div>
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="sciWrite" />
                                                    <label class="form-check-label" for="sciWrite">Editar</label>
                                                </div>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="sciCreate" />
                                                    <label class="form-check-label" for="sciCreate">Crear</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap fw-medium text-heading">Modelo de Integridad</td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="miRead" />
                                                    <label class="form-check-label" for="miRead">Ver</label>
                                                </div>
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="miWrite" />
                                                    <label class="form-check-label" for="miWrite">Editar</label>
                                                </div>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="miCreate" />
                                                    <label class="form-check-label" for="miCreate">Crear</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap fw-medium text-heading">Evidencias</td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="evRead" />
                                                    <label class="form-check-label" for="evRead">Ver</label>
                                                </div>
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="evWrite" />
                                                    <label class="form-check-label" for="evWrite">Editar</label>
                                                </div>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="evCreate" />
                                                    <label class="form-check-label" for="evCreate">Subir</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap fw-medium text-heading">Reportes y Reconocimientos</td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="repRead" />
                                                    <label class="form-check-label" for="repRead">Ver</label>
                                                </div>
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="repWrite" />
                                                    <label class="form-check-label" for="repWrite">Editar</label>
                                                </div>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="repCreate" />
                                                    <label class="form-check-label" for="repCreate">Exportar</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap fw-medium text-heading">Semáforo y Alertas</td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="monRead" />
                                                    <label class="form-check-label" for="monRead">Ver</label>
                                                </div>
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="monWrite" />
                                                    <label class="form-check-label" for="monWrite">Editar</label>
                                                </div>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="monCreate" />
                                                    <label class="form-check-label" for="monCreate">Configurar</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap fw-medium text-heading">Configuración del Sistema</td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="cfgRead" />
                                                    <label class="form-check-label" for="cfgRead">Ver</label>
                                                </div>
                                                <div class="form-check mb-0 me-4 me-lg-12">
                                                    <input class="form-check-input" type="checkbox" id="cfgWrite" />
                                                    <label class="form-check-label" for="cfgWrite">Editar</label>
                                                </div>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="cfgCreate" />
                                                    <label class="form-check-label"
                                                        for="cfgCreate">Administrar</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Guardar Rol</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Modal Agregar/Editar Rol -->
