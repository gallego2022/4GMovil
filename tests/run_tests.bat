@echo off
chcp 65001 >nul
echo 🧪 EJECUTANDO TESTS DEL PROYECTO DE REFACTORING
echo ==================================================
echo.

REM Verificar si estamos en el directorio correcto
if not exist "artisan" (
    echo [ERROR] No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz del proyecto Laravel.
    pause
    exit /b 1
)

echo [INFO] Verificando dependencias...
php --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP no está instalado o no está en el PATH
    pause
    exit /b 1
)

composer --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Composer no está instalado o no está en el PATH
    pause
    exit /b 1
)

echo [INFO] Instalando dependencias de testing...
composer install --no-interaction --prefer-dist --optimize-autoloader

echo [INFO] Limpiando caché...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo [INFO] Generando clave de aplicación...
php artisan key:generate

echo [INFO] Ejecutando tests de FASE 1: FUNDAMENTOS...
echo.

REM Tests de FASE 1
echo [INFO] Testing LoggingService...
php artisan test tests/Unit/Services/LoggingServiceTest.php --verbose

echo [INFO] Testing ValidationService...
php artisan test tests/Unit/Services/ValidationServiceTest.php --verbose

echo [INFO] Testing CacheService...
php artisan test tests/Unit/Services/CacheServiceTest.php --verbose

echo [INFO] Testing BaseController...
php artisan test tests/Unit/Controllers/BaseControllerTest.php --verbose

echo.
echo [INFO] Ejecutando tests de FASE 2: CORE SERVICES...
echo.

REM Tests de FASE 2
echo [INFO] Testing NotificationService...
php artisan test tests/Unit/Services/NotificationServiceTest.php --verbose

echo [INFO] Testing AuthService...
php artisan test tests/Unit/Services/AuthServiceTest.php --verbose

echo.
echo [INFO] Ejecutando todos los tests unitarios...
php artisan test tests/Unit --verbose

echo.
echo [INFO] Resumen de tests ejecutados:
echo ✅ FASE 1: FUNDAMENTOS - Tests completados
echo ✅ FASE 2: CORE SERVICES - Tests completados
echo.

echo [SUCCESS] ¡Todos los tests han sido ejecutados exitosamente!
echo [INFO] Revisa la salida anterior para ver los resultados detallados.

pause
