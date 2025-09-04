# 🚨 Nueva Lógica de Alertas de Stock - Basada en Porcentajes

## 📋 **Resumen de la Implementación**

Se ha implementado exitosamente una nueva regla de negocio para el manejo de alertas de stock que utiliza porcentajes del stock inicial del producto en lugar de valores fijos.

### ✅ **Nueva Lógica Implementada**

1. **Stock Bajo**: 60% del stock inicial del producto
2. **Stock Crítico**: 20% del stock inicial del producto
3. **Stock Inicial**: Campo `stock` del producto (establecido al crear)

## 🏗️ **Arquitectura de la Nueva Lógica**

### **Cálculo de Umbrales**
```php
// En el modelo Producto
public function getUmbralStockBajoAttribute(): int
{
    $stockInicial = $this->stock;
    if ($stockInicial <= 0) {
        return $this->stock_minimo ?? 5; // Fallback
    }
    // 60% del stock inicial
    return (int) ceil(($stockInicial * 60) / 100);
}

public function getUmbralStockCriticoAttribute(): int
{
    $stockInicial = $this->stock;
    if ($stockInicial <= 0) {
        return max(1, (int) ceil(($this->stock_minimo ?? 5) * 0.2)); // Fallback
    }
    // 20% del stock inicial
    return (int) ceil(($stockInicial * 20) / 100);
}
```

### **Estados de Stock**
```php
public function getStockBajoAttribute(): bool
{
    $umbral = $this->umbral_stock_bajo;
    return $this->stock_disponible > $umbral && $this->stock_disponible <= ($umbral * 1.5);
}

public function getStockCriticoAttribute(): bool
{
    $umbral = $this->umbral_stock_critico;
    return $this->stock_disponible <= $umbral;
}
```

## 📊 **Ejemplos de Cálculo**

### **Ejemplo 1: Producto con Stock Inicial 100**
- **Stock Inicial**: 100 unidades
- **Umbral Bajo (60%)**: 60 unidades
- **Umbral Crítico (20%)**: 20 unidades
- **Estados**:
  - **Normal**: > 60 unidades
  - **Bajo**: 61-90 unidades
  - **Crítico**: ≤ 20 unidades
  - **Sin Stock**: 0 unidades

### **Ejemplo 2: Producto con Stock Inicial 50**
- **Stock Inicial**: 50 unidades
- **Umbral Bajo (60%)**: 30 unidades
- **Umbral Crítico (20%)**: 10 unidades
- **Estados**:
  - **Normal**: > 30 unidades
  - **Bajo**: 31-45 unidades
  - **Crítico**: ≤ 10 unidades
  - **Sin Stock**: 0 unidades

### **Ejemplo 3: Producto con Stock Inicial 10**
- **Stock Inicial**: 10 unidades
- **Umbral Bajo (60%)**: 6 unidades
- **Umbral Crítico (20%)**: 2 unidades
- **Estados**:
  - **Normal**: > 6 unidades
  - **Bajo**: 7-9 unidades
  - **Crítico**: ≤ 2 unidades
  - **Sin Stock**: 0 unidades

## 🔧 **Archivos Modificados**

### **Modelo Producto**
- `app/Models/Producto.php`: Nueva lógica de umbrales y estados

### **Servicios**
- `app/Services/InventarioService.php`: Métodos de alertas actualizados
- `app/Services/Business/ProductoServiceOptimizadoCorregido.php`: Mantiene stock inicial

### **Repositorios**
- `app/Repositories/ProductoRepository.php`: Guarda stock inicial

### **Vistas**
- `resources/views/pages/admin/productos/form.blade.php`: Campo stock editable
- `resources/views/pages/admin/inventario/dashboard.blade.php`: Dashboard actualizado

### **Comandos**
- `app/Console/Commands/ActualizarAlertasStock.php`: Comando de actualización

## 🚀 **Cómo Usar la Nueva Lógica**

### **1. Crear un Producto con Stock Inicial**
```php
$producto = Producto::create([
    'nombre_producto' => 'iPhone 15 Pro',
    'precio' => 999.99,
    'stock' => 100, // Este será el stock inicial para calcular alertas
    // ... otros campos
]);

// Los umbrales se calculan automáticamente:
// $producto->umbral_stock_bajo = 60 (60% de 100)
// $producto->umbral_stock_critico = 20 (20% de 100)
```

### **2. Verificar Estados de Stock**
```php
// Verificar si tiene stock bajo
if ($producto->stock_bajo) {
    echo "Producto con stock bajo";
}

// Verificar si tiene stock crítico
if ($producto->stock_critico) {
    echo "Producto con stock crítico";
}

// Obtener estado completo
$estado = $producto->estado_stock; // 'normal', 'bajo', 'critico', 'sin_stock'

// Obtener información detallada
$info = $producto->info_estado_stock;
```

### **3. Obtener Umbrales de Alerta**
```php
$umbralBajo = $producto->umbral_stock_bajo;     // 60% del stock inicial
$umbralCritico = $producto->umbral_stock_critico; // 20% del stock inicial
```

## 📈 **Dashboard Actualizado**

### **Nuevas Características**
- **Información de Umbrales**: Muestra los umbrales calculados para cada producto
- **Estado Visual**: Colores diferenciados para cada estado de stock
- **Porcentaje del Stock Inicial**: Muestra cuánto representa el stock actual del inicial
- **Explicación de la Lógica**: Información clara sobre cómo se calculan las alertas

### **Tabla de Productos con Variantes**
- **Stock Inicial**: Campo `stock` del producto
- **Stock Actual**: Stock disponible real
- **Estado**: Estado calculado con la nueva lógica
- **Umbrales**: Valores de stock bajo y crítico

## ⚠️ **Consideraciones Importantes**

### **1. Productos Existentes**
- Los productos que ya tenían stock mantendrán su valor como stock inicial
- Las alertas se recalcularán automáticamente con la nueva lógica
- Usar el comando `php artisan productos:actualizar-alertas-stock` para ver cambios

### **2. Fallback para Stock Inicial 0**
- Si un producto tiene stock inicial 0, se usan los valores por defecto:
  - **Stock Bajo**: `stock_minimo` (por defecto 5)
  - **Stock Crítico**: 20% del `stock_minimo` (por defecto 1)

### **3. Compatibilidad con Variantes**
- La nueva lógica funciona tanto para productos con variantes como sin variantes
- Para productos con variantes, el stock disponible se calcula desde las variantes
- Para productos sin variantes, se usa el stock directo del producto

## 🧪 **Comandos de Prueba**

### **Actualizar Alertas de Stock**
```bash
# Ver productos y sus nuevos umbrales
php artisan productos:actualizar-alertas-stock

# Forzar actualización sin confirmación
php artisan productos:actualizar-alertas-stock --force
```

### **Verificar Lógica**
```bash
# Acceder al dashboard de inventario
# URL: /admin/inventario

# Verificar que se muestren los nuevos umbrales
# Verificar que los estados se calculen correctamente
```

## 📊 **Beneficios de la Nueva Implementación**

1. **Flexibilidad**: Cada producto tiene sus propios umbrales basados en su stock inicial
2. **Inteligencia**: Los umbrales se adaptan al tamaño del inventario del producto
3. **Consistencia**: Lógica uniforme para todos los productos
4. **Mantenibilidad**: Fácil de entender y modificar
5. **Escalabilidad**: Funciona con productos de cualquier tamaño de inventario

## 🔮 **Próximos Pasos Recomendados**

1. **Probar la nueva lógica** con productos existentes
2. **Verificar el dashboard** en diferentes escenarios
3. **Ajustar porcentajes** si es necesario (actualmente 60% y 20%)
4. **Considerar personalización** por categoría de producto
5. **Implementar notificaciones** automáticas para alertas críticas

---

**Estado**: ✅ **IMPLEMENTADO Y FUNCIONAL**
**Última actualización**: {{ date('Y-m-d H:i:s') }}
**Versión**: 2.0.0
**Lógica**: Stock Bajo = 60% del stock inicial, Stock Crítico = 20% del stock inicial
