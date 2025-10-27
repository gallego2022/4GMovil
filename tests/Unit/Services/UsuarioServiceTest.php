<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\UsuarioService;
use App\Interfaces\UsuarioRepositoryInterface;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class UsuarioServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $usuarioService;
    protected $usuarioRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app['config']->set('session.driver', 'array');
        
        // Crear mock del repositorio
        $this->usuarioRepository = Mockery::mock(UsuarioRepositoryInterface::class);
        $this->usuarioService = new UsuarioService($this->usuarioRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test: Obtener todos los usuarios
     */
    public function test_get_all_users()
    {
        // Arrange
        $usuarios = Usuario::hydrate([
            [
                'usuario_id' => 1,
                'nombre_usuario' => 'Juan Pérez',
                'correo_electronico' => 'juan@test.com',
                'rol' => 'cliente',
                'estado' => true
            ],
            [
                'usuario_id' => 2,
                'nombre_usuario' => 'María González',
                'correo_electronico' => 'maria@test.com',
                'rol' => 'admin',
                'estado' => true
            ]
        ]);
        
        $this->usuarioRepository
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($usuarios);

        // Act
        $result = $this->usuarioService->getAllUsers();

        // Assert
        $this->assertCount(2, $result);
    }

    /**
     * Test: Obtener usuario por ID existente
     */
    public function test_get_user_by_id_existente()
    {
        // Arrange
        $usuario = Usuario::hydrate([[
            'usuario_id' => 1,
            'nombre_usuario' => 'Juan Pérez',
            'correo_electronico' => 'juan@test.com',
            'rol' => 'cliente',
            'estado' => true
        ]])->first();
        
        $this->usuarioRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($usuario);

        // Act
        $result = $this->usuarioService->getUserById(1);

        // Assert
        $this->assertEquals('Juan Pérez', $result->nombre_usuario);
    }

    /**
     * Test: Obtener usuario por ID inexistente
     */
    public function test_get_user_by_id_inexistente()
    {
        // Arrange
        $this->usuarioRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        // Act
        $result = $this->usuarioService->getUserById(999);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Test: Actualizar usuario exitosamente
     */
    public function test_update_user_exitoso()
    {
        // Arrange
        $id = 1;
        $data = [
            'nombre_usuario' => 'Juan Pérez Actualizado',
            'correo_electronico' => 'juan.actualizado@test.com',
            'telefono' => '1234567890',
            'rol' => 'cliente',
            'estado' => true
        ];
        
        $this->usuarioRepository
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andReturn(true);

        // Act
        $result = $this->usuarioService->updateUser($id, $data);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('actualizado', $result['message']);
    }

    /**
     * Test: Actualizar usuario con imagen de perfil
     */
    public function test_update_user_con_imagen_perfil()
    {
        // Arrange
        $id = 1;
        $data = [
            'nombre_usuario' => 'Juan Pérez',
            'correo_electronico' => 'juan@test.com',
            'telefono' => '1234567890',
            'rol' => 'cliente',
            'estado' => true
        ];
        $profileImage = Mockery::mock('Illuminate\Http\UploadedFile');
        
        $usuario = Usuario::hydrate([[
            'usuario_id' => 1,
            'nombre_usuario' => 'Juan Pérez',
            'correo_electronico' => 'juan@test.com'
        ]])->first();
        
        $this->usuarioRepository
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andReturn(true);
        
        $this->usuarioRepository
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($usuario);
        
        $this->usuarioRepository
            ->shouldReceive('updateProfileImage')
            ->once()
            ->with($usuario, $profileImage)
            ->andReturn(true);

        // Act
        $result = $this->usuarioService->updateUser($id, $data, $profileImage);

        // Assert
        $this->assertTrue($result['success']);
    }

    /**
     * Test: Actualizar usuario inexistente
     */
    public function test_update_user_inexistente()
    {
        // Arrange
        $id = 999;
        $data = [
            'nombre_usuario' => 'Usuario Inexistente',
            'correo_electronico' => 'inexistente@test.com',
            'telefono' => '1234567890',
            'rol' => 'cliente',
            'estado' => true
        ];
        
        $this->usuarioRepository
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andReturn(false);

        // Act
        $result = $this->usuarioService->updateUser($id, $data);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('No se encontró', $result['message']);
    }

    /**
     * Test: Actualizar usuario con error
     */
    public function test_update_user_con_error()
    {
        // Arrange
        $id = 1;
        $data = [
            'nombre_usuario' => 'Usuario Con Error',
            'correo_electronico' => 'error@test.com',
            'telefono' => '1234567890',
            'rol' => 'cliente',
            'estado' => true
        ];
        
        $this->usuarioRepository
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andThrow(new \Exception('Error de base de datos'));

        // Act
        $result = $this->usuarioService->updateUser($id, $data);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al actualizar', $result['message']);
    }

    /**
     * Test: Crear usuario exitosamente
     */
    public function test_create_user_exitoso()
    {
        // Arrange
        $data = [
            'nombre_usuario' => 'Nuevo Usuario',
            'correo_electronico' => 'nuevo@test.com',
            'contrasena' => 'password123',
            'telefono' => '0987654321',
            'rol' => 'cliente',
            'estado' => true
        ];
        
        $usuario = Usuario::hydrate([[
            'usuario_id' => 1,
            'nombre_usuario' => 'Nuevo Usuario',
            'correo_electronico' => 'nuevo@test.com',
            'rol' => 'cliente',
            'estado' => true
        ]])->first();
        
        $this->usuarioRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($usuario);

        // Act
        $result = $this->usuarioService->createUser($data);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('creado', $result['message']);
        $this->assertArrayHasKey('usuario', $result);
    }

    /**
     * Test: Crear usuario con error
     */
    public function test_create_user_con_error()
    {
        // Arrange
        $data = [
            'nombre_usuario' => 'Usuario Error',
            'correo_electronico' => 'error@test.com',
            'contrasena' => 'password123',
            'telefono' => '1234567890',
            'rol' => 'cliente',
            'estado' => true
        ];
        
        $this->usuarioRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andThrow(new \Exception('Error de base de datos'));

        // Act
        $result = $this->usuarioService->createUser($data);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al crear', $result['message']);
    }

    /**
     * Test: Eliminar usuario exitosamente
     */
    public function test_delete_user_exitoso()
    {
        // Arrange
        $id = 1;
        
        $this->usuarioRepository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);

        // Act
        $result = $this->usuarioService->deleteUser($id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('eliminado', $result['message']);
    }

    /**
     * Test: Eliminar usuario inexistente
     */
    public function test_delete_user_inexistente()
    {
        // Arrange
        $id = 999;
        
        $this->usuarioRepository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(false);

        // Act
        $result = $this->usuarioService->deleteUser($id);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('No se encontró', $result['message']);
    }

    /**
     * Test: Eliminar usuario con error
     */
    public function test_delete_user_con_error()
    {
        // Arrange
        $id = 1;
        
        $this->usuarioRepository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andThrow(new \Exception('Error de base de datos'));

        // Act
        $result = $this->usuarioService->deleteUser($id);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al eliminar', $result['message']);
    }

    /**
     * Test: Actualizar rol exitosamente
     */
    public function test_update_role_exitoso()
    {
        // Arrange
        $id = 1;
        $role = 'admin';
        
        $this->usuarioRepository
            ->shouldReceive('updateRole')
            ->once()
            ->with($id, $role)
            ->andReturn(true);

        // Act
        $result = $this->usuarioService->updateRole($id, $role);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Rol actualizado', $result['message']);
    }

    /**
     * Test: Actualizar rol de usuario inexistente
     */
    public function test_update_role_usuario_inexistente()
    {
        // Arrange
        $id = 999;
        $role = 'admin';
        
        $this->usuarioRepository
            ->shouldReceive('updateRole')
            ->once()
            ->with($id, $role)
            ->andReturn(false);

        // Act
        $result = $this->usuarioService->updateRole($id, $role);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('No se encontró', $result['message']);
    }

    /**
     * Test: Actualizar rol con error
     */
    public function test_update_role_con_error()
    {
        // Arrange
        $id = 1;
        $role = 'admin';
        
        $this->usuarioRepository
            ->shouldReceive('updateRole')
            ->once()
            ->with($id, $role)
            ->andThrow(new \Exception('Error de base de datos'));

        // Act
        $result = $this->usuarioService->updateRole($id, $role);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al actualizar', $result['message']);
    }

    /**
     * Test: Cambiar estado del usuario exitosamente
     */
    public function test_toggle_user_status_exitoso()
    {
        // Arrange
        $id = 1;
        
        $this->usuarioRepository
            ->shouldReceive('toggleStatus')
            ->once()
            ->with($id)
            ->andReturn(true);

        // Act
        $result = $this->usuarioService->toggleUserStatus($id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Estado del usuario actualizado', $result['message']);
    }

    /**
     * Test: Cambiar estado de usuario inexistente
     */
    public function test_toggle_user_status_inexistente()
    {
        // Arrange
        $id = 999;
        
        $this->usuarioRepository
            ->shouldReceive('toggleStatus')
            ->once()
            ->with($id)
            ->andReturn(false);

        // Act
        $result = $this->usuarioService->toggleUserStatus($id);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('No se encontró', $result['message']);
    }

    /**
     * Test: Cambiar estado del usuario con error
     */
    public function test_toggle_user_status_con_error()
    {
        // Arrange
        $id = 1;
        
        $this->usuarioRepository
            ->shouldReceive('toggleStatus')
            ->once()
            ->with($id)
            ->andThrow(new \Exception('Error de base de datos'));

        // Act
        $result = $this->usuarioService->toggleUserStatus($id);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al cambiar el estado', $result['message']);
    }
}

