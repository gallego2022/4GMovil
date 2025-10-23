# ☁️ Instalación con Laravel Cloud - 4GMovil

## Requisitos previos
- Cuenta de Laravel Cloud
- Repositorio en GitHub/GitLab

## Configuración

### 1. Variables de entorno
Configurar las siguientes variables en Laravel Cloud:

#### Base de datos
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=root
DB_PASSWORD=<tu-password>
```

#### Redis
```
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DB=0
```

#### Aplicación
```
APP_NAME=4GMovil
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.laravel-cloud.com
```

#### Stripe (Producción)
```
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

#### Google OAuth (Producción)
```
GOOGLE_CLIENT_ID=tu-client-id
GOOGLE_CLIENT_SECRET=tu-client-secret
GOOGLE_REDIRECT_URI=https://tu-dominio.laravel-cloud.com/auth/callback/google
```

#### Correo electrónico
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME=4GMovil
```

### 2. Configuración de servicios
- **PHP**: 8.3
- **Node.js**: 18
- **MySQL**: Incluido
- **Redis**: Incluido

### 3. Despliegue automático
El despliegue se realiza automáticamente cuando se hace push a la rama principal.

## Comandos de despliegue manual

### Desde Laravel Cloud CLI
```bash
laravel-cloud deploy
```

### Verificar estado
```bash
laravel-cloud status
```

### Ver logs
```bash
laravel-cloud logs
```

## Optimizaciones incluidas

- ✅ Caché de configuración
- ✅ Caché de rutas
- ✅ Caché de vistas
- ✅ Optimización de autoloader
- ✅ Compilación de assets
- ✅ Enlace simbólico de storage
- ✅ Migraciones automáticas

## Monitoreo

- **Métricas**: Disponibles en el dashboard de Laravel Cloud
- **Logs**: Accesibles desde la interfaz web
- **Base de datos**: phpMyAdmin incluido
- **Redis**: Redis Commander incluido
