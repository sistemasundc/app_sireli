<?php

session_start();

require_once(__DIR__ . '/../modelos/Verificacion.php');

$verificaciones = new Verificacion();

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fecha = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'listar':
        $rspta = $verificaciones->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "medio_id" => $reg->medio_id,
                "medio_nombre" => $reg->medio_nombre,
                "indicador_nombre" => $reg->indicador_nombre,
                "cantidad_evidencias" => $reg->cantidad_evidencias,
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
        $indicador_id = isset($_POST['indicador_id']) ? trim($_POST['indicador_id']) : "";
        $medio_nombre = isset($_POST['medio_nombre']) ? trim($_POST['medio_nombre']) : "";

        if (empty($indicador_id || empty($medio_nombre))) {
            echo json_encode(['success' => false]);
            break;
        }

        $rspta = $verificaciones->registrarVerificacion($indicador_id, $medio_nombre);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    /*     case 'seleccionarVerificacion':
        $rspta = $verificaciones->seleccionarVerificacion();

        echo '<option value="" disabled selected>Seleccione una opción</option>'; // Opción vacía

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->medio_id . '">' . $reg->medio_nombre . '</option>';
        }

        break; */
    case 'seleccionarVerificacion':
        $rspta = $verificaciones->seleccionarVerificacion();

        echo '<option value="" disabled selected>Seleccione una opción (Nombre de indicador - Nombre del Medio de Verificación)</option>';

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->medio_id . '">' . $reg->indicador_nombre. ' - <b>' . $reg->medio_nombre . '</b></option>';
        }
        break;

    case 'desactivarMedio':
        $medio_id = isset($_POST["medio_id"]) ? trim($_POST["medio_id"]) : "";

        if (strlen($medio_id) > 0) {
            if ($verificaciones->tieneEvidencias($medio_id) > 0) {

                echo json_encode(['success' => false]);
            } else {

                $rspta = $verificaciones->desactivarMedio($medio_id);
                echo json_encode(['success' => $rspta]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'activarMedio':
        $medio_id = isset($_POST["medio_id"]) ? trim($_POST["medio_id"]) : "";

        if (strlen($medio_id) > 0) {
            $rspta = $verificaciones->activarMedio($medio_id);
            if ($rspta) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'obtenerMedio':
        $medio_id = isset($_POST["medio_id"]) ? trim($_POST["medio_id"]) : "";

        $rspta = $verificaciones->obtenerMedio($medio_id);

        echo json_encode($rspta);
        break;


    case 'actualizarMedio':
        $medio_id = isset($_POST["medio_id"]) ? trim($_POST["medio_id"]) : "";
        $medio_nombre = isset($_POST["medio_nombre"]) ? trim($_POST["medio_nombre"]) : "";


        $rspta = $verificaciones->actualizarMedio($medio_id, $medio_nombre);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
}
