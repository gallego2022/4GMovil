<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GooglePasswordController extends Controller
{
    /**
     * Establece la contraseña para el usuario de Google
     */
    public function setPassword(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autenticado'], 401);
            }
            return redirect()->route('login');
        }

        $usuario = Auth::user();

        // Validar la solicitud
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un símbolo.',
        ]);

        try {
            // Establecer la contraseña
            $usuario->contrasena = Hash::make($request->password);
            $usuario->save();

            Log::info("Contraseña establecida para usuario de Google: {$usuario->correo_electronico}");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Contraseña establecida exitosamente! Ahora puedes hacer login manual con tu correo y contraseña.',
                    'redirect' => route('perfil')
                ]);
            }

            return redirect()->route('perfil')
                ->with('success', '¡Contraseña establecida exitosamente! Ahora puedes hacer login manual con tu correo y contraseña.');

        } catch (\Exception $e) {
            Log::error('Error al establecer contraseña para usuario de Google: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Error al establecer la contraseña. Inténtalo de nuevo.'
                ], 500);
            }
            
            return back()
                ->withErrors(['error' => 'Error al establecer la contraseña. Inténtalo de nuevo.'])
                ->withInput();
        }
    }
}
