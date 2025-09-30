@extends('layouts.app-new')

@section('title', __('admin.actions.edit') . ' ' . __('admin.fields.brand') . ' - 4GMovil')

@section('content')
    <div class="space-y-6">
        <!-- Encabezado -->
        <div>
            <h2
                class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:truncate sm:text-3xl sm:tracking-tight">
               Editar {{ __('admin.fields.brand') }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Modifica los datos de la marca seleccionada</p>
        </div>

        <!-- Formulario -->
        <form action="{{ route('marcas.update', $marca->marca_id) }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')

            <div class="space-y-12">
                <div
                    class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 dark:border-gray-700 pb-12 md:grid-cols-3">
                    <div>
                        <h2 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-100">Información de la {{ __('admin.fields.brand') }}</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
                            Esta información se mostrará en el catálogo de productos.
                        </p>
                    </div>

                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                        <div class="sm:col-span-4">
                            <x-validation-field 
                                name="nombre"
                                label="Nombre de la marca"
                                type="text"
                                placeholder="Nombre de la marca"
                                :required="true"
                                :rules="['required', 'minLength:2', 'maxLength:100', 'noSpecialChars', 'brandFormat']"
                                :messages="[
                                    'required' => 'El nombre de la marca es requerido',
                                    'minLength' => 'Mínimo 2 caracteres',
                                    'maxLength' => 'Máximo 100 caracteres',
                                    'noSpecialChars' => 'No debe contener caracteres especiales',
                                    'brandFormat' => 'Debe empezar con mayúscula'
                                ]"
                                help-text="Nombre de la marca del producto (solo letras, números y espacios)"
                                :value="old('nombre', $marca->nombre)"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('marcas.index') }}"
                    class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 transition-colors duration-200">
                   {{ __('admin.actions.cancel') }}                </a>
                <button type="submit"
                    class=" inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 hover:shadow-xl">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                    Actualizar {{ __('admin.fields.brand') }}                </button>
            </div>
        </form>
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "{{ session('success') }}",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: "{{ session('error') }}",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
            });
        </script>
    @endif

@endsection
