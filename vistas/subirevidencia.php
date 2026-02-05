<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}
// Validar que el usuario sea RESPONSABLE
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
    <div class="pc-content"><!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="menu.php">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Evidencias Pendientes</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Subir Evidencia</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row mt-0">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card border m-2">
                        <form id="formSubirEvidencia" enctype="multipart/form-data">
                            <input type="hidden" id="evidencia_id" name="evidencia_id">
                            <div class="card-body py-2 px-3">
                                <div class="d-sm-flex align-items-center">
                                    <ul class="list-inline me-auto my-1">
                                        <li class="list-inline-item align-bottom">
                                            <a href="javascript:history.back();" class="avtar avtar-s btn-link-secondary" id="mail-back_inbox">
                                                <i class="ti ti-chevron-left f-16"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item align-bottom">
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/images/user/avatar-1.jpg" alt="user-image" class="img-user rounded-circle" style="width: 40px; height: 40px;">
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="mb-0 text-truncate" id="evidencia-nombre" style="font-size: 14px;">Nombre de Evidencia</h6>
                                                    <p class="mb-0 text-muted text-sm" id="usuario-evidencia" style="font-size: 12px;">Registrado por: &lt;usuario&gt;</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <ul class="list-inline ms-sm-auto ms-2 my-1">
                                        <li class="list-inline-item text-muted" id="fecha-registro" style="font-size: 12px;">Fecha de Registro</li>
                                    </ul>
                                </div>
                                <hr class="border border-secondary-subtle mt-2">
                                <!-- Contenido del mensaje -->
                                <div class=" rounded my-3 mx-3">
                                    <div class="d-flex align-items-center flex-wrap mb-2">
                                        <span class="badge text-bg-primary text-wrap text-break lh-1 text-start mt-3 d-block w-100 p-2">RESPONSABLES</span>
                                    </div>
                                    <div class="border rounded p-2 mt-1 bg-white">
                                        <p id="responsables" class="mb-0 f-12" style="text-align: justify;">Responsables</p>
                                    </div>

                                    <div class="d-flex align-items-center flex-wrap mb-2">
                                        <span class="badge text-bg-primary text-wrap text-break lh-1 text-start mt-3 d-block w-100 p-2">INDICADOR</span>
                                    </div>
                                    <div class="border rounded p-2 mt-1 bg-white">
                                        <p id="indicador-nombre" class="mb-0 f-14" style="text-align: justify;"></p>
                                    </div>

                                    <div class="d-flex align-items-center flex-wrap mb-2">
                                        <span class="badge text-bg-primary text-wrap text-break lh-1 text-start mt-3 d-block w-100 p-2">MEDIO DE VERIFICACIÓN</span>
                                    </div>
                                    <div class="border rounded p-2 mt-1 bg-white">
                                        <p id="medio-nombre" class="mb-0 f-14" style="text-align: justify;">Medio</p>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap mb-2">
                                        <span class="badge text-bg-primary text-wrap text-break lh-1 text-start mt-3 d-block w-100 p-2">CONSIDERACIONES</span>
                                    </div>
                                    <div class="border rounded p-2 mt-1 bg-white">
                                        <p id="evidencia-consideraciones" class="mb-0 f-14" style="text-align: justify;">Consideraciones</p>
                                    </div>
                                </div>
                                <!-- Subida de archivos -->
                                <div class="p-3 rounded">
                                    <label for="archivo_presentacion" class="form-label fw-bold">Subir archivo de evidencia</label>
                                    <input type="file" id="archivo_presentacion" name="archivo_presentacion" class="form-control mb-3" required />
                                    <p class="form-text text-muted" style="font-size:12px;">
                                        Tipos de archivo permitidos: DOCX, PDF, RAR, ZIP. Tamaño máximo: 200 MB.
                                    </p>
                                    <div id="vista-previa" class="text-center" style="display: none;">
                                        <h5>Vista previa</h5>
                                        <div id="vista-previa-contenido"></div>
                                    </div>
                                    <div class="alert alert-success text-center" role="alert">
                                        <strong>DECLARACIÓN DE VERACIDAD</strong><br>
                                        En calidad de responsable, certifico que la información y evidencias remitidas son auténticas, veraces y constituyen documentación oficial que podrá ser presentada a SUNEDU en el marco del cumplimiento de las Condiciones Básicas de Calidad.
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-success">Enviar archivo</button>
                                    </div>
                                </div>

                                <!-- FilePond CSS & JS -->
                                <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
                                <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSpinner" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 200vw; margin: 0; height: 100vh; pointer-events: none;">
        <div class="modal-content d-flex align-items-center justify-content-center text-center p-4"
            style="background-color: rgba(0,0,0,0.5); border: none; border-radius: 0; width: 100vw; height: 100vh; box-shadow: none; pointer-events: auto;">
            <div class="bg-light p-4 rounded shadow-sm" style="background-color: #e5f7e1;">
                <div class="spinner-border text-success mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div>
                    <strong>Subiendo evidencia...</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const input = document.querySelector('#archivo_presentacion');
    const pond = FilePond.create(input, {
        allowMultiple: false,
        acceptedFileTypes: [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/zip',
            'application/x-rar-compressed'
        ],
        labelIdle: 'Arrastra y suelta un archivo o <span class="filepond--label-action">explora</span>'
    });

    const vistaPrevia = document.getElementById('vista-previa');
    const vistaPreviaContenido = document.getElementById('vista-previa-contenido');

    pond.on('addfile', (error, file) => {
        if (error) {
            console.error('Error al agregar archivo:', error);
            return;
        }

        const archivo = file.file;
        const tipo = archivo.type;
        vistaPreviaContenido.innerHTML = '';

        if (tipo === 'application/pdf') {
            const iframe = document.createElement('iframe');
            iframe.src = URL.createObjectURL(archivo);
            iframe.style.width = '100%';
            iframe.style.height = '300px';
            vistaPreviaContenido.appendChild(iframe);
        } else {
            vistaPreviaContenido.innerHTML = `<p><strong>Archivo seleccionado:</strong> ${archivo.name}</p>`;
        }

        vistaPrevia.style.display = 'block';
    });

    pond.on('removefile', () => {
        vistaPrevia.style.display = 'none';
        vistaPreviaContenido.innerHTML = '';
    });
</script>

<!-- [ Main Content ] end -->
<?php
include('footer.php')

?>

<script src="scripts/responsable-evidencias.js"></script>
<script src="scripts/index.js"></script>