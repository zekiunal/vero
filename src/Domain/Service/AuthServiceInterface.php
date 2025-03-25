<?php

declare(strict_types=1);

namespace App\Domain\Service;

interface AuthServiceInterface
{
    /**
     * Authenticate with the API using username and password
     *
     * @param string $username The username
     * @param string $password The password
     * @return string The access token
     * @throws \Exception If authentication fails
     */
    public function authenticate(string $username, string $password): string;
}