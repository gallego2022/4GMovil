# 🚀 4GMovil - Plataforma E-commerce Full Stack

![Laravel](https://img.shields.io/badge/Laravel-12.0-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.4.17-38B2AC.svg)
![Vite](https://img.shields.io/badge/Vite-7.1.5-646CFF.svg)
![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen)

**4GMovil** es una plataforma **E-commerce moderna** construida con **Laravel 12, Tailwind CSS y Vite**, diseñada para ofrecer una experiencia de compra completa, segura y lista para producción.

### ✨ Características clave
- 🔐 Autenticación con email/contraseña y **Google OAuth**  
- 💳 Pasarela de pagos **Stripe** (incluye suscripciones y webhooks)  
- 📊 Gestión avanzada de inventario con reportes y alertas automáticas  
- 🎨 Interfaz responsive + **modo oscuro persistente**  
- 🔎 Búsqueda en tiempo real con autocompletado  

📌 Proyecto académico desarrollado **de forma individual** como parte de mi formación en **Tecnología en Análisis y Desarrollo de Software (SENA)**.  

---

## 🎥 Demo Rápida

![Landing Dark](docs/capturas/landing-dark.gif)
![Carrito](docs/capturas/carrito.gif)
![Admin Dashboard](docs/capturas/admin-dashboard.gif)

👉 Mira más en la sección [Capturas de Pantalla](#-capturas-de-pantalla).

---

## 📋 Índice

- [Estado Actual](#-estado-actual-del-proyecto)  
- [Características](#-características-principales)  
- [Mi Rol y Aportes](#-mi-rol-y-aportes)  
- [Stack Tecnológico](#-stack-tecnológico)  
- [Instalación](#-instalación)  
- [Capturas de Pantalla](#-capturas-de-pantalla)  
- [Testing](#-testing)  
- [Despliegue](#-despliegue)  
- [Contribución](#-contribución)  
- [Documentación Adicional](#-documentación-adicional)  

---

## 🎉 Estado Actual del Proyecto

✅ Completamente funcional  
✅ Base de datos y seeders corregidos  
✅ Modo oscuro implementado  
✅ Sistema de especificaciones dinámicas por categoría  
✅ Optimizado para producción  

📅 **Última actualización**: Septiembre 2025  

---

## 🛍️ Características Principales

- Catálogo con categorías y marcas  
- Carrito y checkout optimizados  
- Autenticación tradicional y con Google  
- Integración de **Stripe** (pagos y suscripciones)  
- Gestión avanzada de inventario con alertas y reportes  
- Interfaz responsive con modo oscuro persistente  
- Búsqueda en tiempo real con autocompletado  
- Panel admin con métricas y exportes  

---

## 👨‍💻 Mi Rol y Aportes

Este proyecto fue desarrollado **por mí** como trabajo académico y personal. Mis aportes principales:

- Diseño y desarrollo completo del backend en **Laravel 12** (Repository Pattern + Services).  
- Implementación de **Google OAuth** y verificación de email.  
- Integración de **Stripe** con soporte de webhooks y suscripciones.  
- Creación del sistema de **especificaciones dinámicas de productos** y búsqueda avanzada.  
- Construcción de la interfaz con **Tailwind CSS**, responsive y con modo oscuro persistente.  
- Automatización de despliegue con **Docker** y documentación técnica detallada.  

---

## 🛠️ Stack Tecnológico

**Backend**: Laravel 12, PHP 8.2+, MySQL/SQLite  
**Frontend**: Vite, Tailwind CSS, Alpine.js, Axios  
**Integraciones**: Stripe, Google OAuth, SMTP (Gmail), Postmark/AWS SES  
**Herramientas Dev**: PHPUnit, Laravel Pint, ESLint, Prettier, Docker, Git  

---

## ⚙️ Instalación

### Opción 1: Docker (recomendada)

```bash
git clone https://github.com/tu-usuario/4gmovil.git
cd 4gmovil
cp env.docker.example .env
docker-compose up --build -d
```

- App: http://localhost:8000  
- Admin: http://localhost:8000/admin  

### Opción 2: Instalación tradicional

```bash
git clone https://github.com/tu-usuario/4gmovil.git
cd 4gmovil
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

- App: http://127.0.0.1:8000  
- Admin: http://127.0.0.1:8000/admin  

👤 **Credenciales admin por defecto**  
Email: `4gmoviltest@gmail.com`  
Password: `Admin123!`  

---

## 📸 Capturas de Pantalla

- Landing (modo claro/oscuro)  
- Autocompletado en búsqueda  
- Checkout y carrito  
- Dashboard admin  
- Páginas de error personalizadas  

*(Ver gifs en carpeta `docs/capturas/`)*

---

## 🧪 Testing

```bash
# Tests unitarios
php artisan test --testsuite=Unit

# Tests de integración
php artisan test --testsuite=Feature
```

---

## 🚀 Despliegue en Producción

```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

Incluye configuración para **Docker** y servidores tradicionales (Apache/Nginx).  

---

## 🤝 Contribución

1. Fork del proyecto  
2. Crear rama (`git checkout -b feature/NuevaFeature`)  
3. Commit (`git commit -m 'Add NuevaFeature'`)  
4. Push (`git push origin feature/NuevaFeature`)  
5. Pull request  

---

## 📚 Documentación Adicional

- [Guía de Optimización de Rendimiento](PERFORMANCE_OPTIMIZATION.md)  
- [Configuración de Google OAuth](GOOGLE_OAUTH_SETUP.md)  
- [Webhooks de Stripe](STRIPE_WEBHOOK_SETUP.md)  
- [Guía de Despliegue con Docker](DOCKER_DEPLOYMENT_GUIDE.md)  

---

## 📄 Licencia

Este proyecto está bajo Licencia MIT.  
