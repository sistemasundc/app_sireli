<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Sistema de Registro para el Licenciamiento Institucional</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Able Pro is a trending dashboard template built with the Bootstrap 5 design framework. It is available in multiple technologies, including Bootstrap, React, Vue, CodeIgniter, Angular, .NET, and more.">
    <meta name="keywords"
        content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard">
    <meta name="author" content="Phoenixcoded">

    <!-- [Favicon] icon -->
    <link rel="icon" href="../assets/images/sistema/logo-tramite.png" type="image/x-icon"> <!-- [Font] Family -->
    <!-- <link rel="stylesheet" href="../assets/fonts/inter/inter.css" id="main-font-link" /> -->
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="../assets/css/style-preset.css">
    <link rel="stylesheet" href="../assets/css/plugins/style.css">
    <link rel="stylesheet" href="../assets/css/plugins/stylepropio.css">
    <link rel="stylesheet" href="../assets/css/plugins/dropzone.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <!-- Incluir el CSS de Quill -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
     
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="dashboard.php" class="b-brand text-primary">
                    <!-- ========   Change your logo from here   ============ -->
                    <img src="../assets/images/logoundc.png" class="img-fluid logo-lg" alt="logo" width="150px">
                    <span class="badge bg-light-primary rounded-pill ms-2 theme-version">v1.0</span>
                </a>
            </div>
            <div class="navbar-content">
                <div class="card pc-user-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="<?php echo $_SESSION['user_image'] ?>" alt="user-image" class="user-avtar wid-45 rounded-circle" />
                            </div>
                            <div class="flex-grow-1 ms-3 me-2">
                                <h6 class="mb-0"><?php echo $_SESSION["nomcompleto"] ?></h6>
                                <small><?php echo $_SESSION["rol_nom"] ?></small>
                            </div>
                            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-sort-outline"></use>
                                </svg>
                            </a>
                        </div>
                        <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                            <div class="pt-3">
                                <a href="perfil.php" class="d-flex align-items-center">
                                    <i class="ti ti-user"></i>
                                    <span>Mi Perfil</span>
                                    <!-- <span class="badge text-bg-warning ms-2">En Proceso</span> -->
                                </a>
                                <a href="../controladores/usuarios.php?op=salir">
                                    <i class="ti ti-power"></i>
                                    <span>Cerrar Sesion</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="pc-navbar">
                    <li class="pc-item pc-caption">
                        <label>Navegación</label>
                    </li>

                    <li class="pc-item">
                        <a href="dashboard.php" class="pc-link">
                            <span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-status-up"></use>
                                </svg>
                            </span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                    <?php
                    if ($_SESSION['rol_id'] == "1") {
                        echo '

        <li class="pc-item pc-caption">
          <label>Administración</label>
        </li>
        <li class="pc-item">
          <a href="usuarios.php" class="pc-link"
            ><span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-profile-2user-outline"></use>
              </svg> </span
            ><span class="pc-mtext">Usuarios</span></a
          >
        </li>
        <li class="pc-item">
          <a href="oficinas.php" class="pc-link"
            ><span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-element-plus"></use>
              </svg> </span
            ><span class="pc-mtext">Oficinas</span></a
          >
        </li>
        
                    <li class="pc-item pc-caption">
                        <label>Registros</label>
                        <svg class="pc-icon">
                            <use xlink:href="#custom-box-1"></use>
                        </svg>
                    </li>
                    <li class="pc-item">
                        <a href="condiciones.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-clipboard"></use>
                                </svg> </span><span class="pc-mtext">Condiciones</span></a>
                    </li>
                    <li class="pc-item">
                        <a href="componentes.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-box-1"></use>
                                </svg> </span><span class="pc-mtext">Componentes</span></a>
                    </li>
                    <li class="pc-item">
                        <a href="indicadores.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-mouse-circle"></use>
                                </svg> </span><span class="pc-mtext">Indicadores</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="verificaciones.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-layer"></use>
                                </svg> </span><span class="pc-mtext">Medios de Verificación</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="evidencias.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-document-text"></use>
                                </svg> </span><span class="pc-mtext">Evidencias</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="reporte.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-document-filter"></use>
                                </svg> </span><span class="pc-mtext">Reportes</span>
                        </a>
                    </li>
                    ';
                    }
                    ?>
                    <?php
                    if ($_SESSION['rol_id'] == "3") {
                        echo '<li class="pc-item">
                        <a href="evidencias_pendientes.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-document-text"></use>
                                </svg> </span><span class="pc-mtext">Pendientes</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="misevidencias.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-element-plus"></use>
                                </svg> </span><span class="pc-mtext">Todas las Evidencias</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="manual.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-note-1"></use>
                                </svg> </span><span class="pc-mtext">Manual de Uso</span>
                        </a>
                    </li>
                    ';
                    }
                    ?>
                    <?php
                    if ($_SESSION['rol_id'] == "2") {
                        echo '<li class="pc-item pc-caption">
                        <label>Registros</label>
                        <svg class="pc-icon">
                            <use xlink:href="#custom-box-1"></use>
                        </svg>
                    </li>
                    <li class="pc-item">
                        <a href="evidencias.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-document-text"></use>
                                </svg> </span><span class="pc-mtext">Evidencias</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Monitoreo</label>
                        <svg class="pc-icon">
                            <use xlink:href="#custom-box-1"></use>
                        </svg>
                    </li>
                    <li class="pc-item">
                        <a href="evidencias_recibir.php" class="pc-link d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-document-text"></use>
                                    </svg>
                                </span>
                                <span class="pc-mtext ms-2">Por Recibir</span>
                            </span>
                            <span class="badge bg-dark pc-h-badge" id="totalPorRecibir">0</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="evidencias_revisar.php" class="pc-link d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-document-text"></use>
                                    </svg>
                                </span>
                                <span class="pc-mtext ms-2">Por Revisar</span>
                            </span>
                            <span class="badge bg-dark pc-h-badge" id="totalPorRevisar">0</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="gestion_evidencias.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-element-plus"></use>
                                </svg> </span><span class="pc-mtext">Todas las Evidencias</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="reporte.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-document-filter"></use>
                                </svg> </span><span class="pc-mtext">Reportes</span>
                        </a>
                    </li>
                    ';
                    }
                    ?>
                    <?php
                    if ($_SESSION['rol_id'] == "4") {
                        echo '<li class="pc-item">
                        <a href="reporte.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-document-filter"></use>
                                </svg> </span><span class="pc-mtext">Reportes</span>
                        </a>
                    </li>
                    ';
                    }
                    ?>
                </ul>
                <div class="sidebar-support mt-2">
                    <a href="https://wa.me/51949026909" class="pc-link" target="_blank" style="display: flex; align-items: center; text-decoration: none;">
                        <span class="pc-micon" style="flex-shrink: 0; margin-right: 10px;">
                            <svg class="pc-icon" width="24" height="24" style="fill: gray;">
                                <use xlink:href="#custom-call-calling"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext" style="display: flex; flex-direction: column; align-items: flex-start;">
                            <span style="font-size: 10px; font-weight: bold;">CONSULTAS:</span>
                            <span class="mb-2" style="font-size: 13px;">Oficina de Gestión de Calidad</span>
                            <span class="badge text-bg-success" style="font-size: 12px;"><strong>WhatsApp: 949 026 909</strong></span>
                        </span>
                    </a>
                    <a href="https://wa.me/51994368324" class="pc-link" target="_blank" style="display: flex; align-items: center; text-decoration: none;">
                        <span class="pc-micon" style="flex-shrink: 0; margin-right: 10px;">
                            <svg class="pc-icon" width="24" height="24" style="fill: gray;">
                                <use xlink:href="#custom-call-calling"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext" style="display: flex; flex-direction: column; align-items: flex-start;">
                            <span style="font-size: 10px; font-weight: bold;">SOPORTE TÉCNICO:</span>
                            <span class="mb-2" style="font-size: 13px;">Ing. Aida Correa Gámez</span>
                            <span class="badge text-bg-success" style="font-size: 12px;"><strong>WhatsApp: 994 368 324</strong></span>
                        </span>
                    </a>
                    <!-- <a href="https://wa.me/51994368324" class="pc-link" target="_blank" style="display: flex; align-items: center; text-decoration: none;">
                        <span class="pc-micon" style="flex-shrink: 0; margin-right: 10px;">
                            <svg class="pc-icon" width="24" height="24" style="fill: gray;">
                                <use xlink:href="#custom-call-calling"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext" style="display: flex; flex-direction: column; align-items: flex-start;">
                            <span style="font-size: 10px; font-weight: bold;">CONSULTAS:</span>
                            <span class="mb-2" style="font-size: 14px;">Ing. Yenifer Peralta</span>
                            <span class="badge text-bg-success" style="font-size: 12px;"><strong>WhatsApp: 994 368 324</strong></span>
                        </span>
                    </a> -->
                </div>

            </div>
        </div>
    </nav>
    <!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
            
    <header class="pc-header">
        <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0 bg-white" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>

                </ul>
            </div>
            <style>
                /* Ocultar en pantallas pequeñas */
                @media (max-width: 576px) {
                    #oficina-nombre {
                        display: none !important;
                    }
                }
            </style>

            <div class="ms-auto d-flex align-items-center justify-content-between">
                <span id="oficina-nombre" class="badge bg-light-primary mx-2" style="font-size: 12px;">
                    <?php echo $_SESSION['oficina_nom'] ?>
                </span>

                <ul class="list-unstyled">

                    <?php
                    if ($_SESSION['rol_id'] == "3") {
                        echo '
                   
                    <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-notification"></use>
                            </svg>
                            <span class="badge bg-success pc-h-badge" id="notification-count">0</span> <!-- Aquí se actualizará el número de notificaciones -->
                        </a>
                        <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header d-flex align-items-center justify-content-between">
                                <h5 class="m-0">Notificationes</h5>
                                <!--<a href="#!" class="btn btn-link btn-sm">Marcar como leídas</a>-->
                            </div>
                            <div class="dropdown-body text-wrap header-notification-scroll position-relative" style="max-height: calc(100vh - 215px); overflow-y: auto;" id="notification-list">
                                <!-- Aquí se agregarán las notificaciones -->
                            </div>

                            <div class="text-center py-2">
                                <a href="notificaciones.php" class="link-danger">Ver todas las Notificaciones</a>
                            </div>
                        </div>
                    </li>';
                    }
                    ?>


                    <li class="dropdown pc-h-item header-user-profile">
                        <a
                            class="pc-head-link dropdown-toggle arrow-none me-0"
                            data-bs-toggle="dropdown"
                            href="#"
                            role="button"
                            aria-haspopup="false"
                            data-bs-auto-close="outside"
                            aria-expanded="false">
                            <img src="<?php echo $_SESSION['user_image'] ?>" alt="user-image" class="user-avtar" />
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header d-flex align-items-center justify-content-between">
                                <h5 class="m-0">Mi Perfil</h5>
                            </div>
                            <div class="dropdown-body">
                                <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                                    <div class="d-flex mb-1">
                                        <div class="flex-shrink-0">
                                            <img src="<?php echo $_SESSION['user_image'] ?>" alt="user-image" class="user-avtar wid-35" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1"><?php echo $_SESSION["nomcompleto"] ?></h6>
                                            <span><?php echo $_SESSION["usu_correo"] ?></span>
                                            <br>
                                            <span><?php echo $_SESSION['oficina_nom'] ?></span>
                                            <div id="oficina-id" data-oficina-id="<?php echo $_SESSION["oficina_id"]; ?>" style="display: none;"></div>
                                            <div id="usu-id" data-usu-id="<?php echo $_SESSION["usu_id"]; ?>" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <hr class="border-secondary border-opacity-50" />
                                    <!-- <div class="card">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h5 class="mb-0 d-inline-flex align-items-center"><svg class="pc-icon text-muted me-2">
                                                        <use xlink:href="#custom-notification-outline"></use>
                                                    </svg>Notification</h5>
                                                <div class="form-check form-switch form-check-reverse m-0">
                                                    <input class="form-check-input f-18" type="checkbox" role="switch" />
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <p class="text-span">Ajustes</p>
                                    <a href="perfil.php" class="dropdown-item">
                                        <span>
                                            <i class="ti ti-user"></i>
                                            <span>Mi Perfil</span>
                                            <!-- <span class="badge text-bg-warning ms-2">En Proceso</span> -->
                                        </span>
                                    </a>
                                    <!-- <a href="#" class="dropdown-item">
                                        <span>
                                            <svg class="pc-icon text-muted me-2">
                                                <use xlink:href="#custom-share-bold"></use>
                                            </svg>
                                            <span>Share</span>
                                        </span>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <span>
                                            <svg class="pc-icon text-muted me-2">
                                                <use xlink:href="#custom-lock-outline"></use>
                                            </svg>
                                            <span>Change Password</span>
                                        </span>
                                    </a> -->
                                    <hr class="border-secondary border-opacity-50" />

                                    <hr class="border-secondary border-opacity-50" />
                                    <div class="d-grid mb-3">
                                        <a class="btn btn-primary" href="../controladores/usuarios.php?op=salir">
                                            <svg class="pc-icon me-2">
                                                <use xlink:href="#custom-logout-1-outline"></use>
                                            </svg>Cerrar sesión
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- [ Header ] end -->