<?php

require_once(__DIR__ . '/../modelos/Rol.php');

$roles = new Rol();

switch ($_GET["op"]) {

	case 'seleccionarRol':
		$rspta = $roles->seleccionarRol();

		echo '<option value="" disabled selected>Seleccionar rol</option>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value="' . $reg->rol_id . '">' . $reg->rol_nom . '</option>';
		}

		break;
}
