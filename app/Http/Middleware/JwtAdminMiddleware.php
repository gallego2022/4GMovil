<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JwtAdminMiddleware
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

            // Verificar que el token pertenezca a un admin
            if (! $this->jwtService->isAdminToken($token)) {
                return $this->unauthorizedResponse('No tienes permisos de administrador');
            }

            // El JwtAuthMiddleware ya autenticó al usuario, solo verificamos el rol
            $payload = $this->jwtService->validateToken($token);

            if (! $payload) {
                // Cerrar sesión si el token está expirado o es inválido
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return $this->unauthorizedResponse('Token JWT inválido o expirado. Por favor, inicia sesión nuevamente.');
            }

            if (! isset($payload['rol']) || $payload['rol'] !== 'admin') {
                return $this->unauthorizedResponse('No tienes permisos de administrador');
            }

            return $next($request);
        } catch (\Exception $e) {
            \Log::error('Error en JwtAdminMiddleware: '.$e->getMessage());

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
            ], 403)->cookie('jwt_token', '', -1); // Eliminar cookie JWT
        }

        // Eliminar cookie JWT al redirigir
        return redirect()->route('login')
            ->with('error', $message)
            ->cookie('jwt_token', '', -1); // Eliminar cookie JWT
    }
}
