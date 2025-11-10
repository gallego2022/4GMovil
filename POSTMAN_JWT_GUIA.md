# 📮 Guía Completa: Probar JWT en Postman

## 🚀 Configuración Inicial en Postman

### 1. Crear una Nueva Collection

1. Abre Postman
2. Crea una nueva Collection llamada "JWT Authentication"
3. Guarda todas las requests en esta collection

## 📋 Endpoints JWT - Configuración Detallada

### ✅ Endpoint 1: Login (POST)

**Configuración:**
- **Method:** `POST` ⚠️ **IMPORTANTE: Debe ser POST, no GET**
- **URL:** `http://localhost/api/jwt/login`
- **Headers:**
  ```
  Content-Type: application/json
  Accept: application/json
  ```
- **Body:**
  - Selecciona `raw`
  - Selecciona `JSON` en el dropdown
  - Ingresa:
  ```json
  {
    "correo_electronico": "admin@example.com",
    "contrasena": "tu_password"
  }
  ```

**Respuesta Esperada (200 OK):**
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

**⚠️ Error 405:**
Si obtienes error 405, verifica:
- ✅ El método es `POST` (no GET)
- ✅ La URL es `http://localhost/api/jwt/login` (con `/api`)
- ✅ El header `Content-Type: application/json` está presente

---

### ✅ Endpoint 2: Validar Token (GET)

**Configuración:**
- **Method:** `GET`
- **URL:** `http://localhost/api/jwt/validate?token=TU_TOKEN_AQUI`
- **Headers:**
  ```
  Accept: application/json
  ```

**Respuesta Esperada (200 OK):**
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

**⚠️ Error 405:**
Si obtienes error 405, verifica:
- ✅ El método es `GET` (no POST, PUT, DELETE)
- ✅ La URL incluye el query parameter `?token=...`

---

### ✅ Endpoint 3: Validar Token (POST)

**Configuración:**
- **Method:** `POST`
- **URL:** `http://localhost/api/jwt/validate`
- **Headers:**
  ```
  Content-Type: application/json
  Accept: application/json
  Authorization: Bearer TU_TOKEN_AQUI
  ```
- **Body:**
  - Selecciona `raw`
  - Selecciona `JSON`
  - Puede estar vacío: `{}`

**Respuesta Esperada (200 OK):**
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

**⚠️ Error 405:**
Si obtienes error 405, verifica:
- ✅ El método es `POST` (no GET, PUT, DELETE)
- ✅ El header `Authorization: Bearer TU_TOKEN` está presente

---

### ✅ Endpoint 4: Refrescar Token

**Configuración:**
- **Method:** `POST` ⚠️ **IMPORTANTE: Debe ser POST**
- **URL:** `http://localhost/api/jwt/refresh`
- **Headers:**
  ```
  Content-Type: application/json
  Accept: application/json
  Authorization: Bearer TU_TOKEN_AQUI
  ```
- **Body:**
  - Selecciona `raw`
  - Selecciona `JSON`
  - Puede estar vacío: `{}`

**Respuesta Esperada (200 OK):**
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

**⚠️ Error 405:**
Si obtienes error 405, verifica:
- ✅ El método es `POST` (no GET)
- ✅ El header `Authorization: Bearer TU_TOKEN` está presente

---

### ✅ Endpoint 5: Generar Token (Requiere Sesión)

**Configuración:**
- **Method:** `POST`
- **URL:** `http://localhost/api/jwt/token`
- **Headers:**
  ```
  Content-Type: application/json
  Accept: application/json
  ```
- **Nota:** Este endpoint requiere que el usuario esté autenticado con sesión (no JWT)

**⚠️ Error 405:**
Si obtienes error 405, verifica:
- ✅ El método es `POST` (no GET)

---

## 🔧 Solución de Problemas Comunes

### Problema 1: Error 405 en `/api/jwt/login`

**Causa:** Estás usando GET en lugar de POST

**Solución:**
1. En Postman, cambia el método de `GET` a `POST`
2. Asegúrate de tener el body con JSON
3. Verifica que el header `Content-Type: application/json` esté presente

**Captura de pantalla de configuración correcta:**
```
[POST ▼] http://localhost/api/jwt/login

Headers:
  Content-Type: application/json
  Accept: application/json

Body (raw - JSON):
{
  "correo_electronico": "admin@example.com",
  "contrasena": "password"
}
```

---

### Problema 2: Error 404 en lugar de 405

**Causa:** La URL está mal escrita

**Solución:**
- ✅ Correcto: `http://localhost/api/jwt/login`
- ❌ Incorrecto: `http://localhost/jwt/login`
- ❌ Incorrecto: `http://localhost/api/login`

---

### Problema 3: Error 422 (Unprocessable Entity)

**Causa:** Los datos del body están mal formateados

**Solución:**
- Verifica que el body sea JSON válido
- Verifica que los campos sean: `correo_electronico` y `contrasena` (no `email` y `password`)
- Verifica que el header `Content-Type: application/json` esté presente

---

### Problema 4: Error 401 (Unauthorized)

**Causa:** Credenciales incorrectas o token inválido

**Solución:**
- Verifica que las credenciales sean correctas
- Verifica que el token no haya expirado
- Verifica que el token esté completo (no truncado)

---

## 🎯 Flujo Completo de Prueba en Postman

### Paso 1: Login
1. Crea una request `POST /api/jwt/login`
2. Configura headers y body como se muestra arriba
3. Envía la request
4. **Copia el token** de la respuesta

### Paso 2: Guardar Token en Variable
1. En Postman, ve a la pestaña "Tests" de la request de login
2. Agrega este código:
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("jwt_token", jsonData.token);
    console.log("Token guardado:", jsonData.token);
}
```
2. Esto guardará el token en una variable de entorno

### Paso 3: Usar Token en Otras Requests
1. En las requests que requieren token, usa:
   ```
   Authorization: Bearer {{jwt_token}}
   ```
2. Postman reemplazará `{{jwt_token}}` con el valor guardado

### Paso 4: Validar Token
1. Crea una request `GET /api/jwt/validate?token={{jwt_token}}`
2. O usa `POST /api/jwt/validate` con header `Authorization: Bearer {{jwt_token}}`

---

## 📸 Ejemplo de Configuración Visual

### Request de Login (POST)
```
┌─────────────────────────────────────────┐
│ POST  http://localhost/api/jwt/login    │
├─────────────────────────────────────────┤
│ Headers:                                │
│   Content-Type: application/json        │
│   Accept: application/json             │
├─────────────────────────────────────────┤
│ Body (raw - JSON):                     │
│ {                                       │
│   "correo_electronico": "admin@...",  │
│   "contrasena": "password"             │
│ }                                       │
└─────────────────────────────────────────┘
```

### Request de Validación (GET)
```
┌─────────────────────────────────────────┐
│ GET  http://localhost/api/jwt/validate │
│      ?token=eyJ0eXAiOiJKV1QiLCJhbGc... │
├─────────────────────────────────────────┤
│ Headers:                                │
│   Accept: application/json             │
└─────────────────────────────────────────┘
```

---

## ✅ Checklist de Verificación

Antes de probar, verifica:

- [ ] El servidor está corriendo (`docker-compose ps`)
- [ ] La URL incluye `/api` al inicio
- [ ] El método HTTP es correcto (POST para login, GET/POST para validate)
- [ ] Los headers están configurados correctamente
- [ ] El body es JSON válido (si aplica)
- [ ] Las credenciales son correctas
- [ ] El token está completo (no truncado)

---

## 🐛 Si el Error Persiste

1. **Limpia la caché de rutas:**
   ```bash
   docker-compose exec app php artisan route:clear
   docker-compose exec app php artisan config:clear
   ```

2. **Verifica que las rutas estén registradas:**
   ```bash
   docker-compose exec app php artisan route:list --path=jwt
   ```

3. **Revisa los logs:**
   ```bash
   docker-compose exec app tail -f storage/logs/laravel.log
   ```

4. **Prueba con cURL para comparar:**
   ```bash
   curl -X POST http://localhost/api/jwt/login \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"correo_electronico":"admin@example.com","contrasena":"password"}'
   ```

