<?php

declare(strict_types=1);

namespace Tests\Unit\Gif;

use App\Application\Gif\SearchGifs;
use App\Domain\Gif\Gif;
use App\Domain\Gif\GifCatalog;
use PHPUnit\Framework\TestCase;

class SearchGifsTest extends TestCase
{
    public function test_returns_the_gifs_from_the_catalog(): void
    {
        $gifs = [
            new Gif('abc', 'Un gato', 'https://giphy.com/abc'),
            new Gif('def', 'Otro gato', 'https://giphy.com/def'),
        ];
        $catalog = new FakeGifCatalog($gifs);

        $result = (new SearchGifs($catalog))->execute('gato', 10, 20);

        $this->assertSame($gifs, $result);
        $this->assertSame('gato', $catalog->query);
        $this->assertSame(10, $catalog->limit);
        $this->assertSame(20, $catalog->offset);
    }
}

final class FakeGifCatalog implements GifCatalog
{
    public ?string $query = null;

    public ?int $limit = null;

    public ?int $offset = null;

    /**
     * @param list<Gif> $gifs
     */
    public function __construct(private readonly array $gifs)
    {
    }

    public function search(string $query, ?int $limit = null, ?int $offset = null): array
    {
        $this->query = $query;
        $this->limit = $limit;
        $this->offset = $offset;

        return $this->gifs;
    }

    public function find(string $id): ?Gif
    {
        return null;
    }
}
