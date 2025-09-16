<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Error') - {{ config('app.name', '4GMovil') }}</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Inicialización de modo oscuro antes del render para evitar FOUC -->
    <script>
        (function() {
            try {
                var stored = localStorage.getItem('darkMode');
                var shouldDark = stored === 'true' || (stored === null && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (shouldDark) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {
                // noop
            }
        })();
    </script>
    
    <!-- Estilos básicos para errores -->
    <style>
        :root {
            --bg-start: #eef2ff; /* slate-50/indigo-50 */
            --bg-mid: #e0e7ff;   /* indigo-100 */
            --bg-end: #c7d2fe;   /* indigo-200 */
            --card-bg: rgba(255, 255, 255, 0.8);
            --card-border: rgba(17, 24, 39, 0.08);
            --text-primary: #0f172a;
            --text-secondary: rgba(15, 23, 42, 0.7);
            --accent-from: #6366f1;
            --accent-via: #8b5cf6;
            --accent-to: #d946ef;
        }

        .dark :root, .dark {
            --bg-start: #0b1020; /* profundo */
            --bg-mid: #0f172a;   /* slate-900 */
            --bg-end: #111827;   /* gray-900 */
            --card-bg: rgba(17, 24, 39, 0.75);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: rgba(248, 250, 252, 0.75);
            --accent-from: #60a5fa;
            --accent-via: #818cf8;
            --accent-to: #c084fc;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(1200px circle at 10% 10%, var(--bg-start), transparent 40%),
                        radial-gradient(1200px circle at 90% 20%, var(--bg-mid), transparent 40%),
                        linear-gradient(135deg, var(--bg-start) 0%, var(--bg-mid) 40%, var(--bg-end) 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            transition: background 300ms ease;
        }
        
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .error-card {
            background: var(--card-bg);
            -webkit-backdrop-filter: blur(22px);
            backdrop-filter: blur(22px);
            border-radius: 28px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 30px 60px -16px rgba(0, 0, 0, 0.35);
            border: 1px solid var(--card-border);
            max-width: 640px;
            width: 100%;
            transition: background 300ms ease, border-color 300ms ease, box-shadow 300ms ease;
        }
        
        .error-code {
            font-size: 10rem;
            font-weight: 900;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 25%, #c7d2fe 50%, #a5b4fc 75%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 0.9;
            margin-bottom: 1.5rem;
        }

        .dark .error-code {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 25%, #a5b4fc 50%, #93c5fd 75%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .error-title {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .error-description {
            font-size: 1.125rem;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        
        .error-icon {
            font-size: 4rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            opacity: 0.95;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }
        
        .error-btn {
            padding: 1rem 2rem;
            border-radius: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .error-btn-primary {
            background: linear-gradient(135deg, var(--accent-from), var(--accent-via), var(--accent-to));
            color: white;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }
        
        .error-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(99, 102, 241, 0.6);
        }
        
        .error-btn-secondary {
            background: rgba(0, 0, 0, 0.04);
            color: var(--text-primary);
            border: 2px solid rgba(0, 0, 0, 0.08);
        }
        
        .error-btn-secondary:hover {
            background: rgba(0, 0, 0, 0.06);
            border-color: rgba(0, 0, 0, 0.16);
        }

        .dark .error-btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-primary);
            border: 2px solid rgba(255, 255, 255, 0.12);
        }

        .dark .error-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .error-info {
            background: rgba(0, 0, 0, 0.04);
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid var(--card-border);
        }
        
        .error-info h4 {
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .error-info p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .error-services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .error-service-card {
            background: rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--card-border);
            text-align: center;
        }
        
        .error-service-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.95;
        }
        
        .error-service-title {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .error-service-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .error-links {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--card-border);
        }
        
        .error-links h4 {
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .error-links-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
        }
        
        .error-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.04);
            border: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .error-links a:hover {
            color: var(--text-primary);
            background: rgba(0, 0, 0, 0.06);
            border-color: rgba(0, 0, 0, 0.16);
        }

        .dark .error-info,
        .dark .error-service-card,
        .dark .error-links a {
            background: rgba(255, 255, 255, 0.06);
        }

        .dark .error-links a:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        @media (max-width: 768px) {
            .error-container {
                padding: 1rem;
            }
            
            .error-card {
                padding: 2rem;
            }
            
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 1.75rem;
            }
            
            .error-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .error-btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }
        }
        
        /* Buscador de 404 */
        .error-search-form {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            justify-content: center;
        }
        .error-search-input {
            flex: 1 1 auto;
            max-width: 420px;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            background: rgba(255, 255, 255, 0.9);
            color: #0f172a;
            outline: none;
            transition: border-color 200ms ease, box-shadow 200ms ease;
        }
        .error-search-input::placeholder {
            color: rgba(15, 23, 42, 0.5);
        }
        .error-search-input:focus {
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }
        .error-search-btn {
            padding: 0.875rem 1rem;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            background: linear-gradient(135deg, var(--accent-from), var(--accent-via));
            color: #fff;
            cursor: pointer;
            transition: transform 150ms ease, box-shadow 200ms ease;
        }
        .error-search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.35);
        }
        .dark .error-search-input {
            background: rgba(17, 24, 39, 0.85);
            color: var(--text-primary);
        }
        .dark .error-search-input::placeholder {
            color: rgba(248, 250, 252, 0.5);
        }
        /* Sugerencias */
        .search-suggestions {
            position: absolute;
            z-index: 1000;
            width: min(640px, calc(100% - 2rem));
            max-height: 60vh;
            overflow-y: auto;
            margin-top: 0.5rem;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
        }
        .search-suggestions .item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: var(--text-primary); text-decoration: none; }
        .search-suggestions .item:hover { background: rgba(0,0,0,0.06); }
        .search-suggestions .section-title { padding: 0.5rem 1rem; font-size: 0.8rem; opacity: 0.8; text-transform: uppercase; letter-spacing: .04em; }
    </style>
</head>
<body>
    <!-- Contenedor principal -->
    <div class="error-container">
        <div class="error-card">
            @yield('content')
        </div>
    </div>
    
    <!-- Botón flotante de modo oscuro -->
    <button id="error-theme-toggle" aria-label="Cambiar tema"
            style="position: fixed; right: 1rem; bottom: 1rem; z-index: 9999; border: 1px solid var(--card-border); border-radius: 9999px; padding: 0.75rem; background: var(--card-bg); color: var(--text-primary); box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"
    >
        <span id="error-theme-icon" class="fas fa-moon"></span>
    </button>
    <script>
        (function() {
            var btn = document.getElementById('error-theme-toggle');
            var icon = document.getElementById('error-theme-icon');
            function isDark() { return document.documentElement.classList.contains('dark'); }
            function setIcon() { icon.className = isDark() ? 'fas fa-sun' : 'fas fa-moon'; }
            setIcon();
            btn.addEventListener('click', function() {
                var nowDark = !isDark();
                if (nowDark) {
                    document.documentElement.classList.add('dark');
                    try { localStorage.setItem('darkMode', 'true'); } catch (e) {}
                } else {
                    document.documentElement.classList.remove('dark');
                    try { localStorage.setItem('darkMode', 'false'); } catch (e) {}
                }
                setIcon();
            });
        })();
    </script>
    
    <!-- Scripts adicionales -->
    @stack('scripts')
</body>
</html>