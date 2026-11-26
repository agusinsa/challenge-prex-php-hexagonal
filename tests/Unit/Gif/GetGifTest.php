<?php

declare(strict_types=1);

namespace Tests\Unit\Gif;

use App\Application\Gif\GetGif;
use App\Domain\Gif\Gif;
use App\Domain\Gif\GifCatalog;
use App\Domain\Gif\GifNotFound;
use PHPUnit\Framework\TestCase;

class GetGifTest extends TestCase
{
    public function test_returns_the_gif_when_the_catalog_finds_it(): void
    {
        $gif = new Gif('abc', 'Un gato', 'https://giphy.com/abc');

        $result = (new GetGif(new FindGifCatalog($gif)))->execute('abc');

        $this->assertSame($gif, $result);
    }

    public function test_fails_when_the_catalog_does_not_find_it(): void
    {
        $this->expectException(GifNotFound::class);

        (new GetGif(new FindGifCatalog(null)))->execute('no-existe');
    }
}

final class FindGifCatalog implements GifCatalog
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
