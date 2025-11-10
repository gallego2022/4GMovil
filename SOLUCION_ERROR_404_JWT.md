# 🔧 Solución Error 404 en JWT Login

El error **404 Not Found** significa que la ruta no se encuentra. Aquí están las soluciones:

## ✅ Verificación Rápida

### 1. Verificar que las Rutas Estén Registradas

Ejecuta este comando para verificar que las rutas JWT estén registradas:

```bash
docker-compose exec app php artisan route:list --path=api/jwt
```

**Deberías ver:**
```
POST       api/jwt/login .............. jwt.login › Auth\JwtController@login
GET|HEAD   api/jwt/login .................................... jwt.login.info  
POST       api/jwt/refresh ... jwt.refresh › Auth\JwtController@refreshToken  
POST       api/jwt/token ...... jwt.token › Auth\JwtController@generateToken  
POST       api/jwt/validate jwt.validate › Auth\JwtController@validateToken   
GET|HEAD   api/jwt/validate jwt.validate.get › Auth\JwtController@validateT…  
```

### 2. Verificar URL Correcta

**✅ URL Correcta:**
```
POST http://localhost:8000/api/jwt/login
```

**❌ URLs Incorrectas:**
```
POST http://localhost/jwt/login          (falta /api)
POST http://localhost/api/login          (falta /jwt)
POST http://127.0.0.1/api/jwt/login      (puede funcionar, pero usa localhost)
```

### 3. Verificar Puerto del Servidor

El servidor está corriendo en el puerto **8000** según la configuración de Docker:

```bash
docker-compose ps
```

Deberías ver:
```
4gmovil_app   ...   0.0.0.0:8000->80/tcp
```

**URL Correcta con Puerto:**
```
POST http://localhost:8000/api/jwt/login
```

**Si no especificas el puerto, el navegador usa el puerto 80 por defecto**, que puede no estar mapeado correctamente.

## 🔍 Soluciones Paso a Paso

### Solución 1: Usar el Puerto Correcto

En Postman, usa la URL completa con el puerto:

```
POST http://localhost:8000/api/jwt/login
```

### Solución 2: Limpiar Caché de Rutas

Si las rutas no aparecen, limpia la caché:

```bash
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

### Solución 3: Verificar que el Servidor Esté Corriendo

```bash
docker-compose ps
```

Asegúrate de que el contenedor `4gmovil_app` esté en estado `Up`.

### Solución 4: Probar con cURL

Prueba la ruta directamente desde el contenedor:

```bash
docker-compose exec app curl -X POST http://localhost/api/jwt/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"correo_electronico":"admin@example.com","contrasena":"password"}'
```

Si esto funciona, el problema es con la URL en Postman.

### Solución 5: Verificar Configuración de Postman

**Configuración Correcta en Postman:**

1. **Method:** `POST`
2. **URL:** `http://localhost:8000/api/jwt/login`
3. **Headers:**
   ```
   Content-Type: application/json
   Accept: application/json
   ```
4. **Body (raw - JSON):**
   ```json
   {
     "correo_electronico": "admin@example.com",
     "contrasena": "password"
   }
   ```

## 🐛 Problemas Comunes

### Problema 1: Error 404 en Postman pero funciona en cURL

**Causa:** URL incorrecta o puerto incorrecto

**Solución:**
- Usa `http://localhost:8000/api/jwt/login` (con puerto 8000)
- Verifica que Postman no esté agregando caracteres extra

### Problema 2: Error 404 en todas las rutas API

**Causa:** Las rutas API no están registradas

**Solución:**
1. Verifica que `RouteServiceProvider` esté registrando las rutas API
2. Ejecuta: `docker-compose exec app php artisan route:list --path=api`
3. Si no aparecen rutas, verifica `app/Providers/RouteServiceProvider.php`

### Problema 3: Error 404 solo en algunas rutas

**Causa:** La ruta específica no existe o está mal escrita

**Solución:**
- Verifica la ruta exacta: `docker-compose exec app php artisan route:list --path=api/jwt`
- Compara con la URL que estás usando en Postman

### Problema 4: Error 404 después de cambios

**Causa:** Caché de rutas desactualizada

**Solución:**
```bash
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

## ✅ Checklist de Verificación

Antes de probar, verifica:

- [ ] El servidor está corriendo: `docker-compose ps`
- [ ] Las rutas están registradas: `php artisan route:list --path=api/jwt`
- [ ] La URL incluye el puerto: `http://localhost:8000/api/jwt/login`
- [ ] El método HTTP es correcto: `POST` (no GET)
- [ ] Los headers están configurados: `Content-Type: application/json`
- [ ] El body es JSON válido
- [ ] La caché está limpia: `php artisan route:clear`

## 🧪 Prueba Rápida

Ejecuta este comando para probar la ruta directamente:

```bash
docker-compose exec app curl -X POST http://localhost/api/jwt/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"correo_electronico":"admin@example.com","contrasena":"password"}'
```

**Si esto funciona**, el problema es con la configuración en Postman.

**Si esto no funciona**, el problema es con el servidor o las rutas.

## 📝 Notas Importantes

1. **Puerto 8000:** El servidor está mapeado al puerto 8000, no al 80
2. **Prefijo /api:** Todas las rutas API tienen el prefijo `/api`
3. **Método POST:** El login requiere método `POST`, no `GET`
4. **Headers:** Siempre incluye `Content-Type: application/json`

## 🔗 URLs Completas para Postman

### Login
```
POST http://localhost:8000/api/jwt/login
```

### Validar Token (GET)
```
GET http://localhost:8000/api/jwt/validate?token=TU_TOKEN
```

### Validar Token (POST)
```
POST http://localhost:8000/api/jwt/validate
Authorization: Bearer TU_TOKEN
```

### Refrescar Token
```
POST http://localhost:8000/api/jwt/refresh
Authorization: Bearer TU_TOKEN
```

