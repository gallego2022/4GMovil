@echo off
echo ========================================
echo   4GMovil - Instalacion Tradicional
echo ========================================
echo.

REM Verificar si PHP esta instalado
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP no esta instalado.
    echo Por favor instala XAMPP, WAMP o PHP manualmente
    echo XAMPP: https://www.apachefriends.org/
    echo WAMP: https://www.wampserver.com/
    pause
    exit /b 1
)

echo PHP encontrado. Continuando...
echo.

REM Verificar si Composer esta instalado
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Composer no esta instalado.
    echo Por favor instala Composer desde: https://getcomposer.org/
    pause
    exit /b 1
)

echo Composer encontrado. Continuando...
echo.

REM Verificar si Node.js esta instalado
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Node.js no esta instalado.
    echo Por favor instala Node.js desde: https://nodejs.org/
    pause
    exit /b 1
)

echo Node.js encontrado. Continuando...
echo.

REM Verificar si Git esta instalado
git --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Git no esta instalado.
    echo Por favor instala Git desde: https://git-scm.com/downloads
    pause
    exit /b 1
)

echo Git encontrado. Continuando...
echo.

REM Verificar si el proyecto ya existe
if exist "4gmovil" (
    echo El directorio 4gmovil ya existe.
    set /p choice="¿Deseas continuar y actualizar el proyecto? (s/n): "
    if /i "%choice%" neq "s" (
        echo Instalacion cancelada.
        pause
        exit /b 0
    )
    echo Actualizando proyecto...
    cd 4gmovil
    git pull origin main
) else (
    echo Clonando proyecto desde GitHub...
    git clone https://github.com/tu-usuario/4gmovil.git
    cd 4gmovil
)

echo.
echo Instalando dependencias PHP...
composer install

echo.
echo Instalando dependencias JavaScript...
npm install

echo.
echo Configurando variables de entorno...
if not exist ".env" (
    copy .env.example .env
    echo Archivo .env creado desde .env.example
) else (
    echo El archivo .env ya existe.
)

echo.
echo Configurando base de datos...
echo IMPORTANTE: Asegurate de que MySQL este corriendo
echo.

REM Verificar conexion a base de datos
php artisan tinker --execute="DB::connection()->getPdo();" >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: No se puede conectar a la base de datos.
    echo Por favor:
    echo 1. Asegurate de que MySQL este corriendo
    echo 2. Configura las credenciales en el archivo .env
    echo 3. Crea la base de datos: CREATE DATABASE 4gmovil_db;
    pause
    exit /b 1
)

echo Base de datos conectada correctamente.
echo.

echo Generando clave de aplicacion...
php artisan key:generate

echo.
echo Creando enlace simbolico para storage...
php artisan storage:link

echo.
echo Ejecutando migraciones y seeders...
php artisan migrate:fresh --seed

echo.
echo Construyendo assets...
npm run build

echo.
echo ========================================
echo   Instalacion completada!
echo ========================================
echo.
echo URLs de acceso:
echo - Aplicacion: http://127.0.0.1:8000
echo - Admin Panel: http://127.0.0.1:8000/admin
echo.
echo Credenciales por defecto:
echo - Admin: 4gmoviltest@gmail.com / Admin123!
echo.
echo Para iniciar el servidor:
echo php artisan serve
echo.
echo IMPORTANTE: Configura tus credenciales de Google OAuth y Stripe en el archivo .env
echo.
pause
