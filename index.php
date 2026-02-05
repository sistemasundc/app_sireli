<?php

require_once("./modelos/Usuario.php");

include('./google/config.php');



function convertirPrimeraLetraMayuscula($texto)
{
    $textoMinusculas = strtolower($texto);
    $textoConvertido = ucwords($textoMinusculas);
    return $textoConvertido;
}
function obtenerPrimerNombreYApellido($nombres, $apellidos)
{
    $partesNombres = explode(' ', $nombres);
    $partesApellidos = explode(' ', $apellidos);
    $primerNombre = ucfirst(strtolower($partesNombres[0]));
    $primerApellido = '';
    foreach ($partesApellidos as $parte) {
        if (in_array(strtolower($parte), ['de', 'del', 'la', 'los'])) {
            $primerApellido .= ' ' . strtolower($parte);
        } else {
            $primerApellido .= ' ' . ucfirst(strtolower($parte));
            break;
        }
    }
    $primerApellido = trim($primerApellido);
    return $primerNombre . ' ' . ucwords($primerApellido);
}

// Check if there is an error message in the session
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the error message from the session
}

if (isset($_GET["code"])) {
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

    if (!isset($token['error'])) {
        $google_client->setAccessToken($token['access_token']);
        $_SESSION['access_token'] = $token['access_token'];

        $google_service = new Google_Service_Oauth2($google_client);
        $data = $google_service->userinfo->get();

        $usuarios = new Usuario();
        $rspta = $usuarios->verificarLogeo($data['email']);

        $opcionesRol = [];
        while ($row = $rspta->fetch_object()) {
            $opcionesRol[] = $row;
        }

        if (count($opcionesRol) > 0) {
            $_SESSION['user_data'] = [
                'correo' => $data['email'],
                'nombre' => $data['name'],
                'imagen' => $data['picture'],
                'roles' => $opcionesRol
            ];

            if (count($opcionesRol) > 1) {
                // Si hay más de una opción, redirigir para que el usuario seleccione rol
                header('Location: ./seleccionar_rol.php');
                exit;
            } else {
                // Solo una opción → iniciar sesión directamente
                $fetch = $opcionesRol[0];

                $nomConvertido = obtenerPrimerNombreYApellido($fetch->usu_nom, $fetch->usu_ape);
                $rolConvertido = convertirPrimeraLetraMayuscula($fetch->rol_nom);

                $_SESSION['usu_id'] = $fetch->usu_id;
                /* $_SESSION['nomcompleto'] = $nomConvertido; */
                $_SESSION['nomcompleto'] = $fetch->usu_nom . ' ' . $fetch->usu_ape;
                $_SESSION['usu_correo'] = $fetch->usu_correo;
                $_SESSION['rol_id'] = $fetch->rol_id;
                $_SESSION['oficina_id'] = $fetch->oficina_id;
                $_SESSION['oficina_nom'] = $fetch->oficina_nom;
                $_SESSION['rol_nom'] = $rolConvertido;
                $_SESSION['user_image'] = $data['picture'];

                header('Location: ./vistas/dashboard.php');
                exit;
            }
        } else {
            // Si el correo no está registrado
            unset($_SESSION['access_token']);
            $google_client->revokeToken();
            $_SESSION['error_message'] = "Sin acceso";
            header('Location: ./index.php');
            exit;
        }
    }
}


// Mostrar el botón de inicio de sesión siempre
$login_button = '


  <a class="btn mt-2 btn-danger" href="' . $google_client->createAuthUrl() . '">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="menu-icon bi bi-google mb-1" viewBox="0 0 16 16">
      <path d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z"></path>
    </svg>
    Acceder con Google
  </a>
';
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
    <link rel="stylesheet" href="./assets/fonts/inter/inter.css" id="main-font-link" />
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="./assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="/assets/fonts/feather.css">
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
        max-width: 400px;
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
</head>

<body>

    <div class="container-fluid d-flex flex-column min-vh-100 p-0">
        <div class="row row-flex m-0">
            <!-- Columna del Formulario -->
            <div class="col-md-4 d-flex justify-content-center align-items-center" style="background-color: white;">
                <div class="auth-main p-4">
                    <!-- Formulario de Login -->
                    <div class="card my-5" style="border: none;">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="col-auto">
                                    <img src="./assets/images/sistema/logo-tramite.png" alt="Logo" class="img-fluid" style="height: 70px;">
                                </div>
                                <div class="row align-items-center mb-3">
                                    <div class="col">
                                        <h3 class="mb-0 f-w-600" style="color: #001969;">SIRELI</h3>
                                        <span style="color:#001969;">Sistema de Registro para el Licenciamiento Institucional</span>
                                    </div>
                                </div>

                                <span class="text-center text-muted f-w-500 mb-3 d-block mt-3">Inicie sesión con su correo institucional</span>
                                <?php echo '<div class="d-grid my-3">' . $login_button . '</div>'; ?>

                                <p class="mt-4 text-muted text-center" style="font-size: 13px;">
                                    Al hacer clic en <strong>"Acceder con Google"</strong>, aceptas nuestros
                                    <a href="https://web.undc.edu.pe/terminos-y-condiciones/" target="_blank" class="text-decoration-underline" style="color: #0547a3;">Términos y Condiciones</a>
                                    y nuestra
                                    <a href="https://web.undc.edu.pe/pdatospersonales/" target="_blank" class="text-decoration-underline" style="color: #0547a3;">Política de Privacidad</a>.
                                </p>
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

    <!-- Sweetalert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <?php
    if ($error_message == "Sin acceso") {
        echo
        '<script>
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
              toast.onmouseenter = Swal.stopTimer;
              toast.onmouseleave = Swal.resumeTimer;
            }
          });
          Toast.fire({
            icon: "error",
            title: "No tienes acceso al sistema."
          });
        </script>';
    }
    ?>

</body>
<!-- [Body] end -->

</html>