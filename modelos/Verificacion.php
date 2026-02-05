<?php

require_once(__DIR__ . '/../config/conexion.php');


class Verificacion
{

    public function __construct() {}
    public function listar()
    {
        $sql = "SELECT 
            medio.medio_id, 
            indi.indicador_nombre,
            medio.medio_nombre,
            medio.estado,
            COUNT(evi.evidencia_id) AS cantidad_evidencias
        FROM 
            tm_medio_verificacion medio
        LEFT JOIN 
            tm_indicadores indi ON indi.indicador_id = medio.indicador_id
        LEFT JOIN 
            tm_evidencias evi ON evi.medio_id = medio.medio_id
        WHERE
        medio.estado=1 AND indi.estado=1 AND evi.estado=1
        GROUP BY 
            medio.medio_id, 
            indi.indicador_nombre, 
            medio.medio_nombre, 
            medio.estado
        ORDER BY 
            CAST(SUBSTRING_INDEX(indi.indicador_nombre, ' ', 1) AS UNSIGNED) ASC;
        ;";

        return ejecutarConsulta($sql);
    }

    public function registrarVerificacion($indicador_id, $medio_nombre)
    {
        $sql = "INSERT INTO tm_medio_verificacion(indicador_id,medio_nombre) VALUES ('$indicador_id','$medio_nombre')";
        return ejecutarConsulta($sql);
    }

    /*     public function seleccionarVerificacion()
    {
        $sql = "SELECT * FROM tm_medio_verificacion ORDER BY medio_nombre ASC";
        return ejecutarConsulta($sql);
    } */

    public function seleccionarVerificacion()
    {
        $sql = "SELECT m.medio_id, m.medio_nombre, c.componente_nombre,i.indicador_nombre
            FROM tm_medio_verificacion AS m
            JOIN tm_indicadores AS i ON m.indicador_id = i.indicador_id
            JOIN tm_componentes AS c ON i.componente_id = c.componente_id
            WHERE m.estado = 1 AND i.estado=1
            ORDER BY CAST(SUBSTRING_INDEX(indicador_nombre, '.', 1) AS UNSIGNED)  ASC";

        return ejecutarConsulta($sql);
    }
    public function tieneEvidencias($medio_id)
    {
        $sql = "SELECT COUNT(*) AS total FROM tm_evidencias WHERE medio_id='$medio_id' AND estado = 1";
        $result = ejecutarConsultaSimpleFila($sql);
        return isset($result['total']) ? $result['total'] : 0;
    }
    public function desactivarMedio($medio_id)
    {
        $sql = "UPDATE tm_medio_verificacion SET estado='0' WHERE medio_id='$medio_id'";
        return ejecutarConsulta($sql);
    }

    public function activarMedio($medio_id)
    {
        $sql = "UPDATE tm_medio_verificacion SET estado='1' WHERE medio_id='$medio_id'";
        return ejecutarConsulta($sql);
    }

    public function obtenerMedio($medio_id)
    {
        $sql = "SELECT medio_id, medio_nombre FROM tm_medio_verificacion WHERE medio_id = '$medio_id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function actualizarMedio($medio_id, $medio_nombre)
    {
        $sql = "UPDATE tm_medio_verificacion SET medio_nombre = '$medio_nombre' WHERE medio_id = '$medio_id'";
        return ejecutarConsulta($sql);
    }
}
