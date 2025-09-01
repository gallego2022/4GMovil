<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Stock - Variante</title>
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
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .alert-subtitle {
            font-size: 16px;
            color: #6c757d;
        }
        .product-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #3B82F6;
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
        .stock-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .stock-item {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }
        .stock-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stock-value {
            font-size: 24px;
            font-weight: bold;
        }
        .stock-actual {
            color: #dc3545;
        }
        .stock-minimo {
            color: #fd7e14;
        }
        .percentage-bar {
            background-color: #e2e8f0;
            border-radius: 10px;
            height: 20px;
            margin: 15px 0;
            overflow: hidden;
        }
        .percentage-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        .percentage-fill.critico {
            background: linear-gradient(90deg, #dc3545, #fd7e14);
        }
        .percentage-fill.bajo {
            background: linear-gradient(90deg, #fd7e14, #ffc107);
        }
        .percentage-text {
            text-align: center;
            font-weight: bold;
            margin-top: 5px;
        }
        .actions {
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3B82F6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 0 10px;
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
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .urgent {
            background-color: #fff5f5;
            border-left-color: #dc3545;
        }
        .warning {
            background-color: #fffbf0;
            border-left-color: #fd7e14;
        }
        .info {
            background-color: #f0f9ff;
            border-left-color: #3B82F6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">4GMovil S.A.S</div>
            <div class="alert-icon">
                <?php if($tipoAlerta === 'agotado'): ?>
                    🚨
                <?php elseif($tipoAlerta === 'critico'): ?>
                    ⚠️
                <?php else: ?>
                    📉
                <?php endif; ?>
            </div>
            <div class="alert-title">
                <?php if($tipoAlerta === 'agotado'): ?>
                    Stock Agotado
                <?php elseif($tipoAlerta === 'critico'): ?>
                    Stock Crítico
                <?php else: ?>
                    Stock Bajo
                <?php endif; ?>
            </div>
            <div class="alert-subtitle">Alerta de inventario - Variante de producto</div>
        </div>

        <div class="product-info <?php echo e($tipoAlerta === 'agotado' ? 'urgent' : ($tipoAlerta === 'critico' ? 'warning' : 'info')); ?>">
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

            <div class="stock-info">
                <div class="stock-item">
                    <div class="stock-label">Stock Actual</div>
                    <div class="stock-value stock-actual"><?php echo e(number_format($stockActual)); ?></div>
                </div>
                <div class="stock-item">
                    <div class="stock-label">Stock Mínimo</div>
                    <div class="stock-value stock-minimo"><?php echo e(number_format($stockMinimo)); ?></div>
                </div>
            </div>

            <?php if($stockMinimo > 0): ?>
                <div class="percentage-bar">
                    <div class="percentage-fill <?php echo e($tipoAlerta); ?>" style="width: <?php echo e(min(100, ($stockActual / $stockMinimo) * 100)); ?>%;"></div>
                </div>
                <div class="percentage-text">
                    <?php echo e($porcentajeActual); ?>% del stock mínimo
                </div>
            <?php endif; ?>
        </div>

        <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #2d3748;">📋 Información Adicional</h3>
            <ul style="margin: 0; padding-left: 20px; color: #4a5568;">
                <li><strong>Producto ID:</strong> <?php echo e($producto->producto_id); ?></li>
                <li><strong>Variante ID:</strong> <?php echo e($variante->variante_id); ?></li>
                <li><strong>Categoría:</strong> <?php echo e($producto->categoria->nombre ?? 'N/A'); ?></li>
                <li><strong>Marca:</strong> <?php echo e($producto->marca->nombre ?? 'N/A'); ?></li>
                <li><strong>Precio base:</strong> $<?php echo e(number_format($producto->precio, 0, ',', '.')); ?></li>
                <li><strong>Precio total:</strong> $<?php echo e(number_format($producto->precio + $variante->precio_adicional, 0, ',', '.')); ?></li>
            </ul>
        </div>

        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #856404;">💡 Recomendaciones</h4>
            <?php if($tipoAlerta === 'agotado'): ?>
                <p style="margin: 0; color: #856404;">
                    <strong>Acción inmediata requerida:</strong> Esta variante se ha agotado completamente. 
                    Se recomienda realizar un pedido de reposición urgente para evitar pérdida de ventas.
                </p>
            <?php elseif($tipoAlerta === 'critico'): ?>
                <p style="margin: 0; color: #856404;">
                    <strong>Acción urgente:</strong> El stock está en niveles críticos. 
                    Se recomienda realizar un pedido de reposición en las próximas 24-48 horas.
                </p>
            <?php else: ?>
                <p style="margin: 0; color: #856404;">
                    <strong>Monitoreo:</strong> El stock está por debajo del mínimo recomendado. 
                    Se recomienda planificar una reposición en los próximos días.
                </p>
            <?php endif; ?>
        </div>

        <div class="actions">
            <a href="<?php echo e($productoUrl); ?>" class="btn">Ver Producto</a>
            <a href="<?php echo e(route('admin.inventario.variantes.reporte')); ?>" class="btn btn-secondary">Reporte de Inventario</a>
        </div>

        <div class="footer">
            <p>Este es un mensaje automático del sistema de inventario de 4GMovil S.A.S</p>
            <p>Fecha: <?php echo e(now()->format('d/m/Y H:i:s')); ?></p>
            <p>Si tienes alguna pregunta, contacta al equipo de administración.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Proyecto V11.3\4GMovil\resources\views/correo/stock-bajo-variante.blade.php ENDPATH**/ ?>