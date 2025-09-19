# Imagen base oficial de PHP con Apache
FROM php:8.3-apache

# Instalar dependencias del sistema y extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json y composer.lock antes para aprovechar caché
COPY composer.json composer.lock ./

# Instalar dependencias de Laravel (sin dev y optimizado)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copiar el código de la aplicación
COPY . .

# Ejecutar scripts de Laravel después de copiar código
RUN composer dump-autoload -o \
    && php artisan package:discover --ansi || true

# Dar permisos a storage y bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Habilitar módulos de Apache necesarios para Laravel
RUN a2enmod rewrite headers

# Copiar configuración personalizada de Apache
COPY ./docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Exponer puerto (Render usa $PORT automáticamente)
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
