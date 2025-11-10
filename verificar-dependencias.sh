#!/bin/bash

echo "=========================================="
echo "Verificación de Dependencias - 4GMovil"
echo "=========================================="
echo ""

echo "1. Verificando dependencias de Composer (PHP)..."
echo "----------------------------------------"
composer show --installed | grep -E "(dompdf|laravel|intervention)" || echo "No se encontraron paquetes específicos"
echo ""

echo "2. Verificando si DomPDF está instalado..."
echo "----------------------------------------"
if composer show dompdf/dompdf 2>/dev/null; then
    echo "✓ DomPDF está instalado"
else
    echo "✗ DomPDF NO está instalado"
    echo "  Ejecuta: composer require dompdf/dompdf"
fi
echo ""

echo "3. Verificando dependencias de npm (Node.js)..."
echo "----------------------------------------"
if [ -d "node_modules" ]; then
    echo "✓ node_modules existe"
    if [ -d "node_modules/sweetalert2" ]; then
        echo "✓ SweetAlert2 está instalado"
    else
        echo "✗ SweetAlert2 NO está instalado"
        echo "  Ejecuta: npm install"
    fi
else
    echo "✗ node_modules NO existe"
    echo "  Ejecuta: npm install"
fi
echo ""

echo "4. Verificando autoload de Composer..."
echo "----------------------------------------"
composer dump-autoload --no-interaction
if [ $? -eq 0 ]; then
    echo "✓ Autoload actualizado correctamente"
else
    echo "✗ Error al actualizar autoload"
fi
echo ""

echo "5. Verificando archivos de configuración..."
echo "----------------------------------------"
if [ -f "composer.json" ]; then
    echo "✓ composer.json existe"
else
    echo "✗ composer.json NO existe"
fi

if [ -f "package.json" ]; then
    echo "✓ package.json existe"
else
    echo "✗ package.json NO existe"
fi
echo ""

echo "=========================================="
echo "Verificación completada"
echo "=========================================="

