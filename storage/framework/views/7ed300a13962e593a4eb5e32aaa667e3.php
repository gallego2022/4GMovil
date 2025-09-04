<?php $__env->startSection('title', 'Listado de Productos - 4GMovil'); ?>

<?php $__env->startPush('datatables-css'); ?>
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('jquery-script'); ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('datatables-script'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<?php $__env->stopPush(); ?>

<?php
    function getEstadoClasses($estado) {
        $estado = strtolower(trim($estado));
        
        switch($estado) {
            case 'nuevo':
                return [
                    'bg' => 'bg-green-50 dark:bg-green-900',
                    'text' => 'text-green-700 dark:text-green-300',
                    'ring' => 'ring-green-600/20 dark:ring-green-600/30'
                ];
            case 'de exhibicion':
            case 'de exhibición':
            case 'exhibicion':
            case 'exhibición':
                return [
                    'bg' => 'bg-blue-50 dark:bg-blue-900',
                    'text' => 'text-blue-700 dark:text-blue-300',
                    'ring' => 'ring-blue-600/20 dark:ring-blue-600/30'
                ];
            default:
                return [
                    'bg' => 'bg-gray-50 dark:bg-gray-700',
                    'text' => 'text-gray-700 dark:text-gray-300',
                    'ring' => 'ring-gray-600/20 dark:ring-gray-600/30'
                ];
        }
    }
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
         <!-- Vista móvil (cards) -->
     <div class="grid grid-cols-1 gap-4 sm:hidden" id="mobileCards">
         <!-- Encabezado móvil -->
         <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
             <div class="mb-4">
                 <h2 class="text-xl font-bold text-gray-900 dark:text-white">Listado de Productos</h2>
                 <p class="text-sm text-gray-500 dark:text-gray-300">Gestiona los productos de la tienda</p>
        </div>
             
             <!-- Botones de acción móvil -->
             <div class="flex flex-wrap items-center gap-2 mb-4">
                 <!-- Botón Crear Producto -->
            <a href="<?php echo e(route('productos.create')); ?>" 
               class="inline-flex items-center rounded-lg bg-gradient-to-r from-slate-600 to-gray-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-slate-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 hover:shadow-xl min-w-[180px] justify-center">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 transition-transform duration-200 group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Crear Producto
            </a>
                 
                 <!-- Botones de exportación móvil -->
                 <div class="relative group">
                     <button id="exportExcelMovil"
                         class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-full transition duration-300"
                         title="Exportar a Excel">
                         <!-- Excel Icon -->
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M12 4v16m8-8H4" />
                         </svg>
                     </button>
                     <!-- Tooltip para Excel -->
                     <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded px-2 py-1 shadow-lg z-10 whitespace-nowrap">
                         Exportar a Excel
        </div>
    </div>

                 <div class="relative group">
                     <button id="exportPDFMovil"
                         class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition duration-300"
                         title="Exportar a PDF">
                         <!-- PDF Icon -->
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M19 11H5m14 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V11m14-6H5a2 2 0 00-2 2v2h18V7a2 2 0 00-2-2z" />
                </svg>
            </button>
                     <!-- Tooltip para PDF -->
                     <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded px-2 py-1 shadow-lg z-10 whitespace-nowrap">
                         Exportar a PDF
                     </div>
        </div>
    </div>

        <!-- Campo de búsqueda móvil -->
            <div class="relative mt-2 rounded-md shadow-sm">
                <input type="text" 
                       id="busquedaMovil" 
                       class="block w-full rounded-md border-0 py-1.5 pl-4 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-800 sm:text-sm sm:leading-6" 
                       placeholder="Buscar productos...">
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden producto-card">
            <div class="p-4">
                <div class="flex items-start space-x-4">
                    <!-- Imagen del producto -->
                    <div class="flex-shrink-0">
                        <?php if($producto->imagenes->isNotEmpty()): ?>
                        <img src="<?php echo e(asset('storage/' . $producto->imagenes[0]->ruta_imagen)); ?>" 
                             class="h-24 w-24 rounded-lg object-cover shadow-sm" 
                             alt="<?php echo e($producto->nombre_producto); ?>">
                        <?php else: ?>
                        <img src="<?php echo e(asset('img/Logo_2.png')); ?>" 
                             class="h-24 w-24 rounded-lg object-cover shadow-sm" 
                             alt="Sin imagen">
                        <?php endif; ?>
                    </div>
                    <!-- Información del producto -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                            <?php echo e($producto->nombre_producto); ?>

                        </h3>
                        <div class="mt-1 flex flex-col space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                ID: <?php echo e($producto->producto_id); ?>

                            </p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                $<?php echo e(number_format($producto->precio, 2)); ?>

                            </p>
                            
                            <!-- Información de Stock Mejorada -->
                            <div class="space-y-1">
                                <!-- Stock Total -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Stock Total:</span>
                                    <span class="text-xs font-medium text-gray-900 dark:text-gray-100"><?php echo e($producto->stock); ?></span>
                                </div>
                                
                                <!-- Stock Disponible -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Disponible:</span>
                                    <span class="text-xs font-medium <?php echo e($producto->stock_disponible > 10 ? 'text-green-600 dark:text-green-400' : ($producto->stock_disponible > 5 ? 'text-yellow-600 dark:text-yellow-400' : ($producto->stock_disponible > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'))); ?>">
                                        <?php echo e($producto->stock_disponible); ?>

                                    </span>
                                </div>
                                
                                <!-- Stock Reservado (solo si hay) -->
                                <?php if($producto->stock_reservado > 0): ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Reservado:</span>
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400"><?php echo e($producto->stock_reservado); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Indicador de estado -->
                                <?php if($producto->stock_disponible <= 0): ?>
                                    <div class="flex items-center space-x-1 mt-1">
                                        <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs text-red-600 dark:text-red-400">Sin stock disponible</span>
                                    </div>
                                <?php elseif($producto->stock_reservado > $producto->stock * 0.5): ?>
                                    <div class="flex items-center space-x-1 mt-1">
                                        <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs text-yellow-600 dark:text-yellow-400">Alto stock reservado</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Categoría: <?php echo e($producto->categoria->nombre ?? 'Sin categoría'); ?>

                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Marca: <?php echo e($producto->marca->nombre ?? 'Sin marca'); ?>

                            </p>
                        </div>
                    </div>
                </div>
                <!-- Botones de acción -->
                <div class="mt-4 flex justify-end space-x-2">
                    <a href="<?php echo e(route('productos.edit', $producto)); ?>" 
                       class="inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/>
                        </svg>
                        Editar
                    </a>
                    <form action="<?php echo e(route('productos.destroy', $producto)); ?>" method="POST" class="form-eliminar inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" 
                                class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-950 px-3 py-2 text-sm font-semibold text-red-700 dark:text-red-300 shadow-sm ring-1 ring-inset ring-red-600/20 dark:ring-red-900/20 hover:bg-red-100 dark:hover:bg-red-900">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/>
                            </svg>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
            No hay productos registrados en el sistema
        </div>
        <?php endif; ?>
    </div>

    <!-- Vista escritorio (tabla) -->
    <div class="hidden sm:block">
        <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-md">
                         <!-- Encabezado -->
             <div class="mb-6">
                 <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                     <div>
                         <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">Listado de Productos</h2>
                         <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Gestiona los productos de la tienda</p>
                     </div>

                       <!-- Botón Crear Producto -->
                    <a href="<?php echo e(route('productos.create')); ?>" 
                    class="group inline-flex items-center rounded-xl bg-gradient-to-r from-slate-600 to-gray-700 px-8 py-4 text-base font-semibold text-white shadow-lg hover:from-slate-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 hover:shadow-xl min-w-[180px] justify-center">
                     <svg class="-ml-0.5 mr-3 h-6 w-6 transition-transform duration-300 group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                     </svg>
                     Crear Producto
                 </a>

                 </div>
             </div>
             
            <!-- Barra de herramientas con búsqueda y filtros -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                     <!-- Búsqueda personalizada -->
                     <div class="flex-1 max-w-md">
                         <label for="busquedaEscritorio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                             Buscar productos
                         </label>
                         <div class="relative">
                             <input type="text" 
                                    id="busquedaEscritorio" 
                                    class="block w-full rounded-md border-0 py-2 pl-10 pr-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-700 sm:text-sm" 
                                    placeholder="Buscar por nombre, ID, categoría...">
                             <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                 <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                     <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                 </svg>
                             </div>
                         </div>
                     </div>
                     
                     <!-- Información de registros -->
                     <div class="text-sm text-gray-500 dark:text-gray-400">
                         <span id="infoRegistros">Mostrando todos los productos</span>
                     </div>
            <!-- Botones de Exportacion -->
                     <button id="exportExcelEscritorio"
                     class="group inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 hover:shadow-xl min-w-[120px] justify-center">
                     <svg class="mr-2 h-5 w-5 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                     </svg>
                     Excel
                 </button>
                 
                 <button id="exportPDFEscritorio"
                     class="group inline-flex items-center rounded-xl bg-gradient-to-r from-red-500 to-pink-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-red-600 hover:to-pink-700 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 hover:shadow-xl min-w-[120px] justify-center">
                     <svg class="mr-2 h-5 w-5 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V11m14-6H5a2 2 0 00-2 2v2h18V7a2 2 0 00-2-2z" />
                     </svg>
                     PDF
                 </button>
                 </div>
             </div>
            <!-- Tabla -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="tablaProductos"
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 text-gray-700 dark:text-gray-300 text-sm font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">ID</th>
                                <th class="px-6 py-4 text-left font-semibold">Imagen</th>
                                <th class="px-6 py-4 text-left font-semibold">Nombre</th>
                                <th class="px-6 py-4 text-left font-semibold">Precio</th>
                                <th class="px-6 py-4 text-left font-semibold">Stock</th>
                                <th class="px-6 py-4 text-left font-semibold">Estado</th>
                                <th class="px-6 py-4 text-left font-semibold">Categoría</th>
                                <th class="px-6 py-4 text-left font-semibold">Marca</th>
                                <th class="px-6 py-4 text-center font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200 ease-in-out border-b border-gray-100 dark:border-gray-800">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100"><?php echo e($producto->producto_id); ?></td>
                            <td class="px-6 py-4">
                                <?php if($producto->imagenes->isNotEmpty()): ?>
                                <img src="<?php echo e(asset('storage/' . $producto->imagenes[0]->ruta_imagen)); ?>" 
                                     class="h-14 w-14 rounded-lg object-cover shadow-md border border-gray-200 dark:border-gray-700" 
                                     alt="<?php echo e($producto->nombre_producto); ?>">
                                <?php else: ?>
                                <img src="<?php echo e(asset('img/Logo_2.png')); ?>" 
                                     class="h-14 w-14 rounded-lg object-cover shadow-md border border-gray-200 dark:border-gray-700" 
                                     alt="Sin imagen">
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100"><?php echo e($producto->nombre_producto); ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">$<?php echo e(number_format($producto->precio, 0, ',', '.')); ?></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1">
                                    <!-- Stock Total -->
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Total:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            <?php echo e($producto->stock); ?>

                                        </span>
                                    </div>
                                    
                                    <!-- Stock Disponible -->
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Disponible:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($producto->stock_disponible > 10 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : ($producto->stock_disponible > 5 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : ($producto->stock_disponible > 0 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'))); ?>">
                                            <?php echo e($producto->stock_disponible); ?>

                                        </span>
                                    </div>
                                    
                                    <!-- Stock Reservado (solo si hay) -->
                                    <?php if($producto->stock_reservado > 0): ?>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Reservado:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            <?php echo e($producto->stock_reservado); ?>

                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Indicador de estado -->
                                    <?php if($producto->stock_disponible <= 0): ?>
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs text-red-600 dark:text-red-400">Sin stock disponible</span>
                                        </div>
                                    <?php elseif($producto->stock_reservado > $producto->stock * 0.5): ?>
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs text-yellow-600 dark:text-yellow-400">Alto stock reservado</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                    $estadoClasses = getEstadoClasses($producto->estado);
                                ?>
                                <span class="inline-flex items-center rounded-full <?php echo e($estadoClasses['bg']); ?> px-3 py-1 text-sm font-medium <?php echo e($estadoClasses['text']); ?> ring-1 ring-inset <?php echo e($estadoClasses['ring']); ?>">
                                    <?php echo e(ucfirst($producto->estado)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300"><?php echo e($producto->categoria->nombre ?? 'Sin categoría'); ?></td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300"><?php echo e($producto->marca->nombre ?? 'Sin marca'); ?></td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <!-- Botón Editar -->
                                    <div class="relative group">
                                        <a href="<?php echo e(route('productos.edit', $producto)); ?>" 
                                           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/50 dark:hover:bg-blue-900 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-all duration-200 ease-in-out transform hover:scale-110">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536M9 13l6-6m-6 6v3h3l6-6m-3-3L6 18H3v-3L15.232 5.232z" />
                                            </svg>
                                        </a>
                                        <div
                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg dark:bg-gray-700 z-10 whitespace-nowrap">
                                            Editar
                                        </div>
                                    </div>

                                    <!-- Botón Eliminar -->
                                    <div class="relative group">
                                        <form action="<?php echo e(route('productos.destroy', $producto)); ?>" method="POST" class="form-eliminar inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-all duration-200 ease-in-out transform hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                        <div
                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg dark:bg-gray-700 z-10 whitespace-nowrap">
                                            Eliminar
                                        </div>
                                    </div>
                                </div>
                            </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                            <td colspan="9" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                                    No hay productos registrados en el sistema
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
        timer: 2500,
        showConfirmButton: false
    });
</script>
<?php endif; ?>

<?php if(session('eliminado') == 'ok'): ?>
<script>
    Swal.fire({
        title: 'Eliminado',
        text: 'El producto ha sido eliminado correctamente.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
</script>
<?php endif; ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Ocultar completamente los botones por defecto de DataTables */
    .dt-buttons {
        display: none !important;
    }
    
    /* Estilos para tooltips personalizados */
    .tooltip {
        position: relative;
        display: inline-block;
    }
    
    .tooltip .tooltiptext {
        visibility: hidden;
        width: auto;
        background-color: #1f2937;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px 12px;
        position: absolute;
        z-index: 1000;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 12px;
        white-space: nowrap;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .tooltip .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #1f2937 transparent transparent transparent;
    }
    
    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
    
    /* Estilos para modo oscuro */
    .dark .tooltip .tooltiptext {
        background-color: #374151;
    }
    
    .dark .tooltip .tooltiptext::after {
        border-color: #374151 transparent transparent transparent;
    }
    
    /* Estilos para bordes de tabla en modo oscuro */
    .dark #tablaProductos {
        border: 1px solid #374151 !important;
    }
    
    .dark #tablaProductos th {
        border-bottom: 1px solid #4b5563 !important;
        border-right: 1px solid #4b5563 !important;
    }
    
    .dark #tablaProductos td {
        border-bottom: 1px solid #374151 !important;
        border-right: 1px solid #374151 !important;
    }
    
    .dark #tablaProductos th:last-child,
    .dark #tablaProductos td:last-child {
        border-right: none !important;
    }
    
    .dark #tablaProductos tr:last-child td {
        border-bottom: none !important;
    }
    
    /* Estilos para bordes de tabla en modo claro */
    #tablaProductos {
        border: 1px solid #d1d5db !important;
    }
    
    #tablaProductos th {
        border-bottom: 1px solid #e5e7eb !important;
        border-right: 1px solid #e5e7eb !important;
    }
    
    #tablaProductos td {
        border-bottom: 1px solid #f3f4f6 !important;
        border-right: 1px solid #f3f4f6 !important;
    }
    
    #tablaProductos th:last-child,
    #tablaProductos td:last-child {
        border-right: none !important;
    }
    
    #tablaProductos tr:last-child td {
        border-bottom: none !important;
    }
    
         /* Asegurar que los bordes de DataTables sean visibles */
     .dark .dataTables_wrapper .dataTables_scroll {
         border: 1px solid #374151 !important;
     }
     
     .dataTables_wrapper .dataTables_scroll {
         border: 1px solid #d1d5db !important;
     }
     
     /* Mejorar visibilidad del contenedor principal en modo oscuro */
     .dark .bg-white.dark\:bg-gray-900 {
         border: 2px solid #4b5563 !important;
         box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2) !important;
     }
     
     /* Mejorar visibilidad de títulos en modo oscuro */
     .dark h2.text-2xl.font-bold {
         color: #f9fafb !important;
         text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
     }
     
     .dark p.text-sm.text-gray-500.dark\:text-gray-300 {
         color: #d1d5db !important;
     }
     
     /* Mejorar contraste del contenedor en modo claro */
     .bg-white.dark\:bg-gray-900 {
         border: 1px solid #e5e7eb !important;
         box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
     }
     
     /* Ocultar elementos de DataTables que no necesitamos */
     .dataTables_filter {
         display: none !important;
     }
     
     .dataTables_length {
         display: none !important;
     }
     
     .dataTables_info {
         display: none !important;
     }
     
     /* Estilos para la barra de herramientas personalizada */
     .bg-gray-50.dark\:bg-gray-800 {
         border: 1px solid #e5e7eb !important;
         box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
     }
     
     .dark .bg-gray-50.dark\:bg-gray-800 {
         border: 1px solid #374151 !important;
         box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2) !important;
     }
     
     /* Mejorar apariencia de los botones de exportación */
     .bg-green-500.hover\:bg-green-600,
     .bg-red-500.hover\:bg-red-600 {
         box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
     }
     
     .bg-green-500.hover\:bg-green-600:hover,
     .bg-red-500.hover\:bg-red-600:hover {
         box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
         transform: translateY(-1px) !important;
     }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Verificar si hay datos antes de inicializar DataTables
        var hasData = <?php echo e($productos->count() > 0 ? 'true' : 'false'); ?>;
        
        if (hasData) {
            // Inicializar DataTable solo si hay datos
            var table = $('#tablaProductos').DataTable({
                dom: 'rtip', // Removido 'f' para ocultar el campo de búsqueda por defecto
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Productos - 4GMovil',
                        text: 'Exportar a Excel',
                        className: 'buttons-excel', 
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7]
                        },
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c[r^="C"]', sheet).attr('s', '2');
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Productos - 4GMovil',
                        text: 'Exportar a PDF',
                        className: 'buttons-pdf', 
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7]
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    }
                ],
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
                processing: true,
                serverSide: false,
                searching: true,
                ordering: true,
                columnDefs: [
                    { orderable: false, targets: [1, -1] },
                    { searchable: false, targets: [1, -1] }
                ],
                order: [[0, 'asc']],
                responsive: true,
                pagingType: "simple_numbers",
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                pageLength: 10,
                // Configuración para manejar tablas vacías
                deferRender: true,
                drawCallback: function(settings) {
                    // Aplicar estilos a los botones de paginación
                    $('.paginate_button').addClass('relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0');
                    $('.paginate_button.current').addClass('z-10 bg-brand-600 text-white hover:bg-brand-500 ring-brand-600').removeClass('text-gray-900 ring-gray-300');
                    $('.paginate_button.disabled').addClass('opacity-50 cursor-not-allowed');

                    // Aplicar estilos dark mode a elementos de DataTables
                    $('.dataTables_length select').addClass('dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700');
                    $('.dataTables_info').addClass('dark:text-gray-100');
                     
                     // Actualizar información de registros después de un pequeño delay
                     setTimeout(function() {
                         if (typeof updateInfoRegistros === 'function') {
                             updateInfoRegistros();
                         }
                     }, 100);
                 },
                 // Configuración para manejar tablas vacías
                 initComplete: function(settings, json) {
                     // Verificar si la tabla está vacía
                     if (this.api().data().length === 0) {
                         // Ocultar elementos de DataTables cuando no hay datos
                         $('.dataTables_paginate').hide();
                         $('.dataTables_length').hide();
                         $('.dataTables_info').hide();
                     }
                 }
             });
             
             // Inicializar información de registros después de que DataTable esté listo
             setTimeout(function() {
                 if (typeof updateInfoRegistros === 'function') {
                     updateInfoRegistros();
                 }
             }, 200);
             
             // Función para actualizar información de registros
             function updateInfoRegistros() {
                 if (!table || typeof table.page !== 'function') {
                     console.warn('DataTable no está disponible para actualizar información');
                     return;
                 }
                 
                 try {
                     var info = table.page.info();
                     var totalRecords = info.recordsTotal;
                     var filteredRecords = info.recordsDisplay;
                     var currentPage = info.page + 1;
                     var totalPages = info.pages;
                     var startRecord = info.start + 1;
                     var endRecord = info.end;
                     
                     if (filteredRecords === totalRecords) {
                         $('#infoRegistros').text(`Mostrando ${startRecord} a ${endRecord} de ${totalRecords} productos`);
                    } else {
                         $('#infoRegistros').text(`Mostrando ${startRecord} a ${endRecord} de ${filteredRecords} productos (filtrado de ${totalRecords} total)`);
                     }
                 } catch (error) {
                     console.error('Error al actualizar información de registros:', error);
                     $('#infoRegistros').text('Información no disponible');
                 }
             }
             
             // Búsqueda personalizada para escritorio
             $('#busquedaEscritorio').on('keyup', function() {
                 var searchValue = $(this).val();
                 if (table && typeof table.search === 'function') {
                     table.search(searchValue).draw();
                 } else {
                     console.warn('DataTable no está disponible para búsqueda');
                 }
             });

             // Búsqueda personalizada para móvil
             $('#busquedaMovil').on('keyup', function() {
                 var searchValue = $(this).val();
                 searchMobileCards(searchValue);
             });

             // Asegurar que la búsqueda funcione después de que DataTables esté listo
             setTimeout(function() {
                 // Re-registrar el evento de búsqueda para escritorio
                 $('#busquedaEscritorio').off('keyup').on('keyup', function() {
                     var searchValue = $(this).val();
                     if (table && typeof table.search === 'function') {
                         table.search(searchValue).draw();
                     }
                 });
                 
                 // Re-registrar el evento de búsqueda para móvil
                 $('#busquedaMovil').off('keyup').on('keyup', function() {
                     var searchValue = $(this).val();
                     searchMobileCards(searchValue);
                 });
             }, 500);

            // Botones personalizados de exportación (escritorio)
             setTimeout(function() {
                 $('#exportExcelEscritorio').on('click', function () {
                     if (table && table.buttons) {
                         table.buttons('.buttons-excel').trigger();
                     }
                 });
     
                 $('#exportPDFEscritorio').on('click', function () {
                     if (table && table.buttons) {
                         table.buttons('.buttons-pdf').trigger();
                     }
                 });
             }, 100);
             
             // Botones personalizados de exportación (móvil)
             setTimeout(function() {
                 $('#exportExcelMovil').on('click', function () {
                     if (table && table.buttons) {
                         table.buttons('.buttons-excel').trigger();
                     }
                 });
     
                 $('#exportPDFMovil').on('click', function () {
                     if (table && table.buttons) {
                         table.buttons('.buttons-pdf').trigger();
                     }
                 });
             }, 100);
        } else {
            // Si no hay datos, ocultar elementos de DataTables
            $('.dataTables_paginate').hide();
            $('.dataTables_length').hide();
            $('.dataTables_info').hide();
            $('#infoRegistros').text('No hay productos registrados');
        }

        // Confirmación para eliminar producto
        $('.form-eliminar').submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0088ff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Función para buscar en las tarjetas móviles
        function searchMobileCards(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            var visibleCount = 0;
            
            $('#mobileCards .producto-card').each(function() {
                const card = $(this);
                
                // Obtener todos los textos de la tarjeta
                const nombre = card.find('h3').text().toLowerCase();
                const id = card.find('p').filter(function() {
                    return $(this).text().includes('ID:');
                }).text().toLowerCase();
                const precio = card.find('p').filter(function() {
                    return $(this).text().includes('$');
                }).text().toLowerCase();
                const categoria = card.find('p').filter(function() {
                    return $(this).text().includes('Categoría:');
                }).text().toLowerCase();
                const marca = card.find('p').filter(function() {
                    return $(this).text().includes('Marca:');
                }).text().toLowerCase();
                const stock = card.find('p').filter(function() {
                    return $(this).text().includes('Stock:');
                }).text().toLowerCase();
                
                // Buscar en todos los spans (estados)
                const estados = card.find('span').map(function() {
                    return $(this).text().toLowerCase();
                }).get().join(' ');
                
                // Verificar si el término de búsqueda coincide con algún campo
                const matchFound = nombre.includes(searchTerm) || 
                    id.includes(searchTerm) || 
                    precio.includes(searchTerm) || 
                    categoria.includes(searchTerm) || 
                    marca.includes(searchTerm) || 
                    stock.includes(searchTerm) ||
                    estados.includes(searchTerm);
                
                if (matchFound) {
                    card.show();
                    visibleCount++;
                } else {
                    card.hide();
                }
            });

            // Mostrar mensaje cuando no hay resultados
            const noResultsMsg = $('#mobileNoResults');
            
            if (visibleCount === 0 && searchTerm !== '') {
                if (noResultsMsg.length === 0) {
                    $('#mobileCards').append(`
                        <div id="mobileNoResults" class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                            No se encontraron productos que coincidan con la búsqueda
                        </div>
                    `);
                }
            } else {
                noResultsMsg.remove();
            }
        }

        // Evento de búsqueda en móvil
        let searchTimeout;
        $('#busquedaMovil').on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val();
            searchTimeout = setTimeout(() => {
                searchMobileCards(searchTerm);
            }, 300);
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/pages/admin/productos/listadoP.blade.php ENDPATH**/ ?>