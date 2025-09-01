

<?php $__env->startSection('title', 'Movimientos de Variantes - 4GMovil'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Movimientos de Variantes</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Historial de entradas y salidas de stock por variante</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.inventario.variantes.dashboard')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
                <button onclick="registrarEntrada()" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar Entrada
                </button>
                <button onclick="registrarSalida()" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                    Registrar Salida
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filtros</h3>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Fecha Inicio
                </label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" 
                       value="<?php echo e($fechaInicio->format('Y-m-d')); ?>"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Fecha Fin
                </label>
                <input type="date" name="fecha_fin" id="fecha_fin" 
                       value="<?php echo e($fechaFin->format('Y-m-d')); ?>"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label for="tipo_movimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipo de Movimiento
                </label>
                <select name="tipo_movimiento" id="tipo_movimiento" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Todos</option>
                    <option value="entrada" <?php echo e($tipoMovimiento == 'entrada' ? 'selected' : ''); ?>>Entrada</option>
                    <option value="salida" <?php echo e($tipoMovimiento == 'salida' ? 'selected' : ''); ?>>Salida</option>
                    <option value="ajuste" <?php echo e($tipoMovimiento == 'ajuste' ? 'selected' : ''); ?>>Ajuste</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Resumen de movimientos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Entradas</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        <?php echo e($movimientos->where('tipo_movimiento', 'entrada')->sum('cantidad')); ?>

                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Salidas</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        <?php echo e($movimientos->where('tipo_movimiento', 'salida')->sum('cantidad')); ?>

                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Movimientos</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        <?php echo e($movimientos->count()); ?>

                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de movimientos -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Historial de Movimientos</h3>
        </div>
        
        <?php if($movimientos->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Variante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cantidad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Stock Anterior
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Stock Nuevo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Motivo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Usuario
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movimiento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo e($movimiento->fecha_movimiento->format('d/m/Y')); ?>

                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <?php echo e($movimiento->fecha_movimiento->format('H:i')); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo e($movimiento->variante->producto->nombre_producto ?? 'N/A'); ?>

                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        ID: <?php echo e($movimiento->variante->producto->producto_id ?? 'N/A'); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo e($movimiento->variante->nombre ?? 'N/A'); ?>

                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        ID: <?php echo e($movimiento->variante_id); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($movimiento->tipo_movimiento == 'entrada'): ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Entrada
                                        </span>
                                    <?php elseif($movimiento->tipo_movimiento == 'salida'): ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Salida
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Ajuste
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo e($movimiento->cantidad); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo e($movimiento->stock_anterior); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo e($movimiento->stock_nuevo); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo e($movimiento->motivo); ?>

                                    </div>
                                    <?php if($movimiento->referencia): ?>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Ref: <?php echo e($movimiento->referencia); ?>

                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo e($movimiento->usuario->nombre ?? 'Sistema'); ?>

                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <?php echo e($movimiento->usuario->email ?? 'N/A'); ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if($movimientos->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <?php echo e($movimientos->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="px-6 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay movimientos</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    No se encontraron movimientos con los filtros aplicados.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function registrarEntrada() {
    // Implementar modal para registrar entrada
    alert('Función de registro de entrada en desarrollo');
}

function registrarSalida() {
    // Implementar modal para registrar salida
    alert('Función de registro de salida en desarrollo');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Proyecto V11.3\4GMovil\resources\views/pages/admin/inventario/variantes/movimientos.blade.php ENDPATH**/ ?>