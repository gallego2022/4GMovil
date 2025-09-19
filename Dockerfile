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

# Copiar composer.json y composer.lock primero (para aprovechar la caché de dependencias)
COPY composer.json composer.lock ./

# Instalar dependencias de Laravel (sin dev y optimizado)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copiar todo el código del proyecto
COPY . .

# Crear carpetas necesarias de Laravel y dar permisos ANTES de Artisan
RUN mkdir -p /var/www/html/storage/framework/{cache,sessions,views} \
    && touch /var/www/html/storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Ahora sí ejecutamos Artisan sin que falle
RUN composer dump-autoload -o \
    && php artisan package:discover --ansi || true

# Habilitar módulos de Apache necesarios para Laravel
RUN a2enmod rewrite headers

# Copiar configuración personalizada de Apache
COPY ./docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Exponer puerto (Render usará $PORT automáticamente)
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
