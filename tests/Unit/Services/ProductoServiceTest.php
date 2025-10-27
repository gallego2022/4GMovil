<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProductoService;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\VarianteProducto;
use App\Models\MovimientoInventario;
use App\Interfaces\ProductoRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $productoService;
    protected $productoRepository;
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

        // Crear mock del repositorio
        $this->productoRepository = $this->createMock(ProductoRepositoryInterface::class);
        
        // Inicializar servicio
        $this->productoService = new ProductoService($this->productoRepository);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_all_products()
    {
        // Arrange: Configurar el repositorio mock
        $products = collect([
            Producto::make(['producto_id' => 1, 'nombre_producto' => 'Producto 1']),
            Producto::make(['producto_id' => 2, 'nombre_producto' => 'Producto 2'])
        ]);

        $this->productoRepository
            ->expects($this->once())
            ->method('getAllWithRelations')
            ->willReturn(\Illuminate\Database\Eloquent\Collection::make($products));

        // Act
        $result = $this->productoService->getAllProducts();

        // Assert
        $this->assertCount(2, $result);
    }

    /** @test */
    public function it_can_create_a_product_without_variants()
    {
        // Arrange: Configurar datos de producto
        $data = [
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ];

        // Mock del producto creado
        $productoMock = Producto::make($data);
        $productoMock->producto_id = 1;

        // Configurar el repositorio mock
        $this->productoRepository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function($arg) use ($data) {
                return $arg['nombre_producto'] === $data['nombre_producto'] &&
                       $arg['precio'] === $data['precio'] &&
                       $arg['stock'] === $data['stock'];
            }))
            ->willReturn($productoMock);

        // Act
        $result = $this->productoService->createProduct($data);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Producto creado correctamente.', $result['message']);
    }

    /** @test */
    public function it_can_create_a_product_with_variants()
    {
        // Arrange: Configurar datos de producto con variantes
        $data = [
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ];

        $variantes = [
            [
                'nombre' => 'Rojo',
                'codigo_color' => '#FF0000',
                'stock' => 5,
                'precio_adicional' => 0
            ],
            [
                'nombre' => 'Azul',
                'codigo_color' => '#0000FF',
                'stock' => 3,
                'precio_adicional' => 10
            ]
        ];

        // Mock del producto creado
        $productoMock = Producto::make($data);
        $productoMock->producto_id = 1;

        // Configurar el repositorio mock
        $this->productoRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($productoMock);

        // Act
        $result = $this->productoService->createProduct($data, [], $variantes);

        // Assert
        $this->assertTrue($result['success']);
        
        // Verificar que se crearon las variantes
        $variantesCount = VarianteProducto::where('producto_id', 1)->count();
        $this->assertEquals(2, $variantesCount);
    }

    /** @test */
    public function it_cannot_create_product_without_required_fields()
    {
        // Arrange: Datos incompletos (sin todos los campos requeridos)
        $data = [
            'nombre_producto' => 'Producto Test',
            'precio' => 100.00
            // Faltan campos requeridos: stock, estado, categoria_id, marca_id
        ];

        // Configurar el repositorio mock para que se llame solo una vez
        $this->productoRepository
            ->expects($this->never())
            ->method('create');

        // Act
        try {
            $result = $this->productoService->createProduct($data);
            
            // Assert: Si no se lanzó excepción, debe retornar success = false
            $this->assertFalse($result['success']);
        } catch (\InvalidArgumentException $e) {
            // Esto es correcto, esperamos que se lance la excepción
            $this->assertStringContainsString('El campo', $e->getMessage());
        }
    }

    /** @test */
    public function it_can_update_a_product()
    {
        // Arrange: Crear producto
        $producto = Producto::create([
            'nombre_producto' => 'Producto Original',
            'descripcion' => 'Descripción original',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        $updateData = [
            'nombre_producto' => 'Producto Actualizado',
            'descripcion' => 'Descripción actualizada',
            'precio' => 150.00,
            'stock' => 15,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ];

        // Mock del repositorio
        $this->productoRepository
            ->expects($this->once())
            ->method('findById')
            ->with($producto->producto_id)
            ->willReturn($producto);

        $this->productoRepository
            ->expects($this->once())
            ->method('update')
            ->with($producto->producto_id, $this->callback(function($arg) {
                return $arg['nombre_producto'] === 'Producto Actualizado';
            }))
            ->willReturn(true);

        // Act
        $result = $this->productoService->updateProduct($producto->producto_id, $updateData);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Producto actualizado correctamente.', $result['message']);
    }

    /** @test */
    public function it_cannot_update_nonexistent_product()
    {
        // Arrange
        $updateData = [
            'nombre_producto' => 'Producto Actualizado',
            'precio' => 150.00,
            'stock' => 15,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ];

        // Mock del repositorio
        $this->productoRepository
            ->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        // Act
        $result = $this->productoService->updateProduct(999, $updateData);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals('No se encontró el producto.', $result['message']);
    }

    /** @test */
    public function it_can_delete_a_product()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto a Eliminar',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Mock del repositorio
        $this->productoRepository
            ->expects($this->once())
            ->method('delete')
            ->with($producto->producto_id)
            ->willReturn(true);

        // Act
        $result = $this->productoService->deleteProduct($producto->producto_id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Producto eliminado correctamente.', $result['message']);
    }

    /** @test */
    public function it_cannot_delete_nonexistent_product()
    {
        // Arrange: Mock del repositorio
        $this->productoRepository
            ->expects($this->once())
            ->method('delete')
            ->with(999)
            ->willReturn(false);

        // Act
        $result = $this->productoService->deleteProduct(999);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals('No se encontró el producto.', $result['message']);
    }

    /** @test */
    public function it_can_get_product_by_id()
    {
        // Arrange: Crear producto con variantes
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Agregar variantes
        $variante1 = VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Rojo',
            'codigo_color' => '#FF0000',
            'stock' => 5,
            'precio_adicional' => 0
        ]);

        $variante2 = VarianteProducto::create([
            'producto_id' => $producto->producto_id,
            'nombre' => 'Azul',
            'codigo_color' => '#0000FF',
            'stock' => 3,
            'precio_adicional' => 10
        ]);

        // Mock del repositorio
        $this->productoRepository
            ->expects($this->once())
            ->method('findById')
            ->with($producto->producto_id)
            ->willReturn($producto);

        // Act
        $result = $this->productoService->getProductById($producto->producto_id);

        // Assert
        $this->assertNotNull($result);
        $this->assertArrayHasKey('producto', $result);
        $this->assertArrayHasKey('categorias', $result);
        $this->assertArrayHasKey('marcas', $result);
    }

    /** @test */
    public function it_returns_null_when_getting_nonexistent_product()
    {
        // Arrange: Mock del repositorio
        $this->productoRepository
            ->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        // Act
        $result = $this->productoService->getProductById(999);

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function it_registers_movement_when_creating_product_with_stock()
    {
        // Arrange: Configurar datos de producto
        $data = [
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id,
            'costo_unitario' => 70.00
        ];

        // Mock del producto creado
        $productoMock = Producto::make($data);
        $productoMock->producto_id = 1;
        $productoMock->costo_unitario = 70.00;

        // Configurar el repositorio mock
        $this->productoRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($productoMock);

        // Act
        $result = $this->productoService->createProduct($data);

        // Assert
        $this->assertTrue($result['success']);
        
        // Verificar que se registró el movimiento de inventario
        $movimiento = MovimientoInventario::where('producto_id', 1)
            ->where('tipo_movimiento', 'entrada')
            ->first();
        
        $this->assertNotNull($movimiento);
        $this->assertEquals(10, $movimiento->cantidad);
    }

    /** @test */
    public function it_sets_default_values_for_stock_min_max_and_cost()
    {
        // Arrange: Datos sin stock_minimo, stock_maximo y costo_unitario
        $data = [
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ];

        // Mock del producto creado
        $productoMock = Producto::make($data);
        $productoMock->producto_id = 1;

        // Configurar el repositorio mock
        $this->productoRepository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function($arg) {
                // Verificar que se establecieron valores por defecto
                return $arg['stock_minimo'] === 5 &&
                       $arg['stock_maximo'] === 100 &&
                       $arg['costo_unitario'] === 70.00; // 70% de 100
            }))
            ->willReturn($productoMock);

        // Act
        $result = $this->productoService->createProduct($data);

        // Assert
        $this->assertTrue($result['success']);
    }
}

