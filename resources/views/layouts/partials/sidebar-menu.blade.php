@php
    $isActive = fn($route) => request()->is($route) ? 'active' : '';
@endphp

<!-- Menú de navegación -->
<nav class="mt-5 flex-1 px-2 space-y-1">
    <!-- Inicio -->
    <a href="{{ route('admin.index') }}" 
       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.index') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
        <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.index') ? 'text-brand-300' : 'text-brand-300 group-hover:text-brand-200' }}" 
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Inicio
    </a>

    <!-- Productos -->
    <div x-data="{ open: {{ request()->routeIs('productos.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('productos.*') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('productos.*') ? 'text-brand-300' : 'text-brand-300 group-hover:text-brand-200' }}" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Productos
            <svg class="ml-auto h-5 w-5 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="mt-1 space-y-1">
            <a href="{{ route('productos.listadoP') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('productos.listadoP') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Lista de Productos
            </a>
            <a href="{{ route('productos.create') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('productos.create') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Categorías -->
    <div x-data="{ open: {{ request()->routeIs('categorias.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('categorias.*') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('categorias.*') ? 'text-brand-300' : 'text-brand-300 group-hover:text-brand-200' }}" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Categorías
            <svg class="ml-auto h-5 w-5 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="mt-1 space-y-1">
            <a href="{{ route('categorias.index') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('categorias.index') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Lista de Categorías
            </a>
            <a href="{{ route('categorias.create') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('categorias.create') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Nueva Categoría
            </a>
        </div>
    </div>

    <!-- Marcas -->
    <div x-data="{ open: {{ request()->routeIs('marcas.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('marcas.*') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('marcas.*') ? 'text-brand-300' : 'text-brand-300 group-hover:text-brand-200' }}" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Marcas
            <svg class="ml-auto h-5 w-5 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="mt-1 space-y-1">
            <a href="{{ route('marcas.index') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('marcas.index') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Lista de Marcas
            </a>
            <a href="{{ route('marcas.create') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('marcas.create') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Nueva Marca
            </a>
        </div>
    </div>

    <!-- Usuarios -->
    <div x-data="{ open: {{ request()->routeIs('usuarios.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('usuarios.*') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('usuarios.*') ? 'text-brand-300' : 'text-brand-300 group-hover:text-brand-200' }}" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Usuarios
            <svg class="ml-auto h-5 w-5 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="mt-1 space-y-1">
            <a href="{{ route('usuarios.index') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('usuarios.index') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Lista de Usuarios
            </a>
            <a href="{{ route('usuarios.create') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('usuarios.create') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Nuevo Usuario
            </a>
        </div>
    </div>

    <!-- Métodos de Pago -->
    <div x-data="{ open: {{ request()->routeIs('metodos-pago.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('metodos-pago.*') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('metodos-pago.*') ? 'text-brand-300' : 'text-brand-300 group-hover:text-brand-200' }}" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
            </svg>
            Métodos de Pago
            <svg class="ml-auto h-5 w-5 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="mt-1 space-y-1">
            <a href="{{ route('metodos-pago.index') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('metodos-pago.index') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Lista de Métodos
            </a>
            <a href="{{ route('metodos-pago.create') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('metodos-pago.create') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Nuevo Método
            </a>
        </div>
    </div>
     <!-- Pedidos -->
     <div x-data="{ open: {{ request()->routeIs('admin.pedidos.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pedidos.*') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.pedidos.*') ? 'text-brand-300' : 'text-brand-300 group-hover:text-brand-200' }}" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Pedidos
            <svg class="ml-auto h-5 w-5 transform transition-transform duration-200" 
                 :class="{'rotate-90': open}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <div x-show="open" class="mt-1 space-y-1">
            <a href="{{ route('admin.pedidos.index') }}" 
               class="group flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pedidos.index') ? 'bg-brand-700 text-white' : 'text-brand-100 hover:bg-brand-700 hover:text-white' }}">
                Lista de Pedidos
            </a>
        </div>
    </div>
</nav>
