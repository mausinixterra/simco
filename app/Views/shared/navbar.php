<?php
use App\Core\Lang;

/**
 * Navbar compartido.
 *
 * Variables opcionales que la vista puede definir antes de incluirlo:
 *   $navbarHomeUrl      URL del logo (por defecto '/')
 *   $navbarShowLangMenu Mostrar el menú de idioma (por defecto true)
 *   $navbarLangEsUrl    URL para cambiar a español (por defecto '?lang=es')
 *   $navbarLangEnUrl    URL para cambiar a inglés (por defecto '?lang=en')
 */
$navbarHomeUrl      = $navbarHomeUrl      ?? '/';
$navbarShowLangMenu = $navbarShowLangMenu ?? true;
$navbarLangEsUrl    = $navbarLangEsUrl    ?? '?lang=es';
$navbarLangEnUrl    = $navbarLangEnUrl    ?? '?lang=en';
?>
<nav class="bg-white dark:bg-gray-800 shadow">
    <div class="container mx-auto px-6 py-3 flex justify-end items-center space-x-4">
        <!-- Logo -->
        <div class="flex-grow">
            <a href="<?= htmlspecialchars($navbarHomeUrl, ENT_QUOTES, 'UTF-8') ?>" class="flex items-center">
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

            <?php if ($navbarShowLangMenu): ?>
            <!-- Language Menu -->
            <div class="relative">
                <button id="lang-menu-button" class="flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none transition duration-300 ease-in-out transform hover:scale-110 focus:scale-110 cursor-pointer">
                    <svg width="24" height="24" viewBox="0 0 512 512" class="fill-current">
                        <path d="M56 40A48 48 0 008 88v160a48 48 0 0040 47.33V336a8 8 0 0013.66 5.66L107.31 296H184v80a48 48 0 0048 48h172.69l45.65 45.66A8 8 0 00464 464v-40.67A48 48 0 00504 376V216a48 48 0 00-48-48H328V88a48 48 0 00-48-48zM56 56h224a32 32 0 0132 32v160a32 32 0 01-32 32H104a8 8 0 00-5.66 2.34L64 316.69V288a8 8 0 00-8-8 32 32 0 01-32-32V88a32 32 0 0132-32zm103.77 48a8 8 0 00-7.77 8v16H96a8 8 0 000 16h32.28c1.51 21.91 9.18 41.75 20.93 57.03-10.5 9.41-23.34 14.97-37.21 14.97a8 8 0 000 16c18 0 34.62-7.17 48-19.25 13.38 12.08 30 19.25 48 19.25a8 8 0 000-16c-13.87 0-26.71-5.56-37.21-14.97 11.75-15.28 19.42-35.12 20.93-57.03H224a8 8 0 000-16h-56v-16a8 8 0 00-8.23-8zm-15.45 40h31.36A90.35 90.35 0 01160 188.83 90.35 90.35 0 01144.32 144zM328 184h128a32 32 0 0132 32v160a32 32 0 01-32 32 8 8 0 00-8 8v28.69l-34.34-34.35A8 8 0 00408 408H232a32 32 0 01-32-32v-80h80a48 48 0 0048-48zM352.22 240a8 8 0 00-7.75 5.31l-40 112a8 8 0 1015.06 5.38L331.92 328h40.16l12.39 34.69a8 8 0 1015.06-5.38l-40-112a8 8 0 00-7.31-5.31zM352 271.79L366.36 312h-28.72z"/>
                    </svg>
                </button>
                <div id="lang-menu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-20 hidden">
                    <a href="<?= htmlspecialchars($navbarLangEsUrl, ENT_QUOTES, 'UTF-8') ?>" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 <?=($_SESSION['lang']??'es')==='es'?'font-bold text-blue-600 dark:text-blue-400':''?>">
                        <?=Lang::get('lang_es')?>
                    </a>
                    <a href="<?= htmlspecialchars($navbarLangEnUrl, ENT_QUOTES, 'UTF-8') ?>" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 <?=($_SESSION['lang']??'es')==='en'?'font-bold text-blue-600 dark:text-blue-400':''?>">
                        <?=Lang::get('lang_en')?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Navbar functionality: theme toggle and language menu -->
<script src="/assets/js/navbar.js"></script>
