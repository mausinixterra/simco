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

// Get error message if provided
$errorMessage = $errorMessage ?? null;
$debugMode = $_ENV['APP_DEBUG'] === 'true';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Lang::get('error_db_title', 'Error de Conexión') ?></title>
    <link rel="stylesheet" href="/assets/css/output.css">
    <link rel="stylesheet" href="/assets/css/database-error.css">
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen">
    <!-- Navbar with language selector -->
    <?php require_once __DIR__ . '/../shared/navbar.php'; ?>

    <!-- Error content -->
    <div class="flex items-center justify-center min-h-[calc(100vh-64px)] p-4">
        <div class="max-w-2xl w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            <div class="text-center">
                <!-- Database icon -->
                <div class="mb-6 animate-float">
                    <svg class="w-24 h-24 mx-auto text-red-500 dark:text-red-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        <g class="animate-shake">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v.01M12 16v.01"></path>
                        </g>
                    </svg>
                </div>

                <!-- Error heading -->
                <h1 class="text-4xl font-black text-gray-800 dark:text-gray-100 mb-4">
                    <?= Lang::get('error_db_heading', 'Error de Conexión') ?>
                </h1>

                <!-- Error message -->
                <p class="text-xl text-gray-700 dark:text-gray-300 mb-3">
                    <?= Lang::get('error_db_message', 'No se pudo conectar a la base de datos') ?>
                </p>

                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    <?= Lang::get('error_db_description', 'Estamos experimentando problemas técnicos en este momento. Por favor, intente nuevamente más tarde.') ?>
                </p>

                <!-- Actions box -->
                <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-3">
                        <?= Lang::get('error_db_actions_title', '¿Qué puedes hacer?') ?>
                    </h2>
                    <ul class="text-left text-gray-700 dark:text-gray-300 space-y-2">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?= Lang::get('error_db_action_1', 'Espera unos minutos e intenta nuevamente') ?></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?= Lang::get('error_db_action_2', 'Verifica tu conexión a internet') ?></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?= Lang::get('error_db_action_3', 'Si el problema persiste, contacta al soporte técnico') ?></span>
                        </li>
                    </ul>
                </div>

            <!-- Debug information (only in development mode) -->
            <?php if ($debugMode && $errorMessage): ?>
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6 text-left">
                <h3 class="text-sm font-semibold text-red-900 dark:text-red-300 mb-2">Información de Debug (Solo desarrollo):</h3>
                <pre class="text-xs text-red-800 dark:text-red-300 overflow-x-auto whitespace-pre-wrap break-words"><?= htmlspecialchars($errorMessage) ?></pre>
            </div>
            <?php endif; ?>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="/" class="inline-flex items-center justify-center bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <?= Lang::get('error_db_button_home', 'Volver al Inicio') ?>
                </a>
                <button id="retry-button" onclick="retryConnection()" class="inline-flex items-center justify-center bg-green-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-green-700 transition duration-200 cursor-pointer">
                    <svg id="retry-icon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <svg id="retry-spinner" class="hidden w-5 h-5 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="retry-text" data-retrying-text="<?= Lang::get('error_db_retrying', 'Reintentando...') ?>"><?= Lang::get('error_db_button_retry', 'Intentar Nuevamente') ?></span>
                </button>
            </div>

            <!-- Support contact -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <?= Lang::get('error_db_support', 'Si necesitas ayuda urgente, contacta a soporte técnico') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- External JavaScript for functionality -->
    <script src="/assets/js/database-error.js"></script>
</body>
</html>
