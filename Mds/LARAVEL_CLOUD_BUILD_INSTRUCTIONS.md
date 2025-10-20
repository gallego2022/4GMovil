# Instrucciones de Construcción para Laravel Cloud

## Problema Actual

Laravel Cloud sigue usando las configuraciones de Docker y no está aplicando nuestras configuraciones específicas, causando el error:

```
Please provide a valid cache path.
```

## Solución Definitiva

### Paso 1: Configurar el Script de Construcción en Laravel Cloud

En Laravel Cloud, configura el **Build Command** como:

```bash
chmod +x build-laravel-cloud.sh
./build-laravel-cloud.sh
```

### Paso 2: Variables de Entorno Críticas

En Laravel Cloud, configura estas variables de entorno **ANTES** de ejecutar el build:

```bash
# Variables críticas para resolver el error de caché
VIEW_COMPILED_PATH=/tmp/views
VIEW_DEBUG=false
VIEW_CACHE_ENABLED=true
VIEW_CACHE_PATH=/tmp/views
CACHE_DRIVER=file
CACHE_STORE=file
CACHE_PREFIX=4gmovil_cache

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=laravel
DB_PASSWORD=password

# Aplicación
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.laravel.cloud
```

### Paso 3: Verificar que el Script Funcione

El script `build-laravel-cloud.sh` hace lo siguiente:

1. **Elimina archivos de Docker** que causan conflictos:
   - `.env` (si existe)
   - `env.docker.example`
   - `docker-compose.yml`
   - `Dockerfile`
   - `docker/` (directorio completo)

2. **Crea directorios necesarios**:
   - `storage/framework/cache/data`
   - `storage/framework/sessions`
   - `storage/framework/views`
   - `/tmp/views`

3. **Crea archivo `.env` específico** para Laravel Cloud con todas las configuraciones correctas

4. **Configura permisos** correctos

5. **Limpia y optimiza** la aplicación

## ¿Por qué esta solución funciona?

### Problema Original:
- Laravel Cloud lee `env.docker.example` o configuraciones de Docker
- Las configuraciones de Docker tienen `DB_HOST=db` (servicio Docker)
- Laravel Cloud no puede resolver estas configuraciones
- El compilador de vistas no encuentra la ruta de caché

### Solución:
- **Eliminamos** todos los archivos de Docker
- **Forzamos** la creación de un `.env` específico para Laravel Cloud
- **Configuramos** rutas de caché que Laravel Cloud puede usar
- **Optimizamos** la aplicación para producción

## Comandos de Verificación

Después del despliegue, puedes verificar que todo funciona:

```bash
# Verificar configuración
php artisan config:show

# Verificar caché
php artisan cache:clear
php artisan config:cache

# Verificar vistas
php artisan view:clear
php artisan view:cache

# Verificar rutas
php artisan route:clear
php artisan route:cache
```

## Resultado Esperado

Con esta configuración:

1. ✅ Laravel Cloud no encontrará archivos de Docker
2. ✅ Usará el archivo `.env` específico que creamos
3. ✅ Las rutas de caché estarán configuradas correctamente
4. ✅ El error "Please provide a valid cache path" se resolverá
5. ✅ La aplicación se desplegará correctamente

## Notas Importantes

- **NO elimines** los archivos de Docker de tu repositorio GitHub
- El script los elimina solo durante el build en Laravel Cloud
- Puedes seguir usando Docker para desarrollo local
- Solo cambia la configuración para despliegue en Laravel Cloud

## Si el Problema Persiste

Si aún tienes problemas, verifica:

1. **Variables de entorno**: Asegúrate de que todas las variables estén configuradas en Laravel Cloud
2. **Script de construcción**: Verifica que el script se ejecute correctamente
3. **Logs**: Revisa los logs de Laravel Cloud para ver errores específicos
4. **Permisos**: Asegúrate de que Laravel Cloud tenga permisos de escritura en `/tmp/views`
