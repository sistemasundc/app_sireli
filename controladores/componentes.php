<?php

session_start();

require_once(__DIR__ . '/../modelos/Componente.php');

$componentes = new Componente;

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fecha = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'listar':
        $rspta = $componentes->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "componente_id" => $reg->componente_id,
                "componente_nombre" => $reg->componente_nombre,
                "cbc_nombre" => $reg->cbc_nombre,
                "numero_indicadores" => $reg->numero_indicadores,
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
        $cbc_id = isset($_POST['cbc_id']) ? trim($_POST['cbc_id']) : "";
        $componente_nombre = isset($_POST['componente_nombre']) ? trim($_POST['componente_nombre']) : "";

        if (empty($cbc_id || empty($componente_nombre))) {
            echo json_encode(['success' => false]);
            break;
        }
        /* 
            $rspta = $usuarios->verificarCorreo($usu_correo);
            $fetch = $rspta->fetch_object();
    
            if (isset($fetch->usu_id)) {
                echo json_encode(['success' => false]);
                break;
            } */

        $rspta = $componentes->registrarComponente($cbc_id, $componente_nombre);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'seleccionarComponente':
        $rspta = $componentes->seleccionarComponente();

        echo '<option value="" disabled selected>Seleccione una opción</option>'; // Opción vacía

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->componente_id . '">' . $reg->componente_nombre . '</option>';
        }

        break;

    case 'seleccionarComponenteFiltro':
        $rspta = $componentes->seleccionarComponente();

        echo '<option value="" selected>Todos los componentes</option>'; // Opción vacía

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->componente_id . '">' . $reg->componente_nombre . '</option>';
        }

        break;

    case 'desactivarComponente':
        $componente_id = isset($_POST["componente_id"]) ? trim($_POST["componente_id"]) : "";

        if (strlen($componente_id) > 0) {
            if ($componentes->tieneIndicadores($componente_id) > 0) {

                echo json_encode(['success' => false]);
            } else {

                $rspta = $componentes->desactivarComponente($componente_id);
                echo json_encode(['success' => $rspta]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'activarComponente':
        $componente_id = isset($_POST["componente_id"]) ? trim($_POST["componente_id"]) : "";

        if (strlen($componente_id) > 0) {
            $rspta = $componentes->activarComponente($componente_id);
            if ($rspta) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'obtenerComponente':
        $componente_id = isset($_POST["componente_id"]) ? trim($_POST["componente_id"]) : "";

        $rspta = $componentes->obtenerComponente($componente_id);

        echo json_encode($rspta);
        break;


    case 'actualizarComponente':
        $componente_id = isset($_POST["componente_id"]) ? trim($_POST["componente_id"]) : "";
        $componente_nombre = isset($_POST["componente_nombre"]) ? trim($_POST["componente_nombre"]) : "";


        $rspta = $componentes->actualizarComponente($componente_id, $componente_nombre);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
}
