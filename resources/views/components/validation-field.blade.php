@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'rules' => [],
    'messages' => [],
    'options' => [],
    'rows' => 3,
    'min' => null,
    'max' => null,
    'step' => null,
    'pattern' => null,
    'autocomplete' => null,
    'readonly' => false,
    'disabled' => false,
    'class' => '',
    'wrapperClass' => '',
    'helpText' => '',
    'icon' => null,
    'iconPosition' => 'left'
])

@php
    $fieldId = $name ?: 'field_' . uniqid();
    $isRequired = $required || in_array('required', $rules);
    $errorClass = $errors->has($name) ? 'border-red-300 dark:border-red-600 text-red-900 dark:text-red-200 placeholder-red-300 dark:placeholder-red-500 focus:border-red-500 focus:ring-red-500' : '';
    $baseClass = 'block w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-700 dark:text-gray-100 sm:text-sm transition-colors duration-200';
    $finalClass = $baseClass . ' ' . $errorClass . ' ' . $class;
    
    // Configurar atributos HTML5
    $htmlAttributes = [];
    if ($isRequired) $htmlAttributes['required'] = 'required';
    if ($min !== null) $htmlAttributes['min'] = $min;
    if ($max !== null) $htmlAttributes['max'] = $max;
    if ($step !== null) $htmlAttributes['step'] = $step;
    if ($pattern) $htmlAttributes['pattern'] = $pattern;
    if ($autocomplete) $htmlAttributes['autocomplete'] = $autocomplete;
    if ($readonly) $htmlAttributes['readonly'] = 'readonly';
    if ($disabled) $htmlAttributes['disabled'] = 'disabled';
    
    // Configurar validaciones JavaScript
    $jsRules = json_encode($rules);
    $jsMessages = json_encode($messages);
@endphp

<div class="validation-field {{ $wrapperClass }}" 
     data-field-name="{{ $name }}"
     data-rules="{{ $jsRules }}"
     data-messages="{{ $jsMessages }}">
    
    @if($label)
        <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if($isRequired)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $icon !!}
                </svg>
            </div>
        @endif

        @if($type === 'textarea')
            @php
                $textareaValue = old($name, $value);
                // Asegurar que el valor sea una cadena
                $textareaValue = $textareaValue !== null ? (string)$textareaValue : '';
            @endphp
            <textarea 
                name="{{ $name }}" 
                id="{{ $fieldId }}"
                rows="{{ $rows }}"
                class="{{ $finalClass }} {{ $icon && $iconPosition === 'left' ? 'pl-10' : '' }}"
                placeholder="{{ $placeholder }}"
                @foreach($htmlAttributes as $attr => $value) {{ $attr }}="{{ $value }}" @endforeach
            >{{ $textareaValue }}</textarea>
        @elseif($type === 'select')
            @php
                $selectedValue = old($name, $value);
                // Convertir a string para comparación estricta
                $selectedValue = $selectedValue !== null && $selectedValue !== '' ? (string)$selectedValue : '';
            @endphp
            <select 
                name="{{ $name }}" 
                id="{{ $fieldId }}"
                class="{{ $finalClass }} {{ $icon && $iconPosition === 'left' ? 'pl-10' : '' }}"
                @foreach($htmlAttributes as $attr => $value) {{ $attr }}="{{ $value }}" @endforeach
            >
                <option value="">{{ $placeholder ?: 'Selecciona una opción' }}</option>
                @foreach($options as $optionValue => $optionLabel)
                    @php
                        $optionValueStr = (string)$optionValue;
                    @endphp
                    <option value="{{ $optionValue }}" {{ $selectedValue === $optionValueStr ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        @else
            <input 
                type="{{ $type }}" 
                name="{{ $name }}" 
                id="{{ $fieldId }}"
                value="{{ old($name, $value) }}"
                class="{{ $finalClass }} {{ $icon && $iconPosition === 'left' ? 'pl-10' : '' }}"
                placeholder="{{ $placeholder }}"
                @foreach($htmlAttributes as $attr => $value) {{ $attr }}="{{ $value }}" @endforeach
            />
        @endif

        @if($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $icon !!}
                </svg>
            </div>
        @endif
    </div>

    @if($helpText)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $helpText }}</p>
    @endif

    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror

    <!-- Mensaje de error dinámico (se llena con JavaScript) -->
    <p class="field-error mt-1 text-sm text-red-600 dark:text-red-400" style="display: none;"></p>
</div>

@push('scripts')
<script>
(function() {
    // Usar IIFE para crear un scope único y evitar conflictos de variables
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar validación para este campo
        const field = document.querySelector('[data-field-name="{{ $name }}"]');
        if (field && window.ValidationSystem) {
            const rules = {!! $jsRules !!};
            const fieldMessages = {!! $jsMessages !!};
            
            if (rules.length > 0) {
                window.ValidationSystem.addRule('{{ $name }}', rules);
                if (Object.keys(fieldMessages).length > 0) {
                    window.ValidationSystem.addMessage('{{ $name }}', fieldMessages);
                }
            }
            
            // Marcar campo como requerido si tiene la regla 'required'
            if (rules.includes('required')) {
                field.setAttribute('data-required', 'true');
            }
            
            // Configurar validación en tiempo real
            const isRequired = rules.includes('required');
            if (isRequired) {
                const events = ['input', 'blur', 'change'];
                events.forEach(event => {
                    field.addEventListener(event, (e) => {
                        window.ValidationSystem.validateField(field, rules, fieldMessages);
                    });
                });
                
                // Validación inicial si el campo tiene valor
                if (field.value) {
                    window.ValidationSystem.validateField(field, rules, fieldMessages);
                }
            } else {
                // Para campos opcionales, solo validar al salir del campo (blur)
                field.addEventListener('blur', (e) => {
                    window.ValidationSystem.validateField(field, rules, fieldMessages);
                });
            }
            
            // Agregar restricciones de entrada basadas en el tipo y reglas
            addInputRestrictions(field, '{{ $type }}', rules);
        }
    });
})();

// Función para agregar restricciones de entrada
function addInputRestrictions(field, type, rules) {
    // Restricciones para campos de teléfono
    if (type === 'tel' || rules.includes('phone')) {
        field.addEventListener('input', function(e) {
            // Solo permitir números, +, -, espacios y paréntesis
            e.target.value = e.target.value.replace(/[^0-9+\-\(\)\s]/g, '');
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[^0-9+\-\(\)\s]/g, '');
            e.target.value = cleanPaste;
        });
    }
    
    // Restricciones para campos numéricos
    if (type === 'number' || rules.includes('decimal') || rules.includes('integer')) {
        field.addEventListener('input', function(e) {
            if (rules.includes('integer')) {
                // Solo números enteros
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            } else {
                // Números decimales
                e.target.value = e.target.value.replace(/[^0-9.]/g, '');
                // Evitar múltiples puntos decimales
                const parts = e.target.value.split('.');
                if (parts.length > 2) {
                    e.target.value = parts[0] + '.' + parts.slice(1).join('');
                }
                // Limitar a 2 decimales para precios
                if (field.name === 'precio' && parts.length === 2 && parts[1].length > 2) {
                    e.target.value = parts[0] + '.' + parts[1].substring(0, 2);
                }
            }
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            let cleanPaste = paste.replace(/[^0-9.]/g, '');
            if (rules.includes('integer')) {
                cleanPaste = paste.replace(/[^0-9]/g, '');
            }
            e.target.value = cleanPaste;
        });
    }
    
    // Restricciones para campos de texto sin números
    if (rules.includes('noNumbers')) {
        field.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[0-9]/g, '');
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[0-9]/g, '');
            e.target.value = cleanPaste;
        });
    }
    
    // Restricciones para campos sin caracteres especiales
    if (rules.includes('noSpecialChars')) {
        field.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g, '');
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g, '');
            e.target.value = cleanPaste;
        });
    }
    
    // Restricciones para nombres de campos (snake_case)
    if (rules.includes('fieldNameFormat')) {
        field.addEventListener('input', function(e) {
            // Solo letras minúsculas, números y guiones bajos
            e.target.value = e.target.value.replace(/[^a-z0-9_]/g, '');
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[^a-z0-9_]/g, '');
            e.target.value = cleanPaste;
        });
    }
    
    // Restricciones para campos de email
    if (type === 'email' || rules.includes('email')) {
        field.addEventListener('input', function(e) {
            // Permitir letras, números, @, ., -, _
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9@._-]/g, '');
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[^a-zA-Z0-9@._-]/g, '');
            e.target.value = cleanPaste;
        });
    }
    
    // Restricciones para campos de SKU
    if (field.name === 'sku' || rules.includes('uniqueSku')) {
        field.addEventListener('input', function(e) {
            // Solo letras mayúsculas, números y guiones
            e.target.value = e.target.value.replace(/[^A-Z0-9-]/g, '').toUpperCase();
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[^A-Z0-9-]/g, '').toUpperCase();
            e.target.value = cleanPaste;
        });
    }
    
    // Restricciones para campos de código de barras
    if (field.name === 'codigo_barras' || rules.includes('integer')) {
        field.addEventListener('input', function(e) {
            // Solo números
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[^0-9]/g, '');
            e.target.value = cleanPaste;
        });
    }
    
    // Restricciones para campos de dimensiones
    if (field.name === 'dimensiones') {
        field.addEventListener('input', function(e) {
            // Solo números, x, espacios y cm
            e.target.value = e.target.value.replace(/[^0-9x\s]/gi, '');
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[^0-9x\s]/gi, '');
            e.target.value = cleanPaste;
        });
    }
    
    // Restricciones para campos de peso
    if (field.name === 'peso') {
        field.addEventListener('input', function(e) {
            // Solo números y un punto decimal
            e.target.value = e.target.value.replace(/[^0-9.]/g, '');
            // Evitar múltiples puntos decimales
            const parts = e.target.value.split('.');
            if (parts.length > 2) {
                e.target.value = parts[0] + '.' + parts[1];
            }
            // Limitar a 2 decimales
            if (parts.length === 2 && parts[1].length > 2) {
                e.target.value = parts[0] + '.' + parts[1].substring(0, 2);
            }
        });
        
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[^0-9.]/g, '');
            e.target.value = cleanPaste;
        });
    }
}
</script>
@endpush
