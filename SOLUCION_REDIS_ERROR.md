# Solución para Error de Conexión Redis

## Problema Identificado

El error que estás experimentando indica que Laravel no puede conectarse al servidor Redis:

```
Illuminate\Redis\Connectors\PredisConnector->connect() at /var/www/html/vendor/laravel/framework/src/Illuminate/Redis/RedisManager.php:111
```

**Error específico:** `Class "Predis\Client" not found`

## Causas del Problema

1. **Falta el archivo `.env`** - Laravel necesita este archivo para las variables de entorno
2. **Falta la dependencia Predis** - No está instalada en `composer.json`
3. **Redis no está configurado correctamente** en el entorno Docker
4. **Orden de inicio incorrecto** de los contenedores
5. **Variables de entorno faltantes** para la conexión Redis

## Solución Paso a Paso

### 1. Configurar el Entorno

**En Windows:**
```bash
setup-env.bat
```

**En Linux/Mac:**
```bash
chmod +x setup-env.sh
./setup-env.sh
```

### 2. Instalar Predis y Reconstruir Contenedores

**En Windows:**
```bash
rebuild-with-predis.bat
```

**En Linux/Mac:**
```bash
chmod +x rebuild-with-predis.sh
./rebuild-with-predis.sh
```

### 3. Solucionar la Conexión Redis (si es necesario)

**En Windows:**
```bash
fix-redis-connection.bat
```

**En Linux/Mac:**
```bash
chmod +x fix-redis-connection.sh
./fix-redis-connection.sh
```

### 4. Verificación Manual

1. **Verificar que los contenedores estén ejecutándose:**
   ```bash
   docker-compose ps
   ```

2. **Probar la conexión Redis:**
   ```bash
   docker-compose exec app php artisan tinker
   ```
   Luego ejecutar:
   ```php
   Redis::ping()
   ```

3. **Ver logs si hay problemas:**
   ```bash
   docker-compose logs redis
   docker-compose logs app
   ```

## Configuración Aplicada

### Variables de Entorno Agregadas

- `REDIS_HOST=redis`
- `REDIS_PORT=6379`
- `REDIS_PASSWORD=`
- `REDIS_DB=0`
- `REDIS_CLIENT=predis`
- `CACHE_DRIVER=redis`
- `QUEUE_CONNECTION=redis`

### Dependencias Actualizadas

- El contenedor `app` ahora depende de `redis`
- Orden de inicio: `db` → `redis` → `app` → `queue-worker`

## Verificación de la Solución

### 1. Estado de los Contenedores
```bash
docker-compose ps
```
Todos los contenedores deben estar en estado "Up".

### 2. Test de Conexión Redis
```bash
docker-compose exec app php artisan tinker
```
```php
Redis::ping()  // Debe devolver "PONG"
```

### 3. Test de Cache
```bash
docker-compose exec app php artisan tinker
```
```php
Cache::put('test', 'valor', 60)
Cache::get('test')  // Debe devolver "valor"
```

### 4. Test de Queue
```bash
docker-compose exec app php artisan queue:work --once
```

## Troubleshooting

### Si Redis sigue sin funcionar:

1. **Reiniciar todos los contenedores:**
   ```bash
   docker-compose down
   docker-compose up -d
   ```

2. **Limpiar volúmenes de Redis:**
   ```bash
   docker volume rm 4gmovil_redis_data
   docker-compose up -d
   ```

3. **Verificar configuración Redis:**
   ```bash
   docker-compose exec redis redis-cli ping
   ```

4. **Verificar red Docker:**
   ```bash
   docker network ls
   docker network inspect 4gmovil_4gmovil_network
   ```

### Si el problema persiste:

1. **Verificar logs detallados:**
   ```bash
   docker-compose logs --tail=50 redis
   docker-compose logs --tail=50 app
   ```

2. **Verificar configuración Laravel:**
   ```bash
   docker-compose exec app php artisan config:show cache
   docker-compose exec app php artisan config:show database
   ```

## Archivos Modificados

- `composer.json` - Agregada dependencia `predis/predis`
- `docker-compose.yml` - Agregadas variables Redis y dependencias
- `setup-env.bat/sh` - Script para configurar entorno
- `rebuild-with-predis.bat/sh` - Script para reconstruir con Predis
- `fix-redis-connection.bat/sh` - Script para solucionar Redis
- `SOLUCION_REDIS_ERROR.md` - Esta documentación

## Próximos Pasos

Una vez solucionado el problema:

1. **Ejecutar migraciones:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

2. **Ejecutar seeders:**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

3. **Compilar assets:**
   ```bash
   docker-compose exec app npm run build
   ```

4. **Verificar funcionamiento completo:**
   ```bash
   docker-compose exec app php artisan serve --host=0.0.0.0 --port=8000
   ```
