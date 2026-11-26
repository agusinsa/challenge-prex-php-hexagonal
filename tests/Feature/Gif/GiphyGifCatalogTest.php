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

    public function test_find_maps_a_single_gif(): void
    {
        Http::fake([
            'api.giphy.com/v1/gifs/abc*' => Http::response([
                'data' => [
                    'id' => 'abc',
                    'title' => 'Un gato',
                    'url' => 'https://giphy.com/abc',
                ],
            ]),
        ]);

        $gif = (new GiphyGifCatalog('test-key'))->find('abc');

        $this->assertNotNull($gif);
        $this->assertSame('abc', $gif->id());
        $this->assertSame('Un gato', $gif->title());
        $this->assertSame('https://giphy.com/abc', $gif->url());
    }

    public function test_find_returns_null_when_giphy_does_not_have_the_gif(): void
    {
        Http::fake([
            'api.giphy.com/*' => Http::response([
                'data' => [],
                'meta' => ['status' => 400, 'msg' => 'Validation error'],
            ], 400),
        ]);

        $this->assertNull((new GiphyGifCatalog('test-key'))->find('no-existe'));
    }
}
