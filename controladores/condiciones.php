<?php

session_start();

require_once(__DIR__ . '/../modelos/Condicion.php');

$condiciones = new Condicion();

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fecha = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'listar':
        $rspta = $condiciones->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "cbc_id" => $reg->cbc_id,
                "cbc_nombre" => $reg->cbc_nombre,
                "cbc_descripcion" => $reg->cbc_descripcion,
                "numero_componentes" => $reg->numero_componentes,
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
        $cbc_nombre = isset($_POST['cbc_nombre']) ? trim($_POST['cbc_nombre']) : "";
        $cbc_descripcion = isset($_POST['cbc_descripcion']) ? trim($_POST['cbc_descripcion']) : "";

        if (empty($cbc_nombre) || empty($cbc_descripcion)) {
            echo json_encode(['success' => false]);
            break;
        }

        $rspta = $condiciones->registrarCondicion($cbc_nombre, $cbc_descripcion);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'seleccionarCondicion':
        $rspta = $condiciones->seleccionarCondicion();

        echo '<option value="" disabled selected>Seleccione una opción</option>'; // Opción vacía

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->cbc_id . '">' . $reg->cbc_nombre . '</option>';
        }

        break;

    case 'seleccionarCondicionFiltro':
        $rspta = $condiciones->seleccionarCondicion();

        echo '<option value="" selected>Todas las condiciones</option>'; // Opción vacía

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->cbc_id . '">' . $reg->cbc_nombre . '</option>';
        }

        break;

    case 'desactivarCondicion':
        $cbc_id = isset($_POST["cbc_id"]) ? trim($_POST["cbc_id"]) : "";

        if (strlen($cbc_id) > 0) {
            // Verificar si el CBC tiene componentes activos
            if ($condiciones->tieneComponentes($cbc_id) > 0) {
                // Si tiene componentes activos, no se puede desactivar
                echo json_encode(['success' => false]);
            } else {
                // Si no tiene componentes activos, desactivar el CBC
                $rspta = $condiciones->desactivarCondicion($cbc_id);
                echo json_encode(['success' => $rspta]);
            }
        } else {

            echo json_encode(['success' => false]);
        }
        break;

    case 'activarCondicion':
        $cbc_id = isset($_POST["cbc_id"]) ? trim($_POST["cbc_id"]) : "";

        if (strlen($cbc_id) > 0) {
            $rspta = $condiciones->activarCondicion($cbc_id);
            if ($rspta) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'obtenerCondicion':
        $cbc_id = isset($_POST["cbc_id"]) ? trim($_POST["cbc_id"]) : "";

        $rspta = $condiciones->obtenerCondicion($cbc_id);

        echo json_encode($rspta);
        break;

    case 'actualizarCondicion':
        $cbc_id = isset($_POST["cbc_id"]) ? trim($_POST["cbc_id"]) : "";
        $cbc_nombre = isset($_POST["cbc_nombre"]) ? trim($_POST["cbc_nombre"]) : "";
        $cbc_descripcion = isset($_POST["cbc_descripcion"]) ? trim($_POST["cbc_descripcion"]) : "";
        /* 
        $rspta = $condiciones->verificarCondicion($cbc_descripcion);
        $fetch = $rspta->fetch_object();

        if (isset($fetch->cbc_id)) {
            echo json_encode(['success' => false]);
            break;
        }
        */

        $rspta = $condiciones->actualizarCondicion($cbc_id, $cbc_nombre, $cbc_descripcion);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
}
