<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}
// Validar que el usuario sea administrador
if ($_SESSION['rol_id'] != "3") {
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
<!-- Alerta -->
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;margin-top: 4%;"></div>


<style>
    .toast-container {
        z-index: 9999 !important;
    }

    .ql-toolbar {
        width: 100%;
    }

    .ql-container {
        width: 100%;
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
                            <li class="breadcrumb-item active" aria-current="page">Evidencias Pendientes</li>
                        </ul>
                    </div>

                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-1">Evidencias Por Subir</h4>
                            </div>
                        </div>
                        <!-- Nota -->
                        <div class="mt-3">
                            <div class="alert alert-info text-dark fs-6 py-2 px-3 rounded">
                                <b>Nota:</b> Si su plazo ha vencido, comuníquese con la Oficina de Calidad a
                                <a href="mailto:licenciamiento@undc.edu.pe" class="text-dark text-decoration-underline">
                                    gcalidad@undc.edu.pe
                                </a>
                            </div>
                            <div class="alert alert-warning text-dark fs-6 py-2 px-3 rounded">
                                <b>Importante:</b> Si en las evidencias asignadas figura más de un responsable, deberán coordinar entre sí para que uno realice la carga correspondiente.
                            </div>
                        </div>
                        <div class="mt-3">
                            <div id="alert-vencimiento">

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="row mt-0 p-0">
            <div class="col-lg-12">
                <div class="card">
                    <div id="mis-evidencias-list-pendientes" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 m-2 mt-3">
                        <!-- Las tarjetas generadas se añadirán aquí dinámicamente -->
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<!-- [ Main Content ] end -->
<?php
include('footer.php')

?>

<script src="scripts/responsable-evidencias.js"></script>
<script src="scripts/index.js"></script>