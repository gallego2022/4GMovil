<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Helpers\PhotoHelper;

class GoogleController extends Controller
{
    /**
     * Redirige al usuario a Google para autenticación
     */
    public function redirectToGoogle()
    {
        try {
            $url = Socialite::driver('google')
                ->redirect()
                ->getTargetUrl();
                
            // Agregar parámetro para forzar selección de cuenta
            $url .= '&prompt=select_account';
                
            return redirect($url);
        } catch (\Exception $e) {
            Log::error('Error al redirigir a Google: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Error al conectar con Google. Inténtalo de nuevo.');
        }
    }

    /**
     * Maneja la respuesta de Google después de la autenticación
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            Log::info("Google callback recibido para: " . $googleUser->getEmail());
            Log::info("Google ID: " . $googleUser->getId());
            Log::info("Google Name: " . $googleUser->getName());
            
            // Buscar usuario existente por google_id o email
            $usuario = Usuario::where('google_id', $googleUser->getId())
                ->orWhere('correo_electronico', $googleUser->getEmail())
                ->first();
            
            if ($usuario) {
                Log::info("Usuario encontrado en BD: " . $usuario->correo_electronico);
                Log::info("Usuario google_id: " . $usuario->google_id);
                Log::info("Usuario estado: " . $usuario->estado);
                
                // CASO 1: Usuario existe con google_id
                if ($usuario->google_id === $googleUser->getId()) {
                    Log::info("Usuario existente con google_id: {$usuario->correo_electronico}");
                    
                    // Actualizar foto de perfil si es de Google
                    if (PhotoHelper::isGooglePhotoUrl($usuario->foto_perfil)) {
                        $usuario->foto_perfil = PhotoHelper::cleanGooglePhotoUrl($googleUser->getAvatar());
                        $usuario->save();
                    }
                    
                    Auth::login($usuario);
                    Log::info("Auth::login ejecutado para: " . $usuario->correo_electronico);
                    Log::info("Usuario autenticado: " . (Auth::check() ? 'SÍ' : 'NO'));
                    Log::info("Usuario actual: " . (Auth::user() ? Auth::user()->correo_electronico : 'NONE'));
                    
                    Log::info("Usuario existente logueado con Google: {$usuario->correo_electronico}");
                    return redirect()->intended('/perfil')->with('success', '¡Bienvenido de vuelta!');
                }
                
                // CASO 2: Usuario existe con email pero sin google_id (convertir a Google)
                if ($usuario->correo_electronico === $googleUser->getEmail() && !$usuario->google_id) {
                    Log::info("Convirtiendo usuario existente a Google: {$usuario->correo_electronico}");
                    
                    $usuario->google_id = $googleUser->getId();
                    $usuario->foto_perfil = PhotoHelper::cleanGooglePhotoUrl($googleUser->getAvatar());
                    $usuario->email_verified_at = now();
                    $usuario->save();
                    
                    Auth::login($usuario);
                    Log::info("Auth::login ejecutado para usuario convertido: " . $usuario->correo_electronico);
                    Log::info("Usuario autenticado: " . (Auth::check() ? 'SÍ' : 'NO'));
                    
                    Log::info("Usuario convertido a Google: {$usuario->correo_electronico}");
                    return redirect()->intended('/perfil')->with('success', '¡Cuenta vinculada con Google exitosamente!');
                }
                
                // CASO 3: Conflicto - google_id diferente (puede pasar en desarrollo)
                if ($usuario->google_id && $usuario->google_id !== $googleUser->getId()) {
                    Log::warning("Conflicto de google_id para: {$usuario->correo_electronico}");
                    
                    // En desarrollo, permitir sobrescribir
                    if (app()->environment('local', 'development')) {
                        $usuario->google_id = $googleUser->getId();
                        $usuario->foto_perfil = PhotoHelper::cleanGooglePhotoUrl($googleUser->getAvatar());
                        $usuario->save();
                        
                        Auth::login($usuario);
                        Log::info("Auth::login ejecutado para conflicto resuelto: " . $usuario->correo_electronico);
                        Log::info("Usuario autenticado: " . (Auth::check() ? 'SÍ' : 'NO'));
                        
                        Log::info("Conflicto resuelto en desarrollo para: {$usuario->correo_electronico}");
                        return redirect()->intended('/perfil')->with('success', '¡Bienvenido! Conflicto resuelto.');
                    } else {
                        return redirect()->route('login')->with('error', 'Esta cuenta ya está asociada con otra cuenta de Google.');
                    }
                }
            } else {
                // CASO 4: Nuevo usuario
                Log::info("Creando nuevo usuario de Google: {$googleUser->getEmail()}");
                
                $usuario = Usuario::create([
                    'nombre_usuario' => $googleUser->getName() ?: explode('@', $googleUser->getEmail())[0],
                    'correo_electronico' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'contrasena' => null, // Sin contraseña para usuarios de Google
                    'foto_perfil' => PhotoHelper::cleanGooglePhotoUrl($googleUser->getAvatar()),
                    'estado' => true,
                    'rol' => 'cliente',
                    'fecha_registro' => now(),
                    'email_verified_at' => now(), // Google ya verificó el email
                ]);
                
                Auth::login($usuario);
                Log::info("Auth::login ejecutado para nuevo usuario: " . $usuario->correo_electronico);
                Log::info("Usuario autenticado: " . (Auth::check() ? 'SÍ' : 'NO'));
                
                Log::info("Nuevo usuario creado con Google: {$usuario->correo_electronico}");
                
                // Devolver JSON para mostrar modal
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => '¡Cuenta creada exitosamente!',
                        'redirect' => '/perfil',
                        'showPasswordModal' => true
                    ]);
                } else {
                    // Para navegadores normales, redirigir directamente
                    return redirect()->route('perfil')->with('success', '¡Cuenta creada exitosamente!');
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error en callback de Google: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('login')->with('error', 'Error al autenticarse con Google. Inténtalo de nuevo.');
        }
    }
}
