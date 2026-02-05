<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['rol_id'] != "1" && $_SESSION['rol_id'] != "2" && $_SESSION['rol_id'] != "4") {
    header("Location: dashboard.php");
    exit();
}

?>



<?php
include('head.php')
?>

<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content"><!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="menu.php">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Reportes</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Reporte</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- [ breadcrumb ] end --><!-- [ Main Content ] start -->
        <div class="row mt-0"><!-- [ sample-page ] start -->
            <div class="col-sm-12">
                <div class="card table-card">
                    <div class="card-body">
                        <div class="mx-4 mt-4 mb-0">
                            <div class="row g-3 align-items-end">
                                <!-- Filtro de Condicion -->
                                <div class="col-md-3 col-sm-6">
                                    <label for="cbc_id" class="form-label">Filtrar por Condicion</label>
                                    <select id="cbc_id" class="form-select" aria-label="Select Oficina">

                                    </select>
                                </div>

                                <!-- Filtro de Oficina -->
                                <div class="col-md-3 col-sm-6">
                                    <label for="oficina_id" class="form-label">Filtrar por Oficina</label>
                                    <select id="oficina_id" class="form-select" aria-label="Select Oficina">

                                    </select>
                                </div>

                                <!-- Botón de Filtrar -->
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label invisible d-none d-md-block">Filtrar</label>
                                    <button id="btnFiltrarOficina" class="btn btn-primary w-100">
                                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                                    </button>
                                </div>

                                <!-- Botón de Ver Todos -->
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label invisible d-none d-md-block">Ver Todos</label>
                                    <button id="btnVerTodos" class="btn btn-secondary w-100">
                                        Ver Todos
                                    </button>
                                </div>
                            </div>
                            <div id="filtros-aplicados" class="mt-3 mb-3" style="display: none; display: flex; align-items: center;">
                                <p class="my-auto"><strong>Filtros aplicados:</strong></p>
                                <div id="filtros-mostrados" class="d-flex flex-wrap gap-2 ms-2"></div>
                            </div>

                        </div>
                        <div class="table-responsive">
                            <div class="datatable-wrapper datatable-loading no-footer searchable fixed-columns">
                                <div class="datatable-container">
                                    <table class="table table-hover datatable-table" id="tblReporte" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 29.769392033542978%;">Condición</th>
                                                <th style="width: 16.666666666666664%;">Componente</th>
                                                <th style="width: 8.70020964360587%;">Indicador</th>
                                                <th style="width: 7.861635220125786%;">Medio</th>
                                                <th style="width: 13.20754716981132%;">Evidencias</th>
                                                <th style="width: 13.20754716981132%;">Responsables</th>
                                                <th style="width: 13.20754716981132%;">Coordinadores</th>
                                                <th style="width: 7.861635220125786%;">Archivo</th>
                                                <th style="width: 13.20754716981132%;">Cumple Si/No</th>
                                                <th style="width: 13.20754716981132%;">Cumplimiento a Nivel de MV</th>
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
        <style>
            #tblReporte thead th {
                background-color: #343a40;
                /* Gris oscuro tipo Bootstrap */
                color: #fff;
                /* Texto blanco */
                text-align: center;
                vertical-align: middle;
                border: 0.5px solid #b0b0b0;
            }

            #tblReporte th,
            #tblReporte td {
                border: 0.3px solid #b0b0b0;
                vertical-align: middle;
            }
        </style>


    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalPDF" tabindex="-1" aria-labelledby="modalPDFLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPDFLabel">Vista previa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <iframe id="iframePDF" src="" frameborder="0" width="100%" height="700px"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
<?php
include('footer.php')
?>

<script src="scripts/index.js"></script>
<script src="scripts/estadisticas.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<!-- Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<!-- JSZip para Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Buttons HTML5 export -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>