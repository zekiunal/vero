<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\UseCase\GeneratePdfUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class ServiceController
{
    private GeneratePdfUseCase $generatePdfUseCase;
    private LoggerInterface $logger;

    public function __construct(
        GeneratePdfUseCase $generatePdfUseCase,
        LoggerInterface $logger
    ) {
        $this->generatePdfUseCase = $generatePdfUseCase;
        $this->logger = $logger;
    }

    /**
     * Generate PDF from API data
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response The response with PDF
     */
    public function generatePdf(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        if (!isset($params['username']) || !isset($params['password'])) {
            $this->logger->warning('Missing credentials', [
                'params' => array_keys($params),
            ]);

            $response->getBody()->write(json_encode([
                'error' => 'Missing required parameters: username and password',
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $username = $params['username'];
        $password = $params['password'];

        try {
            $pdfContent = $this->generatePdfUseCase->execute($username, $password);

            $response->getBody()->write($pdfContent);

            return $response
                ->withHeader('Content-Type', 'application/pdf')
                ->withHeader('Content-Disposition', 'attachment; filename="tasks.pdf"')
                ->withStatus(200);
        } catch (\Exception $e) {
            $this->logger->error('Error generating PDF', [
                'error' => $e->getMessage(),
            ]);

            $response->getBody()->write(json_encode([
                'error' => 'Failed to generate PDF: ' . $e->getMessage(),
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}