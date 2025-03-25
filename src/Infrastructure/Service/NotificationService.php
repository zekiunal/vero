<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Service\NotificationServiceInterface;
use Predis\Client as RedisClient;
use Psr\Log\LoggerInterface;

class NotificationService implements NotificationServiceInterface
{
    private RedisClient $redis;
    private LoggerInterface $logger;
    private string $channel = 'notifications';

    public function __construct(RedisClient $redis, LoggerInterface $logger)
    {
        $this->redis = $redis;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function send(string $message, array $data = []): bool
    {
        try {
            $payload = json_encode([
                'message' => $message,
                'data' => $data,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);

            $this->redis->publish($this->channel, $payload);

            $this->logger->info('Notification sent', [
                'message' => $message,
                'channel' => $this->channel,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to send notification', [
                'message' => $message,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}