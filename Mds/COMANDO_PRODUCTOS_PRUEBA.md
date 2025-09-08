# Comando de Creación de Productos de Prueba

## Descripción

El comando `productos:crear-prueba` permite generar productos de prueba con especificaciones dinámicas para todas las categorías del sistema. Este comando es especialmente útil para:

- Probar el sistema de filtros dinámicos
- Generar datos de prueba para desarrollo
- Verificar que las especificaciones funcionan correctamente
- Crear contenido de muestra para demostraciones

## Uso Básico

```bash
# Crear 10 productos por categoría (por defecto)
php artisan productos:crear-prueba

# Crear 5 productos por categoría
php artisan productos:crear-prueba --cantidad=5

# Crear productos solo para una categoría específica
php artisan productos:crear-prueba --categoria=2 --cantidad=3

# Forzar creación aunque ya existan productos
php artisan productos:crear-prueba --forzar

# Ver ayuda del comando
php artisan productos:crear-prueba --help
```

## Opciones Disponibles

| Opción | Descripción | Valor por Defecto |
|--------|-------------|-------------------|
| `--categoria` | ID de la categoría específica | Todas las categorías |
| `--cantidad` | Cantidad de productos por categoría | 10 |
| `--forzar` | Forzar creación aunque existan productos | false |

## Características del Comando

### 1. **Verificación de Dependencias**
- Verifica que existan categorías en la base de datos
- Verifica que existan marcas disponibles
- Valida que la categoría especificada exista (si se proporciona)

### 2. **Generación Inteligente de Datos**
- **Nombres de Productos**: Genera nombres realistas según la categoría
- **Precios**: Genera precios aleatorios entre $500,000 y $5,000,000 COP
- **Stock**: Genera stock aleatorio entre 5 y 50 unidades
- **SKU**: Genera códigos SKU únicos de 8 caracteres
- **Código de Barras**: Genera códigos de barras de 13 dígitos

### 3. **Especificaciones Dinámicas**
El comando genera valores realistas para cada tipo de especificación:

#### **Smartphones**
- RAM: 2GB, 3GB, 4GB, 6GB, 8GB, 12GB, 16GB, 32GB
- Almacenamiento: 32GB, 64GB, 128GB, 256GB, 512GB, 1TB
- Pantalla: 5.5", 6.1", 6.7", 7.0"
- Procesadores: Intel, AMD, Apple, Qualcomm, MediaTek
- Cámaras: 12MP, 16MP, 20MP, 24MP, 48MP, 64MP, 108MP
- Batería: 3000mAh, 4000mAh, 4500mAh, 5000mAh, 6000mAh

#### **Laptops**
- RAM: 4GB, 8GB, 16GB, 32GB, 64GB
- Almacenamiento: 128GB, 256GB, 512GB, 1TB, 2TB
- Pantalla: 13.3", 14", 15.6", 17"
- Procesadores: Intel Core i3/i5/i7/i9, AMD Ryzen 3/5/7/9
- GPU: Intel UHD, NVIDIA RTX, AMD Radeon
- Sistemas Operativos: Windows, macOS, Linux

#### **Tablets**
- RAM: 2GB, 3GB, 4GB, 6GB, 8GB
- Almacenamiento: 32GB, 64GB, 128GB, 256GB
- Pantalla: 7.9", 10.1", 11", 12.9"
- Sistemas Operativos: iOS, Android, Windows

#### **Auriculares**
- Tipo: In-ear, On-ear, Over-ear, True Wireless
- Conectividad: Cableado, Bluetooth, USB-C
- Impedancia: 16Ω, 32Ω, 64Ω, 300Ω
- Cancelación de Ruido: Sí/No

#### **Bafles**
- Tipo: Portátil, Bluetooth, WiFi, Smart
- Potencia: 5W, 10W, 20W, 50W, 100W
- Conectividad: Bluetooth, WiFi, Cableado
- Características: Resistente al agua, Luces RGB

#### **Smartwatches**
- Pantalla: 1.2", 1.4", 1.6", 1.8", 2.0"
- Batería: 1 día, 3 días, 7 días, 14 días
- Sistemas Operativos: watchOS, Wear OS, Tizen
- Sensores: GPS, Ritmo cardíaco, Oxígeno en sangre

### 4. **Manejo de Errores**
- **Transacciones de Base de Datos**: Cada producto se crea dentro de una transacción
- **Rollback Automático**: Si hay un error, se revierten todos los cambios
- **Validación de Datos**: Verifica que los datos sean válidos antes de crear
- **Mensajes Informativos**: Proporciona feedback detallado del proceso

### 5. **Progreso Visual**
- Barra de progreso para cada categoría
- Contadores de productos creados
- Mensajes de estado claros

## Ejemplos de Uso

### Crear Productos para Todas las Categorías
```bash
php artisan productos:crear-prueba
```
**Resultado**: Crea 10 productos para cada categoría que tenga especificaciones definidas.

### Crear Productos para una Categoría Específica
```bash
php artisan productos:crear-prueba --categoria=2 --cantidad=15
```
**Resultado**: Crea 15 productos solo para la categoría "Laptops" (ID: 2).

### Forzar Creación
```bash
php artisan productos:crear-prueba --forzar
```
**Resultado**: Crea productos aunque ya existan en las categorías.

### Crear Productos de Prueba para Desarrollo
```bash
# Crear pocos productos para pruebas rápidas
php artisan productos:crear-prueba --cantidad=3

# Crear muchos productos para pruebas exhaustivas
php artisan productos:crear-prueba --cantidad=50 --forzar
```

## Estructura de Datos Generados

### Producto Base
```php
[
    'nombre_producto' => 'MacBook Pro 1',
    'descripcion' => 'Producto de prueba 1 de la categoría Laptops',
    'precio' => 2500000,
    'precio_anterior' => 3000000,
    'stock' => 25,
    'stock_minimo' => 5,
    'stock_maximo' => 100,
    'categoria_id' => 2,
    'marca_id' => 1,
    'estado' => 'nuevo',
    'activo' => true,
    'sku' => 'ABC12345',
    'codigo_barras' => 1234567890123,
    'peso' => 1.5,
    'dimensiones' => '30x20x2 cm'
]
```

### Especificaciones Generadas
```php
[
    'ram' => '16',
    'almacenamiento' => '512',
    'pantalla' => '14',
    'procesador' => 'Intel Core i7',
    'sistema_operativo' => 'macOS',
    // ... más especificaciones según la categoría
]
```

## Consideraciones Importantes

### 1. **Dependencias Requeridas**
- Categorías deben existir en la base de datos
- Marcas deben estar disponibles
- Especificaciones deben estar definidas para las categorías

### 2. **Rendimiento**
- El comando usa transacciones para garantizar consistencia
- Procesa categorías una por una para evitar sobrecarga de memoria
- Incluye barra de progreso para monitorear el avance

### 3. **Seguridad**
- No modifica productos existentes (a menos que se use --forzar)
- Valida todos los datos antes de insertarlos
- Maneja errores de manera segura

### 4. **Mantenimiento**
- Los productos creados son completamente funcionales
- Pueden ser editados o eliminados normalmente
- Las especificaciones se generan según las reglas definidas

## Troubleshooting

### Error: "No hay categorías disponibles"
```bash
# Ejecutar los seeders primero
php artisan migrate:fresh --seed
```

### Error: "No hay marcas disponibles"
```bash
# Verificar que el seeder de marcas se ejecutó
php artisan db:seed --class=MarcasSeeder
```

### Error: "No hay especificaciones definidas"
```bash
# Verificar que las especificaciones estén activas
# Revisar la tabla especificaciones_categoria
```

### Error: "Ya existen productos en esta categoría"
```bash
# Usar la opción --forzar
php artisan productos:crear-prueba --forzar
```

## Integración con el Sistema

Este comando se integra perfectamente con:

- **Filtros Dinámicos**: Los productos generados funcionan con el sistema de filtros
- **Páginas de Productos**: Se muestran correctamente en el catálogo
- **Carrito de Compras**: Pueden ser agregados al carrito
- **Panel de Administración**: Pueden ser editados desde el admin

## Conclusión

El comando `productos:crear-prueba` es una herramienta esencial para el desarrollo y testing del sistema de productos con especificaciones dinámicas. Proporciona datos realistas y funcionales que permiten probar todas las características del sistema de manera eficiente.
