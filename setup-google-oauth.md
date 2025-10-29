# 🔧 Configuración de Google OAuth

## Problema Identificado
El login con Google no funciona porque las credenciales de OAuth no están configuradas correctamente.

**Credenciales actuales (incorrectas):**
- `GOOGLE_CLIENT_ID=1034347` ❌
- `GOOGLE_CLIENT_SECRET=GOCSPX` ❌

## Solución

### 1. Obtener Credenciales de Google OAuth

1. **Ve a Google Cloud Console:**
   - URL: https://console.developers.google.com/

2. **Crea o selecciona un proyecto:**
   - Si no tienes proyecto, crea uno nuevo
   - Si ya tienes uno, selecciónalo

3. **Habilita las APIs necesarias:**
   - Ve a "APIs y servicios" > "Biblioteca"
   - Busca y habilita "Google+ API"
   - Busca y habilita "Google OAuth2 API"

4. **Crea las credenciales:**
   - Ve a "APIs y servicios" > "Credenciales"
   - Haz clic en "Crear credenciales" > "ID de cliente OAuth 2.0"
   - Selecciona "Aplicación web"

5. **Configura las URIs de redirección:**
   - **URI de redirección autorizada:**
     - `http://localhost:8000/auth/callback/google` (desarrollo)
     - `https://tu-dominio.com/auth/callback/google` (producción)

6. **Copia las credenciales:**
   - **ID de cliente:** (algo como `123456789-abcdefg.apps.googleusercontent.com`)
   - **Secreto de cliente:** (algo como `GOCSPX-abcdefghijklmnop`)

### 2. Actualizar Variables de Entorno

Actualiza tu archivo `.env` con las credenciales reales:

```bash
# GOOGLE OAUTH
GOOGLE_CLIENT_ID=tu-client-id-real-aqui
GOOGLE_CLIENT_SECRET=tu-client-secret-real-aqui
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/callback/google
```

### 3. Verificar Configuración

Ejecuta el comando de verificación:

```bash
docker-compose exec app php artisan google:check
```

### 4. Probar Login con Google

1. Ve a `http://localhost:8000/login`
2. Haz clic en "Iniciar sesión con Google"
3. Deberías ser redirigido a Google para autenticarte

## Comandos Útiles

```bash
# Verificar configuración de Google OAuth
docker-compose exec app php artisan google:check

# Limpiar caché de configuración
docker-compose exec app php artisan config:clear

# Ver logs de autenticación
docker-compose exec app tail -f storage/logs/laravel.log
```

## Notas Importantes

- ⚠️ **Nunca compartas tus credenciales de OAuth**
- 🔒 **Usa credenciales diferentes para desarrollo y producción**
- 📝 **Guarda las credenciales en un lugar seguro**
- 🔄 **Reinicia el contenedor después de cambiar las variables de entorno**

## Troubleshooting

### Error: "redirect_uri_mismatch"
- Verifica que la URI de redirección en Google Console coincida exactamente con `GOOGLE_REDIRECT_URI`

### Error: "invalid_client"
- Verifica que el `GOOGLE_CLIENT_ID` sea correcto

### Error: "unauthorized_client"
- Verifica que el `GOOGLE_CLIENT_SECRET` sea correcto

### Error: "access_denied"
- El usuario canceló la autenticación en Google




