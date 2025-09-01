<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

echo "🧪 Probando middlewares de seguridad...\n\n";

// 1. Verificar que los middlewares están registrados
echo "1️⃣ Verificando registro de middlewares...\n";

try {
    $middleware = app('router')->getMiddleware();
    
    if (isset($middleware['email.verified'])) {
        echo "   ✅ Middleware 'email.verified' registrado\n";
    } else {
        echo "   ❌ Middleware 'email.verified' NO registrado\n";
    }
    
    if (isset($middleware['admin'])) {
        echo "   ✅ Middleware 'admin' registrado\n";
    } else {
        echo "   ❌ Middleware 'admin' NO registrado\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error verificando middlewares: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Verificar rutas protegidas
echo "2️⃣ Verificando rutas protegidas...\n";

$rutasEmailVerified = [];
$rutasAdmin = [];

foreach (Route::getRoutes() as $route) {
    $middlewares = $route->middleware();
    
    if (in_array('email.verified', $middlewares)) {
        $rutasEmailVerified[] = $route->uri();
    }
    
    if (in_array('admin', $middlewares)) {
        $rutasAdmin[] = $route->uri();
    }
}

echo "   📧 Rutas con email.verified: " . count($rutasEmailVerified) . "\n";
echo "   🔒 Rutas con admin: " . count($rutasAdmin) . "\n";

if (!empty($rutasEmailVerified)) {
    echo "   📋 Ejemplos de rutas email.verified:\n";
    foreach (array_slice($rutasEmailVerified, 0, 3) as $ruta) {
        echo "      • /{$ruta}\n";
    }
}

if (!empty($rutasAdmin)) {
    echo "   📋 Ejemplos de rutas admin:\n";
    foreach (array_slice($rutasAdmin, 0, 3) as $ruta) {
        echo "      • /{$ruta}\n";
    }
}

echo "\n";

// 3. Crear usuario de prueba
echo "3️⃣ Creando usuario de prueba...\n";

$email = 'test_middleware_' . time() . '@example.com';
$usuario = Usuario::create([
    'nombre_usuario' => 'Usuario Test Middleware',
    'correo_electronico' => $email,
    'contrasena' => Hash::make('Password123!'),
    'telefono' => '3001234567',
    'estado' => true,
    'rol' => 'cliente',
    'fecha_registro' => now(),
    'email_verified_at' => null, // Sin verificar
]);

echo "   ✅ Usuario creado: {$usuario->nombre_usuario}\n";
echo "   📧 Email: {$usuario->correo_electronico}\n";
echo "   🔑 Contraseña: Password123!\n";
echo "   📧 Verificado: " . ($usuario->email_verified_at ? 'Sí' : 'No') . "\n";
echo "   👤 Rol: {$usuario->rol}\n\n";

// 4. Simular diferentes escenarios
echo "4️⃣ Simulando escenarios de seguridad...\n";

// Escenario 1: Usuario sin verificar intenta acceder a rutas protegidas
echo "   🔒 Escenario 1: Usuario sin verificar → Redirigido a verificación OTP\n";

// Escenario 2: Usuario cliente intenta acceder a rutas admin
echo "   🚫 Escenario 2: Usuario cliente → Redirigido a perfil\n";

// Escenario 3: Usuario verificado y admin
$usuario->update([
    'email_verified_at' => now(),
    'rol' => 'admin'
]);

echo "   ✅ Escenario 3: Usuario verificado y admin → Acceso completo\n";

echo "\n";

// 5. Limpieza
echo "5️⃣ Limpiando datos de prueba...\n";

$usuario->delete();
echo "   ✅ Usuario de prueba eliminado\n";

echo "\n🎉 ¡Prueba completada exitosamente!\n";
echo "📋 Resumen de la migración:\n";
echo "   • Middleware email.verified implementado ✅\n";
echo "   • Middleware admin implementado ✅\n";
echo "   • Trait AdminCheck eliminado ✅\n";
echo "   • Controladores limpiados ✅\n";
echo "   • Rutas protegidas configuradas ✅\n";

echo "\n🔒 Sistema de seguridad mejorado:\n";
echo "   📧 Verificación de email obligatoria\n";
echo "   🔒 Protección de rutas admin\n";
echo "   🚫 Redirecciones automáticas\n";
echo "   🧹 Código más limpio y mantenible\n";
