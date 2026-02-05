<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}
// Validar que el usuario sea administrador
if ($_SESSION['rol_id'] != "3" && $_SESSION['rol_id'] != "2") {
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
                                                <th>Comentario</th>
                                                <?php
                                                if ($_SESSION['rol_id'] != "3") {
                                                ?><th>Accion</th>
                                                <?php

                                                }

                                                ?>
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

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item text-center">
                            <div class="d-flex flex-column align-items-center">
                                <p class="mb-1 fw-bold" id="evidencia-nombre"></p>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <span id="evidencia-estado"></span>
                                    <span id="fecha-subsanacion" style="display: none;"></span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex align-items-cente">
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Nivel de cumplimiento</label>
                                <p class="mb-0" id="evidencia-cumplimiento"></p>
                            </div>
                        </li>

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
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Coordinadores</label>
                                <p class="mb-0" id="coordinadores"></p>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Indicador</label>
                                <p class="mb-0" id="indicador-nombre"></p>
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
                                <label class="mb-0 flex-shrink-0 fw-semibold" style="width: 120px;">Creado por</label>
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


<!-- Calificar Evidencia Modal -->
<div class="modal fade" id="RegistrarCalificacionModal" data-bs-keyboard="false"  role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="modal-content" id="formCalificacion" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Calificación</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="historial_id" name="historial_id">
                    <div class="mb-3">
                        <label for="grado" class="form-label">Calificación <span class="text-danger">(*)</span></label>
                        <select class="form-select" id="grado_id" name="grado" required>
                            <option value="">Seleccionar</option>

                        </select>
                    </div>
                    <div class="mb-3" id="fechaReprogramacionContainer" style="display: none;">
                        <label for="fecha_reprogramacion" class="form-label">Fecha de Subsanación <span class="text-danger">(*)</span></label>
                        <input type="date" class="form-control" id="fecha_reprogramacion" name="fecha_reprogramacion" required>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones (Opcional)</label>
                        <textarea type="text" class="form-control" id="observaciones" name="observaciones" placeholder="Ingrese una observación"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="archivo_observacion" class="form-label">Archivo Adjunto (Opcional)</label>
                        <input type="file" class="form-control" id="archivo_observacion" name="archivo_observacion">
                        <span class="form-text text-muted">Máximo permitido: 100 MB</span>
                    </div>
                    <div class="text-muted mt-2">
                        <small><span class="text-danger">(*)</span> Campo obligatorio</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarCalificacion">Guardar Calificación</button>

                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPDF" tabindex="-1" aria-labelledby="modalPDFLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-sm-down modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPDFLabel">Vista previa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <iframe id="iframePDF" src="" frameborder="0" style="width: 100%; height: 70vh;"></iframe>
      </div>
    </div>
  </div>
</div>

<!-- Modal de carga -->
<div class="modal fade" id="modalSpinner" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 200vw; margin: 0; height: 100vh; pointer-events: none;">
    <div class="modal-content d-flex align-items-center justify-content-center text-center p-4"
         style="background-color: rgba(0,0,0,0.5); border: none; border-radius: 0; width: 100vw; height: 100vh; box-shadow: none; pointer-events: auto;">
      <div class="bg-light p-4 rounded shadow-sm" style="background-color: #e5f7e1;">
        <div class="spinner-border text-success mb-3" role="status" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <div>
          <strong>Registrando calificación...</strong>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- [ Main Content ] end -->
<?php
include('footer.php')

?>

<script src="scripts/index.js"></script>
<?php
// Cargar script según el rol
if ($_SESSION['rol_id'] == "3") {
    echo '<script src="scripts/responsable-evidencias.js"></script>';
} elseif ($_SESSION['rol_id'] == "2") {
    echo '<script src="scripts/evaluador-evidencias.js"></script>';
}
?>
<!-- Mejor unir un solo js -->