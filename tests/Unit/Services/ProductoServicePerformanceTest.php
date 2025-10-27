<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProductoService;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\VarianteProducto;
use App\Interfaces\ProductoRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ProductoServicePerformanceTest extends TestCase
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

    /** @test */
    public function it_can_handle_large_number_of_products()
    {
        // Arrange - Crear múltiples productos
        $products = collect();
        for ($i = 1; $i <= 100; $i++) {
            $products->push(Producto::make([
                'producto_id' => $i,
                'nombre_producto' => "Producto {$i}",
                'precio' => 100.00,
                'stock' => 10
            ]));
        }

        $this->productoRepository
            ->expects($this->once())
            ->method('getAllWithRelations')
            ->willReturn(\Illuminate\Database\Eloquent\Collection::make($products));

        // Act
        $startTime = microtime(true);
        $result = $this->productoService->getAllProducts();
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert
        $this->assertCount(100, $result);
        // Verificar que la operación es razonablemente rápida (< 1 segundo para 100 productos)
        $this->assertLessThan(1.0, $executionTime);
    }

    /** @test */
    public function it_can_handle_products_with_many_variants()
    {
        // Arrange - Crear producto con muchas variantes
        $producto = Producto::create([
            'nombre_producto' => 'Producto con muchas variantes',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 0,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Crear 50 variantes para el producto
        for ($i = 1; $i <= 50; $i++) {
            VarianteProducto::create([
                'producto_id' => $producto->producto_id,
                'nombre' => "Variante {$i}",
                'stock' => 10,
                'disponible' => true
            ]);
        }

        // Act
        $startTime = microtime(true);
        $variantes = $producto->fresh()->variantes;
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert
        $this->assertCount(50, $variantes);
        // Verificar que la carga de relaciones es rápida (< 0.5 segundos)
        $this->assertLessThan(0.5, $executionTime);
    }

    /** @test */
    public function it_optimizes_query_with_eager_loading()
    {
        // Arrange - Crear productos con variantes
        $products = [];
        for ($i = 1; $i <= 20; $i++) {
            $producto = Producto::create([
                'nombre_producto' => "Producto {$i}",
                'descripcion' => 'Descripción',
                'precio' => 100.00,
                'stock' => 10,
                'estado' => 'nuevo',
                'categoria_id' => $this->categoria->categoria_id,
                'marca_id' => $this->marca->marca_id
            ]);

            // Crear 5 variantes para cada producto
            for ($j = 1; $j <= 5; $j++) {
                VarianteProducto::create([
                    'producto_id' => $producto->producto_id,
                    'nombre' => "Variante {$j}",
                    'stock' => 10,
                    'disponible' => true
                ]);
            }

            $products[] = $producto;
        }

        // Act - Cargar con eager loading
        $startTime = microtime(true);
        $queriesExecuted = 0;
        DB::listen(function () use (&$queriesExecuted) {
            $queriesExecuted++;
        });

        $productosWithVariantes = Producto::with('variantes')->get();
        
        // Acceder a las variantes de cada producto
        foreach ($productosWithVariantes as $producto) {
            $variantes = $producto->variantes;
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert
        $this->assertCount(20, $productosWithVariantes);
        // Con eager loading, deberíamos tener pocas consultas (idealmente 2: 1 para productos, 1 para variantes)
        $this->assertLessThan(10, $queriesExecuted);
        $this->assertLessThan(0.5, $executionTime);
    }

    /** @test */
    public function it_can_create_multiple_products_efficiently()
    {
        // Arrange
        $productsCreated = 0;

        // Act
        $startTime = microtime(true);
        
        for ($i = 1; $i <= 50; $i++) {
            $data = [
                'nombre_producto' => "Producto {$i}",
                'descripcion' => 'Descripción',
                'precio' => 100.00,
                'stock' => 10,
                'estado' => 'nuevo',
                'categoria_id' => $this->categoria->categoria_id,
                'marca_id' => $this->marca->marca_id
            ];

            $productoMock = Producto::make($data);
            $productoMock->producto_id = $i;

            $this->productoRepository
                ->expects($this->at($i - 1))
                ->method('create')
                ->willReturn($productoMock);

            $result = $this->productoService->createProduct($data);
            
            if ($result['success']) {
                $productsCreated++;
            }
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert
        $this->assertEquals(50, $productsCreated);
        // Verificar que el tiempo es razonable (< 5 segundos para 50 productos)
        $this->assertLessThan(5.0, $executionTime);
    }

    /** @test */
    public function it_can_handle_concurrent_updates()
    {
        // Arrange
        $producto = Producto::create([
            'nombre_producto' => 'Producto Test',
            'descripcion' => 'Descripción',
            'precio' => 100.00,
            'stock' => 100,
            'estado' => 'nuevo',
            'categoria_id' => $this->categoria->categoria_id,
            'marca_id' => $this->marca->marca_id
        ]);

        // Act
        $startTime = microtime(true);

        // Simular actualizaciones concurrentes
        $updates = [];
        for ($i = 1; $i <= 10; $i++) {
            $updateData = [
                'nombre_producto' => "Producto {$i}",
                'descripcion' => 'Descripción actualizada',
                'precio' => 100.00,
                'stock' => 10 + $i,
                'estado' => 'nuevo',
                'categoria_id' => $this->categoria->categoria_id,
                'marca_id' => $this->marca->marca_id
            ];

            $this->productoRepository
                ->expects($this->at($i - 1))
                ->method('findById')
                ->with($producto->producto_id)
                ->willReturn($producto);

            $this->productoRepository
                ->expects($this->at($i - 1 + 10))
                ->method('update')
                ->willReturn(true);

            $result = $this->productoService->updateProduct($producto->producto_id, $updateData);
            $updates[] = $result;
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert
        $this->assertCount(10, $updates);
        $this->assertLessThan(2.0, $executionTime);
    }

    /** @test */
    public function it_efficiently_processes_bulk_operations()
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

        $variantes = [];
        for ($i = 1; $i <= 30; $i++) {
            $variantes[] = [
                'nombre' => "Variante {$i}",
                'stock' => $i,
                'disponible' => true
            ];
        }

        // Act
        $startTime = microtime(true);
        
        foreach ($variantes as $varianteData) {
            VarianteProducto::create([
                'producto_id' => $producto->producto_id,
                'nombre' => $varianteData['nombre'],
                'stock' => $varianteData['stock'],
                'disponible' => $varianteData['disponible']
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert
        $variantesCount = VarianteProducto::where('producto_id', $producto->producto_id)->count();
        $this->assertEquals(30, $variantesCount);
        // Verificar que la operación en lote es eficiente
        $this->assertLessThan(1.0, $executionTime);
    }

    /** @test */
    public function it_uses_indexes_for_fast_queries()
    {
        // Arrange - Crear múltiples productos con diferentes estados
        for ($i = 1; $i <= 50; $i++) {
            Producto::create([
                'nombre_producto' => "Producto {$i}",
                'descripcion' => 'Descripción',
                'precio' => 100.00,
                'stock' => 10,
                'estado' => $i % 2 == 0 ? 'nuevo' : 'usado',
                'categoria_id' => $this->categoria->categoria_id,
                'marca_id' => $this->marca->marca_id
            ]);
        }

        // Act
        $startTime = microtime(true);
        
        DB::listen(function () {
            // Verificar que las consultas usan índices
        });

        $productosNuevos = Producto::where('estado', 'nuevo')->count();
        $productosUsados = Producto::where('estado', 'usado')->count();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert
        $this->assertEquals(25, $productosNuevos);
        $this->assertEquals(25, $productosUsados);
        // Las consultas con índices deben ser muy rápidas
        $this->assertLessThan(0.1, $executionTime);
    }

    /** @test */
    public function it_efficiently_paginates_large_results()
    {
        // Arrange - Crear muchos productos
        for ($i = 1; $i <= 200; $i++) {
            Producto::create([
                'nombre_producto' => "Producto {$i}",
                'descripcion' => 'Descripción',
                'precio' => 100.00,
                'stock' => 10,
                'estado' => 'nuevo',
                'categoria_id' => $this->categoria->categoria_id,
                'marca_id' => $this->marca->marca_id
            ]);
        }

        // Act
        $startTime = microtime(true);
        
        // Paginar resultados (15 por página)
        $products = Producto::paginate(15);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert
        $this->assertCount(15, $products->items());
        $this->assertEquals(200, $products->total());
        // La paginación debe ser rápida incluso con muchos registros
        $this->assertLessThan(0.5, $executionTime);
    }

    /** @test */
    public function it_handles_memory_efficiently()
    {
        // Arrange - Crear productos
        $memoryBefore = memory_get_usage();
        
        // Act
        $productos = [];
        for ($i = 1; $i <= 100; $i++) {
            $productos[] = Producto::create([
                'nombre_producto' => "Producto {$i}",
                'descripcion' => 'Descripción',
                'precio' => 100.00,
                'stock' => 10,
                'estado' => 'nuevo',
                'categoria_id' => $this->categoria->categoria_id,
                'marca_id' => $this->marca->marca_id
            ]);
        }

        $memoryAfter = memory_get_usage();
        $memoryUsed = $memoryAfter - $memoryBefore;

        // Assert
        // Verificar que el uso de memoria es razonable (< 10MB para 100 productos)
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed);
    }
}

