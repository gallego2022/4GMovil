package com.movil4g.reportes;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

@SpringBootApplication
/**
 * Aplicación principal del microservicio de reportes.
 *
 * Este servicio expone endpoints REST para consultar métricas
 * de ventas, productos y pedidos, utilizando consultas SQL
 * a la base de datos transaccional.
 */
public class ReportesApplication {
    /**
     * Punto de entrada del microservicio.
     *
     * @param args argumentos de arranque
     */
    public static void main(String[] args) {
        SpringApplication.run(ReportesApplication.class, args);
    }
}
