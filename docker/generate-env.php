<?php

declare(strict_types=1);

/**
 * Genera el archivo .env a partir de las variables de entorno del contenedor.
 *
 * Hace falta porque public_html/index.php carga la configuración con
 * Dotenv::createImmutable(ROOT_PATH)->load(), que lanza una excepción si el
 * archivo no existe. En Render la configuración se define como variables de
 * entorno, no como archivo.
 *
 * El escapado se hace aquí, en PHP, y no en el shell: los valores pueden
 * contener comillas, barras invertidas, espacios o el carácter '$', que en
 * bash son fáciles de corromper.
 *
 * Uso: php generate-env.php /ruta/al/.env
 */

$destino = $argv[1] ?? '/var/www/html/.env';

/**
 * Devuelve la variable de entorno o null si no está definida o está vacía.
 */
function env(string $clave): ?string
{
    $valor = getenv($clave);

    if ($valor === false) {
        return null;
    }

    $valor = trim($valor);

    return $valor === '' ? null : $valor;
}

/**
 * Formatea una línea KEY="valor" escapando lo que phpdotenv interpreta
 * dentro de comillas dobles: la barra invertida y la comilla doble.
 */
function linea(string $clave, ?string $valor): string
{
    $valor = $valor ?? '';
    $escapado = str_replace(['\\', '"'], ['\\\\', '\\"'], $valor);

    return $clave . '="' . $escapado . '"' . PHP_EOL;
}

// ---------------------------------------------------------------------------
// Credenciales de base de datos
//
// Si Render provee DATABASE_URL (su PostgreSQL gestionado) y no se han
// definido las DB_* por separado, se descompone con parse_url(), que maneja
// correctamente contraseñas con '@' o ':' y los valores URL-encoded.
// ---------------------------------------------------------------------------
$dbHost     = env('DB_HOST');
$dbPort     = env('DB_PORT');
$dbDatabase = env('DB_DATABASE');
$dbUsername = env('DB_USERNAME');
$dbPassword = env('DB_PASSWORD');

$databaseUrl = env('DATABASE_URL');

if ($databaseUrl !== null && $dbHost === null) {
    $partes = parse_url($databaseUrl);

    if ($partes === false || !isset($partes['host'])) {
        fwrite(STDERR, "[generate-env] AVISO: DATABASE_URL no se pudo interpretar.\n");
    } else {
        fwrite(STDOUT, "[generate-env] Derivando DB_* desde DATABASE_URL\n");

        $dbHost     = $partes['host'];
        $dbPort     = isset($partes['port']) ? (string) $partes['port'] : $dbPort;
        $dbUsername = isset($partes['user']) ? rawurldecode($partes['user']) : $dbUsername;
        $dbPassword = isset($partes['pass']) ? rawurldecode($partes['pass']) : $dbPassword;

        if (isset($partes['path'])) {
            $nombreBase = ltrim($partes['path'], '/');
            $dbDatabase = $nombreBase !== '' ? $nombreBase : $dbDatabase;
        }
    }
}

// ---------------------------------------------------------------------------
// Composición del archivo
// ---------------------------------------------------------------------------
$contenido  = '# Generado por generate-env.php en el arranque del contenedor.' . PHP_EOL;
$contenido .= '# No editar a mano: se reescribe en cada despliegue.' . PHP_EOL;
$contenido .= '# La configuracion real vive en las variables de entorno del servicio.' . PHP_EOL;
$contenido .= PHP_EOL;

$contenido .= linea('DB_HOST', $dbHost);
$contenido .= linea('DB_PORT', $dbPort ?? '5432');
$contenido .= linea('DB_DATABASE', $dbDatabase);
$contenido .= linea('DB_USERNAME', $dbUsername);
$contenido .= linea('DB_PASSWORD', $dbPassword);
$contenido .= PHP_EOL;

$contenido .= linea('APP_ENV', env('APP_ENV') ?? 'production');
$contenido .= linea('APP_DEBUG', env('APP_DEBUG') ?? 'false');
$contenido .= PHP_EOL;

$contenido .= linea('MAIL_MAILER', env('MAIL_MAILER') ?? 'smtp');
$contenido .= linea('MAIL_HOST', env('MAIL_HOST'));
$contenido .= linea('MAIL_PORT', env('MAIL_PORT') ?? '587');
$contenido .= linea('MAIL_USERNAME', env('MAIL_USERNAME'));
$contenido .= linea('MAIL_PASSWORD', env('MAIL_PASSWORD'));
$contenido .= linea('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION') ?? 'tls');
$contenido .= linea('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS') ?? 'noreply@simco.com');
$contenido .= linea('MAIL_FROM_NAME', env('MAIL_FROM_NAME') ?? 'SIMCO - Sistema de PQRSF');

if (file_put_contents($destino, $contenido) === false) {
    fwrite(STDERR, "[generate-env] ERROR: no se pudo escribir {$destino}\n");
    exit(1);
}

@chmod($destino, 0600);

// ---------------------------------------------------------------------------
// Aviso temprano: sin estas variables, Database::getInstance() renderiza la
// página de error y hace exit, así que el sitio responde 503 en cada petición.
// ---------------------------------------------------------------------------
$faltantes = [];
foreach (['DB_HOST' => $dbHost, 'DB_DATABASE' => $dbDatabase, 'DB_USERNAME' => $dbUsername] as $clave => $valor) {
    if ($valor === null || $valor === '') {
        $faltantes[] = $clave;
    }
}

if ($faltantes !== []) {
    fwrite(STDERR, '[generate-env] AVISO: sin valor -> ' . implode(', ', $faltantes)
        . '. La aplicacion respondera 503 hasta que se configuren.' . PHP_EOL);
} else {
    fwrite(STDOUT, "[generate-env] .env generado correctamente\n");
}
