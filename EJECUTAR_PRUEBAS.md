# 🧪 Guía para Ejecutar Pruebas

## 📋 Comandos para Ejecutar Pruebas

### 1. **Ejecutar TODAS las pruebas del proyecto**
```bash
docker-compose run --rm test php artisan test
```

### 2. **Ejecutar pruebas de Inventario y Pedidos (las que acabamos de crear)**
```bash
docker-compose run --rm test php artisan test \
    tests/Unit/Services/InventarioServiceTest.php \
    tests/Unit/Services/Business/PedidoServiceTest.php
```

### 3. **Ejecutar solo pruebas de Inventario**
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/InventarioServiceTest.php
```

### 4. **Ejecutar solo pruebas de Pedidos**
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/Business/PedidoServiceTest.php
```

### 5. **Ejecutar todas las pruebas de Services**
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/
```

### 6. **Ejecutar con detalle (ver cada prueba individual)**
```bash
docker-compose run --rm test php artisan test --testdox
```

### 7. **Ejecutar y detenerse en el primer error**
```bash
docker-compose run --rm test php artisan test --stop-on-failure
```

### 8. **Ejecutar pruebas filtradas por patrón**
```bash
# Solo pruebas que contengan "Producto"
docker-compose run --rm test php artisan test --filter Producto

# Solo pruebas que contengan "Pedido"
docker-compose run --rm test php artisan test --filter Pedido
```

### 9. **Ejecutar pruebas con PHPUnit directamente**
```bash
docker-compose run --rm test php vendor/bin/phpunit
```

### 10. **Ver cobertura de pruebas**
```bash
docker-compose run --rm test php artisan test --coverage
```

---

## 🎯 Recomendaciones

### **Para desarrollo diario:**
```bash
# Ejecutar solo las pruebas que creaste/modificaste
docker-compose run --rm test php artisan test tests/Unit/Services/InventarioServiceTest.php tests/Unit/Services/Business/PedidoServiceTest.php
```

### **Antes de hacer commit:**
```bash
# Ejecutar todas las pruebas para asegurar que nada se rompió
docker-compose run --rm test php artisan test
```

### **Para encontrar errores:**
```bash
# Ejecutar con detalle para ver exactamente qué falla
docker-compose run --rm test php artisan test --testdox --stop-on-failure
```

---

## 📊 Interpretación de Resultados

### **Si ves:**
```
Tests:    27 passed
```
✅ **Todas las pruebas pasaron** - El código está funcionando correctamente

### **Si ves:**
```
Tests:    10 passed, 2 failed
```
⚠️ **Algunas pruebas fallaron** - Hay errores que necesitan corrección

### **Si ves:**
```
FAILED  Tests\Unit\Services\InventarioServiceTest > it_can_register_stock_entry
```
❌ **Una prueba específica falló** - Necesitas corregir ese caso

---

## 🔧 Comandos Adicionales Útiles

### **Limpiar caché antes de ejecutar pruebas**
```bash
docker-compose run --rm test php artisan cache:clear
docker-compose run --rm test php artisan config:clear
docker-compose run --rm test php artisan test
```

### **Re-ejecutar migraciones (si hay cambios)**
```bash
docker-compose run --rm test php artisan migrate:fresh --env=testing
docker-compose run --rm test php artisan test
```

### **Ver estadísticas de pruebas**
```bash
docker-compose run --rm test php artisan test --compact
```

---

## 📝 Ejemplo de Ejecución Completa

```bash
# 1. Limpiar caché
docker-compose run --rm test php artisan cache:clear

# 2. Ejecutar migraciones de prueba
docker-compose run --rm test php artisan migrate --env=testing

# 3. Ejecutar todas las pruebas
docker-compose run --rm test php artisan test

# 4. Ver resultados detallados
docker-compose run --rm test php artisan test --testdox
```

---

## 🎉 Ejecución Rápida (Todo en uno)

```bash
docker-compose run --rm test php artisan test \
    tests/Unit/Services/InventarioServiceTest.php \
    tests/Unit/Services/Business/PedidoServiceTest.php
```

**Este comando ejecutará:**
- ✅ 13 pruebas de InventarioServiceTest
- ✅ 14 pruebas de PedidoServiceTest
- ✅ Total: 27 pruebas
- ✅ Resultado esperado: 27 passed ✅

