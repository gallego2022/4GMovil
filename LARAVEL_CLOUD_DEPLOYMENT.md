# 🚀 Guía de Despliegue en Laravel Cloud

## 📋 Configuración de Build Commands

Laravel Cloud puede detectar automáticamente los archivos de configuración o puedes configurarlos manualmente:

### **Opción 1: Configuración Automática (Recomendada)**
Laravel Cloud detectará automáticamente estos archivos:
- `.laravel-cloud/build.sh` - Script de construcción
- `.laravel-cloud/deploy.sh` - Script de despliegue
- `.laravel-cloud/config.json` - Configuración JSON
- `laravel-cloud.yml` - Configuración YAML

### **Opción 2: Configuración Manual en Dashboard**
En el dashboard de Laravel Cloud, configura:

#### **Build Command:**
```bash
./build-laravel-cloud.sh
```

#### **Deploy Command (opcional):**
```bash
./restore-redis-config.sh
```

## 🔧 Cómo funciona

### **1. Durante el Build:**
- ✅ Usa `laravel-cloud.env` como base
- ✅ Temporalmente cambia Redis por archivos para evitar errores
- ✅ Ejecuta migraciones y optimizaciones
- ✅ Compila assets con npm

### **2. Después del Build:**
- ✅ Restaura configuración original de Redis
- ✅ Limpia caché para aplicar nueva configuración
- ✅ Aplica optimizaciones de Redis

## 📁 Archivos utilizados

### **Archivos de configuración de Laravel Cloud:**
| Archivo | Propósito | Detección automática |
|---------|-----------|---------------------|
| `.laravel-cloud/build.sh` | Script de construcción | ✅ Sí |
| `.laravel-cloud/deploy.sh` | Script de despliegue | ✅ Sí |
| `.laravel-cloud/config.json` | Configuración JSON | ✅ Sí |
| `laravel-cloud.yml` | Configuración YAML | ✅ Sí |

### **Archivos de la aplicación:**
| Archivo | Propósito |
|---------|-----------|
| `laravel-cloud.env` | Variables de entorno principales |
| `build-laravel-cloud.sh` | Script de construcción principal |
| `restore-redis-config.sh` | Script de restauración de Redis |
| `config/database-simple.php` | Configuración de BD para build |
| `config/cache-build.php` | Configuración de caché para build |

## ⚙️ Variables de entorno importantes

### **En `laravel-cloud.env`:**
```bash
# Cache y Session (se cambian temporalmente durante build)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Base de datos (Laravel Cloud inyecta automáticamente)
# No configurar manualmente

# Redis (Laravel Cloud maneja automáticamente)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 🎯 Flujo de despliegue

1. **Laravel Cloud** ejecuta `build-laravel-cloud.sh`
2. **Script** copia `laravel-cloud.env` a `.env`
3. **Script** cambia temporalmente Redis por archivos
4. **Script** ejecuta migraciones y optimizaciones
5. **Script** restaura configuración de Redis
6. **Aplicación** funciona con Redis en runtime

## ✅ Ventajas de esta configuración

- ✅ **Usa tu configuración completa** de `laravel-cloud.env`
- ✅ **Evita errores** de Redis durante el build
- ✅ **Mantiene optimizaciones** de Redis en runtime
- ✅ **Configuración automática** de base de datos por Laravel Cloud
- ✅ **Build exitoso** sin errores de conexión

## 🔍 Troubleshooting

### **Si hay errores de Redis durante build:**
- Verifica que `config/database-simple.php` tenga timeouts cortos
- Asegúrate de que `config/cache-build.php` use solo archivos

### **Si Redis no funciona en runtime:**
- Verifica que Laravel Cloud tenga Redis habilitado
- Ejecuta `./restore-redis-config.sh` manualmente
- Revisa las variables de entorno en Laravel Cloud dashboard
