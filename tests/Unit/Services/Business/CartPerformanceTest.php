<?php

namespace Tests\Unit\Services\Business;

use Tests\TestCase;
use App\Services\Business\CarritoService;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Usuario;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CartPerformanceTest extends TestCase
{
    protected $carritoService;
    protected $usuarios;
    protected $productos;
    protected $variantes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carritoService = new CarritoService();
        
        // Configurar base de datos de prueba
        $this->artisan('migrate:fresh');
        
        // Configurar sesión para las pruebas
        $this->app['config']->set('session.driver', 'array');
        
        // Crear datos de prueba
        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Crear categoría
        $categoria = Categoria::create([
            'nombre' => 'Categoría Test',
            'descripcion' => 'Descripción de categoría test',
            'estado' => true,
            'orden' => 1
        ]);

        // Crear marca
        $marca = Marca::create([
            'nombre' => 'Marca Test',
            'descripcion' => 'Descripción de marca test',
            'estado' => true,
            'orden' => 1
        ]);

        // Crear múltiples usuarios
        $this->usuarios = collect();
        for ($i = 1; $i <= 10; $i++) {
            $usuario = Usuario::create([
                'nombre_usuario' => "Usuario Performance {$i}",
                'correo_electronico' => "performance{$i}@example.com",
                'contrasena' => Hash::make('password123'),
                'telefono' => "123456789{$i}",
                'estado' => true,
                'rol' => 'cliente',
                'fecha_registro' => now(),
                'email_verified_at' => now()
            ]);
            $this->usuarios->push($usuario);
        }

        // Crear múltiples productos
        $this->productos = collect();
        for ($i = 1; $i <= 20; $i++) {
            $producto = Producto::create([
                'nombre_producto' => "Producto Performance {$i}",
                'descripcion' => "Descripción del producto performance {$i}",
                'precio' => 100.00 + $i,
                'stock' => 100,
                'estado' => 'nuevo',
                'activo' => true,
                'categoria_id' => $categoria->categoria_id,
                'marca_id' => $marca->marca_id
            ]);
            $this->productos->push($producto);
        }

        // Crear múltiples variantes
        $this->variantes = collect();
        foreach ($this->productos as $producto) {
            for ($j = 1; $j <= 3; $j++) {
                $variante = VarianteProducto::create([
                    'producto_id' => $producto->producto_id,
                    'nombre' => "Variante {$j}",
                    'codigo_color' => "#FF{$j}0000",
                    'descripcion' => "Descripción de variante {$j}",
                    'precio_adicional' => 10.00 * $j,
                    'stock' => 50,
                    'disponible' => true,
                    'sku' => "VAR{$producto->producto_id}_{$j}"
                ]);
                $this->variantes->push($variante);
            }
        }
    }

    /** @test */
    public function it_can_handle_multiple_concurrent_cart_operations()
    {
        $startTime = microtime(true);
        
        // Simular operaciones concurrentes en el carrito
        $operations = [];
        for ($i = 0; $i < 50; $i++) {
            $usuario = $this->usuarios->random();
            $producto = $this->productos->random();
            
            Auth::login($usuario);
            
            $request = new Request([
                'producto_id' => $producto->producto_id,
                'cantidad' => rand(1, 5)
            ]);
            
            $result = $this->carritoService->addToCart($request);
            $operations[] = $result;
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Verificar que todas las operaciones fueron exitosas
        $successfulOperations = collect($operations)->filter(function ($result) {
            return $result['success'] === true;
        });
        
        $this->assertCount(50, $successfulOperations);
        $this->assertLessThan(10, $executionTime); // Debe completarse en menos de 10 segundos
        
        Log::info('Prueba de rendimiento: Operaciones concurrentes del carrito', [
            'operaciones' => 50,
            'tiempo_ejecucion' => $executionTime,
            'operaciones_exitosas' => $successfulOperations->count()
        ]);
    }

    /** @test */
    public function it_can_handle_large_cart_with_many_items()
    {
        $usuario = $this->usuarios->first();
        Auth::login($usuario);
        
        $startTime = microtime(true);
        
        // Agregar muchos productos al carrito
        for ($i = 0; $i < 100; $i++) {
            $producto = $this->productos->random();
            $variante = $this->variantes->where('producto_id', $producto->producto_id)->random();
            
            $request = new Request([
                'producto_id' => $producto->producto_id,
                'variante_id' => $variante->variante_id,
                'cantidad' => 1
            ]);
            
            $result = $this->carritoService->addToCart($request);
            $this->assertTrue($result['success']);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Verificar que el carrito tiene muchos items
        $cartResult = $this->carritoService->getCart();
        $this->assertTrue($cartResult['success']);
        $this->assertGreaterThan(50, $cartResult['data']['total_items']);
        
        $this->assertLessThan(15, $executionTime); // Debe completarse en menos de 15 segundos
        
        Log::info('Prueba de rendimiento: Carrito con muchos items', [
            'items_agregados' => 100,
            'tiempo_ejecucion' => $executionTime,
            'total_items_carrito' => $cartResult['data']['total_items']
        ]);
    }

    /** @test */
    public function it_can_handle_rapid_cart_updates()
    {
        $usuario = $this->usuarios->first();
        Auth::login($usuario);
        
        // Primero agregar un producto
        $producto = $this->productos->first();
        $request = new Request([
            'producto_id' => $producto->producto_id,
            'cantidad' => 1
        ]);
        $this->carritoService->addToCart($request);
        
        // Obtener el item del carrito
        $carrito = \App\Models\Carrito::where('usuario_id', $usuario->usuario_id)->first();
        $item = \App\Models\CarritoItem::where('carrito_id', $carrito->id)->first();
        
        $startTime = microtime(true);
        
        // Realizar muchas actualizaciones rápidas
        for ($i = 0; $i < 50; $i++) {
            $updateRequest = new Request(['cantidad' => rand(1, 10)]);
            $result = $this->carritoService->updateCartItem($item->id, $updateRequest);
            $this->assertTrue($result['success']);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(5, $executionTime); // Debe completarse en menos de 5 segundos
        
        Log::info('Prueba de rendimiento: Actualizaciones rápidas del carrito', [
            'actualizaciones' => 50,
            'tiempo_ejecucion' => $executionTime
        ]);
    }

    /** @test */
    public function it_can_handle_concurrent_session_cart_operations()
    {
        $startTime = microtime(true);
        
        // Simular operaciones concurrentes en carritos de sesión
        $operations = [];
        for ($i = 0; $i < 30; $i++) {
            // Simular diferentes sesiones
            Session::flush();
            
            $producto = $this->productos->random();
            $variante = $this->variantes->where('producto_id', $producto->producto_id)->random();
            
            $request = new Request([
                'producto_id' => $producto->producto_id,
                'variante_id' => $variante->variante_id,
                'cantidad' => rand(1, 3)
            ]);
            
            $result = $this->carritoService->addToCart($request);
            $operations[] = $result;
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Verificar que todas las operaciones fueron exitosas
        $successfulOperations = collect($operations)->filter(function ($result) {
            return $result['success'] === true;
        });
        
        $this->assertCount(30, $successfulOperations);
        $this->assertLessThan(8, $executionTime); // Debe completarse en menos de 8 segundos
        
        Log::info('Prueba de rendimiento: Operaciones concurrentes de carrito de sesión', [
            'operaciones' => 30,
            'tiempo_ejecucion' => $executionTime,
            'operaciones_exitosas' => $successfulOperations->count()
        ]);
    }

    /** @test */
    public function it_can_handle_cart_sync_performance()
    {
        $usuario = $this->usuarios->first();
        
        // Crear carrito de sesión con muchos items
        $sessionCart = [];
        for ($i = 0; $i < 20; $i++) {
            $producto = $this->productos->random();
            $variante = $this->variantes->where('producto_id', $producto->producto_id)->random();
            
            $sessionCart[] = [
                'producto_id' => $producto->producto_id,
                'variante_id' => $variante->variante_id,
                'cantidad' => rand(1, 3)
            ];
        }
        Session::put('cart', $sessionCart);
        
        $startTime = microtime(true);
        
        // Sincronizar carrito de sesión con usuario
        Auth::login($usuario);
        $result = $this->carritoService->syncSessionCartWithUser();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertTrue($result['success']);
        $this->assertLessThan(3, $executionTime); // Debe completarse en menos de 3 segundos
        
        Log::info('Prueba de rendimiento: Sincronización de carrito', [
            'items_sincronizados' => 20,
            'tiempo_ejecucion' => $executionTime
        ]);
    }

    /** @test */
    public function it_can_handle_cart_summary_performance()
    {
        $usuario = $this->usuarios->first();
        Auth::login($usuario);
        
        // Crear carrito con muchos items
        for ($i = 0; $i < 50; $i++) {
            $producto = $this->productos->random();
            $variante = $this->variantes->where('producto_id', $producto->producto_id)->random();
            
            $request = new Request([
                'producto_id' => $producto->producto_id,
                'variante_id' => $variante->variante_id,
                'cantidad' => rand(1, 5)
            ]);
            
            $this->carritoService->addToCart($request);
        }
        
        $startTime = microtime(true);
        
        // Obtener resumen del carrito múltiples veces
        for ($i = 0; $i < 20; $i++) {
            $result = $this->carritoService->getCartSummary();
            $this->assertTrue($result['success']);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(2, $executionTime); // Debe completarse en menos de 2 segundos
        
        Log::info('Prueba de rendimiento: Resumen del carrito', [
            'consultas' => 20,
            'tiempo_ejecucion' => $executionTime
        ]);
    }

    /** @test */
    public function it_can_handle_cart_clear_performance()
    {
        $usuario = $this->usuarios->first();
        Auth::login($usuario);
        
        // Crear carrito con muchos items
        for ($i = 0; $i < 100; $i++) {
            $producto = $this->productos->random();
            $variante = $this->variantes->where('producto_id', $producto->producto_id)->random();
            
            $request = new Request([
                'producto_id' => $producto->producto_id,
                'variante_id' => $variante->variante_id,
                'cantidad' => 1
            ]);
            
            $this->carritoService->addToCart($request);
        }
        
        $startTime = microtime(true);
        
        // Limpiar carrito
        $result = $this->carritoService->clearCart();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertTrue($result['success']);
        $this->assertLessThan(1, $executionTime); // Debe completarse en menos de 1 segundo
        
        Log::info('Prueba de rendimiento: Limpieza del carrito', [
            'items_eliminados' => 100,
            'tiempo_ejecucion' => $executionTime
        ]);
    }

    /** @test */
    public function it_can_handle_memory_usage_efficiently()
    {
        $usuario = $this->usuarios->first();
        Auth::login($usuario);
        
        $initialMemory = memory_get_usage();
        
        // Crear carrito con muchos items
        for ($i = 0; $i < 200; $i++) {
            $producto = $this->productos->random();
            $variante = $this->variantes->where('producto_id', $producto->producto_id)->random();
            
            $request = new Request([
                'producto_id' => $producto->producto_id,
                'variante_id' => $variante->variante_id,
                'cantidad' => 1
            ]);
            
            $this->carritoService->addToCart($request);
        }
        
        $finalMemory = memory_get_usage();
        $memoryUsed = $finalMemory - $initialMemory;
        
        // Verificar que el uso de memoria es razonable (menos de 50MB)
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed);
        
        Log::info('Prueba de rendimiento: Uso de memoria', [
            'items_agregados' => 200,
            'memoria_inicial' => $initialMemory,
            'memoria_final' => $finalMemory,
            'memoria_utilizada' => $memoryUsed
        ]);
    }

    /** @test */
    public function it_can_handle_database_query_performance()
    {
        $usuario = $this->usuarios->first();
        Auth::login($usuario);
        
        // Crear carrito con items
        for ($i = 0; $i < 30; $i++) {
            $producto = $this->productos->random();
            $variante = $this->variantes->where('producto_id', $producto->producto_id)->random();
            
            $request = new Request([
                'producto_id' => $producto->producto_id,
                'variante_id' => $variante->variante_id,
                'cantidad' => rand(1, 3)
            ]);
            
            $this->carritoService->addToCart($request);
        }
        
        $startTime = microtime(true);
        
        // Realizar múltiples consultas al carrito
        for ($i = 0; $i < 50; $i++) {
            $result = $this->carritoService->getCart();
            $this->assertTrue($result['success']);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(3, $executionTime); // Debe completarse en menos de 3 segundos
        
        Log::info('Prueba de rendimiento: Consultas a la base de datos', [
            'consultas' => 50,
            'tiempo_ejecucion' => $executionTime
        ]);
    }

    /** @test */
    public function it_can_handle_stress_test()
    {
        $startTime = microtime(true);
        $successfulOperations = 0;
        $failedOperations = 0;
        
        // Prueba de estrés con múltiples usuarios y operaciones
        for ($i = 0; $i < 100; $i++) {
            $usuario = $this->usuarios->random();
            Auth::login($usuario);
            
            $producto = $this->productos->random();
            $variante = $this->variantes->where('producto_id', $producto->producto_id)->random();
            
            $request = new Request([
                'producto_id' => $producto->producto_id,
                'variante_id' => $variante->variante_id,
                'cantidad' => rand(1, 5)
            ]);
            
            try {
                $result = $this->carritoService->addToCart($request);
                if ($result['success']) {
                    $successfulOperations++;
                } else {
                    $failedOperations++;
                }
            } catch (\Exception $e) {
                $failedOperations++;
            }
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Verificar que la mayoría de operaciones fueron exitosas
        $this->assertGreaterThan(80, $successfulOperations); // Al menos 80% de éxito
        $this->assertLessThan(20, $failedOperations); // Menos del 20% de fallos
        $this->assertLessThan(20, $executionTime); // Debe completarse en menos de 20 segundos
        
        Log::info('Prueba de rendimiento: Prueba de estrés', [
            'operaciones_totales' => 100,
            'operaciones_exitosas' => $successfulOperations,
            'operaciones_fallidas' => $failedOperations,
            'tiempo_ejecucion' => $executionTime,
            'porcentaje_exito' => ($successfulOperations / 100) * 100
        ]);
    }

    protected function tearDown(): void
    {
        // Limpiar datos de prueba
        \App\Models\CarritoItem::truncate();
        \App\Models\Carrito::truncate();
        VarianteProducto::truncate();
        Producto::truncate();
        Usuario::truncate();
        Categoria::truncate();
        Marca::truncate();
        
        // Limpiar sesión
        Session::flush();
        
        parent::tearDown();
    }
}
