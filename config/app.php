<?php

declare(strict_types=1);

return [
    'default_lang' => 'es',
    'supported_langs' => ['es', 'en'],
    
    // Configuración de correo electrónico
    'mail' => [
        'enabled' => true, // Activar/desactivar el envío de correos
        'mailer' =>  'smtp',
        'host' =>'smtp.gmail.com',
        'port' => 587,
        'username' =>  'pqrsdf@clinicadeoccidente.com',
        'password' =>  'ecgz wvfw ilfe ampk',
        'encryption' => 'tls',
        'from' => [
            'address' => 'no-reply@clinicadeoccidente.com',
            'name' => 'PQRSF - Sistema de manifestaciones Clínica de Occidente',
        ],
    ],
];