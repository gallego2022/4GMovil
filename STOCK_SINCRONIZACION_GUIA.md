# Guía de Sincronización de Stock: Productos y Variantes

## 📋 Resumen del Sistema

El campo `stock` en la tabla `productos` ahora funciona como el **stock total calculado** de todas sus variantes. Esto te permite:

- ✅ Mantener un stock total preciso del producto
- ✅ Tener control granular por variante (color)
- ✅ Sincronización automática cuando cambia el stock de variantes
- ✅ Reportes consolidados de inventario

## 🔄 Cómo Funciona la Sincronización

### 1. **Sincronización Automática**
Cuando cambia el stock de una variante, automáticamente se actualiza el stock del producto padre:

```php
// Ejemplo: Venta de una variante
$variante = VarianteProducto::find(1);
$variante->registrarSalida(2, 'Venta online', $usuarioId);

// Automáticamente se ejecuta:
// $producto->sincronizarStockConVariantes();
// Actualiza: producto.stock = suma de todas las variantes
```

### 2. **Cálculo del Stock Total**
```php
// Stock total del producto (suma de todas las variantes)
$producto->stock; // Campo en la base de datos
$producto->getStockTotalVariantesAttribute(); // Método calculado

// Stock disponible (solo variantes disponibles)
$producto->getStockDisponibleVariantesAttribute();
```

## 🛠️ Comandos Disponibles

### Sincronizar Stock Manualmente
```bash
# Sincronizar todos los productos
php artisan productos:sincronizar-stock

# Sincronizar un producto específico
php artisan productos:sincronizar-stock --producto-id=123

# Forzar sincronización sin confirmación
php artisan productos:sincronizar-stock --force
```

### Ejecutar desde el Código
```php
use App\Services\StockSincronizacionService;

$service = new StockSincronizacionService();

// Sincronizar un producto específico
$resultado = $service->sincronizarProducto(123);

// Sincronizar todos los productos
$resultado = $service->sincronizarTodosLosProductos();

// Verificar integridad
$integridad = $service->verificarIntegridadStock();
```

## 📊 Métodos del Modelo Producto

### Nuevos Métodos Disponibles
```php
// Sincronizar stock con variantes
$producto->sincronizarStockConVariantes();

// Verificar si tiene variantes
$producto->tieneVariantes(); // true/false

// Obtener stock real (considerando variantes o no)
$producto->stock_real; // Accessor

// Verificar stock suficiente (considerando variantes)
$producto->tieneStockSuficienteReal(5);

// Estado de stock real
$producto->estado_stock_real; // 'normal', 'bajo', 'critico', 'sin_stock'
```

### Métodos Existentes Mejorados
```php
// Stock total de variantes
$producto->stock_total_variantes; // Suma de todas las variantes

// Stock disponible de variantes
$producto->stock_disponible_variantes; // Solo variantes disponibles

// Verificar si necesita reposición
$producto->necesitaReposicionVariantes();

// Obtener variantes con stock bajo
$producto->variantes_con_stock_bajo;
```

## 🔧 Uso en el Proceso de Compra

### 1. **Verificar Stock Antes de Agregar al Carrito**
```php
public function agregarAlCarrito(Request $request)
{
    $variante = VarianteProducto::find($request->variante_id);
    
    // Verificar stock específico de la variante
    if (!$variante->tieneStockSuficiente($request->cantidad)) {
        return response()->json([
            'error' => 'Stock insuficiente para el color seleccionado'
        ], 400);
    }
    
    // También puedes verificar el stock total del producto
    if (!$variante->producto->tieneStockSuficienteReal($request->cantidad)) {
        return response()->json([
            'error' => 'Stock insuficiente del producto'
        ], 400);
    }
    
    // Agregar al carrito...
}
```

### 2. **Procesar Venta**
```php
public function procesarVenta($varianteId, $cantidad, $usuarioId)
{
    $variante = VarianteProducto::find($varianteId);
    
    // Registrar salida (automáticamente sincroniza el producto padre)
    $variante->registrarSalida($cantidad, 'Venta confirmada', $usuarioId);
    
    // El stock del producto padre se actualiza automáticamente
    // No necesitas hacer nada más
}
```

## 📈 Reportes y Monitoreo

### 1. **Reporte de Sincronización**
```php
$service = new StockSincronizacionService();
$reporte = $service->obtenerReporteSincronizacion();

// Incluye:
// - Total de productos
// - Productos con/sin variantes
// - Stock total del sistema
// - Productos desincronizados
// - Resumen por variantes
```

### 2. **Verificar Integridad**
```php
$integridad = $service->verificarIntegridadStock();

// Detecta:
// - Productos desincronizados
// - Variantes con stock pero producto sin stock
// - Problemas de consistencia
```

### 3. **Corrección Automática**
```php
$resultado = $service->corregirSincronizacion();

// Corrige automáticamente:
// - Productos desincronizados
// - Inconsistencias de stock
```

## 🎯 Casos de Uso Comunes

### 1. **Producto con Variantes de Color**
```php
// Producto: "Camiseta Básica"
// Variantes: Negro (10), Blanco (5), Azul (3)

$producto = Producto::find(1);
echo $producto->stock; // 18 (10+5+3)
echo $producto->stock_disponible_variantes; // 18 (todas disponibles)

// Venta de 2 camisetas negras
$varianteNegro = $producto->variantes()->where('nombre', 'Negro')->first();
$varianteNegro->registrarSalida(2, 'Venta', $usuarioId);

echo $producto->fresh()->stock; // 16 (18-2)
```

### 2. **Producto Sin Variantes**
```php
// Producto: "Libro de Cocina" (sin variantes)

$producto = Producto::find(2);
echo $producto->stock_real; // Usa el stock directo del producto
echo $producto->tieneVariantes(); // false
```

### 3. **Verificar Disponibilidad**
```php
// En el frontend
@foreach($producto->variantes as $variante)
    <div class="color-option {{ $variante->stock_disponible > 0 ? 'available' : 'unavailable' }}">
        <span>{{ $variante->nombre }}</span>
        <span>Stock: {{ $variante->stock_disponible }}</span>
    </div>
@endforeach

// Stock total del producto
<span>Stock total: {{ $producto->stock }}</span>
```

## ⚠️ Consideraciones Importantes

### 1. **Rendimiento**
- La sincronización automática es eficiente para la mayoría de casos
- Para productos con muchas variantes, considera sincronización diferida
- Usa índices en la base de datos para optimizar consultas

### 2. **Consistencia**
- Siempre usa los métodos del modelo para cambiar stock
- Evita actualizar directamente los campos de stock
- Ejecuta verificaciones de integridad periódicamente

### 3. **Backup y Logs**
- El sistema mantiene logs de todas las operaciones
- Considera hacer backup antes de sincronizaciones masivas
- Monitorea los logs para detectar problemas

## 🔍 Troubleshooting

### Problemas Comunes

#### 1. **Stock Desincronizado**
```bash
# Verificar integridad
php artisan tinker
>>> $service = new App\Services\StockSincronizacionService();
>>> $service->verificarIntegridadStock();

# Corregir automáticamente
>>> $service->corregirSincronizacion();
```

#### 2. **Variantes Sin Stock pero Producto con Stock**
```php
// Verificar manualmente
$producto = Producto::find(123);
$stockCalculado = $producto->getStockTotalVariantesAttribute();
$stockActual = $producto->stock;

if ($stockCalculado !== $stockActual) {
    $producto->sincronizarStockConVariantes();
}
```

#### 3. **Errores en Sincronización**
```php
// Verificar logs
tail -f storage/logs/laravel.log | grep "sincronizar"

// Reintentar sincronización
$producto->sincronizarStockConVariantes();
```

## 📝 Ejemplos Prácticos

### Ejemplo 1: Gestión de Inventario
```php
// Agregar stock a una variante específica
$variante = VarianteProducto::find(1);
$variante->registrarEntrada(50, 'Compra proveedor', $usuarioId);

// El stock del producto se actualiza automáticamente
echo $producto->fresh()->stock; // +50
```

### Ejemplo 2: Reporte de Inventario
```php
// Productos que necesitan reposición
$productosNecesitanReposicion = Producto::whereHas('variantes', function($query) {
    $query->whereRaw('stock_disponible <= stock_minimo');
})->get();

foreach ($productosNecesitanReposicion as $producto) {
    echo "Producto: {$producto->nombre_producto}\n";
    echo "Stock total: {$producto->stock}\n";
    echo "Variantes con stock bajo:\n";
    
    foreach ($producto->variantes_con_stock_bajo as $variante) {
        echo "  - {$variante->nombre}: {$variante->stock_disponible}\n";
    }
}
```

### Ejemplo 3: API para Frontend
```php
// Endpoint para obtener stock de un producto
public function getStockProducto($productoId)
{
    $producto = Producto::with('variantes')->findOrFail($productoId);
    
    return response()->json([
        'producto_id' => $producto->producto_id,
        'nombre' => $producto->nombre_producto,
        'stock_total' => $producto->stock,
        'stock_disponible' => $producto->stock_disponible_variantes,
        'tiene_variantes' => $producto->tieneVariantes(),
        'variantes' => $producto->variantes->map(function($variante) {
            return [
                'variante_id' => $variante->variante_id,
                'nombre' => $variante->nombre,
                'stock_disponible' => $variante->stock_disponible,
                'disponible' => $variante->disponible
            ];
        })
    ]);
}
```

## 🎉 Beneficios del Sistema

1. **Precisión**: Stock exacto por variante y total
2. **Automatización**: Sincronización automática
3. **Flexibilidad**: Funciona con y sin variantes
4. **Trazabilidad**: Logs completos de movimientos
5. **Escalabilidad**: Optimizado para grandes volúmenes
6. **Mantenibilidad**: Código limpio y bien documentado

Este sistema te permite manejar el stock de manera eficiente y precisa, manteniendo siempre la consistencia entre productos y sus variantes.
