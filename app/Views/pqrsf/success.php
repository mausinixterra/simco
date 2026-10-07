<?php 
use App\Core\Lang;

// Configuración de variables para el header
$pageTitle = Lang::get('success_page_title') . ' - SIMCO PQRSF';

// Include header compartido
require_once __DIR__ . '/../shared/header.php';
?>

<!-- Success page specific styles and scripts -->
<link rel="stylesheet" href="/assets/css/success-page.css">
<script src="/assets/js/success-page.js" defer></script>

<?php
// Navbar compartido sin menú de idioma; el logo vuelve al formulario de origen
$navbarHomeUrl      = (isset($formOrigin) && $formOrigin === 'insurance') ? '/aseguradoras' : '/';
$navbarShowLangMenu = false;

require __DIR__ . '/../shared/navbar.php';
?>

    <!-- Contenido principal -->
    <main class="container mx-auto px-6 py-6">
        <div class="max-w-3xl mx-auto">
            <!-- Card de éxito/error -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
                <div id="success" class="text-center">
                    
                    <!-- Ícono SVG animado -->
                    <div class="icon icon--order-success svg flex justify-center mb-6">
                        <?php if (isset($success) && $success): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="80px" height="80px" viewBox="0 0 72 72">
                                <g fill="none" stroke="#10b981" stroke-width="2">
                                    <circle cx="36" cy="36" r="35" style="stroke-dasharray:240px, 240px; stroke-dashoffset: 480px;"></circle>
                                    <path d="M17.417,37.778l9.93,9.909l25.444-25.393" style="stroke-dasharray:50px, 50px; stroke-dashoffset: 0px;"></path>
                                </g>
                            </svg>
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="80px" height="80px" viewBox="0 0 72 72">
                                <g fill="none" stroke="#ef4444" stroke-width="2">
                                    <circle cx="36" cy="36" r="35" style="stroke-dasharray:240px, 240px; stroke-dashoffset: 480px;"></circle>
                                    <path d="M22,22 L50,50 M50,22 L22,50" style="stroke-dasharray:50px, 50px; stroke-dashoffset: 0px;"></path>
                                </g>
                            </svg>
                        <?php endif; ?>
                    </div>

                    <!-- Mensajes de éxito o error -->
                    <?php if (isset($success) && $success): ?>
                        <div class="fade-in">
                            <h1 class="text-3xl font-extrabold text-green-600 dark:text-green-400 mb-4">
                                <?= Lang::get('success_registration_title') ?>
                            </h1>
                            
                            <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-500 dark:border-green-600 rounded-lg p-6 mb-6">
                                <p class="text-base text-gray-700 dark:text-gray-200 mb-2">
                                    <?= Lang::get('success_pqrsf_number') ?>
                                </p>
                                <p class="text-4xl font-extrabold text-green-700 dark:text-green-300">
                                    <?= isset($pqrsfId) ? htmlspecialchars((string)$pqrsfId, ENT_QUOTES, 'UTF-8') : 'N/A' ?>
                                </p>
                            </div>
                            
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                <?= Lang::get('success_thank_you_message') ?>
                            </p>
                            
                            <?php if (isset($emailSent) && $emailSent): ?>
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-lg mb-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium"><?= Lang::get('success_email_sent_confirmation') ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="fade-in">
                            <h1 class="text-3xl font-extrabold text-red-600 dark:text-red-400 mb-4">
                                <?= Lang::get('error_registration_title') ?>
                            </h1>
                            
                            <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-500 dark:border-red-600 rounded-lg p-6 mb-6">
                                <p class="text-base text-gray-700 dark:text-gray-200">
                                    <?= isset($errorMessage) ? htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') : Lang::get('error_generic_message') ?>
                                </p>
                            </div>
                            
                            <p class="text-gray-600 dark:text-gray-400">
                                <?= Lang::get('error_try_again_message') ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Información adicional -->
                    <div class="fade-in-delayed mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <?= Lang::get('success_redirect_message') ?>
                            <span class="font-semibold text-[#1f3766] dark:text-blue-400"><span id="countdown">8</span> <?= Lang::get('success_seconds') ?></span>
                        </p>
                        
                        <!-- Botón para volver inmediatamente -->
                        <a href="<?= isset($formOrigin) && $formOrigin === 'insurance' ? '/aseguradoras' : '/' ?>" id="return-home-btn" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1f3766] hover:bg-[#2d4a8f] text-white font-semibold rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span><?= Lang::get('success_return_home_button') ?></span>
                        </a>

                        <!-- Información de contacto -->
                        <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 font-semibold">
                                <?= Lang::get('success_contact_info') ?>:
                            </p>
                            <div class="flex flex-wrap justify-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span>📞 PBX: (602) 6603000 Ext. 753</span>
                                <span>📱 WhatsApp: 321 722 8389</span>
                                <span>🌐 www.clinicadeoccidente.com</span>
                            </div>
                        </div>
                        
                        <!-- Footer con Ley 1755 -->
                        <div class="mt-6 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <?= Lang::get('law_1755_notice') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (igual que el formulario) -->
    <?php require_once __DIR__ . '/../shared/footer.php'; ?>

</body>

</html>
