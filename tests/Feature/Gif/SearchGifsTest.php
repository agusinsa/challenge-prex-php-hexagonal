<?php

declare(strict_types=1);

namespace Tests\Feature\Gif;

use App\Domain\Gif\Gif;
use App\Domain\Gif\GifCatalog;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class SearchGifsTest extends TestCase
{
    public function test_search_requires_a_token(): void
    {
        $response = $this->get('/api/gifs?query=gato');

        $response->assertUnauthorized();
    }

    public function test_search_returns_the_catalog_results(): void
    {
        Passport::actingAs(new User());
        $this->app->instance(GifCatalog::class, new SearchGifCatalog([
            new Gif('abc', 'Un gato', 'https://giphy.com/abc'),
        ]));

        $response = $this->getJson('/api/gifs?query=gato&limit=10&offset=0');

        $response->assertOk()->assertExactJson([
            'data' => [
                [
                    'id' => 'abc',
                    'title' => 'Un gato',
                    'url' => 'https://giphy.com/abc',
                ],
            ],
        ]);
    }

    public function test_search_returns_422_when_the_query_is_missing(): void
    {
        Passport::actingAs(new User());

        $response = $this->getJson('/api/gifs');

        $response->assertUnprocessable();
    }
}

final class SearchGifCatalog implements GifCatalog
{
    /**
     * @param list<Gif> $gifs
     */
    public function __construct(private readonly array $gifs)
    {
    }

    public function search(string $query, ?int $limit = null, ?int $offset = null): array
    {
        return $this->gifs;
    }

    public function find(string $id): ?Gif
    {
        return null;
    }
}
