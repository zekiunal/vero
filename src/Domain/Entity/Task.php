<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Task
{
    private string $task;
    private string $title;
    private string $description;
    private string $colorCode;
    private string $businessUnit;
    private string $wageType;
    private ?string $workingTime;
    private bool $isAvailableInTimeTrackingKioskMode;
    private string $sort;

    public function __construct(
        string $task,
        string $title,
        string $description,
        string $colorCode,
        string $businessUnit,
        string $wageType,
        ?string $workingTime,
        bool $isAvailableInTimeTrackingKioskMode,
        string $sort
    ) {
        $this->task = $task;
        $this->title = $title;
        $this->description = $description;
        $this->colorCode = $colorCode;
        $this->businessUnit = $businessUnit;
        $this->wageType = $wageType;
        $this->workingTime = $workingTime;
        $this->isAvailableInTimeTrackingKioskMode = $isAvailableInTimeTrackingKioskMode;
        $this->sort = $sort;
    }

    public function getTask(): string
    {
        return $this->task;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getColorCode(): string
    {
        return !empty($this->colorCode) ? $this->colorCode : '#CCCCCC';
    }

    public function getBusinessUnit(): string
    {
        return $this->businessUnit;
    }

    public function getWageType(): string
    {
        return $this->wageType;
    }

    public function getWorkingTime(): ?string
    {
        return $this->workingTime;
    }

    public function isAvailableInTimeTrackingKioskMode(): bool
    {
        return $this->isAvailableInTimeTrackingKioskMode;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['task'] ?? '',
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['colorCode'] ?? '#CCCCCC',
            $data['businessUnit'] ?? $data['BusinessUnitKey'] ?? '',
            $data['wageType'] ?? '',
            $data['workingTime'] ?? null,
            $data['isAvailableInTimeTrackingKioskMode'] ?? false,
            $data['sort'] ?? '0'
        );
    }

    public function toArray(): array
    {
        return [
            'task' => $this->task,
            'title' => $this->title,
            'description' => $this->description,
            'colorCode' => $this->getColorCode(),
            'businessUnit' => $this->businessUnit,
            'wageType' => $this->wageType,
            'workingTime' => $this->workingTime,
            'isAvailableInTimeTrackingKioskMode' => $this->isAvailableInTimeTrackingKioskMode,
            'sort' => $this->sort
        ];
    }
}