package com.4gmovil.reportes.controller;

import com.4gmovil.reportes.dto.PedidosPorEstadoDTO;
import com.4gmovil.reportes.dto.TopProductoDTO;
import com.4gmovil.reportes.dto.VentasResumenDTO;
import com.4gmovil.reportes.service.ReportesService;
import org.springframework.format.annotation.DateTimeFormat;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import java.time.LocalDate;
import java.util.List;

@RestController
@RequestMapping("/reportes")
public class ReportesController {

    private final ReportesService service;

    public ReportesController(ReportesService service) {
        this.service = service;
    }

    @GetMapping("/ventas/resumen")
    public VentasResumenDTO ventasResumen(
            @RequestParam("desde") String desde,
            @RequestParam("hasta") String hasta) {
        LocalDate d = LocalDate.parse(desde);
        LocalDate h = LocalDate.parse(hasta);
        return service.obtenerVentasResumen(d, h);
    }

    @GetMapping("/ventas/top-productos")
    public List<TopProductoDTO> topProductos(
            @RequestParam("desde") String desde,
            @RequestParam("hasta") String hasta,
            @RequestParam(name = "limit", defaultValue = "10") Integer limit,
            @RequestParam(name = "categoriaId", required = false) Long categoriaId,
            @RequestParam(name = "marcaId", required = false) Long marcaId) {
        LocalDate d = LocalDate.parse(desde);
        LocalDate h = LocalDate.parse(hasta);
        return service.obtenerTopProductos(d, h, limit, categoriaId, marcaId);
    }

    @GetMapping("/pedidos/por-estado")
    public List<PedidosPorEstadoDTO> pedidosPorEstado(
            @RequestParam("desde") String desde,
            @RequestParam("hasta") String hasta) {
        LocalDate d = LocalDate.parse(desde);
        LocalDate h = LocalDate.parse(hasta);
        return service.obtenerPedidosPorEstado(d, h);
    }

}
