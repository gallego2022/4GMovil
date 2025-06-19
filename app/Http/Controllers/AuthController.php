<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
            return back()
                ->withErrors(['contrasena_actual' => trans('auth.password_current_wrong')])
                ->withInput();
        }

        try {
            $usuario->contrasena = Hash::make($request->nueva_contrasena);
            $usuario->save();

            return redirect()->route('perfil')
                ->with('mensaje', trans('auth.password_change_success'))
                ->with('tipo', 'success');
        } catch (\Exception $e) {
            Log::error('Error al cambiar contraseña: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => trans('auth.password_change_error')])
                ->withInput();
        }
    }

    // Funcion para registrar un nuevo usuario
    public function registrar(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|max:25',
            'correo_electronico' => 'required|email|unique:usuarios,correo_electronico',
            'contrasena' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'telefono' => ['required', 'digits:10', 'regex:/^[0-9]+$/'],
            'acepta_terminos' => 'accepted',
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
            Log::info('Correo de verificación enviado a: ' . $usuario->correo_electronico);

            return redirect()->route('verification.notice')
                ->with('mensaje', trans('auth.verify_email_notice'))
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

        if (Auth::attempt([
            'correo_electronico' => $request->correo_electronico,
            'password' => $request->contrasena,
        ])) {
            if (!Auth::user()->estado) {
                Auth::logout();
                return back()
                    ->withErrors(['correo_electronico' => trans('profile.account_inactive')])
                    ->withInput();
            }

            $request->session()->regenerate();

            if (Auth::user()->rol === 'admin') {
                return redirect()->route('admin.index')
                    ->with('mensaje', trans('auth.login_success'));
            } else {
                return redirect()->route('landing')
                    ->with('mensaje', trans('auth.login_success'));
            }
        }

        return back()
            ->withErrors(['correo_electronico' => trans('auth.login_error')])
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
            ->with('mensaje', trans('auth.logout_success'));
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
            'nombre_usuario' => 'required|string|max:25',
            'correo_electronico' => 'required|email|unique:usuarios,correo_electronico,' . $usuario->usuario_id . ',usuario_id',
            'telefono' => ['nullable', 'digits:10', 'regex:/^[0-9]+$/'],
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
            
            if ($usuario->foto_perfil && Storage::disk('public')->exists($usuario->foto_perfil)) {
                Storage::disk('public')->delete($usuario->foto_perfil);
                $usuario->foto_perfil = null;
                $usuario->save();
                
                return response()->json([
                    'tipo' => 'success',
                    'mensaje' => trans('profile.photo_delete_success')
                ]);
            }
            
            return response()->json([
                'tipo' => 'error',
                'mensaje' => trans('profile.photo_delete_error')
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar la foto de perfil: ' . $e->getMessage());
            
            return response()->json([
                'tipo' => 'error',
                'mensaje' => trans('profile.photo_delete_error')
            ], 500);
        }
    }

    public function validarContrasenaActual(Request $request)
    {
        if (!$request->has('password')) {
            return response()->json(['valid' => false, 'message' => 'No se proporcionó contraseña']);
        }

        $user = auth()->user();
        $isValid = Hash::check($request->password, $user->contrasena);

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Contraseña correcta' : 'Contraseña incorrecta'
        ]);
    }
}
