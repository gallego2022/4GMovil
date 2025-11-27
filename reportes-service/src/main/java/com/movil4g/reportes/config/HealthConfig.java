package com.movil4g.reportes.config;

import org.springframework.boot.actuate.health.Health;
import org.springframework.boot.actuate.health.HealthIndicator;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class HealthConfig {
    @Bean
    public HealthIndicator appHealthIndicator() {
        return () -> Health.up().withDetail("service", "reportes").build();
    }
}
