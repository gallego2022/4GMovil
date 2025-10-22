# 📁 Estructura del Proyecto 4GMovil - Actualizada

## 🎯 Resumen del Proyecto

**4GMovil** es una plataforma e-commerce completa desarrollada en Laravel 12 con PHP 8.2+, que incluye sistema de autenticación, gestión de productos, checkout con Stripe, y administración completa.

## 📊 Información General

- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Base de Datos**: MySQL/PostgreSQL
- **Frontend**: Tailwind CSS + Alpine.js
- **Build Tool**: Vite
- **Estado**: ✅ Producción Ready

## 🏗️ Estructura Principal

```
4GMovil/
├── 📁 app/                          # Código de la aplicación
│   ├── 📁 Console/Commands/         # Comandos Artisan personalizados
│   ├── 📁 Http/Controllers/         # Controladores MVC
│   │   ├── 📁 Admin/               # Controladores del panel admin
│   │   ├── 📁 Cliente/             # Controladores del cliente
│   │   └── 📁 Auth/                # Controladores de autenticación
│   ├── 📁 Models/                   # Modelos Eloquent
│   ├── 📁 Services/                 # Lógica de negocio
│   │   ├── 📁 Business/            # Servicios de negocio
│   │   └── 📁 Base/                # Servicios base
│   ├── 📁 Mail/                     # Clases de correo
│   ├── 📁 Jobs/                     # Jobs de cola
│   └── 📁 Helpers/                  # Funciones auxiliares
├── 📁 resources/                    # Recursos frontend
│   ├── 📁 views/                    # Vistas Blade
│   │   ├── 📁 layouts/             # Layouts principales
│   │   ├── 📁 pages/               # Páginas específicas
│   │   ├── 📁 modules/             # Módulos reutilizables
│   │   └── 📁 components/          # Componentes Blade
│   ├── 📁 js/                       # JavaScript
│   └── 📁 css/                      # Estilos CSS
├── 📁 routes/                       # Definición de rutas
│   ├── web.php                      # Rutas web
│   ├── admin.php                    # Rutas del admin
│   ├── cliente.php                  # Rutas del cliente
│   └── api.php                      # Rutas API
├── 📁 database/                     # Base de datos
│   ├── 📁 migrations/              # Migraciones
│   ├── 📁 seeders/                 # Seeders
│   └── 📁 factories/               # Factories
├── 📁 public/                       # Archivos públicos
│   ├── 📁 build/                    # Assets compilados
│   ├── 📁 css/                      # CSS compilado
│   ├── 📁 js/                       # JavaScript compilado
│   └── 📁 img/                      # Imágenes
├── 📁 storage/                       # Almacenamiento
│   ├── 📁 app/                      # Archivos de la app
│   ├── 📁 logs/                     # Logs del sistema
│   └── 📁 framework/                # Cache del framework
├── 📁 tests/                        # Pruebas
│   ├── 📁 Feature/                  # Pruebas de integración
│   └── 📁 Unit/                     # Pruebas unitarias
├── 📁 Mds/                          # Documentación
└── 📁 vendor/                        # Dependencias Composer
```

## 🔧 Archivos de Configuración

### **Composer (PHP)**
```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "laravel/cashier": "^15.7",
        "laravel/socialite": "^5.23",
        "intervention/image": "2.7",
        "mcamara/laravel-localization": "^2.3"
    }
}
```

### **NPM (JavaScript)**
```json
{
    "dependencies": {
        "alpinejs": "^3.15.0",
        "chart.js": "^4.4.0",
        "sweetalert2": "^11.23.0"
    },
    "devDependencies": {
        "tailwindcss": "^3.4.17",
        "vite": "^7.1.5",
        "axios": "^1.12.2"
    }
}
```

## 🎯 Módulos Principales

### 1. **Sistema de Autenticación**
- **Archivos clave**: `app/Http/Controllers/Auth/`, `app/Services/AuthService.php`
- **Funcionalidades**: Login, registro, Google OAuth, OTP, recuperación de contraseña
- **Rutas**: `/login`, `/register`, `/auth/google`

### 2. **Gestión de Productos**
- **Archivos clave**: `app/Http/Controllers/Admin/ProductosController.php`
- **Funcionalidades**: CRUD productos, variantes, especificaciones, imágenes
- **Rutas**: `/admin/productos/*`

### 3. **Sistema de Checkout**
- **Archivos clave**: `app/Services/Business/CheckoutService.php`
- **Funcionalidades**: Carrito, checkout, integración Stripe, gestión de pedidos
- **Rutas**: `/checkout/*`, `/pedidos/*`

### 4. **Panel de Administración**
- **Archivos clave**: `app/Http/Controllers/Admin/`
- **Funcionalidades**: Dashboard, gestión de usuarios, inventario, reportes
- **Rutas**: `/admin/*`

### 5. **Sistema de Notificaciones**
- **Archivos clave**: `app/Mail/`, `app/Jobs/`
- **Funcionalidades**: Correos automáticos, notificaciones admin, alertas stock
- **Integración**: Queue system, email templates

## 🗄️ Base de Datos

### **Tablas Principales**
- `usuarios` - Usuarios del sistema
- `productos` - Catálogo de productos
- `variantes_producto` - Variantes de productos
- `pedidos` - Órdenes de compra
- `detalles_pedido` - Detalles de pedidos
- `pagos` - Transacciones de pago
- `direcciones` - Direcciones de usuarios

### **Relaciones Clave**
- Usuario → Pedidos (1:N)
- Producto → Variantes (1:N)
- Pedido → Detalles (1:N)
- Pedido → Pago (1:1)

## 🚀 Comandos Útiles

### **Desarrollo**
```bash
# Instalar dependencias
composer install
npm install

# Ejecutar migraciones
php artisan migrate

# Poblar base de datos
php artisan db:seed

# Compilar assets
npm run dev
npm run build
```

### **Producción**
```bash
# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build:prod
```

## 📋 Estado Actual

### ✅ **Completado**
- Sistema de autenticación completo
- Gestión de productos con variantes
- Checkout integrado con Stripe
- Panel de administración funcional
- Sistema de notificaciones
- Optimizaciones de rendimiento

### 🔄 **En Desarrollo**
- Mejoras en UX/UI
- Optimizaciones adicionales
- Nuevas funcionalidades

## 📚 Documentación Relacionada

- [Sistema de Checkout y Pedidos](SISTEMA_CHECKOUT_PEDIDOS_ACTUALIZADO.md)
- [Sistema de Variantes](SISTEMA_VARIANTES_COMPLETO.md)
- [Sistema de Alertas](SISTEMA_ALERTAS_COMPLETO.md)
- [Configuración de Stripe](STRIPE_WEBHOOK_SETUP.md)
- [Optimización de Rendimiento](PERFORMANCE_OPTIMIZATION_README.md)

---

*Documentación actualizada: $(date)*
*Versión del proyecto: 2.0*
*Estado: Producción Ready*
