<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\OtpCode;

class RequireEmailVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $usuario = Auth::user();
            
            // Si el usuario no tiene email verificado
            if (!$usuario->email_verified_at) {
                // Enviar nuevo código OTP si no tiene uno válido
                if (!OtpCode::tieneCodigoValido($usuario->usuario_id, 'email_verification')) {
                    $usuario->sendEmailVerificationNotification();
                }
                
                return redirect()->route('otp.verify.register')
                    ->with('verification_email', $usuario->correo_electronico)
                    ->with('error', 'Debes verificar tu correo electrónico para acceder a esta sección.');
            }
        }

        return $next($request);
    }
}
