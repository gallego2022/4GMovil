# 🔑 Cómo Generar Token JWT para Variables de Entorno

Esta guía te muestra diferentes formas de generar un token JWT para agregarlo a variables de entorno (Postman, .env, etc.).

## 🚀 Métodos para Generar Token JWT

### Método 1: Comando Artisan (Recomendado)

#### Generar Token por Email

```bash
docker-compose exec app php artisan jwt:generate --email=admin@example.com
```

#### Generar Token por ID

```bash
docker-compose exec app php artisan jwt:generate --id=1
```

#### Exportar para Variables de Entorno

```bash
docker-compose exec app php artisan jwt:generate --email=admin@example.com --export
```

**Salida:**
```
Token JWT generado exitosamente

Usuario: Admin
Email: admin@example.com
Rol: admin

Token:
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...

Información del Token:
  - User ID: 1
  - Rol: admin
  - Email: admin@example.com
  - Expira: 2024-01-01 12:00:00

Formato para variables de entorno:

JWT_TOKEN=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...

Para Postman:
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

---

### Método 2: API de Login (Postman/cURL)

#### Paso 1: Login con API

**En Postman:**
```
POST http://localhost:8000/api/jwt/login
Content-Type: application/json
Accept: application/json

{
  "correo_electronico": "admin@example.com",
  "contrasena": "tu_password"
}
```

**Respuesta:**
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "usuario": {
    "id": 1,
    "nombre": "Admin",
    "email": "admin@example.com",
    "rol": "admin"
  }
}
```

#### Paso 2: Copiar el Token

Copia el valor de `token` de la respuesta y úsalo en tus variables de entorno.

---

### Método 3: Tinker (Laravel Console)

#### Paso 1: Abrir Tinker

```bash
docker-compose exec app php artisan tinker
```

#### Paso 2: Generar Token

```php
// Obtener usuario
$usuario = \App\Models\Usuario::where('correo_electronico', 'admin@example.com')->first();

// Generar token
$jwtService = app(\App\Services\JwtService::class);
$token = $jwtService->generateToken($usuario);

// Mostrar token
echo "Token: " . $token . "\n";

// Validar token
$payload = $jwtService->validateToken($token);
print_r($payload);
```

#### Paso 3: Copiar el Token

Copia el token que se muestra y úsalo en tus variables de entorno.

---

### Método 4: Script PHP

Crea un archivo `generate-token.php` en la raíz del proyecto:

```php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = $argv[1] ?? 'admin@example.com';

$usuario = \App\Models\Usuario::where('correo_electronico', $email)->first();

if (!$usuario) {
    echo "Usuario no encontrado\n";
    exit(1);
}

$jwtService = app(\App\Services\JwtService::class);
$token = $jwtService->generateToken($usuario);

echo "JWT_TOKEN=" . $token . "\n";
```

**Uso:**
```bash
docker-compose exec app php generate-token.php admin@example.com
```

---

## 📋 Usar Token en Variables de Entorno

### Postman

#### Opción 1: Variable de Entorno

1. Crea una nueva variable de entorno en Postman
2. Nombre: `JWT_TOKEN`
3. Valor: `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...`
4. En tus requests, usa: `Authorization: Bearer {{JWT_TOKEN}}`

#### Opción 2: Variable de Collection

1. Ve a tu Collection → Variables
2. Agrega variable `JWT_TOKEN`
3. Usa `{{JWT_TOKEN}}` en tus requests

#### Opción 3: Script Automático

En la request de login, agrega este script en la pestaña "Tests":

```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    if (jsonData.success && jsonData.token) {
        pm.environment.set("JWT_TOKEN", jsonData.token);
        console.log("Token guardado:", jsonData.token);
    }
}
```

Esto guardará automáticamente el token en la variable de entorno después del login.

---

### Archivo .env

**No recomendado** para tokens JWT (son temporales), pero si necesitas hacerlo:

```env
JWT_TOKEN=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

**Nota:** Los tokens JWT expiran, así que no es recomendable guardarlos en `.env`.

---

### Script de Prueba (Bash)

Crea un archivo `test-jwt.sh`:

```bash
#!/bin/bash

# Obtener token
TOKEN=$(docker-compose exec app php artisan jwt:generate --email=admin@example.com --export | grep "JWT_TOKEN=" | cut -d'=' -f2)

# Exportar para uso en scripts
export JWT_TOKEN=$TOKEN

# Usar en requests
curl -X GET http://localhost:8000/api/jwt/validate \
  -H "Authorization: Bearer $JWT_TOKEN"
```

---

### Script de Prueba (PowerShell)

Crea un archivo `test-jwt.ps1`:

```powershell
# Obtener token
$token = docker-compose exec app php artisan jwt:generate --email=admin@example.com --export | Select-String "JWT_TOKEN=" | ForEach-Object { $_.Line.Split('=')[1] }

# Exportar para uso en scripts
$env:JWT_TOKEN = $token

# Usar en requests
Invoke-RestMethod -Uri "http://localhost:8000/api/jwt/validate" -Method Get -Headers @{ "Authorization" = "Bearer $env:JWT_TOKEN" }
```

---

## ✅ Verificar Token

### Validar Token

```bash
# Con query parameter
curl -X GET "http://localhost:8000/api/jwt/validate?token=TU_TOKEN"

# Con header Authorization
curl -X GET http://localhost:8000/api/jwt/validate \
  -H "Authorization: Bearer TU_TOKEN"
```

### Ver Información del Token

```bash
docker-compose exec app php artisan jwt:generate --email=admin@example.com
```

El comando mostrará información completa del token, incluyendo:
- User ID
- Rol
- Email
- Fecha de expiración

---

## 🔄 Refrescar Token

Si el token expira, puedes refrescarlo:

```bash
# Con API
curl -X POST http://localhost:8000/api/jwt/refresh \
  -H "Authorization: Bearer TOKEN_ANTERIOR" \
  -H "Content-Type: application/json"
```

---

## 📝 Resumen de Métodos

| Método | Comando | Uso |
|--------|---------|-----|
| **Artisan** | `php artisan jwt:generate --email=admin@example.com` | Desarrollo, scripts |
| **API Login** | `POST /api/jwt/login` | Postman, aplicaciones |
| **Tinker** | `php artisan tinker` | Debug, pruebas |
| **Script PHP** | `php generate-token.php` | Automatización |

---

## 🎯 Recomendación

**Para Postman:**
1. Usa el script automático en la request de login
2. El token se guarda automáticamente en `{{JWT_TOKEN}}`
3. Usa `Authorization: Bearer {{JWT_TOKEN}}` en todas tus requests

**Para Desarrollo:**
1. Usa `php artisan jwt:generate --email=admin@example.com`
2. Copia el token manualmente
3. Úsalo en tus pruebas

**Para Producción:**
1. Usa la API de login (`POST /api/jwt/login`)
2. Guarda el token en memoria (no en archivos)
3. Refresca el token antes de que expire

---

## ⚠️ Notas Importantes

1. **Los tokens JWT expiran** (por defecto 1 hora)
2. **No guardes tokens en archivos** de código o repositorios
3. **Usa variables de entorno** en Postman o scripts
4. **Refresca el token** antes de que expire
5. **No compartas tokens** en producción

