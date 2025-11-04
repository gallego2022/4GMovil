# 🎯 Opciones de Pruebas para Ejecutar

## 📋 Pruebas Disponibles en el Proyecto

### ✅ **Pruebas que acabamos de crear/corregir**:
1. **InventarioServiceTest.php** - 13 pruebas ✅
2. **PedidoServiceTest.php** - 14 pruebas ✅
3. **StripeServiceTest.php** - Correcciones aplicadas

### 📊 **Otras pruebas existentes**:

#### **Autenticación (8 archivos)**:
- AdvancedAuthServiceTest.php
- AdvancedProfileManagementTest.php  
- AuthAuditTest.php
- AuthPerformanceTest.php
- AuthSecurityTest.php
- AuthServiceTest.php
- OtpEmailVerificationTest.php
- PasswordResetTest.php

#### **Carrito/Checkout (4 archivos)**:
- CarritoServiceTest.php
- CartPerformanceTest.php
- CartSecurityTest.php
- CheckoutServiceTest.php

#### **Productos (4 archivos)**:
- ProductoServiceTest.php
- ProductoServicePerformanceTest.php
- ProductoServiceSecurityTest.php
- VarianteProductoServiceTest.php
- StockSincronizacionServiceTest.php
- ReservaStockServiceTest.php

#### **Servicios Simples (7 archivos)**:
- SimpleAuthServiceTest.php
- SimpleAuthorizationServiceTest.php
- SimpleCacheServiceTest.php
- SimpleConfigurationServiceTest.php
- SimpleFileServiceTest.php
- SimpleLoggingServiceTest.php
- SimpleNotificationServiceTest.php
- SimpleValidationServiceTest.php

#### **Otros (4 archivos)**:
- NotificationServiceTest.php
- LoggingServiceTest.php
- CacheServiceTest.php
- ValidationServiceTest.php

---

## 🎯 Opciones de Ejecución

### **Opción 1: Ejecutar solo las pruebas CRÍTICAS** ⚡
```bash
docker-compose run --rm test php artisan test \
    tests/Unit/Services/InventarioServiceTest.php \
    tests/Unit/Services/Business/PedidoServiceTest.php \
    tests/Unit/Services/StripeServiceTest.php
```
**Ventaja**: Rápido, solo las pruebas nuevas/corregidas  
**Tiempo**: ~2-3 minutos

---

### **Opción 2: Ejecutar pruebas por categoría** 🎯

#### **A. Solo Autenticación**:
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/Auth*
```

#### **B. Solo Productos**:
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/Producto*.php
```

#### **C. Solo Carrito**:
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/Business/Cart*.php
```

---

### **Opción 3: Ejecutar TODAS las pruebas** ⏰
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/
```
**Ventaja**: Vista completa del estado  
**Tiempo**: ~10-15 minutos

---

### **Opción 4: Verificar grupos específicos** 🔍

#### **Verificar que Producto funciona**:
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/ProductoServiceTest.php
```

#### **Verificar que Carrito funciona**:
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/Business/CarritoServiceTest.php
```

#### **Verificar Autenticación**:
```bash
docker-compose run --rm test php artisan test tests/Unit/Services/AuthServiceTest.php
```

---

## 📊 Recomendación

### **Opción Recomendada**: Opción 1 (Críticas)
Ejecutar las pruebas que acabamos de crear/corregir para verificar que todo funciona:

```bash
docker-compose run --rm test php artisan test \
    tests/Unit/Services/InventarioServiceTest.php \
    tests/Unit/Services/Business/PedidoServiceTest.php \
    tests/Unit/Services/StripeServiceTest.php
```

**Esto ejecutará**: ~40-50 pruebas en total  
**Tiempo esperado**: ~3-5 minutos

---

## 🎯 ¿Qué quieres hacer?

**A)** Ejecutar las 3 pruebas críticas (Inventario, Pedidos, Stripe)  
**B)** Ejecutar todas las pruebas del proyecto  
**C)** Ejecutar un grupo específico (Autenticación, Productos, etc.)  
**D)** Ejecutar solo ProductoServiceTest para verificar





