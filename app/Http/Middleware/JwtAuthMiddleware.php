<?php

namespace App\Http\Middleware;

use App\Services\Auth\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    public function __construct(
        private JwtService $jwtService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Obtener el token del header Authorization
            $token = $this->extractToken($request);

            // JWT es obligatorio - rechazar si no hay token
            if (! $token) {
                return $this->unauthorizedResponse('Token JWT requerido. Por favor, inicia sesión.');
            }

            // Validar el token
            $payload = $this->jwtService->validateToken($token);

            if (! $payload) {
                // Cerrar sesión si el token está expirado o es inválido
                Auth::logout();
                $request->session()->invalidate();
                // NO regenerar el token CSRF aquí para evitar errores 419 en formularios abiertos
                // El token se regenerará en la siguiente petición GET
                // $request->session()->regenerateToken();

                return $this->unauthorizedResponse('Token JWT inválido o expirado. Por favor, inicia sesión nuevamente.');
            }

            // Obtener el usuario desde el token
            $usuario = $this->jwtService->getUserFromToken($token);

            if (! $usuario) {
                return $this->unauthorizedResponse('Usuario no encontrado');
            }

            // Verificar que el usuario esté activo
            if (! $usuario->estado) {
                return $this->unauthorizedResponse('Usuario inactivo');
            }

            // Autenticar al usuario en la sesión de Laravel (para compatibilidad con código existente)
            // Solo hacer login si el usuario no está ya autenticado o es diferente
            $necesitaLogin = !Auth::check() || Auth::id() !== $usuario->usuario_id;
            
            if ($necesitaLogin) {
                Auth::login($usuario);
                // Solo regenerar token CSRF cuando se hace login de un usuario diferente
                // y SOLO en peticiones GET (no en POST/PUT/DELETE para evitar errores 419)
                // Esto asegura que cada usuario tenga su propio token CSRF
                // pero NO regenera en cada petición para evitar errores 419
                if ($request->isMethod('GET') && !$request->ajax()) {
                    Log::debug('JwtAuthMiddleware: Regenerando token CSRF por login de usuario diferente', [
                        'user_id' => $usuario->usuario_id,
                        'method' => $request->method(),
                    ]);
                    $request->session()->regenerateToken();
                }
            }
            
            // IMPORTANTE: NO regenerar el token CSRF en cada petición
            // Esto causa que el token en el formulario no coincida con el token en la sesión
            // El token CSRF se mantiene válido durante toda la sesión del usuario
            // Solo se regenera cuando:
            // 1. Se hace login de un usuario diferente Y es una petición GET (arriba)
            // 2. Se invalida la sesión explícitamente

            // Agregar información del token a la request
            $request->merge(['jwt_payload' => $payload]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error en JwtAuthMiddleware: '.$e->getMessage());

            return $this->unauthorizedResponse('Error de autenticación');
        }
    }

    /**
     * Extrae el token JWT del header Authorization, cookie o query parameter
     */
    private function extractToken(Request $request): ?string
    {
        // 1. Intentar obtener desde header Authorization (prioridad)
        $authorization = $request->header('Authorization');
        if ($authorization && preg_match('/Bearer\s+(.*)$/i', $authorization, $matches)) {
            return $matches[1];
        }

        // 2. Intentar obtener desde cookie (para login tradicional)
        $cookieToken = $request->cookie('jwt_token');
        if ($cookieToken) {
            return $cookieToken;
        }

        // 3. Intentar obtener desde query parameter (para compatibilidad)
        $queryToken = $request->query('token');
        if ($queryToken) {
            return $queryToken;
        }

        return null;
    }

    /**
     * Retorna una respuesta de no autorizado
     */
    private function unauthorizedResponse(string $message): Response
    {
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'unauthorized',
                'expired' => true,
            ], 401)->cookie('jwt_token', '', -1); // Eliminar cookie JWT
        }

        // Eliminar cookie JWT al redirigir
        return redirect()->route('login')
            ->with('error', $message)
            ->cookie('jwt_token', '', -1); // Eliminar cookie JWT
    }
}
