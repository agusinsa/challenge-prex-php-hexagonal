<?php

declare(strict_types=1);

namespace App\Domain\Favorite;

final class Favorite
{
    public function __construct(
        private readonly string $gifId,
        private readonly string $alias,
        private readonly int $userId,
    ) {
    }

    public function gifId(): string
    {
        return $this->gifId;
    }

    public function alias(): string
    {
        return $this->alias;
    }

    public function userId(): int
    {
        return $this->userId;
    }
}
