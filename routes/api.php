<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Rutas API para validaciones en tiempo real
Route::prefix('admin/api')->middleware(['auth', 'admin'])->group(function () {
    // Verificar SKU único
    Route::get('/check-sku', function (Request $request) {
        $sku = $request->query('sku');
        $productId = $request->query('product_id'); // Para edición
        
        if (!$sku) {
            return response()->json(['exists' => false]);
        }
        
        $query = \App\Models\Producto::where('sku', $sku);
        
        // Si es edición, excluir el producto actual
        if ($productId) {
            $query->where('producto_id', '!=', $productId);
        }
        
        $exists = $query->exists();
        
        return response()->json(['exists' => $exists]);
    });
    
    // Verificar nombre de campo único en especificaciones
    Route::get('/check-field-name', function (Request $request) {
        $name = $request->query('name');
        $categoryId = $request->query('category');
        $specificationId = $request->query('specification_id'); // Para edición
        
        if (!$name || !$categoryId) {
            return response()->json(['exists' => false]);
        }
        
        $query = \App\Models\EspecificacionCategoria::where('categoria_id', $categoryId)
            ->where('nombre_campo', $name);
        
        // Si es edición, excluir la especificación actual
        if ($specificationId) {
            $query->where('especificacion_id', '!=', $specificationId);
        }
        
        $exists = $query->exists();
        
        return response()->json(['exists' => $exists]);
    });
    
    // Verificar email único
    Route::get('/check-email', function (Request $request) {
        $email = $request->query('email');
        $userId = $request->query('user_id'); // Para edición
        
        if (!$email) {
            return response()->json(['exists' => false]);
        }
        
        $query = \App\Models\User::where('email', $email);
        
        // Si es edición, excluir el usuario actual
        if ($userId) {
            $query->where('id', '!=', $userId);
        }
        
        $exists = $query->exists();
        
        return response()->json(['exists' => $exists]);
    });
}); 