<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AdminCheck
{
    public function verificarAdmin()
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') {
            return redirect()->route('perfil')->with('error', 'No tienes permiso para acceder.');
        }

        return null; // Significa que sí es admin
    }
}
