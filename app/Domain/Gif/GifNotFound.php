<?php

declare(strict_types=1);

namespace App\Domain\Gif;

use RuntimeException;

final class GifNotFound extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('GIF no encontrado.');
    }
}
