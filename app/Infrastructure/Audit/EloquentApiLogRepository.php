<?php

declare(strict_types=1);

namespace App\Infrastructure\Audit;

use App\Domain\Audit\ApiLog;
use App\Domain\Audit\ApiLogRepository;
use App\Models\ApiLog as ApiLogModel;

final class EloquentApiLogRepository implements ApiLogRepository
{
    public function save(ApiLog $log): void
    {
        ApiLogModel::query()->create([
            'service' => $log->service(),
            'request_body' => $log->requestBody(),
            'status_code' => $log->statusCode(),
            'response_body' => $log->responseBody(),
            'ip' => $log->ip(),
            'user_id' => $log->userId(),
        ]);
    }
}
