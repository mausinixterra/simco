<?php 
use App\Core\Lang;
use App\Helpers\TextHelper;

// Configuración de variables para el header
$pageTitle = Lang::get('satisfaction_page_title');

// Include header compartido
require_once __DIR__ . '/../shared/header.php';
?>

<?php
// Navbar compartido: conserva los parámetros de la URL al cambiar de idioma
$navbarLangEsUrl = '?' . http_build_query(['lang' => 'es'] + $_GET);
$navbarLangEnUrl = '?' . http_build_query(['lang' => 'en'] + $_GET);

require __DIR__ . '/../shared/navbar.php';
?>

<div class="min-h-[calc(100vh-200px)] bg-gray-50 dark:bg-gray-900">
    <main class="container mx-auto px-6 py-12">
        
        <?php if ($showError ?? false): ?>
            <!-- Error Message Container -->
            <div class="max-w-2xl mx-auto">
                <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 rounded-lg shadow-lg p-8 text-center">
                    <div class="flex justify-center mb-6">
                        <img src="/assets/img/logo_cdo.png" alt="Logo" class="max-w-xs rounded-lg dark:opacity-90">
                    </div>
                    <h2 class="text-2xl font-bold text-red-800 dark:text-red-400 mb-4">
                        <?= Lang::get('satisfaction_error_title') ?>
                    </h2>
                    
                    <?php if ($errorType === 'no_connection'): ?>
                        <p class="text-gray-700 dark:text-gray-300 text-lg">
                            <?= Lang::get('satisfaction_error_no_connection') ?>
                        </p>
                    <?php elseif ($errorType === 'not_found'): ?>
                        <p class="text-gray-700 dark:text-gray-300 text-lg">
                            <?= Lang::get('satisfaction_error_not_found') ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif (isset($showResult) && $showResult): ?>
            <!-- Result Message Container -->
            <div class="max-w-2xl mx-auto">
                <div class="<?= $resultSuccess ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' ?> border-2 rounded-lg shadow-lg p-8 text-center">
                    <div class="flex justify-center mb-6">
                        <img src="/assets/img/logo_cdo.png" alt="Logo" class="max-w-xs rounded-lg dark:opacity-90">
                    </div>
                    <h2 class="text-2xl font-bold <?= $resultSuccess ? 'text-green-800 dark:text-green-400' : 'text-red-800 dark:text-red-400' ?> mb-4">
                        <?= $resultSuccess ? Lang::get('satisfaction_success_title') : Lang::get('satisfaction_error_save_title') ?>
                    </h2>
                    <p class="text-gray-700 dark:text-gray-300 text-lg">
                        <?= $resultSuccess ? Lang::get('satisfaction_success_message') : Lang::get('satisfaction_error_save_message') ?>
                    </p>
                </div>
            </div>

        <?php else: ?>
            <!-- Satisfaction Form -->
            <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                <div class="flex justify-center mb-6">
                    <img src="/assets/img/logo_cdo.png" alt="Logo" class="max-w-xs rounded-lg dark:opacity-90">
                </div>
                
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-extrabold text-[#1f3766] dark:text-gray-100 mb-2">
                        <?= Lang::get('satisfaction_form_title') ?>
                    </h1>
                </div>
                
                <div class="text-gray-700 dark:text-gray-300 space-y-4 mb-8">
                    <p>
                        <?= Lang::get('satisfaction_greeting') ?> 
                        <span class="font-semibold text-[#1f3766] dark:text-blue-400">
                            <?= htmlspecialchars(TextHelper::formatearNombre($pqrsfData['nombrepeticionario']), ENT_QUOTES, 'UTF-8') ?>
                        </span>,
                    </p>
                    <p>
                        <?= Lang::get('satisfaction_case_info_1') ?> 
                        <strong class="text-[#1f3766] dark:text-blue-400"><?= htmlspecialchars($pqrsfData['id'], ENT_QUOTES, 'UTF-8') ?></strong> 
                        <?= Lang::get('satisfaction_case_info_2') ?> 
                        <strong class="text-[#1f3766] dark:text-blue-400"><?= htmlspecialchars(TextHelper::formatearNombre($pqrsfData['nombrepaciente']), ENT_QUOTES, 'UTF-8') ?>,</strong> 
                        <?= Lang::get('satisfaction_case_info_3') ?> 
                        <strong class="text-[#1f3766] dark:text-blue-400"><?= htmlspecialchars(TextHelper::formatearNombre($pqrsfData['servicio']), ENT_QUOTES, 'UTF-8') ?></strong>
                    </p>
                    <p><?= Lang::get('satisfaction_request_message') ?></p>
                </div>

                <form action="/satisfaccion" method="post" id="formulario-conformidad">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($encodedId, ENT_QUOTES, 'UTF-8') ?>">
                    
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 border-b-2 border-gray-400 dark:border-gray-700 pb-2 mb-6">
                        <?= Lang::get('satisfaction_response_title') ?>
                    </h2>
                    
                    <!-- Radio Options -->
                    <div class="flex justify-center gap-8 py-6 mb-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input 
                                type="radio" 
                                id="si" 
                                name="conformidad" 
                                value="si" 
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2"
                                required
                            >
                            <span class="text-lg font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                <?= Lang::get('satisfaction_yes') ?>
                            </span>
                        </label>
                        
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input 
                                type="radio" 
                                id="no" 
                                name="conformidad" 
                                value="no" 
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2"
                                required
                            >
                            <span class="text-lg font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                <?= Lang::get('satisfaction_no') ?>
                            </span>
                        </label>
                    </div>
                    
                    <!-- Reason Field (Hidden by default) -->
                    <div id="campo-motivo" class="hidden mb-6">
                        <div class="relative z-0 w-full group">
                            <textarea 
                                id="motivo" 
                                name="motivo" 
                                rows="4"
                                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer resize-none"
                                placeholder=" "
                                minlength="10"
                                maxlength="240"
                            ></textarea>
                            <label for="motivo" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                <?= Lang::get('satisfaction_reason_label') ?> <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <div class="flex justify-end text-sm mt-2">
                            <span id="caracteres-restantes" class="text-gray-600 dark:text-gray-400">240</span>
                            <span class="text-gray-600 dark:text-gray-400 ml-1"><?= Lang::get('satisfaction_characters_remaining') ?></span>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        id="boton-enviar" 
                        disabled
                        class="w-full text-white bg-[#1f3766] hover:bg-[#152849] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:bg-gray-400 dark:disabled:bg-gray-600 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-colors duration-200"
                    >
                        <i class="fa-solid fa-check"></i>
                        <span><?= Lang::get('satisfaction_submit_button') ?></span>
                    </button>
                </form>
            </div>
        <?php endif; ?>
        
    </main>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>

<!-- JavaScript -->
<script src="/assets/js/satisfaction.js"></script>

</body>
</html>
