<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\Service\AuthServiceInterface;
use App\Domain\Service\HttpClientServiceInterface;

class TaskRepository implements TaskRepositoryInterface
{
    private HttpClientServiceInterface $httpClient;
    private AuthServiceInterface $authService;
    private string $apiBaseUrl;

    public function __construct(
        HttpClientServiceInterface $httpClient,
        AuthServiceInterface $authService,
        string $apiBaseUrl
    ) {
        $this->httpClient = $httpClient;
        $this->authService = $authService;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(string $authToken): array
    {
        $url = "{$this->apiBaseUrl}/dev/index.php/v1/tasks/select";
        $headers = [
            'Authorization' => "Bearer {$authToken}",
            'Content-Type' => 'application/json',
        ];

        $response = $this->httpClient->get($url, $headers);

        $tasks = [];

        foreach ($response as $taskData) {
            // Ensure only tasks with required fields are included
            if (
                isset($taskData['task']) &&
                isset($taskData['title']) &&
                isset($taskData['description']) &&
                isset($taskData['colorCode'])
            ) {
                $tasks[] = Task::fromArray($taskData);

            }
        }

        return $tasks;
    }
}