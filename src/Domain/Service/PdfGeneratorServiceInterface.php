<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Task;

interface PdfGeneratorServiceInterface
{
    /**
     * Generate PDF from tasks
     *
     * @param Task[] $tasks Array of tasks to include in the PDF
     * @return string The PDF content as a string
     */
    public function generatePdf(array $tasks): string;
}