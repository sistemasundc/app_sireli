<?php

require_once(__DIR__ . '/../config/conexion.php');


class Componente
{

	public function __construct() {}

	public function listar()
	{
		$sql = "SELECT comp.componente_id, cbc.cbc_nombre, comp.componente_nombre, comp.estado, COUNT(indi.indicador_id) AS numero_indicadores FROM tm_componentes comp LEFT JOIN tm_cbc cbc ON cbc.cbc_id = comp.cbc_id LEFT JOIN tm_indicadores indi ON indi.componente_id = comp.componente_id WHERE comp.estado=1 AND indi.estado=1 AND cbc.estado=1 GROUP BY comp.componente_id, cbc.cbc_nombre, comp.componente_nombre, comp.estado ORDER BY comp.componente_nombre";
		return ejecutarConsulta($sql);
	}
	public function registrarComponente($cbc_id, $componente_nombre)
	{
		$sql = "INSERT INTO tm_componentes (cbc_id,componente_nombre) VALUES ('$cbc_id','$componente_nombre')";
		return ejecutarConsulta($sql);
	}

	public function seleccionarComponente()
	{
		$sql = "SELECT * FROM tm_componentes WHERE estado = 1 ORDER BY componente_nombre ASC";
		return ejecutarConsulta($sql);
	}
	public function tieneIndicadores($componente_id)
	{
		$sql = "SELECT COUNT(*) AS total FROM tm_indicadores WHERE componente_id= '$componente_id' AND estado = 1";

		$result = ejecutarConsultaSimpleFila($sql);
		return isset($result['total']) ? $result['total'] : 0;
	}
	public function desactivarComponente($componente_id)
	{
		$sql = "UPDATE tm_componentes SET estado='0' WHERE componente_id='$componente_id'";
		return ejecutarConsulta($sql);
	}

	public function activarComponente($componente_id)
	{
		$sql = "UPDATE tm_componentes SET estado='1' WHERE componente_id='$componente_id'";
		return ejecutarConsulta($sql);
	}

	public function obtenerComponente($componente_id)
	{
		$sql = "SELECT componente_id, componente_nombre FROM tm_componentes WHERE componente_id = '$componente_id'";
		return ejecutarConsultaSimpleFila($sql);
	}
	public function actualizarComponente($componente_id, $componente_nombre)
	{
		$sql = "UPDATE tm_componentes SET componente_nombre = '$componente_nombre' WHERE componente_id = '$componente_id'";
		return ejecutarConsulta($sql);
	}
}
