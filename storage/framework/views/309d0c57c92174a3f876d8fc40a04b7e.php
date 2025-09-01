<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Agotado - Variante</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3B82F6;
            margin-bottom: 10px;
        }
        .alert-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .alert-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #dc3545;
        }
        .alert-subtitle {
            font-size: 16px;
            color: #6c757d;
        }
        .urgent-banner {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .urgent-banner h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .urgent-banner p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .product-info {
            background-color: #fff5f5;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .product-name {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 10px;
        }
        .variant-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            margin-right: 15px;
        }
        .variant-details {
            flex: 1;
        }
        .variant-name {
            font-weight: bold;
            color: #4a5568;
        }
        .stock-status {
            background-color: #dc3545;
            color: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin: 20px 0;
        }
        .stock-status h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .stock-status .stock-value {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .impact-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .impact-section h3 {
            margin-top: 0;
            color: #2d3748;
        }
        .impact-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .impact-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
        }
        .impact-list li:last-child {
            border-bottom: none;
        }
        .impact-list li:before {
            content: "⚠️ ";
            margin-right: 8px;
        }
        .actions {
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: #dc3545;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 0 10px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #c82333;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .info-item {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
        }
        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">4GMovil S.A.S</div>
            <div class="alert-icon">🚨</div>
            <div class="alert-title">STOCK AGOTADO</div>
            <div class="alert-subtitle">Alerta crítica de inventario - Variante de producto</div>
        </div>

        <div class="urgent-banner">
            <h2>⚠️ ACCIÓN INMEDIATA REQUERIDA</h2>
            <p>Esta variante se ha agotado completamente. Se requiere reposición urgente para evitar pérdida de ventas.</p>
        </div>

        <div class="product-info">
            <div class="product-name"><?php echo e($producto->nombre_producto); ?></div>
            
            <div class="variant-info">
                <div class="color-preview" style="background-color: <?php echo e($variante->codigo_color ?? '#CCCCCC'); ?>;"></div>
                <div class="variant-details">
                    <div class="variant-name"><?php echo e($variante->nombre); ?></div>
                    <div style="color: #6c757d; font-size: 14px;">
                        Código: <?php echo e($variante->variante_id); ?> | 
                        Precio adicional: $<?php echo e(number_format($variante->precio_adicional, 0, ',', '.')); ?>

                    </div>
                </div>
            </div>

            <div class="stock-status">
                <h3>Estado del Stock</h3>
                <div class="stock-value">0</div>
                <p>Unidades disponibles</p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Producto ID</div>
                <div class="info-value"><?php echo e($producto->producto_id); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Variante ID</div>
                <div class="info-value"><?php echo e($variante->variante_id); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Categoría</div>
                <div class="info-value"><?php echo e($producto->categoria->nombre ?? 'N/A'); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Marca</div>
                <div class="info-value"><?php echo e($producto->marca->nombre ?? 'N/A'); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Precio Base</div>
                <div class="info-value">$<?php echo e(number_format($producto->precio, 0, ',', '.')); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Precio Total</div>
                <div class="info-value">$<?php echo e(number_format($producto->precio + $variante->precio_adicional, 0, ',', '.')); ?></div>
            </div>
        </div>

        <div class="impact-section">
            <h3>📊 Impacto del Agotamiento</h3>
            <ul class="impact-list">
                <li><strong>Pérdida de ventas:</strong> No se pueden procesar pedidos para esta variante</li>
                <li><strong>Experiencia del cliente:</strong> Los clientes no pueden comprar esta variante</li>
                <li><strong>Reputación:</strong> Puede afectar la percepción de disponibilidad del producto</li>
                <li><strong>Competencia:</strong> Los clientes pueden buscar alternativas en otros sitios</li>
                <li><strong>SEO:</strong> Puede afectar el posicionamiento si el producto aparece como no disponible</li>
            </ul>
        </div>

        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #856404;">🚀 Acciones Recomendadas</h4>
            <ol style="margin: 0; padding-left: 20px; color: #856404;">
                <li><strong>Inmediato (0-2 horas):</strong> Contactar al proveedor para solicitar reposición urgente</li>
                <li><strong>Hoy mismo:</strong> Actualizar la página del producto para mostrar "Temporalmente agotado"</li>
                <li><strong>En 24 horas:</strong> Implementar notificación de "Volver a tener stock" para clientes interesados</li>
                <li><strong>En 48 horas:</strong> Revisar el stock mínimo para evitar futuros agotamientos</li>
                <li><strong>Esta semana:</strong> Analizar la demanda para ajustar el inventario</li>
            </ol>
        </div>

        <div class="actions">
            <a href="<?php echo e($productoUrl); ?>" class="btn">Ver Producto</a>
            <a href="<?php echo e(route('admin.inventario.variantes.reporte')); ?>" class="btn btn-secondary">Reporte de Inventario</a>
        </div>

        <div class="footer">
            <p><strong>Este es un mensaje automático del sistema de inventario de 4GMovil S.A.S</strong></p>
            <p>Fecha de agotamiento: <?php echo e($fechaAgotamiento); ?></p>
            <p>Si tienes alguna pregunta, contacta al equipo de administración inmediatamente.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Proyecto V11.3\4GMovil\resources\views/correo/stock-agotado-variante.blade.php ENDPATH**/ ?>