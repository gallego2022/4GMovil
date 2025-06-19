@extends('layouts.landing')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-wrap -mx-4">
        <!-- Formulario de checkout -->
        <div class="w-full lg:w-2/3 px-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6">Finalizar Compra</h2>

                @if(session('mensaje'))
                    <div class="mb-4 rounded-md p-4 {{ session('tipo', 'success') === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                @if(session('tipo', 'success') === 'success')
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
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

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Por favor, corrige los siguientes errores:</h3>
                                <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                
                <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    
                    <!-- Dirección de envío -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Dirección de envío</h3>
                        
                        @if($direcciones->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
                                <p class="text-yellow-700">No tienes direcciones guardadas.</p>
                                <a href="{{ route('direcciones.create') }}" class="text-primary hover:text-blue-700 mt-2 inline-block">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Agregar nueva dirección
                                </a>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($direcciones as $direccion)
                                    <div class="border rounded p-4 hover:border-blue-500 transition-colors {{ old('direccion_id') == $direccion->direccion_id ? 'border-blue-500' : '' }}">
                                        <label class="flex items-start cursor-pointer">
                                            <input type="radio" 
                                                   name="direccion_id" 
                                                   value="{{ $direccion->direccion_id }}" 
                                                   class="mt-1" 
                                                   required
                                                   {{ old('direccion_id') == $direccion->direccion_id ? 'checked' : '' }}>
                                            <div class="ml-3">
                                                <div class="flex items-center mb-1">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $direccion->tipo_direccion === 'casa' ? 'bg-green-100 text-green-800' : ($direccion->tipo_direccion === 'apartamento' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                                        {{ ucfirst($direccion->tipo_direccion) }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-900">{{ $direccion->direccion }}</p>
                                                <p class="text-gray-600">{{ $direccion->barrio }}</p>
                                                <p class="text-gray-600">{{ $direccion->ciudad }}, {{ $direccion->departamento }}</p>
                                                <p class="text-gray-600">Teléfono: {{ $direccion->telefono }}</p>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            
                            <a href="{{ route('direcciones.create') }}" class="text-primary hover:text-blue-700 mt-4 inline-block">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Agregar otra dirección
                            </a>
                        @endif

                        @error('direccion_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Método de pago -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Método de pago</h3>
                        
                        <div class="space-y-4">
                            @foreach($metodosPago as $metodo)
                                <div class="border rounded p-4 hover:border-blue-500 transition-colors {{ old('metodo_pago_id') == $metodo->metodo_id ? 'border-blue-500' : '' }}">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" 
                                               name="metodo_pago_id" 
                                               value="{{ $metodo->metodo_id }}" 
                                               class="mr-3" 
                                               required
                                               {{ old('metodo_pago_id') == $metodo->metodo_id ? 'checked' : '' }}>
                                        <span class="font-medium">{{ $metodo->nombre_metodo }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('metodo_pago_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" 
                            id="confirmarPedido"
                            class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $direcciones->isEmpty() ? 'disabled' : '' }}>
                        <span class="inline-flex items-center">
                            <span class="mr-2">Confirmar Pedido</span>
                            <span id="spinner" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </span>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Resumen del pedido -->
        <div class="w-full lg:w-1/3 px-4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="text-lg font-semibold mb-4">Resumen del pedido</h3>
                
                <div class="space-y-4 mb-6">
                    @php $total = 0; @endphp
                    @foreach($cart as $item)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">{{ $item['name'] }}</p>
                                <p class="text-gray-600">Cantidad: {{ $item['quantity'] }}</p>
                            </div>
                            <p class="font-medium">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                        </div>
                        @php $total += $item['price'] * $item['quantity']; @endphp
                    @endforeach
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Envío</span>
                        <span class="font-medium">Gratis</span>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Total</span>
                        <span class="text-primary">${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const submitButton = document.getElementById('confirmarPedido');
    const spinner = document.getElementById('spinner');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validar dirección seleccionada
        const direccionSeleccionada = form.querySelector('input[name="direccion_id"]:checked');
        if (!direccionSeleccionada) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor, selecciona una dirección de envío',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0088ff'
            });
            return;
        }

        // Validar método de pago seleccionado
        const metodoPagoSeleccionado = form.querySelector('input[name="metodo_pago_id"]:checked');
        if (!metodoPagoSeleccionado) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor, selecciona un método de pago',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0088ff'
            });
            return;
        }

        // Deshabilitar el botón y mostrar spinner
        submitButton.disabled = true;
        spinner.classList.remove('hidden');

        // Enviar el formulario
        form.submit();
    });

    // Habilitar/deshabilitar botón según selecciones
    function validarFormulario() {
        const direccionSeleccionada = form.querySelector('input[name="direccion_id"]:checked');
        const metodoPagoSeleccionado = form.querySelector('input[name="metodo_pago_id"]:checked');
        submitButton.disabled = !direccionSeleccionada || !metodoPagoSeleccionado;
    }

    // Agregar listeners para los radio buttons
    form.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', validarFormulario);
    });

    // Validar estado inicial
    validarFormulario();
});
</script>
@endpush
@endsection 