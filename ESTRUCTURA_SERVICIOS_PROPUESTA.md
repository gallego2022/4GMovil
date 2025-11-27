# 📁 Estructura Propuesta de Servicios - 4GMovil

## 🎯 Organización por Dominios/Funcionalidades

```
app/Services/
├── 📁 Base/
│   └── BaseService.php                    # Clase base abstracta
│
├── 📁 Catalog/                            # Catálogo de productos
│   ├── ProductoService.php
│   └── CategoriaService.php
│
├── 📁 Inventory/                          # Gestión de inventario
│   ├── InventarioService.php
│   ├── StockAlertService.php              # (OptimizedStockAlertService)
│   ├── StockSincronizacionService.php
│   └── ReservaStockService.php
│
├── 📁 Orders/                             # Pedidos y checkout
│   ├── PedidoService.php
│   ├── CheckoutService.php
│   └── CarritoService.php
│
├── 📁 Payment/                            # Pagos
│   └── StripeService.php
│
├── 📁 Auth/                               # Autenticación y seguridad
│   ├── AuthService.php
│   ├── JwtService.php
│   ├── OtpService.php
│   └── AuthorizationService.php
│
├── 📁 User/                               # Gestión de usuarios
│   └── UsuarioService.php
│
├── 📁 Notification/                       # Notificaciones
│   ├── NotificationService.php
│   ├── AdminNotificationService.php
│   └── PedidoNotificationService.php
│
├── 📁 Dashboard/                          # Dashboard y métricas
│   ├── DashboardService.php
│   └── DashboardMetricsService.php
│
├── 📁 Public/                             # Funcionalidades públicas
│   ├── LandingService.php
│   └── ContactoService.php
│
└── 📁 Infrastructure/                     # Servicios de infraestructura
    ├── FileService.php
    ├── CacheService.php
    ├── RedisCacheService.php
    ├── LoggingService.php
    ├── ValidationService.php
    └── ConfigurationService.php
```

## 📋 Mapeo de Servicios Actuales → Nueva Estructura

### Catalog (Catálogo)
- `ProductoService.php` → `Catalog/ProductoService.php`
- `CategoriaService.php` → `Catalog/CategoriaService.php`

### Inventory (Inventario)
- `InventarioService.php` → `Inventory/InventarioService.php`
- `OptimizedStockAlertService.php` → `Inventory/StockAlertService.php` (renombrado)
- `StockSincronizacionService.php` → `Inventory/StockSincronizacionService.php`
- `ReservaStockService.php` → `Inventory/ReservaStockService.php`

### Orders (Pedidos)
- `Business/PedidoService.php` → `Orders/PedidoService.php`
- `Business/CheckoutService.php` → `Orders/CheckoutService.php`
- `Business/CarritoService.php` → `Orders/CarritoService.php`

### Payment (Pagos)
- `StripeService.php` → `Payment/StripeService.php`

### Auth (Autenticación)
- `AuthService.php` → `Auth/AuthService.php`
- `JwtService.php` → `Auth/JwtService.php`
- `OtpService.php` → `Auth/OtpService.php`
- `AuthorizationService.php` → `Auth/AuthorizationService.php`

### User (Usuarios)
- `UsuarioService.php` → `User/UsuarioService.php`

### Notification (Notificaciones)
- `NotificationService.php` → `Notification/NotificationService.php`
- `AdminNotificationService.php` → `Notification/AdminNotificationService.php`
- `PedidoNotificationService.php` → `Notification/PedidoNotificationService.php`

### Dashboard
- `DashboardService.php` → `Dashboard/DashboardService.php`
- `DashboardMetricsService.php` → `Dashboard/DashboardMetricsService.php`

### Public (Público)
- `LandingService.php` → `Public/LandingService.php`
- `ContactoService.php` → `Public/ContactoService.php`

### Infrastructure (Infraestructura)
- `FileService.php` → `Infrastructure/FileService.php`
- `CacheService.php` → `Infrastructure/CacheService.php`
- `RedisCacheService.php` → `Infrastructure/RedisCacheService.php`
- `LoggingService.php` → `Infrastructure/LoggingService.php`
- `ValidationService.php` → `Infrastructure/ValidationService.php`
- `ConfigurationService.php` → `Infrastructure/ConfigurationService.php`

## ✅ Ventajas de esta Estructura

1. **Organización clara**: Cada servicio está en su dominio correspondiente
2. **Fácil navegación**: Encontrar servicios relacionados es más simple
3. **Escalabilidad**: Fácil agregar nuevos servicios en el dominio correcto
4. **Mantenibilidad**: Cambios en un dominio no afectan otros
5. **Nomenclatura consistente**: Nombres descriptivos y organizados

## 🔄 Cambios Necesarios

1. Crear las carpetas de dominio
2. Mover archivos a sus nuevas ubicaciones
3. Actualizar namespaces en todos los servicios
4. Actualizar imports en controladores, comandos y otros servicios
5. Renombrar `OptimizedStockAlertService` a `StockAlertService` (opcional pero recomendado)

