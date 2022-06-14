<?php

namespace App\Entity;

use DateTimeImmutable;

class WorkLog
{
    private ?int $id;
    private Task $task;
    private string $description;
    private DateTimeImmutable $startedAt;
    private ?DateTimeImmutable $finishedAt;
    private ?int $duration;

    public function __construct(
        ?int $id,
        Task $task,
        string $description,
        DateTimeImmutable $startedAt,
        ?DateTimeImmutable $finishedAt = null
    ) {
        $this->id = $id;
        $this->task = $task;
        $this->startedAt = $startedAt;
        $this->finishedAt = $finishedAt;
        $this->description = $description;
        $this->updateDuration();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?DateTimeImmutable $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
        $this->updateDuration();
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    private function updateDuration(): void
    {
        if (null === $this->finishedAt) {
            $this->duration = null;
        } else {
            $this->duration = $this->finishedAt->getTimestamp() - $this->startedAt->getTimestamp();
        }
    }
}