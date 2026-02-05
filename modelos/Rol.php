<?php 

require_once(__DIR__ . '/../config/conexion.php');

Class Rol {

	public function __construct()	{

	}
  
	public function seleccionarRol()	{
		$sql="SELECT * FROM tm_rol ORDER BY rol_nom ASC";
		return ejecutarConsulta($sql);
	}
  
}

?>