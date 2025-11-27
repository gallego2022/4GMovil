package com.movil4g.reportes.controller;

import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
public class HomeController {
    @GetMapping("/")
    public ResponseEntity<String> home() {
        String html = "<!doctype html><html lang=\"es\"><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><title>Reportes Service · Estado</title></head><body style=\"font-family:system-ui;margin:2rem\"><h1>Microservicio de Reportes</h1><p>Esta instancia está en ejecución.</p><p><a href=\"/reportes/ping\">Probar ping</a> · <a href=\"/\">Página estática</a></p></body></html>";
        return ResponseEntity.ok()
                .header(HttpHeaders.CACHE_CONTROL, "no-store")
                .contentType(MediaType.TEXT_HTML)
                .body(html);
    }
}
