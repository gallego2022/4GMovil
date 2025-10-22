# 🚀 Guía de Instalación - 4GMovil

Esta guía te ayudará a instalar el proyecto 4GMovil en diferentes entornos de desarrollo y producción.

## 📋 Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Instalación Local](#instalación-local)
- [Instalación con Docker](#instalación-con-docker)
- [Instalación en Laravel Cloud](#instalación-en-laravel-cloud)
- [Comandos Útiles](#comandos-útiles)
- [Solución de Problemas](#solución-de-problemas)

## 🔧 Requisitos Previos

### Para Instalación Local
- **PHP 8.2+** con extensiones: `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`
- **Composer** (gestor de dependencias PHP)
- **Node.js 18+** (opcional, para compilar assets)
- **MySQL 8.0+** o **MariaDB 10.3+**
- **Git**

### Para Docker
- **Docker Desktop** (Windows/Mac) o **Docker Engine** (Linux)
- **Docker Compose**

### Para Laravel Cloud
- **Laravel Cloud CLI**
- Cuenta en **Laravel Cloud**

## 💻 Instalación Local

### Windows
```bash
# Ejecutar script de instalación
install-local.bat
```

### Linux/macOS
```bash
# Hacer ejecutable y ejecutar
chmod +x install-local.sh
./install-local.sh
```

### Configuración Manual
```bash
# 1. Clonar repositorio
git clone https://github.com/tu-usuario/4GMovil.git
cd 4GMovil

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=root
DB_PASSWORD=tu_password

# 5. Configurar caché
php artisan cache:table
php artisan cache:setup-cloud --driver=file

# 6. Ejecutar migraciones
php artisan migrate
php artisan db:seed

# 7. Iniciar servidor
php artisan serve
```

## 🐳 Instalación con Docker

### Windows
```bash
# Ejecutar script de instalación
install-docker.bat
```

### Linux/macOS
```bash
# Hacer ejecutable y ejecutar
chmod +x install-docker.sh
./install-docker.sh
```

### Configuración Manual
```bash
# 1. Configurar variables de entorno
cp .env.example .env

# 2. Configurar para Docker
echo "DB_HOST=mysql" >> .env
echo "CACHE_DRIVER=redis" >> .env
echo "REDIS_HOST=redis" >> .env

# 3. Iniciar contenedores
docker-compose up -d

# 4. Instalar dependencias
docker-compose exec app composer install
docker-compose exec app npm install

# 5. Configurar aplicación
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan cache:table
docker-compose exec app php artisan cache:setup-cloud --driver=redis

# 6. Ejecutar migraciones
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# 7. Compilar assets
docker-compose exec app npm run build
```

### Servicios Disponibles
- **Aplicación**: http://localhost:8000
- **Redis Commander**: http://localhost:8081 (admin/admin)
- **MySQL**: localhost:3306 (root/password)
- **Redis**: localhost:6379

## ☁️ Instalación en Laravel Cloud

### Windows
```bash
# Ejecutar script de instalación
install-laravel-cloud.bat
```

### Linux/macOS
```bash
# Hacer ejecutable y ejecutar
chmod +x install-laravel-cloud.sh
./install-laravel-cloud.sh
```

### Configuración Manual
```bash
# 1. Instalar Laravel Cloud CLI
composer global require laravel/cloud

# 2. Autenticarse
laravel-cloud auth:login

# 3. Crear proyecto
laravel-cloud project:create 4gmovil

# 4. Configurar base de datos
laravel-cloud db:create 4gmovil

# 5. Configurar variables de entorno en el panel
# - APP_KEY
# - DB_PASSWORD
# - STRIPE_KEY
# - STRIPE_SECRET
# - GOOGLE_CLIENT_ID
# - GOOGLE_CLIENT_SECRET

# 6. Deploy
laravel-cloud deploy

# 7. Configurar aplicación
laravel-cloud artisan cache:table
laravel-cloud artisan cache:setup-cloud --driver=database
laravel-cloud artisan migrate --force
laravel-cloud artisan db:seed --force
```

## 🛠️ Comandos Útiles

### Caché
```bash
# Configurar caché según entorno
php artisan cache:configure-environment

# Limpiar caché
php artisan cache:clear

# Probar rendimiento de caché
php artisan test:cache-performance-fallback

# Configurar caché para Laravel Cloud
php artisan cache:setup-cloud --driver=database
```

### Base de Datos
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Ver estado de migraciones
php artisan migrate:status

# Resetear base de datos
php artisan migrate:fresh --seed
```

### Docker
```bash
# Ver logs
docker-compose logs -f

# Detener contenedores
docker-compose down

# Reiniciar contenedores
docker-compose restart

# Ejecutar comandos
docker-compose exec app php artisan [comando]
```

### Laravel Cloud
```bash
# Ver estado
laravel-cloud app:status

# Ver logs
laravel-cloud logs

# Ejecutar comandos
laravel-cloud artisan [comando]

# Monitorear
./monitor-laravel-cloud.sh
```

## 🚨 Solución de Problemas

### Error de Cache Path (Directorio de Cache)
Si encuentras el error `Please provide a valid cache path`, ejecuta:

**Windows:**
```bash
# Ejecutar script de creación de directorios
setup-storage-directories.bat
```

**Linux/macOS:**
```bash
# Ejecutar script de creación de directorios
chmod +x setup-storage-directories.sh
./setup-storage-directories.sh
```

**Manual:**
```bash
# Crear directorios necesarios
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Configurar permisos (Linux/macOS)
chmod -R 775 storage bootstrap/cache
```

### Error de Permisos
```bash
# Linux/macOS
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Windows (ejecutar como administrador)
icacls storage /grant "IIS_IUSRS:(OI)(CI)F"
icacls bootstrap/cache /grant "IIS_IUSRS:(OI)(CI)F"
```

### Error de Redis
```bash
# Usar file cache como fallback
php artisan cache:setup-cloud --driver=file
```

### Error de Base de Datos
```bash
# Verificar conexión
php artisan tinker
DB::connection()->getPdo();
```

### Error de Docker
```bash
# Limpiar contenedores
docker-compose down -v
docker system prune -a

# Reconstruir
docker-compose build --no-cache
docker-compose up -d
```

### Error de Laravel Cloud
```bash
# Verificar autenticación
laravel-cloud auth:check

# Verificar proyecto
laravel-cloud project:list

# Verificar variables de entorno
laravel-cloud env:list
```

## 📚 Documentación Adicional

- [Guía de Caché Redis](LARAVEL_CLOUD_CACHE_GUIDE.md)
- [Configuración Docker](docker-compose.cache.yml)
- [Funcionalidades del Proyecto](FUNCIONALIDADES_COMPLETAS_4GMOVIL.md)

## 🤝 Soporte

Si encuentras problemas durante la instalación:

1. Verifica que todos los requisitos estén instalados
2. Revisa los logs de error
3. Consulta la sección de solución de problemas
4. Crea un issue en el repositorio

## 📝 Notas

- Los scripts de instalación incluyen verificaciones automáticas
- Se recomienda usar Docker para desarrollo local
- Laravel Cloud es ideal para producción
- El caché Redis mejora significativamente el rendimiento
- **IMPORTANTE**: Los scripts ahora crean automáticamente todos los directorios necesarios para evitar errores de cache path
- Si encuentras problemas, ejecuta `setup-storage-directories.bat` (Windows) o `./setup-storage-directories.sh` (Linux/macOS)

## 🔍 Verificación de Redis

### Scripts de Verificación
```bash
# Windows
verificar-redis-docker.bat
verificar-queue-docker.bat

# Linux/macOS
./verificar-redis-docker.sh
./verificar-queue-docker.sh
```

### Comandos Manuales de Redis
```bash
# Verificar conexión
docker-compose exec redis redis-cli ping

# Ver todas las claves
docker-compose exec redis redis-cli keys "*"

# Ver estadísticas
docker-compose exec redis redis-cli info

# Limpiar Redis
docker-compose exec redis redis-cli flushall

# Ver configuración
docker-compose exec redis redis-cli config get "*"
```

### Probar Caché desde la Aplicación
```bash
# Abrir Tinker
docker-compose exec app php artisan tinker

# Probar caché
Cache::put('test', 'Redis funciona', 60)
Cache::get('test')
```

## 🔄 Verificación de Queue Workers

### Scripts de Verificación
```bash
# Windows
verificar-queue-docker.bat

# Linux/macOS
./verificar-queue-docker.sh
```

### Comandos Manuales de Queue
```bash
# Ver logs del worker
docker-compose logs -f queue-worker

# Reiniciar worker
docker-compose restart queue-worker

# Detener worker
docker-compose stop queue-worker

# Iniciar worker
docker-compose start queue-worker

# Ver todos los contenedores
docker-compose ps
```

### Probar Queue desde la Aplicación
```bash
# Abrir Tinker
docker-compose exec app php artisan tinker

# Probar queue
dispatch(new \App\Jobs\ProcesarAlertaStockBajo());
```
