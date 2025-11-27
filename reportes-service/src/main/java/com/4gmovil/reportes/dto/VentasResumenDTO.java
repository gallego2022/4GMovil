package com.4gmovil.reportes.dto;

import java.math.BigDecimal;
import java.util.Map;

public class VentasResumenDTO {
    private final BigDecimal totalVentas;
    private final Long totalPedidosCompletados;
    private final BigDecimal ticketPromedio;
    private final Map<String, BigDecimal> ingresosDiarios;

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
