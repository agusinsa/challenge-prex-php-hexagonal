<?php

declare(strict_types=1);

namespace Tests\Unit\Audit;

use App\Application\Audit\RecordApiLog;
use App\Domain\Audit\ApiLog;
use App\Domain\Audit\ApiLogRepository;
use PHPUnit\Framework\TestCase;

class RecordApiLogTest extends TestCase
{
    public function test_stores_the_log_and_hides_the_password(): void
    {
        $logs = new FakeApiLogRepository();
        $record = new RecordApiLog($logs);

        $record->execute(
            'login',
            ['email' => 'ada@example.com', 'password' => 'secret'],
            200,
            '{"token":"abc","expires_in":1800}',
            '127.0.0.1',
            7,
        );

        $saved = $logs->saved;
        $this->assertInstanceOf(ApiLog::class, $saved);
        $this->assertSame('login', $saved->service());
        $this->assertSame('{"email":"ada@example.com","password":"***"}', $saved->requestBody());
        $this->assertSame(200, $saved->statusCode());
        $this->assertSame('{"token":"abc","expires_in":1800}', $saved->responseBody());
        $this->assertSame('127.0.0.1', $saved->ip());
        $this->assertSame(7, $saved->userId());
    }

    public function test_stores_a_log_without_a_user(): void
    {
        $logs = new FakeApiLogRepository();
        $record = new RecordApiLog($logs);

        $record->execute('login', ['email' => 'ada@example.com'], 401, '{"message":"Credenciales inválidas."}', '10.0.0.1', null);

        $this->assertNull($logs->saved?->userId());
        $this->assertSame('{"email":"ada@example.com"}', $logs->saved?->requestBody());
    }
}

final class FakeApiLogRepository implements ApiLogRepository
{
    public ?ApiLog $saved = null;

    public function save(ApiLog $log): void
    {
        $this->saved = $log;
    }
}
