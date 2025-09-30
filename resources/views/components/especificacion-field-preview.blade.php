@php
    $labelText = $especificacion->etiqueta;
    if ($especificacion->requerido) {
        $labelText .= ' *';
    }
    if ($especificacion->unidad) {
        $labelText .= " ({$especificacion->unidad})";
    }
@endphp

@switch($especificacion->tipo_campo)
    @case('texto')
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $labelText }}</label>
        <input type="text" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="Ingresa {{ strtolower($especificacion->etiqueta) }}" disabled>
        @break
        
    @case('numero')
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $labelText }}</label>
        <input type="number" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="Ingresa un número" disabled>
        @break
        
    @case('select')
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $labelText }}</label>
        <select class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" disabled>
            <option value="">Selecciona una opción</option>
            @if($especificacion->opciones)
                @foreach(explode(',', $especificacion->opciones) as $opcion)
                    <option value="{{ trim($opcion) }}">{{ trim($opcion) }}</option>
                @endforeach
            @else
                <option value="opcion1">Opción 1</option>
                <option value="opcion2">Opción 2</option>
            @endif
        </select>
        @break
        
    @case('checkbox')
        <div class="flex items-center">
            <input type="checkbox" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded" disabled>
            <label class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ $labelText }}</label>
        </div>
        @break
        
    @case('radio')
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $labelText }}</label>
        <div class="space-y-2">
            @if($especificacion->opciones)
                @foreach(explode(',', $especificacion->opciones) as $opcion)
                    <div class="flex items-center">
                        <input type="radio" name="preview_radio" value="{{ trim($opcion) }}" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300" disabled>
                        <label class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ trim($opcion) }}</label>
                    </div>
                @endforeach
            @else
                <div class="flex items-center">
                    <input type="radio" name="preview_radio" value="opcion1" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300" disabled>
                    <label class="ml-2 block text-sm text-gray-900 dark:text-gray-100">Opción 1</label>
                </div>
                <div class="flex items-center">
                    <input type="radio" name="preview_radio" value="opcion2" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300" disabled>
                    <label class="ml-2 block text-sm text-gray-900 dark:text-gray-100">Opción 2</label>
                </div>
            @endif
        </div>
        @break
        
    @case('date')
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $labelText }}</label>
        <input type="date" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" disabled>
        @break
        
    @case('email')
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $labelText }}</label>
        <input type="email" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="ejemplo@correo.com" disabled>
        @break
        
    @case('url')
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $labelText }}</label>
        <input type="url" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="https://ejemplo.com" disabled>
        @break
        
    @default
        <p class="text-gray-500 dark:text-gray-400 text-center">Tipo de campo no reconocido</p>
@endswitch

<div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
    <p class="text-sm text-blue-800 dark:text-blue-200">
        <strong>Nota:</strong> Esta es una vista previa del campo. Los campos están deshabilitados para demostración.
    </p>
</div>
