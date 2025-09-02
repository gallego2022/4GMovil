<?php
    $isActive = fn($route) => request()->is($route) ? 'active' : '';
?>

<!-- Menú de navegación -->
<nav class="space-y-2">
    <!-- Inicio -->
    <a href="<?php echo e(route('admin.index')); ?>" 
       class="sidebar-nav-item <?php echo e(request()->routeIs('admin.index') ? 'active' : ''); ?>">
        <svg class="mr-3 flex-shrink-0 h-5 w-5" 
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>Inicio</span>
    </a>

    <!-- Productos -->
    <div x-data="{ open: <?php echo e(request()->routeIs('productos.*') || request()->routeIs('admin.productos.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="sidebar-nav-item w-full text-left <?php echo e(request()->routeIs('productos.*') || request()->routeIs('admin.productos.*') ? 'active' : ''); ?>">
            <svg class="mr-3 flex-shrink-0 h-5 w-5" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span class="flex-1">Productos</span>
            <svg class="ml-auto h-4 w-4 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="sidebar-submenu mt-1">
            <a href="<?php echo e(route('productos.listadoP')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('productos.listadoP') ? 'active' : ''); ?>">
                <span>Lista de Productos</span>
            </a>
            <a href="<?php echo e(route('productos.create')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('productos.create') ? 'active' : ''); ?>">
                <span>Nuevo Producto</span>
            </a>
        </div>
    </div>

    <!-- Categorías -->
    <div x-data="{ open: <?php echo e(request()->routeIs('categorias.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="sidebar-nav-item w-full text-left <?php echo e(request()->routeIs('categorias.*') ? 'active' : ''); ?>">
            <svg class="mr-3 flex-shrink-0 h-5 w-5" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <span class="flex-1">Categorías</span>
            <svg class="ml-auto h-4 w-4 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="sidebar-submenu mt-1">
            <a href="<?php echo e(route('categorias.index')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('categorias.index') ? 'active' : ''); ?>">
                <span>Lista de Categorías</span>
            </a>
            <a href="<?php echo e(route('categorias.create')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('categorias.create') ? 'active' : ''); ?>">
                <span>Nueva Categoría</span>
            </a>
        </div>
    </div>

    <!-- Marcas -->
    <div x-data="{ open: <?php echo e(request()->routeIs('marcas.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="sidebar-nav-item w-full text-left <?php echo e(request()->routeIs('marcas.*') ? 'active' : ''); ?>">
            <svg class="mr-3 flex-shrink-0 h-5 w-5" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="flex-1">Marcas</span>
            <svg class="ml-auto h-4 w-4 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="sidebar-submenu mt-1">
            <a href="<?php echo e(route('marcas.index')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('marcas.index') ? 'active' : ''); ?>">
                <span>Lista de Marcas</span>
            </a>
            <a href="<?php echo e(route('marcas.create')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('marcas.create') ? 'active' : ''); ?>">
                <span>Nueva Marca</span>
            </a>
        </div>
    </div>

    <!-- Usuarios -->
    <div x-data="{ open: <?php echo e(request()->routeIs('usuarios.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="sidebar-nav-item w-full text-left <?php echo e(request()->routeIs('usuarios.*') ? 'active' : ''); ?>">
            <svg class="mr-3 flex-shrink-0 h-5 w-5" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <span class="flex-1">Usuarios</span>
            <svg class="ml-auto h-4 w-4 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="sidebar-submenu mt-1">
            <a href="<?php echo e(route('usuarios.index')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('usuarios.index') ? 'active' : ''); ?>">
                <span>Lista de Usuarios</span>
            </a>
            <a href="<?php echo e(route('usuarios.create')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('usuarios.create') ? 'active' : ''); ?>">
                <span>Nuevo Usuario</span>
            </a>
        </div>
    </div>

    <!-- Métodos de Pago -->
    <div x-data="{ open: <?php echo e(request()->routeIs('metodos-pago.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="sidebar-nav-item w-full text-left <?php echo e(request()->routeIs('metodos-pago.*') ? 'active' : ''); ?>">
            <svg class="mr-3 flex-shrink-0 h-5 w-5" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <span class="flex-1">Métodos de Pago</span>
            <svg class="ml-auto h-4 w-4 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="sidebar-submenu mt-1">
            <a href="<?php echo e(route('metodos-pago.index')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('metodos-pago.index') ? 'active' : ''); ?>">
                <span>Lista de Métodos</span>
            </a>
            <a href="<?php echo e(route('metodos-pago.create')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('metodos-pago.create') ? 'active' : ''); ?>">
                <span>Nuevo Método</span>
            </a>
        </div>
    </div>

    <!-- Pedidos -->
    <div x-data="{ open: <?php echo e(request()->routeIs('admin.pedidos.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="sidebar-nav-item w-full text-left <?php echo e(request()->routeIs('admin.pedidos.*') ? 'active' : ''); ?>">
            <svg class="mr-3 flex-shrink-0 h-5 w-5" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span class="flex-1">Pedidos</span>
            <svg class="ml-auto h-4 w-4 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="sidebar-submenu mt-1">
            <a href="<?php echo e(route('admin.pedidos.index')); ?>" 
               class="sidebar-submenu-item <?php echo e(request()->routeIs('admin.pedidos.index') ? 'active' : ''); ?>">
                <span>Lista de Pedidos</span>
            </a>
        </div>
    </div>

    
</nav>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\layouts\partials\sidebar-menu.blade.php ENDPATH**/ ?>