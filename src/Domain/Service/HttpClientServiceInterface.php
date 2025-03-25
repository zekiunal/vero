<?php

declare(strict_types=1);

namespace App\Domain\Service;

interface HttpClientServiceInterface
{
    /**
     * Make a GET request
     *
     * @param string $url The URL to request
     * @param array $headers Optional headers
     * @return array The response data as an array
     */
    public function get(string $url, array $headers = []): array;

    /**
     * Make a POST request
     *
     * @param string $url The URL to request
     * @param array $data The data to send
     * @param array $headers Optional headers
     * @return array The response data as an array
     */
    public function post(string $url, array $data, array $headers = []): array;
}