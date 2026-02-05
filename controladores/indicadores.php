<?php

session_start();

require_once(__DIR__ . '/../modelos/Indicador.php');

$indicadores = new Indicador;

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fecha = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'listar':
        $rspta = $indicadores->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "indicador_id" => $reg->indicador_id,
                "indicador_nombre" => $reg->indicador_nombre,
                "componente_nombre" => $reg->componente_nombre,
                "numero_medios_verificacion" => $reg->numero_medios_verificacion,
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

    case 'seleccionarIndicador':
        $rspta = $indicadores->seleccionarIndicador();

        echo '<option value="" disabled selected>Seleccione una opción</option>'; // Opción vacía

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->indicador_id . '">' . $reg->indicador_nombre . '</option>';
        }

        break;

    case 'seleccionarIndicadorFiltro':
        $rspta = $indicadores->seleccionarIndicador();

        echo '<option value="" selected>Todos los Indicadores</option>'; // Opción vacía

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->indicador_id . '">' . $reg->indicador_nombre . '</option>';
        }

        break;

    case 'guardar':
        $componente_id = isset($_POST['componente_id']) ? trim($_POST['componente_id']) : "";
        $indicador_nombre = isset($_POST['indicador_nombre']) ? trim($_POST['indicador_nombre']) : "";

        if (empty($componente_id || empty($indicador_nombre))) {
            echo json_encode(['success' => false]);
            break;
        }


        $rspta = $indicadores->registrarIndicador($componente_id, $indicador_nombre);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'desactivarIndicador':
        $indicador_id = isset($_POST["indicador_id"]) ? trim($_POST["indicador_id"]) : "";

        if (strlen($indicador_id) > 0) {
            if ($indicadores->tieneMedios($indicador_id) > 0) {

                echo json_encode(['success' => false]);
            } else {

                $rspta = $indicadores->desactivarIndicador($indicador_id);
                echo json_encode(['success' => $rspta]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'activarIndicador':
        $indicador_id = isset($_POST["indicador_id"]) ? trim($_POST["indicador_id"]) : "";

        if (strlen($indicador_id) > 0) {
            $rspta = $indicadores->activarIndicador($indicador_id);
            if ($rspta) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'obtenerIndicador':
        $indicador_id = isset($_POST["indicador_id"]) ? trim($_POST["indicador_id"]) : "";

        $rspta = $indicadores->obtenerIndicador($indicador_id);

        echo json_encode($rspta);
        break;


    case 'actualizarIndicador':
        $indicador_id = isset($_POST["indicador_id"]) ? trim($_POST["indicador_id"]) : "";
        $indicador_nombre = isset($_POST["indicador_nombre"]) ? trim($_POST["indicador_nombre"]) : "";

        $rspta = $indicadores->actualizarIndicador($indicador_id, $indicador_nombre);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
}
