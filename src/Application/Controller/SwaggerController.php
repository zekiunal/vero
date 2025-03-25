<?php

declare(strict_types=1);

namespace App\Application\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SwaggerController
{
    /**
     * Display Swagger UI
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response The response
     */
    public function index(Request $request, Response $response): Response
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PDF Service API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.3/swagger-ui.css">
    <style>
        html { box-sizing: border-box; overflow: -moz-scrollbars-vertical; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.3/swagger-ui-bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.3/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "/api/docs/json",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "StandaloneLayout"
            });
            window.ui = ui;
        };
    </script>
</body>
</html>
HTML;

        $response->getBody()->write($html);

        return $response
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }

    /**
     * Return Swagger JSON specification
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response The response
     */
    public function json(Request $request, Response $response): Response
    {
        $host = $request->getUri()->getHost();
        $port = $request->getUri()->getPort();
        $scheme = $request->getUri()->getScheme();

        if ($port && !in_array($port, [80, 443])) {
            $host .= ':' . $port;
        }

        $spec = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'PDF Service API',
                'version' => '1.0.0',
                'description' => 'API for generating PDF documents from tasks data',
            ],
            'servers' => [
                [
                    'url' => $scheme . '://' . $host,
                    'description' => 'Current environment',
                ],
            ],
            'paths' => [
                '/service' => [
                    'get' => [
                        'summary' => 'Generate PDF from tasks data',
                        'description' => 'Authenticates with provided credentials, fetches tasks data from API, and generates a PDF document',
                        'parameters' => [
                            [
                                'name' => 'username',
                                'in' => 'query',
                                'required' => true,
                                'schema' => [
                                    'type' => 'string',
                                ],
                                'description' => 'Username for API authentication',
                            ],
                            [
                                'name' => 'password',
                                'in' => 'query',
                                'required' => true,
                                'schema' => [
                                    'type' => 'string',
                                ],
                                'description' => 'Password for API authentication',
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'PDF document generated successfully',
                                'content' => [
                                    'application/pdf' => [
                                        'schema' => [
                                            'type' => 'string',
                                            'format' => 'binary',
                                        ],
                                    ],
                                ],
                            ],
                            '400' => [
                                'description' => 'Missing required parameters',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'error' => [
                                                    'type' => 'string',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '500' => [
                                'description' => 'Internal server error',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'error' => [
                                                    'type' => 'string',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/health' => [
                    'get' => [
                        'summary' => 'Health check',
                        'description' => 'Check if the service is running properly',
                        'responses' => [
                            '200' => [
                                'description' => 'Service status',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'status' => [
                                                    'type' => 'string',
                                                ],
                                                'timestamp' => [
                                                    'type' => 'integer',
                                                ],
                                                'version' => [
                                                    'type' => 'string',
                                                ],
                                                'checks' => [
                                                    'type' => 'object',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response->getBody()->write(json_encode($spec, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}