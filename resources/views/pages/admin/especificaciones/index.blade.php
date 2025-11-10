@extends('layouts.app-new')

@section('title', __('admin.specifications.title') . ' por ' . __('admin.fields.category') . ' - 4GMovil')

@push('datatables-css')
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">
@endpush

@push('jquery-script')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
@endpush

@push('datatables-script')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
@endpush

@section('content')
    <!-- Notificaciones -->
    <x-notifications />

    <div class="space-y-6">
        <!-- Vista móvil (cards) -->
        <div class="grid grid-cols-1 gap-4 sm:hidden" id="mobileCards">
            <!-- Encabezado móvil -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('admin.specifications.title') }} por {{ __('admin.fields.category') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-300">Gestiona las especificaciones técnicas de cada
                        categoría</p>
                </div>

                <!-- Botones de acción móvil -->
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <!-- Botón Crear Especificación -->
                    <a href="{{ route('admin.especificaciones.create') }}"
                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-slate-600 to-gray-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-slate-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 hover:shadow-xl min-w-[180px] justify-center">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 transition-transform duration-300 group-hover:rotate-180"
                            viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                       Crear Especificación
                    </a>
                </div>

                <!-- Campo de búsqueda móvil -->
                <div class="relative mt-2 rounded-md shadow-sm">
                    <input type="text" id="busquedaMovil"
                        class="block w-full rounded-md border-0 py-1.5 pl-4 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-800 sm:text-sm sm:leading-6"
                        placeholder="{{ __('admin.actions.search') }} especificaciones...">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            @forelse($especificaciones as $especificacion)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden especificacion-card">
                    <div class="p-4">
                        <div class="flex items-start space-x-4">
                            <!-- Icono de especificación -->
                            <div class="flex-shrink-0">
                                <div
                                    class="h-16 w-16 rounded-lg bg-brand-100 dark:bg-brand-900 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-brand-600 dark:text-brand-400" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <!-- Información de la especificación -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                                    {{ $especificacion->etiqueta }}
                                </h3>
                                <div class="mt-1 flex flex-col space-y-1">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                       {{ __('admin.fields.category') }}: {{ $especificacion->categoria->nombre ?? __('admin.fields.without_category') }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                       {{ __('admin.fields.type') }}: {{ ucfirst($especificacion->tipo_campo) }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                       {{ __('admin.fields.required') }}: {{ $especificacion->requerido ? __('admin.status.yes') : __('admin.status.no') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Botones de acción -->
                        <div class="mt-4 flex justify-end space-x-2">
                            <a href="{{ route('admin.especificaciones.edit', $especificacion->especificacion_id) }}"
                                class="inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-300" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                                </svg>
                               {{ __('admin.actions.edit') }}                            </a>
                            <form action="{{ route('admin.especificaciones.destroy', $especificacion->especificacion_id) }}"
                                  method="POST" 
                                  class="inline confirm-action"
                                  data-title="¿Eliminar especificación?"
                                  data-message="¿Estás seguro de eliminar la especificación {{ $especificacion->nombre }}?"
                                  data-confirm-text="Sí, eliminar"
                                  data-method="DELETE">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-950 px-3 py-2 text-sm font-semibold text-red-700 dark:text-red-300 shadow-sm ring-1 ring-inset ring-red-600/20 dark:ring-red-900/20 hover:bg-red-100 dark:hover:bg-red-900">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                                            clip-rule="evenodd" />
                                    </svg>
                                   {{ __('admin.actions.delete') }}                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                   {{ __('admin.status.no') }} hay especificaciones registradas en el sistema
                </div>
            @endforelse
        </div>

        <!-- Vista escritorio (tabla) -->
        <div class="hidden sm:block">
            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-md">
                <!-- Encabezado -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                        <div>
                            <h2
                                class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                               {{ __('admin.specifications.title') }} por {{ __('admin.fields.category') }}</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Gestiona las especificaciones técnicas
                                de cada categoría</p>
                        </div>

                        <!-- Botón Crear Especificación -->
                        <a href="{{ route('admin.especificaciones.create') }}"
                            class="group inline-flex items-center rounded-xl bg-gradient-to-r from-slate-600 to-gray-700 px-8 py-4 text-base font-semibold text-white shadow-lg hover:from-slate-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 hover:shadow-xl min-w-[180px] justify-center">
                            <svg class="-ml-0.5 mr-3 h-6 w-6 transition-transform duration-300 group-hover:rotate-90"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                           Crear Especificación
                        </a>
                    </div>
                </div>

                <!-- Barra de herramientas con búsqueda y filtros -->
                <div
                    class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                        <!-- Búsqueda personalizada -->
                        <div class="flex-1 max-w-md">
                            <label for="busquedaEscritorio"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                               {{ __('admin.actions.search') }} especificaciones
                            </label>
                            <div class="relative">
                                <input type="text" id="busquedaEscritorio"
                                    class="block w-full rounded-md border-0 py-2 pl-10 pr-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-700 sm:text-sm"
                                    placeholder="{{ __('admin.actions.search') }} por nombre, categoría...">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Información de registros -->
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span id="infoRegistros">Mostrando todas las especificaciones</span>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table id="tablaEspecificaciones" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead
                                class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 text-gray-700 dark:text-gray-300 text-sm font-semibold">
                                <tr>
                                    <th class="px-6 py-4 text-left font-semibold">ID</th>
                                    <th class="px-6 py-4 text-left font-semibold">{{ __('admin.fields.name') }}</th>
                                    <th class="px-6 py-4 text-left font-semibold">{{ __('admin.fields.category') }}</th>
                                    <th class="px-6 py-4 text-left font-semibold">{{ __('admin.fields.type') }}</th>
                                    <th class="px-6 py-4 text-center font-semibold">{{ __('admin.fields.required') }}</th>
                                    <th class="px-6 py-4 text-center font-semibold">{{ __('admin.fields.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm">
                                @forelse($especificaciones as $especificacion)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200 ease-in-out border-b border-gray-100 dark:border-gray-800">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                            {{ $especificacion->especificacion_id }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                            {{ $especificacion->etiqueta }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                            {{ $especificacion->categoria->nombre ?? __('admin.fields.without_category') }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                            {{ ucfirst($especificacion->tipo_campo) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($especificacion->requerido)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                   {{ __('admin.status.yes') }}                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                   {{ __('admin.status.no') }}                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 flex items-center gap-3 justify-center">
                                            <!-- Botón Editar -->
                                            <div class="relative group">
                                                <a href="{{ route('admin.especificaciones.edit', $especificacion->especificacion_id) }}"
                                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/50 dark:hover:bg-blue-900 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-all duration-200 ease-in-out transform hover:scale-110">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536M9 13l6-6m-6 6v3h3l6-6m-3-3L6 18H3v-3L15.232 5.232z" />
                                                    </svg>
                                                </a>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg dark:bg-gray-700 z-10 whitespace-nowrap">
                                                   {{ __('admin.actions.edit') }}                                                </div>
                                            </div>

                                            <!-- Botón Eliminar -->
                                            <div class="relative group">
                                                <form
                                                    action="{{ route('admin.especificaciones.destroy', $especificacion->especificacion_id) }}"
                                                    method="POST" 
                                                    class="inline confirm-action"
                                                    data-title="¿Eliminar especificación?"
                                                    data-message="¿Estás seguro de eliminar esta especificación?"
                                                    data-confirm-text="Sí, eliminar"
                                                    data-method="DELETE">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-all duration-200 ease-in-out transform hover:scale-110">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg dark:bg-gray-700 z-10 whitespace-nowrap">
                                                   {{ __('admin.actions.delete') }}                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                                           {{ __('admin.status.no') }} hay especificaciones registradas en el sistema
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Verificar si hay datos antes de inicializar DataTables
                var hasData = {{ $especificaciones->count() > 0 ? 'true' : 'false' }};

                if (hasData) {
                    // Inicializar DataTable solo si hay datos
                    var table = $('#tablaEspecificaciones').DataTable({
                        dom: 'rtip',
                        language: {
                            "sProcessing": "Procesando...",
                            "sLengthMenu": "{{ __('admin.actions.show') }} _MENU_ registros",
                            "sZeroRecords": "{{ __('admin.status.no') }} se encontraron resultados",
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
                                "sFirst": "{{ __('admin.actions.first') }}",
                                "sLast": "{{ __('admin.actions.last') }}",
                                "sNext": "{{ __('admin.actions.next') }}",
                                "sPrevious": "{{ __('admin.actions.previous') }}"
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
                        columnDefs: [{
                                orderable: false,
                                targets: [-1]
                            },
                            {
                                searchable: false,
                                targets: [-1]
                            }
                        ],
                        order: [
                            [0, 'asc']
                        ],
                        responsive: true,
                        pagingType: "simple_numbers",
                        lengthMenu: [
                            [10, 25, 50, -1],
                            [10, 25, 50, "{{ __('admin.actions.all') }}"]
                        ],
                        pageLength: 10,
                        deferRender: true,
                        drawCallback: function(settings) {
                            // Aplicar estilos a los botones de paginación
                            $('.paginate_button').addClass(
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0'
                                );
                            $('.paginate_button.current').addClass(
                                    'z-10 bg-brand-600 text-white hover:bg-brand-500 ring-brand-600')
                                .removeClass('text-gray-900 ring-gray-300');
                            $('.paginate_button.disabled').addClass('opacity-50 cursor-not-allowed');

                            // Aplicar estilos dark mode a elementos de DataTables
                            $('.dataTables_length select').addClass(
                                'dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700');
                            $('.dataTables_info').addClass('dark:text-gray-100');

                            // Actualizar información de registros después de un pequeño delay
                            setTimeout(function() {
                                if (typeof updateInfoRegistros === 'function') {
                                    updateInfoRegistros();
                                }
                            }, 100);
                        },
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
                                $('#infoRegistros').text(
                                    `Mostrando ${startRecord} a ${endRecord} de ${totalRecords} especificaciones`);
                            } else {
                                $('#infoRegistros').text(
                                    `Mostrando ${startRecord} a ${endRecord} de ${filteredRecords} especificaciones (filtrado de ${totalRecords} total)`
                                    );
                            }
                        } catch (error) {
                            console.error('Error al actualizar información de registros:', error);
                            $('#infoRegistros').text('{{ __('admin.messages.info') }} no disponible');
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
                } else {
                    // Si no hay datos, ocultar elementos de DataTables
                    $('.dataTables_paginate').hide();
                    $('.dataTables_length').hide();
                    $('.dataTables_info').hide();
                    $('#infoRegistros').text('{{ __('admin.status.no') }} hay especificaciones registradas');
                }

                // Confirmación para eliminar especificación
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
                        cancelButtonText: '{{ __('admin.actions.cancel') }}'
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

                    $('#mobileCards .especificacion-card').each(function() {
                        const card = $(this);

                        // Obtener todos los textos de la tarjeta
                        const nombre = card.find('h3').text().toLowerCase();
                        const categoria = card.find('p').filter(function() {
                            return $(this).text().includes('Categoría:');
                        }).text().toLowerCase();
                        const tipo = card.find('p').filter(function() {
                            return $(this).text().includes('Tipo:');
                        }).text().toLowerCase();

                        // Verificar si el término de búsqueda coincide con algún campo
                        const matchFound = nombre.includes(searchTerm) || categoria.includes(searchTerm) || tipo
                            .includes(searchTerm);

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
                           {{ __('admin.status.no') }} se encontraron especificaciones que coincidan con la búsqueda
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
