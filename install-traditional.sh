#!/bin/bash

echo "========================================"
echo "  4GMovil - Instalación Tradicional"
echo "========================================"
echo

# Verificar si PHP está instalado
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP no está instalado."
    echo "Por favor instala PHP 8.2+ desde: https://www.php.net/downloads"
    exit 1
fi

echo "PHP encontrado. Continuando..."
echo

# Verificar si Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "ERROR: Composer no está instalado."
    echo "Por favor instala Composer desde: https://getcomposer.org/"
    exit 1
fi

echo "Composer encontrado. Continuando..."
echo

# Verificar si Node.js está instalado
if ! command -v node &> /dev/null; then
    echo "ERROR: Node.js no está instalado."
    echo "Por favor instala Node.js desde: https://nodejs.org/"
    exit 1
fi

echo "Node.js encontrado. Continuando..."
echo

# Verificar si Git está instalado
if ! command -v git &> /dev/null; then
    echo "ERROR: Git no está instalado."
    echo "Por favor instala Git desde: https://git-scm.com/downloads"
    exit 1
fi

echo "Git encontrado. Continuando..."
echo

# Verificar si el proyecto ya existe
if [ -d "4gmovil" ]; then
    echo "El directorio 4gmovil ya existe."
    read -p "¿Deseas continuar y actualizar el proyecto? (s/n): " choice
    if [[ ! $choice =~ ^[Ss]$ ]]; then
        echo "Instalación cancelada."
        exit 0
    fi
    echo "Actualizando proyecto..."
    cd 4gmovil
    git pull origin main
else
    echo "Clonando proyecto desde GitHub..."
    git clone https://github.com/tu-usuario/4gmovil.git
    cd 4gmovil
fi

echo
echo "Instalando dependencias PHP..."
composer install

echo
echo "Instalando dependencias JavaScript..."
npm install

echo
echo "Configurando variables de entorno..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "Archivo .env creado desde .env.example"
else
    echo "El archivo .env ya existe."
fi

echo
echo "Configurando base de datos..."
echo "IMPORTANTE: Asegúrate de que MySQL esté corriendo"
echo

# Verificar conexión a base de datos
php artisan tinker --execute="DB::connection()->getPdo();" >/dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "ERROR: No se puede conectar a la base de datos."
    echo "Por favor:"
    echo "1. Asegúrate de que MySQL esté corriendo"
    echo "2. Configura las credenciales en el archivo .env"
    echo "3. Crea la base de datos: CREATE DATABASE 4gmovil_db;"
    exit 1
fi

echo "Base de datos conectada correctamente."
echo

echo "Generando clave de aplicación..."
php artisan key:generate

echo
echo "Creando enlace simbólico para storage..."
php artisan storage:link

echo
echo "Ejecutando migraciones y seeders..."
php artisan migrate:fresh --seed

echo
echo "Construyendo assets..."
npm run build

echo
echo "========================================"
echo "  ¡Instalación completada!"
echo "========================================"
echo
echo "URLs de acceso:"
echo "- Aplicación: http://127.0.0.1:8000"
echo "- Admin Panel: http://127.0.0.1:8000/admin"
echo
echo "Credenciales por defecto:"
echo "- Admin: 4gmoviltest@gmail.com / Admin123!"
echo
echo "Para iniciar el servidor:"
echo "php artisan serve"
echo
echo "IMPORTANTE: Configura tus credenciales de Google OAuth y Stripe en el archivo .env"
echo
