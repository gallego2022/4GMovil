package com.movil4g.reportes.dto;

public class PedidosPorEstadoDTO {
    private final Long estadoId;
    private final String estado;
    private final Long cantidad;

    public PedidosPorEstadoDTO(Long estadoId, String estado, Long cantidad) {
        this.estadoId = estadoId;
        this.estado = estado;
        this.cantidad = cantidad;
    }

    public Long getEstadoId() { return estadoId; }
    public String getEstado() { return estado; }
    public Long getCantidad() { return cantidad; }
}
