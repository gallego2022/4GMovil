<?php

/**
 * Script de Verificación Completa de Integración
 * 
 * Verifica la cadena completa:
 * 1. Migraciones → Modelos
 * 2. Modelos → Controladores/Servicios
 * 3. Controladores → Servicios/Repositorios
 * 4. Controladores → Rutas (usando sistema de rutas de Laravel)
 * 5. Rutas → Vistas
 * 6. Vistas → Rutas
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Iniciando verificación completa de integración...\n\n";

$errores = [];
$advertencias = [];
$exitos = [];

// ==================== 1. VERIFICAR MIGRACIONES → MODELOS ====================
echo "📊 1. Verificando Migraciones → Modelos...\n";
$migraciones = glob(__DIR__ . '/../database/migrations/*.php');
$modelos = glob(__DIR__ . '/../app/Models/*.php');

$tablasEnMigraciones = [];
foreach ($migraciones as $migracion) {
    $contenido = file_get_contents($migracion);
    if (preg_match('/Schema::(create|table)\([\'"]([^\'"]+)[\'"]/', $contenido, $matches)) {
        $tabla = $matches[2];
        $tablasEnMigraciones[$tabla] = $migracion;
    }
}

$modelosVerificados = [];
foreach ($modelos as $modelo) {
    $nombreArchivo = basename($modelo, '.php');
    $contenido = file_get_contents($modelo);
    
    // Buscar $table o protected $table
    if (preg_match('/protected\s+\$table\s*=\s*[\'"]([^\'"]+)[\'"]/', $contenido, $matches)) {
        $tabla = $matches[1];
        if (isset($tablasEnMigraciones[$tabla])) {
            $modelosVerificados[$nombreArchivo] = $tabla;
            $exitos[] = "✅ Modelo {$nombreArchivo} → Tabla {$tabla} (migración existe)";
        } else {
            $advertencias[] = "⚠️  Modelo {$nombreArchivo} → Tabla {$tabla} (migración no encontrada)";
        }
    }
}

echo "   Modelos verificados: " . count($modelosVerificados) . "\n\n";

// ==================== 2. VERIFICAR MODELOS → CONTROLADORES/SERVICIOS ====================
echo "📊 2. Verificando Modelos → Controladores/Servicios...\n";

$controladores = glob(__DIR__ . '/../app/Http/Controllers/**/*.php');
$servicios = glob(__DIR__ . '/../app/Services/**/*.php');
$repositorios = glob(__DIR__ . '/../app/Repositories/*.php');

$modelosUsados = [];
foreach (array_merge($controladores, $servicios, $repositorios) as $archivo) {
    if (strpos($archivo, 'Base') !== false || strpos($archivo, 'Servicios') !== false) {
        continue;
    }
    $contenido = file_get_contents($archivo);
    // Buscar uso de modelos: App\Models\NombreModelo o use App\Models\NombreModelo
    if (preg_match_all('/App\\\\Models\\\\([A-Za-z]+)|use App\\\\Models\\\\([A-Za-z]+)/', $contenido, $matches)) {
        foreach (array_merge($matches[1], $matches[2]) as $modelo) {
            if (!empty($modelo)) {
                $modelosUsados[$modelo] = true;
            }
        }
    }
}

foreach ($modelosUsados as $modelo => $usado) {
    $archivoModelo = __DIR__ . "/../app/Models/{$modelo}.php";
    if (file_exists($archivoModelo)) {
        $exitos[] = "✅ Modelo {$modelo} existe y está siendo usado";
    } else {
        $errores[] = "❌ Modelo {$modelo} se usa pero no existe";
    }
}

echo "   Modelos en uso verificados: " . count($modelosUsados) . "\n\n";

// ==================== 3. VERIFICAR CONTROLADORES → SERVICIOS/REPOSITORIOS ====================
echo "📊 3. Verificando Controladores → Servicios/Repositorios...\n";

// Cargar todos los servicios disponibles
$serviciosDisponibles = [];
foreach ($servicios as $servicio) {
    $rutaRelativa = str_replace(__DIR__ . '/../app/Services/', '', $servicio);
    $rutaRelativa = str_replace('.php', '', $rutaRelativa);
    $claseCompleta = 'App\\Services\\' . str_replace('/', '\\', $rutaRelativa);
    $serviciosDisponibles[$claseCompleta] = $servicio;
}

$repositoriosDisponibles = [];
foreach ($repositorios as $repositorio) {
    $nombre = basename($repositorio, '.php');
    $claseCompleta = 'App\\Repositories\\' . $nombre;
    $repositoriosDisponibles[$claseCompleta] = $repositorio;
}

foreach ($controladores as $controlador) {
    if (strpos($controlador, 'Base') !== false || strpos($controlador, 'Servicios') !== false) {
        continue;
    }
    
    $contenido = file_get_contents($controlador);
    $nombreControlador = basename($controlador, '.php');
    
    // Buscar servicios inyectados en el constructor o como propiedades
    if (preg_match_all('/use\s+([A-Za-z0-9\\\\]+Service)/', $contenido, $matches)) {
        foreach ($matches[1] as $servicio) {
            // Normalizar el namespace
            if (strpos($servicio, 'App\\') === 0) {
                $servicioCompleto = $servicio;
            } else {
                $servicioCompleto = 'App\\Services\\' . $servicio;
            }
            
            // Buscar el servicio en todos los namespaces disponibles
            $servicioEncontrado = false;
            if (isset($serviciosDisponibles[$servicioCompleto])) {
                $exitos[] = "✅ Controlador {$nombreControlador} → Servicio {$servicioCompleto} (existe)";
                $servicioEncontrado = true;
            } else {
                // Buscar por nombre de clase sin namespace
                $nombreClase = basename(str_replace('\\', '/', $servicioCompleto));
                foreach ($serviciosDisponibles as $servicioDisponible => $archivo) {
                    if (basename($servicioDisponible, '.php') === $nombreClase || 
                        strpos($servicioDisponible, $nombreClase) !== false) {
                        $exitos[] = "✅ Controlador {$nombreControlador} → Servicio {$servicioDisponible} (existe)";
                        $servicioEncontrado = true;
                        break;
                    }
                }
            }
            
            if (!$servicioEncontrado) {
                $errores[] = "❌ Controlador {$nombreControlador} → Servicio {$servicioCompleto} (no existe)";
            }
        }
    }
}

echo "   Servicios verificados\n\n";

// ==================== 4. VERIFICAR CONTROLADORES → RUTAS (USANDO LARAVEL) ====================
echo "📊 4. Verificando Controladores → Rutas (usando sistema de Laravel)...\n";

use Illuminate\Support\Facades\Route;

$rutasLaravel = [];
try {
    // Obtener rutas de forma más segura
    $rutas = Route::getRoutes();
    $contador = 0;
    foreach ($rutas as $ruta) {
        $contador++;
        if ($contador > 1000) break; // Limitar para evitar timeouts
        
        try {
            $accion = $ruta->getAction();
            if (isset($accion['controller'])) {
                $controller = $accion['controller'];
                if (preg_match('/([A-Za-z0-9\\\\]+)@([a-zA-Z_][a-zA-Z0-9_]*)/', $controller, $matches)) {
                    $claseCompleta = $matches[1];
                    $metodo = $matches[2];
                    
                    if (!isset($rutasLaravel[$claseCompleta])) {
                        $rutasLaravel[$claseCompleta] = [];
                    }
                    if (!in_array($metodo, $rutasLaravel[$claseCompleta])) {
                        $rutasLaravel[$claseCompleta][] = $metodo;
                    }
                }
            }
        } catch (\Exception $e) {
            // Continuar con la siguiente ruta
            continue;
        }
    }
} catch (\Exception $e) {
    echo "   ⚠️  Error al obtener rutas de Laravel: " . $e->getMessage() . "\n";
    echo "   Continuando con verificación de archivos de rutas...\n";
    
    // Fallback: leer archivos de rutas directamente
    $archivosRutas = glob(__DIR__ . '/../routes/*.php');
    foreach ($archivosRutas as $archivoRuta) {
        $contenido = file_get_contents($archivoRuta);
        // Buscar Route::resource
        if (preg_match_all('/Route::resource\([\'"]([^\'"]+)[\'"],\s*([A-Za-z0-9\\\\]+)::class/', $contenido, $matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $controlador = $matches[2][$i];
                if (!isset($rutasLaravel[$controlador])) {
                    $rutasLaravel[$controlador] = [];
                }
                $rutasLaravel[$controlador] = array_merge($rutasLaravel[$controlador], 
                    ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
            }
        }
        // Buscar Route::get/post/put/patch/delete con controlador
        if (preg_match_all('/Route::(get|post|put|patch|delete)\([^,]+,\s*\[([^,]+)::class,\s*[\'"]([^\'"]+)[\'"]/', $contenido, $matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $controlador = $matches[2][$i];
                $metodo = $matches[3][$i];
                if (!isset($rutasLaravel[$controlador])) {
                    $rutasLaravel[$controlador] = [];
                }
                if (!in_array($metodo, $rutasLaravel[$controlador])) {
                    $rutasLaravel[$controlador][] = $metodo;
                }
            }
        }
    }
}

// Verificar métodos de controladores
foreach ($controladores as $controlador) {
    if (strpos($controlador, 'Base') !== false || strpos($controlador, 'Servicios') !== false) {
        continue;
    }
    
    try {
        $contenido = file_get_contents($controlador);
        $namespace = '';
        if (preg_match('/namespace\s+([A-Za-z0-9\\\\]+);/', $contenido, $nsMatch)) {
            $namespace = $nsMatch[1];
        }
        
        $nombreClase = basename($controlador, '.php');
        $claseCompleta = $namespace . '\\' . $nombreClase;
        
        // Buscar métodos públicos usando Reflection
        if (class_exists($claseCompleta)) {
            $reflection = new ReflectionClass($claseCompleta);
            $metodos = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            
            // Métodos heredados que no necesitan rutas
            $metodosHeredados = [
                'middleware', 'getMiddleware', 'callAction', 'authorize', 'authorizeForUser',
                'authorizeResource', 'validateWith', 'validate', 'validateWithBag',
                '__construct', '__destruct', '__call', '__callStatic', '__get', '__set',
                '__isset', '__unset', '__sleep', '__wakeup', '__toString', '__invoke',
                '__clone', '__debugInfo'
            ];
            
            foreach ($metodos as $metodo) {
                $nombreMetodo = $metodo->getName();
                
                // Saltar métodos heredados o mágicos
                if (in_array($nombreMetodo, $metodosHeredados) || strpos($nombreMetodo, '__') === 0) {
                    continue;
                }
                
                // Solo verificar métodos definidos en esta clase, no heredados
                if ($metodo->getDeclaringClass()->getName() !== $claseCompleta) {
                    continue;
                }
                
                if (isset($rutasLaravel[$claseCompleta]) && in_array($nombreMetodo, $rutasLaravel[$claseCompleta])) {
                    $exitos[] = "✅ {$claseCompleta}::{$nombreMetodo}() tiene ruta definida";
                } else {
                    $advertencias[] = "⚠️  {$claseCompleta}::{$nombreMetodo}() NO tiene ruta definida";
                }
            }
        }
    } catch (\Exception $e) {
        $advertencias[] = "⚠️  Error al verificar {$controlador}: " . $e->getMessage();
    }
}

echo "   Métodos de controladores verificados\n\n";

// ==================== 5. VERIFICAR RUTAS → VISTAS ====================
echo "📊 5. Verificando Rutas → Vistas...\n";

$vistas = glob(__DIR__ . '/../resources/views/**/*.blade.php');
$vistasDisponibles = [];
foreach ($vistas as $vista) {
    $rutaVista = str_replace(__DIR__ . '/../resources/views/', '', $vista);
    $rutaVista = str_replace('.blade.php', '', $rutaVista);
    $rutaVista = str_replace('/', '.', $rutaVista);
    $vistasDisponibles[$rutaVista] = $vista;
}

foreach ($controladores as $controlador) {
    if (strpos($controlador, 'Base') !== false || strpos($controlador, 'Servicios') !== false) {
        continue;
    }
    
    $contenido = file_get_contents($controlador);
    
    // Buscar View::make o view()
    if (preg_match_all('/View::make\([\'"]([^\'"]+)[\'"]|view\([\'"]([^\'"]+)[\'"]/', $contenido, $matches)) {
        foreach (array_merge($matches[1], $matches[2]) as $vista) {
            if (empty($vista)) continue;
            
            // Verificar vista con puntos
            $vistaEncontrada = false;
            if (isset($vistasDisponibles[$vista])) {
                $exitos[] = "✅ Vista '{$vista}' existe";
                $vistaEncontrada = true;
            } else {
                // Verificar si existe como archivo físico
                $rutaVista = str_replace('.', '/', $vista);
                $archivoVista = __DIR__ . '/../resources/views/' . $rutaVista . '.blade.php';
                if (file_exists($archivoVista)) {
                    $exitos[] = "✅ Vista '{$vista}' existe (archivo físico)";
                    $vistaEncontrada = true;
                }
            }
            
            if (!$vistaEncontrada) {
                $errores[] = "❌ Vista '{$vista}' NO existe (usada en " . basename($controlador) . ")";
            }
        }
    }
}

echo "   Vistas verificadas\n\n";

// ==================== 6. VERIFICAR VISTAS → RUTAS ====================
echo "📊 6. Verificando Vistas → Rutas...\n";

$rutasNombradas = [];
try {
    $rutas = Route::getRoutes();
    foreach ($rutas as $ruta) {
        $nombre = $ruta->getName();
        if ($nombre) {
            $rutasNombradas[$nombre] = true;
        }
    }
} catch (\Exception $e) {
    echo "   ⚠️  Error al obtener rutas nombradas: " . $e->getMessage() . "\n";
}

$rutasEnVistas = [];
foreach ($vistas as $vista) {
    $contenido = file_get_contents($vista);
    
    // Buscar route('nombre.ruta')
    if (preg_match_all('/route\([\'"]([^\'"]+)[\'"]/', $contenido, $matches)) {
        foreach ($matches[1] as $nombreRuta) {
            if (!isset($rutasEnVistas[$nombreRuta])) {
                $rutasEnVistas[$nombreRuta] = [];
            }
            $rutasEnVistas[$nombreRuta][] = basename($vista);
        }
    }
}

foreach ($rutasEnVistas as $nombreRuta => $archivos) {
    if (isset($rutasNombradas[$nombreRuta])) {
        $exitos[] = "✅ Ruta '{$nombreRuta}' existe (usada en " . count($archivos) . " vista(s))";
    } else {
        $errores[] = "❌ Ruta '{$nombreRuta}' NO existe (usada en: " . implode(', ', array_unique($archivos)) . ")";
    }
}

echo "   Rutas en vistas verificadas\n\n";

// ==================== 7. VERIFICAR SERVICIOS → MÉTODOS USADOS ====================
echo "📊 7. Verificando Servicios → Métodos usados en Controladores...\n";

foreach ($controladores as $controlador) {
    if (strpos($controlador, 'Base') !== false || strpos($controlador, 'Servicios') !== false) {
        continue;
    }
    
    $contenido = file_get_contents($controlador);
    $nombreControlador = basename($controlador, '.php');
    
    // Buscar servicios inyectados
    if (preg_match('/protected\s+\$([a-zA-Z]+Service);/', $contenido, $serviceMatch)) {
        $nombreServicio = $serviceMatch[1];
        
        // Buscar métodos del servicio usados
        if (preg_match_all('/\$this->' . $nombreServicio . '->([a-zA-Z_][a-zA-Z0-9_]*)\(/', $contenido, $methodMatches)) {
            foreach ($methodMatches[1] as $metodo) {
                // Buscar el servicio
                $servicioEncontrado = false;
                foreach ($servicios as $servicio) {
                    $contenidoServicio = file_get_contents($servicio);
                    $nombreServicioArchivo = basename($servicio, '.php');
                    
                    // Verificar si el nombre del servicio coincide
                    if (stripos($nombreServicioArchivo, $nombreServicio) !== false) {
                        if (preg_match('/public\s+function\s+' . $metodo . '\s*\(/', $contenidoServicio)) {
                            $exitos[] = "✅ {$nombreControlador} → {$nombreServicioArchivo}::{$metodo}() existe";
                            $servicioEncontrado = true;
                            break;
                        }
                    }
                }
                
                if (!$servicioEncontrado) {
                    $advertencias[] = "⚠️  {$nombreControlador} → {$nombreServicio}Service::{$metodo}() NO encontrado";
                }
            }
        }
    }
}

echo "   Métodos de servicios verificados\n\n";

// ==================== REPORTE FINAL ====================
echo "================================================================================\n";
echo "📊 REPORTE FINAL\n";
echo "================================================================================\n\n";

echo "✅ Éxitos: " . count($exitos) . "\n";
echo "⚠️  Advertencias: " . count($advertencias) . "\n";
echo "❌ Errores: " . count($errores) . "\n\n";

if (count($errores) > 0) {
    echo "❌ ERRORES ENCONTRADOS:\n";
    echo "--------------------------------------------------------------------------------\n";
    foreach (array_slice($errores, 0, 50) as $error) {
        echo "  {$error}\n";
    }
    if (count($errores) > 50) {
        echo "  ... y " . (count($errores) - 50) . " errores más\n";
    }
    echo "\n";
}

if (count($advertencias) > 0) {
    echo "⚠️  ADVERTENCIAS (primeras 30):\n";
    echo "--------------------------------------------------------------------------------\n";
    foreach (array_slice($advertencias, 0, 30) as $advertencia) {
        echo "  {$advertencia}\n";
    }
    if (count($advertencias) > 30) {
        echo "  ... y " . (count($advertencias) - 30) . " advertencias más\n";
    }
    echo "\n";
}

if (count($errores) === 0 && count($advertencias) === 0) {
    echo "✅ ¡Todo está correctamente integrado!\n";
} elseif (count($errores) === 0) {
    echo "✅ No hay errores críticos. Revisa las advertencias.\n";
} else {
    echo "❌ Se encontraron errores que deben corregirse.\n";
}

echo "\n";
