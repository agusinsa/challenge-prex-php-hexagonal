<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Auth\PasswordVerifier;
use Illuminate\Support\Facades\Hash;

final class HashPasswordVerifier implements PasswordVerifier
{
    public function verify(string $plainPassword, string $passwordHash): bool
    {
        return Hash::check($plainPassword, $passwordHash);
    }
}
