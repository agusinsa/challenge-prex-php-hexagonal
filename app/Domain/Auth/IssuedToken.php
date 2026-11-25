<?php

declare(strict_types=1);

namespace App\Domain\Auth;

final class IssuedToken
{
    public function __construct(
        private readonly string $value,
        private readonly int $expiresIn,
    ) {
    }

    public function value(): string
    {
        return $this->value;
    }

    public function expiresIn(): int
    {
        return $this->expiresIn;
    }
}
