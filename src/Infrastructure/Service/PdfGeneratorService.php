<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Entity\Task;
use App\Domain\Service\PdfGeneratorServiceInterface;
use Psr\Log\LoggerInterface;

class PdfGeneratorService implements PdfGeneratorServiceInterface
{
    private LoggerInterface $logger;
    private string $tmpDir;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->tmpDir = sys_get_temp_dir();
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function generatePdf(array $tasks): string
    {
        // Generate HTML content
        $htmlContent = $this->generateHtml($tasks);

        // Create temporary files
        $htmlFile = tempnam($this->tmpDir, 'task_html_') . '.html';
        $pdfFile = tempnam($this->tmpDir, 'task_pdf_') . '.pdf';

        file_put_contents($htmlFile, $htmlContent);

        // Generate PDF using wkhtmltopdf
        $command = sprintf(
            'wkhtmltopdf --encoding utf-8 %s %s 2>&1',
            escapeshellarg($htmlFile),
            escapeshellarg($pdfFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $this->logger->error('PDF generation failed', [
                'command' => $command,
                'output' => implode("\n", $output),
                'returnCode' => $returnCode,
            ]);

            throw new \Exception('PDF generation failed');
        }

        // Read PDF file
        $pdfContent = file_get_contents($pdfFile);

        // Clean up temporary files
        unlink($htmlFile);
        unlink($pdfFile);

        return $pdfContent;
    }

    /**
     * Generate HTML from tasks
     *
     * @param Task[] $tasks Array of tasks
     * @return string HTML content
     */
    private function generateHtml(array $tasks): string
    {
        $taskRows = '';

        foreach ($tasks as $task) {
            $taskRows .= $this->generateTaskRow($task);
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .color-box {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 5px;
            vertical-align: middle;
            border: 1px solid #ccc;
        }
        .task-code {
            font-weight: bold;
            color: #555;
        }
        .task-title {
            font-size: 16px;
            font-weight: bold;
        }
        .task-description {
            color: #666;
            white-space: pre-line;
        }
        .business-unit {
            background-color: #f8f8f8;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
        .kiosk-enabled {
            color: green;
            font-weight: bold;
        }
        .kiosk-disabled {
            color: #999;
        }
    </style>
</head>
<body>
    <h1>Task Report</h1>
    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Title</th>
                <th>Description</th>
                <th>Business Unit</th>
                <th>Color Code</th>
                <th>Kiosk Mode</th>
            </tr>
        </thead>
        <tbody>
            $taskRows
        </tbody>
    </table>
    <div>
        <p>Generated on: {$this->getCurrentDate()}</p>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Generate HTML for a task row
     *
     * @param Task $task The task entity
     * @return string HTML for the task row
     */
    private function generateTaskRow(Task $task): string
    {
        $colorCode = htmlspecialchars($task->getColorCode());
        $description = nl2br(htmlspecialchars($task->getDescription()));
        $kioskStatus = $task->isAvailableInTimeTrackingKioskMode()
            ? '<span class="kiosk-enabled">Available</span>'
            : '<span class="kiosk-disabled">Not Available</span>';

        return <<<HTML
<tr>
    <td class="task-code">{$task->getTask()}</td>
    <td class="task-title">{$task->getTitle()}</td>
    <td class="task-description">{$description}</td>
    <td><span class="business-unit">{$task->getBusinessUnit()}</span></td>
    <td>
        <div class="color-box" style="background-color: {$colorCode};"></div>
        {$colorCode}
    </td>
    <td>{$kioskStatus}</td>
</tr>
HTML;
    }

    /**
     * Get the current date formatted
     *
     * @return string Formatted date
     */
    private function getCurrentDate(): string
    {
        return (new \DateTime())->format('Y-m-d H:i:s');
    }
}