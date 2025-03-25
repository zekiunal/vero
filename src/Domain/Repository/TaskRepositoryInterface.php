<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Task;

interface TaskRepositoryInterface
{
    /**
     * Fetch all tasks from the API
     *
     * @param string $authToken The authentication token
     * @return Task[] Array of Task entities
     */
    public function fetchAll(string $authToken): array;
}