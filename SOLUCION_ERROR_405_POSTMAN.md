# 🔧 Solución Error 405 en Postman

El error **405 Method Not Allowed** significa que estás usando un método HTTP incorrecto para la ruta que estás probando.

## 🔍 Diagnóstico Rápido

### 1. Verificar Método HTTP en Postman

Asegúrate de usar el método correcto:

| Ruta | Método Correcto | ❌ Método Incorrecto |
|------|----------------|---------------------|
| `/api/jwt/login` | **POST** | GET, PUT, DELETE |
| `/api/jwt/validate` | **GET** o **POST** | PUT, DELETE |
| `/api/jwt/refresh` | **POST** | GET, PUT, DELETE |
| `/api/jwt/token` | **POST** | GET, PUT, DELETE |

### 2. Verificar URL Completa

Asegúrate de usar la URL completa con el prefijo `/api`:

✅ **Correcto:**
```
POST http://localhost/api/jwt/login
```

❌ **Incorrecto:**
```
POST http://localhost/jwt/login
POST http://localhost/api/login
```

## 📋 Configuración Correcta en Postman

### Endpoint: Login JWT

**Request:**
- **Method:** `POST`
- **URL:** `http://localhost/api/jwt/login`
- **Headers:**
  ```
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (raw JSON):**
  ```json
  {
    "correo_electronico": "admin@example.com",
    "contrasena": "tu_password"
  }
  ```

### Endpoint: Validar Token (GET)

**Request:**
- **Method:** `GET`
- **URL:** `http://localhost/api/jwt/validate?token=TU_TOKEN_AQUI`
- **Headers:**
  ```
  Accept: application/json
  ```

### Endpoint: Validar Token (POST)

**Request:**
- **Method:** `POST`
- **URL:** `http://localhost/api/jwt/validate`
- **Headers:**
  ```
  Content-Type: application/json
  Accept: application/json
  Authorization: Bearer TU_TOKEN_AQUI
  ```
- **Body (raw JSON):**
  ```json
  {}
  ```

### Endpoint: Refrescar Token

**Request:**
- **Method:** `POST`
- **URL:** `http://localhost/api/jwt/refresh`
- **Headers:**
  ```
  Content-Type: application/json
  Accept: application/json
  Authorization: Bearer TU_TOKEN_AQUI
  ```
- **Body (raw JSON):**
  ```json
  {}
  ```

## 🐛 Problemas Comunes y Soluciones

### Problema 1: Error 405 en `/api/jwt/login`

**Causa:** Estás usando GET en lugar de POST

**Solución:**
1. En Postman, cambia el método de `GET` a `POST`
2. Asegúrate de tener el body con `correo_electronico` y `contrasena`

### Problema 2: Error 405 en `/api/jwt/validate`

**Causa:** Estás usando PUT o DELETE en lugar de GET o POST

**Solución:**
- Usa `GET` con query parameter: `?token=TU_TOKEN`
- O usa `POST` con header `Authorization: Bearer TU_TOKEN`

### Problema 3: Error 404 en lugar de 405

**Causa:** La URL está mal escrita o falta el prefijo `/api`

**Solución:**
- Verifica que la URL sea: `http://localhost/api/jwt/login`
- No uses: `http://localhost/jwt/login`

### Problema 4: Error 405 en rutas protegidas

**Causa:** Estás usando un método incorrecto para rutas admin

**Solución:**
- Verifica el método HTTP correcto para cada ruta
- Usa `GET` para rutas de visualización
- Usa `POST` para rutas de creación
- Usa `PUT` o `PATCH` para rutas de actualización
- Usa `DELETE` para rutas de eliminación

## ✅ Verificar Rutas Disponibles

Ejecuta este comando para ver todas las rutas JWT:

```bash
docker-compose exec app php artisan route:list | grep jwt
```

O para ver todas las rutas API:

```bash
docker-compose exec app php artisan route:list --path=api
```

## 🧪 Prueba Rápida con cURL

Si Postman no funciona, prueba con cURL para verificar que el servidor responde:

```bash
# Login
curl -X POST http://localhost/api/jwt/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"correo_electronico":"admin@example.com","contrasena":"tu_password"}'

# Validar token (GET)
curl -X GET "http://localhost/api/jwt/validate?token=TU_TOKEN" \
  -H "Accept: application/json"

# Validar token (POST)
curl -X POST http://localhost/api/jwt/validate \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN" \
  -d '{}'
```

## 📸 Captura de Pantalla de Postman Correcta

**Configuración correcta para Login:**

```
Method: [POST ▼]
URL: http://localhost/api/jwt/login

Headers:
  Content-Type: application/json
  Accept: application/json

Body:
  [x] raw
  [JSON ▼]
  
  {
    "correo_electronico": "admin@example.com",
    "contrasena": "password"
  }
```

## 🔍 Verificar en los Logs

Si el error persiste, revisa los logs:

```bash
docker-compose exec app tail -f storage/logs/laravel.log
```

Busca mensajes relacionados con:
- "MethodNotAllowedHttpException"
- "Route not found"
- "405"

## 💡 Tips Adicionales

1. **Limpia la caché de rutas:**
   ```bash
   docker-compose exec app php artisan route:clear
   docker-compose exec app php artisan config:clear
   ```

2. **Verifica que el servidor esté corriendo:**
   ```bash
   docker-compose ps
   ```

3. **Verifica que las rutas estén registradas:**
   ```bash
   docker-compose exec app php artisan route:list --path=jwt
   ```

