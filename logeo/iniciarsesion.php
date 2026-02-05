<?php
session_start();
require_once("../modelos/Usuario.php");

if (!isset($_SESSION['user_data']) || !isset($_POST['rol_seleccionado'])) {
    header("Location: ../index.php");
    exit;
}

$index = intval($_POST['rol_seleccionado']);
$roles = $_SESSION['user_data']['roles'];

// Validar que el índice esté dentro del rango
if (!isset($roles[$index])) {
    $_SESSION['error_message'] = "Rol seleccionado inválido.";
    header("Location: seleccionar_rol.php");
    exit;
}

$rol = $roles[$index];

// Procesar y establecer variables de sesión definitivas
$_SESSION['usu_id'] = $rol->usu_id;
$_SESSION['nomcompleto'] = $rol->usu_nom . ' ' . $rol->usu_ape;
$_SESSION['usu_correo'] = $rol->usu_correo;
$_SESSION['rol_id'] = $rol->rol_id;
$_SESSION['rol_nom'] = ucfirst(strtolower($rol->rol_nom));
$_SESSION['oficina_id'] = $rol->oficina_id;
$_SESSION['oficina_nom'] = ($rol->oficina_nom);
$_SESSION['user_image'] = $_SESSION['user_data']['imagen'];

// Limpiar datos temporales
unset($_SESSION['user_data']);

// Redirigir al dashboard
header("Location: ../vistas/dashboard.php");
exit;
