<?php
session_start();

if (!isset($_SESSION['usu_id'])) {
    echo "No tienes acceso a este archivo. Por favor, inicie sesión.";
    exit;
}

if (!isset($_GET['archivo'])) {
    echo "Nombre de archivo no especificado.";
    exit;
}

$archivo = basename($_GET['archivo']);

// Definir posibles rutas de búsqueda
$rutas = [
    "../archivos/evidencias/$archivo",
    "../archivos/observaciones/$archivo"
];

$archivoPath = null;

foreach ($rutas as $ruta) {
    if (file_exists($ruta)) {
        $archivoPath = $ruta;
        break;
    }
}

if ($archivoPath) {
    $extension = strtolower(pathinfo($archivoPath, PATHINFO_EXTENSION));

    // Si es PDF, mostrar en el navegador
    if ($extension === 'pdf') {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($archivoPath) . '"');
        header('Content-Length: ' . filesize($archivoPath));
        readfile($archivoPath);
        exit;
    }

    // Para otros formatos, forzar descarga
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($archivoPath) . '"');
    header('Content-Length: ' . filesize($archivoPath));
    readfile($archivoPath);
    exit;
} else {
    echo "El archivo no existe.";
    exit;
}