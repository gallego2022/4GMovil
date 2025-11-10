<?php

namespace App\Services;

use App\Models\Usuario;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class JwtService
{
    /**
     * Clave secreta para firmar los tokens JWT
     */
    private string $secretKey;

    /**
     * Tiempo de expiración del token en segundos (por defecto 1 hora)
     */
    private int $expirationTime;

    /**
     * Algoritmo de encriptación
     */
    private string $algorithm = 'HS256';

    public function __construct()
    {
        $this->secretKey = config('jwt.secret', env('JWT_SECRET', config('app.key')));
        $this->expirationTime = config('jwt.expiration', env('JWT_EXPIRATION', 3600));
    }

    /**
     * Genera un token JWT para un usuario
     */
    public function generateToken(Usuario $usuario): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->expirationTime;

        $payload = [
            'iss' => config('app.url'), // Issuer
            'aud' => config('app.url'), // Audience
            'iat' => $issuedAt, // Issued at
            'exp' => $expirationTime, // Expiration time
            'sub' => $usuario->usuario_id, // Subject (user ID)
            'rol' => $usuario->rol, // User role
            'email' => $usuario->correo_electronico, // User email
        ];

        try {
            return JWT::encode($payload, $this->secretKey, $this->algorithm);
        } catch (\Exception $e) {
            Log::error('Error generando token JWT: ' . $e->getMessage());
            throw new \RuntimeException('Error al generar token JWT', 0, $e);
        }
    }

    /**
     * Valida y decodifica un token JWT
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded;
        } catch (\Firebase\JWT\ExpiredException $e) {
            Log::warning('Token JWT expirado: ' . $e->getMessage());
            return null;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            Log::warning('Token JWT con firma inválida: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Error validando token JWT: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene el usuario desde un token JWT
     */
    public function getUserFromToken(string $token): ?Usuario
    {
        $payload = $this->validateToken($token);

        if (!$payload || !isset($payload['sub'])) {
            return null;
        }

        return Usuario::find($payload['sub']);
    }

    /**
     * Verifica si un token es válido y pertenece a un admin
     */
    public function isAdminToken(string $token): bool
    {
        $payload = $this->validateToken($token);

        if (!$payload) {
            return false;
        }

        return isset($payload['rol']) && $payload['rol'] === 'admin';
    }

    /**
     * Refresca un token (genera uno nuevo con el mismo usuario)
     */
    public function refreshToken(string $token): ?string
    {
        $payload = $this->validateToken($token);

        if (!$payload || !isset($payload['sub'])) {
            return null;
        }

        $usuario = Usuario::find($payload['sub']);

        if (!$usuario) {
            return null;
        }

        return $this->generateToken($usuario);
    }
}

