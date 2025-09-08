# 🧹 Migraciones Limpias - Sistema Consolidado

## 📋 **Resumen de Cambios**

Se han eliminado las migraciones duplicadas y problemáticas, creando un sistema de migraciones limpio y consolidado que evita inconsistencias en la base de datos.

## 🗑️ **Migraciones Eliminadas**

### **Migraciones Duplicadas:**
- ❌ `2025_09_01_191707_create_pedidos_system_consolidated.php` - Reemplazada por migración más completa
- ❌ `2025_09_01_191130_create_pagos_table_final.php` - Integrada en migración consolidada
- ❌ `2025_09_01_192715_add_foreign_keys_after_tables_created.php` - Reemplazada por migración específica
- ❌ `2025_09_03_135423_add_estado_column_to_metodos_pago_table.php` - Campo ya incluido
- ❌ `2025_09_03_153605_add_tipo_direccion_to_direcciones_table.php` - Campo ya incluido

## ✅ **Nuevas Migraciones Limpias**

### **1. `create_direcciones_table_consolidated.php`**
- **Tabla**: `direcciones`
- **Campos**: Todos los campos necesarios incluyendo `tipo_direccion`
- **Índices**: Optimizados para consultas frecuentes
- **Timestamps**: Habilitados para seguimiento de cambios

### **2. `create_sistema_pedidos_completo.php`**
- **Tablas**:
  - `estados_pedido` - Estados del pedido con colores y orden
  - `metodos_pago` - Métodos de pago con configuración y estado
  - `pedidos` - Pedidos principales con referencias
  - `detalles_pedido` - Detalles de productos en pedidos
  - `resenas` - Sistema de reseñas con verificación
  - `pagos` - Registro de pagos con estados

### **3. `add_foreign_keys_to_sistema_pedidos.php`**
- **Claves Foráneas**: Todas las relaciones entre tablas
- **Integridad Referencial**: Configurada correctamente
- **Cascada**: Eliminación en cascada donde es apropiado

## 🚀 **Cómo Aplicar las Migraciones**

### **Opción 1: Script Automático (Recomendado)**

#### **Windows:**
```bash
reset_and_migrate_clean.bat
```

#### **Linux/Mac:**
```bash
./reset_and_migrate_clean.sh
```

### **Opción 2: Comandos Manuales**

```bash
# Limpiar cachés
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

# Resetear base de datos
php artisan migrate:reset

# Ejecutar migraciones limpias
php artisan migrate

# Ejecutar seeders
php artisan db:seed
```

## 📊 **Estructura de Tablas Final**

### **Tabla `direcciones`:**
```sql
CREATE TABLE direcciones (
    direccion_id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    usuario_id BIGINT UNSIGNED NOT NULL,
    nombre_destinatario VARCHAR(255) NOT NULL,
    telefono VARCHAR(255) NOT NULL,
    calle VARCHAR(255) NOT NULL,
    numero VARCHAR(255) NOT NULL,
    piso VARCHAR(255) NULL,
    departamento VARCHAR(255) NULL,
    codigo_postal VARCHAR(255) NOT NULL,
    ciudad VARCHAR(255) NOT NULL,
    provincia VARCHAR(255) NOT NULL,
    pais VARCHAR(255) DEFAULT 'Argentina',
    referencias TEXT NULL,
    predeterminada BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    tipo_direccion VARCHAR(255) DEFAULT 'casa',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_usuario_activo (usuario_id, activo),
    INDEX idx_predeterminada (predeterminada),
    INDEX idx_tipo_direccion (tipo_direccion)
);
```

### **Tabla `estados_pedido`:**
```sql
CREATE TABLE estados_pedido (
    estado_id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NULL,
    color VARCHAR(255) DEFAULT '#6B7280',
    activo BOOLEAN DEFAULT TRUE,
    orden INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_activo_orden (activo, orden)
);
```

### **Tabla `metodos_pago`:**
```sql
CREATE TABLE metodos_pago (
    metodo_id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    icono VARCHAR(255) NULL,
    configuracion VARCHAR(255) NULL,
    estado BOOLEAN DEFAULT TRUE,
    orden INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_estado_orden (estado, orden)
);
```

## 🔒 **Integridad Referencial**

### **Claves Foráneas Configuradas:**
- **`direcciones.usuario_id`** → `usuarios.usuario_id` (CASCADE)
- **`pedidos.usuario_id`** → `usuarios.usuario_id` (CASCADE)
- **`pedidos.direccion_id`** → `direcciones.direccion_id` (RESTRICT)
- **`pedidos.estado_id`** → `estados_pedido.estado_id` (RESTRICT)
- **`detalles_pedido.pedido_id`** → `pedidos.pedido_id` (CASCADE)
- **`resenas.usuario_id`** → `usuarios.usuario_id` (CASCADE)
- **`pagos.pedido_id`** → `pedidos.pedido_id` (CASCADE)

## 📝 **Notas Importantes**

### **Antes de Ejecutar:**
1. **Backup**: Hacer backup de la base de datos actual
2. **Datos**: Los datos existentes se perderán al hacer reset
3. **Seeders**: Se ejecutarán automáticamente para recrear datos básicos

### **Después de Ejecutar:**
1. **Verificar**: Comprobar que todas las tablas se crearon correctamente
2. **Probar**: Verificar que las funcionalidades básicas funcionen
3. **Logs**: Revisar logs por si hay errores durante la migración

## 🎯 **Beneficios de la Limpieza**

- ✅ **Sin Duplicados**: No hay migraciones que se sobrescriban
- ✅ **Estructura Clara**: Cada migración tiene un propósito específico
- ✅ **Integridad**: Claves foráneas configuradas correctamente
- ✅ **Mantenimiento**: Fácil de mantener y modificar
- ✅ **Performance**: Índices optimizados para consultas frecuentes

## 🆘 **Solución de Problemas**

### **Error: "Table already exists"**
```bash
php artisan migrate:reset
php artisan migrate
```

### **Error: "Foreign key constraint fails"**
```bash
php artisan migrate:fresh
php artisan db:seed
```

### **Error: "Column already exists"**
```bash
php artisan migrate:rollback
php artisan migrate
```

---

**⚠️ IMPORTANTE**: Esta operación eliminará todos los datos existentes. Asegúrate de hacer backup antes de proceder.
