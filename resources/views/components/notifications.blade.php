@if(session('mensaje'))
<div class="fixed top-4 right-4 z-50 max-w-sm w-full" 
     x-data="{ 
         show: true, 
         autoClose: true,
         timeLeft: 5,
         startTimer() {
             this.timer = setInterval(() => {
                 if (this.autoClose) {
                     this.timeLeft--;
                     if (this.timeLeft <= 0) {
                         this.show = false;
                         clearInterval(this.timer);
                     }
                 }
             }, 1000);
         },
         pauseTimer() {
             this.autoClose = false;
         },
         resumeTimer() {
             this.autoClose = true;
         }
     }" 
     x-show="show" 
     x-init="startTimer()"
     @mouseenter="pauseTimer()"
     @mouseleave="resumeTimer()"
     x-transition:enter="transform ease-out duration-300 transition" 
     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" 
     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" 
     x-transition:leave="transition ease-in duration-100" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0">
    <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden {{ session('tipo', 'success') === 'success' ? 'bg-green-50 dark:bg-green-900/20' : (session('tipo') === 'error' ? 'bg-red-50 dark:bg-red-900/20' : (session('tipo') === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-blue-50 dark:bg-blue-900/20')) }}">
        <!-- Barra de progreso para auto-cierre -->
        <div class="h-1 bg-gray-200 dark:bg-gray-700">
            <div class="h-full {{ session('tipo', 'success') === 'success' ? 'bg-green-500' : (session('tipo') === 'error' ? 'bg-red-500' : (session('tipo') === 'warning' ? 'bg-yellow-500' : 'bg-blue-500')) }} transition-all duration-1000 ease-linear" 
                 :style="`width: ${(timeLeft / 5) * 100}%`"></div>
        </div>
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    @if(session('tipo', 'success') === 'success')
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif(session('tipo') === 'error')
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif(session('tipo') === 'warning')
                        <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    @else
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium {{ session('tipo', 'success') === 'success' ? 'text-green-800 dark:text-green-200' : (session('tipo') === 'error' ? 'text-red-800 dark:text-red-200' : (session('tipo') === 'warning' ? 'text-yellow-800 dark:text-yellow-200' : 'text-blue-800 dark:text-blue-200')) }}">
                        {{ session('mensaje') }}
                    </p>
                    <!-- Indicador de tiempo restante -->
                    <div class="mt-1 text-xs {{ session('tipo', 'success') === 'success' ? 'text-green-600 dark:text-green-400' : (session('tipo') === 'error' ? 'text-red-600 dark:text-red-400' : (session('tipo') === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400')) }}">
                        <span x-show="autoClose">Se cierra en <span x-text="timeLeft"></span>s</span>
                        <span x-show="!autoClose" class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Pausado
                        </span>
                    </div>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="show = false" class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
