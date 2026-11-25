<?php

declare(strict_types=1);

namespace App\Domain\Gif;

final class Gif
{
    public function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly string $url,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function url(): string
    {
        return $this->url;
    }
}
