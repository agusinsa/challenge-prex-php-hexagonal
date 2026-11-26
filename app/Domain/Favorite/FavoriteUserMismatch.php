<?php

declare(strict_types=1);

namespace App\Domain\Favorite;

use RuntimeException;

final class FavoriteUserMismatch extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('No podés guardar un favorito de otro usuario.');
    }
}
