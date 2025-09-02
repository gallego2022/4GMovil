<?php $__env->startSection('title', 'Usuarios - 4GMovil'); ?>

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

<?php $__env->startPush('styles'); ?>
<style>
    /* Ocultar completamente los botones por defecto de DataTables */
    .dt-buttons {
        display: none !important;
    }
    
    /* Ocultar botones específicos */
    .buttons-excel,
    .buttons-pdf {
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
    .dark #tablaUsuarios {
        border: 1px solid #374151 !important;
    }
    
    .dark #tablaUsuarios th {
        border-bottom: 1px solid #4b5563 !important;
        border-right: 1px solid #4b5563 !important;
    }
    
    .dark #tablaUsuarios td {
        border-bottom: 1px solid #374151 !important;
        border-right: 1px solid #374151 !important;
    }
    
    .dark #tablaUsuarios th:last-child,
    .dark #tablaUsuarios td:last-child {
        border-right: none !important;
    }
    
    .dark #tablaUsuarios tr:last-child td {
        border-bottom: none !important;
    }
    
    /* Estilos para bordes de tabla en modo claro */
    #tablaUsuarios {
        border: 1px solid #d1d5db !important;
    }
    
    #tablaUsuarios th {
        border-bottom: 1px solid #e5e7eb !important;
        border-right: 1px solid #e5e7eb !important;
    }
    
    #tablaUsuarios td {
        border-bottom: 1px solid #f3f4f6 !important;
        border-right: 1px solid #f3f4f6 !important;
    }
    
    #tablaUsuarios th:last-child,
    #tablaUsuarios td:last-child {
        border-right: none !important;
    }
    
    #tablaUsuarios tr:last-child td {
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

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Vista móvil (cards) -->
    <div class="grid grid-cols-1 gap-4 sm:hidden" id="mobileCards">
        <!-- Encabezado móvil -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Usuarios Registrados</h2>
                <p class="text-sm text-gray-500 dark:text-gray-300">Gestiona todos los usuarios del sistema</p>
            </div>
            
            <!-- Botones de acción móvil -->
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <!-- Botón Crear Usuario -->
                <a href="<?php echo e(route('usuarios.create')); ?>" 
                   class="inline-flex items-center rounded-lg bg-gradient-to-r from-slate-600 to-gray-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-slate-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 hover:shadow-xl min-w-[180px] justify-center">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 transition-transform duration-300 group-hover:rotate-180" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Crear Usuario
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

            <!-- Filtros móviles -->
            <div class="grid grid-cols-1 gap-3 mb-4">
                <div>
                    <label for="filtroRolMovil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Filtrar por rol
                    </label>
                    <select id="filtroRolMovil" class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-700 sm:text-sm">
                        <option value="">Todos</option>
                        <option value="admin">Administrador</option>
                        <option value="cliente">Cliente</option>
                        <option value="invitado">Invitado</option>
                    </select>
                </div>
                <div>
                    <label for="filtroEstadoMovil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Filtrar por estado
                    </label>
                    <select id="filtroEstadoMovil" class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-700 sm:text-sm">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div>
                    <button id="limpiarFiltrosMovil" class="w-full inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 011.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Campo de búsqueda móvil -->
            <div class="relative mt-2 rounded-md shadow-sm">
                <input type="text" 
                       id="busquedaMovil" 
                       class="block w-full rounded-md border-0 py-1.5 pl-4 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-800 sm:text-sm sm:leading-6" 
                       placeholder="Buscar usuarios...">
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden usuario-card" 
             data-rol="<?php echo e(strtolower($usuario->rol)); ?>" 
             data-estado="<?php echo e($usuario->estado == 'activo' ? 'activo' : 'inactivo'); ?>">
            <div class="p-4 space-y-4">
                <!-- Cabecera con foto y nombre -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <?php if($usuario->foto_perfil): ?>
                        <?php
                            $photoUrl = \App\Helpers\PhotoHelper::getPhotoUrl($usuario->foto_perfil);
                        ?>
                            <img src="<?php echo e($photoUrl); ?>" 
                                 class="h-12 w-12 rounded-full object-cover" 
                                 alt="<?php echo e($usuario->nombre_usuario); ?>">
                        <?php else: ?>
                            <span class="inline-block h-12 w-12 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                                <svg class="h-full w-full text-gray-300 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo e($usuario->nombre_usuario); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">ID: <?php echo e($usuario->usuario_id); ?></p>
                    </div>
                </div>

                <!-- Información del usuario -->
                <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo e($usuario->correo_electronico); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo e($usuario->telefono); ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rol</dt>
                        <dd class="mt-1">
                            <?php switch($usuario->rol):
                                case ('admin'): ?>
                                    <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-700/30">
                                        Admin
                                    </span>
                                    <?php break; ?>
                                <?php case ('cliente'): ?>
                                    <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10 dark:ring-gray-500/30">
                                        Cliente
                                    </span>
                                    <?php break; ?>
                                <?php default: ?>
                                    <span class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900 px-2 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-300 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-600/30">
                                        Invitado
                                    </span>
                            <?php endswitch; ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                        <dd class="mt-1">
                            <?php if($usuario->estado == 'activo'): ?>
                                <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verificado</dt>
                        <dd class="mt-1">
                            <?php if($usuario->email_verified_at): ?>
                                <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                    Verificado
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                    No Verificado
                                </span>
                            <?php endif; ?>
                        </dd>
                    </div>
                </dl>

                <!-- Acciones -->
                <div class="mt-4 flex justify-end space-x-3">
                    <a href="<?php echo e(route('usuarios.edit', $usuario)); ?>" 
                       class="inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/>
                        </svg>
                        Editar
                    </a>
                    <form action="<?php echo e(route('usuarios.destroy', $usuario)); ?>" method="POST" class="form-eliminar inline">
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
            No hay usuarios registrados en el sistema
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
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">Usuarios Registrados</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Gestiona todos los usuarios del sistema</p>
                    </div>
                    
                    <!-- Botón Crear Usuario -->
                    <a href="<?php echo e(route('usuarios.create')); ?>" 
                       class="group inline-flex items-center rounded-xl bg-gradient-to-r from-slate-600 to-gray-700 px-8 py-4 text-base font-semibold text-white shadow-lg hover:from-slate-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 hover:shadow-xl min-w-[180px] justify-center">
                        <svg class="-ml-0.5 mr-3 h-6 w-6 transition-transform duration-300 group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Crear Usuario
                    </a>
                </div>
            </div>
            
            <!-- Tarjeta separada para filtros -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 mb-6">
                <div class="flex flex-wrap items-center justify-center gap-6">
                    <div class="flex items-center gap-6">
                       <!-- Filtros -->
                    <div class="flex flex-col sm:flex-row gap-4 flex-1">
                        <div class="space-y-6">
                            <label for="filtroRol" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por rol</label>
                            <select id="filtroRol" class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-700 sm:text-sm">
                                <option value="">Todos</option>
                                <option value="admin">Administrador</option>
                                <option value="cliente">Cliente</option>
                                <option value="invitado">Invitado</option>
                            </select>
                        </div>

                        <div class="space-y-6">
                            <label for="filtroEstado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por estado</label>
                            <select id="filtroEstado" class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-700 sm:text-sm">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button id="limpiarFiltros" class="inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6.28 5.22a.75.75 0 011.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                                Limpiar Filtros
                            </button>
                        </div>
                    </div>
                        
                        
                    </div>
                </div>
            </div>
            
            <!-- Barra de herramientas con búsqueda y filtros -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    
                    
                    <!-- Búsqueda personalizada -->
                    <div class="flex-1 max-w-md">
                        <label for="busquedaEscritorio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Buscar usuarios
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="busquedaEscritorio" 
                                   class="block w-full rounded-md border-0 py-2 pl-10 pr-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-700 sm:text-sm" 
                                   placeholder="Buscar por nombre, email, ID...">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de registros -->
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span id="infoRegistros">Mostrando todos los usuarios</span>
                    </div>
                    
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
            <div class="overflow-x-auto">
                <table id="tablaUsuarios"
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-sm">
                        <tr>
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Foto</th>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Teléfono</th>
                            <th class="px-4 py-2 text-left">Rol</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                            <th class="px-4 py-2 text-left">Email Verificado</th>
                            <th class="px-4 py-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm">
                        <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-2"><?php echo e($usuario->usuario_id); ?></td>
                            <td class="px-4 py-2">
                                <?php if($usuario->foto_perfil): ?>
                                    <?php
                                        $photoUrl = \App\Helpers\PhotoHelper::getPhotoUrl($usuario->foto_perfil);
                                    ?>
                                    <img src="<?php echo e($photoUrl); ?>" 
                                         class="h-10 w-10 rounded-full object-cover" 
                                         alt="<?php echo e($usuario->nombre_usuario); ?>">
                                <?php else: ?>
                                    <span class="inline-block h-10 w-10 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                                        <svg class="h-full w-full text-gray-300 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2"><?php echo e($usuario->nombre_usuario); ?></td>
                            <td class="px-4 py-2"><?php echo e($usuario->correo_electronico); ?></td>
                            <td class="px-4 py-2"><?php echo e($usuario->telefono); ?></td>
                            <td class="px-4 py-2">
                                <?php switch($usuario->rol):
                                    case ('admin'): ?>
                                        <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-700/30">
                                            Admin
                                        </span>
                                        <?php break; ?>
                                    <?php case ('cliente'): ?>
                                        <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10 dark:ring-gray-500/30">
                                            Cliente
                                        </span>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <span class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900 px-2 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-300 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-600/30">
                                            Invitado
                                        </span>
                                <?php endswitch; ?>
                            </td>
                            <td class="px-4 py-2">
                                <?php if($usuario->estado == 'activo'): ?>
                                    <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2">
                                <?php if($usuario->email_verified_at): ?>
                                    <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                        Verificado
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                        No Verificado
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <!-- Botón Editar -->
                                    <div class="relative group">
                                        <a href="<?php echo e(route('usuarios.edit', $usuario)); ?>" 
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
                                        <form action="<?php echo e(route('usuarios.destroy', $usuario)); ?>" method="POST" class="form-eliminar inline">
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
                                No hay usuarios registrados en el sistema
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
        text: 'El usuario ha sido eliminado correctamente.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
</script>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Verificar si hay datos antes de inicializar DataTables
        var hasData = <?php echo e($usuarios->count() > 0 ? 'true' : 'false'); ?>;
        
        if (hasData) {
            // Inicializar DataTable solo si hay datos
            var table = $('#tablaUsuarios').DataTable({
                dom: 'rtip', // Removido 'f' para ocultar el campo de búsqueda por defecto
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Usuarios - 4GMovil',
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
                        title: 'Usuarios - 4GMovil',
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
                         $('#infoRegistros').text(`Mostrando ${startRecord} a ${endRecord} de ${totalRecords} usuarios`);
                    } else {
                         $('#infoRegistros').text(`Mostrando ${startRecord} a ${endRecord} de ${filteredRecords} usuarios (filtrado de ${totalRecords} total)`);
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
            $('#infoRegistros').text('No hay usuarios registrados');
        }

        // Función para filtrar tarjetas en vista móvil
        function filterMobileCards() {
            const rolValue = $('#filtroRolMovil').val().toLowerCase();
            const estadoValue = $('#filtroEstadoMovil').val().toLowerCase();

            // Remover mensaje de no resultados si existe
            $('#noResultsMobile').remove();
            
            let visibleCount = 0;
            
            $('#mobileCards .usuario-card').each(function() {
                const card = $(this);
                const cardRol = card.attr('data-rol');
                const cardEstado = card.attr('data-estado');
                
                const matchRol = !rolValue || cardRol === rolValue;
                const matchEstado = !estadoValue || cardEstado === estadoValue;
                
                if (matchRol && matchEstado) {
                    card.show();
                    visibleCount++;
                } else {
                    card.hide();
                }
            });

            // Mostrar mensaje si no hay resultados
            if (visibleCount === 0) {
                $('#mobileCards').append(`
                    <div id="noResultsMobile" class="col-span-full p-4 text-center">
                        <div class="rounded-md bg-gray-50 dark:bg-gray-800 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        No se encontraron resultados
                                    </h3>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        No hay usuarios que coincidan con los filtros seleccionados.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }
        }

        // Configuración de filtros personalizados para ambas vistas
        $('#filtroRol, #filtroEstado').on('change', function() {
            const rolValue = $('#filtroRol').val();
            const estadoValue = $('#filtroEstado').val();

            // Filtrar tabla de escritorio
            if (table && typeof table.column === 'function') {
                table.column(5).search(rolValue).column(6).search(estadoValue).draw();
            }
            
            // Filtrar tarjetas móviles
            filterMobileCards();
        });

        // Filtros móviles
        $('#filtroRolMovil, #filtroEstadoMovil').on('change', function() {
            filterMobileCards();
        });

        // Limpiar filtros en ambas vistas
        $('#limpiarFiltros').on('click', function() {
            $('#filtroRol, #filtroEstado').val('');
            
            // Limpiar filtros en tabla de escritorio
            if (table && typeof table.columns === 'function') {
                table.columns([5, 6]).search('').draw();
            }
            
            // Mostrar todas las tarjetas en móvil
            $('#mobileCards .usuario-card').show();
            $('#noResultsMobile').remove();
        });

        // Limpiar filtros móviles
        $('#limpiarFiltrosMovil').on('click', function() {
            $('#filtroRolMovil, #filtroEstadoMovil').val('');
            $('#mobileCards .usuario-card').show();
            $('#noResultsMobile').remove();
        });

        // Confirmación para eliminar usuario (para ambas vistas)
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
            
            $('#mobileCards .usuario-card').each(function() {
                const card = $(this);
                
                // Obtener todos los textos de la tarjeta
                const nombre = card.find('h3').text().toLowerCase();
                const id = card.find('p').filter(function() {
                    return $(this).text().includes('ID:');
                }).text().toLowerCase();
                const email = card.find('dd').filter(function() {
                    return $(this).prev('dt').text().includes('Email');
                }).text().toLowerCase();
                const telefono = card.find('dd').filter(function() {
                    return $(this).prev('dt').text().includes('Teléfono');
                }).text().toLowerCase();
                
                // Verificar si el término de búsqueda coincide con algún campo
                const matchFound = nombre.includes(searchTerm) || 
                    id.includes(searchTerm) || 
                    email.includes(searchTerm) || 
                    telefono.includes(searchTerm);
                
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
                            No se encontraron usuarios que coincidan con la búsqueda
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

        // Manejar cambios de tamaño de ventana
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth < 640) { // sm breakpoint
                    filterMobileCards(); // Reaplicar filtros en vista móvil
                } else {
                    if (table && typeof table.columns !== 'undefined' && typeof table.columns.adjust === 'function') {
                        try {
                            table.columns.adjust();
                            if (typeof table.responsive !== 'undefined' && typeof table.responsive.recalc === 'function') {
                                table.responsive.recalc();
                            }
                        } catch (error) {
                            console.warn('Error al ajustar tabla en resize:', error);
                        }
                    }
                }
            }, 250);
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\usuario\Documents\GitHub\4GMovil\resources\views/pages/admin/usuarios/index.blade.php ENDPATH**/ ?>