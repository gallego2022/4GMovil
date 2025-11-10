# 🔐 Funcionamiento del Login y Logout - 4GMovil

## 📋 Resumen

Este documento explica cómo funciona el sistema de autenticación (login y logout) en la aplicación 4GMovil, incluyendo tanto el sistema tradicional de sesiones como el nuevo sistema JWT.

---

## 🔑 Sistema de Login

### 1. **Login Tradicional (Sesión Web)**

#### Rutas
- **GET** `/login` - Muestra el formulario de login
- **POST** `/logear` - Procesa el login

#### Flujo Completo

```
1. Usuario accede a /login
   ↓
2. Se muestra formulario de login
   ↓
3. Usuario ingresa correo y contraseña
   ↓
4. POST /logear → AuthController@logear
   ↓
5. AuthService@logear valida credenciales
   ↓
6. Verificaciones de seguridad:
   - Usuario existe
   - Usuario puede hacer login manual (no es solo Google)
   - Contraseña correcta
   - Usuario activo (estado = true)
   - Email verificado
   ↓
7. Si todo es correcto:
   - Auth::login($usuario) - Crea sesión
   - Regenera token de sesión
   - Redirige según rol:
     * Admin → /admin
     * Cliente → / (landing)
```

#### Código del Flujo

**Controlador** (`app/Http/Controllers/Auth/AuthController.php`):
```php
public function logear(Request $request)
{
    // 1. Validar datos de entrada
    $request->validate([
        'correo_electronico' => 'required|email',
        'contrasena' => 'required|string'
    ]);

    // 2. Llamar al servicio de autenticación
    $result = $this->authService->logear($request);

    // 3. Si es exitoso, redirigir según rol
    if ($result['success']) {
        if ($result['usuario']->rol === 'admin') {
            return Redirect::route('admin.index');
        } else {
            return Redirect::route('landing');
        }
    }

    // 4. Manejar errores
    // ...
}
```

**Servicio** (`app/Services/AuthService.php`):
```php
public function logear(Request $request): array
{
    // 1. Buscar usuario por email
    $usuario = Usuario::where('correo_electronico', $request->correo_electronico)->first();
    
    // 2. Verificar que existe
    if (!$usuario) {
        return ['success' => false, 'message' => 'Credenciales inválidas'];
    }
    
    // 3. Verificar que puede hacer login manual (no es solo Google)
    if (!$usuario->canLoginManually()) {
        return ['success' => false, 'message' => 'Esta cuenta solo puede iniciar sesión con Google'];
    }
    
    // 4. Verificar contraseña
    if (Hash::check($request->contrasena, $usuario->contrasena)) {
        // 5. Crear sesión
        Auth::login($usuario);
        
        // 6. Verificar que el usuario esté activo
        if (!$usuario->estado) {
            Auth::logout();
            return ['success' => false, 'message' => 'Cuenta inactiva'];
        }
        
        // 7. Verificar que el email esté verificado
        if (!$usuario->email_verified_at) {
            Auth::logout();
            // Enviar nuevo código OTP si no tiene uno válido
            $usuario->sendEmailVerificationNotification();
            return ['success' => false, 'message' => 'Debes verificar tu email'];
        }
        
        // 8. Regenerar token de sesión
        $request->session()->regenerate();
        
        // 9. Retornar éxito
        return [
            'success' => true,
            'usuario' => $usuario,
            'redirect_route' => $usuario->rol === 'admin' ? 'admin.index' : 'landing'
        ];
    }
    
    return ['success' => false, 'message' => 'Credenciales inválidas'];
}
```

#### Validaciones de Seguridad

1. **Usuario existe**: Verifica que el email esté registrado
2. **Login manual permitido**: Verifica que el usuario tenga contraseña (no es solo Google)
3. **Contraseña correcta**: Verifica la contraseña con `Hash::check()`
4. **Usuario activo**: Verifica que `estado = true`
5. **Email verificado**: Verifica que `email_verified_at` no sea null
6. **Regeneración de sesión**: Previene ataques de fijación de sesión

#### Tipos de Error

- `invalid_credentials` - Credenciales incorrectas
- `google_account` - Cuenta solo de Google (sin contraseña)
- `inactive_account` - Cuenta inactiva
- `unverified_email` - Email no verificado
- `server_error` - Error del servidor

---

### 2. **Login JWT (API)**

#### Rutas
- **POST** `/api/jwt/login` - Autentica y genera token JWT
- **GET** `/api/jwt/login` - Muestra documentación del endpoint

#### Flujo Completo

```
1. Cliente envía POST /api/jwt/login con credenciales
   ↓
2. JwtController@login recibe la petición
   ↓
3. Valida datos de entrada
   ↓
4. AuthService@logear valida credenciales (mismo flujo que login tradicional)
   ↓
5. Si es exitoso:
   - JwtService@generateToken genera token JWT
   - Retorna token + información del usuario
   ↓
6. Cliente guarda token y lo usa en peticiones posteriores
```

#### Código del Flujo

**Controlador** (`app/Http/Controllers/Auth/JwtController.php`):
```php
public function login(Request $request)
{
    // 1. Validar datos de entrada
    $validator = Validator::make($request->all(), [
        'correo_electronico' => 'required|email',
        'contrasena' => 'required|string',
    ]);

    // 2. Intentar autenticar (usa el mismo servicio que login tradicional)
    $result = $this->authService->logear($request);

    if (!$result['success']) {
        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'error_type' => $result['error_type'] ?? 'invalid_credentials'
        ], 401);
    }

    // 3. Generar token JWT
    $token = $this->jwtService->generateToken($result['usuario']);

    // 4. Retornar token + información del usuario
    return response()->json([
        'success' => true,
        'token' => $token,
        'token_type' => 'Bearer',
        'expires_in' => config('jwt.expiration', 3600),
        'usuario' => [
            'id' => $result['usuario']->usuario_id,
            'nombre' => $result['usuario']->nombre_usuario,
            'email' => $result['usuario']->correo_electronico,
            'rol' => $result['usuario']->rol,
        ]
    ]);
}
```

#### Ejemplo de Uso

**Request:**
```http
POST /api/jwt/login
Content-Type: application/json

{
  "correo_electronico": "admin@example.com",
  "contrasena": "password"
}
```

**Response (Éxito):**
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

**Response (Error):**
```json
{
  "success": false,
  "message": "Credenciales inválidas",
  "error_type": "invalid_credentials"
}
```

---

## 🚪 Sistema de Logout

### 1. **Logout Tradicional (Sesión Web)**

#### Rutas
- **POST** `/logout` - Cierra la sesión

#### Flujo Completo

```
1. Usuario hace clic en "Cerrar sesión"
   ↓
2. POST /logout → AuthController@logout
   ↓
3. AuthService@logout:
   - Auth::logout() - Cierra sesión de Laravel
   - $request->session()->invalidate() - Invalida sesión
   - $request->session()->regenerateToken() - Regenera token CSRF
   ↓
4. Redirige a / (landing) con mensaje de éxito
```

#### Código del Flujo

**Controlador** (`app/Http/Controllers/Auth/AuthController.php`):
```php
public function logout(Request $request)
{
    try {
        // 1. Llamar al servicio de logout
        $result = $this->authService->logout($request);
    
        // 2. Si es exitoso, redirigir a landing
        if ($result['success']) {
            return redirect()
                ->route('landing')
                ->with('status', $result['message'])
                ->with('status_type', 'info');
        }

        // 3. Manejar errores
        return $this->backError($result['message']);

    } catch (\Exception $e) {
        Log::error('Error en logout: ' . $e->getMessage());
        return $this->backError(trans('auth.logout_error'));
    }
}
```

**Servicio** (`app/Services/AuthService.php`):
```php
public function logout(Request $request): array
{
    try {
        // 1. Cerrar sesión de Laravel
        Auth::logout();
        
        // 2. Invalidar sesión
        $request->session()->invalidate();
        
        // 3. Regenerar token CSRF
        $request->session()->regenerateToken();
        
        return [
            'success' => true,
            'message' => trans('auth.logout_success')
        ];

    } catch (\Exception $e) {
        Log::error('Error en logout: ' . $e->getMessage());
        
        return [
            'success' => false,
            'message' => trans('auth.logout_error')
        ];
    }
}
```

#### Seguridad del Logout

1. **Auth::logout()**: Cierra la sesión del usuario en Laravel
2. **session()->invalidate()**: Invalida completamente la sesión
3. **session()->regenerateToken()**: Regenera el token CSRF para prevenir ataques

#### Implementación en Frontend

**Formulario HTML:**
```html
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Cerrar sesión</button>
</form>
```

**JavaScript (Alpine.js):**
```javascript
logout() {
    if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        // Crear y enviar formulario de logout
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/logout';
        
        // Agregar token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}
```

---

### 2. **Logout JWT (API)**

#### Nota Importante

**JWT es stateless**, lo que significa que no hay un "logout" tradicional. El token JWT es válido hasta que expire. Sin embargo, hay estrategias para implementar logout:

#### Estrategias de Logout JWT

1. **Logout del Cliente (Recomendado)**
   - El cliente simplemente elimina el token del almacenamiento
   - El token sigue siendo válido hasta que expire
   - No requiere comunicación con el servidor

2. **Blacklist de Tokens (Opcional)**
   - Mantener una lista negra de tokens revocados
   - Verificar en cada petición si el token está en la blacklist
   - Requiere almacenamiento (Redis, base de datos)

3. **Tokens de Corta Duración + Refresh**
   - Tokens de acceso cortos (15 minutos)
   - Tokens de refresh largos (7 días)
   - Al hacer logout, invalidar el refresh token

#### Implementación Actual

Actualmente, el sistema JWT no tiene un endpoint de logout porque:
- Los tokens expiran automáticamente (1 hora por defecto)
- El cliente puede simplemente eliminar el token
- Es más eficiente y escalable

**Logout del Cliente:**
```javascript
// Eliminar token del almacenamiento
localStorage.removeItem('jwt_token');
// o
sessionStorage.removeItem('jwt_token');

// Redirigir a login
window.location.href = '/login';
```

---

## 🔄 Comparación: Login Tradicional vs JWT

| Característica | Login Tradicional | Login JWT |
|---------------|-------------------|-----------|
| **Método** | Sesión web | Token JWT |
| **Estado** | Stateful (sesión en servidor) | Stateless (sin sesión) |
| **Almacenamiento** | Cookie de sesión | Token en cliente |
| **Expiración** | Configurable en sesión | Configurable en token |
| **Logout** | Invalida sesión | Eliminar token (cliente) |
| **Uso** | Web tradicional | API, SPA, móvil |
| **Seguridad** | CSRF protection | CORS + Token validation |
| **Escalabilidad** | Requiere sesión compartida | No requiere sesión |

---

## 📊 Diagrama de Flujo

### Login Tradicional

```
┌─────────────┐
│   Usuario   │
└──────┬──────┘
       │
       │ 1. Accede a /login
       ▼
┌─────────────┐
│  Formulario │
│    Login    │
└──────┬──────┘
       │
       │ 2. POST /logear
       ▼
┌─────────────┐
│AuthController│
└──────┬──────┘
       │
       │ 3. AuthService@logear
       ▼
┌─────────────┐
│ Validaciones│
│  - Usuario  │
│  - Password │
│  - Estado   │
│  - Email    │
└──────┬──────┘
       │
       │ 4. Auth::login()
       ▼
┌─────────────┐
│   Sesión    │
│   Creada    │
└──────┬──────┘
       │
       │ 5. Redirige según rol
       ▼
┌─────────────┐
│  Dashboard  │
│  o Landing  │
└─────────────┘
```

### Logout Tradicional

```
┌─────────────┐
│   Usuario   │
└──────┬──────┘
       │
       │ 1. POST /logout
       ▼
┌─────────────┐
│AuthController│
└──────┬──────┘
       │
       │ 2. AuthService@logout
       ▼
┌─────────────┐
│ Auth::logout│
│ + Invalidate│
│ + Regenerate│
└──────┬──────┘
       │
       │ 3. Redirige a /
       ▼
┌─────────────┐
│   Landing  │
└─────────────┘
```

---

## 🛡️ Seguridad

### Medidas de Seguridad Implementadas

1. **Validación de Entrada**: Todos los datos de entrada son validados
2. **Hash de Contraseñas**: Usa `bcrypt` para hashear contraseñas
3. **Verificación de Email**: Requiere email verificado para login
4. **Estado de Usuario**: Verifica que el usuario esté activo
5. **Regeneración de Sesión**: Previene fijación de sesión
6. **Token CSRF**: Protección contra CSRF en formularios
7. **Tokens JWT Firmados**: Tokens JWT firmados con clave secreta
8. **Expiración de Tokens**: Tokens JWT expiran automáticamente

### Buenas Prácticas

1. **Nunca almacenar contraseñas en texto plano**
2. **Usar HTTPS en producción**
3. **Implementar rate limiting en login**
4. **Registrar intentos de login fallidos**
5. **Implementar bloqueo de cuenta después de X intentos**
6. **Usar tokens JWT de corta duración**
7. **Implementar refresh tokens para renovación**

---

## 📝 Resumen de Rutas

### Login
- `GET /login` - Formulario de login
- `POST /logear` - Login tradicional (sesión)
- `POST /api/jwt/login` - Login JWT (API)

### Logout
- `POST /logout` - Logout tradicional (sesión)
- JWT: Eliminar token del cliente

### Otros Endpoints JWT
- `POST /api/jwt/token` - Generar token para usuario autenticado
- `POST /api/jwt/refresh` - Refrescar token JWT
- `POST /api/jwt/validate` - Validar token JWT

---

**Fecha de actualización**: {{ date('Y-m-d') }}
**Versión de Laravel**: 12
**Sistema de Autenticación**: Sesión + JWT (Híbrido)

