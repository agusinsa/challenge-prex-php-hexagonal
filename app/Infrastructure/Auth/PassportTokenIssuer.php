<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Auth\IssuedToken;
use App\Domain\Auth\TokenIssuer;
use App\Domain\Auth\User;
use App\Models\User as UserModel;

final class PassportTokenIssuer implements TokenIssuer
{
    public function issue(User $user): IssuedToken
    {
        $model = UserModel::query()->findOrFail($user->id());
        $result = $model->createToken('login');

        return new IssuedToken($result->accessToken, (int) $result->expiresIn);
    }
}
