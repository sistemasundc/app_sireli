<?php

require_once(__DIR__ . '/../config/conexion.php');


class Estadistica
{

    public function __construct() {}
    public function mediosPorCondicion()
    {
        $sql = "SELECT  c.cbc_id,c.cbc_nombre,COUNT(m.medio_id) AS total_medios FROM tm_cbc AS c          
        LEFT JOIN     tm_componentes       AS cp ON cp.cbc_id        = c.cbc_id
        LEFT JOIN     tm_indicadores       AS i  ON i.componente_id  = cp.componente_id
        LEFT JOIN     tm_medio_verificacion AS m ON m.indicador_id = i.indicador_id
        WHERE         c.estado = 1                     
        GROUP BY      c.cbc_id, c.cbc_nombre
        ORDER BY      cbc_nombre ASC;";
        return ejecutarConsulta($sql);
    }
    public function gradoPorEvidencias()
    {
        $sql = "SELECT
                IFNULL(g.grado_nombre, 'Pendiente') AS grado_nombre,
                g.grado_id,
                COUNT(e.evidencia_id) AS total_evidencias
            FROM tm_evidencias AS e
            LEFT JOIN tm_grado_cumplimiento AS g
                ON e.grado_id = g.grado_id
            WHERE e.estado = 1
            GROUP BY g.grado_id, g.grado_nombre
            ORDER BY g.grado_id;";

        return ejecutarConsulta($sql);
    }

    public function evidenciaEstadoPorOficina($oficina_id)
    {
        // Iniciar la consulta SQL
        $sql = "SELECT 
            e.estado_revision,
            COUNT(DISTINCT e.evidencia_id) AS TotalPorEstado
        FROM tm_evidencias e
        LEFT JOIN tm_evidencias_oficinas eo
            ON eo.evidencia_id = e.evidencia_id
            AND eo.oficina_id = $oficina_id
            AND eo.eliminado = 0
        LEFT JOIN tm_evidencias_coordinadores ec
            ON ec.evidencia_id = e.evidencia_id
            AND ec.oficina_id = $oficina_id
            AND ec.eliminado = 0
        WHERE e.estado = 1
        AND (eo.evidencia_id IS NOT NULL OR ec.evidencia_id IS NOT NULL)
        GROUP BY e.estado_revision;
        ";

        return ejecutarConsulta($sql);
    }

    /* public function evidenciaEstadoPorOficina($oficina_id)
    {
        // Iniciar la consulta SQL
        $sql = "SELECT 
            e.estado_revision, 
            IFNULL(COUNT(eo_sub.evidencia_id), 0) AS TotalPorEstado
        FROM 
            tm_evidencias e
        LEFT JOIN tm_evidencias_oficinas eo_sub 
            ON eo_sub.evidencia_id = e.evidencia_id
            AND eo_sub.oficina_id = $oficina_id
        WHERE e.estado=1  AND eo_sub.eliminado=0
        GROUP BY 
            e.estado_revision";

        return ejecutarConsulta($sql);
    } */
    public function evidenciasPorOficina($oficina_id)
    {
        $sql = "SELECT COUNT(*) AS TotalPorOficina
        FROM (
            -- Evidencias asociadas por oficina normal
            SELECT e.evidencia_id
            FROM tm_evidencias e
            JOIN tm_evidencias_oficinas eo
                ON eo.evidencia_id = e.evidencia_id
            WHERE eo.oficina_id = $oficina_id
            AND e.estado = 1
            AND eo.eliminado = 0

            UNION

            -- Evidencias asociadas por coordinadores
            SELECT e.evidencia_id
            FROM tm_evidencias e
            JOIN tm_evidencias_coordinadores ec
                ON ec.evidencia_id = e.evidencia_id
            WHERE ec.oficina_id = $oficina_id
            AND e.estado = 1
            AND ec.eliminado = 0
        ) AS t;
        ";

        $result = ejecutarConsultaSimpleFila($sql);
        return isset($result['TotalPorOficina']) ? $result['TotalPorOficina'] : 0;
    }

    /* public function evidenciasPorOficina($oficina_id)
    {
        $sql = "SELECT COUNT(*) AS TotalPorOficina 
            FROM tm_evidencias e 
            JOIN tm_evidencias_oficinas eo_sub 
            ON eo_sub.evidencia_id = e.evidencia_id 
            WHERE eo_sub.oficina_id = $oficina_id AND e.estado=1 AND eo_sub.eliminado=0";

        $result = ejecutarConsultaSimpleFila($sql);
        return isset($result['TotalPorOficina']) ? $result['TotalPorOficina'] : 0;
    } */

    public function reporteUsuariosActivos()
    {
        // Iniciar la consulta SQL
        $sql = "SELECT COUNT(*) as TotalUsuariosActivos FROM `tm_usuario` WHERE estado = 1;";

        $result = ejecutarConsultaSimpleFila($sql);

        return isset($result['TotalUsuariosActivos']) ? $result['TotalUsuariosActivos'] : 0;
    }

    public function evidenciasPorRecibir()
    {
        $sql = "SELECT 
                COUNT(*) as total_por_recibir
                FROM tm_evidencias e
                INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
                INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
                LEFT JOIN tm_evidencias_historial h ON h.evidencia_id = e.evidencia_id
                LEFT JOIN tm_usuario ue ON h.usu_emisor_id = ue.usu_id
                LEFT JOIN tm_oficina o ON h.oficina_origen_id = o.oficina_id
                WHERE EXISTS (
                    SELECT 1 
                    FROM tm_evidencias_oficinas eo_sub
                    WHERE eo_sub.evidencia_id = e.evidencia_id
                    AND e.estado_revision = 'Enviado' 
                    AND h.estado2 = 'Sin Recibir' AND eo_sub.eliminado=0);";

        $result = ejecutarConsultaSimpleFila($sql);
        return isset($result['total_por_recibir']) ? $result['total_por_recibir'] : 0;
    }

    public function evidenciasPorRevisar()
    {
        $sql = "SELECT 
                COUNT(*) as total_por_revisar
                FROM tm_evidencias e
                INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
                INNER JOIN tm_usuario u ON e.usu_id = u.usu_id

                LEFT JOIN tm_evidencias_historial h ON h.evidencia_id = e.evidencia_id
                LEFT JOIN tm_usuario ue ON h.usu_emisor_id = ue.usu_id
                LEFT JOIN tm_oficina o ON h.oficina_origen_id = o.oficina_id

                WHERE EXISTS (
                    SELECT 1 
                    FROM tm_evidencias_oficinas eo_sub
                    WHERE eo_sub.evidencia_id = e.evidencia_id
                    AND e.estado_revision = 'En Revision' AND h.estado2='Recibido' AND eo_sub.eliminado=0);";

        $result = ejecutarConsultaSimpleFila($sql);
        return isset($result['total_por_revisar']) ? $result['total_por_revisar'] : 0;
    }

    /*     public function reportes()
    {
        // Iniciar la consulta SQL
        $sql = "SELECT 
            (SELECT COUNT(*) FROM `tm_cbc`) AS TotalCondiciones,
            (SELECT COUNT(*) FROM `tm_componentes`) AS TotalComponentes,
            (SELECT COUNT(*) FROM `tm_indicadores`) AS TotalIndicadores,
            (SELECT COUNT(*) FROM `tm_medio_verificacion`) AS TotalMedios,
            (SELECT COUNT(*) FROM `tm_evidencias`) AS TotalEvidencias;";

        return ejecutarConsultaSimpleFila($sql);
    } */

    /*     public function reportesFiltrados($cbc_id, $componente_id, $indicador_id, $oficina_id)
    {
        $filtros = [
            'cbc_id' => $cbc_id,
            'componente_id' => $componente_id,
            'indicador_id' => $indicador_id,
            'oficina_id' => $oficina_id
        ];

        if (array_filter($filtros) === []) {
            $sql = "SELECT 
            (SELECT COUNT(*) FROM tm_cbc WHERE estado = 1) AS TotalCondiciones,
            (SELECT COUNT(*) FROM tm_componentes WHERE estado = 1) AS TotalComponentes,
            (SELECT COUNT(*) FROM tm_indicadores WHERE estado = 1) AS TotalIndicadores,
            (SELECT COUNT(*) FROM tm_medio_verificacion WHERE estado = 1) AS TotalMedios,
            (SELECT COUNT(*) FROM `tm_evidencias`) AS TotalEvidencias;";
        } else {
            $cbc_id = $cbc_id ?: '';
            $componente_id = $componente_id ?: '';
            $indicador_id = $indicador_id ?: '';
            $oficina_id = $oficina_id ?: '';

            $sql = "
        SELECT 
            -- Total condiciones filtradas
            (SELECT COUNT(DISTINCT c.cbc_id)
             FROM tm_cbc c
             INNER JOIN tm_componentes comp ON comp.cbc_id = c.cbc_id AND comp.estado = 1
             INNER JOIN tm_indicadores i ON i.componente_id = comp.componente_id AND i.estado = 1
             INNER JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id AND m.estado = 1
             INNER JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
             INNER JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
             WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
               AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
               AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
               AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
            ) AS TotalCondiciones,

            -- Total componentes filtrados
            (SELECT COUNT(DISTINCT comp.componente_id)
             FROM tm_componentes comp
             INNER JOIN tm_indicadores i ON i.componente_id = comp.componente_id AND i.estado = 1
             INNER JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id AND m.estado = 1
             INNER JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
             INNER JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
             WHERE ('$cbc_id' = '' OR comp.cbc_id = '$cbc_id')
               AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
               AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
               AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
            ) AS TotalComponentes,

           -- Total indicadores filtrados (corrige el JOIN hasta cbc)
            (SELECT COUNT(DISTINCT i.indicador_id)
            FROM tm_indicadores i
            INNER JOIN tm_componentes comp ON i.componente_id = comp.componente_id AND comp.estado = 1
            INNER JOIN tm_cbc c ON comp.cbc_id = c.cbc_id AND c.estado = 1
            INNER JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id AND m.estado = 1
            INNER JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
            INNER JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
            WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
            AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
            AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
            AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
            ) AS TotalIndicadores,


            -- Total medios filtrados
            (SELECT COUNT(DISTINCT m.medio_id)
             FROM tm_medio_verificacion m
             INNER JOIN tm_indicadores i ON m.indicador_id = i.indicador_id AND i.estado = 1
             INNER JOIN tm_componentes comp ON i.componente_id = comp.componente_id AND comp.estado = 1
             INNER JOIN tm_cbc c ON comp.cbc_id = c.cbc_id AND c.estado = 1
             INNER JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
             INNER JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
             WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
               AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
               AND ('$indicador_id' = '' OR m.indicador_id = '$indicador_id')
               AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
            ) AS TotalMedios,

            -- Total evidencias filtradas
            (SELECT COUNT(
                    " . ($oficina_id === '' ? "DISTINCT e.evidencia_id" : "*") . "
                )
            FROM tm_evidencias e
            INNER JOIN tm_medio_verificacion m ON e.medio_id = m.medio_id AND m.estado = 1
            INNER JOIN tm_indicadores i ON m.indicador_id = i.indicador_id AND i.estado = 1
            INNER JOIN tm_componentes comp ON i.componente_id = comp.componente_id AND comp.estado = 1
            INNER JOIN tm_cbc c ON comp.cbc_id = c.cbc_id AND c.estado = 1
            INNER JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
            WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
            AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
            AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
            AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
            AND e.estado = 1
            ) AS TotalEvidencias


        ";
        }

        return ejecutarConsultaSimpleFila($sql);
    } */

    public function reportesFiltrados($cbc_id, $componente_id, $indicador_id, $oficina_id)
    {
        $filtros = [
            'cbc_id' => $cbc_id,
            'componente_id' => $componente_id,
            'indicador_id' => $indicador_id,
            'oficina_id' => $oficina_id
        ];

        if (array_filter($filtros) === []) {
            $sql = "SELECT 
            (SELECT COUNT(*) FROM tm_cbc WHERE estado = 1) AS TotalCondiciones,
            (SELECT COUNT(*) FROM tm_componentes WHERE estado = 1) AS TotalComponentes,
            (SELECT COUNT(*) FROM tm_indicadores WHERE estado = 1) AS TotalIndicadores,
            (SELECT COUNT(*) FROM tm_medio_verificacion WHERE estado = 1) AS TotalMedios,
            (SELECT COUNT(*) FROM `tm_evidencias` WHERE estado = 1) AS TotalEvidencias;";
        } else {
            $cbc_id = $cbc_id ?: '';
            $componente_id = $componente_id ?: '';
            $indicador_id = $indicador_id ?: '';
            $oficina_id = $oficina_id ?: '';

            $sql = "
        SELECT 
            -- Total condiciones filtradas
            (SELECT COUNT(DISTINCT c.cbc_id)
             FROM tm_cbc c
             LEFT JOIN tm_componentes comp ON comp.cbc_id = c.cbc_id AND comp.estado = 1
             LEFT JOIN tm_indicadores i ON i.componente_id = comp.componente_id AND i.estado = 1
             LEFT JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id AND m.estado = 1
             LEFT JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
             LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
             LEFT JOIN tm_evidencias_coordinadores ec ON ec.evidencia_id = e.evidencia_id AND ec.eliminado = 0
             WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
               AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
               AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
               AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id' OR ec.oficina_id = '$oficina_id')
               AND c.estado = 1
            ) AS TotalCondiciones,

            -- Total componentes filtrados
            (SELECT COUNT(DISTINCT comp.componente_id)
             FROM tm_componentes comp
             LEFT JOIN tm_indicadores i ON i.componente_id = comp.componente_id AND i.estado = 1
             LEFT JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id AND m.estado = 1
             LEFT JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
             LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
             LEFT JOIN tm_evidencias_coordinadores ec ON ec.evidencia_id = e.evidencia_id AND ec.eliminado = 0
             WHERE ('$cbc_id' = '' OR comp.cbc_id = '$cbc_id')
               AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
               AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
               AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id' OR ec.oficina_id = '$oficina_id')
               AND comp.estado = 1
            ) AS TotalComponentes,

           -- Total indicadores filtrados (corrige el JOIN hasta cbc)
            (SELECT COUNT(DISTINCT i.indicador_id)
            FROM tm_indicadores i
            LEFT JOIN tm_componentes comp ON i.componente_id = comp.componente_id AND comp.estado = 1
            LEFT JOIN tm_cbc c ON comp.cbc_id = c.cbc_id AND c.estado = 1
            LEFT JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id AND m.estado = 1
            LEFT JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
            LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
            LEFT JOIN tm_evidencias_coordinadores ec ON ec.evidencia_id = e.evidencia_id AND ec.eliminado = 0
            WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
            AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
            AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
            AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id' OR ec.oficina_id = '$oficina_id')
            AND i.estado = 1
            ) AS TotalIndicadores,


            -- Total medios filtrados
            (SELECT COUNT(DISTINCT m.medio_id)
             FROM tm_medio_verificacion m
             LEFT JOIN tm_indicadores i ON m.indicador_id = i.indicador_id AND i.estado = 1
             LEFT JOIN tm_componentes comp ON i.componente_id = comp.componente_id AND comp.estado = 1
             LEFT JOIN tm_cbc c ON comp.cbc_id = c.cbc_id AND c.estado = 1
             LEFT JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
             LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
             LEFT JOIN tm_evidencias_coordinadores ec ON ec.evidencia_id = e.evidencia_id AND ec.eliminado = 0
             WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
               AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
               AND ('$indicador_id' = '' OR m.indicador_id = '$indicador_id')
               AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id' OR ec.oficina_id = '$oficina_id')
               AND m.estado = 1
            ) AS TotalMedios,

            -- Total evidencias filtradas
            (SELECT COUNT(
                    " . ($oficina_id === '' ? "DISTINCT e.evidencia_id" : "*") . "
                )
            FROM tm_evidencias e
            LEFT JOIN tm_medio_verificacion m ON e.medio_id = m.medio_id AND m.estado = 1
            LEFT JOIN tm_indicadores i ON m.indicador_id = i.indicador_id AND i.estado = 1
            LEFT JOIN tm_componentes comp ON i.componente_id = comp.componente_id AND comp.estado = 1
            LEFT JOIN tm_cbc c ON comp.cbc_id = c.cbc_id AND c.estado = 1
            LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
            LEFT JOIN tm_evidencias_coordinadores ec ON ec.evidencia_id = e.evidencia_id AND ec.eliminado = 0
            WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
            AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
            AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
            AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id' OR ec.oficina_id = '$oficina_id')
            AND e.estado = 1
            ) AS TotalEvidencias


        ";
        }

        return ejecutarConsultaSimpleFila($sql);
    }
    public function reportesFiltradosOld($cbc_id, $componente_id, $indicador_id, $oficina_id)
    {
        $filtros = [
            'cbc_id' => $cbc_id,
            'componente_id' => $componente_id,
            'indicador_id' => $indicador_id,
            'oficina_id' => $oficina_id
        ];

        if (array_filter($filtros) === []) {
            $sql = "SELECT 
            (SELECT COUNT(*) FROM tm_cbc WHERE estado = 1) AS TotalCondiciones,
            (SELECT COUNT(*) FROM tm_componentes WHERE estado = 1) AS TotalComponentes,
            (SELECT COUNT(*) FROM tm_indicadores WHERE estado = 1) AS TotalIndicadores,
            (SELECT COUNT(*) FROM tm_medio_verificacion WHERE estado = 1) AS TotalMedios,
            (SELECT COUNT(*) FROM `tm_evidencias` WHERE estado = 1) AS TotalEvidencias;";
        } else {
            $cbc_id = $cbc_id ?: '';
            $componente_id = $componente_id ?: '';
            $indicador_id = $indicador_id ?: '';
            $oficina_id = $oficina_id ?: '';

            $sql = "
        SELECT 
            -- Total condiciones filtradas
            (SELECT COUNT(DISTINCT c.cbc_id)
             FROM tm_cbc c
             LEFT JOIN tm_componentes comp ON comp.cbc_id = c.cbc_id AND comp.estado = 1
             LEFT JOIN tm_indicadores i ON i.componente_id = comp.componente_id AND i.estado = 1
             LEFT JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id AND m.estado = 1
             LEFT JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
             LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
             WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
               AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
               AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
               AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
               AND c.estado = 1
            ) AS TotalCondiciones,

            -- Total componentes filtrados
            (SELECT COUNT(DISTINCT comp.componente_id)
             FROM tm_componentes comp
             LEFT JOIN tm_indicadores i ON i.componente_id = comp.componente_id AND i.estado = 1
             LEFT JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id AND m.estado = 1
             LEFT JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
             LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
             WHERE ('$cbc_id' = '' OR comp.cbc_id = '$cbc_id')
               AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
               AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
               AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
               AND comp.estado = 1
            ) AS TotalComponentes,

           -- Total indicadores filtrados (corrige el JOIN hasta cbc)
            (SELECT COUNT(DISTINCT i.indicador_id)
            FROM tm_indicadores i
            LEFT JOIN tm_componentes comp ON i.componente_id = comp.componente_id AND comp.estado = 1
            LEFT JOIN tm_cbc c ON comp.cbc_id = c.cbc_id AND c.estado = 1
            LEFT JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id AND m.estado = 1
            LEFT JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
            LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
            WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
            AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
            AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
            AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
            AND i.estado = 1
            ) AS TotalIndicadores,


            -- Total medios filtrados
            (SELECT COUNT(DISTINCT m.medio_id)
             FROM tm_medio_verificacion m
             LEFT JOIN tm_indicadores i ON m.indicador_id = i.indicador_id AND i.estado = 1
             LEFT JOIN tm_componentes comp ON i.componente_id = comp.componente_id AND comp.estado = 1
             LEFT JOIN tm_cbc c ON comp.cbc_id = c.cbc_id AND c.estado = 1
             LEFT JOIN tm_evidencias e ON e.medio_id = m.medio_id AND e.estado = 1
             LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
             WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
               AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
               AND ('$indicador_id' = '' OR m.indicador_id = '$indicador_id')
               AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
               AND m.estado = 1
            ) AS TotalMedios,

            -- Total evidencias filtradas
            (SELECT COUNT(
                    " . ($oficina_id === '' ? "DISTINCT e.evidencia_id" : "*") . "
                )
            FROM tm_evidencias e
            LEFT JOIN tm_medio_verificacion m ON e.medio_id = m.medio_id AND m.estado = 1
            LEFT JOIN tm_indicadores i ON m.indicador_id = i.indicador_id AND i.estado = 1
            LEFT JOIN tm_componentes comp ON i.componente_id = comp.componente_id AND comp.estado = 1
            LEFT JOIN tm_cbc c ON comp.cbc_id = c.cbc_id AND c.estado = 1
            LEFT JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id AND eo.eliminado = 0
            WHERE ('$cbc_id' = '' OR c.cbc_id = '$cbc_id')
            AND ('$componente_id' = '' OR comp.componente_id = '$componente_id')
            AND ('$indicador_id' = '' OR i.indicador_id = '$indicador_id')
            AND ('$oficina_id' = '' OR eo.oficina_id = '$oficina_id')
            AND e.estado = 1
            ) AS TotalEvidencias


        ";
        }

        return ejecutarConsultaSimpleFila($sql);
    }


    /*     public function listarReporte()
    {
        $sql = "SELECT
        c.cbc_id,
        c.cbc_nombre,

        cp.componente_id,
        cp.componente_nombre,

        i.indicador_id,
        i.indicador_nombre,

        m.medio_id,
        m.medio_nombre,

        e.evidencia_id,
        e.evidencia_nombre,
        e.grado_id,
        g.grado_nombre AS grado_cumplimiento,

        (
    SELECT GROUP_CONCAT(DISTINCT CONCAT('- ', o2.oficina_nom) SEPARATOR '<br>')
    FROM tm_evidencias_oficinas eo2
    INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
    WHERE eo2.evidencia_id = e.evidencia_id
    ) AS oficinas_vinculadas


    FROM tm_cbc AS c
    JOIN tm_componentes AS cp ON cp.cbc_id = c.cbc_id
    JOIN tm_indicadores AS i ON i.componente_id = cp.componente_id
    JOIN tm_medio_verificacion AS m ON m.indicador_id = i.indicador_id
    LEFT JOIN tm_evidencias AS e ON e.medio_id = m.medio_id
    LEFT JOIN tm_grado_cumplimiento AS g ON g.grado_id = e.grado_id
    WHERE c.estado=1 AND cp.estado=1 AND m.estado=1 AND i.estado=1 AND (e.evidencia_id IS NULL OR e.estado = 1)
    ORDER BY
        c.cbc_id,
        cp.componente_id,
        i.indicador_id,
        m.medio_id,
        e.evidencia_id";

        return ejecutarConsulta($sql);
    } */

    public function listarReporteok1($condicion = '', $oficina = '')
    {
        $filtroCond = '';
        $filtroOfi = '';

        if ($condicion != '') {
            $filtroCond = "AND c.cbc_id = '$condicion'";
        }

        if ($oficina != '') {
            $filtroOfi = "AND EXISTS (
            SELECT 1 
            FROM tm_evidencias_oficinas eo2
            WHERE eo2.evidencia_id = e.evidencia_id AND eo2.oficina_id = '$oficina'
        )";
        }

        $sql = "SELECT
        c.cbc_id,
        c.cbc_nombre,
        cp.componente_nombre,
        i.indicador_nombre,
        m.medio_nombre,
        e.evidencia_nombre,
        g.grado_nombre AS grado_cumplimiento,
        (
            SELECT GROUP_CONCAT(DISTINCT CONCAT('- ', o2.oficina_nom) SEPARATOR '<br>')
            FROM tm_evidencias_oficinas eo2
            INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
            WHERE eo2.evidencia_id = e.evidencia_id
        ) AS oficinas_vinculadas
    FROM tm_cbc AS c
    JOIN tm_componentes AS cp ON cp.cbc_id = c.cbc_id
    JOIN tm_indicadores AS i ON i.componente_id = cp.componente_id
    JOIN tm_medio_verificacion AS m ON m.indicador_id = i.indicador_id
    LEFT JOIN tm_evidencias AS e ON e.medio_id = m.medio_id
    LEFT JOIN tm_grado_cumplimiento AS g ON g.grado_id = e.grado_id
    WHERE c.estado = 1 AND cp.estado = 1 AND m.estado = 1 AND i.estado = 1 AND (e.evidencia_id IS NULL OR e.estado = 1)
    $filtroCond
    $filtroOfi
    ORDER BY c.cbc_id, cp.componente_id, i.indicador_id, m.medio_id, e.evidencia_id";

        return ejecutarConsulta($sql);
    }

    /*     public function listarReporte($condicion = '', $oficina = '')
    {
        $filtroCond = '';
        $filtroOfi = '';

        if ($condicion != '') {
            $filtroCond = "AND c.cbc_id = '$condicion'";
        }

        if ($oficina != '') {
            $filtroOfi = "AND EXISTS (
            SELECT 1 
            FROM tm_evidencias_oficinas eo2
            WHERE eo2.evidencia_id = e.evidencia_id AND eo2.oficina_id = '$oficina'
        )";
        }

        $sql = "SELECT
        c.cbc_id,
        c.cbc_nombre,
        cp.componente_nombre,
        i.indicador_nombre,
        m.medio_id,
        m.medio_nombre,
        estado_revision,

        CASE
            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id AND ev.estado = 1
            ) = 0 THEN 'No aplica'

             WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                    AND ev.estado = 1 
                    AND (ev.grado_id IS NULL OR ev.estado_revision IN ('En Revision', 'Enviado'))
            ) > 0 THEN 'Pendiente'

            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                LEFT JOIN tm_grado_cumplimiento gc ON gc.grado_id = ev.grado_id
                WHERE ev.medio_id = m.medio_id AND ev.estado = 1 AND gc.grado_nombre = 'No Cumple'
            ) = (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id AND ev.estado = 1
            ) THEN 'No Cumple'

            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                LEFT JOIN tm_grado_cumplimiento gc ON gc.grado_id = ev.grado_id
                WHERE ev.medio_id = m.medio_id AND ev.estado = 1 AND gc.grado_nombre IN ('No Cumple', 'Cumple Parcialmente')
            ) > 0 THEN 'Cumple Parcialmente'

            ELSE 'Si Cumple'
        END AS cumplimiento_medio,

        e.evidencia_nombre,
        g.grado_nombre AS grado_cumplimiento,
        (
            SELECT GROUP_CONCAT(DISTINCT CONCAT('- ', o2.oficina_nom) SEPARATOR '<br>')
            FROM tm_evidencias_oficinas eo2
            INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
            WHERE eo2.evidencia_id = e.evidencia_id
        ) AS oficinas_vinculadas

    FROM tm_cbc AS c
    JOIN tm_componentes AS cp ON cp.cbc_id = c.cbc_id
    JOIN tm_indicadores AS i ON i.componente_id = cp.componente_id
    JOIN tm_medio_verificacion AS m ON m.indicador_id = i.indicador_id
    LEFT JOIN tm_evidencias AS e ON e.medio_id = m.medio_id
    LEFT JOIN tm_grado_cumplimiento AS g ON g.grado_id = e.grado_id
    WHERE c.estado = 1 AND cp.estado = 1 AND m.estado = 1 AND i.estado = 1 AND (e.evidencia_id IS NULL OR e.estado = 1)
    $filtroCond
    $filtroOfi
    ORDER BY c.cbc_id, cp.componente_id, i.indicador_id, m.medio_id, e.evidencia_id";

        return ejecutarConsulta($sql);
    } */

    public function listarReporte($condicion = '', $oficina = '')
    {
        $filtroCond = '';
        $filtroOfi = '';

        if ($condicion != '') {
            $filtroCond = "AND c.cbc_id = '$condicion'";
        }

        // Filtrar por oficina solo si hay evidencia
        if ($oficina != '') {
            $filtroOfi = "AND (
            (e.evidencia_id IS NOT NULL AND (
                EXISTS (
                    SELECT 1 
                    FROM tm_evidencias_oficinas eo2
                    WHERE eo2.evidencia_id = e.evidencia_id 
                      AND eo2.oficina_id = '$oficina' 
                      AND eo2.eliminado = 0
                ) 
                OR EXISTS (
                    SELECT 1 
                    FROM tm_evidencias_coordinadores ec
                    WHERE ec.evidencia_id = e.evidencia_id
                      AND ec.oficina_id = '$oficina'
                      AND ec.eliminado = 0
                )
            ))
        )";
        }

        $sql = "
    SELECT
        c.cbc_id,
        c.cbc_nombre,
        cp.componente_id,
        cp.componente_nombre,
        i.indicador_id,
        i.indicador_nombre,
        m.medio_id,
        m.medio_nombre,
        estado_revision,

        CASE
            -- 1. No aplica (no tiene evidencias)
            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
            ) = 0 THEN 'No aplica'

            -- 2. Todas están pendientes
            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
                  AND ev.estado_revision IN ('Pendiente', 'En Revision', 'Enviado')
            ) = (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
            ) THEN 'Pendiente'

            -- 3. Todas No Cumple
            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
                  AND ev.grado_id = 3
            ) = (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
            ) THEN 'No Cumple'

            -- 4. Todas Si Cumple
            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
                  AND ev.grado_id = 1
            ) = (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
            ) THEN 'Si Cumple'

            -- 5. Mezcla = Cumple Parcialmente
            ELSE 'Cumple Parcialmente'
        END AS cumplimiento_medio,

        e.evidencia_nombre,
        g.grado_nombre AS grado_cumplimiento,

        -- Oficinas vinculadas
        (
            SELECT GROUP_CONCAT(DISTINCT CONCAT('- ', o2.oficina_nom) SEPARATOR '<br>')
            FROM tm_evidencias_oficinas eo2
            INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
            WHERE eo2.evidencia_id = e.evidencia_id 
              AND eo2.eliminado = 0
        ) AS oficinas_vinculadas,

        -- Coordinadores vinculados
        (
            SELECT GROUP_CONCAT(DISTINCT CONCAT('- ', o2.oficina_nom) SEPARATOR '<br>')
            FROM tm_evidencias_coordinadores ec
            INNER JOIN tm_oficina o2 ON ec.oficina_id = o2.oficina_id
            WHERE ec.evidencia_id = e.evidencia_id 
              AND ec.eliminado = 0
        ) AS coordinadores_vinculados,

        -- Último archivo de presentación finalizado
        COALESCE((
            SELECT eh.archivo_presentacion
            FROM tm_evidencias_historial eh
            WHERE eh.evidencia_id = e.evidencia_id
              AND eh.estado2 = 'Finalizado'
              AND e.estado_revision = 'Finalizado'
            ORDER BY eh.fecha_emision DESC
            LIMIT 1
        ), 'No disponible') AS archivo_presentacion

    FROM tm_cbc AS c
    JOIN tm_componentes AS cp ON cp.cbc_id = c.cbc_id
    JOIN tm_indicadores AS i ON i.componente_id = cp.componente_id
    JOIN tm_medio_verificacion AS m ON m.indicador_id = i.indicador_id
    LEFT JOIN tm_evidencias AS e ON e.medio_id = m.medio_id
    LEFT JOIN tm_grado_cumplimiento AS g ON g.grado_id = e.grado_id
    WHERE c.estado = 1 
      AND cp.estado = 1 
      AND m.estado = 1 
      AND i.estado = 1 
      AND (e.evidencia_id IS NULL OR e.estado = 1)
      $filtroCond
      $filtroOfi
    ORDER BY c.cbc_id, cp.componente_id, i.indicador_id, m.medio_id, e.evidencia_id
    ";

        return ejecutarConsulta($sql);
    }

    public function resumenCumplimientoMV($condicion = '')
    {
        $filtroCond = '';
        if ($condicion != '') {
            $filtroCond = "AND c.cbc_id = '$condicion'";
        }

        $sql = "
    SELECT
        SUM(CASE WHEN cumplimiento_medio = 'No aplica' THEN 1 ELSE 0 END) AS no_aplica,
        SUM(CASE WHEN cumplimiento_medio = 'Pendiente' THEN 1 ELSE 0 END) AS pendiente,
        SUM(CASE WHEN cumplimiento_medio = 'No Cumple' THEN 1 ELSE 0 END) AS no_cumple,
        SUM(CASE WHEN cumplimiento_medio = 'Si Cumple' THEN 1 ELSE 0 END) AS si_cumple,
        SUM(CASE WHEN cumplimiento_medio = 'Cumple Parcialmente' THEN 1 ELSE 0 END) AS cumple_parcial
    FROM (
        SELECT 
            m.medio_id,
            CASE
                WHEN COUNT(ev.evidencia_id) = 0 THEN 'No aplica'
                WHEN SUM(ev.estado_revision IN ('Pendiente','En Revision','Enviado')) = COUNT(ev.evidencia_id) THEN 'Pendiente'
                WHEN SUM(ev.grado_id = 3) = COUNT(ev.evidencia_id) THEN 'No Cumple'
                WHEN SUM(ev.grado_id = 1) = COUNT(ev.evidencia_id) THEN 'Si Cumple'
                ELSE 'Cumple Parcialmente'
            END AS cumplimiento_medio
        FROM tm_medio_verificacion m
        LEFT JOIN tm_evidencias ev ON ev.medio_id = m.medio_id AND ev.estado = 1
        JOIN tm_indicadores i ON i.indicador_id = m.indicador_id
        JOIN tm_componentes cp ON cp.componente_id = i.componente_id
        JOIN tm_cbc c ON c.cbc_id = cp.cbc_id
        WHERE c.estado = 1 AND cp.estado = 1 AND i.estado = 1 AND m.estado = 1
        $filtroCond
        GROUP BY m.medio_id
    ) AS sub;
    ";

        return ejecutarConsulta($sql);
    }




    public function listarMediosCumplimiento($condicion = '')
    {
        $filtroCond = '';

        if ($condicion != '') {
            $filtroCond = "AND c.cbc_id = '$condicion'";
        }

        $sql = "
    SELECT
        m.medio_id,
        m.medio_nombre,

        CASE
            /* 1. NO APLICA */
            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id AND ev.estado = 1
            ) = 0
            THEN 'No aplica'

            /* 2. PENDIENTE: TODOS pendiente */
            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
                  AND ev.estado_revision IN ('Pendiente','En Revision','Enviado')
            ) = (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id AND ev.estado = 1
            )
            THEN 'Pendiente'

            /* 3. NO CUMPLE: TODOS grado_id = 3 */
            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
                  AND ev.grado_id = 3
            ) = (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id AND ev.estado = 1
            )
            THEN 'No Cumple'

            /* 4. SI CUMPLE: TODOS grado_id = 1 */
            WHEN (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id 
                  AND ev.estado = 1
                  AND ev.grado_id = 1
            ) = (
                SELECT COUNT(*) 
                FROM tm_evidencias ev
                WHERE ev.medio_id = m.medio_id AND ev.estado = 1
            )
            THEN 'Si Cumple'

            /* 5. CUMPLE PARCIALMENTE: mezcla */
            ELSE 'Cumple Parcialmente'
        END AS cumplimiento_medio

    FROM tm_cbc c
    JOIN tm_componentes cp ON cp.cbc_id = c.cbc_id
    JOIN tm_indicadores i ON i.componente_id = cp.componente_id
    JOIN tm_medio_verificacion m ON m.indicador_id = i.indicador_id
    WHERE c.estado = 1 AND cp.estado = 1 AND m.estado = 1
    $filtroCond
    ORDER BY m.medio_id ASC";

        return ejecutarConsulta($sql);
    }
    public function resumenOficinaCumplimientoMV($oficina_id)
    {
        $sql = "SELECT 
                SUM(CASE WHEN grado_cumplimiento = 'Si Cumple' THEN 1 ELSE 0 END) AS si_cumple,
                SUM(CASE WHEN grado_cumplimiento = 'No Cumple' THEN 1 ELSE 0 END) AS no_cumple,
                SUM(CASE WHEN grado_cumplimiento = 'Cumple Parcialmente' THEN 1 ELSE 0 END) AS cumple_parcial,
                SUM(CASE WHEN grado_cumplimiento = 'Por Revisar' THEN 1 ELSE 0 END) AS pendiente
            FROM (
                SELECT 
                    CASE 
                        WHEN e.estado_revision IN ('Enviado', 'En Revisión') THEN 'Por Revisar'
                        ELSE IFNULL(gc.grado_nombre, 'Por Revisar')
                    END AS grado_cumplimiento
                FROM 
                    tm_evidencias e
                LEFT JOIN tm_evidencias_oficinas eo 
                    ON eo.evidencia_id = e.evidencia_id
                    AND eo.oficina_id = '$oficina_id'
                    AND eo.eliminado = 0
                LEFT JOIN tm_evidencias_coordinadores ec 
                    ON ec.evidencia_id = e.evidencia_id
                    AND ec.oficina_id = '$oficina_id'
                    AND ec.eliminado = 0
                LEFT JOIN tm_grado_cumplimiento gc 
                    ON e.grado_id = gc.grado_id
                WHERE 
                    e.estado = 1
                    AND (eo.evidencia_id IS NOT NULL OR ec.evidencia_id IS NOT NULL)
                GROUP BY e.evidencia_id
            ) AS subconsulta";

        return ejecutarConsulta($sql);
    }

    /*   public function resumenOficinaCumplimientoMV($oficina_id)
    {
        $sql = "SELECT 
                SUM(CASE WHEN grado_cumplimiento = 'Si Cumple' THEN 1 ELSE 0 END) AS si_cumple,
                SUM(CASE WHEN grado_cumplimiento = 'No Cumple' THEN 1 ELSE 0 END) AS no_cumple,
                SUM(CASE WHEN grado_cumplimiento = 'Cumple Parcialmente' THEN 1 ELSE 0 END) AS cumple_parcial,
                SUM(CASE WHEN grado_cumplimiento = 'Por Revisar' THEN 1 ELSE 0 END) AS pendiente
            FROM (
                SELECT 
                    CASE 
                        WHEN e.estado_revision IN ('Enviado', 'En Revisión') THEN 'Por Revisar'
                        ELSE IFNULL(gc.grado_nombre, 'Por Revisar')
                    END AS grado_cumplimiento
                FROM 
                    tm_evidencias e
                JOIN 
                    tm_evidencias_oficinas eo_sub ON eo_sub.evidencia_id = e.evidencia_id
                LEFT JOIN 
                    tm_grado_cumplimiento gc ON e.grado_id = gc.grado_id
                WHERE 
                    eo_sub.oficina_id = '$oficina_id' 
                    AND e.estado = 1 AND eo_sub.eliminado=0
            ) AS subconsulta";

        return ejecutarConsulta($sql);
    } */

    public function listarMediosCumplimientoPorOficina($oficina_id)
    {
        $sql = "
        SELECT 
            e.evidencia_nombre AS evidencia_nombre,
            e.estado_revision AS estado_revision,
            CASE 
                WHEN e.estado_revision IN ('Enviado', 'En Revisión') THEN 'Por Revisar'
                ELSE IFNULL(gc.grado_nombre, 'Por Revisar')
            END AS grado_cumplimiento
        FROM 
            tm_evidencias e
        LEFT JOIN tm_evidencias_oficinas eo
            ON eo.evidencia_id = e.evidencia_id
            AND eo.oficina_id = '$oficina_id'
            AND eo.eliminado = 0
        LEFT JOIN tm_evidencias_coordinadores ec
            ON ec.evidencia_id = e.evidencia_id
            AND ec.oficina_id = '$oficina_id'
            AND ec.eliminado = 0
        LEFT JOIN tm_grado_cumplimiento gc
            ON e.grado_id = gc.grado_id
        WHERE 
            e.estado = 1
            AND (eo.evidencia_id IS NOT NULL OR ec.evidencia_id IS NOT NULL)
        GROUP BY e.evidencia_id
    ";

        return ejecutarConsulta($sql);
    }
    /*     public function listarMediosCumplimientoPorOficina($oficina_id)
    {
        $sql = "
    SELECT 
        e.evidencia_nombre AS evidencia_nombre,
        e.estado_revision AS estado_revision,
        CASE 
            WHEN e.estado_revision IN ('Enviado', 'En Revisión') THEN 'Por Revisar'
            ELSE IFNULL(gc.grado_nombre, 'Por Revisar')
        END AS grado_cumplimiento
    FROM 
        tm_evidencias e
    JOIN 
        tm_evidencias_oficinas eo_sub ON eo_sub.evidencia_id = e.evidencia_id
    LEFT JOIN 
        tm_grado_cumplimiento gc ON e.grado_id = gc.grado_id
    WHERE 
        eo_sub.oficina_id = '$oficina_id' 
        AND e.estado = 1 AND eo_sub.eliminado=0
";


        return ejecutarConsulta($sql);
    } */
}
