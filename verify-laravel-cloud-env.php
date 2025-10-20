<?php

/**
 * Script de verificación para variables de entorno de Laravel Cloud
 * Este script verifica que las variables de entorno estén correctamente configuradas
 */

echo "🔍 Verificando configuración de Laravel Cloud...\n\n";

// Variables de entorno críticas para Laravel Cloud
$requiredVars = [
    'APP_NAME',
    'APP_ENV',
    'APP_KEY',
    'APP_URL',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD',
    'CACHE_DRIVER',
    'SESSION_DRIVER',
    'QUEUE_CONNECTION',
];

$optionalVars = [
    'REDIS_HOST',
    'REDIS_PORT',
    'MAIL_MAILER',
    'MAIL_HOST',
    'STRIPE_KEY',
    'GOOGLE_CLIENT_ID',
];

echo "📋 Variables de entorno requeridas:\n";
$allRequiredPresent = true;

foreach ($requiredVars as $var) {
    $value = getenv($var);
    if ($value !== false && !empty($value)) {
        echo "  ✅ $var: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
    } else {
        echo "  ❌ $var: NO CONFIGURADA\n";
        $allRequiredPresent = false;
    }
}

echo "\n📋 Variables de entorno opcionales:\n";
foreach ($optionalVars as $var) {
    $value = getenv($var);
    if ($value !== false && !empty($value)) {
        echo "  ✅ $var: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
    } else {
        echo "  ⚠️  $var: No configurada (opcional)\n";
    }
}

echo "\n🔧 Configuración de base de datos:\n";
$dbHost = getenv('DB_HOST');
$dbDatabase = getenv('DB_DATABASE');
$dbUsername = getenv('DB_USERNAME');

if ($dbHost && $dbDatabase && $dbUsername) {
    echo "  ✅ Host: $dbHost\n";
    echo "  ✅ Database: $dbDatabase\n";
    echo "  ✅ Username: $dbUsername\n";
    echo "  ✅ Password: " . (getenv('DB_PASSWORD') ? '***CONFIGURADA***' : 'NO CONFIGURADA') . "\n";
} else {
    echo "  ❌ Variables de base de datos no configuradas correctamente\n";
}

echo "\n🔴 Configuración de Redis:\n";
$redisHost = getenv('REDIS_HOST');
$redisPort = getenv('REDIS_PORT');

if ($redisHost && $redisPort) {
    echo "  ✅ Host: $redisHost\n";
    echo "  ✅ Port: $redisPort\n";
} else {
    echo "  ⚠️  Redis no configurado (usará caché de archivos)\n";
}

echo "\n📊 Resumen:\n";
if ($allRequiredPresent) {
    echo "  ✅ Todas las variables requeridas están configuradas\n";
    echo "  🚀 La aplicación debería funcionar correctamente en Laravel Cloud\n";
} else {
    echo "  ❌ Faltan variables de entorno requeridas\n";
    echo "  🔧 Revisa la configuración en Laravel Cloud\n";
}

echo "\n💡 Consejos para Laravel Cloud:\n";
echo "  • Las variables de base de datos se configuran automáticamente\n";
echo "  • No configures manualmente DB_HOST, DB_DATABASE, etc.\n";
echo "  • Usa las variables de entorno del panel de Laravel Cloud\n";
echo "  • Verifica que APP_URL esté configurada correctamente\n";

echo "\n";
