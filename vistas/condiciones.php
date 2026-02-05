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
                            <li class="breadcrumb-item" aria-current="page">Condiciones</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Condiciones Básicas de Calidad</h4>
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
                        <form id="formCondicion" method="POST" class="mb-0">
                            <div class="row g-3"> <!-- Aplicando el espaciado entre las columnas -->

                                <div class="col-12 col-sm-6 col-md-4 col-lg-4 d-flex align-items-center mb-3 mb-sm-0">
                                    <label for="cbc_nombre" class="form-label me-2">Nombre:</label>
                                    <input type="text" class="form-control" id="cbc_nombre" placeholder="Digite nombre de la condición" required>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4 col-lg-6 d-flex align-items-center mb-3 mb-sm-0">
                                    <label for="cbc_descripcion" class="form-label me-2">Descripción:</label>
                                    <input type="text" class="form-control" id="cbc_descripcion" placeholder="Digite descripción de la condición" required>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-2 d-flex align-items-center">
                                    <button class="btn btn-primary w-100" id="btnRegistrarCondicion" type="submit">
                                        <i class="custom-logout-1-outline"></i> Registrar Condición
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
                                <table class="table table-hover datatable-table w-100" id="tblCondiciones">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre de condición</th>
                                            <th>Descripción</th>
                                            <th>Componente</th>
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


<!-- Editar Condicion -->

<div class="modal fade" id="EditarCondicionModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="modal-content" method="POST">
                <div class="modal-header">
                    <h5 class="mb-0">Editar Condicion</h5>
                    <a href="#" id="btnCerrarModal" class="avtar avtar-s btn-link-danger btn-pc-default ms-auto" data-bs-dismiss="modal">
                        <i class="ti ti-x f-20"></i>
                    </a>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cbc_id" name="cbc_id">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-2" id="divOficina_nom">
                                <label class="form-label">Nombre de la Condicion</label>
                                <input type="text" class="form-control" id="cbc_nombre_actual" name="cbc_nombre_actual">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-2">
                                <label class="form-label">Descripción</label>
                                <input type="text" class="form-control" id="cbc_descripcion_actual" name="cbc_descripcion_actual">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div class="flex-grow-1 text-end">
                        <button type="button" class="btn btn-link-danger btn-pc-default" data-bs-dismiss="modal" id="btnCerrarModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnEditarCondicion">Guardar</button>
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

<script src="scripts/condiciones.js"></script>
