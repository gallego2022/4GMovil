package com.movil4g.reportes.dto;

import java.math.BigDecimal;

/**
 * DTO para representar un producto destacado por ventas.
 */
public class TopProductoDTO {
    private final Long productoId;
    private final String nombre;
    private final Long cantidadVendida;
    private final BigDecimal ingresos;

    /**
     * Crea una instancia con métricas del producto.
     *
     * @param productoId identificador del producto
     * @param nombre nombre del producto
     * @param cantidadVendida unidades vendidas en el rango
     * @param ingresos ingresos generados en el rango
     */
    public TopProductoDTO(Long productoId, String nombre, Long cantidadVendida, BigDecimal ingresos) {
        this.productoId = productoId;
        this.nombre = nombre;
        this.cantidadVendida = cantidadVendida;
        this.ingresos = ingresos;
    }

    public Long getProductoId() { return productoId; }
    public String getNombre() { return nombre; }
    public Long getCantidadVendida() { return cantidadVendida; }
    public BigDecimal getIngresos() { return ingresos; }
}
