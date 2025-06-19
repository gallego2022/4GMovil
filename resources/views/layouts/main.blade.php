<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo_pagina')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style-login.css') }}">
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen flex items-center justify-center p-4">
    @yield('contenido')


     <script>
        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');

            loginForm.classList.toggle('flipped');
            loginForm.classList.toggle('opacity-0');
            registerForm.classList.toggle('flipped');
            registerForm.classList.toggle('opacity-0');

            setTimeout(() => {
                if (loginForm.classList.contains('flipped')) {
                    loginForm.style.zIndex = '0';
                    registerForm.style.zIndex = '10';
                } else {
                    loginForm.style.zIndex = '10';
                    registerForm.style.zIndex = '0';
                }
            }, 250);
        }
    </script>
</body>
</html>
