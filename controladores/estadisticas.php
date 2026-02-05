<?php

session_start();

require_once(__DIR__ . '/../modelos/Estadistica.php');

$estadisticas = new Estadistica();

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fecha = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'mediosPorCondicion':
        $rspta = $estadisticas->mediosPorCondicion();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "cbc_id" => $reg->cbc_id,
                "cbc_nombre" => $reg->cbc_nombre,
                "total_medios" => $reg->total_medios
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

    case 'gradoPorEvidencias':
        $rspta = $estadisticas->gradoPorEvidencias();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "grado_id" => $reg->grado_id,
                "grado_nombre" => $reg->grado_nombre,
                "total_evidencias" => $reg->total_evidencias
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

    case 'evidenciaEstadoPorOficina':
        $oficina_id = $_SESSION['oficina_id'];

        $totalEvidencias = $estadisticas->evidenciasPorOficina($oficina_id);
        $rspta = $estadisticas->evidenciaEstadoPorOficina($oficina_id);

        $data = array();

        $pendienteYObservado = 0;
        $enviadoYRevision = 0;
        $finalizados = 0;

        // Recorremos los registros y agrupamos los estados
        while ($reg = $rspta->fetch_object()) {
            if ($reg->estado_revision == 'Pendiente' || $reg->estado_revision == 'Observado') {
                $pendienteYObservado += $reg->TotalPorEstado;
            } elseif ($reg->estado_revision == 'Enviado' || $reg->estado_revision == 'En Revision') {
                $enviadoYRevision += $reg->TotalPorEstado;
            } elseif ($reg->estado_revision == 'Finalizado') {
                $finalizados += $reg->TotalPorEstado;
            } else {
                $data[] = array(
                    "estado_revision" => $reg->estado_revision,
                    "TotalPorEstado" => $reg->TotalPorEstado
                );
            }
        }

        // Construimos el arreglo con los grupos principales y lo combinamos con lo demás
        $data = array_merge(
            array(
                array(
                    "estado_revision" => 'Pendientes',
                    "TotalPorEstado" => $pendienteYObservado
                ),
                array(
                    "estado_revision" => 'Enviados',
                    "TotalPorEstado" => $enviadoYRevision
                ),
                array(
                    "estado_revision" => 'Finalizado',
                    "TotalPorEstado" => $finalizados
                )
            ),
            $data
        );

        // Respuesta JSON final
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data,
            "totalEvidencias" => $totalEvidencias
        );

        echo json_encode($results);
        break;


    /* case 'reportes':

        $reportes = $estadisticas->reportes();


        $data = array(
            "TotalCondiciones" => $reportes['TotalCondiciones'],
            "TotalComponentes" => $reportes['TotalComponentes'],
            "TotalIndicadores" => $reportes['TotalIndicadores'],
            "TotalMedios" => $reportes['TotalMedios'],
            "TotalEvidencias" => $reportes['TotalEvidencias']
        );

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => 1,
            "iTotalDisplayRecords" => 1,
            "aaData" => array($data)
        );

        echo json_encode($results);
        break; */

    case 'reportes':
        $cbc_id = isset($_POST['cbc_id']) ? $_POST['cbc_id'] : null;
        $componente_id = isset($_POST['componente_id']) ? $_POST['componente_id'] : null;
        $indicador_id = isset($_POST['indicador_id']) ? $_POST['indicador_id'] : null;
        $oficina_id = isset($_POST['oficina_id']) ? $_POST['oficina_id'] : null;

        $reportes = $estadisticas->reportesFiltrados($cbc_id, $componente_id, $indicador_id, $oficina_id);

        $data = array(
            "TotalCondiciones" => $reportes['TotalCondiciones'],
            "TotalComponentes" => $reportes['TotalComponentes'],
            "TotalIndicadores" => $reportes['TotalIndicadores'],
            "TotalMedios" => $reportes['TotalMedios'],
            "TotalEvidencias" => $reportes['TotalEvidencias']
        );

        echo json_encode([
            "sEcho" => 1,
            "iTotalRecords" => 1,
            "iTotalDisplayRecords" => 1,
            "aaData" => [$data]
        ]);
        break;


    case 'listarReporte':

        $condicion = isset($_POST['cbc_id']) ? $_POST['cbc_id'] : '';
        $oficina = isset($_POST['oficina_id']) ? $_POST['oficina_id'] : '';

        $rspta = $estadisticas->listarReporte($condicion, $oficina);

        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "cbc_id" => $reg->cbc_id,
                "cbc_nombre" => $reg->cbc_nombre,
                "componente_id" => $reg->componente_id,
                "componente_nombre" => $reg->componente_nombre,
                "indicador_id" => $reg->indicador_id,
                "indicador_nombre" => $reg->indicador_nombre,
                "medio_id" => $reg->medio_id,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "grado_cumplimiento" => $reg->grado_cumplimiento,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas,
                "coordinadores_vinculados" => $reg->coordinadores_vinculados,
                "cumplimiento_medio" => $reg->cumplimiento_medio,
                "estado_revision" => $reg->estado_revision,
                "archivo_presentacion" => $reg->archivo_presentacion
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

    case 'resumenCumplimientoMV':
        $condicion = isset($_GET['cbc_id']) ? $_GET['cbc_id'] : '';
        $rspta = $estadisticas->resumenCumplimientoMV($condicion);

        $reg = $rspta->fetch_object();

        if ($reg) {
            echo json_encode([
                "no_aplica" => (int)$reg->no_aplica,
                "pendiente" => (int)$reg->pendiente,
                "no_cumple" => (int)$reg->no_cumple,
                "cumple_parcial" => (int)$reg->cumple_parcial,
                "si_cumple" => (int)$reg->si_cumple
            ]);
        } else {
            echo json_encode([
                "no_aplica" => 0,
                "pendiente" => 0,
                "no_cumple" => 0,
                "cumple_parcial" => 0,
                "si_cumple" => 0
            ]);
        }
        break;

    case 'listarMediosCumplimiento':
        $condicion = isset($_GET['cbc_id']) ? $_GET['cbc_id'] : '';
        $rspta = $estadisticas->listarMediosCumplimiento($condicion);

        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "medio_id" => $reg->medio_id,
                "medio_nombre" => $reg->medio_nombre,
                "cumplimiento_medio" => $reg->cumplimiento_medio
            );
        }

        echo json_encode(['aaData' => $data]);
        break;

    case 'resumenOficinaCumplimientoMV':
        $oficina_id = isset($_GET['oficina_id']) ? $_GET['oficina_id'] : '';

        $rspta = $estadisticas->resumenOficinaCumplimientoMV($oficina_id);

        if (!$rspta) {
            echo json_encode(['error' => 'Error en la consulta SQL.']);
            return;
        }

        $reg = $rspta->fetch_object();

        // Siempre devolver los 4 valores, incluso si reg es null
        echo json_encode([
            "si_cumple" => isset($reg->si_cumple) ? (int)$reg->si_cumple : 0,
            "no_cumple" => isset($reg->no_cumple) ? (int)$reg->no_cumple : 0,
            "cumple_parcial" => isset($reg->cumple_parcial) ? (int)$reg->cumple_parcial : 0,
            "pendiente" => isset($reg->pendiente) ? (int)$reg->pendiente : 0
        ]);
        break;


    case 'listarMediosCumplimientoPorOficina':
        $oficina_id = isset($_GET['oficina_id']) ? $_GET['oficina_id'] : '';
        $rspta = $estadisticas->listarMediosCumplimientoPorOficina($oficina_id);

        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "evidencia_nombre" => $reg->evidencia_nombre,
                "grado_cumplimiento" => $reg->grado_cumplimiento,
                "estado_revision" => $reg->estado_revision
            );
        }

        echo json_encode(['aaData' => $data]);
        break;
        
    case 'evidenciasPorRecibir':
        $rspta = $estadisticas->evidenciasPorRecibir();
        echo json_encode(["total" => $rspta]);
        break;

    case 'evidenciasPorRevisar':
        $rspta = $estadisticas->evidenciasPorRevisar();
        echo json_encode(["total" => $rspta]);
        break;
        /* case 'listarReporte':
        $rspta = $estadisticas->listarReporte();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "cbc_nombre" => $reg->cbc_nombre,
                "componente_nombre" => $reg->componente_nombre,
                "indicador_nombre" => $reg->indicador_nombre,
                "medio_nombre" => $reg->medio_nombre,
                "evidencia_nombre" => $reg->evidencia_nombre,
                "grado_cumplimiento" => $reg->grado_cumplimiento,
                "oficinas_vinculadas" => $reg->oficinas_vinculadas
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
}
