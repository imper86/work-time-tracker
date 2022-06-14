<?php

namespace App\Entity;

use DateTimeImmutable;

class Task
{
    private ?int $id;
    private string $code;
    private string $name;
    private ?string $description;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $lastLoggedAt = null;

    public function __construct(?int $id, string $code, string $name, ?string $description = null, ?DateTimeImmutable $createdAt = null)
    {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = $createdAt ?: new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastLoggedAt(): ?DateTimeImmutable
    {
        return $this->lastLoggedAt;
    }

    public function setLastLoggedAt(?DateTimeImmutable $lastLoggedAt): void
    {
        $this->lastLoggedAt = $lastLoggedAt;
    }
}