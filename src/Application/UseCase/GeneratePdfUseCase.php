<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\Service\AuthServiceInterface;
use App\Domain\Service\NotificationServiceInterface;
use App\Domain\Service\PdfGeneratorServiceInterface;
use Psr\Log\LoggerInterface;

class GeneratePdfUseCase
{
    private TaskRepositoryInterface $taskRepository;
    private AuthServiceInterface $authService;
    private PdfGeneratorServiceInterface $pdfGenerator;
    private NotificationServiceInterface $notificationService;
    private LoggerInterface $logger;

    public function __construct(
        TaskRepositoryInterface $taskRepository,
        AuthServiceInterface $authService,
        PdfGeneratorServiceInterface $pdfGenerator,
        NotificationServiceInterface $notificationService,
        LoggerInterface $logger
    ) {
        $this->taskRepository = $taskRepository;
        $this->authService = $authService;
        $this->pdfGenerator = $pdfGenerator;
        $this->notificationService = $notificationService;
        $this->logger = $logger;
    }

    /**
     * Execute the use case to generate a PDF from tasks
     *
     * @param string $username The username
     * @param string $password The password
     * @return string The generated PDF content
     * @throws \Exception If the operation fails
     */
    public function execute(string $username, string $password): string
    {
        $this->logger->info('Starting PDF generation process', [
            'username' => $username,
        ]);

        try {
            // Authenticate with API
            $token = $this->authService->authenticate($username, $password);

            // Fetch tasks
            $tasks = $this->taskRepository->fetchAll($token);

            if (empty($tasks)) {
                throw new \Exception('No tasks found');
            }

            $this->logger->info('Tasks fetched successfully', [
                'count' => count($tasks),
            ]);

            // Generate PDF
            $pdfContent = $this->pdfGenerator->generatePdf($tasks);

            // Send notification
            $this->notificationService->send('PDF generated successfully', [
                'username' => $username,
                'taskCount' => count($tasks),
                'timestamp' => time(),
            ]);

            return $pdfContent;
        } catch (\Exception $e) {
            $this->logger->error('PDF generation failed', [
                'error' => $e->getMessage(),
                'username' => $username,
            ]);

            // Notify about failure
            $this->notificationService->send('PDF generation failed', [
                'username' => $username,
                'error' => $e->getMessage(),
                'timestamp' => time(),
            ]);

            throw $e;
        }
    }
}