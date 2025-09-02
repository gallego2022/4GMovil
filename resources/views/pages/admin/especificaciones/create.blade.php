@extends('layouts.app-new')

@section('title', 'Crear Especificación - 4GMovil')

@push('css')
<style>
    .form-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .dark .form-section {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-color: #475569;
    }
    
    .field-type-preview {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 0.5rem;
    }
    
    .dark .field-type-preview {
        background: #334155;
        border-color: #475569;
    }
    
    .options-input {
        display: none;
    }
    
    .options-input.show {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Crear Nueva Especificación
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Define una nueva especificación técnica para una categoría de productos
                </p>
            </div>
            
            <!-- Botón Volver -->
            <a href="{{ route('admin.especificaciones.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Listado
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
        <form action="{{ route('admin.especificaciones.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <!-- Sección: Información Básica -->
            <div class="form-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Información Básica
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Categoría -->
                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <select id="categoria_id" name="categoria_id" required
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                            <option value="">Selecciona una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->categoria_id }}" {{ old('categoria_id') == $categoria->categoria_id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Orden -->
                    <div>
                        <label for="orden" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Orden de Visualización
                        </label>
                        <input type="number" id="orden" name="orden" value="{{ old('orden') }}" min="0"
                               class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm"
                               placeholder="Dejar vacío para auto-asignar">
                        @error('orden')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sección: Configuración del Campo -->
            <div class="form-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Configuración del Campo
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre del Campo -->
                    <div>
                        <label for="nombre_campo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre del Campo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre_campo" name="nombre_campo" value="{{ old('nombre_campo') }}" required
                               class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm"
                               placeholder="ej: ram, almacenamiento, pantalla">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Nombre técnico del campo (sin espacios, solo minúsculas)
                        </p>
                        @error('nombre_campo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Etiqueta -->
                    <div>
                        <label for="etiqueta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Etiqueta <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="etiqueta" name="etiqueta" value="{{ old('etiqueta') }}" required
                               class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm"
                               placeholder="ej: Memoria RAM, Almacenamiento, Tamaño de Pantalla">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Nombre visible para el usuario
                        </p>
                        @error('etiqueta')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Tipo de Campo -->
                    <div>
                        <label for="tipo_campo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo de Campo <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_campo" name="tipo_campo" required
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                            <option value="">Selecciona el tipo</option>
                            <option value="text" {{ old('tipo_campo') == 'text' ? 'selected' : '' }}>Texto</option>
                            <option value="textarea" {{ old('tipo_campo') == 'textarea' ? 'selected' : '' }}>Área de texto</option>
                            <option value="number" {{ old('tipo_campo') == 'number' ? 'selected' : '' }}>Número</option>
                            <option value="select" {{ old('tipo_campo') == 'select' ? 'selected' : '' }}>Selector</option>
                            <option value="checkbox" {{ old('tipo_campo') == 'checkbox' ? 'selected' : '' }}>Casilla de verificación</option>
                            <option value="radio" {{ old('tipo_campo') == 'radio' ? 'selected' : '' }}>Botones de radio</option>
                            <option value="date" {{ old('tipo_campo') == 'date' ? 'selected' : '' }}>Fecha</option>
                            <option value="email" {{ old('tipo_campo') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="url" {{ old('tipo_campo') == 'url' ? 'selected' : '' }}>URL</option>
                        </select>
                        @error('tipo_campo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unidad -->
                    <div>
                        <label for="unidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Unidad de Medida
                        </label>
                        <input type="text" id="unidad" name="unidad" value="{{ old('unidad') }}"
                               class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm"
                               placeholder="ej: GB, pulgadas, MP, mAh">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Unidad de medida (opcional)
                        </p>
                        @error('unidad')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Opciones (solo para select, radio, checkbox) -->
                <div id="opciones_container" class="mt-6 options-input">
                    <label for="opciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Opciones Disponibles
                    </label>
                    <textarea id="opciones" name="opciones" rows="3"
                              class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm"
                              placeholder="Ingresa las opciones separadas por comas (ej: 2GB, 4GB, 8GB, 16GB)">{{ old('opciones') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Separa cada opción con una coma
                    </p>
                    @error('opciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Sección: Configuración Avanzada -->
            <div class="form-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Configuración Avanzada
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Requerido -->
                    <div class="flex items-center">
                        <input type="checkbox" id="requerido" name="requerido" value="1" {{ old('requerido') ? 'checked' : '' }}
                               class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                        <label for="requerido" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                            Campo requerido
                        </label>
                    </div>

                    <!-- Activo -->
                    <div class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" value="1" {{ old('activo', '1') ? 'checked' : '' }}
                               class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                        <label for="activo" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                            Especificación activa
                        </label>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mt-6">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="3"
                              class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm"
                              placeholder="Descripción opcional del campo">{{ old('descripcion') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Explica para qué sirve este campo
                    </p>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Vista Previa -->
            <div class="form-section">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Vista Previa del Campo
                </h3>
                
                <div id="field_preview" class="field-type-preview">
                    <p class="text-gray-500 dark:text-gray-400 text-center">
                        Selecciona un tipo de campo para ver la vista previa
                    </p>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.especificaciones.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                    Cancelar
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Crear Especificación
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoCampoSelect = document.getElementById('tipo_campo');
    const opcionesContainer = document.getElementById('opciones_container');
    const fieldPreview = document.getElementById('field_preview');
    
    // Mostrar/ocultar opciones según el tipo de campo
    function toggleOpciones() {
        const tipo = tipoCampoSelect.value;
        const tiposConOpciones = ['select', 'radio', 'checkbox'];
        
        if (tiposConOpciones.includes(tipo)) {
            opcionesContainer.classList.add('show');
        } else {
            opcionesContainer.classList.remove('show');
        }
        
        updatePreview();
    }
    
    // Actualizar vista previa
    function updatePreview() {
        const tipo = tipoCampoSelect.value;
        const etiqueta = document.getElementById('etiqueta').value || 'Etiqueta del Campo';
        const unidad = document.getElementById('unidad').value;
        const opciones = document.getElementById('opciones').value;
        const requerido = document.getElementById('requerido').checked;
        
        if (!tipo) {
            fieldPreview.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">Selecciona un tipo de campo para ver la vista previa</p>';
            return;
        }
        
        let previewHTML = '';
        const labelText = etiqueta + (requerido ? ' *' : '') + (unidad ? ` (${unidad})` : '');
        
        switch (tipo) {
            case 'text':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="text" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="Ingresa ${etiqueta.toLowerCase()}">
                `;
                break;
                
            case 'textarea':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <textarea rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="Ingresa ${etiqueta.toLowerCase()}"></textarea>
                `;
                break;
                
            case 'number':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="number" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="Ingresa un número">
                `;
                break;
                
            case 'select':
                if (opciones) {
                    const opcionesArray = opciones.split(',').map(opt => opt.trim());
                    let optionsHTML = '<option value="">Selecciona una opción</option>';
                    opcionesArray.forEach(opt => {
                        optionsHTML += `<option value="${opt}">${opt}</option>`;
                    });
                    
                    previewHTML = `
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                        <select class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                            ${optionsHTML}
                        </select>
                    `;
                } else {
                    previewHTML = `
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                        <select class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                            <option value="">Selecciona una opción</option>
                            <option value="opcion1">Opción 1</option>
                            <option value="opcion2">Opción 2</option>
                        </select>
                    `;
                }
                break;
                
            case 'checkbox':
                previewHTML = `
                    <div class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900 dark:text-gray-100">${labelText}</label>
                    </div>
                `;
                break;
                
            case 'radio':
                if (opciones) {
                    const opcionesArray = opciones.split(',').map(opt => opt.trim());
                    let radioHTML = '';
                    opcionesArray.forEach((opt, index) => {
                        radioHTML += `
                            <div class="flex items-center">
                                <input type="radio" name="preview_radio" value="${opt}" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300">
                                <label class="ml-2 block text-sm text-gray-900 dark:text-gray-100">${opt}</label>
                            </div>
                        `;
                    });
                    
                    previewHTML = `
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                        <div class="space-y-2">
                            ${radioHTML}
                        </div>
                    `;
                } else {
                    previewHTML = `
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" name="preview_radio" value="opcion1" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300">
                                <label class="ml-2 block text-sm text-gray-900 dark:text-gray-100">Opción 1</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="preview_radio" value="opcion2" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300">
                                <label class="ml-2 block text-sm text-gray-900 dark:text-gray-100">Opción 2</label>
                            </div>
                        </div>
                    `;
                }
                break;
                
            case 'date':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="date" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm">
                `;
                break;
                
            case 'email':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="email" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="ejemplo@correo.com">
                `;
                break;
                
            case 'url':
                previewHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${labelText}</label>
                    <input type="url" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="https://ejemplo.com">
                `;
                break;
        }
        
        fieldPreview.innerHTML = previewHTML;
    }
    
    // Event listeners
    tipoCampoSelect.addEventListener('change', toggleOpciones);
    document.getElementById('etiqueta').addEventListener('input', updatePreview);
    document.getElementById('unidad').addEventListener('input', updatePreview);
    document.getElementById('opciones').addEventListener('input', updatePreview);
    document.getElementById('requerido').addEventListener('change', updatePreview);
    
    // Inicializar
    toggleOpciones();
});
</script>
@endpush

@endsection
