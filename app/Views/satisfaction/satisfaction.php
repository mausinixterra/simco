<?php 
use App\Core\Lang;
use App\Models\SatisfactionModel;

// Configuración de variables para el header
$pageTitle = Lang::get('satisfaction_page_title');

// Include header compartido
require_once __DIR__ . '/../shared/header.php';
?>

<!-- Custom navbar for satisfaction page that preserves URL parameters -->
<?php 
// Build language URLs preserving current parameters
$currentParams = $_GET;
$esParams = $currentParams;
$esParams['lang'] = 'es';
$enParams = $currentParams;
$enParams['lang'] = 'en';
$langEsUrl = '?' . http_build_query($esParams);
$langEnUrl = '?' . http_build_query($enParams);
?>

<nav class="bg-white dark:bg-gray-800 shadow">
    <div class="container mx-auto px-6 py-3 flex justify-end items-center space-x-4">
        <!-- Logo -->
        <div class="flex-grow">
            <a href="/" class="flex items-center">
                <img src="/assets/img/logo_header_dark.png" alt="Logo" class="h-8 w-auto mr-3 block dark:hidden">
                <img src="/assets/img/logo_header_light.png" alt="Logo" class="h-8 w-auto mr-3 hidden dark:block">
            </a>
        </div>
        
        <div class="flex items-center space-x-4">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none transition duration-300 ease-in-out transform hover:scale-110 focus:scale-110 cursor-pointer">
                <svg id="theme-toggle-dark-icon" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-toggle-light-icon" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>
            </button>

            <!-- Language Menu -->
            <div class="relative">
                <button id="lang-menu-button" class="flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none transition duration-300 ease-in-out transform hover:scale-110 focus:scale-110 cursor-pointer">
                    <svg width="24" height="24" viewBox="0 0 512 512" class="fill-current">
                        <path d="M56 40A48 48 0 008 88v160a48 48 0 0040 47.33V336a8 8 0 0013.66 5.66L107.31 296H184v80a48 48 0 0048 48h172.69l45.65 45.66A8 8 0 00464 464v-40.67A48 48 0 00504 376V216a48 48 0 00-48-48H328V88a48 48 0 00-48-48zM56 56h224a32 32 0 0132 32v160a32 32 0 01-32 32H104a8 8 0 00-5.66 2.34L64 316.69V288a8 8 0 00-8-8 32 32 0 01-32-32V88a32 32 0 0132-32zm103.77 48a8 8 0 00-7.77 8v16H96a8 8 0 000 16h32.28c1.51 21.91 9.18 41.75 20.93 57.03-10.5 9.41-23.34 14.97-37.21 14.97a8 8 0 000 16c18 0 34.62-7.17 48-19.25 13.38 12.08 30 19.25 48 19.25a8 8 0 000-16c-13.87 0-26.71-5.56-37.21-14.97 11.75-15.28 19.42-35.12 20.93-57.03H224a8 8 0 000-16h-56v-16a8 8 0 00-8.23-8zm-15.45 40h31.36A90.35 90.35 0 01160 188.83 90.35 90.35 0 01144.32 144zM328 184h128a32 32 0 0132 32v160a32 32 0 01-32 32 8 8 0 00-8 8v28.69l-34.34-34.35A8 8 0 00408 408H232a32 32 0 01-32-32v-80h80a48 48 0 0048-48zM352.22 240a8 8 0 00-7.75 5.31l-40 112a8 8 0 1015.06 5.38L331.92 328h40.16l12.39 34.69a8 8 0 1015.06-5.38l-40-112a8 8 0 00-7.31-5.31zM352 271.79L366.36 312h-28.72z"/>
                    </svg>
                </button>
                <div id="lang-menu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-20 hidden">
                    <a href="<?= htmlspecialchars($langEsUrl) ?>" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 <?=($_SESSION['lang']??'es')==='es'?'font-bold text-blue-600 dark:text-blue-400':''?>">
                        <?=Lang::get('lang_es')?>
                    </a>
                    <a href="<?= htmlspecialchars($langEnUrl) ?>" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 <?=($_SESSION['lang']??'es')==='en'?'font-bold text-blue-600 dark:text-blue-400':''?>">
                        <?=Lang::get('lang_en')?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Navbar functionality: theme toggle and language menu -->
<script src="/assets/js/navbar.js"></script>

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
                        <?php echo Lang::get('satisfaction_error_title'); ?>
                    </h2>
                    
                    <?php if ($errorType === 'no_connection'): ?>
                        <p class="text-gray-700 dark:text-gray-300 text-lg">
                            <?php echo Lang::get('satisfaction_error_no_connection'); ?>
                        </p>
                    <?php elseif ($errorType === 'not_found'): ?>
                        <p class="text-gray-700 dark:text-gray-300 text-lg">
                            <?php echo Lang::get('satisfaction_error_not_found'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif (isset($showResult) && $showResult): ?>
            <!-- Result Message Container -->
            <div class="max-w-2xl mx-auto">
                <div class="<?php echo $resultSuccess ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'; ?> border-2 rounded-lg shadow-lg p-8 text-center">
                    <div class="flex justify-center mb-6">
                        <img src="/assets/img/logo_cdo.png" alt="Logo" class="max-w-xs rounded-lg dark:opacity-90">
                    </div>
                    <h2 class="text-2xl font-bold <?php echo $resultSuccess ? 'text-green-800 dark:text-green-400' : 'text-red-800 dark:text-red-400'; ?> mb-4">
                        <?php echo $resultSuccess ? Lang::get('satisfaction_success_title') : Lang::get('satisfaction_error_save_title'); ?>
                    </h2>
                    <p class="text-gray-700 dark:text-gray-300 text-lg">
                        <?php echo $resultSuccess ? Lang::get('satisfaction_success_message') : Lang::get('satisfaction_error_save_message'); ?>
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
                        <?php echo Lang::get('satisfaction_form_title'); ?>
                    </h1>
                </div>
                
                <div class="text-gray-700 dark:text-gray-300 space-y-4 mb-8">
                    <p>
                        <?php echo Lang::get('satisfaction_greeting'); ?> 
                        <span class="font-semibold text-[#1f3766] dark:text-blue-400">
                            <?php echo SatisfactionModel::formatoNombre($pqrsfData['nombrepeticionario']); ?>
                        </span>,
                    </p>
                    <p>
                        <?php echo Lang::get('satisfaction_case_info_1'); ?> 
                        <strong class="text-[#1f3766] dark:text-blue-400"><?php echo htmlspecialchars($pqrsfData['id']); ?></strong> 
                        <?php echo Lang::get('satisfaction_case_info_2'); ?> 
                        <strong class="text-[#1f3766] dark:text-blue-400"><?php echo SatisfactionModel::formatoNombre($pqrsfData['nombrepaciente']); ?>,</strong> 
                        <?php echo Lang::get('satisfaction_case_info_3'); ?> 
                        <strong class="text-[#1f3766] dark:text-blue-400"><?php echo SatisfactionModel::formatoNombre($pqrsfData['servicio']); ?></strong>
                    </p>
                    <p><?php echo Lang::get('satisfaction_request_message'); ?></p>
                </div>

                <form action="/satisfaccion" method="post" id="formulario-conformidad">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($encodedId); ?>">
                    
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 border-b-2 border-gray-400 dark:border-gray-700 pb-2 mb-6">
                        <?php echo Lang::get('satisfaction_response_title'); ?>
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
                                <?php echo Lang::get('satisfaction_yes'); ?>
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
                                <?php echo Lang::get('satisfaction_no'); ?>
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
                                <?php echo Lang::get('satisfaction_reason_label'); ?> <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <div class="flex justify-end text-sm mt-2">
                            <span id="caracteres-restantes" class="text-gray-600 dark:text-gray-400">240</span>
                            <span class="text-gray-600 dark:text-gray-400 ml-1"><?php echo Lang::get('satisfaction_characters_remaining'); ?></span>
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
                        <span><?php echo Lang::get('satisfaction_submit_button'); ?></span>
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
