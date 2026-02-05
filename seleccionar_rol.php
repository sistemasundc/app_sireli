<?php
session_start();

// Validar que existan datos del usuario
if (!isset($_SESSION['user_data'])) {
    header("Location: index.php");
    exit;
}

$nombreUsuario = $_SESSION['user_data']['nombre'];
$imagenUsuario = $_SESSION['user_data']['imagen'];
$roles = $_SESSION['user_data']['roles'];
?>

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
    <link rel="icon" href="./assets/images/sistema/logo-tramite.png" type="image/x-icon"> <!-- [Font] Family -->
    <!--  <link rel="stylesheet" href="./assets/fonts/inter/inter.css" id="main-font-link" /> -->
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="./assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="./assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="./assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="./assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="./assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="./assets/css/style-preset.css">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->
<style>
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        /*  background: url('./assets/images/sistema/fondo.jpg') no-repeat center center fixed; */
        background-size: cover;
    }

    .auth-main {
        width: 100%;
        max-width: 500px;
        /* Limita ancho del formulario */
    }

    .img-login {
        max-height: 70vh;
        /* Limita altura de la imagen */
        max-width: 80%;
        object-fit: contain;
    }

    .row-flex {
        flex-grow: 1;
    }

    footer {
        background-color: #0547a3;
        font-size: 0.85rem;
    }

    @media (max-width: 767px) {
        .col-md-8 {
            display: none;
            /* Oculta imagen en móviles */
        }
    }
</style>

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <div class="container-fluid d-flex flex-column min-vh-100 p-0">
        <div class="row row-flex m-0">
            <!-- Columna del Formulario -->

            <div class="col-md-4 d-flex justify-content-center align-items-center" style="background-color: white;">
                <div class="auth-main p-4">
                    <!-- Formulario de Login -->
                    <div class="card my-5" style="border: none;">
                        <div class=" card-body">
                            <div class="text-center">
                                <!-- <div class="row align-items-center mb-3">
                                    <div class="col-auto">
                                        <img src="./assets/images/sistema/logo-tramite.png" alt="Logo" class="img-fluid" style="height: 70px;">
                                    </div>

                                    <div class="col-auto">
                                        <div class="vr" style="height: 40px; width: 2px;"></div>
                                    </div>

                                    <div class="col">
                                        <h2 class="mb-0 f-w-800" style="color: #001969;">SIRELI</h2>
                                        <span style="color:#001969;">Sistema de Registro para el Licenciamiento Institucional</span>
                                    </div>
                                </div> -->

                                <img src="<?= $imagenUsuario ?>" class="rounded-circle mb-3" width="50" height="50" alt="Foto de perfil">
                                <h4 class="mb-3">¡Hola, Bienvenido!</h4>
                                <!-- <h4 class="mb-3">Hola, <?= htmlspecialchars($nombreUsuario) ?></h4> -->
                                <p class="text-muted">Selecciona con qué rol y oficina deseas ingresar:</p>

                                <?php foreach ($roles as $index => $rol): ?>
                                    <form action="./logeo/iniciarsesion.php" method="POST" class="mb-2">
                                        <input type="hidden" name="rol_seleccionado" value="<?= $index ?>">
                                        <button type="submit" class="btn btn-outline-primary w-100 text-center">
                                            <strong><?= htmlspecialchars($rol->rol_nom) ?></strong> – <?= htmlspecialchars($rol->oficina_nom) ?>
                                        </button>
                                    </form>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna de la Imagen -->
            <div class="col-md-8 d-flex justify-content-center align-items-center"
                style="background-image: linear-gradient(rgba(5, 71, 163, 0.5), rgba(5, 71, 163, 0.2)), url('./assets/images/sistema/fondo.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;">
                <figure class="text-center m-0 w-100">
                    <img src="./assets/images/sistema/condiciones_basicas_calidad.png"
                        alt="Login Image"
                        class="img-fluid img-login"
                        style="max-height: 65vh; max-width: 80%; object-fit: contain;">
                    <figcaption class="text-white small mt-2">
                        Imagen tomada de
                        <a href="https://www.sunedu.gob.pe/8-condiciones-basicas-de-calidad/" target="_blank" class="text-white f-w-300">
                            https://www.sunedu.gob.pe/8-condiciones-basicas-de-calidad/
                        </a>
                    </figcaption>
                </figure>
            </div>

        </div>

        <!-- Footer siempre visible -->
        <footer class="text-white text-center py-3">
            <div class="container">
                <p class="mb-1">© 2025 Universidad Nacional de Cañete - Sistema de Registro para el Licenciamiento Institucional</p>
            </div>
        </footer>
    </div>

    <!-- [ Main Content ] end -->
    <!-- Required Js -->
    <script src="./assets/js/plugins/popper.min.js"></script>
    <script src="./assets/js/plugins/simplebar.min.js"></script>
    <script src="./assets/js/plugins/bootstrap.min.js"></script>
    <script src="./assets/js/fonts/custom-font.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="./assets/js/theme.js"></script>
    <script src="./assets/js/plugins/feather.min.js"></script>

</body>
<!-- [Body] end -->

</html>