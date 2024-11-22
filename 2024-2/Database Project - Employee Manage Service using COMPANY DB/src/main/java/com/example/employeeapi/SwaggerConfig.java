package com.example.employeeapi;

import io.swagger.v3.oas.models.OpenAPI;
import io.swagger.v3.oas.models.info.Info;
import io.swagger.v3.parser.OpenAPIV3Parser;
import io.swagger.v3.parser.core.models.SwaggerParseResult;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.core.io.Resource;
import org.springframework.core.io.ResourceLoader;

import java.io.IOException;
import java.io.InputStream;
import java.nio.charset.StandardCharsets;

@Configuration
public class SwaggerConfig {

    private final ResourceLoader resourceLoader;

    public SwaggerConfig(ResourceLoader resourceLoader) {
        this.resourceLoader = resourceLoader;
    }

    @Bean
    public OpenAPI customOpenAPI() throws IOException {
        OpenAPI openAPI = new OpenAPI().info(new Info().title("Your API").version("1.0"));

        // JSON 파일을 Resource로 불러오기
        Resource resource = resourceLoader.getResource("classpath:static/openapi.json");
        try (InputStream inputStream = resource.getInputStream()) {
            // InputStream을 사용하여 JSON 파일 내용 읽기
            String customSwaggerJson = new String(inputStream.readAllBytes(), StandardCharsets.UTF_8);
            OpenAPIV3Parser parser = new OpenAPIV3Parser();
            SwaggerParseResult result = parser.readContents(customSwaggerJson);

            // Paths 객체를 초기화
            if (openAPI.getPaths() == null) {
                openAPI.setPaths(new io.swagger.v3.oas.models.Paths());
            }

            // Components의 Schemas를 초기화
            if (openAPI.getComponents() == null) {
                openAPI.setComponents(new io.swagger.v3.oas.models.Components());
            }

            // 병합
            if (result.getOpenAPI() != null) {
                openAPI.getPaths().putAll(result.getOpenAPI().getPaths());
                if (result.getOpenAPI().getComponents() != null &&
                        result.getOpenAPI().getComponents().getSchemas() != null) {
                    openAPI.getComponents().setSchemas(
                            result.getOpenAPI().getComponents().getSchemas());
                }
            }
        }
        return openAPI;
    }
}
