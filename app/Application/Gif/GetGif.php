<?php

declare(strict_types=1);

namespace App\Application\Gif;

use App\Domain\Gif\Gif;
use App\Domain\Gif\GifCatalog;
use App\Domain\Gif\GifNotFound;

final class GetGif
{
    public function __construct(
        private readonly GifCatalog $catalog,
    ) {
    }

    public function execute(string $id): Gif
    {
        $gif = $this->catalog->find($id);

        if ($gif === null) {
            throw new GifNotFound();
        }

        return $gif;
    }
}
