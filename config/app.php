<?php

declare(strict_types=1);

return [
    'default_lang' => 'es',
    'supported_langs' => ['es', 'en'],
    
    // Configuración de correo electrónico
    'mail' => [
        'enabled' => true, // Activar/desactivar el envío de correos
        'mailer' => $_ENV['MAIL_MAILER'] ?? 'smtp',
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
        'port' => (int)($_ENV['MAIL_PORT'] ?? 587),
        'username' => $_ENV['MAIL_USERNAME'] ?? 'pqrsdf@clinicadeoccidente.com',
        'password' => $_ENV['MAIL_PASSWORD'] ?? 'ecgz wvfw ilfe ampk',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@clinicadeoccidente.com',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'PQRSF - Sistema de manifestaciones Clínica de Occidente',
        ],
    ],
];