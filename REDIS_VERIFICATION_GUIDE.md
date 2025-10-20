# 🔍 Guía de Verificación de Redis en Laravel Cloud

## 📋 Métodos de Verificación

### **1. Verificación Rápida (Recomendada)**

#### **Comando Artisan:**
```bash
php artisan redis:verify
```

#### **Con información detallada:**
```bash
php artisan redis:verify --detailed
```

### **2. Monitoreo en Tiempo Real**

#### **Monitoreo continuo:**
```bash
php artisan redis:monitor
```

#### **Con parámetros personalizados:**
```bash
php artisan redis:monitor --interval=10 --duration=120
```

### **3. Verificación desde Navegador (Solo desarrollo)**

#### **URL de verificación:**
```
https://tu-dominio.laravel.cloud/redis-status
```

**Nota:** Solo funciona en entornos `local` y `testing` por seguridad.

### **4. Script de Verificación**

#### **Script PHP independiente:**
```bash
php verify-redis-laravel-cloud.php
```

#### **Script Bash:**
```bash
./check-redis-status.sh
```

### **5. Verificación Manual con Tinker**

#### **Conexión básica:**
```bash
php artisan tinker --execute="Redis::ping()"
```

#### **Verificar caché:**
```bash
php artisan tinker --execute="Cache::put('test', 'value', 60); echo Cache::get('test');"
```

#### **Verificar sesiones:**
```bash
php artisan tinker --execute="session(['test' => 'value']); echo session('test');"
```

#### **Verificar colas:**
```bash
php artisan tinker --execute="Queue::connection('redis'); echo 'Colas Redis OK';"
```

## 🎯 Qué Verificar

### **1. Configuración**
- ✅ `CACHE_DRIVER=redis`
- ✅ `SESSION_DRIVER=redis`
- ✅ `QUEUE_CONNECTION=redis`
- ✅ Variables de Redis configuradas

### **2. Conectividad**
- ✅ Redis responde al ping
- ✅ Conexión establecida correctamente
- ✅ Autenticación funcionando (si aplica)

### **3. Servicios**
- ✅ Caché funcionando
- ✅ Sesiones funcionando
- ✅ Colas funcionando

### **4. Operaciones**
- ✅ Lectura/escritura de datos
- ✅ Diferentes tipos de datos
- ✅ TTL (tiempo de vida) funcionando

## 🔧 Solución de Problemas

### **Error: "Connection refused"**
```bash
# Verificar que Redis esté habilitado en Laravel Cloud
# Ejecutar script de restauración
./restore-redis-config.sh
```

### **Error: "Class Redis not found"**
```bash
# Instalar extensión Redis
composer require predis/predis
# O habilitar extensión phpredis en Laravel Cloud
```

### **Error: "Authentication failed"**
```bash
# Verificar variables de entorno
echo $REDIS_PASSWORD
# Configurar correctamente en Laravel Cloud dashboard
```

### **Error: "Cache not working"**
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
# Verificar configuración
php artisan config:show cache
```

## 📊 Interpretación de Resultados

### **✅ Todo Funcionando:**
```
✅ Redis responde: PONG
✅ Caché funcionando correctamente
✅ Sesiones funcionando
✅ Colas configuradas para Redis
```

### **⚠️ Parcialmente Funcionando:**
```
✅ Redis responde: PONG
❌ Error en caché: Connection refused
✅ Sesiones funcionando
❌ Error en colas: Class Redis not found
```

### **❌ No Funcionando:**
```
❌ Error de conexión: Connection refused
❌ Error en caché: Connection refused
❌ Error en sesiones: Connection refused
❌ Error en colas: Connection refused
```

## 🚀 Comandos de Diagnóstico

### **Verificar configuración completa:**
```bash
php artisan redis:verify --detailed
```

### **Monitorear en tiempo real:**
```bash
php artisan redis:monitor --interval=5 --duration=60
```

### **Verificar logs:**
```bash
tail -f storage/logs/laravel.log | grep -i redis
```

### **Verificar colas:**
```bash
php artisan queue:work --once --verbose
```

## 📝 Notas Importantes

1. **Solo en desarrollo:** La ruta `/redis-status` solo funciona en entornos `local` y `testing`
2. **Seguridad:** No expongas información de Redis en producción
3. **Monitoreo:** Usa `redis:monitor` para verificar el rendimiento
4. **Logs:** Revisa los logs de Laravel Cloud para errores de Redis
5. **Plan de Laravel Cloud:** Asegúrate de que Redis esté habilitado en tu plan

## 🔄 Restauración de Redis

Si Redis no está funcionando, ejecuta:

```bash
# Restaurar configuración de Redis
./restore-redis-config.sh

# O manualmente
php artisan config:clear
php artisan cache:clear
```

## 📞 Soporte

Si tienes problemas persistentes:
1. Verifica que Redis esté habilitado en tu plan de Laravel Cloud
2. Revisa las variables de entorno en el dashboard
3. Contacta al soporte de Laravel Cloud
4. Revisa los logs de la aplicación
