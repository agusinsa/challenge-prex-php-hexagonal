<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\Auth\InvalidCredentials;
use App\Domain\Auth\PasswordVerifier;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;

final class LoginUser
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly PasswordVerifier $passwords,
    ) {
    }

    public function execute(string $email, string $password): User
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || ! $this->passwords->verify($password, $user->passwordHash())) {
            throw new InvalidCredentials();
        }

        return $user;
    }
}
