# Imagen base oficial de PHP con Apache
FROM php:8.3-apache

# Instalar dependencias del sistema y extensiones de PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json y composer.lock antes para aprovechar la caché de Docker
COPY composer.json composer.lock ./

# Instalar dependencias de Laravel (sin dev y optimizado)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copiar todo el código del proyecto
COPY . .

# Ejecutar scripts de Laravel después de que todo el código está dentro
RUN composer dump-autoload -o \
    && php artisan package:discover --ansi || true

# Generar APP_KEY si no existe (en Render ya debes definirlo en Variables de Entorno)
RUN if [ -z "$APP_KEY" ]; then php artisan key:generate --ansi; fi

# Dar permisos a storage y bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite
COPY ./docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Exponer puerto
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
