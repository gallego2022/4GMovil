# Configuración de Email para 4GMovil

## Problema Identificado
El sistema de restablecimiento de contraseña está funcionando, pero los correos no se están enviando. Esto indica un problema en la configuración de email.

## Solución: Configurar Email en .env

### 1. Gmail (Recomendado para desarrollo)

```env
# Configuración de Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-correo@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-correo@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**⚠️ IMPORTANTE para Gmail:**
- Necesitas una "Contraseña de aplicación" (no tu contraseña normal)
- Activa la verificación en 2 pasos en tu cuenta de Google
- Ve a "Seguridad" → "Contraseñas de aplicación"
- Genera una contraseña para "4GMovil"

### 2. Outlook/Hotmail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=tu-correo@outlook.com
MAIL_PASSWORD=tu-contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-correo@outlook.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Yahoo

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_USERNAME=tu-correo@yahoo.com
MAIL_PASSWORD=tu-contraseña-de-aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-correo@yahoo.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Mailtrap (Para pruebas)

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu-username-de-mailtrap
MAIL_PASSWORD=tu-password-de-mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=from@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Pasos para Configurar

### Paso 1: Editar archivo .env
```bash
# En la raíz de tu proyecto
nano .env
# o
code .env
```

### Paso 2: Agregar configuración
Agrega las líneas de configuración según tu proveedor de email.

### Paso 3: Limpiar caché
```bash
php artisan config:clear
php artisan cache:clear
```

### Paso 4: Probar configuración
```bash
# Probar email básico
php artisan test:email tu-correo@ejemplo.com

# Probar restablecimiento de contraseña
php artisan test:password-reset tu-correo@ejemplo.com
```

## Verificación de Configuración

### Comando de Prueba
```bash
php artisan test:email tu-correo@ejemplo.com
```

Este comando mostrará:
- Configuración actual de email
- Intento de envío de correo de prueba
- Errores si los hay
- Soluciones sugeridas

## Troubleshooting Común

### Error: "Connection refused"
- Verifica que el puerto esté abierto
- Asegúrate de que el host sea correcto
- Verifica tu firewall

### Error: "Authentication failed"
- Verifica usuario y contraseña
- Para Gmail, usa contraseña de aplicación
- Asegúrate de que la cuenta permita SMTP

### Error: "SSL/TLS required"
- Cambia `MAIL_ENCRYPTION` a `tls` o `ssl`
- Verifica que el puerto sea correcto (587 para TLS, 465 para SSL)

### Correos van a spam
- Verifica `MAIL_FROM_ADDRESS` y `MAIL_FROM_NAME`
- Usa una dirección de email válida y verificada
- Considera usar servicios como Mailgun o SendGrid para producción

## Configuración para Producción

Para producción, considera usar servicios profesionales:

### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=tu-dominio.com
MAILGUN_SECRET=tu-api-key
```

### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=tu-api-key
MAIL_ENCRYPTION=tls
```

### Amazon SES
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=tu-access-key
AWS_SECRET_ACCESS_KEY=tu-secret-key
AWS_DEFAULT_REGION=us-east-1
```

## Verificación Final

Después de configurar:

1. **Prueba email básico:**
   ```bash
   php artisan test:email tu-correo@ejemplo.com
   ```

2. **Prueba restablecimiento de contraseña:**
   - Ve a `/password/reset`
   - Ingresa tu correo
   - Verifica que llegue el email

3. **Revisa logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Notas Importantes

- **Nunca** subas tu archivo `.env` a Git
- Las contraseñas de aplicación son más seguras que contraseñas normales
- Para desarrollo, Mailtrap es excelente para pruebas
- Para producción, usa servicios profesionales como Mailgun o SendGrid
