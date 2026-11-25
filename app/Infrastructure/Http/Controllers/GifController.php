<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Gif\SearchGifs;
use App\Domain\Gif\Gif;
use App\Infrastructure\Http\Requests\SearchGifsRequest;
use Illuminate\Http\JsonResponse;

final class GifController
{
    public function __construct(
        private readonly SearchGifs $searchGifs,
    ) {
    }

    public function search(SearchGifsRequest $request): JsonResponse
    {
        $gifs = $this->searchGifs->execute(
            $request->string('query')->toString(),
            $request->filled('limit') ? $request->integer('limit') : null,
            $request->filled('offset') ? $request->integer('offset') : null,
        );

        return response()->json([
            'data' => array_map(fn (Gif $gif): array => [
                'id' => $gif->id(),
                'title' => $gif->title(),
                'url' => $gif->url(),
            ], $gifs),
        ]);
    }
}
