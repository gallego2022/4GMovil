<?php

/**
 * Script para verificar el uso de Redis en Laravel Cloud
 * Ejecutar con: php verify-redis-laravel-cloud.php
 */

echo "🔍 Verificando configuración de Redis en Laravel Cloud...\n\n";

// Cargar variables de entorno
if (file_exists('.env')) {
    $env = parse_ini_file('.env', true);
    echo "✅ Archivo .env encontrado\n";
} else {
    echo "❌ Archivo .env no encontrado\n";
    exit(1);
}

// Verificar configuración de caché
echo "\n📦 CONFIGURACIÓN DE CACHÉ:\n";
echo "CACHE_DRIVER: " . ($env['CACHE_DRIVER'] ?? 'No definido') . "\n";
echo "CACHE_STORE: " . ($env['CACHE_STORE'] ?? 'No definido') . "\n";

// Verificar configuración de sesiones
echo "\n🔐 CONFIGURACIÓN DE SESIONES:\n";
echo "SESSION_DRIVER: " . ($env['SESSION_DRIVER'] ?? 'No definido') . "\n";
echo "SESSION_LIFETIME: " . ($env['SESSION_LIFETIME'] ?? 'No definido') . "\n";

// Verificar configuración de colas
echo "\n⚡ CONFIGURACIÓN DE COLAS:\n";
echo "QUEUE_CONNECTION: " . ($env['QUEUE_CONNECTION'] ?? 'No definido') . "\n";

// Verificar configuración de Redis
echo "\n🔴 CONFIGURACIÓN DE REDIS:\n";
echo "REDIS_HOST: " . ($env['REDIS_HOST'] ?? 'No definido') . "\n";
echo "REDIS_PORT: " . ($env['REDIS_PORT'] ?? 'No definido') . "\n";
echo "REDIS_PASSWORD: " . (isset($env['REDIS_PASSWORD']) ? 'Configurado' : 'No configurado') . "\n";
echo "REDIS_CLIENT: " . ($env['REDIS_CLIENT'] ?? 'No definido') . "\n";

// Verificar si Redis está siendo usado
$usingRedis = false;
$redisServices = [];

if (($env['CACHE_DRIVER'] ?? '') === 'redis') {
    $usingRedis = true;
    $redisServices[] = 'Caché';
}

if (($env['SESSION_DRIVER'] ?? '') === 'redis') {
    $usingRedis = true;
    $redisServices[] = 'Sesiones';
}

if (($env['QUEUE_CONNECTION'] ?? '') === 'redis') {
    $usingRedis = true;
    $redisServices[] = 'Colas';
}

echo "\n🎯 RESUMEN:\n";
if ($usingRedis) {
    echo "✅ Redis está configurado y siendo usado para:\n";
    foreach ($redisServices as $service) {
        echo "   - $service\n";
    }
} else {
    echo "❌ Redis NO está siendo usado\n";
    echo "   - Caché: " . ($env['CACHE_DRIVER'] ?? 'No definido') . "\n";
    echo "   - Sesiones: " . ($env['SESSION_DRIVER'] ?? 'No definido') . "\n";
    echo "   - Colas: " . ($env['QUEUE_CONNECTION'] ?? 'No definido') . "\n";
}

// Verificar conectividad de Redis (si está configurado)
if ($usingRedis) {
    echo "\n🔌 VERIFICANDO CONECTIVIDAD DE REDIS:\n";
    
    try {
        // Intentar conectar a Redis
        $redis = new Redis();
        $host = $env['REDIS_HOST'] ?? '127.0.0.1';
        $port = $env['REDIS_PORT'] ?? 6379;
        $password = $env['REDIS_PASSWORD'] ?? null;
        
        $connected = $redis->connect($host, $port, 5); // 5 segundos timeout
        
        if ($connected) {
            if ($password) {
                $auth = $redis->auth($password);
                if (!$auth) {
                    echo "❌ Error de autenticación con Redis\n";
                } else {
                    echo "✅ Conexión a Redis exitosa\n";
                    
                    // Probar operaciones básicas
                    $redis->set('test_key', 'test_value', 10);
                    $value = $redis->get('test_key');
                    
                    if ($value === 'test_value') {
                        echo "✅ Operaciones de Redis funcionando correctamente\n";
                    } else {
                        echo "❌ Error en operaciones de Redis\n";
                    }
                    
                    $redis->del('test_key');
                }
            } else {
                echo "✅ Conexión a Redis exitosa (sin autenticación)\n";
            }
        } else {
            echo "❌ No se pudo conectar a Redis en $host:$port\n";
        }
        
        $redis->close();
        
    } catch (Exception $e) {
        echo "❌ Error al conectar con Redis: " . $e->getMessage() . "\n";
        echo "   Asegúrate de que Redis esté disponible en Laravel Cloud\n";
    }
}

echo "\n📋 RECOMENDACIONES:\n";
if (!$usingRedis) {
    echo "⚠️  Para usar Redis en Laravel Cloud:\n";
    echo "   1. Verifica que Redis esté habilitado en tu plan de Laravel Cloud\n";
    echo "   2. Configura las variables de entorno correctamente\n";
    echo "   3. Ejecuta: ./restore-redis-config.sh\n";
} else {
    echo "✅ Redis está configurado correctamente\n";
    echo "   - Verifica que las operaciones funcionen en la aplicación\n";
    echo "   - Monitorea el rendimiento en el dashboard de Laravel Cloud\n";
}

echo "\n🔍 Para verificar en tiempo real, ejecuta:\n";
echo "   php artisan tinker --execute=\"Redis::ping()\"\n";
echo "   php artisan cache:clear\n";
echo "   php artisan queue:work --once\n";
