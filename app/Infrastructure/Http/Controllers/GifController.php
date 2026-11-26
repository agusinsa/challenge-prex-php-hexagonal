<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Gif\GetGif;
use App\Application\Gif\SearchGifs;
use App\Domain\Gif\Gif;
use App\Domain\Gif\GifNotFound;
use App\Infrastructure\Http\Requests\SearchGifsRequest;
use Illuminate\Http\JsonResponse;

final class GifController
{
    public function __construct(
        private readonly SearchGifs $searchGifs,
        private readonly GetGif $getGif,
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
            'data' => array_map(fn (Gif $gif): array => $this->gifToArray($gif), $gifs),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $gif = $this->getGif->execute($id);
        } catch (GifNotFound $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }

        return response()->json([
            'data' => $this->gifToArray($gif),
        ]);
    }

    /**
     * @return array{id: string, title: string, url: string}
     */
    private function gifToArray(Gif $gif): array
    {
        return [
            'id' => $gif->id(),
            'title' => $gif->title(),
            'url' => $gif->url(),
        ];
    }
}
