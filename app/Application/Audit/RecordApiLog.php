<?php

declare(strict_types=1);

namespace App\Application\Audit;

use App\Domain\Audit\ApiLog;
use App\Domain\Audit\ApiLogRepository;

final class RecordApiLog
{
    public function __construct(
        private readonly ApiLogRepository $logs,
    ) {
    }

    /**
     * @param array<string, mixed> $requestBody
     */
    public function execute(
        string $service,
        array $requestBody,
        int $statusCode,
        string $responseBody,
        string $ip,
        ?int $userId,
    ): void {
        $log = new ApiLog(
            $service,
            $this->encode($this->redact($requestBody)),
            $statusCode,
            $responseBody,
            $ip,
            $userId,
        );

        $this->logs->save($log);
    }

    /**
     * @param array<string, mixed> $requestBody
     * @return array<string, mixed>
     */
    private function redact(array $requestBody): array
    {
        if (array_key_exists('password', $requestBody)) {
            $requestBody['password'] = '***';
        }

        return $requestBody;
    }

    /**
     * @param array<string, mixed> $requestBody
     */
    private function encode(array $requestBody): string
    {
        return json_encode($requestBody, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
