<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\RedisCacheService;
use Illuminate\Support\Collection;

class OptimizedStockAlertService
{
    protected $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Obtiene alertas de stock optimizadas agrupando variantes por producto
     */
    public function getOptimizedStockAlerts(): array
    {
        return $this->cacheService->remember('alertas:optimizadas', 900, function () {
            $alertas = [
                'productos_criticos' => $this->getProductosCriticos(),
                'productos_stock_bajo' => $this->getProductosStockBajo(),
                'variantes_agotadas' => $this->getVariantesAgotadas(),
                'total_alertas' => 0
            ];

            $alertas['total_alertas'] = 
                $alertas['productos_criticos']->count() + 
                $alertas['productos_stock_bajo']->count() + 
                $alertas['variantes_agotadas']->count();

            return $alertas;
        });
    }

    /**
     * Obtiene productos con stock crítico (agrupando variantes)
     */
    private function getProductosCriticos(): Collection
    {
        return Producto::with(['variantes', 'categoria', 'marca', 'imagenes'])
            ->where('disponible', true)
            ->get()
            ->filter(function ($producto) {
                // Si el producto tiene variantes, verificar si todas están críticas
                if ($producto->variantes->isNotEmpty()) {
                    return $this->tieneVariantesCriticas($producto);
                }
                
                // Si no tiene variantes, verificar stock del producto principal
                return $this->esStockCritico($producto->stock, $producto->stock_inicial);
            })
            ->map(function ($producto) {
                return $this->enriquecerProductoConAlertas($producto);
            });
    }

    /**
     * Obtiene productos con stock bajo (agrupando variantes)
     */
    private function getProductosStockBajo(): Collection
    {
        return Producto::with(['variantes', 'categoria', 'marca', 'imagenes'])
            ->where('disponible', true)
            ->get()
            ->filter(function ($producto) {
                // Si el producto tiene variantes, verificar si tiene stock bajo
                if ($producto->variantes->isNotEmpty()) {
                    return $this->tieneVariantesStockBajo($producto);
                }
                
                // Si no tiene variantes, verificar stock del producto principal
                return $this->esStockBajo($producto->stock, $producto->stock_inicial);
            })
            ->map(function ($producto) {
                return $this->enriquecerProductoConAlertas($producto);
            });
    }

    /**
     * Obtiene variantes completamente agotadas
     */
    private function getVariantesAgotadas(): Collection
    {
        return VarianteProducto::with(['producto.categoria', 'producto.marca', 'producto.imagenes'])
            ->where('disponible', true)
            ->where('stock', 0)
            ->get()
            ->map(function ($variante) {
                return [
                    'variante' => $variante,
                    'producto' => $variante->producto,
                    'tipo_alerta' => 'agotado',
                    'stock_actual' => 0,
                    'stock_minimo' => $this->calcularStockMinimo($variante->producto->stock_inicial),
                    'porcentaje' => 0
                ];
            });
    }

    /**
     * Verifica si un producto tiene variantes con stock crítico
     */
    private function tieneVariantesCriticas(Producto $producto): bool
    {
        return $producto->variantes->filter(function ($variante) use ($producto) {
            return $this->esStockCritico($variante->stock, $producto->stock_inicial);
        })->isNotEmpty();
    }

    /**
     * Verifica si un producto tiene variantes con stock bajo
     */
    private function tieneVariantesStockBajo(Producto $producto): bool
    {
        return $producto->variantes->filter(function ($variante) use ($producto) {
            return $this->esStockBajo($variante->stock, $producto->stock_inicial);
        })->isNotEmpty();
    }

    /**
     * Verifica si el stock es crítico
     */
    private function esStockCritico(int $stock, ?int $stockInicial): bool
    {
        if ($stockInicial === null || $stockInicial <= 0) {
            return $stock <= 5;
        }
        
        $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
        return $stock <= $umbralCritico && $stock > 0;
    }

    /**
     * Verifica si el stock está bajo
     */
    private function esStockBajo(int $stock, ?int $stockInicial): bool
    {
        if ($stockInicial === null || $stockInicial <= 0) {
            return $stock <= 10 && $stock > 5;
        }
        
        $umbralBajo = (int) ceil(($stockInicial * 60) / 100);
        $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
        return $stock <= $umbralBajo && $stock > $umbralCritico;
    }

    /**
     * Enriquece un producto con información de alertas
     */
    private function enriquecerProductoConAlertas(Producto $producto): array
    {
        $variantesConProblemas = $producto->variantes->filter(function ($variante) use ($producto) {
            return $this->esStockCritico($variante->stock, $producto->stock_inicial) ||
                   $this->esStockBajo($variante->stock, $producto->stock_inicial);
        });

        return [
            'producto' => $producto,
            'variantes_problematicas' => $variantesConProblemas,
            'total_variantes_problematicas' => $variantesConProblemas->count(),
            'tipo_alerta' => $this->determinarTipoAlerta($producto, $variantesConProblemas),
            'stock_actual' => $producto->stock,
            'stock_minimo' => $this->calcularStockMinimo($producto->stock_inicial),
            'porcentaje' => $this->calcularPorcentaje($producto->stock, $producto->stock_inicial)
        ];
    }

    /**
     * Determina el tipo de alerta para un producto
     */
    private function determinarTipoAlerta(Producto $producto, Collection $variantesProblema): string
    {
        if ($producto->variantes->isNotEmpty()) {
            $tieneCriticas = $variantesProblema->filter(function ($variante) use ($producto) {
                return $this->esStockCritico($variante->stock, $producto->stock_inicial);
            })->isNotEmpty();

            return $tieneCriticas ? 'critico' : 'bajo';
        }

        return $this->esStockCritico($producto->stock, $producto->stock_inicial) ? 'critico' : 'bajo';
    }

    /**
     * Calcula el stock mínimo
     */
    private function calcularStockMinimo(?int $stockInicial): int
    {
        if ($stockInicial === null || $stockInicial <= 0) {
            return 10;
        }
        
        return (int) ceil(($stockInicial * 20) / 100);
    }

    /**
     * Calcula el porcentaje de stock
     */
    private function calcularPorcentaje(int $stock, ?int $stockInicial): float
    {
        if ($stockInicial === null || $stockInicial <= 0) {
            return 0;
        }
        
        return round(($stock / $stockInicial) * 100, 1);
    }

    /**
     * Obtiene las variantes problemáticas de un producto específico
     */
    public function getVariantesProblematicas(int $productoId): Collection
    {
        $producto = Producto::with(['variantes'])->findOrFail($productoId);
        
        return $producto->variantes->filter(function ($variante) use ($producto) {
            return $this->esStockCritico($variante->stock, $producto->stock_inicial) ||
                   $this->esStockBajo($variante->stock, $producto->stock_inicial);
        })->map(function ($variante) use ($producto) {
            return [
                'variante' => $variante,
                'tipo_alerta' => $this->esStockCritico($variante->stock, $producto->stock_inicial) ? 'critico' : 'bajo',
                'stock_actual' => $variante->stock,
                'stock_minimo' => $this->calcularStockMinimo($producto->stock_inicial),
                'porcentaje' => $this->calcularPorcentaje($variante->stock, $producto->stock_inicial)
            ];
        });
    }
}
