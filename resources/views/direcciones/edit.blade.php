@extends('layouts.landing')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Editar Dirección</h2>
                <span class="text-sm text-gray-500">* Campos requeridos</span>
            </div>

            @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Hay errores en el formulario:</h3>
                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('direcciones.update', $direccion->direccion_id) }}" method="POST" class="space-y-6" id="direccionForm">
                @csrf
                @method('PUT')

                <!-- Nombre del Destinatario -->
                <div>
                    <label for="nombre_destinatario" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Destinatario *
                    </label>
                    <input type="text" 
                           id="nombre_destinatario" 
                           name="nombre_destinatario" 
                           value="{{ old('nombre_destinatario', $direccion->nombre_destinatario) }}"
                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Nombre completo de quien recibirá el pedido</p>
                </div>

                <!-- Provincia -->
                <div>
                    <label for="provincia" class="block text-sm font-medium text-gray-700 mb-1">
                        Provincia *
                    </label>
                    <input type="text" 
                           id="provincia" 
                           name="provincia" 
                           value="{{ old('provincia', $direccion->provincia) }}"
                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Nombre de la provincia</p>
                </div>

                <!-- Ciudad -->
                <div>
                    <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1">
                        Ciudad *
                    </label>
                    <input type="text" 
                           id="ciudad" 
                           name="ciudad" 
                           value="{{ old('ciudad', $direccion->ciudad) }}"
                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           required>
                </div>

                <!-- Departamento -->
                <div>
                    <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">
                        Departamento
                    </label>
                    <input type="text" 
                           id="departamento" 
                           name="departamento" 
                           value="{{ old('departamento', $direccion->departamento) }}"
                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           placeholder="Ej: A, B, 1, 2, etc. (Opcional)">
                    <p class="mt-1 text-sm text-gray-500">Departamento o unidad (opcional)</p>
                </div>

                <!-- Calle -->
                <div>
                    <label for="calle" class="block text-sm font-medium text-gray-700 mb-1">
                        Calle *
                    </label>
                    <input type="text" 
                           id="calle" 
                           name="calle" 
                           value="{{ old('calle', $direccion->calle) }}"
                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Nombre de la calle, avenida o vía</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Número -->
                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700 mb-1">
                            Número *
                        </label>
                        <input type="text" 
                               id="numero" 
                               name="numero" 
                               value="{{ old('numero', $direccion->numero) }}"
                               class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               required>
                        <p class="mt-1 text-sm text-gray-500">Número de la casa o edificio</p>
                    </div>

                    <!-- Piso -->
                    <div>
                        <label for="piso" class="block text-sm font-medium text-gray-700 mb-1">
                            Piso
                        </label>
                        <input type="text" 
                               id="piso" 
                               name="piso" 
                               value="{{ old('piso', $direccion->piso) }}"
                               class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               placeholder="Ej: 5, 3er piso, etc. (Opcional)">
                        <p class="mt-1 text-sm text-gray-500">Piso o nivel (opcional)</p>
                    </div>
                </div>

                <!-- Código Postal -->
                <div>
                    <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-1">
                        Código Postal *
                    </label>
                    <input type="text" 
                           id="codigo_postal" 
                           name="codigo_postal" 
                           value="{{ old('codigo_postal', $direccion->codigo_postal) }}"
                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           maxlength="6"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Formato: 6 dígitos numéricos</p>
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono de contacto *
                    </label>
                    <input type="tel" 
                           id="telefono" 
                           name="telefono" 
                           value="{{ old('telefono', $direccion->telefono) }}"
                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           maxlength="10"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Formato: 10 dígitos numéricos</p>
                </div>

                <!-- Tipo de Dirección -->
                <div>
                    <label for="tipo_direccion" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Dirección
                    </label>
                    <select name="tipo_direccion" 
                            id="tipo_direccion" 
                            class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900">
                        <option value="casa" {{ old('tipo_direccion', $direccion->tipo_direccion) === 'casa' ? 'selected' : '' }}>Casa</option>
                        <option value="apartamento" {{ old('tipo_direccion', $direccion->tipo_direccion) === 'apartamento' ? 'selected' : '' }}>Apartamento</option>
                        <option value="trabajo" {{ old('tipo_direccion', $direccion->tipo_direccion) === 'trabajo' ? 'selected' : '' }}>Trabajo</option>
                        <option value="otro" {{ old('tipo_direccion', $direccion->tipo_direccion) === 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Selecciona el tipo de dirección para mejor organización</p>
                </div>

                <!-- Referencias -->
                <div>
                    <label for="referencias" class="block text-sm font-medium text-gray-700 mb-1">
                        Referencias
                    </label>
                    <textarea id="referencias" 
                              name="referencias" 
                              rows="3"
                              class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                              placeholder="Instrucciones adicionales para la entrega (opcional)">{{ old('referencias', $direccion->referencias) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Información adicional para encontrar la dirección</p>
                </div>

                <!-- País -->
                <div>
                    <label for="pais" class="block text-sm font-medium text-gray-700 mb-1">
                        País
                    </label>
                    <input type="text" 
                           id="pais" 
                           name="pais" 
                           value="{{ old('pais', $direccion->pais ?? 'Argentina') }}"
                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           placeholder="Ej: Argentina">
                    <p class="mt-1 text-sm text-gray-500">País de la dirección</p>
                </div>

                <!-- Dirección Predeterminada -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="predeterminada" 
                               value="1" 
                               {{ old('predeterminada', $direccion->predeterminada) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <span class="ml-2 text-sm text-gray-700">Marcar como dirección predeterminada</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">Esta será tu dirección principal para futuras compras</p>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('direcciones.index') }}" 
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('direccionForm');
    const codigoPostal = document.getElementById('codigo_postal');
    const telefono = document.getElementById('telefono');

    // Validación del formulario antes de enviar
    form.addEventListener('submit', function(e) {
        // Validar campos requeridos
        const required = form.querySelectorAll('[required]');
        let valid = true;

        required.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                valid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (valid) {
            // El formulario se envía automáticamente
            return true;
        } else {
            e.preventDefault();
            Swal.fire({
                title: '¡Error!',
                text: 'Por favor, completa todos los campos requeridos correctamente',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
    });

    // Limpiar errores de validación al escribir
    form.querySelectorAll('input, textarea').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('border-red-500');
        });
    });
</script>
@endsection 