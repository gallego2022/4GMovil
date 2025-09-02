<?php $__env->startSection('title', 'Dashboard - 4GMovil'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-content sm:truncate sm:text-3xl sm:tracking-tight">Dashboard</h2>
            <p class="mt-1 text-sm text-content-secondary">Bienvenido al panel de administración de 4GMovil</p>
        </div>
        <div class="mt-4 flex sm:ml-4 sm:mt-0 space-x-3">
            <a href="<?php echo e(route('admin.inventario.dashboard')); ?>" 
               class="inline-flex items-center rounded-lg bg-gradient-to-r from-stone-600 to-neutral-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-stone-700 hover:to-neutral-800 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-stone-600 hover:shadow-xl">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 transition-transform duration-200 group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Inventario
            </a>
            <a href="<?php echo e(route('productos.create')); ?>" 
               class="inline-flex items-center rounded-lg bg-gradient-to-r from-slate-600 to-gray-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-slate-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 hover:shadow-xl">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 transition-transform duration-200 group-hover:rotate-90" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Nuevo producto
            </a>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Productos -->
        <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-blue-600 p-3">
                    <!-- Icono de productos -->
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-content-secondary">Total Productos</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-content"><?php echo e($totalProductos); ?></p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 dark:bg-gray-900 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="<?php echo e(route('productos.listadoP')); ?>" class="font-medium text-gray-600 dark:text-brand-600 hover:text-blue-500">Ver Productos<span class="sr-only"> productos</span></a>
                    </div>
                </div>
            </dd>
        </div>

        <!-- Total Usuarios -->
        <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-green-500 p-3">
                    <!-- Icono de usuarios -->  
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-content-secondary">Total Usuarios</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-content"><?php echo e($usuarios); ?></p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 dark:bg-gray-900 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="<?php echo e(route('usuarios.index')); ?>" class="font-medium text-gray-600 dark:text-brand-600 hover:text-blue-500">Ver Usuarios<span class="sr-only"> usuarios</span></a>
                    </div>
                </div>
            </dd>
        </div>

        <!-- Total Categorías -->
        <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <!-- Icono de categorías -->
                <div class="absolute rounded-md bg-blue-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-content-secondary">Total Categorías</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-content"><?php echo e($totalCategorias); ?></p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 dark:bg-gray-900 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="<?php echo e(route('categorias.index')); ?>" class="font-medium text-gray-600 dark:text-brand-600 hover:text-blue-500">Ver categorías<span class="sr-only"> categorías</span></a>
                    </div>
                </div>
            </dd>
        </div>

        <!-- Total Marcas -->
        <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <!-- Icono de marcas -->
                <div class="absolute rounded-md bg-purple-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-content-secondary">Total Marcas</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-content"><?php echo e($totalMarcas); ?></p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 dark:bg-gray-900 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                            <a href="<?php echo e(route('marcas.index')); ?>" class="font-medium text-gray-600 dark:text-brand-600 hover:text-blue-500">Ver marcas<span class="sr-only"> marcas</span></a>
                    </div>
                </div>
            </dd>
        </div>
    </dl>

    <!-- Estadísticas de Webhooks y Pagos -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-content">📊 Estadísticas de Webhooks y Pagos</h3>
            <p class="mt-1 max-w-2xl text-sm text-content-secondary">Monitoreo en tiempo real de eventos de Stripe</p>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 p-4">
                <!-- Total Eventos Webhook -->
                <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-5 shadow sm:px-6">
                    <dt>
                        <div class="absolute rounded-md bg-blue-400 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-blue-100">Total Eventos</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-white"><?php echo e($webhookStats['total_events']); ?></p>
                    </dd>
                </div>

                <!-- Eventos Procesados -->
                <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-green-500 to-green-600 px-4 py-5 shadow sm:px-6">
                    <dt>
                        <div class="absolute rounded-md bg-green-400 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-green-100">Procesados</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-white"><?php echo e($webhookStats['processed_events']); ?></p>
                    </dd>
                </div>

                <!-- Eventos Fallidos -->
                <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-red-500 to-red-600 px-4 py-5 shadow sm:px-6">
                    <dt>
                        <div class="absolute rounded-md bg-red-400 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-red-100">Fallidos</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-white"><?php echo e($webhookStats['failed_events']); ?></p>
                    </dd>
                </div>

                <!-- Eventos Pendientes -->
                <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 px-4 py-5 shadow sm:px-6">
                    <dt>
                        <div class="absolute rounded-md bg-yellow-400 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-yellow-100">Pendientes</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-white"><?php echo e($webhookStats['pending_events']); ?></p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Estadísticas de Pedidos -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-content">🛒 Estadísticas de Pedidos</h3>
            <p class="mt-1 max-w-2xl text-sm text-content-secondary">Estado actual de los pedidos en el sistema</p>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 p-4">
                <!-- Total Pedidos -->
                <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-5 shadow sm:px-6">
                    <dt>
                        <div class="absolute rounded-md bg-purple-400 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-purple-100">Total Pedidos</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-white"><?php echo e($pedidoStats['total_pedidos']); ?></p>
                    </dd>
                </div>

                <!-- Pedidos Pendientes -->
                <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 px-4 py-5 shadow sm:px-6">
                    <dt>
                        <div class="absolute rounded-md bg-yellow-400 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-yellow-100">Pendientes</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-white"><?php echo e($pedidoStats['pedidos_pendientes']); ?></p>
                    </dd>
                </div>

                <!-- Pedidos Confirmados -->
                <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-green-500 to-green-600 px-4 py-5 shadow sm:px-6">
                    <dt>
                        <div class="absolute rounded-md bg-green-400 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-green-100">Confirmados</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-white"><?php echo e($pedidoStats['pedidos_confirmados']); ?></p>
                    </dd>
                </div>

                <!-- Pedidos Cancelados -->
                <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-red-500 to-red-600 px-4 py-5 shadow sm:px-6">
                    <dt>
                        <div class="absolute rounded-md bg-red-400 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-red-100">Cancelados</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-white"><?php echo e($pedidoStats['pedidos_cancelados']); ?></p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Eventos Recientes de Webhooks -->
    <?php if($recentWebhooks->isNotEmpty()): ?>
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium leading-6 text-content">🔔 Eventos Recientes de Webhooks</h3>
                    <p class="mt-1 max-w-2xl text-sm text-content-secondary">Últimos eventos procesados por Stripe</p>
                </div>
                <button type="button" onclick="toggleFilters()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                    </svg>
                    Filtros
                </button>
            </div>
        </div>
        
        <!-- Filtros -->
        <div id="filters" class="hidden border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 py-4">
                <form method="GET" action="<?php echo e(route('admin.index')); ?>" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Filtro por Estado -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-content">Estado</label>
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Todos los estados</option>
                            <option value="processed" <?php echo e($filters['status'] === 'processed' ? 'selected' : ''); ?>>Procesados</option>
                            <option value="failed" <?php echo e($filters['status'] === 'failed' ? 'selected' : ''); ?>>Fallidos</option>
                            <option value="pending" <?php echo e($filters['status'] === 'pending' ? 'selected' : ''); ?>>Pendientes</option>
                        </select>
                    </div>

                    <!-- Filtro por Tipo de Evento -->
                    <div>
                        <label for="event_type" class="block text-sm font-medium text-content">Tipo de Evento</label>
                        <select id="event_type" name="event_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Todos los tipos</option>
                            <option value="payment_intent.succeeded" <?php echo e($filters['event_type'] === 'payment_intent.succeeded' ? 'selected' : ''); ?>>Pago Exitoso</option>
                            <option value="payment_intent.payment_failed" <?php echo e($filters['event_type'] === 'payment_intent.payment_failed' ? 'selected' : ''); ?>>Pago Fallido</option>
                            <option value="payment_intent.canceled" <?php echo e($filters['event_type'] === 'payment_intent.canceled' ? 'selected' : ''); ?>>Pago Cancelado</option>
                        </select>
                    </div>

                    <!-- Filtro por Fecha Desde -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-content">Fecha Desde</label>
                        <input type="date" id="date_from" name="date_from" value="<?php echo e($filters['date_from']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Filtro por Fecha Hasta -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-content">Fecha Hasta</label>
                        <input type="date" id="date_to" name="date_to" value="<?php echo e($filters['date_to']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Filtro por Pedido ID -->
                    <div>
                        <label for="pedido_id" class="block text-sm font-medium text-content">ID de Pedido</label>
                        <input type="number" id="pedido_id" name="pedido_id" value="<?php echo e($filters['pedido_id']); ?>" placeholder="Ej: 123" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Límite de resultados -->
                    <div>
                        <label for="limit" class="block text-sm font-medium text-content">Límite de resultados</label>
                        <select id="limit" name="limit" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="5" <?php echo e($filters['limit'] == 5 ? 'selected' : ''); ?>>5</option>
                            <option value="10" <?php echo e($filters['limit'] == 10 ? 'selected' : ''); ?>>10</option>
                            <option value="25" <?php echo e($filters['limit'] == 25 ? 'selected' : ''); ?>>25</option>
                            <option value="50" <?php echo e($filters['limit'] == 50 ? 'selected' : ''); ?>>50</option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filtrar
                        </button>
                        <a href="<?php echo e(route('admin.index')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-content sm:pl-6">Evento</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-content">Pedido</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-content">Estado</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-content">Fecha</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-content">Intentos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <?php $__currentLoopData = $recentWebhooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $webhook): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-content sm:pl-6">
                                <div class="flex items-center">
                                    <?php if($webhook->event_type === 'payment_intent.succeeded'): ?>
                                        <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20">
                                            ✅ Pago Exitoso
                                        </span>
                                    <?php elseif($webhook->event_type === 'payment_intent.payment_failed'): ?>
                                        <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20">
                                            ❌ Pago Fallido
                                        </span>
                                    <?php elseif($webhook->event_type === 'payment_intent.canceled'): ?>
                                        <span class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900 px-2 py-1 text-xs font-medium text-yellow-700 dark:text-yellow-300 ring-1 ring-inset ring-yellow-600/20">
                                            ⏹️ Pago Cancelado
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-900 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-600/20">
                                            <?php echo e($webhook->event_type); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-content">
                                <?php if($webhook->pedido): ?>
                                    <a href="#" class="text-brand-600 hover:text-brand-900 dark:hover:text-brand-400">
                                        #<?php echo e($webhook->pedido->pedido_id); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-content">
                                <?php if($webhook->status === 'processed'): ?>
                                    <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20">
                                        Procesado
                                    </span>
                                <?php elseif($webhook->status === 'failed'): ?>
                                    <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20">
                                        Fallido
                                    </span>
                                <?php elseif($webhook->status === 'pending'): ?>
                                    <span class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900 px-2 py-1 text-xs font-medium text-yellow-700 dark:text-yellow-300 ring-1 ring-inset ring-yellow-600/20">
                                        Pendiente
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-900 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-600/20">
                                        <?php echo e(ucfirst($webhook->status)); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-content">
                                <?php echo e($webhook->created_at->format('d/m/Y H:i')); ?>

                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-content">
                                <?php echo e($webhook->attempts); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Últimos productos agregados -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-content">Últimos productos agregados</h3>
            <p class="mt-1 max-w-2xl text-sm text-content-secondary">Los productos más recientes en el catálogo</p>
        </div>
        
        <!-- Vista de tarjetas para móvil -->
        <div class="border-t border-gray-200 dark:border-gray-700 md:hidden">
            <div class="p-4 space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $ultimosProductos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <?php if($producto->imagenes->isNotEmpty()): ?>
                            <img class="h-16 w-16 rounded-lg object-cover" 
                                 src="<?php echo e(asset('storage/' . $producto->imagenes[0]->ruta_imagen)); ?>" 
                                 alt="<?php echo e($producto->nombre_producto); ?>">
                            <?php else: ?>
                            <div class="h-16 w-16 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo e($producto->nombre_producto); ?></h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($producto->marca->nombre_marca); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">$<?php echo e(number_format($producto->precio, 0, ',', '.')); ?></p>
                                    <?php if($producto->estado == 'Nuevo'): ?>
                                    <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                        <?php echo e(ucfirst($producto->estado)); ?>

                                    </span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900 px-2 py-1 text-xs font-medium text-yellow-700 dark:text-yellow-300 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-600/30">
                                        <?php echo e(ucfirst($producto->estado)); ?>

                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($producto->categoria->nombre_categoria); ?></p>
                                <a href="<?php echo e(route('productos.edit', $producto->producto_id)); ?>" class="text-sm text-brand-600 hover:text-brand-900 dark:hover:text-brand-400 font-medium">Editar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay productos</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No hay productos registrados en el catálogo.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Vista de tabla para desktop -->
        <div class="border-t border-gray-200 dark:border-gray-700 hidden md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-content sm:pl-6">Producto</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-content">Categoría</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-content">Precio</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-content">Estado</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Acciones</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <?php $__empty_1 = true; $__currentLoopData = $ultimosProductos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <?php if($producto->imagenes->isNotEmpty()): ?>
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                             src="<?php echo e(asset('storage/' . $producto->imagenes[0]->ruta_imagen)); ?>" 
                                             alt="<?php echo e($producto->nombre_producto); ?>">
                                        <?php else: ?>
                                        <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-content"><?php echo e($producto->nombre_producto); ?></div>
                                        <div class="text-content-secondary"><?php echo e($producto->marca->nombre_marca); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-content-secondary">
                                <?php echo e($producto->categoria->nombre_categoria); ?>

                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-content-secondary">
                                $<?php echo e(number_format($producto->precio, 0, ',', '.')); ?>

                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <?php if($producto->estado == 'Nuevo'): ?>
                                <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                    <?php echo e(ucfirst($producto->estado)); ?>

                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900 px-2 py-1 text-xs font-medium text-yellow-700 dark:text-yellow-300 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-600/30">
                                    <?php echo e(ucfirst($producto->estado)); ?>

                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <a href="<?php echo e(route('productos.edit', $producto->producto_id)); ?>" class="text-brand-600 hover:text-brand-900 dark:hover:text-brand-400">Editar</a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                No hay productos registrados
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<script>
    function toggleFilters() {
        const filtersDiv = document.getElementById('filters');
        if (filtersDiv.classList.contains('hidden')) {
            filtersDiv.classList.remove('hidden');
        } else {
            filtersDiv.classList.add('hidden');
        }
    }
</script>

<?php echo $__env->make('layouts.app-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/pages/admin/index.blade.php ENDPATH**/ ?>