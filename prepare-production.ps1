# ============================================
# Script de Preparación para Producción
# SIMCO - Windows PowerShell
# ============================================
# Construye el paquete de producción en un directorio APARTE.
# El árbol de trabajo NO se modifica: vendor/ conserva las dependencias de
# desarrollo, node_modules/ se queda donde está y el output.css sin minificar
# no se toca. Puedes seguir desarrollando inmediatamente después.
# ============================================

param(
    # Directorio donde se construye el paquete. Se borra y recrea en cada ejecución.
    [string]$Destino = "build\simco-produccion",
    # Omitir la creación del ZIP (útil si vas a subir por SFTP o git).
    [switch]$SinZip
)

$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "SIMCO - Preparación para Producción" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Colores
$success = "Green"
$errorColor = "Red"
$warning = "Yellow"
$info = "Cyan"

# Verificar que estamos en el directorio correcto
if (-Not (Test-Path "composer.json")) {
    Write-Host "✗ Error: Este script debe ejecutarse desde el directorio raíz del proyecto" -ForegroundColor $errorColor
    exit 1
}

$raiz = (Get-Location).Path
$rutaDestino = Join-Path $raiz $Destino

Write-Host "✓ Directorio correcto" -ForegroundColor $success
Write-Host "  Origen : $raiz" -ForegroundColor Gray
Write-Host "  Destino: $rutaDestino" -ForegroundColor Gray
Write-Host ""

# ============================================
# 1. Verificar archivos necesarios
# ============================================
Write-Host "1. Verificando archivos necesarios..." -ForegroundColor $info
$requiredFiles = @("composer.json", "composer.lock", "package.json", "tailwind.config.js", ".env.example", "public_html\index.php")
foreach ($file in $requiredFiles) {
    if (Test-Path $file) {
        Write-Host "  ✓ $file existe" -ForegroundColor $success
    } else {
        Write-Host "  ✗ $file no encontrado" -ForegroundColor $errorColor
        exit 1
    }
}
Write-Host ""

# ============================================
# 2. Preparar el directorio de destino
# ============================================
Write-Host "2. Preparando directorio de destino..." -ForegroundColor $info
if (Test-Path $rutaDestino) {
    Write-Host "  Limpiando build anterior..." -ForegroundColor $info
    Remove-Item -Recurse -Force $rutaDestino
}
New-Item -ItemType Directory -Path $rutaDestino -Force | Out-Null
Write-Host "  ✓ $Destino listo" -ForegroundColor $success
Write-Host ""

# ============================================
# 3. Copiar el código fuente
# ============================================
Write-Host "3. Copiando código de la aplicación..." -ForegroundColor $info

$directorios = @("app", "config", "public_html", "resources", "routes")
foreach ($dir in $directorios) {
    Copy-Item -Path $dir -Destination $rutaDestino -Recurse -Force
    Write-Host "  ✓ $dir/" -ForegroundColor $success
}

# .env.example viaja como plantilla para el primer despliegue.
# El .env real NUNCA se copia: se crea en el servidor.
$archivos = @("composer.json", "composer.lock", ".htaccess", ".env.example")
foreach ($archivo in $archivos) {
    if (Test-Path $archivo) {
        Copy-Item -Path $archivo -Destination $rutaDestino -Force
        Write-Host "  ✓ $archivo" -ForegroundColor $success
    }
}

# Los adjuntos de usuarios no se despliegan: se quedan en el servidor.
$anexosCopiados = Join-Path $rutaDestino "public_html\Anexos"
if (Test-Path $anexosCopiados) {
    Remove-Item -Recurse -Force $anexosCopiados
}
New-Item -ItemType Directory -Path $anexosCopiados -Force | Out-Null
Write-Host "  ✓ public_html/Anexos/ vacío (los adjuntos no se despliegan)" -ForegroundColor $success
Write-Host ""

# ============================================
# 4. Instalar dependencias de producción EN EL DESTINO
# ============================================
Write-Host "4. Instalando dependencias de producción..." -ForegroundColor $info
Write-Host "  (tu vendor/ de desarrollo no se toca)" -ForegroundColor Gray
try {
    & composer install --no-dev --optimize-autoloader --no-interaction --working-dir="$rutaDestino"
    if ($LASTEXITCODE -ne 0) { throw "composer devolvió código $LASTEXITCODE" }

    if (-Not (Test-Path (Join-Path $rutaDestino "vendor\autoload.php"))) {
        throw "no se generó vendor/autoload.php"
    }
    Write-Host "  ✓ vendor/ instalado sin dependencias de desarrollo" -ForegroundColor $success
} catch {
    Write-Host "  ✗ Error al ejecutar Composer: $_" -ForegroundColor $errorColor
    Write-Host "  Asegúrate de tener Composer instalado: https://getcomposer.org/" -ForegroundColor $warning
    exit 1
}
Write-Host ""

# ============================================
# 5. Compilar TailwindCSS minificado EN EL DESTINO
# ============================================
Write-Host "5. Compilando TailwindCSS para producción..." -ForegroundColor $info
Write-Host "  (tu output.css de desarrollo no se toca)" -ForegroundColor Gray
$cssDestino = Join-Path $rutaDestino "public_html\assets\css\output.css"
try {
    & npx @tailwindcss/cli -i ./resources/css/input.css -o "$cssDestino" --minify
    if ($LASTEXITCODE -ne 0) { throw "npx devolvió código $LASTEXITCODE" }

    if (Test-Path $cssDestino) {
        $fileSizeKB = [math]::Round((Get-Item $cssDestino).Length / 1KB, 2)
        Write-Host "  ✓ CSS minificado ($fileSizeKB KB)" -ForegroundColor $success
    } else {
        throw "no se generó output.css"
    }
} catch {
    Write-Host "  ✗ Error al compilar CSS: $_" -ForegroundColor $errorColor
    Write-Host "  Necesitas Node.js y haber ejecutado 'npm install'" -ForegroundColor $warning
    exit 1
}
Write-Host ""

# ============================================
# 6. Generar el checklist de despliegue
# ============================================
Write-Host "6. Generando checklist de despliegue..." -ForegroundColor $info
$checklist = @'
═══════════════════════════════════════════════════════════
CHECKLIST DE DESPLIEGUE A PRODUCCIÓN - SIMCO
═══════════════════════════════════════════════════════════
Generado: {0}
Paquete:  {1}

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
'@ -f (Get-Date -Format "yyyy-MM-dd HH:mm:ss"), $Destino

$rutaChecklist = Join-Path (Split-Path $rutaDestino -Parent) "DEPLOYMENT-CHECKLIST.txt"
$utf8SinBom = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText($rutaChecklist, $checklist, $utf8SinBom)
Write-Host "  ✓ DEPLOYMENT-CHECKLIST.txt generado (fuera del paquete)" -ForegroundColor $success
Write-Host ""

# ============================================
# 7. Empaquetar
# ============================================
$rutaZip = Join-Path (Split-Path $rutaDestino -Parent) "simco-produccion.zip"
if ($SinZip) {
    Write-Host "7. ZIP omitido (-SinZip)" -ForegroundColor $info
} else {
    Write-Host "7. Empaquetando..." -ForegroundColor $info
    Write-Host "  Comprimiendo (puede tardar, vendor/ tiene muchos archivos)..." -ForegroundColor Gray
    if (Test-Path $rutaZip) { Remove-Item -Force $rutaZip }
    Compress-Archive -Path (Join-Path $rutaDestino "*") -DestinationPath $rutaZip
    $zipMB = [math]::Round((Get-Item $rutaZip).Length / 1MB, 2)
    Write-Host "  ✓ simco-produccion.zip ($zipMB MB)" -ForegroundColor $success
}
Write-Host ""

# ============================================
# 8. Resumen final
# ============================================
$archivosCopiados = (Get-ChildItem -Path $rutaDestino -Recurse -File).Count

Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "✅ PAQUETE LISTO" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Paquete:   $Destino  ($archivosCopiados archivos)" -ForegroundColor White
if (-Not $SinZip) {
    Write-Host "  ZIP:       $(Split-Path $rutaZip -Leaf)" -ForegroundColor White
}
Write-Host "  Checklist: $(Split-Path $rutaChecklist -Leaf)" -ForegroundColor White
Write-Host ""
Write-Host "🔧 Tu entorno de desarrollo NO se ha modificado:" -ForegroundColor $success
Write-Host "   - vendor/ conserva las dependencias de desarrollo" -ForegroundColor White
Write-Host "   - node_modules/ intacto" -ForegroundColor White
Write-Host "   - public_html/assets/css/output.css sin minificar" -ForegroundColor White
Write-Host "   - .env sin tocar" -ForegroundColor White
Write-Host ""
Write-Host "⚠️  EL .env NO VIAJA EN EL PAQUETE:" -ForegroundColor $warning
Write-Host "   Se crea UNA sola vez en el servidor, en el primer despliegue," -ForegroundColor Yellow
Write-Host "   a partir de .env.example." -ForegroundColor Yellow
Write-Host ""
Write-Host "   Si el proyecto YA está en producción, el servidor ya tiene el" -ForegroundColor Yellow
Write-Host "   suyo con las credenciales reales: NO lo vuelvas a crear ni a" -ForegroundColor Yellow
Write-Host "   subir. Sobrescribirlo rompe la conexión a la base de datos y" -ForegroundColor Yellow
Write-Host "   el envío de correos." -ForegroundColor Yellow
Write-Host ""
Write-Host "📋 Próximos pasos:" -ForegroundColor $info
Write-Host "   1. Revisa DEPLOYMENT-CHECKLIST.txt" -ForegroundColor White
Write-Host "   2. Sube el contenido del paquete al servidor" -ForegroundColor White
Write-Host "   3. Configura el document root y el .htaccess" -ForegroundColor White
Write-Host "   4. Verifica las tablas de la base de datos" -ForegroundColor White
Write-Host "   5. Activa SSL y prueba el sitio" -ForegroundColor White
Write-Host ""
Write-Host "   Guía completa: OPERACIONES.md" -ForegroundColor White
Write-Host ""
