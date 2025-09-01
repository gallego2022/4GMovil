<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRules;

class ResetPasswordController extends Controller
{
    // Cambia este valor si tu campo es 'correo_electronico'
    public function username()
    {
        return 'correo_electronico';
    }

    // Redirección luego del restablecimiento
    protected $redirectTo = '/login';

    /**
     * Mostrar el formulario de restablecimiento de contraseña con OTP
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = null;
        
        // Si hay un token, intentar decodificarlo
        if ($token && $token !== 'otp') {
            try {
                $decoded = base64_decode($token);
                if ($decoded && strpos($decoded, '|') !== false) {
                    list($emailFromToken, $timestamp) = explode('|', $decoded, 2);
                    
                    // Verificar que el token no sea muy antiguo (máximo 1 hora)
                    if (time() - $timestamp < 3600) {
                        $email = $emailFromToken;
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error decodificando token: ' . $e->getMessage());
            }
        }
        
        // Si no hay email del token, intentar otras fuentes
        if (!$email) {
            $email = $request->query('email') ?? $request->email ?? session('email_sent');
        }
        
        // Debug: Log para ver qué está pasando
        \Illuminate\Support\Facades\Log::info('ResetPasswordController - Email sources:', [
            'token' => $token,
            'query_email' => $request->query('email'),
            'request_email' => $request->email,
            'session_email' => session('email_sent'),
            'final_email' => $email
        ]);
        
        // Si no hay email, redirigir al formulario de solicitud
        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Debes solicitar un código OTP primero.']);
        }
        
        // Guardar el email en la sesión para mantenerlo durante el proceso
        session(['email_sent' => $email]);
        
        return view('auth.passwords.reset-otp')->with([
            'email' => $email
        ]);
    }

    /**
     * Restablecer la contraseña usando OTP
     */
    public function reset(Request $request)
    {
        // Validar que el email esté presente y sea válido
        if (!$request->filled('email')) {
            return back()
                ->withInput($request->only('otp_code'))
                ->withErrors(['email' => 'El campo correo electrónico es obligatorio.']);
        }

        $request->validate([
            'email' => 'required|email|exists:usuarios,correo_electronico',
            'otp_code' => 'required|string|size:6',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                PasswordRules::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.exists' => 'No encontramos una cuenta registrada con ese correo electrónico.',
            'otp_code.required' => 'El código OTP es obligatorio.',
            'otp_code.size' => 'El código OTP debe tener 6 dígitos.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.mixed_case' => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un carácter especial.',
        ]);

        try {
            $usuario = \App\Models\Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Usuario no encontrado.']);
            }

            // Verificar código OTP
            if (!\App\Models\OtpCode::verificar($usuario->usuario_id, $request->otp_code, 'password_reset')) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['otp_code' => 'Código OTP inválido o expirado.']);
            }

            // Actualizar contraseña
            $usuario->forceFill([
                'contrasena' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            \Illuminate\Support\Facades\Log::info("Contraseña restablecida exitosamente para: {$usuario->correo_electronico}");

            return redirect()->route('login')
                ->with('status', '¡Tu contraseña ha sido restablecida exitosamente! Ahora puedes iniciar sesión con tu nueva contraseña.')
                ->with('status_type', 'success');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error restableciendo contraseña: ' . $e->getMessage());
            
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Error al restablecer la contraseña. Intenta nuevamente.']);
        }
    }
}

