# Configuración de Base de Datos en Laravel Cloud

## Problema Actual
```
SQLSTATE[HY000] [2002] Connection refused
```

**Causa**: La base de datos no está configurada o no es accesible desde Laravel Cloud.

## Solución: Configurar Base de Datos en Laravel Cloud

### Paso 1: Verificar Configuración de Base de Datos

En Laravel Cloud, ve a la sección **"Database"** y verifica que:

1. **Base de datos esté creada**
2. **Credenciales estén configuradas**
3. **Conexión esté activa**

### Paso 2: Variables de Entorno de Base de Datos

Asegúrate de que estas variables estén configuradas en Laravel Cloud:

```bash
# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=laravel
DB_PASSWORD=password

# Si Laravel Cloud proporciona credenciales diferentes, úsalas
# DB_HOST=tu-host-de-base-de-datos
# DB_USERNAME=tu-usuario
# DB_PASSWORD=tu-password
```

### Paso 3: Verificar Conexión

Antes de ejecutar migraciones, verifica la conexión:

```bash
# En el terminal de Laravel Cloud
php artisan tinker
>>> DB::connection()->getPdo();
```

Si esto falla, la base de datos no está configurada correctamente.

### Paso 4: Configurar Base de Datos en Laravel Cloud

#### Opción A: Base de Datos Automática
Si Laravel Cloud proporciona una base de datos automáticamente:

1. Ve a **"Database"** en el panel de Laravel Cloud
2. **Crea una nueva base de datos** si no existe
3. **Copia las credenciales** proporcionadas
4. **Actualiza las variables de entorno** con las credenciales reales

#### Opción B: Base de Datos Externa
Si usas una base de datos externa (como PlanetScale, AWS RDS, etc.):

1. **Configura la base de datos externa**
2. **Actualiza las variables de entorno** con las credenciales reales
3. **Verifica la conectividad** desde Laravel Cloud

### Paso 5: Ejecutar Migraciones

Una vez que la base de datos esté configurada:

```bash
# Verificar conexión
php artisan migrate:status

# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders
php artisan db:seed --force
```

## Configuración de Base de Datos Recomendada

### Para Laravel Cloud (Recomendado)
```bash
# Variables de entorno para Laravel Cloud
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=laravel
DB_PASSWORD=password
```

### Para Base de Datos Externa
```bash
# Variables de entorno para base de datos externa
DB_CONNECTION=mysql
DB_HOST=tu-host-externo.com
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=tu-usuario
DB_PASSWORD=tu-password
```

## Solución de Problemas

### Error: Connection refused
**Causa**: Base de datos no configurada o credenciales incorrectas
**Solución**: Verificar configuración de base de datos en Laravel Cloud

### Error: Access denied
**Causa**: Credenciales incorrectas
**Solución**: Verificar usuario y contraseña

### Error: Database does not exist
**Causa**: Base de datos no creada
**Solución**: Crear base de datos en Laravel Cloud

## Pasos Siguientes

1. **Configurar base de datos** en Laravel Cloud
2. **Verificar credenciales** en variables de entorno
3. **Probar conexión** con `php artisan tinker`
4. **Ejecutar migraciones** una vez que la conexión funcione

## Notas Importantes

- **Laravel Cloud puede proporcionar una base de datos automáticamente**
- **Verifica las credenciales reales** en el panel de Laravel Cloud
- **No uses credenciales de Docker** en producción
- **La base de datos debe estar accesible** desde Laravel Cloud
