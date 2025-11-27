package com.movil4g.reportes.dto;

/**
 * DTO para representar la cantidad de pedidos por estado.
 */
public class PedidosPorEstadoDTO {
    private final Long estadoId;
    private final String estado;
    private final Long cantidad;

    /**
     * Crea una instancia con el estado y su cantidad.
     *
     * @param estadoId identificador del estado
     * @param estado nombre del estado
     * @param cantidad número de pedidos en el estado
     */
    public PedidosPorEstadoDTO(Long estadoId, String estado, Long cantidad) {
        this.estadoId = estadoId;
        this.estado = estado;
        this.cantidad = cantidad;
    }

    public Long getEstadoId() { return estadoId; }
    public String getEstado() { return estado; }
    public Long getCantidad() { return cantidad; }
}
