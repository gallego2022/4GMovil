# 👥 Guía de Sincronización para Equipos Mixtos

## 🔄 **Problema: Equipo con Docker y sin Docker**

### **Escenarios Comunes:**
- **Desarrollador A**: Usa Docker
- **Desarrollador B**: No tiene Docker (instalación tradicional)
- **Cambios**: Se hacen en ambos entornos
- **Problema**: ¿Cómo sincronizar y mantener compatibilidad?

## 🛠️ **Soluciones Implementadas**

### **1. Archivos de Configuración Separados**

```
4GMovil/
├── .env.example              # Configuración base
├── env.docker.example        # Para Docker
├── env.github.example        # Para instalación tradicional
├── install-docker.bat        # Script Docker (Windows)
├── install-docker.sh         # Script Docker (Linux/Mac)
├── install-traditional.bat   # Script tradicional (Windows)
└── install-traditional.sh    # Script tradicional (Linux/Mac)
```

### **2. Scripts de Instalación Automática**

#### **Para Docker:**
```bash
# Windows
install-docker.bat

# Linux/Mac
chmod +x install-docker.sh
./install-docker.sh
```

#### **Para Instalación Tradicional:**
```bash
# Windows
install-traditional.bat

# Linux/Mac
chmod +x install-traditional.sh
./install-traditional.sh
```

## 🔄 **Flujo de Trabajo para Equipos Mixtos**

### **1. Configuración Inicial del Proyecto**

#### **Desarrollador con Docker:**
```bash
git clone https://github.com/tu-usuario/4gmovil.git
cd 4gmovil
cp env.docker.example .env
docker-compose up --build -d
```

#### **Desarrollador sin Docker:**
```bash
git clone https://github.com/tu-usuario/4gmovil.git
cd 4gmovil
cp .env.example .env
# Editar .env con configuración local
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

### **2. Sincronización de Cambios**

#### **Cuando alguien hace cambios:**

1. **Commit y Push:**
```bash
git add .
git commit -m "Descripción del cambio"
git push origin main
```

2. **Pull de cambios:**
```bash
git pull origin main
```

3. **Aplicar cambios según el entorno:**

**Con Docker:**
```bash
# Si hay cambios en dependencias
docker-compose down
docker-compose up --build -d

# Si hay cambios en base de datos
docker exec 4gmovil_app php artisan migrate

# Si hay cambios en assets
docker exec 4gmovil_app npm run build
```

**Sin Docker:**
```bash
# Si hay cambios en dependencias
composer install
npm install

# Si hay cambios en base de datos
php artisan migrate

# Si hay cambios en assets
npm run build
```

## 📝 **Diferencias Clave a Considerar**

### **URLs y Configuración:**

| Aspecto | Docker | Sin Docker |
|---------|--------|------------|
| **URL de la app** | `http://localhost:8000` | `http://127.0.0.1:8000` |
| **Host de BD** | `db` | `127.0.0.1` |
| **Google OAuth** | `http://localhost:8000/...` | `http://127.0.0.1:8000/...` |
| **Archivo .env** | `env.docker.example` | `.env.example` |

### **Comandos de Desarrollo:**

| Acción | Docker | Sin Docker |
|--------|--------|------------|
| **Ejecutar migraciones** | `docker exec 4gmovil_app php artisan migrate` | `php artisan migrate` |
| **Limpiar caché** | `docker exec 4gmovil_app php artisan cache:clear` | `php artisan cache:clear` |
| **Construir assets** | `docker exec 4gmovil_app npm run build` | `npm run build` |
| **Ver logs** | `docker-compose logs -f app` | `tail -f storage/logs/laravel.log` |

## 🔧 **Herramientas de Sincronización**

### **1. Script de Sincronización Automática**

Crear `sync-changes.bat` (Windows):
```batch
@echo off
echo Sincronizando cambios...

REM Pull de cambios
git pull origin main

REM Verificar si está en Docker
if exist "docker-compose.yml" (
    echo Aplicando cambios en Docker...
    docker-compose down
    docker-compose up --build -d
    docker exec 4gmovil_app php artisan migrate
    docker exec 4gmovil_app npm run build
) else (
    echo Aplicando cambios en instalación tradicional...
    composer install
    npm install
    php artisan migrate
    npm run build
)

echo Sincronización completada!
pause
```

### **2. Script de Sincronización para Linux/Mac**

Crear `sync-changes.sh`:
```bash
#!/bin/bash
echo "Sincronizando cambios..."

# Pull de cambios
git pull origin main

# Verificar si está en Docker
if [ -f "docker-compose.yml" ]; then
    echo "Aplicando cambios en Docker..."
    docker-compose down
    docker-compose up --build -d
    docker exec 4gmovil_app php artisan migrate
    docker exec 4gmovil_app npm run build
else
    echo "Aplicando cambios en instalación tradicional..."
    composer install
    npm install
    php artisan migrate
    npm run build
fi

echo "Sincronización completada!"
```

## 📋 **Checklist de Sincronización**

### **Antes de hacer cambios:**
- [ ] Pull de cambios recientes
- [ ] Verificar que el entorno esté funcionando
- [ ] Hacer backup de la base de datos (si es necesario)

### **Después de hacer cambios:**
- [ ] Probar cambios en el entorno local
- [ ] Commit y push de cambios
- [ ] Notificar al equipo sobre los cambios
- [ ] Actualizar documentación si es necesario

### **Al recibir cambios:**
- [ ] Pull de cambios
- [ ] Aplicar cambios según el entorno
- [ ] Probar que todo funcione
- [ ] Reportar problemas si los hay

## 🚨 **Problemas Comunes y Soluciones**

### **Problema: "Conflicto en .env"**
```bash
# Solución: Usar archivos específicos
git checkout -- .env
cp env.docker.example .env  # Para Docker
# o
cp .env.example .env        # Para instalación tradicional
```

### **Problema: "Dependencias desactualizadas"**
```bash
# Docker
docker-compose down
docker-compose up --build -d

# Sin Docker
composer install
npm install
```

### **Problema: "Base de datos desactualizada"**
```bash
# Docker
docker exec 4gmovil_app php artisan migrate

# Sin Docker
php artisan migrate
```

## 📚 **Mejores Prácticas**

### **1. Comunicación:**
- Usar issues de GitHub para reportar problemas
- Documentar cambios importantes
- Notificar al equipo sobre cambios críticos

### **2. Versionado:**
- Hacer commits pequeños y descriptivos
- Usar branches para features grandes
- Hacer pull requests para revisión

### **3. Testing:**
- Probar cambios en ambos entornos
- Verificar que las migraciones funcionen
- Asegurar que los assets se construyan correctamente

### **4. Documentación:**
- Mantener actualizada la documentación
- Documentar cambios en la configuración
- Crear guías para nuevos desarrolladores

## 🎯 **Recomendaciones**

### **Para Equipos Pequeños (2-3 personas):**
- Usar Docker para consistencia
- Documentar el proceso de instalación
- Usar scripts de sincronización

### **Para Equipos Grandes (4+ personas):**
- Establecer estándares claros
- Usar CI/CD para automatización
- Implementar code review obligatorio

### **Para Proyectos en Producción:**
- Usar Docker en desarrollo
- Instalación tradicional en producción
- Automatizar el despliegue

---

**¡Con estas herramientas, tu equipo puede trabajar eficientemente sin importar si usan Docker o no! 🚀**
