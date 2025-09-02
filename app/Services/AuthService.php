<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Helpers\PhotoHelper;
use Intervention\Image\Facades\Image;

class AuthService
{
    /**
     * Cambiar contraseña del usuario
     */
    public function cambiarContrasena(Request $request): array
    {
        try {
            $usuario = Auth::user();

            if (!Hash::check($request->contrasena_actual, $usuario->contrasena)) {
                return [
                    'success' => false,
                    'message' => trans('auth.password_current_wrong'),
                    'errors' => ['contrasena_actual' => [trans('auth.password_current_wrong')]]
                ];
            }

            $usuario->contrasena = Hash::make($request->nueva_contrasena);
            $usuario->save();

            return [
                'success' => true,
                'message' => trans('auth.password_change_success')
            ];

        } catch (\Exception $e) {
            Log::error('Error al cambiar contraseña: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => trans('auth.password_change_error')
            ];
        }
    }

    /**
     * Registrar un nuevo usuario
     */
    public function registrar(Request $request): array
    {
        try {
            $usuario = Usuario::create([
                'nombre_usuario' => $request->nombre_usuario,
                'correo_electronico' => $request->correo_electronico,
                'contrasena' => Hash::make($request->contrasena),
                'telefono' => $request->telefono,
                'estado' => true,
                'rol' => 'cliente',
                'fecha_registro' => now(),
            ]);

            Auth::login($usuario);
            $usuario->sendEmailVerificationNotification();
            Log::info('Código OTP de verificación enviado a: ' . $usuario->correo_electronico);

            return [
                'success' => true,
                'usuario' => $usuario,
                'message' => 'Se ha enviado un código OTP a tu correo electrónico'
            ];

        } catch (\Exception $e) {
            Log::error('Error en registro: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => trans('messages.error')
            ];
        }
    }

    /**
     * Iniciar sesión
     */
    public function logear(Request $request): array
    {
        try {
            // Buscar usuario primero para verificar si puede hacer login manual
            $usuario = Usuario::where('correo_electronico', $request->correo_electronico)->first();
            
            if ($usuario && !$usuario->canLoginManually()) {
                // Usuario existe pero no tiene contraseña (es usuario de Google)
                return [
                    'success' => false,
                    'message' => trans('auth.login_error_google_account'),
                    'error_type' => 'google_account'
                ];
            }

            if (Auth::attempt([
                'correo_electronico' => $request->correo_electronico,
                'password' => $request->contrasena,
            ])) {
                $usuario = Auth::user();
                
                // Verificar si la cuenta está activa
                if (!$usuario->estado) {
                    Auth::logout();
                    return [
                        'success' => false,
                        'message' => trans('auth.account_inactive'),
                        'error_type' => 'inactive_account'
                    ];
                }

                // Verificar si el email está verificado
                if (!$usuario->email_verified_at) {
                    Auth::logout();
                    
                    // Enviar nuevo código OTP si no tiene uno válido
                    if (!\App\Models\OtpCode::tieneCodigoValido($usuario->usuario_id, 'email_verification')) {
                        $usuario->sendEmailVerificationNotification();
                    }
                    
                    return [
                        'success' => false,
                        'message' => 'Debes verificar tu correo electrónico antes de acceder al sistema.',
                        'error_type' => 'unverified_email',
                        'usuario' => $usuario
                    ];
                }

                $request->session()->regenerate();

                return [
                    'success' => true,
                    'usuario' => $usuario,
                    'redirect_route' => $usuario->rol === 'admin' ? 'admin.index' : 'landing'
                ];
            }

            return [
                'success' => false,
                'message' => trans('auth.login_error'),
                'error_type' => 'invalid_credentials'
            ];

        } catch (\Exception $e) {
            Log::error('Error en login: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => trans('auth.login_error'),
                'error_type' => 'server_error'
            ];
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request): array
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return [
                'success' => true,
                'message' => trans('auth.logout_success')
            ];

        } catch (\Exception $e) {
            Log::error('Error en logout: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => trans('auth.logout_error')
            ];
        }
    }

    /**
     * Obtener perfil del usuario
     */
    public function getPerfil(): array
    {
        try {
            $usuario = Auth::user();
            
            return [
                'success' => true,
                'usuario' => $usuario,
                'view' => $usuario->rol === 'admin' ? 'modules.auth.perfil' : 'modules.cliente.perfil'
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener perfil: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al cargar el perfil'
            ];
        }
    }

    /**
     * Actualizar perfil del usuario
     */
    public function actualizarPerfil(Request $request): array
    {
        try {
            $usuario = Auth::user();

            $usuario->nombre_usuario = $request->nombre_usuario;
            $usuario->correo_electronico = $request->correo_electronico;
            
            if ($request->telefono) {
                $usuario->telefono = $request->telefono;
            }

            if ($request->hasFile('foto_perfil')) {
                if ($usuario->foto_perfil) {
                    Storage::disk('public')->delete($usuario->foto_perfil);
                }
                
                $path = $request->file('foto_perfil')->store('fotos_perfil', 'public');
                $usuario->foto_perfil = $path;
            }

            $usuario->save();

            return [
                'success' => true,
                'message' => trans('profile.update_success')
            ];

        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => trans('profile.update_error')
            ];
        }
    }

    /**
     * Eliminar foto de perfil
     */
    public function eliminarFoto(): array
    {
        try {
            $usuario = Auth::user();
            
            // Verificar si el usuario tiene foto de perfil
            if (!$usuario->foto_perfil) {
                return [
                    'success' => false,
                    'message' => 'No tienes una foto de perfil para eliminar'
                ];
            }
            
            // Verificar si es una URL externa (Google, Facebook, etc.)
            if (PhotoHelper::isExternalUrl($usuario->foto_perfil)) {
                // Es una URL externa, solo limpiar la BD
                $usuario->foto_perfil = null;
                $usuario->save();
                
                return [
                    'success' => true,
                    'message' => 'Foto de perfil eliminada exitosamente'
                ];
            }
            
            // Verificar si el archivo existe en el almacenamiento
            if (!Storage::disk('public')->exists($usuario->foto_perfil)) {
                // Si el archivo no existe en storage pero está en la BD, limpiar la BD
                $usuario->foto_perfil = null;
                $usuario->save();
                
                return [
                    'success' => true,
                    'message' => 'Foto de perfil eliminada exitosamente'
                ];
            }
            
            // Eliminar el archivo del almacenamiento
            $deleted = Storage::disk('public')->delete($usuario->foto_perfil);
            
            if ($deleted) {
                // Limpiar la referencia en la base de datos
                $usuario->foto_perfil = null;
                $usuario->save();
                
                return [
                    'success' => true,
                    'message' => 'Foto de perfil eliminada exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se pudo eliminar el archivo de la foto'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar la foto de perfil: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error interno del servidor al eliminar la foto'
            ];
        }
    }

    /**
     * Validar contraseña actual
     */
    public function validarContrasenaActual(Request $request): array
    {
        try {
            if (!$request->has('password')) {
                return [
                    'success' => false,
                    'message' => 'No se proporcionó contraseña'
                ];
            }

            $user = Auth::user();
            $isValid = Hash::check($request->password, $user->contrasena);

            return [
                'success' => true,
                'valid' => $isValid,
                'message' => $isValid ? 'Contraseña correcta' : 'Contraseña incorrecta'
            ];

        } catch (\Exception $e) {
            Log::error('Error al validar contraseña: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al validar la contraseña'
            ];
        }
    }
}
