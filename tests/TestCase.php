<?php

namespace Tests;

use App\Domain\Audit\ApiLog;
use App\Domain\Audit\ApiLogRepository;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(ApiLogRepository::class, new DiscardingApiLogRepository());
    }
}

final class DiscardingApiLogRepository implements ApiLogRepository
{
    public function save(ApiLog $log): void
    {
    }
}
