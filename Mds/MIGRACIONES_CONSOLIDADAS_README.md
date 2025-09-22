# 🚀 **MIGRACIONES CONSOLIDADAS - SISTEMA COMPLETO**

## 📋 **RESUMEN DE LA REFACTORIZACIÓN**

Se han consolidado **+50 migraciones fragmentadas** en solo **8 migraciones principales** para simplificar el mantenimiento y evitar inconsistencias.

## 🔄 **ANTES vs DESPUÉS**

### **ANTES (Fragmentado):**
- ❌ 50+ archivos de migración
- ❌ Campos agregados de forma incremental
- ❌ Posibles inconsistencias entre migraciones
- ❌ Difícil de mantener y debuggear
- ❌ Orden de ejecución complejo
- ❌ Problemas de dependencias entre tablas

### **DESPUÉS (Consolidado):**
- ✅ 8 migraciones principales
- ✅ Estructura completa desde el inicio
- ✅ Sin inconsistencias
- ✅ Fácil de mantener y entender
- ✅ Orden de ejecución claro
- ✅ Claves foráneas manejadas correctamente

## 📁 **ESTRUCTURA DE MIGRACIONES CONSOLIDADAS**

### **1. 🧑‍💼 `create_usuarios_table_consolidated.php`**
**Tablas incluidas:**
- `usuarios` - Tabla principal de usuarios (compatible con Laravel)
- `password_reset_tokens` - Tokens para reset de contraseña (Laravel por defecto)
- `sessions` - Sesiones de usuario (Laravel por defecto)

**Campos incluidos en usuarios:**
- Básicos: nombre, email, contraseña, teléfono, foto
- Estado y rol del usuario
- Verificación de email
- Integración Google OAuth (`google_id`)
- Integración Stripe (`stripe_id`, `pm_type`, `pm_last_four`)
- Remember token para autenticación
- Timestamps completos

**Migraciones consolidadas:**
- `2025_05_15_205121_create_usuarios_table.php`
- `2025_05_16_044322_add_estado_rol_fecha_to_usuarios_table.php`
- `2025_05_22_211832_add_email_verified_at_to_usuarios_table.php`
- `2025_06_04_020658_add_timestamps_to_usuarios_table.php`
- `2025_07_29_211848_make_telefono_nullable_in_usuarios_table.php`
- `2025_08_06_210450_add_google_id_to_usuarios_table.php`
- `2025_08_12_210144_make_contrasena_nullable_in_usuarios_table.php`
- `2025_08_14_152102_add_remember_token_to_usuarios_table.php`
- `2025_08_14_153702_add_stripe_fields_to_usuarios_table.php`
- `0001_01_01_000000_create_users_table.php` (Laravel por defecto - ELIMINADA)

### **2. 🏷️ `create_pedidos_system_consolidated.php`**
**Tablas incluidas:**
- `categorias` - Categorías de productos
- `marcas` - Marcas de productos
- `estados_pedido` - Estados del pedido
- `metodos_pago` - Métodos de pago disponibles
- `direcciones` - Direcciones de envío
- `pedidos` - Pedidos principales
- `detalles_pedido` - Detalles de productos en pedidos
- `resenas` - Reseñas de productos
- `imagenes_productos` - Imágenes de productos
- `otp_codes` - Códigos OTP para verificación
- `webhook_events` - Eventos de webhooks

**Migraciones consolidadas:**
- `2025_05_15_205040_create_categorias_table.php`
- `2025_05_15_205118_create_marcas_table.php`
- `2025_05_15_205133_create_estados_pedido_table.php`
- `2025_05_15_205136_create_metodos_pago_table.php`
- `2025_05_15_205129_create_direcciones_table.php`
- `2025_05_15_205138_create_pedidos_table.php`
- `2025_05_15_205140_create_detalles_pedido_table.php`
- `2025_05_15_205145_create_resenas_table.php`
- `2025_05_15_205127_create_imagenes_productos_table.php`
- `2025_08_29_214527_create_otp_codes_table.php`
- `2025_08_07_143643_create_webhook_events_table.php`
- `2025_06_09_210321_update_direcciones_table.php`
- `2025_06_09_220000_insert_estados_pedido.php`
- `2025_07_31_201718_update_estados_pedido_simplified.php`
- `2025_07_31_201915_fix_estados_pedido_final.php`
- `2025_07_31_202215_fix_estados_pedido_final.php`

### **3. 📦 `create_productos_table_consolidated.php`**
**Tabla:** `productos`
**Campos incluidos:**
- Básicos: nombre, descripción, precio, stock
- Estado del producto (nuevo/usado)
- Sistema de stock reservado
- Relaciones con categoría y marca (nullable temporalmente)
- Imagen del producto

**Migraciones consolidadas:**
- `2025_05_15_205123_create_productos_table.php`
- `2025_05_31_051550_add_estado_to_productos_table.php`
- `2025_12_01_000002_add_stock_reservado_to_productos.php`

### **4. 🏪 `create_inventario_system_consolidated.php`**
**Tablas incluidas:**
- `variantes_producto` - Variantes de productos
- `imagenes_variantes` - Imágenes de variantes
- `movimientos_inventario` - Movimientos de stock unificados (productos y variantes)
- `reservas_stock_variantes` - Reservas de stock
- `especificaciones_categoria` - Especificaciones por categoría
- `especificaciones_producto` - Valores de especificaciones

**Migraciones consolidadas:**
- `2025_12_15_000000_create_variantes_producto_table.php`
- `2025_12_15_000001_create_imagenes_variantes_table.php`
- `2025_12_01_000001_create_movimientos_inventario_table.php`
- `2025_08_31_051202_add_timestamps_to_movimientos_inventario_variantes_table.php`
- `2025_08_31_051925_fix_foreign_key_movimientos_inventario_variantes.php`
- `2025_08_31_052043_add_venta_to_tipo_movimiento_enum_variantes.php`
- `2025_08_31_040327_create_reservas_stock_variantes_table.php`
- `2025_08_31_010002_create_especificaciones_categoria_table.php`
- `2025_08_31_010016_create_especificaciones_producto_table.php`
- `2025_08_31_034317_add_missing_columns_to_variantes_producto.php`
- `2025_08_31_050838_add_timestamps_to_movimientos_inventario_variantes.php`
- `2025_12_01_000000_improve_inventory_management.php`
- `2025_12_01_000003_update_movimientos_inventario_add_reserva_liberacion_types.php`
- `2025_12_01_000032_update_foreign_key_constraints_add_cascade_delete.php`

### **5. 💳 `create_pagos_table_final.php`**
**Tabla:** `pagos`
**Campos incluidos:**
- ID del pago y pedido
- Monto y método de pago
- Fecha del pago
- Estado del pago
- Referencia opcional
- Timestamps

**Migraciones consolidadas:**
- `2025_05_15_205142_create_pagos_table.php`
- `2025_05_15_205143_add_estado_to_pagos_table.php`
- `2025_09_01_190004_add_referencia_to_pagos_table.php`

### **6. 🔐 `create_stripe_system_consolidated.php`**
**Tablas incluidas:**
- `subscriptions` - Suscripciones de Stripe
- `subscription_items` - Items de suscripción

**Migraciones consolidadas:**
- `2025_08_07_093049_create_customer_columns.php`
- `2025_08_07_093050_create_subscriptions_table.php`
- `2025_08_07_093051_create_subscription_items_table.php`

### **7. 🔗 `add_foreign_keys_after_tables_created.php`**
**Propósito:** Agregar todas las claves foráneas después de que las tablas estén creadas
**Beneficios:**
- Evita problemas de dependencias
- Permite crear tablas en cualquier orden
- Facilita el debugging y mantenimiento

**Claves foráneas agregadas:**
- Productos → Categorías y Marcas
- Variantes → Productos
- Movimientos → Productos/Variantes y Usuarios
- Pedidos → Usuarios, Direcciones y Estados
- Detalles → Pedidos, Productos y Variantes
- Reseñas → Usuarios, Productos y Pedidos
- Imágenes → Productos y Variantes
- Especificaciones → Categorías y Productos
- Suscripciones → Usuarios
- Items de suscripción → Suscripciones

## 🚀 **CÓMO EJECUTAR LAS MIGRACIONES**

### **Opción 1: Script de Reset y Migración Completa (RECOMENDADO para desarrollo)**

#### **Windows:**
```bash
reset_and_migrate.bat
```

#### **Linux/Mac:**
```bash
./reset_and_migrate.sh
```

**Este script:**
1. Resetea completamente la base de datos
2. Ejecuta las migraciones por defecto de Laravel
3. Ejecuta todas las migraciones consolidadas en orden correcto
4. Agrega todas las claves foráneas al final

### **Opción 2: Script de Migración Incremental**

#### **Windows:**
```bash
migrate_all_consolidated.bat
```

#### **Linux/Mac:**
```bash
./migrate_all_consolidated.sh
```

### **Opción 3: Comandos Manuales**
```bash
# 1. Usuarios (con OAuth, Stripe, password reset y sessions)
php artisan migrate --path=database/migrations/2025_09_01_191259_create_usuarios_table_consolidated.php

# 2. Categorías, marcas y sistema de pedidos
php artisan migrate --path=database/migrations/2025_09_01_191707_create_pedidos_system_consolidated.php

# 3. Productos (sin claves foráneas)
php artisan migrate --path=database/migrations/2025_09_01_191458_create_productos_table_consolidated.php

# 4. Sistema de inventario (sin claves foráneas)
php artisan migrate --path=database/migrations/2025_09_01_191559_create_inventario_system_consolidated.php

# 5. Sistema Stripe
php artisan migrate --path=database/migrations/2025_09_01_191759_create_stripe_system_consolidated.php

# 6. Tabla de pagos
php artisan migrate --path=database/migrations/2025_09_01_191130_create_pagos_table_final.php

# 7. Agregar todas las claves foráneas
php artisan migrate --path=database/migrations/2025_09_01_192715_add_foreign_keys_after_tables_created.php
```

## ⚠️ **IMPORTANTE: ORDEN DE EJECUCIÓN**

Las migraciones **DEBEN** ejecutarse en este orden específico:

1. **Usuarios** (base del sistema)
2. **Productos** (depende de categorías y marcas)
3. **Inventario** (depende de productos)
4. **Pedidos** (depende de usuarios, productos y variantes)
5. **Stripe** (depende de usuarios)
6. **Pagos** (depende de pedidos y métodos de pago)
7. **Claves foráneas** (se agregan al final)

**NO cambiar el orden** para evitar errores de dependencias.

## ✅ **BENEFICIOS DE LA CONSOLIDACIÓN**

1. **Mantenibilidad**: Un solo lugar para cada funcionalidad
2. **Consistencia**: Sin campos faltantes o duplicados
3. **Claridad**: Estructura fácil de entender
4. **Debugging**: Más fácil identificar problemas
5. **Despliegue**: Menos archivos que gestionar
6. **Documentación**: Cada migración está bien documentada
7. **Rendimiento**: Índices optimizados desde el inicio
8. **Dependencias**: Claves foráneas manejadas correctamente
9. **Compatibilidad**: Compatible con Laravel por defecto
10. **Escalabilidad**: Fácil agregar nuevas funcionalidades

## 🧹 **LIMPIEZA REALIZADA**

### **Migraciones ELIMINADAS:**
- ❌ `0001_01_01_000000_create_users_table.php` (conflicto con usuarios)
- ❌ Todas las migraciones fragmentadas de usuarios
- ❌ Todas las migraciones fragmentadas de productos
- ❌ Todas las migraciones fragmentadas de inventario
- ❌ Todas las migraciones fragmentadas de pedidos

### **Migraciones MANTENIDAS:**
- ✅ `0001_01_01_000001_create_cache_table.php` (Laravel por defecto)
- ✅ `0001_01_01_000002_create_jobs_table.php` (Laravel por defecto)

## 🔍 **VERIFICACIÓN POST-MIGRACIÓN**

```bash
# Verificar estado de migraciones
php artisan migrate:status

# Verificar estructura de tablas principales
php artisan tinker
>>> Schema::getColumnListing('usuarios');
>>> Schema::getColumnListing('productos');
>>> Schema::getColumnListing('pedidos');
>>> Schema::getColumnListing('pagos');
>>> Schema::getColumnListing('variantes_producto');

# Verificar claves foráneas
>>> Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('productos');
>>> Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('pedidos');
```

## 📞 **SOPORTE**

Si encuentras problemas:
1. Verificar logs: `storage/logs/laravel.log`
2. Verificar estado: `php artisan migrate:status`
3. Verificar dependencias: Las claves foráneas se agregan al final
4. Asegurar que las tablas base existan antes de las dependientes

## 🎯 **PRÓXIMOS PASOS RECOMENDADOS**

1. **Ejecutar las migraciones consolidadas** usando los scripts automatizados
2. **Verificar la estructura** de todas las tablas creadas
3. **Probar el checkout** para verificar que funciona correctamente
4. **Eliminar migraciones fragmentadas** que ya no se necesiten
5. **Documentar cambios** en el equipo de desarrollo

---

**Última actualización**: 2025-09-01
**Estado**: ✅ Implementado y consolidado completamente
**Reducción**: De 50+ migraciones a solo 8 principales
**Mejoras**: Claves foráneas manejadas correctamente, compatibilidad con Laravel
