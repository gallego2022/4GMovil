# 🔐 Uso Exclusivo de JWT - 4GMovil

## 📋 Resumen

Este documento explica cómo usar **SOLO JWT** para autenticación en la aplicación 4GMovil, eliminando completamente la dependencia de sesiones web.

---

## ✅ Cambios Realizados

### 1. **Middlewares JWT Obligatorios**

Los middlewares JWT ahora **requieren obligatoriamente** un token JWT válido:

- **`JwtAuthMiddleware`**: Rechaza peticiones sin token JWT
- **`JwtAdminMiddleware`**: Rechaza peticiones sin token JWT de admin

### 2. **Rutas de Admin Actualizadas**

Todas las rutas de admin ahora **solo aceptan JWT**:

```php
// Antes (híbrido):
Route::middleware(['jwt.auth', 'jwt.admin', 'auth', 'admin', ...])

// Ahora (solo JWT):
Route::middleware(['jwt.auth', 'jwt.admin', ...])
```

### 3. **Rutas API Actualizadas**

Las rutas API de admin también **solo aceptan JWT**:

```php
// Antes (híbrido):
Route::prefix('admin/api')->middleware(['jwt.auth', 'jwt.admin', 'auth', 'admin'])

// Ahora (solo JWT):
Route::prefix('admin/api')->middleware(['jwt.auth', 'jwt.admin'])
```

---

## 🔑 Autenticación con JWT

### 1. **Login y Obtención de Token**

#### Endpoint
```
POST /api/jwt/login
```

#### Request
```http
POST /api/jwt/login
Content-Type: application/json

{
  "correo_electronico": "admin@example.com",
  "contrasena": "password"
}
```

#### Response (Éxito)
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

#### Response (Error)
```json
{
  "success": false,
  "message": "Credenciales inválidas",
  "error_type": "invalid_credentials"
}
```

---

## 🔒 Uso del Token JWT

### 1. **En Peticiones HTTP**

#### Header Authorization (Recomendado)
```http
GET /admin/productos
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

#### Query Parameter (Alternativo)
```
GET /admin/productos?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### 2. **En JavaScript (Frontend)**

#### Almacenamiento del Token
```javascript
// Después de login exitoso
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

if (data.success) {
  // Guardar token (localStorage o sessionStorage)
  localStorage.setItem('jwt_token', data.token);
  localStorage.setItem('user', JSON.stringify(data.usuario));
}
```

#### Uso del Token en Peticiones
```javascript
// Función helper para obtener token
function getJwtToken() {
  return localStorage.getItem('jwt_token');
}

// Función helper para hacer peticiones autenticadas
async function authenticatedFetch(url, options = {}) {
  const token = getJwtToken();
  
  if (!token) {
    // Redirigir a login si no hay token
    window.location.href = '/login';
    return;
  }
  
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    ...options.headers
  };
  
  return fetch(url, {
    ...options,
    headers
  });
}

// Ejemplo de uso
async function obtenerProductos() {
  const response = await authenticatedFetch('/admin/productos');
  const productos = await response.json();
  return productos;
}
```

### 3. **En cURL**

```bash
# Login
curl -X POST http://localhost:8000/api/jwt/login \
  -H "Content-Type: application/json" \
  -d '{
    "correo_electronico": "admin@example.com",
    "contrasena": "password"
  }'

# Usar token en peticiones
curl -X GET http://localhost:8000/admin/productos \
  -H "Authorization: Bearer {tu_token_jwt}"
```

---

## 🚪 Logout con JWT

### Nota Importante

**JWT es stateless**, lo que significa que no hay un "logout" tradicional en el servidor. El token JWT es válido hasta que expire.

### Estrategia de Logout

#### 1. **Logout del Cliente (Recomendado)**

El cliente simplemente elimina el token del almacenamiento:

```javascript
function logout() {
  // Eliminar token del almacenamiento
  localStorage.removeItem('jwt_token');
  localStorage.removeItem('user');
  
  // Redirigir a login
  window.location.href = '/login';
}
```

#### 2. **Blacklist de Tokens (Opcional)**

Si necesitas invalidar tokens antes de que expiren, puedes implementar una blacklist:

```php
// En JwtService
public function revokeToken(string $token): bool
{
    // Guardar token en blacklist (Redis, base de datos, etc.)
    // Ejemplo con Redis:
    Redis::setex("jwt:blacklist:{$token}", 3600, '1');
    return true;
}

// En middleware, verificar blacklist antes de validar token
public function isTokenRevoked(string $token): bool
{
    return Redis::exists("jwt:blacklist:{$token}");
}
```

---

## 🔄 Refrescar Token

### Endpoint
```
POST /api/jwt/refresh
```

### Request
```http
POST /api/jwt/refresh
Authorization: Bearer {token_actual}
```

### Response
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

### Implementación en Frontend

```javascript
// Función para refrescar token automáticamente
async function refreshTokenIfNeeded() {
  const token = getJwtToken();
  
  if (!token) {
    return null;
  }
  
  try {
    const response = await fetch('/api/jwt/refresh', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Actualizar token
      localStorage.setItem('jwt_token', data.token);
      return data.token;
    }
  } catch (error) {
    console.error('Error al refrescar token:', error);
    // Si falla, hacer logout
    logout();
  }
  
  return null;
}

// Refrescar token antes de que expire (ej: 5 minutos antes)
setInterval(() => {
  const token = getJwtToken();
  if (token) {
    // Verificar si el token expira pronto
    const payload = JSON.parse(atob(token.split('.')[1]));
    const expirationTime = payload.exp * 1000; // Convertir a milisegundos
    const currentTime = Date.now();
    const timeUntilExpiration = expirationTime - currentTime;
    
    // Si expira en menos de 5 minutos, refrescar
    if (timeUntilExpiration < 5 * 60 * 1000) {
      refreshTokenIfNeeded();
    }
  }
}, 60000); // Verificar cada minuto
```

---

## ✅ Validar Token

### Endpoint
```
POST /api/jwt/validate
GET /api/jwt/validate
```

### Request
```http
POST /api/jwt/validate
Authorization: Bearer {token}
```

### Response (Válido)
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

### Response (Inválido)
```json
{
  "success": false,
  "valid": false,
  "message": "Token inválido o expirado"
}
```

---

## 🛡️ Manejo de Errores

### Errores Comunes

#### 1. **Token No Proporcionado**
```json
{
  "success": false,
  "message": "Token JWT requerido. Por favor, inicia sesión.",
  "error": "unauthorized"
}
```
**Solución**: Incluir token en header `Authorization: Bearer {token}`

#### 2. **Token Inválido o Expirado**
```json
{
  "success": false,
  "message": "Token JWT inválido o expirado. Por favor, inicia sesión nuevamente.",
  "error": "unauthorized"
}
```
**Solución**: Hacer login nuevamente o refrescar token

#### 3. **Usuario No Encontrado**
```json
{
  "success": false,
  "message": "Usuario no encontrado",
  "error": "unauthorized"
}
```
**Solución**: El usuario fue eliminado, hacer login nuevamente

#### 4. **Usuario Inactivo**
```json
{
  "success": false,
  "message": "Usuario inactivo",
  "error": "unauthorized"
}
```
**Solución**: Contactar al administrador

#### 5. **Sin Permisos de Admin**
```json
{
  "success": false,
  "message": "No tienes permisos de administrador",
  "error": "unauthorized"
}
```
**Solución**: Usar cuenta de administrador

---

## 📝 Ejemplo Completo de Implementación

### Frontend (JavaScript)

```javascript
// Configuración JWT
const JWT_CONFIG = {
  tokenKey: 'jwt_token',
  userKey: 'user',
  loginUrl: '/api/jwt/login',
  refreshUrl: '/api/jwt/refresh',
  validateUrl: '/api/jwt/validate'
};

// Clase para manejar autenticación JWT
class JwtAuth {
  // Login
  static async login(email, password) {
    try {
      const response = await fetch(JWT_CONFIG.loginUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          correo_electronico: email,
          contrasena: password
        })
      });
      
      const data = await response.json();
      
      if (data.success) {
        // Guardar token y usuario
        localStorage.setItem(JWT_CONFIG.tokenKey, data.token);
        localStorage.setItem(JWT_CONFIG.userKey, JSON.stringify(data.usuario));
        return { success: true, data };
      }
      
      return { success: false, message: data.message };
    } catch (error) {
      return { success: false, message: 'Error de conexión' };
    }
  }
  
  // Obtener token
  static getToken() {
    return localStorage.getItem(JWT_CONFIG.tokenKey);
  }
  
  // Obtener usuario
  static getUser() {
    const user = localStorage.getItem(JWT_CONFIG.userKey);
    return user ? JSON.parse(user) : null;
  }
  
  // Verificar si está autenticado
  static isAuthenticated() {
    return !!this.getToken();
  }
  
  // Hacer petición autenticada
  static async authenticatedFetch(url, options = {}) {
    const token = this.getToken();
    
    if (!token) {
      this.logout();
      throw new Error('No hay token disponible');
    }
    
    const headers = {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers
    };
    
    let response = await fetch(url, {
      ...options,
      headers
    });
    
    // Si el token expiró, intentar refrescar
    if (response.status === 401) {
      const refreshed = await this.refreshToken();
      if (refreshed) {
        // Reintentar petición con nuevo token
        headers['Authorization'] = `Bearer ${this.getToken()}`;
        response = await fetch(url, {
          ...options,
          headers
        });
      } else {
        this.logout();
        throw new Error('Token expirado');
      }
    }
    
    return response;
  }
  
  // Refrescar token
  static async refreshToken() {
    const token = this.getToken();
    if (!token) return false;
    
    try {
      const response = await fetch(JWT_CONFIG.refreshUrl, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      
      const data = await response.json();
      
      if (data.success) {
        localStorage.setItem(JWT_CONFIG.tokenKey, data.token);
        return true;
      }
    } catch (error) {
      console.error('Error al refrescar token:', error);
    }
    
    return false;
  }
  
  // Logout
  static logout() {
    localStorage.removeItem(JWT_CONFIG.tokenKey);
    localStorage.removeItem(JWT_CONFIG.userKey);
    window.location.href = '/login';
  }
  
  // Validar token
  static async validateToken() {
    const token = this.getToken();
    if (!token) return false;
    
    try {
      const response = await fetch(JWT_CONFIG.validateUrl, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      
      const data = await response.json();
      return data.valid === true;
    } catch (error) {
      return false;
    }
  }
}

// Uso
// Login
const result = await JwtAuth.login('admin@example.com', 'password');
if (result.success) {
  console.log('Login exitoso');
}

// Hacer petición autenticada
const response = await JwtAuth.authenticatedFetch('/admin/productos');
const productos = await response.json();

// Logout
JwtAuth.logout();
```

---

## 🔧 Configuración

### Variables de Entorno

```env
# JWT Configuration
JWT_SECRET=tu_clave_secreta_aqui  # Por defecto usa APP_KEY
JWT_EXPIRATION=3600  # Tiempo de expiración en segundos (1 hora)
JWT_ALGORITHM=HS256  # Algoritmo de encriptación
```

---

## ⚠️ Consideraciones Importantes

### 1. **Seguridad del Token**
- **Nunca** almacenar tokens en localStorage si la aplicación es vulnerable a XSS
- Considerar usar **httpOnly cookies** para mayor seguridad
- Usar **HTTPS** en producción siempre

### 2. **Expiración de Tokens**
- Los tokens expiran automáticamente después del tiempo configurado
- Implementar renovación automática antes de que expire
- Considerar usar refresh tokens para mayor seguridad

### 3. **Manejo de Errores**
- Siempre verificar si el token es válido antes de hacer peticiones
- Manejar errores 401 (no autorizado) redirigiendo a login
- Implementar retry automático con refresh token

### 4. **Escalabilidad**
- JWT es stateless, perfecto para aplicaciones distribuidas
- No requiere sesión compartida entre servidores
- Ideal para APIs y aplicaciones móviles

---

## 📊 Comparación: Solo JWT vs Híbrido

| Característica | Solo JWT | Híbrido (JWT + Sesión) |
|---------------|----------|------------------------|
| **Complejidad** | Menor | Mayor |
| **Escalabilidad** | Excelente | Buena |
| **Seguridad** | Alta | Alta |
| **Uso** | API, SPA, móvil | Web tradicional + API |
| **Sesión** | No requiere | Requiere |
| **Logout** | Eliminar token | Invalidar sesión |

---

## ✅ Ventajas de Usar Solo JWT

1. **Stateless**: No requiere almacenamiento de sesión
2. **Escalable**: Funciona perfectamente con múltiples servidores
3. **API-First**: Ideal para APIs REST
4. **Móvil**: Perfecto para aplicaciones móviles
5. **SPA**: Ideal para Single Page Applications
6. **Simplicidad**: Menos complejidad en el servidor

---

**Fecha de actualización**: {{ date('Y-m-d') }}
**Versión**: Solo JWT
**Estado**: ✅ Implementado

