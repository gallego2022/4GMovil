# 🗺️ **MAPA RELACIONAL COMPLETO - BASE DE DATOS 4GMOVIL**

## 📊 **VISIÓN GENERAL DEL SISTEMA**

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           SISTEMA 4GMOVIL - E-COMMERCE                        │
│                              Base de Datos Consolidada                        │
└─────────────────────────────────────────────────────────────────────────────────┘
```

## 🏗️ **ARQUITECTURA DE TABLAS**

### **1. 🧑‍💼 SISTEMA DE USUARIOS (CORE)**
```
usuarios (PK: usuario_id)
├── password_reset_tokens (PK: email)
└── sessions (PK: id, FK: usuario_id)
```

### **2. 🏷️ SISTEMA DE CATALOGO**
```
categorias (PK: categoria_id)
├── productos (PK: producto_id, FK: categoria_id, FK: marca_id)
│   ├── variantes_producto (PK: variante_id, FK: producto_id)
│   │   └── imagenes_variantes (PK: imagen_id, FK: variante_id)
│   ├── imagenes_productos (PK: imagen_id, FK: producto_id)
│   └── especificaciones_producto (PK: especificacion_producto_id, FK: producto_id, FK: especificacion_id)
├── especificaciones_categoria (PK: especificacion_id, FK: categoria_id)
└── marcas (PK: marca_id)
```

### **3. 🏪 SISTEMA DE INVENTARIO**
```
productos (PK: producto_id)
├── movimientos_inventario (PK: movimiento_id, FK: producto_id, FK: usuario_id)
└── variantes_producto (PK: variante_id, FK: producto_id)
    ├── movimientos_inventario (PK: movimiento_id, FK: variante_id, FK: usuario_id)
    └── reservas_stock_variantes (PK: reserva_id, FK: variante_id, FK: usuario_id)
```

### **4. 🛒 SISTEMA DE PEDIDOS**
```
usuarios (PK: usuario_id)
├── direcciones (PK: direccion_id, FK: usuario_id)
├── pedidos (PK: pedido_id, FK: usuario_id, FK: direccion_id, FK: estado_id)
│   ├── detalles_pedido (PK: detalle_id, FK: pedido_id, FK: producto_id, FK: variante_id)
│   └── pagos (PK: pago_id, FK: pedido_id, FK: metodo_id)
├── estados_pedido (PK: estado_id)
├── metodos_pago (PK: metodo_id)
├── resenas (PK: resena_id, FK: usuario_id, FK: producto_id, FK: pedido_id)
└── otp_codes (PK: otp_id, FK: usuario_id)
```

### **5. 💳 SISTEMA DE PAGOS Y SUSCRIPCIONES**
```
usuarios (PK: usuario_id)
├── subscriptions (PK: subscription_id, FK: usuario_id)
│   └── subscription_items (PK: subscription_item_id, FK: subscription_id)
└── pagos (PK: pago_id, FK: pedido_id, FK: metodo_id)
```

### **6. 🔄 SISTEMA DE COLAS Y CACHÉ (LARAVEL)**
```
jobs (PK: id)
├── job_batches (PK: id)
└── failed_jobs (PK: id)

cache (PK: key)
└── cache_locks (PK: key)
```

## 🔗 **RELACIONES DETALLADAS**

### **📋 TABLA: `usuarios` (CENTRAL)**
```
usuarios (PK: usuario_id)
├── 1:N → direcciones (usuario_id)
├── 1:N → pedidos (usuario_id)
├── 1:N → resenas (usuario_id)
├── 1:N → otp_codes (usuario_id)
├── 1:N → subscriptions (usuario_id)
├── 1:N → sessions (usuario_id)
├── 1:N → movimientos_inventario (usuario_id)
└── 1:N → reservas_stock_variantes (usuario_id)
```

### **📋 TABLA: `productos` (CATALOGO)**
```
productos (PK: producto_id)
├── N:1 ← categorias (categoria_id)
├── N:1 ← marcas (marca_id)
├── 1:N → variantes_producto (producto_id)
├── 1:N → imagenes_productos (producto_id)
├── 1:N → especificaciones_producto (producto_id)
├── 1:N → detalles_pedido (producto_id)
├── 1:N → resenas (producto_id)
├── 1:N → movimientos_inventario (producto_id)
└── 1:N → imagenes_productos (producto_id)
```

### **📋 TABLA: `pedidos` (ORDENES)**
```
pedidos (PK: pedido_id)
├── N:1 ← usuarios (usuario_id)
├── N:1 ← direcciones (direccion_id)
├── N:1 ← estados_pedido (estado_id)
├── 1:N → detalles_pedido (pedido_id)
├── 1:N → pagos (pedido_id)
└── 1:N → resenas (pedido_id)
```

### **📋 TABLA: `categorias` (CATALOGO)**
```
categorias (PK: categoria_id)
├── 1:N → productos (categoria_id)
└── 1:N → especificaciones_categoria (categoria_id)
```

### **📋 TABLA: `marcas` (CATALOGO)**
```
marcas (PK: marca_id)
└── 1:N → productos (marca_id)
```

## 🔐 **TIPOS DE RELACIONES**

### **1:1 (Uno a Uno)**
- No hay relaciones 1:1 en el sistema actual

### **1:N (Uno a Muchos)**
- `usuarios` → `pedidos` (un usuario puede tener muchos pedidos)
- `categorias` → `productos` (una categoría puede tener muchos productos)
- `marcas` → `productos` (una marca puede tener muchos productos)
- `productos` → `variantes_producto` (un producto puede tener muchas variantes)
- `pedidos` → `detalles_pedido` (un pedido puede tener muchos detalles)

### **N:1 (Muchos a Uno)**
- `productos` → `categorias` (muchos productos pueden pertenecer a una categoría)
- `productos` → `marcas` (muchos productos pueden pertenecer a una marca)
- `pedidos` → `usuarios` (muchos pedidos pueden pertenecer a un usuario)

### **N:M (Muchos a Muchos)**
- `productos` ↔ `especificaciones_categoria` (a través de `especificaciones_producto`)
- `usuarios` ↔ `productos` (a través de `resenas`)

## 📊 **DIAGRAMA VISUAL SIMPLIFICADO**

```
                    ┌─────────────┐
                    │   usuarios  │ ← CENTRAL
                    └─────────────┘
                           │
                    ┌──────┼──────┐
                    │      │      │
              ┌─────▼─────┐ │ ┌───▼─────┐
              │ direcciones│ │ │ pedidos │
              └───────────┘ │ └─────────┘
                           │      │
                    ┌──────▼──────┐
                    │  productos  │
                    └─────────────┘
                           │
                    ┌──────┼──────┐
                    │      │      │
              ┌─────▼─────┐ │ ┌───▼─────┐
              │categorias │ │ │ marcas  │
              └───────────┘ │ └─────────┘
```

## 🎯 **PUNTOS CLAVE DEL DISEÑO**

### **1. Normalización**
- **3NF (Tercera Forma Normal)** implementada
- Sin redundancia de datos
- Claves foráneas bien definidas

### **2. Integridad Referencial**
- **CASCADE**: Eliminación en cascada para relaciones fuertes
- **RESTRICT**: Prevención de eliminación para relaciones críticas
- **SET NULL**: Nulificación para relaciones opcionales

### **3. Índices Optimizados**
- Claves primarias indexadas automáticamente
- Índices compuestos para consultas frecuentes
- Índices en campos de búsqueda y filtrado

### **4. Escalabilidad**
- Estructura preparada para crecimiento
- Separación clara de responsabilidades
- Fácil agregar nuevas funcionalidades

## 🔍 **CONSULTAS TÍPICAS OPTIMIZADAS**

### **1. Productos por Categoría**
```sql
SELECT p.*, c.nombre as categoria, m.nombre as marca
FROM productos p
JOIN categorias c ON p.categoria_id = c.categoria_id
JOIN marcas m ON p.marca_id = m.marca_id
WHERE c.activo = true AND p.stock_disponible > 0;
```

### **2. Pedidos de Usuario con Detalles**
```sql
SELECT ped.*, d.*, ep.nombre as estado
FROM pedidos ped
JOIN direcciones d ON ped.direccion_id = d.direccion_id
JOIN estados_pedido ep ON ped.estado_id = ep.estado_id
WHERE ped.usuario_id = ?;
```

### **3. Stock Disponible por Producto**
```sql
SELECT p.nombre_producto, p.stock, p.stock_reservado, 
       p.stock_disponible, COUNT(v.variante_id) as total_variantes
FROM productos p
LEFT JOIN variantes_producto v ON p.producto_id = v.producto_id
GROUP BY p.producto_id;
```

## 📈 **VENTAJAS DEL DISEÑO ACTUAL**

### **✅ Rendimiento**
- Índices optimizados para consultas frecuentes
- Relaciones bien definidas para JOINs eficientes
- Sin redundancia de datos

### **✅ Mantenibilidad**
- Estructura clara y lógica
- Fácil de entender y modificar
- Separación de responsabilidades

### **✅ Escalabilidad**
- Preparado para crecimiento
- Fácil agregar nuevas funcionalidades
- Estructura modular

### **✅ Integridad**
- Claves foráneas bien definidas
- Restricciones de integridad
- Sin inconsistencias de datos

## 🚀 **RECOMENDACIONES PARA EL FUTURO**

### **1. Monitoreo de Rendimiento**
- Implementar logging de consultas lentas
- Monitorear uso de índices
- Optimizar consultas frecuentes

### **2. Backup y Recuperación**
- Implementar backup automático
- Estrategia de recuperación ante desastres
- Testing de restauración

### **3. Seguridad**
- Implementar auditoría de cambios
- Encriptación de datos sensibles
- Control de acceso granular

---

**Estado**: ✅ **MAPEO COMPLETO Y FUNCIONAL**  
**Fecha**: 2025-09-01  
**Base de Datos**: **4GMovil Consolidada** 🎯
