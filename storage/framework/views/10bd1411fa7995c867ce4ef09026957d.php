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

                <!-- Nombre del Destinatario -->
                <div>
                    <label for="nombre_destinatario" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Destinatario *
                    </label>
                    <input type="text" 
                           name="nombre_destinatario" 
                           id="nombre_destinatario" 
                           class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           required
                           placeholder="Ej: Juan Pérez">
                    <p class="mt-1 text-sm text-gray-500">Nombre completo de quien recibirá el pedido</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Provincia -->
                    <div>
                        <label for="provincia" class="block text-sm font-medium text-gray-700 mb-1">
                            Provincia *
                        </label>
                        <input type="text" 
                               name="provincia" 
                               id="provincia" 
                               class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               required
                               placeholder="Ej: Buenos Aires">
                        <p class="mt-1 text-sm text-gray-500">Escribe el nombre de tu provincia</p>
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
                               placeholder="Ej: Buenos Aires">
                        <p class="mt-1 text-sm text-gray-500">Escribe el nombre de tu ciudad</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Departamento -->
                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">
                            Departamento
                        </label>
                        <input type="text" 
                               name="departamento" 
                               id="departamento" 
                               class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               placeholder="Ej: A, B, 1, 2, etc. (Opcional)">
                        <p class="mt-1 text-sm text-gray-500">Departamento o unidad (opcional)</p>
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
                               maxlength="10"
                               placeholder="Ej: 1001">
                        <p class="mt-1 text-sm text-gray-500">Código postal de tu zona</p>
                    </div>
                </div>

                <!-- Dirección -->
                <div>
                    <label for="calle" class="block text-sm font-medium text-gray-700 mb-1">
                        Calle *
                    </label>
                    <input type="text" 
                           name="calle" 
                           id="calle" 
                           class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           required
                           placeholder="Ej: Av. Corrientes, Calle Florida, etc.">
                    <p class="mt-1 text-sm text-gray-500">Nombre de la calle, avenida o vía</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Número -->
                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700 mb-1">
                            Número *
                        </label>
                        <input type="text" 
                               name="numero" 
                               id="numero" 
                               class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               required
                               placeholder="Ej: 1234">
                        <p class="mt-1 text-sm text-gray-500">Número de la casa o edificio</p>
                    </div>

                    <!-- Piso -->
                    <div>
                        <label for="piso" class="block text-sm font-medium text-gray-700 mb-1">
                            Piso
                        </label>
                        <input type="text" 
                               name="piso" 
                               id="piso" 
                               class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                               placeholder="Ej: 5, 3er piso, etc. (Opcional)">
                        <p class="mt-1 text-sm text-gray-500">Piso o nivel (opcional)</p>
                    </div>
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono de Contacto *
                    </label>
                    <input type="tel" 
                           name="telefono" 
                           id="telefono" 
                           class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           required
                           maxlength="20"
                           placeholder="Ej: +54 11 1234-5678">
                    <p class="mt-1 text-sm text-gray-500">Teléfono donde contactarte para la entrega</p>
                </div>

                <!-- Tipo de Dirección -->
                <div>
                    <label for="tipo_direccion" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Dirección
                    </label>
                    <select name="tipo_direccion" 
                            id="tipo_direccion" 
                            class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900">
                        <option value="casa">Casa</option>
                        <option value="apartamento">Apartamento</option>
                        <option value="trabajo">Trabajo</option>
                        <option value="otro">Otro</option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Selecciona el tipo de dirección para mejor organización</p>
                </div>

                <!-- Referencias -->
                <div>
                    <label for="referencias" class="block text-sm font-medium text-gray-700 mb-1">
                        Referencias
                    </label>
                    <textarea name="referencias" 
                              id="referencias" 
                              rows="3" 
                              class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                              placeholder="Edificio azul, piso 3, timbre 302. Referencias adicionales para encontrar la dirección."></textarea>
                    <p class="mt-1 text-sm text-gray-500">Agrega cualquier información adicional que ayude a encontrar la dirección</p>
                </div>

                <!-- País -->
                <div>
                    <label for="pais" class="block text-sm font-medium text-gray-700 mb-1">
                        País
                    </label>
                    <input type="text" 
                           name="pais" 
                           id="pais" 
                           class="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-gray-900"
                           value="Colombia"
                           placeholder="Ej: Colombia">
                    <p class="mt-1 text-sm text-gray-500">País de la dirección (por defecto: Colombia)</p>
                </div>

                <!-- Dirección Predeterminada -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="predeterminada" 
                               value="1" 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <span class="ml-2 text-sm text-gray-700">Marcar como dirección predeterminada</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">Esta será tu dirección principal para futuras compras</p>
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
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\usuario\Documents\GitHub\4GMovil\resources\views/direcciones/create.blade.php ENDPATH**/ ?>