<?php

declare(strict_types=1);

namespace App\Domain\Favorite;

interface FavoriteRepository
{
    public function findByUserAndGif(int $userId, string $gifId): ?Favorite;

    public function save(Favorite $favorite): void;
}
