<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Middleware;

use App\Application\Audit\RecordApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class LogApiRequest
{
    public function __construct(
        private readonly RecordApiLog $recordApiLog,
    ) {
    }

    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $user = $request->user();

        $this->recordApiLog->execute(
            $this->serviceName($request),
            array_merge($request->all(), $request->route()?->parameters() ?? []),
            $response->getStatusCode(),
            (string) $response->getContent(),
            (string) $request->ip(),
            $user === null ? null : (int) $user->getAuthIdentifier(),
        );
    }

    private function serviceName(Request $request): string
    {
        $action = $request->route()?->getActionName();

        if (! is_string($action) || ! str_contains($action, '@')) {
            return $request->path();
        }

        [$class, $method] = explode('@', $action, 2);

        return class_basename($class).'@'.$method;
    }
}
