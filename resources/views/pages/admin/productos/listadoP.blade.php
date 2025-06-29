@extends('layouts.app-new')

@section('title', 'Listado de Productos - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">Listado de Productos</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Gestiona todos los productos de tu catálogo</p>
        </div>
        <div class="mt-4 sm:ml-4 sm:mt-0">
            <a href="{{ route('productos.create') }}" 
               class="inline-flex items-center rounded-md bg-brand-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Crear Producto
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="space-y-1">
            <label for="filtroCategoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por categoría</label>
            <select id="filtroCategoria" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 pl-3 pr-10 text-base text-gray-900 dark:text-gray-100 focus:border-brand-500 focus:outline-none focus:ring-brand-500 sm:text-sm">
                <option value="">Todas</option>
                @foreach($categorias as $categoria)
                <option value="{{ strtolower($categoria->nombre_categoria) }}">{{ $categoria->nombre_categoria }}</option>
                @endforeach
            </select>
        </div>

        <div class="space-y-1">
            <label for="filtroMarca" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por marca</label>
            <select id="filtroMarca" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 pl-3 pr-10 text-base text-gray-900 dark:text-gray-100 focus:border-brand-500 focus:outline-none focus:ring-brand-500 sm:text-sm">
                <option value="">Todas</option>
                @foreach($marcas as $marca)
                <option value="{{ strtolower($marca->nombre_marca) }}">{{ $marca->nombre_marca }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end">
            <button id="limpiarFiltros" class="inline-flex w-full items-center justify-center rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 011.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
                Limpiar Filtros
            </button>
        </div>
    </div>

    <!-- Vista móvil (cards) -->
    <div class="grid grid-cols-1 gap-4 sm:hidden" id="mobileCards">
        <!-- Campo de búsqueda móvil -->
        <div class="mb-4">
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

        @forelse($productos as $producto)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden producto-card">
            <div class="p-4">
                <div class="flex items-start space-x-4">
                    <!-- Imagen del producto -->
                    <div class="flex-shrink-0">
                        @if($producto->imagenes->isNotEmpty())
                        <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                             class="h-24 w-24 rounded-lg object-cover shadow-sm" 
                             alt="{{ $producto->nombre_producto }}">
                        @else
                        <img src="{{ asset('img/Logo_2.png') }}" 
                             class="h-24 w-24 rounded-lg object-cover shadow-sm" 
                             alt="Sin imagen">
                        @endif
                    </div>
                    <!-- Información del producto -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                            {{ $producto->nombre_producto }}
                        </h3>
                        <div class="mt-1 flex flex-col space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                ID: {{ $producto->producto_id }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                ${{ number_format($producto->precio, 2) }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Stock: {{ $producto->stock }}
                            </p>
                            <div class="flex items-center space-x-2">
                                @if($producto->estado == 'Nuevo')
                                <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                    {{ $producto->estado }}
                                </span>
                                @else
                                <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                    {{ $producto->estado }}
                                </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Categoría: {{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Marca: {{ $producto->marca->nombre_marca ?? 'Sin marca' }}
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Botones de acción -->
                <div class="mt-4 flex justify-end space-x-2">
                    <a href="{{ route('productos.edit', $producto) }}" 
                       class="inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/>
                        </svg>
                        Editar
                    </a>
                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="form-eliminar inline">
                        @csrf
                        @method('DELETE')
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
        @empty
        <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
            No hay productos registrados en el sistema
        </div>
        @endforelse
    </div>

    <!-- Vista escritorio (tabla) -->
    <div class="hidden sm:block">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg dark:ring-white dark:ring-opacity-10">
                    <table id="tablaProductos" class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">ID</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Imagen</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Nombre</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Precio</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Stock</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Estado</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Categoría</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Marca</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                            @forelse($productos as $producto)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800" 
                                data-categoria="{{ strtolower($producto->categoria->nombre_categoria ?? '') }}"
                                data-marca="{{ strtolower($producto->marca->nombre_marca ?? '') }}">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">
                                    {{ $producto->producto_id }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($producto->imagenes->isNotEmpty())
                                    <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                                         class="h-12 w-12 rounded-md object-cover shadow-sm" 
                                         alt="{{ $producto->nombre_producto }}">
                                    @else
                                    <img src="{{ asset('img/Logo_2.png') }}" 
                                         class="h-12 w-12 rounded-md object-cover shadow-sm" 
                                         alt="Sin imagen">
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $producto->nombre_producto }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-gray-100">${{ number_format($producto->precio, 2) }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $producto->stock }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($producto->estado == 'Nuevo')
                                    <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                        {{ $producto->estado }}
                                    </span>
                                    @else
                                    <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                        {{ $producto->estado }}
                                    </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                    {{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                    {{ $producto->marca->nombre_marca ?? 'Sin marca' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('productos.edit', $producto) }}" 
                                           class="inline-flex items-center rounded-md bg-white dark:bg-gray-800 px-2.5 py-1.5 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            Editar
                                        </a>
                                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="form-eliminar inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-950 px-2.5 py-1.5 text-sm font-semibold text-red-700 dark:text-red-300 shadow-sm ring-1 ring-inset ring-red-600/20 dark:ring-red-900/20 hover:bg-red-100 dark:hover:bg-red-900">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No hay productos registrados en el sistema
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: "{{ session('success') }}",
        timer: 2500,
        showConfirmButton: false
    });
</script>
@endif

@if(session('eliminado') == 'ok')
<script>
    Swal.fire({
        title: 'Eliminado',
        text: 'El producto ha sido eliminado correctamente.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializar DataTable solo para la vista de escritorio
        var table = $('#tablaProductos').DataTable({
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
            dom: "<'flex flex-col sm:flex-row items-center justify-between'<'flex-none sm:flex-1'l><'flex-none sm:flex-1'f>>" +
                 "<'overflow-x-auto'tr>" +
                 "<'flex flex-col sm:flex-row items-center justify-between'<'flex-none sm:flex-1'i><'flex-none sm:flex-1'p>>",
            pagingType: "simple_numbers",
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            pageLength: 10,
            drawCallback: function(settings) {
                // Aplicar estilos a los botones de paginación
                $('.paginate_button').addClass('relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0');
                $('.paginate_button.current').addClass('z-10 bg-brand-600 text-white hover:bg-brand-500 ring-brand-600').removeClass('text-gray-900 ring-gray-300');
                $('.paginate_button.disabled').addClass('opacity-50 cursor-not-allowed');

                // Aplicar estilos dark mode a elementos de DataTables
                $('.dataTables_length select').addClass('dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700');
                $('.dataTables_filter input').addClass('dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700');
                $('.dataTables_info').addClass('dark:text-gray-100');
            }
        });

        // Función para filtrar tarjetas móviles
        function filterMobileCards() {
            var categoriaValue = $('#filtroCategoria').val().toLowerCase();
            var marcaValue = $('#filtroMarca').val().toLowerCase();

            $('#mobileCards .producto-card').each(function() {
                var card = $(this);
                var categoria = card.find('p:contains("Categoría:")').text().toLowerCase();
                var marca = card.find('p:contains("Marca:")').text().toLowerCase();
                
                var categoriaMatch = !categoriaValue || categoria.includes(categoriaValue);
                var marcaMatch = !marcaValue || marca.includes(marcaValue);
                
                if (categoriaMatch && marcaMatch) {
                    card.show();
                } else {
                    card.hide();
                }
            });
        }

        // Configuración de filtros personalizados
        $('#filtroCategoria, #filtroMarca').on('change', function() {
            var categoriaValue = $('#filtroCategoria').val().toLowerCase();
            var marcaValue = $('#filtroMarca').val().toLowerCase();

            // Filtrar tabla
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var categoriaMatch = !categoriaValue || data[6].toLowerCase().includes(categoriaValue);
                    var marcaMatch = !marcaValue || data[7].toLowerCase().includes(marcaValue);
                    return categoriaMatch && marcaMatch;
                }
            );

            table.draw();
            $.fn.dataTable.ext.search.pop();

            // Filtrar tarjetas móviles
            filterMobileCards();
        });

        // Limpiar filtros
        $('#limpiarFiltros').on('click', function() {
            $('#filtroCategoria, #filtroMarca').val('');
            table.search('').columns().search('').draw();
            filterMobileCards();
        });

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
            $('#mobileCards .producto-card').each(function() {
                const card = $(this);
                const nombre = card.find('h3').text().toLowerCase();
                const id = card.find('p:contains("ID:")').text().toLowerCase();
                const precio = card.find('p:contains("$")').text().toLowerCase();
                const categoria = card.find('p:contains("Categoría:")').text().toLowerCase();
                const marca = card.find('p:contains("Marca:")').text().toLowerCase();
                const estado = card.find('span').text().toLowerCase();
                
                if (nombre.includes(searchTerm) || 
                    id.includes(searchTerm) || 
                    precio.includes(searchTerm) || 
                    categoria.includes(searchTerm) || 
                    marca.includes(searchTerm) || 
                    estado.includes(searchTerm)) {
                    card.show();
                } else {
                    card.hide();
                }
            });

            // Mostrar mensaje cuando no hay resultados
            const visibleCards = $('#mobileCards .producto-card:visible').length;
            const noResultsMsg = $('#mobileNoResults');
            
            if (visibleCards === 0) {
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
@endpush

@endsection