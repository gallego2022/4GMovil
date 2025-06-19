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

<div class="space-y-6">
    <!-- Nombre del producto -->
    <div>
        <label for="nombre_producto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
        <div class="mt-1">
            <input type="text" 
                   name="nombre_producto" 
                   id="nombre_producto"
                   class="block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:bg-gray-800 dark:text-gray-100 @error('nombre_producto') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                   placeholder="Nombre del producto"
                   required 
                   value="{{ old('nombre_producto', $producto->nombre_producto ?? '') }}">
        </div>
        @error('nombre_producto')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Descripción -->
    <div>
        <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
        <div class="mt-1">
            <textarea name="descripcion" 
                      id="descripcion"
                      rows="4"
                      placeholder="Describe el producto"
                      class="block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:bg-gray-800 dark:text-gray-100 @error('descripcion') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
        </div>
        @error('descripcion')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Precio y Stock -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                </div>
                <input type="number" 
                       name="precio" 
                       id="precio"
                       step="0.01"
                       min="0"
                       placeholder="0.00"
                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 pl-7 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:bg-gray-800 dark:text-gray-100 @error('precio') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                       required 
                       value="{{ old('precio', $producto->precio ?? '') }}">
            </div>
            @error('precio')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock</label>
            <div class="mt-1">
                <input type="number" 
                       name="stock" 
                       id="stock"
                       min="0"
                       placeholder="Cantidad disponible"
                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:bg-gray-800 dark:text-gray-100 @error('stock') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500 @enderror" 
                       required 
                       value="{{ old('stock', $producto->stock ?? '') }}">
            </div>
            @error('stock')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Estado -->
    <div>
        <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado del producto</label>
        <select name="estado" 
                id="estado" 
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:bg-gray-800 dark:text-gray-100">
            <option value="nuevo" {{ old('estado', $producto->estado ?? '') == 'nuevo' ? 'selected' : '' }}>Nuevo</option>
            <option value="usado" {{ old('estado', $producto->estado ?? '') == 'usado' ? 'selected' : '' }}>Usado</option>
        </select>
        @error('estado')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Categoría y Marca -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoría</label>
            <select name="categoria_id" 
                    id="categoria_id"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:bg-gray-800 dark:text-gray-100 @error('categoria_id') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 @enderror" 
                    required>
                <option value="">Selecciona una categoría</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->categoria_id }}"
                        {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->categoria_id ? 'selected' : '' }}>
                        {{ $categoria->nombre_categoria }}
                    </option>
                @endforeach
            </select>
            @error('categoria_id')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="marca_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Marca</label>
            <select name="marca_id" 
                    id="marca_id"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:bg-gray-800 dark:text-gray-100 @error('marca_id') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 @enderror" 
                    required>
                <option value="">Selecciona una marca</option>
                @foreach($marcas as $marca)
                    <option value="{{ $marca->marca_id }}"
                        {{ old('marca_id', $producto->marca_id ?? '') == $marca->marca_id ? 'selected' : '' }}>
                        {{ $marca->nombre_marca }}
                    </option>
                @endforeach
            </select>
            @error('marca_id')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Imágenes -->
    <div>
        <label for="imagenes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Imágenes</label>
        <div class="mt-1">
            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-md hover:border-brand-500 dark:hover:border-brand-400 transition-colors duration-200">
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
                                   accept="image/jpeg,image/png,image/jpg">
                        </label>
                        <p class="pl-1">o arrastrar y soltar</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF hasta 2MB</p>
                </div>
            </div>
        </div>
        @error('imagenes.*')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        <!-- Contenedor para previsualización -->
        <div id="preview-container" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4"></div>
    </div>
</div>

@push('scripts')
<script>
    // Previsualización de imágenes
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const container = document.getElementById('preview-container');
        container.innerHTML = ''; // Limpiar previsualizaciones anteriores
        
        Array.from(e.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'relative group';
                preview.innerHTML = `
                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                        <img src="${e.target.result}" alt="Preview" class="h-full w-full object-cover object-center">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 bg-black bg-opacity-50 group-hover:opacity-100 transition-opacity duration-200">
                            <div class="text-center">
                                <p class="text-sm text-white truncate px-2">${file.name}</p>
                                <p class="text-xs text-gray-300 mt-1">${(file.size / (1024 * 1024)).toFixed(2)} MB</p>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(preview);
            }
            reader.readAsDataURL(file);
        });
    });

    // Drag and drop
    const dropZone = document.querySelector('div.flex.justify-center');
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-brand-500', 'dark:border-brand-400');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-brand-500', 'dark:border-brand-400');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        const fileInput = document.getElementById('imagenes');
        
        // Crear un nuevo objeto FileList
        const dataTransfer = new DataTransfer();
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                dataTransfer.items.add(file);
            }
        });
        
        fileInput.files = dataTransfer.files;
        // Disparar el evento change manualmente
        fileInput.dispatchEvent(new Event('change'));
    }
</script>
@endpush
