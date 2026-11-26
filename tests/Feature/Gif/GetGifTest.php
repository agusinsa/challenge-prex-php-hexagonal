<?php

declare(strict_types=1);

namespace Tests\Feature\Gif;

use App\Domain\Gif\Gif;
use App\Domain\Gif\GifCatalog;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GetGifTest extends TestCase
{
    public function test_show_requires_a_token(): void
    {
        $response = $this->get('/api/gifs/abc');

        $response->assertUnauthorized();
    }

    public function test_show_returns_the_gif(): void
    {
        Passport::actingAs(new User());
        $this->app->instance(GifCatalog::class, new ShowGifCatalog(
            new Gif('abc', 'Un gato', 'https://giphy.com/abc'),
        ));

        $response = $this->getJson('/api/gifs/abc');

        $response->assertOk()->assertExactJson([
            'data' => [
                'id' => 'abc',
                'title' => 'Un gato',
                'url' => 'https://giphy.com/abc',
            ],
        ]);
    }

    public function test_show_returns_404_when_the_gif_does_not_exist(): void
    {
        Passport::actingAs(new User());
        $this->app->instance(GifCatalog::class, new ShowGifCatalog(null));

        $response = $this->getJson('/api/gifs/no-existe');

        $response->assertNotFound();
    }
}

final class ShowGifCatalog implements GifCatalog
{
    public function __construct(private readonly ?Gif $gif)
    {
    }

    public function search(string $query, ?int $limit = null, ?int $offset = null): array
    {
        return [];
    }

    public function find(string $id): ?Gif
    {
        return $this->gif;
    }
}
