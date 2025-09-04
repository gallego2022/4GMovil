@if ($errors->any())
    <div class="rounded-md bg-red-50 dark:bg-red-900/50 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Se encontraron los siguientes errores:</h3>
                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

@if (session('success'))
    <div class="rounded-md bg-green-50 dark:bg-green-900/50 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800 dark:text-green-200">¡Éxito!</h3>
                <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="rounded-md bg-red-50 dark:bg-red-900/50 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error</h3>
                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            </div>
        </div>
    </div>
@endif

<div class="space-y-8">
    <!-- Información Básica -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Información Básica
        </h3>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Nombre del producto -->
            <div>
                <label for="nombre_producto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nombre del Producto <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="nombre_producto" 
                       id="nombre_producto"
                       class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('nombre_producto') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                       placeholder="Ej: iPhone 15 Pro Max"
                       required 
                       value="{{ old('nombre_producto', $producto->nombre_producto ?? '') }}">
                @error('nombre_producto')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- SKU -->
            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    SKU (Código de Producto)
                </label>
                <input type="text" 
                       name="sku" 
                       id="sku"
                       class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('sku') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                       placeholder="Ej: IPH15PM-256GB"
                       value="{{ old('sku', $producto->sku ?? '') }}">
                @error('sku')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Descripción -->
        <div class="mt-6">
            <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Descripción <span class="text-red-500">*</span>
            </label>
            <textarea name="descripcion" 
                      id="descripcion"
                      rows="4"
                      placeholder="Describe las características, beneficios y especificaciones del producto..."
                      class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('descripcion') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
            @error('descripcion')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Precios y Costos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
            </svg>
            Información de Precios
        </h3>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Precio de Venta -->
            <div>
                <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Precio de Venta <span class="text-red-500">*</span>
                </label>
                <div class="relative rounded-lg shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                    </div>
                    <input type="number" 
                           name="precio" 
                           id="precio"
                           min="0"
                           placeholder="0"
                           class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 pl-7 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('precio') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                           required 
                           value="{{ old('precio', $producto->precio ?? '') }}">
                </div>
                @error('precio')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Costo Unitario -->
            <div>
                <label for="costo_unitario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Costo Unitario (70% del precio de venta)
                </label>
                <div class="relative rounded-lg shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                    </div>
                    <input type="number" 
                           name="costo_unitario" 
                           id="costo_unitario"
                           min="0"
                           placeholder="Se calcula automáticamente"
                           disabled
                           class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 pl-7 shadow-sm bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-400 sm:text-sm cursor-not-allowed transition-colors duration-200 @error('costo_unitario') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                           value="{{ old('costo_unitario', $producto->costo_unitario ?? '') }}">
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Se calcula automáticamente como el 70% del precio de venta</p>
                @error('costo_unitario')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Gestión de Inventario -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707-.293l-2.414-2.414a1 1 0 01-.707-.293H6.586a1 1 0 00-.707.293L3.707 12.707A1 1 0 004.586 13H2" />
            </svg>
            Gestión de Inventario
        </h3>
        
                 <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
             <!-- Stock Inicial (para calcular alertas) -->
             <div>
                 <label for="stock_inicial" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                     Stock Inicial <span class="text-red-500">*</span>
                 </label>
                 <input type="number" 
                        name="stock_inicial" 
                        id="stock_inicial"
                        min="0"
                        placeholder="0"
                        class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('stock_inicial') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                        required 
                        value="{{ old('stock_inicial', $producto->stock_inicial ?? '') }}">
                 <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Stock inicial para calcular alertas (60% = bajo, 20% = crítico)</p>
                 @error('stock_inicial')
                     <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                 @enderror
             </div>

             <!-- Stock Padre (calculado desde variantes) -->
             <div>
                 <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                     Stock Total <span class="text-red-500">*</span>
                 </label>
                 <input type="number" 
                        name="stock" 
                        id="stock"
                        min="0"
                        placeholder="0"
                        disabled
                        class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-400 sm:text-sm cursor-not-allowed transition-colors duration-200 @error('stock') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                        value="{{ old('stock', $producto->stock ?? 0) }}">
                 <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Se calcula automáticamente desde las variantes de color</p>
                 @error('stock')
                     <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                 @enderror
             </div>

             <!-- Stock Mínimo (Umbral Crítico) -->
             <div>
                 <label for="stock_minimo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                     Umbral Crítico (20% del Stock Inicial)
                 </label>
                 <input type="number" 
                        name="stock_minimo" 
                        id="stock_minimo"
                        min="0"
                        placeholder="0"
                        disabled
                        class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 sm:text-sm cursor-not-allowed transition-colors duration-200 @error('stock_minimo') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                        value="{{ old('stock_minimo', $producto->stock_minimo ?? 0) }}">
                 <p class="mt-1 text-xs text-blue-600 dark:text-blue-400">
                     <span class="font-medium">Se calcula automáticamente:</span> 20% del stock inicial
                     @if(isset($producto) && $producto->stock > 0)
                         <br><span class="text-gray-500">Ejemplo: Stock inicial {{ $producto->stock }} → Umbral crítico {{ ceil($producto->stock * 0.2) }}</span>
                     @endif
                 </p>
                 @error('stock_minimo')
                     <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                 @enderror
             </div>

             <!-- Stock Máximo (Umbral Bajo) -->
             <div>
                 <label for="stock_maximo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                     Umbral Bajo (60% del Stock Inicial)
                 </label>
                 <input type="number" 
                        name="stock_maximo" 
                        id="stock_maximo"
                        min="0"
                        placeholder="0"
                        disabled
                        class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 sm:text-sm cursor-not-allowed transition-colors duration-200 @error('stock_maximo') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                        value="{{ old('stock_maximo', $producto->stock_maximo ?? 0) }}">
                 <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                     <span class="font-medium">Se calcula automáticamente:</span> 60% del stock inicial
                     @if(isset($producto) && $producto->stock > 0)
                         <br><span class="text-gray-500">Ejemplo: Stock inicial {{ $producto->stock }} → Umbral bajo {{ ceil($producto->stock * 0.6) }}</span>
                     @endif
                 </p>
                 @error('stock_maximo')
                     <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                 @enderror
             </div>

             <!-- Estado Activo -->
             <div>
                 <label for="activo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                     Estado Activo
                 </label>
                 <div class="flex items-center h-12 px-4">
                     <input type="checkbox" 
                            name="activo" 
                            id="activo"
                            value="1"
                            {{ old('activo', $producto->activo ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded transition-colors duration-200">
                     <label for="activo" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                         Producto disponible para venta
                     </label>
                 </div>
                 @error('activo')
                     <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                 @enderror
             </div>
         </div>
    </div>

    <!-- Categorización -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Categorización
        </h3>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Categoría -->
            <div>
                <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Categoría <span class="text-red-500">*</span>
                </label>
                <select name="categoria_id" 
                        id="categoria_id"
                        class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('categoria_id') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 @enderror" 
                        required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->categoria_id }}"
                            {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->categoria_id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('categoria_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Marca -->
            <div>
                <label for="marca_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Marca <span class="text-red-500">*</span>
                </label>
                <select name="marca_id" 
                        id="marca_id"
                        class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('marca_id') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 @enderror" 
                        required>
                    <option value="">Selecciona una marca</option>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->marca_id }}"
                            {{ old('marca_id', $producto->marca_id ?? '') == $marca->marca_id ? 'selected' : '' }}>
                            {{ $marca->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('marca_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado del Producto -->
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Estado del Producto
                </label>
                <select name="estado" 
                        id="estado" 
                        class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200">
                    <option value="nuevo" {{ old('estado', $producto->estado ?? '') == 'nuevo' ? 'selected' : '' }}>Nuevo</option>
                    <option value="usado" {{ old('estado', $producto->estado ?? '') == 'usado' ? 'selected' : '' }}>Usado</option>
                </select>
                @error('estado')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Información Física -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
            </svg>
            Información Física
        </h3>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Peso -->
            <div>
                <label for="peso" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Peso (kg)
                </label>
                <input type="number" 
                       name="peso" 
                       id="peso"
                       step="0.01"
                       min="0"
                       placeholder="0.00"
                       class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('peso') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                       value="{{ old('peso', $producto->peso ?? '') }}">
                @error('peso')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dimensiones -->
            <div>
                <label for="dimensiones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Dimensiones
                </label>
                <input type="text" 
                       name="dimensiones" 
                       id="dimensiones"
                       placeholder="Largo x Ancho x Alto (cm)"
                       class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('dimensiones') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                       value="{{ old('dimensiones', $producto->dimensiones ?? '') }}">
                @error('dimensiones')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Código de Barras -->
            <div>
                <label for="codigo_barras" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Código de Barras
                </label>
                <input type="text" 
                       name="codigo_barras" 
                       id="codigo_barras"
                       placeholder="1234567890123"
                       class="block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200 @error('codigo_barras') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                       value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}">
                @error('codigo_barras')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Especificaciones Técnicas -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
            </svg>
            Especificaciones Técnicas
        </h3>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Las especificaciones se cargarán automáticamente según la categoría seleccionada.
            </p>
        </div>
        
        <!-- Contenedor para especificaciones dinámicas -->
        <div id="especificaciones-container" class="space-y-4">
            <!-- Las especificaciones se cargarán dinámicamente aquí -->
        </div>
        
        <!-- Mensaje cuando no hay especificaciones -->
        <div id="no-especificaciones" class="hidden text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Selecciona una categoría para ver las especificaciones disponibles</p>
        </div>
    </div>



    <!-- Variantes de Colores -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z" />
            </svg>
            Variantes de Colores
        </h3>
        
        <div class="space-y-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Agrega las variantes de colores disponibles para este producto. Cada variante puede tener su propio stock y precio adicional.
            </p>
            
            <!-- Contenedor de variantes -->
            <div id="variantes-container" class="space-y-4">
                <!-- Las variantes se agregarán dinámicamente aquí -->
            </div>
            
            <!-- Botón para agregar variante -->
            <button type="button" 
                    id="agregar-variante"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 dark:text-purple-300 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Agregar Variante de Color
            </button>
        </div>
    </div>

    <!-- Imágenes del Producto -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Imágenes del Producto
        </h3>
        
        <div class="space-y-4">
            <!-- Área de subida -->
            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-brand-500 dark:hover:border-brand-400 transition-colors duration-200">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <label for="imagenes" class="relative cursor-pointer rounded-md bg-white dark:bg-gray-800 font-medium text-brand-600 dark:text-brand-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-brand-500 focus-within:ring-offset-2 dark:focus-within:ring-offset-gray-800 hover:text-brand-500 transition-colors duration-200">
                            <span>Subir archivos</span>
                            <input type="file" 
                                   name="imagenes[]" 
                                   id="imagenes" 
                                   class="sr-only" 
                                   multiple 
                                   accept="image/jpeg,image/png,image/jpg,image/webp">
                        </label>
                        <p class="pl-1">o arrastrar y soltar</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WEBP hasta 5MB por imagen</p>
                </div>
            </div>

            @error('imagenes.*')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <!-- Contenedor para previsualización -->
            <div id="preview-container" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Variables globales
    let especificacionesActuales = [];
    let valoresEspecificaciones = {};

    // Cálculo automático del costo unitario
    document.getElementById('precio').addEventListener('input', function() {
        const precio = parseFloat(this.value) || 0;
        const costoUnitario = precio * 0.7; // 70% del precio
        document.getElementById('costo_unitario').value = costoUnitario.toFixed(2);
    });

    // Cargar especificaciones cuando cambie la categoría
    document.getElementById('categoria_id').addEventListener('change', function() {
        const categoriaId = this.value;
        if (categoriaId) {
            cargarEspecificaciones(categoriaId);
        } else {
            limpiarEspecificaciones();
        }
    });

         // Función para cargar especificaciones
     async function cargarEspecificaciones(categoriaId) {
         try {
             console.log('Cargando especificaciones para categoría:', categoriaId);
             
             const response = await fetch(`/api/especificaciones/${categoriaId}`);
             
             if (!response.ok) {
                 throw new Error(`HTTP error! status: ${response.status}`);
             }
             
             const especificaciones = await response.json();
             console.log('Especificaciones recibidas:', especificaciones);
             
             // Log detallado de cada especificación
             especificaciones.forEach((espec, index) => {
                 console.log(`Especificación ${index + 1}:`, {
                     nombre_campo: espec.nombre_campo,
                     tipo_campo: espec.tipo_campo,
                     opciones: espec.opciones,
                     opciones_tipo: typeof espec.opciones,
                     opciones_array: espec.opciones_array,
                     opciones_array_tipo: typeof espec.opciones_array
                 });
             });
             
             especificacionesActuales = especificaciones;
             renderizarEspecificaciones(especificaciones);
             
         } catch (error) {
             console.error('Error al cargar especificaciones:', error);
             mostrarError('Error al cargar las especificaciones de la categoría: ' + error.message);
         }
     }

    // Función para renderizar especificaciones
    function renderizarEspecificaciones(especificaciones) {
        const container = document.getElementById('especificaciones-container');
        const noEspecificaciones = document.getElementById('no-especificaciones');
        
        if (especificaciones.length === 0) {
            container.innerHTML = '';
            noEspecificaciones.classList.remove('hidden');
            return;
        }
        
        noEspecificaciones.classList.add('hidden');
        
        let html = '';
        especificaciones.forEach((espec, index) => {
            html += crearCampoEspecificacion(espec, index);
        });
        
        container.innerHTML = html;
        
        // Agregar event listeners a los campos creados
        agregarEventListenersEspecificaciones();
    }

    // Función para crear un campo de especificación
    function crearCampoEspecificacion(espec, index) {
        const valorActual = valoresEspecificaciones[espec.nombre_campo] || '';
        const requerido = espec.requerido ? '<span class="text-red-500">*</span>' : '';
        const unidad = espec.unidad ? ` (${espec.unidad})` : '';
        
        // Debug: mostrar qué tipo de campo se está procesando
        console.log(`Creando campo: ${espec.nombre_campo}, Tipo: ${espec.tipo_campo}, Etiqueta: ${espec.etiqueta}`);
        
        let campo = '';
        
        switch (espec.tipo_campo) {
            case 'text':
            case 'texto':
                campo = `
                    <input type="text" 
                           name="especificaciones[${espec.nombre_campo}]" 
                           id="espec_${espec.nombre_campo}"
                           value="${valorActual}"
                           class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200"
                           placeholder="Ingresa ${espec.etiqueta.toLowerCase()}"
                           ${espec.requerido ? 'required' : ''}>
                `;
                break;
                
            case 'number':
            case 'numero':
                campo = `
                    <input type="number" 
                           name="especificaciones[${espec.nombre_campo}]" 
                           id="espec_${espec.nombre_campo}"
                           value="${valorActual}"
                           class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200"
                           placeholder="0"
                           ${espec.requerido ? 'required' : ''}>
                `;
                break;
                
            case 'select':
                 // Para campos select, necesitamos obtener las opciones de la base de datos
                 // o usar opciones predefinidas si están disponibles
                 let opciones = [];
                 
                 // Si hay opciones_array definidas, usarlas
                 if (espec.opciones_array && Array.isArray(espec.opciones_array)) {
                     opciones = espec.opciones_array;
                 } else if (espec.opciones && typeof espec.opciones === 'string') {
                     // Si hay opciones como string, convertirlas a array
                     opciones = espec.opciones.split(',').map(op => op.trim());
                 } else if (espec.opciones && Array.isArray(espec.opciones)) {
                     // Si ya es un array
                     opciones = espec.opciones;
                 } else {
                     // Opciones por defecto para campos comunes
                     switch (espec.nombre_campo) {
                         case 'ram':
                             opciones = ['2GB', '4GB', '8GB', '16GB', '32GB', '64GB'];
                             break;
                         case 'almacenamiento':
                             opciones = ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB', '2TB'];
                             break;
                         case 'pantalla':
                             opciones = ['5.5"', '6.1"', '6.7"', '7"', '10.1"', '11"', '13"', '15.6"', '17"'];
                             break;
                         case 'resolucion':
                             opciones = ['HD (1280x720)', 'Full HD (1920x1080)', '2K (2560x1440)', '4K (3840x2160)', 'Retina'];
                             break;
                                                 case 'procesador':
                            opciones = ['Intel Core i3', 'Intel Core i5', 'Intel Core i7', 'Intel Core i9', 'AMD Ryzen 3', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9', 'Apple M1', 'Apple M2', 'Apple M3'];
                            break;
                        case 'sistema_operativo':
                            opciones = ['Android', 'iOS', 'Windows', 'macOS', 'Linux'];
                            break;
                        default:
                            opciones = ['Opción 1', 'Opción 2', 'Opción 3'];
                     }
                 }
                 
                 let opcionesHtml = '<option value="">Selecciona una opción</option>';
                 opciones.forEach(opcion => {
                     const selected = valorActual === opcion ? 'selected' : '';
                     opcionesHtml += `<option value="${opcion}" ${selected}>${opcion}</option>`;
                 });
                 
                 campo = `
                     <select name="especificaciones[${espec.nombre_campo}]" 
                             id="espec_${espec.nombre_campo}"
                             class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200"
                             ${espec.requerido ? 'required' : ''}>
                         ${opcionesHtml}
                     </select>
                 `;
                 break;
                
            case 'textarea':
                campo = `
                    <textarea name="especificaciones[${espec.nombre_campo}]" 
                              id="espec_${espec.nombre_campo}"
                              rows="3"
                              class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200"
                              placeholder="Describe ${espec.etiqueta.toLowerCase()}"
                              ${espec.requerido ? 'required' : ''}>${valorActual}</textarea>
                `;
                break;
                
            case 'checkbox':
                const checked = valorActual === '1' || valorActual === true ? 'checked' : '';
                campo = `
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="especificaciones[${espec.nombre_campo}]" 
                               id="espec_${espec.nombre_campo}"
                               value="1"
                               ${checked}
                               class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded transition-colors duration-200">
                        <label for="espec_${espec.nombre_campo}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            ${espec.etiqueta}
                        </label>
                    </div>
                `;
                break;
                
            case 'radio':
                const opcionesRadio = espec.opciones_array || [];
                let radioHtml = '';
                opcionesRadio.forEach((opcion, radioIndex) => {
                    const checked = valorActual === opcion ? 'checked' : '';
                    radioHtml += `
                        <div class="flex items-center">
                            <input type="radio" 
                                   name="especificaciones[${espec.nombre_campo}]" 
                                   id="espec_${espec.nombre_campo}_${radioIndex}"
                                   value="${opcion}"
                                   ${checked}
                                   class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 transition-colors duration-200"
                                   ${espec.requerido ? 'required' : ''}>
                            <label for="espec_${espec.nombre_campo}_${radioIndex}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                ${opcion}
                            </label>
                        </div>
                    `;
                });
                
                campo = `
                    <div class="space-y-2">
                        ${radioHtml}
                    </div>
                `;
                break;
                
            default:
                // Campo por defecto si el tipo no es reconocido
                console.warn(`Tipo de campo no reconocido: ${espec.tipo_campo} para ${espec.nombre_campo}`);
                campo = `
                    <input type="text" 
                           name="especificaciones[${espec.nombre_campo}]" 
                           id="espec_${espec.nombre_campo}"
                           value="${valorActual}"
                           class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200"
                           placeholder="Ingresa ${espec.etiqueta.toLowerCase()}"
                           ${espec.requerido ? 'required' : ''}>
                `;
                break;
        }
        
        return `
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        ${espec.etiqueta}${unidad} ${requerido}
                    </label>
                    ${espec.descripcion ? `<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${espec.descripcion}</p>` : ''}
                </div>
                ${campo}
            </div>
        `;
    }

    // Función para agregar event listeners a las especificaciones
    function agregarEventListenersEspecificaciones() {
        especificacionesActuales.forEach(espec => {
            const campo = document.getElementById(`espec_${espec.nombre_campo}`);
            if (campo) {
                campo.addEventListener('change', function() {
                    guardarValorEspecificacion(espec.nombre_campo, this.value);
                });
                
                if (espec.tipo_campo === 'checkbox') {
                    campo.addEventListener('change', function() {
                        guardarValorEspecificacion(espec.nombre_campo, this.checked ? '1' : '0');
                    });
                }
            }
        });
    }

    // Función para guardar valor de especificación
    function guardarValorEspecificacion(nombreCampo, valor) {
        valoresEspecificaciones[nombreCampo] = valor;
    }

    // Función para limpiar especificaciones
    function limpiarEspecificaciones() {
        const container = document.getElementById('especificaciones-container');
        const noEspecificaciones = document.getElementById('no-especificaciones');
        
        container.innerHTML = '';
        noEspecificaciones.classList.remove('hidden');
        especificacionesActuales = [];
        valoresEspecificaciones = {};
    }

    // Función para mostrar errores
    function mostrarError(mensaje) {
        // Crear notificación de error
        const notificacion = document.createElement('div');
        notificacion.className = 'fixed top-4 right-4 z-50 bg-red-500 text-white p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full';
        notificacion.innerHTML = `
            <div class="flex items-center">
                <span class="mr-2">❌</span>
                <span class="text-sm font-medium">${mensaje}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notificacion);
        
        // Animar entrada
        setTimeout(() => {
            notificacion.classList.remove('translate-x-full');
        }, 100);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            notificacion.classList.add('translate-x-full');
            setTimeout(() => {
                if (notificacion.parentElement) {
                    notificacion.remove();
                }
            }, 300);
        }, 5000);
    }

    // Cargar especificaciones al cargar la página si ya hay una categoría seleccionada
    document.addEventListener('DOMContentLoaded', function() {
        const categoriaSelect = document.getElementById('categoria_id');
        if (categoriaSelect.value) {
            cargarEspecificaciones(categoriaSelect.value);
        }
        
        // Cargar especificaciones existentes si estamos editando
        @if(isset($producto) && $producto->especificaciones && $producto->especificaciones->count() > 0)
            // Esperar a que se carguen las especificaciones de la categoría
            setTimeout(() => {
                @foreach($producto->especificaciones as $especProducto)
                    valoresEspecificaciones['{{ $especProducto->especificacionCategoria->nombre_campo }}'] = '{{ $especProducto->valor }}';
                @endforeach
                
                // Re-renderizar las especificaciones con los valores existentes
                if (especificacionesActuales.length > 0) {
                    renderizarEspecificaciones(especificacionesActuales);
                }
            }, 500);
        @endif
    });

    // Calcular costo unitario al cargar la página si ya hay un precio
    document.addEventListener('DOMContentLoaded', function() {
        const precioInput = document.getElementById('precio');
        const costoInput = document.getElementById('costo_unitario');
        
        if (precioInput.value && !costoInput.value) {
            const precio = parseFloat(precioInput.value) || 0;
            const costoUnitario = precio * 0.7;
            costoInput.value = costoUnitario.toFixed(2);
        }
    });

    // Previsualización de imágenes mejorada
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const container = document.getElementById('preview-container');
        container.innerHTML = ''; // Limpiar previsualizaciones anteriores
        
        Array.from(e.target.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'relative group';
                    preview.innerHTML = `
                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="h-full w-full object-cover object-center">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                <button type="button" class="remove-image opacity-0 group-hover:opacity-100 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors duration-200" data-index="${index}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">${file.name}</p>
                    `;
                    container.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Función para remover imágenes
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-image')) {
            const button = e.target.closest('.remove-image');
            const index = parseInt(button.dataset.index);
            const input = document.getElementById('imagenes');
            const dt = new DataTransfer();
            
            Array.from(input.files).forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });
            
            input.files = dt.files;
            
            // Recrear previsualizaciones
            const event = new Event('change');
            input.dispatchEvent(event);
        }
    });

    // Gestión de variantes de colores
    let varianteCounter = 0;
    
    // Cargar variantes existentes si estamos editando
    @if(isset($producto) && $producto->variantes && $producto->variantes->count() > 0)
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($producto->variantes as $variante)
                agregarVarianteExistente({
                    nombre: '{{ $variante->nombre }}',
                    codigo_color: '{{ $variante->codigo_color ?? "#000000" }}',
                    stock: {{ $variante->stock }},
                    precio_adicional: {{ $variante->precio_adicional ?? 0 }},
                    descripcion: '{{ $variante->descripcion ?? "" }}',
                    disponible: {{ $variante->disponible ? 'true' : 'false' }},
                    imagenes: [
                        @foreach($variante->imagenes as $imagen)
                            {
                                url: '{{ Storage::url($imagen->ruta_imagen) }}',
                                nombre: '{{ $imagen->nombre_archivo }}',
                                id: {{ $imagen->imagen_variante_id }}
                            }@if(!$loop->last),@endif
                        @endforeach
                    ]
                });
            @endforeach
        });
    @endif
    
    document.getElementById('agregar-variante').addEventListener('click', function() {
        const container = document.getElementById('variantes-container');
        const varianteId = `variante-${varianteCounter++}`;
        
        const varianteHtml = `
            <div id="${varianteId}" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Variante de Color</h4>
                    <button type="button" 
                            onclick="eliminarVariante('${varianteId}')"
                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Nombre del color -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre del Color <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="variantes[${varianteCounter-1}][nombre]" 
                               class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                               placeholder="Ej: Negro, Blanco, Azul"
                               required>
                    </div>
                    
                    <!-- Código de color -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Código de Color
                        </label>
                        <div class="flex">
                            <input type="color" 
                                   name="variantes[${varianteCounter-1}][codigo_color]" 
                                   class="h-10 w-16 border-gray-300 dark:border-gray-600 rounded-l-md focus:border-purple-500 focus:ring-purple-500"
                                   value="#000000">
                            <input type="text" 
                                   class="flex-1 px-3 py-2 rounded-r-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                                   placeholder="#000000"
                                   pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                    </div>
                    
                    <!-- Stock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Stock <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="variantes[${varianteCounter-1}][stock]" 
                               min="0"
                               class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                               placeholder="0"
                               required>
                    </div>
                    
                    <!-- Precio adicional -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Precio Adicional
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   name="variantes[${varianteCounter-1}][precio_adicional]" 
                                   step="0.01"
                                   min="0"
                                   class="block w-full px-3 py-2 pl-7 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Descripción del Color
                    </label>
                    <textarea name="variantes[${varianteCounter-1}][descripcion]" 
                              rows="2"
                              class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                              placeholder="Describe las características del color..."></textarea>
                </div>
                
                <div class="mt-4 flex items-center">
                    <input type="checkbox" 
                           name="variantes[${varianteCounter-1}][disponible]" 
                           value="1"
                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded transition-colors duration-200"
                           checked>
                    <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Disponible para venta
                    </label>
                </div>

                <!-- Imágenes de la variante -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Imágenes del Color
                    </label>
                    <div class="space-y-3">
                        <!-- Área de subida -->
                        <div class="flex justify-center px-4 py-3 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-purple-500 dark:hover:border-purple-400 transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-xs text-gray-600 dark:text-gray-400">
                                    <label class="relative cursor-pointer rounded-md bg-white dark:bg-gray-800 font-medium text-purple-600 dark:text-purple-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-purple-500 focus-within:ring-offset-2 dark:focus-within:ring-offset-gray-800 hover:text-purple-500 transition-colors duration-200">
                                        <span>Subir imágenes</span>
                                        <input type="file" 
                                               name="variantes[${varianteCounter-1}][imagenes][]" 
                                               class="sr-only variante-imagenes" 
                                               multiple 
                                               accept="image/jpeg,image/png,image/jpg,image/webp"
                                               data-variante-id="${varianteCounter-1}">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WEBP hasta 5MB</p>
                            </div>
                        </div>

                        <!-- Contenedor para previsualización de imágenes de la variante -->
                        <div id="variante-imagenes-${varianteCounter-1}" class="grid grid-cols-3 gap-2 variante-preview-container"></div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', varianteHtml);
        
        // Sincronizar color picker con input de texto
        const colorInput = container.querySelector(`#${varianteId} input[type="color"]`);
        const textInput = container.querySelector(`#${varianteId} input[type="text"]`);
        
        colorInput.addEventListener('input', function() {
            textInput.value = this.value;
        });
        
        textInput.addEventListener('input', function() {
            if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                colorInput.value = this.value;
            }
        });
    });
    
    // Función para eliminar variante
    function eliminarVariante(varianteId) {
        const variante = document.getElementById(varianteId);
        if (variante) {
            variante.remove();
        }
    }
    
        // Función para agregar variante existente (para edición)
    function agregarVarianteExistente(varianteData) {
        const container = document.getElementById('variantes-container');
        const varianteId = `variante-${varianteCounter++}`;
        
        const varianteHtml = `
            <div id="${varianteId}" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Variante de Color</h4>
                    <button type="button" 
                            onclick="eliminarVariante('${varianteId}')"
                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Nombre del color -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre del Color <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="variantes[${varianteCounter-1}][nombre]" 
                               value="${varianteData.nombre}"
                               class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                               placeholder="Ej: Negro, Blanco, Azul"
                               required>
                    </div>
                    
                    <!-- Código de color -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Código de Color
                        </label>
                        <div class="flex">
                            <input type="color" 
                                   name="variantes[${varianteCounter-1}][codigo_color]" 
                                   value="${varianteData.codigo_color}"
                                   class="h-10 w-16 border-gray-300 dark:border-gray-600 rounded-l-md focus:border-purple-500 focus:ring-purple-500">
                            <input type="text" 
                                   value="${varianteData.codigo_color}"
                                   class="flex-1 px-3 py-2 rounded-r-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                                   placeholder="#000000"
                                   pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                    </div>
                    
                    <!-- Stock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Stock <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="variantes[${varianteCounter-1}][stock]" 
                               value="${varianteData.stock}"
                               min="0"
                               class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                               placeholder="0"
                               required>
                    </div>
                    
                    <!-- Precio adicional -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Precio Adicional
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   name="variantes[${varianteCounter-1}][precio_adicional]" 
                                   value="${varianteData.precio_adicional}"
                                   step="0.01"
                                   min="0"
                                   class="block w-full px-3 py-2 pl-7 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Descripción del Color
                    </label>
                    <textarea name="variantes[${varianteCounter-1}][descripcion]" 
                              rows="2"
                              class="block w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-gray-900 dark:bg-gray-600 dark:text-gray-100 transition-colors duration-200" 
                              placeholder="Describe las características del color...">${varianteData.descripcion}</textarea>
                </div>
                
                <div class="mt-4 flex items-center">
                    <input type="checkbox" 
                           name="variantes[${varianteCounter-1}][disponible]" 
                           value="1"
                           ${varianteData.disponible ? 'checked' : ''}
                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded transition-colors duration-200">
                    <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Disponible para venta
                    </label>
                </div>

                <!-- Imágenes de la variante -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Imágenes del Color
                    </label>
                    <div class="space-y-3">
                        <!-- Área de subida -->
                        <div class="flex justify-center px-4 py-3 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-purple-500 dark:hover:border-purple-400 transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-xs text-gray-600 dark:text-gray-400">
                                    <label class="relative cursor-pointer rounded-md bg-white dark:bg-gray-800 font-medium text-purple-600 dark:text-purple-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-purple-500 focus-within:ring-offset-2 dark:focus-within:ring-offset-gray-800 hover:text-purple-500 transition-colors duration-200">
                                        <span>Subir imágenes</span>
                                        <input type="file" 
                                               name="variantes[${varianteCounter-1}][imagenes][]" 
                                               class="sr-only variante-imagenes" 
                                               multiple 
                                               accept="image/jpeg,image/png,image/jpg,image/webp"
                                               data-variante-id="${varianteCounter-1}">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WEBP hasta 5MB</p>
                            </div>
                        </div>

                        <!-- Contenedor para previsualización de imágenes de la variante -->
                        <div id="variante-imagenes-${varianteCounter-1}" class="grid grid-cols-3 gap-2 variante-preview-container">
                            ${varianteData.imagenes && varianteData.imagenes.length > 0 ? 
                                varianteData.imagenes.map((imagen, index) => `
                                    <div class="relative group">
                                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                                            <img src="${imagen.url}" alt="${imagen.nombre}" class="h-full w-full object-cover object-center">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                                <button type="button" class="remove-existing-image opacity-0 group-hover:opacity-100 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors duration-200" data-imagen-id="${imagen.id}" data-variante-id="${varianteCounter-1}">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">${imagen.nombre}</p>
                                        <input type="hidden" name="variantes[${varianteCounter-1}][imagenes_existentes][]" value="${imagen.id}">
                                    </div>
                                `).join('') : ''
                            }
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', varianteHtml);
        
        // Sincronizar color picker con input de texto
        const colorInput = container.querySelector(`#${varianteId} input[type="color"]`);
        const textInput = container.querySelector(`#${varianteId} input[type="text"]`);
        
        colorInput.addEventListener('input', function() {
            textInput.value = this.value;
        });
        
        textInput.addEventListener('input', function() {
            if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                colorInput.value = this.value;
            }
        });
    }
     
     // Gestión de imágenes por variante
     document.addEventListener('change', function(e) {
         if (e.target.classList.contains('variante-imagenes')) {
             const varianteId = e.target.dataset.varianteId;
             const container = document.getElementById(`variante-imagenes-${varianteId}`);
             container.innerHTML = ''; // Limpiar previsualizaciones anteriores
             
             const files = Array.from(e.target.files);
             let validFiles = 0;
             let invalidFiles = 0;
             
             files.forEach((file, index) => {
                 if (file.type.startsWith('image/')) {
                     validFiles++;
                     const reader = new FileReader();
                     reader.onload = function(e) {
                         const preview = document.createElement('div');
                         preview.className = 'relative group';
                         preview.innerHTML = `
                             <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                                 <img src="${e.target.result}" alt="Preview ${index + 1}" class="h-full w-full object-cover object-center">
                                 <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                     <button type="button" class="remove-variante-image opacity-0 group-hover:opacity-100 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors duration-200" data-variante-id="${varianteId}" data-index="${index}">
                                         <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                         </svg>
                                     </button>
                                 </div>
                             </div>
                             <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">${file.name}</p>
                         `;
                         container.appendChild(preview);
                     };
                     reader.readAsDataURL(file);
                 } else {
                     invalidFiles++;
                 }
             });
             
             // Mostrar alertas de éxito/error
             if (validFiles > 0) {
                 showAlert(`✅ Se seleccionaron ${validFiles} imagen(es) válida(s) para la variante`, 'success');
             }
             
             if (invalidFiles > 0) {
                 showAlert(`⚠️ Se ignoraron ${invalidFiles} archivo(s) que no son imágenes válidas`, 'warning');
             }
         }
     });
     
     // Función para mostrar alertas
     function showAlert(message, type = 'info') {
         const alertDiv = document.createElement('div');
         alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
         
         let bgColor, textColor, icon;
         
         switch (type) {
             case 'success':
                 bgColor = 'bg-green-500';
                 textColor = 'text-white';
                 icon = '✅';
                 break;
             case 'warning':
                 bgColor = 'bg-yellow-500';
                 textColor = 'text-white';
                 icon = '⚠️';
                 break;
             case 'error':
                 bgColor = 'bg-red-500';
                 textColor = 'text-white';
                 icon = '❌';
                 break;
             default:
                 bgColor = 'bg-blue-500';
                 textColor = 'text-white';
                 icon = 'ℹ️';
         }
         
         alertDiv.className += ` ${bgColor} ${textColor}`;
         alertDiv.innerHTML = `
             <div class="flex items-center">
                 <span class="mr-2">${icon}</span>
                 <span class="text-sm font-medium">${message}</span>
                 <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white hover:text-gray-200">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                     </svg>
                 </button>
             </div>
         `;
         
         document.body.appendChild(alertDiv);
         
         // Animar entrada
         setTimeout(() => {
             alertDiv.classList.remove('translate-x-full');
         }, 100);
         
         // Auto-remover después de 5 segundos
         setTimeout(() => {
             alertDiv.classList.add('translate-x-full');
             setTimeout(() => {
                 if (alertDiv.parentElement) {
                     alertDiv.remove();
                 }
             }, 300);
         }, 5000);
     }
     
           // Función para remover imágenes de variantes
      document.addEventListener('click', function(e) {
          if (e.target.closest('.remove-variante-image')) {
              const button = e.target.closest('.remove-variante-image');
              const varianteId = button.dataset.varianteId;
              const index = parseInt(button.dataset.index);
              const input = document.querySelector(`input[data-variante-id="${varianteId}"]`);
              const dt = new DataTransfer();
              
              Array.from(input.files).forEach((file, i) => {
                  if (i !== index) {
                      dt.items.add(file);
                  }
              });
              
              input.files = dt.files;
              
              // Recrear previsualizaciones
              const event = new Event('change');
              input.dispatchEvent(event);
          }
          
          // Función para remover imágenes existentes de variantes
          if (e.target.closest('.remove-existing-image')) {
              const button = e.target.closest('.remove-existing-image');
              const imagenId = button.dataset.imagenId;
              const varianteId = button.dataset.varianteId;
              
              // Agregar el ID de la imagen a un campo hidden para marcarla como eliminada
              const hiddenInput = document.createElement('input');
              hiddenInput.type = 'hidden';
              hiddenInput.name = `variantes[${varianteId}][imagenes_eliminadas][]`;
              hiddenInput.value = imagenId;
              
              // Encontrar el contenedor de la variante y agregar el campo hidden
              const varianteContainer = button.closest('.bg-gray-50, .bg-gray-700');
              varianteContainer.appendChild(hiddenInput);
              
              // Eliminar la imagen del DOM
              const imageContainer = button.closest('.relative.group');
              imageContainer.remove();
              
              showAlert('✅ Imagen eliminada. Se eliminará al guardar el producto.', 'success');
          }
      });
     
     // Validación de formulario mejorada
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor completa todos los campos requeridos.');
        }
    });

    // JavaScript para cálculos dinámicos
    document.addEventListener('DOMContentLoaded', function() {
        const stockInicialInput = document.getElementById('stock_inicial');
        const stockMinimoInput = document.getElementById('stock_minimo');
        const stockMaximoInput = document.getElementById('stock_maximo');
        
        // Función para calcular y mostrar los umbrales
        function calcularUmbrales() {
            const stockInicial = parseInt(stockInicialInput.value) || 0;
            
            if (stockInicial > 0) {
                // Calcular umbrales
                const umbralBajo = Math.ceil((stockInicial * 60) / 100);
                const umbralCritico = Math.ceil((stockInicial * 20) / 100);
                
                // Actualizar campos
                stockMinimoInput.value = umbralCritico;
                stockMaximoInput.value = umbralBajo;
                
                // Actualizar ejemplos en los labels
                actualizarEjemplos(stockInicial, umbralBajo, umbralCritico);
                
                // Mostrar campos con colores apropiados
                stockMinimoInput.style.backgroundColor = '#dbeafe'; // bg-blue-50
                stockMaximoInput.style.backgroundColor = '#fef3c7'; // bg-yellow-50
                
                // Mostrar información de que se actualizarán al guardar
                mostrarInfoActualizacion();
            } else {
                // Si no hay stock inicial, mostrar 0
                stockMinimoInput.value = 0;
                stockMaximoInput.value = 0;
                
                // Ocultar ejemplos
                ocultarEjemplos();
                
                // Campos en gris
                stockMinimoInput.style.backgroundColor = '#f3f4f6'; // bg-gray-100
                stockMaximoInput.style.backgroundColor = '#f3f4f6'; // bg-gray-100
                
                // Ocultar información de actualización
                ocultarInfoActualizacion();
            }
        }
        
        // Función para actualizar los ejemplos en tiempo real
        function actualizarEjemplos(stockInicial, umbralBajo, umbralCritico) {
            const ejemploMinimo = stockMinimoInput.parentNode.querySelector('.text-xs');
            const ejemploMaximo = stockMaximoInput.parentNode.querySelector('.text-xs');
            
            if (ejemploMinimo && ejemploMaximo) {
                ejemploMinimo.innerHTML = `
                    <span class="font-medium">Se calcula automáticamente:</span> 20% del stock inicial
                    <br><span class="text-gray-500">Ejemplo: Stock inicial ${stockInicial} → Umbral crítico ${umbralCritico}</span>
                    <br><span class="text-blue-600 font-medium">Se actualizará al guardar el producto</span>
                `;
                
                ejemploMaximo.innerHTML = `
                    <span class="font-medium">Se calcula automáticamente:</span> 60% del stock inicial
                    <br><span class="text-gray-500">Ejemplo: Stock inicial ${stockInicial} → Umbral bajo ${umbralBajo}</span>
                    <br><span class="text-yellow-600 font-medium">Se actualizará al guardar el producto</span>
                `;
            }
        }
        
        // Función para ocultar ejemplos
        function ocultarEjemplos() {
            const ejemploMinimo = stockMinimoInput.parentNode.querySelector('.text-xs');
            const ejemploMaximo = stockMaximoInput.parentNode.querySelector('.text-xs');
            
            if (ejemploMinimo && ejemploMaximo) {
                ejemploMinimo.innerHTML = `
                    <span class="font-medium">Se calcula automáticamente:</span> 20% del stock inicial
                `;
                
                ejemploMaximo.innerHTML = `
                    <span class="font-medium">Se calcula automáticamente:</span> 60% del stock inicial
                `;
            }
        }
        
        // Función para mostrar información de actualización
        function mostrarInfoActualizacion() {
            let infoContainer = document.getElementById('info-actualizacion-umbrales');
            if (!infoContainer) {
                infoContainer = document.createElement('div');
                infoContainer.id = 'info-actualizacion-umbrales';
                infoContainer.className = 'col-span-full p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 mb-4';
                infoContainer.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-blue-700 dark:text-blue-300">
                            <strong>Nota:</strong> Los umbrales mostrados son previsualizaciones. Se actualizarán automáticamente en la base de datos al guardar el producto.
                        </span>
                    </div>
                `;
                
                // Insertar después del primer div de la grilla
                const primeraColumna = document.querySelector('.grid.grid-cols-1.gap-6.md\\:grid-cols-4');
                if (primeraColumna) {
                    primeraColumna.parentNode.insertBefore(infoContainer, primeraColumna);
                }
            }
        }
        
        // Función para ocultar información de actualización
        function ocultarInfoActualizacion() {
            const infoContainer = document.getElementById('info-actualizacion-umbrales');
            if (infoContainer) {
                infoContainer.remove();
            }
        }
        
        // Event listener para cambios en el stock inicial
        stockInicialInput.addEventListener('input', calcularUmbrales);
        
        // Calcular umbrales al cargar la página
        calcularUmbrales();
        
        // Mostrar información adicional sobre la nueva lógica
        const infoContainer = document.createElement('div');
        infoContainer.className = 'col-span-full p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 mb-6';
        infoContainer.innerHTML = `
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-green-800 dark:text-green-200">Lógica de Alertas de Stock</h4>
                    <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                        Los umbrales de alerta se calculan automáticamente basándose en el stock inicial:
                    </p>
                    <ul class="text-sm text-green-700 dark:text-green-300 mt-2 space-y-1">
                        <li>• <strong>Umbral Bajo:</strong> 60% del stock inicial (campo "Stock Inicial")</li>
                        <li>• <strong>Umbral Crítico:</strong> 20% del stock inicial (campo "Stock Inicial")</li>
                        <li>• <strong>Los campos se actualizan automáticamente</strong> cuando cambias el stock inicial</li>
                        <li>• <strong>Los valores se guardan en la base de datos</strong> al enviar el formulario</li>
                    </ul>
                </div>
            </div>
        `;
        
        // Insertar después del primer div de la grilla
        const primeraColumna = document.querySelector('.grid.grid-cols-1.gap-6.md\\:grid-cols-4');
        if (primeraColumna) {
            primeraColumna.parentNode.insertBefore(infoContainer, primeraColumna);
        }
        
        // Agregar validación antes de enviar el formulario
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const stockInicial = parseInt(stockInicialInput.value) || 0;
                
                if (stockInicial <= 0) {
                    e.preventDefault();
                    alert('El stock inicial debe ser mayor a 0 para calcular los umbrales de alerta.');
                    stockInicialInput.focus();
                    return false;
                }
                
                // Mostrar confirmación de actualización de umbrales
                if (confirm('¿Estás seguro de que quieres actualizar el producto? Los umbrales de alerta se recalcularán automáticamente.')) {
                    return true;
                } else {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
</script>
@endpush

