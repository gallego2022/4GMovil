@extends('layouts.app-new')

@section('title', 'Nuevo Producto - 4GMovil')

@section('content')
<!-- Notificaciones -->
<x-notifications />

<div class="space-y-6">
    <!-- Encabezado -->
    <div>
        <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">Nuevo Producto</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ingresa los detalles del nuevo producto</p>
    </div>

    <!-- Formulario -->
    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-800/5 sm:rounded-xl md:col-span-2">
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="px-4 py-6 sm:p-8">
            @csrf
            @include('pages.admin.productos.form')

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('productos.listadoP') }}" 
                   class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100 hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="inline-flex justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-emerald-600 hover:to-teal-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Crear Producto
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('imagenes');
    const preview = document.getElementById('preview-container');
    let filesArray = [];

    input.addEventListener('change', () => {
        filesArray = Array.from(input.files);
        renderPreviews();
    });

    function renderPreviews() {
        preview.innerHTML = '';
        filesArray.forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = e => {
                const col = document.createElement('div');
                col.className = 'relative inline-block mr-4 mb-4';

                const imgContainer = document.createElement('div');
                imgContainer.className = 'relative group';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-24 h-24 object-cover rounded-lg shadow-md dark:shadow-gray-700/50';
                img.alt = 'Previsualización';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition-colors duration-200 opacity-0 group-hover:opacity-100';
                removeBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                `;
                removeBtn.title = 'Eliminar imagen';
                removeBtn.onclick = () => {
                    filesArray.splice(index, 1);
                    updateInputFiles();
                    renderPreviews();
                };

                imgContainer.appendChild(img);
                imgContainer.appendChild(removeBtn);
                col.appendChild(imgContainer);
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        filesArray.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }
});
</script>
@endpush

@endsection
