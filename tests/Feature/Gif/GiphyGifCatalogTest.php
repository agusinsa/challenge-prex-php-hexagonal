<?php

declare(strict_types=1);

namespace Tests\Feature\Gif;

use App\Infrastructure\Gif\GiphyGifCatalog;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GiphyGifCatalogTest extends TestCase
{
    public function test_maps_the_giphy_payload_to_gifs(): void
    {
        Http::fake([
            'api.giphy.com/*' => Http::response([
                'data' => [
                    [
                        'id' => 'abc',
                        'title' => 'Un gato',
                        'url' => 'https://giphy.com/abc',
                    ],
                ],
            ]),
        ]);

        $gifs = (new GiphyGifCatalog('test-key'))->search('gato', 10, 0);

        $this->assertCount(1, $gifs);
        $this->assertSame('abc', $gifs[0]->id());
        $this->assertSame('Un gato', $gifs[0]->title());
        $this->assertSame('https://giphy.com/abc', $gifs[0]->url());

        Http::assertSent(function ($request): bool {
            return $request['q'] === 'gato'
                && $request['limit'] === 10
                && $request['offset'] === 0
                && $request['api_key'] === 'test-key';
        });
    }
}
