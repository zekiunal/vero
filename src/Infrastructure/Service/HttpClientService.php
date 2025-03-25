<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Service\HttpClientServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class HttpClientService implements HttpClientServiceInterface
{
    private Client $client;
    private LoggerInterface $logger;

    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function get(string $url, array $headers = []): array
    {
        try {
            $response = $this->client->get($url, [
                'headers' => $headers,
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('Failed to parse JSON response', [
                    'url' => $url,
                    'error' => json_last_error_msg(),
                    'response' => substr($body, 0, 500),
                ]);
                throw new \Exception('Invalid JSON response');
            }

            return $data;
        } catch (GuzzleException $e) {
            $this->logger->error('HTTP request failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('HTTP request failed: ' . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function post(string $url, array $data, array $headers = []): array
    {
        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json' => $data,
            ]);

            $body = $response->getBody()->getContents();
            $responseData = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('Failed to parse JSON response', [
                    'url' => $url,
                    'error' => json_last_error_msg(),
                    'response' => substr($body, 0, 500),
                ]);
                throw new \Exception('Invalid JSON response');
            }

            return $responseData;
        } catch (GuzzleException $e) {
            $this->logger->error('HTTP request failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('HTTP request failed: ' . $e->getMessage());
        }
    }
}