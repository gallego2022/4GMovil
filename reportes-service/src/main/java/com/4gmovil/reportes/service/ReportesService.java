package com.movil4g.reportes.service;

import com.movil4g.reportes.dto.PedidosPorEstadoDTO;
import com.movil4g.reportes.dto.TopProductoDTO;
import com.movil4g.reportes.dto.VentasResumenDTO;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Service
public class ReportesService {
    private final JdbcTemplate jdbcTemplate;

    public ReportesService(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public VentasResumenDTO obtenerVentasResumen(LocalDate desde, LocalDate hasta) {
        BigDecimal totalVentas = jdbcTemplate.queryForObject(
                "SELECT COALESCE(SUM(monto),0) FROM pagos WHERE estado='completado' AND fecha_pago BETWEEN ? AND ?",
                BigDecimal.class, desde.atStartOfDay(), hasta.plusDays(1).atStartOfDay());

        Long totalPedidosCompletados = jdbcTemplate.queryForObject(
                "SELECT COALESCE(COUNT(DISTINCT pedido_id),0) FROM pagos WHERE estado='completado' AND fecha_pago BETWEEN ? AND ?",
                Long.class, desde.atStartOfDay(), hasta.plusDays(1).atStartOfDay());

        BigDecimal ticketPromedio = totalPedidosCompletados != null && totalPedidosCompletados > 0
                ? totalVentas.divide(BigDecimal.valueOf(totalPedidosCompletados), 2, java.math.RoundingMode.HALF_UP)
                : BigDecimal.ZERO;

        List<Map<String, Object>> diarios = jdbcTemplate.queryForList(
                "SELECT DATE(fecha_pago) dia, COALESCE(SUM(monto),0) monto FROM pagos WHERE estado='completado' AND fecha_pago BETWEEN ? AND ? GROUP BY DATE(fecha_pago) ORDER BY DATE(fecha_pago)",
                desde.atStartOfDay(), hasta.plusDays(1).atStartOfDay());

        Map<String, BigDecimal> ingresosDiarios = new HashMap<>();
        for (Map<String, Object> row : diarios) {
            ingresosDiarios.put(row.get("dia").toString(), new BigDecimal(row.get("monto").toString()));
        }

        return new VentasResumenDTO(totalVentas, totalPedidosCompletados, ticketPromedio, ingresosDiarios);
    }

    public List<TopProductoDTO> obtenerTopProductos(LocalDate desde, LocalDate hasta, Integer limit, Long categoriaId, Long marcaId) {
        StringBuilder sql = new StringBuilder();
        sql.append("SELECT dp.producto_id, p.nombre_producto, COALESCE(SUM(dp.cantidad),0) cantidad, COALESCE(SUM(dp.subtotal),0) ingresos ");
        sql.append("FROM detalles_pedido dp ");
        sql.append("JOIN pedidos ped ON ped.pedido_id = dp.pedido_id ");
        sql.append("JOIN productos p ON p.producto_id = dp.producto_id ");
        sql.append("WHERE ped.fecha_pedido BETWEEN ? AND ? ");
        if (categoriaId != null) {
            sql.append("AND p.categoria_id = ? ");
        }
        if (marcaId != null) {
            sql.append("AND p.marca_id = ? ");
        }
        sql.append("GROUP BY dp.producto_id, p.nombre_producto ");
        sql.append("ORDER BY ingresos DESC, cantidad DESC ");
        sql.append("LIMIT ?");

        java.util.List<Object> params = new java.util.ArrayList<>();
        params.add(desde.atStartOfDay());
        params.add(hasta.plusDays(1).atStartOfDay());
        if (categoriaId != null) params.add(categoriaId);
        if (marcaId != null) params.add(marcaId);
        params.add(limit);

        return jdbcTemplate.query(
                sql.toString(),
                (rs, i) -> new TopProductoDTO(
                        rs.getLong("producto_id"),
                        rs.getString("nombre_producto"),
                        rs.getLong("cantidad"),
                        rs.getBigDecimal("ingresos")
                ),
                params.toArray()
        );
    }

    public List<PedidosPorEstadoDTO> obtenerPedidosPorEstado(LocalDate desde, LocalDate hasta) {
        return jdbcTemplate.query(
                "SELECT ep.estado_id, ep.nombre estado, COALESCE(COUNT(ped.pedido_id),0) cantidad " +
                        "FROM pedidos ped " +
                        "JOIN estados_pedido ep ON ep.estado_id = ped.estado_id " +
                        "WHERE ped.fecha_pedido BETWEEN ? AND ? " +
                        "GROUP BY ep.estado_id, ep.nombre " +
                        "ORDER BY cantidad DESC",
                (rs, i) -> new PedidosPorEstadoDTO(
                        rs.getLong("estado_id"),
                        rs.getString("estado"),
                        rs.getLong("cantidad")
                ),
                desde.atStartOfDay(), hasta.plusDays(1).atStartOfDay()
        );
    }


}
