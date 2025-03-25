<?php

declare(strict_types=1);

use Slim\App;
use App\Application\Controller\ServiceController;
use App\Application\Controller\HealthCheckController;
use App\Application\Controller\SwaggerController;

return function (App $app) {
    // Service route for PDF generation
    $app->get('/service', [ServiceController::class, 'generatePdf']);

    // Health check route
    $app->get('/health', [HealthCheckController::class, 'check']);

    // Swagger documentation
    $app->get('/api/docs', [SwaggerController::class, 'index']);
    $app->get('/api/docs/json', [SwaggerController::class, 'json']);
};