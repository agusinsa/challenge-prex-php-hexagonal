<?php

declare(strict_types=1);

namespace App\Application\Gif;

use App\Domain\Gif\GifCatalog;

final class SearchGifs
{
    public function __construct(
        private readonly GifCatalog $catalog,
    ) {
    }

    /**
     * @return list<\App\Domain\Gif\Gif>
     */
    public function execute(string $query, ?int $limit = null, ?int $offset = null): array
    {
        return $this->catalog->search($query, $limit, $offset);
    }
}
