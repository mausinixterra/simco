# ============================================
# Script de Verificación de Instalación
# SIMCO - Windows PowerShell
# ============================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "SIMCO - Verificación de Instalación" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$success = "Green"
$errorColor = "Red"
$warning = "Yellow"
$info = "Cyan"
$totalChecks = 0
$passedChecks = 0
$failedChecks = 0

function Test-Item {
    param (
        [string]$Name,
        [scriptblock]$Test,
        [string]$ErrorMessage = "",
        [bool]$Critical = $false
    )
    
    $script:totalChecks++
    Write-Host "Verificando: $Name..." -NoNewline
    
    try {
        $result = & $Test
        if ($result) {
            Write-Host " ✓" -ForegroundColor $success
            $script:passedChecks++
            return $true
        } else {
            if ($Critical) {
                Write-Host " ✗ CRÍTICO" -ForegroundColor $errorColor
            } else {
                Write-Host " ⚠" -ForegroundColor $warning
            }
            $script:failedChecks++
            if ($ErrorMessage) {
                Write-Host "  → $ErrorMessage" -ForegroundColor $warning
            }
            return $false
        }
    } catch {
        Write-Host " ✗ ERROR" -ForegroundColor $errorColor
        $script:failedChecks++
        Write-Host "  → $_" -ForegroundColor $errorColor
        return $false
    }
}

Write-Host "1. VERIFICACIÓN DE SOFTWARE REQUERIDO" -ForegroundColor $info
Write-Host "─────────────────────────────────────────" -ForegroundColor $info

# PHP
$null = Test-Item -Name "PHP 8.2+ instalado" -Test {
    # -join: php -v devuelve varias líneas. Con un array a la izquierda, -match
    # actúa como filtro y NO rellena $Matches, lo que hacía fallar a $matches[1].
    $phpVersion = (& php -v) -join "`n"
    if ($phpVersion -match "PHP (\d+)\.(\d+)") {
        # Comparar major/minor por separado: [double]"8.10" daría 8.1 < 8.2.
        $major = [int]$matches[1]
        $minor = [int]$matches[2]
        return ($major -gt 8) -or ($major -eq 8 -and $minor -ge 2)
    }
    return $false
} -ErrorMessage "Instala PHP 8.2+ desde https://windows.php.net/" -Critical $true

# Composer
$null = Test-Item -Name "Composer instalado" -Test {
    $null -ne (Get-Command composer -ErrorAction SilentlyContinue)
} -ErrorMessage "Instala Composer desde https://getcomposer.org/" -Critical $true

# Node.js
$null = Test-Item -Name "Node.js instalado" -Test {
    $null -ne (Get-Command node -ErrorAction SilentlyContinue)
} -ErrorMessage "Instala Node.js desde https://nodejs.org/"

# NPM
$null = Test-Item -Name "NPM instalado" -Test {
    $null -ne (Get-Command npm -ErrorAction SilentlyContinue)
} -ErrorMessage "Instala Node.js (incluye NPM)"

Write-Host ""
Write-Host "2. VERIFICACIÓN DE ARCHIVOS DEL PROYECTO" -ForegroundColor $info
Write-Host "─────────────────────────────────────────" -ForegroundColor $info

$requiredFiles = @(
    @{Path="composer.json"; Name="composer.json"},
    @{Path="package.json"; Name="package.json"},
    @{Path="tailwind.config.js"; Name="tailwind.config.js"},
    @{Path=".env.example"; Name=".env.example"},
    @{Path="public_html\index.php"; Name="public_html/index.php"},
    @{Path="app\Config\Database.php"; Name="app/Config/Database.php"},
    @{Path="app\Controllers\PqrsfController.php"; Name="app/Controllers/PqrsfController.php"},
    @{Path="routes\web.php"; Name="routes/web.php"}
)

foreach ($file in $requiredFiles) {
    $null = Test-Item -Name $file.Name -Test {
        Test-Path $file.Path
    } -ErrorMessage "Archivo faltante: $($file.Path)" -Critical $true
}

Write-Host ""
Write-Host "3. VERIFICACIÓN DE CONFIGURACIÓN" -ForegroundColor $info
Write-Host "─────────────────────────────────────────" -ForegroundColor $info

# .env
$envExists = Test-Item -Name "Archivo .env existe" -Test {
    Test-Path ".env"
} -ErrorMessage "Copia .env.example a .env y configúralo"

if ($envExists) {
    $envContent = Get-Content ".env" -Raw
    
    $null = Test-Item -Name ".env - DB_HOST configurado" -Test {
        $envContent -match "DB_HOST=.+" -and $envContent -notmatch "DB_HOST=$"
    } -ErrorMessage "Configura DB_HOST en .env"
    
    $null = Test-Item -Name ".env - DB_DATABASE configurado" -Test {
        $envContent -match "DB_DATABASE=.+" -and $envContent -notmatch "DB_DATABASE=$"
    } -ErrorMessage "Configura DB_DATABASE en .env"
    
    $null = Test-Item -Name ".env - MAIL_USERNAME configurado" -Test {
        $envContent -match "MAIL_USERNAME=.+" -and $envContent -notmatch "MAIL_USERNAME=$"
    } -ErrorMessage "Configura MAIL_USERNAME en .env para envío de correos"
}

Write-Host ""
Write-Host "4. VERIFICACIÓN DE DEPENDENCIAS" -ForegroundColor $info
Write-Host "─────────────────────────────────────────" -ForegroundColor $info

$null = Test-Item -Name "Vendor instalado (Composer)" -Test {
    Test-Path "vendor\autoload.php"
} -ErrorMessage "Ejecuta: composer install"

$null = Test-Item -Name "Node modules instalado" -Test {
    Test-Path "node_modules"
} -ErrorMessage "Ejecuta: npm install (solo si vas a compilar CSS)"

Write-Host ""
Write-Host "5. VERIFICACIÓN DE DIRECTORIOS" -ForegroundColor $info
Write-Host "─────────────────────────────────────────" -ForegroundColor $info

$requiredDirs = @(
    @{Path="public_html\Anexos"; Name="public_html/Anexos"},
    @{Path="app\Controllers"; Name="app/Controllers"},
    @{Path="app\Models"; Name="app/Models"},
    @{Path="app\Services"; Name="app/Services"},
    @{Path="app\Views"; Name="app/Views"}
)

foreach ($dir in $requiredDirs) {
    $null = Test-Item -Name $dir.Name -Test {
        Test-Path $dir.Path
    } -ErrorMessage "Directorio faltante: $($dir.Path)" -Critical $true
}

Write-Host ""
Write-Host "6. VERIFICACIÓN DE ASSETS" -ForegroundColor $info
Write-Host "─────────────────────────────────────────" -ForegroundColor $info

$null = Test-Item -Name "CSS fuente existe" -Test {
    Test-Path "resources\css\input.css"
} -ErrorMessage "Archivo fuente de CSS faltante"

$null = Test-Item -Name "CSS compilado existe" -Test {
    Test-Path "public_html\assets\css\output.css"
} -ErrorMessage "Ejecuta: npx @tailwindcss/cli -i ./resources/css/input.css -o ./public_html/assets/css/output.css --minify"

$null = Test-Item -Name "JavaScript existe" -Test {
    Test-Path "public_html\assets\js\form-validation.js"
} -ErrorMessage "Archivos JavaScript faltantes"

$null = Test-Item -Name "Imágenes existen" -Test {
    (Test-Path "public_html\assets\img\logo_cdo.png") -and 
    (Test-Path "public_html\assets\img\logo_header_light.png")
} -ErrorMessage "Imágenes del proyecto faltantes"

Write-Host ""
Write-Host "7. VERIFICACIÓN DE EXTENSIONES PHP" -ForegroundColor $info
Write-Host "─────────────────────────────────────────" -ForegroundColor $info

$phpExtensions = @("pdo_pgsql", "mbstring", "curl", "xml", "zip")
foreach ($ext in $phpExtensions) {
    $null = Test-Item -Name "PHP extensión: $ext" -Test {
        $output = & php -m 2>&1
        $output -match $ext
    } -ErrorMessage "Habilita la extensión $ext en php.ini"
}

Write-Host ""
Write-Host "8. VERIFICACIÓN OPCIONAL" -ForegroundColor $info
Write-Host "─────────────────────────────────────────" -ForegroundColor $info

$null = Test-Item -Name "PostgreSQL cliente instalado (psql)" -Test {
    $null -ne (Get-Command psql -ErrorAction SilentlyContinue)
} -ErrorMessage "Instala PostgreSQL para gestión de BD (opcional)"

$null = Test-Item -Name "Git instalado" -Test {
    $null -ne (Get-Command git -ErrorAction SilentlyContinue)
} -ErrorMessage "Instala Git para control de versiones (opcional)"

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "RESUMEN DE VERIFICACIÓN" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "Total de verificaciones: $totalChecks" -ForegroundColor White
Write-Host "Pasadas: $passedChecks" -ForegroundColor $success
Write-Host "Fallidas: $failedChecks" -ForegroundColor $(if ($failedChecks -eq 0) { $success } else { $warning })
Write-Host ""

if ($failedChecks -eq 0) {
    Write-Host "✅ INSTALACIÓN COMPLETA" -ForegroundColor $success
    Write-Host "El proyecto está listo para usar" -ForegroundColor $success
    Write-Host ""
    Write-Host "🚀 Próximos pasos:" -ForegroundColor $info
    Write-Host "   1. Inicia el servidor PHP:" -ForegroundColor White
    Write-Host "      php -S localhost:8000 -t public_html public_html/server.php" -ForegroundColor Gray
    Write-Host ""
    Write-Host "   2. Abre en el navegador:" -ForegroundColor White
    Write-Host "      http://localhost:8000" -ForegroundColor Gray
    Write-Host ""
    Write-Host "   3. Para compilar CSS en desarrollo:" -ForegroundColor White
    Write-Host "      npx @tailwindcss/cli -i ./resources/css/input.css -o ./public_html/assets/css/output.css --watch" -ForegroundColor Gray
} else {
    Write-Host "⚠️ INSTALACIÓN INCOMPLETA" -ForegroundColor $warning
    Write-Host "Por favor, revisa los errores arriba y corrígelos" -ForegroundColor $warning
    Write-Host ""
    Write-Host "📖 Consulta README.md para más información" -ForegroundColor $info
}

Write-Host ""
Write-Host "Presiona Enter para salir..." -ForegroundColor Gray
Read-Host
