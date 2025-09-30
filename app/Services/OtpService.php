<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OtpVerification;

class OtpService
{
    /**
     * Enviar código OTP
     */
    public function sendOtp(Request $request): array
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:usuarios,correo_electronico'
            ]);

            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ];
            }

            // Verificar si ya tiene un código válido
            if (OtpCode::tieneCodigoValido($usuario->usuario_id, 'email_verification')) {
                $otp = OtpCode::obtenerCodigoValido($usuario->usuario_id, 'email_verification');
                $tiempoRestante = $otp->tiempoRestante();
                
                return [
                    'success' => false,
                    'message' => "Ya tienes un código válido. Espera {$tiempoRestante} minutos para solicitar uno nuevo."
                ];
            }

            // Crear nuevo código OTP
            $otp = OtpCode::crear($usuario->usuario_id, 'email_verification', 10);

            // Enviar correo
            Mail::to($usuario->correo_electronico)->send(new OtpVerification($usuario, $otp->codigo, 'email_verification', 10));

            Log::info("Código OTP enviado a: {$usuario->correo_electronico}");

            return [
                'success' => true,
                'message' => 'Código OTP enviado correctamente',
                'tiempo_expiracion' => 10
            ];

        } catch (\Exception $e) {
            Log::error('Error enviando OTP: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al enviar el código OTP'
            ];
        }
    }

    /**
     * Verificar código OTP
     */
    public function verifyOtp(Request $request): array
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:usuarios,correo_electronico',
                'codigo' => 'required|string|size:6'
            ]);

            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ];
            }

            // Verificar código OTP
            if (OtpCode::verificar($usuario->usuario_id, $request->codigo, 'email_verification')) {
                // Marcar email como verificado
                $usuario->update([
                    'email_verified_at' => now()
                ]);

                Log::info("Email verificado exitosamente: {$usuario->correo_electronico}");

                return [
                    'success' => true,
                    'message' => 'Email verificado correctamente'
                ];
            }

            return [
                'success' => false,
                'message' => 'Código OTP inválido o expirado'
            ];

        } catch (\Exception $e) {
            Log::error('Error verificando OTP: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al verificar el código OTP'
            ];
        }
    }

    /**
     * Enviar OTP para restablecimiento de contraseña
     */
    public function sendPasswordResetOtp(Request $request): array
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:usuarios,correo_electronico'
            ]);

            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ];
            }

            // Verificar si ya tiene un código válido
            if (OtpCode::tieneCodigoValido($usuario->usuario_id, 'password_reset')) {
                $otp = OtpCode::obtenerCodigoValido($usuario->usuario_id, 'password_reset');
                $tiempoRestante = $otp->tiempoRestante();
                
                // Si han pasado menos de 1 minuto desde la última solicitud, permitir invalidar
                if (OtpCode::puedeSolicitarNuevoCodigo($usuario->usuario_id, 'password_reset', 1)) {
                    // Invalidar códigos existentes y continuar
                    OtpCode::invalidarCodigosExistentes($usuario->usuario_id, 'password_reset');
                    Log::info("Códigos OTP anteriores invalidados para: {$usuario->correo_electronico}");
                } else {
                    return [
                        'success' => false,
                        'message' => "Ya tienes un código válido. Espera {$tiempoRestante} minutos para solicitar uno nuevo."
                    ];
                }
            }

            // Crear nuevo código OTP
            $otp = OtpCode::crear($usuario->usuario_id, 'password_reset', 10);

            // Enviar correo
            Mail::to($usuario->correo_electronico)->send(new OtpVerification($usuario, $otp->codigo, 'password_reset', 10));

            Log::info("Código OTP para restablecimiento enviado a: {$usuario->correo_electronico}");

            return [
                'success' => true,
                'message' => 'Código OTP enviado correctamente',
                'tiempo_expiracion' => 10
            ];

        } catch (\Exception $e) {
            Log::error('Error enviando OTP de restablecimiento: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al enviar el código OTP'
            ];
        }
    }

    /**
     * Verificar OTP para restablecimiento de contraseña
     */
    public function verifyPasswordResetOtp(Request $request): array
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:usuarios,correo_electronico',
                'codigo' => 'required|string|size:6'
            ]);

            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ];
            }

            // Verificar código OTP
            if (OtpCode::verificar($usuario->usuario_id, $request->codigo, 'password_reset')) {
                Log::info("OTP de restablecimiento verificado para: {$usuario->correo_electronico}");

                return [
                    'success' => true,
                    'message' => 'Código OTP verificado correctamente',
                    'usuario_id' => $usuario->usuario_id
                ];
            }

            return [
                'success' => false,
                'message' => 'Código OTP inválido o expirado'
            ];

        } catch (\Exception $e) {
            Log::error('Error verificando OTP de restablecimiento: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al verificar el código OTP'
            ];
        }
    }

    /**
     * Limpiar códigos OTP expirados
     */
    public function limpiarExpirados(): array
    {
        try {
            $eliminados = OtpCode::limpiarExpirados();
            
            Log::info("Se eliminaron {$eliminados} códigos OTP expirados");
            
            return [
                'success' => true,
                'message' => "Se eliminaron {$eliminados} códigos OTP expirados"
            ];
        } catch (\Exception $e) {
            Log::error('Error limpiando códigos OTP expirados: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al limpiar códigos expirados'
            ];
        }
    }
}
