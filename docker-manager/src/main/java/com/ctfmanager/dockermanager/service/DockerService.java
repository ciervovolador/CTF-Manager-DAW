package com.ctfmanager.dockermanager.service;

import org.springframework.stereotype.Service;

import java.io.BufferedReader;
import java.io.File;
import java.io.InputStreamReader;

@Service
public class DockerService {

    private final String labPath =
            "C:\\Users\\diego\\Documents\\Proyectos\\TFMMCB25";

    public String ejecutar(String... comando) {

        try {

            ProcessBuilder builder =
                    new ProcessBuilder(comando);

            builder.directory(
                    new File(labPath)
            );

            builder.redirectErrorStream(true);

            Process proceso =
                    builder.start();

            StringBuilder salida =
                    new StringBuilder();

            try (
                BufferedReader reader =
                    new BufferedReader(
                        new InputStreamReader(
                            proceso.getInputStream()
                        )
                    )
            ) {

                String linea;

                while (
                    (linea = reader.readLine()) != null
                ) {

                    salida
                        .append(linea)
                        .append(System.lineSeparator());
                }
            }

            proceso.waitFor();

            return salida.toString();

        } catch (Exception e) {

            return "";
        }
    }
}