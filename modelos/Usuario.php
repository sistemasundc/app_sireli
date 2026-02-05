<?php

require_once(__DIR__ . '/../config/conexion.php');


class Usuario
{

	public function __construct() {}

	//UsuarioLogeo
	public function verificarLogeo($email)
	{
		$sql = "SELECT u.usu_id,u.usu_correo,u.usu_nom,u.usu_ape,u.rol_id,u.oficina_id,r.rol_nom, o.oficina_nom FROM tm_usuario u INNER JOIN tm_oficina o ON u.oficina_id=o.oficina_id INNER JOIN tm_rol r ON u.rol_id=r.rol_id WHERE u.usu_correo='$email' AND u.estado='1' AND o.estado='1' GROUP BY u.usu_id";
		return ejecutarConsulta($sql);
	}

	//Mostrar Datos Usuario
	public function mostrarUsuario($usu_id)
	{
		$sql = "SELECT u.usu_id,u.usu_ape,u.usu_nom,u.usu_telf,u.usu_correo,r.rol_id,r.rol_nom,s.oficina_id,s.oficina_nom,u.estado FROM tm_usuario u INNER JOIN tm_rol r ON u.rol_id=r.rol_id INNER JOIN tm_oficina s ON u.oficina_id=s.oficina_id WHERE usu_id='$usu_id'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Listar Usuarios
	public function listar()
	{
		$sql = "SELECT u.usu_id,CONCAT(u.usu_nom,' ',u.usu_ape) as 'nomcompleto',r.rol_nom,s.oficina_nom,u.estado, u.usu_telf, u.usu_correo, u.fech_crea FROM tm_usuario u INNER JOIN tm_rol r ON u.rol_id=r.rol_id INNER JOIN tm_oficina s ON u.oficina_id=s.oficina_id ORDER BY nomcompleto ASC";
		return ejecutarConsulta($sql);
	}

	//Registrar Usuario
	public function registrarUsuario($usu_nom, $usu_ape, $usu_telf, $usu_correo, $rol_id, $oficina_id, $fech_crea)
	{
		$usu_nom = ucwords(strtolower($usu_nom));
		$usu_ape = ucwords(strtolower($usu_ape));
		$sql = "INSERT INTO tm_usuario(usu_nom,usu_ape,usu_telf,usu_correo,rol_id,oficina_id,fech_crea) VALUES (('$usu_nom'),('$usu_ape'),'$usu_telf','$usu_correo','$rol_id','$oficina_id','$fech_crea')";
		return ejecutarConsulta($sql);
	}

	//Verificar Correo Registrado
	public function verificarCorreo($usu_correo)
	{
		$sql = "SELECT usu_id FROM tm_usuario WHERE usu_correo='$usu_correo'";
		return ejecutarConsulta($sql);
	}

	//Obtener Datos Usuario
	public function obtenerUsuario($usu_id)
	{
		$sql = "SELECT usu_id, usu_nom, usu_ape,usu_telf,usu_correo, rol_id,oficina_id FROM tm_usuario WHERE usu_id = '$usu_id'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Actualizar Usuario
	public function actualizarUsuario($usu_id, $usu_nom, $usu_ape, $usu_correo, $usu_telf, $rol_id, $oficina_id)
	{
		$usu_nom = ucwords(strtolower($usu_nom));
		$usu_ape = ucwords(strtolower($usu_ape));
		$sql = "UPDATE tm_usuario SET usu_nom = '$usu_nom',usu_ape = '$usu_ape', usu_correo = '$usu_correo', usu_telf = '$usu_telf', rol_id = '$rol_id', oficina_id = '$oficina_id'WHERE usu_id = '$usu_id'";

		return ejecutarConsulta($sql);
	}
	//Desactivar Usuario
	public function desactivarUsuario($usu_id)
	{
		$sql = "UPDATE tm_usuario SET estado='0' WHERE usu_id='$usu_id'";
		return ejecutarConsulta($sql);
	}
	//Activar Usuario
	public function activarUsuario($usu_id)
	{
		$sql = "UPDATE tm_usuario SET estado='1' WHERE usu_id='$usu_id'";
		return ejecutarConsulta($sql);
	}

	/* Para Mi Perfil */
	public function mostrarDatosUsuario($usu_id)
	{
		$sql = "SELECT u.usu_id,u.usu_ape,u.usu_nom,u.usu_telf,u.usu_correo,r.rol_id,r.rol_nom,s.oficina_id,s.oficina_nom,u.estado, u.fech_crea FROM tm_usuario u INNER JOIN tm_rol r ON u.rol_id=r.rol_id INNER JOIN tm_oficina s ON u.oficina_id=s.oficina_id WHERE usu_id='$usu_id'";
		return ejecutarConsulta($sql);
	}

	public function actualizarMiUsuario($usu_id, $usu_nom, $usu_ape, $usu_telf)
	{
		$sql = "UPDATE tm_usuario SET usu_nom = '$usu_nom',usu_ape = '$usu_ape', usu_telf = '$usu_telf' WHERE usu_id = '$usu_id'";

		return ejecutarConsulta($sql);
	}
}
