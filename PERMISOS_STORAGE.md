# 🔐 Configuración de Permisos de Storage - 4GMovil (DESARROLLO)

## Estructura de permisos

### Directorios principales
```
storage/
├── framework/          # 777 (cache, sessions, views)
├── logs/              # 777 (archivos de log)
├── app/
│   └── public/        # 777 (archivos subidos)
└── (otros)            # 755 (seguridad)

bootstrap/cache/        # 777 (caché de autoloader)
public/storage/         # 755 (enlace simbólico)
```

## Permisos específicos

### Storage Framework (777)
- `storage/framework/cache/` - Caché de aplicación
- `storage/framework/sessions/` - Sesiones de usuario
- `storage/framework/views/` - Vistas compiladas

### Storage Logs (777)
- `storage/logs/` - Archivos de log de Laravel

### Storage App Public (777)
- `storage/app/public/productos/` - Imágenes de productos
- `storage/app/public/fotos_perfil/` - Fotos de perfil
- `storage/app/public/perfiles/` - Archivos de perfil

### Bootstrap Cache (777)
- `bootstrap/cache/` - Caché de autoloader y configuración

### Public Storage (755)
- `public/storage/` - Enlace simbólico a storage/app/public

## Comandos de permisos

### Para Docker
```bash
# Aplicar permisos completos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage
chmod -R 755 /var/www/html/storage
chmod -R 777 /var/www/html/storage/framework /var/www/html/storage/logs /var/www/html/storage/app/public
chmod -R 777 /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/public/storage
```

### Para Laravel Cloud
```bash
# Aplicar permisos
chmod -R 755 storage
chmod -R 777 storage/framework storage/logs storage/app/public
chmod -R 777 bootstrap/cache
chmod -R 755 public/storage
```

## Verificación de permisos

### Verificar permisos actuales
```bash
ls -la storage/
ls -la storage/framework/
ls -la storage/logs/
ls -la storage/app/public/
ls -la bootstrap/cache/
ls -la public/storage/
```

### Verificar propietario
```bash
ls -la storage/ | head -5
ls -la bootstrap/cache/ | head -5
```

## Solución de problemas

### Error: "Permission denied"
```bash
# Reaplicar permisos
sudo chown -R www-data:www-data storage bootstrap/cache public/storage
sudo chmod -R 755 storage
sudo chmod -R 777 storage/framework storage/logs storage/app/public
sudo chmod -R 777 bootstrap/cache
```

### Error: "Storage link not found"
```bash
# Recrear enlace simbólico
php artisan storage:link
chmod -R 755 public/storage
```

### Error: "Cache directory not writable"
```bash
# Aplicar permisos de caché
chmod -R 777 storage/framework bootstrap/cache
```

## Seguridad

### Permisos seguros
- **755**: Directorios que no necesitan escritura
- **777**: Solo para directorios que Laravel debe escribir
- **www-data**: Usuario propietario en Docker
- **apache**: Usuario propietario en Laravel Cloud

### Directorios críticos
- `storage/framework/` - Debe ser 777
- `storage/logs/` - Debe ser 777
- `storage/app/public/` - Debe ser 777
- `bootstrap/cache/` - Debe ser 777

## Monitoreo

### Verificar funcionamiento
```bash
# Probar escritura en caché
php artisan cache:clear

# Probar escritura en logs
php artisan log:clear

# Probar subida de archivos
# (usar interfaz web para subir imagen)
```
