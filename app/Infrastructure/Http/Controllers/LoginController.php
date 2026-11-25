<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Auth\LoginUser;
use App\Domain\Auth\InvalidCredentials;
use App\Domain\Auth\TokenIssuer;
use App\Infrastructure\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

final class LoginController
{
    public function __construct(
        private readonly LoginUser $loginUser,
        private readonly TokenIssuer $tokens,
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->loginUser->execute(
                $request->string('email')->toString(),
                $request->string('password')->toString(),
            );
        } catch (InvalidCredentials $exception) {
            return response()->json(['message' => $exception->getMessage(),], 401);
        }

        $token = $this->tokens->issue($user);

        return response()->json([
            'token' => $token->value(),
            'expires_in' => $token->expiresIn(),
        ]);
    }
}
