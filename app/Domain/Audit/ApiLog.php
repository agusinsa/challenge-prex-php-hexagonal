<?php

declare(strict_types=1);

namespace App\Domain\Audit;

final class ApiLog
{
    public function __construct(
        private readonly string $service,
        private readonly string $requestBody,
        private readonly int $statusCode,
        private readonly string $responseBody,
        private readonly string $ip,
        private readonly ?int $userId,
    ) {
    }

    public function service(): string
    {
        return $this->service;
    }

    public function requestBody(): string
    {
        return $this->requestBody;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function responseBody(): string
    {
        return $this->responseBody;
    }

    public function ip(): string
    {
        return $this->ip;
    }

    public function userId(): ?int
    {
        return $this->userId;
    }
}
