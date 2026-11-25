<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Models\User as UserModel;

final class EloquentUserRepository implements UserRepository
{
    public function findByEmail(string $email): ?User
    {
        $userModel = UserModel::query()->where('email', $email)->first();

        if ($userModel === null) {
            return null;
        }

        return new User($userModel->id, $userModel->email, $userModel->password);
    }
}
