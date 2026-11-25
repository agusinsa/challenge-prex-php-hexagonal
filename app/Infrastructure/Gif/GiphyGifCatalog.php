<?php

declare(strict_types=1);

namespace App\Infrastructure\Gif;

use App\Domain\Gif\Gif;
use App\Domain\Gif\GifCatalog;
use Illuminate\Support\Facades\Http;

final class GiphyGifCatalog implements GifCatalog
{
    public function __construct(
        private readonly string $apiKey,
    ) {
    }

    public function search(string $query, ?int $limit = null, ?int $offset = null): array
    {
        $parameters = [
            'api_key' => $this->apiKey,
            'q' => $query,
        ];

        if ($limit !== null) {
            $parameters['limit'] = $limit;
        }

        if ($offset !== null) {
            $parameters['offset'] = $offset;
        }

        $response = Http::get('https://api.giphy.com/v1/gifs/search', $parameters)->throw();

        return array_map(
            fn (array $item): Gif => new Gif(
                $item['id'],
                $item['title'] ?? '',
                $item['url'] ?? '',
            ),
            $response->json('data') ?? [],
        );
    }
}
