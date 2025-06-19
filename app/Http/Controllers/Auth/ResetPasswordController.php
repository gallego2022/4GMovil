<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    // Cambia este valor si tu campo es 'correo_electronico'
    public function username()
    {
        return 'correo_electronico';
    }

    // Redirección luego del restablecimiento
    protected $redirectTo = '/perfil';
}

