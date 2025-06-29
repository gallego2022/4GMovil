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

                <!-- Tipo de dirección -->
                <div>
                    <label for="tipo_direccion" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Dirección *
                    </label>
                    <select name="tipo_direccion" 
                            id="tipo_direccion" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                            required>
                        <option value="casa" {{ old('tipo_direccion', $direccion->tipo_direccion) === 'casa' ? 'selected' : '' }}>Casa</option>
                        <option value="apartamento" {{ old('tipo_direccion', $direccion->tipo_direccion) === 'apartamento' ? 'selected' : '' }}>Apartamento</option>
                        <option value="oficina" {{ old('tipo_direccion', $direccion->tipo_direccion) === 'oficina' ? 'selected' : '' }}>Oficina</option>
                    </select>
                </div>

                <!-- Departamento -->
                <div>
                    <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">
                        Departamento *
                    </label>
                    <select name="departamento" 
                            id="departamento" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                            required>
                        <option value="">Selecciona un departamento</option>
                        <option value="Amazonas" {{ old('departamento', $direccion->departamento) === 'Amazonas' ? 'selected' : '' }}>Amazonas</option>
                        <option value="Antioquia" {{ old('departamento', $direccion->departamento) === 'Antioquia' ? 'selected' : '' }}>Antioquia</option>
                        <option value="Arauca" {{ old('departamento', $direccion->departamento) === 'Arauca' ? 'selected' : '' }}>Arauca</option>
                        <option value="Atlántico" {{ old('departamento', $direccion->departamento) === 'Atlántico' ? 'selected' : '' }}>Atlántico</option>
                        <option value="Bolívar" {{ old('departamento', $direccion->departamento) === 'Bolívar' ? 'selected' : '' }}>Bolívar</option>
                        <option value="Boyacá" {{ old('departamento', $direccion->departamento) === 'Boyacá' ? 'selected' : '' }}>Boyacá</option>
                        <option value="Caldas" {{ old('departamento', $direccion->departamento) === 'Caldas' ? 'selected' : '' }}>Caldas</option>
                        <option value="Caquetá" {{ old('departamento', $direccion->departamento) === 'Caquetá' ? 'selected' : '' }}>Caquetá</option>
                        <option value="Casanare" {{ old('departamento', $direccion->departamento) === 'Casanare' ? 'selected' : '' }}>Casanare</option>
                        <option value="Cauca" {{ old('departamento', $direccion->departamento) === 'Cauca' ? 'selected' : '' }}>Cauca</option>
                        <option value="Cesar" {{ old('departamento', $direccion->departamento) === 'Cesar' ? 'selected' : '' }}>Cesar</option>
                        <option value="Chocó" {{ old('departamento', $direccion->departamento) === 'Chocó' ? 'selected' : '' }}>Chocó</option>
                        <option value="Córdoba" {{ old('departamento', $direccion->departamento) === 'Córdoba' ? 'selected' : '' }}>Córdoba</option>
                        <option value="Cundinamarca" {{ old('departamento', $direccion->departamento) === 'Cundinamarca' ? 'selected' : '' }}>Cundinamarca</option>
                        <option value="Guainía" {{ old('departamento', $direccion->departamento) === 'Guainía' ? 'selected' : '' }}>Guainía</option>
                        <option value="Guaviare" {{ old('departamento', $direccion->departamento) === 'Guaviare' ? 'selected' : '' }}>Guaviare</option>
                        <option value="Huila" {{ old('departamento', $direccion->departamento) === 'Huila' ? 'selected' : '' }}>Huila</option>
                        <option value="La Guajira" {{ old('departamento', $direccion->departamento) === 'La Guajira' ? 'selected' : '' }}>La Guajira</option>
                        <option value="Magdalena" {{ old('departamento', $direccion->departamento) === 'Magdalena' ? 'selected' : '' }}>Magdalena</option>
                        <option value="Meta" {{ old('departamento', $direccion->departamento) === 'Meta' ? 'selected' : '' }}>Meta</option>
                        <option value="Nariño" {{ old('departamento', $direccion->departamento) === 'Nariño' ? 'selected' : '' }}>Nariño</option>
                        <option value="Norte de Santander" {{ old('departamento', $direccion->departamento) === 'Norte de Santander' ? 'selected' : '' }}>Norte de Santander</option>
                        <option value="Putumayo" {{ old('departamento', $direccion->departamento) === 'Putumayo' ? 'selected' : '' }}>Putumayo</option>
                        <option value="Quindío" {{ old('departamento', $direccion->departamento) === 'Quindío' ? 'selected' : '' }}>Quindío</option>
                        <option value="Risaralda" {{ old('departamento', $direccion->departamento) === 'Risaralda' ? 'selected' : '' }}>Risaralda</option>
                        <option value="San Andrés y Providencia" {{ old('departamento', $direccion->departamento) === 'San Andrés y Providencia' ? 'selected' : '' }}>San Andrés y Providencia</option>
                        <option value="Santander" {{ old('departamento', $direccion->departamento) === 'Santander' ? 'selected' : '' }}>Santander</option>
                        <option value="Sucre" {{ old('departamento', $direccion->departamento) === 'Sucre' ? 'selected' : '' }}>Sucre</option>
                        <option value="Tolima" {{ old('departamento', $direccion->departamento) === 'Tolima' ? 'selected' : '' }}>Tolima</option>
                        <option value="Valle del Cauca" {{ old('departamento', $direccion->departamento) === 'Valle del Cauca' ? 'selected' : '' }}>Valle del Cauca</option>
                        <option value="Vaupés" {{ old('departamento', $direccion->departamento) === 'Vaupés' ? 'selected' : '' }}>Vaupés</option>
                        <option value="Vichada" {{ old('departamento', $direccion->departamento) === 'Vichada' ? 'selected' : '' }}>Vichada</option>
                    </select>
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
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                           required>
                </div>

                <!-- Barrio -->
                <div>
                    <label for="barrio" class="block text-sm font-medium text-gray-700 mb-1">
                        Barrio *
                    </label>
                    <input type="text" 
                           id="barrio" 
                           name="barrio" 
                           value="{{ old('barrio', $direccion->barrio) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                           required>
                </div>

                <!-- Dirección -->
                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">
                        Dirección Completa *
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-2">
                        <select name="tipo_via" id="tipo_via" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="Calle">Calle</option>
                            <option value="Carrera">Carrera</option>
                            <option value="Avenida">Avenida</option>
                            <option value="Diagonal">Diagonal</option>
                            <option value="Transversal">Transversal</option>
                        </select>
                        <input type="text" 
                               name="numero_via" 
                               id="numero_via" 
                               class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="123"
                               required>
                        <span class="flex items-center justify-center">#</span>
                        <input type="text" 
                               name="numero_casa" 
                               id="numero_casa" 
                               class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="45-67"
                               required>
                    </div>
                    <input type="text" 
                           id="complemento" 
                           name="complemento" 
                           placeholder="Apto, Interior, etc. (opcional)"
                           class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <input type="hidden" 
                           id="direccion" 
                           name="direccion" 
                           value="{{ old('direccion', $direccion->direccion) }}"
                           required>
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
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
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
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                           maxlength="10"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Formato: 10 dígitos numéricos</p>
                </div>

                <!-- Instrucciones -->
                <div>
                    <label for="instrucciones" class="block text-sm font-medium text-gray-700 mb-1">
                        Instrucciones de entrega
                    </label>
                    <textarea id="instrucciones" 
                              name="instrucciones" 
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                              placeholder="Instrucciones adicionales para la entrega (opcional)">{{ old('instrucciones', $direccion->instrucciones) }}</textarea>
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
    const tipoVia = document.getElementById('tipo_via');
    const numeroVia = document.getElementById('numero_via');
    const numeroCasa = document.getElementById('numero_casa');
    const complemento = document.getElementById('complemento');
    const direccionCompleta = document.getElementById('direccion');
    const codigoPostal = document.getElementById('codigo_postal');
    const telefono = document.getElementById('telefono');

    // Función para actualizar la dirección completa
    function actualizarDireccionCompleta() {
        let direccion = `${tipoVia.value} ${numeroVia.value} # ${numeroCasa.value}`;
        if (complemento.value) {
            direccion += ` ${complemento.value}`;
        }
        direccionCompleta.value = direccion;
    }

    // Eventos para actualizar la dirección completa
    [tipoVia, numeroVia, numeroCasa, complemento].forEach(element => {
        element.addEventListener('input', actualizarDireccionCompleta);
    });

    // Validación de código postal
    codigoPostal.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').substr(0, 6);
    });

    // Validación de teléfono
    telefono.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').substr(0, 10);
    });

    // Validación del formulario antes de enviar
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Actualizar la dirección completa antes de enviar
        actualizarDireccionCompleta();

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

        // Validar formato de teléfono
        if (telefono.value.length !== 10) {
            telefono.classList.add('border-red-500');
            valid = false;
        }

        // Validar formato de código postal
        if (codigoPostal.value.length !== 6) {
            codigoPostal.classList.add('border-red-500');
            valid = false;
        }

        if (valid) {
            this.submit();
        }
    });

    // Inicializar los campos de dirección con la dirección existente
    window.addEventListener('load', function() {
        const direccionActual = direccionCompleta.value;
        if (direccionActual) {
            // Intentar extraer los componentes de la dirección
            const match = direccionActual.match(/^(Calle|Carrera|Avenida|Diagonal|Transversal)\s+(\d+)\s+#\s+(\d+(?:-\d+)?)\s*(.*)$/);
            if (match) {
                tipoVia.value = match[1];
                numeroVia.value = match[2];
                numeroCasa.value = match[3];
                complemento.value = match[4] || '';
            }
        }
    });
</script>
@endsection 