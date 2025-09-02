<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Services\ProductoService;
use App\Models\Resena;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoPublicoController extends Controller
{
    protected $productoService;

    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    /**
     * Mostrar un producto específico (público)
     */
    public function show($id)
    {
        try {
            // Log para debug
            Log::info('Método show público llamado', [
                'id_original' => $id,
                'tipo_id' => gettype($id),
                'url_completa' => request()->fullUrl(),
                'referer' => request()->header('referer'),
                'user_agent' => request()->header('user-agent')
            ]);
            
            // Convertir el ID a entero ya que viene como string desde la ruta
            $id = (int) $id;
            
            // Log después de la conversión
            Log::info('ID convertido', [
                'id_convertido' => $id,
                'tipo_id_convertido' => gettype($id)
            ]);
            
            $data = $this->productoService->getProductById($id);
            
            if (!$data || !$data['producto']) {
                Log::warning('Producto no encontrado en show público', [
                    'id' => $id,
                    'data' => $data
                ]);
                abort(404, 'Producto no encontrado');
            }

            $producto = $data['producto'];
            
            // Cargar relaciones necesarias incluyendo reseñas
            $producto->load(['categoria', 'marca', 'imagenes', 'variantes.imagenes', 'resenas.usuario']);
            
            // Obtener productos relacionados (misma categoría y marca)
            $productosRelacionados = Producto::with(['categoria', 'marca', 'imagenes', 'resenas'])
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
                $productosAdicionales = Producto::with(['categoria', 'marca', 'imagenes', 'resenas'])
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
                $productosPopulares = Producto::with(['categoria', 'marca', 'imagenes', 'resenas'])
                    ->where('producto_id', '!=', $producto->producto_id)
                    ->where('estado', 'nuevo')
                    ->where('stock', '>', 0)
                    ->whereNotIn('producto_id', $productosRelacionados->pluck('producto_id'))
                    ->orderBy('created_at', 'desc')
                    ->limit(4 - $productosRelacionados->count())
                    ->get();
                
                $productosRelacionados = $productosRelacionados->merge($productosPopulares);
            }
            
            return view('productos.show', compact('producto', 'productosRelacionados'));
            
        } catch (\Exception $e) {
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
                'comentario' => 'required|string|min:10|max:1000',
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
                'comentario.min' => '📝 El comentario debe tener al menos 10 caracteres',
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

            // Crear la reseña
            $resenaData = [
                'producto_id' => $productoId,
                'calificacion' => $request->calificacion,
                'comentario' => $request->comentario,
            ];

            // Si hay usuario autenticado, usar su ID
            if (Auth::check()) {
                $resenaData['usuario_id'] = Auth::id();
            } else {
                // Si no hay usuario autenticado, crear o usar un usuario anónimo
                $usuarioAnonimo = \App\Models\Usuario::firstOrCreate(
                    ['correo_electronico' => 'anonimo@example.com'],
                    [
                        'nombre_usuario' => $request->nombre_usuario,
                        'correo_electronico' => 'anonimo@example.com',
                        'contrasena' => bcrypt('password'),
                        'rol' => 'cliente',
                        'estado' => true,
                        'fecha_registro' => now()
                    ]
                );
                $resenaData['usuario_id'] = $usuarioAnonimo->usuario_id;
            }

            $resena = Resena::create($resenaData);

            Log::info('Reseña creada exitosamente', [
                'producto_id' => $productoId,
                'usuario_id' => $resenaData['usuario_id'],
                'calificacion' => $request->calificacion,
                'resena_id' => $resena->resena_id,
                'usuario_autenticado' => Auth::check()
            ]);

            return response()->json([
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

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Producto no encontrado para reseña', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Producto no encontrado',
                'error_type' => 'not_found',
                'suggestion' => 'Verifica que el producto existe y esté disponible'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en reseña', [
                'producto_id' => $productoId,
                'errors' => $e->errors()
            ]);
            
            // Obtener el primer error para mostrar un mensaje principal
            $firstError = collect($e->errors())->first()[0] ?? 'Hay errores en los datos ingresados';
            
            return response()->json([
                'success' => false,
                'message' => '❌ ' . $firstError,
                'errors' => $e->errors(),
                'error_type' => 'validation',
                'suggestions' => [
                    'calificacion' => 'Selecciona una calificación de 1 a 5 estrellas',
                    'comentario' => 'Escribe un comentario de al menos 10 caracteres',
                    'nombre_usuario' => 'Ingresa tu nombre completo',
                ]
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al crear reseña', [
                'producto_id' => $productoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Error interno del servidor',
                'error_type' => 'server_error',
                'suggestion' => 'Por favor, intenta nuevamente en unos minutos',
                'debug_info' => config('app.debug') ? $e->getMessage() : null
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
                'min' => '📝 El comentario debe tener al menos 10 caracteres',
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
