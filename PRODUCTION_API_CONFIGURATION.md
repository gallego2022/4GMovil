# Configuración de APIs para Producción

## URLs Actualizadas para Laravel Cloud

Tu dominio de producción es: `https://4gmovil-main-gc1onv.laravel.cloud`

## 1. Google OAuth Configuration

### URLs que necesitas actualizar en Google Cloud Console:

1. **Ve a [Google Cloud Console](https://console.cloud.google.com/)**
2. **Selecciona tu proyecto**
3. **Ve a "APIs & Services" → "Credentials"**
4. **Edita tu OAuth 2.0 Client ID**

### URLs de Redirección Autorizadas:
```
https://4gmovil-main-gc1onv.laravel.cloud/auth/callback/google
```

### URLs de JavaScript Autorizadas:
```
https://4gmovil-main-gc1onv.laravel.cloud
```

### Variables de Entorno Actualizadas:
```bash
GOOGLE_CLIENT_ID=tu_google_client_id_real
GOOGLE_CLIENT_SECRET=tu_google_client_secret_real
GOOGLE_REDIRECT_URI=https://4gmovil-main-gc1onv.laravel.cloud/auth/callback/google
```

## 2. Stripe Configuration

### URLs de Webhooks en Stripe Dashboard:

1. **Ve a [Stripe Dashboard](https://dashboard.stripe.com/)**
2. **Ve a "Developers" → "Webhooks"**
3. **Crea un nuevo webhook o edita el existente**

### URL del Webhook:
```
https://4gmovil-main-gc1onv.laravel.cloud/stripe/webhook
```

### Eventos a Escuchar:
- `payment_intent.succeeded`
- `payment_intent.payment_failed`
- `checkout.session.completed`
- `invoice.payment_succeeded`
- `invoice.payment_failed`

### Variables de Entorno Actualizadas:
```bash
STRIPE_KEY=pk_live_tu_stripe_public_key_real
STRIPE_SECRET=sk_live_tu_stripe_secret_key_real
STRIPE_WEBHOOK_SECRET=whsec_tu_webhook_secret_real
```

## 3. Configuración en Laravel Cloud

### Actualizar Variables de Entorno:

En Laravel Cloud, configura estas variables:

```bash
# Google OAuth
GOOGLE_CLIENT_ID=tu_google_client_id_real
GOOGLE_CLIENT_SECRET=tu_google_client_secret_real
GOOGLE_REDIRECT_URI=https://4gmovil-main-gc1onv.laravel.cloud/auth/callback/google

# Stripe
STRIPE_KEY=pk_live_tu_stripe_public_key_real
STRIPE_SECRET=sk_live_tu_stripe_secret_key_real
STRIPE_WEBHOOK_SECRET=whsec_tu_webhook_secret_real
```

## 4. Rutas que Necesitan Actualización

### Verificar que estas rutas existan en tu aplicación:

```php
// routes/web.php o routes/api.php
Route::get('/auth/callback/google', [AuthController::class, 'handleGoogleCallback']);
Route::post('/stripe/webhook', [StripeController::class, 'handleWebhook']);
```

## 5. Pasos para Configurar

### Paso 1: Google OAuth
1. **Ve a Google Cloud Console**
2. **Actualiza las URLs de redirección**
3. **Copia las credenciales reales**
4. **Actualiza las variables de entorno en Laravel Cloud**

### Paso 2: Stripe
1. **Ve a Stripe Dashboard**
2. **Crea/actualiza el webhook**
3. **Copia las credenciales reales**
4. **Actualiza las variables de entorno en Laravel Cloud**

### Paso 3: Verificar Funcionamiento
```bash
# En Laravel Cloud, verificar configuración
php artisan config:show

# Probar Google OAuth
# Visita: https://4gmovil-main-gc1onv.laravel.cloud/auth/google

# Probar Stripe
# Verifica que los webhooks lleguen correctamente
```

## 6. URLs Importantes

### URLs de Producción:
- **Aplicación**: `https://4gmovil-main-gc1onv.laravel.cloud`
- **Google OAuth**: `https://4gmovil-main-gc1onv.laravel.cloud/auth/callback/google`
- **Stripe Webhook**: `https://4gmovil-main-gc1onv.laravel.cloud/stripe/webhook`

### URLs de Desarrollo (para referencia):
- **Local**: `http://localhost:8000`
- **Google OAuth Local**: `http://localhost:8000/auth/callback/google`
- **Stripe Webhook Local**: `http://localhost:8000/stripe/webhook`

## 7. Verificación Final

### Comandos para verificar:
```bash
# Verificar configuración
php artisan config:show

# Verificar rutas
php artisan route:list | grep -E "(google|stripe)"

# Probar conexiones
php artisan tinker
>>> config('services.google.client_id');
>>> config('services.stripe.key');
```

## ¡Configuración Completada!

Con estas configuraciones, tu aplicación tendrá:
- ✅ **Google OAuth funcionando en producción**
- ✅ **Stripe webhooks funcionando**
- ✅ **URLs correctas para producción**
- ✅ **Integración completa de APIs**
