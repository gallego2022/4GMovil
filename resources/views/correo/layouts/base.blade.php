<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '4GMovil S.A.S')</title>
    <style>
        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        /* Contenedor principal */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #3B82F6, #1E40AF);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .tagline {
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Contenido principal */
        .content {
            padding: 30px 20px;
        }
        
        /* Footer */
        .footer {
            background-color: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        
        .footer a {
            color: #3B82F6;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        /* Botones */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3B82F6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 10px 5px;
            transition: background-color 0.3s ease;
        }
        
        .btn:hover {
            background-color: #2563eb;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        /* Tarjetas */
        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        
        .card-header {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
            margin: 0;
        }
        
        /* Alertas */
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        
        .alert-info {
            background-color: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
        }
        
        .alert-warning {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            color: #92400e;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            border: 1px solid #f87171;
            color: #991b1b;
        }
        
        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #34d399;
            color: #065f46;
        }
        
        /* Grid */
        .grid {
            display: grid;
            gap: 15px;
        }
        
        .grid-2 {
            grid-template-columns: 1fr 1fr;
        }
        
        .grid-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .header {
                padding: 20px 15px;
            }
        }
        
        /* Utilidades */
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
        
        .mt-0 { margin-top: 0; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
        
        .text-sm { font-size: 14px; }
        .text-lg { font-size: 18px; }
        .text-xl { font-size: 24px; }
        .text-2xl { font-size: 28px; }
        
        .font-bold { font-weight: bold; }
        .font-semibold { font-weight: 600; }
        
        .text-gray-500 { color: #6c757d; }
        .text-gray-600 { color: #4a5568; }
        .text-gray-700 { color: #2d3748; }
        .text-blue-600 { color: #3B82F6; }
        .text-red-600 { color: #dc3545; }
        .text-green-600 { color: #059669; }
        .text-yellow-600 { color: #d97706; }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">4GMovil S.A.S</div>
            <div class="tagline">Tu tienda de tecnología de confianza</div>
        </div>
        
        <!-- Contenido principal -->
        <div class="content">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>4GMovil S.A.S</strong></p>
            <p>Tu tienda de tecnología de confianza</p>
            <p>📧 info@4gmovil.com | 📞 +57 300 123 4567</p>
            <p>📍 Calle Principal #123, Ciudad, Colombia</p>
            <p class="text-sm mt-2">
                Este es un mensaje automático del sistema de 4GMovil S.A.S.<br>
                Si tienes alguna pregunta, no dudes en contactarnos.
            </p>
            <p class="text-sm mt-2">
                Fecha: {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>
