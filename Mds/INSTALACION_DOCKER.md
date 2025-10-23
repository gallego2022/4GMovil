# 🐳 Instalación con Docker - 4GMovil

## Requisitos previos
- Docker
- Docker Compose

## Instalación rápida

### 1. Clonar el repositorio
```bash
git clone <repository-url>
cd 4GMovil
```

### 2. Configurar variables de entorno
```bash
cp env.docker.example .env
```

### 3. Construir y ejecutar contenedores
```bash
docker-compose up --build
```

### 4. Acceder a la aplicación
- **Aplicación**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **Redis Commander**: http://localhost:8081

## Comandos útiles

### Reiniciar servicios
```bash
docker-compose restart
```

### Ver logs
```bash
docker-compose logs -f app
```

### Ejecutar comandos Artisan
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
```

### Detener servicios
```bash
docker-compose down
```

### Limpiar volúmenes
```bash
docker-compose down -v
```

## Estructura de servicios

- **app**: Aplicación Laravel (puerto 8000)
- **db**: MySQL 8.0 (puerto 3307)
- **redis**: Redis (puerto 6379)
- **phpmyadmin**: Interfaz web MySQL (puerto 8080)
- **redis-commander**: Interfaz web Redis (puerto 8081)
- **queue-worker**: Worker de colas

## Configuración de base de datos

- **Host**: db
- **Puerto**: 3306
- **Base de datos**: 4gmovil
- **Usuario**: laraveluser
- **Contraseña**: laravelpass

## Configuración de Redis

- **Host**: redis
- **Puerto**: 6379
- **Base de datos**: 0
