<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}
// Validar que el usuario sea Responsable
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
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Notificaciones</li>
                        </ul>
                    </div>

                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-1">Todas las Notificaciones</h4>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-0 p-0">
            <!-- Columna para el conteo de notificaciones leídas y no leídas -->
            <div class="col-12 col-md-4" id="notificaciones-stats">
                <div class="card">
                    <div class="card-header">
                        <h5>Estado de Notificaciones</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>
                                <svg class="pc-icon me-2">
                                    <use xlink:href="#custom-notification"></use>
                                </svg>
                                Leídas:
                            </strong>
                            <span id="notificaciones-leidas">0</span>
                        </p>
                        <p>
                            <strong>
                                <svg class="pc-icon me-2"  style="color: #007bff;">
                                    <use xlink:href="#custom-notification"></use>
                                </svg>No Leídas:
                            </strong>
                            <span id="notificaciones-no-leidas">0</span>
                        </p>
                    </div>
                </div>
            </div>
            <!-- Columna para las Notificaciones -->
            <div class="col-12 col-md-8" id="notificaciones-column" style="max-height: 600px; overflow-y: auto;">
                <div id="notificaciones-list" class="row">
                    <!-- Aquí se agregarán las notificaciones -->
                </div>
            </div>


        </div>

    </div>
</div>



<?php
include('footer.php')

?>

<script src="scripts/index.js"></script>