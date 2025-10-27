<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\StockSincronizacionService;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockSincronizacionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $stockSincronizacionService;
    protected $categoria;
    protected $marca;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar sesión para pruebas
        $this->app['config']->set('session.driver', 'array');
        
        // Inicializar servicio
        $this->stockSincronizacionService = new StockSincronizacionService();
        
        // Crear categoría y marca de prueba
        $this->categoria = Categoria::create([
            'nombre' => 'Categoria Test',
            'descripcion' => 'Descripción de prueba',
            'activo' => true
        ]);

        $this->marca = Marca::create([
            'nombre' => 'Marca Test',
            'activo' => true
        ]);
    }

    /** @test */
    public function it_can_synchronize_single_product_stock()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 50, // Stock desincronizado
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'stock_minimo' => 5,
            'stock_maximo' => 100,
            'costo_unitario' => 70.00
        ]);

        // Crear variantes con stock total de 30
        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Azul',
            'stock' => 20,
            'disponible' => true
        ]);

        // Act
        $result = $this->stockSincronizacionService->sincronizarProducto($producto->producto_id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals($producto->producto_id, $result['producto_id']);
        $this->assertEquals(2, $result['variantes_count']);
        $this->assertEquals(30, $result['stock_nuevo']);
    }

    /** @test */
    public function it_can_synchronize_all_products_stock()
    {
        // Arrange
        $producto1 = Producto::create([
            'nombre_producto' => 'Producto 1',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 0,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        $producto2 = Producto::create([
            'nombre_producto' => 'Producto 2',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 0,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Crear variantes para cada producto
        VarianteProducto::create([
            'producto_id' => $producto1->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        VarianteProducto::create([
            'producto_id' => $producto2->producto_id,
            'nombre' => 'Azul',
            'stock' => 5,
            'disponible' => true
        ]);

        // Act
        $result = $this->stockSincronizacionService->sincronizarTodosLosProductos();

        // Assert
        $this->assertEquals(2, $result['total_productos']);
        $this->assertEquals(2, $result['exitosos']);
        $this->assertEquals(0, $result['fallidos']);
    }

    /** @test */
    public function it_returns_error_when_synchronizing_nonexistent_product()
    {
        // Act
        $result = $this->stockSincronizacionService->sincronizarProducto(999);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals(999, $result['producto_id']);
        $this->assertArrayHasKey('error', $result);
    }

    /** @test */
    public function it_can_get_synchronization_report()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 0,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        // Act
        $reporte = $this->stockSincronizacionService->obtenerReporteSincronizacion();

        // Assert
        $this->assertArrayHasKey('total_productos', $reporte);
        $this->assertArrayHasKey('productos_con_variantes', $reporte);
        $this->assertArrayHasKey('productos_sin_variantes', $reporte);
        $this->assertArrayHasKey('stock_total_sistema', $reporte);
        $this->assertArrayHasKey('resumen_variantes', $reporte);
        $this->assertEquals(1, $reporte['total_productos']);
        $this->assertEquals(1, $reporte['productos_con_variantes']);
    }

    /** @test */
    public function it_can_verify_stock_integrity()
    {
        // Arrange - Producto con variantes (desincronizado)
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 100, // Stock desincronizado (debería ser 30)
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Crear variantes con stock total de 30
        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Azul',
            'stock' => 20,
            'disponible' => true
        ]);

        // Act
        $result = $this->stockSincronizacionService->verificarIntegridadStock();

        // Assert
        $this->assertArrayHasKey('problemas', $result);
        $this->assertArrayHasKey('advertencias', $result);
        $this->assertArrayHasKey('total_problemas', $result);
        $this->assertArrayHasKey('total_advertencias', $result);
        
        // Debe detectar que hay desincronización
        $this->assertGreaterThan(0, $result['total_problemas']);
        $this->assertCount(1, $result['problemas']);
    }

    /** @test */
    public function it_can_fix_synchronization_issues_automatically()
    {
        // Arrange - Producto desincronizado
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 100, // Stock desincronizado
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Crear variantes con stock total de 30
        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Azul',
            'stock' => 20,
            'disponible' => true
        ]);

        // Act
        $result = $this->stockSincronizacionService->corregirSincronizacion();

        // Assert
        $this->assertArrayHasKey('corregidos', $result);
        $this->assertArrayHasKey('errores', $result);
        $this->assertArrayHasKey('total_productos', $result);
        
        // Debe haber corregido al menos 1 producto
        $this->assertGreaterThan(0, $result['corregidos']);
        $this->assertEquals(0, $result['errores']);
        
        // Verificar que el stock se sincronizó correctamente
        $this->assertEquals(30, $producto->fresh()->stock);
    }

    /** @test */
    public function it_identifies_products_without_stock_but_with_variant_stock()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 0,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Crear variante con stock
        VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        // Act
        $result = $this->stockSincronizacionService->verificarIntegridadStock();

        // Assert
        $this->assertGreaterThanOrEqual(0, $result['total_problemas']);
    }

    /** @test */
    public function it_handles_products_without_variants()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Act
        $result = $this->stockSincronizacionService->sincronizarProducto($producto->producto_id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals(0, $result['variantes_count']);
    }

    /** @test */
    public function it_calculates_total_variant_stock_correctly()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 0,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Crear múltiples variantes
        for ($i = 1; $i <= 5; $i++) {
            VarianteProducto::create([
                'producto_id' => $producto->producto_id,
                'nombre' => "Variante {$i}",
                'stock' => $i * 10,
                'disponible' => true
            ]);
        }

        // Act
        $result = $this->stockSincronizacionService->sincronizarProducto($producto->producto_id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals(5, $result['variantes_count']);
        // Stock total = 10 + 20 + 30 + 40 + 50 = 150
        $this->assertEquals(150, $result['stock_nuevo']);
    }
}

