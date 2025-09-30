@extends('layouts.landing')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Confirmar Pago
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                Tu pedido ha sido creado y el stock ha sido reservado. Confirma tu pago para completar la compra.
            </p>
        </div>

        @if(session('mensaje'))
            <div class="mb-6 rounded-md p-4 {{ session('tipo', 'success') === 'success' ? 'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-200' }}">
                <div class="flex">
                    <div class="flex-shrink-0">
                        @if(session('tipo', 'success') === 'success')
                            <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('mensaje') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Información del Pedido -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
                    Detalles del Pedido
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Número de Pedido:</span>
                        <span class="text-gray-900 dark:text-white">#{{ $pedido->pedido_id }}</span>
                    </div>
                    
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Fecha:</span>
                        <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Método de Pago:</span>
                        <span class="text-gray-900 dark:text-white">{{ $pedido->pago->metodoPago->nombre ?? 'N/A' }}</span>
                    </div>
                    
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Estado:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">
                            Pendiente de Confirmación
                        </span>
                    </div>
                </div>

                <!-- Productos -->
                <div class="mt-6">
                    <h3 class="text-lg font-medium mb-3 text-gray-900 dark:text-white">Productos</h3>
                    <div class="space-y-3">
                        @foreach($pedido->detalles as $detalle)
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                        {{ $detalle->producto->nombre_producto }}
                                    </h4>
                                    @if($detalle->variante_id)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Variante: {{ $detalle->variante->nombre_variante ?? 'N/A' }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $detalle->cantidad }} x ${{ number_format($detalle->producto->precio, 0, ',', '.') }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Subtotal: ${{ number_format($detalle->cantidad * $detalle->producto->precio, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Total -->
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Total:</span>
                        <span class="text-xl font-bold text-primary">${{ number_format($pedido->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Información de Envío y Confirmación -->
            <div class="space-y-6">
                <!-- Dirección de Envío -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                        Dirección de Envío
                    </h3>
                    
                    @if($pedido->direccion)
                        <div class="space-y-2">
                            <p class="text-gray-900 dark:text-white">
                                <strong>{{ $pedido->direccion->nombre_direccion }}</strong>
                            </p>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ $pedido->direccion->direccion_completa }}
                            </p>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ $pedido->direccion->ciudad }}, {{ $pedido->direccion->departamento }}
                            </p>
                            <p class="text-gray-600 dark:text-gray-400">
                                Teléfono: {{ $pedido->direccion->telefono }}
                            </p>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No hay dirección registrada</p>
                    @endif
                </div>

                <!-- Confirmación de Pago -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                        Confirmar Pago
                    </h3>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    Información Importante
                                </h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                    Al confirmar el pago, el stock será descontado definitivamente y tu pedido será procesado.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('checkout.confirmar', $pedido->pedido_id) }}" method="POST" id="confirmForm">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="confirmar_pago" required 
                                           class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Confirmo que he realizado el pago y deseo procesar mi pedido
                                    </span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="aceptar_terminos" required 
                                           class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Acepto los términos y condiciones de la compra
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex space-x-4">
                            <button type="submit" 
                                    id="confirmarBtn"
                                    class="flex-1 bg-primary text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="buttonText">Confirmar Pago</span>
                                <span id="spinner" class="hidden ml-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                            
                            <a href="{{ route('landing') }}" 
                               class="flex-1 bg-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-400 transition-colors text-center">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('confirmForm');
    const confirmBtn = document.getElementById('confirmarBtn');
    const buttonText = document.getElementById('buttonText');
    const spinner = document.getElementById('spinner');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar que ambos checkboxes estén marcados
        const confirmarPago = form.querySelector('input[name="confirmar_pago"]').checked;
        const aceptarTerminos = form.querySelector('input[name="aceptar_terminos"]').checked;
        
        if (!confirmarPago || !aceptarTerminos) {
            Swal.fire({
                title: 'Error',
                text: 'Debes marcar ambas casillas para continuar',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0088ff'
            });
            return;
        }
        
        // Mostrar confirmación final
        Swal.fire({
            title: '¿Confirmar Pago?',
            text: 'Esta acción procesará tu pedido y descontará el stock definitivamente. ¿Estás seguro?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0088ff',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar botón y mostrar spinner
                confirmBtn.disabled = true;
                buttonText.textContent = 'Procesando...';
                spinner.classList.remove('hidden');
                
                // Enviar formulario
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection