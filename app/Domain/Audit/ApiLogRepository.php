<?php

declare(strict_types=1);

namespace App\Domain\Audit;

interface ApiLogRepository
{
    public function save(ApiLog $log): void;
}
