<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Mostrar el formulario de solicitud de restablecimiento de contraseña con OTP
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email-otp');
    }

    /**
     * Enviar código OTP para restablecimiento de contraseña
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,correo_electronico',
        ], [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.exists' => 'No encontramos una cuenta registrada con ese correo electrónico.',
        ]);

        try {
            $usuario = \App\Models\Usuario::where('correo_electronico', $request->email)->first();
            
            // Verificar si ya tiene un código válido
            if (\App\Models\OtpCode::tieneCodigoValido($usuario->usuario_id, 'password_reset')) {
                $otp = \App\Models\OtpCode::obtenerCodigoValido($usuario->usuario_id, 'password_reset');
                $tiempoRestante = $otp->tiempoRestante();
                
                return back()->withErrors([
                    'email' => "Ya tienes un código válido. Espera {$tiempoRestante} minutos para solicitar uno nuevo."
                ]);
            }

            // Crear nuevo código OTP
            $otp = \App\Models\OtpCode::crear($usuario->usuario_id, 'password_reset', 10);

            // Enviar correo
            \Illuminate\Support\Facades\Mail::to($usuario->correo_electronico)
                ->send(new \App\Mail\OtpVerification($usuario, $otp->codigo, 'password_reset', 10));

            \Illuminate\Support\Facades\Log::info("Código OTP para restablecimiento enviado a: {$usuario->correo_electronico}");

            // Crear un token temporal para el email
            $tempToken = base64_encode($request->email . '|' . time());
            
            return back()->with([
                'status' => '¡Código OTP enviado! Revisa tu correo electrónico para continuar.',
                'email_sent' => $request->email,
                'temp_token' => $tempToken
            ])->withInput($request->only('email'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando OTP de restablecimiento: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Error al enviar el código OTP. Intenta nuevamente.'
            ]);
        }
    }
}
