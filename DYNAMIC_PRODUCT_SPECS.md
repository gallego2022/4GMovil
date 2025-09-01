# Sistema de Especificaciones Dinámicas de Productos

## Descripción General

Este sistema permite crear formularios dinámicos para productos basados en su categoría. Cada categoría de producto (ej: smartphones, laptops, auriculares) tiene sus propias especificaciones técnicas específicas que se cargan automáticamente cuando se selecciona la categoría en el formulario de creación/edición de productos.

## Arquitectura del Sistema

### 1. Base de Datos

#### Tabla: `especificaciones_categoria`
Define qué campos de especificación están disponibles para cada categoría.

```sql
CREATE TABLE especificaciones_categoria (
    especificacion_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    categoria_id BIGINT NOT NULL,
    nombre_campo VARCHAR(100) NOT NULL,
    etiqueta VARCHAR(255) NOT NULL,
    tipo_campo ENUM('text', 'number', 'select', 'textarea', 'checkbox', 'radio') DEFAULT 'text',
    opciones TEXT NULL, -- JSON para campos select/radio
    unidad VARCHAR(50) NULL,
    descripcion TEXT NULL,
    requerido BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id) ON DELETE CASCADE
);
```

#### Tabla: `especificaciones_producto`
Almacena los valores específicos de cada producto.

```sql
CREATE TABLE especificaciones_producto (
    especificacion_producto_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    producto_id BIGINT NOT NULL,
    especificacion_id BIGINT NOT NULL,
    valor TEXT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE,
    FOREIGN KEY (especificacion_id) REFERENCES especificaciones_categoria(especificacion_id) ON DELETE CASCADE,
    UNIQUE KEY unique_producto_especificacion (producto_id, especificacion_id)
);
```

### 2. Modelos Eloquent

#### EspecificacionCategoria
```php
class EspecificacionCategoria extends Model
{
    protected $table = 'especificaciones_categoria';
    protected $primaryKey = 'especificacion_id';
    
    protected $fillable = [
        'categoria_id', 'nombre_campo', 'etiqueta', 'tipo_campo',
        'opciones', 'unidad', 'descripcion', 'requerido', 'orden', 'activo'
    ];
    
    protected $casts = [
        'opciones' => 'array',
        'requerido' => 'boolean',
        'activo' => 'boolean'
    ];
    
    // Relaciones
    public function categoria() { /* ... */ }
    public function especificacionesProducto() { /* ... */ }
    
    // Scopes
    public function scopeActivas($query) { /* ... */ }
    public function scopePorCategoria($query, $categoriaId) { /* ... */ }
    public function scopeOrdenadas($query) { /* ... */ }
    
    // Accessors
    public function getOpcionesArrayAttribute() { /* ... */ }
    public function getEtiquetaCompletaAttribute() { /* ... */ }
}
```

#### EspecificacionProducto
```php
class EspecificacionProducto extends Model
{
    protected $table = 'especificaciones_producto';
    protected $primaryKey = 'especificacion_producto_id';
    
    protected $fillable = ['producto_id', 'especificacion_id', 'valor'];
    
    // Relaciones
    public function producto() { /* ... */ }
    public function especificacionCategoria() { /* ... */ }
}
```

### 3. API Endpoint

#### Ruta: `/api/especificaciones/{categoriaId}`
```php
Route::get('/api/especificaciones/{categoriaId}', function ($categoriaId) {
    $especificaciones = \App\Models\EspecificacionCategoria::where('categoria_id', $categoriaId)
        ->where('activo', true)
        ->orderBy('orden', 'asc')
        ->get();
    
    return response()->json($especificaciones);
})->name('api.especificaciones.categoria');
```

**Respuesta JSON:**
```json
[
    {
        "especificacion_id": 1,
        "categoria_id": 1,
        "nombre_campo": "pantalla",
        "etiqueta": "Tamaño de Pantalla",
        "tipo_campo": "text",
        "unidad": "pulgadas",
        "requerido": true,
        "orden": 1
    },
    {
        "especificacion_id": 2,
        "categoria_id": 1,
        "nombre_campo": "ram",
        "etiqueta": "Memoria RAM",
        "tipo_campo": "select",
        "opciones": ["2", "3", "4", "6", "8", "12", "16"],
        "unidad": "GB",
        "requerido": true,
        "orden": 3
    }
]
```

### 4. Frontend (JavaScript)

#### Funcionalidades Principales:

1. **Carga Dinámica de Especificaciones**
```javascript
document.getElementById('categoria_id').addEventListener('change', function() {
    const categoriaId = this.value;
    if (categoriaId) {
        cargarEspecificaciones(categoriaId);
    } else {
        limpiarEspecificaciones();
    }
});
```

2. **Renderizado de Campos**
```javascript
function crearCampoEspecificacion(espec, index) {
    const { tipo_campo, nombre_campo, etiqueta, opciones, unidad, requerido } = espec;
    
    switch (tipo_campo) {
        case 'text':
            return `<input type="text" name="especificaciones[${nombre_campo}]" 
                    class="form-input" ${requerido ? 'required' : ''} />`;
        case 'select':
            return `<select name="especificaciones[${nombre_campo}]" 
                    class="form-select" ${requerido ? 'required' : ''}>
                    <option value="">Seleccionar...</option>
                    ${opciones.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                    </select>`;
        // ... otros tipos de campo
    }
}
```

3. **Gestión de Valores**
```javascript
function guardarValorEspecificacion(nombreCampo, valor) {
    valoresEspecificaciones[nombreCampo] = valor;
}

// Al enviar el formulario
document.querySelector('form').addEventListener('submit', function(e) {
    // Agregar valores de especificaciones al formulario
    Object.keys(valoresEspecificaciones).forEach(nombreCampo => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `especificaciones[${nombreCampo}]`;
        input.value = valoresEspecificaciones[nombreCampo];
        this.appendChild(input);
    });
});
```

### 5. Backend (Controladores y Servicios)

#### ProductoController
```php
public function store(Request $request)
{
    $request->validate([
        // ... validaciones existentes
        'especificaciones.*' => 'nullable|string',
    ]);
    
    // ... lógica de creación
}

public function update(Request $request, $id)
{
    $request->validate([
        // ... validaciones existentes
        'especificaciones.*' => 'nullable|string',
    ]);
    
    // ... lógica de actualización
}
```

#### ProductoService
```php
private function saveEspecificaciones($producto, array $especificaciones): void
{
    foreach ($especificaciones as $nombreCampo => $valor) {
        $especificacionCategoria = \App\Models\EspecificacionCategoria::where('nombre_campo', $nombreCampo)
            ->where('categoria_id', $producto->categoria_id)
            ->where('activo', true)
            ->first();

        if ($especificacionCategoria) {
            \App\Models\EspecificacionProducto::updateOrCreate(
                [
                    'producto_id' => $producto->producto_id,
                    'especificacion_id' => $especificacionCategoria->especificacion_id
                ],
                ['valor' => $valor]
            );
        }
    }
}
```

## Especificaciones Predefinidas

### Smartphones
- Tamaño de Pantalla (text, pulgadas)
- Resolución (text)
- Memoria RAM (select: 2, 3, 4, 6, 8, 12, 16 GB)
- Almacenamiento (select: 32, 64, 128, 256, 512 GB)
- Procesador (text)
- Cámara Principal (text, MP)
- Batería (text, mAh)
- Sistema Operativo (select: Android, iOS)
- Conectividad (checkbox: 4G, 5G, WiFi, Bluetooth, NFC)

### Laptops
- Procesador (text)
- Memoria RAM (select: 4, 8, 16, 32 GB)
- Almacenamiento (select: 128, 256, 512, 1TB GB)
- Tarjeta Gráfica (text)
- Tamaño de Pantalla (text, pulgadas)
- Resolución (select: HD, Full HD, 2K, 4K)
- Sistema Operativo (select: Windows, macOS, Linux)
- Conectividad (checkbox: WiFi, Bluetooth, USB-C, HDMI)

### Auriculares
- Tipo (select: In-ear, On-ear, Over-ear)
- Conectividad (select: Cable, Bluetooth, Wireless)
- Cancelación de Ruido (checkbox)
- Micrófono (checkbox)
- Impedancia (text, ohmios)
- Sensibilidad (text, dB)
- Batería (text, horas)

## Flujo de Trabajo

### 1. Creación de Producto
1. Usuario selecciona categoría en el formulario
2. JavaScript detecta el cambio y hace petición AJAX a `/api/especificaciones/{categoriaId}`
3. Se renderizan dinámicamente los campos de especificación
4. Usuario completa los campos y envía el formulario
5. Backend valida y guarda el producto junto con sus especificaciones

### 2. Edición de Producto
1. Al cargar la página, se cargan las especificaciones existentes del producto
2. Se renderizan los campos con los valores actuales
3. Usuario puede modificar los valores
4. Al guardar, se actualizan las especificaciones

### 3. Visualización de Producto
1. Se cargan las especificaciones del producto desde la base de datos
2. Se muestran en la vista del producto con formato apropiado

## Ventajas del Sistema

1. **Flexibilidad**: Cada categoría puede tener sus propias especificaciones
2. **Escalabilidad**: Fácil agregar nuevas categorías y especificaciones
3. **Mantenibilidad**: Especificaciones centralizadas y reutilizables
4. **Experiencia de Usuario**: Formularios dinámicos y contextuales
5. **Consistencia**: Validación y estructura uniforme

## Configuración y Uso

### 1. Instalación
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeder de especificaciones
php artisan db:seed --class=EspecificacionesCategoriaSeeder
```

### 2. Agregar Nueva Categoría
1. Crear la categoría en la tabla `categorias`
2. Agregar especificaciones en `especificaciones_categoria`
3. El sistema automáticamente reconocerá la nueva categoría

### 3. Modificar Especificaciones
1. Editar registros en `especificaciones_categoria`
2. Los cambios se reflejan inmediatamente en el formulario

## Consideraciones Técnicas

### Rendimiento
- Las especificaciones se cargan via AJAX solo cuando es necesario
- Se utilizan índices en la base de datos para consultas eficientes
- Los valores se almacenan en caché del lado del cliente

### Seguridad
- Validación tanto en frontend como backend
- Sanitización de datos de entrada
- Protección contra inyección SQL mediante Eloquent

### Mantenimiento
- Logs detallados para debugging
- Estructura modular para fácil extensión
- Documentación completa del código

## Archivos Modificados/Creados

### Nuevos Archivos
- `database/migrations/2025_08_31_010002_create_especificaciones_categoria_table.php`
- `database/migrations/2025_08_31_010016_create_especificaciones_producto_table.php`
- `app/Models/EspecificacionCategoria.php`
- `app/Models/EspecificacionProducto.php`
- `database/seeders/EspecificacionesCategoriaSeeder.php`

### Archivos Modificados
- `app/Models/Producto.php` - Agregada relación `especificaciones`
- `app/Models/Categoria.php` - Agregada relación `especificaciones`
- `resources/views/pages/admin/productos/form.blade.php` - Agregada sección dinámica
- `app/Http/Controllers/ProductoController.php` - Agregada validación
- `app/Services/ProductoService.php` - Agregada lógica de guardado
- `routes/web.php` - Agregada ruta API

## Próximas Mejoras

1. **Editor Visual**: Interfaz para administrar especificaciones sin código
2. **Validación Avanzada**: Reglas de validación específicas por tipo de campo
3. **Importación/Exportación**: Herramientas para migrar especificaciones
4. **Búsqueda Avanzada**: Filtros por especificaciones en el catálogo
5. **Comparación**: Comparar productos por especificaciones
6. **API Completa**: Endpoints para gestión completa de especificaciones
