<?php

require_once(__DIR__ . '/../config/conexion.php');


class Indicador
{

	public function __construct() {}

	public function listar()
	{
		$sql = "SELECT indi.indicador_id, comp.componente_nombre, indi.indicador_nombre AS indicador_nombre, indi.estado, COUNT(medio.medio_id) AS numero_medios_verificacion FROM tm_indicadores indi LEFT JOIN tm_componentes comp ON comp.componente_id = indi.componente_id LEFT JOIN tm_medio_verificacion medio ON medio.indicador_id = indi.indicador_id GROUP BY indi.indicador_id, comp.componente_nombre, indi.indicador_nombre, indi.estado ORDER BY indi.indicador_nombre;";
		return ejecutarConsulta($sql);
	}

	public function seleccionarIndicador()
	{
		$sql = "SELECT * FROM tm_indicadores ORDER BY indicador_nombre ASC";
		return ejecutarConsulta($sql);
	}
	public function registrarIndicador($componente_id, $indicador_nombre)
	{
		$sql = "INSERT INTO tm_indicadores (componente_id,indicador_nombre) VALUES ('$componente_id','$indicador_nombre')";
		return ejecutarConsulta($sql);
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
