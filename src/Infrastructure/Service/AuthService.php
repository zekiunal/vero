<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Service\AuthServiceInterface;
use App\Domain\Service\HttpClientServiceInterface;
use Predis\Client as RedisClient;

class AuthService implements AuthServiceInterface
{
    private HttpClientServiceInterface $httpClient;
    private RedisClient $redis;
    private string $apiBaseUrl;

    public function __construct(
        HttpClientServiceInterface $httpClient,
        RedisClient $redis,
        string $apiBaseUrl
    ) {
        $this->httpClient = $httpClient;
        $this->redis = $redis;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(string $username, string $password): string
    {
        // Check if token is cached in Redis
        $cacheKey = "auth_token:{$username}";
        $cachedToken = $this->redis->get($cacheKey);

        if ($cachedToken) {
            return $cachedToken;
        }

        // If not cached, authenticate with API
        $url = "{$this->apiBaseUrl}/index.php/login";
        $headers = [
            'Authorization' => 'Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz',
            'Content-Type' => 'application/json',
        ];

        $data = [
            'username' => $username,
            'password' => $password,
        ];

        try {
            $response = $this->httpClient->post($url, $data, $headers);

            if (!isset($response['oauth']['access_token'])) {
                throw new \Exception('Invalid authentication response');
            }

            $accessToken = $response['oauth']['access_token'];
            $expiresIn = $response['oauth']['expires_in'] ?? 1200;

            // Cache token in Redis with expiry
            $this->redis->setex($cacheKey, $expiresIn - 60, $accessToken);

            return $accessToken;
        } catch (\Exception $e) {
            throw new \Exception('Authentication failed: ' . $e->getMessage());
        }
    }
}