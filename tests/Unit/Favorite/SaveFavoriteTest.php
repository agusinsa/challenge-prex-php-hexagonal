<?php

declare(strict_types=1);

namespace Tests\Unit\Favorite;

use App\Application\Favorite\SaveFavorite;
use App\Domain\Favorite\Favorite;
use App\Domain\Favorite\FavoriteAlreadySaved;
use App\Domain\Favorite\FavoriteRepository;
use App\Domain\Favorite\FavoriteUserMismatch;
use PHPUnit\Framework\TestCase;

class SaveFavoriteTest extends TestCase
{
    public function test_saves_the_favorite_when_the_user_matches(): void
    {
        $favorites = new FakeFavoriteRepository(null);
        $save = new SaveFavorite($favorites);

        $result = $save->execute(1, 'abc', 'mi gato', 1);

        $this->assertSame('abc', $result->gifId());
        $this->assertSame('mi gato', $result->alias());
        $this->assertSame(1, $result->userId());
        $this->assertSame($result, $favorites->saved);
    }

    public function test_rejects_a_favorite_for_another_user(): void
    {
        $favorites = new FakeFavoriteRepository(null);
        $save = new SaveFavorite($favorites);

        try {
            $save->execute(1, 'abc', 'mi gato', 2);
            $this->fail('Tenía que rechazar el favorito de otro usuario.');
        } catch (FavoriteUserMismatch) {
            $this->assertNull($favorites->saved);
        }
    }

    public function test_rejects_a_gif_the_user_already_saved(): void
    {
        $existing = new Favorite('abc', 'otro alias', 1);
        $favorites = new FakeFavoriteRepository($existing);
        $save = new SaveFavorite($favorites);

        try {
            $save->execute(1, 'abc', 'mi gato', 1);
            $this->fail('Tenía que rechazar un GIF ya guardado.');
        } catch (FavoriteAlreadySaved) {
            $this->assertNull($favorites->saved);
        }
    }
}

final class FakeFavoriteRepository implements FavoriteRepository
{
    public ?Favorite $saved = null;

    public function __construct(private readonly ?Favorite $existing)
    {
    }

    public function findByUserAndGif(int $userId, string $gifId): ?Favorite
    {
        return $this->existing;
    }

    public function save(Favorite $favorite): void
    {
        $this->saved = $favorite;
    }
}
