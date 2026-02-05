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
                            <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
                        </ul>
                    </div>

                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-1">Mi Perfil</h4>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
        <div class="row"><!-- [ sample-page ] start -->
            <div class="col-sm-12">
                <div class="tab-content">
                    <!-- Primera pestaña (Perfil) -->
                    <div class="tab-pane active"> <!-- Clase 'active' añadida aquí para mostrarla por defecto -->
                        <div class="row">
                            <div class="col-lg-4 col-xxl-3">
                                <div class="card">
                                    <div class="card-body position-relative">
                                        <div class="position-absolute end-0 top-0 p-3"><span class="badge bg-primary">Pro</span></div>
                                        <div class="text-center mt-3">
                                            <div class="chat-avtar d-inline-flex mx-auto">
                                                <img class="rounded-circle img-fluid wid-70" src="<?php echo $_SESSION['user_image'] ?>" alt="User image">
                                            </div>
                                            <h5 class="mb-0" id="usu_completo_perfil"></h5>
                                            <span class="badge text-bg-success mt-2" id="usu_estado_perfil">Success</span>
                                            <hr class="my-3 border border-secondary-subtle">
                                            <div class="d-inline-flex align-items-center justify-content-start w-100 mb-3"><i class="ti ti-mail me-2"></i>
                                                <p class="mb-0" id="usu_correo_perfil"></p>
                                            </div>
                                            <div class="d-inline-flex align-items-center justify-content-start w-100 mb-3"><i class="ti ti-phone me-2"></i>
                                                <p class="mb-0" id="usu_telf_perfil"></p>
                                            </div>
                                            <div class="d-inline-flex align-items-center justify-content-start w-100 mb-3"><i class="ti ti-building me-2"></i>
                                                <p class="mb-0" id="oficina_nom_perfil"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-xxl-9">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Detalles Personales</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item px-0 pt-0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-1 text-muted">Nombres y Apellidos</p>
                                                        <p class="mb-0 fw-bold" id="usu_completo"></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1 text-muted">Dependencia</p>
                                                        <p class="mb-0 fw-bold" id="oficina_nom"></p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item px-0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-1 text-muted">Celular</p>
                                                        <p class="mb-0 fw-bold" id="usu_telf"></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1 text-muted">Correo</p>
                                                        <p class="mb-0 fw-bold" id="usu_correo"></p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item px-0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-1 text-muted">Rol</p>
                                                        <span class="badge text-bg-warning" id="usu_rol">Success</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1 text-muted">Acceso desde</p>
                                                        <p class="mb-0 fw-bold" id="fech_crea"></p>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Actualizar datos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Los 3 inputs en una sola fila -->
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nombres <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="usu_nom_actual" value="">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Apellidos <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="usu_ape_actual" value="">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Celular <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control" id="usu_telf_actual" maxlength="9" value="" pattern="[0-9]{9}" required>
                                                <small class="form-text text-muted">Debe ingresar un número de celular válido (9 dígitos).</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botón de actualizar alineado a la derecha -->
                                    <div class="row text-end">
                                        <div class="col-12">
                                            <button class="btn btn-primary" id="btnActualizarPerfil">Actualizar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div><!-- [ sample-page ] end -->
        </div>


    </div>
</div>



<?php
include('footer.php')

?>

<script src="scripts/usuarios.js"></script>
<script src="scripts/index.js"></script>