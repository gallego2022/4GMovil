

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Agregar Nueva Dirección</h2>
                <span class="text-sm text-gray-500">* Campos requeridos</span>
            </div>

            <?php if($errors->any()): ?>
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
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <form action="<?php echo e(route('direcciones.store')); ?>" method="POST" class="space-y-6" id="direccionForm">
                <?php echo csrf_field(); ?>

                <!-- Tipo de Dirección -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Dirección *
                    </label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipo_direccion" value="casa" class="form-radio text-blue-600" checked>
                            <span class="ml-2">Casa</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipo_direccion" value="apartamento" class="form-radio text-blue-600">
                            <span class="ml-2">Apartamento</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipo_direccion" value="oficina" class="form-radio text-blue-600">
                            <span class="ml-2">Oficina</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Departamento -->
                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">
                            Departamento *
                        </label>
                        <select name="departamento" 
                                id="departamento" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                required>
                            <option value="">Seleccione un departamento</option>
                            <option value="Amazonas">Amazonas</option>
                            <option value="Antioquia">Antioquia</option>
                            <option value="Arauca">Arauca</option>
                            <option value="Atlántico">Atlántico</option>
                            <option value="Bolívar">Bolívar</option>
                            <option value="Boyacá">Boyacá</option>
                            <option value="Caldas">Caldas</option>
                            <option value="Caquetá">Caquetá</option>
                            <option value="Casanare">Casanare</option>
                            <option value="Cauca">Cauca</option>
                            <option value="Cesar">Cesar</option>
                            <option value="Chocó">Chocó</option>
                            <option value="Córdoba">Córdoba</option>
                            <option value="Cundinamarca">Cundinamarca</option>
                            <option value="Guainía">Guainía</option>
                            <option value="Guaviare">Guaviare</option>
                            <option value="Huila">Huila</option>
                            <option value="La Guajira">La Guajira</option>
                            <option value="Magdalena">Magdalena</option>
                            <option value="Meta">Meta</option>
                            <option value="Nariño">Nariño</option>
                            <option value="Norte de Santander">Norte de Santander</option>
                            <option value="Putumayo">Putumayo</option>
                            <option value="Quindío">Quindío</option>
                            <option value="Risaralda">Risaralda</option>
                            <option value="San Andrés y Providencia">San Andrés y Providencia</option>
                            <option value="Santander">Santander</option>
                            <option value="Sucre">Sucre</option>
                            <option value="Tolima">Tolima</option>
                            <option value="Valle del Cauca">Valle del Cauca</option>
                            <option value="Vaupés">Vaupés</option>
                            <option value="Vichada">Vichada</option>
                        </select>
                    </div>

                    <!-- Ciudad -->
                    <div>
                        <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1">
                            Ciudad *
                        </label>
                        <input type="text" 
                               name="ciudad" 
                               id="ciudad" 
                               class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               required
                               placeholder="Ej: Bogotá">
                        <p class="mt-1 text-sm text-gray-500">Escribe el nombre de tu ciudad</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Barrio -->
                    <div>
                        <label for="barrio" class="block text-sm font-medium text-gray-700 mb-1">
                            Barrio *
                        </label>
                        <input type="text" 
                               name="barrio" 
                               id="barrio" 
                               class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               required
                               placeholder="Ej: San José">
                    </div>

                    <!-- Código Postal -->
                    <div>
                        <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-1">
                            Código Postal *
                        </label>
                        <input type="text" 
                               name="codigo_postal" 
                               id="codigo_postal" 
                               class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               required
                               pattern="[0-9]{6}"
                               maxlength="6"
                               placeholder="Ej: 110111">
                        <p class="mt-1 text-sm text-gray-500">
                            <a href="http://visor.codigopostal.gov.co/472/visor/" 
                               target="_blank" 
                               class="text-blue-600 hover:text-blue-800">
                                Buscar mi código postal
                            </a>
                        </p>
                    </div>
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
                               class="px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               placeholder="123"
                               required>
                        <span class="flex items-center justify-center">#</span>
                        <input type="text" 
                               name="numero_casa" 
                               id="numero_casa" 
                               class="px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               placeholder="45-67"
                               required>
                    </div>
                    <input type="text" 
                           name="complemento" 
                           id="complemento" 
                           class="mt-2 w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           placeholder="Apartamento, Interior, Bloque, etc. (Opcional)">
                    <input type="hidden" name="direccion" id="direccion_completa">
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono de Contacto *
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            +57
                        </span>
                        <input type="tel" 
                               name="telefono" 
                               id="telefono" 
                               class="flex-1 px-4 py-3 rounded-none rounded-r-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               required
                               pattern="[0-9]{10}"
                               maxlength="10"
                               placeholder="3001234567">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Ingresa un número de celular válido</p>
                </div>

                <!-- Instrucciones de entrega -->
                <div>
                    <label for="instrucciones" class="block text-sm font-medium text-gray-700 mb-1">
                        Instrucciones de Entrega
                    </label>
                    <textarea name="instrucciones" 
                              id="instrucciones" 
                              rows="3" 
                              class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                              placeholder="Edificio azul, piso 3, timbre 302. Referencias adicionales para encontrar la dirección."></textarea>
                    <p class="mt-1 text-sm text-gray-500">Agrega cualquier información adicional que ayude a encontrar la dirección</p>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 pt-4 border-t">
                    <a href="<?php echo e(url()->previous()); ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Dirección
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('direccionForm');
    const tipoVia = document.getElementById('tipo_via');
    const numeroVia = document.getElementById('numero_via');
    const numeroCasa = document.getElementById('numero_casa');
    const complemento = document.getElementById('complemento');
    const direccionCompleta = document.getElementById('direccion_completa');
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
        } else {
            Swal.fire({
                title: '¡Error!',
                text: 'Por favor, completa todos los campos requeridos correctamente',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Proyecto V11.3\4GMovil\resources\views/direcciones/create.blade.php ENDPATH**/ ?>