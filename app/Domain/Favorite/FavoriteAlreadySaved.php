<?php

declare(strict_types=1);

namespace App\Domain\Favorite;

use RuntimeException;

final class FavoriteAlreadySaved extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('El GIF ya está guardado.');
    }
}
