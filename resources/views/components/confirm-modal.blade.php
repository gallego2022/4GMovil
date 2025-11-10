@props([
    'id' => 'confirmModal',
    'title' => '¿Confirmar acción?',
    'message' => '¿Estás seguro de realizar esta acción?',
    'confirmText' => 'Sí, confirmar',
    'cancelText' => 'Cancelar',
    'confirmColor' => 'red',
    'showWarning' => true,
])

<!-- Modal de confirmación -->
<div id="{{ $id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-{{ $confirmColor }}-100 dark:bg-{{ $confirmColor }}-900/30">
                <svg class="h-6 w-6 text-{{ $confirmColor }}-600 dark:text-{{ $confirmColor }}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-5">
                {{ $title }}
            </h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-300" id="{{ $id }}_message">
                    {{ $message }}
                </p>
                @if($showWarning)
                    <p class="text-sm text-{{ $confirmColor }}-600 dark:text-{{ $confirmColor }}-400 mt-2 font-medium">
                        Esta acción no se puede deshacer.
                    </p>
                @endif
            </div>
            <div class="items-center px-4 py-3">
                <form id="{{ $id }}_form" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-{{ $confirmColor }}-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-{{ $confirmColor }}-700 focus:outline-none focus:ring-2 focus:ring-{{ $confirmColor }}-500 transition-colors duration-200">
                        {{ $confirmText }}
                    </button>
                </form>
                <button id="{{ $id }}_cancel" 
                        class="mt-2 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                    {{ $cancelText }}
                </button>
            </div>
        </div>
    </div>
</div>

