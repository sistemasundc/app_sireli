<?php

session_start();

require_once(__DIR__ . '/../modelos/Evidencia.php');

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$evidencias = new Evidencia;

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fecha = $Date->format("Y-m-d H:i:s");
$fechaArchivo = $Date->format("YmdHis");


switch ($_GET["op"]) {

    case 'listar':
        $oficina_id = isset($_POST['oficina_id']) ? $_POST['oficina_id'] : '';
        $busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
        $rspta = $evidencias->listar($oficina_id, $busqueda);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_id" => $reg->evidencia_id,
                "indicador_nombre" => $reg->indicador_nombre,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                "estado" => $reg->estado,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "coordinadores_vinculados" => $reg->coordinadores_vinculados,

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

    /*  case 'guardar':

        $medio_id = isset($_POST['medio_id']) ? trim($_POST['medio_id']) : "";
        $evidencia_nombre = isset($_POST['evidencia_nombre']) ? trim($_POST['evidencia_nombre']) : "";
        $evidencia_consideraciones = isset($_POST['evidencia_consideraciones']) ? trim($_POST['evidencia_consideraciones']) : "";
        $fecha_plazo_inicio = isset($_POST['fecha_plazo_inicio']) ? trim($_POST['fecha_plazo_inicio']) : "";
        $fecha_plazo_fin = isset($_POST['fecha_plazo_fin']) ? trim($_POST['fecha_plazo_fin']) : "";
        $oficinas = isset($_POST['oficina_id_evidencia']) ? $_POST['oficina_id_evidencia'] : [];

        if (empty($medio_id) || empty($evidencia_nombre) || empty($evidencia_consideraciones) || empty($fecha_plazo_inicio) || empty($fecha_plazo_fin)) {
            echo json_encode(['success' => false]);
            break;
        }

        if (empty($oficinas)) {
            echo json_encode(['success' => false]);
            break;
        }

        $rspta = $evidencias->registrarEvidencia($medio_id, $evidencia_nombre, $evidencia_consideraciones, $fecha_plazo_inicio, $fecha_plazo_fin, $oficinas);

        if ($rspta['success']) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break; */
    case 'guardar':

        $medio_id = isset($_POST['medio_id']) ? trim($_POST['medio_id']) : "";
        $evidencia_nombre = isset($_POST['evidencia_nombre']) ? trim($_POST['evidencia_nombre']) : "";
        $evidencia_consideraciones = isset($_POST['evidencia_consideraciones']) ? trim($_POST['evidencia_consideraciones']) : "";
        $fecha_plazo_inicio = isset($_POST['fecha_plazo_inicio']) ? trim($_POST['fecha_plazo_inicio']) : "";
        $fecha_plazo_fin = isset($_POST['fecha_plazo_fin']) ? trim($_POST['fecha_plazo_fin']) : "";
        $oficinas = isset($_POST['oficina_id_evidencia']) ? $_POST['oficina_id_evidencia'] : [];
        $coordinadores = isset($_POST['coordinador_id_evidencia']) ? $_POST['coordinador_id_evidencia'] : [];
        $fecha_registro = $fecha;

        if (empty($medio_id) || empty($evidencia_nombre) || empty($evidencia_consideraciones) || empty($fecha_plazo_inicio) || empty($fecha_plazo_fin)) {
            echo json_encode(['success' => false]);
            break;
        }

        if (empty($oficinas)) {
            echo json_encode(['success' => false]);
            break;
        }

        $rspta = $evidencias->registrarEvidencia($medio_id, $evidencia_nombre, $evidencia_consideraciones, $fecha_plazo_inicio, $fecha_plazo_fin, $oficinas, $coordinadores, $fecha_registro);

        if ($rspta['success']) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    /*     case 'obtenerEvidencia':
        $evidencia_id = isset($_POST["evidencia_id"]) ? trim($_POST["evidencia_id"]) : "";

        $rspta = $evidencias->obtenerEvidencia($evidencia_id);

        echo json_encode($rspta);
        break; */

    case 'obtenerEvidencia':
        $evidencia_id = isset($_POST["evidencia_id"]) ? trim($_POST["evidencia_id"]) : "";

        $rspta = $evidencias->obtenerEvidencia($evidencia_id); // <-- obtiene solo los datos de evidencia

        if ($rspta) {
            // Aquí agregamos las oficinas activas a la respuesta
            $rspta["oficinas"] = $evidencias->obtenerOficinasPorEvidencia($evidencia_id);
            $rspta["coordinadores"] = $evidencias->obtenerCoordinadoresPorEvidencia($evidencia_id);
        }

        echo json_encode($rspta);
        break;

    /* case 'actualizarEvidencia':

        $evidencia_id = isset($_POST["evidencia_id"]) ? trim($_POST["evidencia_id"]) : "";
        $evidencia_nombre = isset($_POST["evidencia_nombre"]) ? trim($_POST["evidencia_nombre"]) : "";
        $evidencia_consideraciones = isset($_POST["evidencia_consideraciones"]) ? trim($_POST["evidencia_consideraciones"]) : "";
        $fecha_plazo_inicio = isset($_POST["fecha_plazo_inicio"]) ? trim($_POST["fecha_plazo_inicio"]) : "";
        $fecha_plazo_fin = isset($_POST["fecha_plazo_fin"]) ? trim($_POST["fecha_plazo_fin"]) : "";


        $rspta = $evidencias->actualizarEvidencia($evidencia_id, $evidencia_nombre, $evidencia_consideraciones, $fecha_plazo_inicio, $fecha_plazo_fin);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break; */

    /* case 'actualizarEvidenciaold':
        $evidencia_id = isset($_POST["evidencia_id"]) ? trim($_POST["evidencia_id"]) : "";
        $evidencia_nombre = isset($_POST["evidencia_nombre"]) ? trim($_POST["evidencia_nombre"]) : "";
        $evidencia_consideraciones = isset($_POST["evidencia_consideraciones"]) ? trim($_POST["evidencia_consideraciones"]) : "";
        $fecha_plazo_inicio = isset($_POST["fecha_plazo_inicio"]) ? trim($_POST["fecha_plazo_inicio"]) : "";
        $fecha_plazo_fin = isset($_POST["fecha_plazo_fin"]) ? trim($_POST["fecha_plazo_fin"]) : "";


        $fecha_registro = $fecha;


        $oficinas = isset($_POST["oficina_id_evidencia_actual"])
            ? (is_array($_POST["oficina_id_evidencia_actual"])
                ? $_POST["oficina_id_evidencia_actual"]
                : explode(",", $_POST["oficina_id_evidencia_actual"]))
            : [];


        if ($evidencia_id == "" || $evidencia_nombre == "" || count($oficinas) == 0) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']);
            break;
        }


        $rspta = $evidencias->actualizarEvidencia(
            $evidencia_id,
            $evidencia_nombre,
            $evidencia_consideraciones,
            $fecha_plazo_inicio,
            $fecha_plazo_fin,
            $oficinas,
            $fecha_registro
        );

        echo json_encode($rspta);
        break; */

    case 'actualizarEvidencia':
        $evidencia_id = isset($_POST["evidencia_id"]) ? trim($_POST["evidencia_id"]) : "";
        $evidencia_nombre = isset($_POST["evidencia_nombre"]) ? trim($_POST["evidencia_nombre"]) : "";
        $evidencia_consideraciones = isset($_POST["evidencia_consideraciones"]) ? trim($_POST["evidencia_consideraciones"]) : "";
        $fecha_plazo_inicio = isset($_POST["fecha_plazo_inicio"]) ? trim($_POST["fecha_plazo_inicio"]) : "";
        $fecha_plazo_fin = isset($_POST["fecha_plazo_fin"]) ? trim($_POST["fecha_plazo_fin"]) : "";


        $fecha_registro = $fecha;


        $oficinas = isset($_POST["oficina_id_evidencia_actual"])
            ? (is_array($_POST["oficina_id_evidencia_actual"])
                ? $_POST["oficina_id_evidencia_actual"]
                : explode(",", $_POST["oficina_id_evidencia_actual"]))
            : [];

        $coordinadores = isset($_POST["coordinador_id_evidencia_actual"])
            ? (is_array($_POST["coordinador_id_evidencia_actual"])
                ? $_POST["coordinador_id_evidencia_actual"]
                : explode(",", $_POST["coordinador_id_evidencia_actual"]))
            : [];


        if ($evidencia_id == "" || $evidencia_nombre == "" || count($oficinas) == 0) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']);
            break;
        }


        $rspta = $evidencias->actualizarEvidencia(
            $evidencia_id,
            $evidencia_nombre,
            $evidencia_consideraciones,
            $fecha_plazo_inicio,
            $fecha_plazo_fin,
            $oficinas,
            $coordinadores,
            $fecha_registro
        );

        echo json_encode($rspta);
        break;


    case 'actualizarPlazosGrupo':
        $ids          = !empty($_POST["evidencias"]) ? explode(",", $_POST["evidencias"]) : [];
        $fecha_inicio = !empty($_POST["fecha_inicio"]) ? trim($_POST["fecha_inicio"]) : "";
        $fecha_fin    = !empty($_POST["fecha_fin"])    ? trim($_POST["fecha_fin"])    : "";

        if (count($ids) === 0) {
            echo json_encode(['success' => false, 'msg' => 'No hay evidencias seleccionadas']);
            break;
        }

        $todo_ok = true;
        foreach ($ids as $id) {
            $rspta = $evidencias->actualizarEvidenciaPorGrupo(
                $id,
                $fecha_inicio !== "" ? $fecha_inicio : null,
                $fecha_fin    !== "" ? $fecha_fin    : null
            );

            if (!$rspta) {
                $todo_ok = false;
            }
        }

        // --- Mensajes claros ---
        if ($todo_ok) {
            echo json_encode(['success' => true]);
        } else {
            if (count($ids) === 1) {
                echo json_encode(['success' => false, 'msg' => 'No se pudo actualizar la evidencia']);
            } else {
                echo json_encode(['success' => false, 'msg' => 'Algunas evidencias no se pudieron actualizar']);
            }
        }
        break;

    case 'listarTodosIds':
        $oficina_id = !empty($_POST["oficina_id"]) ? trim($_POST["oficina_id"]) : null;
        $busqueda   = !empty($_POST["busqueda"])   ? trim($_POST["busqueda"])   : null;

        $ids = $evidencias->listarTodosIds($oficina_id, $busqueda);

        echo json_encode([
            'success' => true,
            'ids'     => $ids
        ]);
        break;



    case 'desactivarEvidencia':

        $evidencia_id = isset($_POST["evidencia_id"]) ? trim($_POST["evidencia_id"]) : "";

        if (strlen($evidencia_id) > 0) {

            $rspta = $evidencias->desactivarEvidencia($evidencia_id);

            if ($rspta) {

                echo json_encode(['success' => true]);
            } else {

                echo json_encode(['success' => false]);
            }
        } else {

            echo json_encode(['success' => false]);
        }
        break;
    case 'listarTodasEvidencias':

        $oficina_id = $_POST['oficina_id'];
        $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

        $rspta = $evidencias->listarTodasEvidencias($oficina_id, $estado);

        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_id" => $reg->evidencia_id,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                "estado" => $reg->estado,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                "fecha_registro" => $reg->fecha_registro,
                "fecha_subsanacion" => $reg->fecha_subsanacion,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "nomcompleto" => $reg->nomcompleto,
                "estado_revision" => $reg->estado_revision,
                "coordinadores_vinculados"=> $reg->coordinadores_vinculados
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

    case 'listarPorOficina':

        $oficina_id = $_POST['oficina_id'];
        $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

        $rspta = $evidencias->listarPorOficina($oficina_id, $estado);

        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_id" => $reg->evidencia_id,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                "estado" => $reg->estado,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                "fecha_registro" => $reg->fecha_registro,
                "fecha_subsanacion" => $reg->fecha_subsanacion,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "coordinadores_vinculados" => $reg->coordinadores_vinculados,
                "nomcompleto" => $reg->nomcompleto,
                "estado_revision" => $reg->estado_revision,
                "rol_oficina" => $reg->rol_oficina

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

    /* case 'listarPorOficina':

        $oficina_id = $_POST['oficina_id'];
        $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

        $rspta = $evidencias->listarPorOficina($oficina_id, $estado);

        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_id" => $reg->evidencia_id,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                "estado" => $reg->estado,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                "fecha_registro" => $reg->fecha_registro,
                "fecha_subsanacion" => $reg->fecha_subsanacion,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "nomcompleto" => $reg->nomcompleto,
                "estado_revision" => $reg->estado_revision
            );
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break; */

    case 'listarPorOficinaPendientes':

        $oficina_id = $_SESSION['oficina_id'];
        $rspta = $evidencias->listarPorOficinaPendientes($oficina_id);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_id" => $reg->evidencia_id,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                "estado" => $reg->estado,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                "fecha_registro" => $reg->fecha_registro,
                "fecha_subsanacion" => $reg->fecha_subsanacion,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "nomcompleto" => $reg->nomcompleto,
                "estado_revision" => $reg->estado_revision,
                "rol_oficina" => $reg->rol_oficina,
                "coordinadores_vinculados" => $reg->coordinadores_vinculados
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
    /* case 'listarPorOficinaPendientes':

        $oficina_id = $_SESSION['oficina_id'];
        $rspta = $evidencias->listarPorOficinaPendientes($oficina_id);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_id" => $reg->evidencia_id,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                "estado" => $reg->estado,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                "fecha_registro" => $reg->fecha_registro,
                "fecha_subsanacion" => $reg->fecha_subsanacion,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "nomcompleto" => $reg->nomcompleto,
                "estado_revision" => $reg->estado_revision
            );
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break; */

    case 'listarNotificacionesPorOficina':

        $oficina_id = $_SESSION['oficina_id'];
        $rspta = $evidencias->listarNotificacionesPorOficina($oficina_id);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "noti_id" => $reg->noti_id,
                "mensaje" => $reg->mensaje,
                "fecha_envio" => $reg->fecha_envio,
                "oficina_id" => $reg->oficina_id,
                "estado_leido" => $reg->estado_leido,
                "usu_id" => $reg->usu_id
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

    case 'marcarNotificacionLeida':

        $noti_id = $_POST['noti_id'];
        $usu_id = $_SESSION['usu_id'];
        $fecha_leida = $fecha;

        $rspta = $evidencias->marcarNotificacionComoLeida($noti_id, $usu_id, $fecha_leida);

        if ($rspta) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'listarEvidenciaIdPorOficina':
        $evidencia_id = isset($_POST['evidencia_id']) ? $_POST['evidencia_id'] : '';
        $oficina_id = isset($_SESSION['oficina_id']) ? $_SESSION['oficina_id'] : '';

        if ($evidencia_id && $oficina_id) {
            $rspta = $evidencias->listarEvidenciaIdPorOficina($evidencia_id, $oficina_id);
            $data = array();

            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "evidencia_id" => $reg->evidencia_id,
                    "medio_nombre" => $reg->medio_nombre,
                    "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                    "estado" => $reg->estado,
                    "evidencia_nombre" => $reg->evidencia_nombre,
                    "indicador_nombre" => $reg->indicador_nombre,
                    "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                    "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                    "fecha_subsanacion" => $reg->fecha_subsanacion,
                    "fecha_registro" => $reg->fecha_registro,
                    "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                    "coordinadores_vinculados" => $reg->coordinadores_vinculados,
                    "nomcompleto" => $reg->nomcompleto,
                    "estado_revision" => $reg->estado_revision,
                    "grado_nombre" => $reg->grado_nombre,
                    "rol_oficina"=>$reg->rol_oficina
                );
            }

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );

            echo json_encode($results);
        } else {
            // Puedes devolver un error en formato JSON también si quieres
            echo json_encode(["aaData" => []]);
        }
        break;


    case 'mostrarHistorialEvidenciaId':
        $evidencia_id = isset($_POST['evidencia_id']) ? $_POST['evidencia_id'] : '';


        if ($evidencia_id) {
            $rspta = $evidencias->mostrarHistorialEvidencia($evidencia_id);
            $data = array();

            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "archivo_presentacion" => $reg->archivo_presentacion,
                    "archivo_observacion" => $reg->archivo_observacion,
                    "historial_id" => $reg->historial_id,
                    "emisor_oficina" => $reg->emisor_oficina,
                    "fecha_emision" => $reg->fecha_emision,
                    "fecha_recepcion" => $reg->fecha_recepcion,
                    "estado_revision" => $reg->estado_revision,
                    "receptor_oficina" => $reg->receptor_oficina,
                    "estado2" => $reg->estado2,
                    "observaciones" => $reg->observaciones,
                    "grado_id" => $reg->grado_id

                );
            }

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );

            echo json_encode($results);
        } else {
            // Puedes devolver un error en formato JSON también si quieres
            echo json_encode(["aaData" => []]);
        }
        break;


    case 'subirEvidenciaPresentacion':

        $evidencia_id = $_POST['evidencia_id'];
        $usu_emisor_id = $_SESSION["usu_id"];
        $oficina_origen_id = $_SESSION["oficina_id"];
        $oficina_destino_id = 1; // Oficina de Calidad
        $archivo_nombre = null;
        $fecha_emision = $fecha;

        if (isset($_FILES['archivo_presentacion']) && $_FILES['archivo_presentacion']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['archivo_presentacion']['tmp_name'];
            $archivo_original = basename($_FILES['archivo_presentacion']['name']);
            $fecha_actual = $fechaArchivo;
            $archivo_nombre = $fecha_actual . "_" . $archivo_original;
            $ruta_destino = "../archivos/evidencias/" . $archivo_nombre;

            if (!is_writable(dirname($ruta_destino))) {
                echo json_encode(['success' => false, 'msg' => 'No se tiene permiso para guardar el archivo.']);
                break;
            }

            if (!move_uploaded_file($tmp_name, $ruta_destino)) {
                echo json_encode(['success' => false, 'msg' => 'Error al guardar el archivo.']);
                break;
            }
        } else {
            echo json_encode(['success' => false, 'msg' => 'Archivo no recibido o error al subir el archivo.']);
            break;
        }

        // Llamada a función unificada
        $resultado = $evidencias->registrarHistorialYEvidencia(
            $evidencia_id,
            $usu_emisor_id,
            $oficina_origen_id,
            $oficina_destino_id,
            $archivo_nombre,
            $fecha_emision
        );

        if ($resultado['success']) {
            $correo = $resultado['usuario']['correo'];
            $evidencia_nombre = $resultado['evidencia']['nombre'];
            $oficina_nombre = $resultado['usuario']['oficina_nombre'];

            // Enviar correo
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'licenciamiento@undc.edu.pe';
                $mail->Password = 'assz ouit kdkh gwbc'; // Usa contraseña de aplicación
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('licenciamiento@undc.edu.pe', 'Sistema de Registro para el Licenciamiento Institucional');
                $mail->addAddress($correo);
                $mail->addAddress('licenciamiento@undc.edu.pe');
                $mail->isHTML(true);

                $numero_aleatorio = rand(1, 99);
                $numero_formateado = str_pad($numero_aleatorio, 2, '0', STR_PAD_LEFT);
                $mail->Subject = 'SIRELI: Evidencia registrada - #' . $numero_formateado;

                $mail->Body = '
<html>
<head>
    <meta charset="UTF-8">
    <title>Evidencia registrada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        .header {
            background-color: #004aad;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .header img {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .body {
            padding: 20px;
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }
        .envio {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #eee;
        }
        .envio p {
            margin: 10px 0;
        }
        .footer {
            background-color: #f4f4f4;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #555;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://sireli.undc.edu.pe/assets/images/logoundc.png" alt="Logo UNDC" style="max-height: 80px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto;margin-bottom:0px">
            <p style="margin: 0; font-size: 16px;">Sistema de Registro para el Licenciamiento Institucional</p>
        </div>
        <div class="body">
            <div class="envio">
                <p>👋 Estimado/a,</p>
                <p>Se ha registrado la presentación de una evidencia en el sistema SIRELI.</p>
                <p><strong>Evidencia:</strong> ' . htmlspecialchars($evidencia_nombre) . '</p>
                <p><strong>Responsable:</strong> ' . $oficina_nombre . '</p>
                <p><strong>Fecha de emisión:</strong> ' . $fecha_emision . '</p>
                <p>Puede realizar el seguimiento de esta evidencia ingresando al sistema:</p>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="https://sireli.undc.edu.pe/" target="_blank" style="background-color: #004aad; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                        Ir al sistema SIRELI
                    </a>
                </div>
            </div>

        </div>
        <div class="footer">
            <p style="margin: 2px 0;"><strong>Oficina de Gestión de Calidad</strong></p>
            <p style="margin: 2px 0;">UNIVERSIDAD NACIONAL DE CAÑETE</p>
            <p style="margin: 2px 0;"><strong>Contacto:</strong> 949 026 909</p>
            <p style="margin: 2px 0; font-size: 12px; color: #888;">Este mensaje ha sido generado automáticamente y no requiere respuesta.</p>
        </div>
    </div>
</body>
</html>';

                $mail->send();
                echo json_encode([
                    'success' => true,
                    'msg' => 'Evidencia registrada con éxito.'
                ]);
            } catch (Exception $e) {

                echo json_encode([
                    'success' => true,  // se mantiene en true
                    'msg' => 'Evidencia registrada con éxito.'
                    // Opcional: 'email_error' => $mail->ErrorInfo
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'msg' => $resultado['msg']
            ]);
        }

        break;


    /* Rol Evaluador */
    case 'listarEvidenciasPorRecibir':

        $oficina_id = isset($_POST['oficina_id']) ? $_POST['oficina_id'] : null;

        $rspta = $evidencias->listarEvidenciasPorRecibir($oficina_id);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_id" => $reg->evidencia_id,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                "estado" => $reg->estado,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                "fecha_registro" => $reg->fecha_registro,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "nomcompleto" => $reg->nomcompleto,
                "estado_revision" => $reg->estado_revision,
                "emisor_nombre" => $reg->emisor_nombre,
                "oficina_origen" => $reg->oficina_origen,
                "fecha_emision" => $reg->fecha_emision,
                "historial_id" => $reg->historial_id,
                "coordinadores_vinculados"=> $reg->coordinadores_vinculados
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
    /* case 'listarEvidenciasPorRecibir':

        $rspta = $evidencias->listarEvidenciasPorRecibir();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_id" => $reg->evidencia_id,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                "estado" => $reg->estado,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                "fecha_registro" => $reg->fecha_registro,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "nomcompleto" => $reg->nomcompleto,
                "estado_revision" => $reg->estado_revision,
                "emisor_nombre" => $reg->emisor_nombre,
                "oficina_origen" => $reg->oficina_origen,
                "fecha_emision" => $reg->fecha_emision,
                "historial_id" => $reg->historial_id
            );
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break; */

    case 'recibirEvidencia':
        $historial_id = isset($_POST["historial_id"]) ? limpiarCadena($_POST["historial_id"]) : "";
        $fecha_recepcion = $fecha;

        $rspta = $evidencias->recibirEvidencia($historial_id, $fecha_recepcion);

        if ($rspta) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false]);
        }
        break;

    case 'listarEvidenciasPorRevisar':

        $rspta = $evidencias->listarEvidenciasPorRevisar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_id" => $reg->evidencia_id,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                "estado" => $reg->estado,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                "fecha_registro" => $reg->fecha_registro,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "nomcompleto" => $reg->nomcompleto,
                "estado_revision" => $reg->estado_revision,
                "emisor_nombre" => $reg->emisor_nombre,
                "oficina_origen" => $reg->oficina_origen,
                "fecha_emision" => $reg->fecha_emision,
                "historial_id" => $reg->historial_id,
                "grado_id" => $reg->grado_id,
                "coordinadores_vinculados"=> $reg->coordinadores_vinculados
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


    case 'listarEvidenciaId':
        $evidencia_id = isset($_POST['evidencia_id']) ? $_POST['evidencia_id'] : '';

        if ($evidencia_id) {
            $rspta = $evidencias->listarEvidenciaId($evidencia_id);
            $data = array();

            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "evidencia_id" => $reg->evidencia_id,
                    "medio_nombre" => $reg->medio_nombre,
                    "evidencia_consideraciones" => $reg->evidencia_consideraciones,
                    "estado" => $reg->estado,
                    "evidencia_nombre" => $reg->evidencia_nombre,
                    "indicador_nombre" => $reg->indicador_nombre,
                    "fecha_plazo_inicio" => $reg->fecha_plazo_inicio,
                    "fecha_plazo_fin" => $reg->fecha_plazo_fin,
                    "fecha_registro" => $reg->fecha_registro,
                    "fecha_subsanacion" => $reg->fecha_subsanacion,
                    "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                    "nomcompleto" => $reg->nomcompleto,
                    "estado_revision" => $reg->estado_revision,
                    "grado_id" => $reg->grado_id,
                    "grado_nombre" => $reg->grado_nombre,
                    "coordinadores_vinculados" => $reg->coordinadores_vinculados
                );
            }

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );

            echo json_encode($results);
        } else {
            // Puedes devolver un error en formato JSON también si quieres
            echo json_encode(["aaData" => []]);
        }
        break;
    case 'seleccionarGrado':
        $rspta = $evidencias->seleccionarGradoCumplimiento();

        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->grado_id . '>' . $reg->grado_nombre . '</option>';
        }
        break;

    /*  case 'registrarCalificacion':
        $historial_id = $_POST["historial_id"];
        $grado_id = $_POST["grado_id"];
        $observaciones = $_POST["observaciones"];
        $fecha_reprogramacion = isset($_POST['fecha_reprogramacion']) ? trim($_POST['fecha_reprogramacion']) :  null;


        $rspta = $evidencias->registrarCalificacion($historial_id, $grado_id, $observaciones, $fecha_reprogramacion);

        if ($rspta) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false]);
        }
        break; */

    case 'registrarCalificacion':
        $historial_id = $_POST["historial_id"];
        $grado_id = $_POST["grado_id"];
        $observaciones = $_POST["observaciones"];
        $fecha_reprogramacion = isset($_POST['fecha_reprogramacion']) ? trim($_POST['fecha_reprogramacion']) : null;
        $fecha_revision = $fecha;
        $archivo_nombre = null;

        if (isset($_FILES['archivo_observacion']) && $_FILES['archivo_observacion']['error'] === 0) {
            $nombre_original = basename($_FILES['archivo_observacion']['name']);
            $nombre_limpio = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nombre_original);
            $fecha_actual = date('YmdHis');
            $archivo_nombre = $fecha_actual . "_" . $nombre_limpio;

            $directorio_destino = "../archivos/observaciones/";
            $ruta_destino = $directorio_destino . $archivo_nombre;

            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            if (!move_uploaded_file($_FILES['archivo_observacion']['tmp_name'], $ruta_destino)) {
                echo json_encode(["success" => false, "mensaje" => "Error al guardar el archivo."]);
                exit;
            }
        }

        $rspta = $evidencias->registrarCalificacion(
            $historial_id,
            $grado_id,
            $observaciones,
            $fecha_reprogramacion,
            $archivo_nombre,
            $fecha_revision
        );

        if ($rspta && $rspta['ok']) {
            $evidencia_nombre = $rspta['evidencia_nombre'];

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'licenciamiento@undc.edu.pe';
                $mail->Password = 'assz ouit kdkh gwbc'; // Usa contraseña de aplicación ucrk hkgh hbmk merh
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('licenciamiento@undc.edu.pe', 'Sistema de Registro para el Licenciamiento Institucional');
                /* $mail->addAddress('soporte.tecnico@undc.edu.pe'); // ← puedes hacerlo dinámico */
                $mail->addAddress($rspta["usu_correo"]);

                $mail->isHTML(true);
                $numero_aleatorio = rand(1, 99);
                $numero_formateado = str_pad($numero_aleatorio, 2, '0', STR_PAD_LEFT);
                $mail->Subject = 'SIRELI: Calificacion registrada - #' . $numero_formateado;

                switch ($grado_id) {
                    case 1:
                        $grado_texto = 'Si Cumple';
                        $grado_color = '#198754';
                        break;
                    case 2:
                        $grado_texto = 'Cumple Parcialmente';
                        $grado_color = '#ffc107';
                        break;
                    case 3:
                        $grado_texto = 'No Cumple';
                        $grado_color = '#dc3545';
                        break;
                    default:
                        $grado_texto = 'No definido';
                        $grado_color = '#6c757d';
                }

                $mail->Body = '
<html>
<head>
    <meta charset="UTF-8">
    <title>Calificación de Evidencia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        .header {
            background-color: #004aad;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .header img {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .body {
            padding: 20px;
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }
        .envio {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #eee;
        }
        .envio p {
            margin: 10px 0;
        }
        .footer {
            background-color: #f4f4f4;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #555;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://sireli.undc.edu.pe/assets/images/logoundc.png" alt="Logo UNDC" style="max-height: 80px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto;margin-bottom:0px">
            <p style="margin: 0; font-size: 16px;">Sistema de Registro para el Licenciamiento Institucional</p>
        </div>

        <div class="body">
            <div class="envio">
                <p>👋 Estimado/a,</p>
                <p>Le informamos que la evidencia que presentó ha sido revisada y su calificación ha sido registrada en el sistema. A continuación, los detalles:</p>
                <p><strong>Evidencia:</strong> ' . htmlspecialchars($evidencia_nombre) . '</p>
                <p><strong>Nivel de cumplimiento:</strong> 
                    <span class="badge text-bg-success" style="background-color:' . $grado_color . '; font-size:11px;">' . htmlspecialchars($grado_texto) . '</span>
                </p>
                <p><strong>Fecha de revisión:</strong> ' . $fecha_revision . '</p>
                <p><strong>Observaciones:</strong> ' . (!empty(trim($observaciones)) ? nl2br(htmlspecialchars($observaciones)) : 'No se registraron observaciones.') . '</p>
                ' . (!empty($fecha_reprogramacion) ? '<p><strong>Fecha de subsanación:</strong> ' . $fecha_reprogramacion . '</p>' : '') . '
                <p>Si tiene alguna duda sobre esta calificación revise el estado de su evidencia en el sistema:</p>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="https://sireli.undc.edu.pe/" target="_blank" style="background-color: #004aad; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                        Ir al sistema SIRELI
                    </a>
                </div>
            </div>
        </div>
        <div class="footer">
            <p style="margin: 2px 0;"><strong>Oficina de Gestión de Calidad</strong></p>
            <p style="margin: 2px 0;">UNIVERSIDAD NACIONAL DE CAÑETE</p>
            <p style="margin: 2px 0;"><strong>Contacto:</strong> 949 026 909</p>
            <p style="margin: 2px 0; font-size: 12px; color: #888;">Este mensaje ha sido generado automáticamente y no requiere respuesta.</p>
        </div>
    </div>
</body>
</html>';


                /*  if ($archivo_nombre && file_exists($ruta_destino)) {
                    $mail->addAttachment($ruta_destino, $archivo_nombre);
                } */

                if (!empty($rspta["usu_correo"])) {
                    $mail->addAddress($rspta["usu_correo"]);
                    $mail->send();
                    echo json_encode(["success" => true]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "mensaje" => "No se encontró el correo del usuario emisor. No se envió la notificación."
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    "success" => false,
                    "email_error" => "Error al enviar correo: " . $mail->ErrorInfo
                ]);
            }
        } else {
            echo json_encode(["success" => false]);
        }
        break;
}
