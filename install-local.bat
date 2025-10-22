@echo off
REM ========================================
REM INSTALACIÓN LOCAL - 4GMovil
REM Sistema: Windows
REM Entorno: Desarrollo Local
REM ========================================

echo.
echo ========================================
echo INSTALACIÓN LOCAL - 4GMovil
echo ========================================
echo.

REM Verificar si PHP está instalado
echo [1/10] Verificando PHP...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP no está instalado o no está en el PATH
    echo Por favor instala PHP 8.2+ desde: https://windows.php.net/download/
    pause
    exit /b 1
)
echo ✓ PHP encontrado

REM Verificar si Composer está instalado
echo [2/10] Verificando Composer...
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Composer no está instalado
    echo Por favor instala Composer desde: https://getcomposer.org/download/
    pause
    exit /b 1
)
echo ✓ Composer encontrado

REM Verificar si Node.js está instalado (opcional)
echo [3/10] Verificando Node.js...
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Node.js no está instalado
    echo Para compilar assets, instala Node.js desde: https://nodejs.org/
    set SKIP_NODE=1
) else (
    echo ✓ Node.js encontrado
    set SKIP_NODE=0
)

REM Crear directorios necesarios antes de instalar dependencias
echo [4/10] Creando directorios necesarios...
if not exist storage\framework mkdir storage\framework
if not exist storage\framework\cache mkdir storage\framework\cache
if not exist storage\framework\cache\data mkdir storage\framework\cache\data
if not exist storage\framework\sessions mkdir storage\framework\sessions
if not exist storage\framework\views mkdir storage\framework\views
if not exist storage\logs mkdir storage\logs
if not exist bootstrap\cache mkdir bootstrap\cache
echo ✓ Directorios necesarios creados

REM Instalar dependencias PHP
echo Instalando dependencias PHP...
composer install
if %errorlevel% neq 0 (
    echo ERROR: Falló la instalación de dependencias PHP
    pause
    exit /b 1
)
echo ✓ Dependencias PHP instaladas

REM Instalar dependencias Node.js (si está disponible)
if %SKIP_NODE%==0 (
    echo [5/10] Instalando dependencias Node.js...
    npm install
    if %errorlevel% neq 0 (
        echo ADVERTENCIA: Falló la instalación de dependencias Node.js
        echo Continuando sin compilar assets...
    ) else (
        echo ✓ Dependencias Node.js instaladas
    )
) else (
    echo [5/10] Saltando instalación de Node.js...
)

REM Copiar archivo de configuración
echo [6/10] Configurando archivo de entorno...
if not exist .env (
    copy env.local.example .env
    echo ✓ Archivo .env creado desde env.local.example
) else (
    echo ✓ Archivo .env ya existe
)

REM Configurar variables de entorno para desarrollo local
echo [7/10] Configurando variables de entorno...
echo.
echo Configurando para desarrollo local...
echo CACHE_DRIVER=file
echo CACHE_PREFIX=4gmovil_cache_
echo APP_ENV=local
echo.

REM Generar clave de aplicación
echo [8/10] Generando clave de aplicación...
php artisan key:generate
if %errorlevel% neq 0 (
    echo ERROR: Falló la generación de clave
    pause
    exit /b 1
)
echo ✓ Clave de aplicación generada

REM Configurar base de datos
echo [9/10] Configurando base de datos...
echo.
echo IMPORTANTE: Configura tu base de datos en el archivo .env
echo.
echo Ejemplo de configuración:
echo DB_CONNECTION=mysql
echo DB_HOST=127.0.0.1
echo DB_PORT=3306
echo DB_DATABASE=4gmovil
echo DB_USERNAME=root
echo DB_PASSWORD=tu_password
echo.
echo Después de configurar la base de datos, ejecuta:
echo php artisan migrate
echo php artisan db:seed
echo.

REM Configurar caché
echo [10/10] Configurando sistema de caché...
php artisan cache:table
php artisan cache:setup-cloud --driver=file
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Falló la configuración de caché
    echo Continuando...
) else (
    echo ✓ Sistema de caché configurado
)

REM Compilar assets (si Node.js está disponible)
if %SKIP_NODE%==0 (
    echo.
    echo Compilando assets...
    npm run build
    if %errorlevel% neq 0 (
        echo ADVERTENCIA: Falló la compilación de assets
        echo Puedes compilar manualmente con: npm run dev
    ) else (
        echo ✓ Assets compilados
    )
)

echo.
echo ========================================
echo INSTALACIÓN COMPLETADA
echo ========================================
echo.
echo Próximos pasos:
echo 1. Configura tu base de datos en .env
echo 2. Ejecuta: php artisan migrate
echo 3. Ejecuta: php artisan db:seed
echo 4. Inicia el servidor: php artisan serve
echo 5. Accede a: http://localhost:8000
echo.
echo Comandos útiles:
echo - php artisan cache:clear
echo - php artisan test:cache-performance-fallback
echo - php artisan cache:configure-environment
echo.
pause
