# Configuración de Google OAuth para 4GMovil

## Variables de Entorno Requeridas

Agrega las siguientes variables a tu archivo `.env`:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=tu_client_id_de_google
GOOGLE_CLIENT_SECRET=tu_client_secret_de_google
GOOGLE_REDIRECT_URI=http://tu-dominio.com/auth/callback/google
```

## Pasos para Configurar Google OAuth

### 1. Crear Proyecto en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la API de Google+ (si no está habilitada)

### 2. Configurar Credenciales OAuth 2.0

1. Ve a "APIs & Services" > "Credentials"
2. Haz clic en "Create Credentials" > "OAuth 2.0 Client IDs"
3. Selecciona "Web application"
4. Configura los URIs autorizados:
   - **Authorized JavaScript origins:**
     - `http://localhost:8000` (para desarrollo local)
     - `https://tu-dominio.com` (para producción)
   - **Authorized redirect URIs:**
     - `http://localhost:8000/auth/callback/google` (para desarrollo local)
     - `https://tu-dominio.com/auth/callback/google` (para producción)

### 3. Obtener Credenciales

1. Copia el **Client ID** y **Client Secret** generados
2. Agrégalos a tu archivo `.env`

### 4. Configuración para Desarrollo Local

Si estás desarrollando localmente, asegúrate de que tu archivo `.env` tenga:

```env
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/callback/google
```

### 5. Configuración para Producción

Para producción, actualiza las variables con tu dominio real:

```env
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=https://tu-dominio.com/auth/callback/google
```

## Funcionalidades Implementadas

- ✅ Login con Google OAuth
- ✅ Registro automático de nuevos usuarios
- ✅ Vinculación de cuentas existentes
- ✅ Verificación automática de email
- ✅ Almacenamiento del Google ID para identificación única
- ✅ Manejo de errores y logging

## Rutas Configuradas

- `GET /auth/redirect/google` - Redirige al usuario a Google
- `GET /auth/callback/google` - Maneja la respuesta de Google

## Notas Importantes

1. **Seguridad**: Nunca compartas tu Client Secret
2. **Dominios**: Asegúrate de que los URIs autorizados coincidan exactamente
3. **HTTPS**: En producción, usa siempre HTTPS
4. **Logs**: Los errores se registran en `storage/logs/laravel.log`

## Pruebas

Para probar la funcionalidad:

1. Ejecuta `php artisan serve`
2. Ve a `http://localhost:8000/login`
3. Haz clic en el botón "Google"
4. Completa el flujo de autenticación de Google
5. Verifica que seas redirigido correctamente
