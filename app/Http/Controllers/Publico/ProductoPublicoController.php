<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Base\WebController;
use App\Services\Catalog\ProductoService;
use App\Models\Resena;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Exception;

class ProductoPublicoController extends WebController
{
    protected $productoService;

    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    /**
     * Mostrar un producto específico (público)
     */
    public function show(Request $request, $id)
    {
        try {
            $this->applyLocalization();
            // Log para debug
            Log::info('Método show público llamado', [
                'id_original' => $id,
                'tipo_id' => gettype($id),
                'url_completa' => $request->fullUrl(),
                'referer' => $request->header('referer'),
                'user_agent' => $request->header('user-agent')
            ]);
            
            // Convertir el ID a entero ya que viene como string desde la ruta
            $id = (int) $id;
            
            // Log después de la conversión
            Log::info('ID convertido', [
                'id_convertido' => $id,
                'tipo_id_convertido' => gettype($id)
            ]);
            
            $data = $this->productoService->getProductById($id);
            
            if (!$data || !$data['success'] || !isset($data['data'])) {
                Log::warning('Producto no encontrado en show público', [
                    'id' => $id,
                    'data' => $data
                ]);
                abort(404, 'Producto no encontrado');
            }

            $producto = $data['data'];
            
            // Cargar relaciones necesarias incluyendo solo reseñas públicas (sin pedido_id)
            $producto->load(['categoria', 'marca', 'imagenes', 'variantes.imagenes']);
            
            // Cargar solo reseñas públicas (sin pedido_id) para mostrar en el show del producto
            $producto->load(['resenas' => function($query) {
                $query->whereNull('pedido_id')
                      ->where('activa', true)
                      ->with('usuario');
            }]);
            
            // Obtener productos relacionados (misma categoría y marca)
            // Solo cargar reseñas públicas (sin pedido_id)
            $productosRelacionados = Producto::with(['categoria', 'marca', 'imagenes', 'resenas' => function($query) {
                $query->whereNull('pedido_id')
                      ->where('activa', true);
            }])
                ->where('producto_id', '!=', $producto->producto_id) // Excluir el producto actual
                ->where('estado', 'nuevo') // Solo productos activos
                ->where('stock', '>', 0) // Solo productos con stock
                ->where(function($query) use ($producto) {
                    $query->where('categoria_id', $producto->categoria_id) // Misma categoría
                          ->orWhere('marca_id', $producto->marca_id); // O misma marca
                })
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();
            
            // Si no hay suficientes productos relacionados, agregar productos de la misma categoría
            if ($productosRelacionados->count() < 4) {
                $productosAdicionales = Producto::with(['categoria', 'marca', 'imagenes', 'resenas' => function($query) {
                    $query->whereNull('pedido_id')
                          ->where('activa', true);
                }])
                    ->where('producto_id', '!=', $producto->producto_id)
                    ->where('estado', 'nuevo')
                    ->where('stock', '>', 0)
                    ->where('categoria_id', $producto->categoria_id)
                    ->whereNotIn('producto_id', $productosRelacionados->pluck('producto_id'))
                    ->orderBy('created_at', 'desc')
                    ->limit(4 - $productosRelacionados->count())
                    ->get();
                
                $productosRelacionados = $productosRelacionados->merge($productosAdicionales);
            }
            
            // Si aún no hay suficientes, agregar productos populares
            if ($productosRelacionados->count() < 4) {
                $productosPopulares = Producto::with(['categoria', 'marca', 'imagenes', 'resenas' => function($query) {
                    $query->whereNull('pedido_id')
                          ->where('activa', true);
                }])
                    ->where('producto_id', '!=', $producto->producto_id)
                    ->where('estado', 'nuevo')
                    ->where('stock', '>', 0)
                    ->whereNotIn('producto_id', $productosRelacionados->pluck('producto_id'))
                    ->orderBy('created_at', 'desc')
                    ->limit(4 - $productosRelacionados->count())
                    ->get();
                
                $productosRelacionados = $productosRelacionados->merge($productosPopulares);
            }
            
            return View::make('productos.show', compact('producto', 'productosRelacionados'));
            
        } catch (Exception $e) {
            Log::error('Error al mostrar producto público', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Error al cargar el producto');
        }
    }

    /**
     * Crear una nueva reseña para un producto
     */
    public function storeResena(Request $request, $productoId)
    {
        try {
            // Validar que el producto existe
            $producto = Producto::findOrFail($productoId);
            
            // Validar los datos de la reseña
            $validationRules = [
                'calificacion' => 'required|integer|min:1|max:5',
                'comentario' => 'required|string|min:3|max:1000',
            ];

            $validationMessages = [
                // Mensajes para calificación
                'calificacion.required' => '⚠️ Debes seleccionar una calificación',
                'calificacion.integer' => '❌ La calificación debe ser un número válido',
                'calificacion.min' => '⭐ La calificación mínima es 1 estrella',
                'calificacion.max' => '⭐ La calificación máxima es 5 estrellas',
                
                // Mensajes para comentario
                'comentario.required' => '📝 Debes escribir un comentario',
                'comentario.string' => '❌ El comentario debe ser un texto válido',
                'comentario.min' => '📝 El comentario debe tener al menos 3 letras',
                'comentario.max' => '📝 El comentario no puede exceder 1000 caracteres',
                
                // Mensajes para nombre de usuario (cuando no está autenticado)
                'nombre_usuario.required' => '👤 Debes ingresar tu nombre',
                'nombre_usuario.string' => '❌ El nombre debe ser un texto válido',
                'nombre_usuario.min' => '👤 El nombre debe tener al menos 2 caracteres',
                'nombre_usuario.max' => '👤 El nombre no puede exceder 100 caracteres',
            ];
            // Si no hay usuario autenticado, requerir nombre
            if (!Auth::check()) {
                $validationRules['nombre_usuario'] = 'required|string|min:2|max:100';
            }

            $request->validate($validationRules, $validationMessages);

            // Si hay usuario autenticado, usar su ID
            $usuarioId = null;
            if (Auth::check()) {
                $usuarioId = Auth::id();
            } else {
                // Si no hay usuario autenticado, crear o usar un usuario anónimo
                $usuarioAnonimo = Usuario::firstOrCreate(
                    ['correo_electronico' => 'anonimo@example.com'],
                    [
                        'nombre_usuario' => $request->nombre_usuario,
                        'correo_electronico' => 'anonimo@example.com',
                        'contrasena' => Hash::make('password'),
                        'rol' => 'cliente',
                        'estado' => true,
                        'fecha_registro' => Carbon::now()
                    ]
                );
                $usuarioId = $usuarioAnonimo->usuario_id;
            }

            // Buscar reseña pública existente del usuario para este producto (sin pedido_id)
            // Las reseñas desde el show de productos son públicas y no tienen pedido_id
            $resenaExistente = Resena::where('usuario_id', $usuarioId)
                ->where('producto_id', $productoId)
                ->whereNull('pedido_id') // Solo reseñas públicas
                ->first();

            if ($resenaExistente) {
                // Actualizar reseña pública existente
                $resenaExistente->update([
                    'calificacion' => $request->calificacion,
                    'comentario' => $request->comentario,
                    'activa' => true,
                    'verificada' => false, // Resetear verificación al actualizar
                    'pedido_id' => null, // Asegurar que sea pública
                ]);
                $resena = $resenaExistente;
                $esActualizacion = true;
            } else {
                // Verificar cuántas reseñas públicas tiene el usuario para este producto
                $resenasExistentes = Resena::where('usuario_id', $usuarioId)
                    ->where('producto_id', $productoId)
                    ->whereNull('pedido_id') // Solo contar reseñas públicas
                    ->count();

                // Permitir hasta 2 reseñas públicas por usuario/producto
                if ($resenasExistentes >= 2) {
                    return Response::json([
                        'success' => false,
                        'message' => '⚠️ Ya has escrito el máximo de 2 reseñas públicas para este producto',
                        'error_type' => 'limit_reached',
                        'suggestion' => 'Solo puedes escribir hasta 2 reseñas públicas por producto'
                    ], 422);
                }

                // Crear nueva reseña pública (sin pedido_id)
                $resenaData = [
                    'usuario_id' => $usuarioId,
                    'producto_id' => $productoId,
                    'pedido_id' => null, // Reseña pública, no asociada a un pedido
                    'calificacion' => $request->calificacion,
                    'comentario' => $request->comentario,
                    'verificada' => false,
                    'activa' => true,
                ];
                $resena = Resena::create($resenaData);
                $esActualizacion = false;
            }

            Log::info('Reseña ' . ($esActualizacion ? 'actualizada' : 'creada') . ' exitosamente', [
                'producto_id' => $productoId,
                'usuario_id' => $usuarioId,
                'calificacion' => $request->calificacion,
                'resena_id' => $resena->resena_id,
                'usuario_autenticado' => Auth::check(),
                'actualizada' => $esActualizacion ?? false
            ]);

            return Response::json([
                'success' => true,
                'message' => '¡Reseña enviada exitosamente!',
                'resena' => [
                    'id' => $resena->resena_id,
                    'calificacion' => $resena->calificacion,
                    'comentario' => $resena->comentario,
                    'fecha' => $resena->created_at->diffForHumans(),
                    'usuario' => [
                        'nombre' => $resena->usuario->nombre_usuario ?? 'Usuario',
                        'inicial' => strtoupper(substr($resena->usuario->nombre_usuario ?? 'U', 0, 1))
                    ]
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            Log::error('Producto no encontrado para reseña', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ]);
            
            return Response::json([
                'success' => false,
                'message' => '❌ Producto no encontrado',
                'error_type' => 'not_found',
                'suggestion' => 'Verifica que el producto existe y esté disponible'
            ], 404);

        } catch (ValidationException $e) {
            Log::error('Error de validación en reseña', [
                'producto_id' => $productoId,
                'errors' => $e->errors()
            ]);
            
            // Obtener el primer error para mostrar un mensaje principal
            $firstError = Collection::make($e->errors())->first()[0] ?? 'Hay errores en los datos ingresados';
            
            return Response::json([
                'success' => false,
                'message' => '❌ ' . $firstError,
                'errors' => $e->errors(),
                'error_type' => 'validation',
                'suggestions' => [
                    'calificacion' => 'Selecciona una calificación de 1 a 5 estrellas',
                    'comentario' => 'Escribe un comentario de al menos 3 letras',
                    'nombre_usuario' => 'Ingresa tu nombre completo',
                ]
            ], 422);

        } catch (QueryException $e) {
            Log::error('Error de base de datos al crear reseña', [
                'producto_id' => $productoId,
                'error' => $e->getMessage(),
                'sql' => $e->getSql() ?? null,
                'bindings' => $e->getBindings() ?? null,
            ]);
            
            // Verificar si es un error de restricción única
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                return Response::json([
                    'success' => false,
                    'message' => '⚠️ Ya has enviado una reseña para este producto. Puedes actualizarla desde tu perfil.',
                    'error_type' => 'duplicate',
                    'suggestion' => 'Si deseas modificar tu reseña, puedes actualizarla desde tu cuenta'
                ], 409);
            }
            
            return Response::json([
                'success' => false,
                'message' => '❌ Error al guardar la reseña',
                'error_type' => 'database_error',
                'suggestion' => 'Por favor, intenta nuevamente en unos minutos',
                'debug_info' => Config::get('app.debug') ? $e->getMessage() : null
            ], 500);
            
        } catch (Exception $e) {
            Log::error('Error al crear reseña', [
                'producto_id' => $productoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return Response::json([
                'success' => false,
                'message' => '❌ Error interno del servidor',
                'error_type' => 'server_error',
                'suggestion' => 'Por favor, intenta nuevamente en unos minutos',
                'debug_info' => Config::get('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Formatear mensajes de error para mejor presentación
     */
    private function formatErrorMessage($field, $rule, $value = null)
    {
        $messages = [
            'calificacion' => [
                'required' => '⚠️ Debes seleccionar una calificación',
                'integer' => '❌ La calificación debe ser un número válido',
                'min' => '⭐ La calificación mínima es 1 estrella',
                'max' => '⭐ La calificación máxima es 5 estrellas',
            ],
            'comentario' => [
                'required' => '📝 Debes escribir un comentario',
                'string' => '❌ El comentario debe ser un texto válido',
                'min' => '📝 El comentario debe tener al menos 3 letras',
                'max' => '📝 El comentario no puede exceder 1000 caracteres',
            ],
            'nombre_usuario' => [
                'required' => '👤 Debes ingresar tu nombre',
                'string' => '❌ El nombre debe ser un texto válido',
                'min' => '👤 El nombre debe tener al menos 2 caracteres',
                'max' => '👤 El nombre no puede exceder 100 caracteres',
            ]
        ];

        return $messages[$field][$rule] ?? "Error en el campo {$field}";
    }
}
