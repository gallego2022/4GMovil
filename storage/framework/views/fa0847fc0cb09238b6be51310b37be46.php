<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Mensaje de éxito/error -->
        <?php if(session('mensaje')): ?>
            <div class="mb-4 rounded-md p-4 <?php echo e(session('tipo', 'success') === 'success' ? 'bg-green-50 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200'); ?>">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <?php if(session('tipo', 'success') === 'success'): ?>
                            <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        <?php else: ?>
                            <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium"><?php echo e(session('mensaje')); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Sección de Foto de Perfil -->
                    <div class="md:col-span-1">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md text-center">
                            <div class="relative inline-block">
                                <?php if($usuario->foto_perfil): ?>
                                    <?php
                                        $photoUrl = \App\Helpers\PhotoHelper::getPhotoUrl($usuario->foto_perfil);
                                    ?>
                                    <div class="profile-image-container w-48 h-48 rounded-full overflow-hidden border-4 border-blue-200 dark:border-blue-700 mx-auto">
                                        <img src="<?php echo e($photoUrl); ?>" 
                                             alt="<?php echo e(__('profile.photo_alt', ['name' => $usuario->nombre_usuario])); ?>" 
                                             class="profile-image">
                                    </div>
                                    <button type="button" 
                                            onclick="confirmarEliminarFoto()"
                                            class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                <?php else: ?>
                                    <div class="w-48 h-48 mx-auto rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-24 h-24 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h2 class="mt-4 text-xl font-semibold text-gray-800 dark:text-white"><?php echo e($usuario->nombre_usuario); ?></h2>
                            <p class="text-gray-500 dark:text-gray-400"><?php echo e($usuario->correo_electronico); ?></p>
                            <br>
                            <!-- Botón para cambiar contraseña -->
                            <button type="button" 
                                   onclick="showChangePasswordModal()"
                                   class="inline-flex justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                <?php echo e(__('profile.change_password')); ?>

                            </button>
                        </div>
                    </div>

                    <!-- Formulario de Edición -->
                    <div class="md:col-span-2">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6"><?php echo e(__('profile.edit_profile')); ?></h3>

                            <?php if($errors->any()): ?>
                                <div class="mb-4 bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <ul class="list-disc list-inside text-sm">
                                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form action="<?php echo e(route('perfil.actualizar')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                <!-- Nombre de Usuario -->
                                <div>
                                    <label for="nombre_usuario" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <?php echo e(__('profile.nombre_usuario')); ?>

                                    </label>
                                    <input type="text" 
                                           id="nombre_usuario" 
                                           name="nombre_usuario" 
                                           value="<?php echo e(old('nombre_usuario', $usuario->nombre_usuario)); ?>" 
                                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" 
                                           required>
                                </div>

                                <!-- Correo Electrónico -->
                                <div>
                                    <label for="correo_electronico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <?php echo e(__('profile.correo_electronico')); ?>

                                    </label>
                                    <input type="email" 
                                           id="correo_electronico" 
                                           name="correo_electronico" 
                                           value="<?php echo e(old('correo_electronico', $usuario->correo_electronico)); ?>" 
                                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" 
                                           required>
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <?php echo e(__('profile.telefono')); ?>

                                    </label>
                                    <input type="tel" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="<?php echo e(old('telefono', $usuario->telefono)); ?>" 
                                           class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                                </div>

                                <!-- Foto de Perfil -->
                                <div>
                                    <label for="foto_perfil" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <?php echo e(__('profile.foto_perfil')); ?>

                                    </label>
                                    <input type="file" 
                                           id="foto_perfil" 
                                           name="foto_perfil" 
                                           accept="image/jpeg,image/png,image/webp" 
                                            class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-600 file:text-gray-900 hover:file:bg-brand-700">
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        <?php echo e(__('profile.photo_requirements')); ?>

                                    </p>
                                </div>

                                <!-- Botón de Guardar -->
                                <div class="flex justify-end">
                                    <button type="submit" 
                                                    class="inline-flex justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-emerald-600 hover:to-teal-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 hover:shadow-xl">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <?php echo e(__('profile.save_changes')); ?>

                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('components.change-password-modal', [
    'title' => 'Cambiar Contraseña',
    'currentPasswordLabel' => 'Contraseña Actual',
    'newPasswordLabel' => 'Nueva Contraseña',
    'confirmPasswordLabel' => 'Confirmar Nueva Contraseña',
    'newPasswordPlaceholder' => 'Mínimo 8 caracteres',
    'confirmPasswordPlaceholder' => 'Repite tu nueva contraseña',
    'passwordRequirements' => 'Debe contener mayúscula, minúscula, número y símbolo',
    'cancelButtonText' => 'Cancelar',
    'submitButtonText' => 'Cambiar Contraseña'
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
// Funciones para el modal de cambio de contraseña
// Todas las funciones están incluidas en el componente reutilizable

function confirmarEliminarFoto() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '<?php echo e(__("profile.confirm_delete_photo")); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear un formulario temporal para enviar la petición DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo e(route("perfil.eliminarFoto")); ?>';
            
            // Agregar el token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrfToken);
            
            // Agregar el método DELETE
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            // Enviar la petición con fetch para manejar la respuesta
            fetch('<?php echo e(route("perfil.eliminarFoto")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.tipo === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.mensaje,
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                    }).then(() => {
                        // Recargar la página para mostrar los cambios
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.mensaje || 'Ocurrió un error al eliminar la foto',
                        background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar la foto',
                    background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                });
                console.error('Error:', error);
            });
        }
    });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\usuario\Documents\GitHub\4GMovil\resources\views/modules/auth/perfil.blade.php ENDPATH**/ ?>