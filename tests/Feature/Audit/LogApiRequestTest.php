<?php

declare(strict_types=1);

namespace Tests\Feature\Audit;

use App\Domain\Audit\ApiLog;
use App\Domain\Audit\ApiLogRepository;
use App\Domain\Auth\IssuedToken;
use App\Domain\Auth\PasswordVerifier;
use App\Domain\Auth\TokenIssuer;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use Tests\TestCase;

class LogApiRequestTest extends TestCase
{
    public function test_login_is_logged_without_the_password(): void
    {
        $logs = new RecordingApiLogRepository();
        $this->app->instance(ApiLogRepository::class, $logs);
        $this->app->instance(UserRepository::class, new AuditUserRepository(new User(7, 'ada@example.com', 'hash')));
        $this->app->instance(PasswordVerifier::class, new AuditPasswordVerifier(true));
        $this->app->instance(TokenIssuer::class, new AuditTokenIssuer(new IssuedToken('token-plano', 1800)));

        $response = $this->postJson('/api/login', [
            'email' => 'ada@example.com',
            'password' => 'secret',
        ]);

        $response->assertOk();

        $saved = $logs->saved;
        $this->assertInstanceOf(ApiLog::class, $saved);
        $this->assertSame('LoginController@login', $saved->service());
        $this->assertSame('{"email":"ada@example.com","password":"***"}', $saved->requestBody());
        $this->assertSame(200, $saved->statusCode());
        $this->assertSame('{"token":"token-plano","expires_in":1800}', $saved->responseBody());
        $this->assertNull($saved->userId());
    }

    public function test_a_request_without_a_token_is_logged(): void
    {
        $logs = new RecordingApiLogRepository();
        $this->app->instance(ApiLogRepository::class, $logs);

        $response = $this->get('/api/gifs?query=gato');

        $response->assertUnauthorized();

        $saved = $logs->saved;
        $this->assertInstanceOf(ApiLog::class, $saved);
        $this->assertSame('GifController@search', $saved->service());
        $this->assertSame(401, $saved->statusCode());
        $this->assertNull($saved->userId());
    }
}

final class RecordingApiLogRepository implements ApiLogRepository
{
    public ?ApiLog $saved = null;

    public function save(ApiLog $log): void
    {
        $this->saved = $log;
    }
}

final class AuditUserRepository implements UserRepository
{
    public function __construct(private readonly ?User $user)
    {
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user;
    }
}

final class AuditPasswordVerifier implements PasswordVerifier
{
    public function __construct(private readonly bool $matches)
    {
    }

    public function verify(string $plainPassword, string $passwordHash): bool
    {
        return $this->matches;
    }
}

final class AuditTokenIssuer implements TokenIssuer
{
    public function __construct(private readonly IssuedToken $token)
    {
    }

    public function issue(User $user): IssuedToken
    {
        return $this->token;
    }
}
