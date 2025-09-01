<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\Usuario;

class AuthService
{
    protected $loggingService;
    protected $cacheService;
    protected $validationService;

    public function __construct(
        LoggingService $loggingService, 
        CacheService $cacheService,
        ValidationService $validationService
    ) {
        $this->loggingService = $loggingService;
        $this->cacheService = $cacheService;
        $this->validationService = $validationService;
    }

    /**
     * Registrar nuevo usuario
     */
    public function registerUser(array $data): array
    {
        try {
            $this->loggingService->info('Iniciando registro de usuario', [
                'email' => $data['email'] ?? 'no especificado'
            ]);

            // Validar datos
            $validatedData = $this->validationService->validateUser($data);
            
            // Verificar si el usuario ya existe
            if (Usuario::where('email', $validatedData['email'])->exists()) {
                return [
                    'success' => false,
                    'error' => 'El usuario ya existe con este email'
                ];
            }

            // Crear usuario
            $user = Usuario::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'email_verified_at' => null,
                'remember_token' => Str::random(60)
            ]);

            // Disparar evento de registro
            event(new Registered($user));

            // Generar token de verificación
            $verificationToken = $this->generateVerificationToken($user);

            $this->loggingService->info('Usuario registrado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return [
                'success' => true,
                'user' => $user,
                'verification_token' => $verificationToken,
                'message' => 'Usuario registrado exitosamente'
            ];

        } catch (\Exception $e) {
            $this->loggingService->error('Error al registrar usuario', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'error' => 'Error al registrar usuario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Autenticar usuario
     */
    public function authenticateUser(array $credentials, bool $remember = false): array
    {
        try {
            $this->loggingService->info('Iniciando autenticación de usuario', [
                'email' => $credentials['email'] ?? 'no especificado'
            ]);

            // Validar credenciales
            $validatedData = $this->validationService->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ], $credentials);

            // Intentar autenticación
            if (Auth::attempt($validatedData, $remember)) {
                $user = Auth::user();
                
                // Verificar si el email está verificado
                if (!$user->email_verified_at) {
                    Auth::logout();
                    return [
                        'success' => false,
                        'error' => 'Debe verificar su email antes de iniciar sesión'
                    ];
                }

                // Generar token de sesión
                $sessionToken = $this->generateSessionToken($user);

                $this->loggingService->info('Usuario autenticado exitosamente', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                return [
                    'success' => true,
                    'user' => $user,
                    'session_token' => $sessionToken,
                    'message' => 'Autenticación exitosa'
                ];
            }

            return [
                'success' => false,
                'error' => 'Credenciales inválidas'
            ];

        } catch (\Exception $e) {
            $this->loggingService->error('Error en autenticación', [
                'error' => $e->getMessage(),
                'credentials' => $credentials
            ]);

            return [
                'success' => false,
                'error' => 'Error en autenticación: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cerrar sesión
     */
    public function logoutUser(): array
    {
        try {
            $user = Auth::user();
            
            if ($user) {
                $this->loggingService->info('Usuario cerrando sesión', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                // Invalidar token de sesión
                $this->invalidateSessionToken($user);

                // Cerrar sesión
                Auth::logout();

                return [
                    'success' => true,
                    'message' => 'Sesión cerrada exitosamente'
                ];
            }

            return [
                'success' => false,
                'error' => 'No hay sesión activa'
            ];

        } catch (\Exception $e) {
            $this->loggingService->error('Error al cerrar sesión', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Error al cerrar sesión: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar email
     */
    public function verifyEmail(string $token): array
    {
        try {
            $this->loggingService->info('Verificando email con token', ['token' => $token]);

            // Buscar usuario por token
            $user = Usuario::where('email_verification_token', $token)->first();

            if (!$user) {
                return [
                    'success' => false,
                    'error' => 'Token de verificación inválido'
                ];
            }

            // Verificar email
            $user->update([
                'email_verified_at' => now(),
                'email_verification_token' => null
            ]);

            $this->loggingService->info('Email verificado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return [
                'success' => true,
                'user' => $user,
                'message' => 'Email verificado exitosamente'
            ];

        } catch (\Exception $e) {
            $this->loggingService->error('Error al verificar email', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Error al verificar email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Solicitar restablecimiento de contraseña
     */
    public function requestPasswordReset(string $email): array
    {
        try {
            $this->loggingService->info('Solicitando restablecimiento de contraseña', ['email' => $email]);

            // Verificar que el usuario existe
            $user = Usuario::where('email', $email)->first();
            
            if (!$user) {
                return [
                    'success' => false,
                    'error' => 'No existe un usuario con este email'
                ];
            }

            // Generar token de restablecimiento
            $token = Password::createToken($user);

            // Enviar email de restablecimiento
            $resetLink = url('/reset-password', ['token' => $token, 'email' => $email]);

            $this->loggingService->info('Token de restablecimiento generado', [
                'user_id' => $user->id,
                'email' => $email,
                'token' => $token
            ]);

            return [
                'success' => true,
                'token' => $token,
                'reset_link' => $resetLink,
                'message' => 'Se ha enviado un enlace de restablecimiento a su email'
            ];

        } catch (\Exception $e) {
            $this->loggingService->error('Error al solicitar restablecimiento de contraseña', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Error al solicitar restablecimiento: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Restablecer contraseña
     */
    public function resetPassword(string $token, string $email, string $password): array
    {
        try {
            $this->loggingService->info('Restableciendo contraseña', [
                'email' => $email,
                'token' => $token
            ]);

            // Verificar token
            $user = Usuario::where('email', $email)->first();
            
            if (!$user || !Password::tokenExists($user, $token)) {
                return [
                    'success' => false,
                    'error' => 'Token de restablecimiento inválido'
                ];
            }

            // Actualizar contraseña
            $user->update([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60)
            ]);

            // Eliminar token usado
            Password::deleteToken($user);

            // Disparar evento
            event(new PasswordReset($user));

            $this->loggingService->info('Contraseña restablecida exitosamente', [
                'user_id' => $user->id,
                'email' => $email
            ]);

            return [
                'success' => true,
                'user' => $user,
                'message' => 'Contraseña restablecida exitosamente'
            ];

        } catch (\Exception $e) {
            $this->loggingService->error('Error al restablecer contraseña', [
                'email' => $email,
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Error al restablecer contraseña: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Usuario $user, string $currentPassword, string $newPassword): array
    {
        try {
            $this->loggingService->info('Cambiando contraseña de usuario', [
                'user_id' => $user->id
            ]);

            // Verificar contraseña actual
            if (!Hash::check($currentPassword, $user->password)) {
                return [
                    'success' => false,
                    'error' => 'La contraseña actual es incorrecta'
                ];
            }

            // Actualizar contraseña
            $user->update([
                'password' => Hash::make($newPassword),
                'remember_token' => Str::random(60)
            ]);

            $this->loggingService->info('Contraseña cambiada exitosamente', [
                'user_id' => $user->id
            ]);

            return [
                'success' => true,
                'message' => 'Contraseña cambiada exitosamente'
            ];

        } catch (\Exception $e) {
            $this->loggingService->error('Error al cambiar contraseña', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Error al cambiar contraseña: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generar token de verificación
     */
    protected function generateVerificationToken(Usuario $user): string
    {
        $token = Str::random(64);
        
        $user->update(['email_verification_token' => $token]);
        
        return $token;
    }

    /**
     * Generar token de sesión
     */
    protected function generateSessionToken(Usuario $user): string
    {
        $token = Str::random(64);
        
        // Almacenar en cache con TTL
        $this->cacheService->set("session_token_{$user->id}", $token, 3600);
        
        return $token;
    }

    /**
     * Invalidar token de sesión
     */
    protected function invalidateSessionToken(Usuario $user): void
    {
        $this->cacheService->forget("session_token_{$user->id}");
    }

    /**
     * Verificar token de sesión
     */
    public function verifySessionToken(Usuario $user, string $token): bool
    {
        $storedToken = $this->cacheService->get("session_token_{$user->id}");
        return $storedToken === $token;
    }

    /**
     * Obtener usuario autenticado
     */
    public function getAuthenticatedUser(): ?Usuario
    {
        return Auth::user();
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin(Usuario $user): bool
    {
        return $user->role === 'admin' || $user->hasRole('admin');
    }

    /**
     * Obtener permisos del usuario
     */
    public function getUserPermissions(Usuario $user): array
    {
        return $this->cacheService->remember("user_permissions_{$user->id}", 3600, function () use ($user) {
            // Aquí implementarías la lógica para obtener permisos
            // Por ejemplo, desde roles, políticas, etc.
            return $user->permissions ?? [];
        });
    }
}
