<?php

declare(strict_types=1);

namespace App\Application\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Predis\Client as RedisClient;

class HealthCheckController
{
    private RedisClient $redis;
    private string $apiVersion;

    public function __construct(RedisClient $redis)
    {
        $this->redis = $redis;
        $this->apiVersion = $_ENV['API_VERSION'] ?? 'dev';
    }

    /**
     * Health check endpoint
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response The response
     */
    public function check(Request $request, Response $response): Response
    {
        $status = [
            'status' => 'ok',
            'timestamp' => time(),
            'version' => $this->apiVersion,
            'checks' => [
                'redis' => $this->checkRedis(),
            ],
        ];

        $response->getBody()->write(json_encode($status));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Check Redis connection
     *
     * @return array The check result
     */
    private function checkRedis(): array
    {
        try {
            $pingResult = $this->redis->ping();

            return [
                'status' => $pingResult === 'PONG' ? 'ok' : 'error',
                'message' => $pingResult === 'PONG' ? 'Connected' : 'Unexpected response',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }
}