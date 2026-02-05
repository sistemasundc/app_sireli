<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}
// Validar que el usuario sea administrador
if ($_SESSION['rol_id'] != "2") {
    header("Location: dashboard.php");
    exit();
}
?>


<!-- Toasts -->
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
    <!-- Toast de éxito -->
    <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mensajefloat"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
    <!-- Toast de advertencia -->
    <div id="liveToastwarning" class="toast align-items-center text-bg-danger border-0" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mensajefloatwarning"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
    .toast-container {
        z-index: 9999 !important;
        margin-top: 4%;
    }

    .ql-toolbar {
        width: 100%;
    }

    .ql-container {
        width: 100%;
    }
</style>

<?php
include('head.php')

?>

<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="menu.php">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Seguimiento de Evidencia</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Seguimiento de Evidencia</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5><span class="p-l-5">Historial de la Evidencia</span></h5>
                    </div>

                    <!--                <div class="border-bottom card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-12"><button type="button" class="btn btn-sm my-2 btn-light-success"><i
                                        class="mx-2 feather icon-message-square"></i>Post a reply</button> <button
                                    type="button" class="btn btn-sm my-2 btn-light-warning"><i
                                        class="mx-2 feather icon-edit"></i>Post a Note</button> <button type="button"
                                    class="btn btn-sm my-2 btn-light-danger"><i
                                        class="mx-2 feather icon-user-check"></i>Customer Notes</button></div>
                        </div>
                    </div> -->
                    <div class="border-bottom card-body">
                        <div class="row">
                            <span class="badge text-bg-primary text-wrap text-break lh-1 text-start mt-3 d-block w-100 p-2">ARCHIVOS ADJUNTOS</span>
                            <ul class="list-group list-group-flush pb-2" id="archivos-adjuntos">
                                <!-- Aquí se mostrarán los archivos -->
                            </ul>

                            <span class="badge text-bg-primary text-wrap text-break lh-1 text-start mt-3 d-block w-100 p-2">FLUJO DE ENVÍO</span>
                            <div class="card-body table-border-style">
                                <div class="table-responsive">
                                    <table class="table" id="tabla-historial" style="font-size: 14px;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>N° Historial</th>
                                                <th>Origen</th>
                                                <th>Fecha Emisión</th>
                                                <th>Destino</th>
                                                <th>Fecha Recepcion</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Las filas se insertarán aquí mediante JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Detalle de Evidencia</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success d-block text-center text-uppercase" id="evidencia-nombre"><i
                                class="feather icon-check-circle mx-2"></i>Verified Purchase</div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex align-items-cente">
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Plazo de envío</label>
                                <p class="mb-0" id="evidencia-plazo"></p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Responsables</label>
                                <p class="mb-0" id="responsables"></p>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Medio de Verificación</label>
                                <p class="mb-0" id="medio-nombre"></p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex align-items-cente">
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Consideración</label>
                                <p class="mb-0" id="evidencia-consideraciones"></p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex align-items-cente">
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Asignado por</label>
                                <p class="mb-0" id="evidencia-usuario"></p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex align-items-cente">
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Creado</label>
                                <p class="mb-0" id="fecha-creacion"></p>
                            </div>
                        </li>

                        <!-- <li class="list-group-item py-3"><button type="button"
                                class="btn btn-sm btn-light-warning me-2"><i
                                    class="mx-2 feather icon-thumbs-up"></i>Make Private</button> <button type="button"
                                class="btn btn-sm btn-light-danger"><i
                                    class="mx-2 feather icon-trash-2"></i>Delete</button></li> -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- [ Main Content ] end -->
<?php
include('footer.php')

?>

<script src="scripts/misevidencias.js"></script>