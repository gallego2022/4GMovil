# 🗄️ Comandos de Base de Datos - 4GMovil (DESARROLLO)

## Orden correcto de comandos

### 1. Verificar que los contenedores estén ejecutándose
```bash
docker-compose ps
```

### 2. Ejecutar migraciones (crear tablas)
```bash
docker-compose exec app php artisan migrate
```

### 3. Ejecutar seeders (poblar tablas)
```bash
docker-compose exec app php artisan db:seed
```

### 4. Verificar que todo esté funcionando
```bash
docker-compose exec app php artisan migrate:status
```

## Comandos alternativos

### Si las migraciones fallan, reiniciar todo:
```bash
# Detener contenedores
docker-compose down

# Limpiar volúmenes (¡CUIDADO! Esto borra la base de datos)
docker-compose down -v

# Reconstruir y ejecutar
docker-compose up -d --build

# Esperar que la base de datos esté lista
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### Si hay problemas de permisos:
```bash
# Verificar permisos de storage
docker-compose exec app ls -la storage/

# Reaplicar permisos si es necesario
docker-compose exec app chmod -R 777 storage/
```

### Si hay problemas de conexión a la base de datos:
```bash
# Verificar conexión
docker-compose exec app php artisan tinker
# En tinker: DB::connection()->getPdo();

# Verificar variables de entorno
docker-compose exec app php artisan config:show database
```

## Verificación de funcionamiento

### 1. Verificar que las tablas existan
```bash
docker-compose exec db mysql -u laraveluser -plaravelpass 4gmovil -e "SHOW TABLES;"
```

### 2. Verificar que los seeders funcionaron
```bash
docker-compose exec db mysql -u laraveluser -plaravelpass 4gmovil -e "SELECT COUNT(*) FROM usuarios;"
```

### 3. Verificar logs de la aplicación
```bash
docker-compose logs app | grep -i error
```

## Solución de problemas comunes

### Error: "Table doesn't exist"
**Causa**: Las migraciones no se ejecutaron
**Solución**: Ejecutar `php artisan migrate` antes de `php artisan db:seed`

### Error: "Connection refused"
**Causa**: La base de datos no está lista
**Solución**: Esperar unos segundos y verificar que el contenedor `db` esté ejecutándose

### Error: "Permission denied"
**Causa**: Permisos de storage incorrectos
**Solución**: Reaplicar permisos con los comandos del Dockerfile

### Error: "Class not found"
**Causa**: Autoloader no actualizado
**Solución**: Ejecutar `composer dump-autoload` (ya está en el Dockerfile)
