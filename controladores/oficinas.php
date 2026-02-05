<?php

session_start();

require_once(__DIR__ . '/../modelos/Oficina.php');

$oficinas = new Oficina();

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fech_crea = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'listar':
        $rspta = $oficinas->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "oficina_id" => $reg->oficina_id,
                "oficina_nom" => $reg->oficina_nom,
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
    case 'seleccionarOficina':
        $rspta = $oficinas->seleccionarOficina();

        echo '<option value="" disabled selected>Seleccionar Oficina </option>';
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->oficina_id . '>' . $reg->oficina_nom . '</option>';
        }
        break;

    case 'seleccionarOficinas':
        $rspta = $oficinas->seleccionarOficina();

        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->oficina_id . '>' . $reg->oficina_nom . '</option>';
        }
        break;

    case 'seleccionarCoordinadores':
        $rspta = $oficinas->seleccionarOficina();

        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->oficina_id . '>' . $reg->oficina_nom . '</option>';
        }
        break;

    case 'seleccionarOficinaFiltro':
        $rspta = $oficinas->seleccionarOficina();
        echo '<option value="">Todas las Oficinas</option>';
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->oficina_id . '>' . $reg->oficina_nom . '</option>';
        }
        break;
    case 'guardar':
        $oficina_nom = $_POST['oficina_nom'];

        $rspta = $oficinas->verificarOficina($oficina_nom);
        $fetch = $rspta->fetch_object();

        if (isset($fetch->oficina_id)) {
            echo json_encode(['success' => false]);
            break;
        }

        $rspta = $oficinas->registrarOficina($oficina_nom);
        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
    case 'obtenerOficina':
        $oficina_id = isset($_POST["oficina_id"]) ? trim($_POST["oficina_id"]) : "";

        $rspta = $oficinas->obtenerOficina($oficina_id);

        echo json_encode($rspta);
        break;

    case 'actualizarOficina':
        $oficina_id = isset($_POST["oficina_id"]) ? trim($_POST["oficina_id"]) : "";
        $oficina_nom = isset($_POST["oficina_nom"]) ? trim($_POST["oficina_nom"]) : "";
        /* 
        $rspta = $oficinas->verificarOficina($oficina_nom);
        $fetch = $rspta->fetch_object();

        if (isset($fetch->oficina_id)) {
            echo json_encode(['success' => false]);
            break;
        } */

        $rspta = $oficinas->actualizarOficina($oficina_id, $oficina_nom);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'desactivarOficina':
        $oficina_id = isset($_POST["oficina_id"]) ? trim($_POST["oficina_id"]) : "";

        if (strlen($oficina_id) > 0) {
            $rspta = $oficinas->desactivarOficina($oficina_id);
            if ($rspta) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'activarOficina':
        $oficina_id = isset($_POST["oficina_id"]) ? trim($_POST["oficina_id"]) : "";

        if (strlen($oficina_id) > 0) {
            $rspta = $oficinas->activarOficina($oficina_id);
            if ($rspta) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;
}
