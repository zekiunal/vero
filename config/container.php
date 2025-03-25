<?php

declare(strict_types=1);

use App\Domain\Repository\TaskRepositoryInterface;
use App\Infrastructure\Repository\TaskRepository;
use App\Infrastructure\Service\AuthService;
use App\Domain\Service\AuthServiceInterface;
use App\Infrastructure\Service\PdfGeneratorService;
use App\Domain\Service\PdfGeneratorServiceInterface;
use App\Infrastructure\Service\HttpClientService;
use App\Domain\Service\HttpClientServiceInterface;
use App\Infrastructure\Service\NotificationService;
use App\Domain\Service\NotificationServiceInterface;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Predis\Client as RedisClient;

return [
    LoggerInterface::class => function (ContainerInterface $c) {
        $logger = new Logger('app');

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler('php://stdout');
        $logger->pushHandler($handler);

        return $logger;
    },

    Client::class => function (ContainerInterface $c) {
        return new Client([
            'timeout' => 5.0,
        ]);
    },

    RedisClient::class => function (ContainerInterface $c) {
        return new RedisClient([
            'scheme' => 'tcp',
            'host' => $_ENV['REDIS_HOST'] ?? 'redis',
            'port' => $_ENV['REDIS_PORT'] ?? 6379,
        ]);
    },

    HttpClientServiceInterface::class => function (ContainerInterface $c) {
        return new HttpClientService(
            $c->get(Client::class),
            $c->get(LoggerInterface::class)
        );
    },

    AuthServiceInterface::class => function (ContainerInterface $c) {
        return new AuthService(
            $c->get(HttpClientServiceInterface::class),
            $c->get(RedisClient::class),
            $_ENV['API_BASE_URL'] ?? 'https://api.baubuddy.de'
        );
    },

    TaskRepositoryInterface::class => function (ContainerInterface $c) {
        return new TaskRepository(
            $c->get(HttpClientServiceInterface::class),
            $c->get(AuthServiceInterface::class),
            $_ENV['API_BASE_URL'] ?? 'https://api.baubuddy.de'
        );
    },

    PdfGeneratorServiceInterface::class => function (ContainerInterface $c) {
        return new PdfGeneratorService(
            $c->get(LoggerInterface::class)
        );
    },

    NotificationServiceInterface::class => function (ContainerInterface $c) {
        return new NotificationService(
            $c->get(RedisClient::class),
            $c->get(LoggerInterface::class)
        );
    },
];