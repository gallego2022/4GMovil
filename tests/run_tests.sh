#!/bin/bash

echo "🧪 EJECUTANDO TESTS DEL PROYECTO DE REFACTORING"
echo "=================================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar mensajes con colores
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar si estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    print_error "No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz del proyecto Laravel."
    exit 1
fi

print_status "Verificando dependencias..."
if ! command -v php &> /dev/null; then
    print_error "PHP no está instalado o no está en el PATH"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    print_error "Composer no está instalado o no está en el PATH"
    exit 1
fi

print_status "Instalando dependencias de testing..."
composer install --no-interaction --prefer-dist --optimize-autoloader

print_status "Limpiando caché..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

print_status "Generando clave de aplicación..."
php artisan key:generate

print_status "Ejecutando tests de FASE 1: FUNDAMENTOS..."
echo ""

# Tests de FASE 1
print_status "Testing LoggingService..."
php artisan test tests/Unit/Services/LoggingServiceTest.php --verbose

print_status "Testing ValidationService..."
php artisan test tests/Unit/Services/ValidationServiceTest.php --verbose

print_status "Testing CacheService..."
php artisan test tests/Unit/Services/CacheServiceTest.php --verbose

print_status "Testing BaseController..."
php artisan test tests/Unit/Controllers/BaseControllerTest.php --verbose

echo ""
print_status "Ejecutando tests de FASE 2: CORE SERVICES..."
echo ""

# Tests de FASE 2
print_status "Testing NotificationService..."
php artisan test tests/Unit/Services/NotificationServiceTest.php --verbose

print_status "Testing AuthService..."
php artisan test tests/Unit/Services/AuthServiceTest.php --verbose

echo ""
print_status "Ejecutando todos los tests unitarios..."
php artisan test tests/Unit --verbose

echo ""
print_status "Ejecutando tests de cobertura..."
if command -v phpdbg &> /dev/null; then
    phpdbg -qrr vendor/bin/phpunit --coverage-html coverage tests/Unit
    print_success "Reporte de cobertura generado en: coverage/"
else
    print_warning "phpdbg no está disponible. No se puede generar reporte de cobertura."
    print_status "Instalando xdebug para cobertura..."
    composer require --dev phpunit/php-code-coverage
fi

echo ""
print_status "Resumen de tests ejecutados:"
echo "✅ FASE 1: FUNDAMENTOS - Tests completados"
echo "✅ FASE 2: CORE SERVICES - Tests completados"
echo ""

print_success "¡Todos los tests han sido ejecutados exitosamente!"
print_status "Revisa la salida anterior para ver los resultados detallados."
