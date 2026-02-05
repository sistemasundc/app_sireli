<?php

require_once(__DIR__ . '/../config/conexion.php');


class Oficina
{

	public function __construct() {}
	public function listar()
	{
		$sql = "SELECT oficina_id, oficina_nom,estado FROM `tm_oficina` ORDER BY oficina_nom ASC";
		return ejecutarConsulta($sql);
	}
	public function seleccionarOficina()
	{
		$sql = "SELECT * FROM tm_oficina WHERE estado=1 ORDER BY oficina_nom ASC";
		return ejecutarConsulta($sql);
	}
	
	//Registrar Oficina
	public function registrarOficina($oficina_nom)
	{
		/* $oficina_nom = ucwords(strtolower($oficina_nom)); */
		$sql = "INSERT INTO tm_oficina (oficina_nom) VALUES ('$oficina_nom')";
		return ejecutarConsulta($sql);
	}

	public function verificarOficina($oficina_nom)
	{
		$sql = "SELECT oficina_id FROM tm_oficina WHERE oficina_nom='$oficina_nom'";
		return ejecutarConsulta($sql);
	}

	public function obtenerOficina($oficina_id)
	{
		$sql = "SELECT oficina_id, oficina_nom FROM tm_oficina WHERE oficina_id = '$oficina_id'";
		return ejecutarConsultaSimpleFila($sql);
	}
	//Actualizar Oficina
	public function actualizarOficina($oficina_id, $oficina_nom)
	{
		/* $oficina_nom = ucwords(strtolower($oficina_nom)); */
		$sql = "UPDATE tm_oficina SET oficina_nom = '$oficina_nom' WHERE oficina_id = '$oficina_id'";

		return ejecutarConsulta($sql);
	}
	//Desactivar Oficina
	public function desactivarOficina($oficina_id)
	{
		$sql = "UPDATE tm_oficina SET estado='0' WHERE oficina_id='$oficina_id'";
		return ejecutarConsulta($sql);
	}

	public function activarOficina($oficina_id)
	{
		$sql = "UPDATE tm_oficina SET estado='1' WHERE oficina_id='$oficina_id'";
		return ejecutarConsulta($sql);
	}
}
