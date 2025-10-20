#!/bin/bash

# Script para verificar el estado de Redis en Laravel Cloud
# Ejecutar con: ./check-redis-status.sh

echo "🔍 Verificando estado de Redis en Laravel Cloud..."
echo "=================================================="

# Verificar archivo .env
echo "📋 Verificando configuración..."
if [ -f ".env" ]; then
    echo "✅ Archivo .env encontrado"
    
    # Mostrar configuración de Redis
    echo ""
    echo "🔧 Configuración actual:"
    echo "CACHE_DRIVER: $(grep '^CACHE_DRIVER=' .env | cut -d'=' -f2)"
    echo "SESSION_DRIVER: $(grep '^SESSION_DRIVER=' .env | cut -d'=' -f2)"
    echo "QUEUE_CONNECTION: $(grep '^QUEUE_CONNECTION=' .env | cut -d'=' -f2)"
    echo "REDIS_HOST: $(grep '^REDIS_HOST=' .env | cut -d'=' -f2)"
    echo "REDIS_PORT: $(grep '^REDIS_PORT=' .env | cut -d'=' -f2)"
else
    echo "❌ Archivo .env no encontrado"
    exit 1
fi

echo ""
echo "🧪 Probando conectividad..."

# Probar ping a Redis
php -r "
try {
    \$redis = new Redis();
    \$host = getenv('REDIS_HOST') ?: '127.0.0.1';
    \$port = getenv('REDIS_PORT') ?: 6379;
    \$password = getenv('REDIS_PASSWORD');
    
    if (\$redis->connect(\$host, \$port, 5)) {
        if (\$password && !\$redis->auth(\$password)) {
            echo '❌ Error de autenticación Redis\n';
            exit(1);
        }
        
        \$pong = \$redis->ping();
        echo '✅ Redis responde: ' . \$pong . '\n';
        
        // Probar operación básica
        \$redis->set('test_key', 'test_value', 10);
        \$value = \$redis->get('test_key');
        
        if (\$value === 'test_value') {
            echo '✅ Operaciones de Redis funcionando\n';
        } else {
            echo '❌ Error en operaciones de Redis\n';
        }
        
        \$redis->del('test_key');
        \$redis->close();
        
    } else {
        echo '❌ No se pudo conectar a Redis\n';
        exit(1);
    }
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . '\n';
    exit(1);
}
"

echo ""
echo "📊 Verificando servicios de Laravel..."

# Verificar caché
echo "Caché:"
php artisan tinker --execute="
try {
    Cache::put('test_cache', 'test_value', 60);
    \$value = Cache::get('test_cache');
    if (\$value === 'test_value') {
        echo '✅ Caché funcionando\n';
    } else {
        echo '❌ Error en caché\n';
    }
    Cache::forget('test_cache');
} catch (Exception \$e) {
    echo '❌ Error en caché: ' . \$e->getMessage() . '\n';
}
"

# Verificar sesiones
echo "Sesiones:"
php artisan tinker --execute="
try {
    session(['test_session' => 'test_value']);
    \$value = session('test_session');
    if (\$value === 'test_value') {
        echo '✅ Sesiones funcionando\n';
    } else {
        echo '❌ Error en sesiones\n';
    }
    session()->forget('test_session');
} catch (Exception \$e) {
    echo '❌ Error en sesiones: ' . \$e->getMessage() . '\n';
}
"

# Verificar colas
echo "Colas:"
php artisan tinker --execute="
try {
    \$queue = Queue::connection('redis');
    echo '✅ Conexión de colas Redis establecida\n';
} catch (Exception \$e) {
    echo '❌ Error en colas: ' . \$e->getMessage() . '\n';
}
"

echo ""
echo "🎯 Resumen:"
echo "Para verificar en tiempo real, ejecuta:"
echo "  php artisan redis:verify"
echo "  php artisan redis:monitor"
echo ""
echo "Para verificar logs de Redis:"
echo "  php artisan queue:work --once"
echo "  php artisan cache:clear"
