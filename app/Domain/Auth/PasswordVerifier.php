<?php

declare(strict_types=1);

namespace App\Domain\Auth;

interface PasswordVerifier
{
    public function verify(string $plainPassword, string $passwordHash): bool;
}
