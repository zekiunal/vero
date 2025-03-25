<?php

declare(strict_types=1);

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use App\Application\Middleware\JsonBodyParserMiddleware;

return function (App $app) {
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add JSON content type to responses
    $app->add(new JsonBodyParserMiddleware());

    // Add Error Middleware
    $errorMiddleware = $app->addErrorMiddleware(
        (bool)($_ENV['DISPLAY_ERROR_DETAILS'] ?? true),
        (bool)($_ENV['LOG_ERRORS'] ?? true),
        (bool)($_ENV['LOG_ERROR_DETAILS'] ?? true)
    );

    return $errorMiddleware;
};