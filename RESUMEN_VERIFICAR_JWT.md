# 🔐 Resumen: Cómo Verificar Autenticación JWT

## 🚀 Métodos Rápidos de Verificación

### 1. **Prueba Rápida con cURL (Recomendado)**

```bash
# 1. Login y obtener token
curl -X POST http://localhost/api/jwt/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"correo_electronico":"admin@example.com","contrasena":"tu_password"}'

# 2. Guarda el token de la respuesta y valídalo
curl -X GET "http://localhost/api/jwt/validate?token=TU_TOKEN_AQUI" \
  -H "Accept: application/json"

# 3. Prueba una ruta protegida
curl -X GET http://localhost/admin \
  -H "Authorization: Bearer TU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

### 2. **Usar Scripts Automatizados**

**Windows:**
```bash
verificar-jwt.bat admin@example.com tu_password
```

**Linux/Mac:**
```bash
chmod +x verificar-jwt.sh
./verificar-jwt.sh admin@example.com tu_password
```

### 3. **Ejecutar Tests Automatizados**

```bash
docker-compose exec app php artisan test tests/Unit/JwtAuthenticationTest.php
```

### 4. **Verificar desde el Navegador**

1. **Login tradicional:**
   - Ve a `http://localhost/login`
   - Inicia sesión con tus credenciales
   - Abre DevTools (F12) → Application → Cookies
   - Verifica que existe la cookie `jwt_token`

2. **Acceder a ruta protegida:**
   - Después de login, ve a `http://localhost/admin`
   - Debe permitir acceso si el JWT es válido

3. **Verificar logout:**
   - Cierra sesión
   - La cookie `jwt_token` debe eliminarse
   - Intentar acceder a `/admin` debe redirigir al login

## ✅ Checklist de Verificación

- [ ] **Login API genera token JWT** → `POST /api/jwt/login` retorna token
- [ ] **Token se puede validar** → `GET /api/jwt/validate?token=...` retorna `valid: true`
- [ ] **Login web genera cookie** → Cookie `jwt_token` existe después de login
- [ ] **Rutas protegidas requieren token** → Sin token retorna 401 o redirige
- [ ] **Rutas protegidas aceptan token válido** → Con token válido permite acceso
- [ ] **Admin middleware funciona** → Solo admins pueden acceder a rutas admin
- [ ] **Logout elimina cookie** → Cookie `jwt_token` se elimina al cerrar sesión
- [ ] **Token expirado rechazado** → Token expirado no permite acceso
- [ ] **Token inválido rechazado** → Token inválido no permite acceso

## 🔍 Verificación con Tinker

```bash
docker-compose exec app php artisan tinker
```

```php
// Obtener usuario
$usuario = \App\Models\Usuario::where('correo_electronico', 'admin@example.com')->first();

// Generar token
$jwtService = app(\App\Services\JwtService::class);
$token = $jwtService->generateToken($usuario);
echo "Token: $token\n";

// Validar token
$payload = $jwtService->validateToken($token);
print_r($payload);

// Obtener usuario desde token
$usuarioFromToken = $jwtService->getUserFromToken($token);
echo "Usuario: " . $usuarioFromToken->nombre_usuario . "\n";

// Verificar si es admin
$isAdmin = $jwtService->isAdminToken($token);
echo "Es admin: " . ($isAdmin ? 'Sí' : 'No') . "\n";
```

## 📋 Verificar Configuración

```bash
# Verificar configuración JWT
docker-compose exec app php artisan tinker --execute="print_r(config('jwt'));"

# Verificar que JWT_SECRET esté configurado
docker-compose exec app php artisan tinker --execute="echo config('jwt.secret') ? 'OK' : 'ERROR: JWT_SECRET no configurado';"
```

## 🐛 Problemas Comunes

### Token no se genera
- Verifica que `JWT_SECRET` esté en `.env` o usa `APP_KEY`
- Verifica que `firebase/php-jwt` esté instalado: `composer show firebase/php-jwt`

### Token inválido
- Verifica que el `JWT_SECRET` sea el mismo para generar y validar
- Verifica que el token no haya expirado (por defecto 1 hora)

### Cookie no se guarda
- En desarrollo, verifica que `Secure` sea `false` si no usas HTTPS
- Verifica que la cookie tenga `httpOnly: true` y `SameSite: Lax`

### Middleware no funciona
- Verifica que las rutas usen `JwtAuthMiddleware` o `JwtAdminMiddleware`
- Revisa logs: `docker-compose exec app tail -f storage/logs/laravel.log | grep -i jwt`

## 📚 Documentación Completa

Para más detalles, consulta:
- `VERIFICAR_JWT.md` - Guía completa de verificación
- `verificar-jwt.sh` - Script de verificación (Linux/Mac)
- `verificar-jwt.bat` - Script de verificación (Windows)

