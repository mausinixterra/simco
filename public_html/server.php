<?php

// Este es un script de enrutamiento para el servidor de desarrollo de PHP.

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Si el archivo solicitado existe (como una imagen o un archivo CSS), sírvelo directamente.
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Para cualquier otra petición, carga el front-controller (index.php).
require_once __DIR__ . '/index.php';