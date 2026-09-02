<?php use App\Core\Lang; ?>
<footer class="m-4 mt-1 rounded-lg bg-white shadow-sm dark:bg-gray-800">
    <div class="mx-auto w-full max-w-screen-xl p-4 md:py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <div>
                    <img src="/assets/img/logo_title_dark.png" alt="Logo" class="block h-10 w-auto dark:hidden">
                    <img src="/assets/img/logo_title_light.png" alt="Logo" class="hidden h-10 w-auto dark:block">
                </div>
                <img src="/assets/img/logo_cdo.png" class="h-10" alt="Logo de la clínica de occidente" />
            </a>
            <div class="flex flex-col items-start sm:items-end text-sm text-gray-500 dark:text-gray-400">
                <a href="<?= Lang::get('privacy_policy_file_path') ?>" target="_blank" class="mb-1 transition-colors hover:underline hover:text-blue-600 dark:hover:text-blue-400"><?= Lang::get('footer_privacy') ?? 'Política de datos personales' ?></a>
                <span>© <?= date('Y') ?> <a href="https://clinicadeoccidente.com.co/" target="_blank" class="transition-colors hover:underline hover:text-blue-600 dark:hover:text-blue-400">Clínica de Occidente</a> <?= Lang::get('footer_rights') ?? 'Todos los derechos reservados' ?>.</span>
            </div>
        </div>
    </div>
</footer>
