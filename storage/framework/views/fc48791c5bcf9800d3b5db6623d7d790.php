<?php $__env->startSection('title', 'Crear Especificación - 4GMovil'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Encabezado -->
    <div>
        <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">Crear Nueva Especificación</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define una nueva especificación técnica para una categoría de productos</p>
    </div>

    <!-- Formulario -->
    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-800/5 sm:rounded-xl md:col-span-2">
        <form action="<?php echo e(route('admin.especificaciones.store')); ?>" method="POST" class="px-4 py-6 sm:p-8">
            <?php echo csrf_field(); ?>
            
            <!-- Sección: Información Básica -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Información Básica
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Categoría -->
                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <select id="categoria_id" name="categoria_id" required
                                class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200">
                            <option value="">Selecciona una categoría</option>
                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($categoria->categoria_id); ?>" <?php echo e(old('categoria_id') == $categoria->categoria_id ? 'selected' : ''); ?>>
                                    <?php echo e($categoria->nombre); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['categoria_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Orden -->
                    <div>
                        <label for="orden" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Orden de Visualización
                        </label>
                        <input type="number" id="orden" name="orden" value="<?php echo e(old('orden')); ?>" min="0"
                               class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200"
                               placeholder="Dejar vacío para auto-asignar">
                        <?php $__errorArgs = ['orden'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Sección: Configuración del Campo -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Configuración del Campo
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre del Campo -->
                    <div>
                        <label for="nombre_campo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre del Campo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre_campo" name="nombre_campo" value="<?php echo e(old('nombre_campo')); ?>" required
                               class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200"
                               placeholder="ej: ram, almacenamiento, pantalla">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Nombre técnico del campo (sin espacios, solo minúsculas)
                        </p>
                        <?php $__errorArgs = ['nombre_campo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Etiqueta -->
                    <div>
                        <label for="etiqueta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Etiqueta <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="etiqueta" name="etiqueta" value="<?php echo e(old('etiqueta')); ?>" required
                               class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200"
                               placeholder="ej: Memoria RAM, Almacenamiento, Tamaño de Pantalla">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Nombre visible para el usuario
                        </p>
                        <?php $__errorArgs = ['etiqueta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Tipo de Campo -->
                    <div>
                        <label for="tipo_campo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo de Campo <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_campo" name="tipo_campo" required
                                class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200">
                            <option value="">Selecciona el tipo</option>
                            <option value="text" <?php echo e(old('tipo_campo') == 'text' ? 'selected' : ''); ?>>Texto</option>
                            <option value="textarea" <?php echo e(old('tipo_campo') == 'textarea' ? 'selected' : ''); ?>>Área de texto</option>
                            <option value="number" <?php echo e(old('tipo_campo') == 'number' ? 'selected' : ''); ?>>Número</option>
                            <option value="select" <?php echo e(old('tipo_campo') == 'select' ? 'selected' : ''); ?>>Selector</option>
                            <option value="checkbox" <?php echo e(old('tipo_campo') == 'checkbox' ? 'selected' : ''); ?>>Casilla de verificación</option>
                            <option value="radio" <?php echo e(old('tipo_campo') == 'radio' ? 'selected' : ''); ?>>Botones de radio</option>
                            <option value="date" <?php echo e(old('tipo_campo') == 'date' ? 'selected' : ''); ?>>Fecha</option>
                            <option value="email" <?php echo e(old('tipo_campo') == 'email' ? 'selected' : ''); ?>>Email</option>
                            <option value="url" <?php echo e(old('tipo_campo') == 'url' ? 'selected' : ''); ?>>URL</option>
                        </select>
                        <?php $__errorArgs = ['tipo_campo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Unidad -->
                    <div>
                        <label for="unidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Unidad de Medida
                        </label>
                        <input type="text" id="unidad" name="unidad" value="<?php echo e(old('unidad')); ?>"
                               class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200"
                               placeholder="ej: GB, pulgadas, MP, mAh">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Unidad de medida (opcional)
                        </p>
                        <?php $__errorArgs = ['unidad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Opciones (solo para select, radio, checkbox) -->
                <div id="opciones_container" class="mt-6 hidden">
                    <label for="opciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Opciones Disponibles
                    </label>
                    <textarea id="opciones" name="opciones" rows="3"
                              class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200"
                              placeholder="Ingresa las opciones separadas por comas (ej: 2GB, 4GB, 8GB, 16GB)"><?php echo e(old('opciones')); ?></textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Separa cada opción con una coma
                    </p>
                    <?php $__errorArgs = ['opciones'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- Sección: Configuración Avanzada -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Configuración Avanzada
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Requerido -->
                    <div class="flex items-center">
                        <input type="checkbox" id="requerido" name="requerido" value="1" <?php echo e(old('requerido') ? 'checked' : ''); ?>

                               class="h-5 w-5 text-brand-600 focus:ring-brand-500 border-gray-300 rounded transition-colors duration-200">
                        <label for="requerido" class="ml-3 block text-sm text-gray-900 dark:text-gray-100">
                            Campo requerido
                        </label>
                    </div>

                    <!-- Activo -->
                    <div class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" value="1" <?php echo e(old('activo', '1') ? 'checked' : ''); ?>

                               class="h-5 w-5 text-brand-600 focus:ring-brand-500 border-gray-300 rounded transition-colors duration-200">
                        <label for="activo" class="ml-3 block text-sm text-gray-900 dark:text-gray-100">
                            Especificación activa
                        </label>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mt-6">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="3"
                              class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200"
                              placeholder="Descripción opcional del campo"><?php echo e(old('descripcion')); ?></textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Explica para qué sirve este campo
                    </p>
                    <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- Vista Previa -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Vista Previa del Campo
                </h3>
                
                <div id="field_preview" class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-center">
                        Selecciona un tipo de campo para ver la vista previa
                    </p>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="<?php echo e(route('admin.especificaciones.index')); ?>" 
                   class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100 hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200">
                    Cancelar
                </a>
                
                <button type="submit" 
                        class="inline-flex justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-emerald-600 hover:to-teal-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Crear Especificación
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoCampoSelect = document.getElementById('tipo_campo');
    const opcionesContainer = document.getElementById('opciones_container');
    const fieldPreview = document.getElementById('field_preview');
    
    // Mostrar/ocultar opciones según el tipo de campo
    function toggleOpciones() {
        const tipo = tipoCampoSelect.value;
        const tiposConOpciones = ['select', 'radio', 'checkbox'];
        
        if (tiposConOpciones.includes(tipo)) {
            opcionesContainer.classList.remove('hidden');
            opcionesContainer.classList.add('block');
        } else {
            opcionesContainer.classList.add('hidden');
            opcionesContainer.classList.remove('block');
        }
        
        updatePreview();
    }
    
    // Actualizar vista previa
    function updatePreview() {
        const tipo = tipoCampoSelect.value;
        const etiqueta = document.getElementById('etiqueta').value || 'Etiqueta del Campo';
        const unidad = document.getElementById('unidad').value;
        const opciones = document.getElementById('opciones').value;
        const requerido = document.getElementById('requerido').checked;
        
        if (!tipo) {
            fieldPreview.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">Selecciona un tipo de campo para ver la vista previa</p>';
            return;
        }
        
        let previewHTML = '';
        const labelText = etiqueta + (requerido ? ' *' : '') + (unidad ? ` (${unidad})` : '');
        
        switch (tipo) {
            case 'text':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="text" class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200" placeholder="Ingresa ${etiqueta.toLowerCase()}">
                `;
                break;
                
            case 'textarea':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <textarea rows="3" class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200" placeholder="Ingresa ${etiqueta.toLowerCase()}"></textarea>
                `;
                break;
                
            case 'number':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="number" class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200" placeholder="Ingresa un número">
                `;
                break;
                
            case 'select':
                if (opciones) {
                    const opcionesArray = opciones.split(',').map(opt => opt.trim());
                    let optionsHTML = '<option value="">Selecciona una opción</option>';
                    opcionesArray.forEach(opt => {
                        optionsHTML += `<option value="${opt}">${opt}</option>`;
                    });
                    
                    previewHTML = `
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                        <select class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200">
                            ${optionsHTML}
                        </select>
                    `;
                } else {
                    previewHTML = `
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                        <select class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200">
                            <option value="">Selecciona una opción</option>
                            <option value="opcion1">Opción 1</option>
                            <option value="opcion2">Opción 2</option>
                        </select>
                    `;
                }
                break;
                
            case 'checkbox':
                previewHTML = `
                    <div class="flex items-center">
                        <input type="checkbox" class="h-5 w-5 text-brand-600 focus:ring-brand-500 border-gray-300 rounded transition-colors duration-200">
                        <label class="ml-3 block text-sm text-gray-900 dark:text-gray-100">${labelText}</label>
                    </div>
                `;
                break;
                
            case 'radio':
                if (opciones) {
                    const opcionesArray = opciones.split(',').map(opt => opt.trim());
                    let radioHTML = '';
                    opcionesArray.forEach((opt, index) => {
                        radioHTML += `
                            <div class="flex items-center">
                                <input type="radio" name="preview_radio" value="${opt}" class="h-5 w-5 text-brand-600 focus:ring-brand-500 border-gray-300 transition-colors duration-200">
                                <label class="ml-3 block text-sm text-gray-900 dark:text-gray-100">${opt}</label>
                            </div>
                        `;
                    });
                    
                    previewHTML = `
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                        <div class="space-y-2">
                            ${radioHTML}
                        </div>
                    `;
                } else {
                    previewHTML = `
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" name="preview_radio" value="opcion1" class="h-5 w-5 text-brand-600 focus:ring-brand-500 border-gray-300 transition-colors duration-200">
                                <label class="ml-3 block text-sm text-gray-900 dark:text-gray-100">Opción 1</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="preview_radio" value="opcion2" class="h-5 w-5 text-brand-600 focus:ring-brand-500 border-gray-300 transition-colors duration-200">
                                <label class="ml-3 block text-sm text-gray-900 dark:text-gray-100">Opción 2</label>
                            </div>
                        </div>
                    `;
                }
                break;
                
            case 'date':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="date" class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200">
                `;
                break;
                
            case 'email':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="email" class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200" placeholder="ejemplo@correo.com">
                `;
                break;
                
            case 'url':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="url" class="block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200" placeholder="https://ejemplo.com">
                `;
                break;
        }
        
        fieldPreview.innerHTML = previewHTML;
    }
    
    // Event listeners
    tipoCampoSelect.addEventListener('change', toggleOpciones);
    document.getElementById('etiqueta').addEventListener('input', updatePreview);
    document.getElementById('unidad').addEventListener('input', updatePreview);
    document.getElementById('opciones').addEventListener('input', updatePreview);
    document.getElementById('requerido').addEventListener('change', updatePreview);
    
    // Inicializar
    toggleOpciones();
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\usuario\Documents\GitHub\4GMovil\resources\views/pages/admin/especificaciones/create.blade.php ENDPATH**/ ?>