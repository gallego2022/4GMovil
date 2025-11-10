# Guía para Verificar Dependencias en Docker

## Comandos para Verificar Dependencias

### 1. Verificar Dependencias de Composer (PHP)

#### Dentro del contenedor Docker:
```bash
# Entrar al contenedor
docker-compose exec app bash

# Ver todas las dependencias instaladas
composer show --installed

# Verificar un paquete específico (ej: DomPDF)
composer show dompdf/dompdf

# Verificar si falta alguna dependencia
composer validate

# Verificar autoload
composer dump-autoload
```

#### Desde fuera del contenedor:
```bash
# Verificar dependencias
docker-compose exec app composer show --installed

# Verificar un paquete específico
docker-compose exec app composer show dompdf/dompdf

# Validar composer.json
docker-compose exec app composer validate
```

### 2. Verificar Dependencias de npm (Node.js)

#### Dentro del contenedor Docker:
```bash
# Entrar al contenedor
docker-compose exec app bash

# Ver todas las dependencias instaladas
npm list

# Verificar un paquete específico (ej: SweetAlert2)
npm list sweetalert2

# Verificar si falta alguna dependencia
npm audit
```

#### Desde fuera del contenedor:
```bash
# Verificar dependencias
docker-compose exec app npm list

# Verificar un paquete específico
docker-compose exec app npm list sweetalert2
```

### 3. Instalar Dependencias Faltantes

#### Si falta DomPDF:
```bash
# Dentro del contenedor
docker-compose exec app composer require dompdf/dompdf

# O reconstruir la imagen
docker-compose build --no-cache app
docker-compose up -d
```

#### Si faltan dependencias de npm:
```bash
# Dentro del contenedor
docker-compose exec app npm install

# O reconstruir la imagen
docker-compose build --no-cache app
docker-compose up -d
```

### 4. Verificar Estado del Contenedor

```bash
# Ver estado de los contenedores
docker-compose ps

# Ver logs del contenedor
docker-compose logs app

# Verificar que el contenedor esté corriendo
docker-compose exec app php -v
docker-compose exec app composer --version
docker-compose exec app npm --version
```

### 5. Script de Verificación Automática

#### Ejecutar el script dentro del contenedor:
```bash
# Copiar el script al contenedor (si es necesario)
docker-compose exec app bash verificar-dependencias.sh

# O ejecutar comandos directamente
docker-compose exec app bash -c "composer show --installed && npm list"
```

## Verificación Rápida

### Comando único para verificar todo:
```bash
docker-compose exec app bash -c "
echo '=== Composer Dependencies ===' && \
composer show --installed | grep -E '(dompdf|laravel)' && \
echo '' && \
echo '=== npm Dependencies ===' && \
npm list --depth=0 2>/dev/null | grep -E '(sweetalert|alpine)' && \
echo '' && \
echo '=== Verificación completada ==='
"
```

## Solución de Problemas

### Si DomPDF no está instalado:
1. Entrar al contenedor: `docker-compose exec app bash`
2. Instalar: `composer require dompdf/dompdf`
3. Salir: `exit`

### Si npm no tiene dependencias:
1. Entrar al contenedor: `docker-compose exec app bash`
2. Instalar: `npm install`
3. Compilar: `npm run build`
4. Salir: `exit`

### Si necesitas reconstruir todo:
```bash
# Reconstruir la imagen (instalará todas las dependencias)
docker-compose build --no-cache app

# Reiniciar el contenedor
docker-compose up -d app
```

