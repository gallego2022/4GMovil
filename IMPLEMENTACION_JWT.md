# 🔐 Implementación de Protección JWT - 4GMovil

## 📋 Resumen

Se ha implementado un sistema completo de autenticación JWT (JSON Web Tokens) para proteger las rutas del dashboard administrativo. El sistema permite tanto autenticación por sesión como por JWT, funcionando de forma híbrida.

---

## ✅ Componentes Implementados

### 1. **Servicio JWT** (`app/Services/JwtService.php`)
- Generación de tokens JWT
- Validación de tokens
- Refresco de tokens
- Verificación de roles de admin
- Extracción de usuarios desde tokens

### 2. **Middlewares JWT**
- **`JwtAuthMiddleware`**: Valida tokens JWT y autentica usuarios
- **`JwtAdminMiddleware`**: Verifica que el token pertenezca a un administrador

### 3. **Controlador JWT** (`app/Http/Controllers/Auth/JwtController.php`)
- `POST /api/jwt/login` - Autenticar y obtener token
- `POST /api/jwt/token` - Generar token para usuario autenticado
- `POST /api/jwt/refresh` - Refrescar token
- `POST /api/jwt/validate` - Validar token

### 4. **Configuración** (`config/jwt.php`)
- Clave secreta JWT
- Tiempo de expiración (por defecto 1 hora)
- Algoritmo de encriptación (HS256)

---

## 🔧 Configuración

### Variables de Entorno

Agregar al archivo `.env`:

```env
# JWT Configuration
JWT_SECRET=tu_clave_secreta_aqui  # Por defecto usa APP_KEY
JWT_EXPIRATION=3600  # Tiempo de expiración en segundos (1 hora)
JWT_ALGORITHM=HS256  # Algoritmo de encriptación
JWT_ISSUER=http://localhost  # Emisor del token
JWT_AUDIENCE=http://localhost  # Destinatario del token
```

### Middlewares Registrados

Los middlewares JWT están registrados en:
- `app/Http/Kernel.php` (líneas 69-70)
- `bootstrap/app.php` (líneas 18-19)

---

## 🛣️ Rutas Protegidas

### Rutas de Admin con JWT

Todas las rutas en `routes/admin.php` ahora aceptan:
- ✅ Autenticación por sesión (método tradicional)
- ✅ Autenticación por JWT (nuevo método)

**Middleware aplicado:**
```php
Route::middleware(['jwt.auth', 'jwt.admin', 'auth', 'admin', ...])
```

### Rutas API de Admin con JWT

Las rutas en `routes/api.php` bajo `admin/api` también aceptan JWT:
```php
Route::prefix('admin/api')->middleware(['jwt.auth', 'jwt.admin', 'auth', 'admin'])
```

---

## 📡 Endpoints JWT

### 1. Login y Obtener Token
```http
POST /api/jwt/login
Content-Type: application/json

{
  "correo_electronico": "admin@example.com",
  "contrasena": "password"
}
```

**Respuesta exitosa:**
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

### 2. Generar Token (Usuario Autenticado)
```http
POST /api/jwt/token
Authorization: Bearer {session_token}
```

### 3. Refrescar Token
```http
POST /api/jwt/refresh
Authorization: Bearer {jwt_token}
```

### 4. Validar Token
```http
POST /api/jwt/validate
Authorization: Bearer {jwt_token}
```

---

## 🔒 Uso de Tokens JWT

### En Peticiones HTTP

**Header Authorization:**
```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

**Query Parameter (alternativo):**
```
GET /admin?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### En JavaScript (Frontend)

```javascript
// Obtener token
const response = await fetch('/api/jwt/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    correo_electronico: 'admin@example.com',
    contrasena: 'password'
  })
});

const data = await response.json();
const token = data.token;

// Usar token en peticiones
fetch('/admin/productos', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

### En cURL

```bash
# Login
curl -X POST http://localhost:8000/api/jwt/login \
  -H "Content-Type: application/json" \
  -d '{"correo_electronico":"admin@example.com","contrasena":"password"}'

# Usar token
curl -X GET http://localhost:8000/admin \
  -H "Authorization: Bearer {token}"
```

---

## 🔄 Funcionamiento Híbrido

El sistema funciona de forma híbrida:

1. **Si hay token JWT**: Lo valida y autentica al usuario
2. **Si no hay token JWT**: Permite continuar con autenticación de sesión
3. **Para APIs**: Requiere token JWT válido
4. **Para web**: Acepta tanto JWT como sesión

---

## 🛡️ Seguridad

### Características de Seguridad

- ✅ Tokens firmados con clave secreta
- ✅ Tokens con expiración configurable
- ✅ Validación de rol de administrador
- ✅ Verificación de usuario activo
- ✅ Manejo de errores seguro
- ✅ Logs de errores de autenticación

### Payload del Token

```json
{
  "iss": "http://localhost",      // Issuer
  "aud": "http://localhost",      // Audience
  "iat": 1234567890,              // Issued at
  "exp": 1234571490,               // Expiration
  "sub": 1,                        // User ID
  "rol": "admin",                  // User role
  "email": "admin@example.com"     // User email
}
```

---

## 📝 Comandos Útiles

### Limpiar caché de configuración
```bash
docker-compose exec app php artisan config:clear
```

### Verificar rutas registradas
```bash
docker-compose exec app php artisan route:list | grep jwt
```

### Verificar middlewares
```bash
docker-compose exec app php artisan route:list --name=admin
```

---

## 🧪 Pruebas

### Probar Login JWT
```bash
curl -X POST http://localhost:8000/api/jwt/login \
  -H "Content-Type: application/json" \
  -d '{
    "correo_electronico": "admin@example.com",
    "contrasena": "password"
  }'
```

### Probar Acceso con Token
```bash
# Reemplazar {token} con el token obtenido
curl -X GET http://localhost:8000/admin \
  -H "Authorization: Bearer {token}"
```

### Probar Validación de Token
```bash
curl -X POST http://localhost:8000/api/jwt/validate \
  -H "Authorization: Bearer {token}"
```

---

## ⚠️ Notas Importantes

1. **Clave Secreta**: Asegúrate de tener una clave secreta fuerte en producción
2. **Expiración**: Los tokens expiran después del tiempo configurado (por defecto 1 hora)
3. **HTTPS**: En producción, siempre usa HTTPS para proteger los tokens
4. **Almacenamiento**: Los tokens deben almacenarse de forma segura en el cliente (no en localStorage si es posible)
5. **Refresh**: Implementa renovación automática de tokens antes de que expiren

---

## 🔧 Solución de Problemas

### Token Inválido
- Verificar que la clave secreta sea la misma en todos los servicios
- Verificar que el token no haya expirado
- Verificar el formato del header Authorization

### Usuario No Autenticado
- Verificar que el token esté en el header Authorization
- Verificar que el usuario exista y esté activo
- Verificar que el rol sea 'admin' para rutas de admin

### Error de Middleware
- Limpiar caché: `php artisan config:clear`
- Verificar que los middlewares estén registrados
- Revisar logs: `storage/logs/laravel.log`

---

## 📚 Archivos Creados/Modificados

### Archivos Nuevos
- `app/Services/JwtService.php`
- `app/Http/Middleware/JwtAuthMiddleware.php`
- `app/Http/Middleware/JwtAdminMiddleware.php`
- `app/Http/Controllers/Auth/JwtController.php`
- `config/jwt.php`

### Archivos Modificados
- `app/Http/Kernel.php` - Registro de middlewares JWT
- `bootstrap/app.php` - Registro de aliases JWT
- `routes/admin.php` - Aplicación de middlewares JWT
- `routes/api.php` - Rutas JWT y protección de rutas admin

---

## ✅ Estado de Implementación

- ✅ Instalación de paquete JWT (firebase/php-jwt)
- ✅ Servicio JWT creado
- ✅ Middlewares JWT creados
- ✅ Controlador JWT creado
- ✅ Configuración JWT creada
- ✅ Rutas JWT creadas
- ✅ Rutas de admin protegidas con JWT
- ✅ Sistema híbrido (JWT + Sesión) funcionando

---

**Fecha de implementación**: {{ date('Y-m-d') }}
**Versión de Laravel**: 12
**Paquete JWT**: firebase/php-jwt

