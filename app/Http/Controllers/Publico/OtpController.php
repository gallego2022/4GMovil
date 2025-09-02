<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Base\WebController;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OtpController extends WebController
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
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
        try {
            $result = $this->otpService->sendOtp($request);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'tiempo_expiracion' => $result['tiempo_expiracion']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 429);

        } catch (\Exception $e) {
            Log::error('Error enviando OTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Verificar código OTP
     */
    public function verifyOtp(Request $request)
    {
        try {
            $result = $this->otpService->verifyOtp($request);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error verificando OTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Enviar OTP para restablecimiento de contraseña
     */
    public function sendPasswordResetOtp(Request $request)
    {
        try {
            $result = $this->otpService->sendPasswordResetOtp($request);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'tiempo_expiracion' => $result['tiempo_expiracion']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 429);

        } catch (\Exception $e) {
            Log::error('Error enviando OTP de restablecimiento: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Verificar OTP para restablecimiento de contraseña
     */
    public function verifyPasswordResetOtp(Request $request)
    {
        try {
            $result = $this->otpService->verifyPasswordResetOtp($request);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'usuario_id' => $result['usuario_id']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error verificando OTP de restablecimiento: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Limpiar códigos OTP expirados (comando de mantenimiento)
     */
    public function limpiarExpirados()
    {
        try {
            $result = $this->otpService->limpiarExpirados();
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error limpiando códigos OTP expirados: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
}
