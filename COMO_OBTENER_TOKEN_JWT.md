# 🔑 Cómo Obtener el Token JWT

## 📋 Problema

Tienes cookies de sesión (`4gmovil_session`, `XSRF-TOKEN`) pero **no tienes la cookie `jwt_token`** que es necesaria para acceder a rutas protegidas con JWT.

## ✅ Soluciones

### Solución 1: Obtener Token JWT desde la API (Recomendado)

#### Paso 1: Login con API

**En Postman o cURL:**

```bash
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

#### Paso 2: Usar el Token

**Opción A: Header Authorization (Recomendado para API)**
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

**Opción B: Query Parameter**
```
GET http://localhost:8000/api/jwt/validate?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

**Opción C: Cookie (para navegador)**
La cookie `jwt_token` se guarda automáticamente después del login tradicional.

---

### Solución 2: Login Tradicional (Web)

#### Paso 1: Login desde el Navegador

1. Ve a: `http://localhost:8000/login`
2. Inicia sesión con tus credenciales
3. La cookie `jwt_token` se guarda automáticamente

#### Paso 2: Verificar la Cookie

**En DevTools (F12):**
- **Application** → **Cookies** → `http://localhost`
- Busca la cookie `jwt_token`

**Nota:** La cookie `jwt_token` es `httpOnly`, por lo que:
- ✅ Se envía automáticamente en las peticiones
- ❌ No es visible desde JavaScript (`document.cookie`)
- ❌ Puede no aparecer en DevTools si `Secure: true` y estás en HTTP

---

### Solución 3: Generar Token para Usuario Autenticado

Si ya estás autenticado con sesión, puedes generar un token JWT:

**En Postman:**
```
POST http://localhost:8000/api/jwt/token
Content-Type: application/json
Accept: application/json
Cookie: 4gmovil_session=TU_SESION
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

---

## 🔍 Verificar que el Token Funciona

### Validar Token

**GET:**
```
GET http://localhost:8000/api/jwt/validate?token=TU_TOKEN
```

**POST:**
```
POST http://localhost:8000/api/jwt/validate
Authorization: Bearer TU_TOKEN
```

**Respuesta Esperada:**
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

---

## 🐛 Problemas Comunes

### Problema 1: No veo la cookie `jwt_token` en DevTools

**Causa:** 
- La cookie es `httpOnly` (no visible en JavaScript)
- La cookie tiene `Secure: true` (solo HTTPS)
- Estás en desarrollo con HTTP

**Solución:**
- La cookie se envía automáticamente en las peticiones
- No necesitas verla en DevTools para que funcione
- Si necesitas verla, usa las herramientas de red (Network tab) en DevTools

### Problema 2: La cookie no se guarda

**Causa:** 
- `Secure: true` en desarrollo con HTTP
- Dominio incorrecto
- Ruta incorrecta

**Solución:**
- Ya ajustamos el código para que `Secure: false` en desarrollo
- Haz login nuevamente después de la actualización
- Verifica que estés en `http://localhost:8000`

### Problema 3: No puedo acceder a rutas protegidas

**Causa:** 
- No tienes token JWT válido
- El token expiró
- El token no se está enviando correctamente

**Solución:**
1. Obtén un token nuevo con `/api/jwt/login`
2. Verifica que el token sea válido con `/api/jwt/validate`
3. Asegúrate de enviar el token en cada petición:
   - Header: `Authorization: Bearer TU_TOKEN`
   - O Cookie: `jwt_token=TU_TOKEN`

---

## 📝 Resumen

### Para Postman/API:
1. **Login:** `POST /api/jwt/login` con credenciales
2. **Obtener token:** Copia el `token` de la respuesta
3. **Usar token:** Agrega header `Authorization: Bearer TU_TOKEN`

### Para Navegador:
1. **Login:** `POST /login` desde el formulario web
2. **Cookie automática:** La cookie `jwt_token` se guarda automáticamente
3. **Uso automático:** El navegador envía la cookie en cada petición

---

## ✅ Checklist

- [ ] Obtener token JWT desde `/api/jwt/login`
- [ ] Guardar el token (en variable de Postman o cookie)
- [ ] Usar el token en peticiones protegidas
- [ ] Verificar que el token sea válido con `/api/jwt/validate`
- [ ] Si el token expira, refrescarlo con `/api/jwt/refresh`

