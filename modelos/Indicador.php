<?php

require_once(__DIR__ . '/../config/conexion.php');


class Indicador
{

	public function __construct() {}

	public function listar()
	{
		$sql = "SELECT indi.indicador_id, comp.componente_nombre, indi.indicador_nombre AS indicador_nombre, indi.estado, COUNT(medio.medio_id) AS numero_medios_verificacion FROM tm_indicadores indi LEFT JOIN tm_componentes comp ON comp.componente_id = indi.componente_id LEFT JOIN tm_medio_verificacion medio ON medio.indicador_id = indi.indicador_id WHERE indi.estado = 1 AND comp.estado=1 AND medio.estado=1 GROUP BY indi.indicador_id, comp.componente_nombre, indi.indicador_nombre, indi.estado ORDER BY comp.componente_nombre ASC;";
		return ejecutarConsulta($sql);
	}

	public function seleccionarIndicador()
	{
		$sql = "SELECT * FROM tm_indicadores WHERE estado = 1 ORDER BY CAST(SUBSTRING_INDEX(indicador_nombre, '.', 1) AS UNSIGNED)  ASC";
		return ejecutarConsulta($sql);
	}
	public function registrarIndicador($componente_id, $indicador_nombre)
	{
		$sql = "INSERT INTO tm_indicadores (componente_id,indicador_nombre) VALUES ('$componente_id','$indicador_nombre')";
		return ejecutarConsulta($sql);
	}
	public function tieneMedios($indicador_id)
	{
		$sql = "SELECT COUNT(*) AS total FROM tm_medio_verificacion WHERE indicador_id= '$indicador_id' AND estado = 1";
		$result = ejecutarConsultaSimpleFila($sql);
		return isset($result['total']) ? $result['total'] : 0;
	}
	public function desactivarIndicador($indicador_id)
	{
		$sql = "UPDATE tm_indicadores SET estado='0' WHERE indicador_id='$indicador_id'";
		return ejecutarConsulta($sql);
	}

	public function activarIndicador($indicador_id)
	{
		$sql = "UPDATE tm_indicadores SET estado='1' WHERE indicador_id='$indicador_id'";
		return ejecutarConsulta($sql);
	}

	public function obtenerIndicador($indicador_id)
	{
		$sql = "SELECT indicador_id, indicador_nombre FROM tm_indicadores WHERE indicador_id = '$indicador_id'";
		return ejecutarConsultaSimpleFila($sql);
	}
	public function actualizarIndicador($indicador_id, $indicador_nombre)
	{
		$sql = "UPDATE tm_indicadores SET indicador_nombre = '$indicador_nombre' WHERE indicador_id = '$indicador_id'";
		return ejecutarConsulta($sql);
	}
}
