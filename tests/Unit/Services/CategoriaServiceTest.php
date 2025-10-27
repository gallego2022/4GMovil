<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CategoriaService;
use App\Interfaces\CategoriaRepositoryInterface;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class CategoriaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $categoriaService;
    protected $categoriaRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app['config']->set('session.driver', 'array');
        
        // Crear mock del repositorio
        $this->categoriaRepository = Mockery::mock(CategoriaRepositoryInterface::class);
        $this->categoriaService = new CategoriaService($this->categoriaRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test: Obtener todas las categorías
     */
    public function test_get_all_categorias()
    {
        // Arrange
        $categorias = Categoria::hydrate([
            ['categoria_id' => 1, 'nombre' => 'Electrónica'],
            ['categoria_id' => 2, 'nombre' => 'Ropa'],
            ['categoria_id' => 3, 'nombre' => 'Hogar']
        ]);
        
        $this->categoriaRepository
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($categorias);

        // Act
        $result = $this->categoriaService->getAllCategorias();

        // Assert
        $this->assertCount(3, $result);
        $this->assertEquals($categorias, $result);
    }

    /**
     * Test: Obtener categoría por ID existente
     */
    public function test_get_categoria_by_id_existente()
    {
        // Arrange
        $categoria = Categoria::hydrate([['categoria_id' => 1, 'nombre' => 'Electrónica']])->first();
        
        $this->categoriaRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($categoria);

        // Act
        $result = $this->categoriaService->getCategoriaById(1);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('categoria', $result);
        $this->assertEquals('Electrónica', $result['categoria']->nombre);
    }

    /**
     * Test: Obtener categoría por ID inexistente
     */
    public function test_get_categoria_by_id_inexistente()
    {
        // Arrange
        $this->categoriaRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        // Act
        $result = $this->categoriaService->getCategoriaById(999);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Test: Crear categoría exitosamente
     */
    public function test_create_categoria_exitoso()
    {
        // Arrange
        $data = ['nombre' => 'Nueva Categoría'];
        $categoria = Categoria::hydrate([['categoria_id' => 1, 'nombre' => 'Nueva Categoría']])->first();
        
        $this->categoriaRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($categoria);

        // Act
        $result = $this->categoriaService->createCategoria($data);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('creada', $result['message']);
        $this->assertArrayHasKey('categoria', $result);
    }

    /**
     * Test: Crear categoría con error
     */
    public function test_create_categoria_con_error()
    {
        // Arrange
        $data = ['nombre' => 'Nueva Categoría'];
        
        $this->categoriaRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andThrow(new \Exception('Error de base de datos'));

        // Act
        $result = $this->categoriaService->createCategoria($data);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al crear', $result['message']);
    }

    /**
     * Test: Actualizar categoría exitosamente
     */
    public function test_update_categoria_exitoso()
    {
        // Arrange
        $id = 1;
        $data = ['nombre' => 'Categoría Actualizada'];
        
        $this->categoriaRepository
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andReturn(true);

        // Act
        $result = $this->categoriaService->updateCategoria($id, $data);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('actualizada', $result['message']);
    }

    /**
     * Test: Actualizar categoría inexistente
     */
    public function test_update_categoria_inexistente()
    {
        // Arrange
        $id = 999;
        $data = ['nombre' => 'Categoría Inexistente'];
        
        $this->categoriaRepository
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andReturn(false);

        // Act
        $result = $this->categoriaService->updateCategoria($id, $data);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('No se encontró', $result['message']);
    }

    /**
     * Test: Actualizar categoría con error
     */
    public function test_update_categoria_con_error()
    {
        // Arrange
        $id = 1;
        $data = ['nombre' => 'Categoría Con Error'];
        
        $this->categoriaRepository
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andThrow(new \Exception('Error de base de datos'));

        // Act
        $result = $this->categoriaService->updateCategoria($id, $data);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al actualizar', $result['message']);
    }

    /**
     * Test: Eliminar categoría exitosamente
     */
    public function test_delete_categoria_exitoso()
    {
        // Arrange
        $id = 1;
        
        $this->categoriaRepository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);

        // Act
        $result = $this->categoriaService->deleteCategoria($id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('eliminada', $result['message']);
    }

    /**
     * Test: Eliminar categoría inexistente
     */
    public function test_delete_categoria_inexistente()
    {
        // Arrange
        $id = 999;
        
        $this->categoriaRepository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(false);

        // Act
        $result = $this->categoriaService->deleteCategoria($id);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('No se encontró', $result['message']);
    }

    /**
     * Test: Eliminar categoría con error
     */
    public function test_delete_categoria_con_error()
    {
        // Arrange
        $id = 1;
        
        $this->categoriaRepository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andThrow(new \Exception('Error de base de datos'));

        // Act
        $result = $this->categoriaService->deleteCategoria($id);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al eliminar', $result['message']);
    }
}

