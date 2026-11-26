<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Favorite\SaveFavorite;
use App\Domain\Favorite\FavoriteAlreadySaved;
use App\Domain\Favorite\FavoriteUserMismatch;
use App\Infrastructure\Http\Requests\SaveFavoriteRequest;
use Illuminate\Http\JsonResponse;

final class FavoriteController
{
    public function __construct(
        private readonly SaveFavorite $saveFavorite,
    ) {
    }

    public function store(SaveFavoriteRequest $request): JsonResponse
    {
        try {
            $favorite = $this->saveFavorite->execute(
                (int) $request->user()->getAuthIdentifier(),
                $request->string('gif_id')->toString(),
                $request->string('alias')->toString(),
                $request->integer('user_id'),
            );
        } catch (FavoriteUserMismatch $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 403);
        } catch (FavoriteAlreadySaved $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 409);
        }

        return response()->json([
            'data' => [
                'gif_id' => $favorite->gifId(),
                'alias' => $favorite->alias(),
                'user_id' => $favorite->userId(),
            ],
        ], 201);
    }
}
