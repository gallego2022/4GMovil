# 🐳 Guía de Despliegue Docker desde GitHub

## 📋 Resumen

Esta guía te explica cómo clonar e instalar el proyecto 4GMovil desde GitHub en cualquier PC usando Docker, considerando el cambio de `127.0.0.1` a `localhost`.

## 🚀 Instalación Rápida

### 1. **Clonar el Repositorio**
```bash
git clone https://github.com/tu-usuario/4gmovil.git
cd 4gmovil
```

### 2. **Configurar Variables de Entorno**
```bash
# Copiar archivo de configuración Docker
cp env.docker.example .env
```

### 3. **Desplegar con Docker**
```bash
# Construir y levantar contenedores
docker-compose up --build -d
```

### 4. **Verificar Instalación**
- **Aplicación**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **Base de datos**: localhost:3306

## 🔧 Configuración Detallada

### **Archivo `.env` para Docker**

El archivo `env.docker.example` ya está configurado para Docker:

```env
APP_NAME="4GMovil"
APP_ENV=production
APP_KEY=base64:gRO33MAV0Lza0BC8blZlvMHUzg8zMAoiO/kCmRyi+64=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

# Base de datos (Docker)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=laraveluser
DB_PASSWORD=laravelpass

# Google OAuth (actualizar con tus credenciales)
GOOGLE_CLIENT_ID=tu-google-client-id
GOOGLE_CLIENT_SECRET=tu-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/callback/google

# Stripe (actualizar con tus credenciales)
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### **Diferencias Clave: `127.0.0.1` vs `localhost`**

| Aspecto | Desarrollo Local | Docker |
|---------|------------------|--------|
| **URL de la app** | `http://127.0.0.1:8000` | `http://localhost:8000` |
| **Host de BD** | `127.0.0.1` | `db` (nombre del contenedor) |
| **Puerto BD** | `3306` | `3306` (mapeado) |
| **Google OAuth** | `http://127.0.0.1:8000/...` | `http://localhost:8000/...` |

## 📝 Pasos Detallados

### **Paso 1: Preparar el Sistema**

#### **Requisitos:**
- Docker Desktop instalado
- Git instalado
- Puerto 8000 y 3306 disponibles

#### **Verificar Docker:**
```bash
docker --version
docker-compose --version
```

### **Paso 2: Clonar y Configurar**

```bash
# Clonar repositorio
git clone https://github.com/tu-usuario/4gmovil.git
cd 4gmovil

# Copiar configuración Docker
cp env.docker.example .env

# Editar configuración (opcional)
# nano .env
```

### **Paso 3: Configurar Servicios Externos**

#### **Google OAuth:**
1. Ir a [Google Cloud Console](https://console.cloud.google.com/)
2. Crear/editar proyecto
3. Habilitar Google+ API
4. Crear credenciales OAuth 2.0
5. Agregar URI de redirección: `http://localhost:8000/auth/callback/google`
6. Copiar `Client ID` y `Client Secret` al archivo `.env`

#### **Stripe:**
1. Ir a [Stripe Dashboard](https://dashboard.stripe.com/)
2. Obtener claves de API (pública y secreta)
3. Configurar webhook: `http://localhost:8000/stripe/webhook`
4. Copiar credenciales al archivo `.env`

### **Paso 4: Desplegar con Docker**

```bash
# Construir y levantar contenedores
docker-compose up --build -d

# Ver logs (opcional)
docker-compose logs -f

# Verificar estado de contenedores
docker-compose ps
```

### **Paso 5: Verificar Instalación**

#### **Verificar Contenedores:**
```bash
# Estado de contenedores
docker-compose ps

# Debería mostrar:
# 4gmovil_app        Up      0.0.0.0:8000->80/tcp
# 4gmovil_db         Up      0.0.0.0:3306->3306/tcp
# 4gmovil_phpmyadmin Up      0.0.0.0:8080->80/tcp
# 4gmovil_redis      Up      0.0.0.0:6379->6379/tcp
```

#### **Verificar Aplicación:**
- **Aplicación**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **phpMyAdmin**: http://localhost:8080

#### **Credenciales por Defecto:**
- **Admin**: `4gmoviltest@gmail.com` / `Admin123!`
- **Base de datos**: `laraveluser` / `laravelpass`

## 🔄 Comandos de Gestión

### **Gestión de Contenedores:**
```bash
# Iniciar contenedores
docker-compose up -d

# Detener contenedores
docker-compose down

# Reiniciar contenedores
docker-compose restart

# Ver logs
docker-compose logs -f app

# Acceder al contenedor de la app
docker exec -it 4gmovil_app bash
```

### **Comandos Laravel en Docker:**
```bash
# Ejecutar migraciones
docker exec 4gmovil_app php artisan migrate

# Limpiar caché
docker exec 4gmovil_app php artisan cache:clear

# Generar clave de aplicación
docker exec 4gmovil_app php artisan key:generate

# Ejecutar seeders
docker exec 4gmovil_app php artisan db:seed
```

## 🛠️ Solución de Problemas

### **Problema: Puerto ya en uso**
```bash
# Verificar qué está usando el puerto
netstat -ano | findstr :8000

# Cambiar puerto en docker-compose.yml
ports:
  - "8001:80"  # Cambiar 8000 por 8001
```

### **Problema: Contenedor no inicia**
```bash
# Ver logs detallados
docker-compose logs app

# Reconstruir contenedor
docker-compose down
docker-compose up --build -d
```

### **Problema: Base de datos no conecta**
```bash
# Verificar que el contenedor de BD esté corriendo
docker-compose ps

# Ver logs de la base de datos
docker-compose logs db

# Reiniciar solo la base de datos
docker-compose restart db
```

### **Problema: Imágenes no cargan**
```bash
# Verificar enlace simbólico
docker exec 4gmovil_app ls -la /var/www/html/public/storage

# Recrear enlace simbólico
docker exec 4gmovil_app rm /var/www/html/public/storage
docker exec 4gmovil_app ln -s /var/www/html/storage/app/public /var/www/html/public/storage
```

## 📊 Monitoreo

### **Verificar Estado:**
```bash
# Estado de contenedores
docker-compose ps

# Uso de recursos
docker stats

# Logs en tiempo real
docker-compose logs -f
```

### **Backup de Base de Datos:**
```bash
# Crear backup
docker exec 4gmovil_db mysqldump -u laraveluser -plaravelpass 4gmovil > backup.sql

# Restaurar backup
docker exec -i 4gmovil_db mysql -u laraveluser -plaravelpass 4gmovil < backup.sql
```

## 🔄 Actualizaciones

### **Actualizar desde GitHub:**
```bash
# Obtener últimos cambios
git pull origin main

# Reconstruir contenedores
docker-compose down
docker-compose up --build -d

# Ejecutar migraciones (si las hay)
docker exec 4gmovil_app php artisan migrate
```

### **Actualizar Dependencias:**
```bash
# Actualizar dependencias PHP
docker exec 4gmovil_app composer update

# Actualizar dependencias Node.js
docker exec 4gmovil_app npm update

# Reconstruir assets
docker exec 4gmovil_app npm run build
```

## 🌐 Configuración para Producción

### **Variables de Entorno de Producción:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de datos de producción
DB_HOST=tu-servidor-bd
DB_DATABASE=4gmovil_prod
DB_USERNAME=usuario_prod
DB_PASSWORD=contraseña_segura

# Google OAuth para producción
GOOGLE_REDIRECT_URI=https://tu-dominio.com/auth/callback/google

# Stripe para producción
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
```

### **Optimizaciones de Producción:**
```bash
# Optimizar autoloader
docker exec 4gmovil_app composer install --optimize-autoloader --no-dev

# Cachear configuración
docker exec 4gmovil_app php artisan config:cache
docker exec 4gmovil_app php artisan route:cache
docker exec 4gmovil_app php artisan view:cache
```

## 📝 Notas Importantes

1. **URLs**: El proyecto usa `localhost:8000` en lugar de `127.0.0.1:8000`
2. **Base de datos**: El host es `db` (nombre del contenedor), no `localhost`
3. **Volúmenes**: Los datos persisten en volúmenes Docker
4. **Puertos**: 8000 (app), 3306 (BD), 8080 (phpMyAdmin), 6379 (Redis)
5. **Imágenes**: Se almacenan en volúmenes Docker y se sirven via enlace simbólico

## 🆘 Soporte

Si tienes problemas:
1. Verifica que Docker Desktop esté corriendo
2. Revisa los logs: `docker-compose logs -f`
3. Verifica que los puertos estén disponibles
4. Asegúrate de que las credenciales de servicios externos sean correctas

---

**¡Tu proyecto 4GMovil estará funcionando en minutos con Docker! 🚀**
