<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}
// Validar que el usuario sea evaluador
if ($_SESSION['rol_id'] != "2") {
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
                            <li class="breadcrumb-item" aria-current="page">Evidencias</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Por Revisar</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row mt-0">
            <div class="col-lg-12">
                <div class="card">
                    <div class="d-flex justify-content-center" id="paginacion-container">
                        <nav>
                            <ul class="pagination" id="paginacion-evidencias-por-revisar"></ul>
                        </nav>
                    </div>
                    <div id="mensaje-registros-revisar" class="text-center"></div>
                    <div id="mis-evidencias-list-revisar" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 m-2 mt-3">
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

<script src="scripts/evaluador-evidencias.js"></script>
<script src="scripts/index.js"></script>