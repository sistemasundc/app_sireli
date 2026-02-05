<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
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
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="menu.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manuales de Uso</li>
                        </ul>
                    </div>

                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-1">Manuales de Uso</h4>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-0 p-0 justify-content-center">
            <!-- Columna centrada para la vista previa del PDF -->
            
                <div class="card shadow">
                    <div class="card-body">
                        <!-- Vista previa del PDF centrada -->
                        <div class="pdf-preview" style="height: 600px; border: 1px solid #ccc;">
                            <iframe src="manual/MANUAL DE USUARIO - RESPONSABLE DE OFICINA - SIRELI.pdf"
                                width="100%"
                                height="100%"
                                style="border: none;"></iframe>
                        </div>
                    </div>
                </div>
            
        </div>


    </div>
</div>



<?php
include('footer.php')

?>

<script src="scripts/index.js"></script>