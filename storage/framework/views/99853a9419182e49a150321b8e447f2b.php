<?php $__env->startSection('title', 'Detalle de Pedido #' . $pedido->pedido_id . ' - 4GMovil'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                Pedido #<?php echo e($pedido->pedido_id); ?>

            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Detalle completo del pedido realizado el <?php echo e(\Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y \a \l\a\s H:i')); ?>

            </p>
        </div>
        
        <a href="<?php echo e(route('admin.pedidos.index')); ?>" 
           class="inline-flex items-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l7.158 7.158a.75.75 0 11-1.06 1.06l-8.5-8.5a.75.75 0 010-1.06l8.5-8.5a.75.75 0 111.06 1.06L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
            </svg>
            Volver a Pedidos
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-md bg-green-50 dark:bg-green-900/50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="rounded-md bg-red-50 dark:bg-red-900/50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200"><?php echo e(session('error')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Estado del Pedido -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Estado del Pedido
                    </h3>
                    <form action="<?php echo e(route('admin.pedidos.updateEstado', $pedido->pedido_id)); ?>" method="POST" class="flex items-center space-x-3">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <select name="estado_id" 
                                class="rounded-md px-4 py-3 border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200">
                            <?php $__currentLoopData = \App\Models\EstadoPedido::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($estado->estado_id); ?>" 
                                    <?php echo e($pedido->estado_id == $estado->estado_id ? 'selected' : ''); ?>>
                                    <?php echo e($estado->nombre); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="submit" 
                        class="inline-flex justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-emerald-600 hover:to-teal-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                            Actualizar
                        </button>
                    </form>
                </div>
                
                <div class="mt-4">
                                         <?php
                         $estadoClasses = 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-gray-600/20 dark:ring-gray-600/30';
                         switch(strtolower($pedido->estado->nombre)) {
                             case 'pendiente':
                                 $estadoClasses = 'bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-600/30';
                                 break;
                             case 'confirmado':
                                 $estadoClasses = 'bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-600/30';
                                 break;
                             case 'cancelado':
                                 $estadoClasses = 'bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 ring-red-600/20 dark:ring-red-600/30';
                                 break;
                             default:
                                 $estadoClasses = 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-gray-600/20 dark:ring-gray-600/30';
                         }
                     ?>
                    <span class="inline-flex items-center rounded-md <?php echo e($estadoClasses); ?> px-3 py-1 text-sm font-medium ring-1 ring-inset">
                        <?php echo e($pedido->estado->nombre); ?>

                    </span>
                </div>
            </div>

            <!-- Detalles del Pedido -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Productos del Pedido
                </h3>
                

                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Producto
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Cantidad
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Precio Unitario
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php $__currentLoopData = $pedido->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <?php if($detalle->producto && $detalle->producto->imagenes && $detalle->producto->imagenes->isNotEmpty()): ?>
                                                    <img class="h-10 w-10 rounded-lg object-cover" 
                                                         src="<?php echo e(asset('storage/' . $detalle->producto->imagenes[0]->ruta_imagen)); ?>" 
                                                         alt="<?php echo e($detalle->producto->nombre_producto); ?>">
                                                <?php else: ?>
                                                    <div class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    <?php echo e($detalle->producto->nombre_producto ?? 'Producto no encontrado'); ?>

                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    SKU: <?php echo e($detalle->producto->sku ?? 'N/A'); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <?php echo e($detalle->cantidad ?? 0); ?>

                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        $<?php echo e(number_format($detalle->precio_unitario ?? 0, 0, ',', '.')); ?>

                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        $<?php echo e(number_format(($detalle->cantidad ?? 0) * ($detalle->precio_unitario ?? 0), 0, ',', '.')); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($pedido->detalles->count() == 0): ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No hay productos en este pedido
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Total del Pedido:
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-gray-900 dark:bg-gray-700 dark:text-gray-100">
                                        $<?php echo e(number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.')); ?>

                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna lateral -->
        <div class="space-y-6">
            <!-- Información del Cliente -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Información del Cliente
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($pedido->usuario->nombre_usuario); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($pedido->usuario->correo_electronico); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($pedido->direccion->telefono ?? 'No especificado'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Información de Pago -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Información de Pago
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Método de pago</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <?php echo e(\App\Helpers\PaymentHelper::getPaymentMethodName($pedido)); ?>

                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Estado del pago</label>
                        <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                            <?php echo e(ucfirst($pedido->pago->estado ?? 'Completado')); ?>

                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha del pago</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <?php echo e(\Carbon\Carbon::parse($pedido->pago->fecha_pago)->format('d/m/Y H:i')); ?>

                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Monto pagado</label>
                        <p class="mt-1 text-lg font-bold text-green-600 dark:text-green-400">
                            $<?php echo e(number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.')); ?>

                        </p>
                    </div>
                </div>
            </div>

            <!-- Información de Envío -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Dirección de Envío
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de dirección</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e(ucfirst($pedido->direccion->tipo_direccion)); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Dirección</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($pedido->direccion->direccion); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Barrio</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($pedido->direccion->barrio); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Ciudad</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($pedido->direccion->ciudad); ?>, <?php echo e($pedido->direccion->departamento); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Código Postal</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($pedido->direccion->codigo_postal); ?></p>
                    </div>
                    <?php if($pedido->direccion->instrucciones): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Instrucciones adicionales</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"><?php echo e($pedido->direccion->instrucciones); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: "<?php echo e(session('success')); ?>",
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
    });
</script>
<?php endif; ?>

<?php if(session('error')): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: "<?php echo e(session('error')); ?>",
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
    });
</script>
<?php endif; ?>

<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\pages\admin\pedidos\show.blade.php ENDPATH**/ ?>