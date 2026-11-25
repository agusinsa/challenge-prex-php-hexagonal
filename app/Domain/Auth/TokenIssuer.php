<?php

declare(strict_types=1);

namespace App\Domain\Auth;

interface TokenIssuer
{
    public function issue(User $user): IssuedToken;
}
