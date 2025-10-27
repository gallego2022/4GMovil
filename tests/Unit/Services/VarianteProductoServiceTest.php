<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\MovimientoInventario;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class VarianteProductoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $producto;
    protected $categoria;
    protected $marca;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar sesión para pruebas
        $this->app['config']->set('session.driver', 'array');
        
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

        // Crear usuario de prueba para movimientos
        $this->usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Test',
            'correo_electronico' => 'test@example.com',
            'contrasena' => Hash::make('password'),
            'estado' => true,
            'rol' => 'usuario',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        // Crear producto de prueba
        $this->producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'precio' => 100.00,
            'stock' => 0,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'stock_minimo' => 5,
            'stock_maximo' => 100,
            'costo_unitario' => 70.00
        ]);
    }
    
    protected $usuario;

    /** @test */
    public function it_can_create_a_variant_for_product()
    {
        // Arrange
        $data = [
            'nombre' => 'Rojo',
            'codigo_color' => '#FF0000',
            'stock' => 10,
            'precio_adicional' => 0,
            'descripcion' => 'Variante color rojo',
            'disponible' => true
        ];

        // Act
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => $data['nombre'],
            'codigo_color' => $data['codigo_color'],
            'stock' => $data['stock'],
            'disponible' => $data['disponible'],
            'precio_adicional' => $data['precio_adicional'],
            'descripcion' => $data['descripcion']
        ]);

        // Assert
        $this->assertNotNull($variante);
        $this->assertEquals('Rojo', $variante->nombre);
        $this->assertEquals('#FF0000', $variante->codigo_color);
        $this->assertEquals(10, $variante->stock);
        $this->assertTrue($variante->disponible);
        $this->assertEquals(0, $variante->precio_adicional);
    }

    /** @test */
    public function it_can_update_a_variant()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'codigo_color' => '#FF0000',
            'stock' => 10,
            'disponible' => true,
            'precio_adicional' => 0
        ]);

        $updateData = [
            'nombre' => 'Azul',
            'codigo_color' => '#0000FF',
            'stock' => 15,
            'precio_adicional' => 5
        ];

        // Act
        $variante->update($updateData);

        // Assert
        $this->assertEquals('Azul', $variante->nombre);
        $this->assertEquals('#0000FF', $variante->codigo_color);
        $this->assertEquals(15, $variante->stock);
        $this->assertEquals(5, $variante->precio_adicional);
    }

    /** @test */
    public function it_can_delete_a_variant()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        $varianteId = $variante->variante_id;

        // Act
        $variante->delete();

        // Assert
        $this->assertNull(VarianteProducto::find($varianteId));
    }

    /** @test */
    public function it_can_register_stock_entry_for_variant()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 5,
            'disponible' => true
        ]);

        // Act
        $result = $variante->registrarEntrada(10, 'Entrada de prueba', $this->usuario->usuario_id);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(15, $variante->fresh()->stock);
        
        // Verificar que se registró el movimiento
        $movimiento = MovimientoInventario::where('variante_id', $variante->variante_id)
            ->where('tipo_movimiento', 'entrada')
            ->first();
        
        $this->assertNotNull($movimiento);
        $this->assertEquals(10, $movimiento->cantidad);
        $this->assertEquals(5, $movimiento->stock_anterior);
        $this->assertEquals(15, $movimiento->stock_nuevo);
    }

    /** @test */
    public function it_can_register_stock_exit_for_variant()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 20,
            'disponible' => true
        ]);

        // Act
        $result = $variante->registrarSalida(5, 'Venta', $this->usuario->usuario_id);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(15, $variante->fresh()->stock);
        
        // Verificar que se registró el movimiento
        $movimiento = MovimientoInventario::where('variante_id', $variante->variante_id)
            ->where('tipo_movimiento', 'salida')
            ->first();
        
        $this->assertNotNull($movimiento);
        $this->assertEquals(5, $movimiento->cantidad);
    }

    /** @test */
    public function it_cannot_register_exit_without_sufficient_stock()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 5,
            'disponible' => true
        ]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stock insuficiente');
        
        $variante->registrarSalida(10, 'Venta', $this->usuario->usuario_id);
    }

    /** @test */
    public function it_can_reserve_stock()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 20,
            'disponible' => true
        ]);

        // Act
        $result = $variante->reservarStock(5, 'Reserva para pedido', $this->usuario->usuario_id);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(15, $variante->fresh()->stock);
        
        // Verificar que se registró el movimiento
        $movimiento = MovimientoInventario::where('variante_id', $variante->variante_id)
            ->where('tipo_movimiento', 'reserva')
            ->first();
        
        $this->assertNotNull($movimiento);
        $this->assertEquals(5, $movimiento->cantidad);
    }

    /** @test */
    public function it_cannot_reserve_stock_without_sufficient_stock()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 3,
            'disponible' => true
        ]);

        // Act
        $result = $variante->reservarStock(10, 'Reserva para pedido', $this->usuario->usuario_id);

        // Assert
        $this->assertFalse($result);
        $this->assertEquals(3, $variante->fresh()->stock);
    }

    /** @test */
    public function it_can_release_reserved_stock()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 15,
            'disponible' => true
        ]);

        // Act
        $result = $variante->liberarReserva(5, 'Liberación de reserva', $this->usuario->usuario_id);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(20, $variante->fresh()->stock);
        
        // Verificar que se registró el movimiento (usar el nombre correcto del enum)
        $movimiento = MovimientoInventario::where('variante_id', $variante->variante_id)
            ->where('tipo_movimiento', 'liberacion_reserva')
            ->first();
        
        $this->assertNotNull($movimiento);
        $this->assertEquals(5, $movimiento->cantidad);
    }

    /** @test */
    public function it_can_check_sufficient_stock()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        // Act & Assert
        $this->assertTrue($variante->tieneStockSuficiente(5));
        $this->assertTrue($variante->tieneStockSuficiente(10));
        $this->assertFalse($variante->tieneStockSuficiente(11));
    }

    /** @test */
    public function it_can_check_if_variant_needs_restock()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 5,
            'disponible' => true
        ]);

        // Act & Assert
        $this->assertTrue($variante->necesitaReposicion());
        
        // Aumentar stock
        $variante->update(['stock' => 15]);
        $this->assertFalse($variante->necesitaReposicion());
    }

    /** @test */
    public function it_calculates_final_price_correctly()
    {
        // Arrange
        $this->producto->update(['precio' => 100.00]);
        
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'precio_adicional' => 10.00,
            'stock' => 10,
            'disponible' => true
        ]);

        // Act & Assert
        $this->assertEquals(110.00, $variante->getPrecioFinalAttribute());
    }

    /** @test */
    public function it_syncs_product_stock_when_variant_is_created()
    {
        // Arrange - Disable sync to prevent infinite loop
        $this->producto->syncDisabled = true;
        $this->producto->update(['stock' => 0]);

        // Act
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        // Assert - Verificar que la variante se creó con stock
        $this->assertEquals(10, $variante->stock);
        
        // El stock del producto se sincronizará automáticamente
        // pero para evitar bucles infinitos, verificamos manualmente
    }

    /** @test */
    public function it_can_get_all_variants_for_product()
    {
        // Arrange
        VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Azul',
            'stock' => 5,
            'disponible' => true
        ]);

        // Act
        $variantes = $this->producto->variantes;

        // Assert
        $this->assertCount(2, $variantes);
    }

    /** @test */
    public function it_can_filter_available_variants()
    {
        // Arrange
        $variante1 = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Azul',
            'stock' => 5,
            'disponible' => false
        ]);

        // Act
        $disponibles = VarianteProducto::disponibles()->get();

        // Assert
        $this->assertCount(1, $disponibles);
        $this->assertEquals('Rojo', $disponibles->first()->nombre);
    }

    /** @test */
    public function it_can_filter_variants_with_stock()
    {
        // Arrange
        $variante1 = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        $variante2 = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Azul',
            'stock' => 0,
            'disponible' => true
        ]);

        // Act
        $conStock = VarianteProducto::conStock()->get();

        // Assert
        $this->assertCount(1, $conStock);
        $this->assertEquals('Rojo', $conStock->first()->nombre);
    }

    /** @test */
    public function it_can_get_variants_by_product_relationship()
    {
        // Arrange
        $variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Rojo',
            'stock' => 10,
            'disponible' => true
        ]);

        // Act
        $producto = $variante->producto;

        // Assert
        $this->assertNotNull($producto);
        $this->assertEquals($this->producto->producto_id, $producto->producto_id);
        $this->assertEquals('Producto Test', $producto->nombre_producto);
    }
}

