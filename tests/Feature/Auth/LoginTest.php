<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Domain\Auth\IssuedToken;
use App\Domain\Auth\PasswordVerifier;
use App\Domain\Auth\TokenIssuer;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_returns_a_token_when_the_credentials_match(): void
    {
        $this->app->instance(UserRepository::class, new LoginUserRepository(new User(1, 'ada@example.com', 'hash')));
        $this->app->instance(PasswordVerifier::class, new LoginPasswordVerifier(true));
        $this->app->instance(TokenIssuer::class, new LoginTokenIssuer(new IssuedToken('token-plano', 1800)));

        $response = $this->postJson('/api/login', [
            'email' => 'ada@example.com',
            'password' => 'secreto',
        ]);

        $response->assertOk()->assertExactJson([
            'token' => 'token-plano',
            'expires_in' => 1800,
        ]);
    }

    public function test_login_returns_401_when_the_credentials_are_invalid(): void
    {
        $this->app->instance(UserRepository::class, new LoginUserRepository(null));
        $this->app->instance(PasswordVerifier::class, new LoginPasswordVerifier(true));
        $this->app->instance(TokenIssuer::class, new LoginTokenIssuer(new IssuedToken('token-plano', 1800)));

        $response = $this->postJson('/api/login', [
            'email' => 'ada@example.com',
            'password' => 'incorrecta',
        ]);

        $response->assertUnauthorized();
    }

    public function test_login_returns_422_when_the_body_is_incomplete(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertUnprocessable();
    }
}

final class LoginUserRepository implements UserRepository
{
    public function __construct(private readonly ?User $user)
    {
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user;
    }
}

final class LoginPasswordVerifier implements PasswordVerifier
{
    public function __construct(private readonly bool $matches)
    {
    }

    public function verify(string $plainPassword, string $passwordHash): bool
    {
        return $this->matches;
    }
}

final class LoginTokenIssuer implements TokenIssuer
{
    public function __construct(private readonly IssuedToken $token)
    {
    }

    public function issue(User $user): IssuedToken
    {
        return $this->token;
    }
}
