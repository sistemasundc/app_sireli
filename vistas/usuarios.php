<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}

// Validar que el usuario sea administrador
if ($_SESSION['rol_id'] != "1") {
    header("Location: dashboard.php");
    exit();
}
?>

<?php
include('head.php')
?>


<!-- Toasts -->
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
    <!-- Toast de éxito -->
    <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mensajefloat"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    <!-- Toast de advertencia -->
    <div id="liveToastwarning" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mensajefloatwarning"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>


<style>
    .toast-container {
        z-index: 9999 !important;
        margin-top: 4%;
    }
</style>

<div class="pc-container">
    <div class="pc-content"><!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Usuarios</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Usuarios</h4>
                            </div>
                            <div class="text-end">
                                <a type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 text-white" data-bs-toggle="modal" data-bs-target="#RegistraUsuarioModal">
                                    <i class="ti ti-plus f-18"></i> Agregar Usuario
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- [ breadcrumb ] end --><!-- [ Main Content ] start -->
        <div class="row mt-0"><!-- [ sample-page ] start -->
            <div class="col-sm-12">
                <div class="card table-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="datatable-wrapper datatable-loading no-footer searchable fixed-columns">
                                <div class="datatable-container">
                                    <table class="table table-hover datatable-table" id="tblUsuarios">
                                        <thead>
                                            <tr>
                                                <th style="width: 6.918238993710692%;">#</th>
                                                <th style="width: 29.769392033542978%;">Nombres</th>
                                                <th style="width: 16.666666666666664%;">Celular</th>
                                                <th style="width: 8.70020964360587%;">Rol</th>
                                                <th style="width: 7.861635220125786%;">Oficina</th>
                                                <th style="width: 13.20754716981132%;">Creado</th>
                                                <th style="width: 13.20754716981132%;">Estado</th>
                                                <th class="text-center" style="width: 16.87631027253669%;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- [ sample-page ] end -->
        </div><!-- [ Main Content ] end -->
    </div>
</div>

<!-- Registrar usuario -->
<div class="modal fade" id="RegistraUsuarioModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="modal-content" id="formUsuario" method="POST">
                <div class="modal-header">
                    <h5 class="mb-0">Registrar Usuario</h5>
                    <a href="#" id="btnCerrarModal" class="avtar avtar-s btn-link-danger btn-pc-default ms-auto" data-bs-dismiss="modal"><i class="ti ti-x f-20"></i></a>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Nombres <span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control" id="usu_nom" placeholder="Nombres">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Apellidos <span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control" id="usu_ape" placeholder="Apellidos">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3" id="divUsu_correo">
                                <label class="form-label">Correo <span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control" id="usu_correo" placeholder="example@undc.edu.pe">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Celular <span>(Opcional)</span></label>
                                <input type="text" class="form-control" id="usu_telf" placeholder="992992992">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3" id="divRol_id">
                                <label class="form-label">Rol <span class="text-danger">(*)</span></label>
                                <select class="form-select" id="rol_id" name="rol_id" data-live-search="true" title="Elige el rol ...">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3" id="divOficina_id">
                                <label class="form-label">Oficina <span class="text-danger">(*)</span></label>
                                <select class="form-select" id="oficina_id" name="oficina_id" data-live-search="true" title="Elige la sede ...">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted mt-2">
                        <small><span class="text-danger">(*)</span> Campo obligatorio</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <ul class="list-inline me-auto mb-0">
                        <li class="list-inline-item align-bottom">
                            <a class="avtar avtar-s btn-link-danger btn-pc-default w-sm-auto" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete" id="limpiarCampos">
                                <i class="ti ti-trash f-18"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="flex-grow-1 text-end">
                        <button type="button" class="btn btn-link-danger btn-pc-default" data-bs-dismiss="modal" id="btnCerrarModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarUsuario">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Editar usuario -->
<div class="modal fade" id="EditarUsuarioModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="modal-content" id="formEditarUsuario" method="POST">
                <div class="modal-header">
                    <h5 class="mb-0">Editar Usuario</h5>
                    <a href="#" id="btnCerrarModal" class="avtar avtar-s btn-link-danger btn-pc-default ms-auto" data-bs-dismiss="modal"><i class="ti ti-x f-20"></i></a>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="usu_id" name="usu_id">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="usu_nom_actual" name="usu_nom_nuevo">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="usu_ape_actual" name="usu_ape_nuevo">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3" id="divUsu_correo">
                                <label class="form-label">Correo</label>
                                <input type="text" class="form-control" id="usu_correo_actual" name="usu_correo_nuevo">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Celular</label>
                                <input type="text" class="form-control" id="usu_telf_actual" name="usu_telf_nuevo">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3" id="divRol_id">
                                <label class="form-label">Rol</label>
                                <select class="form-select" id="rol_id_actual" name="rol_id_nuevo" data-live-search="true" title="Elige el rol ...">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3" id="divOficina_id">
                                <label class="form-label">Oficina</label>
                                <select class="form-select" id="oficina_id_actual" name="oficina_id_nuevo" data-live-search="true" title="Elige la sede ...">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div class="flex-grow-1 text-end">
                        <button type="button" class="btn btn-link-danger btn-pc-default" data-bs-dismiss="modal" id="btnCerrarModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnEditarUsuario">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- [ Main Content ] end -->
<?php
include('footer.php')
?>

<script src="scripts/usuarios.js"></script>
<script src="scripts/index.js"></script>