<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonBodyParserMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            $contents = $request->getBody()->getContents();
            $request->getBody()->rewind();
            $data = json_decode($contents, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $data = null;
            }

            $request = $request->withParsedBody($data);
        }

        return $handler->handle($request);
    }
}