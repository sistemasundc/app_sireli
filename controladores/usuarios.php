<?php

session_start();

require_once(__DIR__ . '/../modelos/Usuario.php');

$usuarios = new Usuario();


$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fech_crea = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {
    case 'mostrarUsuario':
        $rspta = $usuarios->mostrarUsuario($usu_id);
        echo json_encode($rspta);
        break;

    case 'salir':
        include('../google/config.php');

        // Revocar token de Google
        $google_client->revokeToken();

        // Limpiar sesión completamente
        session_start();
        $_SESSION = [];
        session_unset();
        session_destroy();

        // Eliminar cookies de sesión si existen
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Evitar que el navegador guarde caché
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Redireccionar al login
        header("Location: ../index.php");
        exit();

    case 'listar':
        $rspta = $usuarios->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "usu_id" => $reg->usu_id,
                "nomcompleto" => $reg->nomcompleto,
                "usu_telf" => $reg->usu_telf,
                "usu_correo" => $reg->usu_correo,
                "rol_nom" => $reg->rol_nom,
                "oficina_nom" => $reg->oficina_nom,
                "fech_crea" => $reg->fech_crea,
                "estado" => ($reg->estado)
                    ? '<span class="badge rounded-pill bg-success p-2">Activo</span>'
                    : '<span class="badge rounded-pill bg-danger p-2">Inactivo</span>',
                "acciones" => '<button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button> ' .
                    '<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>'
            );
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break;
    case 'guardar':
        $usu_nom = isset($_POST['usu_nom']) ? trim($_POST['usu_nom']) : "";
        $usu_ape = isset($_POST['usu_ape']) ? trim($_POST['usu_ape']) : "";
        $usu_telf = isset($_POST['usu_telf']) ? trim($_POST['usu_telf']) : "";
        $usu_correo = isset($_POST['usu_correo']) ? trim($_POST['usu_correo']) : "";
        $rol_id = isset($_POST['rol_id']) ? trim($_POST['rol_id']) : "";
        $oficina_id = isset($_POST['oficina_id']) ? trim($_POST['oficina_id']) : "";

        if (empty($usu_nom) || empty($usu_ape) || empty($usu_correo) || empty($rol_id) || empty($oficina_id)) {
            echo json_encode(['success' => false]);
            break;
        }

        /* 
        $rspta = $usuarios->verificarCorreo($usu_correo);
        
        $fetch = $rspta->fetch_object();

        if (isset($fetch->usu_id)) {
            echo json_encode(['success' => false]);
            break;
        }*/

        $rspta = $usuarios->registrarUsuario($usu_nom, $usu_ape, $usu_telf, $usu_correo, $rol_id, $oficina_id, $fech_crea);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'desactivarUsuario':
        $usu_id = isset($_POST["usu_id"]) ? trim($_POST["usu_id"]) : "";

        if (strlen($usu_id) > 0) {
            $rspta = $usuarios->desactivarUsuario($usu_id);
            if ($rspta) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;
    case 'activarUsuario':
        $usu_id = isset($_POST["usu_id"]) ? trim($_POST["usu_id"]) : "";

        if (strlen($usu_id) > 0) {
            $rspta = $usuarios->activarUsuario($usu_id);
            if ($rspta) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'obtenerUsuario':
        $usu_id = isset($_POST["usu_id"]) ? trim($_POST["usu_id"]) : "";

        $rspta = $usuarios->obtenerUsuario($usu_id);

        echo json_encode($rspta);
        break;

    case 'actualizarUsuario':
        $usu_id = isset($_POST["usu_id"]) ? trim($_POST["usu_id"]) : "";
        $usu_nom = isset($_POST["usu_nom"]) ? trim($_POST["usu_nom"]) : "";
        $usu_ape = isset($_POST["usu_ape"]) ? trim($_POST["usu_ape"]) : "";
        $usu_correo = isset($_POST["usu_correo"]) ? trim($_POST["usu_correo"]) : "";
        $usu_telf = isset($_POST["usu_telf"]) ? trim($_POST["usu_telf"]) : "";
        $rol_id = isset($_POST["rol_id"]) ? trim($_POST["rol_id"]) : "";
        $oficina_id = isset($_POST["oficina_id"]) ? trim($_POST["oficina_id"]) : "";

        $rspta = $usuarios->actualizarUsuario($usu_id, $usu_nom, $usu_ape, $usu_correo, $usu_telf, $rol_id, $oficina_id);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
        
    case 'mostrarDatosUsuario':
        $usu_id = isset($_POST["usu_id"]) ? trim($_POST["usu_id"]) : "";
        $rspta = $usuarios->mostrarDatosUsuario($usu_id);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "usu_id" => $reg->usu_id,
                "usu_nom" => $reg->usu_nom,
                "usu_ape" => $reg->usu_ape,
                "usu_telf" => $reg->usu_telf,
                "usu_correo" => $reg->usu_correo,
                "rol_nom" => $reg->rol_nom,
                "oficina_nom" => $reg->oficina_nom,
                "estado" => $reg->estado,
                "fech_crea" => $reg->fech_crea

            );
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );


        echo json_encode($results);
        break;

    case 'actualizarMiUsuario':
        $usu_id = isset($_POST["usu_id"]) ? trim($_POST["usu_id"]) : "";
        $usu_nom = isset($_POST["usu_nom"]) ? trim($_POST["usu_nom"]) : "";
        $usu_ape = isset($_POST["usu_ape"]) ? trim($_POST["usu_ape"]) : "";
        $usu_telf = isset($_POST["usu_telf"]) ? trim($_POST["usu_telf"]) : "";

        $rspta = $usuarios->actualizarMiUsuario($usu_id, $usu_nom, $usu_ape, $usu_telf);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    default:

        break;
}
