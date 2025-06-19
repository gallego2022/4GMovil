@extends('layouts.app-new')

@section('title', 'Usuarios - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">Usuarios Registrados</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Gestiona todos los usuarios del sistema</p>
        </div>
        <div class="mt-4 sm:ml-4 sm:mt-0">
            <a href="{{ route('usuarios.create') }}" 
               class="inline-flex items-center rounded-md bg-brand-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Crear Usuario
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="space-y-1">
            <label for="filtroRol" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por rol</label>
            <select id="filtroRol" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 pl-3 pr-10 text-base text-gray-900 dark:text-gray-100 focus:border-brand-500 focus:outline-none focus:ring-brand-500 sm:text-sm">
                <option value="">Todos</option>
                <option value="admin">Administrador</option>
                <option value="cliente">Cliente</option>
                <option value="invitado">Invitado</option>
            </select>
        </div>

        <div class="space-y-1">
            <label for="filtroEstado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por estado</label>
            <select id="filtroEstado" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 py-2 pl-3 pr-10 text-base text-gray-900 dark:text-gray-100 focus:border-brand-500 focus:outline-none focus:ring-brand-500 sm:text-sm">
                <option value="">Todos</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
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

    <!-- Tabla/Cards -->
    <div class="mt-8 flow-root">
        <!-- Vista móvil (cards) -->
        <div class="grid grid-cols-1 gap-4 sm:hidden" id="mobileCards">
            @foreach($usuarios as $usuario)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden usuario-card" 
                 data-rol="{{ strtolower($usuario->rol) }}" 
                 data-estado="{{ $usuario->estado == 'activo' ? 'activo' : 'inactivo' }}">
                <div class="p-4 space-y-4">
                    <!-- Cabecera con foto y nombre -->
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @if($usuario->foto_perfil)
                                <img src="{{ asset('storage/' . $usuario->foto_perfil) }}" 
                                     class="h-12 w-12 rounded-full object-cover" 
                                     alt="{{ $usuario->nombre_usuario }}">
                            @else
                                <span class="inline-block h-12 w-12 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                                    <svg class="h-full w-full text-gray-300 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $usuario->nombre_usuario }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $usuario->usuario_id }}</p>
                        </div>
                    </div>

                    <!-- Información del usuario -->
                    <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $usuario->correo_electronico }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $usuario->telefono }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rol</dt>
                            <dd class="mt-1">
                                @switch($usuario->rol)
                                    @case('admin')
                                        <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-700/30">
                                            Admin
                                        </span>
                                        @break
                                    @case('cliente')
                                        <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10 dark:ring-gray-500/30">
                                            Cliente
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900 px-2 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-300 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-600/30">
                                            Invitado
                                        </span>
                                @endswitch
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd class="mt-1">
                                @if($usuario->estado == 'activo')
                                    <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                        Inactivo
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verificado</dt>
                            <dd class="mt-1">
                                @if($usuario->email_verified_at)
                                    <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                        Verificado
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                        No Verificado
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <!-- Acciones -->
                    <div class="mt-4 flex justify-end space-x-3">
                        <a href="{{ route('usuarios.edit', $usuario) }}" 
                           class="inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/>
                            </svg>
                            Editar
                        </a>
                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="form-eliminar inline">
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
            @endforeach
        </div>

        <!-- Vista escritorio (tabla) -->
        <div class="hidden sm:block -mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg dark:ring-white dark:ring-opacity-10">
                    <table id="tablaUsuarios" class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">#</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Foto</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Nombre</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Email</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Teléfono</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Rol</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Estado</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Email Verificado</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                            @foreach($usuarios as $usuario)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">
                                    {{ $usuario->usuario_id }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($usuario->foto_perfil)
                                        <img src="{{ asset('storage/' . $usuario->foto_perfil) }}" 
                                             class="h-10 w-10 rounded-full object-cover" 
                                             alt="{{ $usuario->nombre_usuario }}">
                                    @else
                                        <span class="inline-block h-10 w-10 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                                            <svg class="h-full w-full text-gray-300 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $usuario->nombre_usuario }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $usuario->correo_electronico }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $usuario->telefono }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                    @switch($usuario->rol)
                                        @case('admin')
                                            <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-700/30">
                                                Admin
                                            </span>
                                            @break
                                        @case('cliente')
                                            <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10 dark:ring-gray-500/30">
                                                Cliente
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900 px-2 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-300 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-600/30">
                                                Invitado
                                            </span>
                                    @endswitch
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                    @if($usuario->estado == 'activo')
                                        <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                    @if($usuario->email_verified_at)
                                        <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20 dark:ring-green-600/30">
                                            Verificado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20 dark:ring-red-600/30">
                                            No Verificado
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('usuarios.edit', $usuario) }}" 
                                           class="inline-flex items-center rounded-md bg-white dark:bg-gray-800 px-2.5 py-1.5 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            Editar
                                        </a>
                                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="form-eliminar inline">
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
                            @endforeach
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
        text: 'El usuario ha sido eliminado correctamente.',
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
        var table = $('#tablaUsuarios').DataTable({
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

        // Función para filtrar tarjetas en vista móvil
        function filterMobileCards() {
            const rolValue = $('#filtroRol').val().toLowerCase();
            const estadoValue = $('#filtroEstado').val().toLowerCase();

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
            table.column(5).search(rolValue).column(6).search(estadoValue).draw();
            
            // Filtrar tarjetas móviles
            filterMobileCards();
        });

        // Limpiar filtros en ambas vistas
        $('#limpiarFiltros').on('click', function() {
            $('#filtroRol, #filtroEstado').val('');
            
            // Limpiar filtros en tabla de escritorio
            table.columns([5, 6]).search('').draw();
            
            // Mostrar todas las tarjetas en móvil
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

        // Manejar cambios de tamaño de ventana
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth < 640) { // sm breakpoint
                    filterMobileCards(); // Reaplicar filtros en vista móvil
                } else {
                    table.columns.adjust().responsive.recalc(); // Reajustar tabla de escritorio
                }
            }, 250);
        });
    });
</script>
@endpush

@endsection