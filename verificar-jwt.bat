@echo off
REM Script para verificar autenticación JWT (Windows)
REM Uso: verificar-jwt.bat [email] [password]

setlocal enabledelayedexpansion

set BASE_URL=http://localhost
set EMAIL=%1
set PASSWORD=%2

if "%EMAIL%"=="" set EMAIL=admin@example.com
if "%PASSWORD%"=="" set PASSWORD=password

echo ==========================================
echo Verificación de Autenticación JWT
echo ==========================================
echo URL Base: %BASE_URL%
echo Email: %EMAIL%
echo.

REM Verificar que curl esté disponible
where curl >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: curl no está instalado o no está en el PATH
    echo Por favor, instala curl o usa Git Bash
    exit /b 1
)

REM 1. Verificar que el servidor esté corriendo
echo 1. Verificando que el servidor esté corriendo...
curl -s -o nul -w "%%{http_code}" "%BASE_URL%" > temp_http.txt
set /p HTTP_CODE=<temp_http.txt
del temp_http.txt

if "%HTTP_CODE%"=="200" (
    echo [OK] Servidor está corriendo
) else if "%HTTP_CODE%"=="301" (
    echo [OK] Servidor está corriendo (redirección)
) else if "%HTTP_CODE%"=="302" (
    echo [OK] Servidor está corriendo (redirección)
) else (
    echo [ERROR] Servidor no está respondiendo (HTTP %HTTP_CODE%^)
    exit /b 1
)

REM 2. Probar login API
echo.
echo 2. Probando login API...
curl -s -X POST "%BASE_URL%/api/jwt/login" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"correo_electronico\":\"%EMAIL%\",\"contrasena\":\"%PASSWORD%\"}" > temp_login.json

findstr /C:"\"success\":true" temp_login.json >nul
if %errorlevel% equ 0 (
    echo [OK] Login API exitoso
    REM Extraer token (método simple)
    powershell -Command "(Get-Content temp_login.json | ConvertFrom-Json).token" > temp_token.txt 2>nul
    set /p TOKEN=<temp_token.txt
    del temp_token.txt
    if "!TOKEN!"=="" (
        echo [ERROR] No se pudo extraer el token de la respuesta
        type temp_login.json
        del temp_login.json
        exit /b 1
    )
    echo [INFO] Token obtenido: !TOKEN:~0,50!...
) else (
    echo [ERROR] Login API falló
    type temp_login.json
    del temp_login.json
    exit /b 1
)
del temp_login.json

REM 3. Validar token
echo.
echo 3. Validando token JWT...
curl -s -X GET "%BASE_URL%/api/jwt/validate" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %TOKEN%" > temp_validate.json

findstr /C:"\"valid\":true" temp_validate.json >nul
if %errorlevel% equ 0 (
    echo [OK] Token JWT válido
    type temp_validate.json
) else (
    echo [ERROR] Token JWT inválido
    type temp_validate.json
    del temp_validate.json
    exit /b 1
)
del temp_validate.json

REM 4. Probar ruta protegida con token
echo.
echo 4. Probando ruta protegida (admin) con token...
curl -s -w "\n%%{http_code}" -X GET "%BASE_URL%/admin" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %TOKEN%" ^
  -b "jwt_token=%TOKEN%" > temp_protected.txt

REM Obtener código HTTP (última línea)
for /f "delims=" %%i in ('type temp_protected.txt ^| findstr /R "[0-9][0-9][0-9]$"') do set HTTP_CODE=%%i

if "%HTTP_CODE%"=="200" (
    echo [OK] Ruta protegida accesible (HTTP 200^)
) else if "%HTTP_CODE%"=="302" (
    echo [OK] Ruta protegida accesible (HTTP 302 - redirección^)
) else (
    echo [ERROR] Ruta protegida no accesible (HTTP %HTTP_CODE%^)
    type temp_protected.txt
)
del temp_protected.txt

REM 5. Probar ruta protegida SIN token
echo.
echo 5. Probando ruta protegida SIN token (debe fallar)...
curl -s -w "\n%%{http_code}" -X GET "%BASE_URL%/admin" ^
  -H "Accept: application/json" > temp_no_token.txt

for /f "delims=" %%i in ('type temp_no_token.txt ^| findstr /R "[0-9][0-9][0-9]$"') do set HTTP_CODE=%%i

if "%HTTP_CODE%"=="401" (
    echo [OK] Ruta protegida correctamente rechazada sin token (HTTP 401^)
) else if "%HTTP_CODE%"=="302" (
    echo [OK] Ruta protegida correctamente rechazada sin token (HTTP 302 - redirección^)
) else (
    echo [ERROR] Ruta protegida debería rechazar sin token (HTTP %HTTP_CODE%^)
)
del temp_no_token.txt

echo.
echo ==========================================
echo Verificación completada
echo ==========================================

endlocal

