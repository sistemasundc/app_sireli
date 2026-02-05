<?php

require_once(__DIR__ . '/../config/conexion.php');


class Condicion
{

	public function __construct() {}
	public function listar()
	{
		$sql = "SELECT cbc.cbc_id AS cbc_id, cbc.cbc_nombre AS cbc_nombre, cbc.cbc_descripcion AS cbc_descripcion, cbc.estado, COUNT(comp.componente_id) AS numero_componentes FROM tm_cbc cbc LEFT JOIN tm_componentes comp ON cbc.cbc_id = comp.cbc_id WHERE cbc.estado=1 AND comp.estado=1 GROUP BY cbc.cbc_id ORDER BY cbc.cbc_nombre ASC;";
		return ejecutarConsulta($sql);
	}

	public function registrarCondicion($cbc_nombre, $cbc_descripcion)
	{
		$cbc_nombre = ucwords(strtolower($cbc_nombre));
		$cbc_descripcion = ucwords(strtolower($cbc_descripcion));
		$sql = "INSERT INTO tm_cbc(cbc_nombre, cbc_descripcion) VALUES ('$cbc_nombre','$cbc_descripcion')";
		return ejecutarConsulta($sql);
	}

	public function seleccionarCondicion()
	{
		$sql = "SELECT * FROM tm_cbc WHERE estado=1 ORDER BY cbc_nombre ASC";
		return ejecutarConsulta($sql);
	}
	public function tieneComponentes($cbc_id)
	{
		$sql = "SELECT COUNT(*) AS total FROM tm_componentes WHERE cbc_id = '$cbc_id' AND estado = 1";

		$result = ejecutarConsultaSimpleFila($sql);
		return isset($result['total']) ? $result['total'] : 0;
	}
	public function desactivarCondicion($cbc_id)
	{
		$sql = "UPDATE tm_cbc SET estado='0' WHERE cbc_id='$cbc_id'";
		return ejecutarConsulta($sql);
	}

	public function activarCondicion($cbc_id)
	{
		$sql = "UPDATE tm_cbc SET estado='1' WHERE cbc_id='$cbc_id'";
		return ejecutarConsulta($sql);
	}

	public function obtenerCondicion($cbc_id)
	{
		$sql = "SELECT cbc_id, cbc_nombre, cbc_descripcion FROM tm_cbc WHERE cbc_id = '$cbc_id'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function actualizarCondicion($cbc_id, $cbc_nombre, $cbc_descripcion)
	{
		$sql = "UPDATE tm_cbc SET cbc_nombre = '$cbc_nombre', cbc_descripcion = '$cbc_descripcion' WHERE cbc_id = '$cbc_id'";
		return ejecutarConsulta($sql);
	}

	public function verificarCondicion($cbc_descripcion)
	{
		$sql = "SELECT cbc_id FROM tm_cbc WHERE cbc_descripcion='$cbc_descripcion'";
		return ejecutarConsulta($sql);
	}
}
