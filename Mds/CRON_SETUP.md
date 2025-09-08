# 🔧 Configuración de Cron Job para 4GMovil

## ¿Qué es un Cron Job?

Un **cron job** es una tarea programada que se ejecuta automáticamente en el servidor en momentos específicos. Es como un "reloj despertador" para tu servidor.

## 🎯 Tareas Programadas en 4GMovil

### **Alertas de Variantes**
- **Diario a las 9:00 AM**: Verificación completa de alertas
- **Cada 2 horas**: Verificación de stock agotado
- **Cada 4 horas**: Verificación de stock crítico
- **Cada 6 horas**: Verificación de stock bajo

### **Limpieza de Reservas**
- **Cada hora**: Limpiar reservas expiradas

### **Inventario General**
- **Diario a las 8:00 AM**: Verificación de stock con notificaciones
- **Cada 4 horas**: Verificación de stock sin notificaciones

## 🖥️ Configuración para Windows (XAMPP)

### **Opción 1: Task Scheduler (Recomendado)**

1. **Abrir Task Scheduler**
   - Presiona `Win + R`
   - Escribe `taskschd.msc`
   - Presiona Enter

2. **Crear Tarea Básica**
   - Click en "Create Basic Task"
   - Nombre: `4GMovil Scheduled Tasks`
   - Descripción: `Ejecutar tareas programadas de 4GMovil`

3. **Configurar Trigger**
   - Seleccionar "Daily"
   - Establecer hora: `00:00:00`
   - Repetir cada: `1 day`

4. **Configurar Acción**
   - Seleccionar "Start a program"
   - Program/script: `C:\xampp\php\php.exe`
   - Add arguments: `artisan schedule:run`
   - Start in: `C:\xampp\htdocs\Proyecto V11.3\4GMovil`

5. **Configuración Avanzada**
   - Marcar "Run with highest privileges"
   - En "Settings" → "Allow task to be run on demand"

### **Opción 2: Script Batch**

1. **Usar el archivo `cron-tasks.bat`**
2. **Configurar Task Scheduler para ejecutar cada minuto:**
   ```
   * * * * * "C:\xampp\htdocs\Proyecto V11.3\4GMovil\cron-tasks.bat"
   ```

## 🐧 Configuración para Linux/Unix

### **Editar Crontab**

1. **Abrir terminal y ejecutar:**
   ```bash
   crontab -e
   ```

2. **Agregar la siguiente línea:**
   ```bash
   # Ejecutar tareas programadas de 4GMovil cada minuto
   * * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
   ```

3. **O usar el script:**
   ```bash
   # Hacer ejecutable el script
   chmod +x cron-tasks.sh
   
   # Agregar al crontab
   * * * * * /ruta/a/tu/proyecto/cron-tasks.sh
   ```

## 🔍 Verificar que Funciona

### **1. Verificar Logs**
```bash
# Ver logs de cron
tail -f storage/logs/cron.log

# Ver logs de alertas
tail -f storage/logs/variantes-alertas.log
tail -f storage/logs/variantes-agotado.log
tail -f storage/logs/variantes-critico.log
tail -f storage/logs/variantes-bajo.log
```

### **2. Probar Manualmente**
```bash
# Ejecutar tareas programadas manualmente
php artisan schedule:run

# Ver tareas programadas
php artisan schedule:list
```

### **3. Verificar Comandos**
```bash
# Probar alertas
php artisan variantes:verificar-alertas

# Probar limpieza de reservas
php artisan reservas:limpiar-expiradas
```

## ⚙️ Configuración de Variables de Entorno

Agregar al archivo `.env`:

```env
# Emails para alertas de inventario
INVENTORY_ALERT_EMAILS=tu-email@gmail.com,otro-email@gmail.com

# Umbrales de alerta
INVENTORY_CRITICAL_THRESHOLD=20
INVENTORY_LOW_THRESHOLD=60

# Configuración de notificaciones
INVENTORY_NOTIFICATIONS_ENABLED=true
INVENTORY_MAX_FREQUENCY=24
INVENTORY_INCLUDE_RECOMMENDATIONS=true

# Configuración de reservas
STOCK_RESERVATION_EXPIRY=30
AUTO_CLEANUP_RESERVATIONS=true
MAX_USER_RESERVATIONS=5
```

## 🚨 Solución de Problemas

### **Problema: Las tareas no se ejecutan**
1. Verificar que el cron job esté configurado correctamente
2. Verificar permisos del archivo
3. Verificar logs en `storage/logs/cron.log`

### **Problema: No se envían emails**
1. Verificar configuración de email en `.env`
2. Verificar logs de Laravel
3. Probar envío manual de email

### **Problema: Comandos no funcionan**
1. Verificar que PHP esté en el PATH
2. Verificar que Laravel esté instalado correctamente
3. Ejecutar `php artisan config:clear`

## 📊 Monitoreo

### **Logs Importantes**
- `storage/logs/cron.log` - Ejecución de tareas programadas
- `storage/logs/variantes-alertas.log` - Alertas de variantes
- `storage/logs/reservas.log` - Limpieza de reservas
- `storage/logs/laravel.log` - Logs generales de Laravel

### **Comandos de Monitoreo**
```bash
# Ver estado de las tareas
php artisan schedule:list

# Ver logs en tiempo real
tail -f storage/logs/cron.log

# Verificar emails configurados
php artisan tinker --execute="echo config('inventory.stock_alerts.emails');"
```

## ✅ Checklist de Configuración

- [ ] Cron job configurado y funcionando
- [ ] Variables de entorno configuradas
- [ ] Emails de alerta configurados
- [ ] Logs funcionando correctamente
- [ ] Comandos probados manualmente
- [ ] Alertas enviándose correctamente
- [ ] Limpieza de reservas funcionando
