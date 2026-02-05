<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}
// Validar que el usuario sea administrador
if ($_SESSION['rol_id'] != "1" && $_SESSION['rol_id'] != "2") {
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

    .ql-toolbar {
        width: 100%;
    }

    .ql-container {
        width: 100%;
    }

    .card-vencida {
        background-color: #fff0f0 !important;
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
                                <h4 class="mb-0">Evidencias</h4>
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
                        <form id="formEvidencia" method="POST" class="mb-0">
                            <div class="row g-3"> <!-- Aplicando el espaciado entre las columnas -->

                                <div class="col-12">
                                    <label for="medio_id" class="form-label fw-bold">Medio de verificación</label><span class="text-danger"> (*)</span>
                                    <select class="form-select" id="medio_id" name="medio_id" data-live-search="true"></select>
                                </div>
                            </div>

                            <div class="row g-3 my-2">
                                <div class="col-12 col-sm-4">
                                    <label for="evidencia_nombre" class="form-label fw-bold">Nombre de Evidencia</label><span class="text-danger"> (*)</span>
                                    <input type="text" class="form-control" id="evidencia_nombre" placeholder="Digite descripción de la evidencia" required>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <label for="oficina_id_evidencia" class="form-label fw-bold">Responsable (Oficinas)</label><span class="text-danger"> (*)</span>
                                    <select class="form-control" id="oficina_id_evidencia" name="oficina_id_evidencia[]" multiple></select>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <label for="coordinador_id_evidencia" class="form-label fw-bold">Coordinadores (Oficinas)</label><span class="text-danger"> (*)</span>
                                    <select class="form-control" id="coordinador_id_evidencia" name="coordinador_id_evidencia[]" multiple></select>
                                </div>
                            </div>

                            <div class="row g-3 my-2">
                                <div class="col-12">
                                    <label for="evidencia_consideraciones" class="form-label fw-bold">Consideraciones</label><span> (Opcional)</span>

                                    <div id="evidencia_consideraciones_container" style="height: auto; min-height: 100px; resize: vertical; overflow: auto; border: 1px solid #ced4da; border-radius: 0.375rem;">
                                        <div id="evidencia_consideraciones" style="min-height: 100px;"></div>
                                    </div>
                                </div>
                            </div>


                            <div class="row g-3 my-2">
                                <div class="col-12 col-sm-6 col-md-5">
                                    <label for="fecha_plazo_inicio" class="form-label fw-bold">Fecha plazo Inicial</label><span class="text-danger"> (*)</span>
                                    <input type="date" class="form-control" id="fecha_plazo_inicio" required>
                                </div>

                                <div class="col-12 col-sm-6 col-md-5">
                                    <label for="fecha_plazo_fin" class="form-label fw-bold">Fecha plazo Final</label><span class="text-danger"> (*)</span>
                                    <input type="date" class="form-control" id="fecha_plazo_fin" required>
                                </div>

                                <div class="col-12 col-sm-12 col-md-2 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" id="btnRegistrarEvidencia" type="submit">
                                        <i class="custom-logout-1-outline"></i> Registrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card table-card">
                    <div class="mx-4 mt-4 mb-0">
                        <div class="row g-3 align-items-end">
                            <!-- Filtro de Oficina -->
                            <div class="col-md-3 col-sm-6">
                                <label for="oficina_id" class="form-label">Filtrar por Oficina</label>
                                <select id="oficina_id" class="form-select">
                                    <!-- dinámicamente -->
                                </select>
                            </div>

                            <!-- Filtro de Búsqueda -->
                            <div class="col-md-3 col-sm-6">
                                <label for="inputBusqueda" class="form-label">Buscar evidencia</label>
                                <input type="text" id="inputBusqueda" class="form-control" placeholder="Ingrese nombre de la evidencia">
                            </div>

                            <!-- Botón de Filtrar -->
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label invisible d-none d-md-block">Filtrar</label>
                                <button id="btnFiltrarOficina" class="btn btn-primary w-100">
                                    <span class="pc-micon">
                                        <svg class="pc-icon">
                                            <use xlink:href="#custom-sort-outline"></use>
                                        </svg>
                                    </span> Filtrar
                                </button>
                            </div>

                            <!-- Botón de Ver Todos -->
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label invisible d-none d-md-block">Ver Todos</label>
                                <button id="btnVerTodos" class="btn btn-secondary w-100">
                                    <span class="pc-micon">
                                        <svg class="pc-icon">
                                            <use xlink:href="#custom-layer"></use>
                                        </svg>
                                    </span> Ver Todos
                                </button>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label invisible d-none d-md-block">Modificar Plazos</label>
                                <button id="btnModificarPlazos" class="btn btn-secondary w-100">
                                    <span class="pc-micon">
                                        <svg class="pc-icon">
                                            <use xlink:href="#custom-layer"></use>
                                        </svg>
                                    </span> Modificar Plazos
                                </button>
                            </div>
                        </div>
                        <div id="filtros-aplicados" class="mt-3" style="display: none; display: flex; align-items: center;">
                            <p class="my-auto"><strong>Filtros aplicados:</strong></p>
                            <div id="filtros-mostrados" class="d-flex flex-wrap gap-2 ms-2"></div>
                        </div>
                    </div>

                    <div class="card-body mt-4">

                        <div id="evidencia-list" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 m-2">
                            <!-- Las tarjetas generadas se añadirán aquí dinámicamente -->
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAll">
                                <i class="ti ti-square-check me-1"></i> Seleccionar todo
                            </button>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <nav>
                                <ul class="pagination" id="paginacion-evidencias"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>



        </div><!-- [ sample-page ] end -->
    </div><!-- [ Main Content ] end -->
</div>

<div class="offcanvas offcanvas-end" id="offcanvasFileDesc">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Vista Previa</h5>
        <a href="javascript:void(0)" class="avtar avtar-s btn-link-danger btn-pc-default ms-auto" id="closeOffcanvas">
            <i class="ti ti-circle-x f-18"></i>
        </a>
    </div>
    <hr class="border border-secondary-subtle m-0">
    <div class="offcanvas-body">
        <div class="my-3 text-center">
            <h5 class="mb-1 mt-4">Document-final.docx</h5>
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                <div class="avtar avtar-xs bg-light-danger">
                    <i class="ti ti-calendar-event f-20"></i>
                </div>
                <p id="evidenciaPlazo" class="mb-1 text-muted" style="margin: 0;">16 Nov 2022</p>
            </div>
            <span id="evidenciaEstado" class="badge text-bg-success"></span>
        </div>
        <hr class="my-4 border border-secondary-subtle">

        <!-- RESPONSABLES -->
        <span class="badge text-bg-secondary text-wrap text-break lh-1 text-start mt-3 d-block w-100">RESPONSABLES</span>
        <div class="border rounded p-2 mt-1 bg-white">
            <p id="evidenciaOficinas" class="mb-0 text-dark fw-semibold f-12">Nombre</p>
        </div>
        <!-- COORDINADORES -->
        <span class="badge text-bg-secondary text-wrap text-break lh-1 text-start mt-3 d-block w-100">COORDINADORES</span>
        <div class="border rounded p-2 mt-1 bg-white">
            <p id="evidenciaCoordinadores" class="mb-0 text-dark fw-semibold f-12">Nombre</p>
        </div>
        <!-- INDICADOR -->
        <span class="badge text-bg-secondary text-wrap text-break lh-1 text-start mt-3 d-block w-100">INDICADOR</span>

        <div class="border rounded p-2 mt-1 bg-white">
            <p id="indicadorNombre" class="mb-0 text-muted f-12" style="text-align: justify;">Medio</p>
        </div>

        <!-- MEDIO DE VERIFICACIÓN -->
        <span class="badge text-bg-secondary text-wrap text-break lh-1 text-start mt-3 d-block w-100">MEDIO DE VERIFICACIÓN</span>

        <div class="border rounded p-2 mt-1 bg-white">
            <p id="medioNombre" class="mb-0 text-muted f-12" style="text-align: justify;">Medio</p>
        </div>

        <!-- CONSIDERACIONES -->
        <span class="badge text-bg-secondary text-wrap text-break lh-1 text-start mt-3 d-block w-100">CONSIDERACIONES</span>

        <div class="border rounded p-2 mt-1 bg-white">
            <p id="evidenciaConsideraciones" class="mb-0 text-muted f-12" style="text-align: justify;">Esta evidencia debe cumplir con el formato requerido.</p>
        </div>

        <!-- <div class="row g-2 mt-3">
            <div class="col-6">
                <div class="d-grid">
                    <button class="btn btn-light-secondary">Editar</button>
                </div>
            </div>
            <div class="col-6">
                <div class="d-grid">
                    <button class="btn btn-light-danger">Cerrar</button>
                </div>
            </div>
        </div> -->
    </div>
</div>

<!-- Editar MV -->

<div class="modal fade" id="EditarEvidenciaModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="modal-content" method="POST">
                <div class="modal-header">
                    <h5 class="mb-0">Editar Evidencias</h5>
                    <a href="#" id="btnCerrarModal" class="avtar avtar-s btn-link-danger btn-pc-default ms-auto" data-bs-dismiss="modal">
                        <i class="ti ti-x f-20"></i>
                    </a>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="evidencia_id" name="evidencia_id">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-2">
                                <label class="form-label">Nombre de la Evidencia</label><span class="text-danger"> (*)</span>
                                <input type="text" class="form-control" id="evidencia_nombre_actual" name="evidencia_nombre_actual">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="oficina_id_evidencia_actual" class="form-label">Responsable (Oficinas)</label><span class="text-danger"> (*)</span>
                            <select class="form-control" id="oficina_id_evidencia_actual" name="oficina_id_evidencia_actual[]" multiple></select>
                        </div>
                        <div class="col-sm-12 mt-2">
                            <label for="coordinador_id_evidencia_actual" class="form-label">Coordinador (Oficinas)</label>
                            <select class="form-control" id="coordinador_id_evidencia_actual" name="coordinador_id_evidencia_actual[]" multiple></select>
                        </div>
                        <div class="col-sm-12 mt-2">
                            <div class="mb-2">
                                <label class="form-label">Fecha Plazo Inicial</label><span class="text-danger"> (*)</span>
                                <input type="date" class="form-control" id="fecha_plazo_inicio_actual" name="fecha_plazo_inicio_actual">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="mb-2">
                                <label class="form-label">Fecha Plazo Final</label><span class="text-danger"> (*)</span>
                                <input type="date" class="form-control" id="fecha_plazo_fin_actual" name="fecha_plazo_fin_actual">
                            </div>
                        </div>
                        <div class="row g-3 my-2">
                            <div class="col-12">
                                <label for="evidencia_consideraciones_actual" class="form-label fw-bold">Consideraciones</label><span> (Opcional)</span>

                                <div style="height: auto; min-height: 100px; resize: vertical; overflow: auto; border: 1px solid #ced4da; border-radius: 0.375rem;">
                                    <div id="evidencia_consideraciones_actual" style="min-height: 200px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <div class="flex-grow-1 text-end">
                        <button type="button" class="btn btn-link-danger btn-pc-default" data-bs-dismiss="modal" id="btnCerrarModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnActualizarEvidencia">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Editar Plazos -->
<div class="modal fade" id="EditarPlazosEvidenciaModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="formEditarPlazosEvidencia" method="POST">
                <div class="modal-header">
                    <h5 class="mb-0">Editar Plazos por Grupo de Evidencias</h5>
                    <a href="#" id="btnCerrarModal" class="avtar avtar-s btn-link-danger btn-pc-default ms-auto" data-bs-dismiss="modal">
                        <i class="ti ti-x f-20"></i>
                    </a>
                </div>
                <div class="modal-body">
                    <!-- input ocultos -->
                    <input type="hidden" id="evidencia_id" name="evidencia_id">
                    <input type="hidden" id="evidenciasSeleccionadas" name="evidenciasSeleccionadas">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="ti ti-info-circle me-2 f-20"></i>
                        <div>
                            Puede registrar <b>solo el plazo inicial</b>,
                            <b>solo el plazo final</b> o <b>ambos</b>. <br>
                            Si no modifica un campo, se mantendrá el valor actual.
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-2">
                                <label class="form-label">Fecha Plazo Inicial</label>
                                <input type="date" class="form-control" id="fecha_plazo_inicio_nuevo" name="fecha_plazo_inicio_nuevo">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="mb-2">
                                <label class="form-label">Fecha Plazo Final</label>
                                <input type="date" class="form-control" id="fecha_plazo_fin_nuevo" name="fecha_plazo_fin_nuevo">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-footer justify-content-between">
                    <div class="flex-grow-1 text-end">
                        <button type="button" class="btn btn-link-danger btn-pc-default" data-bs-dismiss="modal" id="btnCerrarModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnActualizarPlazosEvidencia">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    // Inicializar Quill en el contenedor 'consideraciones'
    var quillRegistrar = new Quill('#evidencia_consideraciones', {
        theme: 'snow',
        placeholder: 'Escribe las consideraciones aquí...',
        modules: {
            toolbar: [
                [{
                    'header': '1'
                }, {
                    'header': '2'
                }, {
                    'font': []
                }],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                ['bold', 'italic', 'underline'],
                [{
                    'align': []
                }],
                ['link']
            ]
        }
    });

    var quill = new Quill('#evidencia_consideraciones_actual', {
        theme: 'snow',
        placeholder: 'Escribe las consideraciones aquí...',
        modules: {
            toolbar: [
                [{
                    'header': '1'
                }, {
                    'header': '2'
                }, {
                    'font': []
                }],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                ['bold', 'italic', 'underline'],
                [{
                    'align': []
                }],
                ['link']
            ]
        }
    });
</script>

<!-- [ Main Content ] end -->
<?php
include('footer.php')

?>


<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- JS de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Cargar DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- Cargar DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Include select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />


<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
<!-- Cargar tu script de usuarios.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="scripts/evidencias.js"></script>
<script src="scripts/index.js"></script>