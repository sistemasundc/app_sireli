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
                            <li class="breadcrumb-item"><a href="menu.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Evidencias</li>
                        </ul>
                    </div>

                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-1">Todas las Evidencias</h4>
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

                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-0 p-0">
            <div class="col-lg-12">
                <div class="card">

                    <ul class="nav nav-tabs profile-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="profile-tab-5" data-bs-toggle="tab" href="#profile-5" role="tab" aria-selected="true" data-estado="">
                                <i class="ti ti-users me-2"></i>Todos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="profile-tab-1" data-bs-toggle="tab" href="#profile-1" role="tab" aria-selected="false" tabindex="-1" data-estado="Pendiente">
                                <i class="ti ti-user me-2"></i>Pendientes
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="profile-tab-3" data-bs-toggle="tab" href="#profile-3" role="tab" aria-selected="false" tabindex="-1" data-estado="Observado">
                                <i class="ti ti-id me-2"></i>Observado
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="profile-tab-2" data-bs-toggle="tab" href="#profile-2" role="tab" aria-selected="false" tabindex="-1" data-estado="En Revision,Enviado">
                                <i class="ti ti-file-text me-2"></i>Enviados
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="profile-tab-4" data-bs-toggle="tab" href="#profile-4" role="tab" aria-selected="false" tabindex="-1" data-estado="Finalizado">
                                <i class="ti ti-lock me-2"></i>Finalizado
                            </a>
                        </li>
                    </ul>


                    <div id="mis-evidencias-list" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 m-2 mt-3">

                        <!-- Las tarjetas generadas se añadirán aquí dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
include('footer.php')

?>

<script src="scripts/responsable-evidencias.js"></script>
<script src="scripts/index.js"></script>