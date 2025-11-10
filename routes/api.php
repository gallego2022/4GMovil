<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\JwtController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas JWT - Autenticación
Route::prefix('jwt')->name('jwt.')->group(function () {
    // Login y generación de token
    Route::post('/login', [JwtController::class, 'login'])->name('login');
    
    // Documentación/información del endpoint de login (GET)
    Route::get('/login', function () {
        return response()->json([
            'message' => 'Este endpoint requiere método POST',
            'endpoint' => '/api/jwt/login',
            'method' => 'POST',
            'description' => 'Autentica un usuario y genera un token JWT',
            'required_fields' => [
                'correo_electronico' => 'string|email|required',
                'contrasena' => 'string|required'
            ],
            'example' => [
                'url' => '/api/jwt/login',
                'method' => 'POST',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'body' => [
                    'correo_electronico' => 'admin@example.com',
                    'contrasena' => 'password'
                ]
            ],
            'response_success' => [
                'success' => true,
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'usuario' => [
                    'id' => 1,
                    'nombre' => 'Admin',
                    'email' => 'admin@example.com',
                    'rol' => 'admin'
                ]
            ],
            'other_endpoints' => [
                'POST /api/jwt/token' => 'Generar token para usuario autenticado (requiere sesión)',
                'POST /api/jwt/refresh' => 'Refrescar token JWT',
                'POST /api/jwt/validate' => 'Validar token JWT',
                'GET /api/jwt/validate' => 'Validar token JWT (GET)'
            ]
        ]); // Documentación del endpoint (GET devuelve info, POST es el método correcto)
    })->name('login.info');
    
    // Generar token para usuario autenticado (requiere sesión)
    Route::middleware('auth')->post('/token', [JwtController::class, 'generateToken'])->name('token');
    
    // Refrescar token
    Route::post('/refresh', [JwtController::class, 'refreshToken'])->name('refresh');
    
    // Validar token
    Route::post('/validate', [JwtController::class, 'validateToken'])->name('validate');
    Route::get('/validate', [JwtController::class, 'validateToken'])->name('validate.get');
});

// Nota: Las rutas de validación en tiempo real (check-sku, check-field-name, check-email)
// han sido movidas a routes/admin.php para que funcionen correctamente con la URL /admin/api/...
// sin el prefijo /api adicional que se agrega automáticamente a las rutas en api.php 