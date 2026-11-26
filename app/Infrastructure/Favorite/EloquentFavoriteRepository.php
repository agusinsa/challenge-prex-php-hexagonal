<?php

declare(strict_types=1);

namespace App\Infrastructure\Favorite;

use App\Domain\Favorite\Favorite;
use App\Domain\Favorite\FavoriteRepository;
use App\Models\Favorite as FavoriteModel;

final class EloquentFavoriteRepository implements FavoriteRepository
{
    public function findByUserAndGif(int $userId, string $gifId): ?Favorite
    {
        $row = FavoriteModel::query()
            ->where('user_id', $userId)
            ->where('gif_id', $gifId)
            ->first();

        if ($row === null) {
            return null;
        }

        return new Favorite($row->gif_id, $row->alias, (int) $row->user_id);
    }

    public function save(Favorite $favorite): void
    {
        FavoriteModel::query()->create([
            'gif_id' => $favorite->gifId(),
            'alias' => $favorite->alias(),
            'user_id' => $favorite->userId(),
        ]);
    }
}
