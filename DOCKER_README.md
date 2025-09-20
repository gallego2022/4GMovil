# 🐳 Despliegue con Docker - 4GMovil

Este documento explica cómo desplegar el proyecto 4GMovil usando Docker.

## 📋 Requisitos Previos

- Docker Desktop instalado y funcionando
- Docker Compose instalado
- Al menos 4GB de RAM disponible
- Puertos 8000, 8080, 3306 y 6379 libres

## 🚀 Despliegue Rápido

### Opción 1: Script Automático (Recomendado)

**En Windows:**
```bash
deploy.bat
```

**En Linux/Mac:**
```bash
chmod +x deploy.sh
./deploy.sh
```

### Opción 2: Comandos Manuales

1. **Configurar variables de entorno:**
```bash
cp env.docker.example .env
# Editar .env con tus configuraciones
```

2. **Construir y levantar servicios:**
```bash
docker-compose up --build -d
```

3. **Verificar estado:**
```bash
docker-compose ps
```

## 🌐 Servicios Disponibles

| Servicio | URL | Descripción |
|----------|-----|-------------|
| **Aplicación** | http://localhost:8000 | Aplicación principal de 4GMovil |
| **phpMyAdmin** | http://localhost:8080 | Administrador de base de datos |
| **MySQL** | localhost:3306 | Base de datos MySQL |
| **Redis** | localhost:6379 | Cache y sesiones |

### Credenciales de Base de Datos

- **Host:** localhost:3306
- **Base de datos:** 4gmovil
- **Usuario:** laraveluser
- **Contraseña:** laravelpass
- **Root:** rootpassword

## 📁 Estructura de Archivos Docker

```
4GMovil/
├── docker/
│   ├── apache/
│   │   └── laravel.conf      # Configuración de Apache
│   └── init.sh               # Script de inicialización
├── docker-compose.yml        # Configuración de servicios
├── Dockerfile               # Imagen de la aplicación
├── .dockerignore            # Archivos a ignorar en build
├── env.docker.example       # Variables de entorno de ejemplo
├── deploy.sh                # Script de despliegue (Linux/Mac)
└── deploy.bat               # Script de despliegue (Windows)
```

## 🔧 Comandos Útiles

### Gestión de Contenedores

```bash
# Ver estado de contenedores
docker-compose ps

# Ver logs en tiempo real
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f app

# Detener todos los servicios
docker-compose down

# Detener y eliminar volúmenes
docker-compose down -v

# Reconstruir un servicio específico
docker-compose up --build app
```

### Acceso a Contenedores

```bash
# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Ejecutar comandos Artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear

# Acceder a la base de datos
docker-compose exec db mysql -u laraveluser -p4gmovil 4gmovil
```

### Gestión de Datos

```bash
# Backup de base de datos
docker-compose exec db mysqldump -u laraveluser -plaravelpass 4gmovil > backup.sql

# Restaurar base de datos
docker-compose exec -T db mysql -u laraveluser -plaravelpass 4gmovil < backup.sql

# Limpiar volúmenes (¡CUIDADO! Elimina todos los datos)
docker-compose down -v
docker volume prune
```

## 🛠️ Configuración Avanzada

### Variables de Entorno

Edita el archivo `.env` para configurar:

```env
# Aplicación
APP_NAME="4GMovil"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

# Base de datos
DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=4gmovil
DB_USERNAME=laraveluser
DB_PASSWORD=laravelpass

# Stripe (configurar con tus claves reales)
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...

# Google OAuth (configurar con tus credenciales)
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
```

### Personalización de Puertos

Si necesitas cambiar los puertos, edita `docker-compose.yml`:

```yaml
services:
  app:
    ports:
      - "3000:80"  # Cambiar 3000 por el puerto deseado
```

## 🐛 Solución de Problemas

### Problemas Comunes

1. **Puerto ya en uso:**
   ```bash
   # Verificar qué proceso usa el puerto
   netstat -tulpn | grep :8000
   # Cambiar puerto en docker-compose.yml
   ```

2. **Error de permisos:**
   ```bash
   # En Linux/Mac
   sudo chown -R $USER:$USER storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

3. **Base de datos no conecta:**
   ```bash
   # Verificar logs de la base de datos
   docker-compose logs db
   # Reiniciar servicios
   docker-compose restart
   ```

4. **Aplicación no carga:**
   ```bash
   # Verificar logs de la aplicación
   docker-compose logs app
   # Verificar configuración de Apache
   docker-compose exec app cat /etc/apache2/sites-available/000-default.conf
   ```

### Logs Detallados

```bash
# Ver todos los logs
docker-compose logs

# Logs con timestamps
docker-compose logs -t

# Logs de los últimos 100 líneas
docker-compose logs --tail=100
```

## 🔄 Actualizaciones

Para actualizar la aplicación:

```bash
# Detener servicios
docker-compose down

# Actualizar código (git pull, etc.)

# Reconstruir y levantar
docker-compose up --build -d

# Ejecutar migraciones si es necesario
docker-compose exec app php artisan migrate
```

## 📊 Monitoreo

### Recursos del Sistema

```bash
# Uso de recursos de contenedores
docker stats

# Información detallada de un contenedor
docker inspect 4gmovil_app
```

### Health Checks

Los servicios incluyen health checks automáticos:

```bash
# Verificar estado de salud
docker-compose ps
```

## 🆘 Soporte

Si encuentras problemas:

1. Revisa los logs: `docker-compose logs`
2. Verifica la configuración: `docker-compose config`
3. Consulta la documentación de Laravel
4. Revisa los issues del proyecto

---

**¡Listo!** Tu aplicación 4GMovil debería estar funcionando en http://localhost:8000
