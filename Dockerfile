# Imagen base oficial de PHP con Apache
FROM php:8.3-apache

# Instalar dependencias del sistema y extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    netcat-openbsd \
    wget \
    gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de configuración primero para aprovechar caché
COPY composer.json composer.lock package.json package-lock.json ./

# Instalar dependencias de PHP (incluyendo dev para desarrollo y pruebas)
RUN composer install --optimize-autoloader --no-scripts && \
    composer install --dev || true

# Instalar dependencias de Node.js (desarrollo - incluye devDependencies)
RUN npm ci --timeout=300000

# Copiar el resto del proyecto
COPY . .

# Crear archivo .env desde el ejemplo
RUN cp env.docker.example .env

# Configurar variables de entorno para el build
ENV VIEW_COMPILED_PATH=/var/www/html/storage/framework/views
ENV CACHE_DRIVER=file

# Crear carpetas que Laravel necesita y dar permisos
RUN mkdir -p /var/www/html/storage/framework/cache/data \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/app/public/{productos,fotos_perfil,perfiles} \
    && mkdir -p /var/www/html/bootstrap/cache \
    && touch /var/www/html/storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 777 /var/www/html/storage/framework \
    && chmod -R 777 /var/www/html/storage/logs \
    && chmod -R 777 /var/www/html/storage/app/public \
    && chmod -R 777 /var/www/html/bootstrap/cache

# Ejecutar Artisan
RUN composer dump-autoload -o \
    && php artisan package:discover --ansi || true

# Compilar assets de producción
RUN npm run build

# Mantener dependencias de desarrollo para desarrollo
# RUN npm prune --production  # Comentado para desarrollo

# Crear enlace simbólico de storage
RUN php artisan storage:link


# Mantener dependencias de desarrollo para desarrollo
# RUN composer install --no-dev --optimize-autoloader --no-scripts  # Comentado para desarrollo

# Asegurar permisos finales después de todas las operaciones
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 777 /var/www/html/storage/framework \
    && chmod -R 777 /var/www/html/storage/logs \
    && chmod -R 777 /var/www/html/storage/app/public \
    && chmod -R 777 /var/www/html/bootstrap/cache \
    && (test -L /var/www/html/public/storage || test -d /var/www/html/public/storage) && chown -R www-data:www-data /var/www/html/public/storage && chmod -R 755 /var/www/html/public/storage || true

# Habilitar módulos de Apache
RUN a2enmod rewrite headers

# Copiar configuración de Apache personalizada
COPY ./docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Copiar script de inicialización
COPY ./docker/init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh

# Exponer puerto
EXPOSE 80

# Comando por defecto
CMD ["/usr/local/bin/init.sh", "apache2-foreground"]
