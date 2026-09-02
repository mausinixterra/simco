#!/bin/bash

################################################################################
# Script de Preparación para Producción - SIMCO
#
# Construye el paquete de producción en un directorio APARTE.
# El árbol de trabajo NO se modifica: vendor/ conserva las dependencias de
# desarrollo, node_modules/ se queda donde está y el output.css sin minificar
# no se toca. Puedes seguir desarrollando inmediatamente después.
#
# Uso:  ./prepare-production.sh [--sin-zip] [--destino RUTA]
################################################################################

set -euo pipefail

# Colores
VERDE='\033[0;32m'
ROJO='\033[0;31m'
AMARILLO='\033[1;33m'
CYAN='\033[0;36m'
GRIS='\033[0;90m'
NC='\033[0m'

ok()    { echo -e "  ${VERDE}✓${NC} $1"; }
err()   { echo -e "  ${ROJO}✗${NC} $1"; }
warn()  { echo -e "  ${AMARILLO}⚠${NC} $1"; }
gris()  { echo -e "  ${GRIS}$1${NC}"; }

DESTINO="build/simco-produccion"
CREAR_ZIP=1

while [[ $# -gt 0 ]]; do
    case "$1" in
        --sin-zip)  CREAR_ZIP=0; shift ;;
        --destino)  DESTINO="$2"; shift 2 ;;
        *) err "Opción desconocida: $1"; exit 1 ;;
    esac
done

echo -e "${CYAN}========================================${NC}"
echo -e "${CYAN}SIMCO - Preparación para Producción${NC}"
echo -e "${CYAN}========================================${NC}"
echo ""

# Verificar que estamos en la raíz del proyecto
if [ ! -f "composer.json" ]; then
    err "Este script debe ejecutarse desde el directorio raíz del proyecto"
    exit 1
fi

RAIZ="$(pwd)"
RUTA_DESTINO="$RAIZ/$DESTINO"

ok "Directorio correcto"
gris "Origen : $RAIZ"
gris "Destino: $RUTA_DESTINO"
echo ""

# ============================================
# 1. Verificar archivos necesarios
# ============================================
echo -e "${CYAN}1. Verificando archivos necesarios...${NC}"
for f in composer.json composer.lock package.json tailwind.config.js .env.example public_html/index.php; do
    if [ -e "$f" ]; then
        ok "$f existe"
    else
        err "$f no encontrado"
        exit 1
    fi
done
echo ""

# ============================================
# 2. Preparar el directorio de destino
# ============================================
echo -e "${CYAN}2. Preparando directorio de destino...${NC}"
if [ -d "$RUTA_DESTINO" ]; then
    gris "Limpiando build anterior..."
    rm -rf "$RUTA_DESTINO"
fi
mkdir -p "$RUTA_DESTINO"
ok "$DESTINO listo"
echo ""

# ============================================
# 3. Copiar el código fuente
# ============================================
echo -e "${CYAN}3. Copiando código de la aplicación...${NC}"
for d in app config public_html resources routes; do
    cp -R "$d" "$RUTA_DESTINO/"
    ok "$d/"
done

# .env.example viaja como plantilla para el primer despliegue.
# El .env real NUNCA se copia: se crea en el servidor.
for f in composer.json composer.lock .htaccess .env.example; do
    if [ -f "$f" ]; then
        cp "$f" "$RUTA_DESTINO/"
        ok "$f"
    fi
done

# Los adjuntos de usuarios no se despliegan: se quedan en el servidor.
rm -rf "$RUTA_DESTINO/public_html/Anexos"
mkdir -p "$RUTA_DESTINO/public_html/Anexos"
ok "public_html/Anexos/ vacío (los adjuntos no se despliegan)"
echo ""

# ============================================
# 4. Instalar dependencias de producción EN EL DESTINO
# ============================================
echo -e "${CYAN}4. Instalando dependencias de producción...${NC}"
gris "(tu vendor/ de desarrollo no se toca)"
if ! command -v composer &> /dev/null; then
    err "Composer no está instalado: https://getcomposer.org/"
    exit 1
fi
composer install --no-dev --optimize-autoloader --no-interaction --working-dir="$RUTA_DESTINO"
if [ ! -f "$RUTA_DESTINO/vendor/autoload.php" ]; then
    err "No se generó vendor/autoload.php"
    exit 1
fi
ok "vendor/ instalado sin dependencias de desarrollo"
echo ""

# ============================================
# 5. Compilar TailwindCSS minificado EN EL DESTINO
# ============================================
echo -e "${CYAN}5. Compilando TailwindCSS para producción...${NC}"
gris "(tu output.css de desarrollo no se toca)"
if ! command -v npx &> /dev/null; then
    err "npx no está disponible (requiere Node.js): https://nodejs.org/"
    exit 1
fi
CSS_DESTINO="$RUTA_DESTINO/public_html/assets/css/output.css"
npx @tailwindcss/cli -i ./resources/css/input.css -o "$CSS_DESTINO" --minify
if [ ! -f "$CSS_DESTINO" ]; then
    err "No se generó output.css"
    exit 1
fi
ok "CSS minificado ($(du -h "$CSS_DESTINO" | cut -f1))"
echo ""

# ============================================
# 6. Generar el checklist de despliegue
# ============================================
echo -e "${CYAN}6. Generando checklist de despliegue...${NC}"
RUTA_CHECKLIST="$(dirname "$RUTA_DESTINO")/DEPLOYMENT-CHECKLIST.txt"
cat > "$RUTA_CHECKLIST" <<EOF
═══════════════════════════════════════════════════════════
CHECKLIST DE DESPLIEGUE A PRODUCCIÓN - SIMCO
═══════════════════════════════════════════════════════════
Generado: $(date '+%Y-%m-%d %H:%M:%S')
Paquete:  $DESTINO

CONTENIDO DEL PAQUETE
─────────────────────────────────────────
□ app/ config/ public_html/ resources/ routes/
□ vendor/ (sin dependencias de desarrollo)
□ composer.json, composer.lock, .htaccess
□ .env.example (plantilla, solo para el PRIMER despliegue)
□ public_html/Anexos/ vacío

NO incluido a propósito:
  - .env          -> se crea en el servidor, nunca se sube
  - node_modules/ -> no se necesita en producción
  - .git/, *.md, scripts de preparación

EL .env
─────────────────────────────────────────
□ ¿Primer despliegue? Crea el .env en el servidor a partir de
  .env.example y rellena las credenciales reales. Permiso 600.
□ ¿YA está en producción? El servidor ya tiene su .env con las
  credenciales reales: NO lo toques ni lo sobrescribas.
  Sobrescribirlo tumba la base de datos y el envío de correos.

EN EL SERVIDOR (cPanel)
─────────────────────────────────────────
□ PHP 8.2+ seleccionado
□ Extensiones activas: pdo_pgsql, pgsql, mbstring, curl, gd, xml, zip
□ php.ini: upload_max_filesize 25M, post_max_size 30M, memory_limit 256M
□ Document root apuntando a public_html/ del proyecto
□ .htaccess configurado en el document root
□ Permisos: 755 directorios, 644 archivos, 775 Anexos, 600 .env
□ .env apuntando a la base de datos existente
□ Verificadas las 9 tablas que usa la aplicación (ver OPERACIONES.md)
□ SSL activo y HTTPS forzado

VERIFICACIÓN POST-DESPLIEGUE
─────────────────────────────────────────
□ El sitio carga con candado válido
□ https://<dominio>/.env devuelve 403 o 404, nunca su contenido
□ Los desplegables del formulario traen datos
□ Se registra una PQRSF de prueba y llega el correo
□ Los adjuntos se guardan en public_html/Anexos/AAAA/MM/DD/
□ La encuesta de conformidad funciona
□ Cambio de idioma ES/EN y modo oscuro
□ Sin errores en cPanel -> Errors tras 24 horas

═══════════════════════════════════════════════════════════
Guía completa: OPERACIONES.md
═══════════════════════════════════════════════════════════
EOF
ok "DEPLOYMENT-CHECKLIST.txt generado (fuera del paquete)"
echo ""

# ============================================
# 7. Empaquetar
# ============================================
RUTA_ZIP="$(dirname "$RUTA_DESTINO")/simco-produccion.tar.gz"
if [ "$CREAR_ZIP" -eq 0 ]; then
    echo -e "${CYAN}7. Empaquetado omitido (--sin-zip)${NC}"
else
    echo -e "${CYAN}7. Empaquetando...${NC}"
    rm -f "$RUTA_ZIP"
    tar -czf "$RUTA_ZIP" -C "$RUTA_DESTINO" .
    ok "simco-produccion.tar.gz ($(du -h "$RUTA_ZIP" | cut -f1))"
fi
echo ""

# ============================================
# 8. Resumen final
# ============================================
NUM_ARCHIVOS=$(find "$RUTA_DESTINO" -type f | wc -l | tr -d ' ')

echo -e "${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${VERDE}✅ PAQUETE LISTO${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo "  Paquete:   $DESTINO  ($NUM_ARCHIVOS archivos)"
[ "$CREAR_ZIP" -eq 1 ] && echo "  Paquete:   $(basename "$RUTA_ZIP")"
echo "  Checklist: $(basename "$RUTA_CHECKLIST")"
echo ""
echo -e "${VERDE}🔧 Tu entorno de desarrollo NO se ha modificado:${NC}"
echo "   - vendor/ conserva las dependencias de desarrollo"
echo "   - node_modules/ intacto"
echo "   - public_html/assets/css/output.css sin minificar"
echo "   - .env sin tocar"
echo ""
echo -e "${AMARILLO}⚠️  EL .env NO VIAJA EN EL PAQUETE:${NC}"
echo -e "${AMARILLO}   Se crea UNA sola vez en el servidor, en el primer despliegue,${NC}"
echo -e "${AMARILLO}   a partir de .env.example.${NC}"
echo ""
echo -e "${AMARILLO}   Si el proyecto YA está en producción, el servidor ya tiene el${NC}"
echo -e "${AMARILLO}   suyo con las credenciales reales: NO lo vuelvas a crear ni a${NC}"
echo -e "${AMARILLO}   subir. Sobrescribirlo rompe la conexión a la base de datos y${NC}"
echo -e "${AMARILLO}   el envío de correos.${NC}"
echo ""
echo -e "${CYAN}📋 Próximos pasos:${NC}"
echo "   1. Revisa DEPLOYMENT-CHECKLIST.txt"
echo "   2. Sube el contenido del paquete al servidor"
echo "   3. Configura el document root y el .htaccess"
echo "   4. Verifica las tablas de la base de datos"
echo "   5. Activa SSL y prueba el sitio"
echo ""
echo "   Guía completa: OPERACIONES.md"
echo ""
