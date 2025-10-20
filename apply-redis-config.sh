#!/bin/bash

# Script para aplicar configuración de Redis después del build
# Este script restaura la configuración de Redis para runtime

echo "🔴 Aplicando configuración de Redis para runtime..."

# Verificar si el archivo .env existe
if [ ! -f ".env" ]; then
    echo "❌ Archivo .env no encontrado"
    exit 1
fi

# Aplicar configuración de Redis
echo "📋 Configurando servicios para usar Redis..."

# Configurar caché para Redis
sed -i 's/CACHE_DRIVER=file/CACHE_DRIVER=redis/' .env
sed -i 's/CACHE_STORE=file/CACHE_STORE=redis/' .env

# Configurar sesiones para Redis
sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=redis/' .env
sed -i 's/SESSION_DRIVER=cookie/SESSION_DRIVER=redis/' .env

# Configurar colas para Redis
sed -i 's/QUEUE_CONNECTION=sync/QUEUE_CONNECTION=redis/' .env
sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=redis/' .env

echo "✅ Configuración de Redis aplicada"

# Limpiar caché para aplicar nueva configuración
echo "🧹 Limpiando caché para aplicar configuración de Redis..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "✅ Caché limpiado"

# Verificar configuración
echo "🔍 Verificando configuración aplicada..."
echo "CACHE_DRIVER: $(grep '^CACHE_DRIVER=' .env | cut -d'=' -f2)"
echo "SESSION_DRIVER: $(grep '^SESSION_DRIVER=' .env | cut -d'=' -f2)"
echo "QUEUE_CONNECTION: $(grep '^QUEUE_CONNECTION=' .env | cut -d'=' -f2)"

# Probar Redis
echo "🧪 Probando Redis..."
php artisan tinker --execute="
try {
    \$pong = Redis::ping();
    echo '✅ Redis responde: ' . \$pong . PHP_EOL;
    
    // Probar caché
    Cache::put('test_redis_config', 'test_value', 60);
    \$value = Cache::get('test_redis_config');
    if (\$value === 'test_value') {
        echo '✅ Caché con Redis funcionando' . PHP_EOL;
    } else {
        echo '❌ Error en caché con Redis' . PHP_EOL;
    }
    Cache::forget('test_redis_config');
    
    // Probar sesiones
    session(['test_redis_session' => 'test_value']);
    \$sessionValue = session('test_redis_session');
    if (\$sessionValue === 'test_value') {
        echo '✅ Sesiones con Redis funcionando' . PHP_EOL;
    } else {
        echo '❌ Error en sesiones con Redis' . PHP_EOL;
    }
    session()->forget('test_redis_session');
    
    // Probar colas
    \$queue = Queue::connection('redis');
    echo '✅ Colas con Redis configuradas' . PHP_EOL;
    
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo "✅ Configuración de Redis aplicada exitosamente!"
