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

class CartSecurityTest extends TestCase
{
    protected $carritoService;
    protected $usuario;
    protected $producto;
    protected $variante;

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

        // Crear usuario
        $this->usuario = Usuario::create([
            'nombre_usuario' => 'Usuario Seguridad',
            'correo_electronico' => 'seguridad@example.com',
            'contrasena' => Hash::make('password123'),
            'telefono' => '1234567890',
            'estado' => true,
            'rol' => 'cliente',
            'fecha_registro' => now(),
            'email_verified_at' => now()
        ]);

        // Crear producto
        $this->producto = Producto::create([
            'nombre_producto' => 'Producto Seguridad',
            'descripcion' => 'Descripción del producto seguridad',
            'precio' => 100.00,
            'stock' => 10,
            'estado' => 'nuevo',
            'activo' => true,
            'categoria_id' => $categoria->categoria_id,
            'marca_id' => $marca->marca_id
        ]);

        // Crear variante
        $this->variante = VarianteProducto::create([
            'producto_id' => $this->producto->producto_id,
            'nombre' => 'Variante Seguridad',
            'codigo_color' => '#FF0000',
            'descripcion' => 'Descripción de variante seguridad',
            'precio_adicional' => 10.00,
            'stock' => 5,
            'disponible' => true,
            'sku' => 'VAR001'
        ]);
    }

    /** @test */
    public function it_cannot_add_product_with_negative_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => -1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad mínima es 1');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_zero_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 0
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad mínima es 1');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_excessive_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 101 // Más que el máximo permitido (100)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad máxima es 100');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_without_product_id()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'cantidad' => 1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: El ID del producto es obligatorio');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_without_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad es obligatoria');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_invalid_product_id()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => 'invalid_id',
            'cantidad' => 1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: El producto no existe');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_invalid_variant_id()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'variante_id' => 'invalid_variant',
            'cantidad' => 1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La variante del producto no existe');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_non_numeric_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 'not_a_number'
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad debe ser un número entero');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_update_cart_item_with_negative_quantity()
    {
        Auth::login($this->usuario);

        // Primero agregar un producto
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request);

        // Obtener el item ID
        $carrito = \App\Models\Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $item = \App\Models\CarritoItem::where('carrito_id', $carrito->id)->first();

        // Intentar actualizar con cantidad negativa
        $updateRequest = new Request(['cantidad' => -1]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad mínima es 1');
        
        $this->carritoService->updateCartItem($item->id, $updateRequest);
    }

    /** @test */
    public function it_cannot_update_cart_item_with_zero_quantity()
    {
        Auth::login($this->usuario);

        // Primero agregar un producto
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request);

        // Obtener el item ID
        $carrito = \App\Models\Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $item = \App\Models\CarritoItem::where('carrito_id', $carrito->id)->first();

        // Intentar actualizar con cantidad cero
        $updateRequest = new Request(['cantidad' => 0]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad mínima es 1');
        
        $this->carritoService->updateCartItem($item->id, $updateRequest);
    }

    /** @test */
    public function it_cannot_update_cart_item_with_excessive_quantity()
    {
        Auth::login($this->usuario);

        // Primero agregar un producto
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request);

        // Obtener el item ID
        $carrito = \App\Models\Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $item = \App\Models\CarritoItem::where('carrito_id', $carrito->id)->first();

        // Intentar actualizar con cantidad excesiva
        $updateRequest = new Request(['cantidad' => 101]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad máxima es 100');
        
        $this->carritoService->updateCartItem($item->id, $updateRequest);
    }

    /** @test */
    public function it_cannot_update_cart_item_without_quantity()
    {
        Auth::login($this->usuario);

        // Primero agregar un producto
        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 2
        ]);
        $this->carritoService->addToCart($request);

        // Obtener el item ID
        $carrito = \App\Models\Carrito::where('usuario_id', $this->usuario->usuario_id)->first();
        $item = \App\Models\CarritoItem::where('carrito_id', $carrito->id)->first();

        // Intentar actualizar sin cantidad
        $updateRequest = new Request([]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad es obligatoria');
        
        $this->carritoService->updateCartItem($item->id, $updateRequest);
    }

    /** @test */
    public function it_cannot_update_nonexistent_cart_item()
    {
        Auth::login($this->usuario);

        $updateRequest = new Request(['cantidad' => 5]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No query results for model [App\Models\CarritoItem].');
        
        $this->carritoService->updateCartItem(99999, $updateRequest);
    }

    /** @test */
    public function it_cannot_remove_nonexistent_cart_item()
    {
        Auth::login($this->usuario);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No query results for model [App\Models\CarritoItem].');
        
        $this->carritoService->removeFromCart(99999);
    }

    /** @test */
    public function it_cannot_add_product_with_xss_in_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => '<script>alert("xss")</script>'
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad debe ser un número entero');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_sql_injection_in_product_id()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => "1'; DROP TABLE productos; --",
            'cantidad' => 1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: El producto no existe');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_sql_injection_in_variant_id()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'variante_id' => "1'; DROP TABLE variantes_producto; --",
            'cantidad' => 1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La variante del producto no existe');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_extremely_large_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 999999999
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad máxima es 100');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_decimal_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 1.5
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad debe ser un número entero');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_string_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => 'abc'
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad debe ser un número entero');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_array_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => [1, 2, 3]
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad debe ser un número entero');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_null_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => null
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad es obligatoria');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_empty_string_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => ''
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Datos inválidos: La cantidad es obligatoria');
        
        $this->carritoService->addToCart($request);
    }

    /** @test */
    public function it_cannot_add_product_with_boolean_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => true
        ]);

        // Boolean true se convierte a 1, que es válido
        $result = $this->carritoService->addToCart($request);
        
        $this->assertTrue($result['success']);
    }

    /** @test */
    public function it_cannot_add_product_with_object_quantity()
    {
        Auth::login($this->usuario);

        $request = new Request([
            'producto_id' => $this->producto->producto_id,
            'cantidad' => new \stdClass()
        ]);

        $this->expectException(\TypeError::class);
        
        $this->carritoService->addToCart($request);
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
