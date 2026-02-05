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
                            <li class="breadcrumb-item"><a href="menu.php">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Oficinas</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Oficinas</h4>
                            </div>
                            <div class="text-end">
                                <a type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 text-white" data-bs-toggle="modal" data-bs-target="#RegistraOficinaModal">
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
                                    <table class="table table-hover datatable-table" id="tblOficinas">
                                        <thead>
                                            <tr>
                                                <th style="width: 6.918238993710692%;">#</th>
                                                <th style="width: 29.769392033542978%;">Nombre de Oficina</th>
                                                <th style="width: 16.666666666666664%;">Estado</th>
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
<!-- [ Main Content ] end -->

<!-- Registrar oficina -->
<div class="modal fade" id="RegistraOficinaModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="modal-content" id="formOficina" method="POST">
                <div class="modal-header">
                    <h5 class="mb-0">Registrar Oficina</h5>
                    <a href="#" id="btnCerrarModal" class="avtar avtar-s btn-link-danger btn-pc-default ms-auto" data-bs-dismiss="modal">
                        <i class="ti ti-x f-20"></i>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-2" id="divOficina_nom">
                                <label class="form-label">Nombre de la Oficina</label>
                                <input type="text" class="form-control" id="oficina_nom" name="oficina_nom" placeholder="Nombre de la Oficina">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <ul class="list-inline me-auto mb-0">
                        <li class="list-inline-item align-bottom">
                            <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default w-sm-auto" data-bs-toggle="tooltip" title="Eliminar" id="limpiarCampos">
                                <i class="ti ti-trash f-18"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="flex-grow-1 text-end">
                        <button type="button" class="btn btn-link-danger btn-pc-default" data-bs-dismiss="modal" id="btnCerrarModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarOficina">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Editar Oficina -->

<div class="modal fade" id="EditarOficinaModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="modal-content" method="POST">
                <div class="modal-header">
                    <h5 class="mb-0">Editar Oficina</h5>
                    <a href="#" id="btnCerrarModal" class="avtar avtar-s btn-link-danger btn-pc-default ms-auto" data-bs-dismiss="modal">
                        <i class="ti ti-x f-20"></i>
                    </a>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="oficina_id" name="oficina_id">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-2" id="divOficina_nom">
                                <label class="form-label">Nombre de la Oficina</label>
                                <input type="text" class="form-control" id="oficina_nom_actual" name="oficina_nom_actual" placeholder="Nombre de la Oficina">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div class="flex-grow-1 text-end">
                        <button type="button" class="btn btn-link-danger btn-pc-default" data-bs-dismiss="modal" id="btnCerrarModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnEditarOficina">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include('footer.php')
?>

<script src="scripts/oficinas.js"></script>