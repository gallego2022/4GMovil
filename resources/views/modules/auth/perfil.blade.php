@extends('layouts.app-new')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Mensaje de éxito/error -->
        @if(session('mensaje'))
            <div class="mb-4 rounded-md p-4 {{ session('tipo', 'success') === 'success' ? 'bg-green-50 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200' }}">
                <div class="flex">
                    <div class="flex-shrink-0">
                        @if(session('tipo', 'success') === 'success')
                            <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('mensaje') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Sección de Foto de Perfil -->
                    <div class="md:col-span-1">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md text-center">
                            <div class="relative inline-block">
                                @if($usuario->foto_perfil)
                                    <img src="{{ asset('storage/' . $usuario->foto_perfil) }}" 
                                         alt="{{ __('profile.photo_alt', ['name' => $usuario->nombre_usuario]) }}" 
                                         class="rounded-full w-48 h-48 mx-auto object-cover border-4 border-blue-200 dark:border-blue-700">
                                    <button type="button" 
                                            onclick="confirmarEliminarFoto()"
                                            class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @else
                                    <div class="w-48 h-48 mx-auto rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-24 h-24 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <h2 class="mt-4 text-xl font-semibold text-gray-800 dark:text-white">{{ $usuario->nombre_usuario }}</h2>
                            <p class="text-gray-500 dark:text-gray-400">{{ $usuario->correo_electronico }}</p>
                            
                            <!-- Botón para cambiar contraseña -->
                            <a href="{{ route('cambiar.contrasena') }}" 
                               class="mt-4 inline-flex items-center px-4 py-2 bg-brand-600 text-white rounded-md hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors duration-200 dark:focus:ring-offset-gray-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                {{ __('profile.change_password') }}
                            </a>
                        </div>
                    </div>

                    <!-- Formulario de Edición -->
                    <div class="md:col-span-2">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">{{ __('profile.edit_profile') }}</h3>

                            @if($errors->any())
                                <div class="mb-4 bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <ul class="list-disc list-inside text-sm">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('perfil.actualizar') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf
                                @method('PUT')

                                <!-- Nombre de Usuario -->
                                <div>
                                    <label for="nombre_usuario" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('validation.attributes.nombre_usuario') }}
                                    </label>
                                    <input type="text" 
                                           id="nombre_usuario" 
                                           name="nombre_usuario" 
                                           value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}"
                                           class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm @error('nombre_usuario') border-red-400 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 @enderror"
                                           required>
                                    @error('nombre_usuario')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Correo Electrónico -->
                                <div>
                                    <label for="correo_electronico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('validation.attributes.correo_electronico') }}
                                    </label>
                                    <input type="email" 
                                           id="correo_electronico" 
                                           name="correo_electronico" 
                                           value="{{ old('correo_electronico', $usuario->correo_electronico) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm @error('correo_electronico') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 @enderror"
                                           required>
                                    @error('correo_electronico')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('validation.attributes.telefono') }}
                                    </label>
                                    <input type="tel" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono', $usuario->telefono) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm @error('telefono') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 @enderror">
                                    @error('telefono')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Foto de Perfil -->
                                <div>
                                    <label for="foto_perfil" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('profile.photo') }}
                                    </label>
                                    <div class="mt-1 flex items-center">
                                        <input type="file" 
                                               id="foto_perfil" 
                                               name="foto_perfil" 
                                               accept="image/*"
                                               class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 dark:file:bg-brand-900 file:text-brand-700 dark:file:text-brand-300 hover:file:bg-brand-100 dark:hover:file:bg-brand-800 @error('foto_perfil') border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 @enderror">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('profile.photo_requirements') }}</p>
                                    @error('foto_perfil')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Botón de Guardar -->
                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ __('profile.save_changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmarEliminarFoto() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas eliminar tu foto de perfil?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('perfil.eliminarFoto') }}", {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.tipo === 'success') {
                    Swal.fire({
                        title: 'Eliminada!',
                        text: data.mensaje,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.mensaje || 'Error al eliminar la foto');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo eliminar la foto de perfil'
                });
            });
        }
    });
}
</script>
@endpush

@endsection