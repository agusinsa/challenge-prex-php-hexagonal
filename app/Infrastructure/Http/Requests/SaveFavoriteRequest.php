<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'gif_id' => ['required', 'string'],
            'alias' => ['required', 'string'],
            'user_id' => ['required', 'integer'],
        ];
    }
}
