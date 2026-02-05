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
<style>
    .choices__list--dropdown {
        z-index: 1051 !important;
        position: absolute;
    }
</style>
<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content"><!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Dashboard</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- [ breadcrumb ] end --><!-- [ Main Content ] start -->


        <div class="row mt-0"><!-- [ sample-page ] start -->
            <?php
            // Verificar si el usuario tiene el ID igual a 1
            if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == "1" || isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == "2" || $_SESSION['rol_id'] == "4") {
            ?>

                <div class="row mt-0">
                    <div class="col-md-12">
                        <div class="alert alert-secondary" role="alert" style="background-color:#edf2ff; border-color: #0e5fbb;">
                            <strong style="color:#0e5fbb;">¡Bienvenido al Sistema de Registro para el Licenciamiento Institucional!</strong><br>
                            Este sistema está diseñado para garantizar el cumplimiento de las 8 condiciones básicas de calidad, asegurando así la excelencia educativa y administrativa.
                            Estamos comprometidos con la mejora continua para ofrecerte una experiencia óptima. ¡Gracias por ser parte de nuestro proceso de calidad!
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="cbc_id" class="form-label">Filtrar por Condicion</label>
                        <select id="cbc_id" class="form-select" aria-label="Select Oficina">
                            <option value="">Todas las Oficinas</option>
                            <!-- Las oficinas se añadirán aquí dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="componente_id" class="form-label">Filtrar por Componentes</label>
                        <select id="componente_id" class="form-select" aria-label="Select Oficina">
                            <option value="">Todas las Oficinas</option>
                            <!-- Las oficinas se añadirán aquí dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="indicador_id" class="form-label">Filtrar por Indicador</label>
                        <select id="indicador_id" class="form-select" aria-label="Select Oficina">
                            <option value="">Todas las Oficinas</option>
                            <!-- Las oficinas se añadirán aquí dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="oficina_id" class="form-label">Filtrar por Oficina</label>
                        <select id="oficina_id" class="form-select" aria-label="Select Oficina">
                            <option value="">Todas las Oficinas</option>
                            <!-- Las oficinas se añadirán aquí dinámicamente -->
                        </select>
                    </div>

                    <!-- Card 1 -->

                </div>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5 g-4 mt-2">
                    <!-- Total Condiciones -->
                    <div class="col">
                        <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="text-white m-0" id="TotalCondiciones"></h3>
                                    <span class="m-t-10 text-white">Condiciones</span>
                                </div>
                                <span class="pc-micon">
                                    <svg class="pc-icon" style="width: 24px; height: 24px;">
                                        <use xlink:href="#custom-element-plus"></use>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Componentes -->
                    <div class="col">
                        <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="text-white m-0" id="TotalComponentes"></h3>
                                    <span class="m-t-10 text-white">Componentes</span>
                                </div>
                                <span class="pc-micon">
                                    <svg class="pc-icon" style="width: 24px; height: 24px;">
                                        <use xlink:href="#custom-element-plus"></use>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Indicadores -->
                    <div class="col">
                        <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="text-white m-0" id="TotalIndicadores"></h3>
                                    <span class="m-t-10 text-white">Indicadores</span>
                                </div>
                                <span class="pc-micon">
                                    <svg class="pc-icon" style="width: 24px; height: 24px;">
                                        <use xlink:href="#custom-element-plus"></use>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Medios -->
                    <div class="col">
                        <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="text-white m-0" id="TotalMedios"></h3>
                                    <span class="m-t-10 text-white">Medios de Verificación</span>
                                </div>
                                <span class="pc-micon">
                                    <svg class="pc-icon" style="width: 24px; height: 24px;">
                                        <use xlink:href="#custom-element-plus"></use>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Evidencias -->
                    <div class="col">
                        <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="text-white m-0" id="TotalEvidencias"></h3>
                                    <span class="m-t-10 text-white">Evidencias</span>
                                </div>
                                <span class="pc-micon">
                                    <svg class="pc-icon" style="width: 24px; height: 24px;">
                                        <use xlink:href="#custom-element-plus"></use>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mt-0">
                    <!-- Gráfico de Condiciones -->
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    Avance de Medios de Verificación por Condiciones
                                </h5>
                                <span id="tituloCondiciones" class="badge text-bg-primary d-none"></span>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div id="chartCumplimientoMV" style="width: 100%; height: 100%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Medios de Verificación -->
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    Avance del Nivel de Cumplimiento por Medios de Verificación
                                </h5>
                                <span id="tituloMedios" class="badge text-bg-primary d-none"></span>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <table id="tblMediosCumplimiento" class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Medio de Verificación</th>
                                            <th>Nivel de Cumplimiento</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segunda Fila de Gráficos -->
                <div class="row g-4 mt-0">
                    <!-- Medios por CBC -->
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Medios de Verificación por CBC</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div id="chartMediosCBC" style="width: 100%; height: 100%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Grado de Cumplimiento por Evidencias -->
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Nivel de Cumplimiento por Evidencias</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div id="chartEvidencias" style="width: 100%; height: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>


            <?php
            }
            ?>

            <?php
            // Verificar si el usuario tiene el ID igual a 3
            if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == "3") {
            ?>
                <div class="col-md-12">
                    <div class="alert alert-secondary" role="alert" style="background-color:#edf2ff; border-color: #0e5fbb;">
                        <strong style="color:#0e5fbb;">¡Bienvenido al Sistema de Registro para el Licenciamiento Institucional!</strong><br>
                        Este sistema está diseñado para garantizar el cumplimiento de las 8 condiciones básicas de calidad, asegurando así la excelencia educativa y administrativa.
                        Estamos comprometidos con la mejora continua para ofrecerte una experiencia óptima. ¡Gracias por ser parte de nuestro proceso de calidad!
                    </div>
                </div>

                <div class="row mt-0" id="cards-container">
                    <!-- Las tarjetas se insertarán dinámicamente aquí -->
                </div>
                <div class="row mb-4">
                    <div class="col-sm-6 col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5>
                                    Avance del Nivel Cumplimiento de Evidencias
                                    <!-- <span class="badge text-bg-primary ms-2 d-none"></span> -->
                                </h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="height: 300px;">
                                <div id="chartOficinaCumplimientoMV" style="width: 100%; height: 100%;"></div>
                            </div>

                        </div>
                    </div>

                    <div class="col-sm-6 col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5>
                                    Nivel de Cumplimiento por Evidencias
                                    <span id="tituloMedios" class="badge text-bg-primary ms-2 d-none"></span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="card-body" style="height: 250px; overflow-y: auto;">
                                    <table id="tblOficinaMediosCumplimiento" class="table table-bordered table-striped table-xs mt-2" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Evidencias</th>
                                                <th>Estado</th>
                                                <th>Nivel de Cumplimiento</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            <?php
            }
            ?>
        </div>

    </div>
</div>
<!-- [ Main Content ] end -->
<?php
include('footer.php')
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />


<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.39.0"></script> -->
<script src="scripts/estadisticas.js"></script>
<script src="scripts/index.js"></script>