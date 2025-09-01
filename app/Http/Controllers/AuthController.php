<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Helpers\PhotoHelper;
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{
    // Esta función muestra el formulario de inicio de sesión
    public function index()
    {
        return view('modules.auth.login');
    }

    // Esta funcion muestra el formulario para cambiar contraseña
    public function formCambiarContrasena()
    {
        return view('modules.auth.cambiar-contrasena');
    }
    // Funcion para cambiar contraseña
    public function cambiarContrasena(Request $request)
    {
        $request->validate([
            'contrasena_actual' => [
                'required',
                'string',
                'min:8'
            ],
            'nueva_contrasena' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:contrasena_actual',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'nueva_contrasena_confirmation' => 'required'
        ]);

        $usuario = Auth::user();

        if (!Hash::check($request->contrasena_actual, $usuario->contrasena)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('auth.password_current_wrong'),
                    'errors' => ['contrasena_actual' => [trans('auth.password_current_wrong')]]
                ], 422);
            }
            
            return back()
                ->withErrors(['contrasena_actual' => trans('auth.password_current_wrong')])
                ->withInput();
        }

        try {
            $usuario->contrasena = Hash::make($request->nueva_contrasena);
            $usuario->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('auth.password_change_success')
                ]);
            }

            return redirect()->route('perfil')
                ->with('mensaje', trans('auth.password_change_success'))
                ->with('tipo', 'success');
        } catch (\Exception $e) {
            Log::error('Error al cambiar contraseña: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('auth.password_change_error')
                ], 500);
            }
            
            return back()
                ->withErrors(['error' => trans('auth.password_change_error')])
                ->withInput();
        }
    }

    // Funcion para registrar un nuevo usuario
    public function registrar(Request $request)
    {
        $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:25', 'regex:/^[\pL\s]+$/u'],
            'correo_electronico' => 'required|email|unique:usuarios,correo_electronico',
            'contrasena' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'telefono' => ['required', 'regex:/^3\d{9}$/'],
            'acepta_terminos' => 'accepted',
        ], [
            'nombre_usuario.required' => trans('auth.validation.nombre_usuario.required'),
            'nombre_usuario.max' => trans('auth.validation.nombre_usuario.max'),
            'nombre_usuario.regex' => trans('auth.validation.nombre_usuario.regex'),
            'correo_electronico.required' => trans('auth.validation.correo_electronico.required'),
            'correo_electronico.email' => trans('auth.validation.correo_electronico.email'),
            'correo_electronico.unique' => trans('auth.validation.correo_electronico.unique'),
            'telefono.required' => trans('auth.validation.telefono.required'),
            'telefono.regex' => trans('auth.validation.telefono.regex'),
            'contrasena.required' => trans('auth.validation.contrasena.required'),
            'contrasena.min' => trans('auth.validation.contrasena.min'),
            'contrasena.confirmed' => trans('auth.validation.contrasena.confirmed'),
            'contrasena.regex' => trans('auth.validation.contrasena.regex'),
            'acepta_terminos.accepted' => trans('auth.validation.acepta_terminos.accepted'),
        ]);

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

            return redirect()->route('otp.verify.register')
                ->with('verification_email', $usuario->correo_electronico)
                ->with('mensaje', 'Se ha enviado un código OTP a tu correo electrónico')
                ->with('registro_exitoso', trans('auth.register_success'));
        } catch (\Exception $e) {
            Log::error('Error en registro: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => trans('messages.error')])
                ->withInput();
        }
    }

    // Funcion para iniciar sesión
    public function logear(Request $request)
    {
        $request->validate([
            'correo_electronico' => 'required|email',
            'contrasena' => 'required|string'
        ]);

        // Buscar usuario primero para verificar si puede hacer login manual
        $usuario = Usuario::where('correo_electronico', $request->correo_electronico)->first();
        
        if ($usuario && !$usuario->canLoginManually()) {
            // Usuario existe pero no tiene contraseña (es usuario de Google)
            return back()
                ->with('error_login', trans('auth.login_error_google_account'))
                ->withInput(['correo_electronico' => $request->correo_electronico]);
        }

        if (Auth::attempt([
            'correo_electronico' => $request->correo_electronico,
            'password' => $request->contrasena,
        ])) {
            $usuario = Auth::user();
            
            // Verificar si la cuenta está activa
            if (!$usuario->estado) {
                Auth::logout();
                return back()
                    ->with('error_login', trans('auth.account_inactive'))
                    ->withInput(['correo_electronico' => $request->correo_electronico]);
            }

            // Verificar si el email está verificado
            if (!$usuario->email_verified_at) {
                Auth::logout();
                
                // Enviar nuevo código OTP si no tiene uno válido
                if (!\App\Models\OtpCode::tieneCodigoValido($usuario->usuario_id, 'email_verification')) {
                    $usuario->sendEmailVerificationNotification();
                }
                
                return redirect()->route('otp.verify.register')
                    ->with('verification_email', $usuario->correo_electronico)
                    ->with('error_login', 'Debes verificar tu correo electrónico antes de acceder al sistema.')
                    ->withInput(['correo_electronico' => $request->correo_electronico]);
            }

            $request->session()->regenerate();

            if ($usuario->rol === 'admin') {
                return redirect()->route('admin.index')
                    ->with('status', trans('auth.login_success'))
                    ->with('status_type', 'success');
            } else {
                return redirect()->route('landing')
                    ->with('status', trans('auth.login_success'))
                    ->with('status_type', 'success');
            }
        }

        return back()
            ->with('error_login', trans('auth.login_error'))
            ->withInput(['correo_electronico' => $request->correo_electronico]);
    }

    // Esta función cierra la sesión del usuario
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()
            ->route('landing')
            ->with('status', trans('auth.logout_success'))
            ->with('status_type', 'info');
    }

    // Esta función muestra el perfil del usuario autenticado
    public function perfil()
    {
        $usuario = Auth::user();
        
        if ($usuario->rol === 'admin') {
            return view('modules.auth.perfil', compact('usuario'));
        }
        
        return view('modules.cliente.perfil', compact('usuario'));
    }

    // Esta función actualiza el perfil del usuario autenticado
    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:25', 'regex:/^[\pL\s]+$/u'],
            'correo_electronico' => 'required|email|unique:usuarios,correo_electronico,' . $usuario->usuario_id . ',usuario_id',
            'telefono' => ['nullable', 'regex:/^3\d{9}$/'],
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
        ]);

        try {
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

            return back()
                ->with('mensaje', trans('profile.update_success'))
                ->with('tipo', 'success');
        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => trans('profile.update_error')])
                ->withInput();
        }
    }
    // Eliminar foto
    public function eliminarFoto()
    {
        try {
            $usuario = Auth::user();
            
            // Verificar si el usuario tiene foto de perfil
            if (!$usuario->foto_perfil) {
                return response()->json([
                    'tipo' => 'error',
                    'mensaje' => 'No tienes una foto de perfil para eliminar'
                ], 400);
            }
            
            // Verificar si es una URL externa (Google, Facebook, etc.)
            if (PhotoHelper::isExternalUrl($usuario->foto_perfil)) {
                // Es una URL externa, solo limpiar la BD
                $usuario->foto_perfil = null;
                $usuario->save();
                
                return response()->json([
                    'tipo' => 'success',
                    'mensaje' => 'Foto de perfil eliminada exitosamente'
                ]);
            }
            
            // Verificar si el archivo existe en el almacenamiento
            if (!Storage::disk('public')->exists($usuario->foto_perfil)) {
                // Si el archivo no existe en storage pero está en la BD, limpiar la BD
                $usuario->foto_perfil = null;
                $usuario->save();
                
                return response()->json([
                    'tipo' => 'success',
                    'mensaje' => 'Foto de perfil eliminada exitosamente'
                ]);
            }
            
            // Eliminar el archivo del almacenamiento
            $deleted = Storage::disk('public')->delete($usuario->foto_perfil);
            
            if ($deleted) {
                // Limpiar la referencia en la base de datos
                $usuario->foto_perfil = null;
                $usuario->save();
                
                return response()->json([
                    'tipo' => 'success',
                    'mensaje' => 'Foto de perfil eliminada exitosamente'
                ]);
            } else {
                return response()->json([
                    'tipo' => 'error',
                    'mensaje' => 'No se pudo eliminar el archivo de la foto'
                ], 500);
            }
            
        } catch (\Exception $e) {
            // Log del error
            error_log('Error al eliminar la foto de perfil: ' . $e->getMessage());
            
            return response()->json([
                'tipo' => 'error',
                'mensaje' => 'Error interno del servidor al eliminar la foto'
            ], 500);
        }
    }

    public function validarContrasenaActual(Request $request)
    {
        if (!$request->has('password')) {
            return response()->json(['valid' => false, 'message' => 'No se proporcionó contraseña']);
        }

        $user = Auth::user();
        $isValid = Hash::check($request->password, $user->contrasena);

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Contraseña correcta' : 'Contraseña incorrecta'
        ]);
    }
}
