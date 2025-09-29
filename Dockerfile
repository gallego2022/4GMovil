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
    nodejs \
    npm \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de configuración primero para aprovechar caché
COPY composer.json composer.lock package.json package-lock.json ./

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Instalar dependencias de Node.js
RUN npm install

# Copiar el resto del proyecto
COPY . .

# Crear archivo .env desde el ejemplo
RUN cp env.docker.example .env

# Compilar assets de Vite
RUN npm run build

# Crear carpetas que Laravel necesita y dar permisos
RUN mkdir -p /var/www/html/storage/framework/{cache,sessions,views} \
    && mkdir -p /var/www/html/storage/logs \
    && touch /var/www/html/storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# El enlace simbólico se creará en el script de inicialización para evitar conflictos con volúmenes

# Ejecutar Artisan
RUN composer dump-autoload -o \
    && php artisan package:discover --ansi || true

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
CMD ["/usr/local/bin/init.sh"]
