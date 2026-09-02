#!/usr/bin/env bash
# ============================================================================
# Arranque del contenedor SIMCO
#
# 1. Genera el .env desde las variables de entorno (lo hace generate-env.php,
#    porque el escapado de valores es delicado y PHP lo resuelve mejor).
# 2. Fija el puerto en el que escucha Apache (Render inyecta PORT).
# 3. Asegura que el directorio de adjuntos exista y sea escribible.
# ============================================================================
set -euo pipefail

RAIZ="/var/www/html"
ARCHIVO_ENV="$RAIZ/.env"

log() { echo "[entrypoint] $*"; }

# ---------------------------------------------------------------------------
# 1. Configuración
#
# El .env se escribe en la raíz del proyecto, que está FUERA del DocumentRoot
# (/var/www/html/public_html), así que no es accesible por web.
# ---------------------------------------------------------------------------
if [ -f "$ARCHIVO_ENV" ] && [ "${SIMCO_CONSERVAR_ENV:-0}" = "1" ]; then
    log ".env existente conservado (SIMCO_CONSERVAR_ENV=1)"
else
    php /usr/local/bin/generate-env.php "$ARCHIVO_ENV"
    chown www-data:www-data "$ARCHIVO_ENV" 2>/dev/null || true
fi

# ---------------------------------------------------------------------------
# 2. Puerto
#
# Apache necesita el valor tanto en Listen como en el VirtualHost, y no acepta
# variables de entorno en esas directivas, así que se sustituye al arrancar.
# ---------------------------------------------------------------------------
PUERTO="${PORT:-10000}"
log "Apache escuchara en el puerto $PUERTO"

sed "s/__PORT__/${PUERTO}/g" \
    /etc/apache2/sites-available/000-default.conf.tpl \
    > /etc/apache2/sites-available/000-default.conf

echo "Listen ${PUERTO}" > /etc/apache2/ports.conf

# ---------------------------------------------------------------------------
# 3. Adjuntos
#
# Sin un disco persistente montado aquí, este directorio es EFIMERO: Render lo
# vacia en cada despliegue o reinicio. Ver la nota en OPERACIONES.md.
# ---------------------------------------------------------------------------
mkdir -p "$RAIZ/public_html/Anexos"
chown -R www-data:www-data "$RAIZ/public_html/Anexos" 2>/dev/null || true
chmod -R 775 "$RAIZ/public_html/Anexos" 2>/dev/null || true

log "Arrancando: $*"
exec "$@"
