<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Stock Bajo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            padding: 20px;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            font-size: 16px;
            background-color: #dc3545;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #c82333;
        }
        .footer {
            font-size: 12px;
            color: #6c757d;
            margin-top: 30px;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
        .content {
            margin: 20px 0;
            line-height: 1.6;
        }
        .product-details {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .product-details h3 {
            color: #495057;
            margin-top: 0;
        }
        .product-details p {
            margin: 8px 0;
        }
        .warning-icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .alert-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #721c24;
        }
        .stock-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            color: #856404;
        }
        .stock-info h4 {
            color: #856404;
            margin-top: 0;
        }
        .stock-info .stock-number {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
        }
        .actions {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            color: #1976d2;
        }
        .actions h4 {
            color: #1976d2;
            margin-top: 0;
        }
        .actions ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .actions li {
            margin: 8px 0;
        }
        .product-image {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning-icon">⚠️</div>
        <h1>Stock Bajo</h1>
        
        <div class="content">
            <p>Se ha detectado que un producto tiene <strong>stock bajo</strong> en <strong style="color: #000;">4GMovil</strong>.</p>
            <p>Es necesario tomar acción inmediata para reponer el inventario.</p>
        </div>
        
        <div class="alert-box">
            <strong>🚨 ALERTA DE INVENTARIO:</strong><br>
            El producto ha alcanzado el nivel mínimo de stock establecido.
        </div>
        
        <div class="stock-info">
            <h4>📊 Información del Stock</h4>
            <p><strong>Stock Actual:</strong> <span class="stock-number">{{ $producto->stock }}</span> unidades</p>
            <p><strong>Porcentaje del Stock:</strong> <span class="stock-number">{{ $porcentajeActual }}%</span></p>
            <p><strong>Stock Mínimo:</strong> {{ $stockMinimo }} unidades</p>
            <p><strong>Estado:</strong> <span style="color: #dc3545;">CRÍTICO</span></p>
        </div>
        
        <div class="product-details">
            <h3>📦 Detalles del Producto</h3>
            <p><strong>Nombre:</strong> {{ $producto->nombre_producto }}</p>
            <p><strong>Código:</strong> {{ $producto->codigo ?? 'N/A' }}</p>
            <p><strong>Categoría:</strong> {{ $producto->categoria->nombre_categoria ?? 'N/A' }}</p>
            <p><strong>Precio:</strong> ${{ number_format($producto->precio, 0, ',', '.') }}</p>
            <p><strong>Fecha de Alerta:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        
        <a href="{{ $productoUrl }}" class="btn">📦 Ver Producto</a>
        
        <div class="actions">
            <h4>🔄 Acciones Recomendadas</h4>
            <ul>
                <li><strong>Contactar al proveedor</strong> - Para solicitar reposición inmediata</li>
                <li><strong>Verificar stock en bodega</strong> - Confirmar disponibilidad real</li>
                <li><strong>Actualizar inventario</strong> - Corregir discrepancias si las hay</li>
                <li><strong>Evaluar demanda</strong> - Analizar si se necesita más stock</li>
                <li><strong>Notificar al equipo</strong> - Informar sobre la situación</li>
            </ul>
        </div>
        
        <div class="content">
            <p><strong>📋 Próximos Pasos:</strong></p>
            <p>• Revisar el producto en el sistema</p>
            <p>• Contactar al proveedor para reposición</p>
            <p>• Actualizar el stock cuando llegue la mercancía</p>
            <p>• Monitorear el nivel de stock regularmente</p>
        </div>
        
        <div class="footer">
            <p>Esta alerta se genera automáticamente cuando el stock alcanza el nivel mínimo.</p>
            <p>Por favor, toma las acciones necesarias para evitar quedarte sin inventario.</p>
            <p>© 2025 4GMovil S.A.S. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
