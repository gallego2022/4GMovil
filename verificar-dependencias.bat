@echo off
echo ==========================================
echo Verificacion de Dependencias - 4GMovil
echo ==========================================
echo.

echo 1. Verificando dependencias de Composer (PHP)...
echo ----------------------------------------
composer show --installed | findstr /i "dompdf laravel intervention"
echo.

echo 2. Verificando si DomPDF esta instalado...
echo ----------------------------------------
composer show dompdf/dompdf 2>nul
if %errorlevel% equ 0 (
    echo [OK] DomPDF esta instalado
) else (
    echo [ERROR] DomPDF NO esta instalado
    echo   Ejecuta: composer require dompdf/dompdf
)
echo.

echo 3. Verificando dependencias de npm (Node.js)...
echo ----------------------------------------
if exist node_modules (
    echo [OK] node_modules existe
    if exist node_modules\sweetalert2 (
        echo [OK] SweetAlert2 esta instalado
    ) else (
        echo [ERROR] SweetAlert2 NO esta instalado
        echo   Ejecuta: npm install
    )
) else (
    echo [ERROR] node_modules NO existe
    echo   Ejecuta: npm install
)
echo.

echo 4. Verificando autoload de Composer...
echo ----------------------------------------
composer dump-autoload --no-interaction
if %errorlevel% equ 0 (
    echo [OK] Autoload actualizado correctamente
) else (
    echo [ERROR] Error al actualizar autoload
)
echo.

echo 5. Verificando archivos de configuracion...
echo ----------------------------------------
if exist composer.json (
    echo [OK] composer.json existe
) else (
    echo [ERROR] composer.json NO existe
)

if exist package.json (
    echo [OK] package.json existe
) else (
    echo [ERROR] package.json NO existe
)
echo.

echo ==========================================
echo Verificacion completada
echo ==========================================
pause

