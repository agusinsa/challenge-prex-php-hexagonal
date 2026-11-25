<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Application\Auth\LoginUser;
use App\Domain\Auth\InvalidCredentials;
use App\Domain\Auth\PasswordVerifier;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use PHPUnit\Framework\TestCase;

class LoginUserTest extends TestCase
{
    public function test_returns_the_user_when_the_password_matches(): void
    {
        $user = new User(1, 'ada@example.com', 'hash-secreto');

        $login = new LoginUser(
            new FakeUserRepository($user),
            new FakePasswordVerifier(true),
        );

        $result = $login->execute('ada@example.com', 'secreto');

        $this->assertSame($user, $result);
    }

    public function test_rejects_an_unknown_email(): void
    {
        $login = new LoginUser(
            new FakeUserRepository(null),
            new FakePasswordVerifier(true),
        );

        $this->expectException(InvalidCredentials::class);

        $login->execute('nadie@example.com', 'secreto');
    }

    public function test_rejects_a_wrong_password(): void
    {
        $user = new User(1, 'ada@example.com', 'hash-secreto');

        $login = new LoginUser(
            new FakeUserRepository($user),
            new FakePasswordVerifier(false),
        );

        $this->expectException(InvalidCredentials::class);

        $login->execute('ada@example.com', 'incorrecta');
    }
}

final class FakeUserRepository implements UserRepository
{
    public function __construct(private readonly ?User $user)
    {
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user;
    }
}

final class FakePasswordVerifier implements PasswordVerifier
{
    public function __construct(private readonly bool $matches)
    {
    }

    public function verify(string $plainPassword, string $passwordHash): bool
    {
        return $this->matches;
    }
}
