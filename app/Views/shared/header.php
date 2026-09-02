<?php use App\Core\Lang; ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'es', ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'SIMCO - PQRSF', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
    <link rel="stylesheet" href="/assets/css/output.css">
    
    <?php if (isset($useChoicesJs) && $useChoicesJs): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <style>
        .dark .choices__inner{background-color:#1f2937!important;border-color:#4b5563!important;color:#fff!important}
        .dark .choices__list--dropdown{background-color:#1f2937!important;border-color:#4b5563!important}
        .dark .choices__list--dropdown .choices__item--selectable{color:#e5e7eb!important}
        .dark .choices__list--dropdown .choices__item--selectable.is-highlighted{background-color:#374151!important;color:#fff!important}
        .dark .choices__input{background-color:#1f2937!important;color:#fff!important}
        .dark .choices__list--single .choices__item{color:#fff!important}
        .dark select{background-color:transparent;color:#fff;border-color:#4b5563}
        .dark select option{background-color:#1f2937;color:#fff}
        .dark select option:first-child{color:#9ca3af}
        .dark select:not([multiple]):not([size]){background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");background-position:right .5rem center;background-repeat:no-repeat;background-size:1.5em 1.5em;padding-right:2.5rem}
        select.peer.has-value~label,select.peer:focus~label{--tw-translate-y:-1.5rem;--tw-scale-x:.75;--tw-scale-y:.75;transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}
        select.peer.has-value~label{color:#3b82f6}
        .dark select.peer.has-value~label{color:#60a5fa}
    </style>
    <?php endif; ?>
    
    <?php if (isset($customStyles)): ?>
    <style><?= $customStyles ?></style>
    <?php endif; ?>
    
    <?php if (isset($customScripts)): ?>
    <script type="text/javascript"><?= $customScripts ?></script>
    <?php endif; ?>
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans">
