# 🚀 Sistema de Alertas Automáticas - 4GMovil S.A.S

## 📋 **Resumen del Sistema Implementado**

### **✅ Funcionalidades Completadas**

#### **1. Sistema de Alertas Inteligentes**
- **Stock Agotado** (0 unidades): Alerta inmediata con acción urgente
- **Stock Crítico** (≤20% del mínimo): Alerta urgente para reposición
- **Stock Bajo** (≤60% del mínimo): Alerta informativa para monitoreo

#### **2. Emails Profesionales**
- **Diseño responsivo** y moderno con layout base
- **Información detallada** del producto y variante
- **Recomendaciones específicas** según el tipo de alerta
- **Enlaces directos** al producto y reportes
- **Plantillas personalizables** con sistema de herencia

#### **3. Integración Automática**
- **Se dispara automáticamente** cuando el stock cambia
- **Evita spam** enviando alertas solo cuando es necesario
- **Logs detallados** para seguimiento y monitoreo

#### **4. Tareas Programadas**
- **Diario a las 9:00 AM**: Verificación completa de alertas
- **Cada 2 horas**: Verificación de stock agotado
- **Cada 4 horas**: Verificación de stock crítico
- **Cada 6 horas**: Verificación de stock bajo
- **Cada hora**: Limpieza de reservas expiradas

## 🛠️ **Archivos Creados/Modificados**

### **📧 Sistema de Emails**
- `app/Mail/StockBajoVariante.php` - Email para stock bajo/crítico
- `app/Mail/StockAgotadoVariante.php` - Email para stock agotado
- `resources/views/correo/layouts/base.blade.php` - Layout base para emails
- `resources/views/correo/stock-bajo-variante.blade.php` - Plantilla de stock bajo
- `resources/views/correo/stock-agotado-variante.blade.php` - Plantilla de stock agotado

### **⚙️ Jobs y Procesamiento**
- `app/Jobs/ProcesarAlertaStockVariante.php` - Job para procesar alertas
- `app/Models/VarianteProducto.php` - Métodos de alertas automáticas

### **🔧 Comandos Artisan**
- `app/Console/Commands/VerificarAlertasStockVariantes.php` - Verificar alertas
- `app/Console/Commands/CrearDatosPruebaVariantes.php` - Crear datos de prueba
- `app/Console/Commands/ProbarEmailsVariantes.php` - Probar envío de emails
- `app/Console/Commands/LimpiarReservasExpiradas.php` - Limpiar reservas

### **📅 Tareas Programadas**
- `app/Console/Kernel.php` - Configuración de tareas programadas
- `cron-tasks.sh` - Script para Linux/Unix
- `cron-tasks.bat` - Script para Windows
- `CRON_SETUP.md` - Documentación de configuración

### **⚙️ Configuración**
- `config/inventory.php` - Configuración del sistema de inventario
- `SISTEMA_ALERTAS_COMPLETO.md` - Este archivo de documentación

## 🎯 **Comandos Disponibles**

### **Verificación de Alertas**
```bash
# Verificar todas las alertas
php artisan variantes:verificar-alertas

# Verificar solo stock agotado
php artisan variantes:verificar-alertas --tipo=agotado

# Verificar solo stock crítico
php artisan variantes:verificar-alertas --tipo=critico

# Verificar solo stock bajo
php artisan variantes:verificar-alertas --tipo=bajo
```

### **Pruebas y Datos**
```bash
# Crear datos de prueba
php artisan variantes:crear-prueba

# Probar envío de emails
php artisan variantes:probar-emails

# Probar con email específico
php artisan variantes:probar-emails --email=tu-email@gmail.com
```

### **Mantenimiento**
```bash
# Limpiar reservas expiradas
php artisan reservas:limpiar-expiradas

# Ejecutar tareas programadas manualmente
php artisan schedule:run

# Ver tareas programadas
php artisan schedule:list
```

## 📧 **Configuración de Emails**

### **Variables de Entorno (.env)**
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

## 🔧 **Configuración del Cron Job**

### **Windows (XAMPP)**
1. Abrir Task Scheduler (`Win + R` → `taskschd.msc`)
2. Crear tarea básica: "4GMovil Scheduled Tasks"
3. Configurar para ejecutar diariamente
4. Acción: `C:\xampp\php\php.exe` con argumentos `artisan schedule:run`
5. Directorio de inicio: `C:\xampp\htdocs\Proyecto V11.3\4GMovil`

### **Linux/Unix**
```bash
# Editar crontab
crontab -e

# Agregar línea
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

## 📊 **Monitoreo y Logs**

### **Logs Importantes**
- `storage/logs/cron.log` - Ejecución de tareas programadas
- `storage/logs/variantes-alertas.log` - Alertas de variantes
- `storage/logs/variantes-agotado.log` - Stock agotado
- `storage/logs/variantes-critico.log` - Stock crítico
- `storage/logs/variantes-bajo.log` - Stock bajo
- `storage/logs/reservas.log` - Limpieza de reservas

### **Comandos de Monitoreo**
```bash
# Ver logs en tiempo real
tail -f storage/logs/cron.log

# Ver estado de las tareas
php artisan schedule:list

# Verificar emails configurados
php artisan tinker --execute="echo config('inventory.stock_alerts.emails');"
```

## 🎨 **Personalización de Plantillas**

### **Layout Base**
- Header con logo y tagline personalizable
- Footer con información de contacto
- Sistema de clases CSS para estilos
- Responsive design para móviles

### **Plantillas de Email**
- **Stock Agotado**: Diseño de emergencia con acciones inmediatas
- **Stock Crítico**: Diseño de advertencia con acciones urgentes
- **Stock Bajo**: Diseño informativo con recomendaciones

### **Elementos Personalizables**
- Colores de marca en el header
- Información de contacto en el footer
- Textos y mensajes según el tipo de alerta
- Enlaces a productos y reportes

## 🚨 **Solución de Problemas**

### **Problema: Las tareas no se ejecutan**
1. Verificar configuración del cron job
2. Verificar permisos de archivos
3. Revisar logs en `storage/logs/cron.log`

### **Problema: No se envían emails**
1. Verificar configuración de email en `.env`
2. Probar envío manual con `php artisan variantes:probar-emails`
3. Revisar logs de Laravel

### **Problema: Alertas no se disparan**
1. Verificar que las variantes tengan stock bajo
2. Ejecutar verificación manual: `php artisan variantes:verificar-alertas`
3. Revisar logs de alertas

## ✅ **Checklist de Implementación**

- [x] Sistema de alertas automáticas implementado
- [x] Emails profesionales con diseño responsivo
- [x] Tareas programadas configuradas
- [x] Comandos de prueba y mantenimiento
- [x] Documentación completa
- [x] Configuración de variables de entorno
- [x] Logs y monitoreo
- [x] Plantillas personalizables
- [x] Integración con sistema de reservas
- [x] Pruebas del sistema funcionando

## 🎉 **Próximos Pasos Sugeridos**

1. **Configurar emails** en el archivo `.env`
2. **Configurar cron job** según tu sistema operativo
3. **Personalizar plantillas** con tu marca
4. **Probar el sistema** con datos reales
5. **Configurar monitoreo** de logs
6. **Implementar notificaciones push** (opcional)
7. **Crear reportes automáticos** (opcional)

## 📞 **Soporte**

Si tienes alguna pregunta o necesitas ayuda con la configuración:

- 📧 Email: info@4gmovil.com
- 📞 Teléfono: +57 300 123 4567
- 📍 Dirección: Calle Principal #123, Ciudad, Colombia

---

**¡El sistema de alertas automáticas está listo para usar! 🚀**
