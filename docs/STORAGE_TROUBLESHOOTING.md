# 🔧 Solución de Problemas de Storage - 4GMovil

## 🚨 Problema: Las imágenes no se cargan

### Síntomas:
- Las imágenes de productos no se muestran
- Las fotos de perfil no se cargan
- Error 404 al acceder a `/storage/...`

### 🔍 Causa:
El enlace simbólico de Docker no funciona correctamente para todas las subcarpetas de storage.

### ✅ Soluciones:

#### **Solución Automática (Recomendada):**
```bash
# Ejecutar el script de sincronización
./sync-storage.bat

# O manualmente:
docker compose exec app php artisan storage:sync --force
```

#### **Solución Manual:**
```bash
# 1. Verificar que las imágenes existen
docker compose exec app ls -la /var/www/html/storage/app/public/

# 2. Copiar manualmente las carpetas
docker compose exec app cp -r /var/www/html/storage/app/public/productos /var/www/html/public/storage/
docker compose exec app cp -r /var/www/html/storage/app/public/fotos_perfil /var/www/html/public/storage/

# 3. Verificar que funcionen
docker compose exec app ls -la /var/www/html/public/storage/
```

## 🛠️ Prevención

### **1. Script de Inicialización Mejorado**
El archivo `docker/init.sh` ahora incluye sincronización automática.

### **2. Comando Artisan**
```bash
# Sincronizar storage
php artisan storage:sync

# Forzar sincronización
php artisan storage:sync --force
```

### **3. Instalación Mejorada**
El `install-docker.bat` ahora incluye sincronización automática.

## 📁 Estructura de Storage

```
storage/app/public/
├── productos/          # Imágenes de productos
├── fotos_perfil/      # Fotos de perfil de usuarios
├── perfiles/          # Fotos de perfil (alternativo)
└── otros/             # Otras imágenes

public/storage/        # Enlace simbólico (puede fallar)
├── productos/         # Copia manual
├── fotos_perfil/     # Copia manual
└── perfiles/         # Copia manual
```

## 🔄 Mantenimiento

### **Sincronización Periódica:**
```bash
# Ejecutar semanalmente o después de subir nuevas imágenes
docker compose exec app php artisan storage:sync
```

### **Verificación:**
```bash
# Verificar que las imágenes estén accesibles
curl -I http://localhost:8000/storage/productos/imagen.webp
curl -I http://localhost:8000/storage/fotos_perfil/foto.png
```

## 🚀 Solución Definitiva

Para evitar este problema en el futuro:

1. **Usar el comando de sincronización** después de cada instalación
2. **Ejecutar `sync-storage.bat`** si las imágenes no cargan
3. **Revisar los logs** si hay problemas: `docker compose logs app`

## 📞 Soporte

Si el problema persiste:
1. Verificar que Docker esté funcionando
2. Revisar los logs: `docker compose logs app`
3. Reiniciar contenedores: `docker compose restart`
4. Reconstruir si es necesario: `docker compose up --build -d`
