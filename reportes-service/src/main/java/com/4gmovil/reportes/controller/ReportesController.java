package com.movil4g.reportes.controller;

import com.movil4g.reportes.dto.PedidosPorEstadoDTO;
import com.movil4g.reportes.dto.TopProductoDTO;
import com.movil4g.reportes.dto.VentasResumenDTO;
import com.movil4g.reportes.service.ReportesService;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import jakarta.servlet.http.HttpServletRequest;
import org.springframework.web.bind.annotation.RestController;

import java.time.LocalDate;
import java.util.List;

@RestController
@RequestMapping("/reportes")
/**
 * Controlador REST para endpoints del módulo de reportes.
 *
 * Expone operaciones para consultar resumen de ventas,
 * productos más vendidos y distribución de pedidos por estado.
 */
public class ReportesController {

    private final ReportesService service;

    public ReportesController(ReportesService service) {
        this.service = service;
    }

    @GetMapping("/ventas/resumen")
    /**
     * Obtiene el resumen de ventas en el rango indicado.
     *
     * @param desde fecha inicial (ISO-8601, ej. 2025-01-01)
     * @param hasta fecha final (ISO-8601)
     * @return métricas agregadas de ventas
     */
    public VentasResumenDTO ventasResumen(HttpServletRequest request) {
        String desde = request.getParameter("desde");
        String hasta = request.getParameter("hasta");
        LocalDate now = LocalDate.now();
        LocalDate d = (desde == null || desde.isBlank()) ? now.minusDays(30) : LocalDate.parse(desde);
        LocalDate h = (hasta == null || hasta.isBlank()) ? now : LocalDate.parse(hasta);
        return service.obtenerVentasResumen(d, h);
    }

    @GetMapping("/ventas/top-productos")
    /**
     * Lista los productos con mayores ingresos/cantidad en el rango.
     *
     * @param desde fecha inicial (ISO-8601)
     * @param hasta fecha final (ISO-8601)
     * @param limit cantidad máxima a devolver
     * @param categoriaId filtro opcional por categoría
     * @param marcaId filtro opcional por marca
     * @return lista ordenada de productos destacados
     */
    public List<TopProductoDTO> topProductos(HttpServletRequest request) {
        String desde = request.getParameter("desde");
        String hasta = request.getParameter("hasta");
        String limitStr = request.getParameter("limit");
        String categoriaStr = request.getParameter("categoriaId");
        String marcaStr = request.getParameter("marcaId");
        Integer limit = (limitStr == null || limitStr.isBlank()) ? 10 : Integer.parseInt(limitStr);
        Long categoriaId = (categoriaStr == null || categoriaStr.isBlank()) ? null : Long.parseLong(categoriaStr);
        Long marcaId = (marcaStr == null || marcaStr.isBlank()) ? null : Long.parseLong(marcaStr);
        LocalDate now = LocalDate.now();
        LocalDate d = (desde == null || desde.isBlank()) ? now.minusDays(30) : LocalDate.parse(desde);
        LocalDate h = (hasta == null || hasta.isBlank()) ? now : LocalDate.parse(hasta);
        return service.obtenerTopProductos(d, h, limit, categoriaId, marcaId);
    }

    @GetMapping("/pedidos/por-estado")
    /**
     * Devuelve la cantidad de pedidos por estado en el rango.
     *
     * @param desde fecha inicial (ISO-8601)
     * @param hasta fecha final (ISO-8601)
     * @return distribución de pedidos por estado
     */
    public List<PedidosPorEstadoDTO> pedidosPorEstado(HttpServletRequest request) {
        String desde = request.getParameter("desde");
        String hasta = request.getParameter("hasta");
        LocalDate now = LocalDate.now();
        LocalDate d = (desde == null || desde.isBlank()) ? now.minusDays(30) : LocalDate.parse(desde);
        LocalDate h = (hasta == null || hasta.isBlank()) ? now : LocalDate.parse(hasta);
        return service.obtenerPedidosPorEstado(d, h);
    }

    @GetMapping("/ping")
    /**
     * Endpoint simple para verificación rápida del servicio.
     *
     * @return texto "OK" si el controlador está accesible
     */
    public String ping() {
        return "OK";
    }

}
