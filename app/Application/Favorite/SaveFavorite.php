<?php

declare(strict_types=1);

namespace App\Application\Favorite;

use App\Domain\Favorite\Favorite;
use App\Domain\Favorite\FavoriteAlreadySaved;
use App\Domain\Favorite\FavoriteRepository;
use App\Domain\Favorite\FavoriteUserMismatch;

final class SaveFavorite
{
    public function __construct(
        private readonly FavoriteRepository $favorites,
    ) {
    }

    public function execute(int $authenticatedUserId, string $gifId, string $alias, int $userId): Favorite
    {
        if ($authenticatedUserId !== $userId) {
            throw new FavoriteUserMismatch();
        }

        if ($this->favorites->findByUserAndGif($userId, $gifId) !== null) {
            throw new FavoriteAlreadySaved();
        }

        $favorite = new Favorite($gifId, $alias, $userId);
        $this->favorites->save($favorite);

        return $favorite;
    }
}
