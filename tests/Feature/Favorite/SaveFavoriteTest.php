<?php

declare(strict_types=1);

namespace Tests\Feature\Favorite;

use App\Domain\Favorite\Favorite;
use App\Domain\Favorite\FavoriteRepository;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class SaveFavoriteTest extends TestCase
{
    public function test_store_requires_a_token(): void
    {
        $response = $this->post('/api/favorites', [
            'gif_id' => 'abc',
            'alias' => 'mi gato',
            'user_id' => 7,
        ]);

        $response->assertUnauthorized();
    }

    public function test_store_returns_the_saved_favorite(): void
    {
        $this->authenticate(7);
        $favorites = new StoreFavoriteRepository(null);
        $this->app->instance(FavoriteRepository::class, $favorites);

        $response = $this->postJson('/api/favorites', [
            'gif_id' => 'abc',
            'alias' => 'mi gato',
            'user_id' => 7,
        ]);

        $response->assertCreated()->assertExactJson([
            'data' => [
                'gif_id' => 'abc',
                'alias' => 'mi gato',
                'user_id' => 7,
            ],
        ]);
        $this->assertNotNull($favorites->saved);
        $this->assertSame('abc', $favorites->saved->gifId());
    }

    public function test_store_returns_403_when_the_user_does_not_match_the_token(): void
    {
        $this->authenticate(7);
        $favorites = new StoreFavoriteRepository(null);
        $this->app->instance(FavoriteRepository::class, $favorites);

        $response = $this->postJson('/api/favorites', [
            'gif_id' => 'abc',
            'alias' => 'mi gato',
            'user_id' => 8,
        ]);

        $response->assertForbidden()->assertExactJson([
            'message' => 'No podés guardar un favorito de otro usuario.',
        ]);
        $this->assertNull($favorites->saved);
    }

    public function test_store_returns_409_when_the_gif_is_already_saved(): void
    {
        $this->authenticate(7);
        $this->app->instance(FavoriteRepository::class, new StoreFavoriteRepository(
            new Favorite('abc', 'otro alias', 7),
        ));

        $response = $this->postJson('/api/favorites', [
            'gif_id' => 'abc',
            'alias' => 'mi gato',
            'user_id' => 7,
        ]);

        $response->assertConflict()->assertExactJson([
            'message' => 'El GIF ya está guardado.',
        ]);
    }

    public function test_store_returns_422_when_the_body_is_incomplete(): void
    {
        $this->authenticate(7);

        $response = $this->postJson('/api/favorites', []);

        $response->assertUnprocessable();
    }

    private function authenticate(int $userId): void
    {
        $user = new User();
        $user->id = $userId;

        Passport::actingAs($user);
    }
}

final class StoreFavoriteRepository implements FavoriteRepository
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
