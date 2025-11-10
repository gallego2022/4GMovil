# 🔒 Informe de Seguridad del Dashboard - 4GMovil

## 📋 Resumen Ejecutivo

Este informe analiza la seguridad de las rutas del dashboard administrativo y identifica posibles vulnerabilidades donde usuarios no-administradores podrían acceder a funcionalidades restringidas.

---

## ✅ Rutas Correctamente Protegidas

### Rutas con Middleware `['auth', 'admin']` en `routes/admin.php`

Todas las siguientes rutas están correctamente protegidas y **SOLO** son accesibles para administradores:

#### Dashboard
- ✅ `GET /admin` → `admin.index` (Dashboard principal)

#### Productos
- ✅ `GET /admin/productos` → `admin.productos.index`
- ✅ `GET /admin/productos/create` → `admin.productos.create`
- ✅ `POST /admin/productos` → `admin.productos.store`
- ✅ `GET /admin/productos/{producto}` → `admin.productos.show`
- ✅ `GET /admin/productos/{producto}/edit` → `admin.productos.edit`
- ✅ `PUT /admin/productos/{producto}` → `admin.productos.update`
- ✅ `DELETE /admin/productos/{producto}` → `admin.productos.destroy`
- ✅ `GET /productos/listadoP` → `productos.listadoP`
- ✅ `GET /productos/{producto}/detalles` → `productos.detalles`
- ✅ `GET /productos/stock/actualizado` → `productos.stock.actualizado`
- ✅ `DELETE /productos/{producto}/imagenes/{imagen}` → `imagenes.destroy`

#### Variantes de Productos
- ✅ `GET /productos/{producto}/variantes` → `productos.variantes.index`
- ✅ `GET /productos/{producto}/variantes/create` → `productos.variantes.create`
- ✅ `POST /productos/{producto}/variantes` → `productos.variantes.store`
- ✅ `GET /productos/{producto}/variantes/{variante}/edit` → `productos.variantes.edit`
- ✅ `PUT /productos/{producto}/variantes/{variante}` → `productos.variantes.update`
- ✅ `DELETE /productos/{producto}/variantes/{variante}` → `productos.variantes.destroy`

#### Usuarios
- ✅ `GET /usuarios` → `usuarios.index`
- ✅ `GET /usuarios/create` → `usuarios.create`
- ✅ `POST /usuarios` → `usuarios.store`
- ✅ `GET /usuarios/{usuario}` → `usuarios.show`
- ✅ `GET /usuarios/{usuario}/edit` → `usuarios.edit`
- ✅ `PUT /usuarios/{usuario}` → `usuarios.update`
- ✅ `DELETE /usuarios/{usuario}` → `usuarios.destroy`
- ✅ `GET /usuarios/{usuario}/asignar-rol` → `usuarios.asignarRol`
- ✅ `POST /usuarios/{usuario}/asignar-rol` → `usuarios.updateRol`
- ✅ `PATCH /usuarios/{usuario}/toggle` → `usuarios.toggle`

#### Categorías
- ✅ Todas las rutas de `categorias` resource (index, create, store, show, edit, update, destroy)

#### Marcas
- ✅ Todas las rutas de `marcas` resource (index, create, store, show, edit, update, destroy)

#### Especificaciones
- ✅ Todas las rutas bajo `admin/especificaciones/*` → `admin.especificaciones.*`

#### Métodos de Pago
- ✅ Todas las rutas de `metodos-pago` resource

#### Pedidos (Admin)
- ✅ `GET /admin/pedidos` → `admin.pedidos.index`
- ✅ `GET /admin/pedidos/{pedido}` → `admin.pedidos.show`
- ✅ `PUT /admin/pedidos/{pedido}/estado` → `admin.pedidos.updateEstado`

#### Inventario
- ✅ `GET /admin/inventario` → `admin.inventario.dashboard`
- ✅ `GET /admin/inventario/movimientos` → `admin.inventario.movimientos`
- ✅ `GET /admin/inventario/reporte` → `admin.inventario.reporte`
- ✅ `GET /admin/inventario/reporte-pdf` → `admin.inventario.reporte-pdf`
- ✅ `GET /admin/inventario/productos-mas-vendidos` → `admin.inventario.productos-mas-vendidos`
- ✅ `GET /admin/inventario/valor-por-categoria` → `admin.inventario.valor-por-categoria`
- ✅ `GET /admin/inventario/exportar-reporte` → `admin.inventario.exportar-reporte`
- ✅ `POST /admin/inventario/registrar-entrada` → `admin.inventario.registrar-entrada`
- ✅ `POST /admin/inventario/registrar-salida` → `admin.inventario.registrar-salida`
- ✅ `POST /admin/inventario/ajustar-stock` → `admin.inventario.ajustar-stock`
- ✅ `GET /admin/inventario/alertas-optimizadas` → `admin.inventario.alertas-optimizadas`
- ✅ `GET /admin/inventario/alertas/variantes` → `admin.inventario.alertas.variantes`
- ✅ `GET /admin/inventario/alertas/estadisticas` → `admin.inventario.alertas.estadisticas`
- ✅ `GET /admin/inventario/alertas/variantes-producto` → `admin.inventario.alertas.variantes-producto`
- ✅ `POST /admin/inventario/alertas/reponer-stock` → `admin.inventario.alertas.reponer-stock`

#### Rutas API Admin
- ✅ `GET /api/admin/api/check-sku` → Protegida con `['auth', 'admin']`
- ✅ `GET /api/admin/api/check-field-name` → Protegida con `['auth', 'admin']`
- ✅ `GET /api/admin/api/check-email` → Protegida con `['auth', 'admin']`

---

## 🚨 VULNERABILIDADES IDENTIFICADAS

### ⚠️ VULNERABILIDAD CRÍTICA #1: Rutas de Reseñas Duplicadas

**Ubicación**: `routes/cliente.php` líneas 27-33

**Problema**: Las rutas de gestión de reseñas están definidas **DOS VECES** con diferentes niveles de protección:

1. **En `routes/admin.php`** (líneas 60-67): Protegidas con `['auth', 'admin']` ✅
2. **En `routes/cliente.php`** (líneas 27-33): Protegidas solo con `['auth', 'email.verified']` ❌

**Rutas afectadas**:
```php
// En cliente.php - ACCESIBLES POR CUALQUIER USUARIO AUTENTICADO
Route::prefix('productos/{producto}/resenas')->name('productos.resenas.')->group(function () {
    Route::get('/', [ProductoController::class, 'resenasIndex'])->name('index');
    Route::get('/create', [ProductoController::class, 'resenasCreate'])->name('create');
    Route::post('/', [ProductoController::class, 'resenasStore'])->name('store');
    Route::get('/{resena}/edit', [ProductoController::class, 'resenasEdit'])->name('edit');
    Route::put('/{resena}', [ProductoController::class, 'resenasUpdate'])->name('update');
    Route::delete('/{resena}', [ProductoController::class, 'resenasDestroy'])->name('destroy');
});
```

**Impacto**: 
- ❌ Cualquier usuario autenticado puede **EDITAR** reseñas de otros usuarios
- ❌ Cualquier usuario autenticado puede **ELIMINAR** reseñas de otros usuarios
- ❌ Cualquier usuario autenticado puede **VER** todas las reseñas de administración
- ❌ Cualquier usuario autenticado puede **CREAR** reseñas como administrador

**Orden de carga de rutas** (en `web.php`):
1. `admin.php` (línea 146) - Se carga primero
2. `cliente.php` (línea 147) - Se carga después y **SOBRESCRIBE** las rutas de admin

**Solución recomendada**: 
- **ELIMINAR** las rutas de reseñas de `routes/cliente.php` 
- Si los clientes necesitan gestionar sus propias reseñas, crear rutas separadas con validación de propiedad en el controlador

---

### ⚠️ VULNERABILIDAD MEDIA #2: Rutas API Públicas sin Protección

**Ubicación**: `routes/web.php` líneas 155-220

**Problema**: Hay rutas API que exponen información del sistema sin protección de admin:

#### 1. Especificaciones por Categoría
```php
// Línea 155 - ACCESIBLE SIN AUTENTICACIÓN
Route::get('/api/especificaciones/{categoriaId}', function ($categoriaId) {
    $especificaciones = \App\Models\EspecificacionCategoria::where('categoria_id', $categoriaId)
        ->where('estado', true)
        ->orderBy('orden', 'asc')
        ->get();
    return response()->json($especificaciones);
})->name('api.especificaciones.categoria');
```

**Impacto**: 
- ⚠️ Expone estructura de especificaciones del sistema
- ⚠️ Puede ser usado para mapear la estructura de datos

**Recomendación**: 
- Si es necesario para el frontend público, mantenerla
- Si solo es para admin, agregar middleware `['auth', 'admin']`

#### 2. Valores de Especificaciones
```php
// Línea 165 - ACCESIBLE SIN AUTENTICACIÓN
Route::get('/api/especificaciones/{categoriaId}/valores', function ($categoriaId) {
    // Expone valores únicos de especificaciones
    // ...
})->name('api.especificaciones.valores');
```

**Impacto**: 
- ⚠️ Expone datos de productos a través de valores de especificaciones
- ⚠️ Puede ser usado para inferir información de inventario

**Recomendación**: 
- Evaluar si es necesario para el frontend público
- Si solo es para admin, agregar middleware `['auth', 'admin']`

#### 3. Variantes de Productos
```php
// Línea 200 - ACCESIBLE SIN AUTENTICACIÓN
Route::get('/api/productos/{producto}/variantes', function ($producto) {
    $variantes = \App\Models\VarianteProducto::where('producto_id', $producto)
        ->where('disponible', true)
        ->orderBy('nombre', 'asc')
        ->get();
    // Retorna información de variantes incluyendo stock_disponible
    // ...
})->name('api.productos.variantes');
```

**Impacto**: 
- ⚠️ Expone información de **stock disponible** sin autenticación
- ⚠️ Puede ser usado para mapear inventario
- ⚠️ Información sensible de negocio

**Recomendación**: 
- **CRÍTICO**: Agregar middleware `['auth']` como mínimo
- Si solo es para admin, usar `['auth', 'admin']`
- O limitar la información expuesta (ocultar stock si no es necesario)

---

### ⚠️ VULNERABILIDAD BAJA #3: Conflicto de Rutas de Productos

**Ubicación**: `routes/admin.php` y `routes/publico.php`

**Problema**: Hay rutas de productos con el mismo patrón pero diferentes niveles de acceso:

- `routes/admin.php` línea 47: `Route::resource('productos', ProductoController::class)` → Protegida con `admin`
- `routes/publico.php` línea 18: `Route::get('/productos/{producto}', ...)` → Pública

**Impacto**: 
- ⚠️ Confusión en el enrutamiento
- ⚠️ Posible conflicto de nombres de rutas

**Estado actual**: 
- ✅ Laravel resuelve correctamente: la ruta pública tiene prioridad para GET requests
- ✅ La ruta de admin se usa para otras operaciones (POST, PUT, DELETE)

**Recomendación**: 
- Mantener como está, pero documentar claramente
- Considerar usar prefijos diferentes para evitar confusión

---

## 🔍 Análisis del Middleware de Admin

### Middleware `RequireAdminRole`

**Ubicación**: `app/Http/Middleware/RequireAdminRole.php`

**Funcionamiento**:
1. ✅ Verifica que el usuario esté autenticado
2. ✅ Verifica que el usuario tenga `rol === 'admin'`
3. ✅ Redirige a login si no está autenticado
4. ✅ Redirige a perfil si no es admin
5. ✅ Maneja excepciones correctamente

**Registro del Middleware**:
- ✅ Registrado en `app/Http/Kernel.php` línea 66 como `'admin'`
- ✅ Registrado en `bootstrap/app.php` línea 17 como alias `'admin'`

**Estado**: ✅ **FUNCIONA CORRECTAMENTE**

---

## 📊 Resumen de Seguridad

### Rutas Protegidas Correctamente
- ✅ **Dashboard**: 1 ruta
- ✅ **Productos**: 15+ rutas
- ✅ **Usuarios**: 9 rutas
- ✅ **Categorías**: 7 rutas
- ✅ **Marcas**: 7 rutas
- ✅ **Especificaciones**: 8+ rutas
- ✅ **Pedidos**: 3 rutas
- ✅ **Inventario**: 15+ rutas
- ✅ **API Admin**: 3 rutas

**Total**: ~70+ rutas correctamente protegidas

### Vulnerabilidades Encontradas
- 🚨 **CRÍTICA**: 1 (Rutas de reseñas duplicadas)
- ⚠️ **MEDIA**: 3 (Rutas API públicas)
- ⚠️ **BAJA**: 1 (Conflicto de rutas)

---

## ✅ Recomendaciones de Seguridad

### Prioridad ALTA

1. **ELIMINAR rutas de reseñas de `routes/cliente.php`**
   - Las rutas de gestión completa de reseñas deben ser solo para admin
   - Si los clientes necesitan editar sus propias reseñas, crear rutas separadas con validación de propiedad

2. **PROTEGER ruta de variantes de productos**
   - Agregar middleware `['auth']` como mínimo
   - Considerar ocultar información de stock si no es necesaria para el frontend público

### Prioridad MEDIA

3. **EVALUAR rutas API de especificaciones**
   - Determinar si son necesarias para el frontend público
   - Si solo son para admin, agregar middleware `['auth', 'admin']`

4. **DOCUMENTAR conflictos de rutas**
   - Documentar claramente qué rutas son públicas vs admin
   - Considerar usar prefijos diferentes para evitar confusión

### Prioridad BAJA

5. **AUDITORÍA de rutas**
   - Revisar periódicamente las rutas para detectar duplicados
   - Usar `php artisan route:list` para verificar todas las rutas registradas

---

## 🛠️ Comandos Útiles para Verificación

```bash
# Ver todas las rutas registradas
php artisan route:list

# Ver solo rutas de admin
php artisan route:list --name=admin

# Ver rutas de productos
php artisan route:list --name=productos

# Ver rutas de reseñas (para detectar duplicados)
php artisan route:list --name=resenas

# Limpiar caché de rutas
php artisan route:clear
php artisan config:clear
```

---

## 📝 Notas Finales

- El middleware de admin (`RequireAdminRole`) funciona correctamente
- La mayoría de las rutas están correctamente protegidas
- La vulnerabilidad crítica de las reseñas debe ser corregida inmediatamente
- Las rutas API públicas deben ser evaluadas caso por caso

---

**Fecha del análisis**: {{ date('Y-m-d') }}
**Versión de Laravel**: 12
**Versión de PHP**: 8.2.12

