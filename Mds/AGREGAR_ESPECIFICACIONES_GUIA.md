# Guía para Agregar Especificaciones a una Categoría

## Descripción

Esta guía te explica las diferentes formas de agregar especificaciones técnicas a una categoría de productos en el sistema. Las especificaciones permiten crear formularios dinámicos que se adaptan según el tipo de producto.

## Métodos Disponibles

### **1. Comando Artisan (Recomendado)**

El método más rápido y fácil es usar el comando Artisan:

```bash
# Agregar especificaciones a una categoría específica
php artisan especificaciones:agregar 7

# Forzar creación aunque existan especificaciones
php artisan especificaciones:agregar 7 --forzar

# Ver ayuda del comando
php artisan especificaciones:agregar --help
```

**Ventajas:**
- ✅ Rápido y fácil
- ✅ Especificaciones predefinidas y probadas
- ✅ Validación automática
- ✅ Barra de progreso
- ✅ Manejo de errores

### **2. Script Interactivo**

Para especificaciones personalizadas, usa el script interactivo:

```bash
php agregar_especificaciones.php
```

**Características:**
- 🎯 Menú interactivo
- 🔧 Especificaciones personalizadas
- 📋 Ver especificaciones existentes
- ⚙️ Opciones predefinidas

### **3. Base de Datos Directa**

Insertar directamente en la tabla `especificaciones_categoria`:

```sql
INSERT INTO especificaciones_categoria (
    categoria_id, 
    nombre_campo, 
    etiqueta, 
    tipo_campo, 
    opciones, 
    unidad, 
    descripcion, 
    requerido, 
    orden, 
    activo
) VALUES (
    7, 
    'tipo', 
    'Tipo de Accesorio', 
    'select', 
    '["Cable", "Cargador", "Carcasa", "Protector", "Soporte", "Otro"]', 
    NULL, 
    'Tipo de accesorio del producto', 
    1, 
    1, 
    1
);
```

### **4. Seeder Personalizado**

Crear un seeder específico:

```bash
php artisan make:seeder EspecificacionesAccesoriosSeeder
```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EspecificacionCategoria;

class EspecificacionesAccesoriosSeeder extends Seeder
{
    public function run()
    {
        $especificaciones = [
            [
                'categoria_id' => 7,
                'nombre_campo' => 'tipo',
                'etiqueta' => 'Tipo de Accesorio',
                'tipo_campo' => 'select',
                'opciones' => ['Cable', 'Cargador', 'Carcasa', 'Protector', 'Soporte', 'Otro'],
                'requerido' => true,
                'orden' => 1,
            ],
            // ... más especificaciones
        ];

        foreach ($especificaciones as $espec) {
            EspecificacionCategoria::create($espec);
        }
    }
}
```

## Tipos de Campos Disponibles

### **1. Text (`text`)**
Campo de texto libre.

```php
[
    'nombre_campo' => 'procesador',
    'etiqueta' => 'Procesador',
    'tipo_campo' => 'text',
    'unidad' => null,
    'requerido' => true,
    'orden' => 5
]
```

### **2. Number (`number`)**
Campo numérico.

```php
[
    'nombre_campo' => 'bateria',
    'etiqueta' => 'Capacidad de Batería',
    'tipo_campo' => 'number',
    'unidad' => 'mAh',
    'requerido' => true,
    'orden' => 6
]
```

### **3. Select (`select`)**
Lista desplegable con opciones predefinidas.

```php
[
    'nombre_campo' => 'ram',
    'etiqueta' => 'Memoria RAM',
    'tipo_campo' => 'select',
    'unidad' => 'GB',
    'opciones' => ['2', '3', '4', '6', '8', '12', '16', '32'],
    'requerido' => true,
    'orden' => 3
]
```

### **4. Textarea (`textarea`)**
Campo de texto multilínea.

```php
[
    'nombre_campo' => 'puertos',
    'etiqueta' => 'Puertos Disponibles',
    'tipo_campo' => 'textarea',
    'requerido' => false,
    'orden' => 11
]
```

### **5. Checkbox (`checkbox`)**
Campo de verificación (Sí/No).

```php
[
    'nombre_campo' => 'carga_rapida',
    'etiqueta' => 'Carga Rápida',
    'tipo_campo' => 'checkbox',
    'requerido' => false,
    'orden' => 11
]
```

## Especificaciones Predefinidas por Categoría

### **Smartphones (ID: 1)**
- Tamaño de Pantalla (text, pulgadas)
- Resolución (text)
- Memoria RAM (select: 2, 3, 4, 6, 8, 12, 16, 32 GB)
- Almacenamiento (select: 32, 64, 128, 256, 512, 1TB GB)
- Procesador (text)
- Capacidad de Batería (number, mAh)
- Cámara Principal (text, MP)
- Cámara Frontal (text, MP)
- Sistema Operativo (select: iOS, Android, HarmonyOS)
- Versión del SO (text)
- Carga Rápida (checkbox)
- Resistente al Agua (checkbox)

### **Laptops (ID: 2)**
- Tamaño de Pantalla (text, pulgadas)
- Resolución (select: 1366x768, 1920x1080, 2560x1440, 3840x2160)
- Memoria RAM (select: 4, 8, 16, 32, 64 GB)
- Almacenamiento (select: 128, 256, 512, 1TB, 2TB GB)
- Tipo de Almacenamiento (select: SSD, HDD, SSD + HDD)
- Procesador (text)
- Tarjeta Gráfica (text)
- Sistema Operativo (select: Windows, macOS, Linux, Sin SO)
- Duración de Batería (text, horas)
- Peso (number, kg)
- Puertos Disponibles (textarea)

### **Tablets (ID: 3)**
- Tamaño de Pantalla (text, pulgadas)
- Resolución (text)
- Memoria RAM (select: 2, 3, 4, 6, 8 GB)
- Almacenamiento (select: 32, 64, 128, 256 GB)
- Procesador (text)
- Capacidad de Batería (number, mAh)
- Cámara Principal (text, MP)
- Cámara Frontal (text, MP)
- Sistema Operativo (select: iOS, Android, Windows)
- Conectividad (select: WiFi, WiFi + Cellular, WiFi + 5G)

### **Auriculares (ID: 4)**
- Tipo de Auricular (select: In-ear, On-ear, Over-ear, True Wireless)
- Conectividad (select: Cableado, Bluetooth, USB-C, Bluetooth + Cableado)
- Impedancia (number, Ω)
- Rango de Frecuencia (text, Hz)
- Duración de Batería (text, horas)
- Cancelación de Ruido (checkbox)
- Resistente al Agua (checkbox)
- Micrófono Integrado (checkbox)

### **Bafles (ID: 5)**
- Tipo de Bafle (select: Portátil, Bluetooth, WiFi, Smart, Party)
- Potencia (number, W)
- Conectividad (select: Bluetooth, WiFi, Cableado, Bluetooth + WiFi)
- Impedancia (number, Ω)
- Rango de Frecuencia (text, Hz)
- Duración de Batería (text, horas)
- Resistente al Agua (checkbox)
- Luces RGB (checkbox)
- Entradas Disponibles (textarea)

### **Smartwatches (ID: 6)**
- Tamaño de Pantalla (text, pulgadas)
- Resolución (text)
- Duración de Batería (text, días)
- Sistema Operativo (select: watchOS, Wear OS, Tizen, Proprietary)
- Resistente al Agua (checkbox)
- GPS Integrado (checkbox)
- Monitor de Ritmo Cardíaco (checkbox)
- Monitor de Oxígeno en Sangre (checkbox)
- Conectividad (select: Bluetooth, WiFi, Bluetooth + WiFi, Bluetooth + WiFi + LTE)

### **Accesorios (ID: 7)**
- Tipo de Accesorio (select: Cable, Cargador, Carcasa, Protector, Soporte, Otro)
- Compatibilidad (text)
- Material (text)
- Color (text)
- Dimensiones (text, cm)

## Ejemplos Prácticos

### **Ejemplo 1: Agregar especificaciones a Accesorios**

```bash
# Usando el comando Artisan
php artisan especificaciones:agregar 7

# Resultado esperado:
# 📱 **Agregando especificaciones a: Accesorios**
# ✅ Tipo de Accesorio
# ✅ Compatibilidad
# ✅ Material
# ✅ Color
# ✅ Dimensiones
# 📊 Total agregadas: 5
```

### **Ejemplo 2: Crear especificaciones personalizadas**

```bash
# Usando el script interactivo
php agregar_especificaciones.php

# Seleccionar opción 2 (Crear especificaciones personalizadas)
# Seleccionar categoría ID: 7
# Ingresar especificaciones manualmente
```

### **Ejemplo 3: Verificar especificaciones existentes**

```bash
# Usando el script interactivo
php agregar_especificaciones.php

# Seleccionar opción 3 (Ver especificaciones de una categoría)
# Seleccionar categoría ID: 1

# Resultado esperado:
# 📋 **Especificaciones de Smartphones:**
#    • Tamaño de Pantalla (pantalla) - text - pulgadas [Requerido]
#    • Resolución (resolucion) - text [Requerido]
#    • Memoria RAM (ram) - select - GB [Requerido]
#    • ...
```

## Consideraciones Importantes

### **1. Orden de las Especificaciones**
- Usa el campo `orden` para controlar el orden de aparición
- Los números más bajos aparecen primero
- Recomendado: usar incrementos de 10 (1, 10, 20, 30...) para facilitar inserción de nuevas especificaciones

### **2. Nombres de Campos**
- Usa nombres en minúsculas y sin espacios
- Ejemplos: `ram`, `pantalla`, `bateria`, `sistema_operativo`
- Evita caracteres especiales excepto guiones bajos

### **3. Unidades**
- Incluye unidades cuando sea relevante
- Ejemplos: `GB`, `mAh`, `pulgadas`, `kg`, `Ω`
- Las unidades se muestran junto a la etiqueta

### **4. Opciones para Select**
- Usa arrays para las opciones
- Mantén consistencia en el formato
- Considera el orden lógico de las opciones

### **5. Validación**
- Marca como `requerido` solo los campos esenciales
- Los campos requeridos se validan automáticamente en el formulario

## Troubleshooting

### **Error: "Categoría no encontrada"**
```bash
# Verificar que la categoría existe
php artisan tinker
>>> App\Models\Categoria::all()->pluck('nombre_categoria', 'categoria_id')
```

### **Error: "Ya existen especificaciones"**
```bash
# Usar la opción --forzar
php artisan especificaciones:agregar 7 --forzar
```

### **Error: "No hay especificaciones predefinidas"**
- La categoría no tiene especificaciones predefinidas
- Usa el script interactivo para crear especificaciones personalizadas
- O crea un seeder personalizado

### **Error: "Campo duplicado"**
- Verifica que el `nombre_campo` sea único para la categoría
- Usa nombres descriptivos y únicos

## Integración con el Sistema

Una vez agregadas las especificaciones:

1. **Formulario de Productos**: Se cargan automáticamente al seleccionar la categoría
2. **Filtros Dinámicos**: Aparecen en la página de productos públicos
3. **API**: Disponibles a través de `/api/especificaciones/{categoriaId}`
4. **Comando de Prueba**: Se usan para generar productos de prueba

## Conclusión

El método más recomendado es usar el comando Artisan:

```bash
php artisan especificaciones:agregar {categoria_id}
```

Este método es rápido, seguro y proporciona especificaciones predefinidas y probadas para cada categoría. Para casos especiales, usa el script interactivo para crear especificaciones personalizadas.
