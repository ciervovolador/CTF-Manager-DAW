package com.ctfmanager.dockermanager.controller;

import com.ctfmanager.dockermanager.service.DockerService;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.Map;

@RestController
@RequestMapping("/lab")
public class LabController {

    private final DockerService dockerService;

    public LabController(
            DockerService dockerService
    ) {
        this.dockerService = dockerService;
    }

    @GetMapping("/status")
    public Map<String, Object> status() {

        String salida =
                dockerService.ejecutar(
                        "docker",
                        "compose",
                        "ps",
                        "--status",
                        "running",
                        "-q"
                );

        boolean running =
                !salida.trim().isEmpty();

        return Map.of(
                "running", running,
                "message",
                running
                    ? "Laboratorio activo"
                    : "Laboratorio detenido"
        );
    }
}