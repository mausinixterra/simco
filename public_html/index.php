<?php

declare(strict_types=1);

session_start();

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/vendor/autoload.php';

use App\Core\Lang;
use App\Core\Router;
use Dotenv\Dotenv;

// 1. Cargar Entorno y Configuración
$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();
$config = require ROOT_PATH . '/config/app.php';

// 2. Cargar Internacionalización (i18n)
$requestedLang = $_GET['lang'] ?? $_SESSION['lang'] ?? $config['default_lang'];

// Validate and sanitize language parameter
if (isset($_GET['lang'])) {
    // Only allow alphanumeric characters (2-5 chars)
    $sanitizedLang = preg_replace('/[^a-z]/', '', strtolower($_GET['lang']));
    
    if (!in_array($sanitizedLang, $config['supported_langs'])) {
        // Invalid language requested, redirect to same page without lang parameter
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?');
        header("Location: {$redirectUrl}");
        exit;
    }
    
    $_SESSION['lang'] = $sanitizedLang;
} else {
    // Use session language or default
    $_SESSION['lang'] = in_array($requestedLang, $config['supported_langs']) ? $requestedLang : $config['default_lang'];
}

Lang::load($_SESSION['lang']);

// 3. Cargar y dirigir las rutas
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

Router::load(ROOT_PATH . '/routes/web.php')
    ->direct($requestUri, $requestMethod);