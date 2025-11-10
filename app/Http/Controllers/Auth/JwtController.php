<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\JwtService;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JwtController extends Controller
{
    public function __construct(
        private JwtService $jwtService,
        private AuthService $authService
    ) {
    }

    /**
     * Genera un token JWT para un usuario autenticado
     */
    public function generateToken(Request $request)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes estar autenticado para generar un token JWT'
                ], 401);
            }

            $usuario = Auth::user();

            // Verificar que el usuario esté activo
            if (!$usuario->estado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tu cuenta está inactiva'
                ], 403);
            }

            // Generar el token JWT
            $token = $this->jwtService->generateToken($usuario);

            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.expiration', 3600),
                'usuario' => [
                    'id' => $usuario->usuario_id,
                    'nombre' => $usuario->nombre_usuario,
                    'email' => $usuario->correo_electronico,
                    'rol' => $usuario->rol,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generando token JWT: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar token JWT'
            ], 500);
        }
    }

    /**
     * Autentica un usuario y genera un token JWT
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo_electronico' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Intentar autenticar al usuario
            $result = $this->authService->logear($request);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Credenciales inválidas',
                    'error_type' => $result['error_type'] ?? 'invalid_credentials'
                ], 401);
            }

            $usuario = $result['usuario'];

            // Generar el token JWT
            $token = $this->jwtService->generateToken($usuario);

            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.expiration', 3600),
                'usuario' => [
                    'id' => $usuario->usuario_id,
                    'nombre' => $usuario->nombre_usuario,
                    'email' => $usuario->correo_electronico,
                    'rol' => $usuario->rol,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en login JWT: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al autenticar'
            ], 500);
        }
    }

    /**
     * Refresca un token JWT
     */
    public function refreshToken(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            $token = $request->query('token');
        }

        if ($token && preg_match('/Bearer\s+(.*)$/i', $token, $matches)) {
            $token = $matches[1];
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 401);
        }

        try {
            $newToken = $this->jwtService->refreshToken($token);

            if (!$newToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido o expirado'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'token' => $newToken,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.expiration', 3600),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error refrescando token JWT: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al refrescar token'
            ], 500);
        }
    }

    /**
     * Valida un token JWT
     */
    public function validateToken(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            $token = $request->query('token');
        }

        if ($token && preg_match('/Bearer\s+(.*)$/i', $token, $matches)) {
            $token = $matches[1];
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Token no proporcionado'
            ], 401);
        }

        try {
            $payload = $this->jwtService->validateToken($token);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Token inválido o expirado'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'valid' => true,
                'payload' => [
                    'user_id' => $payload['sub'] ?? null,
                    'rol' => $payload['rol'] ?? null,
                    'email' => $payload['email'] ?? null,
                    'expires_at' => isset($payload['exp']) ? date('Y-m-d H:i:s', $payload['exp']) : null,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error validando token JWT: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Error al validar token'
            ], 500);
        }
    }
}

