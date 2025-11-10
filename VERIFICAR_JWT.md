# Guía para Verificar Autenticación JWT

Esta guía te ayudará a verificar que la autenticación JWT está funcionando correctamente en tu aplicación.

## 1. Verificar Configuración JWT

Primero, verifica que la configuración JWT esté correcta:

```bash
# Verificar que existe el archivo de configuración
docker-compose exec app cat config/jwt.php

# Verificar variables de entorno (si las usas)
docker-compose exec app php artisan tinker --execute="echo config('jwt.secret');"
```

## 2. Pruebas Manuales con cURL

### 2.1. Login API (Generar Token JWT)

```bash
# Reemplaza con tus credenciales reales
curl -X POST http://localhost/api/jwt/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "correo_electronico": "admin@example.com",
    "contrasena": "tu_password"
  }'
```

**Respuesta esperada:**
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
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

### 2.2. Validar Token JWT

```bash
# Reemplaza TOKEN con el token obtenido del login
curl -X GET "http://localhost/api/jwt/validate?token=TOKEN" \
  -H "Accept: application/json"
```

**O usando header Authorization:**

```bash
curl -X GET http://localhost/api/jwt/validate \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN"
```

**Respuesta esperada:**
```json
{
  "success": true,
  "valid": true,
  "payload": {
    "user_id": 1,
    "rol": "admin",
    "email": "admin@example.com",
    "expires_at": "2024-01-01 12:00:00"
  }
}
```

### 2.3. Probar Ruta Protegida (Admin)

```bash
# Reemplaza TOKEN con el token obtenido del login
curl -X GET http://localhost/admin \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -b "jwt_token=TOKEN" \
  -c cookies.txt
```

**Respuesta esperada:**
- Si el token es válido: Acceso permitido (200 OK)
- Si el token es inválido: `{"success": false, "message": "Token JWT inválido...", "error": "unauthorized"}` (401)

### 2.4. Probar Ruta Protegida SIN Token

```bash
curl -X GET http://localhost/admin \
  -H "Accept: application/json"
```

**Respuesta esperada:**
- Debe retornar 401 Unauthorized o redirigir al login

## 3. Verificar Login Tradicional (Web)

### 3.1. Login desde el Navegador

1. Abre tu navegador y ve a: `http://localhost/login`
2. Inicia sesión con tus credenciales
3. Abre las **DevTools** (F12) → **Application** → **Cookies**
4. Verifica que existe la cookie `jwt_token` con un valor JWT

### 3.2. Verificar Cookie JWT en Navegador

En la consola del navegador (F12 → Console), ejecuta:

```javascript
// Ver todas las cookies
document.cookie

// Verificar que existe jwt_token
console.log(document.cookie.includes('jwt_token'));
```

**Nota:** La cookie `jwt_token` es `httpOnly`, por lo que no será visible desde JavaScript. Esto es correcto y seguro.

### 3.3. Verificar Acceso a Rutas Protegidas

1. Después de hacer login, intenta acceder a: `http://localhost/admin`
2. Debe permitir el acceso si el JWT es válido
3. Si cierras sesión, la cookie debe eliminarse y no deberías poder acceder

## 4. Pruebas con Tinker (Laravel)

### 4.1. Generar Token Manualmente

```bash
docker-compose exec app php artisan tinker
```

```php
// Obtener un usuario
$usuario = \App\Models\Usuario::where('correo_electronico', 'admin@example.com')->first();

// Generar token
$jwtService = app(\App\Services\JwtService::class);
$token = $jwtService->generateToken($usuario);
echo "Token: " . $token . "\n";

// Validar token
$payload = $jwtService->validateToken($token);
print_r($payload);

// Obtener usuario desde token
$usuarioFromToken = $jwtService->getUserFromToken($token);
echo "Usuario: " . $usuarioFromToken->nombre_usuario . "\n";
```

### 4.2. Verificar Middleware

```php
// Simular una request con token
$request = \Illuminate\Http\Request::create('/admin', 'GET');
$request->headers->set('Authorization', 'Bearer ' . $token);

// O con cookie
$request->cookies->set('jwt_token', $token);

// Verificar que el middleware puede extraer el token
$middleware = app(\App\Http\Middleware\JwtAuthMiddleware::class);
// (Esto requiere reflexión o testing más complejo)
```

## 5. Verificar Logs

Revisa los logs de Laravel para ver si hay errores relacionados con JWT:

```bash
docker-compose exec app tail -f storage/logs/laravel.log | grep -i jwt
```

## 6. Pruebas Automatizadas

Ejecuta los tests (si existen):

```bash
docker-compose exec app php artisan test --filter Jwt
```

## 7. Checklist de Verificación

- [ ] Login API genera token JWT correctamente
- [ ] Token JWT se puede validar
- [ ] Login tradicional (web) genera cookie `jwt_token`
- [ ] Rutas protegidas con `JwtAuthMiddleware` requieren token válido
- [ ] Rutas protegidas con `JwtAdminMiddleware` requieren token de admin
- [ ] Rutas sin token retornan 401 o redirigen al login
- [ ] Logout elimina la cookie `jwt_token`
- [ ] Token expirado no permite acceso
- [ ] Token inválido no permite acceso
- [ ] Usuario inactivo no puede autenticarse

## 8. Problemas Comunes

### Token no se genera
- Verifica que `JWT_SECRET` esté configurado en `.env`
- Verifica que la librería `firebase/php-jwt` esté instalada

### Token inválido
- Verifica que el `JWT_SECRET` sea el mismo usado para generar y validar
- Verifica que el token no haya expirado

### Cookie no se guarda
- Verifica que la cookie tenga los atributos correctos (httpOnly, Secure, SameSite)
- En desarrollo, `Secure` debe ser `false` si no usas HTTPS

### Middleware no funciona
- Verifica que el middleware esté registrado correctamente
- Verifica que las rutas usen el middleware correcto
- Revisa los logs para ver errores específicos

