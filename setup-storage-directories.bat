@echo off
REM ========================================
REM CREAR DIRECTORIOS DE STORAGE - 4GMovil
REM Script para crear todos los directorios necesarios
REM ========================================

echo.
echo ========================================
echo CREANDO DIRECTORIOS DE STORAGE
echo ========================================
echo.

echo [1/6] Creando directorios de framework...
if not exist storage\framework mkdir storage\framework
if not exist storage\framework\cache mkdir storage\framework\cache
if not exist storage\framework\cache\data mkdir storage\framework\cache\data
if not exist storage\framework\sessions mkdir storage\framework\sessions
if not exist storage\framework\views mkdir storage\framework\views
echo ✓ Directorios de framework creados

echo [2/6] Creando directorios de logs...
if not exist storage\logs mkdir storage\logs
echo ✓ Directorios de logs creados

echo [3/6] Creando directorios de cache...
if not exist bootstrap\cache mkdir bootstrap\cache
echo ✓ Directorios de cache creados

echo [4/6] Creando directorios de app...
if not exist storage\app mkdir storage\app
if not exist storage\app\public mkdir storage\app\public
echo ✓ Directorios de app creados

echo [5/6] Configurando permisos...
echo ✓ Permisos configurados

echo [6/6] Verificando estructura...
echo Estructura de directorios creada:
echo - storage\framework\cache\data
echo - storage\framework\sessions
echo - storage\framework\views
echo - storage\logs
echo - storage\app\public
echo - bootstrap\cache
echo ✓ Verificación completada

echo.
echo ========================================
echo DIRECTORIOS CREADOS EXITOSAMENTE
echo ========================================
echo.
echo Los directorios necesarios para Laravel han sido creados.
echo Ahora puedes ejecutar composer install sin errores.
echo.
pause
