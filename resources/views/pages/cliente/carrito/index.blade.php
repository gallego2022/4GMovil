@extends('layouts.landing')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Mi Carrito de Compras</h1>
                <p class="text-gray-600">Gestiona tus productos en el carrito de compras.</p>
            </div>
        </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Productos en tu carrito</h5>
                </div>
                <div class="card-body">
                    <div id="carrito-completo">
                        <div class="carrito-items">
                            <!-- Los items del carrito se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Resumen del Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total de items:</span>
                        <span id="carrito-cantidad">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total a pagar:</span>
                        <span class="fw-bold text-primary" id="carrito-total">$0</span>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card me-2"></i>
                            Proceder al Pago
                        </a>
                        <button class="btn btn-outline-danger" onclick="carritoController.limpiarCarrito()">
                            <i class="fas fa-trash me-2"></i>
                            Limpiar Carrito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Prueba de Funcionamiento</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Agregar Producto de Prueba</h6>
                            <form id="form-agregar-prueba">
                                @csrf
                                <div class="mb-3">
                                    <label for="producto_id" class="form-label">Producto ID</label>
                                    <input type="number" class="form-control" id="producto_id" name="producto_id" value="1" min="1">
                                </div>
                                <div class="mb-3">
                                    <label for="variante_id" class="form-label">Variante ID (opcional)</label>
                                    <input type="number" class="form-control" id="variante_id" name="variante_id" value="" min="1">
                                </div>
                                <div class="mb-3">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" value="1" min="1">
                                </div>
                                <button type="button" class="btn btn-success btn-agregar-carrito">
                                    <i class="fas fa-plus me-2"></i>
                                    Agregar al Carrito
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h6>Información de Debug</h6>
                            <div class="alert alert-info">
                                <small>
                                    <strong>Estado actual:</strong><br>
                                    Items en carrito: <span id="debug-items">0</span><br>
                                    Total: <span id="debug-total">$0</span><br>
                                    Usuario autenticado: {{ Auth::check() ? 'Sí' : 'No' }}<br>
                                    ID Usuario: {{ Auth::id() ?? 'N/A' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/carrito.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar información de debug
    function actualizarDebug() {
        document.getElementById('debug-items').textContent = window.carritoController?.cantidadItems || 0;
        document.getElementById('debug-total').textContent = '$' + (window.carritoController?.total || 0).toLocaleString();
    }
    
    // Actualizar cada 2 segundos
    setInterval(actualizarDebug, 2000);
    actualizarDebug();
});
</script>
@endpush
@endsection