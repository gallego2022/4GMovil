package com.movil4g.reportes.dto;

import java.math.BigDecimal;
import java.util.Map;

/**
 * DTO con el resumen de ventas para un rango de fechas.
 */
public class VentasResumenDTO {
    private final BigDecimal totalVentas;
    private final Long totalPedidosCompletados;
    private final BigDecimal ticketPromedio;
    private final Map<String, BigDecimal> ingresosDiarios;

    /**
     * Constructor del resumen de ventas.
     *
     * @param totalVentas suma de montos completados
     * @param totalPedidosCompletados cantidad de pedidos completados
     * @param ticketPromedio promedio por pedido
     * @param ingresosDiarios mapa de ingresos por día (YYYY-MM-DD)
     */
    public VentasResumenDTO(BigDecimal totalVentas, Long totalPedidosCompletados, BigDecimal ticketPromedio, Map<String, BigDecimal> ingresosDiarios) {
        this.totalVentas = totalVentas;
        this.totalPedidosCompletados = totalPedidosCompletados;
        this.ticketPromedio = ticketPromedio;
        this.ingresosDiarios = ingresosDiarios;
    }

    public BigDecimal getTotalVentas() { return totalVentas; }
    public Long getTotalPedidosCompletados() { return totalPedidosCompletados; }
    public BigDecimal getTicketPromedio() { return ticketPromedio; }
    public Map<String, BigDecimal> getIngresosDiarios() { return ingresosDiarios; }
}
