<?php

require_once(__DIR__ . '/../config/conexion.php');


class Evidencia
{

    public function __construct() {}
    public function listar($oficina_id = '', $busqueda = '')
    {
        $filtro_oficina = "";
        $filtro_busqueda = "";

        // Filtro por oficina (en responsables o coordinadores)
        if (!empty($oficina_id)) {
            $filtro_oficina = " AND (
                EXISTS (
                    SELECT 1
                    FROM tm_evidencias_oficinas eo_sub
                    WHERE eo_sub.evidencia_id = e.evidencia_id
                    AND eo_sub.oficina_id = '$oficina_id'
                    AND eo_sub.eliminado = 0
                )
                OR 
                EXISTS (
                    SELECT 1
                    FROM tm_evidencias_coordinadores ec_sub
                    WHERE ec_sub.evidencia_id = e.evidencia_id
                    AND ec_sub.oficina_id = '$oficina_id'
                )
            ) ";
        }

        // Filtro por nombre de evidencia
        if (!empty($busqueda)) {
            $filtro_busqueda = " AND e.evidencia_nombre LIKE '%$busqueda%' ";
        }

        // Consulta SQL
        $sql = "SELECT 
                e.evidencia_id,
                e.evidencia_nombre,
                e.evidencia_consideraciones,
                e.fecha_plazo_inicio,
                e.fecha_plazo_fin,
                e.fecha_subsanacion,
                e.estado,
                e.fecha_registro,
                
                mv.medio_nombre,
                i.indicador_nombre,
                u.usu_nom,

                -- Responsables
                GROUP_CONCAT(DISTINCT o.oficina_nom SEPARATOR '<br>') AS oficinas_vinculadas,
                GROUP_CONCAT(DISTINCT o.oficina_id SEPARATOR ',') AS oficinas_vinculadas_ids,

                -- Coordinadores
                GROUP_CONCAT(DISTINCT oc.oficina_nom SEPARATOR '<br>') AS coordinadores_vinculados,
                GROUP_CONCAT(DISTINCT oc.oficina_id SEPARATOR ',') AS coordinadores_vinculados_ids

            FROM tm_evidencias e
            
            INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
            INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
            INNER JOIN tm_indicadores i ON mv.indicador_id = i.indicador_id

            LEFT JOIN tm_evidencias_oficinas eo ON e.evidencia_id = eo.evidencia_id AND eo.eliminado = 0
            LEFT JOIN tm_oficina o ON eo.oficina_id = o.oficina_id

            LEFT JOIN tm_evidencias_coordinadores ec ON e.evidencia_id = ec.evidencia_id AND ec.eliminado = 0
            LEFT JOIN tm_oficina oc ON ec.oficina_id = oc.oficina_id

            WHERE 1=1 
                $filtro_oficina 
                $filtro_busqueda 
                AND e.estado = 1

            GROUP BY e.evidencia_id
            ORDER BY e.fecha_registro DESC";

        return ejecutarConsulta($sql);
    }

    public function listarold($oficina_id = '', $busqueda = '')
    {
        $filtro_oficina = "";
        $filtro_busqueda = "";

        // Filtro por oficina 
        if (!empty($oficina_id)) {
            $filtro_oficina = " AND EXISTS (
            SELECT 1
            FROM tm_evidencias_oficinas eo_sub
            WHERE eo_sub.evidencia_id = e.evidencia_id
            AND eo_sub.oficina_id = '$oficina_id' AND eo_sub.eliminado=0  
        ) ";
        }

        // Filtro por el nombre de evidencia
        if (!empty($busqueda)) {
            $filtro_busqueda = " AND e.evidencia_nombre LIKE '%$busqueda%' ";
        }

        // Consulta SQL
        $sql = "SELECT e.evidencia_id,
                   e.evidencia_nombre,
                   e.evidencia_consideraciones,
                   e.fecha_plazo_inicio,
                   e.fecha_plazo_fin,
                   e.fecha_subsanacion,
                   e.estado,
                   e.fecha_registro,
                   mv.medio_nombre,
                   i.indicador_nombre,
                   u.usu_nom,
                   GROUP_CONCAT(DISTINCT o.oficina_nom SEPARATOR '<br>') AS oficinas_vinculadas,
                   GROUP_CONCAT(o.oficina_id SEPARATOR ',') AS oficinas_vinculadas_ids
            FROM tm_evidencias e
            INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
            INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
            INNER JOIN tm_indicadores i ON mv.indicador_id = i.indicador_id
            LEFT JOIN tm_evidencias_oficinas eo ON e.evidencia_id = eo.evidencia_id
            LEFT JOIN tm_oficina o ON eo.oficina_id = o.oficina_id
            WHERE 1=1 $filtro_oficina $filtro_busqueda AND e.estado= 1 AND eo.eliminado=0
            GROUP BY e.evidencia_id
            ORDER BY e.fecha_registro DESC";

        return ejecutarConsulta($sql);
    }

    /*  public function listar()
    {
        $sql = "SELECT e.evidencia_id,
               e.evidencia_nombre,
               e.evidencia_consideraciones,
               e.fecha_plazo_inicio,
               e.fecha_plazo_fin,
               e.fecha_subsanacion,
               e.estado,
               e.fecha_registro,
               mv.medio_nombre,
               u.usu_nom,
               GROUP_CONCAT(o.oficina_nom SEPARATOR '<br>') AS oficinas_vinculadas
        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
        LEFT JOIN tm_evidencias_oficinas eo ON e.evidencia_id = eo.evidencia_id
        LEFT JOIN tm_oficina o ON eo.oficina_id = o.oficina_id
        GROUP BY e.evidencia_id
        ORDER BY e.fecha_registro DESC";
        return ejecutarConsulta($sql);
    } */

    /*     public function registrarEvidencia($medio_id, $evidencia_nombre, $evidencia_consideraciones, $fecha_plazo_inicio, $fecha_plazo_fin, $oficinas)
    {
        $conexion = $GLOBALS['conexion'];
        mysqli_autocommit($conexion, FALSE);

        try {

            $usu_id = $_SESSION['usu_id'];

            $sql = "INSERT INTO tm_evidencias (medio_id, usu_id, evidencia_nombre, evidencia_consideraciones, fecha_plazo_inicio, fecha_plazo_fin)
                VALUES ('$medio_id', '$usu_id', '$evidencia_nombre', '$evidencia_consideraciones', '$fecha_plazo_inicio', '$fecha_plazo_fin')";

            $resultado = ejecutarConsulta($sql);

            if (!$resultado) {
                throw new Exception("Error al insertar la evidencia: " . mysqli_error($conexion));
            }

            $evidencia_id = $conexion->insert_id;

            foreach ($oficinas as $oficina_id) {
                $sql_oficina = "INSERT INTO tm_evidencias_oficinas (evidencia_id, oficina_id) VALUES ('$evidencia_id', '$oficina_id')";
                $resultado_oficina = ejecutarConsulta($sql_oficina);

                if (!$resultado_oficina) {
                    throw new Exception("Error al asociar la oficina con la evidencia: " . mysqli_error($conexion));
                }
            }

            foreach ($oficinas as $oficina_id) {

                $notificacion_sql = "INSERT INTO tm_notificaciones (usu_id, oficina_id,evidencia_id, mensaje, estado_leido)
                    VALUES (NULL, '$oficina_id','$evidencia_id', 'Nueva evidencia registrada: $evidencia_nombre', 0)";

                $resultado_notificacion = ejecutarConsulta($notificacion_sql);

                if (!$resultado_notificacion) {
                    throw new Exception("Error al registrar la notificación: " . mysqli_error($conexion));
                }
            }

            mysqli_commit($conexion);

            return ['success' => true, 'message' => 'Evidencia y notificación registradas con éxito.'];
        } catch (Exception $e) {

            mysqli_rollBack($conexion);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    } */

    public function registrarEvidencia($medio_id, $evidencia_nombre, $evidencia_consideraciones, $fecha_plazo_inicio, $fecha_plazo_fin, $oficinas, $coordinadores, $fecha_registro)
    {
        $conexion = $GLOBALS['conexion'];
        mysqli_autocommit($conexion, FALSE);

        try {

            $usu_id = $_SESSION['usu_id'];

            // 1. Insert principal
            $sql = "INSERT INTO tm_evidencias (medio_id, usu_id, evidencia_nombre, evidencia_consideraciones, fecha_plazo_inicio, fecha_plazo_fin, fecha_registro)
                VALUES ('$medio_id', '$usu_id', '$evidencia_nombre', '$evidencia_consideraciones', '$fecha_plazo_inicio', '$fecha_plazo_fin','$fecha_registro')";

            $resultado = ejecutarConsulta($sql);

            if (!$resultado) {
                throw new Exception("Error al insertar la evidencia: " . mysqli_error($conexion));
            }

            // ID válido
            $evidencia_id = $conexion->insert_id;

            // 2. Asignar oficinas responsables
            foreach ($oficinas as $ofi_id) {
                $sql_oficina = "INSERT INTO tm_evidencias_oficinas (evidencia_id, oficina_id) VALUES ('$evidencia_id', '$ofi_id')";
                if (!ejecutarConsulta($sql_oficina)) {
                    throw new Exception("Error al asociar la oficina como responsable.");
                }
            }

            // Notificaciones responsables
            foreach ($oficinas as $ofi_id) {
                $noti_sql = "INSERT INTO tm_notificaciones (usu_id, oficina_id, evidencia_id, mensaje, estado_leido, fecha_envio)
                    VALUES (NULL, '$ofi_id', '$evidencia_id', 'Nueva evidencia registrada como Responsable: $evidencia_nombre', 0, '$fecha_registro')";
                if (!ejecutarConsulta($noti_sql)) {
                    throw new Exception("Error al registrar notificación de responsable.");
                }
            }

            // 3. Asignar coordinadores
            foreach ($coordinadores as $coord_id) {
                $sql_coord = "INSERT INTO tm_evidencias_coordinadores (evidencia_id, oficina_id) VALUES ('$evidencia_id', '$coord_id')";
                if (!ejecutarConsulta($sql_coord)) {
                    throw new Exception("Error al asociar la oficina como coordinador.");
                }
            }

            // Notificaciones coordinadores
            foreach ($coordinadores as $coord_id) {
                $noti_sql = "INSERT INTO tm_notificaciones (usu_id, oficina_id, evidencia_id, mensaje, estado_leido, fecha_envio)
                    VALUES (NULL, '$coord_id', '$evidencia_id', 'Nueva evidencia registrada como Coordinador: $evidencia_nombre', 0, '$fecha_registro')";
                if (!ejecutarConsulta($noti_sql)) {
                    throw new Exception("Error al registrar notificación de coordinador.");
                }
            }

            mysqli_commit($conexion);

            return ['success' => true, 'message' => 'Evidencia registrada correctamente.'];
        } catch (Exception $e) {

            mysqli_rollBack($conexion);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function obtenerEvidencia($evidencia_id)
    {
        $sql = "SELECT evidencia_id, evidencia_nombre, evidencia_consideraciones, fecha_plazo_inicio, fecha_plazo_fin FROM tm_evidencias WHERE evidencia_id = '$evidencia_id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function obtenerOficinasPorEvidencia($evidencia_id)
    {
        $conexion = $GLOBALS['conexion'];
        $oficinas = [];

        $sql = "SELECT oficina_id FROM tm_evidencias_oficinas 
            WHERE evidencia_id = '$evidencia_id' AND eliminado = 0";

        $resultado = mysqli_query($conexion, $sql);

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $oficinas[] = $fila['oficina_id'];
        }

        return $oficinas;
    }

    public function obtenerCoordinadoresPorEvidencia($evidencia_id)
    {
        $conexion = $GLOBALS['conexion'];
        $coordinadores = [];

        $sql = "SELECT oficina_id FROM tm_evidencias_coordinadores 
            WHERE evidencia_id = '$evidencia_id' AND eliminado = 0";

        $resultado = mysqli_query($conexion, $sql);

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $coordinadores[] = $fila['oficina_id'];
        }

        return $coordinadores;
    }

    /*     public function actualizarEvidencia($evidencia_id, $evidencia_nombre, $evidencia_consideraciones, $fecha_plazo_inicio, $fecha_plazo_fin)
    {
        $sql = "UPDATE tm_evidencias SET evidencia_nombre = '$evidencia_nombre', evidencia_consideraciones ='$evidencia_consideraciones' , fecha_plazo_inicio ='$fecha_plazo_inicio', fecha_plazo_fin =' $fecha_plazo_fin' WHERE evidencia_id = '$evidencia_id'";
        return ejecutarConsulta($sql);
    } */

    public function actualizarEvidenciaOld($evidencia_id, $evidencia_nombre, $evidencia_consideraciones, $fecha_plazo_inicio, $fecha_plazo_fin, $oficinas, $fecha_registro)
    {
        $conexion = $GLOBALS['conexion'];
        mysqli_autocommit($conexion, FALSE);

        try {
            // 1. Actualizar evidencia
            $sql = "UPDATE tm_evidencias 
                SET evidencia_nombre = '$evidencia_nombre', 
                    evidencia_consideraciones = '$evidencia_consideraciones', 
                    fecha_plazo_inicio = '$fecha_plazo_inicio', 
                    fecha_plazo_fin = '$fecha_plazo_fin' 
                WHERE evidencia_id = '$evidencia_id'";
            if (!ejecutarConsulta($sql)) {
                throw new Exception("Error al actualizar la evidencia.");
            }

            // 2. Eliminar oficinas anteriores
            $sql = "UPDATE tm_evidencias_oficinas SET eliminado = 1 WHERE evidencia_id = '$evidencia_id'";
            if (!ejecutarConsulta($sql)) {
                throw new Exception("Error al eliminar oficinas anteriores.");
            }

            // 3. Eliminar notificaciones anteriores
            $sql = "UPDATE tm_notificaciones SET eliminado = 1 WHERE evidencia_id = '$evidencia_id'";
            if (!ejecutarConsulta($sql)) {
                throw new Exception("Error al eliminar notificaciones anteriores.");
            }

            // 4. Insertar nuevas oficinas y notificaciones
            foreach ($oficinas as $oficina_id) {
                $oficina_id = trim($oficina_id); // prevenir espacios o errores
                if ($oficina_id === '') continue; // saltar vacíos

                // Registrar oficina
                $sql = "INSERT INTO tm_evidencias_oficinas (evidencia_id, oficina_id, eliminado) 
                    VALUES ('$evidencia_id', '$oficina_id', 0)";
                if (!ejecutarConsulta($sql)) {
                    throw new Exception("Error al insertar oficina $oficina_id.");
                }

                // Registrar notificación
                $mensaje = "Nueva evidencia registrada: $evidencia_nombre";
                $sql = "INSERT INTO tm_notificaciones 
                    (usu_id, oficina_id, evidencia_id, mensaje, estado_leido, fecha_envio, eliminado)
                    VALUES (NULL, '$oficina_id', '$evidencia_id', '$mensaje', 0, '$fecha_registro', 0)";
                if (!ejecutarConsulta($sql)) {
                    throw new Exception("Error al insertar notificación para oficina $oficina_id.");
                }
            }

            mysqli_commit($conexion);
            return ['success' => true, 'message' => 'Evidencia actualizada correctamente.'];
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function actualizarEvidencia($evidencia_id, $evidencia_nombre, $evidencia_consideraciones, $fecha_plazo_inicio, $fecha_plazo_fin, $oficinas, $coordinadores, $fecha_registro)
    {
        $conexion = $GLOBALS['conexion'];
        mysqli_autocommit($conexion, FALSE);

        try {

            /* ======================================================
           1. ACTUALIZAR DATOS PRINCIPALES DE LA EVIDENCIA
        ====================================================== */
            $sql = "UPDATE tm_evidencias 
                SET evidencia_nombre = '$evidencia_nombre', 
                    evidencia_consideraciones = '$evidencia_consideraciones', 
                    fecha_plazo_inicio = '$fecha_plazo_inicio', 
                    fecha_plazo_fin = '$fecha_plazo_fin'
                WHERE evidencia_id = '$evidencia_id'";

            if (!ejecutarConsulta($sql)) {
                throw new Exception("Error al actualizar la evidencia.");
            }

            /* ======================================================
           2. MARCAR COMO ELIMINADO LAS OFICINAS Y COORDINADORES PREVIOS
        ====================================================== */
            $sql = "UPDATE tm_evidencias_oficinas SET eliminado = 1 WHERE evidencia_id = '$evidencia_id'";
            if (!ejecutarConsulta($sql)) {
                throw new Exception("Error al eliminar oficinas anteriores.");
            }

            $sql = "UPDATE tm_evidencias_coordinadores SET eliminado = 1 WHERE evidencia_id = '$evidencia_id'";
            if (!ejecutarConsulta($sql)) {
                throw new Exception("Error al eliminar coordinadores anteriores.");
            }

            /* ======================================================
           3. MARCAR NOTIFICACIONES ANTERIORES COMO ELIMINADAS
        ====================================================== */
            $sql = "UPDATE tm_notificaciones SET eliminado = 1 WHERE evidencia_id = '$evidencia_id'";
            if (!ejecutarConsulta($sql)) {
                throw new Exception("Error al eliminar notificaciones anteriores.");
            }

            /* ======================================================
           4. INSERTAR NUEVAS OFICINAS Y NOTIFICACIONES
        ====================================================== */
            foreach ($oficinas as $ofi_id) {

                $ofi_id = trim($ofi_id);
                if ($ofi_id === '') continue;

                // Insertar oficina
                $sql = "INSERT INTO tm_evidencias_oficinas (evidencia_id, oficina_id, eliminado)
                    VALUES ('$evidencia_id', '$ofi_id', 0)";

                if (!ejecutarConsulta($sql)) {
                    throw new Exception("Error al insertar la oficina $ofi_id.");
                }

                // Notificación
                $mensaje = "Nueva evidencia registrada como Responsable: $evidencia_nombre";

                $sql = "INSERT INTO tm_notificaciones 
                    (usu_id, oficina_id, evidencia_id, mensaje, estado_leido, fecha_envio, eliminado)
                    VALUES (NULL, '$ofi_id', '$evidencia_id', '$mensaje', 0, '$fecha_registro', 0)";

                if (!ejecutarConsulta($sql)) {
                    throw new Exception("Error al insertar notificación para oficina $ofi_id.");
                }
            }

            /* ======================================================
           5. INSERTAR NUEVOS COORDINADORES Y NOTIFICACIONES
        ====================================================== */
            foreach ($coordinadores as $coord_id) {

                $coord_id = trim($coord_id);
                if ($coord_id === '') continue;

                // Insertar coordinador
                $sql = "INSERT INTO tm_evidencias_coordinadores (evidencia_id, oficina_id, eliminado)
                    VALUES ('$evidencia_id', '$coord_id', 0)";

                if (!ejecutarConsulta($sql)) {
                    throw new Exception("Error al insertar coordinador $coord_id.");
                }

                // Notificación
                $mensaje = "Nueva evidencia registrada como Coordinador: $evidencia_nombre";

                $sql = "INSERT INTO tm_notificaciones 
                    (usu_id, oficina_id, evidencia_id, mensaje, estado_leido, fecha_envio, eliminado)
                    VALUES (NULL, '$coord_id', '$evidencia_id', '$mensaje', 0, '$fecha_registro', 0)";

                if (!ejecutarConsulta($sql)) {
                    throw new Exception("Error al insertar notificación del coordinador $coord_id.");
                }
            }

            mysqli_commit($conexion);
            return ['success' => true, 'message' => 'Evidencia actualizada correctamente.'];
        } catch (Exception $e) {

            mysqli_rollback($conexion);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function actualizarEvidenciaPorGrupo($evidencia_id, $fecha_plazo_inicio, $fecha_plazo_fin)
    {
        $campos = [];
        if (!empty($fecha_plazo_inicio)) {
            $campos[] = "fecha_plazo_inicio = '$fecha_plazo_inicio'";
        }
        if (!empty($fecha_plazo_fin)) {
            $campos[] = "fecha_plazo_fin = '$fecha_plazo_fin'";
        }

        if (count($campos) === 0) {
            return false; // nada que actualizar
        }

        $sql = "UPDATE tm_evidencias 
            SET " . implode(", ", $campos) . "
            WHERE evidencia_id = '$evidencia_id'";

        return ejecutarConsulta($sql);
    }
    public function listarTodosIds($oficina_id = null, $busqueda = null)
    {
        $sql = "SELECT evidencia_id 
            FROM tm_evidencias 
            WHERE estado = 1";

        if (!empty($oficina_id)) {
            $sql .= " AND oficina_id = '$oficina_id'";
        }

        if (!empty($busqueda)) {
            $sql .= " AND evidencia_nombre LIKE '%$busqueda%'";
        }

        $rspta = ejecutarConsulta($sql);

        $ids = [];
        while ($row = $rspta->fetch_assoc()) {
            $ids[] = $row["evidencia_id"];
        }

        return $ids;
    }
    public function desactivarEvidencia($evidencia_id)
    {

        $conexion = $GLOBALS['conexion'];

        // Deshabilitar el autocommit para iniciar la transacción
        mysqli_autocommit($conexion, FALSE);

        try {
            // Actualizar tm_evidencias (estado = 0)
            $sql1 = "UPDATE tm_evidencias SET estado = 0 WHERE evidencia_id = ?";
            $stmt1 = $conexion->prepare($sql1);
            $stmt1->bind_param("i", $evidencia_id);
            if (!$stmt1->execute()) {
                throw new Exception('Error al desactivar la evidencia en tm_evidencias');
            }

            // Actualizar tm_evidencias_historial (eliminado = 1)
            $sql2 = "UPDATE tm_evidencias_historial SET eliminado = 1 WHERE evidencia_id = ?";
            $stmt2 = $conexion->prepare($sql2);
            $stmt2->bind_param("i", $evidencia_id);
            if (!$stmt2->execute()) {
                throw new Exception('Error al actualizar tm_evidencias_historial');
            }

            // Actualizar tm_notificaciones (eliminado = 1)
            $sql3 = "UPDATE tm_notificaciones SET eliminado = 1 WHERE evidencia_id = ?";
            $stmt3 = $conexion->prepare($sql3);
            $stmt3->bind_param("i", $evidencia_id);
            if (!$stmt3->execute()) {
                throw new Exception('Error al actualizar tm_notificaciones');
            }

            // Actualizar tm_evidencias_oficinas (eliminado = 1)
            $sql4 = "UPDATE tm_evidencias_oficinas SET eliminado = 1 WHERE evidencia_id = ?";
            $stmt4 = $conexion->prepare($sql4);
            $stmt4->bind_param("i", $evidencia_id);
            if (!$stmt4->execute()) {
                throw new Exception('Error al actualizar tm_evidencias_oficinas');
            }

            // Si todas las consultas fueron exitosas, hacer commit
            $conexion->commit();  // Confirma cambios
            return true;
        } catch (Exception $e) {
            // Si ocurre un error, hacer rollback y mostrar el error
            $conexion->rollback();
            // Retorna false si la transacción falla
            return false;
        } finally {

            mysqli_autocommit($conexion, TRUE);
        }
    }
    public function listarTodasEvidencias($oficina_id, $estado)
    {
        $sql = "SELECT 
                e.evidencia_id,
                e.evidencia_nombre,
                e.evidencia_consideraciones,
                e.fecha_plazo_inicio,
                e.fecha_plazo_fin,
                e.fecha_subsanacion,
                e.estado,
                e.fecha_registro,
                mv.medio_nombre,
                e.estado_revision,
                CONCAT(u.usu_nom, ' ', u.usu_ape) AS nomcompleto,
                (
                    SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                    FROM tm_evidencias_oficinas eo2
                    INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
                    WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
                ) AS oficinas_vinculadas,
                (
                    SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                    FROM tm_evidencias_coordinadores ec
                    INNER JOIN tm_oficina o2 ON ec.oficina_id = o2.oficina_id
                    WHERE ec.evidencia_id = e.evidencia_id AND ec.eliminado = 0
                ) AS coordinadores_vinculados

            FROM tm_evidencias e
            INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
            INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
            WHERE 1 = 1 AND e.estado=1";

        // Si se ha enviado oficina_id, añadir filtro
        if (!empty($oficina_id)) {
            $sql .= " AND (
        EXISTS (
            SELECT 1 
            FROM tm_evidencias_oficinas eo_sub
            WHERE eo_sub.evidencia_id = e.evidencia_id
            AND eo_sub.oficina_id = '$oficina_id'
            AND eo_sub.eliminado = 0
        )
        OR
        EXISTS (
            SELECT 1 
            FROM tm_evidencias_coordinadores ec_sub
            WHERE ec_sub.evidencia_id = e.evidencia_id
            AND ec_sub.oficina_id = '$oficina_id'
            AND ec_sub.eliminado = 0
            )
        )";
        }

        // Si se ha enviado estado, añadir filtro
        if (!empty($estado)) {
            $estadoArr = explode(',', $estado);
            $estadoCondicion = implode("','", array_map('trim', $estadoArr));
            $sql .= " AND e.estado_revision IN ('$estadoCondicion')";
        }

        $sql .= " ORDER BY e.fecha_plazo_fin DESC";

        return ejecutarConsulta($sql);
    }
    public function listarPorOficina($oficina_id, $estado)
    {
        $sql = "SELECT 
            e.evidencia_id,
            e.evidencia_nombre,
            e.evidencia_consideraciones,
            e.fecha_plazo_inicio,
            e.fecha_plazo_fin,
            e.fecha_subsanacion,
            e.estado,
            e.fecha_registro,
            mv.medio_nombre,
            e.estado_revision,
            CONCAT(u.usu_nom, ' ', u.usu_ape) AS nomcompleto,

            (
                SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                FROM tm_evidencias_oficinas eo2
                INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
                WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
            ) AS oficinas_vinculadas,

            (
                SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                FROM tm_evidencias_coordinadores ec
                INNER JOIN tm_oficina o2 ON ec.oficina_id = o2.oficina_id
                WHERE ec.evidencia_id = e.evidencia_id AND ec.eliminado=0
            ) AS coordinadores_vinculados,

            (
                SELECT CASE
                    WHEN EXISTS (
                        SELECT 1 FROM tm_evidencias_oficinas eo
                        WHERE eo.evidencia_id = e.evidencia_id
                        AND eo.oficina_id = '$oficina_id'
                        AND eo.eliminado = 0
                    ) THEN 'responsable'

                    WHEN EXISTS (
                        SELECT 1 FROM tm_evidencias_coordinadores ec
                        WHERE ec.evidencia_id = e.evidencia_id
                        AND ec.oficina_id = '$oficina_id'
                        AND ec.eliminado = 0
                    ) THEN 'coordinador'
                END
            ) AS rol_oficina

        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id

        WHERE 
            (
                EXISTS (
                    SELECT 1 
                    FROM tm_evidencias_oficinas eo_sub
                    WHERE eo_sub.evidencia_id = e.evidencia_id
                    AND eo_sub.oficina_id = '$oficina_id' 
                    AND eo_sub.eliminado = 0
                )
                OR
                EXISTS (
                    SELECT 1 
                    FROM tm_evidencias_coordinadores ec_sub
                    WHERE ec_sub.evidencia_id = e.evidencia_id
                    AND ec_sub.oficina_id = '$oficina_id'
                    AND ec_sub.eliminado = 0
                )
            )

            AND e.estado = 1
        ";

        // Verificar si el estado no está vacío y permitir múltiples estados
        if ($estado !== '') {
            // Si el estado contiene varios valores, realizar una búsqueda con `IN`
            $estadoArr = explode(',', $estado);
            $estadoCondicion = implode("', '", $estadoArr);
            $sql .= " AND e.estado_revision IN ('$estadoCondicion')";
        }

        $sql .= " ORDER BY e.fecha_plazo_fin DESC";
        return ejecutarConsulta($sql);
    }

    /*     public function listarPorOficina($oficina_id, $estado)
    {
        $sql = "SELECT 
            e.evidencia_id,
            e.evidencia_nombre,
            e.evidencia_consideraciones,
            e.fecha_plazo_inicio,
            e.fecha_plazo_fin,
            e.fecha_subsanacion,
            e.estado,
            e.fecha_registro,
            mv.medio_nombre,
            e.estado_revision,
            CONCAT(u.usu_nom, ' ', u.usu_ape) AS nomcompleto,
            (
                SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                FROM tm_evidencias_oficinas eo2
                INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
                WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
            ) AS oficinas_vinculadas
        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
        WHERE EXISTS (
            SELECT 1 
            FROM tm_evidencias_oficinas eo_sub
            WHERE eo_sub.evidencia_id = e.evidencia_id
            AND eo_sub.oficina_id = '$oficina_id' AND eo_sub.eliminado=0
        )
        AND e.estado = 1
        ";

        // Verificar si el estado no está vacío y permitir múltiples estados
        if ($estado !== '') {
            // Si el estado contiene varios valores, realizar una búsqueda con `IN`
            $estadoArr = explode(',', $estado);
            $estadoCondicion = implode("', '", $estadoArr);
            $sql .= " AND e.estado_revision IN ('$estadoCondicion')";
        }

        $sql .= " ORDER BY e.fecha_plazo_fin DESC";
        return ejecutarConsulta($sql);
    } */
    public function listarPorOficinaPendientes($oficina_id)
    {
        $sql = "SELECT 
            e.evidencia_id,
            e.evidencia_nombre,
            e.evidencia_consideraciones,
            e.fecha_plazo_inicio,
            e.fecha_plazo_fin,
            e.fecha_subsanacion,
            e.estado,
            e.fecha_registro,
            mv.medio_nombre,
            e.estado_revision,
            CONCAT(u.usu_nom, ' ', u.usu_ape) AS 'nomcompleto',
            (
                SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                FROM tm_evidencias_oficinas eo2
                INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
                WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
            ) AS oficinas_vinculadas,
            (
                SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                FROM tm_evidencias_coordinadores ec
                INNER JOIN tm_oficina o2 ON ec.oficina_id = o2.oficina_id
                WHERE ec.evidencia_id = e.evidencia_id AND ec.eliminado=0
            ) AS coordinadores_vinculados,
            (
            SELECT 
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM tm_evidencias_oficinas eo 
                        WHERE eo.evidencia_id = e.evidencia_id 
                        AND eo.oficina_id = '$oficina_id' 
                        AND eo.eliminado = 0
                    )
                    THEN 'responsable'
                    WHEN EXISTS (
                        SELECT 1 FROM tm_evidencias_coordinadores ec
                        WHERE ec.evidencia_id = e.evidencia_id 
                        AND ec.oficina_id = '$oficina_id'
                        AND ec.eliminado = 0
                    )
                    THEN 'coordinador'
                END
        ) AS rol_oficina
        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
        WHERE EXISTS (
            SELECT 1
            FROM tm_evidencias_oficinas eo_sub
            WHERE eo_sub.evidencia_id = e.evidencia_id
            AND eo_sub.oficina_id = '$oficina_id' AND eo_sub.eliminado=0
            AND (e.estado_revision = 'Pendiente' OR e.estado_revision = 'Observado')
        )
        OR EXISTS (
        SELECT 1
        FROM tm_evidencias_coordinadores ec_sub
        WHERE ec_sub.evidencia_id = e.evidencia_id
        AND ec_sub.oficina_id = '$oficina_id'
        AND ec_sub.eliminado = 0
        AND (e.estado_revision = 'Pendiente' OR e.estado_revision = 'Observado')
        )
        AND e.estado = 1 
        ORDER BY e.fecha_plazo_fin DESC;
        ";

        return ejecutarConsulta($sql);
    }
    /*     public function listarPorOficinaPendientes($oficina_id)
    {
        $sql = "SELECT 
            e.evidencia_id,
            e.evidencia_nombre,
            e.evidencia_consideraciones,
            e.fecha_plazo_inicio,
            e.fecha_plazo_fin,
            e.fecha_subsanacion,
            e.estado,
            e.fecha_registro,
            mv.medio_nombre,
            e.estado_revision,
            CONCAT(u.usu_nom, ' ', u.usu_ape) AS 'nomcompleto',
            (
                SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                FROM tm_evidencias_oficinas eo2
                INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
                WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
            ) AS oficinas_vinculadas
        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
        WHERE EXISTS (
            SELECT 1
            FROM tm_evidencias_oficinas eo_sub
            WHERE eo_sub.evidencia_id = e.evidencia_id
            AND eo_sub.oficina_id = '$oficina_id' AND eo_sub.eliminado=0
            AND (e.estado_revision = 'Pendiente' OR e.estado_revision = 'Observado')
        )
        AND e.estado = 1 
        ORDER BY e.fecha_plazo_fin DESC;
        ";

        return ejecutarConsulta($sql);
    } */
    /* 
    public function listarNotificacionesPorOficina($oficina_id)
    {
        $sql = "SELECT n.noti_id, n.mensaje, n.fecha_envio, n.oficina_id, n.usu_id FROM tm_notificaciones n WHERE n.oficina_id ='$oficina_id' AND n.usu_id IS NULL AND n.eliminado= 0 ORDER BY n.fecha_envio DESC;";

        return ejecutarConsulta($sql);
    } */
    public function listarNotificacionesPorOficina($oficina_id)
    {
        $sql = "SELECT n.noti_id, n.mensaje, n.fecha_envio, n.oficina_id, n.usu_id, estado_leido FROM tm_notificaciones n WHERE n.oficina_id ='$oficina_id' AND eliminado=0 ORDER BY n.fecha_envio DESC;";
        return ejecutarConsulta($sql);
    }

    public function marcarNotificacionComoLeida($noti_id, $usu_id, $fecha_lectura)
    {

        $sql = "UPDATE tm_notificaciones 
            SET estado_leido = 1, usu_id = '$usu_id', fecha_lectura = '$fecha_lectura' 
            WHERE noti_id = '$noti_id'";

        return ejecutarConsulta($sql);
    }

    public function listarEvidenciaIdPorOficina($evidencia_id, $oficina_id)
    {
        $sql = "SELECT 
            e.evidencia_id,
            e.evidencia_nombre,
            e.evidencia_consideraciones,
            e.fecha_plazo_inicio,
            e.fecha_plazo_fin,
            e.fecha_subsanacion,
            e.estado,
            e.fecha_registro,
            mv.medio_nombre,
            mi.indicador_nombre,
            e.estado_revision,
            gc.grado_nombre,
            CONCAT(u.usu_nom, ' ', u.usu_ape) AS nomcompleto,

            ( SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
              FROM tm_evidencias_oficinas eo2 
              INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
              WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
            ) AS oficinas_vinculadas,

            ( SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
              FROM tm_evidencias_coordinadores ec 
              INNER JOIN tm_oficina o2 ON ec.oficina_id = o2.oficina_id
              WHERE ec.evidencia_id = e.evidencia_id AND ec.eliminado=0
            ) AS coordinadores_vinculados,

            (
                SELECT CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM tm_evidencias_oficinas eo 
                        WHERE eo.evidencia_id = e.evidencia_id 
                        AND eo.oficina_id = '$oficina_id' 
                        AND eo.eliminado = 0
                    ) THEN 'responsable'
                    WHEN EXISTS (
                        SELECT 1 FROM tm_evidencias_coordinadores ec
                        WHERE ec.evidencia_id = e.evidencia_id 
                        AND ec.oficina_id = '$oficina_id'
                        AND ec.eliminado = 0
                    ) THEN 'coordinador'
                END
            ) AS rol_oficina

        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_indicadores mi ON mv.indicador_id = mi.indicador_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
        LEFT JOIN tm_grado_cumplimiento gc ON gc.grado_id = e.grado_id

        WHERE e.evidencia_id = '$evidencia_id'
        AND (
            EXISTS (
                SELECT 1 FROM tm_evidencias_oficinas eo
                WHERE eo.evidencia_id = e.evidencia_id 
                AND eo.oficina_id = '$oficina_id'
                AND eo.eliminado = 0
            )
            OR
            EXISTS (
                SELECT 1 FROM tm_evidencias_coordinadores ec
                WHERE ec.evidencia_id = e.evidencia_id
                AND ec.oficina_id = '$oficina_id'
                AND ec.eliminado = 0
            )
        )

        LIMIT 1";

        return ejecutarConsulta($sql);
    }
    /*     public function listarEvidenciaIdPorOficina($evidencia_id, $oficina_id)
    {
        $sql = "SELECT 
            e.evidencia_id,
            e.evidencia_nombre,
            e.evidencia_consideraciones,
            e.fecha_plazo_inicio,
            e.fecha_plazo_fin,
            e.fecha_subsanacion,
            e.estado,
            e.fecha_registro,
            mv.medio_nombre,
            mi.indicador_nombre,
            e.estado_revision,
            gc.grado_nombre,
            CONCAT(u.usu_nom, ' ', u.usu_ape) AS nomcompleto,
            (
                SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                FROM tm_evidencias_oficinas eo2
                INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
                WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
            ) AS oficinas_vinculadas
        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_indicadores mi ON mv.indicador_id = mi.indicador_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
        INNER JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id
        LEFT JOIN tm_grado_cumplimiento gc ON gc.grado_id = e.grado_id
        WHERE e.evidencia_id = '$evidencia_id' AND eo.oficina_id = '$oficina_id'
        LIMIT 1";

        return ejecutarConsulta($sql);
    } */


    public function registrarHistorialYEvidencia($evidencia_id, $usu_emisor_id, $oficina_origen_id, $oficina_destino_id, $archivo_presentacion, $fecha_emision)
    {
        $conexion = $GLOBALS['conexion'];
        mysqli_autocommit($conexion, FALSE); // Iniciar transacción

        try {
            // 1. Insertar historial
            $sql1 = "INSERT INTO tm_evidencias_historial (
                    evidencia_id, usu_emisor_id, oficina_origen_id, oficina_destino_id, estado2, archivo_presentacion, fecha_emision
                ) VALUES (
                    '$evidencia_id', '$usu_emisor_id', '$oficina_origen_id', '$oficina_destino_id', 'Sin Recibir', '$archivo_presentacion', '$fecha_emision'
                )";

            if (!$conexion->query($sql1)) {
                throw new Exception("Error al registrar historial: " . $conexion->error);
            }

            // 2. Obtener el ID del historial insertado
            $historial_id = $conexion->insert_id;

            // 3. Actualizar estado de la evidencia
            $sql2 = "UPDATE tm_evidencias SET estado_revision = 'Enviado' WHERE evidencia_id = '$evidencia_id'";
            if (!$conexion->query($sql2)) {
                throw new Exception("Error al actualizar evidencia: " . $conexion->error);
            }

            // 4. Obtener correo del usuario emisor y su oficina
            $sqlUsuario = "
            SELECT 
                u.usu_correo AS correo,
                u.oficina_id AS oficina_id,
                o.oficina_nom AS oficina_nombre
            FROM tm_usuario u
            INNER JOIN tm_oficina o ON u.oficina_id = o.oficina_id
            WHERE u.usu_id = '$usu_emisor_id'
        ";
            $resUsuario = $conexion->query($sqlUsuario);
            if (!$resUsuario || $resUsuario->num_rows === 0) {
                throw new Exception("No se encontró al usuario con ID $usu_emisor_id");
            }
            $usuario = $resUsuario->fetch_assoc();

            // 5. Obtener nombre de la evidencia
            $sqlEvidencia = "SELECT evidencia_nombre AS nombre FROM tm_evidencias WHERE evidencia_id = '$evidencia_id'";
            $resEvidencia = $conexion->query($sqlEvidencia);
            if (!$resEvidencia || $resEvidencia->num_rows === 0) {
                throw new Exception("No se encontró la evidencia con ID $evidencia_id");
            }
            $evidencia = $resEvidencia->fetch_assoc();

            // 6. Confirmar la transacción
            mysqli_commit($conexion);

            return [
                'success' => true,
                'historial_id' => $historial_id,
                'usuario' => $usuario,         // contiene correo, oficina_id y oficina_nombre
                'evidencia' => $evidencia      // contiene nombre
            ];
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            error_log($e->getMessage());
            return [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }
    }

    /* public function registrarHistorial($evidencia_id, $usu_emisor_id, $oficina_origen_id, $oficina_destino_id, $archivo_presentacion, $fecha_emision)
    {
        $sql = "INSERT INTO tm_evidencias_historial(
                evidencia_id, usu_emisor_id, oficina_origen_id, oficina_destino_id, estado2, archivo_presentacion, fecha_emision
            ) VALUES (
                '$evidencia_id', '$usu_emisor_id', '$oficina_origen_id', '$oficina_destino_id', 'Sin Recibir', '$archivo_presentacion', '$fecha_emision'
            )";
        return ejecutarConsulta($sql);
    }

    public function actualizarEstadoEvidencia($evidencia_id)
    {

        $sql = "UPDATE tm_evidencias SET estado_revision = 'Enviado' WHERE evidencia_id = '$evidencia_id'";
        return ejecutarConsulta($sql);
    } */
    public function mostrarHistorialEvidencia($evidencia_id)
    {
        $sql = "SELECT 
        h.historial_id, 
        h.evidencia_id, 
        h.usu_emisor_id, 
        h.usu_receptor_id, 
        h.cumplimiento, 
        h.observaciones, 
        h.archivo_presentacion, 
        h.archivo_observacion, 
        h.fecha_emision, 
        h.fecha_recepcion, 
        h.fecha_revision,
        h.estado2,

        u_emisor.usu_nom AS emisor_nom, 
        u_emisor.usu_ape AS emisor_ape, 
        o_emisor.oficina_nom AS emisor_oficina,

        u_receptor.usu_nom AS receptor_nom, 
        u_receptor.usu_ape AS receptor_ape, 
        o_receptor.oficina_nom AS receptor_oficina,

        e.estado_revision,
        e.grado_id

    FROM 
        tm_evidencias_historial h

    LEFT JOIN 
        tm_usuario u_emisor ON h.usu_emisor_id = u_emisor.usu_id
    LEFT JOIN 
        tm_oficina o_emisor ON h.oficina_origen_id = o_emisor.oficina_id

    LEFT JOIN 
        tm_usuario u_receptor ON h.usu_receptor_id = u_receptor.usu_id
    LEFT JOIN 
        tm_oficina o_receptor ON h.oficina_destino_id = o_receptor.oficina_id

    LEFT JOIN 
        tm_evidencias e ON e.evidencia_id = h.evidencia_id

    WHERE 
        h.evidencia_id = '$evidencia_id';
    ";

        return ejecutarConsulta($sql);
    }


    /* Rol Evaluador */
    /* public function listarEvidenciasPorRecibir($oficina_id = null)
{
    $sql = "SELECT 
        e.evidencia_id,
        e.evidencia_nombre,
        e.evidencia_consideraciones,
        e.fecha_plazo_inicio,
        e.fecha_plazo_fin,
        e.fecha_subsanacion,
        e.estado,
        e.fecha_registro,
        mv.medio_nombre,
        e.estado_revision,
        h.historial_id,
        h.fecha_emision,
        CONCAT(u.usu_nom,' ',u.usu_ape) AS nomcompleto,
        CONCAT(ue.usu_nom, ' ', ue.usu_ape) AS emisor_nombre,
        o.oficina_nom AS oficina_origen,

        (
            SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>') 
            FROM tm_evidencias_oficinas eo2
            INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
            WHERE eo2.evidencia_id = e.evidencia_id
        ) AS oficinas_vinculadas

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
        AND h.estado2 = 'Sin Recibir'
    )";

    // Si se pasa un `oficina_id`, filtramos las evidencias por la oficina
    if ($oficina_id) {
        $sql .= " AND eo.oficina_id = " . $oficina_id;  // Asegúrate de usar el alias correcto
    }

    $sql .= " ORDER BY e.fecha_plazo_fin DESC";

    return ejecutarConsulta($sql);
} */
    public function listarEvidenciasPorRecibir($oficina_id = null)
    {
        $sql = "SELECT 
        e.evidencia_id,
        e.evidencia_nombre,
        e.evidencia_consideraciones,
        e.fecha_plazo_inicio,
        e.fecha_plazo_fin,
        e.fecha_subsanacion,
        e.estado,
        e.fecha_registro,
        mv.medio_nombre,
        e.estado_revision,
        h.historial_id,
        h.fecha_emision,
        CONCAT(u.usu_nom,' ',u.usu_ape) AS nomcompleto,
        CONCAT(ue.usu_nom, ' ', ue.usu_ape) AS emisor_nombre,
        o.oficina_nom AS oficina_origen,

        (SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
         FROM tm_evidencias_oficinas eo2
         INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
         WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
        ) AS oficinas_vinculadas,

        (SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
         FROM tm_evidencias_coordinadores ec
         INNER JOIN tm_oficina o2 ON ec.oficina_id = o2.oficina_id
         WHERE ec.evidencia_id = e.evidencia_id AND ec.eliminado=0
        ) AS coordinadores_vinculados

        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
        LEFT JOIN tm_evidencias_historial h ON h.evidencia_id = e.evidencia_id
        LEFT JOIN tm_usuario ue ON h.usu_emisor_id = ue.usu_id
        LEFT JOIN tm_oficina o ON h.oficina_origen_id = o.oficina_id
        ";

        $sql .= " WHERE e.estado_revision = 'Enviado'
              AND h.estado2 = 'Sin Recibir' AND e.estado=1";

        if (!empty($oficina_id)) {
            $sql .= " AND (
                            -- Responsable
                            EXISTS (
                                SELECT 1 FROM tm_evidencias_oficinas eo_sub
                                WHERE eo_sub.evidencia_id = e.evidencia_id
                                AND eo_sub.oficina_id = '$oficina_id'
                                AND eo_sub.eliminado = 0
                            )
                            OR
                            -- Coordinador
                            EXISTS (
                                SELECT 1 FROM tm_evidencias_coordinadores ec_sub
                                WHERE ec_sub.evidencia_id = e.evidencia_id
                                AND ec_sub.oficina_id = '$oficina_id'
                                AND ec_sub.eliminado = 0
                            )
                        )";
        }

        $sql .= " ORDER BY h.fecha_emision ASC";

        return ejecutarConsulta($sql);
    }
    public function listarEvidenciasPorRecibirold($oficina_id = null)
    {
        $sql = "SELECT 
        e.evidencia_id,
        e.evidencia_nombre,
        e.evidencia_consideraciones,
        e.fecha_plazo_inicio,
        e.fecha_plazo_fin,
        e.fecha_subsanacion,
        e.estado,
        e.fecha_registro,
        mv.medio_nombre,
        e.estado_revision,
        h.historial_id,
        h.fecha_emision,
        CONCAT(u.usu_nom,' ',u.usu_ape) AS nomcompleto,
        CONCAT(ue.usu_nom, ' ', ue.usu_ape) AS emisor_nombre,
        o.oficina_nom AS oficina_origen,

        (
            SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>') 
            FROM tm_evidencias_oficinas eo2
            INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
            WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
        ) AS oficinas_vinculadas

    FROM tm_evidencias e
    INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
    INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
    LEFT JOIN tm_evidencias_historial h ON h.evidencia_id = e.evidencia_id
    LEFT JOIN tm_usuario ue ON h.usu_emisor_id = ue.usu_id
    LEFT JOIN tm_oficina o ON h.oficina_origen_id = o.oficina_id";

        // Agregar WHERE EXISTS con la subconsulta condicional para el filtro de oficina_id
        $sql .= " WHERE e.estado=1 AND EXISTS (
        SELECT 1 
        FROM tm_evidencias_oficinas eo_sub
        WHERE eo_sub.evidencia_id = e.evidencia_id
        AND e.estado_revision = 'Enviado' 
        AND h.estado2 = 'Sin Recibir' AND eo_sub.eliminado=0";

        // Condición condicional para el filtro de oficina_id
        if ($oficina_id) {
            $sql .= " AND eo_sub.oficina_id = " . $oficina_id;  // Solo se agrega si oficina_id está definido
        }

        // Cerrar la subconsulta EXISTS
        $sql .= ")";

        // Ordenar por la fecha de plazo final
        $sql .= " ORDER BY h.fecha_emision ASC";

        return ejecutarConsulta($sql);
    }

    /*     public function listarEvidenciasPorRecibir()
    {
        $sql = "SELECT 
        e.evidencia_id,
        e.evidencia_nombre,
        e.evidencia_consideraciones,
        e.fecha_plazo_inicio,
        e.fecha_plazo_fin,
        e.fecha_subsanacion,
        e.estado,
        e.fecha_registro,
        mv.medio_nombre,
        e.estado_revision,
        h.historial_id,
        h.fecha_emision,
        CONCAT(u.usu_nom,' ',u.usu_ape) AS nomcompleto,
        
        -- Agregados desde el historial
        CONCAT(ue.usu_nom, ' ', ue.usu_ape) AS emisor_nombre,
        o.oficina_nom AS oficina_origen,

        (
            SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
            FROM tm_evidencias_oficinas eo2
            INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
            WHERE eo2.evidencia_id = e.evidencia_id
        ) AS oficinas_vinculadas

    FROM tm_evidencias e
    INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
    INNER JOIN tm_usuario u ON e.usu_id = u.usu_id

    -- Join directo a historial (sin filtro de último)
    LEFT JOIN tm_evidencias_historial h ON h.evidencia_id = e.evidencia_id
    LEFT JOIN tm_usuario ue ON h.usu_emisor_id = ue.usu_id
    LEFT JOIN tm_oficina o ON h.oficina_origen_id = o.oficina_id

    WHERE EXISTS (
        SELECT 1 
        FROM tm_evidencias_oficinas eo_sub
        WHERE eo_sub.evidencia_id = e.evidencia_id
        AND e.estado_revision = 'Enviado' AND h.estado2='Sin Recibir'
    )

    ORDER BY e.fecha_plazo_fin DESC;
    ";

        return ejecutarConsulta($sql);
    } */
    public function listarEvidenciasPorRevisar()
    {
        $sql = "SELECT 
        e.evidencia_id,
        e.evidencia_nombre,
        e.evidencia_consideraciones,
        e.fecha_plazo_inicio,
        e.fecha_plazo_fin,
        e.fecha_subsanacion,
        e.estado,
        e.fecha_registro,
        e.grado_id,
        mv.medio_nombre,
        e.estado_revision,
        h.historial_id,
        h.fecha_emision,
        CONCAT(u.usu_nom,' ',u.usu_ape) AS nomcompleto,
        
        -- Agregados desde el historial
        CONCAT(ue.usu_nom, ' ', ue.usu_ape) AS emisor_nombre,
        o.oficina_nom AS oficina_origen,

        (
            SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
            FROM tm_evidencias_oficinas eo2
            INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
            WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
        ) AS oficinas_vinculadas,
        (
            SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
            FROM tm_evidencias_coordinadores ec
            INNER JOIN tm_oficina o2 ON ec.oficina_id = o2.oficina_id
            WHERE ec.evidencia_id = e.evidencia_id AND ec.eliminado = 0
        ) AS coordinadores_vinculados

        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id

        -- Join directo a historial (sin filtro de último)
        LEFT JOIN tm_evidencias_historial h ON h.evidencia_id = e.evidencia_id
        LEFT JOIN tm_usuario ue ON h.usu_emisor_id = ue.usu_id
        LEFT JOIN tm_oficina o ON h.oficina_origen_id = o.oficina_id

        WHERE EXISTS (
            SELECT 1 
            FROM tm_evidencias_oficinas eo_sub
            WHERE eo_sub.evidencia_id = e.evidencia_id
            AND e.estado_revision = 'En Revision' AND h.estado2='Recibido' AND eo_sub.eliminado=0
        )

        ORDER BY e.fecha_plazo_fin DESC;
        ";

        return ejecutarConsulta($sql);
    }
    public function listarEvidenciasPorRevisarOld()
    {
        $sql = "SELECT 
        e.evidencia_id,
        e.evidencia_nombre,
        e.evidencia_consideraciones,
        e.fecha_plazo_inicio,
        e.fecha_plazo_fin,
        e.fecha_subsanacion,
        e.estado,
        e.fecha_registro,
        e.grado_id,
        mv.medio_nombre,
        e.estado_revision,
        h.historial_id,
        h.fecha_emision,
        CONCAT(u.usu_nom,' ',u.usu_ape) AS nomcompleto,
        
        -- Agregados desde el historial
        CONCAT(ue.usu_nom, ' ', ue.usu_ape) AS emisor_nombre,
        o.oficina_nom AS oficina_origen,

        (
            SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
            FROM tm_evidencias_oficinas eo2
            INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
            WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
        ) AS oficinas_vinculadas

    FROM tm_evidencias e
    INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
    INNER JOIN tm_usuario u ON e.usu_id = u.usu_id

    -- Join directo a historial (sin filtro de último)
    LEFT JOIN tm_evidencias_historial h ON h.evidencia_id = e.evidencia_id
    LEFT JOIN tm_usuario ue ON h.usu_emisor_id = ue.usu_id
    LEFT JOIN tm_oficina o ON h.oficina_origen_id = o.oficina_id

    WHERE EXISTS (
        SELECT 1 
        FROM tm_evidencias_oficinas eo_sub
        WHERE eo_sub.evidencia_id = e.evidencia_id
        AND e.estado_revision = 'En Revision' AND h.estado2='Recibido' AND eo_sub.eliminado=0
    )

    ORDER BY e.fecha_plazo_fin DESC;
    ";

        return ejecutarConsulta($sql);
    }

    /* public function recibirEvidencia($historial_id,$fecha_recepcion)
    {
        $sql = "UPDATE tm_evidencias_historial SET estado2 = 'Recibido', fecha_recepcion = '$fecha_recepcion'  WHERE historial_id = '$historial_id'";
        $sql2 = "UPDATE tm_evidencias SET estado_revision = 'En Revision', fecha_recepcion = '$fecha_recepcion'  WHERE historial_id = '$historial_id'";
        return ejecutarConsulta($sql);
    } */
    public function recibirEvidencia($historial_id, $fecha_recepcion)
    {
        // Actualiza el historial
        $sql1 = "UPDATE tm_evidencias_historial 
                 SET estado2 = 'Recibido', fecha_recepcion = '$fecha_recepcion'  
                 WHERE historial_id = '$historial_id'";

        // Obtener evidencia_id desde el historial
        $sqlGet = "SELECT evidencia_id FROM tm_evidencias_historial WHERE historial_id = '$historial_id'";
        $res = ejecutarConsultaSimpleFila($sqlGet);

        if ($res && isset($res['evidencia_id'])) {
            $evidencia_id = $res['evidencia_id'];

            // Actualiza la evidencia asociada
            $sql2 = "UPDATE tm_evidencias 
                     SET estado_revision = 'En Revision' WHERE evidencia_id = '$evidencia_id'";

            ejecutarConsulta($sql2);
        }

        return ejecutarConsulta($sql1);
    }

    public function listarEvidenciaId($evidencia_id)
    {
        $sql = "SELECT 
            e.evidencia_id,
            e.evidencia_nombre,
            e.evidencia_consideraciones,
            e.fecha_plazo_inicio,
            e.fecha_plazo_fin,
            e.fecha_subsanacion,
            e.estado,
            e.fecha_registro,
            mv.medio_nombre,
            mi.indicador_nombre,
            e.estado_revision,
            e.grado_id,
            gc.grado_nombre,
            CONCAT(u.usu_nom, ' ', u.usu_ape) AS nomcompleto,
            (
                SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                FROM tm_evidencias_oficinas eo2
                INNER JOIN tm_oficina o2 ON eo2.oficina_id = o2.oficina_id
                WHERE eo2.evidencia_id = e.evidencia_id AND eo2.eliminado=0
            ) AS oficinas_vinculadas,
            (
                SELECT GROUP_CONCAT(DISTINCT o2.oficina_nom SEPARATOR '<br>')
                FROM tm_evidencias_coordinadores ec
                INNER JOIN tm_oficina o2 ON ec.oficina_id = o2.oficina_id
                WHERE ec.evidencia_id = e.evidencia_id AND ec.eliminado = 0
            ) AS coordinadores_vinculados
        FROM tm_evidencias e
        INNER JOIN tm_medio_verificacion mv ON e.medio_id = mv.medio_id
        INNER JOIN tm_indicadores mi ON mv.indicador_id = mi.indicador_id
        INNER JOIN tm_usuario u ON e.usu_id = u.usu_id
        INNER JOIN tm_evidencias_oficinas eo ON eo.evidencia_id = e.evidencia_id
        LEFT JOIN tm_grado_cumplimiento gc ON gc.grado_id = e.grado_id
        WHERE e.evidencia_id = '$evidencia_id'";

        return ejecutarConsulta($sql);
    }

    public function seleccionarGradoCumplimiento()
    {
        $sql = "SELECT grado_id, grado_nombre,estado FROM `tm_grado_cumplimiento` WHERE estado=1";
        return ejecutarConsulta($sql);
    }

    public function registrarCalificacion($historial_id, $grado_id, $observaciones, $fecha_reprogramacion = null, $archivo_observacion = null, $fecha_revision)
    {
        // Obtener evidencia_id, evidencia_nombre y usu_emisor_id
        $sqlEvidenciaId = "SELECT h.evidencia_id, e.evidencia_nombre, h.usu_emisor_id
                       FROM tm_evidencias_historial h
                       INNER JOIN tm_evidencias e ON h.evidencia_id = e.evidencia_id
                       WHERE h.historial_id = $historial_id";
        $res = ejecutarConsultaSimpleFila($sqlEvidenciaId);

        if (!$res) {
            return [
                "ok" => false,
                "error" => "No se encontró la evidencia para el historial_id $historial_id."
            ];
        }

        $evidencia_id = $res["evidencia_id"];
        $evidencia_nombre = $res["evidencia_nombre"];
        $usu_emisor_id = $res["usu_emisor_id"];

        // Actualizar tm_evidencias según grado
        if ($grado_id == 3) {
            $sql1 = "UPDATE tm_evidencias 
                 SET grado_id = $grado_id, 
                     estado_revision = 'Observado',
                     fecha_subsanacion = '$fecha_reprogramacion'
                 WHERE evidencia_id = $evidencia_id";
        } else {
            $sql1 = "UPDATE tm_evidencias 
                 SET grado_id = $grado_id, 
                     estado_revision = 'Finalizado',
                     fecha_subsanacion = NULL
                 WHERE evidencia_id = $evidencia_id";
        }

        // Actualizar tm_evidencias_historial
        $sql2 = "UPDATE tm_evidencias_historial 
             SET observaciones = '$observaciones',
                 estado2 = 'Finalizado',
                 fecha_revision = '$fecha_revision'";

        if ($archivo_observacion) {
            $sql2 .= ", archivo_observacion = '$archivo_observacion'";
        }

        $sql2 .= " WHERE historial_id = $historial_id";

        ejecutarConsulta($sql1);
        $ok = ejecutarConsulta($sql2);

        // Obtener correo del usuario emisor
        $sqlCorreo = "SELECT usu_correo FROM tm_usuario WHERE usu_id = $usu_emisor_id";
        $resCorreo = ejecutarConsultaSimpleFila($sqlCorreo);

        $usu_correo = $resCorreo ? $resCorreo["usu_correo"] : null;

        return [
            "ok" => $ok,
            "evidencia_nombre" => $evidencia_nombre,
            "usu_emisor_id" => $usu_emisor_id,
            "usu_correo" => $usu_correo
        ];
    }

    /* public function registrarCalificacion($historial_id, $grado_id, $observaciones, $fecha_reprogramacion = null, $archivo_observacion = null, $fecha_revision)
    {
        // Obtener evidencia_id desde el historial
        $sqlEvidenciaId = "SELECT evidencia_id FROM tm_evidencias_historial WHERE historial_id = $historial_id";
        $res = ejecutarConsultaSimpleFila($sqlEvidenciaId);
        $evidencia_id = $res["evidencia_id"];

        // Armar SQL de actualización según grado
        if ($grado_id == 3) {
            $sql1 = "UPDATE tm_evidencias 
                 SET grado_id = $grado_id, 
                     estado_revision = 'Observado',
                     fecha_subsanacion = '$fecha_reprogramacion'
                 WHERE evidencia_id = $evidencia_id";
        } else {
            $sql1 = "UPDATE tm_evidencias 
                 SET grado_id = $grado_id, 
                     estado_revision = 'Finalizado',
                     fecha_subsanacion = NULL
                 WHERE evidencia_id = $evidencia_id";
        }


        $sql2 = "UPDATE tm_evidencias_historial 
             SET observaciones = '$observaciones',
                 estado2 = 'Finalizado', fecha_revision='$fecha_revision'";


        if ($archivo_observacion) {
            $sql2 .= ", archivo_observacion = '$archivo_observacion'";
        }

        $sql2 .= " WHERE historial_id = $historial_id";

        ejecutarConsulta($sql1);
        return ejecutarConsulta($sql2);
    } */

    /* public function registrarCalificacion($historial_id, $grado_id, $observaciones, $fecha_reprogramacion = null)
    {
        // Obtener evidencia_id desde el historial
        $sqlEvidenciaId = "SELECT evidencia_id FROM tm_evidencias_historial WHERE historial_id = $historial_id";
        $res = ejecutarConsultaSimpleFila($sqlEvidenciaId);
        $evidencia_id = $res["evidencia_id"];

        // Armar SQL de actualización según grado
        if ($grado_id == 3) {
            // Observado
            $sql1 = "UPDATE tm_evidencias 
                     SET grado_id = $grado_id, 
                         estado_revision = 'Observado',
                         fecha_subsanacion = '$fecha_reprogramacion'
                     WHERE evidencia_id = $evidencia_id";
        } else {
            // Cumple o cumple parcialmente
            $sql1 = "UPDATE tm_evidencias 
                     SET grado_id = $grado_id, 
                         estado_revision = 'Finalizado',
                         fecha_subsanacion = NULL
                     WHERE evidencia_id = $evidencia_id";
        }

        // Guardar observación
        $sql2 = "UPDATE tm_evidencias_historial 
                 SET observaciones = '$observaciones',
                 estado2 ='Finalizado'
                 WHERE historial_id = $historial_id";

        ejecutarConsulta($sql1);
        return ejecutarConsulta($sql2);
    } */
}
