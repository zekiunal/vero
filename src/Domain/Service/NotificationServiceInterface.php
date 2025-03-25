<?php

declare(strict_types=1);

namespace App\Domain\Service;

interface NotificationServiceInterface
{
    /**
     * Send a notification
     *
     * @param string $message The notification message
     * @param array $data Additional data for the notification
     * @return bool Whether the notification was sent successfully
     */
    public function send(string $message, array $data = []): bool;
}