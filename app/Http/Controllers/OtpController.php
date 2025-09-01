<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\OtpCode;
use App\Models\Usuario;
use App\Mail\OtpVerification;

class OtpController extends Controller
{
    /**
     * Mostrar la página de verificación OTP
     */
    public function showVerificationForm()
    {
        return view('auth.otp-verification');
    }

    /**
     * Mostrar la página de verificación OTP para registro (con email pre-llenado)
     */
    public function showRegisterVerificationForm()
    {
        $email = session('verification_email');
        if (!$email) {
            return redirect()->route('otp.verify.form');
        }
        
        return view('auth.otp-verification-register', compact('email'));
    }

    /**
     * Enviar código OTP
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,correo_electronico'
        ]);

        try {
            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar si ya tiene un código válido
            if (OtpCode::tieneCodigoValido($usuario->usuario_id, 'email_verification')) {
                $otp = OtpCode::obtenerCodigoValido($usuario->usuario_id, 'email_verification');
                $tiempoRestante = $otp->tiempoRestante();
                
                return response()->json([
                    'success' => false,
                    'message' => "Ya tienes un código válido. Espera {$tiempoRestante} minutos para solicitar uno nuevo."
                ], 429);
            }

            // Crear nuevo código OTP
            $otp = OtpCode::crear($usuario->usuario_id, 'email_verification', 10);

            // Enviar correo
            Mail::to($usuario->correo_electronico)->send(new OtpVerification($usuario, $otp->codigo, 'email_verification', 10));

            Log::info("Código OTP enviado a: {$usuario->correo_electronico}");

            return response()->json([
                'success' => true,
                'message' => 'Código OTP enviado correctamente',
                'tiempo_expiracion' => 10
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando OTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el código OTP'
            ], 500);
        }
    }

    /**
     * Verificar código OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,correo_electronico',
            'codigo' => 'required|string|size:6'
        ]);

        try {
            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar código OTP
            if (OtpCode::verificar($usuario->usuario_id, $request->codigo, 'email_verification')) {
                // Marcar email como verificado
                $usuario->update([
                    'email_verified_at' => now()
                ]);

                Log::info("Email verificado exitosamente: {$usuario->correo_electronico}");

                return response()->json([
                    'success' => true,
                    'message' => 'Email verificado correctamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Código OTP inválido o expirado'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error verificando OTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el código OTP'
            ], 500);
        }
    }

    /**
     * Enviar OTP para restablecimiento de contraseña
     */
    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,correo_electronico'
        ]);

        try {
            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar si ya tiene un código válido
            if (OtpCode::tieneCodigoValido($usuario->usuario_id, 'password_reset')) {
                $otp = OtpCode::obtenerCodigoValido($usuario->usuario_id, 'password_reset');
                $tiempoRestante = $otp->tiempoRestante();
                
                return response()->json([
                    'success' => false,
                    'message' => "Ya tienes un código válido. Espera {$tiempoRestante} minutos para solicitar uno nuevo."
                ], 429);
            }

            // Crear nuevo código OTP
            $otp = OtpCode::crear($usuario->usuario_id, 'password_reset', 10);

            // Enviar correo
            Mail::to($usuario->correo_electronico)->send(new OtpVerification($usuario, $otp->codigo, 'password_reset', 10));

            Log::info("Código OTP para restablecimiento enviado a: {$usuario->correo_electronico}");

            return response()->json([
                'success' => true,
                'message' => 'Código OTP enviado correctamente',
                'tiempo_expiracion' => 10
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando OTP de restablecimiento: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el código OTP'
            ], 500);
        }
    }

    /**
     * Verificar OTP para restablecimiento de contraseña
     */
    public function verifyPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,correo_electronico',
            'codigo' => 'required|string|size:6'
        ]);

        try {
            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar código OTP
            if (OtpCode::verificar($usuario->usuario_id, $request->codigo, 'password_reset')) {
                Log::info("OTP de restablecimiento verificado para: {$usuario->correo_electronico}");

                return response()->json([
                    'success' => true,
                    'message' => 'Código OTP verificado correctamente',
                    'usuario_id' => $usuario->usuario_id
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Código OTP inválido o expirado'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error verificando OTP de restablecimiento: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el código OTP'
            ], 500);
        }
    }

    /**
     * Limpiar códigos OTP expirados (comando de mantenimiento)
     */
    public function limpiarExpirados()
    {
        try {
            $eliminados = OtpCode::limpiarExpirados();
            
            Log::info("Se eliminaron {$eliminados} códigos OTP expirados");
            
            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$eliminados} códigos OTP expirados"
            ]);
        } catch (\Exception $e) {
            Log::error('Error limpiando códigos OTP expirados: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar códigos expirados'
            ], 500);
        }
    }
}
