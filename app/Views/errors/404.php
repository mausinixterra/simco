<?php

declare(strict_types=1);

// Load language system if not already loaded
if (!class_exists('App\Core\Lang')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
}

use App\Core\Lang;

// Load configuration and language
$config = require __DIR__ . '/../../../config/app.php';

// Get language from session or use default
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_SESSION['lang'] ?? $config['default_lang'];
Lang::load($lang);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Lang::get('error_404_title', 'Página No Encontrada') ?></title>
    <link rel="stylesheet" href="/assets/css/output.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-9xl font-black text-gray-400"><?= Lang::get('error_404_heading', '404') ?></h1>
        <p class="text-2xl font-semibold text-gray-700 mt-4"><?= Lang::get('error_404_message', 'Página No Encontrada') ?></p>
        <p class="text-gray-500 mt-2"><?= Lang::get('error_404_description', 'Lo sentimos, la página que buscas no existe.') ?></p>
        <a href="/" class="mt-6 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">
            <?= Lang::get('error_404_button', 'Volver al Inicio') ?>
        </a>
    </div>
</body>
</html>