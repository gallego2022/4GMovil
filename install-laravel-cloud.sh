#!/bin/bash

# ========================================
# INSTALACIÓN LARAVEL CLOUD - 4GMovil
# Sistema: Laravel Cloud
# Entorno: Producción
# ========================================

set -e  # Salir si hay errores

echo ""
echo "========================================"
echo "INSTALACIÓN LARAVEL CLOUD - 4GMovil"
echo "========================================"
echo ""

# Función para verificar comandos
check_command() {
    if ! command -v $1 &> /dev/null; then
        echo "ERROR: $1 no está instalado"
        echo "Por favor instala $1 antes de continuar"
        exit 1
    fi
}

# [1/15] Verificar Laravel Cloud CLI
echo "[1/15] Verificando Laravel Cloud CLI..."
check_command laravel-cloud
CLOUD_VERSION=$(laravel-cloud --version)
echo "✓ Laravel Cloud CLI encontrado ($CLOUD_VERSION)"

# [2/15] Verificar autenticación
echo "[2/15] Verificando autenticación..."
if ! laravel-cloud auth:check &> /dev/null; then
    echo "ERROR: No estás autenticado en Laravel Cloud"
    echo "Ejecuta: laravel-cloud auth:login"
    exit 1
fi
echo "✓ Autenticado en Laravel Cloud"

# [3/15] Crear archivo .env para producción
echo "[3/15] Configurando archivo de entorno para producción..."
if [ ! -f .env.production ]; then
    cp .env.example .env.production
    echo "✓ Archivo .env.production creado"
else
    echo "✓ Archivo .env.production ya existe"
fi

# Configurar variables específicas para Laravel Cloud
echo "Configurando variables para Laravel Cloud..."
cat > .env.production << 'EOF'
APP_NAME="4GMovil"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tu-proyecto.laravel-cloud.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Base de datos (configurar en Laravel Cloud)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=root
DB_PASSWORD=

# Caché optimizado para Laravel Cloud
CACHE_DRIVER=database
CACHE_PREFIX=4gmovil_cache_

# Sesiones
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cola de trabajos
QUEUE_CONNECTION=database

# Mail (configurar en Laravel Cloud)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@4gmovil.com
MAIL_FROM_NAME="4GMovil"

# Stripe (configurar en Laravel Cloud)
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Google OAuth (configurar en Laravel Cloud)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

# Configuración de caché específica
CACHE_TTL=3600
CACHE_CLEAR_ON_DEPLOY=true
EOF
echo "✓ Variables de entorno configuradas para Laravel Cloud"

# [4/15] Crear proyecto en Laravel Cloud
echo "[4/15] Creando proyecto en Laravel Cloud..."
read -p "¿Nombre del proyecto en Laravel Cloud? (4gmovil): " PROJECT_NAME
PROJECT_NAME=${PROJECT_NAME:-4gmovil}

if ! laravel-cloud project:list | grep -q "$PROJECT_NAME"; then
    laravel-cloud project:create "$PROJECT_NAME"
    echo "✓ Proyecto '$PROJECT_NAME' creado en Laravel Cloud"
else
    echo "✓ Proyecto '$PROJECT_NAME' ya existe en Laravel Cloud"
fi

# [5/15] Configurar base de datos
echo "[5/15] Configurando base de datos..."
laravel-cloud db:create "$PROJECT_NAME"
echo "✓ Base de datos configurada"

# [6/15] Configurar variables de entorno en Laravel Cloud
echo "[6/15] Configurando variables de entorno en Laravel Cloud..."
echo "IMPORTANTE: Configura manualmente las siguientes variables en el panel de Laravel Cloud:"
echo ""
echo "Variables obligatorias:"
echo "- APP_KEY: (se generará automáticamente)"
echo "- DB_PASSWORD: (contraseña de la base de datos)"
echo "- STRIPE_KEY: (clave pública de Stripe)"
echo "- STRIPE_SECRET: (clave secreta de Stripe)"
echo "- GOOGLE_CLIENT_ID: (ID del cliente de Google OAuth)"
echo "- GOOGLE_CLIENT_SECRET: (secreto del cliente de Google OAuth)"
echo ""
echo "Variables opcionales:"
echo "- MAIL_HOST: (servidor SMTP)"
echo "- MAIL_USERNAME: (usuario SMTP)"
echo "- MAIL_PASSWORD: (contraseña SMTP)"
echo ""

# [7/15] Preparar archivos para deploy
echo "[7/15] Preparando archivos para deploy..."

# Crear .gitignore específico para producción
cat > .gitignore.production << 'EOF'
# Archivos de desarrollo
.env
.env.local
.env.docker
.env.testing

# Archivos de caché
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
bootstrap/cache/*

# Logs
storage/logs/*

# Archivos de usuario
storage/app/public/*
public/storage

# Archivos de Node.js
node_modules/
npm-debug.log
yarn-error.log

# Archivos de IDE
.vscode/
.idea/
*.swp
*.swo

# Archivos del sistema
.DS_Store
Thumbs.db
EOF

# [8/15] Optimizar para producción
echo "[8/15] Optimizando para producción..."

# Instalar dependencias de producción
composer install --no-dev --optimize-autoloader --no-interaction
echo "✓ Dependencias de producción instaladas"

# Compilar assets
if command -v npm &> /dev/null; then
    npm install --production --silent
    npm run build
    echo "✓ Assets compilados para producción"
else
    echo "ADVERTENCIA: Node.js no está disponible, saltando compilación de assets"
fi

# [9/15] Crear archivo de configuración específico para Laravel Cloud
echo "[9/15] Creando configuración específica para Laravel Cloud..."
cat > config/cache-cloud.php << 'EOF'
<?php

use Illuminate\Support\Str;

return [
    'default' => env('CACHE_DRIVER', 'database'),
    'stores' => [
        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],
    ],
    'prefix' => env('CACHE_PREFIX', '4gmovil_cache_'),
];
EOF
echo "✓ Configuración de caché específica creada"

# [10/15] Crear script de post-deploy
echo "[10/15] Creando script de post-deploy..."
cat > laravel-cloud-deploy.sh << 'EOF'
#!/bin/bash

# Script de post-deploy para Laravel Cloud
echo "Ejecutando post-deploy..."

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar caché
php artisan cache:table
php artisan cache:setup-cloud --driver=database

# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders
php artisan db:seed --force

# Configurar storage
php artisan storage:link

echo "Post-deploy completado"
EOF
chmod +x laravel-cloud-deploy.sh
echo "✓ Script de post-deploy creado"

# [11/15] Crear archivo de configuración de Laravel Cloud
echo "[11/15] Creando configuración de Laravel Cloud..."
cat > laravel-cloud.yml << 'EOF'
name: 4gmovil
services:
  - name: app
    build:
      context: .
      dockerfile: Dockerfile.cloud
    environment:
      - APP_ENV=production
      - CACHE_DRIVER=database
    deploy:
      replicas: 1
      resources:
        limits:
          memory: 512Mi
        requests:
          memory: 256Mi
    healthcheck:
      test: ["CMD", "php", "artisan", "health:check"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s
EOF
echo "✓ Configuración de Laravel Cloud creada"

# [12/15] Crear Dockerfile específico para Laravel Cloud
echo "[12/15] Creando Dockerfile para Laravel Cloud..."
cat > Dockerfile.cloud << 'EOF'
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor

# Limpiar caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www

# Copiar archivos de la aplicación
COPY . /var/www

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install --production --silent
RUN npm run build

# Configurar permisos
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www

# Configurar supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Exponer puerto
EXPOSE 8000

# Comando por defecto
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
EOF
echo "✓ Dockerfile para Laravel Cloud creado"

# [13/15] Crear configuración de supervisor
echo "[13/15] Creando configuración de supervisor..."
mkdir -p docker/supervisor
cat > docker/supervisor/supervisord.conf << 'EOF'
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/php-fpm.err.log
stdout_logfile=/var/log/supervisor/php-fpm.out.log

[program:laravel-worker]
command=php artisan queue:work --sleep=3 --tries=3 --max-time=3600
directory=/var/www
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/laravel-worker.err.log
stdout_logfile=/var/log/supervisor/laravel-worker.out.log
user=www-data
numprocs=2
EOF
echo "✓ Configuración de supervisor creada"

# [14/15] Crear script de monitoreo
echo "[14/15] Creando script de monitoreo..."
cat > monitor-laravel-cloud.sh << 'EOF'
#!/bin/bash

# Script de monitoreo para Laravel Cloud
echo "Monitoreando aplicación en Laravel Cloud..."

# Verificar estado de la aplicación
echo "Estado de la aplicación:"
laravel-cloud app:status

# Verificar logs
echo "Últimos logs:"
laravel-cloud logs --tail=50

# Verificar caché
echo "Estado del caché:"
laravel-cloud artisan cache:configure-environment

# Verificar base de datos
echo "Estado de la base de datos:"
laravel-cloud artisan migrate:status

echo "Monitoreo completado"
EOF
chmod +x monitor-laravel-cloud.sh
echo "✓ Script de monitoreo creado"

# [15/15] Deploy a Laravel Cloud
echo "[15/15] Desplegando a Laravel Cloud..."
echo "IMPORTANTE: Asegúrate de haber configurado todas las variables de entorno en el panel de Laravel Cloud"
echo ""
read -p "¿Continuar con el deploy? (y/N): " CONTINUE
if [[ $CONTINUE =~ ^[Yy]$ ]]; then
    laravel-cloud deploy
    echo "✓ Deploy completado"
    
    # Ejecutar post-deploy
    echo "Ejecutando post-deploy..."
    laravel-cloud artisan cache:clear
    laravel-cloud artisan cache:table
    laravel-cloud artisan cache:setup-cloud --driver=database
    laravel-cloud artisan migrate --force
    laravel-cloud artisan db:seed --force
    echo "✓ Post-deploy completado"
else
    echo "Deploy cancelado. Ejecuta 'laravel-cloud deploy' cuando estés listo."
fi

echo ""
echo "========================================"
echo "INSTALACIÓN LARAVEL CLOUD COMPLETADA"
echo "========================================"
echo ""
echo "Configuración completada:"
echo "- Proyecto: $PROJECT_NAME"
echo "- Base de datos: Configurada"
echo "- Variables de entorno: Configurar en el panel"
echo "- Caché: Database cache optimizado"
echo ""
echo "Próximos pasos:"
echo "1. Configura las variables de entorno en el panel de Laravel Cloud"
echo "2. Ejecuta: laravel-cloud deploy"
echo "3. Verifica: laravel-cloud app:status"
echo ""
echo "Comandos útiles:"
echo "- Ver logs: laravel-cloud logs"
echo "- Ejecutar comandos: laravel-cloud artisan [comando]"
echo "- Monitorear: ./monitor-laravel-cloud.sh"
echo ""
