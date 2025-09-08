<!DOCTYPE html>
<html lang="es" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de Administración - 4GMovil')</title>
    <meta name="description" content="@yield('meta-description', 'Panel de administración de 4GMovil')">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Critical Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- DataTables CSS - Load only when needed -->
    @stack('datatables-css')

    <!-- Custom Styles -->
    @stack('styles')

    
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Estilos para modo oscuro */
        .dark body {
            background-color: #0f172a;
        }

        .dark .bg-white {
            background-color: #1e293b;
        }

        .dark .text-gray-700 {
            color: #e2e8f0;
        }

        .dark .text-gray-900 {
            color: #ffffff;
        }

        .dark .text-gray-500 {
            color: #94a3b8;
        }

        .dark .bg-gray-50 {
            background-color: #334155;
        }

        .dark .bg-gray-100 {
            background-color: #0f172a;
        }

        .dark .ring-gray-300 {
            --tw-ring-color: #475569;
        }

        .dark .hover\:bg-gray-50:hover {
            background-color: #334155;
        }

        .dark .divide-gray-200>*+* {
            border-color: #475569;
        }

        .dark .shadow-sm {
            box-shadow: none;
        }

        /* Estilos para modo claro */
        body {
            background-color: #f8fafc;
        }

        .bg-white {
            background-color: #ffffff;
        }

        .text-gray-700 {
            color: #374151;
        }

        .text-gray-900 {
            color: #111827;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .bg-gray-100 {
            background-color: #f3f4f6;
        }

        /* Texto oscuro para modo claro */
        .text-dark {
            color: #111827;
        }

        .text-dark-secondary {
            color: #374151;
        }

        .text-dark-muted {
            color: #6b7280;
        }

        /* Performance optimizations */
        .lazy-load {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .lazy-load.loaded {
            opacity: 1;
        }

        /* Fix for profile image aspect ratio */
        .profile-image-container {
            aspect-ratio: 1;
            overflow: hidden;
        }

        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Layout tecnológico - Adaptativo */
        .main-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', system-ui, sans-serif;
            transition: all 0.3s ease;
        }

        .main-container.dark {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        .content-wrapper {
            flex: 1;
            display: flex;
        }

        .sidebar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 40;
            width: 320px;
            background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 50%, #e2e8f0 100%);
            border-right: 1px solid rgba(0, 255, 255, 0.2);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar-wrapper.dark {
            background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 50%, #2a2a2a 100%);
            border-right: 1px solid rgba(0, 255, 255, 0.3);
        }

        .main-content {
            flex: 1;
            margin-left: 320px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            transition: all 0.3s ease;
        }

        .main-content.dark {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        .header-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .header-wrapper.dark {
            background: rgba(10, 10, 10, 0.95);
            border-bottom: 1px solid rgba(0, 255, 255, 0.3);
        }

        /* Sidebar Navigation Styles - Tecnológico Adaptativo */
        .sidebar-nav {
            padding: 2rem 1.5rem;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            margin: 0.375rem 0;
            border-radius: 0.875rem;
            color: rgba(0, 255, 255, 0.8);
            font-weight: 500;
            font-size: 0.95rem;
            font-family: 'Inter', system-ui, sans-serif;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }

        .sidebar-nav-item:hover {
            background: rgba(0, 255, 255, 0.1);
            color: #00ffff;
            transform: translateX(4px);
            border-color: rgba(0, 255, 255, 0.3);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.2);
        }

        .sidebar-nav-item.active {
            background: rgba(0, 255, 255, 0.15);
            color: #00ffff;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
            border-color: rgba(0, 255, 255, 0.5);
        }

        .sidebar-nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #00ffff;
            border-radius: 0 2px 2px 0;
            box-shadow: 0 0 10px #00ffff;
        }

        /* Modo claro para sidebar */
        .sidebar-wrapper:not(.dark) .sidebar-nav-item {
            color: rgba(0, 100, 200, 0.8);
        }

        .sidebar-wrapper:not(.dark) .sidebar-nav-item:hover {
            background: rgba(0, 100, 200, 0.1);
            color: #0064c8;
            border-color: rgba(0, 100, 200, 0.3);
            box-shadow: 0 0 15px rgba(0, 100, 200, 0.2);
        }

        .sidebar-wrapper:not(.dark) .sidebar-nav-item.active {
            background: rgba(0, 100, 200, 0.15);
            color: #0064c8;
            border-color: rgba(0, 100, 200, 0.5);
            box-shadow: 0 0 20px rgba(0, 100, 200, 0.3);
        }

        .sidebar-wrapper:not(.dark) .sidebar-nav-item.active::before {
            background: #0064c8;
            box-shadow: 0 0 10px #0064c8;
        }

        /* Modo oscuro para sidebar */
        .sidebar-wrapper.dark .sidebar-nav-item {
            color: rgba(0, 255, 255, 0.8);
        }

        .sidebar-wrapper.dark .sidebar-nav-item:hover {
            background: rgba(0, 255, 255, 0.1);
            color: #00ffff;
            border-color: rgba(0, 255, 255, 0.3);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.2);
        }

        .sidebar-wrapper.dark .sidebar-nav-item.active {
            background: rgba(0, 255, 255, 0.15);
            color: #00ffff;
            border-color: rgba(0, 255, 255, 0.5);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        /* Estilos para el scroll del sidebar */
        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(0, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 255, 255, 0.5);
        }

        /* Scrollbar para modo claro */
        .sidebar-wrapper:not(.dark) .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(0, 100, 200, 0.3);
        }

        .sidebar-wrapper:not(.dark) .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 100, 200, 0.5);
        }

        /* Asegurar que el sidebar tenga scroll suave */
        .sidebar-nav {
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 255, 255, 0.3) transparent;
        }

        .sidebar-wrapper:not(.dark) .sidebar-nav {
            scrollbar-color: rgba(0, 100, 200, 0.3) transparent;
        }

        .sidebar-wrapper.dark .sidebar-nav-item.active::before {
            background: #00ffff;
            box-shadow: 0 0 10px #00ffff;
        }

        .sidebar-submenu {
            margin-left: 1.5rem;
            padding-left: 1.25rem;
            border-left: 2px solid rgba(0, 255, 255, 0.3);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar-submenu-item {
            padding: 0.75rem 1.25rem;
            border-radius: 0.625rem;
            color: rgba(0, 255, 255, 0.7);
            font-size: 0.9rem;
            font-family: 'Inter', system-ui, sans-serif;
            transition: all 0.3s ease;
            display: block;
            width: 100%;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .sidebar-submenu-item:hover {
            background: rgba(0, 255, 255, 0.1);
            color: #00ffff;
            transform: translateX(4px);
            border-color: rgba(0, 255, 255, 0.2);
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.1);
        }

        .sidebar-submenu-item.active {
            background: rgba(0, 255, 255, 0.15);
            color: #00ffff;
            border-color: rgba(0, 255, 255, 0.3);
        }

        /* Modo claro para submenu */
        .sidebar-wrapper:not(.dark) .sidebar-submenu {
            border-left: 2px solid rgba(0, 100, 200, 0.3);
        }

        .sidebar-wrapper:not(.dark) .sidebar-submenu-item {
            color: rgba(0, 100, 200, 0.7);
        }

        .sidebar-wrapper:not(.dark) .sidebar-submenu-item:hover {
            background: rgba(0, 100, 200, 0.1);
            color: #0064c8;
            border-color: rgba(0, 100, 200, 0.2);
            box-shadow: 0 0 10px rgba(0, 100, 200, 0.1);
        }

        .sidebar-wrapper:not(.dark) .sidebar-submenu-item.active {
            background: rgba(0, 100, 200, 0.15);
            color: #0064c8;
            border-color: rgba(0, 100, 200, 0.3);
        }

        /* Modo oscuro para submenu */
        .sidebar-wrapper.dark .sidebar-submenu {
            border-left: 2px solid rgba(0, 255, 255, 0.3);
        }

        .sidebar-wrapper.dark .sidebar-submenu-item {
            color: rgba(0, 255, 255, 0.7);
        }

        .sidebar-wrapper.dark .sidebar-submenu-item:hover {
            background: rgba(0, 255, 255, 0.1);
            color: #00ffff;
            border-color: rgba(0, 255, 255, 0.2);
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.1);
        }

        .sidebar-wrapper.dark .sidebar-submenu-item.active {
            background: rgba(0, 255, 255, 0.15);
            color: #00ffff;
            border-color: rgba(0, 255, 255, 0.3);
        }

        /* Logo Styles - Tecnológico Adaptativo */
        .sidebar-logo {
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(0, 255, 255, 0.2);
            margin-bottom: 1.5rem;
        }

        .sidebar-logo img {
            height: 3.5rem;
            filter: brightness(0) invert(1) sepia(1) hue-rotate(180deg) saturate(2);
            transition: all 0.3s ease;
        }

        .sidebar-logo:hover img {
            filter: brightness(0) invert(1) sepia(1) hue-rotate(180deg) saturate(3);
            transform: scale(1.05);
        }

        /* Modo claro para logo */
        .sidebar-wrapper:not(.dark) .sidebar-logo {
            border-bottom: 1px solid rgba(0, 100, 200, 0.2);
        }

        .sidebar-wrapper:not(.dark) .sidebar-logo img {
            filter: brightness(0) saturate(0) hue-rotate(200deg) saturate(2);
        }

        .sidebar-wrapper:not(.dark) .sidebar-logo:hover img {
            filter: brightness(0) saturate(0) hue-rotate(200deg) saturate(3);
        }

        /* Modo oscuro para logo */
        .sidebar-wrapper.dark .sidebar-logo {
            border-bottom: 1px solid rgba(0, 255, 255, 0.2);
        }

        .sidebar-wrapper.dark .sidebar-logo img {
            filter: brightness(0) invert(1) sepia(1) hue-rotate(180deg) saturate(2);
        }

        .sidebar-wrapper.dark .sidebar-logo:hover img {
            filter: brightness(0) invert(1) sepia(1) hue-rotate(180deg) saturate(3);
        }

        /* Search Bar Styles - Tecnológico Adaptativo */
        .search-container {
            position: relative;
            margin: 1.5rem;
        }

        .search-input {
            width: 100%;
            padding: 0.875rem 1.25rem 0.875rem 3rem;
            border: 1px solid rgba(0, 255, 255, 0.2);
            border-radius: 0.875rem;
            background: rgba(0, 0, 0, 0.3);
            color: #00ffff;
            font-size: 0.95rem;
            font-family: 'Inter', system-ui, sans-serif;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .search-input::placeholder {
            color: rgba(0, 255, 255, 0.5);
        }

        .search-input:focus {
            outline: none;
            background: rgba(0, 0, 0, 0.5);
            border-color: rgba(0, 255, 255, 0.5);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(0, 255, 255, 0.7);
        }

        /* Modo claro para search */
        .sidebar-wrapper:not(.dark) .search-input {
            border: 1px solid rgba(0, 100, 200, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: #0064c8;
        }

        .sidebar-wrapper:not(.dark) .search-input::placeholder {
            color: rgba(0, 100, 200, 0.5);
        }

        .sidebar-wrapper:not(.dark) .search-input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(0, 100, 200, 0.5);
            box-shadow: 0 0 15px rgba(0, 100, 200, 0.2);
        }

        .sidebar-wrapper:not(.dark) .search-icon {
            color: rgba(0, 100, 200, 0.7);
        }

        /* Modo oscuro para search */
        .sidebar-wrapper.dark .search-input {
            border: 1px solid rgba(0, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.3);
            color: #00ffff;
        }

        .sidebar-wrapper.dark .search-input::placeholder {
            color: rgba(0, 255, 255, 0.5);
        }

        .sidebar-wrapper.dark .search-input:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: rgba(0, 255, 255, 0.5);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.2);
        }

        .sidebar-wrapper.dark .search-icon {
            color: rgba(0, 255, 255, 0.7);
        }



        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar-wrapper {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar-wrapper.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Mobile Sidebar Specific Styles - Tecnológico Adaptativo */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 320px;
            max-width: 85vw;
            background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 50%, #e2e8f0 100%);
            border-right: 1px solid rgba(0, 100, 200, 0.2);
            box-shadow: 0 0 30px rgba(0, 100, 200, 0.1);
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
        }

        .mobile-sidebar.show {
            transform: translateX(0);
        }

        .mobile-sidebar .sidebar-logo {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(0, 100, 200, 0.2);
            margin-bottom: 1rem;
        }

        .mobile-sidebar .sidebar-nav {
            padding: 1rem;
        }

        /* Modo oscuro para mobile sidebar */
        .dark .mobile-sidebar {
            background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 50%, #2a2a2a 100%);
            border-right: 1px solid rgba(0, 255, 255, 0.3);
        }

        .dark .mobile-sidebar .sidebar-logo {
            border-bottom: 1px solid rgba(0, 255, 255, 0.3);
        }

        /* Modo claro para mobile sidebar */
        .mobile-sidebar:not(.dark) {
            background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 50%, #e2e8f0 100%);
            border-right: 1px solid rgba(0, 100, 200, 0.2);
        }

        .mobile-sidebar:not(.dark) .sidebar-logo {
            border-bottom: 1px solid rgba(0, 100, 200, 0.2);
        }

        .mobile-sidebar:not(.dark) .sidebar-logo img {
            filter: brightness(0) saturate(0) hue-rotate(200deg) saturate(2);
        }

        .mobile-sidebar:not(.dark) .sidebar-logo:hover img {
            filter: brightness(0) saturate(0) hue-rotate(200deg) saturate(3);
        }

        .dark .mobile-sidebar .sidebar-logo img {
            filter: brightness(0) invert(1) sepia(1) hue-rotate(180deg) saturate(2);
        }

        .dark .mobile-sidebar .sidebar-logo:hover img {
            filter: brightness(0) invert(1) sepia(1) hue-rotate(180deg) saturate(3);
        }

        /* Estilos específicos para elementos del sidebar móvil en modo claro */
        .mobile-sidebar:not(.dark) .sidebar-nav-item {
            color: rgba(0, 100, 200, 0.8);
        }

        .mobile-sidebar:not(.dark) .sidebar-nav-item:hover {
            background: rgba(0, 100, 200, 0.1);
            color: #0064c8;
            border-color: rgba(0, 100, 200, 0.3);
            box-shadow: 0 0 15px rgba(0, 100, 200, 0.2);
        }

        .mobile-sidebar:not(.dark) .sidebar-nav-item.active {
            background: rgba(0, 100, 200, 0.15);
            color: #0064c8;
            border-color: rgba(0, 100, 200, 0.5);
            box-shadow: 0 0 20px rgba(0, 100, 200, 0.3);
        }

        .mobile-sidebar:not(.dark) .sidebar-nav-item.active::before {
            background: #0064c8;
            box-shadow: 0 0 10px #0064c8;
        }

        .mobile-sidebar:not(.dark) .sidebar-submenu {
            border-left: 2px solid rgba(0, 100, 200, 0.3);
        }

        .mobile-sidebar:not(.dark) .sidebar-submenu-item {
            color: rgba(0, 100, 200, 0.7);
        }

        .mobile-sidebar:not(.dark) .sidebar-submenu-item:hover {
            background: rgba(0, 100, 200, 0.1);
            color: #0064c8;
            border-color: rgba(0, 100, 200, 0.2);
            box-shadow: 0 0 10px rgba(0, 100, 200, 0.1);
        }

        .mobile-sidebar:not(.dark) .sidebar-submenu-item.active {
            background: rgba(0, 100, 200, 0.15);
            color: #0064c8;
            border-color: rgba(0, 100, 200, 0.3);
        }

        /* Estilos específicos para elementos del sidebar móvil en modo oscuro */
        .dark .mobile-sidebar .sidebar-nav-item {
            color: rgba(0, 255, 255, 0.8);
        }

        .dark .mobile-sidebar .sidebar-nav-item:hover {
            background: rgba(0, 255, 255, 0.1);
            color: #00ffff;
            border-color: rgba(0, 255, 255, 0.3);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.2);
        }

        .dark .mobile-sidebar .sidebar-nav-item.active {
            background: rgba(0, 255, 255, 0.15);
            color: #00ffff;
            border-color: rgba(0, 255, 255, 0.5);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        .dark .mobile-sidebar .sidebar-nav-item.active::before {
            background: #00ffff;
            box-shadow: 0 0 10px #00ffff;
        }

        .dark .mobile-sidebar .sidebar-submenu {
            border-left: 2px solid rgba(0, 255, 255, 0.3);
        }

        .dark .mobile-sidebar .sidebar-submenu-item {
            color: rgba(0, 255, 255, 0.7);
        }

        .dark .mobile-sidebar .sidebar-submenu-item:hover {
            background: rgba(0, 255, 255, 0.1);
            color: #00ffff;
            border-color: rgba(0, 255, 255, 0.2);
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.1);
        }

        .dark .mobile-sidebar .sidebar-submenu-item.active {
            background: rgba(0, 255, 255, 0.15);
            color: #00ffff;
            border-color: rgba(0, 255, 255, 0.3);
        }



        /* Content Area Improvements */
        .content-area {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            font-family: 'Poppins', system-ui, sans-serif;
            color: #111827;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 20px rgba(0, 100, 200, 0.3);
        }

        .page-subtitle {
            color: #374151;
            font-size: 1.1rem;
            font-family: 'Inter', system-ui, sans-serif;
            font-weight: 500;
        }

        .dark .page-title {
            background: linear-gradient(135deg, #00ffff 0%, #0080ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        .dark .page-subtitle {
            color: rgba(0, 255, 255, 0.7);
        }

        /* Card Improvements - Tecnológico Adaptativo */
        .content-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 100, 200, 0.1);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            color: #111827;
        }

        .content-card:hover {
            box-shadow: 0 12px 40px rgba(0, 100, 200, 0.1);
            transform: translateY(-4px);
            border-color: rgba(0, 100, 200, 0.3);
        }

        .dark .content-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border-color: rgba(0, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: #e2e8f0;
        }

        .dark .content-card:hover {
            box-shadow: 0 12px 40px rgba(0, 255, 255, 0.1);
            border-color: rgba(0, 255, 255, 0.3);
        }

        /* Button Improvements - Tecnológico Adaptativo */
        .btn-modern {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-family: 'Inter', system-ui, sans-serif;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #0064c8 0%, #004a96 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 100, 200, 0.3);
            font-weight: 700;
        }

        .btn-primary-modern:hover {
            background: linear-gradient(135deg, #004a96 0%, #0064c8 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 100, 200, 0.4);
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            font-weight: 700;
        }

        .btn-success-modern:hover {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-danger-modern {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            font-weight: 700;
        }

        .btn-danger-modern:hover {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
        }

        .btn-neon-modern {
            background: transparent;
            border: 2px solid #0064c8;
            color: #0064c8;
            box-shadow: 0 0 10px rgba(0, 100, 200, 0.3);
        }

        .btn-neon-modern:hover {
            background: #0064c8;
            color: white;
            box-shadow: 0 0 20px rgba(0, 100, 200, 0.5);
        }

        /* Modo oscuro para botones */
        .dark .btn-primary-modern {
            background: linear-gradient(135deg, #00ffff 0%, #0080ff 100%);
            color: #0a0a0a;
            box-shadow: 0 4px 12px rgba(0, 255, 255, 0.3);
        }

        .dark .btn-primary-modern:hover {
            background: linear-gradient(135deg, #0080ff 0%, #00ffff 100%);
            box-shadow: 0 8px 20px rgba(0, 255, 255, 0.4);
        }

        .dark .btn-success-modern {
            background: linear-gradient(135deg, #00ff80 0%, #00cc66 100%);
            color: #0a0a0a;
            box-shadow: 0 4px 12px rgba(0, 255, 128, 0.3);
        }

        .dark .btn-success-modern:hover {
            background: linear-gradient(135deg, #00cc66 0%, #00ff80 100%);
            box-shadow: 0 8px 20px rgba(0, 255, 128, 0.4);
        }

        .dark .btn-danger-modern {
            background: linear-gradient(135deg, #ff0080 0%, #cc0066 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 0, 128, 0.3);
        }

        .dark .btn-danger-modern:hover {
            background: linear-gradient(135deg, #cc0066 0%, #ff0080 100%);
            box-shadow: 0 8px 20px rgba(255, 0, 128, 0.4);
        }

        .dark .btn-neon-modern {
            border: 2px solid #00ffff;
            color: #00ffff;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
        }

        .dark .btn-neon-modern:hover {
            background: #00ffff;
            color: #0a0a0a;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
        }

        /* Estilos para texto en modo claro y oscuro */
        .text-content {
            color: #111827;
        }

        .dark .text-content {
            color: #e2e8f0;
        }

        .text-content-secondary {
            color: #374151;
        }

        .dark .text-content-secondary {
            color: #94a3b8;
        }

        .text-content-muted {
            color: #6b7280;
        }

        .dark .text-content-muted {
            color: #64748b;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">
    <div class="main-container" x-data="{ sidebarOpen: false }">
        <!-- Sidebar Móvil (offcanvas) -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-50 lg:hidden"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

            <!-- Overlay de fondo -->
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false">
            </div>

            <!-- Sidebar Móvil -->
            <div class="mobile-sidebar" :class="{ 'show': sidebarOpen, 'dark': darkMode }">
                <div class="flex justify-end p-4">
                    <button type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-black bg-opacity-20 hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-inset dark:focus:ring-white transition-all duration-200"
                        @click="sidebarOpen = false">
                        <span class="sr-only">Cerrar sidebar</span>
                        <svg class="h-6 w-6 text-black dark:text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Logo -->
                <div class="sidebar-logo">
                    <a href="{{ route('admin.index') }}" class="text-2xl font-bold text-blue-600">
                        <img src="{{ asset('img/Logo_2.png') }}" alt="4GMovil " class="h-16">
                    </a>
                </div>

                <!-- Navegación móvil -->
                <div class="flex-1 overflow-y-auto sidebar-nav" style="max-height: calc(100vh - 200px);">
                    @include('layouts.partials.sidebar-menu')
                </div>
            </div>
        </div>

        <!-- Sidebar Desktop -->
        <div class="sidebar-wrapper hidden lg:block" :class="{ 'dark': darkMode }">
            <!-- Logo -->
            <div class="sidebar-logo">
                <a href="{{ route('admin.index') }}" class="text-2xl font-bold text-blue-600">
                    <img src="{{ asset('img/Logo_2.png') }}" alt="4GMovil " class="h-16">
                </a>
            </div>



            <!-- Navegación -->
            <div class="flex-1 overflow-y-auto sidebar-nav" style="max-height: calc(100vh - 120px);">
                @include('layouts.partials.sidebar-menu')
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="main-content" :class="{ 'dark': darkMode }">
            <!-- Barra superior -->
            <header class="header-wrapper sticky top-0 z-10 flex h-16 flex-shrink-0" :class="{ 'dark': darkMode }">
                <button type="button"
                    class="border-r border-gray-200 dark:border-gray-700 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-500 lg:hidden"
                    @click="sidebarOpen = true">
                    <span class="sr-only">Abrir sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex flex-1 justify-between px-4">

                    <div class="ml-4 flex items-center md:ml-6">
                        @auth
                            <div class="flex items-center space-x-4">
                                <!-- Botón de cambio de tema -->
                                <button @click="darkMode = !darkMode"
                                    class="p-2 text-blue-600 hover:text-blue-700 dark:text-cyan-400 dark:hover:text-cyan-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-cyan-500 transition-all duration-300 hover:bg-blue-100 dark:hover:bg-cyan-900/20 rounded-lg">
                                    <span class="sr-only">Cambiar tema</span>
                                    <!-- Ícono sol para modo claro -->
                                    <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <!-- Ícono luna para modo oscuro -->
                                    <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </button>

                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="flex items-center max-w-xs text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-cyan-500 transition-all duration-300 hover:bg-blue-100 dark:hover:bg-cyan-900/20 p-2 rounded-lg">
                                        <span class="sr-only">Abrir menú usuario</span>
                                        @if (Auth::user()->foto_perfil)
                                            @php
                                                $photoUrl = \App\Helpers\PhotoHelper::getPhotoUrl(
                                                    Auth::user()->foto_perfil,
                                                );
                                            @endphp
                                            <div
                                                class="profile-image-container h-8 w-8 rounded-full overflow-hidden ring-2 ring-blue-400 dark:ring-cyan-400">
                                                <img class="profile-image lazy-load" src="{{ $photoUrl }}"
                                                    alt="{{ Auth::user()->nombre_usuario }}" loading="lazy">
                                            </div>
                                        @else
                                            <div
                                                class="h-8 w-8 rounded-full bg-blue-100 dark:bg-cyan-900 flex items-center justify-center ring-2 ring-blue-400 dark:ring-cyan-400">
                                                <svg class="h-5 w-5 text-blue-600 dark:text-cyan-300" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <span
                                            class="md:block hidden lg:inline ml-3 text-gray-700 dark:text-cyan-300 font-medium">{{ Auth::user()->nombre_usuario }}</span>
                                    </button>

                                    <!-- Menú desplegable -->
                                    <div x-show="open" @click.away="open = false"
                                        class="origin-top-right absolute right-0 md:right-1 mt-2 w-56 md:w-64 rounded-xl shadow-2xl py-2 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md ring-1 ring-blue-500/20 dark:ring-cyan-500/20 border border-blue-200 dark:border-cyan-500/20"
                                        role="menu" x-cloak>
                                        <a href="{{ url('/perfil') }}"
                                            class="block px-6 py-3 text-sm text-gray-700 dark:text-cyan-300 hover:bg-blue-50 dark:hover:bg-cyan-900/50 transition-all duration-300 font-medium"
                                            role="menuitem">
                                            <i class="fa-solid fa-user mr-3 text-blue-600 dark:text-cyan-400"></i>Mi perfil
                                        </a>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="block w-full text-left px-6 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/50 transition-all duration-300 font-medium"
                                                role="menuitem">
                                                <i class="fa-solid fa-right-from-bracket mr-3"></i>Cerrar sesión
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>

                    <!-- Iconos de redes sociales en la esquina superior derecha -->
                    <div class="flex items-center space-x-3 mr-4">
                        <a href="https://www.facebook.com/cuatro.g.movil.2025/"
                            class="text-blue-600 hover:text-blue-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-all duration-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-cyan-900/20 hover:scale-110">
                            <i class="fab fa-facebook-f text-base md:text-lg"></i>
                        </a>
                        <a href="https://www.instagram.com/tumovil4g/?fbclid=IwY2xjawJClENleHRuA2FlbQIxMAABHfgoRN_xkx-QlgRi5XJX_YY8IVnmcJTaee4R2UWXoOMJTTip9ml-DYoVXw_aem_-9MriOuo88DFcLIGBeizRw"
                            class="text-blue-600 hover:text-blue-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-all duration-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-cyan-900/20 hover:scale-110">
                            <i class="fab fa-instagram text-base md:text-lg"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send/?phone=573117337272&text&type=phone_number&app_absent=0"
                            class="text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 transition-all duration-300 p-2 rounded-full hover:bg-green-100 dark:hover:bg-green-900/20 hover:scale-110">
                            <i class="fab fa-whatsapp text-base md:text-lg"></i>
                        </a>
                        <a href="https://www.tiktok.com/@tumovil4g"
                            class="text-blue-600 hover:text-blue-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-all duration-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-cyan-900/20 hover:scale-110">
                            <i class="fab fa-tiktok text-base md:text-lg"></i>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Contenido de la página -->
            <main class="flex-1">
                <div class="content-area">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Load jQuery only when needed -->
    @stack('jquery-script')

    <!-- Load DataTables only when needed -->
    @stack('datatables-script')

    <!-- SweetAlert2 Scripts -->
    @include('layouts.partials.sweet-alerts')

    @stack('scripts')

    <!-- Lazy loading script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lazy load images
            const lazyImages = document.querySelectorAll('.lazy-load');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            });

            lazyImages.forEach(img => imageObserver.observe(img));
        });
    </script>

</body>

</html>
