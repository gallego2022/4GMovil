# 🔧 Solución de Problemas Docker - 4GMovil (DESARROLLO)

## Error: "vite: not found"

### Problema
```
sh: 1: vite: not found
```

### Solución
Este error ocurre porque las dependencias de desarrollo no se instalan correctamente.

**Causa**: El comando `npm ci --only=production` no instala `vite` que está en `devDependencies`.

**Solución aplicada (DESARROLLO)**:
1. Cambiar a `npm ci` (instala todas las dependencias incluyendo devDependencies)
2. Compilar assets con `npm run build`
3. Mantener dependencias de desarrollo (comentado `npm prune --production`)

## Error: "Permission denied" en storage

### Problema
```
Permission denied: /var/www/html/storage/logs/laravel.log
```

### Solución
Los permisos se configuran automáticamente en el Dockerfile:
```dockerfile
chown -R www-data:www-data /var/www/html/storage
chmod -R 755 /var/www/html/storage
chmod -R 777 /var/www/html/storage/framework
chmod -R 777 /var/www/html/storage/logs
chmod -R 777 /var/www/html/storage/app/public
```

## Error: "Database connection failed"

### Problema
```
SQLSTATE[HY000] [2002] Connection refused
```

### Solución
1. Verificar que el contenedor de base de datos esté ejecutándose:
   ```bash
   docker-compose ps
   ```

2. Verificar variables de entorno en `.env`:
   ```
   DB_HOST=db
   DB_DATABASE=4gmovil
   DB_USERNAME=laraveluser
   DB_PASSWORD=laravelpass
   ```

## Error: "Redis connection failed"

### Problema
```
Connection refused: redis:6379
```

### Solución
1. Verificar que Redis esté ejecutándose:
   ```bash
   docker-compose ps redis
   ```

2. Verificar configuración en `.env`:
   ```
   REDIS_HOST=redis
   REDIS_PORT=6379
   ```

## Comandos de diagnóstico

### Ver logs de contenedores
```bash
# Ver logs de la aplicación
docker-compose logs app

# Ver logs de base de datos
docker-compose logs db

# Ver logs de Redis
docker-compose logs redis
```

### Verificar estado de contenedores
```bash
# Estado de todos los contenedores
docker-compose ps

# Estado detallado
docker-compose ps -a
```

### Acceder a contenedor para debugging
```bash
# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Verificar instalación de dependencias
docker-compose exec app npm list

# Verificar permisos
docker-compose exec app ls -la storage/
```

### Reconstruir contenedores
```bash
# Reconstruir sin caché
docker-compose build --no-cache

# Reconstruir y ejecutar
docker-compose up --build

# Limpiar todo y empezar de nuevo
docker-compose down -v
docker-compose up --build
```

## Optimizaciones aplicadas

### Dockerfile (DESARROLLO)
- ✅ Instalación correcta de Node.js 18
- ✅ Instalación de todas las dependencias (incluyendo devDependencies)
- ✅ Compilación de assets después de instalar dependencias
- ✅ Mantenimiento de dependencias de desarrollo (para desarrollo)
- ✅ Permisos de storage configurados correctamente
- ✅ APP_ENV=local y APP_DEBUG=true

### docker-compose.yml (DESARROLLO)
- ✅ Comandos de inicialización con permisos
- ✅ Volúmenes optimizados
- ✅ Variables de entorno para desarrollo (APP_ENV=local, APP_DEBUG=true)

## Verificación de funcionamiento

### 1. Verificar que todos los servicios estén ejecutándose
```bash
docker-compose ps
```

### 2. Verificar logs sin errores
```bash
docker-compose logs app | grep -i error
```

### 3. Verificar acceso a la aplicación
- **Aplicación**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **Redis Commander**: http://localhost:8081

### 4. Verificar permisos de storage
```bash
docker-compose exec app ls -la storage/
```

## Comandos de mantenimiento

### Limpiar sistema Docker
```bash
# Limpiar contenedores parados
docker container prune

# Limpiar imágenes no utilizadas
docker image prune

# Limpiar volúmenes no utilizados
docker volume prune

# Limpiar todo (¡CUIDADO!)
docker system prune -a
```

### Backup de base de datos
```bash
# Crear backup
docker-compose exec db mysqldump -u laraveluser -plaravelpass 4gmovil > backup.sql

# Restaurar backup
docker-compose exec -T db mysql -u laraveluser -plaravelpass 4gmovil < backup.sql
```
