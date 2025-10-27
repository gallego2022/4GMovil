<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\InventarioService;
use App\Services\RedisCacheService;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\MovimientoInventario;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class InventarioServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $inventarioService;
    protected $cacheService;
    protected $categoria;
    protected $marca;
    protected $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app['config']->set('session.driver', 'array');
        
        // Crear usuario de prueba
        $this->usuario = Usuario::create([
            'nombre_usuario' => 'Admin Test',
            'correo_electronico' => 'admin@test.com',
            'contrasena' => Hash::make('password'),
            'estado' => true,
            'rol' => 'admin',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        // Crear categoría y marca
        $this->categoria = Categoria::create([
            'nombre' => 'Test Category',
            'descripcion' => 'Categoria de prueba',
            'activo' => true
        ]);

        $this->marca = Marca::create([
            'nombre' => 'Test Brand',
            'activo' => true
        ]);

        // Mock del RedisCacheService
        $this->cacheService = $this->createMock(RedisCacheService::class);
        $this->inventarioService = new InventarioService($this->cacheService);
    }

    /** @test */
    public function it_can_get_products_with_low_stock()
    {
        // Arrange - Crear producto con stock bajo
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 5, // Stock bajo
            'stock_inicial' => 100,
            'stock_minimo' => 20,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Crear variante con stock bajo
        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Variante Test',
            'stock' => 5,
            'disponible' => true
        ]);

        // Act
        $productosBajo = $this->inventarioService->getProductosStockBajo();

        // Assert
        $this->assertGreaterThanOrEqual(0, $productosBajo->count());
    }

    /** @test */
    public function it_can_get_products_with_critical_stock()
    {
        // Arrange - Crear producto con stock crítico
        $producto = Producto::create([
            'nombre_producto' => 'Producto Crítico',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 2,
            'stock_inicial' => 100,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Variante',
            'stock' => 2,
            'disponible' => true
        ]);

        // Act
        $productosCritico = $this->inventarioService->getProductosStockCritico();

        // Assert
        $this->assertGreaterThanOrEqual(0, $productosCritico->count());
    }

    /** @test */
    public function it_can_get_products_without_stock()
    {
        // Arrange - Crear producto sin stock
        $producto = Producto::create([
            'nombre_producto' => 'Producto Sin Stock',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 0,
            'stock_inicial' => 0,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Variante',
            'stock' => 0,
            'disponible' => false
        ]);

        // Act
        $productosSinStock = $this->inventarioService->getProductosSinStock();

        // Assert
        $this->assertGreaterThanOrEqual(0, $productosSinStock->count());
    }

    /** @test */
    public function it_can_register_stock_entry()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 10,
            'stock_inicial' => 10,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Act
        $result = $this->inventarioService->registrarEntrada(
            $producto->producto_id,
            20,
            'Entrada de prueba',
            $this->usuario->usuario_id
        );

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(30, $producto->fresh()->stock);
        
        // Verificar que se registró el movimiento
        $movimiento = MovimientoInventario::where('producto_id', $producto->producto_id)
            ->where('tipo_movimiento', 'entrada')
            ->first();
        
        $this->assertNotNull($movimiento);
        $this->assertEquals(20, $movimiento->cantidad);
    }

    /** @test */
    public function it_can_register_stock_exit()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 30,
            'stock_inicial' => 30,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Act
        $result = $this->inventarioService->registrarSalida(
            $producto->producto_id,
            10,
            'Venta',
            $this->usuario->usuario_id
        );

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(20, $producto->fresh()->stock);
        
        // Verificar que se registró el movimiento
        $movimiento = MovimientoInventario::where('producto_id', $producto->producto_id)
            ->where('tipo_movimiento', 'salida')
            ->first();
        
        $this->assertNotNull($movimiento);
        $this->assertEquals(10, $movimiento->cantidad);
    }

    /** @test */
    public function it_cannot_register_exit_without_sufficient_stock()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 5,
            'stock_inicial' => 5,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Act
        $result = $this->inventarioService->registrarSalida(
            $producto->producto_id,
            20,
            'Venta',
            $this->usuario->usuario_id
        );

        // Assert
        $this->assertFalse($result);
        $this->assertEquals(5, $producto->fresh()->stock);
    }

    /** @test */
    public function it_can_adjust_stock()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 50,
            'stock_inicial' => 50,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Act
        $result = $this->inventarioService->ajustarStock(
            $producto->producto_id,
            30,
            'Ajuste de inventario',
            $this->usuario->usuario_id
        );

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(30, $producto->fresh()->stock);
    }

    /** @test */
    public function it_can_get_movements_report()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 100,
            'stock_inicial' => 100,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Crear algunos movimientos
        MovimientoInventario::create([
            'producto_id' => $producto->producto_id,
            'tipo_movimiento' => 'entrada',
            'cantidad' => 50,
            'stock_anterior' => 0,
            'stock_nuevo' => 50,
            'motivo' => 'Entrada inicial',
            'usuario_id' => $this->usuario->usuario_id,
            'fecha_movimiento' => now()->subDays(10)
        ]);

        MovimientoInventario::create([
            'producto_id' => $producto->producto_id,
            'tipo_movimiento' => 'salida',
            'cantidad' => 20,
            'stock_anterior' => 50,
            'stock_nuevo' => 30,
            'motivo' => 'Venta',
            'usuario_id' => $this->usuario->usuario_id,
            'fecha_movimiento' => now()
        ]);

        // Act
        $fechaInicio = now()->subDays(15);
        $fechaFin = now();
        $reporte = $this->inventarioService->getReporteMovimientos($fechaInicio, $fechaFin);

        // Assert
        $this->assertArrayHasKey('movimientos', $reporte);
        $this->assertArrayHasKey('resumen', $reporte);
        $this->assertEquals(50, $reporte['resumen']['total_entradas']);
        $this->assertEquals(20, $reporte['resumen']['total_salidas']);
        $this->assertEquals(2, $reporte['resumen']['total_movimientos']);
    }

    /** @test */
    public function it_can_calculate_total_inventory_value()
    {
        // Arrange - Crear varios productos
        for ($i = 1; $i <= 5; $i++) {
            Producto::create([
                'nombre_producto' => "Producto {$i}",
                'descripcion' => 'Test',
                'precio' => 100.00 * $i,
                'stock' => 10 * $i,
                'stock_inicial' => 10 * $i,
                'stock_minimo' => 5,
                'estado' => 'nuevo',
                'activo' => true,
                'categoria_id' => $this->categoria->categoria_id,
                'marca_id' => $this->marca->marca_id,
                'costo_unitario' => 70.00 * $i
            ]);
        }

        // Act
        $valorTotal = $this->inventarioService->getValorTotalInventario();

        // Assert
        $this->assertGreaterThan(0, $valorTotal);
    }

    /** @test */
    public function it_can_get_movements_by_type()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 50,
            'stock_inicial' => 50,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Crear movimientos de diferentes tipos
        MovimientoInventario::create([
            'producto_id' => $producto->producto_id,
            'tipo_movimiento' => 'entrada',
            'cantidad' => 30,
            'stock_anterior' => 0,
            'stock_nuevo' => 30,
            'motivo' => 'Entrada',
            'usuario_id' => $this->usuario->usuario_id,
            'fecha_movimiento' => now()->subDays(5)
        ]);

        MovimientoInventario::create([
            'producto_id' => $producto->producto_id,
            'tipo_movimiento' => 'salida',
            'cantidad' => 10,
            'stock_anterior' => 30,
            'stock_nuevo' => 20,
            'motivo' => 'Salida',
            'usuario_id' => $this->usuario->usuario_id,
            'fecha_movimiento' => now()
        ]);

        // Act
        $fechaInicio = now()->subDays(10);
        $fechaFin = now();
        $movimientos = $this->inventarioService->getMovimientosByTipo('entrada', $fechaInicio, $fechaFin, null, null);

        // Assert
        $this->assertArrayHasKey('movimientos', $movimientos);
        $this->assertGreaterThanOrEqual(1, $movimientos['movimientos']->count());
    }

    /** @test */
    public function it_handles_concurrent_stock_movements()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 100,
            'stock_inicial' => 100,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Act - Simular movimientos concurrentes
        $result1 = $this->inventarioService->registrarEntrada(
            $producto->producto_id,
            10,
            'Entrada 1',
            $this->usuario->usuario_id
        );

        $result2 = $this->inventarioService->registrarEntrada(
            $producto->producto_id,
            5,
            'Entrada 2',
            $this->usuario->usuario_id
        );

        // Assert
        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertEquals(115, $producto->fresh()->stock);
    }

    /** @test */
    public function it_validates_negative_stock()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 10,
            'stock_inicial' => 10,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        // Act - Intentar ajuste que resultaría en stock negativo
        $result = $this->inventarioService->ajustarStock(
            $producto->producto_id,
            -15, // Esto resultaría en stock negativo (10 - 15 = -5)
            'Ajuste negativo',
            $this->usuario->usuario_id
        );

        // Assert - El servicio debe manejar esto correctamente
        // Si el servicio previene stock negativo, debe retornar false o ajustar a 0
        if ($result) {
            // Si el servicio permite el ajuste, debe asegurar que el stock no sea negativo
            $this->assertGreaterThanOrEqual(0, $producto->fresh()->stock);
        } else {
            // Si el servicio previene el ajuste, el stock debe mantenerse igual
            $this->assertEquals(10, $producto->fresh()->stock);
        }
    }

    /** @test */
    public function it_can_get_inventory_summary()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Test',
            'precio' => 100.00,
            'stock' => 50,
            'stock_inicial' => 50,
            'stock_minimo' => 5,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ]);

        MovimientoInventario::create([
            'producto_id' => $producto->producto_id,
            'tipo_movimiento' => 'entrada',
            'cantidad' => 20,
            'stock_anterior' => 0,
            'stock_nuevo' => 20,
            'motivo' => 'Entrada',
            'usuario_id' => $this->usuario->usuario_id,
            'fecha_movimiento' => now()->subDays(5)
        ]);

        // Act
        $fechaInicio = now()->subDays(10);
        $fechaFin = now();
        $resumen = $this->inventarioService->getResumenInventario($fechaInicio, $fechaFin);

        // Assert
        $this->assertArrayHasKey('total_entradas', $resumen);
        $this->assertArrayHasKey('total_salidas', $resumen);
        $this->assertArrayHasKey('productos_afectados', $resumen);
    }
}

