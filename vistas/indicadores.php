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
                            <li class="breadcrumb-item" aria-current="page">Indicadores</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Indicadores</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- [ breadcrumb ] end --><!-- [ Main Content ] start -->

        <div class="row mt-0"><!-- [ sample-page ] start -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formIndicador" method="POST" class="mb-0">
                            <div class="row g-3"> <!-- Aplicando el espaciado entre las columnas -->

                                <div class="col-12 col-sm-6 col-md-4 col-lg-4 d-flex align-items-center">
                                    <label for="indicador_nombre" class="form-label me-2">Nombre:</label>
                                    <input type="text" class="form-control" id="indicador_nombre" placeholder="Digite nombre del indicador" required>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4 col-lg-6 d-flex align-items-center">
                                    <label for="componente_id" class="form-label me-2">Componente:</label>
                                    <select class="form-select" id="componente_id" name="componente_id" data-live-search="true" title="Elige el componente ...">
                                    </select>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-2 d-flex align-items-center">
                                    <button class="btn btn-primary w-100" id="btnRegistrarIndicador" type="submit">
                                        <i class="custom-logout-1-outline"></i> Registrar Indicador
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card table-card">
                    <div class="card-body">
                    </div>
                    <div class="table-responsive">
                        <div class="datatable-wrapper datatable-loading no-footer searchable fixed-columns">
                            <div class="datatable-container">
                                <table class="table table-hover datatable-table" id="tblIndicadores" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Componente</th>
                                            <th>Nombre de Indicador</th>
                                            <th>Medios de Verificación</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
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

<!-- Editar Componente -->

<div class="modal fade" id="EditarIndicadorModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="modal-content" method="POST">
                <div class="modal-header">
                    <h5 class="mb-0">Editar Indicador</h5>
                    <a href="#" id="btnCerrarModal" class="avtar avtar-s btn-link-danger btn-pc-default ms-auto" data-bs-dismiss="modal">
                        <i class="ti ti-x f-20"></i>
                    </a>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="indicador_id" name="indicador_id">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-2">
                                <label class="form-label">Nombre del Componente</label>
                                <input type="text" class="form-control" id="indicador_nombre_actual" name="indicador_nombre_actual">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div class="flex-grow-1 text-end">
                        <button type="button" class="btn btn-link-danger btn-pc-default" data-bs-dismiss="modal" id="btnCerrarModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnEditarIndicador">Guardar</button>
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

<script src="scripts/indicadores.js"></script>