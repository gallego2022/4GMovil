<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta sección.');
        }

        // Verificar si el usuario tiene rol de admin
        if (Auth::user()->rol !== 'admin') {
            return redirect()->route('perfil')
                ->with('error', 'No tienes permisos de administrador para acceder a esta sección.');
        }

        return $next($request);
    }
}
