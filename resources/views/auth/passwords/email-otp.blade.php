<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4GMovil - Restablecer Contraseña</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style-login.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="container mx-auto flex flex-col lg:flex-row items-center justify-center gap-16 min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 p-4">
        <!-- Phone Illustration -->
        <div class="phone-container">
            <div class="phone">
                <div class="notch"></div>
                <div class="phone-screen">
                    <div class="text-center mb-8">
                        <a href="{{ route('landing') }}">
                            <h2 class="text-2xl font-bold text-white">4GMovil</h2>
                        </a>
                        <p class="text-gray-400 mt-2">Restablecer Contraseña</p>
                    </div>
                    
                    <div class="w-full max-w-xs">
                        <div class="grid grid-cols-4 gap-2 mb-6">
                            <div class="app-icon bg-gradient-to-br from-blue-500 to-blue-600">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-purple-500 to-purple-600">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-red-500 to-red-600">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-green-500 to-green-600">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        
                        <div class="bg-blue-900 bg-opacity-30 rounded-lg p-4 text-center border border-blue-800">
                            <p class="text-blue-200 text-sm">Verificación segura</p>
                            <p class="text-white font-medium">Te enviaremos un código OTP</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Forms Container -->
        <div class="max-w-xl w-full toggle-forms">
            <!-- Forgot Password Form -->
            <div class="form-container max-w-xl h-auto bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700" id="forgot-password-form">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white">Restablecer Contraseña</h2>
                    <p class="text-gray-400 mt-2">Te ayudamos a recuperar el acceso a tu cuenta con OTP</p>
                </div>
                
                @if (session('status'))
                    <script>
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: '{{ session('status') }}',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            background: '#1f2937',
                            color: '#10b981',
                            iconColor: '#10b981',
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });
                    </script>
                @endif
                
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf
                    
                    <div class="relative">
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Correo electrónico</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" placeholder="Escribe tu correo electrónico registrado" 
                                   value="{{ old('email') }}"
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition">
                            <div class="input-highlight"></div>
                        </div>
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h3 class="text-white font-medium mb-2">Proceso de recuperación con OTP:</h3>
                        <ul class="text-gray-300 text-sm space-y-1">
                            <li class="flex items-center">
                                <i class="fas fa-1 text-blue-400 text-xs mr-2"></i>
                                <span>Ingresa tu correo electrónico registrado</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-2 text-blue-400 text-xs mr-2"></i>
                                <span>Recibirás un código OTP de 6 dígitos</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-3 text-blue-400 text-xs mr-2"></i>
                                <span>Ingresa el código y crea tu nueva contraseña</span>
                            </li>
                        </ul>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-blue-600 transition duration-300 shadow-lg hover:shadow-blue-500/30">
                        <i class="fas fa-paper-plane mr-2"></i> Enviar Código OTP
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Regresar al Inicio de Sesión
                    </a>
                </div>

                @if (session('email_sent'))
                    <div class="mt-6 p-4 bg-blue-900 bg-opacity-30 rounded-lg border border-blue-800">
                        <p class="text-blue-200 text-sm">
                            <strong>Código enviado a:</strong> {{ session('email_sent') }}
                        </p>
                        <p class="text-blue-300 text-xs mt-1">
                            Revisa tu bandeja de entrada y carpeta de spam.
                        </p>
                        <div class="mt-3">
                            <form method="POST" action="{{ route('password.reset.otp.post') }}" class="inline">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('email_sent') }}">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-300">
                                    <i class="fas fa-arrow-right mr-2"></i> Continuar con OTP
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
