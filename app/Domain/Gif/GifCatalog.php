<?php

declare(strict_types=1);

namespace App\Domain\Gif;

interface GifCatalog
{
    /**
     * @return list<Gif>
     */
    public function search(string $query, ?int $limit = null, ?int $offset = null): array;

    public function find(string $id): ?Gif;
}
