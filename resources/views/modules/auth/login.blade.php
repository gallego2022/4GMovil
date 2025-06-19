@extends('layouts.main')

@section('titulo_pagina', 'Login de usuario')

@section('contenido')
<div class="container mx-auto flex flex-col lg:flex-row items-center justify-center gap-16">
    <!-- Phone Illustration -->
    <div class="phone-container">
        <div class="phone">
            <div class="notch"></div>
            <div class="phone-screen">
                <div class="text-center mb-8">

                    <a href="{{ route('landing') }}">
                        <h2 class="text-2xl font-bold text-white">4GMovil</h2>
                    </a>
                    <p class="text-gray-400 mt-2">Tu tienda de celulares</p>
                </div>

                <div class="w-full max-w-xs">
                    <div class="grid grid-cols-4 gap-2 mb-6">
                        <div class="app-icon bg-gradient-to-br from-blue-500 to-blue-600">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="app-icon bg-gradient-to-br from-purple-500 to-purple-600">
                            <i class="fas fa-mobile-screen"></i>
                        </div>
                        <div class="app-icon bg-gradient-to-br from-red-500 to-red-600">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <div class="app-icon bg-gradient-to-br from-green-500 to-green-600">
                            <i class="fas fa-tablet-screen-button"></i>
                        </div>
                    </div>

                    <div class="bg-blue-900 bg-opacity-30 rounded-lg p-4 text-center border border-blue-800">
                        <p class="text-blue-200 text-sm">Ofertas exclusivas</p>
                        <p class="text-white font-medium">Hasta 40% de descuento</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @if (session('error_login'))
    <div class="text-red-400 text-center mb-4">
        {{ session('error_login') }}
    </div>
    @endif

    <!-- Forms Container -->
    <div class="w-full max-w-md toggle-forms">
        <!-- Login Form -->
        <div class="form-container bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-700" id="login-form">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white">Iniciar Sesión</h2>
                <p class="text-gray-400 mt-2">Accede a tu cuenta 4GMovil</p>
            </div>

            <form action="{{ route('logear') }}" method="post" class="space-y-6" id="login-form" novalidate>
                @csrf

                <div class="relative">
                    <input type="email" name="correo_electronico" id="correo_electronico_login"
                        value="{{ old('correo_electronico') }}" placeholder="Correo electrónico"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition">
                    <div class="input-highlight"></div>
                    @error('correo_electronico')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="relative">
                    <input type="password" placeholder="Contraseña" name="contrasena" id="contrasena_login"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition">
                    <div class="input-highlight"></div>
                    @error('contrasena')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox"
                            class="form-checkbox h-4 w-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500">
                        <span class="ml-2 text-gray-300 text-sm">Recordarme</span>
                    </label>

                    <a href="#" class="text-sm text-blue-400 hover:text-blue-300">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-blue-600 transition duration-300 shadow-lg hover:shadow-blue-500/30">
                    <i class="fas fa-sign-in-alt mr-2"></i> Ingresar
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-400">¿No tienes una cuenta?
                    <button onclick="toggleForms()"
                        class="text-blue-400 hover:text-blue-300 font-medium">Regístrate</button>
                </p>
            </div>

            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-800 text-gray-400">O ingresa con</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <button
                        class="flex items-center justify-center py-2 px-4 bg-gray-700 rounded-lg text-gray-300 hover:bg-gray-600 transition">
                        <i class="fab fa-google text-red-400 mr-2"></i> Google
                    </button>
                    <button
                        class="flex items-center justify-center py-2 px-4 bg-gray-700 rounded-lg text-gray-300 hover:bg-gray-600 transition">
                        <i class="fab fa-apple text-gray-300 mr-2"></i> Apple
                    </button>
                    <button
                        class="flex items-center justify-center py-2 px-4 bg-gray-700 rounded-lg text-gray-300 hover:bg-gray-600 transition">
                        <i class="fab fa-facebook-f text-blue-400 mr-2"></i> FB
                    </button>
                </div>
            </div>
        </div>

        <!-- Formulario de Registro -->
        <div class="form-container bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-700 absolute inset-0 flipped opacity-0"
            id="register-form">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white">Regístrate</h2>
                <p class="text-gray-400 mt-2">Crea tu cuenta 4GMovil</p>
            </div>

            <form class="space-y-6" action="{{ route('registrar') }}" method="post"  id="register-form" novalidate>
                @csrf

                {{-- Nombre --}}
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Nombre"
                        name="nombre_usuario"
                        id="nombre_usuario"
                        value="{{ old('nombre_usuario') }}"
                        class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition @error('nombre_usuario') border-red-500 ring-2 ring-red-400 focus:border-red-600 @enderror">
                    @error('nombre_usuario')
                    <span class="absolute right-3 top-3 text-red-400">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                    <small class="text-red-400 text-sm mt-1 block">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Correo Electrónico --}}
                <div class="relative">
                    <input
                        type="email"
                        placeholder="Correo electrónico"
                        name="correo_electronico"
                        id="correo_electronico_register"
                        value="{{ old('correo_electronico') }}"
                        class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition @error('correo_electronico') border-red-500 ring-2 ring-red-400 focus:border-red-600 @enderror">
                    @error('correo_electronico')
                    <span class="absolute right-3 top-3 text-red-400">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                    <small class="text-red-400 text-sm mt-1 block">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div class="relative">
                    <input
                        type="tel"
                        placeholder="Teléfono"
                        name="telefono"
                        id="telefono"
                        value="{{ old('telefono') }}"
                        class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400  focus:outline-none focus:border-blue-500 transition @error('telefono') border-red-500 ring-2 ring-red-400 focus:border-red-600 @enderror">
                    @error('telefono')
                    <span class="absolute right-3 top-3 text-red-400">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                    <small class="text-red-400 text-sm mt-1 block">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div class="relative">
                    <input
                        type="password"
                        placeholder="Contraseña"
                        name="contrasena"
                        id="contrasena_register"
                        class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition @error('contrasena') border-red-500 ring-2 ring-red-400 focus:border-red-600 @enderror">
                    @error('contrasena')
                    <span class="absolute right-3 top-3 text-red-400">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                    <small class="text-red-400 text-sm mt-1 block">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Confirmar Contraseña --}}
                <div class="relative">
                    <input
                        type="password"
                        placeholder="Confirmar contraseña"
                        name="contrasena_confirmation"
                        id="contrasena_confirmation"
                        class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition @error('contrasena_confirmation') border-red-500 ring-2 ring-red-400 focus:border-red-600 @enderror">
                    @error('contrasena_confirmation')
                    <span class="absolute right-3 top-3 text-red-400">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                    <small class="text-red-400 text-sm mt-1 block">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Checkbox Términos --}}
                <label class="flex items-start space-x-2">
                    <input
                        type="checkbox"
                        name="acepta_terminos"
                        class="form-checkbox h-4 w-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 mt-1 @error('acepta_terminos') border-red-500 ring-2 ring-red-400 focus:border-red-600 @enderror"
                        {{ old('acepta_terminos') ? 'checked' : '' }}
                        required>
                    <span class="text-gray-300 text-sm">
                        Acepto los
                        <a href="#" class="text-blue-400 hover:text-blue-300">Términos</a> y la
                        <a href="#" class="text-blue-400 hover:text-blue-300">Política de Privacidad</a>
                    </span>
                </label>
                @error('acepta_terminos')
                <small class="text-red-400 text-sm mt-1 block">{{ $message }}</small>
                @enderror

                {{-- Botón enviar --}}
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white py-3 px-4 rounded-lg font-medium
            hover:from-blue-700 hover:to-blue-600 transition duration-300 shadow-lg hover:shadow-blue-500/30 flex justify-center items-center">
                    <i class="fas fa-user-plus mr-2"></i> Crear cuenta
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-400">¿Ya tienes una cuenta?
                    <button onclick="toggleForms()" class="text-blue-400 hover:text-blue-300 font-medium">Inicia
                        sesión</button>
                </p>
            </div>


        </div>
    </div>
</div>
<!-- Alerta confrimacion registro -->
@if (session('registro_exitoso'))
<script>
    Swal.fire({
        title: '{{ __("messages.registration_success") }}',
        icon: 'success',
        timer: 2300,
        showConfirmButton: false
    });
</script>
@endif

<!-- Validacion de campos login-form -->
 <script>
// Agregar los mensajes de validación al inicio
const validationMessages = {
    required_field: '{{ __("forms.required_field") }}',
    invalid_format: '{{ __("forms.invalid_format") }}',
    fix_errors: '{{ __("forms.fix_errors") }}',
    validation_error: '{{ __("forms.validation_error") }}',
    name: {
        required: '{{ __("forms.name.required") }}',
        format: '{{ __("forms.name.format") }}'
    },
    email: {
        required: '{{ __("forms.email.required") }}',
        format: '{{ __("forms.email.format") }}'
    },
    phone: {
        required: '{{ __("forms.phone.required") }}',
        format: '{{ __("forms.phone.format") }}'
    },
    password: {
        required: '{{ __("forms.password.required") }}',
        min_length: '{{ __("forms.password.min_length") }}',
        uppercase: '{{ __("forms.password.uppercase") }}',
        lowercase: '{{ __("forms.password.lowercase") }}',
        number: '{{ __("forms.password.number") }}',
        symbol: '{{ __("forms.password.symbol") }}',
        mismatch: '{{ __("forms.password.mismatch") }}'
    },
    terms: {
        required: '{{ __("forms.terms.required") }}'
    },
    auth: {
        login_success: '{{ __("messages.login_success") }}',
        login_error: '{{ __("messages.error") }}'
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const correoInput = document.getElementById('correo_electronico_login');
    const passInput = document.getElementById('contrasena_login');

    function showToast(message, icon = 'error') {
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: icon,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    function createErrorElement() {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
        return errorDiv;
    }

    function showInputError(input, message) {
        // Remover mensaje de error anterior si existe
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        // Crear y mostrar nuevo mensaje de error
        if (message) {
            const errorDiv = createErrorElement();
            errorDiv.textContent = message;
            input.parentNode.appendChild(errorDiv);
            input.classList.add('border-red-500');
            input.classList.remove('border-green-500');
        } else {
            input.classList.remove('border-red-500');
            input.classList.add('border-green-500');
        }
    }

    function validateInput(input, validations) {
        const value = input.value.trim();
        
        for (const validation of validations) {
            if (!validation.isValid(value)) {
                showInputError(input, validation.message);
                return false;
            }
        }
        
        showInputError(input, ''); // Limpiar error si es válido
        return true;
    }

    const emailValidations = [
        {
            isValid: value => value !== '',
            message: validationMessages.email.required
        },
        {
            isValid: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            message: validationMessages.email.format
        }
    ];

    const passwordValidations = [
        {
            isValid: value => value !== '',
            message: validationMessages.password.required
        },
        {
            isValid: value => value.length >= 8,
            message: validationMessages.password.min_length
        }
    ];

    correoInput.addEventListener('input', () => {
        validateInput(correoInput, emailValidations);
    });

    passInput.addEventListener('input', () => {
        validateInput(passInput, passwordValidations);
    });

    loginForm.addEventListener('submit', (e) => {
        const isEmailValid = validateInput(correoInput, emailValidations);
        const isPasswordValid = validateInput(passInput, passwordValidations);

        if (!isEmailValid || !isPasswordValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: validationMessages.validation_error,
                text: validationMessages.fix_errors,
                confirmButtonText: '{{ __("messages.confirm") }}'
            });
        }
    });
});
</script>

<!-- Validacion de campos form-register-->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('register-form');
    const inputs = {
        nombre: document.getElementById('nombre_usuario'),
        correo: document.getElementById('correo_electronico_register'),
        telefono: document.getElementById('telefono'),
        contrasena: document.getElementById('contrasena_register'),
        confirmacion: document.getElementById('contrasena_confirmation'),
        terminos: document.querySelector('input[name="acepta_terminos"]')
    };

    const validations = {
        nombre: [
            {
                isValid: value => value.trim() !== '',
                message: validationMessages.name.required
            },
            {
                isValid: value => /^[a-zA-ZÀ-ÿ\s]{2,25}$/.test(value),
                message: validationMessages.name.format
            }
        ],
        correo: [
            {
                isValid: value => value.trim() !== '',
                message: validationMessages.email.required
            },
            {
                isValid: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
                message: validationMessages.email.format
            }
        ],
        telefono: [
            {
                isValid: value => value.trim() !== '',
                message: validationMessages.phone.required
            },
            {
                isValid: value => /^\d{10}$/.test(value),
                message: validationMessages.phone.format
            }
        ],
        contrasena: [
            {
                isValid: value => value.trim() !== '',
                message: validationMessages.password.required
            },
            {
                isValid: value => value.length >= 8,
                message: validationMessages.password.min_length
            },
            {
                isValid: value => /[A-Z]/.test(value),
                message: validationMessages.password.uppercase
            },
            {
                isValid: value => /[a-z]/.test(value),
                message: validationMessages.password.lowercase
            },
            {
                isValid: value => /\d/.test(value),
                message: validationMessages.password.number
            },
            {
                isValid: value => /[\W_]/.test(value),
                message: validationMessages.password.symbol
            }
        ]
    };

    function showToast(message, icon = 'error') {
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: icon,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    function createErrorElement() {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
        return errorDiv;
    }

    function showInputError(input, message) {
        // Remover mensaje de error anterior si existe
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        // Crear y mostrar nuevo mensaje de error
        if (message) {
            const errorDiv = createErrorElement();
            errorDiv.textContent = message;
            input.parentNode.appendChild(errorDiv);
            input.classList.add('border-red-500');
            input.classList.remove('border-green-500');
        } else {
            input.classList.remove('border-red-500');
            input.classList.add('border-green-500');
        }
    }

    function validateInput(input, validationRules) {
        const value = input.value.trim();
        
        for (const validation of validationRules) {
            if (!validation.isValid(value)) {
                showInputError(input, validation.message);
                return false;
            }
        }
        
        showInputError(input, ''); // Limpiar error si es válido
        return true;
    }

    // Validación en tiempo real para cada input
    Object.entries(inputs).forEach(([key, input]) => {
        if (key !== 'confirmacion' && key !== 'terminos') {
            input.addEventListener('input', () => {
                validateInput(input, validations[key]);
            });

            // También validar cuando el campo pierde el foco
            input.addEventListener('blur', () => {
                validateInput(input, validations[key]);
            });
        }
    });

    // Validación especial para confirmación de contraseña
    inputs.confirmacion.addEventListener('input', () => {
        const isValid = inputs.confirmacion.value === inputs.contrasena.value;
        showInputError(
            inputs.confirmacion, 
            isValid ? '' : validationMessages.password.mismatch
        );
    });

    // También validar confirmación cuando pierde el foco
    inputs.confirmacion.addEventListener('blur', () => {
        const isValid = inputs.confirmacion.value === inputs.contrasena.value;
        showInputError(
            inputs.confirmacion, 
            isValid ? '' : validationMessages.password.mismatch
        );
    });

    // Validación de términos y condiciones
    inputs.terminos.addEventListener('change', () => {
        showInputError(
            inputs.terminos,
            inputs.terminos.checked ? '' : validationMessages.terms.required
        );
    });

    // Validación al enviar el formulario
    form.addEventListener('submit', (e) => {
        let isValid = true;

        // Validar todos los campos
        Object.entries(inputs).forEach(([key, input]) => {
            if (key !== 'confirmacion' && key !== 'terminos') {
                if (!validateInput(input, validations[key])) {
                    isValid = false;
                }
            }
        });

        // Validar confirmación de contraseña
        if (inputs.confirmacion.value !== inputs.contrasena.value) {
            isValid = false;
            showInputError(inputs.confirmacion, validationMessages.password.mismatch);
        }

        // Validar términos y condiciones
        if (!inputs.terminos.checked) {
            isValid = false;
            showInputError(inputs.terminos, validationMessages.terms.required);
        }

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: validationMessages.validation_error,
                text: validationMessages.fix_errors,
                confirmButtonText: '{{ __("messages.confirm") }}'
            });
        }
    });

    // Formateo automático del teléfono
    inputs.telefono.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) value = value.slice(0, 10);
        e.target.value = value;
    });
});
</script>



@endsection